<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penilaian;
use App\Models\User;
use App\Models\SistemKriteria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenilaianController extends Controller
{
    /**
     * Display a listing of karyawan with their penilaian status.
     * Menampilkan daftar karyawan dengan status kelengkapan penilaian per periode
     */
    public function index(Request $request)
    {
        // Default periode = bulan dan tahun sekarang
        $bulan = $request->query('bulan', date('n'));
        $tahun = $request->query('tahun', date('Y'));
        $divisi = $request->query('divisi');

        // Get all active sub-kriteria (level 2) yang harus dinilai
        // Filter by supervisor assignment if logged in as supervisor
        $subKriteriaQuery = SistemKriteria::where('level', 2)
            ->where('is_active', true);

        // If supervisor, only count sub-kriteria from assigned kriteria
        if (auth()->user()->isSupervisor()) {
            $subKriteriaQuery->whereHas('parent', function ($q) {
                $q->where(function ($query) {
                    $query->where('assigned_to_supervisor_id', auth()->id())
                        ->orWhereNull('assigned_to_supervisor_id');
                });
            });
        }

        $totalSubKriteria = $subKriteriaQuery->count();

        // Get all karyawan yang bisa dinilai
        $karyawanQuery = User::where('role', 'karyawan')
            ->approved()
            ->active();

        // Filter by divisi
        if ($divisi) {
            $karyawanQuery->where('divisi', $divisi);
        }

        $karyawanList = $karyawanQuery->orderBy('nama', 'asc')->get();

        // Get penilaian count per karyawan untuk periode ini
        $penilaianCounts = Penilaian::byPeriode($bulan, $tahun)
            ->select('id_karyawan', DB::raw('COUNT(DISTINCT id_sub_kriteria) as jumlah_dinilai'))
            ->groupBy('id_karyawan')
            ->pluck('jumlah_dinilai', 'id_karyawan');

        // Build status tracking data
        $statusData = $karyawanList->map(function ($karyawan) use ($penilaianCounts, $totalSubKriteria) {
            $jumlahDinilai = $penilaianCounts->get($karyawan->id, 0);
            $persentase = $totalSubKriteria > 0 ? round(($jumlahDinilai / $totalSubKriteria) * 100, 1) : 0;

            return [
                'karyawan' => $karyawan,
                'jumlah_dinilai' => $jumlahDinilai,
                'total_kriteria' => $totalSubKriteria,
                'jumlah_belum' => $totalSubKriteria - $jumlahDinilai,
                'persentase' => $persentase,
                'status' => $jumlahDinilai === 0 ? 'belum_mulai' : ($jumlahDinilai >= $totalSubKriteria ? 'selesai' : 'dalam_proses')
            ];
        });

        // Get available divisi from karyawan
        $divisiList = User::where('role', 'karyawan')
            ->approved()
            ->active()
            ->select('divisi')
            ->distinct()
            ->orderBy('divisi')
            ->pluck('divisi')
            ->filter();

        // Get available years from existing penilaian
        $tahunList = Penilaian::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Add current year if not exists
        if (!$tahunList->contains(date('Y'))) {
            $tahunList->prepend(date('Y'));
        }

        // Generate periode label
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

        // Statistics
        $stats = [
            'total_karyawan' => $statusData->count(),
            'selesai' => $statusData->where('status', 'selesai')->count(),
            'dalam_proses' => $statusData->where('status', 'dalam_proses')->count(),
            'belum_mulai' => $statusData->where('status', 'belum_mulai')->count(),
            'persentase_selesai' => $statusData->count() > 0 ?
                round(($statusData->where('status', 'selesai')->count() / $statusData->count()) * 100, 1) : 0
        ];

        return view('penilaian.index', compact(
            'statusData',
            'bulan',
            'tahun',
            'periodeLabel',
            'divisiList',
            'divisi',
            'tahunList',
            'stats',
            'totalSubKriteria'
        ));
    }

    /**
     * Display overview of kriteria penilaian for specific karyawan.
     * Menampilkan daftar kriteria dengan status sudah/belum dinilai per karyawan
     */
    public function overview($karyawanId, $bulan, $tahun)
    {
        $karyawan = User::findOrFail($karyawanId);

        // Get all active kriteria dengan sub-kriteria and supervisor info
        $kriteria = SistemKriteria::where('level', 1)
            ->where('is_active', true)
            ->with([
                'subKriteria' => function ($query) {
                    $query->where('is_active', true)->orderBy('urutan', 'asc');
                },
                'assignedSupervisor'
            ])
            ->orderBy('urutan', 'asc')
            ->get();

        // Get existing penilaian untuk periode ini
        $existingPenilaian = Penilaian::byKaryawan($karyawanId)
            ->byPeriode($bulan, $tahun)
            ->get()
            ->keyBy('id_sub_kriteria');

        // Current user
        $currentUser = auth()->user();

        // Calculate status for each kriteria and check access
        $kriteriaWithStatus = $kriteria->map(function ($item) use ($existingPenilaian, $currentUser) {
            $totalSubKriteria = $item->subKriteria->count();
            $dinilaiCount = 0;

            foreach ($item->subKriteria as $sub) {
                if ($existingPenilaian->has($sub->id)) {
                    $dinilaiCount++;
                }
            }

            $persentase = $totalSubKriteria > 0 ? round(($dinilaiCount / $totalSubKriteria) * 100, 1) : 0;

            // Check if current user has access to this kriteria
            $hasAccess = true;
            if ($currentUser->isSupervisor()) {
                // Supervisor has access ONLY if assigned to them (not NULL)
                $hasAccess = $item->assigned_to_supervisor_id === $currentUser->id;
            }

            return [
                'kriteria' => $item,
                'total_sub' => $totalSubKriteria,
                'dinilai' => $dinilaiCount,
                'belum_dinilai' => $totalSubKriteria - $dinilaiCount,
                'persentase' => $persentase,
                'status' => $dinilaiCount === 0 ? 'belum' : ($dinilaiCount >= $totalSubKriteria ? 'selesai' : 'sebagian'),
                'has_access' => $hasAccess,
                'assigned_supervisor' => $item->assignedSupervisor,
            ];
        });

        // Generate periode label
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

        return view('penilaian.overview', compact(
            'karyawan',
            'kriteriaWithStatus',
            'bulan',
            'tahun',
            'periodeLabel'
        ));
    }

    /**
     * Show the form for creating a new penilaian.
     * Form dynamic yang load kriteria & sub-kriteria berdasarkan karyawan
     *
     * New: If kriteria_id provided, only show that specific kriteria (single kriteria flow)
     */
    public function create(Request $request)
    {
        // Get query params
        $karyawanId = $request->query('karyawan_id');
        $bulan = $request->query('bulan', date('n'));
        $tahun = $request->query('tahun', date('Y'));
        $kriteriaId = $request->query('kriteria_id'); // NEW: specific kriteria

        // Get karyawan list yang bisa dinilai (for old flow)
        $karyawanList = User::where('role', 'karyawan')
            ->approved()
            ->orderBy('nama', 'asc')
            ->get();

        $karyawan = null;
        $kriteria = collect();
        $singleKriteria = null; // NEW: for single kriteria mode
        $existingPenilaian = collect();
        $periodeLabel = null;

        // If karyawan selected
        if ($karyawanId) {
            $karyawan = User::findOrFail($karyawanId);

            // Generate periode label
            $namaBulan = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];
            $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

            // NEW: Single kriteria flow (from overview page)
            if ($kriteriaId) {
                // Get specific kriteria only
                $kriteriaQuery = SistemKriteria::where('level', 1)
                    ->where('id', $kriteriaId)
                    ->where('is_active', true);

                // Check supervisor access
                if (auth()->user()->isSupervisor()) {
                    $kriteriaQuery->where('assigned_to_supervisor_id', auth()->id());
                }

                $singleKriteria = $kriteriaQuery
                    ->with([
                        'subKriteria' => function ($query) {
                            $query->where('is_active', true)
                                ->with(['dropdownOptions' => function ($q) {
                                    $q->where('is_active', true)->orderBy('urutan', 'asc');
                                }])
                                ->orderBy('urutan', 'asc');
                        },
                        'dropdownOptions' => function ($q) {
                            // Load dropdown options for kriteria with direct tipe_input (no sub-kriteria)
                            $q->where('is_active', true)->orderBy('urutan', 'asc');
                        }
                    ])
                    ->first();

                // If kriteria not found or no access, redirect back with error
                if (!$singleKriteria) {
                    return redirect()->route('penilaian.overview', [
                        'karyawanId' => $karyawanId,
                        'bulan' => $bulan,
                        'tahun' => $tahun
                    ])->with('error', 'Kriteria tidak ditemukan atau Anda tidak memiliki akses ke kriteria ini.');
                }

                // Use single kriteria in array for view compatibility
                $kriteria = collect([$singleKriteria]);
            } else {
                // OLD FLOW: Get all kriteria (legacy flow)
                $kriteriaQuery = SistemKriteria::where('level', 1)
                    ->where('is_active', true);

                // If supervisor, only show assigned kriteria
                if (auth()->user()->isSupervisor()) {
                    $kriteriaQuery->where('assigned_to_supervisor_id', auth()->id());
                }
                $kriteria = $kriteriaQuery
                    ->with([
                        'subKriteria' => function ($query) {
                            $query->where('is_active', true)
                                ->with(['dropdownOptions' => function ($q) {
                                    $q->where('is_active', true)->orderBy('urutan', 'asc');
                                }])
                                ->orderBy('urutan', 'asc');
                        },
                        'dropdownOptions' => function ($q) {
                            // Load dropdown options for kriteria with direct tipe_input (no sub-kriteria)
                            $q->where('is_active', true)->orderBy('urutan', 'asc');
                        }
                    ])
                    ->orderBy('urutan', 'asc')
                    ->get();
            }

            // Check existing penilaian untuk periode ini
            $existingPenilaian = Penilaian::byKaryawan($karyawanId)
                ->byPeriode($bulan, $tahun)
                ->get()
                ->keyBy('id_sub_kriteria');
        }

        return view('penilaian.create', compact(
            'karyawanList',
            'karyawan',
            'kriteria',
            'singleKriteria',
            'bulan',
            'tahun',
            'existingPenilaian',
            'periodeLabel'
        ));
    }

    /**
     * Store a newly created penilaian in storage.
     * Menyimpan multiple penilaian sekaligus (batch insert untuk satu karyawan satu periode)
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'karyawan_id' => 'required|exists:users,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2100',
            'penilaian' => 'required|array|min:1',
            'penilaian.*.id_kriteria' => 'required|exists:sistem_kriteria,id',
            'penilaian.*.id_sub_kriteria' => 'required|exists:sistem_kriteria,id',
            'penilaian.*.nilai' => 'required|numeric|min:0',
        ], [
            'karyawan_id.required' => 'Karyawan wajib dipilih',
            'karyawan_id.exists' => 'Karyawan tidak ditemukan',
            'bulan.required' => 'Bulan penilaian wajib dipilih',
            'bulan.integer' => 'Bulan harus berupa angka',
            'bulan.min' => 'Bulan minimal 1 (Januari)',
            'bulan.max' => 'Bulan maksimal 12 (Desember)',
            'tahun.required' => 'Tahun penilaian wajib diisi',
            'tahun.integer' => 'Tahun harus berupa angka',
            'penilaian.required' => 'Data penilaian wajib diisi',
            'penilaian.array' => 'Format data penilaian tidak valid',
            'penilaian.min' => 'Minimal harus ada 1 penilaian',
            'penilaian.*.nilai.required' => 'Nilai wajib diisi untuk semua sub-kriteria',
            'penilaian.*.nilai.numeric' => 'Nilai harus berupa angka',
        ]);

        try {
            DB::beginTransaction();

            $karyawanId = $request->karyawan_id;
            $bulan = $request->bulan;
            $tahun = $request->tahun;
            $tanggalPenilaian = Carbon::now();

            // Generate periode label
            $namaBulan = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];
            $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

            // Delete only existing penilaian for specific sub-kriteria being submitted
            // (tidak delete semua periode, hanya yang sedang di-input)
            $subKriteriaIds = collect($request->penilaian)->pluck('id_sub_kriteria')->toArray();
            Penilaian::byKaryawan($karyawanId)
                ->byPeriode($bulan, $tahun)
                ->whereIn('id_sub_kriteria', $subKriteriaIds)
                ->forceDelete();

            // Insert batch penilaian
            $insertedCount = 0;
            foreach ($request->penilaian as $data) {
                Penilaian::create([
                    'id_karyawan' => $karyawanId,
                    'id_kriteria' => $data['id_kriteria'],
                    'id_sub_kriteria' => $data['id_sub_kriteria'],
                    'nilai' => $data['nilai'],
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'periode_label' => $periodeLabel,
                    'catatan' => $data['catatan'] ?? null,
                    'tanggal_penilaian' => $tanggalPenilaian,
                    'dinilai_oleh_supervisor_id' => auth()->user()->role === 'supervisor' ? auth()->id() : null,
                    'created_by_super_admin_id' => auth()->user()->role === 'super_admin' ? auth()->id() : null,
                    'created_by_hrd_id' => auth()->user()->role === 'hrd' ? auth()->id() : null,
                ]);
                $insertedCount++;
            }

            DB::commit();

            // Check if we should redirect to overview (single kriteria mode from query param)
            if ($request->has('from_overview') || $request->session()->get('from_overview')) {
                return redirect()->route('penilaian.overview', [
                    'karyawanId' => $karyawanId,
                    'bulan' => $bulan,
                    'tahun' => $tahun
                ])->with('success', "Berhasil menyimpan {$insertedCount} penilaian untuk periode {$periodeLabel}");
            }

            return redirect()->route('penilaian.index')
                ->with('success', "Berhasil menyimpan {$insertedCount} penilaian untuk periode {$periodeLabel}");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified penilaian.
     * Menampilkan detail lengkap penilaian per karyawan per periode
     */
    public function show($karyawanId, $bulan, $tahun)
    {
        $karyawan = User::findOrFail($karyawanId);

        // Get all penilaian untuk karyawan & periode ini
        $penilaianList = Penilaian::byKaryawan($karyawanId)
            ->byPeriode($bulan, $tahun)
            ->with(['kriteria', 'subKriteria', 'dinilaiOlehSupervisor'])
            ->get();

        if ($penilaianList->isEmpty()) {
            return redirect()->route('penilaian.index')
                ->with('error', 'Penilaian tidak ditemukan untuk periode ini');
        }

        // Group by kriteria untuk tampilan terstruktur
        $penilaianGrouped = $penilaianList->groupBy('id_kriteria');

        // Get kriteria info
        $kriteriaList = SistemKriteria::whereIn('id', $penilaianGrouped->keys())
            ->get()
            ->keyBy('id');

        // Get periode info
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

        return view('penilaian.show', compact(
            'karyawan',
            'penilaianList',
            'penilaianGrouped',
            'kriteriaList',
            'bulan',
            'tahun',
            'periodeLabel'
        ));
    }

    /**
     * Show the form for editing the specified penilaian.
     * Form edit dengan data pre-filled
     */
    public function edit($karyawanId, $bulan, $tahun)
    {
        $karyawan = User::findOrFail($karyawanId);

        // Get existing penilaian
        $existingPenilaian = Penilaian::byKaryawan($karyawanId)
            ->byPeriode($bulan, $tahun)
            ->with(['kriteria', 'subKriteria'])
            ->get()
            ->keyBy('id_sub_kriteria');

        if ($existingPenilaian->isEmpty()) {
            return redirect()->route('penilaian.index')
                ->with('error', 'Penilaian tidak ditemukan untuk periode ini');
        }

        // Get all active kriteria dengan sub-kriteria & dropdown options
        $kriteria = SistemKriteria::where('level', 1)
            ->where('is_active', true)
            ->with(['subKriteria' => function ($query) {
                $query->where('is_active', true)
                    ->with(['dropdownOptions' => function ($q) {
                        $q->where('is_active', true)->orderBy('urutan', 'asc');
                    }])
                    ->orderBy('urutan', 'asc');
            }])
            ->orderBy('urutan', 'asc')
            ->get();

        return view('penilaian.edit', compact(
            'karyawan',
            'kriteria',
            'bulan',
            'tahun',
            'existingPenilaian'
        ));
    }

    /**
     * Update the specified penilaian in storage.
     * Update batch penilaian untuk satu periode
     */
    public function update(Request $request, $karyawanId, $bulan, $tahun)
    {
        // Validation
        $request->validate([
            'penilaian' => 'required|array|min:1',
            'penilaian.*.id_kriteria' => 'required|exists:sistem_kriteria,id',
            'penilaian.*.id_sub_kriteria' => 'required|exists:sistem_kriteria,id',
            'penilaian.*.nilai' => 'required|numeric|min:0',
        ], [
            'penilaian.required' => 'Data penilaian wajib diisi',
            'penilaian.*.nilai.required' => 'Nilai wajib diisi untuk semua sub-kriteria',
        ]);

        try {
            DB::beginTransaction();

            $tanggalPenilaian = Carbon::now();

            // Generate periode label
            $namaBulan = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];
            $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

            // Delete existing penilaian
            Penilaian::byKaryawan($karyawanId)
                ->byPeriode($bulan, $tahun)
                ->forceDelete();

            // Re-insert dengan data baru
            $updatedCount = 0;
            foreach ($request->penilaian as $data) {
                Penilaian::create([
                    'id_karyawan' => $karyawanId,
                    'id_kriteria' => $data['id_kriteria'],
                    'id_sub_kriteria' => $data['id_sub_kriteria'],
                    'nilai' => $data['nilai'],
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'periode_label' => $periodeLabel,
                    'catatan' => $data['catatan'] ?? null,
                    'tanggal_penilaian' => $tanggalPenilaian,
                    'dinilai_oleh_supervisor_id' => auth()->user()->role === 'supervisor' ? auth()->id() : null,
                    'created_by_super_admin_id' => auth()->user()->role === 'super_admin' ? auth()->id() : null,
                    'created_by_hrd_id' => auth()->user()->role === 'hrd' ? auth()->id() : null,
                ]);
                $updatedCount++;
            }

            DB::commit();

            return redirect()->route('penilaian.show', [$karyawanId, $bulan, $tahun])
                ->with('success', "Berhasil mengupdate {$updatedCount} penilaian untuk periode {$periodeLabel}");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate penilaian: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified penilaian from storage.
     * Menghapus semua penilaian untuk satu karyawan di satu periode
     */
    public function destroy($karyawanId, $bulan, $tahun)
    {
        try {
            // Get periode label for message
            $namaBulan = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];
            $periodeLabel = $namaBulan[$bulan] . ' ' . $tahun;

            // Soft delete
            $deleted = Penilaian::byKaryawan($karyawanId)
                ->byPeriode($bulan, $tahun)
                ->delete();

            if ($deleted > 0) {
                return redirect()->route('penilaian.index')
                    ->with('success', "Berhasil menghapus penilaian periode {$periodeLabel}");
            } else {
                return redirect()->route('penilaian.index')
                    ->with('error', 'Penilaian tidak ditemukan');
            }
        } catch (\Exception $e) {
            return redirect()->route('penilaian.index')
                ->with('error', 'Gagal menghapus penilaian: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SistemKriteria;
use App\Models\Penilaian;
use App\Models\HasilTopsis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  public function index()
  {
    $stats = [];

    // Get current month and year
    $currentMonth = date('n');
    $currentYear = date('Y');

    if (auth()->user()->isSuperAdmin() || auth()->user()->isHRD()) {
      // Admin & HRD: Show all statistics
      $stats = [
        'total_karyawan' => User::where('role', 'karyawan')
          ->where('status_akun', 'aktif')
          ->count(),
        'total_supervisor' => User::where('role', 'supervisor')
          ->where('status_akun', 'aktif')
          ->count(),
        'total_kriteria' => SistemKriteria::where('level', 1)
          ->where('is_active', true)
          ->count(),
        'total_penilaian_bulan_ini' => Penilaian::where('bulan', $currentMonth)
          ->where('tahun', $currentYear)
          ->distinct('id_karyawan')
          ->count('id_karyawan'),
        'ranking_generated' => HasilTopsis::where('bulan', $currentMonth)
          ->where('tahun', $currentYear)
          ->exists(),
      ];
    } elseif (auth()->user()->isSupervisor()) {
      // Supervisor: Show total karyawan only
      $stats = [
        'total_karyawan' => User::where('role', 'karyawan')
          ->where('status_akun', 'aktif')
          ->count(),
      ];
    } else {
      // Karyawan: Show personal stats
      // Get all ranking history for this karyawan (all months & divisions)
      $rankingHistory = HasilTopsis::where('id_karyawan', auth()->id())
        ->orderByRaw('tahun DESC, bulan DESC')
        ->orderBy('divisi_filter', 'asc')
        ->get()
        ->map(function ($hasil) {
          // Get total karyawan for this periode & divisi
          $totalQuery = HasilTopsis::where('bulan', $hasil->bulan)
            ->where('tahun', $hasil->tahun);

          if ($hasil->divisi_filter) {
            $totalQuery->where('divisi_filter', $hasil->divisi_filter);
          } else {
            $totalQuery->whereNull('divisi_filter');
          }

          $totalKaryawan = $totalQuery->count();

          return [
            'bulan' => $hasil->bulan,
            'tahun' => $hasil->tahun,
            'periode_label' => $hasil->periode_label,
            'divisi_label' => $hasil->divisi_filter ?? 'Semua Divisi',
            'ranking' => $hasil->ranking,
            'skor_topsis' => $hasil->skor_topsis,
            'total_karyawan' => $totalKaryawan,
            'tanggal_generate' => $hasil->tanggal_generate,
          ];
        });

      // Get latest ranking (most recent periode, prioritize "Semua Divisi")
      $latestRanking = $rankingHistory->first();

      $stats = [
        'total_penilaian_saya' => Penilaian::where('id_karyawan', auth()->id())
          ->where('bulan', $currentMonth)
          ->where('tahun', $currentYear)
          ->count(),
        'ranking_saya' => $latestRanking['ranking'] ?? null,
        'skor_topsis_saya' => $latestRanking['skor_topsis'] ?? null,
        'total_karyawan_periode_ini' => $latestRanking['total_karyawan'] ?? 0,
        'latest_periode_label' => $latestRanking['periode_label'] ?? null,
        'latest_divisi_label' => $latestRanking['divisi_label'] ?? null,
        'ranking_history' => $rankingHistory,
      ];
    }

    $currentPeriod = [
      'bulan' => $currentMonth,
      'tahun' => $currentYear,
      'label' => $this->getBulanLabel($currentMonth) . ' ' . $currentYear
    ];

    return view('dashboard', compact('stats', 'currentPeriod'));
  }

  private function getBulanLabel($bulan)
  {
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
    return $namaBulan[$bulan] ?? '';
  }
}

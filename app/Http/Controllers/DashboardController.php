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
      $stats = [
        'total_penilaian_saya' => Penilaian::where('id_karyawan', auth()->id())
          ->where('bulan', $currentMonth)
          ->where('tahun', $currentYear)
          ->count(),
        'ranking_saya' => HasilTopsis::where('id_karyawan', auth()->id())
          ->where('bulan', $currentMonth)
          ->where('tahun', $currentYear)
          ->whereNull('divisi_filter')
          ->value('ranking'),
        'skor_topsis_saya' => HasilTopsis::where('id_karyawan', auth()->id())
          ->where('bulan', $currentMonth)
          ->where('tahun', $currentYear)
          ->whereNull('divisi_filter')
          ->value('skor_topsis'),
        'total_karyawan_periode_ini' => HasilTopsis::where('bulan', $currentMonth)
          ->where('tahun', $currentYear)
          ->whereNull('divisi_filter')
          ->count(),
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

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Ranking Lengkap - {{ $periodeLabel }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 3px solid #4F46E5;
        }

        .header h1 {
            font-size: 18px;
            color: #4F46E5;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 12px;
            color: #666;
            font-weight: normal;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .summary-card {
            background: #F3F4F6;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            border: 1px solid #E5E7EB;
        }

        .summary-label {
            font-size: 9px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #4F46E5;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #4F46E5;
            margin: 20px 0 10px 0;
            padding-bottom: 6px;
            border-bottom: 2px solid #E5E7EB;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9px;
        }

        table th {
            background: #4F46E5;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
        }

        table td {
            padding: 6px;
            border-bottom: 1px solid #E5E7EB;
        }

        table tr:nth-child(even) {
            background: #F9FAFB;
        }

        .rank-1 {
            background: #FDE68A !important;
            font-weight: bold;
        }

        .rank-2 {
            background: #E5E7EB !important;
            font-weight: bold;
        }

        .rank-3 {
            background: #FED7AA !important;
            font-weight: bold;
        }

        .rank-badge {
            display: inline-block;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            text-align: center;
            font-weight: bold;
            color: white;
            padding-top: 5px;
            vertical-align: middle;
        }

        .badge-gold {
            background: linear-gradient(135deg, #FCD34D, #F59E0B);
        }

        .badge-silver {
            background: linear-gradient(135deg, #D1D5DB, #9CA3AF);
        }

        .badge-bronze {
            background: linear-gradient(135deg, #FDBA74, #FB923C);
        }

        .badge-other {
            background: #6B7280;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 25px;
            padding-top: 12px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
            font-size: 8px;
            color: #666;
        }

        .info-box {
            background: #EEF2FF;
            border: 1px solid #C7D2FE;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 9px;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <h1>LAPORAN HASIL PERANGKINGAN KARYAWAN</h1>
        <h2>Sistem Penilaian Kinerja Karyawan - Metode TOPSIS</h2>
        <h2>Periode: {{ $periodeLabel }}</h2>
    </div>

    {{-- Summary Cards --}}
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">Total Karyawan</div>
            <div class="summary-value">{{ $hasilRanking->count() }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Skor Tertinggi</div>
            <div class="summary-value">{{ number_format($hasilRanking->max('skor_topsis') * 100, 2) }}%</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Skor Terendah</div>
            <div class="summary-value">{{ number_format($hasilRanking->min('skor_topsis') * 100, 2) }}%</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Rata-rata Skor</div>
            <div class="summary-value">{{ number_format($hasilRanking->avg('skor_topsis') * 100, 2) }}%</div>
        </div>
    </div>

    {{-- Info Generate --}}
    <div class="info-box">
        <strong>Informasi Generate:</strong><br>
        Tanggal: {{ $tanggalGenerate->format('d F Y, H:i') }} WIB |
        Oleh: {{ $generatedBy->nama }} ({{ ucfirst($generatedBy->role) }})
    </div>

    {{-- Podium Top 3 --}}
    @if ($hasilRanking->count() >= 3)
        <h3 class="section-title">🏆 Top 3 Karyawan Terbaik</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%; text-align: center;">Rank</th>
                    <th style="width: 15%;">NIK</th>
                    <th style="width: 25%;">Nama Karyawan</th>
                    <th style="width: 17%;">Divisi</th>
                    <th style="width: 17%;">Jabatan</th>
                    <th style="width: 18%; text-align: center;">Skor TOPSIS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hasilRanking->take(3) as $hasil)
                    <tr class="rank-{{ $hasil->ranking }}">
                        <td class="text-center">
                            <span
                                class="rank-badge {{ $hasil->ranking == 1 ? 'badge-gold' : ($hasil->ranking == 2 ? 'badge-silver' : 'badge-bronze') }}">
                                {{ $hasil->ranking }}
                            </span>
                        </td>
                        <td>{{ $hasil->karyawan->nik }}</td>
                        <td><strong>{{ $hasil->karyawan->nama }}</strong></td>
                        <td>{{ $hasil->karyawan->divisi }}</td>
                        <td>{{ $hasil->karyawan->jabatan }}</td>
                        <td class="text-center">
                            <strong>{{ number_format($hasil->skor_topsis * 100, 2) }}%</strong>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Full Ranking Table --}}
    <h3 class="section-title">Tabel Ranking Lengkap</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">Rank</th>
                <th style="width: 12%;">NIK</th>
                <th style="width: 22%;">Nama Karyawan</th>
                <th style="width: 15%;">Divisi</th>
                <th style="width: 15%;">Jabatan</th>
                <th style="width: 10%; text-align: center;">Skor</th>
                <th style="width: 10%; text-align: center;">D+</th>
                <th style="width: 10%; text-align: center;">D-</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($hasilRanking as $hasil)
                <tr class="{{ $hasil->ranking <= 3 ? 'rank-' . $hasil->ranking : '' }}">
                    <td class="text-center">
                        @if ($hasil->ranking <= 3)
                            <span
                                class="rank-badge {{ $hasil->ranking == 1 ? 'badge-gold' : ($hasil->ranking == 2 ? 'badge-silver' : 'badge-bronze') }}">
                                {{ $hasil->ranking }}
                            </span>
                        @else
                            <span class="rank-badge badge-other">{{ $hasil->ranking }}</span>
                        @endif
                    </td>
                    <td>{{ $hasil->karyawan->nik }}</td>
                    <td>{{ $hasil->karyawan->nama }}</td>
                    <td>{{ $hasil->karyawan->divisi }}</td>
                    <td>{{ $hasil->karyawan->jabatan }}</td>
                    <td class="text-center">{{ number_format($hasil->skor_topsis * 100, 2) }}%</td>
                    <td class="text-center">{{ number_format($hasil->jarak_ideal_positif, 4) }}</td>
                    <td class="text-center">{{ number_format($hasil->jarak_ideal_negatif, 4) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Perangkingan Karyawan</p>
        <p>Tanggal Generate: {{ now()->format('d F Y, H:i') }} WIB</p>
        <p style="margin-top: 5px; font-style: italic;">Confidential - Untuk Penggunaan Internal Only</p>
    </div>
</body>

</html>

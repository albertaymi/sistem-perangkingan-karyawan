<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Ranking Pribadi - {{ $hasil->karyawan->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #4F46E5;
        }

        .header h1 {
            font-size: 20px;
            color: #4F46E5;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14px;
            color: #666;
            font-weight: normal;
        }

        .info-box {
            background: #F3F4F6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #4F46E5;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        .ranking-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 18px;
            font-weight: bold;
            color: white;
            margin: 10px 0;
        }

        .rank-1 { background: linear-gradient(135deg, #FDE68A, #FCD34D); color: #92400E; }
        .rank-2 { background: linear-gradient(135deg, #E5E7EB, #D1D5DB); color: #374151; }
        .rank-3 { background: linear-gradient(135deg, #FED7AA, #FDBA74); color: #92400E; }
        .rank-other { background: linear-gradient(135deg, #6366F1, #4F46E5); }

        .score-card {
            background: white;
            border: 2px solid #E5E7EB;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .score-card h3 {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
        }

        .score-value {
            font-size: 28px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 5px;
        }

        .score-percentage {
            font-size: 14px;
            color: #666;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #4F46E5;
            margin: 25px 0 12px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #E5E7EB;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th {
            background: #4F46E5;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }

        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 10px;
        }

        table tr:nth-child(even) {
            background: #F9FAFB;
        }

        .formula-box {
            background: #FEF3C7;
            border: 1px solid #FCD34D;
            padding: 12px;
            border-radius: 6px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 10px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
            font-size: 9px;
            color: #666;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .metric-card {
            background: #F9FAFB;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #E5E7EB;
        }

        .metric-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }

        .metric-value {
            font-size: 16px;
            font-weight: bold;
            color: #4F46E5;
        }

        .page-break {
            page-break-after: always;
        }

        .text-center {
            text-align: center;
        }

        .highlight {
            background: #DBEAFE;
            padding: 2px 6px;
            border-radius: 3px;
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

    {{-- Informasi Karyawan --}}
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">NIK</span>
            <span class="info-value">: {{ $hasil->karyawan->nik }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Nama Karyawan</span>
            <span class="info-value">: {{ $hasil->karyawan->nama }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Divisi</span>
            <span class="info-value">: {{ $hasil->karyawan->divisi }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Jabatan</span>
            <span class="info-value">: {{ $hasil->karyawan->jabatan }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email</span>
            <span class="info-value">: {{ $hasil->karyawan->email }}</span>
        </div>
    </div>

    {{-- Ranking Badge --}}
    <div class="text-center">
        <span class="ranking-badge {{ $hasil->ranking == 1 ? 'rank-1' : ($hasil->ranking == 2 ? 'rank-2' : ($hasil->ranking == 3 ? 'rank-3' : 'rank-other')) }}">
            Peringkat #{{ $hasil->ranking }} dari {{ $totalKaryawan }} Karyawan
        </span>
    </div>

    {{-- Skor TOPSIS --}}
    <div class="score-card">
        <h3>Skor TOPSIS</h3>
        <div class="score-value">{{ number_format($hasil->skor_topsis, 4) }}</div>
        <div class="score-percentage">({{ number_format($hasil->skor_topsis * 100, 2) }}%)</div>
    </div>

    {{-- Distance Metrics --}}
    <div class="grid-2">
        <div class="metric-card">
            <div class="metric-label">D+ (Jarak ke Solusi Ideal Positif)</div>
            <div class="metric-value">{{ number_format($hasil->d_positif, 6) }}</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">D- (Jarak ke Solusi Ideal Negatif)</div>
            <div class="metric-value">{{ number_format($hasil->d_negatif, 6) }}</div>
        </div>
    </div>

    {{-- Nilai Per Kriteria --}}
    <h3 class="section-title">Nilai Per Kriteria</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Kriteria</th>
                <th style="width: 15%; text-align: center;">Bobot</th>
                <th style="width: 15%; text-align: center;">Tipe</th>
                <th style="width: 20%; text-align: center;">Skor Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kriteriaScores as $kriteria)
                <tr>
                    <td>{{ $kriteria['nama'] }}</td>
                    <td style="text-align: center;">{{ $kriteria['bobot'] }}%</td>
                    <td style="text-align: center;">{{ $kriteria['tipe'] }}</td>
                    <td style="text-align: center;"><strong>{{ $kriteria['nilai'] }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Page Break --}}
    <div class="page-break"></div>

    {{-- Detail Perhitungan TOPSIS --}}
    <h3 class="section-title">Detail Perhitungan TOPSIS</h3>

    <p style="margin-bottom: 15px; text-align: justify;">
        Metode TOPSIS (Technique for Order of Preference by Similarity to Ideal Solution) menghitung jarak terhadap solusi ideal positif dan negatif untuk menentukan ranking karyawan terbaik.
    </p>

    {{-- Decision Matrix --}}
    <h4 style="font-size: 12px; margin: 15px 0 10px 0; color: #333;">1. Decision Matrix (Matriks Keputusan)</h4>
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Kriteria</th>
                <th style="width: 50%; text-align: center;">Nilai Original</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detailPerhitungan['decision_matrix'] ?? [] as $key => $value)
                <tr>
                    <td>{{ $key }}</td>
                    <td style="text-align: center;">{{ number_format($value, 4) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Normalized Matrix --}}
    <h4 style="font-size: 12px; margin: 15px 0 10px 0; color: #333;">2. Normalized Matrix (Matriks Ternormalisasi)</h4>
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Kriteria</th>
                <th style="width: 50%; text-align: center;">Nilai Normalisasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detailPerhitungan['normalized_matrix'] ?? [] as $key => $value)
                <tr>
                    <td>{{ $key }}</td>
                    <td style="text-align: center;">{{ number_format($value, 6) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Weighted Matrix --}}
    <h4 style="font-size: 12px; margin: 15px 0 10px 0; color: #333;">3. Weighted Matrix (Matriks Terbobot)</h4>
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Kriteria</th>
                <th style="width: 50%; text-align: center;">Nilai Terbobot</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detailPerhitungan['weighted_matrix'] ?? [] as $key => $value)
                <tr>
                    <td>{{ $key }}</td>
                    <td style="text-align: center;">{{ number_format($value, 6) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Formula TOPSIS --}}
    <h4 style="font-size: 12px; margin: 15px 0 10px 0; color: #333;">4. Perhitungan Skor TOPSIS</h4>
    <div class="formula-box">
        <strong>Formula:</strong> V = D- / (D+ + D-)<br><br>
        <strong>Perhitungan:</strong><br>
        V = {{ number_format($hasil->d_negatif, 6) }} / ({{ number_format($hasil->d_positif, 6) }} + {{ number_format($hasil->d_negatif, 6) }})<br>
        V = {{ number_format($hasil->d_negatif, 6) }} / {{ number_format($hasil->d_positif + $hasil->d_negatif, 6) }}<br>
        V = <span class="highlight">{{ number_format($hasil->skor_topsis, 6) }}</span>
    </div>

    {{-- Kesimpulan --}}
    <h3 class="section-title">Kesimpulan</h3>
    <div style="background: #DBEAFE; padding: 15px; border-radius: 8px; border-left: 4px solid #3B82F6;">
        <p style="margin-bottom: 8px;">
            <strong>{{ $hasil->karyawan->nama }}</strong> memperoleh peringkat <strong>#{{ $hasil->ranking }}</strong> dari total <strong>{{ $totalKaryawan }}</strong> karyawan yang dinilai pada periode <strong>{{ $periodeLabel }}</strong>.
        </p>
        <p style="margin-bottom: 8px;">
            Dengan skor TOPSIS sebesar <strong>{{ number_format($hasil->skor_topsis, 4) }}</strong> ({{ number_format($hasil->skor_topsis * 100, 2) }}%), menunjukkan tingkat kinerja yang
            @if($hasil->ranking <= 3)
                <strong style="color: #059669;">sangat baik</strong>
            @elseif($hasil->ranking <= 10)
                <strong style="color: #3B82F6;">baik</strong>
            @else
                <strong>cukup baik</strong>
            @endif
            dibandingkan dengan karyawan lainnya.
        </p>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Perangkingan Karyawan</p>
        <p>Tanggal Generate: {{ $hasil->tanggal_generate->format('d F Y, H:i') }} WIB</p>
        <p style="margin-top: 5px; font-style: italic;">Confidential - Untuk Penggunaan Internal Only</p>
    </div>
</body>
</html>

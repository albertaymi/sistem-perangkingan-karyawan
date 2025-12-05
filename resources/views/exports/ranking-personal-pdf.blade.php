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

        .rank-1 {
            background: linear-gradient(135deg, #FDE68A, #FCD34D);
            color: #92400E;
        }

        .rank-2 {
            background: linear-gradient(135deg, #E5E7EB, #D1D5DB);
            color: #374151;
        }

        .rank-3 {
            background: linear-gradient(135deg, #FED7AA, #FDBA74);
            color: #92400E;
        }

        .rank-other {
            background: linear-gradient(135deg, #6366F1, #4F46E5);
        }

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
    </div>

    {{-- Ranking Badge --}}
    <div class="text-center">
        <span
            class="ranking-badge {{ $hasil->ranking == 1 ? 'rank-1' : ($hasil->ranking == 2 ? 'rank-2' : ($hasil->ranking == 3 ? 'rank-3' : 'rank-other')) }}">
            Peringkat #{{ $hasil->ranking }} dari {{ $totalKaryawan }} Karyawan
        </span>
    </div>

    {{-- Detail Penilaian Per Kriteria --}}
    @php
        $kriteriaWithPenilaian = [];
        foreach ($kriteriaData as $kriteria) {
            $penilaianItems = [];
            $totalSkor = 0;
            $hasSubKriteria = $kriteria->subKriteria->count() > 0;

            if ($hasSubKriteria) {
                foreach ($kriteria->subKriteria as $subKriteria) {
                    $penilaian = $hasil->karyawan
                        ->penilaian()
                        ->where('id_sub_kriteria', $subKriteria->id)
                        ->where('bulan', $hasil->bulan)
                        ->where('tahun', $hasil->tahun)
                        ->first();

                    if ($penilaian) {
                        $penilaianItems[] = [
                            'nama' => $subKriteria->nama_kriteria,
                            'bobot' => $subKriteria->bobot,
                            'nilai' => $penilaian->nilai,
                            'tipe_input' => $subKriteria->tipe_input,
                            'tipe_kriteria' => $subKriteria->tipe_kriteria,
                            'nilai_min' => $subKriteria->nilai_min,
                            'nilai_max' => $subKriteria->nilai_max,
                        ];
                        $totalSkor += $penilaian->nilai;
                    }
                }
            } else {
                $penilaian = $hasil->karyawan
                    ->penilaian()
                    ->where('id_kriteria', $kriteria->id)
                    ->where('bulan', $hasil->bulan)
                    ->where('tahun', $hasil->tahun)
                    ->first();

                if ($penilaian) {
                    $penilaianItems[] = [
                        'nama' => $kriteria->nama_kriteria,
                        'bobot' => $kriteria->bobot,
                        'nilai' => $penilaian->nilai,
                        'tipe_input' => $kriteria->tipe_input,
                        'tipe_kriteria' => $kriteria->tipe_kriteria,
                        'nilai_min' => $kriteria->nilai_min,
                        'nilai_max' => $kriteria->nilai_max,
                    ];
                    $totalSkor = $penilaian->nilai;
                }
            }

            $kriteriaWithPenilaian[] = [
                'kriteria' => $kriteria,
                'items' => $penilaianItems,
                'totalSkor' => $totalSkor,
                'hasSubKriteria' => $hasSubKriteria,
            ];
        }
    @endphp

    @foreach ($kriteriaWithPenilaian as $data)
        @php
            $kriteria = $data['kriteria'];
            $isBenefit = $kriteria->tipe_kriteria === 'benefit';
            $bgColor = $isBenefit ? '#ecfdf5' : '#fef2f2';
            $borderColor = $isBenefit ? '#10b981' : '#ef4444';
            $textColor = $isBenefit ? '#059669' : '#dc2626';
        @endphp

        <div class="card" style="border-left: 4px solid {{ $borderColor }}; background: {{ $bgColor }};">
            <h3 style="color: {{ $textColor }}; margin-bottom: 15px; font-size: 12px; font-weight: bold;">
                {{ $kriteria->nama_kriteria }}
                ({{ $isBenefit ? 'Benefit +' : 'Cost -' }})
                - Bobot: {{ $kriteria->bobot }}%
            </h3>

            <table class="table" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="text-align: left; width: 30%;">
                            {{ $data['hasSubKriteria'] ? 'Sub-Kriteria' : 'Kriteria' }}
                        </th>
                        <th style="text-align: center; width: 12%;">Tipe</th>
                        <th style="text-align: center; width: 12%;">Bobot (%)</th>
                        <th style="text-align: left; width: 26%;">Tipe Input</th>
                        <th style="text-align: center; width: 20%;">Skor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['items'] as $item)
                        @php
                            $itemIsBenefit = ($item['tipe_kriteria'] ?? 'benefit') === 'benefit';
                            $itemBadgeColor = $itemIsBenefit ? '#059669' : '#dc2626';
                            $itemBadgeBg = $itemIsBenefit ? '#d1fae5' : '#fee2e2';
                        @endphp
                        <tr>
                            <td style="font-weight: 600;">{{ $item['nama'] }}</td>
                            <td style="text-align: center;">
                                <span
                                    style="display: inline-block; padding: 2px 6px; font-size: 8px; font-weight: 600; border-radius: 4px; background: {{ $itemBadgeBg }}; color: {{ $itemBadgeColor }};">
                                    {{ $itemIsBenefit ? 'Benefit +' : 'Cost -' }}
                                </span>
                            </td>
                            <td style="text-align: center;">{{ $item['bobot'] }}%</td>
                            <td style="font-size: 10px;">
                                @if ($item['tipe_input'] === 'angka')
                                    Input Angka ({{ $item['nilai_min'] ?? 0 }}-{{ $item['nilai_max'] ?? 100 }})
                                @elseif($item['tipe_input'] === 'rating')
                                    Rating Scale (1-5)
                                @elseif($item['tipe_input'] === 'dropdown')
                                    Dropdown
                                @else
                                    -
                                @endif
                            </td>
                            <td style="text-align: center; font-weight: 700; color: {{ $textColor }};">
                                {{ number_format($item['nilai'], 0) }}
                            </td>
                        </tr>
                    @endforeach

                    @if ($data['hasSubKriteria'])
                        <tr style="background: #f9fafb; font-weight: bold;">
                            <td colspan="4" style="text-align: left;">
                                Total Skor {{ $kriteria->nama_kriteria }}
                            </td>
                            <td style="text-align: center; font-size: 14px; color: {{ $textColor }};">
                                {{ number_format($data['totalSkor'], 0) }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @endforeach

    {{-- Ringkasan Keseluruhan --}}
    <div class="card"
        style="background: linear-gradient(to right, #f9fafb, #ffffff); border: 2px solid #e5e7eb; page-break-inside: avoid;">
        <h3 class="card-title" style="margin-bottom: 15px;">📊 Ringkasan Keseluruhan</h3>

        <table style="width: 100%; margin-bottom: 15px; border-collapse: collapse;">
            <tr>
                <td
                    style="width: 33%; text-align: center; background: #eff6ff; padding: 15px; border-radius: 8px; vertical-align: top;">
                    <div style="font-size: 10px; color: #2563eb; font-weight: 600; margin-bottom: 5px;">
                        Skor TOPSIS Final
                    </div>
                    <div style="font-size: 20px; font-weight: bold; color: #1e40af;">
                        {{ number_format($hasil->skor_topsis, 4) }}
                    </div>
                    <div style="font-size: 10px; color: #2563eb; margin-top: 3px;">
                        {{ number_format($hasil->skor_topsis * 100, 2) }}%
                    </div>
                </td>
                <td style="width: 1%;"></td>
                <td
                    style="width: 33%; text-align: center; background: #f0fdf4; padding: 15px; border-radius: 8px; vertical-align: top;">
                    <div style="font-size: 10px; color: #16a34a; font-weight: 600; margin-bottom: 5px;">
                        Jarak Ideal Positif (D+)
                    </div>
                    <div style="font-size: 20px; font-weight: bold; color: #15803d;">
                        {{ number_format($hasil->jarak_ideal_positif, 4) }}
                    </div>
                    <div style="font-size: 9px; color: #16a34a; margin-top: 3px;">
                        Semakin kecil semakin baik
                    </div>
                </td>
                <td style="width: 1%;"></td>
                <td
                    style="width: 33%; text-align: center; background: #fef2f2; padding: 15px; border-radius: 8px; vertical-align: top;">
                    <div style="font-size: 10px; color: #dc2626; font-weight: 600; margin-bottom: 5px;">
                        Jarak Ideal Negatif (D-)
                    </div>
                    <div style="font-size: 20px; font-weight: bold; color: #b91c1c;">
                        {{ number_format($hasil->jarak_ideal_negatif, 4) }}
                    </div>
                    <div style="font-size: 9px; color: #dc2626; margin-top: 3px;">
                        Semakin besar semakin baik
                    </div>
                </td>
            </tr>
        </table>

        <div
            style="text-align: center; padding: 15px; background: linear-gradient(to right, #fef3c7, #fed7aa); border: 1px solid #fbbf24; border-radius: 8px;">
            <p style="font-size: 11px; color: #374151; margin: 0; line-height: 1.6;">
                Dengan skor <strong style="color: #1e40af;">{{ number_format($hasil->skor_topsis, 4) }}</strong>,
                <strong>{{ $hasil->karyawan->nama }}</strong>
                berada di peringkat
                <strong
                    style="display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px;
                    @if ($hasil->ranking == 1) background: #fde047; color: #854d0e;
                    @elseif($hasil->ranking == 2) background: #e5e7eb; color: #1f2937;
                    @elseif($hasil->ranking == 3) background: #fed7aa; color: #9a3412;
                    @else background: #dbeafe; color: #1e40af; @endif
                ">
                    #{{ $hasil->ranking }}
                </strong>
                untuk periode <strong>{{ $periodeLabel }}</strong>
            </p>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Perangkingan Karyawan</p>
        <p>Tanggal Generate: {{ $hasil->tanggal_generate->format('d F Y, H:i') }} WIB</p>
        <p style="margin-top: 5px; font-style: italic;">Confidential - Untuk Penggunaan Internal Only</p>
    </div>
</body>

</html>

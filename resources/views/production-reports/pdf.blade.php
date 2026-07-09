<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: garuda, sans-serif; font-size: 8px; color: #0f172a; }
        h1 { margin: 0; text-align: center; font-size: 15px; }
        .meta { margin-top: 4px; text-align: center; color: #475569; }
        .production-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .production-table th, .production-table td { border: 1px solid #94a3b8; padding: 3px; vertical-align: middle; }
        .production-table th { background: #eaf1dc; font-weight: bold; text-align: center; }
        .production-table .total-row td { background: #eaf1dc; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .nowrap { white-space: nowrap; }
        .section { margin-top: 10px; width: 48%; float: left; margin-right: 2%; }
        .section table { width: 100%; border-collapse: collapse; }
        .section th, .section td { border: 1px solid #94a3b8; padding: 4px; }
        .section th { background: #f1f5f9; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="meta">วันที่ออกรายงาน {{ thai_date($generatedAt) }} เวลา {{ $generatedAt->format('H:i') }} น.</div>
    @include('production-reports._table')

    <div class="section">
        <table>
            <thead><tr><th colspan="4">เปรียบเทียบกับมาตรฐาน</th></tr></thead>
            <tbody>
                @foreach ($standards as $standard)
                    <tr>
                        <td>{{ $standard['label'] }}</td>
                        <td class="text-right">{{ number_format($standard['actual'], 2) }}</td>
                        <td class="text-right">{{ number_format($standard['std'], 2) }}</td>
                        <td class="text-right">{{ number_format($standard['percent'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <table>
            <thead><tr><th>รายการ</th><th>บาท</th><th>บาท/ตัว</th><th>Note</th></tr></thead>
            <tbody>
                @foreach ($financeRows as $financeRow)
                    <tr>
                        <td>{{ $financeRow['label'] }}</td>
                        <td class="text-right">{{ number_format($financeRow['amount'], 2) }}</td>
                        <td class="text-right">{{ number_format($financeRow['per_bird'], 2) }}</td>
                        <td>{{ $financeRow['note'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if ($adjustment?->note)
        <div style="clear: both; padding-top: 10px;">Note: {{ $adjustment->note }}</div>
    @endif
</body>
</html>

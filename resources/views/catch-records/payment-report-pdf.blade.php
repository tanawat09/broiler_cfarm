<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: garuda, sans-serif;
            font-size: 10px;
            color: #0f172a;
        }
        h1 {
            margin: 0;
            font-size: 18px;
            line-height: 1.2;
            text-align: center;
        }
        h2 {
            margin: 16px 0 0;
            font-size: 13px;
            line-height: 1.2;
        }
        .meta {
            margin-top: 6px;
            text-align: center;
            color: #475569;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
            vertical-align: middle;
        }
        th {
            background: #e2e8f0;
            font-weight: bold;
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary th,
        .summary td {
            background: #f8fafc;
            font-weight: bold;
        }
        .note {
            margin-top: 10px;
            color: #64748b;
            font-size: 9px;
        }
        .signature-wrap {
            margin-top: 32px;
            width: 100%;
        }
        .signature {
            width: 32%;
            float: left;
            text-align: center;
            font-size: 11px;
        }
        .line {
            margin: 34px 18px 4px;
            border-top: 1px solid #334155;
        }
        .small-table {
            font-size: 8.5px;
        }
        .nowrap {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <h1>รายงานสรุปค่าจับไก่</h1>
    <div class="meta">
        ฟาร์ม {{ $flock->farm->farm_name ?? '-' }} / รุ่น {{ $flock->flock_code }}
        <br>
        วันที่ออกรายงาน {{ thai_date($generatedAt) }} เวลา {{ $generatedAt->format('H:i') }} น.
    </div>

    <h2>สรุปการจ่ายเงินรายทีม</h2>
    <table class="small-table">
        <thead>
            <tr>
                <th style="width: 24px;">ลำดับ</th>
                <th>ทีมจับไก่</th>
                <th style="width: 38px;">คัน</th>
                <th style="width: 52px;">ตัว</th>
                <th style="width: 46px;">กล่อง</th>
                <th style="width: 54px;">ค่าจับ</th>
                <th style="width: 54px;">น้ำมัน</th>
                <th style="width: 54px;">รถยก</th>
                <th style="width: 58px;">ก่อนหัก</th>
                <th style="width: 54px;">หัก 3%</th>
                <th style="width: 58px;">สุทธิ</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($teamPaymentRows as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['catching_team'] }}</td>
                    <td class="text-right">{{ number_format($row['total_trips']) }}</td>
                    <td class="text-right">{{ number_format($row['total_birds']) }}</td>
                    <td class="text-right">{{ number_format($row['total_boxes']) }}</td>
                    <td class="text-right">{{ number_format($row['total_fee'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['fuel_cost'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['forklift_cost'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['payment_total'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['withholding_amount'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['net_payment'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">ยังไม่มีข้อมูลทีมจับไก่ในรุ่นนี้</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="summary">
                <td colspan="2" class="text-center">รวม</td>
                <td class="text-right">{{ number_format($teamPaymentRows->sum('total_trips')) }}</td>
                <td class="text-right">{{ number_format($teamPaymentRows->sum('total_birds')) }}</td>
                <td class="text-right">{{ number_format($teamPaymentRows->sum('total_boxes')) }}</td>
                <td class="text-right">{{ number_format($teamPaymentSummary['total_fee'], 2) }}</td>
                <td class="text-right">{{ number_format($teamPaymentSummary['total_fuel_cost'], 2) }}</td>
                <td class="text-right">{{ number_format($teamPaymentSummary['total_forklift_cost'], 2) }}</td>
                <td class="text-right">{{ number_format($teamPaymentSummary['payment_total'], 2) }}</td>
                <td class="text-right">{{ number_format($teamPaymentSummary['withholding_amount'], 2) }}</td>
                <td class="text-right">{{ number_format($teamPaymentSummary['net_payment'], 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="note">
        หมายเหตุ: หัก ณ ที่จ่าย 3% คำนวณจากยอดรวมก่อนหัก (ค่าจับ + ค่าน้ำมัน + ค่ารถยก)
    </div>

    <h2>รายละเอียดรายคัน</h2>
    <table class="small-table">
        <thead>
            <tr>
                <th style="width: 30px;">คันที่</th>
                <th style="width: 58px;">วันที่จับ</th>
                <th style="width: 38px;">เล้า</th>
                <th style="width: 72px;">ทะเบียน</th>
                <th style="width: 50px;">ตัว</th>
                <th style="width: 44px;">กล่อง</th>
                <th style="width: 64px;">ชนิดรถ</th>
                <th>ทีมจับไก่</th>
                <th style="width: 58px;">ค่าจับ</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($catchRecords as $record)
                <tr>
                    <td class="text-center nowrap">{{ $record->sequence }}</td>
                    <td class="text-center nowrap">{{ thai_date($record->catch_date) }}</td>
                    <td class="text-center nowrap">{{ $record->house?->house_no ?? '-' }}</td>
                    <td class="nowrap">{{ $record->license_plate }}</td>
                    <td class="text-right nowrap">{{ number_format($record->birds_count) }}</td>
                    <td class="text-right nowrap">{{ number_format($record->boxes_count) }}</td>
                    <td>{{ $record->vehicle_type ?: '-' }}</td>
                    <td>{{ $record->catching_team ?: '-' }}</td>
                    <td class="text-right nowrap">{{ number_format((float) $record->catching_fee, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">ยังไม่มีรายละเอียดรายคัน</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="summary">
                <td colspan="4" class="text-center">รวม</td>
                <td class="text-right">{{ number_format($catchRecords->sum('birds_count')) }}</td>
                <td class="text-right">{{ number_format($catchRecords->sum('boxes_count')) }}</td>
                <td colspan="2"></td>
                <td class="text-right">{{ number_format((float) $catchRecords->sum('catching_fee'), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-wrap">
        <div class="signature">
            <div class="line"></div>
            ผู้จัดทำ
        </div>
        <div class="signature">
            <div class="line"></div>
            ผู้ตรวจสอบ
        </div>
        <div class="signature">
            <div class="line"></div>
            ผู้อนุมัติ
        </div>
    </div>
</body>
</html>

<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: garuda, sans-serif;
            font-size: 9.5px;
            color: #172033;
        }

        .header {
            border-bottom: 3px solid #0f766e;
            padding-bottom: 8px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            line-height: 1.15;
            color: #0f172a;
            text-align: center;
        }

        .subtitle {
            margin-top: 5px;
            text-align: center;
            font-size: 10.5px;
            color: #475569;
        }

        .summary-strip {
            width: 100%;
            margin-top: 10px;
            border-collapse: separate;
            border-spacing: 5px 0;
        }

        .summary-card {
            border: 1px solid #99f6e4;
            background: #ecfdf5;
            padding: 7px 8px;
            text-align: center;
        }

        .summary-card .label {
            font-size: 8.5px;
            color: #0f766e;
            font-weight: bold;
        }

        .summary-card .value {
            margin-top: 3px;
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }

        .section-title {
            margin: 14px 0 6px;
            padding: 5px 8px;
            background: #0f766e;
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
        }

        .section-title.light {
            background: #334155;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
            vertical-align: middle;
        }

        th {
            background: #e2e8f0;
            color: #0f172a;
            font-weight: bold;
            text-align: center;
        }

        tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        tfoot td,
        .summary-row td {
            background: #dff7ef;
            color: #0f172a;
            font-weight: bold;
        }

        .money {
            color: #047857;
            font-weight: bold;
        }

        .deduct {
            color: #b91c1c;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .nowrap {
            white-space: nowrap;
        }

        .small-table {
            font-size: 8.3px;
        }

        .house-table {
            font-size: 8.8px;
        }

        .note {
            margin-top: 8px;
            padding: 6px 8px;
            border: 1px solid #fde68a;
            background: #fffbeb;
            color: #92400e;
            font-size: 8.7px;
        }

        .page-break {
            page-break-before: always;
        }

        .signature-wrap {
            margin-top: 28px;
            width: 100%;
        }

        .signature {
            width: 32%;
            float: left;
            text-align: center;
            font-size: 10px;
            color: #334155;
        }

        .line {
            margin: 32px 18px 4px;
            border-top: 1px solid #334155;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">รายงานสรุปจ่ายเงินค่าจับไก่</div>
        <div class="subtitle">
            ฟาร์ม {{ $flock->farm->farm_name ?? '-' }} / รุ่น {{ $flock->flock_code }}
            <br>
            ออกรายงาน {{ thai_date($generatedAt) }} เวลา {{ $generatedAt->format('H:i') }} น.
        </div>
    </div>

    <table class="summary-strip">
        <tr>
            <td class="summary-card">
                <div class="label">เที่ยวรถทั้งหมด</div>
                <div class="value">{{ number_format($teamPaymentRows->sum('total_trips')) }}</div>
            </td>
            <td class="summary-card">
                <div class="label">ค่าจับรวม</div>
                <div class="value">{{ number_format($teamPaymentSummary['total_fee'], 2) }}</div>
            </td>
            <td class="summary-card">
                <div class="label">ยอดก่อนหัก</div>
                <div class="value">{{ number_format($teamPaymentSummary['payment_total'], 2) }}</div>
            </td>
            <td class="summary-card">
                <div class="label">หัก ณ ที่จ่าย 3%</div>
                <div class="value">{{ number_format($teamPaymentSummary['withholding_amount'], 2) }}</div>
            </td>
            <td class="summary-card">
                <div class="label">สุทธิจ่าย</div>
                <div class="value">{{ number_format($teamPaymentSummary['net_payment'], 2) }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">สรุปการจ่ายเงินรายทีม</div>
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
                    <td class="text-right money">{{ number_format($row['total_fee'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['fuel_cost'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['forklift_cost'], 2) }}</td>
                    <td class="text-right money">{{ number_format($row['payment_total'], 2) }}</td>
                    <td class="text-right deduct">{{ number_format($row['withholding_amount'], 2) }}</td>
                    <td class="text-right money">{{ number_format($row['net_payment'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">ยังไม่มีข้อมูลทีมจับไก่ในรุ่นนี้</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
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

    <div class="section-title light">สรุปรายเล้า</div>
    <table class="house-table">
        <thead>
            <tr>
                <th style="width: 30px;">ลำดับ</th>
                <th>เล้า</th>
                <th style="width: 52px;">คัน</th>
                <th style="width: 72px;">ตัว</th>
                <th style="width: 60px;">กล่อง</th>
                <th style="width: 78px;">ค่าจับ</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($housePaymentRows as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center nowrap">เล้า {{ $row['house_no'] }}</td>
                    <td class="text-right">{{ number_format($row['total_trips']) }}</td>
                    <td class="text-right">{{ number_format($row['total_birds']) }}</td>
                    <td class="text-right">{{ number_format($row['total_boxes']) }}</td>
                    <td class="text-right money">{{ number_format($row['total_fee'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">ยังไม่มีข้อมูลสรุปรายเล้า</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-center">รวม</td>
                <td class="text-right">{{ number_format($housePaymentSummary['total_trips']) }}</td>
                <td class="text-right">{{ number_format($housePaymentSummary['total_birds']) }}</td>
                <td class="text-right">{{ number_format($housePaymentSummary['total_boxes']) }}</td>
                <td class="text-right">{{ number_format($housePaymentSummary['total_fee'], 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="page-break"></div>

    <div class="section-title">รายละเอียดรายคัน (เรียงตามคันที่/ลำดับ)</div>
    <table class="small-table">
        <thead>
            <tr>
                <th style="width: 30px;">คันที่</th>
                <th style="width: 58px;">วันที่จับ</th>
                <th style="width: 36px;">เล้า</th>
                <th style="width: 70px;">ทะเบียน</th>
                <th style="width: 48px;">ตัว</th>
                <th style="width: 42px;">กล่อง</th>
                <th style="width: 62px;">ชนิดรถ</th>
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
                    <td class="text-right nowrap money">{{ number_format((float) $record->catching_fee, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">ยังไม่มีรายละเอียดรายคัน</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
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

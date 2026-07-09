@php
    $fmt = fn ($value, $dec = 0) => is_numeric($value) ? number_format((float) $value, $dec) : '-';
@endphp

<table class="production-table">
    <thead>
        <tr>
            <th>เล้า</th>
            <th>วันลงไก่</th>
            <th>วันจับไก่</th>
            <th>อายุจับ</th>
            <th>เพศ</th>
            <th>เกรด</th>
            <th>พันธุ์</th>
            <th>แหล่งลูกไก่</th>
            <th>จน.ไก่ลง+แถม</th>
            <th>ไก่จับ</th>
            <th>นน.รวม</th>
            <th>นน.เฉลี่ย</th>
            <th>%รอด</th>
            <th>อาหารใช้</th>
            <th>FCR</th>
            <th>PI</th>
            <th>นน./พื้นที่</th>
            <th>ตายขนส่ง</th>
            <th>%ตายขนส่ง</th>
            <th>%ตกกราว</th>
            <th>%อุ้งตีน</th>
            <th>พรีเมียมอุ้งตีน</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                <td class="text-center">{{ $row['house_no'] }}</td>
                <td class="text-center nowrap">{{ $row['placement_date_text'] }}</td>
                <td class="text-center nowrap">{{ $row['catch_date_text'] }}</td>
                <td class="text-right">{{ $fmt($row['age'], 0) }}</td>
                <td class="text-center">{{ $row['sex'] }}</td>
                <td class="text-center">{{ $row['grade'] }}</td>
                <td class="text-center">{{ $row['breed'] }}</td>
                <td>{{ $row['chick_source'] }}</td>
                <td class="text-right">{{ $fmt($row['initial_birds'], 0) }}</td>
                <td class="text-right">{{ $fmt($row['net_birds'], 0) }}</td>
                <td class="text-right">{{ $fmt($row['total_weight'], 2) }}</td>
                <td class="text-right">{{ $fmt($row['avg_weight'], 2) }}</td>
                <td class="text-right">{{ $fmt($row['survival_rate'], 2) }}</td>
                <td class="text-right">{{ $fmt($row['feed_used'], 0) }}</td>
                <td class="text-right">{{ $fmt($row['fcr'], 3) }}</td>
                <td class="text-right">{{ $fmt($row['pi'], 0) }}</td>
                <td class="text-right">{{ $fmt($row['weight_per_area'], 2) }}</td>
                <td class="text-right">{{ $fmt($row['doa_birds'], 0) }}</td>
                <td class="text-right">{{ $fmt($row['doa_rate'], 2) }}</td>
                <td class="text-right">{{ $fmt($row['condemned_rate'], 2) }}</td>
                <td class="text-right">{{ $fmt($row['problem_rate'], 2) }}</td>
                <td class="text-right">{{ $fmt($row['premium_base'], 0) }}</td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td class="text-center" colspan="3">รวม</td>
            <td class="text-right">{{ $fmt($totals['age'], 2) }}</td>
            <td colspan="4"></td>
            <td class="text-right">{{ $fmt($totals['initial_birds'], 0) }}</td>
            <td class="text-right">{{ $fmt($totals['net_birds'], 0) }}</td>
            <td class="text-right">{{ $fmt($totals['total_weight'], 2) }}</td>
            <td class="text-right">{{ $fmt($totals['avg_weight'], 2) }}</td>
            <td class="text-right">{{ $fmt($totals['survival_rate'], 2) }}</td>
            <td class="text-right">{{ $fmt($totals['feed_used'], 0) }}</td>
            <td class="text-right">{{ $fmt($totals['fcr'], 3) }}</td>
            <td class="text-right">{{ $fmt($totals['pi'], 0) }}</td>
            <td class="text-right">{{ $fmt($totals['weight_per_area'], 2) }}</td>
            <td class="text-right">{{ $fmt($totals['doa_birds'], 0) }}</td>
            <td class="text-right">{{ $fmt($totals['doa_rate'], 2) }}</td>
            <td class="text-right">{{ $fmt($totals['condemned_rate'], 2) }}</td>
            <td class="text-right">{{ $fmt($totals['problem_rate'], 2) }}</td>
            <td class="text-right">{{ $fmt($totals['premium_base'], 0) }}</td>
        </tr>
    </tbody>
</table>

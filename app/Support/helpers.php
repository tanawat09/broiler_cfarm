<?php

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

if (! function_exists('thai_date')) {
    function thai_date(CarbonInterface|string|null $date): string
    {
        if ($date === null || $date === '') {
            return '-';
        }

        $carbon = $date instanceof CarbonInterface ? $date : Carbon::parse($date);

        return $carbon->format('d/m/').($carbon->year + 543);
    }
}

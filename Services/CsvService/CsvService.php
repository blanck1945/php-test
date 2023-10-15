<?php

namespace Services\CsvService;

class CsvService
{
    public function total($csv)
    {
        $totals = [
            'total' => 0,
            'total_positive' => 0,
            'total_negative' => 0
        ];

        foreach ($csv as $value) {
            $amount = explode('$', $value[3]);
            $sign = $amount[0];
            $num = $amount[1];
            $num_formatted = (float) str_replace(',', '', $num);

            if ($sign === '-') {
                $amount = explode('$', $value[3])[1];
                $num_formatted = -1 * abs($num_formatted);
            }

            $totals['total'] += $num_formatted;

            if ($num_formatted > 0) {
                $totals['total_positive'] += $num_formatted;
            } else {
                $totals['total_negative'] += $num_formatted;
            }
        }
        return $totals;
    }
}

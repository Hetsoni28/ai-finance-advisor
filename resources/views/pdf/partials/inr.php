<?php
// Indian number format for PDFs: ₹1,23,45,678.00
return function ($n, int $dec = 2): string {
    $s = number_format(abs((float) $n), $dec, '.', '');
    [$int, $frac] = array_pad(explode('.', $s), 2, '');
    if (strlen($int) > 3) {
        $int = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', substr($int, 0, -3)) . ',' . substr($int, -3);
    }
    return ((float) $n < 0 ? '-' : '') . '₹' . $int . ($dec ? '.' . $frac : '');
};

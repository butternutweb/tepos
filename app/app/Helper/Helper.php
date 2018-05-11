<?php

function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}
function money_short($number){
    if ($number < 1000) {
        return number_format($number);
    } elseif ($number < 1000000) {
        return number_format($number / 1000) . 'K';
    } else if ($number < 1000000000) {
        return number_format($number / 1000000) . 'M';
    } else {
        return number_format($number / 1000000000) . 'B';
    };
}
<?php
// app/Helpers/helpers.php
if (!function_exists('str_limit_words')) {
    function str_limit_words($string, $words = 100, $end = '...')
    {
        $wordArr = explode(' ', $string);
        if (count($wordArr) <= $words) {
            return $string;
        }
        return implode(' ', array_slice($wordArr, 0, $words)) . $end;
    }
}

if (!function_exists('formatDate')) {
    /**
     * Format a given date to 'd F Y' format.
     *
     * @param  string|\DateTime $date
     * @return string
     */
    function formatDate($date)
    {
        return \Carbon\Carbon::parse($date)->format('d F Y');
    }
}

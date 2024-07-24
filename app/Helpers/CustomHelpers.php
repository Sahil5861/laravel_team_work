<?php

namespace App\Helpers;

use Illuminate\Support\Facades\URL;

class CustomHelpers
{
    public static function statusToggle($url, $id, $token, $currentText)
    {
        $newText = $currentText === 'Active' ? 'Inactive' : 'Active';
        $newClass = $newText === 'Active' ? 'btn-success' : 'btn-danger';

        return [
            'url' => URL::to($url . '/' . $id),
            'data' => [
                '_token' => $token,
            ],
            'newText' => $newText,
            'newClass' => $newClass,
        ];
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
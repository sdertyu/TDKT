<?php

if (!function_exists('formatDate')) {
    function formatDate($ngay)
    {
        return \Carbon\Carbon::parse($ngay)->format('H:i:s d/m/Y');
    }
}
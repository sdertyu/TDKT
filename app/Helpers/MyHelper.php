<?php

use Carbon\Carbon;

if (!function_exists('formatDate')) {
    function formatDate($ngay)
    {
        return \Carbon\Carbon::parse($ngay)->format('H:i:s d/m/Y');
    }
}

if (!function_exists('getDateNow')) {
    function getDateNow()
    {
        $carbon = Carbon::now();  // Tạo đối tượng Carbon với thời gian hiện tại
        $carbon->setTimezone('Asia/Ho_Chi_Minh');  // Đặt múi giờ là múi giờ Việt Nam
        return $carbon->format('Y-m-d H:i:s');
    }
}

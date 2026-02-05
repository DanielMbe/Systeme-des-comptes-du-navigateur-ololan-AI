<?php
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $ololanBrowser = "Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Ololan/3.1.673.33 Chrome/118.0.5993.220 Safari/537.36";
    if ($ololanBrowser !== $userAgent) {
        header("Location: https://www.ololan.com");
    }
    exit();
?>
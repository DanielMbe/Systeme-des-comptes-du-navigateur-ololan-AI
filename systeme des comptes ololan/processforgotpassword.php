<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$ololanBrowser = "Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Ololan/3.1.673.33 Chrome/118.0.5993.220 Safari/537.36";
if ($ololanBrowser !== $userAgent) {
    header("Location: https://www.ololan.com");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $verificationcode = isset($_POST['verificationcode']) ? $_POST['verificationcode'] : '';

    //parsing data to prevent XSS cross scripting
    $verificationcode = filter_var($verificationcode, FILTER_SANITIZE_STRING);

    if (empty($verificationcode)) {
        header("Location: https://accounts.ololan.com/forgotpasswordverification?signuperror=Emptyfields");
        exit();
    }

    session_start();
    $securityCode = $_SESSION['securityCode'];
    $sessiontDateTime = $_SESSION['sessionDateTime'];
    $currentDateTime = date('Y-m-d H:i:s');
    $datetime1 = new DateTime($sessiontDateTime);
    $datetime2 = new DateTime($currentDateTime);

    //calculate the difference between the two datetime objects in minutes
    $interval = $datetime1->diff($datetime2);
    $minutesDifference = $interval->i;

    //compare the difference to 5 minutes
    if ($minutesDifference >= 5) {
        session_destroy();
        header("Location: https://accounts.ololan.com/forgotpasswordemail?signuperror=Your%20session%20has%20ended%20sorry");
        exit();
    }

    if ($verificationcode == $securityCode) {
        header("Location: https://accounts.ololan.com/resetpassword");
        exit();
    } else {
        header("Location: https://accounts.ololan.com/forgotpasswordverification?signuperror=Validation%20code%20is%20incorrect");
        exit();
    }
} else {
    header("Location: https://accounts.ololan.com");
    exit();
}
?>
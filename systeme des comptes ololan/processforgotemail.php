<?php
    use PHPMailer\PHPMailer\PHPMailer;

    require 'PHPMailer-6.8.0/src/PHPMailer.php';
    require 'PHPMailer-6.8.0/src/SMTP.php';
    require 'PHPMailer-6.8.0/src/Exception.php';

    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $ololanBrowser = "Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Ololan/3.1.673.33 Chrome/118.0.5993.220 Safari/537.36";
    if ($ololanBrowser !== $userAgent) {
        header("Location: https://www.ololan.com");
        exit();
    }

$currentDateTime = date('Y-m-d H:i:s'); //get the current date and time in the default format (Y-m-d H:i:s)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $forgottenemail = isset($_POST['forgottenemail']) ? $_POST['forgottenemail'] : '';

    //validate and sanitize data to prevent XSS cross scripting
    $forgottenemail = filter_var($forgottenemail, FILTER_VALIDATE_EMAIL);

    if (empty($forgottenemail)) {
        header("Location: https://accounts.ololan.com/forgotpasswordemail?signuperror=Emptyfields");
        exit();
    }

    if (!isset($_SESSION['user_id_forgottenemail'])) {
        session_start();
        $_SESSION['user_id_forgottenemail'] = mb_strlen($forgottenemail) . mb_strlen($forgottenemail);
        $_SESSION['forgottenemail'] = $forgottenemail;
    }

    $min = 10000; // Minimum 5-digit number
    $max = 99999; // Maximum 5-digit number
    $securityCode = random_int($min, $max);
    $_SESSION['securityCode'] = $securityCode;
    $_SESSION['sessionDateTime'] = $currentDateTime;

    $db_host = "localhost";
    $db_name = "u850448322_olauthservice";
    $db_user = "u850448322_ololanadmin";
    $db_pass = "IadLAnoloNmXyZ22@MariaBd";

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("SELECT firstname, lastname FROM ololanaccountusers WHERE email = :email");
        $stmt->bindParam(':email', $forgottenemail);
        $stmt->execute();

        // check if the user exists
        if ($stmt->rowCount() === 1) {
            $row = $stmt->fetch();
            $firstname = $row["firstname"];
            $lastname = $row["lastname"];
            $credentialemail = "accounts@ololan.com";
            $credentialPassword = "StNuoCcA20@oloLAn24";
            
            $mail = new PHPMailer();
            $mail->SMTPDebug = 0; //enable SMTP debugging (0 = no debug output)
            $mail->isSMTP();
            $mail->Host = 'smtp.titan.email';//host from mail server
            $mail->SMTPAuth = true;
            $mail->Username = $credentialemail;
            $mail->Password = $credentialPassword;
            $mail->SMTPSecure = 'ssl'; //set the encryption type (TLS or SSL, depending on your server)
            $mail->Port = 465; //set the SMTP port (TLS: 587, SSL: 465)
            $mail->setFrom($credentialemail, 'Ololan Accounts Team');
            $mail->addAddress($forgottenemail, $firstname . " " . $lastname); //add a recipient email address and name
            $mail->Subject = 'Reset Password';
            $mail->Body = 'Hi ' . $firstname . ', This is your password resetting code : ' . $securityCode . '. This code is availble only for 5 minutes.';

            if ($mail->send()) {
                header("Location: https://accounts.ololan.com/forgotpasswordverification");
                $pdo = null;
                exit();
            } else {
                header("Location: https://accounts.ololan.com/forgotpasswordemail?signuperror=Server%20error%20try%20again%20later");
                session_destroy();
                $pdo = null;
                exit();
            }
        } else {
            //redirect to the sign up page with error string
            header("Location: https://accounts.ololan.com/forgotpasswordemail?signuperror=Account%20does%20not%20exist");
            session_destroy();
            $pdo = null;
            exit();
        }
    } catch (PDOException $exception) {
        header("Location: https://accounts.ololan.com/forgotpasswordemail?signuperror=Server%20error%20try%20again%20later");
        session_destroy();
        $pdo = null;
        exit();
    }
} else {
    header("Location: https://accounts.ololan.com");
    exit();
}
?>
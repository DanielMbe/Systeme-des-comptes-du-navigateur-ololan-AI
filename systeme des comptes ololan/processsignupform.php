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

    $currentDateTime = date('Y-m-d H:i:s');//get the current date and time in the default format (Y-m-d H:i:s)

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $firstname = isset($_POST['firstname']) ? $_POST['firstname'] : '';
        $lastname = isset($_POST['lastname']) ? $_POST['lastname'] : '';
        $birthdate = isset($_POST['birthdate']) ? $_POST['birthdate'] : '';
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $firstpasswordset = isset($_POST['firstpasswordset']) ? trim($_POST['firstpasswordset']) : '';
        $secondpasswordset = isset($_POST['secondpasswordset']) ? $_POST['secondpasswordset'] : '';

        //validate and sanitize data to prevent XSS cross scripting
        $firstname = filter_var($firstname, FILTER_SANITIZE_STRING);
        $lastname = filter_var($lastname, FILTER_SANITIZE_STRING);
        $birthdate = filter_var($birthdate, FILTER_SANITIZE_STRING);
        $username = filter_var($username, FILTER_VALIDATE_EMAIL);
        $firstpasswordset = filter_var($firstpasswordset, FILTER_SANITIZE_STRING);
        $secondpasswordset = filter_var($secondpasswordset, FILTER_SANITIZE_STRING);

        if (empty($firstname) || empty($lastname) || empty($birthdate) || empty($username) || empty($firstpasswordset) || empty($secondpasswordset)) {
            header("Location: https://accounts.ololan.com/signup?signuperror=emptyfields");
            exit();
        } else {
            $regex = "/[^A-Za-z0-9]/";
            if (preg_match($regex, $firstname) || preg_match($regex, $lastname)) {
                header("Location: https://accounts.ololan.com/signup?signuperror=specials%20characters%20not%20allowed");
                exit();
            }
        }

        if ($firstpasswordset === $secondpasswordset) {
            if (!isset($_SESSION['user_id_signup']))
            {
                session_start();
                $_SESSION['user_id_signup'] = mb_strlen($username) . mb_strlen($firstpasswordset);
                $_SESSION['firstname'] = $firstname;
                $_SESSION['lastname'] = $lastname;
                $_SESSION['birthdate'] = $birthdate;
                $_SESSION['username'] = $username;
                $_SESSION['firstpasswordset'] = $firstpasswordset;
                $_SESSION['secondpasswordset'] = $secondpasswordset;
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
                $stmt = $pdo->prepare("SELECT * FROM ololanaccountusers WHERE email = :email");
                $stmt->bindParam(':email', $username);
                $stmt->execute();

                if ($stmt->rowCount() === 1) {
                    $pdo = null;
                    session_destroy();
                    header("Location: https://accounts.ololan.com/signup?signuperror=Email%20already%20used");
                    exit();
                }

                $credentialemail = "accounts@ololan.com";
                $credentialPassword = "StNuoCcA20@oloLAn24";
                $mail = new PHPMailer(true);
                $mail->SMTPDebug = 0;//enable SMTP debugging (0 = no debug output)
                $mail->isSMTP();
                $mail->Host = 'smtp.titan.email';
                $mail->SMTPAuth = true;
                $mail->Username = $credentialemail;
                $mail->Password = $credentialPassword;
                $mail->SMTPSecure = 'ssl';//set the encryption type (TLS or SSL, depending on your server)
                $mail->Port = 465;//set the SMTP port (TLS: 587, SSL: 465)
                $mail->setFrom($credentialemail, 'Ololan Accounts Team');
                $mail->addAddress($username, $firstname . " " . $lastname); //add a recipient email address and name
                $mail->Subject = 'Account Validation';
                $mail->Body = 'Hi ' . $firstname . ', This is your account validation code : ' . $securityCode . '. This code is availble only for 5 minutes.';
                
                if ($mail->send()) {
                    header("Location: https://accounts.ololan.com/accountvalidation");
                    $pdo = null;
                    exit();
                } else {
                    header("Location: https://accounts.ololan.com/signup?signuperror=MailServer%20error%20try%20again%20later");
                    session_destroy();
                    $pdo = null;
                    exit();
                }
            } catch (PDOException $exception) {
                header("Location: https://accounts.ololan.com/signup?signuperror=Exception501Server%20error%20try%20again%20later");
                session_destroy();
                $pdo = null;
                exit();
            }
        } else {
            header("Location: https://accounts.ololan.com/signup?signuperror=Passwords%20does%20not%20match");
            session_destroy();
            exit();
        }
    } else {
        header("Location: https://accounts.ololan.com");
        exit();
    }
?>
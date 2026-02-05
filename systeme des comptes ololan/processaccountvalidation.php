<?php
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $ololanBrowser = "Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Ololan/3.1.673.33 Chrome/118.0.5993.220 Safari/537.36";
    if ($ololanBrowser !== $userAgent) {
        header("Location: https://www.ololan.com");
        exit();
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $verificationcode = isset($_POST['verificationcode']) ? $_POST['verificationcode'] : '';

        //validate and sanitize data to prevent XSS cross scripting
        $verificationcode = filter_var($verificationcode, FILTER_SANITIZE_STRING);
        if (empty($verificationcode)) {
            header("Location: https://accounts.ololan.com/accountvalidation?signuperror=Emptyfields");
            exit();
        }

        session_start();
        $firstname = $_SESSION['firstname'];
        $lastname = $_SESSION['lastname'];
        $birthdate = $_SESSION['birthdate'];
        $username = $_SESSION['username'];
        $firstpasswordset = $_SESSION['firstpasswordset'];
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
            header("Location: https://accounts.ololan.com/signup?signuperror=Your%20session%20has%20ended");
            exit();
        }

        if ($verificationcode == $securityCode) {
            $db_host = "localhost";
            $db_name = "u850448322_olauthservice";
            $db_user = "u850448322_ololanadmin";
            $db_pass = "IadLAnoloNmXyZ22@MariaBd";

            try {
                $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $estatus = "logged out";
                $lastconnection = "none";

                //use prepared statements to prevent SQL injection
                $stmt = $pdo->prepare("INSERT INTO ololanaccountusers (firstname, lastname, birthday, email, epassword, estatus, lastconnection) VALUES (:firstname, :lastname, :birthday, :email, :epassword, :estatus, :lastconnection)");
                $stmt->bindParam(':firstname', $firstname);
                $stmt->bindParam(':lastname', $lastname);
                $stmt->bindParam(':birthday', $birthdate);
                $stmt->bindParam(':email', $username);
                $stmt->bindParam(':epassword', $firstpasswordset);
                $stmt->bindParam(':estatus', $estatus);
                $stmt->bindParam(':lastconnection', $lastconnection);

                if ($stmt->execute()) {
                    header("Location: https://accounts.ololan.com/signupsuccessful");
                    session_destroy();
                    $pdo = null;
                    exit();
                } else {
                    header("Location: https://accounts.ololan.com/signup?signuperror=Server%20error%20try%20again%20later");
                    session_destroy();
                    $pdo = null;
                    exit();
                }
            } catch (PDOException $exception) {
                header("Location: https://accounts.ololan.com/signup?signuperror=Server%20error%20try%20again%20later");
                session_destroy();
                $pdo = null;
                exit();
            }
        } else {
            header("Location: https://accounts.ololan.com/accountvalidation?signuperror=Server%20error%20try%20again%20later");
            exit();
        }
    } else {
        header("Location: https://accounts.ololan.com");
        exit();
    }
?>
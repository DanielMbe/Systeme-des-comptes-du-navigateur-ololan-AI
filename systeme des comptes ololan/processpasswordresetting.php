<?php
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $ololanBrowser = "Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Ololan/3.1.673.33 Chrome/118.0.5993.220 Safari/537.36";
    if ($ololanBrowser !== $userAgent) {
        header("Location: https://www.ololan.com");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $firstpasswordset = isset($_POST['firstpasswordset']) ? trim($_POST['firstpasswordset']) : '';
        $secondpasswordset = isset($_POST['secondpasswordset']) ? $_POST['secondpasswordset'] : '';

        //parsing data to prevent XSS cross scripting
        $firstpasswordset = filter_var($firstpasswordset, FILTER_SANITIZE_STRING);
        $secondpasswordset = filter_var($secondpasswordset, FILTER_SANITIZE_STRING);

        if (empty($firstpasswordset) || empty($secondpasswordset)) {
            header("Location: https://accounts.ololan.com/resetpassword?signuperror=emptyfields");
            exit();
        }

        if ($firstpasswordset == $secondpasswordset) {
            $db_host = "localhost";
            $db_name = "u850448322_olauthservice";
            $db_user = "u850448322_ololanadmin";
            $db_pass = "IadLAnoloNmXyZ22@MariaBd";

            session_start();
            $username = $_SESSION['forgottenemail'];

            try {
                $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                //use prepared statements to prevent SQL injection
                $stmt = $pdo->prepare("UPDATE ololanaccountusers SET epassword = :epassword WHERE email = :email");
                $stmt->bindParam(':email', $username);
                $stmt->bindParam(':epassword', $firstpasswordset);

                if ($stmt->execute()) {
                    header("Location: https://accounts.ololan.com/passwordresetsuccessful");
                    session_destroy();
                    $pdo = null;
                    exit();
                } else {
                    header("Location: https://accounts.ololan.com/resetpassword?signuperror=Server%20error%20try%20again%20later");
                    $pdo = null;
                    exit();
                }
            } catch (PDOException $exception) {
                header("Location: https://accounts.ololan.com/resetpassword?signuperror=Server%20error%20try%20again%20late");
                $pdo = null;
                exit();
            }
        } else {
            header("Location: https://accounts.ololan.com/resetpassword?signuperror=Passwords%20do%20not%20match");
            exit();
        }
    } else {
        header("Location: https://accounts.ololan.com");
        exit();
    }
?>
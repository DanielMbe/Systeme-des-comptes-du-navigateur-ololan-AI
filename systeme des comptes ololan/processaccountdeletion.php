<?php
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $ololanBrowser = "Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Ololan/3.1.673.33 Chrome/118.0.5993.220 Safari/537.36";
    if ($ololanBrowser !== $userAgent) {
        header("Location: https://www.ololan.com");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        session_start();
        $username = $_SESSION['email'];
        $password = $_SESSION['password'];

        $db_host = "localhost";
        $db_name = "u850448322_olauthservice";
        $db_user = "u850448322_ololanadmin";
        $db_pass = "IadLAnoloNmXyZ22@MariaBd";

        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //use prepared statements to prevent SQL injection
            $stmtA = $pdo->prepare("SELECT * FROM ololanaccountusers WHERE email = :email AND epassword = :epassword");
            $stmtA->bindParam(':email', $username);
            $stmtA->bindParam(':epassword', $password);
            $stmtA->execute();

            session_destroy();
            // check if the user exists
            if ($stmtA->rowCount() === 1) {
                //process disconnection
                $stmt = $pdo->prepare("DELETE FROM ololanaccountusers WHERE email = :email AND epassword = :epassword");
                $stmt->bindParam(':email', $username);
                $stmt->bindParam(':epassword', $password);
                $stmt->execute();

                header("Location: https://accounts.ololan.com/loggedout");
                $pdo = null;
                exit();
            } else {
                //redirect to the sign in page with error string
                header("Location: https://accounts.ololan.com?loginerror=username%20or%20password%20incorrect");
                $pdo = null;
                exit();
            }
        } catch (PDOException $exception) {
            session_destroy();
            header("Location: https://accounts.ololan.com");
            $pdo = null;
            exit();
        }
    } else {
        header("Location: https://accounts.ololan.com");
        exit();
    }
?>

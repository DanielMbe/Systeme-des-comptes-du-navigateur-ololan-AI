<?php
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $ololanBrowser = "Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Ololan/3.1.673.33 Chrome/118.0.5993.220 Safari/537.36";
    if ($ololanBrowser !== $userAgent) {
        header("Location: https://www.ololan.com");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $userID = $_GET['userID'];
        $ipAddress = "0.0.0.0";

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ipAddress = trim(end($ipList));
        } else if (!empty($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }

        $ipAddress = filter_var($ipAddress, FILTER_VALIDATE_IP);
        $lastconnection = date('Y-m-d H:i:s');

        $db_host = "localhost";
        $db_name = "u850448322_olauthservice";
        $db_user = "u850448322_ololanadmin";
        $db_pass = "IadLAnoloNmXyZ22@MariaBd";

        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //use prepared statements to prevent SQL injection
            $stmtA = $pdo->prepare("SELECT * FROM ololanbrowserusers WHERE useridentifier = :useridentifier");
            $stmtA->bindParam(':useridentifier', $userID);
            $stmtA->execute();

            // check if the user exists
            if ($stmtA->rowCount() === 1) {
                //process update info
                $stmtB = $pdo->prepare("UPDATE ololanbrowserusers SET ipaddress = :ipaddress, lastconnection = :lastconnection WHERE useridentifier = :useridentifier");
                $stmtB->bindParam(':ipaddress', $ipAddress);
                $stmtB->bindParam(':lastconnection', $lastconnection);
                $stmtB->bindParam(':useridentifier', $userID);
                $stmtB->execute();
                $pdo = null;
                exit();
            } else {
                //process add new user
                $stmtC = $pdo->prepare("INSERT INTO ololanbrowserusers (ipaddress, lastconnection, useridentifier) VALUES (:ipaddress, :lastconnection, :useridentifier)");
                $stmtC->bindParam(':ipaddress', $ipAddress);
                $stmtC->bindParam(':lastconnection', $lastconnection);
                $stmtC->bindParam(':useridentifier', $userID);
                $stmtC->execute();
                $pdo = null;
                exit();
            }
        } catch (PDOException $exception) {
            header("Location: https://accounts.ololan.com");
            $pdo = null;
            exit();
        }
    } else {
        header("Location: https://accounts.ololan.com");
        exit();
    }
?>

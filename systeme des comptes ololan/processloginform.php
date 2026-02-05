<?php
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $ololanBrowser = "Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Ololan/3.1.673.33 Chrome/118.0.5993.220 Safari/537.36";
    if ($ololanBrowser !== $userAgent) {
        header("Location: https://www.ololan.com");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        //validate and sanitize data to prevent XSS cross scripting
        $username = filter_var($username, FILTER_VALIDATE_EMAIL);
        $password = filter_var($password, FILTER_SANITIZE_STRING);

        if (empty($username) || empty($password)) {
            header("Location: https://accounts.ololan.com?loginerror=Emptyfields");
            exit();
        }

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

            // check if the user exists
            if ($stmtA->rowCount() === 1) {
                //process synchronization
                $login = $username;
                $row = $stmtA->fetch();
                $username = html_entity_decode($row["firstname"]) . "%20". html_entity_decode($row["lastname"]);
                $estatus = "logged in";
                $lastconnection = date('Y-m-d H:i:s');

                $stmtB = $pdo->prepare("UPDATE ololanaccountusers SET estatus = :estatus, lastconnection = :lastconnection WHERE email = :email AND epassword = :epassword");
                $stmtB->bindParam(':estatus', $estatus);
                $stmtB->bindParam(':lastconnection', $lastconnection);
                $stmtB->bindParam(':email', $login);
                $stmtB->bindParam(':epassword', $password);
                $stmtB->execute();

                header("Location: https://accounts.ololan.com/closeololanscript?ulogin=$login&uname=$username&upassword=$password");
                $pdo = null;
                exit();
            } else {
                //redirect to the sign in page with error string
                header("Location: https://accounts.ololan.com?loginerror=Username%20or%20password%20incorrect");
                $pdo = null;
                exit();
            }
        } catch (PDOException $exception) {
            header("Location: https://accounts.ololan.com?loginerror=Server%20error");
            $pdo = null;
            exit();
        }
    } else {
        header("Location: https://accounts.ololan.com");
        exit();
    }
?>

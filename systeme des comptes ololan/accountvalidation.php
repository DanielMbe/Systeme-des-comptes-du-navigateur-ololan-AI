<?php
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $ololanBrowser = "Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Ololan/3.1.673.33 Chrome/118.0.5993.220 Safari/537.36";
    if ($ololanBrowser !== $userAgent) {
        header("Location: https://www.ololan.com");
        exit();
    }
    session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Ololan account - Verify your e-mail</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='style/signin.css'>
    <link rel="icon" href="image/ololanLogo.png">
    <script src='script/main.js'></script>
</head>

<body>
    <header>
        <img alt="logo ololan" src="image/ololanCompanyLogo.png" class="companyLogo">
    </header>
    <section>
        <div id="paperDoc">
            <form action="processaccountvalidation.php" method="post" class="accountValdForm">
                <div id="containerField">
                    <img alt="verification logo" src="image/accountVerification.png" id="verifyLogo">
                </div>
                <?php
                    if (isset($_GET['signuperror'])) {
                        $signupError = $_GET['signuperror'];
                        echo "<div style='color: red;'>$signupError</div>";
                    }
                ?>
                <div class="formField">
                    <div class="fieldLabel">
                        <span>
                            A verification code has been sent to
                            <?php
                                $email = "mailError";
                                if (isset($_SESSION['username'])) {
                                    $email = $_SESSION['username'];
                                }
                                echo "<strong>" . $email . "</strong>";
                            ?><br>
                            Enter the code in the form below to validate your Ololan account.
                        </span>
                    </div>
                    <div class="container">
                        <input type="text" name="verificationcode" class="inputField" placeholder="Enter verification code" required>
                    </div>
                </div>
                <div id="clickSpace" class="specialclick">
                    <div></div>
                    <button id="signinButton" type="submit">Verify</button>
                </div>
            </form>
        </div>
    </section>
    <footer>
        <img alt="logo ololan" src="image/ololanCompanyLogo.png" class="companyLogo">
        <div id="fMenuArea">
            <nav class="footerMenu">
                <div class="fiMenuTitle">Products</div>
                <div class="fiMenuContainer"><a class="fitemMenu" href="https://www.ololan.com/browser">Ololan Browser</a></div>
                <div class="fiMenuContainer"><a class="fitemMenu" href="https://www.ololan.com/eina">Ololan Eina</a></div>
                <div class="fiMenuContainer"><a class="fitemMenu" href="https://www.ololan.com/project">Services</a></div>
            </nav>
            <nav class="footerMenu">
                <div class="fiMenuTitle">Support</div>
                <div class="fiMenuContainer"><a class="fitemMenu" href="https://www.ololan.com/helps">Helps</a></div>
                <div class="fiMenuContainer"><a class="fitemMenu" href="https://www.ololan.com/report">Report issue</a></div>
            </nav>
            <nav class="footerMenu">
                <div class="fiMenuTitle">Company</div>
                <div class="fiMenuContainer"><a class="fitemMenu" href="https://www.ololan.com/knowus">About Ololan</a></div>
                <div class="fiMenuContainer"><a class="fitemMenu" href="https://www.ololan.com/careers">Careers</a></div>
                <div class="fiMenuContainer"><a class="fitemMenu" href="https://www.ololan.com/contactus">Contact us</a></div>
            </nav>
        </div>
        <div id="companyArea">
            <div id="copyRight">
                <span>© 2022 - 2024</span>
                <span>Ololan LLC All rights reserved</span>
            </div>
            <nav class="navMenu">
                <div class="iMenuContainer"><a class="itemMenu" href="https://legals.ololan.com">Privacy</a></div>
                <div class="iMenuContainer"><a class="itemMenu" href="https://legals.ololan.com">Terms of use</a></div>
                <div class="iMenuContainer"><a class="itemMenu" href="https://legals.ololan.com">EULA</a></div>
            </nav>
            <nav class="navMenu">
                <div class="iMenuContainer">
                    <a href="https://www.linkedin.com/company/ololan-llc/" target="_blank"><img alt="social network" src="image/linkedin.png" class="social"></a>
                </div>
                <div class="iMenuContainer">
                    <a href="https://www.instagram.com/ololancompany?utm_source=qr&igsh=MTBpOXhsZGlnbzNjcQ==" target="_blank"><img alt="social network" src="image/instagram.png" class="social"></a>
                </div>
                <div class="iMenuContainer">
                    <a href="https://x.com/OlolanLLC?t=Es3pz25k2QLb2nPdOEkmxg&s=09" target="_blank"><img alt="social network" src="image/twitter.png" class="social"></a>
                </div>
            </nav>
        </div>
    </footer>
</body>

</html>
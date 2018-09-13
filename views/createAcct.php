<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST){
    require_once '../database/data_layer.php';
    $dataLayer = new data_layer();

    if (count($_POST) == 6) {
        $dataLayer->createNewUser($_POST);
    }
}
 ?>

<!DOCTYPE html>
<html>
<head>
    <title>Rochester Riverside Convention Center</title>
    <meta charset='utf-8'/>
    <meta name='viewport' content='width=device-width, initial-scale = 1.0, minimum-scale = 1.0, maximum-scale = 5.0' />
    <link rel='stylesheet' type='text/css' media='screen' href='/style/css/createAcct.css'>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script type='text/javascript' src='/js/scripts.js'></script>
    <link href='../assets/fonts/fontawesome-free-5.2.0-web/css/all.min.css' rel='stylesheet'>
</head>

<body id='createAcctPage' class='backgroundImage'>
    <div class='container'>
        <!-- Landing Section -->
        <section id='create-form'>
            <!-- Header -->
            <div class='header'>
                <h1 class='title centered'>Create Account</h1>
                <h2 class='subtitle centered'>We just need a little information for the<br/>HR department to activate your account.</h2>
            </div>

            <!-- Send Password Form -->
            <form class='formContainer' action='createAcct.php' method='POST'>

                <!-- Create Screen 1. Outer div is for JS -->
                <div class="formStep" id="formStep1">
                    <div class='inputWithIcon'>
                        <input class='block' id='phoneNumber' type = 'text' placeholder= 'Phone Number' name='phoneNumber' required="required">
                        <i class='fas fa-phone' aria-hidden='true'></i>
                    </div>

                    <div class='inputWithIcon'>
                        <input onkeyup="confirmPassword()" class='block' id='password' type = 'password' placeholder= 'Password' name='password' required="required">
                        <i class='fas fa-key pwIcon' aria-hidden='true'></i>
                    </div>

                    <div class='inputWithIcon'>
                        <input onkeyup="confirmPassword()" class='block' id='passwordConfirm' type = 'text' placeholder= 'Confirm Password' name='passwordConfirm' required="required">
                        <i class='fas fa-lock pwIcon' aria-hidden='true'></i>
                    </div>
                    <input class='block submit centered' id='next' type = 'button' value= 'Continue' onclick="nextStep()"/>
                    <a id='login' href='../index.php'>Log In</a>
                </div>

                <!-- Create Screen 2 -->
                <div class="formStep" id="formStep2" style="display: none;">
                    <div class='inputWithIcon'>
                        <input class='block' id='fName' type = 'text' placeholder= 'First Name' name='fName' required="required" autofocus>
                        <i class='fas fa-address-card' aria-hidden='true'></i>
                    </div>
                    <div class='inputWithIcon'>
                        <input class='block' id='lName' type = 'text' placeholder= 'Last Name' name='lName' required="required" autofocus>
                        <i class='fas fa-address-card' aria-hidden='true'></i>
                    </div>

                    <div class='inputWithIcon'>
                        <input class='block' id='email' type = 'text' placeholder= 'E-mail Address' name='email' required="required" autofocus>
                        <i class='fas fa-user' aria-hidden='true'></i>
                    </div>

                    <div class='inputWithIcon'>
                        <select class='block inputWithIcon' id='dept' name='dept' required="required">
                            <option value="" disabled selected>Department</option>
                            <option value="1">HR</option>
                            <option value="2">Admin</option>
                            <option value="3">Sales</option>
                            <option value="4">Production</option>
                            <option value="5">Operations</option>
                            <option value="6">Food and Beverage</option>
                            <option value="7">Garage</option>
                        </select>
                        <i class='fas fa-building' aria-hidden='true'></i>
                    </div>
                    <input class='block submit centered' id='submit' type = 'submit' value= 'Send Request'/>
                    <a id='login' href='../index.php'>Log In</a>
                </div>
            </form>
        </section>
    </div>
</body>
</html>

<?php
session_start();
error_reporting(E_ALL);
include("dbconnection.php");

$dt = date("Y-m-d");
$tim = date("H:i:s");

if (isset($_SESSION['patientid'])) {
    echo "<script>window.location='patientaccount.php';</script>";
}

$err = '';
if (isset($_POST['submit'])) {
    $loginId = trim($_POST['loginid'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($loginId === '' || $password === '') {
        $err = "<div class='alert alert-danger'><strong>Heads up!</strong> Please enter both username and password.</div>";
    } else {
        $stmt = mysqli_prepare($con, "SELECT patientid FROM patient WHERE loginid = ? AND password = ? AND status = 'Active' LIMIT 1");

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $loginId, $password);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $patientId);

            if (mysqli_stmt_fetch($stmt)) {
                $_SESSION['patientid'] = $patientId;
                echo "<script>window.location='patientaccount.php';</script>";
                exit;
            } else {
                $err = "<div class='alert alert-danger'><strong>Oh!</strong> Change a few things up and try submitting again.</div>";
            }

            mysqli_stmt_close($stmt);
        } else {
            $err = "<div class='alert alert-danger'><strong>System error.</strong> Please try again later.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<title>HMS - Patient</title>
<link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link href="assets/css/main.css" rel="stylesheet">
<link href="assets/css/login.css" rel="stylesheet">
<link href="assets/css/themes/all-themes.css" rel="stylesheet" />
</head>
<body class="theme-cyan login-page authentication">
<div class="container">
    <div id="err"><?php echo $err; ?></div>
    <div class="card-top"></div>
    <div class="card">
        <h1 class="title"><span>Hospital Management System</span>Login <span class="msg">Hello, Patient!</span></h1>
        <div class="col-md-12">
            <form method="post" action="" name="frmadminlogin" id="sign_in" onSubmit="return validateform()">
                <div class="input-group"><span class="input-group-addon"><i class="zmdi zmdi-account"></i></span>
                    <div class="form-line">
                        <input type="text" name="loginid" id="loginid" class="form-control" placeholder="Username" />
                    </div>
                </div>
                <div class="input-group"><span class="input-group-addon"><i class="zmdi zmdi-lock"></i></span>
                    <div class="form-line">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" />
                    </div>
                </div>
                <div>
                    <div class="">
                        <input type="checkbox" name="rememberme" id="rememberme" class="filled-in chk-col-pink">
                        <label for="rememberme">Remember Me</label>
                    </div>
                    <div class="text-center">
                        <input type="submit" name="submit" id="submit" value="Login" class="btn btn-raised waves-effect g-bg-cyan" />
                    </div>
                    <div class="text-center"><a href="forgot-password.html">Forgot Password?</a></div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="clear"></div>
<div class="theme-bg"></div>
</div>
</div>
<script type="application/javascript">
var alphaExp = /^[a-zA-Z]+$/;
var alphaspaceExp = /^[a-zA-Z\s]+$/;
var numericExpression = /^[0-9]+$/;
var alphanumericExp = /^[0-9a-zA-Z]+$/;
var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;

function validateform()
{
    if(document.frmpatlogin.loginid.value == "")
    {
        alert("Login ID should not be empty..");
        document.frmpatlogin.loginid.focus();
        return false;
    }
    else if(document.frmpatlogin.password.value == "")
    {
        alert("Password should not be empty..");
        document.frmpatlogin.password.focus();
        return false;
    }
    else if(document.frmpatlogin.password.value.length < 8)
    {
        alert("Password length should be more than 8 characters...");
        document.frmpatlogin.password.focus();
        return false;
    }
}
</script>

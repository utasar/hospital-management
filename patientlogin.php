<?php
session_start();
error_reporting(E_ALL);
include("dbconnection.php");

$dt = date("Y-m-d");
$tim = date("H:i:s");

if (isset($_SESSION['adminid'])) {
    echo "<script>window.location='adminaccount.php';</script>";
}

$err = '';
if (isset($_POST['submit'])) {
    $loginId = trim($_POST['loginid'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($loginId === '' || $password === '') {
        $err = "<div class='alert alert-danger'><strong>Heads up!</strong> Please enter both username and password.</div>";
    } else {
        $stmt = mysqli_prepare($con, "SELECT adminid FROM admin WHERE loginid = ? AND password = ? AND status = 'Active' LIMIT 1");

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $loginId, $password);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $adminId);

            if (mysqli_stmt_fetch($stmt)) {
                $_SESSION['adminid'] = $adminId;
                echo "<script>window.location='adminaccount.php';</script>";
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
<title>Hospital Admin</title>
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
        <h1 class="title"><span>Hospital Management System</span>Login <span class="msg">Sign in to start your session</span></h1>
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
<script src="assets/bundles/libscripts.bundle.js"></script>
<script src="assets/bundles/vendorscripts.bundle.js"></script>
<script src="assets/bundles/mainscripts.bundle.js"></script>
</body>
</html>
<script type="application/javascript">
var alphaExp = /^[a-zA-Z]+$/;
var alphaspaceExp = /^[a-zA-Z\s]+$/;
var numericExpression = /^[0-9]+$/;
var alphanumericExp = /^[0-9a-zA-Z]+$/;
var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;

function validateform()
{
    if(document.frmadminlogin.loginid.value == "")
    {
        document.getElementById("err").innerHTML = "<div class='alert alert-info'><strong>Heads up!</strong> Please enter login ID.</div>";
        document.frmadminlogin.loginid.focus();
        return false;
    }
    else if(!document.frmadminlogin.loginid.value.match(alphanumericExp))
    {
        document.getElementById("err").innerHTML = "<div class='alert alert-warning'><strong>Heads up!</strong> Invalid login ID.</div>";
        document.frmadminlogin.loginid.focus();
        return false;
    }
    else if(document.frmadminlogin.password.value == "")
    {
        document.getElementById("err").innerHTML = "<div class='alert alert-info'><strong>Heads up!</strong> Password should not be empty.</div>";
        document.frmadminlogin.password.focus();
        return false;
    }
    else if(document.frmadminlogin.password.value.length < 8)
    {
        document.getElementById("err").innerHTML = "<div class='alert alert-info'><strong>Heads up!</strong> Password length should be at least 8 characters.</div>";
        document.frmadminlogin.password.focus();
        return false;
    }
    else
    {
        return true;
    }
}
</script>

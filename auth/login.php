<?php
session_start();
include "../db.php";

$error = "";

if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users
            WHERE username='$username'
            AND password='$password'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: ../dashboard.php");
        exit();

    } else {

        $error = "Invalid username or password.";

    }

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>

        body{
            font-family:Arial;
            background:#0f172a;
            color:white;
        }

        .login-box{

            width:350px;
            margin:100px auto;
            padding:30px;
            background:#1e293b;
            border-radius:10px;

        }

        input{

            width:100%;
            padding:10px;
            margin:10px 0;

        }

        button{

            width:100%;
            padding:10px;
            background:#06b6d4;
            border:none;
            color:white;
            font-size:16px;
            cursor:pointer;

        }

        h2{

            text-align:center;

        }

        p{

            color:red;

        }

    </style>

</head>

<body>

<div class="login-box">

<h2>Inventory Login</h2>

<?php
if($error!=""){
    echo "<p>$error</p>";
}
?>

<form method="POST">

<input
type="text"
name="username"
placeholder="Username"
required>

<input
type="password"
name="password"
placeholder="Password"
required>

<button
type="submit"
name="login">

Login

</button>

</form>

</div>

</body>
</html>
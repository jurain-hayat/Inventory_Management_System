<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<meta charset="UTF-8">

<title>Inventory Management System</title>

<link rel="stylesheet" href="/inventory/assets/css/style.css">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial;
}

body{

background:#0f172a;
color:white;

}

.header{

background:#111827;
padding:20px;
text-align:center;
font-size:28px;
font-weight:bold;

}

.container{

width:90%;
margin:auto;
margin-top:30px;

}

.card-container{

display:flex;
gap:20px;
margin-top:30px;

}

.card{

flex:1;
background:#1e293b;
padding:30px;
border-radius:12px;
text-align:center;

}

.card h2{

font-size:40px;
margin-top:10px;

}

.btn{

display:inline-block;
padding:12px 20px;
background:#06b6d4;
color:white;
text-decoration:none;
border-radius:6px;
margin-top:20px;

}

.menu{

margin-top:40px;

}

.menu a{

display:inline-block;
margin-right:20px;

}

table{

width:100%;
margin-top:30px;
border-collapse:collapse;

}

table th,
table td{

padding:12px;
border:1px solid #374151;
text-align:center;

}

table th{

background:#1f2937;

}

</style>

</head>

<body>

<div class="header">

Inventory Management System

</div>

<div class="container">
    <?php
if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['user_id'])) {
    include __DIR__ . "/navbar.php";
}
?>
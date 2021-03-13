<?php
//logout page
session_start();
unset($_SESSION['uid'],$_SESSION['user'],$_SESSION['roles']);
header("location:login.php");



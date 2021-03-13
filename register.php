<?php
//registration page
@date_default_timezone_set('America/New_York');
require_once "./smarty/Smarty.class.php";
require_once dirname(__FILE__) . '/' . 'lib' . '/' . 'Db.php';
$smarty = new Smarty();
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$fname = isset($_POST['fname']) ? trim($_POST['fname']) : '';
$lname = isset($_POST['lname']) ? trim($_POST['lname']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$database = new Db();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($username) || empty($password) || empty($email)) {
        $smarty->assign('message', 'Username, password or email cannot be empty');
        $smarty->assign('waitSecond', '5');
        $smarty->assign('jumpUrl', 'register.php');
        $smarty->display('error.html');
        exit();
    }
    $userInfo = $database->get('user', ['uid', 'username', 'password', 'email'], [
        'OR' => array(
            'username' => $username,
            'email' => $email,
        )
    ]);
    if ($userInfo) {
        $smarty->assign('message', 'Username or email already exists');
        $smarty->assign('waitSecond', '5');
        $smarty->assign('jumpUrl', 'register.php');
        $smarty->display('error.html');
        exit();
    }
    $data = [
        'username' => $username,
        'fname' => $fname,
        'lname' => $lname,
        'password' => md5($password),
        'email' => $email,
        'create_time' => date('Y-m-d H:i:s', time()),
        'update_time' => date('Y-m-d H:i:s', time()),
        'roles' => 3,
    ];
    unset($_SESSION['uid'], $_SESSION['username'], $_SESSION['roles']);
    $res = $database->insert('user', $data);
    if ($res) {
        $smarty->assign('message', 'Registration successful');
        $smarty->assign('waitSecond', '5');
        $smarty->assign('jumpUrl', 'login.php');
        $smarty->display('success.html');
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $smarty->display('register.html');
}

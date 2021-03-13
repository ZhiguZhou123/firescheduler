<?php
//login page
session_start();
require_once "./smarty/Smarty.class.php";
$smarty = new Smarty();
$site_url = !empty($_COOKIE['jump_url']) ? $_COOKIE['jump_url'] : 'index.php';
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $uid = !empty($_SESSION['uid']) ? $_SESSION['uid'] : 0;
    if ($uid) {
        header("location:$site_url");
    }
    $smarty->display('login.html');
} else {
    $userName = isset($_POST['username']) ? trim($_POST['username']) : '';
    $passWord = isset($_POST['password']) ? trim($_POST['password']) : '';
    $site_url = !empty($_COOKIE['jump_url']) ? $_COOKIE['jump_url'] : 'index.php';
    if (empty($passWord) || empty($userName)) {
        $smarty->assign('message', 'Username or password cannot be empty');
        $smarty->assign('waitSecond', '5');
        $smarty->assign('jumpUrl', $site_url);
        $smarty->display('error.html');
        exit();
    }
    require_once dirname(__FILE__) . '/' . 'lib' . '/' . 'Db.php';
    $database = new Db();
    $userInfo = $database->get('user', ['uid', 'username', 'password', 'email', 'roles','fname','lname'], [
        'username' => $userName
    ]);
    if ($userInfo) {
        $realpass = md5($passWord);
        if ($realpass == $userInfo['password']) {
            $_SESSION['username'] = $userName;
            $_SESSION['uid'] = $userInfo['uid'];
            $_SESSION['role'] = $userInfo['roles'];
            $_SESSION['name'] = $userInfo['fname'].' '.$userInfo['lname'];
            header("location:$site_url");
            exit();
        } else {
            $smarty->assign('username', $userName);
            $smarty->assign('message', 'Incorrect passowrd');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', $site_url);
            $smarty->assign('username', $userName);
            $smarty->display('error.html');
            exit();
        }
    } else {
        $smarty->assign('message', 'Username does not exist');
        $smarty->assign('waitSecond', '5');
        $smarty->assign('jumpUrl', $site_url);
        $smarty->display('error.html');
        exit();
    }

}



<?php
//importance page
session_start();
require_once "./smarty/Smarty.class.php";
require_once dirname(__FILE__) . '/' . 'lib' . '/' . 'Db.php';
$uid = !empty($_SESSION['uid']) ? intval($_SESSION['uid']) : 0;
if (empty($uid)) {
    setcookie('jump_url', 'notice.php', time() + 3600, '/');
    header("location:login.php");
    exit();
}
$smarty = new Smarty();
$database = new Db();
//left menu
$menuList =   $config = require_once(dirname(__FILE__) . '/' . 'lib/MenuConfig.php');
$smarty->assign('menuList', $menuList);
$smarty->assign('username', $_SESSION['username']);
$smarty->display('notice.html');

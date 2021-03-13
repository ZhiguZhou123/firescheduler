<?php
//homepage index
session_start();
require_once "./smarty/Smarty.class.php";
require_once dirname(__FILE__) . '/' . 'lib' . '/' . 'Db.php';
$uid = !empty($_SESSION['uid']) ? intval($_SESSION['uid']) : 0;
if (empty($uid)) {
    setcookie('jump_url', 'index.php', time() + 3600, '/');
    header("location:login.php");
    exit();
}
$smarty = new Smarty();
$database = new Db();


//left menu
$menuList =   $config = require_once(dirname(__FILE__) . '/' . 'lib/MenuConfig.php');
$centerMenu[] = [
    'name' => 'Schedule a Fire Drill',
    'url' => "subscribe.php?type=add",
];
$centerMenu[] = [
    'name' => 'View Upcoming Fire Drills',
    'url' => "subscribe.php?type=index",
];
$centerMenu[] = [
    'name' => 'List of IU Buildings',
    'url' => "building.php?type=index",
];
$centerMenu[] = [
    'name' => 'Importance of Fire Drills',
    'url' => "notice.php",
];

$smarty->assign('menuList', $menuList);
$smarty->assign('centerMenu', $centerMenu);
$smarty->assign('username', $_SESSION['username']);
$smarty->display('index.html');

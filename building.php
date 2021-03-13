<?php
//front-end building list
session_start();
require_once "./smarty/Smarty.class.php";
require_once dirname(__FILE__) . '/' . 'lib' . '/' . 'Db.php';
$uid = !empty($_SESSION['uid']) ? intval($_SESSION['uid']) : 0;
$role = !empty($_SESSION['role']) ? $_SESSION['role'] : 0;
header("content-type:text/html;charset=utf-8");
if (empty($uid)) {
    setcookie('jump_url', 'building.php', time() + 3600, '/');
    header("location:login.php");
    exit();
}
$smarty = new Smarty();
$database = new Db();
//left menu
$menuList = $config = require_once(dirname(__FILE__) . '/' . 'lib/MenuConfig.php');
$smarty->assign('menuList', $menuList);
$smarty->assign('username', $_SESSION['username']);

$type = isset($_GET['type']) ? trim($_GET['type']) : 'index';
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $typelist = $database->select('buildingcategory', ['id', 'cate_name']);
    if ($typelist) {
        $typelist = array_column($typelist, 'cate_name', 'id');
    }

    if ($type == 'detail') {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            $smarty->assign('message', 'Incorrect parameter');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'building.php?type=index');
            $smarty->display('error.html');
            exit();
        }
        $data = $database->get('buildings', ['id', 'name', 'building_type', 'max_capacity','building_code','char_two_code','create_time', 'update_time', 'address', 'area', 'limit_num'], [
            'id' => $id
        ]);
        $data['type_name'] = isset($typelist[$data['building_type']]) ? $typelist[$data['building_type']] : 'unkonw';
        $smarty->assign('data', $data);
        $smarty->display('building_detail.html');
    } else {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
        $pre = $page - 1 > 0 ? ($page - 1) * $limit : 0;
        $list = $database->select('buildings', ['id', 'name', 'building_type', 'building_code','char_two_code','create_time', 'update_time', 'address', 'area','max_capacity'], [
            'is_del' => 0,
            // 'LIMIT' => [$pre, $limit],
        ]);
        if ($list) {
            foreach ($list as $key => $value) {
                $list[$key]['typename'] = isset($typelist[$value['building_type']]) ? $typelist[$value['building_type']] : 'unkonw';
            }
        }
        $smarty->assign('list', $list);
        $smarty->display('building_index.html');
    }
}
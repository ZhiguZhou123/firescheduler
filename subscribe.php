<?php
session_start();
//front end-drill management
@date_default_timezone_set('America/New_York');
require_once "./smarty/Smarty.class.php";
$uid = !empty($_SESSION['uid']) ? intval($_SESSION['uid']) : 0;
$role = !empty($_SESSION['role']) ? $_SESSION['role'] : 0;
header("content-type:text/html;charset=utf-8");
if (empty($uid)) {
    setcookie('jump_url', 'subscribe.php', time() + 3600, '/');
    header("location:login.php");
}
require_once dirname(__FILE__) . '/' . 'lib' . '/' . 'Db.php';

$database = new Db();
$smarty = new Smarty();
$type = isset($_GET['type']) ? $_GET['type'] : 'index';
$buildingList = $database->select('buildings', ['id', 'name']);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    //left menu
    $menuList = $config = require_once(dirname(__FILE__) . '/' . 'lib/MenuConfig.php');
    $smarty->assign('menuList', $menuList);
    $smarty->assign('username', $_SESSION['username']);
    if ($type == 'add') {
        $smarty->assign('list', $buildingList);
        $smarty->display('subscribe_add.html');
    } elseif ($type == 'edit') {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            $smarty->assign('message', 'Incorrect parameter');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=index');
            $smarty->display('error.html');
        }
        $data = $database->get('subscribe', ['id', 'user_id', 'subscribe_time', 'building_id', 'note', 'create_time', 'update_time', 'status'], [
            'id' => $id,
            'user_id' => $uid
        ]);
        $smarty->assign('list', $buildingList);
        $smarty->assign('data', $data);
        $smarty->display('subscribe_edit.html');
        exit();
    } elseif ($type == 'detail') {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            $smarty->assign('message', 'Incorrect parameter');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=index');
            $smarty->display('error.html');
            exit();
        }
        $data = $database->get('subscribe', ['id', 'user_id', 'subscribe_time', 'building_id', 'note', 'create_time', 'update_time', 'status'], [
            'id' => $id,
            'user_id' => $uid
        ]);
        $smarty->assign('list', $buildingList);
        $smarty->assign('data', $data);
        $smarty->display('subscribe_detail.html');
        exit();
    } elseif ($type == 'del') {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            $smarty->assign('message', 'Incorrect parameter');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=index');
            $smarty->display('error.html');
        }
        $res = $database->delete('subscribe', ['id' => $id, 'user_id' => $uid]);
        $smarty->assign('message', 'Successful');
        $smarty->assign('waitSecond', '5');
        $smarty->assign('jumpUrl', 'subscribe.php?type=index');
        $smarty->display('success.html');
    } else {
        $list = $database->select('subscribe',
            array(
                "[>]buildings" => array("building_id" => "id")
            ),
            ['subscribe.id', 'subscribe.user_id', 'subscribe.subscribe_time', 'subscribe.subscribe_name', 'subscribe.building_id', 'subscribe.note', 'subscribe.create_time', 'subscribe.update_time', 'subscribe.status', 'buildings.name'], [
                'AND' => ['subscribe.user_id' => $uid],
                "ORDER" => ["subscribe.subscribe_time" => "DESC"]
            ]);
        $smarty->assign('list', $list);
        $smarty->display('subscribe_index.html');
    }

} else {
    $type = isset($_POST['type']) ? $_POST['type'] : 'index';
    if ($type == 'index') {
        $smarty->display('subscribe_index.html');
    } elseif ($type == 'save') {
        $building_id = isset($_POST['building_id']) ? intval($_POST['building_id']) : 0;
        $note = isset($_POST['note']) ? trim($_POST['note']) : '';
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $user_id = $uid;
        $date = isset($_POST['date']) ? trim($_POST['date']) : 0;
        if (!$building_id) {
            $smarty->assign('message', 'Select building');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=add');
            $smarty->display('error.html');
            exit;
        }
        if (empty($date)) {
            $smarty->assign('message', 'Select timeslot');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=add');
            $smarty->display('error.html');
            exit;
        }



//1.Determine if weekend
        $weenkend = date('w', $date);
        if ($weenkend == 0 || $weenkend == 6) {
            $smarty->assign('message', 'Selected timeslot is during a weekend');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=add');
            $smarty->display('error.html');
            exit;
        }

        $time = strtotime($date);
        if($time <= time()){
            $smarty->assign('message', 'Selected timeslot has already passed');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=add');
            $smarty->display('error.html');
            exit;
        }
        $start_time = date('Y-m-d 00:00:00', $time);
        $end_time = date('Y-m-d 23:59:59', $time);
        //2.Determine if timeslot is taken
        $data = $database->select('subscribe', ['*'], [
            'building_id' => $building_id,
            "subscribe_time[<>]" => [$start_time, $end_time]
        ]);
        if (count($data)) {
            $smarty->display('error.html');
            $smarty->assign('message', 'Timeslot is taken, unable to schedule');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=index');
            $smarty->display('error.html');
            exit;
        }

        ////3.Determine if reaches drill limit
        $years = date('Y-01-01 00:00:00', $date);
        $hasSubscribe = $database->count('subscribe', ['id'], [
            'building_id' => $building_id,
            "subscribe_time[>=]" => $years
        ]);
        $buildingData = $database->get('buildings', ['id', 'limit_num', 'name'], [
            'id' => $building_id,
        ]);
        if ($hasSubscribe > 0 && $hasSubscribe > $buildingData['limit_num']) {
            $msg = 'Exceeds ' . $buildingData['name'] . 'drill limit ' . $buildingData['limit_num'] . ',unable to schedule';
            $smarty->assign('message', $msg);
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=index');
            $smarty->display('error.html');
            exit();
        }
        $insertData = [
            'building_id' => $building_id,
            'user_id' => $user_id,
            'subscribe_time' => $date,
            'note' => $note,
            'status' => 0,
            'create_time' => date('Y-m-d H:i:s', time()),
            'update_time' => date('Y-m-d H:i:s', time()),
            'subscribe_name' => !empty($_SESSION['name']) ? $_SESSION['name'] : $_SESSION['username'],
        ];

        $res = $database->insert('subscribe', $insertData);
        if ($res) {
           /* $smarty->assign('message', 'Successful');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=index');
            $smarty->display('success.html');
           */
            header("location:subscribe.php?type=index");
            exit();
        } else {
            $smarty->assign('message', 'Failed');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=index');
            $smarty->display('error.html');
            exit();
        }
    } elseif ('type' == 'update') {
        $building_id = isset($_POST['building_id']) ? intval($_POST['building_id']) : 0;
        $note = isset($_POST['note']) ? trim($_POST['note']) : '';
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $user_id = $uid;
        $date = isset($_POST['date']) ? trim($_POST['date']) : 0;
        if (!$building_id) {
            $smarty->assign('message', 'Select building');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=add');
            $smarty->display('error.html');
            exit;
        }
        if (empty($date)) {
            $smarty->assign('message', 'Select timeslot');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=add');
            $smarty->display('error.html');
            exit;
        }
        //1.Determine if weekend
        $weenkend = date('w', $date);
        if ($weenkend == 0 || $weenkend == 6) {
            $smarty->assign('message', 'Selected time is during a weekend');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=add');
            $smarty->display('error.html');
            exit;
        }
        $time = strtotime($date);
        if($time <= time()){
            $smarty->assign('message', 'Selected timeslot has already passed');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=add');
            $smarty->display('error.html');
            exit;
        }
        $start_time = date('Y-m-d 00:00:00', $time);
        $end_time = date('Y-m-d 23:59:59', $time);
        //2.Determine if timeslot is taken
        $data = $database->select('subscribe', ['*'], [
            'building_id' => $building_id,
            "subscribe_time[<>]" => [$start_time, $end_time]
        ]);
        if (count($data)) {
            $smarty->display('error.html');
            $smarty->assign('message', 'Timeslot is taken, unable to schedule');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=index');
            $smarty->display('error.html');
            exit;
        }

        ////3.Determine if reaches drill limit
        $years = date('Y-01-01 00:00:00', $date);
        $hasSubscribe = $database->count('subscribe', ['id'], [
            'building_id' => $building_id,
            "subscribe_time[>=]" => $years
        ]);
        $buildingData = $database->get('buildings', ['id', 'limit_num', 'name'], [
            'id' => $building_id,
        ]);
        if ($hasSubscribe > 0 && $hasSubscribe > $buildingData['limit_num']) {
            $msg = 'Exceeds ' . $buildingData['name'] . 'drill limit ' . $buildingData['limit_num'] . ',unable to schedule';
            $smarty->assign('message', $msg);
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=index');
            $smarty->display('error.html');
            exit();
        }
        $insertData = [
            'building_id' => $building_id,
            'user_id' => $user_id,
            'subscribe_time' => $date,
            'note' => $note,
            'status' => 0,
            'update_time' => date('Y-m-d H:i:s', time()),
        ];

        $res = $database->update('subscribe', $insertData, ['id' => $id]);
        if ($res) {
            $smarty->assign('message', 'Successful');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=index');
            $smarty->display('success.html');
            exit();
        } else {
            $smarty->assign('message', 'Failed');
            $smarty->assign('waitSecond', '5');
            $smarty->assign('jumpUrl', 'subscribe.php?type=edit&id=' . $id);
            $smarty->display('error.html');
            exit();
        }
    }
}



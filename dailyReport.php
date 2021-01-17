<?php
require "vendor/autoload.php";
require_once("getData.php");
require_once("countAmount.php");
require_once("createTextAndSendSlack.php");
require_once("toSheets.php");

$yesterday = new DateTime('yesterday 00:00:00', new DateTimeZone('Asia/Tokyo'));
$yesterday_u = $yesterday -> format('U');
$today = new DateTime('midnight', new DateTimeZone('Asia/Tokyo'));
$today_u = $today -> format('U');


$get_channel = new getData();
$channel_list = $get_channel -> get_channel_list();

$get_users = new getData();
$users_list = $get_users -> get_users_list();

$get_intern_users = new getData();
$intern_users_list = $get_intern_users -> get_intern_users_list();

$count_total_num = 0;
$count_num = array();

foreach($channel_list as $key){
    $channelID = $key['id'];

    $Data = new getData();
    $decodedData = $Data -> get_data($yesterday_u, $today_u, $channelID);

    $countAmount = new countAmount();
    $countData = $countAmount -> count($decodedData, $count_total_num, $count_num, $users_list);
    $count_num = $countData['count'];
    $count_total_num = $countData['totalCount'];
}

$makeTextAndSend = new createTextAndSendSlack();
$rawJson = $makeTextAndSend -> create_text_daily($count_total_num, $count_num, $users_list);
$makeTextAndSend -> send_to_slack($rawJson);

$to_sheets = new toSheets();
$to_sheets -> write_users_on_dailySheets($users_list, $intern_users_list);
$to_sheets -> write_values_on_dailySheets($count_total_num, $count_num, $users_list);

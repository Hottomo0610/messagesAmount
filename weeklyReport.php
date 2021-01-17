<?php
require_once("./getData.php");
require_once("./countAmount.php");
require_once("./createTextAndSendSlack.php");
require_once("./toSheets.php");

$today = new DateTime('midnight', new DateTimeZone('Asia/Tokyo'));
$today_u = $today -> format('U');
$before1week = new DateTime('midnight', new DateTimeZone('Asia/Tokyo'));
$before1week_u = $before1week -> modify('-1 week') -> format('U');

$get_channel = new getData();
$channel_list = $get_channel -> get_channel_list();

$get_users = new getData();
$users_list = $get_users -> get_users_list();

$count_total_num = 0;
$count_num = array();

foreach($channel_list as $key){
    $channelID = $key['id'];

    $Data = new getData();
    $decodedData = $Data -> get_data($before1week_u, $today_u, $channelID);

    $countAmount = new countAmount();
    $countData = $countAmount -> count($decodedData, $count_total_num, $count_num);
    $count_num = $countData['count'];
    $count_total_num = $countData['totalCount'];
}

$makeTextAndSend = new createTextAndSendSlack();
$rawJson = $makeTextAndSend -> create_text_weekly($count_total_num, $count_num, $users_list);
$makeTextAndSend -> send_to_slack($rawJson);

$to_sheets = new toSheets();
$to_sheets -> write_users_on_weeklySheets($users_list);
$to_sheets -> write_values_on_weeklySheets($count_total_num, $count_num, $users_list);
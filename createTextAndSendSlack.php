<?php

class createTextAndSendSlack {
    public function create_text_daily($count_total_num, $count_num, $users_list){
        //Slackに送信するテキストを作成

        date_default_timezone_set("Asia/Tokyo");
        $week_array = array("日", "月", "火", "水", "木", "金", "土");

        $yesterday = new DateTime('yesterday 00:00:00', new DateTimeZone('Asia/Tokyo'));
        $yesterday_f = $yesterday->format('Y/m/d');
        $w = $yesterday->format('w');
        $week = $week_array[$w];

        arsort($count_num);

        $title = ":loudspeaker:*".$yesterday_f."（".$week."曜日）のSlack発信量*";
        $textString = "";
        $textString .= "`総合 => ".$count_total_num."`\n\n*＜個別発信量＞*";

        foreach($count_num as $user => $count){
            $user_name = "$user (".$users_list["$user"].")";
            $textString .= "\n".sprintf("%-40s", $user_name)."=> *$count*";
        }

        $rawJson ="{
            'username': 'messagesAmount',
            'icon_emoji': 'memo',
            'attachments': [
                {
                    'fallback': '昨日のSlack発信量',
                    'color': '#36a64f',
                    'pretext': '$title',
                    'text': '$textString'
                }
            ]
        }";

        return $rawJson;
    }

    public function create_text_weekly($count_total_num, $count_num, $users_list){
        //Slackに送信するテキストを作成

        date_default_timezone_set("Asia/Tokyo");

        $yesterday = new DateTime('yesterday', new DateTimeZone('Asia/Tokyo'));
        $yesterday_m = $yesterday->format('Y/m/d');
        $before1week = new DateTime('midnight', new DateTimeZone('Asia/Tokyo'));
        $before1week_m = $before1week -> modify('-1 week') -> format('Y/m/d');

        arsort($count_num);

        $title = ":loudspeaker:*先週（".$before1week_m."～".$yesterday_m."）のSlack発信量*";
        $textString = "";
        $textString .= "`総合 => ".$count_total_num."`\n\n*＜個別発信量＞*";

        foreach($count_num as $user => $count){
            $user_name = "$user (".$users_list["$user"].")";
            $textString .= "\n".sprintf("%-40s", $user_name)."=> *$count*";
        }

        $rawJson ="{
            'username': 'messagesAmount',
            'icon_emoji': 'memo',
            'attachments': [
                {
                    'fallback': '昨日のSlack発信量',
                    'color': '#36a64f',
                    'pretext': '$title',
                    'text': '$textString'
                }
            ]
        }";

        return $rawJson;
    }

    public function send_to_slack($rawJson){
        //CURLでSlackに送信
        $ch = curl_init("https://hooks.slack.com/services/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");
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $rawJson );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($ch);
        curl_close($ch);
    }
}
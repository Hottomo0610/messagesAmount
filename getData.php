<?php

class getData {
    public function get_channel_list(){
        //SlackのWPワークスペース内にあるチャンネルの情報を取得

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "https://slack.com/api/conversations.list?token=xoxp-XXXXXXXXXXXXXXXXXXXXXXXX&exclude_archived=true&limit=500&types=public_channel"
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);

        $decodedData = json_decode($resp, true);
        $channel_list = $decodedData['channels'];

        $i = 0;

        foreach($channel_list as $key){
            if(strpos($key['name'], 'times') !== false){
                unset($channel_list[$i]);
            }
            $i++;
        }

        $channel_list = array_values($channel_list);

        return $channel_list;
    }

    public function get_users_list(){
        //SlackのWPワークスペースに属する全てのユーザーの情報を取得

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "https://slack.com/api/users.list?token=xoxp-XXXXXXXXXXXXXXXXXXXXXXXXXX"
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);

        $decodedData = json_decode($resp, true);
        $users_list = $decodedData['members'];

        //不要なユーザーやbotを除外して、さらにデータを加工
        $valid_users_list = array();

        foreach($users_list as $value){
            if($value["is_bot"]!=1){
                if($value["deleted"]!=1){
                    if ($value["is_restricted"]!=1) {
                        if ($value["name"]!="slackbot") {
                            $valid_users_list["<@".$value["id"].">"] = $value["real_name"];
                        }
                    }
                }
            }
        }

        return $valid_users_list;
    }

    public function get_intern_users_list(){
        //インターン生用のチャンネルからインターン生のユーザーリストを取得
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "https://slack.com/api/conversations.members?token=xoxp-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX&channel=GBH6SHBAT"
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);

        $decodedData = json_decode($resp, true);
        $users_list = $decodedData['members'];
        $users_list2 = array();
        foreach($users_list as $value){
            $users_list2[] = "<@".$value.">";
        }
        return $users_list2;
    }
    
    public function get_data($yesterday_u, $today_u, $channelID){
        //Slackの特定のチャンネルから、今日から〇日前までのメッセージを取得

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "https://slack.com/api/conversations.history?token=xoxp-XXXXXXXXXXXXXXXXX&channel=$channelID&inclusive=true&oldest=$yesterday_u&latest=$today_u"
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);

        $decodedData = json_decode($resp, true);
        return $decodedData;
    }
}

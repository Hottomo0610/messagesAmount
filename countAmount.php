<?php 

class countAmount {

    public function count($decodedData, $count_total_num, $count_num, $users_list){
        $message_array = $decodedData['messages'];

        foreach($message_array as $value){
            if((array_key_exists("subtype", $value) == false)){
                if ((array_key_exists("bot_id", $value) == false)||($value['bot_id']="")){
                    if (array_key_exists("display_as_bot", $value)==false) {
                        $checkID = "<@".$value['user'].">";
                        if (array_key_exists($checkID, $users_list)) {
                            $userID = $value['user'];
                            $userKey = "<@".$userID.">";

                            if (array_key_exists("$userKey", $count_num)) {
                                $count_num["$userKey"]++;
                                $count_total_num++;
                            } else {
                                $count_num["$userKey"] = 1;
                                $count_total_num++;
                            }
                        }
                    }
                }
            }
        }

        $countData = array(
            "count" => $count_num,
            "totalCount" => $count_total_num
        );
        return $countData;
    }
}
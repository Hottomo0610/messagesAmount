<?php
require "vendor/autoload.php";

class toSheets {

    public function write_users_on_dailySheets($users_list, $intern_users_list){
        //clientの作成
        $credentials_path = "/home/vagrant/workspace/messagesAmount/credentials.json";
        $client = new \Google_Client();
        $client->setScopes([
            \Google_Service_Sheets::SPREADSHEETS,
            \Google_Service_Sheets::DRIVE,
        ]);
        $client->setAuthConfig($credentials_path);

        $send_data = array();
        $sheetName = "日次データ";

        $send_data[0][0] = "";
        $send_data[0][1] = "曜日";
        $send_data[0][2] = "合計";
        $send_data[1][0] = "常時 / インターン生";
        $send_data[1][1] = "";
        $send_data[1][2] = "";
        $send_data[2][0] = "Date";
        $send_data[2][1] = "Week";
        $send_data[2][2] = "Total";

        $i = 3;

        foreach($users_list as $key => $value){
            if(in_array($key, $intern_users_list)){
                $send_data[0][$i] = $value;
                $send_data[1][$i] = "Intern";
            } else {
                $send_data[0][$i] = $value;
                $send_data[1][$i] = "Jouji";
            }
            $send_data[2][$i] = $key;
            $i++;
        }

        $service = new \Google_Service_Sheets($client);
        $spreadsheet_id = "1FgH0W9V_Js9MN0KeqEU7HSvzilVSLMAggcqdnIXJ54E";
        $range = $sheetName."!A1";
        $body = new \Google_Service_Sheets_ValueRange([
            "majorDimension" => "ROWS",
            "values" => $send_data
        ]);
        $params = ['valueInputOption' => 'USER_ENTERED'];
        $result = $service->spreadsheets_values->update($spreadsheet_id, $range, $body, $params);

        //$updated_cell_count = $result->getUpdatedCells();
        //printf("%d cells updated.", $result->getUpdatedCells());
        //echo $updated_cell_count;
    }

    public function write_users_on_weeklySheets($users_list){
        //clientの作成
        $credentials_path = "/home/vagrant/workspace/messagesAmount/credentials.json";
        $client = new \Google_Client();
        $client->setScopes([
            \Google_Service_Sheets::SPREADSHEETS,
            \Google_Service_Sheets::DRIVE,
        ]);
        $client->setAuthConfig($credentials_path);

        $send_data = array();
        $sheetName = "週次データ";

        $send_data[0][0] = "";
        $send_data[0][1] = "合計";
        $send_data[1][0] = "Date";
        $send_data[1][1] = "Total";

        $i = 2;

        foreach($users_list as $key => $value){
            $send_data[0][$i] = $value;
            $send_data[1][$i] = $key;
            $i++;
        }

        $service = new \Google_Service_Sheets($client);
        $spreadsheet_id = "1FgH0W9V_Js9MN0KeqEU7HSvzilVSLMAggcqdnIXJ54E";
        $range = $sheetName."!A1";
        $body = new \Google_Service_Sheets_ValueRange([
            "majorDimension" => "ROWS",
            "values" => $send_data
        ]);
        $params = ['valueInputOption' => 'USER_ENTERED'];
        $result = $service->spreadsheets_values->update($spreadsheet_id, $range, $body, $params);
    }


    public function write_values_on_dailySheets($count_total_num, $count_num, $users_list){
        $credentials_path = "/home/vagrant/workspace/messagesAmount/credentials.json";
        $client = new \Google_Client();
        $client->setScopes([
            \Google_Service_Sheets::SPREADSHEETS,
            \Google_Service_Sheets::DRIVE,
        ]);
        $client->setAuthConfig($credentials_path);

        $sheetName = "日次データ";

        $week_array = array("日", "月", "火", "水", "木", "金", "土");

        $newDate = new DateTime("yesterday 00:00:00", new DateTimeZone('Asia/Tokyo'));
        $send_date = $newDate -> format("Y/m/d");
        $w = $newDate->format('w');
        $send_week = $week_array[$w];

        $users_index = array();
        $send_data = array();
        $send_data[0][0] = $send_date;
        $send_data[0][1] = $send_week."曜日";
        $send_data[0][2] = $count_total_num;

        $i = 3;

        foreach($users_list as $key => $value){
            $users_index[$key] = $i;
            $i++;
        }

        foreach($count_num as $key => $value){
            $a = $users_index[$key];
            $send_data[0][$a] = $value;
        }

        $length = count($users_list);
        
        for($j = 2; $j<$length+3; $j++){
            if(array_key_exists($j, $send_data[0])!=true){
                $send_data[0][$j] = 0;
            }
        }
        ksort($send_data[0]);

        //var_dump($send_data);

        $service = new \Google_Service_Sheets($client);
        $spreadsheet_id = "1FgH0W9V_Js9MN0KeqEU7HSvzilVSLMAggcqdnIXJ54E";
        $range = $sheetName."!A3";
        $body = new \Google_Service_Sheets_ValueRange([
            "majorDimension" => "ROWS",
            "values" => $send_data
        ]);
        $params = ['valueInputOption' => 'USER_ENTERED'];
        $result = $service->spreadsheets_values->append($spreadsheet_id, $range, $body, $params);
        //echo $result;

        //$updated = $result->getUpdates();
        //echo $updated;
    }


    public function write_values_on_weeklySheets($count_total_num, $count_num, $users_list){
        $credentials_path = "/home/vagrant/workspace/messagesAmount/credentials.json";
        $client = new \Google_Client();
        $client->setScopes([
            \Google_Service_Sheets::SPREADSHEETS,
            \Google_Service_Sheets::DRIVE,
        ]);
        $client->setAuthConfig($credentials_path);

        $sheetName = "週次データ";

        $yesterday = new DateTime('yesterday', new DateTimeZone('Asia/Tokyo'));
        $send_date1 = $yesterday->format('Y-m-d');
        $before1week = new DateTime('midnight', new DateTimeZone('Asia/Tokyo'));
        $send_date2 = $before1week -> modify('-1 week') -> format('Y-m-d');

        $send_date = $send_date2."～".$send_date1;

        $users_index = array();
        $send_data = array();
        $send_data[0][0] = $send_date;
        $send_data[0][1] = $count_total_num;

        $i = 2;

        foreach($users_list as $key => $value){
            $users_index[$key] = $i;
            $i++;
        }

        foreach($count_num as $key => $value){
            $a = $users_index[$key];
            $send_data[0][$a] = $value;
        }

        $length = count($users_list);
        
        for($j = 0; $j<$length+1; $j++){
            if(array_key_exists($j, $send_data[0])!=true){
                $send_data[0][$j] = 0;
            }
        }
        ksort($send_data[0]);

        $service = new \Google_Service_Sheets($client);
        $spreadsheet_id = "1FgH0W9V_Js9MN0KeqEU7HSvzilVSLMAggcqdnIXJ54E";
        $range = $sheetName."!A3";
        $body = new \Google_Service_Sheets_ValueRange([
            "majorDimension" => "ROWS",
            "values" => $send_data
        ]);
        $params = ['valueInputOption' => 'USER_ENTERED'];
        $result = $service->spreadsheets_values->append($spreadsheet_id, $range, $body, $params);
    }
}
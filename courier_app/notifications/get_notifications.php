<?php
if(isset($_REQUEST['ws_token'])) {
    require_once('../initialize.php');
    if ($_REQUEST['ws_token']!=WS_TOKEN) {
        header("HTTP/1.0 403 Forbidden");
    } 
    else
    {
        header("Content-Type: application/json; charset=UTF-8");
        if (isset($_REQUEST["param1"]))
        {
            $customer_id = decrypt_output($_REQUEST['param1']);
            $device_type = decrypt_output($_REQUEST['param2']);
            $app_language_id = decrypt_output($_REQUEST['param3']);
            $query_text =addslashes("App\\Models\\Courier");
         

            //AccessToken Control
            $access_token = $_REQUEST['access_token'];
            $request_access_token_items_array = CONTROL_ACCESS_TOKEN($conn,$access_token,$customer_id);
            if ($request_access_token_items_array['status_code'] == '1') {
                $request_data_array = array();
                $query_select_request = $conn->query("SELECT * FROM `notifications` WHERE `notifiable_id`='$customer_id' AND `notifiable_type`='$query_text' AND `deleted_at` IS NULL ORDER BY `created_at` DESC");
                while($objRequest = $query_select_request->fetch_object())
                {
                    $notification_id = $objRequest->id;
            
                           $notification_title = "WeLokal";//GET_STRIPPED_TEXT($objRequest->notification_title);
                        $notification_text = GET_STRIPPED_TEXT($objRequest->data);

                        $notification_text = json_decode($notification_text, true);

                        $notification_text =  $notification_text['message'];

                        $notification_date = $objRequest->created_at;
                        $request_background_color_code = "FFFFFF";
                        $request_background_color_code2 ="000000";
                        array_push($request_data_array, array($notification_id,$notification_title,$notification_text,$notification_date,$request_background_color_code,$request_background_color_code2));
                }
                $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>$request_data_array);
            }
            else
            {
                $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>array());
            }
        }

        //UPDATE NOTIFICATION AS READ
        $conn->query("UPDATE `notifications` SET `read_at`= NOW() WHERE `notifiable_id`='$customer_id' AND `notifiable_type`='$query_text'");
        
        echo encrypt_output(json_encode($json_output_request_array));
        $conn->close();
    }
}
else { header("HTTP/1.0 403 Forbidden"); }
?>
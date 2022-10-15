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
            $setting_option_id = decrypt_output($_REQUEST['param2']);
            $setting_option_value = decrypt_output($_REQUEST['param3']);
            $app_language_id = decrypt_output($_REQUEST['param4']);
            $device_type = decrypt_output($_REQUEST['param5']);

            //AccessToken Control
            $access_token = $_REQUEST['access_token'];
            $request_access_token_items_array = CONTROL_ACCESS_TOKEN($conn,$access_token,$customer_id);
            if ($request_access_token_items_array['status_code'] == '1') {
                //CHECK BLOCKING USER
                $query_select_request = $conn->query("SELECT * FROM `tbl_notification_setting_records` WHERE `setting_option_id`='$setting_option_id' AND `customer_id`='$customer_id'");
                if($query_select_request->num_rows <= 0)
                {
                    //INSERT SIDE
                    $query_insert_request = $conn->query("INSERT INTO `tbl_notification_setting_records`(customer_id,setting_option_id,setting_option_value) VALUES('$customer_id', '$setting_option_id','$setting_option_value')");
                    if($query_insert_request)
                    {
                        $json_output_request_array = array('part1'=>"OK",'part2'=>"");
                    }
                    else
                    {
                        $message = GET_RESULT_MESSAGE($conn,'101',$app_language_id);
                        $json_output_request_array = array('part1'=>"FAIL",'part2'=>$message);
                    }
                }
                else
                {
                    //UPDATE SIDE
                    $query_update_request = $conn->query("UPDATE `tbl_notification_setting_records` SET `setting_option_value`='$setting_option_value'  WHERE `setting_option_id`='$setting_option_id' AND `customer_id`='$customer_id'");
                    if($query_update_request)
                    {
                        $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>"OK",'part2'=>"");
                    }
                    else
                    {
                        $message = GET_RESULT_MESSAGE($conn,'101',$app_language_id);
                        $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>"FAIL",'part2'=>$message);
                    }
                }
            }
            else { $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>"FAIL",'part2'=>$request_access_token_items_array['access_token']); }
        }
        echo encrypt_output(json_encode($json_output_request_array));
        $conn->close();
    }
}
else { header("HTTP/1.0 403 Forbidden"); }
?>
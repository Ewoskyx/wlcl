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
            $app_language_id = decrypt_output($_REQUEST['param2']);
            $device_type = decrypt_output($_REQUEST['param3']);

            //AccessToken Control
            $access_token = $_REQUEST['access_token'];
            $request_access_token_items_array = CONTROL_ACCESS_TOKEN($conn,$access_token,$customer_id);
            if ($request_access_token_items_array['status_code'] == '1') {
                $request_data_array = array();
                $query_select_request = $conn->query("SELECT * FROM `tbl_notification_settings` WHERE `visible`=1 AND `language_id`='$app_language_id'");
                while($objRequest = $query_select_request->fetch_object())
                {
                    $request_option_id = $objRequest->id;
                    $request_option_title = $objRequest->title;
                    $request_option_value = "1";

                    //GET SETTING OPTION DETAILS
                    $query_select_request_option = $conn->query("SELECT * FROM `tbl_notification_setting_records` WHERE `setting_option_id`='$request_option_id' AND `customer_id`='$customer_id'");
                    if ($query_select_request_option->num_rows > 0) 
                    {
                        $objRequestOption = $query_select_request_option->fetch_object();
                        $request_option_value = $objRequestOption->setting_option_value;
                    }
                    array_push($request_data_array, array($request_option_id,$request_option_title,$request_option_value));
                }
                $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>$request_data_array);
            }
            else
            {
                $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>array());
            }
        }
        echo encrypt_output(json_encode($json_output_request_array));
        $conn->close();
    }
}
else { header("HTTP/1.0 403 Forbidden"); }
?>
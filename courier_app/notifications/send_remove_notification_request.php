<?php
if(isset($_REQUEST['ws_token'])) {
    require_once('../initialize.php');
    if ($_REQUEST['ws_token']!=WS_TOKEN) {
        header("HTTP/1.0 403 Forbidden");
    } 
    else
    {
        header("Content-Type: application/json; charset=UTF-8");
        if (isset($_REQUEST["param1"]) && isset($_REQUEST["param2"]))
        {
            $customer_id = decrypt_output($_REQUEST['param1']);
            $notification_id = decrypt_output($_REQUEST['param2']);
            $app_language_id = decrypt_output($_REQUEST['param3']);
            $device_type = decrypt_output($_REQUEST['param4']);
            $query_text =addslashes("App\\Models\\Courier");
         


            //AccessToken Control
            $access_token = $_REQUEST['access_token'];
            $request_access_token_items_array = CONTROL_ACCESS_TOKEN($conn,$access_token,$customer_id);
            if ($request_access_token_items_array['status_code'] == '1') {
                $query_insert_request = $conn->query("UPDATE `notifications` SET `deleted_at`= NOW() WHERE `notifiable_id`='$customer_id' AND `notifiable_type`='$query_text' AND `id`='$notification_id'");
                if($query_insert_request)
                {
                    $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>"OK",'part2'=>"");
                }
                else
                {
                    $result_message = GET_RESULT_MESSAGE($conn,'101',$app_language_id);
                    $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>"FAIL",'part2'=>$message);
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
<?php
if(isset($_REQUEST['ws_token'])) {
    require_once('../initialize.php');
    if ($_REQUEST['ws_token']!=WS_TOKEN) {
        header("HTTP/1.0 403 Forbidden");
    }
    else
    {
        header("Content-Type: application/json; charset=UTF-8");
        if (isset($_REQUEST['param1']))
        {
            $customer_id       = decrypt_output($_REQUEST['param1']);
            $request_form_data = decrypt_output($_REQUEST['param2']);
            $app_language_id   = decrypt_output($_REQUEST['param3']);
            $device_type       = decrypt_output($_REQUEST['param4']);

            $request_form_data_array = explode('[#]', $request_form_data);
            $request_fistname        = trim($request_form_data_array[0]);
            $request_lastname        = trim($request_form_data_array[1]);
            $username                = trim($request_form_data_array[2]);
            $request_email           = trim($request_form_data_array[3]);
            $request_telephone       = trim($request_form_data_array[4]);

            //AccessToken Control
            $access_token = $_REQUEST['access_token'];
            $request_access_token_items_array = CONTROL_ACCESS_TOKEN($conn,$access_token,$customer_id);
            if ($request_access_token_items_array['status_code'] == '1') 
            {
                $query_update_request = $conn->query("UPDATE `couriers` SET `surname`='$request_lastname',`username`='$username',`name`='$request_fistname',`email`='$request_email',`phone`='$request_telephone' WHERE `id`='$customer_id'");
                if($query_update_request)
                {
                    $message = GET_RESULT_MESSAGE($conn,'100',$app_language_id);
                    $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>"OK",'part2'=>$message);

                    //SET LOG
                    SET_CUSTOMER_LOGS($conn,$customer_id,'account_settings_change',base64_encode("FName:".$request_fistname.", Email:".$request_email.", PNumber:".$request_telephone),'1','1','Kurye');
                }
                else
                {
                    $message = GET_RESULT_MESSAGE($conn,'101',$app_language_id);
                    $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>"FAIL",'part2'=>$message);
                }
            }
            else 
            {
                $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>"FAIL",'part2'=>$request_access_token_items_array['access_token']);
            }
        }
    
        echo encrypt_output(json_encode($json_output_request_array));
        $conn->close();
    }
}
else { header("HTTP/1.0 403 Forbidden"); }
?>
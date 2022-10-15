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
            $customer_id = decrypt_output($_REQUEST['param1']);
            $device_open_udid = decrypt_output($_REQUEST['param2']);
            $device_id = decrypt_output($_REQUEST['param3']);
            $device_vendor_id = decrypt_output($_REQUEST['param4']);
            $app_language_id = decrypt_output($_REQUEST['param5']);
            $device_type = decrypt_output($_REQUEST['param6']);
            
            //AccessToken Control
            $access_token = $_REQUEST['access_token'];
            $request_access_token_items_array = CONTROL_ACCESS_TOKEN($conn,$access_token,$customer_id);


            $request_title = GET_RESULT_MESSAGE($conn,'122',$app_language_id);
            $request_detail = GET_RESULT_MESSAGE($conn,'116',$app_language_id);
            $request_titles_array = array($request_title,$request_detail);
            $request_time_in_second = "60";

            $request_time = date("Y-m-d H:i:s"); 
            $request_random_code = mt_rand(1000000000,9999999999);
            $request_qr_code = $customer_id."-".$request_random_code."-".$device_open_udid."-".$device_type."#".$request_time;
            
            //GET USER DATA
            //$request_qr_code = GET_SELECTED_USER_DATA_LIST($conn,$customer_id)['barcode'];
            
            $query_update_request = $conn->query("UPDATE `couriers` SET `qr_code` = '$request_qr_code' WHERE `id` = '$customer_id'");
            if($query_update_request)
            {
                $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>$request_qr_code,'part2'=>$request_titles_array,'part3'=>$request_time_in_second);

                //SET LOG
                SET_CUSTOMER_LOGS($conn,$customer_id,'create_qrcode',base64_encode("QRCode:".$request_qr_code),'1','1','Kurye');
            }
            else
            {
                $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>"NOT_CREATED_QRCODE_YET",'part2'=>$request_titles_array,'part3'=>$request_time_in_second);
            }
        } 
        
        echo encrypt_output(json_encode($json_output_request_array));
        $conn->close();
    }
}
else { header("HTTP/1.0 403 Forbidden"); }
?>
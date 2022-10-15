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
            $customer_id     = decrypt_output($_REQUEST['param1']);
            $scanned_code    = decrypt_output($_REQUEST['param2']);
            $user_id         = decrypt_output($_REQUEST['param3']);
            $order_id        = decrypt_output($_REQUEST['param4']);
            $app_language_id = decrypt_output($_REQUEST['param5']);
            $device_type     = decrypt_output($_REQUEST['param6']);

             //AccessToken Control
            $access_token = $_REQUEST['access_token'];
            $request_access_token_items_array = CONTROL_ACCESS_TOKEN($conn,$access_token,$customer_id);

            //PARSE SCANNED QR CODE
            // $parseScannedCode = explode("-",$scanned_code);
            // $request_prefix   = $parseScannedCode[0];
            // $request_user_id  = $parseScannedCode[1];

            //CHECK FOR SCANNED STICKER STATUS
            $query_select_qr_code = $conn->query("SELECT `id` FROM `customers` WHERE `qr_code`='$scanned_code'");
           

            if($query_select_qr_code->num_rows > 0)
            {
                $objQrCode = $query_select_qr_code->fetch_object();

                $request_user_id = $objQrCode->id;

                if($request_user_id == $user_id)
                {
                   //SET LOG
                    SET_CUSTOMER_LOGS($conn,$customer_id,'submit_order',base64_encode("QRCode:".$scanned_code),'1','1','Kurye');

                    //END PROCESS UPDATE ORDERS
                    $order_courier_status_id = "3";
                    $conn->query("UPDATE `order_histories` SET `order_status_id` = '$order_courier_status_id' WHERE `order_id`='$order_id'");

                    $message = GET_RESULT_MESSAGE($conn,'100',$app_language_id);
                    $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>"OK",'part2'=>$message);

                }
                else
                {
                    $message = GET_RESULT_MESSAGE($conn,'101',$app_language_id);
                    $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>"FAIL",'part2'=>$message);

                    //SET LOG
                    SET_CUSTOMER_LOGS($conn,$customer_id,'error_user_qrcode',base64_encode("QRCode:".$scanned_code),'1','1','Kurye');
                }
            }
            else //IF THERE IS NO QR CODE IN DATABASE
            {
                $message = GET_RESULT_MESSAGE($conn,'101',$app_language_id); //176
                    $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>"FAIL",'part2'=>$message);

                //SET LOG
                SET_CUSTOMER_LOGS($conn,$customer_id,'error_qrcode',base64_encode("QRCode:".$scanned_code),'1','1','Kurye');
            }
        } 
        
        echo encrypt_output(json_encode($json_output_request_array));
        $conn->close();
    }
}
else { header("HTTP/1.0 403 Forbidden"); }
?>
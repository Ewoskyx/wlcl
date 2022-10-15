<?php
if(isset($_REQUEST['ws_token'])) {
    require_once('initialize.php');

    if ($_REQUEST['ws_token']!=WS_TOKEN) {
        header("HTTP/1.0 403 Forbidden");
    }
    else
    {
        header("Content-Type: application/json; charset=UTF-8");
        // if (isset($_REQUEST['param1']))
        // {
            $courier_id         = decrypt_output($_REQUEST['param1']);
            $app_version_number = decrypt_output($_REQUEST['param2']);
            $device_open_udid   = decrypt_output($_REQUEST['param3']);
            $app_language_id    = decrypt_output($_REQUEST['param4']);
            $device_type        = decrypt_output($_REQUEST['param5']);
            
            if (empty($app_language_id)) {
                $app_language_id = 1;
            }
            
            //AccessToken Control
            $access_token = $_REQUEST['access_token'];
            
            $request_access_token_items_array = CONTROL_ACCESS_TOKEN($conn,$access_token,$courier_id);
            
            if ($request_access_token_items_array['status_code'] == '1')
            {
                $query_selet_request = $conn->query("SELECT * FROM `courrier_menus` WHERE  `visible`=1  AND `app_language_id` = '$app_language_id' ORDER BY sort_order ASC");
                
                $request_data_list = array();
                while($rowRequest = $query_selet_request->fetch_object())
                {
                    $badge_value = "0";
                    $request_status_value = $rowRequest->request_status_value;
                    if ($request_status_value == '1') {
                        $query_select_order = $conn->query("SELECT `order_histories`.`order_id`,`order_histories`.`order_status_id`,`orders`.`customer_id`,`orders`.`payment_type_id`,`orders`.`address`,`orders`.`address_id`,`orders`.`main_price`,`orders`.`discounting_price`,`orders`.`order_type`,`orders`.`created_at` FROM `order_histories` INNER JOIN `orders` ON `order_histories`.`id`=`orders`.`id` WHERE `order_histories`.`order_status_id`=2  AND `orders`.`courier_id`='$courier_id'  ORDER BY `order_histories`.`order_id` DESC");
                
                        $badge_value = $query_select_order->num_rows;
                    }
                    elseif ($request_status_value == '2')
                    {
                        $query_select_order = $conn->query("SELECT `order_histories`.`order_id`,`order_histories`.`order_status_id`,`orders`.`customer_id`,`orders`.`payment_type_id`,`orders`.`address`,`orders`.`address_id`,`orders`.`main_price`,`orders`.`discounting_price`,`orders`.`order_type`,`orders`.`created_at` FROM `order_histories` INNER JOIN `orders` ON `order_histories`.`id`=`orders`.`id` WHERE `order_histories`.`order_status_id`=3  AND `orders`.`courier_id`='$courier_id'  ORDER BY `order_histories`.`order_id` DESC");
                        $badge_value = $query_select_order->num_rows;
                    }
                    
                    $request_image_ulr = SERVICE_BASE_URL."/imgs/".$rowRequest->menu_image;
                    
                    array_push($request_data_list, array($rowRequest->id,$rowRequest->menu_title,$request_image_ulr,$rowRequest->prepare_segue,strval($badge_value)));
                }
                
                
                $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>$request_data_list);
            }
            else
            {
                $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>array());
            }
        // }
        // else
        // {
        //     $json_output_request_array = array('token'=>'','part1'=>array(),'part2'=>array(),'part3'=>0,'part4'=>"0",'part5'=>"",'part6'=>"",'part7'=>array());
        // }
        echo encrypt_output(json_encode($json_output_request_array));
        $conn->close();
    }
}
else { 
   header("HTTP/1.0 403 Forbidden"); }
?>
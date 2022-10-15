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
                $query_selet_request = $conn->query("SELECT * FROM `courrier_menus` WHERE  `visible`=1  AND app_language_id = '$app_language_id' ORDER BY sort_order ASC");
                $intt = 0;
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


                        if ($intt ==0) {

                            array_push($request_data_list, array($rowRequest->id,$rowRequest->menu_title,$request_image_ulr,$rowRequest->prepare_segue,strval($badge_value),strval("52.485059"),strval("-1.886223")));
                            array_push($request_data_list, array($rowRequest->id,$rowRequest->menu_title,$request_image_ulr,$rowRequest->prepare_segue,strval($badge_value),strval("52.483541"),strval("-1.886304")));
                        }
                        else
                            if ($intt ==1) {
                        
                         array_push($request_data_list, array($rowRequest->id,$rowRequest->menu_title,$request_image_ulr,$rowRequest->prepare_segue,strval($badge_value),strval("52.482365"),strval("-1.883263")));
                          array_push($request_data_list, array($rowRequest->id,$rowRequest->menu_title,$request_image_ulr,$rowRequest->prepare_segue,strval($badge_value),strval("52.481336"),strval("-1.885284")));
                    }else if ($intt ==2) {

                        
                    }
                    $intt = $intt +1;
                   
                }
                
                //UPDATE CONTROL
                $request_any_popup_message_array = array();
                $query_select_request = $conn->query("SELECT * FROM `mobile_settings` WHERE `visible`='1' AND `type`='2' AND `language_id`='$app_language_id' AND `version`<>'$app_version_number'");
                if($objRequest = $query_select_request->fetch_object())
                {
                    $request_message_content = str_replace("SERVICE_BASE_URL", SERVICE_BASE_URL, $objRequest->contents);
                    array_push($request_any_popup_message_array, $request_message_content,$objRequest->button_title,$objRequest->update_link);
                }
                
                $request_badge_value = "0";
                //GET NOTIFICATION BADGE COUNT
                  $query_text =addslashes("App\\Models\\Courier");
                $query_select_notification = $conn->query("SELECT * FROM `notifications` WHERE `notifiable_id`='$courier_id' AND `notifiable_type`='$query_text'AND `read_at` IS NULL AND `deleted_at` IS NULL ORDER BY `created_at` DESC");
                if ($row_badge_count = $query_select_notification->fetch_object()) {
                    $request_badge_value = $row_badge_count->NOF_NOTIF_MESSAGE;
                }
                
            
                //GET COURIER LOGOUT STATUS
                $querySelectLogoutStatus = $conn->query("SELECT `working_status` FROM `couriers` WHERE `id`='$courier_id'");
                $rowLogOutStatus = $querySelectLogoutStatus->fetch_object();
                $courier_logout_status = "1"; //0 or LEAVE BLANK FOR FORCE TO LOGIN
                $courier_on_task = $rowLogOutStatus->working_status;
                $courier_status_title  = ($courier_on_task == '1') ? "ON" : "OFF";

                //GET USER LOGOUT STATUS
                $request_user_profile_array = array();
                if (strlen($courier_id)>0) 
                {
                    //$request_user_data_array = GET_USER_DATA_LIST($courier_id,$conn);
                    $query_select_request = $conn->query("SELECT * FROM `couriers` WHERE `id`= '$courier_id' AND `visible`=1");
                    
                    if ($objRequest = $query_select_request->fetch_object()){

                       $request_photo_file =SERVICE_AVATAR_BASE_URL."personel/".$objRequest->avatar;

                       $request_user_profile_array = array($courier_id, $objRequest->name,$objRequest->surname,$objRequest->username, $objRequest->email, $objRequest->phone,$request_photo_file, "1"); 
                    }
                }

                $request_segment_array = array();
                array_push($request_segment_array,array('id'=>'1','title'=>'Patient'));
                array_push($request_segment_array,array('id'=>'2','title'=>'In distribution'));
                array_push($request_segment_array,array('id'=>'3','title'=>'He is not working today'));
                
                $repair_message = '';
                $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>$request_data_list,'part2'=>$request_any_popup_message_array,'part3'=>5,'part4'=>$courier_logout_status,'part5'=>$courier_on_task,'part6'=>strval($request_badge_value),'part7'=>$request_user_profile_array,'part8'=>$request_segment_array,'part9'=>$repair_message,'part10'=>$courier_status_title);
            }
            else
            {
                $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>array(),'part2'=>array(),'part3'=>0,'part4'=>"0",'part5'=>"",'part6'=>"",'part7'=>array(),'part8'=>array(),'part9'=>'');
            }
    
        echo encrypt_output(json_encode($json_output_request_array));
        $conn->close();
    }
}
else { 
   header("HTTP/1.0 403 Forbidden"); }
?>
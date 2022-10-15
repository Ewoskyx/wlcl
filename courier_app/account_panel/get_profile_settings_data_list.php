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
            $user_id            = decrypt_output($_REQUEST['param1']);
            $app_language_id    = decrypt_output($_REQUEST['param2']);
            $device_type        = decrypt_output($_REQUEST['param3']);
            
            $request_data_list = array();
            //AccessToken Control
            $access_token = $_REQUEST['access_token'];
            
            $request_access_token_items_array = CONTROL_ACCESS_TOKEN($conn,$access_token,$user_id);
            
            if ($request_access_token_items_array['status_code'] == '1')
            {
                $app_language_id = 2;
                //////////////////// PROFILE SETTINGS /////////////////////////
                $query_select_result = $conn->query("SELECT `T1`.`id`,`T2`.`title`,`T1`.`image`,`T1`.`prepare_segue`,`T1`.`badge_status_id` FROM `courier_app_profile_settings` AS `T1` INNER JOIN `courier_app_profile_settings_description` AS `T2` ON `T1`.`id` = `T2`.`parent_id` WHERE `T2`.`language_id`='$app_language_id' AND `T1`.`visible` = 1 ORDER BY `T1`.`sort_order`");
                $first_row_control =0;
                while($rowData = $query_select_result->fetch_object())
                {
                    $row_name = $rowData->title;
                    $row_image = SERVICE_BASE_URL."/imgs/profile_images/".$rowData->image;
                    $row_prepare_segue = $rowData->prepare_segue;
                    $row_badge_status_id = $rowData->badge_status_id;
                    
                    $row_badge_value = "0";
                    $menu_badge_value = "0";
                    if($row_badge_status_id == '3')
                    {
                        //GET NOTIFICATION BADGE COUNT
                        //$query_select_notification = $conn->query("SELECT COUNT(`id`) AS NOF_NOTIF_MESSAGE FROM tbl_notifications WHERE `customer_id`='$user_id' AND `check_status`='0'");
                        //$row_badge_count = $query_select_notification->fetch_object();
                        $row_badge_count="0";
                        $row_badge_value = $row_badge_count->NOF_NOTIF_MESSAGE;
                    }


                    
                    array_push($request_data_list, array($row_name,$row_image,$row_badge_value,$row_prepare_segue));
                }

                $request_data_list2 = array();
                array_push($request_data_list2,array('1','Patient'));
                array_push($request_data_list2,array('2','In distribution'));
                array_push($request_data_list2,array('3','He is not working today'));
                
                $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>$request_data_list,'part2'=>$request_data_list2,'part3'=>'WORKING ON');
            }
            else
            {
                $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>array(),'part2'=>array(),'part3'=>'');
            }
        }
        echo encrypt_output(json_encode($json_output_request_array));
        $conn->close();
    }
}
else {
    header("HTTP/1.0 403 Forbidden"); }
?>
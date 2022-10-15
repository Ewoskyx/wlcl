<?php
if(isset($_REQUEST['ws_token'])) {
    require_once('../initialize.php');
    if ($_REQUEST['ws_token']!=WS_TOKEN) {
        header("HTTP/1.0 403 Forbidden");
    } 
    else
    {
        header("Content-Type: application/json; charset=UTF-8");
        if (isset($_REQUEST['param1']) && $_FILES["file"]["name"])
        {
            $customer_id = decrypt_output($_REQUEST['param1']);
            $app_language_id = decrypt_output($_REQUEST['param2']);
            $device_type = decrypt_output($_REQUEST['param3']);

            //AccessToken Control
            $access_token = $_REQUEST['access_token'];
            $request_access_token_items_array = CONTROL_ACCESS_TOKEN($conn,$access_token,$customer_id);
            if ($request_access_token_items_array['status_code'] == '1') {
                $photo_name = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0 , 30 ).".jpg";
                 if(move_uploaded_file($_FILES["file"]["tmp_name"],  "../../../../sushidotto/public/uploads/tablet/personel/".$photo_name))             
                {   

                    $query_update_request = $conn->query("UPDATE `couriers` SET `avatar`='$photo_name' WHERE `id`='$customer_id'");
                    if($query_update_request)
                    {
                      $request_photo = SERVICE_AVATAR_BASE_URL."personel/".$photo_name;
                        $message = GET_RESULT_MESSAGE($conn,'100',$app_language_id);
                        $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>"OK",'part2'=>$message,'part3'=>$request_photo);

                        //SET LOG
                        //SET_CUSTOMER_LOGS($conn,$customer_id,'profil_photo_change',base64_encode("Kurye:","Photo:".$photo_name),'1');
                    }
                    else
                    {
                        $message = GET_RESULT_MESSAGE($conn,'101',$app_language_id);
                          $message ="2";
                        $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>"FAIL",'part2'=>$message,'part3'=>$photo_name);
                    }
                }
                else
                {
                    $message = GET_RESULT_MESSAGE($conn,'101',$app_language_id);
                      $message ="ewew2";
                    $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>"FAIL",'part2'=>$message,'part3'=>$photo_name);

                    //SET LOG
                    SET_CUSTOMER_LOGS($conn,$customer_id,'profil_photo_change',base64_encode("Kurye:","Photo:".$photo_name.", Error:Fail upload"),'0');
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
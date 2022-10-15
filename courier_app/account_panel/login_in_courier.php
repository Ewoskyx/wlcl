<?php
if(isset($_REQUEST['ws_token'])) {
    require_once('../initialize.php');
    if ($_REQUEST['ws_token']!=WS_TOKEN) {
        header("HTTP/1.0 403 Forbidden");
    } 
    else
    {
        header("Content-Type: application/json; charset=UTF-8");
        if (isset($_REQUEST['param1']) && isset($_REQUEST['param2']))
        {
            $username = decrypt_output($_REQUEST['param1']);
             $password = decrypt_output($_REQUEST['param2']);
              $latitude = decrypt_output($_REQUEST['param3']);
               $longitude = decrypt_output($_REQUEST['param4']);
                $device_id = decrypt_output($_REQUEST['param5']);
                 $device_type = decrypt_output($_REQUEST['param6']);
                  $app_language_id = decrypt_output($_REQUEST['param7']);

            // $options = [
            //     'cost' => 12,
            // ];
            // $creat_encrypt_password = 'sushi1234'
            // $request_encrypt_password = password_hash($creat_encrypt_password, PASSWORD_BCRYPT, $options);
            // $conn->query("UPDATE `couriers` SET `password` = '$request_encrypt_password' WHERE `id`=11");


            $query_select_request = $conn->query("SELECT * FROM `couriers` WHERE `email`= '$username' OR  `username`= '$username' AND `visible`=1");
            if ($query_select_request->num_rows <= 0)
            {
                $message = "Girilen bilgilere uygun bir kullanıcı bulunmamaktadır";
                $json_output_request_array = array('part1'=>"FAIL",'part2'=>$message,'part3'=>"",'part4'=>"");
            }
            else
            {
                $objRequest = $query_select_request->fetch_object();
                $salt_value_password = $objRequest->password;

                if (password_verify($password, $salt_value_password)) 
                {
                     $customer_id = $objRequest->id;

                     $request_access_token_items_array = SET_ACCESS_TOKEN($conn,$customer_id);

                     $request_access_token = $request_access_token_items_array['access_token'];

                     $request_photo_file =SERVICE_AVATAR_BASE_URL."personel/".$objRequest->avatar;

                     $request_user_data_array = array($customer_id, $objRequest->name,$objRequest->surname,$objRequest->username, $objRequest->email, $objRequest->phone,$request_photo_file,  $objRequest->branch_id); 

                     $conn->query("UPDATE `couriers` SET `lat`= '".$latitude."',`lng` = '".$longitude."',`device_id` = '".$device_id."',`device_type` = '".$device_type."' WHERE `id`='".$customer_id."'");

                     $json_output_request_array = array('token'=>$request_access_token,'part1'=>"OK",'part2'=>"",'part3'=>$request_user_data_array);
                }
                else
                {
                        $message = "Kullanıcı bilgileri yanlış";
                        $json_output_request_array = array('part1'=>"FAIL",'part2'=>$message,'part3'=>"",'part4'=>"");
                }
            }  
        }

        echo encrypt_output(json_encode($json_output_request_array));
        $conn->close();
    }
}
else { header("HTTP/1.0 403 Forbidden"); }
?>
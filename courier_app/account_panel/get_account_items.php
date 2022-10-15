<?php
if(isset($_REQUEST['ws_token'])) 
{
    require_once('../initialize.php');
    if ($_REQUEST['ws_token']!=WS_TOKEN) {
        header("HTTP/1.0 403 Forbidden");
    }
    else
    {
        header("Content-Type: application/json; charset=UTF-8");
        $json_output_request_array =  array();
        if (isset($_REQUEST['param1']))
        {
            $customer_id     = decrypt_output($_REQUEST['param1']);
            $app_language_id = decrypt_output($_REQUEST['param2']);
            $device_type     = decrypt_output($_REQUEST['param3']);
            
            //GET PACKAGES
            $request_data_array = array();
            $query_select_request = $conn->query("SELECT `username`, `name`, `surname`, `avatar`, `phone`, `email` FROM `couriers` WHERE `id` = '$customer_id'");
            while($objRequest =$query_select_request->fetch_object())
            {
        
                 //$user_image = SERVICE_BASE_URL."/account_panel/profile_photos/".$objRequest->photo;

                 $user_image = SERVICE_AVATAR_BASE_URL."personel/".$objRequest->avatar;
        

                array_push($request_data_array, array($objRequest->name,'',SERVICE_BASE_URL.'account_panel/imgs/n1@2x.png','Ad'));

                array_push($request_data_array, array($objRequest->surname,'',SERVICE_BASE_URL.'account_panel/imgs/n1@2x.png','Soyad'));

                array_push($request_data_array, array($objRequest->username,'',SERVICE_BASE_URL.'account_panel/imgs/n1@2x.png','Kullanıcı Adı'));

                array_push($request_data_array, array($objRequest->email,'FieldTypeEmail',SERVICE_BASE_URL.'account_panel/imgs/n2@2x.png','E-Posta'));

                array_push($request_data_array, array($objRequest->phone,'FieldTypePhone',SERVICE_BASE_URL.'account_panel/imgs/n6@2x.png','Telefon Numarası'));
            }
            
            $request_title = ($app_language_id ==1) ? "HESAP BİLGİLERİM" : "ACCOUNT INFORMATION" ;

            $json_output_request_array = array('part1'=>$request_data_array,'part2'=>$request_title,'part3'=>$user_image);
        }
        echo encrypt_output(json_encode($json_output_request_array));
        $conn->close();
    }
}
else { header("HTTP/1.0 403 Forbidden"); }
// ?>
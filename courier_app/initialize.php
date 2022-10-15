<?php

date_default_timezone_set('Europe/Istanbul');
setlocale(LC_ALL, 'tr_TR');
$servername = "localhost";
$username = "user_sushidotto";
$password = "s=a7sEetoBkr";
$dbname = "welokal";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error)
die("Connection failed: " . $conn->connect_error);
$conn->query("set names 'utf8'");
set_time_limit(0);//max_execution_time

define("PRODUCTION_ENVIRONMENT", "PRODUCTION");
define("WS_TOKEN", "DzkGxvJNj1IjJLNVOYK94AT6c8crxvvs3MlD7qOXLtAk1oU0vj");

define("MAIN_BASE_URL", "https://welokal.limonist.ist/");
define("SERVICE_BASE_URL", MAIN_BASE_URL."courier_app/");
define("SERVICE_BASE_URL2", SERVICE_BASE_URL."uploads/");
define("SERVICE_IMAGE_BASE_URL", SERVICE_BASE_URL."uploads/");
define("SERVICE_VIDEO_BASE_URL", SERVICE_BASE_URL."uploads/");
define("SERVICE_AVATAR_BASE_URL", MAIN_BASE_URL."public/uploads/tablet/");
define("SERVICE_AVATAR_CUSTOMER_BASE_URL", MAIN_BASE_URL."public/");

define("ORDER_ICREMENT_VALUE", "2");
define("ORDER_MAX_VALUE", "20");
define("ORDER_MIN_VALUE", "0");
define("STUDENT_FRIENDLY_VALUE", 10);
define("PRODUCT_OPTION_CONTROL", "2"); // 2 IS NEW POPUP 1 OLD

define("ONESIGNAL_API_KEY", "NDY2OTYyMzItMzkyZC00Zjg3LTliNDYtYWI0N2VjZTM0YzY4");
define("ONESIGNAL_API_ID", "f973e9cb-3a06-4e8e-94b7-69bcf9298eec");

define("ONESIGNAL_BRANCH_API_KEY", "OGU2MzJjYTYtNzY3Yi00NTVkLWE3NTEtZDg5ZGFmYTdjZTY4");
define("ONESIGNAL_BRANCH_API_ID", "a98f735f-4a6b-47ef-9b2b-f093115982ee");

define("ONESIGNAL_API_URL", "https://onesignal.com/api/v1/notifications");

define("NOTIF_PEM_FILE_NAME", "ck_app_apns.pem");
define("NOTIF_PEM_FILE_PSWD", "apns:2195");
define("GOOGLE_API_KEY", "AIzaSyCWlbuiIzFTeY9NJxYFLPo7KghnJY1JE9g");

define("APP_NAME", "WE LOKAL");
define("APP_MAIL_SERVER", "mail.limonistcustomer.com");
define("APP_MAIL_SERVER_USERNAME", "support@limonistcustomer.com");
define("APP_MAIL_SERVER_PASSWORD", "limonist0328");
define("APP_MAIL_ADDRESS", "destek@limonist.com");
define("FIREBASE_KEY", "AAAAHtdV_Po:APA91bGouOQkq_l5ZGlvZWDUoLbBKCeg2J4qfMewzSC7j9mXgTPu6w4_3LrIRmVSEakj2csYMgHuwsSo6LKXr-fUUkuQILtpcsKeq0HezJQdXJD7cOiY0-DD2s4wZkcpwkJ3RPW0QybG");
define("APP_NAME_FOR_ANDROID_NOTIFICATION", "WE LOKAL");

function encrypt_output($plaintext)
{
    if (PRODUCTION_ENVIRONMENT=='PRODUCTION') {
        require_once('config/Aes256cbc.php');
        $aes = new Aes();
        return $aes->encrypt($plaintext);
    } else {
        
        return base64_encode($plaintext);
    }
    return FALSE;
}
function decrypt_output($plaintext)
{
    if (PRODUCTION_ENVIRONMENT=='PRODUCTION') {
        require_once('config/Aes256cbc.php');
        $aes = new Aes();
        return $aes->decrypt($plaintext);
    } else {
        return base64_decode($plaintext);
    }
    return FALSE;
}

function CONTROL_ACCESS_TOKEN($conn,$access_token,$customer_id)
{
    //0-Error / No user
    //1- New Token
    //2- Logout
    
    if (intval($customer_id)>0) {
        $query_select_request = $conn->query("SELECT * FROM `token` WHERE `customer_id`= '$customer_id' AND `access_token`= '$access_token' LIMIT 1");
        if ($objRequest = $query_select_request->fetch_object())
        {
            $request_id = $objRequest->id;
            $request_refresh_token = $objRequest->refresh_token;
            
            //GET TOKEN CREDENTIALS
            $request_token_client_id      = "LimonsitApp";
            $request_token_client_secret  = "0mqhkjo1cf79x7htqfr5w338le6chvcn6rtf5216ptza5tinvi";
            $request_token_client_api_url ="https://token.limonist.ist/oauth2/public";
            $curl = curl_init();
            curl_setopt_array($curl, array(
                                           CURLOPT_URL => $request_token_client_api_url.'/middleware_use.php/api/user',
                                           CURLOPT_RETURNTRANSFER => true,
                                           CURLOPT_ENCODING => '',
                                           CURLOPT_MAXREDIRS => 10,
                                           CURLOPT_TIMEOUT => 0,
                                           CURLOPT_FOLLOWLOCATION => true,
                                           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                           CURLOPT_CUSTOMREQUEST => 'GET',
                                           CURLOPT_POSTFIELDS => 'client_id='.$request_token_client_id.'&client_secret='.$request_token_client_secret.'&scope=basic',
                                           CURLOPT_HTTPHEADER => array(
                                                                       'Authorization: Bearer '.$access_token,
                                                                       'Content-Type: application/x-www-form-urlencoded'
                                                                       ),
                                           ));
            
            $response = curl_exec($curl);
            $http_code_info = curl_getinfo($curl);
            curl_close($curl);
            if (intval($http_code_info['http_code']) == 200)
            {
                return array("status_code"=>'1',"access_token"=>$access_token);
            }
            else
            {
                //CALL REFRESH TOKEN
                $curl = curl_init();
                curl_setopt_array($curl, array(
                                               CURLOPT_URL => $request_token_client_api_url.'/refresh_token.php/access_token',
                                               CURLOPT_RETURNTRANSFER => true,
                                               CURLOPT_ENCODING => '',
                                               CURLOPT_MAXREDIRS => 10,
                                               CURLOPT_TIMEOUT => 0,
                                               CURLOPT_FOLLOWLOCATION => true,
                                               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                               CURLOPT_CUSTOMREQUEST => 'POST',
                                               CURLOPT_POSTFIELDS => 'grant_type=refresh_token&client_id='.$request_token_client_id.'&client_secret='.$request_token_client_secret.'&refresh_token='.$request_refresh_token,
                                               CURLOPT_HTTPHEADER => array(
                                                                           'Content-Type: application/x-www-form-urlencoded'
                                                                           ),
                                               ));
                
                $response = curl_exec($curl);
                $http_code_info = curl_getinfo($curl);
                curl_close($curl);
                $request_response_json_array = json_decode($response);
                if (intval($http_code_info['http_code']) == 200)
                {
                    if (is_object($request_response_json_array))
                    {
                        $request_token_type = $request_response_json_array->token_type;
                        $request_access_token = $request_response_json_array->access_token;
                        $request_refresh_token = $request_response_json_array->refresh_token;
                        $request_expires_in = $request_response_json_array->expires_in;
                        
                        //UPDATE TOKEN
                        $conn->query("UPDATE `tbl_customer_token` SET `access_token`='$request_access_token',`refresh_token`='$request_refresh_token',`expire_time`='$request_expires_in' WHERE `id`='$request_id'");
                        return array("status_code"=>'1',"access_token"=>$request_access_token);
                    }
                }
                else
                {
                    $request_error = $request_response_json_array->error;
                    $request_error_descriptionn = $request_response_json_array->error_description;
                    //return array("status_code"=>'2',"error_type"=>$request_error,"error_description"=>$request_error_descriptionn);
                    // return array("status_code"=>'2',"access_token"=>'$request_error_descriptionn'); // status_code 2
                    return array("status_code"=>'1',"access_token"=>'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiJMaW1vbnNpdEFwcCIsImp0aSI6Ijk1YTJlMzE0ZjRlOTE2NDVjNDM5NTFjYjhmNDc4MmIyNDFkNDNlYzRiNzE5YzYzMTUwMzUyYWYzOTc5MDJjODFlNjZmZWViNGQ0OTdhZjAwIiwiaWF0IjoxNjYyMTE1NTY4LCJuYmYiOjE2NjIxMTU1NjgsImV4cCI6MTY2MjExNjQ2OCwic3ViIjoiMSIsInNjb3BlcyI6WyJiYXNpYyIsImVtYWlsIl19.Fea2_XdBvOi-0vGZP8GUCcN7Lj3wHBWXcl0W-6hxsm45OjbbvCu_TgGIyP7GXmeiqwfpML5qAof2GhtfuS48D6vSsqGLsMk4bLYAvrVtN90egW44sy5OnwBvp8QmoBCG8izfAf8A-lER9tqnPKkNpgCEW8Q_J0VD0yVADqH3ZBizVY-4fCmPntnlnmYF2Er7mR4GI2hworcY1WLfZ9lWEVTHrC_uE5yJg7E2PKoTSQv9zR5IrOViDTrtu7PQpKUkTgYKjaGobqW4TKA3p-MNNWjQvqWnuox0rrZDAezsYdUzx4bQLVJuqAiJFnOVAbnxALW_2jm5jvP-WQexNwDCEQ'); 
                }
            }
        }
        else { 
        // return array("status_code"=>'2',"access_token"=>"Please check your user information and try again."); 
        return array("status_code"=>'1',"access_token"=>'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiJMaW1vbnNpdEFwcCIsImp0aSI6Ijk1YTJlMzE0ZjRlOTE2NDVjNDM5NTFjYjhmNDc4MmIyNDFkNDNlYzRiNzE5YzYzMTUwMzUyYWYzOTc5MDJjODFlNjZmZWViNGQ0OTdhZjAwIiwiaWF0IjoxNjYyMTE1NTY4LCJuYmYiOjE2NjIxMTU1NjgsImV4cCI6MTY2MjExNjQ2OCwic3ViIjoiMSIsInNjb3BlcyI6WyJiYXNpYyIsImVtYWlsIl19.Fea2_XdBvOi-0vGZP8GUCcN7Lj3wHBWXcl0W-6hxsm45OjbbvCu_TgGIyP7GXmeiqwfpML5qAof2GhtfuS48D6vSsqGLsMk4bLYAvrVtN90egW44sy5OnwBvp8QmoBCG8izfAf8A-lER9tqnPKkNpgCEW8Q_J0VD0yVADqH3ZBizVY-4fCmPntnlnmYF2Er7mR4GI2hworcY1WLfZ9lWEVTHrC_uE5yJg7E2PKoTSQv9zR5IrOViDTrtu7PQpKUkTgYKjaGobqW4TKA3p-MNNWjQvqWnuox0rrZDAezsYdUzx4bQLVJuqAiJFnOVAbnxALW_2jm5jvP-WQexNwDCEQ');  } //status_code 2
    }
    else { 
        // return array("status_code"=>'0',"access_token"=>"Please check your user information and try again.");
    return array("status_code"=>'1',"access_token"=>'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiJMaW1vbnNpdEFwcCIsImp0aSI6Ijk1YTJlMzE0ZjRlOTE2NDVjNDM5NTFjYjhmNDc4MmIyNDFkNDNlYzRiNzE5YzYzMTUwMzUyYWYzOTc5MDJjODFlNjZmZWViNGQ0OTdhZjAwIiwiaWF0IjoxNjYyMTE1NTY4LCJuYmYiOjE2NjIxMTU1NjgsImV4cCI6MTY2MjExNjQ2OCwic3ViIjoiMSIsInNjb3BlcyI6WyJiYXNpYyIsImVtYWlsIl19.Fea2_XdBvOi-0vGZP8GUCcN7Lj3wHBWXcl0W-6hxsm45OjbbvCu_TgGIyP7GXmeiqwfpML5qAof2GhtfuS48D6vSsqGLsMk4bLYAvrVtN90egW44sy5OnwBvp8QmoBCG8izfAf8A-lER9tqnPKkNpgCEW8Q_J0VD0yVADqH3ZBizVY-4fCmPntnlnmYF2Er7mR4GI2hworcY1WLfZ9lWEVTHrC_uE5yJg7E2PKoTSQv9zR5IrOViDTrtu7PQpKUkTgYKjaGobqW4TKA3p-MNNWjQvqWnuox0rrZDAezsYdUzx4bQLVJuqAiJFnOVAbnxALW_2jm5jvP-WQexNwDCEQ'); }//status_code 0
}

function SET_ACCESS_TOKEN($conn,$customer_id)
{
    
    $request_token_client_id      = "LimonsitApp";
    $request_token_client_secret  = "0mqhkjo1cf79x7htqfr5w338le6chvcn6rtf5216ptza5tinvi";
    $request_token_client_api_url ="https://token.limonist.ist/oauth2/public";

    $curl = curl_init();
    curl_setopt_array($curl, array(
                                   CURLOPT_URL => $request_token_client_api_url.'/password.php/access_token',
                                   CURLOPT_RETURNTRANSFER => true,
                                   CURLOPT_ENCODING => '',
                                   CURLOPT_MAXREDIRS => 10,
                                   CURLOPT_TIMEOUT => 0,
                                   CURLOPT_FOLLOWLOCATION => true,
                                   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                   CURLOPT_CUSTOMREQUEST => 'POST',
                                   CURLOPT_POSTFIELDS => 'grant_type=password&client_id='.$request_token_client_id.'&client_secret='.$request_token_client_secret.'&username=usr&password=pswd&scope=basic',
                                   CURLOPT_HTTPHEADER => array(
                                                               'Content-Type: application/x-www-form-urlencoded'
                                                               ),
                                   ));
    
    $response = curl_exec($curl);
    $http_code_info = curl_getinfo($curl);
    curl_close($curl);
    $request_response_json_array = json_decode($response);
    // error_log("http_code ".$http_code_info['http_code']);
    // error_log("request_response_json_array ".$request_response_json_array);
    // error_log("error_description ".$request_response_json_array->error_description);
    if (intval($http_code_info['http_code']) == 200)  /// clear
    {
        if (is_object($request_response_json_array))
        {
            $request_token_type = $request_response_json_array->token_type;
            $request_access_token = $request_response_json_array->access_token;
            $request_refresh_token = $request_response_json_array->refresh_token;
            $request_expires_in = $request_response_json_array->expires_in;
            
            //INSERT TOKEN
            $conn->query("INSERT INTO `token`(`customer_id`, `access_token`, `refresh_token`, `expire_time`) VALUES ('$customer_id','$request_access_token','$request_refresh_token','$request_expires_in')");
            return array("status_code"=>'1',"access_token"=>$request_access_token);
        }
    }
    else
    {
        $request_error = $request_response_json_array->error;
        $request_error_description = $request_response_json_array->error_description;
        return array("status_code"=>'0',"access_token"=>"$request_error_description");
    }
}


function GET_ORDER_STATUS($conn,$order_status_id,$app_language_id="1")
{
    $status_color = "";
    
    $product_status_name = "";
    
    $queryCartProductStatus = $conn->query("SELECT t_o_d.`color`,t_o_d.status_name FROM `tbl_order_status` t_o_s INNER JOIN `tbl_order_status_description` t_o_d ON t_o_s.`order_status_id` = t_o_d.`order_status_id`   WHERE   t_o_s.`order_status_id` = '$order_status_id' AND t_o_d.`language_id` = '$app_language_id'    ORDER BY t_o_s.`sort_order` ASC");
    if ($objCartProductStatus = $queryCartProductStatus->fetch_object())
    {
        $product_status_name  = $objCartProductStatus->status_name;
        $status_color         = $objCartProductStatus->color;
    }
    
    return array("status"=>$product_status_name,"color"=>$status_color);
}



function GET_BRANCH_ADRESS($conn,$branch_id)
{
    $query_select_request = $conn->query("SELECT `address` FROM `branches` WHERE `id`='$branch_id'");
    $objRequest = $query_select_request->fetch_object();
    $full_adress= $objRequest->address;
    return $full_adress;
}

function GET_BRANCH_DATA($conn,$branch_id,$app_language_id="1")
{
    $request_data_list = array();
    $query_select_request = $conn->query("SELECT * FROM `tbl_branches` WHERE `id`='$branch_id' AND `status` = 1 AND `deleted_at` IS NULL");
    if($objRequest = $query_select_request->fetch_object())
    {
        $full_adress  = $objRequest->address;
        $phone_number = $objRequest->phone_number;
        $order_status = $objRequest->order_status;
        $branch_name  = $objRequest->title;
        $branch_lat   = $objRequest->lat;
        $branch_lng   = $objRequest->lng;

        $branch_hours = $objRequest->working_hours;

        $branch_lat_delta = "0.009";
        $branch_lng_delta = "0.009";
        
        $branch_open_hours = explode('-', $branch_hours);
        if (count($branch_open_hours)>1) {
            $start_hour = $branch_open_hours[0];
            $close_hour = $branch_open_hours[1];
        }
        
        $current_hour = date("H:i");
        
        $current_hour = strtotime($current_hour);
        
        $start_hour   = strtotime($start_hour);
        
        $close_hour   = strtotime($close_hour);
        
        $close_control = "1"; // kapali
        if ($current_hour>=$start_hour && $close_hour > $current_hour) {
            $close_control = "0"; // acik
        }

        $request_data_list = array("branch_address"=>$full_adress,"online_status"=>$order_status,"branch_name"=>$branch_name,"branch_lat"=>$branch_lat,"branch_lng"=>$branch_lng,"branch_lat_delta"=>$branch_lat_delta,"branch_lng_delta"=>$branch_lng_delta,"phone_number"=>$phone_number,"branch_hours"=>$branch_hours,"close_control"=>$close_control);
    }
    
    return $request_data_list;
}


function GET_STRIPPED_TEXT($string)
{
    $field_value = stripslashes($string);
    $field_value = html_entity_decode($field_value);
    $field_value = strip_tags($field_value);
    return $field_value;
}

function CHECK_LOGIN_BAN($customer_id,$conn)
{
    $query_select_request = $conn->query("SELECT `customer_id` FROM `users_logs` WHERE TIMESTAMPDIFF(HOUR,`date_added`,NOW())<1 AND `customer_id`='$customer_id' AND `key`='login_ban'");
    if ($query_select_request->num_rows == 0) {
        $query_select_request = $conn->query("SELECT COUNT(`customer_id`) AS `count` FROM `users_logs` WHERE `date_added`>=DATE_SUB(NOW(), INTERVAL '1' HOUR) AND `customer_id`='$customer_id'");
        $objRequest = $query_select_request->fetch_object();
        if (intval($objRequest->count)>5)
        {
            //SET LOG
            SET_CUSTOMER_LOGS($conn,$customer_id,'login_ban',base64_encode("customer_id:".$customer_id),'1');
            return true;
        }
        else { return false; }
    }
    return true;
}

function SET_CUSTOMER_LOGS($conn,$customer_id,$key,$data,$status="0",$item_id="1",$item_code="customer")
{
    $conn->query("INSERT INTO `users_logs`(`customer_id`, `item_id`, `item_code`, `key`, `data`,`status`) VALUES ('$customer_id','$item_id','$item_code','$key','$data','$status')");
}

function GET_USER_TOTAL_POINT($conn,$customer_id)
{
    //GET USER POINT
    $query_select_request = $conn->query("SELECT IFNULL(SUM(`points`),0) AS `total` FROM `customer_points` WHERE `customer_id` = '$customer_id' ");
    $objRequest = $query_select_request->fetch_object();
    $request_total_point_value = $objRequest->total;
    return $request_total_point_value;
}


function GET_SELECTED_DESCRIPTION_DATA_LIST($conn,$table_name,$descriptionable_type,$app_language_id="1")
{
    $request_data_array = array();
    $query_select_request= $conn->query("SELECT T1.`id`, T1.`image`, T2.`title`, T2.`sub_title`, T2.`description`, T2.`short_description` FROM
    $table_name T1 INNER JOIN `descriptions` T2 ON T1.`id` = T2.`descriptionable_id` WHERE  T1.`deleted_at` IS NULL AND T2.`language_id` = '1' ORDER BY T1.`row_number` ASC");
    while($objRequest=$query_select_request->fetch_object())
    {
          $id          = $objRequest->id;
          $title       = $objRequest->title;
          $sub_title   = $objRequest->sub_title;
          $description = $objRequest->description;
          $short_description = $objRequest->short_description;
          $image = SERVICE_IMAGE_BASE_URL.$objRequest->image;
        
          $request_data_array[]  = array("id"=>$id,"title"=>$title, "sub_title"=>$sub_title, "description"=>$description, "short_description"=>$short_description, "image"=>$image);
    }
    
    return $request_data_array;
}

function GET_SELECTED_USER_DATA_LIST($conn,$customer_id)
{
    $request_user_data_list = array();
    if (strlen($customer_id)>0) {
        $query_select_request = $conn->query("SELECT * FROM `customers` WHERE `id`='$customer_id'");
        if($objRequest = $query_select_request->fetch_object())
        {
            $request_name      = $objRequest->name;
            $request_surname   = $objRequest->surname;
            $request_email     = $objRequest->email;
            $request_phone     = $objRequest->phone;
            $request_gender    = $objRequest->gender;
            $request_birthdate = '';//$objRequest->birthdate;

            $name_surname = $request_name." ".$request_surname;
            $request_photo = SERVICE_AVATAR_CUSTOMER_BASE_URL.$objRequest->avatar;
            $request_app_language_id = '1';//$objRequest->app_language_id;
            $request_device_type = $objRequest->device_type;
            $request_device_id = $objRequest->device_id;
            $request_device_open_udid = $objRequest->open_udid;
            $request_city_id = $objRequest->city_id;
            $city = "Antalya";

            $request_login_status = "0";//$objRequest->login_status;

            $request_user_data_list = array("name_surname"=>$name_surname,"name"=>$request_name,"surname"=>$request_surname,"email"=>$request_email,"phone"=>$request_phone,"photo"=>$request_photo,"login_status"=>$request_login_status,"device_type"=>$request_device_type,"device_id"=>$request_device_id,"app_language_id"=>$request_app_language_id,"open_udid"=>$request_device_open_udid,"city"=>$city,"birthdate"=>$request_birthdate,"gender"=>$request_gender);
        }
    }
    
    return $request_user_data_list;
}

function GET_PAYMENT_METHOD_NAME($payment_method_id,$app_language_id,$conn)
{
    $request_name = "";
    $query_select_request = $conn->query("SELECT * FROM `tbl_payment_method_description` WHERE `parent_id`='$payment_method_id' AND `language_id`='$app_language_id'");
    if($objRequest = $query_select_request->fetch_object())
        $request_name=$objRequest->name;
    
    return $request_name;
}


function GET_SELECTED_PROMOTION_DATA($promotion_id,$app_language_id,$conn)
{
    $query_select_request = $conn->query("SELECT `T2`.`title`, `T2`.`detail`, `T1`.`type`, `T1`.`amount`, `T1`.`limit` FROM `tbl_promotion` as `T1` INNER JOIN `tbl_promotion_description` as `T2` ON `T1`.`id`=`T2`.`promotion_id` WHERE `T1`.`id`='$promotion_id' AND `T2`.`language_id`='$app_language_id'");
    $objRequest = $query_select_request->fetch_object();
    $request_tile = GET_STRIPPED_TEXT($objRequest->title);
    $request_detail = GET_STRIPPED_TEXT($objRequest->detail);
    $request_type = $objRequest->type;
    $request_amount = $objRequest->amount;
    $request_limit = $objRequest->limit;
    
    $request_items_array_list = array("title"=>$request_tile,"detail"=>$request_detail,"amount"=>$request_amount,"limit"=>$request_limit,"type"=>$request_type);
    return $request_items_array_list;
}

function GET_SELECTED_PRODUCT_ATTRIBUTES($product_id,$app_language_id,$branch_id,$conn)
{
    $query_select_request = $conn->query("SELECT `TBLPRDDES`.`name`, `TBLPRDDES`.`description`, `TBLPRD`.`image`, `TBLPRD`.`price`, `TBLPRD`.`quantity` FROM `tbl_product` as `TBLPRD` INNER JOIN `tbl_product_description` as `TBLPRDDES` ON `TBLPRD`.`id`=`TBLPRDDES`.`product_id` WHERE `TBLPRD`.`id`='$product_id' AND `TBLPRD`.`branch_id` = '$branch_id' AND  `TBLPRD`.`quantity`>0 AND `TBLPRDDES`.`language_id`='$app_language_id'");
    $objProduct = $query_select_request->fetch_object();
    $product_name = GET_STRIPPED_TEXT($objProduct->name);
    $product_description = $objProduct->description;
    $product_price = $objProduct->price;
    $product_price_old = "";
    
    $product_quantity = $objProduct->quantity;
    
    $product_image = SERVICE_IMAGE_BASE_URL.$objProduct->image;
    $product_items_array_list = array("name"=>$product_name,"price"=>$product_price,"description"=>$product_description,"image"=>$product_image,"quantity"=>$product_quantity,"price_old"=>$product_price_old);
    
    return $product_items_array_list;
}

function GET_RANDOM_COLOR_CODES($conn,$limit_value)
{
    $query_select_request_color_code = $conn->query("SELECT `color_code` FROM `tbl_color_codes` WHERE 1 ORDER BY RAND() LIMIT $limit_value");
    $rowRequestColorCode = $query_select_request_color_code->fetch_assoc();
    $request_color_codes = $rowRequestColorCode['color_code'];
    if (intval($limit_value)>1)
    {
        $rowRequestColorCode = $query_select_request_color_code->fetch_assoc();
        $request_color_codes .="[-]".$rowRequestColorCode['color_code'];
    }
    return $request_color_codes;
}


function GET_SETTINGS_VALUE($conn,$setting_code="000",$param_1="",$param_2="")
{
    $query_select_settings = $conn->query("SELECT REPLACE(REPLACE(`details`,'[PLACEHOLDER1]','$param_1'),'[PLACEHOLDER2]','$param_2') AS `details` FROM `tbl_settings` WHERE `setting_code`='$setting_code' AND `visible`=1");
    $objRequestSetting = $query_select_settings->fetch_object();
    $request_setting_value = $objRequestSetting->details;
    
    return $request_setting_value;
}

function GET_RESULT_MESSAGE($conn,$message_code,$language_id="1",$param_1="",$param_2="",$param_3="",$param_4="")
{
    $query_select_request_message = $conn->query("SELECT REPLACE(REPLACE(REPLACE(REPLACE(`message_text`,'[PLACEHOLDER1]','$param_1'),'[PLACEHOLDER2]','$param_2'),'[PLACEHOLDER3]','$param_3'),'[PLACEHOLDER4]','$param_4') AS RESULT_MESSAGE FROM `notification_and_service_messages` WHERE `message_code`='$message_code' AND `language_id`='$language_id' AND `visible`=1");
    $objRequestResultMessage = $query_select_request_message->fetch_object();
    $result_message = $objRequestResultMessage->RESULT_MESSAGE;
    return $result_message;
}

function SEND_NOTIFICATION_MESSAGE($conn,$receiver_id,$receiver_device_type,$receiver_device_id,$notification_title,$notification_message,$notification_andro_message,$notification_sound="default",$args1="ntf",$args2="",$args3=1,$args4="0")
{
    if($receiver_device_type == 'A')
    {
        $sound = 'default';
        $to= $receiver_device_id;
        $message = $notification_message;
        $activity = "MAIN_ACTIVITY";
        
        $notification = array(
                              'title' => APP_NAME_FOR_ANDROID_NOTIFICATION,
                              'body' => $message,
                              'sound' => $sound,
                              'click_action' => $activity
                              );
        
        $extraNotificationData = array("message" => $notification);
        
        $fields = array(
                        'to' => $to,
                        'data' => $extraNotificationData,
                        );
        
        $url = 'https://fcm.googleapis.com/fcm/send';
        
        $headers = array(
                         'Authorization: key=' . FIREBASE_KEY,
                         'Content-Type: application/json'
                         );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_exec($ch);
        curl_close($ch);
        curl_close($ch);
        
    }
    else if($receiver_device_type == 'I')
    {
        if (strlen($receiver_device_id) > 0)
        {
            $apiKey = ONESIGNAL_API_KEY; //one signal api key
            $appId  = ONESIGNAL_API_ID; //one signal api id
            if ($notification_andro_message = "BRANCH") {
                $apiKey = ONESIGNAL_BRANCH_API_KEY; //one signal api key
                $appId  = ONESIGNAL_BRANCH_API_ID; //one signal api id
            }
            
            $content = array("en" => $notification_message , "tr" => $notification_message);
            $fields = array('app_id'=>$appId, 'include_player_ids'=>[$receiver_device_id], 'contents'=>$content, 'headings' => array("en"=>$notification_title,"tr"=>$notification_title));
            $fields = json_encode($fields);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, ONESIGNAL_API_URL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Authorization: Basic '.$apiKey));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $response = curl_exec($ch);
            curl_close($ch);
            
            if (!$response)
            {
                // $send_success=0; // fail
                // log_message("error",PHP_EOL);
            }
            else
            {
                // $send_success=1; // success
            }
        }
    }
    
    //INSERT NOTIFICATION
    $conn->query("INSERT INTO `notifications`(`user_id`,`description`) VALUES ('$receiver_id','$notification_message')");
}

function SEND_NOTIFICATION_MESSAGE_NEW($conn,$receiver_id,$receiver_device_id,$notification_title,$notification_message)
{
    if (strlen($receiver_device_id) > 0)
    {
        $apiKey = ONESIGNAL_API_KEY; //one signal api key
        $appId = ONESIGNAL_API_ID; //one signal api id

        $content = array("en" => $notification_message , "tr" => $notification_message);
        $fields = array('app_id'=>$appId, 'include_player_ids'=>[$receiver_device_id], 'contents'=>$content, 'headings' => array("en"=>$notification_title,"tr"=>$notification_title));
        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, ONESIGNAL_API_URL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Authorization: Basic '.$apiKey));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        error_log("Bildirim iletildi");
    
    }


    //INSERT NOTIFICATION
    //$conn->query("INSERT INTO `tbl_notifications`(`customer_id`,`notification_title`,`notification_text`) VALUES ('$receiver_id','$notification_title','$notification_message')");
}


function platformType($platform)
{
    if ($platform === "iPhone1,1") return "iPhone (1st generation)";
    if ($platform === "iPhone1,2") return "iPhone 3G";
    if ($platform === "iPhone2,1") return "iPhone 3GS";
    if ($platform === "iPhone3,1") return "iPhone 4 (GSM)";
    if ($platform === "iPhone3,2") return "iPhone 4 (GSM, 2nd revision)";
    if ($platform === "iPhone3,3") return "iPhone 4 (Verizon)";
    if ($platform === "iPhone4,1") return "iPhone 4S";
    if ($platform === "iPhone5,1") return "iPhone 5 (GSM)";
    if ($platform === "iPhone5,2") return "iPhone 5 (GSM+CDMA)";
    if ($platform === "iPhone5,3") return "iPhone 5c (GSM)";
    if ($platform === "iPhone5,4") return "iPhone 5c (GSM+CDMA)";
    if ($platform === "iPhone6,1") return "iPhone 5s (GSM)";
    if ($platform === "iPhone6,2") return "iPhone 5s (GSM+CDMA)";
    if ($platform === "iPhone7,2") return "iPhone 6";
    if ($platform === "iPhone7,1") return "iPhone 6 Plus";
    if ($platform === "iPhone8,1") return "iPhone 6s";
    if ($platform === "iPhone8,2") return "iPhone 6s Plus";
    if ($platform === "iPhone8,4") return "iPhone SE";
    if ($platform === "iPhone9,1") return "iPhone 7 (GSM+CDMA)";
    if ($platform === "iPhone9,3") return "iPhone 7 (GSM)";
    if ($platform === "iPhone9,2") return "iPhone 7 Plus (GSM+CDMA)";
    if ($platform === "iPhone9,4") return "iPhone 7 Plus (GSM)";
    if ($platform === "iPhone10,1") return "iPhone 8 (GSM+CDMA)";
    if ($platform === "iPhone10,4") return "iPhone 8 (GSM)";
    if ($platform === "iPhone10,2") return "iPhone 8 Plus (GSM+CDMA)";
    if ($platform === "iPhone10,5") return "iPhone 8 Plus (GSM)";
    if ($platform === "iPhone10,3") return "iPhone X (GSM+CDMA)";
    if ($platform === "iPhone10,6") return "iPhone X (GSM)";
    if ($platform === "iPhone11,1") return "iPhone XR (GSM+CDMA)";
    if ($platform === "iPhone11,2") return "iPhone XS (GSM+CDMA)";
    if ($platform === "iPhone11,3") return "iPhone XS (GSM)";
    if ($platform === "iPhone11,4") return "iPhone XS Max (GSM+CDMA)";
    if ($platform === "iPhone11,5") return "iPhone XS Max (GSM, Dual Sim, China)";
    if ($platform === "iPhone11,6") return "iPhone XS Max (GSM)";
    if ($platform === "iPhone11,8") return "iPhone XR (GSM)";
    if ($platform === "iPhone12,1") return "iPhone 11";
    if ($platform === "iPhone12,3") return "iPhone 11 Pro";
    if ($platform === "iPhone12,5") return "iPhone 11 Pro Max";
    if ($platform === "iPhone12,8") return "iPhone SE (2nd Gen)";
    if ($platform === "iPhone13,1") return "iPhone 12 Mini";
    if ($platform === "iPhone13,2") return "iPhone 12";
    if ($platform === "iPhone13,3") return "iPhone 12 Pro";
    if ($platform === "iPhone13,4") return "iPhone 12 Pro Max";
    if ($platform === "iPod1,1") return "iPod Touch 1G";
    if ($platform === "iPod2,1") return "iPod Touch 2G";
    if ($platform === "iPod3,1") return "iPod Touch 3G";
    if ($platform === "iPod4,1") return "iPod Touch 4G";
    if ($platform === "iPod5,1") return "iPod Touch 5G";
    if ($platform === "iPod7,1") return "iPod Touch 6G";
    if ($platform === "iPad1,1") return "iPad";
    if ($platform === "iPad2,1") return "iPad 2 (WiFi)";
    if ($platform === "iPad2,2") return "iPad 2 (GSM)";
    if ($platform === "iPad2,3") return "iPad 2 (CDMA)";
    if ($platform === "iPad2,4") return "iPad 2 (WiFi)";
    if ($platform === "iPad2,5") return "iPad Mini (WiFi)";
    if ($platform === "iPad2,6") return "iPad Mini (GSM)";
    if ($platform === "iPad2,7") return "iPad Mini (CDMA)";
    if ($platform === "iPad3,1") return "iPad 3 (WiFi)";
    if ($platform === "iPad3,2") return "iPad 3 (CDMA)";
    if ($platform === "iPad3,3") return "iPad 3 (GSM)";
    if ($platform === "iPad3,4") return "iPad 4 (WiFi)";
    if ($platform === "iPad3,5") return "iPad 4 (GSM)";
    if ($platform === "iPad3,6") return "iPad 4 (CDMA)";
    if ($platform === "iPad4,1") return "iPad Air (WiFi)";
    if ($platform === "iPad4,2") return "iPad Air (GSM)";
    if ($platform === "iPad4,3") return "iPad Air (CDMA)";
    if ($platform === "iPad4,4") return "iPad Mini 2 (WiFi)";
    if ($platform === "iPad4,5") return "iPad Mini 2 (Cellular)";
    if ($platform === "iPad4,6") return "iPad Mini 2 (Cellular)";
    if ($platform === "iPad4,7") return "iPad Mini 3 (WiFi)";
    if ($platform === "iPad4,8") return "iPad Mini 3 (Cellular)";
    if ($platform === "iPad4,9") return "iPad Mini 3 (Cellular)";
    if ($platform === "iPad5,1") return "iPad Mini 4 (WiFi)";
    if ($platform === "iPad5,2") return "iPad Mini 4 (Cellular)";
    if ($platform === "iPad5,3") return "iPad Air 2 (WiFi)";
    if ($platform === "iPad5,4") return "iPad Air 2 (Cellular)";
    if ($platform === "iPad6,3") return "iPad Pro 9.7-inch (WiFi)";
    if ($platform === "iPad6,4") return "iPad Pro 9.7-inch (Cellular)";
    if ($platform === "iPad6,7") return "iPad Pro 12.9-inch (WiFi)";
    if ($platform === "iPad6,8") return "iPad Pro 12.9-inch (Cellular)";
    if ($platform === "iPad6,11") return "iPad 5 (WiFi)";
    if ($platform === "iPad6,12") return "iPad 5 (Cellular)";
    if ($platform === "iPad7,1") return "iPad Pro 12.9-inch 2 (WiFi)";
    if ($platform === "iPad7,2") return "iPad Pro 12.9-inch 2 (Cellular)";
    if ($platform === "iPad7,3") return "iPad Pro 10.5-inch (WiFi)";
    if ($platform === "iPad7,4") return "iPad Pro 10.5-inch (Cellular)";
    if ($platform === "iPad7,5") return "iPad 6 (WiFi)";
    if ($platform === "iPad7,6") return "iPad 6 (Cellular)";
    if ($platform === "iPad8,1") return "iPad Pro 11-inch (WiFi)";
    if ($platform === "iPad8,2") return "iPad Pro 11-inch (WiFi, 1TB)";
    if ($platform === "iPad8,3") return "iPad Pro 11-inch (Cellular)";
    if ($platform === "iPad8,4") return "iPad Pro 11-inch (Cellular, 1TB)";
    if ($platform === "iPad8,5") return "iPad Pro 12.9-inch 3 (WiFi)";
    if ($platform === "iPad8,6") return "iPad Pro 12.9-inch 3 (WiFi, 1TB)";
    if ($platform === "iPad8,7") return "iPad Pro 12.9-inch 3 (Cellular)";
    if ($platform === "iPad8,8") return "iPad Pro 12.9-inch 3 (Cellular, 1TB)";
    if ($platform === "i386") return "Simulator";
    if ($platform === "x86_64") return "Simulator";
    return $platform;
}

?>
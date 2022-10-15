<?php
if(isset($_REQUEST['ws_token'])) {
    require_once('../initialize.php');
    if ($_REQUEST['ws_token']!=WS_TOKEN) {
        header("HTTP/1.0 403 Forbidden");
    }
    else
    {
        header("Content-Type: application/json; charset=UTF-8");
        $json_output_request_array = array();
        $request_data_list = array();
        if (isset($_REQUEST['param1']))
        {
            $courier_id            = decrypt_output($_REQUEST['param1']);
            $app_language_id    = decrypt_output($_REQUEST['param2']);
            $device_type        = decrypt_output($_REQUEST['param3']);
            $query_text =addslashes("App\\Models\\Product");
            $query_text_package =addslashes("App\\Models\\ProductPackage");
            
            //AccessToken Control
            $access_token = $_REQUEST['access_token'];
            
            $request_access_token_items_array = CONTROL_ACCESS_TOKEN($conn,$access_token,$courier_id);
            
            if ($request_access_token_items_array['status_code'] == '1')
            {
                array_push($request_data_list, array("1","Ne233ak",'Ne233ak 7 Shotton Street','7 Shotton Street Cramlington Northumberland','BOOKED','02/09/22','23:50','TIMEOUT','LETTER','£3.85','0.1 kg'));
                array_push($request_data_list, array("2","Ne233ay",'Ne233ay 8 Shotton Street','8 Shotton Street Cramlington Northumberland','DELIVERY','03/09/22','22:50','TIMEOUT','LETTER','£4.90','0.2 kg'));
                array_push($request_data_list, array("3","Ne233ay",'Ne233ay 9 Shotton Street','9 Shotton Street Cramlington Northumberland','BOOKED','04/09/22','19:50','TIMEOUT','LETTER','£2.95','0.3 kg'));
                array_push($request_data_list, array("4","Ne233ay",'Ne233ay 10 Shotton Street','10 Shotton Street Cramlington Northumberland','DELIVERY','05/09/22','15:50','TIMEOUT','LETTER','£4.95','0.4 kg'));
                // array_push($request_data_list, array("5","Ne233ay",'Ne233ay 11 Shotton Street','11 Shotton Street Cramlington Northumberland','BOOKED','06/09/22','22:50','TIMEOUT','LETTER','£4.45','0.5 kg'));
                
                $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>$request_data_list);
            }
            else
            {
                $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>array());
            }
        }
        echo encrypt_output(json_encode($json_output_request_array));
        $conn->close();
    }
}
else {
    header("HTTP/1.0 403 Forbidden"); }
?>
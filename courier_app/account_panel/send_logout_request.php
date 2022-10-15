<?php
if(isset($_REQUEST['ws_token'])) {
    require_once('../initialize.php');
    if ($_REQUEST['ws_token']!=WS_TOKEN) {
        header("HTTP/1.0 403 Forbidden");
    }
    else
    {
        if (isset($_REQUEST['param1']))
        {
            $access_token = $_REQUEST['access_token'];
            $customer_id = decrypt_output($_REQUEST['param1']);
            $courier_on_task_status = decrypt_output($_REQUEST['param2']);
            $app_language_id = decrypt_output($_REQUEST['param3']); 
            $device_type = decrypt_output($_REQUEST['param4']); 

            $update_request = $conn->query("UPDATE `couriers` SET `working_status`='$courier_on_task_status'  WHERE `id` = '$customer_id'");

            if ($update_request) {
                  $json_output_request_array = array('part1'=>"OK",'part2'=>"");
            }
            else
            {
                $json_output_request_array = array('part1'=>"FAIL",'part2'=>$message);
            }
          

           
        }

        echo encrypt_output(json_encode($json_output_request_array));
        $conn->close();
    }
}
else { header("HTTP/1.0 403 Forbidden"); }
?>
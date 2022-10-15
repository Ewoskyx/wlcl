<?php
if(isset($_REQUEST['ws_token'])) {
    require_once('initialize.php');
    if ($_REQUEST['ws_token']!=WS_TOKEN) {
        header("HTTP/1.0 403 Forbidden");
    }
    else
    {
        if (isset($_REQUEST['param1']))
        {
            $courier_id = decrypt_output($_REQUEST['param1']);
            $courier_lat = decrypt_output($_REQUEST['param2']); // 1 user 2 personel
            $courier_lng = decrypt_output($_REQUEST['param3']); // 1 user 2 personel
            $app_language_id = decrypt_output($_REQUEST['param4']); 
            $device_type = decrypt_output($_REQUEST['param5']); 

           $conn->query("UPDATE tbl_personel SET `user_lat`='$courier_lat', `user_lng`='$courier_lng'  WHERE `id`='$courier_id'");
           
        }
        
        $conn->close();
    }
}
else { header("HTTP/1.0 403 Forbidden"); }
?>
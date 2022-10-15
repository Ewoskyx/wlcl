<?php
if(isset($_REQUEST['ws_token'])) {
    require_once('initialize.php');
    if ($_REQUEST['ws_token']!=WS_TOKEN) {
        header("HTTP/1.0 403 Forbidden");
    } 
    else
    {
        header("Content-Type: application/json; charset=UTF-8");
        if (isset($_REQUEST['param1']))
        {

         $app_version_number = decrypt_output($_REQUEST['param1']);
         $app_language_id = decrypt_output($_REQUEST['param2']);
         
         $app_update_details="";
         $app_status="NOT_YET";
         $query_select_version = $conn->query("SELECT * FROM `mobile_settings` WHERE `type`='A' AND `language_id`='$app_language_id' AND visible='1' AND `version`<>'$app_version_number'");
         $app_details ="";
        if($objRequest = $query_select_version->fetch_object())
            {
                $version= $objRequest->version;
                $contents= $objRequest->contents;
                $button_action_type = $objRequest->button_action_type;
                $is_can_close = $objRequest->is_can_close;
                $url = $objRequest->update_link;
                $title = $objRequest->button_title;
                $message_content = str_replace("SERVICE_BASE_URL", SERVICE_BASE_URL,$contents);
                $app_details = $message_content."[-]".$title."[-]".$url."[-]".$button_action_type."[-]".$is_can_close;
            } 
    
                      $json_output_request_array = array('part1'=>$app_details,'part2'=>"1");

        } 
        
        echo encrypt_output(json_encode($json_output_request_array));
        $conn->close();
    }
}
else { header("HTTP/1.0 403 Forbidden"); }
?>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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

            $email_address = trim(decrypt_output($_REQUEST['param1']));
            $app_language_id = trim(decrypt_output($_REQUEST['param2']));
            $device_type = decrypt_output($_REQUEST['param3']);

            $query_select_request = $conn->query("SELECT * FROM `couriers` WHERE `email` = '$email_address'");
            if($query_select_request->num_rows <= 0)
            {
                $message = GET_RESULT_MESSAGE($conn,'103',$app_language_id);
                $json_output_request_array = array('part1'=>"FAIL",'part2'=>$message);
            }
            else
            {
                //READ CUSTOMER DATA
                $objRequest = $query_select_request->fetch_object();
                $customer_id = $objRequest->id;

                $shuffled_chars = "abcdefghkmnopqrstuvwxyzABCDEFGHKLMNOPQRSTUVWXYZ0123456789";
                $creat_encrypt_password = substr(str_shuffle($shuffled_chars),0,8);

                $options = [
                'cost' => 12,
                ];
                // $creat_encrypt_password = 'WeLokal1234'
                $request_encrypt_password = password_hash($creat_encrypt_password, PASSWORD_BCRYPT, $options);

                    //Load Composer's autoloader
                     require 'PHPMailer/vendor/autoload.php';
                    $mail = new PHPMailer(true);
                    try {

                        $request_app_logo = SERVICE_BASE_URL."imgs/logo.png";
                        $request_mail_body_subject = GET_RESULT_MESSAGE($conn,'166',$app_language_id);
                        $request_mail_body_title = GET_RESULT_MESSAGE($conn,'165',$app_language_id);
                        $request_mail_body_detail = GET_RESULT_MESSAGE($conn,'164',$app_language_id);
                        $request_mail_body = GET_RESULT_MESSAGE($conn,'163',$app_language_id,$request_app_logo,$request_mail_body_title,$creat_encrypt_password,$request_mail_body_detail);

                         $mail->isSMTP();                                        // Set mailer to use SMTP
                         $mail->CharSet = "utf-8";
                         $mail->Host = APP_MAIL_SERVER;             // Specify main and backup SMTP servers
                         $mail->SMTPAuth = true;                               // Enable SMTP authentication
                         $mail->Username = APP_MAIL_SERVER_USERNAME;    // SMTP username
                         $mail->Password = APP_MAIL_SERVER_PASSWORD;                   // SMTP password
                         $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;                         // Enable TLS encryption, `ssl` also accepted
                         $mail->Port = 465;                                // TCP port to connect to
                         $mail->setFrom(APP_MAIL_ADDRESS, APP_NAME); 
                         $mail->addAddress($email_address, APP_NAME);     // Add a recipient
                         $mail->isHTML(true);                                        // Set email format to HTML
                         $mail->Body = $request_mail_body;
                         $mail->Subject = $request_mail_body_subject;
                         $mail->send();

                         //UPDATE PASSWORD
                         $conn->query("UPDATE `couriers` SET `password`= '$request_encrypt_password' WHERE `email`='$email_address'");

                         //SET LOG
                         SET_CUSTOMER_LOGS($conn,$customer_id,'password_recovery',base64_encode("Email:".$email_address.", Pass:".$request_encrypt_password.", Salt:".$shuffled_salt),'1');

                         $message = GET_RESULT_MESSAGE($conn,'100',$app_language_id);
                        $json_output_request_array = array('part1'=>"OK",'part2'=>$message);


                    } catch (Exception $e) {
                        //SET LOG
                        SET_CUSTOMER_LOGS($conn,$customer_id,'password_recovery',base64_encode("Email:".$email_address.", Pass:".$request_encrypt_password.", Salt:".$shuffled_salt.", Error:".$mail->ErrorInfo),'0');
                        
                        $message = $message = GET_RESULT_MESSAGE($conn,'101',$app_language_id);
                        $json_output_request_array = array('part1'=>"FAIL",'part2'=>$message);
                    }
            }
        } 
        echo encrypt_output(json_encode($json_output_request_array));
        $conn->close();
    }
}
else { header("HTTP/1.0 403 Forbidden"); }
?>
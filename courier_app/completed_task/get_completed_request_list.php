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
            $courier_id         = decrypt_output($_REQUEST['param1']);
            $app_language_id    = decrypt_output($_REQUEST['param2']);
            $device_type        = decrypt_output($_REQUEST['param3']);
            
            $request_data_list = array();
            //AccessToken Control
            $access_token = $_REQUEST['access_token'];
            
            $request_access_token_items_array = CONTROL_ACCESS_TOKEN($conn,$access_token,$courier_id);
            
            if ($request_access_token_items_array['status_code'] == '1')
            {
                $query_select_request = $conn->query("SELECT `order_histories`.`order_id`,`order_histories`.`order_status_id`,`orders`.`customer_id`,`orders`.`payment_type_id`,`orders`.`address`,`orders`.`address_id`,`orders`.`main_price`,`orders`.`discounting_price`,`orders`.`total_price`,`orders`.`order_type`,`orders`.`created_at` FROM `order_histories` INNER JOIN `orders` ON `order_histories`.`id`=`orders`.`id` WHERE `order_histories`.`order_status_id`=3 AND `orders`.`courier_id`='11' ORDER BY `order_histories`.`order_id` DESC");
                
                while($rowRequest = $query_select_request->fetch_object())
                {
                    $request_id = $rowRequest->order_id;
                    $customer_id = $rowRequest->customer_id;
                    $request_address_id = $rowRequest->address_id;
                    $request_order_status = $rowRequest->order_status_id;
                    $request_date = date('d/m/Y H:s',strtotime($rowRequest->created_at));
                    $payment_type_id = $rowRequest->payment_type_id;
                    
                    $selected_user_data_list = GET_SELECTED_USER_DATA_LIST($conn,$customer_id);
                    if (count($selected_user_data_list) > 0)
                    {
                        $request_firstname =  $selected_user_data_list['name'];
                        $request_lastname  = $selected_user_data_list['surname'];
                        $request_telephone  = $selected_user_data_list['phone'];
                        $request_first_last_name = $request_firstname." ".$request_lastname;
                        
                    }
                    
                    
                    $request_total =  "£".$rowRequest->total_price;
                    $request_address = $rowRequest->address;
                    
                    $request_address = trim($request_address);
                    
                    
                    //GET PRODUT LIST
                    $request_product_list = array();
                    $query_select_products = $conn->query("SELECT * FROM `order_items` WHERE `order_id`='$request_id'");
                    $first_row_control_product = 0;
                    while($rowRequestProduct = $query_select_products->fetch_object())
                    {
                        $product_package_id = $rowRequestProduct->product_package_id;
                        $customer_set_id = $rowRequestProduct->customer_set_id;
                        $product_info = $rowRequestProduct->product_info;
                        $type = $rowRequestProduct->type;
                        $quantity = $rowRequestProduct->quantity;
                        $price = ($quantity*$rowRequestProduct->discounting_price);
                        $total_price = "£".$rowRequestProduct->total_price;

                        $price = "£".number_format($price, 2);

                        if ($device_type =="I") {
                            $json_decode_request = json_decode($product_info, true);
                        
                            $product_title =  str_replace('Adet', "Quantity", $json_decode_request['title']);
                            $product_image = array();

                            $product_image =   $json_decode_request['image'];

                            // if (count($product_image)>1) 
                            // {
                            //     $product_image = $product_image[0];
                            // }

                            // array_push($request_product_list, array($product_title,$quantity,$price,$total_price,strval($type)));
                            array_push($request_product_list, array($product_package_id,$customer_set_id,$product_title,strval($type),$quantity,$price,$total_price,$product_image));
                        }
                        else
                        {
                            array_push($request_product_list, array($product_package_id,$customer_set_id,$product_info,$type,$quantity,$price,$total_price));
                        }
                    }
                    
                    //GET PAYMENT STATUS
                    $query_text =addslashes("App\\Models\\PaymentType"); ;
                    
                    $query_select_payment_status = $conn->query("SELECT `payment_types`.`id`,`descriptions`.`title` FROM `payment_types` INNER JOIN `descriptions` ON `payment_types`.`id` =`descriptions`.`descriptionable_id` WHERE `descriptions`.`descriptionable_type`='$query_text' AND `descriptions`.`language_id`=1 AND `payment_types`.`id`='$payment_type_id'");
                    
                    $rowRequestPaymentStatus = $query_select_payment_status->fetch_object();
                    $request_payment_method = $rowRequestPaymentStatus->title;
                    
                    // $request_payment_method = $rowRequest->payment_method;
                    
                    // //GET ADDRESS LOCATIONS
                    $query_select_address = $conn->query("SELECT * FROM `customer_addresses` WHERE `id`='$request_address_id'");
                    $rowRequestAddress = $query_select_address->fetch_object();
                    $address_latitude  = $rowRequestAddress->lat;
                    $address_longitude = $rowRequestAddress->lng;
                    
                    if (strlen($address_latitude)<=0)
                    {
                        $address_latitude = "";
                        $address_longitude = "";
                    }
                    
                    //GET ORDER STATUS
                    /* $query_select_status = $conn->query("SELECT * FROM `tbl_order_status_description` WHERE `order_status_id`='$request_order_status' AND `language_id`='$app_language_id'");
                     
                     if ($rowRequestStatus    = $query_select_status->fetch_object()) {
                     $request_order_status = $rowRequestStatus->status_name;
                     }*/
                    $request_order_status="";
                    
                    // //GET ADDRESS LOCATIONS
                    $query_select_address = $conn->query("SELECT * FROM `customer_addresses` WHERE `id`='$request_address_id'");
                    $rowRequestAddress = $query_select_address->fetch_object();
                    $address_latitude  = $rowRequestAddress->lat;
                    $address_longitude = $rowRequestAddress->lng;
                    
                    
                    if (strlen($address_latitude)<=0)
                    {
                        $address_latitude = "";
                        $address_longitude = "";
                    }
                    $request_order_mesaage = "";
                    
                    
                    $html_text = '<!DOCTYPE html><html><head><style>body {font-family: "Comfortaa"; font-size: 15px;} table {border-collapse: collapse;width: 100%;}th, td {padding: 8px;text-align: left;border-bottom: 1px solid #ddd;}</style></head><body>';
                    $html_text .= '<p style="font-size:15;"><b>'.'<p style="color:#ED1D24;">'.$request_table_title1.'</b>'."Sipariş Bilgisi".'</p>';
                    $html_text .= '<p style="font-size:15;"><b>'.$request_table_title2.'</b>'.$request_first_last_name.'</p>';
                    $html_text .= '<p style="font-size:15;"><b>'.'<p style="color:#ED1D24;">'.$request_table_title1.'</b>'."Adres Bilgisi".'</p>';
                    $html_text .= '<p style="font-size:15;"><b>'.$request_table_title4.'</b>'.$request_address.'</p>';
                    $html_text .= '<p style="font-size:15;"><b>'.'<p style="color:#ED1D24;">'.$request_table_title1.'</b>'."Ödeme Bilgisi".'</p>';
                    $html_text .= '<p style="font-size:15;"><b>'.$request_table_title6.'</b>'.$request_payment_method.'</p>';
                    $html_text .= '<p style="font-size:15px;"><b>'.$request_table_title7.'</b></p>';
                    $html_text .= '<table style="width:100%">';
                    $html_text .= '<p style="font-size:15;"><b>'.'<p style="color:#ED1D24;">'.$request_table_title1.'</b>'."Ürün Listesi".'</p>';
                    
                    $query_select_products = $conn->query("SELECT * FROM `order_items` WHERE `order_id`='$request_id'");
                    while($rowRequestProduct = $query_select_products->fetch_object()){
                        $product_package_id = $rowRequestProduct->product_package_id;
                        $customer_set_id = $rowRequestProduct->customer_set_id;
                        $product_info = $rowRequestProduct->product_info;
                        $type = $rowRequestProduct->type;
                        $quantity = $rowRequestProduct->quantity;
                        $price = ($quantity*$rowRequestProduct->discounting_price);
                        $total_price = "£".$rowRequestProduct->total_price;
                        $order_type = $rowRequestProduct->type;
                        $a = json_decode($product_info, true);
                        $html_text .= '<tr><div></div></td><td>'.$a["title"].'</td><td>'.$total_price.'</td></tr>';
                        
                        
                    }
                    
                    $html_text .= '<table style="width:100%"><tr><td width="75%"><b>'.'<table style="background-color:white;color:#ED1D24;">'.$request_table_title4.'</b></td><td width="25%">'."TOPLAM".'</td><td width="50%">'."".'</td><td width="25%">'.$request_total.'</td></tr></table>';
                    $html_text .='</body></html>';
                    
                    $order_status_title = GET_RESULT_MESSAGE($conn,'173',$app_language_id);
                    $order_detail_title = "Was Delivered ";
                    array_push($request_data_list, array($request_id,$request_address_id,$request_date,$request_first_last_name,$request_total,$request_address,strval($address_latitude),strval($address_longitude),$request_telephone,$request_payment_method,$request_product_list,$request_order_status,$request_order_mesaage,$customer_id,$order_status_title,$order_detail_title,$html_text,' £4.95'));
                }
                
                $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>$request_data_list,'part2'=>'Total Fee £14.85');
            }
            else
            {
                $json_output_request_array = array('token'=>$request_access_token_items_array,'part1'=>array(),'part2'=>'Total Fee £0.00');
            }
        }
        echo encrypt_output(json_encode($json_output_request_array));
        $conn->close();
    }
}
else {
    header("HTTP/1.0 403 Forbidden"); }
?>
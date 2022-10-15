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
        
        if (isset($_REQUEST['param1']))
        {
            $courier_id            = decrypt_output($_REQUEST['param1']);
            $app_language_id    = decrypt_output($_REQUEST['param2']);
            $device_type        = decrypt_output($_REQUEST['param3']);
            $query_text =addslashes("App\\Models\\Product");
            $query_text_package =addslashes("App\\Models\\ProductPackage");
            $request_data_list = array();
            
            //AccessToken Control
            $access_token = $_REQUEST['access_token'];
            
            $request_access_token_items_array = CONTROL_ACCESS_TOKEN($conn,$access_token,$courier_id);
            
            if ($request_access_token_items_array['status_code'] == '1')
            {
                $query_select_request = $conn->query("SELECT `order_histories`.`order_id`,`order_histories`.`order_status_id`,`orders`.`customer_id`,`orders`.`payment_type_id`,`orders`.`address`,`orders`.`address_id`,`orders`.`main_price`,`orders`.`discounting_price`,`orders`.`total_price`,`orders`.`order_type`,`orders`.`created_at` FROM `order_histories` INNER JOIN `orders` ON `order_histories`.`id`=`orders`.`id` WHERE `order_histories`.`order_status_id`=2 AND `orders`.`courier_id`='$courier_id' ORDER BY `order_histories`.`order_id` DESC"); // AND  DATE_FORMAT(date_added,'%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d')
                
                $first_row_control = 0;
                while($rowRequest = $query_select_request->fetch_object())
                {
                    $request_id = $rowRequest->order_id;
                    $customer_id = $rowRequest->customer_id;
                    $request_address_id = $rowRequest->address_id;
                    $request_order_status = $rowRequest->order_status_id;
                    $payment_type_id = $rowRequest->payment_type_id;
                    $request_date = date('d/m/Y H:s',strtotime($rowRequest->created_at));
                    $selected_user_data_list = GET_SELECTED_USER_DATA_LIST($conn,$customer_id);
                    if (count($selected_user_data_list) > 0)
                    {
                        $request_firstname =  $selected_user_data_list['name'];
                        $request_lastname  = $selected_user_data_list['surname'];
                        $request_telephone  = $selected_user_data_list['phone'];
                        $request_first_last_name = $request_firstname." ".$request_lastname;
                        
                    }
                    
                    
                    $request_first_last_name = $request_firstname." ".$request_lastname;
                    $request_total = "£".$rowRequest->total_price;
                    $request_address = $rowRequest->address;
                    
                    $request_address = trim($request_address);
                    $request_telephone = preg_replace('/\D+/', '', $request_telephone);
                    
                    // $request_payment_method = strip_tags($rowRequest->payment_method_id);
                    
                    //GET PRODUT LIST
                    $request_product_list = array();
                    $query_select_products = $conn->query("SELECT * FROM `order_items` WHERE `order_id`='$request_id'");
                    $first_row_control_product = 0;
                    while($rowRequestProduct = $query_select_products->fetch_object())
                    {
                        
                        $product_package_id = $rowRequestProduct->product_package_id;
                        $customer_set_id    = $rowRequestProduct->customer_set_id;
                        $product_info       = $rowRequestProduct->product_info;
                        $type               = $rowRequestProduct->type;
                        $quantity           = $rowRequestProduct->quantity;
                        $price              = ($quantity*$rowRequestProduct->discounting_price);
                        $total_price        = $rowRequestProduct->total_price." £ ";
                        $order_type         = $rowRequestProduct->type;
                        
                        $request_sets_item_list = array();
                        
                        $price=  number_format($price, 2)." £ ";
                        
                        if($order_type=="2")
                        {
                            $customer_set_id = $rowRequestProduct->customer_set_id;
                            $query_set_item  = $conn->query("SELECT * FROM `customer_set_items` WHERE `customer_set_id`='$customer_set_id'");
                            
                            while($rowRequestSet = $query_set_item->fetch_object())
                            {
                                $product_id         = $rowRequestSet->product_id;
                                $product_package_id = $rowRequestSet->product_package_id;
                                $quantity_new       = $rowRequestSet->quantity;
                                
                                $queryName = $conn->query("SELECT `products`.`id`,`products`.`category_id`,`descriptions`.`title`,`descriptions`.`description` FROM `products` INNER JOIN `descriptions` ON `products`.`id` =`descriptions`.`descriptionable_id` WHERE `descriptions`.`descriptionable_type`='$query_text' AND `descriptions`.`language_id`='1' and `products`.`id`='$product_id'");
                                
                                $num_rows = $queryName->num_rows;
                                if ($num_rows>0)
                                {
                                    $rowResult     = $queryName->fetch_object();
                                    $product_name  = $rowResult->title;
                                    
                                    $queryPackageInfo = $conn->query("SELECT `product_packages`.`discount_rate`,`product_packages`.`id`,`product_packages`.`price`,`descriptions`.`title` FROM `product_packages` INNER JOIN `descriptions` ON `product_packages`.`id` =`descriptions`.`descriptionable_id` WHERE `descriptions`.`descriptionable_type`='$query_text_package' AND `descriptions`.`language_id`='$app_language_id' AND `product_packages`.`product_id`='$product_id' AND `product_packages`.`id`='$product_package_id'");
                                    
                                    $num_rows_package = $queryPackageInfo->num_rows;
                                    if ($num_rows_package>0)
                                    {
                                        $rowResultPackage = $queryPackageInfo->fetch_object();
                                        $package_name     = $rowResultPackage->title;
                                    }
                                    
                                    $product_name = $product_name." ( ".$package_name." ) ";
                                    
                                }
                                array_push($request_sets_item_list, array($product_id,$product_package_id,$quantity_new,$product_name));
                            }
                        }
                         
                         if ($device_type =="I") 
                         {
                            $json_decode_request = json_decode($product_info, true);
                        
                            $product_title =  str_replace('Adet', "Quantity", $json_decode_request['title']);
                            $product_image = array();

                            $product_image =  $json_decode_request['image'];

                            // array_push($request_product_list, array($product_title,$quantity,$price,$total_price,strval($type),$product_image));
                            array_push($request_product_list, array($product_package_id,$customer_set_id,$product_title,$type,$quantity,$price,$total_price,$product_image,$request_sets_item_list));
                        }
                        else
                        {
                            array_push($request_product_list, array($product_package_id,$customer_set_id,$product_info,$type,$quantity,$price,$total_price,$order_type,$request_sets_item_list));
                        }
                    }
                    
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
                    
                    $request_order_mesaage = "Bu Yolda";
                    if ($request_order_status !=3)
                    {
                        $request_order_mesaage = "Address";
                    }
                    
                    $order_status_title = "Sipariş Durumu";
                    
                    array_push($request_data_list, array($request_id,$request_address_id,$request_date,$request_first_last_name,$request_total,$request_address,$address_latitude,$address_longitude,$request_telephone,strval($request_payment_method),$request_product_list,$request_order_status,$request_order_mesaage,$customer_id,$order_status_title));
                }
                
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
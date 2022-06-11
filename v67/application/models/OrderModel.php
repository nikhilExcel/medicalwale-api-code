<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class OrderModel extends CI_Model {

    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";
 
    public function check_auth_client() {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return json_output(401, array(
                'status' => 401,
                'message' => 'Unauthorized.'
            ));
        }
    }

    public function auth() {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorizations', TRUE);
        $q = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
        if ($q == "") {
            return json_output(401, array(
                'status' => 401,
                'message' => 'Unauthorized.'
            ));
        } else {
            if ($q->expired_at < date('Y-m-d H:i:s')) {
                return json_output(401, array(
                    'status' => 401,
                    'message' => 'Your session has been expired.'
                ));
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = '2030-11-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array(
                    'expired_at' => $expired_at,
                    'updated_at' => $updated_at
                ));
                return array(
                    'status' => 200,
                    'message' => 'Authorized.'
                );
            }
        }
    }

    public function pharmacy_booking_sendmail($user_email, $msg, $booking_id){
        
        $subject = "REGISTRATION INFORMATION";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Medicalwale.com <no-reply@medicalwale.com>' . "\r\n";
        $headers .= 'Cc: ' . "\r\n";
        $message  = '<div style="max-width: 700px;float: none;margin: 0px auto;">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
   <div id="styles_holder">
      <style>
         .ReadMsgBody { width: 100%; background-color: #ffffff; }
         .ExternalClass { width: 100%; background-color: #ffffff; }
         .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
         html { width: 100%; }
         body { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }
         table { border-spacing: 0; border-collapse: collapse; table-layout: fixed; margin:0 auto; }
         table table table { table-layout: auto; }
         img { display: block !important; }
         table td { border-collapse: collapse; }
         .yshortcuts a { border-bottom: none !important; }
         a { color: #1abc9c; text-decoration: none; }
         /*Responsive*/
         @media only screen and (max-width: 640px) {
         body { width: auto !important; }
         table[class="table-inner"] { width: 90% !important; }
         table[class="table-full"] { width: 100% !important; text-align: center !important; }
         /* Image */
         img[class="img1"] { width: 100% !important; height: auto !important; }
         }
         @media only screen and (max-width: 479px) {
         body { width: auto !important; }
         table[class="table-inner"] { width: 90% !important; }
         table[class="table-full"] { width: 100% !important; text-align: center !important; }
         /* image */
         img[class="img1"] { width: 100% !important; }
         }


      </style>
   </div>
  
   <div id="frame" class="ui-sortable">
      <table data-thumb="" data-module="header-bar" data-bgcolor="Main BG" align="center" width="100%" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class="">
         <tbody>
            <tr>
               <td data-border-top-color="Top Border" style="border-top:4px solid #049341;"></td>
            </tr>
            <tr>
               <td height="25"></td>
            </tr>
            <tr>
               <td align="center">
                  <table data-bgcolor="Container" width="600" class="table-inner" bgcolor="#ffffff" style="border-top-left-radius:5px;border-top-right-radius:5px;" border="0" align="center" cellpadding="0" cellspacing="0">
                     <tbody>
                        <tr>
                           <td align="center" style="border-bottom: 5px solid #049341;">
                              <table class="table-inner" width="520" border="0" align="center" cellpadding="0" cellspacing="0">
                                 <tbody>
                                    <tr>
                                       <td style="padding: 10px 0px;">
                                          <!--Logo-->
                                          <table class="table-full" border="0" align="left" cellpadding="0" cellspacing="0">
                                             <tbody>
                                                <tr>
                                                   <td align="center" style="line-height:0px;">
                                                      <img data-crop="false" mc:edit="quinn-box-1" style="display:block; line-height:0px; font-size:0px; border:0px;height: 70px;" src="http://medicalwale.com/img/email-logo.png" alt="logo"   >
                                                   </td>
                                                </tr>
                                             </tbody>
                                          </table>
                                          <!--End Logo-->
                                          <!--social-->
                                          <table border="0" align="right" cellpadding="0" cellspacing="0" class="table-full">
                                             <tbody>
                                                <tr>
                                                   <td height="15"></td>
                                                </tr>
                                                <tr>
                                                   <td align="center">
                                                      <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                         <tbody>
                                                            <tr>
                                                               <td align="center" style="">
                                                                   <font style="font-size:11px;line-height:16px" face="font-family: arial,sans-serif;" color="#666666">
                                    <b style="font-size: 12px;font-family: arial, sans-serif;"></b><br>
                                    </font>
                                    <font style="font-size:14px" face="HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue, Helvetica, Arial, sans-serif" color="#666666">
                                    Call Us: </font><font style="font-size:16px" face="HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica, Arial, sans-serif" color="#7db701"><strong>022-60123457</strong></font>
                                                               </td>
                                                            </tr>
                                                         </tbody>
                                                      </table>
                                                   </td>
                                                </tr>
                                             </tbody>
                                          </table>
                                          <!--End social-->
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
         </tbody>
      </table>
      
      
      <table data-thumb="" data-module="1-2-right" data-bgcolor="Main BG" width="100%" align="center" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class="">
         <tbody>
            <tr>
               <td align="center">
                  <table data-bgcolor="Container" bgcolor="#FFFFFF" class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="height: 250px;">
                     <tbody  style="background: url(https://medicalwale.com/img/mail_bg.jpg);background-size: cover;">
                        <tr>
                           <td height="20"></td>
                        </tr>
                        <tr>
                           <td align="center">
                              <table class="table-inner" width="570" border="0" align="center" cellpadding="0" cellspacing="0">
                                 <tbody >
                                    <tr>
                                       <td>
                                          <!-- img -->
                                          <table class="table-full" border="0" align="left" cellpadding="0" cellspacing="0" style="width: 320px;">
                                             <tbody>
                                                <tr>
                                                   <td align="left" style="padding-bottom: 10px;">
                                                       <p><font style="font-size:14px;color:#fff;line-height: 20px;" face="arial,sans-serif" >Welcome to </font><br>
                                       <font style="font-size:22px;color:#fff;" face="arial,sans-serif" ><span class="il">Medicalwale.com</span></font>
                                    </p>
                                    <p><font style="font-size:14px;color:#fff;line-height: 20px;" face="arial,sans-serif" >' .$msg . ' </font></p>
                                    <p><font style="font-size:14px;color:#fff;" face="arial,sans-serif" >For any clarification <br> Mail us on: <a href="mailto:partner@medicalwale.com" style="color:#fff">partner@medicalwale.com</a> </font></p>
                                 
                                                   </td>
                                                </tr>
                                             </tbody>
                                          </table>
                                          <table border="0" align="right" cellpadding="0" cellspacing="0" class="table-full" style="width: 250px;">
                                             <!-- Title -->
                                             <tbody>
                                                <tr>
                                                   <td>
                                                  <!--  <table style="background-color:rgb(249,246,246);border:0px solid rgb(213,210,210);width:100%;" cellpadding="0" cellspacing="0">
                                       <tbody>
                                       <tr>
                                             <td style="padding-left:20px;padding-top:10px;padding-bottom:10px;padding-right:10px;    background: #a8abaf;    text-align: center;" valign="top" align="left"><font style="font-size:16px" face="arial,sans-serif" color="#ffffff">Your Login Details</font></td>
                                          </tr>
                                          <tr>
                                             <td style="padding:18px 15px 4px;background: #fff;" align="left">
                                             <font style="font-size:14px;color: #656060;font-weight: 600;" face="arial,sans-serif">Link</font>
                                                <font style="font-size:14px;line-height:21px" face="arial,sans-serif" color="#333333">
                                                <a href="" target="_blank" style="color: #656060;text-decoration: none;"></a></font>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="padding:18px 15px 4px;background: #fff;" align="left">
                                             <font style="font-size:14px;color: #656060;font-weight: 600;" face="arial,sans-serif">Email:</font>
                                                <font style="font-size:14px;line-height:21px" face="arial,sans-serif" color="#333333">
                                                <a href="#" target="_blank" style="color: #656060;text-decoration: none;"></a></font>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="padding-bottom:18px;padding-top:4px;padding-left:15px;background: #fff;" align="left">
                                                <font style="font-size:14px;color: #656060;font-weight: 600;" face="arial,sans-serif">Password :</font>
                                                <font style="font-size:14px;line-height:21px;color: #656060" face="arial,sans-serif" ></font>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table> -->
                                                   </td>
                                                </tr>
                                                <!--End Title-->
                                           
                                               
                                                <!--Content-->
                                              <!--   <tr>
                                                   <td data-link-style="text-decoration:none; color:#1abc9c;" data-link-color="Content" data-size="Content" data-color="Content" mc:edit="quinn-box-25" align="left" style="font-family: Open Sans, Arial, sans-serif; font-size:14px; color:#fff; line-height:28px;">
                                                    <a href="" target="_blank"> <button type="button" style="width: 100%;margin-right: 5px;background: #3c98ed;font-size: 16px;font-weight: bold;color: #fff;font-family: Arial,Helvetica,sans-serif;padding: 6px 12px;font-size: 14px;font-weight: 400;line-height: 1.42857143;text-align: center;-ms-touch-action: manipulation;cursor: pointer;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;background-image: none;border: 1px solid transparent;cursor: pointer;">Login </button></a>
                                                     
                                                   </td>
                                                </tr> -->
                                                <!--End Content-->
                                             </tbody>
                                          </table>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td height="20"></td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
         </tbody>
      </table>
      

      
      <table data-thumb="" data-module="quote" data-bgcolor="Main BG" width="100%" align="center" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class="" >
         <tbody>
            <tr>
               <td data-bgcolor="Feature BG" align="center" bgcolor="#e7e7e7">
                  <table data-bgcolor="Feature inner" bgcolor="#fff" class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"> 
                     <tbody>
                        <tr>
                           <td align="center">
                              <table class="table-inner" width="520" border="0" align="center" cellpadding="0" cellspacing="0">
                                 <tbody>
                                    <tr>
                                       <td height="35"></td>
                                    </tr>
                                    <!-- intro -->
                                   
                                    <!-- end intro -->
                                    <tr>
                                       <td height="5"></td>
                                    </tr>
                                    <!-- Quote -->
                                    <tr>
                                      <!--  <td data-link-style="text-decoration:none; color:#4a4a4a;" data-link-color="Quote" data-size="Quote" data-color="Quote" mc:edit="quinn-box-56" align="center" style="font-family: century Gothic, arial, sans-serif; font-size:16px; color:#4a4a4a; font-weight:bold;">"Your Resume has been Shorlisted"</td> -->
                                    </tr>
                                    <!-- end Quote -->
                                                                       <tr>
                                       <td height="35"></td>
                                    </tr>
                                  
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
          
         </tbody>
      </table>
      
      
          

      <table data-thumb="" data-module="footer" data-bgcolor="Main BG" width="100%" bgcolor="#e7e7e7" border="0" align="center" cellpadding="0" cellspacing="0" class="">
         <tbody>
            
            <tr>
               <td data-bgcolor="Footer Bar" bgcolor="#191919" align="center" style="font-family: Open Sans, Arial, sans-serif; font-size:11px; color:#ffffff;background: #e7e7e7;">
                  <table class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                     <tbody>
                        <tr>
                           <td>
                              <!-- copyright -->
                              <table class="table-full" bgcolor="#e7e7e7" border="0" align="left" cellpadding="0" cellspacing="0" style="width: 100%;">
                                 <tbody>
                                    <tr>
                                       <td data-link-style="text-decoration:none; color:#ffffff;" data-link-color="Footer Link" data-size="Content" data-color="Footer Text" mc:edit="quinn-box-81" height="30" style="font-family: Open Sans, Arial, sans-serif; font-size:12px; color: #6d6d6d;line-height: 19px;text-align:center;    padding: 10px 0px;">
By using these service, you agree that you are bound by the Terms of Service.<br/>Copyright 2017 AEGIS HEALTH SOLUTIONS PVT. LTD. All rights reserved.
                                       </td>
                                    </tr>
                                    <tr>
                                       <td height="15"></td>
                                    </tr>
                                 </tbody>
                              </table>
                             
                             
                           </td>
                        </tr>
               
                     </tbody>
                  </table>
               </td>
            </tr>
    
         </tbody>
      </table>

   </div>
</div>';
        $sentmail = mail($user_email, $subject, $message, $headers);
        
    }
    
    public function address_list($user_id) {
        $query = $this->db->query("SELECT address_id,name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' order by address_id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $address_id = $row['address_id'];
                $name = $row['name'];
                $mobile = $row['mobile'];
                $address1 = $row['address1'];
                $address2 = $row['address2'];
                $landmark = $row['landmark'];
                $city = $row['city'];
                $state = $row['state'];
                $pincode = $row['pincode'];
                $resultpost[] = array(
                    "address_id" => $address_id,
                    "name" => $name,
                    "mobile" => $mobile,
                    'address1' => $address1,
                    'address2' => $address2,
                    'landmark' => $landmark,
                    'city' => $city,
                    'state' => $state,
                    'pincode' => $pincode
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function address_add($user_id, $name, $mobile, $pincode, $address1, $address2, $landmark, $city, $state) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $address_data = array(
            'user_id' => $user_id,
            'name' => $name,
            'address1' => $address1,
            'address2' => $address2,
            'landmark' => $landmark,
            'mobile' => $mobile,
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode,
            'date' => $created_at
        );
        $this->db->insert('user_address', $address_data);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function address_update($user_id, $address_id, $name, $address1, $address2, $landmark, $mobile, $city, $state, $pincode) {
        $query = $this->db->query("UPDATE `user_address` SET `name`='$name',`mobile`='$mobile',`address1`='$address1',`address2`='$address2',`landmark`='$landmark',`city`='$city',`state`='$state',`pincode`='$pincode' WHERE address_id='$address_id' and user_id='$user_id'");
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function address_delete($user_id, $address_id) {
        $query = $this->db->query("DELETE FROM `user_address` WHERE address_id='$address_id' AND user_id='$user_id'");
        return array(
            'status' => 201,
            'message' => 'success'
        );
    } 
    
    public function order_route($current_listing_id,$current_listing_name,$old_order_id,$user_id, $listing_id, $listing_type, $listing_name, $address_id, $payment_method, $order_product_id, $order_product_price, $order_product_quantity, $order_product_name, $delivery_charge, $order_product_img, $chat_id, $order_product_unit, $order_product_unit_value, $is_night_delivery,$lat,$lng,$invoice_no){
        if ($listing_type == 13) {
            date_default_timezone_set('Asia/Kolkata');
            $order_date = date('Y-m-d H:i:s');
            //$invoice_no = date("YmdHis");
            //$invoice_no= '20180528180409';
            $order_status = 'Awaiting Confirmation';
            $order_total = '0';
            $action_by = 'customer';
            $product_id = explode(",", $order_product_id);
            $product_quantity = explode(",", $order_product_quantity);
            $product_price = explode(",", $order_product_price);
            $cnt = count($product_id);
            for ($i = 0; $i < $cnt; $i++) {
                $order_total = $order_total + ($product_price[$i] * $product_quantity[$i]);
            }
            $query = $this->db->query("SELECT name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' and address_id='$address_id' limit 1");
            foreach ($query->result_array() as $row) {
                $name = $row['name'];
                $mobile = $row['mobile'];
                $address1 = $row['address1'];
                $address2 = $row['address2'];
                $landmark = $row['landmark'];
                $city = $row['city'];
                $state = $row['state'];
                $pincode = $row['pincode'];
            }
            $user_order = array(
                'user_id' => $user_id,
                'listing_id' => $listing_id,
                'listing_name' => $listing_name,
                'listing_type' => $listing_type,
                'invoice_no' => $invoice_no,
                'chat_id' => $chat_id,
                'lat' => $lat,
                'lng' => $lng,
                'address_id' => $address_id,
                'name' => $name,
                'mobile' => $mobile,
                'pincode' => $pincode,
                'address1' => $address1,
                'address2' => $address2,
                'landmark' => $landmark,
                'city' => $city,
                'state' => $state,
                'payment_method' => $payment_method,
                'order_total' => $order_total,
                'delivery_charge' => $delivery_charge,
                'order_date' => $order_date,
                'order_status' => $order_status,
                'action_by' => $action_by,
                'is_night_delivery' => $is_night_delivery
            );
            $this->db->insert('user_order', $user_order);
            $order_id = $this->db->insert_id();
            if($order_id>0){
                $update = $this->db->query("UPDATE `user_order` SET `updated_at`='$order_date',`order_status`='Order Cancelled',`cancel_reason`='',`action_by`='customer' WHERE order_id='$old_order_id'");
                
            }
            $sub_total = '0';
            $product_status = '';
            $product_status_type = '';
            $product_status_value = '';
            $product_order_status = 'Awaiting Confirmation';
            $product_id = explode(",", $order_product_id);
            $product_quantity = explode(",", $order_product_quantity);
            $product_price = explode(",", $order_product_price);
            $product_name = explode(",", $order_product_name);
            $product_img = explode(",", $order_product_img);
            $product_unit = explode(",", $order_product_unit);
            $product_unit_value = explode(",", $order_product_unit_value);
            $cnt = count($product_id);
            for ($i = 0; $i < $cnt; $i++) {
                $sub_total = $product_price[$i] * $product_quantity[$i];
                $product_order = array(
                    'order_id' => $order_id,
                    'product_name' => $product_name[$i],
                    'product_img' => str_replace('https://d2c8oti4is0ms3.cloudfront.net/images/product_images/', '', $product_img[$i]),
                    'product_id' => $product_id[$i],
                    'product_quantity' => $product_quantity[$i],
                    'product_price' => $product_price[$i],
                    'sub_total' => $sub_total,
                    'product_status' => $product_status,
                    'product_status_type' => $product_status_type,
                    'product_status_value' => $product_status_value,
                    'product_unit' => $product_unit[$i],
                    'product_unit_value' => $product_unit_value[$i],
                    'order_status' => $product_order_status
                );
                $this->db->insert('user_order_product', $product_order);
            }
        }
        //
        $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
        define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        
        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent) {
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url,"notifivation_image" => $img_url, "tag" => $tag, "notification_type" => "order", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
            if ($key_count == '1') {
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
                );
            }
            if ($key_count == '2') {
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
                );
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
        }
        if ($order_id > 0) {
            $order_date = date('j M Y h:i A', strtotime($order_date));
            $order_info = $this->db->select('token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $order_info->token_status;
            if ($token_status > 0) {
                $reg_id = $order_info->token;
                $agent = $order_info->agent;
                $msg = 'Rerouting: Your order no.'.$invoice_no.' of Rs.'.$order_total.' has been rerouted from '.$current_listing_name.' to '.$listing_name.'. We will update you on the order status shortly.';
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag = 'text';
                $key_count = '1';
                $title = 'Order Update';
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
            }
            if ($listing_type !== "31") {
                $old_partner_info = $this->db->select('token,token_status,phone,agent')->from('users')->where('id', $current_listing_id)->get()->row();
                $old_partner_token_status = $old_partner_info->token_status;
                if ($old_partner_token_status > 0) {
                    $partner_phone = $old_partner_info->phone;
                    $reg_id = $old_partner_info->token;
                    $agent = $old_partner_info->agent;
                    $msg = 'Rerouting: Your order no.'.$invoice_no.' of Rs.'.$order_total.' has been rerouted to '.$current_listing_name;
                    $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                    $tag = 'text';
                    $key_count = '2';
                    $title = 'Order Rerouted';
                    $pharmacy_notifications = array(
                        'listing_id' => $listing_id,
                        'order_id' => $order_id,
                        'title' => $title,
                        'msg' => $msg,
                        'image' => $img_url,
                        'notification_type' => 'order',
                        'order_status' => $order_status,
                        'order_date' => $order_date,
                        'invoice_no' => $invoice_no
                    );
                    $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
                    send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
                }
                
                $partner_info = $this->db->select('token,token_status,phone,agent')->from('users')->where('id', $listing_id)->get()->row();
                $partner_token_status = $partner_info->token_status;
                if ($partner_token_status > 0) {
                    $partner_phone = $partner_info->phone;
                    $reg_id = $partner_info->token;
                    $agent = $partner_info->agent;
                    $msg = 'Order Received: Order no.'.$invoice_no.' of Rs.'.$order_total.' has been received. Please confirm availability at the earliest.';
                    $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                    $tag = 'text';
                    $key_count = '2';
                    $title = 'New Order';
                    $pharmacy_notifications = array(
                        'listing_id' => $listing_id,
                        'order_id' => $order_id,
                        'title' => $title,
                        'msg' => $msg,
                        'image' => $img_url,
                        'notification_type' => 'order',
                        'order_status' => $order_status,
                        'order_date' => $order_date,
                        'invoice_no' => $invoice_no
                    );
                    $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
                    send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
                }
                $message = 'Order Received: Order no.'.$invoice_no.' of Rs.'.$order_total.' has been received. Please confirm availability at the earliest.';
                $post_data = array('From' => '02233721563', 'To' => $partner_phone, 'Body' => $message);
                $exotel_sid = "aegishealthsolutions";
                $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                $http_result = curl_exec($ch);
                curl_close($ch);
                //sms same to nyla,abdul, zaheer
                $message2 = 'There is new order in pharmacy store. Pharmacy Name-' . $listing_name . ', Pharmacy Mobile- ' . $mobile . ', Order Id-' . $order_id . ', Order Date-' . $order_date . '.';
                $post_data2 = array('From' => '02233721563', 'To' => '9619294702,7506908285', 'Body' => $message2);
                $exotel_sid2 = "aegishealthsolutions";
                $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
                $url2 = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
                $ch2 = curl_init();
                curl_setopt($ch2, CURLOPT_VERBOSE, 1);
                curl_setopt($ch2, CURLOPT_URL, $url2);
                curl_setopt($ch2, CURLOPT_POST, 1);
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch2, CURLOPT_FAILONERROR, 0);
                curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
                $http_result2 = curl_exec($ch2);
                curl_close($ch2);
            }
        }
        return array(
            'status' => 201,
            'message' => 'success',
            'order_id' => $invoice_no
        );
    }

    public function order_add($user_id, $listing_id, $listing_type, $listing_name, $address_id, $payment_method, $order_product_id, $order_product_price, $order_product_quantity, $order_product_name, $delivery_charge, $order_product_img, $chat_id, $product_unit, $product_unit_value, $is_night_delivery, $schedule_date, $device_type, $cancel_status, $thyrocare_cancel_status, $lead_id, $reference_id, $lat, $lng){
        $address1 = $mobile = $name = $phone = '';
        
        
        
        $address2 = '';
        $landmark = '';
        $city = '';
        $state = '';
        $pincode = '';
        if ($listing_type == 13) {
            date_default_timezone_set('Asia/Kolkata');
            $order_date = date('Y-m-d H:i:s');
            $invoice_no = date("YmdHis");
            $order_status = 'Awaiting Confirmation';
            $order_total = '0';
            $action_by = 'customer';
            $product_id = explode(",", $order_product_id);
            $product_quantity = explode(",", $order_product_quantity);
            $product_price = explode(",", $order_product_price);
            $cnt = count($product_id);
            for ($i = 0; $i < $cnt; $i++) {
                $order_total = $order_total + ($product_price[$i] * $product_quantity[$i]);
            }
            $query = $this->db->query("SELECT name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' and address_id='$address_id' limit 1");
            foreach ($query->result_array() as $row) {
                $name = $row['name'];
                $mobile = $row['mobile'];
                $address1 = $row['address1'];
                $address2 = $row['address2'];
                $landmark = $row['landmark'];
                $city = $row['city'];
                $state = $row['state'];
                $pincode = $row['pincode'];
            }
            $user_order = array(
                'user_id' => $user_id,
                'listing_id' => $listing_id,
                'listing_name' => $listing_name,
                'listing_type' => $listing_type,
                'invoice_no' => $invoice_no,
                'chat_id' => $chat_id,
                'lat' => $lat,
                'lng' => $lng,
                'address_id' => $address_id,
                'name' => $name,
                'mobile' => $mobile,
                'pincode' => $pincode,
                'address1' => $address1,
                'address2' => $address2,
                'landmark' => $landmark,
                'city' => $city,
                'state' => $state,
                'payment_method' => $payment_method,
                'order_total' => $order_total,
                'delivery_charge' => $delivery_charge,
                'order_date' => $order_date,
                'order_status' => $order_status,
                'action_by' => $action_by,
                'is_night_delivery' => $is_night_delivery
            );
            $this->db->insert('user_order', $user_order);
            $order_id = $this->db->insert_id();
            $sub_total = '0';
            $product_status = '';
            $product_status_type = '';
            $product_status_value = '';
            $product_order_status = 'Awaiting Confirmation';
            $product_id = explode(",", $order_product_id);
            $product_quantity = explode(",", $order_product_quantity);
            $product_price = explode(",", $order_product_price);
            $product_name = explode(",", $order_product_name);
            $product_img = explode(",", $order_product_img);
            $product_unit = explode(",", $product_unit);
            $product_unit_value = explode(",", $product_unit_value);
            $cnt = count($product_id);
            for ($i = 0; $i < $cnt; $i++) {
                $sub_total = $product_price[$i] * $product_quantity[$i];
                $product_order = array(
                    'order_id' => $order_id,
                    'product_name' => $product_name[$i],
                    'product_img' => str_replace('https://d2c8oti4is0ms3.cloudfront.net/images/product_images/', '', $product_img[$i]),
                    'product_id' => $product_id[$i],
                    'product_quantity' => $product_quantity[$i],
                    'product_price' => $product_price[$i],
                    'sub_total' => $sub_total,
                    'product_status' => $product_status,
                    'product_status_type' => $product_status_type,
                    'product_status_value' => $product_status_value,
                    'product_unit' => $product_unit[$i],
                    'product_unit_value' => $product_unit_value[$i],
                    'order_status' => $product_order_status
                );
                $this->db->insert('user_order_product', $product_order);
                
            }
            
            
        }
        if ($listing_type == 31) {
            $query = $this->db->query("SELECT * FROM `users` WHERE id='$user_id'")->row();
           
            date_default_timezone_set('Asia/Kolkata');
            $order_date = date('Y-m-d H:i:s');
            $invoice_no = date("YmdHis");
            $order_status = '';
            $order_total = '1';
            $action_by = 'customer';
            $user_id = $user_id;
            $listing_id = $listing_id;
            $listing_name = $listing_name;
            $listing_type = $listing_type;
            $invoice_no = $invoice_no;
    
               $name = $query ->name;
                 $phone = $query ->phone;
                 $agent = $query ->agent;
        
            
            
            $user_order = array(
                'user_id' => $user_id,
                'listing_id' => $listing_id,
                'order_type' => 'thyrocare',
                'listing_name' => $listing_name,
                'listing_type' => $listing_type,
                'invoice_no' => $invoice_no,
                'name' => $name,
                'mobile' => $phone,
                'order_total' => $order_total,
                'order_date' => $order_date,
                'order_status' => $order_status,
                'action_by' => $action_by
                
            );
            $this->db->insert('user_order', $user_order);
            $order_id = $this->db->insert_id();
            $product_order = array(
                'order_id' => $order_id,
                'schedule_date' => $schedule_date,
                'reference_id' => $reference_id,
                'device_type' => $device_type,
                'lead_id' => $lead_id,
                'reference_id' => $reference_id
            );
            $this->db->insert('user_order_thyrocare', $product_order);
        }
        
        //added by zak for missbelly
        if($listing_type == "37")
        {
             $query = $this->db->query("SELECT * FROM `users` WHERE id='$user_id'")->row();
           
            date_default_timezone_set('Asia/Kolkata');
            $order_date = date('Y-m-d H:i:s');
            $invoice_no = date("YmdHis");
            $order_status = 'Order Confirmed';
            $order_total = '1';
            $action_by = 'customer';
            $user_id = $user_id;
            $listing_id = $listing_id;
            $listing_name = $listing_name;
            $listing_type = $listing_type;
            $invoice_no = $invoice_no;
    
               $name = $query ->name;
                 $phone = $query ->phone;
                 $agent = $query ->agent;
        
            
            
            $user_order = array(
                'user_id' => $user_id,
                'listing_id' => $listing_id,
                'order_type' => 'miss_belly',
                'listing_name' => $listing_name,
                'listing_type' => $listing_type,
                'invoice_no' => $invoice_no,
                'name' => $name,
                'mobile' => $phone,
                'order_total' => $order_total,
                'order_date' => $order_date,
                'order_status' => $order_status,
                'action_by' => $action_by
                
            );
            $this->db->insert('user_order', $user_order);
            $order_id = $this->db->insert_id();
        }
        
        //Nursing Attendant order by SSAM PARIIHAR 26/05/2018
        if ($listing_type == 12) {
            date_default_timezone_set('Asia/Kolkata');
            $order_date = date('Y-m-d H:i:s');
            $invoice_no = date("YmdHis");
            $order_status = 'Awaiting Confirmation';
            $order_total = '0';
            $action_by = 'customer';
            // echo ($order_product_quantity);
            // echo ($order_product_id);
            // die();
            $product_id = explode(",", $order_product_id);
            $product_quantity = explode(",", $order_product_quantity);
            $product_price = explode(",", $order_product_price);
            $cnt = count($product_id);
              
            for ($i = 0; $i < $cnt; $i++) {
                
                $order_total = $order_total + ($product_price[$i] * $product_quantity[$i]);
            }
            if($address_id == "")
            {
                 $query = $this->db->query("SELECT name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' limit 1");
            }
            else
            {
            $query = $this->db->query("SELECT name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' and address_id='$address_id' limit 1");
            }
         
            //$query = $this->db->query("SELECT name FROM `users` WHERE id='$user_id'");
            // foreach ($query->result_array() as $row) {
            //     $name = $row['name'];
            // }
           // $name = "";
            // $mobile = "";
            // $pincode ="";
            // $address1 ="";
            // $address2 = "";
            // $city ="";
            // $state = "";
            // $pincode = "";
            // $landmark = "";
            foreach ($query->result_array() as $row) {
                $name = $row['name'];
                $mobile = $row['mobile'];
                $address1 = $row['address1'];
                $address2 = $row['address2'];
                $landmark = $row['landmark'];
                $city = $row['city'];
                $state = $row['state'];
                $pincode = $row['pincode'];
            }
            
            $user_order = array(
                'user_id' => $user_id,
                'listing_id' => $listing_id,
                'listing_name' => $listing_name,
                'listing_type' => $listing_type,
                'invoice_no' => $invoice_no,
                'chat_id' => $chat_id,
                'lat' => $lat,
                'lng' => $lng,
                'address_id' => $address_id,
                'name' => $name,
                'mobile' => $mobile,
                'pincode' => $pincode,
                'address1' => $address1,
                'address2' => $address2,
                'landmark' => $landmark,
                'city' => $city,
                'state' => $state,
                'payment_method' => $payment_method,
                'order_total' => $order_total,
                'delivery_charge' => $delivery_charge,
                'order_date' => $order_date,
                'order_status' => $order_status,
                'action_by' => $action_by,
                'is_night_delivery' => $is_night_delivery
            );
            $this->db->insert('user_order', $user_order);
            $order_id = $this->db->insert_id();
            $sub_total = '0';
            $product_status = '';
            $product_status_type = '';
            $product_status_value = '';
            $product_order_status = 'Awaiting Confirmation';
            $product_id = explode(",", $order_product_id);
            $product_quantity = explode(",", $order_product_quantity);
            $product_price = explode(",", $order_product_price);
            $product_name = explode(",", $order_product_name);
            $product_img = explode(",", $order_product_img);
            $product_unit = explode(",", $product_unit);
            $product_unit_value = explode(",", $product_unit_value);
            $cnt = count($product_id);
            for ($i = 0; $i < $cnt; $i++) {
                $sub_total = $product_price[$i] * $product_quantity[$i];
                $product_order = array(
                    'order_id' => $order_id,
                    'product_name' => $product_name[$i],
                    'product_img' => str_replace('https://d2c8oti4is0ms3.cloudfront.net/images/product_images/', '', $product_img[$i]),
                    'product_id' => $product_id[$i],
                    'product_quantity' => $product_quantity[$i],
                    'product_price' => $product_price[$i],
                    'sub_total' => $sub_total,
                    'product_status' => $product_status,
                    'product_status_type' => $product_status_type,
                    'product_status_value' => $product_status_value,
                    'product_unit' => $product_unit[$i],
                    'product_unit_value' => $product_unit_value[$i],
                    'order_status' => $product_order_status
                );
                $this->db->insert('user_order_product', $product_order);
            }
        }
        
        
        define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent) {
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url,"notifivation_image" => $img_url, "tag" => $tag, "notification_type" => "order", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
            if ($key_count == '1') {
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
                );
            }
            if ($key_count == '2') {
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
                );
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
        }
        if ($order_id > 0) {
            $order_date = date('j M Y h:i A', strtotime($order_date));
            $order_info = $this->db->select('phone,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $order_info->token_status;
            $customer_phone = $order_info->phone;
            if ($token_status > 0) {
                $reg_id = $order_info->token;
                $agent = $order_info->agent;
                $msg = 'Your order of Rs.'.$order_total.' has been initiated. Please keep the order no. '.$invoice_no.' for future reference.';
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag = 'text';
                $key_count = '1';
                $title = 'Order Placed';
                
                  //added by zak for all notification therough services
        //         $notification_array = array(
        //               'title' => $title,
        //               'msg'  => $msg,
        //               'img_url' => $img_url,
        //               'tag' => $tag,
        //               'order_status' => $order_status,
        //               'order_date' => $order_date,
        //               'order_id'   => $order_id,
        //               'post_id'  => "",
        //               'listing_id'  => "",
        //               'booking_id'  => "",
        //               'invoice_no' => $invoice_no,
        //               'user_id'  => $user_id,
        //               'notification_type'  => 'order',
        //               'notification_date'  => date('Y-m-d H:i:s')
                       
        //     );
        //  $this->db->insert('All_notification_Mobile', $notification_array);
        // //end 
                
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
            }
                $message = 'Your order of Rs.'.$order_total.' has been initiated. Please keep the order no. '.$invoice_no.' for future reference.';
                $post_data = array('From' => '02233721563', 'To' => $customer_phone, 'Body' => $message);
                $exotel_sid = "aegishealthsolutions";
                $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                $http_result = curl_exec($ch);
                curl_close($ch);
            
       
            if (trim($listing_type) != ("31" || "37")) {
             
                $partner_info = $this->db->select('token,token_status,phone,agent')->from('users')->where('id', $listing_id)->get()->row();
                // echo "select('token,token_status,phone,agent')->from('users')->where('id', $listing_id)->get()->row()";
                // print_r ($partner_info);
                // die();
                $partner_token_status = $partner_info->token_status;
                $partner_phone = "";
                if ($partner_token_status > 0) {
                    $partner_phone = $partner_info->phone;
                    $reg_id = $partner_info->token;
                    $agent = $partner_info->agent;
                    $msg = 'Order Received: Order no.'.$invoice_no.' of Rs.'.$order_total.' has been received. Please confirm availability at the earliest.';
                    $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                    $tag = 'text';
                    $key_count = '2';
                    $title = 'New Order';
                    //web notification starts
                    $pharmacy_notifications = array(
                        'listing_id' => $listing_id,
                        'order_id' => $order_id,
                        'title' => $title,
                        'msg' => $msg,
                        'image' => $img_url,
                        'notification_type' => 'order',
                        'order_status' => $order_status,
                        'order_date' => $order_date,
                        'invoice_no' => $invoice_no
                    );
                    //print_r($pharmacy_notifications); die();
                    $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
                    //web notification ends
                    send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
                }
                $message = 'Order Received: Order no.'.$invoice_no.' of Rs.'.$order_total.' has been received. Please confirm availability at the earliest.';
                $post_data = array('From' => '02233721563', 'To' => $partner_phone, 'Body' => $message);
                $exotel_sid = "aegishealthsolutions";
                $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                $http_result = curl_exec($ch);
                curl_close($ch);
                //sms same to nyla,abdul, zaheer
                $message2 = 'There is new order in pharmacy store. Name:'.$listing_name.',Mobile:'.$mobile.',Order No:'.$order_id.',Date:'.$order_date.'. Thank You';
                $post_data2 = array('From' => '02233721563', 'To' => '9619294702,7506908285', 'Body' => $message2);
                $exotel_sid2 = "aegishealthsolutions";
                $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
                $url2 = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
                $ch2 = curl_init();
                curl_setopt($ch2, CURLOPT_VERBOSE, 1);
                curl_setopt($ch2, CURLOPT_URL, $url2);
                curl_setopt($ch2, CURLOPT_POST, 1);
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch2, CURLOPT_FAILONERROR, 0);
                curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
                $http_result2 = curl_exec($ch2);
                curl_close($ch2);
            }
            
            
        }
        return array(
            'status' => 201,
            'message' => 'success',
            'order_id' => $invoice_no
        );
    }

    public function order_list($user_id, $listing_type) {
        if ($listing_type != '0') 
        {
          //  echo "select action_by,order_id,order_type,delivery_time,listing_id,cancel_reason,action_by,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status,rxId from user_order where user_id='$user_id' and listing_type='$listing_type' order by order_id desc";
            $query = $this->db->query("select action_by,order_id,order_type,delivery_time,listing_id,cancel_reason,action_by,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status,rxId from user_order where user_id='$user_id' and listing_type='$listing_type' order by order_id desc");
        } 
        else 
        {
            $query = $this->db->query("select action_by,order_id,order_type,delivery_time,listing_id,cancel_reason,action_by,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status,rxId from user_order where user_id='$user_id' order by order_id desc");
        }
        $count = $query->num_rows();
        if ($count > 0) { 
            foreach ($query->result_array() as $row) {
                $order_id = $row['order_id'];
                $order_type = $row['order_type'];
                $delivery_time = $row['delivery_time'];
                $listing_id = $row['listing_id'];
                $listing_name = $row['listing_name'];
                $listing_type = $row['listing_type'];
                $invoice_no = $row['invoice_no'];
                $chat_id = $row['chat_id'];
                $address_id = $row['address_id'];
                $name = $row['name'];
                $mobile = $row['mobile'];
                $pincode = $row['pincode'];
                $address1 = $row['address1'];
                $address2 = $row['address2'];
                $landmark = $row['landmark'];
                $city = $row['city'];
                $state = $row['state'];
                $action_by = $row['action_by'];
                $payment_method = $row['payment_method'];
                $order_date = $row['order_date'];
                $order_date = date('j M Y h:i A', strtotime($order_date));
                $delivery_charge = $row['delivery_charge'];
                $order_status = $row['order_status'];
                $order_type = $row['order_type'];
                $action_by = $row['action_by'];
                $rxId = $row['rxId'];
               //added by zak for maintain medlife cancel order 
                  $is_cancel = 'false';
                if($rxId != 'NULL')
                {
                    $query = $this->db->query("select orderState from medlife_order where rxID='$rxId' order by id desc");
                $medlife_order =  $query->row();
                $medlife_order_num =  $query->num_rows();
                // = $medlife_order->orderState;
                 //    $medlife_order= $this->db->select('orderState')->from('medlife_order')->where('rxID', $rxId)->get()->row();
                 if($medlife_order_num>0)
                 {
                     $medlife_status = $medlife_order->orderState;
                     $order_status = $medlife_order->orderState;
                     if($medlife_status == 'Received')
                     {
                         $is_cancel = 'true';
                     }
                     else
                     {
                         $is_cancel = 'false';
                     }
                 }
                 else
                 {
                     $is_cancel = 'false';
                 }
                }
                
                
                if ($action_by == 'vendor') {
                    $cancel_reason = $row['cancel_reason'];
                } else {
                    $cancel_reason = '';
                }
                $user_info = $this->db->query("SELECT name,phone FROM users WHERE id='$user_id'");
                $getuser_info = $user_info->row_array();
                $user_name = $getuser_info['name'];
                $user_mobile = $getuser_info['phone'];
                $product_resultpost = array();
                $prescription_result = array();
                if ($order_type == 'order') {
                    $order_total = '0';
                    $product_query = $this->db->query("select id as product_order_id,product_unit,product_unit_value,product_id,product_name,product_img,product_quantity,product_discount,product_price,sub_total,product_status,product_status_type,product_status_value,order_status from user_order_product where order_id='$order_id' order by product_order_id asc");
                    $product_count = $product_query->num_rows();
                    if ($product_count > 0) {
                        foreach ($product_query->result_array() as $product_row) {
                            $product_order_id = $product_row['product_order_id'];
                            $product_id = $product_row['product_id'];
                            $product_name = $product_row['product_name'];
                            $product_img = "https://d2c8oti4is0ms3.cloudfront.net/images/product_images/" . $product_row['product_img'];
                            $product_quantity = $product_row['product_quantity'];
                            $product_discount = $product_row['product_discount'];
                            $product_price = $product_row['product_price'];
                            $product_unit = $product_row['product_unit'];
                            $product_unit_value = $product_row['product_unit_value'];
                            $sub_total = $product_row['sub_total'];
                            $product_status = $product_row['product_status'];
                            $product_status_type = $product_row['product_status_type'];
                            $product_status_value = $product_row['product_status_value'];
                            $product_order_status = $product_row['order_status'];
                            $order_total = $order_total + ($product_quantity * $product_price);
                            $product_resultpost[] = array(
                                "product_order_id" => $product_order_id,
                                "product_id" => $product_id,
                                "product_name" => $product_name,
                                "product_img" => $product_img,
                                "product_quantity" => $product_quantity,
                                "product_price" => $product_price,
                                "product_unit" => $product_unit,
                                "product_unit_value" => $product_unit_value,
                                "product_discount" => $product_discount,
                                "sub_total" => $sub_total,
                                "product_status" => $product_status,
                                "product_status_type" => $product_status_type,
                                "product_status_value" => $product_status_value,
                                "product_order_status" => $product_order_status
                            );
                        }
                    } else {
                        $product_resultpost = array();
                    }
                } else {
                    $order_total = '0';
                    $product_query = $this->db->query("SELECT id as product_order_id, order_status,prescription_image FROM prescription_order_details WHERE order_id='$order_id' order by product_order_id asc");
                    $product_count = $product_query->num_rows();
                    if ($product_count > 0) {
                        foreach ($product_query->result_array() as $product_row) {
                            $product_order_id = $product_row['product_order_id'];
                            $product_id = $product_row['product_order_id'];
                            $product_name = '';
                            $prescription_image = '';
                            $product_img = $product_row['prescription_image'];
                            $images_1 = "";
                            if (strpos($product_img, '/') == true) {
                                 $images_1 = "https://d2c8oti4is0ms3.cloudfront.net/" . $product_img;
                            }
                            else
                            {
                                $images_1= "https://d2c8oti4is0ms3.cloudfront.net/images/prescription_images/" . $product_img;
                            }
                            
                            $product_quantity = '';
                            $product_price = '';
                            $sub_total = '';
                            $product_status = '';
                            $product_status_type = '';
                            $product_status_value = '';
                            $product_order_status = $product_row['order_status'];

                            $product_resultpost[] = array(
                                "product_order_id" => $product_order_id,
                                "product_id" => $product_id,
                                "product_name" => $product_name,
                                "product_img" => $images_1,
                                "product_quantity" => $product_quantity,
                                "product_price" => $product_price,
                                "product_discount" => '0',
                                "sub_total" => $sub_total,
                                "product_status" => $product_status,
                                "product_status_type" => $product_status_type,
                                "product_status_value" => $product_status_value,
                                "product_order_status" => $product_order_status
                            );
                        }
                        $prescription_query = $this->db->query("SELECT * FROM `prescription_order_list` where order_id='$order_id' order by id desc");
                        $prescription_count = $prescription_query->num_rows();
                        if ($prescription_count > 0) {
                            foreach ($prescription_query->result_array() as $prescription_row) {
                                $prescription_name = $prescription_row['prescription_name'];
                                $prescription_quantity = $prescription_row['prescription_quantity'];
                                $prescription_price = $prescription_row['prescription_price'];
                                $prescription_discount = $prescription_row['prescription_discount'];
                                $prescription_status = $prescription_row['prescription_status'];

                                $prescription_result[] = array(
                                    "prescription_name" => $prescription_name,
                                    "prescription_quantity" => $prescription_quantity,
                                    "prescription_price" => $prescription_price,
                                    "prescription_discount" => $prescription_discount,
                                    "prescription_status" => $prescription_status
                                );
                            }
                        }
                    }
                }

                $resultpost[] = array(
                    "order_id" => $order_id,
                    "delivery_time" => str_replace('null', '', $delivery_time),
                    "order_type" => $order_type,
                    "listing_id" => $listing_id,
                    "listing_name" => $listing_name,
                    "listing_type" => $listing_type,
                    "invoice_no" => $invoice_no,
                    "chat_id" => $chat_id,
                    "address_id" => $address_id,
                    "name" => $name,
                    "mobile" => $mobile,
                    "pincode" => $pincode,
                    "address1" => $address1,
                    "address2" => $address2,
                    "landmark" => $landmark,
                    "city" => $city,
                    "state" => $state,
                    "user_name" => $user_name,
                    "user_mobile" => $user_mobile,
                    "order_total" => $order_total,
                    "payment_method" => $payment_method,
                    "order_date" => $order_date,
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    "delivery_charge" => $delivery_charge,
                    "product_order" => $product_resultpost,
                    "prescription_order" => $prescription_result,
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
     public function order_list_v2($user_id, $listing_type) {
        if ($listing_type != '0') 
        {
          //  echo "select action_by,order_id,order_type,delivery_time,listing_id,cancel_reason,action_by,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status,rxId from user_order where user_id='$user_id' and listing_type='$listing_type' order by order_id desc";
            $query = $this->db->query("select action_by,order_id,order_type,delivery_time,listing_id,cancel_reason,action_by,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status,rxId from user_order where user_id='$user_id' and listing_type='$listing_type' group by invoice_no order by order_id desc");
        } 
        else 
        {
            $query = $this->db->query("select action_by,order_id,order_type,delivery_time,listing_id,cancel_reason,action_by,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status,rxId from user_order where user_id='$user_id' group by invoice_no order by order_id desc");
        }
        $count = $query->num_rows();
        if ($count > 0) { 
            foreach ($query->result_array() as $row) {
                $order_id = $row['order_id'];
                $order_type = $row['order_type'];
                $delivery_time = $row['delivery_time'];
                $listing_id = $row['listing_id'];
                $listing_name = $row['listing_name'];
                $listing_type = $row['listing_type'];
                $invoice_no = $row['invoice_no'];
                $chat_id = $row['chat_id'];
                $address_id = $row['address_id'];
                $name = $row['name'];
                $mobile = $row['mobile'];
                $pincode = $row['pincode'];
                $address1 = $row['address1'];
                $address2 = $row['address2'];
                $landmark = $row['landmark'];
                $city = $row['city'];
                $state = $row['state'];
                $action_by = $row['action_by'];
                $payment_method = $row['payment_method'];
                $order_date = $row['order_date'];
                $order_date = date('j M Y h:i A', strtotime($order_date));
                $delivery_charge = $row['delivery_charge'];
                $order_status = $row['order_status'];
                $order_type = $row['order_type'];
                $action_by = $row['action_by'];
                $rxId = $row['rxId'];
                $orderId = "";
               //added by zak for maintain medlife cancel order 
                  $is_cancel = 'false';
                if($rxId != 'NULL')
                {
                    $query = $this->db->query("select orderState,orderId from medlife_order where rxID='$rxId' order by id desc");
                $medlife_order =  $query->row();
                $medlife_order_num =  $query->num_rows();
                // = $medlife_order->orderState;
                 //    $medlife_order= $this->db->select('orderState')->from('medlife_order')->where('rxID', $rxId)->get()->row();
                 if($medlife_order_num>0)
                 {
                     $medlife_status = $medlife_order->orderState;
                     $order_status = $medlife_order->orderState;
                     $orderId = $medlife_order->orderId;
                     if($medlife_status == 'Received')
                     {
                         $is_cancel = 'true';
                     }
                     else
                     {
                         $is_cancel = 'false';
                     }
                 }
                 else
                 {
                     $is_cancel = 'false';
                 }
                }
                
                
                if ($action_by == 'vendor') {
                    $cancel_reason = $row['cancel_reason'];
                } else {
                    $cancel_reason = $row['cancel_reason'];
                }
                $user_info = $this->db->query("SELECT name,phone FROM users WHERE id='$user_id'");
                $getuser_info = $user_info->row_array();
                $user_name = $getuser_info['name'];
                $user_mobile = $getuser_info['phone'];
                $prescription_resultpost=array();
                $product_resultpost  = array();
                $prescription_result  = array();
                 $query1 = $this->db->query("select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where invoice_no='$invoice_no'");
                  $count1 = $query1->num_rows();
                  $order_total=0;
                  $sub_total_sum2=0;
                  $sub_total_sum1=0;
                  $sub_total_discount=0;
                  $sub_total_discount1=0;
                  if ($count1 > 0) {
            foreach ($query1->result_array() as $row1) {
                    $order_id1            = $row1['order_id'];
                    $product_query = $this->db->query("select id as product_order_id,product_id,product_discount,product_name,product_unit,product_unit_value,product_img,product_quantity,product_price,sub_total,product_status,product_status_type,product_status_value,order_status from user_order_product where order_id='$order_id1' order by product_order_id asc");
                    $product_count = $product_query->num_rows();
                    if ($product_count > 0) {
                        foreach ($product_query->result_array() as $product_row) {
                           
                            $product_order_id     = $product_row['product_order_id'];
                            $product_id           = $product_row['product_id'];
                            $product_name         = $product_row['product_name'];
                            $product_img          = $product_row['product_img'];
                            $product_price        = $product_row['product_price'];
                            $product_unit         = $product_row['product_unit'];
                            $product_unit_value   = $product_row['product_unit_value'];
                            $product_quantity     = $product_row['product_quantity'];
                            $product_discount     = $product_row['product_discount'];
                            $sub_total            = $product_row['sub_total'];
                            $product_status       = $product_row['product_status'];
                            $product_status_type  = $product_row['product_status_type'];
                            $product_status_value = $product_row['product_status_value'];
                            $product_order_status = $product_row['order_status'];
                            
                            $sub_total_sum1      += $product_price * $product_quantity;
                            $sub_total_discount +=$product_discount;
                            $product_resultpost[] = array(
                                "order_id" => $order_id1,
                                "product_order_id" => $product_order_id,
                                "product_id" => $product_id,
                                "product_name" => $product_name,
                                "product_img" => $product_img,
                                "product_quantity" => $product_quantity,
                                "product_price" => $product_price,
                                "product_unit" => $product_unit,
                                "product_unit_value" => $product_unit_value,
                                "product_discount" => $product_discount,
                                "sub_total" => $sub_total,
                                "product_status" => $product_status,
                                "product_status_type" => $product_status_type,
                                "product_status_value" => $product_status_value,
                                "product_order_status" => $product_order_status
                            );
                        }
                    }
              
                    $product_query1 = $this->db->query("SELECT id as product_order_id, order_status,prescription_image,prescription_image2,prescription_image3,prescription_image4,original_prescription FROM prescription_order_details WHERE order_id='$order_id1' order by product_order_id asc");
                    $product_count1 = $product_query1->num_rows();
                    if ($product_count1 > 0) {
                        foreach ($product_query1->result_array() as $product_row1) {
                            $product_order_id1     = $product_row1['product_order_id'];
                            $product_id1           = $product_row1['product_order_id'];
                            $product_name1         = '';
                            $product_img1          = '';
                            $product_img1          = $product_row1['prescription_image'];
                            $product_img2          = '';
                            $product_img2          = $product_row1['original_prescription'];
                            $product_quantity1     = '';
                            $product_price1        = '';
                            $sub_total1            = '';
                            $product_status1       = '';
                            $product_status_type1  = '';
                            $product_status_value1 = '';
                            $product_order_status1 = $product_row1['order_status'];
                         if(!empty($product_img1))
                           {
                            if (strpos($product_img1, '/') == true) {
                                $images_1 = "https://d2c8oti4is0ms3.cloudfront.net/" . $product_img1;
                           }
                           else
                           {
                               $images_1= "https://d2c8oti4is0ms3.cloudfront.net/images/prescription_images/" . $product_img1;
                           }
                           }
                           else
                           {
                               $images_1="";
                           }
                           
                           if(!empty($product_img2))
                           {
                            if (strpos($product_img2, '/') == true) {
                                $images_2 = "https://d2c8oti4is0ms3.cloudfront.net/" . $product_img2;
                           }
                           else
                           {
                               $images_2= "https://d2c8oti4is0ms3.cloudfront.net/images/prescription_images/" . $product_img2;
                           }
                        }
                        else
                        {
                            $images_2="";
                        }
                           
                            $prescription_resultpost[] = array(
                                "order_id" => $order_id1,
                                "product_order_id" => $product_order_id1,
                                "product_id" => $product_id1,
                                "product_name" => $product_name1,
                                "product_img" => $images_1,
                                "product_orginal_img" => $images_2,
                                "product_quantity" => $product_quantity1,
                                "product_price" => $product_price1,
                                "product_unit" => '',
                                "product_unit_value" => '',
                                "product_discount" => '0',
                                "sub_total" => $sub_total1,
                                "product_status" => $product_status1,
                                "product_status_type" => $product_status_type1,
                                "product_status_value" => $product_status_value1,
                                "product_order_status" => $product_order_status1
                            );
                        }
                        
                        
                        $prescription_query = $this->db->query("SELECT * FROM `prescription_order_list` where order_id='$order_id' order by id desc");
                        $prescription_count = $prescription_query->num_rows();
                        if ($prescription_count > 0) {
                            foreach ($prescription_query->result_array() as $prescription_row) {
                                $finalamt=$prescription_row['prescription_price']*$prescription_row['prescription_quantity'];
                                $sub_total_sum2+=$finalamt;
                                
                                $prescription_name     = $prescription_row['prescription_name'];
                                $prescription_quantity = $prescription_row['prescription_quantity'];
                                $prescription_price    = $prescription_row['prescription_price'];
                                $prescription_discount = $prescription_row['prescription_discount'];
                                $prescription_status   = $prescription_row['prescription_status'];
                               $sub_total_discount1 += $prescription_discount;
                                $prescription_result[] = array(
                                    "prescription_name" => $prescription_name,
                                    "prescription_quantity" => $prescription_quantity,
                                    "prescription_price" => $prescription_price,
                                    "prescription_discount" => $prescription_discount,
                                    "prescription_status" => $prescription_status
                                );
                                
                            }
                        }
                        
                    }
                
                   
            }
      }
$order_total=$sub_total_sum1+$sub_total_sum2;
$order_total_discount=$sub_total_discount+$sub_total_discount1;
if($order_total_discount=="")
{
    $order_total_discount=0;
}
else
{
    $order_total_discount;
}

if($order_status!="Awaiting Confirmation")
{
    $user_info_user = $this->db->query("SELECT medical_name,payment_type FROM medical_stores WHERE user_id='$listing_id' or pharmacy_branch_user_id='$listing_id'");
    $getuser_info_user = $user_info_user->row_array();    
   $listing_name=$getuser_info_user['medical_name'];
   if($getuser_info_user['payment_type']!=null || !empty($getuser_info_user['payment_type']))
   {
   $listing_paymode=$getuser_info_user['payment_type'];
   }
   else
   {
       $listing_paymode="Cash On Delivery";
   }
}
else
{
  
   $listing_paymode="Cash On Delivery";  
}
if($listing_type=="38")
{
   $listing_name="Medlife"; 
}
else
{
    $listing_name;
}
if($rxId == "")
{
    $rxId="";
}
else
{
    $rxId;
}

                $resultpost[] = array(
                    "order_id" => $order_id,
                    "medlife_order_id" => $orderId,
                    "delivery_time" => str_replace('null', '', $delivery_time),
                    "order_type" => $order_type,
                    "listing_id" => $listing_id,
                    "listing_name" => $listing_name,
                    "listing_type" => $listing_type,
                    "listing_payment_mode" => $listing_paymode,
                    "invoice_no" => $invoice_no,
                    "chat_id" => $chat_id,
                    "address_id" => $address_id,
                    "name" => $name,
                    "mobile" => $mobile,
                    "pincode" => $pincode,
                    "address1" => $address1,
                    "address2" => $address2,
                    "landmark" => $landmark,
                    "city" => $city,
                    "state" => $state,
                    "user_name" => $user_name,
                    "user_mobile" => $user_mobile,
                    "order_total" => $order_total,
                    "order_discount"=>$order_total_discount,
                    "payment_method" => $payment_method,
                    "order_date" => $order_date,
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    "delivery_charge" => $delivery_charge,
                    "product_order" => $product_resultpost,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result,
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    
    
     public function re_order_list_v2($user_id,$page) {
         
          
    $limit = 10;
    $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
         $start = ($page - 1) * $limit;
         $sql = "select action_by,order_id,order_type,delivery_time,listing_id,cancel_reason,action_by,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status,rxId from user_order where user_id='$user_id' and listing_type='13' and order_status='Order Delivered' group by invoice_no order by order_id desc LIMIT $start, $limit";
         
       $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) { 
            foreach ($query->result_array() as $row) {
                 $order_status = $row['order_status'];
               
                $order_id = $row['order_id'];
                $order_type = $row['order_type'];
                $delivery_time = $row['delivery_time'];
                $listing_id = $row['listing_id'];
                $listing_name = $row['listing_name'];
                $listing_type = $row['listing_type'];
                $invoice_no = $row['invoice_no'];
                $chat_id = $row['chat_id'];
                $address_id = $row['address_id'];
                $name = $row['name'];
                $mobile = $row['mobile'];
                $pincode = $row['pincode'];
                $address1 = $row['address1'];
                $address2 = $row['address2'];
                $landmark = $row['landmark'];
                $city = $row['city'];
                $state = $row['state'];
                $action_by = $row['action_by'];
                $payment_method = $row['payment_method'];
                $order_date = $row['order_date'];
                $order_date = date('j M Y h:i A', strtotime($order_date));
                $delivery_charge = $row['delivery_charge'];
               
                $order_type = $row['order_type'];
                $action_by = $row['action_by'];
                $rxId = $row['rxId'];
                $orderId = "";
               //added by zak for maintain medlife cancel order 
                  $is_cancel = 'false';
              /*  if($rxId != 'NULL')
                {
                    $query = $this->db->query("select orderState,orderId from medlife_order where rxID='$rxId' order by id desc");
                $medlife_order =  $query->row();
                $medlife_order_num =  $query->num_rows();
                // = $medlife_order->orderState;
                 //    $medlife_order= $this->db->select('orderState')->from('medlife_order')->where('rxID', $rxId)->get()->row();
                 if($medlife_order_num>0)
                 {
                     $medlife_status = $medlife_order->orderState;
                     $order_status = $medlife_order->orderState;
                     $orderId = $medlife_order->orderId;
                     if($medlife_status == 'Received')
                     {
                         $is_cancel = 'true';
                     }
                     else
                     {
                         $is_cancel = 'false';
                     }
                 }
                 else
                 {
                     $is_cancel = 'false';
                 }
                }
                */
                
                if ($action_by == 'vendor') {
                    $cancel_reason = $row['cancel_reason'];
                } else {
                    $cancel_reason = $row['cancel_reason'];
                }
                $user_info = $this->db->query("SELECT name,phone FROM users WHERE id='$user_id'");
                $getuser_info = $user_info->row_array();
                $user_name = $getuser_info['name'];
                $user_mobile = $getuser_info['phone'];
                $prescription_resultpost=array();
                $product_resultpost  = array();
                $prescription_result  = array();
                 $query1 = $this->db->query("select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where invoice_no='$invoice_no'");
                  $count1 = $query1->num_rows();
                  $order_total=0;
                  $sub_total_sum2=0;
                  $sub_total_sum1=0;
                  $sub_total_discount=0;
                  $sub_total_discount1=0;
                  if ($count1 > 0) {
            foreach ($query1->result_array() as $row1) {
                    $order_id1            = $row1['order_id'];
                    $product_query = $this->db->query("select id as product_order_id,product_id,product_discount,product_name,product_unit,product_unit_value,product_img,product_quantity,product_price,sub_total,product_status,product_status_type,product_status_value,order_status from user_order_product where order_id='$order_id1' order by product_order_id asc");
                    $product_count = $product_query->num_rows();
                    if ($product_count > 0) {
                        foreach ($product_query->result_array() as $product_row) {
                           
                            $product_order_id     = $product_row['product_order_id'];
                            $product_id           = $product_row['product_id'];
                            $product_name         = $product_row['product_name'];
                            $product_img          = $product_row['product_img'];
                            $product_price        = $product_row['product_price'];
                            $product_unit         = $product_row['product_unit'];
                            $product_unit_value   = $product_row['product_unit_value'];
                            $product_quantity     = $product_row['product_quantity'];
                            $product_discount     = $product_row['product_discount'];
                            $sub_total            = $product_row['sub_total'];
                            $product_status       = $product_row['product_status'];
                            $product_status_type  = $product_row['product_status_type'];
                            $product_status_value = $product_row['product_status_value'];
                            $product_order_status = $product_row['order_status'];
                            
                            $sub_total_sum1      += $product_price * $product_quantity;
                            $sub_total_discount +=$product_discount;
                            $product_resultpost[] = array(
                                "order_id" => $order_id1,
                                "product_order_id" => $product_order_id,
                                "product_id" => $product_id,
                                "product_name" => $product_name,
                                "product_img" => $product_img,
                                "product_quantity" => $product_quantity,
                                "product_price" => $product_price,
                                "product_unit" => $product_unit,
                                "product_unit_value" => $product_unit_value,
                                "product_discount" => $product_discount,
                                "sub_total" => $sub_total,
                                "product_status" => $product_status,
                                "product_status_type" => $product_status_type,
                                "product_status_value" => $product_status_value,
                                "product_order_status" => $product_order_status
                            );
                        }
                    }
              
                    $product_query1 = $this->db->query("SELECT id as product_order_id, order_status,prescription_image,prescription_image2,prescription_image3,prescription_image4,original_prescription FROM prescription_order_details WHERE order_id='$order_id1' order by product_order_id asc");
                    $product_count1 = $product_query1->num_rows();
                    if ($product_count1 > 0) {
                        foreach ($product_query1->result_array() as $product_row1) {
                            $product_order_id1     = $product_row1['product_order_id'];
                            $product_id1           = $product_row1['product_order_id'];
                            $product_name1         = '';
                            $product_img1          = '';
                            $product_img1          = $product_row1['prescription_image'];
                            $product_img2          = '';
                            $product_img2          = $product_row1['original_prescription'];
                            $product_quantity1     = '';
                            $product_price1        = '';
                            $sub_total1            = '';
                            $product_status1       = '';
                            $product_status_type1  = '';
                            $product_status_value1 = '';
                            $product_order_status1 = $product_row1['order_status'];
                         if(!empty($product_img1))
                           {
                            if (strpos($product_img1, '/') == true) {
                                $images_1 = "https://d2c8oti4is0ms3.cloudfront.net/" . $product_img1;
                           }
                           else
                           {
                               $images_1= "https://d2c8oti4is0ms3.cloudfront.net/images/prescription_images/" . $product_img1;
                           }
                           }
                           else
                           {
                               $images_1="";
                           }
                           
                           if(!empty($product_img2))
                           {
                            if (strpos($product_img2, '/') == true) {
                                $images_2 = "https://d2c8oti4is0ms3.cloudfront.net/" . $product_img2;
                           }
                           else
                           {
                               $images_2= "https://d2c8oti4is0ms3.cloudfront.net/images/prescription_images/" . $product_img2;
                           }
                        }
                        else
                        {
                            $images_2="";
                        }
                           
                            $prescription_resultpost[] = array(
                                "order_id" => $order_id1,
                                "product_order_id" => $product_order_id1,
                                "product_id" => $product_id1,
                                "product_name" => $product_name1,
                                "product_img" => $images_1,
                                "product_orginal_img" => $images_2,
                                "product_quantity" => $product_quantity1,
                                "product_price" => $product_price1,
                                "product_unit" => '',
                                "product_unit_value" => '',
                                "product_discount" => '0',
                                "sub_total" => $sub_total1,
                                "product_status" => $product_status1,
                                "product_status_type" => $product_status_type1,
                                "product_status_value" => $product_status_value1,
                                "product_order_status" => $product_order_status1
                            );
                        }
                        
                        
                        $prescription_query = $this->db->query("SELECT * FROM `prescription_order_list` where order_id='$order_id' order by id desc");
                        $prescription_count = $prescription_query->num_rows();
                        if ($prescription_count > 0) {
                            foreach ($prescription_query->result_array() as $prescription_row) {
                                $finalamt=$prescription_row['prescription_price']*$prescription_row['prescription_quantity'];
                                $sub_total_sum2+=$finalamt;
                                
                                $prescription_name     = $prescription_row['prescription_name'];
                                $prescription_quantity = $prescription_row['prescription_quantity'];
                                $prescription_price    = $prescription_row['prescription_price'];
                                $prescription_discount = $prescription_row['prescription_discount'];
                                $prescription_status   = $prescription_row['prescription_status'];
                               $sub_total_discount1 += $prescription_discount;
                                $prescription_result[] = array(
                                    "prescription_name" => $prescription_name,
                                    "prescription_quantity" => $prescription_quantity,
                                    "prescription_price" => $prescription_price,
                                    "prescription_discount" => $prescription_discount,
                                    "prescription_status" => $prescription_status
                                );
                                
                            }
                        }
                        
                    }
                
                   
            }
      }
$order_total=$sub_total_sum1+$sub_total_sum2;
$order_total_discount=$sub_total_discount+$sub_total_discount1;
if($order_total_discount=="")
{
    $order_total_discount=0;
}
else
{
    $order_total_discount;
}

if($order_status!="Awaiting Confirmation")
{
    $user_info_user = $this->db->query("SELECT medical_name,payment_type FROM medical_stores WHERE user_id='$listing_id' or pharmacy_branch_user_id='$listing_id'");
    $getuser_info_user = $user_info_user->row_array();    
   $listing_name=$getuser_info_user['medical_name'];
   if($getuser_info_user['payment_type']!=null || !empty($getuser_info_user['payment_type']))
   {
   $listing_paymode=$getuser_info_user['payment_type'];
   }
   else
   {
       $listing_paymode="Cash On Delivery";
   }
}
else
{
  
   $listing_paymode="Cash On Delivery";  
}

if($rxId == "")
{
    $rxId="";
}
else
{
    $rxId;
}

                $resultpost[] = array(
                    "order_id" => $order_id,
                    "medlife_order_id" => $orderId,
                    "delivery_time" => str_replace('null', '', $delivery_time),
                    "order_type" => $order_type,
                    "listing_id" => $listing_id,
                    "listing_name" => $listing_name,
                    "listing_type" => $listing_type,
                    "listing_payment_mode" => $listing_paymode,
                    "invoice_no" => $invoice_no,
                    "chat_id" => $chat_id,
                    "address_id" => $address_id,
                    "name" => $name,
                    "mobile" => $mobile,
                    "pincode" => $pincode,
                    "address1" => $address1,
                    "address2" => $address2,
                    "landmark" => $landmark,
                    "city" => $city,
                    "state" => $state,
                    "user_name" => $user_name,
                    "user_mobile" => $user_mobile,
                    "order_total" => $order_total,
                    "order_discount"=>$order_total_discount,
                    "payment_method" => $payment_method,
                    "order_date" => $order_date,
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    "delivery_charge" => $delivery_charge,
                    "product_order" => $product_resultpost,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result,
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel
                );
                
            }
        } else {
            $resultpost = array();
        }
         $sql2 = "select action_by,order_id,order_type,delivery_time,listing_id,cancel_reason,action_by,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status,rxId from user_order where user_id='$user_id' and listing_type='13' and order_status='Order Delivered' group by invoice_no order by order_id ";
         
       $query2 = $this->db->query($sql2);
        $count2 = $query2->num_rows();
        $final=array("status" => 200, "message" => "success", "count" => sizeof($resultpost),"final_count" => $count2, "data" => $resultpost);
        return $final;
    }

    public function prescription_add($user_id, $listing_id, $address_id, $listing_name, $listing_type, $chat_id, $payment_method, $delivery_charge, $is_night_delivery) {
        date_default_timezone_set('Asia/Kolkata');
        $order_date = date('Y-m-d H:i:s');
        $invoice_no = date("YmdHis");
        $order_status = 'Awaiting Confirmation';
        $action_by = 'customer';
        $product_order_status = 'Awaiting Confirmation';
       
        //echo "SELECT name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' and address_id='$address_id' limit 1";
        $query = $this->db->query("SELECT name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' and address_id='$address_id' limit 1");
        foreach ($query->result_array() as $row) {
            $name = $row['name'];
            $mobile = $row['mobile'];
            $address1 = $row['address1'];
            $address2 = $row['address2'];
            $landmark = $row['landmark'];
            $city = $row['city'];
            $state = $row['state'];
            $pincode = $row['pincode'];
        }
        $user_order = array(
            'user_id' => $user_id,
            'order_type' => 'prescription',
            'listing_id' => $listing_id,
            'listing_name' => $listing_name,
            'listing_type' => $listing_type,
            'invoice_no' => $invoice_no,
            'chat_id' => $chat_id,
            'address_id' => $address_id,
            'name' => $name,
            'mobile' => $mobile,
            'pincode' => $pincode,
            'address1' => $address1,
            'address2' => $address2,
            'landmark' => $landmark,
            'city' => $city,
            'state' => $state,
            'payment_method' => $payment_method,
            'order_total' => '0',
            'delivery_charge' => '0',
            'order_date' => $order_date,
            'order_status' => $order_status,
            'action_by' => $action_by,
            'delivery_charge' => $delivery_charge,
            'is_night_delivery' => $is_night_delivery
        );
        $this->db->insert('user_order', $user_order);
        $order_id = $this->db->insert_id();
        
        
        
        
        define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent) {
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url, "tag" => $tag,"notification_image" => $img_url,"notification_date" => $order_date, "notification_type" => "prescription", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
            if ($key_count == '1') {
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
                );
            }
            if ($key_count == '2') {
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
                );
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
           // print_r($result);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
        }
        if ($order_id > 0) {
        
            if($listing_type !='38')
            {
            $order_date = date('j M Y h:i A', strtotime($order_date));
            $order_info = $this->db->select('token, agent, token_status')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $order_info->token_status;
            if ($token_status > 0) {
                $reg_id = $order_info->token;
                $agent = $order_info->agent;
              //  $msg = 'Thanks uploading your prescription with ' . $listing_name;
               $msg = 'Thanks for placing your order with ' . $listing_name;
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag = 'text';
                $key_count = '1';
                $title = 'Order Placed';
                
                
        //          //added by zak for all notification therough services
                 $notification_array = array(
                       'title' => $title,
                       'msg'  => $msg,
                       'img_url' => $img_url,
                       'tag' => $tag,
                       'order_status' => $order_status,
                       'order_date' => $order_date,
                       'order_id'   => $order_id,
                       'post_id'  => "",
                       'listing_id'  => "",
                       'booking_id'  => "",
                       'invoice_no' => $invoice_no,
                       'user_id'  => $user_id,
                       'notification_type'  => 'prescription',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
             );
          $this->db->insert('All_notification_Mobile', $notification_array);
        // //end 
                
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
            }
            
            
            //$partner_info = $this->db->select('token, agent, token_status,phone')->from('users')->where('id', $listing_id)->get()->row();
            $partner_info = $this->db->query('SELECT token, agent, token_status,phone from users where id= '.$listing_id);
            $partner_token_status = $partner_info->num_rows();
            $partner_info = $partner_info->row();
            if ($partner_token_status > 0) {
                $partner_token_status = $partner_info->token_status;
                $partner_phone = $partner_info->phone;
                $reg_id = $partner_info->token;
                $agent = $partner_info->agent;
                $msg = 'You Have Received a New Prescription Order';
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag = 'text';
                $key_count = '2';
                $title = 'New Order';
                //web notification starts
                $pharmacy_notifications = array(
                    'listing_id' => $listing_id,
                    'order_id' => $order_id,
                    'title' => $title,
                    'msg' => $msg,
                    'image' => $img_url,
                    'notification_type' => 'prescription',
                    'order_status' => $order_status,
                    'order_date' => $order_date,
                    'invoice_no' => $invoice_no
                );
                $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
                //web notification ends
                
                
                
                
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
               if($listing_type != '38')
                //sms same as order
                {
                $message = 'You have received an order, kindly login to https://pharmacy.medicalwale.com';
                $post_data = array('From' => '02233721563', 'To' => $partner_phone, 'Body' => $message);
                $exotel_sid = "aegishealthsolutions";
                $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                $http_result = curl_exec($ch);
                curl_close($ch);
                //sms same to nyla,abdul, zaheer
                $message2 = 'There is new order in pharmacy store. Pharmacy Name-' . $listing_name . ', Pharmacy Mobile- ' . $mobile . ', Order Id-' . $order_id . ', Order Date-' . $order_date . '.';
                $post_data2 = array('From' => '02233721563', 'To' => '9619294702,7506908285', 'Body' => $message2);
                $exotel_sid2 = "aegishealthsolutions";
                $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
                $url2 = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
                $ch2 = curl_init();
                curl_setopt($ch2, CURLOPT_VERBOSE, 1);
                curl_setopt($ch2, CURLOPT_URL, $url2);
                curl_setopt($ch2, CURLOPT_POST, 1);
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch2, CURLOPT_FAILONERROR, 0);
                curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
                $http_result2 = curl_exec($ch2);
                curl_close($ch2);
                }
                
            }
            }
        }
        return $order_id;
    }

   function whatsapp($phone,$template_type,$whatsapp_body)
{
        $this->destinationPhone = $phone;
		$this->template_type =$template_type;
		$this->body =$whatsapp_body;

        $data = json_encode($this);
        
        $url = "https://whatsapp.creativemantraz.com/whatsapp/public/whatsapp";
       // $url=$u.$ur;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER,['User-ID:1','Authorizations:25iwFyq/LSO1U','Client-Service:frontend-client','Auth-Key:medicalwalerestapi','Content-Type:application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
}  

    //added by zak for quick book order upload prescription
   //start
    public function prescription_add_quickbook($user_id, $address_id, $chat_id, $payment_method, $delivery_charge, $is_night_delivery,$lat,$lng,$product_id,$is_profile,$listing_id,$night_owls) 
    {
        $this->load->model('PartnermnoModel');
        $return_data=array();
        // Added by swapnali on 20th sept 2k19 , to check availibility of mno delivery area
        if($night_owls == 1){
            $order_id = $pincode = "";
            $res = $this->PartnermnoModel->mno_available_delivery($address_id, $pincode);
            if($res['delivery_available'] == 0){
               $return_data['order_id'] = $order_id;
               $return_data['message'] = $res['message'];
               
               return $return_data;
            } 
        }
        
        $nightowlDetails = array();
        $listing_id_new = array();
        date_default_timezone_set('Asia/Kolkata');
        $order_date = date('Y-m-d H:i:s');
        $invoice_no = date("YmdHis");
        $order_status = 'Awaiting Confirmation';
        $action_by = 'customer';
        $product_order_status = 'Awaiting Confirmation';
        $phone = $mobile = $pincode = $state = $city = $landmark = $address2 = $address1 = $name = "";
        $delivery_charge = '0';
        $listing_type = '13';
        $mobile='';
        $radius = 5;
        $limit = 10;
        $start = 0;
       
        $pharmacy_listing_count = 0;  
        
        function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyCMHN__LczEziaYuKXQ-SKdiMfx5AiY66o&callback=initMap";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
            return $dist;
        }
       
      //  echo "SELECT name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' and address_id='$address_id' limit 1";
        $query = $this->db->query("SELECT name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' and address_id='$address_id' limit 1");
        foreach ($query->result_array() as $row) {
            // print_r($row); die();
            $name = $row['name'];
            $mobile = $row['mobile'];
            $address1 = $row['address1'];
            $address2 = $row['address2'];
            $landmark = $row['landmark'];
            $city = $row['city'];
            $state = $row['state'];
            $pincode = $row['pincode'];
        }
        
        if($night_owls == 0){
            
        $order_deliver_by = '';
        
        if($is_profile != 'yes')
            {
                
            $sql = sprintf("SELECT `id`, `user_id`,`medical_name`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND discount > 9 and online_offline !='Offline' HAVING distance < '%s' ORDER BY distance LIMIT 20", ($lat), ($lng), ($lat), ($radius));
          
            $query = $this->db->query($sql);
            $count = $query->num_rows();
           
            
            if($count>0){
                       foreach ($query->result_array() as $mrow) {
                         //  echo 'id'.$mrow['user_id'];
                         //  echo 'name'.$mrow['medical_name'];
                               $listing_id_new[] = $mrow['user_id'];
                               $listing_name[] = $mrow['medical_name'];
            }
    
            $listing_id =  implode(',', $listing_id_new);
           // $listing_name = implode(',',$listing_name);
          
              if(count($listing_id_new) > 1)
            {
                if($is_profile!= 'yes')
                {
                  $listing_name_insert="Instant Order";    
                 
                }
                else
                {
                    $listing_name_insert="Favourite Pharmacy"; 
                }
            }
            else if(count($listing_id_new) == 1)
            {
                $query12 = $this->db->query("SELECT `medical_name` FROM medical_stores WHERE user_id = '$listing_id' or pharmacy_branch_user_id='$listing_id'"); 
                $m1row2 = $query12->row_array();
                $listing_name_insert=$m1row2['medical_name'];
            } 
            
            
            }
            }
        else
        {
          $listing_id =  json_decode($listing_id,TRUE);
         // print_r($listing_id);
       //   echo count($listing_id);
              for($i = 0; $i < count($listing_id); $i++)
            {
                $check_id = $listing_id[$i];
                  $query1 = $this->db->query("SELECT `id`, `user_id`,`medical_name` FROM medical_stores WHERE is_approval='1' AND is_active='1' AND user_id = '$check_id' or pharmacy_branch_user_id='$check_id'");
     /* echo "SELECT `id`, `user_id`,`medical_name` FROM medical_stores WHERE is_approval='1' AND is_active='1' AND user_id = '$check_id'";
      die;*/
        //$query1 = $this->db->query($sql1);
        $count1 = $query1->num_rows();
        if($count1>0){
                   foreach ($query1->result_array() as $m1row) {
                       
                           $listing_id_new[] = $m1row['user_id'];
                           $listing_name[] = $m1row['medical_name'];
                        }
          
            }
        }
        //print_r($listing_id_new);
          $listing_id =  implode(',', $listing_id_new);
          //$listing_name = implode(',',$listing_name);
          
            if(count($listing_id_new) > 1)
        {
            if($is_profile!= 'yes')
            {
              $listing_name_insert="Instant Order";
            }
            else
            {
              
              $listing_name_insert="Favourite Pharmacy";
            }
        }
        else if(count($listing_id_new) == 1)
        {
            $query12 = $this->db->query("SELECT `medical_name` FROM medical_stores WHERE user_id = '$listing_id' or pharmacy_branch_user_id='$listing_id'"); 
            $m1row2 = $query12->row_array();
            $listing_name_insert=$m1row2['medical_name'];
        } 
          
        }
        } else {
            
            // a1
            // night owls
            $radius = 10;
            $delivery_charge = '0';
            $listing_type = '44';
            $listing_id = '';
            $listing_name_insert = '';
            $order_deliver_by = 'mno';
        }
       
        $user_order = array(
            'user_id' => $user_id,
            'order_type' => 'prescription',
            'listing_id' => $listing_id,
            'listing_name' => $listing_name_insert,
            'listing_type' => $listing_type,
            'invoice_no' => $invoice_no,
            'chat_id' => $chat_id,
            'address_id' => $address_id,
            'name' => $name,
            'mobile' => $mobile,
            'pincode' => $pincode,
            'address1' => $address1,
            'address2' => $address2,
            'landmark' => $landmark,
            'city' => $city,
            'state' => $state,
            'payment_method' => $payment_method,
            'order_total' => '0',
            'order_date' => $order_date,
            'order_status' => $order_status,
            'action_by' => $action_by,
            'lat'=>$lat,
            'lng'=>$lng,
            'delivery_charge' => $delivery_charge,
            'is_night_delivery' => $is_night_delivery,
            'order_deliver_by' => $order_deliver_by
            
        );
        
        
        $this->db->insert('user_order', $user_order);
        $order_id = $this->db->insert_id();
        
        // a2
        
       
        if($night_owls > 0){
           $nightowlDetails = $this->PartnermnoModel->assign_mno($user_id,$invoice_no,$lat,$lng); 
        }
        
       
         define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent) {
            
          // echo $key_count; echo "<br>";
           
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url, "tag" => $tag,"notification_image" => $img_url,"notification_date" => $order_date, "notification_type" => "prescription", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
            if ($key_count == '1') {
               
                $headers = array(
                   // GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
                );
            }
            if ($key_count == '3') {
               // $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
               $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                  $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "icon" => $img_url, "tag" => $tag, "notification_image" => $img_url,"notification_date" => $order_date, "notification_type" => "prescription","click_action" => "https://vendor.sandbox.medicalwale.com/pharmacy/dashboard/pharmacy_notifications", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
             
            );
               
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AAAASZs-aSM:APA91bEQ0SZFS7YnRRK95O2Jp_PdzVC8t_osgDK0UdWXeQRo06fEedknj2b26yfX_YPkLq85h0lc3hUBlnSSPD1qvfikzFYiHd8tlt7BeuOg3f5WRwQxmz19WbEGbZEX894zZEF5iRO5'
                );
            }
            if ($key_count == '2') {
               // $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
               $fields = array(
                'to' => $reg_id,
                'priority' => "high",
               $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "body" => $msg, "icon" => $img_url, "tag" => $tag, "notification_image" => $img_url,"notification_date" => $order_date, "notification_type" => "prescription", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
               
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AAAASZs-aSM:APA91bEQ0SZFS7YnRRK95O2Jp_PdzVC8t_osgDK0UdWXeQRo06fEedknj2b26yfX_YPkLq85h0lc3hUBlnSSPD1qvfikzFYiHd8tlt7BeuOg3f5WRwQxmz19WbEGbZEX894zZEF5iRO5'
                );
            }
              if ($key_count == '4') {
               // $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
               
               $fields = array(
                'to' => $reg_id,
                'priority' => "high",
               $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "body" => $msg, "icon" => $img_url, "tag" => $tag, "notification_image" => $img_url,"notification_date" => $order_date, "notification_type" => "prescription", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name,"sound" => "https://medicalwale.s3.amazonaws.com/images/Sound/Vendor_Panel.mp3")
            );
              // print_r($fields);
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AAAASZs-aSM:APA91bEQ0SZFS7YnRRK95O2Jp_PdzVC8t_osgDK0UdWXeQRo06fEedknj2b26yfX_YPkLq85h0lc3hUBlnSSPD1qvfikzFYiHd8tlt7BeuOg3f5WRwQxmz19WbEGbZEX894zZEF5iRO5'
              // $agent === 'android' ? 'Authorization: key=AIzaSyD2RUGDhVmm2X9BNuLCvrV_tPu6wMMS3Zg' : 'Authorization: key=AIzaSyCyZI_Kn7HcGdeL227UiVsyCWvGB-2JoRQ'
                );
                // print_r($headers);
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
           //print_r($result);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
        }
       
       
      
       
        if ($order_id > 0 ) {
            // a3
           if($night_owls > 0){
                $listing_name = 'night owls';
               // $listing_id_new = '';
           }else {
               if(count($listing_id_new) > 1)
               {
                $listing_name = count($listing_id_new)." Pharmacies One of them will be confirm it shortly";
               }
               else
               {
                   $listing_name = implode(',',$listing_name);
                 
               }
           }
           
           
           
            $order_date = date('j M Y h:i A', strtotime($order_date));
            $order_info = $this->db->select('name, email, token, agent, token_status,phone')->from('users')->where('id', $user_id)->get()->row();
            $order_info_count = $this->db->select('email, token, agent, token_status,phone')->from('users')->where('id', $user_id)->get()->num_rows();
            if($order_info_count>0)
            {
             $token_status = $order_info->token_status;
            if ($token_status > 0) {
                $reg_id = $order_info->token;
                $user_name = $order_info->name;
                $agent = $order_info->agent;
                $user_email = $order_info->email;
                 $phone = $order_info->phone;
          //     $msg = 'Thanks uploading your prescription with ' . $listing_name;
                 $msg = 'Thanks for placing order with ' . $listing_name;
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag = 'text';
                $key_count = '1';
                $title = 'Order Placed';
                
        //           //added by zak for all notification therough services
                 $notification_array = array(
                       'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                    'tag' => $tag,
                       'order_status' => $order_status,
                      'order_date' => $order_date,
                      'order_id'   => $invoice_no,
                      'post_id'  => "",
                      'listing_id'  => "",
                     'booking_id'  => "",
                      'invoice_no' => $invoice_no,
                      'user_id'  => $user_id,
                      'notification_type'  => 'prescription',
                      'notification_date'  => date('Y-m-d H:i:s')
                       
            );
        $this->db->insert('All_notification_Mobile', $notification_array);
        //end 
        
        $action_by_status = "Customer";
        $orderStatus = "Order placed by ".$user_name;
        $order_status = $this->OrderModel->update_status($invoice_no,$orderStatus, $action_by_status);
        
        send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
                    if(!empty($user_email))
                     {
                        $this->pharmacy_booking_sendmail($user_email, $msg, $invoice_no);
                     }
//        print_r($listing_id_new); die();
           $pharmacy_listing_count=count($listing_id_new);
          
              
               if($pharmacy_listing_count==1)
             {
                 $message2 = 'Thanks for placing your order with ' . $listing_name . ', Order Id-' . $invoice_no . ' -Team Medicalwale Any Enquiry please contact on +91 9619146163';
                 $post_data2 = array('From' => '02233721563', 'To' => $phone, 'Body' => $message2);
                 $exotel_sid2 = "aegishealthsolutions";
                 $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
                 $url2 = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
                 $ch2 = curl_init();
                 curl_setopt($ch2, CURLOPT_VERBOSE, 1);
                 curl_setopt($ch2, CURLOPT_URL, $url2);
                 curl_setopt($ch2, CURLOPT_POST, 1);
                 curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
                 curl_setopt($ch2, CURLOPT_FAILONERROR, 0);
                 curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
                 curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
                 $http_result2 = curl_exec($ch2);
                 curl_close($ch2); 
             }
             
            //  a4
             else if($night_owls > 0){
                 $message2 = 'Thanks for placing your order with night owls , Order Id-' . $invoice_no . 'We will asign night owl to you as soon as possible.';
                 $post_data2 = array('From' => '02233721563', 'To' => $phone, 'Body' => $message2);
                 $exotel_sid2 = "aegishealthsolutions";
                 $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
                 $url2 = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
                 $ch2 = curl_init();
                 curl_setopt($ch2, CURLOPT_VERBOSE, 1);
                 curl_setopt($ch2, CURLOPT_URL, $url2);
                 curl_setopt($ch2, CURLOPT_POST, 1);
                 curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
                 curl_setopt($ch2, CURLOPT_FAILONERROR, 0);
                 curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
                 curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
                 $http_result2 = curl_exec($ch2);
                 curl_close($ch2);  
             } 
             elseif($pharmacy_listing_count > 1)
             {
                 $message2 = 'Thanks for placing your order with ' . $pharmacy_listing_count . ' Pharmacies , Order Id-' . $invoice_no . ' -Team Medicalwale Any Enquiry please contact on +91 9619146163';
                 $post_data2 = array('From' => '02233721563', 'To' => $phone, 'Body' => $message2);
                 $exotel_sid2 = "aegishealthsolutions";
                 $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
                 $url2 = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
                 $ch2 = curl_init();
                 curl_setopt($ch2, CURLOPT_VERBOSE, 1);
                 curl_setopt($ch2, CURLOPT_URL, $url2);
                 curl_setopt($ch2, CURLOPT_POST, 1);
                 curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
                 curl_setopt($ch2, CURLOPT_FAILONERROR, 0);
                 curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
                 curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
                 $http_result2 = curl_exec($ch2);
                 curl_close($ch2);  
             }
                
               
            
           
             }
            }
            //$partner_info = $this->db->select('token, agent, token_status,phone')->from('users')->where('id', $listing_id)->get()->row();
           
            $listing_id =  explode(',', $listing_id);
      //  $listing_name = explode(',',$listing_name);
    //   a5 -> not for nightowl
          if($night_owls == 0){
              
          
           for($i = 0 ; $i<count($listing_id); $i++)
             {           
             $partner_info = $this->db->query('SELECT web_token, token,agent, token_status,phone from users where id= '.$listing_id[$i]);
             //echo 'SELECT web_token, agent, token_status,phone from users where id= '.$listing_id[$i];
            $partner_token_status = $partner_info->num_rows();
            $partner_info = $partner_info->row();
            if ($partner_token_status > 0) {
                $partner_token_status = $partner_info->token_status;
                $partner_phone = $partner_info->phone;
                $reg_id = $partner_info->web_token;
                $agent = $partner_info->agent;
                //$msg = 'You Have Received a New Order kindly Confirm it First';
                $msg='Kindly confirm and accept the new order';
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag = 'text';
                $key_count = '2';
                $title = 'New Order';
                //web notification starts
                $pharmacy_notifications = array(
                    'listing_id' => $listing_id[$i],
                    'order_id' => $order_id,
                    'title' => $title,
                    'msg' => $msg,
                    'image' => $img_url,
                    'notification_type' => 'prescription',
                    'order_status' => $order_status,
                    'order_date' => $order_date,
                    'invoice_no' => $invoice_no
                );
                $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
                //web notification ends
                
                   //whatsapp start//
                   $mobile=array('9967119932','7021327803');
               foreach($mobile as $phone){
                   $pharmacy_whatsapp_notifications=array();
                $mobile='91'.$phone;
                $temp_type="all";
                $whatsapp_msg="You have received an order. Kindly accept the order to get Customer Details.";
               // $this->whatsapp($mobile,$temp_type,$whatsapp_msg);
                
                 $pharmacy_whatsapp_notifications = array(
                    'listing_id' => $listing_id[$i],
                    'type' => "pharmacy",
                    'template_name' => $mobile,
                    'text' => $whatsapp_msg
                );
               
                //$this->db->insert('whatsapp_notifications', $pharmacy_whatsapp_notifications);
               }
                //whatsapp end//
                
                
                
                //echo $key_count; echo "partner";
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, '4', $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
                
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
                
                
                
                // app notification start
                $partner_token_status1 = $partner_info->token_status;
                $partner_phone1 = $partner_info->phone;
                $reg_id1 = $partner_info->token;
                $agent1 = $partner_info->agent;
                //$msg1 = 'You Have Received a New Order kindly Confirm it First';
                $msg1='Kindly confirm and accept the new order';
                $img_url1 = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag1 = 'text';
                $key_count1 = '3';
                $title1 = 'New Order';
                //web notification starts
                $pharmacy_notifications1 = array(
                    'listing_id' => $listing_id[$i],
                    'order_id' => $order_id,
                    'title' => $title1,
                    'msg' => $msg1,
                    'image' => $img_url1,
                    'notification_type' => 'prescription',
                    'order_status' => $order_status,
                    'order_date' => $order_date,
                    'invoice_no' => $invoice_no
                );
                send_gcm_notify($title1, $reg_id1, $msg1, $img_url1, $tag1, $key_count1, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent1);
                // app notification end 
                
                // echo $key_count; echo "partner _ app";
            
                //sms same as order
                $message = 'You have received an order, kindly login to https://pharmacy.medicalwale.com';
                $post_data = array('From' => '02233721563', 'To' => $partner_phone, 'Body' => $message);
                $exotel_sid = "aegishealthsolutions";
                $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                $http_result = curl_exec($ch);
                curl_close($ch);
                // //sms same to nyla,abdul, zaheer
                // $message2 = 'There is new order in pharmacy store. Pharmacy Name-' . $listing_name[$i] . ', Pharmacy Mobile- ' . $mobile . ', Order Id-' . $order_id . ', Order Date-' . $order_date . '.';
                // $post_data2 = array('From' => '02233721563', 'To' => '9619294702,7506908285', 'Body' => $message2);
                // $exotel_sid2 = "aegishealthsolutions";
                // $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
                // $url2 = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
                // $ch2 = curl_init();
                // curl_setopt($ch2, CURLOPT_VERBOSE, 1);
                // curl_setopt($ch2, CURLOPT_URL, $url2);
                // curl_setopt($ch2, CURLOPT_POST, 1);
                // curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
                // curl_setopt($ch2, CURLOPT_FAILONERROR, 0);
                // curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
                // curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
                // $http_result2 = curl_exec($ch2);
                // curl_close($ch2);
            }
             }
          }
            
        }
        $return_data['order_id'] = $order_id;
        $return_data['message'] = '';
       
        return $return_data;
        // return $order_id;
 
}
   //end
   
 
    
       
       
     
   
  
    
   // added by dhaval for quick book from stack to order medicines 
    
     public function order_from_stack($user_id,$address_id, $payment_method, $delivery_charges , $is_night_delivery, $lat, $lng, $product_details,$is_profile,$listing_id,$night_owls)
       {
           $return_data = array();
           $order_deliver_by = '';
           $name = "";
          // $mobile ="9158209205";
        $this->load->model('PartnermnoModel');
        
        
        $return_data=array();
        // Added by swapnali on 20th sept 2k19 , to check availibility of mno delivery area
        if($night_owls == 1){
            $invoice_no = $pincode = "";
            $res = $this->PartnermnoModel->mno_available_delivery($address_id, $pincode);
            if($res['delivery_available'] == 0){
               $return_data['invoice_no'] = $invoice_no;
               $return_data['message'] = $res['message'];
               
               return $return_data;
            } 
        }
        
        
         date_default_timezone_set('Asia/Kolkata');
        $order_date = date('Y-m-d H:i:s');
        $invoice_no = date("YmdHis");
        $order_status = 'Awaiting Confirmation';
        $action_by = 'customer';
        $product_order_status = 'Awaiting Confirmation';
        //print_r($product_details);
        $chat_id = 'user'.$user_id;
          $product_details1 = json_decode($product_details,TRUE);
         
          $prescription_data = $product_details1['prescrption_data'];
          $medicine_product_data = $product_details1['medicine_product_data'];
          
        
      //    print_r($product_details['medicine_product_data']);
        $p_count = count($product_details1['prescrption_data']);
        $m_count = count($product_details1['medicine_product_data']);
        $listing_type = '13';
        
         $radius = 5;
        $limit = 10;
        $start = 0;
       
        $pharmacy_listing_count =  count($listing_id);    
        function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyCMHN__LczEziaYuKXQ-SKdiMfx5AiY66o&callback=initMap";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
            return $dist;
        }
        
        //FOR ADDRESS FROM ADDRESS ID BY ZAK
         $query = $this->db->query("SELECT name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' and address_id='$address_id' limit 1");
        foreach ($query->result_array() as $row) {
            $name = $row['name'];
            $mobile = $row['mobile'];
            $address1 = $row['address1'];
            $address2 = $row['address2'];
            $landmark = $row['landmark'];
            $city = $row['city'];
            $state = $row['state'];
            $pincode = $row['pincode'];
        }
        // echo $name ; die();
        //END
        
        //added for notification send  for placing order
        
         define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent) {
            
          // echo $key_count; echo "<br>";
           
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url, "tag" => $tag,"notification_image" => $img_url,"notification_date" => $order_date, "notification_type" => "prescription", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
            
            if ($key_count == '1') {
               
                $headers = array(
                   // GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
                );
            }
            if ($key_count == '3') {
               // $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
               $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                  $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "icon" => $img_url, "tag" => $tag, "notification_image" => $img_url,"notification_date" => $order_date, "notification_type" => "prescription","click_action" => "https://vendor.sandbox.medicalwale.com/pharmacy/dashboard/pharmacy_notifications", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
             
            );
               
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AAAASZs-aSM:APA91bEQ0SZFS7YnRRK95O2Jp_PdzVC8t_osgDK0UdWXeQRo06fEedknj2b26yfX_YPkLq85h0lc3hUBlnSSPD1qvfikzFYiHd8tlt7BeuOg3f5WRwQxmz19WbEGbZEX894zZEF5iRO5'
                );
            }
            if ($key_count == '2') {
               // $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
               
               $fields = array(
                'to' => $reg_id,
                'priority' => "high",
               $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "body" => $msg, "icon" => $img_url, "tag" => $tag, "notification_image" => $img_url,"notification_date" => $order_date, "notification_type" => "prescription", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
               
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AAAASZs-aSM:APA91bEQ0SZFS7YnRRK95O2Jp_PdzVC8t_osgDK0UdWXeQRo06fEedknj2b26yfX_YPkLq85h0lc3hUBlnSSPD1qvfikzFYiHd8tlt7BeuOg3f5WRwQxmz19WbEGbZEX894zZEF5iRO5'
               //$agent === 'android' ? 'Authorization: key=AIzaSyANRr1UMlTQrPN_eZ0BaD70_aq4kIRJ4k4' : 'Authorization: key=AIzaSyANRr1UMlTQrPN_eZ0BaD70_aq4kIRJ4k4'
                );
            }
             if ($key_count == '4') {
               // $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
               
               $fields = array(
                'to' => $reg_id,
                'priority' => "high",
               $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "body" => $msg, "icon" => $img_url, "tag" => $tag, "notification_image" => $img_url,"notification_date" => $order_date, "notification_type" => "prescription", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name,"sound" => "https://medicalwale.s3.amazonaws.com/images/Sound/Vendor_Panel.mp3")
            );
              // print_r($fields);
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AAAASZs-aSM:APA91bEQ0SZFS7YnRRK95O2Jp_PdzVC8t_osgDK0UdWXeQRo06fEedknj2b26yfX_YPkLq85h0lc3hUBlnSSPD1qvfikzFYiHd8tlt7BeuOg3f5WRwQxmz19WbEGbZEX894zZEF5iRO5'
               //$agent === 'android' ? 'Authorization: key=AIzaSyD2RUGDhVmm2X9BNuLCvrV_tPu6wMMS3Zg' : 'Authorization: key=AIzaSyCyZI_Kn7HcGdeL227UiVsyCWvGB-2JoRQ'
                );
                // print_r($headers);
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
        //   print_r($result);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
          //   die;
        }
       
        
        //end
        
        //added for generate order id for common added product
        
        
         if($night_owls == 0){
             $order_deliver_by = '';
        if($is_profile != 'yes')
        {
             $sql = sprintf("SELECT `id`, `user_id`,`medical_name`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND discount > 9 and online_offline !='Offline' HAVING distance < '%s' ORDER BY distance LIMIT 20", ($lat), ($lng), ($lat), ($radius));
          
            $query = $this->db->query($sql);
            $count = $query->num_rows();
            if($count>0){
                       foreach ($query->result_array() as $mrow) {
                         //  echo 'id'.$mrow['user_id'];
                         //  echo 'name'.$mrow['medical_name'];
                               $listing_id_new[] = $mrow['user_id'];
                               $listing_name[] = $mrow['medical_name'];
            }
             $pharmacy_listing_count =  count($listing_id_new);
           $listing_id =  implode(',', $listing_id_new);
            
    
            $listing_name = implode(',',$listing_name);
           
            
              if(count($listing_id_new) > 1)
            {
                if($is_profile!= 'yes')
                {
                  $listing_name_insert="Instant Order";  
                }
                else
                {
                  
                  $listing_name_insert="Favourite Pharmacy";
                }
            }
            else if(count($listing_id_new) == 1)
            {
                $query12 = $this->db->query("SELECT `medical_name` FROM medical_stores WHERE user_id = '$listing_id' or pharmacy_branch_user_id='$listing_id'"); 
                $m1row2 = $query12->row_array();
                $listing_name_insert=$m1row2['medical_name'];
            }
           
            }
        }
        else
        {
          $listing_id =  json_decode($listing_id,TRUE);
         //print_r($listing_id);
         //echo count($listing_id);
          $pharmacy_listing_count =  count($listing_id);
          $listing_id_new=array();
          $listing_name=array();
          $user="";
              for($i = 0; $i < count($listing_id); $i++)
            {
                 $check_id = $listing_id[$i];
              
                  $query1 = $this->db->query("SELECT `id`, `user_id`,`medical_name`,pharmacy_branch_user_id FROM medical_stores WHERE is_approval='1' AND is_active='1' AND user_id = '$check_id' or pharmacy_branch_user_id='$check_id'");
     
                 $count1 = $query1->num_rows();
      
               if($count1 > 0){
                  
                  $m1row = $query1->row_array();
                     
                                 if($m1row['pharmacy_branch_user_id']!=0)
                                 {
                                     $user= $m1row['pharmacy_branch_user_id'];
                                 }
                                 else
                                 {
                                    $user= $m1row['user_id'];
                                 }
                                   $listing_id_new[] =$user; 
                                   $listing_name[] = $m1row['medical_name'];
                           
                    }
                  
        }
        
        //echo $listing_name;
       
            $listing_id =  implode(',', $listing_id_new);
            
            $listing_name = implode(',',$listing_name);
       
            if(count($listing_id_new) > 1)
        {
            if($is_profile!= 'yes')
                {
                  $listing_name_insert="Instant Order";  
                }
                else
                {
                  
                  $listing_name_insert="Favourite Pharmacy";
                }
           
        }
        else if(count($listing_id_new) == 1 or count($listing_id_new) == 0)
        {
            $query12 = $this->db->query("SELECT `medical_name` FROM medical_stores WHERE user_id = '$listing_id' or pharmacy_branch_user_id='$listing_id'"); 
            $m1row2 = $query12->row_array();
            $listing_name_insert=$m1row2['medical_name'];
              
        }
            
        }
        
         } else {
             $order_deliver_by = 'mno';
             // b1
             
             $listing_name = $this->db->query("SELECT `name` FROM `users` WHERE `id` = '$user_id'")->row_array(); 
             $listing_name_insert = $listing_name['name'];
             $listing_type = '44';
         }
      
    
        if($p_count > 0 )
        {
        $user_order = array(
            'user_id' => $user_id,
            'order_type' => 'prescription',
            'listing_id' => $listing_id,
            'listing_name' => $listing_name_insert,
            'listing_type' => $listing_type,
            'invoice_no' => $invoice_no,
            'chat_id' => $chat_id,
            'address_id' => $address_id,
            'name' => $name,
            'mobile' => $mobile,
            'pincode' => $pincode,
            'address1' => $address1,
            'address2' => $address2,
            'landmark' => $landmark,
            'city' => $city,
            'state' => $state,
            'lat'=>$lat,
            'lng'=>$lng,
            'payment_method' => $payment_method,
            'order_total' => '0',
            'delivery_charge' => '0',
            'order_date' => $order_date,
            'order_status' => $order_status,
            'action_by' => $action_by,
            'is_night_delivery' => $is_night_delivery,
            'order_deliver_by' => $order_deliver_by
            
        );
      
        $this->db->insert('user_order', $user_order);
        $order_id = $this->db->insert_id();
        
        // b2
        $listing_name1 = $this->db->query("SELECT `name` FROM `users` WHERE `id` = '$user_id'")->row_array(); 
        $listing_name_insert1 = $listing_name1['name'];
        
        $action_by_status = "Customer";
        $orderStatus = "Order placed by ".$listing_name_insert1;
        $order_status = $this->OrderModel->update_status( $invoice_no,$orderStatus, $action_by_status);
        
        if($night_owls > 0){
           $nightowlDetails = $this->PartnermnoModel->assign_mno($user_id,$invoice_no,$lat,$lng); 
        }

        //END
        }
        else
        {
          $order_id=0;  
        }
        
        
        //FOR PRESCRIBED ORDER AND ADD INTO PRESCPTION_ORDER_DETAILS TABLE
        for($i = 0 ; $i < $p_count ; $i++)
          {
              $pre_id   = $prescription_data[$i]['product_id'];
              $pre_type = $prescription_data[$i]['prescription_type'];
              
              
               $query = $this->db->query("SELECT * FROM `user_card_order` WHERE product_id='$pre_id' AND user_id = '$user_id'");
             //echo "SELECT * FROM `user_card_order` WHERE product_id='$pre_id' AND user_id = '$user_id'";
               foreach ($query->result_array() as $prow) {
                         
                  $prescription_url = $prow['prescription_url']; 
                  $offline_prescription = $prow['offline_prescription'];
                  $original_prescription = $prow['original_prescription'];
                  $description = $prow['description'];
                         if($prescription_url != '' && $prescription_url != null)
                         {
                         $prescription_name = str_replace("https://s3.amazonaws.com/medicalwale/images/prescription_images/","",$prescription_url);
                         }
                         else
                         {
                        $prescription_name = str_replace("https://s3.amazonaws.com/medicalwale/images/prescription_images/","",$offline_prescription);
                         } 
               }
               
                  date_default_timezone_set('Asia/Calcutta');
                    //    $invoice_no = date("YmdHis");
                        $order_status = 'Awaiting Confirmation';
                       // $actual_image_name  = $query_status->prescription_link;
                        $this->db->query("INSERT INTO `prescription_order_details`(`order_id`, `prescription_image`,`order_status`, `uni_id`,`description`,`original_prescription`) VALUES ('$order_id', '$prescription_name','$order_status', '$invoice_no',' $description','$original_prescription')");
               
         }    
         
         //addded for send gcm notification and text messagess to user and vendor
 
         
        
         //end
         //inserrt order entry
         //print_r($listing_id);
         if($m_count > 0 )
         {
         $user_order = array(
            'user_id' => $user_id,
            'order_type' => 'order',
            'listing_id' => $listing_id,
            'listing_name' => $listing_name_insert,
            'listing_type' => $listing_type,
            'invoice_no' => $invoice_no,
            'chat_id' => $chat_id,
            'address_id' => $address_id,
            'name' => $name,
            'mobile' => $mobile,
            'pincode' => $pincode,
            'address1' => $address1,
            'address2' => $address2,
            'landmark' => $landmark,
            'city' => $city,
            'state' => $state,
            'payment_method' => $payment_method,
            'order_total' => '0',
            'delivery_charge' => '0',
            'lat'=>$lat,
            'lng'=>$lng,
            'order_date' => $order_date,
            'order_status' => $order_status,
            'action_by' => $action_by,
            'is_night_delivery' => $is_night_delivery,
             'order_deliver_by' => $order_deliver_by
            
        );
        $this->db->insert('user_order', $user_order);
        $order_id1 = $this->db->insert_id();
        
        
        // b3
        $listing_name1 = $this->db->query("SELECT `name` FROM `users` WHERE `id` = '$user_id'")->row_array(); 
        $listing_name_insert1 = $listing_name1['name'];
        $action_by_status = "Customer";
        $orderStatus = "Order placed by ".$listing_name_insert1;
        $order_status = $this->OrderModel->update_status($invoice_no,$orderStatus, $action_by_status);
        
        
        if($night_owls > 0){
           $nightowlDetails = $this->PartnermnoModel->assign_mno($user_id,$invoice_no,$lat,$lng); 
        }
        
}
else
{
    $order_id1=0;
}
// print_r($user_order); die();
        for($j = 0 ; $j < $m_count ; $j++)
          {
              $pro_id = $medicine_product_data[$j]['product_id'];
            //  echo 'test'.$pro_id;
              $query = $this->db->query("SELECT * FROM `user_card_order` WHERE product_id = '$pro_id' AND user_id = '$user_id'");
         //     echo "SELECT * FROM `user_card_order` WHERE product_id='$pro_id' AND user_id = '$user_id'";
               foreach ($query->result_array() as $mrow) {
                         
                            $product_id = $mrow['product_id']; 
                           $product_quantity = $mrow['quantity'];
        
               }
               
               
               
                  $query = $this->db->query("SELECT product_name,product_price,image FROM `product` WHERE id='$pro_id'");
               foreach ($query->result_array() as $mrow) {
                         
                            $product_id = $pro_id; 
                        //   $product_quantity = $row['quantity'];
                           $product_price = $mrow['product_price'];
                           $product_name = $mrow['product_name'];
                           $product_img = $mrow['image'];
        
               }
            //  echo 'product_id'.$pro_id;
              
          //  date_default_timezone_set('Asia/Kolkata');
           // $order_date = date('Y-m-d H:i:s');
         //   $invoice_no = date("YmdHis");
            $order_status = 'Awaiting Confirmation';
            $order_total = '0';
            $action_by = 'customer';
            // $product_id = $medicine_product_data[$j]['product_id'];
            // $product_quantity = explode(",", $order_product_quantity);
         //   $product_price = explode(",", $order_product_price);
           // $cnt = count($product_id);
            // for ($i = 0; $i < $cnt; $i++) {
                $order_total = $order_total + ($product_price * $product_quantity);
            //}
           
           
            $sub_total = '0';
            $product_status = '';
            $product_status_type = '';
            $product_status_value = '';
            $product_order_status = 'Awaiting Confirmation';
            // $product_id = explode(",", $order_product_id);
            // $product_quantity = explode(",", $order_product_quantity);
            // $product_price = explode(",", $order_product_price);
            // $product_name = explode(",", $order_product_name);
            // $product_img = explode(",", $order_product_img);
            $product_unit = '';
            $product_unit_value = '';
            // $cnt = count($product_id);
            // for ($i = 0; $i < $cnt; $i++) {
                $sub_total = $product_price * $product_quantity;
                $product_order = array(
                    'order_id' => $order_id1,
                    'product_name' => $product_name,
                    'product_img' => str_replace('https://d2c8oti4is0ms3.cloudfront.net/images/product_images/', '', $product_img),
                    'product_id' => $product_id,
                    'product_quantity' => $product_quantity,
                    'product_price' => $product_price,
                    'sub_total' => $sub_total,
                    'product_status' => $product_status,
                    'product_status_type' => $product_status_type,
                    'product_status_value' => $product_status_value,
                    'product_unit' => $product_unit,
                    'product_unit_value' => $product_unit_value,
                    'order_status' => $product_order_status
                );
                $this->db->insert('user_order_product', $product_order);
                
          //  }
            
          }
         
           //addded for send gcm notification and text messagess to user and vendor
           
            
           
            
         
            //$partner_info = $this->db->select('token, agent, token_status,phone')->from('users')->where('id', $listing_id)->get()->row();
           
            $listing_id =  explode(',', $listing_id);
       // $listing_name = explode(',',$listing_name);
       
       
    //   b4 -> no nightowls
          if($night_owls == 0){
              
           //print_r($listing_id);
          
             
           
               //$listing_id =  explode(',', $listing_id);
              for($i = 0 ; $i < count($listing_id); $i++)
             { 
                 
            $partner_info = $this->db->query("SELECT token, agent, web_token,token_status,phone from users where id= '$listing_id[$i]'");
            // echo "SELECT web_token, agent, token_status,phone from users where id= '$listing_id[$i]'";
            $partner_token_status = $partner_info->num_rows();
            $partner_info = $partner_info->row();
            if ($partner_token_status > 0) {
                $partner_token_status = $partner_info->token_status;
                $partner_phone = $partner_info->phone;
                $reg_id = $partner_info->token;
                $reg_id_web = $partner_info->web_token;
                $agent = $partner_info->agent;
                $msg = 'Kindly confirm and accept the new order';
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag = 'text';
                $key_count = '2';
                $title = 'New Order';
                //web notification starts
                
               
                $pharmacy_notifications = array(
                    'listing_id' => $listing_id[$i],
                    'order_id' => $order_id1,
                    'title' => $title,
                    'msg' => $msg,
                    'image' => $img_url,
                    'notification_type' => 'prescription',
                    'order_status' => $order_status,
                    'order_date' => $order_date,
                    'invoice_no' => $invoice_no
                );
                $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
               
                //whatsapp start//
                $mobile='91'.$partner_phone;
                $temp_type="all";
                $whatsapp_msg="You have received an order. Kindly accept the order to get Customer Details.";
               // $this->whatsapp($mobile,$temp_type,$whatsapp_msg);
                
                 $pharmacy_whatsapp_notifications = array(
                    'listing_id' => $listing_id[$i],
                    'type' => "pharmacy",
                    'template_name' => "",
                    'text' => $whatsapp_msg
                );
               // $this->db->insert('whatsapp_notifications', $pharmacy_whatsapp_notifications);
               
                //whatsapp end//
                
                
               
               
               
                  $notification_array = array(
                      'title' => $title,
                     'msg'  => $msg,
                       'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => $order_status,
                      'order_date' => $order_date,
                       'order_id'   => $order_id1,
                      'post_id'  => "",
                     'listing_id'  =>  "",
                      'booking_id'  => "",
                     'invoice_no' => $invoice_no,
                     'user_id'  => $listing_id[$i],
                      'notification_type'  => 'prescription',
                      'notification_date'  => date('Y-m-d H:i:s')
                       
             );
          $this->db->insert('All_notification_Mobile', $notification_array);
                
                
                send_gcm_notify($title, $reg_id_web, $msg, $img_url, $tag, '4', $order_status, $order_date, $order_id1, $invoice_no, $name, $listing_name[$i], $agent);
                
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id1, $invoice_no, $name, $listing_name[$i], $agent);
            
            
                $msg1 = 'Kindly confirm and accept the new order';
                $img_url1 = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag1 = 'text';
                $key_count1 = '3';
                $title1 = 'New Order';
                send_gcm_notify($title1, $reg_id, $msg1, $img_url1, $tag1, $key_count1, $order_status, $order_date, $order_id1, $invoice_no, $name, $listing_name[$i], $agent);
                //web notification ends
            }
             }
             
             
             
             
          }
            
          
          
        
        $final_order = $order_id.','.$order_id1;
        /* 
         
         $resp = array(
                            'status' => 200,
                            'message' => 'Success',
                            'order_id'=>$order_id
                        );
                        */
                           $order_date = date('j M Y h:i A', strtotime($order_date));
            $order_info = $this->db->select('email,token, agent, token_status,phone')->from('users')->where('id', $user_id)->get()->row();
            $order_info_count = $this->db->select('token, agent, token_status,phone')->from('users')->where('id', $user_id)->get()->num_rows();
         if($order_info_count>0)
            {
            $token_status = $order_info->token_status;
            if ($token_status > 0) {
                $reg_id = $order_info->token;
                $agent = $order_info->agent;
                $phone = $order_info->phone;
                $user_email = $order_info->email;
           //     $msg = 'Thanks uploading your prescription with ' . $listing_name;
           
        //   b5
           
           if($night_owls > 0){
               $listing_name = 'night owls';
           }
           if($pharmacy_listing_count == "1")
           {
              $listing_name; 
           }
           else
           {
               $listing_name = $pharmacy_listing_count ."Pharmacies One of them will be confirm it shortly";
           }
                $msg = 'Thanks for placing order with ' . $listing_name;
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag = 'text';
                $key_count = '1';
                $title = 'Order Placed';
                
                //added by zak for all notification therough services
                $notification_array = array(
                      'title' => $title,
                     'msg'  => $msg,
                       'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => $order_status,
                      'order_date' => $order_date,
                       'order_id'   => $invoice_no,
                      'post_id'  => "",
                     'listing_id'  => "",
                      'booking_id'  => "",
                     'invoice_no' => $invoice_no,
                     'user_id'  => $user_id,
                      'notification_type'  => 'prescription',
                      'notification_date'  => date('Y-m-d H:i:s')
                    );
          $this->db->insert('All_notification_Mobile', $notification_array);
        //end 
              
              
              
              //sms same as order
             //echo $pharmacy_listing_count;
             if($pharmacy_listing_count=="1")
             {
                 $message2 = 'Thanks for placing your order with ' . $listing_name . ', Order Id-' . $invoice_no . ' -Team Medicalwale Any Enquiry please contact on +91 9619146163';
                 $post_data2 = array('From' => '02233721563', 'To' => $phone, 'Body' => $message2);
                 $exotel_sid2 = "aegishealthsolutions";
                 $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
                 $url2 = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
                 $ch2 = curl_init();
                 curl_setopt($ch2, CURLOPT_VERBOSE, 1);
                 curl_setopt($ch2, CURLOPT_URL, $url2);
                 curl_setopt($ch2, CURLOPT_POST, 1);
                 curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
                 curl_setopt($ch2, CURLOPT_FAILONERROR, 0);
                 curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
                 curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
                 $http_result2 = curl_exec($ch2);
                 curl_close($ch2); 
             }
             elseif($pharmacy_listing_count > "1")
             {
                 $message2 = 'Thanks for placing your order with ' . $pharmacy_listing_count . ' Pharmacies , Order Id-' . $invoice_no . ' -Team Medicalwale Any Enquiry please contact on +91 9619146163';
                 $post_data2 = array('From' => '02233721563', 'To' => $phone, 'Body' => $message2);
                 $exotel_sid2 = "aegishealthsolutions";
                 $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
                 $url2 = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
                 $ch2 = curl_init();
                 curl_setopt($ch2, CURLOPT_VERBOSE, 1);
                 curl_setopt($ch2, CURLOPT_URL, $url2);
                 curl_setopt($ch2, CURLOPT_POST, 1);
                 curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
                 curl_setopt($ch2, CURLOPT_FAILONERROR, 0);
                 curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
                 curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
                 $http_result2 = curl_exec($ch2);
                 curl_close($ch2);  
             }
              
              
  // echo $pharmacy_listing_count ; die();
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id1, $invoice_no, $name, $listing_name, $agent);
                
                 if(!empty($user_email))
                     {
                        $this->pharmacy_booking_sendmail($user_email, $msg, $invoice_no);
                     }
                }
            }
                        
                         $sql="DELETE FROM `user_card_order` WHERE `user_id`='$user_id'";
                        $query = $this->db->query($sql);
                       
                        //   return $invoice_no;
                        
            $return_data['invoice_no'] = $invoice_no;
           $return_data['message'] = '';
           
           return $return_data;
                       
       }
    
    // end
   
   
    public function favourite_add_quickbook($user_id, $address_id, $chat_id, $payment_method, $delivery_charge, $is_night_delivery,$lat,$lng,$fav_pharmacy) {
      
        date_default_timezone_set('Asia/Kolkata');
        $order_date = date('Y-m-d H:i:s');
        $invoice_no = date("YmdHis");
        $order_status = 'Awaiting Confirmation';
        $action_by = 'customer';
        $product_order_status = 'Awaiting Confirmation';
        
        $listing_type = '13';
        
         $radius = 5;
        $limit = 10;
        $start = 0;
       

        function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyCMHN__LczEziaYuKXQ-SKdiMfx5AiY66o&callback=initMap";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
            return $dist;
        }
       
       
        //echo "SELECT name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' and address_id='$address_id' limit 1";
        $query = $this->db->query("SELECT name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' and address_id='$address_id' limit 1");
        foreach ($query->result_array() as $row) {
            $name = $row['name'];
            $mobile = $row['mobile'];
            $address1 = $row['address1'];
            $address2 = $row['address2'];
            $landmark = $row['landmark'];
            $city = $row['city'];
            $state = $row['state'];
            $pincode = $row['pincode'];
        }
        
        $fav1 = explode(',',$fav_pharmacy);
        $fav2 = implode("','",$fav1);
        
        $sql = sprintf("SELECT `id`, `user_id`,`medical_name`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND user_id IN ('$fav2') HAVING distance < '%s' ORDER BY distance", ($lat), ($lng), ($lat), ($radius));
      
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if($count>0){
                   foreach ($query->result_array() as $mrow) {
                     //  echo 'id'.$mrow['user_id'];
                     //  echo 'name'.$mrow['medical_name'];
                           $listing_id[] = $mrow['user_id'];
                           $listing_name[] = $mrow['medical_name'];
        }

        $listing_id =  implode(',', $listing_id);
        $listing_name = implode(',',$listing_name);
       
        // echo $listing_id;
        // echo $listing_name;
       
        $user_order = array(
            'user_id' => $user_id,
            'order_type' => 'prescription',
            'listing_id' => $listing_id,
            'listing_name' => 'medicalwale_quickbook',
            'listing_type' => $listing_type,
            'invoice_no' => $invoice_no,
            'chat_id' => $chat_id,
            'address_id' => $address_id,
            'name' => $name,
            'mobile' => $mobile,
            'pincode' => $pincode,
            'address1' => $address1,
            'address2' => $address2,
            'landmark' => $landmark,
            'city' => $city,
            'state' => $state,
            'lat'=>$lat,
            'lng'=>$lng,
            'payment_method' => $payment_method,
            'order_total' => '0',
            'delivery_charge' => '0',
            'order_date' => $order_date,
            'order_status' => $order_status,
            'action_by' => $action_by,
            'delivery_charge' => $delivery_charge,
            'is_night_delivery' => $is_night_delivery
        );
        $this->db->insert('user_order', $user_order);
        $order_id = $this->db->insert_id();
        
       
        
        
        define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent) {
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url, "tag" => $tag,"notification_image" => $img_url,"notification_date" => $order_date, "notification_type" => "prescription", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
            if ($key_count == '1') {
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
                );
            }
            if ($key_count == '2') {
               // $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
               $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "body" => $msg, "icon" => $img_url, "tag" => $tag,"notification_image" => $img_url, "notification_date" => $order_date,"notification_type" => "prescription","click_action" => "https://vendor.sandbox.medicalwale.com/pharmacy/dashboard/pharmacy_notifications", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
               
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    //$agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AAAASZs-aSM:APA91bEQ0SZFS7YnRRK95O2Jp_PdzVC8t_osgDK0UdWXeQRo06fEedknj2b26yfX_YPkLq85h0lc3hUBlnSSPD1qvfikzFYiHd8tlt7BeuOg3f5WRwQxmz19WbEGbZEX894zZEF5iRO5'
                 $agent === 'android' ? 'Authorization: key=AIzaSyDHRrC6NE8v8Rj-t-_DlApjskQL0MBvDs8' : 'Authorization: key=AIzaSyANRr1UMlTQrPN_eZ0BaD70_aq4kIRJ4k4'
         
                );
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
        }
        if ($order_id > 0) {
            
           
            
            $order_date = date('j M Y h:i A', strtotime($order_date));
            $order_info = $this->db->select('token, agent, token_status')->from('users')->where('id', $user_id)->get()->row();
            $order_info_count = $this->db->select('token, agent, token_status')->from('users')->where('id', $user_id)->get()->num_rows();
            if($order_info_count>0)
            {
            $token_status = $order_info->token_status;
            if ($token_status > 0) {
                $reg_id = $order_info->token;
                $agent = $order_info->agent;
                $msg = 'Thanks uploading your prescription with ' . $listing_name;
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag = 'text';
                $key_count = '1';
                $title = 'Order Placed';
                
   
                
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
            }
            }
            //$partner_info = $this->db->select('token, agent, token_status,phone')->from('users')->where('id', $listing_id)->get()->row();
           
            $listing_id =  explode(',', $listing_id);
            $listing_name = explode(',',$listing_name);
           
           for($i = 0 ; $i<count($listing_id); $i++)
             {           
            $partner_info = $this->db->query('SELECT web_token, token, agent, token_status,phone from users where id= '.$listing_id[$i]);
           // echo 'SELECT web_token, agent, token_status,phone from users where id= '.$listing_id[$i];
            $partner_token_status = $partner_info->num_rows();
            $partner_info = $partner_info->row();
            if ($partner_token_status > 0) {
                $partner_token_status = $partner_info->token_status;
                $partner_phone = $partner_info->phone;
                $reg_id = $partner_info->web_token;
                $agent = $partner_info->agent;
                $msg = 'You Have Received a New Prescription Order';
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag = 'text';
                $key_count = '2';
                $title = 'New Order';
                //web notification starts
                $pharmacy_notifications = array(
                    'listing_id' => $listing_id[$i],
                    'order_id' => $order_id,
                    'title' => $title,
                    'msg' => $msg,
                    'image' => $img_url,
                    'notification_type' => 'prescription',
                    'order_status' => $order_status,
                    'order_date' => $order_date,
                    'invoice_no' => $invoice_no
                );
                $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
                //web notification ends
                
                
                
                
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name[$i], $agent);
            
                  //app notification starts
                $partner_token_status1 = $partner_info->token_status;
                $partner_phone1 = $partner_info->phone;
                $reg_id1 = $partner_info->token;
                $agent1 = $partner_info->agent;
                $msg1 = 'You Have Received a New Prescription Order';
                $img_url1 = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag1 = 'text';
                $key_count1 = '2';
                $title1 = 'New Order';
                //web notification starts
                $pharmacy_notifications1 = array(
                    'listing_id' => $listing_id[$i],
                    'order_id' => $order_id,
                    'title' => $title1,
                    'msg' => $msg1,
                    'image' => $img_url1,
                    'notification_type' => 'prescription',
                    'order_status' => $order_status,
                    'order_date' => $order_date,
                    'invoice_no' => $invoice_no
                );
               
                //app notification ends
                
                
                
                
                send_gcm_notify($title1, $reg_id1, $msg1, $img_url1, $tag1, $key_count1, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name[$i], $agent1);
            
  
                //sms same as order
                // $message = 'You have received an order, kindly login to https://pharmacy.medicalwale.com';
                // $post_data = array('From' => '02233721563', 'To' => $partner_phone, 'Body' => $message);
                // $exotel_sid = "aegishealthsolutions";
                // $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                // $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_VERBOSE, 1);
                // curl_setopt($ch, CURLOPT_URL, $url);
                // curl_setopt($ch, CURLOPT_POST, 1);
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                // curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                // $http_result = curl_exec($ch);
                // curl_close($ch);
                // //sms same to nyla,abdul, zaheer
                // $message2 = 'There is new order in pharmacy store. Pharmacy Name-' . $listing_name[$i] . ', Pharmacy Mobile- ' . $mobile . ', Order Id-' . $order_id . ', Order Date-' . $order_date . '.';
                // $post_data2 = array('From' => '02233721563', 'To' => '9619294702,7506908285', 'Body' => $message2);
                // $exotel_sid2 = "aegishealthsolutions";
                // $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
                // $url2 = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
                // $ch2 = curl_init();
                // curl_setopt($ch2, CURLOPT_VERBOSE, 1);
                // curl_setopt($ch2, CURLOPT_URL, $url2);
                // curl_setopt($ch2, CURLOPT_POST, 1);
                // curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
                // curl_setopt($ch2, CURLOPT_FAILONERROR, 0);
                // curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
                // curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
                // $http_result2 = curl_exec($ch2);
                // curl_close($ch2);
            }
             }
            
        }
        return $invoice_no;
    }   
}

  /*  public function order_confirm_cancel($order_id, $type, $order_status, $cancel_reason) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d');
        $updated_at = date('Y-m-d H:i:s');
        $order_type_query = $this->db->query("select order_type,user_id from user_order where order_id='$order_id' ");
        $get_order_info = $order_type_query->row_array();
        $order_type = $get_order_info['order_type'];
        $user_id = $get_order_info['user_id'];
        
        

        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent, $order_type) {
            //added by zak for all notification therough services
        //         $notification_array = array(
        //               'title' => $title,
        //               'msg'  => $msg,
        //               'img_url' => $img_url,
        //               'tag' => $tag,
        //               'order_status' => $order_status,
        //               'order_date' => $order_date,
        //               'order_id'   => $order_id,
        //               'post_id'  => "",
        //               'listing_id'  => "",
        //               'booking_id'  => "",
        //               'invoice_no' => $invoice_no,
        //               'user_id'  => $user_id,
        //               'notification_type'  => 'prescription',
        //               'notification_date'  => date('Y-m-d H:i:s')
                       
        //     );
        //  $this->db->insert('All_notification_Mobile', $notification_array);
        // //end 
            
            
            
            if (!defined("GOOGLE_GCM_URL"))
                define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url, "tag" => $tag, "notification_type" => $order_type, "order_status" => $order_status, "order_date" => $order_date, "invoice_no" => $order_id, "order_id" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
            //echo $result;
            //print_r($result);
        }
        function send_gcm_notify_usr($title, $reg_id, $msg, $img_url, $tag, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent, $order_type) {
              //added by zak for all notification therough services
        //         $notification_array = array(
        //               'title' => $title,
        //               'msg'  => $msg,
        //               'img_url' => $img_url,
        //               'tag' => $tag,
        //               'order_status' => $order_status,
        //               'order_date' => $order_date,
        //               'order_id'   => $order_id,
        //               'post_id'  => "",
        //               'listing_id'  => "",
        //               'booking_id'  => "",
        //               'invoice_no' => $invoice_no,
        //               'user_id'  => $user_id,
        //               'notification_type'  => 'prescription',
        //               'notification_date'  => date('Y-m-d H:i:s')
                       
        //     );
        //  $this->db->insert('All_notification_Mobile', $notification_array);
        //end 
           
           
            if (!defined("GOOGLE_GCM_URL"))
                define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url, "tag" => $tag, "notification_type" => $order_type, "order_status" => $order_status, "order_date" => $order_date, "invoice_no" => $order_id, "order_id" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
            //echo $result;
        }
        if ($type == 'Order Confirmed') {
            $update = $this->db->query("UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='Order Confirmed',`cancel_reason`='',`action_by`='customer' WHERE order_id='$order_id'");
            $updated_at = date('j M Y h:i A', strtotime($updated_at));
            $res_order = $this->db->query("select order_id,user_id,listing_id,invoice_no,name,listing_name from user_order where order_id='$order_id' limit 1");
            $order_info = $res_order->row_array();
            $user_id = $order_info['user_id'];
            $listing_id = $order_info['listing_id'];
            $invoice_no = $order_info['invoice_no'];
            $order_invoice_no = $order_info['order_id'];
            $name = $order_info['name'];
            $listing_name = $order_info['listing_name'];
            $updated_at = date('j M Y h:i A', strtotime($updated_at));
            //user notify starts
            $order_info = $this->db->select('token, agent, token_status')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $order_info->token_status;
            if ($token_status > 0) {
                $reg_id = $order_info->token;
                $agent = $order_info->agent;
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag = 'text';
                $title = 'Order Confirmed ';
                $msg = 'Your order has been confirmed';
                send_gcm_notify_usr($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $order_invoice_no, $name, $listing_name, $agent, $order_type);
            }
            //user notify ends
            $res_token = $this->db->query("select token,token_status,agent,phone from users where id='$listing_id' limit 1");
            $token_value = $res_token->row_array();
            $token_status = $token_value['token_status'];
            $partner_phone = $token_value['phone'];
            if ($token_status > 0) {
                $reg_id = $token_value['token'];
                $agent = $token_value['agent'];
                $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/1/images/logo.png';
                $tag = 'text';
                $title = 'Order Confirmed';
                $msg = 'Kindly deliver the order';
                //web notification starts
                $pharmacy_notifications = array(
                    'listing_id' => $listing_id,
                    'order_id' => $order_id,
                    'title' => $title,
                    'msg' => $msg,
                    'image' => $img_url,
                    'notification_type' => $order_type,
                    'order_status' => $order_status,
                    'order_date' => $updated_at,
                    'invoice_no' => $invoice_no
                );
                $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
                //web notification ends
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $order_invoice_no, $name, $listing_name, $agent, $order_type);
            }
            $message = 'Order confirmed from the customer, Kindly deliver the order.';
            $post_data = array('From' => '02233721563', 'To' => $partner_phone, 'Body' => $message);
            $exotel_sid = "aegishealthsolutions";
            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
            $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            $http_result = curl_exec($ch);
            curl_close($ch);
            return array(
                'status' => 201,
                'message' => 'Order Confirmed'
            );
        }
        if ($type == 'Order Cancelled') {
            $res_status = $this->db->query("select order_status from user_order where order_id='$order_id' limit 1");
            $o_status = $res_status->row_array();
            $check_status = $o_status['order_status'];
            if ($check_status == 'Order Delivered') {
                return array(
                    'status' => 201,
                    'message' => 'Order Delivered'
                );
            } else {
                $update = $this->db->query("UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='Order Cancelled',`cancel_reason`='$cancel_reason',`action_by`='customer' WHERE order_id='$order_id'");
                if ($update) {
                    $res_order = $this->db->query("select order_id,listing_id,user_id,invoice_no,name,listing_name from user_order where order_id='$order_id' limit 1");
                    $order_info = $res_order->row_array();
                    $user_id = $order_info['user_id'];
                    $listing_id = $order_info['listing_id'];
                    $invoice_no = $order_info['invoice_no'];
                     $order_invoice_no = $order_info['order_id'];
                    $name = $order_info['name'];
                    $listing_name = $order_info['listing_name'];
                    $updated_at = date('j M Y h:i A', strtotime($updated_at));
                    //user notify starts
                    $order_info = $this->db->select('token, agent, token_status')->from('users')->where('id', $user_id)->get()->row();
                    $token_status = $order_info->token_status;
                    if ($token_status > 0) {
                        $reg_id = $order_info->token;
                        $agent = $order_info->agent;
                        $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                        $tag = 'text';
                        $title = 'Order Cancelled ';
                        $msg = 'Your order has been cancelled';
                        send_gcm_notify_usr($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $order_invoice_no, $name, $listing_name, $agent, $order_type);
                    }
                    //user notify ends
                    $res_token = $this->db->query("select token,token_status,agent,phone from users where id='$listing_id' limit 1");
                    $token_value = $res_token->row_array();
                    $token_status = $token_value['token_status'];
                    $partner_phone = $token_value['phone'];
                    if ($token_status > 0) {
                        $reg_id = $token_value['token'];
                        $agent = $token_value['agent'];
                        $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/1/images/logo.png';
                        $tag = 'text';
                        $title = 'Order Cancelled';
                        $msg = 'You order has been cancelled';
                        //web notification starts
                        $pharmacy_notifications = array(
                            'listing_id' => $listing_id,
                            'order_id' => $order_id,
                            'title' => $title,
                            'msg' => $msg,
                            'image' => $img_url,
                            'notification_type' => $order_type,
                            'order_status' => $order_status,
                            'order_date' => $updated_at,
                            'invoice_no' => $invoice_no
                        );
                        $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
                        //web notification ends
                        send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $order_invoice_no, $name, $listing_name, $agent, $order_type);
                    }
                    $message = 'Order cancelled, You order has been cancelled.';
                    $post_data = array('From' => '02233721563', 'To' => $partner_phone, 'Body' => $message);
                    $exotel_sid = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_VERBOSE, 1);
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                    $http_result = curl_exec($ch);
                    curl_close($ch);
                    return array(
                        'status' => 201,
                        'message' => 'Order Cancelled'
                    );
                } else {
                    return array(
                        'status' => 201,
                        'message' => 'failed'
                    );
                }
            }
        }
    }
    */
    
     public function order_confirm_cancel($order_id, $type, $order_status, $cancel_reason,$mode) {
          $this->load->model('PartnermnoModel');
          
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d');
        $updated_at = date('Y-m-d H:i:s');
        $order_type_query = $this->db->query("select order_type,user_id,listing_type,name from user_order where invoice_no='$order_id' ");
        $get_order_info = $order_type_query->row_array();
        $order_type = $get_order_info['order_type'];
        $user_id = $get_order_info['user_id'];
        $listing_type = $get_order_info['listing_type'];
        $name = $get_order_info['name'];
        

        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent, $order_type) {
        
              
        
            if (!defined("GOOGLE_GCM_URL"))
                define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url, "tag" => $tag,"notification_image" => $img_url, "notification_date" => $order_date,"notification_type" => $order_type, "order_status" => $order_status, "order_date" => $order_date, "invoice_no" => $order_id, "order_id" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
            //echo $result;
            //print_r($result);
        }
        function send_gcm_notify_web($title, $reg_id, $msg, $img_url, $tag, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent, $order_type) {
        
              
        
            if (!defined("GOOGLE_GCM_URL"))
                define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url, "tag" => $tag,"notification_image" => $img_url, "notification_date" => $order_date,"notification_type" => $order_type, "order_status" => $order_status, "order_date" => $order_date, "invoice_no" => $order_id, "order_id" => $invoice_no, "name" => $name, "listing_name" => $listing_name,"sound" => "https://medicalwale.s3.amazonaws.com/images/Sound/Vendor_Panel.mp3")
            );
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                //$agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
                $agent === 'android' ? 'Authorization: key=AIzaSyD2RUGDhVmm2X9BNuLCvrV_tPu6wMMS3Zg' : 'Authorization: key=AIzaSyCyZI_Kn7HcGdeL227UiVsyCWvGB-2JoRQ'
              
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
            //echo $result;
            //print_r($result);
        }
        function send_gcm_notify_usr($title, $reg_id, $msg, $img_url, $tag, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent, $order_type) {
           
            if (!defined("GOOGLE_GCM_URL"))
                define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url, "tag" => $tag, "notification_image" => $img_url,"notification_date" => $order_date,"notification_type" => $order_type, "order_status" => $order_status, "order_date" => $order_date, "invoice_no" => $order_id, "order_id" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
            //echo $result;
        }
        
        if ($type == 'Order Confirmed') {
            $update = $this->db->query("UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='Order Confirmed',`payment_method`='$mode',`cancel_reason`='',`action_by`='customer' WHERE invoice_no='$order_id'");

            $invno = $order_id;
            $action_by_status = "Customer";
            $orderStatus = "Order confirmed by ".$name;
            $this->OrderModel->update_status( $invno,$orderStatus, $action_by_status);
                    
            
            
            $updated_at = date('j M Y h:i A', strtotime($updated_at));
            $res_order = $this->db->query("select order_id,user_id,listing_id,invoice_no,name,listing_name from user_order where invoice_no='$order_id' limit 1");
            $order_info = $res_order->row_array();
            $user_id = $order_info['user_id'];
            $listing_id = $order_info['listing_id'];
            $invoice_no = $order_info['invoice_no'];
            $order_invoice_no = $order_info['order_id'];
            $name = $order_info['name'];
            $listing_name = $order_info['listing_name'];
            $updated_at = date('j M Y h:i A', strtotime($updated_at));
            //user notify starts
            $order_info = $this->db->select('email,token, agent, token_status')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $order_info->token_status;
            if ($token_status > 0) {
                $reg_id = $order_info->token;
                $agent = $order_info->agent;
                $user_email = $order_info->email;
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag = 'text';
                $title = 'Order Confirmed ';
                $msg = 'Your order has been confirmed';
                send_gcm_notify_usr($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $order_invoice_no, $name, $listing_name, $agent, $order_type);
                 
                 $notification_array = array(
                          'title' => $title,
                          'msg'  => $msg,
                          'img_url' => $img_url,
                          'tag' => $tag,
                          'order_status' => $order_status,
                          'order_date' => $updated_at,
                          'order_id'   => $invoice_no,
                          'listing_name' => $listing_name,
                          'post_id'  => "",
                          'listing_id'  => "",
                          'booking_id'  => "",
                          'invoice_no' => $invoice_no,
                          'user_id'  => $user_id,
                          'notification_type'  => $order_type,
                          'notification_date'  => date('Y-m-d H:i:s')
                           
                );
             $this->db->insert('All_notification_Mobile', $notification_array);
              if(!empty($user_email))
                     {
                        $this->pharmacy_booking_sendmail($user_email, $msg, $order_invoice_no);
                     }
            }
            
            //user notify ends
            
            // pharmacy notifications
                    $mno_details = $this->db->query("SELECT u.web_token,u.token,u.agent,u.phone,mo.mno_id, mo.id FROM `mno_orders` as mo left join users as u on (mo.mno_id = u.id) WHERE mo.`invoice_no` = '$invoice_no' AND mo.`ongoing` = 1 AND mo.`status` = 'accepted'")->row_array();
            
            if(sizeof($mno_details) > 0){
                $res_token = $this->db->query("select email, token,token_status,agent,phone from users where id='$listing_id' limit 1");
                $token_value = $res_token->row_array();
                $token_status = $token_value['token_status'];
                $partner_phone = $token_value['phone'];
                $user_email = $token_value['email'];
                if ($token_status > 0) {
                    $reg_id = $token_value['token'];
                    $reg_id_web = $token_value['web_token'];
                    $agent = $token_value['agent'];
                    $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                    $tag = 'text';
                    $title = 'Order Confirmed by user';
                    $msg = 'Night owl will come to pick the order';
                    //web notification starts
                    $pharmacy_notifications = array(
                        'listing_id' => $listing_id,
                        'order_id' => $order_invoice_no,
                        'title' => $title,
                        'msg' => $msg,
                        'image' => $img_url,
                        'notification_type' => $order_type,
                        'order_status' => $order_status,
                        'order_date' => $updated_at,
                        'invoice_no' => $invoice_no
                    );
                    $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
                    //web notification ends
                    
                    send_gcm_notify_web($title, $reg_id_web, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $order_invoice_no, $name, $listing_name, $agent, $order_type);
                     
                    send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $order_invoice_no, $name, $listing_name, $agent, $order_type);
                     
                     $notification_array = array(
                                'title' => $title,
                              'msg'  => $msg,
                              'img_url' => $img_url,
                              'tag' => $tag,
                              'order_status' => $order_status,
                              'order_date' => $updated_at,
                              'order_id'   => $order_id,
                              'listing_name' => $listing_name,
                              'post_id'  => "",
                              'listing_id'  => "",
                              'booking_id'  => "",
                              'invoice_no' => $order_invoice_no,
                              'user_id'  => $user_id,
                              'notification_type'  => $order_type,
                              'notification_date'  => date('Y-m-d H:i:s')
                               
                    ); 
                 $this->db->insert('All_notification_Mobile', $notification_array);
                 if(!empty($user_email))
                         {
               $this->pharmacy_booking_sendmail($user_email, $msg, $order_invoice_no);
                         }
                }
                $message = 'Order confirmed by the customer, Night owl will come to pick up the order.';
                $post_data = array('From' => '02233721563', 'To' => $partner_phone, 'Body' => $message);
                $exotel_sid = "aegishealthsolutions";
                $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                $http_result = curl_exec($ch);
                curl_close($ch);
                
            
            
                // confirmed by user send notification to night owl
                
                    
                    $message = 'User confirmed Order. order id #'.$invoice_no.'';
                    $customer_phone = $mno_details['phone'];
                    $post_data = array('From' => '02233721563', 'To' => $customer_phone, 'Body' => $message);
                    $exotel_sid = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_VERBOSE, 1);
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                    $http_result = curl_exec($ch);
                    curl_close($ch);
                    
                    // if(!empty($mno_details['token']) &&  sizeof($mno_details['token']) > 0){
                    
                    
                     // new notification to mno
                        
                         $receiver_id = $mno_details['mno_id'];
                        $invoice_no = $invoice_no;
                        $notification_type = 8; //CUSTOMER_ACCEPTED_ORDER refer mno_notification_types
                        $img = "https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png";
                        $mno_order_id = $mno_details['id'];
                        $title = "User confirmed order";
                        $msg = "User confirmed Order. order id ".$invoice_no." please deliver it";
                
                    $res = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
                            
                        
              
                        /*$reg_id = $mno_details['token'];
                        $agent = $mno_details['agent'];
                        $msg = 'User confirmed Order. order id '.$invoice_no.' please deliver it';
                        $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                        $tag = 'text';
                        $key_count = '1';
                        $title = 'User confirmed order';
                        $order_status = 'User confirmed';
                        $order_date = '';
                        $name = '';
                        $notification_type = 5;
                        
                     
                        if(!empty($reg_id)){
                            $this->PartnermnoModel->send_gcm_notify_mno($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
                        }*/
                            
                        
                
                
                
            } else {
            
                $res_token = $this->db->query("select email, web_token,token,token_status,agent,phone from users where id='$listing_id' limit 1");
                $token_value = $res_token->row_array();
                $token_status = $token_value['token_status'];
                $partner_phone = $token_value['phone'];
                $user_email = $token_value['email'];
                if ($token_status > 0) {
                    $reg_id = $token_value['token'];
                    $reg_id_web = $token_value['web_token'];
                    $agent = $token_value['agent'];
                    $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                    $tag = 'text';
                    $title = 'Order Confirmed';
                    $msg = 'Kindly deliver the order';
                    //web notification starts
                    $pharmacy_notifications = array(
                        'listing_id' => $listing_id,
                        'order_id' => $order_invoice_no,
                        'title' => $title,
                        'msg' => $msg,
                        'image' => $img_url,
                        'notification_type' => $order_type,
                        'order_status' => $order_status,
                        'order_date' => $updated_at,
                        'invoice_no' => $invoice_no
                    );
                    $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
                    //web notification ends
                    send_gcm_notify_web($title, $reg_id_web, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $order_invoice_no, $name, $listing_name, $agent, $order_type);
                    
                    send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $order_invoice_no, $name, $listing_name, $agent, $order_type);
                     
                     $notification_array = array(
                                'title' => $title,
                              'msg'  => $msg,
                              'img_url' => $img_url,
                              'tag' => $tag,
                              'order_status' => $order_status,
                              'order_date' => $updated_at,
                              'order_id'   => $order_id,
                              'listing_name' => $listing_name,
                              'post_id'  => "",
                              'listing_id'  => "",
                              'booking_id'  => "",
                              'invoice_no' => $order_invoice_no,
                              'user_id'  => $listing_id,
                              'notification_type'  => $order_type,
                              'notification_date'  => date('Y-m-d H:i:s')
                               
                    );
                 $this->db->insert('All_notification_Mobile', $notification_array);
                 if(!empty($user_email))
                         {
               $this->pharmacy_booking_sendmail($user_email, $msg, $order_invoice_no);
                         }
                }
                $message = 'Order confirmed from the customer, Kindly deliver the order.';
                $post_data = array('From' => '02233721563', 'To' => $partner_phone, 'Body' => $message);
                $exotel_sid = "aegishealthsolutions";
                $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                $http_result = curl_exec($ch);
                curl_close($ch);
                
            }
            return array(
                'status' => 201,
                'message' => 'Order Confirmed'
            );
        }
        if ($type == 'Order Cancelled') { 
            $res_status = $this->db->query("select order_status, name from user_order where invoice_no='$order_id' limit 1");
            $o_status = $res_status->row_array();
            $check_status = $o_status['order_status'];
            $name = $o_status['name'];
            if ($check_status == 'Order Delivered') {
                return array(
                    'status' => 201,
                    'message' => 'Order Delivered'
                );
            } else {
                $update = $this->db->query("UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='Order Cancelled',`cancel_reason`='$cancel_reason',`action_by`='customer' WHERE invoice_no='$order_id'");
                
                $invno = $order_id;
                    $action_by_status = "Customer";
                    $orderStatus = "Order cancelled by ".$name;
                    $this->OrderModel->update_status( $invno,$orderStatus, $action_by_status);
                    
                
                if ($update) {
                    $res_order = $this->db->query("select order_id,listing_id,user_id,invoice_no,name,listing_name from user_order where invoice_no='$order_id' limit 1");
                    $order_info = $res_order->row_array();
                    
                    
                    
            
            
                    $user_id = $order_info['user_id'];
                    $listing_id = $order_info['listing_id'];
                    $invoice_no = $order_info['invoice_no'];
                    $order_invoice_no = $order_info['order_id'];
                    $name = $order_info['name'];
                      $listing_name = $order_info['listing_name'];
                    $updated_at = date('j M Y h:i A', strtotime($updated_at));
                    //user notify starts
                    $order_info = $this->db->select('email,token, agent, token_status')->from('users')->where('id', $user_id)->get()->row();
                    $token_status = $order_info->token_status;
                    if ($token_status > 0) {
                        $reg_id = $order_info->token;
                        $agent = $order_info->agent;
                        $user_email=  $order_info->email;
                        $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                        $tag = 'text';
                        $title = 'Order Cancelled ';
                        $msg = 'Your order '.$order_id.' has been cancelled' ;
                        send_gcm_notify_usr($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $order_invoice_no, $name, $listing_name, $agent, $order_type);
                        
                 $notification_array = array(
                                'title' => $title,
                          'msg'  => $msg,
                          'img_url' => $img_url,
                          'tag' => $tag,
                          'order_status' => $order_status,
                          'order_date' => $updated_at,
                          'order_id'   => $invoice_no,
                          'listing_name' => $listing_name,
                          'post_id'  => "",
                          'listing_id'  => "",
                          'booking_id'  => "",
                          'invoice_no' => $invoice_no,
                          'user_id'  => $user_id,
                          'notification_type'  => $order_type,
                          'notification_date'  => date('Y-m-d H:i:s')
                               
                    );
                 $this->db->insert('All_notification_Mobile', $notification_array);
                 if(!empty($user_email))
                     {
                        $this->pharmacy_booking_sendmail($user_email, $msg, $invoice_no);
                     }
                    }
                    //user notify ends
                    
                    // pharmacy notification
                    
                        $res_token = $this->db->query("select email,web_token,token,token_status,agent,phone from users where id='$listing_id' limit 1");
                        $token_value = $res_token->row_array();
                        $token_status = $token_value['token_status'];
                        $partner_phone = $token_value['phone'];
                        if ($token_status > 0) {
                            $reg_id = $token_value['token'];
                            $reg_id_web = $token_value['web_token'];
                            $agent = $token_value['agent'];
                            $user_email=  $token_value['email'];
                            $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                            $tag = 'text';
                            $title = 'Order Cancelled';
                            $msg = 'Your order '.$order_id.' has been cancelled' ;
                            //web notification starts
                            $pharmacy_notifications = array(
                                'listing_id' => $listing_id,
                                'order_id' => $order_id,
                                'title' => $title,
                                'msg' => $msg,
                                'image' => $img_url,
                                'notification_type' => $order_type,
                                'order_status' => $order_status,
                                'order_date' => $updated_at,
                                'invoice_no' => $invoice_no
                            );
                            $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
                            //web notification ends
                            send_gcm_notify_web($title, $reg_id_web, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $order_invoice_no, $name, $listing_name, $agent, $order_type);
                     
                            send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $order_invoice_no, $name, $listing_name, $agent, $order_type);
                     
                      
                             $notification_array = array(
                                       'title' => $title,
                              'msg'  => $msg,
                              'img_url' => $img_url,
                              'tag' => $tag,
                              'order_status' => $order_status,
                              'order_date' => $updated_at,
                              'order_id'   => $invoice_no,
                              'listing_name' => $listing_name,
                              'post_id'  => "",
                              'listing_id'  => "",
                              'booking_id'  => "",
                              'invoice_no' => $invoice_no,
                              'user_id'  => $listing_id,
                              'notification_type'  => $order_type,
                              'notification_date'  => date('Y-m-d H:i:s')
                                       
                            );
                         $this->db->insert('All_notification_Mobile', $notification_array);
                         if(!empty($user_email))
                         {
                            $this->pharmacy_booking_sendmail($user_email, $msg, $invoice_no);
                         }
               
                        }
                        $message = 'Order cancelled, Your order '.$invoice_no.' has been cancelled' ;
                        $post_data = array('From' => '02233721563', 'To' => $partner_phone, 'Body' => $message);
                        $exotel_sid = "aegishealthsolutions";
                        $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                        $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_VERBOSE, 1);
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                        $http_result = curl_exec($ch);
                        curl_close($ch);
                        
                       $mno_details = $this->db->query("SELECT u.id as user_id,u.phone ,u.token,u.agent,mo.mno_id, mo.id FROM `mno_orders` as mo left join users as u on (mo.mno_id = u.id) WHERE mo.`invoice_no` = '$invoice_no' AND mo.`ongoing` = 1 AND mo.`status` = 'accepted'")->row_array();
                              
                        
                    if(sizeof($mno_details) > 0){
                               
                               
                            //   ongoing as 0
                                         
                    $mno_order_id = $mno_details['id'];
                    // update cancel_reason_after_accept , redirected_to, and order_cancel'
                    $this->db->query("UPDATE `mno_orders` SET cancel_reason_after_accept = '$cancel_reason', `order_cancel` = '1', ongoing = 0 where id= '$mno_order_id'");
                    
        
                        // cancelled by user send notification to night owl
                        
                        
                           
                            $message = 'User cancelled Order. order id #'.$invoice_no.'';
                            $customer_phone = $mno_details['phone'];
                            $mno_id = $mno_details['id'];
                            $post_data = array('From' => '02233721563', 'To' => $customer_phone, 'Body' => $message);
                            $exotel_sid = "aegishealthsolutions";
                            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                            $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_VERBOSE, 1);
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                            $http_result = curl_exec($ch);
                            curl_close($ch);
                            
                            // if(!empty($mno_details['token']) &&  sizeof($mno_details['token']) > 0){
                      
                               /* $reg_id = $mno_details['token'];
                                $agent = $mno_details['agent'];
                                $msg = 'User cancelled Order. order id #'.$invoice_no;
                                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                                $tag = 'text';
                                $title = 'User cancelled order';
                                $order_status = 'User cancelled';
                                $order_date = '';
                                $name = '';
                                $notification_type = 6;
                                
                             
                                if(!empty($reg_id)){
                                    $this->PartnermnoModel->send_gcm_notify_mno($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
                                }*/
                                
                                   $receiver_id = $mno_details['mno_id'];
                                    $invoice_no = $invoice_no;
                                    $notification_type = 9; //CUSTOMER_REJECTED_ORDER refer mno_notification_types
                                    $img = "https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png";
                                    $mno_order_id = $mno_details['id'];
                                    $title = "User cancelled order";
                                    $msg = "User cancelled Order. order id ".$invoice_no;
                            
                                $res = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
                            
                  
                    
                                $this->db->query("UPDATE `mno_orders` SET `ongoing`=0, `cancel_reason`='User cancelled'  WHERE `invoice_no`='$invoice_no' and `mno_id`= '$mno_id' AND `ongoing` = '1'");
                        
                        
                        }   
                 
                
                        return array(
                            'status' => 201,
                            'message' => 'Order Cancelled'
                        );
                } else {
                    return array(
                        'status' => 201,
                        'message' => 'failed'
                    );
                }
            }
        }
    }
    
    
    
     //added by zak for quick book from stack to order medicines 
   //start
  /* public function order_from_stack($user_id,$address_id, $payment_method, $delivery_charges , $is_night_delivery, $lat, $lng, $product_details,$is_profile,$listing_id)
       {
         date_default_timezone_set('Asia/Kolkata');
        $order_date = date('Y-m-d H:i:s');
        $invoice_no = date("YmdHis");
        $order_status = 'Awaiting Confirmation';
        $action_by = 'customer';
        $product_order_status = 'Awaiting Confirmation';
        
        $chat_id = 'user'.$user_id;
          $product_details = json_decode($product_details,TRUE);
          
          $prescription_data = $product_details['prescrption_data'];
          $medicine_product_data = $product_details['medicine_product_data'];
          
       //   print_r($product_details['prescrption_data']);
      //    print_r($product_details['medicine_product_data']);
      $p_count = count($product_details['prescrption_data']);
        $m_count = count($product_details['medicine_product_data']);
        $listing_type = '13';
        
         $radius = 5;
        $limit = 10;
        $start = 0;
       

        function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyCMHN__LczEziaYuKXQ-SKdiMfx5AiY66o&callback=initMap";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
            return $dist;
        }
        
        //FOR ADDRESS FROM ADDRESS ID BY ZAK
         $query = $this->db->query("SELECT name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' and address_id='$address_id' limit 1");
        foreach ($query->result_array() as $row) {
            $name = $row['name'];
            $mobile = $row['mobile'];
            $address1 = $row['address1'];
            $address2 = $row['address2'];
            $landmark = $row['landmark'];
            $city = $row['city'];
            $state = $row['state'];
            $pincode = $row['pincode'];
        }
        //END
        
        //added for notification send  for placing order
        
         define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent) {
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url, "tag" => $tag, "notification_type" => "prescription", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
            if ($key_count == '1') {
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
                );
            }
            if ($key_count == '2') {
               // $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
               $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "body" => $msg, "icon" => $img_url, "tag" => $tag, "notification_type" => "prescription","click_action" => "https://vendor.sandbox.medicalwale.com/pharmacy/dashboard/pharmacy_notifications", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
               
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AAAASZs-aSM:APA91bEQ0SZFS7YnRRK95O2Jp_PdzVC8t_osgDK0UdWXeQRo06fEedknj2b26yfX_YPkLq85h0lc3hUBlnSSPD1qvfikzFYiHd8tlt7BeuOg3f5WRwQxmz19WbEGbZEX894zZEF5iRO5'
                );
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
        }
       
        
        //end
        
        //added for generate order id for common added product
        
        if($is_profile != 'yes')
        {
         $sql = sprintf("SELECT `id`, `user_id`,`medical_name`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' HAVING distance < '%s' ORDER BY distance LIMIT 20", ($lat), ($lng), ($lat), ($radius));
      
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if($count>0){
                   foreach ($query->result_array() as $mrow) {
                     //  echo 'id'.$mrow['user_id'];
                     //  echo 'name'.$mrow['medical_name'];
                           $listing_id[] = $mrow['user_id'];
                           $listing_name[] = $mrow['medical_name'];
        }

        $listing_id =  implode(',', $listing_id);
        $listing_name = implode(',',$listing_name);
        }
        }
        else
       {
          $listing_id =  json_decode($listing_id,TRUE);
      //    print_r($listing_id);
       //   echo count($listing_id);
              for($i = 0; $i < count($listing_id); $i++)
            {
                $check_id = $listing_id[$i];
                  $query1 = $this->db->query("SELECT `id`, `user_id`,`medical_name` FROM medical_stores WHERE is_approval='1' AND is_active='1' AND user_id = '$check_id'");
      
        //$query1 = $this->db->query($sql1);
        $count1 = $query1->num_rows();
        if($count1>0){
                   foreach ($query1->result_array() as $m1row) {
                       
                           $listing_id1[] = $m1row['user_id'];
                           $listing_name1[] = $m1row['medical_name'];
                        }
          
            }
        }
            $listing_id =  implode(',', $listing_id1);
            $listing_name = implode(',',$listing_name1);
        }
        
        $user_order = array(
            'user_id' => $user_id,
            'order_type' => 'prescription',
            'listing_id' => $listing_id,
            'listing_name' => 'medicalwale_quickbook',
            'listing_type' => $listing_type,
            'invoice_no' => $invoice_no,
            'chat_id' => $chat_id,
            'address_id' => $address_id,
            'name' => $name,
            'mobile' => $mobile,
            'pincode' => $pincode,
            'address1' => $address1,
            'address2' => $address2,
            'landmark' => $landmark,
            'city' => $city,
            'state' => $state,
            'payment_method' => $payment_method,
            'order_total' => '0',
            'delivery_charge' => '0',
            'order_date' => $order_date,
            'order_status' => $order_status,
            'action_by' => $action_by,
            'is_night_delivery' => $is_night_delivery
            
        );
        $this->db->insert('user_order', $user_order);
        $order_id = $this->db->insert_id();

        //END
        
        
        
        //FOR PRESCRIBED ORDER AND ADD INTO PRESCPTION_ORDER_DETAILS TABLE
        for($i = 0 ; $i < $p_count ; $i++)
          {
              $pre_id   = $prescription_data[$i]['product_id'];
              $pre_type = $prescription_data[$i]['prescription_type'];
              
              
               $query = $this->db->query("SELECT * FROM `user_card_order` WHERE product_id='$pre_id' AND user_id = '$user_id'");
            //   echo "SELECT * FROM `user_card_order` WHERE product_id='$pre_id' AND user_id = '$user_id'";
               foreach ($query->result_array() as $prow) {
                         
                  $prescription_url = $prow['prescription_url']; 
                  $offline_prescription = $prow['offline_prescription'];
                         if($prescription_url != '' && $prescription_url != null)
                         {
                         $prescription_name = str_replace("https://s3.amazonaws.com/medicalwale/images/prescription_images/","",$prescription_url);
                         }
                         else
                         {
                        $prescription_name = str_replace("https://s3.amazonaws.com/medicalwale/images/prescription_images/","",$offline_prescription);
                         } 
               }
               
            //   echo 'prescription name'.$prescription_name;
            //   echo 'url'.$prescription_url;
            //   echo 'offline'.$offline_prescription;
                  date_default_timezone_set('Asia/Calcutta');
                        $invoice_no = date("YmdHis");
                        $order_status = 'Awaiting Confirmation';
                       // $actual_image_name  = $query_status->prescription_link;
                        $this->db->query("INSERT INTO `prescription_order_details`(`order_id`, `prescription_image`,`order_status`, `uni_id`) VALUES ('$order_id', '$prescription_name','$order_status', '$invoice_no')");
               
         }    
         
         //addded for send gcm notification and text messagess to user and vendor
          if ($order_id > 0) {
            
           
            
            $order_date = date('j M Y h:i A', strtotime($order_date));
            $order_info = $this->db->select('token, agent, token_status')->from('users')->where('id', $user_id)->get()->row();
            $order_info_count = $this->db->select('token, agent, token_status')->from('users')->where('id', $user_id)->get()->num_rows();
            if($order_info_count>0)
            {
            $token_status = $order_info->token_status;
            if ($token_status > 0) {
                $reg_id = $order_info->token;
                $agent = $order_info->agent;
           //     $msg = 'Thanks uploading your prescription with ' . $listing_name;
                 $msg = 'Thanks for placing your order with ' . $listing_name;
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag = 'text';
                $key_count = '1';
                $title = 'Order Placed';
                
        //           //added by zak for all notification therough services
        //         $notification_array = array(
        //               'title' => $title,
        //               'msg'  => $msg,
        //               'img_url' => $img_url,
        //               'tag' => $tag,
        //               'order_status' => $order_status,
        //               'order_date' => $order_date,
        //               'order_id'   => $order_id,
        //               'post_id'  => "",
        //               'listing_id'  => "",
        //               'booking_id'  => "",
        //               'invoice_no' => $invoice_no,
        //               'user_id'  => $user_id,
        //               'notification_type'  => 'prescription',
        //               'notification_date'  => date('Y-m-d H:i:s')
                       
        //     );
        //  $this->db->insert('All_notification_Mobile', $notification_array);
        //end 
                
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
            }
            }
            //$partner_info = $this->db->select('token, agent, token_status,phone')->from('users')->where('id', $listing_id)->get()->row();
           
            $listing_id =  explode(',', $listing_id);
        $listing_name = explode(',',$listing_name);
           
           for($i = 0 ; $i<count($listing_id); $i++)
             {           
            $partner_info = $this->db->query('SELECT web_token, agent, token_status,phone from users where id= '.$listing_id[$i]);
           // echo 'SELECT web_token, agent, token_status,phone from users where id= '.$listing_id[$i];
            $partner_token_status = $partner_info->num_rows();
            $partner_info = $partner_info->row();
            if ($partner_token_status > 0) {
                $partner_token_status = $partner_info->token_status;
                $partner_phone = $partner_info->phone;
                $reg_id = $partner_info->web_token;
                $agent = $partner_info->agent;
                $msg = 'You Have Received a New Order kindly Confirm it First';
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag = 'text';
                $key_count = '2';
                $title = 'New Order';
                //web notification starts
                $pharmacy_notifications = array(
                    'listing_id' => $listing_id[$i],
                    'order_id' => $order_id,
                    'title' => $title,
                    'msg' => $msg,
                    'image' => $img_url,
                    'notification_type' => 'prescription',
                    'order_status' => $order_status,
                    'order_date' => $order_date,
                    'invoice_no' => $invoice_no
                );
                $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
                //web notification ends
                
                
                
                
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name[$i], $agent);
            
                //sms same as order
                // $message = 'You have received an order, kindly login to https://pharmacy.medicalwale.com';
                // $post_data = array('From' => '02233721563', 'To' => $partner_phone, 'Body' => $message);
                // $exotel_sid = "aegishealthsolutions";
                // $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                // $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_VERBOSE, 1);
                // curl_setopt($ch, CURLOPT_URL, $url);
                // curl_setopt($ch, CURLOPT_POST, 1);
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                // curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                // $http_result = curl_exec($ch);
                // curl_close($ch);
                // //sms same to nyla,abdul, zaheer
                // $message2 = 'There is new order in pharmacy store. Pharmacy Name-' . $listing_name[$i] . ', Pharmacy Mobile- ' . $mobile . ', Order Id-' . $order_id . ', Order Date-' . $order_date . '.';
                // $post_data2 = array('From' => '02233721563', 'To' => '9619294702,7506908285', 'Body' => $message2);
                // $exotel_sid2 = "aegishealthsolutions";
                // $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
                // $url2 = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
                // $ch2 = curl_init();
                // curl_setopt($ch2, CURLOPT_VERBOSE, 1);
                // curl_setopt($ch2, CURLOPT_URL, $url2);
                // curl_setopt($ch2, CURLOPT_POST, 1);
                // curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
                // curl_setopt($ch2, CURLOPT_FAILONERROR, 0);
                // curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
                // curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
                // $http_result2 = curl_exec($ch2);
                // curl_close($ch2);
            }
             }
            
        }
         
         
         //end
         //inserrt order entry
         $user_order = array(
            'user_id' => $user_id,
            'order_type' => 'order',
            'listing_id' => $listing_id,
            'listing_name' => 'medicalwale_quickbook',
            'listing_type' => $listing_type,
            'invoice_no' => $invoice_no,
            'chat_id' => $chat_id,
            'address_id' => $address_id,
            'name' => $name,
            'mobile' => $mobile,
            'pincode' => $pincode,
            'address1' => $address1,
            'address2' => $address2,
            'landmark' => $landmark,
            'city' => $city,
            'state' => $state,
            'payment_method' => $payment_method,
            'order_total' => '0',
            'delivery_charge' => '0',
            'order_date' => $order_date,
            'order_status' => $order_status,
            'action_by' => $action_by,
            'is_night_delivery' => $is_night_delivery
            
        );
        $this->db->insert('user_order', $user_order);
        $order_id1 = $this->db->insert_id();

        for($j = 0 ; $j < $m_count ; $j++)
          {
              $pro_id = $medicine_product_data[$j]['product_id'];
            //  echo 'test'.$pro_id;
              $query = $this->db->query("SELECT * FROM `user_card_order` WHERE product_id = '$pro_id' AND user_id = '$user_id'");
         //     echo "SELECT * FROM `user_card_order` WHERE product_id='$pro_id' AND user_id = '$user_id'";
               foreach ($query->result_array() as $mrow) {
                         
                            $product_id = $mrow['product_id']; 
                           $product_quantity = $mrow['quantity'];
        
               }
               
               
               
                  $query = $this->db->query("SELECT 'product_name','product_price','image' FROM `product` WHERE id='$pro_id'");
               foreach ($query->result_array() as $mrow) {
                         
                            $product_id = $pro_id; 
                        //   $product_quantity = $row['quantity'];
                           $product_price = $mrow['product_price'];
                           $product_name = $mrow['product_name'];
                           $product_img = $mrow['image'];
        
               }
            //  echo 'product_id'.$pro_id;
              
            date_default_timezone_set('Asia/Kolkata');
            $order_date = date('Y-m-d H:i:s');
            $invoice_no = date("YmdHis");
            $order_status = 'Awaiting Confirmation';
            $order_total = '0';
            $action_by = 'customer';
            // $product_id = $medicine_product_data[$j]['product_id'];
            // $product_quantity = explode(",", $order_product_quantity);
         //   $product_price = explode(",", $order_product_price);
           // $cnt = count($product_id);
            // for ($i = 0; $i < $cnt; $i++) {
                $order_total = $order_total + ($product_price * $product_quantity);
            //}
           
           
            $sub_total = '0';
            $product_status = '';
            $product_status_type = '';
            $product_status_value = '';
            $product_order_status = 'Awaiting Confirmation';
            // $product_id = explode(",", $order_product_id);
            // $product_quantity = explode(",", $order_product_quantity);
            // $product_price = explode(",", $order_product_price);
            // $product_name = explode(",", $order_product_name);
            // $product_img = explode(",", $order_product_img);
            $product_unit = '';
            $product_unit_value = '';
            // $cnt = count($product_id);
            // for ($i = 0; $i < $cnt; $i++) {
                $sub_total = $product_price * $product_quantity;
                $product_order = array(
                    'order_id' => $order_id1,
                    'product_name' => $product_name,
                    'product_img' => str_replace('https://d2c8oti4is0ms3.cloudfront.net/images/product_images/', '', $product_img),
                    'product_id' => $product_id,
                    'product_quantity' => $product_quantity,
                    'product_price' => $product_price,
                    'sub_total' => $sub_total,
                    'product_status' => $product_status,
                    'product_status_type' => $product_status_type,
                    'product_status_value' => $product_status_value,
                    'product_unit' => $product_unit,
                    'product_unit_value' => $product_unit_value,
                    'order_status' => $product_order_status
                );
                $this->db->insert('user_order_product', $product_order);
                
          //  }
            
          }
          
           //addded for send gcm notification and text messagess to user and vendor
          if ($order_id1 > 0) {
            
           
            
            $order_date = date('j M Y h:i A', strtotime($order_date));
            $order_info = $this->db->select('token, agent, token_status')->from('users')->where('id', $user_id)->get()->row();
            $order_info_count = $this->db->select('token, agent, token_status')->from('users')->where('id', $user_id)->get()->num_rows();
            if($order_info_count>0)
            {
            $token_status = $order_info->token_status;
            if ($token_status > 0) {
                $reg_id = $order_info->token;
                $agent = $order_info->agent;
           //     $msg = 'Thanks uploading your prescription with ' . $listing_name;
                 $msg = 'Thanks for placing your order with ' . $listing_name;
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag = 'text';
                $key_count = '1';
                $title = 'Order Placed';
                
        //           //added by zak for all notification therough services
        //         $notification_array = array(
        //               'title' => $title,
        //               'msg'  => $msg,
        //               'img_url' => $img_url,
        //               'tag' => $tag,
        //               'order_status' => $order_status,
        //               'order_date' => $order_date,
        //               'order_id'   => $order_id,
        //               'post_id'  => "",
        //               'listing_id'  => "",
        //               'booking_id'  => "",
        //               'invoice_no' => $invoice_no,
        //               'user_id'  => $user_id,
        //               'notification_type'  => 'prescription',
        //               'notification_date'  => date('Y-m-d H:i:s')
                       
        //     );
        //  $this->db->insert('All_notification_Mobile', $notification_array);
        //end 
                
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id1, $invoice_no, $name, $listing_name, $agent);
            }
            }
            //$partner_info = $this->db->select('token, agent, token_status,phone')->from('users')->where('id', $listing_id)->get()->row();
           
            $listing_id =  explode(',', $listing_id);
        $listing_name = explode(',',$listing_name);
           
           for($i = 0 ; $i<count($listing_id); $i++)
             {           
            $partner_info = $this->db->query('SELECT web_token, agent, token_status,phone from users where id= '.$listing_id[$i]);
           // echo 'SELECT web_token, agent, token_status,phone from users where id= '.$listing_id[$i];
            $partner_token_status = $partner_info->num_rows();
            $partner_info = $partner_info->row();
            if ($partner_token_status > 0) {
                $partner_token_status = $partner_info->token_status;
                $partner_phone = $partner_info->phone;
                $reg_id = $partner_info->web_token;
                $agent = $partner_info->agent;
                $msg = 'You Have Received a New Order kindly Confirm it First';
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag = 'text';
                $key_count = '2';
                $title = 'New Order';
                //web notification starts
                $pharmacy_notifications = array(
                    'listing_id' => $listing_id[$i],
                    'order_id' => $order_id1,
                    'title' => $title,
                    'msg' => $msg,
                    'image' => $img_url,
                    'notification_type' => 'prescription',
                    'order_status' => $order_status,
                    'order_date' => $order_date,
                    'invoice_no' => $invoice_no
                );
                $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
                //web notification ends
                
                
                
                
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id1, $invoice_no, $name, $listing_name[$i], $agent);
            
                //sms same as order
                // $message = 'You have received an order, kindly login to https://pharmacy.medicalwale.com';
                // $post_data = array('From' => '02233721563', 'To' => $partner_phone, 'Body' => $message);
                // $exotel_sid = "aegishealthsolutions";
                // $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                // $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_VERBOSE, 1);
                // curl_setopt($ch, CURLOPT_URL, $url);
                // curl_setopt($ch, CURLOPT_POST, 1);
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                // curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                // $http_result = curl_exec($ch);
                // curl_close($ch);
                // //sms same to nyla,abdul, zaheer
                // $message2 = 'There is new order in pharmacy store. Pharmacy Name-' . $listing_name[$i] . ', Pharmacy Mobile- ' . $mobile . ', Order Id-' . $order_id1 . ', Order Date-' . $order_date . '.';
                // $post_data2 = array('From' => '02233721563', 'To' => '9619294702,7506908285', 'Body' => $message2);
                // $exotel_sid2 = "aegishealthsolutions";
                // $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
                // $url2 = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
                // $ch2 = curl_init();
                // curl_setopt($ch2, CURLOPT_VERBOSE, 1);
                // curl_setopt($ch2, CURLOPT_URL, $url2);
                // curl_setopt($ch2, CURLOPT_POST, 1);
                // curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
                // curl_setopt($ch2, CURLOPT_FAILONERROR, 0);
                // curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
                // curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
                // $http_result2 = curl_exec($ch2);
                // curl_close($ch2);
            }
             }
            
        }
          
        
        $final_order = $order_id.','.$order_id1;
         
         return $order_id;
       }*/
    
    //end
    
      //addded for order_details for perticular order_id 
    public function order_details_by_id($user_id,  $order_id)
    {
        $gst_per = $chc = $order_amount = $gst_rs = $grand_total = "";
        $dis = 0;
        $this->load->model('All_booking_model');
        $tracker = array();
         $query = $this->db->query("select actual_cost,chc,gst,discount,delivery_charges_by_customer,action_by,order_id,order_type,delivery_time,listing_id,cancel_reason,action_by,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status,rxId from user_order where user_id='$user_id'  and invoice_no='$order_id' group by invoice_no order by order_id desc");
       
                
       
        $count = $query->num_rows();
        if ($count > 0) { 
            $tracker = $this->All_booking_model->pharmacy_tracker($user_id, $order_id);
       
            foreach ($query->result_array() as $row) {
                
                
                $order_id = $row['order_id'];
                $order_type = $row['order_type'];
                $delivery_time = $row['delivery_time'];
                $listing_id = $row['listing_id'];
                $listing_name = $row['listing_name'];
                $listing_type = $row['listing_type'];
                $invoice_no = $row['invoice_no'];
                $chat_id = $row['chat_id'];
                $address_id = $row['address_id'];
                $name = $row['name'];
                $mobile = $row['mobile'];
                $pincode = $row['pincode'];
                $address1 = $row['address1'];
                $address2 = $row['address2'];
                $landmark = $row['landmark'];
                $city = $row['city'];
                $state = $row['state'];
                $action_by = $row['action_by'];
                $payment_method = $row['payment_method'];
                $order_date = $row['order_date'];
                $order_date = date('j M Y h:i A', strtotime($order_date));
                $delivery_charge = $row['delivery_charge'];
                $order_status = $row['order_status'];
                $order_type = $row['order_type'];
                $action_by = $row['action_by'];
                $rxId = $row['rxId'];
                $orderId = "";
                
                /*Added by swapnali on 20th nov 2k19 at 5pm*/
                $delivery_charge = $row['delivery_charge'];
                $delivery_charges_by_customer = $row['delivery_charges_by_customer'];
                $order_status = $row['order_status'];
                $order_type = $row['order_type'];
                $action_by = $row['action_by'];
                $rxId = $row['rxId'];
                
                
                    $delivery_charge = $delivery_charges_by_customer;
                
                $dis = $row['discount'];
                $gst_per = $row['gst'];
                $chc = strval($row['chc']);
                $order_amount = strval($row['actual_cost']);
                $gst_rs = strval(($order_amount - $dis)  * ($gst_per / 100));
                $grand_total = strval($order_amount + $gst_rs + $chc - $dis + $delivery_charge); 
                /*Added by swapnali on 20th nov 2k19 at 5pm => END*/
                
               //added by zak for maintain medlife cancel order 
                  $is_cancel = 'false';
                if($rxId != 'NULL')
                {
                    $query = $this->db->query("select orderState,orderId from medlife_order where rxID='$rxId' order by id desc");
                $medlife_order =  $query->row();
                $medlife_order_num =  $query->num_rows();
                // = $medlife_order->orderState;
                 //    $medlife_order= $this->db->select('orderState')->from('medlife_order')->where('rxID', $rxId)->get()->row();
                 if($medlife_order_num>0)
                 {
                     $medlife_status = $medlife_order->orderState;
                     $order_status = $medlife_order->orderState;
                     $orderId = $medlife_order->orderId;
                     if($medlife_status == 'Received')
                     {
                         $is_cancel = 'true';
                     }
                     else
                     {
                         $is_cancel = 'false';
                     }
                 }
                 else
                 {
                     $is_cancel = 'false';
                 }
                }
                
                
                if ($action_by == 'vendor') {
                    $cancel_reason = $row['cancel_reason'];
                } else {
                    $cancel_reason = '';
                }
                $user_info = $this->db->query("SELECT name,phone FROM users WHERE id='$user_id'");
                $getuser_info = $user_info->row_array();
                $user_name = $getuser_info['name'];
                $user_mobile = $getuser_info['phone'];
                $prescription_resultpost=array();
                $product_resultpost  = array();
                $prescription_result  = array();
                 $query1 = $this->db->query("select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where invoice_no='$invoice_no'");
                  $count1 = $query1->num_rows();
                  $order_total=0;
                  $sub_total_sum2=0;
                  $sub_total_sum1=0;
                  $sub_total_discount=0;
                  $sub_total_discount1=0;
                  if ($count1 > 0) {
            foreach ($query1->result_array() as $row1) {
                    $order_id1            = $row1['order_id'];
                    $product_query = $this->db->query("select id as product_order_id,product_id,product_discount,product_name,product_unit,product_unit_value,product_img,product_quantity,product_price,sub_total,product_status,product_status_type,product_status_value,order_status from user_order_product where order_id='$order_id1' order by product_order_id asc");
                    $product_count = $product_query->num_rows();
                    if ($product_count > 0) {
                        foreach ($product_query->result_array() as $product_row) {
                           
                            $product_order_id     = $product_row['product_order_id'];
                            $product_id           = $product_row['product_id'];
                            $product_name         = $product_row['product_name'];
                            $product_img          = $product_row['product_img'];
                            $product_price        = $product_row['product_price'];
                            $product_unit         = $product_row['product_unit'];
                            $product_unit_value   = $product_row['product_unit_value'];
                            $product_quantity     = $product_row['product_quantity'];
                            $product_discount     = $product_row['product_discount'];
                            $sub_total            = $product_row['sub_total'];
                            $product_status       = $product_row['product_status'];
                            $product_status_type  = $product_row['product_status_type'];
                            $product_status_value = $product_row['product_status_value'];
                            $product_order_status = $product_row['order_status'];
                            
                            $sub_total_sum1      += $product_price * $product_quantity;
                            $sub_total_discount +=$product_discount;
                            $product_resultpost[] = array(
                                "order_id" => $order_id1,
                                "product_order_id" => $product_order_id,
                                "product_id" => $product_id,
                                "product_name" => $product_name,
                                "product_img" => $product_img,
                                "product_quantity" => $product_quantity,
                                "product_price" => $product_price,
                                "product_unit" => $product_unit,
                                "product_unit_value" => $product_unit_value,
                                "product_discount" => $product_discount,
                                "sub_total" => $sub_total,
                                "product_status" => $product_status,
                                "product_status_type" => $product_status_type,
                                "product_status_value" => $product_status_value,
                                "product_order_status" => $product_order_status
                            );
                        }
                    }
              
                    $product_query1 = $this->db->query("SELECT id as product_order_id, order_status,prescription_image,prescription_image2,prescription_image3,prescription_image4 FROM prescription_order_details WHERE order_id='$order_id1' order by product_order_id asc");
                    $product_count1 = $product_query1->num_rows();
                    if ($product_count1 > 0) {
                        foreach ($product_query1->result_array() as $product_row1) {
                            $product_order_id1     = $product_row1['product_order_id'];
                            $product_id1           = $product_row1['product_order_id'];
                            $product_name1         = '';
                            $product_img1          = '';
                            $product_img1          = $product_row1['prescription_image'];
                            $product_quantity1     = '';
                            $product_price1        = '';
                            $sub_total1            = '';
                            $product_status1       = '';
                            $product_status_type1  = '';
                            $product_status_value1 = '';
                            $product_order_status1 = $product_row1['order_status'];
                              if (strpos($product_img1, '/') == true) {
                                $images_1 = "https://d2c8oti4is0ms3.cloudfront.net/" . $product_img1;
                           }
                           else
                           {
                               $images_1= "https://d2c8oti4is0ms3.cloudfront.net/images/prescription_images/" . $product_img1;
                           }
                            $prescription_resultpost[] = array(
                                "order_id" => $order_id1,
                                "product_order_id" => $product_order_id1,
                                "product_id" => $product_id1,
                                "product_name" => $product_name1,
                                "product_img" =>  $images_1,
                                "product_quantity" => $product_quantity1,
                                "product_price" => $product_price1,
                                "product_unit" => '',
                                "product_unit_value" => '',
                                "product_discount" => '0',
                                "sub_total" => $sub_total1,
                                "product_status" => $product_status1,
                                "product_status_type" => $product_status_type1,
                                "product_status_value" => $product_status_value1,
                                "product_order_status" => $product_order_status1
                            );
                        }
                        
                        
                        $prescription_query = $this->db->query("SELECT * FROM `prescription_order_list` where order_id='$order_id' order by id desc");
                        $prescription_count = $prescription_query->num_rows();
                        if ($prescription_count > 0) {
                            foreach ($prescription_query->result_array() as $prescription_row) {
                                $finalamt=$prescription_row['prescription_price']*$prescription_row['prescription_quantity'];
                                $sub_total_sum2+=$finalamt;
                                
                                $prescription_name     = $prescription_row['prescription_name'];
                                $prescription_quantity = $prescription_row['prescription_quantity'];
                                $prescription_price    = $prescription_row['prescription_price'];
                                $prescription_discount = $prescription_row['prescription_discount'];
                                $prescription_status   = $prescription_row['prescription_status'];
                               $sub_total_discount1 += $prescription_discount;
                                $prescription_result[] = array(
                                    "prescription_name" => $prescription_name,
                                    "prescription_quantity" => $prescription_quantity,
                                    "prescription_price" => $prescription_price,
                                    "prescription_discount" => $prescription_discount,
                                    "prescription_status" => $prescription_status
                                );
                                
                            }
                        }
                        
                    }
                
                   
            }
      }
$order_total=$sub_total_sum1+$sub_total_sum2;
$order_total_discount=$sub_total_discount+$sub_total_discount1;
if($order_total_discount=="")
{
    $order_total_discount=0;
}
else
{
    $order_total_discount;
}
 $user_info_user = $this->db->query("SELECT medical_name,payment_type FROM medical_stores WHERE user_id='$listing_id'");
    $getuser_info_user = $user_info_user->row_array();    
   $listing_name=$getuser_info_user['medical_name'];
   if($getuser_info_user['payment_type']!=null || !empty($getuser_info_user['payment_type']))
   {
   $listing_paymode=$getuser_info_user['payment_type'];
   } 
   else
   {
       $listing_paymode="Cash On Delivery";
   }
                $resultpost[] = array(
                    "order_id" => $order_id,
                    "medlife_order_id" => $orderId,
                    "delivery_time" => str_replace('null', '', $delivery_time),
                    "order_type" => $order_type,
                    "listing_id" => $listing_id,
                    "listing_name" => $listing_name,
                    "listing_type" => $listing_type,
                    "listing_payment_mode"=>$listing_paymode,
                    "invoice_no" => $invoice_no,
                    "chat_id" => $chat_id,
                    "address_id" => $address_id,
                    "name" => $name,
                    "mobile" => $mobile,
                    "pincode" => $pincode,
                    "address1" => $address1,
                    "address2" => $address2,
                    "landmark" => $landmark,
                    "city" => $city,
                    "state" => $state,
                    "user_name" => $user_name,
                    "user_mobile" => $user_mobile,
                    "order_total" => $order_total,
                    "order_discount"=>$order_total_discount,
                    "gst_rs" => $gst_rs,
                    "gst_per" => $gst_per,
                    "chc" => $chc,
                    "grand_total" => $grand_total,
                    "payment_method" => $payment_method,
                    "order_date" => $order_date,
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    "delivery_charge" => $delivery_charge,
                    "product_order" => $product_resultpost,
                    "tracker" => $tracker,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result,
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }
    
    
    // update status
    public function update_status( $invoice_no,$status, $action_by){
       // $created_at = date('Y-m-d H:i:s');
        $checkStatus = $this->db->query("SELECT * FROM `user_order_tracking` WHERE `invoice_no` LIKE '$invoice_no' AND `status` LIKE '$status' AND `action_by` LIKE '$action_by' ")->result_array();
        if(sizeof($checkStatus) == 0){
            $this->db->query("INSERT INTO `user_order_tracking`(`invoice_no`, `status`, `action_by`) VALUES ( '$invoice_no','$status', '$action_by')");
        }
    
        return 1;
    }
  
    
}

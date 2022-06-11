<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class BachatHCModel extends CI_Model
{
    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";

    
    public function check_auth_client()
    {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key       = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return json_output(401, array(
                'status' => 401,
                'message' => 'Unauthorized.'
            ));
        }
    }
    
    
    public function auth()
    {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token    = $this->input->get_request_header('Authorizations', TRUE);
        $q        = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
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
                $expired_at = '2018-11-12 08:57:58';
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
    
    
    public function get_bachat_details($user_id)
    {
        $card_list=array();
        $query = $this->db->query("SELECT * FROM `user_card_detail` WHERE status='0'");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $card_id  = $row['id'];
                $price   = $row['main_amt'];
                $discount_price=$row['discount_price'];
                $card_image=$row['card_image'];
                $card_description=$row['msg'];
                $card_validity=$row['card_validity'];
                $buy="0";
                $date=date('Y-m-d');
                $query1 = $this->db->query("SELECT * FROM `user_privilage_card` where `user_card_id` = '$card_id' and user_id='$user_id'");
                
                $count1 = $query1->num_rows();
                if ($count1 > 0) 
                   {
                     $buy="1";  
                   }
                   
                
                $card_list[] = array(
                    "card_id"=>$card_id,
                    "card_name" =>"Bachat Health Card",
                    "price" => $price,
                    "discount_price"=>$discount_price,
                    "card_image"=>$card_image,
                    "card_description"=>$card_description,
                    "card_validity"=>$card_validity,
                    "buy_card"=>$buy
                    
                );
               
            }
        }
        return $card_list; 
    }
    
    
    public function get_bachat_service_details($user_id,$card_id)
    {
        
        $card_list=array();
        $services=array();
        $offer=array();
        $query = $this->db->query("SELECT * FROM `user_card_detail` WHERE status='0' and id='$card_id'");
        $count = $query->num_rows();
        if ($count > 0) {
           $row= $query->row_array();
                $card_id  = $row['id'];
                $price   = $row['main_amt'];
                $discount_price=$row['discount_price'];
                $card_image=$row['card_image'];
                $card_description=$row['msg'];
                $card_validity=$row['card_validity'];
              
                // service details
                $query1 = $this->db->query("SELECT * FROM `user_card_services` where card_id='$card_id'");
        if (sizeof($query1) > 0) {
            foreach ($query1->result_array() as $row) {
                $service_id  = $row['service_id'];
                $service_name   = $row['service_name'];
                $service_vendor_type  = $row['service_vendor_type'];
                $service_vendor_id   = $row['service_vendor_id'];
                $service_vendor_name  = $row['service_vendor_name'];
                $service_mrp   = $row['service_mrp'];
                $service_offer   = $row['service_offer'];
                $service_duration   = $row['service_duration'];
                $service_image =  $row['service_image'];
                $service_description = $row['service_details'];
                
                
                $services[] = array(
                    "service_id"=>$service_id,
                    "service_name" =>$service_name,
                    "service_vendor_type" => $service_vendor_type,
                    "service_vendor_id"=>$service_vendor_id,
                    "service_vendor_name"=>$service_vendor_name,
                    "service_mrp"=>$service_mrp,
                    "service_offer" => $service_offer,
                    "service_duration" => $service_duration,
                    "service_image" => $service_image,
                    "service_description" => $service_description,
                );
            }
        }
        
       
                
                //offer details
                $date=date('Y-m-d');
                /*echo "SELECT * FROM `vendor_offers` where end_date >= '$date' and listing_id='35'";*/
                $query2 = $this->db->query("SELECT * FROM `vendor_offers` where end_date >= '$date' and listing_id='35'");
        if (sizeof($query2) > 0) {
            foreach ($query2->result_array() as $row1) {
                $offer_id  = $row1['id'];
                $offer_name   = $row1['name'];
                $offer_mrp  = $row1['max_discound'];
                $offer_discount   = $row1['offer_on'];
                $offer_description=$row1['offer_description'];
                
                
                $offer[] = array(
                    "offer_id"=>$offer_id,
        "offer_name"=>$offer_name,
        "offer_mrp"=>$offer_mrp,
        "offer_discount"=>$offer_discount,
        "offer_code"=>$offer_name,
        "offer_description"=>$offer_description
                );
            }
        }
               
                 $buy="0";
                $date=date('Y-m-d');
                $query1 = $this->db->query("SELECT * FROM `user_privilage_card` where `user_card_id` = '$card_id' and user_id='$user_id'");
                $count1 = $query1->num_rows();
                if ($count1 > 0) 
                   {
                     $buy="1";  
                   }
                   
                $card_list[] = array(
                    "card_id"=>$card_id,
                    "card_name" =>"Bachat Health Card",
                    "price" => $price,
                    "discount_price"=>$discount_price,
                    "card_image"=>$card_image,
                    "card_description"=>$card_description,
                     "card_validity"=>$card_validity,
                     "buy"=>$buy,
                    "benifits" =>$services,
                    "offer" =>$offer
                );
               
            
        }
        return $card_list; 
    }
    
   public function create_bachat($user_id,$coupon_id, $amount, $payment_method, $transaction_id, $trans_status,$card_type) 
   {
      date_default_timezone_set('Asia/Kolkata');
      $booking_id = date("YmdHis");
      $created_at = date('Y-m-d H:i:s');
      $response = array();
      if($trans_status == 1){
         $response = $this->LoginModel->generate_privilage_new($user_id,$coupon_id,$amount,$card_type);
            if(array_key_exists('id',$response)){
                $card_id = $response['id'];
                $card_no = $response['card_no'];
                $is_active = $response['is_active'];
            } else {
                $card_id = "";
                $is_active=0;
            }
            
            if($is_active=="1")
            {
                // Debit Entry
                $mw_id = $mw_id_type = $user_id_type = 0;
                $invoice_no = $booking_id;
                $user_comments = $card_no;
                $mw_comments = "Paid for bachat health card";
                $vendor_comments = $card_no;
                $credit = 0;
                $debit = $amount;
                $transaction_of = 2; // entry for amount
                $order_type = 6; // BHC
                $transaction_id = $transaction_id;
                $transaction_date = $created_at;
                $vendor_id = 35;
                $array_data = array();
                $res = $this->LedgerModel->create_ledger($user_id, $invoice_no, $user_id_type, $mw_id, $mw_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status,$transaction_date,$vendor_id,$array_data);
               
               // credit Entry
                $mw_id1 = $mw_id_type1 = $user_id_type1 = 0;
                $invoice_no1 = $booking_id;
                $user_comments1 = $card_no;
                $mw_comments1 = "Paid for bachat health card";
                $vendor_comments1 = $card_no;
                //$payment_method = "";
                $credit1 = $amount;
                $debit1 = 0;
                $transaction_of1 = 2; // entry for amount
                $order_type1 = 6; // BHC
                $transaction_id1 = $transaction_id;
                //$trans_status = 1;
                $transaction_date1 = $created_at;
                $vendor_id1 = 35;
                $array_data1 = array();
                $res1 = $this->LedgerModel->create_ledger($user_id, $invoice_no1, $user_id_type1, $mw_id1, $mw_id_type1, $credit1, $debit1, $payment_method, $user_comments1, $mw_comments1, $vendor_comments1,$order_type1,$transaction_of1,$transaction_id1,$trans_status,$transaction_date1,$vendor_id1,$array_data1);
             
                   $query1 = $this->db->query("SELECT * FROM `user_card_services` where card_id='$card_type'");
        if (sizeof($query1) > 0) {
            foreach ($query1->result_array() as $row) {
                $service_id  = $row['service_id'];
                $service_name   = $row['service_name'];
                $service_vendor_type  = $row['service_vendor_type'];
                $service_vendor_id   = $row['service_vendor_id'];
                $service_vendor_name  = $row['service_vendor_name'];
                $service_mrp   = $row['service_mrp'];
                $service_offer   = $row['service_offer'];
                $service_duration   = $row['service_duration'];
                $service_count = $row['service_count'];
                $service_description = $row['service_details'];
                $service_image = $row['service_image'];
                $service_listing_id=$row['listing_id'];
                $service_product_id=$row['product_id'];
                $service_package_id=$row['package_id'];
                $service_test_id=$row['test_id'];
                $service_center=$row['service_center'];
                $missbelly_month=$row['missbelly_month'];
                $start_date=date('Y-m-d');
                $end_date = date('Y-m-d', strtotime("+$service_duration", strtotime($start_date)));
                
               
                $user_data = array(
                        'service_card_id'=>$service_id,
                        'user_id'=>$user_id,
                        'ecard' => $card_id,
                        'listing_id'=>$service_listing_id,
                        'test_id'=>$service_test_id,
                        'package_id'=>$service_package_id,
                        'product_id'=>$service_product_id,
                        'service_center'=>$service_center,
                        'card_type'=>$card_type,
                        'missbelly_month'=>$missbelly_month,
                        'service_name' => $service_name,
                        'service_vendor_type' => $service_vendor_type,
                        'service_vendor_id' => $service_vendor_id,
                        'service_vendor_name' => $service_vendor_name,
                        'service_details' => $service_description,
                        'service_mrp' => $service_mrp,
                        'service_count' => $service_count,
                        'service_duration'=>$service_duration,
                        'service_offer'=>$service_offer,
                        'service_image'=>$service_image,
                        'created_by'=>$created_at,
                        'created_date'=>$created_at,
                        'updated_by'=>$created_at,
                        'updated_date'=>$created_at,
                        'service_used_count'=>0,
                        'start_date'=>$start_date,
                        'end_date'=>$end_date,
                        'payment_method'=>$payment_method
                        
                    
                    );
                
                $this->db->insert('user_card_services_list', $user_data);
                $this->db->insert_id();
                
            }
        }
             
             
                
            }
           
            
            
        } else {
            // payment failed
            $response = array('status' => 204);
        }
        return $response;
   }
    
    
    public function get_user_bachat_list($user_id)
    {
        $card_list=array();
        $services=array();
        $date=date('Y-m-d');
        //$query = $this->db->query("SELECT * FROM `user_privilage_card` WHERE  user_id='$user_id' and end_date >='$date'");
        $query = $this->db->query("SELECT * FROM `user_privilage_card` WHERE  user_id='$user_id'");
        $count = $query->num_rows();
        if ($count > 0) 
        {
             foreach ($query->result_array() as $row1)
             {
        
                $card_id  = $row1['id'];
                $price   = $row1['pay_type'];
                $card_number=$row1['card_no'];
                  $card_type=$row1['card_type'];
                $start_date=$row1['start_date'];
                $end_date=$row1['end_date'];
                // service details
                $query1 = $this->db->query("SELECT * FROM `user_card_services_list` where ecard='$card_id'");
                 if (sizeof($query1) > 0) {
            foreach ($query1->result_array() as $row) {
                $service_id  = $row['service_id'];
                $service_name   = $row['service_name'];
                $service_vendor_type  = $row['service_vendor_type'];
                $service_vendor_id   = $row['service_vendor_id'];
                $service_vendor_name  = $row['service_vendor_name'];
                $service_mrp   = $row['service_mrp'];
                $service_offer   = $row['service_offer'];
                $service_duration   = $row['service_duration'];
                $service_image = $row['service_image'];
                $service_description = $row['service_details'];
                $service_used_count=$row['service_used_count'];
                $service_listing_id=$row['listing_id'];
                $service_product_id=$row['product_id'];
                $service_package_id=$row['package_id'];
                $service_test_id=$row['test_id'];
                $service_center=$row['service_center'];
                $service_count=$row['service_count'];
                $missbelly=$row['missbelly_month'];
                $payment_method=$row['payment_method'];
                $services[] = array(
                    "service_id"=>$service_id,
                    "service_name" =>$service_name,
                    "service_vendor_type" => $service_vendor_type,
                    "service_vendor_id"=>$service_vendor_id,
                    "service_vendor_name"=>$service_vendor_name,
                    "service_mrp"=>$service_mrp,
                    "service_offer" => $service_offer,
                    "service_duration" => $service_duration,
                    "service_image" => $service_image,
                    "service_description" => $service_description,
                    'listing_id'=>$service_listing_id,
                    'test_id'=>$service_test_id,
                    'package_id'=>$service_package_id,
                    'product_id'=>$service_product_id,
                    'service_center'=>$service_center,
                    'missbelly'=>$missbelly,
                    "service_total_count"=>$service_count,
                    "service_total_used"=>$service_used_count,
                    "payment_method"=>$payment_method
                );
            }
        }
        
       
              
                   
                $card_list[] = array(
                    "card_id"=>$card_id,
                    "price" => $price,
                    "card_number"=>$card_number,
                    "card_type"=>$card_type,
                    "start_date"=>$start_date,
                    "end_date"=>$end_date,
                    "benifits" =>$services,
                   
                );
             }
        }
        return $card_list; 
    }
    
      public function get_user_bachat_list_detail($user_id,$card_id)
    {
        
        $card_list=array();
        $services=array();
        $date=date('Y-m-d');
        //$query = $this->db->query("SELECT * FROM `user_privilage_card` WHERE  user_id='$user_id' and id='$card_id' and end_date >='$date'");
        $query = $this->db->query("SELECT * FROM `user_privilage_card` WHERE  user_id='$user_id'");
        $count = $query->num_rows();
        if ($count > 0) {
             foreach ($query->result_array() as $row1)
             {
        
                $card_id  = $row1['id'];
                $price   = $row1['pay_type'];
                $card_number=$row1['card_no'];
                 $card_type=$row1['card_type'];
                $start_date=$row1['start_date'];
                $end_date=$row1['end_date'];
                // service details
                $query1 = $this->db->query("SELECT * FROM `user_card_services_list` where ecard='$card_id'");
                 if (sizeof($query1) > 0) {
            foreach ($query1->result_array() as $row) {
                $service_id  = $row['service_id'];
                $service_name   = $row['service_name'];
                $service_vendor_type  = $row['service_vendor_type'];
                $service_vendor_id   = $row['service_vendor_id'];
                $service_vendor_name  = $row['service_vendor_name'];
                $service_mrp   = $row['service_mrp'];
                $service_offer   = $row['service_offer'];
                $service_duration   = $row['service_duration'];
                $service_image = $row['service_image'];
                $service_description = $row['service_details'];
                $service_used_count=$row['service_used_count'];
                $service_listing_id=$row['listing_id'];
                $service_product_id=$row['product_id'];
                $service_package_id=$row['package_id'];
                $service_test_id=$row['test_id'];
                $service_center=$row['service_center'];
                $service_count=$row['service_count'];
                $missbelly=$row['missbelly_month'];
                $payment_method=$row['payment_method'];
                $services[] = array(
                    "service_id"=>$service_id,
                    "service_name" =>$service_name,
                    "service_vendor_type" => $service_vendor_type,
                    "service_vendor_id"=>$service_vendor_id,
                    "service_vendor_name"=>$service_vendor_name,
                    "service_mrp"=>$service_mrp,
                    "service_offer" => $service_offer,
                    "service_duration" => $service_duration,
                    "service_image" => $service_image,
                    "service_description" => $service_description,
                    'listing_id'=>$service_listing_id,
                    'test_id'=>$service_test_id,
                    'package_id'=>$service_package_id,
                    'product_id'=>$service_product_id,
                    'service_center'=>$service_center,
                    'missbelly'=>$missbelly,
                    "service_total_count"=>$service_count,
                    "service_total_used"=>$service_used_count,
                    "payment_method"=>$payment_method
                );
            }
        }
        
       
              
                   
                $card_list[] = array(
                    "card_id"=>$card_id,
                    "price" => $price,
                    "card_number"=>$card_number,
                    "card_type"=>$card_type,
                    "start_date"=>$start_date,
                    "end_date"=>$end_date,
                    "benifits" =>$services,
                   
                );
             }
            
        }
        return $card_list; 
    }
    
    public function missbelly_booking($user_id,$month,$package,$bachat_service_id) {
        $booking_id = date('YmdHis');
        $new_deit_entry = array(
                    'user_id' => $user_id,
                    'enroll_from' => "Bachat Card",
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $user_id
                );

                $this->db->insert('diet_leads', $new_deit_entry);
                $lead_id = $this->db->insert_id();
               
                /*$month = "";
                $this->db->select('month');
                $this->db->from('diet_master_package');
                $this->db->where('id', 1);
                $query1 = $this->db->get();
                $month = $query1->row()->month;

                $month1 = "+$month";*/
                $effectiveDate1 = date('Y-m-d');
                $effectiveDate = date('Y-m-d', strtotime("+$month", strtotime($effectiveDate1)));


                $new_booking_lead = array(
                    'user_id' => $user_id,
                    'package_id' => $package,
                    'leads_id' => $lead_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'booking_from_date' => date('Y-m-d'),
                    'booking_to_date' => $effectiveDate,
                    'created_by' => $user_id,
                    'status' => '1'
                );

                $this->db->insert('diet_packages_booked', $new_booking_lead);

                $notification = array('user_id' => $user_id,
                    'listing_id' => "",
                    'package_id' => $package,
                    'booking_id' => $booking_id,
                    'order_id' => 0,
                    'title' => "Diet Package Booking",
                    'msg' => "Diet Package Booked Through Ecard.",
                    'notification_type' => "Diet Booking",
                    'created_at' => date('Y-m-d H:i:s')
                );
                $this->db->insert('diet_plan_notifications', $notification);


                
                /*$booking_history = array('user_id' => $user_id,
                    'leads_id' => $lead_id,
                    'package_id' => 1,
                    'booking_id' => $booking_id,
                    'booking_from' => date('Y-m-d'),
                    'booking_to' => $effectiveDate,
                    'created_at' => date('Y-m-d H:i:s')
                );*/
                
                $booking_history = array('user_id' => $user_id,
                    'leads_id' => $lead_id,
                    'package_id' => $package,
                    'booking_id' => $booking_id,
                    'booking_from' => '0000-00-00',
                    'booking_to' => '0000-00-00',
                    'created_at' => date('Y-m-d H:i:s')
                );

                $this->db->insert('diet_user_package_history', $booking_history);
                $diet_booking_id = $this->db->insert_id();
                //end 
                //added by jakir on 01-june-2018 for notification on activation 
                $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                
                $booking_history_bachat =   array('user_id' => $user_id,
                                            'booking_id' => $booking_id,
                                            'vendor_type' => 37,
                                            'vendor_id' => 0,
                                            'booking_date' => date('Y-m-d H:i:s'),
                                            'created_at' => date('Y-m-d H:i:s')
                                        );
                
                $this->db->insert('bachat_card_booking',$booking_history_bachat);
                 $up=$this->db->query("UPDATE `user_card_services_list` SET `service_used_count`=service_used_count+1 WHERE service_id='$bachat_service_id'");
                 $response[] = array(
                'booking_id' => $booking_id,
                'ledger_id' => "0",
                'ledger_type' => ""
            ); 
            return array(
                'status' => 200,
                'message' => 'success',
                'data' => $response
            );
    }
    
     public function dentist_branch_list($user_id,$listing_id,$lat,$lng,$page)
    {
               $radius = '5';

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
            $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
            return $dist;
        }
        
        if($page==""){
         $limit = " LIMIT 0, 10";   
        }
        else
        {
            $limit = 10;
            $start = 0;
            if ($page > 0) {
                if (!is_numeric($page)) {
                    $page = 1;
                }
            }
            $start = ($page - 1) * $limit;
            $limit = " LIMIT $start, $limit";   
        }
        $sql = "SELECT * FROM dentists_branch  WHERE hub_user_id='$listing_id' $limit";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
          
        if($count>0)
        {
            foreach($query->result_array() as $row)
            {
                   $ids                     = $row['id'];
                   $branch_id               = $row['dentists_branch_user_id'];
                   $name_of_hospital        = $row['name_of_hospital'];
                   $about_us                = $row['about_us'];
                   $establishment_year      = $row['establishment_year'];
                   $speciality              = $row['speciality'];
                   $category                = $row['category'];
                   $surgery                 = $row['surgery'];
                   $services                = $row['services'];
                   $address                 = $row['address'];
                   $address_2               = $row['address_2'];
                   $pincode                 = $row['pincode'];
                   $phone                   = $row['phone'];
                   $city                    = $row['city'];
                   $state                   = $row['state'];
                   $email                   = $row['email'];
                   $lat1                     = $row['lat'];
                   $lng1                     =  $row['lng'];
                   $image                   =  $row['image'];
                   $rating                  = $row['rating'];
                   $review                  = $row['review'];
                   $user_discount           = $row['medicalwale_discount'];
                   $discount_description    = $row['description'];
                   $opening_hours     = $row['opening_hours'];
                   
                 $final_Day      = array();
                        $day_array_list = explode('|', $opening_hours);
                        $open_close_status = 'close';
                        if (count($day_array_list) > 1) {
                            for ($i = 0; $i < count($day_array_list); $i++) {
                                $day_list = explode('>', $day_array_list[$i]);
                                for ($j = 0; $j < count($day_list); $j++) {
                                    $day_time_list = explode('-', $day_list[$j]);
                                    for ($k = 1; $k < count($day_time_list); $k++) {
                                        $time_list1 = explode(',', $day_time_list[0]);
                                        $time_list2 = explode(',', $day_time_list[1]);
                                        $time       = array();
                                        $open_close = array();
                                        // print_r($time_list2); die();
                                        for ($l = 0; $l < count($time_list1) && $l < count($time_list2); $l++) {
                                            $time_check        = $time_list1[$l] . '-' . $time_list2[$l];
                                            $time[]            = str_replace('close-close', '', $time_check);
                                            $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                            $system_end_time   = date("H.i", strtotime($time_list2[$l]));
                                            $current_time      = date('H.i');
                                            if ($current_time >= $system_start_time && $current_time <= $system_end_time) {
                                                $open_close[] = 'open';
                                                $open_close_status = 'open';
                                                $st = date("h.i a", strtotime($system_start_time));
                                                        $et = date("h.i a", strtotime($system_end_time));
                                                        if($st == "00:00" &&  $et == "23:59"){
                                                              $open_hrs ="24 hrs";
                                                        } else {
                                                            $open_hrs = date("h.i a", strtotime($system_start_time)) ." - " . date("h.i a", strtotime($system_end_time)); 
                                                        
                                                        }
                                                
                                            } else {
                                                $open_close[] = 'close';
                                                $open_close_status = 'close';
                                            }
                                        }
                                    }
                                }
                                $final_Day[] = array(
                                    'day' => $day_list[0],
                                    'time' => $time,
                                    'status' => $open_close
                                );
                            }
                        } else {
                            $final_Day[] = array(
                                'day' => 'close',
                                'time' => array(),
                                'status' => array()
                            );
                        }
                        
                       
                    
                        $dfinal=date("l");
                        $open_hrs ="";
                        $current_day_final="";
                        $first ="";
                        $second ="";
                        $open_close_status ='close';
                        $counts = count($final_Day[0]['time']);
                        if($counts > 1)
                        {
                            foreach($final_Day as $key => $product)
                            {
                                
                                if($product['day']===$dfinal)
                                {
                                    if($product['time'][0] != "" && $product['time'][0] != 'close-close')  {
                                        $ex = explode('-', $product['time'][0]);
                                        $first = $ex[0];
                                        
                                        if($product['time'][$counts-1] != "" && $product['time'][$counts-1] != 'close-close') {
                                        $ex = explode('-', $product['time'][$counts-1]);
                                        $second = $ex[1]; }
                                        
                                        $current_day_final = $product['time'][0];
                                        $open_hrs = $first.'-'.$second;
                                        
                                        $d1 = date('Y-m-d H:i:s', strtotime($second));
                                        $d2 = date('Y-m-d H:i:s');
                                        if($d1 > $d2)
                                        {
                                            $open_close_status = 'open';
                                        }
                                    }
                                }
                            } 
                            
                        }
                        else if($counts == 1)
                        {
                            foreach($final_Day as $key => $product)
                            {
                            
                                if($product['day']===$dfinal)
                                {
                                    if($product['time'][0] !='' && $product['time'][0] != 'close-close') {
                                         $current_day_final = $product['time'][0];
                                         $open_hrs =$product['time'][0];
                                         $ex = explode('-', $product['time'][0]);
                                         $first = $ex[0];
                                         $second = $ex[1];
                                         
                                          $d1 = date('Y-m-d H:i:s', strtotime($second));
                                            $d2 = date('Y-m-d H:i:s');
                                            if($d1 > $d2)
                                            {
                                                $open_close_status = 'open';
                                            }
                                            
                                    }
                                }
                            }    
                        }
                
                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $image = '';
                }
                
                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $branch_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $branch_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $branch_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                
                  //Review Count
                $Review_query = $this->db->query("SELECT * FROM `hospital_review` WHERE hospital_id=$branch_id");
                $Review_count = $Review_query->num_rows();
                
                
                //Profile View
                //echo "SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'";
                $Profile_query = $this->db->query("SELECT * FROM `profile_views_master` WHERE listing_id=$branch_id and vendor_type='8'");
                $Profile_count = $Profile_query->num_rows();
                
                
                    if($user_discount == null || $user_discount == '' || $user_discount == NULL)
                    {
                        $user_discount = '0';
                    }
                   
                   if($rating == null || $rating == '' || $rating == NULL)
                    {
                        $rating = '4';
                    }
                    
                    
                    
                      $speciallity_array = array();
                      $speciallity_data = explode(',', $speciality);
                foreach ($speciallity_data as $speciallity_data) {
                    
                    if($speciallity_data != ""){
                        if(preg_match('/[0-9]/', $speciallity_data)){
                            $Query = $this->db->query("SELECT name FROM dentists_specialist WHERE id='$speciallity_data'");
                              $count = $Query->num_rows();
                            if($count>0)
                            {
                            $what_we_offer_value = $Query->row()->name;
                            $speciallity_array[] = $what_we_offer_value;
                            }
                            else
                            {
                                $speciallity_array[] = $speciallity_data;
                            }
                        }else{
                            $speciallity_array[] = $speciallity_data;
                        }
                        
                    }
                    
                }
                
                 //-----------------------service-------------------------
                $exp1=explode(',',$services);
                $treatments = array();
                $sub_category_details = array();
                $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE branch_id='$branch_id' group by category_id");
                $ser = $Querys->result();
                if(!empty($ser))
                {
                    foreach ($ser as $ser1) {
                        $sub_category_details = array();
                        $s_id = $ser1->id;
                        $category_id = $ser1->category_id;
                        
                        $Query = $this->db->query("SELECT * FROM dentist_services_master WHERE id='$category_id' ");
                        $main_service = $Query->row()->name;
                      
                        $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE category_id='$category_id' AND branch_id='$branch_id'");
                        $m_service = $Querys->result();    
                            if(!empty($m_service))
                            {
                                foreach ($m_service as $mser1) {
                                    $ids = $mser1->id;
                                    $service_name = $mser1->service_name;
                                    $price = $mser1->price;
                                    $discount = $mser1->discount;
                                    
                                        if(in_array($ids,$exp1))
                                        {
                                                $sub_category_details[] = array(
                                                    'id'         => $ids,
                                                                         'service_name' => $service_name,
                                                                      'price' => $price,
                                                                      'discount' => $discount
                                                                      );
                                        }
                                    
                                }
                                
                            }
                            
                            $treatments[] = array(
                                   
                                   'cat_name' => $main_service,
                                   'sub_catdata' => $sub_category_details
                            
                            );
                          
                    }
                }
                //print_r($treatments);
                
                //-----------------------packages-------------------------
                $packages = array();
                $sql_p = "SELECT * FROM packages WHERE user_id='$listing_id' AND FIND_IN_SET('" . $ids . "',branch)";
                $query_p = $this->db->query($sql_p);
                $count_p = $query_p->num_rows();
                  
                if($count_p>0)
                {
                    foreach($query_p->result_array() as $row)
                    {
                           $pid                   =  $row['id'];
                           $image                   =  $row['image'];
                           $package_name            = $row['package_name'];
                            $package_details        = $row['package_details'];
                            $price                  = $row['price'];
                            $discount               = $row['discount'];
                      
                        
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $image = '';
                        }
                        
            
               
                   
                $packages[] = array(
                       'id'                     => $pid,
                       'package_name'           => $package_name,
                        'package_details'       => $package_details,
                        'price'                 => $price,
                        'discount'              => $discount,
                        'image'                 => $image
                       );
               }
           }
           $fav_pharmacy = $this->db->select('id')->from('dentist_favourite')->where('user_id', $user_id)->where('listing_id', $branch_id)->get()->num_rows();
                            
                $distances = str_replace(',', '.', GetDrivingDistance($lat, $lat1, $lng, $lng1));
                
                $result_data[] = array(
                        'id'                    => $ids,
                       'listing_id'             => $branch_id,
                       'listing_type'           => '39',
                       'distance'               => $distances,
                       'name_of_clinic'         => $name_of_hospital,
                       'about_us'               => $about_us,
                       'establishment_year'     => $establishment_year,
                       'speciality'             => $speciallity_array,
                       'services'               => $services,
                       'packages'               => $packages,
                       'treatments'             => $treatments,
                       'address'                => $address,
                       'address_2'              => $address_2,
                       'pincode'                => $pincode,
                       'phone'                  => $phone,
                       'city'                   => $city,
                       'state'                  => $state,
                       'email'                  => $email,
                       'lat'                    => $lat,
                       'lng'                    => $lng,
                       'image'                  => $image,
                       'opening_day'            => $final_Day,
                               'open_hrs'               => $open_hrs,
                               'open_close_status'      => $open_close_status,
                       'rating'                 => $rating,
                       'review'                 => '0',
                       'followers'              => $followers,
                       'following'              => $following,
                        'profile_views'         => $Profile_count,
                        'is_follow'             => $is_follow,
                       'user_discount'          => $user_discount,
                       'discount_description'   => $discount_description,
                       'favourite'              => $fav_pharmacy,
                       //'mba'                    => $mba,
                       //'certified'              => $certified,
                       //'recommended'            => $recommended,
                       //'featured'               => $featured,
                       //'free_consultancy'       => $free_consultancy
                       );
               }
           }
           else
           {
               return $result_data = array();
           }
           
             function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
                    $sort_col = array();
                    foreach ($arr as $key => $row) {
                        $sort_col[$key] = $row[$col];
                    }
                    array_multisort($sort_col, $dir, $arr);
                }
       

        array_sort_by_column($result_data, 'distance');
           return $result_data;
     }
    
}

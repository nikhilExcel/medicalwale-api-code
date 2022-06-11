<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class All_booking_model extends CI_Model
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
   
    

    
    
 
    
    
    public function pharmacy_presciption_appointment_listing($user_id )
    {
          
          /*pharmacy prescription Start Here*/
                 $query = $this->db->query("SELECT user_order.*,medical_stores.medical_name FROM user_order LEFT JOIN medical_stores on user_order.listing_id=medical_stores.id WHERE  (user_order.listing_type='13' or user_order.listing_type='38' )  AND user_order.user_id='$user_id' group by user_order.invoice_no ");



                $count = $query->num_rows();
                if ($count > 0) {
                    
                       foreach ($query->result_array() as $row) {
              
               
                    $invoice_no = $row['invoice_no'];
                    $order_date = $row['order_date'];
                    $status  = $row['order_status'];
                    $customer_name              = $row['name'];
                    $customer_no                = $row['mobile'];
                    $address1          = $row['address1'];
                    $address2 = $row['address2'];
                    $landmark = $row['landmark'];
                    $pincode = $row['pincode'];
                    $city = $row['city'];
                    $state = $row['state'];
                    $payment_method = $row['payment_method'];
                 
                    $home_delievery_charge = $row['delivery_charge'];
                    $total_discount = $row['order_total'];

                

                $resultpost[] = array(
                    'invoice_no' => $invoice_no,
                    'order_date' => $order_date,
                    'order_status' => $status,
                    'name' => $customer_name,
                    'mobile' => $customer_no,
                    'address1' => $address1,
                    'address2' => $address2,
                    'landmark' => $landmark,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'payment_method' => $payment_method,
                    'delivery_charge' => $home_delievery_charge,
                    'order_total' => $total_discount,
                    'listing_type'=>$row['listing_type']
                );
            }
                   
                       
                
           
              
             
                } else {
                    $resultpost = array();
                }
                
                
                /*pharmacy Prescription End Here*/
                
                
                /*nursing booking Start Here*/
                $query1 = $this->db->query("SELECT ua.landmark,ua.address1,ua.address2,ua.state as patient_state,ua.city as patient_city,ua.pincode as patient_pincode,ua.name as patient_name,b.*,nas.*,nsb.id as order_id,nsb.user_id as change_user_id,u.name,u.phone,b.id as book, b.vendor_id,u.id as userid ,nsb.package_amount as amts,na.name as listing_name,na.contact as nursing_contact From nursing_attendant_services nas LEFT JOIN nursing_attendant na ON(nas.user_id=na.user_id) LEFT JOIN booking_master b ON(b.package_id=nas.id) LEFT JOIN users u ON(u.id=b.user_id) LEFT JOIN nursing_booking_details nsb ON(b.booking_id=nsb.booking_id) LEFT JOIN user_address ua ON(ua.user_id=b.user_id) where b.user_id ='$user_id' and b.vendor_id = '12'");
$count1 = $query1->num_rows();
   if ($count1 > 0) {
                    
                       foreach ($query1->result_array() as $row) {
              
               
                    $booking_id = $row['booking_id'];
                    $booking_date = $row['booking_date'];
                    $status  = $row['status'];
                    $user_name  = $row['user_name'];
                    $user_mobile = $row['user_mobile'];
                    $user_gender = $row['user_gender'];
                    $booking_date   = $row['booking_date'];
                    $listing_name   = $row['listing_name'];
                    $nursing_contact   = $row['nursing_contact'];
                    $patient_name   = $row['patient_name'];
                     $address1 = $row['address1'];
                    $address2 = $row['address2'];
                    $landmark = $row['landmark'];
                    $pincode = $row['patient_pincode'];
                    $city = $row['patient_city'];
                    $state = $row['patient_state'];
                    $payment_method = $row['payment_mode'];
                    $service_name = $row['service_name'];
                    $description = $row['description'];
                    $rate = $row['rate'];
                

                $resultpost1[] = array(
                    'booking_id' => $booking_id,
                    'booking_date' => $booking_date,
                    'status' => $status,
                    'user_gender'=>$user_gender,
                    'user_name' => $user_name,
                    'user_mobile' => $user_mobile,
                    'booking_date' => $booking_date,
                    'listing_name' => $listing_name,
                    'nursing_contact' => $nursing_contact,
                    'patient_name' => $patient_name,
                    'address1' => $address1,
                    'address2' => $address2,
                    'landmark' => $landmark,
                    'patient_pincode' => $pincode,
                    'patient_city' => $city,
                    'patient_state' => $state,
                    'payment_mode' => $payment_method,
                    'service_name' => $service_name,
                    'description' => $description,
                    'rate' => $rate,
                    'listing_type'=> $row['vendor_id']
                );
            }
                   
                       
                
           
              
             
                } else {
                    $resultpost1 = array();
                }
                
                /*Nursing  Booking End Here*/
                
                /*pharmacy general order Start here*/
                 /*  $query2 = $this->db->query("SELECT user_order.cancel_reason,user_order.user_id, user_order.action_by,user_order.is_night_delivery,user_order.order_status,user_order.order_id,user_order.pincode,user_order.delivery_charge,user_order.invoice_no,user_order.order_date,user_order.payment_method,user_order.name, user_order.address1,user_order.address2,user_order.landmark,user_order.city,user_order.state,user_order.mobile, user_order_product.product_name,user_order_product.product_unit,user_order_product.product_unit_value,user_order_product.id,user_order_product.product_img,user_order_product.product_quantity,user_order_product.product_price,user_order_product.product_discount,user_order_product.sub_total,users.name as customer_name FROM user_order LEFT JOIN user_order_product on user_order.order_id=user_order_product.order_id LEFT JOIN users on user_order.user_id=users.id WHERE user_order.user_id='$user_id'");
$count2 = $query2->num_rows();
   if ($count2 > 0) {
                    
                       foreach ($query2->result_array() as $row) {
              
               
                    $invoice_no = $row['invoice_no'];
                    $order_date = $row['order_date'];
                    $status  = $row['order_status'];
                    $user_name  = $row['customer_name'];
                    $user_mobile = $row['mobile'];
                    $address1 = $row['address1'];
                    $landmark = $row['landmark'];
                    $pincode = $row['pincode'];
                    $city = $row['city'];
                    $state = $row['state'];
                    $payment_method = $row['payment_method'];
                    $night_delivery_charge = $row['is_night_delivery'];
                    $product_quantity = $row['product_quantity'];
                    $product_price = $row['product_price'];
                    $product_discount = $row['product_discount'];
                    $delivery_charge   = $row['delivery_charge'];
                    $product_name   = $row['product_name'];
                    $product_id  = $row['id'];
                    $product_unit_value   = $row['product_unit_value'];
                    $product_unit = $row['product_unit'];
                
                $resultpost2[] = array(
                    'invoice_no' => $invoice_no,
                    'order_date' => $order_date,
                    'order_status' => $status,
                    'customer_name'=>$user_name,
                    'mobile' => $user_name,
                    'address1' => $address1,
                    'landmark' => $landmark,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'payment_method' => $payment_method,
                    'is_night_delivery' => $night_delivery_charge,
                    'product_quantity' => $product_quantity,
                    'product_price' => $product_price,
                    'product_discount' => $product_discount,
                    'delivery_charge' => $delivery_charge,
                    'product_name' => $product_name,
                    'id' => $product_id,
                    'product_unit_value' => $product_unit_value,
                    'product_unit' => $product_unit,
                    
                );
            }
                   
                       
                
           
              
             
                } else {
                    $resultpost2 = array();
                } */
                /*pharmacy general Order End Here*/
                
                
                /*fitness booking Start Here*/
                
                $query3 = $this->db->query("select fitness_center_branch.branch_name,business_category.category, fitness_center.center_name, packages.package_name,packages.price, booking_master.id as order_id,booking_master.booking_id,booking_master.user_id as change_user_id, booking_master.user_name,booking_master.vendor_id, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.listing_id, booking_master.booking_date,booking_master.status FROM booking_master LEFT JOIN fitness_center_branch ON (booking_master.branch_id=fitness_center_branch.id) LEFT JOIN packages ON (booking_master.package_id= packages.id) LEFT JOIN business_category ON (business_category.id = booking_master.category_id) LEFT JOIN fitness_center ON (fitness_center.user_id= booking_master.user_id) WHERE booking_master.user_id = '$user_id' and booking_master.vendor_id = '6'");
                
    $count3 = $query3->num_rows();
   if ($count3 > 0) {
                    
                       foreach ($query3->result_array() as $row) {
              
               
                    $booking_id = $row['booking_id'];
                    $booking_date = $row['booking_date'];
                    $status  = $row['status'];
                    $user_name  = $row['user_name'];
                    $user_mobile = $row['user_mobile'];
                    $user_email = $row['user_email'];
                    $user_gender = $row['user_gender'];
                    $booking_date = $row['booking_date'];
                    $center_name = $row['center_name'];
                    $branch_name = $row['branch_name'];
                    
                $resultpost3[] = array(
                    'booking_id' => $booking_id,
                    'booking_date' => $booking_date,
                    'status' => $status,
                    'user_name' => $user_name,
                    'user_mobile' => $user_mobile,
                    'user_email' => $user_email,
                    'user_gender' => $user_gender,
                    'booking_date' => $booking_date,
                    'center_name'=>$center_name,
                    'branch_name' => $branch_name,
                    'listing_type'=> $row['vendor_id']
                    
                );
            }
                   
                       
                
           
              
             
                } else {
                    $resultpost3 = array();
                }   
                /*fitness booking end here*/
                
                
                /*labs Booking Start Here*/
                
                
                       $query4 = $this->db->query("SELECT booking_master.*,booking_master.user_id as change_user_id,booking_master.id as book_id,lab_booking_details.* FROM booking_master INNER join lab_booking_details on(booking_master.booking_id=lab_booking_details.booking_id) where (booking_master.vendor_id='10' or booking_master.vendor_id='31' ) And booking_master.user_id = '$user_id'");
                       
              
                
    $count4 = $query4->num_rows();
   if ($count4 > 0) {
                    
                       foreach ($query4->result_array() as $row) {
              
               
                    $booking_id = $row['booking_id'];
                    $booking_date = $row['booking_date'];
                    $status  = $row['status'];
                    $user_name  = $row['user_name'];
                    $user_mobile = $row['user_mobile'];
                    $user_email = $row['user_email'];
                    $user_gender = $row['user_gender'];
                   
                    $branch_name = $row['branch_name'];
                    $mobile_no = $row['mobile_no'];
                    $address1 = $row['address_line1'];
                    $address2 = $row['address_line2'];
                   /* $landmark = $row['landmark'];*/
                    $pincode = $row['pincode'];
                    $city = $row['city'];
                    $state = $row['state'];
                    $payment_mode = $row['payment_mode'];
                
                
                $resultpost4[] = array(
                    'booking_id' => $booking_id,
                    'booking_date' => $booking_date,
                    'status' => $status,
                    'user_name' => $user_name,
                    'user_mobile' => $user_mobile,
                    'user_email' => $user_email,
                   
                    'branch_name' => $branch_name,
                    'address_line1' => $address1,
                    'address_line2' => $address2,
                   
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'payment_mode' => $payment_mode,
                    'listing_type'=> $row['vendor_id']
                    
                );
            }
                   
                       
                
           
              
             
                } else {
                    $resultpost4 = array();
                }
                /*labs Booking End Here*/
                
                /*Tyrocare Booking Start Here  */
                      
                   /*    $query5 = $this->db->query("SELECT booking_master.*,booking_master.id as book_id,booking_master.user_id as change_user_id,lab_booking_details.* FROM booking_master INNER join lab_booking_details on(booking_master.booking_id=lab_booking_details.booking_id) where booking_master.vendor_id='31' And booking_master.user_id = '$user_id'");
                       
              
                
    $count5 = $query5->num_rows();
   if ($count5 > 0) {
                    
                       foreach ($query5->result_array() as $row) {
              
               
                    $booking_id = $row['booking_id'];
                    $booking_date = $row['booking_date'];
                    $status  = $row['status'];
                    $user_name  = $row['user_name'];
                    $user_mobile = $row['user_mobile'];
                    $user_gender = $row['user_gender'];
                    $branch_name = $row['branch_name'];
                    $mobile_no = $row['mobile_no'];
                    $address1 = $row['address_line1'];
                    $address2 = $row['address_line2'];
                    $pincode = $row['pincode'];
                    $city = $row['city'];
                    $state = $row['state'];
                    $payment_mode = $row['payment_mode'];
                
                
                $resultpost5[] = array(
                    'booking_id' => $booking_id,
                    'booking_date' => $booking_date,
                    'status' => $status,
                    'user_name' => $user_name,
                    'user_mobile' => $user_mobile,
                    'user_email' => $user_email,
                    'user_gender' => $user_gender,
                    'branch_name' => $branch_name,
                    'address_line1' => $address1,
                    'address_line2' => $address2,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'payment_mode' => $payment_mode,
                   
                    
                );
            }
                   
            
             
                } else {
                    $resultpost5 = array();
                }
                */
                
                /*Tyrocare Booking End Here  */
                
                
                
                /* Hospital  booking Start*/
             
                
                
                /*Hospital  Booking End Here  */
              $resultpost62 = array();
              $resultpost61 = array();
              
               $query6= $this->db->query("select hospital_booking_master.booking_id,hospital_booking_master.booking_date,hospital_booking_master.listing_id,hospital_booking_master.user_id,hospital_booking_master.listing_id,
hospital_booking_master.doctor_id,hospital_doctor_prescription.id as prescription_id,hospital_booking_master.booking_time,hospital_booking_master.status,hospital_booking_master.consultation_type,
hospitals.name_of_hospital,users.name as patient_name, hospitals.address,hospitals.image,hospitals.state,hospitals.city,hospitals.pincode, dl.consultation,hospitals.phone,dl.doctor_name from hospital_booking_master  LEFT JOIN hospital_doctor_prescription 
ON (hospital_booking_master.patient_id = hospital_doctor_prescription.patient_id AND hospital_booking_master.doctor_id = hospital_doctor_prescription.doctor_id AND hospital_booking_master.listing_id = hospital_doctor_prescription.hospital_id) LEFT JOIN hospitals on (hospital_booking_master.listing_id = hospitals.user_id)
LEFT JOIN users ON (hospital_booking_master.user_id = users.id)  LEFT JOIN hospital_doctor_list as dl ON(dl.id=hospital_booking_master.doctor_id)
where hospital_booking_master.user_id = '$user_id' ORDER BY hospital_booking_master.id DESC");

$count6 = $query6->num_rows();
            if ($count6 > 0) {
                foreach ($query6->result_array() as $row) {
                    $booking_id = $row['booking_id'];
                    $booking_date = $row['booking_date'];
                    $booking_time = $row['booking_time'];
                    $patient_name = $row['patient_name'];
                    $doctor_name = $row['doctor_name'];
                    $consultation_charges = $row['consultation'];
                    $branch_name = $row['name_of_hospital'];
                    $consultation_type = $row['consultation_type'];
                    $address = $row['address'] . "," . $row['city'] . "," . $row['state'] . "," . $row['pincode'];
                    $image = $row['image'];
                    $clinic_contact_no = $row['phone'];
                    $prescription_id = $row['prescription_id'];
                    $status = $row['status'];
                    $doctor_id = $row['listing_id'];
                    
                      if($status == null)
                    {
                        $status = "";
                    }
                    if($prescription_id == null)
                    {
                        $prescription_id = "";
                    }
               $resultpost61[] = array(
                        'booking_id' => $booking_id,
                        'patient_name' => $patient_name,
                        'doctor_name' => $doctor_name,
                        'consultation_charge' => $consultation_charges,
                        'consultation_type' => $consultation_type,
                        'clinic_name' => $branch_name,
                        'address' => $address,
                        'image' => $image,
                        'clinic_contact_no' => $clinic_contact_no,
                        'status' => $status,
                        'prescription_id' => $prescription_id,
                        'hospital_id' => "",
                        'phone'=>"",
                        'ward_id' => "",
                        'emergency' => "",
                        'package_id' => "",
                        'amount' => "",
                        'booking_time' => $booking_time,
                        'booking_date' => $booking_date,
                        'listing_type'=>8
                    );
                    
           
                }
            }
$query8 = $this->db->query("SELECT booking_master.*,hospital_booking_details.*,health_record.patient_name,hospitals.address,hospitals.name_of_hospital,hospitals.city,hospitals.state,hospitals.phone,hospitals.pincode FROM booking_master LEFT JOIN users ON (booking_master.user_id = users.id) LEFT JOIN health_record ON(booking_master.patient_id=health_record.id) LEFT JOIN hospitals ON(booking_master.listing_id=hospitals.user_id) LEFT JOIN hospital_booking_details ON(booking_master.booking_id=hospital_booking_details.booking_id) where booking_master.user_id = '$user_id' and booking_master.vendor_id = '8' ORDER BY booking_master.id DESC ");

            $count8 = $query8->num_rows();
            if ($count8 > 0) {
                foreach ($query8->result_array() as $row) {
                    $booking_id = $row['booking_id'];
                    $booking_date = $row['booking_date'];
                    $address = $row['address'] . "," . $row['city'] . "," . $row['state'] . "," . $row['pincode'];
                    $contact_no = $row['user_mobile'];
                    $status = $row['status'];
                    $Hospital_id = $row['listing_id'];
                    $branch_name = $row['name_of_hospital'];
                    $ward_id = $row['ward_id'];
                    $emergency = $row['emergency'];
                    $package_id = $row['package_id'];
                    $amount = $row['amount'];
                    $booking_time = $row['booking_time'];
                    $booking_date = $row['booking_date'];
                    $patient_id = $row['patient_id'];
                    $patient_name = $row['patient_name'];
                    $patient_gender = $row['patient_gender'];
                    $patient_age = $row['patient_age'];
                    $patient_allergies = $row['patient_allergies'];
                    
                      if($status == null)
                    {
                        $status = "";
                    }
                $resultpost62[] = array(
                        'booking_id' => $booking_id,
                        'hospital_id' => $Hospital_id,
                        'patient_name' => $patient_name,
                        'doctor_name' => "",
                        'consultation_charge' => "",
                        'consultation_type' => "",
                        'clinic_name' => $branch_name,
                        'address' => $address,
                        'image' => "",
                        'clinic_contact_no' => "",
                        'status' => $status,
                        'prescription_id' => "",
                        
                        'phone'=>$contact_no,
                        'ward_id' => $ward_id,
                        'emergency' => $emergency,
                        'package_id' => $package_id,
                        'amount' => $amount,
                        'booking_time' => $booking_time,
                        'booking_date' => $booking_date,
                        'listing_type'=>8
                       );
                }
            }
                /*Doctor Appoinnment Start Here  */
              $resultpost6=array();        
          $resultpost6=array_merge($resultpost61,$resultpost62);
                    
                    
                  
              $query9 = $this->db->query("select doctor_booking_master.booking_id, doctor_booking_master.booking_date,doctor_booking_master.listing_id,doctor_booking_master.user_id, doctor_booking_master.listing_id,doctor_booking_master.clinic_id, doctor_prescription.id as prescription_id, doctor_booking_master.booking_time,doctor_booking_master.status,doctor_booking_master.consultation_type, doctor_clinic.clinic_name,users.name as patient_name, doctor_clinic.address,doctor_clinic.image,doctor_clinic.state,doctor_clinic.city,doctor_clinic.pincode, doctor_clinic.consultation_charges,doctor_clinic.contact_no,dl.doctor_name from doctor_booking_master LEFT JOIN doctor_prescription ON (doctor_booking_master.id = doctor_prescription.booking_id AND doctor_booking_master.user_id = doctor_prescription.patient_id AND doctor_booking_master.listing_id = doctor_prescription.doctor_id AND doctor_booking_master.clinic_id = doctor_prescription.clinic_id) LEFT JOIN doctor_clinic on (doctor_booking_master.clinic_id = doctor_clinic.id) LEFT JOIN users ON (doctor_booking_master.user_id = users.id) LEFT JOIN doctor_list as dl ON(dl.user_id=doctor_booking_master.listing_id) where doctor_booking_master.user_id = '$user_id' ORDER BY doctor_booking_master.id DESC");

            $count9 = $query9->num_rows();
            if ($count9 > 0) {
                foreach ($query9->result_array() as $row) {

                    $booking_id = $row['booking_id'];
                    $booking_date = $row['booking_date'];
                    $booking_time = $row['booking_time'];
                    $patient_name = $row['patient_name'];
                    $doctor_name = $row['doctor_name'];
                    $consultation_charges = $row['consultation_charges'];
                    $branch_name = $row['clinic_name'];
                    $consultation_type = $row['consultation_type'];
                    $address = $row['address'] . "," . $row['city'] . "," . $row['state'] . "," . $row['pincode'];
                    $image = $row['image'];
                    $clinic_contact_no = $row['contact_no'];
                    $prescription_id = $row['prescription_id'];
                    $status = $row['status'];
                    $doctor_id = $row['listing_id'];

                    $booking_time = str_replace('PM', '', $booking_time);
                    $booking_time = str_replace('AM', '', $booking_time);

                    if ($status == null) {
                        $status = "";
                    }
                    //echo $trimmed ;
                    if ($prescription_id != "") {
                        $url = "https://vendor.sandbox.medicalwale.com/doctor/prescription/" . $prescription_id . ".pdf";
                    } else {
                        $url = "";
                    }
                   
                  
                  
                  
                  
          
                
                $resultpost9[] = array(
                    'booking_id' => $booking_id,
                        'doctor_id' => $doctor_id,
                        'appointment_date' => $booking_date,
                        'appointment_time' => $booking_time,
                        'patient_name' => $patient_name,
                        'doctor_name' => $doctor_name,
                        'consultation_charge' => $consultation_charges,
                        'consultation_type' => $consultation_type,
                        'clinic_name' => $branch_name,
                        'clinic_address' => $address,
                        'image' => $image,
                        'clinic_contact_no' => $clinic_contact_no,
                        'status' => $status,
                        'prescription_id' => $prescription_id,
                        'prescription_pdf' => $url,
                        'listing_type'=>5
                    
                );
            }
                   
            
             
                } else {
                    $resultpost9 = array();
                }
                
                
                /*Doctor Appoinnment Booking End Here  */
              
               
                  /*Heallmall Appointment Booking End Here  */
                      
                       $query10 = $this->db->query("SELECT uo.order_id,uo.order_status,uo.user_id,uo.invoice_no,uo.address_id,uo.name,uo.mobile,uo.pincode,uo.address1, uo.address2,uo.landmark,uo.city,uo.state,uo.order_total, uo.payment_method,uo.order_date,uo.delivery_charge,uop.product_id,uop.product_name, uop.product_name,uop.product_img,uop.product_quantity,uop.product_price,uop.sub_total,uop.product_discount,uop.product_price,pd.uni_id,t.name as customer_name,u.phone as customer_number,medical_stores.medical_name,medical_stores.email,medical_stores.contact_no FROM user_order as uo  LEFT JOIN medical_stores on uo.listing_id=medical_stores.user_id 
LEFT JOIN user_order_product uop ON(uo.order_id =uop.order_id) LEFT JOIN prescription_order_details as pd ON(uo.order_id=pd.order_id) 
LEFT JOIN users as t ON(uo.user_id=t.id) LEFT JOIN users as u ON(uo.user_id=u.id)
where uo.user_id = '$user_id' and uo.listing_type='34'");
                       
              
                
    $count10 = $query10->num_rows();
   if ($count10 > 0) {
                    
                       foreach ($query10->result_array() as $row) {
              
               
                    $invoice_no = $row['invoice_no'];
                    $order_date = $row['order_date'];
                    $status  = $row['order_status'];
                    $customer_name  = $row['customer_name'];
                    $mobile = $row['mobile'];
                    $medical_name = $row['medical_name'];
                    $email = $row['email'];
                    $contact_no = $row['mobile'];
                    $name = $row['name'];
                    $address1 = $row['address1'];
                    $address2 = $row['address2'];
                    $landmark = $row['landmark'];
                    $pincode = $row['pincode'];
                    $city = $row['city'];
                    $state = $row['state'];
                    $payment_method = $row['payment_method']; 
                  
                    
                
                
                $resultpost10[] = array(
                    'invoice_no' => $invoice_no,
                    'order_date' => $order_date,
                    'order_status' => $status,
                    'customer_name' => $customer_name,
                    'mobile' => $mobile,
                    'medical_name' => $medical_name,
                    'email' => $email,
                    'contact_no' => $contact_no,
                    'name' => $name,
                    'address1' => $address1,
                    'address2' => $address2,
                    'landmark' => $landmark,
                    'pincode' => $pincode,
                     'city' => $city,
                    'state' => $state,
                    'payment_method' => $payment_method,
                   'listing_type'=>$row['listing_type']
                    
                );
            }
                   
            
             
                } else {
                    $resultpost10 = array();
                }
                
                
                /*Heallmall Appointment Booking End Here  */
              
             
            $all_booking[] = array(
              
                'Pharmacy' => $resultpost,
               /* 'Pharamacy General Order' => $resultpost2,*/
                'Fitness Centres' => $resultpost3,
                'Nursing Attendant'=>$resultpost1,
                'Labs'=>$resultpost4,
              /*  'Tyrocare Booking'=>$resultpost5,*/
                'Hospital'=>$resultpost6,
             /*   'Hospital OPD Booking'=>$resultpost8,*/
                'Doctors'=>$resultpost9,
                'HealthMall'=>$resultpost10,
            );
                
             return $all_booking;
    }
    
   // Added by Dhaval all booking details
     public function all_booking_details($user_id,$term )
    {
          //Fitness Center Start Here
         if(empty($term))
         {
          $querys1 = $this->db->query("SELECT fitness_center_branch.branch_name,fitness_center_branch.user_id as branch_fit_id, fitness_center_branch.branch_image, fitness_center_branch.branch_phone, fitness_center_branch.branch_address, fitness_center_branch.pincode, fitness_center_branch.state, fitness_center_branch.city,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time,booking_master.trainer_package_id,booking_master.trainer_id, booking_master.joining_date FROM booking_master
            INNER JOIN fitness_center_branch ON booking_master.branch_id=fitness_center_branch.id
           
            
            WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='6' ORDER BY booking_master.id DESC");
    }
else
{
$querys1 = $this->db->query("SELECT fitness_center_branch.branch_name,fitness_center_branch.user_id as branch_fit_id, fitness_center_branch.branch_image, fitness_center_branch.branch_phone, fitness_center_branch.branch_address, fitness_center_branch.pincode, fitness_center_branch.state, fitness_center_branch.city,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time, booking_master.joining_date,booking_master.trainer_package_id,booking_master.trainer_id FROM booking_master
            INNER JOIN fitness_center_branch ON booking_master.branch_id=fitness_center_branch.id
           
           
            WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='6' and (fitness_center_branch.branch_name like '%$term%' or  booking_master.booking_id like '%$term%') ORDER BY booking_master.id DESC");
}      

         
            $count1 = $querys1->num_rows();
            if ($count1 > 0) {
                foreach ($querys1->result_array() as $row1) {
                    $package_id = $row1['package_id'];
                    $trainer_package_id=$row1['trainer_package_id'];
                    $trainer_id=$row1['trainer_id'];
                    if($package_id !=0)
                    {
                    
                    
                    $query12 = $this->db->query("SELECT packages.package_name, packages.package_details, packages.price from packages  where packages.id ='$package_id' ");
                     $row12=$query12->row_array();
                    
                    $booking_id = $row1['booking_id'];
                    $listing_id = $row1['branch_fit_id'];
                    $branch_id = $row1['branch_id'];
                    $branch_name = $row1['branch_name'];
                    $branch_image = $row1['branch_image'];
                    $branch_phone = $row1['branch_phone'];
                    $branch_address = $row1['branch_address'];
                    $branch_pincode = $row1['pincode'];
                    $branch_city = $row1['city'];
                    $branch_state = $row1['state'];
                    $appointment_user_name = $row1['user_name'];
                    $appointment_user_mobile = $row1['user_mobile'];
                    $appointment_user_email = $row1['user_email'];
                    $category_id = $row1['category_id'];
                    $status = $row1['status'];
                    $package_name = $row12['package_name'];
                    $package_details = $row12['package_details'];
                    $package_price = $row12['price'];
                    $trail_booking_date = $row1['trail_booking_date'];
                    $trail_booking_time = $row1['trail_booking_time'];
                    $joining_date = date('j M Y', strtotime($row1['joining_date']));
                    $book_for_date = date('d/m/Y', strtotime($row1['joining_date']));
                    $is_free_trial = 'No';
                    date_default_timezone_set('Asia/Kolkata');
                    if ($package_id == '100') {
                        $joining_date_ = $trail_booking_date . ' ' . $trail_booking_time;

                        $joining_date = date('j M Y | h:i A', strtotime($joining_date_));
                        $is_free_trial = 'Yes';
                    }
                    $booking_date = date('j M Y | h:i A', strtotime($row1['booking_date']));

                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
                    }
                    
                    if($status == null)
                    {
                        $status = "";
                    }
                   


                    $resultpost1[] = array(
                        'booking_id' => $booking_id,
                        'branch_name' => $branch_name,
                        'branch_id' => $branch_id,
                        'listing_id' => $listing_id,
                        'trainer_id' => '',
                        'trainer_name' => '',
                        'branch_image' => $branch_image,
                        'branch_phone' => $branch_phone,
                        'branch_address' => $branch_address,
                        'branch_pincode' => $branch_pincode,
                        'branch_city' => $branch_city,
                        'branch_state' => $branch_state,
                        'appointment_user_name' => $appointment_user_name,
                        'appointment_user_mobile' => $appointment_user_mobile,
                        'appointment_user_email' => $appointment_user_email,
                        'package_name' => $package_name,
                        'package_details' => $package_details,
                        'package_price' => $package_price,
                        'is_free_trial' => $is_free_trial,
                        'joining_date' => $joining_date,
                        'book_for_date' => $book_for_date,
                        'booking_date' => $booking_date,
                        'status' => $status,
                        'listing_type'=>$row1['vendor_id']
                    );
                    }
                    else
                    {
                        $query12 = $this->db->query("SELECT personal_trainer_packages.package_name, personal_trainer_packages.package_details, personal_trainer_packages.price from personal_trainer_packages  where personal_trainer_packages.id ='$trainer_package_id' ");
                     $row12=$query12->row_array();
                     
                      $query123 = $this->db->query("SELECT * from personal_trainers  where id ='$trainer_id' ");
                     $row123=$query123->row_array();
                    
                    $booking_id = $row1['booking_id'];
                    $listing_id = $row1['branch_fit_id'];
                    $branch_id = $row1['branch_id'];
                    $branch_name = $row1['branch_name'];
                    $branch_image = $row1['branch_image'];
                    $branch_phone = $row1['branch_phone'];
                    $branch_address = $row1['branch_address'];
                    $branch_pincode = $row1['pincode'];
                    $trainer_name=$row123['manager_name'];
                    $branch_city = $row1['city'];
                    $branch_state = $row1['state'];
                    $appointment_user_name = $row1['user_name'];
                    $appointment_user_mobile = $row1['user_mobile'];
                    $appointment_user_email = $row1['user_email'];
                    $category_id = $row1['category_id'];
                    $status = $row1['status'];
                    $package_name = $row12['package_name'];
                    $package_details = $row12['package_details'];
                    $package_price = $row12['price'];
                    $trail_booking_date = $row1['trail_booking_date'];
                    $trail_booking_time = $row1['trail_booking_time'];
                    $joining_date = date('j M Y', strtotime($row1['joining_date']));
                    $book_for_date = date('d/m/Y', strtotime($row1['joining_date']));
                    $is_free_trial = 'No';
                    date_default_timezone_set('Asia/Kolkata');
                    if ($package_id == '100') {
                        $joining_date_ = $trail_booking_date . ' ' . $trail_booking_time;

                        $joining_date = date('j M Y | h:i A', strtotime($joining_date_));
                        $is_free_trial = 'Yes';
                    }
                    $booking_date = date('j M Y | h:i A', strtotime($row1['booking_date']));

                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
                    }
                    
                    if($status == null)
                    {
                        $status = "";
                    }
                   


                    $resultpost1[] = array(
                        'booking_id' => $booking_id,
                        'branch_name' => $branch_name,
                        'branch_id' => $branch_id,
                        'listing_id' => $listing_id,
                        'trainer_id' => $trainer_id,
                        'trainer_name' => $trainer_name,
                        'branch_image' => $branch_image,
                        'branch_phone' => $branch_phone,
                        'branch_address' => $branch_address,
                        'branch_pincode' => $branch_pincode,
                        'branch_city' => $branch_city,
                        'branch_state' => $branch_state,
                        'appointment_user_name' => $appointment_user_name,
                        'appointment_user_mobile' => $appointment_user_mobile,
                        'appointment_user_email' => $appointment_user_email,
                        'package_name' => $package_name,
                        'package_details' => $package_details,
                        'package_price' => $package_price,
                        'is_free_trial' => $is_free_trial,
                        'joining_date' => $joining_date,
                        'book_for_date' => $book_for_date,
                        'booking_date' => $booking_date,
                        'status' => $status,
                        'listing_type'=>$row1['vendor_id']
                    );
                    }
                }
            } else {
                $resultpost1 = array();
            }
          // Fitness Center End Here
        
          // Doctor Start Here
          if(empty($term))
          {
           $query2 = $this->db->query("select doctor_booking_master.booking_id, doctor_booking_master.booking_date,doctor_booking_master.listing_id,doctor_booking_master.user_id, doctor_booking_master.listing_id,doctor_booking_master.clinic_id, doctor_prescription.id as prescription_id, doctor_booking_master.booking_time,doctor_booking_master.status,doctor_booking_master.consultation_type, doctor_clinic.clinic_name,users.name as patient_name, doctor_clinic.address,doctor_clinic.image,doctor_clinic.state,doctor_clinic.city,doctor_clinic.pincode, doctor_clinic.consultation_charges,doctor_clinic.contact_no,dl.doctor_name from doctor_booking_master LEFT JOIN doctor_prescription ON (doctor_booking_master.booking_id = doctor_prescription.booking_id AND doctor_booking_master.listing_id = doctor_prescription.doctor_id AND doctor_booking_master.clinic_id = doctor_prescription.clinic_id)  LEFT JOIN doctor_clinic on (doctor_booking_master.clinic_id = doctor_clinic.id) LEFT JOIN users ON (doctor_booking_master.user_id = users.id) LEFT JOIN doctor_list as dl ON(dl.user_id=doctor_booking_master.listing_id) where doctor_booking_master.user_id = '$user_id' ORDER BY doctor_booking_master.id DESC");
}
else
{
     $query2 = $this->db->query("select doctor_booking_master.booking_id, doctor_booking_master.booking_date,doctor_booking_master.listing_id,doctor_booking_master.user_id, doctor_booking_master.listing_id,doctor_booking_master.clinic_id, doctor_prescription.id as prescription_id, doctor_booking_master.booking_time,doctor_booking_master.status,doctor_booking_master.consultation_type, doctor_clinic.clinic_name,users.name as patient_name, doctor_clinic.address,doctor_clinic.image,doctor_clinic.state,doctor_clinic.city,doctor_clinic.pincode, doctor_clinic.consultation_charges,doctor_clinic.contact_no,dl.doctor_name from doctor_booking_master LEFT JOIN doctor_prescription ON (doctor_booking_master.booking_id = doctor_prescription.booking_id AND doctor_booking_master.listing_id = doctor_prescription.doctor_id AND doctor_booking_master.clinic_id = doctor_prescription.clinic_id) LEFT JOIN doctor_clinic on (doctor_booking_master.clinic_id = doctor_clinic.id) LEFT JOIN users ON (doctor_booking_master.user_id = users.id) LEFT JOIN doctor_list as dl ON(dl.user_id=doctor_booking_master.listing_id) where doctor_booking_master.user_id = '$user_id' and (dl.doctor_name like '%$term%' or doctor_booking_master.booking_id like '%$term%' or doctor_clinic.clinic_name like '%$term%') ORDER BY doctor_booking_master.id DESC");
                
}
            $count2 = $query2->num_rows();
            if ($count2 > 0) {
                foreach ($query2->result_array() as $row2) {
                 
                    $booking_id = $row2['booking_id'];
                    $booking_date = $row2['booking_date'];
                    $booking_time = $row2['booking_time'];
                    $patient_name = $row2['patient_name'];
                    $doctor_name = $row2['doctor_name'];
                    $consultation_charges = $row2['consultation_charges'];
                    $branch_name = $row2['clinic_name'];
                    $consultation_type = $row2['consultation_type'];
                    $address = $row2['address'] . "," . $row2['city'] . "," . $row2['state'] . "," . $row2['pincode'];
                    $image = $row2['image'];
                    $clinic_contact_no = $row2['contact_no'];
                    $prescription_id = $row2['prescription_id'];
                    $status = $row2['status'];
                    $doctor_id = $row2['listing_id'];
                    
                    $booking_time = str_replace('PM','', $booking_time);
                    $booking_time = str_replace('AM','', $booking_time);
                    
                      if($status == null)
                    {
                        $status = "";
                    }
                     //echo $trimmed ;
                     if($prescription_id!="")
                     {
                    $url="http://vendorsandbox.medicalwale.com/doctor/prescription/".$prescription_id.".pdf";
                     }
                     else
                     {
                         $url="";
                     }
                     if($image== null)
                     {
                         $image="";
                     }
                     if($prescription_id== null)
                     {
                         $prescription_id="";
                     }
                    $resultpost2[] = array(
                        'booking_id' => $booking_id,
                        'doctor_id' => $doctor_id,
                        'appointment_date' => $booking_date,
                        'appointment_time' => $booking_time,
                        'patient_name' => $patient_name,
                        'doctor_name' => $doctor_name,
                        'consultation_charge' => $consultation_charges,
                        'consultation_type' => $consultation_type,
                        'clinic_name' => $branch_name,
                        'clinic_address' => $address,
                        'image' => $image,
                        'clinic_contact_no' => $clinic_contact_no,
                        'status' => $status,
                        'prescription_id' => $prescription_id,
                        'prescription_pdf'=> $url,
                        'listing_type'=>5
                    );
                }
            } else {
                $resultpost2 = array();
            }
          // Doctor End Here
        
          // Nursing Attendant Start Here
            if(empty($term))
            {
                //echo "SELECT booking_master.*,health_record.patient_name,nursing_attendant.address,nursing_attendant.city,nursing_attendant.state,nursing_attendant.contact,nursing_attendant.pincode,nursing_booking_details.* FROM booking_master  LEFT JOIN users ON (booking_master.user_id = users.id) LEFT JOIN health_record ON(booking_master.patient_id=health_record.id) LEFT JOIN nursing_attendant ON(booking_master.listing_id=nursing_attendant.user_id) LEFT JOIN nursing_booking_details ON(booking_master.booking_id=nursing_booking_details.booking_id) where booking_master.user_id = '$user_id' and booking_master.vendor_id = '12' ORDER BY booking_master.id DESC";
                $query3 = $this->db->query("SELECT booking_master.*,health_record.patient_name,nursing_attendant.address,nursing_attendant.city,nursing_attendant.state,nursing_attendant.contact,nursing_attendant.pincode,nursing_booking_details.* FROM booking_master  LEFT JOIN users ON (booking_master.user_id = users.id) LEFT JOIN health_record ON(booking_master.patient_id=health_record.id) LEFT JOIN nursing_attendant ON(booking_master.listing_id=nursing_attendant.user_id) LEFT JOIN nursing_booking_details ON(booking_master.booking_id=nursing_booking_details.booking_id) where booking_master.user_id = '$user_id' and booking_master.vendor_id = '12' ORDER BY booking_master.id DESC");
            }
            else
            {
              $query3 = $this->db->query("SELECT booking_master.*,health_record.patient_name,nursing_attendant.address,nursing_attendant.city,nursing_attendant.state,nursing_attendant.contact,nursing_attendant.pincode,nursing_booking_details.* FROM booking_master  LEFT JOIN users ON (booking_master.user_id = users.id) LEFT JOIN health_record ON(booking_master.patient_id=health_record.id) LEFT JOIN nursing_attendant ON(booking_master.listing_id=nursing_attendant.user_id) LEFT JOIN nursing_booking_details ON(booking_master.booking_id=nursing_booking_details.booking_id) where booking_master.user_id = '$user_id' and booking_master.vendor_id = '12'
                                   and (nursing_booking_details.package_name like '%$term%' or booking_master.booking_id like '%$term%')ORDER BY booking_master.id DESC");
            
            }
            $count3 = $query3->num_rows();
            if ($count3 > 0) {
                foreach ($query3->result_array() as $row3) {
                    $resultbids = array();
                    $listing_id = $row3['listing_id'];
                    $package_name = $row3['package_name'];
                    $package_amount = $row3['package_amount'];
                    $package_image = $row3['package_image'];
                    
                    
                    $booking_id = $row3['booking_id'];
                    $package_id = $row3['package_id'];
                    $booking_date = $row3['booking_date'];
                    $patient_name = $row3['patient_name'];
                    $address = $row3['address'] . "," . $row3['city'] . ",".$row3['state'] . "," . $row3['pincode'];
                    $contact_no = $row3['contact'];
                    $status = $row3['status'];
                    $Nursing_id = $row3['listing_id'];
                    $patiente_condition =$row3['patiente_condition'];
                    $attendent_time =$row3['attendent_time'];
                    $attendant_hour = $row3['attendant_hour'];
                    $tentative_intime = $row3['tentative_intime'];
                    $tentative_outtime = $row3['tentative_outtime'];
                    $nursing_gender = $row3['nursing_gender'];
                    $attendant_needed = $row3['attendant_needed'];
                    $joining_date = $row3['joining_date'];
                    $book_type = $row3['book_type'];
                      if($status == null)
                    {
                        $status = "";
                    }
                    
                    // ghanshyam parihar
                    // echo "SELECT brb.*,na.phone,na.name FROM booking_requirment_bids brb  LEFT JOIN users na ON (na.id = brb.listing_id) where brb.booking_id = '$booking_id' ORDER BY brb.id DESC";
                  // echo "SELECT brb.*,na.phone,na.name FROM booking_requirment_bids brb  LEFT JOIN users na ON (na.id = brb.listing_id) where brb.booking_id = '$booking_id' ORDER BY brb.id DESC";                   
                    $querybids = $this->db->query("SELECT brb.*,na.phone,na.name FROM booking_requirment_bids brb  LEFT JOIN users na ON (na.id = brb.listing_id) where brb.booking_id = '$booking_id' ORDER BY brb.id DESC");
                    $countbids = $querybids->num_rows();
                    if ($countbids > 0) {
                       // $book_type = 'bids_booking';
                        foreach ($querybids->result_array() as $rowbids) {
                        $bid_id = $rowbids['id'];
                        $l_id = $rowbids['listing_id'];
                        $book_id = $rowbids['booking_id'];
                        $rate = $rowbids['rate'];
                        $name = $rowbids['name'];
                        $phone = $rowbids['phone'];
                        $bid_status = $rowbids['booking_status'];
                        if($bid_status == 0)
                        {
                            $st = "Not-Booked";
                        }
                        else
                        {
                            $st = "Booked";
                        }
                        
                                                
                        $resultbids[] = array(
                            'bid_id' => $bid_id,
                            'listing_id' => $l_id,
                            'booking_id' => $book_id,
                            'rate' => $rate,
                            'phone' => $phone,
                            'nursing_name' => $name,
                            'booking_status' => $st
                        );
                    }
                    }
                    else{
                      //  $book_type = 'special_booking';
                        $resultbids = array();
                    }
                    
                    $resultpost3[] = array(
                        'listing_id' => $listing_id,
                        'booking_id' => $booking_id,
                        'nursing_id' => $Nursing_id,
                        'appointment_date' => $booking_date,
                        'patient_name' => $patient_name,
                        'package_id' => $package_id,
                        'package_name' => $package_name,
                        'package_amount' => $package_amount,
                        'package_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$package_image,
                        'address'=>$address,
                        'phone' =>$contact_no,
                        // ghanshyam parihar
                        'bids' => $resultbids,
                        'booking_type' => $book_type,
                        // ghanshyam parihar
                        'patiente_condition' =>$patiente_condition,
                        'attendent_time' =>$attendent_time,
                        'attendant_hour' =>$attendant_hour,
                        'tentative_intime' =>$tentative_intime,
                        'nursing_gender' =>$nursing_gender,
                        'attendant_needed' =>$attendant_needed,
                        'booking_date'=>$joining_date,
                        'status' => $status,
                        'listing_type'=>$row3['vendor_id']
                    );
                }
            } else {
                $resultpost3 = array();
            }
          // Nursing Attendant End Here
        // Lab Start Here
        $resultpost4 = array(); 
       if(empty($term))
         {  
        $count_query4 = $this->db->query("SELECT lb.*,bm.vendor_id,bm.status from booking_master as bm LEFT JOIN lab_booking_details as lb ON(bm.booking_id = lb.booking_id) where bm.user_id='$user_id' and (bm.vendor_id='10' or bm.vendor_id='31') order by bm.id desc");
         }
else
{
     $count_query4 = $this->db->query("SELECT lb.*,bm.vendor_id,bm.status from booking_master as bm LEFT JOIN lab_booking_details as lb ON(bm.booking_id = lb.booking_id) where bm.user_id='$user_id' and (bm.vendor_id='10' or bm.vendor_id='31') and (lb.product like '%$term%' or bm.booking_id like '%$term%' or lb.branch_name like '%$term%')");
           
}   
           
            $lab_booked4       = $count_query4->num_rows();
            
            if ($lab_booked4 > 0) {
                foreach ($count_query4->result_array() as $Lbooked) {
                     
                      if($Lbooked['vendor_id']=="31")
                    {
                        $uid =$Lbooked['user_id'];    
            $user_query = $this->db->query("SELECT email,phone,name FROM users WHERE id='$uid'");
              $con_id=$user_query->num_rows();
            if($con_id > 0)
            {
                $email = $user_query->row()->email;
                $phone = $user_query->row()->phone;
                $user_name = $user_query->row()->name;        
            }
            else
            {
                 $email = "";
                $phone = "";
                $user_name = "";
            }      
                    
                $bk_id = $Lbooked['booking_id'];   
                /*echo"SELECT status FROM booking_master WHERE booking_id='$bk_id'";*/
                $book_query = $this->db->query("SELECT status FROM booking_master WHERE booking_id='$bk_id'");
                $conff=$user_query->num_rows();
                if($conff>0){
                    $status = $book_query->row()->status;
                }
                else{
                    $status = '';
                }
            
            
            //NAWAZ
            //echo "SELECT report_path FROM reports WHERE booking_id='$bk_id'";
            $report_path ='';
            $book_query_path = $this->db->query("SELECT report_path FROM reports WHERE booking_id='$bk_id'");
            $report_path_count =  $book_query_path->num_rows();;
            if($report_path_count > 0){
                $report_path = $book_query_path->row()->report_path;
            }
            
                      if($Lbooked['reference_id']!='')
                      {
                    
                $resultpost4[] = array(
                'user_id'=> $Lbooked['user_id'], 
                'user_name'=>$user_name,
                'amount'=> $Lbooked['amount'],
                'report_code'=> $Lbooked['report_code'],
                'becount'=> $Lbooked['becount'],
                'hc'=> $Lbooked['hc'],
                'ref_code'=> $Lbooked['ref_code'],
                'reports'=> $Lbooked['reports'],
                'bendataxml'=> $Lbooked['bendataxml'],
                'product'=> $Lbooked['product'],
                'vendor_type'=> $Lbooked['vendor_type'],
                'address_line1'=> $Lbooked['address_line1'],
                'pincode'=> $Lbooked['pincode'],
                'mobile_no'=> $Lbooked['mobile_no'],
                'email_id'=> $Lbooked['email_id'],
                'booking_date'=> $Lbooked['booking_date'],
                'booking_time'=> $Lbooked['booking_time'],
                'booking_id'=> $Lbooked['booking_id'],
                'reference_id'=> $Lbooked['reference_id'],
                'lead_id'=> $Lbooked['lead_id'],
                'status'=>$status,
                'report_path'=>$report_path,
                "listing_type"=> "31"
                );
                      }
                    }  
                     else
                     {
                        
                    $user_id = $Lbooked['user_id'];
                    $patient_id = $Lbooked['patient_id'];
                    $listing_id = $Lbooked['listing_id'];
                    $vendor_type = $Lbooked['vendor_type'];
                    $branch_id = $Lbooked['branch_id'];
                    $branch_name = $Lbooked['branch_name'];
                    $at_home = $Lbooked['at_home'];
                    $address_line1 = $Lbooked['address_line1'];
                    $address_line2 = $Lbooked['address_line2'];
                    $city = $Lbooked['city'];
                    $state = $Lbooked['state'];
                    $pincode = $Lbooked['pincode'];
                    $mobile_no = $Lbooked['mobile_no'];
                    $email_id = $Lbooked['email_id'];
                    $address_id = $Lbooked['address_id'];
                    $test_ids = $Lbooked['test_id'];
                    $package_id = $Lbooked['package_id'];
                    $booking_date = $Lbooked['booking_date'];
                    $booking_time = $Lbooked['booking_time'];
                    $booking_id = $Lbooked['booking_id']; 
                    $status = $Lbooked['status']; 
                    
                    
                    $Booed_test_list = array();
                    if ($test_ids != '' && $test_ids != '0') {
                        $Testids = explode(',', $test_ids);
                        
                        foreach ($Testids as $tid) {
                          //  echo "SELECT * FROM lab_test_details WHERE test_id = '$tid'";
                            $Query = $this->db->query("SELECT * FROM lab_test_details WHERE test_id = '$tid'");
                            $Comp = $Query->row();
                            $comp_count = $Query->num_rows();
                            //print_r($Comp);
                            if($comp_count>0)
                            {
                                $test           = $Comp->test;
                                $test_id        = $Comp->test_id;
                                $price          = $Comp->price;
                                $offer          = $Comp->offer;
                                $executive_rate = $Comp->executive_rate;
                                $home_delivery  = $Comp->home_delivery;
                            
                                
                                 $Booed_test_list[] = array(
                                    'test_id' => $test,
                                    'test' => $test_id,
                                    'price' => $price,
                                    'home_delivery' => "0");
                            }
                           
                        }
                    }
                     $lab_pack_name = "";
                        $pack_details = "";
                        $pack_amount = "";
                    
                    if($package_id > 0){
                         
                        $LP_query = $this->db->query("SELECT * FROM lab_packages1 WHERE id='$package_id'");
                        $result1 = $LP_query->num_rows();
                        if($result1 > 0)
                        {
                        $lab_pack_name = $LP_query->row()->package_name;
                        $pack_details = $LP_query->row()->package_details;
                        $pack_amount = $LP_query->row()->Price;
                        }
                    }
                      if($status == null)
                    {
                        $status = "";
                    }
                    
                    $resultpost4[] = array(
                        'user_id'=> $user_id,
                        'patient_id'=> $patient_id,
                        'listing_id'=> $listing_id,
                        'vendor_type'=> $vendor_type,
                        'branch_id'=> $branch_id,
                        'branch_name'=> $branch_name,
                        'at_home'=> $at_home,
                        'address_line1'=> $address_line1,
                        'address_line2'=> $address_line2,
                        'city'=> $city,
                        'state'=> $state,
                        'pincode'=> $pincode,
                        'mobile_no'=> $mobile_no,
                        'email_id'=> $email_id,
                        'address_id'=> $address_id,
                        'test_id'=> $test_ids,
                        'package_id'=> $package_id,
                        'package_name'=> $lab_pack_name,
                        'package_details'=> $pack_details,
                        'package_price'=> $pack_amount,
                        'booking_date'=> $booking_date,
                        'booking_time'=> $booking_time,
                        'booking_id'=> $booking_id,
                        'booked_tests'=>$Booed_test_list,
                        'status'=>$status
                    );
                     }
                }
            }else{
               $resultpost4 = array(); 
            }
        // Lab End Here 
        // Hospitals Start Here
    
       $resultpost61 = array();
       // IPD
         if(empty($term))
         { 
       $booking_details = $this->db->query("SELECT * FROM booking_master WHERE user_id='$user_id' and vendor_id='8' order by id DESC");
         }
else
{
      $booking_details = $this->db->query("SELECT bm.*,bm.id as ids, hp.package_name FROM booking_master as bm LEFT JOIN hospital_packages as hp ON (bm.package_id=hp.id) WHERE bm.user_id='$user_id' and bm.vendor_id='8' and (hp.package_name like '%$term%' or bm.booking_id like '%$term%') order by bm.id DESC");
        
}
        $booking_count = $booking_details->num_rows();
        if($booking_count>0)
        {
           foreach($booking_details ->result_array() as $row )
           {
               $id = $row['id'];
               $booking_id = $row['booking_id'];
               $package_id = $row['package_id'];
              $listing_id = $row['listing_id'];
              $vendor_detaild = $this->db->query("SELECT name,phone FROM users WHERE id='$listing_id' ");
              $vendor_count = $vendor_detaild->num_rows();
              $vendor_name ='';
              if($vendor_count>0)
              {
             $vendor_name = $vendor_detaild->row()->name;
              }
              
              $hospital_details = $this->db->query("SELECT * FROM hospital_booking_details WHERE booking_id='$booking_id' and vendor_type='8' order by id DESC");
              $hospital_count = $hospital_details->num_rows();
              $hospital_name ='';
              if($hospital_count>0)
              {
             $ward_id = $hospital_details->row()->ward_id;
             $amount = $hospital_details->row()->amount;
             $patient_preferred_date = $hospital_details->row()->patient_preferred_date;
              }
              
              $ward_details = $this->db->query("SELECT * FROM hospital_wards WHERE hospital_id='$listing_id'");
              $ward_count = $ward_details->num_rows();
              $room_type ='';
                $capacity ='';
                  $price ='';
              if($ward_count>0)
              {
             $room_type = $ward_details->row()->room_type;
             $capacity = $ward_details->row()->capacity;
             $price =  $ward_details->row()->price;
              }
              
              $pack_details = $this->db->query("SELECT * FROM hospital_packages WHERE id='$package_id'");
              $pack_count = $pack_details->num_rows();
            
              if($pack_count>0)
              {
             $package_name = $pack_details->row()->package_name;
              }
              
              
              
              $user_id = $row['user_id'];
              $patient_id = $row['patient_id'];
              $name = $row['user_name'];
             $phone =$row['user_mobile'];
             $email = $row['user_email'];
             $gender = $row['user_gender'];
             $branch_id = $row['branch_id'];
             $vendor_id=$row['vendor_id'];
            $ex=explode(' ',$row['booking_date']);
            $booking_time=$hospital_details->row()->booking_time;
            $booking_date = $row['booking_date'];
            $status = $row['status'];
            $joining_date = $row['joining_date'];
            $category_id = $row['category_id'];
            $booking_address = $row['booking_address'];
            $booking_mobile = $row['booking_mobile'];
            if($category_id=="")
            {
                $category_id="";
            }
            
            $resultpost61[] = array(
                    'id' => $id,
                    'booking_id' => $booking_id,
                    'package_id'=> $package_id,
                    'listing_id' => $listing_id,
                    'user_id' => $user_id,
                    'patient_id' => $patient_id,
                    'package_name'=>$package_name,
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'gender' => $gender,
                    'branch_id' =>$branch_id,
                    'vendor_id' =>$vendor_id,
                     'booking_time' => $booking_time,
                        'booking_date' => $booking_date,
                    'status' => $status,
                    'joining_date' => $patient_preferred_date,
                    'category_id' => $category_id,
                    'booking_address' => $booking_address,
                    'booking_mobile' => $booking_mobile,
                    'vendor_name' => $vendor_name,
                    'ward_id' => $ward_id,
                    'amount' => $amount,
                    'room_type'=> $room_type,
                    'capacity' => $capacity,
                    'price' => $price,
                    'booking_type'=>"IPD"
                );
            
           }
        }
        
       // OPD
         if(empty($term))
         {
        $query= $this->db->query("select hospital_booking_master.booking_id,hospital_booking_master.booking_date,hospital_booking_master.listing_id,hospital_booking_master.user_id,hospital_booking_master.listing_id,
hospital_booking_master.doctor_id,hospital_doctor_prescription.id as prescription_id,hospital_booking_master.booking_time,hospital_booking_master.status,hospital_booking_master.consultation_type,
hospitals.name_of_hospital,users.name as patient_name, hospitals.address,hospitals.image,hospitals.state,hospitals.city,hospitals.pincode, dl.consultation,hospitals.phone,dl.doctor_name from hospital_booking_master  LEFT JOIN hospital_doctor_prescription 
ON (hospital_booking_master.patient_id = hospital_doctor_prescription.patient_id AND hospital_booking_master.doctor_id = hospital_doctor_prescription.doctor_id AND hospital_booking_master.listing_id = hospital_doctor_prescription.hospital_id) LEFT JOIN hospitals on (hospital_booking_master.listing_id = hospitals.user_id)
LEFT JOIN users ON (hospital_booking_master.user_id = users.id)  LEFT JOIN hospital_doctor_list as dl ON(dl.id=hospital_booking_master.doctor_id)
where hospital_booking_master.user_id = '$user_id' ORDER BY hospital_booking_master.id DESC");
}
else
{
     $query= $this->db->query("select hospital_booking_master.booking_id,hospital_booking_master.booking_date,hospital_booking_master.listing_id,hospital_booking_master.user_id,hospital_booking_master.listing_id,
hospital_booking_master.doctor_id,hospital_doctor_prescription.id as prescription_id,hospital_booking_master.booking_time,hospital_booking_master.status,hospital_booking_master.consultation_type,
hospitals.name_of_hospital,users.name as patient_name, hospitals.address,hospitals.image,hospitals.state,hospitals.city,hospitals.pincode, dl.consultation,hospitals.phone,dl.doctor_name from hospital_booking_master  LEFT JOIN hospital_doctor_prescription 
ON (hospital_booking_master.patient_id = hospital_doctor_prescription.patient_id AND hospital_booking_master.doctor_id = hospital_doctor_prescription.doctor_id AND hospital_booking_master.listing_id = hospital_doctor_prescription.hospital_id) LEFT JOIN hospitals on (hospital_booking_master.listing_id = hospitals.user_id)
LEFT JOIN users ON (hospital_booking_master.user_id = users.id)  LEFT JOIN hospital_doctor_list as dl ON(dl.id=hospital_booking_master.doctor_id)
where hospital_booking_master.user_id = '$user_id' and (hospitals.name_of_hospital like '%$term%' or dl.doctor_name like '%$term%'or hospital_booking_master.booking_id like '%$term%')ORDER BY hospital_booking_master.id DESC");

}
$count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $booking_id = $row['booking_id'];
                    $booking_date = $row['booking_date'];
                    $booking_time = $row['booking_time'];
                    $patient_name = $row['patient_name'];
                    $doctor_name = $row['doctor_name'];
                    $consultation_charges = $row['consultation'];
                    $branch_name = $row['name_of_hospital'];
                    $consultation_type = $row['consultation_type'];
                    
                    $address = $row['address'] . "," . $row['city'] . "," . $row['state'] . "," . $row['pincode'];
                    $image = $row['image'];
                    $clinic_contact_no = $row['phone'];
                    $prescription_id = $row['prescription_id'];
                    $status = $row['status'];
                    $doctor_id = $row['listing_id'];
                    
                      if($status == null)
                    {
                        $status = "";
                    }
                       if($branch_name == null)
                    {
                        $branch_name = "";
                    }
                       if($image == null)
                    {
                        $image = "";
                    }
                       if($prescription_id == null)
                    {
                        $prescription_id = "";
                    }
                       if($clinic_contact_no == null)
                    {
                        $clinic_contact_no = "";
                    }
                    $resultpost61[] = array(
                        'booking_id' => $booking_id,
                        'doctor_id' => $doctor_id,
                        'appointment_date' => $booking_date,
                        'appointment_time' => $booking_time,
                        'patient_name' => $patient_name,
                        'doctor_name' => $doctor_name,
                        'consultation_charge' => $consultation_charges,
                        'consultation_type' => $consultation_type,
                        'clinic_name' => $branch_name,
                        'clinic_address' => $address,
                        'image' => $image,
                        'clinic_contact_no' => $clinic_contact_no,
                        'status' => $status,
                        'prescription_id' => $prescription_id,
                        'booking_type'=>"OPD"
                    );
                }
            }
       
      
          $resultpost5=$resultpost61;
                    
        // Hospitals End Here
        
        // Health Mall Start Here
        
      $resultpost7 = array();
        $pro_list = array();
        if(empty($term))
         {
        $results = $this->db->query("SELECT vd.delivery_by_medicalwale, uo.* FROM `user_order` as uo left join vendor_details_hm as vd on (uo.`listing_id` = vd.v_id)  WHERE uo.`user_id` = '$user_id' AND  uo.`listing_type`='34' ORDER BY order_id DESC");
    }
else
{
      $results = $this->db->query("SELECT vd.delivery_by_medicalwale, uo.* FROM `user_order` as uo left join vendor_details_hm as vd on (uo.`listing_id` = vd.v_id) WHERE uo.`user_id` = '$user_id' AND  uo.`listing_type`='34'  and (user_order.order_id like '%$term%' or user_order.invoice_no like '%$term%' or listing_name like '%$term%') ORDER BY order_id DESC");

}
        foreach($results->result_array() as $order){
            $products = $this->db->query("SELECT * FROM `orders_details_hm` WHERE `order_id` = ".$order['order_id']." AND `user_id` = '$user_id'");
            $details = $products->result_array();
           // print_r($products->result_array());die;
            $pro_list =array();
            $i = 0;
           foreach($products->result_array() as $pro){
                $product_qty = $pro['product_qty'];
                $price = $pro['price'];  
                $res = $this->db->query("SELECT brand_name,pd_name,pd_id, pd_photo_1,pd_mrp_price,pd_vendor_price FROM product_details_hm WHERE pd_id = '".$pro['product_id']."'")->result_array();
                $res[0]['product_qty'] = $product_qty;
                $res[0]['price'] = $price;
                
                if($pro['variable_pd_id'] > 0){
                    $variable_pd_id = $pro['variable_pd_id'];
                    $variable_product = $this->db->query("SELECT * FROM `variable_products_hm` WHERE `id` = $variable_pd_id ")->row_array(); 
                    
                    $colorId = $variable_product['color'];
                    $sizeId = $variable_product['size'];
                    $color = $this->get_color_by_id($colorId);
                    $size = $this->get_size_by_id($sizeId);
                    
                    $c = $color[0]['color'];
                    $s = $size[0]['size_name'];
                    $prodName = $res[0]['pd_name'];
                    if($s != null && $s != ''){
                        $prodName = $prodName." - $s"    ;
                    }
                    if($c != null && $c != ''){
                        $prodName = $prodName." - $c"    ;
                    }                   
                    $res[0]['pd_name'] = $prodName;
                    
                  // print_r($variable_product); die();
                    
                    if(!empty($variable_product['image'])){
                        $res[0]['pd_photo_1'] = $variable_product['image'];
                        $res[0]['pd_mrp_price'] = $variable_product['price'];
                        $res[0]['pd_vendor_price'] = $variable_product['vendor_price'];
                    }
                   // $res[0]['pd_photo_1'] = $variable_product['image'];
                    
                    $res[0]['variable_product'] = $variable_product;
                    //print_r($variable_product); die();   
                    
                } else {
                    $variable_product = (object)[];
                    $res[0]['variable_product']=$variable_product;
                } 
               
                array_push($pro_list,$res[0]);
               
                $i++;
            }
           // print_r($pro_list); die();
            $order += ['products'=>$pro_list];
                foreach($order as $key => $value){
                    if($value == null){
                        $order[$key] = "";
                    }
                }
            $resultpost7[] = $order;
        }
        // Health Mall End Here
        
          /*pharmacy prescription Start Here*/
          
          if(empty($term))
          {
                 $query = $this->db->query("SELECT user_order.*,medical_stores.medical_name FROM user_order LEFT JOIN medical_stores on user_order.listing_id=medical_stores.id WHERE  (user_order.listing_type='13' or user_order.listing_type='38' )  AND user_order.user_id='$user_id' group by user_order.invoice_no order by user_order.order_id DESC");
}
 else
          {
             $query = $this->db->query("SELECT user_order.*,medical_stores.medical_name FROM user_order LEFT JOIN medical_stores on user_order.listing_id=medical_stores.id WHERE  (user_order.listing_type='13' or user_order.listing_type='38' )  AND user_order.user_id='$user_id'  and (user_order.listing_name like '%$term%' or user_order.invoice_no like '%$term%')  group by user_order.invoice_no ");
  
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
                  $orderId="";
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
                                "product_img" => $images_1,
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
   if($listing_name=="Instant Order")
{
   $listing_name= "Instant Order";
}
elseif($listing_name=="Favourite Pharmacy")
{
   $listing_name= "Favourite Pharmacy"; 
}
else
{
  $listing_name=$getuser_info_user['medical_name'];
}
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


             if($listing_type=="38")
               {
                   if($rxId == null)
                {
                  //$resultpost=array();
                }
                else
                {
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
               }
               else
               {
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
                    "rxid" => "",
                    "is_cancel" => $is_cancel
                ); 
               }
            }
        } else {
            $resultpost = array();
        }
                
                
                /*pharmacy Prescription End Here*/
        
            $all_booking[] = array(
                'Pharmacy' => $resultpost, 
                'Labs'=>$resultpost4,
                'Doctors'=>$resultpost2,
                'Healthmall'=>$resultpost7,
                'Hospital'=>$resultpost5,
                'Fitness Centres' => $resultpost1,
                'Nursing Attendant'=>$resultpost3,
                
                
                
                
            );
                
             return $all_booking;
    }
   
   

  
 // Search in Appointment list Added By Dhaval
   public function search_list($user_id, $keyword) {
        if ($user_id > 0) {
           $partss = explode(' ', $keyword);
           $parts = $partss;
           $keywords = $keyword;
            
            // Doctor Start Here
            $field1 = '';
            $field2 = '';
            $field3 = '';
            if(count($parts) > 1)
            {
                for($i=0;$i<count($parts);$i++)
                {
                   
                $doctor_array =array();
                $query2 = $this->db->query("select doctor_booking_master.booking_id, doctor_booking_master.booking_date,doctor_booking_master.listing_id,doctor_booking_master.user_id, doctor_booking_master.listing_id,doctor_booking_master.clinic_id, doctor_prescription.id as prescription_id, doctor_booking_master.booking_time,doctor_booking_master.status,doctor_booking_master.consultation_type, doctor_clinic.clinic_name,users.name as patient_name, doctor_clinic.address,doctor_clinic.image,doctor_clinic.state,doctor_clinic.city,doctor_clinic.pincode, doctor_clinic.consultation_charges,doctor_clinic.contact_no,dl.doctor_name from doctor_booking_master LEFT JOIN doctor_prescription ON (doctor_booking_master.booking_id = doctor_prescription.booking_id AND doctor_booking_master.listing_id = doctor_prescription.doctor_id AND doctor_booking_master.clinic_id = doctor_prescription.clinic_id) LEFT JOIN doctor_clinic on (doctor_booking_master.clinic_id = doctor_clinic.id) LEFT JOIN users ON (doctor_booking_master.user_id = users.id) LEFT JOIN doctor_list as dl ON(dl.user_id=doctor_booking_master.listing_id) where doctor_booking_master.user_id = '$user_id' and (dl.doctor_name like '%$parts[$i]%' or doctor_booking_master.booking_id like '%$parts[$i]%' or doctor_clinic.clinic_name like '%$parts[$i]%' )ORDER BY doctor_booking_master.id DESC");
                $count2 = $query2->num_rows();
                if ($count2 > 0) 
                {
                 foreach ($query2->result_array() as $row2) 
                 {
                    $booking_id = $row2['booking_id'];
                    $booking_date = $row2['booking_date'];
                    $booking_time = $row2['booking_time'];
                    $patient_name = $row2['patient_name'];
                    $doctor_name = $row2['doctor_name'];
                    $consultation_charges = $row2['consultation_charges'];
                    $branch_name = $row2['clinic_name'];
                    $consultation_type = $row2['consultation_type'];
                    $address = $row2['address'] . "," . $row2['city'] . "," . $row2['state'] . "," . $row2['pincode'];
                    $image = $row2['image'];
                    $clinic_contact_no = $row2['contact_no'];
                    $prescription_id = $row2['prescription_id'];
                    $status = $row2['status'];
                    $doctor_id = $row2['listing_id'];
                    $booking_time = str_replace('PM','', $booking_time);
                    $booking_time = str_replace('AM','', $booking_time);
                    if($status == null)
                    {
                        $status = "";
                    }
                     //echo $trimmed ;
                     if($prescription_id!="")
                     {
                    $url="http://vendorsandbox.medicalwale.com/doctor/prescription/".$prescription_id.".pdf";
                     }
                     else
                     {
                         $url="";
                     }
                     if($image== null)
                     {
                         $image="";
                     }
                     if($prescription_id== null)
                     {
                         $prescription_id="";
                     }
                    $resultpost2[] = array(
                        'booking_id' => $booking_id,
                        'doctor_id' => $doctor_id,
                        'appointment_date' => $booking_date,
                        'appointment_time' => $booking_time,
                        'patient_name' => $patient_name,
                        'doctor_name' => $doctor_name,
                        'consultation_charge' => $consultation_charges,
                        'consultation_type' => $consultation_type,
                        'clinic_name' => $branch_name,
                        'clinic_address' => $address,
                        'image' => $image,
                        'clinic_contact_no' => $clinic_contact_no,
                        'status' => $status,
                        'prescription_id' => $prescription_id,
                        'prescription_pdf'=> $url,
                        'listing_type'=>5
                    );
                }
           
                        $doctor_array[] = array(
                            'title' => 'Doctors',
                            'array' => $resultpost2
                        );
                    } else {
                        $doctor_array[] = array(
                            'title' => 'Doctors',
                            'array' => array()
                        );
                    }
                }
            }
            else
            {
                 
                 $doctor_array =array();
                $query2 = $this->db->query("select doctor_booking_master.booking_id, doctor_booking_master.booking_date,doctor_booking_master.listing_id,doctor_booking_master.user_id, doctor_booking_master.listing_id,doctor_booking_master.clinic_id, doctor_prescription.id as prescription_id, doctor_booking_master.booking_time,doctor_booking_master.status,doctor_booking_master.consultation_type, doctor_clinic.clinic_name,users.name as patient_name, doctor_clinic.address,doctor_clinic.image,doctor_clinic.state,doctor_clinic.city,doctor_clinic.pincode, doctor_clinic.consultation_charges,doctor_clinic.contact_no,dl.doctor_name from doctor_booking_master LEFT JOIN doctor_prescription ON (doctor_booking_master.booking_id = doctor_prescription.booking_id AND doctor_booking_master.listing_id = doctor_prescription.doctor_id AND doctor_booking_master.clinic_id = doctor_prescription.clinic_id) LEFT JOIN doctor_clinic on (doctor_booking_master.clinic_id = doctor_clinic.id) LEFT JOIN users ON (doctor_booking_master.user_id = users.id) LEFT JOIN doctor_list as dl ON(dl.user_id=doctor_booking_master.listing_id) where doctor_booking_master.user_id = '$user_id' and (dl.doctor_name like '%$keyword%' or doctor_booking_master.booking_id like '%$keyword%' or doctor_clinic.clinic_name like '%$keyword%') ORDER BY doctor_booking_master.id DESC");
                
             
                
                $count2 = $query2->num_rows();
                if ($count2 > 0) 
                {
                 
                 foreach ($query2->result_array() as $row2) 
                 {
                    $booking_id = $row2['booking_id'];
                    $booking_date = $row2['booking_date'];
                    $booking_time = $row2['booking_time'];
                    $patient_name = $row2['patient_name'];
                    $doctor_name = $row2['doctor_name'];
                    $consultation_charges = $row2['consultation_charges'];
                    $branch_name = $row2['clinic_name'];
                    $consultation_type = $row2['consultation_type'];
                    $address = $row2['address'] . "," . $row2['city'] . "," . $row2['state'] . "," . $row2['pincode'];
                    $image = $row2['image'];
                    $clinic_contact_no = $row2['contact_no'];
                    $prescription_id = $row2['prescription_id'];
                    $status = $row2['status'];
                    $doctor_id = $row2['listing_id'];
                    $booking_time = str_replace('PM','', $booking_time);
                    $booking_time = str_replace('AM','', $booking_time);
                    if($status == null)
                    {
                        $status = "";
                    }
                     //echo $trimmed ;
                     if($prescription_id!="")
                     {
                    $url="http://vendorsandbox.medicalwale.com/doctor/prescription/".$prescription_id.".pdf";
                     }
                     else
                     {
                         $url="";
                     }
                     if($image== null)
                     {
                         $image="";
                     }
                     if($prescription_id== null)
                     {
                         $prescription_id="";
                     }
                    $resultpost2[] = array(
                        'booking_id' => $booking_id,
                        'doctor_id' => $doctor_id,
                        'appointment_date' => $booking_date,
                        'appointment_time' => $booking_time,
                        'patient_name' => $patient_name,
                        'doctor_name' => $doctor_name,
                        'consultation_charge' => $consultation_charges,
                        'consultation_type' => $consultation_type,
                        'clinic_name' => $branch_name,
                        'clinic_address' => $address,
                        'image' => $image,
                        'clinic_contact_no' => $clinic_contact_no,
                        'status' => $status,
                        'prescription_id' => $prescription_id,
                        'prescription_pdf'=> $url,
                        'listing_type'=>5
                    );
                }
           
                        $doctor_array[] = array(
                            'title' => 'Doctors',
                            'array' => $resultpost2
                        );
                    } else {
                         $doctor_array[] = array(
                            'title' => 'Doctors',
                            'array' => array()
                        );
                    }
            }
            // Doctor End Here
            
            
             // Fitness Start Here
           $field1 = '';
            $field2 = '';
            $field3 = '';
            if(count($parts) > 1)
            {
                for($i=0;$i<count($parts);$i++)
                {
                 $Fitness_array =array();
                  $querys1 = $this->db->query("SELECT fitness_center_branch.branch_name,fitness_center_branch.user_id as branch_fit_id, fitness_center_branch.branch_image, fitness_center_branch.branch_phone, fitness_center_branch.branch_address, fitness_center_branch.pincode, fitness_center_branch.state, fitness_center_branch.city,packages.package_name, packages.package_details, packages.price,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time, booking_master.joining_date FROM booking_master
            INNER JOIN fitness_center_branch ON booking_master.branch_id=fitness_center_branch.id
            INNER JOIN packages ON booking_master.package_id=packages.id
            WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='6' and (fitness_center_branch.branch_name like '%$parts[$i]%' or packages.package_name like '%$parts[$i]%' or booking_master.booking_id like '%$parts[$i]%') ORDER BY booking_master.id DESC");
            $count1 = $querys1->num_rows();
            if ($count1 > 0) {
                foreach ($querys1->result_array() as $row1) {
                    $package_id = $row1['package_id'];
                    $booking_id = $row1['booking_id'];
                    $listing_id = $row1['branch_fit_id'];
                     $branch_id = $row1['branch_id'];
                    $branch_name = $row1['branch_name'];
                    $branch_image = $row1['branch_image'];
                    $branch_phone = $row1['branch_phone'];
                    $branch_address = $row1['branch_address'];
                    $branch_pincode = $row1['pincode'];
                    $branch_city = $row1['city'];
                    $branch_state = $row1['state'];
                    $appointment_user_name = $row1['user_name'];
                    $appointment_user_mobile = $row1['user_mobile'];
                    $appointment_user_email = $row1['user_email'];
                    $category_id = $row1['category_id'];
                    $status = $row1['status'];
                    $package_name = $row1['package_name'];
                    $package_details = $row1['package_details'];
                    $package_price = $row1['price'];
                    $trail_booking_date = $row1['trail_booking_date'];
                    $trail_booking_time = $row1['trail_booking_time'];
                    $joining_date = date('j M Y', strtotime($row1['joining_date']));
                    $book_for_date = date('d/m/Y', strtotime($row1['joining_date']));
                    $is_free_trial = 'No';
                    date_default_timezone_set('Asia/Kolkata');
                    if ($package_id == '100') {
                        $joining_date_ = $trail_booking_date . ' ' . $trail_booking_time;

                        $joining_date = date('j M Y | h:i A', strtotime($joining_date_));
                        $is_free_trial = 'Yes';
                    }
                    $booking_date = date('j M Y | h:i A', strtotime($row1['booking_date']));

                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
                    }
                    
                    if($status == null)
                    {
                        $status = "";
                    }
                   


                    $resultpost1[] = array(
                        'booking_id' => $booking_id,
                        'branch_name' => $branch_name,
                        'branch_id' => $branch_id,
                        'listing_id' => $listing_id,
                        'branch_image' => $branch_image,
                        'branch_phone' => $branch_phone,
                        'branch_address' => $branch_address,
                        'branch_pincode' => $branch_pincode,
                        'branch_city' => $branch_city,
                        'branch_state' => $branch_state,
                        'appointment_user_name' => $appointment_user_name,
                        'appointment_user_mobile' => $appointment_user_mobile,
                        'appointment_user_email' => $appointment_user_email,
                        'package_name' => $package_name,
                        'package_details' => $package_details,
                        'package_price' => $package_price,
                        'is_free_trial' => $is_free_trial,
                        'joining_date' => $joining_date,
                        'book_for_date' => $book_for_date,
                        'booking_date' => $booking_date,
                        'status' => $status,
                        'listing_type'=>$row1['vendor_id']
                    );
                   
                }
                  $Fitness_array[] = array(
                            'title' => 'Fitness Centres',
                            'array' => $resultpost1
                        );
            } else {
                 $Fitness_array[] = array(
                            'title' => 'Fitness Centres',
                            'array' => array()
                        );
            }
           
                       
                  
                }
            }
            else
            {
                 
                 $Fitness_array =array();
                  $querys1 = $this->db->query("SELECT fitness_center_branch.branch_name,fitness_center_branch.user_id as branch_fit_id, fitness_center_branch.branch_image, fitness_center_branch.branch_phone, fitness_center_branch.branch_address, fitness_center_branch.pincode, fitness_center_branch.state, fitness_center_branch.city,packages.package_name, packages.package_details, packages.price,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time, booking_master.joining_date FROM booking_master
            INNER JOIN fitness_center_branch ON booking_master.branch_id=fitness_center_branch.id
            INNER JOIN packages ON booking_master.package_id=packages.id
            WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='6' and (fitness_center_branch.branch_name like '%$keyword%' or packages.package_name like '%$keyword%' or booking_master.booking_id like '%$keyword%') ORDER BY booking_master.id DESC");
            $count1 = $querys1->num_rows();
            if ($count1 > 0) {
                foreach ($querys1->result_array() as $row1) {
                    $package_id = $row1['package_id'];
                    $booking_id = $row1['booking_id'];
                    $listing_id = $row1['branch_fit_id'];
                     $branch_id = $row1['branch_id'];
                    $branch_name = $row1['branch_name'];
                    $branch_image = $row1['branch_image'];
                    $branch_phone = $row1['branch_phone'];
                    $branch_address = $row1['branch_address'];
                    $branch_pincode = $row1['pincode'];
                    $branch_city = $row1['city'];
                    $branch_state = $row1['state'];
                    $appointment_user_name = $row1['user_name'];
                    $appointment_user_mobile = $row1['user_mobile'];
                    $appointment_user_email = $row1['user_email'];
                    $category_id = $row1['category_id'];
                    $status = $row1['status'];
                    $package_name = $row1['package_name'];
                    $package_details = $row1['package_details'];
                    $package_price = $row1['price'];
                    $trail_booking_date = $row1['trail_booking_date'];
                    $trail_booking_time = $row1['trail_booking_time'];
                    $joining_date = date('j M Y', strtotime($row1['joining_date']));
                    $book_for_date = date('d/m/Y', strtotime($row1['joining_date']));
                    $is_free_trial = 'No';
                    date_default_timezone_set('Asia/Kolkata');
                    if ($package_id == '100') {
                        $joining_date_ = $trail_booking_date . ' ' . $trail_booking_time;

                        $joining_date = date('j M Y | h:i A', strtotime($joining_date_));
                        $is_free_trial = 'Yes';
                    }
                    $booking_date = date('j M Y | h:i A', strtotime($row1['booking_date']));

                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
                    }
                    
                    if($status == null)
                    {
                        $status = "";
                    }
                   


                    $resultpost1[] = array(
                        'booking_id' => $booking_id,
                        'branch_name' => $branch_name,
                        'branch_id' => $branch_id,
                        'listing_id' => $listing_id,
                        'branch_image' => $branch_image,
                        'branch_phone' => $branch_phone,
                        'branch_address' => $branch_address,
                        'branch_pincode' => $branch_pincode,
                        'branch_city' => $branch_city,
                        'branch_state' => $branch_state,
                        'appointment_user_name' => $appointment_user_name,
                        'appointment_user_mobile' => $appointment_user_mobile,
                        'appointment_user_email' => $appointment_user_email,
                        'package_name' => $package_name,
                        'package_details' => $package_details,
                        'package_price' => $package_price,
                        'is_free_trial' => $is_free_trial,
                        'joining_date' => $joining_date,
                        'book_for_date' => $book_for_date,
                        'booking_date' => $booking_date,
                        'status' => $status,
                        'listing_type'=>$row1['vendor_id']
                    );
                     
                }
                $Fitness_array[] = array(
                            'title' => 'Fitness Centres',
                            'array' => $resultpost1
                        );
            } else {
                 $Fitness_array[] = array(
                            'title' => 'Fitness Centres',
                            'array' => array()
                        );
            }
           
            }
            // Fitness End Here
            
              // Nursing Start Here
            $field1 = '';
            $field2 = '';
            $field3 = '';
            if(count($parts) > 1)
            {
                for($i=0;$i<count($parts);$i++)
                {
                 $Nursing_array =array();
                   $query3 = $this->db->query("SELECT booking_master.*,health_record.patient_name,nursing_attendant.address,nursing_attendant.city,nursing_attendant.state,nursing_attendant.contact,nursing_attendant.pincode,nursing_booking_details.* FROM booking_master  LEFT JOIN users ON (booking_master.user_id = users.id) LEFT JOIN health_record ON(booking_master.patient_id=health_record.id) LEFT JOIN nursing_attendant ON(booking_master.listing_id=nursing_attendant.user_id) LEFT JOIN nursing_booking_details ON(booking_master.booking_id=nursing_booking_details.booking_id) where booking_master.user_id = '$user_id' and booking_master.vendor_id = '12'
                       and (nursing_booking_details.package_name like '%$parts[$i]%' or booking_master.booking_id like '%$parts[$i]%')ORDER BY booking_master.id DESC");

            $count3 = $query3->num_rows();
            if ($count3 > 0) {
                foreach ($query3->result_array() as $row3) {
                    
                    $listing_id = $row3['listing_id'];
                    $package_name = $row3['package_name'];
                    $package_amount = $row3['package_amount'];
                    $package_image = $row3['package_image'];
                    
                    
                    $booking_id = $row3['booking_id'];
                    $package_id = $row3['package_id'];
                    $booking_date = $row3['booking_date'];
                    $patient_name = $row3['patient_name'];
                    $address = $row3['address'] . "," . $row3['city'] . ",".$row3['state'] . "," . $row3['pincode'];
                    $contact_no = $row3['contact'];
                    $status = $row3['status'];
                    $Nursing_id = $row3['listing_id'];
                    $patiente_condition =$row3['patiente_condition'];
                    $attendent_time =$row3['attendent_time'];
                    $attendant_hour = $row3['attendant_hour'];
                    $tentative_intime = $row3['tentative_intime'];
                    $tentative_outtime = $row3['tentative_outtime'];
                    $nursing_gender = $row3['nursing_gender'];
                    $attendant_needed = $row3['attendant_needed'];
                    $joining_date = $row3['joining_date'];
                    
                      if($status == null)
                    {
                        $status = "";
                    }
                    
                    $resultpost3[] = array(
                        'listing_id' => $listing_id,
                        'booking_id' => $booking_id,
                        'nursing_id' => $Nursing_id,
                        'appointment_date' => $booking_date,
                        'patient_name' => $patient_name,
                        'package_id' => $package_id,
                        'package_name' => $package_name,
                        'package_amount' => $package_amount,
                        'package_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$package_image,
                        'address'=>$address,
                        'phone' =>$contact_no,
                        'patiente_condition' =>$patiente_condition,
                        'attendent_time' =>$attendent_time,
                        'attendant_hour' =>$attendant_hour,
                        'tentative_intime' =>$tentative_intime,
                        'nursing_gender' =>$nursing_gender,
                        'attendant_needed' =>$attendant_needed,
                        'booking_date'=>$joining_date,
                        'status' => $status,
                        'listing_type'=>$row3['vendor_id']
                    );
                }
                  $Nursing_array[] = array(
                            'title' => 'Nursing Attendant',
                            'array' => $resultpost3
                        );
            } else {
                 $Nursing_array[] = array(
                            'title' => 'Nursing Attendant',
                            'array' => array()
                        );
            }
           
                       
                  
                }
            }
            else
            {
                 
                 $Nursing_array =array();
                  $query3 = $this->db->query("SELECT booking_master.*,health_record.patient_name,nursing_attendant.address,nursing_attendant.city,nursing_attendant.state,nursing_attendant.contact,nursing_attendant.pincode,nursing_booking_details.* FROM booking_master  LEFT JOIN users ON (booking_master.user_id = users.id) LEFT JOIN health_record ON(booking_master.patient_id=health_record.id) LEFT JOIN nursing_attendant ON(booking_master.listing_id=nursing_attendant.user_id) LEFT JOIN nursing_booking_details ON(booking_master.booking_id=nursing_booking_details.booking_id) where booking_master.user_id = '$user_id' and booking_master.vendor_id = '12'
                       and (nursing_booking_details.package_name like '%$keyword%' or booking_master.booking_id like '%$keyword%')ORDER BY booking_master.id DESC");

            $count3 = $query3->num_rows();
            if ($count3 > 0) {
                foreach ($query3->result_array() as $row3) {
                    
                    $listing_id = $row3['listing_id'];
                    $package_name = $row3['package_name'];
                    $package_amount = $row3['package_amount'];
                    $package_image = $row3['package_image'];
                    
                    
                    $booking_id = $row3['booking_id'];
                    $package_id = $row3['package_id'];
                    $booking_date = $row3['booking_date'];
                    $patient_name = $row3['patient_name'];
                    $address = $row3['address'] . "," . $row3['city'] . ",".$row3['state'] . "," . $row3['pincode'];
                    $contact_no = $row3['contact'];
                    $status = $row3['status'];
                    $Nursing_id = $row3['listing_id'];
                    $patiente_condition =$row3['patiente_condition'];
                    $attendent_time =$row3['attendent_time'];
                    $attendant_hour = $row3['attendant_hour'];
                    $tentative_intime = $row3['tentative_intime'];
                    $tentative_outtime = $row3['tentative_outtime'];
                    $nursing_gender = $row3['nursing_gender'];
                    $attendant_needed = $row3['attendant_needed'];
                    $joining_date = $row3['joining_date'];
                    
                      if($status == null)
                    {
                        $status = "";
                    }
                    
                    $resultpost3[] = array(
                        'listing_id' => $listing_id,
                        'booking_id' => $booking_id,
                        'nursing_id' => $Nursing_id,
                        'appointment_date' => $booking_date,
                        'patient_name' => $patient_name,
                        'package_id' => $package_id,
                        'package_name' => $package_name,
                        'package_amount' => $package_amount,
                        'package_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$package_image,
                        'address'=>$address,
                        'phone' =>$contact_no,
                        'patiente_condition' =>$patiente_condition,
                        'attendent_time' =>$attendent_time,
                        'attendant_hour' =>$attendant_hour,
                        'tentative_intime' =>$tentative_intime,
                        'nursing_gender' =>$nursing_gender,
                        'attendant_needed' =>$attendant_needed,
                        'booking_date'=>$joining_date,
                        'status' => $status,
                        'listing_type'=>$row3['vendor_id']
                    );
                }
                $Nursing_array[] = array(
                            'title' => 'Nursing Attendant',
                            'array' => $resultpost3
                        );
            } else {
                 $Nursing_array[] = array(
                            'title' => 'Nursing Attendant',
                            'array' => array()
                        );
            }
           
            }
            // Nursing End Here
            
           // Lab Start Here
            $field1 = '';
            $field2 = '';
            $field3 = '';
            if(count($parts) > 1)
            {
                for($i=0;$i<count($parts);$i++)
                {
                 $Labs_array =array();
                 $count_query4 = $this->db->query("SELECT lb.*,bm.vendor_id,bm.status from booking_master as bm LEFT JOIN lab_booking_details as lb ON(bm.booking_id = lb.booking_id) where bm.user_id='$user_id' and (bm.vendor_id='10' or bm.vendor_id='31') and (lb.product like '%$parts[$i]%' or bm.booking_id like '%$parts[$i]%' or lb.branch_name like '%$parts[$i]%')");
            $lab_booked4       = $count_query4->num_rows();
            
            if ($lab_booked4 > 0) {
                foreach ($count_query->result_array() as $Lbooked) {
                      
                      if($Lbooked['vendor_id']=="31")
                    {
                        $uid =$Lbooked['user_id'];    
            $user_query = $this->db->query("SELECT email,phone,name FROM users WHERE id='$uid'");
                $email = $user_query->row()->email;
                $phone = $user_query->row()->phone;
                $user_name = $user_query->row()->name;        
                    
                $bk_id = $Lbooked['booking_id'];   
                /*echo"SELECT status FROM booking_master WHERE booking_id='$bk_id'";*/
            $book_query = $this->db->query("SELECT status FROM booking_master WHERE booking_id='$bk_id'");
            $status = $book_query->row()->status;
            
            
            //NAWAZ
            //echo "SELECT report_path FROM reports WHERE booking_id='$bk_id'";
            $report_path ='';
            $book_query_path = $this->db->query("SELECT report_path FROM reports WHERE booking_id='$bk_id'");
            $report_path_count =  $book_query_path->num_rows();;
            if($report_path_count > 0){
                $report_path = $book_query_path->row()->report_path;
            }
            
                      if($Lbooked['reference_id']!='')
                      {
                    
                $resultpost4[] = array(
                'user_id'=> $Lbooked['user_id'], 
                'user_name'=>$user_name,
                'amount'=> $Lbooked['amount'],
                'report_code'=> $Lbooked['report_code'],
                'becount'=> $Lbooked['becount'],
                'hc'=> $Lbooked['hc'],
                'ref_code'=> $Lbooked['ref_code'],
                'reports'=> $Lbooked['reports'],
                'bendataxml'=> $Lbooked['bendataxml'],
                'product'=> $Lbooked['product'],
                'vendor_type'=> $Lbooked['vendor_type'],
                'address_line1'=> $Lbooked['address_line1'],
                'pincode'=> $Lbooked['pincode'],
                'mobile_no'=> $Lbooked['mobile_no'],
                'email_id'=> $Lbooked['email_id'],
                'booking_date'=> $Lbooked['booking_date'],
                'booking_time'=> $Lbooked['booking_time'],
                'booking_id'=> $Lbooked['booking_id'],
                'reference_id'=> $Lbooked['reference_id'],
                'lead_id'=> $Lbooked['lead_id'],
                'status'=>$status,
                'report_path'=>$report_path,
                "listing_type"=> "31"
                );
                      }
                    }  
                     else
                     {
                        
                    $user_id = $Lbooked['user_id'];
                    $patient_id = $Lbooked['patient_id'];
                    $listing_id = $Lbooked['listing_id'];
                    $vendor_type = $Lbooked['vendor_type'];
                    $branch_id = $Lbooked['branch_id'];
                    $branch_name = $Lbooked['branch_name'];
                    $at_home = $Lbooked['at_home'];
                    $address_line1 = $Lbooked['address_line1'];
                    $address_line2 = $Lbooked['address_line2'];
                    $city = $Lbooked['city'];
                    $state = $Lbooked['state'];
                    $pincode = $Lbooked['pincode'];
                    $mobile_no = $Lbooked['mobile_no'];
                    $email_id = $Lbooked['email_id'];
                    $address_id = $Lbooked['address_id'];
                    $test_ids = $Lbooked['test_id'];
                    $package_id = $Lbooked['package_id'];
                    $booking_date = $Lbooked['booking_date'];
                    $booking_time = $Lbooked['booking_time'];
                    $booking_id = $Lbooked['booking_id']; 
                    $status = $Lbooked['status']; 
                    
                    
                    $Booed_test_list = array();
                    if ($test_ids != '' && $test_ids != '0') {
                        $Testids = explode(',', $test_ids);
                        
                        foreach ($Testids as $tid) {
                          //  echo "SELECT * FROM lab_test_details WHERE test_id = '$tid'";
                            $Query = $this->db->query("SELECT * FROM lab_test_details WHERE test_id = '$tid'");
                            $Comp = $Query->row();
                            $comp_count = $Query->num_rows();
                            //print_r($Comp);
                            if($comp_count>0)
                            {
                                $test           = $Comp->test;
                                $test_id        = $Comp->test_id;
                                $price          = $Comp->price;
                                $offer          = $Comp->offer;
                                $executive_rate = $Comp->executive_rate;
                                $home_delivery  = $Comp->home_delivery;
                            
                                
                                 $Booed_test_list[] = array(
                                    'test_id' => $test,
                                    'test' => $test_id,
                                    'price' => $price,
                                    'home_delivery' => "0");
                            }
                           
                        }
                    }
                     $lab_pack_name = "";
                        $pack_details = "";
                        $pack_amount = "";
                    
                    if($package_id > 0){
                         
                        $LP_query = $this->db->query("SELECT * FROM lab_packages1 WHERE id='$package_id'");
                        $result1 = $LP_query->num_rows();
                        if($result1 > 0)
                        {
                        $lab_pack_name = $LP_query->row()->package_name;
                        $pack_details = $LP_query->row()->package_details;
                        $pack_amount = $LP_query->row()->Price;
                        }
                    }
                      if($status == null)
                    {
                        $status = "";
                    }
                    
                    $resultpost4[] = array(
                        'user_id'=> $user_id,
                        'patient_id'=> $patient_id,
                        'listing_id'=> $listing_id,
                        'vendor_type'=> $vendor_type,
                        'branch_id'=> $branch_id,
                        'branch_name'=> $branch_name,
                        'at_home'=> $at_home,
                        'address_line1'=> $address_line1,
                        'address_line2'=> $address_line2,
                        'city'=> $city,
                        'state'=> $state,
                        'pincode'=> $pincode,
                        'mobile_no'=> $mobile_no,
                        'email_id'=> $email_id,
                        'address_id'=> $address_id,
                        'test_id'=> $test_ids,
                        'package_id'=> $package_id,
                        'package_name'=> $lab_pack_name,
                        'package_details'=> $pack_details,
                        'package_price'=> $pack_amount,
                        'booking_date'=> $booking_date,
                        'booking_time'=> $booking_time,
                        'booking_id'=> $booking_id,
                        'booked_tests'=>$Booed_test_list,
                        'status'=>$status
                    );
                     }
                }
                  $Labs_array[] = array(
                            'title' => 'Labs',
                            'array' => $resultpost4
                        );
            } else {
                 $Labs_array[] = array(
                            'title' => 'Labs',
                            'array' => array()
                        );
            }
           
                       
                  
                }
            }
            else
            {
                 
                 $Labs_array =array();
                  $count_query4 = $this->db->query("SELECT lb.*,bm.vendor_id,bm.status from booking_master as bm LEFT JOIN lab_booking_details as lb ON(bm.booking_id = lb.booking_id) where bm.user_id='$user_id' and (bm.vendor_id='10' or bm.vendor_id='31') and (lb.product like '%$keywords%' or bm.booking_id like '%$keywords%' or lb.branch_name like '%$keywords%')");
            $lab_booked4       = $count_query4->num_rows();
            
            if ($lab_booked4 > 0) {
                foreach ($count_query4->result_array() as $Lbooked) {
                     
                    if($Lbooked['vendor_id']=="31")
                    {
                        $uid =$Lbooked['user_id'];    
            $user_query = $this->db->query("SELECT email,phone,name FROM users WHERE id='$uid'");
                $email = $user_query->row()->email;
                $phone = $user_query->row()->phone;
                $user_name = $user_query->row()->name;        
                    
                $bk_id = $Lbooked['booking_id'];   
                /*echo"SELECT status FROM booking_master WHERE booking_id='$bk_id'";*/
            $book_query = $this->db->query("SELECT status FROM booking_master WHERE booking_id='$bk_id'");
            $status = $book_query->row()->status;
            
            
            //NAWAZ
            //echo "SELECT report_path FROM reports WHERE booking_id='$bk_id'";
            $report_path ='';
            $book_query_path = $this->db->query("SELECT report_path FROM reports WHERE booking_id='$bk_id'");
            $report_path_count =  $book_query_path->num_rows();;
            if($report_path_count > 0){
                $report_path = $book_query_path->row()->report_path;
            }
            
                      if($Lbooked['reference_id']!='')
                      {
                    
                $resultpost4[] = array(
                'user_id'=> $Lbooked['user_id'], 
                'user_name'=>$user_name,
                'amount'=> $Lbooked['amount'],
                'report_code'=> $Lbooked['report_code'],
                'becount'=> $Lbooked['becount'],
                'hc'=> $Lbooked['hc'],
                'ref_code'=> $Lbooked['ref_code'],
                'reports'=> $Lbooked['reports'],
                'bendataxml'=> $Lbooked['bendataxml'],
                'product'=> $Lbooked['product'],
                'vendor_type'=> $Lbooked['vendor_type'],
                'address_line1'=> $Lbooked['address_line1'],
                'pincode'=> $Lbooked['pincode'],
                'mobile_no'=> $Lbooked['mobile_no'],
                'email_id'=> $Lbooked['email_id'],
                'booking_date'=> $Lbooked['booking_date'],
                'booking_time'=> $Lbooked['booking_time'],
                'booking_id'=> $Lbooked['booking_id'],
                'reference_id'=> $Lbooked['reference_id'],
                'lead_id'=> $Lbooked['lead_id'],
                'status'=>$status,
                'report_path'=>$report_path,
                "listing_type"=> "31"
                );
                      }
                    }
                     else
                     {
                        
                    $user_id = $Lbooked['user_id'];
                    $patient_id = $Lbooked['patient_id'];
                    $listing_id = $Lbooked['listing_id'];
                    $vendor_type = $Lbooked['vendor_type'];
                    $branch_id = $Lbooked['branch_id'];
                    $branch_name = $Lbooked['branch_name'];
                    $at_home = $Lbooked['at_home'];
                    $address_line1 = $Lbooked['address_line1'];
                    $address_line2 = $Lbooked['address_line2'];
                    $city = $Lbooked['city'];
                    $state = $Lbooked['state'];
                    $pincode = $Lbooked['pincode'];
                    $mobile_no = $Lbooked['mobile_no'];
                    $email_id = $Lbooked['email_id'];
                    $address_id = $Lbooked['address_id'];
                    $test_ids = $Lbooked['test_id'];
                    $package_id = $Lbooked['package_id'];
                    $booking_date = $Lbooked['booking_date'];
                    $booking_time = $Lbooked['booking_time'];
                    $booking_id = $Lbooked['booking_id']; 
                    $status = $Lbooked['status']; 
                    
                    
                    $Booed_test_list = array();
                    if ($test_ids != '' && $test_ids != '0') {
                        $Testids = explode(',', $test_ids);
                        
                        foreach ($Testids as $tid) {
                          //  echo "SELECT * FROM lab_test_details WHERE test_id = '$tid'";
                            $Query = $this->db->query("SELECT * FROM lab_test_details WHERE test_id = '$tid'");
                            $Comp = $Query->row();
                            $comp_count = $Query->num_rows();
                            //print_r($Comp);
                            if($comp_count>0)
                            {
                                $test           = $Comp->test;
                                $test_id        = $Comp->test_id;
                                $price          = $Comp->price;
                                $offer          = $Comp->offer;
                                $executive_rate = $Comp->executive_rate;
                                $home_delivery  = $Comp->home_delivery;
                            
                                
                                 $Booed_test_list[] = array(
                                    'test_id' => $test,
                                    'test' => $test_id,
                                    'price' => $price,
                                    'home_delivery' => "0");
                            }
                           
                        }
                    }
                    
                     $lab_pack_name = "";
                        $pack_details = "";
                        $pack_amount = "";
                    if($package_id > 0){
                         
                        $LP_query = $this->db->query("SELECT * FROM lab_packages1 WHERE id='$package_id'");
                        $result1 = $LP_query->num_rows();
                        if($result1 > 0)
                        {
                        $lab_pack_name = $LP_query->row()->package_name;
                        $pack_details = $LP_query->row()->package_details;
                        $pack_amount = $LP_query->row()->Price;
                        }
                    }
                      if($status == null)
                    {
                        $status = "";
                    }
                    
                    $resultpost4[] = array(
                        'user_id'=> $user_id,
                        'patient_id'=> $patient_id,
                        'listing_id'=> $listing_id,
                        'vendor_type'=> $vendor_type,
                        'branch_id'=> $branch_id,
                        'branch_name'=> $branch_name,
                        'at_home'=> $at_home,
                        'address_line1'=> $address_line1,
                        'address_line2'=> $address_line2,
                        'city'=> $city,
                        'state'=> $state,
                        'pincode'=> $pincode,
                        'mobile_no'=> $mobile_no,
                        'email_id'=> $email_id,
                        'address_id'=> $address_id,
                        'test_id'=> $test_ids,
                        'package_id'=> $package_id,
                        'package_name'=> $lab_pack_name,
                        'package_details'=> $pack_details,
                        'package_price'=> $pack_amount,
                        'booking_date'=> $booking_date,
                        'booking_time'=> $booking_time,
                        'booking_id'=> $booking_id,
                        'booked_tests'=>$Booed_test_list,
                        'status'=>$status
                    );
                     }
                }
                $Labs_array[] = array(
                            'title' => 'Labs',
                            'array' => $resultpost4
                        );
            } else {
                 $Labs_array[] = array(
                            'title' => 'Labs',
                            'array' => array()
                        );
            }
           
            }
            // Lab End Here 
            
            
            // Pharmacy Start Here
            $field1 = '';
            $field2 = '';
            $field3 = '';
            if(count($parts) > 1)
            {
                for($i=0;$i<count($parts);$i++)
                {
                 $Pharmacy_array =array();
                 $query = $this->db->query("SELECT user_order.*,medical_stores.medical_name FROM user_order LEFT JOIN medical_stores on user_order.listing_id=medical_stores.id WHERE  (user_order.listing_type='13' or user_order.listing_type='38' )  AND user_order.user_id='$user_id' and (user_order.listing_name like '%$parts[$i]%' or user_order.invoice_no like '%$parts[$i]%') group by user_order.invoice_no ");



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
                   $orderId="";
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
                                "product_img" => $images_1,
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
    if($listing_name=="Instant Order")
{
   $listing_name= "Instant Order";
}
elseif($listing_name=="Favourite Pharmacy")
{
   $listing_name= "Favourite Pharmacy"; 
}
else
{
  $listing_name=$getuser_info_user['medical_name'];
}
   
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

                  if($listing_type=="38")
               {
                   if($rxId != 'NULL')
                {
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
                else
                {
                    $resultpost = array();
                }
               }
               else
               {
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
                }
                  $Pharmacy_array[] = array(
                            'title' => 'Pharmacy',
                            'array' => $resultpost
                        );
            } else {
                 $Pharmacy_array[] = array(
                            'title' => 'Pharmacy',
                            'array' => array()
                        );
            }
           
                       
                  
                }
            }
            else
            {
              $Pharmacy_array =array();
                 $query = $this->db->query("SELECT user_order.*,medical_stores.medical_name FROM user_order LEFT JOIN medical_stores on user_order.listing_id=medical_stores.id WHERE  (user_order.listing_type='13' or user_order.listing_type='38' )  AND user_order.user_id='$user_id'  and (user_order.listing_name like '%$keywords%' or user_order.invoice_no like '%$keywords%')  group by user_order.invoice_no ");


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
                   $orderId="";
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
                                "product_img" => $images_1,
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

                 if($listing_type=="38")
               {
                   if($rxId != 'NULL')
                {
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
                else
                {
                    $resultpost = array();
                }
               }
               else
               {
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
                }
                $Pharmacy_array[] = array(
                            'title' => 'Pharmacy',
                            'array' => $resultpost
                        );
            } else {
                 $Pharmacy_array[] = array(
                            'title' => 'Pharmacy',
                            'array' => array()
                        );
            }
           
            }
            // Pharmacy End Here 
            
          // Hospitals Start Here
            $field1 = '';
            $field2 = '';
            $field3 = '';
            if(count($parts) > 1)
            {
                for($i=0;$i<count($parts);$i++)
                {
                 $Hospitals_array = array();
       $booking_details = $this->db->query("SELECT bm.*,bm.id as ids, hp.package_name FROM booking_master as bm LEFT JOIN hospital_packages as hp ON (bm.package_id=hp.id) WHERE user_id='$user_id' and vendor_id='8' and (hp.package_name like '%$parts[$i]%' or bm.booking_id like '%$parts[$i]%') order by id DESC");
         
        $booking_count = $booking_details->num_rows();
        if($booking_count>0)
        {
           foreach($booking_details ->result_array() as $row )
           {
               $id = $row['ids'];
               $booking_id = $row['booking_id'];
               $package_id = $row['package_id'];
              $listing_id = $row['listing_id'];
              $package_name= $row['package_name'];
              $vendor_detaild = $this->db->query("SELECT name,phone FROM users WHERE id='$listing_id' ");
              $vendor_count = $vendor_detaild->num_rows();
              $vendor_name ='';
              if($vendor_count>0)
              {
             $vendor_name = $vendor_detaild->row()->name;
              }
              
              $hospital_details = $this->db->query("SELECT * FROM hospital_booking_details WHERE booking_id='$booking_id' and vendor_type='8' order by id DESC");
              $hospital_count = $hospital_details->num_rows();
              $hospital_name ='';
              if($hospital_count>0)
              {
             $ward_id = $hospital_details->row()->ward_id;
             $amount = $hospital_details->row()->amount;
             $patient_preferred_date = $hospital_details->row()->patient_preferred_date;
              }
              
              $ward_details = $this->db->query("SELECT * FROM hospital_wards WHERE hospital_id='$listing_id'");
              $ward_count = $ward_details->num_rows();
              $room_type ='';
                $capacity ='';
                  $price ='';
              if($ward_count>0)
              {
             $room_type = $ward_details->row()->room_type;
             $capacity = $ward_details->row()->capacity;
             $price =  $ward_details->row()->price;
              }
              
              /*$pack_details = $this->db->query("SELECT * FROM hospital_packages WHERE id='$package_id'");
              $pack_count = $pack_details->num_rows();
            
              if($pack_count>0)
              {
             $package_name = $pack_details->row()->package_name;
              }
              */
              
              
              $user_id = $row['user_id'];
              $patient_id = $row['patient_id'];
              $name = $row['user_name'];
             $phone =$row['user_mobile'];
             $email = $row['user_email'];
             $gender = $row['user_gender'];
             $branch_id = $row['branch_id'];
              $vendor_id=$row['vendor_id'];
            $booking_time = $hospital_details->row()->booking_time;
                    $booking_date = $row['booking_date'];
            $status = $row['status'];
            $joining_date = $row['joining_date'];
            $category_id = $row['category_id'];
            $booking_address = $row['booking_address'];
            $booking_mobile = $row['booking_mobile'];
            if($category_id=="")
            {
                $category_id="";
            }
            
            $resultpost_hopsital[] = array(
                    'id' => $id,
                    'booking_id' => $booking_id,
                    'package_id'=> $package_id,
                    'listing_id' => $listing_id,
                    'user_id' => $user_id,
                    'patient_id' => $patient_id,
                    'package_name'=>$package_name,
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'gender' => $gender,
                    'branch_id' =>$branch_id,
                    'vendor_id' =>$vendor_id,
                     'booking_time' => $booking_time,
                        'booking_date' => $booking_date,
                    'status' => $status,
                    'joining_date' => $patient_preferred_date,
                    'category_id' => $category_id,
                    'booking_address' => $booking_address,
                    'booking_mobile' => $booking_mobile,
                    'vendor_name' => $vendor_name,
                    'ward_id' => $ward_id,
                    'amount' => $amount,
                    'room_type'=> $room_type,
                    'capacity' => $capacity,
                    'price' => $price,
                    'booking_type'=>"IPD"
                );
            
           }
           
           $Hospitals_array[] = array(
                            'title' => 'Hospital',
                            'array' => $resultpost_hopsital
                        );
        }
        
         $query= $this->db->query("select hospital_booking_master.booking_id,hospital_booking_master.booking_date,hospital_booking_master.listing_id,hospital_booking_master.user_id,hospital_booking_master.listing_id,
hospital_booking_master.doctor_id,hospital_doctor_prescription.id as prescription_id,hospital_booking_master.booking_time,hospital_booking_master.status,hospital_booking_master.consultation_type,
hospitals.name_of_hospital,users.name as patient_name, hospitals.address,hospitals.image,hospitals.state,hospitals.city,hospitals.pincode, dl.consultation,hospitals.phone,dl.doctor_name from hospital_booking_master  LEFT JOIN hospital_doctor_prescription 
ON (hospital_booking_master.patient_id = hospital_doctor_prescription.patient_id AND hospital_booking_master.doctor_id = hospital_doctor_prescription.doctor_id AND hospital_booking_master.listing_id = hospital_doctor_prescription.hospital_id) LEFT JOIN hospitals on (hospital_booking_master.listing_id = hospitals.user_id)
LEFT JOIN users ON (hospital_booking_master.user_id = users.id)  LEFT JOIN hospital_doctor_list as dl ON(dl.id=hospital_booking_master.doctor_id)
where hospital_booking_master.user_id = '$user_id' and (hospitals.name_of_hospital like '%$parts[$i]%' or dl.doctor_name like '%$parts[$i]%'or hospital_booking_master.booking_id like '%$parts[$i]%')ORDER BY hospital_booking_master.id DESC");

           $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $booking_id = $row['booking_id'];
                    $booking_date = $row['booking_date'];
                    $booking_time = $row['booking_time'];
                    $patient_name = $row['patient_name'];
                    $doctor_name = $row['doctor_name'];
                    $consultation_charges = $row['consultation'];
                    $branch_name = $row['name_of_hospital'];
                    $consultation_type = $row['consultation_type'];
                    
                    $address = $row['address'] . "," . $row['city'] . "," . $row['state'] . "," . $row['pincode'];
                    $image = $row['image'];
                    $clinic_contact_no = $row['phone'];
                    $prescription_id = $row['prescription_id'];
                    $status = $row['status'];
                    $doctor_id = $row['listing_id'];
                    
                      if($status == null)
                    {
                        $status = "";
                    }
                       if($branch_name == null)
                    {
                        $branch_name = "";
                    }
                       if($image == null)
                    {
                        $image = "";
                    }
                       if($prescription_id == null)
                    {
                        $prescription_id = "";
                    }
                       if($clinic_contact_no == null)
                    {
                        $clinic_contact_no = "";
                    }
                    $resultpost_hopsital[] = array(
                        'booking_id' => $booking_id,
                        'doctor_id' => $doctor_id,
                        'appointment_date' => $booking_date,
                        'appointment_time' => $booking_time,
                        'patient_name' => $patient_name,
                        'doctor_name' => $doctor_name,
                        'consultation_charge' => $consultation_charges,
                        'consultation_type' => $consultation_type,
                        'clinic_name' => $branch_name,
                        'clinic_address' => $address,
                        'image' => $image,
                        'clinic_contact_no' => $clinic_contact_no,
                        'status' => $status,
                        'prescription_id' => $prescription_id,
                        'booking_type'=>"OPD"
                    );
                }
                  $Hospitals_array[] = array(
                            'title' => 'Hospital',
                            'array' => $resultpost_hopsital
                        );
            }
        
        
        
    
         if($booking_count==0 and $count==0)
         {
             $Hospitals_array[] = array(
                            'title' => 'Hospital',
                            'array' => array()
                        );
         }
                }
            }
            else
            {
              $Hospitals_array =array();
                
       // IPD
       $booking_details = $this->db->query("SELECT bm.*,bm.id as ids, hp.package_name FROM booking_master as bm LEFT JOIN hospital_packages as hp ON (bm.package_id=hp.id) WHERE bm.user_id='$user_id' and bm.vendor_id='8' and (hp.package_name like '%$keywords%' or bm.booking_id like '%$keywords%') order by bm.id DESC");
         
        $booking_count = $booking_details->num_rows();
        if($booking_count>0)
        {
           foreach($booking_details ->result_array() as $row )
           {
               $id = $row['ids'];
               $booking_id = $row['booking_id'];
               $package_id = $row['package_id'];
              $listing_id = $row['listing_id'];
              $package_name= $row['package_name'];
              $vendor_detaild = $this->db->query("SELECT name,phone FROM users WHERE id='$listing_id' ");
              $vendor_count = $vendor_detaild->num_rows();
              $vendor_name ='';
              if($vendor_count>0)
              {
             $vendor_name = $vendor_detaild->row()->name;
              }
              
              $hospital_details = $this->db->query("SELECT * FROM hospital_booking_details WHERE booking_id='$booking_id' and vendor_type='8' order by id DESC");
              $hospital_count = $hospital_details->num_rows();
              $hospital_name ='';
              if($hospital_count>0)
              {
             $ward_id = $hospital_details->row()->ward_id;
             $amount = $hospital_details->row()->amount;
             $patient_preferred_date = $hospital_details->row()->patient_preferred_date;
              }
              
              $ward_details = $this->db->query("SELECT * FROM hospital_wards WHERE hospital_id='$listing_id'");
              $ward_count = $ward_details->num_rows();
              $room_type ='';
                $capacity ='';
                  $price ='';
              if($ward_count>0)
              {
             $room_type = $ward_details->row()->room_type;
             $capacity = $ward_details->row()->capacity;
             $price =  $ward_details->row()->price;
              }
              
              /*$pack_details = $this->db->query("SELECT * FROM hospital_packages WHERE id='$package_id'");
              $pack_count = $pack_details->num_rows();
            
              if($pack_count>0)
              {
             $package_name = $pack_details->row()->package_name;
              }
              */
              
              
              $user_id = $row['user_id'];
              $patient_id = $row['patient_id'];
              $name = $row['user_name'];
             $phone =$row['user_mobile'];
             $email = $row['user_email'];
             $gender = $row['user_gender'];
             $branch_id = $row['branch_id'];
              $vendor_id=$row['vendor_id'];
             $booking_date = $row['booking_date'];
            $status = $row['status'];
            $joining_date = $row['joining_date'];
            $category_id = $row['category_id'];
            $booking_address = $row['booking_address'];
            $booking_mobile = $row['booking_mobile'];
            if($category_id=="")
            {
                $category_id="";
            }
            
            $resultpost_hopsital[] = array(
                    'id' => $id,
                    'booking_id' => $booking_id,
                    'package_id'=> $package_id,
                    'listing_id' => $listing_id,
                    'user_id' => $user_id,
                    'patient_id' => $patient_id,
                    'package_name'=>$package_name,
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'gender' => $gender,
                    'branch_id' =>$branch_id,
                    'vendor_id' =>$vendor_id,
                    'booking_date'=>$booking_date,
                    'status' => $status,
                    'joining_date' => $patient_preferred_date,
                    'category_id' => $category_id,
                    'booking_address' => $booking_address,
                    'booking_mobile' => $booking_mobile,
                    'vendor_name' => $vendor_name,
                    'ward_id' => $ward_id,
                    'amount' => $amount,
                    'room_type'=> $room_type,
                    'capacity' => $capacity,
                    'price' => $price,
                    'booking_type'=>"IPD"
                );
            
           }
           
           $Hospitals_array[] = array(
                            'title' => 'Hospital',
                            'array' => $resultpost_hopsital
                        );
        }
        
         $query= $this->db->query("select hospital_booking_master.booking_id,hospital_booking_master.booking_date,hospital_booking_master.listing_id,hospital_booking_master.user_id,hospital_booking_master.listing_id,
hospital_booking_master.doctor_id,hospital_doctor_prescription.id as prescription_id,hospital_booking_master.booking_time,hospital_booking_master.status,hospital_booking_master.consultation_type,
hospitals.name_of_hospital,users.name as patient_name, hospitals.address,hospitals.image,hospitals.state,hospitals.city,hospitals.pincode, dl.consultation,hospitals.phone,dl.doctor_name from hospital_booking_master  LEFT JOIN hospital_doctor_prescription 
ON (hospital_booking_master.patient_id = hospital_doctor_prescription.patient_id AND hospital_booking_master.doctor_id = hospital_doctor_prescription.doctor_id AND hospital_booking_master.listing_id = hospital_doctor_prescription.hospital_id) LEFT JOIN hospitals on (hospital_booking_master.listing_id = hospitals.user_id)
LEFT JOIN users ON (hospital_booking_master.user_id = users.id)  LEFT JOIN hospital_doctor_list as dl ON(dl.id=hospital_booking_master.doctor_id)
where hospital_booking_master.user_id = '$user_id' and (hospitals.name_of_hospital like '%$keywords%' or dl.doctor_name like '%$keywords%'or hospital_booking_master.booking_id like '%$keywords%') ORDER BY hospital_booking_master.id DESC");

           $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $booking_id = $row['booking_id'];
                    $booking_date = $row['booking_date'];
                    $booking_time = $row['booking_time'];
                    $patient_name = $row['patient_name'];
                    $doctor_name = $row['doctor_name'];
                    $consultation_charges = $row['consultation'];
                    $branch_name = $row['name_of_hospital'];
                    $consultation_type = $row['consultation_type'];
                    
                    $address = $row['address'] . "," . $row['city'] . "," . $row['state'] . "," . $row['pincode'];
                    $image = $row['image'];
                    $clinic_contact_no = $row['phone'];
                    $prescription_id = $row['prescription_id'];
                    $status = $row['status'];
                    $doctor_id = $row['listing_id'];
                    
                      if($status == null)
                    {
                        $status = "";
                    }
                       if($branch_name == null)
                    {
                        $branch_name = "";
                    }
                       if($image == null)
                    {
                        $image = "";
                    }
                       if($prescription_id == null)
                    {
                        $prescription_id = "";
                    }
                       if($clinic_contact_no == null)
                    {
                        $clinic_contact_no = "";
                    }
                    $resultpost_hopsital[] = array(
                        'booking_id' => $booking_id,
                        'doctor_id' => $doctor_id,
                        'appointment_date' => $booking_date,
                        'appointment_time' => $booking_time,
                        'patient_name' => $patient_name,
                        'doctor_name' => $doctor_name,
                        'consultation_charge' => $consultation_charges,
                        'consultation_type' => $consultation_type,
                        'clinic_name' => $branch_name,
                        'clinic_address' => $address,
                        'image' => $image,
                        'clinic_contact_no' => $clinic_contact_no,
                        'status' => $status,
                        'prescription_id' => $prescription_id,
                        'booking_type'=>"OPD"
                    );
                }
                  $Hospitals_array[] = array(
                            'title' => 'Hospital',
                            'array' => $resultpost_hopsital
                        );
            }
        
        
        
    
         if($booking_count==0 and $count==0)
         {
             $Hospitals_array[] = array(
                            'title' => 'Hospital',
                            'array' => array()
                        );
         }
           
            }
            // Hospitals End Here 
            // Health Mall Start Here
            
            
            
           
	    
	      $field1 = '';
            $field2 = '';
            $field3 = '';
            if(count($parts) > 1)
            { 
                for($i=0;$i<count($parts);$i++)
                {     
                 
                 $Healthmall_array =array();
                  $query3 = $this->db->query("SELECT * FROM `user_order` WHERE `user_id` = '$user_id' AND  `listing_type`='34'  and (user_order.order_id like '%$parts[$i]%' or user_order.invoice_no like '%$parts[$i]%' or user_order.listing_name like '%$parts[$i]%') ORDER BY order_id DESC");

            $count3 = $query3->num_rows();
            if ($count3 > 0) {
              
               foreach($query3->result_array() as $order){
	        $products = $this->db->query("SELECT * FROM `orders_details_hm` WHERE `order_id` = ".$order['order_id']." AND `user_id` = '$user_id'");
	        $details = $products->result_array();
	       // print_r($products->result_array());die;
	        $pro_list =array();
	        $i = 0;
	       foreach($products->result_array() as $pro){
	            $product_qty = $pro['product_qty'];
                $price = $pro['price'];  
	            $res = $this->db->query("SELECT brand_name,pd_name,pd_id, pd_photo_1,pd_mrp_price,pd_vendor_price FROM product_details_hm WHERE pd_id = '".$pro['product_id']."'")->result_array();
	            $res[0]['product_qty'] = $product_qty;
	            $res[0]['price'] = $price;
	            
	            if($pro['variable_pd_id'] > 0){
	                $variable_pd_id = $pro['variable_pd_id'];
	                $variable_product = $this->db->query("SELECT * FROM `variable_products_hm` WHERE `id` = $variable_pd_id ")->row_array(); 
	                
	                $colorId = $variable_product['color'];
	                $sizeId = $variable_product['size'];
	                $color = $this->get_color_by_id($colorId);
                    $size = $this->get_size_by_id($sizeId);
	                
	                $c = $color[0]['color'];
                    $s = $size[0]['size_name'];
	                $prodName = $res[0]['pd_name'];
	                if($s != null && $s != ''){
	                    $prodName = $prodName." - $s"    ;
	                }
                    if($c != null && $c != ''){
	                    $prodName = $prodName." - $c"    ;
	                }	                
	                $res[0]['pd_name'] = $prodName;
	                
	              // print_r($variable_product); die();
	                
	                if(!empty($variable_product['image'])){
	                    $res[0]['pd_photo_1'] = $variable_product['image'];
	                    $res[0]['pd_mrp_price'] = $variable_product['price'];
	                    $res[0]['pd_vendor_price'] = $variable_product['vendor_price'];
	                }
	               // $res[0]['pd_photo_1'] = $variable_product['image'];
	                
	                $res[0]['variable_product'] = $variable_product;
	                //print_r($variable_product); die();   
	                
	            } else {
	                $variable_product = (object)[];
	                $res[0]['variable_product']=$variable_product;
	            } 
	           
	            array_push($pro_list,$res[0]);
	           
	            $i++;
	        }
	        
	        $order += ['products'=>$pro_list];
	            foreach($order as $key => $value){
        	        if($value == null){
        	            $order[$key] = ""   ;
        	        }
        	    }
        	    
	        $resultpost7[] = $order;
	    }
              
              
                $Healthmall_array[] = array(
                            'title' => 'HealthMall',
                            'array' => $resultpost7
                        );
            } else {
                 $Healthmall_array[] = array(
                            'title' => 'HealthMall',
                            'array' => array()
                        );
            }
           
              }
            }
            else
            {    
                 
                 $Healthmall_array =array();
                  $query3 = $this->db->query("SELECT * FROM `user_order` WHERE `user_id` = '$user_id' AND  `listing_type`='34'  and (user_order.order_id like '%$keyword%' or user_order.invoice_no like '%$keyword%' or listing_name like '%$keyword%') ORDER BY order_id DESC");

            $count3 = $query3->num_rows();
            if ($count3 > 0) {
              
               foreach($query3->result_array() as $order){
	        $products = $this->db->query("SELECT * FROM `orders_details_hm` WHERE `order_id` = ".$order['order_id']." AND `user_id` = '$user_id'");
	        $details = $products->result_array();
	       // print_r($products->result_array());die;
	        $pro_list =array();
	        $i = 0;
	       foreach($products->result_array() as $pro){
	            $product_qty = $pro['product_qty'];
                $price = $pro['price'];  
	            $res = $this->db->query("SELECT brand_name,pd_name,pd_id, pd_photo_1,pd_mrp_price,pd_vendor_price FROM product_details_hm WHERE pd_id = '".$pro['product_id']."'")->result_array();
	            $res[0]['product_qty'] = $product_qty;
	            $res[0]['price'] = $price;
	            
	            if($pro['variable_pd_id'] > 0){
	                $variable_pd_id = $pro['variable_pd_id'];
	                $variable_product = $this->db->query("SELECT * FROM `variable_products_hm` WHERE `id` = $variable_pd_id ")->row_array(); 
	                
	                $colorId = $variable_product['color'];
	                $sizeId = $variable_product['size'];
	                $color = $this->get_color_by_id($colorId);
                    $size = $this->get_size_by_id($sizeId);
	                
	                $c = $color[0]['color'];
                    $s = $size[0]['size_name'];
	                $prodName = $res[0]['pd_name'];
	                if($s != null && $s != ''){
	                    $prodName = $prodName." - $s"    ;
	                }
                    if($c != null && $c != ''){
	                    $prodName = $prodName." - $c"    ;
	                }	                
	                $res[0]['pd_name'] = $prodName;
	                
	              // print_r($variable_product); die();
	                
	                if(!empty($variable_product['image'])){
	                    $res[0]['pd_photo_1'] = $variable_product['image'];
	                    $res[0]['pd_mrp_price'] = $variable_product['price'];
	                    $res[0]['pd_vendor_price'] = $variable_product['vendor_price'];
	                }
	               // $res[0]['pd_photo_1'] = $variable_product['image'];
	                
	                $res[0]['variable_product'] = $variable_product;
	                //print_r($variable_product); die();   
	                
	            } else {
	                $variable_product = (object)[];
	                $res[0]['variable_product']=$variable_product;
	            } 
	           
	            array_push($pro_list,$res[0]);
	           
	            $i++;
	        }
	        
	        $order += ['products'=>$pro_list];
	            foreach($order as $key => $value){
        	        if($value == null){
        	            $order[$key] = ""   ;
        	        }
        	    }
        	    
	        $resultpost7[] = $order;
	    }
              
              
                $Healthmall_array[] = array(
                            'title' => 'HealthMall',
                            'array' => $resultpost7
                        );
            } else {
                 $Healthmall_array[] = array(
                            'title' => 'HealthMall',
                            'array' => array()
                        );
            }
           
              }
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
            // Health Mall End Here
            
           $resultpost = array_merge($Pharmacy_array,$Labs_array,$doctor_array,$Healthmall_array,$Hospitals_array,$Fitness_array,$Nursing_array); 
            
        }
        else
        {
            $resultpost = array();
        }
        return $resultpost;
    }
  
    
    
    
 
    
    
   
  
    
    
  
  

    public function user_read_slot($clinic_id, $doctor_id, $consultation_type)
    {
        $todayDay  = date('l');
        $todayDate = date('Y-m-d H:i:s');
        for ($i = 0; $i < 7; $i++) {
            $time_array           = array();
            $time_array1          = array();
            $time_array2          = array();
            $time_array3          = array();
            $time_slott           = array();
            $todayDate            = date('Y-m-d', strtotime($todayDate . ' +1 day'));
            $todayDay             = date('l', strtotime($todayDate));
            
            // doctor_slot_details
            
            if($clinic_id == '0'){
                $day_time_slots       = $this->db->query("SELECT * FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
                //  echo "SELECT * FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'";
            }else{
                $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
                //  echo "SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'";
            }
            $count_day_time_slots = $day_time_slots->num_rows();
            foreach ($day_time_slots->result_array() as $row) {
                if($clinic_id == '0'){
                      $timeSlot              = $row['timeSlot'];
                } else {
                      $timeSlot              = $row['timeSlot'];
                }
                
              
                $from_time             = $row['from_time'];
                $to_time               = $row['to_time'];
                // $status                = $row['status'];
                $day_time_status       = $this->db->query("SELECT `id`,`clinic_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `doctor_booking_master` WHERE `listing_id` = '$doctor_id' AND  `clinic_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
                $count_day_time_status = $day_time_status->num_rows();
                if ($count_day_time_status) {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    }
                } else {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    }
                }
            }
            $time_slott[] = array(
                'Morning' => $time_array,
                'time_slot' => 'Morning'
            );
            $time_slott[] = array(
                'Afternoon' => $time_array1,
                'time_slot' => 'Afternoon'
            );
            $time_slott[] = array(
                'Evening' => $time_array3,
                'time_slot' => 'Evening'
            );
            $time_slott[] = array(
                'Night' => $time_array2,
                'time_slot' => 'Night'
            );
            $time_slots[] = array(
                'day' => $todayDay,
                'date' => $todayDate,
                'timings' => $time_slott
            );
        }
        return $time_slots;
    }
    public function insert_doctor_users_feedback($doctor_id, $user_id, $type, $feedback, $ratings, $recommend,$booking_id,$booking_type)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date           = date('Y-m-d H:i:s');
        $feedback_array = array(
            'type' => $type,
            'user_id' => $user_id,
            'doctor_id' => $doctor_id,
            'feedback' => $feedback,
            'created_at' => $date,
            'ratings' => $ratings,
            'recommend' => $recommend
        );
        $this->db->insert('doctor_user_feedback', $feedback_array);
        
        if($booking_type == 'inperson')
           {
            //$query = $this->db->query("UPDATE doctor_booking_master SET status='7' WHERE booking_id =$booking_id  AND user_id='$patient_id'");
           }
           else
           {
            $query = $this->db->query("UPDATE doctor_booking_master SET status='7' WHERE booking_id =$booking_id");

           }
        
           //added by jakir on 17-july-2018 for notification on add prescription 
                $user_plike = $this->db->query("SELECT name FROM users WHERE id='$doctor_id'");
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $doctor_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $doctor_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $user_details = $this->db->query("SELECT name FROM users WHERE id='$user_id'"); 
                 $getdetails = $user_details ->row_array();
                 $user_name = $getdetails['name'];
                 
                $getusr = $user_plike->row_array();
                $usr_name = $getusr['name'];
                $msg = $usr_name . ', You got the feedback from' . $user_name;
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$doctor_id'");
                $title = $usr_name . ', YouYou got the feedback from' . $user_name;
                $customer_token_count = $customer_token->num_rows();

                if ($customer_token_count > 0) {
                    $token_status = $customer_token->row_array();
                    $agent = $token_status['agent'];
                    $reg_id = $token_status['token'];
                    $img_url = $userimage;
                    $tag = 'text';
                    $key_count = '1';
                    $this->send_gcm_notify_feedback($title, $reg_id, $msg, $img_url,$tag,$agent);
                }
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
    
    public function send_gcm_notify_feedback($title, $reg_id, $msg, $img_url,$tag,$agent) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        if (!defined("GOOGLE_GCM_URL"))
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        $fields = array(
            'to' => $reg_id,
            'priority' => "high",
            $agent === 'android' ? 'data' : 'notification' => array(
                "title" => $title,
                "message" => $msg,
                "notification_image" => $img_url,
                "tag" => $tag,
                'sound' => 'default',
                "notification_type" => 'user_feedback',
                "notification_date" => $date,
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch
        , CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
    }
    
    //view appointment list by jakir 
    public function view_appointments_module($user_id)
    {
        $Appointment_dataList   = $this->db->query("SELECT * FROM `doctor_booking_master` where `user_id`= '$user_id' ");
        $count_appointment_slot = $Appointment_dataList->num_rows();
        if ($count_appointment_slot > 0) {
            foreach ($Appointment_dataList->result_array() as $row) {
                $booking_id        = $row['booking_id'];
                $user_id           = $row['user_id'];
                $listing_id        = $row['listing_id'];
                $clinic_id         = $row['patient_id'];
                $booking_date      = $row['booking_date'];
                $consultation_type = $row['consultation_type'];
                $from_time         = $row['from_time'];
                $to_time           = $row['to_time'];
                $description       = $row['description'];
                $status            = $row['status'];
                $query12           = $this->db->query("SELECT `name` FROM `users` where `id`= '$listing_id' ");
                $doctor            = $query12->row_array();
                $doctor_name       = $doctor['name'];
                $query12           = $this->db->query("SELECT `clinic_name` FROM `doctor_clinic` where `doctor_id`= '$listing_id' AND `id` = '$clinic_id'");
                $clinic            = $query12->row_array();
                $clinic_name       = $clinic['clinic_name'];
                $resultpost[]      = array(
                    'booking_id' => $booking_id,
                    'user_id' => $user_id,
                    'doctor_name' => $doctor_name,
                    'clinic_name' => $clinic_name,
                    'booking_date' => $booking_date,
                    'consultation_type' => $consultation_type,
                    'from_time' => $from_time,
                    'to_time' => $to_time,
                    'description' => $description,
                    'status' => $status
                );
            }
            return $resultpost;
        } else
            return array();
    }
    public function get_doctor_name($doctor_id)
    {
        // doctor_list
        $doctorNameRow = $this->db->query("SELECT * FROM `doctor_list` where `id`= '$doctor_id' ");
        foreach ($doctorNameRow->result_array() as $row) {
            $doctorName = $row['doctor_name'];
        }
        return $doctorName;
    }
    // doctor_prescription
    public function get_doctor_prescription($patient_id)
    {
        // $doctorprescriptionRows = $this->db->query("SELECT * FROM `doctor_prescription` where `doctor_id`= '$doctor_id' AND `patient_id`= '$patient_id' ");
        $doctorprescriptionRows = $this->db->query("SELECT * FROM `doctor_prescription` where `patient_id`= '$patient_id' ");
        return $doctorprescriptionRows;
    }
    // get_clinic_name
    public function get_clinic_name($clinic_id)
    {
        // doctor_list
        $clinicNameRow = $this->db->query("SELECT * FROM `doctor_clinic` where `id`= '$clinic_id' ");
        foreach ($clinicNameRow->result_array() as $row) {
            $clinicName = $row['clinic_name'];
        }
        return $clinicName;
    }
    // get_doctor_prescription_medicine
    public function get_doctor_prescription_medicine($prescription_id)
    {
        // doctor_list
        $prescriptionIdRow = $this->db->query("SELECT * FROM `doctor_prescription_medicine` where `prescription_id`= '$prescription_id' ");
        foreach ($prescriptionIdRow->result_array() as $rowId) {
            $medicineDetails['medicine_name']    = $rowId['medicine_name'];
            $medicineDetails['dosage_unit']      = $rowId['dosage_unit'];
            $medicineDetails['frequency_first']  = $rowId['frequency_first'];
            $medicineDetails['frequency_second'] = $rowId['frequency_second'];
            $medicineDetails['frequency_third']  = $rowId['frequency_third'];
            $medicineDetails['instruction']      = $rowId['instruction'];
            // $medicineDetails['prescription_id'] = $rowId['id'];
            $allMedicine[]                       = $medicineDetails;
        }
        return $allMedicine;
    }
    // get_doctor_prescription_test
    public function get_doctor_prescription_test($prescription_id)
    {
        // doctor_list
        $prescriptionTestRow = $this->db->query("SELECT * FROM `doctor_prescription_test` where `prescription_id`= '$prescription_id' ");
        foreach ($prescriptionTestRow->result_array() as $rowRow) {
            $testDetailsCategory = $rowRow['category'];
            $testCatRow          = $this->db->query("SELECT * FROM `doctor_test` where `id`= '$testDetailsCategory' ");
            foreach ($testCatRow->result_array() as $cat) {
                $testCategory = $cat['test_name'];
            }
            $test['category'] = $testCategory;
            $test['test']     = $rowRow['test'];
            $testDetails[]    = $test;
        }
        return $testDetails;
    }
    public function booking_details($booking_id)
    {
        //echo "SELECT `doctor_booking_master`.*,doctor_list.*,doctor_clinic.lat,doctor_clinic.lng,doctor_clinic.consultation_charges,doctor_clinic.map_location,doctor_clinic.address,vendor_discount.* FROM `doctor_booking_master` LEFT JOIN doctor_clinic ON(doctor_booking_master.clinic_id = doctor_clinic.id) LEFT JOIN doctor_list ON(doctor_booking_master.listing_id = doctor_list.user_id) LEFT JOIN vendor_discount ON(doctor_booking_master.listing_id = vendor_discount.vendor_id AND vendor_discount.discount_category = 'doctor_visit') WHERE doctor_booking_master.`booking_id` = '$booking_id' AND  ";
        $booking_details           = $this->db->query("SELECT `doctor_booking_master`.*,doctor_list.*,doctor_clinic.lat,doctor_clinic.lng,doctor_clinic.consultation_charges,doctor_clinic.map_location,doctor_clinic.address,vendor_discount.* FROM `doctor_booking_master` LEFT JOIN doctor_clinic ON(doctor_booking_master.clinic_id = doctor_clinic.id) LEFT JOIN doctor_list ON(doctor_booking_master.listing_id = doctor_list.user_id) LEFT JOIN vendor_discount ON(doctor_booking_master.listing_id = vendor_discount.vendor_id AND vendor_discount.discount_category = 'doctor_visit') WHERE doctor_booking_master.`booking_id` = '$booking_id'");
        $booking_details           = $booking_details->row_array();
        $doctor_id                 = $booking_details['listing_id'];
        $clinic_id                 = $booking_details['clinic_id'];
        $booking_id                = $booking_details['booking_id'];
        $booking_date              = $booking_details['booking_date'];
        $booking_time              = $booking_details['booking_time'];
        $created_date              = $booking_details['created_date'];
        $consultation_type         = $booking_details['consultation_type'];
        $status                    = $booking_details['status'];
        $doctor_name               = $booking_details['doctor_name'];
        $doctor_email              = $booking_details['email'];
        $doctor_experience         = $booking_details['experience'];
        $speciality                = $booking_details['speciality'];
        $doctor_dob                = $booking_details['dob'];
        $doctor_telephone          = $booking_details['telephone'];
        $doctor_lat                = $booking_details['lat'];
        $doctor_lng                = $booking_details['lng'];
        $doctor_address            = $booking_details['map_location'];
        $doctor_consultation_visit = $booking_details['consultation_charges'];
        $discount_amount           = $booking_details['discount_min'];
        $discount_type             = $booking_details['discount_type'];
        $discount_limit            = $booking_details['discount_limit'];
        $discount_category         = $booking_details['discount_category'];
        $visit_charge              = "";
        if ($discount_category == "doctor_visit") {
            if ($discount_type == "percent") {
                $visit_discount_amount = $doctor_consultation_visit * ($discount_amount / 100);
                if ($visit_discount_amount > $discount_limit) {
                    $visit_discount_amount = $discount_limit;
                } else {
                    $visit_discount_amount = $visit_discount_amount;
                }
                $visit_charge = $doctor_consultation_visit - $visit_discount_amount;
            } else if ($discount_type == "rupees") {
                $visit_discount_amount = $doctor_consultation_visit - $discount_amount;
                if ($visit_discount_amount > $discount_limit) {
                    $visit_discount_amount = $discount_limit;
                } else {
                    $visit_discount_amount = $visit_discount_amount;
                }
                $visit_charge = $doctor_consultation_visit - $visit_discount_amount;
            }
            if ($visit_charge < 0) {
                $visit_charge = 0;
            }
        }
        
        //doctor consultaion
        if($consultation_type == 'visit')
        {
            
        }
        else
        {
        $clinic_id = 0;
        }
        $results = array();
        $available_call = "0";
        $available_video = "0";
        $available_chat = "0";
        $results_call = array();
        $results_video = array();
        $results_chat = array();
        $q = $this->db->query("SELECT * FROM `doctor_consultation` WHERE `doctor_user_id` = '$doctor_id' AND consultation_name<>'visit'");
        
        $qRows = $q->result_array();
        
        foreach($qRows as $qRow){
            if($qRow['consultation_name'] == 'chat' && $qRow['is_active'] == 1){
                $available_call = $qRow['is_active'];
                $duration = $qRow['duration'];
                $charges = $qRow['charges'];
                
                $results_call['consultation_type'] = 'call';
                $results_call['info'] = array(
                        'duration' => $duration,
                        'charges' => $charges
                    );    
            }
            
            if($qRow['consultation_name'] == 'video' && $qRow['is_active'] == 1){
                $available_video = $qRow['is_active'];
                $duration = $qRow['duration'];
                $charges = $qRow['charges'];
                
                $results_video['consultation_type'] = 'video';
                $results_video['info'] = array(
                        'duration' => $duration,
                        'charges' => $charges
                    );    
            }
            
             if($qRow['consultation_name'] == 'chat' && $qRow['is_active'] == 1){
                $available_chat = $qRow['is_active'];
                $duration = $qRow['duration'];
                $charges = $qRow['charges'];
                
                $results_chat['consultation_type'] = 'chat';
                $results_chat['info'] = array(
                        'duration' => $duration,
                        'charges' => $charges
                    );    
            }
            
        }
      
      if($results_call){$results[] = $results_call;}
      if($results_video){$results[] = $results_video;}
      if($results_chat){$results[] = $results_chat;}
        
        
        // doctor_consultation
        if($clinic_id == "0"){
          
            $charges = $this->db->query("SELECT * FROM `doctor_consultation`  WHERE `doctor_user_id` = '$doctor_id' AND `consultation_name` = '$consultation_type'");
            // [charges]
            foreach($charges->result_array() as $charge ){
                $doctor_consultation_visit = $charge['charges'];
            }
              
        }
        $doctor_consultation_video      = $booking_details['consultaion_video'];
        $doctor_consultation_voice_call = $booking_details['consultation_voice_call'];
        $doctor_image                   = $booking_details['image'];
        if ($doctor_image != '') {
            $doctor_image = str_replace(' ', '%20', $doctor_image);
            $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
        } else {
            $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
        }
        $doctor_ratings       = $booking_details['rating'];
        $doctor_qualification = $booking_details['qualification'];
        $doctor_category      = $booking_details['category'];
        $area_expertise       = array();
        $query_sp             = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'" . $doctor_category . "')");
        $total_category       = $query_sp->num_rows();
        if ($total_category > 0) {
            foreach ($query_sp->result_array() as $get_sp) {
                $id               = $get_sp['id'];
                $area_expertised  = $get_sp['area_expertise'];
                $area_expertise[] = array(
                    'id' => $id,
                    'area_expertise' => $area_expertised
                );
            }
        } else {
            $area_expertise = array();
        }
        $degree_array  = array();
        $degree_       = explode(',', $doctor_qualification);
        $count_degree_ = count($degree_);
        if ($count_degree_ > 1) {
            foreach ($degree_ as $degree_) {
                $degree_array[] = array(
                    'degree' => $degree_
                );
            }
        } else {
            $degree_array = array();
        }
        $special_array = array();
        $speciality    = explode(",", $speciality);
        $specialitycnt = count($speciality);
        //$specialitycnt--;
        if ($specialitycnt > 0) {
            for ($j = 0; $j < $specialitycnt; $j++) {
                $special_array[] = array(
                    'specialization' => $speciality[$j]
                );
            }
        } else {
            $special_array = array();
        }
        // $special_ = explode(',', $speciality);
        // $count_special_ = count($special_);
        // if ($count_special_ > 1) {
        //     foreach ($special_ as $special_) {
        //         $special_array[] = array(
        //             'specialization' => $special_
        //         );
        //     }
        // } else {
        //     $special_array = array();
        // }
        $testDetails = array(
            'doctor_id' => $doctor_id,
            'clinic_id' => $clinic_id,
            'booking_id' => $booking_id,
            'booking_date' => $booking_date,
            'booking_time' => $booking_time,
            'created_date' => $created_date,
            'consultation_type' => $consultation_type,
            'doctor_name' => $doctor_name,
            'email' => $doctor_email,
            'experience' => $doctor_experience,
            'dob' => $doctor_dob,
            'telephone' => $doctor_telephone,
            'info' => $results,
            'lat' => $doctor_lat,
            'lng' => $doctor_lng,
            'doctor_address' => $doctor_address,
            'doctor_image' => $doctor_image,
            'rating' => $doctor_ratings,
            'degree' => $degree_array,
            'status' => $status,
            'doctor_specialization' => $special_array,
            'area_expertise' => $area_expertise,
            'consultation_charges' => $doctor_consultation_visit,
            'discount_amount_min' => $booking_details['discount_min'],
            'discount_amount_max' => $booking_details['discount_max'],
            'discount_type' => $booking_details['discount_type'],
            'discount_limit' => $booking_details['discount_limit'],
            'discount_category' => $booking_details['discount_category'],
            'payable_amount' => $visit_charge
        );
        return $testDetails;
    }
    // vendor_discount
    public function vendor_discount($vendor_id, $clinic_id)
    {
        $discount      = array();
        $call          = array();
        $text          = array();
        $video         = array();
        $clinic        = array();
        $today         = date('Y-m-d');
        $doctorCharges = $this->db->query("SELECT * FROM `doctor_list` WHERE id = '$vendor_id'");
        $count         = $doctorCharges->num_rows();
        if ($count > 0) {
            foreach ($doctorCharges->result_array() as $row) {
                $consultation_video     = $row['consultation_video'];
                $consultaion_chat       = $row['consultaion_chat'];
                $consultaion_voice_call = $row['consultaion_voice_call'];
            }
        }
        // $clinic_id
        if ($clinic_id > 0) {
            $doctorClinicCharges = $this->db->query("SELECT * FROM `doctor_clinic` WHERE id = '$clinic_id' AND doctor_id = '$vendor_id'");
            $doctorClinicCount   = $doctorClinicCharges->num_rows();
            if ($doctorClinicCount > 0) {
                foreach ($doctorClinicCharges->result_array() as $rowClinic) {
                    $consultation_visit = $rowClinic['consultation_charges'];
                }
            }
        }
        $vendorDiscount      = $this->db->query("SELECT * FROM `vendor_discount` WHERE vendor_id = '$vendor_id'");
        //  doctor_voice
        $vendorDiscountCount = $vendorDiscount->num_rows();
        if ($vendorDiscountCount > 0) {
            foreach ($vendorDiscount->result_array() as $row1) {
                // print_r($row1);
                $discountAmt   = $row1['discount_amount'];
                $discountLimit = $row1['discount_limit'];
                $discountExp   = $row1['discount_exp'];
                if (strtotime($today) <= strtotime($discountExp)) {
                    if ($row1['discount_category'] == "doctor_chat") {
                        if ($row1['discount_type'] == "percent") {
                            $chatDisCharge = $consultaion_chat * ($discountAmt / 100);
                            if ($chatDisCharge > $discountLimit) {
                                $chatDisCharge = $discountLimit;
                            } else {
                                $chatDisCharge = $chatDisCharge;
                            }
                            $chatCharge = $consultaion_chat - $chatDisCharge;
                        } else if ($row1['discount_type'] == "rupees") {
                            $chatDisCharge = $consultaion_chat - $discountAmt;
                            if ($chatDisCharge > $discountLimit) {
                                $chatDisCharge = $discountLimit;
                            } else {
                                $chatDisCharge = $chatDisCharge;
                            }
                            $chatCharge = $consultaion_chat - $chatDisCharge;
                        }
                        $details['amount']         = $consultaion_chat;
                        $details['payable_amount'] = $chatCharge;
                        $details['total_discount'] = $chatDisCharge;
                        $details['discount']       = $discountAmt;
                        $details['discount_type']  = $row1['discount_type'];
                        $details['discount_limit'] = $discountLimit;
                        $details['category']       = $row1['discount_category'];
                        $details['expiry']         = $discountExp;
                        $detailsAll[]              = $details;
                    }
                    if ($row1['discount_category'] == "doctor_call") {
                        if ($row1['discount_type'] == "percent") {
                            $voiceDisCharge = $consultaion_voice_call * ($discountAmt / 100);
                            if ($voiceDisCharge > $discountLimit) {
                                $voiceDisCharge = $discountLimit;
                            } else {
                                $voiceDisCharge = $voiceDisCharge;
                            }
                            $voiceCharge = $consultaion_voice_call - $voiceDisCharge;
                        } else if ($row1['discount_type'] == "rupees") {
                            $voiceDisCharge = $consultaion_voice_call - $discountAmt;
                            if ($voiceDisCharge > $discountLimit) {
                                $voiceDisCharge = $discountLimit;
                            } else {
                                $voiceDisCharge = $voiceDisCharge;
                            }
                            $voiceCharge = $consultaion_voice_call - $voiceDisCharge;
                        }
                        $details['amount']         = $consultaion_voice_call;
                        $details['payable_amount'] = $voiceCharge;
                        $details['total_discount'] = $voiceDisCharge;
                        $details['discount']       = $discountAmt;
                        $details['discount_type']  = $row1['discount_type'];
                        $details['discount_limit'] = $discountLimit;
                        $details['category']       = $row1['discount_category'];
                        $details['expiry']         = $discountExp;
                        $detailsAll[]              = $details;
                    }
                    if ($row1['discount_category'] == "doctor_video") {
                        if ($row1['discount_type'] == "percent") {
                            $videoDisCharge = $consultation_video * ($discountAmt / 100);
                            if ($videoDisCharge > $discountLimit) {
                                $videoDisCharge = $discountLimit;
                            } else {
                                $videoDisCharge = $videoDisCharge;
                            }
                            $videoCharge = $consultation_video - $videoDisCharge;
                        } else if ($row1['discount_type'] == "rupees") {
                            $videoDisCharge = $consultation_video - $discountAmt;
                            if ($videoDisCharge > $discountLimit) {
                                $videoDisCharge = $discountLimit;
                            } else {
                                $videoDisCharge = $videoDisCharge;
                            }
                            $videoCharge = $consultation_video - $videoDisCharge;
                        }
                        $details['amount']         = $consultation_video;
                        $details['payable_amount'] = $videoCharge;
                        $details['total_discount'] = $videoDisCharge;
                        $details['discount']       = $discountAmt;
                        $details['discount_type']  = $row1['discount_type'];
                        $details['discount_limit'] = $discountLimit;
                        $details['category']       = $row1['discount_category'];
                        $details['expiry']         = $discountExp;
                        $detailsAll[]              = $details;
                    }
                    if ($clinic_id > 0) {
                        if ($row1['discount_category'] == "doctor_visit") {
                            // $consultation_visit
                            /* $visitCharge = "";
                            $visitDisCharge = "";*/
                            if ($row1['discount_type'] == "percent") {
                                $visitDisCharge = $consultation_visit * ($discountAmt / 100);
                                if ($visitDisCharge > $discountLimit) {
                                    $visitDisCharge = $discountLimit;
                                } else {
                                    $visitDisCharge = $visitDisCharge;
                                }
                                $visitCharge = $consultation_visit - $visitDisCharge;
                            } else if ($row1['discount_type'] == "rupees") {
                                $visitDisCharge = $consultation_visit - $discountAmt;
                                if ($visitDisCharge > $discountLimit) {
                                    $visitDisCharge = $discountLimit;
                                } else {
                                    $visitDisCharge = $visitDisCharge;
                                }
                                $visitCharge = $consultation_visit - $visitDisCharge;
                            }
                            $details['amount']         = $consultation_visit;
                            $details['payable_amount'] = $visitCharge;
                            $details['total_discount'] = $visitDisCharge;
                            $details['discount']       = $discountAmt;
                            $details['discount_type']  = $row1['discount_type'];
                            $details['discount_limit'] = $discountLimit;
                            $details['category']       = $row1['discount_category'];
                            $details['expiry']         = $discountExp;
                            $detailsAll[]              = $details;
                        }
                    }
                } else {
                    $details['expiry'] = "discount expired";
                    $detailsAll[]      = $details;
                }
            }
        }
        //  die();
        $resp = array(
            "status" => 200,
            "data" => $detailsAll
        );
        return $resp;
    }
    /*
    User Approval Status
    1=Confirm 
    2=Reschedule
    2 task in this method
    1.Doctor Appointment Status Change
    2.Notification trigger
    
    
    1 = booked by user / awaiting confirmation from doctor
    2 = doctor confirm ( payment pending from user side)
    3 = doctor cancel (doctor cancel or user canceled)
    4 = rescheduled by doctor (awaiting confirmation from user)
    5 = user confirm (after payment done meeting is schedule for perticular date ) 
    6 = awaiting feedback 
    7 = completed (all process done completed all meetings)
    */
    public function user_payment_approval($doctor_id, $status, $booking_id, $user_id)
    {
        //echo $doctor_id . ',' . $status . ',' . $booking_id . ',' . $user_id;
        //die();
        date_default_timezone_set('Asia/Kolkata');
        $date         = date('Y-m-d H:i:s');
        //$status = strtolower($status);
        $status       = $status; // 2 - if doctor confirm timing ,4 doctor cancelled timing ,6 reschedule
        //echo "SELECT user_id,booking_id,listing_id FROM doctor_booking_master WHERE booking_id='$booking_id'";
        $table_record = $this->db->query("SELECT user_id,booking_id,listing_id FROM doctor_booking_master WHERE booking_id='$booking_id'");
        $count_user   = $table_record->num_rows();
        if ($count_user > 0) {
            $booking_array = array(
                'status' => $status,
                'created_date' => $date
            );
            $updateStatus  = $this->db->where('booking_id', $booking_id)->update('doctor_booking_master', $booking_array);
            if (!$updateStatus) {
                return array(
                    'status' => 204,
                    'message' => 'Update failed'
                );
            }
            if ($status == '5') //user confirm, payment pending
                {
                $row       = $table_record->row();
                $user_id   = $row->user_id;
                $doctor_id = $row->listing_id;
                $this->confirm_status($user_id, $booking_id, $doctor_id);
            }
            else if ($status == '8')
            {
                 $row       = $table_record->row();
                $user_id   = $row->user_id;
                $doctor_id = $row->listing_id;
                $this->confirm_cash_on_delivery_status($user_id, $booking_id, $doctor_id);
            }
            else if ($status == '3') //cancel appointment 
                {
                $row       = $table_record->row();
                $user_id   = $row->user_id;
                $doctor_id = $row->listing_id;
                $this->cancel_status($user_id, $booking_id, $doctor_id);
            }
        } else {
            return array(
                'status' => 208,
                'message' => 'Booking data not found'
            );
        }
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    /*
    Confirm Status is used to confirm the 
    status of the appointment.
    Doubt in query call doctor name can be called  from parent method using join
    which is better way.
    */
    public function confirm_status($user_id, $booking_id, $listing_id)
    {
        $appointment_status = '5';
        //$table_record       = $this->db->query("SELECT doctor_name FROM doctor_list WHERE user_id='$listing_id' ");
        $table_record       = $this->db->query("SELECT name as patient_name FROM users WHERE id='$user_id'");
        $count_user         = $table_record->num_rows();
        
          $bookingRecords =  $this->db->query("SELECT `booking_date`,`from_time`,`to_time`,`consultation_type` FROM `doctor_booking_master` WHERE `booking_id`='$booking_id'");
        $count_record  = $bookingRecords->num_rows();
        if($count_record > 0){
            $bookingRecord = $bookingRecords->row();
            
            $booking_date = $bookingRecord->booking_date;
            $from_time = $bookingRecord->from_time;
            $to_time = $bookingRecord->to_time;
            $booking_time = $from_time.'-'.$to_time;
            $consultation_type = $bookingRecord->consultation_type;
        }
        
        if ($count_user > 0) {
            $row         = $table_record->row();
            $patient_name = $row->patient_name;
            $title       = $patient_name . ' has Confirmed an Payment';
            $msg         = $patient_name . '  has Confirmed an Payment.';
           // $this->notifyDoctorMethod($listing_id, $appointment_status, $booking_id, $patient_name, $title, $msg);
            $this->notifyDoctorMethod($listing_id, $user_id,$title,$msg, $booking_date, $booking_time, $booking_id, $consultation_type, $patient_name);
            
        }
    }
    /*
    Cancel Status 
    User has canceled the appointment.
    */
    public function cancel_status($user_id, $booking_id, $listing_id)
    {
        $appointment_status = '3';
        $table_record       = $this->db->query("SELECT name as patient_name FROM users WHERE id='$user_id'");
        $count_user         = $table_record->num_rows();
        
          $bookingRecords =  $this->db->query("SELECT `booking_date`,`from_time`,`to_time`,`consultation_type` FROM `doctor_booking_master` WHERE `booking_id`='$booking_id'");
        $count_record  = $bookingRecords->num_rows();
        if($count_record > 0){
            $bookingRecord = $bookingRecords->row();
            
            $booking_date = $bookingRecord->booking_date;
            $from_time = $bookingRecord->from_time;
            $to_time = $bookingRecord->to_time;
            $booking_time = $from_time.'-'.$to_time;
            $consultation_type = $bookingRecord->consultation_type;
        }
        
        
        if ($count_user > 0) {
            $row         = $table_record->row();
            $patient_name = $row->patient_name;
            $title       = $patient_name . ' has Cancel an Payment';
            $msg         = $patient_name . '  has Cancel an Payment.';
            //$this->notifyDoctorMethod($listing_id, $appointment_status, $booking_id, $patient_name, $title, $msg);
             $this->notifyDoctorMethod($listing_id, $user_id,$title,$msg, $booking_date, $booking_time, $booking_id, $consultation_type, $patient_name);
        }
    }
    
   public function confirm_cash_on_delivery_status($user_id, $booking_id, $listing_id)
    {
        $appointment_status = '8';
        $table_record   = $this->db->query("SELECT name as patient_name FROM users WHERE id='$user_id'");
        
        $bookingRecords =  $this->db->query("SELECT `booking_date`,`from_time`,`to_time`,`consultation_type` FROM `doctor_booking_master` WHERE `booking_id`='$booking_id'");
        $count_record  = $bookingRecords->num_rows();
        if($count_record > 0){
            $bookingRecord = $bookingRecords->row();
            
            $booking_date = $bookingRecord->booking_date;
            $from_time = $bookingRecord->from_time;
            $to_time = $bookingRecord->to_time;
            $booking_time = $from_time.'-'.$to_time;
            $consultation_type = $bookingRecord->consultation_type;
        }
        
        $count_user         = $table_record->num_rows();
        if ($count_user > 0) {
            $row         = $table_record->row();
            $patient_name = $row->patient_name;
            $title       = $patient_name . ' has Confirmed an Payment on Cash on Delivery.' ;
             $msg         = $patient_name . '  has Confirmed an Payment on Cash on Delivery.';
           
            
            //$this->notifyDoctorMethod($listing_id, $appointment_status, $booking_id, $patient_name, $title, $msg);
            $this->notifyDoctorMethod($listing_id, $user_id,$title,$msg, $booking_date, $booking_time, $booking_id, $consultation_type, $patient_name);
        }
    }

    /*
    This method is used for notification.
    Left doctor service.
    */
    // public function notifyDoctorMethod($user_id, $appointment_status, $booking_id, $doctor_name,$title,$msg)
    // {

    //       $customer_token = $this->db->query("SELECT name,token,agent,token_status FROM users WHERE id='$user_id'");
    //       $customer_token_count = $customer_token->num_rows();
    //         if ($customer_token_count > 0) {
    //             $token_status = $customer_token->row_array();
    //             // $getusr = $user_plike->row_array();

    //             $usr_name = $token_status['name'];
    //             $agent = $token_status['agent'];
    //             $reg_id = $token_status['token'];
    //             $img_url = 'https://medicalwale.com/img/medical_logo.png';
    //             $tag = 'text';
    //             $key_count = '1';
    //             $title = $title;
    //             $msg = $msg;
    //             $this->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $booking_id);
    //         }
    // }


    //     //send notification through firebase
    //     /* notification to send in the doctor app for appointment confirmation*/
    // public function send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $booking_id) {
     
    //     date_default_timezone_set('Asia/Kolkata');
    //     $date = date('j M Y h:i A');
        
    //     if (!defined("GOOGLE_GCM_URL"))
    //         define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
    //     $fields = array(
    //         'to' => $reg_id,
    //         'priority' => "high",
    //         $agent === 'android' ? 'data' : 'notification' => array(
    //             "title" => $title,
    //             "message" => $msg,
    //             "notification_image" => $img_url,
    //             "tag" => $tag, 
    //             'sound' => 'default',
    //             "notification_type" => 'appointment_notifications',
    //             "notification_date" => $date,
    //             "booking_id" => $booking_id
    //          //   app date app time  app it 
    //         )
    //     );
       
    //     $headers = array(
    //             GOOGLE_GCM_URL,
    //             'Content-Type: application/json',
    //             $agent === 'android' ? 'Authorization: key=AAAAMCq6aPA:APA91bFjtuwLeq5RK6_DFrXntyJdXCFSPI0JdJcvoXXi0xIGm7qgzQJmUl7aEq3HjUTV3pvlFr5iv5pOWtCoN3JIpMSVhU8ZFzusaibehi5MPwThRmx1pCnm3Tm-x6wI8tGwhc0eUj2U' : 'Authorization: key=AIzaSyAPAKcKLABcHyRhiaf9LO_i2xcosgw3Y3E'
    //         );
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    //     $result = curl_exec($ch);
    //     if ($result === FALSE) {
    //          die('Problem occurred: ' . curl_error($ch));
    //     }
        
    //     curl_close($ch);
    // }
    
    
    
    public function notifyDoctorMethod($listing_id, $user_id,$title,$msg, $booking_date, $booking_time, $booking_id, $consultation_type, $patient_name)
    {

          $customer_token = $this->db->query("SELECT name,token,agent,token_status FROM users WHERE id='$listing_id'");
          $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                // $getusr = $user_plike->row_array();

                $usr_name = $token_status['name'];
                $agent = $token_status['agent'];
                $reg_id = $token_status['token'];
                $img_url = 'https://medicalwale.com/img/medical_logo.png';
                $tag = 'text';
                $key_count = '1';
                $title = $title;
                $msg = $msg;
                $this->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent,$booking_date,$booking_time,$booking_id,$consultation_type);
            }
    }


        //send notification through firebase
        /* notification to send in the doctor app for appointment confirmation*/
    public function send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent,$booking_date,$booking_time,$booking_id,$consultation_type) {
     
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        
        if (!defined("GOOGLE_GCM_URL"))
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        $fields = array(
            'to' => $reg_id,
            'priority' => "high",
            $agent === 'android' ? 'data' : 'notification' => array(
                "title" => $title,
                "message" => $msg,
                "notification_image" => $img_url,
                "tag" => $tag, 
                'sound' => 'default',
                "notification_type" => 'appointment_notifications',
                "notification_date" => $date,
                "appointment_id" => $booking_id,
                "appointment_date" => $booking_date,
                "appointment_time" =>$booking_time,
                "type_of_connect" => $consultation_type
             //   app date app time  app it 
            )
        );
       
        $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AAAAMCq6aPA:APA91bFjtuwLeq5RK6_DFrXntyJdXCFSPI0JdJcvoXXi0xIGm7qgzQJmUl7aEq3HjUTV3pvlFr5iv5pOWtCoN3JIpMSVhU8ZFzusaibehi5MPwThRmx1pCnm3Tm-x6wI8tGwhc0eUj2U' : 'Authorization: key=AIzaSyAPAKcKLABcHyRhiaf9LO_i2xcosgw3Y3E'
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
    }
    
    
    // get_doctor_consultation
    
    public function get_doctor_consultation($doctor_id) {
        $clinic_id = 0;
        $results = array();
        $available_call = "0";
        $available_video = "0";
        $available_chat = "0";
        $results_call = array();
        $results_video = array();
        $results_chat = array();
        $q = $this->db->query("SELECT * FROM `doctor_consultation` WHERE `doctor_user_id` = '$doctor_id' ");
        
        $qRows = $q->result_array();
        
        foreach($qRows as $qRow){
            if($qRow['consultation_name'] == 'chat' && $qRow['is_active'] == 1){
                $available_call = $qRow['is_active'];
                $duration = $qRow['duration'];
                $charges = $qRow['charges'];
                
                $results_call['consultation_type'] = 'call';
                $results_call['info'] = array(
                        'duration' => $duration,
                        'charges' => $charges
                    );    
            }
            
            if($qRow['consultation_name'] == 'video' && $qRow['is_active'] == 1){
                $available_video = $qRow['is_active'];
                $duration = $qRow['duration'];
                $charges = $qRow['charges'];
                
                $results_video['consultation_type'] = 'video';
                $results_video['info'] = array(
                        'duration' => $duration,
                        'charges' => $charges
                    );    
            }
            
             if($qRow['consultation_name'] == 'chat' && $qRow['is_active'] == 1){
                $available_chat = $qRow['is_active'];
                $duration = $qRow['duration'];
                $charges = $qRow['charges'];
                
                $results_chat['consultation_type'] = 'chat';
                $results_chat['info'] = array(
                        'duration' => $duration,
                        'charges' => $charges
                    );    
            }
            
        }
      
      if($results_call){$results[] = $results_call;}
      if($results_video){$results[] = $results_video;}
      if($results_chat){$results[] = $results_chat;}

     
        $data = array ('doctor_id' => $doctor_id, 'available_call' => $available_call, 'available_video' => $available_video, 'available_chat' => $available_chat, 'info' => $results);
        return $data;
    }
    
    // edit_bookings
    public function edit_bookings($booking_id, $newdata) {
        // $data['booking_id'] = $booking_id;
        
        $updateStatus  = $this->db->where('booking_id', $booking_id)->update('doctor_booking_master', $newdata);
        $data = $newdata;
        return $data;
    }
    
    public function get_cart_details_list($user_id)
    {
          $query = $this->db->query("SELECT * FROM `cart` WHERE `user_id` = '$user_id' ");
        $num_count = $query->num_rows();
        $qRows = $query->result_array();
       // print_r ($num_count);
      //  print_r ($qRows);
      //  die();
        if($num_count>0)
        {
        foreach($qRows as $qRow)
        {
            $id = $qRow['id'];
            $user_id = $qRow['user_id'];
            $listing_id =$qRow['listing_id'];
            $product_id =$qRow['product_id'];
            $sub_category = $qRow['sub_category'];
            $quantity = $qRow['quantity'];
            $product_type = $qRow['product_type'];
            $status = $qRow['status'];
            //  echo "SELECT * FROM `product` WHERE `id` = '$id' and `sub_category` = '$sub_category' ";
              $product_query = $this->db->query("SELECT * FROM `product` WHERE `id` = '$product_id' and `sub_category` = '$sub_category' ");
               $nume_count = $product_query->num_rows();
              $product_Rows = $product_query->result_array();
            //  print_r ($nume_count);
            //  print_r ($product_Rows);
            //  die();
            if($nume_count>0)
            {
                foreach($product_Rows as $product_Row)
                {
                    $product_name = $product_Row['product_name'];
                   $product_price = $product_Row['product_price'];
                   $product_description = $product_Row['product_description'];
                   $product_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/product_images/'. $product_Row['image'];
                   $in_stock = $product_Row['in_stock'];
                   $product_weight = $product_Row['pack'];
                }
            }
            else
            {
                $product_name = "";
                   $product_price = "";
                   $product_description = "";
                   $product_image = "";
                   $in_stock = "";
                   $product_weight = "";
            }
            
             $results_cart[] = array(
                        'id' => $id,
                        'user_id' => $user_id,
                        'listing_id' =>$listing_id,
                        'product_id' =>$product_id,
                        'sub_category' =>$sub_category,
                        'quantity' =>$quantity,
                        'product_type'=>$product_type,
                        'status'=>$status,
                        'product_name' => $product_name,
                        'product_price' => $product_price,
                        'product_image' => $product_image,
                        'product_description' => $product_description,
                        'in_stock' => $in_stock,
                        'product_weight' => $product_weight
                    ); 
        }
            $data = $results_cart;
            return $data;
        }
        else
        {
           return array(
                'status' => 200,
                'data' => array(),
                'message' => 'data not found'
            ); 
        }
    }
    
    public function recent_doctor_list($user_id){
        
        //echo "SELECT * FROM doctor_booking_master WHERE user_id='$user_id' GROUP BY listing_id";
        $booking_master = $this->db->query("SELECT * FROM doctor_booking_master WHERE user_id='$user_id' GROUP BY listing_id");
        $booking_count = $booking_master->num_rows();
        $qRows = $booking_master->result_array();
        if($booking_count>0)
        {
            foreach($qRows as $qRow)
            {
                $listing_id = $qRow['listing_id'];
                //echo "SELECT * FROM doctor_list WHERE user_id='$listing_id'";
                $doctor_list = $this->db->query("SELECT * FROM doctor_list WHERE user_id='$listing_id'");
                $doctor_list_count = $doctor_list->num_rows();
                $qRows2 = $doctor_list->row();
                if($doctor_list_count>0)
                {
                    $doctor_name         = $qRows2->doctor_name;
                        $email               = $qRows2->email;
                        $gender              = $qRows2->gender;
                        $doctor_phone        = $qRows2->telephone;
                        $dob                 = $qRows2->dob;
                        $category            = $qRows2->category;
                        $speciality          = $qRows2->speciality;
                        $service             = $qRows2->service;
                        $degree              = $qRows2->qualification;
                        $experience          = $qRows2->experience;
                        $reg_council         = $qRows2->reg_council;
                        $reg_number          = $qRows2->reg_number;
                        $doctor_user_id      = $qRows2->user_id;
                        $address             = $qRows2->address;
                        
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        
                        if ($qRows2->image != '') {
                            $profile_pic = $qRows2->image;
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        }else{
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                        }
                        
                       $resultpost[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $dob,
                            'experience' => $experience,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'rating' => $total_rating
                        );
                }
            }
            return $resultpost;
        }
        else{
            return $resultpost = array();
        }
        
        
    }
    
        public function doctor_notification_confirm($booking_id){
       $data="SELECT doctor_booking_master.*,users.name,users.email,users.phone,doctor_clinic.clinic_name,doctor_list.telephone,doctor_list.doctor_name FROM doctor_booking_master LEFT JOIN users ON (doctor_booking_master.user_id=users.id) LEFT JOIN doctor_clinic ON (doctor_booking_master.clinic_id=doctor_clinic.id) 
          LEFT JOIN doctor_list ON (doctor_booking_master.listing_id=doctor_list.user_id)WHERE doctor_booking_master.booking_id='".$booking_id."'";
         
         $result = $this->db->query($data)->row();
            return $result;
}

 public function get_color_by_id($colorId){
        
        $get_color_by_id = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$colorId'")->result_array();
        if(sizeof($get_color_by_id) > 0){
            return $get_color_by_id;
        } else {
            $get_color_by_id = array();
            return $get_color_by_id;
        }
    }
    
    
    public function get_size_by_id($sizeId){
        $get_size_by_id = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$sizeId'")->result_array();
        if(sizeof($get_size_by_id) > 0){
            return $get_size_by_id;
        } else {
            $get_size_by_id = array();
            return $get_size_by_id;
        }
       
    }
    
}
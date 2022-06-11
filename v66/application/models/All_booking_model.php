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
                    $url="https://doctor.medicalwale.com/prescription/".$prescription_id.".pdf";
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
                   if($status == null){$status = "";}if($listing_id== null){ $listing_id="";}if($booking_id== null){$booking_id="";} if($Nursing_id== null){$Nursing_id="";} 
             if($patient_name== null){  $patient_name="";}if($package_id== null){  $package_id="";} if($package_name== null){  $package_name="";} 
             if($package_amount== null){$package_amount="";}if($book_type== null){$book_type="";}if($patiente_condition== null){ $patiente_condition="";}
             if($attendent_time== null ){ $attendent_time="";}if($attendant_hour== null){ $attendant_hour="";}if($tentative_intime== null){ $tentative_intime="";}
             if($nursing_gender== null){$nursing_gender="";} if($attendant_needed== null)
                    {
                 
                      
                      $attendant_needed="";
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
       // $results = $this->db->query("SELECT * FROM `user_order` WHERE `user_id` = '$user_id' AND  `listing_type`='34' ORDER BY order_id DESC");
       
       $results = $this->db->query("SELECT vd.delivery_by_medicalwale, uo.* FROM `user_order` as uo left join vendor_details_hm as vd on (uo.`listing_id` = vd.v_id) WHERE uo.`user_id` = '$user_id' AND  uo.`listing_type`='34'  and (uo.order_id like '%$term%' or uo.invoice_no like '%$term%' or uo.listing_name like '%$term%') ORDER BY order_id DESC");
       //echo "SELECT vd.delivery_by_medicalwale, uo.* FROM `user_order` as uo left join vendor_details_hm as vd on (uo.`listing_id` = vd.v_id) WHERE uo.`user_id` = '$user_id' AND  uo.`listing_type`='34'  and (user_order.order_id like '%$term%' or user_order.invoice_no like '%$term%' or listing_name like '%$term%') ORDER BY order_id DESC"; die();
    }
else
{
    // echo "SELECT vd.delivery_by_medicalwale, uo.* FROM `user_order` as uo left join vendor_details_hm as vd on (uo.`listing_id` = vd.v_id) WHERE uo.`user_id` = '$user_id' AND  uo.`listing_type`='34'  and (uo.order_id like '%$term%' or uo.invoice_no like '%$term%' or uo.listing_name like '%$term%') ORDER BY order_id DESC"; die();
      $results = $this->db->query("SELECT vd.delivery_by_medicalwale, uo.* FROM `user_order` as uo left join vendor_details_hm as vd on (uo.`listing_id` = vd.v_id) WHERE uo.`user_id` = '$user_id' AND  uo.`listing_type`='34'  and (uo.order_id like '%$term%' or uo.invoice_no like '%$term%' or uo.listing_name like '%$term%') ORDER BY order_id DESC");

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
             $query = $this->db->query("SELECT user_order.*,medical_stores.medical_name FROM user_order LEFT JOIN medical_stores on user_order.listing_id=medical_stores.id WHERE  
             (user_order.listing_type='13' or user_order.listing_type='38' )  AND user_order.user_id='$user_id'  and (user_order.listing_name like '%$term%' or user_order.invoice_no like '%$term%')  group by user_order.invoice_no order by user_order.order_id DESC ");
  
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
if($rxId == "")
{
    $rxId="";
}
else
{
    $rxId;
}
            $tracker = $this->All_booking_model->pharmacy_tracker($user_id, $invoice_no);
            
             if($listing_type=="38")
               {
                  /* if(!empty($rxId) )
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
                    "tracker" => $tracker,
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
                }*/
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
                    "tracker" => $tracker,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result,
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel
                ); 
               }
            }
        } else {
            $resultpost = array();
        }
             
             
             
              if(empty($term))
          {
                 $query = $this->db->query("SELECT * from life_saving_drugs where user_id='$user_id' order by id desc");
}
 else
          {
             $query = $this->db->query("SELECT * from life_saving_drugs where user_id='$user_id' order by id desc");
  
          }

                $count = $query->num_rows();
                 if ($count > 0) { 
            foreach ($query->result_array() as $row) {
                
               
                $member_id = $row['member_id'];
               
                $qty = $row['qty'];
                $date = $row['cdate'];
                $mobile = $row['mobile'];
                $email = $row['email'];
                $image = $row['image'];
                $invoice_no = $row['invoice_no'];
                $order_status = $row['order_status'];
                $action_by = $row['action_by'];
                $updated_at = $row['created_at'];
                $cancel_reason = $row['cancel_reason'];
                if(empty($cancel_reason))
                {
                    $cancel_reason='';
                }
                if(empty($updated_at))
                {
                    $updated_at='';
                }
                
                $query1 = $this->db->query("SELECT * from users where id='$member_id'");
                $row1=$query1->row_array();
            if(empty($manufacturer))
            {
                $manufacturer="";
            }
            if(empty($mrp))
            {
                $mrp="";
            }
            $name=$row1['name'];
                $resultpost8[] = array(
                    "patient_name"=>$name,
                    "qty" => $qty,
                    "date" => $date,
                    "mobile" => $mobile,
                    "email" => $email,
                    "image" => "https://d2c8oti4is0ms3.cloudfront.net/images/Check_In_Image/".$image,
                    "invoice_no" => $invoice_no,
                    "order_status" => $order_status,
                    "created_date"=>$updated_at,
                    "cancel_reason"=>$cancel_reason
                );
               
               }
             
            }
         else {
            $resultpost8 = array();
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
                'Enquiry'=>$resultpost8,
                
                
                
            );
                
             return $all_booking;
    }
   
   public function all_booking_details_v12($user_id,$page)
    {
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        $resultpost = array();
        $resultpost1 = array();
        $resultpost2=array();
        $resultpost3 = array();
        $resultpost4=array();
        $resultpost5=array();
        $resultpost7 = array();
        $resultpost8=array();
        
        $sql1=$this->db->query("SELECT user_id as uid, listing_type as vid, order_date as date, invoice_no as inv_no from user_order where user_id='$user_id'
                                UNION all
                                SELECT user_id as uid, vendor_id as vid,booking_date as date, booking_id as inv_no  from booking_master where user_id='$user_id'
                                UNION all
                                SELECT user_id as uid, vendor_type as vid,booking_date as date, booking_id as inv_no  from doctor_booking_master where user_id='$user_id'
                                UNION all
                                SELECT user_id as uid, listing_id as vid,date as date, invoice_no as inv_no  from life_saving_drugs where user_id='$user_id'
                                UNION all
                                SELECT user_id as uid, vendor_type as vid,booking_date as date, booking_id as inv_no  from hospital_booking_master where user_id='$user_id'
                                ");
        $count12 = $sql1->num_rows();
        
        
        
        
        $sql=$this->db->query("SELECT user_id as uid, listing_type as vid, order_date as date, invoice_no as inv_no from user_order where user_id='$user_id'
                                    UNION all
                                    SELECT user_id as uid, vendor_id as vid,booking_date as date, booking_id as inv_no  from booking_master where user_id='$user_id'
                                    UNION all
                                    SELECT user_id as uid, vendor_type as vid,booking_date as date, booking_id as inv_no  from doctor_booking_master where user_id='$user_id'
                                    UNION all
                                    SELECT user_id as uid, listing_id as vid,date as date, invoice_no as inv_no  from life_saving_drugs where user_id='$user_id'
                                    UNION all
                                    SELECT user_id as uid, vendor_type as vid,booking_date as date, booking_id as inv_no  from hospital_booking_master where user_id='$user_id'
                                    order by date desc  LIMIT $start, $limit");
        $count1 = $sql->num_rows();
            if ($count1 > 0) {
                foreach ($sql->result_array() as $row1) {
                    $invoice_no=$row1['inv_no'];
                    $vid=$row1['vid'];
                    
                    
                if($vid=="13" || $vid=="38")
                    {
                        $query = $this->db->query("SELECT user_order.*,medical_stores.medical_name,medical_stores.profile_pic FROM user_order LEFT JOIN medical_stores on user_order.listing_id=medical_stores.id WHERE (user_order.listing_type='13' or user_order.listing_type='38' ) AND user_order.user_id='$user_id' and  user_order.invoice_no='$invoice_no' group by user_order.invoice_no order by user_order.order_id DESC");
                        $count = $query->num_rows();
                        if ($count > 0) 
                           { 
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
           
               if ($row['profile_pic'] != '') {
                    $profile_pic = $row['profile_pic'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
                }
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
            $tracker = $this->All_booking_model->pharmacy_tracker($user_id, $invoice_no);
            
             if($listing_type=="38")
               {
                   if(!empty($rxId) )
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
                    "tracker" => $tracker,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result,
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel,
                    "profile_img"=>$profile_pic
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
                    "tracker" => $tracker,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result,
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel,
                    "profile_img"=>$profile_pic
                ); 
               }
            }
       } 
                        else 
                        {
                            $resultpost = array();
                        }
                    }
                if($vid=="6")
                    {
                    $querys1 = $this->db->query("SELECT fitness_center_branch.branch_name,fitness_center_branch.user_id as branch_fit_id, fitness_center_branch.branch_image, fitness_center_branch.branch_phone, fitness_center_branch.branch_address, fitness_center_branch.pincode, fitness_center_branch.state, fitness_center_branch.city,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time,booking_master.trainer_package_id,booking_master.trainer_id, booking_master.joining_date FROM booking_master
                                                 INNER JOIN fitness_center_branch ON booking_master.branch_id=fitness_center_branch.id
                                                 WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='6' and booking_master.booking_id='$invoice_no' ORDER BY booking_master.id DESC");
                    $count1 = $querys1->num_rows();
                    if ($count1 > 0) 
                       {
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
                }} 
                    else 
                       {$resultpost1 = array();}
            
                }
                if($vid=="12")
                {
                       $query3 = $this->db->query("SELECT booking_master.*,health_record.patient_name,nursing_attendant.address,nursing_attendant.city,nursing_attendant.state,nursing_attendant.contact,nursing_attendant.pincode,nursing_booking_details.* FROM booking_master  LEFT JOIN users ON (booking_master.user_id = users.id) LEFT JOIN health_record ON(booking_master.patient_id=health_record.id) LEFT JOIN nursing_attendant ON(booking_master.listing_id=nursing_attendant.user_id) LEFT JOIN nursing_booking_details ON(booking_master.booking_id=nursing_booking_details.booking_id) where booking_master.user_id = '$user_id' and booking_master.vendor_id = '12' and booking_master.booking_id='$invoice_no' ORDER BY booking_master.id DESC");
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
                   if($status == null){$status = "";}if($listing_id== null){ $listing_id="";}if($booking_id== null){$booking_id="";} if($Nursing_id== null){$Nursing_id="";} 
             if($patient_name== null){  $patient_name="";}if($package_id== null){  $package_id="";} if($package_name== null){  $package_name="";} 
             if($package_amount== null){$package_amount="";}if($book_type== null){$book_type="";}if($patiente_condition== null){ $patiente_condition="";}
             if($attendent_time== null ){ $attendent_time="";}if($attendant_hour== null){ $attendant_hour="";}if($tentative_intime== null){ $tentative_intime="";}
             if($nursing_gender== null){$nursing_gender="";} if($attendant_needed== null)
                    {
                 
                      
                      $attendant_needed="";
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
                }
                if($vid=="5")
                    {
                     
           $query2 = $this->db->query("select doctor_booking_master.booking_id, doctor_booking_master.booking_date,doctor_booking_master.listing_id,doctor_booking_master.user_id, doctor_booking_master.listing_id,doctor_booking_master.clinic_id, doctor_prescription.id as prescription_id, doctor_booking_master.booking_time,doctor_booking_master.status,doctor_booking_master.consultation_type, doctor_clinic.clinic_name,users.name as patient_name, doctor_clinic.address,doctor_clinic.image,doctor_clinic.state,doctor_clinic.city,doctor_clinic.pincode, doctor_clinic.consultation_charges,doctor_clinic.contact_no,dl.doctor_name from doctor_booking_master LEFT JOIN doctor_prescription ON (doctor_booking_master.booking_id = doctor_prescription.booking_id AND doctor_booking_master.listing_id = doctor_prescription.doctor_id AND doctor_booking_master.clinic_id = doctor_prescription.clinic_id)  LEFT JOIN doctor_clinic on (doctor_booking_master.clinic_id = doctor_clinic.id) LEFT JOIN users ON (doctor_booking_master.user_id = users.id) LEFT JOIN doctor_list as dl ON(dl.user_id=doctor_booking_master.listing_id) where doctor_booking_master.user_id = '$user_id' and doctor_booking_master.booking_id='$invoice_no' ORDER BY doctor_booking_master.id DESC");

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
            
                    }
                 if($vid=="10")
                    {
                       $count_query4 = $this->db->query("SELECT lb.*,bm.vendor_id,bm.status from booking_master as bm LEFT JOIN lab_booking_details as lb ON(bm.booking_id = lb.booking_id) where bm.user_id='$user_id' and bm.booking_id ='$invoice_no' and (bm.vendor_id='10' or bm.vendor_id='31') order by bm.id desc");
                        $lab_booked4       = $count_query4->num_rows();
                        if ($lab_booked4 > 0) 
                        {
                          foreach ($count_query4->result_array() as $Lbooked) 
                            {
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
                                    $book_query = $this->db->query("SELECT status FROM booking_master WHERE booking_id='$bk_id'");
                                     $conff=$user_query->num_rows();
                                     if($conff>0){
                                    $status = $book_query->row()->status;
                                }
                                     else{
                                    $status = '';
                                }
                                     $report_path ='';
                                     $book_query_path = $this->db->query("SELECT report_path FROM reports WHERE booking_id='$bk_id'");
                                     $report_path_count =  $book_query_path->num_rows();
                                     if($report_path_count > 0)
                                       {
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
                        }
                       else
                        {
                         $resultpost4 = array(); 
                        }
                    }    
                if($vid=="34")
                   {
                       
                        $pro_list = array();
                        $results = $this->db->query("SELECT vd.delivery_by_medicalwale, uo.* FROM `user_order` as uo left join vendor_details_hm as vd on (uo.`listing_id` = vd.v_id) WHERE uo.`user_id` = '$user_id' AND  uo.`listing_type`='34'  and uo.invoice_no='$invoice_no' ORDER BY order_id DESC");
                      
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
                            
                            $order += ['products'=>$pro_list];
                                foreach($order as $key => $value){
                                    if($value == null){
                                        $order[$key] = "";
                                    }
                                }
                                
                            $resultpost7[] = $order;
                        }
                   }
                if($vid=="8")
                {
                     $resultpost61 = array();
       // IPD
       
       $booking_details = $this->db->query("SELECT * FROM booking_master WHERE user_id='$user_id' and vendor_id='8' and booking_id='$invoice_no' order by id DESC");
       
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
                    'image'=>'',
                    'booking_type'=>"IPD"
                );
            
           }
        }
        
       // OPD
        
        $query= $this->db->query("select hospital_booking_master.booking_id,hospital_booking_master.booking_date,hospital_booking_master.listing_id,hospital_booking_master.user_id,hospital_booking_master.listing_id,
hospital_booking_master.doctor_id,hospital_doctor_prescription.id as prescription_id,hospital_booking_master.booking_time,hospital_booking_master.status,hospital_booking_master.consultation_type,
hospitals.name_of_hospital,users.name as patient_name, hospitals.address,hospitals.image,hospitals.state,hospitals.city,hospitals.pincode, dl.consultation,hospitals.phone,dl.doctor_name from hospital_booking_master  LEFT JOIN hospital_doctor_prescription 
ON (hospital_booking_master.patient_id = hospital_doctor_prescription.patient_id AND hospital_booking_master.doctor_id = hospital_doctor_prescription.doctor_id AND hospital_booking_master.listing_id = hospital_doctor_prescription.hospital_id) LEFT JOIN hospitals on (hospital_booking_master.listing_id = hospitals.user_id)
LEFT JOIN users ON (hospital_booking_master.user_id = users.id)  LEFT JOIN hospital_doctor_list as dl ON(dl.id=hospital_booking_master.doctor_id)
where hospital_booking_master.user_id = '$user_id' and hospital_booking_master.booking_id='$invoice_no'  ORDER BY hospital_booking_master.id DESC");

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
                }
                if($vid=="45")
                {
                   $query = $this->db->query("SELECT * from life_saving_drugs where user_id='$user_id' order by id desc");
  
                $count = $query->num_rows();
                 if ($count > 0)
                 { 
            foreach ($query->result_array() as $row) 
            {
                
               
                $member_id = $row['member_id'];
               
                $qty = $row['qty'];
                $date = $row['date'];
                $mobile = $row['mobile'];
                $email = $row['email'];
                $image = $row['image'];
                $invoice_no = $row['invoice_no'];
                $order_status = $row['order_status'];
                $action_by = $row['action_by'];
                $updated_at = $row['created_at'];
                $cancel_reason = $row['cancel_reason'];
                if(empty($cancel_reason))
                {
                    $cancel_reason='';
                }
                if(empty($updated_at))
                {
                    $updated_at='';
                }
                
                $query1 = $this->db->query("SELECT * from users where id='$member_id'");
                $row1=$query1->row_array();
            if(empty($manufacturer))
            {
                $manufacturer="";
            }
            if(empty($mrp))
            {
                $mrp="";
            }
            $name=$row1['name'];
                $resultpost8[] = array(
                    "patient_name"=>$name,
                    "qty" => $qty,
                    "date" => $date,
                    "mobile" => $mobile,
                    "email" => $email,
                    "image" => "https://d2c8oti4is0ms3.cloudfront.net/images/Check_In_Image/".$image,
                    "invoice_no" => $invoice_no,
                    "order_status" => $order_status,
                    "created_date"=>$updated_at,
                    "cancel_reason"=>$cancel_reason,
                    "vendor_type"=>45
                );
               
               }
             
            }
         else {
            $resultpost8 = array();
        }
                }
                
                
                
                
                
                
                }
                $all_booking[] = array(
                'Pharmacy' => $resultpost, 
                'Labs'=>$resultpost4,
                'Doctors'=>$resultpost2,
                'Healthmall'=>$resultpost7,
                'Hospital'=>$resultpost5,
                'Fitness Centres' => $resultpost1,
                'Nursing Attendant'=>$resultpost3,
                'Enquiry'=>$resultpost8,
                );
                $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'total_count'=>(string)$count12,
                            'data'=>$all_booking
                        );
            }
            
             else
             {
                  $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'total_count'=>'0',
                            'data'=>array()
                        );
                
             }
                
             return $resp;
             
    }
    
    
    
public function all_booking_details_v1($user_id,$page)
    {
        $image='';
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        $resultpost = array();
        $resultpost1 = array();
        $resultpost2=array();
        $resultpost3 = array();
        $resultpost4=array();
        $resultpost5=array();
        $resultpost7 = array();
        $resultpost8=array();
        $resultpost61 = array();
                     $resultpost62 = array();
                     $resultpost234=array();
                     $resultpost11=array();
                      $resultpost12=array();
        $sql1=$this->db->query("SELECT user_id as uid, listing_type as vid, order_date as date, invoice_no as inv_no from user_order where user_id='$user_id' GROUP BY invoice_no
                                UNION all
                                SELECT user_id as uid, vendor_id as vid,booking_date as date, booking_id as inv_no  from booking_master where user_id='$user_id'
                                UNION all
                                SELECT user_id as uid, vendor_type as vid,booking_date as date, booking_id as inv_no  from doctor_booking_master where user_id='$user_id'
                                UNION all
                                SELECT user_id as uid, vendor_type as vid,booking_date as date, booking_id as inv_no  from hospital_booking_master where user_id='$user_id'
                                UNION all
                                SELECT user_id as uid, listing_id as vid,created_at as date, invoice_no as inv_no  from life_saving_drugs where user_id='$user_id'
                                UNION all
                                SELECT user_id as uid, vendor_type as vid,Create_time as date, ride_id as inv_no  from rides where user_id='$user_id'
                                ");
        $count12 = $sql1->num_rows();
        
        
     
        $sql=$this->db->query("SELECT user_id as uid, listing_type as vid, order_date as date, invoice_no as inv_no from user_order where user_id='$user_id' GROUP BY invoice_no
                                    UNION all
                                    SELECT user_id as uid, vendor_id as vid,booking_date as date, booking_id as inv_no  from booking_master where user_id='$user_id'
                                    UNION all
                                    SELECT user_id as uid, vendor_type as vid,booking_date as date, booking_id as inv_no  from doctor_booking_master where user_id='$user_id'
                                    UNION all
                                    SELECT user_id as uid, vendor_type as vid,booking_date as date, booking_id as inv_no  from hospital_booking_master where user_id='$user_id'
                                    UNION all
                                    SELECT user_id as uid, listing_id as vid,created_at as date, invoice_no as inv_no  from life_saving_drugs where user_id='$user_id'
                                    UNION all
                                    SELECT user_id as uid, vendor_type as vid,Create_time as date, ride_id as inv_no  from rides where user_id='$user_id'
                                    order by date desc  LIMIT $start, $limit");
        $count1 = $sql->num_rows();
        
      
        
        if ($count1 > 0) {
            foreach ($sql->result_array() as $row1) {
               
                $invoice_no=$row1['inv_no'];
                $vid=$row1['vid'];
          
                
            if($vid=="13" || $vid=="38" || $vid=="44")
                {
                    
                  $order_amount =  $gst_rs = $gst_per = $chc = $grand_total = "";
                    $dis = 0;
                        $query = $this->db->query("SELECT user_order.*,medical_stores.medical_name,medical_stores.profile_pic FROM user_order LEFT JOIN medical_stores on user_order.listing_id=medical_stores.user_id WHERE (user_order.listing_type='13' or user_order.listing_type='38' ) AND user_order.user_id='$user_id' and  user_order.invoice_no='$invoice_no' group by user_order.invoice_no order by user_order.order_id DESC");
                        $count = $query->num_rows(); 
                        
                        if ($count > 0) 
                                               { 
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
                                    $order_date = date('l j M Y h:i A', strtotime($order_date));
                                   
                                   
                                   /*Added by swapnali on 20th nov 2k19 at 5pm*/
                                    $delivery_charge = $row['delivery_charge'];
                                    $delivery_charges_by_customer = $row['delivery_charges_by_customer'];
                                    $order_status = $row['order_status'];
                                    $order_type = $row['order_type'];
                                    $action_by = $row['action_by'];
                                    $rxId = $row['rxId'];
                                    
                                    if($vid=="13" || $vid=="44"){
                                        $delivery_charge = $delivery_charges_by_customer;
                                    } 
                                    
                                    $dis = $row['discount'];
                                    $gst_per = $row['gst'];
                                    $chc = strval($row['chc']);
                                    $order_amount = strval($row['actual_cost']);
                                    $gst_rs = strval(($order_amount - $dis)  * ($gst_per / 100));
                                    $grand_total = strval($order_amount + $gst_rs + $chc - $dis + $delivery_charge); 
                                    /*Added by swapnali on 20th nov 2k19 at 5pm => END*/

                                    

                                    
                                   if ($row['profile_pic'] != '') {
                                        $profile_pic = $row['profile_pic'];
                                        $profile_pic = str_replace(' ', '%20', $profile_pic);
                                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                                    } else {
                                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
                                    }
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
                                              
                                                $desc = (($product_price * $product_quantity)*$product_discount)/100;
                                                $sub_total_discount +=$desc;
                                               
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
                                                    "product_discount" => $desc,
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
                                                  
                                                   $desc1 = (($prescription_price*$prescription_quantity)*$prescription_discount)/100;
                                                    $sub_total_discount1 += $desc1;
                                                    $prescription_result[] = array(
                                                        "prescription_name" => $prescription_name,
                                                        "prescription_quantity" => $prescription_quantity,
                                                        "prescription_price" => $prescription_price,
                                                        "prescription_discount" => $desc1,
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
                                $tracker = $this->All_booking_model->pharmacy_tracker($user_id, $invoice_no);
                                
                                 if($listing_type=="38")
                                   {
                                       if(!empty($rxId) )
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
                                        "user_email" => "",
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
                                        "is_cancel" => $is_cancel,
                                        "profile_img"=>$profile_pic,
                                        "image"=>$profile_pic,
                                        "life_qty"=>"",
                                        "urgent"=>"",
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
                                        "user_email" => "",
                                        "order_total" => $order_amount,
                                        "order_discount"=>intval($dis),
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
                                        "is_cancel" => $is_cancel,
                                        "profile_img"=>$profile_pic,
                                        "image"=>$profile_pic,
                                        "life_qty"=>"",
                                        "urgent"=>"",
                                    ); 
                                   }
                                }
                           } 
                        else 
                        {
                            
                            
                            if($vid=="44")
                            {
                                
                         $query = $this->db->query("SELECT * FROM user_order WHERE user_id='$user_id' and invoice_no='$invoice_no' group by invoice_no order by order_id DESC");
                        $count = $query->num_rows(); 
                        
                        if ($count > 0) 
                           { 
            foreach ($query->result_array() as $row) {
                
                $order_id = $row['order_id'];
                $order_type = $row['order_type'];
                $delivery_time = $row['delivery_time'];
               
                $listing_type = $row['listing_type'];
                $invoice_no = $row['invoice_no'];
               
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
                $order_date = date('l j M Y h:i A', strtotime($order_date));
               
                $delivery_charge = $row['delivery_charge'];
                $order_status = $row['order_status'];
                $order_type = $row['order_type'];
                $action_by = $row['action_by'];
                $rxId = $row['rxId'];
                
                /*Added by swapnali on 20th nov 2k19 at 5pm*/
                $delivery_charge = $row['delivery_charge'];
                $delivery_charges_by_customer = $row['delivery_charges_by_customer'];
                $order_status = $row['order_status'];
                $order_type = $row['order_type'];
                $action_by = $row['action_by'];
                $rxId = $row['rxId'];
                
                if($vid=="13" || $vid=="44"){
                    $delivery_charge = $delivery_charges_by_customer;
                } 
                
                $dis = $row['discount'];
                $gst_per = $row['gst'];
                $chc = strval($row['chc']);
                $order_amount = strval($row['actual_cost']);
                $gst_rs = strval(($order_amount - $dis)  * ($gst_per / 100));
                $grand_total = strval($order_amount + $gst_rs + $chc - $dis + $delivery_charge); 
                /*Added by swapnali on 20th nov 2k19 at 5pm => END*/
                
                
                
              
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
                          
                            $desc = (($product_price * $product_quantity)*$product_discount)/100;
                            $sub_total_discount +=$desc;
                           
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
                                "product_discount" => $desc,
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
                              
                               $desc1 = (($prescription_price*$prescription_quantity)*$prescription_discount)/100;
                                $sub_total_discount1 += $desc1;
                                $prescription_result[] = array(
                                    "prescription_name" => $prescription_name,
                                    "prescription_quantity" => $prescription_quantity,
                                    "prescription_price" => $prescription_price,
                                    "prescription_discount" => $desc1,
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
    $listing_paymode="Cash On Delivery";  
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
            $tracker = $this->All_booking_model->pharmacy_tracker($user_id, $invoice_no);
            
           $resultpost[] = array(
                    "order_id" => $order_id,
                    "medlife_order_id" => $orderId,
                    "delivery_time" => str_replace('null', '', $delivery_time), //- swap
                    "order_type" => $order_type,
                    "listing_id" => "",
                    "listing_name" => "Night Owl",
                    "listing_type" => "44",
                    "listing_payment_mode" => $listing_paymode,
                    "invoice_no" => $invoice_no,
                    "chat_id" => "",
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
                    "user_email" => "",
                    "order_total" => $order_amount,
                    "order_discount"=>intval($dis),
                    "gst_rs" => $gst_rs,
                    "gst_per" => $gst_per,
                    "chc" => $chc,
                    "grand_total" => $grand_total,
                    "payment_method" => $payment_method,
                    "order_date" => $order_date,
                   
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    "delivery_charge" => $delivery_charge, //- swap
                    "product_order" => $product_resultpost,
                    "tracker" => $tracker,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result,
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel,
                    "profile_img"=>"",
                    "image"=>"",
                    "life_qty"=>"",
                    "urgent"=>"",
                ); 
            }
       }        
                                
                               
                            }
                            else
                            {
                                $resultpost = array();
                            }
                        }
                    }
                if($vid=="6")
                    {
                    $querys1 = $this->db->query("SELECT fitness_center_branch.branch_name,fitness_center_branch.user_id as branch_fit_id, fitness_center_branch.branch_image, fitness_center_branch.branch_phone, fitness_center_branch.branch_address, fitness_center_branch.pincode, fitness_center_branch.state, fitness_center_branch.city,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time,booking_master.trainer_package_id,booking_master.trainer_id, booking_master.joining_date FROM booking_master
                                                 INNER JOIN fitness_center_branch ON booking_master.branch_id=fitness_center_branch.id
                                                 WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='6' and booking_master.booking_id='$invoice_no' ORDER BY booking_master.id DESC");
                    $count1 = $querys1->num_rows();
                    if ($count1 > 0) 
                       {
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
                   // $order_date= $trail_booking_date . " ". $trail_booking_time;
                   $booking_date1 = date('j M Y  h:i A', strtotime($row1['booking_date']));
                     $order_date1 = date('l j M Y h:i A', strtotime($booking_date1));
                    

                    $resultpost1[] = array(
                        'booking_id' => $booking_id,
                        "invoice_no" => $invoice_no,
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
                        'order_date'=>$order_date1,
                        
                        'joining_date' => $joining_date,
                        'book_for_date' => $book_for_date,
                        'booking_date' => $booking_date,
                        'status' => $status,
                        'listing_type'=>$row1['vendor_id'],
                        "image"=>$branch_image,
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
                   
                    
 $order_date1 = date('l j M Y h:i A', strtotime($row1['booking_date']));

                    $resultpost1[] = array(
                        'booking_id' => $booking_id,
                        "invoice_no" => $invoice_no,
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
                        'order_date'=>$order_date1,
                       
                        'status' => $status,
                        'listing_type'=>$row1['vendor_id']
                    );
                    }
                }} 
                    else 
                       {
                           $resultpost1 = array();
                           
                       }
            
                }
                 if($vid=="36")
                    {
                         
                        $querys1 = $this->db->query("SELECT spa_branch.branch_name,spa_branch.user_id as branch_fit_id, spa_branch.branch_image, spa_branch.branch_phone, spa_branch.branch_address, spa_branch.pincode, spa_branch.state, spa_branch.city,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name,booking_master.amt, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time,booking_master.trainer_package_id,booking_master.trainer_id, booking_master.joining_date FROM booking_master
                                                 INNER JOIN spa_branch ON booking_master.branch_id=spa_branch.id
                                                 WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='36' and booking_master.booking_id='$invoice_no' ORDER BY booking_master.id DESC");
                    $count1 = $querys1->num_rows();
                    if ($count1 > 0) 
                       {
                          
                        foreach ($querys1->result_array() as $row1) {
                        $package_id = $row1['package_id'];
                        $trainer_package_id=$row1['trainer_package_id'];
                        $trainer_id=$row1['trainer_id'];
                    
                    
                    
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
                    $package_price = $row1['amt'];
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
                   // $order_date= $trail_booking_date . " ". $trail_booking_time;
                   $booking_date1 = date('j M Y  h:i A', strtotime($row1['booking_date']));
                     $order_date1 = date('l j M Y h:i A', strtotime($booking_date1));
                       $trainer_details = array();
                      
                       $traind=explode(",",$trainer_id);
                       if(count($traind) > 1)
                       {
                          $trainer_id_new = implode("','",$traind);
                       }
                       else
                       {
                           $trainer_id_new=$trainer_id;
                       }
                        
            $personal_trainer =$this->db->query("SELECT * from personal_trainers  where id IN ('$trainer_id_new')"); 
            $count = $personal_trainer->num_rows();
            if ($count > 0) 
            {
                foreach ($personal_trainer->result_array() as $row122) 
                {
                    $id1 = $row122['id'];
                    $manager_name1 = $row122['manager_name'];
                    $qualifications1 = $row122['qualifications'];
                    
                    $fitness_trainer_pic1 = $row122['fitness_trainer_pic'];
                    $gender1 = $row122['gender'];
                    $contact1 =$row122['contact'];
                    $rating="";
                    $language = $row122['language'];
                    
                    $personal_trainer_lanaguge =$this->db->query("SELECT * from languages  where id IN ('$language')"); 
                    $row122_lang=$personal_trainer_lanaguge->row_array();
                    $language_name=$row122_lang['name'];
                    
                    
                    $trainer_details[] = array(
                                               'id'=> $id1,
                                               'trainer_name' => $manager_name1,
                                               'contact' => $contact1,
                                               'qualifications' => $qualifications1,
                                               
                                               'trainer_pic' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$fitness_trainer_pic1,
                                               'gender' => $gender1,
                                               'rating' => $rating,
                                               'language'=>$language_name,
                                              );
                                      
                    
                }
                    
            }
            else {
               $trainer_details=array();
            }

                    $resultpost11[] = array(
                        'booking_id' => $booking_id,
                        "invoice_no" => $invoice_no,
                        'branch_name' => $branch_name,
                        'branch_id' => $branch_id,
                        'listing_id' => $listing_id,
                        'trainer_details' => $trainer_details,
                        
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
                        'order_date'=>$order_date1,
                        
                        'joining_date' => $joining_date,
                        'book_for_date' => $book_for_date,
                        'booking_date' => $booking_date,
                        'status' => $status,
                        'listing_type'=>$row1['vendor_id'],
                        "image"=>$branch_image,
                    );
                    
                   
                }
                           
                       } 
                    else
                    {
                        $resultpost11=array();
                    }
            
                }
                
                if($vid=="12")
                {
                       $query3 = $this->db->query("SELECT booking_master.*,health_record.patient_name,nursing_attendant.address,nursing_attendant.city,nursing_attendant.state,nursing_attendant.contact,nursing_attendant.pincode,nursing_booking_details.* FROM booking_master  LEFT JOIN users ON (booking_master.user_id = users.id) LEFT JOIN health_record ON(booking_master.patient_id=health_record.id) LEFT JOIN nursing_attendant ON(booking_master.listing_id=nursing_attendant.user_id) LEFT JOIN nursing_booking_details ON(booking_master.booking_id=nursing_booking_details.booking_id) where booking_master.user_id = '$user_id' and booking_master.vendor_id = '12' and booking_master.booking_id='$invoice_no' ORDER BY booking_master.id DESC");
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
                   if($status == null){$status = "";}if($listing_id== null){ $listing_id="";}if($booking_id== null){$booking_id="";} if($Nursing_id== null){$Nursing_id="";} 
             if($patient_name== null){  $patient_name="";}if($package_id== null){  $package_id="";} if($package_name== null){  $package_name="";} 
             if($package_amount== null){$package_amount="";}if($book_type== null){$book_type="";}if($patiente_condition== null){ $patiente_condition="";}
             if($attendent_time== null ){ $attendent_time="";}if($attendant_hour== null){ $attendant_hour="";}if($tentative_intime== null){ $tentative_intime="";}
             if($nursing_gender== null){$nursing_gender="";} if($attendant_needed== null)
                    {
                 
                      
                      $attendant_needed="";
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
                    $order_date= $joining_date ." ".$tentative_intime;
                     $order_date1 = date('l j M Y h:i A', strtotime($order_date));
                    
                     $new_image='';
                     if(empty($package_image))
                     {
                         $new_image='';
                     }
                     else
                      {
                          $new_image="https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$package_image;
                      }
                    $resultpost3[] = array(
                        'listing_id' => $listing_id,
                        "invoice_no" => $invoice_no,
                        'booking_id' => $booking_id,
                        'nursing_id' => $Nursing_id,
                        'appointment_date' => $booking_date,
                        'patient_name' => $patient_name,
                        'package_id' => $package_id,
                        'package_name' => $package_name,
                        'package_amount' => $package_amount,
                        'package_image' => $new_image,
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
                        'order_date'=>$order_date1,
                      
                        'status' => $status,
                        'listing_type'=>$row3['vendor_id'],
                        'image'=>$new_image
                    );
                    
                  
                    
                 
                }
                        } else {
                            $resultpost3 = array();
                        }
                }
                if($vid=="5")
                    {
                     
           $query2 = $this->db->query("select doctor_booking_master.booking_id, doctor_booking_master.booking_date,doctor_booking_master.listing_id,doctor_booking_master.user_id, doctor_booking_master.listing_id,doctor_booking_master.clinic_id, doctor_prescription.id as prescription_id, doctor_booking_master.booking_time,doctor_booking_master.status,doctor_booking_master.consultation_type, users.name as patient_name,dl.doctor_name from doctor_booking_master LEFT JOIN doctor_prescription ON (doctor_booking_master.booking_id = doctor_prescription.booking_id AND doctor_booking_master.listing_id = doctor_prescription.doctor_id AND doctor_booking_master.clinic_id = doctor_prescription.clinic_id) LEFT JOIN users ON (doctor_booking_master.user_id = users.id) LEFT JOIN doctor_list as dl ON(dl.user_id=doctor_booking_master.listing_id) where doctor_booking_master.user_id = '$user_id' and doctor_booking_master.booking_id='$invoice_no' and doctor_booking_master.vendor_type='5' ORDER BY doctor_booking_master.id DESC");

            $count2 = $query2->num_rows();
            if ($count2 > 0) {
                foreach ($query2->result_array() as $row2) {
                 
                    $booking_id = $row2['booking_id'];
                    $booking_date = $row2['booking_date'];
                    $booking_time = $row2['booking_time'];
                    $patient_name = $row2['patient_name'];
                    $doctor_name = $row2['doctor_name'];
                    $clinic_id=$row2['clinic_id'];
                    $doctor_id=$row2['listing_id'];
                    $consultation_type = $row2['consultation_type'];
                    $consultation_charges = "";
                    $branch_name = "";
                    $address = "";
                    $image = "";
                    $clinic_contact_no = "";  
                    
                    
                    $sql21             = "SELECT  * from doctor_clinic WHERE id='$clinic_id' and doctor_id='$doctor_id'";
                    $query_practices1  = $this->db->query($sql21);
                    $total_practices1  = $query_practices1->num_rows();
                    if($total_practices1 > 0)
                    {
                      $get_pract=$query_practices1->row_array(); 
                        $consultation_charges = $get_pract['consultation_charges'];
                        $branch_name = $get_pract['clinic_name'];
                        
                        $address = $get_pract['address'] . "," . $get_pract['city'] . "," . $get_pract['state'] . "," . $get_pract['pincode'];
                        $image = $get_pract['image'];
                        $clinic_contact_no = $get_pract['contact_no'];
                         if ($image != '') {
                            $clinic_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $image;
                        } else {
                            $clinic_image = '';
                        }
                    }
                    else
                    {
                       $consultation_charges = "0";
                        $sql0 = sprintf("SELECT hospital_doctor_id FROM doctor_list WHERE user_id='$doctor_id'");
                    $query_practices0  = $this->db->query($sql0);
                    $total_practices0  = $query_practices0->num_rows();
                    $get_pract0=$query_practices0->row_array();
                    $ids=$get_pract0['hospital_doctor_id'];  
                    if(!empty($ids))
                       {
                      $sql21             = "SELECT hd.*,hd.consultation from hospital_doctor_list as hd LEFT JOIN hospitals as h on (h.user_id = hd.hospital_id ) WHERE hd.id IN ($ids) and h.user_id ='$clinic_id' ";
                       //$query_dr           = $this->db->query("SELECT doctor_clinic.clinic_name,doctor_list.doctor_name FROM hospitals INNER JOIN doctor_list ON doctor_list.=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id' AND doctor_clinic.doctor_id='$listing_id'");
                        $query_practices0  = $this->db->query($sql21);
                         $count_user         = $query_practices0->num_rows();
                         if($count_user > 0)
                         {
                            
                    $get_pract01=$query_practices0->row_array();
                            $consultation_charges = $get_pract01['consultation'];
                         }
                       } 
                        
                      $sql21             = "SELECT  * from hospitals WHERE user_id='$clinic_id'";
                    $query_practices1  = $this->db->query($sql21);
                    $total_practices1  = $query_practices1->num_rows();
                    $get_pract=$query_practices1->row_array(); 
                    
                    $branch_name = $get_pract['name_of_hospital'];
                    $address = $get_pract['address'] . "," . $get_pract['city'] . "," . $get_pract['state'] . "," . $get_pract['pincode'];
                    $image = $get_pract['image'];
                    $clinic_contact_no = $get_pract['phone'];  
                     if ($image != '') {
                            $clinic_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $clinic_image = '';
                        }
                    }
                    $prescription_id = $row2['prescription_id'];
                    $status = $row2['status'];
                    $doctor_id = $row2['listing_id'];
                    
                   /* $booking_time = str_replace('PM','', $booking_time);
                    $booking_time = str_replace('AM','', $booking_time);
                    */
                    $booking_time1 = explode('-',$booking_time);
                    $new =$booking_time1[0];
                    $order_date= $booking_date ." ".$new;
                      if($status == null)
                    {
                        $status = "";
                    }
                     //echo $trimmed ;
                     if($prescription_id!="")
                     {
                    $url="https://doctor.medicalwale.com/prescription/".$prescription_id.".pdf";
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
                        "invoice_no" => $invoice_no,
                        'doctor_id' => $doctor_id,
                        'order_date' => date('l j M Y h:i A', strtotime($order_date)),
                       
                        'patient_name' => $patient_name,
                        'doctor_name' => $doctor_name,
                        'consultation_charge' => $consultation_charges,
                        'consultation_type' => $consultation_type,
                        'clinic_name' => $branch_name,
                        'clinic_address' => $address,
                        'image' => $clinic_image,
                        'clinic_contact_no' => $clinic_contact_no,
                        'status' => $status,
                        'prescription_id' => $prescription_id,
                        'prescription_pdf'=> $url,
                        'listing_type'=>"5",
                        'image'=>$clinic_image
                    );
                }
            } else {
                $resultpost2 = array();
            }
            
                    }
               if($vid=="10" || $vid=="31")
                    {
                       $count_query4 = $this->db->query("SELECT lb.*,bm.vendor_id,bm.status from booking_master as bm LEFT JOIN lab_booking_details as lb ON(bm.booking_id = lb.booking_id) where bm.user_id='$user_id' and bm.booking_id ='$invoice_no' and (bm.vendor_id='10' or bm.vendor_id='31') order by bm.id desc");
                        $lab_booked4       = $count_query4->num_rows();
                        if ($lab_booked4 > 0) 
                        {
                          foreach ($count_query4->result_array() as $Lbooked) 
                            {
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
                                    $book_query = $this->db->query("SELECT status FROM booking_master WHERE booking_id='$bk_id'");
                                     $conff=$user_query->num_rows();
                                     if($conff>0){
                                    $status = $book_query->row()->status;
                                }
                                     else{
                                    $status = '';
                                }
                                     $report_path ='';
                                     $book_query_path = $this->db->query("SELECT report_path FROM reports WHERE booking_id='$bk_id'");
                                     $report_path_count =  $book_query_path->num_rows();
                                     if($report_path_count > 0)
                                       {
                                       $report_path = $book_query_path->row()->report_path;
                                 }
                                     if($Lbooked['reference_id']!='')
                                       {
                                           $order_date1= $Lbooked['booking_date'] ." ".$Lbooked['booking_time'];
                                      $order_date = date('l j M Y h:i A', strtotime($order_date1)); 
                                    
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
                "invoice_no" => $invoice_no,
                'reference_id'=> $Lbooked['reference_id'],
                'lead_id'=> $Lbooked['lead_id'],
                'status'=>$status,
                'report_path'=>$report_path,
                'order_date'=>$order_date,
                
                'image'=>'',
                "listing_type"=> "31"
                );
                                 }
                                   }  
                                else
                                 {
                                   
                                $user_id = $Lbooked['user_id'];
                                  $user_query = $this->db->query("SELECT email,phone,name FROM users WHERE id='$user_id'");
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
                                $order_date1= $Lbooked['booking_date'] ." ".$Lbooked['booking_time'];
                                $order_date = date('l j M Y h:i A', strtotime($order_date1)); 
                                
                                
                              
                                $Booed_test_list = array();
                                $amount=0;
                                $lab_pack_name = "";
                                $pack_details = "";
                                $pack_amount = "";
                                
              
                                if($status == null)
                                {
                                    $status = "";
                                }
                                
        $prescription_list = array(); 
        $lab_prescriptions = $Lbooked['prescriptions'];  
        if(!empty($lab_prescriptions)){
            $lab_prescriptions = explode(',',$lab_prescriptions);
            foreach ($lab_prescriptions as $prescription_img) {
                $prescription_list[] = array(
                    'image'=>'https://d2c8oti4is0ms3.cloudfront.net/images/lab_prescription/'.$prescription_img
                );
            }
            $branch_name='Medicalwale Labs';
        }                        
                                
        if(empty($branch_name)){
            $row_ = $this->db->query("SELECT lab_name,profile_pic FROM lab_center where user_id='$listing_id'")->row_array();
            $branch_name=$row_['lab_name'];
            $image = $row_['profile_pic'];
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
        }
        
        $test_array=array();                     
        $query = $this->db->query("SELECT lt.booking_id,lt.listing_id as vendor_id,lt.vendor_id as listing_type,lt.id,lab_booking_details.address_id,lab_booking_details.created_at,lt.patient_id as member_id,lt.user_id as parent_id,lt.booking_time,ud.address2,ud.address_type,ud.city,ud.address1,ud.full_address,ud.landmark,ud.lat,ud.lng,ud.mobile,ud.name,ud.pincode,ud.relation_ship as relation,ud.state,pm.payment_method,pm.icon,pm.id as payment_id,lab_booking_details.test_id,lab_booking_details.total_cost,lab_booking_details.total_discount,lab_booking_details.amount,user_vendor_ledger.ledger_id,user_vendor_ledger.transaction_id from booking_master lt LEFT JOIN lab_booking_details on lab_booking_details.booking_id=lt.booking_id LEFT JOIN user_address ud on ud.address_id=lab_booking_details.address_id LEFT JOIN payment_method pm on pm.id=lt.payment_mode LEFT JOIN user_vendor_ledger on user_vendor_ledger.invoice_no=lt.booking_id where lt.booking_id='$booking_id' limit 1");
		$num_count  = $query->num_rows();
        if ($num_count > 0) {
        $list = $query->row_array();
        $vendor_id=$list['vendor_id'];
        
        $package_array=array();
        
        $test_query = $this->db->query("SELECT lab_test_master.id,ld.code,ld.report_availability,ld.home_collection_charges,ld.price,ld.discounted_price,ld.discount,ld.medicalwale_discount,lab_test_master.name from lab_test_master_details ld INNER JOIN lab_test_master on lab_test_master.id=ld.test_id INNER JOIN lab_booking_details on FIND_IN_SET(lab_test_master.id,lab_booking_details.test_id) where lab_booking_details.booking_id='$booking_id' and ld.user_id='$vendor_id' group by lab_test_master.id");
        $test_count  = $test_query->num_rows();
                if ($test_count > 0) {
                    $test_row = $test_query->result();
                    foreach ($test_row as $test_list) {
                        $testid  = $test_list->id;
                        $test_name  = $test_list->name;
                        $code  = $test_list->code;
                        $report_availability  = $test_list->report_availability;
                        $home_collection_charges  = $test_list->home_collection_charges;
                        $price  = $test_list->price;
                        $discounted_price  = $test_list->discounted_price;
                        if($discounted_price==0){
                            $discounted_price = $test_list->price;    
                        }
                        $discount  = $test_list->discount;
                        $medicalwale_discount  = $test_list->medicalwale_discount;
                        $test_array[] = array(
                            'type' => 'TEST',
                            'test_id' => $testid,
                            'test_name' => $test_name,
                            'code' => $code,
                            'report_availability' => '',
                            'home_collection_charges' => $home_collection_charges,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'discount' => $discount,
                            'medicalwale_discount' => $medicalwale_discount,
                            'home_delivery' => "0"
                        );
                    }
                }
                
        $test_query = $this->db->query("SELECT ld.id,ld.code,ld.home_collection_charges,ld.price,ld.discounted_price,ld.discount,ld.medicalwale_discount,ld.name from lab_package_master ld INNER JOIN lab_booking_details on FIND_IN_SET(ld.id,lab_booking_details.package_id) where lab_booking_details.booking_id='$booking_id' group by ld.id");
        $test_count  = $test_query->num_rows();
                if ($test_count > 0) {
                    $test_row = $test_query->result();
                    foreach ($test_row as $test_list) {
                        $testid  = $test_list->id;
                        $test_name  = $test_list->name;
                        $code  = $test_list->code;
                        $report_availability  = '';
                        $home_collection_charges  = $test_list->home_collection_charges;
                        $price  = $test_list->price;
                        $discounted_price  = $test_list->discounted_price;
                        if($discounted_price==0){
                            $discounted_price = $test_list->price;    
                        }
                        $discount  = $test_list->discount;
                        $medicalwale_discount  = $test_list->medicalwale_discount;
                        $test_array[] = array(
                            'type' => 'PACKAGE',
                            'test_id' => $testid,
                            'test_name' => $test_name,
                            'package_id' => $testid,
                            'name' => $test_name,
                            'code' => $code,
                            'report_availability' => '',
                            'home_collection_charges' => $home_collection_charges,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'discount' => $discount,
                            'medicalwale_discount' => $medicalwale_discount,
                            'home_delivery' => "0"
                        );
                    }
                }
        
        }                 
                                
                                $resultpost4[] = array(
                                    'user_id'=> $user_id,
                                    'user_name'=>$user_name,
                                    'amount'=>(string)$amount,
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
                                    "invoice_no" => $invoice_no,
                                    'booked_tests'=>$test_array,
                                    'prescription_list' => $prescription_list,
                                    'image'=>$image,
                                    'order_date'=>$order_date,
                                    'status'=>$status,
                                    "listing_type"=> "10"
                                );
                     }
                            }       
                        }
                       else
                        {
                         $resultpost4 = array(); 
                        }
                    }    
                if($vid=="34")
                   {
                       
                        $pro_list = array();
                        $results = $this->db->query("SELECT vd.delivery_by_medicalwale, uo.* FROM `user_order` as uo left join vendor_details_hm as vd on (uo.`listing_id` = vd.v_id) WHERE uo.`user_id` = '$user_id' AND  uo.`listing_type`='34'  and uo.invoice_no='$invoice_no' ORDER BY order_id DESC");
                      
                        foreach($results->result_array() as $order){
                            $products = $this->db->query("SELECT * FROM `orders_details_hm` WHERE `order_id` = ".$order['order_id']." AND `user_id` = '$user_id'");
                            $details = $products->result_array();
                           // print_r($products->result_array());die;
                           $order_date = $order['order_date'];
                           $order_date1 = date('l j M Y h:i A', strtotime($order_date));
                           
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
                             $order += ['image'=>""];
                            
                            foreach($order as $key => $value){
                                if (array_key_exists("order_date",$order))
                                          {
                                            $order['order_date']=$order_date1;
                                          }
                                    if($value == null){
                                        $order[$key] = "";
                                    }
                                }
                                
                            $resultpost7[] = $order;
                        }
                   }
                if($vid=="8")
                {
                     
       // IPD
       
       $booking_details = $this->db->query("SELECT * FROM booking_master WHERE user_id='$user_id' and vendor_id='8' and booking_id='$invoice_no' order by id DESC");
       
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
              $ward_id='';
              if($hospital_count>0)
              {
             $ward_id = $hospital_details->row()->ward_id;
             $amount = $hospital_details->row()->amount;
             $patient_preferred_date = $hospital_details->row()->patient_preferred_date;
              }
              $room_type ='';
            $capacity ='';
            $price ='';
            if(empty($ward_id) || $ward_id !="0"){
              $ward_details = $this->db->query("SELECT * FROM hospital_wards WHERE hospital_id='$listing_id'");
              $ward_count = $ward_details->num_rows();
                   
              if($ward_count>0)
              {
             $room_type = $ward_details->row()->room_type;
             $capacity = $ward_details->row()->capacity;
             $price =  $ward_details->row()->price;
              }
                    }
              
              $pack_details = $this->db->query("SELECT * FROM hospital_packages WHERE id='$package_id'");
              $pack_count = $pack_details->num_rows();
            
              if($pack_count>0)
              {
             $package_name = $pack_details->row()->package_name;
              }
              else
              {
                $package_name="";  
              }
              
              
              
             
              $patient_id = $row['patient_id'];
              
              $patient_id = $row['patient_id'];
              $patient_detaild = $this->db->query("SELECT name,phone,email,gender FROM users WHERE id='$patient_id' ");
              $patient_count = $patient_detaild->num_rows();
             
              if($patient_count>0)
              {
                $name = $patient_detaild->row()->name;
                $phone = $patient_detaild->row()->phone;
                $email = $patient_detaild->row()->email;
                if(empty($email))
                {
                   $email=""; 
                }
                $gender = $patient_detaild->row()->gender;
                if(empty($gender))
                {
                   $gender=""; 
                }
              }
              else
              {
                $name = "";
                $phone = "";
                $email = "";
                $gender = "";  
              }
              
              $user_id = $row['user_id'];
             
              $name = $name;
             $phone =$phone;
             $email = $email;
             $gender = $gender;
              
              
             
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
             $order_date1 = $booking_date . " ".$booking_time;
             $order_date = date('l j M Y h:i A', strtotime($booking_date));
            
            $resultpost61[] = array(
                    'id' => $id,
                    'booking_id' => $booking_id,
                    "invoice_no" => $invoice_no,
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
                    'order_date'=>$order_date,
                    
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
                    'image'=>'',
                    'booking_type'=>"IPD",
                    "listing_type"=> "8"
                );
            
           }
        }
        else
        {
            
        }
        
       // OPD
        
                   
                }
                
                if($vid=="50")
                {
                      $query= $this->db->query("select hospital_booking_master.booking_id,hospital_booking_master.booking_date,hospital_booking_master.listing_id,hospital_booking_master.user_id,hospital_booking_master.listing_id,
hospital_booking_master.doctor_id,hospital_doctor_prescription.id as prescription_id,hospital_booking_master.booking_time,hospital_booking_master.status,hospital_booking_master.consultation_type,
hospitals.name_of_hospital,users.name as patient_name, hospitals.address,hospitals.image,hospitals.state,hospitals.city,hospitals.pincode, dl.consultation,hospitals.phone,dl.doctor_name from hospital_booking_master  LEFT JOIN hospital_doctor_prescription 
ON (hospital_booking_master.patient_id = hospital_doctor_prescription.patient_id AND hospital_booking_master.doctor_id = hospital_doctor_prescription.doctor_id AND hospital_booking_master.listing_id = hospital_doctor_prescription.hospital_id) LEFT JOIN hospitals on (hospital_booking_master.listing_id = hospitals.user_id)
LEFT JOIN users ON (hospital_booking_master.user_id = users.id)  LEFT JOIN hospital_doctor_list as dl ON(dl.id=hospital_booking_master.doctor_id)
where hospital_booking_master.user_id = '$user_id' and hospital_booking_master.booking_id='$invoice_no'  ORDER BY hospital_booking_master.id DESC");

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
                       if($image !='')
                    {
                        $clinic_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                    }
                    else
                    {
                        $clinic_image="";
                    }
                       if($prescription_id == null)
                    {
                        $prescription_id = "";
                    }
                       if($clinic_contact_no == null)
                    {
                        $clinic_contact_no = "";
                    }
                    $booking_time1 = explode('-',$booking_time);
                    $new =$booking_time1[0];
                    $order_date1= date("Y-m-d",strtotime($booking_date)) ." ".$new;

             $order_date = date('l j M Y h:i A', strtotime($order_date1));
             
                    $resultpost62[] = array(
                        'booking_id' => $booking_id,
                        'doctor_id' => $doctor_id,
                        'order_date' => $order_date,
                        "invoice_no" => $invoice_no,
                        'patient_name' => $patient_name,
                        'doctor_name' => $doctor_name,
                        'consultation_charge' => $consultation_charges,
                        'consultation_type' => $consultation_type,
                        'clinic_name' => $branch_name,
                        'clinic_address' => $address,
                        'image' => $clinic_image,
                        'clinic_contact_no' => $clinic_contact_no,
                        'status' => $status,
                        'prescription_id' => $prescription_id,
                        
                        'booking_type'=>"OPD",
                         "listing_type"=> "50"
                    );
                }
            }
                }
                
                if($vid=="45")
                {
                   $query = $this->db->query("SELECT * from life_saving_drugs where user_id='$user_id' and invoice_no='$invoice_no' order by id desc");
  
                $count = $query->num_rows();
                 if ($count > 0)
                 { 
            foreach ($query->result_array() as $row) 
            {
                
               
                $member_id = $row['member_id'];
               
                $qty = $row['qty'];
                $urgent = $row['urgent'];
                $mobile = $row['mobile'];
                $email = $row['email'];
                $image = $row['image'];
                $invoice_no = $row['invoice_no'];
                $order_status = $row['order_status'];
                $action_by = $row['action_by'];
                $updated_at = $row['created_at'];
                $cancel_reason = $row['cancel_reason'];
                if(empty($cancel_reason))
                {
                    $cancel_reason='';
                }
                if(empty($updated_at))
                {
                    $updated_at='';
                }
                
                $query1 = $this->db->query("SELECT * from users where id='$member_id'");
                $row1=$query1->row_array();
            if(empty($manufacturer))
            {
                $manufacturer="";
            }
            if(empty($mrp))
            {
                $mrp="";
            }
            
             $order_date = date('l j M Y h:i A', strtotime($updated_at));
            
             $name=$row1['name'];
             if(empty($name))
             {
                 $name1='';
             }
             else
             {
                 $name1=$name;
             }
                $resultpost8[] = array(
                    "order_id" => $invoice_no,
                    "medlife_order_id" => "",
                    "delivery_time" => "",
                    "order_type" => "Life Saving Drug",
                    "listing_id" => "",
                    "listing_name" => "",
                    "listing_type" => "45",
                    "listing_payment_mode" => "",
                    "invoice_no" => $invoice_no,
                    "chat_id" => "",
                    "address_id" => "",
                    "name" => $name1,
                    "mobile" => $mobile,
                    "pincode" => "",
                    "address1" => "",
                    "address2" => "",
                    "landmark" => "",
                    "city" => "",
                    "state" => "",
                    "user_name" => $name1,
                    "user_mobile" => $mobile,
                    "user_email" => $email,
                    "order_total" => 0,
                    "order_discount"=>0,
                    "payment_method" => "",
                    "order_date" => $order_date,
                   
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    "delivery_charge" => "",
                    "product_order" => array(),
                    "tracker" => array(),
                    "prescription_create" => array(),
                    "prescription_order" => array(),
                    "action_by" => "",
                    "rxid" => "",
                    "is_cancel" => "",
                    "profile_img"=>"https://d2c8oti4is0ms3.cloudfront.net/images/Check_In_Image/".$image,
                    "life_qty" => $qty,
                    "image"=>"https://d2c8oti4is0ms3.cloudfront.net/images/Check_In_Image/".$image,
                    "urgent"=>$urgent
                  
                );
               
               }
             
            }
         else {
            $resultpost8 = array();
        }
                }
                if($vid=="21")
                    {
                     
           $query2 = $this->db->query("select * from rides where user_id = '$user_id' and ride_id='$invoice_no' ORDER BY ride_id DESC");

            $count2 = $query2->num_rows();
            if ($count2 > 0) {
                foreach ($query2->result_array() as $row2) {
                 
                    $booking_id = $row2['ride_id'];
                    $booking_date = $row2['Create_time'];
                    $status = $row2['status'];
                    $patient_name = $row2['user_name'];
                    $patient_mobile = $row2['user_mobile'];
                    $payment_details = "Cash At Point";
                    $from = $row2['pickup_adress'];
                    $to = $row2['drop_address'];
                    $driver_name = $row2['driver_name'];
                    $driver_mobile = $row2['driver_mobile'];
                    $ambulance_type = $row2['subtype_name'];
                    $price = $row2['price'];
                    $discount = "0";
                    $total=$row2['price'];
                    $dirver_id=$row2['driver_id'];
                     $amb="";
                    $query1 = $this->db->query("select * from ambulance_fare where driver_user_id = '$dirver_id'");
                    $count1 = $query1->num_rows();
                    if ($count1 > 0) {
                        $row1=$query1->row_array();
                        $amb=$row1['amb_no'];
                    }
                    else
                    {
                        $amb="";
                    }
                        
                    $resultpost234[] = array(
                        'booking_id' => $booking_id,
                        "invoice_no" => $invoice_no,
                        'order_date' => date('l j M Y h:i A', strtotime($booking_date)),
                        'status' => $status,
                        'dirver_id' => $dirver_id,
                        'patient_name' => $patient_name,
                        'patient_mobile' => $patient_mobile,
                        'payment_type' => $payment_details,
                        'from' => $from,
                        'to' => $to,
                        'driver_name' => $driver_name,
                        'driver_mobile' => $driver_mobile,
                        'ambulance_no' => $amb,
                        
                        'ambulance_type' => $ambulance_type,
                        'price'=> $price,
                        'discount'=>$discount,
                        'listing_type'=>"21",
                        'total_amount'=>$price,
                        'image'=>""
                    );
                }
            } else {
                $resultpost234 = array();
            }
            
                    }
                    
                
                    if($vid=="39")
                    {
                         
                        // $querys1 = $this->db->query("SELECT dentists_branch.name_of_hospital, dentists_branch.id as branch_id ,dentists_branch.dentists_branch_user_id as branch_dentist_id, dentists_branch.image, dentists_branch.phone, dentists_branch.address, dentists_branch.pincode, dentists_branch.state, dentists_branch.city,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time,booking_master.trainer_package_id,booking_master.trainer_id, booking_master.joining_date FROM booking_master LEFT JOIN dentists_branch ON booking_master.branch_id=dentists_branch.id
                        //                          WHERE booking_master.user_id='$user_id' and booking_master.booking_id='$invoice_no' ORDER BY booking_master.id DESC");
                           
                        /*$querys1 = $this->db->query("SELECT dentists_branch.name_of_hospital, dentists_branch.id as branch_id ,dentists_branch.dentists_branch_user_id as branch_dentist_id, dentists_branch.image, dentists_branch.phone, dentists_branch.address, dentists_branch.pincode, dentists_branch.state, dentists_branch.city,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time,booking_master.trainer_package_id,booking_master.trainer_id, booking_master.joining_date FROM booking_master LEFT JOIN dentists_branch ON ( booking_master.listing_id=dentists_branch.hub_user_id and dentists_branch.dentists_branch_user_id )
                                                 WHERE booking_master.user_id='$user_id' and booking_master.booking_id='$invoice_no' ORDER BY booking_master.id DESC");
                                                 */
                                                 
                                                 
                                                 
                        $querys1 = $this->db->query("SELECT booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time,booking_master.trainer_package_id,booking_master.trainer_id, booking_master.joining_date FROM booking_master WHERE booking_master.user_id='$user_id' and booking_master.booking_id='$invoice_no' ORDER BY booking_master.id DESC");
                                                                          
                                                
                                                // echo "SELECT dentists_branch.name_of_hospital, dentists_branch.id as branch_id ,dentists_branch.dentists_branch_user_id as branch_dentist_id, dentists_branch.image, dentists_branch.phone, dentists_branch.address, dentists_branch.pincode, dentists_branch.state, dentists_branch.city,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time,booking_master.trainer_package_id,booking_master.trainer_id, booking_master.joining_date FROM booking_master INNER JOIN dentists_branch ON booking_master.branch_id=dentists_branch.id
                                               //  WHERE booking_master.user_id='$user_id' and booking_master.booking_id='$invoice_no' ORDER BY booking_master.id DESC"; echo "</br></br></br>";
                                                 
                    $count1 = $querys1->num_rows();
                    if ($count1 > 0) 
                       {
                          
                        foreach ($querys1->result_array() as $row1) {
                            
                        //    print_r($row1);
                        $listing_id = $row1['listing_id'];
                        $branch_id = "";
                        $branch_name = "";
                        $branch_image = "";
                        $branch_phone = "";
                        $branch_address = "";
                        $branch_pincode = "";
                        $branch_city = "";
                        $branch_state = "";
                        
                        
                        $query192 = $this->db->query("SELECT dentists_branch.name_of_hospital, dentists_branch.id as branch_id ,dentists_branch.dentists_branch_user_id as branch_dentist_id, dentists_branch.image, dentists_branch.phone, dentists_branch.address, dentists_branch.pincode, dentists_branch.state, dentists_branch.city from dentists_branch where (hub_user_id='$listing_id' OR dentists_branch_user_id='$listing_id') ");
                        $row192=$query192->row_array();    
                        $count12 = $query192->num_rows();
                        if ($count12 > 0) 
                           {
                                $branch_id = $row192['branch_id'];
                                $branch_name = $row192['name_of_hospital'];
                                $branch_image = $row192['image'];
                                $branch_phone = $row192['phone'];
                                $branch_address = $row192['address'];
                                $branch_pincode = $row192['pincode'];
                                $branch_city = $row192['city'];
                                $branch_state = $row192['state'];
                           } 
                           
                         $query1921 = $this->db->query("SELECT dentists_clinic_list.name_of_hospital,dentists_clinic_list.image, dentists_clinic_list.phone, dentists_clinic_list.address, dentists_clinic_list.pincode, dentists_clinic_list.state, dentists_clinic_list.city from dentists_clinic_list where user_id='$listing_id' ");
                        $row1921=$query1921->row_array();    
                        $count123 = $query1921->num_rows();
                        if ($count123 > 0) 
                           {
                                $branch_id = "";
                                $branch_name = $row1921['name_of_hospital'];
                                $branch_image = $row1921['image'];
                                $branch_phone = $row1921['phone'];
                                $branch_address = $row1921['address'];
                                $branch_pincode = $row1921['pincode'];
                                $branch_city = $row1921['city'];
                                $branch_state = $row1921['state'];
                           }    
                        $package_id = $row1['package_id'];
                        $trainer_package_id=$row1['trainer_package_id'];
                        $trainer_id=$row1['trainer_id'];
                    
                    
                    $package_name = "";
                    $package_details = "";
                    $query12 = $this->db->query("SELECT packages.package_name, packages.package_details, packages.price from packages  where packages.id ='$package_id' ");
                     $row12=$query12->row_array();
                    $count1234 = $query12->num_rows();
                    if ($count1234 > 0) 
                    {
                       $package_name = $row12['package_name'];
                    $package_details = $row12['package_details']; 
                    }
                    $booking_id = $row1['booking_id'];
                    
                   
                    $appointment_user_name = $row1['user_name'];
                    $appointment_user_mobile = $row1['user_mobile'];
                    $appointment_user_email = $row1['user_email'];
                    $category_id = $row1['category_id'];
                    $status = $row1['status'];
                    
                //    $package_price = $row1['amt'];
                    $trail_booking_date = $row1['trail_booking_date'];
                    $trail_booking_time = $row1['trail_booking_time'];
                    $joining_date = date('j M Y', strtotime($row1['joining_date']));
                    $book_for_date = date('d/m/Y', strtotime($row1['joining_date']));
                    $is_free_trial = 'No';
                    date_default_timezone_set('Asia/Kolkata');
                  
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
                   // $order_date= $trail_booking_date . " ". $trail_booking_time;
                   $booking_date1 = date('j M Y  h:i A', strtotime($row1['booking_date']));
                     $order_date1 = date('l j M Y h:i A', strtotime($booking_date1));
                


                    $resultpost12[] = array(
                        'booking_id' => $booking_id,
                        'branch_name' => $branch_name,
                        'branch_id' => $branch_id,
                        'listing_id' => $listing_id,
                        "invoice_no" => $invoice_no,
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
                       // 'package_price' => $package_price,
                        'is_free_trial' => $is_free_trial,
                        'order_date'=>$order_date1,
                        
                      //  'joining_date' => $joining_date,
                        //'book_for_date' => $book_for_date,
                        'booking_date' => $booking_date,
                        'status' => $status,
                        'listing_type'=>$row1['vendor_id'],
                        "image"=>$branch_image,
                    );
                    
                   
                }
                      // print_r($resultpost12);    
                       } 
                    else
                    {
                        $resultpost12=array();
                    }
            
                }    
                    
                
                }
                
            //print_r($resultpost62);
                $final_array=array_merge($resultpost,$resultpost1, $resultpost2,$resultpost3,$resultpost4,$resultpost5,$resultpost7,$resultpost8,$resultpost61,$resultpost62,$resultpost234,$resultpost11,$resultpost12);




function cmp($a, $b)
{
    return strcmp($b["invoice_no"], $a["invoice_no"]);
}
usort($final_array, "cmp");
                $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'total_count'=>(string)$count12,
                            'data'=>$final_array
                        );
            }
            
             else
             {
                  $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'total_count'=>'0',
                            'data'=>array()
                        );
                
             }
                
             return $resp;
             
    }    
    
    
    
    
   
   public function all_booking_details_v2($user_id,$page,$vid)
    {
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        $resultpost = array();
     
                    
                    
                if($vid=="13" || $vid=="38" || $vid=="45" || $vid=="44")
                    {
                      $order_amount = $gst_rs = $gst_per = $chc = $grand_total = "";
                      $dis = 0;
                        /*Pharmacy*/
                        $query = $this->db->query("SELECT user_order.*,medical_stores.medical_name,medical_stores.profile_pic FROM user_order LEFT JOIN medical_stores on user_order.listing_id=medical_stores.user_id WHERE (user_order.listing_type='13' or user_order.listing_type='38' ) AND user_order.user_id='$user_id' group by user_order.invoice_no order by user_order.order_date DESC  LIMIT $start, $limit ");
                        $count = $query->num_rows();
                        if ($count > 0) 
                           { 
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
                $order_date = date('l j M Y h:i A', strtotime($order_date));
                $delivery_charge = $row['delivery_charge'];
                $order_status = $row['order_status'];
                $order_type = $row['order_type'];
                $action_by = $row['action_by'];
                $rxId = $row['rxId'];
                
                /*Added by swapnali on 20th nov 2k19 at 5pm*/
                $delivery_charge = $row['delivery_charge'];
                $delivery_charges_by_customer = $row['delivery_charges_by_customer'];
                $order_status = $row['order_status'];
                $order_type = $row['order_type'];
                $action_by = $row['action_by'];
                $rxId = $row['rxId'];
                
                if($vid=="13" || $vid=="44"){
                    $delivery_charge = $delivery_charges_by_customer;
                } 
                
                $dis = $row['discount'];
                $gst_per = $row['gst'];
                $chc = strval($row['chc']);
                $order_amount = strval($row['actual_cost']);
                $gst_rs = strval(($order_amount - $dis)  * ($gst_per / 100));
                $grand_total = strval($order_amount + $gst_rs + $chc - $dis + $delivery_charge); 
                /*Added by swapnali on 20th nov 2k19 at 5pm => END*/
                
                
               //added by zak for maintain medlife cancel order 
                  if ($row['profile_pic'] != '') {
                    $profile_pic = $row['profile_pic'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
                }
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
                            $disc = (($product_price * $product_quantity)*$product_discount)/100;
                            $sub_total_discount +=$disc;
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
                                "product_discount" => $disc,
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
                                $desc1 = (($prescription_price*$prescription_quantity)*$prescription_discount)/100;
                                $sub_total_discount1 += $desc1;
                                $prescription_result[] = array(
                                    "prescription_name" => $prescription_name,
                                    "prescription_quantity" => $prescription_quantity,
                                    "prescription_price" => $prescription_price,
                                    "prescription_discount" => $desc1,
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
            $tracker = $this->All_booking_model->pharmacy_tracker($user_id, $invoice_no);
            
             if($listing_type=="38")
               {
                   if(!empty($rxId) )
                {
                $resultpost4[] = array(
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
                    "user_email" => "",
                    "order_total" => $order_total,
                    "order_discount"=>$order_total_discount,
                    "gst_rs"=> $gst_rs,
                    "gst_per"=> $gst_per,
                    "chc"=> $chc,
                    "grand_total"=> $grand_total,
                    "payment_method" => $payment_method,
                    "order_date" => $order_date,
                    "image"=>$profile_pic,
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    "delivery_charge" => $delivery_charge,
                    "product_order" => $product_resultpost,
                    "tracker" => $tracker,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result,
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel,
                    "life_qty"=>"",
                    "urgent"=>"",
                    "image"=>$profile_pic
                );
                }
                
               }
               else
               {
                   $resultpost4[] = array(
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
                    "user_email" => "",
                    "order_total" => $order_amount,
                    "order_discount"=>intval($dis),
                    "gst_rs"=> $gst_rs,
                    "gst_per"=> $gst_per,
                    "chc"=> $chc,
                    "grand_total"=> $grand_total,
                    "payment_method" => $payment_method,
                    "order_date" => $order_date,
                    "image"=>$profile_pic,
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    "delivery_charge" => $delivery_charge,
                    "product_order" => $product_resultpost,
                    "tracker" => $tracker,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result,
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel,
                    "life_qty"=>"",
                    "urgent"=>"",
                    "image"=>$profile_pic
                ); 
               }
            }
       } 
                        else 
                        {
                            
                            
                            $resultpost4 = array();
                        }
                        
                        /*Medlife orders*/
                        
                        $query1 = $this->db->query("SELECT * from life_saving_drugs where user_id='$user_id' order by created_at DESC  LIMIT $start, $limit");
                         $count1 = $query1->num_rows();
                           if ($count1 > 0)
                              { 
                                foreach ($query1->result_array() as $row) 
                                        {
                                            $member_id = $row['member_id'];
                                            $qty = $row['qty'];
                                            $urgent = $row['urgent'];
                                            $mobile = $row['mobile'];
                                            $email = $row['email'];
                                            $image = $row['image'];
                                            $invoice_no = $row['invoice_no'];
                                            $order_status = $row['order_status'];
                                            $action_by = $row['action_by'];
                                            $updated_at = $row['created_at'];
                                            $cancel_reason = $row['cancel_reason'];
                                            if(empty($cancel_reason))
                                            {
                                                $cancel_reason='';
                                            }
                                            if(empty($updated_at))
                                            {
                                                $updated_at='';
                                            }
                
                                            $query1 = $this->db->query("SELECT * from users where id='$member_id'");
                                            $row1=$query1->row_array();
                                                if(empty($manufacturer))
                                                {
                                                    $manufacturer="";
                                                }
                                                if(empty($mrp))
                                                {
                                                    $mrp="";
                                                }
                                        
                                            $order_date = date('l j M Y h:i A', strtotime($updated_at));
                                            $name=$row1['name'];
                                             if(empty($name))
                                             {
                                                 $name1='';
                                             }
                                             else
                                             {
                                                 $name1=$name;
                                             }
                                            $resultpost5[] = array(
                                               "order_id" => $invoice_no,
                                                "medlife_order_id" => "",
                                                "delivery_time" => "",
                                                "order_type" => "Life Saving Drug",
                                                "listing_id" => "",
                                                "listing_name" => "",
                                                "listing_type" => "45",
                                                "listing_payment_mode" => "",
                                                "invoice_no" => $invoice_no,
                                                "chat_id" => "",
                                                "address_id" => "",
                                                "name" => $name1,
                                                "mobile" => $mobile,
                                                "pincode" => "",
                                                "address1" => "",
                                                "address2" => "",
                                                "landmark" => "",
                                                "city" => "",
                                                "state" => "",
                                                "user_name" => $name1,
                                                "user_mobile" => $mobile,
                                                "user_email" => $email,
                                                "order_total" => 0,
                                                "order_discount"=>0,
                                                "gst_rs"=> "",
                                                "gst_per"=> "",
                                                "chc"=> "",
                                                "grand_total"=> "",
                                                "payment_method" => "",
                                                "order_date" => $order_date,
                                                "order_status" => $order_status,
                                                "cancel_reason" => $cancel_reason,
                                                "delivery_charge" => "",
                                                "product_order" => array(),
                                                "tracker" => array(),
                                                "prescription_create" => array(),
                                                "prescription_order" => array(),
                                                "action_by" => "",
                                                "rxid" => "",
                                                "is_cancel" => "",
                                                "profile_img"=>"https://d2c8oti4is0ms3.cloudfront.net/images/Check_In_Image/".$image,
                                                "life_qty" => $qty,
                                                "image" => "https://d2c8oti4is0ms3.cloudfront.net/images/Check_In_Image/".$image,
                                                "urgent"=>$urgent
                                            );
                                        }
                              }
                            else
                            {
                                $resultpost5 = array();
                            }
                            
                            
                         /*MNO orders*/  
                         $query22 = $this->db->query("SELECT * FROM user_order WHERE user_id='$user_id' and listing_type='44' group by invoice_no order by order_id DESC");
                         $count22 = $query22->num_rows(); 
                        
                        if ($count22 > 0) 
                           { 
            foreach ($query22->result_array() as $row22) {
                
                $order_id = $row22['order_id'];
                $order_type = $row22['order_type'];
                $delivery_time = $row22['delivery_time'];
               
                $listing_type = $row22['listing_type'];
                $invoice_no = $row22['invoice_no'];
               
                $address_id = $row22['address_id'];
                $name = $row22['name'];
                $mobile = $row22['mobile'];
                $pincode = $row22['pincode'];
                $address1 = $row22['address1'];
                $address2 = $row22['address2'];
                $landmark = $row22['landmark'];
                $city = $row22['city'];
                $state = $row22['state'];
                $action_by = $row22['action_by'];
                $payment_method = $row22['payment_method'];
                $order_date = $row22['order_date'];
                $order_date = date('l j M Y h:i A', strtotime($order_date));
               
                $delivery_charge = $row22['delivery_charge'];
                $order_status = $row22['order_status'];
                $order_type = $row22['order_type'];
                $action_by = $row22['action_by'];
                $rxId = $row22['rxId'];
                
                
                /*Added by swapnali on 20th nov 2k19 at 5pm*/
                $delivery_charge = $row22['delivery_charge'];
                $delivery_charges_by_customer = $row22['delivery_charges_by_customer'];
                $order_status = $row22['order_status'];
                $order_type = $row22['order_type'];
                $action_by = $row22['action_by'];
                $rxId = $row22['rxId'];
                
                if($vid=="13" || $vid=="44"){
                    $delivery_charge = $delivery_charges_by_customer;
                } 
                
                $dis = $row22['discount'];
                $gst_per = $row22['gst'];
                $chc = strval($row22['chc']);
                $order_amount = strval($row22['actual_cost']);
                $gst_rs = strval(($order_amount - $dis)  * ($gst_per / 100));
                $grand_total = strval($order_amount + $gst_rs + $chc - $dis + $delivery_charge); 
                /*Added by swapnali on 20th nov 2k19 at 5pm => END*/
              
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
                    $cancel_reason = $row22['cancel_reason'];
                } else {
                    $cancel_reason = $row22['cancel_reason'];
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
                          
                            $desc = (($product_price * $product_quantity)*$product_discount)/100;
                            $sub_total_discount +=$desc;
                           
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
                                "product_discount" => $desc,
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
                              
                               $desc1 = (($prescription_price*$prescription_quantity)*$prescription_discount)/100;
                                $sub_total_discount1 += $desc1;
                                $prescription_result[] = array(
                                    "prescription_name" => $prescription_name,
                                    "prescription_quantity" => $prescription_quantity,
                                    "prescription_price" => $prescription_price,
                                    "prescription_discount" => $desc1,
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
    $listing_paymode="Cash On Delivery";  
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
            $tracker = $this->All_booking_model->pharmacy_tracker($user_id, $invoice_no);
            
           $resultpost99[] = array(
                    "order_id" => $order_id,
                    "medlife_order_id" => $orderId,
                    "delivery_time" => str_replace('null', '', $delivery_time), //- swap
                    "order_type" => $order_type,
                    "listing_id" => "",
                    "listing_name" => "Night Owl",
                    "listing_type" => "44",
                    "listing_payment_mode" => $listing_paymode,
                    "invoice_no" => $invoice_no,
                    "chat_id" => "",
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
                    "user_email" => "",
                    "order_total" => $order_amount,
                    "order_discount"=>intval($dis),
                     "gst_rs"=> $gst_rs,
                    "gst_per"=> $gst_per,
                    "chc"=> $chc,
                    "grand_total"=> $grand_total,
                    "payment_method" => $payment_method,
                    "order_date" => $order_date,
                   
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    "delivery_charge" => $delivery_charge, //- swap
                    "product_order" => $product_resultpost,
                    "tracker" => $tracker,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result,
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel,
                    "profile_img"=>"",
                    "image"=>"",
                    "life_qty"=>"",
                    "urgent"=>"",
                ); 
            }
       }        
                                
                          else
                            {
                                $resultpost99 = array();
                            }      
                           
                             
                            
                            $resultpost=array_merge($resultpost4,$resultpost5,$resultpost99);
                            function cmp($a, $b)
{
    $aDateTime = new DateTime($a["order_date"]);
    $bDateTime = new DateTime($b["order_date"]);

    return $aDateTime < $bDateTime ? 1 : -1;
};

usort($resultpost, "cmp");
                    }
                    
                    
                    /*END pharmacy, MNO, medlife*/
                    
                    
                if($vid=="6")
                    {
                    $querys1 = $this->db->query("SELECT fitness_center_branch.branch_name,fitness_center_branch.user_id as branch_fit_id, fitness_center_branch.branch_image, fitness_center_branch.branch_phone, fitness_center_branch.branch_address, fitness_center_branch.pincode, fitness_center_branch.state, fitness_center_branch.city,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time,booking_master.trainer_package_id,booking_master.trainer_id, booking_master.joining_date FROM booking_master
                                                 INNER JOIN fitness_center_branch ON booking_master.branch_id=fitness_center_branch.id
                                                 WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='6' ORDER BY booking_master.booking_date DESC LIMIT $start, $limit");
                    $count1 = $querys1->num_rows();
                    if ($count1 > 0) 
                       {
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
                    
                    
                     $booking_date1 = date('j M Y  h:i A', strtotime($row1['booking_date']));
                     $order_date1 = date('l j M Y h:i A', strtotime($booking_date1));
                    

                    $resultpost[] = array(
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
                        "order_date" =>$order_date1,
                        "image"=>$branch_image,
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
                   
 $order_date1 = date('l j M Y h:i A', strtotime($row1['booking_date']));

                    $resultpost[] = array(
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
                        "image"=>$branch_image,
                        "order_date" => $order_date1,
                  
                        'listing_type'=>$row1['vendor_id']
                    );
                    }
                }} 
                    else 
                       {$resultpost = array();}
            
                }
                
                
                
                 if($vid=="36")
                    {
                        
                    $querys1 = $this->db->query("SELECT spa_branch.branch_name,spa_branch.user_id as branch_fit_id, spa_branch.branch_image, spa_branch.branch_phone, spa_branch.branch_address, spa_branch.pincode, spa_branch.state, spa_branch.city,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name,booking_master.amt, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time,booking_master.trainer_package_id,booking_master.trainer_id, booking_master.joining_date FROM booking_master
                                                 INNER JOIN spa_branch ON booking_master.branch_id=spa_branch.id
                                                 WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='36' ORDER BY booking_master.booking_date DESC LIMIT $start, $limit");
                    $count1 = $querys1->num_rows();
                    if ($count1 > 0) 
                       {
                foreach ($querys1->result_array() as $row1) {
                    $package_id = $row1['package_id'];
                    $trainer_package_id=$row1['trainer_package_id'];
                    $trainer_id=$row1['trainer_id'];
                    
                    
                    
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
                    $package_price = $row1['amt'];
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
                   // $order_date= $trail_booking_date . " ". $trail_booking_time;
                   $booking_date1 = date('j M Y  h:i A', strtotime($row1['booking_date']));
                     $order_date1 = date('l j M Y h:i A', strtotime($booking_date1));
                       $trainer_details = array();
                      
                       $traind=explode(",",$trainer_id);
                       if(count($traind) > 1)
                       {
                          $trainer_id_new = implode("','",$traind);
                       }
                       else
                       {
                           $trainer_id_new=$trainer_id;
                       }
                        
            $personal_trainer =$this->db->query("SELECT * from personal_trainers  where id IN ('$trainer_id_new')"); 
            $count = $personal_trainer->num_rows();
            if ($count > 0) 
            {
                foreach ($personal_trainer->result_array() as $row122) 
                {
                    $id1 = $row122['id'];
                    $manager_name1 = $row122['manager_name'];
                    $qualifications1 = $row122['qualifications'];
                    
                    $fitness_trainer_pic1 = $row122['fitness_trainer_pic'];
                    $gender1 = $row122['gender'];
                    $contact1 =$row122['contact'];
                    $rating="";
                    $language = $row122['language'];
                    
                    $personal_trainer_lanaguge =$this->db->query("SELECT * from languages  where id IN ('$language')"); 
                    $row122_lang=$personal_trainer_lanaguge->row_array();
                    $language_name=$row122_lang['name'];
                    
                    
                    $trainer_details[] = array(
                                               'id'=> $id1,
                                               'trainer_name' => $manager_name1,
                                               'contact' => $contact1,
                                               'qualifications' => $qualifications1,
                                               
                                               'trainer_pic' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$fitness_trainer_pic1,
                                               'gender' => $gender1,
                                               'rating' => $rating,
                                               'language'=>$language_name,
                                              );
                                      
                    
                }
                    
            }
            else {
               $trainer_details=array();
            }

                    $resultpost[] = array(
                        'booking_id' => $booking_id,
                        'branch_name' => $branch_name,
                        'branch_id' => $branch_id,
                        'listing_id' => $listing_id,
                        'trainer_details' => $trainer_details,
                        
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
                        'order_date'=>$order_date1,
                        
                        'joining_date' => $joining_date,
                        'book_for_date' => $book_for_date,
                        'booking_date' => $booking_date,
                        'status' => $status,
                        'listing_type'=>$row1['vendor_id'],
                        "image"=>$branch_image,
                    );
                    
                   
                }} 
                    else 
                       {
                           $resultpost = array();
                           
                       }
            
                }
                if($vid=="12")
                {
                       $query3 = $this->db->query("SELECT booking_master.*,health_record.patient_name,nursing_attendant.address,nursing_attendant.city,nursing_attendant.state,nursing_attendant.contact,nursing_attendant.pincode,nursing_booking_details.* FROM booking_master  LEFT JOIN users ON (booking_master.user_id = users.id) LEFT JOIN health_record ON(booking_master.patient_id=health_record.id) LEFT JOIN nursing_attendant ON(booking_master.listing_id=nursing_attendant.user_id) LEFT JOIN nursing_booking_details ON(booking_master.booking_id=nursing_booking_details.booking_id) where booking_master.user_id = '$user_id' and booking_master.vendor_id = '12' ORDER BY booking_master.booking_date DESC LIMIT $start, $limit");
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
                   if($status == null){$status = "";}if($listing_id== null){ $listing_id="";}if($booking_id== null){$booking_id="";} if($Nursing_id== null){$Nursing_id="";} 
             if($patient_name== null){  $patient_name="";}if($package_id== null){  $package_id="";} if($package_name== null){  $package_name="";} 
             if($package_amount== null){$package_amount="";}if($book_type== null){$book_type="";}if($patiente_condition== null){ $patiente_condition="";}
             if($attendent_time== null ){ $attendent_time="";}if($attendant_hour== null){ $attendant_hour="";}if($tentative_intime== null){ $tentative_intime="";}
             if($nursing_gender== null){$nursing_gender="";} if($attendant_needed== null)
                    {
                 
                      
                      $attendant_needed="";
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
                     $order_date= $joining_date ." ".$tentative_intime;
                     $order_date1 = date('l j M Y h:i A', strtotime($order_date));
                     $new_image="";
                    if(empty($package_image))
                    {
                        $new_image="";
                    }
                    else
                    {
                        $new_image="https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$package_image;
                    }
                    $resultpost[] = array(
                        'listing_id' => $listing_id,
                        'booking_id' => $booking_id,
                        'nursing_id' => $Nursing_id,
                        'appointment_date' => $booking_date,
                        'patient_name' => $patient_name,
                        'package_id' => $package_id,
                        'package_name' => $package_name,
                        'package_amount' => $package_amount,
                        'package_image' => $new_image,
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
                        'order_date' => $order_date1,
                         "image"=>$new_image,
                        'listing_type'=>$row3['vendor_id']
                    );
                    
                  
                    
                 
                }
                        } else {
                            $resultpost = array();
                        }
                }
                if($vid=="5")
                    {
                     
           $query2 = $this->db->query("select doctor_booking_master.booking_id, doctor_booking_master.booking_date,doctor_booking_master.listing_id,doctor_booking_master.user_id, doctor_booking_master.listing_id,doctor_booking_master.clinic_id, doctor_prescription.id as prescription_id, doctor_booking_master.booking_time,doctor_booking_master.status,doctor_booking_master.consultation_type, doctor_clinic.clinic_name,users.name as patient_name, doctor_clinic.address,doctor_clinic.image,doctor_clinic.state,doctor_clinic.city,doctor_clinic.pincode, doctor_clinic.consultation_charges,doctor_clinic.contact_no,dl.doctor_name from doctor_booking_master LEFT JOIN doctor_prescription ON (doctor_booking_master.booking_id = doctor_prescription.booking_id AND doctor_booking_master.listing_id = doctor_prescription.doctor_id AND doctor_booking_master.clinic_id = doctor_prescription.clinic_id)  LEFT JOIN doctor_clinic on (doctor_booking_master.clinic_id = doctor_clinic.id) LEFT JOIN users ON (doctor_booking_master.user_id = users.id) LEFT JOIN doctor_list as dl ON(dl.user_id=doctor_booking_master.listing_id) where doctor_booking_master.user_id = '$user_id'  and doctor_booking_master.vendor_type = '5'  ORDER BY doctor_booking_master.booking_date DESC LIMIT $start, $limit");

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
                     $consultation_charges = "";
                    $branch_name = "";
                    $address = "";
                    $image = "";
                    $clinic_contact_no = "";  
                    $clinic_id=$row2['clinic_id'];
                    $doctor_id=$row2['listing_id'];
                    
                    $sql21             = "SELECT  * from doctor_clinic WHERE id='$clinic_id' and doctor_id='$doctor_id'";
                    $query_practices1  = $this->db->query($sql21);
                    $total_practices1  = $query_practices1->num_rows();
                    if($total_practices1 > 0)
                    {
                      $get_pract=$query_practices1->row_array(); 
                        $consultation_charges = $get_pract['consultation_charges'];
                        $branch_name = $get_pract['clinic_name'];
                        
                        $address = $get_pract['address'] . "," . $get_pract['city'] . "," . $get_pract['state'] . "," . $get_pract['pincode'];
                        $image = $get_pract['image'];
                        $clinic_contact_no = $get_pract['contact_no'];
                         if ($image != '') {
                            $clinic_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $image;
                        } else {
                            $clinic_image = '';
                        }
                        $lat = $get_pract['lat'];
                    $lng = $get_pract['lng'];
                    }
                    else
                    {
                        $consultation_charges = "0";
                        $sql0 = sprintf("SELECT hospital_doctor_id FROM doctor_list WHERE user_id='$doctor_id'");
                    $query_practices0  = $this->db->query($sql0);
                    $total_practices0  = $query_practices0->num_rows();
                    $get_pract0=$query_practices0->row_array();
                    $ids=$get_pract0['hospital_doctor_id'];  
                    if(!empty($ids))
                       {
                      $sql21             = "SELECT hd.*,hd.consultation from hospital_doctor_list as hd LEFT JOIN hospitals as h on (h.user_id = hd.hospital_id ) WHERE hd.id IN ($ids) and h.user_id ='$clinic_id' ";
                       //$query_dr           = $this->db->query("SELECT doctor_clinic.clinic_name,doctor_list.doctor_name FROM hospitals INNER JOIN doctor_list ON doctor_list.=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id' AND doctor_clinic.doctor_id='$listing_id'");
                        $query_practices0  = $this->db->query($sql21);
                         $count_user         = $query_practices0->num_rows();
                         if($count_user > 0)
                         {
                            
                    $get_pract01=$query_practices0->row_array();
                            $consultation_charges = $get_pract01['consultation'];
                         }
                       }
                        
                        
                        
                        
                        
                        
                      $sql21             = "SELECT  * from hospitals WHERE user_id='$clinic_id'";
                    $query_practices1  = $this->db->query($sql21);
                    $total_practices1  = $query_practices1->num_rows();
                    $get_pract=$query_practices1->row_array(); 
                    
                    $branch_name = $get_pract['name_of_hospital'];
                    $address = $get_pract['address'] . "," . $get_pract['city'] . "," . $get_pract['state'] . "," . $get_pract['pincode'];
                    $image = $get_pract['image'];
                    $clinic_contact_no = $get_pract['phone'];  
                     if ($image != '') {
                            $clinic_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $clinic_image = '';
                        }
                         $lat = $get_pract['lat'];
                    $lng = $get_pract['lng'];
                    }
                    
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
                    $booking_time1 = explode('-',$booking_time);
                    $new =$booking_time1[0];
                    $order_date= $booking_date ." ".$new;
                     if ($image != '') {
                            $clinic_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $image;
                        } else {
                            $clinic_image = '';
                        }
                    $resultpost[] = array(
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
                        'image' => $clinic_image,
                        'clinic_contact_no' => $clinic_contact_no,
                        'status' => $status,
                        'prescription_id' => $prescription_id,
                        'prescription_pdf'=> $url,
                        "order_date" => date('l j M Y h:i A', strtotime($order_date)),
                        'listing_type'=>5
                    );
                }
            } else {
                $resultpost = array();
            }
            
                    }
                 if($vid=="10" || $vid=="31")
                    {
                       $count_query4 = $this->db->query("SELECT lb.*,bm.vendor_id,bm.status,lab_center.lab_name,lab_center.profile_pic from booking_master as bm LEFT JOIN lab_booking_details as lb ON(bm.booking_id = lb.booking_id) LEFT JOIN lab_center ON(bm.listing_id = lab_center.user_id) where bm.user_id='$user_id' and (bm.vendor_id='10' or bm.vendor_id='31') GROUP BY bm.booking_id order by bm.booking_id desc LIMIT $start, $limit");
                        $lab_booked4       = $count_query4->num_rows();
                        if ($lab_booked4 > 0) 
                        {
                          foreach ($count_query4->result_array() as $Lbooked) 
                            {
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
                                    $book_query = $this->db->query("SELECT status FROM booking_master WHERE booking_id='$bk_id'");
                                     $conff=$user_query->num_rows();
                                     if($conff>0){
                                    $status = $book_query->row()->status;
                                }
                                     else{
                                    $status = '';
                                }
                                     $report_path ='';
                                     $book_query_path = $this->db->query("SELECT report_path FROM reports WHERE booking_id='$bk_id'");
                                     $report_path_count =  $book_query_path->num_rows();
                                     if($report_path_count > 0)
                                       {
                                       $report_path = $book_query_path->row()->report_path;
                                 }
                                     if($Lbooked['reference_id']!='')
                                       {
                                         $order_date1= $Lbooked['booking_date'] ." ".$Lbooked['booking_time'];
                                $order_date = date('l j M Y h:i A', strtotime($order_date1)); 
                                   $resultpost[] = array(
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
                "listing_type"=> "31",
                 "order_date" => $order_date,
                    "image"=>'',
                );
                                 }
                                   }  
                                else
                                 {
                                    
                                $user_id = $Lbooked['user_id'];
                             
                                  $user_query = $this->db->query("SELECT email,phone,name FROM users WHERE id='$user_id'");
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
                                    
                                
                                $patient_id = $Lbooked['patient_id'];
                                $listing_id = $Lbooked['listing_id'];
                                $vendor_type = $Lbooked['vendor_type'];
                                $branch_id = $Lbooked['branch_id'];
                                $branch_name = $Lbooked['lab_name'];
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
                                $order_date1= $Lbooked['booking_date'] ." ".$Lbooked['booking_time'];
                                $order_date = date('l j M Y h:i A', strtotime($order_date1)); 
                                if(empty($branch_name)){
                                    $branch_name='Medicalwale Labs';
                                }
                                
                                if(!empty($Lbooked['profile_pic'])){
                                    $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$Lbooked['profile_pic'];
                                }
                                else{
                                    $branch_image = '';
                                }
                                
                                
                                
                                $Booed_test_list = array();
                                 $amount=0;
                                if ($test_ids != '' && $test_ids != '0') {
                                    $Testids = explode(',', $test_ids);
                                    
                                    foreach ($Testids as $tid) {
                                      //  echo "SELECT * FROM lab_test_details WHERE test_id = '$tid'";
                                        $Query = $this->db->query("SELECT lab_test_master.name,lab_test_master_details.test_id,lab_test_master_details.price,lab_test_master_details.home_collection_charges,lab_test_master_details.discounted_price,lab_test_master_details.home_delivery FROM lab_test_master_details INNER JOIN lab_test_master on lab_test_master.id=lab_test_master_details.test_id WHERE lab_test_master_details.test_id = '$tid' and lab_test_master_details.user_id='$listing_id'");
                                        $Comp = $Query->row();
                                        $comp_count = $Query->num_rows();
                                        //print_r($Comp);
                                        if($comp_count>0)
                                        {
                                            $test           = $Comp->name;
                                            $test_id        = $Comp->test_id;
                                            $price          = $Comp->price;
                                            $offer          = $Comp->discounted_price;
                                            $executive_rate = '';
                                            $home_delivery  = $Comp->home_delivery;
                                        
                                             $amount +=$price;
                                             $Booed_test_list[] = array(
                                                'test_id' => $test,
                                                'test' => $test_id,
                                                'price' => $price,
                                                'home_delivery' => $home_delivery);
                                        }
                                       
                                    }
                                }
                                 $lab_pack_name = "";
                                    $pack_details = "";
                                    $pack_amount = "";
                                
                                if($package_id > 0){
                                     
                                    $LP_query = $this->db->query("SELECT * FROM lab_package_master WHERE id='$package_id'");
                                    $result1 = $LP_query->num_rows();
                                    if($result1 > 0)
                                    {
                                      
                                    $lab_pack_name = $LP_query->row()->name;
                                    $pack_details = '';
                                    $pack_amount = $LP_query->row()->price;
                                    $amount +=$pack_amount;    
                                    }
                                }
                                  if($status == null)
                                {
                                    $status = "";
                                }
                                
                                $prescription_list = array(); 
        $lab_prescriptions = $Lbooked['prescriptions'];  
        if(!empty($lab_prescriptions)){
            $lab_prescriptions = explode(',',$lab_prescriptions);
            foreach ($lab_prescriptions as $prescription_img) {
                $prescription_list[] = array(
                    'image'=>'https://d2c8oti4is0ms3.cloudfront.net/images/lab_prescription/'.$prescription_img
                );
            }
            $branch_name='Medicalwale Labs';
        } 
                                 
                                $resultpost[] = array(
                                    'user_id'=> $user_id,
                                    'user_name'=>$user_name,
                                    'amount'=> (string)$amount,
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
                                    'prescription_list' => $prescription_list,
                                    'status'=>$status,
                                    "order_date" => $order_date,
                                    "image"=>$branch_image
                                );
                     }
                            }       
                        }
                       else
                        {
                         $resultpost = array(); 
                        }
                    }    
                if($vid=="34")
                   {
                       
                        $pro_list = array();
                        $results = $this->db->query("SELECT vd.delivery_by_medicalwale, uo.* FROM `user_order` as uo left join vendor_details_hm as vd on (uo.`listing_id` = vd.v_id) WHERE uo.`user_id` = '$user_id' AND  uo.`listing_type`='34'   ORDER BY order_date DESC LIMIT $start, $limit");
                      
                        foreach($results->result_array() as $order){
                            
                                 $new_date=$order['order_date'];
                              $order_date = date('l j M Y h:i A', strtotime($new_date));
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
                            $order += ['image'=>""];
                           foreach($order as $key => $value)
                                {
                                    if (array_key_exists("order_date",$order))
                                          {
                                            $order['order_date']=$order_date;
                                          }
                                    if($value == null){
                                        $order[$key] = "";
                                    }
                                }
                                
                          
                                
                            $resultpost[] = $order;
                        }
                   }
                if($vid=="8" || $vid=="50" )
                {
                     $resultpost61 = array();
       // IPD
       
       $booking_details = $this->db->query("SELECT * FROM booking_master WHERE user_id='$user_id' and vendor_id='8' order by booking_date DESC LIMIT $start, $limit");
       
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
              else
              {
                 $package_name=""; 
              }
               $patient_id = $row['patient_id'];
              $patient_detaild = $this->db->query("SELECT name,phone,email,gender FROM users WHERE id='$patient_id' ");
              $patient_count = $patient_detaild->num_rows();
             
              if($patient_count>0)
              {
                $name = $patient_detaild->row()->name;
                $phone = $patient_detaild->row()->phone;
                $email = $patient_detaild->row()->email;
                if(empty($email))
                {
                   $email=""; 
                }
                $gender = $patient_detaild->row()->gender;
                if(empty($gender))
                {
                   $gender=""; 
                }
              }
              else
              {
                $name = "";
                $phone = "";
                $email = "";
                $gender = "";  
              }
              
              $user_id = $row['user_id'];
             
              $name = $name;
             $phone =$phone;
             $email = $email;
             $gender = $gender;
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
             $order_date1 = $booking_date." ".$booking_time;
              $order_date = date('l j M Y h:i A', strtotime($booking_date));
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
                     "order_date" =>$order_date,
                    "image"=>'',
                    'booking_type'=>"IPD"
                );
            
           }
        }
        
       // OPD
   $query= $this->db->query("select hospital_booking_master.booking_id,hospital_booking_master.booking_date,hospital_booking_master.listing_id,hospital_booking_master.user_id,hospital_booking_master.listing_id,
hospital_booking_master.doctor_id,hospital_doctor_prescription.id as prescription_id,hospital_booking_master.booking_time,hospital_booking_master.status,hospital_booking_master.consultation_type,
hospitals.name_of_hospital,users.name as patient_name, hospitals.address,hospitals.image,hospitals.state,hospitals.city,hospitals.pincode, dl.consultation,hospitals.phone,dl.doctor_name from hospital_booking_master  LEFT JOIN hospital_doctor_prescription 
ON (hospital_booking_master.patient_id = hospital_doctor_prescription.patient_id AND hospital_booking_master.doctor_id = hospital_doctor_prescription.doctor_id AND hospital_booking_master.listing_id = hospital_doctor_prescription.hospital_id) LEFT JOIN hospitals on (hospital_booking_master.listing_id = hospitals.user_id)
LEFT JOIN users ON (hospital_booking_master.user_id = users.id)  LEFT JOIN hospital_doctor_list as dl ON(dl.id=hospital_booking_master.doctor_id)
where hospital_booking_master.user_id = '$user_id' ORDER BY hospital_booking_master.booking_date DESC LIMIT $start, $limit");

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
                       if($image !='')
                    {
                        $clinic_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                    }
                    else
                    {
                        $clinic_image="";
                    }
                       if($prescription_id == null)
                    {
                        $prescription_id = "";
                    }
                       if($clinic_contact_no == null)
                    {
                        $clinic_contact_no = "";
                    }
                    $booking_time1 = explode('-',$booking_time);
                    $new =$booking_time1[0];
                    $order_date1= date("Y-m-d",strtotime($booking_date)) ." ".$new;

             $order_date = date('l j M Y h:i A', strtotime($order_date1));
             
                    $resultpost61[] = array(
                        'booking_id' => $booking_id,
                        'doctor_id' => $doctor_id,
                        'order_date' => $order_date,
                        
                        'patient_name' => $patient_name,
                        'doctor_name' => $doctor_name,
                        'consultation_charge' => $consultation_charges,
                        'consultation_type' => $consultation_type,
                        'clinic_name' => $branch_name,
                        'clinic_address' => $address,
                        'image' => $clinic_image,
                        'clinic_contact_no' => $clinic_contact_no,
                        'status' => $status,
                        'prescription_id' => $prescription_id,
                        
                        'booking_type'=>"OPD",
                         "listing_type"=> "50"
                    );
                }
            }
                         $resultpost=$resultpost61;
                function cmp($a, $b)
{
    $aDateTime = new DateTime($a["order_date"]);
    $bDateTime = new DateTime($b["order_date"]);

    return $aDateTime < $bDateTime ? 1 : -1;
};

usort($resultpost, "cmp");   
              
                }
                
              if($vid=="21")
                    {
                     
           $query2 = $this->db->query("select * from rides where user_id = '$user_id'  ORDER BY ride_id DESC LIMIT $start, $limit");

            $count2 = $query2->num_rows();
            if ($count2 > 0) {
                foreach ($query2->result_array() as $row2) {
                 
                    $booking_id = $row2['ride_id'];
                    $booking_date = $row2['Create_time'];
                    $status = $row2['status'];
                    $patient_name = $row2['user_name'];
                    $patient_mobile = $row2['user_mobile'];
                    $payment_details = "Cash At Point";
                    $from = $row2['pickup_adress'];
                    $to = $row2['drop_address'];
                    $driver_name = $row2['driver_name'];
                    $driver_mobile = $row2['driver_mobile'];
                    $ambulance_type = $row2['subtype_name'];
                    $price = $row2['price'];
                    $discount = "0";
                    $total=$row2['price'];
                    $dirver_id=$row2['driver_id'];
                    $amb="";
                    $query1 = $this->db->query("select * from ambulance_fare where driver_user_id = '$dirver_id'");
                    $count1 = $query1->num_rows();
                    if ($count1 > 0) {
                        $row1=$query1->row_array();
                        $amb=$row1['amb_no'];
                    }
                    else
                    {
                        $amb="";
                    }
                        
                    $resultpost[] = array(
                        'booking_id' => $booking_id,
                        'order_date' => date('l j M Y h:i A', strtotime($booking_date)),
                        'status' => $status,
                        'dirver_id' => $dirver_id,
                        'patient_name' => $patient_name,
                        'patient_mobile' => $patient_mobile,
                        'payment_type' => $payment_details,
                        'from' => $from,
                        'to' => $to,
                        'driver_name' => $driver_name,
                        'driver_mobile' => $driver_mobile,
                        'ambulance_no' => $amb,
                        
                        'ambulance_type' => $ambulance_type,
                        'price'=> $price,
                        'discount'=>$discount,
                        'listing_type'=>"21",
                        'total_amount'=>$price,
                        'image'=>""
                    );
                }
            } else {
                $resultpost = array();
            }
            
                    }
            
             if($vid=="39")
                    {
                         $user_order_query = "SELECT booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time,booking_master.trainer_package_id,booking_master.trainer_id, booking_master.joining_date FROM booking_master WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='39'  ORDER BY booking_master.id DESC LIMIT $start, $limit"; 
                        // die(); 
                        $querys1 = $this->db->query($user_order_query);
                       /* $querys1 = $this->db->query("SELECT dentists_branch.name_of_hospital, dentists_branch.id as branch_id ,dentists_branch.dentists_branch_user_id as branch_dentist_id, dentists_branch.image, dentists_branch.phone, dentists_branch.address, dentists_branch.pincode, dentists_branch.state, dentists_branch.city,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time,booking_master.trainer_package_id,booking_master.trainer_id, booking_master.joining_date FROM booking_master left JOIN dentists_branch ON booking_master.branch_id=dentists_branch.id
                                                 WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='39'  ORDER BY booking_master.id DESC");*/
                    $count1 = $querys1->num_rows();
                    if ($count1 > 0) 
                       {
                          
                        foreach ($querys1->result_array() as $row1) {
                            
                        //    print_r($row1);
                           $listing_id = $row1['listing_id'];
                        $branch_id = "";
                        $branch_name = "";
                        $branch_image = "";
                        $branch_phone = "";
                        $branch_address = "";
                        $branch_pincode = "";
                        $branch_city = "";
                        $branch_state = "";
                        
                        
                        $query192 = $this->db->query("SELECT dentists_branch.name_of_hospital, dentists_branch.id as branch_id ,dentists_branch.dentists_branch_user_id as branch_dentist_id, dentists_branch.image, dentists_branch.phone, dentists_branch.address, dentists_branch.pincode, dentists_branch.state, dentists_branch.city from dentists_branch where (hub_user_id='$listing_id' OR dentists_branch_user_id='$listing_id') ");
                        $row192=$query192->row_array();    
                        $count12 = $query192->num_rows();
                        if ($count12 > 0) 
                           {
                                $branch_id = $row192['branch_id'];
                                $branch_name = $row192['name_of_hospital'];
                                $branch_image = $row192['image'];
                                $branch_phone = $row192['phone'];
                                $branch_address = $row192['address'];
                                $branch_pincode = $row192['pincode'];
                                $branch_city = $row192['city'];
                                $branch_state = $row192['state'];
                           } 
                           
                         $query1921 = $this->db->query("SELECT dentists_clinic_list.name_of_hospital,dentists_clinic_list.image, dentists_clinic_list.phone, dentists_clinic_list.address, dentists_clinic_list.pincode, dentists_clinic_list.state, dentists_clinic_list.city from dentists_clinic_list where user_id='$listing_id' ");
                        $row1921=$query1921->row_array();    
                        $count123 = $query1921->num_rows();
                        if ($count123 > 0) 
                           {
                                $branch_id = "";
                                $branch_name = $row1921['name_of_hospital'];
                                $branch_image = $row1921['image'];
                                $branch_phone = $row1921['phone'];
                                $branch_address = $row1921['address'];
                                $branch_pincode = $row1921['pincode'];
                                $branch_city = $row1921['city'];
                                $branch_state = $row1921['state'];
                           }      
                        $package_id = $row1['package_id'];
                        $trainer_package_id=$row1['trainer_package_id'];
                        $trainer_id=$row1['trainer_id'];
                    
                    
                    $package_name = "";
                    $package_details = "";
                    $query12 = $this->db->query("SELECT packages.package_name, packages.package_details, packages.price from packages  where packages.id ='$package_id' ");
                     $row12=$query12->row_array();
                     $count12 = $query12->num_rows();
                        if ($count12 > 0) 
                           {
                    $package_name = $row12['package_name'];
                    $package_details = $row12['package_details'];
                           }
                    
                    $booking_id = $row1['booking_id'];
                    
                    $appointment_user_name = $row1['user_name'];
                    $appointment_user_mobile = $row1['user_mobile'];
                    $appointment_user_email = $row1['user_email'];
                    $category_id = $row1['category_id'];
                    $status = $row1['status'];
                    
                //    $package_price = $row1['amt'];
                    $trail_booking_date = $row1['trail_booking_date'];
                    $trail_booking_time = $row1['trail_booking_time'];
                    $joining_date = date('j M Y', strtotime($row1['joining_date']));
                    $book_for_date = date('d/m/Y', strtotime($row1['joining_date']));
                    $is_free_trial = 'No';
                    date_default_timezone_set('Asia/Kolkata');
                  
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
                   // $order_date= $trail_booking_date . " ". $trail_booking_time;
                   $booking_date1 = date('j M Y  h:i A', strtotime($row1['booking_date']));
                     $order_date1 = date('l j M Y h:i A', strtotime($booking_date1));
                


                    $resultpost[] = array(
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
                       // 'package_price' => $package_price,
                        'is_free_trial' => $is_free_trial,
                        'order_date'=>$order_date1,
                        
                      //  'joining_date' => $joining_date,
                        //'book_for_date' => $book_for_date,
                        'booking_date' => $booking_date,
                        'status' => $status,
                        'listing_type'=>$row1['vendor_id'],
                        "image"=>$branch_image,
                    );
                    
                   
                }
                           
                       } 
                    else
                    {
                        $resultpost=array();
                    }
            
                }   
             
                
             return $resultpost;
             
    }

  
   public function elasticsearch($user_id,$index_id,$keyword){
     
        $returndoctor = array();
        $perc=array();
     $returnresult = $this->elasticsearch->query_all($index_id,$keyword);
     
            if($returnresult['hits']['total'] < 0){
               
                       foreach($returnresult['hits']['hits'] as $hi){
                     
                          $sim = similar_text($hi['_source']['name'], $keyword, $perc[]);
                       }
                    }

                 //@$dataperc=max($perc);
      //  if($dataperc >= 70){
            $resul= array("query"=>array("bool"=>array("should"=>array(array("term"=>array("user_id.keyword"=>"$user_id")),array("query_string"=>array("default_field"=>"_all","query"=>"$keyword"))))));
          // $resul = '{"query":{"bool":{"must":[],"must_not":[],"should":[{"term":{"user_id.keyword":"43114"}},{"query_string":{"default_field":"_all","query":"20190531160557"}}]}}}';
         $data1=json_encode($resul);
            
           //  print_r($data1);
                
             $returnresult = $this->elasticsearch->suggest($index_id,$data1);
            // print_r($returnresult);
               foreach($returnresult['hits']['hits'] as $hi){
                          $returndoctor[] =$hi['_source'];
                       }
                       return $returndoctor;
                       
           /* }else{
     
                $data = array("suggest"=>array("my-suggestion"=>array("text"=>$keyword, "term"=>array("field"=>"name")))); 
                $data1=json_encode($data);
                $return =   $this->elasticsearch->suggest($index_id,$data1);
            print_r($return);
                        $name=array();
                        foreach($return['suggest']['my-suggestion'] as $a){
                                if(empty($a['options'])){
                                  $name[]=  $a['text'];
                                }else{
                                      $name[]=$a['options'][0]['text'];
                                }
                        }
                        $string_version = implode(' ', $name);
                        $returnresult = $this->elasticsearch->query_all($index_id,$string_version);
                                foreach($returnresult['hits']['hits'] as $hi){
                                          $returndoctor[] = $hi['_source'];
                                       }
                 return $returndoctor;
             }*/
}
  
      public function all_booking_details_search($user_id, $keyword) {
        if ($user_id > 0) {
            
            // People
            $field1 = '';
            $field2 = '';
            $field3 = '';
               $index_id="doctor_booking_master";
                  $doctor_booking=$this->elasticsearch($user_id,$index_id,$keyword);
                    if (!empty($doctor_booking)) {
                        $doctor_booking_array =  $doctor_booking;
                      
                    } else {
                        $doctor_booking_array = array();
                    } 
            
            // Doctor
            $field1 = '';
            $field2 = '';
            $field3 = '';
            $index_id="fitness_booking";
          
            $fitness=$this->elasticsearch($user_id,$index_id,$keyword);
                 
            if (!empty($fitness)) {
                $fitness_array =  $fitness;
            } else {
                $fitness_array = array();
            }
            

            // Pharmacy
            $field1 = '';
            $field2 = '';
            $field3 = '';
              $index_id="lab_booking";
                     $lab=$this->elasticsearch($user_id,$index_id,$keyword);
                if (!empty($lab)) {
                    $lab_array = $lab;
                } else {
                    $lab_array = array();
                }
        
          

            // Labs
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
             
                $index_id="nursing_booking";
                   $nursing=$this->elasticsearch($user_id,$index_id,$keyword);
            if (!empty($nursing)) {
            
                $nursing_array =  $nursing;
            } else {
                $nursing_array = array();
            }
            

            $resultpost = array_merge($doctor_booking_array, $fitness_array, $lab_array, $nursing_array);
       
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
  
  
   public function all_booking_details_noti($user_id,$booking_id,$vid)
    {
       $delivery_charges_by_customer = $delivery_charges_by_vendor = $delivery_charges_by_mw = $delivery_charges_to_mno = 0;
        $resultpost = array();
         $totalAmount = "";
                    $payableAmount = "";
                    $discountAmount = "";
                $order_amount = $gst_rs = $gst_per = $chc = $grand_total = "";
                    $dis = 0;
                if($vid=="13" || $vid=="38" )
                    {
                        $query = $this->db->query("SELECT user_order.*,medical_stores.medical_name,medical_stores.profile_pic FROM user_order LEFT JOIN medical_stores on user_order.listing_id=medical_stores.id  WHERE (user_order.listing_type='13' or user_order.listing_type='38' ) AND user_order.user_id='$user_id' and user_order.invoice_no='$booking_id' ");
                        $count = $query->num_rows();
                        if ($count > 0) 
                           { 
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
                $order_date = date('l j M Y h:i A', strtotime($order_date));
                $actual_cost = $row['actual_cost'];
                $order_total = $row['order_total'];
                $delivery_charge = $row['delivery_charge'];
                $delivery_charges_by_customer = $row['delivery_charges_by_customer']; // - swapnali 
                $delivery_charges_by_vendor = $row['delivery_charges_by_vendor']; // - swapnali 
                $delivery_charges_by_mw = $row['delivery_charges_by_mw']; // - swapnali 
                $delivery_charges_to_mno = $row['delivery_charges_to_mno']; // - swapnali 
                $order_status = $row['order_status'];
                $order_type = $row['order_type'];
                $action_by = $row['action_by'];
                $rxId = $row['rxId'];
                
                
                /*Added by swapnali on 20th nov 2k19 at 5pm*/
                $delivery_charge = $row['delivery_charge'];
                $delivery_charges_by_customer = $row['delivery_charges_by_customer'];
                $order_status = $row['order_status'];
                $order_type = $row['order_type'];
                $action_by = $row['action_by'];
                $rxId = $row['rxId'];
                
                if($vid=="13" || $vid=="44"){
                    $delivery_charge = $delivery_charges_by_customer;
                } 
                
                $dis = $row['discount'];
                $gst_per = $row['gst'];
                $chc = strval($row['chc']);
                $order_amount = strval($row['actual_cost']);
                $gst_rs = strval(($order_amount - $dis)  * ($gst_per / 100));
                $grand_total = strval($order_amount + $gst_rs + $chc - $dis + $delivery_charge); 
                /*Added by swapnali on 20th nov 2k19 at 5pm => END*/
                
               //added by zak for maintain medlife cancel order 
                  if ($row['profile_pic'] != '') {
                    $profile_pic = $row['profile_pic'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
                }
                  $is_cancel = 'false';
                  $orderId="";
                if($rxId != 'NULL')
                {
                    $query = $this->db->query("select orderState,orderId,totalAmount,payableAmount,discountAmount from medlife_order where rxID='$rxId' order by id desc");
                $medlife_order =  $query->row();
                $medlife_order_num =  $query->num_rows();
                // = $medlife_order->orderState;
                 //    $medlife_order= $this->db->select('orderState')->from('medlife_order')->where('rxID', $rxId)->get()->row();
                 if($medlife_order_num>0)
                 {
                     $medlife_status = $medlife_order->orderState;
                     $order_status = $medlife_order->orderState;
                     
                    $totalAmount = $medlife_order->totalAmount;
                    $payableAmount = $medlife_order->payableAmount;
                    $discountAmount = $medlife_order->discountAmount;
                    
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
                 $row1 = $query1->row();
                  $order_total=0;
                  $sub_total_sum2=0;
                  $sub_total_sum1=0;
                  $sub_total_discount=0;
                  $sub_total_discount1=0;
                  if ($count1 > 0) {
            //foreach ($query1->result_array() as $row1) {
                    $order_id1            = $row1->order_id;
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
                            $desc = (($product_price * $product_quantity)*$product_discount)/100;
                            $sub_total_discount +=$desc;
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
                                "product_discount" => $desc,
                                "sub_total" => $sub_total,
                                "product_status" => $product_status,
                                "product_status_type" => $product_status_type,
                                "product_status_value" => $product_status_value,
                                "product_order_status" => $product_order_status
                            );
                        }
                    }
              if($orderId==""){
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
                                
                                $desc1 = (($prescription_price * $prescription_quantity)*$prescription_discount)/100;
                                $sub_total_discount1 += $desc1;
                                $prescription_result[] = array(
                                    "prescription_name" => $prescription_name,
                                    "prescription_quantity" => $prescription_quantity,
                                    "prescription_price" => $prescription_price,
                                    "prescription_discount" => $desc1,
                                    "prescription_status" => $prescription_status
                                );
                                
                            }
                        }
                        
                    }
              }else{
                 
                  
                       
                            $product_order_id1     = $orderId;
                            $product_id1           = '';
                            $product_name1         = '';
                            $product_img1          = '';
                            $product_quantity1     = '';
                            $product_price1        = $totalAmount;
                            $sub_total1            = $payableAmount;
                            $product_discount       = $discountAmount;
                            $product_status1       = '';
                            $product_status_type1  = '';
                            $product_status_value1 = '';
                            $product_order_status1 = '';
                         
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
                                "product_discount" => $product_discount,
                                "sub_total" => $sub_total1,
                                "product_status" => $product_status1,
                                "product_status_type" => $product_status_type1,
                                "product_status_value" => $product_status_value1,
                                "product_order_status" => $product_order_status1
                            );
                        
                        
                        
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
                                
                                $desc1 = (($prescription_price * $prescription_quantity)*$prescription_discount)/100;
                                $sub_total_discount1 += $desc1;
                                $prescription_result[] = array(
                                    "prescription_name" => $prescription_name,
                                    "prescription_quantity" => $prescription_quantity,
                                    "prescription_price" => $prescription_price,
                                    "prescription_discount" => $desc1,
                                    "prescription_status" => $prescription_status
                                );
                                
                            }
                        }
                        
                    
              }
                   
           // }
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
            $tracker = $this->All_booking_model->pharmacy_tracker($user_id, $invoice_no);
            $data2= array();
            $this->load->model('PaymentModel');
            $data1 = $this->PaymentModel->get_invoice_costing($invoice_no);
            $data2[] = $data1['data'];
            
             if($listing_type=="38")
               {
                   if(!empty($rxId) )
                {
                $resultpost4[] = array(
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
                    "user_email" => "",
                    "order_total" => $order_total,
                    "order_discount"=>$order_total_discount,
                    "gst_rs"=> $gst_rs,
                    "gst_per"=> $gst_per,
                    "chc"=> $chc,
                    "grand_total"=> $grand_total,
                    "payment_method" => $payment_method,
                    "order_date" => $order_date,
                    "image"=>$profile_pic,
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    "delivery_charge" => $delivery_charge,
                    "delivery_charges_by_customer" => $delivery_charges_by_customer, //- swap
                    "delivery_charges_by_vendor" => $delivery_charges_by_vendor, //- swap
                    "delivery_charges_by_mw" => $delivery_charges_by_mw, //- swap
                    "delivery_charges_to_mno" => $delivery_charges_to_mno, //- swap
                    "product_order" => $product_resultpost,
                    "tracker" => $tracker,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result,
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel,
                    "life_qty"=>"",
                    "urgent"=>"",
                    "image"=>$profile_pic,
                    "final_calculation"=> $data2
                );
                } else 
                        {
                            $resultpost4 = array();
                        }
                   
               }
               else
               {
                   $resultpost4[] = array(
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
                    "user_email" => "",
                    "order_total" => $order_amount,
                    "order_discount"=>intval($dis),
                    "gst_rs"=> $gst_rs,
                    "gst_per"=> $gst_per,
                    "chc"=> $chc,
                    "grand_total"=> $grand_total,
                    "payment_method" => $payment_method,
                    "order_date" => $order_date,
                    "image"=>$profile_pic,
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    // "delivery_charge" => $delivery_charge, - commented by swapnali
                    "delivery_charge" => $delivery_charges_by_customer,  // for user default delivery charges should be delivery by customer
                    "delivery_charges_by_customer" => $delivery_charges_by_customer, //- swap
                    "delivery_charges_by_vendor" => $delivery_charges_by_vendor, //- swap
                    "delivery_charges_by_mw" => $delivery_charges_by_mw, //- swap
                    "delivery_charges_to_mno" => $delivery_charges_to_mno, //- swap
                    "product_order" => $product_resultpost,
                    "tracker" => $tracker,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result,
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel,
                    "life_qty"=>"",
                    "urgent"=>"",
                    "image"=>$profile_pic,
                    "final_calculation"=> $data2
                ); 
               }
            }
       } 
                        else 
                        {
                            $resultpost4 = array();
                        }
                         $resultpost=$resultpost4;
                    }
                    
                    
                    if($vid=="44" )
                    {
                         $query = $this->db->query("SELECT * FROM user_order WHERE user_id='$user_id' and invoice_no='$booking_id' group by invoice_no order by order_id DESC");
                        $count = $query->num_rows(); 
                        
                        if ($count > 0) 
                           { 
            foreach ($query->result_array() as $row) {
                // print_r($row); die();
                $order_id = $row['order_id'];
                $order_type = $row['order_type'];
                $delivery_time = $row['delivery_time'];
               
                $listing_type = $row['listing_type'];
                $invoice_no = $row['invoice_no'];
               
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
                $order_date = date('l j M Y h:i A', strtotime($order_date));
               
                $delivery_charge = $row['delivery_charge'];
                
                   $delivery_charges_by_customer = $row['delivery_charges_by_customer']; // - swapnali 
                $delivery_charges_by_vendor = $row['delivery_charges_by_vendor']; // - swapnali 
                $delivery_charges_by_mw = $row['delivery_charges_by_mw']; // - swapnali 
                $delivery_charges_to_mno = $row['delivery_charges_to_mno']; // - swapnali 
                $order_status = $row['order_status'];
                $order_type = $row['order_type'];
                $action_by = $row['action_by'];
                $rxId = $row['rxId'];
                
                /*Added by swapnali on 20th nov 2k19 at 5pm*/
                $delivery_charge = $row['delivery_charge'];
                $delivery_charges_by_customer = $row['delivery_charges_by_customer'];
                $order_status = $row['order_status'];
                $order_type = $row['order_type'];
                $action_by = $row['action_by'];
                $rxId = $row['rxId'];
                
                if($vid=="13" || $vid=="44"){
                    $delivery_charge = $delivery_charges_by_customer;
                } 
                
                $dis = $row['discount'];
                $gst_per = $row['gst'];
                $chc = strval($row['chc']);
                $order_amount = strval($row['actual_cost']);
                $gst_rs = strval(($order_amount - $dis)  * ($gst_per / 100));
                $grand_total = strval($order_amount + $gst_rs + $chc - $dis + $delivery_charge); 
                /*Added by swapnali on 20th nov 2k19 at 5pm => END*/
              
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
                  $row1 = $query1->row();
                  $order_total=0;
                  $sub_total_sum2=0;
                  $sub_total_sum1=0;
                  $sub_total_discount=0;
                  $sub_total_discount1=0;
                  if ($count1 > 0) {
            //foreach ($query1->result_array() as $row1) {
                    $order_id1            = $row1->order_id;
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
                          
                            //$desc = (($product_price * $product_quantity)*$product_discount)/100;
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
                              
                             // echo  $desc1 = (($prescription_price*$prescription_quantity)*$prescription_discount)/100;
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
                
                   
            //}
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
    $listing_paymode="Cash On Delivery";  
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
            $tracker = $this->All_booking_model->pharmacy_tracker($user_id, $invoice_no);
            
           $resultpost4[] = array(
                    "order_id" => $order_id,
                    "medlife_order_id" => $orderId,
                    "delivery_time" => str_replace('null', '', $delivery_time), //- swap
                    "order_type" => $order_type,
                    "listing_id" => "",
                    "listing_name" => "Night Owl",
                    "listing_type" => "44",
                    "listing_payment_mode" => $listing_paymode,
                    "invoice_no" => $invoice_no,
                    "chat_id" => "",
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
                    "user_email" => "",
                    "order_total" => $order_amount,
                    "order_discount"=>intval($dis),
                    "gst_rs"=> $gst_rs,
                    "gst_per"=> $gst_per,
                    "chc"=> $chc,
                    "grand_total"=> $grand_total,
                    "payment_method" => $payment_method,
                    "order_date" => $order_date,
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    // "delivery_charge" => $delivery_charge, - commented by swapnali
                    "delivery_charge" => $delivery_charges_by_customer,  // for user default delivery charges should be delivery by customer
                    "delivery_charges_by_customer" => $delivery_charges_by_customer, //- swap
                    "delivery_charges_by_vendor" => $delivery_charges_by_vendor, //- swap
                    "delivery_charges_by_mw" => $delivery_charges_by_mw, //- swap
                    "delivery_charges_to_mno" => $delivery_charges_to_mno, //- swap
                    "product_order" => $product_resultpost,
                    "tracker" => $tracker,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result,
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel,
                    "profile_img"=>"",
                    "image"=>"",
                    "life_qty"=>"",
                    "urgent"=>"",
                ); 
            }
       }   
                        else 
                        {
                            $resultpost4 = array();
                        }
                         $resultpost=$resultpost4;
                    }
                    if($vid=="45")
                    {
                        $query = $this->db->query("SELECT * from life_saving_drugs where user_id='$user_id' and invoice_no='$booking_id'");
                        $count = $query->num_rows();
                           if ($count > 0)
                              { 
                                foreach ($query->result_array() as $row) 
                                        {
                                            $member_id = $row['member_id'];
                                            $qty = $row['qty'];
                                            $urgent = $row['urgent'];
                                            $mobile = $row['mobile'];
                                            $email = $row['email'];
                                            $image = $row['image'];
                                            $invoice_no = $row['invoice_no'];
                                            $order_status = $row['order_status'];
                                            $action_by = $row['action_by'];
                                            $updated_at = $row['created_at'];
                                            $cancel_reason = $row['cancel_reason'];
                                            if(empty($cancel_reason))
                                            {
                                                $cancel_reason='';
                                            }
                                            if(empty($updated_at))
                                            {
                                                $updated_at='';
                                            }
                
                                            $query1 = $this->db->query("SELECT * from users where id='$member_id'");
                                            $row1=$query1->row_array();
                                                if(empty($manufacturer))
                                                {
                                                    $manufacturer="";
                                                }
                                                if(empty($mrp))
                                                {
                                                    $mrp="";
                                                }
                                        
                                            $order_date = date('l j M Y h:i A', strtotime($updated_at));
                                            $name=$row1['name'];
                                             if(empty($name))
                                             {
                                                 $name1='';
                                             }
                                             else
                                             {
                                                 $name1=$name;
                                             }
                                            $resultpost5[] = array(
                                               "order_id" => $invoice_no,
                                                "medlife_order_id" => "",
                                                "delivery_time" => "",
                                                "order_type" => "Life Saving Drug",
                                                "listing_id" => "",
                                                "listing_name" => "",
                                                "listing_type" => "45",
                                                "listing_payment_mode" => "",
                                                "invoice_no" => $invoice_no,
                                                "chat_id" => "",
                                                "address_id" => "",
                                                "name" => $name1,
                                                "mobile" => $mobile,
                                                "pincode" => "",
                                                "address1" => "",
                                                "address2" => "",
                                                "landmark" => "",
                                                "city" => "",
                                                "state" => "",
                                                "user_name" => $name1,
                                                "user_mobile" => $mobile,
                                                "user_email" => $email,
                                                "order_total" => 0,
                                                "order_discount"=>0,
                                                "gst_rs"=> "",
                                                "gst_per"=> "",
                                                "chc"=> "",
                                                "grand_total"=> "",
                                                "payment_method" => "",
                                                "order_date" => $order_date,
                                                "order_status" => $order_status,
                                                "cancel_reason" => $cancel_reason,
                                                "delivery_charge" => "",
                                                "product_order" => array(),
                                                "tracker" => array(),
                                                "prescription_create" => array(),
                                                "prescription_order" => array(),
                                                "action_by" => "",
                                                "rxid" => "",
                                                "is_cancel" => "",
                                                "profile_img"=>"https://d2c8oti4is0ms3.cloudfront.net/images/Check_In_Image/".$image,
                                                "life_qty" => $qty,
                                                "image" => "https://d2c8oti4is0ms3.cloudfront.net/images/Check_In_Image/".$image,
                                                "urgent"=>$urgent
                                            );
                                        }
                              }
                            else
                            {
                                $resultpost5 = array();
                            }
                            
                            $resultpost=$resultpost5;
                            
                    }
                if($vid=="6")
                    {
                    $querys1 = $this->db->query("SELECT fitness_center_branch.branch_name,fitness_center_branch.user_id as branch_fit_id, fitness_center_branch.branch_image, fitness_center_branch.branch_phone, fitness_center_branch.branch_address, fitness_center_branch.pincode, fitness_center_branch.state, fitness_center_branch.city,fitness_center_branch.lat,fitness_center_branch.lng,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time,booking_master.trainer_package_id,booking_master.trainer_id, booking_master.joining_date FROM booking_master
                                                 INNER JOIN fitness_center_branch ON booking_master.branch_id=fitness_center_branch.id
                                                 WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='6' and booking_master.booking_id='$booking_id' ");
                    $count1 = $querys1->num_rows();
                    if ($count1 > 0) 
                       {
                foreach ($querys1->result_array() as $row1) {
                    $package_id = $row1['package_id'];
                    $trainer_package_id=$row1['trainer_package_id'];
                    $trainer_id=$row1['trainer_id'];
                    if($package_id !=0)
                    {
                    
                    
                    $query12 = $this->db->query("SELECT packages.package_name, packages.package_details, packages.price from packages  where packages.id ='$package_id' ");
                     $row12=$query12->row_array();
                     $lat = $row1['lat'];
                    $lng = $row1['lng'];
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
                    
                    
                     $booking_date1 = date('j M Y  h:i A', strtotime($row1['booking_date']));
                     $order_date1 = date('l j M Y h:i A', strtotime($booking_date1));

                    $resultpost[] = array(
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
                        "order_date" =>$order_date1,
                        "image"=>$branch_image,
                        'lat'=>$lat,
                        'lng'=>$lng,
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
                   

$order_date1 = date('l j M Y h:i A', strtotime($row1['booking_date']));
                    $resultpost[] = array(
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
                        "image"=>$branch_image,
                         "order_date" => $order_date1,
                  
                        'listing_type'=>$row1['vendor_id']
                    );
                    }
                }} 
                    else 
                       {$resultpost = array();}
            
                }
                if($vid=="36")
                    {
                        
                    $querys1 = $this->db->query("SELECT spa_branch.branch_name,spa_branch.user_id as branch_fit_id, spa_branch.branch_image, spa_branch.branch_phone, spa_branch.branch_address, spa_branch.pincode, spa_branch.state, spa_branch.city,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name,booking_master.amt, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time,booking_master.trainer_package_id,booking_master.trainer_id, booking_master.joining_date FROM booking_master
                                                 INNER JOIN spa_branch ON booking_master.branch_id=spa_branch.id
                                                 WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='36' and booking_master.booking_id='$booking_id' ORDER BY booking_master.id DESC");
                    $count1 = $querys1->num_rows();
                    if ($count1 > 0) 
                       {
                foreach ($querys1->result_array() as $row1) {
                    $package_id = $row1['package_id'];
                    $trainer_package_id=$row1['trainer_package_id'];
                    $trainer_id=$row1['trainer_id'];
                    
                    
                    
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
                    $package_price = $row1['amt'];
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
                   // $order_date= $trail_booking_date . " ". $trail_booking_time;
                   $booking_date1 = date('j M Y  h:i A', strtotime($row1['booking_date']));
                     $order_date1 = date('l j M Y h:i A', strtotime($booking_date1));
                       $trainer_details = array();
                      
                       $traind=explode(",",$trainer_id);
                       if(count($traind) > 1)
                       {
                          $trainer_id_new = implode("','",$traind);
                       }
                       else
                       {
                           $trainer_id_new=$trainer_id;
                       }
                        
            $personal_trainer =$this->db->query("SELECT * from personal_trainers  where id IN ('$trainer_id_new')"); 
            $count = $personal_trainer->num_rows();
            if ($count > 0) 
            {
                foreach ($personal_trainer->result_array() as $row122) 
                {
                    $id1 = $row122['id'];
                    $manager_name1 = $row122['manager_name'];
                    $qualifications1 = $row122['qualifications'];
                    
                    $fitness_trainer_pic1 = $row122['fitness_trainer_pic'];
                    $gender1 = $row122['gender'];
                    $contact1 =$row122['contact'];
                    $rating="";
                    $language = $row122['language'];
                    
                    $personal_trainer_lanaguge =$this->db->query("SELECT * from languages  where id IN ('$language')"); 
                    $row122_lang=$personal_trainer_lanaguge->row_array();
                    $language_name=$row122_lang['name'];
                    
                    
                    $trainer_details[] = array(
                                               'id'=> $id1,
                                               'trainer_name' => $manager_name1,
                                               'contact' => $contact1,
                                               'qualifications' => $qualifications1,
                                               
                                               'trainer_pic' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$fitness_trainer_pic1,
                                               'gender' => $gender1,
                                               'rating' => $rating,
                                               'language'=>$language_name,
                                              );
                    
                }
                    
            }
            else {
               $trainer_details=array();
            }

                    $resultpost[] = array(
                        'booking_id' => $booking_id,
                        'branch_name' => $branch_name,
                        'branch_id' => $branch_id,
                        'listing_id' => $listing_id,
                        'trainer_details' => $trainer_details,
                        
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
                        'order_date'=>$order_date1,
                        
                        'joining_date' => $joining_date,
                        'book_for_date' => $book_for_date,
                        'booking_date' => $booking_date,
                        'status' => $status,
                        'listing_type'=>$row1['vendor_id'],
                        "image"=>$branch_image,
                    );
                    
                   
                }} 
                    else 
                       {
                           $resultpost = array();
                           
                       }
            
                }
                
                if($vid=="12")
                {
                       $query3 = $this->db->query("SELECT booking_master.*,health_record.patient_name,nursing_attendant.address,nursing_attendant.city,nursing_attendant.state,nursing_attendant.contact,nursing_attendant.pincode,nursing_booking_details.* FROM booking_master  LEFT JOIN users ON (booking_master.user_id = users.id) LEFT JOIN health_record ON(booking_master.patient_id=health_record.id) LEFT JOIN nursing_attendant ON(booking_master.listing_id=nursing_attendant.user_id) LEFT JOIN nursing_booking_details ON(booking_master.booking_id=nursing_booking_details.booking_id) where booking_master.user_id = '$user_id' and booking_master.vendor_id = '12' and booking_master.booking_id='$booking_id' ");
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
                   if($status == null){$status = "";}if($listing_id== null){ $listing_id="";}if($booking_id== null){$booking_id="";} if($Nursing_id== null){$Nursing_id="";} 
             if($patient_name== null){  $patient_name="";}if($package_id== null){  $package_id="";} if($package_name== null){  $package_name="";} 
             if($package_amount== null){$package_amount="";}if($book_type== null){$book_type="";}if($patiente_condition== null){ $patiente_condition="";}
             if($attendent_time== null ){ $attendent_time="";}if($attendant_hour== null){ $attendant_hour="";}if($tentative_intime== null){ $tentative_intime="";}
             if($nursing_gender== null){$nursing_gender="";} if($attendant_needed== null)
                    {
                 
                      
                      $attendant_needed="";
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
                     $order_date= $joining_date ." ".$tentative_intime;
                     $order_date1 = date('l j M Y h:i A', strtotime($order_date));
                     $new_image="";
                    if(empty($package_image))
                    {
                        $new_image="";
                    }
                    else
                    {
                        $new_image="https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$package_image;
                    }
                    $resultpost[] = array(
                        'listing_id' => $listing_id,
                        'booking_id' => $booking_id,
                        'nursing_id' => $Nursing_id,
                        'appointment_date' => $booking_date,
                        'patient_name' => $patient_name,
                        'package_id' => $package_id,
                        'package_name' => $package_name,
                        'package_amount' => $package_amount,
                        'package_image' => $new_image,
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
                        'order_date' => $order_date1,
                         "image"=>$new_image,
                        'listing_type'=>$row3['vendor_id']
                    );
                    
                  
                    
                 
                }
                        } else {
                            $resultpost = array();
                        }
                }
                if($vid=="5")
                    {
                     
           $query2 = $this->db->query("select doctor_booking_master.booking_id, doctor_booking_master.old_price,doctor_booking_master.new_price,doctor_booking_master.user_id,doctor_booking_master.patient_id,
           dl.qualification,dl.experience,
                doctor_booking_master.booking_date,doctor_booking_master.listing_id,doctor_booking_master.user_id, doctor_booking_master.listing_id,doctor_booking_master.clinic_id, doctor_prescription.id as prescription_id, doctor_booking_master.booking_time,doctor_booking_master.status,doctor_booking_master.consultation_type,users.name as patient_name, dl.doctor_name from doctor_booking_master LEFT JOIN doctor_prescription ON (doctor_booking_master.booking_id = doctor_prescription.booking_id AND doctor_booking_master.listing_id = doctor_prescription.doctor_id AND doctor_booking_master.clinic_id = doctor_prescription.clinic_id) LEFT JOIN users ON (doctor_booking_master.user_id = users.id) LEFT JOIN doctor_list as dl ON(dl.user_id=doctor_booking_master.listing_id) where doctor_booking_master.user_id = '$user_id' and doctor_booking_master.booking_id='$booking_id' ");

            $count2 = $query2->num_rows();
            if ($count2 > 0) {
                foreach ($query2->result_array() as $row2) {
                 
                    $booking_id = $row2['booking_id'];
                    $booking_date = $row2['booking_date'];
                    $booking_time = $row2['booking_time'];
                    $patient_name = $row2['patient_name'];
                    $doctor_name = $row2['doctor_name'];
                    $new_price = $row2['new_price'];
                    $old_price = $row2['old_price'];
                    
                   
                    $consultation_type = $row2['consultation_type'];
                    
                    $prescription_id = $row2['prescription_id'];
                    $status = $row2['status'];
                    $doctor_id = $row2['listing_id'];
                    
                    $booking_time = str_replace('PM','', $booking_time);
                    $booking_time = str_replace('AM','', $booking_time);
                    $user_id = $row2['user_id'];
                    $patient_id= $row2['patient_id'];
                    $degree              = $row2['qualification'];
                $experience          = $row2['experience'];
                
                
                     $consultation_charges = "";
                    $branch_name = "";
                    $address = "";
                    $image = "";
                    $clinic_contact_no = "";  
                    $clinic_id=$row2['clinic_id'];
                    $doctor_id=$row2['listing_id'];
                    
                    $sql21             = "SELECT  * from doctor_clinic WHERE id='$clinic_id' and doctor_id='$doctor_id'";
                    $query_practices1  = $this->db->query($sql21);
                    $total_practices1  = $query_practices1->num_rows();
                    if($total_practices1 > 0)
                    {
                      $get_pract=$query_practices1->row_array(); 
                        $consultation_charges = $get_pract['consultation_charges'];
                        $branch_name = $get_pract['clinic_name'];
                        
                        $address = $get_pract['address'] . "," . $get_pract['city'] . "," . $get_pract['state'] . "," . $get_pract['pincode'];
                        $image = $get_pract['image'];
                        $clinic_contact_no = $get_pract['contact_no'];
                         if ($image != '') {
                            $clinic_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $image;
                        } else {
                            $clinic_image = '';
                        }
                        $lat = $get_pract['lat'];
                    $lng = $get_pract['lng'];
                    }
                    else
                    {
                        $consultation_charges = "0";
                        $sql0 = sprintf("SELECT hospital_doctor_id FROM doctor_list WHERE user_id='$doctor_id'");
                    $query_practices0  = $this->db->query($sql0);
                    $total_practices0  = $query_practices0->num_rows();
                    $get_pract0=$query_practices0->row_array();
                    $ids=$get_pract0['hospital_doctor_id'];  
                    if(!empty($ids))
                       {
                      $sql21             = "SELECT hd.*,hd.consultation from hospital_doctor_list as hd LEFT JOIN hospitals as h on (h.user_id = hd.hospital_id ) WHERE hd.id IN ($ids) and h.user_id ='$clinic_id' ";
                       //$query_dr           = $this->db->query("SELECT doctor_clinic.clinic_name,doctor_list.doctor_name FROM hospitals INNER JOIN doctor_list ON doctor_list.=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id' AND doctor_clinic.doctor_id='$listing_id'");
                        $query_practices0  = $this->db->query($sql21);
                         $count_user         = $query_practices0->num_rows();
                         if($count_user > 0)
                         {
                            
                    $get_pract01=$query_practices0->row_array();
                            $consultation_charges = $get_pract01['consultation'];
                         }
                       }
                        
                        
                        
                        
                        
                        
                      $sql21             = "SELECT  * from hospitals WHERE user_id='$clinic_id'";
                    $query_practices1  = $this->db->query($sql21);
                    $total_practices1  = $query_practices1->num_rows();
                    $get_pract=$query_practices1->row_array(); 
                    
                    $branch_name = $get_pract['name_of_hospital'];
                    $address = $get_pract['address'] . "," . $get_pract['city'] . "," . $get_pract['state'] . "," . $get_pract['pincode'];
                    $image = $get_pract['image'];
                    $clinic_contact_no = $get_pract['phone'];  
                     if ($image != '') {
                            $clinic_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $clinic_image = '';
                        }
                         $lat = $get_pract['lat'];
                    $lng = $get_pract['lng'];
                    }
                
                
                
                      if($status == null)
                    {
                        $status = "";
                    }
                     //echo $trimmed ;
                     if($prescription_id!="")
                     {
                    $url="https://doctor.medicalwale.com/prescription/".$prescription_id.".pdf";
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
                    $booking_time1 = explode('-',$booking_time);
                    $new =$booking_time1[0];
                    $order_date= $booking_date ." ".$new;
                     if ($image != '') {
                            $clinic_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/'.$image;
                        } else {
                            $clinic_image = '';
                        }
                        
                        $new_patient_id=0;
                        if($patient_id==0)
                        {
                          $new_patient_id=$user_id;
                          
                        }
                        else
                        {
                            $new_patient_id=$patient_id;
                        }
                        
                        $query_username = $this->db->query("SELECT name  FROM users WHERE id='$new_patient_id'");
                $row_username   = $query_username->row_array();
                $total_username = $row_username['name'];
                        
                         $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_id'");
                $row_rating   = $query_rating->row_array();
                $total_rating = $row_rating['total_rating'];
                if ($total_rating === NULL || $total_rating === '') {
                    $total_rating = '0';
                }
                 
                 $degree_array  = array();
                $degree_       = explode(',', $degree);
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
                 
                    $resultpost[] = array(
                        'booking_id' => $booking_id,
                        'doctor_id' => $doctor_id,
                        'patient_id' => $new_patient_id,
                        'rating' => $total_rating,
                        'experience' => $experience,
                        'doctor_degree' => $degree_array,
                        'old_price' => $old_price,
                        'new_price'=>$new_price,
                        'clinic_id'=>$clinic_id,
                        'appointment_date' => $booking_date,
                        'appointment_time' => $booking_time,
                        'patient_name' => $total_username,
                        'doctor_name' => $doctor_name,
                        'consultation_charge' => $consultation_charges,
                        'consultation_type' => $consultation_type,
                        'clinic_name' => $branch_name,
                        'clinic_address' => $address,
                        'image' => $clinic_image,
                        'clinic_contact_no' => $clinic_contact_no,
                        'status' => $status,
                        'prescription_id' => $prescription_id,
                        'lat' => $lat,
                        'lng' => $lng,
                        'prescription_pdf'=> $url,
                        "order_date" => date('l j M Y h:i A', strtotime($order_date)),
                        'listing_type'=>5
                    );
                }
            } else {
                $resultpost = array();
            }
            
                    }
                 if($vid=="10" || $vid=="31")
                    {
                       $count_query4 = $this->db->query("SELECT lb.*,bm.coupon_id,bm.coupon_discount,bm.vendor_id,bm.status from booking_master as bm LEFT JOIN lab_booking_details as lb ON(bm.booking_id = lb.booking_id) where bm.user_id='$user_id' and (bm.vendor_id='10' or bm.vendor_id='31') and bm.booking_id='$booking_id'");
                        $lab_booked4       = $count_query4->num_rows();
                        if ($lab_booked4 > 0) 
                        {
                          foreach ($count_query4->result_array() as $Lbooked) 
                            {
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
                                    $book_query = $this->db->query("SELECT status FROM booking_master WHERE booking_id='$bk_id'");
                                     $conff=$user_query->num_rows();
                                     if($conff>0){
                                    $status = $book_query->row()->status;
                                }
                                     else{
                                    $status = '';
                                }
                                     $report_path ='';
                                     $book_query_path = $this->db->query("SELECT report_path FROM reports WHERE booking_id='$bk_id'");
                                     $report_path_count =  $book_query_path->num_rows();
                                     if($report_path_count > 0)
                                       {
                                       $report_path = $book_query_path->row()->report_path;
                                 }
					if(!empty($report_pdf)){
            $report_pdf='https://d2c8oti4is0ms3.cloudfront.net/images/Labs/Reports/'.$report_pdf;
        }
					$report_pdf = $Lbooked['report_pdf']; 
                                     if($Lbooked['reference_id']!='')
                                       {
                                        $order_date1= $Lbooked['booking_date'] ." ".$Lbooked['booking_time'];
                                        $order_date = date('l j M Y h:i A', strtotime($order_date1));     
                                   $resultpost[] = array(
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
            	'report_pdf'=> $report_pdf,
                "listing_type"=> "31",
                 "order_date" => $order_date,
                    "image"=>'',
                );
                                 }
                                   }  
                                else
                                 {
                                     
                                     
                                 $report_pdf = $Lbooked['report_pdf']; 
                                 $user_id = $Lbooked['user_id'];
                                 $user_query = $this->db->query("SELECT email,phone,name FROM users WHERE id='$user_id'");
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
                                $patient_id = $Lbooked['patient_id'];
                                $listing_id = $Lbooked['listing_id'];
                                $listing_query = $this->db->query("SELECT latitude,longitude FROM lab_center WHERE user_id='$listing_id'");
                                      $con_id1=$listing_query->num_rows();
                                    if($con_id1 > 0)
                                    {
                                        $lat = $listing_query->row()->latitude;
                                        $lng = $listing_query->row()->longitude;
                                              
                                    }
                                    else
                                    {
                                         $lat = "";
                                        $lng = "";
                                    
                                    }      
                                
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
                                $amount=0;
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
                                        
                                            $amount +=$price;
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
                                    $amount +=$pack_amount;
                                    }
                                }
                                
                                
                                if($status == null)
                                {
                                    $status = "";
                                }
                                
                                
                                $order_date1= $Lbooked['booking_date'] ." ".$Lbooked['booking_time'];
                                $order_date = date('l j M Y h:i A', strtotime($order_date1));     
                                $resultpost_[] = array(
                                    'user_id'=> $user_id,
                                    'user_name'=>$user_name,
                                    'amount'=> (string)$amount,
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
                                    'status'=>$status,
                                    'lat'=>$lat,
                                    'lng'=>$lng,
                                    "order_date" => $order_date,
                                    "image"=>'',
                                );
                                
                                
        $query = $this->db->query("SELECT lt.booking_id,lt.listing_id as vendor_id,lt.vendor_id as listing_type,lt.id,lab_booking_details.address_id,lab_booking_details.created_at,lt.patient_id as member_id,lt.user_id as parent_id,lt.booking_time,ud.address2,ud.address_type,ud.city,ud.address1,ud.full_address,ud.landmark,ud.lat,ud.lng,ud.mobile,ud.name,ud.pincode,ud.relation_ship as relation,ud.state,pm.payment_method,pm.icon,pm.id as payment_id,lab_booking_details.test_id,lab_booking_details.total_cost,lab_booking_details.total_discount,lab_booking_details.amount,user_vendor_ledger.ledger_id,user_vendor_ledger.transaction_id from booking_master lt LEFT JOIN lab_booking_details on lab_booking_details.booking_id=lt.booking_id LEFT JOIN user_address ud on ud.address_id=lab_booking_details.address_id LEFT JOIN payment_method pm on pm.id=lt.payment_mode LEFT JOIN user_vendor_ledger on user_vendor_ledger.invoice_no=lt.booking_id where lt.booking_id='$booking_id' limit 1");
		$num_count  = $query->num_rows();
        if ($num_count > 0) {
        $list = $query->row_array();
        $vendor_id=$list['vendor_id'];
        $test_array=array();
        $package_array=array();
        
        $test_query = $this->db->query("SELECT lab_test_master.id,ld.code,ld.report_availability,ld.home_collection_charges,ld.price,ld.discounted_price,ld.discount,ld.medicalwale_discount,lab_test_master.name from lab_test_master_details ld INNER JOIN lab_test_master on lab_test_master.id=ld.test_id INNER JOIN lab_booking_details on FIND_IN_SET(lab_test_master.id,lab_booking_details.test_id) where lab_booking_details.booking_id='$booking_id' and ld.user_id='$vendor_id' group by lab_test_master.id");
        $test_count  = $test_query->num_rows();
                if ($test_count > 0) {
                    $test_row = $test_query->result();
                    foreach ($test_row as $test_list) {
                        $testid  = $test_list->id;
                        $test_name  = $test_list->name;
                        $code  = $test_list->code;
                        $report_availability  = $test_list->report_availability;
                        $home_collection_charges  = $test_list->home_collection_charges;
                        $price  = $test_list->price;
                        $discounted_price  = $test_list->discounted_price;
                        if($discounted_price==0){
                            $discounted_price = $test_list->price;    
                        }
                        $discount  = $test_list->discount;
                        $medicalwale_discount  = $test_list->medicalwale_discount;
                        $test_array[] = array(
                            'type' => 'TEST',
                            'test_id' => $testid,
                            'test_name' => $test_name,
                            'code' => $code,
                            'report_availability' => '',
                            'home_collection_charges' => $home_collection_charges,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'discount' => $discount,
                            'medicalwale_discount' => $medicalwale_discount,
                            'home_delivery' => "0"
                        );
                    }
                }
                
        $test_query = $this->db->query("SELECT ld.id,ld.code,ld.home_collection_charges,ld.price,ld.discounted_price,ld.discount,ld.medicalwale_discount,ld.name from lab_package_master ld INNER JOIN lab_booking_details on FIND_IN_SET(ld.id,lab_booking_details.package_id) where lab_booking_details.booking_id='$booking_id' group by ld.id");
        $test_count  = $test_query->num_rows();
                if ($test_count > 0) {
                    $test_row = $test_query->result();
                    foreach ($test_row as $test_list) {
                        $testid  = $test_list->id;
                        $test_name  = $test_list->name;
                        $code  = $test_list->code;
                        $report_availability  = '';
                        $home_collection_charges  = $test_list->home_collection_charges;
                        $price  = $test_list->price;
                        $discounted_price  = $test_list->discounted_price;
                        if($discounted_price==0){
                            $discounted_price = $test_list->price;    
                        }
                        $discount  = $test_list->discount;
                        $medicalwale_discount  = $test_list->medicalwale_discount;
                        $test_array[] = array(
                            'type' => 'PACKAGE',
                            'test_id' => $testid,
                            'test_name' => $test_name,
                            'package_id' => $testid,
                            'name' => $test_name,
                            'code' => $code,
                            'report_availability' => '',
                            'home_collection_charges' => $home_collection_charges,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'discount' => $discount,
                            'medicalwale_discount' => $medicalwale_discount,
                            'home_delivery' => "0"
                        );
                    }
                }
        
        $transaction_id=0;
        $ledger_id=0;
        $vendor_id=$list['vendor_id'];
        $listing_type=$list['listing_type'];
        $booking_id=$list['booking_id'];
        $ledger_id=$list['ledger_id'];
        $transaction_id=$list['transaction_id'];
        $certification='';
        $contact='';
        $feature = $this->db->query("SELECT GROUP_CONCAT(feature) as feature FROM `lab_features` LEFT JOIN lab_center on FIND_IN_SET(lab_features.id, lab_center.features) where lab_center.user_id='$vendor_id'")->row_array();
        $certification = $feature['feature'];
        
        $vendor_detail = $this->db->query("SELECT lab_center.lab_name,lab_center.contact_no,lab_center.profile_pic FROM `lab_center` INNER JOIN users on users.id=lab_center.user_id where lab_center.user_id='$vendor_id'")->row_array();
        $vendor_name   = $vendor_detail['lab_name'];
        $contact   = $vendor_detail['contact_no'];
        $image = $vendor_detail['profile_pic'];
         $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
        
        
        
        $prescription_list = array(); 
        $lab_prescriptions = $Lbooked['prescriptions'];  
        if(!empty($lab_prescriptions)){
            $lab_prescriptions = explode(',',$lab_prescriptions);
            foreach ($lab_prescriptions as $prescription_img) {
                $prescription_list[] = array(
                    'image'=>'https://d2c8oti4is0ms3.cloudfront.net/images/lab_prescription/'.$prescription_img
                );
            }
            $vendor_name='Medicalwale Labs';
        }
        if($ledger_id<1){
            $ledger_id='0';
        }
        if($transaction_id==NULL){
            $transaction_id='';
        }
        

        $coupon_id=$Lbooked['coupon_id'];
        $coupon_discount=$Lbooked['coupon_discount'];
        if(!empty($report_pdf)){
            $report_pdf='https://d2c8oti4is0ms3.cloudfront.net/images/Labs/Reports/'.$report_pdf;
        }
        $resultpost[] = array(
            'booking_id'=> $booking_id,
            'ledger_id'=> $ledger_id,
            'transaction_id'=> $transaction_id,
            'listing_type'=>$listing_type,
            'status'=>$status,
            'address1' => $list['address1'],
            'address2' => $list['address2'],
            'address_id' => $list['address_id'],
            'address_type' => $list['address_type'],
            'city' => $list['city'],
            'date' => $list['created_at'],
            'full_address' => $list['full_address'],
            'landmark' => $list['landmark'],
            'lat' => $list['lat'],
            'lng' => $list['lng'],
            'member_id' => $list['member_id'],
            'mobile' => $list['mobile'],
            'name' => $list['name'],
            'parent_id' => $list['parent_id'],
            'icon' => $list['icon'],
            'payment_id' => $list['payment_id'],
            'payment_method' => $list['payment_method'],
            'pincode' => $list['pincode'],
            'relation' => $list['relation'],
            'state' => $list['state'],
            'time' => $list['booking_time'],
            'image'=> $image,
            'vendor_id' => $vendor_id,
            'vendor_name' => $vendor_name,
            'contact' => $contact,
            'certification' => $certification,
            'rating' => 0,
            'coupon_id' => $coupon_id,
            'coupon_discount' => $coupon_discount,
            'prescription_list' => $prescription_list,
            'test_list' => $test_array,
            'report_pdf'=> $report_pdf
        );

		}
		else{
			$resultpost = array(); 
		}
                                
                                
                                
                     }
                            }       
                        }
                       else
                        {
                         $resultpost = array(); 
                        }
                    }    
                if($vid=="34")
                   {
                       
                        $pro_list = array();
                        $results = $this->db->query("SELECT pm.payment_method as payment_method_name, vd.delivery_by_medicalwale, uo.* FROM `user_order` as uo left join vendor_details_hm as vd on (uo.`listing_id` = vd.v_id) LEFT JOIN payment_method as pm on (uo.payment_method = pm.id) WHERE uo.`user_id` = '$user_id' AND  uo.`listing_type`='34' and uo.invoice_no='$booking_id' ");
                      
                        foreach($results->result_array() as $order){
                            
                                 $new_date=$order['order_date'];
                              $order_date = date('l j M Y h:i A', strtotime($new_date));
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
                            $order += ['image'=>""];
                           foreach($order as $key => $value)
                                {
                                    if (array_key_exists("order_date",$order))
                                          {
                                            $order['order_date']=$order_date;
                                          }
                                    if($value == null){
                                        $order[$key] = "";
                                    }
                                }
                                
                          
                                
                            $resultpost[] = $order;
                        }
                   }
                if($vid=="8" || $vid=="50" )
                {
                     $resultpost61 = array();
       // IPD
       
       $booking_details = $this->db->query("SELECT * FROM booking_master WHERE user_id='$user_id' and vendor_id='8' and booking_id='$booking_id'");
       
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
              
              
              $hosp_details = $this->db->query("SELECT * FROM `hospitals`  WHERE user_id='$listing_id'");
              $hosp_count = $hosp_details->num_rows();
              $lat ='';
                $lng ='';
              $booking_address='';
              $booking_mobile='';
              if($hosp_count>0)
              {
             $lat = $hosp_details->row()->lat;
             $lng = $hosp_details->row()->lng;
             $booking_address = $hosp_details->row()->map_location;
            $booking_mobile = $hosp_details->row()->phone;
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
              else
              {
                  $package_name="";
              }
              
              $patient_id = $row['patient_id'];
              $patient_detaild = $this->db->query("SELECT name,phone,email,gender FROM users WHERE id='$patient_id' ");
              $patient_count = $patient_detaild->num_rows();
             
              if($patient_count>0)
              {
                $name = $patient_detaild->row()->name;
                $phone = $patient_detaild->row()->phone;
                $email = $patient_detaild->row()->email;
                if(empty($email))
                {
                   $email=""; 
                }
                $gender = $patient_detaild->row()->gender;
                if(empty($gender))
                {
                   $gender=""; 
                }
              }
              else
              {
                $name = "";
                $phone = "";
                $email = "";
                $gender = "";  
              }
              
              
              $user_id = $row['user_id'];
              $patient_id = $row['patient_id'];
             
             $branch_id = $row['branch_id'];
             $vendor_id=$row['vendor_id'];
            $ex=explode(' ',$row['booking_date']);
            $booking_time=$hospital_details->row()->booking_time;
            $booking_date = $row['booking_date'];
            $status = $row['status'];
            $joining_date = $row['joining_date'];
            $category_id = $row['category_id'];
            
            if($category_id=="")
            {
                $category_id="";
            }
             $order_date1 = $booking_date." ".$booking_time;
              $order_date = date('l j M Y h:i A', strtotime($booking_date));
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
                     "order_date" =>$order_date,
                      "lat" =>$lat,
                       "lng" =>$lng,
                    "image"=>'',
                    'booking_type'=>"IPD"
                );
            
           }
        }
        
       // OPD
      $query= $this->db->query("select hospital_booking_master.booking_id,hospital_booking_master.new_price,hospital_booking_master.old_price,hospital_booking_master.booking_date,hospital_booking_master.listing_id,hospital_booking_master.user_id,hospital_booking_master.listing_id,hospital_booking_master.patient_id,
hospital_booking_master.doctor_id,hospital_doctor_prescription.id as prescription_id,hospital_booking_master.booking_time,hospital_booking_master.status,hospital_booking_master.consultation_type,
hospitals.name_of_hospital,hospitals.lat,hospitals.lng,users.name as patient_name, hospitals.address,hospitals.image,hospitals.state,hospitals.city,hospitals.pincode, dl.consultation,hospitals.phone,dl.doctor_name from hospital_booking_master  LEFT JOIN hospital_doctor_prescription 
ON (hospital_booking_master.patient_id = hospital_doctor_prescription.patient_id AND hospital_booking_master.doctor_id = hospital_doctor_prescription.doctor_id AND hospital_booking_master.listing_id = hospital_doctor_prescription.hospital_id) LEFT JOIN hospitals on (hospital_booking_master.listing_id = hospitals.user_id)
LEFT JOIN users ON (hospital_booking_master.user_id = users.id)  LEFT JOIN hospital_doctor_list as dl ON(dl.id=hospital_booking_master.doctor_id)
where hospital_booking_master.user_id = '$user_id' and hospital_booking_master.booking_id='$booking_id'");

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
                    $doctor_id = $row['doctor_id'];
                    $clinic_id =$row['listing_id'];
                     $lat = $row['lat'];
                    $lng = $row['lng'];
                      if($status == null)
                    {
                        $status = "";
                    }
                       if($branch_name == null)
                    {
                        $branch_name = "";
                    }
                       if($image !='')
                    {
                        $clinic_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                    }
                    else
                    {
                        $clinic_image="";
                    }
                       if($prescription_id == null)
                    {
                        $prescription_id = "";
                    }
                       if($clinic_contact_no == null)
                    {
                        $clinic_contact_no = "";
                    }
                     $user_id = $row['user_id'];
                    $patient_id= $row['patient_id'];
                        $new_patient_id=0;
                        if($patient_id==0)
                        {
                          $new_patient_id=$user_id;
                          
                        }
                        else
                        {
                            $new_patient_id=$patient_id;
                        }
                    $booking_time1 = explode('-',$booking_time);
                    $new =$booking_time1[0];
                    $order_date1= date("Y-m-d",strtotime($booking_date)) ." ".$new;
$degree_array=array();
$total_rating='3';
$new_price = $row['new_price'];
$old_price = $row['old_price'];
$experience="";
             $order_date = date('l j M Y h:i A', strtotime($order_date1));
             
                    $resultpost61[] = array(
                        'booking_id' => $booking_id,
                        'doctor_id' => $doctor_id,
                        'order_date' => $order_date,
                        'patient_id' => $new_patient_id,
                        'rating' => $total_rating,
                        'experience' => $experience,
                        'doctor_degree' => $degree_array,
                        'old_price' => $old_price,
                        'new_price'=>$new_price,
                        'appointment_date' => $booking_date,
                        'appointment_time' => $booking_time,
                        'clinic_id'=>$clinic_id,
                        
                        'patient_name' => $patient_name,
                        'doctor_name' => $doctor_name,
                        'consultation_charge' => $consultation_charges,
                        'consultation_type' => $consultation_type,
                        'clinic_name' => $branch_name,
                        'clinic_address' => $address,
                        'image' => $clinic_image,
                        'clinic_contact_no' => $clinic_contact_no,
                        'status' => $status,
                        'prescription_id' => $prescription_id,
                         'lat' => $lat,
                        'lng' => $lng,
                        'booking_type'=>"OPD",
                         "listing_type"=> "50",
                         'prescription_pdf'=> "",
                        "order_date" => date('l j M Y h:i A', strtotime($order_date))
                       
                    );
                }
            }
               
             
                         $resultpost=$resultpost61;
                function cmp($a, $b)
{
    $aDateTime = new DateTime($a["order_date"]);
    $bDateTime = new DateTime($b["order_date"]);

    return $aDateTime < $bDateTime ? 1 : -1;
};

usort($resultpost, "cmp");   
               
               
               
               
               
               
               
               
               
               
               
               
                }
                
              if($vid=="21")
              {
                  $query2 = $this->db->query("select * from rides where user_id = '$user_id' and ride_id='$booking_id'");

            $count2 = $query2->num_rows();
            if ($count2 > 0) {
                foreach ($query2->result_array() as $row2) {
                 
                    $booking_id = $row2['ride_id'];
                    $booking_date = $row2['Create_time'];
                    $status = $row2['status'];
                    $patient_name = $row2['user_name'];
                    $patient_mobile = $row2['user_mobile'];
                    $payment_details = "Cash At Point";
                    $from = $row2['pickup_adress'];
                    $to = $row2['drop_address'];
                    $driver_name = $row2['driver_name'];
                    $driver_mobile = $row2['driver_mobile'];
                    $ambulance_type = $row2['subtype_name'];
                    $price = $row2['price'];
                    $discount = "0";
                    $total=$row2['price'];
                    $dirver_id=$row2['driver_id'];
                    $amb="";
                    $query1 = $this->db->query("select * from ambulance_fare where driver_user_id = '$dirver_id'");
                    $count1 = $query1->num_rows();
                    if ($count1 > 0) {
                        $row1=$query1->row_array();
                        $amb=$row1['amb_no'];
                    }
                    else
                    {
                        $amb="";
                    }
                        
                    $resultpost[] = array(
                        'booking_id' => $booking_id,
                        'order_date' => date('l j M Y h:i A', strtotime($booking_date)),
                        'status' => $status,
                        'dirver_id' => $dirver_id,
                        'patient_name' => $patient_name,
                        'patient_mobile' => $patient_mobile,
                        'payment_type' => $payment_details,
                        'from' => $from,
                        'to' => $to,
                        'driver_name' => $driver_name,
                        'driver_mobile' => $driver_mobile,
                        'ambulance_no' => $amb,
                        
                        'ambulance_type' => $ambulance_type,
                        'price'=> $price,
                        'discount'=>$discount,
                        'listing_type'=>"21",
                        'total_amount'=>$price,
                        'image'=>""
                    );
                }
            } else {
                $resultpost = array();
            }
              }
            
              if($vid=="39")
                    {
                        $package_name = "";
                    	$package_details = "";
            			$package_discount = "";
            			$package_price = "";
            			$lat = $lng = "";
            			
            			
                         $sql_query = "SELECT dcp.image as prescription_image,payment_method.payment_method as payment_method_name,booking_master.sub_booking_type
,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id,
booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender,
booking_master.branch_id,booking_master.status,booking_master.booking_type,booking_master.payment_mode,
booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date,
booking_master.trail_booking_time,booking_master.trainer_package_id,booking_master.trainer_id,
booking_master.joining_date
FROM booking_master left JOIN
payment_method on (booking_master.payment_mode = payment_method.id)  left join Dental_clinic_prescription as dcp on dcp.order_id = booking_master.id  
WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='39' and booking_master.booking_id='$booking_id' group by booking_master.booking_id  ORDER BY booking_master.id DESC";
                    
                    //   echo $sql_query; die();
                    //   prescription_image
                      $querys1 = $this->db->query($sql_query);
                  // echo "SELECT dentists_branch.name_of_hospital, dentists_branch.id as branch_id ,dentists_branch.dentists_branch_user_id as branch_dentist_id, dentists_branch.image, dentists_branch.phone, dentists_branch.address, dentists_branch.pincode, dentists_branch.state, dentists_branch.city,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status,booking_master.booking_type,booking_master.payment_mode, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time,booking_master.trainer_package_id,booking_master.trainer_id, booking_master.joining_date,dentists_clinic_list.name_of_hospital,dentists_clinic_list.address,dentists_clinic_list.state,dentists_clinic_list.city,dentists_clinic_list.phone,booking_master.branch_id FROM booking_master left JOIN dentists_branch ON booking_master.branch_id=dentists_branch.id left join dentists_clinic_list on dentists_clinic_list.user_id=booking_master.listing_id  
                 //  WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='39' and booking_master.booking_id='$booking_id' ORDER BY booking_master.id DESC";
                    $count1 = $querys1->num_rows();
                    if ($count1 > 0) 
                       {
                          
                        foreach ($querys1->result_array() as $row1) {
                            
                        //    print_r($row1);
                            
                         $listing_id = $row1['listing_id'];
                        $branch_id = "";
                        $branch_name = "";
                        $branch_image = "";
                        $branch_phone = "";
                        $branch_address = "";
                        $branch_pincode = "";
                        $branch_city = "";
                        $branch_state = "";
                        $lat = "";
                        $lng = "";
                        
                        $query192 = $this->db->query("SELECT dentists_branch.lat,dentists_branch.lng,dentists_branch.name_of_hospital, dentists_branch.id as branch_id ,dentists_branch.dentists_branch_user_id as branch_dentist_id, dentists_branch.image, dentists_branch.phone, dentists_branch.address, dentists_branch.pincode, dentists_branch.state, dentists_branch.city from dentists_branch where (hub_user_id='$listing_id' OR dentists_branch_user_id='$listing_id') ");
                        $row192=$query192->row_array();    
                        $count12 = $query192->num_rows();
                        if ($count12 > 0) 
                           {
                                $branch_id = $row192['branch_id'];
                                $branch_name = $row192['name_of_hospital'];
                                $branch_image = $row192['image'];
                                $branch_phone = $row192['phone'];
                                $branch_address = $row192['address'];
                                $branch_pincode = $row192['pincode'];
                                $branch_city = $row192['city'];
                                $branch_state = $row192['state'];
                                 $lat = $row192['lat'];
                        $lng = $row192['lng'];
                           } 
                           
                         $query1921 = $this->db->query("SELECT dentists_clinic_list.lat,dentists_clinic_list.lng,dentists_clinic_list.name_of_hospital,dentists_clinic_list.image, dentists_clinic_list.phone, dentists_clinic_list.address, dentists_clinic_list.pincode, dentists_clinic_list.state, dentists_clinic_list.city from dentists_clinic_list where user_id='$listing_id' ");
                        $row1921=$query1921->row_array();    
                        $count123 = $query1921->num_rows();
                        if ($count123 > 0) 
                           {
                                $branch_id = "";
                                $branch_name = $row1921['name_of_hospital'];
                                $branch_image = $row1921['image'];
                                $branch_phone = $row1921['phone'];
                                $branch_address = $row1921['address'];
                                $branch_pincode = $row1921['pincode'];
                                $branch_city = $row1921['city'];
                                $branch_state = $row1921['state'];
                                $lat = $row1921['lat'];
                        $lng = $row1921['lng'];
                           }          
                            
                            
                        $package_id = $row1['package_id'];
                        $trainer_package_id=$row1['trainer_package_id'];
                        $trainer_id=$row1['trainer_id'];
                        $sub_booking_type = $row1['sub_booking_type'];
                        $prescription_image = $row1['prescription_image'];
                    if($sub_booking_type == 1){
                        
                        $query12 = $this->db->query("SELECT packages.package_name, packages.package_details, packages.price,packages.discount from packages  where packages.id ='$package_id' ");
                        $row12=$query12->row_array();
                        //package
                        if(sizeof($row12) > 0){
                            $package_name = $row12['package_name'] != null ? $row12['package_name']  : ""  ;
                            $package_details = $row12['package_details'] != null ? $row12['package_details']  : ""  ;
                            $package_price = $row12['price'] != null ? $row12['price']  : ""  ;
                            $package_discount = $row12['discount'] != null ? $row12['discount']  : ""  ;
                        }
                    } else  if($sub_booking_type == 2){
                        // treatment
                        $query_treatment = $this->db->query("SELECT * FROM `dental_services_offered` WHERE `id` = '$package_id' ")->row_array();
                        if(sizeof($query_treatment) > 0){
                            
                            $package_name = $query_treatment['service_name'];
                            $package_price = $query_treatment['price'];
                            $package_discount = $query_treatment['discount'];
                            $package_details = $query_treatment['description'];
                        }
                    } else {
                        $package_name = "";
                    	$package_details = "";
            			$package_discount = "";
            			$package_price = "";
                    }
               
                    $booking_id = $row1['booking_id'];
                   
                     
                    $branch_id = $row1['branch_id'];
                    
                  
                    $appointment_user_name = $row1['user_name'];
                    $appointment_user_mobile = $row1['user_mobile'];
                    $appointment_user_email = $row1['user_email'];
                    $category_id = $row1['category_id'];
                    $status = $row1['status'];
                    $booking_type = $row1['booking_type'];
                    
				
                    /*if(empty($row12['package_name']) || empty($row12['package_details']) || $row12['price']== null || $row12['discount']== null ){
                       	
                    }else{
                        
                    		$package_name = $row12['package_name'];
                    		$package_details = $row12['package_details'];
			    	$package_price = $row12['price'];
			     	$package_discount = $row12['discount'];
                    }*/
                    /*Added by swapnali */
                    
                    $trail_booking_date = $row1['trail_booking_date'];
                    $trail_booking_time = $row1['trail_booking_time'];
                    $joining_date = date('j M Y', strtotime($row1['joining_date']));
                    $book_for_date = date('d/m/Y', strtotime($row1['joining_date']));
                    
                    $is_free_trial = 'No';
                    date_default_timezone_set('Asia/Kolkata');
                  
                    $booking_date = date('j M Y | h:i A', strtotime($row1['booking_date']));

                    if($booking_type!="free"){
                        
                        $payment_mode= $row1['payment_mode'];
                        $payment_method_name = $row1['payment_method_name'] != null ? $row1['payment_method_name']  : "" ;
                    }else{
                        
                        $payment_mode="";
                        $payment_method_name = "";
                    }

                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
                    }
                    
                    // $prescription_image
                    if ($prescription_image != '') {
                        $prescription_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/Dental_prescription/' . $prescription_image;
                    } else {
                        $prescription_image = '';
                    }
                    
                    
                    if($status == null)
                    {
                        $status = "";
                    }
                    // 11 Dec 2019 | 03:05 PM
                   
                    
                     $trail_booking_date1 = date('l j M Y', strtotime($trail_booking_date));
                     
                      $booking_date= $trail_booking_date1 . " ". $trail_booking_time;
                     
                   $booking_date1 = date('j M Y  h:i A', strtotime($row1['booking_date']));
                     $order_date1 = date('l j M Y h:i A', strtotime($booking_date1));
                


                    $resultpost[] = array(
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
                        'order_date'=>$order_date1,
                        'package_discount'=>$package_discount,
                      //  'joining_date' => $joining_date,
                        //'book_for_date' => $book_for_date,
                        'booking_date' => $booking_date,
                        'status' => $status,
                        'payment_mode'=>$payment_mode,
                        'payment_method_name' => $payment_method_name,
                        'listing_type'=>$row1['vendor_id'],
                        "image"=>$prescription_image,
                        "lat" => $lat,
                        "lng" => $lng,
                    );
                    
                   
                }
                           
                       } 
                    else
                    {
                        $resultpost=array();
                    }
            
                }  
             
                
             return $resultpost;
             
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
                $tracker = $this->All_booking_model->pharmacy_tracker($user_id, $invoice_no);
                
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
                    "tracker" => $tracker,
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
                    "tracker" => $tracker,
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
            $tracker = $this->All_booking_model->pharmacy_tracker($user_id, $invoice_no);
            
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
                    "tracker" => $tracker,
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
                    "tracker" => $tracker,
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
     public function check_time_format($time) {
        $time_filter = preg_replace('/\s+/', '', $time);
        $final_time = date("h:i A", strtotime($time_filter));
        return $final_time;
    }
      public function is_free_delivery_staus($free_start_time, $free_end_time) {
        $free_start_time_st = date("h:i A", strtotime($free_start_time));
        $free_end_time_st = date("h:i A", strtotime($free_end_time));
        $current_time_st = date('h:i A');


        $date1 = DateTime::createFromFormat('H:i a', $current_time_st);
        $date2 = DateTime::createFromFormat('H:i a', $free_start_time_st);
        $date3 = DateTime::createFromFormat('H:i a', $free_end_time_st);

        if ($date2 < $date3 && $date1 <= $date3) {
            $date3->modify('+1 day')->format('H:i a');
        } elseif ($date2 > $date3 && $date1 >= $date3) {
            $date3->modify('+1 day')->format('H:i a');
        }


        if ($date1 > $date2 && $date1 < $date3) {
            $is_free_delivery = 'Yes';
        } else {
            $is_free_delivery = 'No';
        }

        return $is_free_delivery;
    }
     public function check_day_status($day_type, $days_closed, $is_24hrs_available, $store_open, $store_close) {

        if ($is_24hrs_available === 'Yes') {
            if ($day_type == 'Monday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Tuesday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Wednesday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Thursday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Friday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Saturday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Sunday') {
                $time = '12:00 AM-11:59 PM';
            }
        } else {
            if ($day_type == 'Monday') {
                if ($days_closed == 'Monday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Tuesday') {
                if ($days_closed == 'Tuesday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Wednesday') {
                if ($days_closed == 'Wednesday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Thursday') {
                if ($days_closed == 'Thursday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Friday') {
                if ($days_closed == 'Friday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Saturday') {
                if ($days_closed == 'Saturday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Sunday') {
                if ($days_closed == 'Sunday Closed') {
                    $time = 'close-close';
                } elseif ($days_closed == 'Sunday Half Day') {
                    $time = $store_open . '-02:00 PM';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }
        }

        return $time;
    }
     public function check_current_delivery_charges($is_24hrs_available, $day_night_delivery, $free_start_time, $free_end_time, $night_delivery_charge, $days_closed, $is_min_order_delivery, $min_order) {

        date_default_timezone_set('Asia/Kolkata');
        $free_start_time_st = date("h:i A", strtotime($free_start_time));
        $free_end_time_st = date("h:i A", strtotime($free_end_time));
        $current_time_st = date('h:i A');

        $current_time = DateTime::createFromFormat('H:i a', $current_time_st);
        $system_start_time = DateTime::createFromFormat('H:i a', $free_start_time_st);
        $system_end_time = DateTime::createFromFormat('H:i a', $free_end_time_st);

        if ($system_start_time < $system_end_time && $current_time <= $system_end_time) {
            $system_end_time->modify('+1 day')->format('H:i a');
        } elseif ($system_start_time > $system_end_time && $current_time >= $system_end_time) {
            $system_end_time->modify('+1 day')->format('H:i a');
        }

        if ($is_24hrs_available == 'Yes') {
            if ($day_night_delivery == 'Yes') {

                if ($current_time > $system_start_time && $current_time < $system_end_time) {
                    if ($is_min_order_delivery == 'Yes') {
                        $current_delivery_charges = 'Free Delivery Available Above Rs ' . $min_order;
                    } else {
                        $current_delivery_charges = 'Delivery Not Available Below Rs ' . $min_order;
                    }
                } else {
                    $current_delivery_charges = 'Delivery Charges Applied Rs ' . $night_delivery_charge;
                }
            } else {
                if ($current_time > $system_start_time && $current_time < $system_end_time) {
                    // $current_delivery_charges = 'Free Delivery Available';   
                    if ($is_min_order_delivery == 'Yes') {
                        $current_delivery_charges = 'Free Delivery Available Above Rs ' . $min_order;
                    } else {
                        $current_delivery_charges = 'Delivery Not Available Below Rs ' . $min_order;
                    }
                } else {
                    $current_delivery_charges = 'Delivery Not Available Now';
                }
            }
        } else {

            if ($current_time > $system_start_time && $current_time < $system_end_time) {
                //$current_delivery_charges = 'Free Delivery Available';
                if ($is_min_order_delivery == 'Yes') {
                    $current_delivery_charges = 'Free Delivery Available Above Rs ' . $min_order;
                } else {
                    $current_delivery_charges = 'Delivery Not Available Below Rs ' . $min_order;
                }
            } else {
                $current_delivery_charges = 'Delivery Not Available Now';
            }
        }


        return $current_delivery_charges;
    }
    public function all_vendor_list($user_id,$mlat, $mlng,$page,$vendor_id,$keyword)
    {
          $radius = $page*500;
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
       $resultpost_branch=array();
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

        if($vendor_id == '13') 
        {
            
          if($keyword!="")
          {
               $sql   = sprintf("SELECT mba,recommended,certified,discount_description,medicalwale_discount,otc_discount,ethical_discount,surgical_discount,perscribed_discount,generic_discount,`id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND visible <> '0' AND type <> 'branch' AND medical_name LIKE '%%$keyword%%'   HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
           }
          else
          {
            
            $sql = sprintf("SELECT mba,recommended,certified,discount_description,medicalwale_discount,otc_discount,ethical_discount,surgical_discount,perscribed_discount,generic_discount,`id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND visible <> '0' AND type <> 'branch' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
          }
            $query = $this->db->query($sql);
            $count = $query->num_rows();
            if($count>0){
                foreach ($query->result_array() as $row) {
                     $offer_discount = array();
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $medical_id = $row['user_id'];
                    $medical_name = $row['medical_name'];
                    $store_manager = $row['store_manager'];
                    $address1 = $row['address1'];
                    $address2 = $row['address2'];
                    $pincode = $row['pincode'];
                    $city = $row['city'];
                    $state = $row['state'];
                    $contact_no = $row['contact_no'];
                    $whatsapp_no = $row['whatsapp_no'];
                    $email = $row['email'];
                    $store_since = $row['store_since'];
                    $chat_id = $row['user_id'];
         
                     $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                     $profile_pic = $row['profile_pic'];
                    if ($row['profile_pic'] != '') {
                        $profile_pic = $row['profile_pic'];
                        $profile_pic = str_replace(' ', '%20', $profile_pic);
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                    } else {
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
                    }
                 
                       $distance_root =  round(($distances/1000),1);
                      $FINAL_RESULT = array();
                        
                    $resultpost0[] = array(
                        'id' => $medical_id,
                        'medical_name' => $medical_name,
                        'listing_id' => $chat_id,
                        'listing_type' => '13',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'store_manager' => $store_manager,
                        'address' => $address1.','.$address2,
                       
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $contact_no,
                        'whatsapp_no' => $whatsapp_no,
                        "exotel_no" => '02233721563',
                        'email' => $email,
                        'distance' => $distance_root,
                        'distance_root'=>$distance_root,
                        'profile_pic' => $profile_pic
                       
                    );
                }
               
                //added for generico pharmacy branch 
                
                $radius_branch = '5';
                $sql_branch = sprintf("SELECT mba,recommended,certified,user_id,discount_description,medicalwale_discount,`id`, surgical_discount,perscribed_discount,`pharmacy_branch_user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance,otc_discount,ethical_discount,generic_discount  FROM medical_stores  WHERE is_active= '1' AND visible = '1' AND type = 'branch' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius_branch));
           
            $query_branch = $this->db->query($sql_branch);
            $count_branch = $query_branch->num_rows();
            if($count_branch>0){
               foreach ($query_branch->result_array() as $row) {
                   $offer_discount = array();
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $medical_id = $row['pharmacy_branch_user_id'];
                    $medical_user_id = $row['user_id'];
                    $medical_name = $row['medical_name'];
                    $store_manager = $row['store_manager'];
                    $address1 = $row['address1'];
                    $address2 = $row['address2'];
                    $pincode = $row['pincode'];
                    $city = $row['city'];
                    $state = $row['state'];
                    $contact_no = $row['contact_no'];
                    $whatsapp_no = $row['whatsapp_no'];
                    $email = $row['email'];
            
                    $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
             
                    $distance_root =  round(($distances/1000),1);
                     $profile_pic = $row['profile_pic'];
                    if ($row['profile_pic'] != '') {
                        $profile_pic = $row['profile_pic'];
                        $profile_pic = str_replace(' ', '%20', $profile_pic);
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                    } else {
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
                    }
                    $resultpost_branch[] = array(
                        'id' => $medical_id,
                        'name' => $medical_name,
                        'listing_id' => $chat_id,
                        'listing_type' => '13',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $address1.','.$address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $contact_no,
                        'email' => $email,
                        'distance' => $distance_root,
                        'profile_pic' => $profile_pic
                       
                    );
                    
                   
                   // array_push($resultpost,$resultpost_branch);
                } 
            }
           
                    usort($resultpost0, function($a, $b) {
                                    $a = $a['distance'];
                                    $b = $b['distance'];
                        if ($a == $b) { return 0; }
                            return ($a < $b) ? -1 : 1;
                        });
                        
                        
           if($resultpost_branch!="")
          {
              //echo 'no branch';
             return $resultpost0  =  array_merge($resultpost_branch,$resultpost0);
          }
          else
          {
           return $resultpost0  =  $resultpost0;  
          }
                   
              
            }
            else{
            return  $resultpost0= array();
                
            }
        }
     
      
        else if($vendor_id=='6')
        {
             if($keyword!="")
          {
              
             $sql =   sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time,fcb.user_discount, fcb.to_trail_time, ( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM fitness_center_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1' AND fcb.branch_name LIKE '%%$keyword%%' HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
           }
          else
          {
             $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time,fcb.user_discount, fcb.to_trail_time, ( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM fitness_center_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
          }
            $query = $this->db->query($sql);
            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $branch_id = $row['id'];
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $branch_name = $row['branch_name'];
                    $branch_image = $row['branch_image'];
                    $branch_phone = $row['branch_phone'];
                    $branch_email = $row['branch_email'];
                    $about_branch = $row['about_branch'];
                    $branch_business_category = $row['branch_business_category'];
                    $branch_address = $row['branch_address'];
                    $pincode = $row['pincode'];
                    $state = $row['state'];
                    $city = $row['city'];
                    $listing_id = $row['user_id'];
                    $listing_type = '6';
    
                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
                    }
    
    
                    $distances = str_replace(',', '.', GetDrivingDistance($lat, $mlat, $lng, $mlng));
                    $enquiry_number = "9619146163";
                    $resultpost1[] = array(
                       
                        'id' => $branch_id,
                        'name' => $branch_name,
                        'listing_id' => $listing_id,
                        'listing_type' => $listing_type,
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $branch_address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $branch_phone,
                        'email' => $branch_email,
                        'distance' => $distances,
                        'profile_pic' => $branch_image
                   
                    );
                }
                  function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
                $sort_col = array();
                foreach ($arr as $key => $row) {
                    $sort_col[$key] = $row[$col];
                }
                array_multisort($sort_col, $dir, $arr);
            }
    
            array_sort_by_column($resultpost1, 'distance');
            return $resultpost1;
            
            } else {
               return $resultpost1 = array();
            }
          
          
        }
      
     
      
      else if($vendor_id=='5')
      {
           if($keyword!="")
           {
               $sql   = sprintf("SELECT doctor_list.mba,doctor_list.recommended,doctor_list.certified,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.doctor_name LIKE '%%$keyword%%'  and  doctor_list.is_active = 1  HAVING distance < '%s' ORDER BY distance  LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
           }
          else
          {
           $sql   = sprintf("SELECT doctor_list.mba,doctor_list.recommended,doctor_list.certified,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.is_active = 1  HAVING distance < '%s' ORDER BY distance  LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
          }
            $query = $this->db->query($sql);
            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $lat                 = $row['lat'];
                    $lng                 = $row['lng'];
                    $doctor_name         = $row['doctor_name'];
                    $email               = $row['email'];
                    $gender              = $row['gender'];
                    $doctor_phone        = $row['telephone'];
                    $dob                 = $row['dob'];
                    $category            = $row['category'];
                    $speciality          = $row['speciality'];
                    $service             = $row['service'];
                    $degree              = $row['qualification'];
                    $experience          = $row['experience'];
                    $reg_council         = $row['reg_council'];
                    $reg_number          = $row['reg_number'];
                    $doctor_user_id      = $row['user_id'];
                    $clinic_name         = $row['clinic_name'];
                    $address             = $row['address'];
                    $city                = $row['city'];
                    $state               = $row['state'];
                    $pincode             = $row['pincode'];
             
                    if ($row['image'] != '') {
                        $profile_pic = $row['image'];
                        $profile_pic = str_replace(' ', '%20', $profile_pic);
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                    } else {
                        $profile_pic = 'https://medicalwale.com/img/doctor_default.png';
                    }
                 
                    
                    $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                    $resultpost2[] = array(
                    
                        'id' => $doctor_user_id,
                        'name' => $doctor_name,
                        'listing_id' => $doctor_user_id,
                        'listing_type' => "5",
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $doctor_phone,
                        'email' => $email,
                        'distance' => $distances,
                        'profile_pic' => $profile_pic
                       
                    );
                }
                function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
                $sort_col = array();
                foreach ($arr as $key => $row) {
                    $sort_col[$key] = $row[$col];
                }
                array_multisort($sort_col, $dir, $arr);
            }
            array_sort_by_column($resultpost2, 'distance');
            return $resultpost2 ;
            
            } else {
               return $resultpost2 = array();
            }
        
      }
       
     
       
      else if($vendor_id == '10')
       {
            $resultpost_branch=array();
            if($keyword!="")
              {
             
               $sql    = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_name`,`address1`,`address2`, `pincode`, `city`, `state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`,`user_discount`, IFNULL(delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE `is_active` = 1 and lab_name LIKE '%%$keyword%%'   HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
               
           }
            else
              {
                $sql    = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_name`,`address1`,`address2`, `pincode`, `city`, `state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`,`user_discount`, IFNULL(delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE `is_active` = 1  HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
               }
                $query  = $this->db->query($sql);
                $count  = $query->num_rows();
            
              if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $id                = $row['id'];
                    $labcenter_user_id = $row['user_id'];
                    $lab_name          = $row['lab_name'];
                    $features          = $row['features'];
                    $home_delivery     = $row['home_delivery'];
                    $delivery_charges  = $row['delivery_charges'];
                    $address1          = $row['address1'];
                    $address2          = $row['address2'];
                    $pincode           = $row['pincode'];
                    $city              = $row['city'];
                    $state             = $row['state'];
                    $contact_no        = $row['contact_no'];
                    $whatsapp_no       = $row['whatsapp_no'];
                    $email             = $row['email'];
                    $lat               = $row['latitude'];
                    $lng               = $row['longitude'];
                    $listing_type      = '10';
                    //$rating            = '4.0';
                  
                    $image             = $row['profile_pic'];
                   // $image             = 'https://d2c8oti4is0ms3.cloudfront.net/images/labcenter_images/' . $image;
                   $image1             = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                  
                    $resultpost4[] = array(
                     
                             'id' => $id,
                        'name' => $lab_name,
                        'listing_id' => $labcenter_user_id,
                        'listing_type' => '10',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $address1,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $contact_no,
                        'email' => $email,
                        'distance' => '0',
                        'profile_pic' => $image1
                    
                    );
                }
                return $resultpost4;
            } else {
                return $resultpost4 = array();
            }
       }
      
        
      else if($vendor_id == '8')
       {
            
         if($keyword!="")
              {
                $sql = sprintf("SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat) ) * cos( radians( lng) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat) ) ) ) AS distance FROM hospitals WHERE is_approval='1' AND is_active='1' and name_of_hospital LIKE '%%$keyword%%' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
              }
            else
              {  
                 $sql = sprintf("SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat) ) * cos( radians( lng) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat) ) ) ) AS distance FROM hospitals WHERE is_approval='1' AND is_active='1' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
              }
        $query = $this->db->query($sql);
        $count = $query->num_rows();
         if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $id = $row['id'];
                    $name_of_hospital = $row['name_of_hospital'];
                    $mobile = $row['phone'];
                    $about_us = $row['about_us'];
                    $establishment_year = $row['establishment_year'];
                    $category = $row['category'];
                    $address = $row['address'];
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $pincode = $row['pincode'];
                    $city = $row['city'];
                    $state = $row['state'];
                    $email = $row['email'];
                    $image = $row['image'];
                    $rating = $row['rating'];
                    $reviews = $row['review'];
                    $user_discount = $row['user_discount'];
                    $hospital_user_id = $row['user_id'];
                    $profile_views = '0';
    
                    if ($image != '') {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                    } else {
                        $image = '';
                    }
    
    $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
     $distance_root =  round(($distances/1000),1);
                    //end 
                    $resultpost5[] = array(
                       
                        'id' => $id,
                        'name' => $name_of_hospital,
                        'listing_id' => $hospital_user_id,
                        'listing_type' => "8",
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $mobile,
                        'email' => $email,
                        'distance' => $distances,
                        'profile_pic' => $image
                        
                    );
             }
             return $resultpost5;
            } else {
               return $resultpost5 = array();
            }
       }
      
      else   if($vendor_id == '12')
           {
                if($keyword!="")
              {
                
                $sql = sprintf("SELECT nursing_attendant.*,IFNULL(rating,'') AS rating, IFNULL(review,'') AS review, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM nursing_attendant  where is_active = '1' and nursing_attendant.name LIKE '%%$keyword%%' HAVING distance < '%s' or all_india='1' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
              }
            else
              {  
                 $sql = sprintf("SELECT nursing_attendant.*,IFNULL(rating,'') AS rating, IFNULL(review,'') AS review, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM nursing_attendant  where is_active = '1' HAVING distance < '%s' or all_india='1' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
              }
                   $query = $this->db->query($sql);
                $count = $query->num_rows();
            
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                         $id = $row['id'];
                        $name = $row['name'];
                        
                        $about_us = $row['about_us'];
                        $establishment_year = $row['establishment_year'];
                        $certificates = $row['certificates'];
                        $address = $row['address'];
                        $lat = $row['lat'];
                        $lng = $row['lng'];
                        $pincode = $row['pincode'];
                        $mobile = $row['contact'];
                        $city = $row['city'];
                        $state = $row['state'];
                        $email = $row['email'];
                        $image = $row['image'];
                        $rating = $row['rating'];
                        //$reviews                = $row['review'];        
                         $nursingattendant_user_id = $row['user_id'];
                        //$profile_views          = '1558';
                    
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $image = '';
                        }
        
                        $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $nursingattendant_user_id)->get()->num_rows();
        
                        if ($img_count > 0) {
                            $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $nursingattendant_user_id)->get()->row();
                            $img_file = $profile_query->source;
                            $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                        } else {
                            $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
        
        
                        $resultpost3[] = array(
                      
                             'id' => $id,
                        'name' => $name,
                        'listing_id' => $nursingattendant_user_id,
                        'listing_type' => "12",
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $mobile,
                        'email' => $email,
                        'distance' => 0,
                        'profile_pic' => $userimage
                        );
                    }
                 return $resultpost3;   
                } else {
                    return $resultpost3 = array();
                }
           }
      
       else  if($vendor_id == '36')    
         {
                if($keyword!="")
              {
                
                
                 $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'  and fcb.branch_name LIKE '%%$keyword%%'  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
              }
            else
              {  
            $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'   HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
              }
            $query = $this->db->query($sql);
            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $branch_id = $row['id'];
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $branch_name = $row['branch_name'];
                    $branch_image = $row['branch_image'];
                    $branch_phone = $row['branch_phone'];
                    $branch_email = $row['branch_email'];
                    $about_branch = $row['about_branch'];
                    $branch_address = $row['branch_address'];
                    $pincode = $row['pincode'];
                    $state = $row['state'];
                    $city = $row['city'];
                    $listing_id = $row['user_id'];
                    $listing_type = '36';
                    $rating = '4.5';
                    $reviews = '0';
                    $profile_views =  '0' ;
                    $user_discount = $row['user_discount'];
    
                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image =  'https://medicalwale.com/img/doctor_default.png';
                    }
    
                    $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                    $enquiry_number = "9619146163";
                    $resultpost7[] = array(
                   
                        'id' => $branch_id,
                        'name' => $branch_name,
                        'listing_id' => $listing_id,
                        'listing_type' => $listing_type,
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $branch_address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $branch_phone,
                        'email' => $branch_email,
                        'distance' => $distances,
                        'profile_pic' => $branch_image
                    );
                }
                
                  return $resultpost7 ;
            } else {
               return $resultpost7 = array();
            }
    
           
       }
       
      else if($vendor_id == '39')    
         {
                if($keyword!="")
              {
               $sql = sprintf("SELECT id,hub_user_id,dentists_branch_user_id, name_of_hospital,about_us, establishment_year, speciality, address, address_2, pincode, phone, city, state,   email,  lat, lng, image, rating, review, medicalwale_discount,opening_hours, description,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_branch  WHERE is_active='1' AND hub_user_id='33586' AND name_of_hospital LIKE '%%$keyword%%'  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
              }
            else
              {  
        $sql = sprintf("SELECT id,hub_user_id,dentists_branch_user_id, name_of_hospital,about_us, establishment_year, speciality, address, address_2, pincode, phone, city, state,  email,  lat, lng, image, rating, review, medicalwale_discount,opening_hours, description,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_branch  WHERE is_active='1' AND hub_user_id='33586'  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
              }
           $query = $this->db->query($sql);
           $count = $query->num_rows();
           
           if($count>0)
           {
               foreach($query->result_array() as $row)
               {
                   $branch_id               = $row['id'];
                   $hub_user_id             = $row['hub_user_id'];
                   $dentists_branch_user_id = $row['dentists_branch_user_id'];
                   $name_of_hospital        = $row['name_of_hospital'];
                   $about_us                = $row['about_us'];
                   $establishment_year      = $row['establishment_year'];
                   $speciality              = $row['speciality'];
                   $address                 = $row['address'];
                   $address_2               = $row['address_2'];
                   $pincode                 = $row['pincode'];
                   $phone                   = $row['phone'];
                   $city                    = $row['city'];
                   $state                   = $row['state'];
                   $email                   = $row['email'];
                   $lat                     = $row['lat'];
                   $lng                     =  $row['lng'];
                   $image                   =  $row['image'];
                   
              
                 if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $image = '';
                }
             
                $resultpost6[] = array(
                     
                      
                        'id' => $branch_id,
                        'name' => $name_of_hospital,
                        'listing_id' => $dentists_branch_user_id,
                        'listing_type' => "39",
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $phone,
                        'email' => $email,
                        'distance' => 0,
                        'profile_pic' => $image
                       );
               }
               return  $resultpost6 ;
           }
           else
           {
              return  $resultpost6 = array();
           }
         }
         
       else  if($vendor_id == '17')   
       {
               if($keyword!="")
              {
             
               
               $sql = sprintf("SELECT `id`, `email`, `dentists_branch_user_id`, `name_of_hospital`, `establishment_year`, `phone`,   `user_discount`, `about_us`, `opening_hours`, `address`, `image`, `lat`, `lng`, `review`, `rating`, `state`, `city`, `pincode`, `is_active` ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_branch  WHERE is_active='1' AND hub_user_id='33586' AND name LIKE '%%$keyword%%'  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
               
               
              }
            else
              {  
             $sql = sprintf("SELECT `id`, `email`, `dentists_branch_user_id`, `name_of_hospital`, `establishment_year`, `phone`,   `user_discount`, `about_us`, `opening_hours`, `address`, `image`, `lat`, `lng`, `review`, `rating`, `state`, `city`, `pincode`, `is_active`,  ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_branch  WHERE is_active='1' AND hub_user_id='33586'   HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
              }
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if($count>0){
            foreach ($query->result_array() as $row) {
                $lat = $row['lat'];
                $lng = $row['lng'];
                $optic_id = $row['dentists_branch_user_id'];
                
                $optic_name = $row['name_of_hospital'];
                $address = $row['address'];
                $pincode = $row['pincode'];
                $city = $row['city'];
                $state = $row['state'];
                $phone = $row['phone'];
                $telephone = $row['phone'];
                $store_since = $row['establishment_year'];
                $discount = $row['user_discount'];
                $email = $row['email'];
                $profile_pic = $row['image'];
               
                
                $resultpost8[] = array(
                     'id' => $optic_id,
                        'name' => $optic_name,
                        'listing_id' => $optic_id,
                        'listing_type' => "17",
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $phone,
                        'email' => $email,
                        'distance' => 0,
                        'profile_pic' => $profile_pic
                    
                );
            }
          return   $resultpost8;
        }
        else{
         return   $resultpost8= array();
        }
         }
          
       else
       return $resultpost0 = array();
         /*$all_booking[] = array(
                'Pharmacy' => $resultpost0, 
                'Labs'=>$resultpost4,
                'Doctors'=>$resultpost2,
                'Spa'=>$resultpost7,
                'Hospital'=>$resultpost5,
                'Fitness Centres' => $resultpost1,
                'Nursing Attendant'=>$resultpost3,
                'Dentist' => $resultpost6,
                'Optics' => array()
                
            );
                
             return $all_booking;*/
    }
    
    
    public function pharmacy_tracker($user_id, $invoice_no){
        $data = array();
        $getStatuses = $this->db->query("SELECT * FROM `user_order_tracking` WHERE `invoice_no` LIKE '$invoice_no' ORDER BY `created_at` ASC")->result_array();
        
        foreach($getStatuses as $statuses){
            $created_at = date_create($statuses['created_at']);
            $d = date_format($created_at, 'D jS F Y, g:ia');
            // echo $d ; die();
            $action_by = strtolower($statuses['action_by']);
            $t['timestamp'] = $d;
            $t['status'] = $statuses['status']  ; 
            $data[] = $t;
        }
        
       
        return $data;
        
        
    }
}
?>

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }

//     public function add_doctor_form() {
//         $this->load->model('SalesModel');
//         $method = $_SERVER['REQUEST_METHOD'];
//         if ($method != 'POST') {
//             json_output(400, array(
//                 'status' => 400,
//                 'message' => 'Bad request.'
//             ));
//         } else {
//             $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
//             include('s3_config.php');
//             $data['image'] = '';
//             if(!empty($_FILES['profile_image']['name'])){
//                 $img_name = $_FILES['profile_image']['name'];
//                 $img_size = $_FILES['profile_image']['size'];
//                 $img_tmp  = $_FILES['profile_image']['tmp_name'];
//                 $ext = getExtension($img_name);
//                 if (strlen($img_name) > 0) {
//                     if (in_array($ext, $img_format)) {
//                         $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
//                         $actual_image_path = 'images/healthwall_avatar/' . $actual_image_name;
//                         if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
//                             $data['image'] = $actual_image_name;
//                         }
//                     }
//                 }
//             }
//             $clinic['image'] = '';
//             if (!empty($_FILES['clinic_image']['name'])) {
//                 $img_name = $_FILES['clinic_image']['name'];
//                 $img_size = $_FILES['clinic_image']['size'];
//                 $img_tmp = $_FILES['clinic_image']['tmp_name'];
//                 $ext = getExtension($img_name);
//                 if (strlen($img_name) > 0) {
//                     if (in_array($ext, $img_format)) {
//                         $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
//                         $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
//                         if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
//                             $clinic['image'] = $actual_image_name;
//                         }
//                     }
//                 }
//             }
//             $data['vendor_sign'] = '';
//             if (!empty($_FILES['vendor_sign']['name'])) {
//                 $img_name = $_FILES['vendor_sign']['name'];
//                 $img_size = $_FILES['vendor_sign']['size'];
//                 $img_tmp = $_FILES['vendor_sign']['tmp_name'];
//                 $ext = getExtension($img_name);
//                 if (strlen($img_name) > 0) {
//                     if (in_array($ext, $img_format)) {
//                         $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
//                         $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
//                         if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
//                             $data['vendor_sign'] = $actual_image_name;
//                         }
//                     }
//                 }
//             }
//             $data['sign'] = '';
//             if (!empty($_FILES['sign']['name'])) {
//                 $img_name = $_FILES['sign']['name'];
//                 $img_size = $_FILES['sign']['size'];
//                 $img_tmp = $_FILES['sign']['tmp_name'];
//                 $ext = getExtension($img_name);
//                 if (strlen($img_name) > 0) {
//                     if (in_array($ext, $img_format)) {
//                         $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
//                         $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
//                         if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
//                             $data['sign'] = $actual_image_name;
//                         }
//                     }
//                 }
//             }
//             $data['executive_id'] = $this->input->post('user_id');	
//             $data['executive_name'] = $this->input->post('executive_name');		
//             $data['business_nature'] = $this->input->post('business_nature');
//             $data['doctor_name'] = $this->input->post('name');
//             $data['address'] = $this->input->post('address');
//             $data['email'] = $this->input->post('email');
//             $data['telephone'] = $this->input->post('phone');
//             $data['since'] = $this->input->post('since');
//             $data['area_expertise'] = $this->input->post('area_expertise'); 
//             $data['qualification'] = $this->input->post('degree');
//             $data['dob'] = $this->input->post('dob');
//             $data['gender'] = $this->input->post('gender');			
//             $data['consultaion_chat'] = $this->input->post('chat_fees');
//             $data['consultation_fee'] = $this->input->post('visit_fees');
//             $data['consultaion_video'] = $this->input->post('video_fees');
//             $data['consultation_voice_call'] = $this->input->post('audio_fees');
//             $data['medicale_discount'] = $this->input->post('medicalwale_discount');
//             $data['online_offline'] = $this->input->post('online_offline');
//             $data['online_time'] = $this->input->post('online_time');	
//             $data['person_name'] = $this->input->post('person_name');
//             $data['person_address'] = $this->input->post('person_address');
//             $data['person_city'] = $this->input->post('person_city');
//             $data['person_pincode'] = $this->input->post('person_pincode');
//             $data['person_state'] = $this->input->post('person_state');
//             $data['person_country'] = $this->input->post('person_country');
//             $data['person_telephone'] = $this->input->post('person_telephone');
//             $data['person_email'] = $this->input->post('person_email');
//             $data['person_phone'] = $this->input->post('person_phone');
//             $data['account_no'] = $this->input->post('account_no');
//             $data['account_type'] = $this->input->post('account_type');
//             $data['bank_name'] = $this->input->post('bank_name');
//             $data['ifsc_code'] = $this->input->post('ifsc_code');
//             $data['feedback'] = $this->input->post('feedback');
//             $data['is_approval'] = '1';
//             $data['is_active'] = '1';	

//             $clinic['clinic_name'] = $this->input->post('clinic_name');
// 			$clinic['contact_no'] = $this->input->post('clinic_contact');
//             $clinic['address'] = $this->input->post('clinic_address');
//             $clinic['city'] = $this->input->post('clinic_city');
//             $clinic['state'] = $this->input->post('clinic_state');
//             $clinic['pincode'] = $this->input->post('clinic_pincode');
// 			$clinic['consultation_charges'] = $this->input->post('consultation_fees');		
//             $clinic['open_hours'] = $this->input->post('working_hours');
//             $clinic['map_location'] = $this->input->post('locator');
//             $clinic['lat'] = $this->input->post('lat');
//             $clinic['lng'] = $this->input->post('lng');

//             $user['name'] = $this->input->post('name');
//             $user['email'] = $this->input->post('email');
//             $user['phone'] = $this->input->post('phone');
//             $user['dob'] = $this->input->post('dob');
//             $user['gender'] = $this->input->post('gender');	
//             $user['vendor_id'] = '5';
//             $user['lat'] = $this->input->post('lat');
//             $user['lng'] = $this->input->post('lng');
//             $user['password'] = md5('12345');

//             $package =$this->input->post('package');
//             $offer =$this->input->post('offer');
//             $awards =$this->input->post('awards');	

//             $resp = $this->SalesModel->add_doctor_form($data,$clinic,$user,$package,$offer,$awards);
//             simple_json_output($resp);
//         }
//     }

  public function add_doctor_form() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
            include('s3_config.php');
            $data['image'] = '';
            if(!empty($_FILES['profile_image']['name'])){
                $img_name = $_FILES['profile_image']['name'];
                $img_size = $_FILES['profile_image']['size'];
                $img_tmp  = $_FILES['profile_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/healthwall_avatar/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['image'] = $actual_image_name;
                        }
                    }
                }
            }
            $clinic['image'] = '';
            if (!empty($_FILES['clinic_image']['name'])) {
                $img_name = $_FILES['clinic_image']['name'];
                $img_size = $_FILES['clinic_image']['size'];
                $img_tmp = $_FILES['clinic_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $clinic['image'] = $actual_image_name;
                        }
                    }
                }
            }
            $data['vendor_sign'] = '';
            if (!empty($_FILES['vendor_sign']['name'])) {
                $img_name = $_FILES['vendor_sign']['name'];
                $img_size = $_FILES['vendor_sign']['size'];
                $img_tmp = $_FILES['vendor_sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['vendor_sign'] = $actual_image_name;
                        }
                    }
                }
            }
            $data['sign'] = '';
            if (!empty($_FILES['sign']['name'])) {
                $img_name = $_FILES['sign']['name'];
                $img_size = $_FILES['sign']['size'];
                $img_tmp = $_FILES['sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['sign'] = $actual_image_name;
                        }
                    }
                }
            }
            $data['executive_id'] = $this->input->post('user_id');	
            $data['executive_name'] = $this->input->post('executive_name');		
            $data['business_nature'] = $this->input->post('business_nature');
            $data['doctor_name'] = $this->input->post('name');
            $data['address'] = $this->input->post('address');
            $data['email'] = $this->input->post('email');
            $data['telephone'] = $this->input->post('phone');
            $data['since'] = $this->input->post('since');
            $data['area_expertise'] = $this->input->post('area_expertise'); 
            $data['qualification'] = $this->input->post('degree');
            $data['dob'] = $this->input->post('dob');
            $data['gender'] = $this->input->post('gender');			
            $data['consultaion_chat'] = $this->input->post('chat_fees');
            $data['consultation_fee'] = $this->input->post('visit_fees');
            $data['consultaion_video'] = $this->input->post('video_fees');
            $data['consultation_voice_call'] = $this->input->post('audio_fees');
            $data['medicale_discount'] = $this->input->post('medicalwale_discount');
            $data['online_offline'] = $this->input->post('online_offline');
            $data['online_time'] = $this->input->post('online_time');	
            $data['person_name'] = $this->input->post('person_name');
            $data['person_address'] = $this->input->post('person_address');
            $data['person_city'] = $this->input->post('person_city');
            $data['person_pincode'] = $this->input->post('person_pincode');
            $data['person_state'] = $this->input->post('person_state');
            $data['person_country'] = $this->input->post('person_country');
            $data['person_telephone'] = $this->input->post('person_telephone');
            $data['person_email'] = $this->input->post('person_email');
            $data['person_phone'] = $this->input->post('person_phone');
            $data['account_no'] = $this->input->post('account_no');
            $data['account_type'] = $this->input->post('account_type');
            $data['bank_name'] = $this->input->post('bank_name');
            $data['ifsc_code'] = $this->input->post('ifsc_code');
            $data['feedback'] = $this->input->post('feedback');
            $data['is_approval'] = '1';
            $data['is_active'] = '1';	

            $clinic['clinic_name'] = $this->input->post('clinic_name');
			$clinic['contact_no'] = $this->input->post('clinic_contact');
            $clinic['address'] = $this->input->post('clinic_address');
            $clinic['city'] = $this->input->post('clinic_city');
            $clinic['state'] = $this->input->post('clinic_state');
            $clinic['pincode'] = $this->input->post('clinic_pincode');
			$clinic['consultation_charges'] = $this->input->post('consultation_fees');		
            $clinic['open_hours'] = $this->input->post('working_hours');
            $clinic['map_location'] = $this->input->post('locator');
            $clinic['lat'] = $this->input->post('lat');
            $clinic['lng'] = $this->input->post('lng');

            $user['name'] = $this->input->post('name');
            $user['email'] = $this->input->post('email');
            $user['phone'] = $this->input->post('phone');
            $user['dob'] = $this->input->post('dob');
            $user['gender'] = $this->input->post('gender');	
            $user['vendor_id'] = '5';
            $user['lat'] = $this->input->post('lat');
            $user['lng'] = $this->input->post('lng');
            $user['password'] = md5('12345');

            $package =$this->input->post('package');
            $offer =$this->input->post('offer');
            $awards =$this->input->post('awards');	
            //added for timing slots 
            //start
            $appointment_time = $this->input->post('appointment_time'); if($appointment_time == ""){$appointment_time = "null";};
             $time =  $this->input->post('timings');
              $timings = json_decode($time);
              
              
              $time = array();    
                    $slots = array();
                    $timingsMain = array();
                    //  $timeConsult = array();
                    
                    // $timeCall = $appointment_time_call;
                    // $timeVideo = $appointment_time_video;
                    // $timeText = $appointment_time_text;
                    $timeConsult = $appointment_time;
                    
                    for($i=0;$i<sizeof($timings->timings);$i++){
                        // day monday
                           //  slots 
                             //   time
                              // FromTime -> from to
                            $day = $timings->timings[$i]->day;
                             
                            for($j=0;$j<sizeof($timings->timings[$i]->slots);$j++){
                                // print_r($timings->timings[$i]->slots[$j]);
                                
                                $timeSlot = $timings->timings[$i]->slots[$j]->timeSlot;
                                for($k=0;$k<sizeof($timings->timings[$i]->slots[$j]->time);$k++){
                                            // time
                                            
                                            $fromDate =  $timings->timings[$i]->slots[$j]->time[$k]->FromTime;
                                            $toDate =  $timings->timings[$i]->slots[$j]->time[$k]->ToTime;
                                            
                                           
                                            
                                         
                                            $timestampFrom = date('Y-m-d H:i:s', strtotime($fromDate));
                                            $timestampTo = date('Y-m-d H:i:s', strtotime($toDate));
                                            
                                            $epoToDateFrom =  date("Y-m-d H:i:s", substr($fromDate, 0, 10));
                                            $epoToDateTo =  date("Y-m-d H:i:s", substr($toDate, 0, 10));
                                            
                                            
                                            
                                            
                                              
                                            $timeSlotFrom = date('H:i:s', strtotime($fromDate));
                                            $timeSlotTo = date('H:i:s', strtotime($toDate));
                                                                                    
                                            

                                            $data_time_slots = array(
                                                    'doctor_id' => $doctor_id,
                                                    'clinic_id' => $clinic_id,
                                                    'from_time' => $epoToDateFrom,
                                                    'to_time' => $epoToDateTo,
                                                    'day' => $day,
                                                    'time_slot' => $timeSlot,
                                                    'consultation_type' => $consultation_type,
                                                    'open_hours' => $appointment_time
                                                );
                                                
                                               
                                          
                                        $this->SalesModel->doctor_slot_details($data_time_slots);
                                      
                                            $timestampFromDate = new DateTime(date('Y-m-d H:i:s', strtotime($epoToDateFrom)));
                                            $timestampToDate = new DateTime(date('Y-m-d H:i:s', strtotime($epoToDateTo)));
                                            
                                            $totalHts = $toDate - $fromDate;
                                           
                                            $interval = $timestampFromDate->diff($timestampToDate);
                                            
                                            $remHr = $interval->format('%h');
                                            $remMin = $interval->format('%i');
                                            
                                            $totalRemMin = $remHr * 60 + $remMin;
                                            
                                            
                                           
                                             $timeForConsult = $totalRemMin / $timeConsult;
                                            
                                           
                                          
                                            
                                            
                                             $timeForConsultFloor = floor($timeForConsult);
                                            $decimalForConsult = $timeForConsult - $timeForConsultFloor;
                                            if($decimalForConsult > 0.5){ $finalSlotConsult = ceil($timeForConsult); } else { $finalSlotConsult = floor($timeForConsult); };
                                            
                                            
                                            
                                         
                                           
                                        
                                            
                                            $newCallTime = $epoToDateFrom;
                                             $newVideoTime = $epoToDateFrom;
                                            $newTextTime = $epoToDateFrom;
                                            
                                             $epoToDateFrom =  date("H:i:s", substr($fromDate, 0, 10));
                                            
                                            $newConsultTime = $epoToDateFrom;
                                            
                                            
                                          
                                        
                                      
                                        
                                        for($consultSlot=0;$consultSlot<$finalSlotConsult;$consultSlot++){
                                                
                                          
                                               
                                                $ConsultTimeAdded = strtotime("+".$timeConsult." minutes", strtotime($newConsultTime));
                                                $ConsultTime = date('H:i:s', $ConsultTimeAdded);
                                                
                                          
                                                $data_time = array(
                                                    'doctor_id' => $doctor_id,
                                                    'clinic_id' => $clinic_id,
                                                    'from_time' => $newConsultTime,
                                                    'to_time' => $ConsultTime,
                                                    'day' => $day,
                                                    'timeSlot' => $timeSlot,
                                                    'status' => "0",
                                                    'consultation_type' => $consultation_type
                                                );
                                                $timing_id = $this->SalesModel->doctor_add_clinic_timing($data_time);
                                          
                                          
                                            
                                            // array_push
                                           $time['FromTime']=$newConsultTime;
                                            $time['to_time']=$ConsultTime;
                                            $timeAll[] = $time;
                                             $newConsultTime = $ConsultTime;
                                        
                                            }
                                      
                                    
                                }
                               $slots['time'] = $timeAll;
                                $slots['timeSlot'] = $timeSlot;
                                
                                $slotsAll[]=$slots;
                                // print_r($slots);die();
                            }
                            $timingsNew['day'] = $day;
                            $timingsNew['slots'] = $slotsAll;
                            
                            $timing[] = $timingsNew;
                            
                            // print_r($timingsMain);die();
                    }
            //end
            $resp = $this->SalesModel->add_doctor_form($data,$clinic,$user,$package,$offer,$awards);
            simple_json_output($resp);
        }
    }

   


    public function update_doctor_form() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
            include('s3_config.php');
            $id = $this->input->post('listing_id');

            $file_query = $this->db->query("SELECT image as profile_image,vendor_sign,sign FROM `doctor_list` WHERE user_id='$id'");
            $get_file = $file_query->row();
            $data['image'] = $get_file->profile_image;
            $data['vendor_sign'] = $get_file->vendor_sign;
            $data['sign'] = $get_file->sign;
            
            $file_query2 = $this->db->query("SELECT image as clinic_image FROM `doctor_clinic` WHERE doctor_id='$id'");
            $get_file2 = $file_query2->row();
            $clinic['image'] = $get_file2->clinic_image;

            if (!empty($_FILES['profile_image']['name'])) {
                $img_name = $_FILES['profile_image']['name'];
                $img_size = $_FILES['profile_image']['size'];
                $img_tmp = $_FILES['profile_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file->profile_image != '') {
                            $file = "images/healthwall_avatar/" . $get_file->profile_image;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/healthwall_avatar/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['image'] = $actual_image_name;
                        }
                    }
                }
            }

            if (!empty($_FILES['clinic_image']['name'])) {
                $img_name = $_FILES['clinic_image']['name'];
                $img_size = $_FILES['clinic_image']['size'];
                $img_tmp = $_FILES['clinic_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file2->clinic_image != '') {
                            $file = "images/sales_form_files/" . $get_file2->clinic_image;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $clinic['image'] = $actual_image_name;
                        }
                    }
                }
            }

            if (!empty($_FILES['vendor_sign']['name'])) {
                $img_name = $_FILES['vendor_sign']['name'];
                $img_size = $_FILES['vendor_sign']['size'];
                $img_tmp = $_FILES['vendor_sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file->vendor_sign != '') {
                            $file = "images/sales_form_files/" . $get_file->vendor_sign;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['vendor_sign'] = $actual_image_name;
                        }
                    }
                }
            }

            if (!empty($_FILES['sign']['name'])) {
                $img_name = $_FILES['sign']['name'];
                $img_size = $_FILES['sign']['size'];
                $img_tmp = $_FILES['sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file->sign != '') {
                            $file = "images/sales_form_files/" . $get_file->sign;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['sign'] = $actual_image_name;
                        }
                    }
                }
            }
	
            $data['executive_name'] = $this->input->post('executive_name');		
            $data['business_nature'] = $this->input->post('business_nature');
            $data['doctor_name'] = $this->input->post('name');
            $data['address'] = $this->input->post('address');
            $data['email'] = $this->input->post('email');
            $data['telephone'] = $this->input->post('phone');
            $data['since'] = $this->input->post('since');
            $data['area_expertise'] = $this->input->post('area_expertise'); 
            $data['qualification'] = $this->input->post('degree');
            $data['dob'] = $this->input->post('dob');
            $data['gender'] = $this->input->post('gender');			
            $data['consultaion_chat'] = $this->input->post('chat_fees');
            $data['consultation_fee'] = $this->input->post('visit_fees');
            $data['consultaion_video'] = $this->input->post('video_fees');
            $data['consultation_voice_call'] = $this->input->post('audio_fees');
            $data['medicale_discount'] = $this->input->post('medicalwale_discount');
            $data['online_offline'] = $this->input->post('online_offline');
            $data['online_time'] = $this->input->post('online_time');	
            $data['person_name'] = $this->input->post('person_name');
            $data['person_address'] = $this->input->post('person_address');
            $data['person_city'] = $this->input->post('person_city');
            $data['person_pincode'] = $this->input->post('person_pincode');
            $data['person_state'] = $this->input->post('person_state');
            $data['person_country'] = $this->input->post('person_country');
            $data['person_telephone'] = $this->input->post('person_telephone');
            $data['person_email'] = $this->input->post('person_email');
            $data['person_phone'] = $this->input->post('person_phone');
            $data['account_no'] = $this->input->post('account_no');
            $data['account_type'] = $this->input->post('account_type');
            $data['bank_name'] = $this->input->post('bank_name');
            $data['ifsc_code'] = $this->input->post('ifsc_code');
            $data['feedback'] = $this->input->post('feedback');	
            $data['is_approval'] = '1';
            $data['is_active'] = '1';
            $clinic['clinic_name'] = $this->input->post('clinic_name');
			$clinic['contact_no'] = $this->input->post('clinic_contact');
            $clinic['address'] = $this->input->post('clinic_address');
            $clinic['city'] = $this->input->post('clinic_city');
            $clinic['state'] = $this->input->post('clinic_state');
            $clinic['pincode'] = $this->input->post('clinic_pincode');
			$clinic['consultation_charges'] = $this->input->post('consultation_fees');		
            $clinic['open_hours'] = $this->input->post('working_hours');
            $clinic['map_location'] = $this->input->post('locator');
            $clinic['lat'] = $this->input->post('lat');
            $clinic['lng'] = $this->input->post('lng');
            $user['name'] = $this->input->post('name');
            $user['email'] = $this->input->post('email');
            $user['phone'] = $this->input->post('phone');
            $user['dob'] = $this->input->post('dob');
            $user['gender'] = $this->input->post('gender');	
            $user['vendor_id'] = '5';
            $user['lat'] = $this->input->post('lat');
            $user['lng'] = $this->input->post('lng');
            $package =$this->input->post('package');
            $offer =$this->input->post('offer');
            $awards =$this->input->post('awards');		
            $resp = $this->SalesModel->update_doctor_form($data,$clinic,$user,$id,$package,$offer,$awards);
            simple_json_output($resp);
        }
    }
	
	public function add_pharmacy_form() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
            include('s3_config.php');
            $data['profile_pic'] = '';
            if (!empty($_FILES['profile_image']['name'])) {
                $img_name = $_FILES['profile_image']['name'];
                $img_size = $_FILES['profile_image']['size'];
                $img_tmp = $_FILES['profile_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/healthwall_avatar/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['profile_pic'] = $actual_image_name;
                        }
                    }
                }
            }         
            $data['vendor_sign'] = '';
            if (!empty($_FILES['vendor_sign']['name'])) {
                $img_name = $_FILES['vendor_sign']['name'];
                $img_size = $_FILES['vendor_sign']['size'];
                $img_tmp = $_FILES['vendor_sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['vendor_sign'] = $actual_image_name;
                        }
                    }
                }
            }
            $data['sign'] = '';
            if (!empty($_FILES['sign']['name'])) {
                $img_name = $_FILES['sign']['name'];
                $img_size = $_FILES['sign']['size'];
                $img_tmp = $_FILES['sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['sign'] = $actual_image_name;
                        }
                    }
                }
            }
            $data['executive_id'] = $this->input->post('user_id');	
            $data['executive_name'] = $this->input->post('executive_name');		
            $data['business_nature'] = $this->input->post('business_nature');
            $data['store_manager'] = $this->input->post('manager_name');
            $data['medical_name'] = $this->input->post('name');
            $data['address1'] = $this->input->post('address');
            $data['email'] = $this->input->post('email');
            $data['contact_no'] = $this->input->post('phone');
            $data['store_since'] = $this->input->post('since');
            $data['medicalwale_discount'] = $this->input->post('medicalwale_discount');	
            $data['reach_area'] = $this->input->post('radius_area');
            $data['store_time'] = $this->input->post('store_time');
            $data['is_24hrs_available'] = $this->input->post('is_24_hours');			
            $data['days_closed'] = $this->input->post('non_working_day');
            $data['delivery_time'] = $this->input->post('delivery_time');
            $data['free_home_delivery'] = $this->input->post('free_home_delivery');
            $data['min_order'] = $this->input->post('minimum_order_amount');
            $data['min_order_delivery_charge'] = $this->input->post('minimum_order_delivery');
			$data['night_delivery_charge'] = $this->input->post('night_delivery_amount');
            $data['online_offline'] = $this->input->post('online_offline');
            $data['person_name'] = $this->input->post('person_name');
            $data['person_address'] = $this->input->post('person_address');
            $data['person_city'] = $this->input->post('person_city');
            $data['person_pincode'] = $this->input->post('person_pincode');
            $data['person_state'] = $this->input->post('person_state');
            $data['person_country'] = $this->input->post('person_country');
            $data['person_telephone'] = $this->input->post('person_telephone');
            $data['person_email'] = $this->input->post('person_email');
            $data['person_phone'] = $this->input->post('person_phone');
            $data['account_no'] = $this->input->post('account_no');
            $data['account_type'] = $this->input->post('account_type');
            $data['bank_name'] = $this->input->post('bank_name');
            $data['ifsc_code'] = $this->input->post('ifsc_code');
            $data['feedback'] = $this->input->post('feedback');	
            $data['is_approval'] = '1';
            $data['is_active'] = '1';	
            $data['lat'] = $this->input->post('lat');
            $data['lng'] = $this->input->post('lng');
            
            $user['name'] = $this->input->post('name');
            $user['email'] = $this->input->post('email');
            $user['phone'] = $this->input->post('phone');
            $user['vendor_id'] = '13';
            $user['lat'] = $this->input->post('lat');
            $user['lng'] = $this->input->post('lng');
            $user['password'] = md5('12345');
            
            $package =$this->input->post('package');
            $offer =$this->input->post('offer');			
            $resp = $this->SalesModel->add_pharmacy_form($data,$user,$package,$offer);
            simple_json_output($resp);
        }
    }

    public function update_pharmacy_form() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
            include('s3_config.php');
            $id = $this->input->post('listing_id');
            $file_query = $this->db->query("SELECT profile_pic as profile_image,vendor_sign,sign FROM `medical_stores` WHERE  user_id='$id'");
            $get_file = $file_query->row();
            $data['profile_pic'] = $get_file->profile_image;
            $data['vendor_sign'] = $get_file->vendor_sign;
            $data['sign'] = $get_file->sign;
            if (!empty($_FILES['profile_image']['name'])) {
                $img_name = $_FILES['profile_image']['name'];
                $img_size = $_FILES['profile_image']['size'];
                $img_tmp = $_FILES['profile_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file->profile_image != '') {
                            $file = "images/healthwall_avatar/" . $get_file->profile_image;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/healthwall_avatar/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['profile_pic'] = $actual_image_name;
                        }
                    }
                }
            }
            if (!empty($_FILES['vendor_sign']['name'])) {
                $img_name = $_FILES['vendor_sign']['name'];
                $img_size = $_FILES['vendor_sign']['size'];
                $img_tmp = $_FILES['vendor_sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file->vendor_sign != '') {
                            $file = "images/sales_form_files/" . $get_file->vendor_sign;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['vendor_sign'] = $actual_image_name;
                        }
                    }
                }
            }
            if (!empty($_FILES['sign']['name'])) {
                $img_name = $_FILES['sign']['name'];
                $img_size = $_FILES['sign']['size'];
                $img_tmp = $_FILES['sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file->sign != '') {
                            $file = "images/sales_form_files/" . $get_file->sign;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['sign'] = $actual_image_name;
                        }
                    }
                }
            }
          
            $data['executive_name'] = $this->input->post('executive_name');		
            $data['business_nature'] = $this->input->post('business_nature');
            $data['store_manager'] = $this->input->post('manager_name');
            $data['medical_name'] = $this->input->post('name');
            $data['address1'] = $this->input->post('address');
            $data['email'] = $this->input->post('email');
            $data['contact_no'] = $this->input->post('phone');
            $data['store_since'] = $this->input->post('since');
            $data['medicalwale_discount'] = $this->input->post('medicalwale_discount');	
            $data['reach_area'] = $this->input->post('radius_area');
            $data['store_time'] = $this->input->post('store_time');
            $data['is_24hrs_available'] = $this->input->post('is_24_hours');			
            $data['days_closed'] = $this->input->post('non_working_day');
            $data['delivery_time'] = $this->input->post('delivery_time');
            $data['free_home_delivery'] = $this->input->post('free_home_delivery');
            $data['min_order'] = $this->input->post('minimum_order_amount');
            $data['min_order_delivery_charge'] = $this->input->post('minimum_order_delivery');
			$data['night_delivery_charge'] = $this->input->post('night_delivery_amount');
            $data['online_offline'] = $this->input->post('online_offline');
            $data['person_name'] = $this->input->post('person_name');
            $data['person_address'] = $this->input->post('person_address');
            $data['person_city'] = $this->input->post('person_city');
            $data['person_pincode'] = $this->input->post('person_pincode');
            $data['person_state'] = $this->input->post('person_state');
            $data['person_country'] = $this->input->post('person_country');
            $data['person_telephone'] = $this->input->post('person_telephone');
            $data['person_email'] = $this->input->post('person_email');
            $data['person_phone'] = $this->input->post('person_phone');
            $data['account_no'] = $this->input->post('account_no');
            $data['account_type'] = $this->input->post('account_type');
            $data['bank_name'] = $this->input->post('bank_name');
            $data['ifsc_code'] = $this->input->post('ifsc_code');
            $data['feedback'] = $this->input->post('feedback');	
            $data['is_active'] = '1';
            $data['lat'] = $this->input->post('lat');
            $data['lng'] = $this->input->post('lng');
            $user['name'] = $this->input->post('name');
            $user['email'] = $this->input->post('email');
            $user['phone'] = $this->input->post('phone');
            $user['lat'] = $this->input->post('lat');
            $user['lng'] = $this->input->post('lng');
            $package =$this->input->post('package');
            $offer =$this->input->post('offer');
            $resp = $this->SalesModel->update_pharmacy_form($data,$user,$id,$package,$offer);
            simple_json_output($resp);
        }
    }
    
    
    public function update_vendor_form() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
            include('s3_config.php');
            $id = $this->input->post('listing_id');
            $file_query = $this->db->query("SELECT image as profile_image,vendor_sign,sign,vendor_type FROM `fitness_center` WHERE  user_id='$id'");
            $get_file = $file_query->row();
            $data['image'] = $get_file->profile_image;
            $data['vendor_sign'] = $get_file->vendor_sign;
            $data['sign'] = $get_file->sign;
            $vendor_type = $get_file->vendor_type;
            
            $file_query2 = $this->db->query("SELECT branch_image,gallery FROM `fitness_center_branch` WHERE  user_id='$id'");
            $get_file2 = $file_query2->row();
            $branch['branch_image'] = $get_file2->branch_image;
            $branch['gallery'] = $get_file2->gallery;
            
            if (!empty($_FILES['profile_image']['name'])) {
                $img_name = $_FILES['profile_image']['name'];
                $img_size = $_FILES['profile_image']['size'];
                $img_tmp = $_FILES['profile_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file->profile_image != '') {
                            $file = "images/healthwall_avatar/" . $get_file->profile_image;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/healthwall_avatar/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['image'] = $actual_image_name;
                        }
                    }
                }
            }
          
            if (!empty($_FILES['branch_image']['name'])) {
                $img_name = $_FILES['branch_image']['name'];
                $img_size = $_FILES['branch_image']['size'];
                $img_tmp = $_FILES['branch_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file2->branch_image != '') {
                            $file = "images/sales_form_files/" . $get_file2->branch_image;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $branch['branch_image'] = $actual_image_name;
                        }
                    }
                }
            }
            
            if (!empty($_FILES['gallery']['name'])) {
                $img_name = $_FILES['gallery']['name'];
                $img_size = $_FILES['gallery']['size'];
                $img_tmp = $_FILES['gallery']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file2->gallery != '') {
                            $file = "images/sales_form_files/" . $get_file2->gallery;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $branch['gallery'] = $actual_image_name;
                        }
                    }
                }
            }
           
            if (!empty($_FILES['vendor_sign']['name'])) {
                $img_name = $_FILES['vendor_sign']['name'];
                $img_size = $_FILES['vendor_sign']['size'];
                $img_tmp = $_FILES['vendor_sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file->vendor_sign != '') {
                            $file = "images/sales_form_files/" . $get_file->vendor_sign;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['vendor_sign'] = $actual_image_name;
                        }
                    }
                }
            }

            if (!empty($_FILES['sign']['name'])) {
                $img_name = $_FILES['sign']['name'];
                $img_size = $_FILES['sign']['size'];
                $img_tmp = $_FILES['sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file->sign != '') {
                            $file = "images/sales_form_files/" . $get_file->sign;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['sign'] = $actual_image_name;
                        }
                    }
                }
            }
            
            $data['vendor_type'] =$this->input->post('vendor_type');
            $updated_vendor_type =$this->input->post('vendor_type');
            $data['executive_name'] =$this->input->post('executive_name');
            $data['vendor_type'] =$this->input->post('vendor_type');
            $data['business_nature'] =$this->input->post('business_nature');
            $data['center_name'] =$this->input->post('name');
            $data['email'] =$this->input->post('email');
            $data['contact'] =$this->input->post('phone');
            $data['year'] =$this->input->post('since');
            $data['medicalwale_discount'] =$this->input->post('medicalwale_discount');
            $branch['branch_name'] =$this->input->post('branch_name');
            $branch['branch_address'] =$this->input->post('branch_address');
            $branch['city'] =$this->input->post('branch_city');
            $branch['state'] =$this->input->post('branch_state');
            $branch['pincode'] =$this->input->post('branch_pincode');
            $branch['branch_phone'] =$this->input->post('branch_contact');
            $branch['branch_business_category'] =$this->input->post('bussiness_category');
            $branch['about_branch'] =$this->input->post('about');
            $branch['branch_offer'] =$this->input->post('services');
            $branch['branch_facilities'] =$this->input->post('facility');
            $branch['map_location'] =$this->input->post('locator');
            $branch['is_free_trail'] =$this->input->post('free_trail');
            $branch['opening_hours'] =$this->input->post('working_hours');
            $branch['lat'] = $this->input->post('lat');
            $branch['lng'] = $this->input->post('lng');
            $data['online_offline'] =$this->input->post('online_offline');
            $data['person_name'] =$this->input->post('person_name');
            $data['person_address'] =$this->input->post('person_address');
            $data['person_city'] =$this->input->post('person_city');
            $data['person_pincode'] =$this->input->post('person_pincode');
            $data['person_state'] =$this->input->post('person_state');
            $data['person_country'] =$this->input->post('person_country');
            $data['person_telephone'] =$this->input->post('person_telephone');
            $data['person_email'] =$this->input->post('person_email');
            $data['person_phone'] =$this->input->post('person_phone');
            $data['account_no'] =$this->input->post('account_no');
            $data['bank_name'] =$this->input->post('bank_name');
            $data['account_type'] =$this->input->post('account_type');
            $data['ifsc_code'] =$this->input->post('ifsc_code');
            $data['feedback'] =$this->input->post('feedback');
            $data['manager_name'] =$this->input->post('manager_name');
            $data['is_active'] = '1';
            $user['name'] = $this->input->post('name');
            $user['email'] = $this->input->post('email');
            $user['phone'] = $this->input->post('phone');
            $user['dob'] = $this->input->post('dob');
            $user['gender'] = $this->input->post('gender');	
            $user['lat'] = $this->input->post('lat');
            $user['lng'] = $this->input->post('lng');
            $awards =$this->input->post('awards');
            $package =$this->input->post('package');
            $offer =$this->input->post('offer');
            $vendor_type =$this->input->post('vendor_type');
            $resp = $this->SalesModel->update_vendor_form($data,$branch,$user,$id,$awards,$package,$offer,$vendor_type,$updated_vendor_type);
            simple_json_output($resp);
        }
    }

    public function add_vendor_form() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
            include('s3_config.php');
            $data['image'] = '';
            if (!empty($_FILES['profile_image']['name'])) {
                $img_name = $_FILES['profile_image']['name'];
                $img_size = $_FILES['profile_image']['size'];
                $img_tmp = $_FILES['profile_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/healthwall_avatar/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['image'] = $actual_image_name;
                        }
                    }
                }
            }
            $branch['branch_image'] = '';
            if (!empty($_FILES['branch_image']['name'])) {
                $img_name = $_FILES['branch_image']['name'];
                $img_size = $_FILES['branch_image']['size'];
                $img_tmp = $_FILES['branch_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $branch['branch_image'] = $actual_image_name;
                        }
                    }
                }
            }
            $branch['gallery'] = '';
            if (!empty($_FILES['gallery']['name'])) {
                $img_name = $_FILES['gallery']['name'];
                $img_size = $_FILES['gallery']['size'];
                $img_tmp = $_FILES['gallery']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $branch['gallery'] = $actual_image_name;
                        }
                    }
                }
            }
            $data['vendor_sign'] = '';
            if (!empty($_FILES['vendor_sign']['name'])) {
                $img_name = $_FILES['vendor_sign']['name'];
                $img_size = $_FILES['vendor_sign']['size'];
                $img_tmp = $_FILES['vendor_sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['vendor_sign'] = $actual_image_name;
                        }
                    }
                }
            }
            $data['sign'] = '';
            if (!empty($_FILES['sign']['name'])) {
                $img_name = $_FILES['sign']['name'];
                $img_size = $_FILES['sign']['size'];
                $img_tmp = $_FILES['sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['sign'] = $actual_image_name;
                        }
                    }
                }
            }
            $data['executive_id'] =$this->input->post('user_id');
            $data['executive_name'] =$this->input->post('executive_name');
            $data['vendor_type'] =$this->input->post('vendor_type');
            $data['business_nature'] =$this->input->post('business_nature');
            $data['center_name'] =$this->input->post('name');
            $data['email'] =$this->input->post('email');
            $data['contact'] =$this->input->post('phone');
            $data['year'] =$this->input->post('since');
            $data['medicalwale_discount'] =$this->input->post('medicalwale_discount');
            $branch['branch_name'] =$this->input->post('branch_name');
            $branch['branch_address'] =$this->input->post('branch_address');
            $branch['city'] =$this->input->post('branch_city');
            $branch['state'] =$this->input->post('branch_state');
            $branch['pincode'] =$this->input->post('branch_pincode');
            $branch['branch_phone'] =$this->input->post('branch_contact');
            $branch['branch_business_category'] =$this->input->post('bussiness_category');
            $branch['about_branch'] =$this->input->post('about');
            $branch['branch_offer'] =$this->input->post('services');
            $branch['branch_facilities'] =$this->input->post('facility');
            $branch['map_location'] =$this->input->post('locator');
            $branch['is_free_trail'] =$this->input->post('free_trail');
            $branch['opening_hours'] =$this->input->post('working_hours');
            $branch['lat'] = $this->input->post('lat');
            $branch['lng'] = $this->input->post('lng');
            $data['online_offline']  =$this->input->post('online_offline');
            $data['person_name'] =$this->input->post('person_name');
            $data['person_address'] =$this->input->post('person_address');
            $data['person_city'] =$this->input->post('person_city');
            $data['person_pincode'] =$this->input->post('person_pincode');
            $data['person_state'] =$this->input->post('person_state');
            $data['person_country'] =$this->input->post('person_country');
            $data['person_telephone'] =$this->input->post('person_telephone');
            $data['person_email'] =$this->input->post('person_email');
            $data['person_phone'] =$this->input->post('person_phone');
            $data['account_no'] =$this->input->post('account_no');
            $data['bank_name'] =$this->input->post('bank_name');
            $data['account_type'] =$this->input->post('account_type');
            $data['ifsc_code'] =$this->input->post('ifsc_code');
            $data['feedback'] =$this->input->post('feedback');
            $data['is_active'] = '1';	
            $data['manager_name'] =$this->input->post('manager_name');
            $user['name'] = $this->input->post('name');
            $user['email'] = $this->input->post('email');
            $user['phone'] = $this->input->post('phone');
            $user['dob'] = $this->input->post('dob');
            $user['gender'] = $this->input->post('gender');	
            $user['lat'] = $this->input->post('lat');
            $user['lng'] = $this->input->post('lng');
            $user['password'] = md5('12345');
            $awards =$this->input->post('awards');
            $package =$this->input->post('package');
            $offer =$this->input->post('offer');
            $vendor_type =$this->input->post('vendor_type');
            $user['vendor_id'] = '6';
            date_default_timezone_set('Asia/Kolkata');
            $data['date'] = date('Y-m-d H:i:s');
            $resp = $this->SalesModel->add_vendor_form($data,$branch,$user,$awards,$package,$offer,$vendor_type);
            simple_json_output($resp);
        }
    }
    
    public function add_hospital_form() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
            include('s3_config.php');
            $data['image'] = '';
            if (!empty($_FILES['profile_image']['name'])) {
                $img_name = $_FILES['profile_image']['name'];
                $img_size = $_FILES['profile_image']['size'];
                $img_tmp = $_FILES['profile_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/healthwall_avatar/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['image'] = $actual_image_name;
                        }
                    }
                }
            }
            $branch['gallery'] = '';
            if (!empty($_FILES['gallery']['name'])) {
                $img_name = $_FILES['gallery']['name'];
                $img_size = $_FILES['gallery']['size'];
                $img_tmp = $_FILES['gallery']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $branch['gallery'] = $actual_image_name;
                        }
                    }
                }
            }
            $data['vendor_sign'] = '';
            if (!empty($_FILES['vendor_sign']['name'])) {
                $img_name = $_FILES['vendor_sign']['name'];
                $img_size = $_FILES['vendor_sign']['size'];
                $img_tmp = $_FILES['vendor_sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['vendor_sign'] = $actual_image_name;
                        }
                    }
                }
            }
            $data['sign'] = '';
            if (!empty($_FILES['sign']['name'])) {
                $img_name = $_FILES['sign']['name'];
                $img_size = $_FILES['sign']['size'];
                $img_tmp = $_FILES['sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['sign'] = $actual_image_name;
                        }
                    }
                }
            }
            $branch['doctor_profile_image'] = '';
            if (!empty($_FILES['doctor_profile_image']['name'])) {
                $img_name = $_FILES['doctor_profile_image']['name'];
                $img_size = $_FILES['doctor_profile_image']['size'];
                $img_tmp = $_FILES['doctor_profile_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $branch['doctor_profile_image'] = $actual_image_name;
                        }
                    }
                }
            }
            $data['executive_id'] =$this->input->post('user_id');
            $data['executive_name'] =$this->input->post('executive_name');
            $data['business_nature'] =$this->input->post('business_nature');
            $data['name_of_hospital'] =$this->input->post('name');
            $data['email'] =$this->input->post('email');
            $data['address'] =$this->input->post('address');
            $data['phone'] =$this->input->post('phone');
            $data['concern_person_name'] =$this->input->post('concern_person_name');
            $data['concern_person_phone'] =$this->input->post('concern_person_phone');
            $data['establishment_year'] =$this->input->post('since');
            $data['medicalwale_discount'] =$this->input->post('medicalwale_discount');
            $data['no_branches'] =$this->input->post('no_branches');
            $data['no_doctors'] =$this->input->post('no_doctors');
            
            $branch['name_of_branch'] =$this->input->post('branch_name');
            $branch['email'] =$this->input->post('branch_email');
            $branch['phone'] =$this->input->post('branch_contact');
            $branch['address'] =$this->input->post('branch_address');
            $branch['city'] =$this->input->post('branch_city');
            $branch['state'] =$this->input->post('branch_state');
            $branch['pincode'] =$this->input->post('branch_pincode');
            $branch['establishment_year'] =$this->input->post('branch_establishment_year');
            $branch['about_us'] =$this->input->post('about');
            $branch['services'] =$this->input->post('services');
            $branch['speciality'] =$this->input->post('speciality');
            $branch['services_id'] =$this->input->post('services_id');
            $branch['speciality_id'] =$this->input->post('speciality_id');
            $branch['map_location'] =$this->input->post('locator');
            $branch['facility'] =$this->input->post('facility');
            $branch['doctor_name'] =$this->input->post('doctor_name');
            $branch['doctor_qualifications'] =$this->input->post('doctor_qualifications');
            $branch['visiting_hours'] =$this->input->post('visiting_hours');
            $branch['working_hours']  =$this->input->post('working_hours');
            $branch['lat'] = $this->input->post('lat');
            $branch['lng'] = $this->input->post('lng');
            
            $data['online_offline'] =$this->input->post('online_offline');
            $data['person_name'] =$this->input->post('person_name');
            $data['person_address'] =$this->input->post('person_address');
            $data['person_city'] =$this->input->post('person_city');
            $data['person_pincode'] =$this->input->post('person_pincode');
            $data['person_state'] =$this->input->post('person_state');
            $data['person_country'] =$this->input->post('person_country');
            $data['person_telephone'] =$this->input->post('person_telephone');
            $data['person_email'] =$this->input->post('person_email');
            $data['person_phone'] =$this->input->post('person_phone');
            $data['account_no'] =$this->input->post('account_no');
            $data['bank_name'] =$this->input->post('bank_name');
            $data['account_type'] =$this->input->post('account_type');
            $data['ifsc_code'] =$this->input->post('ifsc_code');
            $data['feedback'] =$this->input->post('feedback');
            $data['is_approval'] = '1';
            $data['is_active'] = '1';
            $package =$this->input->post('package');
            $offer =$this->input->post('offer');
            
            $user['name'] = $this->input->post('name');
            $user['email'] = $this->input->post('email');
            $user['phone'] = $this->input->post('phone');
            $user['vendor_id'] = '8';
            $user['lat'] = $this->input->post('lat');
            $user['lng'] = $this->input->post('lng');
            $user['password'] = md5('12345');
            
            date_default_timezone_set('Asia/Kolkata');
            $data['date'] = date('Y-m-d H:i:s');        
            
            $resp = $this->SalesModel->add_hospital_form($data,$branch,$user,$package,$offer);
            simple_json_output($resp);
        }
    }
    
    
    public function update_hospital_form() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
            include('s3_config.php');
            
            $id = $this->input->post('listing_id');

            $file_query = $this->db->query("SELECT image as profile_image,vendor_sign,sign FROM `hospitals` WHERE user_id='$id'");
            $get_file = $file_query->row();
            $data['image'] = $get_file->profile_image;
            $data['vendor_sign'] = $get_file->vendor_sign;
            $data['sign'] = $get_file->sign;
            
            $file_query2 = $this->db->query("SELECT gallery,doctor_profile_image FROM `hospitals_branch` WHERE hospital_id='$id'");
            $get_file2 = $file_query2->row();
            $branch['doctor_profile_image'] = $get_file2->doctor_profile_image;
            $branch['gallery'] = $get_file2->gallery;

            if (!empty($_FILES['profile_image']['name'])) {
                $img_name = $_FILES['profile_image']['name'];
                $img_size = $_FILES['profile_image']['size'];
                $img_tmp = $_FILES['profile_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file->profile_image != '') {
                            $file = "images/healthwall_avatar/" . $get_file->profile_image;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/healthwall_avatar/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['image'] = $actual_image_name;
                        }
                    }
                }
            }
          
            if (!empty($_FILES['doctor_profile_image']['name'])) {
                $img_name = $_FILES['doctor_profile_image']['name'];
                $img_size = $_FILES['doctor_profile_image']['size'];
                $img_tmp = $_FILES['doctor_profile_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file2->doctor_profile_image != '') {
                            $file = "images/sales_form_files/" . $get_file2->doctor_profile_image;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $branch['doctor_profile_image'] = $actual_image_name;
                        }
                    }
                }
            }
            
            if (!empty($_FILES['gallery']['name'])) {
                $img_name = $_FILES['gallery']['name'];
                $img_size = $_FILES['gallery']['size'];
                $img_tmp = $_FILES['gallery']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file2->gallery != '') {
                            $file = "images/sales_form_files/" . $get_file2->gallery;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $branch['gallery'] = $actual_image_name;
                        }
                    }
                }
            }
           
            if (!empty($_FILES['vendor_sign']['name'])) {
                $img_name = $_FILES['vendor_sign']['name'];
                $img_size = $_FILES['vendor_sign']['size'];
                $img_tmp = $_FILES['vendor_sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file->vendor_sign != '') {
                            $file = "images/sales_form_files/" . $get_file->vendor_sign;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['vendor_sign'] = $actual_image_name;
                        }
                    }
                }
            }

            if (!empty($_FILES['sign']['name'])) {
                $img_name = $_FILES['sign']['name'];
                $img_size = $_FILES['sign']['size'];
                $img_tmp = $_FILES['sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file->sign != '') {
                            $file = "images/sales_form_files/" . $get_file->sign;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['sign'] = $actual_image_name;
                        }
                    }
                }
            }
            
            $data['executive_name'] =$this->input->post('executive_name');
            $data['business_nature'] =$this->input->post('business_nature');
            $data['name_of_hospital'] =$this->input->post('name');
            $data['email'] =$this->input->post('email');
            $data['address'] =$this->input->post('address');
            $data['phone'] =$this->input->post('phone');
            $data['concern_person_name'] =$this->input->post('concern_person_name');
            $data['concern_person_phone'] =$this->input->post('concern_person_phone');
            $data['establishment_year'] =$this->input->post('since');
            $data['medicalwale_discount'] =$this->input->post('medicalwale_discount');
            $data['no_branches'] =$this->input->post('no_branches');
            $data['no_doctors'] =$this->input->post('no_doctors');
            $data['is_active'] = '1';
            
            $branch['name_of_branch'] =$this->input->post('branch_name');
            $branch['email'] =$this->input->post('branch_email');
            $branch['phone'] =$this->input->post('branch_contact');
            $branch['address'] =$this->input->post('branch_address');
            $branch['city'] =$this->input->post('branch_city');
            $branch['state'] =$this->input->post('branch_state');
            $branch['pincode'] =$this->input->post('branch_pincode');
            $branch['establishment_year'] =$this->input->post('branch_establishment_year');
            $branch['about_us'] =$this->input->post('about');
            $branch['services'] =$this->input->post('services');
            $branch['speciality'] =$this->input->post('speciality');
            $branch['services_id'] =$this->input->post('services_id');
            $branch['speciality_id'] =$this->input->post('speciality_id');
            $branch['map_location'] =$this->input->post('locator');
            $branch['facility'] =$this->input->post('facility');
            $branch['doctor_name'] =$this->input->post('doctor_name');
            $branch['doctor_qualifications'] =$this->input->post('doctor_qualifications');
            $branch['visiting_hours'] =$this->input->post('visiting_hours');
            $branch['working_hours']  =$this->input->post('working_hours');
            $branch['lat'] = $this->input->post('lat');
            $branch['lng'] = $this->input->post('lng');
            
            $data['online_offline'] =$this->input->post('online_offline');
            $data['person_name'] =$this->input->post('person_name');
            $data['person_address'] =$this->input->post('person_address');
            $data['person_city'] =$this->input->post('person_city');
            $data['person_pincode'] =$this->input->post('person_pincode');
            $data['person_state'] =$this->input->post('person_state');
            $data['person_country'] =$this->input->post('person_country');
            $data['person_telephone'] =$this->input->post('person_telephone');
            $data['person_email'] =$this->input->post('person_email');
            $data['person_phone'] =$this->input->post('person_phone');
            $data['account_no'] =$this->input->post('account_no');
            $data['bank_name'] =$this->input->post('bank_name');
            $data['account_type'] =$this->input->post('account_type');
            $data['ifsc_code'] =$this->input->post('ifsc_code');
            $data['feedback'] =$this->input->post('feedback');
            $package =$this->input->post('package');
            $offer =$this->input->post('offer');
            
            $user['name'] = $this->input->post('name');
            $user['email'] = $this->input->post('email');
            $user['phone'] = $this->input->post('phone');
            $user['lat'] = $this->input->post('lat');
            $user['lng'] = $this->input->post('lng');
            
            $resp = $this->SalesModel->update_hospital_form($data,$branch,$user,$id,$package,$offer);
            simple_json_output($resp);
        }
    }
    
    
    public function add_lab_form() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
            include('s3_config.php');

            $data['profile_pic'] = '';
            if (!empty($_FILES['profile_image']['name'])) {
                $img_name = $_FILES['profile_image']['name'];
                $img_size = $_FILES['profile_image']['size'];
                $img_tmp = $_FILES['profile_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/healthwall_avatar/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['profile_pic'] = $actual_image_name;
                        }
                    }
                }
            }
            $branch['profile_pic'] = '';
            if (!empty($_FILES['branch_image']['name'])) {
                $img_name = $_FILES['branch_image']['name'];
                $img_size = $_FILES['branch_image']['size'];
                $img_tmp = $_FILES['branch_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $branch['profile_pic'] = $actual_image_name;
                        }
                    }
                }
            }

            $data['vendor_sign'] = '';
            if (!empty($_FILES['vendor_sign']['name'])) {
                $img_name = $_FILES['vendor_sign']['name'];
                $img_size = $_FILES['vendor_sign']['size'];
                $img_tmp = $_FILES['vendor_sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['vendor_sign'] = $actual_image_name;
                        }
                    }
                }
            }
            $data['sign'] = '';
            if (!empty($_FILES['sign']['name'])) {
                $img_name = $_FILES['sign']['name'];
                $img_size = $_FILES['sign']['size'];
                $img_tmp = $_FILES['sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['sign'] = $actual_image_name;
                        }
                    }
                }
            }
            $data['executive_id'] =$this->input->post('user_id');
            $data['executive_name'] =$this->input->post('executive_name');
            $data['business_nature'] =$this->input->post('business_nature');
            $data['lab_name'] =$this->input->post('name');
            $data['email'] =$this->input->post('email');
            $data['contact_no'] =$this->input->post('phone');
            $data['store_since'] =$this->input->post('since');
            $data['no_branches'] =$this->input->post('no_branches');
            $data['medicalwale_discount'] =$this->input->post('medicalwale_discount');
            
            
            $branch['lab_branch_name'] =$this->input->post('branch_name');
            $branch['contact_no'] =$this->input->post('branch_contact');
            $branch['address1'] =$this->input->post('branch_address');
            $branch['city'] =$this->input->post('branch_city');
            $branch['state'] =$this->input->post('branch_state');
            $branch['pincode'] =$this->input->post('branch_pincode');
            $branch['contact_no'] =$this->input->post('branch_contact');
            $branch['email'] =$this->input->post('branch_email');
            $branch['payment_type'] =$this->input->post('payment_mode');
            $branch['services'] =$this->input->post('services');
            $branch['home_visit'] =$this->input->post('home_visit');
            $branch['opening_hours'] =$this->input->post('working_hours');
            $branch['test_list'] =$this->input->post('test_list');
            $branch['latitude'] = $this->input->post('lat');
            $branch['longitude'] = $this->input->post('lng');
            
            $data['online_offline'] =$this->input->post('online_offline');
            $data['person_name'] =$this->input->post('person_name');
            $data['person_address'] =$this->input->post('person_address');
            $data['person_city'] =$this->input->post('person_city');
            $data['person_pincode'] =$this->input->post('person_pincode');
            $data['person_state'] =$this->input->post('person_state');
            $data['person_country'] =$this->input->post('person_country');
            $data['person_telephone'] =$this->input->post('person_telephone');
            $data['person_email'] =$this->input->post('person_email');
            $data['person_phone'] =$this->input->post('person_phone');
            $data['account_no'] =$this->input->post('account_no');
            $data['bank_name'] =$this->input->post('bank_name');
            $data['account_type'] =$this->input->post('account_type');
            $data['ifsc_code'] =$this->input->post('ifsc_code');
            $data['feedback'] =$this->input->post('feedback');
            $data['is_active'] = '1';
            
            $user['name'] = $this->input->post('name');
            $user['email'] = $this->input->post('email');
            $user['phone'] = $this->input->post('phone');
            $user['vendor_id'] = '10';
            $user['lat'] = $this->input->post('lat');
            $user['lng'] = $this->input->post('lng');
            $user['password'] = md5('12345');
            
            $package =$this->input->post('package');
            $offer =$this->input->post('offer');
                    
            $resp = $this->SalesModel->add_lab_form($data,$branch,$user,$package,$offer);
            simple_json_output($resp);
        }
    }
    
    
    public function update_lab_form() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
            include('s3_config.php');
            $id = $this->input->post('listing_id');
            $file_query = $this->db->query("SELECT profile_pic as profile_image,vendor_sign,sign FROM `lab_center` WHERE  user_id='$id'");
            $get_file = $file_query->row();
            $data['profile_pic'] = $get_file->profile_image;
            $data['vendor_sign'] = $get_file->vendor_sign;
            $data['sign'] = $get_file->sign;
            
            $file_query2 = $this->db->query("SELECT profile_pic as branch_image FROM `lab_center_branch` WHERE user_id='$id'");
            $get_file2 = $file_query2->row();
            $branch['profile_pic'] = $get_file2->branch_image;
            
            if (!empty($_FILES['profile_image']['name'])) {
                $img_name = $_FILES['profile_image']['name'];
                $img_size = $_FILES['profile_image']['size'];
                $img_tmp = $_FILES['profile_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file->profile_image != '') {
                            $file = "images/healthwall_avatar/" . $get_file->profile_image;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/healthwall_avatar/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['profile_pic'] = $actual_image_name;
                        }
                    }
                }
            }
            
          
            if (!empty($_FILES['branch_image']['name'])) {
                $img_name = $_FILES['branch_image']['name'];
                $img_size = $_FILES['branch_image']['size'];
                $img_tmp = $_FILES['branch_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file2->branch_image != '') {
                            $file = "images/sales_form_files/" . $get_file2->branch_image;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $branch['profile_pic'] = $actual_image_name;
                        }
                    }
                }
            }
            
            if (!empty($_FILES['vendor_sign']['name'])) {
                $img_name = $_FILES['vendor_sign']['name'];
                $img_size = $_FILES['vendor_sign']['size'];
                $img_tmp = $_FILES['vendor_sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file->vendor_sign != '') {
                            $file = "images/sales_form_files/" . $get_file->vendor_sign;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['vendor_sign'] = $actual_image_name;
                        }
                    }
                }
            }

            if (!empty($_FILES['sign']['name'])) {
                $img_name = $_FILES['sign']['name'];
                $img_size = $_FILES['sign']['size'];
                $img_tmp = $_FILES['sign']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if (in_array($ext, $img_format)) {
                        if ($get_file->sign != '') {
                            $file = "images/sales_form_files/" . $get_file->sign;
                            @unlink(trim($file));
                            DeleteFromToS3($file);
                        }
                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                        $actual_image_path = 'images/sales_form_files/' . $actual_image_name;
                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                            $data['sign'] = $actual_image_name;
                        }
                    }
                }
            }
            
            
            $data['executive_name'] =$this->input->post('executive_name');
            $data['business_nature'] =$this->input->post('business_nature');
            $data['lab_name'] =$this->input->post('name');
            $data['email'] =$this->input->post('email');
            $data['contact_no'] =$this->input->post('phone');
            $data['store_since'] =$this->input->post('since');
            $data['no_branches'] =$this->input->post('no_branches');
            $data['medicalwale_discount'] =$this->input->post('medicalwale_discount');
            $data['is_active'] = '1';
            
            $branch['lab_branch_name'] =$this->input->post('branch_name');
            $branch['contact_no'] =$this->input->post('branch_contact');
            $branch['address1'] =$this->input->post('branch_address');
            $branch['city'] =$this->input->post('branch_city');
            $branch['state'] =$this->input->post('branch_state');
            $branch['pincode'] =$this->input->post('branch_pincode');
            $branch['contact_no'] =$this->input->post('branch_contact');
            $branch['email'] =$this->input->post('branch_email');
            $branch['payment_type'] =$this->input->post('payment_mode');
            $branch['services'] =$this->input->post('services');
            $branch['home_visit'] =$this->input->post('home_visit');
            $branch['opening_hours'] =$this->input->post('working_hours');
            $branch['test_list'] =$this->input->post('test_list');
            $branch['latitude'] = $this->input->post('lat');
            $branch['longitude'] = $this->input->post('lng');
            
            $data['online_offline'] =$this->input->post('online_offline');
            $data['person_name'] =$this->input->post('person_name');
            $data['person_address'] =$this->input->post('person_address');
            $data['person_city'] =$this->input->post('person_city');
            $data['person_pincode'] =$this->input->post('person_pincode');
            $data['person_state'] =$this->input->post('person_state');
            $data['person_country'] =$this->input->post('person_country');
            $data['person_telephone'] =$this->input->post('person_telephone');
            $data['person_email'] =$this->input->post('person_email');
            $data['person_phone'] =$this->input->post('person_phone');
            $data['account_no'] =$this->input->post('account_no');
            $data['bank_name'] =$this->input->post('bank_name');
            $data['account_type'] =$this->input->post('account_type');
            $data['ifsc_code'] =$this->input->post('ifsc_code');
            $data['feedback'] =$this->input->post('feedback');
            
            $package =$this->input->post('package');
            $offer =$this->input->post('offer');
            
            $user['name'] = $this->input->post('name');
            $user['email'] = $this->input->post('email');
            $user['phone'] = $this->input->post('phone');
            $user['lat'] = $this->input->post('lat');
            $user['lng'] = $this->input->post('lng');
            
            $resp = $this->SalesModel->update_lab_form($data,$branch,$user,$id,$package,$offer);
            simple_json_output($resp);
        }
    }

    public function vendor_pharmacy_list() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SalesModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->SalesModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id = $params['user_id'];
                    $resp = $this->SalesModel->vendor_pharmacy_list($user_id);
                }
                json_outputs($resp);
            }
        }
    }
    
     public function doctor_form_list() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SalesModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->SalesModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id = $params['user_id'];
                    //$user_id = '';
                    $resp = $this->SalesModel->doctor_form_list($user_id);
                }
                json_outputs($resp);
            }
        }
    }
    
    
    public function vendor_form_list() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SalesModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->SalesModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id = $params['user_id'];
                    $vendor_type = $params['vendor_type'];
                    //$user_id = '';
                    $resp = $this->SalesModel->vendor_form_list($user_id,$vendor_type);
                }
                json_outputs($resp);
            }
        }
    }
    
    public function vendor_hospital_list() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SalesModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->SalesModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id = $params['user_id'];
                    $resp = $this->SalesModel->vendor_hospital_list($user_id);
                }
                json_outputs($resp);
            }
        }
    }
    
    public function lab_form_list() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SalesModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->SalesModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id = $params['user_id'];
                    $resp = $this->SalesModel->lab_form_list($user_id);
                }
                json_outputs($resp);
            }
        }
    }
    
    public function doctor_degree_list() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SalesModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->SalesModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $resp = $this->SalesModel->doctor_degree_list();
                }
                json_outputs($resp);
            }
        }
    }
    
    public function business_category() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SalesModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->SalesModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $resp = $this->SalesModel->business_category();
                }
                json_outputs($resp);
            }
        }
    }
    
    public function services() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SalesModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->SalesModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $resp = $this->SalesModel->services();
                }
                json_outputs($resp);
            }
        }
    }
    
    public function branch_facility() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SalesModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->SalesModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $resp = $this->SalesModel->branch_facility();
                }
                json_outputs($resp);
            }
        }
    }
    
    public function lab_services() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SalesModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->SalesModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $resp = $this->SalesModel->lab_services();
                }
                json_outputs($resp);
            }
        }
    }
    
    public function hospital_services() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SalesModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->SalesModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $resp = $this->SalesModel->hospital_services();
                }
                json_outputs($resp);
            }
        }
    }
    
    public function hospital_speciality() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SalesModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->SalesModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $resp = $this->SalesModel->hospital_speciality();
                }
                json_outputs($resp);
            }
        }
    }
    
    public function lab_test() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SalesModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->SalesModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $resp = $this->SalesModel->lab_test();
                }
                json_outputs($resp);
            }
        }
    }
    
    public function area_expertise() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SalesModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->SalesModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $resp = $this->SalesModel->area_expertise();
                }
                json_outputs($resp);
            }
        }
    }
    
    public function test() {
        $this->load->model('SalesModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SalesModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->SalesModel->auth();
                if ($response['status'] == 200) {
                    $data = $this->input->post('package');
                    $resp = $this->SalesModel->test($data);
                    json_outputs($resp);
                }
                
            }
        }
    }

}

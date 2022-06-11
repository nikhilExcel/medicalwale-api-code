<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Elastic extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
            
        $this->load->library('ElasticSearch');

    }
    public function insert_elastic()
    {
         /*$hospital_query = $this->db->query("SELECT id,category,user_id,image,name_of_hospital,address,lat,lng FROM hospitals  ");
            $hospital_count = $hospital_query->num_rows();
            if ($hospital_count > 0) {
                foreach ($hospital_query->result_array() as $hospital_row) {
        $id = $hospital_row['id'];
        $category = str_replace("null", "", $hospital_row['category']);
        //$s="General Hospital,Maternity Nursing Home,546,535,523";
              $myArray = explode(',', $category);
                      foreach($myArray as $as){
                          if(preg_replace("/[^A-Z]+/", "", $as)){
                             
                              $asd[]=$as;
                          }
                          
                      }
                $str = implode (", ", $asd);
               
                $data=array('category'=>'1');
                $this->db->set('category');
                $this->db->where('id',$id);
                $this->db->update('hospitals',$data);
                
                }
            }*/
    
        $doctor_query = $this->db->query("SELECT doctor_list.id,doctor_list.category,doctor_list.certified,doctor_list.mba,doctor_list.consultation_fee,doctor_list.experience,doctor_list.discount,doctor_list.gender,doctor_list.service,doctor_list.recommended,doctor_clinic.lat,doctor_clinic.lng,doctor_list.user_id,doctor_list.image,doctor_list.doctor_name,doctor_list.speciality,doctor_list.qualification,doctor_list.address FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.is_active = 1 ");
       $doctor_count = $doctor_query->num_rows();
                    if ($doctor_count > 0) {
                       
                        foreach ($doctor_query->result_array() as $doctor_row) {
                            $data1=array();
                            $id = $doctor_row['id'];
                            $listing_id = $doctor_row['user_id'];
                            $name = str_replace("null", "", $doctor_row['doctor_name']);
                            $field1 = str_replace("null", "", $doctor_row['speciality']);
                            $field2 = str_replace("null", "", $doctor_row['qualification']);
                            $field3 = str_replace("null", "", $doctor_row['address']);
                            $doctor_image = str_replace("null", "", $doctor_row['image']);
                            $category = str_replace("null", "", $doctor_row['category']);
                            $lat = str_replace("null", "", $doctor_row['lat']);
                            $lng = str_replace("null", "", $doctor_row['lng']);
                            $experience = str_replace("null", "", $doctor_row['experience']);
                                 $experience1=round($experience, 0);
                            $discount = str_replace("null", "", $doctor_row['discount']);
                            $gender = str_replace("null", "", $doctor_row['gender']);
                            $service = str_replace("null", "", $doctor_row['service']);
                            $mba = str_replace("null", "", $doctor_row['mba']);
                            $certified = str_replace("null", "", $doctor_row['certified']);
                            $recommended = str_replace("null", "", $doctor_row['recommended']);
                            $consultation_fee = str_replace("null", "", $doctor_row['consultation_fee']);
                            if ($doctor_image != '') {
                               // $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $doctor_image;
                               
                              $image =  'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'. $doctor_image;
                            } else {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                            }
                            
                            $myArray = explode(',', $category);
                               
                            foreach ($myArray as $doctor_cat_rowa) {
                                        if($doctor_cat_rowa!=""){
                                        $doctor_cat_query = $this->db->query("SELECT business_category.category FROM `business_category` where business_category.id=$doctor_cat_rowa");
                                        $doctor_cat_count = $doctor_cat_query->num_rows();
                                        if ($doctor_cat_count > 0){
                                             foreach ($doctor_cat_query->result_array() as $doctor_row1) { 
                                            $data1[]=$doctor_row1['category'];
                                             }
                                        }
                                    }
                            }
                            $str = implode (", ", $data1);
                          
                              $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$listing_id'");
                        $doctor_cat_count = $query_rating->num_rows();
                         if ($doctor_cat_count > 0){
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                         }else{
                           $total_rating = '0';  
                         }
                    
                    
                           $doctor = array(
                                'listing_id' => $listing_id,
                                'name' => $name,
                                'image' => $image,
                                'field1' => $str,
                                'field2' => $field2,
                                'field3' => $field3,
                                'location.lat'=>$lat,
                                'location.lon'=>$lng,
                                'rating'=>$total_rating,
                                'experience'=>$experience1,
                                'gender'=>$gender,
                                'service'=>$service,
                                'discount'=>$discount,
                                'mba'=>$mba,
                                'certified'=>$certified,
                                'recommended'=>$recommended,
                                'consultation_fee'=>$consultation_fee
                            );
                            
                  // $data[]=$this->elasticsearch->add("general", $id, $doctor);
                    
                        }
                    }
	 echo "<pre>";
    print_r($doctor);
    }
    
     public function insert_elastich()
    {
     // $id="1";
       
        $doctor_query = $this->db->query("SELECT id,user_id,image,doctor_name,speciality,qualification,address FROM doctor_list WHERE `is_active` = 1 and `category` LIKE '%52%' ");
       $doctor_count = $doctor_query->num_rows();
                    if ($doctor_count > 0) {
                        foreach ($doctor_query->result_array() as $doctor_row) {
                            $id = $doctor_row['id'];
                            $listing_id = $doctor_row['user_id'];
                            $name = str_replace("null", "", $doctor_row['doctor_name']);
                            $field1 = str_replace("null", "", $doctor_row['speciality']);
                            $field2 = str_replace("null", "", $doctor_row['qualification']);
                            $field3 = str_replace("null", "", $doctor_row['address']);
                            $doctor_image = str_replace("null", "", $doctor_row['image']);
                            if ($doctor_image != '') {
                               // $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $doctor_image;
                               
                              $image =  'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'. $doctor_image;
                            } else {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                            }
                            $doctor = array(
                                'listing_id' => $listing_id,
                                'name' => $name,
                                'image' => $image,
                                'field1' => $field1,
                                'field2' => $field2,
                                'field3' => $field3,
                                'location.lat'=>$lat,
                                'location.lon'=>$lng,
                                'rating'=>$total_rating
                            );
                            
                         //   print_r($doctor);
                   $this->elasticsearch->add("Homeopathic","Homeopathic", $id, $doctor);
                    
                        }
                    }
       
    //	$data = array("name"=>"nisse", "age"=>"14", "sex"=>"male");
   

 //  exit();
    }
	
	 public function insert_dentist_elastich()
    {
     echo "dinesh";
       die();
        $doctor_query = $this->db->query("SELECT id,user_id,image,name_of_hospital,about_us,address FROM dentists_clinic_list WHERE `is_active` = 1 ");
       $doctor_count = $doctor_query->num_rows();
                    if ($doctor_count > 0) {
                        foreach ($doctor_query->result_array() as $doctor_row) {
                            $id = $doctor_row['id'];
                            $listing_id = $doctor_row['user_id'];
                            $name = str_replace("null", "", $doctor_row['name_of_hospital']);
                            $field1 = str_replace("null", "", $doctor_row['about_us']);
                            $field2 = "",
                            $field3 = str_replace("null", "", $doctor_row['address']);
                            $doctor_image = str_replace("null", "", $doctor_row['image']);
                            if ($doctor_image != '') {
                               // $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $doctor_image;
                               
                              $image =  'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'. $doctor_image;
                            } else {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                            }
                            $dentist_doctor = array(
                                'listing_id' => $listing_id,
                                'name' => $name,
                                'image' => $image,
                                'field1' => $field1,
                                'field2' => $field2,
                                'field3' => $field3,
                            );
                            
                         //   print_r($doctor);
                   $data[]=$this->elasticsearch->add("dentist_clinic_list","dentist_clinic_list", $id, $dentist_doctor);
                    
                        }
                    }
          print_r($data); 
    //	$data = array("name"=>"nisse", "age"=>"14", "sex"=>"male");
   

 //  exit();
    }
    
    
   
    public function insert_pharmacy_all()
    {
     // $id="1";
       
       $pharmacy_query = $this->db->query(" SELECT mba,recommended,certified,discount_description,medicalwale_discount,otc_discount,ethical_discount,surgical_discount,perscribed_discount,generic_discount,`id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND visible <> '0' AND type <> 'branch'");
                $pharmacy_count = $pharmacy_query->num_rows();
                if ($pharmacy_count > 0) {
                    foreach ($pharmacy_query->result_array() as $pharmacy_row) {
                     $id = $pharmacy_row['id'];
                        
                          $data[]=$this->elasticsearch->add("pharmacy1", $id, $pharmacy_row);
                    }
                  
                }
              print_r($data);  
    }
    
     public function insert_pharmacy_allgenrico()
    {
     // $id="1";
       
       $pharmacy_query = $this->db->query("SELECT mba,recommended,certified,user_id,discount_description,medicalwale_discount,`id`, surgical_discount,perscribed_discount,`pharmacy_branch_user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no,`profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance,otc_discount,ethical_discount,generic_discount  FROM medical_stores  WHERE is_active= '1' AND visible = '1' AND type = 'branch'");
                $pharmacy_count = $pharmacy_query->num_rows();
                if ($pharmacy_count > 0) {
                    foreach ($pharmacy_query->result_array() as $pharmacy_row) {
                     $id = $pharmacy_row['id']; 
                    
                          $data[]=$this->elasticsearch->add("pharmacygenerico", $id, $pharmacy_row);
                    }
                  
                }
              print_r($data);  
            

    }
    
    
    
    
    public function insert_pharmacy()
    {
     // $id="1";
       
       $pharmacy_query = $this->db->query("SELECT id,lat,lng,user_id,profile_pic,medical_name,address1 FROM medical_stores WHERE `is_active` = 1  ");
                $pharmacy_count = $pharmacy_query->num_rows();
                if ($pharmacy_count > 0) {
                    foreach ($pharmacy_query->result_array() as $pharmacy_row) {
                          $id = $pharmacy_row['id'];
                        $listing_id = $pharmacy_row['user_id'];
                        $name = str_replace("null", "", $pharmacy_row['medical_name']);
                        $field1 = '';
                        $field2 = '';
                        $field3 = str_replace("null", "", $pharmacy_row['address1']);
                        $pharmacy_image = str_replace("null", "", $pharmacy_row['profile_pic']);
                        if ($pharmacy_image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $pharmacy_image;
                        } else {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        $lat = str_replace("null", "", $pharmacy_row['lat']);
                        $lng = str_replace("null", "", $pharmacy_row['lng']);
                         $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM medical_stores_review WHERE medical_stores_id='$listing_id'");
                  $query_pharmacy_count = $query_pharmacy->num_rows();
                         if ($query_pharmacy_count > 0){
                $row_pharma = $query_pharmacy->row_array();
               $rating = $row_pharma['avg_rating'];
               
                if ($rating === NULL) {
                    $rating = '0';
                } 
                             
                         }else{
                           $rating = '0';  
                         }
               
                        $pharmacy = array(
                            'listing_id' => $listing_id,
                            'name' => $name,
                            'image' => $image,
                            'field1' => $field1,
                            'field2' => $field2,
                            'field3' => $field3,
                            'location.lat'=>$lat,
                            'location.lon'=>$lng,
                            'rating'=>$rating
                        );
                        //echo "<pre>";
                        //print_r($pharmacy);
                        //exit();
                          $data[]=$this->elasticsearch->add("medical", $id, $pharmacy);
                    }
                  
                }
              print_r($data);  
            

    }
    
     public function insert_ayurveda()
     {
       $ayurveda_query = $this->db->query("SELECT id,profile_pic,ayurveda_name,address1 FROM ayurveda WHERE `is_active` = 1 ");
            $ayurveda_count = $ayurveda_query->num_rows();
            if ($ayurveda_count > 0) {
                foreach ($ayurveda_query->result_array() as $ayurveda_row) {
                     $id = $ayurveda_row['id'];
                    $name = str_replace("null", "", $ayurveda_row['ayurveda_name']);
                    $field1 = '';
                    $field2 = '';
                    $field3 = str_replace("null", "", $ayurveda_row['address1']);
                    $ayurveda_image = str_replace("null", "", $ayurveda_row['profile_pic']);
                    if ($ayurveda_image != '') {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/ayurveda_images/' . $ayurveda_image;
                    } else {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                    $ayurveda = array(
                        'listing_id' => $user_id,
                        'name' => $name,
                        'image' => $image,
                        'field1' => $field1,
                        'field2' => $field2,
                        'field3' => $field3
                    );
                     $this->elasticsearch->add("ayurveda", $id, $ayurveda);
                }
             
            } 
         
     }
     
     
     public function insert_lab()
     {
        $labs_query = $this->db->query("SELECT id,user_id,profile_pic,lab_name,address1,latitude,longitude,recommended,certified,mba FROM lab_center WHERE `is_active` = 1  ");
            $labs_count = $labs_query->num_rows();
            if ($labs_count > 0) {
                foreach ($labs_query->result_array() as $labs_row) {
                      $id = $labs_row['id'];
                    $listing_id = $labs_row['user_id'];
                    $name = str_replace("null", "", $labs_row['lab_name']);
                    $field1 = '';
                    $field2 = '';
                    $field3 = str_replace("null", "", $labs_row['address1']);
                    $lat = str_replace("null", "", $labs_row['latitude']);
                    $lng = str_replace("null", "", $labs_row['longitude']);
                    $recommended = str_replace("null", "", $labs_row['recommended']);
                    $certified = str_replace("null", "", $labs_row['certified']);
                    $mba = str_replace("null", "", $labs_row['mba']);
                    $labs_image = str_replace("null", "", $labs_row['profile_pic']);
                    if ($labs_image != '') {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/labcenter_images/' . $labs_image;
                    } else {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                    $labs = array(
                        'listing_id' => $listing_id,
                        'name' => $name,
                        'image' => $image,
                        'field1' => $field1,
                        'field2' => $field2,
                        'field3' => $field3,
                        'location.lat'=>$lat,
                        'location.lon'=>$lng,
                        'recommended' => $recommended,
                        'certified'=>$certified,
                        'mba'=>$mba
                    );
                    //print_r($labs);
                      $this->elasticsearch->add("labs", $id, $labs);
                }
            }
         
     }
     
      public function insert_hospital()
     {
	       $this->elasticsearch->delete_index("hospital");
          $hospital_query = $this->db->query("SELECT id,user_id,image,name_of_hospital,address,lat,lng,category FROM hospitals  WHERE `is_active` = 1 ");
            $hospital_count = $hospital_query->num_rows();
            if ($hospital_count > 0) {
                foreach ($hospital_query->result_array() as $hospital_row) {
                      $id = $hospital_row['id'];
                    $listing_id = $hospital_row['user_id'];
                    $name = str_replace("null", "", $hospital_row['name_of_hospital']);
                    $field1 = '';
                    $field2 = '';
                    $field3 = str_replace("null", "", $hospital_row['address']);
                    $lat = str_replace("null", "", $hospital_row['lat']);
                    $lng = str_replace("null", "", $hospital_row['lng']);
                    $hospital_image = str_replace("null", "", $hospital_row['image']);
                    $category = str_replace("null", "", $hospital_row['category']);
                    if ($hospital_image != '') {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $hospital_image;
                    } else {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                    
                    $hospital = array(
                        'listing_id' => $listing_id,
                        'name' => $name,
                        'image' => $image,
                        'field1' => $field1,
                        'field2' => $field2,
                        'field3' => $field3,
                        'lat'=> $lat,
                        'lng'=> $lng,
                        'category'=>$category
                    );
                    
                   // print_r($hospital);
                     $dataa[]=$this->elasticsearch->add("hospital","hospital", $id, $hospital);
                }
                print_r($dataa);
              
            } 
     }
	
	 public function insert_hospital_package()
     {
	       //$this->elasticsearch->delete_index("hospital");
          $hospital_query = $this->db->query("SELECT * FROM `hospital_packages`");
            $hospital_count = $hospital_query->num_rows();
            if ($hospital_count > 0) {
                foreach ($hospital_query->result_array() as $hospital_row) {
                      $id = $hospital_row['id'];
                     $dataa[]=$this->elasticsearch->add("hospital_package","hospital_package", $id, $hospital_row);
                }
                print_r($dataa);
              
            } 
     }
	
	 public function insert_pharmacy_order_list()
     {
	   //$this->elasticsearch->delete_index("hospital");
          $pharmacy_query = $this->db->query("select * from user_order where (listing_type='13' or listing_type='44') group by invoice_no order by order_id desc");
            $pharmacy_count = $pharmacy_query->num_rows();
            if ($pharmacy_count > 0) {
                foreach ($pharmacy_query->result_array() as $pharmacy_row) {
                      $id = $pharmacy_row['order_id'];
                     $dataa[]=$this->elasticsearch->add("pharmacy_order_list","pharmacy_order_list", $id, $pharmacy_row);
                }
                print_r($dataa);
              
            } 
     }
	
	
	
     
     public function insert_hospital_doctor()
     {
          $hospital_query = $this->db->query("SELECT id,user_id FROM hospitals  WHERE `is_active` = 1 ");
            $hospital_count = $hospital_query->num_rows();
            if ($hospital_count > 0) {
                foreach ($hospital_query->result_array() as $hospital_row) {
                      $id = $hospital_row['id'];
                     $listing_id = $hospital_row['user_id'];
                   
                $hospital_query1 = $this->db->query("SELECT id,hospital_id,doctor_name,qualifications,profile_img FROM hospital_doctor_list  WHERE `hospital_id` = $listing_id ");
                $hospital_count1 = $hospital_query1->num_rows();
                if ($hospital_count1 > 0) {
                    foreach ($hospital_query1->result_array() as $hospital_row1) {
                     //   print_r($hospital_row1);
                    
                    $id1 = $hospital_row1['id'];
                    $hospital_id= $hospital_row1['hospital_id'];
                    $doctor_name =str_replace("null", "", $hospital_row1['doctor_name']); 
                    $qualifications = str_replace("null", "", $hospital_row1['qualifications']); 
                    $profile_img = $hospital_row1['profile_img'];
                    if ($profile_img != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $profile_img;
                        } else {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                    
                    $hospital = array(
                        'hospital_id' => $hospital_id,
                        'doctor_name' => $doctor_name,
                        'image' => $image,
                        'qualifications' => $qualifications
                    );
                    //echo "<pre>";
                   // print_r($hospital);
                    $dataa[]=$this->elasticsearch->add("hospital_doctor", $id1, $hospital);
                    }

                }
              
                      
                     ///$dataa[]=$this->elasticsearch->add("hospital", $id, $hospital);
                }
                print_r($dataa);
              
            } 
     }
      public function insert_fitness()
     {
          $fitness_query = $this->db->query("SELECT id,user_id,image,center_name,address FROM fitness_center WHERE `is_active` = 1 and vendor_type='Fitness' ");
                    $fitness_count = $fitness_query->num_rows();
                    if ($fitness_count > 0) {
                        foreach ($fitness_query->result_array() as $fitness_row) {
                            $id = $fitness_row['id'];
                            $listing_id = $fitness_row['user_id'];
                            $name = str_replace("null", "", $fitness_row['center_name']);
                            $field1 = '';
                            $field2 = '';
                            $field3 = str_replace("null", "", $fitness_row['address']);
                            $fitness_image = str_replace("null", "", $fitness_row['image']);
                            if ($fitness_image != '') {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/' . $fitness_image;
                            } else {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                            }
                            $fitness = array(
                                'listing_id' => $listing_id,
                                'name' => $name,
                                'image' => $image,
                                'field1' => $field1,
                                'field2' => $field2,
                                'field3' => $field3
                            );
                            $this->elasticsearch->add("fitness", $id, $fitness);
                        }
                     
                    }
                    
     }
     
      public function insert_nursing_attendant()
     {
         $nursing_query = $this->db->query("SELECT id,user_id,name,address,image FROM nursing_attendant WHERE `is_active` = 1");
                $nursing_count = $nursing_query->num_rows();
                if ($nursing_count > 0) {
                    foreach ($nursing_query->result_array() as $nursing_row) {
                        $id = $nursing_row['id'];
                        $listing_id = $nursing_row['user_id'];
                        $name = str_replace("null", "", $nursing_row['name']);
                        $field1 = '';
                        $field2 = '';
                        $field3 = str_replace("null", "", $nursing_row['address']);
                        $nursing_image = str_replace("null", "", $nursing_row['image']);
                        if ($nursing_image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/nursing_attendant_images/' . $nursing_image;
                        } else {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        $nursing = array(
                            'listing_id' => $listing_id,
                            'name' => $name,
                            'image' => $image,
                            'field1' => $field1,
                            'field2' => $field2,
                            'field3' => $field3
                        );
                          $this->elasticsearch->add("nursingattendant", $id, $nursing);
                    }
                 
                }
                    
     }
     
     
    public function insert_people(){
         $field1 = '';
            $field2 = '';
            $field3 = '';
         $people_query = $this->db->query("SELECT id,name FROM `users` WHERE  vendor_id = '0' and `is_active` = 1 order by name asc");
                    $people_count = $people_query->num_rows();
                    if ($people_count > 0) {
                        foreach ($people_query->result_array() as $people_row) {
                            $listing_id = $people_row['id'];
                            $name = $people_row['name'];
                            $listing_type = '0';
                            $media_query = $this->db->query("SELECT media.source FROM media LEFT JOIN users on users.avatar_id=media.id WHERE users.id='$listing_id' limit 1");
                            $media_count = $media_query->num_rows();
                            if ($media_count > 0) {
                                $media_row = $media_query->row_array();
                                $img_file = $media_row['source'];
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                            } else {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                            }
                            $people = array(
                                'listing_id' => $listing_id,
                                'name' => $name,
                                'image' => $image,
                                'field1' => $field1,
                                'field2' => $field2,
                                'field3' => $field3
                            );
                             $this->elasticsearch->add("people", $listing_id, $people);
                        }
                        
                    }
        
    }
    
    public function insert_hmproduct(){
        
         $hm_query = $this->db->query(" SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,d.pd_quantity,d.pd_short_desc,d.pd_long_desc,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_status = '1'");
                    $hm_count = $hm_query->num_rows();
                    if ($hm_count > 0) {
                        foreach ($hm_query->result_array() as $hm_row) {
                            $pd_id = $hm_row['pd_id'];
                            $v_id = $hm_row['v_id'];
                           $v_name= $hm_row['v_name'];
                           $v_delivery_charge= $hm_row['v_delivery_charge'];
                           $pd_name= $hm_row['pd_name'];
                           $pd_pc_id= $hm_row['pd_pc_id'];
                           $pd_psc_id= $hm_row['pd_psc_id'];
                           $pd_photo_1= $hm_row['pd_photo_1'];
                           $pd_photo_2= $hm_row['pd_photo_2'];
                           $pd_photo_3= $hm_row['pd_photo_3'];
                           $pd_photo_4= $hm_row['pd_photo_4'];
                           $pd_mrp_price= $hm_row['pd_mrp_price'];
                           $pd_vendor_price= $hm_row['pd_vendor_price'];
                           $pd_quantity= $hm_row['pd_quantity'];
                           $pd_short_desc= $hm_row['pd_short_desc'];
                           $pd_long_desc= $hm_row['pd_long_desc'];
                           $total_view= $hm_row['total_view'];
                            $hm_product = array( 
                                'pd_id' => $pd_id,
                                'v_id' => $v_id,
                                'v_name' => $v_name,
                                'v_delivery_charge' => $v_delivery_charge,
                                'pd_name' => $pd_name,
                                'pd_pc_id' => $pd_pc_id,
                                'pd_psc_id' => $pd_psc_id,
                                'pd_photo_1' => $pd_photo_1,
                                'pd_photo_2' => $pd_photo_2,
                                'pd_photo_3' => $pd_photo_3,
                                'pd_photo_4' => $pd_photo_4,
                                'pd_mrp_price' => $pd_mrp_price,
                                'pd_vendor_price' => $pd_vendor_price,
                                'pd_quantity' => $pd_quantity,
                                'pd_short_desc' => $pd_short_desc,
                                'pd_long_desc' => $pd_long_desc,
                                'total_view' => $total_view
                            );
                           // print_r($hm_product);
                           $data[] =$this->elasticsearch->add("product", $pd_id, $hm_product);
                      
                    }
                  
                }
              print_r($data); 
        
    }
	
	
	 public function insert_healthmproduct(){
        
		 
		 $this->elasticsearch->delete_index("hm_product");
         $hm_query = $this->db->query(" SELECT * FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_status = '1'");
                    $hm_count = $hm_query->num_rows();
                    if ($hm_count > 0) {
                        foreach ($hm_query->result_array() as $hm_row) {
                            $pd_id = $hm_row['pd_id'];
                          
                           $data[] =$this->elasticsearch->add("hm_product","product", $pd_id, $hm_row);
                      
                    }
                  
                }
              print_r($data); 
        
    }
	
	
    
    
    public function insert_substitute(){
        
         $sub_query = $this->db->query(" SELECT * from substitute");
                    $sub_count = $sub_query->num_rows();
                    if ($sub_count > 0) {
                        foreach ($sub_query->result_array() as $sub_row) {
                            $id = $sub_row['id'];
                            $group_id = $sub_row['group_id'];
                           $medicine_name= $sub_row['medicine_name'];
                           $price= $sub_row['price'];
                           $image= $sub_row['image'];
                          
                            $sub_product = array( 
                                'id' => $id,
                                'group_id' => $group_id,
                                'medicine_name' => $medicine_name,
                                'price' => $price,
                                'image' => $image
                                
                            );
                           // print_r($sub_product);
                           $data[] =$this->elasticsearch->add("substitute", $id, $sub_product);
                      
                    }
                  
                }
              print_r($data); 
        
    }
  
    
    public function insert_productsub(){
        
         $sub_query = $this->db->query(" SELECT * from medi_product");
                    $sub_count = $sub_query->num_rows();
                    if ($sub_count > 0) {
                        foreach ($sub_query->result_array() as $sub_row) {
                            $id = $sub_row['id'];
                             $category= $sub_row['category'];
                             $sub_category= $sub_row['sub_category'];
                            $is_prescription_needed= $sub_row['is_prescription_needed'];
                            $group_id = $sub_row['group_id'];
                            $medicine_name= $sub_row['medicine_name'];
                            $price= $sub_row['price'];
                            $image= $sub_row['image'];
                            $pack= $sub_row['pack'];
                            $company= $sub_row['company'];
                          
                            $sub_product = array( 
                                'id' => $id,
                                'category' => $category,
                                'sub_category' => $sub_category,
                                'is_prescription_needed' => $is_prescription_needed,
                                'group_id' => $group_id,
                                'medicine_name' => $medicine_name,
                                'price' => $price,
                                'image' => $image,
                                'pack' => $pack,
                                'company' => $company
   
                            );
                           // print_r($sub_product);
                           $data[] =$this->elasticsearch->add("medi_product", $id, $sub_product);
                      
                    }
                  
                }
              print_r($data); 
        
    }
	
	   public function insert_bookingdoctor(){
        
	
			
			 $query2 = $this->db->query("select doctor_booking_master.booking_id, doctor_booking_master.booking_date,doctor_booking_master.listing_id,doctor_booking_master.user_id, doctor_booking_master.listing_id,doctor_booking_master.clinic_id, doctor_prescription.id as prescription_id, doctor_booking_master.booking_time,doctor_booking_master.status,doctor_booking_master.consultation_type, doctor_clinic.clinic_name,users.name as patient_name, doctor_clinic.address,doctor_clinic.image,doctor_clinic.state,doctor_clinic.city,doctor_clinic.pincode, doctor_clinic.consultation_charges,doctor_clinic.contact_no,dl.doctor_name from doctor_booking_master LEFT JOIN doctor_prescription ON (doctor_booking_master.booking_id = doctor_prescription.booking_id AND doctor_booking_master.listing_id = doctor_prescription.doctor_id AND doctor_booking_master.clinic_id = doctor_prescription.clinic_id)  LEFT JOIN doctor_clinic on (doctor_booking_master.clinic_id = doctor_clinic.id) LEFT JOIN users ON (doctor_booking_master.user_id = users.id) LEFT JOIN doctor_list as dl ON(dl.user_id=doctor_booking_master.listing_id)  ORDER BY doctor_booking_master.id DESC");

            $count2 = $query2->num_rows();
            if ($count2 > 0) {
                foreach ($query2->result_array() as $row2) {
			 $user_id = $row2['user_id'];
                   $booking_id = $row2['booking_id'];
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
                     
                     if ($image != '') {
                            $clinic_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $image;
                        } else {
                            $clinic_image = '';
                        }
                        
                    $resultpost = array(
			      'user_id' => $user_id,
                        'booking_id' => $booking_id,
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
			 $data[] =$this->elasticsearch->add("doctor_booking_master", $booking_id, $resultpost);
                }
            }
            	echo "<pre>";
		     print_r($data); 
            
}
	
	  public function insert_medicalstore(){
        		$query = $this->db->query("SELECT user_order.*,medical_stores.medical_name,medical_stores.profile_pic FROM user_order LEFT JOIN medical_stores on user_order.listing_id=medical_stores.id WHERE (user_order.listing_type='13' or user_order.listing_type='38' )  group by user_order.invoice_no order by user_order.order_date DESC   ");
                        $count = $query->num_rows();
                        if ($count > 0) 
                           { 
            foreach ($query->result_array() as $row) {
                $user_id=$row['user_id'];
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
            $tracker = $this->pharmacy_tracker($user_id, $invoice_no);
            
             if($listing_type=="38")
               {
                   if(!empty($rxId) )
                {
                $resultpost = array(
		    "user_id"=>$user_id,	
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
                    "life_date"=>"",
                    "image"=>$profile_pic
                );
			    $data[] =$this->elasticsearch->add("medical_store", $order_id, $resultpost);
                }
                
               }
               else
               {
                   $resultpost = array(
		"user_id"=>$user_id,
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
                    "life_date"=>"",
                    "image"=>$profile_pic
                );
		        $data[] =$this->elasticsearch->add("medical_store", $order_id, $resultpost);
               }
            }
       } 
                        else 
                        {
                            $resultpost4 = array();
                        }
                        
                        $query = $this->db->query("SELECT * from life_saving_drugs");
                        $count = $query->num_rows();
                           if ($count > 0)
                              { 
                                foreach ($query->result_array() as $row) 
                                        {
					    $user_id = $row['user_id'];
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
                                            $resultpost = array(
						"user_id"=>$user_id,
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
                                                "image" => "https://d2c8oti4is0ms3.cloudfront.net/images/Check_In_Image/".$image,
                                                "life_date"=>$date
                                            );
					  $data[] =$this->elasticsearch->add("medical_store", $invoice_no, $resultpost);
                                        }
                              }
         
			  print_r($data); 
                    }
            

	public function insert_fitness_booking(){
	
		
			     
                
		  $querys1 = $this->db->query("SELECT fitness_center_branch.branch_name,fitness_center_branch.user_id as branch_fit_id, fitness_center_branch.branch_image, fitness_center_branch.branch_phone, fitness_center_branch.branch_address, fitness_center_branch.pincode, fitness_center_branch.state, fitness_center_branch.city,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time,booking_master.trainer_package_id,booking_master.trainer_id, booking_master.joining_date FROM booking_master
                                                 INNER JOIN fitness_center_branch ON booking_master.branch_id=fitness_center_branch.id
                                                 WHERE booking_master.vendor_id='6' ORDER BY booking_master.booking_date DESC ");
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
                    $user_id = $row1['user_id'];
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
                    
                    
                     $order_date= $trail_booking_date . " ". $trail_booking_time;
                     $order_date1 = date('l j M Y h:i A', strtotime($order_date));

                    $resultpost = array(
			    'user_id'=>$user_id,
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
			    $data[] =$this->elasticsearch->add("fitness_booking", $booking_id, $resultpost);
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
                   


                    $resultpost = array(
			 'user_id'=>$user_id,
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
                         "order_date" => "",
                  
                        'listing_type'=>$row1['vendor_id']
                    );
			    $data[] =$this->elasticsearch->add("fitness_booking", $booking_id, $resultpost);
                    }
                }}
		print_r($data);
		
	
	}	
	
	
	public function nursing_booking(){
	
		  		
               $query3 = $this->db->query("SELECT booking_master.*,health_record.patient_name,nursing_attendant.address,nursing_attendant.city,nursing_attendant.state,nursing_attendant.contact,nursing_attendant.pincode,nursing_booking_details.* FROM booking_master  LEFT JOIN users ON (booking_master.user_id = users.id) LEFT JOIN health_record ON(booking_master.patient_id=health_record.id) LEFT JOIN nursing_attendant ON(booking_master.listing_id=nursing_attendant.user_id) LEFT JOIN nursing_booking_details ON(booking_master.booking_id=nursing_booking_details.booking_id) where booking_master.vendor_id = '12' ORDER BY booking_master.booking_date DESC ");
                       $count3 = $query3->num_rows();
                      if ($count3 > 0) {
                foreach ($query3->result_array() as $row3) {
                    $resultbids = array();
		$user_id = $row3['user_id'];
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
                    $resultpost = array(
			    'user_id' => $user_id,
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
                    
                   $data[] =$this->elasticsearch->add("nursing_booking", $booking_id, $resultpost);
                    
                 
                }
                        }
		print_r($data);
	
	}
	
	
	public function insert_lab_booking(){
	
		 $count_query4 = $this->db->query("SELECT lb.*,bm.vendor_id,bm.status from booking_master as bm LEFT JOIN lab_booking_details as lb ON(bm.booking_id = lb.booking_id) where  (bm.vendor_id='10' or bm.vendor_id='31') order by bm.id desc");
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
                                
                                $resultpost = array(
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
                                    'image'=>'',
                                    'order_date'=>$order_date,
                                    'status'=>$status,
                                     "listing_type"=> "10"
                                );
					   $data[] =$this->elasticsearch->add("lab_booking", $user_id, $resultpost);
                     }
                            }       
                        }
					 
                    
                       
	
	
	}
	
	
	public function insert_hm_booking(){
	
		   $pro_list = array();
                        $results = $this->db->query("SELECT vd.delivery_by_medicalwale, uo.* FROM `user_order` as uo left join vendor_details_hm as vd on (uo.`listing_id` = vd.v_id) WHERE  uo.`listing_type`='34'   ORDER BY order_date DESC ");
                	  
                   	    foreach($results->result_array() as $order){
                	        $products = $this->db->query("SELECT * FROM `orders_details_hm` WHERE `order_id` = ".$order['order_id']."");
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
                	        $order += ['order_date' => ""];
                	            foreach($order as $key => $value){
                        	        if($value == null){
                        	            $order[$key] = "";
                        	        }
                        	    }
                        	    
                	        $resultpost = $order;
				    
				     $data[] =$this->elasticsearch->add("hm_booking",$order['order_id'], $resultpost);
                	    }
	
	
	
	} 
	
	
	public function insert_hospital_booking() {
	  
			
		
		 $booking_details = $this->db->query("SELECT * FROM booking_master WHERE vendor_id='8'  order by id DESC");
       
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
             $order_date1 = $booking_date . " ".$booking_time;
             $order_date = date('l j M Y h:i A', strtotime($order_date1));
            $resultpost61 = array(
                    'id' => $id,
		    'user_id' => $user_id,
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
		      $data[] =$this->elasticsearch->add("hospital_booking", $id, $resultpost61);
            
           }
        }
        
       // OPD
        
        $query= $this->db->query("select hospital_booking_master.booking_id,hospital_booking_master.booking_date,hospital_booking_master.listing_id,hospital_booking_master.user_id,hospital_booking_master.listing_id,
hospital_booking_master.doctor_id,hospital_doctor_prescription.id as prescription_id,hospital_booking_master.booking_time,hospital_booking_master.status,hospital_booking_master.consultation_type,
hospitals.name_of_hospital,users.name as patient_name, hospitals.address,hospitals.image,hospitals.state,hospitals.city,hospitals.pincode, dl.consultation,hospitals.phone,dl.doctor_name from hospital_booking_master  LEFT JOIN hospital_doctor_prescription 
ON (hospital_booking_master.patient_id = hospital_doctor_prescription.patient_id AND hospital_booking_master.doctor_id = hospital_doctor_prescription.doctor_id AND hospital_booking_master.listing_id = hospital_doctor_prescription.hospital_id) LEFT JOIN hospitals on (hospital_booking_master.listing_id = hospitals.user_id)
LEFT JOIN users ON (hospital_booking_master.user_id = users.id)  LEFT JOIN hospital_doctor_list as dl ON(dl.id=hospital_booking_master.doctor_id)
  ORDER BY hospital_booking_master.id DESC");

$count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
			$user_id = $row['user_id'];
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
                    $booking_time1 = explode('-',$booking_time);
                    $new =$booking_time1[0];
                    $order_date1= $booking_date ." ".$new;

             $order_date = date('l j M Y h:i A', strtotime($order_date1));
                    $resultpost61 = array(
			    'user_id' => $user_id,
                        'booking_id' => $booking_id,
                        'doctor_id' => $doctor_id,
                        'order_date' => $order_date,
                        //'appointment_time' => $booking_time,
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
                         'image'=>'',
                        'booking_type'=>"OPD",
                         "listing_type"=> "8"
                    );
			   $data[] =$this->elasticsearch->add("hospital_booking", $id, $resultpost61);
                }
            }
        
}
	
	
	
    
      public function insert_hospitalproduct(){
        
         $sub_query = $this->db->query(" SELECT * from product");
                    $sub_count = $sub_query->num_rows();
                    if ($sub_count > 0) {
                        foreach ($sub_query->result_array() as $sub_row) {
                            $id = $sub_row['id'];
                            
                            $medicine_name= $sub_row['product_name'];
                            
                          
                            $sub_product = array( 
                                'id' => $id,
                                'product_name' => $medicine_name
                            );
                           // print_r($sub_product);
                           $data[] =$this->elasticsearch->add("h_product", $id, $sub_product);
                      
                    }
                  
                }
              print_r($data); 
        
    }
	
	public function life_drug(){
	 $query = $this->db->query("SELECT * from life_saving_drugs  order by id desc");
  
                $count = $query->num_rows();
                 if ($count > 0)
                 { 
            foreach ($query->result_array() as $row) 
            {
                
               
                $member_id = $row['member_id'];
                $user_id = $row['user_id'];
           
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
                $resultpost8 = array(
				
				 "user_id" => $user_id,
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
                    "life_date"=>$date
                  
                );
		     $data[] =$this->elasticsearch->add("life_drug", $user_id, $resultpost8);
               
               }
             
            }
	}
    
    
    
     public function create_elastic()
    {
    $id = 1337;
	$data = array("name"=>"nisse", "age"=>"14", "sex"=>"male"); 
	$return = $this->elasticsearch->add("general",$id,$data);
	var_dump($return);
    }
    
     public function create()
    {
	$id = 1337;
    $return = 	$this->elasticsearch->create("comments");
    var_dump($return);
    }
    
     public function query_all()
    {

	$return = $this->elasticsearch->query_all('Brown');
	 var_dump($return);
    }
    
     public function search()
    {

$return =	$this->elasticsearch->search(); 
	echo "<pre>";
	 var_dump($return);
    }
    
     public function suggest()
    {
        $keywords="andheri";
        $index_id="doctor";
     $returnresult = $this->elasticsearch->query_all($index_id,$keywords);
    	    if($returnresult['hits']['total'] < 0){
    	    }else{ 
                	   foreach($returnresult['hits']['hits'] as $hi){
                	      echo "<pre>";
                	      $sim[] = similar_text($hi['_source']['name'], $keywords, $perc[]);

                	   }
                	}

        $dataperc=max($perc);
        if($dataperc >= 70){
             $returnresult = $this->elasticsearch->query_all($index_id,$keywords);
               echo "<pre>";
         print_r($returnresult);
        }else{
     
	$data = array("suggest"=>array("my-suggestion"=>array("text"=>$keywords, "term"=>array("field"=>"name")))); 
        $data1=json_encode($data);
        $return =	$this->elasticsearch->suggest($index_id,$data1);

        	$name=array();
        	foreach($return['suggest']['my-suggestion'] as $a){
                	if(empty($a['options'])){
                	  $name[]=  $a['text'];
                	}else{
                	      $name[]=$a['options'][0]['text'];
                	}
                	
            	}
            $string_version = implode(' ', $name);
            $return = $this->elasticsearch->query_all($index_id,$string_version);
         echo "<pre>";
         print_r($return);
    
             }
        }
    
       public function delete()
    {
  $id = 1337;
$return =	$this->elasticsearch->delete('my_index',$id);
	var_dump($return);
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
            $t['status'] = $statuses['status'] ." by ". $action_by ; 
            $data[] = $t;
        }
        
       
        return $data;
        
        
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

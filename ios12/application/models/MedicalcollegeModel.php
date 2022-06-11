<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MedicalcollegeModel extends CI_Model {

    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";

    public function check_auth_client() {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return json_output(401, array('status' => 401, 'message' => 'Unauthorized.'));
        }
    }

    public function auth() {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorizations', TRUE);
        $q = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
        if ($q == "") {
            return json_output(401, array('status' => 401, 'message' => 'Unauthorized.'));
        } else {
            if ($q->expired_at < date('Y-m-d H:i:s')) {
                return json_output(401, array('status' => 401, 'message' => 'Your session has been expired.'));
            } else {
                $updated_at = date('Y-m-d H:i:s');
               $expired_at = '2030-11-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array('expired_at' => $expired_at, 'updated_at' => $updated_at));
                return array('status' => 200, 'message' => 'Authorized.');
            }
        }
    }

    public function encrypt($str) {
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv = hash('MD5', 'mdwale8655328655', true);
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $pad = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        $encrypted = mcrypt_generic($module, $str);
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        return base64_encode($encrypted);
    }

    public function decrypt($str) {
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv = hash('MD5', 'mdwale8655328655', true);
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $str = mdecrypt_generic($module, base64_decode($str));
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        $slast = ord(substr($str, -1));
        $str = substr($str, 0, strlen($str) - $slast);
        return $str;
    }

    public function college_cat_list() {
        return $this->db->select('id,cat_name')->from('medical_college_cat')->order_by('id', 'asc')->get()->result();
    }

    public function college_list($category_id,$latitude,$longitude) {
        //added for check by latitude and longitude 
           $radius = '50';

        function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyDg9YiklFpUhqkDXlwzuC9Cnip4U-78N60&callback=initMap";
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

        
        
        
        //end 
           //   $query = $this->db->query("SELECT id,user_id,college_name,cat_id,phone,address,banner,image,banner_source,  ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance from medical_college_details where FIND_IN_SET('" . $category_id . "', cat_id) HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
               $sql = sprintf("SELECT id,user_id,college_name,cat_id,phone,address,banner,lat,lng,image,banner_source,aided,brochure,  ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance from medical_college_details where FIND_IN_SET('" . $category_id . "', cat_id) HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
              $query = $this->db->query($sql);
      //  $count = $query->num_rows();
            // echo "SELECT id,user_id,college_name,cat_id,phone,address,banner,image,banner_source,  ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance from medical_college_details where FIND_IN_SET('" . $category_id . "', cat_id) HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius);
//die();
      //  $query = $this->db->query("SELECT id,user_id,college_name,cat_id,phone,address,banner,image,banner_source, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( in( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance from medical_college_details where cat_id like '%$category_id%' HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
       // $sql = sprintf("SELECT `id`, `user_id`, `branch_name`, `branch_image`, `branch_phone`, `branch_email`, `about_branch`, `branch_business_category`, `branch_offer`, `branch_facilities`, `branch_address`, `opening_hours`, `pincode`, `state`, `city`, `lat`, `lng`, `is_free_trail`, `from_trail_time`,`user_discount`, `to_trail_time`, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM fitness_center_branch  WHERE  FIND_IN_SET('" . $category . "', branch_business_category)  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
        $count = $query->num_rows();
        if ($count > 0) {

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $cat_id = $row['cat_id'];
                 $lat = $row['lat'];
                $lng = $row['lng'];
                $listing_id = $row['user_id'];
                $college_name = $row['college_name'];
                $phone = $row['phone'];
                $address = $row['address'];
                // $banner = $row['banner'];
                // $banner_source = $row['banner_source'];
                $banner = $row['image'];
                $banner_source = $row['image'];
                $aided = $row['aided'];
                $banner = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $banner;
                $ban_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $banner_source;
                 $distances = str_replace(',', '.', GetDrivingDistance($latitude, $lat, $longitude, $lng));
                 $brochure = $row['brochure'];
                 $brochure_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/medical_college_images/file/' . $brochure;
                 //added for college copare details provide fee,cource type 
                 //start
         $college_courses = array();
        $query = $this->db->query("SELECT id,college_id,course_name,type,duration,description,fees from medical_college_courses where user_id='$listing_id'");
        $courses_count = $query->num_rows();
        if($courses_count>0)
        {
        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $college_id = $row['college_id'];
            $course_name = $row['course_name'];
            $type = $row['type'];
            $duration = $row['duration'];
             $description = $row['description'];
            $fees = $row['fees'];

            $college_courses[] = array(
                "id" => $id,
                "college_id" => $college_id,
                "course_name" => $course_name,
                "type" => $type,
                "duration" => $duration,
                 "description" => $description,
                 "fees" => $fees
            );
        }
        }
                 //end 
                 
                 
                $resultpost[] = array(
                    "id" => $id,
                    "listing_id" => $listing_id,
                    "listing_type" => '11',
                    "cat_id" => $cat_id,
                    "college_name" => $college_name,
                    "phone" => $phone,
                    "distance" => $distances,
                    "address" => $address,
                    "banner" => $banner,
                    "ban_source" => $ban_source,
                    "accrediation" => $aided,
                    "courses" => $college_courses,
                    "brochure_source" => $brochure_source
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    
    ///added by zak for college search accourding to keyword 
    //start
    public function college_list_filter($category_id,$latitude,$longitude,$search_type,$keyword) {
        //added for check by latitude and longitude 
           $radius = '50';

        function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyDg9YiklFpUhqkDXlwzuC9Cnip4U-78N60&callback=initMap";
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

        
        //end 
           //   $query = $this->db->query("SELECT id,user_id,college_name,cat_id,phone,address,banner,image,banner_source,  ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance from medical_college_details where FIND_IN_SET('" . $category_id . "', cat_id) HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
             if($search_type == 'location')
             {
               $sql = sprintf("SELECT id,user_id,college_name,cat_id,phone,address,banner,lat,lng,image,banner_source,  ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance from medical_college_details where FIND_IN_SET('" . $category_id . "', cat_id) HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
              $query = $this->db->query($sql);
              $count = $query->num_rows();
             }
            else if ($search_type == 'name')
            {
               $sql = query("SELECT id,user_id,college_name,cat_id,phone,address,banner,lat,lng,image,banner_source from medical_college_details where FIND_IN_SET('" . $category_id . "', cat_id) and collegetype LIKE '%$keyword%'"); 
              $query = $this->db->query($sql);
              $count = $query->num_rows();
                
            }
            else if ($search_type == 'streams')
            {
                 $sql = query("SELECT id,user_id,college_name,cat_id,phone,address,banner,lat,lng,image,banner_source from medical_college_details where FIND_IN_SET('" . $category_id . "', cat_id) and streams LIKE '%$keyword%'"); 
              $query = $this->db->query($sql);
              $count = $query->num_rows();
            }
        if ($count > 0) {

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $cat_id = $row['cat_id'];
                 $lat = $row['lat'];
                $lng = $row['lng'];
                $listing_id = $row['user_id'];
                $college_name = $row['college_name'];
                $phone = $row['phone'];
                $address = $row['address'];
                // $banner = $row['banner'];
                // $banner_source = $row['banner_source'];
                $banner = $row['image'];
                $banner_source = $row['image'];
                $banner = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $banner;
                $ban_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $banner_source;
                 $distances = str_replace(',', '.', GetDrivingDistance($latitude, $lat, $longitude, $lng));
                $resultpost[] = array(
                    "id" => $id,
                    "listing_id" => $listing_id,
                    "listing_type" => '11',
                    "cat_id" => $cat_id,
                    "college_name" => $college_name,
                    "phone" => $phone,
                    "distance" => $distances,
                    "address" => $address,
                    "banner" => $banner,
                    "ban_source" => $ban_source
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
   //end
    public function college_details($college_id, $user_id) {
        $college_faculty = array();
        $ammenties =array();
     
        $query = $this->db->query("SELECT id,college_id,teacher_name,designation,qualification,image,experience from medical_college_faculty where user_id='$college_id'");
        foreach ($query->result_array() as $row) {
            
            
            $id = $row['id'];
            $collegee_id = $row['college_id'];
            $teacher_name = $row['teacher_name'];
            $designation = $row['designation'];
            $qualification = $row['qualification'];
            $experience = $row['experience'];
            $faculty_image = "https://s3.amazonaws.com/medicalwale/images/medical_college_images/image/".$row['image'];
            
            $college_faculty[] = array(
                "id" => $id,
                "college_id"    => $collegee_id,
                "teacher_name"  => $teacher_name,
                "designation"   => $designation,
                "faculty_experience"   => $experience,
                "faculty_image"   => $faculty_image,
                "qualification" => $qualification
            );
        }
        
        $college_courses = array();
        $query = $this->db->query("SELECT id,college_id,course_name,type,duration,description,fees from medical_college_courses where user_id='$college_id'");
        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $collegee_id = $row['college_id'];
            $course_name = $row['course_name'];
            $type = $row['type'];
            $duration = $row['duration'];
             $description = $row['description'];
            $fees = $row['fees'];

            $college_courses[] = array(
                "id" => $id,
                "college_id" => $collegee_id,
                "course_name" => $course_name,
                "type" => $type,
                "duration" => $duration,
                 "description" => $description,
                 "fees" => $fees
            );
        }
        $college_details = array();
        $query = $this->db->query("SELECT * FROM `medical_college_details` WHERE `user_id`='$college_id'");
        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $cat_id = $row['cat_id'];
            
            $college_name = $row['college_name'];
            $estabishment = $type = $row['establishment_year'];
            $college_type = $row['collegetype'];
            $phone = $row['phone'];
            $aidedby = $row['aided'];
            $email = $row['email'];
            $website = $row['website'];
            $about = $row['about'];
            $affiliation = $row['affiliation'];
            $approved_by = $row['approved_by'];
            $address = $row['address'];
            $lat = $row['lat'];
            $lng = $row['lng'];
          
           
          //  $banner = $row['banner'];
           $clg_banner = $row['image'];
           $banner = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $clg_banner;
            $banner_source = $row['banner_source'];
           //     $ban_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/medical_college_images/' . $banner_source;
          $ban_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $clg_banner;
             $pincode = $row['pincode'];
            $brochure = $row['brochure'];
            $brochure_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/medical_college_images/file/' . $brochure;
             $area = $row['area'];
            $awarded = $row['awarded'];
            
            $details = $about;
            
          
                //start for ememeinities
         //$sql=$this->db->query("SELECT medical_college_amenities.*,college_gallery.id,college_gallery.user_id,college_gallery.name,college_gallery.amenity_id as college_gallery_amenities,college_gallery.type FROM  medical_college_amenities LEFT join college_gallery on (medical_college_amenities.amenity_id=college_gallery.amenity_id) WHERE medical_college_amenities.medical_college_id='".$user_id."'");  
           
           $sql=$this->db->query("SELECT * FROM  medical_college_amenities WHERE medical_college_id='".$user_id."'");  
        //   echo "SELECT * FROM  medical_college_amenities WHERE medical_college_id='".$user_id."'";
           $clgdetails = $sql->result();
           
           $amnt_count = $sql->num_rows();
           if($amnt_count>0)
           {
           for($x=0;$x<count($clgdetails);$x++) 
           {
               $amenity_id = $clgdetails[$x]->amenity_id;
               $amenity_name= $clgdetails[$x]->amenity_name;
               $amenity_description = $clgdetails[$x]->description;
                
                
                $sql=$this->db->query("SELECT *  FROM  college_gallery WHERE amenity_id='".$clgdetails[$x]->amenity_id."'");  
                $Imgclgdetails = $sql->result();
                $amenity_image=array();
              $img_count = $sql->num_rows();
            if($img_count > 0)
            {
           foreach($Imgclgdetails as $ImgColg){

        
           $path ="";
           if($ImgColg->type == 'video')
           {

            $path = "https://s3.amazonaws.com/medicalwale/images/medical_college_images/video/".$ImgColg->name;
            

            }
            if($ImgColg->type == 'image')
            {

            $path = "https://s3.amazonaws.com/medicalwale/images/medical_college_images/image/".$ImgColg->name;
            

            }
          
                $amenity_image[] = $path;
                $ammentie = array(
                "amenity_id" => $amenity_id,
                "amenity_name" => $amenity_name,
                "amenity_description" => $amenity_description,
                "amenity_image" => $amenity_image
                );
               
           }
            }
            else
            {
                 $amenity_image[] = "https://s3.amazonaws.com/medicalwale/images/medical_college_images/image/";
                $ammentie = array(
                "amenity_id" => $amenity_id,
                "amenity_name" => $amenity_name,
                "amenity_description" => $amenity_description,
                "amenity_image" => $amenity_image
                );
            }
            //   $amenity_name= $q['amenity_name'];
            //     $amenity_description = $q['description'];
            //     $amenity_image = $path;
            //     $ammentie = array(
            //     "amenity_name" => $amenity_name,
            //     "amenity_description" => $amenity_description,
            //     "amenity_image" => $amenity_image
            //     );
            $ammenties[] = $ammentie;
            
           
          }
           }
            //end eminities
            
            $college_details[] = array(
                "id" => $id,
                "cat_id" => $cat_id,
                "aidedby" => $aidedby,
                "college_type" => $college_type,
                "college_name" => $college_name,
                "estabishment" => $estabishment,
                "phone" => $phone,
                "exotel_number" => '02233721563',
                "email" => $email,
                "website" => $website,
                "about" => $details,
                "area" => $area,
                "awarded" => $awarded,
                "affiliation" => $affiliation,
                "approved_by" => $approved_by,
                "address" => $address.", ".$pincode, 
                "lat" => $lat,
                "lng" => $lng,
                "ammenties" => $ammenties,
                "banner" => $banner,
                "ban_source" => $ban_source,
                "brochure_source" => $brochure_source,
            );
        }
        
      

       
        //   foreach($sql->result_array() as $q){
        //   //      foreach($clgdetails as $q){
               
               
               
               
               
        //       $amenity_name= $q['amenity_name'];
        //         $amenity_description = $q['description'];
        //         $amenity_image = $q['media'];
        //         $ammentie = array(
        //         "amenity_name" => $amenity_name,
        //         "amenity_description" => $amenity_description,
        //         "amenity_image" => $amenity_image
        //         );
        //     $ammenties[] = $ammentie;
        //   }
           //end for eminities
        $images = array();
        //$query = $this->db->query("SELECT id,college_id,media,source FROM `medical_college_media` WHERE `college_id`='$college_id'");
        $query = $this->db->query("SELECT * FROM `college_gallery` WHERE `user_id`='$college_id'");
        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $user_id = $row['user_id'];
            $media = $row['name'];
            $source = $row['name'];
            $image_source = 'https://s3.amazonaws.com/medicalwale/images/medical_college_images/image/' . $source;

            $images[] = array(
                "id" => $id,
                "user_id" => $user_id,
                "media" => $media,
                "source" => $image_source
            );
        }
        $branches=array();
        $query = $this->db->query("SELECT user_id FROM `medical_college_details` WHERE `id`='$college_id'");
        foreach ($query->result_array() as $rows) {
            $user_id  = $rows['user_id'];
        $branch_query = $this->db->query("SELECT * FROM `medical_center_branch` LEFT JOIN medical_college_details ON medical_college_details.user_id=medical_center_branch.user_id WHERE medical_center_branch.user_id='$user_id'");
        foreach ($branch_query->result_array() as $col_row) {
            $branch_id = $col_row['id'];
            $college_user_id = $col_row['user_id'];
            $branch_name = $col_row['branch_name'];
            $branch_image = $col_row['branch_image'];
            $image_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/medical_college_images/' . $branch_image;
            $branch_phone = $col_row['branch_phone'];
            $branch_email = $col_row['branch_email'];
            $about_branch = $col_row['about_branch'];
            $branch_address = $col_row['branch_address'];
           
               
            $branches[] = array(
                "id" => $branch_id, 
                "college_user_id" => $college_user_id,
                "branch_name" => $branch_name,
                "branch_image" => $image_source,
                "branch_phone" => $branch_phone,
                "branch_email" => $branch_email,
                "about_branch" => $about_branch,
                "branch_address" => $branch_address
            );
        }
        }

        $result[] = array(
            'college_details' => $college_details,
            'course' => $college_courses,
            'faculty' => $college_faculty,
            'images' => $images,
            'branches' => $branches
        );

        return $result;
    }

    public function review_list($user_id, $listing_id) {

        function get_time_difference_php($created_time) {
            date_default_timezone_set('Asia/Calcutta');
            $str = strtotime($created_time);
            $today = strtotime(date('Y-m-d H:i:s'));
            $time_differnce = $today - $str;
            $years = 60 * 60 * 24 * 365;
            $months = 60 * 60 * 24 * 30;
            $days = 60 * 60 * 24;
            $hours = 60 * 60;
            $minutes = 60;
            if (intval($time_differnce / $years) > 1) {
                return intval($time_differnce / $years) . " yrs ago";
            } else if (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . " yr ago";
            } else if (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . " months ago";
            } else if (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . " month ago";
            } else if (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . " days ago";
            } else if (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . " day ago";
            } else if (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . " hrs ago";
            } else if (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . " hr ago";
            } else if (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . " sec ago";
            } else {
                return "few seconds ago";
            }
        }

        $resultpost = '';
        $review_count = $this->db->select('id')->from('medical_college_review')->where('college_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT medical_college_review.id,medical_college_review.user_id,medical_college_review.college_id,medical_college_review.rating,medical_college_review.review, medical_college_review.service,medical_college_review.date as review_date,users.id as user_id,users.name as firstname FROM `medical_college_review` INNER JOIN `users` ON medical_college_review.user_id=users.id WHERE medical_college_review.college_id='$listing_id' order by medical_college_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '8') {
                    $decrypt = $this->decrypt($review);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $review) {
                        $review = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($review)) === $review) {
                        $review = base64_decode($review);
                    }
                }
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                //added by zak for user image review 
               // start
                        $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
               //end
                $like_count = $this->db->select('id')->from('medical_college_review_like')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('medical_college_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('medical_college_review_like')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();




                $resultpost[] = array(
                    'id' => $id,
                    'username' => $username,
                    'rating' => $rating,
                    'review' => $review,
                    'service' => $service,
                    'review_date' => $review_date,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'comment_count' => $post_count,
                    'userimage' => $userimage
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }





  public function review_with_comment($user_id, $listing_id) {

        function get_time_difference_php($created_time) {
            date_default_timezone_set('Asia/Calcutta');
            $str = strtotime($created_time);
            $today = strtotime(date('Y-m-d H:i:s'));
            $time_differnce = $today - $str;
            $years = 60 * 60 * 24 * 365;
            $months = 60 * 60 * 24 * 30;
            $days = 60 * 60 * 24;
            $hours = 60 * 60;
            $minutes = 60;
            if (intval($time_differnce / $years) > 1) {
                return intval($time_differnce / $years) . " yrs ago";
            } else if (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . " yr ago";
            } else if (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . " months ago";
            } else if (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . " month ago";
            } else if (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . " days ago";
            } else if (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . " day ago";
            } else if (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . " hrs ago";
            } else if (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . " hr ago";
            } else if (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . " sec ago";
            } else {
                return "few seconds ago";
            }
        }

        $resultpost = '';
        $review_count = $this->db->select('id')->from('medical_college_review')->where('college_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT medical_college_review.id,medical_college_review.user_id,medical_college_review.college_id,medical_college_review.rating,medical_college_review.review, medical_college_review.service,medical_college_review.date as review_date,users.id as user_id,users.name as firstname FROM `medical_college_review` INNER JOIN `users` ON medical_college_review.user_id=users.id WHERE medical_college_review.college_id='$listing_id' order by medical_college_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '8') {
                    $decrypt = $this->decrypt($review);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $review) {
                        $review = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($review)) === $review) {
                        $review = base64_decode($review);
                    }
                }
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                //added by zak for user image review 
               // start
                        $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
               //end
                $like_count = $this->db->select('id')->from('medical_college_review_like')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('medical_college_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('medical_college_review_like')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

 $review_list_count = $this->db->select('id')->from('medical_college_review_comment')->where('post_id', $id)->get()->num_rows();
        if ($review_list_count) {
            $querycomment = $this->db->query("SELECT medical_college_review_comment.id,medical_college_review_comment.post_id,medical_college_review_comment.comment as comment,medical_college_review_comment.date,users.name,medical_college_review_comment.user_id as post_user_id FROM medical_college_review_comment INNER JOIN users on users.id=medical_college_review_comment.user_id WHERE medical_college_review_comment.post_id='$id' order by medical_college_review_comment.id asc");
                $resultcomment=array();
            foreach ($querycomment->result_array() as $rowc) {
                $comment_id = $rowc['id'];
                $post_id = $rowc['post_id'];
                $comment = $rowc['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
              
                $usernamec = $rowc['name'];
                $date = $rowc['date'];
                $post_user_id = $rowc['post_user_id'];

                $like_countc = $this->db->select('id')->from('medical_college_review_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                $like_yes_noc = $this->db->select('id')->from('medical_college_review_comment_like')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = get_time_difference_php($date);

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimagec = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimagec = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $date = get_time_difference_php($date);
                $resultcomment[] = array(
                    'id' => $comment_id,
                    'username' => $usernamec,
                    'userimage' => $userimagec,
                    'like_count' => $like_countc,
                    'like_yes_no' => $like_yes_noc,
                    'post_id' => $post_id,
                    'comment' => $comment,
                    'comment_date' => $comment_date
                );
            }
        } else {
            $resultcomment = array();
        }




                $resultpost[] = array(
                    'id' => $id,
                    'username' => $username,
                    'rating' => $rating,
                    'review' => $review,
                    'service' => $service,
                    'review_date' => $review_date,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'comment_count' => $post_count,
                    'userimage' => $userimage,
                    'comments'=>$resultcomment
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }



    public function review_like($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from medical_college_review_like where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `medical_college_review_like` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from medical_college_review_like where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $medical_college_review_like = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('medical_college_review_like', $medical_college_review_like);
            $like_query = $this->db->query("SELECT id from medical_college_review_like where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function add_review($user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'college_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('medical_college_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function review_comment($user_id, $post_id, $comment) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $medical_college_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('medical_college_review_comment', $medical_college_review_comment);
        $medical_college_review_comment_query = $this->db->query("SELECT id from medical_college_review_comment where post_id='$post_id'");
        $total_comment = $medical_college_review_comment_query->num_rows();
        return array(
            'status' => 201,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }

    public function review_comment_like($user_id, $comment_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from medical_college_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `medical_college_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from medical_college_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $medical_college_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('medical_college_review_comment_like', $medical_college_review_comment_like);
            $comment_query = $this->db->query("SELECT id from medical_college_review_comment_like where comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }

    public function review_comment_list($user_id, $post_id) {

        function get_time_difference_php($created_time) {
            date_default_timezone_set('Asia/Calcutta');
            $str = strtotime($created_time);
            $today = strtotime(date('Y-m-d H:i:s'));
            $time_differnce = $today - $str;
            $years = 60 * 60 * 24 * 365;
            $months = 60 * 60 * 24 * 30;
            $days = 60 * 60 * 24;
            $hours = 60 * 60;
            $minutes = 60;
            if (intval($time_differnce / $years) > 1) {
                return intval($time_differnce / $years) . ' yrs ago';
            } elseif (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . ' yr ago';
            } elseif (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . ' months ago';
            } elseif (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . ' month ago';
            } elseif (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . ' days ago';
            } elseif (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . ' day ago';
            } elseif (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . ' hrs ago';
            } elseif (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . ' hr ago';
            } elseif (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . ' min ago';
            } elseif (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . ' min ago';
            } elseif (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . ' sec ago';
            } else {
                return 'few seconds ago';
            }
        }

        $review_list_count = $this->db->select('id')->from('medical_college_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT medical_college_review_comment.id,medical_college_review_comment.post_id,medical_college_review_comment.comment as comment,medical_college_review_comment.date,users.name,medical_college_review_comment.user_id as post_user_id FROM medical_college_review_comment INNER JOIN users on users.id=medical_college_review_comment.user_id WHERE medical_college_review_comment.post_id='$post_id' order by medical_college_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '4') {
                    $comment_decrypt = $this->decrypt($comment);
                    $comment_encrypt = $this->encrypt($comment_decrypt);
                    if ($comment_encrypt == $comment) {
                        $comment = $comment_decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($comment)) === $comment) {
                        $comment = base64_decode($comment);
                    }
                }
                $username = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_count = $this->db->select('id')->from('medical_college_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('medical_college_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = get_time_difference_php($date);

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $date = get_time_difference_php($date);
                $resultpost[] = array(
                    'id' => $id,
                    'username' => $username,
                    'userimage' => $userimage,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'post_id' => $post_id,
                    'comment' => $comment,
                    'comment_date' => $comment_date
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }


  //added by zak for request broucher notification service 
  
  public function Request_broucher($user_id,$medicalcol_id)
  {
             date_default_timezone_set('Asia/Kolkata');
              $created_at = date('Y-m-d H:i:s');
              
              $notification = array(
                              'post_id' => '',
                              'timeline_id' => $user_id,
                              'user_id' => $user_id,
                              'notified_by' => $medicalcol_id,
                              'seen' =>'0',
                              'description' => 'user needs college broucher',
                              'type' =>'medical_broucher',
                              'link' => '',
                              'created_at' =>$created_at,
                              'updated_at' =>$created_at
                  );
              
              $this->db->insert('notifications', $notification);
             
              return array(
            'status' => 201,
            'message' => 'success'
             );
  }

   


}

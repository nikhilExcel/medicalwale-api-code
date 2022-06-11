<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class LabcenterModel_v2 extends CI_Model
{
    
    
    public function encrypt($str)
    {
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv  = hash('MD5', 'mdwale8655328655', true);
        $module    = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $pad   = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        $encrypted = mcrypt_generic($module, $str);
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        return base64_encode($encrypted);
    }
    public function decrypt($str)
    {
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv  = hash('MD5', 'mdwale8655328655', true);
        $module    = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $str = mdecrypt_generic($module, base64_decode($str));
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        $slast = ord(substr($str, -1));
        $str   = substr($str, 0, strlen($str) - $slast);
        return $str;
    }
    
    
     function unique_multidim_array($array, $key) { 
        $temp_array = array(); 
        $i = 0; 
        $key_array = array(); 
        
        foreach($array as $val) { 
            if (!in_array($val[$key], $key_array)) { 
                $key_array[] = $val[$key]; 
                $temp_array[] = $val; 
            } 
            $i++; 
        } 
        return $temp_array; 
    } 
    // only those tests will come which are present in lab_test_details1 table
    public function lab_tests($user_id,$home_delivery,$most_popular,$cat_id,$term,$per_page,$page_no){
        // $result = $this->db->query("SELECT DISTINCT lt.`id`, lt.`category_id`, lt.`test`, lt.`description` , lt.`description`, lt.`instructions` FROM `lab_all_test1` as lt JOIN `lab_test_details1` as ld ON ( lt.id = ld.test_id )")->result_array();
        //   return $result;
        $first_page = 1;
        $limit = "";
        $data = array();
        $cat_ids = explode(',',$cat_id);
        // print_r($cat_id); die();
        if($cat_id != 0){
            $cat_ids = $cat_id;
        } else {
            $cat_ids = '222,223,224';
        }
        
        if($per_page != 0 && $page_no != 0){
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
        } else {
            $limit = "";
        }
        
        
      //  echo $cat_ids; die();
        // SELECT lt.`id`, COUNT(ld.test_id) AS lab_count, lt.`category_id`, lt.`test`, lt.`description` , lt.`sample_type` , lt.`description`, lt.`instructions` ,ld.`home_delivery` FROM `lab_all_test1` as lt JOIN `lab_test_details1` as ld ON ( lt.id = ld.test_id ) join lab_center as lc on (ld.user_id = lc.user_id) WHERE lc.is_active = 1 and lt.id = 1

        $details = array();
        if($most_popular == 1){
            if($home_delivery == 1){ //
                $result = $this->db->query("SELECT lt.`id`, lt.`category_id`, lt.`test`, lt.`description` , lt.`sample_type` ,  lt.`description`, lt.`instructions`,lt.`image` ,ld.`home_delivery` FROM `lab_all_test1` as lt JOIN `lab_test_details1` as ld ON ( lt.id = ld.test_id ) join lab_center as lc on (ld.user_id = lc.user_id) WHERE lc.is_active = 1 and ld.`home_delivery`  = 1 AND lt.`test_took_count` > 0 AND lt.category_id IN ($cat_ids) AND lt.`test` LIKE '%$term%' GROUP by lt.`id` ORDER BY lt.`test_took_count` DESC $limit")->result_array();
                $rows_labs_count = $this->db->query("SELECT COUNT(*) as rows_count FROM `lab_all_test1` as lt JOIN `lab_test_details1` as ld ON ( lt.id = ld.test_id ) join lab_center as lc on (ld.user_id = lc.user_id) WHERE lc.is_active = 1 and ld.`home_delivery`  = 1 AND lt.`test_took_count` > 0 AND lt.category_id IN ($cat_ids) AND lt.`test` LIKE '%$term%'")->row_array();
            } else if($home_delivery == 2){
                $result = $this->db->query("SELECT lt.`id`, lt.`category_id`, lt.`test`, lt.`description` , lt.`sample_type` ,  lt.`description`, lt.`instructions`,lt.`image` ,ld.`home_delivery` FROM `lab_all_test1` as lt JOIN `lab_test_details1` as ld ON ( lt.id = ld.test_id ) join lab_center as lc on (ld.user_id = lc.user_id) WHERE lc.is_active = 1 and ld.`home_delivery`  = 0 AND lt.`test_took_count` > 0 AND lt.category_id IN ($cat_ids) AND lt.`test` LIKE '%$term%' GROUP by lt.`id` ORDER BY lt.`test_took_count` DESC $limit")->result_array();
                $rows_labs_count = $this->db->query("SELECT COUNT(*) as rows_count FROM `lab_all_test1` as lt JOIN `lab_test_details1` as ld ON ( lt.id = ld.test_id ) join lab_center as lc on (ld.user_id = lc.user_id) WHERE lc.is_active = 1 and ld.`home_delivery`  = 0 AND lt.`test_took_count` > 0 AND lt.category_id IN ($cat_ids) AND lt.`test` LIKE '%$term%'")->row_array();
            } else {
                $result = $this->db->query("SELECT lt.`id`, lt.`category_id`, lt.`test`, lt.`description` , lt.`sample_type` ,  lt.`description`, lt.`instructions`,lt.`image` ,ld.`home_delivery` FROM `lab_all_test1` as lt JOIN `lab_test_details1` as ld ON ( lt.id = ld.test_id) join lab_center as lc on (ld.user_id = lc.user_id) WHERE lc.is_active = 1 and lt.`test_took_count` > 0 AND lt.category_id IN ($cat_ids) AND lt.`test` LIKE '%$term%' GROUP by lt.`id` ORDER BY lt.`test_took_count` DESC $limit")->result_array();
                 $rows_labs_count = $this->db->query("SELECT COUNT(*) as rows_count FROM `lab_all_test1` as lt JOIN `lab_test_details1` as ld ON ( lt.id = ld.test_id) join lab_center as lc on (ld.user_id = lc.user_id) WHERE lc.is_active = 1 and lt.`test_took_count` > 0 AND lt.category_id IN ($cat_ids) AND lt.`test` LIKE '%$term%'")->row_array();
               
            }   
        } else {
            if($home_delivery == 1){ //
                $result = $this->db->query("SELECT lt.`id`, lt.`category_id`, lt.`test`, lt.`description` , lt.`sample_type` ,  lt.`description`, lt.`instructions` ,lt.`image`,ld.`home_delivery` FROM `lab_all_test1` as lt JOIN `lab_test_details1` as ld ON ( lt.id = ld.test_id ) join lab_center as lc on (ld.user_id = lc.user_id) WHERE lc.is_active = 1 and ld.`home_delivery`  = 1 AND lt.category_id IN ($cat_ids) AND lt.`test` LIKE '%$term%' GROUP by lt.`id` $limit")->result_array();
                $rows_labs_count = $this->db->query("SELECT COUNT(*) as rows_count FROM `lab_all_test1` as lt JOIN `lab_test_details1` as ld ON ( lt.id = ld.test_id ) join lab_center as lc on (ld.user_id = lc.user_id) WHERE lc.is_active = 1 and ld.`home_delivery`  = 1 AND lt.category_id IN ($cat_ids) AND lt.`test` LIKE '%$term%'")->row_array();
            } else if($home_delivery == 2){
                $result = $this->db->query("SELECT lt.`id`, lt.`category_id`, lt.`test`, lt.`description` , lt.`sample_type` ,  lt.`description`, lt.`instructions` ,lt.`image`,ld.`home_delivery` FROM `lab_all_test1` as lt JOIN `lab_test_details1` as ld ON ( lt.id = ld.test_id ) join lab_center as lc on (ld.user_id = lc.user_id) WHERE lc.is_active = 1 and ld.`home_delivery`  = 0 AND lt.category_id IN ($cat_ids) AND lt.`test` LIKE '%$term%' GROUP by lt.`id` $limit")->result_array();
                $rows_labs_count = $this->db->query("SELECT COUNT(*) as rows_count FROM `lab_all_test1` as lt JOIN `lab_test_details1` as ld ON ( lt.id = ld.test_id ) join lab_center as lc on (ld.user_id = lc.user_id) WHERE lc.is_active = 1 and ld.`home_delivery`  = 0 AND lt.category_id IN ($cat_ids) AND lt.`test` LIKE '%$term%'")->row_array();
            } else {
                $result = $this->db->query("SELECT lt.`id`, lt.`category_id`, lt.`test`, lt.`description` , lt.`sample_type` ,  lt.`description`, lt.`instructions` ,lt.`image`,ld.`home_delivery` FROM `lab_all_test1` as lt JOIN `lab_test_details1` as ld ON ( lt.id = ld.test_id ) join lab_center as lc on (ld.user_id = lc.user_id) WHERE lc.is_active = 1 AND lt.category_id IN ($cat_ids) AND lt.`test` LIKE '%$term%' GROUP by lt.`id` $limit")->result_array();
                $rows_labs_count = $this->db->query("SELECT COUNT(*) as rows_count FROM `lab_all_test1` as lt JOIN `lab_test_details1` as ld ON ( lt.id = ld.test_id ) join lab_center as lc on (ld.user_id = lc.user_id) WHERE lc.is_active = 1 AND lt.category_id IN ($cat_ids) AND lt.`test` LIKE '%$term%'")->row_array();
            }    
        }
        
        $data_count = $rows_labs_count['rows_count'];
        if($per_page != 0 && $page_no != 0){
            $last_page = ceil($data_count/$per_page);
            $d['data_count'] = $data_count;
            $d['per_page'] = $per_page;
            $d['current_page'] = $page_no;
            $d['first_page'] = $first_page;
            $d['last_page'] = $last_page;
        }else{
            $page_no = 1;
            $last_page = 1;
            
            $d['data_count'] = $data_count;
            $d['per_page'] = $data_count;
            $d['current_page'] = $page_no;
            $d['first_page'] = $first_page;
            $d['last_page'] = $last_page;
            
        }
        
        
        $details = $this->LabcenterModel_v2->unique_multidim_array($result,'id'); 
        
        if(sizeof($details > 0)){
            foreach($details as $r){
                // print_r($r); die();
                $id = $r['id'];
                $checkcount = $this->db->query("SELECT COUNT(id) as lab_count from `lab_test_details1` WHERE test_id = '$id'")->row_array();
                 $r['lab_count'] = $checkcount['lab_count'];
                $data[] = $r;

            }
        } else {
            $data = array();
        }
        $finalArray = array();
        $finalArray['pagination'] = $d;
        $finalArray['data'] = $data;
        // print_r($details); die();
        return $finalArray;
        // return $details;
    }
    
    //you will get all vendors by test id
    public function lab_vendor_by_test($user_id,$test_id,$per_page,$page_no){
            $testInfo = $this->db->query("SELECT * FROM `lab_all_test1` WHERE `id` = '$test_id'")->row_array();
            $testName = $testInfo['test'];
        if(sizeof($testInfo) > 0 ){
            if($per_page == 0 || $page_no == 0){
            $results = $this->db->query("SELECT lt.* FROM `lab_test_details1` as lt join lab_center as lc on (lt.user_id = lc.user_id ) WHERE `test_id` = '$test_id' AND lc.`is_active` = 1")->result_array();
            $totalData = sizeof($results);
            
             $per_page = $totalData;
                $page_no = 1;
                $last_page = 1;
            
          } else {
            $offset = $per_page*($page_no - 1);
            $results = $this->db->query("SELECT lt.* FROM `lab_test_details1` as lt join lab_center as lc on (lt.user_id = lc.user_id ) WHERE `test_id` = '$test_id' AND lc.`is_active` = 1 LIMIT $per_page OFFSET $offset")->result_array();
            $totalRowCount = $this->db->query("SELECT count(lt.id) as count FROM `lab_test_details1` as lt join lab_center as lc on (lt.user_id = lc.user_id ) WHERE `test_id` = '$test_id' AND lc.`is_active` = 1")->row_array();
            
            $totalData = $totalRowCount['count'];
            $last_page = ceil($totalData/$per_page);
        }
            
            foreach($results as $r){
                $vendorId = $r['user_id'];
                $vendorInfo = $this->db->query("SELECT * FROM `lab_center` WHERE (`user_id` = '$vendorId' OR `branch_user_id` = '$vendorId') AND `is_active` = 1")->row_array();
                if(!empty($vendorInfo)){
                    foreach($vendorInfo as $key => $value){
                       if($key == 'profile_pic' ){
                            $vendorInfo[$key] = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$vendorInfo['profile_pic'] ;
                        }
                    }
                    unset($vendorInfo['is_vendor']);
                    $r['vendor_info'] = $vendorInfo;
                } else {
                    $r['vendor_info'] = (object)[];
                }
                
                $res[] = $r;
            }
            $test['test_name'] = $testName;
            $test['data_count'] = $totalData;
            $test['per_page'] = $per_page;
            $test['current_page'] = $page_no;
            $test['first_page'] = 1;
            $test['last_page'] = $last_page;
            $test['test_available'] = $res;
            
            
        } else {
            $test['test_name'] = "";
            $test['data_count'] = 0;
            $test['per_page'] = 0;
            $test['current_page'] = 0;
            $test['first_page'] = 0;
            $test['last_page'] = 0;
            $test['test_available'] = array();
           
        }    
        return $test;
          
    }
    
    public function lab_test_by_vendor($vendor_id,$per_page,$page_no,$term,$cat_id){
        $timings = $data = array();
        if(!empty($term)){
            $searchTerm = "AND lat.test like '%$term%' or lat.sample_type like '%$term%' or lat.description like '%$term%' ";
        }else{
            $searchTerm = "";
        }
      
        if(!empty($cat_id)){
            $searchcat = "AND lat.category_id = '$cat_id'";
        }else{
            $searchcat = "";
        }
      
        if($per_page != 0 && $page_no != 0){
            $offset = $per_page*($page_no - 1);
            $data =  $this->db->query("SELECT lc.lab_name as vendor_name, m.source ,lc.`recommended`,lc.`certified`,lc.`mba` , lc.opening_hours,lc.address1, lc.address2,lc.contact_no,lc.latitude,lc.longitude, lc.pincode, lc.city, lc.state, ltd.*, lat.test,lat.sample_type,lat.description,lat.instructions FROM `lab_test_details1` as ltd join lab_all_test1 as lat ON (ltd.test_id = lat.id ) left join lab_center as lc on (ltd.user_id = lc.user_id) left join users as u on (ltd.user_id = u.id) left join media as m on (u.avatar_id = m.id)  WHERE ltd.`user_id` = '$vendor_id' AND lc.`is_active` = 1 $searchTerm $searchcat LIMIT $per_page OFFSET $offset ")->result_array();
            
            $dataCount =  $this->db->query("SELECT count(ltd.id) as counts FROM `lab_test_details1` as ltd join lab_all_test1 as lat ON (ltd.test_id = lat.id ) WHERE `user_id` = '$vendor_id' $searchTerm $searchcat ")->row_array();
            
            $totalData = $dataCount['counts'];
            $per_page = $per_page;
            $page_no = $page_no;
            $last_page = ceil($totalData / $per_page);    
            
        } else {
            $data =  $this->db->query("SELECT lc.lab_name as vendor_name, m.source ,lc.`recommended`,lc.`certified`,lc.`mba`,lc.opening_hours,lc.address1,lc.contact_no,lc.latitude,lc.longitude, lc.address2, lc.pincode, lc.city, lc.state, ltd.*, lat.test,lat.sample_type,lat.description,lat.instructions FROM `lab_test_details1` as ltd join lab_all_test1 as lat ON (ltd.test_id = lat.id ) left join lab_center as lc on (ltd.user_id = lc.user_id) left join users as u on (ltd.user_id = u.id) left join media as m on (u.avatar_id = m.id) WHERE ltd.`user_id` = '$vendor_id' AND lc.`is_active` = 1 $searchTerm $searchcat")->result_array();
            $per_page = sizeof($data);
            $page_no = 1;
            $last_page = 1;
            $totalData = sizeof($data);
            // $last_page = ceil($totalData / $per_page);   
        }
       
        
            if(sizeof($data) > 0){ 
                $vendorInfo = $data[0];
            } else {
                $vendorInfo = (object)[];
            }
        //  print_r($vendorInfo); die(); 
       




       $finalData = array();
        foreach($data as $d){
             $d['vendor_address'] = '';
            // foreach($d as $k=>$v){
            //     $d[$k] = $v; 
            // }
                // $d['vendor_name'] = $d['vendor_name'];
            $discount = $d['discount'];    
            $discount_type = 'percent';
            $Price  =$d['price'];
            if($discount > 0){
                $d['discounted_price'] = $this->LabcenterModel_v2->get_disounted_rate($Price,$discount_type,$discount);
            } else {
                $d['discounted_price'] = strval($Price); 
            }
            
                $address1 = $d['address1'];
                $address2 = $d['address2'];
                $pincode = $d['pincode'];
                $city = $d['city'];
                $state = $d['state'];
                $contact_no = $d['contact_no'];
                $latitude = $d['latitude'];
                $longitude = $d['longitude'];
               
            
            if($address1){ $d['vendor_address'] .=  $address1;}
            if($address2){ $d['vendor_address'] .= ', '. $address2;}
            if($city){ $d['vendor_address'] .= ', '. $city;}
            if($state){ $d['vendor_address'] .= ', '. $state;}
            if($pincode){ $d['vendor_address'] .= ', '. $pincode;}
            
            if($contact_no){ $d['contact_no'] =  $contact_no;}
            if($latitude){ $d['latitude'] = $latitude;}
            if($longitude){ $d['longitude'] =  $longitude;}
            
            
            // print_r( $avatar_id) ; die();
            $avatar_id = $d['source'];
            if ($avatar_id != null && !empty($avatar_id)) {
               $d['vendor_image']     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $avatar_id;
            } else {
                $d['vendor_image'] = '';
            }
            
            unset($d['address1']);
            unset($d['address2']);
            unset($d['pincode']);
            unset($d['city']);
            unset($d['state']);
            unset($d['source']);
            
            
            $timings = $d['opening_hours'];
            
            $lab_hours = $this->LabcenterModel_v2->get_timings($timings);
            // print_r($lab_hours['timings']); die();
            $d['status'] = $lab_hours['status'];
            $d['timings'] = $lab_hours['timings'];
            
              $rating_query = $this->db->query("SELECT sum(labcenter_review.rating) as rating, count(labcenter_review.rating) as total_view FROM `labcenter_review` WHERE labcenter_review.labcenter_id='$vendor_id'");
                $rating_coun = $rating_query->num_rows();
                if($rating_coun > 0)
                {
                $rating_counting = $rating_query->row()->total_view;
                if($rating_counting > 0)
                {
                $rating_view = $rating_query->row()->rating;
                $rating_count=$rating_view/$rating_counting;
                }
                else
                {
                    $rating_count=0; 
                }
                }
                else
                {
                   $rating_count=0; 
                }
                $d['rating']=   number_format($rating_count,1); 
         
            $finalData[] = $d;
            
        }
        
        $test['data_count'] = $totalData;
        $test['per_page'] = $per_page;
        $test['current_page'] = $page_no;
        $test['first_page'] = 1;
        $test['last_page'] = $last_page;
        
        // $test['vendor_name'] = $vendorInfo['vendor_name'];
        // $test['vendor_address'] = ' ';
           
           
            
            
        $test['tests'] = $finalData;
        
        return $test;
    }
   
    
     // lab_packages
    public function lab_packages($vendor_id,$per_page,$page_no,$home_delivery,$body_checkup,$lat,$lng,$term,$sort_a_z,$price_low_high){
        
        // echo $sort_a_z; die();
        
         $contact_no = $latitude = $longitude = $status = "Close";
       $timings= $vendors = $finalTests =  $result = $data = array();
        // print_r($body_checkup); die();
        // $home_delivery
        
        if($vendor_id == 0 && $lat != 0 && $lng != 0){
            $nearbyVendors = $this->db->query("SELECT `id`,`user_id`,`recommended`,`certified`,`mba` ,`branch_user_id`, ( 6371 * acos( cos( radians('19.1266993') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('72.8498449') ) + sin( radians('19.1266993') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center WHERE  `is_active` = 1 HAVING distance < '5000' ORDER BY distance")->result_array();
            foreach($nearbyVendors as $v ){
                $vendors[] = $v['user_id'];
                if($v['branch_user_id'] != 0){
                    $vendors[] = $v['branch_user_id'];
                }
            }
        }
        $vendors = implode("','",$vendors);
        
        
        
        if($sort_a_z == 1){
            $soringAlph = "package_name ASC";
        } else if($sort_a_z == 2){
            $soringAlph = "package_name DESC";
        } else {
            $soringAlph = "";
        }
        
        // $price_low_high
        
        
        
        if($price_low_high == 1){
            $priceLowHigh = "Price ASC";
        } else if($price_low_high == 2){
            $priceLowHigh = "Price DESC";
        } else {
            $priceLowHigh = "";
        }
        
        if($soringAlph != "" && $priceLowHigh != ""){
             $orderBy = "ORDER BY ". $priceLowHigh . ", " .$soringAlph;
        } else if($soringAlph == "" && $priceLowHigh == ""){
            $orderBy = "";
        } else {
            $orderBy = "ORDER BY ". $priceLowHigh. " " .$soringAlph;
        }
       
      // echo $orderBy; die();
        
        
        if($vendor_id != 0){
            $vendor_id = $vendor_id;
        } else if(!empty($vendors)){
            $vendor_id = $vendors; 
        } else {
            $vendor_id = 0; 
        }
        // echo $vendor_id; die();
        
        
        if($per_page != 0 && $page_no != 0){
            $offset = $per_page*($page_no - 1);
            
             if($vendor_id != 0){
                //  echo "in page and vendor"; die();
                $data =  $this->db->query("SELECT * FROM `lab_packages1` WHERE `v_id` = 10 AND `user_id` IN ('$vendor_id') AND `home_delivery` IN ($home_delivery) AND `full_body_checkup` IN ($body_checkup) and (`package_name` like '%$term%' or `package_details` like '%$term%') $orderBy LIMIT $per_page OFFSET $offset")->result_array();
               // echo "SELECT * FROM `lab_packages1` WHERE `v_id` = 10 AND `user_id` IN ('$vendor_id') AND `home_delivery` IN ($home_delivery) AND `full_body_checkup` IN ($body_checkup) and (`package_name` like '%$term%' or `package_details` like '%$term%') $orderBy LIMIT $per_page OFFSET $offset"; die();
                $totalData =  $this->db->query("SELECT COUNT(*)  as count FROM `lab_packages1`  WHERE `v_id` = 10 AND `user_id` IN ('$vendor_id') AND `home_delivery` IN ($home_delivery) AND `full_body_checkup` IN ($body_checkup) and (`package_name` like '%$term%' or `package_details` like '%$term%')")->row_array();
                $totalData = $totalData['count'];
                
            } else {
                $data =  $this->db->query("SELECT * FROM `lab_packages1` WHERE `v_id` = 10 AND  `home_delivery` IN ($home_delivery)  AND `full_body_checkup` IN ($body_checkup) and (`package_name` like '%$term%' or `package_details` like '%$term%') $orderBy LIMIT $per_page OFFSET $offset")->result_array();
                
                $totalData =  $this->db->query("SELECT COUNT(*) as count FROM `lab_packages1` WHERE `v_id` = 10 AND `home_delivery` IN ($home_delivery) AND `full_body_checkup` IN ($body_checkup) and (`package_name` like '%$term%' or `package_details` like '%$term%')")->row_array();
               
                $totalData = $totalData['count'];
            }
            
            $per_page = $per_page;
            $page_no = 1;
            $last_page = 1;
            $last_page = ceil($totalData / $per_page);
            
            
        } else {
            if($vendor_id != 0){
                $data =  $this->db->query("SELECT * FROM `lab_packages1` WHERE `v_id` = 10 AND `user_id` IN ('$vendor_id') AND `home_delivery` IN ($home_delivery) AND `full_body_checkup` IN ($body_checkup) and (`package_name` like '%$term%' or `package_details` like '%$term%') $orderBy")->result_array();
            } else {
                $data =  $this->db->query("SELECT * FROM `lab_packages1` WHERE `v_id` = 10 AND `home_delivery` IN ($home_delivery) AND `full_body_checkup` IN ($body_checkup) and (`package_name` like '%$term%' or `package_details` like '%$term%') $orderBy")->result_array();
            }
           
            $totalData = sizeof($data);
            $per_page = $totalData;
            $page_no = 1;
            $last_page = 1;
        }
         
        foreach($data as $d){
            // print_r($d); die;
            $timings = array();
            $user_id = $d['user_id'];
            $user_name = $this->db->query("SELECT `lab_name` as name ,`branch_user_id`,`recommended`,`certified`,`mba`,contact_no,latitude,longitude, `opening_hours`, `profile_pic`,`address1`,`address2`,`pincode`,`city`,`state` FROM `lab_center`  WHERE `user_id` = '$user_id' AND `is_active` = 1")->row_array();
            if(empty($user_name['name'])){
                $user_name = $this->db->query("SELECT `lab_name` as name ,`branch_user_id`,`recommended`,`certified`,`mba`,contact_no,latitude,longitude, `opening_hours`, `profile_pic`,`address1`,`address2`,`pincode`,`city`,`state` FROM `lab_center`  WHERE `branch_user_id` = '$user_id' AND `is_active` = 1")->row_array();
            
                if(empty($user_name['name'])){
                
                    $vendor_name = "";
                    $vendor_image = "";
                    $address1 ="";
                    $address2 ="";
                    $pincode ="";
                    $city ="";
                    $state ="";
                    
                    $recommended = "0";
                    $certified = "0";
                    $mba = "0";

                } else {
                    $vendor_name = $user_name['name'];
                    $vendor_image = "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$user_name['profile_pic'];
                    $address1 = $user_name['address1'];
                    $address2 = $user_name['address2'];
                    $pincode = $user_name['pincode'];
                    $city = $user_name['city'];
                    $state = $user_name['state'];
                    
                    $contact_no = $user_name['contact_no'];
                    $latitude = $user_name['latitude'];
                    $longitude = $user_name['longitude'];
                    
                    $recommended = $user_name['recommended'];
                    $certified = $user_name['certified'];
                    $mba = $user_name['mba'];
                
                
                    
                }
            
                
            } else {
                $vendor_name = $user_name['name'];
                $vendor_image = "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$user_name['profile_pic'];
                $address1 = $user_name['address1'];
                $address2 = $user_name['address2'];
                $pincode = $user_name['pincode'];
                $city = $user_name['city'];
                $state = $user_name['state'];
                
                $contact_no = $user_name['contact_no'];
                    $latitude = $user_name['latitude'];
                    $longitude = $user_name['longitude'];
                    
                    $recommended = $user_name['recommended'];
                    $certified = $user_name['certified'];
                    $mba = $user_name['mba'];
                
            
                
            }
            
            if(empty($user_name['opening_hours'])){
                $timings = array();
                // echo $user_id; die();
                
            } else {
                
                
                $open_timings = $user_name['opening_hours'];
                // $timings = $d['opening_hours'];
            
             
             $lab_hours = $this->LabcenterModel_v2->get_timings($open_timings);
            $status = $lab_hours['status'];
            $timings = $lab_hours['timings'];
            
            
       
                /*   $eachDays = explode('|',$open_timings);
                   
                   foreach($eachDays as $days){
                       
                       $dailyInfo = explode('>',$days);
                       
                       $tms['day'] = $dailyInfo[0];
                       $times = $dailyInfo[1];
                       
                       $time = explode('-',$times);
                       
                       $tms['start_time'] = $time[0];
                       $tms['end_time'] = $time[1];
                       
                       
                          $StarttotalTime = $time[0];
                          $EndtotalTime = $time[1];
                          $stm = explode(',',$StarttotalTime);
                          $etm = explode(',',$EndtotalTime);
                          $start = $end = "";
                          for($i=0;$i<sizeof($stm);$i++){
                              if($i!=0){
                                  $start .= ", " ;
                              }
                              $start .= $stm[$i] ." - ". $etm[$i];
                            
                          } 
                         
                            $tms['time'] = $start;
                       
                       $timings[] = $tms; 
                   }*/
                   
            }
            
           
            $finalTests = $tests =array();
            $d['timings'] = $timings;
            $d['status'] = $status;
            $d['vendor_name'] = $vendor_name ;
            $d['vendor_image'] = $vendor_image ;
            $d['address1'] = $address1;
            $d['address2'] = $address2;
            $d['pincode'] = $pincode;
            $d['city'] = $city;
            $d['state'] = $state;
            $d['contact_no'] = $contact_no;
            $d['latitude'] = $latitude;
            $d['longitude'] = $longitude;
            
            $d['recommended'] = $recommended;
            $d['certified'] = $certified;
            $d['mba'] = $mba;
            $discount = $d['discount'];
            $Price = $d['Price'];
            $discount_type = $d['discount_type'];
            $discounted_price = 0;
            
            
            $pathology_test = $d['pathology_test'];
            $diagnostic_test = $d['diagnostic_test'];  
            $fnac_test = $d['fnac_test'];  
            
            if(!empty($pathology_test)){
                $tests[] = $this->db->query("SELECT * FROM `lab_all_test1` WHERE `id` IN ($pathology_test)")->result_array();
            }
            if(!empty($diagnostic_test)){
                $tests[] = $this->db->query("SELECT * FROM `lab_all_test1` WHERE `id` IN ($diagnostic_test)")->result_array();
            }
            if(!empty($fnac_test)){
                $tests[] = $this->db->query("SELECT * FROM `lab_all_test1` WHERE `id` IN ($fnac_test)")->result_array();
            }            
            // SELECT * FROM `lab_all_test1` WHERE `id` IN (1,2)
            // print_r($tests); die();
            
            
            if($discount > 0){
                $discounted_price = $this->LabcenterModel_v2->get_disounted_rate($Price,$discount_type,$discount);
            } else {
                $discounted_price = strval($Price); 
            }
            // echo $discounted_price; die();
            foreach($d as $key => $value){
                if($value == null && !is_array($value)){
                    $d[$key] = "";
                }
            }
            $d['discounted_price'] = $discounted_price;
            $d['discount_price'] = $discounted_price;
           
            foreach($tests as $ts){
                foreach($ts as $t){
                    if(!empty($t)){
                        unset($t['created_at']);
                        unset($t['created_by']);
                        unset($t['updated_at']);
                        unset($t['updated_by']);
                        
                        $finalTests[] = $t;
                    }
                }
            }
            
            $details = $this->LabcenterModel_v2->unique_multidim_array($finalTests,'id'); 
        // print_r($d); die();
         $rating_query = $this->db->query("SELECT sum(labcenter_review.rating) as rating, count(labcenter_review.rating) as total_view FROM `labcenter_review` WHERE labcenter_review.labcenter_id='$user_id'");
                $rating_coun = $rating_query->num_rows();
                if($rating_coun > 0)
                {
                $rating_counting = $rating_query->row()->total_view;
                if($rating_counting > 0)
                {
                $rating_view = $rating_query->row()->rating;
                $rating_count=$rating_view/$rating_counting;
                }
                else
                {
                    $rating_count=0; 
                }
                }
                else
                {
                   $rating_count=0; 
                }
                $d['rating']=  number_format($rating_count,1); 
        
        
            $d['tests'] = $details;
            
            $result[] = $d;
            // print_r($d); die();
        }
        //  die();
         
         
         
        $test['data_count'] = $totalData;
        $test['per_page'] = $per_page;
        $test['current_page'] = $page_no;
        $test['first_page'] = 1;
        $test['last_page'] = $last_page;
        $test['packages'] = $result;
     
     
        
     
        return $test;
    }
    
    // lab_search_test_packages
    public function lab_search_test_packages($user_id,$term,$search_for,$per_page,$page_no){
        $rows_labs_count = $rows_packages_count = $data_count = 0;
        $rows_labs = $rows_packages = $finalData = $rows_count = $rows = $data = array();
        $offset = $per_page*($page_no - 1);
        // $search_for = 1 means search for test 2 means labs and 0 default for all
        // search tests
        if($search_for == 1){
            $rows = $this->db->query("SELECT `id`, `category_id`, `test`, `sample_type`, `description`, `instructions` FROM `lab_all_test1` WHERE `test` LIKE '%$term%' OR `description` LIKE '%$term%' OR `sample_type` LIKE '%$term%' ORDER BY `test_took_count` DESC  LIMIT $per_page OFFSET $offset")->result_array();
            $rows_count = $this->db->query("SELECT COUNT(*) as rows_count FROM `lab_all_test1` WHERE `test` LIKE '%$term%' OR `description` LIKE '%$term%' OR `sample_type` LIKE '%$term%'")->row_array();
            foreach($rows as $r){
                foreach($r as $k => $v){
                    if($v == null){
                        $r[$k] = "";
                    }
                    $r['search_type'] = "tests";
                    $r['search_name'] = $r['test'];
                }
                $finalData[] = $r;
            }
            $data_count = $rows_count['rows_count'];
            
        } else if($search_for == 2) { //search labs
            $rows = $this->db->query("SELECT `id`, `package_name`, `package_details`,  `Price`, `image`, `v_id`, `pathology_test`, `diagnostic_test`, `fnac_test`, `user_id`, `category_id`, `discount` FROM `lab_packages1` WHERE `v_id` = 10 AND (`package_name` LIKE '%$term%' OR `package_details` LIKE '%$term%')  LIMIT $per_page OFFSET $offset")->result_array();
            $rows_count = $this->db->query("SELECT COUNT(*) as rows_count FROM `lab_packages1` WHERE `v_id` = 10 AND ( `package_name` LIKE '%$term%' OR `package_details` LIKE '%$term%')")->row_array();
            foreach($rows as $r){
                foreach($r as $k => $v){
                    if($v == null){
                        $r[$k] = "";
                    }
                    $r['search_type'] = "packages";
                    $r['search_name'] = $r['package_name'];
                }
                $finalData[] = $r;
            }
            $data_count = $rows_count['rows_count'];
                    
        } else { // both labs and test
            $totalLabs = ceil($per_page / 2);
            $totalPackages = $per_page - $totalLabs;
            
            $offsetLabs = $totalLabs*($page_no - 1);
            $offsetPackages = $totalPackages*($page_no - 1);
            
            
            $rows_labs = $this->db->query("SELECT `id`, `category_id`, `test`, `sample_type`, `description`, `instructions` FROM `lab_all_test1` WHERE `test` LIKE '%$term%' OR `description` LIKE '%$term%' OR `sample_type` LIKE '%$term%' ORDER BY `test_took_count` DESC  LIMIT $totalLabs OFFSET $offsetLabs")->result_array();
            $rows_labs_count = $this->db->query("SELECT COUNT(*) as rows_count FROM `lab_all_test1` WHERE `test` LIKE '%$term%' OR `description` LIKE '%$term%' OR `sample_type` LIKE '%$term%'")->row_array();
        
            $rows_packages = $this->db->query("SELECT `id`, `package_name`, `package_details`, `Price`, `image`, `v_id`, `pathology_test`, `diagnostic_test`, `fnac_test`, `user_id`, `category_id`, `discount` FROM `lab_packages1` WHERE `v_id` = 10 AND  (`package_name` LIKE '%$term%' OR `package_details` LIKE '%$term%')  LIMIT $totalPackages OFFSET $offsetPackages")->result_array();
            $rows_packages_count = $this->db->query("SELECT COUNT(*) as rows_count FROM `lab_packages1` WHERE `v_id` = 10 AND ( `package_name` LIKE '%$term%' OR `package_details` LIKE '%$term%')  LIMIT $per_page OFFSET $offset")->row_array();
            
            $data_count = $rows_labs_count['rows_count'] +  $rows_packages_count['rows_count'];
            // echo $data_count; die();
            
            if($rows_labs_count['rows_count'] > 0)
            {
            foreach($rows_labs as $r){
                foreach($r as $k => $v){
                    if($v == null){
                        $r[$k] = "";
                    }
                    $r['search_type'] = "tests";
                    $r['search_name'] = $r['test'];
                }
                $finalDatalabs[] = $r;
            }
        }
        else
        {
           $finalDatalabs = array(); 
        }
        if($rows_packages_count['rows_count'] > 0)
        {
            foreach($rows_packages as $r){
                foreach($r as $k => $v){
                    if($v == null){
                        $r[$k] = "";
                    }
                    $r['search_type'] = "packages";
                    $r['search_name'] = $r['package_name'];
                }
                $finalDatapackages[] = $r;
            }
        }
        else
        {
            $finalDatapackages=array();
        }
            
            $finalData = array_merge( $finalDatalabs, $finalDatapackages );
        }
        
        if($data_count > 0){
            $last_page = ceil($data_count / $per_page);
            $first_page = 1;
            
        } else {
            $last_page = 0;
            $first_page = 0;
            $page_no = 0;
            $per_page = 0;
        }
        
        if($finalData == null){
            $finalData = array();
        }
        
        $data['data_count'] = $data_count;
        $data['per_page'] = $per_page;
        $data['current_page'] = $page_no;
        $data['first_page'] = $first_page;
        $data['last_page'] = $last_page;
        $data['search_result'] = $finalData;

        
        return $data;
    }
    
    // 
    public function get_disounted_rate($Price,$discount_type,$discount){
        $discounted_price = 0;
        if($discount_type == 'rupee'){
            $discounted_price = $Price - $discount;
        } else {
            $totalDiscount = ( $Price * ($discount / 100));
            $discounted_price = $Price - $totalDiscount;
        }
        
        if($discounted_price < 0){
            $discounted_price = 0;
        }
        return ceil($discounted_price);
    }
    
    // lab_test_by_tests
    
    public function lab_test_by_tests($user_id,$given_test_id,$lat,$lng,$page_no,$per_page){
        $contact_no = $latitude = $longitude = "";
        $status = "Close";
        $data_count = $current_page = $first_page = $last_page = 1;
        $timings =  $vendors = $packageData = $testAllInfo =  $data = array();
        $oldVendorId = 0;
        $pagination = $whereVendor = "";
        
        if($lat != 0 && $lng != 0){
           
            // $nearbyVendors = $this->db->query("SELECT `id`,`user_id`, ( 6371 * acos( cos( radians('19.1266993') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('72.8498449') ) + sin( radians('19.1266993') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  HAVING distance < '5' ORDER BY distance")->result_array();
           
            $nearbyVendors = $this->db->query("SELECT `id`,`branch_user_id`,`recommended`,`certified`,`mba`,`user_id`, ( 6371 * acos( cos( radians('$lat') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('$lng') ) + sin( radians('$lat') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center where `is_active` = 1  HAVING distance < '100' ORDER BY distance")->result_array();
            foreach($nearbyVendors as $v ){
                $vendors[] = $v['user_id'];
                if($v['branch_user_id'] != 0){
                    $vendors[] = $v['branch_user_id'];
                }
            }
            $vendors = implode("','",$vendors);
           
            $whereVendor = "AND ltd.`user_id` IN ('$vendors')";
            
        }
        
        if($page_no != 0 && $per_page != 0){
            $current_page = $page_no;
            $offset = $per_page*($page_no - 1);
            
            $pagination = "LIMIT $per_page OFFSET $offset";
        } 
        
        //123456
        // echo "SELECT ltd.*,lc.lab_name as name, lat.test FROM `lab_test_details1` as ltd LEFT JOIN lab_center as lc ON (ltd.user_id = lc.user_id) LEFT JOIN lab_all_test1 as lat ON(ltd.`test_id` = lat.id) WHERE ltd.`test_id` IN($given_test_id) $whereVendor  ORDER BY `user_id` ASC";
        // die();
        
        
        $strLen = strlen($given_test_id) ;
        $last = substr($given_test_id, -1);
        // print_r($last);
            while(!is_numeric($last)){
                $strLen = $strLen - 1;
                $given_test_id = substr($given_test_id, 0, $strLen);
                
                $last = substr($given_test_id, -1);
                
               
            }
        
        $allTests = $this->db->query("SELECT ltd.*,lc.lab_name as name,lc.profile_pic ,lc.`recommended`,lc.`certified`,lc.`mba`, lc.`opening_hours`,lc.`address1`,lc.`address2`,lc.contact_no,lc.latitude,lc.longitude,lc.`pincode`,lc.`city`,lc.`state`, lat.test,lat.sample_type FROM `lab_test_details1` as ltd LEFT JOIN lab_center as lc ON (ltd.user_id = lc.user_id OR ltd.user_id = lc.branch_user_id) LEFT JOIN lab_all_test1 as lat ON(ltd.`test_id` = lat.id) WHERE ltd.`price` != 0 and  ltd.`test_id` IN($given_test_id) $whereVendor   GROUP by ltd.id   ORDER BY `user_id` ASC $pagination")->result_array();
        // $data_count_All = $this->db->query("SELECT COUNT(ltd.id) as counts FROM `lab_test_details1` as ltd LEFT JOIN lab_center as lc ON (ltd.user_id = lc.user_id) LEFT JOIN lab_all_test1 as lat ON(ltd.`test_id` = lat.id) WHERE ltd.`test_id` IN(1,2) ORDER BY ltd.`user_id` ASC")->row_array();
       $data_count_All = $this->db->query("SELECT COUNT(ltd.id) as counts FROM `lab_test_details1` as ltd LEFT JOIN lab_center as lc ON (ltd.user_id = lc.user_id OR ltd.user_id = lc.branch_user_id) LEFT JOIN lab_all_test1 as lat ON(ltd.`test_id` = lat.id) WHERE ltd.`price` != 0 AND lc.`is_active` = 1 and ltd.`test_id` IN($given_test_id) $whereVendor GROUP BY ltd.`user_id` ORDER BY ltd.`user_id` ASC")->result_array();
        $data_count = sizeof($data_count_All); 
        $totalDiscounted_price = $totalPrice = 0;
      
        foreach($allTests as $t){
            // print_r($t); die();
            $timings = array();
            $id = $t['test_id'];
            $test_id = $t['test_id'];
            $user_id = $t['user_id'];
            $price = $t['price'];
            $discount = $t['discount'];
            $discounted_price = $t['discounted_price'];
            $discount_type = 'percent';
            if($discounted_price == 0 || $discounted_price == ''){
                if($discount != 0){
                    $discounted_price = $this->LabcenterModel_v2->get_disounted_rate($price,$discount_type,$discount);    
                } else {
                    $discounted_price = $price;
                }
                
            }
            
            $offer = $t['offer'];
            $home_delivery = $t['home_delivery'];
            $name = $t['name'];
            $profile_pic = $t['profile_pic'];
            $test = $t['test'];
            // print_r($t['test']); die();
            $sample_type = $t['sample_type'];
            
            $address1 = $t['address1'];
            $address2 = $t['address2'];
            $pincode = $t['pincode'];
            $city = $t['city'];
            $state = $t['state'];
            
             $contact_no = $t['contact_no'];
            $latitude = $t['latitude'];
            $longitude = $t['longitude'];
            
            $recommended = $t['recommended'];
            $certified = $t['certified'];
            $mba = $t['mba'];
            
             
            $open_timings = $t['opening_hours'];
            if(!empty($open_timings)){
            //   $eachDays = explode('|',$open_timings);
              //  $open_timings = $user_name['opening_hours'];
                // $timings = $d['opening_hours'];
            
            //  $timings[]  = $this->LabcenterModel_v2->get_timings($open_timings);
             
               $lab_hours = $this->LabcenterModel_v2->get_timings($open_timings);
            $status = $lab_hours['status'];
            $timings = $lab_hours['timings'];
               
               /*foreach($eachDays as $days){
                   
                   $dailyInfo = explode('>',$days);
                   
                   $tms['day'] = $dailyInfo[0];
                   $times = $dailyInfo[1];
                   
                   $time = explode('-',$times);
                   
                   $tms['start_time'] = $time[0];
                   $tms['end_time'] = $time[1];
                   
                   
                          $StarttotalTime = $time[0];
                          $EndtotalTime = $time[1];
                          $stm = explode(',',$StarttotalTime);
                          $etm = explode(',',$EndtotalTime);
                          $start = $end = "";
                          for($i=0;$i<sizeof($stm);$i++){
                              if($i!=0){
                                  $start .= ", " ;
                              }
                              $start .= $stm[$i] ." - ". $etm[$i];
                            
                          } 
                         
                            $tms['time'] = $start;
                            
                   $timings[] = $tms; 
                   
               }*/
            }  else{
                $timings = array();
            }
            
            if($oldVendorId ==  $t['user_id']){
                
                $testInnerInfo['id'] = $id;
                $testInnerInfo['test_name'] = $test;
                $testInnerInfo['sample_type'] = $sample_type;
                $testInnerInfo['test_id'] = $test_id;
                $testInnerInfo['price'] = $price;
                $testInnerInfo['discounted_price'] = $discounted_price;
                $testInnerInfo['discount'] = $discount;
                
                $testAllInfo['test_info'][] = $testInnerInfo; 
                $totalPrice = $totalPrice + $price;
                $totalDiscounted_price = $totalDiscounted_price + $discounted_price;
                
                
            } else {
               
                if(sizeof($testAllInfo) > 0){
                    
                   $testAllInfo['test_info'] =  $this->LabcenterModel_v2->unique_multidim_array($testAllInfo['test_info'],'id'); 
                   
                   
                    // print_r($testAllInfo['test_info']); die();
                    $testAllInfo['total_price'] = $totalPrice;
                    $testAllInfo['total_discounted_price'] = $totalDiscounted_price;
                    $testAllInfo['total_discount'] = $totalPrice - $totalDiscounted_price;
                    
                    $data[] = $testAllInfo; 
                }
                $testAllInfo = $testInnerInfo = array();
                $totalDiscounted_price = $totalPrice = 0;
                
                $totalPrice = $totalPrice + $price;
                $totalDiscounted_price = $totalDiscounted_price + $discounted_price;
                
                $testAllInfo['vendor_name'] = $name;
                $testAllInfo['vendor_image'] = "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$profile_pic;
                $testAllInfo['address1'] = $address1;
                $testAllInfo['address2'] = $address2;
                $testAllInfo['pincode'] = $pincode;
                $testAllInfo['city'] = $city;
                $testAllInfo['state'] = $state;
                
                $testAllInfo['contact_no'] = $contact_no;
                $testAllInfo['latitude'] = $latitude;
                $testAllInfo['longitude'] = $longitude;
                
                $testAllInfo['vendor_id'] = $user_id;
                $testAllInfo['timings'] = $timings;
                $testAllInfo['status'] =$status;
                
                $testAllInfo['recommended'] = $recommended;
                $testAllInfo['certified'] = $certified;
                $testAllInfo['mba'] = $mba;
                 
                // $testAllInfo['total_price'] = $totalPrice;
                // $testAllInfo['total_discounted_price'] = $totalDiscounted_price;
               
                $rating_query = $this->db->query("SELECT sum(labcenter_review.rating) as rating, count(labcenter_review.rating) as total_view FROM `labcenter_review` WHERE labcenter_review.labcenter_id='$user_id'");
                $rating_coun = $rating_query->num_rows();
                if($rating_coun > 0)
                {
                $rating_counting = $rating_query->row()->total_view;
                if($rating_counting > 0)
                {
                $rating_view = $rating_query->row()->rating;
                $rating_count=$rating_view/$rating_counting;
                }
                else
                {
                    $rating_count=0; 
                }
                }
                else
                {
                   $rating_count=0; 
                }
                $testAllInfo['rating']=  number_format($rating_count,1); 
                
                $testInnerInfo['id'] = $id;
                $testInnerInfo['test_name'] = $test;
                $testInnerInfo['sample_type'] = $sample_type;
                $testInnerInfo['test_id'] = $test_id;
                $testInnerInfo['price'] = $price;
                $testInnerInfo['discounted_price'] = $discounted_price;
                $testInnerInfo['discount'] = $discount;
                $testAllInfo['test_info'][] = $testInnerInfo; 
                
                
            }
            $oldVendorId = $t['user_id'];
            
        }
        if(sizeof($testAllInfo) > 0){
            $testAllInfo['test_info'] =  $this->LabcenterModel_v2->unique_multidim_array($testAllInfo['test_info'],'id'); 
                   
            $testAllInfo['total_price'] = $totalPrice;
            $testAllInfo['total_discounted_price'] = $totalDiscounted_price;
            $testAllInfo['total_discount'] = $totalPrice - $totalDiscounted_price;
            $data[] = $testAllInfo; 
        }
        //  print_r($data); die();
        
        //get $packageData
        
        $contact_no = $latitude = $longitude = "";
        $testAnd = explode(',', $given_test_id);
        $where1 = "";
        $where2 = "";
        $where3 = "";
        $i1 = 0;
        $i2 = 0;
        $i3 = 0;
        foreach($testAnd as $t){
            if($i1 == 0){
                $where1 .= "`pathology_test` LIKE '%$t%'";
            } else {
                 $where1 .= " AND `pathology_test` LIKE '%$t%'";
            }
            $i1++;
        }
        foreach($testAnd as $t){
            if($i2 == 0){
                $where2 .= "`diagnostic_test` LIKE '%$t%'";
            } else {
                 $where2 .= " AND `diagnostic_test` LIKE '%$t%'";
            }
            $i2++;
        }
        foreach($testAnd as $t){
            if($i3 == 0){
                $where3 .= "`fnac_test` LIKE '%$t%'";
            } else {
                 $where3 .= " AND `fnac_test` LIKE '%$t%'";
            }
            $i3++;
        }
        
        $where = '('. $where1 .') OR ('. $where2 .') OR ('.$where3 .')';
        // $packages = $this->db->query("SELECT * FROM `lab_packages1` WHERE `pathology_test` BETWEEN $testAnd OR `diagnostic_test` BETWEEN $testAnd OR `fnac_test` BETWEEN $testAnd")->result_array();
        $packages = $this->db->query("SELECT * FROM `lab_packages1` WHERE `v_id` = 10 AND  $where ")->result_array();
        // SELECT * FROM `lab_packages1` WHERE `pathology_test` LIKE '%3%' AND `pathology_test` LIKE '%2%'

        foreach($packages as $d){
            
            $timings = array();
            $discount = $d['discount'];
            $Price = $d['Price'];
            $discount_type = $d['discount_type'];
            $discounted_price = 0;
            
            
            if($discount > 0){
                $discounted_price = $this->LabcenterModel_v2->get_disounted_rate($Price,$discount_type,$discount);
            } else {
                $discounted_price = strval($Price); 
            }
            
            
            $user_id = $d['user_id'];
            $user_name = $this->db->query("SELECT `lab_name` as name,profile_pic,recommended , certified, mba, opening_hours,`address1`,`address2`,contact_no,latitude,longitude,`pincode`,`city`,`state` FROM `lab_center` WHERE `user_id` = '$user_id' AND `is_active` = 1 ")->row_array();
            if(empty($user_name['name'])){
                $user_name = $this->db->query("SELECT `lab_name` as name,profile_pic,recommended , certified, mba, opening_hours,`address1`,`address2`,contact_no,latitude,longitude,`pincode`,`city`,`state` FROM `lab_center` WHERE `branch_user_id` = '$user_id' AND `is_active` = 1 ")->row_array();
            }
            if(empty($user_name['name'])){
                
                $vendor_name = "";
                $profile_pic = "";
                $address1 = "";
                $address2 = "";
                $pincode = "";
                $city = "";
                $state = "";
                $recommended = "0";
                $certified = "0";
                $mba = "0";
                
                
            } else {
                $vendor_name = $user_name['name'];
                $profile_pic = "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$user_name['profile_pic'];
                $address1 = $user_name['address1'];
                $address2 = $user_name['address2'];
                $pincode = $user_name['pincode'];
                $city = $user_name['city'];
                $state = $user_name['state'];
                
                $recommended = $user_name['recommended'];
                $certified = $user_name['certified'];
                $mba = $user_name['mba'];
                
                $contact_no = $user_name['contact_no'];
                $latitude = $user_name['latitude'];
                $longitude = $user_name['longitude'];
                
                 if(empty($user_name['opening_hours'])){
                    $timings = array();
                    // echo $user_id; die();
                    
                } else {
                    $open_timings = $user_name['opening_hours'];
                      $lab_hours  = $this->LabcenterModel_v2->get_timings($open_timings);
                      
                       $status = $lab_hours['status'];
                        $timings = $lab_hours['timings'];
            
                }
                
                
            }
             
            $rating_query = $this->db->query("SELECT sum(labcenter_review.rating) as rating, count(labcenter_review.rating) as total_view FROM `labcenter_review` WHERE labcenter_review.labcenter_id='$user_id'");
                $rating_coun = $rating_query->num_rows();
                if($rating_coun > 0)
                {
                $rating_counting = $rating_query->row()->total_view;
                if($rating_counting > 0)
                {
                $rating_view = $rating_query->row()->rating;
                $rating_count=$rating_view/$rating_counting;
                }
                else
                {
                    $rating_count=0; 
                }
                }
                else
                {
                   $rating_count=0; 
                }
              
                
            $d['vendor_name'] = $vendor_name ;
            $d['vendor_image'] = $profile_pic;
            
            $d['address1'] = $address1;
            $d['address2'] = $address2;
            $d['pincode'] = $pincode;
            $d['city'] = $city;
            $d['state'] = $state;
            
            $d['contact_no'] = $contact_no;
            $d['latitude'] = $latitude;
            $d['longitude'] = $longitude;
            
            
            
            



            $d['timings'] = $timings;
            $d['status'] = $status;
            
          //  print_r($d); die(); 
            
            foreach($d as $key => $value){
                if($value == null && !is_array($value)){
                    $d[$key] = "";
                }
            }
            $d['recommended'] = $recommended;
            $d['certified'] = $certified;
            $d['mba'] = $mba;
            $d['rating']=   number_format($rating_count,1);
            $d['discounted_price'] = $discounted_price;
            $packageData[] = $d;
        }
        
        
         if($per_page != 0 && $page_no != 0){
             $last_page = ceil($data_count/$per_page);
        } else {
            $per_page = $data_count;
        }
       
            
            
        $finalData['data_count'] = $data_count;
        $finalData['per_page'] = $per_page;
        $finalData['current_page'] = $current_page;
        $finalData['first_page'] = $first_page;
        $finalData['last_page'] = $last_page;
        $finalData['packages'] = $finalData['tests'] = array();
        
       
        
        $finalData['tests'] = $data;
        $finalData['packages'] = $packageData;        
        
        
        
        return $finalData;
    }
    
    public function get_tests_by_ids($test_ids){
        
    }
    
    
    // from old labcenter
    public function labcenter_list($lat, $lng, $user_id, $category_id, $hospital_type,$per_page,$page_no,$featured,$recommended,$certified,$mba)
    {
        // die();
        $is_thyrocare  = $dataTotalCount = 0;
        $todayDay = date("l");
        $recommendedWhere = $certifiedWhere = $mbaWhere = $featuredWhere = $catWhere = $store_open = "";
        $store_close = "";
        $open_close_status = "";
        // echo $todayDay; die();
        
        if($category_id != ""){
            $catWhere = "AND FIND_IN_SET($category_id, cat_id)";
        } else {
            $catWhere = "";
        }
        
        if($featured == 1){
            $featuredWhere = "AND `is_featured` = 1"; 
        } else {
            $featuredWhere = ""; 
        }
        
        // $recommended
        
        if($recommended == 1){
            $recommendedWhere = "AND `recommended` = 1"; 
        } else {
            $recommendedWhere = ""; 
        }
        
        // $certified
        
        if($certified == 1){
            $certifiedWhere = "AND `certified` = 1"; 
        } else {
            $certifiedWhere = ""; 
        }
        
        // $mba
        if($mba == 1){
            $mbaWhere = "AND `mba` = 1"; 
        } else {
            $mbaWhere = ""; 
        }
        
        
        
    //   "LIMIT $per_page OFFSET $offset";
        
        if($per_page != "" && $page_no != ""){
            $offset = $per_page*($page_no - 1);
            
            $wherePagination  = "LIMIT $per_page OFFSET $offset";
        } else {
            $wherePagination = "";
            $page_no = 1;
            // $per_page = $count;
        }
        
        // $per_page
        // $page_no

        
        if($hospital_type == ''){
            $radius = '5';
            // asdasd
            
            $sql    = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_name`,`address1`,`address2`, `pincode`, `city`, `state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`,`user_discount`, IFNULL(delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE  `is_active` = 1  $catWhere $featuredWhere $recommendedWhere $certifiedWhere $mbaWhere ORDER BY distance $wherePagination", ($lat), ($lng), ($lat));
            
            $query  = $this->db->query($sql);
            $count  = $query->num_rows();
            
            
            $getDataCount    = sprintf("SELECT `id`, IFNULL(delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE  `is_active` = 1  $catWhere $featuredWhere $recommendedWhere $certifiedWhere $mbaWhere ORDER BY distance", ($lat), ($lng), ($lat));
            $queryCount  = $this->db->query($getDataCount)->result_array();
            //  print_r($queryCount); die();
           
            $dataTotalCount = sizeof($queryCount);
            
            // echo $dataTotalCount; die();
        }else{
            $radius = '5000';
            $sql    = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_name`,`address1`,`address2`, `pincode`, `city`, `state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`,`user_discount`, IFNULL(delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE  `is_active` = 1 AND is_vendor = $hospital_type $featuredWhere $recommendedWhere $certifiedWhere $mbaWhere  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($lat), ($lng), ($lat), ($radius));
            $query  = $this->db->query($sql);
            $count  = $query->num_rows(); 
            $dataTotalCount = $count;
        }
       
        
        
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                // print_r($row); die();
                
               
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
                $opening_hours     = $row['opening_hours'];
                $lat               = $row['latitude'];
                $lng               = $row['longitude'];
                $listing_type      = '10';
                $rating            = '4.0';
                $profile_views     = '';
                $reviews           = '1000';
                $user_discount     = $row['user_discount'];
                $image             = $row['profile_pic'];
                
                
                // if lab is thyrocare then call thyrocare APIs 
                if($labcenter_user_id == 4684){
                    $is_thyrocare = 1;
                }else {
                    $is_thyrocare = 0;
                }
                
               // $image             = 'https://d2c8oti4is0ms3.cloudfront.net/images/labcenter_images/' . $image;
               $image1             = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                $features_array    = array();
                $feature_query     = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $features . "')");
                foreach ($feature_query->result_array() as $get_list) {
                    $feature          = $get_list['feature'];
                    $features_array[] = array(
                        "name" => $feature
                    );
                }
                $final_Day      = array();
                $day_array_list = explode('|', $opening_hours);
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
                                for ($l = 0; $l < count($time_list1); $l++) {
                                    $tl1 = $time_list1[$l];
                                    $tl2 = $time_list2[$l];
                                    $time_check        = $time_list1[$l] . '-' . $time_list2[$l];
                                    $time[]            = str_replace('close-close', 'close', $time_check);
                                    $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                    $system_end_time   = date("H.i", strtotime($time_list2[$l]));
                                    $current_time      = date('H.i');
                                    if ($current_time > $system_start_time && $current_time < $system_end_time) {
                                        $ocs = $open_close[] = 'open';
                                        
                                    } else {
                                        $ocs = $open_close[] = 'close';
                                        
                                    }
                                }
                            }
                        }
                        $final_Day[] = array(
                            'day' => $day_list[0],
                            'time' => $time,
                            'status' => $open_close
                        );
                        
                        if($todayDay == $day_list[0]){
                            $store_open = $tl1;
                            $store_close = $tl2;
                            $open_close_status = $ocs;
                        }
                        
                    }
                } else {
                    $final_Day[] = array(
                        'day' => 'close',
                        'time' => array(),
                        'status' => array()
                    );
                }
                $current_day = "";
                $followers   = $this->db->select('id')->from('follow_user')->where('parent_id', $labcenter_user_id)->get()->num_rows();
                $following   = $this->db->select('id')->from('follow_user')->where('user_id', $labcenter_user_id)->get()->num_rows();
                $is_follow   = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $labcenter_user_id)->get()->num_rows();
               
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                $package_list  = array();
                $package_query = $this->db->query("SELECT * FROM `lab_packages1`  WHERE  `v_id` = 10 AND user_id='$labcenter_user_id' and branch_id ='' order by id asc");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package_id      = $package_row['id'];
                        $package_name    = $package_row['package_name'];
                        $package_details = $package_row['package_details'];
                        $price           = $package_row['Price'];
                        $image           = $package_row['image'];
                        $home_delivery   = $package_row['home_delivery'];
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        
                        $Price = $package_row['Price'];
                        $discount_type = $package_row['discount_type'];
                        $discount = $package_row['discount'];
                        $full_body_checkup  = $package_row['full_body_checkup'];
                         if($discount > 0){
                            $discounted_price = $this->LabcenterModel_v2->get_disounted_rate($Price,$discount_type,$discount);
                        } else {
                            $discounted_price = strval($Price); 
                        }
                        
                        $package_list[]  = array(
                            'package_id' => $package_id,
                            'package_name' => $package_name,
                            'package_details' => $package_details,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'discount_type' => $discount_type,
                            'discount' => $discount,
                            'full_body_checkup' => $full_body_checkup,
                            'home_delivery' => $home_delivery
                        );
                    }
                } else {
                    $package_list = array();
                }
                $branch_list  = array();
                // $branch_query = $this->db->query("SELECT * FROM `lab_center_branch` WHERE user_id='$labcenter_user_id' order by id asc");
                $branch_query = $this->db->query("SELECT lc.*,u.multi_user_type FROM `lab_center` as lc left join users as u on(lc.`branch_user_id` = u.id) WHERE lc.user_id='$labcenter_user_id' AND lc.`is_active` = 1 AND u.multi_user_type = 'branch' order by id asc");
                // echo "SELECT * FROM `lab_center_branch` WHERE user_id='$labcenter_user_id' order by id asc"; die();
                $branch_count = $branch_query->num_rows();
                if ($branch_count > 0) {
                    foreach ($branch_query->result_array() as $branch_row) {
                        $branch_id                              = $branch_row['id'];
                        $branch_listing_id                      = $branch_row['branch_user_id'];
                        $branch_path_pick_up                    = $branch_row['path_pick_up'];
                        $branch_path_sample_pickup_price_option = $branch_row['path_sample_pickup_price_option'];
                        $branch_path_sample_pickup_price        = $branch_row['path_sample_pickup_price'];
                        $branch_path_delivery_charge            = $branch_row['path_delivery_charge'];
                        $branch_path_delivery_price_options     = $branch_row['path_delivery_price_option'];
                        $branch_path_sample_delivery_price      = $branch_row['path_sample_delivery_price'];
                        $branch_diag_delivery_charge            = $branch_row['diag_delivery_charge'];
                        $branch_diag_delivery_price_option      = $branch_row['diag_delivery_price_option'];
                        $branch_diag_sample_delivery_price      = $branch_row['diag_sample_delivery_price'];
                        $branch_details                         = $branch_row['fnac_pick_up'];
                        /* $branch_details = $branch_row['fnac_sample_pickup_price_option '];
                        $branch_details = $branch_row['fnac_sample_pickup_price '];
                        $branch_details = $branch_row['fnac_delivery_charge'];
                        $branch_details = $branch_row['fnac_sample_delivery_price_option'];
                        $branch_details = $branch_row['fnac_sample_delivery_price '];*/
                        $branch_reg_date                        = $branch_row['reg_date'];
                        $branch_lab_branch_name                 = $branch_row['lab_name'];
                        /*$branch_packages = $branch_row['packages'];*/
                        $branch_store_manager                   = $branch_row['store_manager'];
                        $branch_latitude                        = $branch_row['latitude'];
                        $branch_map_location                    = $branch_row['map_location'];
                        $branch_longitude                       = $branch_row['longitude'];
                        $branch_address1                        = $branch_row['address1'];
                        $branch_address2                        = $branch_row['address2'];
                        $branch_pincode                         = $branch_row['pincode'];
                        $branch_features                        = $branch_row['features'];
                        $branch_city                            = $branch_row['city'];
                        $branch_state                           = $branch_row['state'];
                        $branch_contact_no                      = $branch_row['contact_no'];
                        $branch_whatsapp_no                     = $branch_row['whatsapp_no'];
                        $branch_email                           = $branch_row['email'];
                        $branch_profile_pic                     = $branch_row['profile_pic'];
                        $branch_about_us                        = $branch_row['about_us'];
                        $branch_reach_area                      = $branch_row['reach_area'];
                        $branch_distance                        = $branch_row['distance'];
                        $branch_store_since                     = $branch_row['store_since'];
                        $branch_opening_hours                   = $branch_row['opening_hours'];
                        $branch_home_deliverys                  = $branch_row['home_delivery'];
                        $branch_branch_profile                  = $branch_row['profile_pic'];
                        $branch_delivery_charges                = $branch_row['delivery_charges'];
                        $branch_payment_type                    = $branch_row['payment_type'];
                        $branch_discount                        = $branch_row['discount'];
                        $branch_services                        = $branch_row['terms_condition'];
                        $branch_home_visit                      = $branch_row['home_delivery'];
                        $branch_features_array                  = array();
                        $feature_query                          = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $branch_features . "')");
                        foreach ($feature_query->result_array() as $get_list) {
                            $feature                 = $get_list['feature'];
                            $branch_features_array[] = array(
                                "name" => $feature
                            );
                        }
                        $branch_final_Day      = array();
                        $day_array_list_branch = explode('|', $branch_opening_hours);
                        if (count($day_array_list_branch) > 1) {
                            for ($i = 0; $i < count($day_array_list_branch); $i++) {
                                $day_list = explode('>', $day_array_list_branch[$i]);
                                for ($j = 0; $j < count($day_list); $j++) {
                                    $day_time_list = explode('-', $day_list[$j]);
                                    for ($k = 1; $k < count($day_time_list); $k++) {
                                        $time_list1 = explode(',', $day_time_list[0]);
                                        $time_list2 = explode(',', $day_time_list[1]);
                                        $time       = array();
                                        $open_close = array();
                                        for ($l = 0; $l < count($time_list1); $l++) {
                                            $time_check        = $time_list1[$l] . '-' . $time_list2[$l];
                                            $time[]            = str_replace('close-close', 'close', $time_check);
                                            $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                            $system_end_time   = date("H.i", strtotime($time_list2[$l]));
                                            $current_time      = date('H.i');
                                            if ($current_time > $system_start_time && $current_time < $system_end_time) {
                                                $open_close[] = 'open';
                                            } else {
                                                $open_close[] = 'close';
                                            }
                                        }
                                    }
                                }
                                $branch_final_Day[] = array(
                                    'day' => $day_list[0],
                                    'time' => $time,
                                    'status' => $open_close
                                );
                            }
                        } else {
                            $branch_final_Day[] = array(
                                'day' => 'close',
                                'time' => array(),
                                'status' => array()
                            );
                        }
                        $current_day         = "";
                        $package_branch_list = array();
                        $package_query       = $this->db->query("SELECT * FROM `lab_packages1`  WHERE `v_id` = 10 AND user_id='$labcenter_user_id' and branch_id='$branch_id' order by id asc");
                        $package_count       = $package_query->num_rows();
                        if ($package_count > 0) {
                            foreach ($package_query->result_array() as $package_row) {
                                // print_r($package_row); die();
                                $package_id            = $package_row['id'];
                                $package_name          = $package_row['package_name'];
                                $package_details       = $package_row['package_details'];
                                $price                 = $package_row['Price'];
                                $image                 = $package_row['image'];
                                $home_delivery         = $package_row['home_delivery'];
                                //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                                $package_branch_list[] = array(
                                    'package_id' => $package_id,
                                    'package_name' => $package_name,
                                    'package_details' => $package_details,
                                    'price' => $price,
                                    'home_delivery' => $home_delivery
                                );
                            }
                        } else {
                            $package_branch_list = array();
                        }
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        $branch_list[] = array(
                            'branch_id' => $branch_id,
                            'branch_path_pick_up' => $branch_path_pick_up,
                            'branch_path_sample_pickup_price_option' => $branch_path_sample_pickup_price_option,
                            'branch_path_sample_pickup_price' => $branch_path_sample_pickup_price,
                            'branch_path_delivery_charge' => $branch_path_delivery_charge,
                            'branch_path_delivery_price_options' => $branch_path_delivery_price_options,
                            'branch_path_sample_delivery_price' => $branch_path_sample_delivery_price,
                            'branch_diag_delivery_charge' => $branch_diag_delivery_charge,
                            'branch_diag_delivery_price_option' => $branch_diag_delivery_price_option,
                            'branch_diag_sample_delivery_price' => $branch_diag_sample_delivery_price,
                            'branch_details' => $branch_details,
                            'branch_reg_date' => $branch_reg_date,
                            'branch_lab_branch_name' => $branch_lab_branch_name,
                            'branch_store_manager' => $branch_store_manager,
                            'branch_latitude' => $branch_latitude,
                            'branch_map_location' => $branch_map_location,
                            'branch_longitude' => $branch_longitude,
                            'branch_address1' => $branch_address1,
                            'branch_address2' => $branch_address2,
                            'branch_pincode' => $branch_pincode,
                            'branch_features' => $branch_features_array,
                            'branch_city' => $branch_city,
                            'branch_state' => $branch_state,
                            'branch_contact_no' => $branch_contact_no,
                            'branch_whatsapp_no' => $branch_whatsapp_no,
                            'branch_email' => $branch_email,
                            'branch_opening_day' => $branch_final_Day,
                            'branch_profile_pic' => $branch_profile_pic,
                            'branch_about_us' => $branch_about_us,
                            'branch_reach_area' => $branch_reach_area,
                            'branch_distance' => $branch_distance,
                            'branch_store_since' => $branch_store_since,
                            'branch_package' => $package_branch_list
                        );
                    }
                } else {
                    $branch_list = array();
                }
                $test_in_lab_list = array();
                $test_in_home_list = array();
                // $test_query       = $this->db->query("SELECT * FROM `lab_test_details` WHERE user_id='$labcenter_user_id' order by id asc");
                // $test_query       = $this->db->query("SELECT * FROM `lab_test_details1` WHERE user_id='$labcenter_user_id' order by id asc");
                $test_query       = $this->db->query("SELECT ltd.*, lat.test FROM `lab_test_details1` as ltd left join lab_all_test1 as lat on (ltd.test_id = lat.id) WHERE user_id='$labcenter_user_id' order by id asc");
                
                // echo "SELECT * FROM `lab_test_details1` WHERE user_id='$labcenter_user_id' order by id asc"; die();
                

                $test_count       = $test_query->num_rows();
                if ($test_count > 0) {
                    foreach ($test_query->result_array() as $test_row) {
                        // print_r($test_row); die();
                        $user_id        = $test_row['user_id'];
                        $test           = $test_row['test'];
                        $test_id        = $test_row['test_id'];
                        $price          = $test_row['price'];
                        $offer          = $test_row['offer'];
                        $executive_rate = $test_row['discounted_price'];
                        $home_delivery  = $test_row['home_delivery'];
                        if ($home_delivery == "0") {
                            $test_in_lab_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test,
                                'test' => $test_id,
                                'price' => $price,
                                'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => "0"
                            );
                        } else {
                            $test_in_home_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test_id,
                                'test' => $test,
                                'price' => $price,
                                'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => "1"
                            );
                        }
                    }
                } else {
                    $test_in_lab_list  = array();
                    $test_in_home_list = array();
                }
                
                 $query_count = $this->db->query("SELECT count(user_id) as total_view FROM profile_views_master WHERE listing_id='$labcenter_user_id' ");
                    $TTL_View = $query_count->row()->total_view;
        
        
                //Review Count
                //echo "SELECT * FROM `labcenter_review` WHERE labcenter_id='$id'";
                //$Review_query = $this->db->query("SELECT * FROM `labcenter_review` WHERE labcenter_id='$id'");
                $Review_query = $this->db->query("SELECT labcenter_review.id FROM `labcenter_review` INNER JOIN `users` ON labcenter_review.user_id=users.id WHERE labcenter_review.labcenter_id='$id'");
                $Review_count = $Review_query->num_rows();
        
                $resultpost[] = array(
                    "id" => $id,
                    "is_thyrocare" => $is_thyrocare,
                    "lab_user_id" => $labcenter_user_id,
                    "name" => $lab_name,
                    "listing_type" => '10',
                    "features" => $features_array,
                    "home_delivery" => $home_delivery,
                    "delivery_charges" => $delivery_charges,
                    "address1" => $address1,
                    "address2" => $address2,
                    "pincode" => $pincode,
                    "city" => $city,
                    "exotel_no" => '02233721563',
                    "state" => $state,
                    "contact_no" => $contact_no,
                    "whatsapp_no" => $whatsapp_no,
                    "email" => $email,
                    "rating" => $rating,
                    "followers" => $followers,
                    "following" => $following,
                    "profile_views" => $TTL_View,
                    "reviews" => $Review_count,
                    "is_follow" => $is_follow,
                    "lat" => $lat,
                    "lng" => $lng,
                    'opening_day' => $final_Day,
                    "distance" => round($row['distance'],2),
                    "store_open" => $store_open,
                    "store_close" => $store_close,
                    "open_close_status" => $open_close_status,
                    "image" => $image1,
                    "user_discount" => $user_discount,
                    "package_list" => $package_list,
                    "branch_list" => $branch_list,
                    "home_test_done" => $test_in_home_list,
                    "lab_test_done" => $test_in_lab_list
                );
            }
        } else {
            $resultpost = array();
        }
        
        
        if($per_page == "" || $page_no == "" || $per_page == 0 || $page_no == 0){
             $page_no = 1;
            $per_page = $count;
        } 
        
        $last_page = ceil($dataTotalCount/$per_page);
        
        $resData['total_count'] = $dataTotalCount;
        $resData['page_no'] = $page_no;
        $resData['current_page_count'] = $per_page;
        $resData['last_page'] = $last_page;
        $resData['center_list'] = $resultpost;
        return $resData;
    }
    
    public function labcenter_details($user_id, $listing_id)
    {
        // die();
                // Labs
               $sql = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `discount`,`lab_name`,`address1`,`address2`, `pincode`, `city`, `user_discount`,`state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`, IFNULL(delivery_charges,'') AS delivery_charges FROM lab_center where user_id='$listing_id' AND `is_active` = 1");
               // $sql = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_branch_name`,`address1`,`address2`, `pincode`, `city`, `discount`,`state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`, IFNULL(delivery_charges,'') AS delivery_charges FROM lab_center_branch where user_id='$listing_id'");
                //echo "SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_branch_name`,`address1`,`address2`, `pincode`, `city`, `discount`,`state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`, IFNULL(delivery_charges,'') AS delivery_charges FROM lab_center_branch where user_id='$listing_id'";
                
                $query = $this->db->query($sql);
                $count = $query->num_rows();
          
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id = $row['id'];
                        $labcenter_user_id = $row['user_id'];
                        $lab_name = $row['lab_name'];
                        $features = $row['features'];
                        $home_delivery = $row['home_delivery'];
                        $delivery_charges = $row['delivery_charges'];
                        $address1 = $row['address1'];
                        $address2 = $row['address2'];
                        $pincode = $row['pincode'];
                        $city = $row['city'];
                        $state = $row['state'];
                        $contact_no = $row['contact_no'];
                        $whatsapp_no = $row['whatsapp_no'];
                        $email = $row['email'];
                        $opening_hours = $row['opening_hours'];
                        $lat = $row['latitude'];
                        $lng = $row['longitude'];
                        $rating = '4.0';
                        $profile_views = '1548';
                        $reviews = '1000';
                        $user_discount = $row['discount'];
                        $image = $row['profile_pic'];
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/labcenter_images/' . $image;

                        $features_array = array();
                        if($features != ''){
                            $feature_query = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $features . "')");
                            foreach ($feature_query->result_array() as $get_list) {
    
                                $feature = $get_list['feature'];
                                $features_array[] = array(
                                    "name" => $feature
                                );
                            }
                        }

                        $final_Day = array();
                        $day_array_list = explode('|', $opening_hours);
                        if (count($day_array_list) > 1) {
                            for ($i = 0; $i < count($day_array_list); $i++) {
                                $day_list = explode('>', $day_array_list[$i]);
                                for ($j = 0; $j < count($day_list); $j++) {
                                    $day_time_list = explode('-', $day_list[$j]);
                                    for ($k = 1; $k < count($day_time_list); $k++) {
                                        $time_list1 = explode(',', $day_time_list[0]);
                                        $time_list2 = explode(',', $day_time_list[1]);
                                        $time = array();
                                        $open_close = array();
                                        for ($l = 0; $l < count($time_list1); $l++) {
                                            $time_check = $time_list1[$l] . '-' . $time_list2[$l];
                                            $time[] = str_replace('close-close', 'close', $time_check);
                                            $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                            $system_end_time = date("H.i", strtotime($time_list2[$l]));
                                            $current_time = date('H.i');
                                            if ($current_time > $system_start_time && $current_time < $system_end_time) {
                                                $open_close[] = 'open';
                                            } else {
                                                $open_close[] = 'close';
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
                        $current_day = "";



                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $labcenter_user_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $labcenter_user_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $labcenter_user_id)->get()->num_rows();
                       
                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }

 //********************************************added by jakir on 3 aug 2018 for lab changes*************************************************** 
                  $package_list = array();
                $package_query = $this->db->query("SELECT * FROM `lab_packages1` WHERE `v_id` = 10 AND user_id='$labcenter_user_id' and branch_id ='' order by id asc");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package_id = $package_row['id'];
                        $package_name = $package_row['package_name'];
                        $package_details = $package_row['package_details'];
                        $price = $package_row['Price'];
                        $image = $package_row['image'];
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        
                         $price           = $package_row['Price'];
                        $image           = $package_row['image'];
                        $home_delivery   = $package_row['home_delivery'];
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        
                        $Price = $package_row['Price'];
                        $discount_type = $package_row['discount_type'];
                        $discount = $package_row['discount'];
                        $full_body_checkup  = $package_row['full_body_checkup'];
                         if($discount > 0){
                            $discounted_price = $this->LabcenterModel_v2->get_disounted_rate($Price,$discount_type,$discount);
                        } else {
                            $discounted_price = strval($Price); 
                        }
                        
                        
                        
                        //aaaaaaaaaa
                        $package_list[] = array(
                            'package_id' => $package_id,
                            'package_name' => $package_name,
                            'package_details' => $package_details,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'discount_type' => $discount_type,
                            'discount' => $discount,
                            'full_body_checkup' => $full_body_checkup,
                            'home_delivery' => $home_delivery
                        );
                    }
                } else {
                    $package_list = array();
                }
                
                $branch_list = array();
                $branch_id ="";
                // $branch_query = $this->db->query("SELECT * FROM `lab_center_branch` WHERE user_id='$labcenter_user_id' order by id asc");
                $branch_query = $this->db->query("SELECT lc.*,u.multi_user_type FROM `lab_center` as lc left join users as u on(lc.`branch_user_id` = u.id) WHERE lc.user_id='$labcenter_user_id' AND u.`is_active` = 1 AND u.multi_user_type = 'branch' order by id asc");
                
                $branch_count = $branch_query->num_rows();
                if ($branch_count > 0) {
                    foreach ($branch_query->result_array() as $branch_row) {
                        $branch_id                              = $branch_row['id'];
                        $branch_listing_id                      = $branch_row['branch_user_id'];
                        $branch_path_pick_up                    = $branch_row['path_pick_up'];
                        $branch_path_sample_pickup_price_option = $branch_row['path_sample_pickup_price_option'];
                        $branch_path_sample_pickup_price        = $branch_row['path_sample_pickup_price'];
                        $branch_path_delivery_charge            = $branch_row['path_delivery_charge'];
                        $branch_path_delivery_price_options     = $branch_row['path_delivery_price_option'];
                        $branch_path_sample_delivery_price      = $branch_row['path_sample_delivery_price'];
                        
                        $branch_diag_delivery_charge            = $branch_row['diag_delivery_charge'];
                        $branch_diag_delivery_price_option      = $branch_row['diag_delivery_price_option'];
                        $branch_diag_sample_delivery_price      = $branch_row['diag_sample_delivery_price'];
                        
                        $branch_details                         = $branch_row['fnac_pick_up'];
                       /* $branch_details = $branch_row['fnac_sample_pickup_price_option '];
                        $branch_details = $branch_row['fnac_sample_pickup_price '];
                        $branch_details = $branch_row['fnac_delivery_charge'];
                        $branch_details = $branch_row['fnac_sample_delivery_price_option'];
                        $branch_details = $branch_row['fnac_sample_delivery_price '];*/
                        
                        $branch_reg_date                = $branch_row['reg_date'];
                        $branch_lab_branch_name         = $branch_row['lab_branch_name'];
                        /*$branch_packages = $branch_row['packages'];*/
                        $branch_store_manager           = $branch_row['store_manager'];
                        $branch_latitude                = $branch_row['latitude'];
                        $branch_map_location            = $branch_row['map_location'];
                        $branch_longitude               = $branch_row['longitude'];
                        $branch_address1                = $branch_row['address1'];
                        $branch_address2                = $branch_row['address2'];
                        $branch_pincode                 = $branch_row['pincode'];
                        $branch_features                = $branch_row['features'];
                        $branch_city                    = $branch_row['city'];
                        $branch_state                   = $branch_row['state'];
                        $branch_contact_no              = $branch_row['contact_no'];
                        $branch_whatsapp_no             = $branch_row['whatsapp_no'];
                        $branch_email                   = $branch_row['email'];
                        $branch_profile_pic             = $branch_row['profile_pic'];
                        $branch_about_us                = $branch_row['about_us'];
                        $branch_reach_area              = $branch_row['reach_area'];
                        $branch_distance                = $branch_row['distance'];
                        $branch_store_since             = $branch_row['store_since'];
                        $branch_opening_hours           = $branch_row['opening_hours'];
                        $branch_home_deliverys          = $branch_row['home_delivery'];
                        $branch_branch_profile          = $branch_row['branch_profile'];
                        $branch_delivery_charges        = $branch_row['delivery_charges'];
                        $branch_payment_type            = $branch_row['payment_type'];
                        $branch_discount                = $branch_row['discount'];
                        $branch_services                = $branch_row['terms_condition'];
                        $branch_home_visit              = $branch_row['home_delivery'];
                        
                        
                        $branch_features_array = array();

                        $feature_query = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $branch_features . "')");
                        foreach ($feature_query->result_array() as $get_list) {
        
                            $feature = $get_list['feature'];
                            $branch_features_array[] = array(
                                "name" => $feature
                            );
                        }
        
                        $branch_final_Day = array();
                        $day_array_list_branch = explode('|', $branch_opening_hours);
                        if (count($day_array_list_branch) > 1) {
                            for ($i = 0; $i < count($day_array_list_branch); $i++) {
                                $day_list = explode('>', $day_array_list_branch[$i]);
                                for ($j = 0; $j < count($day_list); $j++) {
                                    $day_time_list = explode('-', $day_list[$j]);
                                    for ($k = 1; $k < count($day_time_list); $k++) {
                                        $time_list1 = explode(',', $day_time_list[0]);
                                        $time_list2 = explode(',', $day_time_list[1]);
                                        $time = array();
                                        $open_close = array();
                                        for ($l = 0; $l < count($time_list1); $l++) {
                                            $time_check = $time_list1[$l] . '-' . $time_list2[$l];
                                            $time[] = str_replace('close-close', 'close', $time_check);
                                            $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                            $system_end_time = date("H.i", strtotime($time_list2[$l]));
                                            $current_time = date('H.i');
                                            if ($current_time > $system_start_time && $current_time < $system_end_time) {
                                                $open_close[] = 'open';
                                            } else {
                                                $open_close[] = 'close';
                                            }
                                        }
                                    }
                                }
                                $branch_final_Day[] = array(
                                    'day' => $day_list[0],
                                    'time' => $time,
                                    'status' => $open_close
                                );
                            }
                        } else {
                            $branch_final_Day[] = array(
                                'day' => 'close',
                                'time' => array(),
                                'status' => array()
                            );
                        }
                        $current_day = "";
                        
                        
                        
                        $package_branch_list = array();
                        $package_query = $this->db->query("SELECT * FROM `lab_packages1` WHERE `v_id` = 10 AND user_id='$labcenter_user_id' and branch_id='$branch_id' order by id asc");
                        $package_count = $package_query->num_rows();
                        if ($package_count > 0) {
                            foreach ($package_query->result_array() as $package_row) {
                                $package_id = $package_row['id'];
                                $package_name = $package_row['lab_name'];
                                $package_details = $package_row['lab_details'];
                                $price = $package_row['Price'];
                                $image = $package_row['image'];
                                //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                                $package_branch_list[] = array(
                                    'package_id' => $package_id,
                                    'package_name' => $package_name,
                                    'package_details' => $package_details,
                                    'price' => $price
                                );
                            }
                        } else {
                            $package_branch_list = array();
                        }
                        
                        
                        
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        $branch_list[] = array(
                            'branch_id' => $branch_id,
                            'branch_path_pick_up' => $branch_path_pick_up,
                            'branch_path_sample_pickup_price_option' => $branch_path_sample_pickup_price_option,
                            'branch_path_sample_pickup_price' => $branch_path_sample_pickup_price,
                            'branch_path_delivery_charge' => $branch_path_delivery_charge,
                            'branch_path_delivery_price_options' => $branch_path_delivery_price_options,
                            'branch_path_sample_delivery_price' => $branch_path_sample_delivery_price,
                            'branch_diag_delivery_charge' => $branch_diag_delivery_charge,
                            'branch_diag_delivery_price_option' => $branch_diag_delivery_price_option,
                            'branch_diag_sample_delivery_price' => $branch_diag_sample_delivery_price,
                            'branch_details' => $branch_details,
                            'branch_reg_date' => $branch_reg_date,
                            'branch_lab_branch_name' => $branch_lab_branch_name,
                            'branch_store_manager' =>$branch_store_manager, 
                            'branch_latitude'=>$branch_latitude,
                            'branch_map_location'=>$branch_map_location,
                            'branch_longitude'=>$branch_longitude,
                            'branch_address1'=>$branch_address1,
                            'branch_address2'=>$branch_address2,
                            'branch_pincode'=>$branch_pincode,
                            'branch_features'=>$branch_features_array,
                            'branch_city'=>$branch_city,
                            'branch_state'=>$branch_state,
                            'branch_contact_no'=>$branch_contact_no,
                            'branch_whatsapp_no'=>$branch_whatsapp_no,
                            'branch_email'=>$branch_email,
                            'branch_opening_day' => $branch_final_Day,
                            'branch_profile_pic'=>$branch_profile_pic,
                            'branch_about_us'=>$branch_about_us,
                            'branch_reach_area'=>$branch_reach_area,
                            'branch_distance'=>$branch_distance,
                            'branch_store_since'=>$branch_store_since,
                            'branch_package'=>$package_branch_list
                        );
                    }
                } else {
                    $branch_list = array();
                }
                
           
                
                $test_in_lab_list = array();
                 $test_in_home_list = array();
                // $test_query       = $this->db->query("SELECT * FROM `lab_test_details` WHERE user_id='$labcenter_user_id' order by id asc");
                // $test_query       = $this->db->query("SELECT * FROM `lab_test_details1` WHERE user_id='$labcenter_user_id' order by id asc");
                $test_query       = $this->db->query("SELECT lat.test , ltd.* FROM `lab_test_details1` as ltd left join lab_all_test1 as lat on (ltd.`test_id` = lat.id) WHERE user_id='$labcenter_user_id' order by id asc");
                $test_count       = $test_query->num_rows();
                if ($test_count > 0) {
                    foreach ($test_query->result_array() as $test_row) {
                        $user_id        = $test_row['user_id'];
                        $test           = $test_row['test'];
                        $test_id        = $test_row['test_id'];
                        $price          = $test_row['price'];
                        // $offer          = $test_row['offer'];
                        // $executive_rate = $test_row['executive_rate'];
                        
                        
                        //bbbbbbbbbb
                        
                        
                         $Price = $package_row['Price'];
                        $discount_type = $package_row['discount_type'];
                        $discount = $package_row['discount'];
                        $full_body_checkup  = $package_row['full_body_checkup'];
                         if($discount > 0){
                            $executive_rate = $this->LabcenterModel_v2->get_disounted_rate($Price,$discount_type,$discount);
                        } else {
                            $executive_rate = strval($Price); 
                        }
                        
                        
                        
                      
                        
                        $home_delivery  = $test_row['home_delivery'];
                        if ($home_delivery == "0") {
                            $test_in_lab_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test,
                                'test' => $test_id,
                                'price' => $price,
                                // 'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => $home_delivery,
                                 'discount_type' => $discount_type,
                            'discount' => $discount,
                                'full_body_checkup' => $full_body_checkup,
                            );
                        } else {
                            $test_in_home_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test_id,
                                'test' => $test,
                                'price' => $price,
                                'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => "1"
                            );
                        }
                    }
                } else {
                    $test_in_lab_list  = array();
                    $test_in_home_list = array();
                }
                
                ///Lab test added by ghanshyam parihar ends

//********************************************************************************End by Jakir *******************************************
                        $resultpost[] = array(
                            "id" => $id,
                            "lab_user_id" => $labcenter_user_id,
                            "name" => $lab_name,
                            "features" => $features_array,
                            "home_delivery" => $home_delivery,
                            "delivery_charges" => $delivery_charges,
                            "address1" => $address1,
                            "address2" => $address2,
                            "pincode" => $pincode,
                            "city" => $city,
                            "state" => $state,
                            "contact_no" => $contact_no,
                            "whatsapp_no" => $whatsapp_no,
                            "exotel_no" => '02233721563',
                            "email" => $email,
                            "rating" => $rating,
                            "followers" => $followers,
                            "following" => $following,
                            "profile_views" => $profile_views,
                            "reviews" => $reviews,
                            "is_follow" => $is_follow,
                            "lat" => $lat,
                            "lng" => $lng,
                            'opening_day' => $final_Day,
                            "image" => $image,
                     "user_discount" => $user_discount,
                    "package_list"=> $package_list,
                    "branch_list"=>$branch_list,
                   // "test_list"=>$test_branch_list
                    "home_test_done" => $test_in_home_list,
                    "lab_test_done" => $test_in_lab_list
                        );
                    }
                } else {
                    $resultpost = array();
                }
            return $resultpost;
    }
    
    public function lab_booking($user_id, $listing_id, $address_line1, $address_line2, $package_id, $user_name, $mobile, $email, $gender, $branch_id, $branch_name, $vendor_id, $status, $payment_mode, $trail_booking_date, $trail_booking_time,  $booking_location, $booking_address, $booking_mobile,$test_ids, $patient_id, $at_home, $city, $state, $pincode, $address_id,$booking_id,$total_cost,$discounted_price,$amount,$coupon_id){
        date_default_timezone_set('Asia/Kolkata');
        $created_at   = date('Y-m-d H:i:s');
        //$joining_date = date('Y-m-d');
        $query        = $this->db->query("SELECT * FROM booking_master WHERE booking_id = '$booking_id' AND `vendor_id` = 10");
        $count        = $query->num_rows();
        if ($count > 0) {
            $lab_booking = array(
                'user_id' => $user_id,
                'patient_id' => $patient_id,
                'booking_id' => $booking_id,
                'package_id' => $package_id,
                'listing_id' => $listing_id,
                'user_name' => $user_name,
                'user_email' => $email,
                'user_mobile' => $mobile,
                'user_gender' => $gender,
                'branch_id' => $branch_id,
                'vendor_id' => $vendor_id,
                'booking_date' => $created_at,
                'status' => $status,
                'trail_booking_date' => $trail_booking_date,
                'trail_booking_time' => $trail_booking_time,
                'payment_mode' => $payment_mode,
                'booking_location' => $booking_location,
                'booking_address' => $booking_address,
                'booking_mobile' => $booking_mobile
            );
            $this->db->where('booking_id', $booking_id);
            $rst = $this->db->update('booking_master', $lab_booking);
           
            // if($package_id == ''){
         
            //     if ($test_ids != '') {
            //         $Testids = explode(',', $test_ids);
            //         foreach ($Testids as $tid) {
            //             $test_data = array(
            //                 'booking_id' => $booking_id,
            //                 'test_id' => $tid
            //             );
            //             $this->db->where('booking_id', $booking_id);
            //             $this->db->where('test_id', $tid);
            //             $rst       = $this->db->update('booking_test', $test_data);
            //         }
            //     }
            // }
            
            
            $insertTolabBookingDetails = array(
                'user_id'=> $user_id,
                'patient_id'=> $patient_id,
                'listing_id'=> $listing_id,
                'vendor_type'=> $vendor_id,
                'branch_id'=> $branch_id,
                'branch_name'=> $branch_name,
                'at_home'=> $at_home,
                'address_line1'=> $address_line1,
                'address_line2'=> $address_line2,
                'city'=> $city,
                'state'=> $state,
                'pincode'=> $pincode,
                'mobile_no'=>$mobile ,
                'email_id'=> $email,
                'address_id'=> $address_id,
                'test_id'=> $test_ids,
                'package_id'=> $package_id,
                'booking_date'=> $trail_booking_date,
                'booking_time'=> $trail_booking_time,
                'booking_id'=> $booking_id,
                'amount' => $discounted_price,
                'total_cost' => $total_cost,
                'total_discount' => $amount
                );
                
                // print_r($insertTolabBookingDetails); die();
            
            $this->db->where('booking_id', $booking_id);
            $rst_comp         = $this->db->update('lab_booking_details', $insertTolabBookingDetails);    
            
            //////// notification for gcm ***********************************************************************
            //added by jakir on 01-june-2018 for notification on activation 
            $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            $img_count  = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                $img_file      = $profile_query->source;
                $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            $getusr               = $user_plike->row_array();
            $usr_name             = $getusr['name'];
            $msg                  = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
            $title                = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);
                
                 $notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => "",
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => $listing_id,
                      'booking_id'  => $booking_id,
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => 'thyro_lab_booking',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
                );
                $this->db->insert('All_notification_Mobile', $notification_array);
            }
            //end
            ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = $user_name . ', Your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $user_phone,
                'Body' => $message
            );
            $exotel_sid   = "aegishealthsolutions";
            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
            $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            $http_result = curl_exec($ch);
            curl_close($ch);
            /////////*******************************************excotel text message vendor ***************************************************
            $vendor_info  = $this->db->select('phone,name,token,token_status,agent,web_token')->from('users')->where('id', $listing_id)->get()->row();
            $token_status = $vendor_info->token_status;
            $vendor_phone = $vendor_info->phone;
            $vendor_name  = $vendor_info->name;
            $message      = $vendor_name . ', Your lab package has been booked by patient ' . $user_name .' successfully and Booking Id is ' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $vendor_phone,
                'Body' => $message
            );
            $exotel_sid   = "aegishealthsolutions";
            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
            $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            $http_result = curl_exec($ch);
            curl_close($ch);
            
            // For lab center vendor website firebase push notification by ghanshyam parihar starts :: Added date: 05 Feb 2019
                $web_token      = $vendor_info->web_token;
                $title_web      = $user_name.' has booked an package from '.$vendor_name;
                $img_url        = $userimage;
                // $img_url      = 'https://medicalwale.s3.amazonaws.com/images/img/medical_logo.png';
                $tag            = 'text';
                $agent          = $vendor_info->agent;;
                $connect_type   = 'lab_booking';
                // Web notification FCM function by ghanshyam parihar date:4 Feb 2019
                $click_action = 'https://labs.medicalwale.com/booking_controller/booking_appointment/'.$listing_id;
                // $click_action = 'https://vendor.sandbox.medicalwale.com/labs/booking_controller/booking_appointment/'.$listing_id;
                $this->send_gcm_web_notify($title_web, $message, $web_token, $img_url, $tag, $agent, $connect_type,$click_action);
                // Web notification FCM function by ghanshyam parihar date:4 Feb 2019 Ends
            // For lab center vendor website firebase push notification by ghanshyam parihar ends :: Added date: 05 Feb 2019
            
            if ($rst) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id'=>$booking_id
                );
            }
        } else {
            $lab_booking = array(
                'user_id' => $user_id,
                'patient_id' => $patient_id,
                'booking_id' => $booking_id,
                'package_id' => $package_id,
                'listing_id' => $listing_id,
                'user_name' => $user_name,
                'user_email' => $email,
                'user_mobile' => $mobile,
                'user_gender' => $gender,
                'branch_id' => $branch_id,
                'vendor_id' => $vendor_id,
                'booking_date' => $created_at,
                'status' => $status,
                'trail_booking_date' => $trail_booking_date,
                'trail_booking_time' => $trail_booking_time,
                'payment_mode' => $payment_mode,
                'booking_location' => $booking_location,
                'booking_address' => $booking_address,
                'booking_mobile' => $booking_mobile
            );
            $rst         = $this->db->insert('booking_master', $lab_booking);
            $appointment_id = $this->db->insert_id();
            if($package_id == ''){
                if ($test_ids != '') {
                    $Testids = explode(',', $test_ids);
                    foreach ($Testids as $tid) {
                        $test_data = array(
                            'booking_id' => $booking_id,
                            'test_id' => $tid
                        );
                        $rst = $this->db->insert('booking_test', $test_data);
                    }
                }
            }
            
            $insertTolabBookingDetails = array(
                'user_id'=> $user_id,
                'patient_id'=> $patient_id,
                'listing_id'=> $listing_id,
                'vendor_type'=> $vendor_id,
                'branch_id'=> $branch_id,
                'branch_name'=> $branch_name,
                'at_home'=> $at_home,
                'address_line1'=> $address_line1,
                'address_line2'=> $address_line2,
                'city'=> $city,
                'state'=> $state,
                'pincode'=> $pincode,
                'mobile_no'=>$mobile ,
                'email_id'=> $email,
                'address_id'=> $address_id,
                'test_id'=> $test_ids,
                'package_id'=> $package_id,
                'booking_date'=> $trail_booking_date,
                'booking_time'=> $trail_booking_time,
                'booking_id'=> $booking_id,
                'amount' => $discounted_price,
                'total_cost' => $total_cost,
                'total_discount' => $amount
                );
        
            $rst_comp         = $this->db->insert('lab_booking_details', $insertTolabBookingDetails);  
            $this->insert_notification_post_question($user_id, $appointment_id,$listing_id);
            
            // coupon code used
            
            if($coupon_id=="0" || empty($coupon_id)){}
            else{
                $user_data_dh    = array(
                       'use_status' => 1
                       );
                       $updateStatus = $this->db->where('coupon',$coupon_id)->update('use_coupon', $user_data_dh);
            }
            //////// notification for gcm ***********************************************************************
            //added by jakir on 01-june-2018 for notification on activation 
            $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            $img_count  = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                $img_file      = $profile_query->source;
                $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            $getusr               = $user_plike->row_array();
            $usr_name             = $getusr['name'];
            $msg                  = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token       = $this->db->query("SELECT email,token,agent, token_status FROM users WHERE id = '$user_id'");
            $title                = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $user_email   = $token_status['email'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);
                
                  $notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => "",
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => $listing_id,
                      'booking_id'  => $booking_id,
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => 'thyro_lab_booking',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
                );
                $this->db->insert('All_notification_Mobile', $notification_array);
                if(!empty($user_email))
                {
                    $this->lab_booking_sendmail($user_email, $user_name, $booking_id);
                }
                
            }
            //end
            ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $user_phone,
                'Body' => $message
            );
            $exotel_sid   = "aegishealthsolutions";
            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
            $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            $http_result = curl_exec($ch);
            curl_close($ch);
            /////////*******************************************excotel text message vendor ***************************************************
            $vendor_info  = $this->db->select('phone,name,token,token_status,agent,web_token')->from('users')->where('id', $listing_id)->get()->row();
            $token_status = $vendor_info->token_status;
            $vendor_phone = $vendor_info->phone;
            $vendor_name  = $vendor_info->name;
            $message      = $vendor_name . ', Your lab package has been booked by patient ' . $user_name . ' successfully and Booking Id is ' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $vendor_phone,
                'Body' => $message
            );
            $exotel_sid   = "aegishealthsolutions";
            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
            $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            $http_result = curl_exec($ch);
            curl_close($ch);
            
            
            // For lab center vendor website firebase push notification by ghanshyam parihar starts date: 05 Feb 2019
                $web_token      = $vendor_info->web_token;
                $title_web      = $user_name.' has booked an package from '.$vendor_name;
                $img_url        = $userimage;
                // $img_url      = 'https://medicalwale.s3.amazonaws.com/images/img/medical_logo.png';
                $tag            = 'text';
                $agent          = $vendor_info->agent;;
                $connect_type   = 'lab_booking';
                
                // Web notification FCM function by ghanshyam parihar date:4 Feb 2019
                $click_action = 'https://labs.medicalwale.com/booking_controller/booking_appointment/'.$listing_id;
                // $click_action = 'https://vendor.sandbox.medicalwale.com/labs/booking_controller/booking_appointment/'.$listing_id;
                $this->send_gcm_web_notify($title_web, $message, $web_token, $img_url, $tag, $agent, $connect_type,$click_action);
                // Web notification FCM function by ghanshyam parihar date:4 Feb 2019 Ends
            
            // For lab center vendor website firebase push notification by ghanshyam parihar ends date: 05 Feb 2019
            
            if ($rst) {
                $remove_all = 1;
                $stack_id = 0;
                $this->UserstackModel->delete_stack($user_id,$stack_id,$remove_all);
                
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id'=>$booking_id
                );
            }
        }
    }
    
    
     function send_gcm_web_notify($title, $msg, $reg_id, $img_url, $tag, $agent, $type,$click_action) {
            date_default_timezone_set('Asia/Kolkata');
            $date = date('j M Y h:i A');
            if (!defined("GOOGLE_GCM_URL"))
                define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'notification' : 'notification' => array(
                     "title"=> $title,
                     "body" => $msg,
                     "click_action"=> $click_action,
                     "icon"=> $img_url
                )
            );
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AAAASZs-aSM:APA91bEQ0SZFS7YnRRK95O2Jp_PdzVC8t_osgDK0UdWXeQRo06fEedknj2b26yfX_YPkLq85h0lc3hUBlnSSPD1qvfikzFYiHd8tlt7BeuOg3f5WRwQxmz19WbEGbZEX894zZEF5iRO5' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
            );
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
    // Web notification FCM function by ghanshyam parihar date:16 Jan 2019 Ends
    public function lab_booking_sendmail($user_email, $user_name, $booking_id)
    {
        
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
                                    <p><font style="font-size:14px;color:#fff;line-height: 20px;" face="arial,sans-serif" >' .$user_name . ', your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference. </font></p>
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
                                                <a href="#" target="_blank" style="color: #656060;text-decoration: none;">' . $user_name . '</a></font>
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
     public function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent){
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        if (!defined("GOOGLE_GCM_URL"))
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        $fields  = array(
            'to' => $reg_id,
            'priority' => "high",
            $agent === 'android' ? 'data' : 'notification' => array(
                "title" => $title,
                "message" => $msg,
                "notification_image" => $img_url,
                "tag" => $tag,
                'sound' => 'default',
                "notification_type" => 'thyro_lab_booking',
                "notification_date" => $date
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        );
        $ch      = curl_init();
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
   
    // end from old labcenter
    
    public function lab_bookings_history($user_id){
        $data = array();
        
        $bookings = $this->db->query("SELECT * FROM `booking_master` WHERE `user_id` = '$user_id' AND `vendor_id` = 10")->result_array();
        foreach($bookings as $b){
            $booking_id = $b['booking_id'];
            if(!empty($booking_id)){
                $booking_det = $this->db->query("SELECT * FROM `lab_booking_details` WHERE `booking_id` = '$booking_id'")->result_array();
                
                foreach($booking_det as $booked){
                    foreach($booked as $k => $v){
                        if($v == null){
                            $booked[$k] = "";
                        } else {
                            $booked[$k] = $v;
                        }
                    }
                    $booking_detail[] = $booked;
                }
                
                foreach($b as $k => $v){
                    if($v == null){
                        $b[$k] = "";
                    } else {
                        $b[$k] = $v;
                    }
                }
                    
                $b['details'] = $booking_detail;
                $booking_details[] = $b;                     
                
                
            }
        }
        $data = $booking_details;
        return $data;
    }
    
    // Web notification FCM function by ghanshyam parihar date:16 Jan 2019 Ends
    
   public function insert_notification_post_question($user_id, $booking_id, $doctor_id){
      $data = array(
                           'user_id'        => $user_id,
                           'post_id'         => $booking_id,
                           'timeline_id'   => $user_id,
                           'type'          => 'Appointment',
                           'seen'          => '1',
                           'notified_by'   => $doctor_id,
                           'description'   => 'Appointment Booking',
                           'created_at'    => curr_date(),
                           'updated_at'    => curr_date()
                           
                   );
       //print_r($data);
       $this->db->insert("notifications", $data);        if($this->db->affected_rows() > 0)
       {
           
           return true; // to the controller
       }
       else{
           return false;
       }
   }  
   

// lab change detail
public function lab_center_info1($user_id){
        $timings = $data = array();
        // echo "SELECT * FROM `lab_center` WHERE `user_id` = $user_id"; die();
       $user_info = $this->db->query("SELECT * FROM `lab_center` WHERE `user_id` = '$user_id'")->row_array();
       if(sizeof($user_info) == 0){
           $user_info = $this->db->query("SELECT * FROM `lab_center` WHERE `branch_user_id` = '$user_id'")->row_array();
       }
           if(sizeof($user_info) > 0)
           {   
               $data = $user_info;
               
               $open_timings = $user_info['opening_hours'];
           
               $eachDays = explode('|',$open_timings);
              
               foreach($eachDays as $days){
                   
                   $dailyInfo = explode('>',$days);
                   
                   $t['day'] = $dailyInfo[0];
                   $times = $dailyInfo[1];
                   
                   $time = explode('-',$times);
                   
                   $t['start_time'] = $time[0];
                   $t['end_time'] = $time[1];
                   
                   
                   $timings[] = $t; 
               
               }
               if($data['branch_user_id'] != 0){
                   $data['user_id'] = $data['branch_user_id'];
               }
                $data['timings'] = $timings;
           } else {
               $data = (object)[];
           }
           
       foreach($data as $k => $v){
           
            if($v == null){
               $data[$k] = "";
            }
       }
       
       return $data;
   }  
   
   //   lab_center_info
    public function lab_center_info($user_id){
        $timings = $data = array();
        // echo "SELECT * FROM `lab_center` WHERE `user_id` = $user_id"; die();
       $user_info = $this->db->query("SELECT * FROM `lab_center` WHERE `user_id` = '$user_id' AND `is_active` = 1")->row_array();
       if(sizeof($user_info) == 0){
           $user_info = $this->db->query("SELECT * FROM `lab_center` WHERE `branch_user_id` = '$user_id' AND `is_active` = 1")->row_array();
       }
           if(sizeof($user_info) > 0)
           {   
               $data = $user_info;
                $features_array    = array();
                $features = $user_info['features'];
                $feature_query     = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $features . "')");
                foreach ($feature_query->result_array() as $get_list) {
                    $feature          = $get_list['feature'];
                    $features_array[] = array(
                        "name" => $feature
                    );
                }
                $data['features'] = $features_array;
               $open_timings = $user_info['opening_hours'];
           
               $eachDays = explode('|',$open_timings);
              $slots = array();
              
                foreach($eachDays as $days){
                   $time_slots = array();
                   $dailyInfo = explode('>',$days);
                   
                   $t['day'] = $dailyInfo[0];
                   $times = $dailyInfo[1];
                   
                   $time = explode('-',$times);
                   
                   $t['start_time'] = $time[0];
                   $t['end_time'] = $time[1];
                   
                   
                   
                          $StarttotalTime = $time[0];
                          $EndtotalTime = $time[1];
                          $stm = explode(',',$StarttotalTime);
                          $etm = explode(',',$EndtotalTime);
                          $start = $end = "";
                          $startTime = "0";
                          
                          for($i=0;$i<sizeof($stm);$i++){
                                if($i!=0){
                                    $start .= ", " ;
                                }
                                $start .= $stm[$i] ." - ". $etm[$i];
                                
                                // slots
                            
                                $s_slot = $stm[$i]; 
                                $e_slot = $etm[$i]; 
                            
                            if($s_slot != $e_slot){
                                $startTime = date("H:i", strtotime($s_slot));
                                $endTim = date("H:i", strtotime($e_slot));
                                  
                                while($startTime < $endTim){
                                    $slot = strtotime($stm[$i]); 
                                   
                                        $slots['time_slot']  =  date("g:i A", strtotime($startTime));
                                        $time_slots[] = $slots;
                                    $startTime = strtotime($startTime);
                                    $startTime = date("H:i", strtotime('+30 minutes', $startTime));
                                     
                                }
                                
                            } else {
                                $startTime = date("H:i", strtotime($s_slot));
                                $e_slot = strtotime($e_slot);
                                $endTim = date("H:i", strtotime('-1 minutes', $e_slot));
                               
                           $i = 0;    
                                  
                                while($startTime < $endTim && $i < 48){
                                    
                                     
                                    $slot = strtotime($s_slot); 
                                   
                                        $slots['time_slot']  =  date("g:i A", strtotime($startTime));
                                        $time_slots[] = $slots;
                                    $startTime = strtotime($startTime);
                                    $startTime = date("H:i", strtotime('+30 minutes', $startTime));
                                    

                                 
                               
                                     $i++;
                                }
                             
                            }
                                
                                
                                
                                
                          } 
                          
                            $t['time'] = $start;
                            $t['time_slots'] = $time_slots;
                                
                   
                   $timings[] = $t; 
                //   $time_slots = $slots;
               
               }
               if($data['branch_user_id'] != 0){
                   $data['user_id'] = $data['branch_user_id'];
               }
                $data['timings'] = $timings;
                
              //  $time_slots = $user_info['opening_hours'];
            
            // $data['time_slots'] = $time_slots;
            
            
                
                
           } else {
               $data = (object)[];
           }
           
       foreach($data as $k => $v){
           
            if($v == null){
               $data[$k] = "";
            }
       }
       
       return $data;
   } 
   
   //   lab_center_search
    public function lab_center_search($term, $user_id, $per_page, $page_no){
        $current_page_count=0;
        $last_page = 0;
        $data = array();
        if($per_page == 0){
            $per_page = 10;
        }
        
        if($page_no == 0){
            $page_no = 1;
        }
        
        //asdasd1
        $offset = $per_page*($page_no - 1);
        $total_data = $this->db->query("SELECT COUNT(`id`)as counts FROM `lab_center` WHERE `lab_name` LIKE '%$term%' or `map_location` LIKE '%$term%' or `address1` LIKE '%$term%' or `address2` LIKE '%$term%' AND `is_active` = 1")->row_array();
        
        $last_page = ceil($total_data['counts']/$per_page);
         
        $data['data_count'] = $total_data['counts'];
        
        $data['per_page'] = $per_page;
        $data['current_page'] = $page_no;
        $data['first_page'] = 1;
        $data['last_page'] = $last_page;
        $data['current_page_count'] = 0;
        
        $data['labs'] = $this->db->query("SELECT `id`,`lab_name` FROM `lab_center` WHERE  `is_active` = 1 AND (`lab_name` LIKE '%$term%' or `map_location` LIKE '%$term%' or `address1` LIKE '%$term%' or `address2` LIKE '%$term%') LIMIT $per_page OFFSET $offset")->result_array();
        
        $data['current_page_count']=sizeof($data['labs']);
        
        
        

       return $data;
   }  
   
   function get_timings($open_timings){
       $status = "Close";
       $t=date('d-m-Y h:i A');
       $today = date("l",strtotime($t));
       $currentTime = date('H:i', strtotime($t));
       
       $timings  = array();
       $eachDays = explode('|',$open_timings);
       
       foreach($eachDays as $days){
           
           $dailyInfo = explode('>',$days);
           
           $tms['day'] = $dailyInfo[0];
           $times = $dailyInfo[1];
           
           
           
           $time = explode('-',$times);
           
           $tms['start_time'] = $time[0];
           $tms['end_time'] = $time[1];
           
           
              $StarttotalTime = $time[0];
              $EndtotalTime = $time[1];
              $stm = explode(',',$StarttotalTime);
              $etm = explode(',',$EndtotalTime);
              $start = $end = "";
              for($i=0;$i<sizeof($stm);$i++){
                  if($i!=0){
                      $start .= ", " ;
                  }
                  $start .= $stm[$i] ." - ". $etm[$i];
                  
                  if($stm[$i] != 'close' && $etm[$i] != 'close' ){
                       $datetime1 = new DateTime($stm[$i]);
                      $datetime2 = new DateTime($etm[$i]);
                      $start_dt =  $datetime1->format('H:i');
                       $end_dt =  $datetime2->format('H:i');
                    

                    if($today == $tms['day'] && $currentTime >= $start_dt && $currentTime <= $end_dt){
                        $status = "Open";
                    }
                  }
                  
                     
                
              } 
             
                $tms['time'] = $start;
           
           $timings[] = $tms; 
        }
        
        $ret['status'] = $status;
        $ret['timings'] = $timings;
       
       return $ret;
   }
   
//   get_offers

    
    public function get_offers($offer_id){
        $allOffers = array();
        $today = $created_date = date("Y-m-d");
        if($offer_id != ""){
            $whereId = "AND id = $offer_id";
        } else {
            $whereId = "";
        }
        
	    $offers = $this->db->query("SELECT * FROM `vendor_offers` WHERE listing_id='10' AND status = '0' AND end_date >= '$today' $whereId ")->result_array();
	    foreach ($offers as $offer) {
	      
	       $o['offer_id'] = $offer['id']; 
	       $o['vendor_id'] = $offer['vendor_id']; 
	       $o['name'] = $offer['name']; 
	       $o['price'] = $offer['price']; 
	       $o['save_type'] = $offer['save_type']; 
	       $o['end_date'] = $offer['end_date']; 
	       $o['offer_image'] = $offer['offer_image']; 
	       $o['offer_description'] = $offer['offer_description']; 
	       $o['offer_tnc'] = $offer['offer_tnc']; 
	       
	       
	     $allOffers[] = $o;
	        
	    }
	    
	    return $allOffers;
	}
	
	 public function get_offer_content($user_id,$offer_id){
        $dataPackages = $allOffers = array();
        $today = $created_date = date("Y-m-d");
        
         $resp = $this->LabcenterModel_v2->get_offer_content_by_id($offer_id);
         
         return $resp;
	 }
	 
	 
	 public function get_offer_content_by_id($offer_id){
        $dataPackages = $testAllInfo = $dataTest = $allOffers = array();
        $today = $created_date = date("Y-m-d");
        $tests = $packages = $data = array();
        $coupon_code = "";
        
        $offers = $this->db->query("SELECT * FROM `vendor_offers` WHERE listing_id='10' AND status = '0' AND end_date >= '$today' AND id = $offer_id ")->result_array();
	    
        foreach($offers as $o){
         //   $offer_id = $o['offer_id'];
            $coupon_code = $o['name'];
            $offer_on = $o['offer_on'];
            $offer_on_ids = $o['offer_on_ids'];
            $vendor_id = $o['vendor_id'];
           
            
            if($vendor_id != ""){
                $whereVendor = "AND ltd.`user_id` = $vendor_id";
                $whereVendorPac = "AND `user_id` = $vendor_id";
            } else {
                $whereVendorPac = $whereVendor = "";
            }
            
            // if tests
            if($offer_on == 2 || $offer_on != 3){
                
                $allTests = $this->db->query("SELECT ltd.*,lc.lab_name as name,lc.profile_pic ,lc.`recommended`,lc.`certified`,lc.`mba`, lc.`opening_hours`,lc.`address1`,lc.`address2`,lc.contact_no,lc.latitude,lc.longitude,lc.`pincode`,lc.`city`,lc.`state`, lat.test,lat.sample_type FROM `lab_test_details1` as ltd LEFT JOIN lab_center as lc ON (ltd.user_id = lc.user_id OR ltd.user_id = lc.branch_user_id) LEFT JOIN lab_all_test1 as lat ON(ltd.`test_id` = lat.id) WHERE ltd.`price` != 0  $whereVendor   GROUP by ltd.id   ORDER BY `user_id` ASC ")->result_array();
                $oldVendorId = "";
                foreach($allTests as $t){
                    $timings = array();
                    $id = $t['test_id'];
                    $test_id = $t['test_id'];
                    $user_id = $t['user_id'];
                    $price = $t['price'];
                    $discount = $t['discount'];
                    $discounted_price = $t['discounted_price'];
                    $discount_type = 'percent';
                    if($discounted_price == 0 || $discounted_price == ''){
                        if($discount != 0){
                            $discounted_price = $this->LabcenterModel_v2->get_disounted_rate($price,$discount_type,$discount);    
                        } else {
                            $discounted_price = $price;
                        }
                        
                    }
                    
                    $offer = $t['offer'];
                    $home_delivery = $t['home_delivery'];
                    $name = $t['name'];
                    $profile_pic = $t['profile_pic'];
                    $test = $t['test'];
                    // print_r($t['test']); die();
                    $sample_type = $t['sample_type'];
                    
                    $address1 = $t['address1'];
                    $address2 = $t['address2'];
                    $pincode = $t['pincode'];
                    $city = $t['city'];
                    $state = $t['state'];
                    
                     $contact_no = $t['contact_no'];
                    $latitude = $t['latitude'];
                    $longitude = $t['longitude'];
                    
                    $recommended = $t['recommended'];
                    $certified = $t['certified'];
                    $mba = $t['mba'];
                    
                     
                    $open_timings = $t['opening_hours'];
                    if(!empty($open_timings)){
        
                     
                       $lab_hours = $this->LabcenterModel_v2->get_timings($open_timings);
                    $status = $lab_hours['status'];
                    $timings = $lab_hours['timings'];
                       
                    }  else{
                        $timings = array();
                    }
                    
                    if($oldVendorId ==  $t['user_id']){
                        
                        $testInnerInfo['id'] = $id;
                        $testInnerInfo['test_name'] = $test;
                        $testInnerInfo['sample_type'] = $sample_type;
                        $testInnerInfo['test_id'] = $test_id;
                        $testInnerInfo['price'] = $price;
                        $testInnerInfo['discounted_price'] = $discounted_price;
                        $testInnerInfo['discount'] = $discount;
                        
                        $testAllInfo['test_info'][] = $testInnerInfo; 
                        $totalPrice = $totalPrice + $price;
                        $totalDiscounted_price = $totalDiscounted_price + $discounted_price;
                        
                        
                    } else {
               
                    if(sizeof($testAllInfo) > 0){
                        
                       $testAllInfo['test_info'] =  $this->LabcenterModel_v2->unique_multidim_array($testAllInfo['test_info'],'id'); 
                       
                       
                        // print_r($testAllInfo['test_info']); die();
                        $testAllInfo['total_price'] = $totalPrice;
                        $testAllInfo['total_discounted_price'] = $totalDiscounted_price;
                        $testAllInfo['total_discount'] = $totalPrice - $totalDiscounted_price;
                        
                        $data[] = $testAllInfo; 
                    }
                    $testAllInfo = $testInnerInfo = array();
                    $totalDiscounted_price = $totalPrice = 0;
                    
                    $totalPrice = $totalPrice + $price;
                    $totalDiscounted_price = $totalDiscounted_price + $discounted_price;
                    
                    $testAllInfo['vendor_name'] = $name;
                    $testAllInfo['vendor_image'] = "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$profile_pic;
                    $testAllInfo['address1'] = $address1;
                    $testAllInfo['address2'] = $address2;
                    $testAllInfo['pincode'] = $pincode;
                    $testAllInfo['city'] = $city;
                    $testAllInfo['state'] = $state;
                    
                    $testAllInfo['contact_no'] = $contact_no;
                    $testAllInfo['latitude'] = $latitude;
                    $testAllInfo['longitude'] = $longitude;
                    
                    $testAllInfo['vendor_id'] = $user_id;
                    $testAllInfo['timings'] = $timings;
                    $testAllInfo['status'] =$status;
                    
                    $testAllInfo['recommended'] = $recommended;
                    $testAllInfo['certified'] = $certified;
                    $testAllInfo['mba'] = $mba;
                     
                    // $testAllInfo['total_price'] = $totalPrice;
                    // $testAllInfo['total_discounted_price'] = $totalDiscounted_price;
                   
                    $rating_query = $this->db->query("SELECT sum(labcenter_review.rating) as rating, count(labcenter_review.rating) as total_view FROM `labcenter_review` WHERE labcenter_review.labcenter_id='$user_id'");
                    $rating_coun = $rating_query->num_rows();
                    if($rating_coun > 0)
                    {
                    $rating_counting = $rating_query->row()->total_view;
                    if($rating_counting > 0)
                    {
                    $rating_view = $rating_query->row()->rating;
                    $rating_count=$rating_view/$rating_counting;
                    }
                    else
                    {
                        $rating_count=0; 
                    }
                    }
                    else
                    {
                       $rating_count=0; 
                    }
                    $testAllInfo['rating']=  number_format($rating_count,1); 
                    
                    $testInnerInfo['id'] = $id;
                    $testInnerInfo['test_name'] = $test;
                    $testInnerInfo['sample_type'] = $sample_type;
                    $testInnerInfo['test_id'] = $test_id;
                    $testInnerInfo['price'] = $price;
                    $testInnerInfo['discounted_price'] = $discounted_price;
                    $testInnerInfo['discount'] = $discount;
                    $testAllInfo['test_info'][] = $testInnerInfo; 
                    
                    
                }
                $oldVendorId = $t['user_id'];
                
            }
                if(sizeof($testAllInfo) > 0){
                    $testAllInfo['test_info'] =  $this->LabcenterModel_v2->unique_multidim_array($testAllInfo['test_info'],'id'); 
                           
                    $testAllInfo['total_price'] = $totalPrice;
                    $testAllInfo['total_discounted_price'] = $totalDiscounted_price;
                    $testAllInfo['total_discount'] = $totalPrice - $totalDiscounted_price;
                    $dataTest[] = $testAllInfo; 
                }
                
            }
            
            // if packages
            if($offer_on == 3 || $offer_on != 2){
                
                $pacdata =  $this->db->query("SELECT * FROM `lab_packages1` WHERE `v_id` = 10 $whereVendorPac  ")->result_array();
                
                
                
                 foreach($pacdata as $d){
            // print_r($d); die;
            $timings = array();
            $user_id = $d['user_id'];
            $user_name = $this->db->query("SELECT `lab_name` as name ,`branch_user_id`,`recommended`,`certified`,`mba`,contact_no,latitude,longitude, `opening_hours`, `profile_pic`,`address1`,`address2`,`pincode`,`city`,`state` FROM `lab_center`  WHERE `user_id` = '$user_id' AND `is_active` = 1")->row_array();
            if(empty($user_name['name'])){
                $user_name = $this->db->query("SELECT `lab_name` as name ,`branch_user_id`,`recommended`,`certified`,`mba`,contact_no,latitude,longitude, `opening_hours`, `profile_pic`,`address1`,`address2`,`pincode`,`city`,`state` FROM `lab_center`  WHERE `branch_user_id` = '$user_id' AND `is_active` = 1")->row_array();
            
                if(empty($user_name['name'])){
                
                    $vendor_name = "";
                    $vendor_image = "";
                    $address1 ="";
                    $address2 ="";
                    $pincode ="";
                    $city ="";
                    $state ="";
                    
                    $recommended = "0";
                    $certified = "0";
                    $mba = "0";

                } else {
                    $vendor_name = $user_name['name'];
                    $vendor_image = "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$user_name['profile_pic'];
                    $address1 = $user_name['address1'];
                    $address2 = $user_name['address2'];
                    $pincode = $user_name['pincode'];
                    $city = $user_name['city'];
                    $state = $user_name['state'];
                    
                    $contact_no = $user_name['contact_no'];
                    $latitude = $user_name['latitude'];
                    $longitude = $user_name['longitude'];
                    
                    $recommended = $user_name['recommended'];
                    $certified = $user_name['certified'];
                    $mba = $user_name['mba'];
                
                
                    
                }
            
                
            } else {
                $vendor_name = $user_name['name'];
                $vendor_image = "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$user_name['profile_pic'];
                $address1 = $user_name['address1'];
                $address2 = $user_name['address2'];
                $pincode = $user_name['pincode'];
                $city = $user_name['city'];
                $state = $user_name['state'];
                
                $contact_no = $user_name['contact_no'];
                    $latitude = $user_name['latitude'];
                    $longitude = $user_name['longitude'];
                    
                    $recommended = $user_name['recommended'];
                    $certified = $user_name['certified'];
                    $mba = $user_name['mba'];
                
            
                
            }
            
            if(empty($user_name['opening_hours'])){
                $timings = array();
                // echo $user_id; die();
                
            } else {
                
                
                $open_timings = $user_name['opening_hours'];
                // $timings = $d['opening_hours'];
            
             
             $lab_hours = $this->LabcenterModel_v2->get_timings($open_timings);
            $status = $lab_hours['status'];
            $timings = $lab_hours['timings'];
            
            
       
                   
            }
            
           
            $finalTests = $tests =array();
            $d['timings'] = $timings;
            $d['status'] = $status;
            $d['vendor_name'] = $vendor_name ;
            $d['vendor_image'] = $vendor_image ;
            $d['address1'] = $address1;
            $d['address2'] = $address2;
            $d['pincode'] = $pincode;
            $d['city'] = $city;
            $d['state'] = $state;
            $d['contact_no'] = $contact_no;
            $d['latitude'] = $latitude;
            $d['longitude'] = $longitude;
            
            $d['recommended'] = $recommended;
            $d['certified'] = $certified;
            $d['mba'] = $mba;
            $discount = $d['discount'];
            $Price = $d['Price'];
            $discount_type = $d['discount_type'];
            $discounted_price = 0;
            
            
            $pathology_test = $d['pathology_test'];
            $diagnostic_test = $d['diagnostic_test'];  
            $fnac_test = $d['fnac_test'];  
            
            if(!empty($pathology_test)){
                $tests[] = $this->db->query("SELECT * FROM `lab_all_test1` WHERE `id` IN ($pathology_test)")->result_array();
            }
            if(!empty($diagnostic_test)){
                $tests[] = $this->db->query("SELECT * FROM `lab_all_test1` WHERE `id` IN ($diagnostic_test)")->result_array();
            }
            if(!empty($fnac_test)){
                $tests[] = $this->db->query("SELECT * FROM `lab_all_test1` WHERE `id` IN ($fnac_test)")->result_array();
            }            
            // SELECT * FROM `lab_all_test1` WHERE `id` IN (1,2)
            // print_r($tests); die();
            
            
            if($discount > 0){
                $discounted_price = $this->LabcenterModel_v2->get_disounted_rate($Price,$discount_type,$discount);
            } else {
                $discounted_price = strval($Price); 
            }
            // echo $discounted_price; die();
            foreach($d as $key => $value){
                if($value == null && !is_array($value)){
                    $d[$key] = "";
                }
            }
            $d['discounted_price'] = $discounted_price;
            $d['discount_price'] = $discounted_price;
           
            foreach($tests as $ts){
                foreach($ts as $t){
                    if(!empty($t)){
                        unset($t['created_at']);
                        unset($t['created_by']);
                        unset($t['updated_at']);
                        unset($t['updated_by']);
                        
                        $finalTests[] = $t;
                    }
                }
            }
            
            $details = $this->LabcenterModel_v2->unique_multidim_array($finalTests,'id'); 
        // print_r($d); die();
         $rating_query = $this->db->query("SELECT sum(labcenter_review.rating) as rating, count(labcenter_review.rating) as total_view FROM `labcenter_review` WHERE labcenter_review.labcenter_id='$user_id'");
                $rating_coun = $rating_query->num_rows();
                if($rating_coun > 0)
                {
                $rating_counting = $rating_query->row()->total_view;
                if($rating_counting > 0)
                {
                $rating_view = $rating_query->row()->rating;
                $rating_count=$rating_view/$rating_counting;
                }
                else
                {
                    $rating_count=0; 
                }
                }
                else
                {
                   $rating_count=0; 
                }
                $d['rating']=  number_format($rating_count,1); 
        
        
            $d['tests'] = $details;
            
            $dataPackages[] = $d;
         
        }
                
            }
            
            // if both : default both
            
            
            
            
        }
        
        $data['offer_id'] = $offer_id;
        $data['coupon_code'] = $coupon_code;
        $data['tests'] = $dataTest;
        $data['packages'] = $dataPackages;
        
        return $data;
        
	 }
	 
    public function search_all($user_id,$term,$lat,$lng,$page_no,$per_page,$search_for){
        $data = array();
        $searchWhere = "";
        
        if($search_for != 0){
            
        } else {
            $searchWhere = "";
        }
        
        return $data;
    }
	 
	 
	 
}
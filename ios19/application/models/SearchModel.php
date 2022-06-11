<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SearchModel extends CI_Model {

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
    
    //encrypt string to md5 base64
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

    //decrypt string from md5 base64
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


        $system_start_time = date("H.i", strtotime("-1 minutes", strtotime($free_start_time)));
        $system_end_time = date("H.i", strtotime("-1 minutes", strtotime($free_end_time)));
        $current_time = date('H.i');



        if ($is_24hrs_available == 'Yes') {
            if ($day_night_delivery == 'Yes') {

                if ($current_time > $system_start_time && $current_time < $system_end_time) {
                    if ($is_min_order_delivery == 'Yes') {
                        $current_delivery_charges = 'Free Delivery Available Above Rs ' . $min_order;
                    } else {
                        $current_delivery_charges = 'Delivery Not Available Below Rs ' . $min_order;
                    }
                } elseif ($system_start_time == '23.59' && $system_end_time == '23.59') { //free delivery set to 12:00 am to 12:00 am
                    // $current_delivery_charges = 'Free Delivery Available';
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
                } elseif ($system_start_time == '23.59' && $system_end_time == '23.59') {
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
        } else {

            if ($current_time > $system_start_time && $current_time < $system_end_time) {
                //$current_delivery_charges = 'Free Delivery Available';
                if ($is_min_order_delivery == 'Yes') {
                    $current_delivery_charges = 'Free Delivery Available Above Rs ' . $min_order;
                } else {
                    $current_delivery_charges = 'Delivery Not Available Below Rs ' . $min_order;
                }
            } elseif ($system_start_time == '23.59' && $system_end_time == '23.59') {
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


        return $current_delivery_charges;
    }

    public function check_time_format($time) {
        $time_filter = preg_replace('/\s+/', '', $time);
        $final_time = date("h:i A", strtotime($time_filter));
        return $final_time;
    }
    
public function elasticsearchspa($index_id,$keyword){
        $returndoctor = array();
        $perc=array();
     	$data = array("query"=>array("bool"=>array("should"=>array(array("match"=>array("branch_address"=>"$keyword" )),array("match"=>array("field1"=>"$keyword" ))))),"suggest"=>array("my-suggestion"=>array("text"=>$keyword, "term"=>array("field"=>"branch_name")))); 
        $data1=json_encode($data);
      
        $returnresult = $this->elasticsearch->advancedquerysize3($index_id,$data1);
        
    	   if($returnresult['hits']['total'] > 0){
    	       
                	   foreach($returnresult['hits']['hits'] as $hi){
                	 
                	      $sim = similar_text($hi['_source']['branch_address'], $keyword, $perc[]);
                	   }
                	}
                 @$dataperc=max($perc);
        if($dataperc >= 50){
               foreach($returnresult['hits']['hits'] as $hi){
                	      $returndoctor[] =$hi['_source'];
                	   }
                     
                	   return $returndoctor;
                	   
                	   
                     }else{
     
                    	$name=array();
                    	foreach($returnresult['suggest']['my-suggestion'] as $a){
                            	if(empty($a['options'])){
                            	  $name[]=  $a['text'];
                            	}else{
                            	      $name[]=$a['options'][0]['text'];
                            	}
                        }
                        $string_version = implode(' ', $name);
                        $returnresult = $this->elasticsearch->query_allsize3($index_id,$string_version);
                                foreach($returnresult['hits']['hits'] as $hi){
                                	      $returndoctor[] = $hi['_source'];
                                	   }
                                	 
                      return $returndoctor;
            }
       
}


public function elasticsearchspa_package($index_id,$keyword){
        $returndoctor = array();
        $perc=array();
     	$data = array("query"=>array("bool"=>array("should"=>array(array("match"=>array("package_name"=>"$keyword" )),array("match"=>array("package_details"=>"$keyword" ))))),"suggest"=>array("my-suggestion"=>array("text"=>$keyword, "term"=>array("field"=>"package_name")))); 
        $data1=json_encode($data);
      
        $returnresult = $this->elasticsearch->advancedquerysize3($index_id,$data1);
        
    	   if($returnresult['hits']['total'] > 0){
    	       
                	   foreach($returnresult['hits']['hits'] as $hi){
                	 
                	      $sim = similar_text($hi['_source']['package_name'], $keyword, $perc[]);
                	   }
                	}
                 @$dataperc=max($perc);
        if($dataperc >= 50){
               foreach($returnresult['hits']['hits'] as $hi){
                	      $returndoctor[] =$hi['_source'];
                	   }
                     
                	   return $returndoctor;
                	   
                     }else{
     
                    	$name=array();
                    	foreach($returnresult['suggest']['my-suggestion'] as $a){
                            	if(empty($a['options'])){
                            	  $name[]=  $a['text'];
                            	}else{
                            	      $name[]=$a['options'][0]['text'];
                            	}
                        }
                        $string_version = implode(' ', $name);
                        $returnresult = $this->elasticsearch->query_allsize3($index_id,$string_version);
                                foreach($returnresult['hits']['hits'] as $hi){
                                	      $returndoctor[] = $hi['_source'];
                                	   }
                                	 
                      return $returndoctor;
            }
       
}

public function elasticsearchhospital_package($index_id,$keyword){
        $returndoctor = array();
        $perc=array();
     	$data = array("query"=>array("bool"=>array("should"=>array(array("match"=>array("package_name"=>"$keyword" ))))),"suggest"=>array("my-suggestion"=>array("text"=>$keyword, "term"=>array("field"=>"package_name")))); 
        $data1=json_encode($data);
      
        $returnresult = $this->elasticsearch->advancedquerysize3($index_id,$data1);
        
    	   if($returnresult['hits']['total'] > 0){
    	       
                	   foreach($returnresult['hits']['hits'] as $hi){
                	 
                	      $sim = similar_text($hi['_source']['package_name'], $keyword, $perc[]);
                	   }
                	}
                 @$dataperc=max($perc);
        if($dataperc >= 50){
               foreach($returnresult['hits']['hits'] as $hi){
                	      $returndoctor[] =$hi['_source'];
                	   }
                     
                	   return $returndoctor;
                	   
                     }else{
     
                    	$name=array();
                    	foreach($returnresult['suggest']['my-suggestion'] as $a){
                            	if(empty($a['options'])){
                            	  $name[]=  $a['text'];
                            	}else{
                            	      $name[]=$a['options'][0]['text'];
                            	}
                        }
                        $string_version = implode(' ', $name);
                        $returnresult = $this->elasticsearch->query_allsize3($index_id,$string_version);
                                foreach($returnresult['hits']['hits'] as $hi){
                                	      $returndoctor[] = $hi['_source'];
                                	   }
                                	 
                      return $returndoctor;
            }
       
}




    public function keyword_list($user_id,$type,$keyword) {
   
       // $query = $this->db->query("SELECT name FROM `users` WHERE name like '%$keyword%' order by name asc limit 10");
       // $count = $query->num_rows();
       
         $lowerkey= strtolower($keyword);
           $parts = explode(' ', $keyword); 
           
           foreach($parts as $a){
               if($a=="doctor"){
                $namep[]="dr";   
               }else{
                   $namep[]=$a;  
               }
           }
            
            $keyword = implode(' ', $namep);
       
        if($type=="hm_product"){
       $data = array("from" => "0", "size" => "9999","query"=>array("match_phrase_prefix"=>array("pd_name"=>$keyword))); 
        $data1=json_encode($data);
        }else{
             $data = array("from" => "0", "size" => "9999","query"=>array("match_phrase_prefix"=>array("name"=>$keyword))); 
        $data1=json_encode($data);
        }
        if($type!=""){
            
            if($type=='all'){
			$type='';
			}else if($type=='Dental_clinic'){ 
			$type='dentist_clinic_list';
			}else{
			$type;
			}
            
       $returnresult = $this->elasticsearch->advancedquerysize3($type,$data1); 
        }else{
            
        $returnresult = $this->elasticsearch->advancedquery($data1);
        }
      $count=$returnresult['hits']['total'];
        if ($count > 0) {
             if($type=="hm_product"){
                  foreach($returnresult['hits']['hits'] as $hi){
           // foreach ($query->result_array() as $row) {
                $keyword = $hi['_source']['pd_name'];
                $resultpost[] = array(
                    'keyword' => $keyword
                );
            }
             }else{
             foreach($returnresult['hits']['hits'] as $hi){
           // foreach ($query->result_array() as $row) {
                $keyword = $hi['_source']['name'];
                $resultpost[] = array(
                    'keyword' => $keyword
                );
            }}
        } else if($count == 0) {
              if($type=="hm_product"){
            	$data = array("suggest"=>array("my-suggestion"=>array("text"=>$keyword, "term"=>array("field"=>"pd_name")))); 
                $data1=json_encode($data);
              }else{
                 	$data = array("suggest"=>array("my-suggestion"=>array("text"=>$keyword, "term"=>array("field"=>"name")))); 
                $data1=json_encode($data); 
                  
              }
    
                 if($type!=""){
                  if( $type=="spa_package"){
                     $returnresult = $this->elasticsearchspa_package($type,$keyword); 
                      }else if($type=="spa_listing"){
                           $returnresult = $this->elasticsearchspa($type,$keyword); 
                         }
                         else if($type=="hospital_package"){
                           $returnresult = $this->elasticsearchhospital_package($type,$keyword); 
                         }
                         else{
                       $returnresult = $this->elasticsearch->advancedquerysize3($type,$data1); 
                      }    
               
                
                }else{
                $returnresult = $this->elasticsearch->advancedquery($data1);
        
                }
             
                //$return =	$this->elasticsearch->keywords($data1);
             if( $type=="spa_package"){
                    foreach($returnresult as $as){
                            	     $resultpost[] = array(
                                        'keyword' => $as['package_name']
                                    );
                            	     
                            	    }
                      }else if($type=="spa_listing"){
                            foreach($returnresult as $as){
                            	     $resultpost[] = array(
                                        'keyword' => $as['branch_name']
                                    );
                            	     
                            	    }
                         }else if($type=="hospital_package"){
                            foreach($returnresult as $as){
                            	     $resultpost[] = array(
                                        'keyword' => $as['package_name']
                                    );
                            	     
                            	    }
                         }else{ 
                                foreach($returnresult['suggest']['my-suggestion'] as $a){
                                            	if(empty($a['options'])){
                                            	   $resultpost[] = array(
                                                        'keyword' => $a['text']
                                                    );
                                            	
                                            	}else{
                                            	    foreach($a['options'] as $as){
                                            	     $resultpost[] = array(
                                                        'keyword' => $as['text']
                                                    );
                                            	     
                                            	    }
                                            	}
                                            	 
                                        }
                         }
            
                }else{
                    $resultpost = array();
                }
        return $resultpost;
    }

    public function elasticsearch($index_id,$keyword){
     
        $returndoctor = array();
        $perc=array();
     $returnresult = $this->elasticsearch->query_all($index_id,$keyword);
     
    	    if($returnresult['hits']['total'] < 0){
    	       
                	   foreach($returnresult['hits']['hits'] as $hi){
                	 
                	      $sim = similar_text($hi['_source']['name'], $keyword, $perc[]);
                	   }
                	}

                 @$dataperc=max($perc);
        if($dataperc >= 70){
            	
             $returnresult = $this->elasticsearch->query_all($index_id,$keyword);
               foreach($returnresult['hits']['hits'] as $hi){
                	      $returndoctor[] =$hi['_source'];
                	   }
                	   return $returndoctor;
                	   
            }else{
     
            	$data = array("suggest"=>array("my-suggestion"=>array("text"=>$keyword, "term"=>array("field"=>"name")))); 
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
                        $returnresult = $this->elasticsearch->query_all($index_id,$string_version);
                                foreach($returnresult['hits']['hits'] as $hi){
                                	      $returndoctor[] = $hi['_source'];
                                	   }
                 return $returndoctor;
             }
}
    public function elasticsearchsize3($index_id,$keyword){
        $returndoctor = array();
        $perc=array();
     	$data = array("query"=>array("bool"=>array("should"=>array(array("match"=>array("name"=>"$keyword" )),array("match"=>array("field1"=>"$keyword" ))))),"suggest"=>array("my-suggestion"=>array("text"=>$keyword, "term"=>array("field"=>"name")))); 
        $data1=json_encode($data);
      
        $returnresult = $this->elasticsearch->advancedquerysize3($index_id,$data1);
        
    	   if($returnresult['hits']['total'] > 0){
    	       
                	   foreach($returnresult['hits']['hits'] as $hi){
                	 
                	      $sim = similar_text($hi['_source']['name'], $keyword, $perc[]);
                	   }
                	}
                 @$dataperc=max($perc);
        if($dataperc >= 50){
               foreach($returnresult['hits']['hits'] as $hi){
                	      $returndoctor[] =$hi['_source'];
                	   }
                     
                	   return $returndoctor;
                	   
                	   
                     }else{
     
                    	$name=array();
                    	foreach($returnresult['suggest']['my-suggestion'] as $a){
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
            }
       
}



 public function elasticsearchdoctor($index_id,$keyword){
        $returndoctor = array();
        $perc=array();
     	$data = array("query"=>array("match"=>array("name"=>"$keyword" )),"suggest"=>array("my-suggestion"=>array("text"=>$keyword, "term"=>array("field"=>"name")))); 
        $data1=json_encode($data);
        $returnresult = $this->elasticsearch->advancedquerysize3($index_id,$data1);
        
    	   if($returnresult['hits']['total'] > 0){
    	       
                	   foreach($returnresult['hits']['hits'] as $hi){
                	 
                	      $sim = similar_text($hi['_source']['name'], $keyword, $perc[]);
                	   }
                	}
                 @$dataperc=max($perc);
        if($dataperc >= 50){
               foreach($returnresult['hits']['hits'] as $hi){
                	      $returndoctor[] =$hi['_source'];
                	   }
                     
                	   return $returndoctor;
                	   
                	   
                     }else{
     
                    	$name=array();
                    	foreach($returnresult['suggest']['my-suggestion'] as $a){
                            	if(empty($a['options'])){
                            	  $name[]=  $a['text'];
                            	}else{
                            	      $name[]=$a['options'][0]['text'];
                            	}
                        }
                        $string_version = implode(' ', $name);
                        $returnresult = $this->elasticsearch->query_allsize3($index_id,$string_version);
                                foreach($returnresult['hits']['hits'] as $hi){
                                	      $returndoctor[] = $hi['_source'];
                                	   }
                                	 
                      return $returndoctor;
            }
       
}

	public function elasticsearchhm_product($index_id,$keyword){
       $this->load->library('Elasticsearch');
        $returndoctor = array();
        $perc=array(); 
      
         $data = array("query"=>array("multi_match"=>array("query"=>"$keyword","fields"=>[ "v_name", "pd_name","pd_short_desc","pd_long_desc" ] )),"suggest"=>array("my-suggestion"=>array("text"=>$keyword, "term"=>array("field"=>"pd_name")))); 
        $data1=json_encode($data);
        $returnresult = $this->elasticsearch->advancedquerysize3($index_id,$data1);
          //  print_r($returnresult);
    	   if($returnresult['hits']['total'] > 0){
    	       
                	   foreach($returnresult['hits']['hits'] as $hi){ 
                	   $returndoctor[] = $hi['_source'];
                	    
                	   }
                	}
           
                      return $returndoctor;
             
}
	
    public function search_list($user_id, $keyword) {
        if($keyword!=""){
             date_default_timezone_set('Asia/Kolkata');
            $lowerkey= strtolower($keyword);
            $data=array('user_id'=>$user_id,'keyword'=>$lowerkey,'date'=>date('Y-m-d H:i:s'));
            $this->db->insert('user_search_history',$data);
            
        }
        
        if ($user_id > 0) {
            
            $lowerkey= strtolower($keyword);
           $parts = explode(' ', $keyword); 
           
           foreach($parts as $a){
               if($a=="doctor"){
                $namep[]="dr";   
               }else{
                   $namep[]=$a;  
               }
           }
            
            $string_versionp = implode(' ', $namep);
            // People
            $field1 = '';
            $field2 = '';
            $field3 = '';
               $index_id="people";
                  $people=$this->elasticsearchsize3($index_id,$string_versionp);
              
                    if (!empty($people)) {
                        $people_array[] = array(
                            'title' => 'People',
                            'listing_type' => 0,
                            'redirection_type' => 1,
                            'array' => $people
                        );
                    } else {
                        $people_array = array();
                    } 
            
            // Doctor
            $field1 = '';
            $field2 = '';
            $field3 = '';
            $index_id="doctor";
           $lowerkey= strtolower($keyword);
           $arr=explode(" ",$lowerkey); 
            
           foreach($arr as $a){
               if($a=="doctor"){
                $name1[]="dr";   
               }else{
                   $name1[]=$a;  
               }
           }
            
            $string_version1 = implode(' ', $name1);
            
            $doctor=$this->elasticsearchsize3($index_id,$string_version1);
                 
            if (!empty($doctor)) {
                $doctor_array[] = array(
                    'title' => 'Doctor',
                    'listing_type' => 5,
                    'redirection_type' => 2,
                    'array' => $doctor
                );
            } else {
                $doctor_array = array();
            }
            
              // Dentist_clinic_list
            $field1 = '';
            $field2 = '';
            $field3 = '';
            $index_id="dentist_clinic_list";
           $lowerkey= strtolower($keyword);

            
            $dental_clinic_list=$this->elasticsearchsize3($index_id,$lowerkey);
                 
            if (!empty($dental_clinic_list)) {
                $dental_clinic_array[] = array(
                    'title' => 'Dental Clinic',
                    'listing_type' => 39,
                     'redirection_type' => 3,
                    'array' => $dental_clinic_list
                );
            } else {
                $dental_clinic_array = array();
            }
            

            // Pharmacy
            $field1 = '';
            $field2 = '';
            $field3 = '';
            
             
              $index_id="medical";
                    
                     $pharmacy=$this->elasticsearchsize3($index_id,$keyword);
                if (!empty($pharmacy)) {
                    $pharmacy_array[] = array(
                        'title' => 'Pharmacy',
                        'listing_type' => 13,
                         'redirection_type' => 11,
                        'array' => $pharmacy
                    );
                } else {
                    $pharmacy_array = array();
                }
        
            /*// Ayurveda
            $field1 = '';
            $field2 = '';
            $field3 = '';
             if(count($parts) > 1)
            {
                for($i=0;$i<count($parts);$i++)
                {
                    $ayurveda_array = array();
                    $ayurveda_query = $this->db->query("SELECT profile_pic,ayurveda_name,address1 FROM ayurveda WHERE ayurveda_name like '%$parts[$i]%' limit 2");
                    $ayurveda_count = $ayurveda_query->num_rows();
                    if ($ayurveda_count > 0) {
                        foreach ($ayurveda_query->result_array() as $ayurveda_row) {
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
                            $ayurveda[] = array(
                                'listing_id' => $user_id,
                                'name' => $name,
                                'image' => $image,
                                'field1' => $field1,
                                'field2' => $field2,
                                'field3' => $field3
                            );
                        }
                        $ayurveda_array[] = array(
                            'title' => 'Ayurveda',
                            'listing_type' => 1,
                            'array' => $ayurveda
                        );
                    } else {
                        $ayurveda_array = array();
                    }
                }
            }else
            {
                 $ayurveda_query = $this->db->query("SELECT profile_pic,ayurveda_name,address1 FROM ayurveda WHERE ayurveda_name like '%$keyword%' limit 2");
            $ayurveda_count = $ayurveda_query->num_rows();
            if ($ayurveda_count > 0) {
                foreach ($ayurveda_query->result_array() as $ayurveda_row) {
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
                    $ayurveda[] = array(
                        'listing_id' => $user_id,
                        'name' => $name,
                        'image' => $image,
                        'field1' => $field1,
                        'field2' => $field2,
                        'field3' => $field3
                    );
                }
                $ayurveda_array[] = array(
                    'title' => 'Ayurveda',
                    'listing_type' => 1,
                    'array' => $ayurveda
                );
            } else {
                $ayurveda_array = array();
            }
            }


            // Homeopathic
            $field1 = '';
            $field2 = '';
            $field3 = '';
             if(count($parts) > 1)
            {
                for($i=0;$i<count($parts);$i++)
                {
                    $homeopathic_array = array();
                    $homeopathic_query = $this->db->query("SELECT user_id,image,doctor_name,speciality,qualification,address FROM doctor_list WHERE doctor_name like '%$parts[$i]%' limit 2");
                    $homeopathic_count = $homeopathic_query->num_rows();
                    if ($homeopathic_count > 0) {
                        foreach ($homeopathic_query->result_array() as $homeopathic_row) {
                            $listing_id = $homeopathic_row['user_id'];
                            $name = str_replace("null", "", $homeopathic_row['doctor_name']);
                            $field1 = str_replace("null", "", $homeopathic_row['speciality']);
                            $field2 = str_replace("null", "", $homeopathic_row['qualification']);
                            $field3 = str_replace("null", "", $homeopathic_row['address']);
                            $homeopathic_image = str_replace("null", "", @$doctor_row['image']);
                            if ($homeopathic_image != '') {
                             //   $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $homeopathic_image;
                                 $image =  'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'. $homeopathic_image;
                            } else {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                            }
                            $homeopathic[] = array(
                                'listing_id' => $listing_id,
                                'name' => $name,
                                'image' => $image,
                                'field1' => $field1,
                                'field2' => $field2,
                                'field3' => $field3
                            );
                        }
                        $homeopathic_array[] = array(
                            'title' => 'Homeopathic',
                            'listing_type' => 9,
                            'array' => $homeopathic
                        );
                    } else {
                        $homeopathic_array = array();
                    }
                }
            }else
            {
                 $homeopathic_query = $this->db->query("SELECT user_id,image,doctor_name,speciality,qualification,address FROM doctor_list WHERE doctor_name like '%$keyword%' limit 2");
            $homeopathic_count = $homeopathic_query->num_rows();
            if ($homeopathic_count > 0) {
                foreach ($homeopathic_query->result_array() as $homeopathic_row) {
                    $listing_id = $homeopathic_row['user_id'];
                    $name = str_replace("null", "", $homeopathic_row['doctor_name']);
                    $field1 = str_replace("null", "", $homeopathic_row['speciality']);
                    $field2 = str_replace("null", "", $homeopathic_row['qualification']);
                    $field3 = str_replace("null", "", $homeopathic_row['address']);
                    $homeopathic_image = str_replace("null", "", @$doctor_row['image']);
                    if ($homeopathic_image != '') {
                     //   $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $homeopathic_image;
                         $image =  'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'. $homeopathic_image;
                    } else {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                    $homeopathic[] = array(
                        'listing_id' => $listing_id,
                        'name' => $name,
                        'image' => $image,
                        'field1' => $field1,
                        'field2' => $field2,
                        'field3' => $field3
                    );
                }
                $homeopathic_array[] = array(
                    'title' => 'Homeopathic',
                    'listing_type' => 9,
                    'array' => $homeopathic
                );
            } else {
                $homeopathic_array = array();
            }
            }*/

            // Labs
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
             
                $index_id="lab";
                   $labs=$this->elasticsearchsize3($index_id,$keyword);
            if (!empty($labs)) {
            
                $labs_array[] = array(
                    'title' => 'Labs',
                    'listing_type' => 10,
                     'redirection_type' => 12,
                    'array' => $labs
                );
            } else {
                $labs_array = array();
            }
            
             // Labs test
            /*$field1 = '';
            $field2 = '';
            $field3 = '';
           
             
                $index_id="lab_test";
                   $labs_test=$this->elasticsearchsize3($index_id,$keyword);
            if (!empty($labs_test)) {
            
                $labs_test_array[] = array(
                    'title' => 'Labs test',
                    'listing_type' => 10,
                    'array' => $labs_test
                );
            } else {
                $labs_test_array = array();
            }
            
             // Labs package
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
             
                $index_id="lab_package";
                   $labs_package=$this->elasticsearchsize3($index_id,$keyword);
            if (!empty($labs_package)) {
            
                $labs_package_array[] = array(
                    'title' => 'Labs Package',
                    'listing_type' => 10,
                    'array' => $labs_package
                );
            } else {
                $labs_package_array = array();
            }
            */
            

            // Nursing Attendant
            $field1 = '';
            $field2 = '';
            $field3 = '';
             
             
               $index_id="nursing_attendant";
                    $nursing=$this->elasticsearchsize3($index_id,$keyword);    
                if (!empty($nursing)) {
                    $nursing_array[] = array(
                        'title' => 'Nursing',
                        'listing_type' => 12,
                         'redirection_type' => 20,
                        'array' => $nursing
                    );
                } else {
                    $nursing_array = array();
                }
            


            /*// Cupping
            $field1 = '';
            $field2 = '';
            $field3 = '';
             if(count($parts) > 1)
            {
                for($i=0;$i<count($parts);$i++)
                {
                     $cupping_array = array();
                    $cupping_query = $this->db->query("SELECT user_id,name,address,image FROM cuppingtherapy WHERE name like '%$parts[$i]%' limit 2");
                    $cupping_count = $cupping_query->num_rows();
                    if ($cupping_count > 0) {
                        foreach ($cupping_query->result_array() as $cupping_row) {
                            $listing_id = $cupping_row['user_id'];
                            $name = str_replace("null", "", $cupping_row['name']);
                            $field1 = '';
                            $field2 = '';
                            $field3 = str_replace("null", "", $cupping_row['address']);
                            $cupping_image = str_replace("null", "", $cupping_row['image']);
                            if ($cupping_image != '') {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/' . $cupping_image;
                            } else {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                            }
                            $cupping[] = array(
                                'listing_id' => $listing_id,
                                'name' => $name,
                                'image' => $image,
                                'field1' => $field1,
                                'field2' => $field2,
                                'field3' => $field3
                            );
                        }
                        $cupping_array[] = array(
                            'title' => 'Cupping',
                            'listing_type' => 16,
                            'array' => $cupping
                        );
                    } else {
                        $cupping_array = array();
                    }
                }
            }else
            {
                 $cupping_query = $this->db->query("SELECT user_id,name,address,image FROM cuppingtherapy WHERE name like '%$keyword%' limit 2");
            $cupping_count = $cupping_query->num_rows();
            if ($cupping_count > 0) {
                foreach ($cupping_query->result_array() as $cupping_row) {
                    $listing_id = $cupping_row['user_id'];
                    $name = str_replace("null", "", $cupping_row['name']);
                    $field1 = '';
                    $field2 = '';
                    $field3 = str_replace("null", "", $cupping_row['address']);
                    $cupping_image = str_replace("null", "", $cupping_row['image']);
                    if ($cupping_image != '') {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/' . $cupping_image;
                    } else {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                    $cupping[] = array(
                        'listing_id' => $listing_id,
                        'name' => $name,
                        'image' => $image,
                        'field1' => $field1,
                        'field2' => $field2,
                        'field3' => $field3
                    );
                }
                $cupping_array[] = array(
                    'title' => 'Cupping',
                    'listing_type' => 16,
                    'array' => $cupping
                );
            } else {
                $cupping_array = array();
            }
            }

            // Physiotherapist
            $field1 = '';
            $field2 = '';
            $field3 = '';
             if(count($parts) > 1)
            {
                for($i=0;$i<count($parts);$i++)
                {
                    $physiotherapist_array = array();
                    $physiotherapist_query = $this->db->query("SELECT user_id,image,doctor_name,speciality,qualification,address FROM doctor_list WHERE doctor_name like '%$parts[$i]%' limit 2");
                    $physiotherapist_count = $physiotherapist_query->num_rows();
                    if ($physiotherapist_count > 0) {
                        foreach ($physiotherapist_query->result_array() as $physiotherapist_row) {
                            $listing_id = $physiotherapist_row['user_id'];
                            $name = str_replace("null", "", $physiotherapist_row['doctor_name']);
                            $field1 = str_replace("null", "", $physiotherapist_row['speciality']);
                            $field2 = str_replace("null", "", $physiotherapist_row['qualification']);
                            $field3 = str_replace("null", "", $physiotherapist_row['address']);
                            $physiotherapist_image = str_replace("null", "", @$doctor_row['image']);
                            if ($physiotherapist_image != '') {
                             //   $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $physiotherapist_image;
                                
                                $image =  'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'. $physiotherapist_image;
                            } else {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                            }
                            $physiotherapist[] = array(
                                'listing_id' => $listing_id,
                                'name' => $name,
                                'image' => $image,
                                'field1' => $field1,
                                'field2' => $field2,
                                'field3' => $field3
                            );
                        }
                        $physiotherapist_array[] = array(
                            'title' => 'Physiotherapist',
                            'listing_type' => 20,
                            'array' => $physiotherapist
                        );
                    } else {
                        $physiotherapist_array = array();
                    }
                }
            }else
            {
                 $physiotherapist_query = $this->db->query("SELECT user_id,image,doctor_name,speciality,qualification,address FROM doctor_list WHERE doctor_name like '%$keyword%' limit 2");
            $physiotherapist_count = $physiotherapist_query->num_rows();
            if ($physiotherapist_count > 0) {
                foreach ($physiotherapist_query->result_array() as $physiotherapist_row) {
                    $listing_id = $physiotherapist_row['user_id'];
                    $name = str_replace("null", "", $physiotherapist_row['doctor_name']);
                    $field1 = str_replace("null", "", $physiotherapist_row['speciality']);
                    $field2 = str_replace("null", "", $physiotherapist_row['qualification']);
                    $field3 = str_replace("null", "", $physiotherapist_row['address']);
                    $physiotherapist_image = str_replace("null", "", @$doctor_row['image']);
                    if ($physiotherapist_image != '') {
                     //   $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $physiotherapist_image;
                        
                        $image =  'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'. $physiotherapist_image;
                    } else {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                    $physiotherapist[] = array(
                        'listing_id' => $listing_id,
                        'name' => $name,
                        'image' => $image,
                        'field1' => $field1,
                        'field2' => $field2,
                        'field3' => $field3
                    );
                }
                $physiotherapist_array[] = array(
                    'title' => 'Physiotherapist',
                    'listing_type' => 20,
                    'array' => $physiotherapist
                );
            } else {
                $physiotherapist_array = array();
            }
            }*/

            // Fitness Center
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
                
                  $index_id="fitness";
                        
                     $fitness=$this->elasticsearchsize3($index_id,$keyword);   
                    if (!empty($fitness)) {
                        $fitness_array[] = array(
                            'title' => 'Fitness',
                            'listing_type' => 6,
                             'redirection_type' => 18,
                            'array' => $fitness
                        );
                    } else {
                        $fitness_array = array();
                    }
            
             // Spa Center
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
                
                    $index_id="spa_listing";
                        
                    $spa_center=$this->elasticsearchspa($index_id,$keyword);
              
                    if (!empty($spa_center)) {
                        foreach($spa_center as $sp_c){
                       $spa_array[]=array('listing_id'=>$sp_c['user_id'],
                        'branch_id'=>$sp_c['id'],
                        'name'=>$sp_c['branch_name'],
                        'image'=>$sp_c['branch_image'],
                        'address'=>$sp_c['branch_address'], 
                        'field2'=>"",
                         'listing_type' => 36,
                        'field3'=>"");
                        }
                        
                        $spa_center_array[] = array(
                            'title' => 'Spa Center',
                            'listing_type' => 36,
                             'redirection_type' => 15,
                            'array' => $spa_array
                        );
                    } else {
                        $spa_center_array = array();
                    }
                    
              // Spa Package
          /*  $field1 = '';
            $field2 = '';
            $field3 = '';
           
                
                  $index_id="spa_package";
                        
                     $spa_package=$this->elasticsearchspa_package($index_id,$keyword);
                   
                    if (!empty($spa_package)) {
                        foreach($spa_package as $sp_c){
                       $spa_parray[]=array('listing_id'=>$sp_c['package_id'],
                        'name'=>$sp_c['package_name'],
                        'image'=>$sp_c['package_image'],
                        'price'=>$sp_c['package_price'], 
                        'centername'=>$sp_c['branch_name'],
                         'listing_type' => 36,
                        'listing_id'=>$sp_c['user_id'],
                        'package_id'=>$sp_c['package_id']);
                        }
                        
                        $spa_center_array[] = array(
                            'title' => 'Spa Package',
                            'listing_type' => 36,
                            'array' => $spa_parray
                        );
                    } else {
                        $spa_center_array = array();
                    }  */      
            

            // Hospital
            $field1 = '';
            $field2 = '';
            $field3 = '';
           $index_id="hospital";
                  $hospital=$this->elasticsearchsize3($index_id,$keyword);  
            if (!empty($hospital)) {
                 
                $hospital_array[] = array(
                    'title' => 'Hospital',
                    'listing_type' => 8,
                     'redirection_type' => 19,
                    'array' => $hospital
                );
            } else {
                $hospital_array = array();
            }
           
           
              // hospital_package
          /*  $field1 = '';
            $field2 = '';
            $field3 = '';
           
                
                  $index_id="hospital_package";
                        
                     $hospital_package=$this->elasticsearchhospital_package($index_id,$keyword);
                 
                    if (!empty($hospital_package)) {
                        foreach($hospital_package as $sp_c){
                       $hospital_parray[]=array('id'=>$sp_c['id'],
                        'name'=>$sp_c['package_name'],
                        'package_desc'=>$sp_c['package_desc'],
                        'package_amount'=>$sp_c['booking_amount'], 
                        'hospital_id'=>$sp_c['hospital_id'],
                        'type'=>$sp_c['treatment_type'],
                        'cat_id'=>$sp_c['package_type']);
                        }
                        
                        $hospital_package_array[] = array(
                            'title' => 'hospital Package',
                            'listing_type' => 8,
                            'array' => $hospital_parray
                        );
                    } else {
                        $hospital_package_array = array();
                    }       
*/
            // Post
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
            $healthwall_array = array();
            // Articles
              $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
              if(!empty($query_lang)) {
              $lang=$query_lang->language;
              }
              else
              {
                  $lang =0;
              }
              
               function RemoveBS($Str) {
            $StrArr = str_split($Str);
            $NewStr = '';
            foreach ($StrArr as $Char) {
                $CharNo = ord($Char);
                if ($CharNo == 163) {
                    $NewStr .= $Char;
                    continue;
                } // keep £
                if ($CharNo > 31 && $CharNo < 127) {
                    $NewStr .= $Char;
                }
            }
            return $NewStr;
        }
            
            if(count($parts) > 1)
            {
                for($i=0;$i<count($parts);$i++)
                {
                     $article_array=array();
                    $query = $this->db->query("SELECT `id`, `cat_id`, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date` FROM article where  (article_title like '% $parts[$i] %' or article_description like '% $parts[$i] %') and language='$lang' limit 2");
                  
                    $count = $query->num_rows();
                    if ($count > 0) {
                        foreach ($query->result_array() as $row) {
                            $article_id = $row['id'];
                            $cat_id = $row['cat_id'];
                            
                            if($lang == 1)
                        {
                            $article_title = $this->decrypt(($row['article_title']));
                            $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
                
                        }
                        else
                        {
                            $article_title = RemoveBS($row['article_title']);
                        //    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                           $article_description = $row['article_description'];
                        }
                            
                            // $article_title = $row['article_title'];
                            // $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                            
                            $article_image = $row['image'];
                            $article_date = $row['posted'];
                            $author = 'Medicalwale.com';
                            $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;
        
                            $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                            $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                            $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                            $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
        
                            $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();
                            $article_list[] = array(
                                'article_id' => $article_id,
                                'category_id' => $cat_id,
                                'article_title' => $article_title,
                                'article_description' => $article_description,
                                'article_image' => $article_image,
                                'article_date' => $article_date,
                                'author' => $author,
                                'like_count' => $like_count,
                                'like_yes_no' => $like_yes_no,
                                'total_views' => $views_count,
                                'is_bookmark' => $is_bookmark,
                                'total_bookmark' => $total_bookmark
                            );
                        }
                        $article_array[] = array(
                            'title' => 'Articles',
                            'listing_type' => 32,
                             'redirection_type' => 6,
                            'array' => $article_list
                        );
                       
                    } else {
                        $article_array = array();
                    }
                }
            }
            else
            {
                 $query = $this->db->query("SELECT `id`, `cat_id`, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date` FROM article where  (article_title like '% $keyword %' or article_description like '% $keyword %') limit 2");
              
                $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $article_id = $row['id'];
                        $cat_id = $row['cat_id'];
                        
                        if($lang == 1)
                    {
                        $article_title = $this->decrypt(($row['article_title']));
                        $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
            
                    }
                    else
                    {
                        $article_title = RemoveBS($row['article_title']);
                    //    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                       $article_description = $row['article_description'];
                    }
                        
                        // $article_title = $row['article_title'];
                        // $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                        
                        $article_image = $row['image'];
                        $article_date = $row['posted'];
                        $author = 'Medicalwale.com';
                        $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;
    
                        $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                        $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                        $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                        $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
    
                        $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();
                        $article_list[] = array(
                            'article_id' => $article_id,
                            'category_id' => $cat_id,
                            'article_title' => $article_title,
                            'article_description' => $article_description,
                            'article_image' => $article_image,
                            'article_date' => $article_date,
                            'author' => $author,
                            'like_count' => $like_count,
                            'like_yes_no' => $like_yes_no,
                            'total_views' => $views_count,
                            'is_bookmark' => $is_bookmark,
                            'total_bookmark' => $total_bookmark
                        );
                    }
                    $article_array[] = array(
                        'title' => 'Articles',
                        'listing_type' => 32,
                        'array' => $article_list
                    );
                } else {
                    $article_array = array();
                }
            }
        
              if(count($parts) > 1)
            {
                for($i=0;$i<count($parts);$i++)
                {
                     $article_array=array();
                    $query = $this->db->query("SELECT article.id, `cat_id`, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date`,article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id where  (article_title like '% $parts[$i] %' or article_description like '% $parts[$i] %') and article_category.type='tips' and  language='$lang' limit 2");
                  
                    $count = $query->num_rows();
                    if ($count > 0) {
                        foreach ($query->result_array() as $row) {
                            $article_id = $row['id'];
                            $cat_id = $row['cat_id'];
                            
                            if($lang == 1)
                        {
                            $article_title = $this->decrypt(($row['article_title']));
                            $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
                
                        }
                        else
                        {
                            $article_title = RemoveBS($row['article_title']);
                        //    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                           $article_description = $row['article_description'];
                        }
                            
                            // $article_title = $row['article_title'];
                            // $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                            
                            $article_image = $row['image'];
                            $article_date = $row['posted'];
                            $author = 'Medicalwale.com';
                            $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;
        
                            $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                            $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                            $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                            $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
        
                            $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();
                            $tips_list[] = array(
                                'article_id' => $article_id,
                                'category_id' => $cat_id,
                                'article_title' => $article_title,
                                'article_description' => $article_description,
                                'article_image' => $article_image,
                                'article_date' => $article_date,
                                'author' => $author,
                                'like_count' => $like_count,
                                'like_yes_no' => $like_yes_no,
                                'total_views' => $views_count,
                                'is_bookmark' => $is_bookmark,
                                'total_bookmark' => $total_bookmark
                            );
                        }
                        $tips_array[] = array(
                            'title' => 'Tips',
                            'listing_type' => 32,
                             'redirection_type' => 7,
                            'array' => $tips_list
                        );
                       
                    } else {
                        $tips_array = array();
                    }
                }
            }
            else
            {
                 $query = $this->db->query("SELECT article.id, `cat_id`, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date`,article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id where  (article_title like '% $keyword %' or article_description like '% $keyword %') and article_category.type='tips' limit 2");
              
                $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $article_id = $row['id'];
                        $cat_id = $row['cat_id'];
                        
                        if($lang == 1)
                    {
                        $article_title = $this->decrypt(($row['article_title']));
                        $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
            
                    }
                    else
                    {
                        $article_title = RemoveBS($row['article_title']);
                    //    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                       $article_description = $row['article_description'];
                    }
                        
                        // $article_title = $row['article_title'];
                        // $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                        
                        $article_image = $row['image'];
                        $article_date = $row['posted'];
                        $author = 'Medicalwale.com';
                        $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;
    
                        $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                        $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                        $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                        $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
    
                        $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();
                        $tips_list[] = array(
                            'article_id' => $article_id,
                            'category_id' => $cat_id,
                            'article_title' => $article_title,
                            'article_description' => $article_description,
                            'article_image' => $article_image,
                            'article_date' => $article_date,
                            'author' => $author,
                            'like_count' => $like_count,
                            'like_yes_no' => $like_yes_no,
                            'total_views' => $views_count,
                            'is_bookmark' => $is_bookmark,
                            'total_bookmark' => $total_bookmark
                        );
                    }
                    $tips_array[] = array(
                        'title' => 'Tips',
                        'listing_type' => 32,
                        'redirection_type' => 7,
                        'array' => $tips_list
                    );
                } else {
                    $tips_array = array();
                }
            }
        

            if(count($parts) > 1)
                {
                    for($i=0;$i<count($parts);$i++)
                    {
                         $article_array=array();
                        $query = $this->db->query("SELECT article.id, `cat_id`, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date`,article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id where  (article_title like '% $parts[$i] %' or article_description like '% $parts[$i] %') and article_category.type='skin_hair' and  language='$lang' limit 2");
                      
                        $count = $query->num_rows();
                        if ($count > 0) {
                            foreach ($query->result_array() as $row) {
                                $article_id = $row['id'];
                                $cat_id = $row['cat_id'];
                                
                                if($lang == 1)
                            {
                                $article_title = $this->decrypt(($row['article_title']));
                                $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
                    
                            }
                            else
                            {
                                $article_title = RemoveBS($row['article_title']);
                            //    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                               $article_description = $row['article_description'];
                            }
                                
                                // $article_title = $row['article_title'];
                                // $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                                
                                $article_image = $row['image'];
                                $article_date = $row['posted'];
                                $author = 'Medicalwale.com';
                                $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;
            
                                $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                                $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                                $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                                $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
            
                                $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();
                                $skin_list[] = array(
                                    'article_id' => $article_id,
                                    'category_id' => $cat_id,
                                    'article_title' => $article_title,
                                    'article_description' => $article_description,
                                    'article_image' => $article_image,
                                    'article_date' => $article_date,
                                    'author' => $author,
                                    'like_count' => $like_count,
                                    'like_yes_no' => $like_yes_no,
                                    'total_views' => $views_count,
                                    'is_bookmark' => $is_bookmark,
                                    'total_bookmark' => $total_bookmark
                                );
                            }
                            $skin_array[] = array(
                                'title' => 'Skin Disease',
                                'listing_type' => 32,
                                'redirection_type' => 8,
                                'array' => $skin_list
                            );
                           
                        } else {
                            $skin_array = array();
                        }
                    }
                }
                else
                {
                     $query = $this->db->query("SELECT article.id, `cat_id`, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date`,article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id where  (article_title like '% $keyword %' or article_description like '% $keyword %') and article_category.type='skin_hair' limit 2");
                  
                    $count = $query->num_rows();
                    if ($count > 0) {
                        foreach ($query->result_array() as $row) {
                            $article_id = $row['id'];
                            $cat_id = $row['cat_id'];
                            
                            if($lang == 1)
                        {
                            $article_title = $this->decrypt(($row['article_title']));
                            $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
                
                        }
                        else
                        {
                            $article_title = RemoveBS($row['article_title']);
                        //    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                           $article_description = $row['article_description'];
                        }
                            
                            // $article_title = $row['article_title'];
                            // $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                            
                            $article_image = $row['image'];
                            $article_date = $row['posted'];
                            $author = 'Medicalwale.com';
                            $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;
        
                            $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                            $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                            $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                            $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
        
                            $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();
                            $skin_list[] = array(
                                'article_id' => $article_id,
                                'category_id' => $cat_id,
                                'article_title' => $article_title,
                                'article_description' => $article_description,
                                'article_image' => $article_image,
                                'article_date' => $article_date,
                                'author' => $author,
                                'like_count' => $like_count,
                                'like_yes_no' => $like_yes_no,
                                'total_views' => $views_count,
                                'is_bookmark' => $is_bookmark,
                                'total_bookmark' => $total_bookmark
                            );
                        }
                        $skin_array[] = array(
                            'title' => 'Skin Disease',
                            'listing_type' => 32,
                            'redirection_type' => 8,
                            'array' => $skin_list
                        );
                    } else {
                        $skin_array = array();
                    }
                }
        
            if(count($parts) > 1)
            {
                for($i=0;$i<count($parts);$i++)
                {
                     $article_array=array();
                    $query = $this->db->query("SELECT article.id, `cat_id`, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date`,article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id where  (article_title like '% $parts[$i] %' or article_description like '% $parts[$i] %') and article_category.type='home_remedies' and  language='$lang' limit 2");
                  
                    $count = $query->num_rows();
                    if ($count > 0) {
                        foreach ($query->result_array() as $row) {
                            $article_id = $row['id'];
                            $cat_id = $row['cat_id'];
                            
                            if($lang == 1)
                        {
                            $article_title = $this->decrypt(($row['article_title']));
                            $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
                
                        }
                        else
                        {
                            $article_title = RemoveBS($row['article_title']);
                        //    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                           $article_description = $row['article_description'];
                        }
                            
                            // $article_title = $row['article_title'];
                            // $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                            
                            $article_image = $row['image'];
                            $article_date = $row['posted'];
                            $author = 'Medicalwale.com';
                            $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;
        
                            $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                            $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                            $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                            $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
        
                            $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();
                            $mom_list[] = array(
                                'article_id' => $article_id,
                                'category_id' => $cat_id,
                                'article_title' => $article_title,
                                'article_description' => $article_description,
                                'article_image' => $article_image,
                                'article_date' => $article_date,
                                'author' => $author,
                                'like_count' => $like_count,
                                'like_yes_no' => $like_yes_no,
                                'total_views' => $views_count,
                                'is_bookmark' => $is_bookmark,
                                'total_bookmark' => $total_bookmark
                            );
                        }
                        $mom_array[] = array(
                            'title' => 'Mom Remedies',
                            'listing_type' => 32,
                            'redirection_type' => 9,
                            'array' => $mom_list
                        );
                       
                    } else {
                        $mom_array = array();
                    }
                }
            }
            else
            {
                 $query = $this->db->query("SELECT article.id, `cat_id`, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date`,article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id where  (article_title like '% $keyword %' or article_description like '% $keyword %') and article_category.type='home_remedies' limit 2");
              
                $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $article_id = $row['id'];
                        $cat_id = $row['cat_id'];
                        
                        if($lang == 1)
                    {
                        $article_title = $this->decrypt(($row['article_title']));
                        $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
            
                    }
                    else
                    {
                        $article_title = RemoveBS($row['article_title']);
                    //    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                       $article_description = $row['article_description'];
                    }
                        
                        // $article_title = $row['article_title'];
                        // $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                        
                        $article_image = $row['image'];
                        $article_date = $row['posted'];
                        $author = 'Medicalwale.com';
                        $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;
    
                        $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                        $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                        $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                        $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
    
                        $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();
                        $mom_list[] = array(
                            'article_id' => $article_id,
                            'category_id' => $cat_id,
                            'article_title' => $article_title,
                            'article_description' => $article_description,
                            'article_image' => $article_image,
                            'article_date' => $article_date,
                            'author' => $author,
                            'like_count' => $like_count,
                            'like_yes_no' => $like_yes_no,
                            'total_views' => $views_count,
                            'is_bookmark' => $is_bookmark,
                            'total_bookmark' => $total_bookmark
                        );
                    }
                    $mom_array[] = array(
                        'title' => 'Mom Remedies',
                        'listing_type' => 32,
                        'redirection_type' => 9,
                        'array' => $mom_list
                    );
                } else {
                    $mom_array = array();
                }
            }

            // Survivor's Stories
            if(count($parts) > 1)
            {
                for($i=0;$i<count($parts);$i++)
                {
                    $survivor_array = array();
                    $survivor_query = $this->db->query("SELECT id,title,description,tag,author,image,date FROM `survival_stories` where is_active='1' and (title like '%$parts[$i]%'  or description like '%$parts[$i]%') order by id desc limit 2");
                    $survivor_count = $survivor_query->num_rows();
                    if ($survivor_count > 0) {
                        foreach ($survivor_query->result_array() as $row) {
                            $id = $row['id'];
                            $title = $row['title'];
                            $description = $row['description'];
                            $tag = $row['tag'];
                            $author = $row['author'];
                            $image = $row['image'];
                            $date = $row['date'];
                            $image = str_replace(" ", "", $image);
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/survival_story_images/' . $image;
        
                            $bookmark_yes_no = $this->db->select('id')->from('survival_stories_bookmark')->where('user_id', $user_id)->where('survival_stories_id', $id)->get()->num_rows();
        
                            $share = 'https://d2c8oti4is0ms3.cloudfront.net/images/';
        
                            $survivor_list[] = array(
                                "id" => $id,
                                "title" => $title,
                                'description' => $description,
                                'tag' => $tag,
                                'author' => $author,
                                'image' => $image,
                                'date' => $date,
                                'share' => $share
                            );
                        }
                        $survivor_array[] = array(
                            'title' => "Survivor's Stories",
                            'listing_type' => 33,
                            'redirection_type' => 10,
                            'array' => $survivor_list
                        );
                    } else {
                        $survivor_array = array();
                    }
                }
            }
            else
            {
                 $survivor_query = $this->db->query("SELECT id,title,description,tag,author,image,date FROM `survival_stories` where is_active='1' and (title like '%$keyword%'  or description like '%$keyword%') order by id desc limit 2");
            $survivor_count = $survivor_query->num_rows();
            if ($survivor_count > 0) {
                foreach ($survivor_query->result_array() as $row) {
                    $id = $row['id'];
                    $title = $row['title'];
                    $description = $row['description'];
                    $tag = $row['tag'];
                    $author = $row['author'];
                    $image = $row['image'];
                    $date = $row['date'];
                    $image = str_replace(" ", "", $image);
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/survival_story_images/' . $image;

                    $bookmark_yes_no = $this->db->select('id')->from('survival_stories_bookmark')->where('user_id', $user_id)->where('survival_stories_id', $id)->get()->num_rows();

                    $share = 'https://d2c8oti4is0ms3.cloudfront.net/images/';

                    $survivor_list[] = array(
                        "id" => $id,
                        "title" => $title,
                        'description' => $description,
                        'tag' => $tag,
                        'author' => $author,
                        'image' => $image,
                        'date' => $date,
                        'share' => $share
                    );
                }
                $survivor_array[] = array(
                    'title' => "Survivor's Stories",
                    'listing_type' => 33,
                    'redirection_type' => 10,
                    'array' => $survivor_list
                );
            } else {
                $survivor_array = array();
            }

            }
            
            // health mall
            
             
            $field1 = '';
            $field2 = '';
            $field3 = '';
            $index_id="hm_product";
            $hm_product=$this->elasticsearchhm_product($index_id,$keyword); 
            if (!empty($hm_product)) {
                
                 foreach($hm_product as $a){
                    $string = strip_tags($a['pd_short_desc']);
                         if (strlen($string) > 80) {
                    $stringCut = substr($string, 0, 80);
                    $endPoint = strrpos($stringCut, ' ');
                    $string = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                    $string .= '...';
                         }
                  $hm_product1[] = array(
                        'listing_id' => $a['pd_id'],
                        'name' => $a['pd_name'],
                        'image' => $a['pd_photo_1'],
                        'field1' => $a['pd_mrp_price'],
                        'field2' =>$a['pd_vendor_price'],
                        'field3' => $string
                    );
                 }
               $hm_product_array[] = array(
                    'title' => 'Health Mall',
                    'listing_type' => 34,
                    'redirection_type' => 22,
                    'array' => $hm_product1
                );
            } else {
                $hm_product_array = array();
            }

            $resultpost = array_merge($people_array, $doctor_array,$dental_clinic_array, $pharmacy_array,  $labs_array, $nursing_array, $fitness_array,$spa_center_array, $hospital_array, $healthwall_array, $article_array,$tips_array,$skin_array,$mom_array, $survivor_array,$hm_product_array);
           
       
       
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    
    //added by zak on 18-09-2018 
    //start
   public function search_list_by_category($user_id, $keyword ,$type)
    {
         if ($user_id > 0) {
                  if($type == '1')
                  {
                       // People
            $field1 = '';
            $field2 = '';
            $field3 = '';
                    $index_id="people";
                    $people=$this->elasticsearchsize3($index_id,$keyword);
                  $people_count=count($people);
                
                    if ($people_count > 0) {
                        $people_array[] = array(
                            'title' => 'People',
                            'listing_type' => 0,
                            'array' => $people
                        );
                        return $people_array;
                    } else {
                       return $people_array = array();
                    } 
                  }
                  else if($type == '2')
                  {
                        // Doctor
            $field1 = '';
            $field2 = '';
            $field3 = '';
             $index_id="doctor";
            $doctor=$this->elasticsearchsize3($index_id,$keyword);
           $doctor_count=count($doctor);
            if ($doctor_count > 0) {
               
                $doctor_array[] = array(
                    'title' => 'Doctor',
                    'listing_type' => 5,
                    'array' => $doctor
                );
                
                return $doctor_array;
            } else {
              return  $doctor_array = array();
            }
                  }
                  else if($type == '3')
                  {
                        // Doctor
            $field1 = '';
            $field2 = '';
            $field3 = '';
             $index_id="dentist_clinic_list";
            $dental_clinic=$this->elasticsearchsize3($index_id,$keyword);
           $doctor_count=count($dental_clinic);
            if ($doctor_count > 0) {
               
                $dental_clinic_array[] = array(
                    'title' => 'Dental Clinic',
                    'listing_type' => 39,
                    'array' => $dental_clinic
                );
                
                return $dental_clinic_array;
            } else {
              return  $dental_clinic_array = array();
            }
                  }
                  else if($type == '4')
                  {
                       // Homeopathic
            $field1 = '';
            $field2 = '';
            $field3 = '';
            $homeopathic_query = $this->db->query("SELECT user_id,image,doctor_name,speciality,qualification,address FROM doctor_list WHERE doctor_name like '%$keyword%'");
            $homeopathic_count = $homeopathic_query->num_rows();
            if ($homeopathic_count > 0) {
                foreach ($homeopathic_query->result_array() as $homeopathic_row) {
                    $user_id = $homeopathic_row['user_id'];
                    $name = str_replace("null", "", $homeopathic_row['doctor_name']);
                    $field1 = str_replace("null", "", $homeopathic_row['speciality']);
                    $field2 = str_replace("null", "", $homeopathic_row['qualification']);
                    $field3 = str_replace("null", "", $homeopathic_row['address']);
                    $homeopathic_image = str_replace("null", "", $homeopathic_row['image']);
                    if ($homeopathic_image != '') {
                     //   $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $homeopathic_image;
                      $image =  'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'. $homeopathic_image;
                    } else {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                    $homeopathic[] = array(
                        'listing_id' => $user_id,
                        'name' => $name,
                        'image' => $image,
                        'field1' => $field1,
                        'field2' => $field2,
                        'field3' => $field3
                    );
                }
                
                $homeopathic_array[] = array(
                    'title' => 'Homeopathic',
                    'listing_type' => 9,
                    'array' => $homeopathic
                );
                return $homeopathic_array;
            } else {
              return $homeopathic_array = array();
            }
                  }
                  else if($type == '5')
                  {
                      $field1 = '';
            $field2 = '';
            $field3 = '';
            $physiotherapist_query = $this->db->query("SELECT user_id,image,doctor_name,speciality,qualification,address FROM doctor_list WHERE doctor_name like '%$keyword%'");
            $physiotherapist_count = $physiotherapist_query->num_rows();
            if ($physiotherapist_count > 0) {
                foreach ($physiotherapist_query->result_array() as $physiotherapist_row) {
                    $user_id = $physiotherapist_row['user_id'];
                    $name = str_replace("null", "", $physiotherapist_row['doctor_name']);
                    $field1 = str_replace("null", "", $physiotherapist_row['speciality']);
                    $field2 = str_replace("null", "", $physiotherapist_row['qualification']);
                    $field3 = str_replace("null", "", $physiotherapist_row['address']);
                    $physiotherapist_image = str_replace("null", "", $physiotherapist_row['image']);
                    if ($physiotherapist_image != '') {
                      //  $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $physiotherapist_image;
                      
                       $image =  'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'. $physiotherapist_image;
                    } else {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                    $physiotherapist[] = array(
                        'listing_id' => $user_id,
                        'name' => $name,
                        'image' => $image,
                        'field1' => $field1,
                        'field2' => $field2,
                        'field3' => $field3
                    );
                }
                $physiotherapist_array[] = array(
                    'title' => 'Physiotherapist',
                    'listing_type' => 20,
                    'array' => $physiotherapist
                );
                
                return $physiotherapist_array;
            } else {
                return $physiotherapist_array = array();
            }
                  }
                /*  else if($type == 'Post')
                  {
                        // Post
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

            $media_array = array();
            $query = $this->db->query("select posts.id as post_id,posts.type as post_type,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id from posts INNER JOIN users on users.id=posts.user_id where posts.user_id<>'' and posts.user_id<>'0' and users.name like '%$keyword%' order by posts.id desc limit 50");
          //  $query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id, IFNULL(posts.repost_user_id,'') AS repost_user_id,posts.repost_location,posts.is_repost,posts.repost_time, posts.type as post_type,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC limit 50");
            $healthwall_count = $query->num_rows();
            if ($healthwall_count > 0) {
                foreach ($query->result_array() as $row) {
                    $post_id = $row['post_id'];
                    $listing_type = $row['vendor_id'];
                    $post_id = $row['post_id'];
                    $post = $row['post'];
                    $category = '';
                    $is_anonymous = $row['is_anonymous'];
                    $tag = $row['tag'];
                    $post_type = $row['post_type'];
                    $date = $row['created_at'];
                    $caption = $row['caption'];
                    
                    $username = $row['name'];
                    $post_user_id = $row['post_user_id'];

                    $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                    if ($img_count > 0) {
                        $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                        $img_file = $profile_query->source;
                        $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                    } else {
                        $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }

                    $media_query = $this->db->query("SELECT media.id AS media_id,media.type AS media_type,media.caption,media.source,post_media.img_width,post_media.img_height,post_media.video_width,post_media.video_height FROM media INNER JOIN post_media on media.id=post_media.media_id where post_media.post_id='$post_id'");
                    $img_val = '';
                    $images = '';
                    $img_comma = '';
                    $img_width = '';
                    $img_height = '';
                    $video_width = '';
                    $video_height = '';
                    foreach ($media_query->result_array() as $media_row) {
                        $media_id = $media_row['media_id'];
                        $media_type = $media_row['media_type'];
                        $source = $media_row['source'];
                        $images = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/' . $source;
                        $caption = $media_row['caption'];
                        $img_width = $media_row['img_width'];
                        $img_height = $media_row['img_height'];
                        $video_width = $media_row['video_width'];
                        $video_height = $media_row['video_height'];

                        $media_array[] = array(
                            'media_id' => $media_id,
                            'type' => $media_type,
                            'images' => $images,
                            'caption' => $caption,
                            'img_height' => $img_height,
                            'img_width' => $img_width,
                            'video_height' => $video_height,
                            'video_width' => $video_width
                        );
                    }

                    $like_count = $this->db->select('id')->from('post_likes')->where('post_id', $post_id)->get()->num_rows();
                    $comment_count = $this->db->select('id')->from('comments')->where('post_id', $post_id)->get()->num_rows();
                    $follow_count = $this->db->select('id')->from('post_follows')->where('post_id', $post_id)->get()->num_rows();
                    $view_count = $this->db->select('id')->from('post_views')->where('post_id', $post_id)->get()->num_rows();

                    $like_yes_no_query = $this->db->query("SELECT id from post_likes where post_id='$post_id' and user_id='$user_id'");
                    $like_yes_no = $like_yes_no_query->num_rows();

                    $follow_yes_no_query = $this->db->query("SELECT id from post_follows where post_id='$post_id' and user_id='$user_id'");
                    $follow_post_yes_no = $follow_yes_no_query->num_rows();

                    $view_yes_no_query = $this->db->query("SELECT id from post_views where post_id='$post_id' and user_id='$user_id'");
                    $view_post_yes_no = $view_yes_no_query->num_rows();

                    $date = get_time_difference_php($date);
                    $healthwall_list[] = array(
                        'id' => $post_id,
                        'post_user_id' => $post_user_id,
                        'listing_type' => $listing_type,
                        'username' => $username,
                        'userimage' => $userimage,
                        'post_type' => $post_type,
                        'post' => $post,
                        'is_anonymous' => $is_anonymous,
                        'tag' => $tag,
                        'category' => $category,
                        'like_count' => $like_count,
                        'follow_count' => $follow_count,
                        'like_yes_no' => $like_yes_no,
                        'follow_post_yes_no' => $follow_post_yes_no,
                        'comment_count' => $comment_count,
                        'views' => $view_count,
                        'view_yes_no' => $view_post_yes_no,
                        'media' => $media_array,
                        'date' => $date
                    );
                }
                $healthwall_array[] = array(
                    'title' => 'Post',
                    'listing_type' => 31,
                    'array' => $healthwall_list
                );
                
                return $healthwall_array;
            } else {
              return $healthwall_array = array();
            }
                  }*/
                  else if($type == '6')
                  {
                      
                       $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
              
              $lang=$query_lang->language;
              
               function RemoveBS($Str) {
            $StrArr = str_split($Str);
            $NewStr = '';
            foreach ($StrArr as $Char) {
                $CharNo = ord($Char);
                if ($CharNo == 163) {
                    $NewStr .= $Char;
                    continue;
                } // keep £
                if ($CharNo > 31 && $CharNo < 127) {
                    $NewStr .= $Char;
                }
            }
            return $NewStr;
        }
                         // Articles
            $query = $this->db->query("SELECT `id`, `cat_id`, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date` FROM article where  (article_title like '%$keyword%' or article_description like '%$keyword%') limit 20");

            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $article_id = $row['id'];
                    $cat_id = $row['cat_id'];
                  //  $article_title = $row['article_title'];
                //    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                    
                      if($lang == 1)
                {
                    $article_title = $this->decrypt(($row['article_title']));
                    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
        
                }
                else
                {
                    $article_title = RemoveBS($row['article_title']);
                //    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                   $article_description = $row['article_description'];
                }
                    
                    $article_image = $row['image'];
                    $article_date = $row['posted'];
                    $author = 'Medicalwale.com';
                    $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;

                    $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                    $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                    $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                    $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();

                    $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();
                    $article_list[] = array(
                        'article_id' => $article_id,
                        'category_id' => $cat_id,
                        'article_title' => $article_title,
                        'article_description' => $article_description,
                        'short_desc' =>"",
                        'article_image' => $article_image,
                        'article_date' => $article_date,
                        'share_url' => "",
                        'author' => $author,
                        'like_count' => $like_count,
                        'like_yes_no' => $like_yes_no,
                        'total_views' => $views_count,
                        'is_bookmark' => $is_bookmark,
                        'total_bookmark' => $total_bookmark,
                        'is_follow' => "No",
                        'total_follow' =>0
                    );
                }
                $article_array[] = array(
                    'title' => 'Articles',
                    'listing_type' => 32,
                    'array' => $article_list
                );
                
                return $article_array;
            } else {
               return $article_array = array();
            }
                  }
                    else if($type == '7')
                  {
                      
                       $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
              
              $lang=$query_lang->language;
              
               function RemoveBS($Str) {
            $StrArr = str_split($Str);
            $NewStr = '';
            foreach ($StrArr as $Char) {
                $CharNo = ord($Char);
                if ($CharNo == 163) {
                    $NewStr .= $Char;
                    continue;
                } // keep £
                if ($CharNo > 31 && $CharNo < 127) {
                    $NewStr .= $Char;
                }
            }
            return $NewStr;
        }
                         // Articles
            $query = $this->db->query("SELECT article.id, `cat_id`, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date`, article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id  where  (article_title like '%$keyword%' or article_description like '%$keyword%') and article_category.type='tips'  limit 20");

            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $article_id = $row['id'];
                    $cat_id = $row['cat_id'];
                  //  $article_title = $row['article_title'];
                //    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                    
                      if($lang == 1)
                {
                    $article_title = $this->decrypt(($row['article_title']));
                    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
        
                }
                else
                {
                    $article_title = RemoveBS($row['article_title']);
                //    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                   $article_description = $row['article_description'];
                }
                    
                    $article_image = $row['image'];
                    $article_date = $row['posted'];
                    $author = 'Medicalwale.com';
                    $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;

                    $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                    $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                    $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                    $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();

                    $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();
                    $tips_list[] = array(
                        'article_id' => $article_id,
                        'category_id' => $cat_id,
                        'article_title' => $article_title,
                        'article_description' => $article_description,
                        'short_desc' =>"",
                        'article_image' => $article_image,
                        'article_date' => $article_date,
                        'share_url' => "",
                        'author' => $author,
                        'like_count' => $like_count,
                        'like_yes_no' => $like_yes_no,
                        'total_views' => $views_count,
                        'is_bookmark' => $is_bookmark,
                        'total_bookmark' => $total_bookmark,
                        'is_follow' => "No",
                        'total_follow' =>0
                    );
                }
                $tips_array[] = array(
                    'title' => 'Tips',
                    'listing_type' => 32,
                    'array' => $tips_list
                );
                
                return $tips_array;
            } else {
               return $tips_array = array();
            }
                  }
                   else if($type == '8')
                  {
                      
                       $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
              
              $lang=$query_lang->language;
              
               function RemoveBS($Str) {
            $StrArr = str_split($Str);
            $NewStr = '';
            foreach ($StrArr as $Char) {
                $CharNo = ord($Char);
                if ($CharNo == 163) {
                    $NewStr .= $Char;
                    continue;
                } // keep £
                if ($CharNo > 31 && $CharNo < 127) {
                    $NewStr .= $Char;
                }
            }
            return $NewStr;
        }
                         // Articles
            $query = $this->db->query("SELECT article.id, `cat_id`, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date`, article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id  where  (article_title like '%$keyword%' or article_description like '%$keyword%') and article_category.type='skin_hair'  limit 20");

            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $article_id = $row['id'];
                    $cat_id = $row['cat_id'];
                  //  $article_title = $row['article_title'];
                //    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                    
                      if($lang == 1)
                {
                    $article_title = $this->decrypt(($row['article_title']));
                    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
        
                }
                else
                {
                    $article_title = RemoveBS($row['article_title']);
                //    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                   $article_description = $row['article_description'];
                }
                    
                    $article_image = $row['image'];
                    $article_date = $row['posted'];
                    $author = 'Medicalwale.com';
                    $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;

                    $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                    $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                    $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                    $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();

                    $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();
                    $skin_list[] = array(
                        'article_id' => $article_id,
                        'category_id' => $cat_id,
                        'article_title' => $article_title,
                        'article_description' => $article_description,
                        'short_desc' =>"",
                        'article_image' => $article_image,
                        'article_date' => $article_date,
                        'share_url' => "",
                        'author' => $author,
                        'like_count' => $like_count,
                        'like_yes_no' => $like_yes_no,
                        'total_views' => $views_count,
                        'is_bookmark' => $is_bookmark,
                        'total_bookmark' => $total_bookmark,
                        'is_follow' => "No",
                        'total_follow' =>0
                    );
                }
                $skin_array[] = array(
                    'title' => 'Skin Disease',
                    'listing_type' => 32,
                    'array' => $skin_list
                );
                
                return $skin_array;
            } else {
               return $skin_array = array();
            }
                  }
                   else if($type == '9')
                  {
                      
                       $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
              
              $lang=$query_lang->language;
              
               function RemoveBS($Str) {
            $StrArr = str_split($Str);
            $NewStr = '';
            foreach ($StrArr as $Char) {
                $CharNo = ord($Char);
                if ($CharNo == 163) {
                    $NewStr .= $Char;
                    continue;
                } // keep £
                if ($CharNo > 31 && $CharNo < 127) {
                    $NewStr .= $Char;
                }
            }
            return $NewStr;
        }
                         // Articles
            $query = $this->db->query("SELECT article.id, `cat_id`, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date`, article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id  where  (article_title like '%$keyword%' or article_description like '%$keyword%') and article_category.type='home_remedies'  limit 20");

            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $article_id = $row['id'];
                    $cat_id = $row['cat_id'];
                  //  $article_title = $row['article_title'];
                //    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                    
                      if($lang == 1)
                {
                    $article_title = $this->decrypt(($row['article_title']));
                    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
        
                }
                else
                {
                    $article_title = RemoveBS($row['article_title']);
                //    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                   $article_description = $row['article_description'];
                }
                    
                    $article_image = $row['image'];
                    $article_date = $row['posted'];
                    $author = 'Medicalwale.com';
                    $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;

                    $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                    $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                    $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                    $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();

                    $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();
                    $mom_list[] = array(
                        'article_id' => $article_id,
                        'category_id' => $cat_id,
                        'article_title' => $article_title,
                        'article_description' => $article_description,
                        'short_desc' =>"",
                        'article_image' => $article_image,
                        'article_date' => $article_date,
                        'share_url' => "",
                        'author' => $author,
                        'like_count' => $like_count,
                        'like_yes_no' => $like_yes_no,
                        'total_views' => $views_count,
                        'is_bookmark' => $is_bookmark,
                        'total_bookmark' => $total_bookmark,
                        'is_follow' => "No",
                        'total_follow' =>0
                    );
                }
                $mom_array[] = array(
                    'title' => 'mom Remedies',
                    'listing_type' => 32,
                    'array' => $mom_list
                );
                
                return $mom_array;
            } else {
               return $mom_array = array();
            }
                  }
                  else if($type == '10')
                  {
                        // Survivor's Stories
            $survivor_query = $this->db->query("SELECT id,title,description,tag,author,image,date FROM `survival_stories` where is_active='1' and (title like '%$keyword%'  or description like '%$keyword%') order by id desc limit 50");
            $survivor_count = $survivor_query->num_rows();
            if ($survivor_count > 0) {
                foreach ($survivor_query->result_array() as $row) {
                    $id = $row['id'];
                    $title = $row['title'];
                    $description = $row['description'];
                    $tag = $row['tag'];
                    $author = $row['author'];
                    $image = $row['image'];
                    $date = $row['date'];
                    $image = str_replace(" ", "", $image);
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $image;

                    $bookmark_yes_no = $this->db->select('id')->from('survival_stories_bookmark')->where('user_id', $user_id)->where('survival_stories_id', $id)->get()->num_rows();

                    $share = 'https://d2c8oti4is0ms3.cloudfront.net/images/';

                    $survivor_list[] = array(
                        "article_id" => $id,
                        "article_title" => $title,
                        'article_description' => $description,
                        'short_desc' => "",
                        'tag' => $tag,
                        'author' => $author,
                        'article_image' => $image,
                        'article_date' => $date,
                        'share_url' => $share,
                        'like_count' => 0,
                        'like_yes_no' =>0,
                        'total_views' => 0,
                        'is_bookmark' => 0,
                        'total_bookmark' => 0,
                        'is_follow' => "No",
                        'total_follow' => 0
                    );
                }
                $survivor_array[] = array(
                    'title' => "Survivor's Stories",
                    'listing_type' => 33,
                    'array' => $survivor_list
                );
                
                return $survivor_array;
            } else {
               return $survivor_array = array();
            }
 
                  }
                  else if($type == '11') 
                  {
                         // Pharmacy
            $field1 = '';
            $field2 = '';
            $field3 = '';
            $index_id="medical";
            $pharmacy=$this->elasticsearchsize3($index_id,$keyword);
            $pharmacy_count=count($pharmacy);
            if ($pharmacy_count > 0) {
               $pharmacy_array[] = array(
                    'title' => 'Pharmacy',
                    'listing_type' => 13,
                    'array' => $pharmacy
                );
                
                return $pharmacy_array;
            } else {
                return  $pharmacy_array = array();
            }

                  }
                  else if($type=='12'){
                       // Labs
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
             
                $index_id="lab";
                   $labs=$this->elasticsearchsize3($index_id,$keyword);
            if (!empty($labs)) {
            
                $labs_array[] = array(
                    'title' => 'Labs',
                    'listing_type' => 10,
                    'array' => $labs
                );
            } else {
                $labs_array = array();
            }
            
             // Labs test
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
             
                $index_id="lab_test";
                   $labs_test=$this->elasticsearchsize3($index_id,$keyword);
            if (!empty($labs_test)) {
            
                $labs_test_array[] = array(
                    'title' => 'Labs test',
                    'listing_type' => 10,
                    'array' => $labs_test
                );
            } else {
                $labs_test_array = array();
            }
            
             // Labs package
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
             
                $index_id="lab_package";
                   $labs_package=$this->elasticsearchsize3($index_id,$keyword);
            if (!empty($labs_package)) {
            
                $labs_package_array[] = array(
                    'title' => 'Labs Package',
                    'listing_type' => 10,
                    'array' => $labs_package
                );
            } else {
                $labs_package_array = array();
            }
            
             $resultpost = array_merge($labs_array,$labs_test_array,$labs_package_array);
          
            return $resultpost;
                      
                  }
                  else if($type == '23')
                    {
                           // Labs
                $field1 = '';
                $field2 = '';
                $field3 = '';
                $index_id="lab";
                $labs=$this->elasticsearchsize3($index_id,$keyword);
                $labs_count=count($labs);
                if ($labs_count > 0) {
                    $labs_array[] = array(
                        'title' => 'Labs',
                        'listing_type' => 10,
                        'array' => $labs
                    );
                    return $labs_array;
                } else {
                   return $labs_array = array();
                }
                          
                      }
                else if($type == '13'){
                $index_id="lab_test";
                   $labs_test=$this->elasticsearchsize3($index_id,$keyword);
            if (!empty($labs_test)) {
            
                $labs_test_array[] = array(
                    'title' => 'Labs test',
                    'listing_type' => 10,
                    'array' => $labs_test
                );
                return $labs_test_array;
            } else {
               return $labs_test_array = array();
            }
            
                 }else if($type == '14'){
                $index_id="lab_package";
                $labs_package=$this->elasticsearchsize3($index_id,$keyword);
                    if (!empty($labs_package)) {
                    
                        $labs_package_array[] = array(
                            'title' => 'Labs Package',
                            'listing_type' => 10,
                            'array' => $labs_package
                        );
                        return $labs_package_array;
                    } else {
                     return   $labs_package_array = array();
                    }
                      
                }
                else if($type=="15"){
                      // Spa Center
                      
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
                
                    $index_id="spa_listing";
                        
                    $spa_center=$this->elasticsearchspa($index_id,$keyword);
              
                    if (!empty($spa_center)) {
                        foreach($spa_center as $sp_c){
                       $spa_array[]=array('listing_id'=>$sp_c['user_id'],
                        'branch_id'=>$sp_c['id'],
                        'name'=>$sp_c['branch_name'],
                        'image'=>$sp_c['branch_image'],
                        'address'=>$sp_c['branch_address'], 
                        'field2'=>"",
                         'listing_type' => 36,
                        'field3'=>"");
                        }
                     
                        $spa_center_array[] = array(
                            'title' => 'Spa Center',
                            'listing_type' => 36,
                            'array' => $spa_array
                        );
                    } else {
                        $spa_center_array = array();
                    }
                    
              // Spa Package
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
                
                  $index_id="spa_package";
                        
                     $spa_package=$this->elasticsearchspa_package($index_id,$keyword);
                   /*  print_r($spa_center);
                     die();*/
                    if (!empty($spa_package)) {
                        foreach($spa_package as $sp_c){
                       $spa_parray[]=array('listing_id'=>$sp_c['package_id'],
                        'name'=>$sp_c['package_name'],
                        'image'=>$sp_c['package_image'],
                        'price'=>$sp_c['package_price'], 
                        'centername'=>$sp_c['branch_name'],
                         'listing_type' => 36,
                        'listing_id'=>$sp_c['user_id'],
                        'package_id'=>$sp_c['package_id']);
                        }
                        
                        $spa_p_array[] = array(
                            'title' => 'Spa Package',
                            'listing_type' => 36,
                            'array' => $spa_parray
                        );
                    } else {
                        $spa_p_array = array();
                    }        
            $resultpost = array_merge($spa_center_array,$spa_p_array);
             return $resultpost;

                }
                else if($type == '25'){
                       // Spa Center
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
                
                    $index_id="spa_listing";
                        
                    $spa_center=$this->elasticsearchspa($index_id,$keyword);
              
                    if (!empty($spa_center)) {
                        foreach($spa_center as $sp_c){
                       $spa_array[]=array('listing_id'=>$sp_c['user_id'],
                        'branch_id'=>$sp_c['id'],
                        'name'=>$sp_c['branch_name'],
                        'image'=>$sp_c['branch_image'],
                        'address'=>$sp_c['branch_address'], 
                        'field2'=>"",
                         'listing_type' => 36,
                        'field3'=>"");
                        }
                        
                        $spa_center_array[] = array(
                            'title' => 'Spa Center',
                            'listing_type' => 36,
                            'array' => $spa_array
                        );
                        return $spa_center_array;
                    } else {
                       return $spa_center_array = array();
                    }
                    }else if($type == '16'){ 
              // Spa Package
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
                
                  $index_id="spa_package";
                        
                     $spa_package=$this->elasticsearchspa_package($index_id,$keyword);
                   /*  print_r($spa_center);
                     die();*/
                    if (!empty($spa_package)) {
                        foreach($spa_package as $sp_c){
                       $spa_parray[]=array('listing_id'=>$sp_c['package_id'],
                        'name'=>$sp_c['package_name'],
                        'image'=>$sp_c['package_image'],
                        'price'=>$sp_c['package_price'], 
                        'centername'=>$sp_c['branch_name'],
                        'listing_type' => 36,
                        'listing_id'=>$sp_c['user_id'],
                        'package_id'=>$sp_c['package_id']);
                        }
                        
                        $spa_center_array[] = array(
                            'title' => 'Spa Package',
                             'listing_type' => 36,
                            'array' => $spa_parray
                        );
                         return $spa_center_array;
                    } else {
                        return $spa_center_array = array();
                    }        
            

                      
                      
                      
                  }
                 
                  else if($type == '19'){
                        // Hospital
                $field1 = '';
                $field2 = '';
                $field3 = '';
               $index_id="hospital";
                      $hospital=$this->elasticsearchsize3($index_id,$keyword);  
                if (!empty($hospital)) {
                     
                    $hospital_array[] = array(
                        'title' => 'Hospital',
                        'listing_type' => 8,
                        'array' => $hospital
                    );
                } else {
                    $hospital_array = array();
                }
           
           
                  // hospital_package
                $field1 = '';
                $field2 = '';
                $field3 = '';
           
                
                  $index_id="hospital_package";
                        
                     $hospital_package=$this->elasticsearchhospital_package($index_id,$keyword);
                 
                    if (!empty($hospital_package)) {
                        foreach($hospital_package as $sp_c){
                       $hospital_parray[]=array('id'=>$sp_c['id'],
                        'name'=>$sp_c['package_name'],
                        'package_desc'=>$sp_c['package_desc'],
                        'package_amount'=>$sp_c['booking_amount'], 
                        'hospital_id'=>$sp_c['hospital_id'],
                        'type'=>$sp_c['treatment_type'],
                        'cat_id'=>$sp_c['package_type']);
                        }
                        
                        $hospital_package_array[] = array(
                            'title' => 'hospital Package',
                            'listing_type' => 8,
                            'array' => $hospital_parray
                        );
                    } else {
                        $hospital_package_array = array();
                    }  
                    
                      $resultpost = array_merge($hospital_array,$hospital_package_array);
          
                    return $resultpost;

                  }
                   else if($type == '17'){
                    // hospital_package
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
                
                  $index_id="hospital_package";
                        
                     $hospital_package=$this->elasticsearchhospital_package($index_id,$keyword);
                 
                    if (!empty($hospital_package)) {
                        foreach($hospital_package as $sp_c){
                       $hospital_parray[]=array('id'=>$sp_c['id'],
                        'name'=>$sp_c['package_name'],
                        'package_desc'=>$sp_c['package_desc'],
                        'package_amount'=>$sp_c['booking_amount'], 
                        'hospital_id'=>$sp_c['hospital_id'],
                        'type'=>$sp_c['treatment_type'],
                        'cat_id'=>$sp_c['package_type']);
                        
                        }
                        
                        $hospital_package_array[] = array(
                            'title' => 'hospital Package',
                            'listing_type' => 8,
                            'array' => $hospital_parray
                        );
                         return $hospital_package_array;
                    } else {
                        return $hospital_package_array = array();
                    }  
                  
                  
                 } 
                 else if($type == '18')
                  {
                     // Fitness Center
            $field1 = '';
            $field2 = '';
            $field3 = '';
            $index_id="fitness";
            $fitness=$this->elasticsearchsize3($index_id,$keyword);
            $fitness_count=count($fitness);
            if ($fitness_count > 0) {
                $fitness_array[] = array(
                    'title' => 'Fitness',
                    'listing_type' => 6,
                    'array' => $fitness
                );
                return $fitness_array;
            } else {
              return $fitness_array = array();
            }  
                  }
                  else if($type == '24')
                  {
                        // Hospital
            $field1 = '';
            $field2 = '';
            $field3 = '';
            
            $index_id="hospital";
            $hospital=$this->elasticsearchsize3($index_id,$keyword);
            $hospital_count=count($hospital);
            if ($hospital_count > 0) {
                $hospital_array[] = array(
                    'title' => 'Hospital',
                    'listing_type' => 8,
                    'array' => $hospital
                );
                return $hospital_array;
            } else {
                return $hospital_array = array();
            }
                  }
                  else if($type == '20')
                  {
                       // Nursing Attendant
            $field1 = '';
            $field2 = '';
            $field3 = '';
            $index_id="nursing_attendant";
            $nursing=$this->elasticsearchsize3($index_id,$keyword);
            $nursing_count=count($nursing);
            if ($nursing_count > 0) {
                
                $nursing_array[] = array(
                    'title' => 'Nursing',
                    'listing_type' => 12,
                    'array' => $nursing
                );
                return $nursing_array;
            } else {
               return $nursing_array = array();
            }
                  }
                  else if($type == '21')
                  {
                     
                       // Post
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
            $media_array = array();
            $query = $this->db->query("select posts.id as post_id,posts.type as post_type,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category,posts.user_id as post_user_id,IFNULL(posts.post_location,'') AS post_location,posts.repost_location,posts.is_repost,posts.repost_time, IFNULL(posts.repost_user_id,'') AS repost_user_id,users.name,users.vendor_id,healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' and users.name like '%$keyword%' order by posts.id desc limit 50");
            $healthwall_count = $query->num_rows();
            if ($healthwall_count > 0) {
                foreach ($query->result_array() as $row) {
                    $post_id = $row['post_id'];
                    $listing_type = $row['vendor_id'];
                    $post_location = $row['post_location'];
                    $post_id = $row['post_id'];
                    $post = $row['post'];
                    if ($post != '') {
                    $post = preg_replace('~[\r\n]+~', '', $post);
                    if ($post_id > '2938') {
                        $decrypt = $this->decrypt($post);
                        $encrypt = $this->encrypt($decrypt);
                        if ($encrypt == $post) {
                            $post = $decrypt;
                        }
                    } else {
                        if (base64_encode(base64_decode($post)) === $post) {
                            $post = base64_decode($post);
                        }
                    }
                }
                    $article_title = $row['article_title'];
                    $article_image = $row['article_image'];
                    $article_domain_name = $row['article_domain_name'];
                    $article_url = $row['article_url'];
                    $category = '';
                    $is_anonymous = $row['is_anonymous'];
                    $tag = $row['tag'];
                    $post_type = $row['post_type'];
                    $date = $row['created_at'];
                    $caption = $row['caption'];
                    $username = $row['name'];
                    $post_user_id = $row['post_user_id'];
                    $repost_user_id = $row['repost_user_id'];
                    $repost_location = $row['repost_location'];
                    $is_repost = $row['is_repost'];
                    $repost_time = $row['repost_time'];
                    $healthwall_category_name = $row['healthwall_category'];
                    $healthwall_category_id = $row['healthwall_category_id'];
                    
                    $share_url = "https://medicalwale.com/share/healthwall/" . $post_id; 
                    $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                    if ($img_count > 0) {
                        $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                        $img_file = $profile_query->source;
                        $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                    } else {
                        $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }

                   $media_query = $this->db->query("SELECT media.id AS media_id,media.type AS media_type,IFNULL(media.caption,'') AS caption,media.source,post_media.img_width,post_media.img_height,post_media.video_width,post_media.video_height FROM media INNER JOIN post_media on media.id=post_media.media_id where post_media.post_id='$post_id'");
                $img_val = '';
                $images = '';
                $img_comma = '';
                $img_width = '';
                $img_height = '';
                $video_width = '';
                $video_height = '';

                $media_array = array();
                foreach ($media_query->result_array() as $media_row) {
                    $media_id = $media_row['media_id'];
                    $media_type = $media_row['media_type'];
                    $source = $media_row['source'];
                    if ($media_type == 'video') {
                        $thumb = 'https://d1g6k76aztls2p.cloudfront.net/healthwall_media/video/' . substr_replace($source, '_thumbnail.jpeg', strrpos($source, '.') + 0);
                        $images = 'https://d2ua8z9537m644.cloudfront.net/healthwall_media/' . $media_type . '/' . $source;
                    } else {
                        $thumb = '';
                        $images = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/' . $media_type . '/' . $source;
                    }
                    $caption = $media_row['caption'];
                    $img_width = $media_row['img_width'];
                    $img_height = $media_row['img_height'];
                    $video_width = $media_row['video_width'];
                    $video_height = $media_row['video_height'];
                    $view_media_count = $this->db->select('id')->from('post_video_views')->where('media_id', $media_id)->get()->num_rows();

                    $view_media_yes_no_query = $this->db->query("SELECT id from post_video_views WHERE media_id='$media_id' and user_id='$user_id'");
                    $view_media_yes_no = $view_media_yes_no_query->num_rows();

                    $media_array[] = array(
                        'media_id' => $media_id,
                        'type' => $media_type,
                        'images' => str_replace(' ', '%20', $images),
                        'thumb' => $thumb,
                        'caption' => $caption,
                        'img_height' => $img_height,
                        'img_width' => $img_width,
                        'video_height' => $video_height,
                        'video_width' => $video_width,
                        'video_views' => $view_media_count,
                        'video_view_yes_no' => $view_media_yes_no
                    );
                }

                    $like_count = $this->db->select('id')->from('post_likes')->where('post_id', $post_id)->get()->num_rows();
                    $comment_count = $this->db->select('id')->from('comments')->where('post_id', $post_id)->get()->num_rows();
                    $follow_count = $this->db->select('id')->from('post_follows')->where('post_id', $post_id)->get()->num_rows();
                    $view_count = $this->db->select('id')->from('post_views')->where('post_id', $post_id)->get()->num_rows();

                    $like_yes_no_query = $this->db->query("SELECT id from post_likes where post_id='$post_id' and user_id='$user_id'");
                    $like_yes_no = $like_yes_no_query->num_rows();

                    $follow_yes_no_query = $this->db->query("SELECT id from post_follows where post_id='$post_id' and user_id='$user_id'");
                    $follow_post_yes_no = $follow_yes_no_query->num_rows();

                    $view_yes_no_query = $this->db->query("SELECT id from post_views where post_id='$post_id' and user_id='$user_id'");
                    $view_post_yes_no = $view_yes_no_query->num_rows();
                    
                    $is_reported_query = $this->db->query("SELECT id from post_report WHERE post_id='$post_id' AND reporter_id='$user_id' AND post_type='healthwall'");
                    $is_reported = $is_reported_query->num_rows();
    
                    $is_post_save_query = $this->db->query("SELECT id from post_save WHERE post_id='$post_id' AND user_id='$user_id' AND post_type='healthwall'");
                    $is_post_save = $is_post_save_query->num_rows();
                
                    $date = get_time_difference_php($date);
                    
                     //comments
                    $query_comment = $this->db->query("SELECT comments.id,comments.post_id,comments.description as comment,comments.updated_at as date,users.name,comments.user_id as post_user_id FROM comments INNER JOIN users on users.id=comments.user_id WHERE comments.post_id='$post_id' order by comments.id desc LIMIT 0,3");
                    $comments = array();
                    $comment_counts = $query_comment->num_rows();
                    if ($comment_counts > 0) {
                        foreach ($query_comment->result_array() as $rows) {
                            $comment_id = $rows['id'];
                            $comment_post_id = $rows['post_id'];
                            $comment = $rows['comment'];
                            $comment = preg_replace('~[\r\n]+~', '', $comment);
                            if ($comment_id > '9547') {
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
                            $comment_username = $rows['name'];
                            $comment_date = $rows['date'];
                            $comment_post_user_id = $rows['post_user_id'];
                            $comment_like_count = $this->db->select('id')->from('comment_likes')->where('comment_id', $comment_id)->get()->num_rows();
                            $comment_like_yes_no = $this->db->select('id')->from('comment_likes')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
                            $comment_date = $comment_date;
                            $comment_img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->num_rows();
                            if ($comment_img_count > 0) {
                                $comment_profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->row();
                                $comment_img_file = $comment_profile_query->source;
                                $comment_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $comment_img_file;
                            } else {
                                $comment_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                            }
                            $query_listing_type = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_post_user_id'");
                            $get_type = $query_listing_type->row_array();
                            $com_listing_type = $get_type['vendor_id'];
                            $comment_reply_array = '';
                            $query_comment_reply = $this->db->query("SELECT comments_reply.id,comments_reply.comment_id,comments_reply.post_id,comments_reply.comment,comments_reply.updated_at as date,users.name,comments_reply.user_id FROM comments_reply INNER JOIN users on users.id=comments_reply.user_id WHERE comments_reply.post_id='$post_id' and comments_reply.comment_id='$comment_id' order by comments_reply.id desc LIMIT 0,3");
                            $comment_reply = array();
                            $comment_count_reply = $query_comment_reply->num_rows();
                            if ($comment_count_reply > 0) {
                                foreach ($query_comment_reply->result_array() as $rows_reply) {
                                    $comment_id = $rows_reply['comment_id'];
                                    $comment_reply_id = $rows_reply['id'];
                                    $comment_reply_post_id = $rows_reply['post_id'];
                                    $comment_reply_username = $rows_reply['name'];
                                    $comment_reply_date = $rows_reply['date'];
                                    $comment_reply_user_id = $rows_reply['user_id'];
                                    $comment_reply = $rows_reply['comment'];
                                    if ($comment_reply != '' && is_numeric($comment_reply)) {
                                    $comment_reply = preg_replace('~[\r\n]+~', '', $comment_reply);
                                    $comment_reply_decrypt = $this->decrypt($comment_reply);
                                    $comment_reply_encrypt = $this->encrypt($comment_reply_decrypt);
                                        if ($comment_reply_encrypt == $comment_reply) {
                                          $comment_reply = $comment_reply_decrypt;
                                        }
                                    }
                                    
                                    $comment_reply_like_count = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->get()->num_rows();
                                    $comment_reply_like_yes_no = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->where('user_id', $user_id)->get()->num_rows();
                                    $comment_reply_date = $comment_reply_date;
                                    $comment_reply_img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->num_rows();
                                    if ($comment_reply_img_count > 0) {
                                        $comment_reply_profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->row();
                                        $comment_reply_img_file = $comment_profile_query->source;
                                        $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $comment_reply_img_file;
                                    } else {
                                        $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                                    }
                                    $query_reply_listing_type = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_reply_user_id'");
                                    $get_reply_type = $query_reply_listing_type->row_array();
                                    $com_reply_listing_type = $get_reply_type['vendor_id'];
                                    $comment_reply_array[] = array(
                                        'id' => $comment_reply_id,
                                        'listing_type' => $com_reply_listing_type,
                                        'comment_user_id' => $comment_reply_user_id,
                                        'username' => $comment_reply_username,
                                        'userimage' => $comment_reply_userimage,
                                        'like_count' => $comment_reply_like_count,
                                        'like_yes_no' => $comment_reply_like_yes_no,
                                        'post_id' => $comment_reply_post_id,
                                        'comment' => $comment_reply,
                                        'comment_date' => $comment_reply_date
                                    );
                                }
                            } else {
                                $comment_reply_array = array();
                            }
                            $comments[] = array(
                                'id' => $comment_id,
                                'listing_type' => $com_listing_type,
                                'comment_user_id' => $comment_post_user_id,
                                'username' => $comment_username,
                                'userimage' => $comment_userimage,
                                'like_count' => $comment_like_count,
                                'like_yes_no' => $comment_like_yes_no,
                                'post_id' => $comment_post_id,
                                'comment' => $comment,
                                'comment_date' => $comment_date,
                                'comment_reply' => $comment_reply_array
                            );
                        }
                    } else {
                        $comments = array();
                    }
                    //comments
                    
                    $repost_user_name = "";
                    $repost = array();
                    if ($is_repost) {
                        if (!empty($repost_user_id) && $repost_user_id != "") {
                            $listing_type_1 = $this->db->query("select vendor_id from users where id = '$repost_user_id' ")->row();
                            if(!empty($listing_type_1))
                            {
                                $listing_type = $listing_type_1->vendor_id;
                            }
                            else
                            {
                                $listing_type = "";
                            }
                            $result = $this->db->query("select users.vendor_id as listing_type, media.title, media.source as source, users.avatar_id from users join media on(users.avatar_id = media.id) where users.id = '$repost_user_id' ");
                            $this->db->select("users.vendor_id as listing_type, media.title, media.source as source, users.avatar_id");
                            $this->db->from('users');
                            $this->db->where("users.id ", $repost_user_id);
                            $this->db->join('media', 'users.avatar_id = media.id');
                            $respost_sorce = $this->db->get()->row();
                            if (!empty($respost_sorce)) {
                                $respost_sorce = $respost_sorce->source;
                            } else {
                                $respost_sorce = "user_avatar.jpg";
                            }
                            //$post_user_id = $row['repost_user_id'];
                           // $repost_user_name = $this->db->select('*')->where('id', $repost_user_id)->get('users')->row()->name;
                            $repost_user_name_1 = $this->db->select('*')->where('id', $repost_user_id)->get('users')->row();
                            if(!empty($repost_user_name_1))
                            {
                                $repost_user_name = $repost_user_name_1->name;
                            }
                            else
                            {
                                $repost_user_name ="";
                            }
                            $repost[] = array(
                                'repost_user_id' => $repost_user_id,
                                'repost_user_name' => $repost_user_name,
                                'repost_location' => $repost_location,
                                'repost_time' => $repost_time,
                                'listing_type' => $listing_type,
                                'repost_user_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/" . $respost_sorce
                            );
                        }
                        if (is_null($repost_user_name) || $repost_user_name == "") {
                            $repost_user_name = '';
                        }
                    } else {
                        $repost = [];
                    }
                    $query_is_repost = $this->db->query("SELECT * FROM `posts` WHERE `user_id`='$post_user_id' AND `repost_user_id`='$user_id' AND `id`='$post_id'");
                    $repost_counts = $query_is_repost->num_rows();
                    if ($repost_counts > 0) {
                        $flag = '1';
                    } else {
                        $flag = '0';
                    }
                
                
                    $healthwall_list[] = array(
                        'id' => $post_id,
                        'post_user_id' => $post_user_id,
                        'listing_type' => $listing_type,
                        'post_location' => $post_location,
                        'healthwall_category' => $healthwall_category_name,
                        'healthwall_category_id' => $healthwall_category_id,
                        'username' => $username,
                        'userimage' => $userimage,
                        'post_type' => $post_type,
                        'post' => str_replace('\n', '', $post),
                        'article_title' => str_replace('null', '', $article_title),
                        'article_image' => str_replace('null', '', $article_image),
                        'article_domain_name' => str_replace('null', '', $article_domain_name),
                        'article_url' => str_replace('null', '', $article_url),
                        'is_anonymous' => $is_anonymous,
                        'tag' => $tag,
                        'category' => $category,
                        'like_count' => $like_count,
                        'follow_count' => $follow_count,
                        'like_yes_no' => $like_yes_no,
                        'follow_post_yes_no' => $follow_post_yes_no,
                        'comment_count' => $comment_count,
                        'views' => $view_count,
                        'view_yes_no' => $view_post_yes_no,
                        'media' => $media_array,
                        'share_url' => $share_url,
                        'date' => $date,
                        'is_reported' => $is_reported,
                        'is_post_save' => $is_post_save,
                        'comments' => $comments,
                        'is_repost' => $is_repost,
                        'repost' => $repost,
                        'repost_flag' => $flag
                    );
                }
               return $healthwall_array[] = array(
                    'title' => 'Post',
                    'listing_type' => 31,
                    'array' => $healthwall_list
                );
            } else {
               return $healthwall_array = array();
            }
                  }
                   else if($type == '22')
                  {
                    
                  $field1 = '';
            $field2 = '';
            $field3 = '';
            $index_id="hm_product";
            $hm_product=$this->elasticsearchhm_product($index_id,$keyword); 
            $hm_product_count=count($hm_product);
            if ($hm_product_count > 0) {
                
                
                 foreach($hm_product as $a){
                    $string = strip_tags($a['pd_short_desc']);
                         if (strlen($string) > 80) {
                    $stringCut = substr($string, 0, 80);
                    $endPoint = strrpos($stringCut, ' ');
                    $string = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                    $string .= '...';
                         }
                  $hm_product1[] = array(
                        'listing_id' => $a['pd_id'],
                        'name' => $a['pd_name'],
                        'image' => $a['pd_photo_1'],
                        'field1' => $a['pd_mrp_price'],
                        'field2' =>$a['pd_vendor_price'],
                        'field3' => $string
                    );
                 }
                 
                $hm_product_array[] = array(
                    'title' => 'Health Mall',
                    'listing_type' => 6,
                    'array' => $hm_product1
                );
                return $hm_product_array;
            } else {
              return $hm_product_array = array();
            }  
                  }
               // return $resultpost = array_merge($people_array,$doctor_array);
              }
         else
         {
            return $resultpost = array();   
         }
         //return $resulpost;
    }
    
     public function search_select_count($user_id, $keyword ,$type)
    {
         if ($user_id > 0) {
                  if($type == '1')
                  {
                       // People
            $field1 = '';
            $field2 = '';
            $field3 = '';
                    $index_id="people";
                    $people=$this->elasticsearchsize3($index_id,$keyword);
                  $people_count=count($people);
                
                    if ($people_count > 0) {
                        $people_array[] = array(
                            'title' => 'People',
                            'listing_type' => 0,
                            'array' => $people
                        );
                        return $people_array;
                    } else {
                       return $people_array = array();
                    } 
                  }
                  else if($type == '2')
                  {
                        // Doctor
            $field1 = '';
            $field2 = '';
            $field3 = '';
             $index_id="doctor";
            $doctor=$this->elasticsearchsize3($index_id,$keyword);
           $doctor_count=count($doctor);
            if ($doctor_count > 0) {
               
                $doctor_array[] = array(
                    'title' => 'Doctor',
                    'listing_type' => 5,
                    'array' => $doctor
                );
                
                return $doctor_array;
            } else {
              return  $doctor_array = array();
            }
                  }
                  else if($type == '3')
                  {
                        // Doctor
            $field1 = '';
            $field2 = '';
            $field3 = '';
             $index_id="dentist_clinic_list";
            $dental_clinic=$this->elasticsearchsize3($index_id,$keyword);
           $doctor_count=count($dental_clinic);
            if ($doctor_count > 0) {
               
                $dental_clinic_array[] = array(
                    'title' => 'Dental Clinic',
                    'listing_type' => 39,
                    'array' => $dental_clinic
                );
                
                return $dental_clinic_array;
            } else {
              return  $dental_clinic_array = array();
            }
                  }
                  else if($type == '4')
                  {
                       // Homeopathic
            $field1 = '';
            $field2 = '';
            $field3 = '';
            $homeopathic_query = $this->db->query("SELECT user_id,image,doctor_name,speciality,qualification,address FROM doctor_list WHERE doctor_name like '%$keyword%'");
            $homeopathic_count = $homeopathic_query->num_rows();
            if ($homeopathic_count > 0) {
                foreach ($homeopathic_query->result_array() as $homeopathic_row) {
                    $user_id = $homeopathic_row['user_id'];
                    $name = str_replace("null", "", $homeopathic_row['doctor_name']);
                    $field1 = str_replace("null", "", $homeopathic_row['speciality']);
                    $field2 = str_replace("null", "", $homeopathic_row['qualification']);
                    $field3 = str_replace("null", "", $homeopathic_row['address']);
                    $homeopathic_image = str_replace("null", "", $homeopathic_row['image']);
                    if ($homeopathic_image != '') {
                     //   $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $homeopathic_image;
                      $image =  'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'. $homeopathic_image;
                    } else {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                    $homeopathic[] = array(
                        'listing_id' => $user_id,
                        'name' => $name,
                        'image' => $image,
                        'field1' => $field1,
                        'field2' => $field2,
                        'field3' => $field3
                    );
                }
                
                $homeopathic_array[] = array(
                    'title' => 'Homeopathic',
                    'listing_type' => 9,
                    'array' => $homeopathic
                );
                return $homeopathic_array;
            } else {
              return $homeopathic_array = array();
            }
                  }
                  else if($type == '5')
                  {
                      $field1 = '';
            $field2 = '';
            $field3 = '';
            $physiotherapist_query = $this->db->query("SELECT user_id,image,doctor_name,speciality,qualification,address FROM doctor_list WHERE doctor_name like '%$keyword%'");
            $physiotherapist_count = $physiotherapist_query->num_rows();
            if ($physiotherapist_count > 0) {
                foreach ($physiotherapist_query->result_array() as $physiotherapist_row) {
                    $user_id = $physiotherapist_row['user_id'];
                    $name = str_replace("null", "", $physiotherapist_row['doctor_name']);
                    $field1 = str_replace("null", "", $physiotherapist_row['speciality']);
                    $field2 = str_replace("null", "", $physiotherapist_row['qualification']);
                    $field3 = str_replace("null", "", $physiotherapist_row['address']);
                    $physiotherapist_image = str_replace("null", "", $physiotherapist_row['image']);
                    if ($physiotherapist_image != '') {
                      //  $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $physiotherapist_image;
                      
                       $image =  'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'. $physiotherapist_image;
                    } else {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                    $physiotherapist[] = array(
                        'listing_id' => $user_id,
                        'name' => $name,
                        'image' => $image,
                        'field1' => $field1,
                        'field2' => $field2,
                        'field3' => $field3
                    );
                }
                $physiotherapist_array[] = array(
                    'title' => 'Physiotherapist',
                    'listing_type' => 20,
                    'array' => $physiotherapist
                );
                
                return $physiotherapist_array;
            } else {
                return $physiotherapist_array = array();
            }
                  }
              
                  else if($type == '6')
                  {
                      
                       $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
              
              $lang=$query_lang->language;
              
               function RemoveBS($Str) {
            $StrArr = str_split($Str);
            $NewStr = '';
            foreach ($StrArr as $Char) {
                $CharNo = ord($Char);
                if ($CharNo == 163) {
                    $NewStr .= $Char;
                    continue;
                } // keep £
                if ($CharNo > 31 && $CharNo < 127) {
                    $NewStr .= $Char;
                }
            }
            return $NewStr;
        }
                         // Articles
            $query = $this->db->query("SELECT `id`, `cat_id`, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date` FROM article where  (article_title like '%$keyword%' or article_description like '%$keyword%') limit 20");

            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $article_id = $row['id'];
                    $cat_id = $row['cat_id'];
                  //  $article_title = $row['article_title'];
                //    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                    
                      if($lang == 1)
                {
                    $article_title = $this->decrypt(($row['article_title']));
                    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
        
                }
                else
                {
                    $article_title = RemoveBS($row['article_title']);
                //    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                   $article_description = $row['article_description'];
                }
                    
                    $article_image = $row['image'];
                    $article_date = $row['posted'];
                    $author = 'Medicalwale.com';
                    $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;

                    $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                    $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                    $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                    $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();

                    $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();
                    $article_list[] = array(
                        'article_id' => $article_id,
                        'category_id' => $cat_id,
                        'article_title' => $article_title,
                        'article_description' => $article_description,
                        'short_desc' =>"",
                        'article_image' => $article_image,
                        'article_date' => $article_date,
                        'share_url' => "",
                        'author' => $author,
                        'like_count' => $like_count,
                        'like_yes_no' => $like_yes_no,
                        'total_views' => $views_count,
                        'is_bookmark' => $is_bookmark,
                        'total_bookmark' => $total_bookmark,
                        'is_follow' => "No",
                        'total_follow' =>0
                    );
                }
                $article_array[] = array(
                    'title' => 'Articles',
                    'listing_type' => 32,
                    'array' => $article_list
                );
                
                return $article_array;
            } else {
               return $article_array = array();
            }
                  }
                    else if($type == '7')
                  {
                      
                       $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
              
              $lang=$query_lang->language;
              
              
                         // Articles
            $query = $this->db->query("SELECT article.id, `cat_id`, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date`, article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id  where  (article_title like '%$keyword%' or article_description like '%$keyword%') and article_category.type='tips'  limit 20");

            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $article_id = $row['id'];
                    $cat_id = $row['cat_id'];
                  //  $article_title = $row['article_title'];
                //    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                    
                      if($lang == 1)
                {
                    $article_title = $this->decrypt(($row['article_title']));
                    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
        
                }
                else
                {
                    $article_title = $row['article_title'];
                //    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                   $article_description = $row['article_description'];
                }
                    
                    $article_image = $row['image'];
                    $article_date = $row['posted'];
                    $author = 'Medicalwale.com';
                    $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;

                    $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                    $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                    $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                    $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();

                    $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();
                    $tips_list[] = array(
                        'article_id' => $article_id,
                        'category_id' => $cat_id,
                        'article_title' => $article_title,
                        'article_description' => $article_description,
                        'short_desc' =>"",
                        'article_image' => $article_image,
                        'article_date' => $article_date,
                        'share_url' => "",
                        'author' => $author,
                        'like_count' => $like_count,
                        'like_yes_no' => $like_yes_no,
                        'total_views' => $views_count,
                        'is_bookmark' => $is_bookmark,
                        'total_bookmark' => $total_bookmark,
                        'is_follow' => "No",
                        'total_follow' =>0
                    );
                }
                $tips_array[] = array(
                    'title' => 'Tips',
                    'listing_type' => 32,
                    'array' => $tips_list
                );
                
                return $tips_array;
            } else {
               return $tips_array = array();
            }
                  }
                   else if($type == '8')
                  {
                      
                       $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
              
              $lang=$query_lang->language;
              
              
                         // Articles
            $query = $this->db->query("SELECT article.id, `cat_id`, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date`, article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id  where  (article_title like '%$keyword%' or article_description like '%$keyword%') and article_category.type='skin_hair'  limit 20");

            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $article_id = $row['id'];
                    $cat_id = $row['cat_id'];
                  //  $article_title = $row['article_title'];
                //    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                    
                      if($lang == 1)
                {
                    $article_title = $this->decrypt(($row['article_title']));
                    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
        
                }
                else
                {
                    $article_title = $row['article_title'];
                //    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                   $article_description = $row['article_description'];
                }
                    
                    $article_image = $row['image'];
                    $article_date = $row['posted'];
                    $author = 'Medicalwale.com';
                    $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;

                    $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                    $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                    $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                    $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();

                    $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();
                    $skin_list[] = array(
                        'article_id' => $article_id,
                        'category_id' => $cat_id,
                        'article_title' => $article_title,
                        'article_description' => $article_description,
                        'short_desc' =>"",
                        'article_image' => $article_image,
                        'article_date' => $article_date,
                        'share_url' => "",
                        'author' => $author,
                        'like_count' => $like_count,
                        'like_yes_no' => $like_yes_no,
                        'total_views' => $views_count,
                        'is_bookmark' => $is_bookmark,
                        'total_bookmark' => $total_bookmark,
                        'is_follow' => "No",
                        'total_follow' =>0
                    );
                }
                $skin_array[] = array(
                    'title' => 'Skin Disease',
                    'listing_type' => 32,
                    'array' => $skin_list
                );
                
                return $skin_array;
            } else {
               return $skin_array = array();
            }
                  }
                   else if($type == '9')
                  {
                      
                       $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
              
              $lang=$query_lang->language;
              
             
                         // Articles
            $query = $this->db->query("SELECT article.id, `cat_id`, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date`, article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id  where  (article_title like '%$keyword%' or article_description like '%$keyword%') and article_category.type='home_remedies'  limit 20");

            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $article_id = $row['id'];
                    $cat_id = $row['cat_id'];
                  //  $article_title = $row['article_title'];
                //    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                    
                      if($lang == 1)
                {
                    $article_title = $this->decrypt(($row['article_title']));
                    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
        
                }
                else
                {
                    $article_title = $row['article_title'];
                //    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                   $article_description = $row['article_description'];
                }
                    
                    $article_image = $row['image'];
                    $article_date = $row['posted'];
                    $author = 'Medicalwale.com';
                    $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;

                    $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                    $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                    $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                    $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();

                    $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();
                    $mom_list[] = array(
                        'article_id' => $article_id,
                        'category_id' => $cat_id,
                        'article_title' => $article_title,
                        'article_description' => $article_description,
                        'short_desc' =>"",
                        'article_image' => $article_image,
                        'article_date' => $article_date,
                        'share_url' => "",
                        'author' => $author,
                        'like_count' => $like_count,
                        'like_yes_no' => $like_yes_no,
                        'total_views' => $views_count,
                        'is_bookmark' => $is_bookmark,
                        'total_bookmark' => $total_bookmark,
                        'is_follow' => "No",
                        'total_follow' =>0
                    );
                }
                $mom_array[] = array(
                    'title' => 'mom Remedies',
                    'listing_type' => 32,
                    'array' => $mom_list
                );
                
                return $mom_array;
            } else {
               return $mom_array = array();
            }
                  }
                  else if($type == '10')
                  {
                        // Survivor's Stories
            $survivor_query = $this->db->query("SELECT id,title,description,tag,author,image,date FROM `survival_stories` where is_active='1' and (title like '%$keyword%'  or description like '%$keyword%') order by id desc limit 50");
            $survivor_count = $survivor_query->num_rows();
            if ($survivor_count > 0) {
                foreach ($survivor_query->result_array() as $row) {
                    $id = $row['id'];
                    $title = $row['title'];
                    $description = $row['description'];
                    $tag = $row['tag'];
                    $author = $row['author'];
                    $image = $row['image'];
                    $date = $row['date'];
                    $image = str_replace(" ", "", $image);
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $image;

                    $bookmark_yes_no = $this->db->select('id')->from('survival_stories_bookmark')->where('user_id', $user_id)->where('survival_stories_id', $id)->get()->num_rows();

                    $share = 'https://d2c8oti4is0ms3.cloudfront.net/images/';

                    $survivor_list[] = array(
                        "article_id" => $id,
                        "article_title" => $title,
                        'article_description' => $description,
                        'short_desc' => "",
                        'tag' => $tag,
                        'author' => $author,
                        'article_image' => $image,
                        'article_date' => $date,
                        'share_url' => $share,
                        'like_count' => 0,
                        'like_yes_no' =>0,
                        'total_views' => 0,
                        'is_bookmark' => 0,
                        'total_bookmark' => 0,
                        'is_follow' => "No",
                        'total_follow' => 0
                    );
                }
                $survivor_array[] = array(
                    'title' => "Survivor's Stories",
                    'listing_type' => 33,
                    'array' => $survivor_list
                );
                
                return $survivor_array;
            } else {
               return $survivor_array = array();
            }
 
                  }
                  else if($type == '11') 
                  {
                         // Pharmacy
            $field1 = '';
            $field2 = '';
            $field3 = '';
            $index_id="medical";
            $pharmacy=$this->elasticsearchsize3($index_id,$keyword);
            $pharmacy_count=count($pharmacy);
            if ($pharmacy_count > 0) {
               $pharmacy_array[] = array(
                    'title' => 'Pharmacy',
                    'listing_type' => 13,
                    'array' => $pharmacy
                );
                
                return $pharmacy_array;
            } else {
                return  $pharmacy_array = array();
            }

                  }
                  else if($type=='12'){
                       // Labs
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
             
                $index_id="lab";
                   $labs=$this->elasticsearchsize3($index_id,$keyword);
            if (!empty($labs)) {
            
                $labs_array[] = array(
                    'title' => 'Labs',
                    'listing_type' => 10,
                    'array' => $labs
                );
            } else {
                $labs_array = array();
            }
            
             // Labs test
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
             
                $index_id="lab_test";
                   $labs_test=$this->elasticsearchsize3($index_id,$keyword);
            if (!empty($labs_test)) {
            
                $labs_test_array[] = array(
                    'title' => 'Labs test',
                    'listing_type' => 10,
                    'array' => $labs_test
                );
            } else {
                $labs_test_array = array();
            }
            
             // Labs package
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
             
                $index_id="lab_package";
                   $labs_package=$this->elasticsearchsize3($index_id,$keyword);
            if (!empty($labs_package)) {
            
                $labs_package_array[] = array(
                    'title' => 'Labs Package',
                    'listing_type' => 10,
                    'array' => $labs_package
                );
            } else {
                $labs_package_array = array();
            }
            
             $resultpost = array_merge($labs_array,$labs_test_array,$labs_package_array);
          
            return $resultpost;
                      
                  }
                  else if($type == '23')
                    {
                           // Labs
                $field1 = '';
                $field2 = '';
                $field3 = '';
                $index_id="lab";
                $labs=$this->elasticsearchsize3($index_id,$keyword);
                $labs_count=count($labs);
                if ($labs_count > 0) {
                    $labs_array[] = array(
                        'title' => 'Labs',
                        'listing_type' => 10,
                        'array' => $labs
                    );
                    return $labs_array;
                } else {
                   return $labs_array = array();
                }
                          
                      }
                else if($type == '13'){
                $index_id="lab_test";
                   $labs_test=$this->elasticsearchsize3($index_id,$keyword);
            if (!empty($labs_test)) {
            
                $labs_test_array[] = array(
                    'title' => 'Labs test',
                    'listing_type' => 10,
                    'array' => $labs_test
                );
                return $labs_test_array;
            } else {
               return $labs_test_array = array();
            }
            
                 }else if($type == '14'){
                $index_id="lab_package";
                $labs_package=$this->elasticsearchsize3($index_id,$keyword);
                    if (!empty($labs_package)) {
                    
                        $labs_package_array[] = array(
                            'title' => 'Labs Package',
                            'listing_type' => 10,
                            'array' => $labs_package
                        );
                        return $labs_package_array;
                    } else {
                     return   $labs_package_array = array();
                    }
                      
                }
                else if($type == "15"){
                      // Spa Center
                      
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
                
                    $index_id="spa_listing";
                        
                    $spa_center=$this->elasticsearchspa($index_id,$keyword);
              
                    if (!empty($spa_center)) {
                        foreach($spa_center as $sp_c){
                       $spa_array[]=array('listing_id'=>$sp_c['user_id'],
                        'branch_id'=>$sp_c['id'],
                        'name'=>$sp_c['branch_name'],
                        'image'=>$sp_c['branch_image'],
                        'address'=>$sp_c['branch_address'], 
                        'field2'=>"",
                         'listing_type' => 36,
                        'field3'=>"");
                        }
                     
                        $spa_center_array[] = array(
                            'title' => 'Spa Center',
                            'listing_type' => 36,
                            'array' => $spa_array
                        );
                    } else {
                        $spa_center_array = array();
                    }
                    
              // Spa Package
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
                
                  $index_id="spa_package";
                        
                     $spa_package=$this->elasticsearchspa_package($index_id,$keyword);
                   /*  print_r($spa_center);
                     die();*/
                    if (!empty($spa_package)) {
                        foreach($spa_package as $sp_c){
                       $spa_parray[]=array('listing_id'=>$sp_c['package_id'],
                        'name'=>$sp_c['package_name'],
                        'image'=>$sp_c['package_image'],
                        'price'=>$sp_c['package_price'], 
                        'centername'=>$sp_c['branch_name'],
                         'listing_type' => 36,
                        'listing_id'=>$sp_c['user_id'],
                        'package_id'=>$sp_c['package_id']);
                        }
                        
                        $spa_p_array[] = array(
                            'title' => 'Spa Package',
                            'listing_type' => 36,
                            'array' => $spa_parray
                        );
                    } else {
                        $spa_p_array = array();
                    }        
            $resultpost = array_merge($spa_center_array,$spa_p_array);
             return $resultpost;

                }
                else if($type == '25'){
                       // Spa Center
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
                
                    $index_id="spa_listing";
                        
                    $spa_center=$this->elasticsearchspa($index_id,$keyword);
              
                    if (!empty($spa_center)) {
                        foreach($spa_center as $sp_c){
                       $spa_array[]=array('listing_id'=>$sp_c['user_id'],
                        'branch_id'=>$sp_c['id'],
                        'name'=>$sp_c['branch_name'],
                        'image'=>$sp_c['branch_image'],
                        'address'=>$sp_c['branch_address'], 
                        'field2'=>"",
                         'listing_type' => 36,
                        'field3'=>"");
                        }
                        
                        $spa_center_array[] = array(
                            'title' => 'Spa Center',
                            'listing_type' => 36,
                            'array' => $spa_array
                        );
                        return $spa_center_array;
                    } else {
                       return $spa_center_array = array();
                    }
                    }else if($type == '16'){ 
              // Spa Package
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
                
                  $index_id="spa_package";
                        
                     $spa_package=$this->elasticsearchspa_package($index_id,$keyword);
                   /*  print_r($spa_center);
                     die();*/
                    if (!empty($spa_package)) {
                        foreach($spa_package as $sp_c){
                       $spa_parray[]=array('listing_id'=>$sp_c['package_id'],
                        'name'=>$sp_c['package_name'],
                        'image'=>$sp_c['package_image'],
                        'price'=>$sp_c['package_price'], 
                        'centername'=>$sp_c['branch_name'],
                        'listing_type' => 36,
                        'listing_id'=>$sp_c['user_id'],
                        'package_id'=>$sp_c['package_id']);
                        }
                        
                        $spa_center_array[] = array(
                            'title' => 'Spa Package',
                             'listing_type' => 36,
                            'array' => $spa_parray
                        );
                         return $spa_center_array;
                    } else {
                        return $spa_center_array = array();
                    }        
            

                      
                      
                      
                  }
                 
                  else if($type == '19'){
                        // Hospital
                $field1 = '';
                $field2 = '';
                $field3 = '';
               $index_id="hospital";
                      $hospital=$this->elasticsearchsize3($index_id,$keyword);  
                if (!empty($hospital)) {
                     
                    $hospital_array[] = array(
                        'title' => 'Hospital',
                        'listing_type' => 8,
                        'array' => $hospital
                    );
                } else {
                    $hospital_array = array();
                }
           
           
                  // hospital_package
                $field1 = '';
                $field2 = '';
                $field3 = '';
           
                
                  $index_id="hospital_package";
                        
                     $hospital_package=$this->elasticsearchhospital_package($index_id,$keyword);
                 
                    if (!empty($hospital_package)) {
                        foreach($hospital_package as $sp_c){
                       $hospital_parray[]=array('id'=>$sp_c['id'],
                        'name'=>$sp_c['package_name'],
                        'package_desc'=>$sp_c['package_desc'],
                        'package_amount'=>$sp_c['booking_amount'], 
                        'hospital_id'=>$sp_c['hospital_id'],
                        'type'=>$sp_c['treatment_type'],
                        'cat_id'=>$sp_c['package_type']);
                        }
                        
                        $hospital_package_array[] = array(
                            'title' => 'hospital Package',
                            'listing_type' => 8,
                            'array' => $hospital_parray
                        );
                    } else {
                        $hospital_package_array = array();
                    }  
                    
                      $resultpost = array_merge($hospital_array,$hospital_package_array);
          
                    return $resultpost;

                  }
                   else if($type == '17'){
                    // hospital_package
            $field1 = '';
            $field2 = '';
            $field3 = '';
           
                
                  $index_id="hospital_package";
                        
                     $hospital_package=$this->elasticsearchhospital_package($index_id,$keyword);
                 
                    if (!empty($hospital_package)) {
                        foreach($hospital_package as $sp_c){
                       $hospital_parray[]=array('id'=>$sp_c['id'],
                        'name'=>$sp_c['package_name'],
                        'package_desc'=>$sp_c['package_desc'],
                        'package_amount'=>$sp_c['booking_amount'], 
                        'hospital_id'=>$sp_c['hospital_id'],
                        'type'=>$sp_c['treatment_type'],
                        'cat_id'=>$sp_c['package_type']);
                        
                        }
                        
                        $hospital_package_array[] = array(
                            'title' => 'hospital Package',
                            'listing_type' => 8,
                            'array' => $hospital_parray
                        );
                         return $hospital_package_array;
                    } else {
                        return $hospital_package_array = array();
                    }  
                  
                  
                 } 
                 else if($type == '18')
                  {
                     // Fitness Center
            $field1 = '';
            $field2 = '';
            $field3 = '';
            $index_id="fitness";
            $fitness=$this->elasticsearchsize3($index_id,$keyword);
            $fitness_count=count($fitness);
            if ($fitness_count > 0) {
                $fitness_array[] = array(
                    'title' => 'Fitness',
                    'listing_type' => 6,
                    'array' => $fitness
                );
                return $fitness_array;
            } else {
              return $fitness_array = array();
            }  
                  }
                  else if($type == '24')
                  {
                        // Hospital
            $field1 = '';
            $field2 = '';
            $field3 = '';
            
            $index_id="hospital";
            $hospital=$this->elasticsearchsize3($index_id,$keyword);
            $hospital_count=count($hospital);
            if ($hospital_count > 0) {
                $hospital_array[] = array(
                    'title' => 'Hospital',
                    'listing_type' => 8,
                    'array' => $hospital
                );
                return $hospital_array;
            } else {
                return $hospital_array = array();
            }
                  }
                  else if($type == '20')
                  {
                       // Nursing Attendant
            $field1 = '';
            $field2 = '';
            $field3 = '';
            $index_id="nursing_attendant";
            $nursing=$this->elasticsearchsize3($index_id,$keyword);
            $nursing_count=count($nursing);
            if ($nursing_count > 0) {
                
                $nursing_array[] = array(
                    'title' => 'Nursing',
                    'listing_type' => 12,
                    'array' => $nursing
                );
                return $nursing_array;
            } else {
               return $nursing_array = array();
            }
                  }
                  else if($type == '21')
                  {
                       // Post
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
            $media_array = array();
            $query = $this->db->query("select posts.id as post_id,posts.type as post_type,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category,posts.user_id as post_user_id,IFNULL(posts.post_location,'') AS post_location,posts.repost_location,posts.is_repost,posts.repost_time, IFNULL(posts.repost_user_id,'') AS repost_user_id,users.name,users.vendor_id,healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' and users.name like '%$keyword%' order by posts.id desc limit 50");
            $healthwall_count = $query->num_rows();
            if ($healthwall_count > 0) {
                foreach ($query->result_array() as $row) {
                    $post_id = $row['post_id'];
                    $listing_type = $row['vendor_id'];
                    $post_location = $row['post_location'];
                    $post_id = $row['post_id'];
                    $post = $row['post'];
                    if ($post != '') {
                    $post = preg_replace('~[\r\n]+~', '', $post);
                    if ($post_id > '2938') {
                        $decrypt = $this->decrypt($post);
                        $encrypt = $this->encrypt($decrypt);
                        if ($encrypt == $post) {
                            $post = $decrypt;
                        }
                    } else {
                        if (base64_encode(base64_decode($post)) === $post) {
                            $post = base64_decode($post);
                        }
                    }
                }
                    $article_title = $row['article_title'];
                    $article_image = $row['article_image'];
                    $article_domain_name = $row['article_domain_name'];
                    $article_url = $row['article_url'];
                    $category = '';
                    $is_anonymous = $row['is_anonymous'];
                    $tag = $row['tag'];
                    $post_type = $row['post_type'];
                    $date = $row['created_at'];
                    $caption = $row['caption'];
                    $username = $row['name'];
                    $post_user_id = $row['post_user_id'];
                    $repost_user_id = $row['repost_user_id'];
                    $repost_location = $row['repost_location'];
                    $is_repost = $row['is_repost'];
                    $repost_time = $row['repost_time'];
                    $healthwall_category_name = $row['healthwall_category'];
                    $healthwall_category_id = $row['healthwall_category_id'];
                    
                    $share_url = "https://medicalwale.com/share/healthwall/" . $post_id; 
                    $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                    if ($img_count > 0) {
                        $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                        $img_file = $profile_query->source;
                        $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                    } else {
                        $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }

                   $media_query = $this->db->query("SELECT media.id AS media_id,media.type AS media_type,IFNULL(media.caption,'') AS caption,media.source,post_media.img_width,post_media.img_height,post_media.video_width,post_media.video_height FROM media INNER JOIN post_media on media.id=post_media.media_id where post_media.post_id='$post_id'");
                $img_val = '';
                $images = '';
                $img_comma = '';
                $img_width = '';
                $img_height = '';
                $video_width = '';
                $video_height = '';

                $media_array = array();
                foreach ($media_query->result_array() as $media_row) {
                    $media_id = $media_row['media_id'];
                    $media_type = $media_row['media_type'];
                    $source = $media_row['source'];
                    if ($media_type == 'video') {
                        $thumb = 'https://d1g6k76aztls2p.cloudfront.net/healthwall_media/video/' . substr_replace($source, '_thumbnail.jpeg', strrpos($source, '.') + 0);
                        $images = 'https://d2ua8z9537m644.cloudfront.net/healthwall_media/' . $media_type . '/' . $source;
                    } else {
                        $thumb = '';
                        $images = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/' . $media_type . '/' . $source;
                    }
                    $caption = $media_row['caption'];
                    $img_width = $media_row['img_width'];
                    $img_height = $media_row['img_height'];
                    $video_width = $media_row['video_width'];
                    $video_height = $media_row['video_height'];
                    $view_media_count = $this->db->select('id')->from('post_video_views')->where('media_id', $media_id)->get()->num_rows();

                    $view_media_yes_no_query = $this->db->query("SELECT id from post_video_views WHERE media_id='$media_id' and user_id='$user_id'");
                    $view_media_yes_no = $view_media_yes_no_query->num_rows();

                    $media_array[] = array(
                        'media_id' => $media_id,
                        'type' => $media_type,
                        'images' => str_replace(' ', '%20', $images),
                        'thumb' => $thumb,
                        'caption' => $caption,
                        'img_height' => $img_height,
                        'img_width' => $img_width,
                        'video_height' => $video_height,
                        'video_width' => $video_width,
                        'video_views' => $view_media_count,
                        'video_view_yes_no' => $view_media_yes_no
                    );
                }

                    $like_count = $this->db->select('id')->from('post_likes')->where('post_id', $post_id)->get()->num_rows();
                    $comment_count = $this->db->select('id')->from('comments')->where('post_id', $post_id)->get()->num_rows();
                    $follow_count = $this->db->select('id')->from('post_follows')->where('post_id', $post_id)->get()->num_rows();
                    $view_count = $this->db->select('id')->from('post_views')->where('post_id', $post_id)->get()->num_rows();

                    $like_yes_no_query = $this->db->query("SELECT id from post_likes where post_id='$post_id' and user_id='$user_id'");
                    $like_yes_no = $like_yes_no_query->num_rows();

                    $follow_yes_no_query = $this->db->query("SELECT id from post_follows where post_id='$post_id' and user_id='$user_id'");
                    $follow_post_yes_no = $follow_yes_no_query->num_rows();

                    $view_yes_no_query = $this->db->query("SELECT id from post_views where post_id='$post_id' and user_id='$user_id'");
                    $view_post_yes_no = $view_yes_no_query->num_rows();
                    
                    $is_reported_query = $this->db->query("SELECT id from post_report WHERE post_id='$post_id' AND reporter_id='$user_id' AND post_type='healthwall'");
                    $is_reported = $is_reported_query->num_rows();
    
                    $is_post_save_query = $this->db->query("SELECT id from post_save WHERE post_id='$post_id' AND user_id='$user_id' AND post_type='healthwall'");
                    $is_post_save = $is_post_save_query->num_rows();
                
                    $date = get_time_difference_php($date);
                    
                     //comments
                    $query_comment = $this->db->query("SELECT comments.id,comments.post_id,comments.description as comment,comments.updated_at as date,users.name,comments.user_id as post_user_id FROM comments INNER JOIN users on users.id=comments.user_id WHERE comments.post_id='$post_id' order by comments.id desc LIMIT 0,3");
                    $comments = array();
                    $comment_counts = $query_comment->num_rows();
                    if ($comment_counts > 0) {
                        foreach ($query_comment->result_array() as $rows) {
                            $comment_id = $rows['id'];
                            $comment_post_id = $rows['post_id'];
                            $comment = $rows['comment'];
                            $comment = preg_replace('~[\r\n]+~', '', $comment);
                            if ($comment_id > '9547') {
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
                            $comment_username = $rows['name'];
                            $comment_date = $rows['date'];
                            $comment_post_user_id = $rows['post_user_id'];
                            $comment_like_count = $this->db->select('id')->from('comment_likes')->where('comment_id', $comment_id)->get()->num_rows();
                            $comment_like_yes_no = $this->db->select('id')->from('comment_likes')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
                            $comment_date = $comment_date;
                            $comment_img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->num_rows();
                            if ($comment_img_count > 0) {
                                $comment_profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->row();
                                $comment_img_file = $comment_profile_query->source;
                                $comment_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $comment_img_file;
                            } else {
                                $comment_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                            }
                            $query_listing_type = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_post_user_id'");
                            $get_type = $query_listing_type->row_array();
                            $com_listing_type = $get_type['vendor_id'];
                            $comment_reply_array = '';
                            $query_comment_reply = $this->db->query("SELECT comments_reply.id,comments_reply.comment_id,comments_reply.post_id,comments_reply.comment,comments_reply.updated_at as date,users.name,comments_reply.user_id FROM comments_reply INNER JOIN users on users.id=comments_reply.user_id WHERE comments_reply.post_id='$post_id' and comments_reply.comment_id='$comment_id' order by comments_reply.id desc LIMIT 0,3");
                            $comment_reply = array();
                            $comment_count_reply = $query_comment_reply->num_rows();
                            if ($comment_count_reply > 0) {
                                foreach ($query_comment_reply->result_array() as $rows_reply) {
                                    $comment_id = $rows_reply['comment_id'];
                                    $comment_reply_id = $rows_reply['id'];
                                    $comment_reply_post_id = $rows_reply['post_id'];
                                    $comment_reply_username = $rows_reply['name'];
                                    $comment_reply_date = $rows_reply['date'];
                                    $comment_reply_user_id = $rows_reply['user_id'];
                                    $comment_reply = $rows_reply['comment'];
                                    if ($comment_reply != '' && is_numeric($comment_reply)) {
                                    $comment_reply = preg_replace('~[\r\n]+~', '', $comment_reply);
                                    $comment_reply_decrypt = $this->decrypt($comment_reply);
                                    $comment_reply_encrypt = $this->encrypt($comment_reply_decrypt);
                                        if ($comment_reply_encrypt == $comment_reply) {
                                          $comment_reply = $comment_reply_decrypt;
                                        }
                                    }
                                    
                                    $comment_reply_like_count = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->get()->num_rows();
                                    $comment_reply_like_yes_no = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->where('user_id', $user_id)->get()->num_rows();
                                    $comment_reply_date = $comment_reply_date;
                                    $comment_reply_img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->num_rows();
                                    if ($comment_reply_img_count > 0) {
                                        $comment_reply_profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->row();
                                        $comment_reply_img_file = $comment_profile_query->source;
                                        $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $comment_reply_img_file;
                                    } else {
                                        $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                                    }
                                    $query_reply_listing_type = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_reply_user_id'");
                                    $get_reply_type = $query_reply_listing_type->row_array();
                                    $com_reply_listing_type = $get_reply_type['vendor_id'];
                                    $comment_reply_array[] = array(
                                        'id' => $comment_reply_id,
                                        'listing_type' => $com_reply_listing_type,
                                        'comment_user_id' => $comment_reply_user_id,
                                        'username' => $comment_reply_username,
                                        'userimage' => $comment_reply_userimage,
                                        'like_count' => $comment_reply_like_count,
                                        'like_yes_no' => $comment_reply_like_yes_no,
                                        'post_id' => $comment_reply_post_id,
                                        'comment' => $comment_reply,
                                        'comment_date' => $comment_reply_date
                                    );
                                }
                            } else {
                                $comment_reply_array = array();
                            }
                            $comments[] = array(
                                'id' => $comment_id,
                                'listing_type' => $com_listing_type,
                                'comment_user_id' => $comment_post_user_id,
                                'username' => $comment_username,
                                'userimage' => $comment_userimage,
                                'like_count' => $comment_like_count,
                                'like_yes_no' => $comment_like_yes_no,
                                'post_id' => $comment_post_id,
                                'comment' => $comment,
                                'comment_date' => $comment_date,
                                'comment_reply' => $comment_reply_array
                            );
                        }
                    } else {
                        $comments = array();
                    }
                    //comments
                    
                    $repost_user_name = "";
                    $repost = array();
                    if ($is_repost) {
                        if (!empty($repost_user_id) && $repost_user_id != "") {
                            $listing_type_1 = $this->db->query("select vendor_id from users where id = '$repost_user_id' ")->row();
                            if(!empty($listing_type_1))
                            {
                                $listing_type = $listing_type_1->vendor_id;
                            }
                            else
                            {
                                $listing_type = "";
                            }
                            $result = $this->db->query("select users.vendor_id as listing_type, media.title, media.source as source, users.avatar_id from users join media on(users.avatar_id = media.id) where users.id = '$repost_user_id' ");
                            $this->db->select("users.vendor_id as listing_type, media.title, media.source as source, users.avatar_id");
                            $this->db->from('users');
                            $this->db->where("users.id ", $repost_user_id);
                            $this->db->join('media', 'users.avatar_id = media.id');
                            $respost_sorce = $this->db->get()->row();
                            if (!empty($respost_sorce)) {
                                $respost_sorce = $respost_sorce->source;
                            } else {
                                $respost_sorce = "user_avatar.jpg";
                            }
                            //$post_user_id = $row['repost_user_id'];
                           // $repost_user_name = $this->db->select('*')->where('id', $repost_user_id)->get('users')->row()->name;
                            $repost_user_name_1 = $this->db->select('*')->where('id', $repost_user_id)->get('users')->row();
                            if(!empty($repost_user_name_1))
                            {
                                $repost_user_name = $repost_user_name_1->name;
                            }
                            else
                            {
                                $repost_user_name ="";
                            }
                            $repost[] = array(
                                'repost_user_id' => $repost_user_id,
                                'repost_user_name' => $repost_user_name,
                                'repost_location' => $repost_location,
                                'repost_time' => $repost_time,
                                'listing_type' => $listing_type,
                                'repost_user_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/" . $respost_sorce
                            );
                        }
                        if (is_null($repost_user_name) || $repost_user_name == "") {
                            $repost_user_name = '';
                        }
                    } else {
                        $repost = [];
                    }
                    $query_is_repost = $this->db->query("SELECT * FROM `posts` WHERE `user_id`='$post_user_id' AND `repost_user_id`='$user_id' AND `id`='$post_id'");
                    $repost_counts = $query_is_repost->num_rows();
                    if ($repost_counts > 0) {
                        $flag = '1';
                    } else {
                        $flag = '0';
                    }
                
                
                    $healthwall_list[] = array(
                        'id' => $post_id,
                        'post_user_id' => $post_user_id,
                        'listing_type' => $listing_type,
                        'post_location' => $post_location,
                        'healthwall_category' => $healthwall_category_name,
                        'healthwall_category_id' => $healthwall_category_id,
                        'username' => $username,
                        'userimage' => $userimage,
                        'post_type' => $post_type,
                        'post' => str_replace('\n', '', $post),
                        'article_title' => str_replace('null', '', $article_title),
                        'article_image' => str_replace('null', '', $article_image),
                        'article_domain_name' => str_replace('null', '', $article_domain_name),
                        'article_url' => str_replace('null', '', $article_url),
                        'is_anonymous' => $is_anonymous,
                        'tag' => $tag,
                        'category' => $category,
                        'like_count' => $like_count,
                        'follow_count' => $follow_count,
                        'like_yes_no' => $like_yes_no,
                        'follow_post_yes_no' => $follow_post_yes_no,
                        'comment_count' => $comment_count,
                        'views' => $view_count,
                        'view_yes_no' => $view_post_yes_no,
                        'media' => $media_array,
                        'share_url' => $share_url,
                        'date' => $date,
                        'is_reported' => $is_reported,
                        'is_post_save' => $is_post_save,
                        'comments' => $comments,
                        'is_repost' => $is_repost,
                        'repost' => $repost,
                        'repost_flag' => $flag
                    );
                }
               return $healthwall_array[] = array(
                    'title' => 'Post',
                    'listing_type' => 31,
                    'array' => $healthwall_list
                );
            } else {
               return $healthwall_array = array();
            }
                  }
                   else if($type == '22')
                  {
                    
                  $field1 = '';
            $field2 = '';
            $field3 = '';
            $index_id="hm_product";
            $hm_product=$this->elasticsearchhm_product($index_id,$keyword); 
            $hm_product_count=count($hm_product);
            if ($hm_product_count > 0) {
                
                
                 foreach($hm_product as $a){
                    $string = strip_tags($a['pd_short_desc']);
                         if (strlen($string) > 80) {
                    $stringCut = substr($string, 0, 80);
                    $endPoint = strrpos($stringCut, ' ');
                    $string = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                    $string .= '...';
                         }
                  $hm_product1[] = array(
                        'listing_id' => $a['pd_id'],
                        'name' => $a['pd_name'],
                        'image' => $a['pd_photo_1'],
                        'field1' => $a['pd_mrp_price'],
                        'field2' =>$a['pd_vendor_price'],
                        'field3' => $string
                    );
                 }
                 
                $hm_product_array[] = array(
                    'title' => 'Health Mall',
                    'listing_type' => 6,
                    'array' => $hm_product1
                );
                return $hm_product_array;
            } else {
              return $hm_product_array = array();
            }  
                  }
               // return $resultpost = array_merge($people_array,$doctor_array);
              }
         else
         {
            return $resultpost = array();   
         }
         //return $resulpost;
    }
    
    
    
    
    //end

    public function page_list($user_id, $keyword, $listing_type, $page) {
        if ($user_id > 0) {
            $limit = 10;
            $start = 0;
            if ($page > 0) {
                if (!is_numeric($page)) {
                    $page = 1;
                }
            }
            $start = ($page - 1) * $limit;
            $field1 = '';
            $field2 = '';
            $field3 = '';

            if ($listing_type == '0') {
                // People
                $people_query = $this->db->query("SELECT id,name FROM `users` WHERE name like '%$keyword%' order by name asc limit $start, $limit");
                $people_count = $people_query->num_rows();
                if ($people_count > 0) {
                    foreach ($people_query->result_array() as $people_row) {
                        $user_id = $people_row['id'];
                        $name = $people_row['name'];
                        $listing_type = '0';
                        $media_query = $this->db->query("SELECT media.source FROM media LEFT JOIN users on users.avatar_id=media.id WHERE users.id='$user_id' limit 1");
                        $media_count = $media_query->num_rows();
                        if ($media_count > 0) {
                            $media_row = $media_query->row_array();
                            $img_file = $media_row['source'];
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                        } else {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        $people[] = array(
                            'listing_id' => $user_id,
                            'name' => $name,
                            'image' => $image,
                            'field1' => $field1,
                            'field2' => $field2,
                            'field3' => $field3
                        );
                    }
                    $people_array[] = array(
                        'title' => 'People',
                        'listing_type' => 0,
                        'array' => $people
                    );
                } else {
                    $people_array = array();
                }
            }

            if ($listing_type == '5') {
                // Doctor
                $doctor_query = $this->db->query("SELECT user_id,image,doctor_name,speciality,qualification,address FROM doctor_list WHERE doctor_name like '%$keyword%' limit $start, $limit");
                $doctor_count = $doctor_query->num_rows();
                if ($doctor_count > 0) {
                    foreach ($doctor_query->result_array() as $doctor_row) {
                        $user_id = $doctor_row['user_id'];
                        $name = str_replace("null", "", $doctor_row['doctor_name']);
                        $field1 = str_replace("null", "", $doctor_row['speciality']);
                        $field2 = str_replace("null", "", $doctor_row['qualification']);
                        $field3 = str_replace("null", "", $doctor_row['address']);
                        $doctor_image = str_replace("null", "", $doctor_row['image']);
                        if ($doctor_image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $doctor_image;
                        } else {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        $doctor[] = array(
                            'listing_id' => $user_id,
                            'name' => $name,
                            'image' => $image,
                            'field1' => $field1,
                            'field2' => $field2,
                            'field3' => $field3
                        );
                    }
                    $doctor_array[] = array(
                        'title' => 'Doctor',
                        'listing_type' => 5,
                        'array' => $doctor
                    );
                } else {
                    $doctor_array = array();
                }
            }

            if ($listing_type == '13') {
                // Pharmacy
                $pharmacy_query = $this->db->query("SELECT user_id,profile_pic,medical_name,address1 FROM medical_stores WHERE medical_name like '%$keyword%' limit $start, $limit");
                $pharmacy_count = $pharmacy_query->num_rows();
                if ($pharmacy_count > 0) {
                    foreach ($pharmacy_query->result_array() as $pharmacy_row) {
                        $user_id = $pharmacy_row['user_id'];
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
                        $pharmacy[] = array(
                            'listing_id' => $user_id,
                            'name' => $name,
                            'image' => $image,
                            'field1' => $field1,
                            'field2' => $field2,
                            'field3' => $field3
                        );
                    }
                    $pharmacy_array[] = array(
                        'title' => 'Pharmacy',
                        'listing_type' => 13,
                        'array' => $pharmacy
                    );
                } else {
                    $pharmacy_array = array();
                }
            }

            if ($listing_type == '1') {
                // Ayurveda
                $ayurveda_query = $this->db->query("SELECT profile_pic,medical_name,address1 FROM ayurveda WHERE medical_name like '%$keyword%' limit $start, $limit");
                $ayurveda_count = $ayurveda_query->num_rows();
                if ($ayurveda_count > 0) {
                    foreach ($ayurveda_query->result_array() as $ayurveda_row) {
                        $name = str_replace("null", "", $ayurveda_row['medical_name']);
                        $field1 = '';
                        $field2 = '';
                        $field3 = str_replace("null", "", $ayurveda_row['address1']);
                        $ayurveda_image = str_replace("null", "", $ayurveda_row['profile_pic']);
                        if ($ayurveda_image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/ayurveda_images/' . $ayurveda_image;
                        } else {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        $ayurveda[] = array(
                            'listing_id' => $user_id,
                            'name' => $name,
                            'image' => $image,
                            'field1' => $field1,
                            'field2' => $field2,
                            'field3' => $field3
                        );
                    }
                    $ayurveda_array[] = array(
                        'title' => 'Ayurveda',
                        'listing_type' => 1,
                        'array' => $ayurveda
                    );
                } else {
                    $ayurveda_array = array();
                }
            }

            if ($listing_type == '9') {
                // Homeopathic
                $homeopathic_query = $this->db->query("SELECT user_id,image,doctor_name,speciality,qualification,address FROM doctor_list WHERE doctor_name like '%$keyword%' limit $start, $limit");
                $homeopathic_count = $homeopathic_query->num_rows();
                if ($homeopathic_count > 0) {
                    foreach ($homeopathic_query->result_array() as $homeopathic_row) {
                        $user_id = $homeopathic_row['user_id'];
                        $name = str_replace("null", "", $homeopathic_row['doctor_name']);
                        $field1 = str_replace("null", "", $homeopathic_row['speciality']);
                        $field2 = str_replace("null", "", $homeopathic_row['qualification']);
                        $field3 = str_replace("null", "", $homeopathic_row['address']);
                        $homeopathic_image = str_replace("null", "", $doctor_row['image']);
                        if ($homeopathic_image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $homeopathic_image;
                        } else {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        $homeopathic[] = array(
                            'listing_id' => $user_id,
                            'name' => $name,
                            'image' => $image,
                            'field1' => $field1,
                            'field2' => $field2,
                            'field3' => $field3
                        );
                    }
                    $homeopathic_array[] = array(
                        'title' => 'homeopathic',
                        'listing_type' => 9,
                        'array' => $homeopathic
                    );
                } else {
                    $homeopathic_array = array();
                }
            }

            if ($listing_type == '10') {
                // Labs
                $labs_query = $this->db->query("SELECT user_id,profile_pic,lab_name,address1 FROM lab_center WHERE lab_name like '%$keyword%' limit $start, $limit");
                $labs_count = $labs_query->num_rows();
                if ($labs_count > 0) {
                    foreach ($labs_query->result_array() as $labs_row) {
                        $user_id = $labs_row['user_id'];
                        $name = str_replace("null", "", $labs_row['lab_name']);
                        $field1 = '';
                        $field2 = '';
                        $field3 = str_replace("null", "", $labs_row['address1']);
                        $labs_image = str_replace("null", "", $labs_row['profile_pic']);
                        if ($labs_image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/labcenter_images/' . $labs_image;
                        } else {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        $labs[] = array(
                            'listing_id' => $user_id,
                            'name' => $name,
                            'image' => $image,
                            'field1' => $field1,
                            'field2' => $field2,
                            'field3' => $field3
                        );
                    }
                    $labs_array[] = array(
                        'title' => 'labs',
                        'listing_type' => 10,
                        'array' => $labs
                    );
                } else {
                    $labs_array = array();
                }
            }

            if ($listing_type == '12') {
                // Nursing Attendant
                $nursing_query = $this->db->query("SELECT user_id,name,address,image FROM nursing_attendant WHERE name like '%$keyword%' limit $start, $limit");
                $nursing_count = $nursing_query->num_rows();
                if ($nursing_count > 0) {
                    foreach ($nursing_query->result_array() as $nursing_row) {
                        $user_id = $nursing_row['user_id'];
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
                        $nursing[] = array(
                            'listing_id' => $user_id,
                            'name' => $name,
                            'image' => $image,
                            'field1' => $field1,
                            'field2' => $field2,
                            'field3' => $field3
                        );
                    }
                    $nursing_array[] = array(
                        'title' => 'nursing',
                        'listing_type' => 12,
                        'array' => $nursing
                    );
                } else {
                    $nursing_array = array();
                }
            }

            if ($listing_type == '16') {
                // Cupping
                $cupping_query = $this->db->query("SELECT user_id,name,address,image FROM cuppingtherapy WHERE name like '%$keyword%' limit $start, $limit");
                $cupping_count = $cupping_query->num_rows();
                if ($cupping_count > 0) {
                    foreach ($cupping_query->result_array() as $cupping_row) {
                        $user_id = $cupping_row['user_id'];
                        $name = str_replace("null", "", $cupping_row['name']);
                        $field1 = '';
                        $field2 = '';
                        $field3 = str_replace("null", "", $cupping_row['address']);
                        $cupping_image = str_replace("null", "", $cupping_row['image']);
                        if ($cupping_image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/' . $cupping_image;
                        } else {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        $cupping[] = array(
                            'listing_id' => $user_id,
                            'name' => $name,
                            'image' => $image,
                            'field1' => $field1,
                            'field2' => $field2,
                            'field3' => $field3
                        );
                    }
                    $cupping_array[] = array(
                        'title' => 'cupping',
                        'listing_type' => 16,
                        'array' => $cupping
                    );
                } else {
                    $cupping_array = array();
                }
            }

            if ($listing_type == '20') {
                // Physiotherapist
                $physiotherapist_query = $this->db->query("SELECT user_id,image,doctor_name,speciality,qualification,address FROM doctor_list WHERE doctor_name like '%$keyword%' limit $start, $limit");
                $physiotherapist_count = $physiotherapist_query->num_rows();
                if ($physiotherapist_count > 0) {
                    foreach ($physiotherapist_query->result_array() as $physiotherapist_row) {
                        $user_id = $physiotherapist_row['user_id'];
                        $name = str_replace("null", "", $physiotherapist_row['doctor_name']);
                        $field1 = str_replace("null", "", $physiotherapist_row['speciality']);
                        $field2 = str_replace("null", "", $physiotherapist_row['qualification']);
                        $field3 = str_replace("null", "", $physiotherapist_row['address']);
                        $physiotherapist_image = str_replace("null", "", $doctor_row['image']);
                        if ($physiotherapist_image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $physiotherapist_image;
                        } else {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        $physiotherapist[] = array(
                            'listing_id' => $user_id,
                            'name' => $name,
                            'image' => $image,
                            'field1' => $field1,
                            'field2' => $field2,
                            'field3' => $field3
                        );
                    }
                    $physiotherapist_array[] = array(
                        'title' => 'physiotherapist',
                        'listing_type' => 20,
                        'array' => $physiotherapist
                    );
                } else {
                    $physiotherapist_array = array();
                }
            }

            if ($listing_type == '6') {
                // Fitness Center
                $fitness_query = $this->db->query("SELECT user_id,image,center_name,address FROM fitness_center WHERE center_name like '%$keyword%' limit $start, $limit");
                $fitness_count = $fitness_query->num_rows();
                if ($fitness_count > 0) {
                    foreach ($fitness_query->result_array() as $fitness_row) {
                        $user_id = $fitness_row['user_id'];
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
                        $fitness[] = array(
                            'listing_id' => $user_id,
                            'name' => $name,
                            'image' => $image,
                            'field1' => $field1,
                            'field2' => $field2,
                            'field3' => $field3
                        );
                    }
                    $fitness_array[] = array(
                        'title' => 'fitness',
                        'listing_type' => 6,
                        'array' => $fitness
                    );
                } else {
                    $fitness_array = array();
                }
            }

            if ($listing_type == '8') {
                // Hospital
                $hospital_query = $this->db->query("SELECT user_id,image,name_of_hospital,address FROM hospitals WHERE name_of_hospital like '%$keyword%' limit $start, $limit");
                $hospital_count = $hospital_query->num_rows();
                if ($hospital_count > 0) {
                    foreach ($hospital_query->result_array() as $hospital_row) {
                        $user_id = $hospital_row['user_id'];
                        $name = str_replace("null", "", $hospital_row['name_of_hospital']);
                        $field1 = '';
                        $field2 = '';
                        $field3 = str_replace("null", "", $hospital_row['address']);
                        $hospital_image = str_replace("null", "", $hospital_row['image']);
                        if ($hospital_image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $hospital_image;
                        } else {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        $hospital[] = array(
                            'listing_id' => $user_id,
                            'name' => $name,
                            'image' => $image,
                            'field1' => $field1,
                            'field2' => $field2,
                            'field3' => $field3
                        );
                    }
                    $hospital_array[] = array(
                        'title' => 'hospital',
                        'listing_type' => 8,
                        'array' => $hospital
                    );
                } else {
                    $hospital_array = array();
                }
            }

            $resultpost = array_merge($people_array);
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function profile_details($user_id, $listing_id, $listing_type,$branch_id) {
        if ($listing_id > 0) {
            if ($listing_type == '0') {
                // People
                $people_query = $this->db->query("SELECT id,name,phone,email,gender,dob FROM `users` WHERE id='$listing_id' limit 1");
                $people_count = $people_query->num_rows();
                if ($people_count > 0) {
                    $user_row = $people_query->row_array();
                    $id = $user_row['id'];
                    $name = $user_row['name'];
                    $phone = $user_row['phone'];
                    $email = $user_row['email'];
                    $gender = $user_row['gender'];
                    $dob = $user_row['dob'];
                    $img_count = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->num_rows();

                    if ($img_count > 0) {
                        $media = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->row();
                        $img_file = $media->source;
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                    } else {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }

                    $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                    $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                    $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();

                    if ($is_follow > 0) {
                        $is_follow = 'Yes';
                    } else {
                        $is_follow = 'No';
                    }

                    $resultpost = array(
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                        'image' => $image,
                        'gender' => $gender,
                        'dob' => $dob,
                        'followers' => $followers,
                        'following' => $following,
                        'reviews_done' => '0',
                        'is_follow' => $is_follow
                    );
                }
            }
            if ($listing_type == '5') {
                // Doctor
                $query = $this->db->query("SELECT id,lat,lng,mba,certified,consultaion_video,consultaion_chat,consultation_voice_call,recommended,category,user_id,discount,doctor_name,about_us,speciality,address,telephone,medical_college,medical_affiliation,charitable_affiliation,awards_recognition,all_24_hrs_available,home_visit_available,qualification,consultation_fee,experience,website,location,days,timing,image,rating,review FROM doctor_list WHERE user_id='$listing_id' limit 1");

                $count = $query->num_rows();
                if ($count > 0) {
                    $row = $query->row_array();
                    $id = $row['id'];
                    $doctor_name = $row['doctor_name'];
                     $mba = $row['mba'];
                $certified = $row['certified'];
                $recommended = $row['recommended'];
                    $about_us = $row['about_us'];
                    $speciality = $row['category']; //changed by deepak
                    $address = $row['address'];
                    $telephone = $row['telephone'];
                    $medical_college = $row['medical_college'];
                    $medical_affiliation = $row['medical_affiliation'];
                    $charitable_affiliation = $row['charitable_affiliation'];
                    $awards_recognition = $row['awards_recognition'];
                    $hrs_available = $row['all_24_hrs_available'];
                    $home_visit_available = $row['home_visit_available'];
                    $qualification = $row['qualification'];
                    $consultation_fee = $row['consultation_fee'];
                    $experience = $row['experience'];
                    $website = $row['website'];
                    $location = $row['location'];
                    $days = $row['days'];
                    $timing = $row['timing'];
                    $rating = $row['rating'];
                    $reviews = $row['review'];
                    $doc_image = $row['image'];
                    $doctor_user_id = $row['user_id'];
                    $discount=$row['discount'];
                    $profile_views = '0';
                    $video         = $row['consultaion_video'];
                $chat             = $row['consultaion_chat'];
                $call                = $row['consultation_voice_call'];
                
                 $lat             = $row['lat'];
                $lng                = $row['lng'];

                    if ($doc_image != '') {
                        $doc_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doc_image;
                    } else {
                        $doc_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }

                    $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                    $following = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();

                    $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $doctor_user_id)->get()->num_rows();

                    if ($is_follow > 0) {
                        $is_follow = 'Yes';
                    } else {
                        $is_follow = 'No';
                    }



                    $result_hospital = '';
                     $new_consultation_charges=array();
      $hospital_query = $this->db->query("SELECT * FROM `doctor_clinic` WHERE doctor_id = '$listing_id' ORDER BY id desc ");
        
        

        $count = $hospital_query->num_rows();
        // echo $count; die(); 
        if ($count > 0) {
            foreach ($hospital_query->result_array() as $row) {
               
                $clinic_name = $row['clinic_name'];
                $address = $row['address'];
                $state = $row['state'];
                $city = $row['city'];
                $pincode = $row['pincode'];
                $map_location = $row['map_location'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $image = $row['image'];
                $consultation_charges = $row['consultation_charges'];
                $contact_no = $row['contact_no'];
                $appointment_time = $row['appointment_time'];
                $opening_hours = $row['open_hours'];
                
         $discountQuery = $this->db->query("SELECT * FROM `vendor_discount` WHERE vendor_id = '$id'");  
         $disCount = $discountQuery->num_rows();
      
            if ($disCount > 0) {
                foreach ($discountQuery->result_array() as $rowDis) {
                    // print_r($rowDis); die();
                    //$discount_amount = 0;
                    $discount_type = $rowDis['discount_type'];
                    $discount_limit = $rowDis['discount_limit'];
                    $discount_cat = $rowDis['discount_category'];
                    
                }
            } else {
               // $discount_amount = 0;
                $discount_type = 0;
                $discount_limit = 0;
                $discount_cat = "null";
            }
                
                
                
                
                
                $open_days = '';
                $day_array_list = '';
                $day_list = '';
                $day_time_list = '';
                $time_list1 = '';
                $time_list2 = '';
                $time = '';
                $system_start_time = '';
                $system_end_time = '';
                $time_check = '';
                $current_time = '';
                $open_close = array();
                $time = array();
                date_default_timezone_set('Asia/Kolkata');
                $data = array();
                
                
                
                
                
                $doctor_id = $row['doctor_id'];
                $clinic_id = $row['id'];
                // echo "doctor_id".$doctor_id."<br>";
                // echo "clinic_id".$clinic_id."<br>";
                $final_Day = array();
                $time_slots = array();
                
                $weekday ='Sunday';
                
                // echo $weekday;
                
               
                // echo $tomorrow;
                // die();
                for($i=0;$i<7;$i++){
                    
                     $weekday = date('l', strtotime($weekday.'+1 day'));
                
                $queryTiming = $this->db->query("SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `day` = '$weekday'" );
               
                $countTiming = $queryTiming->num_rows();
                
                // die();
             
                $time_array = array();
                $time_array1 = array();
                $time_array2 = array();
                $time_array3 = array();
                $time_slott = array();
                
                foreach($queryTiming->result_array() as $row1){
                // if($countTiming){
                   
                    $timeSlotDay = $row1['day'];
                    $timeSlot = $row1['time_slot'];
                    $from_time = $row1['from_time'];
                    $to_time = $row1['to_time'];
                    // echo $timeSlot;
                   
                     if ($timeSlot == 'Morning') {
        				$time_array[] = array(
        					'from_time' => $from_time,
        					'to_time' => $to_time
        				);
        				} else  if ($timeSlot == 'Afternoon') {
        				$time_array1[] = array(
        					'from_time' => $from_time,
        					'to_time' => $to_time
        				);
        				} else if ($timeSlot == 'Evening') {
        				$time_array3[] = array(
        					'from_time' => $from_time,
        					'to_time' => $to_time
        				);
        				} else if ($timeSlot == 'Night') {
        				$time_array2[] = array(
        					'from_time' => $from_time,
        					'to_time' => $to_time
        				);
        			}
        			
        			
                }
                
                $time_slott[] = array(
                    'time_slot'=> 'Morning',
        			'time' => $time_array
        		);
        		$time_slott[] = array(
        		    'time_slot'=> 'Afternoon',
        			'time' => $time_array1
        		);
        		
        		$time_slott[] = array(
        		    'time_slot'=> 'Evening',
        			'time' => $time_array3
        		);
        		$time_slott[] = array(
        		    'time_slot'=> 'Night',
        			'time' => $time_array2
        		);
        		
        		$time_slots[] = array(
        		    'day'=>$weekday,
                   'slots'=> $time_slott
                ); 
                
                }
              
                if ($row['image'] != '') {
                    $profile_pic = $row['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://medicalwale.s3.amazonaws.com/images/doctor_images/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }
               
                
                $new_consultation_charges[]=$consultation_charges;
                    $resultpostDetails['id'] = $row['id'];
                    $resultpostDetails['clinic_name'] = $row['clinic_name'];
                    $resultpostDetails['address'] = $row['address'];
                    $resultpostDetails['state'] = $row['state'];
                    $resultpostDetails['city'] = $row['city'];
                    $resultpostDetails['pincode'] = $row['pincode'];
                    $resultpostDetails['map_location'] = $row['map_location'];
                    $resultpostDetails['lat'] = $row['lat'];
                    $resultpostDetails['lng'] = $row['lng'];
                    $resultpostDetails['image'] = $profile_pic;
                    $resultpostDetails['consultation_charges'] = $row['consultation_charges'];
                    $resultpostDetails['contact_no'] = $row['contact_no'];
                    $resultpostDetails['time_slot'] = $appointment_time;
                    $resultpostDetails['timings'] = $time_slots;
                    $resultpostDetails['discount_type'] =  $discount_type;
                    $resultpostDetails['discount_limit'] = $discount_limit;
                    $resultpostDetails['discount_category'] = $discount_cat;
               
                    
               
                 $result_hospital[] = $resultpostDetails;
            }
            
            
        } else {
            $result_hospital = array();
        }
                    $service = '';
                    $result_services = '';
                    $doctor_services_query = $this->db->query("SELECT service FROM `doctor_services`");
                    foreach ($doctor_services_query->result_array() as $doctor_services) {
                        $service = $doctor_services['service'];
                        $result_services[] = array(
                            'service' => $service
                        );
                    }
                    $specialization = '';
                    $result_specialization = '';
                    $doctor_specialization_query = $this->db->query("SELECT specialization FROM `doctor_specialization`");
                    foreach ($doctor_specialization_query->result_array() as $doctor_specialization) {
                        $specialization = $doctor_specialization['specialization'];
                        $result_specialization[] = array(
                            'specialization' => $specialization
                        );
                    }
date_default_timezone_set('Asia/Kolkata');
                    $current_date = date('Y-m-d H:i:s');
                  
                   $sql   =  sprintf("SELECT doctor_id FROM doctor_online  WHERE doctor_id='$doctor_user_id' and  doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date'");
                   
                     $query_doct_online = $this->db->query($sql);
                     $count1_doc_online = $query_doct_online->num_rows();
                     if($count1_doc_online >0){
                       $online_status= 1;  
                         
                     }else{
                         
                       $online_status = 0; 
                     }
                      if(sizeof($new_consultation_charges) > 1)
                {
                $max_consultation_charges=max($new_consultation_charges);
                $min_consultation_charges=min($new_consultation_charges);
                }
                else
                {
                  $max_consultation_charges= 0;
                  if(sizeof($new_consultation_charges) >= 1)
                  {
                  $min_consultation_charges=max($new_consultation_charges);
                  }
                  else
                  {
                     $min_consultation_charges=0; 
                  }
                }
                  if(empty($call))
                  {
                    $call="0";  
                  }
                   if(empty($chat))
                  {
                     $chat="0"; 
                  }
                   if(empty($video))
                  {
                      $video="0";
                  }
                    $resultpost[] = array(
                        'doctor_id' => $id,
                        'doctor_user_id' => $doctor_user_id,
                        'name' => $doctor_name,
                        'about_us' => $about_us,
                        'speciality' => $speciality,
                        'address' => $address,
                        'telephone' => $telephone,
                        "exotel_no" => '02233721563',
                        'medical_college' => $medical_college,
                        'medical_affiliation' => $medical_affiliation,
                        'charitable_affiliation' => $charitable_affiliation,
                        'awards_recognition' => $awards_recognition,
                        'hrs_available' => $hrs_available,
                        'home_visit_available' => $home_visit_available,
                        'qualification' => $qualification,
                        'consultation_fee' => $consultation_fee,
                        'experience' => $experience,
                        'website' => $website,
                        'location' => $location,
                        'days' => $days,
                        'timing' => $timing,
                        'rating' => $rating,
                        'review' => $reviews,
                        'image' => $doc_image,
                        'followers' => $followers,
                        'following' => $following,
                        'profile_view' => $profile_views,
                        'is_follow' => $is_follow,
                        'doctor_practices' => $result_hospital,
                        'doctor_services' => $result_services,
                        'doctor_specialization' => $result_specialization,
                        'discount'=>$discount,
                        'profile_video'=>"",
                        'mba' => $mba,
                    'certified' => $certified,
                    'recommended' => $recommended,
                    'online_status'=> $online_status,
                      'visit_charge'=>$min_consultation_charges.",".$max_consultation_charges,
                    'call_charge'=>$call,
                    'chat_charge'=>$chat,
                    'video_charge'=>$video,
                    'longitude' => $lng,
                    'latitude' => $lat
                    );
                } else {
                    $resultpost = array();
                }
            }
            if ($listing_type == '13') {
                // Pharmacy
                $query = $this->db->query("SELECT `id`, `user_id`, `email`, `medical_name`,`otc_discount`, `ethical_discount`, `generic_discount`,  `surgical_discount`,`perscribed_discount`, `discount`,`about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`, `is_min_order_delivery`,`min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,IFNULL(online_offline,'') AS online_offline FROM medical_stores  WHERE  is_active='1' AND (user_id='$listing_id' or pharmacy_branch_user_id ='$listing_id')");

                $count = $query->num_rows();
                if ($count > 0) {
                    $row = $query->row_array();
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $discount =$row['discount'];
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
                    $website = $row['website'];
                    $reach_area = $row['reach_area'];
                    $is_24hrs_available = $row['is_24hrs_available'];
                    if ($is_24hrs_available == 'Yes') {
                        $store_open = date("h:i A", strtotime("12:00 AM"));
                        $store_close = date("h:i A", strtotime("11:59 PM"));
                    } else {
                        $store_open = $this->check_time_format($row['store_open']);
                        $store_close = $this->check_time_format($row['store_close']);
                    }
                    $day_night_delivery = $row['day_night_delivery'];
                    $free_start_time = $this->check_time_format($row['free_start_time']);
                    $free_end_time = $this->check_time_format($row['free_end_time']);
                    ;
                    $days_closed = $row['days_closed'];
                    $min_order = $row['min_order'];
                    $is_min_order_delivery = $row['is_min_order_delivery'];
                    $min_order_delivery_charge = $row['min_order_delivery_charge'];
                    $night_delivery_charge = $row['night_delivery_charge'];
                    $payment_type = $row['payment_type'];

                    $online_offline = $row['online_offline'];

                    $profile_pic = $row['profile_pic'];
                    if ($row['profile_pic'] != '') {
                        $profile_pic = $row['profile_pic'];
                        $profile_pic = str_replace(' ', '%20', $profile_pic);
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                    } else {
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
                    }

                    $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $medical_id)->get()->num_rows();
                    $following = $this->db->select('id')->from('follow_user')->where('user_id', $medical_id)->get()->num_rows();
                    $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $medical_id)->get()->num_rows();

                    if ($is_follow > 0) {
                        $is_follow = 'Yes';
                    } else {
                        $is_follow = 'No';
                    }

                    $profile_view = $this->db->select('id')->from('pharmacy_view')->where('listing_id', $medical_id)->get()->num_rows();


                    $activity_id = '0';

                    $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM medical_stores_review WHERE medical_stores_id='$medical_id'");
                    $row_pharma = $query_pharmacy->row_array();
                    $rating = $row_pharma['avg_rating'];
                    if ($rating === NULL) {
                        $rating = '0';
                    }

                    $review = $this->db->select('id')->from('medical_stores_review')->where('medical_stores_id', $medical_id)->get()->num_rows();

                    $chat_id = $row['user_id'];
                    $chat_display = $row['medical_name'];
                    $is_chat = 'Yes';

                    //All Days Open
                      $otc_discount = $row['otc_discount'];
                $ethical_discount = $row['ethical_discount'];
                $generic_discount = $row['generic_discount'];
                $surgical_discount = $row['surgical_discount'];
                $perscribed_discount = $row['perscribed_discount'];
                
              //  echo $otc_discount.$ethical_discount.$generic_discount;
                
                 $offer_dis = array(
                    $perscribed_discount,$generic_discount,$surgical_discount,$ethical_discount,$otc_discount
                    );
                    
                   
                    
                    $offer_key = array('Prescribed Medicines Discount','Generic Medicines','Surgical Product Discount','Wellness/Lifestyle','General Product Discount');
                    
                 //   echo 'array ='.$offer_dis;
                  //  echo 'key ='.$offer_key;
                    for($i = 0; count($offer_dis) > $i; $i++)
                    {
                        if($offer_dis[$i] == '' || $offer_dis[$i] == NULL)
                        {
                            $offer_dis[$i] = '0';
                        }
                        else
                        {
                            
                        }
                        
                        $offer_discount[] = array(
                             'discount_key' => $offer_key[$i],
                             'discount_value' => $offer_dis[$i]
                            );
                        
                    }
                    

                    $Monday = $this->check_day_status('Monday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                    $Tuesday = $this->check_day_status('Tuesday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                    $Wednesday = $this->check_day_status('Wednesday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                    $Thursday = $this->check_day_status('Thursday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                    $Friday = $this->check_day_status('Friday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                    $Saturday = $this->check_day_status('Saturday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                    $Sunday = $this->check_day_status('Sunday', $days_closed, $is_24hrs_available, $store_open, $store_close);


                    $opening_hours = "Monday>$Monday,Tuesday>$Tuesday,Wednesday>$Wednesday,Thursday>$Thursday,Friday>$Friday,Saturday>$Saturday,Sunday>$Sunday";

                    $open_days = '';
                    $day_array_list = '';
                    $day_list = '';
                    $day_time_list = '';
                    $time_list1 = '';
                    $time_list2 = '';
                    $time = '';
                    $system_start_time = '';
                    $system_end_time = '';
                    $time_check = '';
                    $current_time = '';
                    $open_close = array();
                    $time = array();
                    date_default_timezone_set('Asia/Kolkata');
                    $data = array();
                    $final_Day = array();
                    $day_array_list = explode(',', $opening_hours);
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
                                        $time = str_replace('close-close', 'close', $time_check);
                                        $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                        $system_end_time = date("H.i", strtotime($time_list2[$l]));
                                        ;
                                        $current_time = date('H.i');
                                        if ($current_time > $system_start_time && $current_time < $system_end_time) {
                                            $open_close = 'open';
                                        } else {
                                            $open_close = 'closed';
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

                    $current_delivery_charges = $this->check_current_delivery_charges($is_24hrs_available, $day_night_delivery, $free_start_time, $free_end_time, $night_delivery_charge, $days_closed, $is_min_order_delivery, $min_order);

  $offer_dis_new = array(
                    $generic_discount,$otc_discount,$ethical_discount, $perscribed_discount,$surgical_discount
                    );
                   $product_category_list = array();            
                $query_category = $this->db->query("SELECT id,category,image FROM `category` where parent_id=0  order by id asc");
                $i=0;
                
            //   $i=0; 
                
                 foreach ($query_category->result_array() as $row) {
                            $product_id = $row['id'];
                            $product_category = $row['category'];
                            $product_image = $row['image'];
                            $product_image = str_replace(" ", "", $product_image);
                            $product_image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image;
                
                           $sub_category=array();
                           $query_category1 = $this->db->query("SELECT id,category,image FROM `category` where parent_id='$product_id' order by id asc");
                                foreach ($query_category1->result_array() as $row1) {
                                    $product_id1 = $row1['id'];
                                    $product_category1 = $row1['category'];
                                    $product_image1 = $row1['image'];
                                    $product_image1 = str_replace(" ", "", $product_image1);
                                    $product_image1 = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image1;
                                    
                                   $sub_category[] = array(
                                        "id" => $product_id1,
                                        "category" => $product_category1,
                                        "image" => $product_image1
                                       
                                    ); 
                                }
                
                
                            $product_category_list[] = array(
                                "id" => $product_id,
                                "category" => $product_category,
                                "image" => $product_image,
                                "discount"=>$offer_dis_new[$i],
                                "sub_category"=>$sub_category
                            );
                            $i++;
                        }
              
   
                    $resultpost[] = array(
                        'id' => $medical_id,
                        'name' => $medical_name,
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'store_manager' => $store_manager,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'telephone' => $contact_no,
                        'whatsapp_no' => $whatsapp_no,
                        'email' => $email,
                        'store_since' => $store_since,
                        'website' => $website,
                        'is_24hrs_available' => $is_24hrs_available,
                        'store_open' => $store_open,
                        'store_close' => $store_close,
                        'day_night_delivery' => $day_night_delivery,
                        'free_start_time' => $free_start_time,
                        'free_end_time' => $free_end_time,
                        'days_closed' => $days_closed,
                        'min_order' => $min_order,
                        'is_min_order_delivery' => $is_min_order_delivery,
                        'min_order_delivery_charge' => $min_order_delivery_charge,
                        'night_delivery_charge' => $night_delivery_charge,
                        'payment_type' => $payment_type,
                        'opening_day' => $final_Day,
                        'exotel_no' => '02233721563',
                        'current_delivery_charges' => $current_delivery_charges,
                        'online_offline' => $online_offline,
                        'image' => $profile_pic,
                        'rating' => (string) $rating,
                        'followers' => $followers,
                        'following' => $following,
                        'profile_view' => $profile_view,
                        'activity_id' => $activity_id,
                        'is_follow' => $is_follow,
                        'chat_id' => $chat_id,
                        'chat_display' => $chat_display,
                        'is_chat' => $is_chat,
                        'review' => $review,
                        'category_list' => $product_category_list,
                        'offer_discount' => $offer_discount
                    );
                } else {
                    $resultpost = array();
                }
            }
            if ($listing_type == '1') {
                // Ayurveda


                $about_query = $this->db->query("SELECT ayurveda_name,contact_no,profile_pic,about_us FROM `ayurveda` WHERE user_id='$listing_id'");
                $get_about = $about_query->row_array();
                $count = $about_query->num_rows();
                if ($count > 0) {
                    $ayurveda_name = $get_about['ayurveda_name'];
                    $phone = $get_about['contact_no'];
                    $about_us = $get_about['about_us'];
                    $profile_pic = $get_about['profile_pic'];

                    if ($profile_pic != '') {
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/ayurveda_images/' . $profile_pic;
                    } else {
                        $profile_pic = '';
                    }

                    $gallery_array = '';
                    $gallery_query = $this->db->query("SELECT title,media,type FROM `ayurveda_gallery` WHERE user_id='$listing_id'");
                    foreach ($gallery_query->result_array() as $row) {
                        $title = $row['title'];
                        $media = 'https://d2c8oti4is0ms3.cloudfront.net/images/ayurveda_images/' . $row['media'];
                        $type = $row['type'];
                        $gallery_array[] = array(
                            'title' => $title,
                            'media' => $media,
                            'type' => $type
                        );
                    }




                    $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM ayurveda_review WHERE ayurveda_id='$listing_id' ");
                    $row_rating = $query_rating->row_array();
                    $rating = $row_rating['avg_rating'];

                    $profile_views = $this->db->select('id')->from('ayurveda_view')->where('ayurveda_id', $listing_id)->get()->num_rows();

                    $reviews = $this->db->select('id')->from('ayurveda_review')->where('ayurveda_id', $listing_id)->get()->num_rows();


                    $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                    $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                    $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();

                    if ($is_follow > 0) {
                        $is_follow = 'Yes';
                    } else {
                        $is_follow = 'No';
                    }


                    $resultpost[] = array(
                        'ayurveda_name' => $ayurveda_name,
                        'phone' => $phone,
                        'about_us' => $about_us,
                        'exotel_no' => '02233721563',
                        'profile_pic' => $profile_pic,
                        'rating' => $rating,
                        'followers' => $followers,
                        'following' => $following,
                        'profile_views' => $profile_views,
                        'reviews' => $reviews,
                        'is_follow' => $is_follow,
                        "media" => $gallery_array
                    );
                } else {
                    $resultpost = array();
                }
            }
            if ($listing_type == '9') {
                // Homeopathic
            }
            if ($listing_type == '10') {
                // Labs
               $sql = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_name`,`address1`,`address2`, `pincode`, `city`, `user_discount`,`state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`, IFNULL(delivery_charges,'') AS delivery_charges FROM lab_center where user_id='$listing_id'");
              
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
                        $user_discount = $row['user_discount'];
                        $image = $row['profile_pic'];
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$image;

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


                $branch_list = array();
                $branch_query = $this->db->query("SELECT * FROM `lab_center` WHERE user_id='$labcenter_user_id' AND branch_user_id != 0  order by id asc");
                $branch_count = $branch_query->num_rows();
                if ($branch_count > 0) {
                    foreach ($branch_query->result_array() as $branch_row) {
                        $branch_id                              = $branch_row['id'];
                        $listing_id                              = $branch_row['user_id'];
                        $branch_listing_id                      = $branch_row['branch_user_id'];
                        $branch_lab_branch_name                 = $branch_row['lab_name'];
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
                        $branch_opening_hours           = $branch_row['opening_hours'];
                        $branch_profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_profile_pic;
                        
                        
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
                    
                        
                        
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        $branch_list[] = array(
                            'branch_id' => $branch_id,
                            'listing_id' => $listing_id,
                            'branch_listing_id' => $branch_listing_id,
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
                            'branch_profile_pic' => $branch_profile_pic
                        );
                    }
                } else {
                    $branch_list = array();
                }
                
 
                $rating_query = $this->db->query("SELECT sum(labcenter_review.rating) as rating, count(labcenter_review.rating) as total_view FROM `labcenter_review` WHERE labcenter_review.labcenter_id='$labcenter_user_id'");
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
                $rating=  $rating_count; 

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
                            "branch_list"=>$branch_list,
                            "rating"=>(string)$rating
                        );
                    }
                } else {
                    $resultpost = array();
                }
            }
            if ($listing_type == '12') {
                // Nursing Attendant


                $query = $this->db->query(" SELECT `id`, `user_id`, `name`, `about_us`, `services`,`establishment_year`, `certificates`, `address`, `pincode`, `contact`, `city`, `state`, `email`, `lat`, `lng`, `image`, IFNULL(rating, '') AS rating,IFNULL(review, '') AS review, `date`, `is_active` FROM nursing_attendant WHERE user_id='$listing_id'");

                $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id = $row['id'];
                        $name = $row['name'];
                        $about_us = $row['about_us'];
                        $establishment_year = $row['establishment_year'];
                        $services = $row['services'];
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
                        $reviews = $row['review'];
                        $nursingattendant_user_id = $row['user_id'];
                        $profile_views = '0';

                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/nursing_attendant_images/' . $image;
                        } else {
                            $image = '';
                        }

                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $nursingattendant_user_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $nursingattendant_user_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $nursingattendant_user_id)->get()->num_rows();

                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }


                        $certificates_list = array();
                        $certificates_query = $this->db->query("SELECT `name`, `image` FROM `nursing_attendant_certificates` WHERE FIND_IN_SET(name,'" . $certificates . "')");
                        foreach ($certificates_query->result_array() as $get_clist) {
                            $certificates_name = $get_clist['name'];
                            $certificates_image = $get_clist['image'];

                            if ($certificates_image != '') {
                                $certificates_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/nursing_attendant_images/' . $certificates_image;
                            } else {
                                $certificates_image = '';
                            }


                            $certificates_list[] = array(
                                "certificates_name" => $certificates_name,
                                "certificates_image" => $certificates_image
                            );
                        }



                $nursingattendant_service_list = array();
            $nursingattendant_services_query = $this->db->query("SELECT IFNULL(id,'') AS id,IFNULL(rate,'') AS rate,IFNULL(description,'') AS description,IFNULL(service_name,'') AS service_name FROM `nursing_attendant_services` WHERE `user_id`='$nursingattendant_user_id'");
                foreach ($nursingattendant_services_query->result_array() as $get_serlist) {
                    $service_id = $get_serlist['id'];
                    $package_name = $get_serlist['service_name'];
                    $service_desc = $get_serlist['description'];
                    $service_rate = $get_serlist['rate'];

                    $nursingattendant_service_list[] = array(
                        "package_id" => $service_id,
                        "package_name" => $package_name,
                        "package_details" => $service_desc,
                        "price" => $service_rate
                    );
                }
                        //$specialization[] =  explode(",",$services) ;


                        $gallery_query = $this->db->query("SELECT `title` , CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/nursing_attendant_images/',source) AS media FROM `nursing_attendant_media` WHERE nursing_attendant_id='$id'");
                        foreach ($gallery_query->result_array() as $get_list2) {
                            $gallery_title = $get_list2['title'];
                            $gallery_image = $get_list2['media'];
                            $gallery_list[] = array(
                                "title" => $gallery_title,
                                "image" => $gallery_image
                            );
                        }
                        
                        if(empty($gallery_list)){
                            $gallery_list = array();
                        }

$specialization = $row['services']; 
$area_expertise       = array();
        $query_sp             = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'" . $specialization . "')");
        $total_category       = $query_sp->num_rows();
        if ($total_category > 0) {
            foreach ($query_sp->result_array() as $get_sp) {
                $id               = $get_sp['id'];
                $area_expertised  = $get_sp['area_expertise'];
                $area_expertise[] =  $area_expertised;
                
            }
        } else {
           
            $area_expertise[] = explode(",",$specialization) ;
        }


                        $resultpost[] = array(
                            'nursing_attendant_id' => $id,
                            'nursing_user_id' => $nursingattendant_user_id,
                            'name' => $name,
                            'about_us' => $about_us,
                            'establishment_year' => $establishment_year,
                            'certificates_list' => $certificates_list,
                            'specialization' => $area_expertise,
                            'nursingattendant_service_list' => $nursingattendant_service_list,
                            'gallery_list' => $gallery_list,
                            'address' => $address,
                            'telephone' => $mobile,
                            'exotel_no' => '02233721563',
                            'lat' => $lat,
                            'lng' => $lng,
                            'pincode' => $pincode,
                            'city' => $city,
                            'state' => $state,
                            'rating' => $rating,
                            'review' => $reviews,
                            'image' => $image,
                            'followers' => $followers,
                            'following' => $following,
                            'profile_view' => $profile_views,
                            'is_follow' => $is_follow
                        );
                    }
                } else {
                    $resultpost = array();
                }
            }
            if ($listing_type == '16') {
                // Cupping

                $query = $this->db->query("SELECT `id`,`user_id`,`image`, `name`, `description`,`address`, `pincode`, `contact`, `city`, `state`, `whatsapp`, `email`, `opening_hours`, `image`, `lat`, `lng`, `date`, `is_active` FROM cuppingtherapy WHERE user_id='$listing_id' order by id ASC");

                $count = $query->num_rows();
                if ($count > 0) {

                    foreach ($query->result_array() as $row) {
                        $id = $row['id'];
                        $name = $row['name'];
                        $address = $row['address'];
                        $pincode = $row['pincode'];
                        $contact = $row['contact'];
                        $city = $row['city'];
                        $state = $row['state'];
                        $whatsapp = $row['whatsapp'];
                        $email = $row['email'];
                        $opening_hours = $row['opening_hours'];
                        $cuppingtherapy_user_id = $row['user_id'];
                        $lat = $row['lat'];
                        $lng = $row['lng'];
                        $rating = '4.0';
                        $profile_views = '0';
                        $reviews = '0';
                        $description = $row['description'];
                        $image = $row['image'];
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/' . $image;

                        $gallery_query = $this->db->query("SELECT * FROM `cuppingtherapy_media` WHERE `id`='$id'");
                        $gallery_array = array();
                        $gallery_count = $gallery_query->num_rows();
                        if ($gallery_count > 0) {
                            foreach ($gallery_query->result_array() as $rows) {
                                $media_name = $rows['title'];
                                $source = $rows['source'];
                                $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/' . $source;

                                $media_name = str_replace(".jpg", "", $media_name);
                                $gallery_name = $media_name;

                                $cnt = count($gallery);

                                $gallery_array[] = array(
                                    "title" => $gallery_name,
                                    "image" => $gallery
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




                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $cuppingtherapy_user_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $cuppingtherapy_user_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $cuppingtherapy_user_id)->where('parent_id', $cuppingtherapy_user_id)->get()->num_rows();

                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }



                        $gallery_query = $this->db->query("SELECT GROUP_CONCAT(`name`) AS title, GROUP_CONCAT(CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/',name)) AS media FROM `cuppingtherapy_media`");
                        if ($gallery_query) {
                            $row2 = $gallery_query->row();
                            $gallery_name = $row2->title;
                            $gallery = $row2->media;
                        } else {
                            $gallery = '';
                            $gallery_name = '';
                        }


                        $resultpost[] = array(
                            "id" => $id,
                            "name" => $name,
                            "address" => $address,
                            "pincode" => $pincode,
                            "telephone" => $contact,
                            "exotel_no" => '02233721563',
                            "city" => $city,
                            "state" => $state,
                            "whatsapp" => $whatsapp,
                            "email" => $email,
                            "gallery" => $gallery_array,
                            "description" => $description,
                            "rating" => $rating,
                            "followers" => $followers,
                            "following" => $following,
                            "profile_view" => $profile_views,
                            "review" => $reviews,
                            "is_follow" => $is_follow,
                            "lat" => $lat,
                            "lng" => $lng,
                            "opening_day" => $final_Day,
                            "image" => $image
                        );
                    }
                } else {
                    $resultpost = array();
                }
            }

            if ($listing_type == '20') {
                // Physiotherapist
            }
            if ($listing_type == '6') {

                // Fitness Center
$query = $this->db->query("SELECT fc.`user_id`, fc.`center_name`, fc.`contact`, fc.`email`, fc.`image`,en.contact_no as enquiry_number FROM `fitness_center` as fc LEFT JOIN users as u ON(u.id=fc.user_id) LEFT JOIN enquiry_number en ON(en.vendor_id=u.vendor_id) WHERE fc.user_id='$listing_id'");
        //$query = $this->db->query("SELECT `user_id`, `center_name`, `contact`, `email`, `image` FROM `fitness_center` LEFT JOIN enquiry_number as en ON(en.co) WHERE user_id='$listing_id'");
        $count_main = $query->num_rows();
        if ($count_main > 0) {
            $list = $query->row_array();

            $center_name = $list['center_name'];
            $main_contact = $list['contact'];
            $enquiry_number = $list['enquiry_number'];
            $main_email = $list['email'];
            $main_image = $list['image'];
            $listing_type = '6';
            $total_rating = '4.5';
            $total_review = '0';
            $total_profile_views = '0';

            if ($main_image != '') {
                $main_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $main_image;
            } else {
                $main_image = '';
            }
            $query_fitness_tr = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM fitness_center_review WHERE listing_id='$listing_id'");
            $row_fitness_tr = $query_fitness_tr->row_array();
            $total_rating = $row_fitness_tr['total_rating'];
            if ($total_rating === NULL || $total_rating === '') {
                $total_rating = '0';
            }

            $total_review = $this->db->select('id')->from('fitness_center_review')->where('listing_id', $listing_id)->get()->num_rows();
            $total_profile_views = $this->db->select('id')->from('fitness_views')->where('listing_id', $listing_id)->get()->num_rows();

            $main_followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
            $main_following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
            $main_is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();

            if ($main_is_follow > 0) {
                $main_is_follow = 'Yes';
            } else {
                $main_is_follow = 'No';
            }

            if($branch_id=='0')
            {
             $query_branch = $this->db->query("SELECT fcb.id, fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category AS category_id,business_category.category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time, fcb.to_trail_time FROM `fitness_center_branch` AS fcb LEFT JOIN business_category ON fcb.branch_business_category= business_category.id  WHERE fcb.user_id='$listing_id'");
            }
            else
            {
            $query_branch = $this->db->query("SELECT fcb.id, fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category AS category_id,business_category.category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time, fcb.to_trail_time FROM `fitness_center_branch` AS fcb LEFT JOIN business_category ON fcb.branch_business_category= business_category.id  WHERE fcb.user_id='$listing_id' and fcb.id='$branch_id'");
            }
            $count = $query_branch->num_rows();
            $other_branch = array();
            if ($count > 0) {
                foreach ($query_branch->result_array() as $row) {
                    $branch_id = $row['id'];
                   
                    $category_id = $row['category_id'];
                    $cat = explode(',',$category_id);
                    $category = array();
                    for($i=0;$i<count($cat);$i++)
                    {
                        if(!empty($cat[$i]))
                        {
                        $sql = $this->db->select('category')->from('business_category')->where('id', $cat[$i])->get()->row()->category;
                        $category[]=$sql;
                        }
                    }
                   
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $branch_name = $row['branch_name'];
                    $branch_image = $row['branch_image'];
                    $branch_phone = $row['branch_phone'];
                    $branch_email = $row['branch_email'];
                    $about_branch = $row['about_branch'];
                    $branch_offer = $row['branch_offer'];
                    $branch_facilities = $row['branch_facilities'];
                    $branch_address = $row['branch_address'];
                    $opening_hours = $row['opening_hours'];
                    $pincode = $row['pincode'];
                    $state = $row['state'];
                    $city = $row['city'];
                    $is_free_trail = $row['is_free_trail'];
                    $from_trail_time = $row['from_trail_time'];
                    $to_trail_time = $row['to_trail_time'];
                    $listing_id = $row['user_id'];
                    $listing_type = '6';
                    $rating = '4.5';
                    $reviews = '0';
                    $profile_views = '0';
                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
                    }
                    $gallery_query = $this->db->query("SELECT `id`,`media_name`, `type` FROM `fitness_gallery` WHERE `branch_id`='$branch_id' AND `fitness_center_id`= '$listing_id' ");
                    $gallery_array = array();
                    $gallery_count = $gallery_query->num_rows();
                    if ($gallery_count > 0) {
                        foreach ($gallery_query->result_array() as $rows) {
                            $media_name = $rows['media_name'];
                            $type = $rows['type'];
                            $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $type . '/' . $media_name;

                            $gallery_array[] = array(
                                "image" => $gallery,
                                "type" => $type
                            );
                        }
                    }
                    $open_days = '';
                    $day_array_list = '';
                    $day_list = '';
                    $day_time_list = '';
                    $time_list1 = '';
                    $time_list2 = '';
                    $time = '';
                    $system_start_time = '';
                    $system_end_time = '';
                    $time_check = '';
                    $current_time = '';
                    $open_close = array();
                    $time = array();
                    date_default_timezone_set('Asia/Kolkata');
                    $data = array();
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
                                        $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                                        $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                                        $current_time = date('h:i A');

                                        $date1 = DateTime::createFromFormat('H:i a', $current_time);
                                        $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                                        $date3 = DateTime::createFromFormat('H:i a', $system_end_time);

                                        if ($date2 < $date3 && $date1 <= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        } elseif ($date2 > $date3 && $date1 >= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        }

                                        if ($date1 > $date2 && $date1 < $date3) {
                                            $open_close[] = 'open';
                                        } else {
                                            $open_close[] = 'closed';
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



                    $what_we_offer_array = '';
                    $what_we_offer = explode(',', $branch_offer);
                    foreach ($what_we_offer as $what_we_offer) {
                        $what_we_offer_array[] = $what_we_offer;
                    }

                    $facilities_array = array();
                    $facilities = explode(',', $branch_facilities);
                    foreach ($facilities as $facilities) {
                        $facilities_array[] = $facilities;
                    }

                    $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                    $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                    $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();

                    if ($is_follow > 0) {
                        $is_follow = 'Yes';
                    } else {
                        $is_follow = 'No';
                    }



                    $package_list = array();
                    $package_query = $this->db->query("SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' AND categ_id='$category_id' order by id asc");
                    $package_count = $package_query->num_rows();
                    if ($package_count > 0) {
                        foreach ($package_query->result_array() as $package_row) {
                            $package_id = $package_row['id'];
                            $package_name = $package_row['package_name'];
                            $package_details = $package_row['package_details'];
                            $price = $package_row['price'];
                            $image = $package_row['image'];
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                            $package_list[] = array(
                                'package_id' => $package_id,
                                'package_name' => $package_name,
                                'package_details' => $package_details,
                                'price' => $price,
                                'image' => $image
                            );
                        }
                    } else {
                        $package_list = array();
                    }

                    $booking_slot_array = array();
                    if ($is_free_trail == '1') {
                        $from_trail_times = $this->check_time_format($from_trail_time);
                        $to_trail_times = $this->check_time_format($to_trail_time);
                        $time_difference = 60;
                        for ($i = strtotime($from_trail_times); $i <= strtotime($to_trail_times); $i = $i + $time_difference * 60) {
                            $booking_slot_array[] = array(
                                'time' => date("h:i A", $i)
                            );
                        }
                    }

                    $is_order_query = $this->db->query("SELECT id,trail_booking_date,trail_booking_time FROM `booking_master` WHERE listing_id='$listing_id' AND branch_id='$branch_id' AND category_id='$category_id' AND user_id='$user_id' AND package_id='100'");
                    $is_order_count = $is_order_query->num_rows();

                    if ($is_order_count > 0) {
                        $is_order_details=$is_order_query->row_array();
                        $trail_booking_date = $is_order_details['trail_booking_date'];
                            $trail_booking_time = $is_order_details['trail_booking_time'];
                           
                             $joining_date_ = $trail_booking_date . ' ' . $trail_booking_time;

                             $joining_date = date('j M Y | h:i A', strtotime($joining_date_));
                        $is_trial_order = 1;
                    } else {
                         $joining_date ="";
                        $is_trial_order = 0;
                    }
                    $profile_views = $this->db->select('id')->from('fitness_views')->where('listing_id', $listing_id)->get()->num_rows();
                    $reviews = $this->db->select('id')->from('fitness_center_review')->where('branch_id', $branch_id)->get()->num_rows();

            $sub_array = array();
            $personal_trainer = $this->db->select('*')->from('personal_trainers')->where('user_id', $listing_id)->get();
            $count = $personal_trainer->num_rows();
            if ($count > 0) 
            {
                foreach ($personal_trainer->result_array() as $row1) 
                {
                    $id1 = $row1['id'];
                    $category_id1 = $row1['category_id'];
                    $cat1 = explode(',',$category_id1);
                    $category_name1 = array();
                    for($i=0;$i<count($cat1);$i++)
                    {
                        if(!empty($cat1[$i]))
                        {
                        $sql = $this->db->select('category')->from('business_category')->where('id', $cat1[$i])->get()->row()->category;
                        $category_name1[]=$sql;
                        }
                    }
                    
                    $manager_name1 = $row1['manager_name'];
                    $address1 = $row1['address'];
                    $pincode1 = $row1['pincode'];
                    $contact1 = $row1['contact'];
                    $city1 = $row1['city'];
                    $state1 = $row1['state'];
                    $email1 = $row1['email'];
                    $qualifications1 = $row1['qualifications'];
                    $trainer_opening_hours1 = $row1['opening_hours'];
                    $experience1 = $row1['experience'];
                    $fitness_trainer_pic1 = $row1['fitness_trainer_pic'];
                    $gender1 = $row1['gender'];
                   
                      $final_session1=array();
                    $session1 = array();
                    $session2 = array();
                  
                      $session1[] = array(
                                'id'=>'1',
                                'package_name' => 'zumba',
                                'package_details' => 'Details about the package',
                                'price'=>'1,500',
                                'image'=> 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image
                            );
                         
                           $session1[] = array(
                                'id'=>'2',
                                'package_name' => 'zumba',
                                'package_details' => 'Details about the package1',
                                'price'=>'1,500',
                                'image'=> 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image
                            );
                         
                           
                         $final_session1=  array_merge($session1,$session2);
                         
                         
                     $branch_final_Day1      = array();
                        $day_array_list_branch1 = explode('|', $trainer_opening_hours1);
                        if (count($day_array_list_branch1) > 1) {
                            for ($i = 0; $i < count($day_array_list_branch1); $i++) {
                                $day_list1 = explode('>', $day_array_list_branch1[$i]);
                                for ($j = 0; $j < count($day_list1); $j++) {
                                    $day_time_list1 = explode('-', $day_list1[$j]);
                                    for ($k = 1; $k < count($day_time_list1); $k++) {
                                        $time_list11 = explode(',', $day_time_list1[0]);
                                        $time_list21 = explode(',', $day_time_list1[1]);
                                        $time1       = array();
                                        $open_close1 = array();
                                        for ($l = 0; $l < count($time_list11); $l++) {
                                            $time_check1        = $time_list11[$l] . '-' . $time_list21[$l];
                                            $time1[]            = str_replace('close-close', 'close', $time_check1);
                                            $system_start_time1 = date("H.i", strtotime($time_list11[$l]));
                                            $system_end_time1   = date("H.i", strtotime($time_list21[$l]));
                                            $current_time1      = date('H.i');
                                            if ($current_time1 > $system_start_time1 && $current_time1 < $system_end_time1) {
                                                $open_close1[] = 'open';
                                            } else {
                                                $open_close1[] = 'close';
                                            }
                                        }
                                    }
                                }
                                $branch_final_Day1[] = array(
                                    'day' => $day_list1[0],
                                    'time' => $time1,
                                    'status' => $open_close1
                                );
                            }
                        } else {
                            $branch_final_Day1[] = array(
                                'day' => 'close',
                                'time' => array(),
                                'status' => array()
                            );
                        }
                    $sub_array1[] = array('id'=> $id1,
                                       'category' => $category_name1,
                                       'manager_name' => $manager_name1,
                                       'address' => $address1,
                                       'pincode' => $pincode1,
                                       'city' => $city1,
                                       'state' => $state1,
                                       'email' => $email1,
                                       'contact' => $contact1,
                                       'qualifications' => $qualifications1,
                                       'experience' => $experience1,
                                       'fitness_trainer_pic' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$fitness_trainer_pic1,
                                       'gender' => $gender1,
                                        'rating' => $rating,
                                       'package_trainer' => $final_session1
                                     );
                                       //'opening_hours' => $branch_final_Day);
                    
                }
                    
            }
            else {
               $sub_array1=array();
            }



                    $other_branch[] = array(
                        'branch_id' => $branch_id,
                        'lat' => $lat,
                        'lng' => $lng,
                        'category_id' => $category_id,
                        'category' => $category,
                        'listing_id' => $listing_id,
                        'listing_type' => $listing_type,
                        'about_us' => $about_branch,
                        'branch_name' => $branch_name,
                        'address' => $branch_address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact' => $branch_phone,
                        'enquiry_number' => $enquiry_number,
                        'exotel_no' => '02233721563',
                        'email' => $branch_email,
                        'image' => $branch_image,
                        'is_free_trail' => $is_free_trail,
                        'booking_slot_array' => $booking_slot_array,
                        'gallery' => $gallery_array,
                        'what_we_offer' => $what_we_offer_array,
                        'facilities' => $facilities_array,
                        'package_list' => $package_list,
                        'trainer_list' => $sub_array1,
                        'opening_day' => $final_Day,
                        'rating' => $rating,
                        'review' => $reviews,
                        'followers' => $followers,
                        'following' => $following,
                        'is_follow' => $is_follow,
                        'is_trial_order' => $is_trial_order,
                         'joining_date' =>$joining_date,
                        'profile_views' => $profile_views
                    );
                }
                
                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "center_name" => $center_name,
                    "main_contact" => $main_contact,
                    "main_email" => $main_email,
                    "main_image" => $main_image,
                    "total_rating" => $total_rating,
                    "total_review" => $total_review,
                    "total_profile_views" => $total_profile_views,
                    "other_branch" => $other_branch
                );
            } else {
                $other_branch = array();
                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "center_name" => $center_name,
                    "main_contact" => $main_contact,
                    "main_email" => $main_email,
                    "main_image" => $main_image,
                    "total_rating" => $total_rating,
                    "total_review" => $total_review,
                    "total_profile_views" => $total_profile_views,
                    "other_branch" => $other_branch
                );
            }
        } else {
            $resultpost = array(
                "status" => 200,
                "message" => "success",
                "other_branch" => array()
            );
        }
             
            }
            if ($listing_type == '8') {
                // Hospital
 

                $query = $this->db->query("SELECT * FROM hospitals WHERE user_id='$listing_id'");
                
              
                

                $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                    $id = $row['id'];
                    $name_of_hospital = $row['name_of_hospital'];
                    $mobile = $row['phone'];
                    $about_us = $row['about_us'];
                    $establishment_year = $row['establishment_year'];
                    $certificates_accred = $row['certificates_accred'];
                    $category = $row['category'];
                    $speciality = $row['speciality'];
                    $surgery = $row['surgery'];
                    $services = $row['services'];
                    $address = $row['address'];
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $pincode = $row['pincode'];
    
                    $city = $row['city'];
                    $state = $row['state'];
                    $email = $row['email'];
                    $image22 = $row['image'];
                    $rating = $row['rating'];
                    $reviews = $row['review'];
                    $user_discount = $row['user_discount'];
                    $hospital_user_id = $row['user_id'];
                    $recommended = $row['recommended'];
                    $mba = $row['mba'];
                    $certified = $row['certified'];
                    $profile_views = '0';
    
                  
                    $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $hospital_user_id)->get()->num_rows();
                    $following = $this->db->select('id')->from('follow_user')->where('user_id', $hospital_user_id)->get()->num_rows();
                    $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $hospital_user_id)->get()->num_rows();
    
    
    
    
                    if ($is_follow > 0) {
                        $is_follow = 'Yes';
                    } else {
                        $is_follow = 'No';
                    }
    
                    $certificates_accred_list = array();
                    if($certificates_accred != ''){
                        $certificates_accred_query = $this->db->query("SELECT `name`, image FROM `hospital_certificates_accred` WHERE FIND_IN_SET(name,'" . $certificates_accred . "')");
                        foreach ($certificates_accred_query->result_array() as $get_clist) {
                            $certificates_name = $get_clist['name'];
                            $certificates_image = $get_clist['image'];
        
                            if ($certificates_image != '') {
                                $certificates_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $certificates_image;
                            } else {
                                $certificates_image = '';
                            }
        
        
                            $certificates_accred_list[] = array(
                                "certificates_name" => $certificates_name,
                                "certificates_image" => $certificates_image
                            );
                        }
                    }
                    
                    
                   
                    $hospitals_surgery_list = array();
                    if($surgery != ''){
                        $hospitals_surgery_query = $this->db->query("SELECT `id`,`surgery_name`,`surgery_rate`,`surgery_package`, image FROM `hospitals_surgery` WHERE FIND_IN_SET(surgery_name,'" . $surgery . "')");
                        foreach ($hospitals_surgery_query->result_array() as $get_slist) {
                            $surgery_id = $get_slist['id'];
                            $surgery_name = $get_slist['surgery_name'];
                            $surgery_rate = $get_slist['surgery_rate'];
                            $surgery_package = $get_slist['surgery_package'];
                            $surgery_image = $get_slist['image'];
        
                            if ($surgery_image != '') {
                                $surgery_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/'.$surgery_image;
                            } else {
                                $surgery_image = '';
                            }
        
        
                            $hospitals_surgery_list[] = array(
                                "surgery_id" => $surgery_id,
                                "surgery_name" => $surgery_name,
                                "surgery_rate" => $surgery_rate,
                                "surgery_package" => $surgery_package,
                                "surgery_image" => $surgery_image
                            );
                        }
                    }
    
                    
                    $gallery = array();
                    $gallery_query = $this->db->query("SELECT `title`, CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/gallery/',source) AS source FROM `hospital_media` WHERE hospital_id='$hospital_user_id'");
                    foreach ($gallery_query->result_array() as $get_glist) {
                        
                        $title = $get_glist['title'];
                        $media_image = $get_glist['source'];
                        $gallery[] = array(
                            "title" => $title,
                            "image" => $media_image
                        );
                    }
    
                    
                    $hospitals_service_list = array();
                    if($services != ''){
                        $hospitals_services_query = $this->db->query("SELECT `service_name` FROM `hospital_services` WHERE FIND_IN_SET(id,'" . $services . "')");
                        foreach ($hospitals_services_query->result_array() as $get_serlist) {
                            $service_name = $get_serlist['service_name'];
                            $hospitals_service_list[] = array(
                                "service_name" => $service_name
                            );
                        }
                    }
    
    
                    $hospitals_speciality_list = array();
                    if($speciality != ''){
                        
                        $hospitals_speciality_query = $this->db->query("SELECT `id`,`doctors_category`,`name`, image FROM `hospitals_specialist` WHERE FIND_IN_SET(id,'" . $speciality . "')");
                        foreach ($hospitals_speciality_query->result_array() as $get_splist) {
                            $specialist_id = $get_splist['id'];
                            $specialist_name = $get_splist['name'];
                            $doctors_category = $get_splist['doctors_category'];
                            $specialist_image = $get_splist['image'];
        
                            if ($specialist_image != '') {
                                $specialist_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $specialist_image;
                            } else {
                                $specialist_image = '';
                            }
        
                            $hospitals_speciality_list[] = array(
                                "specialist_id" => $specialist_id,
                                "specialist_name" => $specialist_name,
                                "doctors_category" => $doctors_category,
                                "specialist_image" => $specialist_image
                            );
                        }
                    }
                    
                    
                    //added by zak for lab packages for hospitals 
                    $package_list  = array();
                    $package_query = $this->db->query("SELECT * FROM `lab_packages` WHERE user_id='$hospital_user_id' and branch_id ='' order by id asc");
                    //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
                    $package_count = $package_query->num_rows();
                    if ($package_count > 0) {
                        foreach ($package_query->result_array() as $package_row) {
                            $package_id      = $package_row['id'];
                            $package_name    = $package_row['lab_name'];
                            $package_details = $package_row['lab_details'];
                            $price           = $package_row['Price'];
                            $image           = $package_row['image'];
                            $home_delivery   = $package_row['home_delivery'];
                            //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                            $package_list[]  = array(
                                'package_id' => $package_id,
                                'package_name' => $package_name,
                                'package_details' => $package_details,
                                'price' => $price,
                                'home_delivery' => $home_delivery
                            );
                        }
                    } else {
                        $package_list = array();
                    }
                    
                    //Review Count
                    $Review_query = $this->db->query("SELECT * FROM `hospital_review` WHERE hospital_id=$hospital_user_id");
                    $Review_count = $Review_query->num_rows();
                    
                    
                    //Profile View
                    //echo "SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'";
                    $Profile_query = $this->db->query("SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'");
                    $Profile_count = $Profile_query->num_rows();
                    /* $distances = str_replace(',', '.', GetDrivingDistance($latitude, $lat, $longitude, $lng));
                     $distance_root =  round(($distances/1000),1);
                      if ($distances > 999) {
                                    $distance = $distances / 1000;
                                    $meter = round($distance, 2) . ' km';
                                } else {
                                    $meter = round($distances) . ' meters away';
                                }*/
                    //end 
                      if (!empty($image22) ) {
                        $image22 = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$image22;
                    } 
                    elseif($image22==null || $image22=='')
                    {
                        $image22 = '';
                    }
                    
                     $fav_hospitals = $this->db->select('id')->from('hospitals_favourite')->where('user_id', $user_id)->where('listing_id', $hospital_user_id)->get()->num_rows();
                
                    
                    $resultpost[] = array(
                        'id' => $id,
                        'hospital_id' => $hospital_user_id,
                        'name_of_hospital' => $name_of_hospital,
                        'listing_type' => "8",
                        'about_us' => $about_us,
                        'establishment_year' => $establishment_year,
                        'certificates_accred_list' => $certificates_accred_list,
                        'gallery' => $gallery,
                        'hospitals_service_list' => $hospitals_service_list,
                        'hospitals_speciality_list' =>$hospitals_speciality_list,
                        'address' => $address,
                        'mobile' => $mobile,
                        'lat' => $lat,
                        'lng' => $lng,
                        'distance' => 0,
                        'is_24hrs_available' => 'Yes',
                        'open_status'=> "Open Now",
                        'store_open' => "00:00 AM",
                        'store_close' => "11:59 PM",
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => $rating,
                        'review' => $Review_count,
                        'image' => $image22,
                        'followers' => $followers,
                        'following' => $following,
                        'profile_views' => $Profile_count,
                        'is_follow' => $is_follow,
                        'user_discount' => $user_discount,
                        'recommended' => $recommended,
                        'mba' => $mba,
                        'certified' => $certified,
                        'favourite' => $fav_hospitals,
                        'branch_list'=>array(),
                        'lab_package_list' => $package_list
                        
                    );
                    
             }
                } else {
                    $resultpost = array();
                }
            }

            if ($listing_type == '21') {
                // Ambulance

                $query = $this->db->query("SELECT id,`user_id`, `name`, `address`, `phone`, `state`, `city`, `pincode`, `vehicle_in_services`, `ac_available`, `all_24hrs_available`, `establishment_year`, `image`, `list_of_equipment`, `lat`, `lng`, `reviews`, `ratings` FROM ambulance WHERE user_id='$listing_id'");

                $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id = $row['id'];
                        $name = $row['name'];
                        $address = $row['address'];
                        $phone = $row['phone'];
                        $state = $row['state'];
                        $city = $row['city'];
                        $pincode = $row['pincode'];
                        $vehicle_in_services = $row['vehicle_in_services'];
                        $ac_available = $row['ac_available'];
                        $all_24hrs_available = $row['all_24hrs_available'];
                        $establishment_year = $row['establishment_year'];
                        $list_of_equipment2 = $row['list_of_equipment'];
                        $image = $row['image'];
                        $lat = $row['lat'];
                        $lng = $row['lng'];
                        $list_of_equipment = array();
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/ambulance_images/' . $image;
                        } else {
                            $image = '';
                        }

                        $ambulance_user_id = $row['user_id'];
                        $rating = $row['ratings'];
                        $profile_views = '0';
                        $reviews = $row['reviews'];


                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $ambulance_user_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $ambulance_user_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $ambulance_user_id)->where('parent_id', $ambulance_user_id)->get()->num_rows();

                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }



                        $list_of_equipment_query = $this->db->query("SELECT `name` FROM `ambulance_equipment_list` WHERE FIND_IN_SET(name,'" . $list_of_equipment2 . "')");
                        foreach ($list_of_equipment_query->result_array() as $get_list) {
                            $name = $get_list['name'];
                            $list_of_equipment[] = array(
                                "equipment_name" => $name
                            );
                        }

                        $resultpost[] = array(
                            'id' => $id,
                            'ambulance_user_id' => $ambulance_user_id,
                            'name' => $name,
                            'address' => $address,
                            'phone' => $phone,
                            "exotel_no" => '02233721563',
                            'state' => $state,
                            'city' => $city,
                            'pincode' => $pincode,
                            'latitude' => $lat,
                            'longitude' => $lng,
                            'vehicle_in_services' => $vehicle_in_services,
                            'ac_available' => $ac_available,
                            '24hrs_available' => $all_24hrs_available,
                            'establishment_year' => $establishment_year,
                            'image' => $image,
                            'list_of_equipment' => $list_of_equipment,
                            'rating' => $rating,
                            'followers' => $followers,
                            'following' => $following,
                            'profile_views' => $profile_views,
                            'reviews' => $reviews,
                            'is_follow' => $is_follow
                        );
                    }
                } else {
                    $resultpost = array();
                }
            }

            if ($listing_type == '18') {
                // Old Age Home
                $query = $this->db->query("SELECT `id`, `user_id`, `name`, `about_us`, `establishment_year`, `address`, `phone`, `telephone`, `state`, `city`, `pincode`, `image`, `service_offered`, `lat`, `lng`, `reviews`, `ratings` FROM oldagehome where user_id='$listing_id' limit 1");

                $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id = $row['id'];
                        $name = $row['name'];
                        $about_us = $row['about_us'];
                        $establishment_year = $row['establishment_year'];
                        $address = $row['address'];
                        $phone = $row['phone'];
                        $state = $row['state'];
                        $city = $row['city'];
                        $pincode = $row['pincode'];
                        $oldagehome_service_offered2 = $row['service_offered'];
                        $image = $row['image'];
                        $lat = $row['lat'];
                        $lng = $row['lng'];
                        $oldagehome_service_offered = array();
                        $gallery_list = array();
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/oldagehome_images/' . $image;
                        } else {
                            $image = '';
                        }

                        $oldagehome_user_id = $row['user_id'];
                        $rating = $row['ratings'];
                        $profile_views = '0';
                        $reviews = $row['reviews'];


                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $oldagehome_user_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $oldagehome_user_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $oldagehome_user_id)->where('parent_id', $oldagehome_user_id)->get()->num_rows();

                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }



                        $service_offered_query = $this->db->query("SELECT `name` FROM `oldagehome_service_offered` WHERE FIND_IN_SET(name,'" . $oldagehome_service_offered2 . "')");
                        foreach ($service_offered_query->result_array() as $get_list) {
                            $service_offered = $get_list['name'];
                            $oldagehome_service_offered[] = array(
                                "service_offered" => $service_offered
                            );
                        }

                        $gallery_query = $this->db->query("SELECT `title` , CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/oldagehome_images/',source) AS media FROM `oldagehome_media` WHERE oldagehome_id='$id'");
                        foreach ($gallery_query->result_array() as $get_list2) {
                            $gallery_title = $get_list2['title'];
                            $gallery_image = $get_list2['media'];
                            $gallery_list[] = array(
                                "title" => $gallery_title,
                                "image" => $gallery_image
                            );
                        }



                        $resultpost[] = array(
                            'id' => $id,
                            'oldagehome_user_id' => $oldagehome_user_id,
                            'name' => $name,
                            'about_us' => $about_us,
                            'establishment_year' => $establishment_year,
                            'address' => $address,
                            'phone' => $phone,
                            'state' => $state,
                            'city' => $city,
                            'pincode' => $pincode,
                            "exotel_no" => '02233721563',
                            'latitude' => $lat,
                            'longitude' => $lng,
                            'image' => $image,
                            'service_offered' => $oldagehome_service_offered,
                            'gallery' => $gallery_list,
                            'rating' => $rating,
                            'followers' => $followers,
                            'following' => $following,
                            'profile_views' => $profile_views,
                            'reviews' => $reviews,
                            'is_follow' => $is_follow
                        );
                    }
                } else {
                    $resultpost = array();
                }
            }

            if ($listing_type == '19') {
                // Pest Control

                $query = $this->db->query("SELECT `id`,`user_id`,`image`, `name`, `description`,`address`, `pincode`, `contact`, `city`, `state`, `whatsapp`, `email`, `opening_hours`, `image`, `lat`, `lng`, `date`, `is_active` FROM `pest_control` WHERE user_id='$listing_id' order by id asc");
                $count = $query->num_rows();
                if ($count > 0) {

                    foreach ($query->result_array() as $row) {
                        $id = $row['id'];
                        $pestcontrol_id = $row['id'];
                        $pestcontrol_user_id = $row['user_id'];
                        $name = $row['name'];
                        $address = $row['address'];
                        $pincode = $row['pincode'];
                        $contact = $row['contact'];
                        $city = $row['city'];
                        $state = $row['state'];
                        $whatsapp = $row['whatsapp'];
                        $email = $row['email'];
                        $opening_hours = $row['opening_hours'];
                        $pestcontrol_user_id = $row['user_id'];
                        $lat = $row['lat'];
                        $lng = $row['lng'];
                        $rating = '4.5';
                        $profile_views = '0';
                        $reviews = '0';
                        $description = $row['description'];
                        $image = $row['image'];
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/pestcontrol_images/' . $image;

                        $gallery_query = $this->db->query("SELECT * FROM `pest_control_media` WHERE `pest_control_id`='$id'");
                        $gallery_array = array();
                        $gallery_count = $gallery_query->num_rows();
                        if ($gallery_count > 0) {
                            foreach ($gallery_query->result_array() as $rows) {
                                $media_name = $rows['title'];
                                $source = $rows['source'];
                                $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/' . $source;

                                $media_name = str_replace(".jpg", "", $media_name);
                                $gallery_name = $media_name;

                                $cnt = count($gallery);

                                $gallery_array[] = array(
                                    "title" => $gallery_name,
                                    "image" => $gallery
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



                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $pestcontrol_user_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $pestcontrol_user_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $pestcontrol_user_id)->get()->num_rows();

                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }




                        $gallery_query = $this->db->query("SELECT GROUP_CONCAT(`title`) AS title, GROUP_CONCAT(CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/pestcontrol_images/',source)) AS media FROM `pest_control_media`");
                        if ($gallery_query) {
                            $row2 = $gallery_query->row();
                            $gallery_name = $row2->title;
                            $gallery = $row2->media;
                        } else {
                            $gallery = '';
                            $gallery_name = '';
                        }
   $pestcontrol_packages = $this->db->query("SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id = '$pestcontrol_user_id'");
                      //   echo "SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id = '$pestcontrol_user_id'";
                        $count2 = $pestcontrol_packages->num_rows();
                        if ($count2 > 0) {

                            foreach ($pestcontrol_packages->result_array() as $get_list) {
                                $id = $get_list['id'];
                                $package_name = $get_list['package_name'];
                                $package_details = $get_list['package_details'];
                                $price = $get_list['price'];
                                $image = $get_list['image'];
                                if(!empty($image))
                                {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/pestcontrol_images/' . $image;
                                }
                                else
                                {
                                    $image = ""; 
                                }
                                $package[] = array(
                                   "id" => $id,
                                        "package_name" => $package_name,
                                        "package_details" => $package_details,
                                        'price' => $price,
                                        'image' => $image
                                );
                            }
                        } else {

                            $package = array();
                        }  

                      
                        $resultpost[] = array(
                            "id" => $id,
                            "pestcontrol_user_id" => $pestcontrol_user_id,
                            "name" => $name,
                            "address" => $address,
                            "pincode" => $pincode,
                            "telephone" => $contact,
                            "city" => $city,
                            "state" => $state,
                            "whatsapp" => $whatsapp,
                            "email" => $email,
                            "gallery" => $gallery_array,
                            "exotel_no" => '02233721563',
                            //"gallery_name"=>$gallery_name,
                            "description" => $description,
                            "rating" => $rating,
                            "followers" => $followers,
                            "following" => $following,
                            "profile_view" => $profile_views,
                            "review" => $reviews,
                            "is_follow" => $is_follow,
                            "lat" => $lat,
                            "lng" => $lng,
                            'opening_day' => $final_Day,
                            "image" => $image,
                            "package" => $package
                        );
                    }
                } else {
                    $resultpost = array();
                }
            }

            if ($listing_type == '4') {
                // Blood Bank

                $query = $this->db->query("SELECT user_id,id,name as bank_name,year as established,phone as contact,address,about as about_us,hours_open as opening_hours,fda_no as fda_lic_no,bto as bto_name,component,image,reviews,lat,lng,ratings,state,city,pincode FROM blood_bank WHERE user_id='$listing_id'");

                $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id = $row['id'];
                        $bank_name = $row['bank_name'];
                        $established = $row['established'];
                        $contact = $row['contact'];
                        $address = $row['address'];
                        $about_us = $row['about_us'];

                        $fda_lic_no = $row['fda_lic_no'];
                        $bto_name = $row['bto_name'];
                        $component = $row['component'];
                        $image = $row['image'];
                        $lat = $row['lat'];
                        $lng = $row['lng'];
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/bloodbank_images' . $image;
                        } else {
                            $image = '';
                        }
                        $blood_bank_user_id = $row['user_id'];
                        $rating = $row['ratings'];
                        $profile_views = '0';
                        $reviews = $row['reviews'];
                        $opening_hours = $row['opening_hours'];
                        $open_days = '';
                        $day_array_list = '';
                        $day_list = '';
                        $day_time_list = '';
                        $time_list1 = '';
                        $time_list2 = '';
                        $time = '';
                        $system_start_time = '';
                        $system_end_time = '';
                        $time_check = '';
                        $current_time = '';
                        $open_close = array();
                        $time = array();
                        date_default_timezone_set('Asia/Kolkata');
                        $data = array();
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
                                            $time = str_replace('close-close', 'close', $time_check);
                                            if ($time == '12:00 AM-11:59 PM') {
                                                $time = '24 hrs open';
                                            }
                                            $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                                            $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                                            $current_time = date('h:i A');


                                            $date1 = DateTime::createFromFormat('H:i a', $current_time);
                                            $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                                            $date3 = DateTime::createFromFormat('H:i a', $system_end_time);

                                            if ($date2 < $date3 && $date1 <= $date3) {
                                                $date3->modify('+1 day')->format('H:i a');
                                            } elseif ($date2 > $date3 && $date1 >= $date3) {
                                                $date3->modify('+1 day')->format('H:i a');
                                            }


                                            if ($date1 > $date2 && $date1 < $date3) {
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
                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $blood_bank_user_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $blood_bank_user_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $blood_bank_user_id)->get()->num_rows();
                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }
                        $resultpost[] = array(
                            'id' => $id,
                            'blood_bank_user_id' => $blood_bank_user_id,
                            'bank_name' => $bank_name,
                            'established' => $established,
                            'contact' => $contact,
                            'address' => $address,
                            'about_us' => $about_us,
                            "exotel_no" => '02233721563',
                            'fda_lic_no' => $fda_lic_no,
                            'bto_name' => $bto_name,
                            'component' => $component,
                            'opening_day' => $final_Day,
                            'image' => $image,
                            'rating' => $rating,
                            'followers' => $followers,
                            'following' => $following,
                            'profile_views' => $profile_views,
                            'reviews' => $reviews,
                            'is_follow' => $is_follow,
                            'lat' => $lat,
                            'lng' => $lng
                        );
                    }
                } else {
                    $resultpost = array();
                }
            }

            if ($listing_type == '15') {
                // Baby Sitter


                $query = $this->db->query("SELECT `id`, `user_id`, `name`, `about_us`, `establishment_year`, `address`, `phone`, `telephone`, `state`, `city`, `pincode`, `image`, `service_offered`, `lat`, `lng`, `reviews`, `ratings` FROM babysitter WHERE user_id = '$listing_id' order by id ASC");

                $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id = $row['id'];
                        $name = $row['name'];
                        $about_us = $row['about_us'];
                        $establishment_year = $row['establishment_year'];
                        $address = $row['address'];
                        $mobile = $row['phone'];
                        $state = $row['state'];
                        $city = $row['city'];
                        $pincode = $row['pincode'];
                        $babysitter_service_offered2 = $row['service_offered'];
                        $image = $row['image'];
                        $lat = $row['lat'];
                        $lng = $row['lng'];
                        $babysitter_service_offered = array();
                        $gallery_list = array();
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/babysitter_images/' . $image;
                        } else {
                            $image = '';
                        }

                        $babysitter_user_id = $row['user_id'];
                        $rating = $row['ratings'];
                        $profile_views = '415';
                        $reviews = $row['reviews'];


                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $babysitter_user_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $babysitter_user_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $babysitter_user_id)->get()->num_rows();

                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }



                        $service_offered_query = $this->db->query("SELECT `name` FROM `babysitter_service_offered` WHERE FIND_IN_SET(name,'" . $babysitter_service_offered2 . "')");
                        foreach ($service_offered_query->result_array() as $get_list) {
                            $service_offered = $get_list['name'];
                            $babysitter_service_offered[] = array(
                                "service_offered" => $service_offered
                            );
                        }

                        $gallery_query = $this->db->query("SELECT `title` , CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/babysitter_images/',source) AS media FROM `babysitter_media` WHERE babysitter_id='$id'");
                        foreach ($gallery_query->result_array() as $get_list2) {
                            $gallery_title = $get_list2['title'];
                            $gallery_image = $get_list2['media'];
                            $gallery_list[] = array(
                                "title" => $gallery_title,
                                "image" => $gallery_image
                            );
                        }



                        $resultpost[] = array(
                            'id' => $id,
                            'babysitter_user_id' => $babysitter_user_id,
                            'name' => $name,
                            'about_us' => $about_us,
                            'establishment_year' => $establishment_year,
                            'address' => $address,
                            'telephone' => $mobile,
                            'state' => $state,
                            'city' => $city,
                            "exotel_no" => '02233721563',
                            'pincode' => $pincode,
                            'latitude' => $lat,
                            'longitude' => $lng,
                            'image' => $image,
                            'service_offered' => $babysitter_service_offered,
                            'gallery' => $gallery_list,
                            'rating' => $rating,
                            'followers' => $followers,
                            'following' => $following,
                            'profile_view' => $profile_views,
                            'review' => $reviews,
                            'is_follow' => $is_follow
                        );
                    }
                } else {
                    $resultpost = array();
                }
            }

            if ($listing_type == '22') {
                // Nanny

                $query = $this->db->query("SELECT `id`, `user_id`, `name`, `about_us`, `establishment_year`, `address`, `phone`, `telephone`, `state`, `city`, `pincode`, `image`, `service_offered`, `lat`, `lng`, `reviews`, `ratings` FROM dai_nanny WHERE user_id = '$listing_id' order by id ASC");

                $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id = $row['id'];
                        $name = $row['name'];
                        $about_us = $row['about_us'];
                        $establishment_year = $row['establishment_year'];
                        $address = $row['address'];
                        $mobile = $row['phone'];
                        $state = $row['state'];
                        $city = $row['city'];
                        $pincode = $row['pincode'];
                        $dai_nanny_service_offered2 = $row['service_offered'];
                        $image = $row['image'];
                        $lat = $row['lat'];
                        $lng = $row['lng'];
                        $dai_nanny_service_offered = array();
                        $gallery_list = array();
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/dai_nanny_images/' . $image;
                        } else {
                            $image = '';
                        }

                        $dai_nanny_user_id = $row['user_id'];
                        $rating = $row['ratings'];
                        $profile_views = '415';
                        $reviews = $row['reviews'];


                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $dai_nanny_user_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $dai_nanny_user_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $dai_nanny_user_id)->get()->num_rows();

                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }



                        $service_offered_query = $this->db->query("SELECT `name` FROM `dai_nanny_service_offered` WHERE FIND_IN_SET(name,'" . $dai_nanny_service_offered2 . "')");
                        foreach ($service_offered_query->result_array() as $get_list) {
                            $service_offered = $get_list['name'];
                            $dai_nanny_service_offered[] = array(
                                "service_offered" => $service_offered
                            );
                        }

                        $gallery_query = $this->db->query("SELECT `title` , CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/dai_nanny_images/',source) AS media FROM `dai_nanny_media` WHERE dai_nanny_id='$id'");
                        foreach ($gallery_query->result_array() as $get_list2) {
                            $gallery_title = $get_list2['title'];
                            $gallery_image = $get_list2['media'];
                            $gallery_list[] = array(
                                "title" => $gallery_title,
                                "image" => $gallery_image
                            );
                        }



                        $resultpost[] = array(
                            'id' => $id,
                            'dai_nanny_user_id' => $dai_nanny_user_id,
                            'name' => $name,
                            'about_us' => $about_us,
                            'establishment_year' => $establishment_year,
                            'address' => $address,
                            'mobile' => $mobile,
                            "exotel_no" => '02233721563',
                            'state' => $state,
                            'city' => $city,
                            'pincode' => $pincode,
                            'latitude' => $lat,
                            'longitude' => $lng,
                            'image' => $image,
                            'service_offered' => $dai_nanny_service_offered,
                            'gallery' => $gallery_list,
                            'rating' => $rating,
                            'followers' => $followers,
                            'following' => $following,
                            'profile_views' => $profile_views,
                            'reviews' => $reviews,
                            'is_follow' => $is_follow
                        );
                    }
                } else {
                    $resultpost = array();
                }
            }

            if ($listing_type == '23') {
                // 	Psychiatrist

                $query = $this->db->query("SELECT id,lat,lng,user_id,doctor_name,about_us,speciality,address,telephone,medical_college,medical_affiliation,charitable_affiliation,awards_recognition,all_24_hrs_available,home_visit_available,qualification,consultation_fee,experience,website,location,days,timing,image,rating,review FROM psychiatrist_list WHERE user_id='$listing_id'");

                $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id = $row['id'];
                        $doctor_name = $row['doctor_name'];
                        $about_us = $row['about_us'];

                        $address = $row['address'];
                        $telephone = $row['telephone'];
                        $medical_college = $row['medical_college'];
                        $medical_affiliation = $row['medical_affiliation'];
                        $charitable_affiliation = $row['charitable_affiliation'];
                        $awards_recognition = $row['awards_recognition'];
                        $hrs_available = $row['all_24_hrs_available'];
                        $home_visit_available = $row['home_visit_available'];
                        $qualification = $row['qualification'];
                        $consultation_fee = $row['consultation_fee'];
                        $experience = $row['experience'];
                        $website = $row['website'];
                        $location = $row['location'];
                        $days = $row['days'];
                        $timing = $row['timing'];
                        $rating = $row['rating'];
                        $reviews = $row['review'];
                        $image = $row['image'];
                        $doctor_user_id = $row['user_id'];
                        $profile_views = '0';



                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $image;
                        } else {
                            $image = '';
                        }


                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();

                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $doctor_user_id)->get()->num_rows();
                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }
                        $result_hospital = '';

                        $hospital_query = $this->db->query("select hospitals.id as hospital_id,doctor_list.id as doctor_id,hospitals.name_of_hospital,hospitals.address,hospitals.rating,hospitals.image,doctor_hospital_list.opening_days from doctor_list INNER JOIN doctor_hospital_list on doctor_hospital_list.doctor_id = doctor_list.id INNER JOIN hospitals on hospitals.id=doctor_hospital_list.hospital_id where doctor_list.id='$id'");
                        $total_hospital = $hospital_query->num_rows();
                        if ($total_hospital > 0) {
                            foreach ($hospital_query->result_array() as $hospital_row) {
                                $id = $hospital_row['hospital_id'];
                                $hospital_name = $hospital_row['name_of_hospital'];
                                $address = $hospital_row['address'];
                                $rating = $hospital_row['rating'];
                                $hospital_image = $hospital_row['image'];
                                $opening_days = $hospital_row['opening_days'];
                                if ($hospital_image != '') {
                                    $hospital_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $hospital_image;
                                } else {
                                    $hospital_image = '';
                                }


                                date_default_timezone_set('Asia/Kolkata');
                                $open_days = '';
                                $day = '';
                                $time = '';
                                $start_time = '';
                                $end_time = '';
                                $opening_hours = explode(',', $opening_days);
                                foreach ($opening_hours as $opening_hour) {
                                    $array_hours = explode('-', $opening_hour);
                                    $day = $array_hours[0];
                                    $start_time = $array_hours[1];
                                    $end_time = $array_hours[2];
                                    $time = $start_time . ' - ' . $end_time;
                                    $open_days[] = array(
                                        'day' => $day,
                                        'time' => $time
                                    );
                                }

                                $result_hospital[] = array(
                                    'id' => $id,
                                    'hospital_name' => $hospital_name,
                                    'address' => $address,
                                    'rating' => $rating,
                                    'image' => $hospital_image,
                                    'opening_day' => $open_days
                                );
                            }
                        } else {
                            $result_hospital = array();
                        }
                        $service = '';
                        $result_services = '';
                        $doctor_services_query = $this->db->query("SELECT service FROM `doctor_services`");
                        foreach ($doctor_services_query->result_array() as $doctor_services) {
                            $service = $doctor_services['service'];
                            $result_services[] = array(
                                'service' => $service
                            );
                        }
                        $specialization = '';
                        $result_specialization = '';
                        $doctor_specialization_query = $this->db->query("SELECT specialization FROM `doctor_specialization`");
                        foreach ($doctor_specialization_query->result_array() as $doctor_specialization) {
                            $specialization = $doctor_specialization['specialization'];
                            $result_specialization[] = array(
                                'specialization' => $specialization
                            );
                        }


                        $resultpost[] = array(
                            'doctor_id' => $id,
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'about_us' => $about_us,
                            'address' => $address,
                            'telephone' => $telephone,
                            "exotel_no" => '02233721563',
                            'medical_college' => $medical_college,
                            'medical_affiliation' => $medical_affiliation,
                            'charitable_affiliation' => $charitable_affiliation,
                            'awards_recognition' => $awards_recognition,
                            'hrs_available' => $hrs_available,
                            'home_visit_available' => $home_visit_available,
                            'qualification' => $qualification,
                            'consultation_fee' => $consultation_fee,
                            'experience' => $experience,
                            'website' => $website,
                            'location' => $location,
                            'days' => $days,
                            'timing' => $timing,
                            'rating' => $rating,
                            'review' => $reviews,
                            'image' => $image,
                            'followers' => $followers,
                            'following' => $following,
                            'profile_views' => $profile_views,
                            'is_follow' => $is_follow,
                            'doctor_practices' => $result_hospital,
                            'doctor_services' => $result_services,
                            'doctor_specialization' => $result_specialization
                        );
                    }
                } else {
                    $resultpost = array();
                }
            }

            
             if ($listing_type == '11') {
                // college
               
                
                  $college_faculty = array();
        $ammenties =array();
     
        $query = $this->db->query("SELECT id,college_id,teacher_name,designation,qualification,image,experience from medical_college_faculty where user_id='$listing_id'");
      //   echo "SELECT id,college_id,teacher_name,designation,qualification,image,experience from medical_college_faculty where user_id='$listing_id'";
       $count = $query->num_rows();
 if ($count > 0) {
        foreach ($query->result_array() as $row) {
            
        
            
            $id = $row['id'];
            $college_id = $row['college_id'];
            $teacher_name = $row['teacher_name'];
            $designation = $row['designation'];
            $qualification = $row['qualification'];
            $experience = $row['experience'];
            $faculty_image = "https://s3.amazonaws.com/medicalwale/images/medical_college_images/image/".$row['image'];
            
            $college_faculty[] = array(
                "id" => $id,
                "college_id"    => $college_id,
                "teacher_name"  => $teacher_name,
                "designation"   => $designation,
                "faculty_experience"   => $experience,
                "faculty_image"   => $faculty_image,
                "qualification" => $qualification
            );
        }
        
        $college_courses = array();
        $query = $this->db->query("SELECT id,college_id,course_name,type,duration,description,fees from medical_college_courses where user_id='$listing_id'");
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
        $college_details = array();
        $query = $this->db->query("SELECT * FROM `medical_college_details` WHERE `user_id`='$listing_id'");
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
            
          
        //      //start for ememeinities
        //  //$sql=$this->db->query("SELECT medical_college_amenities.*,college_gallery.id,college_gallery.user_id,college_gallery.name,college_gallery.amenity_id as college_gallery_amenities,college_gallery.type FROM  medical_college_amenities LEFT join college_gallery on (medical_college_amenities.amenity_id=college_gallery.amenity_id) WHERE medical_college_amenities.medical_college_id='".$user_id."'");  
           
        //   $sql=$this->db->query("SELECT * FROM  medical_college_amenities WHERE medical_college_id='".$listing_id."'");  
        //   $clgdetails = $sql->result();
           

           
        //   for($x=0;$x<count($clgdetails);$x++) 
        //   {
        //       $amenity_id = $clgdetails[$x]->amenity_id;
        //       $amenity_name= $clgdetails[$x]->amenity_name;
        //       $amenity_description = $clgdetails[$x]->description;
                
                
        //         $sql=$this->db->query("SELECT *  FROM  college_gallery WHERE amenity_id='".$clgdetails[$x]->amenity_id."'");  
        //         $Imgclgdetails = $sql->result();
          
        //     $amenity_image=array();
        //   foreach($Imgclgdetails as $ImgColg){

        
        //   $path ="";
        //   if($ImgColg->type == 'video')
        //   {

        //     $path = "https://s3.amazonaws.com/medicalwale/images/medical_college_images/video/".$ImgColg->name;
            

        //     }
        //     if($ImgColg->type == 'image')
        //     {

        //     $path = "https://s3.amazonaws.com/medicalwale/images/medical_college_images/image/".$ImgColg->name;
            

        //     }
          
        //         $amenity_image[] = $path;
        //         $ammentie = array(
        //         "amenity_id" => $amenity_id,
        //         "amenity_name" => $amenity_name,
        //         "amenity_description" => $amenity_description,
        //         "amenity_image" => $amenity_image
        //         );
               
        //   }
            
        
        //     $ammenties[] = $ammentie;
            
           
        //   }
            //end eminities
            
                       //start for ememeinities
         //$sql=$this->db->query("SELECT medical_college_amenities.*,college_gallery.id,college_gallery.user_id,college_gallery.name,college_gallery.amenity_id as college_gallery_amenities,college_gallery.type FROM  medical_college_amenities LEFT join college_gallery on (medical_college_amenities.amenity_id=college_gallery.amenity_id) WHERE medical_college_amenities.medical_college_id='".$user_id."'");  
           
           $ammentie =new stdClass();
           $sql=$this->db->query("SELECT * FROM  medical_college_amenities WHERE medical_college_id='".$listing_id."'");  
           $clgdetails = $sql->result();
            $clg_count = $sql->num_rows();
            
           
          
           
           for($x=0;$x<$clg_count;$x++) 
           {
              
               $amenity_id = $clgdetails[$x]->amenity_id;
               $amenity_name= $clgdetails[$x]->amenity_name;
               $amenity_description = $clgdetails[$x]->description;
               
                
                $sql=$this->db->query("SELECT *  FROM  college_gallery WHERE amenity_id='".$amenity_id."'"); 
              
                $Imgclgdetails = $sql->result();
          
            $amenity_image=array();
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
                
               
           }
            
            
            $ammentie = array(
                "amenity_id" => $amenity_id,
                "amenity_name" => $amenity_name,
                "amenity_description" => $amenity_description,
                "amenity_image" => $amenity_image
                );
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
            //end eminities
            
            
            $college_details[] = array(
                "id" => $id,
                "cat_id" => $cat_id,
                "aidedby" => $aidedby,
                "college_type" => $college_type,
                "college_name" => $college_name,
                "estabishment" => $estabishment,
                "phone" => $phone,
                "exotel_no" => '02233721563',
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
        
      

       
        $images = array();
        //$query = $this->db->query("SELECT id,college_id,media,source FROM `medical_college_media` WHERE `college_id`='$college_id'");
        $query = $this->db->query("SELECT * FROM `college_gallery` WHERE `user_id`='$listing_id'");
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
        $branch_query = $this->db->query("SELECT * FROM `medical_center_branch` LEFT JOIN medical_college_details ON medical_college_details.user_id=medical_center_branch.user_id WHERE medical_center_branch.user_id='$listing_id'");
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

        $resultpost[] = array(
            'college_details' => $college_details,
            'course' => $college_courses,
            'faculty' => $college_faculty,
            'images' => $images,
            'branches' => $branches
        );

        return $resultpost;
 }
  else {
                    $resultpost = array();
                }
                
 
            }

            if ($listing_type == '24') {
                // 	Counselling

                $query = $this->db->query("SELECT id,lat,lng,user_id,doctor_name,about_us,speciality,address,telephone,medical_college,medical_affiliation,charitable_affiliation,awards_recognition,all_24_hrs_available,home_visit_available,qualification,consultation_fee,experience,website,location,days,timing,image,rating,review FROM counselling_list WHERE user_id='$listing_id' ");

                $count = $query->num_rows();
               
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id = $row['id'];
                        $doctor_name = $row['doctor_name'];
                        $about_us = $row['about_us'];

                        $address = $row['address'];
                        $telephone = $row['telephone'];
                        $medical_college = $row['medical_college'];
                        $medical_affiliation = $row['medical_affiliation'];
                        $charitable_affiliation = $row['charitable_affiliation'];
                        $awards_recognition = $row['awards_recognition'];
                        $hrs_available = $row['all_24_hrs_available'];
                        $home_visit_available = $row['home_visit_available'];
                        $qualification = $row['qualification'];
                        $consultation_fee = $row['consultation_fee'];
                        $experience = $row['experience'];
                        $website = $row['website'];
                        $location = $row['location'];
                        $days = $row['days'];
                        $timing = $row['timing'];
                        $rating = $row['rating'];
                        $reviews = $row['review'];
                        $image = $row['image'];
                        $doctor_user_id = $row['user_id'];
                        $profile_views = '2458';


                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $image;
                        } else {
                            $image = '';
                        }

                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $doctor_user_id)->get()->num_rows();

                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }

                        $result_hospital = '';

                        $hospital_query = $this->db->query("select hospitals.id as hospital_id,doctor_list.id as doctor_id,hospitals.name_of_hospital,hospitals.address,hospitals.rating,hospitals.image,doctor_hospital_list.opening_days from doctor_list INNER JOIN doctor_hospital_list on doctor_hospital_list.doctor_id = doctor_list.id INNER JOIN hospitals on hospitals.id=doctor_hospital_list.hospital_id where doctor_list.id='$id'");
                        $total_hospital = $hospital_query->num_rows();
                        if ($total_hospital > 0) {
                            foreach ($hospital_query->result_array() as $hospital_row) {
                                $id = $hospital_row['hospital_id'];
                                $hospital_name = $hospital_row['name_of_hospital'];
                                $address = $hospital_row['address'];
                                $rating = $hospital_row['rating'];
                                $hospital_image = $hospital_row['image'];
                                $opening_days = $hospital_row['opening_days'];
                                if ($hospital_image != '') {
                                    $hospital_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $hospital_image;
                                } else {
                                    $hospital_image = '';
                                }


                                date_default_timezone_set('Asia/Kolkata');
                                $open_days = '';
                                $day = '';
                                $time = '';
                                $start_time = '';
                                $end_time = '';
                                $opening_hours = explode(',', $opening_days);
                                foreach ($opening_hours as $opening_hour) {
                                    $array_hours = explode('-', $opening_hour);
                                    $day = $array_hours[0];
                                    $start_time = $array_hours[1];
                                    $end_time = $array_hours[2];
                                    $time = $start_time . ' - ' . $end_time;
                                    $open_days[] = array(
                                        'day' => $day,
                                        'time' => $time
                                    );
                                }

                                $result_hospital[] = array(
                                    'id' => $id,
                                    'hospital_name' => $hospital_name,
                                    'address' => $address,
                                    'rating' => $rating,
                                    'image' => $hospital_image,
                                    'opening_day' => $open_days
                                );
                            }
                        } else {
                            $result_hospital = array();
                        }
                        $service = '';
                        $result_services = '';
                        $doctor_services_query = $this->db->query("SELECT service FROM `doctor_services`");
                        foreach ($doctor_services_query->result_array() as $doctor_services) {
                            $service = $doctor_services['service'];
                            $result_services[] = array(
                                'service' => $service
                            );
                        }
                        $specialization = '';
                        $result_specialization = '';
                        $doctor_specialization_query = $this->db->query("SELECT specialization FROM `doctor_specialization`");
                        foreach ($doctor_specialization_query->result_array() as $doctor_specialization) {
                            $specialization = $doctor_specialization['specialization'];
                            $result_specialization[] = array(
                                'specialization' => $specialization
                            );
                        }


                        $resultpost[] = array(
                            'doctor_id' => $id,
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'about_us' => $about_us,
                            'address' => $address,
                            'telephone' => $telephone,
                            "exotel_no" => '02233721563',
                            'medical_college' => $medical_college,
                            'medical_affiliation' => $medical_affiliation,
                            'charitable_affiliation' => $charitable_affiliation,
                            'awards_recognition' => $awards_recognition,
                            'hrs_available' => $hrs_available,
                            'home_visit_available' => $home_visit_available,
                            'qualification' => $qualification,
                            'consultation_fee' => $consultation_fee,
                            'experience' => $experience,
                            'website' => $website,
                            'location' => $location,
                            'days' => $days,
                            'timing' => $timing,
                            'rating' => $rating,
                            'review' => $reviews,
                            'image' => $image,
                            'followers' => $followers,
                            'following' => $following,
                            'profile_views' => $profile_views,
                            'is_follow' => $is_follow,
                            'doctor_practices' => $result_hospital,
                            'doctor_services' => $result_services,
                            'doctor_specialization' => $result_specialization
                        );
                    }
                } else {
                    $resultpost = array();
                }
            }
            
            if($listing_type == '36')
                 {
           $sql = "SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,fcb.massage_service FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.user_id='$listing_id' AND fcb.id='$branch_id'";
        
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            $row = $query->row_array();
            
            $center_name = $row['branch_name'];
            $main_contact = $row['branch_phone'];
            $enquiry_number = '';
            $main_email = $row['branch_email'];
            $main_image = $row['branch_image'];
            $listing_type = '6';
            $total_rating = '4.5';
            $total_review = '0';
            $total_profile_views = '0';
            
                
                $branch_id = $row['id'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $branch_name = $row['branch_name'];
                $branch_image = $row['branch_image'];
                $branch_phone = $row['branch_phone'];
                $branch_email = $row['branch_email'];
                $about_branch = $row['about_branch'];
                $branch_business_category = $row['branch_business_category'];
                $branch_offer = $row['branch_offer'];
                $branch_facilities = $row['branch_facilities'];
                $branch_address = $row['branch_address'];
                $opening_hours = $row['opening_hours'];
                $pincode = $row['pincode'];
                $state = $row['state'];
                $city = $row['city'];
                
                //  $is_free_trail = $row['is_free_trail'];
                //  $from_trail_time = $row['from_trail_time'];
                //  $to_trail_time = $row['to_trail_time'];
                $listing_id = $row['user_id'];
                $listing_type = '36';
                $rating = '4.5';
                $reviews = '0';
                $profile_views =  '0' ;
                $user_discount = $row['user_discount'];

                if ($branch_image != '') {
                    $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                } else {
                    $branch_image = '';
                }

                $gallery_query = $this->db->query("SELECT `id`,`media_name`, `type` FROM `fitness_gallery` WHERE `branch_id`='$branch_id' AND `fitness_center_id`= '$listing_id' ");
                $gallery_array = array();
                $gallery_count = $gallery_query->num_rows();
                if ($gallery_count > 0) {
                    foreach ($gallery_query->result_array() as $rows) {
                        $media_name = $rows['media_name'];
                        $type = $rows['type'];
                        $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $type . '/' . $media_name;

                        $gallery_array[] = array(
                            "image" => $gallery,
                            "type" => $type
                        );
                    }
                }


                $open_days = '';
                $day_array_list = '';
                $day_list = '';
                $day_time_list = '';
                $time_list1 = '';
                $time_list2 = '';
                $time = '';
                $system_start_time = '';
                $system_end_time = '';
                $time_check = '';
                $current_time = '';
                $open_close = array();
                $time = array();
                date_default_timezone_set('Asia/Kolkata');
                $data = array();
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

                                    $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                                    $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                                    $current_time = date('h:i A');

                                    $date1 = DateTime::createFromFormat('H:i a', $current_time);
                                    $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                                    $date3 = DateTime::createFromFormat('H:i a', $system_end_time);

                                    if ($date2 < $date3 && $date1 <= $date3) {
                                        $date3->modify('+1 day')->format('H:i a');
                                    } elseif ($date2 > $date3 && $date1 >= $date3) {
                                        $date3->modify('+1 day')->format('H:i a');
                                    }

                                    if ($date1 > $date2 && $date1 < $date3) {
                                        $open_close[] = 'open';
                                    } else {
                                        $open_close[] = 'closed';
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
            $current_day_final="";
            foreach($final_Day as $key => $product)
            {
            
            if($product['day']===$dfinal)
            {
             $current_day_final = $product['time'][0];
            }
            }    
                $what_we_offer_array = array();
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    //echo "SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'";
                    if($what_we_offer != ""){
                        if(preg_match('/[0-9]/', $what_we_offer)){
                            $Query = $this->db->query("SELECT * FROM spa_services WHERE id='$what_we_offer'");
                            $what_we_offer_value = $Query->row()->services;
                            $what_we_offer_array[] = $what_we_offer_value;
                        }else{
                            $what_we_offer_array[] = $what_we_offer;
                        }
                        
                    }
                    
                }

                $facilities_array = array();
                $facilities = explode(',', $branch_facilities);
                foreach ($facilities as $facilities) {
                    if($what_we_offer != "" && preg_match('/[0-9]/', $facilities)){
                        if(preg_match('/[0-9]/', $facilities)){
                            $Query = $this->db->query("SELECT * FROM spa_facilites WHERE f_id='$facilities'");
                            $facilities_val = $Query->row()->facilities;
                            $facilities_array[] = $facilities_val;
                            
                        }
                        else{
                            $facilities_array[] = $facilities;
                        }
                    }
                }
                
                $branch_business_category_array = array();
                $branch_business = explode(',', $branch_business_category);
               
                foreach ($branch_business as $facilities) {
                    if($facilities !="")
                    {
                            $Query = $this->db->query("SELECT category,id FROM `business_category` WHERE `category_id` = 36 AND id='$facilities'");
                            $facilities_val = $Query->row()->category;
                            $branch_business_category_array[] = $facilities_val;
                    }
                    
                }

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                
                $package_list = array();
                $package_query = $this->db->query("SELECT * FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' order by id asc");
                      $package_count = $package_query->num_rows();
                    if ($package_count > 0) {
                        foreach ($package_query->result_array() as $package_row) {
                            $package_id = $package_row['id'];
                            $package_name = $package_row['package_name'];
                            $package_details = $package_row['package_details'];
                            $price = $package_row['price'];
                            $discount = $package_row['discount'];
                            $image = $package_row['image'];
                            $package_times = $package_row['package_times'];
                            $delivery_type = $package_row['deliverytype'];
                            $categ_id = $package_row['categ_id'];
                            $spa_cat = $package_row['spa_package_category'];
                            
                            $types = array();
                            if(!empty($categ_id))
                            {
                                $exp=explode(',',$categ_id);
                                for($i=0;$i<count($exp);$i++)
                                {
                                    $c_id = $exp[$i];
                                    $query = $this->db->query("SELECT * FROM business_category WHERE id='$c_id'");
                                    $count = $query->num_rows();
                                    if ($count > 0) {
                                        $rowss=$query->row_array();
                                        $cat_id = $rowss['id'];
                                        $category = $rowss['category'];
                                        
                                        $types[] = array('type_id' =>$cat_id,'type' => $category); 
                                    }
                                }
                            }
                            
                            if($package_times == NULL)
                            {
                                $package_times ="";
                            }
                            
                            $imgs = array();
                            $packi_query = $this->db->query("SELECT * FROM spa_package_img WHERE package_id='$package_id'");
                            $count_im = $packi_query->num_rows();
                            if ($count_im > 0) {
                                foreach ($packi_query->result_array() as $row_p) {
                                    $packi_id = $row_p['id'];
                                    $package_image = $row_p['image'];
                                    
                                    $imgs[] = array('image' => 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' .$package_image);
                                }
                            }
                            
                            $categorys = array();
                            if(!empty($spa_cat))
                            {
                                $exp1=explode(',',$spa_cat);
                                for($i=0;$i<count($exp1);$i++)
                                {
                                    $c_id1 = $exp1[$i];
                                    $query1 = $this->db->query("SELECT * FROM spa_category WHERE id='$c_id1'");
                                    $count1 = $query1->num_rows();
                                    if ($count1 > 0) {
                                        $rowss1=$query1->row_array();
                                        $cat_id1 = $rowss1['id'];
                                        $category1 = $rowss1['category'];
                                        
                                        $categorys[] = array('cat_id' =>$cat_id1,'category' => $category1); 
                                    }
                                }
                            }
                            
                            $package_list[] = array(
                                'package_id' => $package_id,
                                'package_name' => $package_name,
                                'package_details' => $package_details,
                                'price' => $price,
                                'discount' => $discount,
                                'package_image' => $imgs,
                                'package_times' => $package_times,
                                'delivery_type' => $delivery_type,
                                'types'      => $types,
                                'category'  => $categorys
                            );
                    }
                } else {
                    $package_list = array();
                }

                  $spa_views = $this->db->select('id')->from('spa_center_review')->where('listing_id', $listing_id)->get()->num_rows();
                $reviews = $this->db->select('id')->from('fitness_center_review')->where('branch_id', $branch_id)->get()->num_rows();

                  $sub_array = array();
            $personal_trainer = $this->db->select('*')->from('personal_trainers')->where('user_id', $listing_id)->get();
            $count = $personal_trainer->num_rows();
            if ($count > 0) 
            {
                foreach ($personal_trainer->result_array() as $row1) 
                {
                    $id1 = $row1['id'];
                    $category_id1 = $row1['category_id'];
                    $cat1 = explode(',',$category_id1);
                    $category_name1 = array();
                    for($i=0;$i<count($cat1);$i++)
                    {
                        if(!empty($cat1[$i]))
                        {
                        $sql = $this->db->select('category')->from('business_category')->where('id', $cat1[$i])->get()->row()->category;
                        $category_name1[]=$sql;
                        }
                    }
                    
                    $manager_name1 = $row1['manager_name'];
                    $address1 = $row1['address'];
                    $pincode1 = $row1['pincode'];
                    $contact1 = $row1['contact'];
                    $city1 = $row1['city'];
                    $state1 = $row1['state'];
                    $email1 = $row1['email'];
                    $qualifications1 = $row1['qualifications'];
                    $trainer_opening_hours1 = $row1['opening_hours'];
                    $experience1 = $row1['experience'];
                    $fitness_trainer_pic1 = $row1['fitness_trainer_pic'];
                    $gender1 = $row1['gender'];
                   
                    $final_session1=array();
                    $session1 = array();
                    $session2 = array();
                  /*   $session1[] = array(
                                'id'=>'1',
                                'package_name' => 'zumba',
                                'package_details' => 'Details about the package',
                                'price'=>'1,500',
                                'image'=> 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image
                            );
                         
                           $session1[] = array(
                                'id'=>'2',
                                'package_name' => 'zumba',
                                'package_details' => 'Details about the package1',
                                'price'=>'1,500',
                                'image'=> 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image
                            );
                         */
                           
                         $final_session1=  array_merge($session1,$session2);
                         
                         
                     $branch_final_Day1      = array();
                        $day_array_list_branch1 = explode('|', $trainer_opening_hours1);
                        if (count($day_array_list_branch1) > 1) {
                            for ($i = 0; $i < count($day_array_list_branch1); $i++) {
                                $day_list1 = explode('>', $day_array_list_branch1[$i]);
                                for ($j = 0; $j < count($day_list1); $j++) {
                                    $day_time_list1 = explode('-', $day_list1[$j]);
                                    for ($k = 1; $k < count($day_time_list1); $k++) {
                                        $time_list11 = explode(',', $day_time_list1[0]);
                                        $time_list21 = explode(',', $day_time_list1[1]);
                                        $time1       = array();
                                        $open_close1 = array();
                                        for ($l = 0; $l < count($time_list11); $l++) {
                                            $time_check1        = $time_list11[$l] . '-' . $time_list21[$l];
                                            $time1[]            = str_replace('close-close', 'close', $time_check1);
                                            $system_start_time1 = date("H.i", strtotime($time_list11[$l]));
                                            $system_end_time1   = date("H.i", strtotime($time_list21[$l]));
                                            $current_time1      = date('H.i');
                                            if ($current_time1 > $system_start_time1 && $current_time1 < $system_end_time1) {
                                                $open_close1[] = 'open';
                                            } else {
                                                $open_close1[] = 'close';
                                            }
                                        }
                                    }
                                }
                                $branch_final_Day1[] = array(
                                    'day' => $day_list1[0],
                                    'time' => $time1,
                                    'status' => $open_close1
                                );
                            }
                        } else {
                            $branch_final_Day1[] = array(
                                'day' => 'close',
                                'time' => array(),
                                'status' => array()
                            );
                        }
                    $sub_array1[] = array('id'=> $id1,
                                       'category' => $category_name1,
                                       'manager_name' => $manager_name1,
                                       'address' => $address1,
                                       'pincode' => $pincode1,
                                       'city' => $city1,
                                       'state' => $state1,
                                       'email' => $email1,
                                       'contact' => $contact1,
                                       'qualifications' => $qualifications1,
                                       'experience' => $experience1,
                                       'fitness_trainer_pic' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$fitness_trainer_pic1,
                                       'gender' => $gender1,
                                        'rating' => $rating
                                       /*'package_trainer' => $final_session1*/
                                     );
                                       //'opening_hours' => $branch_final_Day);
                    
                }
                    
            }
            else {
               $sub_array1=array();
            }


                //$distances = str_replace(',', '.', GetDrivingDistance($latitude, $lat, $longitude, $lng));
                $enquiry_number = "9619146163";
                $resultpost1[] = array(
                    'branch_id' => $branch_id,
                    'lat' => $lat,
                    'lng' => $lng,
                    'category_id' => $branch_business_category,
                    'listing_id' => $listing_id,
                    'listing_type' => $listing_type,
                    'about_us' => $about_branch,
                    'center_name' => $branch_name,
                    'branch_name' => $branch_name,
                    'address' => $branch_address,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'contact' => $branch_phone,
                    'exotel_no' => '02233721563',
                    'enquiry_number' => $enquiry_number,
                    'email' => $branch_email,
                    'image' => $branch_image,
                   // 'is_free_trail' => $is_free_trail,
                  //  'booking_slot_array' => $booking_slot_array,
                    'gallery' => $gallery_array,
                    'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    'therapist_list' => $sub_array1,
                    'speciality' => $branch_business_category_array,
                    'current_time'=>$current_day_final,
                    'opening_day' => $final_Day,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                   // 'is_trial_order' => $is_trial_order,
                    'profile_views' => $spa_views,
                    'user_discount' => $user_discount
                );
                
                 $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "center_name" => $center_name,
                    "main_contact" => $main_contact,
                    "main_email" => $main_email,
                    "main_image" => $main_image,
                    "total_rating" => $total_rating,
                    "total_review" => $total_review,
                    "total_profile_views" => $total_profile_views,
                    "other_branch" => $resultpost1
                );
            
        } 
        else
        {
            $resultpost = array();
        }
        }
          
              if($listing_type == '39')
              {
                      $sql = sprintf("SELECT hub_user_id,dentists_branch_user_id, name_of_hospital,about_us, establishment_year, speciality, address, address_2, pincode, phone, city, state, 	email, 	lat, lng, image, rating, review, medicalwale_discount, description FROM dentists_branch  WHERE is_active='1' AND dentists_branch_user_id='$listing_id'");
                       // $sql = "SELECT * FROM dentists_clinic_list  WHERE user_id='$listing_id' ";
                      $query = $this->db->query($sql);
                       $count = $query->num_rows();
           
           if($count>0)
           {
               foreach($query->result_array() as $row)
               {
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
                   $rating                  = $row['rating'];
                   $review                  = $row['review'];
                   $user_discount           = $row['medicalwale_discount'];
                   $discount_description    = $row['description'];
                     if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $image = '';
                }
                   
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
                    //echo "SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'";
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
                    
                   
                   $resultpost[] = array(
                       'hub_user_id'      => $hub_user_id,
                       'listing_id'       => $dentists_branch_user_id,
                       'listing_type'     => '39',
                       'name_of_clinic'   => $name_of_hospital,
                       'about_us'         => $about_us,
                       'establishment_year' => $establishment_year,
                       'speciality'       => $speciallity_array,
                       'address'          => $address,
                       'address_2'        => $address_2,
                       'pincode'          => $pincode,
                       'phone'            => $phone,
                       'city'             => $city,
                       'state'            => $state,
                       'email'            => $email,
                       'lat'              => $lat,
                       'lng'              => $lng,
                       'image'            => $image,
                       'rating'           => $rating,
                       'review'           => '0',
                       'user_discount'    => $user_discount,
                       'discount_description'  => $discount_description
                       );
               }
           }
           else
           {
                $resultpost = array();
           }
           
              }
              
              if($listing_type == '50')
              {
                $query1 = $this->db->query("SELECT hd.*,h.name_of_hospital from hospital_doctor_list as hd LEFT JOIN hospitals as h on (h.user_id = hd.hospital_id ) WHERE hd.id='$listing_id'");
                $count1 = $query1->num_rows();
                if ($count1 > 0) {
                    $row = $query1->row_array();
                    $id = $row['id'];
                    $doctor_name = $row['doctor_name'];
                    $about_us = $row['about_us'];
                    $speciality = $row['category']; //changed by deepak
                    $address = $row['address'];
                    $telephone = $row['phone'];
                    $medical_college = $row['medical_college'];
                    $medical_affiliation ="";
                    $charitable_affiliation = "";
                    $awards_recognition = "";
                    $hrs_available = "";
                    $home_visit_available = "";
                    $qualification = $row['qualifications'];
                    $consultation_fee = $row['consultation'];
                    $experience = $row['experience'];
                    $website = "";
                    $location = $row['map_location'];
                    $time = $row['appointment_time'];
                    $timing = $row['timing'];
                    $rating ="";
                    $reviews = "";
                    $doc_image = $row['profile_img'];
                    $doctor_user_id = $row['id'];
                    $discount="0";
                    $profile_views = '0';
                    $hospital_id =$row['hospital_id'];

                    if ($doc_image != '') {
                        $doc_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doc_image;
                    } else {
                        $doc_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }

                    $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                    $following = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();

                    $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $doctor_user_id)->get()->num_rows();

                    if ($is_follow > 0) {
                        $is_follow = 'Yes';
                    } else {
                        $is_follow = 'No';
                    }



                    $result_hospital = '';
      $hospital_query = $this->db->query("SELECT * FROM hospitals WHERE user_id='$hospital_id' ORDER BY id desc ");
        
        

        $count = $hospital_query->num_rows();
        // echo $count; die(); 
        if ($count > 0) {
            foreach ($hospital_query->result_array() as $row) {
               
                $clinic_name = $row['name_of_hospital'];
                $address = $row['address'];
                $state = $row['state'];
                $city = $row['city'];
                $pincode = $row['pincode'];
                $map_location = $row['map_location'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $image = $row['image'];
                $consultation_charges = $consultation_fee;
                $contact_no = $row['phone'];
                $appointment_time =$time; 
                $opening_hours = $timing;
                
       
                                           
                $discount_type = 0;
                $discount_limit = 0;
                $discount_cat = "";
           
                
                
                
                
                $open_days = '';
                $day_array_list = '';
                $day_list = '';
                $day_time_list = '';
                $time_list1 = '';
                $time_list2 = '';
                $time = '';
                $system_start_time = '';
                $system_end_time = '';
                $time_check = '';
                $current_time = '';
                $open_close = array();
                $time = array();
                date_default_timezone_set('Asia/Kolkata');
                $data = array();
                
                
                
                
                
                //$doctor_id = $row['id'];
                //$clinic_id = $row['id'];
                // echo "doctor_id".$doctor_id."<br>";
                // echo "clinic_id".$clinic_id."<br>";
                $final_Day = array();
                $time_slots = array();
                
                $weekday ='Sunday';
                
                // echo $weekday;
                
               
                // echo $tomorrow;
                // die();
                for($i=0;$i<7;$i++){
                    
                     $weekday = date('l', strtotime($weekday.'+1 day'));
                
                $queryTiming = $this->db->query("SELECT * FROM `hospital_doctor_slot_details` WHERE `doctor_id` = '$doctor_user_id' AND `clinic_id` = '$hospital_id' AND `day` = '$weekday'" );
               
                $countTiming = $queryTiming->num_rows();
                
                // die();
             
                $time_array = array();
                $time_array1 = array();
                $time_array2 = array();
                $time_array3 = array();
                $time_slott = array();
                
                foreach($queryTiming->result_array() as $row1){
                // if($countTiming){
                   
                    $timeSlotDay = $row1['day'];
                    $timeSlot = $row1['time_slot'];
                    $from_time = $row1['from_time'];
                    $to_time = $row1['to_time'];
                    // echo $timeSlot;
                   
                     if ($timeSlot == 'Morning') {
        				$time_array[] = array(
        					'from_time' => $from_time,
        					'to_time' => $to_time
        				);
        				} else  if ($timeSlot == 'Afternoon') {
        				$time_array1[] = array(
        					'from_time' => $from_time,
        					'to_time' => $to_time
        				);
        				} else if ($timeSlot == 'Evening') {
        				$time_array3[] = array(
        					'from_time' => $from_time,
        					'to_time' => $to_time
        				);
        				} else if ($timeSlot == 'Night') {
        				$time_array2[] = array(
        					'from_time' => $from_time,
        					'to_time' => $to_time
        				);
        			}
        			
        			
                }
                
                $time_slott[] = array(
                    'time_slot'=> 'Morning',
        			'time' => $time_array
        		);
        		$time_slott[] = array(
        		    'time_slot'=> 'Afternoon',
        			'time' => $time_array1
        		);
        		
        		$time_slott[] = array(
        		    'time_slot'=> 'Evening',
        			'time' => $time_array3
        		);
        		$time_slott[] = array(
        		    'time_slot'=> 'Night',
        			'time' => $time_array2
        		);
        		
        		$time_slots[] = array(
        		    'day'=>$weekday,
                   'slots'=> $time_slott
                ); 
                
                }
              
                if ($row['image'] != '') {
                    $profile_pic = $row['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://medicalwale.s3.amazonaws.com/images/doctor_images/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }
               
                
                
                    $resultpostDetails['id'] = $hospital_id;
                    $resultpostDetails['clinic_name'] = $row['name_of_hospital'];
                    $resultpostDetails['address'] = $row['address'];
                    $resultpostDetails['state'] = $row['state'];
                    $resultpostDetails['city'] = $row['city'];
                    $resultpostDetails['pincode'] = $row['pincode'];
                    $resultpostDetails['map_location'] = $row['map_location'];
                    $resultpostDetails['lat'] = $row['lat'];
                    $resultpostDetails['lng'] = $row['lng'];
                    $resultpostDetails['image'] = $profile_pic;
                    $resultpostDetails['consultation_charges'] = $consultation_fee;
                    $resultpostDetails['contact_no'] = $row['phone'];
                    $resultpostDetails['time_slot'] = $appointment_time;
                    $resultpostDetails['timings'] = $time_slots;
                    $resultpostDetails['discount_type'] =  $discount_type;
                    $resultpostDetails['discount_limit'] = $discount_limit;
                    $resultpostDetails['discount_category'] = $discount_cat;
               
                    
               
                 $result_hospital[] = $resultpostDetails;
            }
            
            
        } else {
            $result_hospital = array();
        }
                    $service = '';
                    $result_services = '';
                    $doctor_services_query = $this->db->query("SELECT service FROM `doctor_services`");
                    foreach ($doctor_services_query->result_array() as $doctor_services) {
                        $service = $doctor_services['service'];
                        $result_services[] = array(
                            'service' => $service
                        );
                    }
                    $specialization = '';
                    $result_specialization = '';
                    $doctor_specialization_query = $this->db->query("SELECT specialization FROM `doctor_specialization`");
                    foreach ($doctor_specialization_query->result_array() as $doctor_specialization) {
                        $specialization = $doctor_specialization['specialization'];
                        $result_specialization[] = array(
                            'specialization' => $specialization
                        );
                    }

                    $resultpost[] = array(
                        'doctor_id' => $id,
                        'doctor_user_id' => $doctor_user_id,
                        'name' => $doctor_name,
                        'about_us' => $about_us,
                        'speciality' => $speciality,
                        'address' => $address,
                        'telephone' => $telephone,
                        "exotel_no" => '02233721563',
                        'medical_college' => $medical_college,
                        'medical_affiliation' => $medical_affiliation,
                        'charitable_affiliation' => $charitable_affiliation,
                        'awards_recognition' => $awards_recognition,
                        'hrs_available' => $hrs_available,
                        'home_visit_available' => $home_visit_available,
                        'qualification' => $qualification,
                        'consultation_fee' => $consultation_fee,
                        'experience' => $experience,
                        'website' => $website,
                        'location' => $location,
                        'days' => "",
                        'timing' => $timing,
                        'rating' => $rating,
                        'review' => $reviews,
                        'image' => $doc_image,
                        'followers' => $followers,
                        'following' => $following,
                        'profile_view' => $profile_views,
                        'is_follow' => $is_follow,
                        'doctor_practices' => $result_hospital,
                        'doctor_services' => $result_services,
                        'doctor_specialization' => $result_specialization,
                        'discount'=>$discount
                    );
                } else {
                    $resultpost = array();
                }
              }
            
        } 
        else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    public function image_list($user_id, $keyword) {
       // echo "SELECT id FROM `posts` WHERE description like '%$keyword%' and article_desc like '%$keyword%' order by description asc limit 10";
        
        $query = $this->db->query("SELECT posts.id,post_media.media_id FROM `posts` LEFT JOIN post_media ON(posts.id= post_media.post_id) WHERE (posts.description like '%$keyword%' or posts.article_desc like '%$keyword%') and post_media.media_id <>'' order by posts.description asc limit 10");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $id = $row['id'];
                echo "SELECT media_id FROM `post_media` WHERE post_id ='$id'";
                
                $query_to = $this->db->query("SELECT media_id FROM `post_media` WHERE post_id ='$id'");
                $count_ = $query_to->num_rows();
                if ($count_ > 0) {
                    foreach ($query_to->result_array() as $row_to) {
                        
                        $media_id = $row['id'];
                        $query_three = $this->db->query("SELECT * FROM `media` WHERE id ='$media_id'");
                        $count_three = $query_three->num_rows();
                        if ($count_three > 0) {
                            foreach ($query_three->result_array() as $row_three) {
                                $keyword = $row_three['source'];
                                $resultpost[] = array(
                                    'images' => $keyword
                                );
                            }
                        }
                    }
                }
                
                /*$resultpost[] = array(
                    'keyword' => $keyword
                );*/
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
     public function search_doctor($user_id, $keyword) {
        if ($user_id > 0) {
            $parts = explode(' ', $keyword);
           
            function GetDrivingDistance($lat1, $lat2, $long1, $long2)
        {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyCMHN__LczEziaYuKXQ-SKdiMfx5AiY66o&callback=initMap";
            $ch  = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
           
            $val=$response_a['rows'][0]['elements'][0]['status'];
           if($val!="ZERO_RESULTS")
           {
            $dist       = $response_a['rows'][0]['elements'][0]['distance']['text'];
           }
           else
           {
            $dist       ="";   
           }
            return $dist;
        }
           
            // Doctor
            $field1 = '';
            $field2 = '';
            $field3 = '';
            $index_id="doctor";
          $mlat="19.122269799999998";
        $mlng="72.8456284";
            $doctor=$this->elasticsearchdoctor($index_id,$keyword);
               
            if (!empty($doctor)) {
               
                foreach($doctor as $a){
                    
                  $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $a['lat'], $mlng, $a['lng']));  
                 $doctor1[] = array(
                                'listing_id' => $a['listing_id'],
                                'name' => $a['name'],
                                'image' => $a['image'],
                                'field1' => $a['field1'],
                                'field2' => $a['field2'],
                                'field3' => $a['field3'],
                                'distance'=>$distances
                            );
                }
             
                function array_sort_by_column($arr, $col, $dir = SORT_ASC)
        {
            $sort_col = array();
            foreach ($arr as $key => $row) {
                $sort_col[$key] = $row[$col];
            }
            array_multisort($sort_col, $dir, $arr);
        }
        array_sort_by_column($doctor1, "distance");
                
                
                $doctor_array[] = array(
                    'title' => 'Doctor',
                    'listing_type' => 5,
                    'array' => $doctor1
                );
                
                
            } else {
                $doctor_array = array();
            }
         
          $resultpost = $doctor_array;
        } else {
            $resultpost = array();
        }
         echo json_encode($resultpost);
         
         
     }
     
      public function search_select($user_id,$keyword,$page) {
          
          $limit = 10;
      
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
       
          
          $sub_seach_select=array();
        $query = $this->db->query("SELECT * FROM `search_select` where  parent_id ='0' order by type asc LIMIT $start, $limit");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $sub_seach_select=array();
                $id = $row['type'];
                $name = $row['name'];
                $query_to = $this->db->query("SELECT * FROM `search_select` WHERE parent_id ='$id' order by type asc");
                $count_ = $query_to->num_rows();
                if ($count_ > 0) {
                    foreach ($query_to->result_array() as $row_to) {
                        $s_id = $row_to['type'];
                        $s_name = $row_to['name'];
                        
                        $sub_seach_select[]=array('id'=>$s_id,'name'=>$s_name);
                            
                    }
                }
                
                $count=$this->search_select_count($user_id,$keyword,$id);
                $final_count=count($count);
                if($final_count > 0){
                   $data_aval='1'; 
                    
                }else{
                $data_aval='0';     
                    
                }
                
                $resultpost[]=array("id"=>$id,
                "name"=>$name,
                "image"=>"",
                "data_aval"=>$data_aval,
                "sub_type"=>$sub_seach_select);
  
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

}

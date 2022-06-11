<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class SearchModel extends CI_Model
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
    
    
    
    public function check_day_status($day_type, $days_closed, $is_24hrs_available, $store_open, $store_close)
    {
        
        
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
    
    
    
    
    public function check_current_delivery_charges($is_24hrs_available, $day_night_delivery, $free_start_time, $free_end_time, $night_delivery_charge, $days_closed, $is_min_order_delivery, $min_order)
    {
        
        
        $system_start_time = date("H.i", strtotime("-1 minutes", strtotime($free_start_time)));
        $system_end_time   = date("H.i", strtotime("-1 minutes", strtotime($free_end_time)));
        $current_time      = date('H.i');
        
        
        
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
                
            }
            
            else {
                
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
    
    
    
    public function check_time_format($time)
    {
        $time_filter = preg_replace('/\s+/', '', $time);
        $final_time  = date("h:i A", strtotime($time_filter));
        return $final_time;
    }
    
    
    
    public function keyword_list($user_id, $keyword)
    {
        $query = $this->db->query("SELECT name FROM `users` WHERE name like '%$keyword%' order by name asc limit 10");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $keyword      = $row['name'];
                $resultpost[] = array(
                    'keyword' => $keyword
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    public function search_list($user_id, $keyword)
    {
        if ($user_id > 0) {
            // People
            $field1       = '';
            $field2       = '';
            $field3       = '';
            $people_query = $this->db->query("SELECT id,name FROM `users` WHERE name like '%$keyword%' order by name asc limit 2");
            $people_count = $people_query->num_rows();
            if ($people_count > 0) {
                foreach ($people_query->result_array() as $people_row) {
                    $user_id      = $people_row['id'];
                    $name         = $people_row['name'];
                    $listing_type = '0';
                    $media_query  = $this->db->query("SELECT media.source FROM media LEFT JOIN users on users.avatar_id=media.id WHERE users.id='$user_id' limit 1");
                    $media_count  = $media_query->num_rows();
                    if ($media_count > 0) {
                        $media_row = $media_query->row_array();
                        $img_file  = $media_row['source'];
                        $image     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
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
            
            // Doctor
            $field1       = '';
            $field2       = '';
            $field3       = '';
            $doctor_query = $this->db->query("SELECT user_id,image,doctor_name,speciality,qualification,address FROM doctor_list WHERE doctor_name like '%$keyword%' limit 2");
            $doctor_count = $doctor_query->num_rows();
            if ($doctor_count > 0) {
                foreach ($doctor_query->result_array() as $doctor_row) {
                    $user_id      = $doctor_row['user_id'];
                    $name         = str_replace("null", "", $doctor_row['doctor_name']);
                    $field1       = str_replace("null", "", $doctor_row['speciality']);
                    $field2       = str_replace("null", "", $doctor_row['qualification']);
                    $field3       = str_replace("null", "", $doctor_row['address']);
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
            
            // Pharmacy
            $field1         = '';
            $field2         = '';
            $field3         = '';
            $pharmacy_query = $this->db->query("SELECT user_id,profile_pic,medical_name,address1 FROM medical_stores WHERE medical_name like '%$keyword%' limit 2");
            $pharmacy_count = $pharmacy_query->num_rows();
            if ($pharmacy_count > 0) {
                foreach ($pharmacy_query->result_array() as $pharmacy_row) {
                    $user_id        = $pharmacy_row['user_id'];
                    $name           = str_replace("null", "", $pharmacy_row['medical_name']);
                    $field1         = '';
                    $field2         = '';
                    $field3         = str_replace("null", "", $pharmacy_row['address1']);
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
            
            // Ayurveda
            $field1         = '';
            $field2         = '';
            $field3         = '';
            $ayurveda_query = $this->db->query("SELECT profile_pic,ayurveda_name,address1 FROM ayurveda WHERE ayurveda_name like '%$keyword%' limit 2");
            $ayurveda_count = $ayurveda_query->num_rows();
            if ($ayurveda_count > 0) {
                foreach ($ayurveda_query->result_array() as $ayurveda_row) {
                    $name           = str_replace("null", "", $ayurveda_row['ayurveda_name']);
                    $field1         = '';
                    $field2         = '';
                    $field3         = str_replace("null", "", $ayurveda_row['address1']);
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
            
            
            // Homeopathic
            $field1            = '';
            $field2            = '';
            $field3            = '';
            $homeopathic_query = $this->db->query("SELECT user_id,image,doctor_name,speciality,qualification,address FROM doctor_list WHERE doctor_name like '%$keyword%' limit 2");
            $homeopathic_count = $homeopathic_query->num_rows();
            if ($homeopathic_count > 0) {
                foreach ($homeopathic_query->result_array() as $homeopathic_row) {
                    $user_id           = $homeopathic_row['user_id'];
                    $name              = str_replace("null", "", $homeopathic_row['doctor_name']);
                    $field1            = str_replace("null", "", $homeopathic_row['speciality']);
                    $field2            = str_replace("null", "", $homeopathic_row['qualification']);
                    $field3            = str_replace("null", "", $homeopathic_row['address']);
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
                    'title' => 'Homeopathic',
                    'listing_type' => 9,
                    'array' => $homeopathic
                );
            } else {
                $homeopathic_array = array();
            }
            
            // Labs
            $field1     = '';
            $field2     = '';
            $field3     = '';
            $labs_query = $this->db->query("SELECT user_id,profile_pic,lab_name,address1 FROM lab_center WHERE lab_name like '%$keyword%' limit 2");
            $labs_count = $labs_query->num_rows();
            if ($labs_count > 0) {
                foreach ($labs_query->result_array() as $labs_row) {
                    $user_id    = $labs_row['user_id'];
                    $name       = str_replace("null", "", $labs_row['lab_name']);
                    $field1     = '';
                    $field2     = '';
                    $field3     = str_replace("null", "", $labs_row['address1']);
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
                    'title' => 'Labs',
                    'listing_type' => 10,
                    'array' => $labs
                );
            } else {
                $labs_array = array();
            }
            
            // Nursing Attendant
            $field1        = '';
            $field2        = '';
            $field3        = '';
            $nursing_query = $this->db->query("SELECT user_id,name,address,image FROM nursing_attendant WHERE name like '%$keyword%' limit 2");
            $nursing_count = $nursing_query->num_rows();
            if ($nursing_count > 0) {
                foreach ($nursing_query->result_array() as $nursing_row) {
                    $user_id       = $nursing_row['user_id'];
                    $name          = str_replace("null", "", $nursing_row['name']);
                    $field1        = '';
                    $field2        = '';
                    $field3        = str_replace("null", "", $nursing_row['address']);
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
                    'title' => 'Nursing',
                    'listing_type' => 12,
                    'array' => $nursing
                );
            } else {
                $nursing_array = array();
            }
            
            
            // Cupping
            $field1        = '';
            $field2        = '';
            $field3        = '';
            $cupping_query = $this->db->query("SELECT user_id,name,address,image FROM cuppingtherapy WHERE name like '%$keyword%' limit 2");
            $cupping_count = $cupping_query->num_rows();
            if ($cupping_count > 0) {
                foreach ($cupping_query->result_array() as $cupping_row) {
                    $user_id       = $cupping_row['user_id'];
                    $name          = str_replace("null", "", $cupping_row['name']);
                    $field1        = '';
                    $field2        = '';
                    $field3        = str_replace("null", "", $cupping_row['address']);
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
                    'title' => 'Cupping',
                    'listing_type' => 16,
                    'array' => $cupping
                );
            } else {
                $cupping_array = array();
            }
            
            // Physiotherapist
            $field1                = '';
            $field2                = '';
            $field3                = '';
            $physiotherapist_query = $this->db->query("SELECT user_id,image,doctor_name,speciality,qualification,address FROM doctor_list WHERE doctor_name like '%$keyword%' limit 2");
            $physiotherapist_count = $physiotherapist_query->num_rows();
            if ($physiotherapist_count > 0) {
                foreach ($physiotherapist_query->result_array() as $physiotherapist_row) {
                    $user_id               = $physiotherapist_row['user_id'];
                    $name                  = str_replace("null", "", $physiotherapist_row['doctor_name']);
                    $field1                = str_replace("null", "", $physiotherapist_row['speciality']);
                    $field2                = str_replace("null", "", $physiotherapist_row['qualification']);
                    $field3                = str_replace("null", "", $physiotherapist_row['address']);
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
                    'title' => 'Physiotherapist',
                    'listing_type' => 20,
                    'array' => $physiotherapist
                );
            } else {
                $physiotherapist_array = array();
            }
            
            // Fitness Center
            $field1        = '';
            $field2        = '';
            $field3        = '';
            $fitness_query = $this->db->query("SELECT user_id,image,center_name,address FROM fitness_center WHERE center_name like '%$keyword%' limit 2");
            $fitness_count = $fitness_query->num_rows();
            if ($fitness_count > 0) {
                foreach ($fitness_query->result_array() as $fitness_row) {
                    $user_id       = $fitness_row['user_id'];
                    $name          = str_replace("null", "", $fitness_row['center_name']);
                    $field1        = '';
                    $field2        = '';
                    $field3        = str_replace("null", "", $fitness_row['address']);
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
                    'title' => 'Fitness',
                    'listing_type' => 6,
                    'array' => $fitness
                );
            } else {
                $fitness_array = array();
            }
            
            // Hospital
            $field1         = '';
            $field2         = '';
            $field3         = '';
            $hospital_query = $this->db->query("SELECT user_id,image,name_of_hospital,address FROM hospitals WHERE name_of_hospital like '%$keyword%' limit 2");
            $hospital_count = $hospital_query->num_rows();
            if ($hospital_count > 0) {
                foreach ($hospital_query->result_array() as $hospital_row) {
                    $user_id        = $hospital_row['user_id'];
                    $name           = str_replace("null", "", $hospital_row['name_of_hospital']);
                    $field1         = '';
                    $field2         = '';
                    $field3         = str_replace("null", "", $hospital_row['address']);
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
                    'title' => 'Hospital',
                    'listing_type' => 8,
                    'array' => $hospital
                );
            } else {
                $hospital_array = array();
            }
            
            // Post
            function get_time_difference_php($created_time)
            {
                date_default_timezone_set('Asia/Calcutta');
                $str            = strtotime($created_time);
                $today          = strtotime(date('Y-m-d H:i:s'));
                $time_differnce = $today - $str;
                $years          = 60 * 60 * 24 * 365;
                $months         = 60 * 60 * 24 * 30;
                $days           = 60 * 60 * 24;
                $hours          = 60 * 60;
                $minutes        = 60;
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
            
            $media_array      = array();
            $query            = $this->db->query("select posts.id as post_id,posts.type as post_type,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id from posts INNER JOIN users on users.id=posts.user_id where posts.user_id<>'' and posts.user_id<>'0' order by posts.id desc limit 2");
            $healthwall_count = $query->num_rows();
            if ($healthwall_count > 0) {
                foreach ($query->result_array() as $row) {
                    $post_id      = $row['post_id'];
                    $listing_type = $row['vendor_id'];
                    $post_id      = $row['post_id'];
                    $post         = $row['post'];
                    $category     = '';
                    $is_anonymous = $row['is_anonymous'];
                    $tag          = $row['tag'];
                    $post_type    = $row['post_type'];
                    $date         = $row['created_at'];
                    $caption      = $row['caption'];
                    $username     = $row['name'];
                    $post_user_id = $row['post_user_id'];
                    
                    $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
                    
                    if ($img_count > 0) {
                        $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                        $img_file      = $profile_query->source;
                        $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                    } else {
                        $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                    
                    $media_query  = $this->db->query("SELECT media.id AS media_id,media.type AS media_type,media.caption,media.source,post_media.img_width,post_media.img_height,post_media.video_width,post_media.video_height FROM media INNER JOIN post_media on media.id=post_media.media_id where post_media.post_id='$post_id'");
                    $img_val      = '';
                    $images       = '';
                    $img_comma    = '';
                    $img_width    = '';
                    $img_height   = '';
                    $video_width  = '';
                    $video_height = '';
                    foreach ($media_query->result_array() as $media_row) {
                        $media_id     = $media_row['media_id'];
                        $media_type   = $media_row['media_type'];
                        $source       = $media_row['source'];
                        $images       = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/' . $source;
                        $caption      = $media_row['caption'];
                        $img_width    = $media_row['img_width'];
                        $img_height   = $media_row['img_height'];
                        $video_width  = $media_row['video_width'];
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
                    
                    $like_count    = $this->db->select('id')->from('post_likes')->where('post_id', $post_id)->get()->num_rows();
                    $comment_count = $this->db->select('id')->from('comments')->where('post_id', $post_id)->get()->num_rows();
                    $follow_count  = $this->db->select('id')->from('post_follows')->where('post_id', $post_id)->get()->num_rows();
                    $view_count    = $this->db->select('id')->from('post_views')->where('post_id', $post_id)->get()->num_rows();
                    
                    $like_yes_no_query = $this->db->query("SELECT id from post_likes where post_id='$post_id' and user_id='$user_id'");
                    $like_yes_no       = $like_yes_no_query->num_rows();
                    
                    $follow_yes_no_query = $this->db->query("SELECT id from post_follows where post_id='$post_id' and user_id='$user_id'");
                    $follow_post_yes_no  = $follow_yes_no_query->num_rows();
                    
                    $view_yes_no_query = $this->db->query("SELECT id from post_views where post_id='$post_id' and user_id='$user_id'");
                    $view_post_yes_no  = $view_yes_no_query->num_rows();
                    
                    $date              = get_time_difference_php($date);
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
            } else {
                $healthwall_array = array();
            }
            
            
            
            // Articles
            $query = $this->db->query("SELECT `id`, `cat_id`, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date` FROM article limit 2");
            
            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $article_id          = $row['id'];
                    $cat_id              = $row['cat_id'];
                    $article_title       = $row['article_title'];
                    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                    $article_image       = $row['image'];
                    $article_date        = $row['posted'];
                    $author              = 'Medicalwale.com';
                    $article_image       = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;
                    
                    $like_count  = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
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
            
            
            // Survivor's Stories
            $survivor_query = $this->db->query("SELECT id,title,description,tag,author,image,date FROM `survival_stories` where is_active='1' order by id desc limit 2");
            $survivor_count = $survivor_query->num_rows();
            if ($survivor_count > 0) {
                foreach ($survivor_query->result_array() as $row) {
                    $id          = $row['id'];
                    $title       = $row['title'];
                    $description = $row['description'];
                    $tag         = $row['tag'];
                    $author      = $row['author'];
                    $image       = $row['image'];
                    $date        = $row['date'];
                    $image       = str_replace(" ", "", $image);
                    $image       = 'https://d2c8oti4is0ms3.cloudfront.net/images/survival_story_images/' . $image;
                    
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
                    'array' => $survivor_list
                );
            } else {
                $survivor_array = array();
            }
            
            $resultpost = array_merge($people_array, $doctor_array, $pharmacy_array, $ayurveda_array, $homeopathic_array, $labs_array, $nursing_array, $cupping_array, $physiotherapist_array, $fitness_array, $hospital_array, $healthwall_array, $article_array, $survivor_array);
            
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    public function page_list($user_id, $keyword, $listing_type, $page)
    {
        if ($user_id > 0) {
            $limit = 10;
            $start = 0;
            if ($page > 0) {
                if (!is_numeric($page)) {
                    $page = 1;
                }
            }
            $start  = ($page - 1) * $limit;
            $field1 = '';
            $field2 = '';
            $field3 = '';
            
            if ($listing_type == '0') {
                // People
                $people_query = $this->db->query("SELECT id,name FROM `users` WHERE name like '%$keyword%' order by name asc limit $start, $limit");
                $people_count = $people_query->num_rows();
                if ($people_count > 0) {
                    foreach ($people_query->result_array() as $people_row) {
                        $user_id      = $people_row['id'];
                        $name         = $people_row['name'];
                        $listing_type = '0';
                        $media_query  = $this->db->query("SELECT media.source FROM media LEFT JOIN users on users.avatar_id=media.id WHERE users.id='$user_id' limit 1");
                        $media_count  = $media_query->num_rows();
                        if ($media_count > 0) {
                            $media_row = $media_query->row_array();
                            $img_file  = $media_row['source'];
                            $image     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
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
                        $user_id      = $doctor_row['user_id'];
                        $name         = str_replace("null", "", $doctor_row['doctor_name']);
                        $field1       = str_replace("null", "", $doctor_row['speciality']);
                        $field2       = str_replace("null", "", $doctor_row['qualification']);
                        $field3       = str_replace("null", "", $doctor_row['address']);
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
                        $user_id        = $pharmacy_row['user_id'];
                        $name           = str_replace("null", "", $pharmacy_row['medical_name']);
                        $field1         = '';
                        $field2         = '';
                        $field3         = str_replace("null", "", $pharmacy_row['address1']);
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
                        $name           = str_replace("null", "", $ayurveda_row['medical_name']);
                        $field1         = '';
                        $field2         = '';
                        $field3         = str_replace("null", "", $ayurveda_row['address1']);
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
                        $user_id           = $homeopathic_row['user_id'];
                        $name              = str_replace("null", "", $homeopathic_row['doctor_name']);
                        $field1            = str_replace("null", "", $homeopathic_row['speciality']);
                        $field2            = str_replace("null", "", $homeopathic_row['qualification']);
                        $field3            = str_replace("null", "", $homeopathic_row['address']);
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
                        $user_id    = $labs_row['user_id'];
                        $name       = str_replace("null", "", $labs_row['lab_name']);
                        $field1     = '';
                        $field2     = '';
                        $field3     = str_replace("null", "", $labs_row['address1']);
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
                        $user_id       = $nursing_row['user_id'];
                        $name          = str_replace("null", "", $nursing_row['name']);
                        $field1        = '';
                        $field2        = '';
                        $field3        = str_replace("null", "", $nursing_row['address']);
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
                        $user_id       = $cupping_row['user_id'];
                        $name          = str_replace("null", "", $cupping_row['name']);
                        $field1        = '';
                        $field2        = '';
                        $field3        = str_replace("null", "", $cupping_row['address']);
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
                        $user_id               = $physiotherapist_row['user_id'];
                        $name                  = str_replace("null", "", $physiotherapist_row['doctor_name']);
                        $field1                = str_replace("null", "", $physiotherapist_row['speciality']);
                        $field2                = str_replace("null", "", $physiotherapist_row['qualification']);
                        $field3                = str_replace("null", "", $physiotherapist_row['address']);
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
                        $user_id       = $fitness_row['user_id'];
                        $name          = str_replace("null", "", $fitness_row['center_name']);
                        $field1        = '';
                        $field2        = '';
                        $field3        = str_replace("null", "", $fitness_row['address']);
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
                        $user_id        = $hospital_row['user_id'];
                        $name           = str_replace("null", "", $hospital_row['name_of_hospital']);
                        $field1         = '';
                        $field2         = '';
                        $field3         = str_replace("null", "", $hospital_row['address']);
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
    
    public function profile_details($user_id, $listing_id, $listing_type)
    {
        if ($listing_id > 0) {
            if ($listing_type == '0') {
                // People
                $people_query = $this->db->query("SELECT id,name,phone,email,gender,dob FROM `users` WHERE id='$listing_id' limit 1");
                $people_count = $people_query->num_rows();
                if ($people_count > 0) {
                    $user_row  = $people_query->row_array();
                    $id        = $user_row['id'];
                    $name      = $user_row['name'];
                    $phone     = $user_row['phone'];
                    $email     = $user_row['email'];
                    $gender    = $user_row['gender'];
                    $dob       = $user_row['dob'];
                    $img_count = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->num_rows();
                    
                    if ($img_count > 0) {
                        $media    = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->row();
                        $img_file = $media->source;
                        $image    = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
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
                $query = $this->db->query("SELECT id,lat,lng,category,user_id,doctor_name,about_us,speciality,address,telephone,medical_college,medical_affiliation,charitable_affiliation,awards_recognition,all_24_hrs_available,home_visit_available,qualification,consultation_fee,experience,website,location,days,timing,image,rating,review FROM doctor_list WHERE user_id='$listing_id' limit 1");
                
                $count = $query->num_rows();
                if ($count > 0) {
                    $row                    = $query->row_array();
                    $id                     = $row['id'];
                    $doctor_name            = $row['doctor_name'];
                    $about_us               = $row['about_us'];
                    $speciality             = $row['category']; //changed by deepak
                    $address                = $row['address'];
                    $telephone              = $row['telephone'];
                    $medical_college        = $row['medical_college'];
                    $medical_affiliation    = $row['medical_affiliation'];
                    $charitable_affiliation = $row['charitable_affiliation'];
                    $awards_recognition     = $row['awards_recognition'];
                    $hrs_available          = $row['all_24_hrs_available'];
                    $home_visit_available   = $row['home_visit_available'];
                    $qualification          = $row['qualification'];
                    $consultation_fee       = $row['consultation_fee'];
                    $experience             = $row['experience'];
                    $website                = $row['website'];
                    $location               = $row['location'];
                    $days                   = $row['days'];
                    $timing                 = $row['timing'];
                    $rating                 = $row['rating'];
                    $reviews                = $row['review'];
                    $image                  = $row['image'];
                    $doctor_user_id         = $row['user_id'];
                    $profile_views          = '0';
                    
                    
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
                            $id             = $hospital_row['hospital_id'];
                            $hospital_name  = $hospital_row['name_of_hospital'];
                            $address        = $hospital_row['address'];
                            $rating         = $hospital_row['rating'];
                            $hospital_image = $hospital_row['image'];
                            $opening_days   = $hospital_row['opening_days'];
                            if ($hospital_image != '') {
                                $hospital_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $hospital_image;
                            } else {
                                $hospital_image = '';
                            }
                            
                            
                            date_default_timezone_set('Asia/Kolkata');
                            $open_days     = '';
                            $day           = '';
                            $time          = '';
                            $start_time    = '';
                            $end_time      = '';
                            $opening_hours = explode(',', $opening_days);
                            foreach ($opening_hours as $opening_hour) {
                                $array_hours = explode('-', $opening_hour);
                                $day         = $array_hours[0];
                                $start_time  = $array_hours[1];
                                $end_time    = $array_hours[2];
                                $time        = $start_time . ' - ' . $end_time;
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
                    $service               = '';
                    $result_services       = '';
                    $doctor_services_query = $this->db->query("SELECT service FROM `doctor_services`");
                    foreach ($doctor_services_query->result_array() as $doctor_services) {
                        $service           = $doctor_services['service'];
                        $result_services[] = array(
                            'service' => $service
                        );
                    }
                    $specialization              = '';
                    $result_specialization       = '';
                    $doctor_specialization_query = $this->db->query("SELECT specialization FROM `doctor_specialization`");
                    foreach ($doctor_specialization_query->result_array() as $doctor_specialization) {
                        $specialization          = $doctor_specialization['specialization'];
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
                } else {
                    $resultpost = array();
                }
            }
            if ($listing_type == '13') {
                // Pharmacy
                $query = $this->db->query("SELECT `id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`, `is_min_order_delivery`,`min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,IFNULL(online_offline,'') AS online_offline FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND user_id='$listing_id'");
                
                $count = $query->num_rows();
                if ($count > 0) {
                    $row                = $query->row_array();
                    $lat                = $row['lat'];
                    $lng                = $row['lng'];
                    $medical_id         = $row['user_id'];
                    $medical_name       = $row['medical_name'];
                    $store_manager      = $row['store_manager'];
                    $address1           = $row['address1'];
                    $address2           = $row['address2'];
                    $pincode            = $row['pincode'];
                    $city               = $row['city'];
                    $state              = $row['state'];
                    $contact_no         = $row['contact_no'];
                    $whatsapp_no        = $row['whatsapp_no'];
                    $email              = $row['email'];
                    $store_since        = $row['store_since'];
                    $website            = $row['website'];
                    $reach_area         = $row['reach_area'];
                    $is_24hrs_available = $row['is_24hrs_available'];
                    if ($is_24hrs_available == 'Yes') {
                        $store_open  = date("h:i A", strtotime("12:00 AM"));
                        $store_close = date("h:i A", strtotime("11:59 PM"));
                    } else {
                        $store_open  = $this->check_time_format($row['store_open']);
                        $store_close = $this->check_time_format($row['store_close']);
                    }
                    $day_night_delivery = $row['day_night_delivery'];
                    $free_start_time    = $this->check_time_format($row['free_start_time']);
                    $free_end_time      = $this->check_time_format($row['free_end_time']);
                    ;
                    $days_closed               = $row['days_closed'];
                    $min_order                 = $row['min_order'];
                    $is_min_order_delivery     = $row['is_min_order_delivery'];
                    $min_order_delivery_charge = $row['min_order_delivery_charge'];
                    $night_delivery_charge     = $row['night_delivery_charge'];
                    $payment_type              = $row['payment_type'];
                    
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
                    $row_pharma     = $query_pharmacy->row_array();
                    $rating         = $row_pharma['avg_rating'];
                    if ($rating === NULL) {
                        $rating = '0';
                    }
                    
                    $review = $this->db->select('id')->from('medical_stores_review')->where('medical_stores_id', $medical_id)->get()->num_rows();
                    
                    $chat_id      = $row['user_id'];
                    $chat_display = $row['medical_name'];
                    $is_chat      = 'Yes';
                    
                    //All Days Open
                    
                    
                    $Monday    = $this->check_day_status('Monday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                    $Tuesday   = $this->check_day_status('Tuesday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                    $Wednesday = $this->check_day_status('Wednesday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                    $Thursday  = $this->check_day_status('Thursday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                    $Friday    = $this->check_day_status('Friday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                    $Saturday  = $this->check_day_status('Saturday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                    $Sunday    = $this->check_day_status('Sunday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                    
                    
                    $opening_hours = "Monday>$Monday,Tuesday>$Tuesday,Wednesday>$Wednesday,Thursday>$Thursday,Friday>$Friday,Saturday>$Saturday,Sunday>$Sunday";
                    
                    $open_days         = '';
                    $day_array_list    = '';
                    $day_list          = '';
                    $day_time_list     = '';
                    $time_list1        = '';
                    $time_list2        = '';
                    $time              = '';
                    $system_start_time = '';
                    $system_end_time   = '';
                    $time_check        = '';
                    $current_time      = '';
                    $open_close        = array();
                    $time              = array();
                    date_default_timezone_set('Asia/Kolkata');
                    $data           = array();
                    $final_Day      = array();
                    $day_array_list = explode(',', $opening_hours);
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
                                        $time_check        = $time_list1[$l] . '-' . $time_list2[$l];
                                        $time              = str_replace('close-close', 'close', $time_check);
                                        $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                        $system_end_time   = date("H.i", strtotime($time_list2[$l]));
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
                    
                    
                    $product_category_list = array();
                    $query_category        = $this->db->query("SELECT id,category,image FROM `category` WHERE id<>'3' order by id asc");
                    foreach ($query_category->result_array() as $row) {
                        $product_id       = $row['id'];
                        $product_category = $row['category'];
                        $product_image    = $row['image'];
                        $product_image    = str_replace(" ", "", $product_image);
                        $product_image    = 'https://d2c8oti4is0ms3.cloudfront.net/images/medicalwale_mobile_app_icons/product_category/' . $product_image;
                        
                        $product_category_list[] = array(
                            "id" => $product_id,
                            "category" => $product_category,
                            'image' => $product_image
                        );
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
                        'category_list' => $product_category_list
                    );
                } else {
                    $resultpost = array();
                }
                
                
            }
            
            
            if ($listing_type == '1') {
                // Ayurveda
                
                
                $about_query = $this->db->query("SELECT ayurveda_name,contact_no,profile_pic,about_us FROM `ayurveda` WHERE user_id='$listing_id'");
                $get_about   = $about_query->row_array();
                $count       = $about_query->num_rows();
                if ($count > 0) {
                    $ayurveda_name = $get_about['ayurveda_name'];
                    $phone         = $get_about['contact_no'];
                    $about_us      = $get_about['about_us'];
                    $profile_pic   = $get_about['profile_pic'];
                    
                    if ($profile_pic != '') {
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/ayurveda_images/' . $profile_pic;
                    } else {
                        $profile_pic = '';
                    }
                    
                    $gallery_array = '';
                    $gallery_query = $this->db->query("SELECT title,media,type FROM `ayurveda_gallery` WHERE user_id='$listing_id'");
                    foreach ($gallery_query->result_array() as $row) {
                        $title           = $row['title'];
                        $media           = 'https://d2c8oti4is0ms3.cloudfront.net/images/ayurveda_images/' . $row['media'];
                        $type            = $row['type'];
                        $gallery_array[] = array(
                            'title' => $title,
                            'media' => $media,
                            'type' => $type
                        );
                    }
                    
                    
                    
                    
                    $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM ayurveda_review WHERE ayurveda_id='$listing_id' ");
                    $row_rating   = $query_rating->row_array();
                    $rating       = $row_rating['avg_rating'];
                    
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
                $sql   = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_name`,`address1`,`address2`, `pincode`, `city`, `state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`, IFNULL(delivery_charges,'') AS delivery_charges FROM lab_center where user_id='$listing_id'");
                $query = $this->db->query($sql);
                $count = $query->num_rows();
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
                        $opening_hours     = $row['opening_hours'];
                        $lat               = $row['latitude'];
                        $lng               = $row['longitude'];
                        $rating            = '4.0';
                        $profile_views     = '1548';
                        $reviews           = '1000';
                        $image             = $row['profile_pic'];
                        $image             = 'https://d2c8oti4is0ms3.cloudfront.net/images/labcenter_images/' . $image;
                        
                        $features_array = array();
                        
                        $feature_query = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $features . "')");
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
                            "image" => $image
                            
                        );
                    }
                    
                } else {
                    $resultpost = array();
                }
                
                
                
            }
            if ($listing_type == '12') {
                // Nursing Attendant
                
                
                $query = $this->db->query(" SELECT `id`, `user_id`, `name`, `about_us`, `services`,`establishment_year`, `certificates`, `address`, `pincode`, `contact`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`, `date`, `is_active` FROM nursing_attendant WHERE user_id='$listing_id'");
                
                $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id                       = $row['id'];
                        $name                     = $row['name'];
                        $about_us                 = $row['about_us'];
                        $establishment_year       = $row['establishment_year'];
                        $services                 = $row['services'];
                        $certificates             = $row['certificates'];
                        $address                  = $row['address'];
                        $lat                      = $row['lat'];
                        $lng                      = $row['lng'];
                        $pincode                  = $row['pincode'];
                        $mobile                   = $row['contact'];
                        $city                     = $row['city'];
                        $state                    = $row['state'];
                        $email                    = $row['email'];
                        $image                    = $row['image'];
                        $rating                   = $row['rating'];
                        $reviews                  = $row['review'];
                        $nursingattendant_user_id = $row['user_id'];
                        $profile_views            = '0';
                        
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
                        
                        
                        
                        $certificates_query = $this->db->query("SELECT `name`, `image` FROM `nursing_attendant_certificates` WHERE FIND_IN_SET(name,'" . $certificates . "')");
                        foreach ($certificates_query->result_array() as $get_clist) {
                            $certificates_name  = $get_clist['name'];
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
                        
                        
                        
                        $nursingattendant_services_query = $this->db->query("SELECT `service_name` FROM `nursing_attendant_services` WHERE FIND_IN_SET(service_name,'" . $services . "')");
                        foreach ($nursingattendant_services_query->result_array() as $get_serlist) {
                            $service_name                    = $get_serlist['service_name'];
                            $nursingattendant_service_list[] = array(
                                "service_name" => $service_name
                            );
                        }
                        
                        
                        
                        $gallery_query = $this->db->query("SELECT `title` , CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/nursing_attendant_images/',source) AS media FROM `nursing_attendant_media` WHERE nursing_attendant_id='$id'");
                        foreach ($gallery_query->result_array() as $get_list2) {
                            $gallery_title  = $get_list2['title'];
                            $gallery_image  = $get_list2['media'];
                            $gallery_list[] = array(
                                "title" => $gallery_title,
                                "image" => $gallery_image
                            );
                        }
                        
                        
                        
                        
                        $resultpost[] = array(
                            'nursing_attendant_id' => $id,
                            'nursing_user_id' => $nursingattendant_user_id,
                            'name' => $name,
                            'about_us' => $about_us,
                            'establishment_year' => $establishment_year,
                            'certificates_list' => $certificates_list,
                            'nursingattendant_service_list' => $nursingattendant_service_list,
                            'gallery_list' => $gallery_list,
                            'address' => $address,
                            'mobile' => $mobile,
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
                            'profile_views' => $profile_views,
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
                        $id                     = $row['id'];
                        $name                   = $row['name'];
                        $address                = $row['address'];
                        $pincode                = $row['pincode'];
                        $contact                = $row['contact'];
                        $city                   = $row['city'];
                        $state                  = $row['state'];
                        $whatsapp               = $row['whatsapp'];
                        $email                  = $row['email'];
                        $opening_hours          = $row['opening_hours'];
                        $cuppingtherapy_user_id = $row['user_id'];
                        $lat                    = $row['lat'];
                        $lng                    = $row['lng'];
                        $rating                 = '4.0';
                        $profile_views          = '0';
                        $reviews                = '0';
                        $description            = $row['description'];
                        $image                  = $row['image'];
                        $image                  = 'https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/' . $image;
                        
                        $gallery_query = $this->db->query("SELECT * FROM `cuppingtherapy_media` WHERE `cuppingtherapy_id`='$id'");
                        $gallery_array = array();
                        $gallery_count = $gallery_query->num_rows();
                        if ($gallery_count > 0) {
                            foreach ($gallery_query->result_array() as $rows) {
                                $media_name = $rows['title'];
                                $source     = $rows['source'];
                                $gallery    = 'https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/' . $source;
                                
                                $media_name   = str_replace(".jpg", "", $media_name);
                                $gallery_name = $media_name;
                                
                                $cnt = count($gallery);
                                
                                $gallery_array[] = array(
                                    "title" => $gallery_name,
                                    "image" => $gallery
                                );
                            }
                            
                            
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
                        
                        
                        
                        $gallery_query = $this->db->query("SELECT GROUP_CONCAT(`title`) AS title, GROUP_CONCAT(CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/',source)) AS media FROM `cuppingtherapy_media`");
                        if ($gallery_query) {
                            $row2         = $gallery_query->row();
                            $gallery_name = $row2->title;
                            $gallery      = $row2->media;
                        } else {
                            $gallery      = '';
                            $gallery_name = '';
                        }
                        
                        
                        $resultpost[] = array(
                            "id" => $id,
                            "name" => $name,
                            "address" => $address,
                            "pincode" => $pincode,
                            "contact" => $contact,
                            "city" => $city,
                            "state" => $state,
                            "whatsapp" => $whatsapp,
                            "email" => $email,
                            "gallery" => $gallery_array,
                            "description" => $description,
                            "rating" => $rating,
                            "followers" => $followers,
                            "following" => $following,
                            "profile_views" => $profile_views,
                            "reviews" => $reviews,
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
                $query = $this->db->query("SELECT id,user_id,business_category,center_name,year,manager_name,address,pincode,contact,city,state,whatsapp,email,opening_hours,what_we_offer,facilities,lat,lng,date,image,membership_plan FROM fitness_center WHERE user_id='$listing_id'");
                
                $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id                 = $row['id'];
                        $lat                = $row['lat'];
                        $lng                = $row['lng'];
                        $center_name        = $row['center_name'];
                        $manager_name       = $row['manager_name'];
                        $about_us           = $row['center_name'];
                        $establishment_year = $row['year'];
                        $address            = $row['address'];
                        $pincode            = $row['pincode'];
                        $contact            = $row['contact'];
                        $city               = $row['city'];
                        $state              = $row['state'];
                        $whatsapp           = $row['whatsapp'];
                        $email              = $row['email'];
                        $opening_hours      = $row['opening_hours'];
                        $what_we_offer      = $row['what_we_offer'];
                        $facilities         = $row['facilities'];
                        $business_category  = $row['business_category'];
                        $listing_id         = $row['user_id'];
                        $image              = $row['image'];
                        $rating             = '0';
                        $reviews            = '0';
                        $profile_views      = '0';
                        
                        $gallery_query = $this->db->query("SELECT * FROM `fitness_gallery` WHERE `category_id`='$business_category' AND `user_id`= '$listing_id' ");
                        $gallery_array = array();
                        $gallery_count = $gallery_query->num_rows();
                        if ($gallery_count > 0) {
                            foreach ($gallery_query->result_array() as $rows) {
                                $media_name = $rows['media_name'];
                                $gallery    = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/' . $media_name;
                                
                                $media_name   = str_replace(".jpg", "", $media_name);
                                $gallery_name = $media_name;
                                
                                $cnt = count($gallery);
                                
                                $gallery_array[] = array(
                                    "title" => $gallery_name,
                                    "image" => $gallery
                                );
                            }
                            
                            
                        }
                        
                        
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $image;
                        } else {
                            $image = '';
                        }
                        
                        $open_days         = '';
                        $day_array_list    = '';
                        $day_list          = '';
                        $day_time_list     = '';
                        $time_list1        = '';
                        $time_list2        = '';
                        $time              = '';
                        $system_start_time = '';
                        $system_end_time   = '';
                        $time_check        = '';
                        $current_time      = '';
                        $open_close        = array();
                        $time              = array();
                        date_default_timezone_set('Asia/Kolkata');
                        $data           = array();
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
                        $what_we_offer       = explode(',', $what_we_offer);
                        foreach ($what_we_offer as $what_we_offer) {
                            $what_we_offer_array[] = $what_we_offer;
                        }
                        
                        $facilities_array = array();
                        $facilities       = explode(',', $facilities);
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
                        
                        
                        
                        $package_list  = '';
                        $package_query = $this->db->query("SELECT id,package_name,package_details,price,image FROM `fitness_center_membership_plan` where fitness_center_id='$id' order by id asc");
                        $package_count = $package_query->num_rows();
                        if ($package_count > 0) {
                            foreach ($package_query->result_array() as $package_row) {
                                $package_name    = $package_row['package_name'];
                                $package_details = $package_row['package_details'];
                                $price           = $package_row['price'];
                                $image           = $package_row['image'];
                                $image           = str_replace(" ", "", $image);
                                $image           = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                                $package_list[]  = array(
                                    'package_name' => $package_name,
                                    'package_details' => $package_details,
                                    'price' => $price,
                                    'image' => $image
                                );
                            }
                        } else {
                            $package_list = array();
                        }
                        
                        
                        $resultpost[] = array(
                            'id' => $id,
                            'lat' => $lat,
                            'lng' => $lng,
                            'id' => $id,
                            'listing_id' => $listing_id,
                            'about_us' => $about_us,
                            'center_name' => $center_name,
                            'manager_name' => $manager_name,
                            'address' => $address,
                            'establishment_year' => $establishment_year,
                            'pincode' => $pincode,
                            'contact' => $contact,
                            'city' => $city,
                            'state' => $state,
                            'whatsapp' => $whatsapp,
                            'email' => $email,
                            'image' => $image,
                            'gallery' => $gallery_array,
                            'rating' => $rating,
                            'review' => $reviews,
                            'followers' => $followers,
                            'following' => $following,
                            'is_follow' => $is_follow,
                            'profile_views' => $profile_views,
                            'opening_day' => $final_Day,
                            'what_we_offer' => $what_we_offer_array,
                            'facilities' => $facilities_array,
                            'package_list' => $package_list
                            
                        );
                        
                    }
                } else {
                    $resultpost = array();
                }
            }
            if ($listing_type == '8') {
                // Hospital
                
                
                $query = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year` WHERE user_id='$listing_id'");
                
                $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id                  = $row['id'];
                        $name_of_hospital    = $row['name_of_hospital'];
                        $mobile              = $row['phone'];
                        $about_us            = $row['about_us'];
                        $establishment_year  = $row['establishment_year'];
                        $certificates_accred = $row['certificates_accred'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $surgery             = $row['surgery'];
                        $services            = $row['services'];
                        $address             = $row['address'];
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $pincode             = $row['pincode'];
                        
                        $city             = $row['city'];
                        $state            = $row['state'];
                        $email            = $row['email'];
                        $image            = $row['image'];
                        $rating           = $row['rating'];
                        $reviews          = $row['review'];
                        $hospital_user_id = $row['user_id'];
                        $profile_views    = '2458';
                        
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $image;
                        } else {
                            $image = '';
                        }
                        
                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $hospital_user_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $hospital_user_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $hospital_user_id)->get()->num_rows();
                        
                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }
                        
                        
                        
                        $certificates_accred_query = $this->db->query("SELECT `name`, image FROM `hospital_certificates_accred` WHERE FIND_IN_SET(name,'" . $certificates_accred . "')");
                        foreach ($certificates_accred_query->result_array() as $get_clist) {
                            $certificates_name  = $get_clist['name'];
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
                        
                        $hospitals_surgery_query = $this->db->query("SELECT `id`,`surgery_name`,`surgery_rate`,`surgery_package`, image FROM `hospitals_surgery` WHERE FIND_IN_SET(surgery_name,'" . $surgery . "')");
                        foreach ($hospitals_surgery_query->result_array() as $get_slist) {
                            $surgery_id      = $get_slist['id'];
                            $surgery_name    = $get_slist['surgery_name'];
                            $surgery_rate    = $get_slist['surgery_rate'];
                            $surgery_package = $get_slist['surgery_package'];
                            $surgery_image   = $get_slist['image'];
                            
                            if ($surgery_image != '') {
                                $surgery_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $surgery_image;
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
                        
                        
                        
                        $gallery_query = $this->db->query("SELECT `title`, CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/',source) AS source FROM `hospital_media` WHERE hospital_id='$id'");
                        foreach ($gallery_query->result_array() as $get_glist) {
                            $title       = $get_glist['title'];
                            $media_image = $get_glist['source'];
                            $gallery[]   = array(
                                "title" => $title,
                                "image" => $media_image
                            );
                        }
                        
                        
                        
                        $hospitals_services_query = $this->db->query("SELECT `service_name` FROM `hospital_services` WHERE FIND_IN_SET(service_name,'" . $services . "')");
                        foreach ($hospitals_services_query->result_array() as $get_serlist) {
                            $service_name             = $get_serlist['service_name'];
                            $hospitals_service_list[] = array(
                                "service_name" => $service_name
                            );
                        }
                        
                        
                        
                        $hospitals_speciality_query = $this->db->query("SELECT `id`,`doctors_category`,`name`, image FROM `hospitals_specialist` WHERE FIND_IN_SET(name,'" . $speciality . "')");
                        foreach ($hospitals_speciality_query->result_array() as $get_splist) {
                            $specialist_id    = $get_splist['id'];
                            $specialist_name  = $get_splist['name'];
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
                        
                        
                        $resultpost[] = array(
                            'hospital_id' => $id,
                            'hospital_user_id' => $hospital_user_id,
                            'name_of_hospital' => $name_of_hospital,
                            'about_us' => $about_us,
                            'establishment_year' => $establishment_year,
                            'certificates_accred_list' => $certificates_accred_list,
                            'hospitals_surgery_list' => $hospitals_surgery_list,
                            'gallery' => $gallery,
                            'hospitals_service_list' => $hospitals_service_list,
                            'hospitals_speciality_list' => $hospitals_speciality_list,
                            'address' => $address,
                            'mobile' => $mobile,
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
                            'profile_views' => $profile_views,
                            'is_follow' => $is_follow
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
                        $id                  = $row['id'];
                        $name                = $row['name'];
                        $address             = $row['address'];
                        $phone               = $row['phone'];
                        $state               = $row['state'];
                        $city                = $row['city'];
                        $pincode             = $row['pincode'];
                        $vehicle_in_services = $row['vehicle_in_services'];
                        $ac_available        = $row['ac_available'];
                        $all_24hrs_available = $row['all_24hrs_available'];
                        $establishment_year  = $row['establishment_year'];
                        $list_of_equipment2  = $row['list_of_equipment'];
                        $image               = $row['image'];
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $list_of_equipment   = array();
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/ambulance_images/' . $image;
                        } else {
                            $image = '';
                        }
                        
                        $ambulance_user_id = $row['user_id'];
                        $rating            = $row['ratings'];
                        $profile_views     = '0';
                        $reviews           = $row['reviews'];
                        
                        
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
                            $name                = $get_list['name'];
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
                
                $query = $this->db->query("SSELECT `id`, `user_id`, `name`, `about_us`, `establishment_year`, `address`, `phone`, `telephone`, `state`, `city`, `pincode`, `image`, `service_offered`, `lat`, `lng`, `reviews`, `ratings` FROM oldagehome where user_id='$listing_id' limit 1");
                
                $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id                          = $row['id'];
                        $name                        = $row['name'];
                        $about_us                    = $row['about_us'];
                        $establishment_year          = $row['establishment_year'];
                        $address                     = $row['address'];
                        $phone                       = $row['phone'];
                        $state                       = $row['state'];
                        $city                        = $row['city'];
                        $pincode                     = $row['pincode'];
                        $oldagehome_service_offered2 = $row['service_offered'];
                        $image                       = $row['image'];
                        $lat                         = $row['lat'];
                        $lng                         = $row['lng'];
                        $oldagehome_service_offered  = array();
                        $gallery_list                = array();
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/oldagehome_images/' . $image;
                        } else {
                            $image = '';
                        }
                        
                        $oldagehome_user_id = $row['user_id'];
                        $rating             = $row['ratings'];
                        $profile_views      = '0';
                        $reviews            = $row['reviews'];
                        
                        
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
                            $service_offered              = $get_list['name'];
                            $oldagehome_service_offered[] = array(
                                "service_offered" => $service_offered
                            );
                        }
                        
                        $gallery_query = $this->db->query("SELECT `title` , CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/oldagehome_images/',source) AS media FROM `oldagehome_media` WHERE oldagehome_id='$id'");
                        foreach ($gallery_query->result_array() as $get_list2) {
                            $gallery_title  = $get_list2['title'];
                            $gallery_image  = $get_list2['media'];
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
                        $id                  = $row['id'];
                        $pestcontrol_id      = $row['id'];
                        $pestcontrol_user_id = $row['user_id'];
                        $name                = $row['name'];
                        $address             = $row['address'];
                        $pincode             = $row['pincode'];
                        $contact             = $row['contact'];
                        $city                = $row['city'];
                        $state               = $row['state'];
                        $whatsapp            = $row['whatsapp'];
                        $email               = $row['email'];
                        $opening_hours       = $row['opening_hours'];
                        $pestcontrol_user_id = $row['user_id'];
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $rating              = '4.5';
                        $profile_views       = '0';
                        $reviews             = '0';
                        $description         = $row['description'];
                        $image               = $row['image'];
                        $image               = 'https://d2c8oti4is0ms3.cloudfront.net/images/pestcontrol_images/' . $image;
                        
                        $gallery_query = $this->db->query("SELECT * FROM `pest_control_media` WHERE `pest_control_id`='$id'");
                        $gallery_array = array();
                        $gallery_count = $gallery_query->num_rows();
                        if ($gallery_count > 0) {
                            foreach ($gallery_query->result_array() as $rows) {
                                $media_name = $rows['title'];
                                $source     = $rows['source'];
                                $gallery    = 'https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/' . $source;
                                
                                $media_name   = str_replace(".jpg", "", $media_name);
                                $gallery_name = $media_name;
                                
                                $cnt = count($gallery);
                                
                                $gallery_array[] = array(
                                    "title" => $gallery_name,
                                    "image" => $gallery
                                );
                            }
                            
                            
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
                            $row2         = $gallery_query->row();
                            $gallery_name = $row2->title;
                            $gallery      = $row2->media;
                        } else {
                            $gallery      = '';
                            $gallery_name = '';
                        }
                        
                        
                        $packages_query = $this->db->query("SELECT id,packages FROM `pest_control` WHERE id='$pestcontrol_id'");
                        $count_          = $packages_query->num_rows();
                        $package = '';
                        
                        if ($count_ > 0) {
                            foreach ($packages_query->result_array() as $row) {
                                $packages = $row['packages'];
                                $pestcontrol_packages = $this->db->query("SELECT * FROM `pestcontrol_packages` WHERE FIND_IN_SET(package_name,'" . $packages . "')");
                                foreach ($pestcontrol_packages->result_array() as $get_list) {
                                    $id              = $get_list['id'];
                                    $package_name    = $get_list['package_name'];
                                    $package_details = $get_list['package_details'];
                                    $price           = $get_list['price'];
                                    $image           = $get_list['image'];
                                    $image           = 'https://d2c8oti4is0ms3.cloudfront.net/images/pestcontrol_images/' . $image;
                                    
                                    $package[] = array(
                                        "id" => "$id",
                                        "package_name" => $package_name,
                                        "package_details" => $package_details,
                                        'price' => $price,
                                        'image' => $image
                                    );
                                }
                            }
                        }
						else{
							$package = array();
						}
						
                        
                        $resultpost[] = array(
                            "id" => $id,
                            "pestcontrol_user_id" => $pestcontrol_user_id,
                            "name" => $name,
                            "address" => $address,
                            "pincode" => $pincode,
                            "contact" => $contact,
                            "city" => $city,
                            "state" => $state,
                            "whatsapp" => str_replace('null','',$whatsapp),
                            "email" => $email,
                            "gallery" => $gallery_array,
                            //"gallery_name"=>$gallery_name,
                            "description" => $description,
                            "rating" => $rating,
                            "followers" => $followers,
                            "following" => $following,
                            "profile_views" => $profile_views,
                            "reviews" => $reviews,
                            "is_follow" => $is_follow,
                            "lat" => $lat,
                            "lng" => $lng,
                            "opening_day" => $final_Day,
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
                        $id          = $row['id'];
                        $bank_name   = $row['bank_name'];
                        $established = $row['established'];
                        $contact     = $row['contact'];
                        $address     = $row['address'];
                        $about_us    = $row['about_us'];
                        
                        $fda_lic_no = $row['fda_lic_no'];
                        $bto_name   = $row['bto_name'];
                        $component  = $row['component'];
                        $image      = $row['image'];
                        $lat        = $row['lat'];
                        $lng        = $row['lng'];
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/bloodbank_images' . $image;
                        } else {
                            $image = '';
                        }
                        $blood_bank_user_id = $row['user_id'];
                        $rating             = $row['ratings'];
                        $profile_views      = '0';
                        $reviews            = $row['reviews'];
                        $opening_hours      = $row['opening_hours'];
                        $open_days          = '';
                        $day_array_list     = '';
                        $day_list           = '';
                        $day_time_list      = '';
                        $time_list1         = '';
                        $time_list2         = '';
                        $time               = '';
                        $system_start_time  = '';
                        $system_end_time    = '';
                        $time_check         = '';
                        $current_time       = '';
                        $open_close         = array();
                        $time               = array();
                        date_default_timezone_set('Asia/Kolkata');
                        $data           = array();
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
                                            $time_check = $time_list1[$l] . '-' . $time_list2[$l];
                                            $time       = str_replace('close-close', 'close', $time_check);
                                            if ($time == '12:00 AM-11:59 PM') {
                                                $time = '24 hrs open';
                                            }
                                            if ($time != 'close') {
                                                $time = array(
                                                    $time
                                                );
                                            }
                                            $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                                            $system_end_time   = date("h:i A", strtotime($time_list2[$l]));
                                            $current_time      = date('h:i A');
                                            
                                            
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
                        $followers   = $this->db->select('id')->from('follow_user')->where('parent_id', $blood_bank_user_id)->get()->num_rows();
                        $following   = $this->db->select('id')->from('follow_user')->where('user_id', $blood_bank_user_id)->get()->num_rows();
                        $is_follow   = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $blood_bank_user_id)->get()->num_rows();
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
                        $id                          = $row['id'];
                        $name                        = $row['name'];
                        $about_us                    = $row['about_us'];
                        $establishment_year          = $row['establishment_year'];
                        $address                     = $row['address'];
                        $mobile                      = $row['phone'];
                        $state                       = $row['state'];
                        $city                        = $row['city'];
                        $pincode                     = $row['pincode'];
                        $babysitter_service_offered2 = $row['service_offered'];
                        $image                       = $row['image'];
                        $lat                         = $row['lat'];
                        $lng                         = $row['lng'];
                        $babysitter_service_offered  = array();
                        $gallery_list                = array();
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/babysitter_images/' . $image;
                        } else {
                            $image = '';
                        }
                        
                        $babysitter_user_id = $row['user_id'];
                        $rating             = $row['ratings'];
                        $profile_views      = '415';
                        $reviews            = $row['reviews'];
                        
                        
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
                            $service_offered              = $get_list['name'];
                            $babysitter_service_offered[] = array(
                                "service_offered" => $service_offered
                            );
                        }
                        
                        $gallery_query = $this->db->query("SELECT `title` , CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/babysitter_images/',source) AS media FROM `babysitter_media` WHERE babysitter_id='$id'");
                        foreach ($gallery_query->result_array() as $get_list2) {
                            $gallery_title  = $get_list2['title'];
                            $gallery_image  = $get_list2['media'];
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
                            'mobile' => $mobile,
                            'state' => $state,
                            'city' => $city,
                            'pincode' => $pincode,
                            'latitude' => $lat,
                            'longitude' => $lng,
                            'image' => $image,
                            'service_offered' => $babysitter_service_offered,
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
            
            if ($listing_type == '22') {
                // Nanny
                
                $query = $this->db->query("SELECT `id`, `user_id`, `name`, `about_us`, `establishment_year`, `address`, `phone`, `telephone`, `state`, `city`, `pincode`, `image`, `service_offered`, `lat`, `lng`, `reviews`, `ratings` FROM dai_nanny WHERE user_id = '$listing_id' order by id ASC");
                
                $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id                         = $row['id'];
                        $name                       = $row['name'];
                        $about_us                   = $row['about_us'];
                        $establishment_year         = $row['establishment_year'];
                        $address                    = $row['address'];
                        $mobile                     = $row['phone'];
                        $state                      = $row['state'];
                        $city                       = $row['city'];
                        $pincode                    = $row['pincode'];
                        $dai_nanny_service_offered2 = $row['service_offered'];
                        $image                      = $row['image'];
                        $lat                        = $row['lat'];
                        $lng                        = $row['lng'];
                        $dai_nanny_service_offered  = array();
                        $gallery_list               = array();
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/dai_nanny_images/' . $image;
                        } else {
                            $image = '';
                        }
                        
                        $dai_nanny_user_id = $row['user_id'];
                        $rating            = $row['ratings'];
                        $profile_views     = '415';
                        $reviews           = $row['reviews'];
                        
                        
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
                            $service_offered             = $get_list['name'];
                            $dai_nanny_service_offered[] = array(
                                "service_offered" => $service_offered
                            );
                        }
                        
                        $gallery_query = $this->db->query("SELECT `title` , CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/dai_nanny_images/',source) AS media FROM `dai_nanny_media` WHERE dai_nanny_id='$id'");
                        foreach ($gallery_query->result_array() as $get_list2) {
                            $gallery_title  = $get_list2['title'];
                            $gallery_image  = $get_list2['media'];
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
                        $id          = $row['id'];
                        $doctor_name = $row['doctor_name'];
                        $about_us    = $row['about_us'];
                        
                        $address                = $row['address'];
                        $telephone              = $row['telephone'];
                        $medical_college        = $row['medical_college'];
                        $medical_affiliation    = $row['medical_affiliation'];
                        $charitable_affiliation = $row['charitable_affiliation'];
                        $awards_recognition     = $row['awards_recognition'];
                        $hrs_available          = $row['all_24_hrs_available'];
                        $home_visit_available   = $row['home_visit_available'];
                        $qualification          = $row['qualification'];
                        $consultation_fee       = $row['consultation_fee'];
                        $experience             = $row['experience'];
                        $website                = $row['website'];
                        $location               = $row['location'];
                        $days                   = $row['days'];
                        $timing                 = $row['timing'];
                        $rating                 = $row['rating'];
                        $reviews                = $row['review'];
                        $image                  = $row['image'];
                        $doctor_user_id         = $row['user_id'];
                        $profile_views          = '0';
                        
                        
                        
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
                                $id             = $hospital_row['hospital_id'];
                                $hospital_name  = $hospital_row['name_of_hospital'];
                                $address        = $hospital_row['address'];
                                $rating         = $hospital_row['rating'];
                                $hospital_image = $hospital_row['image'];
                                $opening_days   = $hospital_row['opening_days'];
                                if ($hospital_image != '') {
                                    $hospital_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $hospital_image;
                                } else {
                                    $hospital_image = '';
                                }
                                
                                
                                date_default_timezone_set('Asia/Kolkata');
                                $open_days     = '';
                                $day           = '';
                                $time          = '';
                                $start_time    = '';
                                $end_time      = '';
                                $opening_hours = explode(',', $opening_days);
                                foreach ($opening_hours as $opening_hour) {
                                    $array_hours = explode('-', $opening_hour);
                                    $day         = $array_hours[0];
                                    $start_time  = $array_hours[1];
                                    $end_time    = $array_hours[2];
                                    $time        = $start_time . ' - ' . $end_time;
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
                        $service               = '';
                        $result_services       = '';
                        $doctor_services_query = $this->db->query("SELECT service FROM `doctor_services`");
                        foreach ($doctor_services_query->result_array() as $doctor_services) {
                            $service           = $doctor_services['service'];
                            $result_services[] = array(
                                'service' => $service
                            );
                        }
                        $specialization              = '';
                        $result_specialization       = '';
                        $doctor_specialization_query = $this->db->query("SELECT specialization FROM `doctor_specialization`");
                        foreach ($doctor_specialization_query->result_array() as $doctor_specialization) {
                            $specialization          = $doctor_specialization['specialization'];
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
            
            
            
            if ($listing_type == '24') {
                // 	Counselling
                
                $query = $this->db->query("SELECT id,lat,lng,user_id,doctor_name,about_us,speciality,address,telephone,medical_college,medical_affiliation,charitable_affiliation,awards_recognition,all_24_hrs_available,home_visit_available,qualification,consultation_fee,experience,website,location,days,timing,image,rating,review FROM counselling_list WHERE user_id='$listing_id' ");
                
                $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id          = $row['id'];
                        $doctor_name = $row['doctor_name'];
                        $about_us    = $row['about_us'];
                        
                        $address                = $row['address'];
                        $telephone              = $row['telephone'];
                        $medical_college        = $row['medical_college'];
                        $medical_affiliation    = $row['medical_affiliation'];
                        $charitable_affiliation = $row['charitable_affiliation'];
                        $awards_recognition     = $row['awards_recognition'];
                        $hrs_available          = $row['all_24_hrs_available'];
                        $home_visit_available   = $row['home_visit_available'];
                        $qualification          = $row['qualification'];
                        $consultation_fee       = $row['consultation_fee'];
                        $experience             = $row['experience'];
                        $website                = $row['website'];
                        $location               = $row['location'];
                        $days                   = $row['days'];
                        $timing                 = $row['timing'];
                        $rating                 = $row['rating'];
                        $reviews                = $row['review'];
                        $image                  = $row['image'];
                        $doctor_user_id         = $row['user_id'];
                        $profile_views          = '2458';
                        
                        
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
                                $id             = $hospital_row['hospital_id'];
                                $hospital_name  = $hospital_row['name_of_hospital'];
                                $address        = $hospital_row['address'];
                                $rating         = $hospital_row['rating'];
                                $hospital_image = $hospital_row['image'];
                                $opening_days   = $hospital_row['opening_days'];
                                if ($hospital_image != '') {
                                    $hospital_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $hospital_image;
                                } else {
                                    $hospital_image = '';
                                }
                                
                                
                                date_default_timezone_set('Asia/Kolkata');
                                $open_days     = '';
                                $day           = '';
                                $time          = '';
                                $start_time    = '';
                                $end_time      = '';
                                $opening_hours = explode(',', $opening_days);
                                foreach ($opening_hours as $opening_hour) {
                                    $array_hours = explode('-', $opening_hour);
                                    $day         = $array_hours[0];
                                    $start_time  = $array_hours[1];
                                    $end_time    = $array_hours[2];
                                    $time        = $start_time . ' - ' . $end_time;
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
                        $service               = '';
                        $result_services       = '';
                        $doctor_services_query = $this->db->query("SELECT service FROM `doctor_services`");
                        foreach ($doctor_services_query->result_array() as $doctor_services) {
                            $service           = $doctor_services['service'];
                            $result_services[] = array(
                                'service' => $service
                            );
                        }
                        $specialization              = '';
                        $result_specialization       = '';
                        $doctor_specialization_query = $this->db->query("SELECT specialization FROM `doctor_specialization`");
                        foreach ($doctor_specialization_query->result_array() as $doctor_specialization) {
                            $specialization          = $doctor_specialization['specialization'];
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
            
            
            
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
}
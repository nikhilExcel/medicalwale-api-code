<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SalesModel extends CI_Model {

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

    public function add_doctor_form($data,$clinic,$user,$package,$offer,$awards) {
        $insert_list = $this->db->insert('users', $user);
        $id = $this->db->insert_id();
        $data['user_id']=$id;
        $insert_list = $this->db->insert('doctor_list', $data);
        if ($insert_list) {
            $vendor_discount = array(
                'vendor_id' => $id,
                'discount_min' => 0,
                'discount_max' => 50,
                'discount_type' => 'percent'
            );
            $this->db->insert('vendor_discount', $vendor_discount);
            $clinic['doctor_id']=$id;
            $insert_clinic = $this->db->insert('doctor_clinic', $clinic);
            if($package!=''){
            $package_json = '{"inbox":' . $package . '}';
            $package_data = json_decode($package_json);
            foreach ($package_data->inbox as $package_array) {
                $package_list = array(
                    'name' => $package_array->name,
                    'price' => $package_array->price,
                    'vendor_type' => 'doctor',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_packages', $package_list);
            }
            }
            if($offer!=''){
            $offer_json = '{"inbox":' . $offer . '}';
            $offer_data = json_decode($offer_json);
            foreach ($offer_data->inbox as $offer_array) {
                $offer_list = array(
                    'name' => $offer_array->name,
                    'price' => $offer_array->price,
                    'end_date' => $offer_array->end_date,
                    'vendor_type' => 'doctor',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_offers', $offer_list);
            }
            }
            if($awards!=''){
            $awards_json = '{"inbox":' . $awards . '}';
            $awards_data = json_decode($awards_json);
            foreach ($awards_data->inbox as $awards_array) {
                $awards_list = array(
                    'name' => $awards_array->name,
                    'date' => $awards_array->date,
                    'vendor_type' => 'doctor',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_awards', $awards_list);
            }
            }
            return array(
                'status' => 200,
                'message' => 'success',
                'id' => $id
            );
        } else {
            return array(
                'status' => 200,
                'message' => 'failure',
                'id' => '0'
            );
        }
    }

    public function update_doctor_form($data,$clinic,$user,$id,$package,$offer,$awards) {
        date_default_timezone_set('Asia/Kolkata');
        $created_date = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $update_list = $this->db->UPDATE('users', $user);
        $this->db->where('user_id', $id);
        $update_list = $this->db->UPDATE('doctor_list', $data);
        $this->db->where('doctor_id', $id);
        $update_list = $this->db->UPDATE('doctor_clinic', $clinic);
        if ($update_list) {
            $this->db->query("DELETE FROM `vendor_packages` WHERE vendor_id='$id' and vendor_type='doctor'");
            if($package!=''){
            $package_json = '{"inbox":' . $package . '}';
            $package_data = json_decode($package_json);
            foreach ($package_data->inbox as $package_array) {
                $package_list = array(
                    'name' => $package_array->name,
                    'price' => $package_array->price,
                    'vendor_type' => 'doctor',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_packages', $package_list);
            }
            }
            $this->db->query("DELETE FROM `vendor_offers` WHERE vendor_id='$id' and vendor_type='doctor'");
            if($offer!=''){
            $offer_json = '{"inbox":' . $offer . '}';
            $offer_data = json_decode($offer_json);
            foreach ($offer_data->inbox as $offer_array) {
                $offer_list = array(
                    'name' => $offer_array->name,
                    'price' => $offer_array->price,
                    'end_date' => $offer_array->end_date,
                    'vendor_type' => 'doctor',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_offers', $offer_list);
            }
            }
            $this->db->query("DELETE FROM `vendor_awards` WHERE vendor_id='$id' and vendor_type='doctor'");
            if($awards!=''){
            $awards_json = '{"inbox":' . $awards . '}';
            $awards_data = json_decode($awards_json);
            foreach ($awards_data->inbox as $awards_array) {
                $awards_list = array(
                    'name' => $awards_array->name,
                    'date' => $awards_array->date,
                    'vendor_type' => 'doctor',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_awards', $awards_list);
            }
            }
            return array(
                'status' => 200,
                'message' => 'success'
            );
        } else {
            return array(
                'status' => 200,
                'message' => 'failure'
            );
        }
    }

    public function add_pharmacy_form($data,$user,$package,$offer) {
        $insert_list = $this->db->insert('users', $user);
        $id = $this->db->insert_id();
        $data['user_id']=$id;
        $insert_list = $this->db->insert('medical_stores', $data);
        if ($insert_list) {
            $vendor_discount = array(
                'vendor_id' => $id,
                'discount_min' => 0,
                'discount_max' => 50,
                'discount_type' => 'percent'
            );
            $this->db->insert('vendor_discount', $vendor_discount);
            if($package!=''){
            $package_json = '{"inbox":' . $package . '}';
            $package_data = json_decode($package_json);
            foreach ($package_data->inbox as $package_array) {
                $package_list = array(
                    'name' => $package_array->name,
                    'price' => $package_array->price,
                    'vendor_type' => 'pharmacy',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_packages', $package_list);
            }
            }
            if($offer!=''){
            $offer_json = '{"inbox":' . $offer . '}';
            $offer_data = json_decode($offer_json);
            foreach ($offer_data->inbox as $offer_array) {
                $offer_list = array(
                    'name' => $offer_array->name,
                    'price' => $offer_array->price,
                    'end_date' => $offer_array->end_date,
                    'vendor_type' => 'pharmacy',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_offers', $offer_list);
            }
            }
            return array(
                'status' => 200,
                'message' => 'success',
                'id' => $id
            );
        } else {
            return array(
                'status' => 200,
                'message' => 'failure',
                'id' => '0'
            );
        }
    }

    public function update_pharmacy_form($data,$user,$id,$package,$offer) {
        $this->db->where('id', $id);
        $update_list = $this->db->UPDATE('users', $user);
        $this->db->where('user_id', $id);
        $update_list = $this->db->UPDATE('medical_stores', $data);
        if ($update_list) {
            $this->db->query("DELETE FROM `vendor_packages` WHERE vendor_id='$id' and vendor_type='pharmacy'");
            if($package!=''){
            $package_json = '{"inbox":' . $package . '}';
            $package_data = json_decode($package_json);
            foreach ($package_data->inbox as $package_array) {
                $package_list = array(
                    'name' => $package_array->name,
                    'price' => $package_array->price,
                    'vendor_type' => 'pharmacy',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_packages', $package_list);
            }
            }
            $this->db->query("DELETE FROM `vendor_offers` WHERE vendor_id='$id' and vendor_type='pharmacy'");
            if($offer!=''){
            $offer_json = '{"inbox":' . $offer . '}';
            $offer_data = json_decode($offer_json);
            foreach ($offer_data->inbox as $offer_array) {
                $offer_list = array(
                    'name' => $offer_array->name,
                    'price' => $offer_array->price,
                    'end_date' => $offer_array->end_date,
                    'vendor_type' => 'pharmacy',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_offers', $offer_list);
            }
            }
            return array(
                'status' => 200,
                'message' => 'success'
            );
        } else {
            return array(
                'status' => 200,
                'message' => 'failure'
            );
        }
    }

    public function update_vendor_form($data,$branch,$user,$id,$awards,$package,$offer,$vendor_type,$updated_vendor_type) {
        $this->db->where('id', $id);
        $update_list = $this->db->UPDATE('users', $user);
        $this->db->where('user_id', $id);
        $update_list = $this->db->UPDATE('fitness_center', $data);
        $this->db->where('user_id', $id);
        $update_list = $this->db->UPDATE('fitness_center_branch', $branch);
        if ($update_list) {
            $this->db->query("DELETE FROM `vendor_packages` WHERE vendor_id='$id' and vendor_type='$vendor_type'");
            if($package!=''){
            $package_json = '{"inbox":' . $package . '}';
            $package_data = json_decode($package_json);
            foreach ($package_data->inbox as $package_array) {
                $package_list = array(
                    'name' => $package_array->name,
                    'price' => $package_array->price,
                    'vendor_type' => $updated_vendor_type,
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_packages', $package_list);
            }
            }
            $this->db->query("DELETE FROM `vendor_offers` WHERE vendor_id='$id' and vendor_type='$vendor_type'");
            if($offer!=''){
            $offer_json = '{"inbox":' . $offer . '}';
            $offer_data = json_decode($offer_json);
            foreach ($offer_data->inbox as $offer_array) {
                $offer_list = array(
                    'name' => $offer_array->name,
                    'price' => $offer_array->price,
                    'end_date' => $offer_array->end_date,
                    'vendor_type' => $updated_vendor_type,
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_offers', $offer_list);
            }
            }
            $this->db->query("DELETE FROM `vendor_awards` WHERE vendor_id='$id' and vendor_type='$vendor_type'");
            if($awards!=''){
            $awards_json = '{"inbox":' . $awards . '}';
            $awards_data = json_decode($awards_json);
            foreach ($awards_data->inbox as $awards_array) {
                $awards_list = array(
                    'name' => $awards_array->name,
                    'date' => $awards_array->date,
                    'vendor_type' => $updated_vendor_type,
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_awards', $awards_list);
            }
            }
            return array(
                'status' => 200,
                'message' => 'success'
            );
        } else {
            return array(
                'status' => 200,
                'message' => 'failure'
            );
        }
    }

    public function add_vendor_form($data,$branch,$user,$awards,$package,$offer,$vendor_type) {
        date_default_timezone_set('Asia/Kolkata');
        $created_date = date('Y-m-d H:i:s');
        
        $insert_list = $this->db->insert('users', $user);
        $id = $this->db->insert_id();
        $data['user_id']=$id;
        $insert_list = $this->db->insert('fitness_center', $data);
        if ($insert_list) {
            $vendor_discount = array(
                'vendor_id' => $id,
                'discount_min' => 0,
                'discount_max' => 50,
                'discount_type' => 'percent'
            );
            $this->db->insert('vendor_discount', $vendor_discount);
        $branch['user_id']=$id;
        $insert_branch = $this->db->insert('fitness_center_branch', $branch);
            if($package!=''){
            $package_json = '{"inbox":' . $package . '}';
            $package_data = json_decode($package_json);
            foreach ($package_data->inbox as $package_array) {
                $package_list = array(
                    'name' => $package_array->name,
                    'price' => $package_array->price,
                    'vendor_type' => $vendor_type,
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_packages', $package_list);
            }
            }
            if($offer!=''){
            $offer_json = '{"inbox":' . $offer . '}';
            $offer_data = json_decode($offer_json);
            foreach ($offer_data->inbox as $offer_array) {
                $offer_list = array(
                    'name' => $offer_array->name,
                    'price' => $offer_array->price,
                    'end_date' => $offer_array->end_date,
                    'vendor_type' => $vendor_type,
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_offers', $offer_list);
            }
            }
            if($awards!=''){
            $awards_json = '{"inbox":' . $awards . '}';
            $awards_data = json_decode($awards_json);
            foreach ($awards_data->inbox as $awards_array) {
                $awards_list = array(
                    'name' => $awards_array->name,
                    'date' => $awards_array->date,
                    'vendor_type' => $vendor_type,
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_awards', $awards_list);
            }
            }
            return array(
                'status' => 200,
                'message' => 'success',
                'id' => $id
            );
        } else {
            return array(
                'status' => 200,
                'message' => 'failure',
                'id' => '0'
            );
        }
    }

    public function add_hospital_form($data,$branch,$user,$package,$offer) {
        $insert_list = $this->db->insert('users', $user);
        $id = $this->db->insert_id();
        $data['user_id']=$id;
        $insert_list = $this->db->insert('hospitals', $data);
        if ($insert_list) {
            $vendor_discount = array(
                'vendor_id' => $id,
                'discount_min' => 0,
                'discount_max' => 50,
                'discount_type' => 'percent'
            );
            $this->db->insert('vendor_discount', $vendor_discount);
            $branch['hospital_id']=$id;
            $insert_clinic = $this->db->insert('hospitals_branch', $branch);
            if($package!=''){
            $package_json = '{"inbox":' . $package . '}';
            $package_data = json_decode($package_json);
            foreach ($package_data->inbox as $package_array) {
                $package_list = array(
                    'name' => $package_array->name,
                    'price' => $package_array->price,
                    'vendor_type' => 'hospital',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_packages', $package_list);
            }
            }
            if($offer!=''){
            $offer_json = '{"inbox":' . $offer . '}';
            $offer_data = json_decode($offer_json);
            foreach ($offer_data->inbox as $offer_array) {
                $offer_list = array(
                    'name' => $offer_array->name,
                    'price' => $offer_array->price,
                    'end_date' => $offer_array->end_date,
                    'vendor_type' => 'hospital',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_offers', $offer_list);
            }
            }
            return array(
                'status' => 200,
                'message' => 'success',
                'id' => $id
            );
        } else {
            return array(
                'status' => 200,
                'message' => 'failure',
                'id' => '0'
            );
        }
    }

    public function update_hospital_form($data,$branch,$user,$id,$package,$offer) {
        $this->db->where('id', $id);
        $update_list = $this->db->UPDATE('users', $user);
        $this->db->where('user_id', $id);
        $update_list = $this->db->UPDATE('hospitals', $data);
        $this->db->where('hospital_id', $id);
        $update_list = $this->db->UPDATE('hospitals_branch', $branch);
        if ($update_list) {
            $this->db->query("DELETE FROM `vendor_packages` WHERE vendor_id='$id' and vendor_type='hospital'");
            if($package!=''){
            $package_json = '{"inbox":' . $package . '}';
            $package_data = json_decode($package_json);
            foreach ($package_data->inbox as $package_array) {
                $package_list = array(
                    'name' => $package_array->name,
                    'price' => $package_array->price,
                    'vendor_type' => 'hospital',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_packages', $package_list);
            }
            }
            if($offer!=''){
            $this->db->query("DELETE FROM `vendor_offers` WHERE vendor_id='$id' and vendor_type='hospital'");
            $offer_json = '{"inbox":' . $offer . '}';
            $offer_data = json_decode($offer_json);
            foreach ($offer_data->inbox as $offer_array) {
                $offer_list = array(
                    'name' => $offer_array->name,
                    'price' => $offer_array->price,
                    'end_date' => $offer_array->end_date,
                    'vendor_type' => 'hospital',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_offers', $offer_list);
            }
            }
            return array(
                'status' => 200,
                'message' => 'success'
            );
        } else {
            return array(
                'status' => 200,
                'message' => 'failure'
            );
        }
    }

    public function update_lab_form($data,$branch,$user,$id,$package,$offer) {
        $this->db->where('id', $id);
        $update_list = $this->db->UPDATE('users', $user);
        $this->db->where('user_id', $id);
        $update_list = $this->db->UPDATE('lab_center', $data);
        $this->db->where('user_id', $id);
        $update_list = $this->db->UPDATE('lab_center_branch', $branch);
        if ($update_list) {
            $this->db->query("DELETE FROM `vendor_packages` WHERE vendor_id='$id' and vendor_type='lab'");
            if($package!=''){
            $package_json = '{"inbox":' . $package . '}';
            $package_data = json_decode($package_json);
            foreach ($package_data->inbox as $package_array) {
                $package_list = array(
                    'name' => $package_array->name,
                    'price' => $package_array->price,
                    'vendor_type' => 'lab',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_packages', $package_list);
            }
            }
            $this->db->query("DELETE FROM `vendor_offers` WHERE vendor_id='$id' and vendor_type='lab'");
            if($offer!=''){
            $offer_json = '{"inbox":' . $offer . '}';
            $offer_data = json_decode($offer_json);
            foreach ($offer_data->inbox as $offer_array) {
                $offer_list = array(
                    'name' => $offer_array->name,
                    'price' => $offer_array->price,
                    'end_date' => $offer_array->end_date,
                    'vendor_type' => 'lab',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_offers', $offer_list);
            }
            }
            return array(
                'status' => 200,
                'message' => 'success'
            );
        } else {
            return array(
                'status' => 200,
                'message' => 'failure'
            );
        }
    }

    public function add_lab_form($data,$branch,$user,$package,$offer) {
        $insert_list = $this->db->insert('users', $user);
        $id = $this->db->insert_id();
        $data['user_id']=$id;
        $insert_list = $this->db->insert('lab_center', $data);
        if ($insert_list) {
            $vendor_discount = array(
                'vendor_id' => $id,
                'discount_min' => 0,
                'discount_max' => 50,
                'discount_type' => 'percent'
            );
            $this->db->insert('vendor_discount', $vendor_discount);
            $branch['user_id']=$id;
            $insert = $this->db->insert('lab_center_branch', $branch);
            if($package!=''){
            $package_json = '{"inbox":' . $package . '}';
            $package_data = json_decode($package_json);
            foreach ($package_data->inbox as $package_array) {
                $package_list = array(
                    'name' => $package_array->name,
                    'price' => $package_array->price,
                    'vendor_type' => 'lab',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_packages', $package_list);
            }
            }
            if($offer!=''){
            $offer_json = '{"inbox":' . $offer . '}';
            $offer_data = json_decode($offer_json);
            foreach ($offer_data->inbox as $offer_array) {
                $offer_list = array(
                    'name' => $offer_array->name,
                    'price' => $offer_array->price,
                    'end_date' => $offer_array->end_date,
                    'vendor_type' => 'lab',
                    'vendor_id' => $id
                );
                $this->db->insert('vendor_offers', $offer_list);
            }
            }
            return array(
                'status' => 200,
                'message' => 'success',
                'id' => $id
            );
        } else {
            return array(
                'status' => 200,
                'message' => 'failure',
                'id' => '0'
            );
        }
    }

    public function doctor_form_list($user_id) {
        $query = $this->db->query("SELECT * FROM `doctor_list`  order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            $profile_image = '';
            foreach ($query->result_array() as $row) {
                $id = $row['user_id'];
                $executive_name = $row['executive_name'];
                $business_nature = $row['business_nature'];
                $name = $row['doctor_name'];
                $address = $row['address'];
                $email = $row['email'];
                $phone = $row['telephone'];
                $since = $row['since'];
                $area_expertise = $row['area_expertise'];
                $degree = $row['qualification'];
                $dob = $row['dob'];
                $gender = $row['gender'];
                $chat_fees = $row['consultaion_chat'];
                $visit_fees = $row['consultation_fee'];
                $video_fees = $row['consultaion_video'];
                $audio_fees = $row['consultation_voice_call'];
                $medicalwale_discount = $row['medicale_discount'];
                
                $doctor_id = $row['user_id'];
                $clinic_query = $this->db->query("SELECT * FROM `doctor_clinic` where doctor_id='$doctor_id' order by id desc");
                $clinic = $clinic_query->row_array();
                $clinic_name = $clinic['clinic_name'];
                $clinic_contact = $clinic['contact_no'];
                $clinic_address = $clinic['address'];
                $clinic_city = $clinic['city'];
                $clinic_state = $clinic['state'];
                $clinic_pincode = $clinic['pincode'];
                $consultation_fees = $clinic['consultation_charges'];
                $working_hours = $clinic['open_hours'];
                $locator = $clinic['map_location'];
                $lat = $clinic['lat'];
                $lng = $clinic['lng'];
                
                $online_offline = $row['online_offline'];
                $online_time = $row['online_time'];
                $person_name = $row['person_name'];
                $person_address = $row['person_address'];
                $person_city = $row['person_city'];
                $person_pincode = $row['person_pincode'];
                $person_state = $row['person_state'];
                $person_country = $row['person_country'];
                $person_telephone = $row['person_telephone'];
                $person_email = $row['person_email'];
                $person_phone = $row['person_phone'];
                $account_no = $row['account_no'];
                $account_type = $row['account_type'];
                $bank_name = $row['bank_name'];
                $ifsc_code = $row['ifsc_code'];
                $feedback = $row['feedback'];
                $profile_image = $row['image'];
                $clinic_image = $clinic['image'];
                $vendor_sign = $row['vendor_sign'];
                $sign = $row['sign'];
                if ($profile_image != '') {
                    $profile_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $row['image'];
                }
                if ($clinic_image != '') {
                    $clinic_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sales_form_files/' . $clinic['image'];
                }
                if ($vendor_sign != '') {
                    $vendor_sign = 'https://d2c8oti4is0ms3.cloudfront.net/images/sales_form_files/' . $row['vendor_sign'];
                }
                if ($sign != '') {
                    $sign = 'https://d2c8oti4is0ms3.cloudfront.net/images/sales_form_files/' . $row['sign'];
                }
                $package = '';
                $package_query = $this->db->query("SELECT name,price FROM `vendor_packages` where vendor_id='$id' and vendor_type='doctor' order by id asc");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package[] = array('name' => $package_row['name'], 'price' => $package_row['price']);
                    }
                    $package = json_encode($package);
                }
                $offer = '';
                $offer_query = $this->db->query("SELECT name,price,end_date FROM `vendor_offers` where vendor_id='$id' and vendor_type='doctor' order by id asc");
                $offer_count = $offer_query->num_rows();
                if ($offer_count > 0) {
                    foreach ($offer_query->result_array() as $offer_row) {
                        $offer[] = array('name' => $offer_row['name'], 'price' => $offer_row['price'], 'end_date' => $offer_row['end_date']);
                    }
                    $offer = json_encode($offer);
                }
                $awards = '';
                $awards_query = $this->db->query("SELECT name,date FROM `vendor_awards` where vendor_id='$id' and vendor_type='doctor' order by id asc");
                $awards_count = $awards_query->num_rows();
                if ($offer_count > 0) {
                    foreach ($awards_query->result_array() as $awards_row) {
                        $awards[] = array('name' => $awards_row['name'], 'date' => $awards_row['date']);
                    }
                    $awards = json_encode($awards);
                }

                $result[] = array(
                    "id" => $id,
                    "executive_name" => $executive_name,
                    "business_nature" => $business_nature,
                    "name" => $name,
                    "address" => $address,
                    "email" => $email,
                    "phone" => $phone,
                    "since" => $since,
                    "area_expertise" => $area_expertise,
                    "degree" => $degree,
                    "dob" => $dob,
                    "gender" => $gender,
                    "chat_fees" => $chat_fees,
                    "visit_fees" => $visit_fees,
                    "video_fees" => $video_fees,
                    "audio_fees" => $audio_fees,
                    "medicalwale_discount" => $medicalwale_discount,
                    "clinic_name" => $clinic_name,
                    "clinic_contact" => $clinic_contact,
                    "clinic_address" => $clinic_address,
                    "clinic_city" => $clinic_city,
                    "clinic_state" => $clinic_state,
                    "clinic_pincode" => $clinic_pincode,
                    "lat" => $lat,
                    "lng" => $lng,
                    "consultation_fees" => $consultation_fees,
                    "working_hours" => $working_hours,
                    "locator" => $locator,
                    "online_offline" => $online_offline,
                    "online_time" => $online_time,
                    "person_name" => $person_name,
                    "person_address" => $person_address,
                    "person_city" => $person_city,
                    "person_pincode" => $person_pincode,
                    "person_state" => $person_state,
                    "person_country" => $person_country,
                    "person_telephone" => $person_telephone,
                    "person_email" => $person_email,
                    "person_phone" => $person_phone,
                    "account_no" => $account_no,
                    "account_type" => $account_type,
                    "bank_name" => $bank_name,
                    "ifsc_code" => $ifsc_code,
                    "feedback" => $feedback,
                    "package" => $package,
                    "offer" => $offer,
                    "awards" => $awards,
                    "profile_image" => $profile_image,
                    "clinic_image" => $clinic_image,
                    "vendor_sign" => $vendor_sign,
                    "sign" => $sign
                );
            }
            return $result;
        } else {
            $result = array();
            return $result;
        }
    }

    public function vendor_pharmacy_list($user_id) {
        $query = $this->db->query("SELECT * FROM `medical_stores`  order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            $profile_image = '';
            foreach ($query->result_array() as $row) {
                $id = $row['user_id'];
                $executive_name = $row['executive_name'];
                $manager_name = $row['store_manager'];
                $business_nature = $row['business_nature'];
                $name = $row['medical_name'];
                $address = $row['address1'];
                $email = $row['email'];
                $phone = $row['contact_no'];
                $since = $row['store_since'];
                $medicalwale_discount = $row['medicalwale_discount'];
                $radius_area = $row['reach_area'];
                $store_time = $row['store_time'];
                $is_24_hours = $row['is_24hrs_available'];
                $non_working_day = $row['days_closed'];
                $delivery_time = $row['delivery_time'];
                $free_home_delivery = $row['free_home_delivery'];
                $minimum_order_amount = $row['min_order'];
                $minimum_order_delivery = $row['min_order_delivery_charge'];
                $night_delivery_amount = $row['night_delivery_charge'];
                $online_offline = $row['online_offline'];
                $person_name = $row['person_name'];
                $person_address = $row['person_address'];
                $person_city = $row['person_city'];
                $person_pincode = $row['person_pincode'];
                $person_state = $row['person_state'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $person_country = $row['person_country'];
                $person_telephone = $row['person_telephone'];
                $person_email = $row['person_email'];
                $person_phone = $row['person_phone'];
                $account_no = $row['account_no'];
                $account_type = $row['account_type'];
                $bank_name = $row['bank_name'];
                $ifsc_code = $row['ifsc_code'];
                $feedback = $row['feedback'];
                $profile_image = $row['profile_pic'];
                $vendor_sign = $row['vendor_sign'];
                $sign = $row['sign'];
                if ($profile_image != '') {
                    $profile_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $row['profile_pic'];
                }
                if ($vendor_sign != '') {
                    $vendor_sign = 'https://d2c8oti4is0ms3.cloudfront.net/images/sales_form_files/' . $row['vendor_sign'];
                }
                if ($sign != '') {
                    $sign = 'https://d2c8oti4is0ms3.cloudfront.net/images/sales_form_files/' . $row['sign'];
                }
                $package = '';
                $package_query = $this->db->query("SELECT name,price FROM `vendor_packages` where vendor_id='$id' and vendor_type='pharmacy' order by id asc");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package[] = array('name' => $package_row['name'], 'price' => $package_row['price']);
                    }
                    $package = json_encode($package);
                }
                $offer = '';
                $offer_query = $this->db->query("SELECT name,price,end_date FROM `vendor_offers` where vendor_id='$id' and vendor_type='pharmacy' order by id asc");
                $offer_count = $offer_query->num_rows();
                if ($offer_count > 0) {
                    foreach ($offer_query->result_array() as $offer_row) {
                        $offer[] = array('name' => $offer_row['name'], 'price' => $offer_row['price'], 'end_date' => $offer_row['end_date']);
                    }
                    $offer = json_encode($offer);
                }

                $result[] = array(
                    "id" => $id,
                    "executive_name" => $executive_name,
                    "manager_name" => $manager_name,
                    "business_nature" => $business_nature,
                    "name" => $name,
                    "address" => $address,
                    "email" => $email,
                    "phone" => $phone,
                    "since" => $since,
                    "medicalwale_discount" => $medicalwale_discount,
                    "radius_area" => $radius_area,
                    "store_time" => $store_time,
                    "is_24_hours" => $is_24_hours,
                    "non_working_day" => $non_working_day,
                    "delivery_time" => $delivery_time,
                    "free_home_delivery" => $free_home_delivery,
                    "minimum_order_amount" => $minimum_order_amount,
                    "minimum_order_delivery" => $minimum_order_delivery,
                    "night_delivery_amount" => $night_delivery_amount,
                    "online_offline" => $online_offline,
                    "lat" => $lat,
                    "lng" => $lng,
                    "person_name" => $person_name,
                    "person_address" => $person_address,
                    "person_city" => $person_city,
                    "person_pincode" => $person_pincode,
                    "person_state" => $person_state,
                    "person_country" => $person_country,
                    "person_telephone" => $person_telephone,
                    "person_email" => $person_email,
                    "person_phone" => $person_phone,
                    "account_no" => $account_no,
                    "account_type" => $account_type,
                    "bank_name" => $bank_name,
                    "ifsc_code" => $ifsc_code,
                    "feedback" => $feedback,
                    "package" => $package,
                    "offer" => $offer,
                    "profile_image" => $profile_image,
                    "vendor_sign" => $vendor_sign,
                    "sign" => $sign
                );
            }
            return $result;
        } else {
            $result = array();
            return $result;
        }
    }

    public function vendor_form_list($user_id, $vendor_type) {
        $query = $this->db->query("SELECT * FROM `fitness_center` where vendor_type='$vendor_type' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['user_id'];
                $vendor_type = $row['vendor_type'];
                $business_nature = $row['business_nature'];
                $name = $row['center_name'];
                $email = $row['email'];
                $phone = $row['contact'];
                $since = $row['year'];
                $medicalwale_discount = $row['medicalwale_discount'];
                
                $listing_id = $row['user_id'];
                $branch_query = $this->db->query("SELECT * FROM `fitness_center_branch` where user_id='$listing_id' order by id desc");
                $branch = $branch_query->row_array();
                $branch_name = $branch['branch_name'];
                $branch_address = $branch['branch_address'];
                $branch_city = $branch['city'];
                $branch_state = $branch['state'];
                $branch_pincode = $branch['pincode'];
                $branch_contact = $branch['branch_phone'];
                $bussiness_category = $branch['branch_business_category'];
                $about = $branch['about_branch'];
                $services = $branch['branch_offer'];
                $facility = $branch['branch_facilities'];
                $locator = $branch['map_location'];
                $free_trail = $branch['is_free_trail'];
                $working_hours = $branch['opening_hours'];
                $lat = $branch['lat'];
                $lng = $branch['lng'];
                
                $online_offline = $row['online_offline'];
                $person_name = $row['person_name'];
                $person_address = $row['person_address'];
                $person_city = $row['person_city'];
                $person_pincode = $row['person_pincode'];
                $person_state = $row['person_state'];
                $person_country = $row['person_country'];
                $person_telephone = $row['person_telephone'];
                $person_email = $row['person_email'];
                $person_phone = $row['person_phone'];
                $account_no = $row['account_no'];
                $bank_name = $row['bank_name'];
                $account_type = $row['account_type'];
                $ifsc_code = $row['ifsc_code'];
                $executive_name = $row['executive_name'];
                $feedback = $row['feedback'];
                $manager_name = $row['manager_name'];
                $profile_image = $row['image'];
                
                $listing_id = $row['user_id'];
                $branch_query = $this->db->query("SELECT * FROM `fitness_center_branch` where user_id='$listing_id' order by id desc");
                $branch = $branch_query->row_array();
                $branch_image = $branch['branch_image'];
                $gallery = $branch['gallery'];
                
                $vendor_sign = $row['vendor_sign'];
                $sign = $row['sign'];
                if ($profile_image != '') {
                    $profile_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $row['image'];
                }
                if ($branch_image != '') {
                    $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sales_form_files/' . $branch['branch_image'];
                }
                if ($gallery != '') {
                    $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/sales_form_files/' . $branch['gallery'];
                }
                if ($vendor_sign != '') {
                    $vendor_sign = 'https://d2c8oti4is0ms3.cloudfront.net/images/sales_form_files/' . $row['vendor_sign'];
                }
                if ($sign != '') {
                    $sign = 'https://d2c8oti4is0ms3.cloudfront.net/images/sales_form_files/' . $row['sign'];
                }
                $package = '';
                $package_query = $this->db->query("SELECT name,price FROM `vendor_packages` where vendor_id='$id' and vendor_type='$vendor_type' order by id asc");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package[] = array('name' => $package_row['name'], 'price' => $package_row['price']);
                    }
                    $package = json_encode($package);
                }
                $offer = '';
                $offer_query = $this->db->query("SELECT name,price,end_date FROM `vendor_offers` where vendor_id='$id' and vendor_type='$vendor_type' order by id asc");
                $offer_count = $offer_query->num_rows();
                if ($offer_count > 0) {
                    foreach ($offer_query->result_array() as $offer_row) {
                        $offer[] = array('name' => $offer_row['name'], 'price' => $offer_row['price'], 'end_date' => $offer_row['end_date']);
                    }
                    $offer = json_encode($offer);
                }
                $awards = '';
                $awards_query = $this->db->query("SELECT name,date FROM `vendor_awards` where vendor_id='$id' and vendor_type='$vendor_type' order by id asc");
                $awards_count = $awards_query->num_rows();
                if ($offer_count > 0) {
                    foreach ($awards_query->result_array() as $awards_row) {
                        $awards[] = array('name' => $awards_row['name'], 'date' => $awards_row['date']);
                    }
                    $awards = json_encode($awards);
                }


                $result[] = array(
                    "id" => $id,
                    "executive_name" => $executive_name,
                    "profile_image" => $profile_image,
                    "branch_image" => $branch_image,
                    "gallery" => $gallery,
                    "vendor_type" => $vendor_type,
                    "business_nature" => $business_nature,
                    "name" => $name,
                    "email" => $email,
                    "phone" => $phone,
                    "since" => $since,
                    "medicalwale_discount" => $medicalwale_discount,
                    "manager_name" => $manager_name,
                    "branch_name" => $branch_name,
                    "branch_address" => $branch_address,
                    "branch_city" => $branch_city,
                    "branch_state" => $branch_state,
                    "branch_pincode" => $branch_pincode,
                    "branch_contact" => $branch_contact,
                    "lat" => $lat,
                    "lng" => $lng,
                    "bussiness_category" => $bussiness_category,
                    "about" => $about,
                    "services" => $services,
                    "facility" => $facility,
                    "locator" => $locator,
                    "free_trail" => $free_trail,
                    "working_hours" => $working_hours,
                    "online_offline" => $online_offline,
                    "person_name" => $person_name,
                    "person_address" => $person_address,
                    "person_city" => $person_city,
                    "person_pincode" => $person_pincode,
                    "person_state" => $person_state,
                    "person_country" => $person_country,
                    "person_telephone" => $person_telephone,
                    "person_email" => $person_email,
                    "person_phone" => $person_phone,
                    "account_no" => $account_no,
                    "bank_name" => $bank_name,
                    "account_type" => $account_type,
                    "ifsc_code" => $ifsc_code,
                    "vendor_sign" => $vendor_sign,
                    "feedback" => $feedback,
                    "package" => $package,
                    "offer" => $offer,
                    "awards" => $awards,
                    "sign" => $sign
                );
            }
            return $result;
        } else {
            $result = array();
            return $result;
        }
    }

    public function vendor_hospital_list($user_id) {
        $query = $this->db->query("SELECT * FROM `hospitals`  order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['user_id'];
                $executive_name = $row['executive_name'];
                $business_nature = $row['business_nature'];
                $name = $row['name_of_hospital'];
                $address = $row['address'];
                $email = $row['email'];
                $phone = $row['phone'];
                $concern_person_name = $row['concern_person_name'];
                $concern_person_phone = $row['concern_person_phone'];
                $since = $row['establishment_year'];
                $medicalwale_discount = $row['medicalwale_discount'];
                $no_branches = $row['no_branches'];
                $no_doctors = $row['no_doctors'];
                
                $listing_id = $row['user_id'];
                $branch_query = $this->db->query("SELECT * FROM `hospitals_branch` where hospital_id='$listing_id' order by id desc");
                $branch = $branch_query->row_array();
                $branch_name = $branch['name_of_branch'];
                $branch_email = $branch['email'];
                $branch_contact = $branch['phone'];
                $branch_address = $branch['address'];
                $branch_city = $branch['city'];
                $branch_state = $branch['state'];
                $branch_pincode = $branch['pincode'];
                $branch_establishment_year = $branch['establishment_year'];
                $about = $branch['about_us'];
                $services = $branch['services'];
                $facility = $branch['facility'];
                $locator = $branch['map_location'];
                $speciality = $branch['speciality'];
                $doctor_name = $branch['doctor_name'];
                $doctor_qualifications = $branch['doctor_qualifications'];
                $working_hours = $branch['working_hours'];
                $visiting_hours = $branch['visiting_hours'];
                $lat = $branch['lat'];
                $lng = $branch['lng'];
                
                $online_offline = $row['online_offline'];
                $person_name = $row['person_name'];
                $person_address = $row['person_address'];
                $person_city = $row['person_city'];
                $person_pincode = $row['person_pincode'];
                $person_state = $row['person_state'];
                $person_country = $row['person_country'];
                $person_telephone = $row['person_telephone'];
                $person_email = $row['person_email'];
                $person_phone = $row['person_phone'];
                $account_no = $row['account_no'];
                $bank_name = $row['bank_name'];
                $account_type = $row['account_type'];
                $ifsc_code = $row['ifsc_code'];
                $feedback = $row['feedback'];
                $profile_image = $row['image'];
                
                $doctor_profile_image = $branch['doctor_profile_image'];
                $gallery = $branch['gallery'];
                
                $vendor_sign = $row['vendor_sign'];
                $sign = $row['sign'];
                if ($profile_image != '') {
                    $profile_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $row['image'];
                }
                if ($doctor_profile_image != '') {
                    $doctor_profile_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sales_form_files/' . $branch['doctor_profile_image'];
                }
                if ($gallery != '') {
                    $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/sales_form_files/' . $branch['gallery'];
                }
                if ($vendor_sign != '') {
                    $vendor_sign = 'https://d2c8oti4is0ms3.cloudfront.net/images/sales_form_files/' . $row['vendor_sign'];
                }
                if ($sign != '') {
                    $sign = 'https://d2c8oti4is0ms3.cloudfront.net/images/sales_form_files/' . $row['sign'];
                }
                $package = '';
                $package_query = $this->db->query("SELECT name,price FROM `vendor_packages` where vendor_id='$id' and vendor_type='hospital' order by id asc");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package[] = array('name' => $package_row['name'], 'price' => $package_row['price']);
                    }
                    $package = json_encode($package);
                }
                $offer = '';
                $offer_query = $this->db->query("SELECT name,price,end_date FROM `vendor_offers` where vendor_id='$id' and vendor_type='hospital' order by id asc");
                $offer_count = $offer_query->num_rows();
                if ($offer_count > 0) {
                    foreach ($offer_query->result_array() as $offer_row) {
                        $offer[] = array('name' => $offer_row['name'], 'price' => $offer_row['price'], 'end_date' => $offer_row['end_date']);
                    }
                    $offer = json_encode($offer);
                }


                $result[] = array(
                    "id" => $id,
                    "executive_name" => $executive_name,
                    "business_nature" => $business_nature,
                    "name" => $name,
					"address" => $address,
                    "email" => $email,
                    "phone" => $phone,
                    "concern_person_name" => $concern_person_name,
                    "concern_person_phone" => $concern_person_phone,
                    "since" => $since,
                    "medicalwale_discount" => $medicalwale_discount,
                    "no_branches" => $no_branches,
                    "no_doctors" => $no_doctors,
                    "branch_name" => $branch_name,
                    "branch_email" => $branch_email,
                    "branch_contact" => $branch_contact,
                    "branch_address" => $branch_address,
                    "branch_city" => $branch_city,
                    "branch_state" => $branch_state,
                    "branch_pincode" => $branch_pincode,
                    "branch_establishment_year" => $branch_establishment_year,
                    "lat" => $lat,
                    "lng" => $lng,
                    "about" => $about,
                    "services" => $services,
                    "facility" => $facility,
                    "locator" => $locator,
                    "speciality" => $speciality,
                    "doctor_name" => $doctor_name,
                    "doctor_qualifications" => $doctor_qualifications,
                    "working_hours" => $working_hours,
                    "visiting_hours" => $visiting_hours,
                    "online_offline" => $online_offline,
                    "person_name" => $person_name,
                    "person_address" => $person_address,
                    "person_city" => $person_city,
                    "person_pincode" => $person_pincode,
                    "person_state" => $person_state,
                    "person_country" => $person_country,
                    "person_telephone" => $person_telephone,
                    "person_email" => $person_email,
                    "person_phone" => $person_phone,
                    "account_no" => $account_no,
                    "bank_name" => $bank_name,
                    "account_type" => $account_type,
                    "ifsc_code" => $ifsc_code,
                    "feedback" => $feedback,
                    "package" => $package,
                    "offer" => $offer,
                    "profile_image" => $profile_image,
                    "doctor_profile_image" => $doctor_profile_image,
                    "gallery" => $gallery,
                    "vendor_sign" => $vendor_sign,
                    "sign" => $sign
                );
            }
            return $result;
        } else {
            $result = array();
            return $result;
        }
    }

    public function lab_form_list($user_id) {
        $query = $this->db->query("SELECT * FROM `lab_center` order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['user_id'];
                $executive_name = $row['executive_name'];
                $business_nature = $row['business_nature'];
                $name = $row['lab_name'];
                $email = $row['email'];
                $phone = $row['contact_no'];
                $since = $row['store_since'];
                $no_branches = $row['no_branches'];
                $medicalwale_discount = $row['medicalwale_discount'];
                
                $listing_id = $row['user_id'];
                $branch_query = $this->db->query("SELECT * FROM `lab_center_branch` where user_id='$listing_id' order by id desc");
                $branch = $branch_query->row_array();
                $branch_name = $branch['lab_branch_name'];
                $branch_contact = $branch['contact_no'];
                $branch_address = $branch['address1'];
                $branch_city = $branch['city'];
                $branch_state = $branch['state'];
                $branch_pincode = $branch['pincode'];
                $branch_contact = $branch['contact_no'];
                $branch_email = $branch['email'];
                $payment_mode = $branch['payment_type'];
                $services = $branch['services'];
                $home_visit = $branch['home_visit'];
                $working_hours = $branch['opening_hours'];
                $test_list = $branch['test_list'];
                $lat = $branch['latitude'];
                $lng = $branch['longitude'];
                
                $online_offline = $row['online_offline'];
                $person_name = $row['person_name'];
                $person_address = $row['person_address'];
                $person_city = $row['person_city'];
                $person_pincode = $row['person_pincode'];
                $person_state = $row['person_state'];
                $person_country = $row['person_country'];
                $person_telephone = $row['person_telephone'];
                $person_email = $row['person_email'];
                $person_phone = $row['person_phone'];
                $account_no = $row['account_no'];
                $bank_name = $row['bank_name'];
                $account_type = $row['account_type'];
                $ifsc_code = $row['ifsc_code'];
                $profile_image = $row['profile_pic'];
                $vendor_sign = $row['vendor_sign'];
                $sign = $row['sign'];
                $feedback = $row['feedback'];
                if ($profile_image != '') {
                    $profile_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $row['profile_pic'];
                }
                if ($vendor_sign != '') {
                    $vendor_sign = 'https://d2c8oti4is0ms3.cloudfront.net/images/sales_form_files/' . $row['vendor_sign'];
                }
                if ($sign != '') {
                    $sign = 'https://d2c8oti4is0ms3.cloudfront.net/images/sales_form_files/' . $row['sign'];
                }
                $package = '';
                $package_query = $this->db->query("SELECT name,price FROM `vendor_packages` where vendor_id='$id' and vendor_type='lab' order by id asc");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package[] = array('name' => $package_row['name'], 'price' => $package_row['price']);
                    }
                    $package = json_encode($package);
                }
                $offer = '';
                $offer_query = $this->db->query("SELECT name,price,end_date FROM `vendor_offers` where vendor_id='$id' and vendor_type='lab' order by id asc");
                $offer_count = $offer_query->num_rows();
                if ($offer_count > 0) {
                    foreach ($offer_query->result_array() as $offer_row) {
                        $offer[] = array('name' => $offer_row['name'], 'price' => $offer_row['price'], 'end_date' => $offer_row['end_date']);
                    }
                    $offer = json_encode($offer);
                }

                $result[] = array(
                    "id" => $id,
                    "executive_name" => $executive_name,
                    "profile_image" => $profile_image,
                    "business_nature" => $business_nature,
                    "name" => $name,
                    "email" => $email,
                    "phone" => $phone,
                    "since" => $since,
                    "no_branches" => $no_branches,
                    "medicalwale_discount" => $medicalwale_discount,
                    "branch_name" => $branch_name,
                    "branch_contact" => $branch_contact,
                    "branch_address" => $branch_address,
                    "branch_city" => $branch_city,
                    "branch_state" => $branch_state,
                    "branch_pincode" => $branch_pincode,
                    "branch_contact" => $branch_contact,
                    "branch_email" => $branch_email,
                    "lat" => $lat,
                    "lng" => $lng,
                    "payment_mode" => $payment_mode,
                    "services" => $services,
                    "home_visit" => $home_visit,
                    "working_hours" => $working_hours,
                    "test_list" => $test_list,
                    "online_offline" => $online_offline,
                    "person_name" => $person_name,
                    "person_address" => $person_address,
                    "person_city" => $person_city,
                    "person_pincode" => $person_pincode,
                    "person_state" => $person_state,
                    "person_country" => $person_country,
                    "person_telephone" => $person_telephone,
                    "person_email" => $person_email,
                    "person_phone" => $person_phone,
                    "account_no" => $account_no,
                    "bank_name" => $bank_name,
                    "account_type" => $account_type,
                    "ifsc_code" => $ifsc_code,
                    "feedback" => $feedback,
                    "package" => $package,
                    "offer" => $offer,
                    "vendor_sign" => $vendor_sign,
                    "sign" => $sign
                );
            }
            return $result;
        } else {
            $result = array();
            return $result;
        }
    }

    public function doctor_degree_list() {
        $query = $this->db->query("SELECT * FROM `vendor_doctor_degree` order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $doctor_degree = $row['degree'];
                $result[] = array(
                    "id" => $id,
                    "degree" => $doctor_degree
                );
            }
            return $result;
        } else {
            $result = array();
            return $result;
        }
    }

    public function business_category() {
        $query = $this->db->query("SELECT * FROM `vendor_business_category` order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $category = $row['category'];
                $result[] = array(
                    "id" => $id,
                    "category" => $category
                );
            }
            return $result;
        } else {
            $result = array();
            return $result;
        }
    }

    public function services() {
        $query = $this->db->query("SELECT * FROM `vendor_services` order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $services = $row['services'];
                $result[] = array(
                    "id" => $id,
                    "services" => $services
                );
            }
            return $result;
        } else {
            $result = array();
            return $result;
        }
    }

    public function branch_facility() {
        $query = $this->db->query("SELECT * FROM `vendor_branch_facility` order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $facility = $row['facility'];
                $result[] = array(
                    "id" => $id,
                    "facility" => $facility
                );
            }
            return $result;
        } else {
            $result = array();
            return $result;
        }
    }

    public function lab_services() {
        $query = $this->db->query("SELECT * FROM `vendor_services` where vendor_type='lab' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $services = $row['services'];
                $result[] = array(
                    "id" => $id,
                    "services" => $services
                );
            }
            return $result;
        } else {
            $result = array();
            return $result;
        }
    }
    
    public function hospital_services() {
        $query = $this->db->query("SELECT * FROM `hospital_services` order by service_name asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $services = $row['service_name'];
                $result[] = array(
                    "id" => $id,
                    "services" => $services
                );
            }
            return $result;
        } else {
            $result = array();
            return $result;
        }
    }
    
    public function hospital_speciality() {
        $query = $this->db->query("SELECT * FROM `hospitals_specialist` order by name asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $speciality = $row['name'];
                $result[] = array(
                    "id" => $id,
                    "speciality" => $speciality
                );
            }
            return $result;
        } else {
            $result = array();
            return $result;
        }
    }

    public function lab_test() {
        $query = $this->db->query("SELECT * FROM `vendor_lab_test` order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $test = $row['test'];
                $result[] = array(
                    "id" => $id,
                    "test" => $test
                );
            }
            return $result;
        } else {
            $result = array();
            return $result;
        }
    }
    
    public function area_expertise() {
        $query = $this->db->query("SELECT * FROM `business_category` where category_id='5' order by category asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $expertise = $row['category'];
                $result[] = array(
                    "id" => $id,
                    "expertise" => $expertise
                );
            }
            return $result;
        } else {
            $result = array();
            return $result;
        }
    }


}

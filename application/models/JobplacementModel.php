<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class JobplacementModel extends CI_Model { 

    var $client_service = "frontend-client";
    var $auth_key       = "medicalwalerestapi"; 

    public function check_auth_client(){
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key  = $this->input->get_request_header('Auth-Key', TRUE);
        if($client_service == $this->client_service && $auth_key == $this->auth_key){
            return true;
        } else {
            return json_output(401,array('status' => 401,'message' => 'Unauthorized.'));
        }
    }

    public function auth()
    {
        date_default_timezone_set('Asia/Kolkata');
        $users_id  = $this->input->get_request_header('User-ID', TRUE);
        $token     = $this->input->get_request_header('Authorizations', TRUE);
        $q  = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id',$users_id)->where('token',$token)->get()->row();
        if($q == ""){
            return json_output(401,array('status' => 401,'message' => 'Unauthorized.'));
        } else {
            if($q->expired_at < date('Y-m-d H:i:s')){
                return json_output(401,array('status' => 401,'message' => 'Your session has been expired.'));
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = '2018-11-12 08:57:58'; 
                $this->db->where('users_id',$users_id)->where('token',$token)->update('api_users_authentication',array('expired_at' => $expired_at,'updated_at' => $updated_at));
                return array('status' => 200,'message' => 'Authorized.');
            }
        }
    }

    public function placement_list()
    {
        return $this->db->select('id,category')->from('job_category')->order_by('category', 'asc')->get()->result();
    }
    
    public function job_list($category,$location,$job_type)
    {
		if($category!='0')
		{
		 $query = $this->db->query("select job_list.*,job_category.category from job_list INNER JOIN job_category on job_category.id=job_list.job_role where job_list.job_role='$category' order by job_list.id desc");
		}
		if($location!='0')
		{
		 $query = $this->db->query("select job_list.*,job_category.category from job_list INNER JOIN job_category on job_category.id=job_list.job_role where job_list.job_location like '%$location%' order by job_list.id desc");
		}
		if($job_type!='0')
		{
		 $query = $this->db->query("select job_list.*,job_category.category from job_list INNER JOIN job_category on job_category.id=job_list.job_role where job_list.job_type='$job_type' order by job_list.id desc");
		}
	   
		$resultpost = array();
		
		foreach($query->result_array() as $row){
		$id = $row['id'];
		$job_type = $row['job_type'];
		$job_title = $row['job_title'];
		$job_description = $row['job_description'];
		$job_role = $row['category'];
		$job_location = $row['job_location'];
		$min_salary = $row['min_salary'];
		$max_salary = $row['max_salary'];
		$company_name = $row['company_name'];
		$email = $row['email'];
		$mobile = $row['mobile'];
		$gender = $row['gender'];
		$posted_on = $row['posted_on'];
		
		$resultpost[]=array('id' => $id,'job_type' => $job_type,'job_title' => $job_title,'job_description' => $job_description, 'job_role' => $job_role,'gender' => $gender,'job_location' => $job_location, 'min_salary' => $min_salary,'max_salary' => $max_salary, 'company_name' => $company_name,'email' => $email,'mobile' => $mobile,'posted_on' => $posted_on);
		}
	
        return $resultpost;
    }
	
	public function add_job($job_type,$job_title,$job_description,$job_role,$job_location,$min_salary,$max_salary,$company_name,$email,$mobile,$gender)
    {
	    date_default_timezone_set('Asia/Kolkata');
		$created_at = date('Y-m-d H:i:s');
	    $job_data = array(
        'job_type'=>$job_type,
		'job_title'=>$job_title,
		'job_description'=>$job_description,
		'job_role'=>$job_role,
		'job_location'=>$job_location,
		'min_salary'=>$min_salary,
		'max_salary'=>$max_salary,
		'company_name'=>$company_name,
		'email'=>$email,
		'mobile'=>$mobile,
		'gender'=>$gender,
		'posted_on'=>$created_at
        );
        $success = $this->db->insert('job_list',$job_data);
        $id = $this->db->insert_id();
		return array('status' => 201,'message' => 'success', 'job_id' => $id);
    }
    
    	public function add_job_user_profile($name,$phone,$email,$dob,$gender,$job_role,$min_salary,$max_salary,$year_exp,$month_exp,$city,$user_id)
    {
	    date_default_timezone_set('Asia/Kolkata');
		$created_at = date('Y-m-d H:i:s');
	    $job_data = array(
	    'user_id'=>$user_id, 
        'name'=>$name,
		'phone'=>$phone,
		'email'=>$email,
		'dob'=>$dob,
		'gender'=>$gender,
		'job_role'=>$job_role,
		'min_salary'=>$min_salary,
		'max_salary'=>$max_salary,
		'year_exp'=>$year_exp,
		'month_exp'=>$month_exp,
		'city'=>$city,
		'posted_on'=>$created_at
        );
        
          
        $success = $this->db->insert('job_user_profile',$job_data);
        $id = $this->db->insert_id();
        if ($success) {
                    $date_array = array(
                        'user_id' => $user_id,
                        'name' => $name,
                        'phone' => $phone,
                        'gender' => $gender
                        
                    );
		return array('status' => 201,'message' => 'success', 'data'=>$date_array);
            
        }
		else {
                    return array(
                        'status' => 208,
                        'message' => 'failed'
                    );
                }
    }
    
    public function user_profile_doc($user_id, $resume_file)
    {
        $query = $this->db->query("UPDATE job_user_profile SET resume='$resume_file' WHERE user_id='$user_id'");

        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    public function job_role()
    {
        $query = $this->db->query("SELECT * FROM `job_category` ORDER BY category ASC");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $job_role_id        = $row['id'];
                $job_role        = $row['category'];
                $resultpost[] = array(
                    "job_role_id" => $job_role_id,
                    "job_role" => $job_role
                );
            }
        } else {
            $resultpost = array();
        }
        
        return $resultpost;
    }
    
    public function user_profile_list($user_id)
    {
	
	   
	
		$query = $this->db->query("SELECT * FROM `job_user_profile` ORDER BY id DESC");
	
	   
		$resultpost = array();
		
		foreach($query->result_array() as $row){
		$id = $row['id'];
		$name = $row['name'];
		$phone = $row['phone'];
		$email = $row['email'];
		$dob = $row['dob'];
		$gender = $row['gender'];
		$min_salary = $row['min_salary'];
		$max_salary = $row['max_salary'];
		$job_role = $row['job_role'];
		$year_exp = $row['year_exp'];
		$month_exp = $row['month_exp'];
		$city = $row['city'];
		$resume = $row['resume'];
		$posted_on = $row['posted_on'];
		
		 if ($dob != "") {
$new_dob = date("Y-m-d", strtotime($dob));
$today = date("Y-m-d");
$diff = date_diff(date_create($new_dob), date_create($today));
$age= $diff->format('%y');
                }
		if ($resume != '') {
                    $resume = 'Resume uploded';
                } else {
                    $resume = 'Resume not uploded';
                }
		
		
		$resultpost[]=array('id' => $id,
		'name' => $name,
		'phone' => $phone,
		'email' => $email, 
		'dob' => $dob,
		'age' => $age,
		'gender' => $gender,
		'gender' => $gender,
		'min_salary' => $min_salary,
		'max_salary' => $max_salary, 
		'job_role' => $job_role,
		'year_exp' => $year_exp,
		'month_exp' => $month_exp,
		'city' => $city,
		'resume' => $resume,
		'posted_on' => $posted_on);
		}
	
        return $resultpost;
    }
	
    
}

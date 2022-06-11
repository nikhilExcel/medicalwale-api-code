
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class JobsprofileModel extends CI_Model {
    
                //JOBS-EDUCATION
    public function user_profile_education($user_id,$job_education_id,$school_name, $board_name, $medium, $percentage, $passing_year,$grade,$specifications,$courses,$id) 
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        // last modified date for profile
        $this->db->set('updated_at', $created_at);
        $this->db->where('user_id', $user_id); //which row want to upgrade  
        $this->db->update('jobs_user_profile_master');
        // ends here
        if($job_education_id == 3 || $job_education_id== 5 )
        {
            if($id == "")
            {
                $education_data = array(
            'user_id' => $user_id,
            'job_education_id' => $job_education_id,
            'school_name' => $school_name,
            'board_name' => $board_name,
            'medium' => $medium,
            'percentage' => $percentage,
            'passing_year' => $passing_year,
            'grade' => $grade,
            'specifications' => $specifications,
            'courses' => $courses,
            );
             $success = $this->db->insert('jobs_user_education', $education_data);
             if ($success) {
                $date_array = array(
                    'user_id' => $user_id,
                    'job_education_id' => $job_education_id,
                );
                return array('status' => 201, 'message' => 'success', 'data' => $date_array);
            } else {
                return array(
                    'status' => 208,
                    'message' => 'failed'
                );
            }
            }
            else{
                            // echo 'diploma here'; die();
            $education_data = array(
            'user_id' => $user_id,
            'job_education_id' => $job_education_id,
            'school_name' => $school_name,
            'board_name' => $board_name,
            'medium' => $medium,
            'percentage' => $percentage,
            'passing_year' => $passing_year,
            'grade' => $grade,
            'specifications' => $specifications,
            'courses' => $courses,
            );
            $this->db->where('user_id', $user_id);
            $this->db->where('id', $id);
            $this->db->where('job_education_id', $job_education_id);
            $success = $this->db->update('jobs_user_education', $education_data);
            if ($success) {
                $date_array = array(
                    'user_id' => $user_id,
                    'job_education_id' => $job_education_id,
                );
                return array('status' => 201, 'message' => 'success', 'data' => $date_array);
            } else {
                return array(
                    'status' => 208,
                    'message' => 'failed'
                );
            }

            }
        } 
        else 
        {
                $education_data = array(
                'user_id' => $user_id,
                'job_education_id' => $job_education_id,
                'school_name' => $school_name,
                'board_name' => $board_name,
                'medium' => $medium,
                'percentage' => $percentage,
                'passing_year' => $passing_year,
                'grade' => $grade,
                'specifications' => $specifications,
                'courses' => $courses,
            );
            $p = $this->db->select('id')->from('jobs_user_education')->where('user_id', $user_id)->where('job_education_id', $job_education_id)->get()->num_rows();
             if($p < 1){
           $success = $this->db->insert('jobs_user_education', $education_data);
             }
             else{
                 $this->db->where('user_id', $user_id);
                  $this->db->where('job_education_id', $job_education_id);
                $success =  $this->db->update('jobs_user_education',$education_data);
             }
            
            //$id = $this->db->insert_id();
            if ($success) {
                $date_array = array(
                    'user_id' => $user_id,
                    'job_education_id' => $job_education_id,
                );
                return array('status' => 201, 'message' => 'success', 'data' => $date_array);
            } else {
                return array(
                    'status' => 208,
                    'message' => 'failed'
                );
            }
        }
    }

//JOBS-USER-KEYSKILLS
    public function user_profile_key_skills($user_id,$technical_skills, $technical_skills_desc ) 
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        // last modified date for profile
        $this->db->set('updated_at', $created_at);
        $this->db->where('user_id', $user_id); //which row want to upgrade  
        $this->db->update('jobs_user_profile_master');
        // ends here
        $skills_data = array(
            'user_id' => $user_id,
            'technical_skills' => $technical_skills,
            'technical_skills_desc' => $technical_skills_desc,
            'created_at' => $created_at

        );


        $success = $this->db->insert('jobs_skils_job', $skills_data);
        //$id = $this->db->insert_id();
        if ($success) {
            $date_array = array(
                'user_id' => $user_id,
                
            );
            return array('status' => 201, 'message' => 'success', 'data' => $date_array);
        } else {
            return array(
                'status' => 208,
                'message' => 'failed'
            );
        }
    }

     //JOBS-CERTIFICATE
    public function user_profile_certificate($id,$user_id,$certificate_name,$ce_issued_date, $ce_description, $ce_issued_by,$achievement)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        // last modified date for profile
        $this->db->set('updated_at', $created_at);
        $this->db->where('user_id', $user_id); //which row want to upgrade  
        $this->db->update('jobs_user_profile_master');
        // ends here
        $certificate_data = array(
            'user_id' => $user_id,
            'certificate_name' => $certificate_name,
            'ce_issued_date' => $ce_issued_date,
            'ce_description' => $ce_description,
            'ce_issued_by' => $ce_issued_by,
            'achievement' => $achievement,
        );
        
        $p = $this->db->select('id')->from('jobs_user_certifications')->where('user_id', $user_id)->where('id', $id)->get()->num_rows();
        // print_r($p); die();
        if($p<1){
              $success = $this->db->insert('jobs_user_certifications',$certificate_data);
        }
        else{
            $this->db->where('id', $id);
            $this->db->where('user_id',$user_id);
            $success = $this->db->update('jobs_user_certifications',$certificate_data);
            
        }
        
      
        //$id = $this->db->insert_id();
        if ($success){
                        $date_array = array(
                            'user_id' => $user_id,
                            'certificate_name' => $certificate_name,
                        );
                        return array('status' => 201, 'message' => 'success', 'data' => $date_array);
                    } else
                    {
            return array('status' => 208,'message' => 'failed');
                    }
    }
    
    //JOBS-previous-job-details
    public function user_profile_previous_job($id,$user_id,$company_name,$company_type,$designation,$employment_type,$location, $work_experience, $work_start,$work_end,$desc_profile ) 
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        // last modified date for profile
        $this->db->set('updated_at', $created_at);
        $this->db->where('user_id', $user_id); //which row want to upgrade  
        $this->db->update('jobs_user_profile_master');
        // ends here
        $previous_job_data = array(
            'user_id' => $user_id,
            'company_name' => $company_name,
            'company_type' => $company_type,
            'employment_type' => $employment_type,
            'designation' => $designation,
            'location' => $location,
            'work_experience' => $work_experience,
            'work_start' => $work_start,
            'work_end' => $work_end,
            'desc_profile' => $desc_profile
        );
        $p = $this->db->select('id')->from('jobs_previous_job')->where('user_id', $user_id)->where('id', $id)->get()->num_rows();
        // print_r($p); die();
        if($p<1){
            $success = $this->db->insert('jobs_previous_job', $previous_job_data);
        }
        else{
             $this->db->where('id', $id);
             $this->db->where('user_id',$user_id);
             $success = $this->db->update('jobs_previous_job', $previous_job_data);
        }
       
        //$id = $this->db->insert_id();
        if ($success) {
            $date_array = array(
                'user_id' => $user_id,
                'work_start' =>$work_start,
                'work_end' => $work_end,
                
                
            );
            return array('status' => 201, 'message' => 'success', 'data' => $date_array);
        } else {
            return array(
                'status' => 208,
                'message' => 'failed'
            );
        }
    }

 //JOBS-preferred-job-details
    public function user_profile_preferred_job($user_id,$job_type,$job_location,$job_position, $min_salary) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        // last modified date for profile
        $this->db->set('updated_at', $created_at);
        $this->db->where('user_id', $user_id); //which row want to upgrade  
        $this->db->update('jobs_user_profile_master');
        // ends here
        $job_data = array(
            'user_id' => $user_id,
            'job_type' => $job_type,
            'job_location' => $job_location,
            'job_position' => $job_position,
            'min_salary' => $min_salary,
        );
        
        $p = $this->db->select('id')->from('jobs_preferred_job')->where('user_id', $user_id)->get()->num_rows();
        if($p<1){
             $success = $this->db->insert('jobs_preferred_job', $job_data);
        }
        else{
            $this->db->where('user_id',$user_id);
            $success = $this->db->update('jobs_preferred_job', $job_data);
        }
       
        //$id = $this->db->insert_id();
        if ($success) {
            $date_array = array(
                'user_id' => $user_id,
                
            );
            return array('status' => 201, 'message' => 'success', 'data' => $date_array);
                     } else {
            return array(
                'status' => 208,
                'message' => 'failed'
            );
        }
    }

     public function user_profile_list($id) 
     {
        $query = $this->db->query("SELECT * FROM `jobs_profile` WHERE `user_id` = '$id' ORDER BY id DESC");
        $resultpost = array();

        foreach ($query->result_array() as $row) {
            $user_id = $row['user_id'];
            //$id = $row['id'];
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $mobile = $row['mobile'];
            $email = $row['email'];
            $dob = $row['dob'];
            $gender = $row['gender'];
            $marital_status = $row['marital_status'];
            $address_line1 = $row['address_line1'];
            $address_line2 = $row['address_line2'];
           // $year_exp = $row['year_exp'];
           // $month_exp = $row['month_exp'];
            $city = $row['city'];
            $state = $row['state'];
            //$posted_on = $row['posted_on'];

           /* if ($dob != "") {
                $new_dob = date("Y-m-d", strtotime($dob));
                $today = date("Y-m-d");
                $diff = date_diff(date_create($new_dob), date_create($today));
                $age = $diff->format('%y');
            }
            if ($resume != '') {
                $resume = 'Resume uploded';
            } else {
                $resume = 'Resume not uploded';
            }*/


            $resultpost[] = array('user_id' => $user_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'mobile' => $mobile,
                'email' => $email,
                'dob' => $dob,
               // 'age' => $age,
                'gender' => $gender,
                'marital_status' => $marital_status,
                'address_line1' => $address_line1,
                'address_line2' => $address_line2,
                //'job_role' => $job_role,
                //'year_exp' => $year_exp,
                //'month_exp' => $month_exp,
                'city' => $city,
                'state' => $state,
                //'posted_on' => $posted_on);
                );
        }

        return $resultpost;
    }
    
    public function jobs_listing($user_id,$type,$page,$sort_salary,$location,$salary,$work_experience)
    {
        date_default_timezone_set('Asia/Kolkata');
        $present = date('Y-m-d H:i:s');
        // // $date = $row['expire_date']; //date from database 
        $new_date = date('Y-m-d', strtotime('-7 days', strtotime($present))); 
        //print_r($str2); die();
        // sort_salary = 0(low to high)
        // sort_salary = 1(high to low)

        if($page==""){
        $limit = " LIMIT 0, 10";   
        }
        else
        {
            $limit = 10;
            $start = 0;
            if ($page > 0) {
                if (!is_numeric($page)) {
                    $page = 1;
                }
            }
            $start = ($page - 1) * $limit;
            $limit = " LIMIT $start, $limit";   
        }
        $resultpost = array();
        if ($type =='1') {
            
           // echo $type;
            
            if($sort_salary == 0){
            $query = $this->db->query("SELECT juj.applied_status,juj.favourite_status,jd.`job_id`,jd.`job_position`,jd.`company_name`, jd.`required_exp`,jd.`state`,jd.`job_desc`,jd.posted_on,jd.`favourite`,jd.income,jd.max_salary FROM `jobs_description` as jd left join jobs_user_job as juj on (jd.job_id = juj.job_id AND juj.user_id = '$user_id' ) group by jd.job_id  ORDER BY jd.max_salary ASC $limit");
            foreach ($query->result_array() as $row) {
            $job_id = $row['job_id'];
            //$id = $row['id'];
            $job_position	 = $row['job_position'];
            $company_name = $row['company_name'];
            $required_exp = $row['required_exp'];
            $location = $row['state'];
            $job_desc = $row['job_desc'];
            $favourite_status = $row['favourite_status'];
            $applied_status = $row['applied_status'];
            $posted_on = $row['posted_on'];
            $min_salary = $row['income'];
            $max_salary = $row['max_salary'];
            
            if($favourite_status==''){
                
                $favourite_status='0';
                // echo $favourite_status; die();
            }
            if($applied_status==''){
                $applied_status='0';
            }
            
            $resultpost[] = array('job_id' => $job_id,
                'job_position' => $job_position,
                'company_name' => $company_name,
                'required_exp' => $required_exp,
                'location' => $location,
                'job_desc' => $job_desc,
                'favourite_status' =>$favourite_status,
                'applied_status' =>$applied_status,
                'posted_on' => $posted_on,
                'min_salary' => $min_salary,
                'max_salary' => $max_salary
                );
                }
            
            }
            elseif($sort_salary == 1){
            $query = $this->db->query("SELECT juj.applied_status,juj.favourite_status,jd.`job_id`,jd.`job_position`,jd.`company_name`, jd.`required_exp`,jd.`state`,jd.`job_desc`,jd.posted_on,jd.`favourite`,jd.income,jd.max_salary FROM `jobs_description` as jd left join jobs_user_job as juj on (jd.job_id = juj.job_id AND juj.user_id = '$user_id' ) group by jd.job_id  ORDER BY jd.max_salary DESC $limit");
            foreach ($query->result_array() as $row) {
            $job_id = $row['job_id'];
            //$id = $row['id'];
            $job_position	 = $row['job_position'];
            $company_name = $row['company_name'];
            $required_exp = $row['required_exp'];
            $location = $row['state'];
            $job_desc = $row['job_desc'];
            $favourite_status = $row['favourite_status'];
            $applied_status = $row['applied_status'];
            $posted_on = $row['posted_on'];
            $min_salary = $row['income'];
            $max_salary = $row['max_salary'];
            
            if($favourite_status==''){
                
                $favourite_status='0';
                // echo $favourite_status; die();
            }
            if($applied_status==''){
                $applied_status='0';
            }
            
            $resultpost[] = array('job_id' => $job_id,
                'job_position' => $job_position,
                'company_name' => $company_name,
                'required_exp' => $required_exp,
                'location' => $location,
                'job_desc' => $job_desc,
                'favourite_status' =>$favourite_status,
                'applied_status' =>$applied_status,
                'posted_on' => $posted_on,
                'min_salary' => $min_salary,
                'max_salary' => $max_salary
                );
                }
            
            }
            else
            {
               // echo $sort_salary;
                $str1 = $location;
                $str21 = $salary;
                $s = $salary;
                $l = (explode(",",$str1));
                $qwe= (explode("-",$str21));
                $max_salary = (max($qwe)*100000);
                $min_salary = (min($qwe)*100000);
                $max = max(sizeof($l) , sizeof($s));
                for($i = 0; $i<sizeof($max);$i++){
                    $a = $l[$i];

                $query = $this->db->query("SELECT juj.applied_status,juj.favourite_status,jd.`job_id`,jd.`job_position`,jd.`company_name`, jd.`required_exp`,jd.`state`,jd.`job_desc`,jd.posted_on,jd.`favourite`,jd.income,jd.max_salary,jd.city FROM `jobs_description` as jd left join jobs_user_job as juj on (jd.job_id = juj.job_id AND juj.user_id = '$user_id' ) WHERE jd.city LIKE '%$a%' OR jd.income >= $min_salary AND jd.max_salary <= $max_salary AND jd.required_exp >= '$work_experience' group by jd.job_id ORDER BY jd.posted_on  DESC");
                foreach ($query->result_array() as $row) {
                $job_id = $row['job_id'];
                //$id = $row['id'];
                $job_position	 = $row['job_position'];
                $company_name = $row['company_name'];
                $required_exp = $row['required_exp'];
                $location = $row['state'];
                $job_desc = $row['job_desc'];
                $favourite_status = $row['favourite_status'];
                $applied_status = $row['applied_status'];
                $min_salary = $row['income'];
                $max_salary = $row['max_salary'];
                $posted_on = $row['posted_on'];
            
                if($favourite_status==''){
                
                $favourite_status='0';
                // echo $favourite_status; die();
                }
                if($applied_status==''){
                    $applied_status='0';
                }
            
                $resultpost[] = array('job_id' => $job_id,
                'job_position' => $job_position,
                'company_name' => $company_name,
                'required_exp' => $required_exp,
                'location' => $location,
                'job_desc' => $job_desc,
                'favourite_status' =>$favourite_status,
                'applied_status' =>$applied_status,
                'posted_on' => $posted_on,
                'min_salary' => $min_salary,
                'max_salary' => $max_salary
                );
                }
                }
            }
            
            
        } 
        elseif ($type =='2') {
            
        $query = $this->db->query("SELECT jobs_user_job.favourite_status,jobs_user_job.applied_status, jobs_description.job_id,jobs_description.job_position,jobs_description.company_name,jobs_description.required_exp,jobs_description.state,jobs_description.job_desc,jobs_description.posted_on,jobs_description.income,jobs_description.max_salary FROM jobs_description LEFT JOIN jobs_user_job ON jobs_user_job.job_id = jobs_description.job_id WHERE jobs_user_job.user_id = $user_id AND favourite_status=1 $limit");

            foreach ($query->result_array() as $row) {
            $job_id = $row['job_id'];
            //$id = $row['id'];
            $job_position	 = $row['job_position'];
            $company_name = $row['company_name'];
            $required_exp = $row['required_exp'];
            $location = $row['state'];
            $job_desc = $row['job_desc'];
            $favourite_status = $row['favourite_status'];
            $applied_status = $row['applied_status'];
            $min_salary = $row['income'];
            $max_salary = $row['max_salary'];
            $posted_on = $row['posted_on'];
            
            if($favourite_status==''){
                
                $favourite_status='0';
                // echo $favourite_status; die();
            }
            if($applied_status==''){
                $applied_status='0';
            }
       


            $resultpost[] = array('job_id' => $job_id,
                'job_position' => $job_position,
                'company_name' => $company_name,
                'required_exp' => $required_exp,
                'location' => $location,
                'job_desc' => $job_desc,
                'favourite_status' =>$favourite_status,
                'applied_status' =>$applied_status,
                'posted_on' => $posted_on,
                'min_salary' => $min_salary,
                'max_salary' => $max_salary
              
                );
                }
                
        } elseif ($type =='3') {

    //$job_id = $this->db->get_where('jobs_user_job',array('user_id' => $user_id))->row()->job_id;
    $query = $this->db->query("SELECT jobs_user_job.applied_status,jobs_user_job.favourite_status, jobs_description.job_id,jobs_description.job_position,jobs_description.company_name,jobs_description.required_exp,jobs_description.state,jobs_description.job_desc,jobs_description.posted_on,jobs_description.income,jobs_description.max_salary FROM jobs_description LEFT JOIN jobs_user_job ON jobs_user_job.job_id = jobs_description.job_id WHERE jobs_user_job.user_id = $user_id AND applied_status=1 $limit");

            foreach ($query->result_array() as $row) {
            $job_id = $row['job_id'];
            //$id = $row['id'];
            $job_position	 = $row['job_position'];
            $company_name = $row['company_name'];
            $required_exp = $row['required_exp'];
            $location = $row['state'];
            $job_desc = $row['job_desc'];
            $favourite_status = $row['favourite_status'];
            $applied_status = $row['applied_status'];
            $min_salary = $row['income'];
            $max_salary = $row['max_salary'];
            $posted_on = $row['posted_on'];
            if($favourite_status==''){
                
                $favourite_status='0';
                // echo $favourite_status; die();
            }
            if($applied_status==''){
                $applied_status='0';
            }


            $resultpost[] = array('job_id' => $job_id,
                'job_position' => $job_position,
                'company_name' => $company_name,
                'required_exp' => $required_exp,
                'location' => $location,
                'job_desc' => $job_desc,
                'favourite_status' =>$favourite_status,
                'applied_status' =>$applied_status,
                'posted_on' => $posted_on,
                'min_salary' => $min_salary,
                'max_salary' => $max_salary
              
                );
                }

        }
        elseif($type=='4')
        {
            $query = $this->db->query("SELECT juj.applied_status,juj.favourite_status,jd.`job_id`,jd.`job_position`,jd.`company_name`, jd.`required_exp`,jd.`state`,jd.`job_desc`,jd.posted_on,jd.`favourite`,jd.income,jd.max_salary FROM `jobs_description` as jd left join jobs_user_job as juj on (jd.job_id = juj.job_id AND juj.user_id = '$user_id' ) WHERE jd.posted_on >= '$new_date' group by jd.job_id $limit");
            foreach ($query->result_array() as $row) {
            $job_id = $row['job_id'];
            //$id = $row['id'];
            $job_position	 = $row['job_position'];
            $company_name = $row['company_name'];
            $required_exp = $row['required_exp'];
            $location = $row['state'];
            $job_desc = $row['job_desc'];
            $favourite_status = $row['favourite_status'];
            $applied_status = $row['applied_status'];
            $posted_on = $row['posted_on'];
            $min_salary = $row['income'];
            $max_salary = $row['max_salary'];
            
            if($favourite_status==''){
                
                $favourite_status='0';
                // echo $favourite_status; die();
            }
            if($applied_status==''){
                $applied_status='0';
            }
            
            $resultpost[] = array('job_id' => $job_id,
                'job_position' => $job_position,
                'company_name' => $company_name,
                'required_exp' => $required_exp,
                'location' => $location,
                'job_desc' => $job_desc,
                'favourite_status' =>$favourite_status,
                'applied_status' =>$applied_status,
                'posted_on' => $posted_on,
                'min_salary' => $min_salary,
                'max_salary' => $max_salary
                );
                }
            
            
            
        }
        
        
        

    return $resultpost;
    }
    
    public function jobs_detail_listing($user_id,$job_id)
    {
        $query = $this->db->query("SELECT juj.applied_status,juj.favourite_status,jd.job_id,juj.user_id,jd.job_position,jd.company_name,jd.no_of_vacancies,jd.required_exp,jd.state,jd.city,jd.job_skills_req,jd.income,jd.max_salary,jd.posted_on,jd.job_desc,jd.industry_type,jd.functional_area,jd.role,jd.employment_type,jd.company_website,jd.about_company,jd.desired_candidate_profile,jd.company_id,jd.job_link FROM `jobs_description` as jd left join jobs_user_job as juj on (jd.job_id = juj.job_id AND juj.user_id = '$user_id' ) WHERE jd.job_id =$job_id");
        $resultpost = array();

        foreach ($query->result_array() as $row) {
            $user_id = $row['user_id'];
            $job_id = $row['job_id'];
            $job_position	 = $row['job_position'];
            $company_name = $row['company_name'];
            $required_exp = $row['required_exp'];
            $no_of_vacancies = $row['no_of_vacancies'];
            $location = $row['city'];
            $job_skills_req = $row['job_skills_req'];
            $min_salary	 = $row['income'];
            $max_salary = $row['max_salary'];
            $posted_on = $row['posted_on'];
            $job_desc = $row['job_desc'];
            $industry_type = $row['industry_type'];
            $functional_area = $row['functional_area'];
            $role	 = $row['role'];
            $employment_type = $row['employment_type'];
            $desired_candidate_profile = $row['desired_candidate_profile'];
            $company_website = $row['company_website'];
            $applied_status = $row['applied_status'];
            $favourite_status = $row['favourite_status'];
            $about_company	 = $row['about_company'];
            $company_id = $row['company_id'];
            $job_link = $row['job_link'];
            if($favourite_status==''){
                
                $favourite_status='0';
                // echo $favourite_status; die();
            }
            if($applied_status==''){
                $applied_status='0';
            }
       


            $resultpost[] = array(
                'user_id' =>$user_id,
                'job_id' => $job_id,
                'job_position' => $job_position,
                'company_name' => $company_name,
                'required_exp' => $required_exp,
                'no_of_vacancies' => $no_of_vacancies,
                'location' => $location,
                'job_skills_req' => $job_skills_req,
                'min_salary' => $min_salary,
                'max_salary' => $max_salary,
                'posted_on' => $posted_on,
                'job_desc' => $job_desc,
                'industry_type' => $industry_type,
                'functional_area' => $functional_area,
                'role' => $role,
                'employment_type' => $employment_type,
                'desired_candidate_profile' => $desired_candidate_profile,
                'company_website' => $company_website,
                'applied_status' => $applied_status,
                'favourite_status' =>$favourite_status,
                'about_company' =>$about_company,
                'company_id' => $company_id,
                'job_link' =>$job_link
                );
        }

    return $resultpost;
    }
     
    public function jobs_ques($job_id)
    {
         
          $a = explode(',',$job_id);
          $j=sizeof($a);
       
          
            if( $j > 0 )
                {
                    for ($i=0;$i<$j;$i++)
                        {
                            $query = $this->db->query("SELECT * FROM jobs_apply_job_ques WHERE job_id = $a[$i]");
                            $count = $query->num_rows();
                            $items = $query->result_array(); //all fields of ques
                               
                            $item = $items;
                            $user_id = $this->db->get_where('jobs_description',array('job_id' => $a[$i]))->row()->user_id;
                            //  print_r($user_id); die();
                            $job_position = $this->db->get_where('jobs_description',array('job_id' => $a[$i]))->row()->job_position;
                            $id = '';
                           
                            $ar = array();
                            $ar['user_id'] = $user_id;
                            $ar['job_id'] = $a[$i];
                            $ar['job_position'] = $job_position;
                            $r = $this->sub($items, $id, $a[$i]);
                            $ar['questions'] = $r;
                            $data[] = $ar;
                        }
                }
                
                return  $data;
    }
     
     function sub($items, $id, $job_id) {
        //  print_r($job_id); die();
         $query = $this->db->query("SELECT * FROM jobs_apply_job_ques WHERE job_id = $job_id");
        $count = $query->num_rows();
        $items = $query->result_array(); //all fields of ques
        
        $id = '';
        $data = array();
        $ar = array();
        foreach ($items as $item) {
        //   print_r($item['question_type']); die();
           /* if ($item['question_type'] =='t')
            {
              
                $ar['type'] = $item['question_type'];
                $ar['question'] = $item['questions'];
                $ar['que_id'] = $item['id'];
                $ar['options'] = $item['options'];
                $query = $this->db->query("SELECT * FROM jobs_apply_jobs_ans WHERE ques_id='$id' and job_id='$job_id'");
                $items2 = $query->row_array();
                $ar['answer'] = "";
                $id = $item['id'];
                $ar['check_answer']=array();
                $data[] = $ar; 
            }
            else
            {
                 $ar['type'] = $item['question_type'];
                $ar['question'] = $item['questions'];
                $ar['que_id'] = $item['id'];
                // $ar['options1'] = $item['options'];
                $a = explode(',',$item['options']);
                $j=sizeof($a)-1;
                // print_r($a); die();
                
                for ($i=0;$i<=$j;$i++)
                {
                
                $ar['check_answer'][]=array(
                    'checkbox_value'=>$a[$i]
                    );
                
                
                }
                $query = $this->db->query("SELECT * FROM jobs_apply_jobs_ans WHERE ques_id='$id' and job_id='$job_id'");
                $items2 = $query->row_array();
                $ar['answer'] = "";
                $id = $item['id'];
                $data[] = $ar; 
            }*/
            
            
                $ar['type'] = $item['question_type'];
                $ar['question'] = $item['questions'];
                $ar['que_id'] = $item['id'];
               // $ar['options'] = $item['options'];
                $query = $this->db->query("SELECT * FROM jobs_apply_jobs_ans WHERE ques_id='$id' and job_id='$job_id'");
                $items2 = $query->row_array();
                $ar['answer'] = "";
                $id = $item['id'];
                $a = explode(',',$item['options']);
                $j=sizeof($a)-1;
                if( $j > 0 )
                {
                    for ($i=0;$i<=$j;$i++)
                        {
                        $ar['check_answer'][]=array(
                            'checkbox_value'=>$a[$i]
                            );
                        }
                }
                else
                {
                    $ar['check_answer']=array();
                }
                $data[] = $ar; 

        }

        return $data;
    }
    
    //send notification through firebase
        /* notification to send in the doctor app for appointment confirmation*/
        function send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $booking_id,$notification_type) {
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
                "notification_type" => $notification_type,
                "notification_date" => $date,
                "booking_id" => $booking_id
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
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
     
     public function jobs_ques_ans($user_id,$job_id,$answers) {
        $answer = json_decode($answers);
         $data = array();
         date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        // for notification
        //$company_id = $this->db->get_where('jobs_description',array('job_id' => $job_id))->row()->company_id;
        //print_r($company_id); die();
        //$company_info = $this->db->query("SELECT `user_id`,`company_name` FROM `company_profile` WHERE `user_id`=$company_id")->row_array();
        
        $p = $this->db->select('id')->from('jobs_user_job')->where('user_id', $user_id)->where('job_id', $job_id)->get()->num_rows();
       
        //   die();
       // $answer = json_decode($answers);
        if( sizeof($answer) > 0) {
            foreach($answer as $ans){
                    // print_r(($ans)); 
                    $question_id = $ans->que_id;
                    $answer1 = $ans->answer;
                    $this->db->query("INSERT INTO `jobs_apply_jobs_ans`(`id`, `user_id`, `ques_id`, `job_id`, `answer`) VALUES ('','$user_id','$question_id','$job_id','$answer1')");
                   
            }
             // applied date
              if($p < 1){
                    $this->db->query("INSERT INTO `jobs_user_job`(`id`, `user_id`,`job_id`, `applied_status`,`applied_date`) VALUES ('','$user_id','$job_id','1','$date')");
                    // applied date end}
              }
              
              else{
            $this->db->set('applied_status','1');
            $this->db->set('applied_date',$date);
            $this->db->where('user_id', $user_id); //which row want to upgrade  
            $this->db->where('job_id', $job_id); //which row want to upgrade  
            $this->db->update('jobs_user_job');
                  
              }
            
            // notification for applying for jobs
            
            $reg_id_token = $this->db->query("SELECT name,token,agent,token_status FROM users WHERE id = $user_id")->row_array();
            $title = 'Job Application';
            $msg = 'Congratulations you have successfully applied for a Job. The organization will get back to you soon';
            $reg_id = $reg_id_token['token'];
            $img_url = 'https://vendorsandbox.medicalwale.com/hospitalsss/assetsnewthem/img/job.png';
            $tag = 'jobs';
            $agent = 'android';
            //$type = 'jobs';
            $word = 'Congratulations you have successfully applied for a/ (# of jobs). The organization will get back to you soon';
            $meaning = 'meaning';
            date_default_timezone_set('Asia/Kolkata');
            $date1 = date("Y-m-d");
            $booking_id= '';
            $notification_type = 'Jobs_notifications';
            $this->db->query("INSERT INTO `myactivity_notification` (`user_id`, `notification_type`, `notification_date`, `title`, `msg`,`img_url`, `tag`) VALUES ('$user_id','$notification_type','$date1','$title','$msg','$img_url','$tag')");
            $this->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $booking_id,$notification_type);
            
            $data['message'] = "Applied Successfully !!!";
        }
          return $data;
     }
     
    public function user_profile($user_id,$first_name,$last_name, $mobile, $email, $dob, $gender,$marital_status, $languages_known,$nationality, $address_line1, $address_line2, $city,$state,$country,$pincode,$mother_tongue) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $job_data = array(
            'user_id' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'mobile' => $mobile,
            'email' => $email,
            'dob' => $dob,
            'gender' => $gender,
            'marital_status' => $marital_status,
            'languages_known' => $languages_known,
            'nationality' => $nationality,
            'address_line1' => $address_line1,
            'address_line2' => $address_line2,
            'city' => $city,
            'state' => $state,
            'country' => $country,
            'pincode' => $pincode,
            'mother_tongue' => $mother_tongue,
            'updated_at' =>$created_at
        );
        $p = $this->db->select('id')->from('jobs_user_profile_master')->where('user_id', $user_id)->get()->num_rows();
         if($p < 1){
        $success = $this->db->insert('jobs_user_profile_master', $job_data);
         }
         else{
             $this->db->where('user_id', $user_id);
             $this->db->update('jobs_user_profile_master',$job_data);
         }
            $date_array = array(
                'user_id' => $user_id,
                'first_name' => $first_name,
                 'last_name' => $last_name,
                'mobile' => $mobile,
            );
         return $date_array;
    }
    
    public function user_profile_resume($user_id,$resume_head) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $job_data = array(
            'user_id' => $user_id,
            'resume_head' => $resume_head
        );
        $this->db->set('updated_at', $created_at);
        $this->db->set('resume_head', $resume_head); //value that used to update column  
        $this->db->where('user_id', $user_id); //which row want to upgrade  
        $this->db->update('jobs_user_profile_master'); 
            $date_array = array(
                'user_id' => $user_id
            );
         return $date_array;
    }
    
    public function user_profile_video($user_id,$resume_head) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $job_data = array(
            'user_id' => $user_id,
            'resume_head' => $resume_head
        );
        $this->db->set('updated_at', $created_at);
        $this->db->set('resume_head', $resume_head); //value that used to update column  
        $this->db->where('user_id', $user_id); //which row want to upgrade  
        $this->db->update('jobs_user_profile_master'); 
            $date_array = array(
                'user_id' => $user_id
            );
         return $date_array;
    }

     
     public function favourite_job($user_id, $job_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $pharmacy_view = $this->db->select('id')->from('jobs_favourite_job')->where('user_id', $user_id)->where('job_id', $job_id)->get()->num_rows();
        if($pharmacy_view > 0)
        {
            $this->db->where('user_id', $user_id);
            $this->db->where('job_id', $job_id);
            $this->db->delete('jobs_favourite_job');    
            
            
            
            $this->db->set('favourite_status','0');
            $this->db->where('user_id', $user_id); //which row want to upgrade  
            $this->db->where('job_id', $job_id); //which row want to upgrade  
            $this->db->update('jobs_user_job'); 
            
               return array(
                'status' => 200,
                'favourite' => '0'
                );
            
            
        }
        else
        {
            $data = array(
            'user_id'=>$user_id,
            'job_id'=>$job_id,
            'datetime'=>$date
            );
            
            $data1 = array(
            'user_id'=>$user_id,
            'job_id'=>$job_id,
            'favourite_date'=>$date
            );
            $this->db->insert('jobs_favourite_job', $data); 
            $p = $this->db->select('id')->from('jobs_user_job')->where('user_id', $user_id)->where('job_id', $job_id)->get()->num_rows();
            if($p < 1){
            $this->db->insert('jobs_user_job', $data1); 
            }
            
            $ids = $this->db->insert_id();
            
            $this->db->set('favourite_status','1');
            $this->db->where('user_id', $user_id); //which row want to upgrade  
            $this->db->where('job_id', $job_id); //which row want to upgrade  
            $this->db->update('jobs_user_job');
            return array(
                'status' => 200,
                'favourite' => '1'
                );
            // if(!empty($ids))
            // {
                
                
            //      //user-fav
             
            
            // return array(
            //     'status' => 200,
            //     'favourite' => '1'
            //     );
            // }
            // else
            // {
            //     //user-fav
            
            
            // return array(
            //     'status' => 200,
            //     'favourite' => '0'
            //     );
            // }
        }
    } 
     
    public function similar_job($job_id,$user_id){
        
        $job_position = $this->db->get_where('jobs_description',array('job_id' => $job_id))->row()->job_position;
        $query = $this->db->query("SELECT juj.applied_status,juj.favourite_status,juj.user_id,jd.`job_id`,jd.`job_position`,jd.`company_name`, jd.`required_exp`,jd.`location`,jd.`job_desc`,jd.`favourite`,jd.category_id,jd.posted_on,jd.income,jd.max_salary FROM `jobs_description` as jd left join jobs_user_job as juj on (jd.job_id = juj.job_id AND juj.user_id = '$user_id' )  WHERE `job_position` LIKE '%$job_position%' AND jd.job_id !=$job_id");
        $resultpost = array();

        foreach ($query->result_array() as $row) {
            $job_id = $row['job_id'];
            //$id = $row['id'];
            $job_position	 = $row['job_position'];
            $company_name = $row['company_name'];
            $required_exp = $row['required_exp'];
            $location = $row['location'];
            $job_desc = $row['job_desc'];
            $favourite_status = $row['favourite_status'];
            $applied_status = $row['applied_status'];
            $posted_on = $row['posted_on'];
            $min_salary = $row['income'];
            $max_salary =$row['max_salary'];
             if($favourite_status==''){
                $favourite_status='0';
            }
            if($applied_status==''){
                $applied_status='0';
            }

            $resultpost[] = array('job_id' => $job_id,
                'job_position' => $job_position,
                'company_name' => $company_name,
                'required_exp' => $required_exp,
                'location' => $location,
                'job_desc' => $job_desc,
                'favourite_status' =>$favourite_status,
                'applied_status' =>$applied_status,
                'posted_on' => $posted_on,
                'min_salary' =>$min_salary,
                'max_salary' =>$max_salary
                );
        }

        return $resultpost;

    }
    /* old one made by nikhil using old table "jobs-profile-model"
    public function jobs_company_profile($user_id){
        $query = $this->db->query("SELECT * FROM `jobs_company_profile` WHERE `user_id`=$user_id ");
        $resultpost = array();

        foreach ($query->result_array() as $row) {
            $user_id = $row['user_id'];
            //$id = $row['id'];
            $name	 = $row['name'];
            $position = $row['position'];
            $company_name = $row['company_name'];
            $company_size = $row['company_size'];
            $company_industry_type = $row['company_industry_type'];
            $company_location = $row['company_location'];
            $establishment_year = $row['establishment_year'];
            $recruiter = $row['recruiter'];
            $companys_website = $row['companys_website'];
            $company_desc = $row['company_desc'];
            $company_logo = $row['company_logo'];
            

            $resultpost = array('user_id' => $user_id,
                'name' => $name,
                'position' => $position,
                'company_name' => $company_name,
                'company_size' => $company_size,
                'company_industry_type' => $company_industry_type,
                'company_location' =>$company_location,
                'establishment_year' =>$establishment_year,
                'recruiter' =>$recruiter,
                'companys_website' =>$companys_website,
                'company_desc' =>$company_desc,
                'company_logo' =>$company_logo
                );
        }

        return $resultpost;

    }
     */
     
     public function jobs_company_profile($company_id){
        $query = $this->db->query("SELECT * FROM `company_profile` WHERE `user_id`=$company_id ");
        $resultpost = array();
        $job_count = array();
        $profile_view = $this->db->query("SELECT views FROM `company_profile` WHERE `user_id`=$company_id ");
        foreach ($profile_view->result_array() as $row)
        {
                $views = $row['views'];
        }
        
        $views++;
        //echo ($views); die();
        //$pv = $profile_view;
        $this->db->set('views', $views); //value that used to update column  
        $this->db->where('user_id', $company_id); //which row want to upgrade  
        $this->db->update('company_profile'); 
       // print_r($pv); die();

        foreach ($query->result_array() as $row) {
            $company_id = $row['user_id'];
            //$id = $row['id'];
            // $name	 = $row['name'];
            // $position = $row['position'];
            $company_name = $row['company_name'];
            $company_size = $row['company_size'];
            $company_industry_type = $row['company_industry_type'];
            $sub_category_industry_type = $row['sub_category_industry_type'];
            $state = $row['state'];
            $city = $row['city'];
            // $company_location = $row['company_location'];
            $establishment_year = $row['dob'];
            $recruiter = $row['recruiter'];
            $companys_website = $row['company_website'];
            $company_desc = $row['company_description'];
            $company_logo = 'https://d2c8oti4is0ms3.cloudfront.net/images/job_image/'.$row['company_logo'];
            

            $company_data = array('company_id' => $company_id,
                // 'name' => $name,
                // 'position' => $position,
                'company_name' => $company_name,
                'company_size' => $company_size,
                'company_industry_type' => $company_industry_type,
                'sub_category_industry_type' => $sub_category_industry_type,
                'state' => $state,
                'city' => $city,
                // 'company_location' =>$company_location,
                'establishment_year' =>$establishment_year,
                'recruiter' =>$recruiter,
                'companys_website' =>$companys_website,
                'company_desc' =>$company_desc,
                'company_logo' =>$company_logo
                );
        }
        
        $reviews_counting = $this->db->get_where('company_profile',array('user_id' => $company_id))->row()->views;
        $avg_rating = $this->db->query("SELECT AVG(rating) as avsg FROM jobs_review WHERE company_id = $company_id")->result_array();
        $avgr = ($avg_rating[0]['avsg']);
        $job_count = $this->db->select('job_id')->from('jobs_description')->where('company_id', $company_id)->get()->num_rows();
        $review_count = $this->db->select('id')->from('jobs_review')->where('company_id', $company_id)->get()->num_rows();
        //echo $job_count; die();
        
       

        return $resultpost = array('company_data' =>$company_data,
                                    'view_count' => $reviews_counting,
                                    'average_rating' => $avgr,
                                     'job_count' =>   $job_count,
                                     'review_count' => $review_count);

    }
     public function jobs_main_cat($user_id){
         
        $query = $this->db->query("SELECT * FROM `jobs_category_master` WHERE 1");
        $resultpost = array();

        foreach ($query->result_array() as $row) {
            $category_id = $row['category_id'];
            $p_cat_id	 = $row['p_cat_id'];
            $category_name = $row['category_name'];
            $category_image = $row['category_image'];

            $resultpost[] = array('p_cat_id' => $p_cat_id,
                'category_name' => $category_name,
                'category_image' => $category_image
                );
        }

        return $resultpost;
     }
     
     public function cat_job_listing($category_id,$user_id,$page,$location,$salary,$work_experience,$sort_salary){
         
         $resultpost = array();
         if($page==""){
         $limit = " LIMIT 0, 10";   
        }
        else
        {
            $limit = 10;
            $start = 0;
            if ($page > 0) {
                if (!is_numeric($page)) {
                    $page = 1;
                }
            }
            $start = ($page - 1) * $limit;
            $limit = " LIMIT $start, $limit";   
        }

            if($sort_salary == 0){
            $query = $this->db->query("SELECT juj.applied_status,juj.favourite_status,jd.`job_id`,jd.`job_position`,jd.`company_name`, jd.`required_exp`,jd.`location`,jd.`job_desc`,jd.posted_on,jd.`favourite`,jd.income,jd.max_salary FROM `jobs_description` as jd left join jobs_user_job as juj on (jd.job_id = juj.job_id AND juj.user_id = '$user_id' ) group by jd.job_id  ORDER BY jd.max_salary ASC $limit");
            foreach ($query->result_array() as $row) {
            $job_id = $row['job_id'];
            //$id = $row['id'];
            $job_position	 = $row['job_position'];
            $company_name = $row['company_name'];
            $required_exp = $row['required_exp'];
            $location = $row['location'];
            $job_desc = $row['job_desc'];
            $favourite_status = $row['favourite_status'];
            $applied_status = $row['applied_status'];
            $posted_on = $row['posted_on'];
            $min_salary = $row['income'];
            $max_salary = $row['max_salary'];
            
            if($favourite_status==''){
                
                $favourite_status='0';
                // echo $favourite_status; die();
            }
            if($applied_status==''){
                $applied_status='0';
            }
            
            $resultpost[] = array('job_id' => $job_id,
                'job_position' => $job_position,
                'company_name' => $company_name,
                'required_exp' => $required_exp,
                'location' => $location,
                'job_desc' => $job_desc,
                'favourite_status' =>$favourite_status,
                'applied_status' =>$applied_status,
                'posted_on' => $posted_on,
                'min_salary' => $min_salary,
                'max_salary' => $max_salary
                );
                }
            
            }
            elseif($sort_salary == 1){
            $query = $this->db->query("SELECT juj.applied_status,juj.favourite_status,jd.`job_id`,jd.`job_position`,jd.`company_name`, jd.`required_exp`,jd.`location`,jd.`job_desc`,jd.posted_on,jd.`favourite`,jd.income,jd.max_salary FROM `jobs_description` as jd left join jobs_user_job as juj on (jd.job_id = juj.job_id AND juj.user_id = '$user_id' ) group by jd.job_id  ORDER BY jd.max_salary DESC $limit");
            foreach ($query->result_array() as $row) {
            $job_id = $row['job_id'];
            //$id = $row['id'];
            $job_position	 = $row['job_position'];
            $company_name = $row['company_name'];
            $required_exp = $row['required_exp'];
            $location = $row['location'];
            $job_desc = $row['job_desc'];
            $favourite_status = $row['favourite_status'];
            $applied_status = $row['applied_status'];
            $posted_on = $row['posted_on'];
            $min_salary = $row['income'];
            $max_salary = $row['max_salary'];
            
            if($favourite_status==''){
                
                $favourite_status='0';
                // echo $favourite_status; die();
            }
            if($applied_status==''){
                $applied_status='0';
            }
            
            $resultpost[] = array('job_id' => $job_id,
                'job_position' => $job_position,
                'company_name' => $company_name,
                'required_exp' => $required_exp,
                'location' => $location,
                'job_desc' => $job_desc,
                'favourite_status' =>$favourite_status,
                'applied_status' =>$applied_status,
                'posted_on' => $posted_on,
                'min_salary' => $min_salary,
                'max_salary' => $max_salary
                );
                }
            
            }
            else
            {
                $str1 = $location;
                $str21 = $salary;
                $s = $salary;
                $l = (explode(",",$str1));
                $qwe= (explode("-",$str21));
                $max_salary = (max($qwe)*100000);
                $min_salary = (min($qwe)*100000);
                $max = max(sizeof($l) , sizeof($s));
                for($i = 0; $i<sizeof($max);$i++){
                    $a = $l[$i];

                $query = $this->db->query("SELECT juj.applied_status,juj.favourite_status,jd.`job_id`,jd.`job_position`,jd.`company_name`, jd.`required_exp`,jd.`location`,jd.`job_desc`,jd.posted_on,jd.`favourite`,jd.income,jd.max_salary,jd.city FROM `jobs_description` as jd left join jobs_user_job as juj on (jd.job_id = juj.job_id AND juj.user_id = '$user_id' ) WHERE jd.category_id=$category_id AND jd.city LIKE %$%a OR jd.income >= $min_salary AND jd.max_salary <= $max_salary AND jd.required_exp > '$work_experience' group by jd.job_id ORDER BY jd.job_id DESC $limit");
                foreach ($query->result_array() as $row) {
                $job_id = $row['job_id'];
                //$id = $row['id'];
                $job_position	 = $row['job_position'];
                $company_name = $row['company_name'];
                $required_exp = $row['required_exp'];
                $location = $row['location'];
                $job_desc = $row['job_desc'];
                $favourite_status = $row['favourite_status'];
                $applied_status = $row['applied_status'];
                $min_salary = $row['income'];
                $max_salary = $row['max_salary'];
                $posted_on = $row['posted_on'];
            
                if($favourite_status==''){
                
                $favourite_status='0';
                // echo $favourite_status; die();
                }
                if($applied_status==''){
                    $applied_status='0';
                }
            
                $resultpost[] = array('job_id' => $job_id,
                'job_position' => $job_position,
                'company_name' => $company_name,
                'required_exp' => $required_exp,
                'location' => $location,
                'job_desc' => $job_desc,
                'favourite_status' =>$favourite_status,
                'applied_status' =>$applied_status,
                'posted_on' => $posted_on,
                'min_salary' => $min_salary,
                'max_salary' => $max_salary
                );
                }
                }
            }

        return $resultpost;
     }
     
     public function view_user_profile($user_id){
         
        $query = $this->db->query("SELECT `id`, `image`,`video`,`first_name`,`last_name`,`mobile`,`email`,`city`,`resume_head`,`gender`,`dob`,`marital_status`,`address_line1`,`address_line2`,`city`,`state`,`pincode`,`country`,`nationality`,`languages_known`,`resume` FROM `jobs_user_profile_master` WHERE user_id=$user_id ORDER BY id DESC LIMIT 1");
        $resultpost = array();
        $personal = array();
        $key_skills = array();
        $certificate = array();
        $employment = array();
        $preferred_job = array();
        $updated_date = array();

        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            
            $image = $row['image'];
            $video = $row['video'];
            $first_name	 = $row['first_name'];
            $last_name = $row['last_name'];
            $mobile = $row['mobile'];
            $email = $row['email'];
            $city = $row['city'];
            $resume_head = $row['resume_head'];
            $gender = $row['gender'];
            $dob = $row['dob'];
            $marital_status = $row['marital_status'];
            $address_line1 = $row['address_line1'];
            $address_line2 = $row['address_line2'];
            $state = $row['state'];
            $pincode = $row['pincode'];
            $country = $row['country'];
            $nationality =$row['nationality'];
            $languages_known = $row['languages_known'];
            if($image =="0"){
                $image ="";
            }
            else{
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/job_image/'.$row['image'];
            }
            if($video =="0"){
                $video ="";
            }
            else{
                $video = 'https://d2c8oti4is0ms3.cloudfront.net/images/job_image/'.$row['video'];
            }
            
            
            $resume = 'https://d2c8oti4is0ms3.cloudfront.net/images/job_image/'.$row['resume'];
            
            $personal[] = array('id' => $id,
                'image' => $image,
                'video' => $video,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'mobile' => $mobile,
                'email' => $email,
                'city' => $city,
                'resume_head' =>$resume_head,
                'gender' => $gender,
                'dob' =>$dob,
                'marital_status' =>$marital_status,
                'address_line1' =>$address_line1,
                'address_line2' =>$address_line2,
                'state' =>$state,
                'pincode' =>$pincode,
                'country' =>$country,
                'nationality' =>$nationality,
                'languages_known' =>$languages_known,
                'resume' =>$resume
                
                );
        }
        
        $query1 = $this->db->query("SELECT `id`,`technical_skills`,`technical_skills_desc` FROM `jobs_skils_job` WHERE `user_id`=$user_id");
        foreach ($query1->result_array() as $row) {
            $id = $row['id'];
            $technical_skills = $row['technical_skills'];
            $technical_skills_desc = $row['technical_skills_desc'];

            $key_skills[] = array('id' => $id,
                'technical_skills' => $technical_skills,
                'technical_skills_desc' => $technical_skills_desc
                );

        }
        
        $query2 =$this->db->query("SELECT `id`,`user_id`,`certificate_name`,`ce_issued_date`,`ce_description`,`ce_issued_by`,`achievement` FROM `jobs_user_certifications` WHERE `user_id`=$user_id");
        foreach ($query2->result_array() as $row) {
            $id = $row['id'];
            $user_id = $row['user_id'];
            $certificate_name = $row['certificate_name'];
            $ce_issued_date = $row['ce_issued_date'];
            $ce_description = $row['ce_description'];
            $ce_issued_by = $row['ce_issued_by'];
            $achievement = $row['achievement'];

            $certificate[] = array('id' => $id,
                'certificate_name' => $certificate_name,
                'ce_issued_date' => $ce_issued_date,
                'ce_description' => $ce_description,
                'ce_issued_by' => $ce_issued_by,
                'achievement' => $achievement
                
                );

        }
        
        $query3 =$this->db->query("SELECT `id`,`user_id`,`company_name`,`company_type`,`employment_type`,`designation`,`location`,`work_experience`,`work_start`,`work_end`,`desc_profile` FROM `jobs_previous_job` WHERE `user_id`=$user_id");
        foreach ($query3->result_array() as $row) {
            $id = $row['id'];
            $user_id = $row['user_id'];
            $company_name = $row['company_name'];
            $company_type = $row['company_type'];
            $employment_type = $row['employment_type'];
            $designation = $row['designation'];
            $location = $row['location'];
            $work_experience = $row['work_experience'];
            $work_start = $row['work_start'];
            $work_end = $row['work_end'];
            $desc_profile = $row['desc_profile'];
            

            $employment[] = array('id' => $id,
                'company_name' => $company_name,
                'company_type' => $company_type,
                'employment_type' => $employment_type,
                'designation' => $designation,
                'location' => $location,
                'work_experience' => $work_experience,
                'work_start' => $work_start,
                'work_end' => $work_end,
                'desc_profile' =>$desc_profile,
                
                
                );

        }
        
        $query4 =$this->db->query("SELECT `id`,`user_id`,`job_type`,`job_location`,`job_position`,`min_salary` FROM `jobs_preferred_job` WHERE `user_id`=$user_id");
        foreach ($query4->result_array() as $row) {
            $id = $row['id'];
            $user_id = $row['user_id'];
            $job_type = $row['job_type'];
            $job_location = $row['job_location'];
            $job_position = $row['job_position'];
            $min_salary = $row['min_salary'];
            
            

            $preferred_job[] = array('id' => $id,
                'job_type' => $job_type,
                'job_location' => $job_location,
                'job_position' => $job_position,
                'min_salary' => $min_salary

                );

        }
        
         {
        $p = $this->db->select('id')->from('jobs_user_profile_master')->where('user_id',$user_id)->get()->num_rows();
        $q = $this->db->select('id')->from('jobs_skils_job')->where('user_id',$user_id)->get()->num_rows();
        $r1 = $this->db->select('id')->from('jobs_user_education')->where('user_id',$user_id)->where('job_education_id',1)->get()->num_rows();
        $r2 = $this->db->select('id')->from('jobs_user_education')->where('user_id',$user_id)->where('job_education_id',2)->get()->num_rows();
        $r3 = $this->db->select('id')->from('jobs_user_education')->where('user_id',$user_id)->where('job_education_id',3)->get()->num_rows();
        $r4 = $this->db->select('id')->from('jobs_user_education')->where('user_id',$user_id)->where('job_education_id',4)->get()->num_rows();
        $r5 = $this->db->select('id')->from('jobs_user_education')->where('user_id',$user_id)->where('job_education_id',5)->get()->num_rows();
        $s = $this->db->select('id')->from('jobs_user_certifications')->where('user_id',$user_id)->get()->num_rows();
        $t = $this->db->select('id')->from('jobs_previous_job')->where('user_id',$user_id)->get()->num_rows();
        $u = $this->db->select('id')->from('jobs_preferred_job')->where('user_id',$user_id)->get()->num_rows();
        $v = $this->db->select('resume')->from('jobs_user_profile_master')->where('user_id',$user_id)->get()->num_rows();
        //   print_r($q); die();
        if($p>0){
            $comper= 20;
        }
        else{
            $comper= 0;
        }
        
        if($q>0){
            $comkey = 10;
        }   
        else{
            $comkey = 0;
        }
        
        if($r1>0){
            $r1 = 4;
        }
        else{
            $r1 = 0;
        }
        
        if($r2>0){
             $r2 = 4;
        }
       else{
              $r2 = 0;
       }
        if($r3>0){
            $r3 = 4;
        }else{
            $r3 = 0;
        }
        
        if($r4>0){
              $r4 = 4;
        }
        else{
              $r4 = 0;
        }
      
        if($r5>0){
            $r5 = 4;
        }else{
              $r5 = 0;
        }
        
        if($s>0){
            $s =10;
        }else{
            $s =0;
        }
        
        if($t>0){
              $t =15;
        }
        else{
             $t =0;
        }
        
        if($u>0){
             $u = 15;
        }else{
             $u = 0;
        }
       
        if($v>0){
             $v= 10;
        }else{
             $v= 0;
        }
       

        }
         $percentage = ($comper + $comkey + $r1 + $r2 + $r3 + $r4 + $r5 +$s+ $t+ $u +$v);
         $updated_date = $this->db->get_where('jobs_user_profile_master',array('user_id' => $user_id))->row()->updated_at;

        $resultpost = array('personal' =>$personal,
        'key_skills' =>$key_skills,
        'certificate' =>$certificate,
        'employment' =>$employment,
        'preferred_job' =>$preferred_job,
        'percentage' => $percentage,
        'updated_date' =>$updated_date
            );
        
        return $resultpost;
     }
     
     public function view_user_profile_edu($user_id){
         
        $query = $this->db->query("SELECT `id`,`user_id`,`job_education_id`,`school_name`,`board_name`,`medium`,`percentage`,`passing_year`,`grade`,`specifications`,`courses` FROM `jobs_user_education` WHERE `user_id`= $user_id ORDER BY job_education_id ASC");
        $resultpost = array();
        $personal = array();
        $key_skills = array();
        $certificate = array();
        $employment = array();
        $preferred_job = array();
        $Doctorate = array();

        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $user_id = $row['user_id'];
            $job_education_id	 = $row['job_education_id'];
            $school_name = $row['school_name'];
            $board_name = $row['board_name'];
            $medium = $row['medium'];
            $percentage = $row['percentage'];
            $passing_year = $row['passing_year'];
            $grade = $row['grade'];
            $specifications = $row['specifications'];
            $courses = $row['courses'];

            $personal[] = array('id' => $id,
                'user_id' => $user_id,
                'job_education_id' => $job_education_id,
                'school_name' => $school_name,
                'board_name' => $board_name,
                'medium' => $medium,
                'percentage' => $percentage,
                'passing_year' =>$passing_year,
                'grade' =>$grade,
                'specifications' =>$specifications,
                'courses' =>$courses
               

                );
                

                
        }
        
       /* $query1 = $this->db->query("SELECT `id`,`user_id`,`job_education_id`,`school_name`,`board_name`,`medium`,`percentage`,`passing_year`,`grade`,`specifications`,`courses` FROM `jobs_user_education` WHERE `user_id`= $user_id AND `job_education_id`=2");
        foreach ($query1->result_array() as $row) {
            $id = $row['id'];
            $user_id = $row['user_id'];
            $job_education_id	 = $row['job_education_id'];
            $school_name = $row['school_name'];
            $board_name = $row['board_name'];
            $medium = $row['medium'];
            $percentage = $row['percentage'];
            $passing_year = $row['passing_year'];
            $grade = $row['grade'];
            $specifications = $row['specifications'];
            $courses = $row['courses'];

            $key_skills = array('id' => $id,
                'user_id' => $user_id,
                'job_education_id' => $job_education_id,
                'school_name' => $school_name,
                'board_name' => $board_name,
                'medium' => $medium,
                'percentage' => $percentage,
                'passing_year' =>$passing_year,
                'grade' =>$grade,
                'specifications' =>$specifications,
                'courses' =>$courses
               

                );

        }
        
        $query2 =$this->db->query("SELECT `id`,`user_id`,`job_education_id`,`school_name`,`board_name`,`medium`,`percentage`,`passing_year`,`grade`,`specifications`,`courses` FROM `jobs_user_education` WHERE `user_id`= $user_id AND `job_education_id`=3");
        foreach ($query2->result_array() as $row) {
            $id = $row['id'];
            $user_id = $row['user_id'];
            $job_education_id	 = $row['job_education_id'];
            $school_name = $row['school_name'];
            $board_name = $row['board_name'];
            $medium = $row['medium'];
            $percentage = $row['percentage'];
            $passing_year = $row['passing_year'];
            $grade = $row['grade'];
            $specifications = $row['specifications'];
            $courses = $row['courses'];

            $certificate = array('id' => $id,
                'user_id' => $user_id,
                'job_education_id' => $job_education_id,
                'school_name' => $school_name,
                'board_name' => $board_name,
                'medium' => $medium,
                'percentage' => $percentage,
                'passing_year' =>$passing_year,
                'grade' =>$grade,
                'specifications' =>$specifications,
                'courses' =>$courses
               

                );

        }
        
        $query3 =$this->db->query("SELECT `id`,`user_id`,`job_education_id`,`school_name`,`board_name`,`medium`,`percentage`,`passing_year`,`grade`,`specifications`,`courses` FROM `jobs_user_education` WHERE `user_id`= $user_id AND `job_education_id`=4");
        foreach ($query3->result_array() as $row) {
           $id = $row['id'];
            $user_id = $row['user_id'];
            $job_education_id	 = $row['job_education_id'];
            $school_name = $row['school_name'];
            $board_name = $row['board_name'];
            $medium = $row['medium'];
            $percentage = $row['percentage'];
            $passing_year = $row['passing_year'];
            $grade = $row['grade'];
            $specifications = $row['specifications'];
            $courses = $row['courses'];

            $employment = array('id' => $id,
                'user_id' => $user_id,
                'job_education_id' => $job_education_id,
                'school_name' => $school_name,
                'board_name' => $board_name,
                'medium' => $medium,
                'percentage' => $percentage,
                'passing_year' =>$passing_year,
                'grade' =>$grade,
                'specifications' =>$specifications,
                'courses' =>$courses
               

                );

        }
        
        $query4 =$this->db->query("SELECT `id`,`user_id`,`job_education_id`,`school_name`,`board_name`,`medium`,`percentage`,`passing_year`,`grade`,`specifications`,`courses` FROM `jobs_user_education` WHERE `user_id`= $user_id AND `job_education_id`=5");
        foreach ($query4->result_array() as $row) {
           $id = $row['id'];
            $user_id = $row['user_id'];
            $job_education_id	 = $row['job_education_id'];
            $school_name = $row['school_name'];
            $board_name = $row['board_name'];
            $medium = $row['medium'];
            $percentage = $row['percentage'];
            $passing_year = $row['passing_year'];
            $grade = $row['grade'];
            $specifications = $row['specifications'];
            $courses = $row['courses'];

            $preferred_job = array('id' => $id,
                'user_id' => $user_id,
                'job_education_id' => $job_education_id,
                'school_name' => $school_name,
                'board_name' => $board_name,
                'medium' => $medium,
                'percentage' => $percentage,
                'passing_year' =>$passing_year,
                'grade' =>$grade,
                'specifications' =>$specifications,
                'courses' =>$courses
               

                );

        }
        
        $query5 =$this->db->query("SELECT `id`,`user_id`,`job_education_id`,`school_name`,`board_name`,`medium`,`percentage`,`passing_year`,`grade`,`specifications`,`courses` FROM `jobs_user_education` WHERE `user_id`= $user_id AND `job_education_id`=6");
        foreach ($query5->result_array() as $row) {
           $id = $row['id'];
            $user_id = $row['user_id'];
            $job_education_id	 = $row['job_education_id'];
            $school_name = $row['school_name'];
            $board_name = $row['board_name'];
            $medium = $row['medium'];
            $percentage = $row['percentage'];
            $passing_year = $row['passing_year'];
            $grade = $row['grade'];
            $specifications = $row['specifications'];
            $courses = $row['courses'];

            $Doctorate = array('id' => $id,
                'user_id' => $user_id,
                'job_education_id' => $job_education_id,
                'school_name' => $school_name,
                'board_name' => $board_name,
                'medium' => $medium,
                'percentage' => $percentage,
                'passing_year' =>$passing_year,
                'grade' =>$grade,
                'specifications' =>$specifications,
                'courses' =>$courses
               

                );

        }
        // $resultpost[] = array('SSC(X)' =>$personal,
        // 'HSC(XII)' =>$key_skills,
        // 'Diploma' =>$certificate,
        // 'Graduate' =>$employment,
        // 'Post-graduate' =>$preferred_job,
        // 'Doctorate'=>$Doctorate
        //     ); */
        
        return $resultpost = $personal;
     }
     
     public function languages_known($user_id){
         
        $query = $this->db->query("SELECT DISTINCT(`name`),`id` FROM `languages` ORDER BY name ASC");
        $resultpost = array();

        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $name	 = $row['name'];

            $resultpost[] = array('id' => $id,
                'name' => $name
               
                );
        }

        return $resultpost;
     }
     
     public function available($user_id){
         $resultpost = array();
        $p = $this->db->select('id')->from('jobs_user_profile_master')->where('user_id', $user_id)->get()->num_rows();
        if($p<1)
        {
            $resulpost = array('status'=>0);
        }
        else
        {
             $resulpost =array('status'=>1);
        }
        

        // print_r($resulpost); die();

        return $resultpost[] = $resulpost;
     }
     
    public function user_dashboard($user_id){
        date_default_timezone_set('Asia/Kolkata');
        $present = date('Y-m-d H:i:s');
        $new_date = date('Y-m-d', strtotime('-7 days', strtotime($present)));
        
        $query = $this->db->query("SELECT `id`,`image`,`first_name`,`last_name`,`mobile`,`email`,`city`,`state`,`recruiter_action`,`profile_view_count` FROM `jobs_user_profile_master` WHERE `user_id`=$user_id");
        $resultpost = array();
        $percentage = array();

        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $image	 = $row['image'];
            // $position = $row['position'];
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $mobile = $row['mobile'];
            $email = $row['email'];
            $state = $row['state'];
            $city = $row['city'];
            $recruiter_action = $row['recruiter_action'];
            $profile_view_count = $row['profile_view_count'];
            if($image =="0"){
                $image ="";
            }
            else{
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/job_image/'.$row['image'];
            }

            $personal = array('id' => $id,
                // 'name' => $name,
                // 'position' => $position,
                'image' => $image,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'mobile' => $mobile,
                'email' => $email,
                'state' => $state,
                // 'company_location' =>$company_location,
                'city' =>$city,
                'recruiter_action' => $recruiter_action,
                'profile_view_count' => $profile_view_count
                
                );
        }

        {
        $p = $this->db->select('id')->from('jobs_user_profile_master')->where('user_id',$user_id)->get()->num_rows();
        $q = $this->db->select('id')->from('jobs_skils_job')->where('user_id',$user_id)->get()->num_rows();
        $r1 = $this->db->select('id')->from('jobs_user_education')->where('user_id',$user_id)->where('job_education_id',1)->get()->num_rows();
        $r2 = $this->db->select('id')->from('jobs_user_education')->where('user_id',$user_id)->where('job_education_id',2)->get()->num_rows();
        $r3 = $this->db->select('id')->from('jobs_user_education')->where('user_id',$user_id)->where('job_education_id',3)->get()->num_rows();
        $r4 = $this->db->select('id')->from('jobs_user_education')->where('user_id',$user_id)->where('job_education_id',4)->get()->num_rows();
        $r5 = $this->db->select('id')->from('jobs_user_education')->where('user_id',$user_id)->where('job_education_id',5)->get()->num_rows();
        $s = $this->db->select('id')->from('jobs_user_certifications')->where('user_id',$user_id)->get()->num_rows();
        $t = $this->db->select('id')->from('jobs_previous_job')->where('user_id',$user_id)->get()->num_rows();
        $u = $this->db->select('id')->from('jobs_preferred_job')->where('user_id',$user_id)->get()->num_rows();
        $v = $this->db->select('resume')->from('jobs_user_profile_master')->where('user_id',$user_id)->get()->num_rows();
        //   print_r($v); die();
       if($p>0){
            $comper= 20;
        }
        else{
            $comper= 0;
        }
        
        if($q>0){
            $comkey = 10;
        }   
        else{
            $comkey = 0;
        }
        
        if($r1>0){
            $r1 = 4;
        }
        else{
            $r1 = 0;
        }
        
        if($r2>0){
             $r2 = 4;
        }
       else{
              $r2 = 0;
       }
        if($r3>0){
            $r3 = 4;
        }else{
            $r3 = 0;
        }
        
        if($r4>0){
              $r4 = 4;
        }
        else{
              $r4 = 0;
        }
      
        if($r5>0){
            $r5 = 4;
        }else{
              $r5 = 0;
        }
        
        if($s>0){
            $s =10;
        }else{
            $s =0;
        }
        
        if($t>0){
              $t =15;
        }
        else{
             $t =0;
        }
        
        if($u>0){
             $u = 15;
        }else{
             $u = 0;
        }
       
        if($v>0){
             $v= 10;
        }else{
             $v= 0;
        }
       

        }
        
        {
            $query1 = $this->db->query("SELECT `study`,`department`,`work_exp`,`location` FROM `jobs_alert` WHERE `user_id` =$user_id")->result_array();
        //print_r(($query1)); die();
        
        $resultpost = array();
         $count_alert =0;
        for($i = 0; $i<sizeof($query1);$i++){
            $a = $query1[$i]['study'];
            $b =  $query1[$i]['location'];
            $c = $query1[$i]['work_exp'];
           // print_r($c); die();
            $query10 = $this->db->query("SELECT jd.`favourite` FROM `jobs_description` as jd left join jobs_user_job as juj on (jd.job_id = juj.job_id AND juj.user_id = '$user_id' ) WHERE jd.job_position LIKE '%$a%'|| jd.city LIKE '%$b%'  group by jd.job_id ORDER BY jd.job_id DESC");
            $count_alert += $query10->num_rows();
        }
        
          $count_alert; 
        }
        $count_recommanded = 0;
        {
         $p = $this->db->select('id')->from('jobs_preferred_job')->where('user_id',$user_id)->get()->num_rows();
        if($p>0)
        {
            $query2 = $this->db->query("SELECT * FROM `jobs_preferred_job` WHERE `user_id` =$user_id")->row();

        $a = $query2->job_position;
        $b =  $query2->job_location;
        $qwe1 = $query2->min_salary;
        $qwe1 = str_replace(',', '', $qwe1);
        $qwe= (explode("-",$qwe1));
        $max_salary  = (max($qwe)) * 12;
        $min_salary = (min($qwe)) * 12;
            $query3 = $this->db->query("SELECT juj.applied_status,juj.favourite_status,jd.`job_id`,jd.`job_position`,jd.`company_name`, jd.`required_exp`,jd.`location`,jd.`job_desc`,jd.posted_on,jd.`favourite`,jd.income,jd.max_salary FROM `jobs_description` as jd left join jobs_user_job as juj on (jd.job_id = juj.job_id AND juj.user_id = '$user_id' ) WHERE jd.job_position LIKE '%$a%'|| jd.city LIKE '%$b%' || jd.income > $min_salary || jd.max_salary <  $max_salary group by jd.job_id ORDER BY jd.job_id DESC");
            $count_recommanded += $query3->num_rows();
        }
        else
        {
           $count_recommanded = 0; 
        }
            // recommanded jobs
        

        }
        $count_new_job = 0;
        {
            // new jobs
            $query4 = $this->db->query("SELECT juj.applied_status,juj.favourite_status,jd.`job_id`,jd.`job_position`,jd.`company_name`, jd.`required_exp`,jd.`state`,jd.`job_desc`,jd.posted_on,jd.`favourite`,jd.income,jd.max_salary FROM `jobs_description` as jd left join jobs_user_job as juj on (jd.job_id = juj.job_id AND juj.user_id = '$user_id' ) WHERE jd.posted_on >= '$new_date' group by jd.job_id");
            $count_new_job += $query4->num_rows();
        }
        
        $saved_jobs = $this->db->select('id')->from('jobs_user_job')->where('user_id', $user_id)->where('favourite_status','1')->get()->num_rows();
        $percentage = ($comper + $comkey + $r1 + $r2 + $r3 + $r4 + $r5 +$s+ $t+ $u +$v);
        $updated_date = $this->db->get_where('jobs_user_profile_master',array('user_id' => $user_id))->row()->updated_at;
         //print_r($saved_jobs); die();
        return $resultpost = array('personal' =>$personal,
        'percentage' =>$percentage,
        'saved_jobs' =>$saved_jobs,
        'updated_date' =>$updated_date,
        'count_alert' =>$count_alert,
        'count_recommanded' => $count_recommanded,
        'count_new_job' => $count_new_job
        
            );

    }
    
    public function delete_details($user_id,$type,$id){
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
        //$p = $this->db->select('id')->from('jobs_favourite_job')->where('user_id', $user_id)->where('job_id', $job_id)->get()->num_rows();
        if($type == 1){
            $this->db->query("DELETE FROM `jobs_skils_job` WHERE user_id='$user_id' and id='$id'");
            return array(
                'status' => 200,
                'message' => 'deleted successfully'
            );
        }
        elseif($type == 2){
            $this->db->query("DELETE FROM `jobs_user_education` WHERE user_id='$user_id' and id='$id'");
            return array(
                'status' => 200,
                'message' => 'deleted successfully'
            );
        }
        elseif($type == 3){
            $this->db->query("DELETE FROM `jobs_user_certifications` WHERE user_id='$user_id' and id='$id'");
            return array(
                'status' => 200,
                'message' => 'deleted successfully'
            );
        }
        elseif($type == 4){
            $this->db->query("DELETE FROM `jobs_preferred_job` WHERE user_id='$user_id' and id='$id'");
            return array(
                'status' => 200,
                'message' => 'deleted successfully'
            );
        }
        elseif($type == 5){
            $this->db->query("DELETE FROM `jobs_previous_job` WHERE user_id='$user_id' and id='$id'");
            return array(
                'status' => 200,
                'message' => 'deleted successfully'
            );
        }
    }
    
    public function application_status($user_id,$job_id){
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
        $p = $this->db->select('id')->from('jobs_interview')->where('user_id',$user_id)->where('job_id',$job_id)->get()->num_rows();
        
        if($p > 0)
        {
            $query = $this->db->query("SELECT jobs_user_job.applied_date, jobs_interview.interview_status,jobs_interview.interview_start_date FROM `jobs_user_job` LEFT JOIN jobs_interview ON jobs_user_job.job_id = jobs_interview.job_id WHERE jobs_interview.job_id = $job_id AND jobs_interview.user_id = $user_id ");
            foreach ($query->result_array() as $row) {
            $interview_status = $row['interview_status'];
            $interview_start_date	 = $row['interview_start_date'];
            $applied_date = $row['applied_date'];

            $resultpost = array('applied_status' => $interview_status,
                                  'interview_start_date' => $interview_start_date,
                                  'applied_date' => $applied_date
                );
            }
        }
        else 
        {
         
            $query = $this->db->query("SELECT `applied_status`,`applied_date` FROM `jobs_user_job` WHERE `user_id` = $user_id AND `job_id` = $job_id ");
            
            foreach ($query->result_array() as $row) {
            $applied_status = $row['applied_status'];
            $applied_date	 = $row['applied_date'];

            $resultpost = array('applied_status' => $applied_status,
                                'interview_start_date' => "",
                                 'applied_date' => $applied_date,
                );
        }
        }
        
        return $resultpost;
    }
    
    public function company_job_listing($user_id,$company_id,$page)
    {
        if($page==""){
         $limit = " LIMIT 0, 10";   
        }
        else
        {
            $limit = 10;
            $start = 0;
            if ($page > 0) {
                if (!is_numeric($page)) {
                    $page = 1;
                }
            }
            $start = ($page - 1) * $limit;
            $limit = " LIMIT $start, $limit";   
        }
        
        $resultpost = array();
        $query = $this->db->query("SELECT juj.applied_status,juj.favourite_status,jd.`job_id`,jd.`job_position`,jd.`company_name`, jd.`required_exp`,jd.`location`,jd.`job_desc`,jd.`favourite`,jd.posted_on,jd.income,jd.max_salary FROM `jobs_description` as jd left join jobs_user_job as juj on (jd.job_id = juj.job_id AND juj.user_id = '$user_id' ) WHERE jd.company_id = $company_id group by jd.job_id ORDER BY jd.job_id DESC $limit");
            foreach ($query->result_array() as $row) {
            $job_id = $row['job_id'];
            //$id = $row['id'];
            $job_position	 = $row['job_position'];
            $company_name = $row['company_name'];
            $required_exp = $row['required_exp'];
            $location = $row['location'];
            $job_desc = $row['job_desc'];
            $favourite_status = $row['favourite_status'];
            $applied_status = $row['applied_status'];
            $posted_on  = $row['posted_on'];
            $min_salary = $row['income'];
            $max_salary = $row['max_salary'];
            
            
            if($favourite_status==''){
                
                $favourite_status='0';
                // echo $favourite_status; die();
            }
            if($applied_status==''){
                $applied_status='0';
            }
            
            $resultpost[] = array('job_id' => $job_id,
                'job_position' => $job_position,
                'company_name' => $company_name,
                'required_exp' => $required_exp,
                'location' => $location,
                'job_desc' => $job_desc,
                'favourite_status' =>$favourite_status,
                'applied_status' =>$applied_status,
                'posted_on' => $posted_on,
                'min_salary' =>$min_salary,
                'max_salary' =>$max_salary
                );
        }
        return $resultpost;
    }
    
    public function job_alert($id,$user_id,$study,$department,$work_exp,$location, $alert_name,$salary )
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        // last modified date for profile
        $this->db->set('updated_at', $created_at);
        $this->db->where('user_id', $user_id); //which row want to upgrade  
        $this->db->update('jobs_user_profile_master');
        // ends here
        
        if($id == ""){
            $alert_data = array(
            'user_id' => $user_id,
            'study' => $study,
            'department' => $department,
            'work_exp' => $work_exp,
            'location' => $location,
            'alert_name' => $alert_name,
            'salary' => $salary,
            'created_by' => $user_id
            
        );
        $success =  $this->db->insert('jobs_alert',$alert_data);

        } else {
            $alert_data = array(
            'user_id' => $user_id,
            'study' => $study,
            'department' => $department,
            'work_exp' => $work_exp,
            'location' => $location,
            'alert_name' => $alert_name,
            'salary' =>$salary,
            'created_by' => $user_id
            
        );
         $this->db->where('user_id', $user_id);
         $this->db->where('id',$id);
        $success =  $this->db->update('jobs_alert',$alert_data);
        }
        
        
        
        //$id = $this->db->insert_id();
        if ($success) {
            $date_array = array(
                'user_id' => $user_id,
                'alert_name' => $alert_name,
            );
            return array('status' => 201, 'message' => 'success', 'data' => $date_array);
        } else {
            return array(
                'status' => 208,
                'message' => 'failed'
            );
        }
    }
    
    public function delete_job_alert($user_id,$id)
     {
         
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
        //$p = $this->db->select('id')->from('jobs_favourite_job')->where('user_id', $user_id)->where('job_id', $job_id)->get()->num_rows();
        
            $this->db->query("DELETE FROM `jobs_alert` WHERE user_id='$user_id' AND id='$id'");
            return array(
                'status' => 200,
                'message' => 'deleted successfully'
            );
      }
      
    public function view_alert_listing($user_id)
     {
         $personal = array();
        $query = $this->db->query("SELECT `id`,`user_id`,`study`,`department`,`work_exp`,`location`,`alert_name`,`salary` FROM `jobs_alert` WHERE `user_id`= $user_id");
        

        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $user_id = $row['user_id'];
            $study	 = $row['study'];
            $department = $row['department'];
            $work_exp = $row['work_exp'];
            $location = $row['location'];
            $alert_name = $row['alert_name'];
            $salary = $row['salary'];
            

            $personal[] = array('id' => $id,
                'user_id' => $user_id,
                'study' => $study,
                'department' => $department,
                'work_exp' => $work_exp,
                'location' => $location,
                'alert_name' => $alert_name,
                'salary' => $salary

                );
        }
     return $personal;
     }
     
     public function job_title($user_id)
     {
         $personal = array();
        $query = $this->db->query("SELECT * FROM `job_category`");
        

        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $category = $row['category'];
            

            $personal[] = array('id' => $id,
                'category' => $category,
                );
        }
     return $personal;
     }
     
     public function view_alert_jobs($user_id,$page)
     {
         
        if($page==""){
         $limit = " LIMIT 0, 10";   
        }
        else
        {
            $limit = 10;
            $start = 0;
            if ($page > 0) {
                if (!is_numeric($page)) {
                    $page = 1;
                }
            }
            $start = ($page - 1) * $limit;
            $limit = " LIMIT $start, $limit";   
        }
        
        $query1 = $this->db->query("SELECT `study`,`department`,`work_exp`,`location` FROM `jobs_alert` WHERE `user_id` =$user_id")->result_array();
        //print_r(($query1)); die();
        
        $resultpost = array();
         $count =0;
        for($i = 0; $i<sizeof($query1);$i++){
            $a = $query1[$i]['study'];
            $b =  $query1[$i]['location'];
            $c = $query1[$i]['work_exp'];
           // print_r($c); die();
            $query = $this->db->query("SELECT juj.applied_status,juj.favourite_status,jd.`job_id`,jd.`job_position`,jd.`company_name`, jd.`required_exp`,jd.`location`,jd.`job_desc`,jd.posted_on,jd.`favourite`,jd.income,jd.max_salary FROM `jobs_description` as jd left join jobs_user_job as juj on (jd.job_id = juj.job_id AND juj.user_id = '$user_id' ) WHERE jd.job_position LIKE '%$a%'|| jd.city LIKE '%$b%'  group by jd.job_id ORDER BY jd.job_id DESC $limit");

            foreach ($query->result_array() as $row) {
            $job_id = $row['job_id'];
            //$id = $row['id'];
            $job_position	 = $row['job_position'];
            $company_name = $row['company_name'];
            $required_exp = $row['required_exp'];
            $location = $row['location'];
            $job_desc = $row['job_desc'];
            $favourite_status = $row['favourite_status'];
            $applied_status = $row['applied_status'];
            $posted_on  = $row['posted_on'];
            $min_salary = $row['income'];
            $max_salary = $row['max_salary'];
            
            if($favourite_status==''){
                
                $favourite_status='0';
                // echo $favourite_status; die();
            }
            if($applied_status==''){
                $applied_status='0';
            }
            
            $resultpost[] = array('job_id' => $job_id,
                'job_position' => $job_position,
                'company_name' => $company_name,
                'required_exp' => $required_exp,
                'location' => $location,
                'job_desc' => $job_desc,
                'favourite_status' =>$favourite_status,
                'applied_status' =>$applied_status,
                'posted_on' => $posted_on,
                'min_salary' =>$min_salary,
                'max_salary' =>$max_salary
                );
        }
        }
        return $resultpost;
    
     }
     
    public function view_recommended_jobs($user_id,$page)
     {
         
        if($page==""){
         $limit = " LIMIT 0, 10";   
        }
        else
        {
            $limit = 10;
            $start = 0;
            if ($page > 0) {
                if (!is_numeric($page)) {
                    $page = 1;
                }
            }
            $start = ($page - 1) * $limit;
            $limit = " LIMIT $start, $limit";   
        }
        
        $query1 = $this->db->query("SELECT * FROM `jobs_preferred_job` WHERE `user_id` =$user_id")->row();

        $resultpost = array();
        $count =0;

            $a = $query1->job_position;
           
            $b =  $query1->job_location;
             //print_r(($b)); die();
            $qwe1 = $query1->min_salary;
            $qwe1 = str_replace(',', '', $qwe1);
            
            $qwe= (explode("-",$qwe1));
            //print_r(($qwe)); die();
            $max_salary  = (max($qwe)) * 12;
            $min_salary = (min($qwe)) * 12;
            
            $query = $this->db->query("SELECT juj.applied_status,juj.favourite_status,jd.`job_id`,jd.`job_position`,jd.`company_name`, jd.`required_exp`,jd.`location`,jd.`job_desc`,jd.posted_on,jd.`favourite`,jd.income,jd.max_salary FROM `jobs_description` as jd left join jobs_user_job as juj on (jd.job_id = juj.job_id AND juj.user_id = '$user_id' ) WHERE jd.job_position LIKE '%$a%'|| jd.city LIKE '%$b%' || jd.income > $min_salary || jd.max_salary <  $max_salary group by jd.job_id ORDER BY jd.job_id DESC $limit");

            foreach ($query->result_array() as $row) {
            $job_id = $row['job_id'];
            //$id = $row['id'];
            $job_position	 = $row['job_position'];
            $company_name = $row['company_name'];
            $required_exp = $row['required_exp'];
            $location = $row['location'];
            $job_desc = $row['job_desc'];
            $favourite_status = $row['favourite_status'];
            $applied_status = $row['applied_status'];
            $posted_on  = $row['posted_on'];
            $min_salary = $row['income'];
            $max_salary = $row['max_salary'];
            
            if($favourite_status==''){
                
                $favourite_status='0';
                // echo $favourite_status; die();
            }
            if($applied_status==''){
                $applied_status='0';
            }
            
            $resultpost[] = array('job_id' => $job_id,
                'job_position' => $job_position,
                'company_name' => $company_name,
                'required_exp' => $required_exp,
                'location' => $location,
                'job_desc' => $job_desc,
                'favourite_status' =>$favourite_status,
                'applied_status' =>$applied_status,
                'posted_on' => $posted_on,
                'min_salary' =>$min_salary,
                'max_salary' =>$max_salary
                );
        }
        
        return $resultpost;
    
     }
     
    public function company_review($user_id,$company_id,$current_employ,$employment_status, $review_title, $company_pros, $company_cons,$comment_or_advise,$rating,$designation,$base_salary,$anonymously )
    {
        $p = $this->db->select('id')->from('jobs_review')->where('user_id',$user_id)->where('company_id',$company_id)->get()->num_rows();
        if($p < 1)
        {
            $review_data = array(
            'user_id' => $user_id,
            'company_id' => $company_id,
            'current_employ' => $current_employ,
            'employment_status' => $employment_status,
            'review_title' => $review_title,
            'company_pros' => $company_pros,
            'company_cons' => $company_cons,
            'comment_or_advise' => $comment_or_advise,
            'rating' => $rating,
            'designation' => $designation,
            'base_salary' => $base_salary,
            'anonymously' => $anonymously,
            );
            
            $success = $this->db->insert('jobs_review', $review_data);
            if ($success) {
                $date_array = array(
                    'user_id' => $user_id,
                    'company_id' => $company_id,
                );
                return array('status' => 201, 'message' => 'success', 'data' => $date_array);
            }   
            else {
                return array(
                    'status' => 208,
                    'message' => 'failed'
                );
            }

        }
        else
        {
            $review_data = array(
                'user_id' => $user_id,
                'company_id' => $company_id,
                'current_employ' => $current_employ,
                'employment_status' => $employment_status,
                'review_title' => $review_title,
                'company_pros' => $company_pros,
                'company_cons' => $company_cons,
                'comment_or_advise' => $comment_or_advise,
                'rating' => $rating,
                'designation' => $designation,
                'base_salary' => $base_salary,
                'anonymously' => $anonymously,
                );
                
                $this->db->where('user_id', $user_id);
                $this->db->where('company_id', $company_id);
                $success = $this->db->update('jobs_review', $review_data);
                if ($success) {
                    $date_array = array(
                        'user_id' => $user_id,
                        'company_id' => $company_id,
                    );
                    return array('status' => 201, 'message' => 'success', 'data' => $date_array);
                }   
                else {
                    return array(
                        'status' => 208,
                        'message' => 'failed'
                    );
                }
  
        }
    }
        
    public function view_company_reviews($user_id,$company_id,$page)
    {
        $data =array();
        $query2 = array();
        $array1 = array();
        $array2 = array();
        if($page==""){
         $limit = " LIMIT 1, 10";   
        }
        else
        {
            $limit = 10;
            $start = 1;
            if ($page > 0) {
                if (!is_numeric($page)) {
                    $page = 1;
                }
            }
            $start = ($page - 1) * $limit;
            $limit = " LIMIT $start, $limit";   
        }
        if($page == 1)
        {
           $data = $this->db->query("SELECT jobs_review.*,jobs_user_profile_master.first_name,jobs_user_profile_master.last_name,jobs_user_profile_master.image FROM jobs_review LEFT JOIN jobs_user_profile_master ON jobs_review.user_id = jobs_user_profile_master.user_id WHERE jobs_review.user_id = $user_id $limit")->result_array();
           $array1 = $data;
          
        }
       
       
        $data = $this->db->query("SELECT jobs_review.*,jobs_user_profile_master.first_name,jobs_user_profile_master.last_name,jobs_user_profile_master.image FROM jobs_review LEFT JOIN jobs_user_profile_master ON jobs_review.user_id = jobs_user_profile_master.user_id WHERE jobs_review.user_id != $user_id $limit")->result_array();
        $array2 = $data;

        $final_array = array_merge($array1, $array2);

        return $final_array;
    }
    
    public function delete_review($user_id,$company_id)
    {
         
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $this->db->query("DELETE FROM `jobs_review` WHERE user_id='$user_id' AND company_id='$company_id'");
        return array(
            'status' => 200,
            'message' => 'deleted successfully'
        );
    }
    
    public function delete_profile_image($user_id)
    {
         
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $this->db->set('image','0');
            $this->db->where('user_id', $user_id); //which row want to upgrade  
            $this->db->update('jobs_user_profile_master');
        return array(
            'status' => 200,
            'message' => 'deleted successfully'
        );
    }
    
    public function university_list($user_id,$name){
        $resultpost = array();
         
        
        $limit = " LIMIT 0, 10";   
        $query = $this->db->query("SELECT * FROM `jobs_university` WHERE `name` LIKE '%%$name%%' $limit");
        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $name	 = $row['name'];
            
            
            $resultpost[] = array('id' => $id,
                'name' => $name,
                );
        }
        
        return $resultpost;
    
     }
     
    public function board_list($user_id,$name){
        $resultpost = array();
         
        
        $limit = " LIMIT 0, 10";   
        $query = $this->db->query("SELECT * FROM `jobs_boards` WHERE `name` LIKE '%%$name%%' $limit");
        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $name	 = $row['name'];
            
            
            $resultpost[] = array('id' => $id,
                'name' => $name,
                );
        }
        
        return $resultpost;
    
     }
     
     public function state_list($user_id,$name){
        $resultpost = array();
         
        
        $limit = " LIMIT 0, 10";   
        $query = $this->db->query("SELECT * FROM `states` WHERE `state_name` LIKE '%%$name%%' $limit");
        foreach ($query->result_array() as $row) {
            $state_id = $row['state_id'];
            $state_name	 = $row['state_name'];
            $country_id	 = $row['country_id'];
            
            
            $resultpost[] = array('state_id' => $state_id,
                'state_name' => $state_name,
                
                );
        }
        
        return $resultpost;
    
     }
     
    public function languages_add($user_id,$languages_known,$proficiency,$language_effi)
    {
        
        
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $this->db->set('languages_known',$languages_known);
        $this->db->set('proficiency',$proficiency);
        $this->db->set('language_effi',$language_effi);
        
        $this->db->where('user_id', $user_id); //which row want to upgrade  
        $this->db->update('jobs_user_profile_master');
        return array(
            'status' => 200,
            'message' => 'Added successfully'
        );
    
    }
 /* public function get_posts(){
  $qwe = $this->db->select('id, user_id ,first_name,last_name'); 
    $query = $this->db->get('job_profile_nikhil');
    return $query->result();
  }*/
    
}
?>
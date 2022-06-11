
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class JobsprofileModel extends CI_Model {

                ////user-profile-API
public function add_jobs_user_profile($user_id,$first_name,$last_name, $mobile, $email, $dob, $gender, $marital_status, $address_line1, $address_line2, $city,$state) {
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
            'address_line1' => $address_line1,
            'address_line2' => $address_line2,
            'city' => $city,
            'state' => $state
        );


        $success = $this->db->insert('jobs_profile', $job_data);
        //$id = $this->db->insert_id();
        if ($success) {
            $date_array = array(
                'user_id' => $user_id,
                'first_name' => $first_name,
                 'last_name' => $last_name,
                'mobile' => $mobile,
                'gender' => $gender
            );
            return array('status' => 201, 'message' => 'success', 'data' => $date_array);
        } else {
            return array(
                'status' => 208,
                'message' => 'failed'
            );
        }
    }



                //JOBS-EDUCATION-X
public function add_jobs_user_profile_education($user_id,$highest_qualification,$class_x_school, $x_board, $x_passing_year, $x_medium, $x_marks) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $job_data = array(
            'user_id' => $user_id,
            'highest_qualification' => $highest_qualification,
            'class_x_school' => $class_x_school,
            'x_board' => $x_board,
            'x_passing_year' => $x_passing_year,
            'x_medium' => $x_medium,
            'x_marks' => $x_marks,
            
        );


        $success = $this->db->insert('education_job_nikhil', $job_data);
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

        //JOBS-EDUCATION-XII
public function add_jobs_user_profile_education_college($user_id,$class_xii_college,$xii_board, $xii_passing_year, $xii_medium, $xii_marks) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $job_data = array(
            'user_id' => $user_id,
            'class_xii_college' => $class_xii_college,
            'xii_board' => $xii_board,
            'xii_passing_year' => $xii_passing_year,
            'xii_medium' => $xii_medium,
            'xii_marks' => $xii_marks,
            
        );


        $success = $this->db->insert('education_job_nikhil', $job_data);
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

//JOBS-EDUCATION-GRADUATE
public function add_jobs_user_profile_education_graduate($user_id,$graduate,$g_course, $g_specialisation, $g_university, $g_pass_year,$g_grade) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $job_data = array(
            'user_id' => $user_id,
            'graduate' => $graduate,
            'g_course' => $g_course,
            'g_specialisation' => $g_specialisation,
            'g_university' => $g_university,
            'g_pass_year' => $g_pass_year,
            'g_grade' => $g_grade,
            
        );


        $success = $this->db->insert('education_job_nikhil', $job_data);
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
    
    //JOBS-EDUCATION-POSTGRADUATE
   /* public function add_jobs_user_profile_education_graduate($user_id,$graduate,$g_course, $g_specialisation, $g_university, $g_pass_year,$g_grade) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $job_data = array(
            'user_id' => $user_id,
            'graduate' => $graduate,
            'g_course' => $g_course,
            'g_specialisation' => $g_specialisation,
            'g_university' => $g_university,
            'g_pass_year' => $g_pass_year,
            'g_grade' => $g_grade,
            
        );


        $success = $this->db->insert('education_job_nikhil', $job_data);
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
    }*/
    

        //JOBS-EDUCATION-POSTGRADUATE
public function add_jobs_user_profile_education_postgraduate($user_id,$postgraduate,$pg_course, $pg_specialisation, $pg_university, $pg_pass_year,$pg_grade) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $job_data = array(
            'user_id' => $user_id,
            'postgraduate' => $postgraduate,
            'pg_course' => $pg_course,
            'pg_specialisation' => $pg_specialisation,
            'pg_university' => $pg_university,
            'pg_pass_year' => $pg_pass_year,
            'pg_grade' => $pg_grade,
            
        );


        $success = $this->db->insert('education_job_nikhil', $job_data);
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
//JOBS-EDUCATION-DOCTORATE
public function add_jobs_user_profile_education_doctorate($user_id,$doctorate_phd,$d_course, $d_specialisation, $d_university, $d_pass_year,$d_grade) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $job_data = array(
            'user_id' => $user_id,
            'doctorate_phd' => $doctorate_phd,
            'd_course' => $d_course,
            'd_specialisation' => $d_specialisation,
            'd_university' => $d_university,
            'd_pass_year' => $d_pass_year,
            'd_grade' => $d_grade,
            
        );


        $success = $this->db->insert('education_job_nikhil', $job_data);
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
    public function add_jobs_user_profile_education_certificate($user_id,$certification,$ce_month, $ce_year, $ce_description, $certificate_issued_by,$achievements) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $job_data = array(
            'user_id' => $user_id,
            'certification' => $certification,
            'ce_month' => $ce_month,
            'ce_year' => $ce_year,
            'ce_description' => $ce_description,
            'certificate_issued_by' => $certificate_issued_by,
            'achievements' => $achievements,
            
        );


        $success = $this->db->insert('certification_job_nikhil', $job_data);
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
    
    //JOBS-previous-job-details
    public function add_jobs_user_profile_previous_job($user_id,$company_name,$company_type,$designation, $location, $work_experience, $work_start,$work_end) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $job_data = array(
            'user_id' => $user_id,
            'company_name' => $company_name,
            'company_type' => $company_type,
            'designation' => $designation,
            'location' => $location,
            'work_experience' => $work_experience,
            'work_start' => $work_start,
            'work_end' => $work_end,
            
        );


        $success = $this->db->insert('employment_job_nikhil', $job_data);
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

 //JOBS-preferred-job-details
    public function add_jobs_user_profile_preferred_job($user_id,$job_type,$job_location,$job_position, $min_salary) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $job_data = array(
            'user_id' => $user_id,
            'job_type' => $job_type,
            'job_location' => $job_location,
            'job_position' => $job_position,
            'min_salary' => $min_salary,
           
        );


        $success = $this->db->insert('preferred_job_nikhil', $job_data);
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


     
     
     
     
     public function user_profile_list($id) {
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
     
     
     
     
     
     
     
     
     
     
     
 /* public function get_posts(){
  $qwe = $this->db->select('id, user_id ,first_name,last_name'); 
    $query = $this->db->get('job_profile_nikhil');
    return $query->result();
  }*/
    
}
?>
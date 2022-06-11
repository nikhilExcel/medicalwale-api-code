<?php
  class Book_model extends CI_Model {
       
      public function __construct(){
          
        $this->load->database();
        
      }
	public function getallCategories(){
	
	
	
		$this->db->select('*');

        $this->db->from('job_category');

        $query = $this->db->get();

        if($query->num_rows() > 0){

          return $query->result_array();

        }else{

          return 0;

        }
		// $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
		// // $stmt = $this->db->query("SELECT * FROM job_cat");
		// // $result=($stmt::FETCH_ASSOC);
		// return $result;
		// $results = $this->db->fetch("SELECT * FROM job_cat");
		// $resultsArray = $results->fetch_assoc();
		// return $resultsArray;
	}
	public function getallfinds(){
	
	
	
		$this->db->select('category_id');

        $this->db->from('job_list');

        $query = $this->db->get();

        if($query->num_rows() > 0){

          return $query->result_array();

        }else{

          return 0;

        }
		// $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
		// // $stmt = $this->db->query("SELECT * FROM job_cat");
		// // $result=($stmt::FETCH_ASSOC);
		// return $result;
		// $results = $this->db->fetch("SELECT * FROM job_cat");
		// $resultsArray = $results->fetch_assoc();
		// return $resultsArray;
	}
	public function getalllanguages(){
	
	
	
		$this->db->select('job_category');

        $this->db->from('');

        $query = $this->db->get();

        if($query->num_rows() == 0){

          return $query->result_array();

        }else{

          return 0;

        }
		// $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
		// // $stmt = $this->db->query("SELECT * FROM job_cat");
		// // $result=($stmt::FETCH_ASSOC);
		// return $result;
		// $results = $this->db->fetch("SELECT * FROM job_cat");
		// $resultsArray = $results->fetch_assoc();
		// return $resultsArray;
	}
	public function getalljobtitle(){
	
	
	
		$this->db->select('*');

        $this->db->from('job_category');

        $query = $this->db->get();

        if($query->num_rows() > 0){

          return $query->result_array();

        }else{

          return 0;

        }
		// $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
		// // $stmt = $this->db->query("SELECT * FROM job_cat");
		// // $result=($stmt::FETCH_ASSOC);
		// return $result;
		// $results = $this->db->fetch("SELECT * FROM job_cat");
		// $resultsArray = $results->fetch_assoc();
		// return $resultsArray;
	}
	public function getalljobhire(){
	
		$this->db->select('*');

        $this->db->from('job_user_profile');
		
		$this->db->where('is_active',1);

        $query = $this->db->get();

        if($query->num_rows() > 0){

          return $query->result_array();

        }else{

          return 0;

        }
		// $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
		// // $stmt = $this->db->query("SELECT * FROM job_cat");
		// // $result=($stmt::FETCH_ASSOC);
		// return $result;
		// $results = $this->db->fetch("SELECT * FROM job_cat");
		// $resultsArray = $results->fetch_assoc();
		// return $resultsArray;
	
	}
	public function getJobCategorys($data){
	
		$stmt = "SELECT * FROM `job_list` jp inner join job_category jtc on jp.category_id=jtc.id INNER JOIN job_cat 
		on jp.job_type = job_cat.cat_id where category_id=1 and is_active=1";
	
		$query = $this->db->query($stmt);

		return $query->result_array();
		
		
		// $this->db->select('*');

        // $this->db->from('job_list');
		
		// // $this->db->join('job_cat', 'jp.job_type = job_cat.cat_id', 'inner');
		
		// $where = "category_id=1 and is_active=1";
		
		// $this->db->where($where);

        // $query = $this->db->get();

        // if($query->num_rows() > 0){

          // return $query->result_array();

        // }else{

          // return 0;

        // }
		// $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
		// // $stmt = $this->db->query("SELECT * FROM job_cat");
		// // $result=($stmt::FETCH_ASSOC);
		// return $result;
		// $results = $this->db->fetch("SELECT * FROM job_cat");
		// $resultsArray = $results->fetch_assoc();
		// return $resultsArray;

	}
	public function getBanerAPI(){
		$stmt = "SELECT * FROM job_img where img_active=1";
	
		$query = $this->db->query($stmt);

		return $query->result_array();
	}
	public function getJobApproveByTitle($job_post){
		$stmt = "SELECT * FROM job_list where category_id='".$job_post."' and is_active=1";
	
		$query = $this->db->query($stmt);

		return $query->result_array();
	
	
	}
	public function getJobByType($data){
		
		// parse_str($data['job_by_type'], $params);
		$stmt = "SELECT * FROM `job_list` jp inner join job_category jtc on jp.category_id=jtc.id INNER JOIN job_cat 
		on jp.job_type = job_cat.cat_id where job_type=".$data['job_type']." and is_active=1";
	
		$query = $this->db->query($stmt);

		return $query->result_array();
	
	
	}
	public function postUpload($data){
		$stmt = "INSERT INTO `job_user_profile`(`name`, `phone`, `email`, `dob`, `gender`, `job_role`, `min_salary`, `max_salary`, `year_exp`, `month_exp`, `city`, `resume`) VALUES ('".$user_name."','".$user_mobile."','".$user_email."',".$user_dob.",'".$user_gender."','".$user_job_title."',".$user_min_salary.",".$user_max_salary.",".$user_exp_year.",'".$user_exp_month."','".$user_city."','".$path."')";
	
		$query = $this->db->query($stmt);

	}
	}
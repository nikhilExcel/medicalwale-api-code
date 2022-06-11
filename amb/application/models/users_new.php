<?php

class Users_new extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        // $this->load->library('session');
        //$this->load->library('common');
    }

    function chk_dup() {
        $mobile = trim(stripcslashes($_POST['phone']));
        $this->db->select('count(*) as cnt');
        $this->db->from('users');
        $this->db->where("phone", $mobile);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
        }
    }

    function user_signup() {
        $_POST["password"] = md5($_POST["password"]);

        $this->db->insert('users', $_POST);
        $id = $this->db->insert_id();
        if (!empty($id)) {
            return $id;
        }
    }

    public function chkConfirmId($id) {
        $chkId = $this->db->get_where("users", array("random" => $id))->row();
        return count($chkId);
    }

    function finded_driver() {
        $query = $this->db->query("select * from win_select_driver where user_id = " . $_POST['user_id'] . " and status = 'REQUEST_ACCEPTED'");
        return $query->result();
    }

    function chk_dup_fb($post, $par = NULL) {
        $uname = $post->id;
        if ($par == "data") {
            $this->db->select('*');
        } else {
            $this->db->select('count(*) as cnt');
        }
        $this->db->from('users');
        $where = "fb_id = '$uname' or email = '$post->email'";
        $this->db->where($where);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
        } else {
            return FALSE;
        }
    }

    function verify_otps() {
        $user_id = trim(stripcslashes($_POST['uid']));
        $token = trim(stripcslashes($_POST['otp']));
        // $this->db->update('*');
        // $this->db->from('users');
        // $this->db->where(array("email" => $email, "password" => MD5($_POST['password']), "utype" => $_POST['utype']));
        $this->db->query('UPDATE `users` SET `otp_status` = 1 where `random`= "'.$token.'" AND `id`="'.$user_id.'"');
        $query = $this->db->get();


        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return 0;
        }
    }

    function verify() {
        $mobile = trim(stripcslashes($_POST['mobile']));
        $this->db->select('*');
        $this->db->from('mobile');
        $this->db->where(array("mobile" => $mobile, "password" => MD5($_POST['password']), "utype" => $_POST['utype']))->result();
       $this->db->get();


        // if ($query->num_rows() > 0) {
        //     return $query->row_array();
        // } else {
        //     return 0;
        // }
    }


    function Social_login_verfy(){
       $App_id = trim(stripcslashes($_POST['app_id']));
       $Socialtype = trim(stripcslashes($_POST['Socialtype']));
     
     $query = $this->db->query('Select * from `users` where `App_id`="'.$App_id.'" AND `Socialtype`="'.$Socialtype.'"')->result();
          // $query = $this->db->get();
      

    }

    public function select_amb()  
      {  
         //data is retrive from this query  
         $query = $this->db->query('Select * from `subtype` where `tid`=1')->result();  
         $this->db->get();

         return $query;  
      }


}


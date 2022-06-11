<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';



class User extends REST_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -  
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in 
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    function __construct() {
        // Call the Model constructor
        parent::__construct();
        error_reporting(E_ERROR | E_PARSE);
        $this->load->model("users");
        //$this->load->model("mc_common");
        $this->load->library("common");
        // $this->load->library('session');
    }

    public function get_driver() {
        $res = $this->users->finded_driver();
        if (!empty($res)) {
            $this->response(array("status" => "success", "data" => $res));
        } else {
            $this->response(array("status" => "fail"));
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        $this->load->view('main');
    }

    public function change_password_post() {
         
        if (!empty($_POST['old_password']) && !empty($_POST['new_password']) && !empty($_POST['user_id'])) {
            $res = $this->db->get_where("users", array("id" => $_POST['user_id'], "password" => md5($_POST['old_password'])))->row();
            if (!empty($res)) {
                $this->db->where(array("id" => $_POST['user_id'], "password" => md5($_POST['old_password'])));
                $this->db->update("users", array("password" => md5($_POST['new_password'])));
                $this->response(array("status" => "success"));
            } else {
                $this->response(array("status" => "fail", "data" => "old password are wrong"));
            }
        } else {
            $this->response(array("status" => "fail", "data" => "require data not send"));
        }
    }

    public function profile_post() {
       
        if (!empty($_POST['user_id'])) {
            
            $this->db->select('*');
            $this->db->from('users');
            $this->db->join('driver_registration', 'driver_registration.user_id = users.id','left');
            $this->db->where("users.id", $_POST['user_id']);
      		$res = $this->db->get()->row_array();
     
           // $res1 = $this->db->get_where("driver_registration", array("user_id" => $_GET['user_id']))->row();
             $avatar_id=$res['avatar_id'];
            
             $fares1 = $this->db->get_where("media", array("id" =>$avatar_id))->row_array();
      
       
            if (!empty($fares1['source'])) {
             $res['driver_pic'] = "https://d2c8oti4is0ms3.cloudfront.net/images/ambulance_images/". $fares1['source'];
            }else{
                 $res['driver_pic']="https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg";
            }
            if (!empty($res['license'])) {
                $res['license'] = "https://d2c8oti4is0ms3.cloudfront.net/images/ambulance_images/". $res['license'];
            }
            if (!empty($res['insurance'])) {
                $res['insurance'] = "https://d2c8oti4is0ms3.cloudfront.net/images/ambulance_images/". $res['insurance'];
            }
            if (!empty($res['permit'])) {
                $res['permit'] ="https://d2c8oti4is0ms3.cloudfront.net/images/ambulance_images/". $res['permit'];
            }
            if (!empty($res['registration'])) {
                $res['registration'] = "https://d2c8oti4is0ms3.cloudfront.net/images/ambulance_images/". $res['registration']; 
            }

            $res1=array("vehicle_no"=>"",
           // "insurance"=>"",
            "user"=>"");
            
                       $res2= array_merge($res,$res1);
            $this->response(array("status" => "success", "data" => $res2));
        } else {
            $this->response(array("status" => "fail", "data" => "User id not send"));
        }
    }

      public function update_post() {
        if (!empty($_POST['user_id'])) {
            
            
                if (isset($_FILES["avatar"]) AND ! empty($_FILES["avatar"]["name"])) {
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        $img_name = $_FILES['avatar']['name'];
                        $img_size = $_FILES['avatar']['size'];
                        $img_tmp = $_FILES['avatar']['tmp_name'];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if (in_array($ext, $img_format)) {
                                    $_POST['avatar'] = uniqid().date("YmdHis"). "." . $ext;
                                        $actual_image_path = 'images/ambulance_images/' . $_POST['avatar'];
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                }else{
                                     $this->response(array("status" => "fail", "data" => "Error during file upload: only select jpg,png or gif."));
                                     die;
                                }
                            }
                        }
                    }   
                   
                 if (isset($_FILES["license"]) AND ! empty($_FILES["license"]["name"])) {
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        $img_name = $_FILES['license']['name'];
                        $img_size = $_FILES['license']['size'];
                        $img_tmp = $_FILES['license']['tmp_name'];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if (in_array($ext, $img_format)) {
                                    $_POST['license'] = uniqid().date("YmdHis"). "." . $ext;
                                        $actual_image_path = 'images/ambulance_images/' . $_POST['license'];
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                    
                                             $update_array=array(
         
                                             "license"=>$_POST['license'],
                                    
                                             );
                                                $this->db->where("id", $_POST["user_id"]);
                                                $this->db->update("users", $update_array);
                                }else{
                                    
                                     $this->response(array("status" => "fail", "data" => "Error during file upload: only select jpg,png or gif."));
                                     die;
                                }
                            }
                        }
                    } 
                    
                      if (isset($_FILES["insurance"]) AND ! empty($_FILES["insurance"]["name"])) {
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        $img_name = $_FILES['insurance']['name'];
                        $img_size = $_FILES['insurance']['size'];
                        $img_tmp = $_FILES['insurance']['tmp_name'];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if (in_array($ext, $img_format)) {
                                    $_POST['insurance'] = uniqid().date("YmdHis"). "." . $ext;
                                        $actual_image_path = 'images/ambulance_images/' . $_POST['insurance'];
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                    $update_array=array(
         "insurance"=>$_POST['insurance'],
         );
            $this->db->where("id", $_POST["user_id"]);
            $this->db->update("users", $update_array);
                                }else{
                                    
                                     $this->response(array("status" => "fail", "data" => "Error during file upload: only select jpg,png or gif."));
                                     die;
                                }
                            }
                        }
                    }
                    
                      if (isset($_FILES["permit"]) AND ! empty($_FILES["permit"]["name"])) {
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        $img_name = $_FILES['permit']['name'];
                        $img_size = $_FILES['permit']['size'];
                        $img_tmp = $_FILES['permit']['tmp_name'];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if (in_array($ext, $img_format)) {
                                    $_POST['permit'] = uniqid().date("YmdHis"). "." . $ext;
                                        $actual_image_path = 'images/ambulance_images/' . $_POST['permit'];
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                    $update_array=array(
         "permit"=>$_POST['permit'],
         );
            $this->db->where("id", $_POST["user_id"]);
            $this->db->update("users", $update_array);
                                }else{
                                    
                                     $this->response(array("status" => "fail", "data" => "Error during file upload: only select jpg,png or gif."));
                                     die;
                                }
                            }
                        }
                    }
                    
                    if (isset($_FILES["registration"]) AND ! empty($_FILES["registration"]["name"])) {
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        $img_name = $_FILES['registration']['name'];
                        $img_size = $_FILES['registration']['size'];
                        $img_tmp = $_FILES['registration']['tmp_name'];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if (in_array($ext, $img_format)) {
                                    $_POST['registration'] = uniqid().date("YmdHis"). "." . $ext;
                                        $actual_image_path = 'images/ambulance_images/' . $_POST['registration'];
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                    $update_array=array(
         "registration"=>$_POST['registration'],
         );
            $this->db->where("id", $_POST["user_id"]);
            $this->db->update("users", $update_array);
                                }else{
                                    
                                     $this->response(array("status" => "fail", "data" => "Error during file upload: only select jpg,png or gif."));
                                     die;
                                }
                            }
                        }
                    }
           
            if (!empty($_POST['passowrd'])) {
                $_POST['password'] = md5($_POST['password']);
            }
		 if (!empty($_POST['name'])) {
		 		$new_name=$_POST['name'];
				$new_mobile=$_POST['mobile'];
				$avatar_id_=array('driver_name'=>$new_name,'mobile'=>$new_mobile);
                             	$this->db->where("user_id", $_POST['user_id']);
                        	$this->db->update("driver_registration", $avatar_id_);
		  }
		
            unset($_POST['X-API-KEY']);
              if (!empty($_FILES['avatar']['name'])) {
            $fares = $this->db->get_where("users", array("id" =>$_POST['user_id']))->row_array();
            
		      
		      
                      
                        if($fares['avatar_id']=="0" || $fares['avatar_id']==""){
                            
                            $avatar=array("title"=>$_POST['avatar'],
                        "type"=>"image",
                        "source"=>$_POST['avatar']);
                            $this->db->insert('media',$avatar);
                            $avt_id=$this->db->insert_id();
                         
                             $avatar_id=array("avatar_id"=>$avt_id,
					     );
                             $this->db->where("id", $_POST['user_id']);
                        $this->db->update("users", $avatar_id);
                        
                         
                        }else{
                           $avatar=array("title"=>$_POST['avatar'],
                        "type"=>"image",
                        "source"=>$_POST['avatar']);
                         $this->db->where("id", $fares['avatar_id']);
                        $this->db->update("media", $avatar);
                         $avt_id=$fares['avatar_id'];
                        
                        }
              }
  
            !empty($_POST['avatar']) ? $_POST['avatar'] = "https://d2c8oti4is0ms3.cloudfront.net/images/ambulance_images/". $_POST['avatar'] : '';
            !empty($_POST['license']) ? $_POST['license'] = "https://d2c8oti4is0ms3.cloudfront.net/images/ambulance_images/". $_POST['license'] : '';
            !empty($_POST['insurance']) ? $_POST['insurance'] = "https://d2c8oti4is0ms3.cloudfront.net/images/ambulance_images/". $_POST['insurance'] : '';
            !empty($_POST['permit']) ? $_POST['permit'] = "https://d2c8oti4is0ms3.cloudfront.net/images/ambulance_images/". $_POST['permit'] : '';
            !empty($_POST['registration']) ? $_POST['registration'] = "https://d2c8oti4is0ms3.cloudfront.net/images/ambulance_images/". $_POST['registration'] : '';
            $this->response(array("status" => "success", "data" => $_POST));
        } else {
            $this->response(array("status" => "fail"));
        }
    }
//   public function nearby_get() {

//         empty($_GET['limit']) ? $limit = 5000 : $limit = $_GET['limit'];
//         $query = $this->db->query("select user_id,name,email,latitude,longitude,vehicle_info,(((acos(sin((" . $_GET['lat'] . "*pi()/180)) *
//         sin((`latitude`*pi()/180))+cos((" . $_GET['lat'] . "*pi()/180)) *
//         cos((`latitude`*pi()/180)) * cos(((" . $_GET['long'] . "-
//         `longitude`)*pi()/180))))*180/pi())*60*1.1515*1.609344) 
//         as distance
// from users where utype=1 and is_online = 1 HAVING distance < $limit order by distance asc");

//         $res = $query->result();

//         $fares = $this->db->get_where("settings", array("name" => "FARE"));
//         $unit = $this->db->get_where("settings", array("name" => "UNIT"));

//         $this->response(array("status" => "success", "fair" => array("cost" => $fares->result()[0]->value, "unit" => $unit->result()[0]->value), "data" => $res));
//     }
    public function nearby_get() {

        empty($_GET['limit']) ? $limit = 5000 : $limit = $_GET['limit'];
        $query = $this->db->query("select id,name,email,lat,lng,utype,vehicle_info,(((acos(sin((" . $_GET['lat'] . "*pi()/180)) *
        sin((`lat`*pi()/180))+cos((" . $_GET['lat'] . "*pi()/180)) *
        cos((`lat`*pi()/180)) * cos(((" . $_GET['long'] . "-
        `lng`)*pi()/180))))*180/pi())*60*1.1515*1.609344) 
        as distance
        from users where utype!=0 and utype!=1 and is_online = 1 HAVING distance < $limit order by distance asc");

        $res = $query->result();
        $fares = $this->db->get_where("settings", array("name" => "FARE"));
        $unit = $this->db->get_where("settings", array("name" => "UNIT"));

        $Amb_fares = $this->db->get_where("Ambulance_settings", array("name" => "FARE"));
        $Amb_unit = $this->db->get_where("Ambulance_settings", array("name" => "UNIT"));

        $Doc_visit = $this->db->get_where("Doctor_settings", array("name" => "VISIT"));
        $Doc_unit = $this->db->get_where("Doctor_settings", array("name" => "UNIT"));

        $Nur_visit = $this->db->get_where("Nurse_settings", array("name" => "VISIT"));
        $Nur_unit = $this->db->get_where("Nurse_settings", array("name" => "UNIT"));

$this->response(array("status" => "success", "fair" => array("cost" => $fares->result()[0]->value, "unit" => $unit->result()[0]->value), "Ambulance" => array("Amb_cost" => $Amb_fares->result()[0]->value, "Amb_unit" =>  $Amb_unit->result()[0]->value),"Doctor" => array("Doc_visit" => $Doc_visit->result()[0]->value, "Doc_unit" =>  $Doc_unit->result()[0]->value), "Nurse" => array("Nur_visit" => $Nur_visit->result()[0]->value, "Nur_unit" =>  $Nur_unit->result()[0]->value), "data" => $res));
    } 
        public function Ambulance_nearby_get() {

        empty($_POST['limit']) ? $limit = 5000 : $limit = $_POST['limit'];
        $query = $this->db->query("select id,name,email,lat,lng,vehicle_info,(((acos(sin((" . $_POST['lat'] . "*pi()/180)) *
        sin((`lat`*pi()/180))+cos((" . $_POST['lat'] . "*pi()/180)) *
        cos((`lat`*pi()/180)) * cos(((" . $_POST['long'] . "-
        `lng`)*pi()/180))))*180/pi())*60*1.1515*1.609344) 
        as distance
from users where utype=1 and is_online = 1 HAVING distance < $limit order by distance asc");

        $res = $query->result();

        $fares = $this->db->get_where("settings", array("name" => "FARE"));
        $unit = $this->db->get_where("settings", array("name" => "UNIT"));

        $this->response(array("status" => "success", "fair" => array("cost" => $fares->result()[0]->value, "unit" => $unit->result()[0]->value), "data" => $res));
    }

     public function Doctor_nearby_get() {

        empty($_GET['limit']) ? $limit = 5000 : $limit = $_GET['limit'];
        $query = $this->db->query("select id,name,email,lat,lng,vehicle_info,(((acos(sin((" . $_GET['lat'] . "*pi()/180)) *
        sin((`lat`*pi()/180))+cos((" . $_GET['lat'] . "*pi()/180)) *
        cos((`lat`*pi()/180)) * cos(((" . $_GET['long'] . "-
        `lng`)*pi()/180))))*180/pi())*60*1.1515*1.609344) 
        as distance
from users where utype=3 and is_online = 1 HAVING distance < $limit order by distance asc");

        $res = $query->result();

        $fares = $this->db->get_where("settings", array("name" => "FARE"));
        $unit = $this->db->get_where("settings", array("name" => "UNIT"));

        $this->response(array("status" => "success", "fair" => array("cost" => $fares->result()[0]->value, "unit" => $unit->result()[0]->value), "data" => $res));
    }

        public function Nurse_nearby_get() {

        empty($_GET['limit']) ? $limit = 5000 : $limit = $_GET['limit'];
        $query = $this->db->query("select id,name,email,lat,lng,vehicle_info,(((acos(sin((" . $_GET['lat'] . "*pi()/180)) *
        sin((`lat`*pi()/180))+cos((" . $_GET['lat'] . "*pi()/180)) *
        cos((`lat`*pi()/180)) * cos(((" . $_GET['long'] . "-
        `lng`)*pi()/180))))*180/pi())*60*1.1515*1.609344) 
        as distance
from users where utype=4 and is_online = 1 HAVING distance < $limit order by distance asc");

        $res = $query->result();

        $fares = $this->db->get_where("settings", array("name" => "FARE"));
        $unit = $this->db->get_where("settings", array("name" => "UNIT"));

        $this->response(array("status" => "success", "fair" => array("cost" => $fares->result()[0]->value, "unit" => $unit->result()[0]->value), "data" => $res));
    }

    public function addRide_post() {

        $this->db->insert("rides", $_POST);
        $_POST['id'] = $this->db->insert_id();
        $cnt = $this->db->affected_rows();

        if ($cnt > 0) {

            $this->db->select("token");
            $this->db->from("users u");
            $this->db->join("rides r", "r.driver_id = u.id");
            $this->db->where("r.ride_id", $_POST['id']);
            $qry = $this->db->get();
            $res = $qry->row();

            $load = array();
            $load['title'] = 'Taxiapp';
            $load['msg'] = 'You have a new ride';
            $load['action'] = 'PENDING';
            $token[] = $res->gcm_token;

            $admin = $this->db->get("admin")->row();
            $this->common->android_push($token, $load, $admin->api_key);

            echo json_encode(array("status" => "success", "data" => $_POST));
        } else {
            echo json_encode(array("status" => "fail", "data" => "Error Try LAter."));
        }
    }



public function Driver_addRide_post() {

        $this->db->insert("rides", $_POST);
        $_POST['id'] = $this->db->insert_id();
        $cnt = $this->db->affected_rows();

        if ($cnt > 0) {

            $this->db->select("token");
            $this->db->from("users u");
            $this->db->join("rides r", "r.driver_id = u.id");
            $this->db->where("r.ride_id", $_POST['id']);
            $qry = $this->db->get();
            $res = $qry->row();

            $load = array();
            $load['title'] = 'Taxiapp';
            $load['msg'] = 'You have a new ride';
            $load['action'] = 'PENDING';
            $token[] = $res->gcm_token;

            $admin = $this->db->get("admin")->row();
            $this->common->android_push($token, $load, $admin->api_key);

            echo json_encode(array("status" => "success", "data" => $_POST));
        } else {
            echo json_encode(array("status" => "fail", "data" => "Error Try LAter."));
        }
    }
    public function rides_get() {

        $id = $_GET["id"];
        $status = $_GET["status"];
        empty($_GET['utype']) ? $utype = 0 : $utype = $_GET['utype'];
        $wh = $utype == 0 ? "r.user_id=$id" : "r.driver_id=$id";
        $res = $this->db->query("select r.*,w.mobile as user_mobile,w.avatar as user_avatar,w1.avatar as driver_avatar,w.name as user_name,w1.phone as driver_mobile,w1.id as driver_id,w1.name as driver_name from rides as r left join users as w on r.user_id=w.id left join users as w1 on r.driver_id=w1.id where $wh and r.status='$status' order by r.ride_id desc limit 20");


        $this->response(array("status" => "success", "data" => $res->result_array()));
    }

    public function rides_post() {
        $this->db->where("ride_id", $_POST['ride_id']);
        $this->db->update("rides", $_POST);
        $cnt = $this->db->affected_rows();
        if ($cnt > 0) {
            if (!empty($_POST['status'])) {
                if ($_POST['status'] == 'ACCEPTED') {
                    $this->db->select("token");
                    $this->db->from("users u");
                    $this->db->join("rides r", "r.user_id = u.id");
                    $this->db->where("r.ride_id", $_POST['ride_id']);
                    $qry = $this->db->get();
                    $res = $qry->row();

                    $load = array();
                    $load['title'] = 'Taxiapp';
                    $load['msg'] = 'Your request accepted';
                    $load['action'] = 'ACCEPTED';
                    $token[] = $res->gcm_token;

                    $admin = $this->db->get("admin")->row();
                    $this->common->android_push($token, $load, $admin->api_key);
                }
                if ($_POST['status'] == 'CANCELLED') {

                    $qry = $this->db->query("SELECT `token` FROM (`users` u) JOIN `rides` r ON `r`.`user_id` = `u`.`id` OR `r`.`driver_id` = `u`.`id` WHERE `r`.`ride_id` =  " . $_POST['ride_id'] . "");
                    $res = $qry->result();

                    // $load = array();
                    // $load['title'] = 'Ride Cancelled';
                    // $load['body'] = 'Your ride has been cancelled';
                    // $load['icon'] = 'myicon';
                    // $load['sound'] = 'mySound';
                       $load = array
                      (  
                            'title' => 'Ride Cancelled',
                            'body'  => 'Your Ride Has been Cancelled',
                            'icon'  => 'myicon',
                            'sound' => 'mySound'
                      );
                    foreach ($res as $val) {
                        $token[] = $val->gcm_token;
                    }
                    $admin = $this->db->get("admin")->row();
                    $this->common->android_push($token, $load, $admin->api_key);
                   
                    echo json_encode(array("status" => "success", "data" => "Your ride has been cancelled"));
                } elseif ($_POST['status'] == 'COMPLETED') {
                    $qry = $this->db->query("SELECT `token` FROM (`users` u) JOIN `rides` r ON `r`.`user_id` = `u`.`id` OR `r`.`driver_id` = `u`.`id`
                    WHERE `r`.`ride_id` =  " . $_POST['ride_id'] . "");
                    $res = $qry->result();

                    $load = array();
                    $load['title'] = 'Taxiapp';
                    $load['msg'] = 'Your ride has been completed';
                    $load['action'] = 'COMPLETED';
                    foreach ($res as $val) {
                        $token[] = $val->gcm_token;
                    }
                    $admin = $this->db->get("admin")->row();
                    $this->common->android_push($token, $load, $admin->api_key);
                    echo json_encode(array("status" => "success", "data" => "Your ride has been completed"));
                } else {
                    echo json_encode(array("status" => "success", "data" => "Success"));
                }
            }else{
				if(!empty($_POST['payment_mode'])){
				    
				    
					$this->db->select("token");
                    $this->db->from("users u");
                    $this->db->join("rides r", "r.driver_id = u.id");
                    $this->db->where("r.ride_id", $_POST['ride_id']);
                    $qry = $this->db->get();
                    $res = $qry->row();

                    $load = array();
                    
                    if ($_POST['payment_mode'] != 'PAYPAL') {
                        $load['msg'] = 'User requested offline payment';
                    } else {
                        $load['msg'] = 'User just paid for your ride';
                    }
                    
                    $load['title'] = 'Taxiapp';
                    
                    $load['action'] = 'ACCEPTED';
                    $token[] = $res->gcm_token;

                    $admin = $this->db->get("admin")->row();
                    $this->common->android_push($token, $load, $admin->api_key);
				}
				echo json_encode(array("status" => "success", "data" => "Success"));
			}
        } else {
            echo json_encode(array("status" => "fail", "data" => "Error Try LAter."));
        }
    }


     public function Types_get() {
        $res = $this->db->query("SELECT * FROM `types`")->result();
        if($res){
            $this->response(array("status"=>"success", "data"=>$res));
        }else{
             $this->response(array("status"=>"success", "data"=>array()));
        }
    }
     
  
 
}


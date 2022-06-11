 <?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends CI_Controller {
    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *      http://example.com/index.php/welcome
     *  - or -  
     *      http://example.com/index.php/welcome/index
     *  - or -
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
        
        $this->load->library("common");
        $this->load->library('session');
        
    }

    public function register() {
        
        if (!empty($_POST['email'])) {
            $mobile = $_POST['mobile'];
            $dup = $this->users->chk_dup();
            if (empty($dup['cnt'])) {
                $randomString = $this->common->Generate_hash(16);
                $mail_data = $this->db->get("settings")->result();
                $_POST['random'] = $randomString;
                $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://api.msg91.com/api/sendhttp.php?sender=MSGIND&route=4&mobiles=$mobile&authkey=218426AQyElCXNE5b124151&country=0&message=Currently your Account is under Approval process.",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);
                if ($response) {
                    $_POST['random'] = $randomString;
                    $res = $this->users->user_signup();
                    if (!empty($res)) {
                        $this->load->helper('string');
                        $rand = random_string('alnum', 8) . random_string('numeric', 8) . random_string('alnum', 8) . random_string('numeric', 8);
                        $datakey = array(
                            "user_id" => $res,
                            "key" => $rand
                        );
                        $this->db->insert("keys", $datakey);
                        $cnt = $this->db->affected_rows();

                        $res = $this->db->get_where("users", array('id' => $res))->row_array();
                        if ($cnt > 0) {
                            $res['key'] = $rand;
                        }
                        echo json_encode(array("status" => "success", "data" => $res));
                    } else {
                        echo json_encode(array("status" => "fail", "data" => "Not Inserted"));
                    }
                    header("Content-Type:application/json");
                } else {
                    echo json_encode(array("status" => "fail", "data" => "Email Not send, Please check your SMTP settings"));
                }
            } else {
                echo json_encode(array("status" => "fail", "data" => "User already registered"));
            }
        }
    }

    public function login() {

        $res = $this->users->chkLogin();
        if (!empty($res)) {
            if ($res['status'] == 0) {
                echo json_encode(array("status" => "fail", "data" => "Please first verify your account"));
                die;
            }
            if (!empty($_POST['gcm_token'])) {
                $this->db->where("email", $_POST['email']);
                $this->db->update("users", array("token" => $_POST['gcm_token']));
            }
            unset($res['password']);
            $this->load->helper('string');
            $rand = random_string('alnum', 8) . random_string('numeric', 8) . random_string('alnum', 8) . random_string('numeric', 8);
            
            
            $str = $this->db->query("select user_id from `keys` where user_id = '" . $res['id'] . "'");
            $row = $str->row_array();
            if (!empty($row)) {
                $data = array(
                    "user_id" => $row['user_id'],
                    "key" => $rand
                );
              
                $this->db->where("user_id", $row['user_id']);
                $this->db->update("keys", $data);
            } else {
                $data = array(
                    "user_id" => $res['user_id'],
                    "key" => $rand
                );
                $this->db->insert("keys", $data);
            }
            $cnt = $this->db->affected_rows();
            /*  print_r($cnt);
            die;*/
            if ($cnt > 0) {
                $res['key'] = $rand;
            }
            
                $avatar_id=$res['avatar_id'];
            
             $fares1 = $this->db->get_where("media", array("id" =>$avatar_id))->row_array();
      
       
            if (!empty($fares1['source'])) {
             $res['driver_pic'] = $this->config->base_url() . $fares1['source'];
            }else{
                 $res['driver_pic']="https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg";
            }
             
            
        /*    if (!empty($res['avatar'])) {
                $res['avatar'] = $this->config->base_url() . $res['avatar'];
            }*/
            if (!empty($res['license'])) {
                $res['license'] = $this->config->base_url() . $res['license'];
            }else{
                $res['license'] ="";
                
            }
            if (!empty($res['insurance'])) {
                $res['insurance'] = $this->config->base_url() . $res['insurance'];
            }else{
                $res['insurance'] ="";
                
            }
            if (!empty($res['permit'])) {
                $res['permit'] = $this->config->base_url() . $res['permit'];
            }else{
                $res['permit'] ="";
                
            }
            if (!empty($res['registration'])) {
                $res['registration'] = $this->config->base_url() . $res['registration'];
            }else{
                $res['registration'] ="";
                
            }
            echo json_encode(array("status" => "success", "data" => $res));
        } else {
            echo json_encode(array("status" => "fail", "data" => "Please enter valid email OR password"));
        }
    }

    public function reset_password($conf_id = null) {
        if (!empty($_POST)) {
            unset($_POST['confirm_password']);
            $_POST['random'] = '';
            $_POST['password'] = md5($_POST['password']);
            $this->db->where('id', $_POST['id']);
            $this->db->update("users", $_POST);

            echo "<h3><b style = 'color:green'>Password successfully changed, now you can login..</b></h3>";
        } else {
            $data = '';
            if (!empty($conf_id)) {
                $data['res'] = $this->db->get_where("users", array("random" => $conf_id))->row();
                if (!empty($data['res'])) {
                    $this->load->view('layout/header');
                    $this->load->view("admin/reset_password", $data);
                    //$this->load->view('layout/footer');
                } else {
                    echo "<h3><b style = 'color:red'>Error occur: Contact administration</b></h3>";
                }
            }
        }
    }

    public function confirm($conf_id = null) {
        $data = '';
        if (!empty($conf_id)) {
            $resid = $this->users->chkConfirmId($conf_id, "users");
            if (!empty($resid)) {
                $this->db->where("random", $conf_id);
                $this->db->update("users", array("status" => 1));
                echo "<h3><b style = 'color:green'>Your Registration Request is success, Now you can login with your username and password</b></h3>";
            } else {
                echo "<h3><b style = 'color:red'>User activation failed. contact to administartion</b></h3>";
            }
        }
    }

    public function forgot_password() {
        if (!empty($_POST['email'])) {
            $res = $this->db->get_where("users", array("email" => $_POST['email']))->row();
            if (!empty($res->email)) {
                $randomString = $this->common->Generate_hash(16);
                // you can set your configuration from constant file in application/config folder

                $mail_data = $this->db->get("settings")->result();
                if (!empty($mail_data[2]->value) && !empty($mail_data[3]->value) && !empty($mail_data[4]->value) && !empty($mail_data[5]->value)) {
                    $config = Array(
                        'protocol' => 'smtp',
                        'smtp_host' => $mail_data[2]->value,
                        'smtp_port' => $mail_data[3]->value,
                        'smtp_user' => $mail_data[4]->value, // change it to yours
                        'smtp_pass' => $mail_data[5]->value, // change it to yours
                        'mailtype' => 'html',
                        'charset' => 'iso-8859-1',
                        'wordwrap' => TRUE
                    );

                    $message = 'Hello <b>' . $_POST['email'] . '</b>,';
                    $message .= '<br/>';
                    $message .= 'If you want to reset your password, Click below link <br/>';
                    $message .= '';
//                $message .= "http://127.0.0.1/restaurant/" . "user/confirm/" . $randomString . "";
                    $message .= $this->config->base_url() . "user/reset_password/" . $randomString . "";

                    $this->load->library('email', $config);
                    $this->email->set_newline("\r\n");
                    $this->email->from(empty($mail_data[6]->value) ? 'taxiapp@test.com' : $mail_data[6]->value); // change it to yours
                    $this->email->to($_POST['email']); // change it to yours
                    $this->email->subject('Reset Password');
                    $this->email->message($message);
                    $send = $this->email->send();
                } else {
                    $to = $_POST['email'];
                    $subject = "Confirmation Registarion";
                    $message = 'Hello <b>' . $_POST['email'] . '</b>,';
                    $message .= '<br/>';
                    $message .= 'If you want to reset your password, Click below link <br/>';
                    $message .= '';
                    $message .= $this->config->base_url() . "user/reset_password/" . $randomString . "";
                    $headers = "From: taxiapp@test.com";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    $send = mail($to, $subject, $message, $headers);
                }
                if ($send) {
                    $a['random'] = $randomString;
                    $this->db->where("email", $_POST['email']);
                    $this->db->update("users", $a);
                    echo json_encode(array("status" => "success", "data" => "Email send successfully"));
                } else {
                    echo json_encode(array("status" => "fail", "data" => "Email Not send, Try again"));
                }
            } else {
                echo json_encode(array("status" => "fail", "data" => "Email not registered"));
            }
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        $this->load->view('main');
    }

    public function getTypes() {


         $res = $this->db->query("SELECT * FROM `types`")->result();
        if($res){
            echo json_encode(array("status"=>"success", "data"=>$res));
        }else{
             echo json_encode(array("status"=>"unsuccess", "data"=>array()));
        }
    }

    public function bank_detail_post() {
        if(isset($_POST['id']) && !empty($_POST['id'])){
            $id=$_POST['id'];
            $account_number=$_POST['account_number'];
            $bank_ifsc=$_POST['bank_ifsc'];
            $bank_branch=$_POST['bank_branch']; 
            $bank_name=$_POST['bank_name'];
            
            
            $bank=array("user_id"=>$id,
                "account_number"=>$account_number,
                "bank_ifsc"=>$bank_ifsc,
                "bank_branch"=>$bank_branch,
                "bank_name"=>$bank_name,
                );
               $res =  $this->db->insert('bank_details',$bank);
                
           // $res = $this->db->query("SELECT * FROM `subtype` WHERE `tid`=".$_POST['id'])->result();
            if($res){
                echo json_encode(array("status"=>"success", "data"=>$res));
            }else{
                echo json_encode(array("status"=>"unsuccess", "data"=>array()));
            }
        }else{
            echo '{
                "status":"unsuccess",
                "msg":"Please Send All Parameter"
            }';
        }

         
    }
    
    public function bank_detail_get() {
        if(isset($_POST['id']) && !empty($_POST['id'])){
           
            $res = $this->db->query("SELECT * FROM `bank_details` WHERE `user_id`=".$_POST['id'])->row_array();
            if($res){
                echo json_encode(array("status"=>"success", "data"=>$res));
            }else{
                echo json_encode(array("status"=>"unsuccess", "data"=>array()));
            }
        }else{
            echo '{
                "status":"unsuccess",
                "msg":"Please Send All Parameter"
            }';
        }

         
    }
    
    public function bank_detail_edit() {
        if(isset($_POST['id']) && !empty($_POST['id'])){
           $id=$_POST['id'];
            $account_number=$_POST['account_number'];
            $bank_ifsc=$_POST['bank_ifsc'];
            $bank_branch=$_POST['bank_branch']; 
            $bank_name=$_POST['bank_name'];
           
             $res1 = $this->db->query("SELECT user_id FROM `bank_details` WHERE `user_id`=".$_POST['id'])->row_array();
           if(!empty($res1)){
           $bank=array(
                "account_number"=>$account_number,
                "bank_ifsc"=>$bank_ifsc,
                "bank_branch"=>$bank_branch,
                "bank_name"=>$bank_name,
                );
                
           $this->db->where("user_id", $_POST['id']);
            $res= $this->db->update("bank_details",$bank );
           }else{
               
             $bank=array("user_id"=>$id,
                "account_number"=>$account_number,
                "bank_ifsc"=>$bank_ifsc,
                "bank_branch"=>$bank_branch,
                "bank_name"=>$bank_name,
                );
               $res =  $this->db->insert('bank_details',$bank);
           }
                
           // $res = $this->db->query("SELECT * FROM `bank_details` WHERE `user_id`=".$_POST['id'])->result();
            if($res){
                echo json_encode(array("status"=>"success", "data"=>$res));
            }else{
                echo json_encode(array("status"=>"unsuccess", "data"=>array()));
            }
        }else{
            echo '{
                "status":"unsuccess",
                "msg":"Please Send All Parameter"
            }';
        }

         
    }
    
    public function getSubTypes() {
        if(isset($_POST['id']) && !empty($_POST['id'])){
            
            
            $res = $this->db->query("SELECT * FROM `subtype` WHERE `tid`=".$_POST['id'])->result();
            if($res){
                echo json_encode(array("status"=>"success", "data"=>$res));
            }else{
                echo json_encode(array("status"=>"unsuccess", "data"=>array()));
            }
        }else{
            echo '{
                "status":"unsuccess",
                "msg":"Please Send All Parameter"
            }';
        }

         
    }
    
     public function getamb_types() {
   
            
            $res = $this->db->query("SELECT * FROM `amb_types`")->result();
            if($res){
                echo json_encode(array("status"=>"success", "data"=>$res));
            }else{
                echo json_encode(array("status"=>"unsuccess", "data"=>array()));
            }
        

         
    }
           public function faredata_post() {
    
           
   
            function GetDrivingDistance($lat1, $lat2, $long1, $long2)
                {
                            
                        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=Driving&language=pl-PL&key=AIzaSyCMHN__LczEziaYuKXQ-SKdiMfx5AiY66o&callback=initMap";
                        $ch  = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                        $response = curl_exec($ch);
                        curl_close($ch);
                        $response_a = json_decode($response, true);
                        return $response_a;
                }
            
           
         if(!empty($_POST['agency_id'])){
            $agency_no = $_POST['agency_id'];
         }else{
		  $agency_no = '49055';
		 }
               if(!empty($_POST['driver_user_id'])){
                $user_id = $_POST['driver_user_id'];
               }else{
			  $user_id = '49055';
			 }  
                
                 $lat1=$_POST['lat1'];
            $lat2=$_POST['lat2'];
            $long1=$_POST['long1']; 
            $long2=$_POST['long2'];
            
           
            $amb = $this->db->query("SELECT * FROM ambulance_fare WHERE agency_id= '$agency_no' and driver_user_id='$user_id'" )->row_array();
            if(!empty($amb))
            {
                $res= str_replace(',', '.', GetDrivingDistance($lat1,$lat2,$long1,$long2));
                if(!empty($res))
                {
                    $val=$res['rows'][0]['elements'][0]['status'];
                     if($val!="ZERO_RESULTS")
                       {
                          $dist       = $res['rows'][0]['elements'][0]['distance']['text'];
                          $duration       = $res['rows'][0]['elements'][0]['duration']['text'];
                         
                               $dis=  str_replace('km', '',$dist);
                       
                           if(round($dis) >=$amb['km'] ){
                               $b = $amb['km'] - $dis;
                            $totalkm=abs($b);
                            $fare_per_km=$amb['per_km_Rs']*$totalkm;
                             $total_fare=$fare_per_km+$amb['fix_fare'];
                            
                           }else{
                             $total_fare=$amb['fix_fare'];
                               
                           }
                         
                         echo json_encode( array(
                                    "status"=>"success",
                                    "estimated_total_time" => $duration,
                                    "date"=> date('Y-m-d'),
                                    "total_amount" => $total_fare
                                 ));
                         
                           
                       }
                       else
                       {
                           
                           echo json_encode(array("status"=>"unsuccess","msg"=>"Cannot Calculate Distance"));
                       }
                }
                 else
                {
                   echo json_encode(array("status"=>"unsuccess","msg"=>"Cannot Calculate Distance"));
                }
               
            }
            else
            {
                echo json_encode(array("status"=>"unsuccess","msg"=>"Record not found"));
            }
 
        }
    
    
    
    
    

//       public function nearby_rides(){
//            $this->db->insert("rides", $_POST);
//            $last_id = $this->db->insert_id();

//         empty($_POST['limit']) ? $limit = 3000 : $limit = $_POST['limit'];
//         $query = $this->db->query("select user_id,gcm_token,name,email,latitude,longitude,utype,vehicle_info,(((acos(sin((" . $_POST['lat'] . "*pi()/180)) *
//         sin((`latitude`*pi()/180))+cos((" . $_POST['lat'] . "*pi()/180)) *
//         cos((`latitude`*pi()/180)) * cos(((" . $_POST['long'] . "-
//         `longitude`)*pi()/180))))*180/pi())*60*1.1515*1.609344) 
//         as distance
//         from users where utype!=0 and utype!=1 and subtype='".$_POST['subtype_id']."' and utype='".$_POST['type_id']."' and is_online = 1 HAVING distance < $limit order by distance asc")->row();
//    $token[] = $query->gcm_token;
//    //   print_r($token);
//    // exit();
   
//   define( 'API_ACCESS_KEY', 'AIzaSyBdtoqZnDtDfLWElaGmRi9GrTy0t364SUs');

//      $msg = array
//           (
//                 'body'  => 'You have new ride',
//                 'title' => 'Ride Request',
//                 'icon'  => 'myicon',
//                 'sound' => 'mySound'
//           );

//     $fields = array
//             (
//             'registration_ids' => $token,
//                 'notification'  => $msg
//             );
    
    
//     $headers = array
//             (
//                 'Authorization: key=' . API_ACCESS_KEY,
//                 'Content-Type: application/json'
//             );
//           // print_r($fields);
//           // exit();
// #Send Reponse To FireBase Server    
//         $ch = curl_init();
//         curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
//         curl_setopt( $ch,CURLOPT_POST, true );
//         curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
//         curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
//         curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
//         curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
//         $result = curl_exec($ch );
        
//     $response=json_decode($result);
//    // print_r($result);
//    //  exit();
//    //  echo $result;exit;
//     curl_close( $ch );
//     if(count($response)>0){



//            echo json_encode(array("status" => "success", "Ride_id" => $last_id));
//     }




//  else{

//    echo json_encode(array("status" => "unsuccess", "msg" => "Something went wrong"));
//  }

      

//      }



           public function nearby_rides(){
          date_default_timezone_set('Asia/Kolkata');

        empty($_POST['limit']) ? $limit = 3000 : $limit = $_POST['limit'];
        
       /* echo "select id,token,name,email,lat,lng,utype,vehicle_info,(((acos(sin((" . $_POST['lat'] . "*pi()/180)) *
        sin((`lat`*pi()/180))+cos((" . $_POST['lat'] . "*pi()/180)) *
        cos((`lat`*pi()/180)) * cos(((" . $_POST['long'] . "-
        `lng`)*pi()/180))))*180/pi())*60*1.1515*1.609344) 
        as distance
        from users where utype!=0 and utype!=1 and subtype='".$_POST['subtype_id']."' and utype='".$_POST['type_id']."' and ac_type='".$_POST['ac_type']."' and is_online = 1 HAVING distance < $limit order by distance asc";
      */  
        
        
        $query = $this->db->query("select id,token,name,email,lat,lng,utype,vehicle_info,(((acos(sin((" . $_POST['lat'] . "*pi()/180)) *
        sin((`lat`*pi()/180))+cos((" . $_POST['lat'] . "*pi()/180)) *
        cos((`lat`*pi()/180)) * cos(((" . $_POST['long'] . "-
        `lng`)*pi()/180))))*180/pi())*60*1.1515*1.609344) 
        as distance
        from users where utype!=0 and utype!=1 and subtype='".$_POST['subtype_id']."' and utype='".$_POST['type_id']."' and ac_type='".$_POST['ac_type']."' and is_online = 1 HAVING distance < $limit order by distance asc")->result();
        foreach ($query as $tokeng ) {
                $token[] = $tokeng->token;
                 
           
        }
  
           $sele = $this->db->query("SELECT * from `users` where `id`=".$_POST['user_id'])->row();
        
           $usern = $sele->name;
           if($usern == ''){
            
             $username = 'Someone';
           }
           else{
        $username = $usern;
           }
             $user_mobile=$_POST['user_mobile'];
             $ac=$_POST['ac_type'];
            // $current_lat=$_POST['current_lat'];

             //$current_long=$_POST['current_long'];
             $drop_lat=$_POST['lat'];
             $drop_long=$_POST['long'];
             $pickup_adress=$_POST['pickup_adress'];
             $drop_address=$_POST['drop_address'];

      
  
   $tp = $_POST['type_id'];
   if($tp == 2){
     $msg = array
          (  
                'title' => 'You have a new Ride',
                'body'  => $username. ' is Looking out for Ambulance at  ' .$pickup_adress. ' to '.$drop_address,
                'icon'  => 'myicon',
                'sound' => 'mySound'
          );
   }
    
   // echo $username;
   // echo $address;
   // echo $destination_address;
   // exit;
   
 

    
 define( 'API_ACCESS_KEY', 'AAAA-vknDIs:APA91bG0l7544oS_CisWxq23RL2vfboHZfDGtF9EHT9zxeH16MLf_JdrvsrDvMAPjRb8XJSw8z7k8lG77F7Z7xyOSKE6Ff7QLcSAvFB4PK0JVZIJHzjhKK2nen4Z4hEDHOowdVYZnvi6');
    $fields = array
            (
            'registration_ids' => $token,
                'notification'  => $msg
            );
    
    
    $headers = array
            (
                'Authorization: key=' . API_ACCESS_KEY,
                'Content-Type: application/json'
            );
        //  echo json_encode( $fields );
     //    die;
#Send Reponse To FireBase Server    
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        
    $response=json_decode($result);
   // print_r($result);
   //  exit();
    // echo $result;exit;
    curl_close( $ch );
    if(count($response)>0){

           $this->db->insert("rides", $_POST);
           $last_id = $this->db->insert_id();

           echo json_encode(array("status" => "success", "Ride_id" => $last_id));
    }




 else{

   echo json_encode(array("status" => "unsuccess", "msg" => "Something went wrong"));
 }

      

     }
         public function nearby_get() {
//echo $_POST['lat'];
///echo $_POST['long'];
//die();
        empty($_POST['limit']) ? $limit = 5000 : $limit = $_POST['limit'];
        $query = $this->db->query("select id,name,email,lat as latitude,lng as longitude,utype,vehicle_info,is_online,(((acos(sin((" . $_POST['lat'] . "*pi()/180)) *
        sin((`lat`*pi()/180))+cos((" . $_POST['lat'] . "*pi()/180)) *
        cos((`lat`*pi()/180)) * cos(((" . $_POST['long'] . "-
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

        echo json_encode(array("status" => "success", "data" => $res));
    } 


    public function Ride_detaile(){
	    
	$rides_engegd = $this->db->query("SELECT * FROM `rides` where driver_id='".$_POST['driver_id']."' and status='ACCEPTED' ORDER BY `ride_id` DESC  LIMIT 1")->row();
     	$countRiede=count($rides_engegd);
		if($countRiede == 0 || $countRiede == '' ){ 
	    
       /*commented by nikhil
       $res = $this->db->query("SELECT * from `rides` where `status`='PENDING' and `type_id`= '".$_POST['type_id']."' and `subtype_id`='".$_POST['subtype_id']."' and NOW() <= DATE_ADD(Create_time, INTERVAL 30 MINUTE) ORDER BY `ride_id` DESC ")->result();*/
        // $sql = $this->db->last_query();
        // echo $sql;
        // exit;
	$type_id =    $_POST['type_id'];
        $subtype_id = $_POST['subtype_id'];
        $res = $this->db->query("SELECT * from `rides` where `status`='PENDING' and `type_id`= $type_id and `subtype_id`=$subtype_id and NOW() <= DATE_ADD(Create_time, INTERVAL 30 MINUTE) ORDER BY `ride_id` DESC ")->result();
		

	       if($res == True){
		echo json_encode(array("status" => "success", "Ride_id" => $res));
	       } else{
		 echo json_encode(array("status" => "unsuccess", "msg" => "No Data found"));
	       }
	}
       else{
         echo json_encode(array("status" => "unsuccess", "msg" => "No Data found"));
       }
 
    }
public function passenger(){
     $res = $this->db->query("SELECT * FROM `users` where `id` = ".$_POST['user_id'])->row();
     if($res == True){
         echo json_encode(array("status" => "success", "user_Detail" => $res));
     }
     else{
        echo json_encode(array("status" => "unsuccess", "msg" => "No Data found"));    
     }
}

public function passenger_details(){
     $res = $this->db->query("SELECT * FROM `rides` where `ride_id` = ".$_POST['ride_id'])->row_array();
 //  print_r($res);
     if($res == True){
         
         $pay_mode=$res['pay_mode'];
        
         if($pay_mode!=""){
          $get_mode = $this->db->query('Select * from payment_method where `id`='.$pay_mode.'')->row_array();
           $pay_mode_name=$get_mode['payment_method']; 
          
                     $array_ride_payment=array(
                     "pay_mode"=>$pay_mode_name
                     );
                             
         }else{
              $array_ride_payment=array(
                                 "pay_mode"=>""
                                 );
            }
           // print_r($array_ride_payment);
     
    $rest=array_merge($res,$array_ride_payment);
         
         echo json_encode(array("status" => "success", "user_Detail" => $rest));
     }
     else{
        echo json_encode(array("status" => "unsuccess", "msg" => "No Data found"));    
     }
}
     public function Ride_info(){
       $res = $this->db->query("SELECT * from `rides` where `ride_id`='".$_POST['ride_id']."' and `status`='ACCEPTED' and NOW() <= DATE_ADD(Create_time, INTERVAL 30 MINUTE) ORDER BY `ride_id` DESC LIMIT 1 ")->row();
       // $sql = $this->db->last_query();
       //   echo $sql;
       // //   exit();
       if($res == True){
        echo json_encode(array("status" => "success", "data" => $res));
       }
       else{
         echo json_encode(array("status" => "unsuccess", "msg" => "No Ambulance available"));
       }
 
    }
    
     public function rides_post() {
        $com = $this->db->query("SELECT * FROM `tbl_commision` ORDER BY `commission_id` DESC LIMIT 1")->row();
        if($_POST['utype'] == 2){
          $pr = $_POST['price'] * ($com->For_Ambulance/100);
          $Ac_pr = $_POST['price'] - $pr;
          }
          else if($_POST['utype'] == 3){
            $pr = $_POST['price'] * ($com->For_Doctor/100);
            $Ac_pr = $_POST['price'] - $pr;
          }
               else if($_POST['utype'] == 4){
            $pr = $_POST['price'] * ($com->For_Nurse/100);
            $Ac_pr = $_POST['price'] - $pr;
          }
          else{
            $pr = $_POST['price'] * ($com->For_Ambulance/100);
            $Ac_pr = $_POST['price'] - $pr;
          }

          //   if($cnt){
     //       echo json_encode(array("status" => "success", "data" => "Success"));
     //   }
     //   else{
     //        echo json_encode(array("status" => "fail", "data" => "Error Try LAter."));
     //   }
			if (!empty($_POST['ride_id'])) {
            if (!empty($_POST['status'])) {  
			
		    
				if ($_POST['status'] == 'ACCEPTED') {
						//$res = $this->db->get_where("rides", array("ride_id" => $_POST['ride_id']))->row();
						$this->db->select("token");
						$this->db->from("users u");
						$this->db->join("rides r", "r.user_id = u.id");
						$this->db->where("r.ride_id", $_POST['ride_id']);
						$qry = $this->db->get();
					   $res = $qry->row();
	  $cnt = $this->db->query("UPDATE `rides` set `status`='".$_POST['status']."',`driver_id`='".$_POST['driver_id']."',`driver_mobile`='".$_POST['driver_number']."',`driver_name`='".$_POST['driver_name']."',`agency_id`='".$_POST['agency_id']."',`price`='".$_POST['price']."',`Ad_commission`=".$pr.",`Driver_ear`=".$Ac_pr."  where `ride_id`=".$_POST['ride_id']);
	//   $res = $this->db->query("SELECT * from `users` join rides on rides.user_id=users.id where `ride_id`=".$_POST['ride_id'])->row();
			
					   
					$load = array();
					   $load['title'] = 'Ride confirmed';
					   $load['body'] = 'Your ambulance has been booked Xxx from xxx (ambulance service name) will arrive shortly Your ambulance has been booked for xx/xx/xxxx Tap for the details ';
					   $load['notification_type'] = 'ambulance';
					
					
			
				   
					$token[] = $res->token;

						$admin = $this->db->get("admin")->row();
						 $this->common->android_push($token, $load, "1");
					}
				
					 if ($_POST['status'] == 'CANCELLED') {
						   $cnt = $this->db->query("UPDATE `rides` set `status`='".$_POST['status']."',`driver_id`='".$_POST['driver_id']."',`driver_mobile`='".$_POST['driver_number']."',`driver_name`='".$_POST['driver_name']."',`agency_id`='".$_POST['agency_id']."',`price`='".$_POST['price']."',`Ad_commission`=".$pr.",`Driver_ear`=".$Ac_pr."  where `ride_id`=".$_POST['ride_id']);
	  
					   //  $qry = $this->db->query("SELECT `token` FROM (`users` u) JOIN `rides` r ON `r`.`user_id` = `u`.`id` OR `r`.`driver_id` = `u`.`id`
	 //WHERE `r`.`ride_id` =  " . $_POST['ride_id'] . "");
					  //   $res = $qry->result();
						$this->db->select("token");
						$this->db->from("users u");
						$this->db->join("rides r", "r.user_id = u.id");
						$this->db->where("r.ride_id", $_POST['ride_id']);
						$qry = $this->db->get();
						$res = $qry->row();

						 $load = array();
						$load['title'] = 'Ride cancelled';
						$load['body'] = 'Your ride has been cancelled';
						$load['notification_type'] = 'ambulance';
					
							$token[] = $res->token;
						 
						$admin = $this->db->get("admin")->row();
						 $this->common->android_push($token, $load, $admin->api_key);
					   
						 echo json_encode(array("status" => "success", "data" => "Your ride has been cancelled"));
					 } elseif ($_POST['status'] == 'COMPLETED') {
					   //  $qry = $this->db->query("SELECT `token` FROM (`users` u) JOIN `rides` r ON `r`.`user_id` = `u`.`id` OR `r`.`driver_id` = `u`.`id`
	// WHERE `r`.`ride_id` =  " . $_POST['ride_id'] . "");
						 //$res = $qry->result();
						   $cnt = $this->db->query("UPDATE `rides` set `status`='".$_POST['status']."'  where `ride_id`=".$_POST['ride_id']);
	  
	 $this->db->select("token");
						$this->db->from("users u");
						$this->db->join("rides r", "r.user_id = u.id");
						$this->db->where("r.ride_id", $_POST['ride_id']);
						$qry = $this->db->get();
					   $res = $qry->row();
				 
						 $load = array();
						 $load['title'] = 'Ride completed';
						$load['body'] = 'Your ride has been completed amount:'.$_POST['price'];
						 $load['notification_type'] = 'ambulance';
						$token[] = $res->token;
						 $admin = $this->db->get("admin")->row();
						 $this->common->android_push($token, $load, $admin->api_key);
						 echo json_encode(array("status" => "success", "data" => "Your ride has been completed"));
					 } else {
						 echo json_encode(array("status" => "success", "data" => "Success"));
					 }
             }
                  
             
         } else {
             echo json_encode(array("status" => "fail", "data" => "Error Try LAter."));
         }
    }
    
    /* public function rides_post() {
        $com = $this->db->query("SELECT * FROM `tbl_commision` ORDER BY `commission_id` DESC LIMIT 1")->row();
        if($_POST['utype'] == 2){
          $pr = $_POST['price'] * ($com->For_Ambulance/100);
          $Ac_pr = $_POST['price'] - $pr;
          }
          else if($_POST['utype'] == 3){
            $pr = $_POST['price'] * ($com->For_Doctor/100);
            $Ac_pr = $_POST['price'] - $pr;
          }
               else if($_POST['utype'] == 4){
            $pr = $_POST['price'] * ($com->For_Nurse/100);
            $Ac_pr = $_POST['price'] - $pr;
          }
          else{
            $pr = $_POST['price'] * ($com->For_Ambulance/100);
            $Ac_pr = $_POST['price'] - $pr;
          }

        $cnt = $this->db->query("UPDATE `rides` set `status`='".$_POST['status']."',`driver_id`='".$_POST['driver_id']."',`driver_mobile`='".$_POST['driver_number']."',`driver_name`='".$_POST['driver_name']."',`agency_id`='".$_POST['agency_id']."',`price`='".$_POST['price']."',`Ad_commission`=".$pr.",`Driver_ear`=".$Ac_pr."  where `ride_id`=".$_POST['ride_id']);
         if($cnt){
            echo json_encode(array("status" => "success", "data" => "Success"));
        }
        else{
             echo json_encode(array("status" => "fail", "data" => "Error Try LAter."));
        }
//         if ($cnt > 0) {
//             if (!empty($_POST['status'])) {
//                 if ($_POST['status'] == 'ACCEPTED') {
//                     //$res = $this->db->get_where("rides", array("ride_id" => $_POST['ride_id']))->row();
//                     $this->db->select("gcm_token");
//                     $this->db->from("users u");
//                     $this->db->join("rides r", "r.user_id = u.user_id");
//                     $this->db->where("r.ride_id", $_POST['ride_id']);
//                     $qry = $this->db->get();
//                     $res = $qry->row();

//                     $load = array();
//                     $load['title'] = 'Taxiapp';
//                     $load['msg'] = 'Your request accepted';
//                     $load['action'] = 'ACCEPTED';
//                     $token[] = $res->gcm_token;

//                     $admin = $this->db->get("admin")->row();
//                     $this->common->android_push($token, $load, $admin->api_key);
//                 }
//                 if ($_POST['status'] == 'CANCELLED') {

//                     $qry = $this->db->query("SELECT `gcm_token` FROM (`users` u) JOIN `rides` r ON `r`.`user_id` = `u`.`user_id` OR `r`.`driver_id` = `u`.`user_id`
// WHERE `r`.`ride_id` =  " . $_POST['ride_id'] . "");
//                     $res = $qry->result();

//                     $load = array();
//                     $load['title'] = 'Taxiapp';
//                     $load['msg'] = 'Your ride has been cancelled';
//                     $load['action'] = 'CANCELLED';
//                     foreach ($res as $val) {
//                         $token[] = $val->gcm_token;
//                     }
//                     $admin = $this->db->get("admin")->row();
//                     $this->common->android_push($token, $load, $admin->api_key);
                   
//                     echo json_encode(array("status" => "success", "data" => "Your ride has been cancelled"));
//                 } elseif ($_POST['status'] == 'COMPLETED') {
//                     $qry = $this->db->query("SELECT `gcm_token` FROM (`users` u) JOIN `rides` r ON `r`.`user_id` = `u`.`user_id` OR `r`.`driver_id` = `u`.`user_id`
// WHERE `r`.`ride_id` =  " . $_POST['ride_id'] . "");
//                     $res = $qry->result();

//                     $load = array();
//                     $load['title'] = 'Taxiapp';
//                     $load['msg'] = 'Your ride has been completed';
//                     $load['action'] = 'COMPLETED';
//                     foreach ($res as $val) {
//                         $token[] = $val->gcm_token;
//                     }
//                     $admin = $this->db->get("admin")->row();
//                     $this->common->android_push($token, $load, $admin->api_key);
//                     echo json_encode(array("status" => "success", "data" => "Your ride has been completed"));
//                 } else {
//                     echo json_encode(array("status" => "success", "data" => "Success"));
//                 }
//             }else{
//                 if(!empty($_POST['payment_mode'])){
                    
                    
//                     $this->db->select("gcm_token");
//                     $this->db->from("users u");
//                     $this->db->join("rides r", "r.driver_id = u.user_id");
//                     $this->db->where("r.ride_id", $_POST['ride_id']);
//                     $qry = $this->db->get();
//                     $res = $qry->row();

//                     $load = array();
                    
//                     if ($_POST['payment_mode'] != 'PAYPAL') {
//                         $load['msg'] = 'User requested offline payment';
//                     } else {
//                         $load['msg'] = 'User just paid for your ride';
//                     }
                    
//                     $load['title'] = 'Taxiapp';
                    
//                     $load['action'] = 'ACCEPTED';
//                     $token[] = $res->gcm_token;

//                     $admin = $this->db->get("admin")->row();
//                     $this->common->android_push($token, $load, $admin->api_key);
//                 }
//                 echo json_encode(array("status" => "success", "data" => "Success"));
//             }
//         } else {
//             echo json_encode(array("status" => "fail", "data" => "Error Try LAter."));
//         }
    }
*/

/*public function Acceptride(){
    date_default_timezone_set('Asia/Kolkata');
  echo  $date=date('Y-m-d');
    $time = strtotime(date('h:i'));
  echo  $endTime = date("H:i", strtotime('+30 minutes', $time));
   // print_r("SELECT * from `rides` where  `user_id`='".$_POST['user_id']."' and `status`='ACCEPTED' and pickup_date='".$date."' and pickup_time >='".$endTime."' ORDER BY `ride_id` DESC limit 1 ");
   // die();
    $query = $this->db->query("SELECT * from `rides` where  `user_id`='".$_POST['user_id']."' and `status`='ACCEPTED' and pickup_date='".$date."' and pickup_time >='".$endTime."' ORDER BY `ride_id` DESC limit 1 ")->row();
    
    if($query == True){
         echo json_encode(array("status" => "success", "data" => $query));
    }
    else {
            echo json_encode(array("status" => "fail", "data" => "No data found."));
        }
}*/


public function Acceptride(){
    $query = $this->db->query("SELECT * from `rides` where  `user_id`='".$_POST['user_id']."' and `status`='ACCEPTED' ORDER BY ride_id DESC limit 1 ")->row_array();
    if($query == True){
        
         $driver_id=$query['agency_id'];
$agnecy_id=$query['driver_id'];



$query1 = $this->db->query("SELECT * from `ambulance_fare` where `agency_id`='".$agnecy_id."' and `driver_user_id`='".$driver_id."'")->row_array();

      $query12= array_merge($query,$query1);

        
         echo json_encode(array("status" => "success", "data" => $query12));
    }
    else {
            echo json_encode(array("status" => "fail", "data" => "No data found."));
        }
}
/* get User ride details */
public function User_ride_status(){
    $query = $this->db->query("SELECT * from `rides` where  `user_id`='".$_POST['user_id']."' and payment_status='0' ORDER BY ride_id DESC limit 1 ")->row_array();
    if($query == True){
        
         $driver_id=$query['agency_id'];
$agnecy_id=$query['driver_id'];



$query1 = $this->db->query("SELECT * from `ambulance_fare` where `agency_id`='".$agnecy_id."' and `driver_user_id`='".$driver_id."'")->row_array();

      $query12= array_merge($query,$query1);

        
         echo json_encode(array("status" => "success", "data" => $query12));
    }
    else {
            echo json_encode(array("status" => "fail", "data" => "No data found."));
        }
}





  public function profile() {
       $query = $this->db->query("SELECT * from `users` where `id`='".$_POST['user_id']."' ")->row();


    if($query == True){
         echo json_encode(array("status" => "success", "data" => $query));
    }
    else {
            echo json_encode(array("status" => "fail", "data" => "No data found."));
        }
    }

    // public function profile_update(){
        
    // }
    //  public function profile2_get() {
    //     if (!empty($_GET['user_id'])) {
    //         $res = $this->db->get_where("users", array("user_id" => $_GET['user_id']))->row();
    //         if (!empty($res->avatar)) {
    //             $res->avatar = $this->config->base_url() . $res->avatar;
    //         }
    //         if (!empty($res->license)) {
    //             $res->license = $this->config->base_url() . $res->license;
    //         }
    //         if (!empty($res->insurance)) {
    //             $res->insurance = $this->config->base_url() . $res->insurance;
    //         }
    //         if (!empty($res->permit)) {
    //             $res->permit = $this->config->base_url() . $res->permit;
    //         }
    //         if (!empty($res->registration)) {
    //             $res->registration = $this->config->base_url() . $res->registration;
    //         }
    //         $this->response(array("status" => "success", "data" => $res));
    //     } else {
    //         $this->response(array("status" => "fail", "data" => "User id not send"));
    //     }
    // }
public function Completeride(){
$query = $this->db->query("SELECT * from `rides` where `ride_id`='".$_POST['ride_id']."' and `user_id`='".$_POST['user_id']."' and `status`='COMPLETED' and payment_status='0' ")->row_array();



    if($query == True){
     
 $driver_id=$query['agency_id'];
$agnecy_id=$query['driver_id'];

$query1 = $this->db->query("SELECT * from `ambulance_fare` where `agency_id`='".$agnecy_id."' and `driver_user_id`='".$driver_id."'")->row_array();

      $query12= array_merge($query,$query1);
       
         echo json_encode(array("status" => "success", "data" => $query12));
    }
    else {
            echo json_encode(array("status" => "fail", "data" => "No data found."));
        }
}


public function dAcceptride(){
    $query = $this->db->query("SELECT * from `rides` where  `driver_id`='".$_POST['user_id']."' and (`status`='ACCEPTED' ||`status`='COMPLETED') and payment_status='0' and book_later='' ORDER BY `ride_id` DESC limit 1")->result();
 
    if($query == True){
         echo json_encode(array("status" => "success", "data" => $query));
    }
    else {
            echo json_encode(array("status" => "fail", "data" => "No data found."));
        }
}


public function dAcceptrideupcoming(){
    $query = $this->db->query("SELECT * from `rides` where  `driver_id`='".$_POST['user_id']."' and(`status`='ACCEPTED' ||`status`='COMPLETED') and payment_status='0' and book_later='YES' ORDER BY `ride_id` DESC")->result();
 
    if($query == True){
         echo json_encode(array("status" => "success", "data" => $query));
    }
    else {
            echo json_encode(array("status" => "fail", "data" => "No data found."));
        }
}


public function dCompleteride(){
$query = $this->db->query("SELECT * from `rides` where  `driver_id`='".$_POST['user_id']."' and `status`='COMPLETED' and payment_status='1' ORDER BY `ride_id` DESC ")->result();

    if($query == True){
         echo json_encode(array("status" => "success", "data" => $query));
    }
    else {
            echo json_encode(array("status" => "fail", "data" => "No data found."));
        }
}
public function dCancelledide(){
$query = $this->db->query("SELECT * from `rides` where  `driver_id`='".$_POST['user_id']."' and `status`='CANCELLED' ORDER BY `ride_id` DESC ")->result();
// echo $query;
// print_r($this->db->last_query());
    if($query == True){
         echo json_encode(array("status" => "success", "data" => $query));
    }
    else {
            echo json_encode(array("status" => "fail", "data" => "No data found."));
        }
}

public function update_lat_long(){
    if(!empty($_POST['ride_id'])){
  $update = $this->db->query('UPDATE `rides` SET `Cu_Lat`="'.$_POST['latitude'].'",`Cu_long`="'.$_POST['longitude'].'" where `ride_id`='.$_POST['ride_id']);
     if($update == True){
         echo json_encode(array("status" => "success", "msg" =>'Updated'));
    }
    else {
            echo json_encode(array("status" => "fail", "data" => "Something went wrong."));
        }
    }
    else{
         echo json_encode(array("status" => "fail", "data" => "Ride id is Missing."));
    }
}


public function update_payment_status(){
    if(!empty($_POST['ride_id'])){
  $update = $this->db->query('UPDATE `rides` SET `payment_status`="'.$_POST['payment_status'].'",`pay_mode`="'.$_POST['pay_mode'].'",`payment_trans_id`="'.$_POST['trans_id'].'" where `ride_id`='.$_POST['ride_id']);
     if($update == True){
         echo json_encode(array("status" => "success", "msg" =>'Updated'));
    }
    else {
            echo json_encode(array("status" => "fail", "data" => "Something went wrong."));
        }
    }
    else{
         echo json_encode(array("status" => "fail", "data" => "Ride id is Missing."));
    }
}



public function get_payment_status(){
    if(!empty($_POST['ride_id'])){
  $get = $this->db->query('Select * from rides where `ride_id`='.$_POST['ride_id'].' and `payment_status` =1 ')->row();
     if(count($get) > 0){
         
         $price=$get->price;
         $pay_mode=$get->pay_mode;
          $payment_trans_id=$get->payment_trans_id;
        
          $get_mode = $this->db->query('Select * from payment_method where `id`='.$pay_mode.'')->row();
          $pay_mode_name=$get_mode->payment_method; 
         
     $array_ride_payment=array("payment_status"=>"done",
     "payment_mode"=>$pay_mode_name,
     "price"=>$price,
     "trans_id"=>$payment_trans_id
     );
      echo json_encode(array("status" => "success", "data" => $array_ride_payment));
    }
    else {
            echo json_encode(array("status" => "fail", "data" => "Something went wrong."));
        }
    }
    else{
         echo json_encode(array("status" => "fail", "data" => "Ride id is Missing."));
    }
}


         public function book_later(){
        

        empty($_POST['limit']) ? $limit = 3000 : $limit = $_POST['limit'];
        $query = $this->db->query("select id,token,name,email,lat,lng,utype,vehicle_info,(((acos(sin((" . $_POST['lat'] . "*pi()/180)) *
        sin((`lat`*pi()/180))+cos((" . $_POST['lat'] . "*pi()/180)) *
        cos((`lat`*pi()/180)) * cos(((" . $_POST['long'] . "-
        `lng`)*pi()/180))))*180/pi())*60*1.1515*1.609344) 
        as distance
        from users where utype!=0 and utype!=1 and subtype='".$_POST['subtype_id']."' and utype='".$_POST['type_id']."' and ac_type='".$_POST['ac_type']."' and is_online = 1 HAVING distance < $limit order by distance asc")->result();
foreach ($query as $tokeng ) {
        $token[] = $tokeng->gcm_token;
         
   
}

  
    
   $sele = $this->db->query("SELECT * from `users` where `id`=".$_POST['user_id'])->row();

   $usern = $sele->name;
   if($usern == ''){
    
     $username = 'Someone';
   }
   else{
$username = $usern;
   }
             $user_mobile=$_POST['user_mobile'];
             $ac=$_POST['ac_type'];
             $drop_lat=$_POST['lat'];
             $drop_long=$_POST['long'];
             $pickup_adress=$_POST['pickup_adress'];
             $drop_address=$_POST['drop_address'];
              $pickup_time=$_POST['pickup_time'];
               $pickup_date=$_POST['pickup_date'];
             $book_later=$_POST['book_later'];
      
  
   $tp = $_POST['type_id'];
   if($tp == 2){
     $msg = array
          (  
                'title' => 'You have a new Ride',
                'body'  => $username. ' is Looking out for Ambulance at  ' .$pickup_adress. ' to '.$drop_address,
                'icon'  => 'myicon',
                'sound' => 'mySound'
          );
   }
    
   // echo $username;
   // echo $address;
   // echo $destination_address;
   // exit;
   
 

    
 define( 'API_ACCESS_KEY', 'AAAA-vknDIs:APA91bG0l7544oS_CisWxq23RL2vfboHZfDGtF9EHT9zxeH16MLf_JdrvsrDvMAPjRb8XJSw8z7k8lG77F7Z7xyOSKE6Ff7QLcSAvFB4PK0JVZIJHzjhKK2nen4Z4hEDHOowdVYZnvi6');
    $fields = array
            (
            'registration_ids' => $token,
                'notification'  => $msg
            );
    
    
    $headers = array
            (
                'Authorization: key=' . API_ACCESS_KEY,
                'Content-Type: application/json'
            );
          // print_r($fields);
         
#Send Reponse To FireBase Server    
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
                
            $response=json_decode($result);
           // print_r($result);
           //  exit();
            // echo $result;exit;
            curl_close( $ch );
            if(count($response)>0){
        
                   $this->db->insert("rides", $_POST);
                   $last_id = $this->db->insert_id();
        
                   echo json_encode(array("status" => "success", "Ride_id" => $last_id));
            }else{
        
           echo json_encode(array("status" => "unsuccess", "msg" => "Something went wrong"));
         }
        
              
        
    }




}

/* End of file welcome.php */
    /* Location: ./application/controllers/welcome.php */

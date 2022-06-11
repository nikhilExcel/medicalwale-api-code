<?php



if (!defined('BASEPATH'))

    exit('No direct script access allowed');



class User_new extends CI_Controller {



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



        $this->load->model("users_new");

        

        $this->load->library("common");

        $this->load->library('session');

    }



    public function register() {

        if (!empty($_POST['mobile']) && !empty($_POST['gcm_token'])) {

             $mobile = $_POST['mobile'];

            $dup = $this->users_new->chk_dup();

            if (empty($dup['cnt'])) {

                $randomString = $this->common->Generate_hash(4);

                $mail_data = $this->db->get("settings")->result();

                $_POST['random'] = $randomString;



                       



 $curl = curl_init();



curl_setopt_array($curl, array(

  CURLOPT_URL => "http://api.msg91.com/api/sendhttp.php?sender=MSGIND&route=4&mobiles=$mobile&authkey=219571AuhqogaI25b1a764a&country=0&message=Here is your otp '".$randomString."' . It is valid for only 10 min",

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

                    $res = $this->users_new->user_signup();

                    if (!empty($res)) {

                        $this->load->helper('string');

                        $rand = random_string('numeric', 2) . random_string('numeric', 2) . random_string('numeric', 2) . random_string('numeric', 2);

                        $datakey = array(

                            "user_id" => $res,

                            "key" => $rand

                        );

                        $this->db->insert("keys", $datakey);

                        $cnt = $this->db->affected_rows();



                        $res = $this->db->get_where("users", array('user_id' => $res))->row_array();

                        if ($cnt > 0) {

                            $res['key'] = $rand;

                        }

                        echo json_encode(array("status" => "success", "msg" => 'otp sent to your number'));

                    } else {

                        echo json_encode(array("status" => "fail", "data" => "Not Inserted"));

                    }

                    header("Content-Type:application/json");

                } else {

                    echo json_encode(array("status" => "fail", "data" => "OTP Not send, Please check your SMTP settings"));

                }

            } else {

                // $mobile = $_POST['mobile'];

                // $gcm_token = $_POST['gcm_token'];

                $randomString = $this->common->Generate_hash(4);

                $_POST['random'] = $randomString;



                       



  $curl = curl_init();



curl_setopt_array($curl, array(

  CURLOPT_URL => "http://api.msg91.com/api/sendhttp.php?sender=MSGIND&route=4&mobiles=$mobile&authkey=219571AuhqogaI25b1a764a&country=0&message=Here is your otp '".$randomString."' . It is valid for only 10 min",

  CURLOPT_RETURNTRANSFER => true,

  CURLOPT_ENCODING => "",

  CURLOPT_MAXREDIRS => 10,

  CURLOPT_TIMEOUT => 30,

  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

  CURLOPT_CUSTOMREQUEST => "GET",

  CURLOPT_SSL_VERIFYHOST => 0,

  CURLOPT_SSL_VERIFYPEER => 0,

));



$responseji = curl_exec($curl);

$err = curl_error($curl);

curl_close($curl);

if($responseji){

$update_mobile = $this->db->query('UPDATE `users` set `mobile`= "'.$_POST['mobile'].'",`random`= "'.$randomString.'" ,`otp_status`= 0 ,`gcm_token`="'.$_POST['gcm_token'].'" WHERE `mobile`="'.$_POST['mobile'].'"');

                if($update_mobile){

                    $res_user = $this->db->query("SELECT * FROM `users` where `mobile`='".$_POST['mobile']."'")->row();



                    echo json_encode(array("status" => "success", "msg" => 'otp sent to your number', "data" => $res_user));

                }

                else

                {

                    echo json_encode(array("status" => "fail", "data" => "Error"));

                }

            }

            else{

                echo json_encode(array("status" => "fail", "data" => "otp not send"));

            }



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

                $this->db->where("mobile", $_POST['mobile']);

                $this->db->update("users", array("gcm_token" => $_POST['gcm_token']));

            }

            unset($res['password']);

            $this->load->helper('string');

            $rand = random_string('numeric', 8) . random_string('numeric', 8) . random_string('numeric', 8) . random_string('numeric', 8);

            $str = $this->db->query("select user_id from `keys` where user_id = '" . $res['id'] . "'");

            $row = $str->row_array();

            if (!empty($row)) {

                $data = array(

                    "user_id" => $res['user_id'],

                    "key" => $rand

                );

                $this->db->where("user_id", $res['user_id']);

                $this->db->update("keys", $data);

            } else {

                $data = array(

                    "user_id" => $res['user_id'],

                    "key" => $rand

                );

                $this->db->insert("keys", $data);

            }

            $cnt = $this->db->affected_rows();

            if ($cnt > 0) {

                $res['key'] = $rand;

            }

            if (!empty($res['avatar'])) {

                $res['avatar'] = $this->config->base_url() . $res['avatar'];

            }

            if (!empty($res['license'])) {

                $res['license'] = $this->config->base_url() . $res['license'];

            }

            if (!empty($res['insurance'])) {

                $res['insurance'] = $this->config->base_url() . $res['insurance'];

            }

            if (!empty($res['permit'])) {

                $res['permit'] = $this->config->base_url() . $res['permit'];

            }

            if (!empty($res['registration'])) {

                $res['registration'] = $this->config->base_url() . $res['registration'];

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

            $this->db->where('user_id', $_POST['user_id']);

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



    public function getSubTypes() {
$fare_array=array();
        if(isset($_POST['id']) && !empty($_POST['id'])){
            
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
                
            $lat1=$_POST['lat1'];
            $lat2=$_POST['lat2'];
            $long1=$_POST['long1']; 
            $long2=$_POST['long2'];
            $agency_no = '49055';
               $user_id = '49055';
            
            

            $res = $this->db->query("SELECT * FROM `subtype` WHERE `tid`=".$_POST['id'])->result();

            if($res){
                
                
                foreach($res as $row){
                    
                    
                    
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
                             $fix_fare=$amb['fix_fare'];
                            $per_km=$amb['per_km_Rs'];
                            
                      
                            
                           }else{
                             $total_fare=$amb['fix_fare'];
                               $fix_fare=$amb['fix_fare'];
                                $per_km='';
                           }
                         
                         /*echo json_encode( array(
                                    "status"=>"success",
                                    "estimated_total_time" => $duration,
                                    "date"=> date('Y-m-d'),
                                    "total_amount" => $total_fare
                                 ));*/
                         
                        $id=$row->id;
                        $t_id=$row->tid;
                        $t_name=$row->t_name;
                        $name=$row->name;
                        $icon=$row->icon;
                        $imb_img=$row->amb_img;
                        $details=$row->details;
                        $feature=$row->feature;
                        $ac_nonac=$row->ac_nonac;
                        $when_use=$row->when_use;
                         
                         
                         $fare_array[]=array(
                                    "id"=>$id,
                                   "tid" =>$t_id,
                                    "t_name"=>$t_name,
                                    "name"=>$name,
                                    "icon"    =>$icon,
                                    "amb_img"=>$imb_img,
                                   "details" =>$details,
                                   "feature" =>$feature,
                                   "ac_nonac" =>$ac_nonac,
                                   "when_use" =>$when_use,
                                   "estimated_total_time" => $duration,
                                    "date"=> date('Y-m-d'),
                                    "non_ac_price" => $total_fare,
                                    "ac_price" => $total_fare,
                                    "base_fare"=>$fix_fare,
                                    "rate_per_km"=>$per_km
                                    
                             
                             
                             );
                         
                         
                           
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
                    
                    
                    
                }
                
                

                echo json_encode(array("status"=>"success", "data"=>$fare_array));

            }

        }else{

            echo '{

                "status":"unsuccess",

                "msg":"Please Send All Parameter"

            }';

        }



         

    }


//////book rise///

              public function book_ride(){

        if(isset($_POST['current_lat'])  && isset($_POST['current_long'])  && 
            isset($_POST['drop_lat'])  && isset($_POST['drop_long'])  && 
            isset($_POST['subtype_id']) && isset($_POST['type_id']) && 
            isset($_POST['pickup_adress']) && isset($_POST['drop_address']) &&
          isset($_POST['user_id']) && isset($_POST['ac'])  &&
           isset($_POST['user_mobile'])  && isset($_POST['status']))

        {
             $user_id=$_POST['user_id'];
             $user_mobile=$_POST['user_mobile'];
             $ac=$_POST['ac'];
             $type_id=$_POST['type_id'];
             $subtype_id=$_POST['subtype_id'];
             $current_lat=$_POST['current_lat'];

             $current_long=$_POST['current_long'];
             $drop_lat=$_POST['drop_lat'];
             $drop_long=$_POST['drop_long'];
             $pickup_adress=$_POST['pickup_adress'];
             $drop_address=$_POST['drop_address'];

      $status=$_POST['status'];
         $date = date("Y-m-d h:i:sa");
              


              $ride_insert = $this->db->query('INSERT INTO `rides`( `user_id`, `user_mobile`,  `pickup_adress`, `drop_address`, `lat`, `long`,  `status`, `Cu_Lat`, `Cu_long`, `type_id`, `subtype_id` ,`ac_non``Create_time`) VALUES ("'.$user_id.'","'.$user_mobile.'","'.$pickup_adress.'","'.$drop_address.'","'.$drop_lat.'","'.$drop_long.'","'.$status.'","'.$current_lat.'","'.$current_long.'","'.$type_id.'","'.$subtype_id.'","'.$ac.'","'.$date.'")');

             if($ride_insert){

                     echo json_encode(array("status"=>"success","msg"=>'data inserted'));

                 }

         else{

        

          echo json_encode(array("status"=>"unsuccess","msg"=>'data not inserted'));

                

                 }


     } 
 else

    {

         echo json_encode(array("status"=>"unsuccess","msg"=>'something went wrong'));
    }


}


    ////book ride////


    public function verify_otp(){

        if(isset($_POST['mobile']) && !empty($_POST['mobile']) && !empty($_POST['otp']) && !empty($_POST['latitude']) && !empty($_POST['longitude'])){

    $responses = $this->db->query('SELECT * FROM `users` where `phone`= "'.$_POST['mobile'].'" AND `otp_status` = 0 AND `random`= "'.$_POST['otp'].'"')->row();

    //print_r($responses);

//     print_r($responses['user_id']);

//     exit();

// // exit;

    $uid = $responses->id;



   

              if($responses){

                 // $uid = $responses->user_id;

               

                 $update = $this->db->query('UPDATE `users` SET `otp_status` = 1,`lat`="'.$_POST['latitude'].'",`lng`="'.$_POST['longitude'].'" where `random`= "'.$_POST['otp'].'" AND `mobile` = '.$_POST['mobile']);

//     

               if($update){

                $sql_key = $this->db->query('SELECT * from `keys` where `user_id`='.$uid)->row();

                     echo json_encode(array("status"=>"success","data"=>$sql_key));

                 }

               else{

                     echo json_encode(array("status"=>"unsuccess"));

                 }

                

            }

               else{

                echo json_encode(array("status"=>"unsuccess", "data"=>array()));

            }

        }



    }









    public function social(){

        if(isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['app_id']) && !empty($_POST['app_id'])  && isset($_POST['Socialtype']) && !empty($_POST['Socialtype']) && !empty($_POST['First_name']) && !empty($_POST['Last_name']) && !empty($_POST['latitude']) && !empty($_POST['longitude']))

        {

            $App_id = trim(stripcslashes($_POST['app_id']));

            $date = date("Y-m-d h:i:sa");

            $email = trim(stripcslashes($_POST['email']));

            $Socialtype = trim(stripcslashes($_POST['Socialtype']));

            $socials = $this->db->query('SELECT * from `users` where `App_id`="'.$App_id.'" AND `Socialtype`="'.$Socialtype.'"')->result();

            if ($socials) {

                $update_social = $this->db->query('UPDATE `users` set `First_name`="'.$_POST['First_name'].'",`Last_name`="'.$_POST['Last_name'].'",`lat`= "'.$_POST['latitude'].'",`lng`="'.$_POST['longitude'].'" where `App_id`="'.$App_id.'"');

                if($update_social){

                    echo json_encode(array("status"=>"success" ,"msg"=>'updated'));

                }

                else{

                    echo json_encode(array("status"=>"unsuccess","msg"=>'not updated'));

                }

        }

        else {

              

              $social_insert = $this->db->query('INSERT into `users`(`First_name`,`Last_name`,`email`,`App_id`,`Socialtype`,`reg_date`) VALUES ("'.$_POST['First_name'].'","'.$_POST['Last_name'].'","'.$email.'","'.$App_id.'","'.$Socialtype.'","'.$date.'")');



            if($social_insert){

                     echo json_encode(array("status"=>"success","msg"=>'data inserted'));

                 }

         else{

        

          echo json_encode(array("status"=>"unsuccess","msg"=>'data not inserted'));

                

                 }

        }







    



    }

    else

    {

         echo json_encode(array("status"=>"unsuccess","msg"=>'something went wrong'));

    }



}





function userdata(){

    $user_id = trim(stripcslashes($_POST['id']));



    $user_data = $this->db->query('SELECT * from `users` where `user_id`="'.$user_id.'"')->row();

    if( $user_data ){

        echo json_encode(array("status"=>"success", "data"=>$user_data));



    }

    else

    {

       echo json_encode(array("status"=>"unsuccess")); 

    }



}

function user_update(){

     $user_id = trim(stripcslashes($_POST['id']));





$user_update = $this->db->query('UPDATE `users` set `First_name`="'.$_POST['First_name'].'",`Last_name`="'.$_POST['Last_name'].'",`Gender`="'.$_POST['gender'].'",`Age`="'.$_POST['age'].'",`mobile`="'.$_POST['mobile'].'" WHERE `user_id`="'.$user_id.'"');





if($user_update){

     echo json_encode(array("status"=>"success"));

                 }

                 else{

                     echo json_encode(array("status"=>"unsuccess"));

                 }



}



public function notification(){

    if(isset($_POST['user_id']) && !empty($_POST['user_id'])){

        $sql_select = $this->db->query('SELECT * from `users` where `user_id`='.$_POST['user_id'])->row();

        

         $gcmd = $sql_select->gcm_token;

         $title = 'hello';

     

         $msgs = 'hello this is testing';

          define( 'API_ACCESS_KEY', 'AIzaSyAhHMCanTRv_kdLLbt-1dhrZonPLDGUrKo');



     $msg = array

          (

                'body'  => $msgs,

                'title' => $title,

                'icon'  => 'myicon',

                'sound' => 'mySound'

          );



    $fields = array

            (

                'registration_ids'=> $gcmd,

                'notification'  => $msg

            );

    

    

    $headers = array

            (

                'Authorization: key=' . API_ACCESS_KEY,

                'Content-Type: application/json'

            );

          // print_r($fields);

          // exit();

#Send Reponse To FireBase Server    

        $ch = curl_init();

        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );

        curl_setopt( $ch,CURLOPT_POST, true );

        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );

        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );

        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

        $result = curl_exec($ch );

        curl_close( $ch );

    $res=json_decode($result);

   print_r($result);

    exit();

    if($res->success>0){

           echo json_encode(array("status" => "success", "msg" => "notification send sucessfully"));

    }









 else{



   echo json_encode(array("status" => "unsuccess", "msg" => "Something went wrong"));

 }

  



    }

}

   public function get_sub_Types(){

         $res = $this->db->query("SELECT `name` FROM `subtype` where `tid`='".$_POST['type_id']."'")->result();

         // $sql = $this->db->last_query();

         // echo $sql;

         // exit();

        if($res){

            echo json_encode(array("status"=>"success", "data"=>$res));

        }else{

             echo json_encode(array("status"=>"unsuccess", "msg"=>'data not found')); 

        }

        





     }

     public function nearby_rides(){



        empty($_GET['limit']) ? $limit = 5000 : $limit = $_GET['limit'];

        $query = $this->db->query("select id,name,email,lat,lng,utype,vehicle_info,(((acos(sin((" . $_GET['lat'] . " * pi()/180)) *

        sin((`lat` * pi()/180))+cos((" . $_GET['lat'] . " * pi()/180)) *

        cos((`lat` * pi()/180)) * cos(((" . $_GET['long'] . "-

        `lng`) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) 

        as distance

        from users where utype!=0 and utype!=1 and subtype='".$_POST['subtype_id']."' and is_online = 1 HAVING distance < $limit order by distance asc");

 // $sql = $this->db->last_query();

         // echo $sql;

         // exit();

        $res = $query->result();



     }





public function is_online(){

    if(!empty($_POST['user_id'])){

    $online = $this->db->query("UPDATE `users` SET `is_online`='".$_POST['online_status']."' where `id`=".$_POST['user_id']);

    if($online){

         echo json_encode(array("status"=>"success", "data"=>$_POST['online_status']));

    }

    else{

             echo json_encode(array("status"=>"unsuccess", "msg"=>'something went wrong')); 

        }

}

}

public function complete(){

    if(!empty($_POST['driver_id']) && !empty($_POST['ride_id'])){

        $complete = $this->db->query("UPDATE `rides` SET `status`='".$_POST['status']."',`payment_status`='".$_POST['pay_status']."',`payment_mode`='".$_POST['pay_mod']."' where `status`='ACCEPTED' and `user_id`=".$_POST['driver_id']." and `ride_id`=".$_POST['ride_id']);



        $res = $this->db->query("SELECT * FROM `rides` where `ride_id` = ".$_POST['ride_id'])->row();

         // $sql = $this->db->last_query();

         // echo $sql;

         // exit();

        if($complete == TRUE){



            echo json_encode(array("status"=>"success", "data"=>$res));





        }

        else{

             echo json_encode(array("status"=>"unsuccess", "msg"=>'something went wrong')); 

        }

    }

}

 



 public function rieview(){

    if(!empty($_POST['ride_id'])){



//         $F = $_FILES['file1']['F_priscrition'];

//          $S = $_FILES['file2']['S_priscrition'];

//           $T = $_FILES['file3']['T_priscrition'];

//  $target_dir = BASEPATH."../avatar/priscrition/";

//  $Fp = $target_dir . basename($_FILES["file1"]["F_priscrition"]);

//   $Sp = $target_dir . basename($_FILES["file2"]["S_priscrition"]);

//    $Tp = $target_dir . basename($_FILES["file3"]["T_priscrition"]);



//  $F_priscrition = strtolower(pathinfo($Fp,PATHINFO_EXTENSION));

//  $S_priscrition = strtolower(pathinfo($Sp,PATHINFO_EXTENSION));

//  $T_priscrition = strtolower(pathinfo($Tp,PATHINFO_EXTENSION));





//  $extensions_arr = array("jpg","jpeg","png","gif");





//  if( in_array($F_priscrition,$extensions_arr) && in_array($S_priscrition,$extensions_arr) && in_array($T_priscrition,$extensions_arr)){

 

// echo "hii";

// exit;

//   $image_base641 = base64_encode(file_get_contents($_FILES['file1']['tmp_name']) );

//   $image1 = 'data:image/'.$imageFileType.';base64,'.$image_base64;



//    $image_base642 = base64_encode(file_get_contents($_FILES['file2']['tmp_name']) );

//   $image2 = 'data:image/'.$imageFileType.';base64,'.$image_base64;



//    $image_base643 = base64_encode(file_get_contents($_FILES['file3']['tmp_name']) );

//   $image3 = 'data:image/'.$imageFileType.';base64,'.$image_base64;



//     move_uploaded_file($_FILES['file1']['tmp_name'],$target_dir.$_POST['ride_id']);

//         move_uploaded_file($_FILES['file2']['tmp_name'],$target_dir.$_POST['ride_id']);

//             move_uploaded_file($_FILES['file3']['tmp_name'],$target_dir.$_POST['ride_id']);

         // if (!empty($_FILES['F_priscrition']['name'])) {

            

           

         //        $path = $_FILES['F_priscrition']['name'];

         //        $ext = pathinfo($path, PATHINFO_EXTENSION);

         //        $rand = 'img_' . time() . rand(1, 988);

         //      $config['upload_path'] = BASEPATH . "../avatar/priscrition";

         //        $config['allowed_types'] = 'gif|jpg|png|jpeg';

         //        $config['file_name'] = $rand;

         //        $this->load->library('upload', $config);

         //        $this->upload->initialize($config);

         //        $this->upload->overwrite = true;

         //        if ($this->upload->do_upload('F_priscrition')) {

         //            $data = $this->upload->data();

         //            $_POST['F_priscrition'] = "avatar/" . $rand . '.' . $ext;

         //            $Firstp = $_POST['F_priscrition'];

         //        } else {

         //            $this->response(array("status" => "fail", "data" => "Error during file upload: only select jpg,png or gif."));

         //            die;

         //        }

         //    }



            // if (!empty($_FILES['S_priscrition']['name'])) {

            //     unset($config);

            //     $path = $_FILES['S_priscrition']['name'];

            //     $ext = pathinfo($path, PATHINFO_EXTENSION);

            //     $rand1 = 'img_' . time() . rand(1, 988);

            //     $config['upload_path'] = BASEPATH . "../avatar/priscrition";

            //     $config['allowed_types'] = 'gif|jpg|png|jpeg';

            //     $config['file_name'] = $rand1;

            //     $this->load->library('upload', $config);

            //     $this->upload->initialize($config);

            //     $this->upload->overwrite = true;

            //     if ($this->upload->do_upload('S_priscrition')) {

            //         $data = $this->upload->data();

            //         $_POST['S_priscrition'] = "avatar/" . $rand1 . '.' . $ext;

            //         $Secondp= $_POST['S_priscrition'];

            //     } else {

            //         $this->response(array("status" => "fail", "data" => "Error during file upload: only select jpg,png or gif."));

            //         die;

            //     }

            // }

            // if (!empty($_FILES['T_priscrition']['name'])) {

            //     unset($config);

            //     $path = $_FILES['T_priscrition']['name'];

            //     $ext = pathinfo($path, PATHINFO_EXTENSION);

            //     $rand2 = 'img_' . time() . rand(1, 988);

            //     $config['upload_path'] = BASEPATH . "../avatar/priscrition";

            //     $config['allowed_types'] = 'gif|jpg|png|jpeg';

            //     $config['file_name'] = $rand2;

            //     $this->load->library('upload', $config);

            //     $this->upload->initialize($config);

            //     $this->upload->overwrite = true;

            //     if ($this->upload->do_upload('T_priscrition')) {

            //         $data = $this->upload->data();

            //         $_POST['T_priscrition'] = "avatar/" . $rand2 . '.' . $ext;

            //         $thirdp = $_POST['T_priscrition'];

            //     } else {

            //         $this->response(array("status" => "fail", "data" => "Error during file upload: only select jpg,png or gif."));

            //         die;

            //     }

            // }



$F_priscrition =$this->input->post('F_priscrition');

// $dataURL = $_POST["imageData"]; this should send to the hell cause spend long time

$F_priscrition = str_replace('data:image/png;base64,', '', $F_priscrition);

$F_priscrition = str_replace(' ', '+', $F_priscrition);

$image1 = base64_decode($F_priscrition);

$filename1 = 'Array'.date("d-m-Y-h:i:s") . '.' . 'png'; //renama file name based on time



//$path1 = set_realpath(BASEPATH . "../avatar/priscrition");

$autoload1['helper1'] = array(BASEPATH . "avatar/priscrition");

$path1 = $autoload1['helper1'];

file_put_contents($helper1. $filename1, $image1);



$S_priscrition =$this->input->post('S_priscrition');

// $dataURL = $_POST["imageData"]; this should send to the hell cause spend long time

$S_priscrition = str_replace('data:image/png;base64,', '', $S_priscrition);

$S_priscrition = str_replace(' ', '+', $S_priscrition);

$image2 = base64_decode($S_priscrition);

$filename2 = 'Array'.date("d-m-Y-h:i:s") . '.' . 'png'; //renama file name based on time



$autoload2['helper2'] = array(BASEPATH . "avatar/priscrition");

$path2 = $autoload2['helper2'];

file_put_contents($helper2. $filename2, $image2);



$T_priscrition =$this->input->post('T_priscrition');

// $dataURL = $_POST["imageData"]; this should send to the hell cause spend long time

$T_priscrition = str_replace('data:image/png;base64,', '', $T_priscrition);

$T_priscrition = str_replace(' ', '+', $T_priscrition);

$image3 = base64_decode($T_priscrition);

$filename3 = 'Array'.date("d-m-Y-h:i:s") . '.' . 'png';  //renama file name based on time



$autoload3['helper3'] = array(BASEPATH . "avatar/priscrition");

$path3 = $autoload3['helper3'];

file_put_contents($helper3. $filename3, $image3);



        $rieview = $this->db->query("UPDATE `rides`SET `remark`='".$_POST['remark']."',`pay_mode`='".$_POST['pay_mode']."',`pay_status`=".$_POST['pay_status'].",`First_prescription`='".$filename1."',`Secon_prescription`='".$filename2."',`third_prescription`='".$filename3."' where `ride_id`=".$_POST['ride_id']);

         $res = $this->db->query("SELECT * FROM `rides` where `ride_id` = ".$_POST['ride_id'])->row();



        if($rieview == TRUE){

            echo json_encode(array("status"=>"success", "data"=>$res));

        }

        else{

             echo json_encode(array("status"=>"unsuccess", "msg"=>'something went wrong')); 

        }

    // } 

        //}

 }



}



public function dashboarb_to(){

    if(!empty($_POST["driver_id"])){

        $total_records = $this->db->query("SELECT COUNT(ride_id) as 'totale'  FROM `rides` where `driver_id`=".$_POST['driver_id'])->row();



          $total_record_oneday = $this->db->query("SELECT sum(price) as 'total_oneday'  FROM `rides` where `driver_id`=".$_POST['driver_id']." and NOW() <= DATE_ADD(Create_time, INTERVAL 1 DAY)")->row();

// $str = $this->db->last_query();

//             echo $str;

            $total_record_Weekday = $this->db->query("SELECT sum(price) as 'total_Weekday'  FROM `rides` where `driver_id`=".$_POST['driver_id']." and NOW() <= DATE_ADD(Create_time, INTERVAL 1 WEEK)")->row();

            // $str = $this->db->last_query();

            // echo $str;



             $total_record = $this->db->query("SELECT sum(price) as 'total'  FROM `rides` where `driver_id`=".$_POST['driver_id'])->row();

// $str = $this->db->last_query();

//             echo $str;exit;

             $this->db->select('agency_id,driver_registration.mobile');
            $this->db->from('users');
            $this->db->join('driver_registration', 'driver_registration.user_id = users.id','left');
            $this->db->where("users.id",$_POST["driver_id"]);
             $res = $this->db->get()->row_array();
           echo   $com = $this->db->query("SELECT * FROM `tbl_commision` ORDER BY `commission_id` DESC LIMIT 1")->row();

            $rd = $this->db->query("SELECT * FROM `rides` where `driver_id`=".$_POST["driver_id"])->row();



          if($rd->type_id == 2){

       

        $drivr_Rs = $total_record->total;

         // echo $drivr_Rs;



         //$total_onedays = $total_record_oneday->total_oneday ;

        $Driver_total_oneday = $total_record_oneday->total_oneday;



// echo $Driver_total_oneday;



         

        $_Weekday_drivr_Rs = $total_record_Weekday->total_Weekday;

      // echo $_Weekday_drivr_Rs;

    }
        else{

  // $total_oneday = $total_record->total_oneday ;

        $Driver_total_oneday = $total_record->total_oneday;





          //$Weekday = $total_record->total_Weekday;

        $_Weekday_drivr_Rs = $total_record->total_Weekday;

   }





















       // $trecord =  $total_record->total;

    // $to = $total_record;

    // print_r($total_record);

     if($total_records == TRUE){

          echo json_encode(array("status"=>"success", "Total_rides"=>$total_records->totale +0 , "Total_earning" =>$drivr_Rs +0,"total_oneday"=>$Driver_total_oneday +0 ,"Weekday"=>$_Weekday_drivr_Rs +0,"agency_id"=>$res['agency_id'],"driver_mobile"=>$res['mobile']));

     }

     else {

        echo json_encode(array("status"=>"unsuccess", "msg"=>'something went wrong')); 

     }

        

    }

}

public function overall_ear(){

    if(!empty($_POST["driver_id"])){

        $total_record = $this->db->query("SELECT sum(price) as 'total'  FROM `rides` where `driver_id`=".$_POST['driver_id'])->row();

         $com = $this->db->query("SELECT * FROM `tbl_commision` ORDER BY `commission_id` DESC LIMIT 1")->row();

             $rd = $this->db->query("SELECT * FROM `rides` where `driver_id`=".$_POST["driver_id"])->row();



       // $trecord =  $total_record->total;

    // $to = $total_record;

    // print_r($total_record);

// echo $com->For_Ambulance;

          if($rd->type_id == 2){

        $over_all = $total_record->total * ($com->For_Ambulance/100);

        $drivr_Rs = $total_record->total - $over_all;

      

    }

   else if($rd->type_id == 3){

     $over_all = $total_record->total * ($com->For_Doctor/100);

        $drivr_Rs = $total_record->total - $over_all;

     

   }

     else if($rd->type_id == 4){

     $over_all = $total_record->total * ($com->For_Nurse/100);

        $drivr_Rs = $total_record->total - $over_all;

 



   }



        else{

     $over_all = $total_record->total;

        $drivr_Rs = $total_record->total - $over_all;

   }

    

     if($total_record == TRUE){

          echo json_encode(array("status"=>"success", "Total"=>$drivr_Rs));

     }

     else {

        echo json_encode(array("status"=>"unsuccess", "msg"=>'something went wrong')); 

     }

        

    }

}

public function oneday_earning(){

    if(!empty($_POST["driver_id"])){

        $total_record_oneday = $this->db->query("SELECT sum(price) as 'total_oneday'  FROM `rides` where `driver_id`=".$_POST['driver_id']." and NOW() <= DATE_ADD(Create_time, INTERVAL 1 DAY)")->row();

       // $trecord =  $total_record->total;

    // $to = $total_record;

    // print_r($total_record);

        echo $total_record_oneday->total_oneday;

        exit;

     if($total_record_oneday == TRUE){

          echo json_encode(array("status"=>"success", "Total"=>$total_record_oneday->total_oneday));

     }

     else {

        echo json_encode(array("status"=>"unsuccess", "msg"=>'something went wrong')); 

     }

        

    }

}

public function User_History_rides(){

    if(!empty($_POST['user_id']) && !empty($_POST['utype'])){

       if($_POST['utype'] == 2 ){

          $history = $this->db->query('SELECT *  FROM `rides` where `status`="COMPLETED" and `user_id`='.$_POST['user_id'].' and `type_id`='.$_POST['utype'].' ORDER BY `ride_id` DESC' )->result();



       }

       elseif ($_POST['utype'] == 3 ) {

            $history = $this->db->query('SELECT *  FROM `rides` where `status`="COMPLETED" and `user_id`='.$_POST['user_id'].' and `type_id`='.$_POST['utype'].' ORDER BY `ride_id` DESC' )->result();

       }

       else{

          $history = $this->db->query('SELECT *  FROM `rides` where `status`="COMPLETED" and `user_id`='.$_POST['user_id'].' and `type_id`='.$_POST['utype'].' ORDER BY `ride_id` DESC' )->result();

       }

       if($history == TRUE){

         echo json_encode(array("status"=>"success", "data"=>$history));

       }

          else {

        echo json_encode(array("status"=>"unsuccess", "msg"=>'No Data found')); 

     }



    }



}

public function User_Monthly_History_rides(){

    if(!empty($_POST['user_id']) && !empty($_POST['utype'])){

       if($_POST['utype'] == 2 ){

          $history = $this->db->query('SELECT *  FROM `rides` where `status`="COMPLETED"  and `user_id`='.$_POST['user_id'].' and `type_id`='.$_POST['utype'].' and NOW() <= DATE_ADD(Create_time, INTERVAL 30 DAY) ORDER BY `ride_id` DESC' )->result();



       }

       elseif ($_POST['utype'] == 3 ) {

            $history = $this->db->query('SELECT *  FROM `rides` where `status`="COMPLETED" and `user_id`='.$_POST['user_id'].' and `type_id`='.$_POST['utype'].'and NOW() <= DATE_ADD(Create_time, INTERVAL 30 DAY) ORDER BY `ride_id` DESC' )->result();

       }

       else{

          $history = $this->db->query('SELECT *  FROM `rides` where `status`="COMPLETED" and `user_id`='.$_POST['user_id'].' and `type_id`='.$_POST['utype'].'  and NOW() <= DATE_ADD(Create_time, INTERVAL 30 DAY) ORDER BY `ride_id` DESC' )->result();

       }

       if($history == TRUE){

         echo json_encode(array("status"=>"success", "data"=>$history));

       }

          else {

        echo json_encode(array("status"=>"unsuccess", "msg"=>'No Data found')); 

     }



    }



}

public function driver_History_rides(){

    if(!empty($_POST['driver_id']) && !empty($_POST['utype'])){

       if($_POST['utype'] == 2 ){

          $history = $this->db->query('SELECT *  FROM `rides`  where `status`="COMPLETED" and `driver_id`='.$_POST['driver_id'].' and `type_id`='.$_POST['utype'].' ORDER BY `ride_id` DESC')->result();



       }

       elseif ($_POST['utype'] == 3 ) {

            $history = $this->db->query('SELECT *  FROM `rides`  where `status`="COMPLETED" and `driver_id`='.$_POST['driver_id'].' and `type_id`='.$_POST['utype'].' ORDER BY `ride_id` DESC')->result();

       }

       else{

          $history = $this->db->query('SELECT *  FROM `rides`  where `status`="COMPLETED" and `driver_id`='.$_POST['driver_id'].' and `type_id`='.$_POST['utype'].' ORDER BY `ride_id` DESC')->result();

       }

       if($history == TRUE){

         echo json_encode(array("status"=>"success", "data"=>$history));

       }

          else {

        echo json_encode(array("status"=>"unsuccess", "msg"=>'No Data found')); 

     }



    }

     else {

        echo json_encode(array("status"=>"unsuccess", "msg"=>'No Data found')); 

     }



}



public function driver_Monthly_History_rides(){

    if(!empty($_POST['driver_id']) && !empty($_POST['utype'])){

       if($_POST['utype'] == 2 ){

          $history = $this->db->query('SELECT *  FROM `rides`  where `status`="COMPLETED" and `driver_id`='.$_POST['driver_id'].' and `type_id`='.$_POST['utype'].' and NOW() <= DATE_ADD(Create_time, INTERVAL 30 DAY) ORDER BY `ride_id` DESC')->result();



       }

       elseif ($_POST['utype'] == 3 ) {

            $history = $this->db->query('SELECT *  FROM `rides`  where `status`="COMPLETED" and `driver_id`='.$_POST['driver_id'].' and `type_id`='.$_POST['utype'].' and NOW() <= DATE_ADD(Create_time, INTERVAL 30 DAY)  ORDER BY `ride_id` DESC')->result();

       }

       else{

          $history = $this->db->query('SELECT *  FROM `rides`  where `status`="COMPLETED" and `driver_id`='.$_POST['driver_id'].' and `type_id`='.$_POST['utype'].' and NOW() <= DATE_ADD(Create_time, INTERVAL 30 DAY) ORDER BY `ride_id` DESC')->result();

       }

       if($history == TRUE){

         echo json_encode(array("status"=>"success", "data"=>$history));

       }

          else {

        echo json_encode(array("status"=>"unsuccess", "msg"=>'No Data found')); 

     }



    }

     else {

        echo json_encode(array("status"=>"unsuccess", "msg"=>'No Data found')); 

     }



}

public function payment_history(){

	if(!empty($_POST['driver_id'])){



$total_record = $this->db->query("SELECT `user_mobile`,`Create_time`,`price`,`Ad_commission`,`payment_status`  FROM `rides` where `status`='COMPLETED' and `driver_id`=".$_POST['driver_id'])->result();

           

   if($total_record == TRUE){

         echo json_encode(array("status"=>"success", "data"=>$total_record));

       }

          else {

        echo json_encode(array("status"=>"unsuccess", "msg"=>'No Data found')); 

     }

}



}

public function ride_complete_user(){
	if(!empty($_POST['ride_id']) && !empty($_POST['user_id'])){
	$res = $this->db->query("SELECT * from `rides` where `user_id`=".$_POST['user_id']." and `ride_id`=".$_POST['ride_id']." ORDER BY `user_id` DESC" )->row();
         $status = $res->status;

         if($res== TRUE){
         	echo json_encode(array("status"=>"success", "ride_status"=>$status));
         }
         else
         {
            echo json_encode(array("status"=>"unsuccess", "msg"=>'No Data found')); 
         }

	}
	else {
		 echo json_encode(array("status"=>"unsuccess", "msg"=>'Please send all Parameter')); 
	}
}



public function Driver_update(){
	if (!empty($_POST['user_id'])) {
            if (!empty($_FILES['avatar']['name'])) {
                $path = $_FILES['avatar']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $rand = 'img_' . time() . rand(1, 988);
                $config['upload_path'] = BASEPATH . "../avatar/";
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['file_name'] = $rand;
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                $this->upload->overwrite = true;
                if ($this->upload->do_upload('avatar')) {
                    $data = $this->upload->data();
                    $_POST['avatar'] = "avatar/" . $rand . '.' . $ext;
                } else {
                    $this->response(array("status" => "fail", "data" => "Error during file upload: only select jpg,png or gif."));
                    die;
                }
            }

            if (!empty($_FILES['license']['name'])) {
                unset($config);
                $path = $_FILES['license']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $rand1 = 'img_' . time() . rand(1, 988);
                $config['upload_path'] = BASEPATH . "../avatar/";
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['file_name'] = $rand1;
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                $this->upload->overwrite = true;
                if ($this->upload->do_upload('license')) {
                    $data = $this->upload->data();
                    $_POST['license'] = "avatar/" . $rand1 . '.' . $ext;
                } else {
                    $this->response(array("status" => "fail", "data" => "Error during file upload: only select jpg,png or gif."));
                    die;
                }
            }
            if (!empty($_FILES['insurance']['name'])) {
                unset($config);
                $path = $_FILES['insurance']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $rand2 = 'img_' . time() . rand(1, 988);
                $config['upload_path'] = BASEPATH . "../avatar/";
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['file_name'] = $rand2;
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                $this->upload->overwrite = true;
                if ($this->upload->do_upload('insurance')) {
                    $data = $this->upload->data();
                    $_POST['insurance'] = "avatar/" . $rand2 . '.' . $ext;
                } else {
                    $this->response(array("status" => "fail", "data" => "Error during file upload: only select jpg,png or gif."));
                    die;
                }
            }

            if (!empty($_FILES['permit']['name'])) {
                unset($config);
                $path = $_FILES['permit']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $rand3 = 'img_' . time() . rand(1, 988);
                $config['upload_path'] = BASEPATH . "../avatar/";
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['file_name'] = $rand3;
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                $this->upload->overwrite = true;
                if ($this->upload->do_upload('permit')) {
                    $data = $this->upload->data();
                    $_POST['permit'] = "avatar/" . $rand3 . '.' . $ext;
                } else {
                    $this->response(array("status" => "fail", "data" => "Error during file upload: only select jpg,png or gif."));
                    die;
                }
            }
            if (!empty($_FILES['registration']['name'])) {
                unset($config);
                $path = $_FILES['registration']['name'];
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $rand4 = 'img_' . time() . rand(1, 988);
                $config['upload_path'] = BASEPATH . "../avatar/";
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['file_name'] = $rand4;
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                $this->upload->overwrite = true;
                if ($this->upload->do_upload('registration')) {
                    $data = $this->upload->data();
                    $_POST['registration'] = "avatar/" . $rand4 . '.' . $ext;
                } else {
                    $this->response(array("status" => "fail", "data" => "Error during file upload: only select jpg,png or gif."));
                    die;
                }
            }
            if (!empty($_POST['password'])) {
                $_POST['password'] = md5($_POST['password']);
            }
            
           $driver_up =  $this->db->where("user_id", $_POST["user_id"]);
             $driver_up = $this->db->update("users", $_POST);
            if($driver_up == TRUE){
            	$rew = $this->db->query("SELECT * from `users` where `user_id`=".$_POST["user_id"])->row();
            	echo json_encode(array("status"=>"success", "data"=>$rew));
            }
            else{
            	 echo json_encode(array("status"=>"unsuccess", "msg"=>'Data updation failed')); 
            }
            // !empty($_POST['avatar']) ? $_POST['avatar'] = $this->config->base_url() . $_POST['avatar'] : '';
            // !empty($_POST['license']) ? $_POST['license'] = $this->config->base_url() . $_POST['license'] : '';
            // !empty($_POST['insurance']) ? $_POST['insurance'] = $this->config->base_url() . $_POST['insurance'] : '';
            // !empty($_POST['permit']) ? $_POST['permit'] = $this->config->base_url() . $_POST['permit'] : '';
            // !empty($_POST['registration']) ? $_POST['registration'] = $this->config->base_url() . $_POST['registration'] : '';
            // $this->response(array("status" => "success", "data" => $_POST));
        } else {
            $this->response(array("status" => "fail"));
        }
    }




public function forget_password(){
	if(!empty($_POST['mobile'])){
         $mobile = $_POST['mobile'];
		$mobile_otp = $this->db->query("SELECT * from `users` where `mobile`='".$_POST['mobile']."'")->result();
		if($mobile_otp == TRUE){
			 $randomString = $this->common->Generate_hash(4);
                $mail_data = $this->db->get("settings")->result();
                $_POST['random'] = $randomString;
           $otp = $this->db->query("INSERT INTO `otp_foget`(`mobile`, `otp`) VALUES ('".$_POST['mobile']."','".$_POST['random']."')");
           if($otp == TRUE){

 $curl = curl_init();



curl_setopt_array($curl, array(

  CURLOPT_URL => "http://api.msg91.com/api/sendhttp.php?sender=MSGIND&route=4&mobiles=$mobile&authkey=219571AuhqogaI25b1a764a&country=0&message=Here is your otp '".$randomString."' . It is valid for only 10 minute only",

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
   if($response == TRUE){
   	echo json_encode(array("status"=>"success", "msg"=>'OTP send to your number please verify!')); 
   }
   else{
   	   echo json_encode(array("status"=>"success", "data"=>'otp can not be send'));
   }


curl_close($curl);

           }

		}
		else{
			echo json_encode(array("status"=>"success", "msg"=>'please enter valid mobile number'));
		}
	}
	else{
		 echo json_encode(array("status"=>"success", "data"=>'please send all Parameter'));
	}
}
 public function otp_verify_forget(){
 	if(!empty($_POST['mobile']) && !empty($_POST['otp'])){
 	$otp_verify = $this->db->query("SELECT * from `otp_foget` where `mobile`='".$_POST['mobile']."' and `otp`='".$_POST['otp']."' and `status`= 0 and NOW() <= DATE_ADD(date, INTERVAL 10 MINUTE)");
 	// $str = $this->db->last_query();
 	// echo $str;
 	// exit;
 	if($otp_verify ->num_rows() > 0){
 		$update_status = $this->db->query("UPDATE `otp_foget` set `status`= 1  where `mobile`='".$_POST['mobile']."' and `otp`='".$_POST['otp']."'");
 		if($update_status == TRUE){
 			echo json_encode(array("status"=>"success", "data"=>'valid otp'));
 		}
 		else{
            	 echo json_encode(array("status"=>"unsuccess", "msg"=>'status not changed')); 
            }
 	}
 	else{
            	 echo json_encode(array("status"=>"unsuccess", "msg"=>'This is not valid otp')); 
            }
 	}
 	else{
            	 echo json_encode(array("status"=>"unsuccess", "msg"=>'Error')); 
            }
 }
 
  public function getTypes1() {



            $res = $this->db->query("SELECT * FROM `subtype`")->result();

            if($res){

                echo json_encode(array("status"=>"success", "data"=>$res));

            }else{

                echo json_encode(array("status"=>"unsuccess", "data"=>array()));

            }

    

    }
 
 
}

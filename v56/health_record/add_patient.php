<?php

if (isset($_POST['user_id'])) {
    $result = array();
    require_once("../config.php");
    date_default_timezone_set('Asia/Calcutta');
    $date          = date('Y-m-d');
    $user_id       = $_POST['user_id'];
    $patient_name  = $_POST['patient_name'];
    $relationship  = $_POST['relationship'];
    $email         = $_POST['email'];
    $token         = $_POST['token'];
    $agent         = $_POST['agent'];
    $mobile        = $_POST['phone'];
    $dob           = $_POST['date_of_birth'];
    $date_of_birth = str_replace("/", "-", $dob);
    
    $age         = (date('Y') - date('Y', strtotime($dob)));
    $existing_id = 0;
    $gender      = $_POST['gender'];
    $image       = $_POST['image'];
    $vendor_type = $_POST['vendor_type'];
    
    if ($user_id != '') {
        $sql   = "SELECT id FROM `users` WHERE id='$user_id' limit 1";
        $res   = mysqli_query($hconnection, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {
           /*$res2   = mysqli_query($hconnection, "SELECT id FROM `health_record` WHERE email='$email' and phone='$mobile' limit 1");
            $count2 = mysqli_num_rows($res2);
            if ($count2 > 0) {
                $existing_row = mysqli_fetch_array($res2);
                $existing_id  = $existing_row['id'];
            } else {
                $post_insert = mysqli_query($connection, "INSERT INTO `health_record`(`user_id`,`patient_age`,`patient_name`, `relationship`, `date_of_birth`, `gender`, `created_at`,`email`,`phone`) VALUES ('$user_id','$age','$patient_name','$relationship','$date_of_birth','$gender','$date','$email','$mobile')");
                
                
            }
            if ($post_insert) {
                $patient_id = mysqli_insert_id($connection);				
				$query   = mysqli_query($connection,"SELECT users.id,users.name,users.gender,users.dob,users.phone,users.email,users.relationship,users.created_at,media.source FROM `users` LEFT JOIN media ON users.avatar_id = media.id WHERE users.email='$email' or users.phone='$mobile'");
                $count = mysqli_num_rows($query);
               
                if ($count > 0) {
					$row = mysqli_fetch_array($query);
                    $image = $row['source'];
                    if (empty($image)) {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    } else {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                    }
                } else {
                    date_default_timezone_set('Asia/Kolkata');
                    $updated_at = date('Y-m-d H:i:s');
                    if ($image != '') {
                        $type       = 'image';
                        $insert = mysqli_query($connection, "INSERT INTO `media`(`title`,`type`,`source`, `created_at`, `updated_at`) VALUES ('$image','$type','$image','$updated_at','$updated_at')");
                        $media_id = mysqli_insert_id($connection);;
                        $profile  = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                    } else {
                        $media_id = '0';
                        $profile  = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                    if ($phone == '') {
                        $phone = '';
                    }
                    
                    $userpassword = '';
                    $insert = mysqli_query($connection, "INSERT INTO `users`(`parent_id`,`relationship`,`name`, `avatar_id`, `phone`, `email`, `gender`, `dob`, `password`, `token`, `agent`, `token_status`, `created_at`) VALUES ('$user_id','$relationship','$patient_name','$media_id','$mobile','$email','$gender','$date_of_birth','$userpassword','$token','$agent','1','$updated_at')");
                }
				
                echo json_encode(array(
                    'status' => 200,
                    'message' => 'success',
                    'patient_id' => $patient_id
                ));
            } 
        
            else if ($existing_id > 0) {
                echo json_encode(array(
                    'status' => 202,
                    'message' => 'failed',
                    'message' => 'Patient already exists',
                    'patient_id' => $existing_id
                ));
            } else {
                $patient_id = '0';
                echo json_encode(array(
                    'status' => 201,
                    'message' => 'failure1',
                    'patient_id' => $patient_id
                ));
            }*/
            
          $query = mysqli_query($hconnection, "SELECT users.id FROM `users` WHERE users.email='$email' or users.phone='$mobile'");
		  $count =  mysqli_num_rows($query);
        if ($count > 0) {
            $row = mysqli_fetch_array($query);
           $relation_id=$row['id'];
            date_default_timezone_set('Asia/Kolkata');
                    $updated_at = date('Y-m-d H:i:s');
                    if ($image != '') {
                        $type       = 'image';
                        $insert = mysqli_query($connection, "INSERT INTO `media`(`title`,`type`,`source`, `created_at`, `updated_at`) VALUES ('$image','$type','$image','$updated_at','$updated_at')");
                        $media_id = mysqli_insert_id($connection);;
                        $profile  = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                    } else {
                        $media_id = '0';
                        $profile  = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                   
                 $post_insert = mysqli_query($connection, "INSERT INTO `health_record`(`user_id`,`relation_id`,`patient_age`,`patient_name`, `relationship`, `date_of_birth`, `gender`, `created_at`,`email`,`phone`) VALUES ('$user_id','$relation_id','$age','$patient_name','$relationship','$date_of_birth','$gender','$date','$email','$mobile')");
                $patient_id = mysqli_insert_id($connection);
                echo json_encode(array(
                    'status' => 200,
                    'message' => 'success',
                    'patient_id' => $patient_id
                ));
        } else {
             date_default_timezone_set('Asia/Kolkata');
                    $updated_at = date('Y-m-d H:i:s');
                    if ($image != '') {
                        $type       = 'image';
                        $insert = mysqli_query($connection, "INSERT INTO `media`(`title`,`type`,`source`, `created_at`, `updated_at`) VALUES ('$image','$type','$image','$updated_at','$updated_at')");
                        $media_id = mysqli_insert_id($connection);;
                        $profile  = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                    } else {
                        $media_id = '0';
                        $profile  = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                    if ($phone == '') {
                        $phone = '';
                    }
                    
                    $userpassword = '';
                    $insert = mysqli_query($connection, "INSERT INTO `users`(`parent_id`,`relationship`,`name`, `avatar_id`, `phone`, `email`, `gender`, `dob`, `password`, `token`, `agent`, `token_status`, `created_at`) VALUES ('$user_id','$relationship','$patient_name','$media_id','$mobile','$email','$gender','$date_of_birth','$userpassword','$token','$agent','1','$updated_at')");
                    $patient_id_user = mysqli_insert_id($connection);
                     $post_insert = mysqli_query($connection, "INSERT INTO `health_record`(`user_id`,`relation_id`,`patient_age`,`patient_name`, `relationship`, `date_of_birth`, `gender`, `created_at`,`email`,`phone`) VALUES ('$user_id','$patient_id_user','$age','$patient_name','$relationship','$date_of_birth','$gender','$date','$email','$mobile')");
                     $patient_id = mysqli_insert_id($connection);
          echo json_encode(array(
                    'status' => 200,
                    'message' => 'success',
                    'patient_id' => $patient_id
                ));
        }   
            
            
            
            
        }
        
        
        
        else {
            $patient_id = '0';
            echo json_encode(array(
                'status' => 201,
                'message' => 'failure2',
                'patient_id' => $patient_id
            ));
        }
    } else {
        echo json_encode(array(
            'status' => 201,
            'message' => 'failure3'
        ));
    }
    mysqli_close($connection);
} else {
    echo json_encode(array(
        'status' => 201,
        'message' => 'failure4'
    ));
}
?>

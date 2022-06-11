<?php

if (isset($_POST['user_id'])) {
    $result = array();
    require_once("../config.php");
    date_default_timezone_set('Asia/Calcutta');
    $date = date('Y-m-d');
    $user_id = $_POST['user_id'];
    $patient_name = $_POST['patient_name'];
    $relationship = $_POST['relationship'];
    $dob = $_POST['date_of_birth'];
    $date_of_birth = str_replace("/","-",$dob);
    $gender = $_POST['gender'];
    $image = $_POST['image'];
    
    
    //New
    
    
    

    if ($user_id != '') {
        $sql = "SELECT id FROM `users` WHERE id='$user_id' limit 1";
        $res = mysqli_query($hconnection, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {
            $post_insert = mysqli_query($connection, "INSERT INTO `health_record`(`user_id`,`patient_name`, `relationship`, `date_of_birth`, `gender`, `created_at`) VALUES ('$user_id','$patient_name','$relationship','$date_of_birth','$gender','$date')");
            if ($post_insert) {
                $patient_id = mysqli_insert_id($connection);
                echo json_encode(array(
                    'status' => 200,
                    'message' => 'success',
                    'patient_id' => $patient_id
                ));
            } else {
                $patient_id = '0';
                echo json_encode(array(
                    'status' => 201,
                    'message' => 'failure1',
                    'patient_id' => $patient_id
                ));
            }
        } else {
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

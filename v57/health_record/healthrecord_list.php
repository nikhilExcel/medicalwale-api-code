<?php

require_once("../config.php");
$health_record_list = array();
if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    if ($user_id != '') {
        $sql = "SELECT id,user_id,phone,email,patient_name,relationship,date_of_birth,gender,created_at FROM health_record where `user_id` = '$user_id' and active='0' order by id desc";
        $res = mysqli_query($connection, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {
            $true_false = 'true';
            $health_record_list = array();
            $health_record_media = array();
            while ($row = mysqli_fetch_array($res)) {
                $health_record_id = $row['id'];
                $user_id = $row['user_id'];
                $phone = $row['phone'];
                $email = $row['email'];
                $patient_name = $row['patient_name'];
                $relationship = $row['relationship'];
                $dob = $row['date_of_birth'];
                $date_of_birth = str_replace("-","/",$dob);
                $gender = $row['gender'];
                $created_at = $row['created_at'];
                $image_source = $row['source'];
                $media = $row['media'];
                $health_record_media = '';
                $sql2 = "SELECT id,media,date,created_at,type FROM health_record_media WHERE health_record_id='$health_record_id' order by created_at desc";
                $res2 = mysqli_query($connection, $sql2);
                $count_media = mysqli_num_rows($res2);
                if ($count_media > 0) {
                    while ($row_media = mysqli_fetch_array($res2)) {
                        $media_id = $row_media['id'];
                        $media = $row_media['media'];
                        $date = $row_media['date'];
                        $created_at = $row_media['created_at'];
                        $type_ = $row_media['type'];
                        if ($type_ == 'pdf') {
                            $type_ = 'files';
                        } else {
                            $type_ = 'image';
                        }
                        $media_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/health_record_media/' . $type_ . '/' . $media;

                        $health_record_media[] = array(
                            'document_id' => $media_id,
                            'document_link' => $media_source,
                            'document_date' => $date
                        );
                    }
                } else {
                    $health_record_media = array();
                }
                $health_record_list[] = array(
                    'id' => $health_record_id,
                    'patient_name' => $patient_name,
                    'phone' => $phone,
                    'email' => $email,
                    'relationship' => $relationship,
                    'date_of_birth' => $date_of_birth,
                    'gender' => $gender,
                    'created_at' => $created_at,
                    'document_count' => sizeof($health_record_media),
                    'document_list' => $health_record_media
                );
            }
            $json = array(
                "status" => 200,
                "msg" => "success",
                "count" => sizeof($health_record_list),
                "data" => $health_record_list
            );
        } else {
            $json = array(
                "status" => 0,
                "msg" => "health list not found"
            );
        }
    } else {
        $json = array(
            "status" => 0,
            "msg" => "health list not found"
        );
    }
} else {
    $json = array(
        "status" => 0,
        "msg" => "health list not found"
    );
}
@mysqli_close($conn);
header('Content-type: application/json');
echo json_encode($json);
?>
<?php

if (isset($_POST['child_id'])) {
    $result = array();
    require_once ("../config.php");
    date_default_timezone_set('Asia/Calcutta');
    $record_datetime = $_POST['record_datetime'];
    $date = date('Y-m-d');
    $user_id = $_POST['user_id'];
    $child_id = $_POST['child_id'];
    $comment = $_POST['comment'];

    $image = count($_FILES['image']['name']);

    if ($image > 0) {
        $files = '';
        $widths = '0';
        $heights = '0';
        $flag = '1';
        for ($i = 0; $i < count($_FILES['image']['name']); $i++) {
            $file = '';
            $images = '';
            $data = '';
            $success = '';
            $images = $_FILES['image']['name'][$i];
            $check_type = pathinfo($images, PATHINFO_EXTENSION);

            $file = uniqid() . $_FILES["image"]["name"][$i];
            move_uploaded_file($_FILES['image']["tmp_name"][$i], $base_url . $file);
            $files .= str_replace("../../public_html/child_care_images/", "", $file) . ',';
            if ($flag > 0) {
                $imgfile = str_replace("../../public_html/child_care_images/", "", $file);
                $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/child_care_images/' . $imgfile;
                $imagedetails = getimagesize($img_url);

                $widths = $imagedetails[0];
                $heights = $imagedetails[1];
                $flag = '0';
            }
        }
        $health_record_media = mysqli_query($connection, "INSERT INTO `child_mydiary`(`child_id`, `comment`, `record_datetime`, `image`, `date`, `user_id`,`height`,`width`) VALUES ('$child_id','$comment','$record_datetime','$files','$date','$user_id','$heights','$widths')");
        echo json_encode(array('status' => 200, 'message' => 'success'));
    } else {
        echo json_encode(array('status' => 201, 'message' => 'failure'));
    }
    mysqli_close($connection);
} else {
    echo json_encode(array('status' => 201, 'message' => 'failure'));
}
?>


<?php

if (isset($_POST['user_id'])) {
    $result = array();
    require_once("../config.php");
    date_default_timezone_set('Asia/Calcutta');
    $date = date('Y-m-d H:i:s');
    $uni = date('YmdHis');
    $comment = $_POST['comment'];
    $record_datetimes = $_POST['record_datetime'];
    $record_datetime = date("Y-m-d", strtotime($record_datetimes));
    // $record_datetime= date("j F Y", strtotime($record_datetimes)); 

    $user_id = $_POST['user_id'];
    $child_id = $_POST['child_id'];

    function getExtension($str) {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return $ext;
    }

    $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
    $video_format = array("mp4", "avi", "flv", "wmv", "mov", "3gp", "MP4", "AVI", "FLV", "WMV", "MOV", "3GP");
    include('../s3_config.php');
    $image = count($_FILES['image']['name']);

    if ($user_id != '') {
        $sql = "SELECT id FROM `users` WHERE id='$user_id' limit 1";
        $res = mysqli_query($hconnection, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {

            $post_insert = mysqli_query($connection, "INSERT INTO `child_mydiary`(`child_id`,`comment`, `record_datetime`, `date`, `user_id`) VALUES ('$child_id','$comment', '$record_datetime', '$date','$user_id')");

            if ($post_insert) {
                $post_id = mysqli_insert_id($connection);
                if ($image > 0) {
                    $flag = '1';
                    $video_flag = '1';
                    foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
                        $img_name = $key . $_FILES['image']['name'][$key];
                        $img_size = $_FILES['image']['size'][$key];
                        $img_tmp = $_FILES['image']['tmp_name'][$key];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if (in_array($ext, $img_format)) {
                                    $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/child_care_images/image/' . $actual_image_name;
                                    if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {

                                        if ($flag > 0) {
                                            $img_url = 'https://medicalwale.s3.amazonaws.com/images/child_care_images/image/' . $actual_image_name;
                                            $imagedetails = getimagesize($img_url);
                                            $widths = $imagedetails[0];
                                            $heights = $imagedetails[1];
                                            $flag = '0';
                                        }

                                        $post_media = mysqli_query($connection, "INSERT INTO `child_mydiary_media`(`record_datetime`,`post_id`,`child_id`, `type`, `source`,`img_height`, `img_width`,`video_height`, `video_width`, `created_at`) VALUES ('$record_datetime','$post_id','$child_id', 'image', '$actual_image_name', '$heights', '$widths','0','0', '$date')");
                                    }
                                }
                                if (in_array($ext, $video_format)) {
                                    $uniqid = uniqid() . date("YmdHis");
                                    $actual_video_name = $uniqid . "." . $ext;
                                    $actual_video_path = 'images/child_care_images/video/' . $actual_video_name;

                                    if ($s3->putObjectFile($img_tmp, $bucket, $actual_video_path, S3::ACL_PUBLIC_READ)) {
                                        $video_width = '300';
                                        $video_height = '160';

                                        $post_media = mysqli_query($connection, "INSERT INTO `child_mydiary_media`(`record_datetime`,`post_id`, `child_id`,`type`, `source`,`img_height`, `img_width`,`video_height`, `video_width`, `created_at`) VALUES ('$record_datetime','$post_id','$child_id', 'video', '$actual_video_name', '0', '0','$video_height','$video_width', '$date')");
                                    }
                                }
                            }
                        }
                    }
                }
                echo json_encode(array(
                    'status' => 200,
                    'message' => 'success'
                ));
            } else {
                echo json_encode(array(
                    'status' => 201,
                    'message' => 'fail'
                ));
            }
        } else {
            echo json_encode(array(
                'status' => 201,
                'message' => 'fail'
            ));
        }
    } else {
        echo json_encode(array(
            'status' => 201,
            'message' => 'fail'
        ));
    }
    mysqli_close($connection);
} else {
    echo json_encode(array(
        'status' => 201,
        'message' => 'fail1'
    ));
}
?>
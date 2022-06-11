<?php

if (isset($_POST['user_id'])) {
    $result = array();
    require_once("../config.php");
    date_default_timezone_set('Asia/Calcutta');
    $date = date('Y-m-d H:i:s');
    $uni = date('YmdHis');
    $post_date = $_POST['document_date'];
    $created_at = date('Y-m-d');
    $patient_id = $_POST['patient_id'];
    $user_id = $_POST['user_id'];
    $image_title = $_POST['title'];
    $image_caption = $_POST['caption'];
    $folder_id = $_POST['folder_id'];

    function getExtension($str) {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return $ext;
    }

    $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "pdf", "PNG", "JPG", "JPEG", "GIF", "BMP", "PDF");

    include('../s3_config.php');
    $image = count($_FILES['image']['name']);

    if ($user_id != '') {
        $sql = "SELECT id FROM `users` WHERE id='$user_id' limit 1";
        $res = mysqli_query($hconnection, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {

            if ($image > 0) {
                $files = '';
                $widths = '0';
                $heights = '0';
                $flag = '1';
                $type = explode(',', $_POST['type']);
                foreach ($type as $types) {
                    $type_array[] = $types;
                }
                $i = 0;
                foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
                
                    $img_name = $key . $_FILES['image']['name'][$key];
                    $img_size = $_FILES['image']['size'][$key];
                    $img_tmp = $_FILES['image']['tmp_name'][$key];
                    $ext = getExtension($img_name);
                   // print_r($ext); die();
                    if ($ext == 'pdf' || $ext == 'PDF') {
                        $path_name = 'files';
                    } else {
                        $path_name = 'image';
                    }

                    if (strlen($img_name) > 0) {
                        if ($img_size < (50000 * 50000)) {
                            if (in_array($ext, $img_format)) {


                                if (in_array($ext, $img_format)) {
                                    $files = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/health_record_media/' . $path_name . '/' . $files;
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                }

                                if (!in_array($ext, $img_format) && $type_array[$i] == 'pdf') {
                                    $files = uniqid() . date("YmdHis") . ".pdf";
                                    $actual_image_path = 'images/health_record_media/' . $path_name . '/' . $files;
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                }



                                if ($flag > 0 && $type_array[$i] == 'image') {
                                    $img_url = 'https://medicalwale.s3.amazonaws.com/images/health_record_media/image/' . $files;
                                    $imagedetails = getimagesize($img_url);
                                    $widths = $imagedetails[0];
                                    $heights = $imagedetails[1];
                                    $flag = '0';
                                }
//print_r($post_date); die();
                                $health_record_media = mysqli_query($connection, "INSERT INTO `health_record_media`(`health_record_id`, `media`, `type`, `source`, `img_width`, `img_height`,`image_title`, `image_caption`,`date`, `created_at`,`folder_id`) VALUES ('$patient_id','$files','$type_array[$i]','$files','$widths','$heights','$image_title','$image_caption','$post_date','$created_at','$folder_id')");
                            }
                        }
                    }
                    $i++;
                }
            }
            echo json_encode(array(
                'status' => 200,
                'message' => 'success'
            ));
        } else {
            echo json_encode(array(
                'status' => 201,
                'message' => 'fail1'
            ));
        }
    } else {
        echo json_encode(array(
            'status' => 201,
            'message' => 'fail2'
        ));
    }
    mysqli_close($connection);
} else {
    echo json_encode(array(
        'status' => 201,
        'message' => 'fail3'
    ));
}
?>
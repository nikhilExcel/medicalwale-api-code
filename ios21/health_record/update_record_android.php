<?php

require_once('aws-autoloader.php');

use Aws\S3\S3Client;

function DeleteFromToS3($filename) {
    $bucket = 'medicalwale';
    $s3 = new S3Client([
        'version' => 'latest',
        'region' => 'us-east-1',
        'credentials' => [
            'key' => 'AKIAJNIZJVO2QJMFTL5Q',
            'secret' => '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',
        ]
    ]);
    $s3->deleteObject([
        'Bucket' => $bucket,
        'Key' => $filename,
    ]);
}

if (isset($_POST['patient_id'])) {
    $result = array();
    require_once("../config.php");
    $patient_id = $_POST['patient_id'];
    date_default_timezone_set('Asia/Calcutta');
    $document_date = $_POST['document_date'];
    $image_title = $_POST['title'];
    $image_caption = $_POST['caption'];
    $folder_id = $_POST['folder_id'];
    $created_at = date('Y-m-d');
    $document_id = $_POST['document_id'];
    
    if(array_key_exists("delete_documents",$_POST)){
        $delete = $_POST['delete_documents'];     
    } else {
        $delete = 0;
    }
    // print_r($delete); die();
    
    

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

    if ($patient_id != '') {
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
                            $health_record_media = mysqli_query($connection, "INSERT INTO `health_record_media`(`health_record_id`, `media`, `type`, `source`, `img_width`, `img_height`,`image_title`, `image_caption`,`date`, `created_at`,`folder_id`) VALUES ('$patient_id','$files','$type_array[$i]','$files','$widths','$heights','$image_title','$image_caption','$document_date','$created_at','$folder_id')");
                            
                            /*$health_record_media_update = mysqli_query($connection, "UPDATE `health_record_media` SET  `image_title` ='$image_title' , `image_caption` = '$image_caption',`date` = '$document_date' WHERE `folder_id` = '$folder_id' AND `health_record_id` = '$patient_id'");*/
                            
                            if(!empty($_POST['document_id'])){
                                 $array_document_id = explode(',', $_POST['document_id']);
                                foreach ($array_document_id as $doc_id) {
                                    $health_record_media_doc = mysqli_query($connection, "UPDATE `health_record_media` SET `image_title` = '$image_title' , `image_caption` = '$image_caption',`date` = '$document_date' WHERE `id` = '$doc_id'");
                                                
                                }
                            }
                            
                           
                            
                        }
                    }
                }
                $i++;
            }
        } else {
            
             if(!empty($_POST['document_id'])){
                $array_document_id = explode(',', $_POST['document_id']);
                foreach ($array_document_id as $doc_id) {
                    $health_record_media = mysqli_query($connection, "UPDATE `health_record_media` SET `image_title` = '$image_title' , `image_caption` = '$image_caption',`date` = '$document_date' WHERE `id` = '$doc_id'");
                                
                }
             }
            // $health_record_media_update = mysqli_query($connection, "UPDATE `health_record_media` SET  `image_title` ='$image_title' , `image_caption` = '$image_caption',`date` = '$document_date' WHERE `folder_id` = '$folder_id' AND `health_record_id` = '$patient_id'");
                
            
            
        } 
        
        if($delete != 0 && !empty($delete)){
            $array_document_id = explode(',', $delete);
            if (count($array_document_id) > 0) {
                foreach ($array_document_id as $document_id) {
                    $res2 = mysqli_query($connection, "SELECT media,type FROM health_record_media WHERE health_record_id='$patient_id' and id='$document_id' limit 1");
                    $media_list = mysqli_fetch_array($res2);
                    $media_count = mysqli_num_rows($res2);
                    $media_name = $media_list['media'];
                    $media_type = $media_list['type'];
                    if ($media_type == 'pdf') {
                        $media_type = 'files';
                    } else {
                        $media_type = 'image';
                    }
                    if ($media_count > 0) {
                        $media_path = 'images/health_record_media/' . $media_type . '/' . $media_name;
                        @unlink(trim($media_path));
                        $delete_from_s3 = DeleteFromToS3($media_path);
                    }
                    $delete_health_record_media = mysqli_query($connection, "DELETE FROM `health_record_media` WHERE health_record_id='$patient_id' and id='$document_id'");
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
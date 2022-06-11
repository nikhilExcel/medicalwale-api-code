<?php

if (isset($_POST['user_id']) || isset($_POST['post_id'])) {
    $result = array();
    require_once("../config.php");
    require_once("aws_helper.php");
    date_default_timezone_set('Asia/Calcutta');
    $date = date('Y-m-d H:i:s');
    $uni = date('YmdHis');
    $tag = addslashes($_POST['tag']);
    $category = addslashes($_POST['category']);
    $saheli_category = $_POST['saheli_category'];
    $post_id = addslashes($_POST['post_id']);
    $post = addslashes($_POST['post']);
    $type = addslashes($_POST['type']);
    $user_id = addslashes($_POST['user_id']);
    $user_name = addslashes($_POST['user_name']);
    $user_image = addslashes($_POST['user_image']);
    $caption = $_POST['caption'];
    $article_title = $_POST['article_title'];
    $article_title = str_replace("'", "\'", $article_title);
    $article_image = $_POST['article_image'];
    $article_domain_name = $_POST['article_domain_name'];
    $article_url = addslashes($_POST['article_url']);
    if ($type == 'write_post') {
        $article_desc = '<a href="' . $article_url . '" target="_blank" style="text-decoration: none;"><div id="thumbnail"><img src="close.png" id="remove" width="10px"><img src="' . $article_image . '" style="width: 100%;object-fit: cover;height: 189px;"></div><div id="texts"><div id="title"><span style="font-weight:bold;text-align: justify;">' . $article_title . '</span></div> <div id="desc" style="text-align: justify;"><span style="font-size:12px"></span></div> <div id="meta"><div id="domain">' . $article_domain_name . '</div><div id="author"></div><div class="clear"></div></div></div></a>';
    } else {
        $article_desc = '';
    }

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
            mysqli_set_charset($connection, 'utf8');
            $post_update = mysqli_query($connection, "UPDATE `ask_saheli_post` SET `category`='$category',`saheli_category`='$saheli_category',`post`='$post',`article_desc`='$article_desc',`tag`='$tag',`type`='$type',`user_image`='$user_image',`user_name`='$user_name',`user_id`='$user_id',`article_title`='$article_title',`article_image`='$article_image',`article_domain_name`='$article_domain_name',`article_url`='$article_url' WHERE `id` ='$post_id'");


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
                                $actual_image_path = 'images/ask_saheli_images/image/' . $actual_image_name;
                                if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                    if ($flag > 0) {
                                        $img_url = 'https://medicalwale.s3.amazonaws.com/images/ask_saheli_images/image/' . $actual_image_name;
                                        $imagedetails = getimagesize($img_url);
                                        $widths = $imagedetails[0];
                                        $heights = $imagedetails[1];
                                        $flag = '0';
                                    }
                                    $post_media = mysqli_query($connection, "INSERT INTO `ask_saheli_post_media`(`post_id`,`caption`, `type`, `source`,`img_height`, `img_width`,`video_height`, `video_width`, `created_at`) VALUES ('$post_id','$caption[$key]', 'image', '$actual_image_name', '$heights', '$widths','0','0', '$date')");
                                }
                            }
                            if (in_array($ext, $video_format)) {
                                $uniqid = uniqid() . date("YmdHis");
                                $actual_video_name = $uniqid . "." . $ext;
                                $actual_video_path = 'images/ask_saheli_images/video/' . $actual_video_name;
                                if ($s3->putObjectFile($img_tmp, $bucket, $actual_video_path, S3::ACL_PUBLIC_READ)) {
                                    $video_width = '300';
                                    $video_height = '160';
                                    $post_media = mysqli_query($connection, "INSERT INTO `ask_saheli_post_media`(`post_id`,`caption`,`type`, `source`,`img_height`, `img_width`,`video_height`, `video_width`, `created_at`) VALUES ('$post_id','$caption[$key]', 'video', '$actual_video_name', '0', '0','$video_height','$video_width', '$date')");
                                }
                            }
                        }
                    }
                }
            }

            $array_document_id = explode(',', $_POST['media_id']);
            if (count($array_document_id) > 0) {
                foreach ($array_document_id as $media_id) {
                    $res2 = mysqli_query($connection, "SELECT source,type FROM ask_saheli_post_media WHERE id='$media_id' limit 1");
                    $media_list = mysqli_fetch_array($res2);
                    $media_count = mysqli_num_rows($res2);
                    $media_name = $media_list['source'];
                    $media_type = $media_list['type'];
                    if ($media_type == 'video') {
                        if ($media_count > 0) {
                            $media_name_ = 'https://medicalwale.s3.amazonaws.com/images/ask_saheli_images/video/' . $media_name;
                            @unlink(trim($media_name_));
                            $delete_from_s3 = DeleteFromToS3($media_name_);
                            $delete_healthwall_media = mysqli_query($connection, "DELETE FROM `ask_saheli_post_media` WHERE id='$media_id'");
                        }
                    } else {
                        if ($media_count > 0) {
                            $media_name_ = 'https://medicalwale.s3.amazonaws.com/images/ask_saheli_images/image/' . $media_name;
                            @unlink(trim($media_name_));
                            $delete_from_s3 = DeleteFromToS3($media_name_);
                        }
                        $delete_healthwall_media = mysqli_query($connection, "DELETE FROM `ask_saheli_post_media` WHERE id='$media_id'");
                    }
                }
            }


            echo json_encode(array(
                'status' => 200,
                'message' => 'success'
            ));
        } else {
            echo json_encode(array(
                'status' => 202,
                'message' => 'fail2'
            ));
        }
    } else {
        echo json_encode(array(
            'status' => 203,
            'message' => 'fail3'
        ));
    }
    mysqli_close($connection);
} else {
    echo json_encode(array(
        'status' => 204,
        'message' => 'fail4'
    ));
}
?>
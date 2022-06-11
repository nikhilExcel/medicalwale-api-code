
<?php
if (isset($_POST['user_id'])) {
    $result = array();
    require_once("../../../config.php");
    date_default_timezone_set('Asia/Calcutta');
    $date = date('Y-m-d H:i:s');
    $uni  = date('YmdHis');
    $tag        = $_POST['tag'];
    $category   = $_POST['category'];
    $post       = $_POST['post'];
    $type       = $_POST['type'];
    $user_id    = $_POST['user_id'];
    $user_name  = $_POST['user_name'];
    $user_image = $_POST['user_image'];
    $caption    = $_POST['caption'];
	
    function getExtension($str)
    {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l   = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return $ext;
    }
    
	$img_format = array("jpg", "png", "gif", "bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
	$video_format = array("mp4","avi","flv","wmv","mov","MP4","AVI","FLV","WMV","MOV");
    include('../s3_config.php');
    $image = count($_FILES['image']['name']);
	
    if($user_id != ''){
        $sql   = "SELECT id FROM `users` WHERE id='$user_id' limit 1";
        $res   = mysqli_query($hconnection, $sql);
        $count = mysqli_num_rows($res);
        if($count > 0) {
            $post_insert = mysqli_query($connection, "INSERT INTO `ask_saheli_post`(`category`,`post`,`tag`,`type`,`user_image`,`user_name`, `user_id`, `date`) VALUES ('$category','$post', '$tag','$type','$user_image', '$user_name', '$user_id', '$date')");
            if($post_insert) {
                $post_id = mysqli_insert_id($connection);				
				if($image > 0) {
                    $flag         = '1';
                    $video_flag   = '1';		
                    foreach($_FILES['image']['tmp_name'] as $key => $tmp_name)
                    {                    
                        $img_name = $key.$_FILES['image']['name'][$key];
                        $img_size = $_FILES['image']['size'][$key];
                        $img_tmp  = $_FILES['image']['tmp_name'][$key];
                        $ext      = getExtension($img_name);
                        if (strlen($img_name) > 0) {						
                                if ($img_size < (50000 * 50000)) {  
									if(in_array($ext, $img_format)){
										$actual_image_name = uniqid().date("YmdHis").".".$ext;
										$actual_image_path = 'images/ask_saheli_images/image/' . $actual_image_name;										
										if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)){
										
											if ($flag > 0) {
												$img_url      = 'https://medicalwale.s3.amazonaws.com/images/ask_saheli_images/image/'.$actual_image_name;
												$imagedetails = getimagesize($img_url);
												$widths       = $imagedetails[0];
												$heights      = $imagedetails[1];
												$flag         = '0';												
											}
											$post_media = mysqli_query($connection, "INSERT INTO `ask_saheli_post_media`(`post_id`,`caption`, `type`, `source`,`img_height`, `img_width`,`video_height`, `video_width`, `created_at`) VALUES ('$post_id','$caption[$key]', 'image', '$actual_image_name', '$heights', '$widths','0','0', '$date')");
										}									
									}
									if(in_array($ext, $video_format)){	
									$uniqid = uniqid().date("YmdHis");
                                    $actual_video_name = $uniqid.".".$ext;
                                    $actual_video_path = 'images/ask_saheli_images/video/' . $actual_video_name;								
																	
										if ($s3->putObjectFile($img_tmp, $bucket, $actual_video_path, S3::ACL_PUBLIC_READ)){
                                        $video_url    = 'https://medicalwale.s3.amazonaws.com/images/ask_saheli_images/video/' . $actual_video_name;
										$thumb_size = $_FILES['thumbnail']['size'][$key];
										$thumb_tmp  = $_FILES['thumbnail']['tmp_name'][$key];
										if($thumb_size>0){
											$actual_thumbnail_name = $uniqid.".jpg";
											$actual_thumbnail_path = 'images/healthwall_media/thumb/' . $actual_thumbnail_name;
											$s3->putObjectFile($thumb_tmp, $bucket, $actual_thumbnail_path, S3::ACL_PUBLIC_READ);
										}
										$thumb_url    = 'https://medicalwale.s3.amazonaws.com/images/ask_saheli_images/thumb/' . $actual_thumbnail_name;
										$video_width  = '0';
                                        $video_height = '0';
                                        if ($video_flag > 0) {
                                            $videodetails = getimagesize($thumb_url);
                                            $video_width  = $videodetails[0];
                                            $video_height = $videodetails[1];
                                            $video_flag   = '0';
                                        }
											$post_media = mysqli_query($connection, "INSERT INTO `ask_saheli_post_media`(`post_id`,`caption`,`type`, `source`,`img_height`, `img_width`,`video_height`, `video_width`, `created_at`) VALUES ('$post_id','$caption[$i]', 'video', '$actual_video_name', '0', '0','$video_height','$video_width', '$date')");
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
<?php

include_once('../../../config.php');
if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $user_query = mysqli_query($hconn, "SELECT id FROM users where id='$user_id'");
    $user_count = mysqli_num_rows($user_query);
    if ($user_count > 0) {

        function get_time_difference_php($created_time) {
            date_default_timezone_set('Asia/Calcutta');
            $str = strtotime($created_time);
            $today = strtotime(date('Y-m-d H:i:s'));

            $time_differnce = $today - $str;
            $years = 60 * 60 * 24 * 365;
            $months = 60 * 60 * 24 * 30;
            $days = 60 * 60 * 24;
            $hours = 60 * 60;
            $minutes = 60;
            if (intval($time_differnce / $years) > 1) {
                return intval($time_differnce / $years) . " yrs ago";
            } else if (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . " yr ago";
            } else if (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . " months ago";
            } else if (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . " month ago";
            } else if (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . " days ago";
            } else if (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . " day ago";
            } else if (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . " hrs ago";
            } else if (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . " hr ago";
            } else if (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . " sec ago";
            } else {
                return "few seconds ago";
            }
        }

        $resultpost = array();
        $ask_saheli_postQuery = mysqli_query($conn, "select ask_saheli_post.id,ask_saheli_post.caption,ask_saheli_post.views,ask_saheli_post.video_width,ask_saheli_post.video_height,ask_saheli_post.date,ask_saheli_post.post,ask_saheli_post.image,ask_saheli_post.img_height,ask_saheli_post.img_width,ask_saheli_post.type,ask_saheli_category.category,ask_user.name,ask_saheli_character.image as character_image from ask_saheli_post INNER JOIN ask_saheli_category on ask_saheli_category.id=ask_saheli_post.category INNER JOIN ask_user on ask_user.user_id=ask_saheli_post.user_id INNER JOIN ask_saheli_character on ask_saheli_character.id=ask_user.image order by ask_saheli_post.id desc limit 10");
        $ask_saheli_post_count = mysqli_num_rows($ask_saheli_postQuery);
        if ($ask_saheli_post_count > 0) {
            $flag_check = '1';
            while ($row = mysqli_fetch_array($ask_saheli_postQuery)) {
                $image = '';
                extract($row);
                $id = $id;
                $post = $post;
                $user_name = $name;
                $image = $image;
                $type = $type;
                $category = $category;
                $character_image = $character_image;
                $widths = $img_width;
                $heights = $img_height;
                $video_width = $video_width;
                $video_height = $video_height;
                $caption = $caption;
                $views = $views;
                if ($image != '') {
                    $image = explode(",", $image);
                    $cnt = count($image);
                    $files = '';
                    $img_comma = '';
                    $images = '';
                    if ($cnt > 0) {
                        for ($i = 0; $i < $cnt; $i++) {
                            if ($image[$i] != '') {
                                $images .= $img_comma . 'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/' . $image[$i];
                                $img_comma = ',';
                            }
                        }
                    } else {
                        $images = 'no';
                    }
                } else {
                    $images = 'no';
                }

                $count_query = mysqli_query($conn, "SELECT * FROM `ask_saheli_likes` where post_id='$id'");
                $like_count = mysqli_num_rows($count_query);
                $comment_query = mysqli_query($conn, "SELECT * FROM `ask_saheli_comment` where post_id='$id'");
                $post_count = mysqli_num_rows($comment_query);
                $like_count_query = mysqli_query($conn, "SELECT * FROM `ask_saheli_likes` where user_id='$user_id' and post_id='$id'");
                $like_yes_no = mysqli_num_rows($like_count_query);
                $character_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/character/' . $character_image;
                $date = get_time_difference_php($date);

                $resultpost[] = array('id' => $id, 'user_name' => $user_name, 'post' => $post, 'like_count' => $like_count, 'like_yes_no' => $like_yes_no, 'comment_count' => $post_count, 'views' => $views, 'image' => $images, 'caption' => $caption, 'img_width' => "$widths", 'img_height' => "$heights", 'video_width' => "$video_width", 'video_height' => "$video_height", 'type' => $type, 'category' => $category, 'character_image' => $character_image, 'date' => $date);
            }

            $json = array("status" => 1, "msg" => "success", "count" => sizeof($resultpost), "data" => $resultpost);
        } else {
            $json = array("status" => 0, "msg" => "post not found");
        }
    } else {
        $json = array("status" => 0, "msg" => "user not found");
    }
} else {
    $json = array("status" => 0, "msg" => "post not found");
}
@mysqli_close($hconn);
@mysqli_close($conn);
/* Output header */
header('Content-type: application/json');
echo json_encode($json);
?>
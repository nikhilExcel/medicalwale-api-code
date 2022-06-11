<?php
$result = array();
require_once("../config.php");
date_default_timezone_set('Asia/Calcutta');
$created_at = date('Y-m-d H:i:s');



$res_ = mysqli_query($hconnection, "SELECT id as video_id FROM med_spread WHERE id NOT IN (SELECT * FROM (SELECT video_id FROM med_spread_views GROUP BY video_id) AS a) ORDER BY `id` DESC");
while ($like_list = mysqli_fetch_array($res_)) {
    $video_id = $like_list['video_id'];
    $min = '4000';
    $max = '6570';
    $total_list = rand($min, $max);
    if ($video_id != '') {
        for ($i = 3680; $i <= $total_list; $i++) {
            $sql = "SELECT id FROM `med_spread_views` WHERE user_id='$i' and video_id='$video_id'";
            $res = mysqli_query($hconnection, $sql);
            $count = mysqli_num_rows($res);
            if ($count > 0) {
            } else {
                $media = mysqli_query($hconnection, "INSERT INTO `med_spread_views`(`video_id`, `user_id`) VALUES ('$video_id', '$i')");
            }
        }
        
    }
}

mysqli_close($connection);
?>
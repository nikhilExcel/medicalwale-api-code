<?php
$result = array();
require_once("../config.php");
date_default_timezone_set('Asia/Calcutta');
$created_at = date('Y-m-d H:i:s');

$res_ = mysqli_query($hconnection, "SELECT id as video_id FROM med_spread WHERE id NOT IN (SELECT * FROM (SELECT video_id FROM med_spread_likes GROUP BY video_id HAVING COUNT(video_id) >20) AS a) ORDER BY `id` DESC");

while ($like_list = mysqli_fetch_array($res_)) {
    $video_id = $like_list['video_id'];
    $min = '4000';
    $max = '4570';
    $total_list = rand($min, $max);
    if ($video_id != '') {
        for ($i = 3680; $i <= $total_list; $i++) {
            $media = mysqli_query($hconnection, "INSERT INTO `med_spread_likes`(`video_id`, `user_id`) VALUES ('$video_id', '$i')");
        }
    }
}
mysqli_close($connection);
?>

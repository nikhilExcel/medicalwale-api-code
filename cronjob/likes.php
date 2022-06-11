<?php
$result = array();
require_once("../config.php");
date_default_timezone_set('Asia/Calcutta');
$created_at = date('Y-m-d H:i:s');

$res_ = mysqli_query($hconnection, "SELECT id as post_id,user_id,type FROM posts WHERE id>'8026' and id NOT IN (SELECT * FROM (SELECT post_id FROM post_likes GROUP BY post_id HAVING COUNT(post_id) >20) AS a) ORDER BY `id` DESC");
while ($like_list = mysqli_fetch_array($res_)) {
    $post_id = $like_list['post_id'];
    $post_user_id = $like_list['user_id'];
    $type = $like_list['type'];
    if($type=='question'){
        $min = '3680';
        $max = '4280';
    }else{
        $min = '3680';
        $max = '4380';
    }
    $total_list = rand($min, $max);
    if ($post_id != '') {
        for ($i = 3680; $i <= $total_list; $i++) {
            $media = mysqli_query($hconnection, "INSERT INTO `post_likes`(`post_id`, `user_id`, `created_at`, `updated_at`) VALUES ('$post_id', '$i', '$created_at', '$created_at')");
        }
        
    }
}
mysqli_close($connection);
?>

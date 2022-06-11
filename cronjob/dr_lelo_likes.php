<?php
$result = array();
require_once("../config.php");

$res_ = mysqli_query($hconnection, "SELECT id as post_id,user_id FROM sex_education_question WHERE id>='1897' and id NOT IN (SELECT * FROM (SELECT post_id FROM sex_education_likes GROUP BY post_id HAVING COUNT(post_id) >20) AS a) ORDER BY `id` DESC");

while ($like_list = mysqli_fetch_array($res_)) {
    $post_id = $like_list['post_id'];
    $post_user_id = $like_list['user_id'];
    $min = '3870';
    $max = '4100';
    $total_list = rand($min, $max);
    if ($post_id != '') {
        for ($i = 3680; $i <= $total_list; $i++) {
            $media = mysqli_query($hconnection, "INSERT INTO `sex_education_likes`(`post_id`, `user_id`, `user_image`, `user_name`) VALUES ('$post_id', '$i', '0', 'Mishi')");
        }
        
    }
}
mysqli_close($connection);
?>

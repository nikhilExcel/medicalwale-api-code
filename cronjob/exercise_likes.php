<?php
$result = array();
require_once("../config.php");

$res_ = mysqli_query($hconnection, "SELECT id as post_id FROM exercise_details WHERE id NOT IN (SELECT * FROM (SELECT post_id FROM exercise_likes GROUP BY post_id HAVING COUNT(post_id) >20) AS a) ORDER BY `id` DESC");

while ($like_list = mysqli_fetch_array($res_)) {
    $post_id = $like_list['post_id'];
    $min = '4490';
    $max = '4600';
    $total_list = rand($min, $max);
    if ($post_id != '') {
        for ($i = 3680; $i <= $total_list; $i++) {            
            $media = mysqli_query($hconnection, "INSERT INTO `exercise_likes`(`post_id`, `user_id`) VALUES ('$post_id', '$i')");
        }
        
    }
}
mysqli_close($connection);
?>

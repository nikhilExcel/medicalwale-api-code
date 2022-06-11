<?php
$result = array();
require_once("../config.php");

$res_ = mysqli_query($hconnection, "SELECT id as post_id FROM article WHERE id NOT IN (SELECT * FROM (SELECT article_id as post_id FROM article_likes GROUP BY article_id HAVING COUNT(article_id) >20) AS a) ORDER BY `id` DESC");
while ($like_list = mysqli_fetch_array($res_)) {
    $post_id = $like_list['post_id'];
    $min = '4290';
    $max = '4400';
    $total_list = rand($min, $max);
    if ($post_id != '') {
        for ($i = 3680; $i <= $total_list; $i++) {
            $media = mysqli_query($hconnection, "INSERT INTO `article_likes`(`article_id`, `user_id`) VALUES ('$post_id', '$i')");
        }
        
    }
}

mysqli_close($connection);
?>

<?php
    $result = array();
    require_once("../config.php");
    $res_ = mysqli_query($hconnection, "select media.id,views from media INNER JOIN post_media on media.id=post_media.media_id where media.id>'23758' and media.type='video' and media.views<300");
    while($like_list = mysqli_fetch_array($res_)){
    $media_id = $like_list['id'];
    $media_views = $like_list['views'];
    $min='622';
    $max='791';
    $total_list=rand($min,$max);
    $video_views = $media_views+$total_list;
    $media  = mysqli_query($hconnection, "UPDATE `media` SET `views`='$video_views' where id='$media_id'");
    }
    mysqli_close($connection);
?>
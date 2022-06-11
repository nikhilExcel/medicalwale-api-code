<?php
    $result = array();
    require_once("../config.php");
    $res_ = mysqli_query($hconnection, "SELECT id as post_id,views FROM med_tube order by id desc");
    while($like_list = mysqli_fetch_array($res_)){
	$views=0;
    $post_id = $like_list['post_id'];
    $views   = $like_list['views'];
    $min='15';
    $max='40';
    $total_list=rand($min,$max);
    if($post_id!= '') {
		    $views=$views+$total_list;
            $media  = mysqli_query($hconnection, "UPDATE `med_tube` SET views='$views' where id='$post_id'");
        }
    }
    mysqli_close($connection);
?>
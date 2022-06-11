<?php
    $result = array();
    require_once("../config.php");
    $res_ = mysqli_query($hconnection, "SELECT id as post_id,views FROM exercise_details order by id desc");
    while($like_list = mysqli_fetch_array($res_)){
    $views=0;
    $post_id = $like_list['post_id'];
    $views   = $like_list['views'];
	if($views<750) {
		    $min='750';
    		    $max='850';
    		    $views=rand($min,$max);
                    $media  = mysqli_query($hconnection, "UPDATE `exercise_details` SET views='$views' where id='$post_id'");
        }
	 else{		    
		    $min='15';
		    $max='25';
		    $views=$views+rand($min,$max);
                    $media  = mysqli_query($hconnection, "UPDATE `exercise_details` SET views='$views' where id='$post_id'");
	 }
    }
    mysqli_close($connection);
?>

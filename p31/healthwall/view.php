<?php
if (isset($_POST['media_id'])) 
{
    $result = array();
    require_once("../config.php");
    $media_id       = addslashes($_POST['media_id']);
    if ($media_id != '') {
	    //$user_id='3634';
		for($i=3680;$i<=3810;$i++)
		{
			$media  = mysqli_query($hconnection, "INSERT INTO `post_video_views`(`media_id`, `user_id`) VALUES ('$media_id', '$i')");
		}
		if($media)
		{
			echo json_encode(array('status' => 200,'message' => 'success'));
		}
		else
		{
		    echo json_encode(array('status' => 204,'message' => 'fail1'));
		}
    } 
	else 
	{
        echo json_encode(array('status' => 204,'message' => 'fail2'));
    }    
} 
else 
{
    echo json_encode(array('status' => 204,'message' => 'fail3'));
}
mysqli_close($connection);
?>
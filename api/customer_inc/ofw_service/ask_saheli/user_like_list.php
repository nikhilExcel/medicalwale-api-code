<?php
	// Include confi.php
	include_once('../../../config.php');
	
if(isset($_POST['post_id']))
{
$post_id=$_POST['post_id'];

		$resultpost =array(); 
		$likeQuery = mysqli_query($conn,"select ask_saheli_likes.user_id,ask_user.name,ask_saheli_character.image from ask_saheli_likes INNER JOIN ask_user on ask_user.user_id=ask_saheli_likes.user_id INNER JOIN ask_saheli_character on ask_saheli_character.id=ask_user.image where ask_saheli_likes.post_id='$post_id'");
		$like_count = mysqli_num_rows($likeQuery);
		if($like_count>0)
		{
		while($row = mysqli_fetch_array($likeQuery))
		{
		extract($row);
		$user_id=$user_id;
		$name=$name;
		$image=$image;
		$image='https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/character/'.$image;
		
		$resultpost[] = array('user_id'=>$user_id,'name'=>$name,'image'=>$image); 
		}
		$json = array("status" => 1,"msg" => "success","count"=>sizeof($resultpost),"data" => $resultpost);
	}
	else
	{
	$json = array("status" => 0, "msg" => "list not found");
	}	
}
	else
	{
	$json = array("status" => 0, "msg" => "list not found");
	}

	@mysqli_close($conn);
	/* Output header */
	header('Content-type: application/json');
	echo json_encode($json);
	?>

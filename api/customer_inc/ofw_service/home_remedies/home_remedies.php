<?php
include_once('../../../config.php');

if(isset($_POST['user_id']) && isset($_POST['category_id']))
{

$category_id=$_POST['category_id'];
if($category_id!='')
{
$resulthome_remedies =array();
		$user_id=$_POST['user_id'];
		$home_remediesQuery = mysqli_query($conn,"SELECT id, category_id, title, details, image, date FROM `home_remedies` where is_active='1' AND category_id='$category_id' order by id asc");
		$home_remedies_count = mysqli_num_rows($home_remediesQuery);
		if($home_remedies_count>0)
		{
		while($row = mysqli_fetch_array($home_remediesQuery))
		{
		extract($row);
		$id=$id;
		$category_id= $category_id;
		$title=$title;
		$details=$details;
		$image=$image;
		$date=$date;
		$image=str_replace(" ","",$image);
		$image='https://d2c8oti4is0ms3.cloudfront.net/images/ofw_images/home_remedies/'.$image;
		$count_query = mysqli_query($conn,"SELECT * FROM `home_remedies_likes` where home_remedies_id='$id'");
		$like_count = mysqli_num_rows($count_query);

		$like_count_query = mysqli_query($conn,"SELECT * FROM `home_remedies_likes` where user_id='$user_id' and home_remedies_id='$id'");
		$like_yes_no = mysqli_num_rows($like_count_query);

		$view_count_query = mysqli_query($conn,"SELECT * FROM `home_remedies_likes` where home_remedies_id='$id'");
		$view_count = mysqli_num_rows($view_count_query);

		$bookmark_count_query = mysqli_query($conn,"SELECT * FROM `home_remedies_bookmark` where user_id='$user_id' and home_remedies_id='$id'");
		$bookmark_yes_no = mysqli_num_rows($bookmark_count_query);
		$share='https://medicalwale.com/images/only_for_women/home_remedies/home%20remedies%20details.php?id='.$id;

		$resulthome_remedies[] = array("id"=>$id,
			"title"=>$title,
			"category_id" => $category_id,
			'description'=>$details,
			'image'=>$image,
			'date'=>$date,
			'total_like'=>$like_count,
			'views'=>$view_count,
			'is_like'=>$like_yes_no,
			'is_bookmark'=>$bookmark_yes_no,
			'share'=>$share
			);
		}

		$json = array("status" => 1,"msg" => "success","count"=>sizeof($resulthome_remedies),"data" => $resulthome_remedies);
	}
	else
	{
	$json = array("status" => 0, "msg" => "home remedies list empty");
	}
}
else
{
$json = array("status" => 0, "msg" => "values blank");
}
}
	else
	{
	$json = array("status" => 0, "msg" => "post method error");
	}


	@mysqli_close($conn);

	/* Output header */
	header('Content-type: application/json');
	echo json_encode($json);
	?>

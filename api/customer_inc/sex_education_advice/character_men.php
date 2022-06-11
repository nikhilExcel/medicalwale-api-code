<?php
	// Include confi.php
	include_once('../../config.php');
	
if(isset($_POST['id'])){

		$resultcharacter =array(); 
		$characterQuery = mysqli_query($conn,"SELECT * FROM `sex_education_character` WHERE type='men' order by id asc");
		$character_count = mysqli_num_rows($characterQuery);
		if($character_count>0)
		{
		while($row = mysqli_fetch_array($characterQuery))
		{
		extract($row);
		$id=$id;
		$image=$image;
		$image='https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/'.$image;
		$resultcharacter[] = array('id'=>$id,'image'=>$image); 
		}

		$json = array("status" => 1,"msg" => "success","count"=>sizeof($resultcharacter),"data" => $resultcharacter);
	}
	else
	{
	$json = array("status" => 0, "msg" => "character list not found");
	}
}
	else
	{
	$json = array("status" => 0, "msg" => "character list not found");
	}
	

	@mysqli_close($conn);

	/* Output header */
	header('Content-type: application/json');
	echo json_encode($json);
	?>
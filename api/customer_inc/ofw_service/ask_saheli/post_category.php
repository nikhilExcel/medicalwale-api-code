<?php
	// Include confi.php
	include_once('../../../config.php');
	
if(isset($_POST['id'])){

		$resultcategory =array(); 
		$categoryQuery = mysqli_query($conn,"SELECT id,category FROM `ask_saheli_category` order by id asc");
		$category_count = mysqli_num_rows($categoryQuery);
		if($category_count>0)
		{
		while($row = mysqli_fetch_array($categoryQuery))
		{
		extract($row);
		$id=$id;
		$category=$category;
		$resultcategory[] = array('id'=>$id,'category'=>$category); 
		}

		$json = array("status" => 1,"msg" => "success","count"=>sizeof($resultcategory),"data" => $resultcategory);
	}
	else
	{
	$json = array("status" => 0, "msg" => "category list not found");
	}
}
	else
	{
	$json = array("status" => 0, "msg" => "category list not found");
	}
	

	@mysqli_close($conn);

	/* Output header */
	header('Content-type: application/json');
	echo json_encode($json);
	?>

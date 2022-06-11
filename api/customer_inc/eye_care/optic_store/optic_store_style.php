<?php
include_once('../../../config.php');
if(isset($_POST['id']))
{
		$resultcharacter =array(); 
		$id=$_POST['id']; 
		$style_commentQuery = mysqli_query($conn,"SELECT id,style FROM `optic_store_style`");
		$style_count = mysqli_num_rows($style_commentQuery);
		if($style_count>0)
		{
		while($row = mysqli_fetch_array($style_commentQuery))
		{
		extract($row);
		$style_id=$id;		
		$style=$style;		
		$resultcharacter[] = array('style_id'=>$style_id,'style'=>$style); 
		}
		$json = array("status" => 1,"msg" => "success","count"=>sizeof($resultcharacter),"data" => $resultcharacter);
	}
	else
	{
	$json = array("status" => 0, "msg" => "style list not found");
	}
}
	else
	{
	$json = array("status" => 0, "msg" => "style list not found");
	}
	@mysqli_close($conn);
	header('Content-type: application/json');
	echo json_encode($json);
?>

<?php
include_once('../../config.php');
if(isset($_POST['user_id']))
{
		$resultcharacter =array(); 
		$user_id=$_POST['user_id'];
		$addressQuery = mysqli_query($hconn,"SELECT address_id,firstname,telephone,address_1,address_2,landmark,city,state,postcode FROM `oc_address` WHERE customer_id='$user_id' order by address_id desc");
		$address_count = mysqli_num_rows($addressQuery);
		if($address_count>0)
		{
		while($row = mysqli_fetch_array($addressQuery))
		{
		extract($row);
		$address_id=$address_id;	
		$firstname=$firstname;	
		$telephone=$telephone;	
		$address_1=$address_1;	
		$address_2=$address_2;	
		$landmark=$landmark;	
		$city=$city;	
		$state=$state;	
		$postcode=$postcode;	
		
		$resultcharacter[] = array('address_id'=>$address_id,'firstname'=>$firstname,'telephone'=>$telephone,'address_1'=>$address_1,'address_2'=>$address_2,'landmark'=>$landmark,'city'=>$city,'state'=>$state,'postcode'=>$postcode); 
		}
		$json = array("status" => 1,"msg" => "success","count"=>sizeof($resultcharacter),"data" => $resultcharacter);
	}
	else
	{
	$json = array("status" => 0, "msg" => "address list not found");
	}
}
	else
	{
	$json = array("status" => 0, "msg" => "address list not found");
	}	

	@mysqli_close($conn);
	/* Output header */
	header('Content-type: application/json');
	echo json_encode($json);
?>

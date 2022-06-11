<?php
if(isset($_POST['id']))
{
        $resultpost =array(); 
		$from='100';
		$to='250';
		$price_inc='250';
		for($i=1;$i<15;$i++)
		{
		$resultpost[] = array('from'=>$from,'to'=>$to); 
		if($i=='1')
		{
		    $from='0';
		}
		$from=$from+$price_inc;
		$to=$to+$price_inc;
		}
		$json = array("status" => 1,"msg" => "success","count"=>sizeof($resultpost),"data" => $resultpost);
}
else
{
	$json = array("status" => 0, "msg" => "post not found");
}
@mysqli_close($conn);
header('Content-type: application/json');
echo json_encode($json);
?>

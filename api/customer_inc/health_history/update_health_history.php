<?php 
require_once("../../config.php");
$result = array();

if(isset($_POST['history_id']) && isset($_POST['user_id']) && isset($_POST['patient_name']) && isset($_POST['document_name']) && isset($_POST['description']))
{
$user_id = $_POST['user_id'];
$history_id= $_POST['history_id'];
$patient_name = $_POST['patient_name'];
$document_name = $_POST['document_name'];
$document_date= $_POST['document_date'];
$description = $_POST['description'];
date_default_timezone_set('Asia/Kolkata');
$date=date('Y-m-d');
$image1='';
$image2='';
$image3='';
$image4='';
$image5='';
$image= $_POST['image'];
if($user_id!='' && $patient_name!='' && $document_name!='')
{
if($image!='')
		{
		    $image= explode(",",$image);
		    $cnt=count($image);
		    $files='';
		    $img_comma='';
		    $images='';
		    if($cnt>0)
		    {		   
		    for($i=0;$i<$cnt;$i++)
		    { 
		    if($image[$i]!='')
		    {
		        $images.=$img_comma.'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/'.$image[$i];
				$img_comma=',';		    	
		    }
		    }
		    }
		    else
		    {
		        $images='no';
		    }
		}
		else
		{
		    $images='no';
		}

$update= mysqli_query($connection,"UPDATE `health_history` SET `patient_name`='$patient_name',`document_name`='$document_name',`document_date`='$document_date',`description`='$description',`image`='$images' where id='$history_id' and user_id='$user_id'");

$status='1';
$msg='Health History Updated';
array_push($result,array('data'=>$result));
$arry = array('status'=>$status,'msg'=>$msg);
echo json_encode($arry);
mysqli_close($connection); 
}
else
{
$status='0';
$msg='Please enter all fields!';
$result='';
$arry = array('status'=>$status,'msg'=>$msg);
echo json_encode($arry);
mysqli_close($connection);
} 
}
else
{
$status='0';
$msg='Please enter all fields!';
$result='';
$arry = array('status'=>$status,'msg'=>$msg);
echo json_encode($arry);
mysqli_close($connection);
} 
?>
<?php 
require_once ("config.php"); 
$result = array();

if(isset($_POST['address_id']) && isset($_POST['user_id'])  )
{ 
$address_id=addslashes($_POST['address_id']);
$user_id=addslashes($_POST['user_id']);

$delete= mysqli_query($connection,"DELETE FROM `oc_address` WHERE address_id='$address_id' AND customer_id='$user_id'");

if($delete)
{
$id=  mysqli_insert_id($connection);
$true_false='true';
$msg='Address Deleted!';
array_push($result,array('true_false'=>$true_false,'id'=>$id,'msg'=>$msg));
echo json_encode($result); 
mysqli_close($connection);
}
}
else
{
$error_msg='Blank data, Please provide all details!';
$true_false='false';
array_push($result,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result); 
mysqli_close($connection);
}
?>
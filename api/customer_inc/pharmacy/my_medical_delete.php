<?php 
require_once ("config.php"); 
$result = array();

if(isset($_POST['user_id']) && isset($_POST['medical_id']))
{ 
$user_id=addslashes($_POST['user_id']);
$medical_id=addslashes($_POST['medical_id']);

$delete= mysqli_query($connection,"DELETE FROM `my_medical` WHERE user_id='$user_id' AND medical_id='$medical_id'");

if($delete)
{
$id=  mysqli_insert_id($connection);
$true_false='true';
$msg='Medical Deleted!';
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
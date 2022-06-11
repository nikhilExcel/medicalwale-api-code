<?php 
require_once ("config.php"); 
$result = array();

if(isset($_POST['user_id']) && isset($_POST['medical_id']))
{ 
$user_id=addslashes($_POST['user_id']);
$medical_id=addslashes($_POST['medical_id']);
if($user_id!='' && $medical_id!='')
{
$sql1 = "SELECT * FROM `my_medical` WHERE user_id='$user_id' AND medical_id='$medical_id'"; 
$res1 = mysqli_query($connection,$sql1);
$count1=mysqli_num_rows($res1);
if($count1>0) 
{
$error_msg='You have already added same Medical!';
$true_false='false';
array_push($result,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result); 
mysqli_close($connection);
}
else
{
$insert= mysqli_query($connection,"INSERT INTO `my_medical`( `user_id` ,`medical_id`) VALUES ('$user_id', '$medical_id')");
}
if($insert)
{
$id=  mysqli_insert_id($connection);
$true_false='true';
$msg='Medical Added!';
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
}
else
{
$error_msg='Please provide all details!';
$true_false='false';
array_push($result,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result); 
mysqli_close($connection);
} 
?>
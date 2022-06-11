<?php 
$result = array();
require_once ("../../../config.php"); 
if(isset($_POST['user_id']) && isset($_POST['home_remedies_id']))
{
$user_id = $_POST['user_id'];
$home_remedies_id= $_POST['home_remedies_id'];
if($user_id!='' && $home_remedies_id!='')
{
$sql = "SELECT * FROM `home_remedies_views` WHERE user_id='$user_id' or home_remedies_id='$home_remedies_id'"; 
$res = mysqli_query($connection,$sql);
$count=mysqli_num_rows($res);
if($count>0) 
{
$error_msg='Already Viewed';
$true_false='false';
array_push($result,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result); 
mysqli_close($connection);
}
else
{
$insert= mysqli_query($connection,"INSERT INTO `home_remedies_views`(`user_id`, `home_remedies_id`) VALUES ('$user_id','$home_remedies_id')");
$error_msg='View Added';
$true_false='true';
array_push($result,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result); 
mysqli_close($connection);
}
}
else
{
$error_msg='post method error';
$true_false='false';
array_push($result,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result); 
mysqli_close($connection);
} 
}
else
{
$error_msg='post method error';
$true_false='false';
array_push($result,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result); 
mysqli_close($connection);
}
?>
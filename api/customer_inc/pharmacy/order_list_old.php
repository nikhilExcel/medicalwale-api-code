<?php
require_once("config.php");
if(isset($_POST['id']))
{
$id = $_POST['id'];
$sql = "select * from user_patient where user_id='$id'";
$res = mysqli_query($connection,$sql); 
$count = mysqli_num_rows($res);
$result1 = array();
$result2 = array(); 
if($count>0)
{
$true_false='true';

while($row = mysqli_fetch_array($res))
{

$order_id=$row['id'];
$order_status=$row['status'];
$order_date=$row['date'];
array_push($result2,array('order_id'=>$order_id,'order_status'=>$order_status,'order_date'=>$order_date));
}
array_push($result1,array('true_false'=>$true_false));
$arry = array(array('true_false'=>$true_false),$result2);
echo json_encode($arry);
mysqli_close($connection); 
}
else
{
$error_msg='No Order History';
$true_false='false';
array_push($result1,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result1); 
mysqli_close($connection); 
}
}
else
{
$error_msg='No Order History';
$true_false='false';
array_push($result1,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result1); 
mysqli_close($connection); 
}
?>
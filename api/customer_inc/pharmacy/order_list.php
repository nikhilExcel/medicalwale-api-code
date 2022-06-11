<?php
require_once("config.php");

if(isset($_POST['user_id']))
{
$user_id = $_POST['user_id'];
$sql = "select cart_order_products.medical_id,cart_order.id as order_id,cart_order.user_id,cart_order.address_id,cart_order.uni_id,cart_order.date,cart_order.status,cart_order_products.medical_id from cart_order INNER JOIN cart_order_products on cart_order_products.order_id=cart_order.id where cart_order.user_id='$user_id'";
$res = mysqli_query($connection,$sql); 
$count = mysqli_num_rows($res);
$result1 = array();
$result2 = array(); 
if($count>0)
{
$true_false='true';

while($row = mysqli_fetch_array($res))
{

$true_false='true';
$order_id=$row['order_id'];
$order_no=$row['uni_id'];
$medical_id=$row['medical_id'];
$status=$row['status'];
$order_date=$row['date'];

$sql2 = "SELECT medical_name FROM `registration` WHERE id='$medical_id'";
$res2 = mysqli_query($connection,$sql2); 
$row2 = mysqli_fetch_array($res2);
$medical_name='medical_name';

array_push($result2,array('order_id'=>$order_id,'order_no'=>$order_no,'medical_name'=>$medical_name,'status'=>$status,'order_date'=>$order_date));
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
$error_msg='No Order List';
$true_false='false';
array_push($result1,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result1); 
mysqli_close($connection); 
}
?>
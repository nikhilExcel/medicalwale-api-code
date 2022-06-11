<?php
require_once("config.php");
if(isset($_POST['order_id']))
{
$order_id = $_POST['order_id'];
$sql = "select * from user_patient where id='$order_id'";
$res = mysqli_query($connection,$sql); 
$count = mysqli_num_rows($res);
$result1 = array();
$result2 = array(); 
if($count>0)
{
$true_false='true';
$row = mysqli_fetch_array($res);
$order_id=$row['id'];
$order_status=$row['status'];
$order_date=$row['date'];
$patient_name=$row['patient_name'];
$address1=$row['address_1'];
$address2=$row['address_2'];
$landmark=$row['land_mark'];
$mobile=$row['mobile_number'];
$area=$row['area'];

array_push($result2,array('order_id'=>$order_id,'order_status'=>$order_status,'order_date'=>$order_date,'name'=>$patient_name,'address1'=>$address1,'address2'=>$address2,'landmark'=>$landmark,'mobile'=>$mobile,'area'=>$area));

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
?>
<?php 
$result = array();
require_once ("../../../config.php"); 
if(isset($_POST['user_id']))
{
$user_id = $_POST['user_id'];
$product_id = $_POST['gym_id'];  // please dont change
$rating = $_POST['rating'];
$review = $_POST['review'];
$service = $_POST['service'];
date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d H:i:s');
if($user_id!='' && $product_id!='')
{
$sql = "SELECT id FROM `timelines` WHERE id='$user_id' limit 1"; 
$res = mysqli_query($connection,$sql);
$count=mysqli_num_rows($res);
if($count>0) 
{
$insert= mysqli_query($connection,"INSERT INTO `child_physiotherapy_review`(`user_id`, `product_id`, `rating`, `review`, `service`, `date`) VALUES ('$user_id', '$product_id', '$rating', '$review', '$service', '$date')");
if($insert)
{
$status='1';
$msg='success';
$result = array('status'=>$status,'msg'=>$msg);
echo json_encode($result);
mysqli_close($connection);
}
else
{
$status='0';
$msg='failure';
$result = array('status'=>$status,'msg'=>$msg);
echo json_encode($result);
mysqli_close($connection); 
}
}
else
{
$status='0';
$msg='User not found';
$result = array('status'=>$status,'msg'=>$msg);
echo json_encode($result);
mysqli_close($connection);
}
}
else
{
$status='0';
$msg='Post Method Error!';
$result = array('status'=>$status,'msg'=>$msg);
echo json_encode($result);
mysqli_close($connection);
} 
}
else
{
$status='0';
$msg='Post Method Error!';
$result = array('status'=>$status,'msg'=>$msg);
echo json_encode($result);
mysqli_close($connection);
}
?>
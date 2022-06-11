<?php 
$result = array();
require_once ("../../../config.php"); 
if(isset($_POST['user_id']))
{
date_default_timezone_set('Asia/Calcutta');
$date = date('Y-m-d H:i:s');
$image_id = $_POST['image_id'];
$username = $_POST['username'];
$user_id = $_POST['user_id'];

if($user_id!='')
{
$sql = "SELECT id FROM `timelines` WHERE id='$user_id'"; 
$res = mysqli_query($hconnection,$sql);
$count=mysqli_num_rows($res);
if($count>0) 
{
$sql2 = "SELECT * FROM `ask_user` WHERE user_id='$user_id'"; 
$res2 = mysqli_query($connection,$sql2);
$count2=mysqli_num_rows($res2);
if($count2>0)
{
$update= mysqli_query($connection,"UPDATE `ask_user` SET `image`='$image_id',`name`='$username' where user_id='$user_id'");
}
else
{
$insert= mysqli_query($connection,"INSERT INTO `ask_user`(`user_id`, `name`, `image`) VALUES ('$user_id','$username','$image_id')");
}
$status='1';
$msg='success';

$sql3 = "SELECT image FROM `ask_saheli_character` WHERE id='$image_id'"; 
$res3 = mysqli_query($connection,$sql3);
$list=mysqli_fetch_array($res3);
$image=$list['image'];
$image='https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/character/'.$image;
$result = array('status'=>$status,'msg'=>$msg,'image'=>$image,'username'=>$username);
echo json_encode($result);
mysqli_close($connection);

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
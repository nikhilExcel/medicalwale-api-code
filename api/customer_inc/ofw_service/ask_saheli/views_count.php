<?php 
$result = array();
require_once ("../../../config.php"); 
if(isset($_POST['user_id']) && isset($_POST['post_id']))
{
$user_id = $_POST['user_id'];
$post_id= $_POST['post_id'];
if($user_id!='' && $post_id!='')
{
$sql1 = "SELECT id,views FROM `ask_saheli_post` WHERE id='$post_id'"; 
$res1 = mysqli_query($connection,$sql1);
$count2=mysqli_num_rows($res1);
if($count2>0) 
{
$sql4 = "SELECT id FROM `timelines` WHERE id='$user_id'"; 
$res4 = mysqli_query($hconnection,$sql4);
$count4=mysqli_num_rows($res4);
if($count4>0)
{
$sql = "SELECT * FROM `ask_saheli_views` WHERE user_id='$user_id' and post_id='$post_id'"; 
$res = mysqli_query($connection,$sql);
$count=mysqli_num_rows($res);
if($count>0)
{
$status='1';
$msg='success';
$result = array('status'=>$status,'msg'=>$msg);
echo json_encode($result);
mysqli_close($connection);
}
else
{
$views='0';
$get_list=mysqli_fetch_array($res1);
$views=$get_list['views'];
$views=$views+1;
$update= mysqli_query($connection,"UPDATE `ask_saheli_post` SET `views`='$views' WHERE id='$post_id'");
if($update)
{
$inserts= mysqli_query($connection,"INSERT INTO `ask_saheli_views`(`user_id`, `post_id`) VALUES ('$user_id','$post_id')");
}
$status='1';
$msg='success';
$result = array('status'=>$status,'msg'=>$msg);
echo json_encode($result);
mysqli_close($connection);
}
}
else
{
$status='0';
$msg='User not found!';
$result = array('status'=>$status,'msg'=>$msg);
echo json_encode($result);
mysqli_close($connection);
}
}
else
{
$status='0';
$msg='Post not found!';
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
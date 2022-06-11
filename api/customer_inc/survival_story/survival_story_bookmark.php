<?php 
$result = array();
require_once ("../../config.php"); 
if(isset($_POST['user_id']) && isset($_POST['survival_stories_id']))
{
$user_id = $_POST['user_id'];
$survival_stories_id= $_POST['survival_stories_id'];
if($user_id!='' && $survival_stories_id!='')
{
$res = mysqli_query($connection,"SELECT id FROM `survival_stories_bookmark` WHERE user_id='$user_id' and survival_stories_id='$survival_stories_id' limit 1");
$count=mysqli_num_rows($res);
if($count>0) 
{
$delete= mysqli_query($connection,"DELETE FROM `survival_stories_bookmark` WHERE user_id='$user_id' and survival_stories_id='$survival_stories_id'");
$res_count = mysqli_query($connection,"SELECT id FROM `survival_stories_bookmark` WHERE survival_stories_id='$survival_stories_id'");
$total_count=mysqli_num_rows($res_count);
$status='200';
$msg='success';
$result = array('status'=>200,'msg'=>$msg,'is_bookmark'=>0,'count'=>$total_count);
echo json_encode($result);
mysqli_close($connection);
}
else
{
$insert= mysqli_query($connection,"INSERT INTO `survival_stories_bookmark`(`user_id`, `survival_stories_id`) VALUES ('$user_id','$survival_stories_id')");
$res_count = mysqli_query($connection,"SELECT id FROM `survival_stories_bookmark` WHERE survival_stories_id='$survival_stories_id'");
$total_count=mysqli_num_rows($res_count);
$status='200';
$msg='success';
$result = array('status'=>200,'msg'=>$msg,'is_bookmark'=>1,'count'=>$total_count);
echo json_encode($result);
mysqli_close($connection);
}
}
else
{
$status='400';
$msg='Post Method Error!';
$result = array('status'=>400,'msg'=>$msg);
echo json_encode($result);
mysqli_close($connection);
} 
}
else
{
$status='400';
$msg='Post Method Error!';
$result = array('status'=>400,'msg'=>$msg);
echo json_encode($result);
mysqli_close($connection);
}
?>
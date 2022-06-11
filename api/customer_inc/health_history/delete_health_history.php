<?php 
require_once("../../config.php");
$result = array();
if(isset($_POST['history_id']) && isset($_POST['user_id']))
{
$user_id = $_POST['user_id'];
$history_id= $_POST['history_id'];

if($user_id!='' && $history_id!='')
{
$delete= mysqli_query($connection,"DELETE FROM `health_history` where id='$history_id' and user_id='$user_id'");
$status='1';
$msg='Health History Deleted';
array_push($result,array('data'=>$result));
$arry = array('status'=>$status,'msg'=>$msg);
echo json_encode($arry);
mysqli_close($connection); 
}
else
{
$status='0';
$msg='Please enter all fields!';
$result='';
$arry = array('status'=>$status,'msg'=>$msg,'data'=>$result);
echo json_encode($arry);
mysqli_close($connection);
} 
}
else
{
$status='0';
$msg='Please enter all fields!';
$result='';
$arry = array('status'=>$status,'msg'=>$msg,'data'=>$result);
echo json_encode($arry);
mysqli_close($connection);
} 
?>
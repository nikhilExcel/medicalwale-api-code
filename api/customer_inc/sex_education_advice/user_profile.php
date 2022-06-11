<?php 
$result = array();
require_once ("../../config.php"); 
if(isset($_POST['user_id']))
{
$user_id=$_POST['user_id'];
if($user_id>0)
{
$sql = "SELECT id FROM `users` WHERE id='$user_id'"; 
$res = mysqli_query($connection,$sql);
$count=mysqli_num_rows($res);
if($count>0) 
{
$sql2 = "SELECT sex_education_ask_expert.name,sex_education_ask_expert.image,sex_education_character.id,sex_education_character.image AS character_img FROM `sex_education_ask_expert` 
INNER JOIN `sex_education_character` ON sex_education_ask_expert.image=sex_education_character.id WHERE sex_education_ask_expert.user_id='$user_id'"; 
$res2 = mysqli_query($connection,$sql2);
$count2=mysqli_num_rows($res2);
if($count2>0)
{

$status='1';
$msg='success';
$list2=mysqli_fetch_array($res2);
$username=$list2['name'];
$image=$list2['character_img'];
$image_id=$list2['image'];
$image='https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/'.$image;
$result = array('status'=>$status,'msg'=>$msg,'image'=>$image,'image_id'=>$image_id,'username'=>$username);
echo json_encode($result);
mysqli_close($connection);
}
else
{
$status='0';
$msg='Profile not found';
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
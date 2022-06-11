<?php 
$result = array();
require_once ("../../config.php"); 
if(isset($_POST['user_id']) && isset($_POST['baby_name']) && isset($_POST['boy_girl']) && isset($_POST['baby_mom_dad']) && isset($_POST['baby_birthday']))
{
$user_id = $_POST['user_id'];
$baby_name = $_POST['baby_name'];
$boy_girl = $_POST['boy_girl'];
$image = $_POST['baby_profile_image'];
$baby_birthday = $_POST['baby_birthday'];
$baby_birth_place = $_POST['baby_birth_place'];
$baby_mom_dad = $_POST['baby_mom_dad'];
$date = $_POST['date'];
if($user_id!='' && $baby_name!='' && $boy_girl!='' && $baby_birthday!='' && $baby_birth_place!='' && $baby_mom_dad!='')
{
$sql = "SELECT id FROM `timelines` WHERE id='$user_id' limit 1"; 
$res = mysqli_query($hconn,$sql);
$count=mysqli_num_rows($res);
if($count>0) 
{
if($image!='')
{
$image = str_replace('data:image/jpeg;', '', $image);
$image = str_replace('data:image/jpg;', '', $image);
$image = str_replace('data:image/png;', '', $image);
$image = str_replace(' ', '+', $image);
$image= explode(",",$image);
$files='';
$file='';
$images='';
$data='';
$success='';
$images=$image;
$data = base64_decode($images);
$file = UPLOAD_DIR.uniqid().'.jpg';
$success = file_put_contents($file, $data); 
$files=str_replace("../../public_html/child_care_images/","",$file).',';
}
else
{
$files='';
}
$date = date('Y-m-d H:i:s');
$insert= mysqli_query($hconn,"INSERT INTO `baby_profile`(`user_id`, `baby_name`, `boy_girl`, `baby_profile_image`, `baby_birthday`, `baby_birth_place`, `baby_mom_dad`, `date`) VALUES ('$user_id', '$baby_name', '$boy_girl', '$files', '$baby_birthday', '$baby_birth_place', '$baby_mom_dad', '$date')");
if($insert)
{
$status='1';
$msg='success';
$result = array('status'=>$status,'msg'=>$msg);
echo json_encode($result);
mysqli_close($hconn); 
}
else
{
$status='0';
$msg='failure';
$result = array('status'=>$status,'msg'=>$msg);
echo json_encode($result);
mysqli_close($hconn);
}
}
else
{
$status='0';
$msg='User not found';
$result = array('status'=>$status,'msg'=>$msg);
echo json_encode($result);
mysqli_close($hconn);
}
}
else
{
$status='0';
$msg='Values Blank';
$result = array('status'=>$status,'msg'=>$msg);
echo json_encode($result);
mysqli_close($hconn);
} 
}
else
{
$status='0';
$msg='Post Method Error!';
$result = array('status'=>$status,'msg'=>$msg);
echo json_encode($result);
mysqli_close($hconn);
}
?>
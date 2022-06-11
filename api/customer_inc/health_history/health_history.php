<?php 
require_once("../../config.php");
$result = array();

if(isset($_POST['user_id']) && isset($_POST['patient_name']) && isset($_POST['document_name']) && isset($_POST['description']))
{
$user_id = $_POST['user_id'];
$patient_name = $_POST['patient_name'];
$document_name = $_POST['document_name'];
$document_date= $_POST['document_date'];
$description = $_POST['description'];
date_default_timezone_set('Asia/Kolkata');
$date=date('Y-m-d');
$image='';
$image=$_POST['image'];
define('UPLOAD_DIR', '../../../public_html/health_history_images/');

if($user_id!='' && $patient_name!='' && $document_name!='')
{
if($image!='')
{
$image = str_replace('data:image/jpeg;', '', $image);
$image = str_replace('data:image/jpg;', '', $image);
$image = str_replace('data:image/png;', '', $image);
$image = str_replace(' ', '+', $image);
$image= explode(",",$image);
$cnt=count($image);
$files='';
for($i=0;$i<$cnt;$i++)
{ 
$file='';
$images='';
$data='';
$success='';
$images=$image[$i];
$data = base64_decode($images);
$file = UPLOAD_DIR.uniqid().'.jpg';
$success = file_put_contents($file, $data); 
$files.=str_replace("../../../public_html/health_history_images/","",$file).',';
}
}
else
{
$files='';
}


$insert= mysqli_query($connection,"INSERT INTO `health_history`(`user_id`, `patient_name`, `document_name`,`document_date`, `description`, `image`,`date`) VALUES ('$user_id', '$patient_name', '$document_name','$document_date', '$description', '$files', '$date')");
}
  

$status='1';
$msg='Health History Uploaded Successfully';
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
?>
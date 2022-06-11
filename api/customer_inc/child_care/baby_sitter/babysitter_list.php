<?php 
require_once("../../../config.php");
$doctor_array =array(); 
if(isset($_POST['map_location']))
{
$map_location=$_POST['map_location'];

if($map_location!='')
{
$sql = "SELECT * FROM `doctor_list`";
$res = mysqli_query($connection,$sql); 
$count = mysqli_num_rows($res);
if($count>0)
{
$true_false='true';
while($row = mysqli_fetch_array($res))
{
$true_false='true';
$doctor_id=$row['id'];
$doctor_name=$row['doctor_name'];
$about_us=$row['about_us'];

$address=$row['address'];
$telephone=$row['telephone'];
$experience=$row['experience'];
$qualification=$row['qualification'];
$location=$row['location'];
$days=$row['days'];
$timing=$row['timing'];
$image=$row['image'];
$speciality=$row['speciality'];
$rating=$row['rating'];
$medical_affiliation=$row['medical_affiliation'];
$charitable_affiliation=$row['charitable_affiliation'];
$awards_recognition=$row['awards_recognition'];

$review_query = mysqli_query($conn,"SELECT id FROM `babysitter_review` where product_id='$doctor_id'");
$review_count = mysqli_num_rows($review_query);

$image='https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/'.$image;

$doctor_array[]=array('doctor_id'=>$doctor_id,'doctor_name'=>$doctor_name,'about_us'=>$about_us,'speciality'=>$speciality,'medical_affiliation'=>$medical_affiliation,'charitable_affiliation'=>$charitable_affiliation,'awards_recognition'=>$awards_recognition,'address'=>$address,'telephone'=>$telephone,'rating'=>$rating,'review'=>$review_count,'qualification'=>$qualification,'experience'=>$experience,'location'=>$location,'days'=>$days,'timing'=>$timing,'image'=>$image);
}
$json = array("status" => 1,"msg" => "success","count"=>sizeof($doctor_array),"data" => $doctor_array); 
}
else
{
$json = array("status" => 0, "msg" => "Doctor list not found");
}
}
else
{
$json = array("status" => 0, "msg" => "Doctor list not found"); 
}
}
else
{
$json = array("status" => 0, "msg" => "Doctor list not found");
}
@mysqli_close($conn);
header('Content-type: application/json');
echo json_encode($json);
?>
<?php 
require_once("../../../config.php");
$category_array =array(); 
if(isset($_POST['id']))
{
$sql = "SELECT kamasutra_position_list.id,kamasutra_position_list.position_name,kamasutra_position_list.position_tag,kamasutra_position_list.position_description,kamasutra_position_list.image,kamasutra_position_list.regular_gif_image,kamasutra_position_list.wild_gif_image,kamasutra_category.category,kamasutra_category.id AS category_id  FROM `kamasutra_position_list` INNER JOIN kamasutra_category ON kamasutra_position_list.position_category=kamasutra_category.id order by kamasutra_position_list.id ASC ";
$res = mysqli_query($connection,$sql); 
$count = mysqli_num_rows($res);
if($count>0)
{
$true_false='true';
while($row = mysqli_fetch_array($res))
{
$true_false='true';
$postion_id=$row['id'];
$category_id=$row['category_id'];
$category_name=$row['category'];
$position_name=$row['position_name'];
$position_tag=$row['position_tag'];
$position_description=$row['position_description'];
$position_description=strip_tags($position_description);	
$position_description=html_entity_decode($position_description);
$position_description=htmlspecialchars($position_description);
$image='https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/kamasutra_images/'.$row['image'];
$regular_gif_image='https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/kamasutra_images/'.$row['regular_gif_image'];
$wild_gif_image='https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/kamasutra_images/'.$row['wild_gif_image'];


$category_array[]=array('category_id'=>$category_id,'postion_id'=>$postion_id,'category_name'=>$category_name,'position_name'=>$position_name,'position_tag'=>$position_tag,'position_description'=>$position_description,'image'=>$image,'regular_gif_image'=>$regular_gif_image,'wild_gif_image'=>$wild_gif_image);
}
$json = array("status" => 1,"msg" => "success","count"=>sizeof($category_array),"data" => $category_array); 
}
else
{
$json = array("status" => 0, "msg" => "Category list not found");
}
}
else
{
$json = array("status" => 0, "msg" => "Category list not found");
}
@mysqli_close($conn);
header('Content-type: application/json');
echo json_encode($json);
?>
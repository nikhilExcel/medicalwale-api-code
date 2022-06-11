<?php
require_once("../../config.php");
$articlelist = array();
$sql = mysqli_query($connection,"SELECT * FROM `article` WHERE is_active='1' order by id asc");
$count = mysqli_num_rows($sql);
if($count>0){
    
    while($rows = mysqli_fetch_array($sql)){
        
        $article_id=$rows['id'];
        $article_title=$rows['article_title'];
        $article_description=$rows['article_description'];
        $article_date=$rows['posted'];
        $article_image='https://d2c8oti4is0ms3.cloudfront.net/images/article_images/'.$rows['image'];
        $articlelist[] = array('article_id'=>$article_id,'article_title'=>$article_title,'article_description'=>$article_description,'article_image'=>$article_image,'article_date'=>$article_date);
    
    }
    
    $json = array('status'=>1,'msg'=>'success','count'=>sizeof($articlelist),'data'=>$articlelist);
    
}
else{
    
    $json = array('status'=>0,'msg'=>'article list not found');
    
}

@mysqli_close($connection);
header('Content-type: application/json');
echo json_encode($json);
?>
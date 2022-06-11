<?php

require_once ("../../config.php");
$articlelist = array();
$sql = "SELECT * FROM `article` WHERE is_active='1' order by id asc";
$res = mysqli_query($connection, $sql);
$count = mysqli_num_rows($res);
$result1 = array();
$result2 = array();
if ($count > 0) {
    $true_false = 'true';
    while ($row = mysqli_fetch_array($res)) {
        $true_false = 'true';
        $article_id = $row['id'];
        $article_title = $row['article_title'];
        $article_description = $row['article_description'];
        $article_date = $row['posted'];
        $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $row['image'];
        $articlelist[] = array('article_id' => $article_id, 'article_title' => $article_title, 'article_description' => $article_description, 'article_image' => $article_image, 'article_date' => $article_date);
    }
    $json = array("status" => 1, "msg" => "success", "count" => sizeof($articlelist), "data" => $articlelist);
} else {
    $json = array("status" => 0, "msg" => "article list not found");
}
@mysqli_close($connection);
header('Content-type: application/json');
echo json_encode($json);
?>
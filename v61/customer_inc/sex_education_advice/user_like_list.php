<?php

// Include confi.php
include_once('../../config.php');

if (isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];

    $resultpost = array();
    $likeQuery = mysqli_query($conn, "select sex_education_character.type,sex_education_likes.user_id,sex_education_ask_expert.name,sex_education_character.image from sex_education_likes INNER JOIN sex_education_ask_expert on sex_education_ask_expert.user_id=sex_education_likes.user_id INNER JOIN sex_education_character on sex_education_character.id=sex_education_ask_expert.image where sex_education_likes.post_id='$post_id'");
    $like_count = mysqli_num_rows($likeQuery);
    if ($like_count > 0) {
        while ($row = mysqli_fetch_array($likeQuery)) {
            extract($row);
            $user_id = $user_id;
            $name = $name;
            $image = $image;
            $type = $type;
            if ($type == 'men') {
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/character_men/' . $image;
            } else {
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/character_women/' . $image;
            }


            $resultpost[] = array('user_id' => $user_id, 'name' => $name, 'image' => $image);
        }
        $json = array("status" => 1, "msg" => "success", "count" => sizeof($resultpost), "data" => $resultpost);
    } else {
        $json = array("status" => 0, "msg" => "list not found");
    }
} else {
    $json = array("status" => 0, "msg" => "list not found");
}

@mysqli_close($conn);
/* Output header */
header('Content-type: application/json');
echo json_encode($json);
?>

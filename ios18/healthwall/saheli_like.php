<?php

if (isset($_POST['post_id'])) {
    $result = array();
    require_once("../config.php");
    date_default_timezone_set('Asia/Calcutta');
    $created_at = date('Y-m-d H:i:s');
    $post_id = addslashes($_POST['post_id']);
    $post_user_id = addslashes($_POST['post_user_id']);

    if ($post_id != '') {
        for ($i = 3680; $i <= 3705; $i++) {
            $sql = "SELECT id FROM `ask_saheli_likes` WHERE user_id='$i' and post_id='$post_id'";
            $res = mysqli_query($hconnection, $sql);
            $count = mysqli_num_rows($res);
            if ($count > 0) {
                
            } else {
                $media = mysqli_query($hconnection, "INSERT INTO `ask_saheli_likes`(`post_id`, `user_id`,`user_image`,`user_name`) VALUES ('$post_id', '$i', '7', 'Damini')");
            }
        }
        if ($media) {
            echo json_encode(array('status' => 200, 'message' => 'success'));
        } else {
            echo json_encode(array('status' => 204, 'message' => 'fail1'));
        }
    } else {
        echo json_encode(array(
            'status' => 204,
            'message' => 'fail2'
        ));
    }
} else {
    echo json_encode(array(
        'status' => 204,
        'message' => 'fail3'
    ));
}
mysqli_close($connection);
?>
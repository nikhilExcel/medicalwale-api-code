<?php

if (isset($_POST['post_id'])) {
    $result = array();
    require_once("../config.php");
    date_default_timezone_set('Asia/Calcutta');
    $created_at = date('Y-m-d H:i:s');
    $post_id = addslashes($_POST['post_id']);
    $post_user_id = addslashes($_POST['post_user_id']);

    if ($post_id != '') {
        //$user_id='3634';
        for ($i = 3680; $i <= 3760; $i++) {
            $sql = "SELECT id FROM `post_follows` WHERE user_id='$i' and post_id='$post_id'";
            $res = mysqli_query($hconnection, $sql);
            $count = mysqli_num_rows($res);
            if ($count > 0) {
                
            } else {
                $media = mysqli_query($hconnection, "INSERT INTO `post_follows`(`post_id`, `user_id`, `created_at`, `updated_at`) VALUES ('$post_id', '$i', '$created_at', '$created_at')");
            }
        }
        if ($media) {
            echo json_encode(array('status' => 200, 'message' => 'success'));
        } else {
            echo json_encode(array('status' => 204, 'message' => 'fail1'));
        }
    } else {
        echo json_encode(array('status' => 204, 'message' => 'fail2'));
    }
} else {
    echo json_encode(array('status' => 204, 'message' => 'fail3'));
}
mysqli_close($connection);
?>
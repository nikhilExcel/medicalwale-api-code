<?php

$result = array();
require_once ("../../config.php");
if (isset($_POST['user_id']) && isset($_POST['survival_stories_id'])) {
    $user_id = $_POST['user_id'];
    $survival_stories_id = $_POST['survival_stories_id'];
    if ($user_id != '' && $survival_stories_id != '') {
        $insert = mysqli_query($connection, "INSERT INTO `survival_stories_views`(`user_id`, `survival_stories_id`) VALUES ('$user_id','$survival_stories_id')");
        $status = '200';
        $msg = 'success';
        $result = array('status' => 200, 'msg' => $msg);
        echo json_encode($result);
        mysqli_close($connection);
    } else {
        $status = '400';
        $msg = 'Post Method Error!';
        $result = array('status' => 400, 'msg' => $msg);
        echo json_encode($result);
        mysqli_close($connection);
    }
} else {
    $status = '400';
    $msg = 'Post Method Error!';
    $result = array('status' => 400, 'msg' => $msg);
    echo json_encode($result);
    mysqli_close($connection);
}
?>
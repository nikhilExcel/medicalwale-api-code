<?php

require_once("../../config.php");
$result = array();

if (isset($_POST['user_id']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['description'])) {
    $user_id = $_POST['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $tag = $_POST['tag'];
    date_default_timezone_set('Asia/Kolkata');
    $date = date('Y-m-d');
    $image = '';
    $image = $_POST['image'];
    define('UPLOAD_DIR', '../../../public_html/survival_story_images/');

    if ($user_id != '' && $title != '' && $image != '') {
        $sql = "SELECT id,name FROM `timelines` WHERE id='$user_id' limit 1";
        $res = mysqli_query($hconnection, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {
            $res_name = mysqli_fetch_array($res);
            $author = $res_name['name'];
            if ($image != '') {
                $image = str_replace('data:image/jpeg;', '', $image);
                $image = str_replace('data:image/jpg;', '', $image);
                $image = str_replace('data:image/png;', '', $image);
                $image = str_replace(' ', '+', $image);
                $image = explode(",", $image);
                $cnt = count($image);
                $files = '';
                for ($i = 0; $i < $cnt; $i++) {
                    $file = '';
                    $images = '';
                    $data = '';
                    $success = '';
                    $images = $image[$i];
                    $data = base64_decode($images);
                    $file = UPLOAD_DIR . uniqid() . '.jpg';
                    $success = file_put_contents($file, $data);
                    $files .= str_replace("../../../public_html/survival_story_images/", "", $file) . ',';
                }
            } else {
                $files = '';
            }
            $insert = mysqli_query($connection, "INSERT INTO `survival_stories`(`tag`, `title`, `description`, `author`, `user_id`, `image`, `date`) VALUES ('$tag', '$title', '$description', '$author', '$user_id', '$files', '$date')");
            $status = '1';
            $msg = 'Story Uploaded Successfully';
            array_push($result, array('data' => $result));
            $arry = array('status' => $status, 'msg' => $msg);
            echo json_encode($arry);
            mysqli_close($connection);
        } else {
            $status = '0';
            $msg = 'Please enter all fields!';
            $result = '';
            $arry = array('status' => $status, 'msg' => $msg, 'data' => $result);
            echo json_encode($arry);
            mysqli_close($connection);
        }
    } else {
        $status = '0';
        $msg = 'Please enter all fields!';
        $result = '';
        $arry = array('status' => $status, 'msg' => $msg, 'data' => $result);
        echo json_encode($arry);
        mysqli_close($connection);
    }
} else {
    $status = '0';
    $msg = 'Please enter all fields!';
    $result = '';
    $arry = array('status' => $status, 'msg' => $msg, 'data' => $result);
    echo json_encode($arry);
    mysqli_close($connection);
}
?>
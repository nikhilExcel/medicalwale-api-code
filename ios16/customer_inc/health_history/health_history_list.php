<?php

require_once("../../config.php");
$result1 = array();
$result2 = array();
if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $sql = "SELECT * FROM `health_history` WHERE user_id='$user_id' order by id desc";
    $res = mysqli_query($connection, $sql);
    $count = mysqli_num_rows($res);

    if ($count > 0) {
        $true_false = 'true';

        while ($row = mysqli_fetch_array($res)) {

            $true_false = 'true';
            $history_id = $row['id'];
            $patient_name = $row['patient_name'];
            $document_name = $row['document_name'];
            $document_date = $row['document_date'];
            $description = $row['description'];

            $image = $row['image'];

            if ($image != '') {
                $image = explode(",", $image);
                $cnt = count($image);
                $files = '';
                $img_comma = '';
                $images = '';
                if ($cnt > 0) {
                    for ($i = 0; $i < $cnt; $i++) {
                        if ($image[$i] != '') {
                            $images .= $img_comma . 'https://d2c8oti4is0ms3.cloudfront.net/images/health_history_images/' . $image[$i];
                            $img_comma = ',';
                        }
                    }
                } else {
                    $images = 'no';
                }
            } else {
                $images = 'no';
            }

            array_push($result2, array('history_id' => $history_id, 'patient_name' => $patient_name, 'document_name' => $document_name, 'document_date' => $document_date, 'description' => $description, 'image' => $images));
        }
        array_push($result1, array('true_false' => $true_false));
        $arry = array(array('true_false' => $true_false), $result2);
        echo json_encode($arry);
        mysqli_close($connection);
    } else {
        $error_msg = 'No History List';
        $true_false = 'false';
        array_push($result1, array('true_false' => $true_false, 'error_msg' => $error_msg));
        echo json_encode($result1);
        mysqli_close($connection);
    }
} else {
    $error_msg = 'No History List';
    $true_false = 'false';
    array_push($result1, array('true_false' => $true_false, 'error_msg' => $error_msg));
    echo json_encode($result1);
    mysqli_close($connection);
}
?>
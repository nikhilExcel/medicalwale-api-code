<?php

require_once("config.php");
$result1 = array();
$result2 = array();

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    $sql = "SELECT prescription_order.id,prescription_order.user_id,prescription_order.date,prescription_order_details.order_id,prescription_order_details.prescription_image,prescription_order_details.prescription_image2,prescription_order_details.prescription_image3,prescription_order_details.prescription_image4 
FROM `prescription_order` INNER JOIN `prescription_order_details` ON prescription_order.id=prescription_order_details.order_id 
WHERE prescription_order.user_id='$user_id' order by  prescription_order.id desc";
    $res = mysqli_query($connection, $sql);
    $count = mysqli_num_rows($res);

    if ($count > 0) {
        $true_false = 'true';

        while ($row = mysqli_fetch_array($res)) {

            $true_false = 'true';
            $order_id = $row['id'];
            $order_date = $row['date'];

//1

            if ($row['prescription_image'] != '') {
                $image_1 = $row['prescription_image'];
                $prescription_image = str_replace("images/", "", "$image_1");
                $image1 = 'https://d2c8oti4is0ms3.cloudfront.net/images/inc/images/' . $prescription_image;
            } else {
                $image1 = '';
            }

//2
            if ($row['prescription_image2'] != '') {
                $image_2 = $row['prescription_image2'];
                $prescription_image2 = str_replace("images/", "", "$image_2");
                $image2 = 'https://d2c8oti4is0ms3.cloudfront.net/images/inc/images/' . $prescription_image2;
            } else {
                $image2 = '';
            }

//3
            if ($row['prescription_image3'] != '') {
                $image_3 = $row['prescription_image3'];
                $prescription_image3 = str_replace("images/", "", "$image_3");
                $image3 = 'https://d2c8oti4is0ms3.cloudfront.net/images/inc/images/' . $prescription_image3;
            } else {
                $image3 = '';
            }

//4
            if ($row['prescription_image4'] != '') {
                $image_4 = $row['prescription_image4'];
                $prescription_image4 = str_replace("images/", "", "$image_4");
                $image4 = 'https://d2c8oti4is0ms3.cloudfront.net/images/inc/images/' . $prescription_image4;
            } else {
                $image4 = '';
            }



            array_push($result2, array('order_id' => $order_id, 'order_date' => $order_date, 'prescription_image' => $image1, 'prescription_image2' => $image2, 'prescription_image3' => $image3, 'prescription_image4' => $image4));
        }
        array_push($result1, array('true_false' => $true_false));
        $arry = array(array('true_false' => $true_false), $result2);
        echo json_encode($arry);
        mysqli_close($connection);
    } else {
        $error_msg = 'No Prescription List';
        $true_false = 'false';
        array_push($result1, array('true_false' => $true_false, 'error_msg' => $error_msg));
        echo json_encode($result1);
        mysqli_close($connection);
    }
} else {
    $error_msg = 'No Prescription List';
    $true_false = 'false';
    array_push($result1, array('true_false' => $true_false, 'error_msg' => $error_msg));
    echo json_encode($result1);
    mysqli_close($connection);
}
?>
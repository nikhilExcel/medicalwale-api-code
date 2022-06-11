<?php

require_once("config.php");
$result1 = array();
$result2 = array();
if (isset($_POST['user_id']) && ($_POST['order_id'])) {
    $user_id = $_POST['user_id'];
    $order_id = $_POST['order_id'];

    $sql = "SELECT cart_order.uni_id,cart_order.date,cart_order.status,medical_stores.medical_name,GROUP_CONCAT(product.product_name) AS product_name,GROUP_CONCAT(product.product_price) AS product_price,GROUP_CONCAT(cart_order_products.product_quantity) AS product_quantity,product.is_active,IFNULL(oc_address.address_id,'') AS address_id,IFNULL(oc_address.customer_id,'') AS customer_id,IFNULL(oc_address.firstname,'') AS firstname,IFNULL(oc_address.lastname,'') AS lastname,IFNULL(oc_address.email,'') AS email,IFNULL(oc_address.telephone,'') AS telephone,IFNULL(oc_address.lastname,'') AS lastname,IFNULL(oc_address.address_1,'') AS address_1,IFNULL(oc_address.address_2,'') AS address_2
            FROM `cart_order` 
            INNER JOIN `cart_order_products` 
            ON cart_order.id=cart_order_products.order_id 
            INNER JOIN product 
            ON product.id=cart_order_products.product_id 
            INNER JOIN medical_stores
            ON medical_stores.id=cart_order_products.medical_id 
            LEFT JOIN `oc_address`
            ON oc_address.address_id=cart_order.address_id
            WHERE cart_order.user_id='$user_id' AND cart_order.id='$order_id'";
    $res = mysqli_query($connection, $sql);
    $count = mysqli_num_rows($res);
    if ($count > 0) {
        $true_false = 'true';

        while ($row = mysqli_fetch_array($res)) {

            $true_false = 'true';
            $order_no = $row['uni_id'];
            $order_date = $row['date'];
            $order_status = $row['status'];
            $medical_name = $row['medical_name'];
            $product_name = $row['product_name'];
            $product_price = $row['product_price'];
            $product_quantity = $row['product_quantity'];
//$image=$row['image'];
            $firstname = $row['firstname'];
            $lastname = $row['lastname'];
            $addr_patient_name = $firstname . ' ' . $lastname;
            $addr_address1 = $row['address_1'];
            $addr_address2 = $row['address_2'];
            $addr_landmark = $row['landmark'];
            $addr_mobile = $row['telephone'];
            $is_active = $row['is_active'];

            if ($is_active == 1) {
                $product_availability = 'Available';
            } else {
                $product_availability = 'Not Available';
            }
            array_push($result2, array('order_no' => $order_no, 'order_date' => $order_date, 'order_status' => $order_status, 'medical_name' => $medical_name, 'product_name' => $product_name, 'product_price' => $product_price, 'product_quantity' => $product_quantity, 'product_availability' => $product_availability, 'addr_patient_name' => $addr_patient_name, 'addr_address1' => $addr_address1, 'addr_address2' => $addr_address2, 'addr_landmark' => $addr_landmark, 'addr_mobile' => $addr_mobile));
        }
        array_push($result1, array('true_false' => $true_false));
        $arry = array(array('true_false' => $true_false), $result2);
        echo json_encode($arry);
        mysqli_close($connection);
    } else {
        $error_msg = 'No Order Details';
        $true_false = 'false';
        array_push($result1, array('true_false' => $true_false, 'error_msg' => $error_msg));
        echo json_encode($result1);
        mysqli_close($connection);
    }
} else {
    $error_msg = 'No Order Details';
    $true_false = 'false';
    array_push($result1, array('true_false' => $true_false, 'error_msg' => $error_msg));
    echo json_encode($result1);
    mysqli_close($connection);
}
?>
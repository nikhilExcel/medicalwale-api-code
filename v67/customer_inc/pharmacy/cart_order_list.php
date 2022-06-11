<?php

require_once("config.php");
$resultpost = array();
if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $listing_type = $_POST['listing_type'];

    if ($listing_type != 'all') {
        $res = mysqli_query($connection, "select order_id,order_type,delivery_time,listing_id,cancel_reason,action_by,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where user_id='$user_id' and listing_type='$listing_type' order by order_id desc");
    } else {
        $res = mysqli_query($connection, "select order_id,order_type,delivery_time,listing_id,cancel_reason,action_by,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where user_id='$user_id' order by order_id desc");
    }
    $count = mysqli_num_rows($res);
    if ($count > 0) {
        while ($row = mysqli_fetch_array($res)) {
            $order_id = $row['order_id'];
            $delivery_time = $row['delivery_time'];
            $order_type = $row['order_type'];
            $listing_id = $row['listing_id'];
            $listing_name = $row['listing_name'];
            $listing_type = $row['listing_type'];
            $invoice_no = $row['invoice_no'];
            $chat_id = $row['chat_id'];
            $address_id = $row['address_id'];
            $name = $row['name'];
            $mobile = $row['mobile'];
            $pincode = $row['pincode'];
            $address1 = $row['address1'];
            $address2 = $row['address2'];
            $landmark = $row['landmark'];
            $city = $row['city'];
            $state = $row['state'];
            $delivery_charge = $row['delivery_charge'];
            $payment_method = $row['payment_method'];
            $order_date = $row['order_date'];
            $order_date = date('j M Y h:i A', strtotime($order_date));
            $order_status = $row['order_status'];
            $order_type = $row['order_type'];
            $action_by = $row['action_by'];
            if ($action_by == 'vendor') {
                $cancel_reason = $row['cancel_reason'];
            } else {
                $cancel_reason = '';
            }
            $product_resultpost = array();
            $prescription_result = array();

            if ($order_type == 'order') {
                $order_total = '0';
                $product_query = mysqli_query($connection, "select id as product_order_id,product_unit,product_unit_value,product_id,product_name,product_img,product_quantity,product_discount,product_price,sub_total,product_status,product_status_type,product_status_value,order_status from user_order_product where order_id='$order_id' order by product_order_id asc");
                $product_count = mysqli_num_rows($product_query);
                if ($product_count > 0) {
                    while ($product_row = mysqli_fetch_array($product_query)) {
                        $product_order_id = $product_row['product_order_id'];
                        $product_id = $product_row['product_id'];
                        $product_name = $product_row['product_name'];
                        $product_img = $product_row['product_img'];
                        $product_quantity = $product_row['product_quantity'];
                        $product_discount = $product_row['product_discount'];
                        $product_price = $product_row['product_price'];
                        $product_unit = $product_row['product_unit'];
                        $product_unit_value = $product_row['product_unit_value'];
                        $sub_total = $product_row['sub_total'];
                        $product_status = $product_row['product_status'];
                        $product_status_type = $product_row['product_status_type'];
                        $product_status_value = $product_row['product_status_value'];
                        $product_order_status = $product_row['order_status'];

                        $order_total = $order_total + ($product_quantity * $product_price);

                        $product_resultpost[] = array(
                            "product_order_id" => $product_order_id,
                            "product_id" => $product_id,
                            "product_name" => $product_name,
                            "product_img" => $product_img,
                            "product_quantity" => $product_quantity,
                            "product_price" => $product_price,
                            "product_unit" => $product_unit,
                            "product_unit_value" => $product_unit_value,
                            "product_discount" => $product_discount,
                            "sub_total" => $sub_total,
                            "product_status" => $product_status,
                            "product_status_type" => $product_status_type,
                            "product_status_value" => $product_status_value,
                            "product_order_status" => $product_order_status
                        );
                    }
                }
            } else {

                $product_query = mysqli_query($connection, "SELECT id as product_order_id, order_status,prescription_image,prescription_image2,prescription_image3,prescription_image4 FROM prescription_order_details WHERE order_id='$order_id' order by product_order_id asc");

                $product_count = mysqli_num_rows($product_query);
                if ($product_count > 0) {
                    while ($product_row = mysqli_fetch_array($product_query)) {
                        $product_order_id = $product_row['product_order_id'];
                        $product_id = $product_row['product_order_id'];
                        $product_name = '';
                        $prescription_image = '';
                        $prescription_image2 = '';
                        $prescription_image3 = '';
                        $prescription_image4 = '';
                        $prescription1 = $product_row['prescription_image'];
                        $prescription2 = $product_row['prescription_image2'];
                        $prescription3 = $product_row['prescription_image3'];
                        $prescription4 = $product_row['prescription_image4'];

                        if ($prescription1 != '') {
                            $prescription_image = $prescription1 . ',';
                        }
                        if ($prescription2 != '') {
                            $prescription_image2 = $prescription2 . ',';
                        }
                        if ($prescription3 != '') {
                            $prescription_image3 = $prescription3 . ',';
                        }
                        if ($prescription4 != '') {
                            $prescription_image4 = $prescription4;
                        }

                        $product_img = $prescription_image . $prescription_image2 . $prescription_image3 . $prescription_image4;
                        $product_quantity = '';
                        $product_price = '';
                        $sub_total = '';
                        $product_status = '';
                        $product_status_type = '';
                        $product_status_value = '';
                        $product_order_status = $product_row['order_status'];

                        $product_resultpost[] = array(
                            "product_order_id" => $product_order_id,
                            "product_id" => $product_id,
                            "product_name" => $product_name,
                            "product_img" => $product_img,
                            "product_quantity" => $product_quantity,
                            "product_price" => $product_price,
                            "product_discount" => '0',
                            "sub_total" => $sub_total,
                            "product_status" => $product_status,
                            "product_status_type" => $product_status_type,
                            "product_status_value" => $product_status_value,
                            "product_order_status" => $product_order_status
                        );
                    }


                    $prescription_query = mysqli_query($connection, "SELECT * FROM `prescription_order_list` where order_id='$order_id' order by id desc");
                    $prescription_count = mysqli_num_rows($prescription_query);
                    if ($prescription_count > 0) {
                        while ($prescription_row = mysqli_fetch_array($prescription_query)) {

                            $prescription_name = $prescription_row['prescription_name'];
                            $prescription_quantity = $prescription_row['prescription_quantity'];
                            $prescription_price = $prescription_row['prescription_price'];
                            $prescription_discount = $prescription_row['prescription_discount'];
                            $prescription_status = $prescription_row['prescription_status'];

                            $prescription_result[] = array(
                                "prescription_name" => $prescription_name,
                                "prescription_quantity" => $prescription_quantity,
                                "prescription_price" => $prescription_price,
                                "prescription_discount" => $prescription_discount,
                                "prescription_status" => $prescription_status
                            );
                        }
                    }
                }
            }


            $resultpost[] = array(
                "order_id" => $order_id,
                "delivery_time" => $delivery_time,
                "order_type" => $order_type,
                "listing_id" => $listing_id,
                "listing_name" => $listing_name,
                "listing_type" => $listing_type,
                "invoice_no" => $invoice_no,
                "chat_id" => $chat_id,
                "address_id" => $address_id,
                "name" => $name,
                "mobile" => $mobile,
                "pincode" => $pincode,
                "address1" => $address1,
                "address2" => $address2,
                "landmark" => $landmark,
                "city" => $city,
                "state" => $state,
                "order_total" => $order_total,
                "payment_method" => $payment_method,
                "order_date" => $order_date,
                "order_status" => $order_status,
                "cancel_reason" => $cancel_reason,
                "delivery_charge" => $delivery_charge,
                "product_order" => $product_resultpost,
                "prescription_order" => $prescription_result
            );
        }

        echo json_encode(array("status" => 200, "message" => "success", "count" => sizeof($resultpost), "data" => $resultpost));
        mysqli_close($connection);
    } else {
        echo json_encode(array("status" => 200, "message" => "success", "count" => sizeof($resultpost), "data" => $resultpost));
        mysqli_close($connection);
    }
} else {
    echo json_encode(array("status" => 200, "message" => "success", "count" => sizeof($resultpost), "data" => $resultpost));
    mysqli_close($connection);
}
?>
<?php

include_once('../../../config.php');
if (isset($_POST['id'])) {
    $resultcharacter_men = array();
    $resultcharacter_women = array();
    $resultcharacter_kids = array();
    $resultcharacter_new_arrival = array();
    $resultcharacter_best_seller = array();
    $guarantee_policy = "100% Payment Protection. 7 days easy return in case item is dedective or damaged or different from what was delivered.";

    $optic_store_banner = mysqli_query($conn, "SELECT * FROM `optic_store_banner` limit 1");
    $info = mysqli_fetch_array($optic_store_banner);
    $image_banner = $info['image'];

    $optic_store_men = mysqli_query($conn, "SELECT * FROM `optic_store_category`");
    $optic_store_women = mysqli_query($conn, "SELECT * FROM `optic_store_category`");
    $optic_store_kids = mysqli_query($conn, "SELECT * FROM `optic_store_category` WHERE id='1' OR id='2'");
    $optic_store_new_arrival = mysqli_query($conn, "SELECT * FROM `optic_store_products` WHERE is_newarival='1' order by id desc LIMIT 0,12");
    $optic_store_best_sellers = mysqli_query($conn, "SELECT * FROM `optic_store_products` WHERE is_bestseller='1' order by id desc LIMIT 0,12");


    $optic_store_count1 = mysqli_num_rows($optic_store_men);
    $optic_store_count2 = mysqli_num_rows($optic_store_women);
    $optic_store_count3 = mysqli_num_rows($optic_store_kids);
    $optic_store_count4 = mysqli_num_rows($optic_store_new_arrival);
    $optic_store_count5 = mysqli_num_rows($optic_store_best_sellers);
    $optic_store_count = ($optic_store_count1) + ($optic_store_count2) + ($optic_store_count3) + ($optic_store_count4) + ($optic_store_count5);
    if ($optic_store_count > 0) {
        while ($row1 = mysqli_fetch_array($optic_store_men)) {
            $category_id = $row1['id'];
            $name = $row1['name'];
            $type = 'men';
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/optic_store_images/' . $row1['image'];
            $resultcharacter_men[] = array('category_id' => $category_id, 'name' => $name, 'image' => $image, 'type' => $type);
        }

        while ($row2 = mysqli_fetch_array($optic_store_women)) {
            $category_id = $row2['id'];
            $name = $row2['name'];
            $type = 'women';
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/optic_store_images/' . $row2['image'];
            $resultcharacter_women[] = array('category_id' => $category_id, 'name' => $name, 'image' => $image, 'type' => $type);
        }

        while ($row3 = mysqli_fetch_array($optic_store_kids)) {
            $category_id = $row3['id'];
            $name = $row3['name'];
            $type = 'kids';
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/optic_store_images/' . $row3['image'];
            $resultcharacter_kids[] = array('category_id' => $category_id, 'name' => $name, 'image' => $image, 'type' => $type);
        }

        while ($row4 = mysqli_fetch_array($optic_store_new_arrival)) {
            $product_id = $row4['id'];
            $category_id = $row4['category_id'];
            $product_code = 'OC' . $category_id . '000' . $product_id;
            $name = $row4['product_name'];
            $img = '';
            $img = $row4['image'];
            if ($img != '') {
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/optic_store_images/' . $row4['image'] . ',';
            } else {
                $image = '';
            }
            $price = $row4['price'];
            $availibility = $row4['availibility'];
            $description = "<style>strong{color:#404547;font-weight:400}p{color:#768188;text-align:justify;}</style>" . $row['description'];
            $rating = $row4['rating'];
            $specification = $row4['specification'];

            $roots_herbs_product_view_query = mysqli_query($conn, "SELECT id FROM `optic_store_products_view` where product_id='$product_id'");
            $product_view = mysqli_num_rows($roots_herbs_product_view_query);

            $gender = $row4['gender'];
            $resultcharacter_new_arrival[] = array('category_id' => $category_id, 'product_id' => $product_id, 'product_code' => $product_code, 'name' => $name, 'image' => $image, 'description' => $description, 'availibility' => $availibility, 'price' => $price, 'rating' => $rating, 'review' => $product_view, 'specification' => $specification, 'product_view' => $product_view, 'type' => $gender);
        }
        while ($row5 = mysqli_fetch_array($optic_store_best_sellers)) {
            $product_id = $row5['id'];
            $category_id = $row5['category_id'];
            $product_code = 'OC' . $category_id . '000' . $product_id;
            $name = $row5['product_name'];
            $img = '';
            $img = $row5['image'];
            if ($img != '') {
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/optic_store_images/' . $row5['image'] . ',';
            } else {
                $image = '';
            }
            $price = $row5['price'];
            $availibility = $row5['availibility'];
            $description = "<style>strong{color:#404547;font-weight:400}p{color:#768188;text-align:justify;}</style>" . $row['description'];
            $rating = $row5['rating'];
            $specification = $row5['specification'];

            $roots_herbs_product_view_query = mysqli_query($conn, "SELECT id FROM `optic_store_products_view` where product_id='$product_id'");
            $product_view = mysqli_num_rows($roots_herbs_product_view_query);

            $gender = $row5['gender'];
            $resultcharacter_best_seller[] = array('category_id' => $category_id, 'product_id' => $product_id, 'product_code' => $product_code, 'name' => $name, 'image' => $image, 'description' => $description, 'availibility' => $availibility, 'price' => $price, 'rating' => $rating, 'review' => $product_view, 'specification' => $specification, 'product_view' => $product_view, 'type' => $gender);
        }


        $json = array("status" => 1, "msg" => "success", "guarantee_policy" => $guarantee_policy, "count_men" => sizeof($resultcharacter_men), "count_women" => sizeof($resultcharacter_women), "count_kids" => sizeof($resultcharacter_kids), "count_new_arrival" => sizeof($resultcharacter_new_arrival), "count_best_seller" => sizeof($resultcharacter_best_seller), "image_banner" => $image_banner, "data_men" => $resultcharacter_men, "data_women" => $resultcharacter_women, "data_kids" => $resultcharacter_kids, "data_new_arrival" => $resultcharacter_new_arrival, "data_best_seller" => $resultcharacter_best_seller);
    } else {
        $json = array("status" => 0, "msg" => "Data not found");
    }
} else {
    $json = array("status" => 0, "msg" => "Data not found");
}

@mysqli_close($conn);
/* Output header */
header('Content-type: application/json');
echo json_encode($json);
?>

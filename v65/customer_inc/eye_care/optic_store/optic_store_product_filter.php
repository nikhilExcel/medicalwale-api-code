<?php

include_once('../../../config.php');
if (isset($_POST['category_id'])) {

    $resultcharacter = array();
    $category_id = $_POST['category_id'];
    $type = $_POST['type'];
    $guarantee_policy = "100% Payment Protection. 7 days easy return in case item is dedective or damaged or different from what was delivered.";

    $rows = "SELECT optic_store_products.category_id,optic_store_products.gender,optic_store_products.id,optic_store_products.product_name,optic_store_products.image,optic_store_products.price,optic_store_products.description,optic_store_products.rating,optic_store_products.specification,optic_store_products.availibility,optic_store_products.gender,optic_store_products.style,optic_store_products.brand,optic_store_products.size FROM `optic_store_category` INNER JOIN `optic_store_products` ON optic_store_category.id=optic_store_products.category_id WHERE optic_store_products.category_id='$category_id' and optic_store_products.gender='$type' ";

    $price_json = '{"inbox":' . $_POST['price'] . '}';
    $price_data = json_decode($price_json);
    if (isset($_POST['price']) && $_POST['price'] != "") :
        foreach ($price_data->inbox as $price) {
            $from = $price->from;
            $to = $price->to;
            $price_query .= "optic_store_products.price>='$from' and optic_store_products.price<='$to'";
        }
        $rows .= " AND $price_query";
    endif;

    $size_json = '{"inbox":' . $_POST['size'] . '}';
    $size_data = json_decode($size_json);
    if (isset($_POST['size']) && $_POST['size'] != "") :
        foreach ($size_data->inbox as $size) {
            $size_value = $size->size;
            $size_query .= "optic_store_products.size='$size_value'";
        }
        $rows .= " AND $size_query";
    endif;

    $style_json = '{"inbox":' . $_POST['style'] . '}';
    $style_data = json_decode($style_json);
    if (isset($_POST['style']) && $_POST['style'] != "") :
        foreach ($style_data->inbox as $style) {
            $style_value = $style->style;
            $style_query .= "optic_store_products.style='$style_value'";
        }
        $rows .= " AND $style_query";
    endif;

    $rows .= " order by optic_store_products.id desc";
    $roots_category_commentQuery = mysqli_query($conn, $rows);
    $roots_category_count = mysqli_num_rows($roots_category_commentQuery);
    if ($roots_category_count > 0) {
        while ($row = mysqli_fetch_array($roots_category_commentQuery)) {
            $product_id = $row['id'];
            $category_id = $row['category_id'];
            $product_code = 'OC' . $category_id . '000' . $product_id; //OC-Optic Care + cat_id + 000 sub_cat_id
            $name = $row['product_name'];
            $img = '';
            $img = $row['image'];
            if ($img != '') {
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/optic_store_images/' . $row['image'] . ',';
            } else {
                $image = '';
            }
            $price = $row['price'];
            $availibility = $row['availibility'];
            $description = "<style>strong{color:#404547;font-weight:400}p{color:#768188;text-align:justify;}</style>" . $row['description'];
            //$description=str_replace('–','-',$description);
            //$description=str_replace("’","'",$description);
            //$description= preg_replace('/[\x00-\x1F\x7F]/u', '', $description);	
            //$description=strip_tags($description);	
            //$description=html_entity_decode($description);
            //$description=htmlspecialchars($description);
            $rating = $row['rating'];
            $specification = $row['specification'];
            $gender = $row['gender'];
            //$specification=strip_tags($specification);

            $roots_herbs_product_view_query = mysqli_query($conn, "SELECT id FROM `optic_store_products_view` where product_id='$product_id'");
            $product_view = mysqli_num_rows($roots_herbs_product_view_query);

            $resultcharacter[] = array('category_id' => $category_id, 'product_id' => $product_id, 'product_code' => $product_code, 'name' => $name, 'image' => $image, 'description' => $description, 'availibility' => $availibility, 'price' => $price, 'type' => $gender, 'rating' => $rating, 'review' => $product_view, 'specification' => $specification, 'product_view' => $product_view);
        }
        $json = array("status" => 1, "msg" => "success", "count" => sizeof($resultcharacter), "guarantee_policy" => $guarantee_policy, "data" => $resultcharacter);
    } else {
        $json = array("status" => 0, "msg" => "Data not found");
    }
} else {
    $json = array("status" => 0, "msg" => "Data not found");
}

@mysqli_close($conn);
header('Content-type: application/json');
echo json_encode($json);
?>

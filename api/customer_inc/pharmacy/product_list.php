<?php
	// Include confi.php
	include_once('config.php');
	

if(isset($_POST['sub_category_id']))
{
    $sub_category_id = $_POST['sub_category_id'];
	if(!empty($sub_category_id)){        
		$resultProduct =array(); 
			
		$productQuery = mysqli_query($conn,"SELECT id as product_id,product_name,product_price,pack FROM `product` WHERE sub_category='$sub_category_id' limit 100");
		$product_count = mysqli_num_rows($productQuery);
		if($product_count>0)
		{
		while($row = mysqli_fetch_array($productQuery))
		{
		extract($row);
		$product_id=$product_id;
		$product_name=$product_name;
		$product_price=$product_price;
		$pack=$pack;
		$product_image=$product_name.'.jpg';
		$product_image=str_replace(' ','%20',$product_image);
		$product_image='https://d2c8oti4is0ms3.cloudfront.net/images/product/'.$product_image;
		$resultProduct[] = array("product_id" => $product_id,
			"product_name"=>$product_name,
			"product_price"=>$product_price,
			'product_weight' => $pack,
			'product_image'=>$product_image,
			); 
		}			
			
		$json = array("status" => 1,"msg" => "success","count"=>sizeof($resultProduct),"data" => $resultProduct);
	}
	else
	{
	$json = array("status" => 0, "msg" => "Product list not found");
	}
	}
	else
	{
		$json = array("status" => 0, "msg" => "Sub category id not define");
	}
	
}else{
	$json = array("status" => 0, "msg" => "Request method not accepted");
}
	@mysqli_close($conn);

	/* Output header */
	header('Content-type: application/json');
	echo json_encode($json);
	?>

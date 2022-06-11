<?php
	// Include confi.php
	include_once('config.php');
	

if(isset($_POST['keyword']))
{
    $keyword = $_POST['keyword'];
	if(!empty($keyword)){        
		$resultProduct =array(); 
			
		$productQuery = mysqli_query($conn,"SELECT id,product_name,pack,product_price,content FROM product WHERE product_name LIKE '%$keyword%' limit 15");
		$product_count = mysqli_num_rows($productQuery);
		if($product_count>0)
		{
		while($row = mysqli_fetch_array($productQuery))
		{
		extract($row);
		$product_id=$id;
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
		$json = array("status" => 0, "msg" => "Product list not found");
	}
	
}else{
	$json = array("status" => 0, "msg" => "Request method not accepted");
}
	@mysqli_close($conn);

	/* Output header */
	header('Content-type: application/json');
	echo json_encode($json);
	?>

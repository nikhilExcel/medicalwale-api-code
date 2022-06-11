<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model
{
    public function search_unmap_distributor_list($user_id,$keyword)
    {
        $perPage=10;
        $query = $this->db->query("SELECT id,user_id,name,city FROM `distributor` where is_delete='0' and name like '%$keyword%' order by name asc");
         
        $count = $query->num_rows();
        $data        = array();
        
        $count_array = array();
        foreach ($query->result_array() as $row) {
            $id           = $row['id'];
            $user_id  = $row['user_id'];
            $name  = $row['name'];
            $city     = $row['city'];
            $data[] = array(
                "id" => $id,
                "did" => 'P000'.$id,
                "user_id" => $user_id,
                "name" => $name,
                "city" => $city
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    public function search_distributor_list($user_id,$keyword)
    {
        $perPage=10;
        $query = $this->db->query("SELECT id,user_id,name,address,landmark,city,state,phone,pincode FROM `distributor` where name like '%$keyword%' order by name asc");
         
        $count = $query->num_rows();
        $data        = array();
        
        $count_array = array();
        foreach ($query->result_array() as $row) {
            $id           = $row['id'];
            $user_id  = $row['user_id'];
            $name  = $row['name'];
            $address     = $row['address'];
            $landmark     = $row['landmark'];
            $phone     = $row['phone'];
            $state     = $row['state'];
            $city     = $row['city'];
            $pincode     = $row['pincode'];
            $data[] = array(
                "id" => $id,
                "did" => 'P000'.$id,
                "user_id" => $user_id,
                "name" => $name,
                "address" => $address,
                "landmark" => $landmark,
                "phone" => $phone,
                "state" => $state,
                "city" => $city,
                "pincode" => $pincode
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    public function distributor_list($user_id)
    {
        $perPage=10;
        $query = $this->db->query("SELECT id,user_id,name,address,landmark,city,state,phone,pincode FROM `distributor` where is_delete='0' order by name asc");
         
        $count = $query->num_rows();
        $data        = array();
        
        $count_array = array();
        foreach ($query->result_array() as $row) {
            $id           = $row['id'];
            $user_id  = $row['user_id'];
            $name  = $row['name'];
            $address     = $row['address'];
            $landmark     = $row['landmark'];
            $phone     = $row['phone'];
            $state     = $row['state'];
            $city     = $row['city'];
            $pincode     = $row['pincode'];
            $data[] = array(
                "id" => $id,
                "did" => 'P000'.$id,
                "user_id" => $user_id,
                "name" => $name,
                "address" => $address,
                "landmark" => $landmark,
                "phone" => $phone,
                "state" => $state,
                "city" => $city,
                "pincode" => $pincode
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    public function unmap_distributor_list($user_id)
    {
        $perPage=10;
        $query = $this->db->query("SELECT id,user_id,name,address,landmark,city,state,phone,pincode FROM `distributor` where is_delete='0' order by name asc limit 3");
         
        $count = $query->num_rows();
        $data        = array();
        
        $count_array = array();
        foreach ($query->result_array() as $row) {
            $id           = $row['id'];
            $user_id  = $row['user_id'];
            $name  = $row['name'];
            $address     = $row['address'];
            $landmark     = $row['landmark'];
            $phone     = $row['phone'];
            $state     = $row['state'];
            $city     = $row['city'];
            $pincode     = $row['pincode'];
            $data[] = array(
                "id" => $id,
                "did" => 'P000'.$id,
                "user_id" => $user_id,
                "name" => $name,
                "address" => $address,
                "landmark" => $landmark,
                "phone" => $phone,
                "state" => $state,
                "city" => $city,
                "pincode" => $pincode
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }

    public function distributor_product_list($user_id,$distributors)
    {
        $perPage=10;
        $query = $this->db->query("SELECT product.id,dstock_70486.user_id,dstock_70486.vendor_type,product.product_name,dstock_70486.sku_code,dstock_70486.ptr,dstock_70486.mrp,dstock_70486.quantity,dstock_70486.selling_price FROM `dstock_70486` JOIN `product` ON `product`.`id`=`dstock_70486`.`product_id` JOIN `vendor_type` ON `vendor_type`.`id`=`dstock_70486`.`vendor_type` WHERE `dstock_70486`.`ptr`>0 ORDER BY rand() LIMIT 10");
         
        $count = $query->num_rows();
        
        $data        = array();
        $count_array = array();
        $selected_distributors = array();
        
        $distributors = explode(',', $distributors);
        foreach ($distributors as $dis_id) {
            $query2 = $this->db->query("SELECT name FROM `distributor` where is_delete='0' and `id` = '$dis_id' limit 1");
            if($query2->num_rows()>0){
                $row2   = $query2->row_array();
                $name  = $row2['name'];
                
                $selected_distributors[] = array(
                    "id" => $dis_id,
                    "name" => $name,
                );
            }
            
        }
        
        foreach ($query->result_array() as $row) {
            $id           = $row['id'];
            $user_id  = $row['user_id'];
            $vendor_type  = $row['vendor_type'];
            $product_name     = $row['product_name'];
            $sku_code = $row['sku_code'];
            $ptr         = $row['ptr'];
            $mrp        = $row['mrp'];
            $quantity      = $row['quantity'];
            $selling_price   = $row['selling_price'];
            
            $query2 = $this->db->query("SELECT name FROM `users` WHERE `id` = '$user_id' limit 1");
            $row2   = $query2->row_array();
            $distributor  = $row2['name'];
            $single_img = 'https://d2c8oti4is0ms3.cloudfront.net/images/website_assets/default_medicalwale.jpg';

            $data[] = array(
                "id" => $id,
                "user_id" => $user_id,
                "vendor_type" => $vendor_type,
                "product_name" => $product_name,
                "sku_code" => $sku_code,
                "ptr" => $ptr,
                "mrp" => $mrp,
                "min_quantity" => $quantity,
                "max_quantity" => 10,
                "quantity" => 1,
                "image" => $single_img,
                "selling_price" => $selling_price,
                "price" => $selling_price,
                "distributor" => $distributor,
            );
        }
		
		
		shuffle($data);
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'selected_distributors' => $selected_distributors,
            'data' => $data
        );
        return $resultpost;
    }

    public function search_product_list($user_id,$keyword)
    {
        $perPage=10;
        $query = $this->db->query("SELECT product.id,dstock_70486.user_id,dstock_70486.vendor_type,product.product_name,dstock_70486.sku_code,dstock_70486.ptr,dstock_70486.mrp,dstock_70486.quantity,dstock_70486.selling_price FROM `dstock_70486` JOIN `product` ON `product`.`id`=`dstock_70486`.`product_id` JOIN `vendor_type` ON `vendor_type`.`id`=`dstock_70486`.`vendor_type` WHERE `dstock_70486`.`selling_price`>0 and dstock_70486.product_name REGEXP '$keyword' ORDER BY rand() LIMIT 10");
         
        $count = $query->num_rows();
        $data        = array();
        
        $count_array = array();
        foreach ($query->result_array() as $row) {
            $id           = $row['id'];
            $user_id  = $row['user_id'];
            $vendor_type  = $row['vendor_type'];
            $product_name     = $row['product_name'];
            $sku_code = $row['sku_code'];
            $ptr         = $row['ptr'];
            $mrp        = $row['mrp'];
            $quantity      = $row['quantity'];
            $selling_price   = $row['selling_price'];
            
            $query2 = $this->db->query("SELECT name FROM `users` WHERE `id` = '$user_id' limit 1");
            $row2   = $query2->row_array();
            $distributor  = $row2['name'];
            
          
            $single_img = 'https://d2c8oti4is0ms3.cloudfront.net/images/website_assets/default_medicalwale.jpg';

            
            $data[] = array(
                "id" => $id,
                "user_id" => $user_id,
                "vendor_type" => $vendor_type,
                "product_name" => $product_name,
                "sku_code" => $sku_code,
                "ptr" => $ptr,
                "mrp" => $mrp,
                "min_quantity" => $quantity,
                "max_quantity" => 10,
                "quantity" => 1,
                "image" => $single_img,
                "selling_price" => $selling_price,
                "price" => $selling_price,
                "distributor" => $distributor,
            );
        }
		
		
		shuffle($data);
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }

    public function hot_deals($user_id,$page)
    {
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        
        $query = $this->db->query("SELECT product.id,dstock_70486.user_id,dstock_70486.vendor_type,product.product_name,dstock_70486.sku_code,dstock_70486.ptr,dstock_70486.mrp,dstock_70486.quantity,dstock_70486.selling_price FROM `dstock_70486` JOIN `product` ON `product`.`id`=`dstock_70486`.`product_id` JOIN `vendor_type` ON `vendor_type`.`id`=`dstock_70486`.`vendor_type` INNER JOIN oc_hot_deals on oc_hot_deals.product_id=product.id WHERE `dstock_70486`.`selling_price`>0  LIMIT $start, $limit");
        
        $count_query = $this->db->query("SELECT product.id FROM `dstock_70486` JOIN `product` ON `product`.`id`=`dstock_70486`.`product_id` JOIN `vendor_type` ON `vendor_type`.`id`=`dstock_70486`.`vendor_type` INNER JOIN oc_hot_deals on oc_hot_deals.product_id=product.id WHERE `dstock_70486`.`selling_price`>0");
        $count = $count_query->num_rows();
        
        $data        = array();
        
        $count_array = array();
        foreach ($query->result_array() as $row) {
            $id           = $row['id'];
            $user_id  = $row['user_id'];
            $vendor_type  = $row['vendor_type'];
            $product_name     = $row['product_name'];
            $sku_code = $row['sku_code'];
            $ptr         = $row['ptr'];
            $mrp        = $row['mrp'];
            $quantity      = $row['quantity'];
            $selling_price   = $row['selling_price'];
            
            $query2 = $this->db->query("SELECT name FROM `users` WHERE `id` = '$user_id' limit 1");
            $row2   = $query2->row_array();
            $distributor  = $row2['name'];
            
          
            $single_img = 'https://d2c8oti4is0ms3.cloudfront.net/images/website_assets/default_medicalwale.jpg';

            
            $data[] = array(
                "id" => $id,
                "user_id" => $user_id,
                "vendor_type" => $vendor_type,
                "product_name" => $product_name,
                "sku_code" => $sku_code,
                "ptr" => $ptr,
                "mrp" => $mrp,
                "min_quantity" => $quantity,
                "max_quantity" => 10,
                "quantity" => 1,
                "image" => $single_img,
                "selling_price" => $selling_price,
                "price" => $selling_price,
                "distributor" => $distributor,
            );
        }
		
		shuffle($data);
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data,
            'count' => $count
        );
        return $resultpost;
    }

    public function frequently_purchased_products($user_id,$page)
    {
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        
        $query = $this->db->query("SELECT product.id,product.image,dstock_70486.user_id,dstock_70486.vendor_type,product.product_name,dstock_70486.sku_code,dstock_70486.ptr,dstock_70486.mrp,dstock_70486.quantity,dstock_70486.selling_price FROM `dstock_70486` JOIN `product` ON `product`.`id`=`dstock_70486`.`product_id` JOIN `vendor_type` ON `vendor_type`.`id`=`dstock_70486`.`vendor_type` WHERE `dstock_70486`.`selling_price`>0 ORDER BY rand()  LIMIT $start, $limit");
        
        $count_query = $this->db->query("SELECT product.id FROM `dstock_70486` JOIN `product` ON `product`.`id`=`dstock_70486`.`product_id` JOIN `vendor_type` ON `vendor_type`.`id`=`dstock_70486`.`vendor_type` WHERE `dstock_70486`.`selling_price`>0");
        $count = $count_query->num_rows();
        
        $data        = array();
        
        $count_array = array();
        foreach ($query->result_array() as $row) {
            $id           = $row['id'];
            $user_id  = $row['user_id'];
            $vendor_type  = $row['vendor_type'];
            $product_name     = $row['product_name'];
            $sku_code = $row['sku_code'];
            $ptr         = $row['ptr'];
            $mrp        = $row['mrp'];
            $quantity      = $row['quantity'];
            $selling_price   = $row['selling_price'];
            
            $query2 = $this->db->query("SELECT name FROM `users` WHERE `id` = '$user_id' limit 1");
            $row2   = $query2->row_array();
            $distributor  = $row2['name'];
            
            $image = $row['image'];
            if($image!=''){
                $single_img = 'https://d2c8oti4is0ms3.cloudfront.net/images/product_images/' . $image;
            }
            else{
                $single_img = 'https://d2c8oti4is0ms3.cloudfront.net/images/website_assets/default_medicalwale.jpg';
            }
            
            $data[] = array(
                "id" => $id,
                "user_id" => $user_id,
                "vendor_type" => $vendor_type,
                "product_name" => $product_name,
                "sku_code" => $sku_code,
                "ptr" => $ptr,
                "mrp" => $mrp,
                "min_quantity" => $quantity,
                "max_quantity" => 10,
                "quantity" => 1,
                "image" => $single_img,
                "selling_price" => $selling_price,
                "price" => $selling_price,
                "distributor" => $distributor,
            );
        }
		
		
		shuffle($data);
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data,
            'count' => $count
        );
        return $resultpost;
    }
    
    public function top_selling_products_in_your_area($user_id)
    {
        $perPage=10;
        $query = $this->db->query("SELECT product.id,product.image,dstock_70486.user_id,dstock_70486.vendor_type,product.product_name,dstock_70486.sku_code,dstock_70486.ptr,dstock_70486.mrp,dstock_70486.quantity,dstock_70486.selling_price FROM `dstock_70486` JOIN `product` ON `product`.`id`=`dstock_70486`.`product_id` JOIN `vendor_type` ON `vendor_type`.`id`=`dstock_70486`.`vendor_type` WHERE `dstock_70486`.`selling_price`>0 ORDER BY rand() LIMIT 10");
         
        $count = $query->num_rows();
        $data        = array();
        
        $count_array = array();
        foreach ($query->result_array() as $row) {
            $id           = $row['id'];
            $user_id  = $row['user_id'];
            $vendor_type  = $row['vendor_type'];
            $product_name     = $row['product_name'];
            $sku_code = $row['sku_code'];
            $ptr         = $row['ptr'];
            $mrp        = $row['mrp'];
            $quantity      = $row['quantity'];
            $selling_price   = $row['selling_price'];
            
            $query2 = $this->db->query("SELECT name FROM `users` WHERE `id` = '$user_id' limit 1");
            $row2   = $query2->row_array();
            $distributor  = $row2['name'];
            
            $image = $row['image'];
            if($image!=''){
                $single_img = 'https://d2c8oti4is0ms3.cloudfront.net/images/product_images/' . $image;
            }
            else{
                $single_img = 'https://d2c8oti4is0ms3.cloudfront.net/images/website_assets/default_medicalwale.jpg';
            }
            
            $data[] = array(
                "id" => $id,
                "user_id" => $user_id,
                "vendor_type" => $vendor_type,
                "product_name" => $product_name,
                "sku_code" => $sku_code,
                "ptr" => $ptr,
                "mrp" => $mrp,
                "min_quantity" => $quantity,
                "max_quantity" => 10,
                "quantity" => 1,
                "image" => $single_img,
                "selling_price" => $selling_price,
                "price" => $selling_price,
                "distributor" => $distributor,
            );
        }
		
		shuffle($data);
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    public function new_apprivals($user_id)
    {
        $perPage=10;
        $query = $this->db->query("SELECT product.id,product.image,dstock_70486.user_id,dstock_70486.vendor_type,product.product_name,dstock_70486.sku_code,dstock_70486.ptr,dstock_70486.mrp,dstock_70486.quantity,dstock_70486.selling_price FROM `dstock_70486` JOIN `product` ON `product`.`id`=`dstock_70486`.`product_id` JOIN `vendor_type` ON `vendor_type`.`id`=`dstock_70486`.`vendor_type` WHERE `dstock_70486`.`selling_price`>0 ORDER BY rand() LIMIT 10");
         
        $count = $query->num_rows();
        $data        = array();
        
        $count_array = array();
        foreach ($query->result_array() as $row) {
            $id           = $row['id'];
            $user_id  = $row['user_id'];
            $vendor_type  = $row['vendor_type'];
            $product_name     = $row['product_name'];
            $sku_code = $row['sku_code'];
            $ptr         = $row['ptr'];
            $mrp        = $row['mrp'];
            $quantity      = $row['quantity'];
            $selling_price   = $row['selling_price'];
            
            $query2 = $this->db->query("SELECT name FROM `users` WHERE `id` = '$user_id' limit 1");
            $row2   = $query2->row_array();
            $distributor  = $row2['name'];
            
            $image = $row['image'];
            if($image!=''){
                $single_img = 'https://d2c8oti4is0ms3.cloudfront.net/images/product_images/' . $image;
            }
            else{
                $single_img = 'https://d2c8oti4is0ms3.cloudfront.net/images/website_assets/default_medicalwale.jpg';
            }
            
            $data[] = array(
                "id" => $id,
                "user_id" => $user_id,
                "vendor_type" => $vendor_type,
                "product_name" => $product_name,
                "sku_code" => $sku_code,
                "ptr" => $ptr,
                "mrp" => $mrp,
                "min_quantity" => $quantity,
                "max_quantity" => 10,
                "quantity" => 1,
                "image" => $single_img,
                "selling_price" => $selling_price,
                "price" => $selling_price,
                "distributor" => $distributor,
            );
        }
		
		shuffle($data);
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
	
    public function deal_product_list($user_id,$page)
    {
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        
        $query = $this->db->query("SELECT oc_deals_of_the_day.end_time,product.id,product.image,dstock_70486.user_id,dstock_70486.vendor_type,product.product_name,dstock_70486.sku_code,dstock_70486.ptr,dstock_70486.mrp,dstock_70486.quantity,dstock_70486.selling_price FROM `dstock_70486` JOIN `product` ON `product`.`id`=`dstock_70486`.`product_id` JOIN `vendor_type` ON `vendor_type`.`id`=`dstock_70486`.`vendor_type` INNER JOIN oc_deals_of_the_day on oc_deals_of_the_day.product_id=product.id WHERE `dstock_70486`.`selling_price`>0 ORDER BY rand()  LIMIT $start, $limit");
        
        $count_query = $this->db->query("SELECT product.id FROM `dstock_70486` JOIN `product` ON `product`.`id`=`dstock_70486`.`product_id` JOIN `vendor_type` ON `vendor_type`.`id`=`dstock_70486`.`vendor_type` INNER JOIN oc_deals_of_the_day on oc_deals_of_the_day.product_id=product.id WHERE `dstock_70486`.`selling_price`>0");
        $count = $count_query->num_rows();
        
        $data        = array();
        
        $count_array = array();
        $end_time='';
        
        $query22 = $this->db->query("SELECT oc_deals_of_the_day.end_time FROM `dstock_70486` JOIN `product` ON `product`.`id`=`dstock_70486`.`product_id` JOIN `vendor_type` ON `vendor_type`.`id`=`dstock_70486`.`vendor_type` INNER JOIN oc_deals_of_the_day on oc_deals_of_the_day.product_id=product.id WHERE `dstock_70486`.`selling_price`>0 ORDER BY oc_deals_of_the_day.id desc LIMIT 1");
        if($query22->num_rows()>0){
            $row3=$query22->row_array();
            $end_time   = $row3['end_time'];
        }
        
        //$end_time='2021-07-20 21:12:00';
        
        
        foreach ($query->result_array() as $row) {
            $id           = $row['id'];
            $user_id  = $row['user_id'];
            $vendor_type  = $row['vendor_type'];
            $product_name     = $row['product_name'];
            $sku_code = $row['sku_code'];
            $ptr         = $row['ptr'];
            $mrp        = $row['mrp'];
            $quantity      = $row['quantity'];
            $selling_price   = $row['selling_price'];
            
            
            $query2 = $this->db->query("SELECT name FROM `users` WHERE `id` = '$user_id' limit 1");
            $row2   = $query2->row_array();
            $distributor  = $row2['name'];
            
            $image = $row['image'];
            if($image!=''){
                $single_img = 'https://d2c8oti4is0ms3.cloudfront.net/images/product_images/' . $image;
            }
            else{
                $single_img = 'https://d2c8oti4is0ms3.cloudfront.net/images/website_assets/default_medicalwale.jpg';
            }
            
            $data[] = array(
                "id" => $id,
                "user_id" => $user_id,
                "vendor_type" => $vendor_type,
                "product_name" => $product_name,
                "sku_code" => $sku_code,
                "ptr" => $ptr,
                "mrp" => $mrp,
                "min_quantity" => $quantity,
                "max_quantity" => 10,
                "quantity" => 1,
                "image" => $single_img,
                "selling_price" => $selling_price,
                "price" => $selling_price,
                "distributor" => $distributor,
            );
        }
		
		shuffle($data);
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'end_time' => $end_time,
            'data' => $data,
            'count' => $count
        );
        return $resultpost;
    }
    
}

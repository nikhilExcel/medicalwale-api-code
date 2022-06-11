<?php
defined('BASEPATH') OR exit('No direct script access allowed');


	function json_output($statusHeader,$response)
	{
		$ci =& get_instance();
		$ci->output->set_content_type('application/json');
		$ci->output->set_status_header($statusHeader);
		$ci->output->set_output(json_encode($response));  
	}
	
	function simple_json_output($resp) 
	{
        //header('Content-Type: application/json'); 
        echo json_encode($resp);
	}
	
	function otp_json_output($resp) 
	{
        header('Content-Type: application/json'); 
        echo json_encode($resp);
	}
	
	function userlogin_json_output($resp) 
	{
        header('Content-Type: application/json'); 
        echo json_encode(array("status" => 200,"message" => "success","data"=>array($resp)));
	} 
	
	function json_outputs($resp) 
	{
        header('Content-Type: application/json'); 
        echo json_encode(array("status" => 200,"message" => "success","count"=>sizeof($resp),"data"=>$resp));
	}
	
		function json_outputs_not_found($resp) 
	{
        header('Content-Type: application/json'); 
        echo json_encode(array("status" => 404,"message" => "failure","count"=>0));
	}
	
	function json_healthwall($resp) 
	{
        header('Content-Type: application/json'); 
        echo json_encode($resp);
	}
	
	function kama_json_output($resp) 
	{
        header('Content-Type: application/json');  
        echo json_encode(array("status" => 200,"message" => "success","count"=>sizeof($resp),"jpg_url" => "https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/kamasutra_images/jpg/","url" => "https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/kamasutra_images/gif/","data"=>$resp));
	}
	
	function child_json_output($resp) 
	{
        header('Content-Type: application/json');  
        echo json_encode(array("status" => 200,"message" => "success","count"=>sizeof($resp),"url" => "https://d2c8oti4is0ms3.cloudfront.net/images/child_care_images/image/","data"=>$resp));
	}
	
	function kama_webview_output($sam) 
	{  
        echo $sam;
	}
   


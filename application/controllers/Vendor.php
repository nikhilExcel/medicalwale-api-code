<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        /*
        $check_auth_client = $this->SexeducationModel->check_auth_client();
		if($check_auth_client != true){
			die($this->output->get_output());
		}
		*/
    }

	public function index()
	{
	    json_output(400,array('status' => 400,'message' => 'Bad request.'));
	}
	
	public function login() {
        $this->load->model('VendorModel');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        if(empty($username)){
            simple_json_output(array('status' => 400,'message' => 'Please enter email id or mobile number'));
        }elseif(empty($password)){
            simple_json_output(array('status' => 400,'message' => $username));
        } else{
            $response = $this->VendorModel->login($username,$password);
        }
    }
	

}

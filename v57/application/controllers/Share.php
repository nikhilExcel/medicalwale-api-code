<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Share extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
        /*
          $check_auth_client = $this->SexeducationModel->check_auth_client();
          if($check_auth_client != true){
          die($this->output->get_output());
          }
         */
    }

    public function index() {
        //echo $ref_text=$_SERVER['QUERY_STRING'];
        //header("Location: https://play.google.com/store/apps/details?id=com.medicalwale.medicalwale");

        $route['product_details/(:any)'] = 'Share/product_details/$1';
    }

    public function product_details() {
        echo 'gf';
    }

}

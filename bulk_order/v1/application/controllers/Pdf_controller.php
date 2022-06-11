<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pdf_controller extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('pdf_model');
    }
    
    
    
    
    public function check_download_product_invoice()
    {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params       = json_decode(file_get_contents('php://input'), TRUE);
                $user_id  = $params['user_id'];
                $order_id = $params['order_id'];
                
                   $flag=0;
    	        $check_order = $this->pdf_model->get_inven_distrub_invoice($order_id);
        	    if(!empty($check_order)){
        	        $flag=1;          
        	    }
        	    else{
        	        $flag=2;
        	    }
                
                
                if($flag==0){
                    json_output(200, array(
                        'status' => 400,
                        'message' => 'Invoice Not Generated Yet'
                    ));
                }  
                elseif($flag==2){
                    json_output(200, array(
                        'status' => 400,
                        'message' => 'Invalid requests!'
                    ));
                }
                else{
                    json_output(200, array(
                        'status' => 200,
                        'message' => 'Invoice Generated Successfully'
                    ));
                }

            }
        }
    }
    
    public function download_product_invoice(){
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
        $check_auth_client = $this->LoginModel->check_auth_client();
        if($check_auth_client == true) {
             $params       = json_decode(file_get_contents('php://input'), TRUE);
             $user_id      = $params['user_id'];
             $order_id = $params['order_id'];
             $this->load->library('zip'); 
             $this->load->helper('download');
			 
            $order_detail          =  $this->pdf_model->get_inven_distrub_invoice($order_id);
			$data['order_detail'] =  $order_detail;   
			$data['user_id']	  =  $order_detail['listing_id'];          
			$data['edit']         =  $this->pdf_model->get_distributor_profile($order_detail['listing_id']);
			$this->load->view('view_invoice_details_nikhil', $data);// :blush:
			$html_content = $this->output->get_output();   
              
            $this->load->library('pdf');
            $this->pdf->set_paper("A4", "portrait"); 
	
			$this->pdf->set_option('isHtml5ParserEnabled', TRUE);
			$this->pdf->load_html($html_content);
			$this->pdf->render();
			$pdfname = 'invoice.pdf';
			$this->pdf->stream($pdfname, array("Attachment" => false));
            exit(0);
			
/*			$output = $this->pdf->output();
			file_put_contents($pdfname, $output);
			
			$filepath1 = FCPATH.$pdfname;
            $fdata =file_get_contents($filepath1);
        
            // Download
            $filename = 'invoice_'.date('Ymdhis').'.pdf';
            force_download($filename,$fdata); */
         }
       }
    }
    
    
    
    
    
   public function download_application_pdf($order_id=""){
     /*   $this->load->library('zip');  
        
	/*    $user_id  ='50808';//listing_id
        $order_id = '61';*/
                
        $data = array();
        $order_detail         =  $this->pdf_model->get_inven_distrub_invoice($order_id);
        echo $this->db->last_query().'<br/>';
		$data['order_detail'] =  $order_detail;   
		$data['user_id']	  =  $order_detail['listing_id'];          
		$distributor_profile  =  $this->pdf_model->get_distributor_profile($order_detail['listing_id']);
		echo $this->db->last_query();
		$data['edit']         =  $distributor_profile;

        
      
        $this->load->view('view_invoice_details_nikhil', $data);// :blush:
        $html_content = $this->output->get_output();  

     	$this->load->library('pdf');
        $this->pdf->set_paper("A4", "portrait"); 
        //$paper_size = array(0,0,750,1050);
     //	$this->pdf->set_paper($paper_size);
	
    	$this->pdf->set_option('isHtml5ParserEnabled', TRUE);
    	$this->pdf->load_html($html_content);
    	$this->pdf->render();
        $pdfname = 'invoice.pdf';
        $this->pdf->stream($pdfname, array("Attachment"=>1));
        exit();
        $output = $this->pdf->output();
/*        file_put_contents($pdfname, $output);
	    
        $filepath1 = FCPATH.$pdfname;
        $this->zip->read_file($filepath1);
	    
       
        $filename = 'application_form_'.date('Ymdhis').'.zip';
        $this->zip->download($filename);*/
      
    }
  


    



    
}

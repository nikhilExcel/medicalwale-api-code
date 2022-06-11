<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*require APPPATH . 'third_party/phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;*/

class Phpspreadsheet extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Calcutta');
        $this->load->model('LoginModel');
    }
    
    public function index()
    {
        $data = array();
    }
    
   
    public function check_all_orders()
    {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id   = $params['user_id'];            
				if (array_key_exists("keyword",$params)){
                    $filter['keyword']  = $params['keyword'];
                }
			
				$this->load->helper('download');  
				$data = array();  
				$result = $this->OrderModel->export_order_list($user_id,$filter);
	
				if(!empty($result)){
				     json_output(200, array(
                        'status' => 200,
                        'message' => ''
                    ));
				}
				else{
				     json_output(200, array(
                        'status' => 400,
                        'message' => 'Sorry, No data available for export!'
                    ));
				}
			
			
            }
        }
    }
  	
    public function download_all_orders()
    {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id   = $params['user_id'];
                $type   = $params['type'];
				if (array_key_exists("keyword",$params)){
                    $filter['keyword']  = $params['keyword'];
                }
               
				$fileName='order_report';
				$this->load->helper('download');  
				$data = array();  
				if($type=="PO"){
				 $result = $this->OrderModel->export_order_list($user_id,$filter);
				}
				else{
				 $result = $this->OrderModel->export_order_invoice_list($user_id,$filter);
				}
	
				
	            $this->load->library('excel');
				//$spreadsheet = new Spreadsheet();
                $sheet = new PHPExcel();
			    $sheet->setActiveSheetIndex(0);
				
                $sheet->getActiveSheet()->SetCellValue('A1', 'Sr no'); 
                $sheet->getActiveSheet()->SetCellValue('B1', 'Order Id'); 
                $sheet->getActiveSheet()->SetCellValue('C1', 'Invoice No'); 
                $sheet->getActiveSheet()->SetCellValue('D1', 'Order Status'); 
                $sheet->getActiveSheet()->SetCellValue('E1', 'Listing Name'); 
                $sheet->getActiveSheet()->SetCellValue('F1', 'Order Total'); 
                $sheet->getActiveSheet()->SetCellValue('G1', 'Delivery Type'); 
                $sheet->getActiveSheet()->SetCellValue('H1', 'Order Date'); 
                $sheet->getActiveSheet()->SetCellValue('I1', 'Name'); 
                $sheet->getActiveSheet()->SetCellValue('J1', 'Mobile'); 
                $sheet->getActiveSheet()->SetCellValue('K1', 'Pincode'); 
                $sheet->getActiveSheet()->SetCellValue('L1', 'Address1'); 
                $sheet->getActiveSheet()->SetCellValue('M1', 'Address2'); 
                $sheet->getActiveSheet()->SetCellValue('N1', 'Landmark'); 
                $sheet->getActiveSheet()->SetCellValue('O1', 'State'); 
                $sheet->getActiveSheet()->SetCellValue('P1', 'City'); 
                $sheet->getActiveSheet()->SetCellValue('Q1', 'Product Name'); 
                $sheet->getActiveSheet()->SetCellValue('R1', 'Product Qty'); 
                $sheet->getActiveSheet()->SetCellValue('S1', 'Product Price'); 
		 
				$count = 2;
				foreach ($result as $item) {
                    $sheet->getActiveSheet()->SetCellValue('A'.$count, $count-1); 
                    $sheet->getActiveSheet()->SetCellValue('B'.$count, $item['order_id']); 
                    $sheet->getActiveSheet()->SetCellValue('C'.$count, $item['invoice_no']); 
                    $sheet->getActiveSheet()->SetCellValue('D'.$count, $item['order_status']); 
                    $sheet->getActiveSheet()->SetCellValue('E'.$count, $item['listing_name']); 
                    $sheet->getActiveSheet()->SetCellValue('F'.$count, $item['order_total']); 
                    $sheet->getActiveSheet()->SetCellValue('G'.$count, $item['delivery_type']); 
                    $sheet->getActiveSheet()->SetCellValue('H'.$count, $item['order_date']); 
                    $sheet->getActiveSheet()->SetCellValue('I'.$count, $item['name']); 
                    $sheet->getActiveSheet()->SetCellValue('J'.$count, $item['mobile']); 
                    $sheet->getActiveSheet()->SetCellValue('K'.$count, $item['pincode']); 
                    $sheet->getActiveSheet()->SetCellValue('L'.$count, $item['address1']); 
                    $sheet->getActiveSheet()->SetCellValue('M'.$count, $item['address2']); 
                    $sheet->getActiveSheet()->SetCellValue('N'.$count, $item['landmark']); 
                    $sheet->getActiveSheet()->SetCellValue('O'.$count, $item['state']); 
                    $sheet->getActiveSheet()->SetCellValue('P'.$count, $item['city']); 
                    $sheet->getActiveSheet()->SetCellValue('Q'.$count, $item['product_name']); 
                    $sheet->getActiveSheet()->SetCellValue('R'.$count, $item['product_qty']); 
                    $sheet->getActiveSheet()->SetCellValue('S'.$count, $item['product_price']); 
    				$count++;
				}		 
		    $filename = $fileName. date("Y-m-d-H-i-s").".csv";
            header('Content-Type: application/vnd.ms-excel'); 
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0'); 
            // PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
            $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'CSV');  
            $objWriter->save('php://output'); 
            }
        }
    }  
    
    
    
    
    
    
    
    
    
    
    
     
    public function check_invoice_details_orders()
    {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id   = $params['user_id'];            
                $order_id   = $params['order_id'];            
		
				$this->load->helper('download');  
				$data = array();  
				$result = $this->OrderModel->export_invoice_details($user_id,$order_id);
	
				if(!empty($result)){
				     json_output(200, array(
                        'status' => 200,
                        'message' => ''
                    ));
				}
				else{
				     json_output(200, array(
                        'status' => 400,
                        'message' => 'Sorry, No data available for export!'
                    ));
				}
			
			
            }
        }
    }
  	
    public function download_invoice_details_orders()
    {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id   = $params['user_id'];    
                $order_id   = $params['order_id'];   
		
				$fileName='order_report';
				$this->load->helper('download');  
				$data = array();  
				$result = $this->OrderModel->export_invoice_details($user_id,$order_id);
				
	            $this->load->library('excel');
				//$spreadsheet = new Spreadsheet();
                $sheet = new PHPExcel();
			    $sheet->setActiveSheetIndex(0);
				
                $sheet->getActiveSheet()->SetCellValue('A1', 'CNick'); 
                $sheet->getActiveSheet()->SetCellValue('B1', 'Vendor'); 
                $sheet->getActiveSheet()->SetCellValue('C1', 'CUCode'); 
                $sheet->getActiveSheet()->SetCellValue('D1', 'Customer'); 
                $sheet->getActiveSheet()->SetCellValue('E1', 'Area'); 
                $sheet->getActiveSheet()->SetCellValue('F1', 'City'); 
                $sheet->getActiveSheet()->SetCellValue('G1', 'PinCode'); 
                $sheet->getActiveSheet()->SetCellValue('H1', 'InvNo'); 
                $sheet->getActiveSheet()->SetCellValue('I1', 'InvDate'); 
                $sheet->getActiveSheet()->SetCellValue('J1', 'OrderNo'); 
                $sheet->getActiveSheet()->SetCellValue('K1', 'OrderDate'); 
                $sheet->getActiveSheet()->SetCellValue('L1', 'Transport'); 
                $sheet->getActiveSheet()->SetCellValue('M1', 'Freight'); 
                $sheet->getActiveSheet()->SetCellValue('N1', 'Paid'); 
                $sheet->getActiveSheet()->SetCellValue('O1', 'LRNo'); 
                $sheet->getActiveSheet()->SetCellValue('P1', 'LRDate'); 
                $sheet->getActiveSheet()->SetCellValue('Q1', 'CreditDays'); 
                $sheet->getActiveSheet()->SetCellValue('R1', 'Ad'); 
                $sheet->getActiveSheet()->SetCellValue('S1', 'Ls'); 
                $sheet->getActiveSheet()->SetCellValue('T1', 'Tx'); 
                $sheet->getActiveSheet()->SetCellValue('U1', 'InvAmt'); 
                $sheet->getActiveSheet()->SetCellValue('V1', 'CNote'); 
                $sheet->getActiveSheet()->SetCellValue('W1', 'MfgrNick'); 
                $sheet->getActiveSheet()->SetCellValue('X1', 'Manufacturer'); 
                $sheet->getActiveSheet()->SetCellValue('Y1', 'PrCode'); 
                $sheet->getActiveSheet()->SetCellValue('Z1', 'ProductDesc'); 
                $sheet->getActiveSheet()->SetCellValue('AA1', 'PPack'); 
                $sheet->getActiveSheet()->SetCellValue('AB1', 'MyType'); 
                $sheet->getActiveSheet()->SetCellValue('AC1', 'MyMode'); 
                $sheet->getActiveSheet()->SetCellValue('AD1', 'BatchNo'); 
                $sheet->getActiveSheet()->SetCellValue('AE1', 'ExpDate'); 
                $sheet->getActiveSheet()->SetCellValue('AF1', 'Qty'); 
                $sheet->getActiveSheet()->SetCellValue('AG1', 'Free'); 
                $sheet->getActiveSheet()->SetCellValue('AH1', 'SchQtyAdjInAmt'); 
                $sheet->getActiveSheet()->SetCellValue('AI1', 'Rate'); 
                $sheet->getActiveSheet()->SetCellValue('AJ1', 'GrsAmt'); 
                $sheet->getActiveSheet()->SetCellValue('AK1', 'PTR'); 
                $sheet->getActiveSheet()->SetCellValue('AL1', 'MRP'); 
                $sheet->getActiveSheet()->SetCellValue('AM1', 'WPPer'); 
                $sheet->getActiveSheet()->SetCellValue('AN1', 'OctroiPer'); 
                $sheet->getActiveSheet()->SetCellValue('AO1', 'SchRate'); 
                $sheet->getActiveSheet()->SetCellValue('AP1', 'SchPer'); 
                $sheet->getActiveSheet()->SetCellValue('AQ1', 'CDPer'); 
                $sheet->getActiveSheet()->SetCellValue('AR1', 'TDPer'); 
                $sheet->getActiveSheet()->SetCellValue('AS1', 'CSTPer'); 
                $sheet->getActiveSheet()->SetCellValue('AT1', 'VATPer'); 
                $sheet->getActiveSheet()->SetCellValue('AU1', 'INetAmt'); 
                $sheet->getActiveSheet()->SetCellValue('AV1', 'Remark'); 
                $sheet->getActiveSheet()->SetCellValue('AW1', 'LOCA'); 
                $sheet->getActiveSheet()->SetCellValue('AX1', 'LOCN'); 
                $sheet->getActiveSheet()->SetCellValue('AY1', 'KeepWatch'); 
                $sheet->getActiveSheet()->SetCellValue('AZ1', 'DivNick'); 
                $sheet->getActiveSheet()->SetCellValue('BA1', 'MyTypeId'); 
                $sheet->getActiveSheet()->SetCellValue('BB1', 'MyItemNo'); 
                $sheet->getActiveSheet()->SetCellValue('BC1', 'PTS'); 
                $sheet->getActiveSheet()->SetCellValue('BD1', 'Barcode'); 
                $sheet->getActiveSheet()->SetCellValue('BE1', 'VGSTIN'); 
                $sheet->getActiveSheet()->SetCellValue('BF1', 'CGSTIN'); 
                $sheet->getActiveSheet()->SetCellValue('BG1', 'HSNCode'); 
                $sheet->getActiveSheet()->SetCellValue('BH1', 'IGSTPer'); 
                $sheet->getActiveSheet()->SetCellValue('BI1', 'IGSTAmt'); 
                $sheet->getActiveSheet()->SetCellValue('BJ1', 'CGSTPer'); 
                $sheet->getActiveSheet()->SetCellValue('BK1', 'CGSTAmt'); 
                $sheet->getActiveSheet()->SetCellValue('BL1', 'SGSTPer'); 
                $sheet->getActiveSheet()->SetCellValue('BM1', 'SGSTAmt'); 
                $sheet->getActiveSheet()->SetCellValue('BN1', 'GCCESSPer'); 
                $sheet->getActiveSheet()->SetCellValue('BO1', 'GCCESSAmt'); 
                $sheet->getActiveSheet()->SetCellValue('BP1', 'UR'); 
                $sheet->getActiveSheet()->SetCellValue('BQ1', 'EWBN'); 
                $sheet->getActiveSheet()->SetCellValue('BR1', 'MCode'); 
                $sheet->getActiveSheet()->SetCellValue('BS1', 'PItemName'); 
                $sheet->getActiveSheet()->SetCellValue('BT1', 'TCS_Per'); 
                $sheet->getActiveSheet()->SetCellValue('BU1', 'TCSAmt'); 
                $sheet->getActiveSheet()->SetCellValue('BV1', 'TDS_Per'); 
                $sheet->getActiveSheet()->SetCellValue('BW1', 'TDSAmt'); 
		 
				$count = 2;
				foreach ($result as $item) {
                    $sheet->getActiveSheet()->SetCellValue('A'.$count, '');  
                    $sheet->getActiveSheet()->SetCellValue('B'.$count, $item['name']); 
                    $sheet->getActiveSheet()->SetCellValue('C'.$count, $item['listing_id']); 
                    $sheet->getActiveSheet()->SetCellValue('D'.$count, $item['username']); 
                    $sheet->getActiveSheet()->SetCellValue('E'.$count, $item['area']); 
                    $sheet->getActiveSheet()->SetCellValue('F'.$count, $item['city']); 
                    $sheet->getActiveSheet()->SetCellValue('G'.$count, $item['pincode']); 
                    $sheet->getActiveSheet()->SetCellValue('H'.$count, $item['invoice_type']); 
                    $sheet->getActiveSheet()->SetCellValue('I'.$count, $item['invoice_date']); 
                    $sheet->getActiveSheet()->SetCellValue('J'.$count, $item['invoice_no']); 
                    $sheet->getActiveSheet()->SetCellValue('K'.$count, $item['order_date']); 
                    $sheet->getActiveSheet()->SetCellValue('L'.$count, ''); 
                    $sheet->getActiveSheet()->SetCellValue('M'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('N'.$count, 'FALSE'); 
                    $sheet->getActiveSheet()->SetCellValue('O'.$count, ''); 
                    $sheet->getActiveSheet()->SetCellValue('P'.$count, ''); 
                    $sheet->getActiveSheet()->SetCellValue('Q'.$count, '7'); 
                    $sheet->getActiveSheet()->SetCellValue('R'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('S'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('T'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('U'.$count, $item['price']); 
                    $sheet->getActiveSheet()->SetCellValue('V'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('W'.$count, $item['brand_short']); 
                    $sheet->getActiveSheet()->SetCellValue('X'.$count, $item['brand_name']); 
                    $sheet->getActiveSheet()->SetCellValue('Y'.$count, $item['product_id']); 
                    $sheet->getActiveSheet()->SetCellValue('Z'.$count, $item['product_description']); 
                    $sheet->getActiveSheet()->SetCellValue('AA'.$count, $item['pack']); 
                    $sheet->getActiveSheet()->SetCellValue('AB'.$count, 'SALE'); 
                    $sheet->getActiveSheet()->SetCellValue('AC'.$count, ''); 
                    $sheet->getActiveSheet()->SetCellValue('AD'.$count, $item['batch_no']); 
                    $sheet->getActiveSheet()->SetCellValue('AE'.$count, $item['expiry_date']); 
                    $sheet->getActiveSheet()->SetCellValue('AF'.$count, $item['product_qty']); 
                    $sheet->getActiveSheet()->SetCellValue('AG'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('AH'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('AI'.$count, $item['gst_amount']); 
                    $sheet->getActiveSheet()->SetCellValue('AJ'.$count, $item['product_gross']); 
                    $sheet->getActiveSheet()->SetCellValue('AK'.$count, $item['ptr']); 
                    $sheet->getActiveSheet()->SetCellValue('AL'.$count, $item['product_mrp']); 
                    $sheet->getActiveSheet()->SetCellValue('AM'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('AN'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('AO'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('AP'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('AQ'.$count,  $item['discount_per']); 
                    $sheet->getActiveSheet()->SetCellValue('AR'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('AS'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('AT'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('AU'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('AV'.$count, $item['remark']); 
                    $sheet->getActiveSheet()->SetCellValue('AW'.$count, ''); 
                    $sheet->getActiveSheet()->SetCellValue('AX'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('AY'.$count, 'TRUE'); 
                    $sheet->getActiveSheet()->SetCellValue('AZ'.$count,'NA'); 
                    $sheet->getActiveSheet()->SetCellValue('BA'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('BB'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('BC'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('BD'.$count, $item['barcode']); 
                    $sheet->getActiveSheet()->SetCellValue('BE'.$count, $item['VGSTIN']); 
                    $sheet->getActiveSheet()->SetCellValue('BF'.$count, $item['CGSTIN']); 
                    $sheet->getActiveSheet()->SetCellValue('BG'.$count, $item['hsncode']); 
                    $sheet->getActiveSheet()->SetCellValue('BH'.$count, $item['gst']); 
                    $sheet->getActiveSheet()->SetCellValue('BI'.$count, $item['GST2']); 
                    $sheet->getActiveSheet()->SetCellValue('BJ'.$count, $item['CGST']); 
                    $sheet->getActiveSheet()->SetCellValue('BK'.$count, $item['CGST2']); 
                    $sheet->getActiveSheet()->SetCellValue('BL'.$count, $item['SGST']); 
                    $sheet->getActiveSheet()->SetCellValue('BM'.$count, $item['SGST2']); 
                    $sheet->getActiveSheet()->SetCellValue('BN'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('BO'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('BP'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('BQ'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('BR'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('BS'.$count, $item['product_name']); 
                    $sheet->getActiveSheet()->SetCellValue('BT'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('BU'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('BV'.$count, '0'); 
                    $sheet->getActiveSheet()->SetCellValue('BW'.$count, '0'); 
    				$count++;
				}		 
				
		    $filename = $fileName. date("Y-m-d-H-i-s").".csv";
            header('Content-Type: application/vnd.ms-excel'); 
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0'); 
            // PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
            $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'CSV');  
            $objWriter->save('php://output'); 
		
            }
        }
    }  
}

?>

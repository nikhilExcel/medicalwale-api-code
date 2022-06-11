<?php
/*$check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($user_id);
if($check_table_existance['status'] == true){
    $table_name = $check_table_existance['table_name'];
} 
*/

defined('BASEPATH') OR exit('No direct script access allowed');

class PharmacyAdminModel extends CI_Model
{
    public function pharmacy_orders($page_no,$per_page,$user_orders,$panel_orders,$search,$from_date, $to_date, $order_status){
        $date_search = $order_status_query = $search_query = "";
        $pharmacy_orders = $orders = array();
        $offset = $per_page*($page_no - 1);
        $limit = "LIMIT $per_page OFFSET $offset"; 
        $created_by =  "";
        $order_by = " order by uo.invoice_no DESC ";
        if($user_orders == 1){
            // user orders and mno orders
            $created_by =  " AND uo.created_by = 0 ";
        } 
        if($panel_orders == 1){
            //panel orders   
            $created_by =  " AND uo.created_by > 0 ";
        }
        
        // $from_date $to_date $order_status : pending
        
        /*Date filter*/
        if($to_date != "" && $from_date != ""){
            $to_date = date('Y-m-d', strtotime($to_date . ' +1 day'));
            $date_search = " AND uo.order_date BETWEEN '$from_date' AND '$to_date' ";
        } else if($to_date != "" && $from_date == ""){
            $to_date = date('Y-m-d', strtotime($to_date . ' +1 day'));
            $from_date = "0000-00-00";
            $date_search = " AND uo.order_date BETWEEN '$from_date' AND '$to_date' ";
        } else if($to_date == "" && $from_date != ""){
            $today = date('Y-m-d');
            $to_date = date('Y-m-d', strtotime($today . ' +1 day'));
            $date_search = " AND uo.order_date BETWEEN '$from_date' AND '$to_date' ";
        } else {
            $date_search = "";
        }
        
        if(sizeof($order_status) > 0){
            $order_statuses = "";
            foreach($order_status as $os){
                $order_statuses .= $order_statuses != "" ? ',' : '' ;
                $order_statuses .= "'".$os . "'";
            }
            $order_status_query = " AND uo.order_status IN ($order_statuses) ";
        }
        
        
        /*Search*/
        if($search != ""){
            $search_query = " AND (uo.invoice_no like '%$search%' OR uo.name like '%$search%' OR uo.listing_name like '%$search%' OR uo.order_status like '%$search%' ) ";
        }
        
        
        $get_invoices = $this->db->query("SELECT uo.invoice_no FROM user_order as uo WHERE (uo.listing_type = '13' OR uo.listing_type = '44') $created_by $search_query $order_status_query $date_search GROUP BY invoice_no $order_by $limit")->result_array();
        
        if(sizeof($get_invoices) > 0){
            $get_invoices = array_column($get_invoices, 'invoice_no');
            $get_invoices = implode(",",$get_invoices);
            $sql = "SELECT uo.order_type, uo.order_id,uo.invoice_no,uo.name,uo.listing_name, uo.listing_id, uo.listing_type, uo.order_date, uo.order_status  FROM user_order as uo WHERE (uo.listing_type = '13' OR uo.listing_type = '44') AND uo.invoice_no IN ($get_invoices) $order_by";

            $orders = $this->db->query($sql)->result_array();
            $old_invoice_no = "";
            $prescription_order = $general_order = $po = array();
            foreach($orders as $o){
                $order_type = $o['order_type'];
                $order_id = $o['order_id'];
                $invoice_no = $o['invoice_no'];
                
                
                
                if($invoice_no != $old_invoice_no){
                // } else {
                    // new invoice
                    if(sizeof($po) > 0){
                        $pharmacy_orders[] = $po;
                        $general_order = $prescription_order = $po = array();
                    }
                    
                    $po['order_id'] = $o['order_id'];
                    $po['invoice_no'] = $o['invoice_no'];
                    $po['listing_name'] = $o['listing_name'];
                    $po['name'] = $o['name'];
                    $po['listing_id'] = $o['listing_id'];
                    $po['listing_type'] = $o['listing_type'];
                    $po['order_date'] = $o['order_date'];
                    $po['order_status'] = $o['order_status'];
                    
                }
                
                if($order_type == 'order'){
                    $general_order['order_id'] =  $order_id;
                    $po['general_order'] = $general_order;
                }
                
                if($order_type == 'prescription'){
                    $prescription_order['order_id'] =  $order_id;
                    $po['prescription_order'] = $prescription_order;
                }
                
                $old_invoice_no = $invoice_no;
            }
            if(sizeof($po) > 0){
                $pharmacy_orders[] = $po;
                $general_order = $prescription_order = $po = array();
            }
        }
        
        $return['orders'] = $pharmacy_orders;
        
        return $return;
        
        
    }
}
?>
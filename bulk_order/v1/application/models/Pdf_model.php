<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pdf_model extends CI_Model {
  
    public function get_inven_distrub_invoice($order_id){
        $this->db->select('*');
        $this->db->order_by('order_id', 'desc');
        $this->db->from('inven_distrub_invoice');
        $this->db->where('order_id', $order_id);
        $cat = $this->db->get()->row_array();
        return $cat;
    }
  
  
     public function get_distributor_profile($uid)
    {
        $sql = "SELECT * FROM distributor WHERE user_id ='$uid'";
        $result = $this->db->query($sql)->row();
        return $result;
    }
  
}
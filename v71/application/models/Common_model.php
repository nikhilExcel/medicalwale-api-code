<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common_model extends CI_Model
{
    public function state_list($user_id)
    {
        $query = $this->db->query("SELECT * FROM state_list WHERE country_id='101' order by name");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = $row['name'];
            
            $data[] = array(
                "id" => $id,
                "name" => $name
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    public function city_list($user_id, $state_id)
    {
        $query = $this->db->query("SELECT * FROM city_list WHERE state_id='$state_id' order by name");
        $query2 = $this->db->query("SELECT name FROM state_list WHERE id='$state_id' limit 1");
        $state = $query2->row_array();
        $state_name = $state['name'];
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = $row['name'];
            
            $data[] = array(
                "id" => $id,
                "name" => $name
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data,
            'state' => $state_name
        );
        return $resultpost;
    }
    
}
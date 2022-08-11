<?php
class M_grafik extends CI_Model{
 
    function get_data_transportasi(){
        $query = $this->db->query("SELECT * FROM transportation");
          
        if($query->num_rows() > 0){
            foreach($query->result() as $data){
                $hasil[] = $data;
            }
            return $hasil;
        }
    }
 
}
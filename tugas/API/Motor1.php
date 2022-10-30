<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Motor1 extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);	
        $this->load->database();	     
    }
    public function index_get() {		
		$type = $this->get('type');
		$id   = $this->get('id');
        try{
            if ($type == ''){ //GET motor
                if ($id == ''){
                    $comp = $this->db->get('motor')->result();
                    } else{
                        $this->db->where('id_motor', $id);
                        $comp = $this->db->get('motor') -> result();
                    }
            }elseif($type == 'transaksi'){  //GET transaksi
				if ($id == ''){
                    $comp = $this->db->get('transaksi')->result();   
                }else{
                $this->db->where('tr.id_trans', $id);
                $this->db->join('transaksi AS tr','tr.id_motor = m.id_motor','right');
                $comp = $this->db->get('motor AS m')->result();
                }
            }elseif($type == 'stok'){ //GET stok
                if ($id == ''){
                    $comp = $this->db->get('stok')->result();   
                }else{
                $this->db->where('s.id_motor', $id);
                $this->db->join('stok AS s','s.id_motor = m.id_motor','right');
                $comp = $this->db->get('motor AS m')->result();
                }
            }elseif($type == 'pembeli'){ //GET pembeli
                if ($id == ''){
                    $comp = $this->db->get('pembeli')->result();   
                }else{
                $this->db->where('p.id_pembeli', $id);
                $this->db->join('pembeli AS p','p.id_trans = tr.id_trans','right');
                $this->db->join('motor AS m','tr.id_motor = m.id_motor','right');
                $comp = $this->db->get('transaksi AS tr')->result();
                }
            }
        $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
					  "code"=>200,
					  "message"=>"Response successfully",
					  "data"=>$comp];	
				$this->response($result, 200);
		}catch (Exception $e){
			$result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
					  "code"=>401,
					  "message"=>"Access denied",
					  "data"=>null];	
			$this->response($result, 401);
		}	
    }
    
    //POST transaksi
    function index_post(){
        $type = $this->post('type');
        try{
            if($type == ''){
                $data = array(
                    'id_motor'		=> $this -> post ('id_motor'),
                    'jumlah_unit'		=> $this -> post ('jumlah_unit'));
                $insert = $this->db->insert('transaksi', $data);
                if ($insert) {
                    $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
                              "code"=>200,
                              "message"=>"Response successfully",
                              "data"=>$data];	
                        $this->response($result, 200);
                }else {
                    $this->response(array('status' => 'fail', 502));
                }
            } else if($type == 'pembeli'){
                $data = array(
                    'id_pembeli'		=> $this -> post ('id_pembeli'),
                    'id_trans'		=> $this -> post ('id_trans'),
                    'nama_pembeli'		=> $this -> post ('nama_pembeli'),
                    'tgl_trans'		=> $this -> post ('tgl_trans'));
                $insert = $this->db->insert('pembeli', $data);
                if ($insert) {
                    $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
                              "code"=>200,
                              "message"=>"Response successfully",
                              "data"=>$data];	
                        $this->response($result, 200);
                }else {
                    $this->response(array('status' => 'fail', 502));
                }

            }

        }catch (Exception $e){
			$result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
					  "code"=>401,
					  "message"=>"Access denied",
					  "data"=>null];	
			$this->response($result, 401);
        }   
    }

    // PUT 
    function index_put(){
        $type = $this->put('type');
        $id = $this ->put('id');
        try{
            if($type == ''){
		        $data = array (
                    'id_motor'		=> $this -> put ('id_motor'),
                    'jumlah_unit'		=>$this -> put ('jumlah_unit'));
                $this->db->where('id_trans', $id);
                $updt = $this->db->update('transaksi', $data);
                if ($updt) {
                $this->response($data, 200);
                }
            } else if($type == 'pembeli'){
                $data = array (
                    'id_trans'		=> $this -> put ('id_trans'),
                    'id_pembeli'		=>$this -> put ('id_pembeli'),
                    'nama_pembeli'		=>$this -> put ('nama_pembeli'),
                    'tgl_trans'		=>$this -> put ('tgl_trans'));
                $this->db->where('id_trans', $id);
                $updt = $this->db->update('pembeli', $data);
                if ($updt) {
                $this->response($data, 200);
                }
            }
        }catch (Exception $e){
			$result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
					  "code"=>401,
					  "message"=>"Access denied",
					  "data"=>null];	
			$this->response($result, 401);
        }
		
    }
        	
}
?>

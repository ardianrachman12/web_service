<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Motor2 extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);	
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));	     
    }
    public function index_get() {		
		$type = $this->get('type');
		$id   = $this->get('id');
        $motor=[]; //array motor
        $pembeli=[];
        $transaksi=[];
        $stok=[];
        try{
            if ($type == ''){ //GET motor
                if ($id == ''){
                    $comp = $this->db->get('motor')->result();
                    foreach($comp as $row=>$key):
                        $motor[]=[
                            "id_motor"=>$key->id_motor,
                            "merek"=>$key->merek,
                            "harga"=>$key->harga,
                            "jenis_motor"=>$key->jenis_motor,
                            "warna"=>$key->warna,
                            "generasi"=>$key->generasi
                        ];
                    endforeach;
                    } else{
                       $this->db->where('id_motor', $id);
                       $comp = $this->db->get('motor')->result();
                       $motor=[
                        "id_motor"=>$comp[0]->id_motor,
                        "merek"=>$comp[0]->merek,
                        "harga"=>$comp[0]->harga,
                        "jenis_motor"=>$comp[0]->jenis_motor,
                        "warna"=>$comp[0]->warna,
                        "generasi"=>$comp[0]->generasi
                       ];
                    }
                    $etag = hash('sha256', $comp[0]->generasi);				 
                    $this->cache->save($etag, $motor, 300);	
            }elseif($type == 'transaksi'){  //GET transaksi
				if ($id == ''){
                    $comp = $this->db->get('transaksi')->result();
                    foreach($comp as $row=>$key):
                        $motor[]=[
                            "id_trans"=>$key->id_trans,
                            "_links"=>[(object)["href"=>"motor/{$key->id_motor}",
												"rel"=>"motor",
												"type"=>"GET"]],
                            "jumlah_unit"=>$key->jumlah_unit
                        ];
                    endforeach;
                }else{
                    $this->db->where('id_trans', $id);
                    $comp = $this->db->get('transaksi')->result();
                    $motor=[
                        "id_trans"=>$comp[0]->id_trans,
                        "_links"=>[(object)["href"=>"motor/{$comp[0]->id_motor}",
												"rel"=>"motor",
												"type"=>"GET"]],
                        "jumlah_unit"=>$comp[0]->jumlah_unit
                    ];
                }
                $etag = hash('sha256', $comp[0]->jumlah_unit);				 
                $this->cache->save($etag, $transaksi, 300);	
            }elseif($type == 'stok'){ //GET stok
                if ($id == ''){
                    $comp = $this->db->get('stok')->result();   
                }else{
                $this->db->where('s.id_motor', $id);
                $this->db->join('stok AS s','s.id_motor = m.id_motor','right');
                $comp = $this->db->get('motor AS m')->result();
                }
                $etag = hash('sha256', $comp[0]->stok_motor);				 
                $this->cache->save($etag, $stok, 300);	
            }elseif($type == 'pembeli'){ //GET pembeli
                if ($id == ''){
                    $comp = $this->db->get('pembeli')->result();   
                }else{
                $this->db->where('p.id_pembeli', $id);
                $this->db->join('pembeli AS p','p.id_trans = tr.id_trans','right');
                $this->db->join('motor AS m','tr.id_motor = m.id_motor','right');
                $comp = $this->db->get('transaksi AS tr')->result();
                }
                $etag = hash('sha256', $comp[0]->tgl_trans);				 
                $this->cache->save($etag, $pembeli, 300);	
            }		
		$this->output->set_header('ETag:'.$etag);	
		$this->output->set_header('Cache-Control: must-revalidate');	
		if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {								
			$this->output->set_header('HTTP/1.1 304 Not Modified');
		}else{
        $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
					  "code"=>200,
					  "message"=>"Response successfully",
					  "data"=>$comp];	
				$this->response($result, 200);
        }
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
            $check = $this->db->get("transaksi")->num_rows();
		    if($check==0):
                $insert = $this->db->insert('transaksi', $data);
                if ($insert) {            
                    $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
                                "code"=>201,
                                "message"=>"Data has successfully added",
                                "data"=>$data];	
                    $this->response($result, 201);
                } else {
                    $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
                          "code"=>502,
                          "message"=>"Failed adding data",
                          "data"=>null];	
                    $this->response($result, 502);            
                }
            else:
                $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
                          "code"=>304,
                          "message"=>"Data already added.",
                          "data"=>null];	
                    $this->response($result, 304); 
            endif;
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

    function index_delete (){
        $type = $this->delete('type');
		$id = $this->delete ('id');
		try{
            if ($type == ''){
                $check = $this->db->get("transaksi")->num_rows();
		    if($check==0):
                $delete = $this->db->delete('transaksi');
                if ($insert) {            
                    $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
                                "code"=>201,
                                "message"=>"Data has successfully deleted",
                                "data"=>$data];	
                    $this->response($result, 201);
                } else {
                    $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
                          "code"=>502,
                          "message"=>"Failed deleting data",
                          "data"=>null];	
                    $this->response($result, 502);            
                }
            else:
                $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
                          "code"=>204,
                          "message"=>"Data already delete.",
                          "data"=>null];	
                    $this->response($result, 304); 
            endif;
            } else if ($type == 'pembeli'){
                $this->db->where('id_trans', $id);
		        $delete = $this->db->delete('pembeli');
                if ($delete){
                    $this->response(array('status' => 'success'), 201);
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

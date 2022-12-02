<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
//use Restserver\Libraries\REST_Controller;

class Motor2 extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);	
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));	     
    }
    public function index_get() {		
        $authHeader = $this->input->get_request_header('Authorization');		
        $arr = explode(" ", $authHeader);
        $jwt = isset($arr[1])? $arr[1] : "";
        $secretkey = base64_encode("gampang");
    if($jwt){
        $type = $this->get('type');
		$id   = $this->get('id');
        $motor=[]; //array motor
        $pembeli=[]; //array pembeli
        $transaksi=[];  //array transaksi
        $stok=[];  //array stok
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
                        $motor =[
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
                    $motor =[
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
                    foreach($comp as $row=>$key):
                        $stok = [
                            "_links"=>[(object)["href"=>"motor/{$key->id_motor}",
                                                "rel"=>"motor",
                                                "type"=>"GET"]],
                            "stok_motor"=>$key->stok_motor
                        ];
                    endforeach;   
                }else{
                    $this->db->where('id_motor', $id);
                    $comp = $this->db->get('stok')->result();
                    $motor=[
                    "_links"=>[(object)["href"=>"motor/{$comp[0]->id_motor}",
                                            "rel"=>"motor",
                                            "type"=>"GET"]],
                    "stok_motor"=>$comp[0]->stok_motor
                    ];
                }
                $etag = hash('sha256', $comp[0]->stok_motor);				 
                $this->cache->save($etag, $stok, 300);	
            }elseif($type == 'pembeli'){ //GET pembeli
                if ($id == ''){
                    $comp = $this->db->get('pembeli')->result();   
                }else{
                $this->db->where('id_pembeli', $id);
                    $comp = $this->db->get('pembeli')->result();
                    $pembeli=[
                    "_links"=>[(object)["href"=>"transaksi/{$comp[0]->id_trans}",
                                            "rel"=>"transaksi",
                                            "type"=>"GET"]],
                    "nama_pembeli"=>$comp[0]->nama_pembeli,
                    "tgl_trans"=>$comp[0]->tgl_trans
                    ];
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
    }else{
        $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
					"code"=>401,
					"message"=>"Access denied",
					"data"=>null];	
		$this->response($result, 401);
    }	
    }
    
    //POST transaksi

    function index_post(){
        $authHeader = $this->input->get_request_header('Authorization');		
        $arr = explode(" ", $authHeader);
        $jwt = isset($arr[1])? $arr[1] : "";        
        $secretkey = base64_encode("gampang");
        $type = $this->post('type');
    if($jwt){
        try{
            if($type == ''){
                $data = array(
                    'id_trans'		=> $this -> post ('id_trans'),
                    'id_motor'		=> $this -> post ('id_motor'),
                    'jumlah_unit'		=> $this -> post ('jumlah_unit'));
            $this->db->where("id_trans",$this->post('id_trans'));				
            $check = $this->db->get("transaksi")->num_rows(); //indepotency
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
                $this->db->where("id_trans",$this->post('id_trans'));				
                $check = $this->db->get("pembeli")->num_rows(); //indepotency
                if($check==0):
                    $insert = $this->db->insert('pembeli', $data);
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

            }

        }catch (Exception $e){
			$result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
					  "code"=>401,
					  "message"=>"Access denied",
					  "data"=>null];	
			$this->response($result, 401);
        }
    }else{
        $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
					  "code"=>401,
					  "message"=>"Access denied",
					  "data"=>null];	
			$this->response($result, 401);
    }   
    }

    // PUT 
    function index_put(){
        $authHeader = $this->input->get_request_header('Authorization');		
        $arr = explode(" ", $authHeader);
        $jwt = isset($arr[1])? $arr[1] : "";        
        $secretkey = base64_encode("gampang");
    if($jwt){
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
    }else{
        $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
					  "code"=>401,
					  "message"=>"Access denied",
					  "data"=>null];	
			$this->response($result, 401);
    }
		
    }

    function index_delete (){
        $authHeader = $this->input->get_request_header('Authorization');		
        $arr = explode(" ", $authHeader);
        $jwt = isset($arr[1])? $arr[1] : "";        
        $secretkey = base64_encode("gampang");
    if($jwt){
        $type = $this->delete('type');
		$id = $this->delete ('id');
		try{
            if ($type == ''){
                $this->db->where('id_trans', $id);
		        $delete = $this->db->delete('transaksi');
                if ($delete){
                    $this->response(array('status' => 'success'), 201);
                }
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
	}else{
        $result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
					  "code"=>401,
					  "message"=>"Access denied",
					  "data"=>null];	
			$this->response($result, 401);
    }
    }
        	
}
?>
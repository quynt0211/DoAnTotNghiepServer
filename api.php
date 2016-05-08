<?php
ini_set('max_execution_time', 0);
date_default_timezone_set("Asia/Saigon");
header('Content-Type: text/html; charset=utf-8');

require_once 'dbhelper.php';
require_once("Rest.inc.php");

class API extends REST {
	private $soccerway;

	public function __construct(){
		parent::__construct();				// Init parent contructor
	}

	/*
	 * Public method for access api.
	 * This method dynmically call the method based on the query string
	 *
	 */
	public function processApi(){
		$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
		if((int)method_exists($this,$func) > 0)
			$this->$func();
		else
			$this->response('',404);				// If the method not exist with in this class, response would be "Page not found".
	}

	private function expGetMethod()
    {
        // check method when client sent to server
        if($this->get_request_method() != "GET"){
            $this->response('',406);
        }

        // xu ly du lieu

        // du lieu tra ve:
        $returnData = array(
            'success' => true, 
            'data' => array(
                'key1' => 'value 1',
                'key2' => 'value 2'
        ));

        $this->response($this->json($returnData), 200);
    }

    private function expPostMethod()
    {
        // check method when client sent to server
        if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }

       
        // du lieu tra ve:
        $returnData = array(
            'success' => true, 
            'data' => 'text data'
        );

        $this->response($this->json($returnData), 200);
    }
	
	private function createOwner()
	{
        if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 5)), 406);
        }
		
		$data = array(
			'Name' 			=> $_POST['name'],
			'PhoneNumber' 	=> $_POST['phone'],
			'Address' 		=> $_POST['address'],
			'Password' 		=> $_POST['password']
		);
		
		$tempphone = $_POST['phone'];
		
		$result = DBHelper::runQuery("select * from chuhang where PhoneNumber = ".$tempphone);
		
		if($result->num_rows == 0 ){
			$result = DBHelper::runQuery("select * from dailyvanchuyen where phonenumber = ".$tempphone);
			if($result->num_rows == 0 ){
				if ($id = DBHelper::Insert('chuhang', $data)){
				
				$result = array(
					'idowner' => $id,
					'name'	=> $data['Name'],
					'phonenumber' => $data['PhoneNumber'],
					'address' 	=> $data['Address'],
					'password'	=> $data['Password']
				);
				$returnData = array(
					'success' => true,  
					'owner'		=> $result
				);
			}else {
				$returnData = array(
					'success' => false, 
					'message' => "Hệ Thống Đang Gặp Sự Cố. Vui Lòng Thử Lại Sau"
				);
			}
			$this->response($this->json($returnData), 200);
			}else{
				$returnData = array(
				'success' => false,
				'message' => "Đã tồn tại số điện thoại trên hệ thống"
				);
			$this->response($this->json($returnData), 200);
			}
		}else{
			$returnData = array(
				'success' => false,
				'message' => "Đã tồn tại số điện thoại trên hệ thống"
				);
			$this->response($this->json($returnData), 200);
		}
	}
	
	private function createShipper(){
		// check method when client sent to server
        if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		// create owner
		$data = array(
			'name' 			=> $_POST['name'],
			'phonenumber' 	=> $_POST['phone'],
			'address' 		=> $_POST['address'],
			'password' 		=> $_POST['password'],
			'introduce'		=> $_POST['introduce'],
			'isFragile'		=> $_POST['isfragile'],
			'isInflammable'	=> $_POST['isinflammable'],
			'isBulky'		=> $_POST['isbulky'],
			'isHeavy'		=> $_POST['isheavy'],
			'isSamples'		=> $_POST['issamples']
		);
		
		$tempphone = $_POST['phone'];
		$result = DBHelper::runQuery("select * from dailyvanchuyen where PhoneNumber = ".$tempphone);
		if($result->num_rows == 0 ){
			if ($id = DBHelper::Insert('dailyvanchuyen', $data)){
			// du lieu tra ve:
			$returnData = array(
				'success' => true, 
				'success-code' => 1,
				'$data'			=> $data,
				'id'			=>  $id
			);
		}else {
			// du lieu tra ve:
			$returnData = array(
				'success' => false, 
				'error-code' => 4
			);
		}
			$this->response($this->json($returnData), 200);
		}else{
			$returnData = array(
				'success' => false,
				'error-code' => "5"
				);
			$this->response($this->json($returnData), 200);
			
		}
	}
	
	
	private function login(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$data = array(
			'phone' => $_POST['phone'],
			'password'	=> $_POST['password']
		);
		
		$result1 = DBHelper::runQuery("select * from chuhang where PhoneNumber = ".$data['phone']." and Password = '".$data['password']."'");
		//var_dump("select * from chuhang where PhoneNumber = ".$data['phone']." and Password = ".$data['password']); die;
		//$result1 = DBHelper::runQuery("select * from chuhang where PhoneNumber = ".$data['phone']);
		
		if($result1){
			if($result1->num_rows == 0){
			$result = DBHelper::runQuery("select * from dailyvanchuyen where phonenumber = ".$data['phone']." and password = '".$data['password']."'"  );
				if($result){
					if($result->num_rows == 0 ){
						$returnData = array(
						'success' => false,
						'message' => "Sai Tài Khoản Hoặc Mật Khẩu"
					);
					$this->response($this->json($returnData), 200);
					}else{
						$returnData = array(
						'success' => true,
						'accountType' => 2,
						'data'		=> $result->fetch_assoc()
						);
						$this->response($this->json($returnData), 200);
					}
				}else{
					$returnData = array(
					'success' => false,
					'message' => "Lỗi Hệ Thống"
				);
			$this->response($this->json($returnData), 200);
				}
				
			}else{
				$returnData = array(
					'success' => true,
					'accountType' => 1,
					'data'		=> $result1->fetch_assoc()
					);
				$this->response($this->json($returnData), 200);
			}
		}else{
				$returnData = array(
				'success' => false,
				'message' => "Lỗi Hệ Thống"
				);
			$this->response($this->json($returnData), 200);
		}
	}
	
	
	private function createDeliveryRequirements(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		//create delivery requirements
		$data = array(
				'idowner'  		=> $_POST['idowner'],
				'name'  		=> $_POST['name'],
				'isfragile' 	=> $_POST['isfragile'],
				'isinflammable' => $_POST['isinflammable'],
				'isbulky' 		=> $_POST['isbulky'],
				'isheavy' 		=> $_POST['isheavy'],
				'issamples'		=> $_POST['issamples'],
				'status' 		=> $_POST['status'],
				'createtime'	=> $_POST['createtime'],
				'startlocation' => $_POST['startlocation'],
				'endlocation' 	=> $_POST['endlocation'],
				'number'		=> $_POST['number'],
				'weigh'			=> $_POST['weigh']
			);
		if (isset($_POST['updatedtime']))
			$data['updatedtime'] = $_POST['updatedtime'];
		
		if( isset($_POST['longtitude']))
			$data['longtitude'] = $_POST['longtitude'];
		
		if( isset($_POST['latitude']))
			$data['latitude'] = $_POST['latitude'];
		
		if( isset($_POST['idshipper']))
			$data['idshipper'] = $_POST['idshipper'];
		
		if( isset($_POST['description']))
			$data['description'] = $_POST['description'];
		
		if (DBHelper::Insert('goihang', $data)){
			// du lieu tra ve:
			$returnData = array(
				'success' => true, 
				'success-code' => 1
			);
		}else {
			// du lieu tra ve:
			$returnData = array(
				'success' => false, 
				'error-code' => 4
			);
		}

        $this->response($this->json($returnData), 200);	
		
	}
	
	private function auction(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$data = array(
			'idpackage' 	=> $_POST['idpackage'],
			'idowner' 		=> $_POST['idowner'],
			'idshipper'		=> $_POST['idshipper'],
			'auctiondate' 	=> $_POST['auctiondate']
		);
		
		// check where or not idpackage is deliveryed
		$idpackage = $data['idpackage'];
		$sql = "select isDelivery from goihang where idpackage = ".$idpackage;
		$result = DBHelper::runQuery($sql);
		if($result){
			$row = $result->fetch_assoc();
			$isDelivery = $row['isDelivery'];
			if($isDelivery == 0 ){
				if (DBHelper::Insert('daugia', $data)){
				$returnData = array(
					'success' => true, 
					'success-code' => "OK"
				);
				}else {
					// du lieu tra ve:
					$returnData = array(
						'success' => false, 
						'error-code' => 4
					);
				}
			}else{
				$returnData = array(
						'success' => false, 
						'code' => 10
					);
			}
		}
		$this->response($this->json($returnData), 200);	
	}
	
	private function getDeliveryRequirements(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		//$sql = "select * from goihang";
		$sql = "select goihang.*, chuhang.`Name` as ownerName
				from goihang, chuhang
				where goihang.idowner = chuhang.idowner
				and goihang.isDelivery = 0";
		$result = DBHelper::runQuery($sql);
		
		$data = array();
		while($row = $result->fetch_assoc()){
			$data[] = $row;
		}
		
		
		
		$returnData = array(
				'success' => true, 
				'data' => $data
			);
		$this->response($this->json($returnData), 200);	
	}
	
	
	private function getHistoryShipper(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idShipper = $_POST['id'];
		$sql = "select * from goihang where idshipper = ".$idShipper;
		$result = DBHelper::runQuery($sql);
		$data = array();
		while($row = $result->fetch_assoc()){
			$data[] = $row;
		}
		
		$returnData = array(
				'success' => true, 
				'data' => $data
			);
		$this->response($this->json($returnData), 200);	
	}
	
	private function getHistoryOwner(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idowner = $_POST['id'];
		$sql = "select * from goihang where idowner = ".$idowner;
		$result = DBHelper::runQuery($sql);
		$data = array();
		while($row = $result->fetch_assoc()){
			$data[] = $row;
		}
		
		$returnData = array(
				'success' => true, 
				'data' => $data
			);
		$this->response($this->json($returnData), 200);	
	}
	
	private function updateStatusPackage(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$status = $_POST['status'];
		$idpackage = $_POST['idpackage'];
		$idshipper = $_POST['idshipper'];
		
		$sql = "update goihang set status = '".$status."' where idpackage = ".$idpackage." and idshipper = ".$idshipper;
		$result = DBHelper::runQuery($sql);
		if($result){
			$returnData = array(
				'success' => true, 
				'code' => 1
			);
		}else{
			$returnData = array(
				'success' => false, 
				'error-code' => 4
			);
		}
		$this->response($this->json($returnData), 200);	
	}
	
	private function updateLocation(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idpackage = $_POST['idpackage'];
		$idshipper = $_POST['idshipper'];
		$longtitude = $_POST['longtitude'];
		$latitude = $_POST['latitude'];
		
		$sql = "update goihang set longtitude = ".$longtitude.", latitude = ".$latitude." where idpackage = ".$idpackage." and idshipper = ".$idshipper; 
//		var_dump($sql); die;
		$result = DBHelper::runQuery($sql);
		if($result){
			$returnData = array(
				'success' => true, 
				'code' => 1
			);
		}else{
			$returnData = array(
				'success' => false, 
				'error-code' => 4
			);
		}
		
		$this->response($this->json($returnData), 200);	
	}
	
	private function viewAuction(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idpackage = $_POST['idpackage'];
		$idowner = $_POST['idowner'];
		
		$sql = "select dailyvanchuyen.*, daugia.idpackage, daugia.idowner, daugia.allowed, daugia.auctiondate, daugia.rate
				from dailyvanchuyen, daugia
				where daugia.idshipper = dailyvanchuyen.idshipper
				and daugia.idowner = ".$idowner."
				and daugia.idpackage = ".$idpackage;
		
		$result = DBHelper::runQuery($sql);		
		if($result){
			$data = array();
			while($row = $result->fetch_assoc()){
				$data[] = $row;
			}
			
			$returnData = array(
				'success'	=>true,
				'data'		=> $data
			);
		}else{
			$returnData = array(
				'success'	=>falst,
				'code'		=> 101
				);
		}
		$this->response($this->json($returnData), 200);
	}
	
	private function allowAuction(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idshipper = $_POST['idshipper'];
		$idowner = $_POST['idowner'];
		$idpackage = $_POST['idpackage'];
		
		$sql = "update daugia set allowed = 1 
				where idshipper = ".$idshipper."
				and	idpackage = ".$idpackage."
				and	idowner = ".$idowner;	
		$result = DBHelper::runQuery($sql);
		if($result){
			$returnData = array(
				'success' => true,
				'code'	=> 1
			);
		}else{
			$returnData = array(
				'success' => false,
				'code'	=> 0
			);
		}
		$this->response($this->json($returnData), 200);
	}
	
	private function allowDelivery(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idshipper = $_POST['idshipper'];
		$idowner = $_POST['idowner'];
		$idpackage = $_POST['idpackage'];
		
		$sql = "update goihang set isDelivery = 1, idshipper = ".$idshipper." 
				where idpackage = ".$idpackage."
				and	idowner = ".$idowner;
		//var_dump($sql)			;die;
		$result = DBHelper::runQuery($sql);
		if($result){
			$returnData = array(
				'sucess' => true,
				'code'	 => 1
			);
		}else{
			$returnData = array(
				'sucess' => false,
				'code'	 => 0
			);
		}
		
		$this->response($this->json($returnData), 200);
	}
	
	private function updateDeliveryRequirement(){
		
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idpackage = $_POST['idpackage'];
		$idowner = $_POST['idowner'];
		$data = array(
				'name'  		=> $_POST['name'],
				'isfragile' 	=> $_POST['isfragile'],
				'isinflammable' => $_POST['isinflammable'],
				'isbulky' 		=> $_POST['isbulky'],
				'isheavy' 		=> $_POST['isheavy'],
				'issamples'		=> $_POST['issamples'],
				'status' 		=> $_POST['status'],
				'createtime'	=> $_POST['createtime'],
				'startlocation' => $_POST['startlocation'],
				'endlocation' 	=> $_POST['endlocation'],
				'number'		=> $_POST['number'],
				'weigh'			=> $_POST['weigh']
			);
			
		if( isset($_POST['description']))
			$data['description'] = $_POST['description'];
		
		$sql = "select isDelivery from goihang where idpackage = ".$idpackage;
	//	var_dump($sql); die;
		$result = DBHelper::runQuery($sql);
		if($result){
			$row = $result->fetch_assoc();
			if($row['isDelivery'] == 1 ){
				$returnData = array(
					'success' => false,
					'message'		=> "Đã Có Người Đi Giao"
				);
			}else{
				$resu = DBHelper::Update("goihang", $data, "idpackage = ".$idpackage);
				if($resu){
					$returnData = array(
					'success' => true,
					'message'		=> "Sửa Yêu Cầu Thành Công"
					);
				}else{
					$returnData = array(
					'success' => false,
					'message'		=> "Lỗi Hệ Thống"
					);
				}
			}
		}else{
			$returnData = array(
					'success' => false,
					'message'		=> "Lỗi Hệ Thống"
			);
		}
		
		$this->response($this->json($returnData), 200);
	}
	
	private function removeDeliveryRequirement(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idowner = $_POST['idowner'];
		$idpackage = $_POST['idpackage'];
		
		$selectSql = "select * from goihang where idpackage = ".$idpackage;
		$resultSelect = DBHelper::runQuery($selectSql);
		
		if($resultSelect){
			$row = $resultSelect->fetch_assoc();
			if($row['isDelivery'] == 0 ){
				$sql = "delete from goihang where idowner = ".$idowner." and idpackage = ".$idpackage;
				$result = DBHelper::runQuery($sql);
					
				if($result){
					$returnData = array(
						'success' => true,
						'message'	 => "Hủy Yêu Cầu Thành Công"
						);
					}else{
						$returnData = array(
							'success' => false,
							'message'	 => "Lỗi Hệ Thống"
						);
					}
			
				$this->response($this->json($returnData), 200);
			}else{
				$returnData = array(
							'success' => false,
							'message'	 => "Đã Có Người Đi Giao"
				);
				$this->response($this->json($returnData), 200);
			}
		}else{
			$returnData = array(
							'success' => false,
							'message'	 => "Lỗi Hệ Thống"
				);
			$this->response($this->json($returnData), 200);
		}
	}
	
	// danh sach nhan di giao cua shipper
	private function viewDelivery(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idshipper = $_POST['id'];
		$sql = "select chuhang.`Name` as nameowner, goihang.`name`, goihang.startlocation, goihang.endlocation, daugia.allowed
				from chuhang, daugia, goihang
				where daugia.rate = -1
				and daugia.idpackage = goihang.idpackage
				and goihang.isDelivery = 0
				and goihang.idowner = chuhang.idowner
				and daugia.idshipper = ".$idshipper;
		$result = DBHelper::runQuery($sql);
		if($result){
			$data = array();
			while($row = $result->fetch_assoc()){
				$data[]= $row;
			}
			
			$returnData = array(
				'success' =>true,
				'data'	=> $data
			);
		}else{
			$returnData = array(
				'success' =>false,
				'code'	=> 0
			);
		}
		
		$this->response($this->json($returnData), 200);
	}
	
	// danh sach dau gia cua shipper
	private function viewAuctionShipper(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }

		$idshipper = $_POST['id'];
		
		$sql = "select chuhang.`Name` as nameowner, goihang.`name`, goihang.startlocation, goihang.endlocation, daugia.rate
				from chuhang, daugia, goihang
				where daugia.rate != -1
				and daugia.allowed = 1
				and daugia.idpackage = goihang.idpackage
				and goihang.isDelivery = 0
				and goihang.idowner = chuhang.idowner
				and daugia.idshipper = ".$idshipper;
				
		$result = DBHelper::runQuery($sql);
		if($result){
			$data = array();
			while($row = $result->fetch_assoc()){
				$data[]= $row;
			}
			
			$returnData = array(
				'success' =>true,
				'data'	=> $data
			);
		}else{
			$returnData = array(
				'success' =>false,
				'code'	=> 0
			);
		}
		
		$this->response($this->json($returnData), 200);		
	}
	
	private function updateAuctionShipper(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idshipper = $_POST['idshipper'];
		$idpackage = $_POST['idpackage'];
		$rate = $_POST['rate'];
		
		$sql = "update daugia set rate = ".$rate."
				where idshipper = ".$idshipper." 
				and idpackage = ".$idpackage;
		//var_dump($sql); die;		
		$result = DBHelper::runQuery($sql);
		if($result){
			$returnData = array(
				'success' =>true,
				'code'	=> 1
			);
		}else{
			$returnData = array(
				'success' =>false,
				'code'	=> 0
			);
		}
		
		$this->response($this->json($returnData), 200);	
	}
	
	/*
	 *	Encode array into JSON
	*/
	private function json($data){
		if(is_array($data)){
			return json_encode($data);
		}
	}
}
	
// Initiiate Library
$api = new API;
$api->processApi();

?>
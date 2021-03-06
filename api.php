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
	
	private function loginAdmin(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 5)), 406);
        }
		
		$id = $_POST['id'];
		$password = $_POST['password'];
		
		$sql = "select * from admin where id = '".$id."' and password = '".$password."'";
		$result = DBHelper::runQuery($sql);
		if($result){
			if($result->num_rows == 1){
			$returnData = array(
            'success' => true, 
            'data' => $result->fetch_assoc()
			);
			}else{
				$returnData = array(
					'success' => false, 
					'message' => "Sai Tài Khoản Hoặc Mật Khẩu"
				);
			}
		}else{
			$returnData = array(
            'success' => false, 
            'message' => 'Lỗi Hệ Thống, Vui Lòng Thử Lại'
			);
			
		}
		 $this->response($this->json($returnData), 200);
	}
	
	private function updateInfoOwner(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 5)), 406);
        }
		
		$data = array(
			'id'			=> $_POST['id'],
			'Name' 			=> $_POST['name'],
			'PhoneNumber' 	=> $_POST['phone'],
			'Address' 		=> $_POST['address'],
			'Password' 		=> $_POST['password']
		);
		
		$tempphone = $_POST['phone'];
		
		$result = DBHelper::runQuery("select * from chuhang where PhoneNumber = ".$tempphone);
		if($result->num_rows <2 ){
			$result1 = DBHelper::runQuery("select * from dailyvanchuyen where phonenumber = ".$tempphone);
			if($result1){
				if($result1->num_rows == 0 ){
					$sqlUpdate = "update chuhang set chuhang.`Name` = '".$data['Name']."', 
					chuhang.`Address` = '".$data['Address']."', 
					chuhang.`Password` = '".$data['Password']."',
					chuhang.`PhoneNumber` = '".$data['PhoneNumber']."'
					where chuhang.`idowner` = ".$data['id']; 
					$result2 = DBHelper::runQuery($sqlUpdate);
					
					if($result2){
						$returnData = array(
						'success' => true,
						'message' => "Thành Công"
						);
						$this->response($this->json($returnData), 200);
					}else{
						$returnData = array(
							'success' => false,
							'message' => "Lỗi Hệ Thống, Vui Lòng Thử Lại Sau"
							);
						$this->response($this->json($returnData), 200);
					}
				}else{
					$returnData = array(
					'success' => false,
					'message' => "Đã Tồn Tại Số Điện Thoại Trên Hệ Thống"
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
	}
	
	private function updateInfoShipper(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		// create owner
		$data = array(
			'idshipper'		=> $_POST['idshipper'],
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
		$sqlSelect = "select * from chuhang where PhoneNumber = ".$tempphone;
	//	var_dump($sqlSelect);die;
		$result = DBHelper::runQuery($sqlSelect);
		if($result){
			if($result->num_rows == 0 ){
				$sqlSelect1 = "select * from dailyvanchuyen where phonenumber = ".$tempphone;
			//	var_dump($sqlSelect1); die;
				$result1 = DBHelper::runQuery($sqlSelect1);
				if($result1){
					if($result1->num_rows < 2){
						// update
						$result2 = DBHelper::Update('dailyvanchuyen', $data, 'dailyvanchuyen.idshipper = '.$data['idshipper']);
						if($result2){
							$returnData = array(
							'success' => true,
							'message' => "Cập Nhật Thành Công"
						);
						$this->response($this->json($returnData), 200);
						}else{
							$returnData = array(
							'success' => false,
							'message' => "Lỗi Hệ Thống, Vui Lòng Thử Lại"
							);
							$this->response($this->json($returnData), 200);
						}
					}else{
						$returnData = array(
						'success' => false,
						'message' => "Đã Tồn Tại Số Điện Thoại Trên Hệ Thống"
						);
					$this->response($this->json($returnData), 200);
					}
				}else{
					$returnData = array(
					'success' => false,
					'message' => "Lỗi Hệ Thống, Vui Lòng Thử Lại"
					);
					$this->response($this->json($returnData), 200);
				}
			}else{
				$returnData = array(
				'success' => false,
				'message' => "Đã Tồn Tại Số Điện Thoại Trên Hệ Thống"
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
			$result1 = DBHelper::runQuery("select * from dailyvanchuyen where phonenumber = ".$tempphone);
			if($result1->num_rows == 0 ){
				if ($id = DBHelper::Insert('chuhang', $data)){
				
					$result = array(
						'idowner' => $id,
						'Name'	=> $data['Name'],
						'PhoneNumber' => $data['PhoneNumber'],
						'Address' 	=> $data['Address'],
						'Password'	=> $data['Password']
					);
					
			/*		$returnData = array(
						'success' => true,  
						'owner'		=> $result
					);*/
					
					$returnData = array(
						'success' => true,  
						'message'		=> "Bạn Vui Lòng Đăng Nhập Sau 24h Sau."
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
		
		$result = DBHelper::runQuery("select * from chuhang where PhoneNumber = ".$tempphone);
		if($result->num_rows == 0 ){
			$result1 = DBHelper::runQuery("select * from dailyvanchuyen where phonenumber = ".$tempphone);
			if($result1->num_rows == 0 ){
				if($id = DBHelper::Insert('dailyvanchuyen', $data)){
					$result = array(
						'idshipper' => $id,
						'name'	=> $data['name'],
						'phonenumber' => $data['phonenumber'],
						'address' 	=> $data['address'],
						'password'	=> $data['password'],
						'introduce'	=> $data['introduce'],
						'isFragile'	=> $data['isFragile'],
						'isInflammable'	=> $data['isInflammable'],
						'isBulky'	=> $data['isBulky'],
						'isHeavy'	=> $data['isHeavy'],
						'isSamples'	=> $data['isSamples']
						);
					
				/*	$returnData = array(
						'success' => true,
						'data'		=> $result
						);*/
						$returnData = array(
						'success' => true,
						'message'		=> "Vui Lòng Đăng Nhập Sau 24h"
						);
					$this->response($this->json($returnData), 200);						
				}else{
					$returnData = array(
					'success' => false,
					'message' => "Lỗi Hệ Thống, Vui Lòng Thử Lại"
					);
					$this->response($this->json($returnData), 200);
				}
			}else{
				$returnData = array(
				'success' => false,
				'message' => "Đã Tồn Tại Số Điện Thoại Trên Hệ Thống"
				);
			$this->response($this->json($returnData), 200);
			}
		}else{
			$returnData = array(
				'success' => false,
				'message' => "Đã Tồn Tại Số Điện Thoại Trên Hệ Thống"
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
		
		$sql = "select * from chuhang where PhoneNumber = ".$data['phone']." and Password = '".$data['password']."'";
		$result1 = DBHelper::runQuery($sql);
		
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
						
						$row = $result->fetch_assoc();
						if($row['accept'] == 2){
							$returnData = array(
							'success' => true,
							'accountType' => 2,
							'data'		=> $row
							);
						}
						
						if($row['accept'] == 1 ){
							$returnData = array(
							'success' => false,
							'message'		=> "Tài Khoản Của Bạn Bị Khóa"
							);
						}
						
						if($row['accept'] == 0 ){
							$returnData = array(
							'success' => false,
							'message'		=> "Vui Lòng Đợi Ban Quản Trị Duyệt Tài Khoản Của Bạn"
							);
						}
						
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
				
				$row = $result1->fetch_assoc();
					if($row['accept'] == 2){
						$returnData = array(
						'success' => true,
						'accountType' => 1,
						'data'		=> $row
						);
					}
						
					if($row['accept'] == 1 ){
						$returnData = array(
						'success' => false,
						'message'		=> "Tài Khoản Của Bạn Bị Khóa"
						);
					}
						
					if($row['accept'] == 0 ){
						$returnData = array(
						'success' => false,
						'message'		=> "Vui Lòng Đợi Ban Quản Trị Duyệt Tài Khoản Của Bạn"
						);
					}
						
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
		if( isset($_POST['ownerName']))
			$data['ownerName']	=  $_POST['ownerName'];
		
		if( isset($_POST['idshipper']))
			$data['idshipper'] = $_POST['idshipper'];
		
		if( isset($_POST['description']))
			$data['description'] = $_POST['description'];
		
		if (DBHelper::Insert('goihang', $data)){
			// du lieu tra ve:
			$returnData = array(
				'success' => true, 
				'message' => "Thành Công "
			);
		}else {
			// du lieu tra ve:
			$returnData = array(
				'success' => false, 
				'message' => "Lỗi Hê Thống."
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
		$sql = "select goihang.*, chuhang.`Name` as ownerName, chuhang.PhoneNumber
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
		$sql = "select goihang.*, chuhang.PhoneNumber, daugia.rate from goihang, chuhang, daugia where goihang.idowner = chuhang.idowner and goihang.idpackage = daugia.idpackage and goihang.idshipper = ".$idShipper;
		//var_dump($sql);die;
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
	
	private function getRate(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idpackage = $_POST['idpackage'];
		$idshipper = $_POST['idshipper'];
		$sql = "select * from daugia where idshipper = ".$idshipper." and idpackage = ".$idpackage;
	//	var_dump($sql);die;
		$result = DBHelper::runQuery($sql);
		if($result){
			$returnData = array(
				'success' => true, 
				'data' => $result->fetch_assoc()
			);
		}else{
			$returnData = array(
				'success' => false, 
				'message' => "Lỗi Hệ Thống, Vui Lòng Thử Lại"
			);
		}
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
		$updatetime	= $_POST['updatetime'];
		
		//$sql = "update goihang set status = '".$status."' where idpackage = ".$idpackage." and idshipper = ".$idshipper;
		$sql = "update goihang set status = '".$status."', updatedtime = '".$updatetime."' where idpackage = ".$idpackage." and idshipper = ".$idshipper; 
		$result = DBHelper::runQuery($sql);
		if($result){
			$returnData = array(
				'success' => true, 
				'message' => "Cập Nhật Trạng Thái Thành Công"
			);
		}else{
			$returnData = array(
				'success' => false, 
				'message' => "Lỗi Hệ Thống, Vui Lòng Thử Lại"
			);
		}
		$this->response($this->json($returnData), 200);	
	}
	
	private function getDetailPackage(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idpackage = $_POST['idpackage'];
		$sql = "select * from goihang where idpackage = ".$idpackage;
		$result = DBHelper::runQuery($sql);
		if($result){
			$returnData = array(
				'success' => true, 
				'data' => $result->fetch_assoc()
			);
		}else{
			$returnData = array(
				'success' => false, 
				'message' => "Lỗi Hệ Thống, Vui Lòng Thử Lại"
			);
		}
		
		$this->response($this->json($returnData), 200);
	}
	
	private function getDetailShipper(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idshipper = $_POST['idshipper'];
		$sql = "select * from dailyvanchuyen where idshipper = ".$idshipper;
		
		$result = DBHelper::runQuery($sql);
		
		if($result){
			$data = $result->fetch_assoc();
			$returnData = array(
				'success' => true, 
				'data' => $data
			);
			
		}else{
			$returnData = array(
				'success' => false, 
				'message' => "Lỗi Hệ Thống, Vui Lòng Thử Lại"
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
		$currentLocation = $_POST['location'];
		$updatetime	= $_POST['updatetime'];
		
		$sql = "update goihang set currentlocation = '".$currentLocation."', updatedtime = '".$updatetime."' where idpackage = ".$idpackage." and idshipper = ".$idshipper; 
	//	var_dump($sql); die;
		$result = DBHelper::runQuery($sql);
		if($result){
			$returnData = array(
				'success' => true, 
				'message' => "Cập Nhật Vị Trí Thành Công"
			);
		}else{
			$returnData = array(
				'success' => false, 
				'message' => "Lỗi Hệ Thống, Vui Lòng Thử Lại"
			);
		}
		
		$this->response($this->json($returnData), 200);	
	}
	
	// xem cac dai ly van chuyen nhan di giao hang ( cua owner)
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
				
		$sql1 = "select dailyvanchuyen.*
				from dailyvanchuyen, daugia
				where daugia.idshipper = dailyvanchuyen.idshipper
				and daugia.allowed = 0
				and daugia.idowner = ".$idowner."
				and daugia.idpackage = ".$idpackage;
				
		$result = DBHelper::runQuery($sql1);		
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
				'success'	=>false,
				'message'		=> "Lỗi Hệ Thống"
				);
		}
		$this->response($this->json($returnData), 200);
	}
	
	
	private function viewAuctionAllowed(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idpackage = $_POST['idpackage'];
		$idowner = $_POST['idowner'];
		
		$sql = "select dailyvanchuyen.*, daugia.idpackage, daugia.idowner, daugia.allowed, daugia.auctiondate, daugia.rate
				from dailyvanchuyen, daugia
				where daugia.idshipper = dailyvanchuyen.idshipper
				and daugia.allowed = 1
				and daugia.idowner = ".$idowner."
				and daugia.idpackage = ".$idpackage;
				
		$sql1 = "select dailyvanchuyen.*
				from dailyvanchuyen, daugia
				where daugia.idshipper = dailyvanchuyen.idshipper
				and daugia.allowed = 0
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
				'success'	=>false,
				'message'		=> "Lỗi Hệ Thống"
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
				'message'	=> "Thành Công"
			);
		}else{
			$returnData = array(
				'success' => false,
				'code'	=> "Lỗi Hệ Thống"
			);
		}
		$this->response($this->json($returnData), 200);
	}
	
	private function allowDelivery(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idshipper = $_POST['idshipper'];
		$nameshipper = $_POST['nameshipper'];
		$idowner = $_POST['idowner'];
		$idpackage = $_POST['idpackage'];
		$shipperphone = $_POST['phoneshipper'];
		
		$sqlSelect = "select * from goihang where idpackage = ".$idpackage;
		$resultSelect = DBHelper::runQuery($sqlSelect);
		if($resultSelect){
			$row = $resultSelect->fetch_assoc();
			if($row['isDelivery'] == 1 ){
				$returnData = array(
				'success' => false,
				'message'	 => "Đã Có Người Đi Giao"
				);
				$this->response($this->json($returnData), 200);
			}else{
				$sql = "update goihang set isDelivery = 1, shipperphone = '".$shipperphone."', nameshipper = '".$nameshipper."', idshipper = ".$idshipper." 
				where idpackage = ".$idpackage."
				and	idowner = ".$idowner;
				$result = DBHelper::runQuery($sql);
				if($result){
					$returnData = array(
						'success' => true,
						'message'	 => "Thành Công"
					);
				}else{
					$returnData = array(
						'success' => false,
						'message'	 => "Lỗi Hệ Thống"
					);
				}
		
				$this->response($this->json($returnData), 200);
			}
		}else{
			$returnData = array(
				'sucess' => false,
				'message'	 => "Lỗi Hệ Thống"
			);
			$this->response($this->json($returnData), 200);
		}
		
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
		$sql = "select chuhang.`Name` as nameowner, goihang.`name`, goihang.startlocation, goihang.endlocation, daugia.allowed, daugia.idpackage
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
		
		$sql = "select chuhang.`Name` as nameowner, goihang.`name`, goihang.startlocation, goihang.endlocation, daugia.rate, daugia.idpackage
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
	
	private function getInfoOwner(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idowner = $_POST['idowner'];
		$sql = "select * from chuhang where idowner = ".$idowner;
		$result = DBHelper::runQuery($sql);
		if($result){
			$returnData = array(
				'success' =>true,
				'data'	=> $result->fetch_assoc()
			);
		}else{
			$returnData = array(
				'success' =>false,
				'message'	=> "Lỗi Hệ Thống, Vui Lòng Thử Lại"
			);
		}
		
		$this->response($this->json($returnData), 200);
	}
	
	private function getNotificationShipper(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idshipper = $_POST['idshipper'];
		$status = "";
		
		$sql = "select * from goihang where status = '' and idshipper = ".$idshipper;
		$result = DBHelper::runQuery($sql);
		if($result){
			if($result->num_rows != 0 ){
				$returnData = array(
				'success' =>true,
				'message'	=> "Bạn Nhận Được ".$result->num_rows." Yêu Cầu Đi Giao Hàng. Chi Tiết Xem Trong Lịch Sử"
				);
			}else{
				$returnData = array(
				'success' =>false,
				'message'	=> ""
				);
			}
		}else{
			$returnData = array(
				'success' =>false,
				'message'	=> "Lỗi Hệ Thống, Vui Lòng Thử Lại"
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
		$updatetime = $_POST['updatetime'] ;
		
		$sql = "update daugia set rate = ".$rate.", auctiondate = '".$updatetime."'
				where idshipper = ".$idshipper." 
				and idpackage = ".$idpackage;
	//	var_dump($sql); die;		
		$result = DBHelper::runQuery($sql);
		if($result){
			$returnData = array(
				'success' =>true,
				'message'	=> "Đấu Giá Thành Công"
			);
		}else{
			$returnData = array(
				'success' =>false,
				'message'	=> "Lỗi Hệ Thống, Vui Lòng Thử Lại"
			);
		}
		
		$this->response($this->json($returnData), 200);	
	}
	
	
	// api for shipper 
	private function receiveDelivery(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$data = array(
			'idpackage' => $_POST['idpackage'],
			'idowner'	=> $_POST['idowner'],
			'idshipper'	=> $_POST['idshipper'],
			'auctiondate'=> $_POST['auctiondate'],
			'rate'		=> -1
		);
		
		$sqlSelect = "select * from daugia where idpackage = ".$data['idpackage']."
						and idowner = ".$data['idowner']."
						and idshipper = ".$data['idshipper'];
		//				var_dump($sqlSelect); die;
		$resultSelect = DBHelper::runQuery($sqlSelect);
		if($resultSelect){
			if($resultSelect->num_rows == 0 ){
				$resultInsert = DBHelper::Insert('daugia', $data);
				if($resultInsert){
					$returnData = array(
					'success' =>true,
					'message'	=> "Gửi Yêu Cầu Đi Giao Thành Công"
					);
					$this->response($this->json($returnData), 200);	
				}else{
					$returnData = array(
					'success' =>false,
					'message'	=> "Lỗi Hệ Thống, Vui Lòng Thử Lại"
				);
				$this->response($this->json($returnData), 200);	
				}
			}else{
				$returnData = array(
				'success' =>false,
				'message'	=> "Bạn Đã Gửi Yêu Cầu Nhận Đi Giao"
				);
			$this->response($this->json($returnData), 200);	
			}
		}else{
			$returnData = array(
				'success' =>false,
				'message'	=> "Lỗi Hệ Thống, Vui Lòng Thử Lại"
			);
			$this->response($this->json($returnData), 200);	
		}
	}
	
	
	// api for admin
	private function getOwners(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$accountType = $_POST['type'];
		$sql = "select * from chuhang where accept = ".$accountType;
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
				'message'	=> "Lỗi Hệ Thống, Vui Lòng Thử Lại"
			);
		}
		$this->response($this->json($returnData), 200);	
	}
	
	private function blockOwner(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idOwner = $_POST['idowner'];
		$sql = "update chuhang set accept = 1 where idowner = ".$idOwner;
		$result = DBHelper::runQuery($sql);
		if($result){
			$returnData = array(
				'success' =>true,
				'message'	=> "Chặn Người Dùng Thành Công"
			);
		}else{
			$returnData = array(
				'success' =>false,
				'message'	=> "Lỗi Hệ Thống, Vui Lòng Thử Lại"
			);
		}
		$this->response($this->json($returnData), 200);	
	}
	
	private function allowRegister(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idOwner = $_POST['idowner'];
		$sql = "update chuhang set accept = 2 where idowner = ".$idOwner;
		$result = DBHelper::runQuery($sql);
		if($result){
			$returnData = array(
				'success' =>true,
				'message'	=> "Thành Công"
			);
		}else{
			$returnData = array(
				'success' =>false,
				'message'	=> "Lỗi Hệ Thống, Vui Lòng Thử Lại"
			);
		}
		$this->response($this->json($returnData), 200);	
	}
	
	private function getShippers(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$accountType = $_POST['type'];
		$sql = "select * from dailyvanchuyen where accept = ".$accountType;
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
				'message'	=> "Lỗi Hệ Thống, Vui Lòng Thử Lại"
			);
		}
		$this->response($this->json($returnData), 200);	
	}
	
	private function blockShipper(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idshipper = $_POST['idshipper'];
		$sql = "update dailyvanchuyen set accept = 1 where idshipper = ".$idshipper;
		$result = DBHelper::runQuery($sql);
		if($result){
			$returnData = array(
				'success' =>true,
				'message'	=> "Chặn Người Dùng Thành Công"
			);
		}else{
			$returnData = array(
				'success' =>false,
				'message'	=> "Lỗi Hệ Thống, Vui Lòng Thử Lại"
			);
		}
		$this->response($this->json($returnData), 200);	
	}
	
	private function allowShipperRegister(){
		if($this->get_request_method() != "POST"){
            $this->response($this->json(array('success' => false, 'errror-code' => 2)), 406);
        }
		
		$idshipper = $_POST['idshipper'];
		$sql = "update dailyvanchuyen set accept = 2 where idshipper = ".$idshipper;
		$result = DBHelper::runQuery($sql);
		if($result){
			$returnData = array(
				'success' =>true,
				'message'	=> "Thành Công"
			);
		}else{
			$returnData = array(
				'success' =>false,
				'message'	=> "Lỗi Hệ Thống, Vui Lòng Thử Lại"
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
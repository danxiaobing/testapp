<?php

class SupplierController extends Yaf_Controller_Abstract {
	/**
	 * IndexController::init()
	 *
	 * @return void
	 */
	public function init() {
		# parent::init();
         

    }

	/**
	 * 显示整个后台页面框架及菜单
	 *
	 * @return string
	 */
	public function shiplistAction() {
		$params = array();

		$this->getView()->make('supplier.shiplist',$params);
	}

	public function shiplistJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'bar_shipno' => $request->getPost('bar_shipno',''),
			'bar_shipcontact' => $request->getPost('bar_shipcontact',''),
			'bar_status' => $request->getPost('bar_status',''),
			'bar_shipname' => $request->getPost('bar_shipname',''),
			'bar_company' => $request->getPost('bar_company',''),
			'bar_captain' => $request->getPost('bar_captain',''),
			'pageCurrent' => COMMON :: P(),
			'pageSize' => COMMON :: PR(),
			'orders'  => 'created_at desc',

		);

		if($request->getParam('page')==1){
			$search['page'] = false;
			$search['bar_status'] = $request->getParam('bar_status','');
			unset($search['pageCurrent']);
			unset($search['pageSize']);
		}
		
		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$list = $S->searchShipList($search);

		echo json_encode($list);
	}

	public function shipEditAction(){
		$request = $this->getRequest();
		$id = $request->getParam('id',0);

		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		if(!$id){
			$action = "/supplier/shipNewJson/";
			$params =  array ();
		}
		else{
			$action = "/supplier/shipEditJson/";

			$params = $S->getShipById($id);

			$A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));


		//	$attach = $S->getShipAttachById($id);
			$attach = $A->getAttachByMAS('supplier','ship',$id);
            if( is_array($attach) && count($attach)){
				$files = array();
				foreach ($attach as $file){
				//	$files[] =  $file['module'].'/'.$file['action'].'/'. $file['name'];
					$files[] = $file['sysno'];
				}

				$params['uploaded']  =  join(',',$files);
			}

			$params['attach']  = $attach;

		}

		$params['id'] =  $id;
		$params['action'] =  $action;


		$this->getView()->make('supplier.shipedit',$params);
	}

	public function shipNewJsonAction(){
		$request = $this->getRequest();

		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$input = array(
			'shipno'       =>  $request->getPost('shipno',''),
			'shipname'     =>  $request->getPost('shipname',''),
			'company'=>  $request->getPost('company',''),
			'captain'      =>  $request->getPost('captain',''),
			'shipcontact'	  =>  $request->getPost('shipcontact',''),
			'shipwidth'   =>  $request->getPost('shipwidth',''),
			'shiplength'   =>  $request->getPost('shiplength',''),
			'shiploadcapacity'   =>  $request->getPost('shiploadcapacity',''),
			'shiploadweight'   =>  $request->getPost('shiploadweight',''),
			'shipmarks'     =>  $request->getPost('shipmarks',''),
			'status'             =>  $request->getPost('status','1'),
			'isdel'              =>  $request->getPost('isdel','0'),
			'created_at'		=>'=NOW()',
			'updated_at'		=>'=NOW()'
		);

		$search=array(
                    'bar_shipno'=>$input['shipno'],
                    'real_shipname'=>$input['shipname'],
                    'bar_isdel'=>0,
                    'pageSize'=>20
                    );
		$existship=$S->searchShipisexist($search);
		//print_r($existship);
		if(!empty($existship['totalRow'])){
            COMMON::result(300,'船舶编号或船名不能重复');
            return ;
        }

		if($id = $S->addShip($input)){
			$attach =  $request->getPost('attachment',array());
            if(count($attach) > 0){
			//	$res = 	$S->addShipAttach($id,$attach);
				$res = $A->addAttachModelSysno($id,$attach);
				if(!$res){
					COMMON::result(300,'添加附件失败');
					return;
				}
			}

			$row = $S->getShipById($id);
			COMMON::result(200,'添加成功',$row);
		}else{
			COMMON::result(300,'添加失败');
		}
	}

	public function shipEditJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('id',0);

		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$input = array(
			'shipno'       =>  $request->getPost('shipno',''),
			'shipname'     =>  $request->getPost('shipname',''),
			'company'=>  $request->getPost('company',''),
			'captain'      =>  $request->getPost('captain',''),
			'shipwidth'   =>  $request->getPost('shipwidth',''),
			'shiplength'   =>  $request->getPost('shiplength',''),
			'shiploadcapacity'   =>  $request->getPost('shiploadcapacity',''),
			'shiploadweight'   =>  $request->getPost('shiploadweight',''),
			'shipmarks'     =>  $request->getPost('shipmarks',''),
			'status'             =>  $request->getPost('status','1'),
			'isdel'              =>  $request->getPost('isdel','0'),
			'created_at'		=>'=NOW()',
			'updated_at'		=>'=NOW()'
		);

		if($S->updateShip($id,$input)){
			$attach =  $request->getPost('attachment',array());
			if(count($attach) > 0){
			//	$res = 	$S->addShipAttach($id,$attach);
				$res = $A->addAttachModelSysno($id,$attach);
				if(!$res){
					COMMON::result(300,'添加附件失败');
					return;
				}
			}

			$row = $S->getShipById($id);
			COMMON::result(200,'更新成功',$row);
		}else{
			COMMON::result(300,'更新失败');
		}
	}

	public function shipDelJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('sysno',0);

		if(strpos($id, ',')){
			COMMON::result(300,'一次只能删除一行数据');
			return;
		}

		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		if($S->delShip($id)){
			COMMON::result(200,'删除成功');
		}else{
			COMMON::result(300,'删除失败');
		}
	}


	/**
	 * 车辆管理
	 * @author Jay Xu
	 */

	public function carlistAction()
	{
		$params = array(

		);
		$this->getView()->make('supplier.carlist', $params);
	}

	public function carEditAction(){
		$request = $this->getRequest();
		$id = $request->getParam('id',0);

		$module = array();

		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		if(!$id){
			$action = "/supplier/carNewJson/";
			$params =  array();
			$carprivileges = array();
		}

		else{
			$action = "/supplier/carEditJson/";
			$params = $S->getCarById($id);
		}

		$carprivileges = $S->getCarViewPrivilege($carprivileges);

		$params['privileges'] = $carprivileges['privileges'];
		$params['module'] = $carprivileges['module'];

		$params['id'] =  $id;
		$params['action'] =  $action;

		$this->getView()->make('supplier.caredit',$params);
	}

	public function carNewJsonAction(){
		$request = $this->getRequest();

		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$input = array(
				'carid'         =>  $request->getPost('carid',''),
				'carname'       =>  $request->getPost('carname',''),
				'mobilephone'   =>  $request->getPost('mobilephone',''),
				'idcard'        =>  $request->getPost('idcard',''),
				'carmarks'      =>  $request->getPost('carmarks',''),
				'status'        =>  $request->getPost('status','1'),
				'created_at'	=>'=NOW()',
				'updated_at'	=>'=NOW()'
		);

		#后台验证车牌号
		$search = array (
				'bar_carid'=>$input['carid'],
				'page' => false,
		);
		$carid = $S->searchCar($search);
		if(!empty($carid['list'])){
			COMMON::result(300,'车牌号不能重复');
			return;
		}

		#后台验证司机手机号
		// $search = array (
		// 		'mobilephone'=>$input['mobilephone'],
		// 		'page' => false,
		// );
		// $mobilephone = $S->searchCar($search);
		// if($input['mobilephone'] != null && $mobilephone['list'] != null){
		// 	COMMON::result(300,'手机号码不能重复');
		// 	return;
		// }


		#后台验证身份证号
//		$search = array (
//				'idcard'=>$input['idcard'],
//				'page' => false,
//		);
//		$idcard = $S->searchCar($search);
//		if(!empty($idcard['list']) && $input['idcard'] != null){
//			COMMON::result(300,'身份证号不能重复');
//			return;
//		}


		if($id = $S->addCar($input)){
			$row = $S->getCarById($id);
			COMMON::result(200,'添加成功',$row);
		}else{
			COMMON::result(300,'添加失败');
		}
	}

	public function carEditJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('id',0);
		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$input = array(
				'carid'         =>  $request->getPost('carid',''),
				'carname'       =>  $request->getPost('carname',''),
				'mobilephone'   =>  $request->getPost('mobilephone',''),
				'idcard'        =>  $request->getPost('idcard',''),
				'carmarks'      =>  $request->getPost('carmarks',''),
				'status'        =>  $request->getPost('status','1'),
				'updated_at'	=>'=NOW()'
		);

		#后台验证车牌号
		$search = array (
				'bar_carid' =>$input['carid'],
				'page' =>false,
		);
		$carid = $S->searchCar($search);

		if(!empty($carid['list']) && $carid['list'][0]['sysno']!=$id){
			COMMON::result(300,'车牌号不能重复');
			return;
		}

		#后台验证司机手机号
		// $search = array (
		// 		'mobilephone' =>$input['mobilephone'],
		// 		'page' =>false,
		// );
		// $mobilephone = $S->searchCar($search);
		// if(!empty($mobilephone['list']) && $mobilephone['list'][0]['sysno']!=$id){
		// 	COMMON::result(300,'手机号不能重复');
		// 	return;
		// }

		#后台验证司机身份证号
//		$search = array (
//				'idcard' =>$input['idcard'],
//				'page' =>false,
//		);
//		$idcard = $S->searchCar($search);
//		if(!empty($idcard['list']) && $idcard['list'][0]['sysno']!=$id){
//			COMMON::result(300,'身份证号不能重复');
//			return;
//		}

		if($S->updateCarData($id,$input)){
			$row = $S->getCarById($id);
			COMMON::result(200,'更新成功',$row);
		}else{
			COMMON::result(300,'更新失败');
		}
	}

	public function CarDelJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('sysno',0);

		if(strpos($id, ',')){
			COMMON::result(300,'一次只能删除一行数据');
			return;
		}

		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$input = array(
				'isdel' => 1
		);

		if($S->updateCar($id,$input)){
			COMMON::result(200,'删除成功');
		}else{
			COMMON::result(300,'删除失败');
		}
	}

	public function carlistJsonAction() {
		$request = $this->getRequest();

		$search = array (
				'bar_carid'  => $request->getPost('carid',''),
				'bar_name' => $request->getPost('bar_name',''),
				'bar_parentid' => $request->getPost('bar_parentid','-100'),
				'bar_status' => $request->getPost('bar_status','-100'),
				'pageCurrent' => COMMON :: P(),
				'pageSize' => COMMON :: PR(),

		);
		// print_r($search);die();
		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$list = $S->searchCar($search);

		echo json_encode($list);
	}

	/*
	 * 查询车辆信息，供车入库 出库时调用
	 * @author wu xianneng
	 */
	public function addcarlistAction(){
		$R = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$params = $R->getcarsInfo();

		echo json_encode($params);
	}

	public function shipStatusAction()
	{
		$request = $this->getRequest();
		$idArray = $request->getPost('idArray',0);
		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$result = $S->updateShipStatus($idArray);
		if($result){
			$data = array('msg' => '成功');
		}else{
			$data = array('msg' => '失败');
		}

		echo json_encode($data);

	}
	public function carStatusAction()
	{
		$request = $this->getRequest();
		$idArray = $request->getPost('idArray',0);
		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$result = $S->updateCarStatus($idArray);
		if($result){
			$data = array('msg' => '成功');
		}else{
			$data = array('msg' => '失败');
		}

		echo json_encode($data);

	}

	/**
	 * 车辆轴数限重
	 * @author zhaoshiyu
	 */

	public function carinfolistAction()
	{
		$params = array(

		);
		$this->getView()->make('supplier.carinfolist', $params);
	}
	
	public function carinfolistJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'pageCurrent' => COMMON :: P(),
			'pageSize' => COMMON :: PR(),
			'orders'  => 'created_at desc',

		);

		if($request->getParam('page')==1){
			$search['page'] = false;
			unset($search['pageCurrent']);
			unset($search['pageSize']);
		}

		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$list = $S->searchCarInfoList($search);

		echo json_encode($list);
	}
	//删除车轴限重
	public function carinfoDelJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('sysno',0);
		
		if(strpos($id, ',')){
			COMMON::result(300,'一次只能删除一行数据');
			return;
		}
		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		if($S->delCarinfo($id)){
			COMMON::result(200,'删除成功');
		}else{
			COMMON::result(300,'删除失败');
		}
	}

	public function carinfoEditAction(){
		$request = $this->getRequest();
		$id = $request->getParam('id',0);

		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		if(!$id){
			$action = "/supplier/carinfoNewJson/";
			$params =  array ();
		}
		else{
			$action = "/supplier/carinfoEditJson/";
			$params = $S->getCarinfoById($id);
		}

		$params['id'] =  $id;
		$params['action'] =  $action;


		$this->getView()->make('supplier.carinfoedit',$params);
	}
	//添加车轴限重
	public function carinfoNewJsonAction(){
		$request = $this->getRequest();

		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$input = array(
			'axlenum'       =>  $request->getPost('axlenum',''),
			'carloadweight'     =>  $request->getPost('carloadweight',''),
			'status'             =>  $request->getPost('status','1'),
			'isdel'              =>  $request->getPost('isdel','0'),
			'created_at'		=>'=NOW()',
			'updated_at'		=>'=NOW()'
		);

		if($id = $S->addCarinfo($input)){
			$row = $S->getCarinfoById($id);
			COMMON::result(200,'添加成功',$row);
		}else{
			COMMON::result(300,'添加失败');
		}
	}
	//编辑车轴限重
	public function carinfoEditJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('id',0);

		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$input = array(
			'axlenum'       =>  $request->getPost('axlenum',''),
			'carloadweight'     =>  $request->getPost('carloadweight',''),
			'status'             =>  $request->getPost('status','1'),
			'isdel'              =>  $request->getPost('isdel','0'),
			'created_at'		=>'=NOW()',
			'updated_at'		=>'=NOW()'
		);

		if($S->updateCarinfo($id,$input)){
			$row = $S->getCarinfoById($id);
			COMMON::result(200,'更新成功',$row);
			return;
		}else{
			COMMON::result(300,'更新失败');
			return;
		}
	}
	//批量操作车轴限重启用|停用
	public function carinfoStatusAction()
	{
		$request = $this->getRequest();
		$idArray = $request->getPost('idArray',0);
		$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$result = $S->updateCarinfoStatus($idArray);
		if($result){
			$data = array('msg' => '成功');
		}else{
			$data = array('msg' => '失败');
		}

		echo json_encode($data);

	}
	
	//在预约单或者订单中直接新增车辆信息
	public function updateCarinfoAction()
    {
    	$request = $this->getRequest();
		$cardata = $request->getPost('cardata','');
    	if ($cardata) {
	    	$sup = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
	    	$carres = $sup->checkCardata($cardata);
	    	if (!$carres) {
	    		echo json_encode(array('code' => 300,'msg' => '车辆信息更新失败'));
	    	}else{
	    		echo json_encode(array('code' => 200,'msg' => '车辆信息更新成功'));
	    	}
		}else{
			echo json_encode(array('code' => 300,'msg' => '车辆信息为空'));
		}
    }

    /**
     * title : 检验车牌是否停用
     * params : carid 车牌号
     */
    public function checkcaridAction()
    {
    	$request = $this->getRequest();
    	$carid = urldecode($request->getParam('carid',''));
    	$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
    	if($carid){
    		$status = $S->getcarInfoByCarId($carid);
    		if($status){
    			echo json_encode($status);
    		}else{
    			echo json_encode(1); //$status为false,那么为新增车辆
    		}
			
    	}

    }

}

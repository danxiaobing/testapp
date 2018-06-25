<?php

class StoragetankController extends Yaf_Controller_Abstract {
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
	public function listAction() {
		$params = array(
			'bar_no'=>'',
			'bar_name'=>''
		);

		$S = new StoragetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$search = array(
			'page' => false,
		);
		$list = $S->searchStoragetankcategory($search);
		$params['storagetankcategorylist'] =  $list['list'];

		$A = new AreaModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$search = array(
			'bar_status' => 1,
			'page' => false,
		);
		$list =  $A->searchArea($search);
		$params['arealist'] = $list['list'];

		$this->getView()->make('storagetank.storagetanklist',$params);
	}

	public function listJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'bar_no'=>$request->getPost('bar_no',''),
			'bar_name' => $request->getPost('bar_name',''),
			'bar_categoryid' => $request->getPost('bar_categoryid','-100'),
			'bar_areaid' => $request->getPost('bar_areaid','-100'),
			'bar_typeid' => $request->getPost('bar_typeid','-100'),
			'bar_status' => $request->getPost('bar_status','-100'),
			'bar_isdel' => $request->getPost('bar_isdel','-100'),
			'pageCurrent' => COMMON :: P(),
			'pageSize' => COMMON :: PR(),
		);
		$S = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $S->searchStoragetank($search);

        echo json_encode($list);

	}

	public function EditAction(){
		$request = $this->getRequest();
		$id = $request->getParam('id',0);
		$clearstoragetank = $request->getParam('clearstoragetank',0);

		$S = new StoragetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		if(!$id){
			$action = "/storagetank/newJson/";
			$params =  array();
			$list=array();
			$params['storagetankgoods']=json_encode($list);

		}

		else{
			if($clearstoragetank==0){
				$action = "/storagetank/editJson/";
				$params = $S->getstoragetankById($id);
				//获取清罐信息
				$search = array (
						'storagetank_sysno'=> $id,
						'pageCurrent' => COMMON :: P(),
						'pageSize' => COMMON :: PR(),
						'orders'  => $request->getPost('orders',''),

				);
				$list = $S->getGoodsStorageInfo($search);
				$params['storagetankgoods'] = json_encode($list);
			}
			else{
				$action = "/storagetank/clearJson/";
				$params = $S->getstoragetankById($id);

				//获取清罐信息
				$search = array (
						'storagetank_sysno'=> $id,
						'pageCurrent' => COMMON :: P(),
						'pageSize' => COMMON :: PR(),
						'orders'  => $request->getPost('orders',''),

				);
				$list = $S->getGoodsStorageInfo($search);
				$params['storagetankgoods'] = json_encode($list);
				$params['clearstoragetank']=$clearstoragetank;
			}
		}

		$search = array(
			'bar_status' => 1,
			'page' => false,
		);
		$list = $S->searchStoragetankcategory($search);
		$params['storagetankcategorylist'] =  $list['list'];

		$A = new AreaModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$search = array(
			'bar_status' => 1,
			'page' => false,
		);
		$list =  $A->searchArea($search);
		$params['arealist'] = $list['list'];

		$G = new GoodsModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$search = array(
				'status' => 1,
				'page' => false,
		);
		$list =  $G->getBaseGoodsAttribute($search);
		$params['goodslist'] = json_encode($list['list']);

		$params['storagetanknaturelist'] = array(
            0=>array('id'=>1,'name'=>'内贸罐'),
            1=>array('id'=>2,'name'=>'外贸罐'),
            2=>array('id'=>3,'name'=>'保税罐'),
           );

		$params['id'] =  $id;
		$params['action'] =  $action;

		$this->getView()->make('storagetank.storagetankedit',$params);
    }

    public function newJsonAction(){
		$request = $this->getRequest();

		$S = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$G = new GoodsModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$input = array(
			'storagetankname'=>  $request->getPost('storagetankname',''),
			'storagetank_category_sysno'=>  $request->getPost('storagetank_category_sysno','1'),
			'area_sysno'=>  $request->getPost('area_sysno','1'),
			'storagetanknature'=>  $request->getPost('storagetanknature','1'),
			'theoreticalcapacity'=>  $request->getPost('theoreticalcapacity','1'),
			'goods_sysno'=>  $request->getPost('goods_sysno',''),
			'density'=>  $request->getPost('density',1),
            'height' => $request->getPost('height',0),
            'diameter' => $request->getPost('diameter',0),
			'actualcapacity'=>  $request->getPost('actualcapacity','1'),
			'status'       	=>  $request->getPost('status','1'),
			'isdel'        	=>  $request->getPost('isdel','0'),
			'created_at'	=> 	'=NOW()',
			'updated_at'	=> 	'=NOW()'
		);

		#后台验证储罐编号
		$search = array (
			'storagetankname'=>$input['storagetankname'],
			'page' => false,
		);
		$storagetankno = $S->searchStoragetank($search);
		if(!empty($storagetankno['list'])){
			COMMON::result(300,'储罐编号不能重复');
			return;
		}

		$cannotstorage = $G->getAttributeByGoodsId($input['goods_sysno']);

		if(!empty($cannotstorage[0])){
			$cannotstoragearr = explode(',',$cannotstorage[0]['storagetank_sysno']);
			foreach($cannotstoragearr as $item){
				if($item == $input['storagetank_category_sysno']){
					COMMON::result(300,'该储罐的材质不能储存该货品！');
					return;
				}
			}
		}
		if($id = $S->addstoragetank($input)){
			$row = $S->getstoragetankById($id);
			COMMON::result(200,'添加成功',$row);
		}else{
			COMMON::result(300,'添加失败');
		}
    }

    public function editJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('id',0);

		$S = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$input = array(
			'storagetankname'=>  $request->getPost('storagetankname',''),
			'storagetank_category_sysno'=>  $request->getPost('storagetank_category_sysno','1'),
			'area_sysno'=>  $request->getPost('area_sysno','1'),
			'storagetanknature'=>  $request->getPost('storagetanknature','1'),
			'theoreticalcapacity'=>  $request->getPost('theoreticalcapacity','1'),
			'density'=>  $request->getPost('density',1),
			'actualcapacity'=>  $request->getPost('actualcapacity','1'),
			'height' => $request->getPost('height',0),
			'diameter' => $request->getPost('diameter',0),
			'status'       	=>  $request->getPost('status','1'),
			'isdel'        	=>  $request->getPost('isdel','0'),
			'updated_at'	=> 	'=NOW()'
		);
		$goods_sysno = $request->getPost('goods_sysno','0');

		#后台验证储罐编号
		$search = array (
				'storagetankname' =>$input['storagetankname'],
				'page' => false,
		);
		$storagetankno = $S->searchStoragetank($search);

		if(!empty($storagetankno['list']) && $storagetankno['list'][0]['sysno']!=$id){
			COMMON::result(300,'储罐编号不能重复');
			return;
		}

		$storagetank =$S->getStoragetankById($id);
		if($storagetank['goods_sysno']!=$goods_sysno){
			COMMON::result(300,'编辑不能修改货品');
			return;
		}
		if($S->updatestoragetank($id,$input)){
			$row = $S->getstoragetankById($id);
			COMMON::result(200,'更新成功',$row);
		}else{
			COMMON::result(300,'更新失败');
		}
    }

	/*
	 * 储罐清理
	 */
	public function clearJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('id',0);

		$S = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$input = array(
				'storagetankname'=>  $request->getPost('storagetankname',''),
				'storagetank_category_sysno'=>  $request->getPost('storagetank_category_sysno','1'),
				'area_sysno'=>  $request->getPost('area_sysno','1'),
				'storagetanknature'=>  $request->getPost('storagetanknature','1'),
				'theoreticalcapacity'=>  $request->getPost('theoreticalcapacity','1'),
				'goods_sysno'=>  $request->getPost('goods_sysno',''),
				'density'=>  $request->getPost('density',1),
				'actualcapacity'=>  $request->getPost('actualcapacity','1'),
                'height' => $request->getPost('height',0),
                'diameter' => $request->getPost('diameter',0),
				'tank_stockqty'       	=> 0,
				'orderinqty'       	=> 0,
				'orderoutqty'       	=> 0,
				'status'       	=>  $request->getPost('status','1'),
				'isdel'        	=>  $request->getPost('isdel','0'),
				'updated_at'	=> 	'=NOW()'
		);

		#后台验证储罐编号
		$search = array (
				'storagetankname' =>$input['storagetankname'],
				'pageCurrent'=>COMMON::P(),
				'pageSize'=>COMMON::PR(),
				'orders'=> $request->getPost('orders','')
		);
		$storagetankno = $S->searchStoragetank($search);
		if(!empty($storagetankno['list']) && $storagetankno['list'][0]['sysno']!=$id){
			COMMON::result(300,'储罐编号不能重复');
			return;
		}

		#后台验证该储罐是否为空罐
		$tankinfo = $S->getStoragetankById($id);

		if($tankinfo['tank_stockqty']>0){
			COMMON::result(300,'该储罐不为空');
			return;
		}

		if($S->updatestoragetank($id,$input)){
			$row = $S->getstoragetankById($id);

			$data = array(
					'storagetank_sysno'=>  $id,
					'goods_sysno'=>  $tankinfo['goods_sysno'],
					'cleartankdate'=>  $request->getPost('cleartankdate',''),
					'created_at'=>  '=NOW()',
					'updated_at'=>  '=NOW()',
			);

			if($id=$S->addStoragetankGoods($data)){
				COMMON::result(200,'清罐成功',$row);
			}
		}else{
			COMMON::result(300,'清罐失败');
		}
	}

	/*
	 * 批量修改储罐资料状态
	 */
	public function setstoragetankstatusAction(){
		$request = $this->getRequest();
		$status = $request->getParams('status');
		$status = intval($status['status']);
		$qus = $request->getPost('qus');

		$S = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$input = array(
				'status'  =>  $status,
				'updated_at'=>  '=NOW()'
		);

		$count=0;
		foreach($qus as $item){
			if($S->updatestoragetank($item,$input))
				$count++;
		}
		if($count==count($qus)){
			COMMON::result(200,'操作成功');
		}else{
			COMMON::result(300,'操作失败');
		}
	}

	public function delJsonAction(){
    	$request = $this->getRequest();
		$id = $request->getPost('sysno',0);
		$ids = explode(',',$id);
		if(count($ids)>1){
			COMMON::result(300,'一次只能删除一个储罐');
			return ;
		}

		$S = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$input = array(
			'storagetankname'=> $request->getPost('storagetankname','').'del'.time(),
			'isdel' => 1,
			'updated_at'=>'=NOW()'
		);

		$search = array(
			'storagetank_sysno'=>$id
		);

		$C = new ContractModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$Contractdata = $C->searchContractStorage($search);

		if(count($Contractdata)){
			COMMON::result(300,'该储罐已被合同引用，不能删除');
			return ;
		}

		$B = new BookshipinModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$bookindata = $B->searchBookinStorage($search);

		if(count($bookindata)){
			COMMON::result(300,'该储罐已被入库预约单引用，不能删除');
			return ;
		}

		if($S->updatestoragetank($id,$input)){
			COMMON::result(200,'删除成功');
		}else{
			COMMON::result(300,'删除失败');
		}
    }

	public function categorylistAction() {
		$params = array(
			'bar_no'=>'',
			'bar_name'=>''
		);

		$this->getView()->make('storagetank.storagetankcategorylist',$params);
	}

	public function categorylistJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'bar_name' => $request->getPost('bar_name',''),
			'bar_status' => $request->getPost('bar_status','-100'),
			'bar_isdel' => $request->getPost('bar_isdel','-100'),
			'pageCurrent' => COMMON :: P(),
			'pageSize' => COMMON :: PR(),
			'orders'  => $request->getPost('orders',''),

		);
		$C = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $C->searchStoragetankcategory($search);

        echo json_encode($list);

	}

	public function categoryEditAction(){
		$request = $this->getRequest();
		$id = $request->getParam('id',0);

		$module = array();

		$S = new StoragetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		if(!$id){
			$action = "/storagetank/categorynewJson/";
			$params =  array();
		}

		else{
			$action = "/storagetank/categoryeditJson/";
			$params = $S->getstoragetankcategoryById($id);
		}

		$params['id'] =  $id;
		$params['action'] =  $action;

		$this->getView()->make('storagetank.storagetankcategoryedit',$params);
    }

	public function categorynewJsonAction(){
		$request = $this->getRequest();

		$C = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$input = array(
			'storagetank_categoryname'=>  $request->getPost('storagetank_categoryname',''),
			'status'       	=>  $request->getPost('status','1'),
			'isdel'        	=>  $request->getPost('isdel','0'),
			'created_at'	=> 	'=NOW()',
			'updated_at'	=> 	'=NOW()'
		);

		if($id = $C->addstoragetankcategory($input)){
			$row = $C->getstoragetankcategoryById($id);
			COMMON::result(200,'添加成功',$row);
		}else{
			COMMON::result(300,'添加失败');
		}
    }

    public function categoryeditJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('id',0);

		$C = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$input = array(
			'storagetank_categoryname'=>  $request->getPost('storagetank_categoryname',''),
			'status'       	=>  $request->getPost('status','1'),
			'isdel'        	=>  $request->getPost('isdel','0'),
			'updated_at'	=> 	'=NOW()'
		);
 
		if($C->updatestoragetankcategory($id,$input)){
			$row = $C->getstoragetankcategoryById($id);
			COMMON::result(200,'更新成功',$row);
		}else{
			COMMON::result(300,'更新失败');
		}
    }

	/*
	 * 批量修改储罐材质状态
	 */
	public function setstoragetankcategorystatusAction(){
		$request = $this->getRequest();
		$status = $request->getParams('status');
		$status = intval($status['status']);
		$qus = $request->getPost('qus');

		$S = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$input = array(
				'status'  =>  $status,
				'updated_at'=>  '=NOW()'
		);

		$count=0;
		foreach($qus as $item){
			if($S->updatestoragetankcategory($item,$input))
				$count++;
		}
		if($count==count($qus)){
			COMMON::result(200,'操作成功');
		}else{
			COMMON::result(300,'操作失败');
		}
	}

	public function categorydelJsonAction(){
    	$request = $this->getRequest();
		$id = $request->getPost('sysno',0);

		$C = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$input = array(
			'isdel' => 1
		);

		if($C->updatestoragetankcategory($id,$input)){
			COMMON::result(200,'删除成功');
		}else{
			COMMON::result(300,'删除失败');
		}
    }

	/*
	 * 查询可包罐储罐信息
	 */
	public function getBGstoragetankAction(){
		$S = new StoragetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$data = $S->getstoragetankinfo();
		echo json_encode($data);
	}

	public function getstoragetankAction()
	{
		$search = array (
			'bar_status' => 1,
			'bar_isdel' => 0,
			'page' => false,
		);
		$S = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $S->searchStoragetank($search);
		
		$storagetankData = array();
		foreach ($list['list'] as $key => $value) {
			$storagetankData[$key]['storagetank_sysno'] = $value['sysno'];
			$storagetankData[$key]['storagetankableqty'] = ($value['tank_stockqty'] - $value['orderoutqty']) < 0 ? 0 : ($value['tank_stockqty'] - $value['orderoutqty']);
			$storagetankData[$key]['storagetankgoods_sysno'] = $value['goods_sysno'];
			$storagetankData[$key]['storagetankgoodsname'] = $value['goodsname'];
			$storagetankData[$key]['areaname'] = $value['areaname'];
			$storagetankData[$key]['storagetankname'] = $value['storagetankname'];
			$storagetankData[$key]['tank_stockqty'] = $value['tank_stockqty'];
			$storagetankData[$key]['orderoutqty'] = $value['orderoutqty'];

		}
        echo json_encode($storagetankData);
	}
}

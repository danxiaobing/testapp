<?php
/**
 * 库存查询
 * User: josy
 * Date: 2016/11/22 0022
 * Time: 9:13
 */
class BookoutController extends Yaf_Controller_Abstract {
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
	public function carlistAction() {
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
			'page' => false,
		);

		$list = $C->searchCustomer($search);
		$params['customerlist'] =  $list['list'];
		$goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();
		$this->getView()->make('bookcarout.bocarlist',$params);
	}

	public function listJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'bookingoutno' => $request->getPost('bookingoutno',''),
			'begin_time'=> $request->getPost('begin_time',''),
			'end_time' => $request->getPost('end_time',''),
			'bar_name' => $request->getPost('bar_name',''),
			'bar_status'=>$request->getPost('bar_status',''),
			'bar_docsource'=>$request->getPost('bar_docsource',''),
			'bar_receivenumber' => $request->getPost('bar_receivenumber',''),
			'bar_goodsname' => $request->getPost('bar_goodsname',''),
			'stockouttype' => 2,
			'pageCurrent' => COMMON:: P(),
			'pageSize' => COMMON:: PR(),
			'orders'  => 'created_at desc',
		);

		$B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $B->searchBookout($search);
		foreach ($list['list'] as $key => $value) {
			$list['list'][$key]['unitname'] = '吨';
			if($value['bookingoutstatus'] != 6){
				$receiveend = strtotime($value['receiveend']);
	            $now = strtotime(date('Y-m-d',time()));
	            if ($now > $receiveend) {
	            	$list['list'][$key]['receiveover'] = 1;
	            }else{
	            	$list['list'][$key]['receiveover'] = 0;
	            }
	        }
		}

		echo json_encode($list);

	}

	public function carauditAction()
	{
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
			'page' => false,
		);

		$list = $C->searchCustomer($search);
		$params['customerlist'] =  $list['list'];
		$goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();
		$this->getView()->make('bookcarout.bocarauditlist',$params);
	}

	public function carauditJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'bookingoutno' => $request->getPost('bookingoutno',''),
			'begin_time'=>$request->getPost('begin_time',''),
			'end_time' => $request->getPost('end_time',''),
			'bar_name' => $request->getPost('bar_name',''),
			'bar_status'=>$request->getParam('bar_status',4),
			'bar_docsource' => $request->getPost('bar_docsource',''),
			'bar_receivenumber' => $request->getPost('bar_receivenumber',''),
			'bar_goodsname' => $request->getPost('bar_goodsname',''),
			'stockouttype' => 2,
			'pageCurrent' => COMMON:: P(),
			'pageSize' => COMMON:: PR(),
			'orders'  => 'created_at desc',
		);

		$B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $B->searchBookout($search);
		foreach ($list['list'] as $key => $value) {
			$list['list'][$key]['unitname'] = '吨';
		}
		echo json_encode($list);

	}

	public function detaillistJsonAction(){
		$request = $this->getRequest();
		$id = $request->getParam('id',0);
		$B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$search = array(
			'bookingout_sysno' =>	$id,
			'page' => false
		);
		$detailData = $B->getBookoutDetailList($search);

		echo json_encode($detailData['list']) ;
	}

	public function carEditAction(){
		$request = $this->getRequest();
		$id = $request->getParam('id','0');
		$type = $request->getParam('type','');
		$bookout_sysno = $request->getParam('bookout_sysno',0);

		if(!isset($id)) {
			$id = 0;
		}

		$B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$E = new EmployeeModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$S = new StockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$ST = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		if(!$id){
			$action = "/bookout/carNewJson/";
			$params =  array();
			$params['detaillist'] = json_encode(array());
			$params['carlist'] = json_encode(array());
		}
		else{
			$action = "/bookout/carEditJson/";
			$params = $B->getBookoutById($id);

			if ($params['bookingoutstatus'] != 2 && $params['bookingoutstatus'] != 7 && $type != 'audit' && $type != 'addatt' && $type != 'view') {
                COMMON::result(300, '非暂存或退回状态不能编辑！');
                return;
            }
			
        	$receiveend = strtotime($params['receiveend']);
        	$now = strtotime(date('Y-m-d',time()));
        	if ($now > $receiveend) {
	        	$params['receiveover'] = '是';
	        }else{
	        	$params['receiveover'] = '否';
	        }
            
			$search = array(
				'bookingout_sysno' => $params['sysno'],
				'page' => false
			);
			
			$detailData =  $B->getBookoutDetailList($search);
			
			foreach ($detailData['list'] as $key => $value) {
				if($value['stocktype'] == 1){
					$stockinfo = $S->getElementById(array($value['stock_sysno']));
					$detailData['list'][$key]['instockqty'] = $stockinfo[0]['instockqty'];
					$detailData['list'][$key]['introduceqty'] = '--';
					$detailData['list'][$key]['transInstockqty'] = $stockinfo[0]['instockqty'];
					if( $stockinfo[0]['doctype'] == 3 ){
						$detailData['list'][$key]['instockqty'] = '--';
					}
					$detailData['list'][$key]['ableqty'] = $stockinfo[0]['stockqty']-$stockinfo[0]['clockqty'];
				}elseif($value['stocktype'] == 2){
					$introduceDetailInfo = $I->getIntroduceDetailById(intval($value['stock_sysno']));
					$detailData['list'][$key]['instockqty'] = '--';
					$detailData['list'][$key]['introduceqty'] = $introduceDetailInfo['takegoodsnum'];
					$detailData['list'][$key]['ableqty'] = $introduceDetailInfo['untakegoodsnum']-$introduceDetailInfo['bookingqty'];
				}
				
				
				$stinfo = $ST->getStoragetankById($value['storagetank_sysno']);
				$detailData['list'][$key]['storagetankableqty'] = $stinfo['tank_stockqty'] - $stinfo['orderoutqty'] <0 ? 0 : $stinfo['tank_stockqty'] - $stinfo['orderoutqty'] ;
				$detailData['list'][$key]['unitname'] = '吨';
			}
			$params['detaillist'] = json_encode($detailData['list']) ;

			$carData =  $B->searchBookoutCar($search);
			$params['carlist'] = json_encode($carData['list']) ;

			$A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

			$attach = $A->getAttachByMAS('bookout','car',$id);
			if( is_array($attach) && count($attach)){
				$files = array();
				foreach ($attach as $file){
					$files[] = $file['sysno'];
				}

				$params['uploaded']  =  join(',',$files);
			}

			$params['attach']  = $attach;

			$params['samples']  =  $A->getAttachByMAS('customer','customerlading',$params['customer_sysno']);

		}

        //有效合同客户列表
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
			'page' => false,
		);

		$list = $C->searchCustomer($search);
		$params['customerlist'] =  $list['list'];

		$list = $E->searchEmployee($search);
		$params['employeelist'] =  $list['list'];

		$params['id'] =  $id;
		$params['action'] =  $action;
		$params['status'] = COMMON::getExamBookoutCarStatus($params['bookingoutstatus']);
		$params['type'] = $type;
		$this->getView()->make('bookcarout.caredit',$params);
    }
   
	public function bocardetaileditAction(){
		$request = $this->getRequest();
		$cid = $request->getParam('cid','0');
		$Obj = $request->getPost('Obj',array());
		
		// $params['Obj'] = json_encode($Obj);

		//操作状态 添加|删除
		$handlestatus = $request->getParam('handlestatus','');
		$params['handlestatus'] = $handlestatus;

		$selectedDatas = $request->getPost('selectedDatasArray',array());
		$params['selectedDatas'] = $selectedDatas;
		$params['selectedDatas']['unitname'] = '吨';
		// $params['selectedData'] = json_encode($selectedDatas);

        $B = new StockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $stocklist = [];
        $stockdata = $B -> getAllStock($cid);
        foreach ($stockdata as $key => $value) {
        	if($value['stockqty'] <= 0){
        		unset($stockdata[$key]);
        		continue;
        	}
        	unset($stockdata[$key]['storagetank_sysno']);
        	if($value['ableqty'] != 0){
        		$value['transInstockqty'] = $value['instockqty'];  //货权转移对应的入库单的入库量
	        	if($value['doctype'] == 3){
	        		$value['instockqty'] = '--';
	        	}
        		$stocklist[] = $value;
        	}
        }

		$params['stocklist'] = json_encode($stocklist);

		if(count($stocklist) > 0){
			$this->getView()->make('bookcarout.bocardetailedit',$params);
		}else{
			COMMON::result(300,'该客户没有库存');
		}
	}

	public function bocarcareditAction(){
		$request = $this->getRequest();
		$selectedDatas = $request->getPost('selectedDatasArray',array());
		$params['selectedDatas'] = $selectedDatas;

		$handlestatus = $request->getParam('handlestatus','');
		$params['handlestatus'] = $handlestatus;
		
		$this->getView()->make('bookcarout.bocarcaredit',$params);
	}

	public function customerSampleJsonAction(){
		$request = $this->getRequest();
		$cid = $request->getPost('cid','0');

		$A = new AttachmentModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$sample = $A->getAttachByMAS('customer','customerlading',$cid);

		echo json_encode($sample);
	}

    public function carNewJsonAction()
    {
		$request = $this->getRequest();
		$detaildata = $request->getPost('detaildata',"");
		$cardata = $request->getPost('cardata',"");
		$detaildata = json_decode($detaildata,true);
		$cardata = json_decode($cardata,true);

		if(count($detaildata)==0) {
			COMMON::result(300,'预约单明细不能为空');
			return;
		}

		if(count($cardata)==0) {
			COMMON::result(300,'预约车辆信息不能为空');
			return;
		}

		$B = new BookoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
		
		$input = array(
			'bookingoutno' => $request->getPost('bookingoutno', ''),
			'bookingoutdate' => $request->getPost('bookingoutdate', ''),
			'customer_sysno' => $request->getPost('customer_sysno', ''),
			'customer_name' => $request->getPost('customer_name', ''),
			'docsource' => $request->getPost('docsource', ''),
			'receivestart' => $request->getPost('receivestart', ''),
			'receiveend' => $request->getPost('receiveend', ''),
			'receiveover' => $request->getPost('receiveover', 0) == '是' ? 1 : 0,
			'receivenumber' => $request->getPost('receivenumber', ''),
			'receiveunitname' => $request->getPost('receiveunitname', ''),
			'stockouttype' => 2,
			'bookingoutstatus' => $request->getPost('bookingoutstatus', '1'),
			'inshipname' => $request->getPost('stockinshipname',''),
			'status' => 1,
			'isdel' => 0,
			'created_at' => '=NOW()',
			'updated_at' => '=NOW()'
		);
		if(isset($input['receiveend']) && $input['receiveend'] != ''){
			//提货区间判断
			$result = $B->checkTimeRange($detaildata,$input['receivestart'],$input['receiveend']);
			if($result){
				COMMON::result(300,$result['msg']);
				return;
			}
		}

		if(count($cardata)>=2){
            for($i=0;$i<count($cardata);$i++){
                for($j=$i+1;$j<count($cardata);$j++){
                    if($cardata[$i]['carid']==$cardata[$j]['carid']&&
                        $cardata[$i]['carname']==$cardata[$j]['carname']){
                        COMMON::result(300, '车辆信息不能重复');
                        return;
                    }
                }
            }
        }

		if ($id = $B->addCarBookout($input,$detaildata,$cardata)) {
			$attach =  $request->getPost('attachment',array());

			if(count($attach) > 0){
				$A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
				$res = $A->addAttachModelSysno($id,$attach);
				if(!$res){
					COMMON::result(300,'添加附件失败');
					return;
				}
			}
			$row = $B->getBookoutById($id);
			COMMON::result(200, '添加成功', $row);
		} else {
			COMMON::result(300, '添加失败');
		}
    }

    public function carEditJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('id', 0);
		$detaildata = $request->getPost('detaildata',"");
		$cardata = $request->getPost('cardata',"");
		$detaildata = json_decode($detaildata,true);
		$cardata = json_decode($cardata,true);

		if(count($detaildata)==0) {
			COMMON::result(300,'预约单明细不能为空');
			return;
		}

		if(count($cardata)==0) {
			COMMON::result(300,'预约车辆信息不能为空');
			return;
		}

		$B = new BookoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
		
		$input = array(
			'bookingoutdate' => $request->getPost('bookingoutdate', ''),
			'customer_sysno' => $request->getPost('customer_sysno', ''),
			'customer_name' => $request->getPost('customer_name', ''),
			'docsource' => $request->getPost('docsource', '1'),
			'stockouttype' => 2,
			'bookingoutstatus' => $request->getPost('bookingoutstatus', '1'),
			'receivestart' => $request->getPost('receivestart', ''),
			'receiveend' => $request->getPost('receiveend', ''),
			'receiveover' => $request->getPost('receiveover', 0) == '是' ? 1 : 0,
			'receivenumber' => $request->getPost('receivenumber', ''),
			'receiveunitname' => $request->getPost('receiveunitname', ''),
			'takecompany' => $request->getPost('takecompany', ''),
            'inshipname' => $request->getPost('stockinshipname', ''),
			'status' => 1,
			'isdel' => 0,
			'updated_at' => '=NOW()'
		);
		if(isset($input['receiveend']) && $input['receiveend'] != ''){
			//提货区间判断
			$result = $B->checkTimeRange($detaildata,$input['receivestart'],$input['receiveend']);
			if($result){
				COMMON::result(300,$result['msg']);
				return;
			}
		}
		if(count($cardata)>=2){
            for($i=0;$i<count($cardata);$i++){
                for($j=$i+1;$j<count($cardata);$j++){
                    if($cardata[$i]['carid']==$cardata[$j]['carid']&&
                        $cardata[$i]['carname']==$cardata[$j]['carname']){
                        COMMON::result(300, '车辆信息不能重复');
                        return;
                    }
                }
            }
        }
        
		if ($B->updateCarBookout($id,$input,$detaildata,$cardata)) {
			$row = $B->getBookoutById($id);
			COMMON::result(200, '更新成功', $row);
		} else {
			COMMON::result(300, '更新失败');
		}
    }

	public function  examJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('id',0);
		$examstep = $request->getPost('examstep',0);
		$exammarks = $request->getPost('exammarks','');
		$examidentify = $request->getPost('examidentify','');
		//提单退回
		$type = $request->getParam('type','');

		if($id == 0 || $examstep == 0 ){
			COMMON::result(300,'审核信息错误');
			return false;
		}
		if ( $examstep == 7 && $exammarks=='') {
			COMMON::result(300,'请填写意见');
			return false;
		}
		$B = new BookoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
		$bookoutinfo = $B->getBookoutById($id);
		if (!$bookoutinfo) {
			COMMON::result(300,'预约单信息错误');
			return;
		}
		
		$L = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$user = Yaf_Registry::get(SSN_VAR);

		if($examstep == 7){
			$data = array(
				'bookingoutstatus' => 7,
                'auditreason' => $exammarks
			);
			if ($examidentify == 'back') {
				
				if ($bookoutinfo && $bookoutinfo['issaveorder'] == 1) {
					COMMON::result(300, '退回失败，该预约单已被出库单引用');
					return;
				}
				$data['examidentify'] = 'back';
                $data['backreason']  = $exammarks;
                unset($data['auditreason']);
			}
			$res = $B->updateBookoutData($id,$data);
			
			#库存管理业务操作日志
			if($res){

				$input= array(
					'doc_sysno'  =>  $id,
					'doctype'  =>  2,
					'opertype'  => 6 ,
					'operemployee_sysno' => $user['employee_sysno'],
					'operemployeename' => $user['employeename'],
					'opertime'    => '=NOW()',
					'operdesc'  =>  $exammarks
				);
				if($bookoutinfo['stockouttype'] == 3){
					$input['doctype'] = 24;
				}
				$L->addDocLog($input);
				//更新消息提醒
				$S = new SystemModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
				$S->updateMessage($id);
				COMMON::result(200, '操作成功');
				return;
			}else{
				COMMON::result(300, '操作失败');
				return;
			}
		}elseif ($examstep == 5){

			if($bookoutinfo['docsource'] == 2){
				sleep(2);
			}

			$msg='';
			$res = $B->examBookout($id,$msg, $exammarks);

			if($res){
				$input= array(
					'doc_sysno'  =>  $id,
					'doctype'  =>  2,
					'opertype'  => 4 ,
					'operemployee_sysno' => $user['employee_sysno'],
					'operemployeename' => $user['employeename'],
					'opertime'    => '=NOW()',
					'operdesc'  =>  $exammarks
				);
				if($bookoutinfo['stockouttype'] == 3){
					$input['doctype'] = 24;
				}
				if ($bookoutinfo['docsource'] == '2') {
					COMMON::editStockOutStatus($bookoutinfo['bookingoutno']);
				}

				$L->addDocLog($input);

				COMMON::result(200, '操作成功');
				return;
			}else{
				COMMON::result(300, $msg);
				return;
			}

		}elseif ($examstep == 8) {

			$data = array(
				'bookingoutstatus' => 8,
				'rejectreason' => $exammarks,
			);
			
			$res  = $B->bookoutReject($id,$data);
			
			#库存管理业务操作日志
			if($res){
				$result = COMMON::editStockOutReject($bookoutinfo['bookingoutno'], $exammarks);
				if ($result['code'] != 200) {
					COMMON::result(300, $result['message']);
					return;
				}
				$input= array(
					'doc_sysno'  =>  $id,
					'doctype'  =>  2,
					'opertype'  => 8 ,
					'operemployee_sysno' => $user['employee_sysno'],
					'operemployeename' => $user['employeename'],
					'opertime'    => '=NOW()',
					'operdesc'  =>  $exammarks
				);
				if($bookoutinfo['stockouttype'] == 3){
					$input['doctype'] = 24;
				}
				$L->addDocLog($input);

				COMMON::result(200, '操作成功');
				return;
			}else{
				COMMON::result(300, '操作失败');
				return;
			}
		}

		COMMON::result(300, '操作失败');
		return;
	}

	public function shiplistAction() {
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
			'page' => false,
		);

		$list = $C->searchCustomer($search);
		$params['customerlist'] =  $list['list'];
		$goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();
		$this->getView()->make('bookshipout.boshiplist',$params);
	}

	public function shiplistJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'bookingoutno'=>$request->getPost('bookingoutno',''),
			'begin_time' => $request->getPost('begin_time',''),
			'end_time' => $request->getPost('end_time',''),
			'bar_name' => $request->getPost('bar_name',''),
			'bar_status'=>$request->getPost('bar_status',''),
			'bar_docsource' => $request->getPost('bar_docsource',''),
			'bar_receivenumber' => $request->getPost('bar_receivenumber',''),
			'bar_goodsname' => $request->getPost('bar_goodsname',''),
			'stockouttype' => 1,
			'pageCurrent' => COMMON :: P(),
			'pageSize' => COMMON :: PR(),
			'orders'  => 'created_at desc',
		);

		$B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $B->searchBookout($search);
		foreach ($list['list'] as $key => $value) {
			$list['list'][$key]['unitname'] = '吨';
			if($value['bookingoutstatus'] != 6){
				$receiveend = strtotime($value['receiveend']);
	            $now = strtotime(date('Y-m-d',time()));
	            if ($now > $receiveend) {
	            	$list['list'][$key]['receiveover'] = 1;
	            }else{
	            	$list['list'][$key]['receiveover'] = 0;
	            }
	        }
		}
		echo json_encode($list);

	}

	public function shipauditAction() {
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
			'page' => false,
		);

		$list = $C->searchCustomer($search);
		$params['customerlist'] =  $list['list'];
		$goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();
		$this->getView()->make('bookshipout.boshipauditlist',$params);
	}

	public function shipauditJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'bookingoutno'=>$request->getPost('bookingoutno',''),
			'begin_time' => $request->getPost('begin_time',''),
			'end_time' => $request->getPost('end_time',''),
			'bar_name' => $request->getPost('bar_name',''),
			'bar_status'=>$request->getParam('bar_status',4),
			'bar_docsource'=>$request->getPost('bar_docsource',''),
			'bar_receivenumber' => $request->getPost('bar_receivenumber',''),
			'bar_goodsname' => $request->getPost('bar_goodsname',''),
			'stockouttype' => 1,
			'pageCurrent' => COMMON :: P(),
			'pageSize' => COMMON :: PR(),
			'orders'  => 'created_at desc',
		);

		$B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $B->searchBookout($search);
		foreach ($list['list'] as $key => $value) {
			$list['list'][$key]['unitname'] = '吨';
			
			$receiveend = strtotime($value['receiveend']);
            $now = strtotime(date('Y-m-d',time()));
            if ($now > $receiveend) {
            	$list['list'][$key]['receiveover'] = 1;
            }else{
            	$list['list'][$key]['receiveover'] = 0;
            }
				
		}
		echo json_encode($list);

	}

	public function shipEditAction(){
		$request = $this->getRequest();
		$id = $request->getParam('id','0');
		$type = $request->getParam('type','');
		if(!isset($id)) {
			$id = 0;
		}

		$B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$E = new EmployeeModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		if(!$id){
			$action = "/bookout/shipNewJson/";
			$params =  array();
			$params['detaillist'] = json_encode(array());
		}
		else{
			$action = "/bookout/shipEditJson/";
			$params = $B->getBookoutById($id);

			$status = $params['bookingoutstatus'];
  			if ($status !=2 && $status !=7 && $type != 'audit' && $type != 'view' && $type != 'addatt' && $type != 'sendback') {
                COMMON::result(300, '非暂存或退回状态不能编辑！');
                return;
            }
            $receiveend = strtotime($params['receiveend']);
            $now = strtotime(date('Y-m-d',time()));
            if ($now > $receiveend) {
            	$params['receiveover'] = '是';
            }else{
            	$params['receiveover'] = '否';
            }

			$search = array(
				'bookingout_sysno' =>	$params['sysno'],
				'page' => false
			);

			$S = new StockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
			$G = new GoodsModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
			$ST = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
			$I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

			$detailData =  $B->getBookoutDetailList($search);

			foreach ($detailData['list'] as $key => $value) {	
				if($value['stocktype'] == 1){
					$stockinfo = $S->getElementById(array($value['stock_sysno']));
					$detailData['list'][$key]['instockqty'] = $stockinfo[0]['instockqty'];
					$detailData['list'][$key]['introduceqty'] = '--';
					$detailData['list'][$key]['transInstockqty'] = $stockinfo[0]['instockqty'];
					$detailData['list'][$key]['ableqty'] = $stockinfo[0]['stockqty']-$stockinfo[0]['clockqty'];
					if($stockinfo[0]['doctype'] == 3){
						$detailData['list'][$key]['instockqty'] = '--';
					}
				}elseif($value['stocktype'] == 2){
					$introduceDetailInfo = $I->getIntroduceDetailById(intval($value['stock_sysno']));
					$detailData['list'][$key]['instockqty'] = '--';
					$detailData['list'][$key]['introduceqty'] = $introduceDetailInfo['takegoodsnum'];
					$detailData['list'][$key]['ableqty'] = $introduceDetailInfo['untakegoodsnum']-$introduceDetailInfo['bookingqty'];
				}
				
				$stinfo = $ST->getStoragetankById($value['storagetank_sysno']);
				$detailData['list'][$key]['storagetankableqty'] = $stinfo['tank_stockqty'] - $stinfo['orderoutqty'] <0 ? 0 : $stinfo['tank_stockqty'] - $stinfo['orderoutqty'] ;
				$detailData['list'][$key]['unitname'] = '吨';
			}

			$params['detaillist'] = json_encode($detailData['list']) ;

			$A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));	

			$attach = $A->getAttachByMAS('bookout','ship_uploader',$id);
			if( is_array($attach) && count($attach)){
				$files = array();
				foreach ($attach as $file){
					$files[] = $file['sysno'];
				}
				$params['uploaded1']  =  join(',',$files);
			}

			$params['attach']  = $attach;

			$params['samples']  =  $A->getAttachByMAS('customer','customerlading',$params['customer_sysno']);

		}

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
			'page' => false,
		);

		$list = $C->searchCustomer($search);
		$params['customerlist'] =  $list['list'];

		$list = $E->searchEmployee($search);
		$params['employeelist'] =  $list['list'];

		$params['id'] =  $id;
		$params['action'] =  $action;
		$params['status'] = COMMON::getExamStatus($params['bookingoutstatus']);
		$params['type'] =  $type;
		$this->getView()->make('bookshipout.shipedit',$params);
	}

	public function boshipdetaileditAction(){
		$request = $this->getRequest();
		$cid = $request->getParam('cid','0');
		// $Obj = $request->getPost('Obj',array());

		// if (!empty($Obj)) {
		// 	$params['Obj'] = json_encode($Obj);
		// }
		
		$selectedDatas = $request->getPost('selectedDatasArray',array());	
		$params['selectedDatas'] = $selectedDatas;
		$params['selectedDatas']['unitname'] = '吨';
		// $params['selectedData'] = json_encode($selectedDatas);
		$params['handlestatus'] = $request->getParam('handlestatus','0');

        $B = new StockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $S = new SupplierModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $G = new GoodsModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $Q = new QualityModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));	

        $stocklist = [];
        $stockdata = $B -> getAllStock($cid);

        foreach ($stockdata as $key => $value) {
        	if($value['stockqty'] <= 0){
        		unset($stockdata[$key]);
        		continue;
        	}
        	unset($stockdata[$key]['storagetank_sysno']);
        	if($value['ableqty'] != 0){
	        	$value['transInstockqty'] = $value['instockqty'];  //货权转移对应的入库单的入库量
	        	if($value['doctype'] == 3){
	        		$value['instockqty'] = '--';
	        	}
        		$stocklist[] = $value;
        	}
        	
        }

        $params['stocklist'] = json_encode($stocklist);
        $search = array(
            'bar_status'=>1,
            // 'pageSize'=>20,
            // 'pageCurrent'=>1
            'page' => false,
            );

        $list = $S->searchShipList($search);
        $params['shiplist'] = $list['list'];

        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $quality->getList($search, 99, 1);
        $params['goodsqualitylist'] = $list['list'];

		if(count($stocklist) > 0){
			$this->getView()->make('bookshipout.boshipdetailedit',$params);
		}else{
			COMMON::result(300,'该客户没有库存');
		}

	}

	public function shipNewJsonAction()
	{
		$request = $this->getRequest();
		$detaildata = $request->getPost('detaildata',"");
		$detaildata = json_decode($detaildata,true);
		
		if(count($detaildata)==0) {
			COMMON::result(300,'预约单明细不能为空');
			return;
		}
		$B = new BookoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

		$input = array(
			'bookingoutno' => $request->getPost('bookingoutno', ''),
			'bookingoutstatus' => $request->getPost('bookingoutstatus',''),
			'bookingoutdate' => $request->getPost('bookingoutdate', ''),
			'customer_sysno' => $request->getPost('customer_sysno', ''),
			'customer_name' => $request->getPost('customer_name', ''),
			'docsource' => $request->getPost('docsource', '1'),
			'stockouttype' => 1,
			'cs_employee_sysno' => $request->getPost('cs_employee_sysno', 0),
			'cs_employeename' => $request->getPost('cs_employeename', ''),
			'shipproxyname' => $request->getPost('shipproxyname', ''),
			'businesscheckunitname' => $request->getPost('businesscheckunitname', ''),
			'receiveunitname' => $request->getPost('receiveunitname', ''),
			'receivestart' => $request->getPost('receivestart', ''),
			'receiveend' => $request->getPost('receiveend', ''),
			'receiveover' => $request->getPost('receiveover', 0) == '是' ? 1 : 0,
			'receivenumber' => $request->getPost('receivenumber', ''),
			'bookingoutstatus' => $request->getPost('bookingoutstatus', '1'),
			'status' => 1,
			'isdel' => 0,
			'created_at' => '=NOW()',
			'updated_at' => '=NOW()',
			'ispipelineorder' => $request->getPost('ispipelineorder', 2),
            'isberthorder' => $request->getPost('isberthorder', 2),
            'isqualitycheck' => $request->getPost('isqualitycheck', 1),
		);
		if ($input['cs_employee_sysno'] == 0) {
			unset($input['cs_employee_sysno']);
			unset($input['cs_employeename']);
		}

		if ($id = $B->addShipBookout($input,$detaildata)) {
			$attach =  $request->getPost('attachment',array());
			if(count($attach) > 0){
				$A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
				$res = $A->addAttachModelSysno($id,$attach);
				if(!$res){
					COMMON::result(300,'添加附件失败');
					return;
				}
			}
			$row = $B->getBookoutById($id);
			COMMON::result(200, '添加成功', $row);
		} else {
			COMMON::result(300, '添加失败');
		}
	}

	public function shipEditJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('id', 0);
		$detaildata = $request->getPost('detaildata',"");
		$detaildata = json_decode($detaildata,true);
		if(count($detaildata)==0) {
			COMMON::result(300,'预约单明细不能为空');
			return;
		}

		$B = new BookoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
		$bookoutinfo = $B->getBookoutById($id);
		
		$input = array(
			'bookingoutdate' => $request->getPost('bookingoutdate', ''),
			'customer_sysno' => $request->getPost('customer_sysno', ''),
			'customer_name' => $request->getPost('customer_name', ''),
			'docsource' => $request->getPost('docsource', '1'),
			'stockouttype' => 1,
			'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
			'cs_employeename' => $request->getPost('cs_employeename', '') == '请选择'? '' : $request->getPost('cs_employeename', ''),
			'isbusinesscheck' => 1,
			'shipproxyname' => $request->getPost('shipproxyname', ''),
			'businesscheckunitname' => $request->getPost('businesscheckunitname', ''),
			'receiveunitname' => $request->getPost('receiveunitname', ''),
			'receivestart' => $request->getPost('receivestart', ''),
			'receiveend' => $request->getPost('receiveend', ''),
			'receiveover' => $request->getPost('receiveover', '') == '是' ? 1 : 0,
			'receivenumber' => $request->getPost('receivenumber', ''),
			'bookingoutstatus' => $request->getPost('bookingoutstatus', '1'),
			'status' => 1,
			'isdel' => 0,
			'updated_at' => '=NOW()',
			'ispipelineorder' => $request->getPost('ispipelineorder', 2),
            'isberthorder' => $request->getPost('isberthorder', 2),
            'isqualitycheck' => $request->getPost('isqualitycheck', 1),
		);

		if ($B->updateShipBookout($id,$input,$detaildata)) {

			$row = $B->getBookoutById($id);
			COMMON::result(200, '更新成功', $row);
		} else {
			COMMON::result(300, '更新失败');
		}
	}

	public function delJsonAction(){
    	$request = $this->getRequest();
		$id = $request->getPost('sysno',0);

		$B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$params = $B->getBookoutById($id);

		if($params['bookingoutstatus'] != 2 && $params['bookingoutstatus'] != 7) {
	        COMMON::result(300, '非暂存和退回状态不能删除！');
	        return false;
        }
        if($params['docsource'] == 2 ) {
	        COMMON::result(300, '云仓数据不允许删除');
	        return false;
        }

    	if($B->updateBookoutDetaiData($id)){
			COMMON::result(200,'删除成功');
		}else{
			COMMON::result(300,'删除失败');
		}
        
    }

	public function dbtoexcelAction()
	{

		$request = $this->getRequest();

		$search = array (
			'bookingoutno' => $request->getPost('bookingoutno',''),
			'begin_time'=> $request->getPost('begin_time',''),
			'end_time' => $request->getPost('end_time',''),
			'bar_name' => $request->getPost('bar_name',''),
			'bar_status'=>$request->getPost('bar_status',''),
			'bar_docsource'=>$request->getPost('bar_docsource',''),
			'bar_receivenumber' => $request->getPost('bar_receivenumber',''),
			'bar_goodsname' => $request->getPost('bar_goodsname',''),
			'stockouttype' => 2,
			'page' => false,
			'orders'  => 'created_at desc',
		);

		$B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $B->searchBookout($search);


		/*------------------查询筛选条件返回参数-----------------*/

		ob_end_clean();//清除缓冲区,避免乱码

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("云仓管家")
			->setTitle("车出库预约单列表")
			->setSubject("车出库预约单列表")
			->setDescription("车出库预约单列表");

		$mainTitle = array(
			array('A1:A1', 'A1', '0094CE58', '单据编号'),
			array('B1:B1', 'B1', '005E9CD3', '单据来源'),
			array('C1:C1', 'C1', '005E9CD3', '提单类型'),
			array('D1:D1', 'D1', '0094CE58', '提货开始日'),
			array('E1:E1', 'E1', '0094CE58', '提货结束日'),
			array('F1:F1', 'F1', '0094CE58', '是否逾期'),
			array('G1:G1', 'G1', '0094CE58', '客户名称'),
			array('H1:H1', 'H1', '003376B3', '提货单号'),
			array('I1:I1', 'I1', '003376B3', '购货公司'),
			array('J1:J1', 'J1', '003376B3', '品名'),
			array('K1:K1', 'K1', '003376B3', '规格'),
			array('L1:L1', 'L1', '003376B3', '货物性质'),
			array('M1:M1', 'M1', '003376B3', '计量单位'),
			array('N1:N1', 'N1', '003376B3', '提货数量'),
			array('O1:O1', 'O1', '003376B3', '单据状态'),
		);

		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->setTitle('车出库预约单列表');

		foreach ($mainTitle as $row) {
			$objActSheet->mergeCells($row[0]);
			$objActSheet->setCellValue($row[1], $row[3]);


			$objStyle = $objActSheet->getStyle($row[1]);

			$objStyle->getAlignment()->setHorizontal("center");
			$objStyle->getAlignment()->setVertical("center");
			$objStyle->getAlignment()->setWrapText(true);
			$objStyle->getFont()->setBold(true);
		}
		$line = 1;
		$subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H','I','J','K','L','M','N','O');

		foreach ($list['list'] as $item) {

			$line++;
			for ($i = 0; $i < count($subtitle); $i++) {
				$site = $subtitle[$i] . $line;
				$value = '';
				switch ($i) {
					case 0:
						$value = $item['bookingoutno'];
						break;
					case 1:
						switch($item['docsource']){
							case "1":
								$value = "手工创建";
								break;
							case "2":
								$value = "国烨云仓";
								break;
						}
						break;
					case 2:
						switch($item['taketype']){
							case "1":
								$value = "车提";
								break;
							case "2":
								$value = "客提";
								break;
						}
						break;
					case 3:
						$value = $item['receivestart'];
						break;
					case 4:
						$value = $item['receiveend'];
						break;
					case 5:
						switch($item['receiveover']){
							case "0":
								$value = "否";
								break;
							case "1":
								$value = "是";
								break;
						}
						break;
					case 6:
						$value = $item['customer_name'];
						break;
					case 7:
						$value = $item['receivenumber'];
						break;
					case 8:
						$value = $item['takecompany'];

						break;
					case 9:
						$value = $item['goodsname'];
						break;
					case 10:
						$value = $item['qualityname'];
						break;
					case 11:
						switch($item['goodsnature']){
							case "1":
								$value = "保税";
								break;
							case "2":
								$value = "外贸";
								break;
							case "3":
								$value = "内贸转出口";
								break;
							case "4":
								$value = "内贸内销";
								break;
						}
						break;
					case 12:
						$value = $item['unitname'];
						break;
					case 13:
						$value = $item['bookingoutqty'];
						break;
					case 14:
						switch($item['bookingoutstatus']){
							case "2":
								$value = "暂存";
								break;
							case "3":
								$value = "待确认";
								break;
							case "4":
								$value = "待审核";
								break;
							case "5":
								$value = "已审核";
								break;
							case '6':
								$value = "已完成";
								break;
							case '7':
								$value = "退回";
								break;
							default:
								$value = "新建";
						}
						break;
				}
				$objActSheet->setCellValue($site, $value);
			}
		}

// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="车出库预约单列表.xlsx"');
		header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

	}
	public function shipdbtoexcelAction()
	{

		$request = $this->getRequest();

		$search = array (
			'bookingoutno'=>$request->getPost('bookingoutno',''),
			'begin_time' => $request->getPost('begin_time',''),
			'end_time' => $request->getPost('end_time',''),
			'bar_name' => $request->getPost('bar_name',''),
			'bar_status'=>$request->getPost('bar_status',''),
			'bar_docsource' => $request->getPost('bar_docsource',''),
			'bar_receivenumber' => $request->getPost('bar_receivenumber',''),
			'bar_goodsname' => $request->getPost('bar_goodsname',''),
			'stockouttype' => 1,
			'page' => false,
			'orders'  => 'created_at desc',
		);

		$B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $B->searchBookout($search);


		/*------------------查询筛选条件返回参数-----------------*/

		ob_end_clean();//清除缓冲区,避免乱码

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("云仓管家")
			->setTitle("船出库预约单列表")
			->setSubject("船出库预约单列表")
			->setDescription("船出库预约单列表");

		$mainTitle = array(
			array('A1:A1', 'A1', '0094CE58', '单据编号'),
			array('B1:B1', 'B1', '005E9CD3', '单据来源'),
			array('C1:C1', 'C1', '005E9CD3', '提货开始日'),
			array('D1:D1', 'D1', '0094CE58', '提货结束日'),
			array('E1:E1', 'E1', '0094CE58', '是否逾期'),
			array('F1:F1', 'F1', '0094CE58', '客户姓名'),
			array('G1:G1', 'G1', '0094CE58', '提货单位'),
			array('H1:H1', 'H1', '003376B3', '提货单号'),
			array('I1:I1', 'I1', '003376B3', '货品名称'),
			array('J1:J1', 'J1', '003376B3', '规格'),
			array('K1:K1', 'K1', '003376B3', '货品性质'),
			array('L1:L1', 'L1', '003376B3', '计量单位'),
			array('M1:M1', 'M1', '005E9CD3', '提货数量'),
			array('N1:N1', 'N1', '003376B3', '客服'),
			array('O1:O1', 'O1', '003376B3', '船名'),
			array('P1:P1', 'P1', '005E9CD3', '单据状态'),
		);

		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->setTitle('船出库预约单列表');

		foreach ($mainTitle as $row) {
			$objActSheet->mergeCells($row[0]);
			$objActSheet->setCellValue($row[1], $row[3]);


			$objStyle = $objActSheet->getStyle($row[1]);

			$objStyle->getAlignment()->setHorizontal("center");
			$objStyle->getAlignment()->setVertical("center");
			$objStyle->getAlignment()->setWrapText(true);
			$objStyle->getFont()->setBold(true);
		}
		$line = 1;
		$subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H','I','J','K','L','M','N','O','P');

		foreach ($list['list'] as $item) {

			$line++;
			for ($i = 0; $i < count($subtitle); $i++) {
				$site = $subtitle[$i] . $line;
				$value = '';
				switch ($i) {
					case 0:
						$value = $item['bookingoutno'];
						break;
					case 1:
						switch($item['docsource']){
							case "1":
								$value = "手工创建";
								break;
							case "2":
								$value = "国烨云仓";
								break;
						}
						break;
					case 2:
						$value = $item['receivestart'];
						break;
					case 3:
						$value = $item['receiveend'];
						break;
					case 4:
						switch($item['receiveover']){
							case "0":
								$value = "否";
								break;
							case "1":
								$value = "是";
								break;
						}
						break;
					case 5:
						$value = $item['customer_name'];
						break;
					case 6:
						$value = $item['receiveunitname'];
						break;
					case 7:
						$value = $item['receivenumber'];
						break;
					case 8:
						$value = $item['goodsname'];

						break;
					case 9:
						$value = $item['qualityname'];
						break;
					case 10:
						switch($item['goodsnature']){
							case "1":
								$value = "保税";
								break;
							case "2":
								$value = "外贸";
								break;
							case "3":
								$value = "内贸转出口";
								break;
							case "4":
								$value = "内贸内销";
								break;
						}
						break;
					case 11:
						$value = $item['unitname'];
						break;
					case 12:
						$value = $item['bookingoutqty'];
						break;
					case 13:
						$value = $item['cs_employeename'];
						break;
					case 14:
						$value = $item['shipname'];
						break;
					case 15:
						switch($item['bookingoutstatus']){
							case "2":
								$value = "暂存";
								break;
							case "3":
								$value = "待确认";
								break;
							case "4":
								$value = "待审核";
								break;
							case "5":
								$value = "已审核";
								break;
							case '6':
								$value = "已完成";
								break;
							case '7':
								$value = "退回";
								break;
							default:
								$value = "新建";
						}
						break;
				}
				$objActSheet->setCellValue($site, $value);
			}
		}

// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="船出库预约单列表.xlsx"');
		header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

	}

    public function ajaxUploadAction(){
        $request = $this->getRequest();
        $backid=$request->getPost('backid','');

        $result = array(
            'statusCode'=>'200',
            'message'=>'上传成功',
            'backid'=>$backid,
            'backval'=>''
        );

        $path = "upload/bookout/";
        $up = new FileUpload;
        //设置属性(上传的位置， 大小， 类型， 名是是否要随机生成)
        $up->set("path", $path);
        $up->set("maxsize", 2000000);
        $up->set("allowtype", array("gif", "png", "jpg", "jpeg"));
        $up->set("israndname", true);

        //使用对象中的upload方法， 就可以上传文件， 方法需要传一个上传表单的名子 pic, 如果成功返回true, 失败返回false
        if ($up->upload('file')) {
            $result['backval'] = $path . $up->getFileName();
        } else {
            $result['statusCode']='300';
            $result['message']='上传失败';
        }
        echo json_encode($result);
    }
    //生成唯一预约单号
    public function getbookoutnoAction()
    {
    	$request = $this->getRequest();
    	$prefix = $request->getParam('prefix','');
    	$bookoutno = COMMON::getCodeId($prefix);
    	echo json_encode($bookoutno);
    }

    /**
      * 控货
      * @param array('customer_sysno'=>cid,'goods_sysno'=>gid,'contract_sysno'=>coid,'num'=>num)
      * 返回array array(1=>'欠费超信用期限',2=>'欠费超信用额度',3=>'欠费超控货比重') code含义：200正常返回 非200代表异常
      */  
    public function controlgoodsAction()
    {
    	$request = $this->getRequest();
        $data=$request->getPost('obj',array());

    	$S = new StockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
    	
    	$params = array();
    	$result = array();
    	foreach ($data as $key => $value) {
    		$params['num'] = floatval(isset($value['bookingoutqty']) ? $value['bookingoutqty'] : $value['takegoodsnum']);
    		$list = $S->getStockinfoByid($value['stock_sysno']);
    		if(!$list){
    			COMMON::result(300,'库存信息错误');
 				return false;
    		}
	    	$params['customer_sysno'] = $list['customer_sysno'];
			$params['goods_sysno'] = $list['goods_sysno'];
			$params['contract_sysno'] = $list['contract_sysno'];

			$res = $S->controlgoods($params);
	 		if ($res['code'] == 300 ) {
	 			COMMON::result(300,'数据异常');
	 			return false;
	 		}else{
	 			if($res['message'][1]!=0 || $res['message'][2]!=0 || $res['message'][3]!=0){
	 				echo json_encode($res);die();
	 			}
	 		}
    	}   	
    	echo json_encode(array('code'=>200,'message'=>array('1'=>0,'2'=>0,'3'=>0)));
    	
    }
    
    /*
	* title : 管出库预约
	* author : zhaoshiyu
	*
    */
    public function pipelineListAction() {
		$params = array();
		$this->getView()->make('bookpipeline.list',$params);
	}

	public function pipelineListJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'bookingoutno'=>$request->getPost('bookingoutno',''),
			'begin_time' => $request->getPost('begin_time',''),
			'end_time' => $request->getPost('end_time',''),
			'bar_name' => $request->getPost('bar_name',''),
			'bar_status'=>$request->getPost('bar_status',''),
			'bar_docsource' => $request->getPost('bar_docsource',''),
			'stockouttype' => 3,
			'pageCurrent' => COMMON :: P(),
			'pageSize' => COMMON :: PR(),
			'orders'  => 'created_at desc',
		);

		$B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $B->searchBookout($search);
		echo json_encode($list);

	}

	public function pipelineEditAction(){
		$request = $this->getRequest();
		$id = $request->getParam('id','0');
		$type = $request->getParam('type','');
		if(!isset($id)) {
			$id = 0;
		}

		$B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$E = new EmployeeModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		if(!$id){
			$action = "/bookout/pipelineNewJson/";
			$params =  array();
			$params['detaillist'] = json_encode(array());
		}
		else{
			$action = "/bookout/pipelineEditJson/";
			$params = $B->getBookoutById($id);

			$status = $params['bookingoutstatus'];
  			if ($status !=2 && $status !=7 && $type!='audit' && $type!='sendback' && $type != 'view' && $type!='addatt') {    //audit审核管出库预约  sendback管出库预约退回 view管出库预约查看
                COMMON::result(300, '非暂存或退回状态不能编辑！');
                return;
            }

			$search = array(
				'bookingout_sysno' =>	$params['sysno'],
				'page' => false
			);

			$S = new StockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
			$G = new GoodsModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
			$ST = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

			$detailData =  $B->getBookoutDetailList($search);
			
			foreach ($detailData['list'] as $key => $value) {
				if($value['stocktype'] == 1){
					$stockinfo = $S->getElementById(array($value['stock_sysno']));
					$detailData['list'][$key]['instockqty'] = $stockinfo[0]['instockqty'];
					$detailData['list'][$key]['introduceqty'] = '--';
					if($stockinfo[0]['doctype'] == 3){
						$detailData['list'][$key]['instockqty'] = '--';
					}
					$detailData['list'][$key]['transInstockqty'] = $stockinfo[0]['instockqty'];
					$detailData['list'][$key]['ableqty'] = $stockinfo[0]['stockqty']-$stockinfo[0]['clockqty'];
				}elseif($value['stocktype'] == 2){
					$introduceDetailInfo = $I->getIntroduceDetailById(intval($value['stock_sysno']));
					$detailData['list'][$key]['instockqty'] = '--';
					$detailData['list'][$key]['introduceqty'] = $introduceDetailInfo['takegoodsnum'];
					$detailData['list'][$key]['ableqty'] = $introduceDetailInfo['untakegoodsnum']-$introduceDetailInfo['bookingqty'];

				}

				$stinfo = $ST->getStoragetankById($value['storagetank_sysno']);
				$detailData['list'][$key]['storagetankableqty'] = $stinfo['tank_stockqty'] - $stinfo['orderoutqty'] <0 ? 0 : $stinfo['tank_stockqty'] - $stinfo['orderoutqty'] ;
			}

			$params['detaillist'] = json_encode($detailData['list']) ;

			$A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));	

			$attach = $A->getAttachByMAS('bookpipeline','pipeline',$id);
			if( is_array($attach) && count($attach)){
				$files = array();
				foreach ($attach as $file){
					$files[] = $file['sysno'];
				}
				$params['uploaded']  =  join(',',$files);
			}
			$params['attach']  = $attach;
			$params['samples']  =  $A->getAttachByMAS('customer','customerlading',$params['customer_sysno']);
		}

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
			'page' => false,
		);

		$list = $C->searchCustomer($search);
		$params['customerlist'] =  $list['list'];

		$list = $E->searchEmployee($search);
		$params['employeelist'] =  $list['list'];

		$params['id'] =  $id;
		$params['action'] =  $action;
		$params['status'] = COMMON::getExamStatus($params['bookingoutstatus']);
		$params['type'] = $type;
		$this->getView()->make('bookpipeline.edit',$params);
	}
	
	public function pipelineDetaileditAction(){
		$request = $this->getRequest();
		$cid = $request->getParam('cid','0');
		// $Obj = $request->getPost('Obj',array());

		// if (!empty($Obj)) {
		// 	$params['Obj'] = json_encode($Obj);
		// }
		
		$selectedDatas = $request->getPost('selectedDatasArray',array());	
		$params['selectedDatas'] = $selectedDatas;
		// $params['selectedData'] = json_encode($selectedDatas);
		$params['handlestatus'] = $request->getParam('handlestatus','0');

        $B = new StockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $S = new SupplierModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $G = new GoodsModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

        $stocklist = [];
        $stockdata = $B -> getAllStock($cid);

        foreach ($stockdata as $key => $value) {
        	if($value['stockqty'] <= 0){
        		unset($stockdata[$key]);
        		continue;
        	}
        	unset($stockdata[$key]['storagetank_sysno']);
        	if($value['ableqty'] != 0){
	        	$value['transInstockqty'] = $value['instockqty'];  //货权转移对应的入库单的入库量
	        	if($value['doctype'] == 3){
	        		$value['instockqty'] = '--';
	        	}
        		$stocklist[] = $value;
        	}
        	
        }

        $params['stocklist'] = json_encode($stocklist);
        $search = array(
            'bar_status'=>1,
            'page' => false,
            );

        $list = $S->searchShipList($search);
        $params['shiplist'] = $list['list'];

        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $quality->getList($search, 99, 1);
        $params['goodsqualitylist'] = $list['list'];

		if(count($stocklist) > 0){
			$this->getView()->make('bookpipeline.detailedit',$params);
		}else{
			COMMON::result(300,'该客户没有库存');
		}

	}

	public function pipelineNewJsonAction()
	{
		$request = $this->getRequest();
		$detaildata = $request->getPost('detaildata',"");
		$detaildata = json_decode($detaildata,true);

		if(count($detaildata)==0) {
			COMMON::result(300,'预约单明细不能为空');
			return;
		}
		$B = new BookoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
		
		$input = array(
			'bookingoutno' => $request->getPost('bookingoutno', ''),
			'bookingoutstatus' => $request->getPost('bookingoutstatus',''),
			'bookingoutdate' => $request->getPost('bookingoutdate', ''),
			'customer_sysno' => $request->getPost('customer_sysno', ''),
			'customer_name' => $request->getPost('customer_name', ''),
			'docsource' => $request->getPost('docsource', '1'),
			'stockouttype' => 3,
			'cs_employee_sysno' => $request->getPost('cs_employee_sysno', 0),
			'cs_employeename' => $request->getPost('cs_employeename', '')=='请选择' ? '' : $request->getPost('cs_employeename', ''),
			'receivestart' => $request->getPost('receivestart', ''),
			'receiveend' => $request->getPost('receiveend', ''),
			'receivenumber' => $request->getPost('receivenumber', ''),
			'receiveunitname' => $request->getPost('receiveunitname', ''),
			'bookingoutstatus' => $request->getPost('bookingoutstatus', '1'),
			'status' => 1,
			'isdel' => 0,
			'created_at' => '=NOW()',
			'updated_at' => '=NOW()',
			'ispipelineorder' => $request->getPost('ispipelineorder', 1),
            'isberthorder' => $request->getPost('isberthorder', 2),
            'isqualitycheck' => $request->getPost('isqualitycheck', 1),
		);
		if ($input['cs_employee_sysno'] == 0) {
			unset($input['cs_employee_sysno']);
			unset($input['cs_employeename']);
		}

		if ($id = $B->addPipelineBookout($input,$detaildata)) {
			$attach =  $request->getPost('attachment',array());
			if(count($attach) > 0){
				$A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
				$res = $A->addAttachModelSysno($id,$attach);
				if(!$res){
					COMMON::result(300,'添加附件失败');
					return;
				}
			}
			$row = $B->getBookoutById($id);
			COMMON::result(200, '添加成功', $row);
		} else {
			COMMON::result(300, '添加失败');
		}
	}

	public function pipelineEditJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('id', 0);
		$detaildata = $request->getPost('detaildata',"");
		$detaildata = json_decode($detaildata,true);
		if(count($detaildata)==0) {
			COMMON::result(300,'预约单明细不能为空');
			return;
		}

		$B = new BookoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
		$bookoutinfo = $B->getBookoutById($id);
		
		$input = array(
			'bookingoutdate' => $request->getPost('bookingoutdate', ''),
			'customer_sysno' => $request->getPost('customer_sysno', ''),
			'customer_name' => $request->getPost('customer_name', ''),
			'docsource' => $request->getPost('docsource', '1'),
			'stockouttype' => 3,
			'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
			'cs_employeename' => $request->getPost('cs_employeename', '') == '请选择'? '' : $request->getPost('cs_employeename', ''),
			'isbusinesscheck' => 1,
			'shipproxyname' => $request->getPost('shipproxyname', ''),
			'businesscheckunitname' => $request->getPost('businesscheckunitname', ''),
			'receiveunitname' => $request->getPost('receiveunitname', ''),
			'receivestart' => $request->getPost('receivestart', ''),
			'receiveend' => $request->getPost('receiveend', ''),
			'receiveover' => $request->getPost('receiveover', '') == '是' ? 1 : 0,
			'receivenumber' => $request->getPost('receivenumber', ''),
			'receiveunitname' => $request->getPost('receiveunitname', ''),
			'bookingoutstatus' => $request->getPost('bookingoutstatus', '1'),
			'status' => 1,
			'isdel' => 0,
			'updated_at' => '=NOW()',
			'ispipelineorder' => $request->getPost('ispipelineorder', 2),
            'isberthorder' => $request->getPost('isberthorder', 2),
            'isqualitycheck' => $request->getPost('isqualitycheck', 1),
		);

		if ($B->updatePipelineBookout($id,$input,$detaildata)) {

			$row = $B->getBookoutById($id);
			COMMON::result(200, '更新成功', $row);
		} else {
			COMMON::result(300, '更新失败');
		}
	}

	public function pipelineAuditAction()
	{
		$this->getView()->make('bookpipeline.auditlist',array());
	}
	public function pipelineAuditJsonAction()
	{
		$request = $this->getRequest();

		$search = array (
			'bookingoutno'=>$request->getPost('bookingoutno',''),
			'begin_time' => $request->getPost('begin_time',''),
			'end_time' => $request->getPost('end_time',''),
			'bar_name' => $request->getPost('bar_name',''),
			'bar_status'=> 4,
			'bar_docsource' => $request->getPost('bar_docsource',''),
			'stockouttype' => 3,
			'pageCurrent' => COMMON :: P(),
			'pageSize' => COMMON :: PR(),
			'orders'  => 'created_at desc',
		);

		$B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $B->searchBookout($search);
		echo json_encode($list);
	}
  
	public function pipelineDBtoexcelAction()
	{

		$request = $this->getRequest();

		$search = array (
			'bookingoutno' => $request->getPost('bookingoutno',''),
			'begin_time'=> $request->getPost('begin_time',''),
			'end_time' => $request->getPost('end_time',''),
			'bar_name' => $request->getPost('bar_name',''),
			'bar_status'=>$request->getPost('bar_status',''),
			'bar_docsource'=>$request->getPost('bar_docsource',''),
			'stockouttype' => 3,
			'page' => false,
			'orders'  => 'created_at desc',
		);

		$B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $B->searchBookout($search);


		/*------------------查询筛选条件返回参数-----------------*/

		ob_end_clean();//清除缓冲区,避免乱码

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("云仓管家")
			->setTitle("管出库预约单列表")
			->setSubject("管出库预约单列表")
			->setDescription("管出库预约单列表");

		$mainTitle = array(
			array('A1:A1', 'A1', '0094CE58', '预约单号'),
			array('B1:B1', 'B1', '005E9CD3', '单据来源'),
			array('C1:C1', 'C1', '0094CE58', '提货开始日'),
			array('D1:D1', 'D1', '0094CE58', '提货结束日'),
			array('E1:E1', 'E1', '0094CE58', '客户'),
			array('F1:F1', 'F1', '003376B3', '提货单号'),
			array('G1:G1', 'G1', '003376B3', '品名'),
			array('H1:H1', 'H1', '003376B3', '规格'),
			array('I1:I1', 'I1', '003376B3', '货物性质'),
			array('J1:J1', 'J1', '003376B3', '预提数量'),
			array('K1:K1', 'K1', '003376B3', '客服'),
			array('L1:L1', 'L1', '003376B3', '单据状态'),
		);

		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->setTitle('管出库预约单列表');

		foreach ($mainTitle as $row) {
			$objActSheet->mergeCells($row[0]);
			$objActSheet->setCellValue($row[1], $row[3]);


			$objStyle = $objActSheet->getStyle($row[1]);

			$objStyle->getAlignment()->setHorizontal("center");
			$objStyle->getAlignment()->setVertical("center");
			$objStyle->getAlignment()->setWrapText(true);
			$objStyle->getFont()->setBold(true);
		}
		$line = 1;
		$subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H','I','J','K','L');

		foreach ($list['list'] as $item) {

			$line++;
			for ($i = 0; $i < count($subtitle); $i++) {
				$site = $subtitle[$i] . $line;
				$value = '';
				switch ($i) {
					case 0:
						$value = $item['bookingoutno'];
						break;
					case 1:
						switch($item['docsource']){
							case "1":
								$value = "手工创建";
								break;
							case "2":
								$value = "国烨云仓";
								break;
						}
						break;
					case 2:
						$value = $item['receivestart'];
						break;
					case 3:
						$value = $item['receiveend'];
						break;
					case 4:
						$value = $item['customer_name'];
						break;
					case 5:
						$value = $item['receivenumber'];
						break;
					case 6:
						$value = $item['goodsname'];
						break;
					case 7:
						$value = $item['qualityname'];
						break;
					case 8:
						switch($item['goodsnature']){
							case "1":
								$value = "保税";
								break;
							case "2":
								$value = "外贸";
								break;
							case "3":
								$value = "内贸转出口";
								break;
							case "4":
								$value = "内贸内销";
								break;
						}
						break;
					case 9:
						$value = $item['bookingoutqty'];
						break;

					case 10:
						$value = $item['cs_employeename'];
						break;
					case 11:
						switch($item['bookingoutstatus']){
							case "2":
								$value = "暂存";
								break;
							case "3":
								$value = "待确认";
								break;
							case "4":
								$value = "待审核";
								break;
							case "5":
								$value = "已审核";
								break;
							case '6':
								$value = "已完成";
								break;
							case '7':
								$value = "退回";
								break;
							default:
								$value = "新建";
						}
						break;
				}
				$objActSheet->setCellValue($site, $value);
			}
		}

// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="管出库预约单列表.xlsx"');
		header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}

	public function getDeclareNumAction()
	{
		$request = $this->getRequest();
		$stockin_sysno = $request->getPost('stockin_sysno',0);
		
		$B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$data = $B->getDeclareNum($stockin_sysno);
		echo json_encode($data);
	}
}

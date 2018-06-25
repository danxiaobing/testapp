<?php
/**
 * 提单
 */
class IntroduceController extends Yaf_Controller_Abstract {
	/**
	 * IndexController::init()
	 *
	 * @return void
	 */
	public function init() {
		# parent::init();
    }

	/**
	 * 列表页
	 */
	public function listAction() {
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
			'page' => false,
		);

		$list = $C->searchCustomer($search);
		$params['customerlist'] =  $list['list'];
		$this->getView()->make('introduce.list',$params);
	}


	public function listJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'takegoodsno' => $request->getPost('takegoodsno',''),
			'introductiondate'=> $request->getPost('introductiondate',''),
			'customer_sysno' => $request->getPost('customer_sysno',''),
			'buy_customer_sysno' => $request->getPost('buy_customer_sysno',''),
			'introductiontype' => $request->getPost('introductiontype',''),
			'introductionstatus' => $request->getPost('introductionstatus',''),
			'shipname' => $request->getPost('shipname',''),
			'pageCurrent' => COMMON:: P(),
			'pageSize' => COMMON:: PR(),
			'orders'  => 'created_at desc',
		);

		$I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $I->searchIntroduce($search);
		echo json_encode($list);
	}

	public function auditlistAction() {
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
			'page' => false,
		);

		$list = $C->searchCustomer($search);
		$params['customerlist'] =  $list['list'];
		$this->getView()->make('introduce.auditlist',$params);
	}

	public function auditListJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'takegoodsno' => $request->getPost('takegoodsno',''),
			'introductiondate'=> $request->getPost('introductiondate',''),
			'customer_sysno' => $request->getPost('customer_sysno',''),
			'introductiontype' => $request->getPost('introductiontype',''),
			'introductionstatus' => 3,
			'pageCurrent' => COMMON:: P(),
			'pageSize' => COMMON:: PR(),
			'orders'  => 'created_at desc',
		);

		$I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $I->searchIntroduce($search);
		echo json_encode($list);
	}

	public function reviewListAction() {
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
		$this->getView()->make('introduce.reviewlist',$params);
	}


	public function reviewListJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'stockinno' => $request->getPost('stockinno',''),
			'goods_sysno'=> $request->getPost('goods_sysno',''),
			'customer_sysno' => $request->getPost('customer_sysno',''),
			'pageCurrent' => COMMON:: P(),
			'pageSize' => COMMON:: PR(),
			'orders'  => 'created_at desc',
		);

		$I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $I->searchStockIn($search);
		echo json_encode($list);
	}
	public function reviewDetailListAction()
	{
		$request = $this->getRequest();
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

        $params['id'] = $request->getParam('id','0');
		$this->getView()->make('introduce.reviewdetail',$params);
	}

	public function reviewDetailJsonAction() {
		$request = $this->getRequest();
		$id = $request->getParam('id',0);
		$search = array(
            'stockin_sysno' => $id,
			'page' => false,
		);
		$I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $I->searchStockIn($search);
		echo json_encode($list);
	}
	//出库查看
	public function outListAction()
	{
		$request = $this->getRequest();
        $params['id'] = $request->getParam('id','0');
		$this->getView()->make('introduce.outlist',$params);
	}

	public function outListJsonAction() {
		$request = $this->getRequest();
		$id = $request->getParam('id',0);

		$I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$introductionInfo = $I->getIntroduceInfo($id);

		echo json_encode($introductionInfo);
	}

	public function outListDetailJsonAction() {
		$request = $this->getRequest();
		$id = $request->getParam('id',0);

		$I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$outListDetail = $I->outListDetail($id);

		echo json_encode($outListDetail);
	}

	public function introduceDetailJsonAction() {
		$request = $this->getRequest();

		$search = array(
            'stockin_sysno' => $request->getParam('id',0),
            'takegoodsno' => $request->getPost('takegoodsno',''),
            'introductiontype' => $request->getPost('introductiontype',0),
            'sale_customer_sysno' => $request->getPost('sale_customer_sysno',0),
            'buy_customer_sysno' => $request->getPost('buy_customer_sysno',0),
			'page' => false,
		);

		$I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $I->getIntroduceDetailData($id,$search);
		echo json_encode($list);
	}

	public function editAction(){
		$request = $this->getRequest();
		$id = $request->getParam('id','0');
		$type = $request->getParam('type','');
		if(!isset($id)) {
			$id = 0;
		}

		$I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$S = new StockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		if(!$id){
			$action = "/introduce/newJson/";
			$params =  array();
			$params['detaillist'] = json_encode(array());
		}
		else{
			$action = "/introduce/editJson/";
			$params = $I->getIntroduceById($id);

			if($params['introductionstatus'] != 2 && $params['introductionstatus'] != 6 && $type != 'audit' && $type != 'view' && $type != 'delay' && $type != 'trandown'){
				COMMON::result(300,'暂存和退回状态的才可以编辑');
				return;
			}
			//向下货转
			if($type == 'trandown' && $params['introductionstatus'] != 4){
				COMMON::result(300,'提货中的提单才可以向下货转');
				return;
			}
			//提单延期
			if($type == 'delay'){
				if($params['introductionstatus'] != 4 || $params['introductiontype'] != 1){
					COMMON::result(300,'提货中的可撤销提单才可以延期');
					return;
				}
			}

			$detailData =  $I->getIntroduceDetailList($id);
			if($type == 'trandown'){
				$action = "/introduce/newJson/";

				if(isset($params['father_introduction_sysno'])){
					$result = $I->getIntroduceTree($params['father_introduction_sysno']);
				}
				if(count($result) > 1){
					COMMON::result(300,'只能向下货转3家');
					return;
				}
				$params['sale_customer_sysno'] = $params['buy_customer_sysno'];
				$params['sale_customername'] = $params['buy_customername'];

				unset($params['sysno']);
				unset($params['buy_customer_sysno']);
				unset($params['bug_customername']);
				unset($params['takegoodsno']);
				unset($params['receivestart']);
				unset($params['receiveend']);
				unset($params['freecostdate']);
				unset($params['lossrate']);
				unset($params['lastamount']);
				unset($params['introductionstatus']);
				$params['father_introduction_sysno'] = $id;
				foreach ($detailData as $key => $value) {
					$detailData[$key]['introductiondetail_sysno'] = $value['sysno'];
					//提单可转量
					$detailData[$key]['tobeqty'] = $value['untakegoodsnum'] - $value['bookingqty'];
					unset($detailData[$key]['sysno']);
					unset($detailData[$key]['takegoodsnum']);
					unset($detailData[$key]['untakegoodsnum']);
					unset($detailData[$key]['takegoodsqty']);
					unset($detailData[$key]['outqty']);
					unset($detailData[$key]['controlloss']);
					unset($detailData[$key]['introductiondetailstatus']);
					unset($detailData[$key]['introduction_sysno']);
				}
			}
			if(isset($params['father_introduction_sysno']) && $params['introductiontype'] == 1){
				$fatherInfo = $I->getIntroduceById($params['father_introduction_sysno']);
				if($fatherInfo['introductiontype'] == 1){
					$params['disabled'] = 'disabled';
				}

				if($fatherInfo['receiveend'] == '--'){
					$fatherInfo['receiveend'] = '2117-12-31';
				}
				$params['freceiveend'] = $fatherInfo['receiveend'];
				$params['freceivestart'] = $fatherInfo['receivestart'];
			}
			if($params['introductiontype'] != 1 && $type == 'trandown'){
				unset($params['introductiontype']);
				unset($params['costtype']);
				unset($params['cost_customer_sysno']);
				unset($params['cost_customername']);
			}

			foreach ($detailData as $key => $value) {
				if($value['introductiondetail_sysno'] != 0){
					$detailInfo = $I->getIntroduceDetailById($value['introductiondetail_sysno']);
					//提单可转量
					$detailData[$key]['tobeqty'] = $detailInfo['untakegoodsnum'] - $detailInfo['bookingqty'];
				}else{
					if($value['stocktype'] == 2) {
						$sdetailInfo = $I->getIntroduceDetailById($value['stock_sysno']); //首先获取来源明细
						$sourceInfo = $I->getIntroduceDetailById($sdetailInfo['sysno']);
						$info = $I->getIntroduceById($sourceInfo['introduction_sysno']);
						$detailData[$key]['introductiontype'] = $info['introductiontype'];
					}else{
						$detailData[$key]['introductiontype'] = 0;
					}

				}
				if($value['stocktype'] == 1){
					$stockinData = $I->getStockinById($value['stockin_sysno']);
					$detailData[$key]['release_num'] = isset($stockinData['release_num']) ? $stockinData['release_num'] : 0;
					$detailData[$key]['introduceqty'] = '--';
				}elseif ($value['stocktype'] == 2) {
					$detailData[$key]['introduceqty'] = $value['instockqty'];
					$detailData[$key]['instockqty'] = '--';

				}
			}

			$params['detaillist'] = json_encode($detailData) ;
			if($type != 'trandown'){
				$A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

				$attach = $A->getAttachByMAS('introduce','takegoods',$id);
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
		}

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
			'page' => false,
		);

		$list = $C->searchCustomer($search);
		$params['customerlist'] =  $list['list'];
		if($type != 'trandown'){
			$params['id'] =  $id;
		}else{
			$params['id'] = 0;
		}

		$params['action'] =  $action;
		$params['status'] = COMMON::getIntroduceStatus($params['introductionstatus']);
		$params['type'] =  $type;
		$params['lossrate'] = $params['lossrate']*30;
		$this->getView()->make('introduce.edit',$params);
	}

	public function newJsonAction()
	{
		$request = $this->getRequest();
		$detaildata = $request->getPost('detaildata',"");
		$detaildata = json_decode($detaildata,true);

		if(count($detaildata)==0) {
			COMMON::result(300,'提单明细不能为空');
			return;
		}
		$I = new IntroduceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

		$input = array(
			'introductionno' => $request->getPost('introductionno', ''),
			'introductiontype' => $request->getPost('introductiontype', ''),
			'introductionstatus' => $request->getPost('introductionstatus',''),
			'father_introduction_sysno' => $request->getPost('father_introduction_sysno',''),
			'introductiondate' => $request->getPost('introductiondate', ''),
			'customer_sysno' => $request->getPost('customer_sysno', ''),
			'customername' => $request->getPost('customername', ''),
			'buy_customer_sysno' => $request->getPost('buy_customer_sysno', ''),
			'buy_customername' => $request->getPost('buy_customername', ''),
			'sale_customer_sysno' => $request->getPost('sale_customer_sysno', ''),
			'sale_customername' => $request->getPost('sale_customername', ''),
			'receivestart' => $request->getPost('receivestart', ''),
            'receiveend' => $request->getPost('receiveend', ''),
            'freecostdate' => $request->getPost('freecostdate', ''),
			'takegoodsno' => $request->getPost('takegoodsno', ''),
			'costtype' => $request->getPost('costtype', ''),
			'lossrate' => sprintf('%.3f',$request->getPost('lossrate', '')/30),
			'lastamount' => $request->getPost('lastamount', ''),
			'status' => 1,
			'isdel' => 0,
			'created_at' => '=NOW()',
			'updated_at' => '=NOW()',
		);

		if($input['freecostdate'] == '--'){
			$input['freecostdate'] = '';
		}
		if($input['introductiontype'] == 1){
			$input['cost_customer_sysno'] = $input['customer_sysno'];
			$input['cost_customername'] = $input['customername'];
		}elseif ($input['introductiontype'] == 2) {
			if($input['costtype'] == '1'){
				$input['cost_customer_sysno'] = $input['sale_customer_sysno'];
				$input['cost_customername'] = $input['sale_customername'];
			}else{
				$input['cost_customer_sysno'] = $input['buy_customer_sysno'];
				$input['cost_customername'] = $input['buy_customername'];
			}
		}

		if ($id = $I->addIntroduce($input,$detaildata)) {
			$attach =  $request->getPost('attachment',array());
			if(count($attach) > 0){
				$A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
				$res = $A->addAttachModelSysno($id,$attach);
				if(!$res){
					COMMON::result(300,'添加附件失败');
					return;
				}
			}
			COMMON::result(200, '添加成功');
			return;
		} else {
			COMMON::result(300, '添加失败');
			return;
		}
	}

	public function editJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('id', 0);
		$detaildata = $request->getPost('detaildata',"");
		$detaildata = json_decode($detaildata,true);
		if(count($detaildata)==0) {
			COMMON::result(300,'提单明细不能为空');
			return;
		}

		$I = new IntroduceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

		$input = array(
			'introductionno' => $request->getPost('introductionno', ''),
			'introductiontype' => $request->getPost('introductiontype', ''),
			'introductionstatus' => $request->getPost('introductionstatus',''),
			'introductiondate' => $request->getPost('introductiondate', ''),
			'customer_sysno' => $request->getPost('customer_sysno', ''),
			'customername' => $request->getPost('customername', ''),
			'buy_customer_sysno' => $request->getPost('buy_customer_sysno', ''),
			'buy_customername' => $request->getPost('buy_customername', ''),
			'sale_customer_sysno' => $request->getPost('sale_customer_sysno', ''),
			'sale_customername' => $request->getPost('sale_customername', ''),
			'receivestart' => $request->getPost('receivestart', ''),
            'receiveend' => $request->getPost('receiveend', ''),
            'costtype' => $request->getPost('costtype', ''),
            'freecostdate' => $request->getPost('freecostdate', ''),
			'takegoodsno' => $request->getPost('takegoodsno', ''),
			'costtype' => $request->getPost('costtype', ''),
			'lossrate' => sprintf('%.3f',$request->getPost('lossrate', '')/30),
			'lastamount' => $request->getPost('lastamount', ''),
			'status' => 1,
			'isdel' => 0,
			'updated_at' => '=NOW()',
		);
		if($input['freecostdate'] == '--'){
			$input['freecostdate'] = '';
		}
		if($input['introductiontype'] == 1){
			$input['cost_customer_sysno'] = $input['customer_sysno'];
			$input['cost_customername'] = $input['customername'];
		}elseif ($input['introductiontype'] == 2) {
			if($input['costtype'] == '1'){
				$input['cost_customer_sysno'] = $input['sale_customer_sysno'];
				$input['cost_customername'] = $input['sale_customername'];
			}else{
				$input['cost_customer_sysno'] = $input['buy_customer_sysno'];
				$input['cost_customername'] = $input['buy_customername'];
			}
		}
		if ($I->updateIntroduce($id,$input,$detaildata)) {
			$row = $I->getIntroduceById($id);
			COMMON::result(200, '更新成功',$row);
			return;
		} else {
			COMMON::result(300, '更新失败');
			return;
		}
	}

	public function delJsonAction(){
    	$request = $this->getRequest();
		$id = $request->getPost('sysno',0);

		$I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$params = $I->getIntroduceById($id);

		if($params['introductionstatus'] != 2 && $params['introductionstatus'] != 6) {
	        COMMON::result(300, '非暂存和退回状态不能删除！');
	        return false;
        }


		if($I->delIntroduceData($id)){
			COMMON::result(200,'删除成功');
			return;
		}else{
			COMMON::result(300,'删除失败');
			return;
		}

    }

    public function detaileditAction(){
		$request = $this->getRequest();
		$cid = $request->getParam('cid','0');
		$selectedDatas = $request->getPost('selectedDatasArray',array());
		$params['selectedDatas'] = $selectedDatas;
		$params['selectedDatas']['unitname'] = '吨';
		$params['selectedData'] = json_encode($selectedDatas);
		$params['handlestatus'] = $request->getParam('handlestatus','0');

        $B = new StockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $S = new SupplierModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $G = new GoodsModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $Q = new QualityModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

        $stocklist = [];
        $stockdata = $B -> getAllStocks($cid);

        foreach ($stockdata as $key => $value) {
        	if($value['stockqty'] <= 0){
        		unset($stockdata[$key]);
        		continue;
        	}
        	unset($stockdata[$key]['storagetank_sysno']);
        	if($value['ableqty'] >= 0){
        		$stocklist[] = $value;
        	}

        }

        $params['stocklist'] = json_encode($stocklist);

		if(count($stocklist) > 0 || $params['selectedDatas']['introductiondetail_sysno'] != 0){
			$this->getView()->make('introduce.detailedit',$params);
		}else{
			COMMON::result(300,'该客户没有库存');
			return;
		}

	}

	public function  examJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('id',0);
		$examstep = $request->getPost('examstep',0);
		$exammarks = $request->getPost('exammarks','');

		if($id == 0 || $examstep == 0 ){
			COMMON::result(300,'审核信息错误');
			return false;
		}
		if ($examstep == 6 && $exammarks=='') {
			COMMON::result(300,'请填写意见');
			return false;
		}
		$I = new IntroduceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
		$introduceInfo = $I->getIntroduceById($id);
		if (!$introduceInfo) {
			COMMON::result(300,'提单信息错误');
			return;
		}

		$L = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$user = Yaf_Registry::get(SSN_VAR);

		if($examstep == 6){
			$res = $I->updateIntroduceData($id);

			#库存管理业务操作日志
			if($res){

				$input= array(
					'doc_sysno'  =>  $id,
					'doctype'  =>  29,
					'opertype'  => 5 ,
					'operemployee_sysno' => $user['employee_sysno'],
					'operemployeename' => $user['employeename'],
					'opertime'    => '=NOW()',
					'operdesc'  =>  $exammarks
				);

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
		}elseif ($examstep == 4){
			$msg='';
			$res = $I->examIntroduce($id,$msg);

			if($res){
				$input= array(
					'doc_sysno'  =>  $id,
					'doctype'  =>  29,
					'opertype'  => 3 ,
					'operemployee_sysno' => $user['employee_sysno'],
					'operemployeename' => $user['employeename'],
					'opertime'    => '=NOW()',
					'operdesc'  =>  $exammarks
				);
				$L->addDocLog($input);

				COMMON::result(200, '操作成功');
				return;
			}else{
				COMMON::result(300, $msg);
				return;
			}

		}

		COMMON::result(300, '操作失败');
		return;
	}

	public function introduceDelayAction()
	{
		$request = $this->getRequest();
		$id = $request->getParam('id',0);
		$receivestart = $request->getPost('receivestart','');
		$receiveend = $request->getPost('receiveend','');
		$I = new IntroduceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
		$introduceInfo = $I->getIntroduceById($id);
		if($receivestart != strtotime($introduceInfo['receivestart'])){
			echo json_encode(array('code' => 300,'msg' => '开始时间不能修改'));die();
		}
		if($receiveend < strtotime($introduceInfo['receiveend'])){
			echo json_encode(array('code' => 300,'msg' => '结束时间不能提前'));die();
		}

		if($introduceInfo['father_introduction_sysno'] != 0 ){
			$fintroduceInfo = $I->getIntroduceById(intval($introduceInfo['father_introduction_sysno']));
			if($receiveend > strtotime($fintroduceInfo['receiveend']) && $fintroduceInfo['receiveend'] != '0000-00-00'){
				echo json_encode(array('code' => 300,'msg' => '结束日期不能超过上级提单结束日期'));die();
			}
		}

		$data = array(
			'receiveend' => date('Y-m-d',$receiveend),
			);
		$res = $I->updateIntroduceInfo($id,$data);
		echo json_encode($res);

	}
	public function stopIntroduceAction()
	{
		$request = $this->getRequest();
		$id = $request->getParam('id',0);
		$I = new IntroduceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
		$introduceInfo = $I->getIntroduceById($id);
		if(!$introduceInfo){
			$data = array(
				'code' => 300,
				'msg' => '提单信息错误'
				);
			echo json_encode($data);die();
		}
		if($introduceInfo['introductionstatus'] == 2 || $introduceInfo['introductionstatus'] == 6){
			$data = array(
				'code' => 300,
				'msg' => '暂存或退回状态不允许撤销'
				);
			echo json_encode($data);die();
		}
		if($introduceInfo['introductionstatus'] == 9){
			$data = array(
				'code' => 300,
				'msg' => '提单已作废不能撤销'
				);
			echo json_encode($data);die();
		}
		if($introduceInfo['introductionstatus'] == 7){
			$data = array(
				'code' => 300,
				'msg' => '此提单已撤销'
				);
			echo json_encode($data);die();
		}
		if($introduceInfo['introductionstatus'] == 8){
			$data = array(
				'code' => 300,
				'msg' => '云仓订单不能撤销'
				);
			echo json_encode($data);die();
		}

		$res = $I->stopIntroduce($id);
		if($res['code'] == 200){
			$user = Yaf_Registry::get(SSN_VAR);
			$L = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
			$input= array(
				'doc_sysno'  =>  $id,
				'doctype'  =>  29,
				'opertype'  => 6 ,
				'operemployee_sysno' => $user['employee_sysno'],
				'operemployeename' => $user['employeename'],
				'opertime'    => '=NOW()',
				'operdesc'  =>  '撤销提单'
			);
			$L->addDocLog($input);
		}

		echo json_encode($res);
	}
	//提单作废
	public function cancelIntroduceAction()
	{
		$request = $this->getRequest();
		$id = $request->getParam('id',0);
		$I = new IntroduceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
		$introduceInfo = $I->getIntroduceById($id);
		if(!$introduceInfo){
			$data = array(
				'code' => 300,
				'msg' => '提单信息错误'
				);
			echo json_encode($data);die();
		}
		if($introduceInfo['introductionstatus'] != 4){
			$data = array(
				'code' => 300,
				'msg' => '提货中状态才可以作废'
				);
			echo json_encode($data);die();
		}

		$res = $I->cancelIntroduce($id);
		echo json_encode($res);
	}

}

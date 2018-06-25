<?php
/**
 * @Author: Dujiangjiang
 * @Date:   2016-12-17 11:55:36
 * @Last Modified by:   Dujiangjiang
 * @Last Modified time: 2016-12-17 17:06:33
 */
class ReportstockController extends Yaf_Controller_Abstract {
	private $request;
	/**
	 * IndexController::init()
	 *
	 * @return void
	 */
	public function init() {
		$this->request = $this->getRequest();
    }


	public function indexAction(){
		$customerInstance = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$search = array(
				'bar_status' => '1',
				'bar_isdel' => '0',
				'page' => false,
		);
		$list = $customerInstance ->searchCustomer($search);
		$params['customerlist'] =  $list['list'] ? $list['list'] : [];
		$this->getView()->make('reportstock.list',$params);
	}


	public function stockListJsonAction(){
		$params = array(
			'start_time' => $this->request->getPost('start_time', ''),
			'end_time' => $this->request->getPost('end_time', ''),
			'customer_sysno' => $this->request->getPost('customer_sysno', 0),
			'clearstockstatus' => $this->request->getPost('clearstockstatus', 0),
			'stockinno' => $this->request->getPost('stockinno', ''),
			'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
		);

		$R = new ReportstockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$result=$R->getlist($params);
//		echo "<pre>";print_r($result['list']);die;
		echo json_encode($result);
	}

	public function detailAction(){
		$params['firstfrom_sysno'] = $this->request->getParam('firstfrom_sysno', 0);
		$params['sysno'] = $this->request->getParam('sysno', 0);
		$this->getView()->make('reportstock.detail',$params);
	}

	/**
	 * 清库列表
	 */
	public function clearStockAction(){
		$sysno = $this->request->getParam('sysno', 0);
		$firstfrom_sysno = $this->request->getParam('firstfrom_sysno', 0);
//		echo $sysno;die;
		$R = new ReportstockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $R -> getClearStockDetail( $firstfrom_sysno );
		$param['list'] = json_encode($list);
		$this->getView()->make('reportstock.clearstock', $param );
	}

	/**
	 * 出库
	 */
	public function outStockAction(){
		$sysno = $this->request->getParam('sysno', 0);
		$firstfrom_sysno = $this->request->getParam('firstfrom_sysno', 0);
		$R = new ReportstockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $R -> getOutStockDetail( $firstfrom_sysno );
//		echo "<pre>";print_r($list);die;
		$params['list'] = json_encode(json_encode($list));
		$this->getView()->make('reportstock.outstock', $params );
	}

	/**
	 * 货权转移
	 */
	public function changeStockAction(){
		$sysno = $this->request->getParam('sysno', 0);
		$firstfrom_sysno = $this->request->getParam('firstfrom_sysno', 0);
		$R = new ReportstockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list  = $R -> getChangeStockDetail( $sysno );
		$param['list'] = json_encode($list);
		$this->getView()->make('reportstock.changestock', $param);
	}
}
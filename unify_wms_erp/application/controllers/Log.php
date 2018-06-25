<?php

class LogController extends Yaf_Controller_Abstract {
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
	public function privilegelogAction() {
		$params = array();



		$this->getView()->make('log.privilegelog',$params);
	}

	public function privilegelogJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'bar_controller' => $request->getPost('bar_controller',''),
			'bar_action' => $request->getPost('bar_action',''),
			'bar_realname' => $request->getPost('bar_realname',''),
			'pageCurrent' => COMMON :: P(),
			'pageSize' => COMMON :: PR(),
			'orders'  => $request->getPost('orders',''),

		);

		$L = new LogModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$list = $L->searchPrivilegeLog($search);

		echo json_encode($list);
	}

	public function doclogAction() {
		$params = array();



		$this->getView()->make('log.doclog',$params);
	}

	public function doclogJsonAction() {
		$user = Yaf_Registry::get(SSN_VAR);
		$request = $this->getRequest();

		$id=$request->getPost('id',$request->getParam('id','0'));
		if ($id == 0) {
			$list['list'][] = array(
				'opertype' => '新建',
				'operdesc' => '',
				'operemployeename' => $user['employeename'],
				'opertime' => date('Y-m-d H:i:s',time()),
			);
			echo json_encode($list);
			return false;
		}
		$search = array (
			'bar_id' => $id,
			'page' => false,
			'orders'  => $request->getPost('orders',''),
			'bar_doctype' => $request->getPost('doctype',''),
		);

		$L = new LogModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$list = $L->searchDocLog($search);

		echo json_encode($list);
	}

}

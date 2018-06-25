<?php

class SystemController extends Yaf_Controller_Abstract {
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
	public function privilegelistAction() {
		$params = array();

		$S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$params['rootlist'] =  $S->getPrivilegeListByPid(-1,1);

		$this->getView()->make('system.privilegelist',$params);
	}

    public function privilegetreeAction() {
        $params = array();

        $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $params['rootlist'] =  $S->getPrivilegeListByPid(-1,1);

        $this->getView()->make('system.privilegetree',$params);
    }


    public function privilegetreeJsonAction() {
        $request = $this->getRequest();

        $search = array (
            'bar_name' => $request->getPost('bar_name',''),
            'bar_parentid' => $request->getPost('bar_parentid','-100'),
            'bar_status' => $request->getPost('bar_status','-100'),
            'page' => false,
            'orders'  => $request->getPost('orders',''),

        );



        $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        $data = $S->searchPrivilege($search);

        $list = array();
		
		foreach($data['list'] as $row){
			if($row['parent_sysno'] ==0 )
				$row['parent_sysno'] = null;
			$list[] = $row;
		}

        echo json_encode($list);
    }

	public function privilegelistJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'bar_name' => $request->getPost('bar_name',''),
			'bar_parentid' => $request->getPost('bar_parentid','-100'),
			'bar_status' => $request->getPost('bar_status','-100'),
			'pageCurrent' => COMMON :: P(),
			'pageSize' => COMMON :: PR(),
			'orders'  => $request->getPost('orders',''),

		);



		$S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$list = $S->searchPrivilege($search);

		echo json_encode($list);
	}


	public function privilegeEditAction(){
		$request = $this->getRequest();
		$id = $request->getParam('id',0);

		$S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		if(!$id){
			$action = "/system/privilegeNewJson/";
			$params =  array ();
		}

		else{
			$action = "/system/privilegeEditJson/";

			$params = $S->getPrivilegeById($id);


		}

		$params['rootlist'] =  $S->getPrivilegeListByPid(-1,1);
		$params['id'] =  $id;
		$params['action'] =  $action;


		$this->getView()->make('system.privilegeedit',$params);
    }

	public function privilegeNewJsonAction(){
		$request = $this->getRequest();

		$S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$input = array(
			'privilegeno'       =>  $request->getPost('privilegeno',''),
			'privilegename'     =>  $request->getPost('privilegename',''),
			'privilegeresource'=>  $request->getPost('privilegeresource',''),
			'parent_sysno'      =>  $request->getPost('parent_sysno',''),
			'parentsysnotype'   =>  $request->getPost('parentsysnotype',''),
			'parentsysnoicon'   =>  $request->getPost('parentsysnoicon',''),
			'privilegecontroller'   =>  $request->getPost('privilegecontroller',''),
			'privilegeaction'   =>  $request->getPost('privilegeaction',''),
			'privilegedesc'     =>  $request->getPost('privilegedesc',''),
			'menuorder'          =>  $request->getPost('menuorder','1'),
			'needcheck'          =>  $request->getPost('needcheck','1'),
			'status'             =>  $request->getPost('status','1'),
			'isdel'              =>  $request->getPost('isdel','0'),
			'created_at'		=>'=NOW()',
			'updated_at'		=>'=NOW()'
		);

		if($id = $S->addPrivilege($input)){
			$row = $S->getPrivilegeById($id);
			COMMON::result(200,'添加成功',$row);
		}else{
			COMMON::result(300,'添加失败');
		}
	}

	public function privilegeEditJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('id',0);

		$S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$input = array(
			'privilegeno'       =>  $request->getPost('privilegeno',''),
			'privilegename'     =>  $request->getPost('privilegename',''),
			'privilegeresource'=>  $request->getPost('privilegeresource',''),
			'parent_sysno'      =>  $request->getPost('parent_sysno',''),
			'parentsysnotype'   =>  $request->getPost('parentsysnotype',''),
			'privilegecontroller'   =>  $request->getPost('privilegecontroller',''),
			'privilegeaction'   =>  $request->getPost('privilegeaction',''),
			'parentsysnoicon'   =>  $request->getPost('parentsysnoicon',''),
			'privilegedesc'     =>  $request->getPost('privilegedesc',''),
			'menuorder'          =>  $request->getPost('menuorder','1'),
			'needcheck'          =>  $request->getPost('needcheck','1'),
			'status'             =>  $request->getPost('status','1'),
			'isdel'              =>  $request->getPost('isdel','0'),
			'updated_at'		=>'=NOW()'
		);
 
		if($S->updatePrivilege($id,$input)){
			$row = $S->getPrivilegeById($id);
			COMMON::result(200,'更新成功',$row);
		}else{
			COMMON::result(300,'更新失败');
		}
	}

	public function privilegeDelJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('sysno',0);

		$S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$input = array(
			'isdel' => 1
		);

		if($S->updatePrivilege($id,$input)){
			COMMON::result(200,'删除成功');
		}else{
			COMMON::result(300,'删除失败');
		}
	}

    public function rolelistAction() {
		$params = array();

		$S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$this->getView()->make('system.rolelist',$params);
	}

	public function roleEditAction(){
		$request = $this->getRequest();
		$id = $request->getParam('id',0);

		$module = array();

		$S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		if(!$id){
			$action = "/system/roleNewJson/";
			$params =  array();
			$roleprivileges = array();
		}

		else{
			$action = "/system/roleEditJson/";
			$params = $S->getRoleById($id);
			$roleprivileges = $S->getRolePrivilege($id);
		}

		$roleprivileges = $S->getRoleViewPrivilege($roleprivileges);

  //       $params['privileges'] = $roleprivileges['privileges'];
  //       $params['privileges2'] = $roleprivileges['privileges2'];
  //       $params['privileges3'] = $roleprivileges['privileges3'];
		$params['module'] = $roleprivileges['module'];
		$params['newmodule'] = json_encode($roleprivileges['newmodule']);
		$params['n'] = 0;

		$params['id'] =  $id;
		$params['action'] =  $action;

		$this->getView()->make('system.roleedit',$params);
    }

    public function roleNewJsonAction(){
		$request = $this->getRequest();
		$privileges = $request->getPost('treedata',"");

		$S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$input = array(
			'roleno'       =>  $request->getPost('roleno',''),
			'rolename'     =>  $request->getPost('rolename',''),
			'roledesc'     =>  $request->getPost('roledesc',''),
			'status'             =>  $request->getPost('status','1'),
			'isdel'              =>  $request->getPost('isdel','0'),
			'created_at'	=>'=NOW()',
			'updated_at'	=>'=NOW()'
		);

		if($id = $S->addRole($input,$privileges)){
			$row = $S->getRoleById($id);
			COMMON::result(200,'添加成功',$row);
		}else{
			COMMON::result(300,'添加失败');
		}
	}

	public function roleEditJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('id',0);
		$privileges = $request->getPost('treedata',"");

		$S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$input = array(
			'roleno'       =>  $request->getPost('roleno',''),
			'rolename'     =>  $request->getPost('rolename',''),
			'roledesc'     =>  $request->getPost('roledesc',''),
			'status'             =>  $request->getPost('status','1'),
			'isdel'              =>  $request->getPost('isdel','0'),
			'updated_at'	=>'=NOW()'
		);
 
		if($S->updateRole($id,$input,$privileges)){
			$row = $S->getRoleById($id);
			COMMON::result(200,'更新成功',$row);
		}else{
			COMMON::result(300,'更新失败');
		}
	}

	public function roleDelJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('sysno',0);

		$S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$input = array(
			'isdel' => 1
		);

		if($S->updateRole($id,$input,$privileges)){
			COMMON::result(200,'删除成功');
		}else{
			COMMON::result(300,'删除失败');
		}
	}

	public function rolelistJsonAction() {
		$request = $this->getRequest();

		$search = array (
            'bar_no' => $request->getPost('bar_no',''),
			'bar_name' => $request->getPost('bar_name',''),
			'bar_parentid' => $request->getPost('bar_parentid','-100'),
			'bar_status' => $request->getPost('bar_status','-100'),
			'pageCurrent' => COMMON :: P(),
			'pageSize' => COMMON :: PR(),
			'orders'  => $request->getPost('orders',''),

		);

		$S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		$list = $S->searchRole($search);

		echo json_encode($list);
	}

}
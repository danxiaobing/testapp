<?php
/**
 * @Author: Dujiangjiang
 * @Date:   2016-11-15 17:38:10
 * @Last Modified by:   Dujiangjiang
 * @Last Modified time: 2016-11-15 18:06:03
 */
class DepartmentController extends Yaf_Controller_Abstract {
	/**
	 * IndexController::init()
	 *
	 * @return void
	 */
	public function init() {

    }

    /**
	 * 部门管理
	 * @author Dujiangjiang
	 *  
	 */
	public function listAction(){
		$params = array(
			'bar_no'=>'',
			'bar_name'=>''
		);
		$this->getView()->make('department.list',$params);
	}

	/**
	 * 部门管理列表JSON
	 * @author Dujiangjiang
	 */
	public function listJsonAction() {
		$request = $this->getRequest();

		$search = array (
			'bar_no'=>$request->getPost('bar_no',''),
			'bar_name' => $request->getPost('bar_name',''),
			'bar_parentid' => $request->getPost('bar_parentid','-100'),
			'bar_status' => $request->getPost('bar_status','-100'),
			'bar_isdel' => $request->getPost('bar_isdel','0'),
			'page' => false,
			'orders'  => $request->getPost('orders',''),

		);


		$D = new DepartmentModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$data = $D->searchDepartment($search);

        $list = array();
		
		foreach($data['list'] as $row){
			if($row['parent_sysno'] ==0 )
				$row['parent_sysno'] = null;
			$list[] = $row;
		}

        echo json_encode($list);


	}

	/**
	 * 部门新增与编辑页面
	 * @author Dujiangjiang
	 */
	public function addAndEditAction(){
		$request = $this->getRequest();
		$id = $request->getParam('id',0);
		$pid=0;
		$module = array();
		$D = new DepartmentModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

		if(!$id){
			$action = "/department/newJson/";
			$params =  array();
			$departmentprivileges = array();
		}else{
			$action = "/department/editJson/";
			$params = $D->getDepartmentById($id);
			$departmentprivileges = $D->getDepartmentPrivilege($id);
		}

		$params['rootlist'] = $D->getDepartMentByPid($pid);

		$departmentprivileges = $D->getDepartmentViewPrivilege($departmentprivileges);

        $params['privileges'] = $departmentprivileges['privileges'];
		$params['module'] = $departmentprivileges['module'];

		$params['id'] =  $id;
		$params['action'] =  $action;

		$this->getView()->make('department.edit',$params);
    }

    /**
	 * 部门新增Post处理
	 * @author Dujiangjiang
	 */
	public function newJsonAction(){
		$request = $this->getRequest();
		$privileges = $request->getPost('treedata',"");

		$D = new DepartmentModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$input = array(
			'parent_sysno'	=>  $request->getPost('parent_sysno','0'),
			'departmentno'  =>  trim($request->getPost('departmentno','')),//COMMON::getCodeId('D'),,
			'departmentname'=>  $request->getPost('departmentname',''),
			'departmentdesc'=>  $request->getPost('departmentdesc',''),
			'status'       	=>  $request->getPost('status','1'),
			'isdel'        	=>  $request->getPost('isdel','0'),
			'created_at'	=> 	'=NOW()',
			'updated_at'	=> 	'=NOW()'
		);
        //判断编号是否唯一
	    $uniqueno =	$D->isUniqueNoadd($input['departmentno']);
	//	print_r($uniqueno);die;
		if($uniqueno['code']==300){
			COMMON::result(300,'更新失败:'.$uniqueno['status'],$uniqueno['status']);
			return false;
		}

		if($id = $D->addDepartment($input,$privileges)){
			$row = $D->getDepartmentById($id);
			COMMON::result(200,'添加成功',$row);
		}else{
			COMMON::result(300,'添加失败');
		}
    }

    /**
	 * 部门编辑Post处理
	 * @author Dujiangjiang
	 */
	public function editJsonAction(){
		$request = $this->getRequest();
		$id = $request->getPost('id',0);
		$privileges = $request->getPost('treedata',"");

		$D = new DepartmentModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$input = array(
			'parent_sysno'=>  $request->getPost('parent_sysno','0'),
			'departmentno'  =>  trim($request->getPost('departmentno','')),
			'departmentname'=>  $request->getPost('departmentname',''),
			'departmentdesc'=>  $request->getPost('departmentdesc',''),
			'status'       	=>  $request->getPost('status','1'),
			'isdel'        	=>  $request->getPost('isdel','0'),
			'updated_at'	=> 	'=NOW()'
		);
		$uniqueno =	$D->isUniqueNoedit($input['departmentno'],$id);
		if($uniqueno['code']==300){
			COMMON::result(300,'更新失败:'.$uniqueno['status'],$uniqueno['status']);
			return false;
		}

		if($D->updateDepartment($id,$input,$privileges)){
			$row = $D->getDepartmentById($id);
			COMMON::result(200,'更新成功',$row);
		}else{
			COMMON::result(300,'更新失败');
		}
    }

    /**
	 * 部门删除Post处理
	 * @author Dujiangjiang
	 */
    public function delJsonAction(){
    	$request = $this->getRequest();
		$id = $request->getPost('sysno',0);

		$D = new DepartmentModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$input = array(
			'isdel' => 1,
			'departmentno'=>'del'.strtotime(date('Y-m-d H:i:s')),
		);

		if($D->updateDepartment($id,$input)){
			COMMON::result(200,'删除成功');
		}else{
			COMMON::result(300,'删除失败');
		}
    }
}
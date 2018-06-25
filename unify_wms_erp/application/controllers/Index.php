<?php
use Gregwar\Captcha\CaptchaBuilder;

class IndexController extends Yaf_Controller_Abstract {
	/**
	 * IndexController::init()
	 *
	 * @return void
	 */
	public function init() {
		# parent::init();
        $user  = Yaf_Registry::get(SSN_VAR);
        // if($user==""){
        //     $this->getView()->make('exception');
        // }

    }

	/**
	 * 显示整个后台页面框架及菜单
	 *
	 * @return string
	 */
	public function IndexAction() {
		$params = array();
		$S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$params['user']  = Yaf_Registry::get(SSN_VAR);
		$navtab =  $S->getPrivilegeListByPidUid($params['user']['sysno'],0);

		$params['navtab'] = $navtab;
		//print_r($navtab );
		$E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
		$M = new MessageModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
		$user = Yaf_Registry::get(SSN_VAR);
		$position = $E->getJob($params['user']['sysno']);

		$search = array(
			'type' => 1,
			'cusomer_sysno' => $user['sysno']
			);
		$messageData = $M->getMessageList($search);
		if($messageData){
			$params['count'] = $messageData['count'];
		}

		$params['position'] = $position;
		$params['servicetime'] = date("Y/m/d H:i:s");


		$this->getView()->make('index.index',$params);
	}

	/**
	 *储罐信息图表
	 *@author zhaoshiyu 
	 */
	public function getStorageListAction(){
		//储罐信息
		$S = new StoragetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$storageData = $S->getStorageData();

		//return $storageData;
		//片区信息
		$A = new AreaModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$areaData = $A->getRecordCount();

		if(!empty($areaData))
		foreach ($areaData as $key => $value) {
			if(!empty($storageData)){
				foreach ($storageData as $k => $v) {
					if($v['storagetanknature']==1){
						$v['storagetanknature'] = '内贸罐';
					}elseif($v['storagetanknature']==2){
						$v['storagetanknature'] = '外贸罐';
					}elseif($v['storagetanknature']==3){
						$v['storagetanknature'] = '保税罐';
					}

					if ($v['area_sysno'] == $value['sysno']) {
						$v['storagetankname'] = mb_strimwidth($v['storagetankname'], 0, 12, '...');
						$v['goodsname'] = mb_strimwidth($v['goodsname'], 0, 12, '...');
						$areaData[$key]['storageData'][] = $v;
					}
				}
			}

			if(!isset($areaData[$key]['storageData'])){
				$areaData[$key]['storageData'] = [];
			}
		}

		echo json_encode($areaData);
		exit;
//		return $areaData;
	}

	//压强测试数据
	public function getPressureAction(){
		//测试格式
		$PressureData = array(
			'pressure' => '-1.000',//罐顶压力
			'infusion' => '+1.000',//罐內液位
			'temperature' => '+13.1',//罐內温度
		);
		echo json_encode($PressureData);
		exit;
	}

	public function demo_listAction(){
		$params = array();
		$this->getView()->make('index.datagrid',$params);
	}

	public function LoginAction() {
	//	unset($_SESSION);
		$params = array();
		$this->getView()->make('index.login',$params);
	}

	public function LogintimeoutAction() {
		//	unset($_SESSION);
		$params = array();
		$this->getView()->make('index.logintimeout',$params);
	}

	public function UserLoginAction()
	{
		$request = $this->getRequest();
		$params['username'] = $request->getpost('username','');
		$params['userpwd'] = $request->getpost('passwordhash','');

		$captcha = $request->getpost('captcha','');


		$session = Yaf_Session::getInstance();
		$phrase = $session->get('phrase');
		if($phrase != $captcha){
			$messgin = array();
			$messgin['msg'] = "验证码错误";
			$this->getView()->make('index.login',$messgin);
			return;
		}

		$S = new UserModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$P = new PassworderrorModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		if($user = $S->userLogin($params))
		{

			$ip = COMMON::getclientIp();


			$userUpdate = array('lastlogintime'=>'=NOW()','lastloginip'=>$ip);
//			var_dump($S->setUserInfo($userUpdate,$user['sysno']));die();

			if($S->setUserInfo($userUpdate,$user['sysno']))
			{
				unset($user['userpwd']);
				setcookie ( "u_id", $user['sysno'], 0, "/", '.' . WEB_DOMAIN );
				Yaf_Session::getInstance ()->set ( SSN_VAR, $user );
			}
			$res = $S->checkUser($params['username']);
			$P->delErrorLog($res);
			header("Location: /" );
			//$this->getView()->make('index.index',$params);
		}else{
			$messgin = array();
			$res = $S->checkUser($params['username']);
			$lockstatus = $S->checkUserLockstatus($params['username']);
			if ($lockstatus['lockstatus'] == '1') {
				$messgin['msg'] = "账号被锁定，请联系管理员";
			}else{
				if ($res) {
					$messgin['msg'] = "密码错误";
					$data = array(
					'user_sysno' => $res,
					'timedate'   => time(),
					'status'             =>  1,
					'isdel'              =>  0,
					'created_at'		=>'=NOW()',
					'updated_at'		=>'=NOW()'
					 );
					$result = $P->insetErrorLog($data);
					$num = $P->countErrorLog($res);
					if ($result && $num['num'] >= 3) {
							$messgin['msg'] = "密码输错3次，账号被锁定，请联系管理员解锁";
							$lockstatus['lockstatus'] = 1;
							$S->changeUserStatus($res,$lockstatus);
					}
				}else{
					$messgin['msg'] = "用户名错误";
				}
			}
			
			$this->getView()->make('index.login',$messgin);
		}

	}

	public function ajaxLoginAction()
	{
		$request = $this->getRequest();
		$params['username'] = $request->getpost('username','');
		$params['userpwd'] = $request->getpost('passwordhash','');

		$S = new UserModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		if($user = $S->userLogin($params))
		{

			$ip = COMMON::getclientIp();


			$userUpdate = array('lastlogintime'=>'=NOW()','lastloginip'=>$ip);
//			var_dump($S->setUserInfo($userUpdate,$user['sysno']));die();

			if($S->setUserInfo($userUpdate,$user['sysno']))
			{
				unset($user['userpwd']);
				setcookie ( "u_id", $user['sysno'], 0, "/", '.' . WEB_DOMAIN );
				Yaf_Session::getInstance ()->set ( SSN_VAR, $user );
			}
			COMMON::result(200,'登陆成功',$row);
			//$this->getView()->make('index.index',$params);
		}else{

			COMMON::result(300,'用户名密码错误');
		}

	}

	public function changepasswordAction() {

        $user = Yaf_Registry::get(SSN_VAR);
		$id = $user['sysno'];
		if(!$id)
		{
			COMMON::result(300,'请重新登录');
			return;
		}

        $params = array();

		$U = new UserModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$action = "/user/passwordEditJson/";

        $params = $U->getUserById($id);
        $params['userRoles'] = $U->getUserPrivilege($id);

        $params['id'] =  $id;
        $params['action'] =  $action;

		$this->getView()->make('index.changepassword',$params);
	}

	public function messageAction() {

        $user = Yaf_Registry::get(SSN_VAR);
		$id = $user['sysno'];
		if(!$id)
		{
			COMMON::result(300,'请重新登录');
			return;
		}

        $params = array();

		$U = new UserModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$M = new MessageModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$user = Yaf_Registry::get(SSN_VAR);
		$action = "/user/passwordEditJson/";

        $params = $U->getUserById($id);
        $params['userRoles'] = $U->getUserPrivilege($id);
        $search = array(
			'type' => 1,
			'cusomer_sysno' => $user['sysno']
			);
        $messageList = $M->getMessageList($search);
        if($messageList){
        	$params['messageList'] = $messageList['list'];
        }

        $params['id'] =  $id;
        $params['action'] =  $action;

		$this->getView()->make('index.message',$params);
	}
	//消息操作
	public function updateMessageAction()
	{
		$request = $this->getRequest();
		$id = $request->getPost('id',0);
		$id = substr($id, 1);
		$M = new MessageModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		// $messageInfo = $M->getMessageById($id);
		// if(!$messageInfo){
		// 	COMMON::result(300,'获取消息失败');
		// 	return false;
		// }
		$viewstatus = $request->getPost('viewstatus',1);
        if($viewstatus == 2){
        	$params['viewstatus'] = 2;
        	// $params['readnum'] = $messageInfo['readnum'] + 1;
        }

        $isdel = $request->getPost('isdel',0);
        if($isdel == 1){
        	$params['isdel'] = 1;
        }
		

		$res = $M->updateMessage($id,$params);
		if(!$res){
			COMMON::result(300,"已读失败");
			echo json_encode(array('code'=>300));
			return false;
		}
		echo json_encode(array('code'=>200));
	}

	//获取消息信息
	public function getMessageInfoAction()
	{
		$request = $this->getRequest();
		$id = $request->getPost('id',0);

		if($id){
			$M = new MessageModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
			$messageInfo = $M->getMessageById($id);
			if($messageInfo){
				$info = array(
					'code' => 200,
					'control' => $messageInfo['control'],
					'action'  => $messageInfo['action'],
					'sysno'   => $messageInfo['sysno'],
					);
				echo json_encode($info);
			}else{
				COMMON::result(300,"消息信息有误");
				echo json_encode(array('code'=>300));
				return false;
			}
			
		}else{
			COMMON::result(300,"操作失败");
			echo json_encode(array('code'=>300));
			return false;
		}
	}
	//获取消息条数
	public function getMessageCountAction()
	{
		$M = new MessageModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$user = Yaf_Registry::get(SSN_VAR);
		$search = array(
			'type' => 1,
			'cusomer_sysno' => $user['sysno']
			);
		$res = $M->getMessageList($search);
		if(!$res){
			COMMON::result(300,"获取消息失败");
			echo json_encode(array('code'=>300));
			return false;
		}
		echo json_encode(array('code' => 200,'count' => $res['count']));
	}

	public function demo1Action(){
		$params = array();
		$this->getView()->make('index.demo1',$params);
	}

	public function demo2Action(){
		$params = array();
		$this->getView()->make('index.demo2',$params);
	}

	public function  navtabAction(){
		$res = array();
		$request = $this->getRequest();
		$id = $request->getParam('id',0);

		if(!$id){
			 echo json_encode($res);
			 return;
		}
		$user  = Yaf_Registry::get(SSN_VAR);

		$S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$arr =  $S->getPrivilegeListByPidUid($user['sysno'],$id,1);

		if(count($arr) >0)
			foreach($arr as $item){
				$menu = array();
				$menu['name'] = $item['privilegename'];

				$children = $S->getPrivilegeListByPidUid($user['sysno'],$item['sysno'],1);

				if(count($children) > 0){
					foreach($children as $child){
						$row = array();
						$row['id'] =  'navab'.$child['sysno'];
						$row['name'] =   $child['privilegename'];
						$row['target'] =  "navtab";
						$row['url'] =   trim($child['privilegeresource']);

						$menu['children'][] = $row;
					}
				}else{
					$menu['url'] = trim($item['privilegeresource']);
					$menu['id'] = 'menu'.$item['sysno'];
				}

				$res[] = $menu;
			}


		echo json_encode($res);

	}

	public function logOutAction()
	{

		$arr = array ();
		Yaf_Session::getInstance ()->set ( SSN_VAR, $arr );
		// setcookie('user_str','',time()-3600);
		//setcookie ( "user_str", '', time () - 86400 * 365, "/", '.' . WEB_DOMAIN );
		header("Location: /login");
//		$this->getView()->make('index.login',$arr);
	}

	public function vcodeAction()
	{
		$builder = new CaptchaBuilder;
		$builder->build();
		$session = Yaf_Session::getInstance();
		$session->set('phrase',$builder->getPhrase());

		header('Content-type: image/jpeg');
		$builder->output();
		
	}


	public function ajaxDoneAction()
	{
		COMMON::result(200,'保存成功');
	}

	/**
	 * 储罐视图
	 */
	public function tankdetailAction()
	{
		$params = array();

		$this->getView()->make('index.tankdetail',$params);
	}

	public function storagetankcurrentAction()
	{
		//储罐信息
		$storageData = $this->getStorageListAction();
		$params['storageData'] = $storageData;

		$this->getView()->make('index.storagetankcurrent',$params);
	}

	public function storagetankinAction()
	{
		//储罐信息
		$storageData = $this->getStorageListAction();
		$params['storageData'] = $storageData;

		$this->getView()->make('index.storagetankin',$params);
	}

	public function storagetankinoutAction()
	{
		//储罐信息
		$storageData = $this->getStorageListAction();
		$params['storageData'] = $storageData;

		$this->getView()->make('index.storagetankinout',$params);
	}

}

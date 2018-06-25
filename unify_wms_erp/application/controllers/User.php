<?php
/**
 * 用户信息
 * User: Administrator
 * Date: 2016/11/15 0015
 * Time: 17:09
 */


class UserController extends Yaf_Controller_Abstract
{
    /**
     * IndexController::init()
     *
     * @return void
     */
    public function init()
    {
        # parent::init();
    }

    //*******************************************************************王文浩的分割线********************************************************//


    /**
     * 系统管理-用户列表
     * @author Alan
     * @time 2016-11-11 14:57:20
     */

    public function listAction()
    {
        $params = array();

        $request = $this->getRequest();
        $params['realname'] = $request->getPost('realname','');
        $params['pageSize'] = $request->getPost('pageSize','10');
        $params['pageCurrent'] = $request->getPost('pageSize','1');

        $U = new UserModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $params =  $U->getSystemUser($params);
        $this->getView()->make('user.userlist',$params);
    }

    /**
     * 查询用户列表
     * @author Alan
     * @time 2016-11-11 14:57:20
     */

    public function userlistJsonAction()
    {
        $params = array();

        $request = $this->getRequest();
        $search = array(
            'username' => $request->getPost('username',''),
            'realname' => $request->getPost('realname',''),
            'status' => $request->getPost('bar_status',''),
            'pageSize' => $request->getPost('pageSize','10'),
            'pageCurrent' => $request->getPost('pageCurrent','1'),
        );

        $U = new UserModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list =  $U->getSystemUser($search);

        echo json_encode($list);
    }


    /***
     * 添加/修改用户
     */

    public function usereditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id',0);

        $U = new UserModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        if(!$id){
            $action = "/user/userNewJson/";
            $params =  array ();
            $params['userRoles'] = array();
        } else {
            $action = "/user/userEditJson/";

            $params = $U->getUserById($id);
            $params['userRoles'] = $U->getUserPrivilege($id);
        }
        $params['id'] =  $id;
        $params['action'] =  $action;

        $roleprivileges = $U->roleList();

        $params['rolelist'] = $roleprivileges;
        $params['module'] = $roleprivileges['module'];

        //员工
        $Esearch =array(
            'page'=>false,
        );
        $E = new EmployeeModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $E->searchEmployee($Esearch);
        $params['employeelist'] =  $list['list'];
        //print_r($params['employeelist']);exit();

//        var_dump($params['rolelist']);
        $this->getView()->make('user.useredit',$params);
    }


    /***
     * 添加新用户的操作
     * @author Alan
     * @time 2016-11-14 11:18:53
     */

    public function userNewJsonAction()
    {
        $request = $this->getRequest();
        $privileges = $request->getPost('role','');
        $U = new UserModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $password = $request->getPost('userpwd','');
        $hash = password_hash($password, 1, ['cost' => 10]);


        $input = array(
            'username'      =>  $request->getPost('username',''),
            'userpwd'       =>  $hash,
            'employee_sysno'=>  $request->getPost('employee_sysno',''),
            'realname'      =>  $request->getPost('realname',''),
            'status'        =>  $request->getPost('status','1'),
            'isdel'         =>  $request->getPost('isdel','0'),
            'created_at'	=>'=NOW()',
            'updated_at'	=>'=NOW()'
        );

        switch ($id = $U->addUser($input,$privileges)) {
            case 'existence':
                COMMON::result(300,'账号已存在');
                break;
            case false:
                COMMON::result(300,'添加失败');
                break;                          
            default:
                $row = $U->getUserById($id);
                COMMON::result(200,'添加成功',$row);
                break;
        }

        // if($id = $U->addUser($input,$privileges)){
        //     $row = $U->getUserById($id);
        //     COMMON::result(200,'添加成功',$row);
        // }else{
        //     COMMON::result(300,'添加失败');
        // }

    }

    /**
     * 编辑用户
     * @author Alan
     * @time 	2016-11-14 17:45:05
     */

    public function userEditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id',0);
        $privileges = $request->getPost('role',"");
        $U = new UserModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $P = new PassworderrorModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $password = $request->getPost('userpwd','');

        $status = $request->getPost('status','');
        if($password == '')
        {
            $input = array(
                'username'      =>  $request->getPost('username',''),
                'realname'      =>  $request->getPost('realname',''),
                'employee_sysno'=>  $request->getPost('employee_sysno',''),
                'status'        =>  $request->getPost('status','1'),
                'isdel'         =>  $request->getPost('isdel','0'),
                'updated_at'	=>'=NOW()'
            );
        }else{
            $hash = password_hash($password, 1, ['cost' => 10]);
            $input = array(
                'username'      =>  $request->getPost('username',''),
                'employee_sysno'=>  $request->getPost('employee_sysno',''),
                'userpwd'       =>  $hash,
                'realname'      =>  $request->getPost('realname',''),
                'status'        =>  $request->getPost('status','1'),
                'isdel'         =>  $request->getPost('isdel','0'),
                'updated_at'	=>'=NOW()'
            );
        }


        if($U->updateUser($id,$input,$privileges)){
            $row = $U->getUserById($id);
            //如果账号改为启用，那么删除密码错误记录表里面的相应的记录
            $P->delErrorLog($id);
            COMMON::result(200,'更新成功',$row);
        }else{
            COMMON::result(300,'更新失败');
        }
    }

    public function userDelJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno',0);

        $U = new UserModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        if($U->delUser($id))
        {
            $row = $U->getUserById($id);
            COMMON::result(200,'更新成功',$row);
        }else{
            COMMON::result(300,'更新失败');
        }

    }

    public function passwordEditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id',0);
        if(!$id)
        {
            COMMON::result(300,'参数错误');
            return;
        }

        $U = new UserModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        $oldpassword = $request->getPost('olduserpwd','');
        $newpassword = $request->getPost('newuserpwd','');

        $row = $U->getUserById($id);

        if(password_verify($oldpassword, $row['userpwd']))
        {
            
        }
        else
        {
            COMMON::result(300,'旧密码错误');
            return;
        }

        if(!$newpassword)
        {
            COMMON::result(300,'密码不能为空',$row);
            return;
        }
        $hash = password_hash($newpassword, 1, ['cost' => 10]);
        $input = array(
            'userpwd'       =>  $hash,
            'updated_at'    =>'=NOW()'
        );

        if($U->updateUserPassword($id,$input)){
            COMMON::result(200,'更新成功');
        }else{
            COMMON::result(300,'更新失败');
        }
    }

    public function deblockingAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $U = new UserModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $userinfo = $U->getUserById($id);
        
        if (isset($userinfo['lockstatus']) && $userinfo['lockstatus'] == 0) {
            COMMON::result(300,'此账号不是锁定状态，不需要解锁');
            return false;
        }
        $lockstatus['lockstatus'] = 0;

        if($U->changeUserStatus($id,$lockstatus)){
            $data['code'] = 200;
            echo json_encode($data);
        }else{
            $data['code'] = 300;
            echo json_encode($data);
        }
    }

}
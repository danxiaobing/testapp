<?php
/**
 * @Author: danxiaobing
 * @Date:   2017-2-14
 * @Last Modified by:   danxiaobing
 * @Last Modified time: 2016-2-14
 */
class ApiController extends Yaf_Controller_Abstract
{
    public $request;

    /**
     * IndexController::init()
     *
     * @return void
     */
    public function init()
    {
        # parent::init();
        $this->request = $this->getRequest();
    }

    /**
     * 测试网络通讯是否成功
     */
    public function testAction(){
        COMMON::ApiJson('200','请求成功');
    }

    public function jiamiAction(){
        $post = [
//            'model' => 'api',
//            'action' => 'getOneOutDetail',
//            'username' => 'super@chinayie.com',
//            'userpwd' => '111111'
            'model' => 'api',
            'action' => 'backEmptyCarWeigh',

            'sysno' => '9',
//            'type' => '2',
//            'cranename' => '222',
//            'fullcartime' => date('Y-m-d H:i:s'),
//            'fullcarqty' => '20000',
//            'fullloadometer' => '50T',
            'customer_list' => json_encode([
                [
                    'sysno' =>'2',
                    'customer_sysno' => 17,
                    'realnumber'=>'10000',
                ],[
                    'sysno' =>'3',
                    'customer_sysno' => 17,
                    'realnumber'=>'20000',
                ],
            ]),
//            'model' => 'api',
//            'action' => 'emptyCarWeigh',
//
//            'sysno' => '113',
//            'type' => '2',
//            'cranename' => '3333',
            'emptycartime' => date('Y-m-d H:i:s'),
            'emptycarqty' => '20000',
            'emptyloadometer' => '50T',

            'user_sysno' => '3',
            'employee_sysno' => '103',
            'employeename' => '张飞',

            'version' => '1.1.12'
        ];
        $secret = new blowfish();

        $str = $secret -> encrypt(http_build_query($post));
        echo $str;
    }

    /**
     * 处理返回数据为NULL的值
     * @param array $array
     * @return array
     */
    private static function responseResult(array $array){
        foreach($array as $key=>&$val) {
            if(is_array($val)) {
                $val = self::responseResult($val);
            } else {
                if($val === null){
                    $val = '';
                }
            }
        }
        return $array;
    }

    /**
     * 登录接口
     */
    public function loginAction()
    {
        //版本验证
        self::getVersion();
        $params['username'] = $this->request->getParam('username', '');
        $params['userpwd'] = $this->request->getParam('userpwd', '');

//        $captcha = $this->request->getpost('captcha','');
//        $session = Yaf_Session::getInstance();
//        $phrase = $session->get('phrase');
//        if($phrase != $captcha){
//            $messgin = array();
//            $messgin['msg'] = "验证码错误";
//            $this->getView()->make('index.login',$messgin);
//            return;
//        }

        $S = new UserModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $P = new PassworderrorModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if ($user = $S->userLogin($params)) {
            if(!$user['employee_sysno'] || !$user['employeename']){
                COMMON::ApiJson('300', '请先绑定员工信息');
            }
            #权限验证
            $Pr = new PrivilegeModel(Yaf_Registry::get('db'), Yaf_Registry::get('mc'));
            if (!$Pr->check($this -> request -> getControllerName(), $this -> request -> getActionName(), $user )){
                COMMON::ApiJson(300,'您没有权限访问，请联系管理员。');
            }
            $ip = COMMON::getclientIp();
            $userUpdate = array('lastlogintime' => '=NOW()', 'lastloginip' => $ip);
            if ($S->setUserInfo($userUpdate, $user['sysno'])) {
                $userRes = [
                    'sysno' => $user['sysno'],
                    'username' => $user['username'],
                    'employee_sysno' => $user['employee_sysno'],
                    'employeename' => $user['employeename'],
                    'lastlogintime' => $user['lastlogintime'],
                    'lastloginip' => $user['lastloginip']
                ];
                $userPower['list']  =  $Pr -> getUserForApi($user);
                $userRes = array_merge($userRes, $userPower);
                COMMON::ApiJson('200', '', self::responseResult($userRes));
            }
            $res = $S->checkUser($params['username']);
            $P->delErrorLog($res);
        } else {
            $res = $S->checkUser($params['username']);
            if ($res) {
                $data = array(
                    'user_sysno' => $res,
                    'timedate' => time(),
                    'status' => 1,
                    'isdel' => 0,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()'
                );
                $result = $P->insetErrorLog($data);
                $num = $P->countErrorLog($res);
                if ($result && $num['num'] >= 3) {
                    $S->changeUserStatus($res,['lockstatus' =>1]);
                    COMMON::ApiJson('306', "密码输错3次，账号被锁定，请联系管理员解锁");
                }
                COMMON::ApiJson('305', "用户名或密码错误");
            } else {
                COMMON::ApiJson('305', "用户名或密码错误");
            }
        }
    }

    /**
     * 查询车入库磅码单列表
     */
    public function getCarInListAction(){
        //版本验证
        self::getVersion();
        $carid = $this->request->getParam('carid', '');
        $customername = $this->request->getParam('obj_customername','');
        $begin_time = $this->request->getParam('startDate','');
        $end_time = $this->request->getParam('endDate','');
        $poundsinstatus = $this->request->getParam('poundsinstatus','');
        $search = array(
            'carid'=>$carid,
            'customername' => $customername,
            'begin_time'=>$begin_time,
            'end_time'=>$end_time,
            'poundsinstatus'=> $poundsinstatus ? explode(',', $poundsinstatus) : [2,3],
            'stockinstatus' => 3,
        );
        $P = new PoundsapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $data = $P->getListInApi($search);
        if(!$data){ $data = [];}
        COMMON::ApiJson('200', '', self::responseResult($data));
    }

    /**
     * 查询车出库磅码单列表
     */
    public function getCarOutListAction(){
        //版本验证
        self::getVersion();
        $carid = $this->request->getParam('carid', '');
        $customername = $this->request->getParam('obj_customername','');
        $begin_time = $this->request->getParam('startDate','');
        $end_time = $this->request->getParam('endDate','');
        $poundsoutstatus = $this->request->getParam('poundsoutstatus','');
        $search = array(
            'carid'=>$carid,
            'customername' => $customername,
            'begin_time'=>$begin_time,
            'end_time'=>$end_time,
            'poundsoutstatus'=> $poundsoutstatus ? explode(',', $poundsoutstatus) : [2, 3],
            'stockoutstatus' => 3
        );
        $P = new PoundsapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $data = $P->getListOutApi($search);
        COMMON::ApiJson('200', '', self::responseResult($data));
    }

    /**
     * 查询已过磅车入库磅码单列表
     */
    public function getFinishInAction(){
        //版本验证
        self::getVersion();
        $carid = $this->request->getParam('carid', '');
        $begin_time = $this->request->getParam('startDate','');
        $end_time = $this->request->getParam('endDate','');
        $search = array(
            'carid'=>$carid,
            'begin_time'=>$begin_time,
            'end_time'=>$end_time,
            'poundsinstatus' => [4],
            'fullcartime' => date('Y-m-d', strtotime('-2 days')),
        );
        $P = new PoundsapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $data = $P->getListInApi($search);
        if(!$data){ $data = [];}
        COMMON::ApiJson('200', '', self::responseResult($data));
    }

    /**
     * 查询已完成车出库磅码单列表
     */
    public function getFinishOutAction(){
        //版本验证
        self::getVersion();
        $carid = $this->request->getParam('carid', '');
        $begin_time = $this->request->getParam('startDate','');
        $end_time = $this->request->getParam('endDate','');
        $search = array(
            'carid'=>$carid,
            'poundsoutstatus' => [4],
            'begin_time' => $begin_time,
            'end_time'=>$end_time,
            'emptycartime' => date('Y-m-d', strtotime('-2 days')),
        );
        $P = new PoundsapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $data = $P->getListOutApi($search);
        COMMON::ApiJson('200', '', self::responseResult($data));
    }


    /**
     * 查询单条入库单信息详情
     */
    public function getOneInDetailAction(){
        //版本验证
        self::getVersion();
        $sysno = $this->request->getParam('sysno', 0);
        if(!$sysno){
            COMMON::ApiJson('301' , '参数错误');
        }
        $P = new PoundsapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $data = $P->getOneInApi($sysno);
        COMMON::ApiJson('200', '', self::responseResult($data));
    }

    /**
     * 查询单条出库单信息
     */
    public function getOneOutDetailAction(){
        //版本验证
        self::getVersion();
        $sysno = $this->request->getParam('sysno', 0);
        if(!$sysno){
            COMMON::ApiJson('301' , '参数错误');
        }
        $P = new PoundsapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $data = $P->getOneOutApi($sysno);
        //添加货品的密度属性
        $data['density'] = $P -> getGoodsDensity($data['goods_sysno']);
        COMMON::ApiJson('200', '', self::responseResult($data));
    }

    /**
     * 重车过磅 1 入库  2 出库
     */
    public function fullCarWeighAction(){
        //版本验证
        self::getVersion();
        $sysno = $this -> request -> getParam('sysno', 0);
        if(!is_numeric($sysno)){
            COMMON::ApiJson('301', '入库磅码单SYSNO错误');
        }
        $type = $this -> request -> getParam('type', 0);
        if(!in_array($type, [1,2])){
            COMMON::ApiJson('301', '请检查是否传入磅类型');
        }

        $param['cranename'] = $this -> request -> getParam('cranename', '');
//        if($type == 1 && $param['cranename'] == ''){
//            COMMON::ApiJson('301', '请检查是否传入鹤位号');
//        }
        $param['fullcartime'] = date('Y-m-d H:i:s');
        $param['fullcarqty'] = floor($this -> request -> getParam('fullcarqty', 0));
        if(!$param['fullcarqty']){
            COMMON::ApiJson('301', '请检查是否传入重车过磅重量');
        }
        $param['fullloadometer'] = $this -> request -> getParam('fullloadometer', 0);
        if(!$param['fullloadometer']){
            COMMON::ApiJson('301', '请检查是否传入重车磅码类型');
        }
        //如果是出库重车过磅需要 传输每个客户出货量
        if($type == 2){
            $param['remark'] = $this -> request ->getParam('remark', '');
            $customerList = $this -> request ->getParam('customer_list', '');
            $param['customerList'] = json_decode($customerList, true);
            if(empty($param['customerList'])){
                COMMON::ApiJson('301', '请检查客户出库信息是否正确');
            }
        }

        $user['user_sysno'] = $this -> request -> getParam('user_sysno', 0);
        $user['employee_sysno'] = $this -> request -> getParam('employee_sysno', 0);
        $user['employeename'] = $this -> request -> getParam('employeename', 0);
        foreach($user as $value){
            if(!$value){
                COMMON::ApiJson('301', '用户参数错误'.$value);
            }
        }
        $P = new PoundsapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $res = $P->fullCarWeigh($type, $sysno, $param, $user);
        COMMON::ApiJson($res['code'],$res['message']);
    }

    /**
     *
     * 空车过磅   1 入库  2 出库
     */
    public function emptyCarWeighAction(){
        //版本验证
        self::getVersion();
        $sysno = $this -> request -> getParam('sysno', 0);
        if(!is_numeric($sysno)){
            COMMON::ApiJson('301', '入库磅码单SYSNO错误');
        }
        $type = $this -> request -> getParam('type', 0);
        if(!in_array($type, [1,2])){
            COMMON::ApiJson('301', '请检查是否传入磅类型');
        }

        $param['cranename'] = $this -> request -> getParam('cranename', '');
        if($type == 2 && $param['cranename'] == ''){
            COMMON::ApiJson('301', '请检查是否传入鹤位号');
        }
        $param['emptycartime'] =  date('Y-m-d H:i:s');
        $param['emptycarqty'] = floor($this -> request -> getParam('emptycarqty', 0));
        if(!$param['emptycarqty']){
            COMMON::ApiJson('301', '请检查是否传入空车过磅重量');
        }
        $param['emptyloadometer'] = $this -> request -> getParam('emptyloadometer', 0);
        if(!$param['emptyloadometer']){
            COMMON::ApiJson('301', '请检查是否传入空车磅码类型');
        }

        $user['user_sysno'] = $this -> request -> getParam('user_sysno', 0);
        $user['employee_sysno'] = $this -> request -> getParam('employee_sysno', 0);
        $user['employeename'] = $this -> request -> getParam('employeename', 0);
        foreach($user as $value){
            if(!$value){
                COMMON::ApiJson('301', '用户参数错误'.$value);
            }
        }
        $P = new PoundsapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $res = $P->emptyCarWeigh($type, $sysno, $param, $user);
        COMMON::ApiJson($res['code'],$res['message']);
    }

    /**
     * 获取退货磅码单列表信息
     */
    public function getBackPoundsListAction(){
        //版本验证
        self::getVersion();
        $begin_time = $this->request->getParam('startDate','');
        $end_time = $this->request->getParam('endDate','');
        $poundsinstatus = $this->request->getParam('poundsinstatus','');
        $search = array(
            'begin_time'=>$begin_time,
            'end_time'=>$end_time,
            'poundsinstatus'=> $poundsinstatus ? explode(',', $poundsinstatus) : [2,3],
            'stockbackstatus' => 4,
        );
        $P = new PoundsapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $data = $P->getListBackApi($search);
        if(!$data){ $data = [];}
        COMMON::ApiJson('200', '', self::responseResult($data));
    }

    /**
     * 退货重车过磅
     */
    public function backFullCarWeighAction(){
        //版本验证
        self::getVersion();
        $sysno = $this -> request -> getParam('sysno', 0);
        if(!is_numeric($sysno)){
            COMMON::ApiJson('301', '退货磅码单SYSNO错误');
        }

        $param['cranename'] = $this -> request -> getParam('cranename', '');
        $param['fullcartime'] =  date('Y-m-d H:i:s');
        $param['fullcarqty'] = floor($this -> request -> getParam('fullcarqty', 0));
        if(!$param['fullcarqty']){
            COMMON::ApiJson('301', '请检查是否传入重车过磅重量');
        }
        $param['fullloadometer'] = $this -> request -> getParam('fullloadometer', 0);
        if(!$param['fullloadometer']){
            COMMON::ApiJson('301', '请检查是否传入重车磅码类型');
        }
        $user['user_sysno'] = $this -> request -> getParam('user_sysno', 0);
        $user['employee_sysno'] = $this -> request -> getParam('employee_sysno', 0);
        $user['employeename'] = $this -> request -> getParam('employeename', 0);
        foreach($user as $value){
            if(!$value){
                COMMON::ApiJson('301', '用户参数错误'.$value);
            }
        }
        $P = new PoundsapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $res = $P->backFullCarWeigh($sysno, $param, $user);
        COMMON::ApiJson($res['code'],$res['message']);
    }

    /**
     * 退货空车过磅
     */
    public function backEmptyCarWeighAction(){
        self::getVersion();
        $sysno = $this -> request -> getParam('sysno', 0);
        if(!is_numeric($sysno)){
            COMMON::ApiJson('301', '退货磅码单SYSNO错误');
        }
        $param['cranename'] = $this -> request -> getParam('cranename', '');
        $param['emptycartime'] =  date('Y-m-d H:i:s');
        $param['emptycarqty'] = floor($this -> request -> getParam('emptycarqty', 0));
        if(!$param['emptycarqty']){
            COMMON::ApiJson('301', '请检查是否传入空车过磅重量');
        }
        $param['emptyloadometer'] = $this -> request -> getParam('emptyloadometer', 0);
        if(!$param['emptyloadometer']){
            COMMON::ApiJson('301', '请检查是否传入磅码类型');
        }
        //如果是出库重车过磅需要 传输每个客户出货量
        $customerList = $this -> request ->getParam('customer_list', '');
        $param['customerList'] = json_decode($customerList, true);
        if(empty($param['customerList'])){
            COMMON::ApiJson('301', '请检查客户退货信息是否正确');
        }

        $user['user_sysno'] = $this -> request -> getParam('user_sysno', 0);
        $user['employee_sysno'] = $this -> request -> getParam('employee_sysno', 0);
        $user['employeename'] = $this -> request -> getParam('employeename', 0);
        foreach($user as $value){
            if(!$value){
                COMMON::ApiJson('301', '用户参数错误'.$value);
            }
        }
        $P = new PoundsapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $res = $P->backEmptyCarWeigh($sysno, $param, $user);
        COMMON::ApiJson($res['code'],$res['message']);
    }

    /**
     * 版本验证
     * @return bool
     */
    private function getVersion(){
        $version = $this -> request -> getParam('version', 0);
        if(!$version){
            COMMON::ApiJson(300, '参数错误-版本号未传');
        }
        if(!version_compare(VERSION, $version, 'le')){
            COMMON::ApiJson(300, '你的当前版本过低，请升级客户端');
        }
        return true;
    }

}
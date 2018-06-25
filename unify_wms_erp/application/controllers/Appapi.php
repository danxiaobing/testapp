<?php
/**
 * @Author: danxiaobing
 * @Date:   2017-2-14
 * @Last Modified by:   danxiaobing
 * @Last Modified time: 2016-2-14
 */
class AppapiController extends Yaf_Controller_Abstract
{
    public $request;

    public $listSize = 10;//已审核默认获取数据条数
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
            'model' => 'appapi',
            'action' => 'getOtherExam',
//            'customer_name' => '二',
//            'goods_sysno' => '1',
//            'storagetank_sysno' => 1,
            'page'=> 1,
            'pagesize' => 100,
//            'id' =>  57, //审核数据ID
//            'auditopinion' => '合格',//审核意见
//            'status' => 7, // 6 审核不通过 4审核通过

            'user_sysno' => 3,
            'employee_sysno' => 92,
            'employeename' => '张飞',
        ];
        $secret = new Blowfish();

        $str = $secret -> encrypt(http_build_query($post));
        echo $str;
    }

    /**
     * 登录接口
     */
    public function loginAction()
    {
        //版本验证
//        self::getVersion();
        $params['username'] = $this->request->getParam('username', '');
        $params['userpwd'] = $this->request->getParam('userpwd', '');

        $S = new UserModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $P = new PassworderrorModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if ($user = $S->userLogin($params)) {
            if(!$user['employee_sysno'] || !$user['employeename']){
                COMMON::ApiJson('300', '请先绑定员工信息');
            }
            #权限验证
            $Pr = new PrivilegeModel(Yaf_Registry::get('db'), Yaf_Registry::get('mc'));
            if (!$Pr->check($this -> request -> getControllerName(), $this -> request -> getActionName(), $user )){
                COMMON::ApiJson('300', '您没有权限访问，请联系管理员。');
            }
            $ip = COMMON::getclientIp();
            $userUpdate = array('lastlogintime' => '=NOW()', 'lastloginip' => $ip);
            if ($S->setUserInfo($userUpdate, $user['sysno'])) {
                $userRes = [
                    'sysno' => $user['sysno'],
                    'username' => $user['username'],
                    'realname' => $user['realname'],
                    'employee_sysno' => $user['employee_sysno'],
                    'employeename' => $user['employeename'],
                    'lastlogintime' => $user['lastlogintime'],
                    'lastloginip' => $user['lastloginip']
                ];
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
     * 注册接口
     */
    public function registerAction(){
        $privileges = $this->request->getParam('role','');
        $U = new UserModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $password = $this->request->getParam('userpwd','');
        if(!$password){
            COMMON::ApiJson('300', '请填写密码');
        }

        $input = array(
            'username'      =>  $this->request->getParam('username',''),
            'userpwd'       =>  password_hash($password, 1, ['cost' => 10]),
            'employee_sysno'=>  $this->request->getParam('employee_sysno',''),
            'realname'      =>  $this->request->getParam('realname',''),
            'status'        =>  $this->request->getParam('status','2'),
            'isdel'         =>  0,
            'created_at'	=>'=NOW()',
            'updated_at'	=>'=NOW()'
        );

        switch ($id = $U->addUser($input,$privileges)) {
            case 'existence':
                COMMON::ApiJson('300','账号已存在');
                break;
            case false:
                COMMON::ApiJson('300','添加失败');
                break;
            default:
                $row = $U->getUserById($id);
                COMMON::ApiJson('200','注册成功-请等待管理员审核',$row);
                break;
        }
    }

    /**
     * 仓库首页
     */
    public function depotAction(){
        self::setUserMessage(); //设置用户信息
        $appInstance = new AppapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        //预计收益
        $finance_count = $appInstance -> getFinanceNum();
        $result['list']['count_finance'] = $finance_count;
        //昨日核销
        $yesterdayCancell = $appInstance -> getYesterdayCancellationNum();
        $result['list']['count_yesterday_cancell'] = $yesterdayCancell;
        //客户总数
        $customer_count = $appInstance -> getCustomerNum();
        $result['list']['count_customer'] = $customer_count;
        //库存总量
        $stroageArr = $appInstance -> getStorageCount();
        $result['list']['tank_stockqty'] = $stroageArr['tank_stockqty'];
        //可用库容量
        $result['list']['actualcapacity'] = $stroageArr['actualcapacity'];
        //货品类别
        $goods_count = $appInstance -> getGoodsNum();
        $result['list']['count_goods'] = $goods_count;
        COMMON::ApiJson('200', '请求成功', self::responseResult($result['list']));
    }

    public function getDepotListAction(){
        $search = array (
//            'bar_no'=>$this -> request -> getParam('bar_no',''),
//            'customer_name' => $this -> request ->getParam('customer_name',''),
//            'goods_name' => $this -> request ->getParam('goods_name',''),
//            'bar_status' => $this -> request ->getParam('bar_status','-100'),
//            'bar_isdel' => $this -> request ->getParam('bar_isdel','-100'),
//            'bar_coststatus' => $this -> request ->getParam('bar_coststatus','-100'),
//            'begin_time' => $this -> request ->getParam('startdate',date('Y-m-d',strtotime('-1 months'))),
            'end_time' => $this -> request ->getParam('enddate',date('Y-m-d',time())),
//            'orders'  => $this -> request -> getParam('orders',''),
//            'pageSize'  => $this -> request -> getParam('pagesize', 10),
//            'pageCurrent'  => $this -> request -> getParam('page', 1),
            'page' => false,
        );

        $appapiInstance = new AppapiModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $appapiInstance -> getFinanceList($search);
        COMMON::ApiJson('200', '请求成功', self::responseResult($list));
    }

    public function getDepotDetailAction(){
        $search = array (
            'customer_name' => $this -> request ->getParam('customer_name',''),
            'customer_sysno' => $this -> request ->getParam('customer_sysno',''),
            'end_time' => $this -> request ->getParam('end_time', date('Y-m-d',time())),
        );
        if(!$search['customer_name']){
            COMMON::ApiJson('300', '公司名称');
        }
        $appapiInstance = new AppapiModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $appapiInstance -> getFinanceDetail($search);
        COMMON::ApiJson('200', '请求成功', self::responseResult($list));
    }

    /**
     * @return 仓库库存首页
     */
    public function getStorageListAction()
    {
        $appapiInstance = new AppapiModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $appapiInstance -> getStorageList();
        COMMON::ApiJson('200', '请求成功', self::responseResult($list));
    }

    /**
     * 详细库存
     * getStorageGoodsDetailAction
     * @author ${USER}
     */
    public function getStorageGoodsDetailAction(){
        $startTime = $this -> request ->getParam('startTime',date('Y-m-d',strtotime('-1 day')));
        $endTime = $this -> request ->getParam('endTime',date('Y-m-d'));
        $search = array(
            'start_time'=>date('Y-m-d',strtotime($startTime)),//'-1 days',strtotime($date1)
            'end_time'=> date('Y-m-d H:i:s',strtotime('23 hours 59 minutes 59 seconds',strtotime($endTime))),//'1 days',strtotime($date2)
            'goods_sysno'=>$this -> request-> getParam('goods_sysno',''),
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
        );
        if(!$search['goods_sysno']){
            COMMON::ApiJson('300', '请传入商品ID');
        }
        $S = new AppapiModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $result=$S->getStorageDetail($search);
        COMMON::ApiJson('200', '请求成功', self::responseResult($result));
    }


    /**
     * 首页
     */
    public function indexAction(){
        self::setUserMessage(); //设置用户信息
        $B = new Report_BalanceModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $result = $B -> getGoodsCount();
        $result = $result ? $result : [];
        COMMON::ApiJson('200', '请求成功', self::responseResult($result));
    }

    /**
     * 商品前七天的库存 首页详情
     */
    public function goodsBalanceAction()
    {
        self::setUserMessage(); //设置用户信息
        $goods_sysno = $this->request->getParam('goods_sysno', 0);
        if(!$goods_sysno){
            COMMON::ApiJson('300', '参数错误-商品ID必传');
        }
        $day = $this->request->getParam('day', 7);
        $B = new Report_BalanceModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $result = $B -> getGoodsDayCount($goods_sysno, $day);
        $result = $result ? $result : [];
        COMMON::ApiJson('200', '请求成功', self::responseResult($result));
    }


    ############################################ 待审核 #####################################################
    /**
     * 待审核列表
     */
    public function  reviewedAction(){
        self::setUserMessage(); //设置用户信息
        $pagesize = $this->request->getParam('pagesize', '');
        $page = $this->request->getParam('page', '');
        $beginTime = $this->request->getParam('begin_time', '');
        $endTime = $this->request->getParam('end_time', '');
        //获取零租合同待审核列表
        $search = array(
            'startdate' => $beginTime,
            'enddate' =>   $endTime,
            'contractnodisplay' => $this->request->getParam('contractnodisplay', ''),
            'customer_sysno' => $this->request->getParam('customer_sysno', ''),
            'contractstatus' => 4,
            'saleemployee_sysno' => $this->request->getParam('saleemployee_sysno', ''),
            'csemployee_sysno' => $this->request->getParam('csemployee_sysno', ''),
            'contracttype' => $this->request->getParam('contracttype', ''),
            'contracttypeArr' => [1,2],
            'page' => false,
        );
        $contractInstance = new ContractModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $contractList = $contractInstance->searchContract($search);
        $contractArr = [];
        if($contractList['totalRow'] > 0) {
            foreach ($contractList['list'] as $val) {
                $val['type'] = 1;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $contractArr[] = $val;
            }
        }

        //获取船入库预约待审核列表
        $search = array(
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'bar_bookinginstatus' => 4,
            'page'=> false
        );
        $bookshipinInstance = new BookshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $bookShipList = $bookshipinInstance->searchBookshipin($search);
        $bookShipArr = [];
        if($bookShipList['totalRow'] > 0) {
            foreach ($bookShipList['list'] as $val) {
                $val['type'] = 2;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $bookShipArr[] = $val;
            }
        }

        //获取车入库预约列表
        $search = array(
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'bar_no' => $this->request->getParam('bar_no', ''),
            'customer_sysno' => $this->request->getParam('customer_sysno', ''),
            'bar_stockintype' => $this->request->getParam('bar_stockintype', '2'),
            'bar_bookcarinstatus' => 4,
            'page'=> false,
        );
        $BookcarInInstance = new BookcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $bookCarInList = $BookcarInInstance->searchBookcarin($search);
        $bookCarInArr = [];
        if($bookCarInList['totalRow'] > 0) {
            foreach ($bookCarInList['list'] as $val) {
                $val['type'] = 3;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $bookCarInArr[] = $val;
            }
        }

        //获取船出库预约
        $search = array (
            'bookingoutno'=>$this->request->getParam('bookingoutno',''),
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'bar_name' => $this->request->getParam('bar_name',''),
            'bar_status'=>$this-> request ->getParam('bar_status',4),
            'bar_docsource'=>$this->request->getParam('bar_docsource',''),
            'stockouttype' => 1,
            'page' => false,
            'orders'  => 'created_at desc',
        );
        $bookOutInstance = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $bookOutShipList = $bookOutInstance -> searchBookout($search);
        $bookOutShipArr = [];
        if($bookOutShipList['totalRow'] > 0) {
            foreach ($bookOutShipList['list'] as $val) {
                $val['type'] = 4;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $bookOutShipArr[] = $val;
            }
        }

        //获取车出库预约
        $search = array (
            'bookingoutno' => $this->request->getParam('bookingoutno',''),
            'begin_time'=> $beginTime,
            'end_time' => $endTime,
            'bar_name' => $this->request->getParam('bar_name',''),
            'bar_status'=>$this->request->getParam('bar_status',4),
            'bar_docsource' => $this->request->getParam('bar_docsource',''),
            'stockouttype' => 2,
            'page' => false,
            'orders'  => 'created_at desc',
        );
        $bookOutCarList = $bookOutInstance -> searchBookout($search);
        $bookOutCarArr = [];
        if($bookOutCarList['totalRow'] > 0) {
            foreach ($bookOutCarList['list'] as $val) {
                $val['type'] = 5;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $bookOutCarArr[] = $val;
            }
        }

        //获取船出库审核
        $search = array (
            'bar_no'=>$this->request->getParam('bar_no',''),
            'bar_name' => $this->request->getParam('bar_name',''),
            'bar_stockoutstatus'=> 3,
            'page' => false,
            'stockouttype' => 1,
        );
        $stockOutInstance = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $stockShipOutList = $stockOutInstance -> searchStockout($search);
        $stockShipOutArr = [];
        if($stockShipOutList['totalRow'] > 0) {
            foreach ($stockShipOutList['list'] as $val) {
                $val['type'] = 6;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $stockShipOutArr[] = $val;
            }
        }

        //获取货权转移审核
        $search = array (
            'stocktransdate_start'=>$this->request->getParam('stocktransdate_start',''),
            'stocktransdate_end' => $this->request->getParam('stocktransdate_end',''),
            'stocktransstatus'=> 3,
            'sale_customer_sysno'=>$this->request->getParam('sale_customer_sysno',''),
            'buy_customer_sysno'=>$this->request->getParam('buy_customer_sysno',''),
            'docsource'=>$this->request->getParam('docsource',''),
            'page' => false,
        );
        $stockTransInstance = new StocktransModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $stockTransList = $stockTransInstance->getList($search);
        $stockTransArr = [];
        if($stockTransList['totalRow'] > 0) {
            foreach ($stockTransList['list'] as $val) {
                $val['type'] = 7;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $stockTransArr[] = $val;
            }
        }

        //船入库审核
        $search = array(
            'bar_status' =>$this->request->getParam('bar_status', '-100'),
            'bar_isdel' =>$this->request->getParam('bar_isdel', '-100'),
            'bar_stockintype' =>$this->request->getParam('bar_stockintype', 1),
            'bar_stockinstatus' =>$this->request->getParam('bar_stockinstatus', 3),
            'page' => false,
            'begin_time' => $beginTime,
            'end_time' => $endTime,
        );
        $S = new StockshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $StockshipinList = $S->searchStockshipin($search);
        $stockShipInArr = [];
        if($StockshipinList['totalRow'] > 0) {
            foreach ($StockshipinList['list'] as $val) {
                $val['type'] = 8;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $stockShipInArr[] = $val;
            }
        }

        //合同评审
//        $user = Yaf_Registry::get(SSN_VAR);
//        $E = new EmployeeModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
//        $employee = $E->getEmployeeById($user['employee_sysno']);
//        $search = array(
//            'startDate' => $beginTime,
//            'endDate' => $endTime,
//            'customerId' => $this->request->getParam('obj_customerId', ''),
//            'contStatus' => $this->request->getParam('contStatus', 3),
//            'department_sysno' => $employee['department_sysno'] ? $employee['department_sysno'] : 0,
//            'contractnodisplay' => $this->request->getParam('contractnodisplay',''),
//        );
//        $pages = $this->request->getParam('pageSize', '20000');
//        $pagec = $this->request->getParam('pageCurrent', '1');
//        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
//        $contractReviewList = $C->getReviewList($search, $pages, $pagec);
//        $contractReviewArr = [];
//        if($contractReviewList['totalRow'] > 0) {
//            foreach ($contractReviewList['list'] as $val) {
//                $val['type'] = 9;
//                $contractReviewArr[] = $val;
//            }
//        }

        //获取管入库预约列表
        $search = array(
            'bar_no' => $this->request->getParam('bar_no', ''),
            'customer_sysno' => $this->request->getParam('customer_sysno', ''),
            'bar_status' => $this->request->getParam('bar_status', '-100'),
            'bar_isdel' => $this->request->getParam('bar_isdel', '-100'),
            'bar_stockintype' => 3,
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'bar_bookinginstatus' => 4,
            'page'=> false,
        );
        $S = new BookpipelineinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $bookPipelineInList = $S->searchBookshipin($search);
        $bookPipelineInArr = [];
        if($bookPipelineInList['totalRow'] > 0) {
            foreach ($bookPipelineInList['list'] as $val) {
                $val['type'] = 10;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $bookPipelineInArr[] = $val;
            }
        }

        //获取管入库
        $search = array(
            'bar_no' => $this->request->getParam('bar_no', ''),
            'bar_name' => $this->request->getParam('bar_name', ''),
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'bar_stockinstatus' => 3,
            'bar_stockintype' => 3,
            'page' => false,
        );

        $stockInInstance = new StockpipeinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $pipelineInList = $stockInInstance -> searchStockpipein($search);
        $pipelineInArr = [];
        if($pipelineInList['totalRow'] > 0) {
            foreach ($pipelineInList['list'] as $val) {
                $val['type'] = 11;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $pipelineInArr[] = $val;
            }
        }

        //整租合同待审核
        $search = array(
            'startdate' => $beginTime,
            'enddate' =>   $endTime,
            'contractstatus' => 4,
            'contracttypeArr' => [3,4],
            'page' => false,
        );

        $rentContractList = $contractInstance -> searchContract($search);
        $renContractArr = [];
        if($rentContractList['totalRow'] > 0) {
            foreach ($rentContractList['list'] as $val) {
                $val['type'] = 12;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $renContractArr[] = $val;
            }
        }

        //管出库预约审批
        $search = array (
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'bar_status'=> 4,
            'stockouttype' => 3,
            'page' => false,
        );
        $B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $pipelineBookOutList = $B->searchBookout($search);
        $pipelineBookOutArr = [];
        if($pipelineBookOutList['totalRow'] > 0) {
            foreach ($pipelineBookOutList['list'] as $val) {
                $val['type'] = 13;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $pipelineBookOutArr[] = $val;
            }
        }

        //管出库审批
        $search = array(
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'bar_stockoutstatus' => 3,
            'page' => false,
            'stockouttype' => 3,
        );
        $pipelineOutList = $stockOutInstance -> searchStockout($search);
        $pipelineOutArr = [];
        if($pipelineOutList['totalRow'] > 0) {
            foreach ($pipelineOutList['list'] as $val) {
                $val['type'] = 14;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $pipelineOutArr[] = $val;
            }
        }

        //倒罐审批
        $search = array (
            'bookingretankdate' => $beginTime,
            'bookingretankdate_end' => $endTime,
            'stockretankstatus' => 4,
            'page' => false,
        );
        $retankuInstance = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $retankList = $retankuInstance -> searchapplyretank($search);
        $retankArr = [];
        if($retankList['totalRow'] > 0) {
            foreach ($retankList['list'] as $val) {
                $val['type'] = 15;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $retankArr[] = $val;
            }
        }

        //让步审批
        $search = array(
            'begin_time'  => $beginTime,
            'end_time'  => $endTime,
            'orderstatus'  => 6,
            'page' => false,
        );
        $qualityChcekInstance = new QualitycheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $qualityCheckList = $qualityChcekInstance->getQualitycheckList($search);
        $qualityCheckArr = [];
        $bookno = array(1,4,6,8);    //预约状态
        $stockno = array(2,3,5,7,9,10);  //订单状态
        foreach($qualityCheckList['list'] as $key=>$val){
            //判断业务单号
            if(in_array($val['businesstype'],$bookno)){
                $value['orderno'] = $val['bookingno'];
            }
            if(in_array($val['businesstype'],$stockno)){
                $value['orderno'] = $val['stockno'];
            }
            $val['type'] = 16;
            if(isset($val['customername']))
            {
                $val['customer_name'] = $val['customername'];
            }
            $qualityCheckArr[] = $val;
        }
        //退货审批
        $search = array (
            'begin_time'=> $beginTime,
            'end_time' => $endTime,
            'stockinstatus' => 3,
            'page' => false,
        );
        $rebackInstance = new RebackModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $rebackList = $rebackInstance->searchReback($search);
        $rebackArr = [];
        if($rebackList['totalRow'] > 0) {
            foreach ($rebackList['list'] as $val) {
                $val['type'] = 17;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $rebackArr[] = $val;
            }
        }
        $resultArr =  self::sort_arr(
            array_merge(
                $contractArr,
                $bookShipArr,
                $bookCarInArr,
                $bookOutShipArr,
                $bookOutCarArr,
                $stockShipOutArr,
                $stockTransArr,
                $stockShipInArr,
//                $contractReviewArr,
                $bookPipelineInArr,
                $pipelineInArr,
                $renContractArr,
                $pipelineBookOutArr,
                $pipelineOutArr,
                $retankArr,
                $qualityCheckArr,
                $rebackArr
            ), 'updated_at'
        );
        if(isset($pagesize) && $page==false){
            $data = $resultArr;
        }else{
            $data['totalRow'] = count($resultArr);
            $data['pageCurrent'] = $page;
            $data['totalPage'] = ceil($data['totalRow'] / $pagesize);
            $list=array_chunk($resultArr, $pagesize, false);
            $data['list']= $list[$page-1];
        }

        COMMON::ApiJson('200', '请求成功', self::responseResult($data));
    }



    /**
     * 已评审   合同列表 9
     */
//    public function contractPingShenAction(){
//        self::setUserMessage();
//        $pagesize = $this->request->getParam('pagesize', '10');
//        $page = $this->request->getParam('page', '1');
//        $user = Yaf_Registry::get(SSN_VAR);
//        $search = array(
//            'startDate' => $this -> request -> getParam('startDate', ''),
//            'endDate' => $this -> request -> getParam('endDate', ''),
//            'contStatus' => 4,
//            'employee_sysno' => $user['employee_sysno'] ? $user['employee_sysno'] : 0,
//            'contractnodisplay' => $this -> request -> getParam('contractnodisplay',''),
//        );
//        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
//        $params = $C->getReviewedList($search, $pagesize, $page);
//        if (!$params['list'] || empty($params['list'])) {
//            COMMON::ApiJson('200', '请求成功', []);
//        }else{
//            COMMON::ApiJson('200', '请求成功', self::responseResult($params));
//        }
//    }



    ############################################ 已审核 #####################################################
    /**
     * 已审核合同列表
     */
    public function contractAction()
    {
        self::setUserMessage(); //设置用户信息
        $pagesize = $this->request->getParam('pagesize', '10');
        $page = $this->request->getParam('page', '1');
        $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $seach = array(
            'contractstatus' =>5,
            'contracttypeArr' => [1,2,3,4],
            'pageCurrent'=> $page,
            'pageSize' => $pagesize
        );
        $row = $contract->searchContract($seach);
        if(!$row['list'] || empty($row['list']))
        {
            COMMON::ApiJson('200', '请求成功', []);
        }else{
            COMMON::ApiJson('200', '请求成功', self::responseResult($row));
        }
    }

    /**
     * 已审核入库列表
     */
    public function getExaminaInListAction()
    {
        self::setUserMessage(); //设置用户信息
        $pagesize = $this -> request -> getParam('pagesize', '');
        $page = $this -> request -> getParam('page', '');
        //船入库预约
        $bookshipin = new BookshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_bookinginstatus' =>5,
            'bar_stockintype' => 1,
            'pageCurrent'=> 1,
            'pageSize' => $this -> listSize
        );
        $boonkshipinList = $bookshipin->searchBookshipin($search);
        $boonkshipinArr = [];
        if($boonkshipinList['totalRow'] > 0) {
            foreach ($boonkshipinList['list'] as $val) {
                $val['type'] = 1;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $boonkshipinArr[] = $val;
            }
        }

        //船入库订单 已审核
        $search = array(
            'bar_isdel' =>0,
            'bar_stockintype' =>1,
            'bar_stockinstatus' =>4,
            'pageCurrent'=> 1,
            'pageSize' => $this -> listSize
        );
        $stcokshipInInstance = new StockshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $stcokshipInList = $stcokshipInInstance -> searchStockshipin($search);
        $stcokshipInArr = [];
        if($stcokshipInList['totalRow'] > 0) {
            foreach ($stcokshipInList['list'] as $val) {
                $val['type'] = 2;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $stcokshipInArr[] = $val;
            }
        }

        //车入库预约
        $bookcarin = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_bookcarinstatus' => 5,
            'bar_stockintype' => 2,
            'pageCurrent'=> 1,
            'pageSize' => $this -> listSize
        );
        $boonkcarinList = $bookcarin->searchBookcarin($search);
        $bookcarInArr = [];
        if($boonkcarinList['totalRow'] > 0) {
            foreach ($boonkcarinList['list'] as $val) {
                $val['type'] = 3;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $bookcarInArr[] = $val;
            }
        }

        //管入库预约
        $search = array(
            'bar_isdel' => 0,
            'bar_stockintype' => 3,
            'bar_bookinginstatus' => 5,
            'pageCurrent' => 1,
            'pageSize' => $this -> listSize
        );
        $bookpipelineinInstance = new BookpipelineinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $bookpipelineinList = $bookpipelineinInstance -> searchBookshipin($search);
        $bookpipelineinArr = [];
        if($bookpipelineinList['totalRow'] > 0) {
            foreach ($bookpipelineinList['list'] as $val) {
                $val['type'] = 4;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $bookpipelineinArr[] = $val;
            }
        }

        //管入库
        $search = array(
            'bar_isdel' => 0,
            'bar_stockintype' => 3,
            'bar_stockinstatus' => 4,
            'pageCurrent' => 1,
            'pageSize' => $this -> listSize,
        );
        $stockPipeinInstance = new StockpipeinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $pipeInList = $stockPipeinInstance -> searchStockpipein($search);
        $pipeInArr = [];
        if($pipeInList['totalRow'] > 0) {
            foreach ($pipeInList['list'] as $val) {
                $val['type'] = 5;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $pipeInArr[] = $val;
            }
        }

        $resultArr =  self::sort_arr(
            array_merge(
                $boonkshipinArr,
                $stcokshipInArr,
                $bookcarInArr,
                $bookpipelineinArr,
                $pipeInArr
            ), 'updated_at'
        );
        if(isset($pagesize) && $page==false){
            $data = $resultArr;
        }else{
            $data['totalRow'] = count($resultArr);
            $data['pageCurrent'] = $page;
            $data['totalPage'] = ceil($data['totalRow'] / $pagesize);
            $list=array_chunk($resultArr, $pagesize, false);
            $data['list']= $list[$page-1];
        }

        COMMON::ApiJson('200', '请求成功', self::responseResult($data));

    }



    //出库已审核
    public function getExaminaOutListAction(){
        self::setUserMessage(); //设置用户信息
        $pagesize = $this->request->getParam('pagesize', '10');
        $page = $this->request->getParam('page', '1');
        //船出库预约
        $search = array (
            'stockouttype' => 1,
            'bar_status'   => 5,
            'pageCurrent'=> 1,
            'pageSize' => $this -> listSize,
            'orders' => 'created_at DESC'
        );
        $bookoutInstance = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $bookShipOutList = $bookoutInstance -> searchBookoutForApi($search);
        $bookShipOutArr = [];
        if($bookShipOutList['totalRow'] > 0) {
            foreach ($bookShipOutList['list'] as $val) {
                $val['type'] = 1;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $bookShipOutArr[] = $val;
            }
        }

        //船出库
        $param = array(
            'bar_stockoutstatus' =>4,
            'stockouttype' => 1,
            'pageCurrent'=> 1,
            'pageSize' => $this -> listSize,
            'orders' => 'created_at DESC'
        );
        $stockOutInstance = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $shipOutList = $stockOutInstance -> searchStockout($param);
        $shipOutArr =[];
        if(is_array($shipOutList['list']) && !empty($shipOutList['list'])){
            foreach ($shipOutList['list'] as  $val) {
                $val['unitname'] = '吨';
                $val['type'] = 2;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $shipOutArr[] = $val;
            }
        }

        //车出库预约
        $search = array (
            'stockouttype' => 2,
            'bar_status'   => 6,
            'pageCurrent'=> 1,
            'pageSize' => $this -> listSize,
            'orders' => 'created_at DESC'
        );
        $bookcaroutList = $bookoutInstance->searchBookoutForApi($search);
        $bookcaroutArr = [];
        if($bookcaroutList['totalRow'] > 0) {
            foreach ($bookcaroutList['list'] as $val) {
                $val['type'] = 3;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $bookcaroutArr[] = $val;
            }
        }
        //管出库预约
        $search = array (
            'bar_status'=> 5,
            'stockouttype' => 3,
            'pageCurrent'=> 1,
            'pageSize' => $this -> listSize,
            'orders' => 'created_at DESC'
        );
        $B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $pipelineBookOutList = $B->searchBookoutForApi($search);
        $pipelineBookOutArr = [];
        if($pipelineBookOutList['totalRow'] > 0) {
            foreach ($pipelineBookOutList['list'] as $val) {
                $val['type'] = 4;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $pipelineBookOutArr[] = $val;
            }
        }

        //管出库审批
        $search = array(
            'bar_stockoutstatus' => 4,
            'page' => false,
            'stockouttype' => 3,
            'pageCurrent'=> 1,
            'pageSize' => $this -> listSize,
            'orders' => 'created_at DESC'
        );
        $pipelineOutList = $stockOutInstance -> searchStockout($search);
        $pipelineOutArr = [];
        if($pipelineOutList['totalRow'] > 0) {
            foreach ($pipelineOutList['list'] as $val) {
                $val['type'] = 5;
                if(isset($val['customername']))
                {
                    $val['customer_name'] = $val['customername'];
                }
                $pipelineOutArr[] = $val;
            }
        }

        $resultArr =  self::sort_arr(
            array_merge(
                $bookShipOutArr,
                $shipOutArr,
                $bookcaroutArr,
                $pipelineBookOutArr,
                $pipelineOutArr
            ), 'updated_at'
        );
        if(isset($pagesize) && $page==false){
            $data = $resultArr;
        }else{
            $data['totalRow'] = count($resultArr);
            $data['pageCurrent'] = $page;
            $data['totalPage'] = ceil($data['totalRow'] / $pagesize);
            $list=array_chunk($resultArr, $pagesize, false);
            $data['list']= $list[$page-1];
        }

        COMMON::ApiJson('200', '请求成功', self::responseResult($data));
    }

    /**
     * 已审核货转单列表
     */
    public function stocktransAction(){
        self::setUserMessage(); //设置用户信息
        $pagesize = $this->request->getParam('pagesize', '10');
        $page = $this->request->getParam('page', '1');
        $T = new StocktransModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array (
            'stocktransstatus'=>4,
            'pageCurrent'=> $page,
            'pageSize' => $pagesize
        );

        $row = $T->getList($search);
        $result = $row ? $row : [];
        COMMON::ApiJson('200', '请求成功', self::responseResult($result));
    }

    /**
     * 已审核倒罐列表
     */
    public function getRetankExamAction()
    {
        self::setUserMessage(); //设置用户信息
        $pagesize = $this->request->getParam('pagesize', '10');
        $page = $this->request->getParam('page', '1');
        $search = array (
            'stockretankstatus' => 5,
            'pageCurrent'=> $page,
            'pageSize' => $pagesize
        );
        $retankuInstance = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $retankArr = $retankuInstance -> searchapplyretank($search);
        COMMON::ApiJson('200', '请求成功', self::responseResult($retankArr));
    }

    //其他审批
    public function getOtherExamAction(){
        self::setUserMessage(); //设置用户信息
        //让步审批
        $pagesize = $this->request->getParam('pagesize', '10');
        $page = $this->request->getParam('page', '1');
        $search = array(
            'orderstatus'  => 7,
            'pageCurrent'=> 1,
            'pageSize' => $this -> listSize,
            'orders' => 'created_at DESC',
        );
        $qualityChcekInstance = new QualitycheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $qualityCheckList = $qualityChcekInstance->getQualitycheckList($search);
        $qualityCheckArr = [];
        $bookno = array(1,4,6,8);    //预约状态
        $stockno = array(2,3,5,7,9,10);  //订单状态
        foreach($qualityCheckList['list'] as $key=>$value){
            //判断业务单号
            if(in_array($value['businesstype'],$bookno)){
                $value['orderno'] = $value['bookingno'];
            }
            if(in_array($value['businesstype'],$stockno)){
                $value['orderno'] = $value['stockno'];
            }
            $value['type'] = 1;
            $qualityCheckArr[] = $value;
        }

        //退货审批
        $search = array (
            'stockinstatus' => 4,
            'pageCurrent'=> 1,
            'pageSize' => $this -> listSize,
            'orders' => 'created_at DESC'
        );
        $rebackInstance = new RebackModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $rebackList = $rebackInstance->searchReback($search);
        $rebackArr = [];
        if($rebackList['totalRow'] > 0) {
            foreach ($rebackList['list'] as $val) {
                $val['type'] = 2;
                $rebackArr[] = $val;
            }
        }

        $resultArr =  self::sort_arr(
            array_merge(
                $qualityCheckArr,
                $rebackArr
            ), 'updated_at'
        );
        if(isset($pagesize) && $page==false){
            $data = $resultArr;
        }else{
            $data['totalRow'] = count($resultArr);
            $data['pageCurrent'] = $page;
            $data['totalPage'] = ceil($data['totalRow'] / $pagesize);
            $list=array_chunk($resultArr, $pagesize, false);
            $data['list']= $list[$page-1];
        }

        COMMON::ApiJson('200', '请求成功', self::responseResult($data));
    }


//    /**
//     * 已审核  车出库预约列表 5
//     */
//    public function bookOutCarAction()
//    {
//        self::setUserMessage(); //设置用户信息
//        $pagesize = $this->request->getParam('pagesize', '10');
//        $page = $this->request->getParam('page', '1');
//        //车出库预约
//        $search = array (
//            'stockouttype' => 2,
//            'bar_status'   => '5,6',
//            'pageCurrent'=> $page,
//            'pageSize' => $pagesize
//        );
//        $bookout = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
//        $bookcarout_list = $bookout->searchBookout($search);
//        if (!$bookcarout_list['list'] || empty($bookcarout_list['list'])) {
//            COMMON::ApiJson('200', '请求成功', []);
//        }else{
//            COMMON::ApiJson('200', '请求成功', self::responseResult($bookcarout_list));
//        }
//    }
//
//
//    /**
//     * 已审核 车入库列表
//     */
//    public function carInAction(){
//        self::setUserMessage(); //设置用户信息
//        $pagesize = $this->request->getParam('pagesize', '10');
//        $page = $this->request->getParam('page', '1');
//        $C = new StockcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
//        $search = array(
//            'bar_stockinstatus' =>4,
//            'bar_stockintype' => 2,
//            'pageCurrent'=> $page,
//            'pageSize' => $pagesize
//        );
//        $list = $C->searchStockcarin($search);
//        if (!$list['list'] || empty($list['list'])) {
//            COMMON::ApiJson('200', '请求成功', []);
//        }else{
//            COMMON::ApiJson('200', '请求成功', self::responseResult($list));
//        }
//    }
//
//    /**
//     * 已审核 车出库列表
//     */
//    public function carOutAction(){
//        self::setUserMessage(); //设置用户信息
//        $pagesize = $this->request->getParam('pagesize', '10');
//        $page = $this->request->getParam('page', '1');
//        //车出库审核
//        $param = array(
//            'bar_stockoutstatus' =>4,
//            'stockouttype' => 2,
//            'pageCurrent'=> $page,
//            'pageSize' => $pagesize
//        );
//        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
//        $info = $S->searchStockout($param);
//        foreach ($info['list'] as $key => $value) {
//            $info['list'][$key]['unitname'] = '吨';
//        }
//        if (!$info['list'] || empty($info['list'])) {
//            COMMON::ApiJson('200', '请求成功', []);
//        }else{
//            COMMON::ApiJson('200', '请求成功', self::responseResult($info));
//        }
//    }
//
//    /**
//     * 已审核  船出库预约列表 4
//     */
//    public function bookOutShipAction(){
//        self::setUserMessage(); //设置用户信息
//        $pagesize = $this->request->getParam('pagesize', '10');
//        $page = $this->request->getParam('page', '1');
//        $search = array (
//            'stockouttype' => 1,
//            'bar_status'   => 5,
//            'pageCurrent'=> $page,
//            'pageSize' => $pagesize
//        );
//
//        $bookout = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
//        $bookshipout_list = $bookout->searchBookout($search);
//        if (!$bookshipout_list['list'] || empty($bookshipout_list['list'])) {
//            COMMON::ApiJson('200', '请求成功', []);
//        }else{
//            COMMON::ApiJson('200', '请求成功', self::responseResult($bookshipout_list));
//        }
//    }
//
//
//    /*
//    * 已审核  船入库列表 8
//     */
//    public function shipInAction(){
//        self::setUserMessage(); //设置用户信息
//        $pagesize = $this->request->getParam('pagesize', '10');
//        $page = $this->request->getParam('page', '1');
//        $search = array(
//            'bar_isdel' =>0,
//            'bar_stockintype' =>1,
//            'bar_stockinstatus' =>4,
//            'pageCurrent'=> $page,
//            'pageSize' => $pagesize
//        );
//        $S = new StockshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
//        $list = $S->searchStockshipin($search);
//        if (!$list['list'] || empty($list['list'])) {
//            COMMON::ApiJson('200', '请求成功', []);
//        }else{
//            COMMON::ApiJson('200', '请求成功', self::responseResult($list));
//        }
//    }
//
//    /**
//     * 已审核  船出库列表 6
//     */
//    public function shipOutAction(){
//        self::setUserMessage(); //设置用户信息
//        $pagesize = $this->request->getParam('pagesize', '10');
//        $page = $this->request->getParam('page', '1');
//        $param = array(
//            'bar_stockoutstatus' =>4,
//            'stockouttype' => 1,
//            'pageCurrent'=> $page,
//            'pageSize' => $pagesize,
//            'orders' => 'updated_at desc'
//        );
//        $O = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
//        $info = $O->searchStockout($param);
//        if(is_array($info['list']) && !empty($info['list'])){
//            foreach ($info['list'] as $key => $value) {
//                $info['list'][$key]['unitname'] = '吨';
//            }
//        }
//        if (!$info['list'] || empty($info['list'])) {
//            COMMON::ApiJson('200', '请求成功', []);
//        }else{
//            COMMON::ApiJson('200', '请求成功', self::responseResult($info));
//        }
//    }

    ############################################ 审核 #####################################################
    /**
     * 合同审核
     */
    public function contractReviewedAction()
    {
        self::setUserMessage(); //设置用户信息
        $user = Yaf_Registry::get(SSN_VAR);
        #权限验证
        $Pr = new PrivilegeModel(Yaf_Registry::get('db'), Yaf_Registry::get('mc'));
        if (!$Pr->check($this -> request -> getControllerName(), $this -> request -> getActionName(), $user )){
            COMMON::ApiJson('300', '您没有权限访问，请联系管理员。');
        }
        $id = $this -> request -> getParam('id', 0);
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $auditopinion = $this -> request -> getParam('auditopinion', '');//审核意见
        $type = $this -> request -> getParam('status', '');//3 审核通过   4 审核不通过
        if(!in_array($type, [3,4])){
            COMMON::ApiJson('300', '非法操作！');
        }
        if ($type == 4 && $auditopinion == '') {
            COMMON::ApiJson('300', '审核意见不能为空');
        }
        $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $contract->getContractById($id);
        if(!$list){
            COMMON::ApiJson('300', '未找到该数据！');
        }
        if($list['contractstatus'] != 4){
            COMMON::ApiJson('300', '该合同不是待审核状态');
        }
        $contractlist = array(
            'customer_id' => $list['sysno'],
            'contractnodisplay' => $list['contractnodisplay'],
            'customername' => $list['customername'],
            'contracttype'=>$list['contracttype'],
            'contractstartdate' => $list['contractstartdate'],
            'contractenddate' => $list['contractenddate'],
            'contractcostdate' => $list['contractcostdate'],
            'costtype' => $list['costtype'],
            'updated_at'	=> 	'=NOW()'
        );
        $contractlist['updated_at'] = '=NOW()';

        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $goodsdetaildata = $goods -> getGoodsandpriceByid($id);
        $res = $contract->examecontract($id, $contractlist, $goodsdetaildata, $type, $auditopinion);
        if ($res) {
            COMMON::ApiJson('200', '审核成功');
        } else {
            COMMON::ApiJson('300', '审核失败');
        }
    }

    /**
     * 船入库预约审核
     */
    public function bookingShipReviewedAction()
    {
        self::setUserMessage(); //设置用户信息
        #权限验证
        $user = Yaf_Registry::get(SSN_VAR);
        $Pr = new PrivilegeModel(Yaf_Registry::get('db'), Yaf_Registry::get('mc'));
        if (!$Pr->check($this -> request -> getControllerName(), $this -> request -> getActionName(), $user )){
            COMMON::ApiJson('300', '您没有权限访问，请联系管理员。');
        }
        $id = $this -> request -> getParam('id', 0);
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $status = $this -> request -> getParam('status', ''); //8 驳回 7 审核不通过 5审核通过
        $flowemo = $this -> request -> getParam('auditopinion', '');//审核意见
        if(!in_array($status,[5, 7])){
            COMMON::ApiJson('300', '参数错误-状态');
        }
        if ($status == 6 && $flowemo == '') {
            COMMON::ApiJson('300', '审核意见不能为空');
        }

        $S = new BookshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $detail = $S->getBookshipinById($id);
        if($detail['stockintype'] != 1){
            COMMON::ApiJson('300', '数据错误');
        }
        if($detail['bookinginstatus'] != 4){
            COMMON::ApiJson('300', '该数据不是待审核状态');
        }

        $input = array(
            'bookinginstatus' => $status,
            'flowmemo' => $flowemo,
            'updated_at' => '=NOW()'
        );
        $res = $S->AuditBookshipin($id, $input);
        if ($res) {
            COMMON::ApiJson('200', '审核成功');
        } else {
            COMMON::ApiJson('300', '审核失败');
        }
    }

    /**
     *  船入库审核
     */
    public function stockShipInReviewedAction()
    {
        self::setUserMessage(); //设置用户信息
        #权限验证
        $user = Yaf_Registry::get(SSN_VAR);
        $Pr = new PrivilegeModel(Yaf_Registry::get('db'), Yaf_Registry::get('mc'));
        if (!$Pr->check($this -> request -> getControllerName(), $this -> request -> getActionName(), $user )){
            COMMON::ApiJson('300', '您没有权限访问，请联系管理员。');
        }
        $id = $this -> request -> getParam('id', 0);
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $stockmarks = $this -> request -> getParam('auditopinion', '');// 审核意见
        $status = $this -> request -> getParam('status', ''); //审核状态 6 审核不通过 4审核通过
        if(!in_array($status,[4, 6])){
            COMMON::ApiJson('300', '参数错误-状态');
        }
        if ($status == 6 && $stockmarks == '') {
            COMMON::ApiJson('300', '审核备注不能为空');
        }
        $S = new StockshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $T = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $detail = $S -> getStockshipinById($id);
        if ($detail['stockinstatus'] != 3) {
            COMMON::ApiJson('300', '该数据不是审核状态');
        }
        $bookingin_no = $detail['bookingin_no'];
        $booking_in_sysno = $detail['booking_in_sysno'];
        $stockshipindetaildata = $S ->getStockshipindetailById($id);

        $stockOutInstance = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $booingdata = [
            'sysno' => $booking_in_sysno,
            'isqualitycheck' => $detail['isqualitycheck'],
            'ispipelineorder' => $detail['ispipelineorder'],
            'isberthorder' => $detail['isberthorder'],
        ];
        $pbqData = $stockOutInstance -> getPBQ($booingdata, 2);
        if($status ==4 ) {
            if($detail['isqualitycheck'] == 1 &&  empty($pbqData['qualitycheck'])){
                COMMON::ApiJson('300', '请完成品质检查才可入库!');
            }
            if($detail['isqualitycheck'] == 1 && !in_array($pbqData['qualitycheck'][0]['orderstatus'], [4, 7])){
                COMMON::ApiJson(300, '品质检查不合格');
            }
            if($detail['ispipelineorder'] == 1 && empty($pbqData['pipelineorder'])){
                COMMON::ApiJson(300, '请完成管线才可入库!');
            }
            if($detail['isberthorder'] == 1 && empty($pbqData['berthorder'])){
                COMMON::ApiJson(300, '请完成泊位才可入库!');
            }
        }

        //取出实际入库量
        $beqty = 0;
        foreach ($stockshipindetaildata as $key => $value) {
            $beqty = $beqty + $value['beqty'];
            $storagetank_sysno = $value['storagetank_sysno'];
            $goods_sysno = $stockshipindetaildata[$key]['goods_sysno'];
            $flag = $T->storagetankgoodsbyid($storagetank_sysno);
            if ($flag != $goods_sysno) {
                COMMON::ApiJson('300', '该储罐还有其他货品存量');
            }
        }
        $book = new BookshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $booklist = $book->getBookshipinById($booking_in_sysno);
        $input = array(
            'stockinstatus' => $status,
            'updated_at' => '=NOW()',
            'customer_sysno'=> $detail['customer_sysno'],
            'customername'=> $detail['customername'],
        );
        $flag = $S->updateStockshipin($id, $input, $stockshipindetaildata, $stockmarks, $status, $beqty);
        if ($flag['code']==200) {
            if ($booklist['docsource'] == 2) {
                COMMON::editStockInStatus($bookingin_no, $beqty);
            }
            COMMON::ApiJson('200', '审核成功');
        } else {
            COMMON::ApiJson('300', $flag['message']);
        }
    }


    /**
     * 车入库预约审核
     */
    public function bookingCarReviewedAction()
    {
        self::setUserMessage(); //设置用户信息
        #权限验证
        $user = Yaf_Registry::get(SSN_VAR);
        $Pr = new PrivilegeModel(Yaf_Registry::get('db'), Yaf_Registry::get('mc'));
        if (!$Pr->check($this -> request -> getControllerName(), $this -> request -> getActionName(), $user )){
            COMMON::ApiJson('300', '您没有权限访问，请联系管理员。');
        }
        $id = $this -> request -> getParam('id', 0);
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $bookinginstatus = $this -> request -> getParam('status', ''); //审核状态 6 审核不通过 4 审核通过
        $auditreason = $this -> request -> getParam('auditopinion', ''); //审核意见
        if(!in_array($bookinginstatus,[4, 6])){
            COMMON::ApiJson('300', '参数错误-状态');
        }
        if ($bookinginstatus == 6 && $auditreason == '') {
            COMMON::ApiJson('300', '审核意见不能为空');
        }
        $B = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $detail = $B->getBookcarinById($id);
        if($detail['bookinginstatus'] != 4){
            COMMON::ApiJson('300', '该数据不是待审核状态');
        }
        $bookcarindetaildata = $B -> getBookcarindetailById($id);
        $bookcarincarsdata = $B -> getBookcarincarsById($id);
        if ($bookinginstatus == 6) {
            if ($auditreason == '') {
                COMMON::ApiJson('300', '审核意见不能为空');
                return;
            }
            $status = 7;
        } elseif ($bookinginstatus == 4) {
            $status = 5;
        }

        $input = array(
            'bookinginstatus' => $status,
            'auditreason' => $auditreason,
            'updated_at' => '=NOW()'
        );

        if ($B->auditBookcarin($id, $input,$bookcarindetaildata)) {

            if ($bookinginstatus == 4){
                //生成车入库订单
                $S = new StockcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $input = array(
                    'stockintype' => $detail['stockintype'],
                    'stockinno' => $detail['bookinginno'],
                    'customer_sysno' => $detail['customer_sysno'],
                    'customername' => $detail['customer_name'],
                    'booking_in_sysno' => $id,
                    'bookingin_no' => $detail['bookinginno'],
                    'contract_sysno' => $detail['contract_sysno'],
                    'contractno' => $detail['contract_no'],
                    'docsource'=> $detail['docsource'],
                    'cs_employee_sysno' => $detail['cs_employee_sysno'],
                    'cs_employeename' => $detail['cs_employeename'],
                    'takegoodsno' => $detail['takegoodsno'],
                    'isbusinesscheck' => $detail['isbusinesscheck'],
                    'businesschecktype' => $detail['businesschecktype'],
                    'businesscheckunitname' => $detail['businesscheckunitname'],
                    'stockinstatus' => 2,
                    'status' => 1,
                    'isdel' => 0,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()'
                );
                $res = $S->addStockcarin($input, $bookcarindetaildata, $bookcarincarsdata,3);
                if (!$res) {
                    COMMON::ApiJson('300', '生成入库订单失败');
                    return;
                }
            }

            COMMON::ApiJson('200', '审核成功');
        } else {
            COMMON::ApiJson('300', '审核失败');
        }
    }

    /**
     * 管入库预约审核
     * bookingPipelineReviewedAction
     */
    public function bookingPipelineReviewedAction(){
        self::setUserMessage();
        $id = $this -> request -> getParam('id', 0);
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $status = $this -> request ->getParam('status', '');   // 5审核通过  7 //审核不通过
        if(!in_array($status,[5, 7])){
            COMMON::ApiJson('300', '参数错误-状态');
        }
        $stockmarks = $this-> request ->getParam('auditopinion');   // 审核意见
        if ($status == 7 && $stockmarks == '') {
            COMMON::ApiJson('300', '审核意见不能为空');
        }

        $S = new BookpipelineinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $bookpipelineindetaildata = $S -> getBookingDetailList(['bookingin_sysno' => $id, 'page' => false] );

        $S = new BookpipelineinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $L = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'bookinginstatus' => $status,
            'flowmemo' => $stockmarks,
            'updated_at' => '=NOW()'
        );

        if($status==5){
            foreach($bookpipelineindetaildata['list'] as $key=>$value){
                //添加储罐和货物性质对应关系
                $tankId = $value['storagetank_sysno'];
                $goodsnature = $value['goodsnature'];
                $res = $L->tankTonature($tankId,$goodsnature);
                if($res['code']==300){
                    COMMON::ApiJson(300, $res['message']);
                    return;
                }
            }
        }
        $res = $S->AuditBookpipin($id, $input);
        if ($res['code']==200) {
            COMMON::ApiJson(200, '审核成功');
        } else {
            COMMON::ApiJson(300, $res['message']);
        }
    }

    /**
     * 管入库审核
     * bookingPipelineReviewedAction
     */
    public function pipelineReviewedAction(){
        self::setUserMessage();
        $id = $this -> request -> getParam('id', 0);
        if (!$id) {
            COMMON::ApiJson(300, '参数错误');
            return;
        }
        $stockmarks = $this -> request -> getParam('auditopinion', '');   //审核意见
        $status = $this -> request -> getParam('status', '');   //4审核通过 6 审核不通过
        if(!in_array($status, [4, 6])){
            COMMON::ApiJson('300', '参数错误-状态');
        }
        if ($status == 6 && !$stockmarks) {
            COMMON::ApiJson(300, '审核备注不能为空');
            return;
        }
        $P = new StockpipeinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $T = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $L = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $Sout = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $detail = $P-> getStockshipinById($id);
        $stockpipeindetaildata = $P ->getStockpipeindetailById($id);
        $bookingdata = [
            'sysno' => $detail['booking_in_sysno'],
            'isqualitycheck' => $detail['isqualitycheck'],
            'ispipelineorder' => $detail['ispipelineorder'],
            'isberthorder' => $detail['isberthorder'],
        ];
        $stockpipeinquality = $Sout -> getPBQ( $bookingdata, 5);

        //取出实际入库量
        $beqty = 0;
        foreach ($stockpipeindetaildata as $key => $value) {
            $beqty = $beqty + $value['beqty'];
            $storagetank_sysno = $value['storagetank_sysno'];
            $goods_sysno = $stockpipeindetaildata[$key]['goods_sysno'];
            $flag = $T->storagetankgoodsbyid($storagetank_sysno);
            if ($flag != $goods_sysno) {
                COMMON::ApiJson(300, '该储罐还有其他货品存量');
                return;
            }
            //添加储罐和货物性质对应关系
            if($status==4){
                $tankId = $value['storagetank_sysno'];
                $goodsnature = $value['goodsnature'];
                $res = $L->tankTonature($tankId,$goodsnature);
                if($res['code']==300){
                    COMMON::ApiJson(300, $res['message']);
                    return;
                }
            }
        }
        if($status ==4 ) {
            if($detail['isqualitycheck'] == 1 &&  empty($stockpipeinquality['qualitycheck'])){
                COMMON::ApiJson('300', '请完成品质检查才可入库!');
            }
            if($detail['isqualitycheck'] == 1 && !in_array($stockpipeinquality['qualitycheck'][0]['orderstatus'], [4, 7])){
                COMMON::ApiJson(300, '品质检查不合格');
            }

            if($detail['ispipelineorder'] == 1 && empty($stockpipeinquality['pipelineorder'])){
                COMMON::ApiJson(300, '请完成管线才可入库');
            }
            if($detail['isberthorder'] == 1 && empty($stockpipeinquality['berthorder'])){
                COMMON::ApiJson(300, '请完成泊位才可入库');
            }
        }

        $bookingin_no = $detail['bookingin_no'];
        $booking_in_sysno = $detail['booking_in_sysno'];
        $book = new BookshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $booklist = $book->getBookshipinById($booking_in_sysno);

        #var_dump($booklist['docsource']);exit();
        $input = array(
            'stockinstatus' => $status,
            'updated_at' => '=NOW()',
            'customer_sysno'=> $detail['customer_sysno'],
            'customername'=> $detail['customername'],
        );

//           print_r($input);
        //    print_r($stockpipeindetaildata);die;
        $flag = $P->updateStockpipein($id, $input, $stockpipeindetaildata, $stockmarks, $status, $beqty);
        if ($flag['code']==200) {
            if ($booklist['docsource'] == 2) {
                COMMON::editStockInStatus($bookingin_no, $beqty);
            }
//            $row = $P->getStockshipinById($id);
            COMMON::ApiJson(200, '审核成功');
        } else {
            COMMON::ApiJson(300, $flag['message']);
        }
    }

    /**
     * 车出库，船出库预约审核
     */
    public function stockShipReviewedAction()
    {
        self::setUserMessage(); //设置用户信息
        #权限验证
        $user = Yaf_Registry::get(SSN_VAR);
        $Pr = new PrivilegeModel(Yaf_Registry::get('db'), Yaf_Registry::get('mc'));
        if (!$Pr->check($this -> request -> getControllerName(), $this -> request -> getActionName(), $user )){
            COMMON::ApiJson('300', '您没有权限访问，请联系管理员。');
        }
        $id = $this -> request -> getParam('id',0);
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $examstep = $this -> request -> getParam('status',0); //审核状态 5审核通过 7审核不通过
        $exammarks = $this -> request -> getParam('auditopinion',''); //审核意见

        if($id == 0 || $examstep == 0 ){
            COMMON::ApiJson('300', '非法参数');
        }
        if ( $examstep == 7 && $exammarks=='') {
            COMMON::ApiJson('300', '请填写审核意见');
        }
        $L = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $B = new BookoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if($examstep == 7){
            $data = array(
                'bookingoutstatus' => 7,
                'auditreason' => $exammarks
            );
            $res  = $B->updateBookoutData($id,$data);

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

                $L->addDocLog($input);
                COMMON::ApiJson('200', '审核成功');
            }else{
                COMMON::ApiJson('300', '审核失败');
            }
        }elseif ($examstep == 5){
            $bookoutinfo = $B->getBookoutById($id);
            $msg= '';
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
                if ($bookoutinfo['docsource'] == '2') {
                    COMMON::editStockOutStatus($bookoutinfo['bookingoutno']);
                }
                $L->addDocLog($input);
                COMMON::ApiJson('200', '审核成功');
            }else{
                COMMON::ApiJson('300', $msg);
            }
        }
        COMMON::ApiJson('300', '审核失败');
    }

    /**
     *  船出库审核
     */
    public function stockShipOutReviewedAction()
    {
        self::setUserMessage(); //设置用户信息
        #权限验证
        $user = Yaf_Registry::get(SSN_VAR);
        $Pr = new PrivilegeModel(Yaf_Registry::get('db'), Yaf_Registry::get('mc'));
        if (!$Pr->check($this -> request -> getControllerName(), $this -> request -> getActionName(), $user )){
            COMMON::ApiJson('300', '您没有权限访问，请联系管理员。');
        }
        $id = $this -> request -> getParam('id', 0); //审核数据sysno
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $examstep = $this -> request -> getParam('status', 0); // 审核状态 6 审核不通过  4 审核通过
        $stockoutmarks = $this -> request -> getParam('auditopinion', ''); //审核意见
        if(!in_array($examstep,[4, 6])){
            COMMON::ApiJson('300', '参数错误-状态');
        }
        if ($examstep == 1 && $stockoutmarks == '') {
            COMMON::ApiJson('300', '审核意见不能为空');
        }
        $L = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $S = new StockoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $stockoutInfo = $S->getStockoutById($id);
        if($stockoutInfo['stockoutstatus'] != 3){
            COMMON::ApiJson('300', '该数据不是待审核状态');
        }
        if ($examstep == 6) {
            $data = array(
                'stockoutstatus' => 6,
                'auditreason' => $stockoutmarks,
            );
            #库存管理业务操作日志
            $res = $S->updateStockoutData($id, $data);
            if ($res) {
                $input = array(
                    'doc_sysno' => $id,
                    // 'doctype' => 8,
                    'opertype' => 5,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => $stockoutmarks
                );
                if($stockoutInfo){
                    if ($stockoutInfo['stockouttype'] == 1) {
                        $input['doctype'] = 5;
                    }else{
                        $input['doctype'] = 6;
                    }
                }
                $L->addDocLog($input);
                COMMON::ApiJson('200', '审核成功');
            } else {
                COMMON::ApiJson('300', '审核失败');
            }
        } elseif ($examstep == 4) {
            $msg = '';
            $res = $S->examStockout($id, $msg, $stockoutmarks);
            if ($res) {
                $input = array(
                    'doc_sysno' => $id,
                    // 'doctype' => 8,
                    'opertype' => 3,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => $stockoutmarks
                );
                if($stockoutInfo){
                    if ($stockoutInfo['stockouttype'] == 1) {
                        $input['doctype'] = 5;
                    }else{
                        $input['doctype'] = 6;
                    }
                }

                $bookout = $S->getBookoutDataBysysno($id);
                if ($bookout['docsource'] == 2) {
                    $stockoutqty = $S->getStockoutDetailBySysno($id);
                    COMMON::editStockOutStatusOk($bookout['bookingoutno'], floatval($stockoutqty[0]['beqty']));
                }
                $L->addDocLog($input);
                COMMON::ApiJson('200', '审核成功');
            } else {
                COMMON::ApiJson('300', $msg);
            }
        }elseif ($examstep == 5) {
            $msg = '';
            $res = $S->cancelStockout($id, $msg);
            if ($res) {
                $input = array(
                    'doc_sysno' => $id,
                    // 'doctype' => 8,
                    'opertype' => 4,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => $stockoutmarks
                );
                if($stockoutInfo){
                    if ($stockoutInfo['stockouttype'] == 1) {
                        $input['doctype'] = 5;
                    }else{
                        $input['doctype'] = 6;
                    }
                }
                $L->addDocLog($input);
                COMMON::ApiJson('200', '审核成功');
            } else {
                COMMON::ApiJson('300', $msg);
            }
        }
        COMMON::ApiJson('300', '审核失败');
    }

    //管出库预约审核
    public function bookingPipelineOutReviewedAction(){
        self::setUserMessage();
        $id = $this -> request -> getParam('id',0);
        $examstep = $this -> request -> getParam('status',0);  //5审核通过  7 审核不通过
        $exammarks = $this -> request -> getParam('auditopinion','');   //审核意见
        if(!$id || !$examstep){
            COMMON::ApiJson(300, '审核信息错误');
            return false;
        }
        if(!in_array($examstep,[5, 7])){
            COMMON::ApiJson('300', '参数错误-状态');
        }
        if ( $examstep == 7 && $exammarks=='') {
            COMMON::ApiJson(300,'请填写意见');
            return false;
        }
        $B = new BookoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $bookoutinfo = $B->getBookoutById($id);
        if (!$bookoutinfo) {
            COMMON::ApiJson(300,'预约单信息错误');
            return;
        }

        $L = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $user = Yaf_Registry::get(SSN_VAR);

        if($examstep == 7){
            $data = array(
                'bookingoutstatus' => 7,
                'auditreason' => $exammarks,
            );
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
                COMMON::ApiJson(200, '操作成功');
                return;
            }else{
                COMMON::ApiJson(300, '操作失败');
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

                COMMON::ApiJson(200, '操作成功');
                return;
            }else{
                COMMON::ApiJson(300, $msg);
                return;
            }
        }
    }

    //管出库审批界面
    public function pipelineOutReviewedAction(){
        self::setUserMessage();
        $id = $this -> request -> getParam('id', 0);
        $examstep = $this -> request -> getParam('status', 0);   //4审核通过  //6审核不通过
        $stockoutmarks = $this -> request -> getParam('auditopinion', '');    //审核意见
        if (!$id || !$examstep ) {
            COMMON::result(300, '缺少参数');
            return;
        }
        if(!in_array($examstep,[4, 6])){
            COMMON::ApiJson('300', '参数错误-状态');
        }
        $L = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $S = new StockoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $user = Yaf_Registry::get(SSN_VAR);
        $stockoutInfo = $S->getStockoutById($id);
        if(!$stockoutInfo){
            COMMON::result(300, '出库订单信息有误');
            return;
        }
        if ($examstep == 6) {
            $data = array(
                'stockoutstatus' => 6,
                'auditreason' => $stockoutmarks,
            );
            #库存管理业务操作日志
            $res = $S->updateStockoutData($id, $data);
            if ($res) {
                $input = array(
                    'doc_sysno' => $id,
                    'opertype' => 5,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => $stockoutmarks
                );
                if ($stockoutInfo['stockouttype'] == 1) {
                    $input['doctype'] = 5;
                }elseif($stockoutInfo['stockouttype'] == 3){
                    $input['doctype'] = 26;
                }
                $L->addDocLog($input);
                //更新消息提醒
                $S = new SystemModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
                $S->updateMessage($id);
                COMMON::result(200, '操作成功');
                return;
            } else {
                COMMON::result(300, '操作失败');
                return;
            }
        } elseif ($examstep == 4) {
            $msg = '';
            $res = $S->examStockout($id, $msg, $stockoutmarks);
            if ($res) {
                $input = array(
                    'doc_sysno' => $id,
                    // 'doctype' => 8,
                    'opertype' => 3,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => $stockoutmarks
                );
                if ($stockoutInfo['stockouttype'] == 1) {
                    $input['doctype'] = 5;
                }elseif ($stockoutInfo['stockouttype'] == 3){
                    $input['doctype'] = 26;
                }
                $bookout = $S->getBookoutDataBysysno($id);
                if ($bookout[0]['docsource'] == 2) {
                    $stockoutqty = $S->getStockoutDetailBySysno($id);
                    COMMON::editStockOutStatusOk($bookout[0]['bookingoutno'], floatval($stockoutqty[0]['beqty']));
                }
                $L->addDocLog($input);
                COMMON::result(200, '操作成功');
                return;
            } else {
                COMMON::result(300, $msg);
                return;
            }
        }
        COMMON::result(300, '操作失败');
        return;
    }

    /**
     * 货权转移审核
     */
    public function transReviewedAction()
    {
        self::setUserMessage();//设置用户信息
        #权限验证
        $user = Yaf_Registry::get(SSN_VAR);
        $Pr = new PrivilegeModel(Yaf_Registry::get('db'), Yaf_Registry::get('mc'));
        if (!$Pr->check($this -> request -> getControllerName(), $this -> request -> getActionName(), $user )){
            COMMON::ApiJson('300', '您没有权限访问，请联系管理员。');
        }
        $id = $this -> request -> getParam('id', '0'); //需要审核的合同id
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $stockmarks = $this -> request -> getParam('auditopinion', ''); //审核意见
        $status = $this -> request -> getParam('status', '0');  //审核状态 4审核通过  6审核不通过
        if(!in_array($status,[4, 6])){
            COMMON::ApiJson('300', '参数错误-状态');
        }
        $rejectreason = $this -> request -> getParam('rejectreason', ''); //驳回意见  （暂不需要）

        if($status == 6 && $stockmarks == ''){
            COMMON::ApiJson('300', '审核意见不能为空');
        }

        $stockTransInstance = new StocktransModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $costdata = $stockTransInstance -> getTransById($id);
        if($costdata['stocktransstatus'] !=3 ){
            COMMON::ApiJson('300', '该数据不是待审核状态');
        }
        $res = $stockTransInstance -> audit($id,$status,$stockmarks,$rejectreason,$costdata);

        if($res['statusCode']==200)
            COMMON::ApiJson('200', '审核成功：'.$res['msg']);
        else
            COMMON::ApiJson('300', '审核失败：'.$res['msg'] );
    }

    //倒罐审核
    public function retankReviewedAction(){
        self::setUserMessage();
        $id = $this -> request -> getParam('id', 0);
        if(!$id){
            COMMON::ApiJson(300, '参数错误');
            return;
        }
        $stockretankstatus = $this -> request -> getParam('status', '');   //5 审核通过  7审核不通过
        if(!$stockretankstatus){
            COMMON::ApiJson(300, '参数错误');
            return;
        }
        $auditreason = $this -> request -> getParam('auditopinion', '');     //审核意见
        if ($stockretankstatus == 7 && !$auditreason) {
            COMMON::ApiJson(300, '审核备注不能为空');
            return;
        }
        if(!in_array($stockretankstatus, [5, 7])){
            COMMON::ApiJson('300', '参数错误-状态');
        }
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $detail = $R ->getapplyretank($id);

        $retank = array(
            'bookingretankno' =>$detail['bookingretankno'],
            'bookingretankdate' => $detail['bookingretankdate'],
            'ispipelineorder' => $detail['ispipelineorder'],
            'stockretankstatus' => $stockretankstatus,
            'auditreason' => $auditreason,
            'updated_at' => '=NOW()'
        );
        $res = $R->auditapplyRetank($id,$retank,$stockretankstatus);
        if($res['statusCode']==200){
            COMMON::ApiJson(200, '审核成功');
        } else {
            COMMON::ApiJson(300, '审核失败：'.$res['msg'] );
        };
    }

    //让步审核
    public function qutailtyCheckReviewedAction(){
        self::setUserMessage();
        $id = $this -> request -> getParam('id',0);
        $examstep = $this -> request -> getParam('status',0);   //7 让步审核通过   5 审核不通过
        $auditreason = $this -> request -> getParam('auditopinion','');

        if(!$id || !$examstep){
            COMMON::result(300,'单据信息有误');
            return false;
        }
        if(!in_array($examstep,[5, 7])){
            COMMON::ApiJson('300', '参数错误-状态');
        }

        $Q = new QualitycheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $user = Yaf_Registry::get(SSN_VAR);
        $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $examdetailArr = $Q -> getQualitycheckdetail($id);

        $examdetail = $examdetailArr[0];

        $qualityInfo = $Q->getQualitycheckByid($id);
        if($examstep == 4){
            //审核通过
            if($examdetail['isskip'] != 1){
                $res = $Q->updateDetail($examdetail['sysno'],array('isskip' => $examdetail['isskip']));
                if(!$res){
                    COMMON::result(300,'操作失败');
                    return false;
                }

                $res = $Q->update($id,array('orderstatus' => 4, 'auditreason' => $auditreason));
                if(!$res){
                    COMMON::result(300,'操作失败');
                    return false;
                }
                //回写车入库磅码单
                if($examdetail['ischecked'] == 1 && $qualityInfo['businesstype'] == 3){
                    $res = $Q->updatePoundin($qualityInfo['stock_sysno'],array('quaulitycheck' => 1));
                    if(!$res){
                        COMMON::result(300,'操作失败');
                        return false;
                    }
                }else{
                    $res = $Q->updatePoundin($qualityInfo['stock_sysno'],array('quaulitycheck' => 3));
                    if(!$res){
                        COMMON::result(300,'操作失败');
                        return false;
                    }
                }

                //回写退货表
                if($examdetail['ischecked'] == 1 && $qualityInfo['businesstype'] == 10){
                    $res = $Q->updatePoundReback($qualityInfo['stock_sysno'],array('quaulitycheck' => 1));
                    if(!$res){
                        COMMON::result(300,'操作失败');
                        return false;
                    }
                }else{
                    $res = $Q->updatePoundReback($qualityInfo['stock_sysno'],array('quaulitycheck' => 3));
                    if(!$res){
                        COMMON::result(300,'操作失败');
                        return false;
                    }
                }

                $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 22,
                    'opertype' => 3,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => '审核通过',
                );

                $res = $S->addDocLog($input);

                if(!$res)
                {
                    COMMON::result(300,"添加业务操作日志失败");
                    return false;
                }
            }else{
                COMMON::result(300,"已经选择让步，请点击让步提交");
                return false;
            }

        }elseif($examstep == 5){
            //审核不通过
            $res = $Q->update($id,array('orderstatus' => 5 ,'auditreason' => $auditreason));
            if($res){
                if($qualityInfo['businesstype'] == 3){
                    $res = $Q->updatePoundin($qualityInfo['stock_sysno'],array('quaulitycheck' => 3));
                    if(!$res){
                        COMMON::result(300,'操作失败');
                        return false;
                    }
                }
                if($qualityInfo['businesstype'] == 10){
                    $res = $Q->updatePoundReback($qualityInfo['stock_sysno'],array('quaulitycheck' => 3));
                    if(!$res){
                        COMMON::result(300,'操作失败');
                        return false;
                    }
                }

                $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 22,
                    'opertype' => 4,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => $auditreason,
                );

                $res = $S->addDocLog($input);

                if(!$res)
                {
                    COMMON::result(300,"添加业务操作日志失败");
                    return false;
                }
            }else{
                COMMON::result(300,'操作失败');
                return false;
            }
        }elseif($examstep == 6){
            //让步提交
            if($examdetail['isskip'] == 1){
                $res = $Q->updateDetail($examdetail['sysno'],array('isskip' => $examdetail['isskip']));
                if(!$res){
                    COMMON:result(300,'操作失败');
                    return false;
                }
                $res = $Q->update($id,array('orderstatus' => 6));
                if(!$res){
                    COMMON::result(300,'操作失败');
                    return false;
                }
                $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 22,
                    'opertype' => 5,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => '让步待审核',
                );

                $res = $S->addDocLog($input);

                if(!$res)
                {
                    COMMON::result(300,"添加业务操作日志失败");
                    return false;
                }
            }
        }elseif($examstep == 7){
            //让步审核通过
            $res = $Q->update($id,array('orderstatus' => 7));
            if($res){
                //回写车入库磅码单
                if($qualityInfo['businesstype'] == 3){
                    $res = $Q->updatePoundin($qualityInfo['stock_sysno'],array('quaulitycheck' => 2));
                    if(!$res){
                        COMMON::result(300,'操作失败');
                        return false;
                    }
                }elseif($qualityInfo['businesstype'] == 10){
                    $res = $Q->updatePoundReback($qualityInfo['stock_sysno'],array('quaulitycheck' => 2));
                    if(!$res){
                        COMMON::result(300,'操作失败');
                        return false;
                    }
                }

                $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 22,
                    'opertype' => 6,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => '让步审核通过',
                );

                $res = $S->addDocLog($input);

                if(!$res)
                {
                    COMMON::result(300,"添加业务操作日志失败");
                    return false;
                }
            }else{
                COMMON::result(300,'操作失败');
                return false;
            }

        }elseif($examstep == 8){
            if($qualityInfo['orderstatus'] != 5){
                COMMON::result(300,'退回状态才可以终止');
                return false;
            }
            $res = $Q->update($id,array('orderstatus' => 8));
            if (!$res) {
                COMMON::result(300,'操作失败');
                return false;
            }
            //回写车入库磅码单
            if($qualityInfo['businesstype'] == 3){
                $res = $Q->updatePoundin($qualityInfo['stock_sysno'],array('quaulitycheck' => 3));
                if(!$res){
                    COMMON::result(300,'操作失败');
                    return false;
                }
            }

            //回写退货表
            if($qualityInfo['businesstype'] == 10){
                $res = $Q->updatePoundReback($qualityInfo['stock_sysno'],array('quaulitycheck' => 3));
                if(!$res){
                    COMMON::result(300,'操作失败');
                    return false;
                }
            }
        }
        echo json_encode(array('code' => 200 ,'msg' => '操作成功'));
    }

    //退货审核
    public function rebackReviewAction(){
        self::setUserMessage();
        $id = $this -> request -> getParam('id', 0);
        $status = $this -> request -> getParam('status', '');    //4 审核通过  6审核不通过
        $auditreason = $this -> request -> getParam('auditopinion', '');   //审核意见
        if(!$id){
            COMMON::ApiJson(300, '参数错误');
            return;
        }
        if(!in_array($status,[4, 6])){
            COMMON::ApiJson('300', '审核状态错误');
        }

        $R = new RebackModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'sysno' =>$id,
            'page'=>false,
        );
        $detaildata = $R->getRebackdetailById($search)['list'];

        if (count($detaildata) == 0) {
            COMMON::ApiJson(300, '退货明细不能为空');
            return;
        }
        foreach($detaildata as $key=>$value){
            if($value['rebacknumber']==''){
                COMMON::ApiJson(300, '退货数量不能为空');
            } else{
                if($value['rebacknumber']>$value['realnumber']){
                    COMMON::ApiJson(300, '退货数量不能大于提货数量');
                }
            }
        }
        //获取主表信息
        //$input  =  $R->getrebackInfoById($id);
        if ($status == 6) {
            if (!$auditreason) {
                COMMON::ApiJson(300, '审核备注不能为空');
                return;
            }
        }
        $updata = array(
            'updated_at' => '=NOW()'
        );
        $res = $R->auditReback1($id, $updata, $status, $auditreason);

        if ($res['code']==200) {
            COMMON::ApiJson(200, '审核成功');
        } else {
            COMMON::ApiJson(300, $res['message']);
        }
    }

    /**
     * 获取系统提示信息
     */
    public function getMessageAction()
    {
        self::setUserMessage(); //设置用户信息
        $pagesize = $this->request->getParam('pagesize', '10');
        $page = $this->request->getParam('page', '1');
        $viewstatus   = $this->request->getParam('viewstatus', 1); //1未读 ，2已读
        $user = Yaf_Registry::get(SSN_VAR);
        if(empty($user['user_sysno'])){
            COMMON::ApiJson('300','用户ID不能为空');
        }
        $M = new MessageModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $search = array(
            //'viewstatus' => $viewstatus,
            'cusomer_sysno' => $user['user_sysno'],
        );

        $messageData = $M->getMessageList($search); //获取信息
        if(isset($pagesize) && $page==false){
            $data = $messageData['list'];
        }else{
            $data['totalRow'] = count($messageData['list']);
            $data['pageCurrent'] = $page;
            $data['totalPage'] = ceil($data['totalRow'] / $pagesize);
            $list=array_chunk($messageData['list'], $pagesize, false);
            $data['list']= $list[$page-1] ? $list[$page-1] : [];
        }
        COMMON::ApiJson('200', '请求成功', self::responseResult($data));
    }

    /**
     * 更新消息状态
     */
    public function updateMessageAction()
    {
        self::setUserMessage(); //设置用户信息
        $id = $this->request->getParam('id',0);
        $M = new MessageModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        $viewstatus = $this->request->getParam('viewstatus',1);
        if($viewstatus == 2){
            $params['viewstatus'] = 2;
            // $params['readnum'] = $messageInfo['readnum'] + 1;
        }

        $isdel = $this->request->getParam('isdel',0);
        if($isdel == 1){
            $params['isdel'] = 1;
        }

        $res = $M->updateMessage($id,$params);

        if (!$res) {
            COMMON::ApiJson('300', "更新失败");
        }else{
            COMMON::ApiJson('200', '更新成功');

        }
    }

    /**
     * 合同类型 出入库方式转换
     * @param $arrKey 数组
     * @return string
     */
    private function  contractInotype($arrKey){
        $array = [
            1 =>'船进船出', 2 => '船进车出', 3 =>'车进车出', 4 =>'车进船出'
        ];
        $returnStr = '';
        foreach($arrKey  as $v){
            if(in_array($v, array_keys($array))) {
                $returnStr .= "、" . $array[$v];
            }
        }
        return trim($returnStr, '、');
    }

    /**
     * 获取合同详情  1 9
     */
    public function  getContractDetailAction()
    {
        self::setUserMessage(); //设置用户信息
        $id = $this->request->getParam('id', 0);
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $contractinfo = $contract->getContractById($id);
        if(!$contractinfo){
            COMMON::ApiJson('300', '未找到该数据');
        }
        $params['list'] = $contractinfo;
        $params['list']['inotypes'] =$params['list']['inotype'] ? self::contractInotype(explode(",", $params['list']['inotype'])) : [];
        $params['list']['inotype'] = explode(",", $params['list']['inotype']);
        $settlementInstance = new SettlementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $settlementDetail = $settlementInstance -> getSettlementById( $params['list']['settlement_sysno'] );
        $params['list']['settlementname'] = $settlementDetail['settlementname'];
        #获取附加商品信息
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['list']['goodslist'] = [];
        $goodslist =  $goods->getGoodsandpriceByid($id);
        foreach ($goodslist as $key => $v) {
            $v['lastlossrate'] = (string)($v['lastlossrate']*30);
            $params['list']['goodslist'][] = $v;
        }

        #获取附加杂费信息
        $othercost = new OthercostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['list']['othercostlist']     = $othercost->othercostcontractByid($id);

        $user = Yaf_Registry::get(SSN_VAR);
        $E = new EmployeeModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $employee = $E->getEmployeeById($user['employee_sysno']);
        $reviewdp = $contract->getContractReview($id);
        $restat = 1;
        foreach ($reviewdp as $value) {
            if ($value['department_sysno'] == $employee['department_sysno']) {
                $restat = $value['reviewstatus'];
                break;
            }
        }
        if ($restat == 2 || $restat == 3) {
            $department_sysno = '';
        }else{
            $department_sysno = $employee['department_sysno'];
        }
        $params['list']['reviewdetail'] = $contract -> getContract($id,$department_sysno);

        #获取附加图片
        $params['list']['attach'] = array();
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $attach = $A->getAttachByMAS('contract','addconattach',$id);
        $params['list']['attach'] = array_merge($params['list']['attach'],$attach);
        if( is_array($attach) && count($attach)){
            $files1 = array();
            foreach ($attach as $file){
                $files1[] = $file['sysno'];
            }
            $params['list']['uploaded']  =  join(',',$files1);
        }
        COMMON::ApiJson('200', '请求成功', self::responseResult($params));
    }

    /**
     * 船入库预约详情
     */
    public function getBookingShipInDetailAction()
    {
        self::setUserMessage(); //设置用户信息
        $id = $this->request->getParam('id', 0);
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $S = new BookshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['list'] = $S->getBookshipinById($id);
        if(!$params['list']){
            COMMON::ApiJson('300', '未找到该数据');
        }
        $params['bookingdetail'] = $S->getBookshipindetailById($id);
        $countQty = 0;
        foreach($params['bookingdetail'] as $value){
            $countQty += $value['bookinginqty'];
        }
        $params['list']['qty'] = sprintf('%.3F', $countQty);
        if ($id && !empty($params['bookingdetail'] )) {
            foreach ($params['bookingdetail']  as $key => $value) {
                $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $data = $quality->getQualityById($value['goods_quality_sysno']);
                $params['bookingdetail'][$key]['goods_quality_name'] = $data['qualityname'];
                $params['bookingdetail'][$key]['goods_sysno'] = $value['goods_sysno'];
                $params['bookingdetail'][$key]['goodsname'] = $value['goodsname'];
                $params['bookingdetail'][$key]['shipname'] = $value['shipname'];
            }
        }

        $params['attach'] = array();//图片信息 入库预约单
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $attach = $A->getAttachByMAS('bookshipin', 'booking', $id);
        $params['attach'] = array_merge($params['attach'], $attach);
        if (is_array($attach) && count($attach)) {
            $files1 = array();
            foreach ($attach as $file) {
                $files1[] = $file['sysno'];
            }
            $params['uploaded1'] = join(',', $files1);
        }
        COMMON::ApiJson('200', '请求成功',self::responseResult($params));
    }

    /**
     * 船入库详情
     */
    public function stockShipInDetailAction()
    {
        self::setUserMessage(); //设置用户信息
        $id = $this->request->getParam('id', 0);
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $S = new StockshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['list'] = $S->getStockshipinById($id);
        if(!$params['list']){
            COMMON::ApiJson('300', '未找到该数据');
        }
        //查询入库明细
        #预约带过来
        $search = array(
            'stockin_sysno' => $params['list']['sysno'],
            'status' => 1,
            'page' => false
        );
//        $detaillist = $S->getStockshipindetailById($id);
        $detailData = $S->getStockshipinDetailList($search);
        //连接明细字段与计算入库总量
        $qty = 0;
        for ($i = 0; $i < count($detailData['list']); $i++) {
            $params['list']['detailgoodsname'] = $detailData['list'][$i]['goodsname'];
            if ($detailData['list'][$i]['shipname'] == $detailData['list'][$i + 1]['shipname']) {
                $params['list']['detailshipname'] = '';
            } else {
                $params['list']['detailshipname'] .= $detailData['list'][$i]['shipname'] . ' ';
            }
            if ($detailData[$i]['storagetankname'] == $detailData['list'][$i + 1]['storagetankname']) {
                $params['list']['detailstoragebankname'] = $detailData['list'][$i]['storagetankname'];
            } else {
                $params['list']['detailstoragebankname'] .= $detailData['list'][$i]['storagetankname'] . ' ';
            }
            $qty += $detailData['list'][$i]['beqty'];
        }

        $params['list']['beqty'] = sprintf("%.3f", $qty);

        $params['list']['detaillist'] = $detailData['list'];
        $params['list']['attach'] = array();

        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $attach = $A->getAttachByMAS('stockshipin', 'uploading', $id);
        $params['list']['attach'] = array_merge($params['list']['attach'], $attach);
        if (is_array($attach) && count($attach)) {
            $files1 = array();
            foreach ($attach as $file) {
                $files1[] = $file['sysno'];
            }

            $params['list']['uploaded1'] = join(',', $files1);
        }

        $release_no = $A->getAttachByMAS('stockshipin', 'declare_release', $id);
        $params['list']['release_no'] = $release_no;
        if (is_array($release_no) && count($release_no)) {
            $files2 = array();
            foreach ($release_no as $file) {
                $files2[] = $file['sysno'];
            }

            $params['list']['uploaded2'] = join(',', $files2);
        }

        if ($params['list']['booking_in_sysno']) {
            $B = new BookingModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
            $booking = $B->getBookingInById($params['list']['booking_in_sysno']);
            $params['list']['customer_sysno'] = $booking['customer_sysno'];
            $params['list']['customername'] = $booking['customer_name'];
            $params['list']['booking_in_sysno'] = $booking['sysno'];
            $params['list']['bookingin_no'] = $booking['bookinginno'];
            $params['list']['contract_sysno'] = $booking['contract_sysno'];
            $params['list']['contractno'] = $booking['contract_no'];
            $params['list']['cs_employee_sysno'] = $booking['cs_employee_sysno'];
            $params['list']['cs_employeename'] = $booking['cs_employeename'];
            $params['list']['memo'] = $detailData['list'][0]['memo'];
        }
        $stockOutInstance = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $detail = $stockOutInstance -> getPBQ($booking, 2);
        foreach ($detail['qualitycheck'] as $k => $v){
            $detail['qualitycheck'][$k]['isskip']  = is_numeric($v['isskip']) ? $v['isskip'] : '0';
        }
        $params = array_merge($params, $detail);
//        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
//        $search = array(
//            'bar_status' => '1',
//            'bar_isdel' => '0',
//            'page' => false,
//        );
//        $list = $C->searchCustomer($search);
//        $params['customerlist'] = $list['list'];
//        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
//        $search = array(
//            'bar_status' => '1',
//            'bar_isdel' => '0',
//            'page' => false,
//        );
//        $list = $E->searchEmployee($search);
//        $params['employeelist'] = $list['list'];
//
//        $W = new WharfModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
//        $wharflist = $W->searchWharf(array('page'=>false,'bar_status'=>1,'bar_isdel'=>0));
//        $params['wharflist'] = $wharflist['list'];
//        $Company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
//        $companyname = $Company->getDefault();
//        $params['companyname'] = $companyname['companyname'];
        COMMON::ApiJson('200', '请求成功', self::responseResult($params));
    }

    /**
     * 车入库预约详情 3
     */
    public function getBookingCarInDetailAction()
    {
        self::setUserMessage(); //设置用户信息
        $id = $this->request->getParam('id', 0);
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $S = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['list'] = $S->getBookcarinInfoById($id);
        if(!$params['list']){
            COMMON::ApiJson('300', '未找到该数据');
        }
        $BS = new BookshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['bookingdetail'] = $BS->getBookshipindetailById($id);
        $countQty = 0 ;
        foreach($params['bookingdetail'] as $val){
            $countQty += $val['bookinginqty'];
        }
        $params['list']['qty'] = sprintf('%.3F', $countQty);
        if ($id && !empty($params['bookingdetail'] )) {
            foreach ($params['bookingdetail']  as $key => $value) {
                $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $data = $quality->getQualityById($value['goods_quality_sysno']);
                $params['bookingdetail'][$key]['goods_quality_name'] = $data['qualityname'];
                $params['list']['goods_sysno'] = $value['goods_sysno'];
                $params['list']['goodsname'] = $value['goodsname'];
                $params['list']['shipname'] = $value['shipname'];
            }
        }

        $params['carInDetail'] = $S -> getBookcarincarsById($id);
        $params['attach'] = array();
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $attach = $A->getAttachByMAS('bookcarin', 'bookcarinatt', $id);
        $params['attach'] = array_merge($params['attach'], $attach);
        if (is_array($attach) && count($attach)) {
            $files1 = array();
            foreach ($attach as $file) {
                $files1[] = $file['sysno'];
            }
            $params['uploaded1'] = join(',', $files1);
        }
        COMMON::ApiJson('200', '请求成功',self::responseResult($params));
    }

    //管入库预约详情
    public function getBookingPipelineInAction(){
        self::setUserMessage(); //设置用户信息
        $id = $this->request->getParam('id', 0);
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $bookPipelineInstance = new BookpipelineinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['list'] = $bookPipelineInstance -> getBookshipinById($id);

        if(!$params){
            COMMON::ApiJson('300', '未找到数据');
        }
        $params['list']['attach'] = [];
        $params['list']['uploaded'] = '';
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $attach = $A->getAttachByMAS('bookspipelinein', 'booking', $id);
        $params['list']['attach'] = array_merge($params['list']['attach'], $attach ? $attach : []);
        if (is_array($attach) && count($attach)) {
            $files1 = array();
            foreach ($attach as $file) {
                $files1[] = $file['sysno'];
            }

            $params['list']['uploaded'] = join(',', $files1);
        }
        $bookshipinInstance = new BookshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params['list']['detail'] = $bookshipinInstance -> getBookshipindetailById($id);
        $countQty = 0;
        foreach ($params['list']['detail'] as $value){
            $countQty += $value['bookinginqty'];
        }
        $params['list']['qty'] = sprintf('%.3F', $countQty);
        COMMON::ApiJson('200', '请求成功', self::responseResult($params));
    }

    //管入库详情
    public function getPipelineInAction(){
        self::setUserMessage(); //设置用户信息
        $id = $this->request->getParam('id', 0);
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $stockPipeInstance = new StockpipeinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['list'] = $stockPipeInstance -> getStockshipinById($id);
        $params['list']['attach'] = array();
        $params['list']['uploaded'] = '';
        $sysno = $id;
        $attachInstace = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $attach = $attachInstace -> getAttachByMAS('bookspipelinein', 'booking', $sysno);
        $params['list']['attach'] = array_merge($params['list']['attach'], $attach);
        if (is_array($attach) && count($attach)) {
            $files1 = array();
            foreach ($attach as $file) {
                $files1[] = $file['sysno'];
            }
            $params['list']['uploaded'] = join(',', $files1);
        }
        $booking['sysno'] = $params['list']['booking_in_sysno'];
        $booking['ispipelineorder'] = $params['list']['ispipelineorder'];
        $booking['isqualitycheck'] = $params['list']['isqualitycheck'];
        $booking['isberthorder'] = $params['list']['isberthorder'];
        $stockOutInstance = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $detail = $stockOutInstance -> getPBQ($booking , 5);
        foreach ($detail['qualitycheck'] as $k => $v){
            $detail['qualitycheck'][$k]['isskip']  = is_numeric($v['isskip']) ? $v['isskip'] : '0';
        }
        $params['list'] = array_merge($params['list'], $detail);
        $search = [
            'stockin_sysno' => $id,
            'status' => 1,
            'page' => false
        ];
        $detailData = $stockPipeInstance -> getStockpipeinDetailList($search);
        $params['list']['detail'] = $detailData['list'] ? $detailData['list'] : [];
        $countQty = 0;
        foreach ($detailData['list'] as $value){
            $countQty += $value['beqty'];
        }
        $params['list']['qty'] = sprintf('%.3F', $countQty);
        COMMON::ApiJson('200', '请求成功', self::responseResult($params));
    }

    /**
     * 船出库预约详情 4
     */
    public function bookingShipOutDetailAction()
    {
        self::setUserMessage(); //设置用户信息
        $id = $this->request->getParam('id', 0);
        if(!isset($id)) {
            COMMON::ApiJson('300', '参数错误');
        }
        $B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $params = $B->getBookoutById($id);
        if(!$params){
            COMMON::ApiJson('300', '未找到该数据');
        }
        $search = array(
            'bookingout_sysno' => $params['sysno'],
            'page' => false
        );
        if($params['bookingoutstatus'] != 6){
            $receiveend = strtotime($params['receiveend']);
            $now = strtotime(date('Y-m-d',time()));
            if ($now > $receiveend) {
                $params['receiveover'] = '是';
            }else{
                $params['receiveover'] = '否';
            }
        }else{
            $params['receiveover'] = $params['receiveover'] == 1 ? '是' : '否';
        }
        $S = new StockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $ST = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $detailData =  $B->getBookoutDetailList($search);
        $countQty = 0 ;
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
            $countQty += $value['bookingoutqty'];
        }
        $params['detaillist'] = $detailData['list'];
        $params['qty'] = sprintf('%.3F', $countQty);
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $ship_uploader = $A->getAttachByMAS('bookout','ship_uploader',$id); //提货单图片
        if( is_array($ship_uploader) && count($ship_uploader)){
            $files = array();
            foreach ($ship_uploader as $file){
                $files[] = $file['sysno'];
            }
            $params['uploaded1']  =  join(',',$files);
        }

        $params['attach']  = $ship_uploader;
        //提货单样张
        $params['samples']  =  $A->getAttachByMAS('customer','customerlading',$params['customer_sysno']);
//        $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
//        $E = new EmployeeModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
//        $search = array(
//            'bar_status' => '1',
//            'bar_isdel' => '0',
//            'page' => false,
//        );
//        $list = $C->searchCustomer($search);
//        $params['customerlist'] =  $list['list'];
//        $list = $E->searchEmployee($search);
//        $params['employeelist'] =  $list['list'];

        COMMON::ApiJson('200', '请求成功', self::responseResult($params));
    }

    /**
     * 船出库详情 6
     */
    public function stockShipOutDetailAction()
    {
        self::setUserMessage(); //设置用户信息
        $id = $this->request->getParam('id', 0);
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $BO = new BookoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $stock = new StockModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $params = $S->getStockoutById($id);
        if(!$params){
            COMMON::ApiJson('300', '未找到该数据');
        }
        $search = array(
            'stockout_sysno' => $params['sysno'],
            'status' => 1,
            'page' => false
        );
        $bookoutinfo = $BO->getBookoutById($params['booking_out_sysno']);
        if (count($bookoutinfo) > 0) {
            $params['takegoodsno'] = $bookoutinfo['receivenumber'];
            $params['takegoodscompany'] = $bookoutinfo['receiveunitname'];
            $params['takebetween'] = $bookoutinfo['receivebetween'];
        }
        $bookoutDetailData = $BO->getBookDetail(array('bookingout_sysno' => $params['booking_out_sysno'],'page' => false));
        $detailData = $S->getStockoutDetailList($search);
        $countQty = 0 ;
        foreach ($detailData['list'] as $key => $value) {
            if($value['stocktype'] == 1){
                $stockinfo = $stock->getElementById(array($value['stock_sysno']));
                if(!$stockinfo){
                    COMMON::ApiJson('300','库存记录不存在');
                    return false;
                }
                $detailData['list'][$key]['instockqty'] = $stockinfo[0]['instockqty'];
                $detailData['list'][$key]['introduceqty'] = '--';
            }elseif ($value['stocktype'] == 2) {
                $introduceDetailInfo = $I->getIntroduceDetailById(intval($value['stock_sysno']));
                $detailData['list'][$key]['instockqty'] = '--';
                $detailData['list'][$key]['introduceqty'] = $introduceDetailInfo['takegoodsnum'];
            }
            $detailData['list'][$key]['unitname'] = '吨';
            $res = $S->getStockinShipname($value['stockin_sysno']);
            if ($res) {
                $detailData['list'][$key]['stockinshipname'] = $res;
            }

            $countQty += $value['beqty'];
        }
        $params['qty'] = sprintf('%.3F', $countQty);
        $params['detaillist'] = $detailData['list'];
        $params['attach'] = [];
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $attach = $A->getAttachByMAS('stockout', 'receipt', $id);
        $params['attach'] = array_merge($params['attach'], $attach);
        if (is_array($attach) && count($attach)) {
            $files = array();
            foreach ($attach as $file) {
                $files[] = $file['sysno'];
            }
            $params['uploaded'] = join(',', $files);
        }
        $booking['sysno'] = $params['booking_out_sysno'];
        $booking['ispipelineorder'] = $params['ispipelineorder'];
        $booking['isberthorder'] = $params['isberthorder'];
        $booking['isqualitycheck'] = $params['isqualitycheck'];
        $pbqData = $S->getPBQ($booking, 7);
        foreach ($pbqData['qualitycheck'] as $k => $v){
            $pbqData['qualitycheck'][$k]['isskip']  = is_numeric($v['isskip']) ? $v['isskip'] : '0';
        }
        $params = array_merge($params, $pbqData);
        $takegoods = $A->getAttachByMAS('stockout', 'takegoods', $id);
        $ship_uploader = $A->getAttachByMAS('bookout', 'ship_uploader', $params['booking_out_sysno']);
        $params['takegoods'] = $takegoods;
        if($ship_uploader){
            $params['takegoods'] = array_merge($params['takegoods'], $ship_uploader);
        }
        if (is_array($takegoods) && count($takegoods)) {
            $files = array();
            foreach ($takegoods as $file) {
                $files[] = $file['sysno'];
            }
            $params['uploaded1'] = join(',', $files);
        }
        $params['attach'] = $attach;
//        $C = new CustomerModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
//        $search = array(
//            'bar_status' => '1',
//            'bar_isdel' => '0',
//            'page' => false,
//        );
//        $list = $C->searchCustomer($search);
//        $params['customerlist'] = $list['list'];
//        $E = new EmployeeModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
//        $search = array(
//            'bar_status' => '1',
//            'bar_isdel' => '0',
//            'page' => false,
//        );
//        $list = $E->searchEmployee($search);
//        $params['employeelist'] = $list['list'];
//        //码头
//        $W = new WharfModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
//        $wharflist = $W->searchWharf(array('page'=>false,'bar_status'=>1));
//        $params['wharflist'] = $wharflist['list'];
//        $params['id'] = $id;
//        $params['status'] = COMMON::getStockOutStatus($params['stockoutstatus']);
//        $Company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
//        $companyname = $Company->getDefault();
//        $params['companyname'] = $companyname['companyname'];
        COMMON::ApiJson('200', '请求成功', self::responseResult($params));
    }

    /**
     * 车出库预约详情 5
     */
    public function bookingCarOutDetailAction()
    {
        self::setUserMessage(); //设置用户信息
        $id = $this->request->getParam('id', 0);
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $params = $B->getBookoutById($id);
        if(!$params){
            COMMON::ApiJson('300', '未找到该数据');
        }
        if($params['bookingoutstatus'] != 6){
            $receiveend = strtotime($params['receiveend']);
            $now = strtotime(date('Y-m-d',time()));
            if ($now > $receiveend) {
                $params['receiveover'] = '是';
            }else{
                $params['receiveover'] = '否';
            }
        }else{
            $params['receiveover'] = $params['receiveover'] == 1 ? '是' : '否';
        }
        $search = array(
            'bookingout_sysno' =>	$params['sysno'],
            'page' => false
        );
        $S = new StockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $ST = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $detailData =  $B->getBookoutDetailList($search);
        $countQty = 0 ;
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

            $countQty += $value['bookingoutqty'];
        }
        $params['qty'] = sprintf('%.3F', $countQty);
        $params['detaillist'] = $detailData['list'] ;
        $carData =  $B->searchBookoutCar($search);
        $params['carlist'] = $carData['list'] ;
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

//        $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
//        $E = new EmployeeModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
//        //有效合同客户列表
//        $search = array(
//            'bar_status' => '1',
//            'bar_isdel' => '0',
//            'page' => false,
//        );
//        $list = $C->searchCustomer($search);
//        $params['customerlist'] =  $list['list'];
//        $list = $E->searchEmployee($search);
//        $params['employeelist'] =  $list['list'];
        COMMON::ApiJson('200', '请求成功', self::responseResult($params));
    }

    //管出库预约详情
    public function getBookingPipelineOutAction(){
        self::setUserMessage(); //设置用户信息
        $id = $this->request->getParam('id', 0);
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $bookOutInstance = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $S = new StockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $ST = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $params = $bookOutInstance -> getBookoutById($id);
        if(!$params){
            COMMON::ApiJson('300', '为找到数据');
        }
        $search = array(
            'bookingout_sysno' =>	$params['sysno'],
            'page' => false
        );
        $detailData =  $bookOutInstance -> getBookoutDetailList($search);
        $attachInstance = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $countQty = 0;
        foreach ($detailData['list'] as $key => $value) {
            $countQty  += $value['bookingoutqty'];
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
        $params['detail'] = $detailData['list'];
        $params['qty'] = sprintf('%.3F', $countQty);
        $attach = $attachInstance -> getAttachByMAS('bookpipeline','pipeline',$id);
        if( is_array($attach) && count($attach)){
            $files = array();
            foreach ($attach as $file){
                $files[] = $file['sysno'];
            }
            $params['uploaded']  =  join(',',$files);
        }
        $params['attach']  = $attach;
        COMMON::ApiJson('200', '请求成功', self::responseResult($params));
    }

    //管出库详情
    public function getPipelineOutAction(){
        self::setUserMessage(); //设置用户信息
        $id = $this->request->getParam('id', 0);
        if(!$id){
            COMMON::ApiJson('300', '参数错误');
        }
        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $B = new BookingModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $stock = new StockModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $I = new IntroduceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params = $S->getStockoutById($id);
        if(!$params){
            COMMON::ApiJson('300', '为找到数据');
        }
        $booking = $B->getBookingOutById($params['booking_out_sysno']);

        $pbqData = $S->getPBQ($booking, 9);
        foreach ($pbqData['qualitycheck'] as $k => $v){
            $pbqData['qualitycheck'][$k]['isskip']  = is_numeric($v['isskip']) ? $v['isskip'] : '0';
        }
        $params = array_merge($params, $pbqData);
        $search = array(
            'stockout_sysno' => $params['sysno'],
            'status' => 1,
            'page' => false
        );

        $detailData = $S->getStockoutDetailList($search);
        $countQty = 0;
        foreach ($detailData['list'] as $key => $value) {
            $countQty += $value['beqty'];
            if($value['stocktype'] == 1){
                $stockinfo = $stock->getElementById(array($value['stock_sysno']));
                if(!$stockinfo){
                    COMMON::ApiJson(300,'库存记录不存在');
                    return false;
                }
                $detailData['list'][$key]['instockqty'] = $stockinfo[0]['instockqty'];
                $detailData['list'][$key]['introduceqty'] = '--';
            }elseif ($value['stocktype'] == 2) {
                $introduceDetailInfo = $I->getIntroduceDetailById(intval($value['stock_sysno']));
                $detailData['list'][$key]['instockqty'] = '--';
                $detailData['list'][$key]['introduceqty'] = $introduceDetailInfo['takegoodsnum'];
            }
        }
        $params['detail'] = $detailData['list'];
        $params['qty'] = sprintf('%.3F', $countQty);
        $params['attach'] = [];
        $attach = $A->getAttachByMAS('pipelineout', 'pipeline', $id);
        $bookingAttach = $A->getAttachByMAS('bookpipeline', 'pipeline', $params['booking_out_sysno']);
        if (is_array($attach) && count($attach)) {
            $files = array();
            foreach ($attach as $file) {
                $files[] = $file['sysno'];
            }
            $params['uploaded'] = join(',', $files);
        }
        $params['attach'] = array_merge($params['attach'], $bookingAttach, $attach);
        COMMON::ApiJson('200', '请求成功', self::responseResult($params));
    }

    /**
     * 货转详情 7
     */
    public function stockTransDetailAction()
    {
        self::setUserMessage(); //设置用户信息
        $id = $this->request->getParam('id', 0);
        if (!$id) {
            COMMON::ApiJson('300', '参数错误');
        }
        $stockTransInstance = new StocktransModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $stockTransInstance->getTransById($id);
        if (!$params) {
            COMMON::ApiJson('300', '未找到该数据');
        }
        //得到原货主
        $params['parentShipper'] = $stockTransInstance->getparentshipper($id);

        $params['detail'] = $stockTransInstance->getListDetials(array('stocktrans_sysno' => $id));
        $countQty = 0;
        foreach ($params['detail'] as $val) {
            $countQty += $val['transqty'];
        }
        $params['qty'] = sprintf('%.3F', $countQty);
        //添加附件的显示
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $attach = $A->getAttachByMAS('stocktrans', 'attach-1', $id);
        if (is_array($attach) && count($attach)) {
            $files1 = array();
            foreach ($attach as $file) {
                $files1[] = $file['sysno'];
            }

            $params['upload'] = join(',', $files1);
        }
        $params['attach'] = $attach;
        $params['samples'] = $A->getAttachByMAS('customer', 'customerlading', $params['sale_customer_sysno']);
        if (is_array($params['samples']) && count($params['samples'])) {
            $files1 = array();
            foreach ($params['samples'] as $file) {
                $files1[] = $file['sysno'];
            }
            $params['upload1'] = join(',', $files1);
        }
        if ($params['type'] == 'edit' && ($params['stocktransstatus'] != 2 && $params['stocktransstatus'] != 6)) {
            COMMON::ApiJson('300', '请选择暂存的数据');
            return false;
        }

//        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
//        if($params['cost_contract_type']==1){
//            $search = ['bar_id'=>$params['sale_customer_sysno'],'bar_status' => 1,'bar_isdel' => 0,'page' => false,];
//        }else{
//            $search = ['bar_id'=>$params['buy_customer_sysno'],'bar_status' => 1,'bar_isdel' => 0,'page' => false,];
//        }
//        //合同列表
//        $contractList = $C->searchCustomercontractlist($search);
//        $params['contractlist'] = $contractList['list'];
//        //客户列表
//        $list = $C->searchCustomer(['page' => false,'bar_status'=>1]);
//        $params['customerlist'] =  $list['list'];
//        //有效合同客户列表
//        $search = array(
//            'contractenddate'=>'NOW()',
//            'page' =>false,
//            'bar_status'=>1,
//            'bar_isdel' => 0,
//
//        );
//        $list = $C->searchCustomerContract($search);
//        $params['customerlistContract'] =  $list;
//        $Company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
//        $companyname  = $Company->getDefault();
//        $params['companyname']  = $companyname['companyname'];
        COMMON::ApiJson('200', '请求成功', self::responseResult($params));
    }

    //获取倒罐详情
    public function getRetankDetailAction(){
        self::setUserMessage(); //设置用户信息
        $id = $this->request->getParam('id', 0);
        if (!$id) {
            COMMON::ApiJson('300', '参数错误');
        }
        $R = new RetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $params = $R->getapplyretank($id);
        if(!$params){
            COMMON::ApiJson('300', '为找到数据');
        }
        // 获取 倒罐信息的详情信息单
        $list = $R->getapplyretankdetailById($id);
        $params['count_num'] = 0;
        foreach ($list as $key=>$value){
            $params['count_num'] += $value['bookingretankqty'];
            $params['customername']=$value['customername'];
            $params['stockretank_out_no']=$value['stockretank_out_no'];
            $params['stockretank_in_no']=$value['stockretank_in_no'];
            $params['bookingretankqty']=$value['bookingretankqty'];
            $params['goodsname']=$value['goodsname'];
        }
        $params['detail'] = $list;
        $params['attach'] = [];
        //获取附件
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $attach = $A->getAttachByMAS('retank','retank-edit',$id);
        if( is_array($attach) && count($attach)){
            $files = array();
            foreach ($attach as $file){
                $files[] = $file['sysno'];
            }
            $params['upload1']  =  join(',', $files);
        }
        $params['attach'] = array_merge($params['attach'] , $attach);
        COMMON::ApiJson('200', '请求成功', self::responseResult($params));
    }

    //获取让步审核详情
    public function getQuatilyCheckDetailAction(){
        self::setUserMessage(); //设置用户信息
        $id = $this->request->getParam('id', 0);
        if (!$id) {
            COMMON::ApiJson('300', '参数错误');
        }
        $Q = new QualitycheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params = $Q->getQualitycheckByid($id);
        if(!$params){
            COMMON::ApiJson('300', '为找到数据');
        }

        $businesstype = $params['businesstype'];
        if($businesstype==4)
        {
            $params = $Q->getQualitycheckForpendcarin($id);
        }
        $params['businesstype'] = self::quatilyCheckType($businesstype);
        $params['businesstypenum'] = $businesstype;
        $detail = $Q -> getQualitycheckdetail($id);
        $params['attach'] = [];
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        foreach ($detail as $k=>$v){
            $attach1 = $A->getAttachByMAS('qualitycheck','qualitycheck-edit',$v['sysno']);
            if( is_array($attach1) && count($attach1)){
                $files1 = array();
                foreach ($attach1 as $file){
                    $files1[] = $file['sysno'];
                }
                $attach =  join(',',$files1);
            }
            $params['attach'] = array_merge($params['attach'], $attach1);
        }
        $params['detail'] = $detail ? $detail : [];
        COMMON::ApiJson('200', '请求成功', self::responseResult($params));
    }

    public function quatilyCheckType($key){
        $type = array(
            1=>'船入库预约',
            2=>'船入库订单',
            3=>'车入库磅码单',
            4=>'管入库预约',
            5=>'管入库订单',
            6=>'船出库预约',
            7=>'船出库订单',
            8=>'管出库预约',
            9=>'管出库订单',
            10=>'退货',
        );
        if($key){
            return $type[$key];
        }
        return $type;
    }

    //获取退货详情
    public function getRebackDetailAction(){
        self::setUserMessage(); //设置用户信息
        $id = $this->request->getParam('id', 0);
        if (!$id) {
            COMMON::ApiJson('300', '参数错误');
        }
        $R = new RebackModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params = $R->getrebackInfoById($id);
        if(!$params){
            COMMON::ApiJson('300', '为找到数据');
        }
        $search = array(
            'sysno'=>$id,
            'page'=>false,
        );

        $detail = $R->getRebackdetailById($search);
        $params['count_num'] = 0;
        foreach ($detail['list'] as $value){
            $params['count_num'] += $value['realnumber'];
        }
        $params['detail'] = $detail['list'] ? $detail['list'] : [];
        $params['attach'] = [];
        COMMON::ApiJson('200', '请求成功', self::responseResult($params));
    }

    /**
     *  合同评审审核
     */
    public function contractReviewAction()
    {
        self::setUserMessage();//设置用户信息
        #权限验证
        $user = Yaf_Registry::get(SSN_VAR);
        $Pr = new PrivilegeModel(Yaf_Registry::get('db'), Yaf_Registry::get('mc'));
        if (!$Pr->check($this -> request -> getControllerName(), $this -> request -> getActionName(), $user )){
            COMMON::ApiJson('300', '您没有权限访问，请联系管理员。');
        }
        $cid = $this -> request -> getParam('id', '0'); //需要审核的合同id
        if(!$cid){
            COMMON::ApiJson('300', '参数错误');
        }
        $status = $this -> request -> getParam('status', '2');  //审核状态 2审核通过  3审核不通过
        if(!in_array($status,[2, 3])){
            COMMON::ApiJson('300', '参数错误-状态');
        }
        $reviewmemo = $this -> request -> getParam('reviewmemo', ''); //审核意见
        if($status == 3 && $reviewmemo == ''){
            COMMON::ApiJson('300', '审核意见不能为空');
        }
        $user = Yaf_Registry::get(SSN_VAR);
        $E = new EmployeeModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $employee = $E->getEmployeeById($user['employee_sysno']);
        $C = new ContractModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $reviewdp = $C->getContractReview($cid);
        $flag = false;
        $restat = 1;
        foreach ($reviewdp as $value) {
            if ($value['department_sysno'] == $employee['department_sysno']) {
                $rvid = $value['sysno'];
                $restat = $value['reviewstatus'];
                $flag = true;
                break;
            }
        }
        if ($restat == 2 || $restat == 3) {
            COMMON::ApiJson('300', '您所属的“' . $employee['departmentname'] . '”部门已评审过，请不要重复评审。');
            exit;
        }
        if ($flag) {
            $reviewdata = array(
                'reviewstatus' => $status,
                'reviewemployee_id' => $user['employee_sysno'],
                'reviewemployeename' => $user['employeename'],
                'reviewmemo' => $reviewmemo,
                'reviewdate' => '=NOW()',
                'updated_at' => '=NOW()'
            );
            $res = $C->updateContractReview($reviewdata, $rvid, $cid);
            if ($res) {
                COMMON::ApiJson('200', '审核成功');
                exit;
            } else {
                COMMON::ApiJson('300', '审核失败');
                exit;
            }
        } else {
            COMMON::ApiJson('300', '您所属的“' . $employee['departmentname'] . '”部门不在汇签名单之内，不能参与评审。');
            exit;
        }
    }

    /**
     * 二维数组排序
     * @param $array 需要排序的数组
     * @param $key 需要排序的下标
     * @return array  返回整理后的数据
     */
    private static function sort_arr($array, $key){
        if(is_array($array)){
            $sort = array();
            foreach ($array as $value) {
                $sort[] = $value[$key];
            }
            array_multisort($sort, SORT_DESC, $array);
        }
        return $array;
    }

    /**
     * 获取用户信息
     * @return bool || messagecode
     */
    private function setUserMessage()
    {
        $user['user_sysno'] = $this -> request -> getParam('user_sysno', 0); //用户ID
        $user['sysno'] = $user['user_sysno'];
        $user['employee_sysno'] = $this -> request -> getParam('employee_sysno', 0); //用户角色ID
        $user['employeename'] = $this -> request -> getParam('employeename', 0); //用户名称
        foreach($user as $key => $value){
            if(!$value){
                COMMON::ApiJson('301', '用户参数错误-'.$key);
            }
        }
        Yaf_Registry::set(SSN_VAR, $user);
        return true;
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
}
<?php

/**
 * Created by PhpStorm.
 * User: wuxianneng
 * Date: 2016/11/24 0024
 * Time: 13:29
 */
class ContractController extends Yaf_Controller_Abstract
{
    /**
     * IndexController::init()
     *
     * @return void
     */
    public function init()
    {
        #parent::init();
    }

    /**
     * 整租合同时显示后台整租页面框架及菜单
     * 编辑各种合同时显示后台零租页面框架及菜单
     */
    public function listAction()
    {
        $user = Yaf_Registry::get(SSN_VAR);
        $arr = array(
            'status' => 1
        );
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        $zuguantype = $request->getParam('zuguantype', 0);
        $mode = $request->getParam('mode', '');
        $settlement = new SettlementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $employee = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $customer = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $data = $settlement->getSettlement($arr);

        $params['settlementlist'] = $data['list'];

        $search = array(
            'bar_status' => 1,
            'bar_isdel' => '0',
            'page' => false,
        );
        //客服列表
        $params['csemployee'] = $employee->searchEmployee($search);
        //业务员列表
        $params['saleemployee'] = $employee->searchEmployee($search);

        if ($id) {                  //如果存在id则传递数据进入编辑页面
            $contractinfo = $contract->getContractById($id);

            $copycontract = $request->getParam('copycontract', 0);
            $params['addattach'] = $request->getParam('addattach', '');


            if($copycontract=="1"){
                $contractstatus = 1;
                $contractinfo['contractstatus']=1;
                $contractinfo['contractnodisplay']='';
                $contractinfo['contractstartdate']='';
                $contractinfo['contractenddate']='';
            }else{
                $contractstatus = $contractinfo['contractstatus'];
            }

            if ($zuguantype==1||$zuguantype==2) {       //进入零租页面
                $params['list'] = $contractinfo;
                $inotype = $params['list']['inotype'];
                $inotype = explode(',', $inotype);
                $params['inotype'] = $inotype;
                $params['id'] = $id;

                if($contractstatus==1){
                    $params['action'] = '/contract/addcontract2';
                }elseif($contractstatus==2||$contractstatus==6){
                    $params['action'] = '/contract/editcontract2';
                }
                if($mode =='addattach'){
                    $params['action'] = '/contract/addcontractattach';
                }elseif($mode =='audit'){
                    $params['action'] = '/contract/examecontract2';
                }elseif($mode =='back'){
                    $params['action'] = '/contract/abolishcontract2';
                }

                $params['attach'] = array();

                $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
                $sysno = $id;
                $attach = $A->getAttachByMAS('contract','addconattach',$sysno);
                $params['attach'] = array_merge($params['attach'],$attach);

                if( is_array($attach) && count($attach)){
                    $files1 = array();
                    foreach ($attach as $file){
                        $files1[] = $file['sysno'];
                    }

                    $params['uploaded']  =  join(',',$files1);
                }

                $params['mode'] = $mode;
                $params['employee_sysno'] = $user['employee_sysno'];

                $this->getView()->make('contract.nopackagetank.contractlist', $params);

            } else if ($zuguantype==3||$zuguantype==4) {                                  //进入整租页面
                $params['list'] = $contractinfo;
                $inotype = $params['list']['inotype'];
                $inotype = explode(',', $inotype);
                $params['inotype'] = $inotype;
                $params['userlist'] = $customer->getCustomerById($params['list']['customer_id']);
                $params['id'] = $id;

                if($contractstatus==1){
                    $params['action'] = '/contract/addcontract';
                }elseif($contractstatus==2||$contractstatus==6){
                    $params['action'] = '/contract/editcontract';
                }
                if($mode =='addattach'){
                    $params['attid'] = $id;
                    $params['action'] = '/contract/addcontractattach';
                }elseif($mode =='audit'){
                    $params['action'] = '/contract/examecontract';
                }elseif($mode =='back'){
                    $params['action'] = '/contract/abolishcontract';
                }
                /**
                 * @title 循环解析数组内容
                 */
                $arr = explode(',', $params['list']['inotype']);
                for ($i = 0; $i < 4; $i++) {
                    $params['inotype'][$i] = 0;
                    foreach ($arr as $v) {
                        if ($v > 0) {
                            $params['inotype'][$v - 1] = $v;
                        }
                    }
                }

                $params['attach'] = array();
                $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

                $attach = $A->getAttachByMAS('contract','addconattach',$id);
                $params['attach'] = array_merge($params['attach'],$attach);

                if( is_array($attach) && count($attach)){
                    $files1 = array();
                    foreach ($attach as $file){
                        $files1[] = $file['sysno'];
                    }
                    $params['uploaded']  =  join(',',$files1);
                }

                $params['mode'] = $mode;
                $params['employee_sysno'] = $user['employee_sysno'];

                $this->getView()->make('contract.packagetank.contractlist', $params);
            }elseif($zuguantype==5){                                        //进入靠泊装卸合同
                $params['list'] = $contractinfo;
                $inotype = $params['list']['inotype'];
                $inotype = explode(',', $inotype);
                $params['inotype'] = $inotype;
                $params['userlist'] = $customer->getCustomerById($params['list']['customer_id']);
                $params['id'] = $id;

                if($contractstatus==1){
                    $params['action'] = '/contract/addcontract3';
                }elseif($contractstatus==2||$contractstatus==6){
                    $params['action'] = '/contract/editcontract3';
                }
                if($mode =='addattach'){
                    $params['action'] = '/contract/addcontractattach';
                }elseif($mode =='audit'){
                    $params['action'] = '/contract/examecontract3';
                }elseif($mode =='back'){
                    $params['action'] = '/contract/abolishcontract3';
                }
                /**
                 * @title 循环解析数组内容
                 */
                $arr = explode(',', $params['list']['inotype']);
                for ($i = 0; $i < 4; $i++) {
                    $params['inotype'][$i] = 0;
                    foreach ($arr as $v) {
                        if ($v > 0) {
                            $params['inotype'][$v - 1] = $v;
                        }
                    }
                }

                $params['attach'] = array();
                $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

                $attach = $A->getAttachByMAS('contract','addconattach',$id);
                $params['attach'] = array_merge($params['attach'],$attach);

                if( is_array($attach) && count($attach)){
                    $files1 = array();
                    foreach ($attach as $file){
                        $files1[] = $file['sysno'];
                    }
                    $params['uploaded']  =  join(',',$files1);
                }

                $params['mode'] = $mode;
                $params['employee_sysno'] = $user['employee_sysno'];

                $this->getView()->make('contract.berthaload.contractlist', $params);
            }

        } else {
            $params['id'] = $id;
            $params['list']['contractstatus'] = 1;
            $params['action'] = '/contract/addcontract';
            /**
             * @title 循环解析数组内容
             */
            $arr = explode(',', $params['list']['inotype']);
            for ($i = 0; $i < 4; $i++) {
                $params['inotype'][$i] = 0;
                foreach ($arr as $v) {
                    if ($v > 0) {
                        $params['inotype'][$v - 1] = $v;
                    }
                }
            }
            $params['employee_sysno'] = $user['employee_sysno'];

            $this->getView()->make('contract.packagetank.contractlist', $params);
        }
    }

    /**
     * 新建零租合同时显示后台零租页面框架及菜单
     */
    public function list2Action()
    {
        $user = Yaf_Registry::get(SSN_VAR);
        $arr = array(
            'status' => 1
        );
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        $params['id'] = $id;
        //付款方式
        $settlement = new SettlementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $data = $settlement->getSettlement($arr);
        $params['settlementlist'] = $data['list'];

        $employee = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        //客服列表
        $params['csemployee'] = $employee->searchEmployee($search);
        //业务员列表
        $params['saleemployee'] = $employee->searchEmployee($search);
        $params['action'] = '/contract/addcontract2';
        $params['list']['contractstatus'] = 1;
        $params['employee_sysno'] = $user['employee_sysno'];

        $this->getView()->make('contract.nopackagetank.contractlist', $params);
    }

    /**
     * 新建零租合同时显示后台零租页面框架及菜单
     */
    public function list3Action()
    {
        $user = Yaf_Registry::get(SSN_VAR);
        $arr = array(
            'status' => 1
        );
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        $params['id'] = $id;
        //付款方式
        $settlement = new SettlementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $data = $settlement->getSettlement($arr);
        $params['settlementlist'] = $data['list'];

        $employee = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        //客服列表
        $params['csemployee'] = $employee->searchEmployee($search);
        //业务员列表
        $params['saleemployee'] = $employee->searchEmployee($search);
        $params['action'] = '/contract/addcontract3';
        $params['list']['contractstatus'] = 1;
        $params['employee_sysno'] = $user['employee_sysno'];

        $this->getView()->make('contract.berthaload.contractlist', $params);
    }

    /**
     * 整租合同合约明细新增编辑视图
     */
    public function goodsaddoreditAction()
    {
        $request = $this->getRequest();

        $arr = array(
            'bar_status' => 1,
            'page'=>false
        );
        $id = $request->getParam('id', 0);
        $zuguantype = $request->getParam('zuguantype', 0);

        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $storage = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $unit = new UnitModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['id'] = $id;
        $data = $quality->getList($arr);
        $params['qualitylist'] = $data['list'];
        $params['companylist'] = $company->searchCompany($arr);
        $params['storagelist'] = $storage->searchStoragetank($arr);
        $params['unitlist'] = $unit->getUnit();

        $params['zuguantype'] = $zuguantype;

        $this->getView()->make('contract.packagetank.goodsedit', $params);
    }

    /**
     * 合约明细视图数据
     */
    public function goodsdatailAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if ($id) {
            $list = $goods->getGoodsandpriceByid($id);
            for($i=0;$i<count($list);$i++){
                if(!$list[$i]['goodsnature']){
                    $list[$i]['goodsnature'] = '';
                }
                $list[$i]['lastlossrate'] = $list[$i]['lastlossrate']*30;
            }
            echo json_encode($list);
        } else {
            echo json_encode(array());
        }
    }

    /**
     *零租合同合约明细新增编辑视图
     */
    public function goodsaddoredit2Action()
    {
        $request = $this->getRequest();
        $arr = array(
            'bar_status' => 1,
            'page'=>false
        );
        $id = $request->getParam('id', 0);
        $zuguantype = $request->getParam('zuguantype', 0);
        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $unit = new UnitModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['id'] = $id;
        $data = $quality->getList($arr);
        $params['qualitylist'] = $data['list'];
        $params['unitlist'] = $unit->getUnit();
        $params['companylist'] = $company->searchCompany($arr);

        $params['zuguantype'] = $zuguantype;

        $this->getView()->make('contract.nopackagetank.goodsedit', $params);
    }

    /*
     * 编辑合同明细
     */
     public function contractdetaileditAction(){
         $request = $this->getRequest();
         $params = $request->getPost('selectedDatasArray',array());
         $params['handlestatus'] = $request->getParam('handlestatus','0');
         $zuguantype = $request->getParam('zuguantype','0');
         $contracttype = $request->getParam('contracttype','');
         $params['zuguantype'] = $zuguantype;

         $arr = array(
             'bar_status' => 1,
             'page'=>false
         );

         $company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
         $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
         $data = $quality->getList($arr);
         $params['qualitylist'] = $data['list'];
         $params['companylist'] = $company->searchCompany($arr);

         if($zuguantype=='1'||$zuguantype=='2'){
             $this->getView()->make('contract.nopackagetank.goodsedit',$params);
         }else if($contracttype==5)
         {
             $isladder = $request->getPost();
             $params['isladder'] = $isladder['selectedDatasArray']['isladder'];
             $this->getView()->make('contract.berthaload.goodsedit',$params);
         }
         else{
             $this->getView()->make('contract.packagetank.goodsedit',$params);
         }
     }


    /**
     *靠泊装卸合同合约明细新增编辑视图
     */
    public function goodsaddoredit3Action()
    {
        $params = array();
        $this->getView()->make('contract.berthaload.goodsedit', $params);
    }

    /**
     * 其他费用新增编辑页面
     */
    public function othercostaddoreditAction()
    {
        $request = $this->getRequest();
        $arr = array();
        $id = $request->getParam('id', 0);
        $othercost = new OthercostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if (!$id) {
            $action = "/contract/addothercost/";
            $params = array();
        } else {
            $action = "/contract/editothercost";
        }
        $params['id'] = $id;
        $params['action'] = $action;
        $data = $othercost->getOthercost($arr);
        $params['orthercostlist'] = $data['list'];
        $arr = array(
            'bar_status' => 1,
            'page' => false
        );
        $params['companylist'] = $company->searchCompany($arr);

        $this->getView()->make('contract.packagetank.othercostedit', $params);
    }

    /**
     * 查询现有其它费用数据
     */
    public function othercostdatailAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        $othercost = new OthercostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if ($id) {
            $list = $othercost->othercostcontractByid($id);
            echo json_encode($list);
        } else {
            echo json_encode(array());
        }
    }

    /**
     * 整租租合同保存与提交
     */
    public function addcontractAction()
    {
        $request = $this->getRequest();

        $goodsdetaildata = $request->getPost('goodsdetaildata');
        $othercostdetaildata = $request->getPost('othercostdetaildata');
        $goodsdetaildata = json_decode($goodsdetaildata, true);
        $othercostdetaildata = json_decode($othercostdetaildata, true);
        $contractstatus = $request->getPost('contractstatus', '');
        if (count($goodsdetaildata) == 0) {
            COMMON::result(300, '品名及费用不能为空！');
            return;
        }

        if(count($goodsdetaildata)>=2&&$request->getPost('contracttype','')==4)
            for($i=0;$i<count($goodsdetaildata);$i++){
                $count = 0;
                for($j=$i+1;$j<count($goodsdetaildata);$j++){
                    if($goodsdetaildata[$i]['goodsname']==$goodsdetaildata[$j]['goodsname']){
                        $count++;
                    }
                }
                if($count>=1){
                    COMMON::result(300, '货品名称不能重复！');
                    return;
                }
            }

        /*------------------验证关联表数据------------------------*/
        $employee = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $saleemployeename = $employee->getEmployeeById($request->getPost('obj_business_user_sysno'));
        $csemployeename = $employee->getEmployeeById($request->getPost('csemployee_sysno'));

        $isbrother = $request->getPost('customerrelation', '');
        if ($isbrother == '是') {
            $isbrother = 1;
        } elseif ($isbrother == '否') {
            $isbrother = 0;
        }

        $list = array(
            'contractno' => COMMON::getCodeId('S'),
            'contractnodisplay' => $request->getPost('contractnodisplay', ''),
            'contractdate' => $request->getPost('contractdate', ''),
            'customer_id' => $request->getPost('obj_customer_id', ''),
            'customername' => $request->getPost('obj_customername', ''),
            'settlement_sysno' => $request->getPost('settlement_sysno', ''),
            'contracttype' => $request->getPost('contracttype',''),
            'isbrother' => $isbrother,
            'shipproxy' => $request->getPost('shipproxy', ''),
            'inotype' => $request->getPost('inotype', ''),
            'testrequire' => $request->getPost('testrequire', ''),
            'testrequirebusiness' => $request->getPost('testrequirebusiness', ''),
            'saleemployee_sysno' => $request->getPost('obj_business_user_sysno', ''),
            'saleemployeename' => $saleemployeename['employeename'],
            'csemployee_sysno' => $request->getPost('csemployee_sysno', ''),
            'csemployeename' => $csemployeename['employeename'],
            'contractstartdate' => $request->getPost('contractstartdate', ''),
            'contractenddate' => $request->getPost('contractenddate', ''),
            'contractcostdate' => $request->getPost('contractcostdate', ''),
            'isseal' => $request->getPost('isseal', ''),
            'contractmemo' => $request->getPost('contractmemo', ''),
            'contractstatus' => $contractstatus,
            'isdel' => $request->getPost('isdel', '0'), //逻辑删除：1是0否
            'status' => $request->getPost('status', '1'),
            'version' => $request->getPost('version', '1'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
            'invoiceday' => $request->getPost('invoiceday', ''),
            'settlementtype' => $request->getPost('settlementtype', ''),
            'costtype' => $request->getPost('costtype', ''),
            'instockdate' => $request->getPost('instockdate', '')
        );

        $contractisexist = $contract->contractisexist($list['contractnodisplay']);

        if(count($contractisexist)!=0){
            COMMON::result(300, '合同编号不能重复！');
            return;
        }

        $S = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        foreach ($goodsdetaildata as $item) {
            if ($item['storagetank_sysno'] ) {
                $storagetank_sysno = $item['storagetank_sysno'];
                $goods_sysno = $item['goods_sysno'];
                $isthisgoods = $S->getStoragetankById($storagetank_sysno);

                if ($isthisgoods['goods_sysno'] != $goods_sysno) {
                    COMMON::result(300, '储罐存放的货品与你要放的货品不一致！');
                    return;
                }
            }
        }

        if (!empty($list['inotype'])) {
            $list['inotype'] = implode(',', $list['inotype']);
        }
        /********************************查询合同评审配置表****************************************/
        $configreviewdata = $contract->getconfigreview(1);
        /**************************************END*********************************************/

        $id = $contract->addContract($list, $contractstatus, $goodsdetaildata, $othercostdetaildata, $configreviewdata);
        if ($id) {
            $row = $contract->getContractById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    /**
     * 零租合同保存与提交
     */
    public function addcontract2Action()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);

        $contractstatus = $request->getPost('contractstatus', '');//判断传递的状态值
        $goodsdetaildata = $request->getPost('goodsdetaildata','');
        $othercostdetaildata = $request->getPost('othercostdetaildata','');
        $goodsdetaildata = json_decode($goodsdetaildata, true);
        $othercostdetaildata = json_decode($othercostdetaildata, true);
        $isbrother = $request->getPost('customerrelation', '');
        if ($isbrother == '是') {
            $isbrother = 1;
        } elseif ($isbrother == '否') {
            $isbrother = 0;
        }

        if (count($goodsdetaildata) == 0) {
            COMMON::result(300, '合约明细不能为空！');
            return;
        }

        if(count($goodsdetaildata)>=2)
            for($i=0;$i<count($goodsdetaildata);$i++){
                $count = 0;
                if($count>=1){
                    COMMON::result(300, '货品名称不能重复！');
                    return;
                }
            }

        /*------------------验证关联表数据------------------------*/
        $employee = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $saleemployeename = $employee->getEmployeeById($request->getPost('obj_business_user_sysno'));
        $csemployeename = $employee->getEmployeeById($request->getPost('csemployee_sysno'));

        $list = array(
            'contractno' => COMMON::getCodeId('S'),
            'contractnodisplay' => $request->getPost('contractnodisplay', ''),
            'contractdate' => $request->getPost('contractdate', ''),
            'customer_id' => $request->getPost('obj_customer_id', ''),
            'customername' => $request->getPost('obj_customername', ''),
            'settlement_sysno' => $request->getPost('settlement_sysno', ''),
            'contracttype' => $request->getPost('contracttype', ''),
            'isbrother' => $isbrother,
            'shipproxy' => $request->getPost('shipproxy', ''),
            'inotype' => $request->getPost('inotype', ''),
            'testrequire' => $request->getPost('testrequire', ''),
            'testrequirebusiness' => $request->getPost('testrequirebusiness', ''),
            'saleemployee_sysno' => $request->getPost('obj_business_user_sysno', ''),
            'saleemployeename' => $saleemployeename['employeename'],
            'csemployee_sysno' => $request->getPost('csemployee_sysno', ''),
            'csemployeename' => $csemployeename['employeename'],
            'contractstartdate' => $request->getPost('contractstartdate', ''),
            'contractenddate' => $request->getPost('contractenddate',''),
            'isseal' => $request->getPost('isseal', ''),
            'contractmemo' => $request->getPost('contractmemo', ''),
            'contractstatus' => $contractstatus,
            'isdel' => $request->getPost('isdel', '0'), //逻辑删除：1是0否
            'status' => $request->getPost('status', '1'),
            'version' => $request->getPost('version', '1'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
            'invoiceday' => $request->getPost('invoiceday', ''),
            'settlementtype' => $request->getPost('settlementtype', '')
        );

        $list['contractenddate'] = $list['contractenddate']?$list['contractenddate']:date('Y-m-d',strtotime('+100 year',strtotime($list['contractstartdate'])));

        $contractisexist = $contract->contractisexist($list['contractnodisplay']);

        if(count($contractisexist)!=0){
            COMMON::result(300, '合同编号不能重复！');
            return;
        }

        if (!empty($list['inotype'])) {
            $list['inotype'] = implode(',', $list['inotype']);
        }

        /********************************查询合同评审配置表****************************************/
        $configreviewdata = $contract->getconfigreview(1);
        /**************************************END*********************************************/
        $id = $contract->addContract($list, $contractstatus, $goodsdetaildata, $othercostdetaildata, $configreviewdata);
        if ($id) {
            $row = $contract->getContractById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    /**
     * 靠泊装卸合同保存与提交
     */
    public function addcontract3Action()
    {
        $request = $this->getRequest();
        $goodsdetaildata = $request->getPost('goodsdetaildata');
        $goodsdetaildata = json_decode($goodsdetaildata, true);
        $contractstatus = $request->getPost('contractstatus', '');
        if (count($goodsdetaildata) == 0) {
            COMMON::result(300, '品名及费用不能为空！');
            return;
        }

//        if(count($goodsdetaildata)>=2)
//            for($i=0;$i<count($goodsdetaildata);$i++){
//                $count = 0;
//                for($j=$i+1;$j<count($goodsdetaildata);$j++){
//                    if($goodsdetaildata[$i]['goodsname']==$goodsdetaildata[$j]['goodsname']
//                        &&$goodsdetaildata[$i]['berthcosttype']==$goodsdetaildata[$j]['berthcosttype']){
//                        $count++;
//                    }
//                }
//                if($count>=1){
//                    COMMON::result(300, '货品名称不能重复！');
//                    return;
//                }
//            }

        /*------------------验证关联表数据------------------------*/
        $employee = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $saleemployeename = $employee->getEmployeeById($request->getPost('obj_business_user_sysno'));

        $csemployeename = $employee->getEmployeeById($request->getPost('csemployee_sysno'));

        $isbrother = $request->getPost('customerrelation', '');
        if ($isbrother == '是') {
            $isbrother = 1;
        } elseif ($isbrother == '否') {
            $isbrother = 0;
        }

        $list = array(
            'contractno' => COMMON::getCodeId('S'),
            'contractnodisplay' => $request->getPost('contractnodisplay', ''),
            'contractdate' => $request->getPost('contractdate', ''),
            'customer_id' => $request->getPost('obj_customer_id', ''),
            'customername' => $request->getPost('obj_customername', ''),
            'settlement_sysno' => $request->getPost('settlement_sysno', ''),
            'contracttype' => 5,
            'isbrother' => $isbrother,
            'shipproxy' => $request->getPost('shipproxy', ''),
            'inotype' => $request->getPost('inotype', ''),
            'testrequire' => $request->getPost('testrequire', ''),
            'testrequirebusiness' => $request->getPost('testrequirebusiness', ''),
            'saleemployee_sysno' => $request->getPost('obj_business_user_sysno', ''),
            'saleemployeename' => $saleemployeename['employeename'],
            'csemployee_sysno' => $request->getPost('csemployee_sysno', ''),
            'csemployeename' => $csemployeename['employeename'],
            'contractstartdate' => $request->getPost('contractstartdate', ''),
            'contractenddate' => $request->getPost('contractenddate', ''),
            'isseal' => $request->getPost('isseal', ''),
            'contractmemo' => $request->getPost('contractmemo', ''),
            'contractstatus' => $contractstatus,
            'isdel' => $request->getPost('isdel', '0'), //逻辑删除：1是0否
            'status' => $request->getPost('status', '1'),
            'version' => $request->getPost('version', '1'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
            'invoiceday' => $request->getPost('invoiceday', ''),
            'settlementtype' => $request->getPost('settlementtype', ''),
            'costtype' => $request->getPost('costtype', ''),
            'instockdate' => $request->getPost('instockdate', '')
        );

        $contractisexist = $contract->contractisexist($list['contractnodisplay']);

        if(count($contractisexist)!=0){
            COMMON::result(300, '合同编号不能重复！');
            return;
        }

        if (!empty($list['inotype'])) {
            $list['inotype'] = implode(',', $list['inotype']);
        }
        /********************************查询合同评审配置表****************************************/
        $configreviewdata = $contract->getconfigreview(2);
        /**************************************END*********************************************/
        $othercostdetaildata = array();
        $id = $contract->addContract($list, $contractstatus, $goodsdetaildata, $othercostdetaildata, $configreviewdata);
        if ($id) {
            $row = $contract->getContractById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    /**
     * 编辑整租合同
     */
    public function editcontractAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id');
        $isbrother = $request->getPost('customerrelation', '');
        if ($isbrother == '是') {
            $isbrother = 1;
        } elseif ($isbrother == '否') {
            $isbrother = 0;
        }
        $contractstatus = $request->getPost('contractstatus', '');//判断传递的状态值

        $goodsdetaildata = $request->getPost('goodsdetaildata');
        $othercostdetaildata = $request->getPost('othercostdetaildata');
        $goodsdetaildata = json_decode($goodsdetaildata, true);
        $othercostdetaildata = json_decode($othercostdetaildata, true);
        if (count($goodsdetaildata) == 0) {
            COMMON::result(300, '品名及费用不能为空！');
            return;
        }

        if(count($goodsdetaildata)>=2&&$request->getPost('contracttype','')==4)
            for($i=0;$i<count($goodsdetaildata);$i++){
                $count = 0;
                for($j=$i+1;$j<count($goodsdetaildata);$j++){
                    if($goodsdetaildata[$i]['goodsname']==$goodsdetaildata[$j]['goodsname']){
                        $count++;
                    }
                }
                if($count>=1){
                    COMMON::result(300, '货品名称不能重复！');
                    return;
                }
            }

        $employee = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $saleemployeename = $employee->getEmployeeById($request->getPost('obj_business_user_sysno'));
        $csemployeename = $employee->getEmployeeById($request->getPost('csemployee_sysno'));

        $list = array(
            'contractnodisplay' => $request->getPost('contractnodisplay', ''),
            'contractdate' => $request->getPost('contractdate', ''),
            'customer_id' => $request->getPost('obj_customer_id', ''),
            'customername' => $request->getPost('obj_customername', ''),
            'settlement_sysno' => $request->getPost('settlement_sysno', ''),
            'contracttype' => $request->getPost('contracttype',''),
            'isbrother' => $isbrother,
            'shipproxy' => $request->getPost('shipproxy', ''),
            'inotype' => $request->getPost('inotype', ''),
            'testrequire' => $request->getPost('testrequire', ''),
            'testrequirebusiness' => $request->getPost('testrequirebusiness', ''),
            'saleemployee_sysno' => $request->getPost('obj_business_user_sysno', ''),
            'saleemployeename' => $saleemployeename['employeename'],
            'csemployee_sysno' => $request->getPost('csemployee_sysno', ''),
            'csemployeename' => $csemployeename['employeename'],
            'contractstartdate' => $request->getPost('contractstartdate', ''),
            'contractenddate' => $request->getPost('contractenddate', ''),
            'contractcostdate' => $request->getPost('contractcostdate', ''),
            'isseal' => $request->getPost('isseal', ''),
            'contractmemo' => $request->getPost('contractmemo', ''),
            'contractstatus' => $contractstatus,
            'isdel' => $request->getPost('isdel', '0'), //逻辑删除：1是0否
            'status' => $request->getPost('status', '1'),
            'version' => $request->getPost('version', '1'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
            'invoiceday' => $request->getPost('invoiceday', ''),
            'settlementtype' => $request->getPost('settlementtype', ''),
            'costtype' => $request->getPost('costtype', ''),
            'instockdate' => $request->getPost('instockdate', '')
        );

        $olddisplayno = $contract->getContractById($id);

        if($olddisplayno['contractnodisplay']!=$list['contractnodisplay']){
            $contractisexist = $contract->contractisexist($list['contractnodisplay']);

            if(count($contractisexist)!=0){
                COMMON::result(300, '合同编号不能重复！');
                return;
            }
        }

        $S = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        foreach ($goodsdetaildata as $item) {
            if ($item['storagetank_sysno']) {
                $storagetank_sysno = $item['storagetank_sysno'];
                $goods_sysno = $item['goods_sysno'];
                $isthisgoods = $S->getStoragetankById($storagetank_sysno);

                if ($isthisgoods['goods_sysno'] != $goods_sysno) {
                    COMMON::result(300, '储罐存放的货品与你要放的货品不一致！');
                    return;
                }
            }
        }

        if (!empty($list['inotype'])) {
            $list['inotype'] = implode(',', $list['inotype']);
        }

        /********************************查询合同评审配置表****************************************/
        $configreviewdata = $contract->getconfigreview(1);
        /**************************************END*********************************************/
        $id = $contract->updatecontract($id, $list, $contractstatus, $goodsdetaildata, $othercostdetaildata, $configreviewdata);
        if ($id) {
            $row = $contract->getContractById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    /**
     * 审核整租合同
     */
    public function examecontractAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id');

        $contractstatus = $request->getPost('contractstatus', '');//判断传递的状态值

        if ($contractstatus == 3 || $contractstatus == 4) {
            $goodsdetaildata = $request->getPost('goodsdetaildata');
            $goodsdetaildata = json_decode($goodsdetaildata, true);
            $contractstatus = $request->getPost('contractstatus', '');//判断传递的状态值
            $auditopinion = $request->getPost('auditopinion', '');//不审核意见

            $list = array(
                'customer_id' => $request->getPost('obj_customer_id', ''),
                'contractnodisplay' => $request->getPost('contractnodisplay', ''),
                'customername' => $request->getPost('obj_customername', ''),
                'contracttype'=>$request->getPost('contracttype', ''),
                'contractstartdate' => $request->getPost('contractstartdate', ''),
                'contractenddate' => $request->getPost('contractenddate', ''),
                'contractcostdate' => $request->getPost('contractcostdate', ''),
                'costtype' => $request->getPost('costtype', ''),
                'updated_at'	=> 	'=NOW()'
            );
            $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $res = $contract->examecontract($id,$list,$goodsdetaildata,$contractstatus,$auditopinion);
            if ($res) {
                $row = $contract->getContractById($id);
                COMMON::result(200, '审核成功', $row);
            } else {
                COMMON::result(300, '审核失败');
            }
        }else{
            COMMON::result(300, '不是审核操作');
        }
    }

    /**
     * 编辑零租合同
     */
    public function editcontract2Action()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id');
        $isbrother = $request->getPost('customerrelation', '');
        if ($isbrother == '是') {
            $isbrother = 1;
        } elseif ($isbrother == '否') {
            $isbrother = 0;
        }
        $contractstatus = $request->getPost('contractstatus', '');//判断传递的状态值

        $goodsdetaildata = $request->getPost('goodsdetaildata');
        $othercostdetaildata = $request->getPost('othercostdetaildata');
        $goodsdetaildata = json_decode($goodsdetaildata, true);
        $othercostdetaildata = json_decode($othercostdetaildata, true);
        if (count($goodsdetaildata) == 0) {
            COMMON::result(300, '品名及费用不能为空！');
            return;
        }

        if(count($goodsdetaildata)>=2)
            for($i=0;$i<count($goodsdetaildata);$i++){
                $count = 0;
                if($count>=1){
                    COMMON::result(300, '货品名称不能重复！');
                    return;
                }
            }

        $employee = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $saleemployeename = $employee->getEmployeeById($request->getPost('obj_business_user_sysno'));
        $csemployeename = $employee->getEmployeeById($request->getPost('csemployee_sysno'));

        $list = array(
            'contractnodisplay' => $request->getPost('contractnodisplay', ''),
            'contractdate' => $request->getPost('contractdate', ''),
            'customer_id' => $request->getPost('obj_customer_id', ''),
            'customername' => $request->getPost('obj_customername', ''),
            'settlement_sysno' => $request->getPost('settlement_sysno', ''),
            'contracttype' => $request->getPost('contracttype', ''),
            'isbrother' => $isbrother,
            'shipproxy' => $request->getPost('shipproxy', ''),
            'inotype' => $request->getPost('inotype', ''),
            'testrequire' => $request->getPost('testrequire', ''),
            'testrequirebusiness' => $request->getPost('testrequirebusiness', ''),
            'saleemployee_sysno' => $request->getPost('obj_business_user_sysno', ''),
            'saleemployeename' => $saleemployeename['employeename'],
            'csemployee_sysno' => $request->getPost('csemployee_sysno', ''),
            'csemployeename' => $csemployeename['employeename'],
            'contractstartdate' => $request->getPost('contractstartdate', ''),
            'contractenddate' => $request->getPost('contractenddate', ''),
            'isseal' => $request->getPost('isseal', ''),
            'contractmemo' => $request->getPost('contractmemo', ''),
            'contractstatus' => $contractstatus,
            'isdel' => $request->getPost('isdel', '0'), //逻辑删除：1是0否
            'status' => $request->getPost('status', '1'),
            'version' => $request->getPost('version', '1'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
            'invoiceday' => $request->getPost('invoiceday', ''),
            'settlementtype' => $request->getPost('settlementtype', '')
        );

        $list['contractenddate'] = $list['contractenddate']?$list['contractenddate']:date('Y-m-d',strtotime('+100 year',strtotime($list['contractstartdate'])));

        $olddisplayno = $contract->getContractById($id);

        if($olddisplayno['contractnodisplay']!=$list['contractnodisplay']){
            $contractisexist = $contract->contractisexist($list['contractnodisplay']);

            if(count($contractisexist)!=0){
                COMMON::result(300, '合同编号不能重复！');
                return;
            }
        }

        if (!empty($list['inotype'])) {
            $list['inotype'] = implode(',', $list['inotype']);
        }

        /********************************查询合同评审配置表****************************************/
        $configreviewdata = $contract->getconfigreview(1);
        /**************************************END*********************************************/
        $id = $contract->updatecontract($id, $list, $contractstatus, $goodsdetaildata, $othercostdetaildata, $configreviewdata);
        if ($id) {
            $row = $contract->getContractById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    /**
     * 审核零租合同
     */
    public function examecontract2Action()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id');
        $contractstatus = $request->getPost('contractstatus', '');//判断传递的状态值
        $goodsdetaildata = $request->getPost('goodsdetaildata', '');
        $goodsdetaildata = json_decode($goodsdetaildata,true);
        $auditopinion = $request->getPost('auditopinion', '');//不审核意见

        $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = array(
            'updated_at'	=> 	'=NOW()'
        );
        $res = $contract->examecontract($id,$list, $goodsdetaildata, $contractstatus, $auditopinion);
        if ($res) {
            $row = $contract->getContractById($id);
            COMMON::result(200, '审核成功', $row);
        } else {
            COMMON::result(300, '审核失败');
        }
    }

    /*
     * 编辑靠泊装卸合同
     */
    public function editcontract3Action(){
        $request = $this->getRequest();
        $id = $request->getPost('id');
        $isbrother = $request->getPost('customerrelation', '');
        if ($isbrother == '是') {
            $isbrother = 1;
        } elseif ($isbrother == '否') {
            $isbrother = 0;
        }
        $contractstatus = $request->getPost('contractstatus', '');//判断传递的状态值

        $goodsdetaildata = $request->getPost('goodsdetaildata');
        $goodsdetaildata = json_decode($goodsdetaildata, true);
        if (count($goodsdetaildata) == 0) {
            COMMON::result(300, '品名及费用不能为空！');
            return;
        }

//        if(count($goodsdetaildata)>=2)
//            for($i=0;$i<count($goodsdetaildata);$i++){
//                $count = 0;
//                for($j=$i+1;$j<count($goodsdetaildata);$j++){
//                    if($goodsdetaildata[$i]['goodsname']==$goodsdetaildata[$j]['goodsname']
//                        &&$goodsdetaildata[$i]['berthcosttype']==$goodsdetaildata[$j]['berthcosttype']){
//                        $count++;
//                    }
//                }
//                if($count>=1){
//                    COMMON::result(300, '货品名称不能重复！');
//                    return;
//                }
//            }

        $employee = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $saleemployeename = $employee->getEmployeeById($request->getPost('obj_business_user_sysno'));
        $csemployeename = $employee->getEmployeeById($request->getPost('csemployee_sysno'));

        $list = array(
            'contractnodisplay' => $request->getPost('contractnodisplay', ''),
            'contractdate' => $request->getPost('contractdate', ''),
            'customer_id' => $request->getPost('obj_customer_id', ''),
            'customername' => $request->getPost('obj_customername', ''),
            'settlement_sysno' => $request->getPost('settlement_sysno', ''),
            // 'contracttype' => $request->getPost('contracttype',''),
            'isbrother' => $isbrother,
            'shipproxy' => $request->getPost('shipproxy', ''),
            'inotype' => $request->getPost('inotype', ''),
            'testrequire' => $request->getPost('testrequire', ''),
            'testrequirebusiness' => $request->getPost('testrequirebusiness', ''),
            'saleemployee_sysno' => $request->getPost('obj_business_user_sysno', ''),
            'saleemployeename' => $saleemployeename['employeename'],
            'csemployee_sysno' => $request->getPost('csemployee_sysno', ''),
            'csemployeename' => $csemployeename['employeename'],
            'contractstartdate' => $request->getPost('contractstartdate', ''),
            'contractenddate' => $request->getPost('contractenddate', ''),
            'isseal' => $request->getPost('isseal', ''),
            'contractmemo' => $request->getPost('contractmemo', ''),
            'contractstatus' => $contractstatus,
            'isdel' => $request->getPost('isdel', '0'), //逻辑删除：1是0否
            'status' => $request->getPost('status', '1'),
            'version' => $request->getPost('version', '1'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
            'invoiceday' => $request->getPost('invoiceday', ''),
            'settlementtype' => $request->getPost('settlementtype', ''),
            'costtype' => $request->getPost('costtype', ''),
            'instockdate' => $request->getPost('instockdate', '')
        );

        $olddisplayno = $contract->getContractById($id);

        if($olddisplayno['contractnodisplay']!=$list['contractnodisplay']){
            $contractisexist = $contract->contractisexist($list['contractnodisplay']);

            if(count($contractisexist)!=0){
                COMMON::result(300, '合同编号不能重复！');
                return;
            }
        }

        if (!empty($list['inotype'])) {
            $list['inotype'] = implode(',', $list['inotype']);
        }

        /********************************查询合同评审配置表****************************************/
        $configreviewdata = $contract->getconfigreview(2);
        /**************************************END*********************************************/
        $othercostdetaildata = array();
        $id = $contract->updatecontract($id, $list, $contractstatus, $goodsdetaildata, $othercostdetaildata, $configreviewdata);
        if ($id) {
            $row = $contract->getContractById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    /*
     * 审核靠泊装卸合同
     */
    public function examecontract3Action(){
        $request = $this->getRequest();
        $id = $request->getPost('id');

        $contractstatus = $request->getPost('contractstatus', '');//判断传递的状态值

        if ($contractstatus == 3 || $contractstatus == 4) {
            $goodsdetaildata = $request->getPost('goodsdetaildata');
            $goodsdetaildata = json_decode($goodsdetaildata, true);
            $contractstatus = $request->getPost('contractstatus', '');//判断传递的状态值
            $auditopinion = $request->getPost('auditopinion', '');//审核意见

            $list = array(
                'customer_id' => $request->getPost('obj_customer_id', ''),
                'contractnodisplay' => $request->getPost('contractnodisplay', ''),
                'customername' => $request->getPost('obj_customername', ''),
                'contracttype'=>5,
                'contractstartdate' => $request->getPost('contractstartdate', ''),
                'contractenddate' => $request->getPost('contractenddate', ''),
                'updated_at'	=> 	'=NOW()'
            );
            $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $res = $contract->examecontract($id,$list,$goodsdetaildata,$contractstatus,$auditopinion);
            if ($res) {
                $row = $contract->getContractById($id);
                COMMON::result(200, '审核成功', $row);
            } else {
                COMMON::result(300, '审核失败');
            }
        }else{
            COMMON::result(300, '不是审核操作');
        }
    }

    /*
     * 添加合同附件
     */
    public function addcontractattachAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id','');

        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        $attach =  $request->getPost('attachment',array());

        if(count($attach) > 0){
            $res =  $A->addAttachModelSysno($id,$attach);
            if($res){
                COMMON::result(200,'添加附件成功');
            }else{
                COMMON::result(300,'添加附件失败');
                return;
            }
        }else{
            COMMON::result(300,'请添加附件再上传');
        }
    }

    /**
     * 导出合同EXCEL
     */
    public function ExcelAction() {

        $request = $this->getRequest();
        $search = array (
            'page'=>false,
            'contractnodisplay' => $request->getPost('contractnodisplay',''),
            'customername' => $request->getPost('customername',''),
            'contractstatus' =>$request->getPost('contractstatus',''),
            'contracttype' =>  $request->getPost('contracttype',''),
            'saleemployee_sysno' =>  $request->getPost('saleemployee_sysno',''),
            'csemployee_sysno' =>  $request->getPost('csemployee_sysno',''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR()
        );

        $contract = new ContractModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        $contracts = $contract->searchContract($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("当前合同列表")
            ->setSubject("列表")
            ->setDescription("当前合同列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '合同编号'),
            array('B1:B1', 'B1', '005E9CD3', '合同日期'),
            array('C1:C1', 'C1', '005E9CD3', '客户姓名'),
            array('D1:D1', 'D1', '005E9CD3', '合同起始日'),
            array('E1:E1', 'E1', '005E9CD3', '合同终止日'),
            array('F1:F1', 'F1', '0094CE58', '租罐方式'),
            array('G1:G1', 'G1', '0094CE58', '品名'),
            array('H1:H1', 'H1', '005E9CD3', '计量单位'),
            array('I1:I1', 'I1', '005E9CD3', '货物性质'),
            array('J1:J1', 'J1', '0094CE58', '业务员'),
            array('K1:K1', 'K1', '003376B3', '客服专员'),
            array('L1:L1', 'L1', '0094CE58', '单据状态'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('当前合同列表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I','J','K','L','M','N','O','P','Q','R');

        foreach ($contracts['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['contractnodisplay'];
                        break;
                    case 1:
                        $value = $item['contractdate'];
                        break;
                    case 2:
                        $value = $item['customername'];
                        break;
                    case 3:
                        $value = $item['contractstartdate'];
                        break;
                    case 4:
                        $value = $item['contractenddate'];
                        break;
                    case 5:
                        if ($item['contracttype']==1) {
                            $value = "长约";
                        } else if($item['contracttype']==2){
                            $value = "短约";
                        }else if($item['contracttype']==3){
                            $value = "包罐";
                        }else if($item['contracttype']==4){
                            $value = "包罐容";
                        }else if($item['contracttype']==5){
                            $value = "靠泊装卸";
                        }
                        break;
                    case 6:
                        $value = $item['goodsname'];
                        break;
                    case 7:
                        $value = $item['unitname'];
                        break;
                    case 8:
                        if ($item['goodsnature']==1) {
                            $value = "保税";
                        }else if ($item['goodsnature']==2) {
                            $value = "外贸";
                        }else if ($item['goodsnature']==3) {
                            $value = "内贸转出口";
                        }else if ($item['goodsnature']==4) {
                            $value = "内贸内销";
                        }
                        break;
                    case 9:
                        $value = $item['saleemployeename'];
                        break;
                    case 10:
                        $value = $item['csemployeename'];
                        break;
                    case 11:
                        if ($item['contractstatus']==1) {
                            $value = "新建";
                        }else if ($item['contractstatus']==2) {
                            $value = "暂存";
                        }else if ($item['contractstatus']==3) {
                            $value = "评审中";
                        }else if ($item['contractstatus']==4) {
                            $value = "待审核";
                        }else if ($item['contractstatus']==5) {
                            $value = "已审核";
                        }else if ($item['contractstatus']==6) {
                            $value = "退回";
                        }else if ($item['contractstatus']==7) {
                            $value = "作废";
                        }
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="当前合同查询.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }


    /*
     *
     */
    public function contractgoodsJsonAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        if($id){
            $C = new ContractModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
            $data = $C->getGoodsInfo($id);
            echo json_encode($data);
        }else{
            $data = array();
            echo json_encode($data);
        }
    }

    /**
     * 待评审合同列表
     * @author Dujiangjiang
     */
    public function reviewAction()
    {
        $request = $this->getRequest();

        $search = array(
            'startDate' => $request->getPost('startDate', ''),
            'endDate' => $request->getPost('endDate', ''),
            'customerId' => $request->getPost('customerId', ''),
            'contStatus' => $request->getPost('contStatus', '-100')
        );

        $params = array();
        $params['statusarr'] = array(
            '2' => '暂存',
            '3' => '评审中',
            '4' => '待审核',
            '5' => '已审核',
            '6' => '退回',

        );

        $this->getView()->make('contract.reviewlist', $params);
    }

    /**
     * 已评审合同列表
     * @author Dujiangjiang
     */
    public function reviewedAction()
    {
        $request = $this->getRequest();

        $search = array(
            'startDate' => $request->getPost('startDate', ''),
            'endDate' => $request->getPost('endDate', ''),
            'customerId' => $request->getPost('customerId', ''),
            'contStatus' => $request->getPost('contStatus', '-100')
        );

        $params = array();
        $params['statusarr'] = array(
            '2' => '暂存',
            '3' => '评审中',
            '4' => '待审核',
            '5' => '已审核',
            '6' => '退回',
        );

        $this->getView()->make('contract.reviewedlist', $params);
    }

    /**
     * 待评审合同列表Json
     * @author Dujiangjiang
     */
    public function reviewJsonAction()
    {
        $request = $this->getRequest();
        $user = Yaf_Registry::get(SSN_VAR);
        $E = new EmployeeModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $employee = $E->getEmployeeById($user['employee_sysno']);
        $user_sysno = $user['employee_sysno'];
        $search = array(
            'startDate' => $request->getPost('startDate', ''),
            'endDate' => $request->getPost('endDate', ''),
            'customerId' => $request->getPost('obj_customerId', ''),
            'contStatus' => $request->getPost('contStatus', 3),
            'user_sysno' => $user_sysno ? $user_sysno : 0,
            'department_sysno' => $employee['department_sysno'] ? $employee['department_sysno'] : 0,
            'contractnodisplay' => $request->getPost('contractnodisplay',''),
        );
        $pages = $request->getPost('pageSize', '20');
        $pagec = $request->getPost('pageCurrent', '1');

        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $params = $C->getReviewList($search, $pages, $pagec);

        echo json_encode($params);
    }

    /**
     * 已评审合同列表Json
     * @author Dujiangjiang
     */
    public function reviewedJsonAction()
    {
        $request = $this->getRequest();
        $user = Yaf_Registry::get(SSN_VAR);
        $user_sysno = $user['employee_sysno'];
        $search = array(
            'startDate' => $request->getPost('startDate', ''),
            'endDate' => $request->getPost('endDate', ''),
            'customerId' => $request->getPost('obj_customerId', ''),
            'contStatus' => $request->getPost('contStatus', ''),
            'user_sysno' => $user_sysno ? $user_sysno : 0,
            'employee_sysno' => $user['employee_sysno'] ? $user['employee_sysno'] : 0,
            'contractnodisplay' => $request->getPost('contractnodisplay',''),
        );

        $pages = $request->getPost('pageSize', '20');
        $pagec = $request->getPost('pageCurrent', '1');

        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $params = $C->getReviewedList($search, $pages, $pagec);

        echo json_encode($params);
    }

    /**
     * 评审合同Json
     * @author Dujiangjiang
     */
    public function reviewEditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');

        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params = $C->getContractById($id);

        $settlement = new SettlementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $arr = array('status' => 1);
        $data = $settlement->getSettlement($arr);
        $params['settlementlist'] = $data['list'];

        $employee = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        //客服列表
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
          // 'bar_name' => '客服',
            'page' => false,
        );
        $params['csemployee'] = $employee->searchEmployee($search);
        //业务员列表
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
        //   'bar_name' => '业务',
            'page' => false,
        );
        $params['saleemployee'] = $employee->searchEmployee($search);

        $arr = explode(',', $params['inotype']);
        for ($i = 0; $i < 4; $i++) {
            $params['inotypes'][$i] = 0;
            foreach ($arr as $v) {
                if ($v > 0) {
                    $params['inotypes'][$v - 1] = $v;
                }
            }
        }
        $params['id'] = $id;
        $params['reved']=$request->getParam('reved', '0');


//        echo "<pre>";print_r($params);die;
        $this->getView()->make('contract.reviewedit', $params);

    }

    /**
     * 合同汇签数据Json
     * @author Dujiangjiang
     */
    public function reviewDetailAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');

        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $user = Yaf_Registry::get(SSN_VAR);

        $E = new EmployeeModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $employee = $E->getEmployeeById($user['employee_sysno']);

        $reviewdp = $C->getContractReview($id);
        $contractDetail = $C -> getContractById($id);
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

        if($contractDetail['csemployee_sysno'] && $user['employee_sysno'] == $contractDetail['csemployee_sysno']){
            $department_sysno = '';
        }

        $params = $C->getContract($id,$department_sysno);
        echo json_encode($params);
    }

    /**
     * 合同汇签提交
     * @author Dujiangjiang
     */
    public function reviewPostAction()
    {
        $request = $this->getRequest();
        $cid = $request->getPost('cid', '0');
        $status = $request->getPost('status', '1');
        $reviewmemo = $request->getPost('reviewmemo', '');
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
            COMMON::result(300, '您所属的“' . $employee['departmentname'] . '”部门已评审过，请不要重复评审。');
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
                COMMON::result(200, '操作成功。');
                exit;
            } else {
                COMMON::result(300, '操作失败。');
                exit;
            }
        } else {
            COMMON::result(300, '您所属的“' . $employee['departmentname'] . '”部门不在汇签名单之内，不能参与评审。');
            exit;
        }
    }

    /*
     * 删除合同
     */
    public function delcontractAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);
        $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'contractnodisplay' => $request->getPost('contractnodisplay', '0').'del'.date('Y-m-d',time()),
            'isdel' => 1
        );
        $result = $contract->deletecontract($id, $input);
        if ($result) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    /*
     * 显示合同查询视图
     */
    public function detailAction()
    {
        $params = array();
        $Em = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status'=>1,
            'page'=>false
        );
        $params['employee'] = $Em->getEmployeelist();
        $user = Yaf_Registry::get(SSN_VAR);
        $params['initial_sysno'] = $user['employee_sysno'];
        $customerdata = $C->searchCustomer($search);
        $params['customerlist'] = $customerdata['list'];
        $params['flag'] = 1;
        $this->getView()->make('contract.contractlist', $params);
    }

    /*
     * 获取合同查询数据
     */
    public function listJsonAction()
    {
        $request = $this->getRequest();
        $user = Yaf_Registry::get(SSN_VAR);
        $search = array(
            'startdate' => $request->getPost('startdate', ''),
            'enddate' => $request->getPost('enddate', ''),
            'contractnodisplay' => $request->getPost('contractnodisplay', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'contractstatus' => $request->getPost('contractstatus', ''),
            'saleemployee_sysno' => $request->getPost('saleemployee_sysno', ''),
            'csemployee_sysno' => $request->getPost('csemployee_sysno', ''),
            'contracttype' => $request->getPost('contracttype', ''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR()
        );
        $C = new ContractModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $C->searchContract($search);

        echo json_encode($list);
    }

    /*
     * 作废整租合同
     */
    public function abolishcontractAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id',0);
        $contractstatus = $request->getPost('contractstatus', '');//判断传递的状态值
        $abandonreason = $request->getPost('abandonreason', '');//作废意见（合同备注）

        $S = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'contract_sysno'=>$id,
            'page'=>false
        );
        $bookcarindata  = $S->searchBookcarin($search);
        if(!empty($bookcarindata['list'])){
            COMMON::result(300, '合同已被入库预约单引用，不能作废');
            return ;
        }

        $list = array();
        $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $res = $contract->abolishcontract($id,$list, $contractstatus ,$abandonreason);
        if ($res) {
            $row = $contract->getContractById($id);
            COMMON::result(200, '作废成功', $row);
        } else {
            COMMON::result(300, '作废失败');
        }
    }

    /*
     * 作废零租合同
     */
    public function abolishcontract2Action(){
        $request = $this->getRequest();
        $id = $request->getPost('id');
        $contractstatus = $request->getPost('contractstatus', '');//判断传递的状态值
        $abandonreason = $request->getPost('abandonreason', '');//作废意见（合同备注）

        $S = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'contract_sysno'=>$id,
            'page'=>false
        );
        $bookcarindata  = $S->searchBookcarin($search);
        if(!empty($bookcarindata['list'])){
            COMMON::result(300, '合同已被入库预约单引用，不能作废');
            return ;
        }

        $list = array();
        $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $res = $contract->abolishcontract($id,$list, $contractstatus ,$abandonreason);
        if ($res) {
            $row = $contract->getContractById($id);
            COMMON::result(200, '作废成功', $row);
        } else {
            COMMON::result(300, '作废失败');
        }
    }

    /*
     * 作废靠泊装卸合同
     */
    public function abolishcontract3Action(){
        $request = $this->getRequest();
        $id = $request->getPost('id');
        $contractstatus = $request->getPost('contractstatus', '');//判断传递的状态值
        $abandonreason = $request->getPost('abandonreason', '');//作废意见（合同备注）

        $S = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'contract_sysno'=>$id,
            'page'=>false
        );
        $bookcarindata  = $S->searchBookcarin($search);
        if(!empty($bookcarindata['list'])){
            COMMON::result(300, '合同已被入库预约单引用，不能作废');
            return ;
        }

        $list = array();
        $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $res = $contract->abolishcontract($id,$list, $contractstatus ,$abandonreason);
        if ($res) {
            $row = $contract->getContractById($id);
            COMMON::result(200, '作废成功', $row);
        } else {
            COMMON::result(300, '作废失败');
        }
    }

    /*
     *显示合同待审核查询页面
     */
    public function pendlistAction()
    {
        $params = array();
        $Em = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status'=>1,
            'page'=>false
        );
        $params['employee'] = $Em->getEmployeelist();
        $customerdata = $C->searchCustomer($search);
        $params['customerlist'] = $customerdata['list'];

        $params['flag'] = 1;
        $this->getView()->make('contract.pendlist', $params);
    }

    /*
     * 获取同待审核查询数据
     */
    public function pendlistJsonAction()
    {
        $request = $this->getRequest();
        $search = array(
            'startdate' => $request->getPost('startdate', ''),
            'enddate' => $request->getPost('enddate', ''),
            'contractnodisplay' => $request->getPost('contractnodisplay', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'contractstatus' => 4,
            'saleemployee_sysno' => $request->getPost('saleemployee_sysno', ''),
            'csemployee_sysno' => $request->getPost('csemployee_sysno', ''),
            'contracttype' => $request->getPost('contracttype', ''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR()
        );

        $C = new ContractModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $C->searchContract($search);

        foreach($list['list'] as $item){
            $item['created_at']=date("Y-m-d",$item['created_at']);
        }
        echo json_encode($list);
    }

    //导出合同
    public function exportAction()
    {
        ob_end_clean();
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);

        //合同信息
        $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $contractInfo = $contract->getContractById($id);

        //判断打印合同方式
        //$contracttype = 2;
        //$contractstatus = 6;
        // contracttype      3包罐 4包罐容   整租合同     1 长约 2短约  零租合同     5靠泊装卸
        // contractstatus    1新建 2暂存 3评审中 4待审核 5已审核 6退回  7作废 除1不可打印剩余都可以打印
        if( in_array($contractInfo['contractstatus'], [2, 3, 4, 5, 6, 7] )){
            if (in_array($contractInfo['contracttype'], [3, 4])) {  //整租合同
                return self::baoguan($contractInfo);
            } elseif (in_array($contractInfo['contracttype'], [1, 2])) {  //零租合同
                return self::nobaoguan($contractInfo);
            } elseif (in_array($contractInfo['contracttype'], [5])){  //靠泊装卸
                return self::kaobo($contractInfo);
            } else{
                COMMON::result(300, '未知的租罐方式');
            }
        }else {
            COMMON::result(300, '新建状态不可打印');
        }
    }

    //包罐合同
    private static function baoguan($contractInfo)
    {
        extract($contractInfo);
        $cm = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(APPLICATION_PATH . '/application/views/seal/baoguan.docx');

        $templateProcessor->setValue('contractno', $contractnodisplay);
        $templateProcessor->setValue('contractdate', $contractdate);
        //存货方
        $customer = $cm->getCustomerById($customer_id);
        $templateProcessor->setValue('a_name', $customer['customername']);
        $templateProcessor->setValue('a_address', $customer['customeraddress']);
        $templateProcessor->setValue('a_phone', $customer['customertelephone']);
        $templateProcessor->setValue('a_fax', $customer['customerfax']);

        //货品相关信息和储运条件和仓储费用
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $goodslist = $goods->getGoodsandpriceByid($sysno);
        $templateProcessor->cloneRow('goodsname', count($goodslist));
        foreach ($goodslist as $key => $item) {//货品信息
//            $goodsnature_val = [1=>'保税',2=>'外贸',3=>'内贸转出口',4 =>'内贸内销'];
            $templateProcessor->setValue('goodsname#' . ($key + 1), $item['goodsname']);
//            $templateProcessor->setValue('goodsnature#' . ($key + 1), $goodsnature_val[$item['goodsnature']]);
//            $templateProcessor->setValue('qualityname#' . ($key + 1), $item['qualityname']);
            $templateProcessor->setValue('unitname#' . ($key + 1), $item['unitname']);
            $templateProcessor->setValue('contractrate#' . ($key + 1), floatval($item['contractrate']));
            $templateProcessor->setValue('storagetankname#' . ($key + 1), $item['storagetankname']);
            $templateProcessor->setValue('actualcapacity#' . ($key + 1), floatval($item['capacity']));
//            $templateProcessor->setValue('storagestartdate#' . ($key + 1), $item['storagestartdate']);
//            $templateProcessor->setValue('storageenddate#' . ($key + 1), $item['storageenddate']);
            $templateProcessor->setValue('yearqty#' . ($key + 1), floatval($item['yearqty']));
            $templateProcessor->setValue('yearamount#' . ($key + 1), floatval($item['yearamount'])) ;
            $templateProcessor->setValue('exyearrate#' . ($key + 1), floatval($item['exyearrate']));
            $templateProcessor->setValue('overcapacity#' . ($key + 1), floatval($item['overcapacity']));
            $templateProcessor->setValue('firstlossrate#' . ($key + 1), floatval($item['overfirstpayment']));
            $templateProcessor->setValue('lastlossrate#' . ($key + 1), floatval($item['overlastpayment']));
        }

        //储运条件和仓储费用
        $storagetank = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $storagelist = $storagetank->storagecontractByid($sysno);

        foreach ($storagelist as $key => $item) {
            $company_id = $storagelist[0]['invoice_company_sysno'] ? $storagelist[0]['invoice_company_sysno'] : 0;
//            $templateProcessor->setValue('storagetankname#' . ($key + 1), $item['storagetankname']);
//            $storagetanknature = '';
//            if ($item['storagetanknature'] == 1) $storagetanknature = '内贸罐';
//            elseif ($item['storagetanknature'] == 2) $storagetanknature = '外贸罐';
//            elseif ($item['storagetanknature'] == 3) $storagetanknature = '保税罐';
//            $templateProcessor->setValue('storagetanknature#' . ($key + 1), $storagetanknature);
//            $templateProcessor->setValue('storagetank_categoryname#' . ($key + 1), $item['storagetank_categoryname']);
//            $templateProcessor->setValue('actualcapacity#' . ($key + 1), $item['actualcapacity']);
//            $templateProcessor->setValue('yearqty#' . ($key + 1), $item['yearqty']);
//            $templateProcessor->setValue('yearamount#' . ($key + 1), $item['yearamount']);
//            $templateProcessor->setValue('storageenddate#' . ($key + 1), $item['storageenddate']);
//            $templateProcessor->setValue('startlossqty#' . ($key + 1), $item['startlossqty']);
//            $templateProcessor->setValue('exyearrate#' . ($key + 1), $item['exyearrate']);
        }

        //保管方
        $com = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $master = $com ->getCompanyById($company_id);
        $templateProcessor->setValue('customeraddress', $master['warehouseaddress'] ? $master['warehouseaddress'] : '');
        $templateProcessor->setValue('customertelephone', $master['contacttel'] ? $master['contacttel'] : '');
        $templateProcessor->setValue('customerfax', $master['contactfax'] ? $master['contactfax'] : '');

        //杂费费率标准
        $othercost = new OthercostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $othercostlist = $othercost->othercostcontractByid($sysno);

        $templateProcessor->cloneRow('othercostname', count($othercostlist));

        foreach ($othercostlist as $key => $item) {
            $templateProcessor->setValue('othercostname#' . ($key + 1), $item['othercostname']);
            $templateProcessor->setValue('unitname#' . ($key + 1), $item['unitname']);
            $templateProcessor->setValue('othercostprice#' . ($key + 1), $item['othercostprice']);
        }

        //结算方式
        $settlement = new SettlementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $payinfo = $settlement->getSettlementById($settlement_sysno);
        $pay_name = $payinfo['settlementname'];
        $templateProcessor->setValue('pay_type', $pay_name);
        $templateProcessor->setValue('a_fax', $customer['customerfax']);

        /*
        header("Content-type:text/html;charset=utf-8");
        print_r($storagelist);exit;
        */
        //仓储费
        $chuguan = [];
        foreach ($storagelist as $key => $item) {
            $chuguan[] = [
                'othercostname' => '仓储费',
                'companyname' => $item['companyname'],
                'bank' => $item['bank'],
                'bank_account' => $item['bank_account']
            ];
        }

        if(!empty($chuguan)){
            $othercostlist = array_merge($chuguan, $othercostlist);
        }


        $otherBank = self::payBank($othercostlist);
        $templateProcessor->cloneRow('pay_name', count($otherBank));
        $customername = '';
        //其他费用
        foreach ($otherBank as $key => $item) {
            $templateProcessor->setValue('pay_name#'. ($key + 1), $item['othercostname']);
            $templateProcessor->setValue('pay_people#'. ($key + 1), $item['companyname']);
            $templateProcessor->setValue('pay_bank#'. ($key + 1), $item['bank']);
            $templateProcessor->setValue('pay_account#'. ($key + 1), $item['bank_account']);
            $customername .= "，".$item['companyname'];
        }
        $templateProcessor->setValue('customername', trim($customername, "，"));
        //下载
        $res = $templateProcessor->save();

        if ($res) {
            $fp = fopen($res, "rb"); //二进制方式打开文件
            if ($fp) {
                header("Content-Description: File Transfer");
                header('Content-Disposition: attachment; filename="' . '整租合同.docx' . '"');
                header('Content-Type: ' . 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                header('Content-Transfer-Encoding: binary');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Expires: 0');

                fpassthru($fp); // 输出至浏览器
                exit;
            }
        }

    }

    //非包罐合同
    private static function nobaoguan($contractInfo)
    {
        extract($contractInfo);
        $cm = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(APPLICATION_PATH . '/application/views/seal/feibaoguan.docx');

        //卖方信息
        $customer = $cm->getCustomerById($customer_id);
        $templateProcessor->setValue('seal_no', $contractnodisplay);
        $templateProcessor->setValue('seal_time', $contractdate);
        $templateProcessor->setValue('b_mobile', $customer['customertelephone']);
        $templateProcessor->setValue('b_name', $customer['customername']);
        $templateProcessor->setValue('b_fax', $customer['customerfax']);
        $templateProcessor->setValue('b_address', $customer['customeraddress']);


        //货品相关信息和储运条件和仓储费用
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $goodslist = $goods->getGoodsandpriceByid($sysno);
        $templateProcessor->cloneRow('goodsname', count($goodslist));
        $company_id_str = '';
        foreach ($goodslist as $key => $item) {//货品信息
            $goodsnature_val = [1=>'保税',2=>'外贸',3=>'内贸转出口',4 =>'内贸内销'];
            $company_id_str .= "," .$item['invoice_company_sysno'];
            $jieti = '';
            if($item['isladder']){
                $jieti = '('.$item['ladderstart'].'—'.(floatval($item['ladderend']) ? $item['ladderend'] : '以上').')';
            }
            $templateProcessor->setValue('goodsname#' . ($key + 1), $jieti.$item['goodsname']);
            $templateProcessor->setValue('goodsnature#' . ($key + 1), $goodsnature_val[$item['goodsnature']]);
            $templateProcessor->setValue('guige#' . ($key + 1), $item['qualityname']);
            $templateProcessor->setValue('unit#' . ($key + 1), $item['unitname']);
            $templateProcessor->setValue('cost#' . ($key + 1), $item['firstlossrate']);
//            $templateProcessor->setValue('startdate#' . ($key + 1), $item['storagestartdate']);
//            $templateProcessor->setValue('enddate#' . ($key + 1), $item['storageenddate']);
            $templateProcessor->setValue('startdate#' . ($key + 1), $contractstartdate);
            $templateProcessor->setValue('enddate#' . ($key + 1), $contractenddate);
            $templateProcessor->setValue('first_fee#'.($key + 1), $item['firststorageamount']);
            $templateProcessor->setValue('over_fee#'.($key + 1), $item['lastamount']);
//            $templateProcessor->setValue('transportamount', $item['firsttransportamount']);
        }

        //存货方
        $com = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $companyIdArr = explode("," , trim($company_id_str , ','));
        $chuguan = [];
        foreach($companyIdArr as $key => $value){
            $master = $com ->getCompanyById($value);
            if($key == 0){
                $templateProcessor->setValue('a_address', $master['warehouseaddress']);
                $templateProcessor->setValue('a_phone', $master['contacttel']);
                $templateProcessor->setValue('a_fax', $master['contactfax']);
            }else{
                $templateProcessor->setValue('a_address', '');
                $templateProcessor->setValue('a_phone', '');
                $templateProcessor->setValue('a_fax', '');
            }
            //储罐使用费
            $chuguan[] = [
                'othercostname' => '储罐使用费',
                'companyname' => $master['companyname'],
                'bank' => $master['bank'],
                'bank_account' => $master['bank_account']
            ];
        }


        //结算方式
        $settlement = new SettlementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $payinfo = $settlement->getSettlementById($settlement_sysno);
        $pay_name = $payinfo['settlementname'];
        $templateProcessor->setValue('pay_type', $pay_name);
        $templateProcessor->setValue('b_fax', $customer['customerfax']);

        //杂费费率标准
        $othercost = new OthercostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $othercostlist = $othercost->othercostcontractByid($sysno);
        $templateProcessor->cloneRow('othercostname', count($othercostlist));
        //第五条	其他费用
        foreach($othercostlist as $key => $value){
            $templateProcessor->setValue('othercostname#'.($key +1), $value['othercostname']);
            $templateProcessor->setValue('unitname#'.($key + 1), $value['unitname']);
            $templateProcessor->setValue('othercostprice#'.($key +1), $value['othercostprice']);
        }

        //储罐使用费和管道输送费
        if(!empty($chuguan)) {
            $othercostlist = array_merge($chuguan, $othercostlist);
        }
        $otherBank = self::payBank($othercostlist);
        //其他费用
        $a_name = '';
        $templateProcessor->cloneRow('pay_name', count($otherBank));
        foreach ($otherBank as $key => $item) {
            $templateProcessor->setValue('pay_name#'. ($key + 1), $item['othercostname']);
            $templateProcessor->setValue('pay_people#'. ($key + 1), $item['companyname']);
            $templateProcessor->setValue('pay_bank#'. ($key + 1), $item['bank']);
            $templateProcessor->setValue('pay_account#'. ($key + 1), $item['bank_account']);
            $a_name .= "，".$item['companyname'];
        }

        $templateProcessor->setValue('a_name', trim($a_name, "，"));
        //下载
        $res = $templateProcessor->save();

        if ($res) {
            $fp = fopen($res, "rb"); //二进制方式打开文件
            if ($fp) {
                header("Content-Description: File Transfer");
                header('Content-Disposition: attachment; filename="' . '零租合同.docx' . '"');
                header('Content-Type: ' . 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                header('Content-Transfer-Encoding: binary');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Expires: 0');

                fpassthru($fp); // 输出至浏览器
                exit;
            }
        }
    }

    //靠泊装卸合同
    private static function kaobo($contractInfo)
    {
        extract($contractInfo);
        $cm = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(APPLICATION_PATH . '/application/views/seal/kaobo.docx');
        //甲方信息
        $customer = $cm->getCustomerById($customer_id);
        $templateProcessor->setValue('b_name', $customer['customername']);

        //二.有效期
        $cd = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $contractgoods = $cd->getContractInfoById($sysno);
        $templateProcessor->setValue('startdate', $contractgoods[0]['contractstartdate']);
        $templateProcessor->setValue('enddate', $contractgoods[0]['contractenddate']);
        //四.费用及结算方式
        $templateProcessor->setValue('sysno', $contractgoods[0]['sysno']);
        $templateProcessor->setValue('goodsname', $contractgoods[0]['goodsname']);
        $templateProcessor->setValue('foreign', $contractgoods[0]['berthcostforeign']);
        $templateProcessor->setValue('domestic', $contractgoods[0]['berthcostdomestic']);
        $templateProcessor->setValue('berthcost', $contractgoods[0]['berthcost']);

        //乙方信息
        $v = new VendorModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $vendor = $v->getHengyangVendor();
        $templateProcessor->setValue('a_name', $vendor['vendorname']);

        //下载
        $res = $templateProcessor->save();

        if ($res) {
            $fp = fopen($res, "rb"); //二进制方式打开文件
            if ($fp) {
                header("Content-Description: File Transfer");
                header('Content-Disposition: attachment; filename="' . '靠泊装卸合同.docx' . '"');
                header('Content-Type: ' . 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                header('Content-Transfer-Encoding: binary');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Expires: 0');

                fpassthru($fp); // 输出至浏览器
                exit;
            }
        }
    }

    /**
     * 合同账户处理 如果公司名字一样的则项目名字合并
     * @param array $array
     * @return array
     */
    private static  function payBank(array $array){
        //定义去重条件
        $company_arr = [];
        $result = [];
        if(is_array($array)){
            foreach ($array as $key => $value) {
                //去重 并把重复的写入相同结果集里
                if(in_array($value['companyname'], $company_arr)){
                    foreach ($result as &$v) {
                        if($v['companyname'] == $value['companyname']){
                            if(!in_array($value['othercostname'], explode("，", $v['othercostname']))){
                                $v['othercostname'] = $v['othercostname']."，".$value['othercostname'];
                            }
                        }
                    }
                    continue;
                }else {
                    //不同的的直接写入
                    $company_arr[] = $value['companyname'];
                    $result[] = $value;
                }
            }
        }
        return $result;
    }

    /*
     * 二维数组排序
     */
    public function list_sort_by($list, $field, $sortby = 'asc')
    {
        if (is_array($list))
        {
            $refer = $resultSet = array();
            foreach ($list as $i => $data)
            {
                $refer[$i] = &$data[$field];
            }
            switch ($sortby)
            {
                case 'asc': // 正向排序
                    asort($refer);
                    break;
                case 'desc': // 逆向排序
                    arsort($refer);
                    break;
                case 'nat': // 自然排序
                    natcasesort($refer);
                    break;
            }
            foreach ($refer as $key => $val)
            {
                $resultSet[] = &$list[$key];
            }
            return $resultSet;
        }
        return false;
    }
}
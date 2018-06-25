<?php

class FinancecostController extends Yaf_Controller_Abstract {
    /**
     * IndexController::init()
     *
     * @return void
     */
    public function init() {
        # parent::init();
        // $F = new FinancecostModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        // $this->addFinancecostbyplanAction();
        // exit;

    }

    /**
     * 显示整个后台页面框架及菜单
     *
     * @return string
     */
    public function listAction() {
        $params = array(
            'bar_no'=>'',
            'bar_name'=>''
        );

        $this->getView()->make('financecost.financecostlist',$params);
    }

    public function otherlistAction() {
        $params = array(
            'bar_no'=>'',
            'bar_name'=>''
        );

        $this->getView()->make('financecost.financecostotherlist',$params);
    }

    public function calcAction() {
        $request = $this->getRequest();
        $params = array(
            'bar_no'=>'',
            'bar_name'=>''
        );
        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $search = array(
            'page' => false,
        );
        $list = $C->searchCustomer($search);
        $params['customerlist'] =  $list['list'];

        $data = array (
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

        $B = new ContractModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $contracts = $B->searchContract($data);

        foreach($contracts['list'] as $key=>$value){
            $contracts['list'][$key]['contract_no']=$value['contractnodisplay'];
            unset($contracts['list'][$key]['contractnodisplay']);
        }
        $params['contractslist'] =  $contracts['list'];

        $goods_data = array(
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR()
        );
        $G = new GoodsModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $goods = $G->getBaseGoods($goods_data);
        $params['goods'] = $goods['list'];

        $this->getView()->make('financecost.financecoststoragelist',$params);
    }

    public function loadlistAction() {
        $params = array(
            'bar_no'=>'',
            'bar_name'=>''
        );

        $this->getView()->make('financecost.financecostloadlist',$params);
    }

    public function loadlistJsonAction() {
        $request = $this->getRequest();

        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'bar_name' => $request->getPost('bar_name',''),
            'bar_status' => $request->getPost('bar_status','-100'),
            'bar_isdel' => $request->getPost('bar_isdel','-100'),
            'bar_coststatus' => $request->getPost('bar_coststatus','-100'),
            'begin_time' => $request->getPost('begin_time',''),
            'end_time' => $request->getPost('end_time',''),
            'bar_contract' => $request->getPost('bar_contract',''),
            'bar_berthtype' => $request->getPost('bar_berthtype','-100'),
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),
            'orders'  => $request->getPost('orders',''),

        );
        $F = new FinancecostModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $F->searchFinancecostload($search);

        echo json_encode($list);

    }

    public function listotherJsonAction() {
        $request = $this->getRequest();

        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'bar_name' => $request->getPost('bar_name',''),
            'bar_status' => $request->getPost('bar_status','-100'),
            'bar_isdel' => $request->getPost('bar_isdel','-100'),
            'bar_coststatus' => $request->getPost('bar_coststatus','-100'),
            'begin_time' => $request->getPost('begin_time',''),
            'end_time' => $request->getPost('end_time',''),
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),
            'orders'  => $request->getPost('orders',''),

        );
        $F = new FinancecostModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $F->searchFinancecostother($search);
        foreach ($list['list'] as $k=>$v){
            if($v['costtype'] == 0){
                $costdates = (strtotime($v['costdateend'])-strtotime($v['costdate']))/86400;
                if ($costdates < 30){
                    $list['list'][$k]['countcostdateend'] = (strtotime(date("Y-m-d")) - strtotime($v['costdate']))/86400;
                }else{
                    $list['list'][$k]['countcostdateend'] = 30;
                }
            }else{
                $list['list'][$k]['countcostdateend'] = '--';
            }
            $v['unitname'] = $v['unitname']?$v['unitname']:'吨';
            $list['list'][$k]['unitname'] = $v['unitprice'].'/'.$v['unitname'];
        }
        echo json_encode($list);

    }

    public function listJsonAction() {
        $request = $this->getRequest();

        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'bar_name' => $request->getPost('bar_name',''),
            'bar_status' => $request->getPost('bar_status','-100'),
            'bar_isdel' => $request->getPost('bar_isdel','-100'),
            'bar_coststatus' => $request->getPost('bar_coststatus','-100'),
            'begin_time' => $request->getPost('begin_time',''),
            'end_time' => $request->getPost('end_time',''),
            'bar_contract' => $request->getPost('bar_contract',''),
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),
            'orders'  => $request->getPost('orders',''),

        );
        $F = new FinancecostModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $F->searchFinancecost($search);
        foreach ($list['list'] as $k=>$v){
            if($v['costtype'] == 0){
                $costdates = (strtotime($v['costdateend'])-strtotime($v['costdate']))/86400;
                if ($costdates < 30){
                    $list['list'][$k]['countcostdateend'] = (strtotime(date("Y-m-d")) - strtotime($v['costdate']))/86400;
                }else{
                    $list['list'][$k]['countcostdateend'] = 30;
                }
            }else{
                $list['list'][$k]['countcostdateend'] = '--';
            }
            $v['unitname'] = $v['unitname']?$v['unitname']:'吨';
            $list['list'][$k]['unitname'] = $v['unitprice'].'/'.$v['unitname'];
        }
        echo json_encode($list);

    }

    public function listcalcJsonAction() {
        $request = $this->getRequest();

        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'bar_name' => $request->getPost('bar_name',''),
            'bar_status' => $request->getPost('bar_status','-100'),
            'bar_isdel' => $request->getPost('bar_isdel','-100'),
            'bar_coststatus' => $request->getPost('bar_coststatus','-100'),
            'customer_sysno' => $request->getPost('customer_sysno','-100'),
            'contract_sysno' => $request->getPost('contract_sysno',''),
            'begin_time' => $request->getPost('begin_time',''),
            'end_time' => $request->getPost('end_time',''),
            'shipname' => $request->getPost('shipname',''),
            'begin_time' => $request->getPost('begin_time',''),
            'end_time' => $request->getPost('end_time',''),
            'instockdate' => $request->getPost('instockdate',''),
            'goodsname' => $request->getPost('goodsname',''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'orders'  => $request->getPost('orders',''),

        );
        $F = new FinancecostModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $F->searchFinancecostcalc($search);
        foreach ($list['list'] as $k=>$v){
            if($v['costtype'] == 0){
                $costdates = (strtotime($v['costdateend'])-strtotime($v['costdate']))/86400;
                if ($costdates < 30){
                    $list['list'][$k]['countcostdateend'] = (strtotime(date("Y-m-d")) - strtotime($v['costdate']))/86400;
                }else{
                    $list['list'][$k]['countcostdateend'] = 30;
                }
            }else{
                $list['list'][$k]['countcostdateend'] = '--';
            }
            $v['unitname'] = $v['unitname']?$v['unitname']:'吨';
            $list['list'][$k]['unitname'] = $v['unitprice'].'/'.$v['unitname'];
        }
        echo json_encode($list);

    }

    public function EditAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id','0');
        // $costtype = $request->getPost('costtype','2');
        // $costname = $request->getPost('costname','其他费用');
        $mode = $request->getParam('mode', '');

        if(!isset($id)) {
            $id = 0;
            // $costtype = 2;
            // $costname = '其他费用';
        }

        $S = new FinancecostModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        if(!$id){
            $action = "/financecost/newJson/";
        }

        else{
            $action = "/financecost/editJson/";
            $params = $S->getFinancecostotherById($id);
        }

        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $search = array(
            'page' => false,
        );
        $list = $C->searchCustomer($search);
        $params['customerlist'] =  $list['list'];
        $params['coststatusnamelist'] = array(
            0=>array('id'=>"",'name'=>'新建'),
            1=>array('id'=>1,'name'=>'新建'),
            2=>array('id'=>2,'name'=>'暂存'),
            3=>array('id'=>3,'name'=>'待审核'),
            4=>array('id'=>4,'name'=>'已审核'),
            5=>array('id'=>5,'name'=>'作废'),
            6=>array('id'=>6,'name'=>'退回'),
        );
        // $params['costtype'] = $costtype;
        // $params['costname'] = $costname;
        $params['mode'] = $mode;

        $search = array(
            'page' => false,
        );
        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];

        $params['id'] =  $id;
        $params['action'] =  $action;

        $this->getView()->make('financecost.financecostedit',$params);
    }

    public function newJsonAction()
    {
        $request = $this->getRequest();
        $financecostdetaildata = $request->getPost('financecostdetaildata',"");
        $financecostdetaildata = json_decode($financecostdetaildata,true);
        if(count($financecostdetaildata)==0) {
            COMMON::result(300,'杂费信息不能为空');
            return;
        }

        $F = new FinancecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'costno' => COMMON::getCodeId('F2'),
            'costdate' => $request->getPost('costdate', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'customer_name' => $request->getPost('customer_name', ''),
            'coststatus' => $request->getPost('coststatus', '2'),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contract_no' => $request->getPost('contract_no', ''),
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'version' => $request->getPost('version', '1'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        if ($id = $F->addFinancecost($input,$financecostdetaildata)) {
            // $row = $F->getFinancecostotheroneById($id);
            // $row['shipname'] = $financecostdetaildata['shipname'];
            // $row['goodsname'] = $financecostdetaildata['goodsname'];
            // $row['costname'] = $financecostdetaildata['costname'];
            // $row['qualityname'] = $financecostdetaildata['qualityname'];
            // $row['costqty'] = $financecostdetaildata['costqty'];
            // $row['unitprice'] = $financecostdetaildata['unitprice'];
            // $row['totalprice'] = $financecostdetaildata['totalprice'];
            // $row['costname'] = $financecostdetaildata['costname'];
            // $row['unitname'] = $financecostdetaildata['unitname'];
            // $row['qualityname'] = $financecostdetaildata['qualityname'];
            // $row['storagetankname'] = $financecostdetaildata['storagetankname'];
            // $row['isstoragetank'] = $financecostdetaildata['isstoragetank'];
            // $row['isexceedfirst'] = $financecostdetaildata['isexceedfirst'];
            // COMMON::result(200, '添加成功', $row);
            COMMON::result(200, '添加成功');
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    public function editJsonAction()
    {
        $request = $this->getRequest();
        $financecostdetaildata = $request->getPost('financecostdetaildata',"");
        $financecostdetaildata = json_decode($financecostdetaildata,true);
        if(count($financecostdetaildata)==0) {
            COMMON::result(300,'费用单明细不能为空');
            return;
        }

        $F = new FinancecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'costno' => COMMON::getCodeId('F2'),
            'costdate' => $request->getPost('costdate', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'customername' => $request->getPost('customer_name', ''),
            'coststatus' => $request->getPost('coststatus', '2'),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contract_no' => $request->getPost('contract_no', ''),
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        if ($id = $F->addFinancecost($input,$financecostdetaildata)) {
            $row = $F->getFinancecostById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    public function auditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        $F = new FinancecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'coststatus' => $request->getPost('coststatus', ''),
            'updated_at' => '=NOW()'
        );

        if ($F->updateFinancecost($id, $input, $financecostdetaildata=array(), $request->getPost('coststatus', ''))) {
            $row = $F->getFinancecostById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    public function delJsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('sysno',0);
        $ids = explode(',',$id);

        $S = new FinancecostModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

        if($S->delFinancecost($ids)){
            COMMON::result(200,'删除成功');
        }else{
            COMMON::result(300,'已开票状态费用单无法删除');
        }
    }

    public function adddetailAction()
    {
        $request = $this->getRequest();
        $customer_sysno = $request->getParam('customer_sysno','0');
        $contract_sysno = $request->getParam('contract_sysno','0');
        $customer_name = urldecode($request->getParam('customer_name',''));
        $contract_no = urldecode($request->getParam('contract_no',''));

        $params = array(
            'bar_no'=>'',
            'bar_name'=>''
        );

        $search = array(
            'page' => false,
        );
        $S = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $S->searchStoragetank($search);
        $params['storagetanklist'] =  $list['list'];
        
        $quality = new QualityModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $quality->getList($search,99,1);
        $params['goodsqualitylist'] =  $list['list'];

        $params['customer_sysno'] = $customer_sysno;
        $params['contract_sysno'] = $contract_sysno;
        $params['customer_name'] = $customer_name;
        $params['contract_no'] = $contract_no;

        $this->getView()->make('financecost.financecostadddetail',$params);
    }

    public function adddetailJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id','0');

        if(!isset($id)) {
            $id = 0;
        }

        $S = new FinancecostModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $S->getFinancecostotherdetailById($id);

        echo json_encode($list);
    }

    public function detailsubmitAction()
    {
        $request = $this->getRequest();
        var_dump($request);
        exit;

        $list = array(
            'goods_sysno' => $request->getPost('obj_goods_sysno', ''),
            'goodsname' => $request->getPost('obj_goodsname', ''),
            'goods_quality_sysno' => $request->getPost('goodsquality', ''),
            'goodsnature' => $request->getPost('goodsnature', ''),
            'goodsreceiptdate' => $request->getPost('goodsreceiptdate', ''),
            'tobeqty' => $request->getPost('tobeqty', ''),
            'beqty' => $request->getPost('beqty', ''),
            'storagebank_sysno' => $request->getPost('storagebank_sysno', ''),
            'shipname' => $request->getPost('obj_shipname', ''),
            'expresscompanyname' => $request->getPost('expresscompanyname', ''),
            'memo' => $request->getPost('memo', ''),
        );
        $list = array(

        );

        echo json_encode($list);
    }

    public function othercostdetailAction()
    {
        $request = $this->getRequest();
        $customer_sysno = $request->getParam('cid','0');
        $contract_sysno = $request->getParam('contract_sysno','0');

        $S = new StockshipinModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $S->getStockinByCustomerAndContractId($customer_sysno,$contract_sysno);
        if(!$list)
        {
            $list = array();
        }
        else
        {
            foreach ($list as $key => $value) {
                $list[$key]['isstoragetank'] = $value['isstoragetank']==3 ? 1 : 0;
                $day = $value['stockindate'];
                $list[$key]['isexceedfirst'] = strtotime("$day +30 day")>time() ? 0 : 1;
            }
        }
        

        echo json_encode($list);
    }

    /**
     * 导出仓储费EXCEL
     */
    public function ExcelAction() {

        $request = $this->getRequest();

        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'bar_name' => $request->getPost('bar_name',''),
            'bar_status' => $request->getPost('bar_status','-100'),
            'bar_isdel' => $request->getPost('bar_isdel','-100'),
            'bar_coststatus' => $request->getPost('bar_coststatus','-100'),
            'begin_time' => $request->getPost('begin_time',''),
            'end_time' => $request->getPost('end_time',''),
            'bar_contract' => $request->getPost('bar_contract',''),
            'page'=>false,
        );
        $F = new FinancecostModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $F->searchFinancecost($search);
        foreach ($list['list'] as $k=>$v){
            if($v['costtype'] == 0){
                $costdates = (strtotime($v['costdateend'])-strtotime($v['costdate']))/86400;
                if ($costdates < 30){
                    $list['list'][$k]['countcostdateend'] = (strtotime(date("Y-m-d")) - strtotime($v['costdate']))/86400;
                }else{
                    $list['list'][$k]['countcostdateend'] = 30;
                }
            }else{
                $list['list'][$k]['countcostdateend'] = '--';
            }
            $v['unitname'] = $v['unitname']?$v['unitname']:'吨';
            $list['list'][$k]['unitname'] = $v['unitprice'].'/'.$v['unitname'];
        }
        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("查询仓储费")
            ->setSubject("列表")
            ->setDescription("仓储费列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '费用单号'),
            array('B1:B1', 'B1', '005E9CD3', '结算期间'),
            array('C1:C1', 'C1', '005E9CD3', '合同类型'),
            array('D1:D1', 'D1', '0094CE58', '合同编号'),
            array('E1:E1', 'E1', '0094CE58', '客户'),
            array('F1:F1', 'F1', '0094CE58', '进货日期'),
            array('G1:G1', 'G1', '0094CE58', '船名'),
            array('H1:H1', 'H1', '0094CE58', '进货数量'),
            array('I1:I1', 'I1', '0094CE58', '品名'),
            array('J1:J1', 'J1', '003376B3', '费用类型'),
            array('K1:K1', 'K1', '003376B3', '计费数量'),
            array('L1:L1', 'L1', '003376B3', '计量单位'),
            array('M1:M1', 'M1', '003376B3', '计价天数'),
            array('N1:N1', 'N1', '003376B3', '金额'),
            array('O1:O1', 'O1', '003376B3', '开票状态'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('仓储费列表');

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

        foreach ($list['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['costno'];
                        break;
                    case 1:
                        $value = $item['costdate']."-".$item['costdateend'];
                        break;
                    case 2:
                        if($item['contracttype']==1) {
                            $value = '长约';
                        }elseif($item['contracttype']==2) {
                            $value = '短约';
                        }elseif($item['contracttype']==3) {
                            $value = '包罐';
                        }else{
                            $value = '包罐容';
                        }
                        break;
                    case 3:
                        $value = " ".$item['contract_no'];
                        break;
                    case 4:
                        $value = $item['customer_name'];
                        break;
                    case 5:
                        if($item['instockdate'] == null){
                            $value = '--';
                        }else{
                            $value = $item['instockdate'];
                        }
                        break;
                    case 6:
                        if($item['shipname'] == null){
                            $value = '--';
                        }else{
                            $value = $item['shipname'];
                        }
                        break;
                    case 7:
                        $value = $item['instockqty'];
                        break;
                    case 8:
                        $value = $item['goodsname'];
                        break;
                    case 9:
                        $value = $item['costname'];
                        break;
                    case 10:
                        if($item['costqty'] == null){
                            $value = '--';
                        }else{
                            $value = $item['costqty'];
                        }
                        break;
                    case 11:
                        $unitname = $item['unitname']?$item['unitname']:'吨';
                        $up = $item['unitprice'].'/'.$unitname;
                        $value = $up;
                        break;
                    case 12:
                        $value = $item['countcostdateend'];
                        break;
                    case 13:
                        $value = $item['totalprice'];
                        break;
                    case 14:
                        if ($item['coststatus']==1) {
                            $value = "未生效";
                        }else if ($item['coststatus']==2) {
                            $value = "未开票";
                        }
                        else if ($item['coststatus']==3) {
                            $value = "开票待审核";
                        }else if ($item['coststatus']==4) {
                            $value = "已开票";
                        }else if ($item['coststatus']==5) {
                            $value = "已关闭";
                        }
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="仓储费.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    /**
     * 导出杂费EXCEL
     */
    public function ExcelotherAction() {

        $request = $this->getRequest();

        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'bar_name' => $request->getPost('bar_name',''),
            'bar_status' => $request->getPost('bar_status','-100'),
            'bar_isdel' => $request->getPost('bar_isdel','-100'),
            'bar_coststatus' => $request->getPost('bar_coststatus','-100'),
            'begin_time' => $request->getPost('begin_time',''),
            'end_time' => $request->getPost('end_time',''),
            'page'=>false,
        );
        $F = new FinancecostModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $F->searchFinancecostother($search);
        foreach ($list['list'] as $k=>$v){
            if($v['costtype'] == 0){
                $costdates = (strtotime($v['costdateend'])-strtotime($v['costdate']))/86400;
                if ($costdates < 30){
                    $list['list'][$k]['countcostdateend'] = (strtotime(date("Y-m-d")) - strtotime($v['costdate']))/86400;
                }else{
                    $list['list'][$k]['countcostdateend'] = 30;
                }
            }else{
                $list['list'][$k]['countcostdateend'] = '--';
            }
            $v['unitname'] = $v['unitname']?$v['unitname']:'吨';
            $list['list'][$k]['unitname'] = $v['unitprice'].'/'.$v['unitname'];
        }
        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("查询杂费单")
            ->setSubject("列表")
            ->setDescription("杂费单列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '杂费单号'),
            array('B1:B1', 'B1', '005E9CD3', '合同类型'),
            array('C1:C1', 'C1', '005E9CD3', '合同编号'),
            array('D1:D1', 'D1', '0094CE58', '客户'),
            array('E1:E1', 'E1', '0094CE58', '进货日期'),
            array('F1:F1', 'F1', '0094CE58', '船名'),
            array('G1:G1', 'G1', '0094CE58', '进货数量（吨）'),
            array('H1:H1', 'H1', '0094CE58', '品名'),
            array('I1:I1', 'I1', '0094CE58', '费用类型'),
            array('J1:J1', 'J1', '003376B3', '计费数量'),
            array('K1:K1', 'K1', '003376B3', '计费单价'),
            array('L1:L1', 'L1', '003376B3', '计价天数'),
            array('M1:M1', 'M1', '003376B3', '金额（元）'),
            array('N1:N1', 'N1', '003376B3', '备注'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('杂费单列表');

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

        foreach ($list['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['costno'];
                        break;
                    case 1:
                        if($item['contracttype']==1) {
                            $value = '长约';
                        }elseif($item['contracttype']==2) {
                            $value = '短约';
                        }elseif($item['contracttype']==3) {
                            $value = '包罐';
                        }else{
                            $value = '包罐容';
                        }
                        break;
                    case 2:
                        $value = " ".$item['contract_no'];
                        break;
                    case 3:
                        $value = $item['customer_name'];
                        break;
                    case 4:
                        if($item['stockindate'] == null){
                            $value = '--';
                        }else{
                            $value = $item['stockindate'];
                        }
                        break;
                    case 5:
                        if($item['shipname'] == null){
                            $value = '--';
                        }else{
                            $value = $item['shipname'];
                        }
                        break;
                    case 6:
                        $value = $item['instockqty'];
                        break;
                    case 7:
                        $value = $item['goodsname'];
                        break;
                    case 8:
                        $value = $item['costname'];
                        break;
                    case 9:
                        if($item['costqty'] == null){
                            $value = '--';
                        }else{
                            $value = $item['costqty'];
                        }
                        break;
                    case 10:
                        $value = $item['unitname'];
                        break;
                    case 11:
                        $value = $item['countcostdateend'];
                        break;
                    case 12:
                        $value = $item['totalprice'];
                        break;
                    case 13:
                        $value = $item['memo'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="杂费单.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    /**
     * 导出未计算的仓储费EXCEL
     */
    public function ExcelstorageAction() {

        $request = $this->getRequest();

        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'bar_name' => $request->getPost('bar_name',''),
            'bar_status' => $request->getPost('bar_status','-100'),
            'bar_isdel' => $request->getPost('bar_isdel','-100'),
            'bar_coststatus' => $request->getPost('bar_coststatus','-100'),
            'customer_sysno' => $request->getPost('customer_sysno','-100'),
            'contract_sysno' => $request->getPost('contract_sysno',''),
            'begin_time' => $request->getPost('begin_time',''),
            'end_time' => $request->getPost('end_time',''),
            'shipname' => $request->getPost('shipname',''),
            'page'=>false,
        );
        $F = new FinancecostModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $F->searchFinancecostcalc($search);
        foreach ($list['list'] as $k=>$v){
            if($v['costtype'] == 0){
                $costdates = (strtotime($v['costdateend'])-strtotime($v['costdate']))/86400;
                if ($costdates < 30){
                    $list['list'][$k]['countcostdateend'] = (strtotime(date("Y-m-d")) - strtotime($v['costdate']))/86400;
                }else{
                    $list['list'][$k]['countcostdateend'] = 30;
                }
            }else{
                $list['list'][$k]['countcostdateend'] = '--';
            }
            $v['unitname'] = $v['unitname']?$v['unitname']:'吨';
            $list['list'][$k]['unitname'] = $v['unitprice'].'/'.$v['unitname'];
        }
        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("查询未计算仓储费")
            ->setSubject("列表")
            ->setDescription("未计算仓储费列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '费用单号'),
            array('B1:B1', 'B1', '005E9CD3', '结算期间'),
            array('C1:C1', 'C1', '005E9CD3', '合同类型'),
            array('D1:D1', 'D1', '0094CE58', '合同编号'),
            array('E1:E1', 'E1', '0094CE58', '客户'),
            array('F1:F1', 'F1', '0094CE58', '进货日期'),
            array('G1:G1', 'G1', '0094CE58', '船名'),
            array('H1:H1', 'H1', '0094CE58', '进货数量'),
            array('I1:I1', 'I1', '0094CE58', '品名'),
            array('J1:J1', 'J1', '003376B3', '费用类型'),
            array('K1:K1', 'K1', '003376B3', '计费数量'),
            array('L1:L1', 'L1', '003376B3', '计量单位'),
            array('M1:M1', 'M1', '003376B3', '计价天数'),
            array('N1:N1', 'N1', '003376B3', '实际金额'),
            array('O1:O1','O1','003376B3','预计金额'),
            array('P1:P1', 'P1', '003376B3', '开票状态'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('未计算仓储费列表');

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

        foreach ($list['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['costno'];
                        break;
                    case 1:
                        $value = $item['costdate']."-".$item['costdateend'];
                        break;
                    case 2:
                        if($item['contracttype']==1) {
                            $value = '长约';
                        }elseif($item['contracttype']==2) {
                            $value = '短约';
                        }elseif($item['contracttype']==3) {
                            $value = '包罐';
                        }else{
                            $value = '包罐容';
                        }
                        break;
                    case 3:
                        $value = " ".$item['contract_no'];
                        break;
                    case 4:
                        $value = $item['customer_name'];
                        break;
                    case 5:
                        if($item['instockdate'] == null){
                            $value = '--';
                        }else{
                            $value = $item['instockdate'];
                        }
                        break;
                    case 6:
                        if($item['shipname'] == null){
                            $value = '--';
                        }else{
                            $value = $item['shipname'];
                        }
                        break;
                    case 7:
                        $value = $item['instockqty'];
                        break;
                    case 8:
                        $value = $item['goodsname'];
                        break;
                    case 9:
                        $value = $item['costname'];
                        break;
                    case 10:
                        if($item['costqty'] == null){
                            $value = '--';
                        }else{
                            $value = $item['costqty'];
                        }
                        break;
                    case 11:
//                        $unitname = $item['unitname']?$item['unitname']:'吨';
                        $up = $item['unitname'];
                        $value = $up;
                        break;
                    case 12:
                        $value = $item['countcostdateend'];
                        break;
                    case 13:
                        $value = $item['totalprice'];
                        break;
                    case 14:
                        $value = $item['oldtotalprice'];
                        break;
                    case 15:
                        if ($item['coststatus']==1) {
                            $value = "未生效";
                        }else if ($item['coststatus']==2) {
                            $value = "未开票";
                        }
                        else if ($item['coststatus']==3) {
                            $value = "开票待审核";
                        }else if ($item['coststatus']==4) {
                            $value = "已开票";
                        }else if ($item['coststatus']==5) {
                            $value = "已关闭";
                        }
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="未计算仓储费.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    /**
     * 导出装卸费EXCEL
     */
    public function ExcelloadAction() {

        $request = $this->getRequest();

        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'bar_name' => $request->getPost('bar_name',''),
            'bar_status' => $request->getPost('bar_status','-100'),
            'bar_isdel' => $request->getPost('bar_isdel','-100'),
            'bar_coststatus' => $request->getPost('bar_coststatus','-100'),
            'begin_time' => $request->getPost('begin_time',''),
            'end_time' => $request->getPost('end_time',''),
            'bar_contract' => $request->getPost('bar_contract',''),
            'bar_berthtype' => $request->getPost('bar_berthtype',''),
            'page'=>false,
        );
        $F = new FinancecostModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $F->searchFinancecostload($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("查询装卸费")
            ->setSubject("列表")
            ->setDescription("装卸费列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '费用单号'),
            array('B1:B1', 'B1', '005E9CD3', '结算期间'),
            array('C1:C1', 'C1', '0094CE58', '合同编号'),
            array('D1:D1', 'D1', '0094CE58', '品名'),
            array('E1:E1', 'E1', '0094CE58', '客户'),
            array('F1:F1', 'F1', '0094CE58', '收费类型'),
            array('G1:G1', 'G1', '003376B3', '收费金额'),
            array('H1:H1', 'H1', '003376B3', '开票状态'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('仓储费列表');

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

        foreach ($list['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['costno'];
                        break;
                    case 1:
                        $value = $item['costdate']."-".$item['costdateend'];
                        break;
                    case 2:
                        $value = " ".$item['contract_no'];
                        break;
                    case 3:
                        $value = $item['goodsname'];
                        break;
                    case 4:
                        $value = $item['customer_name'];
                        break;
                    case 5:
                        if ($item['berthtype']==1) {
                            $value = "装货";
                        }else if ($item['berthtype']==2) {
                            $value = "卸货";
                        }
                        break;
                    case 6:
                        $value = $item['totalprice'];
                        break;
                    case 7:
                        if ($item['coststatus']==1) {
                            $value = "未生效";
                        }else if ($item['coststatus']==2) {
                            $value = "未开票";
                        }
                        else if ($item['coststatus']==3) {
                            $value = "开票待审核";
                        }else if ($item['coststatus']==4) {
                            $value = "已开票";
                        }else if ($item['coststatus']==5) {
                            $value = "已关闭";
                        }
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="装卸费.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function statuschangeAction(){
        $request = $this->getRequest();
        $date = $request->getPost('date','');
        $ids = implode(',',$date);
        $C = new FinancecostModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $parms = array(
            'ids'=>$ids,
            'iscalc'=>1,//启用
            'coststatus'=>2,//变成待开票
        );

        $res = $C->updateStatus($parms);
        if($res){
            COMMON::result('200', '执行成功');
        }else{
            COMMON::result('300','执行失败');
        }
    }

    #手动仓储费
    public function addFinancecostbyplanAction()
    {
        return false;
        $S = new FinancecostModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $S->addFinancecostByPlan();
        COMMON::result(200,'执行成功');
        // $list = array();
        // echo json_encode($list);
    }

    #修改费用
    public function editdetailAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $F=new FinancecostModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $params=$F->getFinancecosteditdetailById($id);
        $this->getView()->make('financecost.financecosteditdetail',$params);
    }

    public function editfinancecostAction()
    {
        $request = $this->getRequest();
        $data=$request->getPost('data', '0');
        $F=new FinancecostModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $res=$F->updatedetailFinancecost($data);
        if($res){
            COMMON::result('200');
        }else{
            COMMON::result('300');
        }
    }

}
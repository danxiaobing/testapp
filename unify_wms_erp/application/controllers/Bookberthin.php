<?php

/**
 * Created by PhpStorm.
 * User: 129
 * Date: 2017/7/6
 * Time: 10:08
 */
class BookberthinController extends Yaf_Controller_Abstract
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

    /**
     * 靠泊装列表
     */
    public function listAction()
    {
        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status'=>1,
            'page'=>false
        );
        $customerdata = $C->searchCustomer($search);
        $params['customerlist'] = $customerdata['list'];
        $this->getView()->make('bookberthin.bookberthinlist', $params);
    }

    /*
     * 获取列表数据
     */
    public function listJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'bar_no' => $request->getPost('bar_no', ''),
            'docsource'=>$request->getPost('docsource', ''),
            'customer_sysno'=>$request->getPost('customer_sysno', ''),
            'inshipname'=>$request->getPost('inshipname', ''),
            'bar_bookinstatus' => $request->getPost('bar_bookinstatus', '-100'),
            'bar_stockintype' => 4,
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );
        $B = new BookberthinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $B->searchBookberthin($search);

        echo json_encode($list);

    }

    /**
     * 靠泊装编辑
     */
    public function editAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $mode = $request->getParam('mode','');

        $S = new BookberthinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if (!$id) {
            $action = "/bookberthin/newJson/";
            $params = array();
        } else {
            $params = $S->getbookberthinById($id);
            $status = $params['bookinginstatus'];

            if($mode == 'edit'){
                if($status == 2||$status == 7){
                    $action = '/bookberthin/editJson';
                }
                else{
                    COMMON::result(300, '非暂存或退回状态不能编辑！');
                    return;
                }
            }elseif($mode == 'audit'){
                $action = '/bookberthin/auditJson';
            }elseif($mode == 'back'){
                $action = '/bookberthin/backbookberthin';
            }elseif($mode == 'addattach'){
                $action = '/bookberthin/addcontractattach';
            }
            //暂存状态 编辑
            $params['attach'] = array();
            $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $attach = $A->getAttachByMAS('bookberthin', 'bookberthinatt', $id);
            $params['attach'] = array_merge($params['attach'], $attach);

            if (is_array($attach) && count($attach)) {
                $files1 = array();
                foreach ($attach as $file) {
                    $files1[] = $file['sysno'];
                }
                $params['uploaded1'] = join(',', $files1);
            }
        }

        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'contractstatus' => 5,
            'contractenddate' => 'NOW()',
            'page' => false,
            'bar_status' => 1
        );
        $list = $C->searchCustomerContract($search);
        $params['customerlist'] = $list;

        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];

        $params['id'] = $id;
        $params['action'] = $action;
        $params['mode'] = $mode;
        $this->getView()->make('bookberthin.bookberthinedit', $params);
    }

    /*
     * 新建靠泊装货预约单
     */
    public function newJsonAction()
    {
        $request = $this->getRequest();
        $bookinginstatus = $request->getPost('bookinginstatus', '');
        $bookberthindetaildata = $request->getPost('bookberthindetaildata', "");
        $bookberthindetaildata = json_decode($bookberthindetaildata, true);
        if (count($bookberthindetaildata) == 0) {
            COMMON::result(300, '装卸货品明细信息不能为空');
            return;
        }

        for($i = 1;$i<count($bookberthindetaildata);$i++){
            if($bookberthindetaildata[$i]['goods_sysno'] != $bookberthindetaildata[1]['goods_sysno']){
                COMMON::result(300, '一个预约单只能添加一种货品');
                return;
            }
        }

        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $contract_sysno = $request->getPost('contract_sysno', '');
        foreach($bookberthindetaildata as $item){
            $goods_sysno = $item['goods_sysno'];
            $bercost = $C ->getberthcost($contract_sysno,$goods_sysno);
            if($bercost['berthcost']==''){
                COMMON::result(300, '靠泊装卸合同的装货中没有签订'.$item['goodsname']);
                return;
            }
        }

        $B = new BookberthinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $contracttypedata = $C->getContractById($request->getPost('contract_sysno', '0'));
        if ($contracttypedata) {
            $contracttype = $contracttypedata['contracttype'];
        }

        $input = array(
            'stockintype' => 4,
            'bookinginno' => COMMON::getCodeId('X1'),
            'bookingindate' => $request->getPost('bookingindate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customer_name' => $request->getPost('obj_customer_name', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contract_no' => $request->getPost('contract_no', ''),
            'contracttype' => $contracttype,
            'docsource' => $request->getPost('docsource', ''),
            'inshipname' => $request->getPost('inshipname', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'issave' => $request->getPost('issave', '1'),
            'bookinginstatus' => $request->getPost('bookinginstatus', '2'),
            'isberthorder' => $request->getPost('isberthorder', '1'),
            'ispipelineorder' => $request->getPost('ispipelineorder', '1'),
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        $L = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $shipname_msg = $L->Insertshipname($input['inshipname']);
        if($shipname_msg['code']==300){
            COMMON::result(300,$shipname_msg['message']);
            return;
        }

        if ($id = $B->addBookberthin($input, $bookberthindetaildata,$bookinginstatus)) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $B->getBookberthinById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    /*
     * 编辑靠泊装货预约单明细
     */
    public function bookberthindetaileditAction(){
        $request = $this->getRequest();
        $cuid = $request->getParam('cuid', '0');
        $coid = $request->getParam('coid', '0');
        $handlestatus = $request->getParam('handlestatus','0');
        $mode = $request ->getPost('mode','');

        $params = $request->getPost('selectedDatasArray',array());

        $S = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params['storagetanklist'] = $S->getStoragetank2();

        $search = array(
            'customer_sysno' => $cuid,
            'contract_sysno' => $coid,
            'page' => false,
            'orders' => $request->getPost('orders', ''),

        );

        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $quality->getList($search, 99, 1);
        $params['goodsqualitylist'] = $list['list'];

        $params['customer_sysno'] = $cuid;
        $params['contract_sysno'] = $coid;
        $params['handlestatus'] = $handlestatus;
        $params['mode'] = $mode;

        $this->getView()->make('bookberthin.bookberthindetail', $params);
    }

    /*
     *获取靠泊装货预约单明细数据
     */
    public function adddetailJsonAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $B = new BookberthinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $B->getBookberthindetailById($id);
        echo json_encode($list);
    }

    /*
     * 编辑靠泊装货预约单
     */
    public function editJsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $bookinginstatus = $request->getPost('bookinginstatus', '');
        $bookberthindetaildata = $request->getPost('bookberthindetaildata', "");
        $bookberthindetaildata = json_decode($bookberthindetaildata, true);
        if (count($bookberthindetaildata) == 0) {
            COMMON::result(300, '装卸货品明细不能为空');
            return;
        }
        for($i = 1;$i<count($bookberthindetaildata);$i++){
            if($bookberthindetaildata[$i]['goods_sysno'] != $bookberthindetaildata[1]['goods_sysno']){
                COMMON::result(300, '一个预约单只能添加一种货品');
                return;
            }
        }

        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $contract_sysno = $request->getPost('contract_sysno', '');
        foreach($bookberthindetaildata as $item){
            $goods_sysno = $item['goods_sysno'];
            $bercost = $C ->getberthcost($contract_sysno,$goods_sysno);
            if($bercost['berthcost']==''){
                COMMON::result(300, '靠泊装卸合同的装货中没有签订'.$item['goodsname']);
                return;
            }
        }

        $S = new BookberthinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $contracttypedata = $C->getContractById($request->getPost('contract_sysno', '0'));
        if ($contracttypedata) {
            $contracttype = $contracttypedata['contracttype'];
        }

        $input = array(
            'stockintype' => 4,
            'bookinginno' => $request->getPost('bookinginno', ''),
            'bookingindate' => $request->getPost('bookingindate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customer_name' => $request->getPost('obj_customer_name', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contract_no' => $request->getPost('contract_no', ''),
            'contracttype' => $contracttype,
            'docsource' => $request->getPost('docsource', ''),
            'inshipname' => $request->getPost('inshipname', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'issave' => $request->getPost('issave', '1'),
            'bookinginstatus' => $request->getPost('bookinginstatus', '2'),
            'isberthorder' => $request->getPost('isberthorder', '1'),
            'ispipelineorder' => $request->getPost('ispipelineorder', '1'),
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'updated_at' => '=NOW()'
        );

        $L = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $shipname_msg = $L->Insertshipname($input['inshipname']);
        if($shipname_msg['code']==300){
            COMMON::result(300,$shipname_msg['message']);
            return;
        }

        $bookberthininfo = $S->getbookberthinById($id);
        if($bookberthininfo['bookinginstatus']==7){
            $input['bookinginstatus'] = 7;
        }
        if ($S->updatebookberthin($id, $input, $bookberthindetaildata,$bookinginstatus)) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $S->getbookberthinById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    /*
     * 审核靠泊装货预约单
     */
    public function auditJsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $bookinginstatus = $request->getPost('bookinginstatus', '');
        $auditreason = $request->getPost('auditreason', '');
        $bookberthindetaildata = $request->getPost('bookberthindetaildata', "");
        $bookberthindetaildata = json_decode($bookberthindetaildata, true);

        $B = new BookberthinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if ($bookinginstatus == 6) {
            if ($auditreason == '') {
                COMMON::result(300, '审核备注不能为空');
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

        if ($B->auditBookberthin($id, $input,$bookberthindetaildata)) {

            if ($bookinginstatus == 4){
                //生成靠泊装货订单
                $S = new StockberthinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $input = array(
                    'stockintype' => 4,
                    'stockinno' => $request->getPost('bookinginno', ''),
                    'stockindate' => date('Y-m-d',time()),
                    'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
                    'customername' => $request->getPost('obj_customer_name', ''),
                    'booking_in_sysno' => $id,
                    'bookingin_no' => $request->getPost('bookinginno', ''),
                    'contract_sysno' => $request->getPost('contract_sysno', ''),
                    'contractno' => $request->getPost('contract_no', ''),
                    'docsource'=>$request->getPost('docsource', ''),
                    'inshipname' => $request->getPost('inshipname', ''),
                    'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
                    'cs_employeename' => $request->getPost('cs_employeename', ''),
                    'isberthorder' => $request->getPost('isberthorder', '1'),
                    'ispipelineorder' => $request->getPost('ispipelineorder', '1'),
                    'stockinstatus' => 2,
                    'status' => $request->getPost('status', '1'),
                    'isdel' => $request->getPost('isdel', '0'),
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()'
                );

                $res = $S->addStockberthin($input, $bookberthindetaildata,2);
                if (!$res) {
                    COMMON::result(300, '生成入库订单失败');
                    return;
                }
            }
            $row = $B->getBookberthinById($id);
            COMMON::result(200, '审核成功', $row);
        } else {
            COMMON::result(300, '审核失败');
        }
    }

    /*
     * 靠泊装货预约单上传附件
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

    /*
     * 靠泊装货预约单删除
     */
    public function deljsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);
        $B = new BookberthinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $B->getBookberthinById($id);

        if ($params['docsource'] == 2) {
            COMMON::result(300, '云仓数据不能删除！');
            return;
        }

        $status = $params['bookinginstatus'];

        if ($status != 7 && $status != 2) {
            COMMON::result(300, '该状态无法删除！');
            return;
        }
        $input = array(
            'isdel' => 1
        );
        if ($B->delBookberthin($id, $input)) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败');
        }
    }

    /*
     * 靠泊装货预约单导出excel
     */
    public function excelAction(){
        $request = $this->getRequest();
        $search = array(
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'bar_stockintype' => 4,
            'bar_bookinginstatus' => $request->getPost('bar_bookinginstatus', '-100'),
            'page' => false,
        );

        $B = new BookberthinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $bookcarindata = $B->searchBookberthin($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("当前靠泊装货预约列表")
            ->setSubject("列表")
            ->setDescription("当前靠泊装货预约列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '预约单号'),
            array('B1:B1', 'B1', '005E9CD3', '单据来源'),
            array('C1:C1', 'C1', '005E9CD3', '客户'),
            array('D1:D1', 'D1', '005E9CD3', '合同编号'),
            array('E1:E1', 'E1', '0094CE58', '货品名称'),
            array('F1:F1', 'F1', '005E9CD3', '预约日期'),
            array('G1:G1', 'G1', '0094CE58', '预计数量（吨）'),
            array('H1:H1', 'H1', '0094CE58', '客服'),
            array('I1:I1', 'I1', '0094CE58', '单据状态'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('当前靠泊装货预约列表');

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

        foreach ($bookcarindata['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['bookinginno'];
                        break;
                    case 1:
                        if($item['docsource']==1){
                            $value = '手工创建';
                        }elseif($item['docsource']==2){
                            $value = '国烨云仓';
                        }
                        break;
                    case 2:
                        $value = $item['customer_name'];
                        break;
                    case 3:
                        $value = $item['contract_no'];
                        break;
                    case 4:
                        $value = $item['goodsname'];
                        break;
                    case 5:
                        $value = $item['bookingindate'];
                        break;
                    case 6:
                        $value = $item['bookinginqty'];
                        break;
                    case 7:
                        if ($item['cs_employeename'] == '请选择'){
                            $value = '--';
                        }else{
                            $value = $item['cs_employeename'];
                        }
                        break;
                    case 8:
                        if ($item['bookinginstatus']==2) {
                            $value = "暂存";
                        }
                        else if ($item['bookinginstatus']==3) {
                            $value = "待确认";
                        }else if ($item['bookinginstatus']==4) {
                            $value = "待审核";
                        }else if ($item['bookinginstatus']==5) {
                            $value = "已审核";
                        }else if ($item['bookinginstatus']==6) {
                            $value = "已完成";
                        }else if ($item['bookinginstatus']==7) {
                            $value = "退回";
                        }else{
                            $value = "新建";
                        }
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="靠泊装货预约单.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

}
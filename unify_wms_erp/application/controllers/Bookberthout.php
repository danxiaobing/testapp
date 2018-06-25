<?php

/**
 * Created by PhpStorm.
 * User: 129
 * Date: 2017/7/6
 * Time: 10:56
 */
class BookberthoutController extends Yaf_Controller_Abstract
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
     * 靠泊卸列表
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
        $this->getView()->make('bookberthout.bookberthoutlist', $params);
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
            'bar_bookberthoutstatus' => $request->getPost('bar_bookberthoutstatus', '-100'),
            'bar_stockouttype' => 4,
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );
        $B = new BookberthoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $B->searchBookberthout($search);
        echo json_encode($list);

    }

    /**
     * 靠泊卸货编辑
     */
    public function editAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $mode = $request->getParam('mode','');

        $B = new BookberthoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if (!$id) {
            $action = "/bookberthout/newJson/";
            $params = array();
        } else {
            $params = $B->getBookberthoutById($id);
            $status = $params['bookingoutstatus'];

            if($mode == 'edit'){
                if($status == 2||$status == 7){
                    $action = '/bookberthout/editJson';
                }
                else{
                    COMMON::result(300, '非暂存或退回状态不能编辑！');
                    return;
                }
            }elseif($mode == 'sure'){
                $action = '/bookberthout/surebookcarin';
            }elseif($mode == 'audit'){
                $action = '/bookberthout/auditJson';
            }elseif($mode == 'back'){
                $action = '/bookberthout/backbookcarin';
            }elseif($mode == 'addattach'){
                $action = '/bookberthout/addcontractattach';
            }elseif($mode =='addcar'){
                $action = "/bookberthout/addcarjson/";
            }

            //暂存状态 编辑
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
        $this->getView()->make('bookberthout.bookberthoutedit', $params);
    }

    /*
     * 新建靠泊卸货预约单
     */
    public function newJsonAction()
    {
        $request = $this->getRequest();
        $bookingoutstatus = $request->getPost('bookingoutstatus', '');
        $bookberthoutdetaildata = $request->getPost('bookberthoutdetaildata', "");
        $bookberthoutdetaildata = json_decode($bookberthoutdetaildata, true);
        if (count($bookberthoutdetaildata) == 0) {
            COMMON::result(300, '靠泊卸货明细信息不能为空');
            return;
        }
        for($i = 1;$i<count($bookberthoutdetaildata);$i++){
            if($bookberthoutdetaildata[$i]['goods_sysno'] != $bookberthoutdetaildata[1]['goods_sysno']){
                COMMON::result(300, '一个预约单只能添加一种货品');
                return;
            }
        }

        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $contract_sysno = $request->getPost('contract_sysno', '');
        foreach($bookberthoutdetaildata as $item){
            $goods_sysno = $item['goods_sysno'];
            $bercost = $C ->getberthcost($contract_sysno,$goods_sysno);
            if($bercost['berthcostdomestic']==''&&$bercost['berthcostforeign']==''){
                COMMON::result(300, '靠泊装卸合同的卸货中没有签订'.$item['goodsname']);
                return;
            }
        }

        $B = new BookberthoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'stockouttype' => 4,
            'bookingoutno' => COMMON::getCodeId('X2'),
            'bookingoutdate' => $request->getPost('bookingoutdate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customer_name' => $request->getPost('obj_customer_name', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contractno' => $request->getPost('contractno', ''),
            'docsource' => $request->getPost('docsource', ''),
            'inshipname' => $request->getPost('inshipname', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'bookingoutstatus' => $request->getPost('bookingoutstatus', '2'),
            'isberthorder' => $request->getPost('isberthorder', ''),
            'ispipelineorder' => $request->getPost('ispipelineorder', ''),
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

        if ($id = $B->addBookberthout($input, $bookberthoutdetaildata,$bookingoutstatus)) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $B->getBookberthoutById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    /*
     * 编辑靠泊卸货预约单明细
     */
    public function bookberthoutdetaileditAction(){
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

        $this->getView()->make('bookberthout.bookberthoutdetail', $params);
    }

    /*
     * 获取靠泊卸货预约单明细数据
     */
    public function adddetailJsonAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        if($id){
            $B = new BookberthoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
            $list = $B->getBookberthoutdetailById($id);
        }else{
            $list = array();
        }
        echo json_encode($list);
    }

    /*
     * 编辑靠泊卸货预约单
     */
    public function editJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $bookingoutstatus = $request->getPost('bookingoutstatus', '');
        $bookberthoutdetaildata = $request->getPost('bookberthoutdetaildata', "");
        $bookberthoutdetaildata = json_decode($bookberthoutdetaildata, true);
        if (count($bookberthoutdetaildata) == 0) {
            COMMON::result(300, '装卸货品明细不能为空');
            return;
        }
        for($i = 1;$i<count($bookberthoutdetaildata);$i++){
            if($bookberthoutdetaildata[$i]['goods_sysno'] != $bookberthoutdetaildata[1]['goods_sysno']){
                COMMON::result(300, '一个预约单只能添加一种货品');
                return;
            }
        }

        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $contract_sysno = $request->getPost('contract_sysno', '');
        foreach($bookberthoutdetaildata as $item){
            $goods_sysno = $item['goods_sysno'];
            $bercost = $C ->getberthcost($contract_sysno,$goods_sysno);
            if($bercost['berthcostdomestic']==''&&$bercost['berthcostforeign']==''){
                COMMON::result(300, '靠泊装卸合同的卸货中没有签订'.$item['goodsname']);
                return;
            }
        }

        $S = new BookberthoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'stockouttype' => 4,
            'bookingoutno' => $request->getPost('bookingoutno', ''),
            'bookingoutdate' => $request->getPost('bookingoutdate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customer_name' => $request->getPost('obj_customer_name', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contractno' => $request->getPost('contractno', ''),
            'docsource' => $request->getPost('docsource', ''),
            'inshipname' => $request->getPost('inshipname', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'bookingoutstatus' => $request->getPost('bookingoutstatus', '2'),
            'isberthorder' => $request->getPost('isberthorder', ''),
            'ispipelineorder' => $request->getPost('ispipelineorder', ''),
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

        $bookberthininfo = $S->getbookberthoutById($id);
        if($bookberthininfo['bookinginstatus']==7){
            $input['bookinginstatus'] = 7;
        }

        if ($S->updatebookberthout($id, $input, $bookberthoutdetaildata,$bookingoutstatus)) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $S->getbookberthoutById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    /*
     * 审核靠泊卸货预约单
     */
    public function auditJsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $bookingoutstatus = $request->getPost('bookingoutstatus', '');
        $bookberthoutdetaildata = $request->getPost('bookberthoutdetaildata', "");
        $bookberthoutdetaildata = json_decode($bookberthoutdetaildata, true);
        $auditreason = $request->getPost('auditreason', '');

        $B = new BookberthoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if ($bookingoutstatus == 6) {
            if ($auditreason == '') {
                COMMON::result(300, '审核备注不能为空');
                return;
            }
            $status = 7;
        } elseif ($bookingoutstatus == 4) {
            $status = 5;
        }

        $input = array(
            'ispipelineorder'=>$request->getPost('ispipelineorder',''),
            'isberthorder'=>$request->getPost('isberthorder',''),
            'bookingoutdate'=>$request->getPost('bookingoutdate',''),
            'bookingoutno'=>$request->getPost('bookingoutno',''),
            'bookingoutstatus' => $status,
            'auditreason' => $auditreason,
            'updated_at' => '=NOW()'
        );
        $attach = $request->getPost('attachment', array());
        if (count($attach) > 0) {
            $res = $A->addAttachModelSysno($id, $attach);
            if (!$res) {
                COMMON::result(300, '添加附件失败');
                return;
            }
        }

        if ($B->auditBookberthout($id, $input)) {
            //生成靠泊卸货订单
            if ($bookingoutstatus == 4){

                $S = new StockberthoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $input = array(
                    'stockouttype' => 4,
                    'stockoutno' => $request->getPost('bookingoutno', ''),
                    'stockoutdate' => date('Y-m-d',time()),
                    'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
                    'customername' => $request->getPost('obj_customer_name', ''),
                    'booking_out_sysno' => $id,
                    'bookingout_no' => $request->getPost('bookingoutno', ''),
                    'contract_sysno' => $request->getPost('contract_sysno', ''),
                    'contractno' => $request->getPost('contractno', ''),
                    'docsource'=>$request->getPost('docsource', ''),
                    'inshipname' => $request->getPost('inshipname', ''),
                    'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
                    'cs_employeename' => $request->getPost('cs_employeename', ''),
                    'isberthorder' => $request->getPost('isberthorder', '1'),
                    'ispipelineorder' => $request->getPost('ispipelineorder', '1'),
                    'stockoutstatus' => 2,
                    'status' => $request->getPost('status', '1'),
                    'isdel' => $request->getPost('isdel', '0'),
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()'
                );
                $res = $S->addStockberthout($id,$input, $bookberthoutdetaildata,2);

                if (!$res) {
                    COMMON::result(300, '生成靠泊卸货订单失败');
                    return;
                }
            }

            $row = $B->getBookberthoutById($id);
            COMMON::result(200, '审核成功', $row);
        } else {
            COMMON::result(300, '审核失败');
        }
    }

    /*
     * 靠泊卸货预约单上传附件
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
        $B = new BookberthoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $B->getBookberthoutById($id);

        if ($params['docsource'] == 2) {
            COMMON::result(300, '云仓数据不能删除！');
            return;
        }

        $status = $params['bookingoutstatus'];

        if ($status != 7 && $status != 2) {
            COMMON::result(300, '该状态无法删除！');
            return;
        }
        $input = array(
            'isdel' => 1
        );
        if ($B->delBookberthout($id, $input)) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败');
        }
    }

    /*
     * 靠泊卸货预约单导出excel
     */
    public function excelAction(){
        $request = $this->getRequest();
        $search = array(
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'bar_stockouttype' => 4,
            'bar_bookingoutstatus' => $request->getPost('bar_bookingoutstatus', '-100'),
            'page' => false,
        );

        $B = new BookberthoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $bookcarindata = $B->searchBookberthout($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("当前靠泊卸货预约列表")
            ->setSubject("列表")
            ->setDescription("当前靠泊卸货预约列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '预约单号'),
            array('B1:B1', 'B1', '005E9CD3', '单据来源'),
            array('C1:C1', 'C1', '005E9CD3', '客户'),
            array('D1:D1', 'D1', '005E9CD3', '合同编号'),
            array('E1:E1', 'E1', '0094CE58', '货品名称'),
            array('F1:F1', 'F1', '005E9CD3', '货品性质'),
            array('G1:G1', 'G1', '0094CE58', '预约日期'),
            array('H1:H1', 'H1', '0094CE58', '预计数量（吨）'),
            array('I1:I1', 'I1', '0094CE58', '客服'),
            array('J1:J1', 'J1', '005E9CD3', '单据状态'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('当前靠泊卸货预约列表');

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
                        $value = $item['bookingoutno'];
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
                        $value = $item['contractno'];
                        break;
                    case 4:
                        $value = $item['goodsname'];
                        break;
                    case 5:
                        if ($item['goodsnature']==1) {
                            $value = "保税";
                        }
                        else if ($item['goodsnature']==2) {
                            $value = "外贸";
                        }else if ($item['goodsnature']==3) {
                            $value = "内贸转出口";
                        }else if ($item['goodsnature']==4) {
                            $value = "内贸内销";
                        }
                        break;
                    case 6:
                        $value = $item['bookingoutdate'];
                        break;
                    case 7:
                        $value = $item['bookingoutqty'];
                        break;
                    case 8:
                        if($item['cs_employeename'] == '请选择'){
                            $value = '--';
                        }else{
                            $value = $item['cs_employeename'];
                        }
                        break;
                    case 9:
                        if ($item['bookingoutstatus']==2) {
                            $value = "暂存";
                        }
                        else if ($item['bookingoutstatus']==3) {
                            $value = "待确认";
                        }else if ($item['bookingoutstatus']==4) {
                            $value = "待审核";
                        }else if ($item['bookingoutstatus']==5) {
                            $value = "已审核";
                        }else if ($item['bookingoutstatus']==6) {
                            $value = "已完成";
                        }else if ($item['bookingoutstatus']==7) {
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
        header('Content-Disposition: attachment;filename="靠泊卸货预约单.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

}
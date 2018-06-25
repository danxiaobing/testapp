<?php

/**
 * Created by PhpStorm.
 * User: 129
 * Date: 2017/7/6
 * Time: 15:38
 */
class StockberthinController extends Yaf_Controller_Abstract
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
        $this->getView()->make('stockberthin.stockberthinlist', $params);
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
            'bar_stockinstatus' => $request->getPost('bar_stockinstatus', '-100'),
            'bar_stockintype' => 4,
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );
        $S = new StockberthinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStockberthin($search);
        echo json_encode($list);

    }

    /**
     * 靠泊装货编辑
     */
    public function editAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $mode = $request->getParam('mode','');
        $S = new StockberthinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if (!$id) {
            $action = "/stockberthin/newJson/";
            $params = array();
        } else {
            $params = $S->getstockberthinById($id);
            $status = $params['stockinstatus'];

            if($mode == 'edit'){
                if($status == 2||$status == 6){
                    $action = '/stockberthin/editJson';
                }
                else{
                    COMMON::result(300, '非暂存或退回状态不能编辑！');
                    return;
                }
            }elseif($mode == 'audit'){
                $action = '/stockberthin/auditJson';
            }elseif($mode == 'back'){
                $action = '/stockberthin/backstockberthin';
            }elseif($mode == 'blank'){
                $action = '/stockberthin/blankstockberthin';
            }elseif($mode == 'addattach'){
                $action = '/stockberthin/addcontractattach';
            }
            //暂存状态 编辑
            $params['attach'] = array();
            $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $attach = $A->getAttachByMAS('stockberthin', 'stockberthinatt', $id);
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

        $this->getView()->make('stockberthin.stockberthinedit', $params);
    }

    /*
     * 获取靠泊装货明细数据
     */
    public function adddetailJsonAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $S = new StockberthinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getStockberthindetailById($id);
        echo json_encode($list);
    }

    /*
     * 获取管线明细数据
     */
    public function addpipdetailJsonAction(){
        $request = $this->getRequest();
        $id = $request->getParam('bookid', '0');
        $S = new StockberthinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getpipdetailByBookingId($id);
        echo json_encode($list);
    }

    /*
     * 获取管线明细数据
     */
    public function addberdetailJsonAction(){
        $request = $this->getRequest();
        $id = $request->getParam('bookid', '0');
        $S = new StockberthinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getberdetailByBookingId($id);
        echo json_encode($list);
    }

    /*
     * 靠泊装货明细视图
     */
    public function stockberthindetaileditAction(){
        $request = $this->getRequest();
        $cuid = $request->getParam('cuid', '0');
        $coid = $request->getParam('coid', '0');
        $handlestatus = $request->getParam('handlestatus','0');
        $mode = $request ->getPost('mode','');
        $params = $request->getPost('selectedDatasArray',array());

        $params['customer_sysno'] = $cuid;
        $params['contract_sysno'] = $coid;
        $params['handlestatus'] = $handlestatus;
        $params['mode'] = $mode;

        $this->getView()->make('stockberthin.stockberthindetail', $params);
    }

    /*
     * 编辑靠泊装货
     */
    public function editJsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockinstatus = $request->getPost('stockinstatus', '');
        $stockberthindetaildata = $request->getPost('stockberthindetaildata', "");
        $stockberthindetaildata = json_decode($stockberthindetaildata, true);
        if (count($stockberthindetaildata) == 0) {
            COMMON::result(300, '装卸货品明细不能为空');
            return;
        }
        $count = 0;
        foreach($stockberthindetaildata as $item){
            $count = $count + $item['beqty'];
        }
        if($count==0){
            COMMON::result(300, '装卸货品实际数量不能为0');
            return;
        }

        $S = new StockberthinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $contracttypedata = $C->getContractById($request->getPost('contract_sysno', '0'));
        if ($contracttypedata) {
            $contracttype = $contracttypedata['contracttype'];
        }

        $input = array(
            'stockintype' => 4,
            'stockinno' => $request->getPost('stockinno', ''),
            'stockindate' => date('Y-m-d',time()),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customername' => $request->getPost('obj_customer_name', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contractno' => $request->getPost('contractno', ''),
            'docsource'=>$request->getPost('docsource', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'stockinstatus' => 2,
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'updated_at' => '=NOW()'
        );

        $stockberthininfo = $S->getstockberthinById($id);
        if($stockberthininfo['stockinstatus']==7){
            $input['stockinstatus'] = 7;
        }

        if ($S->updatestockberthin($id, $input, $stockberthindetaildata,$stockinstatus)) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $S->getstockberthinById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    /*
     * 审核靠泊装货订单
     */
    public function auditJsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockinstatus = $request->getPost('stockinstatus', '');
        $auditreason = $request->getPost('auditreason', '');
        $stockberthindetaildata = $request->getPost('stockberthindetaildata', "");
        $stockberthindetaildata = json_decode($stockberthindetaildata, true);

        $S = new StockberthinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if ($stockinstatus == 6) {
            if ($auditreason == '') {
                COMMON::result(300, '审核备注不能为空');
                return;
            }
            $status = 6;
        } elseif ($stockinstatus == 4) {
            $status = 4;
        }

        $input = array(
            'stockinstatus' => $status,
            'stockindate' => $request->getPost('stockindate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customername' => $request->getPost('obj_customer_name', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contractno' => $request->getPost('contractno', ''),
            'auditreason' => $auditreason,
            'updated_at' => '=NOW()'
        );

        if ($S->auditStockberthin($id, $input,$stockberthindetaildata)) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $S->getStockberthinById($id);
            COMMON::result(200, '审核成功', $row);
        } else {
            COMMON::result(300, '审核失败');
        }
    }

    /*
     * 退回靠泊装货订单
     */
    public function backstockberthinAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $backreason = $request->getPost('backreason', '');

        $S = new StockberthinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if ($backreason == '') {
            COMMON::result(300, '退回备注不能为空');
            return;
        }

        $input = array(
            'isdel' => 1,
            'updated_at' => '=NOW()'
        );

        if ($S->backStockberthin($id, $input,$backreason)) {
            COMMON::result(200, '退回成功');
        } else {
            COMMON::result(300, '退回失败');
        }
    }

    /*
     *作废靠泊装货订单
     */
    public function blankstockberthinAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $abandonreason = $request->getPost('abandonreason', '');

        $S = new StockberthinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if ($abandonreason == '') {
            COMMON::result(300, '作废备注不能为空');
            return;
        }

        $input = array(
            'stockinstatus' => 5,
            'auditreason' => $abandonreason,
            'updated_at' => '=NOW()'
        );

        if ($S->blankStockberthin($id, $input)) {
            $row = $S->getStockberthinById($id);
            COMMON::result(200, '作废成功', $row);
        } else {
            COMMON::result(300, '作废失败');
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
            'bar_stockinstatus' => $request->getPost('bar_stockinstatus', '-100'),
            'page' => false,
        );

        $S = new StockberthinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $stockcarindata = $S->searchStockberthin($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("当前靠泊装货订单列表")
            ->setSubject("列表")
            ->setDescription("当前靠泊装货订列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '靠泊装卸单号'),
            array('B1:B1', 'B1', '005E9CD3', '客户'),
            array('C1:C1', 'C1', '005E9CD3', '货品名称'),
            array('D1:D1', 'D1', '005E9CD3', '货品性质'),
            array('E1:E1', 'E1', '005E9CD3', '通知数量（吨）'),
            array('F1:F1', 'F1', '0094CE58', '实际流量（吨）'),
            array('G1:G1', 'G1', '005E9CD3', '客服'),
            array('H1:H1', 'H1', '0094CE58', '单据状态'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('当前靠泊装货订单列表');

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

        foreach ($stockcarindata['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['stockinno'];
                        break;
                    case 1:
                        $value = $item['customername'];
                        break;
                    case 2:
                        $value = $item['goodsname'];
                        break;
                    case 3:
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
                    case 4:
                        $value = $item['tobeqty'];
                        break;
                    case 5:
                        $value = $item['beqty'];
                        break;
                    case 6:
                        if($item['cs_employeename'] == '请选择'){
                            $value = '--';
                        }else{
                            $value = $item['cs_employeename'];
                        }
                        break;
                    case 7:
                        if ($item['stockinstatus']==2) {
                            $value = "暂存";
                        }
                        else if ($item['stockinstatus']==3) {
                            $value = "待审核";
                        }else if ($item['stockinstatus']==4) {
                            $value = "已审核";
                        }else if ($item['stockinstatus']==5) {
                            $value = "作废";
                        }else{
                            $value = "新建";
                        }
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="靠泊装货订单.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    //导出word合同

    //导出靠泊卸货
    public function exportAction()
    {
        ob_end_clean();
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        //靠泊卸货信息
        $B = new StockberthinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $berthoutInfo = $B->getStockberthinById($id);


        if ($berthoutInfo) {
            return self::stockberthinContract($berthoutInfo);
        }else {
            COMMON::result(300, '数据异常');
        }
    }

    //靠泊卸货合同
    private static function stockberthinContract($contractInfo)
    {
        extract($contractInfo);

        $cm = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(APPLICATION_PATH . '/application/views/seal/KBstockberthin.docx');

        $templateProcessor->setValue('customername', $contractInfo['customername']);

        //下载
        $res = $templateProcessor->save();

        if ($res) {
            $fp = fopen($res, "rb"); //二进制方式打开文件
            if ($fp) {
                header("Content-Description: File Transfer");
                header('Content-Disposition: attachment; filename="' . '船舶装船作业流程.docx' . '"');
                header('Content-Type: ' . 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                header('Content-Transfer-Encoding: binary');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Expires: 0');

                fpassthru($fp); // 输出至浏览器
                exit;
            }
        }

    }


}
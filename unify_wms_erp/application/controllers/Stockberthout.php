<?php

/**
 * Created by PhpStorm.
 * User: 129
 * Date: 2017/7/6
 * Time: 15:38
 */
class StockberthoutController extends Yaf_Controller_Abstract
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
     * 靠泊卸货列表
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
        $this->getView()->make('stockberthout.stockberthoutlist', $params);
    }

    /*
     * 获取靠泊卸货数据
     */
    public function listJsonAction(){
        $request = $this->getRequest();

        $search = array(
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'bar_no' => $request->getPost('bar_no', ''),
            'docsource'=>$request->getPost('docsource', ''),
            'customer_sysno'=>$request->getPost('customer_sysno', ''),
            'inshipname'=>$request->getPost('inshipname', ''),
            'bar_stockoutstatus' => $request->getPost('bar_stockoutstatus', '-100'),
            'bar_stockouttype' => 4,
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );
        $S = new StockberthoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStockberthout($search);
        echo json_encode($list);

    }

    /**
     * 待卸货单数据列表
     * @return json
     */
    public function waitberthoutlistAction()
    {
        $params = array(
            'bar_no' => '',
            'bar_name' => '',
            'navid' => 'stockberthout',
        );

        $this->getView()->make('stockberthout.waitberthoutlist', $params);
    }

    /**
     * 卸货预约数据JSON
     * @return json
     */
    public function waitberthOutListJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'begin_time'=>$request->getPost('begin_time'),
            'end_time'=>$request->getPost('end_time'),
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'bar_status' =>5,
            'stockouttype' => 4, //1船入库，2车入库 3管 //4靠泊装卸
        );
        $S = new BookoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchBookout($search);
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
        $S = new StockberthoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $bookout_sysno = $request->getPost('bookout_sysno','');

        if (!$id) {
            $action = "/Stockberthout/newJson/";
            $params = array();

            //获取预约明细
             if($bookout_sysno){
                $B = new BookberthoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

                 //获取主表信息
                 $params = $B->getBookberthoutById($bookout_sysno);
                 $search = array(
                     'bookingout_sysno'=>$bookout_sysno,
                     'page'=>false,
                 );
                 $bookDetail = $B->getBookoutdetailById($search);
                 if(count($bookDetail)>0){
                     foreach($bookDetail as $item){
                         $arr['sysno'] = $item['sysno'];
                         $arr['bookingout_sysno'] = $item['bookingout_sysno'];
                         $arr['goodsname'] = $item['goodsname'];
                         $arr['tobeqty'] = $item['bookingoutqty'];//预计数量
                         $arr['shipbookingdate'] = $item['shipokdate'];
                         $arr['memo'] = $item['memo'];

                         $detaillist[] = $arr;
                     }

                     $params['detaillist'] = json_encode($detaillist);
                 }else{
                     $params['detaillist'] = json_encode([]);
                 }
                }


        } else {
            $params = $S->getStockberthoutById($id);
            $status = $params['stockoutstatus'];

            if($mode == 'edit'){
                if($status == 2||$status == 7){
                    $action = '/Stockberthout/editJson';
                }
                else{
                    COMMON::result(300, '非暂存或退回状态不能编辑！');
                    return;
                }
            }elseif($mode == 'audit'){
                $action = '/Stockberthout/auditJson';
            }elseif($mode == 'back'){
                $action = '/Stockberthout/backStockberthout';
            }elseif($mode == 'blank'){
                $action = '/stockberthout/blankstockberthout';
            }elseif($mode == 'addattach'){
                $action = '/Stockberthout/addcontractattach';
            }


            //暂存状态 编辑
            $params['attach'] = array();
            $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $attach = $A->getAttachByMAS('Stockberthout', 'Stockberthoutatt', $id);
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
        $this->getView()->make('stockberthout.stockberthoutedit', $params);
    }

    /*
     * 获取靠泊卸货明细数据
     */
    public function adddetailJsonAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $S = new StockberthoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getStockberthoutdetailById($id);
        echo json_encode($list);
    }

    /*
    * 获取泊位单数据
    */
    public function getbethJsonAction (){
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $S = new StockberthoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getberthByBookId($id);
        echo json_encode($list);
    }

    /*
    * 获取管线单数据
    */
    public function getpipeJsonAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $S = new StockberthoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getpipeByBookId($id);
        echo json_encode($list);
    }

    /*
     * 靠泊卸货明细视图
     */
    public function StockberthoutdetaileditAction(){
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

        $this->getView()->make('stockberthout.stockberthoutdetail', $params);
    }

    /*
     * 新增靠泊卸货
     */
    public  function newJsonAction(){
        $request = $this->getRequest();
        $bookout_sysno = $request->getPost('bookout_sysno','');
        $stockoutstatus = $request->getPost('stockoutstatus','');
        $stockberthoutdetaildata = $request->getPost('stockberthoutdetaildata', "");
        $stockberthoutdetaildata = json_decode($stockberthoutdetaildata, true);
        if($bookout_sysno){
            if (count($stockberthoutdetaildata) == 0) {
                COMMON::result(300, '明细不能为空');
                return false;
            }

            $S = new StockberthoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

            $contracttypedata = $C->getContractById($request->getPost('contract_sysno', '0'));

            if ($contracttypedata) {
                $contracttype = $contracttypedata['contracttype'];
            }

            $input = array(
                'stockoutno' => COMMON::getCodeId('X3'),
                'stockouttype' => 4,
                'stockoutno' => $request->getPost('stockoutno', ''),
                'stockoutdate' => date('Y-m-d',time()),
                'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
                'customername' => $request->getPost('obj_customer_name', ''),
                'booking_out_sysno' => $bookout_sysno,
                'bookingoutno' => $request->getPost('bookingoutno', ''),
                'contract_sysno' => $request->getPost('contract_sysno', ''),
                'contractno' => $request->getPost('contractno', ''),
                'docsource'=>$request->getPost('docsource', ''),
                'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
                'cs_employeename' => $request->getPost('cs_employeename', ''),
                'stockoutstatus' => 2,
                'status' => $request->getPost('status', '1'),
                'isdel' => $request->getPost('isdel', '0'),
                'updated_at' => '=NOW()'
            );

            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($bookout_sysno, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }
            $res = $S->addstockberthout($bookout_sysno, $input, $stockberthoutdetaildata,$stockoutstatus);
            if ($res['statusCode']==200) {

                $row = $S->getStockberthoutById($bookout_sysno);
                COMMON::result(200, '更新成功'.$res['msg'], $row);
            } else {
                COMMON::result(300, '更新失败'.$res['msg']);
            }
        }else{
            COMMON::result('300','数据异常');

        }

    }

    /*
     * 编辑靠泊卸货
     */
    public function editJsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockoutstatus = $request->getPost('stockoutstatus', '');
        $stockberthoutdetaildata = $request->getPost('stockberthoutdetaildata', "");
        $stockberthoutdetaildata = json_decode($stockberthoutdetaildata, true);
        if (count($stockberthoutdetaildata) == 0) {
            COMMON::result(300, '装卸货品明细不能为空');
            return;
        }
        $count = 0;
        foreach($stockberthoutdetaildata as $item){
            $count = $count + $item['beqty'];
        }
        if($count==0){
            COMMON::result(300, '装卸货品实际数量不能为0');
            return;
        }

        $S = new StockberthoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'stockouttype' => 4,
            'stockoutno' => $request->getPost('stockoutno', ''),
            'stockoutdate' => date('Y-m-d',time()),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customername' => $request->getPost('obj_customer_name', ''),
            'booking_out_sysno' => $id,
            'bookingoutno' => $request->getPost('bookingoutno', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contractno' => $request->getPost('contractno', ''),
            'docsource'=>$request->getPost('docsource', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'stockoutstatus' => 2,
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'updated_at' => '=NOW()'
        );

        $Stockberthoutinfo = $S->getStockberthoutById($id);
        if($Stockberthoutinfo['stockoutstatus']==7){
            $input['stockoutstatus'] = 7;
        }

        if ($S->updatestockberthout($id, $input, $stockberthoutdetaildata,$stockoutstatus)) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $S->getStockberthoutById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    /*
     * 审核靠泊卸货订单
     */
    public function auditJsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockoutstatus = $request->getPost('stockoutstatus', '');
        $auditreason = $request->getPost('auditreason', '');
        $stockberthoutdetaildata = $request->getPost('stockberthoutdetaildata', "");
        $stockberthoutdetaildata = json_decode($stockberthoutdetaildata, true);

        $S = new StockberthoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if ($stockoutstatus == 6) {
            if ($auditreason == '') {
                COMMON::result(300, '审核备注不能为空');
                return;
            }
            $status = 6;
        } elseif ($stockoutstatus == 4) {
            $status = 4;
        }

        $input = array(
            'stockoutstatus' => $status,
            'stockoutdate' => $request->getPost('stockoutdate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customername' => $request->getPost('obj_customer_name', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contractno' => $request->getPost('contractno', ''),
            'auditreason' => $auditreason,
            'updated_at' => '=NOW()'
        );

        if ($S->auditStockberthout($id, $input,$stockberthoutdetaildata)) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $S->getStockberthoutById($id);
            COMMON::result(200, '审核成功', $row);
        } else {
            COMMON::result(300, '审核失败');
        }
    }

    /*
     * 退回靠泊卸货订单
     */
    public function backstockberthoutAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $backreason = $request->getPost('backreason', '');

        $S = new StockberthoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if ($backreason == '') {
            COMMON::result(300, '退回备注不能为空');
            return;
        }

        $input = array(
            'isdel' => 1,
            'updated_at' => '=NOW()'
        );

        if ($S->backStockberthout($id, $input,$backreason)) {
            COMMON::result(200, '退回成功');
        } else {
            COMMON::result(300, '退回失败');
        }
    }

    /*
     *作废靠泊卸货订单
     */
    public function blankstockberthoutAction(){

        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $abandonreason = $request->getPost('abandonreason', '');

        $S = new StockberthoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if ($abandonreason == '') {
            COMMON::result(300, '作废备注不能为空');
            return;
        }

        $input = array(
            'stockoutstatus' => 5,
            'auditreason' => $abandonreason,
            'updated_at' => '=NOW()'
        );

        if ($S->blankStockberthout($id, $input)) {
            $row = $S->getStockberthoutById($id);
            COMMON::result(200, '作废成功', $row);
        } else {
            COMMON::result(300, '作废失败');
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
            'bar_stockoutstatus' => $request->getPost('bar_stockoutstatus', '-100'),
            'page' => false,
        );

        $S = new StockberthoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $stockcarindata = $S->searchStockberthout($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("当前靠泊卸货订单列表")
            ->setSubject("列表")
            ->setDescription("当前靠泊卸货订列表");

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
        $objActSheet->setTitle('当前靠泊卸货订单列表');

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
                        $value = $item['stockoutno'];
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
                        if ($item['cs_employeename'] == '请选择'){
                            $value = '--';
                        }else{
                            $value = $item['cs_employeename'];
                        }
                        break;
                    case 7:
                        if ($item['stockoutstatus']==2) {
                            $value = "暂存";
                        }
                        else if ($item['stockoutstatus']==3) {
                            $value = "待审核";
                        }else if ($item['stockoutstatus']==4) {
                            $value = "已审核";
                        }else if ($item['stockoutstatus']==5) {
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
        header('Content-Disposition: attachment;filename="靠泊卸货订单.xlsx"');
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
        $B = new StockberthoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $berthoutInfo = $B->getStockberthoutById($id);

            if ($berthoutInfo) {
                return self::stockberthoutContract($berthoutInfo);
            }else {
                COMMON::result(300, '数据异常');
            }
    }

    //靠泊卸货合同
    private static function stockberthoutContract($contractInfo)
    {
        extract($contractInfo);

        $cm = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(APPLICATION_PATH . '/application/views/seal/KBstockberthout.docx');

        $templateProcessor->setValue('customername', $contractInfo['customername']);

        //获取明细
        $B = new StockberthoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $stockout_id = $contractInfo['sysno'];
        $stockoutdetail  = $B->getStockberthoutdetailById($stockout_id);
        $shipname = $stockoutdetail[0]['shipname'];

        $templateProcessor->setValue('goodsname', $stockoutdetail[0]['goodsname']);
        $beqty = 0;
        foreach($stockoutdetail as $key=>$value){
               $beqty +=$value['beqty'];
         }
        //已提数
        $templateProcessor->setValue('beqty', $beqty);
        if(!empty($shipname)){
            $S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $search = array (
                'bar_shipname' => $shipname,
                'page' => false,
            );
            $list = $S->searchShipList($search);
            $shiplist = $list['list'][0];

            $templateProcessor->setValue('shipname', $shiplist['shipname']);
            $templateProcessor->setValue('shipcontact', $shiplist['shipcontact']);//联系方式
            $templateProcessor->setValue('captain', $shiplist['captain']);//船长
        }

         $booking_out_sysno = $contractInfo['booking_out_sysno'];

         if($contractInfo['ispipelineorder']==1){
             $P = new PipelineorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
             $search = array(
                 'book_sysno' => $booking_out_sysno?$booking_out_sysno:0,
                 'businesstype'=>16,
             );
             $pipedetail = $P->getDetials($search);

             $templateProcessor->cloneRow('pipeline', count($pipedetail));
//
             foreach ($pipedetail as $key => $item) {//货品信息
                 $templateProcessor->setValue('pipeline#' . ($key + 1), $item['wharf_pipelineno']);
                 $templateProcessor->setValue('wharf_pipelineno#' . ($key + 1), $item['wharf_pipelineno']);
                 $templateProcessor->setValue('area_pipelineno#' . ($key + 1), $item['area_pipelineno']);
             }

         }

        if($contractInfo['isberthorder']){
            $B = new BerthorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $search = array(
                'book_sysno' => $booking_out_sysno?$booking_out_sysno:0,
                'businesstype'=>16,
            );
            $berthdetail = $B->getDetails($search);
               


        }


     //   print_r($pipedetail);die;

        //下载
        $res = $templateProcessor->save();

        if ($res) {
            $fp = fopen($res, "rb"); //二进制方式打开文件
            if ($fp) {
                header("Content-Description: File Transfer");
                header('Content-Disposition: attachment; filename="' . '船舶卸货作业流程.docx' . '"');
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
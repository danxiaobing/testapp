<?php

class BookcarinController extends Yaf_Controller_Abstract
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
     * 显示整个后台页面框架及菜单
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
        $this->getView()->make('bookcarin.bookcarinlist', $params);
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
            'bar_bookcarinstatus' => $request->getPost('bar_bookcarinstatus', '-100'),
            'bar_stockintype' => 2,
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );
        $S = new BookcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchBookcarin($search);

        echo json_encode($list);

    }

    /*
     * 显示编辑页面
     */
    public function EditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $mode = $request->getParam('mode','');

        $S = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if (!$id) {
            $action = "/bookcarin/newJson/";
            $params = array();
        } else {
            $params = $S->getBookcarinInfoById($id);
            $status = $params['bookinginstatus'];

            if($mode == 'edit'){
                if($status == 2||$status == 7){
                    $action = '/bookcarin/editJson';
                }
                else{
                    COMMON::result(300, '非暂存或退回状态不能编辑！');
                    return;
                }
            }elseif($mode == 'sure'){
                $action = '/bookcarin/surebookcarin';
            }elseif($mode == 'audit'){
                $action = '/bookcarin/auditJson';
            }elseif($mode == 'back'){
                $action = '/bookcarin/backbookcarin';
            }elseif($mode == 'addattach'){
                $action = '/bookcarin/addcontractattach';
            }elseif($mode =='addcar'){
                $action = "/bookcarin/addcarjson/";
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

        $this->getView()->make('bookcarin.bookcarinedit', $params);
    }

    /*
     * 新建车入库预约单
     */
    public function newJsonAction()
    {
        $request = $this->getRequest();
        $bookinginstatus = $request->getPost('bookinginstatus', '');
        $bookcarindetaildata = $request->getPost('bookcarindetaildata', "");
        $bookcarindetaildata = json_decode($bookcarindetaildata, true);
        $bookcarincarsdata = $request->getPost('bookcarincarsdata', "");
        $bookcarincarsdata = json_decode($bookcarincarsdata, true);
        if (count($bookcarindetaildata) == 0) {
            COMMON::result(300, '入库明细信息不能为空');
            return;
        }

        $G = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $T = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        foreach ($bookcarindetaildata as $key => $value) {
            $bookcarindetaildata[$key]['goodsname'] = $value['goodsname'];
            $bookcarindetaildata[$key]['goods_sysno'] = $value['goods_sysno'];
            $bookcarindetaildata[$key]['unitname'] = $value['unitname'] ? $value['unitname'] : '吨';
            $detailarr[$bookcarindetaildata[$key]['goods_sysno']] = 1;

            $storagetank_sysno = $value['storagetank_sysno'];
            $goods_sysno = $bookcarindetaildata[$key]['goods_sysno'];

            if($storagetank_sysno){
                if ($T->storagetankgoodsbyid($storagetank_sysno)!=$goods_sysno) {

                    COMMON::result(300, '该储罐还有其他货品存量');
                    return;
                }
            }

            $detailtankarr[$storagetank_sysno] = $value['bookinginqty'];
        }

        if (count($detailarr) > 1) {
            COMMON::result(300, '只能添加一种货品');
            return;
        }

        if (!empty($detailtankarr))
            foreach ($detailtankarr as $key => $value) {
                $search = array(
                    'storagetank_sysno' => $key,
                );
                $available = $T->getStoragetankavailable($search);
                if ($available < $value) {
                    COMMON::result(300, '该储罐可存放容量不足,当前储罐可用容量:' . $available . '吨');
                    return;
                }
            }

        $S = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $contracttypedata = $C->getContractById($request->getPost('contract_sysno', '0'));
        if ($contracttypedata) {
            $contracttype = $contracttypedata['contracttype'];
        }

        $input = array(
            'stockintype' => $request->getPost('stockintype', '2'),
            'bookinginno' => COMMON::getCodeId('B1'),
            'bookingindate' => $request->getPost('bookingindate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customer_name' => $request->getPost('obj_customer_name', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contract_no' => $request->getPost('contract_no', ''),
            'contracttype' => $contracttype,
            'docsource' => $request->getPost('docsource', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'takegoodsno' =>$request->getPost('takegoodsno', ''),
            'carname' => $request->getPost('carname', ''),
            'issave' => $request->getPost('issave', '1'),
            'isbusinesscheck' => $request->getPost('isbusinesscheck', ''),
            'businesschecktype' => $request->getPost('businesschecktype', ''),
            'businesscheckunitname' => $request->getPost('businesscheckunitname', ''),
            'isqualitycheck' => $request->getPost('isqualitycheck', ''),
            'bookinginstatus' => $request->getPost('bookinginstatus', '2'),
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        //若车辆信息不在数据库中，则登记车辆信息
        if($bookcarincarsdata){
            $Su = new SupplierModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $Su -> checkCardata($bookcarincarsdata);
        }

        if(count($bookcarincarsdata)>=2){
            for($i=0;$i<count($bookcarincarsdata);$i++){
                for($j=$i+1;$j<count($bookcarincarsdata);$j++){
                    if($bookcarincarsdata[$i]['carid']==$bookcarincarsdata[$j]['carid']&&
                        $bookcarincarsdata[$i]['carname']==$bookcarincarsdata[$j]['carname']){
                        COMMON::result(300, '车辆信息不能重复');
                        return;
                    }
                }
            }
        }

        if ($id = $S->addBookcarin($input, $bookcarindetaildata, $bookcarincarsdata,$bookinginstatus)) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $S->getBookcarinById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    /*
     * 编辑车入库预约单
     */
    public function editJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $T = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $bookinginstatus = $request->getPost('bookinginstatus', '');

        $bookcarindetaildata = $request->getPost('bookcarindetaildata', "");
        $bookcarincarsdata = $request->getPost('bookcarincarsdata', "");
        $bookcarindetaildata = json_decode($bookcarindetaildata, true);
        $bookcarincarsdata = json_decode($bookcarincarsdata, true);
        if (count($bookcarindetaildata) == 0) {
            COMMON::result(300, '入库预约单明细不能为空');
            return;
        }

        foreach ($bookcarindetaildata as $key => $value) {
            $bookcarindetaildata[$key]['goodsname'] = $value['goodsname'];
            $bookcarindetaildata[$key]['goods_sysno'] = $value['goods_sysno'];
            $bookcarindetaildata[$key]['unitname'] = $value['unitname'] ? $value['unitname'] : '吨';
            $detailarr[$bookcarindetaildata[$key]['goods_sysno']] = 1;

            $storagetank_sysno = $value['storagetank_sysno'];
            $goods_sysno = $bookcarindetaildata[$key]['goods_sysno'];

            if($storagetank_sysno){
                if ($T->storagetankgoodsbyid($storagetank_sysno)!=$goods_sysno) {

                    COMMON::result(300, '该储罐还有其他货品存量');
                    return;
                }
            }

            $detailtankarr[$storagetank_sysno] = $value['bookinginqty'];
        }
        if (count($detailarr) > 1) {
            COMMON::result(300, '只能添加一种货品');
            return;
        }
        foreach ($detailtankarr as $key => $value) {
            $search = array(
                'storagetank_sysno' => $key,
            );
            $available = $T->getStoragetankavailable($search);
            if ($available < $value) {
                COMMON::result(300, '该储罐可存放容量不足,当前储罐可用容量:' . $available . '吨');
                return;
            }
        }

        $S = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $contracttypedata = $C->getContractById($request->getPost('contract_sysno', '0'));
        if ($contracttypedata) {
            $contracttype = $contracttypedata['contracttype'];
        }

        $input = array(
            'stockintype' => $request->getPost('stockintype', '2'),
            'bookinginno' => $request->getPost('bookinginno', ''),
            'bookingindate' => $request->getPost('bookingindate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customer_name' => $request->getPost('obj_customer_name', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contract_no' => $request->getPost('contract_no', ''),
            'contracttype' => $contracttype,
            'docsource' => $request->getPost('docsource', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'takegoodsno' =>$request->getPost('takegoodsno', ''),
            'carname' => $request->getPost('carname', ''),
            'issave' => $request->getPost('issave', '1'),
            'isbusinesscheck' => $request->getPost('isbusinesscheck', ''),
            'businesschecktype' => $request->getPost('businesschecktype', ''),
            'businesscheckunitname' => $request->getPost('businesscheckunitname', ''),
            'isqualitycheck' => $request->getPost('isqualitycheck', ''),
            'bookinginstatus' => $request->getPost('bookinginstatus', ''),
            'rejectreason' => $request->getPost('rejectreason', ''),
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        //若车辆信息不在数据库中，则登记车辆信息
        if($bookcarincarsdata){
            $Su = new SupplierModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $Su -> checkCardata($bookcarincarsdata);
        }

        if(count($bookcarincarsdata)>=2){
            for($i=0;$i<count($bookcarincarsdata);$i++){
                for($j=$i+1;$j<count($bookcarincarsdata);$j++){
                    if($bookcarincarsdata[$i]['carid']==$bookcarincarsdata[$j]['carid']&&
                        $bookcarincarsdata[$i]['carname']==$bookcarincarsdata[$j]['carname']){
                        COMMON::result(300, '车辆信息不能重复');
                        return;
                    }
                }
            }
        }

        $bookcarininfo = $S->getBookcarinById($id);
        if($bookcarininfo['bookinginstatus']==7){
            $input['bookinginstatus'] = 7;
        }

        if ($S->updateBookcarin($id, $input, $bookcarindetaildata, $bookcarincarsdata, $bookinginstatus)) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $S->getBookcarinById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    /*
     * 查看车入库预约单
     */
    public function SeeAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $mode = $request->getParam('mode', '');

        $S = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params = $S->getBookcarinInfoById($id);

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
        $params['mode'] = $mode;

        $this->getView()->make('bookcarin.bookcarinedit', $params);
    }

    /*
     * 车入库预约单添加附件
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
     * 车入库预约单excel导出
     */
    public function excelAction(){
        $request = $this->getRequest();
        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'bar_status' => $request->getPost('bar_status', '-100'),
            'bar_isdel' => $request->getPost('bar_isdel', '-100'),
            'bar_stockintype' => $request->getPost('bar_stockintype', '2'),
            'bar_bookinginstatus' => $request->getPost('bar_bookinginstatus', '-100'),
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'page' => false,
            'orders' => $request->getPost('orders', ''),
        );

        $S = new BookcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $bookcarindata = $S->searchBookcarin($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("当前车入库预约列表")
            ->setSubject("列表")
            ->setDescription("当前车入库预约列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '入库预约单号'),
            array('B1:B1', 'B1', '005E9CD3', '单据来源'),
            array('C1:C1', 'C1', '005E9CD3', '客户'),
            array('D1:D1', 'D1', '005E9CD3', '合同编号'),
            array('E1:E1', 'E1', '0094CE58', '提货单号'),
            array('F1:F1', 'F1', '005E9CD3', '入库预约日期'),
            array('G1:G1', 'G1', '0094CE58', '货品名称'),
            array('H1:H1', 'H1', '0094CE58', '规格'),
            array('I1:I1', 'I1', '0094CE58', '数量'),
            array('J1:J1', 'J1', '0094CE58', '计量单位'),
            array('K1:K1', 'K1', '003376B3', '货物性质'),
            array('L1:L1', 'L1', '003376B3', '罐号'),
            array('M1:M1', 'M1', '003376B3', '客服'),
            array('N1:N1', 'N1', '003376B3', '单据状态'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('当前车入库预约列表');

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
                        $value = $item['takegoodsno'];
                        break;
                    case 5:
                        $value = $item['bookingindate'];
                        break;
                    case 6:
                        $value = $item['goodsname'];
                        break;
                    case 7:
                        $value = $item['goods_quality_name'];
                        break;
                    case 8:
                        $value = $item['bookinginqty'];
                        break;
                    case 9:
                        $value = '吨';//$item['docsource'];
                        break;
                    case 10:
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
                    case 11:
                        $value = $item['storagetankname'];
                        break;
                    case 12:
                        if($item['cs_employeename'] == '请选择'){
                            $value = '--';
                        }else{
                            $value = $item['cs_employeename'];
                        }
                        break;
                    case 13:
                        if ($item['bookinginstatus']==2) {
                            $value = "暂存";
                        }
                        else if ($item['bookinginstatus']==3) {
                            $value = "待确认";
                        }else if ($item['bookinginstatus']==4) {
                            $value = "待审核";
                        }else if ($item['bookinginstatus']==5) {
                            $value = "入库中";
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
        header('Content-Disposition: attachment;filename="当前车入库预约查询.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    /*
     * 审核车入库预约单
     */
    public function auditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $bookinginstatus = $request->getPost('bookinginstatus', '');
        $auditreason = $request->getPost('auditreason', '');
        $bookcarindetaildata = $request->getPost('bookcarindetaildata', "");
        $bookcarindetaildata = json_decode($bookcarindetaildata, true);
        $bookcarincarsdata = $request->getPost('bookcarincarsdata', "");
        $bookcarincarsdata = json_decode($bookcarincarsdata, true);


        $B = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
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

        if ($B->auditBookcarin($id, $input,$bookcarindetaildata)) {

            if ($bookinginstatus == 4){
                //生成车入库订单
                //查询主表信息
                $BookcarinInfo = $B->getBookcarinInfoById($id);
                $isreback = $BookcarinInfo['isreback'];
                $S = new StockcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $takegoodsnum = 0;
                foreach($bookcarindetaildata as $item){
                    $takegoodsnum += $item['bookinginqty'];
                }

                $input = array(
                    'stockintype' => $request->getPost('stockintype', '2'),
                    'stockinno' => $request->getPost('bookinginno', ''),
                    'stockindate' => date('Y-m-d',time()),
                    'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
                    'customername' => $request->getPost('obj_customer_name', ''),
                    'booking_in_sysno' => $id,
                    'bookingin_no' => $request->getPost('bookinginno', ''),
                    'contract_sysno' => $request->getPost('contract_sysno', ''),
                    'contractno' => $request->getPost('contract_no', ''),
                    'docsource'=>$request->getPost('docsource', ''),
                    'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
                    'cs_employeename' => $request->getPost('cs_employeename', ''),
                    'takegoodsno' =>$request->getPost('takegoodsno', ''),
                    'takegoodsnum' =>$takegoodsnum,
                    'isbusinesscheck' => $request->getPost('isbusinesscheck', ''),
                    'businesschecktype' => $request->getPost('businesschecktype', ''),
                    'businesscheckunitname' => $request->getPost('businesscheckunitname', ''),
                    'stockinstatus' => 2,
                    'isreback' => $isreback,
                    'status' => $request->getPost('status', '1'),
                    'isdel' => $request->getPost('isdel', '0'),
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()'
                );

                $res = $S->addStockcarin($input, $bookcarindetaildata, $bookcarincarsdata,3);
                if (!$res) {
                    COMMON::result(300, '生成入库订单失败');
                    return;
                }
            }

            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $B->getBookcarinById($id);
            COMMON::result(200, '审核成功', $row);
        } else {
            COMMON::result(300, '审核失败');
        }
    }

    /*
     * 删除车入库预约单
     */
    public function delJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);
        $S = new BookcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $res = $S->getBookcarinById($id);
        if($res['docsource']==2){
            COMMON::result(300, '国烨云仓订单不能删除');
            return ;
        }
        $input = array(
            'isdel' => 1
        );
        //暂存、退回状态 删除
        if ($res['bookinginstatus'] == 2||$res['bookinginstatus'] == 7) {
            if ($S->delBookcarin($id, $input)) {
                COMMON::result(200, '删除成功');
            } else {
                COMMON::result(300, '删除失败');
            }
        } else {
            COMMON::result(300, '非暂存或退回状态无法删除！');
        }
    }

    /*
     * 添加车入库预约单明细页面
     */
    public function bookcarindetaileditAction()
    {
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

        $this->getView()->make('bookcarin.bookcarinadddetail', $params);
    }

    /*
     * 添加车入库预约单明细数据
     */
    public function adddetailJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');

        $S = new BookcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getBookcarindetailById($id);

        if ($id && !empty($list)) {

            foreach ($list as $key => $value) {
                $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $data = $quality->getQualityById($value['goods_quality_sysno']);
                $list[$key]['qualityname'] = $data['qualityname'];
                $list[$key]['goods_sysno'] = $value['goods_sysno'];
                $list[$key]['goodsname'] = $value['goodsname'];
                $list[$key]['carid'] = $value['carid'];
            }
        }
        echo json_encode($list);
    }

    /*
     * 添加车入库预约单车辆信息页面
     */
    public function bookcarineditcarsdetailAction()
    {
        $request = $this->getRequest();
        $cid = $request->getParam('cid', '0');
        $handlestatus = $request->getParam('handlestatus','0');
        $params = $request->getPost('selectedDatasArray',array());

        $params['customer_sysno'] = $cid;
        $params['handlestatus'] = $handlestatus;
       //print_r($params);die;
        $this->getView()->make('bookcarin.bookcarinaddcars', $params);
    }

    /*
     * 添加车入库预约单车辆信息数据
     */
    public function addcarsdetailJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');

        $S = new BookcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getBookcarincarsById($id);

        echo json_encode($list);
    }

    /**
     * 显示车入库预约待确认列表页面
     */
    public function sureAction()
    {
        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status'=>1,
            'page'=>false
        );
        $customerdata = $C->searchCustomer($search);
        $params['customerlist'] = $customerdata['list'];
        $this->getView()->make('bookcarin.surelist', $params);
    }

    /*
     * 车入库预约待确认列表数据
     */
    public function sureJsonAction()
    {
        $request = $this->getRequest();
        $search = array(
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'bar_no' => $request->getPost('bar_no', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'bar_stockintype' => $request->getPost('bar_stockintype', '2'),
            'bar_bookcarinstatus' => 3,
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR()
        );
        $S = new BookcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchBookcarin($search);

        echo json_encode($list);
    }

    /**
     * 确认车入库预约单
     */
    public function surebookcarinAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id','');
        $bookinginstatus = $request->getPost('bookinginstatus','');
        $confirmreason = $request->getPost('confirmreason', '');
        $bookcarindetaildata = $request->getPost('bookcarindetaildata', "");
        $bookcarindetaildata = json_decode($bookcarindetaildata, true);

        $T = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $L = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if($bookinginstatus ==5){
            foreach ($bookcarindetaildata as $key => $value) {
                $bookcarindetaildata[$key]['goodsname'] = $value['goodsname'];
                $bookcarindetaildata[$key]['goods_sysno'] = $value['goods_sysno'];
                $bookcarindetaildata[$key]['unitname'] = $value['unitname'] ? $value['unitname'] : '吨';
                $detailarr[$bookcarindetaildata[$key]['goods_sysno']] = 1;

                $storagetank_sysno = $value['storagetank_sysno'];
                $goods_sysno = $bookcarindetaildata[$key]['goods_sysno'];

                if(!$storagetank_sysno){
                    COMMON::result(300, '进货罐号不能为空');
                    return;
                }

                if ($T->storagetankgoodsbyid($storagetank_sysno)!=$goods_sysno) {
                    COMMON::result(300, '该储罐还有其他货品存量');
                    return;
                }
                $detailtankarr[$storagetank_sysno] = $value['bookinginqty'];
                //添加储罐和货物性质对应关系
                $tankId = $value['storagetank_sysno'];
                $goodsnature = $value['goodsnature'];
                $res = $L->tankTonature($tankId,$goodsnature);
                if($res['code']==300){
                    COMMON::result(300, $res['message']);
                    return;
                }
            }

            if (count($detailarr) > 1) {
                COMMON::result(300, '只能添加一种货品');
                return;
            }

            foreach ($detailtankarr as $key => $value) {
                $search = array(
                    'storagetank_sysno' => $key,
                );
                $available = $T->getStoragetankavailable($search);
                if ($available < $value) {
                    COMMON::result(300, '该储罐可存放容量不足,当前储罐可用容量:' . $available . '吨');
                    return;
                }
            }
        }

        if($bookinginstatus ==5){
            $status = 4;
        }elseif($bookinginstatus ==6){
            if ($confirmreason == '') {
                COMMON::result(300, '退回备注不能为空');
                return;
            }
            $status = 7;
        }
        $data = [
            'confirmreason' => $confirmreason,
            'bookinginstatus' => $status,
            'updated_at' => '=NOW()'
        ];

        $bookcarin = new BookcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $res = $bookcarin->sureBookcarin($id, $data,$bookcarindetaildata);
        if ($res) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }
            COMMON::result(200, '确认成功!');
        } else {
            COMMON::result(300, '确认失败！');
        }
    }

    /**
     * @title 退回预约单
     */
    public function backbookcarinAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id');
        $backreason = $request->getPost('backreason', '');
        $bookcarindetaildata = $request->getPost('bookcarindetaildata', "");
        $bookcarindetaildata = json_decode($bookcarindetaildata, true);

        if ($backreason == '') {
            COMMON::result(300, '退回备注不能为空');
            return;
        }

        $data = [
            'backreason' => $backreason,
            'bookinginstatus' => 7
        ];
        $bookcarin = new BookcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $res = $bookcarin->backbookcarin($id, $data,$bookcarindetaildata);
        if ($res) {
            COMMON::result(200, '退回成功!');
        } else {
            COMMON::result(300, '退回失败！');
        }
    }

    /*
     * 登记车辆信息
     */
    public function addcarjsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        $bookcarincarsdata = $request->getPost('bookcarincarsdata', "");
        $bookcarincarsdata = json_decode($bookcarincarsdata, true);

        if(empty($bookcarincarsdata)){
            COMMON::result(300, '车辆信息不能为空');
            return;
        }

        $S = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        //若车辆信息不在数据库中，则登记车辆信息
        if($bookcarincarsdata){
            $Su = new SupplierModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $Su -> checkCardata($bookcarincarsdata);
        }

        if ($S->addcarBookcarin($id,$bookcarincarsdata)) {
            $row = $S->getBookcarinById($id);
            COMMON::result(200, '登记车辆成功', $row);
        } else {
            COMMON::result(300, '登记车辆失败');
        }
    }

    /**
     * @titile 车入库预约待审核列表页面
     */
    public function reviewAction()
    {
        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status'=>1,
            'page'=>false
        );
        $customerdata = $C->searchCustomer($search);
        $params['customerlist'] = $customerdata['list'];
        $this->getView()->make('bookcarin.reviewlist', $params);
    }

    /**
     * @titile 车入库预约待审核列表数据
     */
    public function reviewJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'bar_no' => $request->getPost('bar_no', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'bar_stockintype' => $request->getPost('bar_stockintype', '2'),
            'bar_bookcarinstatus' => 4,
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR()
        );
        $S = new BookcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchBookcarin($search);

        echo json_encode($list);
    }

    //判断是否超罐
    public function ajaxjudgestorageAction()
    {
        $request = $this->getRequest();
        $bookcarindetaildata = $request->getPost('bookcarindetaildata','');
        $T = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $count = 0;
        foreach ($bookcarindetaildata as $key => $value) {
            $storagetank_sysno = $value['storagetank_sysno'];
            $detailtankarr[$storagetank_sysno] = $detailtankarr[$storagetank_sysno] + $value['bookinginqty'];
            $search = array(
                'storagetank_sysno' => $storagetank_sysno,
            );
            $available = $T->getStoragetankavailable($search);
            if ($available < $detailtankarr[$storagetank_sysno]) {
                echo json_encode(['code'=>300,'message'=>$value['storagetankname'] . '可存放容量不足,可用容量为:' . $available . '吨']);
                break;
            }else{
                $count ++;
            }
        }
        if($count==count($bookcarindetaildata)){
            echo json_encode(['code'=>200,'message'=>'可以存放']);
        }

    }

}
<?php
#author wu xianneng
class StockcarinController extends Yaf_Controller_Abstract
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
     * 显示整个车入库列表后台页面框架及菜单
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
        $this->getView()->make('stockcarin.stockcarinlist', $params);
    }

    /**
     * 获取车入库列表数据
     */
    public function listJsonAction()
    {
        $request = $this->getRequest();
        $search = array(
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'bar_no' => $request->getPost('bar_no', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'bar_stockinstatus' => $request->getPost('bar_stockinstatus', '-100'),
            'bar_stockintype' => 2,
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
        );
        $S = new StockcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStockcarin($search);

        echo json_encode($list);
    }

    /*
    * 车入库订单查看
    */
    public function SeeAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $mode = $request->getParam('mode', '');

        $S = new StockcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $S->getStockcarinById($id);

        $detailData = $S->getStockcarindetailById($id);
        $carData = $S->getStockcarinCarById($id);
        $params['detaillist'] = json_encode($detailData);
        $params['carlist'] = json_encode($carData);

        $params['attach'] = array();
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $attach = $A->getAttachByMAS('stockcarin', 'sciattach', $id);
        $params['attach'] = array_merge($params['attach'], $attach);

        if (is_array($attach) && count($attach)) {
            $files1 = array();
            foreach ($attach as $file) {
                $files1[] = $file['sysno'];
            }

            $params['uploaded1'] = join(',', $files1);
        }

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $C = new CustomerModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $C->searchCustomer($search);
        $params['customerlist'] = $list['list'];

        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];
        $Company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $companyname = $Company->getDefault();
        $params['companyname'] = $companyname['companyname'];

        $params['id'] = $id;
        $params['mode'] = $mode;
        $params['action'] ='/stockcarin/addstockcarinattach';

        $this->getView()->make('stockcarin.stockcarinedit', $params);
    }

    /*
     * 添加车入库单附件
     */
    public function addstockcarinattachAction(){
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
     * 手动完成入库
     */
    public function confirmstockcarinAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id','');
        $stockinno = $request->getPost('stockinno','');
        $stockcarindetaildata = $request->getPost('stockcarindetaildata','');
        $stockcarindetaildata = json_decode($stockcarindetaildata,true);

        $S = new StockcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        //判断是否有未完成的磅码单
        $P = new PendcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'stockinno'=>$stockinno,
            'pageCurrent'=>COMMON::P(),
            'pageSize'=>COMMON::PR(),
        );
        $stockinpounddata = $P->poundsList($search);

        if(!empty($stockinpounddata['list'])){
            foreach($stockinpounddata['list'] as $item){
                if($item['poundsinstatus']<4){
                    COMMON::result(300, '还有未完成的磅码单，不能完成入库');
                    return;
                }
            }
        }else{
            COMMON::result(300, '该入库单还没入库，不能完成入库');
            return;
        }

        $input = array(
            'stockinno'=>$stockinno,
            'booking_in_sysno'=>$request->getPost('booking_in_sysno',0),
            'customer_sysno'=>$request->getPost('obj_customer_sysno',''),
            'customername'=>$request->getPost('obj_customername',''),
            'contract_sysno'=>$request->getPost('contract_sysno',''),
            'contractno'=>$request->getPost('contractno',''),
            'stockinstatus' =>4,
            'updated_at' => '=NOW()'
        );

        $id = $S->finishStockcarin($id,$input,$stockcarindetaildata);
        if ($id) {
            $row = $S->getStockcarinById($id);
            COMMON::result(200, '完成入库成功', $row);
        } else {
            COMMON::result(300, '完成入库失败');
        }
    }

    /*
     * 添加车入库单车辆
     */
    public function addscicarAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id','');
        $stockinstatus = $request->getPost('stockinstatus', '');
        $stockcarincarsdata = $request->getPost('stockcarincarsdata', "");
        $stockcarincarsdata = json_decode($stockcarincarsdata, true);

        if(empty($stockcarincarsdata)){
            COMMON::result(300, '车辆信息不能为空');
            return;
        }

        $S = new StockcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'changecarreason' =>$request->getPost('changecarreason', ""),
            'updated_at' => '=NOW()'
        );

        //若车辆信息不在数据库中，则登记车辆信息
        if($stockcarincarsdata){
            $Su = new SupplierModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $Su -> checkCardata($stockcarincarsdata);
        }

        if(count($stockcarincarsdata)>=2){
            for($i=0;$i<count($stockcarincarsdata);$i++){
                for($j=$i+1;$j<count($stockcarincarsdata);$j++){
                    if($stockcarincarsdata[$i]['carid']==$stockcarincarsdata[$j]['carid']&&
                        $stockcarincarsdata[$i]['carname']==$stockcarincarsdata[$j]['carname']){
                        COMMON::result(300, '车辆信息不能重复');
                        return;
                    }
                }
            }
        }

        $id = $S->StockcarinAddcar($id,$input,$stockcarincarsdata,$stockinstatus);
        if ($id) {
            $row = $S->getStockcarinById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    /*
     * 车入库订单EXCEL导出
     */
    public function ExcelAction(){
        $request = $this->getRequest();
        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'bar_stockintype' => $request->getPost('bar_stockintype', '2'),
            'bar_stockinstatus' => $request->getPost('bar_stockinstatus', '-100'),
            'page' => false,
        );

        $S = new StockcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $stockcarin = $S->searchStockcarin($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("当前车入库订单列表")
            ->setSubject("列表")
            ->setDescription("当前车入库订单列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '入库单号'),
            array('B1:B1', 'B1', '005E9CD3', '客户'),
            array('C1:C1', 'C1', '005E9CD3', '货品名称'),
            array('D1:D1', 'D1', '0094CE58', '规格'),
            array('E1:E1', 'E1', '0094CE58', '货物性质'),
            array('F1:F1', 'F1', '0094CE58', '合同编号'),
            array('G1:G1', 'G1', '0094CE58', '提货单号'),
            array('H1:H1', 'H1', '0094CE58', '通知数量'),
            array('I1:I1', 'I1', '0094CE58', '已入库数量'),
            array('J1:J1', 'J1', '003376B3', '待入库数量'),
            array('K1:K1', 'K1', '003376B3', '计量单位'),
            array('L1:L1', 'L1', '003376B3', '单据状态')
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('当前车入库订单列表');

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

        foreach ($stockcarin['list'] as $item) {
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
                        $value = $item['qualityname'];
                        break;
                    case 4:
                        if ($item['goodsnature']==1) {
                            $value = '保税';
                        }elseif ($item['goodsnature']==2) {
                            $value = '外贸';
                        }elseif ($item['goodsnature']==3) {
                            $value = '内贸转出口';
                        }elseif ($item['goodsnature']==4) {
                            $value = '内贸内销';
                        }
                        break;
                    case 5:
                        $value = $item['contractno'];
                        break;
                    case 6:
                        $value = $item['takegoodsno'];
                        break;
                    case 7:
                        $value = $item['tobeqty'];
                        break;
                    case 8:
                        $value = $item['beqty'];
                        break;
                    case 9:
                        if($item['stockinstatus']==4) {
                            $value = 0;
                        }else{
                            $value = $item['tobeqty']-$item['beqty'];
                        }
                        break;
                    case 10:
                        $value = '吨';
                        break;
                    case 11:
                        if ($item['stockinstatus']==1) {
                            $value = "新建";
                        }else if ($item['stockinstatus']==2) {
                            $value = "暂存";
                        }
                        else if ($item['stockinstatus']==3) {
                            $value = "入库中";
                        }else if ($item['stockinstatus']==4) {
                            $value = "已完成";
                        }
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="当前车入库订单查询.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    /*
     * 车入库订单新增 编辑显示页面
     */
    public function EditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $booking_sysno = $request->getPost('booking_sysno', '0');
        $mode = $request->getParam('mode','');

        $Bc = new BookcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $S = new StockcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $B = new BookingModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $P = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        if (!$id) {
            $action = '/stockcarin/newJson/';

            $basicinfo = $Bc->getBookcarinById($booking_sysno);
            $params['bookcarininfo'] = $basicinfo;

            $search = array(
                'bookingin_sysno' => $booking_sysno,
                'page' => false
            );

            $detailData = $B->getBookingDetailList($search);

            $dData = array();
            $tmp = $detailData['list'];
            foreach ($tmp as $row) {
                $row['tobeqty'] = $row['bookinginqty'];
                $row['bookin_detail_sysno'] = $row['sysno'];
                $dData[] = $row;
            }

            $params['detaillist'] = json_encode($dData);

            $carData = $Bc->getBookcarincarsById($booking_sysno);

            if(count($carData)==0){
                COMMON::result(300, '车辆信息为空，不能生成入库单，请先登记车辆信息！');
                return;
            }

            $params['carlist'] = json_encode($carData);
        } else {
            $action = '/stockcarin/editJson/';
            $params = $S->getStockcarinById($id);
            $status = $params['stockinstatus'];

            if ($mode == 'edit'&&$status==3) {

                $isexistp = $P->getPoundinfoByScid($id);
                if(!empty($isexistp)){
                    COMMON::result(300, '已存在磅码单无法编辑');
                    return;
                }
            }else if($mode == 'edit'&&$status==4){
                COMMON::result(300, '已完成的入库单不能编辑');
                return;
            }else if($mode =='addattach'){
                $action = '/stockcarin/addstockcarinattach';
            }else if($mode =='addcar'){
                $action = '/stockcarin/addscicar';
            }else if($mode =='confirm'){
                $action = '/stockcarin/confirmstockcarin';
            }

            $detailData = $S->getStockcarindetailById($id);
            $carData = $S->getStockcarinCarById($id);
            $params['detaillist'] = json_encode($detailData);
            $params['carlist'] = json_encode($carData);

            $params['attach'] = array();

            $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $sysno = $id;
            $attach = $A->getAttachByMAS('stockcarin', 'sciattach', $sysno);
            $params['attach'] = array_merge($params['attach'], $attach);

            if (is_array($attach) && count($attach)) {
                $files1 = array();
                foreach ($attach as $file) {
                    $files1[] = $file['sysno'];
                }

                $params['uploaded1'] = join(',', $files1);
            }
        }

        if ($booking_sysno) {
            $B = new BookingModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
            $booking = $B->getBookingInById($booking_sysno);
            $params['customer_sysno'] = $booking['customer_sysno'];
            $params['customername'] = $booking['customer_name'];
            $params['booking_in_sysno'] = $booking['sysno'];
            $params['bookingin_no'] = $booking['bookinginno'];
            $params['contract_sysno'] = $booking['contract_sysno'];
            $params['contractno'] = $booking['contract_no'];
            $params['cs_employee_sysno'] = $booking['cs_employee_sysno'];
            $params['cs_employeename'] = $booking['cs_employeename'];
        }

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $C = new CustomerModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $C->searchCustomer($search);
        $params['customerlist'] = $list['list'];

        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];

        $Company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $companyname = $Company->getDefault();
        $params['companyname'] = $companyname['companyname'];

        $params['id'] = $id;
        $params['action'] = $action;
        $params['mode'] = $mode;

        $this->getView()->make('stockcarin.stockcarinedit', $params);
    }

    /*
     * 车入库订单新增数据处理
     */
    public function newJsonAction()
    {
        $request = $this->getRequest();
        $stockinstatus = $request->getPost('stockinstatus', "");
        $stockcarindetaildata = $request->getPost('stockcarindetaildata', "");
        $stockcarincarsdata = $request->getPost('stockcarincarsdata', "");
        $stockcarindetaildata = json_decode($stockcarindetaildata, true);
        $stockcarincarsdata = json_decode($stockcarincarsdata, true);

        if (count($stockcarindetaildata) == 0) {
            COMMON::result(300, '入库单明细不能为空');
            return;
        }
        if (count($stockcarincarsdata) == 0) {
            COMMON::result(300, '车辆信息不能为空');
            return;
        }

        $S = new StockcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'stockintype' => $request->getPost('stockintype', '2'),
            'stockinno' => COMMON::getCodeId('B2'),
            'stockindate' => $request->getPost('stockindate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customername' => $request->getPost('obj_customername', ''),
            'booking_in_sysno' => $request->getPost('booking_in_sysno', ''),
            'bookingin_no' => $request->getPost('bookingin_no', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contractno' => $request->getPost('contractno', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'takegoodsno' =>$request->getPost('takegoodsno', ''),
            'isbusinesscheck' => $request->getPost('isbusinesscheck', ''),
            'businesschecktype' => $request->getPost('businesschecktype', ''),
            'businesscheckunitname' => $request->getPost('businesscheckunitname', ''),
            'stockinstatus' => $request->getPost('stockinstatus', '1'),
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        //若车辆信息不在数据库中，则登记车辆信息
        if($stockcarincarsdata){
            $Su = new SupplierModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $Su -> checkCardata($stockcarincarsdata);
        }

        if(count($stockcarincarsdata)>=2){
            for($i=0;$i<count($stockcarincarsdata);$i++){
                for($j=$i+1;$j<count($stockcarincarsdata);$j++){
                    if($stockcarincarsdata[$i]['carid']==$stockcarincarsdata[$j]['carid']&&
                        $stockcarincarsdata[$i]['carname']==$stockcarincarsdata[$j]['carname']){
                        COMMON::result(300, '车辆信息不能重复');
                        return;
                    }
                }
            }
        }

        $id = $S->addStockcarin($input, $stockcarindetaildata, $stockcarincarsdata,$stockinstatus);
        if ($id) {
            $row = $S->getStockcarinById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    /*
     * 车入库订单编辑数据处理
     */
    public function editJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockinstatus = $request->getPost('stockinstatus', '');
        $stockcarindetaildata = $request->getPost('stockcarindetaildata', "");
        $stockcarincarsdata = $request->getPost('stockcarincarsdata', "");
        $stockcarindetaildata = json_decode($stockcarindetaildata, true);
        $stockcarincarsdata = json_decode($stockcarincarsdata, true);
        if (count($stockcarindetaildata) == 0) {
            COMMON::result(300, '入库单明细不能为空');
            return;
        }

        if (count($stockcarincarsdata) == 0) {
            COMMON::result(300, '车辆信息不能为空');
            return;
        }

        $S = new StockcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'stockintype' => $request->getPost('stockintype', '2'),
            'stockindate' => $request->getPost('stockindate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customername' => $request->getPost('obj_customername', ''),
            'booking_in_sysno' => $request->getPost('booking_in_sysno', ''),
            'bookingin_no' => $request->getPost('bookingin_no', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contractno' => $request->getPost('contractno', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'takegoodsno' =>$request->getPost('takegoodsno', ''),
            'isbusinesscheck' => $request->getPost('isbusinesscheck', ''),
            'businesschecktype' => $request->getPost('businesschecktype', ''),
            'businesscheckunitname' => $request->getPost('businesscheckunitname', ''),
            'stockinstatus' => $request->getPost('stockinstatus', ''),
            'updated_at' => '=NOW()'
        );

        //若车辆信息不在数据库中，则登记车辆信息
        if($stockcarincarsdata){
            $Su = new SupplierModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $Su -> checkCardata($stockcarincarsdata);
        }

        if(count($stockcarincarsdata)>=2){
            for($i=0;$i<count($stockcarincarsdata);$i++){
                for($j=$i+1;$j<count($stockcarincarsdata);$j++){
                    if($stockcarincarsdata[$i]['carid']==$stockcarincarsdata[$j]['carid']&&
                        $stockcarincarsdata[$i]['carname']==$stockcarincarsdata[$j]['carname']){
                        COMMON::result(300, '车辆信息不能重复');
                        return;
                    }
                }
            }
        }

        $flag = $S->updateStockcarin($id, $input, $stockcarindetaildata, $stockcarincarsdata,$stockinstatus);
        if ($flag) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $S->getStockcarinById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    /*
     * 车入库订单删除数据处理
     */
    public function delJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);

        $S = new StockcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $P = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $isexistp = $P->getPoundinfoByScid($id);
        if(!empty($isexistp)){
            COMMON::result(300, '已存在磅码单无法删除');
            return;
        }

        $input = array(
            'isdel' => 1
        );
        if ($S->getStockcarinById($id)) {
            $data = $S->getStockcarinById($id);
            if ($data['stockinstatus'] == 4) {
                COMMON::result(300, '已完成订单不能删除');
                return;
            }
        }

        $stockcarindetaildata = $S->getStockcarindetailById($id);

        if ($S->delStockcarin($id, $input,$stockcarindetaildata)) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败');
        }
    }

    /*
     * 车入库订单明细编辑显示页面
     */
    public function detailEditAction()
    {
        $request = $this->getRequest();
        $cid = $request->getParam('cid', '0');

        $params = $request->getRequest();

        $Quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodsqualitylist'] =  $Quality->getQualityData();

        $this->getView()->make('stockcarin.detailedit', $params);

    }

    /*
     * 车入库订单车辆信息添加 编辑显示页面
     */
    public function stockcarineditcarsdetailAction()
    {
        $request = $this->getRequest();
        $cid = $request->getParam('cid', '0');
        $handlestatus = $request->getParam('handlestatus','0');

        $params = $request->getPost('selectedDatasArray',array());
        $params['customer_sysno'] = $cid;
        $params['handlestatus'] = $handlestatus;

        $this->getView()->make('stockcarin.caredit', $params);
    }

    /*
     * 车入库订单明细新增显示页面
     */
    public function adddetailAction()
    {
        $request = $this->getRequest();
        $cid = $request->getParam('cid', '0');

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $S = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStoragetank($search);
        $params['storagetanklist'] = $list['list'];

        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $quality->getList($search, 99, 1);
        $params['goodsqualitylist'] = $list['list'];

        $params['customer_sysno'] = $cid;

        $this->getView()->make('stockcarin.stockcarinadddetail', $params);
    }

    /*
     * 车入库订单明细新增数据处理
     */
    public function adddetailJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');

        $S = new StockcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getStockcarindetailById($id);

        if ($id && !empty($list)) {

            foreach ($list as $key => $value) {
                $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $data = $quality->getQualityById($value['goods_quality_sysno']);
                $list[$key]['goods_quality_name'] = $data['qualityname'];
                $list[$key]['obj.goods_sysno'] = $value['goods_sysno'];
                $list[$key]['obj.goodsname'] = $value['goodsname'];
                $list[$key]['obj.carid'] = $value['carid'];
            }
        }
        echo json_encode($list);
    }

}
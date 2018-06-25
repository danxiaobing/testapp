<?php
/**
 * 待退货车辆
 */

class ThcarController extends Yaf_Controller_Abstract
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
    }

    /**
     * 待退回车辆列表页面
     * @return void
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
        $this->getView()->make('thcar.thcarlist', $params);

    }

    /**
     *获取待退货车列表数据
     */
    public function getListJsonAction()
    {
        $request = $this->getRequest();
        $search = array(
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'carid' => $request->getPost('carid', 0),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );

        $S = new ThcarModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchThCar($search);
        echo json_encode($list);
    }

    /*
     * 退货磅码单列表页面
     */
    public function poundslistAction()
    {
        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status'=>1,
            'page'=>false
        );
        $customerdata = $C->searchCustomer($search);
        $params['customerlist'] = $customerdata['list'];
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();
        $tank = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $tankdata = $tank->searchStoragetank($search);
        $params['tanklist'] = $tankdata['list'];
        return $this->getView()->make('thcar.poundsthlist',$params);
    }

    /*
     * 获取退货磅码单列表数据
     */
    public function poundsthlistJsonAction()
    {
        $request = $this->getRequest();
        $search = array(
            'begin_time'=>$request->getPost('startDate',''),
            'end_time'=>$request->getPost('endDate',''),
            'poundsinno' =>$request->getPost('poundsinno',''),
            'carid'=>$request->getPost('carid',''),
            'customer_sysno' =>  $request->getPost('customer_sysno',''),
            'stockrebackno' =>  $request->getPost('stockrebackno',''),
            'goodsname' => $request->getPost('goodsname',''),
            'status' => $request->getPost('status',''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );

        $B = new ThcarModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $result = $B->getPoundscaroutList($search);
        echo json_encode($result);
    }

    /**
     * 退货磅码单编辑页面
     */
    public function EditAction(){
        $request = $this->getRequest();
        $id = $request -> getParam('id', 0);
        $mode= $request -> getParam('mode', '');
        $data = $request -> getPost('data', []);
//        echo "<pre>";var_dump($data);exit;
        $Th = new ThcarModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        if(!$id){
            $params['data'] = $Th -> getpoundrebackbyoutid($data['poundsout_sysno']);
            if($params['data']['issavepoundsreback'] == 1){
                COMMON::result(300, '该出库磅码单已生成退货磅码单');
                return ;
            }
            $params['mode'] = 'new';
            $params['action'] = '/Thcar/newjson';

        }else{
            $params['id'] = $id;
            $params['data'] = $Th -> getpoundsrebackbyid($id);
            $params['mode'] = $mode;
            if($mode == 'edit'){
                $params['action'] = '/Thcar/editjson';
            }elseif($mode == 'ablish'){
                $params['action'] = '/Thcar/ablishjson';
            }


            $Sout = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
            $booking['sysno'] = $id;
            $booking['isqualitycheck'] = 1;
            $pbqData = $Sout->getPBQ($booking, 10);
            if($pbqData){
                $params['qualitycheck'] = empty($pbqData['qualitycheck'][0]['qualitycheck_sysno'])? json_encode([]):json_encode($pbqData['qualitycheck']);
            }else{
                $params['qualitycheck'] = json_encode([]);
            }
        }

        $C = new CraneModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search= array(
            'bar_status'=>1,
            'page'=>false
        );
        $craneList = $C->searchCrane($search);
        $params['craneList'] = $craneList['list'];
        $user = Yaf_Registry::get(SSN_VAR);
        $params['data']['create_username'] = $user['realname'];
        $userInstance = new UserModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['customerlist'] = $userInstance -> getUserList();
        $this->getView()->make('thcar.thcaredit', $params);
    }

    /**
     * 生成退货磅码单获取退货订单明细数据
     */
    public function getstockbackdetailjsonAction(){
        $request = $this->getRequest();
        $id = $request -> getParam('id', 0);
        $Th = new ThcarModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $result = $Th->getstockbackdetailbyid($id);
        echo json_encode($result);
    }

    /*
     * 退货磅码单获取退回磅码单明细数据
     */
    public function getpoundrebackdetailjsonAction(){
        $request = $this->getRequest();
        $id = $request -> getParam('id', 0);
        $Th = new ThcarModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $result = $Th->getpoundsrebackdetailbyid($id);
        echo json_encode($result);
    }


    /**
     * 新建退货磅码单
     */
    public function newjsonAction(){
        $request = $this->getRequest();
        $outcardetaildata = $request->getPost('outcardetaildata', []);
        $outcardetaildata = json_decode($outcardetaildata, true);
//        echo "<pre>";var_dump($outcardetaildata); exit();
        $input = array(
            'poundsinno' =>COMMON::getCodeId('B5'),
            'stockin_sysno' => $request->getPost('stockreback_sysno',''),
            'stockinno' => $request->getPost('stockrebackno',''),
            'stockindetail_sysno' => $request->getPost('stockoutdetail_sysno',''),
            'loadometer' => $request->getPost('loadometer', ''),
            'storagetank_sysno' => $request->getPost('storagetank_sysno', ''),
            'storagetankname' => $request->getPost('storagetankname', ''),
            'cranename'  => $request->getPost('cranename',''),
            'carid' => $request->getPost('carid', ''),
            'carname' => $request->getPost('carname', ''),
            'mobilephone' => $request->getPost('mobilephone', ''),
            'idcard' => $request->getPost('idcard', ''),
            'customername' => $request->getPost('customername',''),
            'goods_sysno' => $request->getPost('goods_sysno',''),
            'goodsname' => $request->getPost('goodsname',''),
            'unloadnumber' =>  $request->getPost('unloadnumber',''),
            'deliverycompany' => $request->getPost('takegoodscompany',''),
            'poundsout_sysno'=> $request->getPost('poundsout_sysno', 0),
            'status' => 1,
            'isdel' => 0,
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
            'poundsinstatus' => 2,
            'create_username' =>  $request->getPost('create_username',''),
            'takegoodsno' => $request->getPost('takegoodsno',''),
            'isqueue' => $request->getPost('isqueue',''),
            'memo' => $request->getPost('memo',''),
        );

//        echo "<pre>";var_dump($input); exit();
        $Th = new ThcarModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $result = $Th -> addPoundsTh($input,$outcardetaildata);
        //var_dump($result);die;
        if(!$result){
            COMMON::result(300, '核单失败');
            return ;
        }else{
            COMMON::result(200, '核单成功');
            return ;
        }
    }

    /**
     * 编辑退货磅码单
     */
    public function editjsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('poundsreback_id','');
        $input = array(
            'storagetank_sysno' => $request->getPost('storagetank_sysno', ''),
            'storagetankname' => $request->getPost('storagetankname', ''),
            'cranename'  => $request->getPost('cranename',''),
            'unloadnumber' =>  $request->getPost('unloadnumber',''),
            'create_username' =>  $request->getPost('create_username',''),
            'updated_at' => '=NOW()',
        );

//         echo "<pre>";var_dump($input); exit();
        $Th = new ThcarModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $result = $Th -> updatePoundsTh($id,$input);
        //var_dump($result);die;
        if(!$result){
            COMMON::result(300, '更新失败');
        }else{
            COMMON::result(200, '更新成功');
        }
    }

    /*
     * 删除退货磅码单
     */
    public function deljsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id','');
        $input = array(
            'isdel' =>  1,
            'updated_at' => '=NOW()',
        );
        $Th = new ThcarModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $result = $Th -> delPoundsTh($id,$input);
        if(!$result){
            COMMON::result(300, '删除失败');
        }else{
            COMMON::result(200, '删除成功');
        }
    }

    /*
     * 作废退货磅码单
     */
    public function ablishjsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('poundsreback_id','');
        $abandonreason = $request->getPost('abandonreason','');
        $poundsthstatus = $request->getPost('poundsinstatus','');
        $outcardetaildata = $request->getPost('outcardetaildata', []);
        $outcardetaildata = json_decode($outcardetaildata, true);
        $pounds = array(
            'poundsinstatus' => 5,
            'abandonreason' => $abandonreason,
        );
        $Th = new ThcarModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $res = $Th->poundsVoid($id,$pounds,$outcardetaildata, $poundsthstatus);

        if($res['code']==200){
            COMMON::result(200,'作废成功!');
        }else{
            COMMON::result(300,$res['message']);
        }
    }

    /**
     * 导出退货磅码单EXCEL
     */
    public function ExcelAction() {
        $request = $this->getRequest();
        $search = array (
            'begin_time' => $request->getPost('startDate',''),
            'end_time' => $request->getPost('endDate',''),
            'poundsinno' =>$request->getPost('poundsinno',''),
            'carid' =>  $request->getPost('carid',''),
            'customer_sysno' =>  $request->getPost('customer_sysno',''),
            'stockrebackno' =>  $request->getPost('stockrebackno',''),
            'status' =>  $request->getPost('status',''),
            'page'=>false,
        );

        $Th = new ThcarModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $result = $Th->getPoundscaroutList($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("退货磅码单列表")
            ->setSubject("列表")
            ->setDescription("退货磅码单列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '单据编号'),
            array('B1:B1', 'B1', '005E9CD3', '客户'),
            array('C1:C1', 'C1', '005E9CD3', '发货公司'),
            array('D1:D1', 'D1', '005E9CD3', '地磅编号'),
            array('E1:E1', 'E1', '005E9CD3', '车牌号'),
            array('F1:F1', 'F1', '0094CE58', '储罐号'),
            array('G1:G1', 'G1', '0094CE58', '品名'),
            array('H1:H1', 'H1', '005E9CD3', '计量单位'),
            array('I1:I1', 'I1', '005E9CD3', '预卸数量'),
            array('J1:J1', 'J1', '0094CE58', '实际重量'),
            array('K1:K1', 'K1', '003376B3', '车辆核对'),
            array('L1:L1', 'L1', '0094CE58', '品检结果'),
            array('M1:M1', 'M1', '0094CE58', '单据状态'),
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

        foreach ($result['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['poundsinno'];
                        break;
                    case 1:
                        $value = $item['customername'];
                        break;
                    case 2:
                        $value = $item['deliverycompany'];
                        break;
                    case 3:
                        $value = $item['loadometer'];
                        break;
                    case 4:
                        $value = $item['carid'];
                        break;
                    case 5:
                        $value = $item['storagetankname'];
                        break;
                    case 6:
                        $value = $item['goodsname'];
                        break;
                    case 7:
                        $value = 'kg';
                        break;
                    case 8:
                        $value = $item['unloadnumber'];
                        break;
                    case 9:
                        $value = $item['beqty'];
                        break;
                    case 10:
                        if ($item['carcheck']==0) {
                            $value = "待核对";
                        }else if ($item['carcheck']==1) {
                            $value = "审核通过";
                        }else if ($item['carcheck']==2) {
                            $value = "车辆退回";
                        }
                        break;
                    case 11:
                        if ($item['quaulitycheck']==0) {
                            $value = "待品检";
                        }else if ($item['quaulitycheck']==1) {
                            $value = "合格";
                        }else if ($item['quaulitycheck']==2) {
                            $value = "让步通过";
                        }else if ($item['quaulitycheck']==3) {
                            $value = "不合格";
                        }
                        break;
                    case 12:
                        if ($item['poundsinstatus']==1) {
                            $value = "新建";
                        }else if ($item['poundsinstatus']==2) {
                            $value = "核单完成";
                        }else if ($item['poundsinstatus']==3) {
                            $value = "空车过磅";
                        }else if ($item['poundsinstatus']==4) {
                            $value = "重车过磅";
                        }else if ($item['poundsinstatus']==5) {
                            $value = "作废";
                        }
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="当前退货磅码单查询.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

}
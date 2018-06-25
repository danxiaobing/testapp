<?php
/**
 * @Author: danxiaobing
 * @Date:   2017-2-14
 * @Last Modified by:   danxiaobing
 * @Last Modified time: 2016-2-14
 */
class BookoutcarsController extends Yaf_Controller_Abstract
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
        $this->request = $this->getRequest();
    }

    /**
     * 车出库数据列表页面
     * @author Dujiangjiang
     * @return void
     */
    public function carOutListAction()
    {
        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status'=>1,
            'page'=>false
        );
        $customerdata = $C->searchCustomer($search);
        $params['customerlist'] = $customerdata['list'];
        $this->getView()->make('bookoutcars.outcarslist', $params);

    }

    /**
     *获取车出库列表信息
     */
    public function getListJsonAction()
    {
        $search = array(
            'begin_time' => $this->request->getPost('begin_time', ''),
            'end_time' => $this->request->getPost('end_time', ''),
            'customername' => $this->request->getPost('customername', ''),
            'carid' => $this->request->getPost('carid', 0),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );

        $S = new BookoutcarsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchBookOutCar($search);
        echo json_encode($list);
    }

    /**
     * 新建车出库单 审核页面
     */
    public function carEditAction(){
        $params = [];
        $sysnoList = $this -> request -> getPost('sysnoList', []);

        if(!empty($sysnoList)){
            $params['poundsoutno'] = COMMON::getCodeId('B3');
            #根据车牌号 获取车主信息

            #获取车辆轴数
            $search= array(
                'page'=>false
            );
            //获取所有车限
            $S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $carInfolist = $S->searchCarInfoList($search);
            $params['carInfolist'] = $carInfolist['list'];

            //获取核单信息
            $bookOutCarInstance = new BookoutcarsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
            $tiDanList = $bookOutCarInstance -> getBookOutcarByIds($sysnoList[0][0],$sysnoList[0][1]);
            foreach ($tiDanList as $key => $value) {
                if(floatval($value['beqty'])-floatval($value['tobeqty'])<0) {
                    $info = $tiDanList[$key];
                    break;
                }else
                {
                    continue;
                }
            }
            if(empty($info)) {
                $info = $tiDanList[0];
            }
            $params['tiDanList'] = $info;

            //获取储罐
            $stankarr = array();
            foreach($sysnoList as $key=>$value ){
                $stockout_mind[] = $value[4];
            }
            $stockout_sysno = implode(',',$stockout_mind);
            $pendCarInInstance = new PendcarinModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $storagetankList = $pendCarInInstance -> getOuttankMsg($stockout_sysno);
            foreach($storagetankList as $key=>$value){
                if(!in_array($value['storagetank_sysno'],$stankarr)){
                    $stankarr[$key]=$value['storagetank_sysno'];
                }else{
                    unset($storagetankList[$key]);
                }
            }
            $params['storagetankList'] = $storagetankList;

            $params['sourcestoragetank_sysno'] = $tiDanList[0]['storagetank_sysno'];
            $sure_storagetamk_sysno = [];
            foreach($tiDanList as $key => $value){
                if(!in_array($value['storagetank_sysno'], $sure_storagetamk_sysno)){
                    $sure_storagetamk_sysno[$key] = $value['storagetank_sysno'];
                }
            }
            $params['sure_storagetamk_sysno'] = implode(",", $sure_storagetamk_sysno);

            $detaildata = array();
            foreach ($sysnoList as $key => $value) {
                $outDetail = $bookOutCarInstance -> getBookOutcarById($value[0],$value[1]);
                $detaildata = array_merge($detaildata, $outDetail);
            }
            foreach ($detaildata as $v){
                $params['tiDanList']['reallynumber'] += $v['tobeqty'];
            }
        }
        if(!$detaildata){
            COMMON::result('300', '该货物已提完');
            return false;
        }
        $C = new CraneModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search['bar_status'] = 1;
        $craneList = $C->searchCrane($search);
        $params['craneList'] = $craneList['list'];
        $user = Yaf_Registry::get(SSN_VAR);
        $params['tiDanList']['create_username'] = $user['realname'];
        $userInstance = new UserModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['customerlist'] = $userInstance -> getUserList();
        $params['detailData'] = json_encode($detaildata);
        $params['type'] = $this->request->getPost('type',false);
        $this->getView()->make('bookoutcars.outcarsedit', $params);
    }

    /**
     * 根据车轴获取车限
     */
    public function getCarloadWeightJsonAction(){
        $sysno = $this->request -> getPost('sysno', 0);
        if($sysno == 0){
            COMMON::result(0, '请检选择车辆轴数');die;

        }
        $S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $carInfolist = $S->getCarinfoById($sysno);
        $carInfolist['carloadweight'] = $carInfolist['carloadweight']*1000;
        COMMON::result(200, '', $carInfolist);die;
    }

    /**
     * 修改提单信息VIEW
     */
    public function editTiDanAction(){
        $params['sysno'] = $this->request->getPost('sysno',0);
        $this->getView()->make('bookoutcars.editidanview', $params);
    }

    /**
     * 修改提货数量action
     */
    public function editNumAction()
    {
        $list['takeqty'] = $this->request->getPost('tihuonum',0);
        $list['sysno'] = $this->request->getPost('sysno',0);
        echo json_encode($list);
    }

    /**
     * 新建车出库单
     */
    public function insertFromDataAjaxAction(){
        $outcardetaildata = $this->request->getPost('outcardetaildata', []);
        $outcardetaildata = json_decode($outcardetaildata, true);
        $request = $this->getRequest();

        #设置提货总数
        $countTakeqty = $request->getPost('takeqty','');
        // foreach($outcardetaildata as $value){
        //     $countTakeqty += $value['takeqty'];
        // }
        count($outcardetaildata)>1 ? $customertype=2 : $customertype=1; //是否拼单
        $params = [
            'poundsoutno' => $request->getPost('poundsoutno', ''),
            'loadometer' => $request->getPost('loadometer', ''),
            'storagetank_sysno' => $request->getPost('storagetank_sysno_outcar', ''),
            'storagetankname' => $request->getPost('storagetankname_outcar', ''),
            'takeqty' => $countTakeqty,
            'loadqty' => $request->getPost('loadqty', ''),
            'car_axle_sysno' => $request->getPost('car_axle_sysno', ''),
            'cartype' => $request->getPost('cartype', 0),
            'carid' => $request->getPost('carid', ''),
            'carname' => $request->getPost('carname', ''),
            'mobilephone' => $request->getPost('mobilephone', ''),
            'idcard' => $request->getPost('idcard', ''),
            'status' => 1,
            'isdel' => 0,
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
            'stockout_sysno' => $request->getPost('stockout_sysno',''),
            'stockoutno' => $request->getPost('stockoutno',''),
            'customername' => $request->getPost('customername',''),
            'goodsname' => $request->getPost('goodsname',''),
            'deliverycompany' => $request->getPost('deliverycompany',''),
            'noticenumber' => $request->getPost('noticenumber',''),
            'poundsoutstatus' => 2,
            'stockoutdetail_sysno' => $request->getPost('stockoutdetail_sysno',''),
            'create_username' =>  $request->getPost('create_username',''),
            'takegoodsno' => $request->getPost('takegoodsno',''),
            'cranename'  => $request->getPost('cranename',''),
            'crane_sysno'  => $request->getPost('crane_sysno',''),
            'customertype' => $customertype,
            'goods_sysno' => $request->getPost('goods_sysno',''),
            'isqueue' => $request->getPost('isqueue',''),
            'memo' => $request->getPost('memo',''),
        ];


        if($this->request->getPost('cartype', 0) ==2){
            if($this->request->getPost('cabin', 0) == 1){
                $params['frontcabin'] =  $this->request->getPost('frontcabin_num', 0);
            }elseif($this->request->getPost('cabin', 0) == 2){
                $params['behindcabin'] =  $this->request->getPost('frontcabin_num', 0);
            }
        }elseif($this->request->getPost('cartype', 0) == 3) {
            $params['isbucket'] = $this->request->getPost('isbucket', 0);
            $params['loadtype'] = $this->request->getPost('loadtype', 0);
            if($params['loadtype'] == 1){
                $params['singlebucketweight'] = $this->request->getPost('singlebucketweight', 0);
                $params['totalunchanged'] = $this->request->getPost('totalunchanged', 0);
            }
            if($params['isbucket']  == 1){
                $params['bucketnumber'] = $this->request->getPost('bucketnumber', 0);
            }elseif($params['isbucket']  == 2){
                $params['emptybucketweight'] = $this->request->getPost('emptybucketweight', 0);
                $params['bucketnumber'] = $this->request->getPost('bucketnumber', 0);
                $params['totalemptybucketweight'] = $this->request->getPost('totalemptybucketweight', 0);
                    
            }
        }
        // var_dump($params); exit();
        $bookOutCarInstance = new BookoutcarsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $result = $bookOutCarInstance -> addPoundsOut($params,$outcardetaildata);
        if(!$result){
            COMMON::result(300, '核单失败');
            
        }else{
            COMMON::result(200, '核单成功');          

        }
    }


    /*
        查看磅码单list页面
     */
    public function poundsoutlistAction()
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
        return $this->getView()->make('bookoutcars.poundsoutlist',$params);
    }

    public function poundsoutlistJsonAction()
    {
        $request = $this->getRequest();

        $carid = $request->getPost('carid','');
        $begin_time = $request->getPost('startDate','');
        $end_time = $request->getPost('endDate','');
        $status = $request->getPost('status','');
        $customername = $request->getPost('customername','');
        $stockoutno = $request->getPost('stockoutno','');
        $poundsoutno = $request->getPost('poundsoutno','');
        $goods_sysno = $request->getPost('goods_sysno','');
        $storagetank_sysno = $request->getPost('storagetank_sysno','');

        $search = array(
            'carid'=>$carid,
            'customername' => $customername,
            'begin_time'=>$begin_time,
            'end_time'=>$end_time,
            'status' => $status,
            'stockoutno' => $stockoutno,
            'poundsoutno' => $poundsoutno,
            'goods_sysno' => $goods_sysno,
            'storagetank_sysno' => $storagetank_sysno,
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'orders' => $this->getRequest()->getPost('orders',''),
            );
        
        $B = new BookoutcarsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $result = $B->getPoundscaroutList($search);

        echo json_encode($result);        
    }

    public function poundsoutDetailAction()
    {
        $request = $this->getRequest();

        $id = $request->getParam('id','');

        $B = new BookoutcarsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $params = $B->getpounds($id);

        $params['void'] = $request->getParam('void',0);
        $params['viewtype'] = $request->getParam('type',0);;
        $detaildata = $B->getPoundsout_detail($id);
        #获取信息end
        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        foreach ($detaildata as $key => $value) {
            $info = $S->getStockoutById($value['stockout_sysno']);
            if($info['stockoutstatus']!=3 && $params['void']==1){ //如果出库订单的状态不是出库中不允许作废
                COMMON::result(300,'出库单'.$value['stockoutno'].'已经完成出库不能作废！');
                exit;
            }
            $carOutDetail = $B -> getCarsDetail($value['stockout_sysno'], $params['carid']);
            $detaildata[$key] = array_merge($value, $carOutDetail ? $carOutDetail : []);
        }

        $params['detailData'] = json_encode($detaildata);

        $G = new GoodsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $goods = $G->getAttributeByGoodsName($params['goodsname']);
        $params['density'] = $goods['density'];

        $Company = new PrinttitleModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $companyname  = $Company->getDefault();
        $params['companyname']  = $companyname['titlename'];

        $this->getView()->make('bookoutcars.poundsoutdetail',$params);
    }

    public function PoundscaroutdelAction()
    {
        $id = $this->getRequest()->getParam('id','');
        // var_dump($id);
        $P = new BookoutcarsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $data = array(
            'isdel' => 1,
            );

        $res = $P->updatePounds($data,$id);


        if($res){
            $carCheckInstance = new CarcheckModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
            $carCheckInstance -> delCarcheck(['isdel' => 1], $id, 10);
            COMMON::result(200,'删除成功!');
        }else{
            COMMON::result(300,'删除失败!');
        }        
    }
    public function PoundscaroutbackAction()
    {
        $id = $this->getRequest()->getParam('id','');
        // var_dump($id);
        $P = new BookoutcarsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $data = array(
            'poundsoutstatus' => 6,
        );

        $res = $P->updatePounds($data,$id);


        if($res){
            COMMON::result(200,'退单成功!');
        }else{
            COMMON::result(300,'退单失败!');
        }
    }

    /**
     * 作废出库磅码单
     * @author  hr 
     */
    public function poundsoutVoidAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id','');
        $abandonreason = $request->getPost('memo','');
        $poundsoutstatus = $request->getPost('status','');
        $carid = $request->getPost('carid','');
        $storagetank_sysno = $request->getPost('storagetank_sysno_detail','');
//        $stockout_sysno = $request->getPost('stockout_sysno', '');
//        $stockoutdetail_sysno = $request->getPost('stockoutdetail_sysno','');
        $detailData = json_decode($request->getPost('detailData',[]),true);
        #获取信息end
        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        // $stockoutstatus = $info['stockoutstatus'];
        // $customer_sysno = $info['customer_sysno'];
        foreach ($detailData as $key => $value) {
            $info = $S->getStockoutById($value['stockout_sysno']);
            if($info['stockoutstatus']!=3){ //如果出库订单的状态不是出库中不允许作废
                COMMON::result(300,'出库单'.$value['stockoutno'].'已经完成出库不能作废！');
                exit;
            }
        }
/*        $P = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $storagetank_info = $P->getStoragetankInfo($storagetank_sysno); //获取储罐的带出量，现存量
*/
        $pounds = array(
            'poundsoutstatus' => 5,
            'abandonreason' => $abandonreason,
            'carid' => $carid,
            );
        // $beqty = floor($num)/1000;
        $B = new BookoutcarsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $res = $B->poundsVoid($pounds,$id,$detailData, $poundsoutstatus);
        // var_dump($res);
        // exit();

        if($res['code']==200){
            #库存管理业务操作日志
            // $stockmarks =  '作废出库磅码单';
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
            $data= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  8,
                'opertype'  => 4,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  $abandonreason,
            );
            $S->addDocLog($data);
            #库存管理业务操作日志end
            //储罐日志
            if($poundsoutstatus !=3 ){
                $storagetank = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
                foreach ($detailData as $value){
                    $data = array(
                        'storagetank_sysno' => $storagetank_sysno,
                        'doc_sysno' => $value['stockout_sysno'],
                        'docno' => $value['stockoutno'],
                        'doctype' => 4,
                        'pounds_sysno' => $id,
                        'poundsno' => $request->getPost('poundsoutno',''),
                        'pounds_type' => 2,
                        'beqty' => sprintf('%.3f', $value['realnumber']/1000),
                    );
                    $storagetank->addStoragetankLog($data);
                }
            }
            COMMON::result(200,'作废成功!');
        }else{
            COMMON::result(300,$res['message']);
        }
    }

    //获取储罐的可存放量
    public function ajaxgetTank_stockqtyAction()
    {
        $storagetank_sysno = $this->getRequest()->getPost('storagetank_sysno','');
        $B = new BookoutcarsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        echo $B->getank_stockqty($storagetank_sysno);
    }

   /**
    * 查看车出库磅码单导出
    */
    public function ExcellistAction()
    {
        $request = $this->getRequest();

        $carid = $request->getPost('pounds_carid','');
        $begin_time = $request->getPost('startDate','');
        $end_time = $request->getPost('endDate','');
        $status = $request->getPost('status','');
        $customername = $request->getPost('customername','');
        $stockoutno = $request->getPost('stockoutno','');
        $poundsoutno = $request->getPost('poundsoutno','');

        $search = array(
            'carid'=>$carid,
            'customername' => $customername,
            'begin_time'=>$begin_time,
            'end_time'=>$end_time,
            'status' => $status,
            'stockoutno' => $stockoutno,
            'poundsoutno' => $poundsoutno,
            'page' => false,
            'orders' => $this->getRequest()->getPost('orders',''),
            );

        $B = new BookoutcarsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $result = $B->getPoundscaroutList($search);

        // var_dump($result);exit();
        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("国烨云仓")
            ->setTitle("车出库磅码单列表")
            ->setSubject("列表")
            ->setDescription("车出库磅码单列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '单据编号'),
            array('B1:B1', 'B1', '005E9CD3', '车牌号'),
            array('C1:C1', 'C1', '005E9CD3', '客户'),
            array('D1:D1', 'D1', '0094CE58', '地磅编号'),
            array('E1:E1', 'E1', '0094CE58', '储罐号'),
            array('F1:F1', 'F1', '0094CE58', '槽车类型'),
            array('G1:G1', 'G1', '0094CE58', '品名'),
            array('H1:H1', 'H1', '003376B3', '计量单位'),
            array('I1:I1', 'I1', '003376B3', '预提数量(kg)'),
            array('J1:J1', 'J1', '0094CE58', '通知装货数(kg)'),
            array('K1:K1', 'K1', '003376B3', '实际重量(kg)'),
            array('L1:L1', 'L1', '003376B3', '单据状态'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('车入库磅码单列表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I','J','K','L');

        $poundsoutstatus = array();
        $poundsoutstatus['1'] = '新建';
        $poundsoutstatus['2'] = '核单完成';
        $poundsoutstatus['3'] = '空车过磅';
        $poundsoutstatus['4'] = '重车过磅';
        $poundsoutstatus['5'] = '作废';

        $cartype = array();
        $cartype['1'] = '槽车';
        $cartype['2'] = '隔舱车';
        $cartype['3'] = '桶车';

        foreach ($result['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                            $value = $item['poundsoutno'];
                        break;
                    case 1:
                        $value = $item['carid'];
                        break;
                    case 2:
                        $value = $item['customername'];
                        break;
                    case 3:
                        $value = $item['loadometer'];

                        break;
                    case 4:
                        $value = $item['storagetankname'];
                        break;
                    case 5:
                        $value = $cartype[$item['cartype']];
                        break;
                    case 6:
                        $value = $item['goodsname'];
                        break;
                    case 7:
                        $value = 'kg';
                        break;
                    case 8:
                        $value = $item['takeqty'];
                        break;
                    case 9:
                        $value = $item['noticenumber'];
                        break;
                    case 10:
                        $value = $item['beqty'];
                        break;
                    case 11:
                        $value = $poundsoutstatus[$item['poundsoutstatus']];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="车出库磅码单列表.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }


    public function bookoutcarsdetaileditAction()
    {
        $params = $this->request->getRequest();

        return $this->getView()->make('bookoutcars.detailedit',$params);
    }

    //出库磅码单编辑
    public function PoundscarouteditAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('poundid',0);

        $B = new BookoutcarsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $tiDanList= $B->getpounds($id);
        // var_dump($tiDanList);exit;
        $params['tiDanList'] = $tiDanList;
        $params['poundsoutno'] = $tiDanList['poundsoutno'];
        $params['id'] = $id;
        $params['edit'] = true;
        $detaildata = $B->getPoundsout_detail($id);
        foreach ($detaildata as $key => $value) {
            $params['tiDanList']['realnumber'] += $value['realnumber'];
            $params['tiDanList']['reallynumber'] += ($value['tobeqty']);
            $carOutDetail = $B -> getCarsDetail($value['stockout_sysno'], $tiDanList['carid']);
            $detaildata[$key] = array_merge($value, $carOutDetail);
        }
        $params['detailData'] = json_encode($detaildata);
        #获取车辆轴数
        $search= array(
            'page'=>false
        );
        //获取所有车限
        $S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $carInfolist = $S->searchCarInfoList($search);
        $params['carInfolist'] = $carInfolist['list'];


        $Goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $goodsInfo = $Goods->getBaseGoods(array('goodsname'=>$tiDanList['goodsname'],'page'=>false));


        $goods_sysno = $goodsInfo['list'][0]['sysno'];

        //获取储罐
        $pendCarInInstance = new PendcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $storagetankList = $pendCarInInstance -> getOutStoragetanks($goods_sysno);

        $params['storagetankList'] = $storagetankList;

        $params['sourcestoragetank_sysno'] = $tiDanList['storagetank_sysno'];

        $sure_storagetamk_sysno[] = $tiDanList['storagetank_sysno'];

        $params['sure_storagetamk_sysno'] = implode(",", $sure_storagetamk_sysno);

        $userInstance = new UserModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['customerlist'] = $userInstance -> getUserList();

        $G = new GoodsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $C = new CraneModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $goods = $G->getAttributeByGoodsName($params['goodsname']);
        $params['density'] = $goods['density'];
        $craneList = $C->searchCrane($search);
        $params['craneList'] = $craneList['list'];
        $this->getView()->make('bookoutcars.outcarsedit',$params);
    }


    public function AjaxpoundouteditAction()
    {
        $outcardetaildata = $this->request->getPost('outcardetaildata', []);
        $outcardetaildata = json_decode($outcardetaildata, true);
        $request = $this->getRequest();

        $id = $request->getPost('poundout_id',0);

        #设置提货总数
        $countTakeqty = $request->getPost('takeqty','');
        // foreach($outcardetaildata as $value){
        //     $countTakeqty += $value['takeqty'];
        // }
        count($outcardetaildata)>1 ? $customertype=2 : $customertype=1; //是否拼单
        
        $params = [
            'loadometer' => $request->getPost('loadometer', ''),
            'storagetank_sysno' => $request->getPost('storagetank_sysno_outcar', ''),
            'storagetankname' => $request->getPost('storagetankname_outcar', ''),
            'takeqty' => $countTakeqty,
            'loadqty' => $request->getPost('loadqty', ''),
            'car_axle_sysno' => $request->getPost('car_axle_sysno', ''),
            'cartype' => $request->getPost('cartype', 0),
            'updated_at' => '=NOW()',
            'stockout_sysno' => $request->getPost('stockout_sysno',''),
            'stockoutno' => $request->getPost('stockoutno',''),
            'customername' => $request->getPost('customername',''),
            'goodsname' => $request->getPost('goodsname',''),
            'deliverycompany' => $request->getPost('deliverycompany',''),
            'noticenumber' => $request->getPost('noticenumber',''),
            'stockoutdetail_sysno' => $request->getPost('stockoutdetail_sysno',''),
            'takegoodsno' => $request->getPost('takegoodsno',''),
            'create_username' =>  $request->getPost('create_username',''),
            'cranename'  => $request->getPost('cranename',''),
            'memo'  => $request->getPost('memo',''),
            'poundsoutno'  => $request->getPost('poundsoutno',''),
            'carid'  => $request->getPost('carid',''),
            'carname'  => $request->getPost('carname',''),
            'mobilephone'  => $request->getPost('mobilephone',''),
            'idcard'  => $request->getPost('idcard',''),
            'customertype' => $customertype,
        ];

        if($this->request->getPost('cartype', 0) ==2){
            if($this->request->getPost('cabin', 0) == 1){
                $params['frontcabin'] =  $this->request->getPost('frontcabin_num', 0);
            }elseif($this->request->getPost('cabin', 0) == 2){
                $params['behindcabin'] =  $this->request->getPost('frontcabin_num', 0);
            }
        }elseif($this->request->getPost('cartype', 0) == 3) {
            $params['isbucket'] = $this->request->getPost('isbucket', 0);
            $params['loadtype'] = $this->request->getPost('loadtype', 0);
            if($params['loadtype'] == 1){
                $params['singlebucketweight'] = $this->request->getPost('singlebucketweight', 0);
                $params['totalunchanged'] = $this->request->getPost('totalunchanged', 0);
            }
            if($params['isbucket']  == 1){
                $params['bucketnumber'] = $this->request->getPost('bucketnumber', 0);
            }elseif($params['isbucket']  == 2){
                $params['emptybucketweight'] = $this->request->getPost('emptybucketweight', 0);
                $params['bucketnumber'] = $this->request->getPost('bucketnumber', 0);
                $params['totalemptybucketweight'] = $this->request->getPost('totalemptybucketweight', 0);

            }
        }

        $bookOutCarInstance = new BookoutcarsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $result = $bookOutCarInstance -> updatePoundsOut($params,$id,$outcardetaildata);
        if($result['code']!=200){
            COMMON::result(300, $result['message']);

        }else{
            COMMON::result(200, $result['message']);

        }
    }

}
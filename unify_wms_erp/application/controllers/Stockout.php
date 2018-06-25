<?php

/**
 * 库存查询
 * User: josy
 * Date: 2016/11/22 0022
 * Time: 9:13
 */
class StockoutController extends Yaf_Controller_Abstract
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

    public function carlistAction()
    {
        $params = array(
            'stockouttype' => 2
        );
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();
        $this->getView()->make('stockcarout.list', $params);
    }

    public function shiplistAction()
    {
        $params = array(
            'stockouttype' => 1
        );
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();
        $this->getView()->make('stockshipout.list', $params);
    }

    public function pipelineListAction()
    {
        $params = array(
            'stockouttype' => 3
        );

        $this->getView()->make('stockoutpipeline.list', $params);
    }

    public function carlistJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'bar_stockoutstatus' => $request->getPost('bar_stockoutstatus', '-100'),
            'bar_receivenumber' => $request->getPost('bar_receivenumber',''),
            'bar_goodsname' => $request->getPost('bar_goodsname',''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'orders' => 'created_at desc',
            'stockouttype' => 2,
        );

        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStockout($search);
        foreach ($list['list'] as $key => $value) {
            $list['list'][$key]['unitname'] = '吨';
        }
        echo json_encode($list);

    }

    public function shiplistJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'bar_stockoutstatus' => $request->getPost('bar_stockoutstatus', '-100'),
            'bar_receivenumber' => $request->getPost('bar_receivenumber',''),
            'bar_goodsname' => $request->getPost('bar_goodsname',''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'orders' => 'created_at desc',
            'stockouttype' => 1,
        );

        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStockout($search);
        foreach ($list['list'] as $key => $value) {
            $list['list'][$key]['unitname'] = '吨';
        }
        echo json_encode($list);

    }

    public function pipelineListJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'bar_stockoutstatus' => $request->getPost('bar_stockoutstatus', '-100'),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'orders' => 'created_at desc',
            'stockouttype' => 3,
        );

        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStockout($search);
        echo json_encode($list);

    }

    public function pipelineAuditAction() {
        $params = array(

        );

        $this->getView()->make('stockoutpipeline.auditlist',$params);
    }

    public function pipelineAuditListJsonAction() {
        $request = $this->getRequest();

        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'bar_name' => $request->getPost('bar_name',''),
            'bar_stockoutstatus'=> 3,
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),
            'orders'  => 'created_at desc',
            'stockouttype' => 3,
        );

        echo json_encode($list);

    }

    //船出待审核列表
    public function shipcheckAction() {
        $params = array(

        );
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();
        $this->getView()->make('stockshipout.auditlist',$params);
    }

    public function shipchecklistJsonAction() {
        $request = $this->getRequest();

        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'bar_name' => $request->getPost('bar_name',''),
            'bar_receivenumber' => $request->getPost('bar_receivenumber',''),
            'bar_goodsname' => $request->getPost('bar_goodsname',''),
            'bar_stockoutstatus'=> $request->getParam('bar_stockoutstatus',3),
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),
            'orders'  => 'created_at desc',
            'stockouttype' => 1,
        );

        $S = new StockoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $S->searchStockout($search);
        foreach ($list['list'] as $key => $value) {
            $list['list'][$key]['unitname'] = '吨';
        }
        echo json_encode($list);

    }

    //船出待执行列表
    public function shipExecuteAction() {
        $params = array(

        );
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();
        $this->getView()->make('stockshipout.executelist',$params);
    }

    public function shipExecuteListJsonAction() {
        $request = $this->getRequest();

        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'bar_name' => $request->getPost('bar_name',''),
            'bar_receivenumber' => $request->getPost('bar_receivenumber',''),
            'bar_goodsname' => $request->getPost('bar_goodsname',''),
            'bar_stockoutstatus'=> $request->getParam('bar_stockoutstatus',8),
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),
            'orders'  => 'created_at desc',
            'stockouttype' => 1,
        );

        $S = new StockoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $S->searchStockout($search);
        foreach ($list['list'] as $key => $value) {
            $list['list'][$key]['unitname'] = '吨';
        }
        echo json_encode($list);

    }

    public function listJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'bar_stockoutstatus' => $request->getPost('bar_stockoutstatus', '-100'),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'orders' => 'created_at desc',
            'stockouttype' => $request->getPost('stockouttype', ''),
        );
        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStockout($search);

        echo json_encode($list);

    }

    public function detaillistJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $search = array(
            'stockout_sysno' => $id,
            'page' => false
        );
        $detailData = $S->getStockoutDetailList($search);
        echo json_encode($detailData['list']);
    }

    public function EditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $stockouttype = $request->getParam('stockouttype', '2');
        $booking_sysno = $request->getPost('booking_sysno', '0');
        $params['handlestatus'] = $request->getParam('handlestatus','');

        $type = $request->getParam('type', '0');

        if (!isset($id)) {
            $id = 0;
        }

        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $C = new CustomerModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $B = new BookingModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $BO = new BookoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $stock = new StockModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $I = new IntroduceModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        if (!$id) {
            $action = "/stockout/newJson/";
            $params = array();

            $params['stockouttype'] = $stockouttype;

            if ($booking_sysno) {
                $search = array(
                    'bookingout_sysno' => $booking_sysno,
                    'page' => false
                );

                $bookoutinfo = $BO->getBookoutById($booking_sysno);

                if (count($bookoutinfo) > 0) {
                    $params['takegoodsno'] = $bookoutinfo['receivenumber'];
                    $params['takegoodscompany'] = $bookoutinfo['receiveunitname'];
                    $params['shipproxyname'] = $bookoutinfo['shipproxyname'];
                    $params['businesscheckunitname'] = $bookoutinfo['businesscheckunitname'];
                    $params['takebetween'] = $bookoutinfo['receivebetween'];
                }

                $detailData = $BO->getBookDetail($search);

                if (count($detailData) > 0) {
                    foreach ($detailData as $key => $value) {
                        $stockinfo = $stock->getElementById(array($value['stock_sysno']));
                        if(!$stockinfo){
                            COMMON::result(300,'库存记录不存在');
                            return false;
                        }
                        $detailData[$key]['instockqty'] = $stockinfo[0]['instockqty'];
                        $detailData[$key]['tobeqty'] = $value['noticenum'];
                        $detailData[$key]['takeqty'] = $value['noticenum'];
                        $detailData[$key]['bookout_detail_sysno'] = $value['sysno'];
                        $detailData[$key]['beqty'] = 0;
                        $detailData[$key]['unitname'] = '吨';
                    }

                    $params['detaillist'] = json_encode($detailData);
                } else
                    $params['detaillist'] = json_encode(array());

            } else {
                $params['detaillist'] = json_encode(array());
            }
            $list = $BO->getBookoutCarByid($booking_sysno);
            $params['carlist'] = json_encode($list);

        } else {
            $action = "/stockout/editJson/";
            $params = $S->getStockoutById($id);
            $receiveend = strtotime($params['receiveend']);
            $now = strtotime(date('Y-m-d',time()));
            if ($now > $receiveend) {
                $params['receiveover'] = '是';
            }else{
                $params['receiveover'] = '否';
            }
            $search = array(
                'stockout_sysno' => $id,
                'page' => false
            );

            if ($params['stockoutstatus'] != 3 && $type != 'view' && $type != 'delay') {
                COMMON::result(300, '非出库中状态不能添加车辆！');
                return;
            }

            $bookoutDetailData = $BO->getBookDetail(array('bookingout_sysno' => $params['booking_out_sysno'],'page' => false));

            $detailData = $S->getStockoutDetailList($search);
            foreach ($detailData['list'] as $dkey => $dvalue) {
                if($dvalue['stocktype'] == 1){
                    $stockinfo = $stock->getElementById(array($dvalue['stock_sysno']));

                    if(!$stockinfo){
                        COMMON::result(300,'库存记录不存在');
                        return false;
                    }
                    $detailData['list'][$dkey]['instockqty'] = $stockinfo[0]['instockqty'];
                    $detailData['list'][$dkey]['introduceqty'] = '--';
                }elseif($dvalue['stocktype'] == 2){
                    $introduceDetailInfo = $I->getIntroduceDetailById(intval($dvalue['stock_sysno']));
                    $detailData['list'][$dkey]['instockqty'] = '--';
                    $detailData['list'][$dkey]['introduceqty'] = $introduceDetailInfo['takegoodsnum'];
                }
                
            }

            $params['detaillist'] = json_encode($detailData['list']);

            
            $carData = $S->getStockoutCarList($search);
            $params['carlist'] = json_encode($carData['list']);
            
            $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

            $attach = $A->getAttachByMAS('bookout', 'car', $params['booking_out_sysno']);
            if (is_array($attach) && count($attach)) {
                $files = array();
                foreach ($attach as $file) {
                    $files[] = $file['sysno'];
                }
                $params['uploaded'] = join(',', $files);
            }
            $params['attach'] = $attach;
        }
        
        if ($booking_sysno) {

            $booking = $B->getBookingOutById($booking_sysno);
            $params['customer_sysno'] = $booking['customer_sysno'];
            $params['customername'] = $booking['customer_name'];
            $params['booking_out_sysno'] = $booking['sysno'];
            $params['bookingoutno'] = $booking['bookingoutno'];
            $params['cs_employee_sysno'] = $booking['cs_employee_sysno'];
            $params['cs_employeename'] = $booking['cs_employeename'];
        }

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $C->searchCustomer($search);
        $params['customerlist'] = $list['list'];

        $E = new EmployeeModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];

        $params['id'] = $id;
        $params['action'] = $action;
        $params['status'] = COMMON::getCarStockOutStatus($params['stockoutstatus']);

        $Company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $companyname = $Company->getDefault();
        $params['companyname'] = $companyname['companyname'];
        $params['type'] = $type;
        $this->getView()->make('stockcarout.edit', $params);
    }


    public function addCarAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $val = $request->getParam('val','');
        $addcar = $request->getParam('addcar','1');

        $stockouttype = 2;
        $car = $request->getParam('car', '2');


        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $C = new CustomerModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $B = new BookingModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $BO = new BookoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $stock = new StockModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $params = $S->getStockoutById($id);

        if ($params['stockoutstatus'] != 3) {
            COMMON::result(300, '非出库中状态不能添加车辆！');
            return;
        }

        $receiveend = strtotime($params['receiveend']);
        $now = strtotime(date('Y-m-d',time()));
        if ($now > $receiveend) {
            $params['receiveover'] = '是';
        }else{
            $params['receiveover'] = '否';
        }

        $search = array(
            'stockout_sysno' => $params['sysno'],
            'status' => 1,
            'page' => false
        );

        $bookoutinfo = $BO->getBookoutById($params['booking_out_sysno']);
        if (count($bookoutinfo) > 0) {
            $params['takegoodsno'] = $bookoutinfo['receivenumber'];
            $params['takegoodscompany'] = $bookoutinfo['receiveunitname'];
            $params['takebetween'] = $bookoutinfo['receivebetween'];
        }

        $bookoutDetailData = $BO->getBookDetail(array('bookingout_sysno' => $params['booking_out_sysno'],'page' => false));

        $detailData = $S->getStockoutDetailList($search);
        foreach ($detailData['list'] as $dkey => $dvalue) {
            $stockinfo = $stock->getElementById(array($dvalue['stock_sysno']));

            if(!$stockinfo){
                COMMON::result(300,'库存记录不存在');
                return false;
            }
            $detailData['list'][$dkey]['instockqty'] = $stockinfo[0]['instockqty'];

            foreach ($bookoutDetailData as $bkey => $bvalue) {
                if ($dvalue['bookout_detail_sysno'] == $bvalue['sysno']) {
                    $detailData['list'][$dkey]['takeqty'] = $bvalue['bookingoutqty'];
                }
            }
        }

        $params['detaillist'] = json_encode($detailData['list']);

        $carData = $S->getStockoutCarList($search);
        $params['carlist'] = json_encode($carData['list']);

        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $attach = $A->getAttachByMAS('stockout', 'receipt', $id);
        if (is_array($attach) && count($attach)) {
            $files = array();
            foreach ($attach as $file) {
                //	$files[] =  $file['module'].'/'.$file['action'].'/'. $file['name'];
                $files[] = $file['sysno'];
            }
            $params['uploaded'] = join(',', $files);
        }
        $params['attach'] = $attach;

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );

        if ($booking_sysno) {

            $booking = $B->getBookingOutById($booking_sysno);
            $params['customer_sysno'] = $booking['customer_sysno'];
            $params['customername'] = $booking['customer_name'];
            $params['booking_out_sysno'] = $booking['sysno'];
            $params['bookingoutno'] = $booking['bookingoutno'];
            $params['cs_employee_sysno'] = $booking['cs_employee_sysno'];
            $params['cs_employeename'] = $booking['cs_employeename'];
        }

        $list = $C->searchCustomer($search);
        $params['customerlist'] = $list['list'];

        $E = new EmployeeModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];

        $params['id'] = $id;
        $params['val'] = $val;
        $params['addcar'] = $addcar;
        $params['status'] = COMMON::getCarStockOutStatus($params['stockoutstatus']);
        $params['stockouttype'] = $stockouttype;

        $Company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $companyname = $Company->getDefault();
        $params['companyname'] = $companyname['companyname'];
        $params['type'] = $type;
        $this->getView()->make('stockcarout.edit', $params);

    }

    public function addcarJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        $stockoutcardata = $request->getPost('stockoutcardata', "");
        $stockoutcardata = json_decode($stockoutcardata, true);
        if (count($stockoutcardata) == 0 && $request->getPost('stockouttype', '1') == 2) {
            COMMON::result(300, '出库车辆信息不能为空');
            return;
        }

        if(count($stockoutcardata)>=2){
            for($i=0;$i<count($stockoutcardata);$i++){
                for($j=$i+1;$j<count($stockoutcardata);$j++){
                    if($stockoutcardata[$i]['carid']==$stockoutcardata[$j]['carid']&&$stockoutcardata[$i]['carname']==$stockoutcardata[$j]['carname']){
                        echo json_encode(array('code' => 300,'msg' => '车辆信息不能重复'));die();
                    }
                }
            }
        }

        $S = new StockoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $res = $S->stockoutAddcar($id, $stockoutcardata);
        if(!$res){
            echo json_encode(array('code' => 300,'msg' => '车辆信息添加失败'));die();
        }else{
            echo json_encode(array('code' => 200,'msg' => '车辆信息添加成功'));die();
        }
    }

    public function receipteditAction()
    {
        $request = $this->getRequest();
        $cid = $request->getParam('cid', '0');
        $val = $request->getparam('val', '');
        $rtype = $request->getParam('rtype', '1');
        $S = new StockModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $search = array(
            'page' => false,
            'customer_sysno' => $cid,
            'iscurrent' => 1
        );

        //	$stockdata =  $S->stockdetail($search);
        $stockdata = $S->getList($search);


        $params['stocklist'] = json_encode($stockdata['list']);
        $params['stockouttype'] = $rtype;
        $params['val'] = $val;

        if (count($stockdata['list']) > 0) {
            $this->getView()->make('stockout.receiptedit', $params);
        } else {
            COMMON::result(300, '该客户没有库存');
        }

    }

    public function detailEditAction()
    {
        $request = $this->getRequest();
        $cid = $request->getParam('cid', '0');
        $rtype = $request->getParam('rtype', '1');

        $params = $request->getRequest();

        $params['stockouttype'] = $rtype;

        $goodsnature = $request->getRequest('goodsnature', '0');
        $goodsnature_arr = array('0' => '', '1' => '保税', '2' => '外贸', '3' => '内贸转出口', '4' => '内贸内销');
        $params['goodsnaturemark'] = $goodsnature_arr[$goodsnature];
        $this->getView()->make('stockout.detailedit', $params);

    }

    public function carEditAction()
    {
        $request = $this->getRequest();

        $id = $request->getPost('id',0 );
        $handlestatus = $request->getParam('handlestatus', '');
        $carData = $request->getPost('carData',0 );
        $search = array(
            'stockout_sysno' => $id,
            'page' => false,
        );
        $S = new StockoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $carlist = $S->getStockoutCarList($search);
        $params['handlestatus'] = $handlestatus;
        $params['carlist'] = json_encode($carlist['list']);
        $params['carData'] = $carData;

        $this->getView()->make('stockcarout.caredit', $params);

    }


    public function newJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', "");
        $stockoutdetaildata = $request->getPost('stockoutdetaildata', "");
 
        $stockoutdetaildata = json_decode($stockoutdetaildata, true);
        if (count($stockoutdetaildata) == 0) {
            COMMON::result(300, '出库单明细不能为空');
            return;
        }

        $stockoutcardata = $request->getPost('stockoutcardata', "");
        $stockoutcardata = json_decode($stockoutcardata, true);
        if (count($stockoutcardata) == 0 && $request->getPost('stockouttype', '1') == 2) {
            COMMON::result(300, '出库车辆信息不能为空');
            return;
        }

        $S = new StockoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'stockouttype' => $request->getPost('stockouttype', '1'),
            'stockoutno' => $request->getPost('stockoutno', '1'),
            'stockoutdate' => $request->getPost('stockoutdate', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'customername' => $request->getPost('customername', ''),
            'booking_out_sysno' => $request->getPost('booking_out_sysno', ''),
            'bookingoutno' => $request->getPost('bookingoutno', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'zj_employee_sysno' => $request->getPost('zj_employee_sysno', ''),
            'zj_employeename' => $request->getPost('zj_employeename', ''),
            'cc_employee_sysno' => $request->getPost('cc_employee_sysno', ''),
            'cc_employeename' => $request->getPost('cc_employeename', ''),
            'stockoutstatus' => $request->getPost('stockoutstatus', '1'),
            'takegoodsno' => $request->getPost('takegoodsno', ''),
            'takegoodsqty' => $request->getPost('takegoodsqty', ''),
            'takegoodscompany' => $request->getPost('takegoodscompany', ''),
            'businesscheckunitname' => $request->getPost('businesscheckunitname', ''),
            'shipproxyname' => $request->getPost('shipproxyname', ''),
            'takebetween' => $request->getPost('takebetween', ''),
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'changecarreason' => $request->getPost('changecarreason',''),
            'abandonreason' => $request->getPost('abandonreason',''),
            'auditreason' => $request->getPost('auditreason',''),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        if ($id = $S->addStockout($input, $stockoutdetaildata, $stockoutcardata, $request->getPost('stockmarks', ''))) {
            $attach = $request->getPost('attachment', array());

            if (count($attach) > 0) {
                $A = new AttachmentModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
                //	$res = 	$S->addShipAttach($id,$attach);
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }
            $row = $S->getStockoutById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    public function editJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockoutdetaildata = $request->getPost('stockoutdetaildata', "");
        $stockoutdetaildata = json_decode($stockoutdetaildata, true);
        if (count($stockoutdetaildata) == 0) {
            COMMON::result(300, '出库单明细不能为空');
            return;
        }
        $stockoutcardata = $request->getPost('stockoutcardata', "");
        $stockoutcardata = json_decode($stockoutcardata, true);
        if (count($stockoutcardata) == 0 && $request->getPost('stockouttype', '1') == 2) {
            COMMON::result(300, '出库车辆信息不能为空');
            return;
        }

        $S = new StockoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'stockoutstatus' => $request->getPost('stockoutstatus', '1'),
            'stockoutdate' => $request->getPost('stockoutdate', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'customername' => $request->getPost('customername', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', '') ,
            'zj_employee_sysno' => $request->getPost('zj_employee_sysno', ''),
            'zj_employeename' => $request->getPost('zj_employeename', ''),
            'cc_employee_sysno' => $request->getPost('cc_employee_sysno', ''),
            'cc_employeename' => $request->getPost('cc_employeename', ''),
            'takegoodsno' => $request->getPost('takegoodsno', ''),
            'takegoodscompany' => $request->getPost('takegoodscompany', ''),
            'businesscheckunitname' => $request->getPost('businesscheckunitname', ''),
            'shipproxyname' => $request->getPost('shipproxyname', ''),
            'takebetween' => $request->getPost('takebetween', ''),
            'changecarreason' => $request->getPost('changecarreason',''),
            'abandonreason' => $request->getPost('abandonreason',''),
            'auditreason' => $request->getPost('auditreason',''),
            'updated_at' => '=NOW()',
        );

        if ($S->updateStockout($id, $input, $stockoutdetaildata, $stockoutcardata, $request->getPost('stockmarks', ''))) {
            $attach = $request->getPost('attachment', array());

            if (count($attach) > 0) {
                $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }
            $row = $S->getStockoutById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    public function  examJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $examstep = $request->getPost('examstep', 0);

        $stockoutmarks = $request->getPost('stockoutmarks', '');
        if ($id == 0 || $examstep == 0 ) {
            COMMON::result(300, '缺少参数');
            return;
        }

        $L = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $S = new StockoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $user = Yaf_Registry::get(SSN_VAR);

        $stockoutInfo = $S->getStockoutById($id);
        if(!$stockoutInfo){
            COMMON::result(300, '出库订单信息有误');
            return;
        }
        
        if ($examstep == 6) {
            $data = array(
                'stockoutstatus' => 6,
                'auditreason' => $stockoutmarks,
            );

            #库存管理业务操作日志

            $res = $S->updateStockoutData($id, $data);
            if ($res) {

                $input = array(
                    'doc_sysno' => $id,
                    // 'doctype' => 8,
                    'opertype' => 5,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => $stockoutmarks
                );

                if ($stockoutInfo['stockouttype'] == 1) {
                    $input['doctype'] = 5;
                }elseif($stockoutInfo['stockouttype'] == 3){
                    $input['doctype'] = 26;
                }
                

                $L->addDocLog($input);
                //更新消息提醒
                $S = new SystemModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
                $S->updateMessage($id);
                COMMON::result(200, '操作成功');
                return;
            } else {
                COMMON::result(300, '操作失败');
                return;
            }
        } elseif ($examstep == 4) {

            $msg = '';
            $res = $S->examStockout($id, $msg, $stockoutmarks);
            if ($res) {
                $input = array(
                    'doc_sysno' => $id,
                    // 'doctype' => 8,
                    'opertype' => 3,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => $stockoutmarks
                );
                if ($stockoutInfo['stockouttype'] == 1) {
                    $input['doctype'] = 5;
                }elseif ($stockoutInfo['stockouttype'] == 3){
                    $input['doctype'] = 26;
                }
                
                
                $bookout = $S->getBookoutDataBysysno($id);
                if ($bookout[0]['docsource'] == 2) {
                    $stockoutqty = $S->getStockoutDetailBySysno($id);
                    // COMMON::editStockOutStatusOk($bookout[0]['bookingoutno'], floatval($stockoutqty[0]['bussinesscheckqty']));
                    COMMON::editStockOutStatusOk($bookout[0]['bookingoutno'], floatval($stockoutqty[0]['beqty']));
                }
                
                $L->addDocLog($input);

                COMMON::result(200, '操作成功');
                return;
            } else {
                COMMON::result(300, $msg);
                return;
            }

        }elseif ($examstep == 5) {
            $msg = '';

            $res = $S->cancelStockout($id, $msg);
            
            if ($res) {

                $input = array(
                    'doc_sysno' => $id,
                    // 'doctype' => 8,
                    'opertype' => 4,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => $stockoutmarks
                );
                if ($stockoutInfo['stockouttype'] == 1) {
                    $input['doctype'] = 5;
                }elseif($stockoutInfo['stockouttype'] == 3){
                    $input['doctype'] = 26;
                }
                
                $L->addDocLog($input);

                COMMON::result(200, '操作成功');
                return;
            } else {
                COMMON::result(300, $msg);
                return;
            }
        }

        COMMON::result(300, '操作失败');
        return;


    }

    public function delJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);
        
        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $input = array(
            'isdel' => 1
        );
        $stockoutInfo = $S->getStockoutById($id);
        if ($stockoutInfo) {
            if($stockoutInfo['stockoutstatus']  !=2 && $stockoutInfo['stockoutstatus']  !=6) {
                COMMON::result(300, '只有暂存和退回状态才允许删除');
                return false;
            }
        }else{
            COMMON::result(300, '删除失败');
        }
        
        if ($S->shipdelStockoutData($id, $input)) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败');
        }
    }

    //出库车辆信息删除
    public function delCarJsonAction()
    {
        $request = $this->getRequest();
        $ids = $request->getParam('ids');//getParam
        $carid = $request->getParam('carid');
        $carid = urldecode($carid);
        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $stockoutInfo = $S->poundSoutstatus($ids,$carid);

        if ($stockoutInfo) {
            if($stockoutInfo[0]['poundsoutstatus']  == 3 || $stockoutInfo[0]['poundsoutstatus']  ==4) {
                COMMON::result(300, '车辆已有过磅信息不能删除');
                return false;
            }
        }

        $input = array(
            'isdel' => '1',
        );
       $res= $S->updateStockCar($carid, $input);

        if ($res) {
             echo json_encode(['code'=>200,'msg'=>'删除成功']);

        } else {
            echo json_encode(['code'=>300,'msg'=>'删除失败']);
        }

    }
    //提单延期
    public function stockoutDelayAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id',0);
        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $res = $S->getStockoutById($id);
        if ($res['stockoutstatus'] != 3) {
            echo json_encode(array('code' => 300,'msg' => '出库中的单据才可以延期'));
            die();
        }
        echo json_encode(array('code' => 200));
    }

    public function stockoutDelayJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $receivestart = $request->getPost('receivestart','');
        $receiveend = $request->getPost('receiveend','');
        $detailData = $request->getPost('detailData','');
        if(strtotime($receivestart) > strtotime($receiveend) && $receiveend != ''){
            echo json_encode(array('code' => 300,'msg' => '开始时间不能大于结束时间'));
            die();
        }
        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $B = new BookoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $stockoutInfo = $S->getStockoutById($id);
        $bookoutInfo = $B->getBookoutById($stockoutInfo['booking_out_sysno']);

        if(strtotime($receivestart) < strtotime($bookoutInfo['receivestart'])){
            echo json_encode(array('code' => 300,'msg' => '开始时间不能小于预约单提货开始时间'));
            die();
        }

        if(strtotime($receiveend) < strtotime($stockoutInfo['receiveend'])){
            echo json_encode(array('code' => 300,'msg' => '结束时间不能小于原提货结束时间'));
            die();
        }

        
        if($receiveend != ''){
            //提货区间判断
            $result = $B->checkTimeRange($detailData,$receivestart,$receiveend);
            if($result){
                echo json_encode($result);
                die();
            }
        }

        $res = $S->updateStockoutData($id, array('receivestart' => $receivestart,'receiveend' => $receiveend,));
        if(!$res){
            echo json_encode(array('code' => 300,'msg' => '更新提货区间失败'));
            die();
        }else{
            echo json_encode(array('code' => 200));
            die();
        }
    }
    /**
     * @title 终止方法
     *
     */
    public function stopAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id',0);
        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $array = $S->stopStockout($id);
        $res = $S->getStockoutById($id);
        if ($res['stockoutstatus'] != 3) {
            COMMON::result(300, '出库中的单据才可以终止');
            return false;
        }

        $res = $S->getStockoutDetailBySysno($id);
        if (!$res) {
            COMMON::result(300, '出库单实提数量为0不允许终止');
            return false;
        }

        if(is_array($array)){
            foreach($array as $key => $value){
                if($value['poundsoutstatus']  < 4){
                    COMMON::result(300, '存在未生效的出库磅码单不允许终止');
                    return false;
                }
            }
        }
        $res = $S->getRebackData($id);
        if($res){
            COMMON::result(300, '存在未完成的退货单，不允许终止');
            return false;
        }
        echo json_encode('success');
    }

    public function stopJsonAction(){
        $request = $this->getRequest();
        $data =   $request->getPost('data',array());
        $id  = $request->getPost('id',0);
        
        $So = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $L = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $user = Yaf_Registry::get(SSN_VAR);
        if($id == 0 ){
            COMMON::result(300, '订单信息错误');
            return false;
        }

        $res = $So->stopCarStockout($id);

        if($res['code'] != 200){
            if(isset($res['msg'])){
                COMMON::result(300, $res['msg']);
            }
            else
            {
                COMMON::result(300, $res['message']);
            }
            
            return false;
        }

        $bookout = $So->getBookoutDataBysysno($id);
        if ($bookout[0]['docsource'] == 2) {
            // $stockoutqty = $So->getPoundsoutByStockoutsysno($id);
            $stockoutqty = $So->getStockoutDetailBySysno($id);
            COMMON::editStockOutStatusOk($bookout[0]['bookingoutno'], floatval($stockoutqty[0]['beqty']));
        }
        $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 6,
                    'opertype' => 3,   //终止
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => "车出库订单终止"
                );

        $L->addDocLog($input);

        echo json_encode('success');

    }
    /**
     * 1、已审核的单据才能进入到作废界面；
   * 2、作废必须录入作废原因；
   * 3、作废的前提条件对应入库单没有被清库；
   * 4、作废成功将单据状态置为作废，将出库预约单状态置为已审核，增加对应入库单的库存；
     */
    public function cancelAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id');


        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $stockoutinfo = $S->getStockoutById($id);
        if ($stockoutinfo['stockoutstatus'] != 4) {
            COMMON::result(300, '已审核的单据才能作废');
            return false;
        }
        $data = array(
                    'code' => 200,
                    'msg'  => '单据已审核',
                );
        echo json_encode($data);
  
    }

    public function downloadSealAction()
    {
        ob_end_clean();//清除缓冲区,避免乱码

        /*  $phpWord = new \PhpOffice\PhpWord\PhpWord();

         $section = $phpWord->addSection();

         $html = 	$this->getView()->html('seal.xianhuo',array());

  //echo $html; return;
         \PhpOffice\PhpWord\Shared\Html::addHtml($section, $html);

         $source =  APPLICATION_PATH . '/application/views/seal/' .'333.html';

  //	   $phpWord = \PhpOffice\PhpWord\IOFactory::load($source);
         $phpWord = \PhpOffice\PhpWord\IOFactory::load($source, 'HTML');

         $phpWord->save('合同.docx', 'Word2007', true);*/


        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(APPLICATION_PATH . '/application/views/seal/' . 't.docx');

// Variables on different parts of document


// Simple table


        $templateProcessor->setValue('userName', 'Josy');

        // $templateProcessor->saveAs('合同123.docx');
        $res = $templateProcessor->save();
        if ($res) {
            $fp = fopen($res, "rb"); //二进制方式打开文件
            if ($fp) {
                header("Content-Description: File Transfer");
                header('Content-Disposition: attachment; filename="' . '合同模板.docx' . '"');
                header('Content-Type: ' . 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                header('Content-Transfer-Encoding: binary');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Expires: 0');

                fpassthru($fp); // 输出至浏览器
                exit;
            }
        }

    }

    public function checkstoragetankAction()
    {
        $request = $this->getRequest();
        $bookingdata = $request->getPost('bookingdata',array());
        $step = $request->getParam('step',0);
        $data = array('code' => 200,'msg' => '');
        $BO = new BookoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        if($step == 2 || $step == 6 || $step == 8){
            //检查通知数量是否大于库存余量

            foreach ($bookingdata as $key => $value) {
                $res = $S->checkStock($value['takeqty'],$value['stocktype'],$value['tobeqty'],$value['stock_sysno']);
                if($res['code'] == 300){
                    echo json_encode($res);
                    die();
                }
            }
            
        }
        foreach ($bookingdata as $key => $value) {

            if ($value['sysno']) {
                $res = $BO->getBookoutDetailById($value['sysno']);
            }else{
                $res = $BO->getBookoutDetailById($value['bookout_detail_sysno']);
            }
            
            if($res && $res['storagetank_sysno'] != $value['storagetank_sysno']){
                $data = array(
                    'code' => 300,
                    'msg' => '订单储罐号和预订单不一致'
                    );
            }
            echo json_encode($data);die();
        }
        echo json_encode($data);
    }
    public function shipeditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $stockouttype = $request->getParam('type', '1');
        $booking_sysno = $request->getPost('booking_sysno', '0');
        $type = $request->getParam('type', '');


        if (!isset($id)) {
            $id = 0;
        }

        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $C = new CustomerModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $B = new BookingModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $BO = new BookoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $stock = new StockModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $I = new IntroduceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        if (!$id) {
            $action = "/stockout/shipnewJson/";
            $params = array();

            $params['stockouttype'] = $stockouttype;

            if ($booking_sysno) {
                $search = array(
                    'bookingout_sysno' => $booking_sysno,
                    'page' => false
                );

                $bookoutinfo = $BO->getBookoutById($booking_sysno);

                if (count($bookoutinfo) > 0) {
                    $params['takegoodsno'] = $bookoutinfo['receivenumber'];
                    $params['takegoodscompany'] = $bookoutinfo['receiveunitname'];
                    $params['shipproxyname'] = $bookoutinfo['shipproxyname'];
                    $params['businesscheckunitname'] = $bookoutinfo['businesscheckunitname'];
                    $params['takebetween'] = $bookoutinfo['receivebetween'];
                    $params['ispipelineorder'] = $bookoutinfo['ispipelineorder'];
                    $params['isberthorder'] = $bookoutinfo['isberthorder'];
                    $params['isqualitycheck'] = $bookoutinfo['isqualitycheck'];
                }

                $detailData = $BO->getBookDetail($search);
                
                if (count($detailData) > 0) {
                    foreach ($detailData as $key => $value) {
                        if($value['stocktype'] == 1){
                            $stockinfo = $stock->getElementById(array($value['stock_sysno']));
                            if(!$stockinfo){
                                COMMON::result(300,'库存记录不存在');
                                return false;
                            }
                            $detailData[$key]['instockqty'] = $stockinfo[0]['instockqty'];
                            $detailData[$key]['introduceqty'] = '--';
                        }elseif($value['stocktype'] == 2){
                            $introduceDetailInfo = $I->getIntroduceDetailById(intval($value['stock_sysno']));
                            $detailData[$key]['instockqty'] = '--';
                            $detailData[$key]['introduceqty'] = $introduceDetailInfo['takegoodsnum'];

                        }
                        
                        $detailData[$key]['takeqty'] = $value['bookingoutqty'];
                        $detailData[$key]['tobeqty'] = $value['noticenum'];
                        $detailData[$key]['bookout_detail_sysno'] = $value['sysno'];
                        $detailData[$key]['beqty'] = 0;
                        $detailData[$key]['unitname'] = '吨';
                        $detailData[$key]['stocktype'] = $value['stocktype'];
                    }

                    $params['detaillist'] = json_encode($detailData);
                } else{
                    $params['detaillist'] = json_encode(array());
                }
                $pbqData = $S->getPBQ($bookoutinfo,6);
                if($pbqData){
                    $params['pipelineorder'] = json_encode($pbqData['pipelineorder']);
                    $params['berthorder'] = json_encode($pbqData['berthorder']);
                    $params['qualitycheck'] = json_encode($pbqData['qualitycheck']);
                }else{
                    $params['pipelineorder'] = json_encode([]);
                    $params['berthorder'] =  json_encode([]);
                    $params['qualitycheck'] = json_encode([]);
                }

            } else {
                $params['detaillist'] = json_encode(array());
            }

            $params['carlist'] = json_encode(array());

        } else {
            $action = "/stockout/shipeditJson/";
            $params = $S->getStockoutById($id);
            if($type == 'print' && $params['stockoutstatus'] != 4){
                COMMON::result(300, '只有已完成状态才允许打印');
                return false;
            }
            if($params['stockoutstatus']  > 2 && $params['stockoutstatus']  != 6 && $type != 'view' && $type != 'audit' && $type != 'print' && $type != 'cancel' && $type != 'execute') {
                COMMON::result(300, '只有暂存状态才允许编辑');
                return false;
            }
            $booking = $B->getBookingOutById($params['booking_out_sysno']);
            $booking_sysno = $params['booking_out_sysno'];
            $pbqData = $S->getPBQ($booking,7);
            if($pbqData){
                $params['pipelineorder'] = json_encode($pbqData['pipelineorder']);
                $params['berthorder'] = json_encode($pbqData['berthorder']);
                $params['qualitycheck'] = json_encode($pbqData['qualitycheck']);
            }else{
                $params['pipelineorder'] = json_encode([]);
                $params['berthorder'] =  json_encode([]);
                $params['qualitycheck'] = json_encode([]);
            }

            $search = array(
                'stockout_sysno' => $params['sysno'],
                'status' => 1,
                'page' => false
            );

            $detailData = $S->getStockoutDetailList($search);
            foreach ($detailData['list'] as $key => $value) {
                if($value['stocktype'] == 1){
                    $stockinfo = $stock->getElementById(array($value['stock_sysno']));
                    if(!$stockinfo){
                        COMMON::result(300,'库存记录不存在');
                        return false;
                    }
                    $detailData['list'][$key]['instockqty'] = $stockinfo[0]['instockqty'];
                    $detailData['list'][$key]['introduceqty'] = '--';
                }elseif ($value['stocktype'] == 2) {
                    $introduceDetailInfo = $I->getIntroduceDetailById(intval($value['stock_sysno']));
                    $detailData['list'][$key]['instockqty'] = '--';
                    $detailData['list'][$key]['introduceqty'] = $introduceDetailInfo['takegoodsnum'];
                }
                $detailData['list'][$key]['unitname'] = '吨';
                $res = $S->getStockinShipname($value['stockin_sysno']);
                if ($res) {
                    $detailData['list'][$key]['stockinshipname'] = $res;
                }
                if($type == 'execute'){
                    $detailData['list'][$key]['bussinesscheckqty'] = '';
                }
                
            }
           
            $params['detaillist'] = json_encode($detailData['list']);

            $attach = $A->getAttachByMAS('stockout', 'receipt', $id);
            if (is_array($attach) && count($attach)) {
                $files = array();
                foreach ($attach as $file) {
                    //  $files[] =  $file['module'].'/'.$file['action'].'/'. $file['name'];
                    $files[] = $file['sysno'];
                }
                $params['uploaded'] = join(',', $files);
            }
 
        }
        $booking_attach = $A->getAttachByMAS('bookout', 'ship_uploader', $booking_sysno);
        if (is_array($booking_attach) && count($booking_attach)) {
            $booking_files = array();
            foreach ($booking_attach as $file) {
                //  $files[] =  $file['module'].'/'.$file['action'].'/'. $file['name'];
                $booking_files[] = $file['sysno'];
            }
        }
        $attach = $A->getAttachByMAS('stockout', 'takegoods', $id);
        if (is_array($attach) && count($attach)) {
            $files = array();
            foreach ($attach as $file) {
                //  $files[] =  $file['module'].'/'.$file['action'].'/'. $file['name'];
                $files[] = $file['sysno'];
            }
            if(is_array($booking_files) && count($booking_files)){
                foreach ($booking_files as $value) {
                    $files[] = $value;
                }
            }
            
        }else{
            if(is_array($booking_files) && count($booking_files)){
                $files = $booking_files;
            }else{
                $files = array();
            }
        }
        $params['uploaded1'] = join(',', $files);

        $params['attach'] = $attach;
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );

        if ($booking_sysno) {

            $booking = $B->getBookingOutById($booking_sysno);
            $params['customer_sysno'] = $booking['customer_sysno'];
            $params['customername'] = $booking['customer_name'];
            $params['booking_out_sysno'] = $booking['sysno'];
            $params['bookingoutno'] = $booking['bookingoutno'];
            $params['cs_employee_sysno'] = $booking['cs_employee_sysno'];
            $params['cs_employeename'] = $booking['cs_employeename'];
        }

        $list = $C->searchCustomer($search);
        $params['customerlist'] = $list['list'];

        $E = new EmployeeModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];

        //码头
        $W = new WharfModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $wharflist = $W->searchWharf(array('page'=>false,'bar_status'=>1));
        $params['wharflist'] = $wharflist['list'];

        $params['id'] = $id;
        $params['action'] = $action;
        $params['status'] = COMMON::getStockOutStatus($params['stockoutstatus']);

        $Company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $companyname = $Company->getDefault();
        $params['companyname'] = $companyname['companyname'];
        $params['type'] = $type;
        $params['print'] = $print;
        $user = Yaf_Registry::get(SSN_VAR); 

        if(!isset($params['sby_employee_sysno']) || $params['sby_employee_sysno'] == 0){
            $params['sby_employee_sysno'] = $user['employee_sysno'];
            $params['sby_employeename'] = $user['employeename'];
        }
        $this->getView()->make('stockshipout.edit', $params);
    }
    public function shipnewJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', "");
        $stockoutdetaildata = $request->getPost('stockoutdetaildata', "");
 
        $stockoutdetaildata = json_decode($stockoutdetaildata, true);
        if (count($stockoutdetaildata) == 0) {
            COMMON::result(300, '出库单明细不能为空');
            return;
        }

        $S = new StockoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'stockouttype' => $request->getPost('stockouttype', '1'),
            'stockoutno' => $request->getPost('stockoutno', ''),
            'stockoutdate' => $request->getPost('stockoutdate', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'customername' => $request->getPost('customername', ''),
            'booking_out_sysno' => $request->getPost('booking_out_sysno', ''),
            'bookingoutno' => $request->getPost('bookingoutno', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', '') == '请选择' ? '' : $request->getPost('cs_employeename', ''),
            'zj_employee_sysno' => $request->getPost('zj_employee_sysno', ''),
            'zj_employeename' => $request->getPost('zj_employeename', '')  == '请选择' ? '' : $request->getPost('zj_employeename', ''),
            'cc_employee_sysno' => $request->getPost('cc_employee_sysno', ''),
            'cc_employeename' => $request->getPost('cc_employeename', '')   == '请选择' ? '' : $request->getPost('cc_employeename', ''),
            'stockoutstatus' => $request->getPost('stockoutstatus', '1'),
            'takegoodsno' => $request->getPost('takegoodsno', ''),
            'takegoodsqty' => $request->getPost('takegoodsqty', ''),
            'takegoodscompany' => $request->getPost('takegoodscompany', ''),
            'businesscheckunitname' => $request->getPost('businesscheckunitname', ''),
            'shipproxyname' => $request->getPost('shipproxyname', ''),
            'takebetween' => $request->getPost('takebetween', ''),
            'wharf_sysno' => $request->getPost('wharf_sysno', ''),
            'wharfname' => $request->getPost('wharfname', ''),
            'shipchecknum' => $request->getPost('shipchecknum', ''),
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
            'ispipelineorder' => $request->getPost('ispipelineorder', '1'),
            'isberthorder' => $request->getPost('isberthorder', '1'),
            'sby_employee_sysno' => $request->getPost('sby_employee_sysno', ''),
            'sby_employeename' => $request->getPost('sby_employeename', ''),
            'memo' => $request->getPost('memo', ''),
        );

        if ($id = $S->addStockout($input, $stockoutdetaildata, $request->getPost('stockmarks', ''))) {
            $attach = $request->getPost('attachment', array());

            if (count($attach) > 0) {
                $A = new AttachmentModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
                //  $res =  $S->addShipAttach($id,$attach);
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }
            $row = $S->getStockoutById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    public function shipeditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        $stockoutdetaildata = $request->getPost('stockoutdetaildata', "");
        $stockoutdetaildata = json_decode($stockoutdetaildata, true);
        if (count($stockoutdetaildata) == 0) {
            COMMON::result(300, '出库单明细不能为空');
            return;
        }

        $S = new StockoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'stockoutstatus' => $request->getPost('stockoutstatus', '1'),
            'stockoutno' => $request->getPost('stockoutno', '1'),
            'stockouttype' => $request->getPost('stockouttype', '1'),
            'stockoutdate' => $request->getPost('stockoutdate', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'customername' => $request->getPost('customername', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', '') == '请选择' ? '' : $request->getPost('cs_employeename', ''),
            'zj_employee_sysno' => $request->getPost('zj_employee_sysno', ''),
            'zj_employeename' => $request->getPost('zj_employeename', '')  == '请选择' ? '' : $request->getPost('zj_employeename', ''),
            'cc_employee_sysno' => $request->getPost('cc_employee_sysno', ''),
            'cc_employeename' => $request->getPost('cc_employeename', '')   == '请选择' ? '' : $request->getPost('cc_employeename', ''),
            'takegoodsno' => $request->getPost('takegoodsno', ''),
            'takegoodscompany' => $request->getPost('takegoodscompany', ''),
            'businesscheckunitname' => $request->getPost('businesscheckunitname', ''),
            'shipproxyname' => $request->getPost('shipproxyname', ''),
            'takebetween' => $request->getPost('takebetween', ''),
            'wharf_sysno' => $request->getPost('wharf_sysno', ''),
            'wharfname' => $request->getPost('wharfname', ''),
            'shipchecknum' => $request->getPost('shipchecknum', ''),
            'updated_at' => '=NOW()',
            'ispipelineorder' => $request->getPost('ispipelineorder', '1'),
            'isberthorder' => $request->getPost('isberthorder', '1'),
            'isqualitycheck' => $request->getPost('isqualitycheck', '1'),
            'sby_employee_sysno' => $request->getPost('sby_employee_sysno', ''),
            'sby_employeename' => $request->getPost('sby_employeename', ''),
            'memo' => $request->getPost('memo', ''),
        );

        if ($S->updateStockout($id, $input, $stockoutdetaildata, $request->getPost('stockmarks', ''))) {
            $attach = $request->getPost('attachment', array());

            if (count($attach) > 0) {
                $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }
            $row = $S->getStockoutById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }
    public function shipdetailEditAction()
    {
        $request = $this->getRequest();
        $cid = $request->getParam('cid', '0');
        $rtype = $request->getParam('rtype', '1');
        $stockoutstatus = $request->getParam('stockoutstatus', 0);

        $params = $request->getRequest();

        $params['stockouttype'] = $rtype;
        $params['stockoutstatus'] = $stockoutstatus;

        $goodsnature = $request->getRequest('goodsnature', '0');
        $goodsnature_arr = array('0' => '', '1' => '保税', '2' => '外贸', '3' => '内贸转出口', '4' => '内贸内销');
        $params['goodsnaturemark'] = $goodsnature_arr[$goodsnature];
        $this->getView()->make('stockshipout.detailedit', $params);

    }


    //船出库订单EXCEL导出
    public function shipdbtoexcelAction()
    {

        $request = $this->getRequest();

        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'bar_receivenumber' => $request->getPost('bar_receivenumber',''),
            'bar_goodsname' => $request->getPost('bar_goodsname',''),
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'bar_stockoutstatus' => $request->getPost('bar_stockoutstatus', '-100'),
            'page' => false,
            'orders' => 'created_at desc',
            'stockouttype' => 1,
        );

        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStockout($search);


        /*------------------查询筛选条件返回参数-----------------*/

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("船出库订单列表")
            ->setSubject("船出库订单列表")
            ->setDescription("船出库订单列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '出库单号'),
            array('B1:B1', 'B1', '005E9CD3', '客户'),
            array('C1:C1', 'C1', '005E9CD3', '质计'),
            array('D1:D1', 'D1', '0094CE58', '品名'),
            array('E1:E1', 'E1', '0094CE58', '规格'),
            array('F1:F1', 'F1', '0094CE58', '货物性质'),
            array('G1:G1', 'G1', '0094CE58', '计量单位'),
            array('H1:H1', 'H1', '003376B3', '提货数量'),
            array('I1:I1', 'I1', '003376B3', '通知数量'),
            array('J1:J1', 'J1', '003376B3', '罐检数量'),
            array('K1:K1', 'K1', '003376B3', '船名'),
            array('L1:L1', 'L1', '003376B3', '单据状态'),
        );

        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('船出库订单列表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H','I','J','K','L');
        foreach ($list['list'] as $item) {

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
                        $value = $item['zj_employeename'];
                        break;
                    case 3:
                        $value = $item['goodsname'];
                        break;
                    case 4:
                        $value = $item['qualityname'];
                        break;
                    case 5:
                        switch($item['goodsnature']){
                            case "1":
                                $value = "保税";
                                break;
                            case "2":
                                $value = "外贸";
                                break;
                            case "3":
                                $value = "内贸转出口";
                                break;
                            case "4":
                                $value = "内贸内销";
                                break;
                        }
                        break;
                    case 6:
                        $value = $item['unitname'];
                        break;
                    case 7:
                        $value = $item['takeqty'];
                        break;
                    case 8:
                        $value = $item['tobeqty'];
                        break;
                    case 9:
                        $value = $item['bussinesscheckqty'];
                        break;
                    case 10:
                        $value = $item['shipname'];
                        break;
                    case 11:
                        switch($item['stockoutstatus']){
                            case "2":
                                $value = "暂存";
                                break;
                            case "3":
                                $value = "待审核";
                                break;
                            case "4":
                                $value = "已审核";
                                break;
                            case '5':
                                $value = "作废";
                                break;
                            default:
                                $value = "新建";
                        }
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }

// Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="船出库订单列表.xlsx"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

    }

    //车出库订单EXCEL导出
    public function cardbtoexcelAction()
    {

        $request = $this->getRequest();

        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'bar_receivenumber' => $request->getPost('bar_receivenumber',''),
            'bar_goodsname' => $request->getPost('bar_goodsname',''),
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'bar_stockoutstatus' => $request->getPost('bar_stockoutstatus', '-100'),
            'page' => false,
            'orders' => 'created_at desc',
            'stockouttype' => 2,
        );

        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStockout($search);

        /*------------------查询筛选条件返回参数-----------------*/

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("车出库订单列表")
            ->setSubject("车出库订单列表")
            ->setDescription("车出库订单列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '出库单号'),
            array('B1:B1', 'B1', '005E9CD3', '客户'),
            array('C1:C1', 'C1', '005E9CD3', '提货单号'),
            array('D1:D1', 'D1', '0094CE58', '提货单位'),
            array('E1:E1', 'E1', '0094CE58', '提货开始日'),
            array('F1:F1', 'F1', '0094CE58', '提货结束日'),
            array('G1:G1', 'G1', '0094CE58', '货品名称'),
            array('H1:H1', 'H1', '003376B3', '规格'),
            array('I1:I1', 'I1', '003376B3', '货物性质'),
            array('J1:J1', 'J1', '003376B3', '计量单位'),
            array('K1:K1', 'K1', '003376B3', '提货数量'),
            array('L1:L1', 'L1', '003376B3', '已提数量'),
            array('M1:M1', 'M1', '003376B3', '待提数量'),
            array('N1:N1', 'N1', '003376B3', '单据状态'),
        );

        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('车出库订单列表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H','I','J','K','L','M','N');
        foreach ($list['list'] as $item) {

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
                        $value = $item['takegoodsno'];
                        break;
                    case 3:
                        $value = $item['takegoodscompany'];
                        break;
                    case 4:
                        $value = $item['receivestart'];
                        break;
                    case 5:
                        $value = $item['receiveend'];
                        break;
                    case 6:
                        $value = $item['goodsname'];
                        break;
                    case 7:
                        $value = $item['qualityname'];
                        break;
                    case 8:
                        switch($item['goodsnature']){
                            case "1":
                                $value = "保税";
                                break;
                            case "2":
                                $value = "外贸";
                                break;
                            case "3":
                                $value = "内贸转出口";
                                break;
                            case "4":
                                $value = "内贸内销";
                                break;
                        }
                        break;
                    case 9:
                        $value = $item['unitname'];
                        break;
                    case 10:
                        $value = $item['tobeqty'];
                        break;
                    case 11:
                        $value = $item['beqty'];
                        break;
                    case 12:
                        $value = $item['takeqty'];
                        break;
                    case 13:
                        switch($item['stockoutstatus']){
                            case "2":
                                $value = "暂存";
                                break;
                            case "3":
                                $value = "出库中";
                                break;
                            case "4":
                                $value = "已完成";
                                break;
                            case '6':
                                $value = "退回";
                                break;
                            default:
                                $value = "新建";
                        }
                        break;
                    
                }
                $objActSheet->setCellValue($site, $value);
            }
        }

// Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="车出库订单列表.xlsx"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

    }
    //船出库订单审核EXCEL导出
    public function shipprecheckdbtoexcelAction()
    {

        $request = $this->getRequest();

        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'bar_name' => $request->getPost('bar_name',''),
            'bar_receivenumber' => $request->getPost('bar_receivenumber',''),
            'bar_goodsname' => $request->getPost('bar_goodsname',''),
            'bar_stockoutstatus'=> 3,
            'page' => false,
            'orders'  => 'created_at desc',
            'stockouttype' => 1,
        );

        $S = new StockoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $S->searchStockout($search);


        /*------------------查询筛选条件返回参数-----------------*/

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("船出库订单审核列表")
            ->setSubject("船出库订单审核列表")
            ->setDescription("船出库订单审核列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '出库单号'),
            array('B1:B1', 'B1', '005E9CD3', '客户'),
            array('C1:C1', 'C1', '005E9CD3', '质计'),
            array('D1:D1', 'D1', '0094CE58', '品名'),
            array('E1:E1', 'E1', '0094CE58', '规格'),
            array('F1:F1', 'F1', '0094CE58', '货物性质'),
            array('G1:G1', 'G1', '0094CE58', '计量单位'),
            array('H1:H1', 'H1', '003376B3', '提货数量'),
            array('I1:I1', 'I1', '003376B3', '通知数量'),
            array('J1:J1', 'J1', '003376B3', '罐检数量'),
            array('K1:K1', 'K1', '003376B3', '船名'),
            array('L1:L1', 'L1', '003376B3', '单据状态'),
        );

        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('船出库订单列表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H','I','J','K','L');
        foreach ($list['list'] as $item) {

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
                        $value = $item['zj_employeename'];
                        break;
                    case 3:
                        $value = $item['goodsname'];
                        break;
                    case 4:
                        $value = $item['qualityname'];
                        break;
                    case 5:
                        switch($item['goodsnature']){
                            case "1":
                                $value = "保税";
                                break;
                            case "2":
                                $value = "外贸";
                                break;
                            case "3":
                                $value = "内贸转出口";
                                break;
                            case "4":
                                $value = "内贸内销";
                                break;
                        }
                        break;
                    case 6:
                        $value = $item['unitname'];
                        break;
                    case 7:
                        $value = $item['takeqty'];
                        break;
                    case 8:
                        $value = $item['tobeqty'];
                        break;
                    case 9:
                        $value = $item['bussinesscheckqty'];
                        break;
                    case 10:
                        $value = $item['shipname'];
                        break;
                    case 11:
                        switch($item['stockoutstatus']){
                            case "2":
                                $value = "暂存";
                                break;
                            case "3":
                                $value = "待审核";
                                break;
                            case "4":
                                $value = "已审核";
                                break;
                            case '5':
                                $value = "作废";
                                break;
                            default:
                                $value = "新建";
                        }
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }

// Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="船出库订单审核列表.xlsx"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

    }

    //管出待执行列表
    public function pipelineExecuteAction() {
        $params = array(

        );
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();
        $this->getView()->make('stockoutpipeline.executelist',$params);
    }

    public function pipelineExecuteListJsonAction() {
        $request = $this->getRequest();

        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'bar_name' => $request->getPost('bar_name',''),
            'bar_receivenumber' => $request->getPost('bar_receivenumber',''),
            'bar_goodsname' => $request->getPost('bar_goodsname',''),
            'bar_stockoutstatus'=> $request->getParam('bar_stockoutstatus',8),
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),
            'orders'  => 'created_at desc',
            'stockouttype' => 3,
        );

        $S = new StockoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $S->searchStockout($search);
        foreach ($list['list'] as $key => $value) {
            $list['list'][$key]['unitname'] = '吨';
        }
        echo json_encode($list);

    }

    //管出库订单
    public function pipelineEditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $booking_sysno = $request->getParam('bookout_sysno', '0');
        $type = $request->getParam('type', '');
        if (!isset($id)) {
            $id = 0;
        }

        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $C = new CustomerModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $B = new BookingModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $BO = new BookoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $stock = new StockModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $I = new IntroduceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if (!$id) {
            $action = "/stockout/pipelineNewJson/";
            $params = array();

            $params['stockouttype'] = 3;
            if(!$booking_sysno){
                COMMON::result(300,'预约信息错误');
                return false; 
            }

            $search = array(
                'bookingout_sysno' => $booking_sysno,
                'page' => false
            );

            $bookoutinfo = $BO->getBookoutById($booking_sysno);

            if (count($bookoutinfo) > 0) {
                $params['takegoodsno'] = $bookoutinfo['receivenumber'];
                $params['takegoodscompany'] = $bookoutinfo['receiveunitname'];
                $params['receivestart'] = $bookoutinfo['receivestart'];
                $params['receiveend'] = $bookoutinfo['receiveend'];
                $params['ispipelineorder'] = $bookoutinfo['ispipelineorder'];
                $params['isberthorder'] = $bookoutinfo['isberthorder'];
                $params['isqualitycheck'] = $bookoutinfo['isqualitycheck'];
                $params['customer_sysno'] = $bookoutinfo['customer_sysno'];
                $params['customername'] = $bookoutinfo['customer_name'];
                $params['booking_out_sysno'] = $bookoutinfo['sysno'];
                $params['bookingoutno'] = $bookoutinfo['bookingoutno'];
                $params['cs_employee_sysno'] = $bookoutinfo['cs_employee_sysno'];
                $params['cs_employeename'] = $bookoutinfo['cs_employeename'];
                $pbqData = $S->getPBQ($bookoutinfo,8);
                if($pbqData){
                    $params['pipelineorder'] = json_encode($pbqData['pipelineorder']);
                    $params['berthorder'] = json_encode($pbqData['berthorder']);
                    $params['qualitycheck'] = json_encode($pbqData['qualitycheck']);
                }else{
                    $params['pipelineorder'] = json_encode([]);
                    $params['berthorder'] =  json_encode([]);
                    $params['qualitycheck'] = json_encode([]);
                }
            }

            $detailData = $BO->getBookDetail($search);

            if (count($detailData) > 0) {
                foreach ($detailData as $key => $value) {
                    if($value['stocktype'] == 1){
                        $stockinfo = $stock->getElementById(array($value['stock_sysno']));
                        if(!$stockinfo){
                            COMMON::result(300,'库存记录不存在');
                            return false;
                        }
                        $detailData[$key]['instockqty'] = $stockinfo[0]['instockqty'];
                        $detailData[$key]['introduceqty'] = '--';
                    }elseif ($value['stocktype'] == 2) {
                        $introduceDetailInfo = $I->getIntroduceDetailById(intval($value['stock_sysno']));
                        $detailData[$key]['instockqty'] = '--';
                        $detailData[$key]['introduceqty'] = $introduceDetailInfo['takegoodsnum'];
                    }
                    
                    $detailData[$key]['takeqty'] = $value['bookingoutqty'];
                    $detailData[$key]['tobeqty'] = $value['noticenum'];
                    $detailData[$key]['bookout_detail_sysno'] = $value['sysno'];
                    $detailData[$key]['beqty'] = 0;
                    $detailData[$key]['unitname'] = '吨';
                }

                $params['detaillist'] = json_encode($detailData);
            } else{
                $params['detaillist'] = json_encode(array());
            }

        } else {
            $action = "/stockout/pipelineEditJson/";
            $params = $S->getStockoutById($id);
            if(!$params){
                COMMON::result(300, '出库订单信息有误');
                return false;
            }
            if($type == 'audit' && $params['stockoutstatus'] != 3){
                COMMON::result(300, '订单不是待审核状态');
                return false;
            }
            if($type == 'cancel' && $params['stockoutstatus'] != 4){
                COMMON::result(300, '已完成订单才能作废');
                return false;
            }
            if($params['stockoutstatus']  > 2 && $params['stockoutstatus']  != 6 && $type != 'cancel' && $type != 'audit' && $type != 'view' && $type != 'execute') {
                COMMON::result(300, '只有暂存状态才允许编辑');
                return false;
            }
            $booking = $B->getBookingOutById($params['booking_out_sysno']);
            $booking_sysno = $params['booking_out_sysno'];
            $pbqData = $S->getPBQ($booking,9);
            if($pbqData){
                $params['pipelineorder'] = json_encode($pbqData['pipelineorder']);
                $params['berthorder'] = json_encode($pbqData['berthorder']);
                $params['qualitycheck'] = json_encode($pbqData['qualitycheck']);
            }else{
                $params['pipelineorder'] = json_encode([]);
                $params['berthorder'] =  json_encode([]);
                $params['qualitycheck'] = json_encode([]);
            }

            $search = array(
                'stockout_sysno' => $params['sysno'],
                'status' => 1,
                'page' => false
            );

            $detailData = $S->getStockoutDetailList($search);
            foreach ($detailData['list'] as $key => $value) {
                if($value['stocktype'] == 1){
                    $stockinfo = $stock->getElementById(array($value['stock_sysno']));
                    if(!$stockinfo){
                        COMMON::result(300,'库存记录不存在');
                        return false;
                    }
                    $detailData['list'][$key]['instockqty'] = $stockinfo[0]['instockqty'];
                    $detailData['list'][$key]['introduceqty'] = '--';
                }elseif ($value['stocktype'] == 2) {
                    $introduceDetailInfo = $I->getIntroduceDetailById(intval($value['stock_sysno']));
                    $detailData['list'][$key]['instockqty'] = '--';
                    $detailData['list'][$key]['introduceqty'] = $introduceDetailInfo['takegoodsnum'];
                }
                if($type == 'execute'){
                    $detailData['list'][$key]['bussinesscheckqty'] = '' ;
                }
            }

            $params['detaillist'] = json_encode($detailData['list']);

            $attach = $A->getAttachByMAS('pipelineout', 'pipeline', $id);
            if (is_array($attach) && count($attach)) {
                $files = array();
                foreach ($attach as $file) {
                    //  $files[] =  $file['module'].'/'.$file['action'].'/'. $file['name'];
                    $files[] = $file['sysno'];
                }
                $params['uploaded'] = join(',', $files);
            }
 
        }
        $booking_attach = $A->getAttachByMAS('bookpipeline','pipeline', $booking_sysno);
        if (is_array($booking_attach) && count($booking_attach)) {
            $booking_files = array();
            foreach ($booking_attach as $file) {
                //  $files[] =  $file['module'].'/'.$file['action'].'/'. $file['name'];
                $booking_files[] = $file['sysno'];
            }
        }

        if (is_array($attach) && count($attach)) {
            $files = array();
            foreach ($attach as $file) {
                //  $files[] =  $file['module'].'/'.$file['action'].'/'. $file['name'];
                $files[] = $file['sysno'];
            }
            if(is_array($booking_files) && count($booking_files)){
                foreach ($booking_files as $value) {
                    $files[] = $value;
                }
            }
            
        }else{
            if(is_array($booking_files) && count($booking_files)){
                $files = $booking_files;
            }else{
                $files = array();
            }
        }
        $params['uploaded'] = join(',', $files);

        $params['attach'] = $attach;
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );

        $list = $C->searchCustomer($search);
        $params['customerlist'] = $list['list'];

        $E = new EmployeeModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];

        $params['id'] = $id;
        $params['action'] = $action;
        $params['status'] = COMMON::getStockOutStatus($params['stockoutstatus']);

        $Company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $companyname = $Company->getDefault();
        $params['companyname'] = $companyname['companyname'];
        $params['type'] = $type;
        $user = Yaf_Registry::get(SSN_VAR); 
        if(!isset($params['sby_employee_sysno']) || $params['sby_employee_sysno'] == 0){
            $params['sby_employee_sysno'] = $user['employee_sysno'];
            $params['sby_employeename'] = $user['employeename'];
        }
        
        $this->getView()->make('stockoutpipeline.edit', $params);
    }
    public function pipelineNewJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', "");
        $stockoutdetaildata = $request->getPost('stockoutdetaildata', "");
 
        $stockoutdetaildata = json_decode($stockoutdetaildata, true);
        if (count($stockoutdetaildata) == 0) {
            COMMON::result(300, '出库单明细不能为空');
            return;
        }

        $S = new StockoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'stockouttype' => $request->getPost('stockouttype', '3'),
            'stockoutno' => $request->getPost('stockoutno', '1'),
            'stockoutdate' => $request->getPost('stockoutdate', ''),
            'docsource' => $request->getPost('docsource', 1),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'customername' => $request->getPost('customername', ''),
            'booking_out_sysno' => $request->getPost('booking_out_sysno', ''),
            'bookingoutno' => $request->getPost('bookingoutno', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', '') == '请选择' ? '' : $request->getPost('cs_employeename', ''),
            'stockoutstatus' => $request->getPost('stockoutstatus', '1'),
            'takegoodsno' => $request->getPost('takegoodsno', ''),
            'takegoodscompany' => $request->getPost('takegoodscompany', ''),
            'receivestart' => $request->getPost('receivestart', ''),
            'receiveend' => $request->getPost('receiveend', ''),
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
            'ispipelineorder' => $request->getPost('ispipelineorder', '1'),
            'isberthorder' => $request->getPost('isberthorder', '1'),
            'isqualitycheck' => $request->getPost('isqualitycheck', '1'),
            'sby_employee_sysno' => $request->getPost('sby_employee_sysno', ''),
            'sby_employeename' => $request->getPost('sby_employeename', ''),
            'memo' => $request->getPost('memo', ''),
        );
        
        if ($id = $S->addStockout($input, $stockoutdetaildata, $request->getPost('stockmarks', ''))) {
            $attach = $request->getPost('attachment', array());

            if (count($attach) > 0) {
                $A = new AttachmentModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
                //  $res =  $S->addShipAttach($id,$attach);
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }
            $row = $S->getStockoutById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    public function pipelineEditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        $stockoutdetaildata = $request->getPost('stockoutdetaildata', "");
        $stockoutdetaildata = json_decode($stockoutdetaildata, true);
        if (count($stockoutdetaildata) == 0) {
            COMMON::result(300, '出库单明细不能为空');
            return;
        }

        $S = new StockoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'stockoutstatus' => $request->getPost('stockoutstatus', '1'),
            'stockouttype' => $request->getPost('stockouttype', '3'),
            'stockoutdate' => $request->getPost('stockoutdate', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'customername' => $request->getPost('customername', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', '') == '请选择' ? '' : $request->getPost('cs_employeename', ''),
            'takegoodsno' => $request->getPost('takegoodsno', ''),
            'takegoodscompany' => $request->getPost('takegoodscompany', ''),
            'businesscheckunitname' => $request->getPost('businesscheckunitname', ''),
            'shipproxyname' => $request->getPost('shipproxyname', ''),
            'takebetween' => $request->getPost('takebetween', ''),
            'wharf_sysno' => $request->getPost('wharf_sysno', ''),
            'wharfname' => $request->getPost('wharfname', ''),
            'updated_at' => '=NOW()',
            'ispipelineorder' => $request->getPost('ispipelineorder', '1'),
            'isberthorder' => $request->getPost('isberthorder', '1'),
            'isqualitycheck' => $request->getPost('isqualitycheck', '1'),
            'sby_employee_sysno' => $request->getPost('sby_employee_sysno', ''),
            'sby_employeename' => $request->getPost('sby_employeename', ''),
            'memo' => $request->getPost('memo', ''),
        );
        
        if ($S->updateStockout($id, $input, $stockoutdetaildata, $request->getPost('stockmarks', ''))) {
            $attach = $request->getPost('attachment', array());

            if (count($attach) > 0) {
                $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }
            $row = $S->getStockoutById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }
    public function pipelineDetailEditAction()
    {
        $request = $this->getRequest();
        $cid = $request->getParam('cid', '0');
        $stockoutstatus = $request->getParam('stockoutstatus', '0');

        $params = $request->getRequest();

        $params['stockouttype'] = 3;
        $params['stockoutstatus'] = $stockoutstatus;

        $goodsnature = $request->getRequest('goodsnature', '0');
        $goodsnature_arr = array('0' => '', '1' => '保税', '2' => '外贸', '3' => '内贸转出口', '4' => '内贸内销');
        $params['goodsnaturemark'] = $goodsnature_arr[$goodsnature];
        $this->getView()->make('stockoutpipeline.detailedit', $params);

    }
    //管出库订单EXCEL导出
    public function pipelinedbtoexcelAction()
    {

        $request = $this->getRequest();

        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'bar_receivenumber' => $request->getPost('bar_receivenumber',''),
            'bar_goodsname' => $request->getPost('bar_goodsname',''),
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'bar_stockoutstatus' => $request->getPost('bar_stockoutstatus', '-100'),
            'page' => false,
            'orders' => 'created_at desc',
            'stockouttype' => 3,
        );

        $S = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStockout($search);


        /*------------------查询筛选条件返回参数-----------------*/

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("管出库订单列表")
            ->setSubject("管出库订单列表")
            ->setDescription("管出库订单列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '出库单号'),
            array('B1:B1', 'B1', '005E9CD3', '客户'),
            array('C1:C1', 'C1', '005E9CD3', '品名'),//
            array('D1:D1', 'D1', '0094CE58', '预提数量'),
            array('E1:E1', 'E1', '0094CE58', '罐检数量'),
            array('F1:F1', 'F1', '0094CE58', '客服'),
            array('G1:G1', 'G1', '0094CE58', '单据状态'),
        );

        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('管出库订单列表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G');
        foreach ($list['list'] as $item) {

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
                        $value = $item['takeqty'];
                        break;
                    case 4:
                        $value = $item['bussinesscheckqty'];
                        break;
                    case 5:
                        $value = $item['cs_employeename'];
                        break;
                    case 6:
                        switch($item['stockoutstatus']){
                            case "2":
                                $value = "暂存";
                                break;
                            case "3":
                                $value = "待审核";
                                break;
                            case "4":
                                $value = "已审核";
                                break;
                            case '5':
                                $value = "作废";
                                break;
                            default:
                                $value = "新建";
                        }
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }

// Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="管出库订单列表.xlsx"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

    }

    //执行退回
    public function executeBackAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id',0);

        $S = new StockoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $L = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $user = Yaf_Registry::get(SSN_VAR);

        $data = array(
            'stockoutstatus' => 6
        );
        $stockoutInfo = $S->getStockoutById($id);
        if(!$stockoutInfo){
            COMMON::result(300, '出库订单信息有误');
            return;
        }
        $res = $S->updateStockoutData($id, $data);
        if ($res) {
            $input = array(
                'doc_sysno' => $id,
                'opertype' => 5,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $stockoutmarks
            );

            if ($stockoutInfo['stockouttype'] == 1) {
                $input['doctype'] = 5;
            }elseif($stockoutInfo['stockouttype'] == 3){
                $input['doctype'] = 26;
            }
            

            $L->addDocLog($input);
            echo json_encode(array('code' => 200,'msg'=>'退回成功'));
            die();
        } else {
            echo json_encode(array('code' => 200,'msg'=>'退回失败'));
            die();
        }
        
    }

    //核单打印
    public function executePrintAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $type = $request->getParam('type',0);
        $S = new StockoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $stockoutData = $S->getStockoutDetailData($id);
        if(!$stockoutData){
            echo json_encode(array('code' => 300,'msg' => '出库信息有误'));
            die();
        }
        if($type == 1){
            if($stockoutData['stockoutstatus'] != 4){
                echo json_encode(array('code' => 300,'msg' => '只有已审核的单据才可以打印'));
                die();
            }
        }
        if($type == 2){
            if($stockoutData['stockoutstatus'] != 8){
                echo json_encode(array('code' => 300,'msg' => '只有待执行单据才可以打印'));
                die();
            }
        }
        echo json_encode($stockoutData);

    }
    
}

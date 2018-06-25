<?php
/**
 * Created by PhpStorm.
 * User: Jay Xu
 * Date: 2017/07/07 0018
 * Time: 15:08
 */
class BerthorderController extends Yaf_Controller_Abstract
{
    /**
     * IndexController::init()
     *
     * @return void
     */
    public $businesstype = array(
        1 => '船入库预约',
        2 => '船入库订单',
        3 => '车入库预约',
        4 => '车入库订单',
        5 => '管入库预约',
        6 => '管入库订单',
        7 => '船出库预约',
        8 => '船出库订单',
        9 => '车出库预约',
        10 => '车出库订单',
        11 => '管出库预约',
        12 => '管出库订单',
        13 => '靠泊装卸入预约',
        14 => '靠泊装卸出预约',
        15 => '靠泊装卸入订单',
        16 => '靠泊装卸出订单',
    );
    public $B = null;

    public function init()
    {

        # parent::init();
        $this->B = new BerthorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
    }

    /**
     * 泊位管理
     * @author Jay Xu
     */

    public function listAction()
    {
        //业务类型
        $params = array();
        $params['list'] = $this->businesstype;
        $this->getView()->make('berthorder.list', $params);
    }

    /**
     * 泊位分配列表JSON
     * @author Jay Xu
     */

    public function ListJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'startTime' => $request->getPost('startTime', ''),
            'endTime' => $request->getPost('endTime', ''),
            'businesstype' => $request->getPost('businesstype', ''),
            'orderstatus' => $request->getPost('orderstatus', ''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );

        $B = new BerthorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $B->searchBerthorder($search);
        $bookno = array(1, 3, 5, 7, 9, 11, 13, 14);    //预约状态
        $stockno = array(2, 4, 6, 8, 10, 12, 15, 16);  //入库状态

        foreach ($list['list'] as $key => $value) {
            //判断业务单号
            if (in_array($value['businesstype'], $bookno)) {
                $list['list'][$key]['orderno'] = $value['bookingno'];
            }
            if (in_array($value['businesstype'], $stockno)) {
                $list['list'][$key]['orderno'] = $value['stockno'];
            }
            $list['list'][$key]['beintime'] = date('Y-m-d', strtotime($value['beintime']));
            $list['list'][$key]['beouttime'] = date('Y-m-d', strtotime($value['beouttime']));
        }

        echo json_encode($list);
    }

    /*
 * 添加页面
 * */
    public function AddeditAction()
    {
        $request = $this->getRequest();
        $params['list'] = $request->getPost('selectedDatasArray', array());
        $params['type'] = $request->getParam('type', '');

        $this->getView()->make('berthorder.adddetail', $params);

    }


    public function EditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);

        $B = new BerthorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if (!$id) {
            COMMON::result(300, '数据异常');
            return;
        }

        $mode = $request->getParam('mode');
        $action = "/berthorder/EditJson/";
        //获取主表信息
        $params['list'] = $B->getBerthorderById($id);
        //业务类型
        $params['businesstype'] = $this->businesstype;
        $bookno = array(1, 3, 5, 7, 9, 11, 13, 14);    //预约状态
        $stockno = array(2, 4, 6, 8, 10, 12, 15, 16);  //入库状态
        //获取业务单号
        if (!$params['list']['businesstype']) {
            COMMON::result(300, '业务单号类型不能为空');
            return;
        }
        if (in_array($params['list']['businesstype'], $bookno)) {
            $params['list']['orderno'] = $params['list']['bookingno'];
        }
        if (in_array($params['list']['businesstype'], $stockno)) {
            $params['list']['orderno'] = $params['list']['stockno'];
        }

        //获取申请人
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
        $this->getView()->make('berthorder.edit', $params);
    }

    public function EditJsonAction()
    {

        $request = $this->getRequest();
        $id = $request->getPost('id', '');
        if (!$id) {
            COMMON::result(300, '数据异常');
            return;
        }

        //泊位分配明细表信息
        $detail = $request->getPost('berthdetaildata', '');
        $detail = json_decode($detail, true);

        //预约明细
        $getbookdata = $request->getPost('getbookdata','');
        $getbookdata = json_decode($getbookdata, true);

        if (count($detail) == 0) {
            COMMON::result(300, '泊位分配单明细不能为空');
            return;
        }

        $B = new BerthorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $step = $request->getPost('step', '');

        $input = array(
            'berthorderno' => $request->getPost('berthorderno', ''),                //泊位单编号
            'orderno' => $request->getPost('orderno', ''),                          //业务单号
            'businesstype' => $request->getPost('businesstype', ''),                //业务类型
            'booking_sysno' => $request->getPost('booking_sysno', ''),              //所属预约单表主键
            'bookingno' => $request->getPost('bookingno', ''),                      //所属预约单编号
            'stock_sysno' => $request->getPost('stock_sysno', ''),                  //所属订单表主键
            'stockno' => $request->getPost('stockno', ''),                          //所属订单编号
            'applydate' => $request->getPost('applydate', ''),                      //申请时间
            'apply_user_sysno' => $request->getPost('apply_user_sysno', ''),        //申请人
            'apply_employeename' => $request->getPost('apply_employeename', ''),    //冗余申请人姓名
            'bookingdate' => $request->getPost('bookingdate', ''),                  //预计到货（港）日期
            'orderstatus' => $request->getPost('orderstatus', ''),                  //单据状态
            'status' => $request->getPost('status', '1'),
            'updated_at' => '=NOW()'
        );
        $input['applydate'] = $input['applydate'] ? $input['applydate'] : date('Y-m-d');
        $res = $B->addBerthorder($detail, $input, $id, $step,$getbookdata);

        if ($res['statusCode'] == 200) {
            $params['id'] = $res['msg'];
            $params['page'] = false;
            $row = $B->searchBerthorder($params);
            COMMON::result(200, '新增成功', $row);
        } else {
            COMMON::result(300, '新增失败：' . $res['msg']);
        };
    }


    public function DelJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        $B = new BerthorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'isdel' => 1
        );

        if ($B->updateBerth($id, $input)) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败');
        }
    }

    /*
 * 明细表json
 *
 *  */
    public function detailJsonAction()
    {

        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $B = new BerthorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if ($id) {
            $where['sysno'] = $id;
            $list = $B->getListDetails($where);
        }

        $list = $list ? $list : array();
/*        if (!empty($list)) {
            foreach ($list as $key => $value) {
                $list[$key]['planintime'] = date('Y-m-d', strtotime($value['planintime']));
                $list[$key]['planouttime'] = date('Y-m-d', strtotime($value['planouttime']));
                $list[$key]['beintime'] = date('Y-m-d', strtotime($value['beintime']));
                $list[$key]['beouttime'] = date('Y-m-d', strtotime($value['beouttime']));
            }
        }*/

        echo json_encode($list);

    }

    //获取基础资料-泊位管理数据
    public function berthJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');

        $B = new BerthModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $search = array(
            'page' => false,
            'bar_status' => 1,
            'iscurrent' => 1,
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR()
        );
        $list = $B->searchBerth($search);
        foreach ($list['list'] as $key => $value) {
            if ($value['berthtype'] == 0) {
                $list['list'][$key]['berthtype'] = '不限';
            }
            if ($value['status'] == 1) {
                $list['list'][$key]['status'] = '启用';
            } elseif ($value['status'] == 2) {
                $list['list'][$key]['status'] = '停用';
            }
        }
        echo json_encode($list);
    }

    //获取基础资料--船舶管理数据
    public function ShipJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');

        $S = new SupplierModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $search = array(
            'page' => false,
            'bar_status' => 1,
            'iscurrent' => 1,
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR()
        );
        $list = $S->searchShipList($search);

        echo json_encode($list);
    }

    //预约明细
    public function getbookJsonAction()
    {
        $request = $this->getRequest();
        $berth_id = $request->getParam('id', '0');

        if ($berth_id) {
            //获取泊位入库单详情
            $info = $this->B->getBerthorderById($berth_id);
            $id = $info['booking_sysno'];
            $businesstype = $info['businesstype'];

            switch($businesstype){
                case ($businesstype==1 || $businesstype==2):
                    $S = new BookshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
                    $list = $S->getBookshipindetailById($id);

                    foreach ($list as $key => $value) {
                        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                        $data = $quality->getQualityById($value['goods_quality_sysno']);
                        $list[$key]['qualityname'] = $data['qualityname'];
                        $list[$key]['bookingindate'] = $value['bookingindate'];
                        $list[$key]['tobeqty'] =$value['bookinginqty'];
                    }
                    //   print_r($list);die;
                    echo json_encode($list);
                    break;
                case ($businesstype==3 || $businesstype==4):
                    $S = new BookcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
                    $list = $S->getBookcarindetailById($id);
                    if (!empty($list)) {
                        foreach ($list as $key => $value) {
                            $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                            $data = $quality->getQualityById($value['goods_quality_sysno']);
                            $list[$key]['qualityname'] = $data['qualityname'];
                            $list[$key]['goods_sysno'] = $value['goods_sysno'];
                            $list[$key]['goodsname'] = $value['goodsname'];
                            $list[$key]['tobeqty'] = $value['bookinginqty'];
                            $list[$key]['bookingindate'] = $value['bookingindate'];
                        }
                    }
                    echo json_encode($list);
                    break;
                case ($businesstype==5 || $businesstype==6):
                    $S = new BookshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
                    $list = $S->getBookshipindetailById($id);

                    if ($id && !empty($list)) {
                        foreach ($list as $key => $value) {
                            $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                            $data = $quality->getQualityById($value['goods_quality_sysno']);
                            $list[$key]['qualityname'] = $data['qualityname'];
                            $list[$key]['goodsname'] = $value['goodsname'];
                            $list[$key]['tobeqty'] = $value['bookinginqty'];
                            $list[$key]['bookingindate'] = $value['bookingindate'];
                        }
                    }
                    //print_r($list);die;
                    echo json_encode($list);
                    break;
                case ($businesstype==7 || $businesstype==8):
                    $B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
                    $search = array(
                        'bookingout_sysno' =>	$id,
                        'page' => false
                    );
                    $detailData =  $B->getBookoutDetailList($search);
                    foreach ($detailData['list'] as $key => $value) {
                        $detailData['list'][$key]['tobeqty'] = $value['bookingoutqty'];
                        $detailData['list'][$key]['unitname'] = '吨';
                        $detailData['list'][$key]['bookingindate'] = $value['shipokdate'];
                    }
                    //  print_r($detailData['list']);die;
                    echo  json_encode($detailData['list']) ;
                    break;
                case ($businesstype==9 || $businesstype==10):
                    //明细数据
                    $B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
                    $search = array(
                        'bookingout_sysno' => $id,
                        'page' => false
                    );
                    $detailData =  $B->getBookoutDetailList($search);
                    foreach ($detailData['list'] as $key => $value) {
                        $detailData['list'][$key]['tobeqty'] = $value['bookingoutqty'];
                        $detailData['list'][$key]['unitname'] = '吨';
                        $detailData['list'][$key]['bookingindate'] = $value['shipokdate'];
                    }
                    echo json_encode($detailData);
                    break;
                case ($businesstype==11 || $businesstype==12):
                    $B = new BookoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
                    $search = array(
                        'bookingout_sysno' =>	$id,
                        'page' => false
                    );
                    $detailData =  $B->getBookoutDetailList($search);
                    foreach ($detailData['list'] as $key => $value) {
                        $detailData['list'][$key]['tobeqty'] = $value['bookingoutqty'];
                        $detailData['list'][$key]['unitname'] = '吨';
                        $detailData['list'][$key]['bookingindate'] = $value['shipokdate'];
                    }
                    // print_r($detailData);die;
                    echo json_encode($detailData['list']) ;
                    break;
                case ($businesstype==13 || $businesstype==15):
                    $B = new BookberthinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
                    $detailData = $B->getBookberthindetailById($id);

                    foreach ($detailData as $key => $value) {
                        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                        $T = new RetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                        if($value['goods_quality_sysno']){
                            $data = $quality->getQualityById($value['goods_quality_sysno']);
                        }
                        if($value['storagetank_sysno']){
                            $detailData[$key]['storagetankname']  =$T->getinstoragetankById($value['storagetank_sysno']);
                        }

                        $detailData[$key]['qualityname'] = $data['qualityname']? $data['qualityname']:0;
                        $detailData[$key]['tobeqty'] = $value['bookinginqty'];
                        $detailData[$key]['unitname'] = '吨';
                        $detailData[$key]['bookingindate'] = $value['bookingindate'];
                    }
                    // print_r($detailData);die;
                    echo json_encode($detailData);
                    break;
                case ($businesstype==14 || $businesstype==16):
                    $B = new BookberthoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
                    $detailData = $B->getBookberthoutdetailById($id);

                    foreach ($detailData as $key => $value) {
                        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                        $T = new RetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                        if($value['goods_quality_sysno']){
                            $data = $quality->getQualityById($value['goods_quality_sysno']);
                        }
                        if($value['storagetank_sysno']){
                            $detailData[$key]['storagetankname']  =$T->getinstoragetankById($value['storagetank_sysno']);
                        }
                        $detailData[$key]['qualityname'] = $data['qualityname']? $data['qualityname']:0;
                        $detailData[$key]['tobeqty'] = $value['bookingoutqty'];
                        $detailData[$key]['unitname'] = '吨';
                        $detailData[$key]['bookingindate'] = $value['shipokdate'];
                    }
                    echo json_encode($detailData);
                    break;
            }
        }else{
            COMMON::result('300','数据异常');
        }



    }
}
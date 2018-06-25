<?php
/**
 * @Author: danxiaobing
 * @Date:   2017-2-14
 * @Last Modified by:   danxiaobing
 * @Last Modified time: 2016-2-14
 */
class WebapiController extends Yaf_Controller_Abstract
{
    /**
     * @var request 请求request
     */
    public $request;

    /**
     * @var vendorDetail 默认仓储信息
     */
    public $vendorDetail;

    /**
     * IndexController::init()
     *
     * @return void
     */
    public function init()
    {
        $this->request = $this->getRequest();
        $vendorInstance = new VendorModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $this -> vendorDetail = $vendorInstance -> getHengyangVendor();
    }

    /**
     * 测试网络通讯是否成功
     */
    public function testAction(){
        COMMON::ApiJson('200','请求成功');
    }

    /**
     * 手机端排号页面
     */
    public function getQueueAction(){
        $sysno = $this -> request -> getParam('sysno', 0);
        if(!$sysno){
            COMMON::ApiJson('300', '参数错误');
        }
        $carQueueInstance = new CarqueueModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $result = $carQueueInstance -> getCarRecodeById($sysno);
        if(!$result){
            COMMON::ApiJson('300', '未找到数据');
        }
        $params['title'] = '排号单';
        $result['vendor_name'] = $this -> vendorDetail['vendorname'];
        $result['doc_source'] = ($result['doc_type'] == 1) ? '车辆装货' : '车辆卸货';
        $result['time'] = date('Y-m-d H:i:s');
//        $return = [
//            'company' => $this->vendorDetail['vendorname'],
//            'numtype' => $result['car_queue_status'],
//            'numstate' => [
//                [
//                    'name'=> '前面等待车辆',
//                    'value'=> $result['num'],
//                    'ntype'=> '辆',
//                ],[
//                    'name'=> '预计等待时间',
//                    'value'=> $result['queuetime'],
//                    'ntype'=> '分钟',
//                ]
//            ],
//            'message' => [
//                'name' => "作业明细",
//                'arr' => [
//                    [
//                        'name'=> '单据类型',
//                        'value'=> ($result['doc_type'] == 1) ? '车辆装货' : '车辆卸货',
//                    ],[
//                        'name'=> '车牌号码',
//                        'value'=> $result['carid'],
//                    ],[
//                        'name'=> '司机姓名',
//                        'value'=> $result['carname'],
//                    ],[
//                        'name'=> '联系方式',
//                        'value'=> $result['mobilephone'],
//                    ],[
//                        'name'=> '鹤位号码',
//                        'value'=> $result['queueno'],
//                    ],[
//                        'name'=> '货品名称',
//                        'value'=> $result['goodsname'],
//                    ],[
//                        'name'=> '作业吨数',
//                        'value'=> $result['estimateqty'],
//                    ],[
//                        'name'=> '地磅编号',
//                        'value'=> $result['loadometer'],
//                    ],
//                ]
//            ],
//            'time' => date('Y-m-d H:i:s')
//        ];
        $this->getView()->make('webapi.queue', $result);
//        COMMON::ApiJson('200', '请求成功', self::responseResult($return));
    }

    /**
     * 车入库单信息
     */
    public function carInDetailAction(){
        $sysno = $this -> request -> getParam('sysno', 0);
        if(!$sysno){
            COMMON::ApiJson('300', '参数错误');
        }
        $S = new StockcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $stockInDetail = $S->getStockcarinById($sysno);
        if(!$stockInDetail){
            COMMON::ApiJson('300', '没有找到相关单据信息，请联系库区相关人员或者稍后再试。');
        }
        $params = [];
        $detailData = $S->getStockcarindetailById($sysno);
        $tobeqty = 0;
        $beqty = 0;
        foreach ($detailData as $value){
            $tobeqty += floatval($value['tobeqty']);
            $beqty += floatval($value['beqty']);
        }
        //查询该单下的所有的磅码单
        $carList = $S -> getPoundsByStockInId($sysno);
        $params['title'] = '入库单信息';
        $params['tobeqty'] = sprintf('%.3f', $tobeqty);
        $params['beqty'] = sprintf('%.3f', $beqty);
        $params['daiti_beqty'] = sprintf('%.3f', floatval($params['tobeqty']) - floatval($params['beqty']));
        $params['vendor_name'] = $this->vendorDetail['vendorname'];
        $params['count'] = count($carList);
        $params['carList'] = $carList;
        $params['time'] = date('Y-m-d H:i:s');
//        $return =[
//            'id' => $params['sysno'],
//            'company' => $this->vendorDetail['vendorname'],
//            'type' => '入库单',
//            'countNum'=> [
//                [
//                    'name' => '通知数量',
//                    'countmoney'=> sprintf('%.3f', $tobeqty)
//                ],[
//                    'name' => '已入库数量',
//                    'countmoney'=> sprintf('%.3f', $beqty)
//                ],[
//                    'name' => '已入库数量',
//                    'countmoney'=> sprintf('%.3f', floatval($params['tobeqty']) - floatval($params['beqty']))
//                ]
//            ],
//            'count' => count($carList),
//        ];
//        foreach ($carList as $value){
//            $return['carnum'][]= [
//                'id'=> $value['sysno'],
//                'carname'=> $value['carid'],
//                'puttype'=> '已入库',
//                'putnum'=> $value['beqty'].'吨',
//                'driver'=> $value['carname'],
//                'phone'=> $value['mobilephone'],
//            ];
//        }
//        $return['time'] = date('Y-m-d H:i:s');
//        COMMON::ApiJson('200', '请求成功',self::responseResult($params));
        $this->getView()->make('webapi.carindetail', $params);
    }

    /**
     *车入库磅码单信息
     */
    public function carInPoundDetailAction(){
        $sysno = $this -> request -> getParam('sysno', 0);
        if(!$sysno){
            COMMON::ApiJson('300', '参数错误');
        }
        $poundInInstance = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $poundInInstance -> getPoundInfoById($sysno);
        if(!$params){
            COMMON::ApiJson('300', '没有找到相关单据信息，请联系库区相关人员或者稍后再试。');
        }
        $params['title'] = '入库磅码单';
        //查询罐区
        $params['guanqu'] = $poundInInstance -> getStankAreaNameByStankId($params['storagetank_sysno']);
        $params['vendor_name'] = $this->vendorDetail['vendorname'];
        $params['time'] = date('Y-m-d H:i:s');
//        $return = [
//            'company' => $this->vendorDetail['vendorname'],
//            'carname' => $params['carid'],
//            'cartype' => '已入库',
//            'carnum' => $params['beqty'],
//            'message' => [
//                [
//                    'name' => '基本信息',
//                    'arr' => [
//                        [
//                            'name' =>'单据类型',
//                            'value' => $params['poundsinno']
//                        ],[
//                            'name' =>'货品名称',
//                            'value' => $params['goodsname']
//                        ],[
//                            'name' =>'货主名称',
//                            'value' => $params['customername']
//                        ],[
//                            'name' =>'提货单号',
//                            'value' => $params['takegoodsno']
//                        ],[
//                            'name' =>'送货公司',
//                            'value' => $params['deliverycompany']
//                        ]
//                    ],
//                ],[
//                    'name' => '作业明细',
//                    'arr' => [
//                        [
//                            'name' =>'重车重量',
//                            'value' => $params['fullcarqty']
//                        ],[
//                            'name' =>'空车重量',
//                            'value' => $params['emptycarqty']
//                        ],[
//                            'name' =>'重车时间',
//                            'value' => $params['fullcartime']
//                        ],[
//                            'name' =>'空车时间',
//                            'value' => $params['emptycartime']
//                        ],[
//                            'name' =>'实际装货重量',
//                            'value' => $params['beqty']
//                        ],[
//                            'name' =>'储罐区域',
//                            'value' => $poundInInstance -> getStankAreaNameByStankId($params['storagetank_sysno'])
//                        ]
//                    ],
//                ]
//            ],
//            'time' => date('Y-m-d H:i:s')
//        ];
//        COMMON::ApiJson('200', '请求成功',self::responseResult($return));
        $this->getView()->make('webapi.carinpounddetail', $params);
    }

    /**
     * 车出库订单信息
     */
    public function carOutAction(){
        $sysno = $this -> request -> getParam('sysno', 0);
        if(!$sysno){
            COMMON::ApiJson('300', '参数错误');
        }
        $stockOutInstance = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $poundsDetail = $stockOutInstance -> getStockOutByPoundsId($sysno);
        $params['title'] = '提货单';
        $params['vendor_name'] = $this->vendorDetail['vendorname'];
        $params['poundsDetail'] = $poundsDetail;
        $params['time'] = date('Y-m-d H:i:s');
        $this->getView()->make('webapi.carout', $params);
    }

    /**
     * 车出库单信息
     */
    public function carOutDetailAction(){
        $sysno = $this -> request -> getParam('sysno', 0);
        if(!$sysno){
            COMMON::ApiJson('300', '参数错误');
        }
        $stockOutInstance = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
//        $stockOut = $stockOutInstance -> getStockoutById($sysno);
        $stockOutDetail = $stockOutInstance -> getStockOutDetailByStockOutSysno($sysno);
        $tobeqty = 0;
        $beqty = 0;
        foreach ($stockOutDetail as $value){
            $tobeqty += floatval($value['tobeqty']);
            $beqty += floatval($value['beqty']);
        }
        $params['title'] = '出库单信息';
        $params['tobeqty'] = sprintf('%.3f', $tobeqty);
        $params['beqty'] = sprintf('%.3f', $beqty);
        $params['daiti_beqty'] = sprintf('%.3f', floatval($params['tobeqty']) - floatval($params['beqty']));
        $params['vendor_name'] = $this->vendorDetail['vendorname'];
        $params['carList'] = $stockOutInstance -> getPoundsOutDetailByStockOutSysno($sysno);
        $params['count'] = count($params['carList']);
        $params['time'] = date('Y-m-d H:i:s');
        $this->getView()->make('webapi.caroutdetail', $params);
    }

    /**
     *车出库磅码单信息
     */
    public function carOutPoundDetailAction(){
        $sysno = $this -> request -> getParam('sysno', 0);
        if(!$sysno){
            COMMON::ApiJson('300', '参数错误');
        }
        $poundsOutInstance = new PoundsapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $poundsOutInstance -> getOneCarOutApi($sysno);
        $params['title'] = '出库磅码单信息';
        $params['time'] = date('Y-m-d H:i:s');
        $this->getView()->make('webapi.caroutpounddetail', $params);
    }

    /**
     * 二维数组排序
     * @param $array 需要排序的数组
     * @param $key 需要排序的下标
     * @return array  返回整理后的数据
     */
    private static function sort_arr($array, $key){
        if(is_array($array)){
            $sort = array();
            foreach ($array as $value) {
                $sort[] = $value[$key];
            }
            array_multisort($sort, SORT_DESC, $array);
        }
        return $array;
    }

    /**
     * 处理返回数据为NULL的值
     * @param array $array
     * @return array
     */
    private static function responseResult(array $array){
        foreach($array as $key=>&$val) {
            if(is_array($val)) {
                $val = self::responseResult($val);
            } else {
                if($val === null){
                    $val = '';
                }
            }
        }
        return $array;
    }
}
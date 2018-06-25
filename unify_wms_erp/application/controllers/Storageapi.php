<?php
/**
 * @Author: danxiaobing
 * @Date:   2017-2-14
 * @Last Modified by:   danxiaobing
 * @Last Modified time: 2016-2-14
 */
class storageapiController extends Yaf_Controller_Abstract
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

    //测试rpc连接
    public function rpctestAction()
    {
        $config = Yaf_Application::app()->getConfig();
        $rpc    = $config->get("rpc");
        $Web = \Hprose\Http\Client::create($rpc->host.'/Wms_Outstock',false);
        $params['bookingno'] = '2017030955505049';
        $data = $Web->stockupFunc($params['bookingno']);
        //出库审核完成stockupFunc
        //出库完成stockoutFunc
        var_dump($data);
        exit;
    }

    /**
     * 测试网络通讯是否成功
     */
    public function testAction(){
        COMMON::ApiJson(200, '请求成功');
    }

    public function jiamiAction(){
//        $post = [
//            'model' => 'storageapi',
//            'action' => 'addCarShip',
//
//            'customerno' => 'C20170307546586',
//            'goodsno' => 'C00004',
//            'qualityno'=> '国标优等品',
//            'contract_no' => '合同编号',
//            'shipproxyname' => '预约时间',
//            'shipproxyname' => '船舶代理',
//            'bookingindate' => '预约时间',
//            'bookinginstatus' => '预约状态',
//            'businesscheckunitname' => '商检单位',
//            'shipname' => '船名',
//            'shipno' => '船编号',
//            'company' => '所属公司',
//            'captain' => '船长',
//            'shipcontact' => '船联系方式',
//            'shiploadweight' => '载重t',
//            'shiplength' => '载重t',
//            'shiploadweight' => '长度m',
//            'shipwidth' => '宽度',
//            'shiploadcapacity' => '吃水',
//            'unitname' => '单位',
//            'goodsnature' => '货物性质',
//            'quality_sysno' => '质量标准SYSNO',
//            'bookinginqty' => '数量',
//            'memo' => '备注',
//            'release_no' => '放行编号',
//            'declaration' => '报关单号'
//        ];
        $post = [
            'model' => 'storageapi',
            'action' => 'stcokDetailList',

//            'start_time' => '2017-4-10',
//            'end_time' => '2017-4-17',
            'customerno' => 'C17033111058654',
//            'goodsno' => 'C00003',
//            'qualityno'=> '国标优等品',
        ];
        $secret = new blowfish();
        $str = $secret -> encrypt(http_build_query($post));
        echo $str;
    }

    /**
     * 获取合同列表信息
     */
    public function getContractListAction(){
        $param = self::getParamAll(true);
        if($param['code'] != 200){
            COMMON::ApiJson($param['code'], $param['message']);
        }
        $search = array(
            'customerno_sysno' => $param['customerno_sysno'],
            'goods_sysno' => $param['goods_sysno'],
//            'quality_sysno' => $param['quality_sysno'],
            'page' => false
        );

        $C = new ContractModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $C->searchContractForApi($search);
        if(empty($list['list'])){
            COMMON::ApiJson(201, '未找到该用户的合同');
        }
        COMMON::ApiJson(200, '请求成功', self::responseResult($list['list']));
    }

    /**
     *	货权及库存验证
     */
    public function getStorageStockListAction(){
        $param = self::getParamAll(true);
        if($param['code'] != 200){
            COMMON::ApiJson($param['code'], $param['message']);
        }
        $search = array(
            'customerno_sysno' => $param['customerno_sysno'],
            'goods_sysno' => $param['goods_sysno'],
            'quality_sysno' => $param['quality_sysno'],
        );
        $storageapiInstances = new StorageapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $num = $storageapiInstances -> getStorageStock($search);
        $search['num'] = $num ? $num : 0;
        COMMON::ApiJson(200, '请求成功', self::responseResult($search));
    }

    /**
     * 查询入库单列表
     */
    public function getStockListAction(){
        $param = self::getParamAll(true);
        if($param['code'] != 200){
            COMMON::ApiJson($param['code'], $param['message']);
        }

        $stockInstances = new StockModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $stockInstances->getAllStockForApi($param['customerno_sysno'], $param['goods_sysno'], $param['quality_sysno']);
        COMMON::ApiJson(200, '请求成功', self::responseResult($list));
    }

    /**
     * 新增入库预约单-车
     * 会员编号*、合同编号*、提货单号、是否商检（1是0否）*、送检方式（1送检2取样）*，商检单位、
     * 品名*、规格*、货物性质*、放行编号、报关单号、预计到货日期*、通知数量*,
     * 车数组array（车牌号*，司机姓名*，手机*，身份证号*）
     */
    public function addCarBookingAction(){
        //根据用户编号获取用户SYSNO
        $params = self::getParamAll(true);
        if($params['code'] != 200){
            COMMON::ApiJson($params['code'], $params['message']);
        }
        $storageapiInstance = new StorageapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $customerno_sysno = $params['customerno_sysno'];
        $customer_name = $storageapiInstance -> getUserNameByCustomerSysno($customerno_sysno);
        $contract_no = $this -> request -> getParam('contract_no', ''); //合同编号
        $contractnodisplay = $storageapiInstance -> getDisplaynoByContractno($contract_no);
        $isbusinesscheck = $this -> request -> getParam('isbusinesscheck', 1); //是否商检 1是0否（车预约单用）
        $businesschecktype = $this -> request -> getParam('businesschecktype', 1); //检验方式 1送检2取样 (车预约单用）
        $carname = $this -> request -> getParam('carname', ''); //承运公司
        $businesscheckunitname = $this -> request -> getParam('businesscheckunitname', ''); //商检单位
        $bookingindate = date("Y-m-d"); //预约时间
        $bookinginstatus = 2; //预约状态
        $takegoodsno = $this -> request -> getParam('takegoodsno', ''); //提货单号
        //CA新增字段
        $ca_number = $this -> request -> getParam('ca_number', ''); //CA编号
        $ca_viewpath = $this -> request -> getParam('ca_viewpath', ''); //CA路径
        $carArr = $this -> request -> getParam('carArr', []);
        $bookcarincarsdata = [];
        if(!empty($carArr)){
            $carArr = $this -> getArrayUniqueByKeys($carArr);
            foreach($carArr as $key => $value){
                if( $value['carid'] != '') {
                    $bookcarincarsdata[$key]['carname'] = $value['carname'] ? $value['carname'] : ''; //司机姓名
                    $bookcarincarsdata[$key]['mobilephone'] = $value['mobilephone'] ? $value['mobilephone'] : ''; //司机手机号
                    $bookcarincarsdata[$key]['idcard'] = $value['idcard'] ? $value['idcard'] : ''; //身份证号
                    $bookcarincarsdata[$key]['carmarks'] = $value['carmarks'] ? $value['carmarks'] : ''; //备注
                    $bookcarincarsdata[$key]['carid'] = $value['carid'] ? $value['carid'] : ''; //车牌号
                    $bookcarincarsdata[$key]['weight'] = $value['weight'] ? $value['weight'] : ''; //载重量(吨)
                }
            }
        }

        $bookcarindetaildata['goods_sysno'] = $params['goods_sysno']; //商品ID
        $bookcarindetaildata['unitname'] = $this->request-> getParam('unitname', '吨'); //单位
        $bookcarindetaildata['goodsnature'] = $this->request-> getParam('goodsnature', 1); //货物性质
        $bookcarindetaildata['goods_quality_sysno'] = $params['quality_sysno']; //质量标准
        $bookcarindetaildata['bookinginqty'] = $this->request-> getParam('bookinginqty', 0); //数量
        $bookcarindetaildata['memo'] = $this->request-> getParam('memo', ''); //备注
        $bookcarindetaildata['release_no'] = $this->request-> getParam('release_no', ''); //放行编号
        $bookcarindetaildata['declaration'] = $this->request-> getParam('declaration', ''); //报关单号
        $bookcarindetaildata['bookingindate'] = $this -> request -> getParam('bookingindate', date("Y-m-d")); //预计到货（港）日期


        //判断该合同编号是否是该用户的合同
        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $isContarct = $C -> getContractByCusomerSysnoForApi($customerno_sysno, $contract_no);
        if(empty($isContarct)){
            COMMON::ApiJson(302, '此合同不是该用户的合同');
        }
        if($isContarct['contractenddate'] < date('Y-m-d')){
            COMMON::ApiJson(302, '此合同已过期请重新选择');
        }
//        if(!$businesscheckunitname){
//            COMMON::ApiJson(300, '商检单位必填');
//        }

        $S = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'stockintype' => 2,
            'bookinginno' => COMMON::getCodeId('B'),
            'bookingindate' => $bookingindate,
            'customer_sysno' => $customerno_sysno,
            'customer_name' => $customer_name,
            'contract_sysno' => $isContarct['sysno'],
            'contract_no' => $contractnodisplay,
            'contracttype' => $isContarct['contracttype'],
            'docsource' => 2,
            'takegoodsno' => $takegoodsno,
            'carname' => $carname,
            'issave' => 1,
            'isbusinesscheck' => $isbusinesscheck,
            'businesschecktype' => $businesschecktype,
            'businesscheckunitname' => $businesscheckunitname,
            'bookinginstatus' => $bookinginstatus,
            'status' => 1,
            'isdel' => 0,
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
            //CA新增字段
            'ca_no' => $ca_number,
            'ca_address' => $ca_viewpath,
        );

        //若车辆信息不在数据库中，则登记车辆信息
        if(!empty($bookcarincarsdata)){
            $Su = new SupplierModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $Su -> checkCardata($bookcarincarsdata);
        }
        $id = $S->addBookcarinForApi($input, $bookcarindetaildata, $bookcarincarsdata, $bookinginstatus);
        if (!$id) {
            COMMON::ApiJson(300, '添加失败');
        }
        COMMON::ApiJson(200, '添加成功', self::responseResult(['sysno' => $id,'bookinginno' => $input['bookinginno']]));
    }

    /**
     * 新增入库预约单-船
     * 会员编号*、合同编号*、船舶代理、商检单位*、预计到港日期*、客服专员
     * 品名*、规格*、货物性质*、放行编号、报关单号、通知数量*、
     * 船名*、船编号、所属公司*、船长*、船联系方式、载重t、长度m、宽度、吃水m
     */
    public function addCarShipAction(){
        //根据用户编号获取用户SYSNO
        $params = self::getParamAll(true);
        if($params['code'] != 200){
            COMMON::ApiJson($params['code'], $params['message']);
        }
        $storageapiInstance = new StorageapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $customerno_sysno = $params['customerno_sysno'];
        $customer_name = $storageapiInstance -> getUserNameByCustomerSysno($customerno_sysno);
        $contract_no = $this -> request -> getParam('contract_no', ''); //合同编号
        $contractnodisplay = $storageapiInstance -> getDisplaynoByContractno($contract_no);
        $shipproxyname = $this -> request -> getParam('shipproxyname', ''); //船舶代理
        $bookingindate = date("Y-m-d"); //预约时间
        $bookinginstatus = 2; //预约状态
        $businesscheckunitname = $this -> request -> getParam('businesscheckunitname', ''); //商检单位
        //CA新增字段
        $ca_number = $this -> request -> getParam('ca_number', ''); //CA编号
        $ca_viewpath = $this -> request -> getParam('ca_viewpath', ''); //CA路径

        $ship['shipname'] = $this->request-> getParam('shipname', ''); //船名
        $ship['shipno'] = $this->request-> getParam('shipno', ''); //船编号
        $ship['company'] = $this->request-> getParam('company', ''); //所属公司
        $ship['captain'] = $this->request-> getParam('captain', ''); //船长
        $ship['shipcontact'] = $this->request-> getParam('shipcontact', ''); //船联系方式
        $ship['shiploadweight'] = $this->request-> getParam('shiploadweight', ''); //载重t
        $ship['shiplength'] = $this->request-> getParam('shiplength', ''); //长度m
        $ship['shipwidth'] = $this->request-> getParam('shipwidth', ''); //宽度
        $ship['shiploadcapacity'] = $this->request-> getParam('shiploadcapacity', ''); //吃水
        $ship['created_at'] = "=NOW()";
        $ship['updated_at'] = "=NOW()";
        //插入船基本信息
        $shipInstance = new SupplierModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        //根据船名查询数据库里是否存在船 如果存在则负略 如果不存在则添加
        if($ship['shipname'] && !$shipInstance -> checkshipForApi($ship['shipname'])){
            $shipInstance -> addShip($ship);
        }
        $bookshipindetaildata['goods_sysno'] = $params['goods_sysno']; //商品ID
        $bookshipindetaildata['unitname'] = $this->request-> getParam('unitname', '吨'); //单位
        $bookshipindetaildata['goodsnature'] = $this->request-> getParam('goodsnature', 1); //货物性质
        $bookshipindetaildata['goods_quality_sysno'] = $params['quality_sysno']; //质量标准
        $bookshipindetaildata['bookinginqty'] = $this->request-> getParam('bookinginqty', 0); //数量
        $bookshipindetaildata['shipname'] = $ship['shipname']; //到货船名
        $bookshipindetaildata['memo'] = $this->request-> getParam('memo', ''); //备注
        $bookshipindetaildata['release_no'] = $this->request-> getParam('release_no', ''); //放行编号
        $bookshipindetaildata['declaration'] = $this->request-> getParam('declaration', ''); //报关单号
        $bookshipindetaildata['bookingindate'] = $this -> request -> getParam('bookingindate' , date("Y-m-d")); //预计到货（港）日期

        //判断该合同编号是否是该用户的合同
        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $isContarct = $C -> getContractByCusomerSysnoForApi($customerno_sysno, $contract_no);
        if(empty($isContarct)){
            COMMON::ApiJson(302, '此合同不是该用户的合同');
        }
        if($isContarct['contractenddate'] < date('Y-m-d')){
            COMMON::ApiJson(302, '此合同已过期请重新选择');
        }
//        if(!$businesscheckunitname){
//            COMMON::ApiJson(300, '商检单位必填');
//        }
//        $bookshipindetaildata['storagetank_sysno'] = $this->request-> getParam('storagetank_sysno', 0); //储罐号
//        $T = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        //判断储罐里是否已还有容量
//        if ($T->storagetankgoodsbyid($bookshipindetaildata['storagetank_sysno']) != $bookshipindetaildata['goods_sysno']) {
//            COMMON::ApiJson(300, '该储罐还有其他货品存量');
//        }

        //查询罐容
//        $search = array(
//            'storagetank_sysno' => $bookshipindetaildata['storagetank_sysno'],
//        );
//        $available = $T->getStoragetankavailable($search);
//        if ($available < $bookshipindetaildata['bookinginqty']) {
//            COMMON::ApiJson(300, '该储罐可存放容量不足,当前储罐可用容量:' . $available . '吨');
//            return;
//        }

        $input = array(
            'stockintype' => 1,
            'bookinginno' => COMMON::getCodeId('B'),
            'bookingindate' => $bookingindate,
            'customer_sysno' => $customerno_sysno,
            'customer_name' => $customer_name ? $customer_name : '',
            'contract_sysno' => $isContarct['sysno'],
            'contract_no' => $contractnodisplay,
            'contracttype' => $isContarct['contracttype'],
            'docsource' => 2,
            'issave' => 1,
            'businesscheckunitname' => $businesscheckunitname,
            'shipproxyname' => $shipproxyname,
            'bookinginstatus' => $bookinginstatus,
            'status' => 1,
            'isdel' => 0,
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
            //CA新增字段
            'ca_no' => $ca_number,
            'ca_address' => $ca_viewpath,
        );
        $S = new BookshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $id = $S->addBookshipinForApi($input, $bookshipindetaildata);
        if (!$id) {
            COMMON::ApiJson(300, '添加失败');
        }
        COMMON::ApiJson(200, '添加成功',self::responseResult(['sysno' => $id,'bookinginno' => $input['bookinginno']]));
    }

    /**
     *	新增出库预约单-船
     */
    public function addOutShipAction(){
        //根据用户编号获取用户SYSNO
        $params = self::getParamAll(true);
        if($params['code'] != 200){
            COMMON::ApiJson($params['code'], $params['message']);
        }
        $storageapiInstance = new StorageapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $customerno_sysno = $params['customerno_sysno'];
        $customer_name = $storageapiInstance -> getUserNameByCustomerSysno($customerno_sysno);
        $shipproxyname = $this -> request -> getParam('shipproxyname', ''); //船舶代理
        $bookingoutdate = $this -> request -> getParam('bookingoutdate' , date("Y-m-d")); //预约时间
        $bookingoutstatus = 2; //预约状态
        $businesscheckunitname = $this -> request -> getParam('businesscheckunitname', ''); //商检单位
        $receivenumber = $this -> request -> getParam('receivenumber', ''); //提货单号
        $receiveunitname = $this -> request -> getParam('receiveunitname', ''); //提货单位
        $receivestart = $this -> request -> getParam('receivestart', ''); //提货区间开始时间
        $receiveend = $this -> request -> getParam('receiveend', ''); //提货区间结束时间
        //CA新增字段
        $ca_number = $this -> request -> getParam('ca_number', ''); //CA编号
        $ca_viewpath = $this -> request -> getParam('ca_viewpath', ''); //CA路径

        $ship['shipname'] = $this->request-> getParam('shipname', ''); //船名
        $ship['shipno'] = $this->request-> getParam('shipno', ''); //船编号
        $ship['company'] = $this->request-> getParam('company', ''); //所属公司
        $ship['captain'] = $this->request-> getParam('captain', ''); //船长
        $ship['shipcontact'] = $this->request-> getParam('shipcontact', ''); //船联系方式
        $ship['shiploadweight'] = $this->request-> getParam('shiploadweight', ''); //载重t
        $ship['shiplength'] = $this->request-> getParam('shiplength', ''); //长度m
        $ship['shipwidth'] = $this->request-> getParam('shipwidth', ''); //宽度
        $ship['shiploadcapacity'] = $this->request-> getParam('shiploadcapacity', ''); //吃水
        $ship['created_at'] = "=NOW()";
        $ship['updated_at'] = "=NOW()";
        //插入船基本信息
        $shipInstance = new SupplierModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        //根据船名查询数据库里是否存在船 如果存在则负略 如果不存在则添加
        if($ship['shipname'] && !$shipInstance -> checkshipForApi($ship['shipname'])){
            $shipInstance->addShip($ship);
        }
        $bookshipOutDetailData['goods_sysno'] = $params['goods_sysno']; //商品ID
        if($params['goods_sysno']) {
            $bookshipOutDetailData['goodsname'] = $storageapiInstance->getGoodsNameBySysno($bookshipOutDetailData['goods_sysno']);
            if (!$bookshipOutDetailData['goodsname']) {
                COMMON::ApiJson(300, '请检查商品编号是否正确');
            }
        }
        $bookshipOutDetailData['unitname'] = $this->request-> getParam('unitname', '吨'); //单位
        $bookshipOutDetailData['shipokdate'] = $this->request-> getParam('shipokdate', ''); //预约到岗日期
        $bookshipOutDetailData['stock_sysno'] = $this -> request -> getParam('stock_sysno', ''); //库存ID
        //根据库存查询该库存所在的罐号
        if(!$bookshipOutDetailData['stock_sysno']){
            COMMON::ApiJson(300, '请检查商品所属库存');
        }
        $bookshipOutDetailData['storagetank_sysno'] = $storageapiInstance -> getStoragetankSysnoByStock($bookshipOutDetailData['stock_sysno']);
        $bookshipOutDetailData['stockin_sysno'] = $this -> request -> getParam('stockin_sysno', ''); //入库ID
        $bookshipOutDetailData['stockin_no'] = $this -> request -> getParam('stockin_no', ''); //入库编号
        $bookshipOutDetailData['goodsnature'] = $this->request-> getParam('goodsnature', 1); //货物性质
        $bookshipOutDetailData['qualityname'] = $this -> request -> getParam('qualityno', ''); //质量标准名称
        $bookshipOutDetailData['bookingoutqty'] = $this->request-> getParam('bookingoutqty', 0); //数量
        $bookshipOutDetailData['noticenum'] = $this->request-> getParam('noticenum', 0); //通知数量
        $bookshipOutDetailData['shipname'] = $ship['shipname']; //到货船名
        $bookshipOutDetailData['memo'] = $this->request-> getParam('memo', ''); //备注
        $bookshipOutDetailData['release_no'] = $this->request-> getParam('release_no', ''); //放行编号
        $bookshipOutDetailData['declaration'] = $this->request-> getParam('declaration', ''); //报关单号
//        if(!$businesscheckunitname){
//            COMMON::ApiJson(300, '商检单位必填');
//        }

        //TODO 判断入库单号是否存在、是否余量足够

        $input = array(
            'stockouttype' => 1,
            'bookingoutno' => COMMON::getCodeId('D1'),
            'bookingoutdate' => $bookingoutdate,
            'customer_sysno' => $customerno_sysno,
            'customer_name' => $customer_name ? $customer_name : '',
            'docsource' => 2,
            'issaveorder' => 0,
            'businesscheckunitname' => $businesscheckunitname,
            'receivestart' => $receivestart,
            'receiveend' => $receiveend,
            'receivenumber' => $receivenumber,
            'receiveunitname' => $receiveunitname,
            'shipproxyname' => $shipproxyname,
            'bookingoutstatus' => $bookingoutstatus,
            'status' => 1,
            'isdel' => 0,
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
            //CA新增字段
            'ca_no' => $ca_number,
            'ca_address' => $ca_viewpath,
        );
        $S = new BookoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $id = $S->addBookshipoutForApi($input, $bookshipOutDetailData);
        if (!$id) {
            COMMON::ApiJson(300, '添加失败');
        }
        COMMON::ApiJson(200, '添加成功',self::responseResult(['sysno' => $id,'bookingoutno' => $input['bookingoutno']]));
    }

    /**
     *	新增出库预约单-车
     */
    public function addOutCarsAction(){
        //根据用户编号获取用户SYSNO
        $params = self::getParamAll(true);
        if($params['code'] != 200){
            COMMON::ApiJson($params['code'], $params['message']);
        }
        $storageapiInstance = new StorageapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $customerno_sysno = $params['customerno_sysno'];
        $customer_name = $storageapiInstance -> getUserNameByCustomerSysno($customerno_sysno);
        $isbusinesscheck = $this -> request -> getParam('isbusinesscheck', 1); //是否商检 1是0否（车预约单用）
        $businesschecktype = $this -> request -> getParam('businesschecktype', 1); //检验方式 1送检2取样 (车预约单用）
        $businesscheckunitname = $this -> request -> getParam('businesscheckunitname', ''); //商检单位
//        $receivebetween = $this -> request -> getParam('receivebetween', ''); //提货区间
        $receivestart = $this -> request -> getParam('receivestart', ''); //提货区间开始时间
        $receiveend = $this -> request -> getParam('receiveend', ''); //提货区间结束时间
        $bookingoutdate = $this -> request -> getParam('bookingoutdate' , date("Y-m-d")); //预约时间
        $bookingoutstatus = 2; //预约状态
        $receivenumber = $this -> request -> getParam('receivenumber', ''); //提货单号
        $receiveunitname = $this -> request -> getParam('receiveunitname', ''); //提货单位
        //CA新增字段
        $ca_number = $this -> request -> getParam('ca_number', ''); //CA编号
        $ca_viewpath = $this -> request -> getParam('ca_viewpath', ''); //CA路径

        $carArr = $this -> request -> getParam('carArr', []);
        $bookcarincarsdata = [];
        if(!empty($carArr)){
            $carArr = $this -> getArrayUniqueByKeys($carArr);
            foreach($carArr as $key => $value){
                if( $value['carid'] != '') {
                    $bookcarincarsdata[$key]['carname'] = $value['carname'] ? $value['carname'] : ''; //司机姓名
                    $bookcarincarsdata[$key]['mobilephone'] = $value['mobilephone'] ? $value['mobilephone'] : ''; //司机手机号
                    $bookcarincarsdata[$key]['idcard'] = $value['idcard'] ? $value['idcard'] : ''; //身份证号
                    $bookcarincarsdata[$key]['carmarks'] = $value['carmarks'] ? $value['carmarks'] : ''; //备注
                    $bookcarincarsdata[$key]['carid'] = $value['carid'] ? $value['carid'] : ''; //车牌号
                    $bookcarincarsdata[$key]['weight'] = $value['weight'] ? $value['weight'] : ''; //载重量(吨)
                }
            }
        }

        $bookCarOutDetailData['stock_sysno'] = $this -> request -> getParam('stock_sysno', ''); //库存ID
        //根据库存查询该库存所在的罐号
        if(!$bookCarOutDetailData['stock_sysno']){
            COMMON::ApiJson(300, '请检查商品所属库存');
        }
        $bookCarOutDetailData['storagetank_sysno'] = $storageapiInstance -> getStoragetankSysnoByStock($bookCarOutDetailData['stock_sysno']);
        $bookCarOutDetailData['stockin_sysno'] = $this -> request -> getParam('stockin_sysno', ''); //入库ID
        $bookCarOutDetailData['stockin_no'] = $this -> request -> getParam('stockin_no', ''); //入库编号
        $bookCarOutDetailData['bookingoutqty'] = $this->request-> getParam('bookingoutqty', 0); //数量
        $bookCarOutDetailData['carstartdate'] = $this->request-> getParam('carstartdate', date("Y-m-d")); //预计提货期间起始日
        $bookCarOutDetailData['carenddate'] = $this->request-> getParam('carenddate', date("Y-m-d")); //预计提货终止日
        $bookCarOutDetailData['noticenum'] = $this->request-> getParam('noticenum', 0); //通知数量
        $bookCarOutDetailData['memo'] = $this->request-> getParam('memo', ''); //备注
        $bookCarOutDetailData['goods_sysno'] = $params['goods_sysno']; //商品ID
        if($params['goods_sysno']) {
            $bookCarOutDetailData['goodsname'] = $storageapiInstance->getGoodsNameBySysno($bookCarOutDetailData['goods_sysno']); //商品名称
            if (!$bookCarOutDetailData['goodsname']) {
                COMMON::ApiJson(300, '请检查商品编号是否正确');
            }
        }else{
            COMMON::ApiJson(300, '请检查商品编号是否正确');
        }
        $bookCarOutDetailData['goodsnature'] = $this->request-> getParam('goodsnature', 1); //货物性质
        $bookCarOutDetailData['qualityname'] = $this -> request -> getParam('qualityno', ''); //质量标准名称
        $bookCarOutDetailData['unitname'] = $this->request-> getParam('unitname', '吨'); //单位
        $bookCarOutDetailData['release_no'] = $this->request-> getParam('release_no', ''); //放行编号
        $bookCarOutDetailData['declaration'] = $this->request-> getParam('declaration', ''); //报关单号
//        if(!$businesscheckunitname){
//            COMMON::ApiJson(300, '商检单位必填');
//        }

        $S = new BookoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'stockouttype' => 2,
            'bookingoutno' => COMMON::getCodeId('C1'),
            'bookingoutdate' => $bookingoutdate,
            'customer_sysno' => $customerno_sysno,
            'customer_name' => $customer_name ? $customer_name : '',
            'docsource' => 2,
            'issaveorder' => 0,
            'businesscheckunitname' => $businesscheckunitname,
//            'receivebetween' => $receivebetween,
            'receivestart' => $receivestart,
            'receiveend' => $receiveend,
            'receivenumber' => $receivenumber,
            'receiveunitname' => $receiveunitname,
            'isbusinesscheck' => $isbusinesscheck,
            'businesschecktype' => $businesschecktype,
            'bookingoutstatus' => $bookingoutstatus,
            'status' => 1,
            'isdel' => 0,
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
            //CA新增字段
            'ca_no' => $ca_number,
            'ca_address' => $ca_viewpath,
        );

        //若车辆信息不在数据库中，则登记车辆信息
        if(!empty($bookcarincarsdata)){
            $Su = new SupplierModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $Su -> checkCardata($bookcarincarsdata);
        }
        $id = $S->addCarBookoutForApi($input, $bookCarOutDetailData, $bookcarincarsdata);
        if (!$id) {
            COMMON::ApiJson(300, '添加失败');
        }
        COMMON::ApiJson(200, '添加成功', self::responseResult(['sysno' => $id, 'bookingoutno' => $input['bookingoutno']]));
    }

    /**
     * 新增货权转移单
     */
    public function stockTransAction(){
        $params = self::getParamAll();
        if($params['code'] != 200){
            COMMON::ApiJson($params['code'], $params['message']);
        }
        $storageApiInstance  = new StorageapiModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $stocktransstatus = 2;
        $buy_customerno = $this -> request -> getParam('buy_customerno', ''); //买方编号
        $stocktransdate = $this -> request -> getParam('stocktransdate', date("Y-m-d")); //转移日期
        $buystartdate = $this -> request -> getParam('buystartdate', date("Y-m-d")); //受让方计费起始日
        $transno = $this -> request -> getParam('transno', ''); //转让单号（提货单号）
        $transqty = $this -> request -> getParam('transqty', 0); //转让数量（提货数量）
        $contractno = $this -> request -> getParam('contractno', ''); //买方合同no
        $contract_sysno = $storageApiInstance->getSysnoByContractno($contractno); //买方合同id
        $sale_contractno = $this -> request -> getParam('sale_contractno', ''); //卖方合同no
        $sale_contract_sysno = $storageApiInstance->getSysnoByContractno($sale_contractno); //卖方合同sysno
        $freecostdate = $this -> request -> getParam('freecostdate', 0); //免仓天数
        $cost_contract_type = $this -> request -> getParam('cost_contract_type', 0); //合同计费
        $out_stock_sysno =  $this -> request -> getParam('out_stock_sysno', 0); //转出库存信息表主键
//        $in_stock_sysno =  $this -> request -> getParam('in_stock_sysno', 0); //转入库存信息表主键
        $memo =  $this -> request -> getParam('memo', 0); //备注
        //CA新增字段
        $ca_number = $this -> request -> getParam('ca_number', ''); //CA编号
        $ca_viewpath = $this -> request -> getParam('ca_viewpath', ''); //CA路径

        if($buy_customerno == ''){
            COMMON::ApiJson(300, '买方编号必填');
        }
        $buy_customer_sysno = $storageApiInstance -> getUserSysno($buy_customerno);
        if(!$buy_customer_sysno){
            COMMON::ApiJson(300, '买方编号错误');
        }
        if(!$cost_contract_type){
            COMMON::ApiJson(300, '请选择计费合同方');
        }
        if(!$transqty){
            COMMON::ApiJson(300, '转让数量必填');
        }
        $data = [
            'stocktransno' =>  COMMON ::getCodeId('T'), //货权转移编号
            'stocktransdate' => $stocktransdate,  //转移日期
            'docsource' => 2, //单据来源：1手工创建2国烨云仓3初始化导入
            'sale_customer_sysno' => $params['customerno_sysno'], //卖方sysno
            'sale_customername' => $storageApiInstance -> getUserNameByCustomerSysno($params['customerno_sysno']), //卖方名称
            'buy_customer_sysno' => $buy_customer_sysno, //买方Sysno
            'buy_customername' => $storageApiInstance -> getUserNameByCustomerSysno($buy_customer_sysno), //买方名称
            'buystartdate' => $buystartdate,
            'transno' => $transno,
            'transqty' => $transqty,
            'stocktransstatus' => $stocktransstatus,
            'contract_sysno' => $contract_sysno ? $contract_sysno : -100,
            'contractno' => $contractno,
            'sale_contract_sysno' => $sale_contract_sysno ? $sale_contract_sysno : -100,
            'sale_contractno' => $sale_contractno,
            'freecostdate' => $freecostdate,
            'cost_contract_type' => $cost_contract_type,
            //CA新增字段
            'ca_no' => $ca_number,
            'ca_address' => $ca_viewpath,
        ];

        if(in_array($stocktransstatus, [4,5,6])){
            COMMON::ApiJson(300, '请按照正规流程审核');
        }

        if(!$out_stock_sysno){
            COMMON::ApiJson(300, '转出 - 库存必填');
        }
        $S = new StockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $stockDetail = $S->getStockinfoByid($out_stock_sysno);
        if(!$stockDetail){
            COMMON::ApiJson(300, '转出 - 库存信息不存在');
        }
        if($stockDetail['customer_sysno'] != $params['customerno_sysno']){
            COMMON::ApiJson(300, '转出 - 此库存不是该用户的库存');
        }
        if(floatval($transqty) > (floatval($stockDetail['stockqty'])- floatval($stockDetail['clockqty']))){
            COMMON::ApiJson(300, '转出 - 库存余量不足');
        }
        $stocktransInstance = new StocktransModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        //添加顶点合同
        if($data['cost_contract_type']==1){
            if($data['sale_contract_sysno']==-100 ) {
                $data['sale_contract_sysno'] = $stockDetail['contract_sysno'];
                $data['sale_contractno'] = $stocktransInstance -> getContractName($data['sale_contract_sysno']);
            }
        }else{
            if( $data['contract_sysno']==-100 ) {
                $data['contract_sysno'] = $stockDetail['contract_sysno'];
                $data['contractno'] = $stocktransInstance -> getContractName($data['contract_sysno']);
            }
        }

        $detail = [
            'out_stock_sysno' => $out_stock_sysno,
            'transqty' => $data['transqty'],
            'memo' => $memo
        ];

        $res = $stocktransInstance -> addForApi($data, $detail);
        if($res['statusCode'] !=200 ) {
            COMMON::ApiJson(300, '新增预约单失败：' . $res['msg']);
        }
        COMMON::ApiJson(200, '新增预约单成功', self::responseResult(['id' => $res['msg'], 'stocktransno' => $data['stocktransno']]));
    }

    /**
     * 获取公共参数
     * @param bool $check 是否验证商品
     * @return array
     */
    private function getParamAll($check = false){
        $customerno_no = $this -> request -> getParam('customerno', '');
        $goods_no = $this -> request -> getParam('goodsno', '');
        $quality_no = $this -> request -> getParam('qualityno', '');
        $params['code'] = 200;
        if(!$customerno_no ){
            return $params = [
                'code' => 300,
                'message' => '参数错误'
            ];
        }
        $storageapiInstance = new StorageapiModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params['customerno_sysno'] = $storageapiInstance -> getUserSysno($customerno_no);
        if(!$params['customerno_sysno']){
            return $params = [
                'code' => 301,
                'message' => '未找到该客户'
            ];
        }
        $params['goods_sysno'] = $storageapiInstance -> getGoodsSysno($goods_no);
        if($check){
            if(!$params['goods_sysno']){
                return $params = [
                    'code' => 301,
                    'message' => '未找到该商品'
                ];
            }
        }
        $params['quality_sysno'] = $storageapiInstance -> getQualitySysno($quality_no);
//        if(!$params['quality_sysno']){
//            return $params = [
//                'code' => 301,
//                'message' => '未找到该质量标准'
//            ];
//        }
        return $params;
    }

    /**
     * 获取用户列表信息
     */
    public function getCustomerListAction(){
        $params = [
            'bar_status' => 1,
            'bar_isdel' => 0,
            'page' => false
        ];
        $customerInstance = new CustomerModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $userList = $customerInstance -> searchCustomerForApi($params);
        if(!$userList){
            COMMON::ApiJson(300, '未请求到数据');
        }
        COMMON::ApiJson(200, '请求成功', $userList['list']);
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

    /**
     *处理车辆相同情况
     * @param $arr 需要处理的数组
     * @return array
     */
    private  function getArrayUniqueByKeys($arr)
    {
        $arr_out = [];
        $arr_wish = [];
        foreach($arr as $k => $v)
        {
            $key_out = $v['carname']."-".$v['carid']; //提取内部一维数组的key(name age)作为外部数组的键
            if(array_key_exists($key_out,$arr_out)){
                continue;
            } else {
                $arr_out[$key_out] = $arr[$k]; //以key_out作为外部数组的键
                $arr_wish[$k] = $arr[$k];  //实现二维数组唯一性
            }
        }
        return $arr_wish;
    }

    /**
     * 报表中心 库存报表列表
     */
    public function stockListAction(){
        $params = $this -> getParamAll();
        if($params['code'] != 200){
            COMMON::ApiJson($params['code'], $params['message']);
        }
        $search = [
            'customer_sysno' => $params['customerno_sysno'],
            'goods_sysno' => $params['goods_sysno'],
//            'pageCurrent' => $this->request->getParam('page', 1),
//            'pageSize' => $this->request->getParam('pageSize', 10),
        ];
        $stockInstance = new StockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $stockInstance ->getCountStockList($search);
        COMMON::ApiJson(200, '请求成功', $this->responseResult($list));
    }

    /**
     * 报表中心 库存详情
     */
    public function stcokDetailListAction(){
        $params = $this -> getParamAll(true);
        if($params['code'] != 200){
            COMMON::ApiJson($params['code'], $params['message']);
        }
        $startTime = $this -> request -> getParam('start_time', '');
        $endTime = $this -> request -> getParam('end_time', '');
        if($startTime && $endTime && ($startTime > $endTime)){
            COMMON::ApiJson(300, '开始时间不能大于结束时间');
        }
        $num = $this -> request -> getParam('num', '-100');
        $stockinno = $this -> request -> getParam('stockinno', '');
        $search = [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'customer_sysno' => $params['customerno_sysno'],
            'goodsname' => $params['goods_sysno'],
            'stockinno' => $stockinno,
            'stockqty' => $num,
            'isclearstock' => 0,
            'page' => false,
        ];
        $stockInstance = new StockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $stockInstance -> getList($search);
        COMMON::ApiJson(200, '请求成功', $this->responseResult($list));
    }

    /**
     * 报表中心 收发存明细
     */
    public function customDtailListAction(){
        $params = $this->getParamAll(true);
        if($params['code'] != 200){
            COMMON::ApiJson($params['code'], $params['message']);
        }
        $startTime = $this->request->getParam('start_time',date('Y-m-d'));
        $endTime = $this->request->getParam('end_time',date('Y-m-d'));
        $customInstance = new ReportcustomModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $search = array(
            'startTime' => $startTime,
            'endTime' =>  $endTime,
            'customer_sysno' => $params['customerno_sysno'],
            'goods_sysno' => $params['goods_sysno'],
            'pageCurrent' => $this->request->getParam('page', 1),
            'pageSize' => $this->request->getParam('pageSize', 10),
        );
        $list = $customInstance->getDateil($search);
        #获取期初数量
        $searchQiChu = [
            'begin_time' =>  $startTime,
            'customer_sysno' => $params['customerno_sysno'],
            'goods_sysno' => $params['goods_sysno'],
            'page' => false,
        ];
        $list['begining'] = $customInstance -> getghostNum($searchQiChu);
        $list['ending'] = $list['begining'] + $list['count'];
        COMMON::ApiJson(200, '请求成功', self::responseResult($list));
    }
    /**
     * 报表中心 对账单
     */
    public function billListAction(){
        $params = $this->getParamAll();
        if($params['code'] != 200){
            COMMON::ApiJson($params['code'], $params['message']);
        }
        $startdate = $this -> request -> getParam('start_time', date('Y-m-d',strtotime('-1 months')));
        $enddate = $this -> request -> getParam('end_time', date('Y-m-d'));
        if($startdate > $enddate){
            COMMON::ApiJson(300, '开始时间不能大于结束时间');
        }
        $params=array(
            'startdate' => $startdate,
            'enddate' => date('Y-m-d',strtotime('1 days',strtotime($enddate))),
            'customer_sysno' => $params['customerno_sysno'],
            'pageCurrent' => $this->request->getParam('page', 1),
            'pageSize' => $this->request->getParam('pageSize', 10),
        );

        $billsModel=new BillsModel(Yaf_Registry::get("db"),Yaf_Registry::get("mc"));
        $resultdata=$billsModel->getBills($params);

        echo json_encode($resultdata);
    }
}
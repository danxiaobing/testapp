<?php

/**
 * Created by PhpStorm.
 * 退货磅码单
 */
class ThcarModel
{
    /**
     * 数据库类实例
     *
     * @var object
     */
    public $dbh = null;

    /**
     * 缓存类实例
     *
     * @var object
     */
    public $mch = null;


    /**
     * ThcarsModel constructor.
     * @param $dbh
     * @param $mch
     */
    public function __construct($dbh, $mch)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    /*
     * 查询待退货车辆
     */
    public function searchThCar($params) {
        $filter = array();
        if (isset($params['carid']) && $params['carid'] != '') {
            $filter[] = " r.`carid` like '%{$params['carid']}%' ";
        }
        if ( isset($params['begin_time']) && $params['begin_time'] != '' ) {
            $filter[] = "r.created_at >= '{$params['begin_time']}'";
        }
        if( isset($params['end_time']) && $params['end_time'] != '' ){
            $filter[] = "r.created_at <= '{$params['end_time']}'";
        }
        if( isset($params['customer_sysno']) &&  $params['customer_sysno'] != ''){
            $filter[] = "rd.customer_sysno = '{$params['customer_sysno']}'";
        }

        $where =" where r.isdel=0 AND r.stockinstatus=4 AND r.carid is not null"  ;
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = " order by r.created_at desc";

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_stock_reback` r
                LEFT JOIN `".DB_PREFIX."doc_stock_reback_detail` rd ON rd.`stockreback_sysno`=r.`sysno`
                {$where} GROUP BY r.`sysno` ";
        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "SELECT r.sysno,r.poundsout_sysno,r.carid,rd.stockout_sysno,rd.stockoutno,rd.takegoodsno,rd.customername,r.goodsname,sod.qualityname,sod.goodsnature
                        FROM `".DB_PREFIX."doc_stock_reback` r
                        LEFT JOIN `".DB_PREFIX."doc_stock_reback_detail` rd ON rd.`stockreback_sysno`=r.`sysno`
                        LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sod ON sod.sysno = rd.stockoutdetail_sysno
                        {$where} GROUP BY r.`sysno` $order";
                $result['list'] = $this->dbh->select($sql);
            }else{
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);
                $sql = "SELECT r.sysno,r.poundsout_sysno,r.carid,rd.stockout_sysno,rd.stockoutno,rd.takegoodsno,rd.customername,r.goodsname,sod.qualityname,sod.goodsnature
                        FROM `".DB_PREFIX."doc_stock_reback` r
                        LEFT JOIN `".DB_PREFIX."doc_stock_reback_detail` rd ON rd.`stockreback_sysno`=r.`sysno`
                        LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sod ON sod.sysno = rd.stockoutdetail_sysno
                        {$where} GROUP BY r.`sysno` $order";
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    /*
     * 查询退货磅码单
     */
    public function getPoundscaroutList($params)
    {
        $filter = array();
        if (isset($params['carid']) && $params['carid'] != '') {
            $filter[] = "pr.`carid` like '%{$params['carid']}%' ";
        }
        if ( isset($params['begin_time']) && $params['begin_time'] != '' ) {
            $filter[] = "pr.fullcartime >= '{$params['begin_time']}'";
        }
        if( isset($params['end_time']) && $params['end_time'] != '' ){
            $filter[] = "pr.fullcartime <= '{$params['end_time']}'";
        }
        if( isset($params['status']) && $params['status'] != '' ){
            $filter[] = "pr.poundsinstatus = {$params['status']} ";
        }
        if( isset($params['poundsinno']) && $params['poundsinno'] !='' ){
            $filter[] = "pr.poundsinno like '%{$params['poundsinno']}%' ";
        }
        if( isset($params['stockrebackno']) && $params['stockrebackno'] !='' ){
            $filter[] = "pr.stockinno like '%{$params['stockrebackno']}%' ";
        }
        if( isset($params['goodsname']) && $params['goodsname'] != '' ){
            $filter[] = "pr.goodsname like '%{$params['goodsname']}%' ";
        }
        if( isset($params['customer_sysno']) && $params['customer_sysno'] != ''){
            $filter[] = "prd.customer_sysno = '{$params['customer_sysno']}' ";
        }
        $where =" where pr.isdel=0  "  ;
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = "order by pr.updated_at desc";
        $sql = "SELECT count(*) FROM `".DB_PREFIX."doc_pounds_reback` pr
                LEFT JOIN hengyang_doc_pounds_reback_detail prd ON prd.pounds_reback_sysno = pr.sysno
                $where GROUP BY pr.sysno $order";
        $count = $this->dbh->select($sql);
        $result['totalRow'] = count($count);
        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT pr.* FROM hengyang_doc_pounds_reback pr
                        LEFT JOIN hengyang_doc_pounds_reback_detail prd ON prd.pounds_reback_sysno = pr.sysno
                        $where GROUP BY pr.sysno $order";
                $result['list'] = $this->dbh->select($sql);
            }else{
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);
                $sql = "SELECT pr.* FROM hengyang_doc_pounds_reback pr
                        LEFT JOIN hengyang_doc_pounds_reback_detail prd ON prd.pounds_reback_sysno = pr.sysno
                        $where GROUP BY pr.sysno $order";
                $result['list'] = $this->dbh->select_page($sql);
            }

            if(!empty($result['list'])){
                foreach($result['list'] AS $key =>$item){
                    $sql = "SELECT DISTINCT customername FROM hengyang_doc_pounds_reback_detail WHERE pounds_reback_sysno = {$item['sysno']}";
                    $customernames = $this->dbh->select($sql);
                    foreach($customernames as $val){
                        $customernamesarr[$key][] = $val['customername'];
                    }

                    if(empty($customernamesarr[$key])){
                        $customernamesarr[$key] = array();
                    }

                    $result['list'][$key]['customername'] = implode(',', $customernamesarr[$key]);
                }
            }
        }

        return $result;

    }

    /*
     * 根据退货磅码id查询退货磅码主表信息
     */
    public function getpoundsrebackbyid($id){
        $sql = "SELECT * FROM ".DB_PREFIX."doc_pounds_reback WHERE sysno = $id";
        return $this->dbh->select_row($sql);
    }

    /*
     * 根据退货磅码id查询退货磅码明细数据
     */
    public function getpoundsrebackdetailbyid($id){
        $sql = "SELECT * FROM ".DB_PREFIX."doc_pounds_reback_detail WHERE pounds_reback_sysno = $id";
        return $this->dbh->select($sql);
    }

    /*
     * 根据出库磅码id查询退货磅码主表信息
     */
    public function getpoundrebackbyoutid($id){
        $sql = "SELECT sr.sysno as stockreback_sysno,sr.stockrebackno,po.*,pod.takegoodscompany FROM hengyang_doc_stock_reback sr
                LEFT JOIN hengyang_doc_pounds_out po ON po.sysno = sr.poundsout_sysno
                LEFT JOIN hengyang_doc_pounds_out_detail pod ON pod.pounds_out_sysno = po.sysno
                WHERE po.sysno = $id GROUP BY po.sysno ";
        return $this->dbh->select_row($sql);
    }

    /*
     * 根据退货订单id查询退货订单明细数据
     */
    public function getstockbackdetailbyid($id){
        $sql = "SELECT * FROM hengyang_doc_stock_reback_detail WHERE stockreback_sysno = $id";
        return $this->dbh->select($sql);
    }

    /*
     * 新增退货磅码单
     */
    public  function addPoundsTh($params,$thDetailParams){
        $this->dbh->begin();
        try{
            $crane_sysno = $params['crane_sysno'];
            unset($params['crane_sysno']);
            $id = $this->insertPoundsTh($params);
            if (!$id) {
                $this->dbh->rollback();
                return false;
            }

            //添加明细
            foreach($thDetailParams as $key => $val){ //var_dump($val);
                unset($val['stocktype']);
                unset($val['doc_sysno']);
                unset($val['memo']);
                unset($val['auditreason']);
                unset($val['rebacknumber']);
                unset($val['bjui_local_index']);

                $val['pounds_reback_sysno'] = $id;
                $val['reback_sysno'] = $val['stockreback_sysno'];
                unset($val['stockreback_sysno']);
                $val['rebackdetail_sysno'] = $val['sysno'];
                unset($val['sysno']);
                $val['goods_sysno'] = $params['goods_sysno'];
                $val['goodsname'] = $params['goodsname'];
                $val['created_at'] = '=NOW()';
                $val['updated_at'] = '=NOW()';

                $rebackId = $this->insertPoundsThDetail($val);
                if(!$rebackId) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            //回写出库磅码单退货字段
            $poundsoutdata = array(
                'issavepoundsreback' =>1
            );
            $res = $this->dbh->update(DB_PREFIX.'doc_pounds_out', $poundsoutdata, 'sysno=' . intval($params['poundsout_sysno']));
            if(!$res) {
                $this->dbh->rollback();
                return false;
            }

            //添加车辆核对记录
            $carCheckInstance = new CarcheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $user = Yaf_Registry::get(SSN_VAR);
            $newCarCheckData = [
                'carcheckno' => COMMON::getCodeId('HD'),
                'businesstype' => 16,
                'business_sysno' => $id,
                'businessno' => $params['poundsinno'],
                'booking_sysno' => '',
                'bookingno' => '',
                'stock_sysno' => '',
                'stockno' => '',
                'carcheckstatus' => 3,
                'operationtype' => 3,
                'carname' => $params['carname'],
                'mobilephone' => $params['mobilephone'],
                'idcard' => $params['idcard'],
                'carid' => $params['carid'],
                'takegoodsnum' => $params['unloadnumber'],
                'created_user_sysno' => $user['sysno'],
                'created_employeename' => $user['realname'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $carCheckRes = $carCheckInstance -> addCarcheck($newCarCheckData);
            if(!$carCheckRes) {
                $this->dbh->rollback();
                return false;
            }
            $S = new SystemModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
            $logData= array(
                'doc_sysno'  =>  $carCheckRes,
                'doctype'  =>  34,
                'opertype'  => 0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  '新建车辆核对',
            );
            $logRes = $S->addDocLog($logData);
            #车辆核对操作日志
            if(!$logRes){
                $this->dbh->rollback();
                return false;
            }

            //判断该单是否需要排队
            if($params['isqueue']==1){
                $Q = new QueuebaseModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $Queuebase_data = $Q->is_existence(1,$crane_sysno);
                $sql = "show table status where Name ='".DB_PREFIX."doc_car_queue'";
                $row = $this->dbh->select_row($sql);
                $car_queuedata = array(
                    'orderno'           => $row['Auto_increment'],
                    'isup'              => 0,
                    'pounds_sysno'      => $id,
                    'doc_type'          => 2,
                    'carid'             => $params['carid'],
                    'carname'           => $params['carname'],
                    'mobilephone'       => $params['mobilephone'],
                    'disablestatus'     => 0,
                    'queuetype_sysno'   => $Queuebase_data['queuetype_sysno'],
                    'tp_sysno'          => $Queuebase_data['sysno'],
                    'queueno'           => $Queuebase_data['queueno'],
                    'goods_sysno'       => $params['goods_sysno'],
                    'goodsname'         => $params['goodsname'],
                    'estimateqty'       => floatval(($params['takeqty']*1000)/1000)/1000,
                    'loadometer'        => $params['loadometer'],
                    'status'            => 1,
                    'isdel'             => 0,
                    'created_at'        => '=NOW()',
                    'updated_at'        => '=NOW()',
                );
                $car_queueID = $this->dbh->insert(DB_PREFIX.'doc_car_queue',$car_queuedata);var_dump($car_queueID);die;
                $updateArr = array(
                    'qrcode_queue'      => '/webapi/getQueue/sysno/'.$car_queueID,
                );
                $this->updatePounds($updateArr, $id);
                if(!$car_queueID){
                    $this->dbh->rollback();
                    return false;

                }
            }

            #操作日志
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  35,
                'opertype'  => 1,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  '新建磅码核单',
            );
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $this->dbh->commit();
            return  true;
        }catch (Exception $e){
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 更新退货磅码单
     */
    public function updatePoundsTh($id,$data)
    {
        $this->dbh->begin();
        try {
            $res = $this->updatePounds($id,$data);
            if(!$res){
                $this->dbh->rollback();
                return false;
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  35,
                'opertype'  => 2,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  '编辑磅码核单',
            );
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $this->dbh->commit();
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['code'=>$e->getCode(),'message'=>$e->getMessage()];
        }
    }

    /*
     * 删除退货磅码单
     */
    public function delPoundsTh($id,$data)
    {
        $this->dbh->begin();
        try {
            $res = $this->updatePounds($id,$data);
            if(!$res){
                $this->dbh->rollback();
                return false;
            }

            //回写出库磅码单退货字段
            $poundsrebackdata = $this ->getpoundsrebackbyid($id);
            $poundsoutdata = array(
                'issavepoundsreback' =>0
            );
            $res = $this->dbh->update(DB_PREFIX.'doc_pounds_out', $poundsoutdata, 'sysno=' . intval($poundsrebackdata['poundsout_sysno']));
            if(!$res) {
                $this->dbh->rollback();
                return false;
            }

            //删除车辆核对单
            $carCheckInstance = new CarcheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $delCarCheck =  $carCheckInstance -> delCarcheck(['isdel' => 1], $id, 16);
            if(!$delCarCheck){
                return false;
            }
            //删除品检单
            $delQualityRes = $this -> dbh -> update(DB_PREFIX.'doc_qualitycheck', ['isdel' => 1], 'businesstype = 10 AND stock_sysno ='.intval($id));
            if(!$delQualityRes){
                throw new Exception('删除品质检查单失败', 300);
                return false;
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  35,
                'opertype'  => 6,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  '删除磅码核单',
            );
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $this->dbh->commit();
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['code'=>$e->getCode(),'message'=>$e->getMessage()];
        }
    }

    /*
     * 插入退货磅码单
     */
    public function insertPoundsTh(array $params){
        return $this->dbh->insert(DB_PREFIX.'doc_pounds_reback', $params);
    }

    /*
    * 插入退货磅码明细
     */
    public function insertPoundsThDetail($params)
    {
        return $this->dbh->insert(DB_PREFIX.'doc_pounds_reback_detail', $params);
    }

    /*
     * 更新退货磅码单
     */
    public function updatePounds($id,$data)
    {
        return $this->dbh->update(DB_PREFIX.'doc_pounds_reback', $data, 'sysno=' . intval($id));
    }

    /**
     * 作废退货磅码单进行回写
     */
    public function poundsVoid($id,$pounds,$outcardetaildata, $poundsthstatus)
    {
        $this->dbh->begin();
        try {
            $res = $this->updatePounds($id,$pounds);
            if(!$res){
                $this->dbh->rollback();
                return ['code'=>300, 'message' => '更新主表失败'];
            }
            if($poundsthstatus ==4 ){
                foreach ($outcardetaildata as $key => $value) {
//                    var_dump($value);exit;
                    $stockoutdetail = $this ->getoutdetailbyid($value['stockoutdetail_sysno']);
                    //更新库存或介绍信明细
                    if($stockoutdetail['stocktype'] == 1){
                        $sql = "UPDATE hengyang_storage_stock SET stockqty = stockqty - {$value['realnumber']} WHERE sysno = {$stockoutdetail['stock_sysno']}";
                        $this ->dbh ->exe($sql);
                    }elseif($stockoutdetail['stocktype'] == 2){
                        $sql = "UPDATE hengyang_doc_introduction_detail SET untakegoodsnum = stockqty - {$value['realnumber']} WHERE sysno = {$stockoutdetail['stock_sysno']}";
                        $this ->dbh ->exe($sql);
                    }
                    //更新出库订单明细
                    $sql = "UPDATE hengyang_doc_stock_out_detail SET takeqty = takeqty + {$value['realnumber']},beqty = beqty - {$value['realnumber']} WHERE sysno = {$stockoutdetail['stock_sysno']}";
                    $this ->dbh ->exe($sql);

                    //更新储罐
                    $sql = "UPDATE hengyang_base_storagetank SET tank_stockqty = tank_stockqty - {$value['realnumber']} WHERE sysno = {$value['storagetank_sysno']}";
                    $this ->dbh ->exe($sql);


                }
                $res = $this->dbh->update(DB_PREFIX.'doc_goods_record_log',array('isdel'=>1),'doc_sysno= '.$id.' AND doc_type = 26');
                if(!$res){
                    return ['code'=>300, 'message' => '删出货物进出记录表失败'];
                }
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  35,
                'opertype'  => 5,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  '作废磅码核单',
            );
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300, 'message' => '插入日志失败'];
            }
            
            $this->dbh->commit();
            return ['code' => 200, 'message' => '成功'];
        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['code' => 300, 'message' => '操作异常'];
        }
    }

    public function getoutdetailbyid($id){
        $sql = "SELECT * FROM hengyang_doc_stock_out_detail WHERE sysno = $id";
        return $this ->dbh ->select_row($sql);
    }

}
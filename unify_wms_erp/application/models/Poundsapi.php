<?php

/**
 * Created by PhpStorm.
 * User: HR
 * Date: 2017/02/14 0015
 * Time: 10:38
 */
class PoundsapiModel
{
    /**
     * 数据库类实例
     *
     * @var object
     */
    public $dbh = null;

    public $mch = null;

    /**
     * PoundsinModel constructor.
     * @param $dbh
     * @param null $mch
     */
    public function __construct($dbh, $mch = null)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    /**
     * 查询入库榜码单列表
     * @param $params
     * @return array
     */
    public function getListInApi($params)
    {
        $filter = array();
        if (isset($params['carid']) && $params['carid'] != '') {
            $filter[] = " p.`carid` like '%{$params['carid']}%' ";
        }
        if ( isset($params['begin_time']) && $params['begin_time'] != '' ) {
            $filter[] = "p.created_at >= '{$params['begin_time']}'";
        }
        if ( isset($params['fullcartime']) && $params['fullcartime'] != '' ) {
            $filter[] = "p.fullcartime >= '{$params['fullcartime']}'";
        }

        if( isset($params['end_time']) && $params['end_time'] != '' ){
            $filter[] = "p.created_at <= '{$params['end_time']}'";
        }

        if( isset($params['customername']) && $params['customername'] != '' ){
            $filter[] = "p.customername like '%{$params['customername']}%' ";
        }
        if( isset($params['poundsinstatus']) && !empty($params['poundsinstatus'])){
            $filter[] = "p.poundsinstatus in (".implode(',', $params['poundsinstatus']).") ";
        }
        if(isset($params['stockinstatus']) && $params['stockinstatus'] != ''){
            $filter[] = "dsi.stockinstatus = ".$params['stockinstatus'];
        }
        #临时处理
        // $where ="p.isdel = 0 AND p.status = 1 AND p.carcheck = 1 AND quaulitycheck in(1,2)";
        $where =" if(p.sysno<=806,p.isdel = 0 AND p.status = 1,p.isdel = 0 AND p.status = 1 AND p.carcheck = 1 AND p.quaulitycheck in(1,2)) ";
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT p.*,dsi.sysno dsi_sysno,dsic.expresscompanyname takegoodscompany FROM `".DB_PREFIX."doc_pounds_in` p
                LEFT JOIN `".DB_PREFIX."doc_stock_in` dsi ON p.stockin_sysno = dsi.sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_in_cars` dsic ON  dsic.stockin_sysno = p.stockin_sysno and dsic.carid = p.carid
                WHERE {$where} ORDER BY p.sysno ASC";
        $result = $this->dbh->select($sql);

        $printInstance = new PrinttitleModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $poundInInstance = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $printArr = $printInstance -> getDefault();
        foreach($result as &$value){
            $value['print_title_name'] = $printArr['titlename'] ? $printArr['titlename'] : '';
            $value['ship_name'] = '槽车进货';
            $value['area_name'] = $poundInInstance -> getStankAreaNameByStankId($value['storagetank_sysno']);
            $value['qrcode_url'] = 'http://'.$_SERVER['HTTP_HOST'].COMMON::createPic('http://'.$_SERVER['HTTP_HOST'].'/webapi/carinDetail/sysno/'.$value['dsi_sysno'], false, $value['poundsinno'], 70, 70);
        }
        return $result;
    }

    /**
     * 查询出库订单列表
     * @param $params
     * @return mixed
     */
    public function getListOutApi($params)
    {
        $filter = array();
        if (isset($params['carid']) && $params['carid'] != '') {
            $filter[] = " p.`carid` like '%{$params['carid']}%' ";
        }
        if ( isset($params['begin_time']) && $params['begin_time'] != '' ) {
            $filter[] = "p.created_at >= '{$params['begin_time']}'";
        }
        if ( isset($params['emptycartime']) && $params['emptycartime'] != '' ) {
            $filter[] = "p.emptycartime >= '{$params['emptycartime']}'";
        }

        if( isset($params['end_time']) && $params['end_time'] != '' ){
            $filter[] = "p.created_at <= '{$params['end_time']}'";
        }
        if( isset($params['customername']) && $params['customername'] != '' ){
            $filter[] = "p.customername like '%{$params['customername']}%' ";
        }
        if( isset($params['poundsoutstatus']) && !empty($params['poundsoutstatus']) ){
            $filter[] = "p.poundsoutstatus in (".implode(',', $params['poundsoutstatus']).") ";
        }
        if( isset($params['stockoutstatus']) && $params['stockoutstatus'] != '' ){
            $filter[] = "dso.stockoutstatus = '{$params['stockoutstatus']}'";
        }
        #临时处理
        //$where ="p.isdel = 0 AND p.status = 1 AND p.carcheck = 1";
        $where =" if(p.sysno<=2234,p.isdel = 0 AND p.status = 1,p.isdel = 0 AND p.status = 1 AND p.carcheck = 1) ";
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT p.* FROM `".DB_PREFIX."doc_pounds_out` p LEFT JOIN ".DB_PREFIX."doc_pounds_out_detail hdpod ON hdpod.pounds_out_sysno = p.sysno LEFT JOIN `".DB_PREFIX."doc_stock_out` dso ON hdpod.stockout_sysno = dso.sysno 
                WHERE {$where} GROUP BY p.sysno ORDER BY p.sysno DESC";
//        $sql = "SELECT p.* FROM `".DB_PREFIX."doc_pounds_out` p
//                WHERE {$where}";
        $result = $this->dbh->select($sql);
        $poundInInstance = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $printInstance = new PrinttitleModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $printArr = $printInstance -> getDefault();
        foreach($result as $key => $value){
            //hdpod.sysno,hdpod.customer_sysno,hdpod.customername,hdpod.realnumber,dso.takegoodsqty,dso.takegoodscompany,dsod.goods_sysno,dsod.stockin_sysno
            $pound_detail_sql = "SELECT hdpod.*,dso.takegoodsqty,dso.takegoodscompany,dsod.goods_sysno,dsod.stockin_sysno,dsod.goodsnature,ss.shipname,dsod.stocktype
                    FROM ".DB_PREFIX."doc_pounds_out_detail hdpod
                    LEFT JOIN `".DB_PREFIX."doc_stock_out` dso ON hdpod.stockout_sysno = dso.sysno
                    LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` dsod ON hdpod.stockoutdetail_sysno = dsod.sysno
                    LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = dsod.stock_sysno 
                    WHERE hdpod.pounds_out_sysno = {$value['sysno']}";
            $result[$key]['pound_detail'] = $this -> dbh -> select($pound_detail_sql);
            $shipName = [];
            $customerArr = [];
            foreach($result[$key]['pound_detail'] as $k =>$v){
                #船名处理
                if($v['stockin_sysno'] && !empty($v['stockin_sysno'])){
                    if($v['stocktype'] == 1){
                        $shipNameSql = "SELECT CASE dsi.stockintype WHEN 2 THEN '槽车进货' WHEN 3 THEN '管输' ELSE dsid.shipname END shipname FROM ".DB_PREFIX."doc_stock_in_detail dsid LEFT JOIN ".DB_PREFIX."doc_stock_in dsi ON dsid.stockin_sysno = dsi.sysno WHERE dsid.stockin_sysno = {$v['stockin_sysno']} LIMIT 1";
                    }else{
                        $shipNameSql = "SELECT shipname FROM ".DB_PREFIX."doc_introduction_detail WHERE sysno = {$v['stockin_sysno']}";
                    }
                    $shipName[$k] = $this->dbh->select_one($shipNameSql) ? $this->dbh->select_one($shipNameSql) : '槽车进货';
                    $customerArr[] = $v['customer_sysno'];
                }
                //获取上家提货信息
                $tidan = self::getTwoFatherTidanList($v['stockout_sysno'], $v['realnumber']);
                $result[$key]['pound_detail'][$k] = array_merge($v, $tidan);
            }
            $customerStr = implode(',', array_unique($customerArr));
            $cussql = "SELECT GROUP_CONCAT(customerabbreviation separator ',') customerabbreviation FROM `".DB_PREFIX."customer` WHERE sysno IN($customerStr)";
            $res = $this-> dbh -> select_one($cussql);
            $result[$key]['customerabbreviation'] = $res ? $res : '';
            $goodsnature = isset($result[$key]['pound_detail'][0]['goodsnature']) ? $result[$key]['pound_detail'][0]['goodsnature'] : 0 ;
            $result[$key]['shipname'] = $shipName[0];
            $goods_sysno = $result[$key]['pound_detail']['goods_sysno'] ? $result[$key]['pound_detail']['goods_sysno'] : 0;
            $result[$key]['density'] = $this -> getGoodsDensity($goods_sysno);
            $result[$key]['print_title_name'] = $printArr['titlename'] ? $printArr['titlename'] : '';
            //给webapi端QRCODE的图片名字
            $stockoutno = $value['poundsoutno']."_1";
            $result[$key]['qrcode_url'] = 'http://'.$_SERVER['HTTP_HOST'].COMMON::createPic('http://'.$_SERVER['HTTP_HOST'].'/webapi/carOut/sysno/'.$value['sysno'], false, $stockoutno, 70, 70);
            $result[$key]['area_name'] = $poundInInstance -> getStankAreaNameByStankId($value['storagetank_sysno']);
            $result[$key]['goodsnature'] = self::getGoodsnature($goodsnature);
        }
        return $result;
    }

    /**
     * @param $stockout_sysno
     * @return array
     */
    public function getTwoFatherTidanList($stockout_sysno, $realnum){
        $result = [];
        if($stockout_sysno){
            $sql = "SELECT bo.sysno,so.takegoodscompany,so.takegoodsno FROM ".DB_PREFIX."doc_booking_out bo LEFT JOIN ".DB_PREFIX."doc_stock_out so ON bo.sysno = so.booking_out_sysno WHERE  so.sysno = ".intval($stockout_sysno);
            $bookingRes = $this -> dbh -> select_row($sql);
            $result = [
                'up_tidan' => '',
                'up_company' => '',
                'tidan' => $bookingRes['takegoodsno'],
                'company' => $bookingRes['takegoodscompany'],
//                'bookingbeqty' => self::getNumFromTidan($bookingRes['sysno'])
                'bookingbeqty' => $realnum
            ];
            $sql = "SELECT * FROM ".DB_PREFIX."doc_takegoods WHERE bookingout_sysno = ".intval($bookingRes['sysno']);
            $res = $this -> dbh -> select_row($sql);
            if($res){
//                $result = [
//                    'up_tidan' => $res['takegoodsno'],
//                    'up_company' => $res['takecompany'],
//                    'company' => $bookingRes['takegoodscompany'],
//                    'tidan' => $bookingRes['takegoodsno'],
//                    'bookingbeqty' => $realnum
//                ];
                if($res['bookingoutfather_sysno'] != 0){
                    $sql = "SELECT * FROM ".DB_PREFIX."doc_takegoods WHERE bookingout_sysno = ".intval($res['bookingoutfather_sysno']);
                    $res2 = $this -> dbh -> select_row($sql);
                    if($res2['bookingoutfather_sysno'] == 0){
                        $result = [
                            'up_tidan' => $res2['takegoodsno'] ,
                            'up_company' => $res2['customer_name'],
                            'tidan' => $bookingRes['takegoodsno'],
                            'company' => $bookingRes['takegoodscompany'],
//                            'bookingbeqty' => self::getNumFromTidan($res['bookingout_sysno'])
                            'bookingbeqty' => $realnum
                        ];
                    }else{
                        $sql3 = "SELECT * FROM ".DB_PREFIX."doc_takegoods WHERE bookingout_sysno = ".intval($res['bookingoutfather_sysno']);
                        $res3 = $this -> dbh -> select_row($sql3);
                        if($res2){
                            $result = [
                                'up_tidan' => $res2['takegoodsno'] ,
                                'up_company' => $res3['takecompany'],
                                'tidan' => $bookingRes['takegoodsno'],
                                'company' => $bookingRes['takegoodscompany'],
//                            'bookingbeqty' => self::getNumFromTidan($res['bookingout_sysno'])
                                'bookingbeqty' => $realnum
                            ];
                        }
                    }

                }
            }
        }
        return $result;
    }

    /**
     * 根据入库ID数量获取提单数量
     */
    private function  getNumFromTidan($bookoutsysno){
        $sql = "SELECT SUM(bookingoutqty) as num FROM ".DB_PREFIX."doc_booking_out_detail WHERE  bookingout_sysno =".intval($bookoutsysno);
        $takegoodsqty =  $this -> dbh -> select_one($sql);
        return $takegoodsqty ? $takegoodsqty : 0;
    }

    /**
     * 查询一条入库磅码单信息
     * @param $sysno
     */
    public function getOneInApi( $sysno ){
        $sql = "SELECT p.*,si.customer_sysno,si.contract_sysno,sid.goods_sysno,sid.goods_quality_sysno,sid.goodsnature,bgp.qualityname,sid.stock_sysno,si.isreback,if(dcg.firstlossrate, dcg.firstlossrate, dcg.contractrate) lossrate,si.takegoodsnum FROM `".DB_PREFIX."doc_pounds_in` p
                LEFT JOIN `".DB_PREFIX."doc_stock_in` si ON p.stockin_sysno = si.sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid ON p.stockin_sysno = sid.stockin_sysno
                LEFT JOIN `".DB_PREFIX."base_goods_quality` bgp ON sid.goods_quality_sysno = bgp.sysno
                LEFT JOIN `".DB_PREFIX."doc_contract_goods` dcg ON dcg.contract_sysno = si.contract_sysno AND dcg.goods_sysno = sid.goods_sysno
                WHERE p.sysno = '".intval($sysno)."'";
        return $this->dbh->select_row($sql);
    }

    /**
     * 查询一条出库磅码单信息
     * @param $sysno
     */
    public function getOneOutApi( $sysno ){
        $sql = "SELECT p.*  FROM `".DB_PREFIX."doc_pounds_out` p
                WHERE p.sysno = '".intval($sysno)."'";
        $res =  $this->dbh->select_row($sql);
        $value['pound_detail'] = [];
        if($res && !empty($res)){
//            $pound_detail_sql = "SELECT hdpod.*,sod.goods_sysno,sod.goods_quality_sysno,sod.goodsnature,sod.goodsname,sod.qualityname,sod.unitname,sod.stockin_sysno,sod.stockinno,so.customer_sysno,so.sysno out_sysno,so.stockoutno,sod.sysno out_detail_sysno,sod.stock_sysno,sod.bookout_detail_sysno
//                    FROM ".DB_PREFIX."doc_pounds_out_detail hdpod
//                    LEFT JOIN `".DB_PREFIX."doc_stock_out` so ON hdpod.stockout_sysno = so.sysno
//                    LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` sod ON hdpod.stockoutdetail_sysno = sod.sysno
//                    WHERE hdpod.pounds_out_sysno = {$res['sysno']}";
            $pound_detail_sql = "SELECT hdpod.*,sod.goods_sysno,sod.goods_quality_sysno,sod.goodsnature,sod.goodsname,sod.qualityname,sod.unitname,sod.stockin_sysno,sod.stockinno,sod.sysno out_detail_sysno,sod.stock_sysno,sod.bookout_detail_sysno,sod.stocktype,sod.takeqty
                    FROM ".DB_PREFIX."doc_pounds_out_detail hdpod
                    LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` sod ON hdpod.stockoutdetail_sysno = sod.sysno
                    WHERE hdpod.pounds_out_sysno = {$res['sysno']}";
            $res['pound_detail'] = $this -> dbh -> select($pound_detail_sql);
        }
        return $res;
    }

    /**
     * 查询一条出库磅码单信息
     * @param $sysno
     */
    public function getOneCarOutApi( $sysno ){
        $sql = "SELECT p.*  FROM `".DB_PREFIX."doc_pounds_out` p
                WHERE p.sysno = '".intval($sysno)."'";
        $res =  $this->dbh->select_row($sql);
        $poundInInstance = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        if($res && !empty($res)){
            $pound_detail_sql = "SELECT hdpod.customername,hdpod.takegoodsno,hdpod.storagetank_sysno,hdpod.takegoodscompany,sum(hdpod.realnumber) detail_beqty, ss.goodsnature,ss.shipname
                                FROM ".DB_PREFIX."doc_pounds_out_detail hdpod 
                                LEFT JOIN ".DB_PREFIX."doc_stock_out_detail dsod ON hdpod.stockoutdetail_sysno = dsod.sysno 
                                LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = dsod.stock_sysno 
                                WHERE hdpod.pounds_out_sysno = ".intval($res['sysno']);
            $detail = $this->dbh->select($pound_detail_sql);
            foreach ($detail as $key => $value){
                if($res['poundsoutstatus'] != 4) {
                    $detail[$key]['detail_beqty'] = '--';
                }
                $detail[$key]['goodsnature'] = self::getGoodsnature($value['goodsnature']);
                $detail[$key]['guanqu'] = $poundInInstance -> getStankAreaNameByStankId($value['storagetank_sysno']);
            }
            $res['pounds_detail'] = $detail;
        }
        return $res;
    }

    private static  function getGoodsnature($key){
//        $array = [
//            1 => '保税',
//            2 => '外贸',
//            3 => '内贸转出口',
//            4 => '内贸内销',
//        ];
//        if(!$key){
//            return '未知类型';
//        }
        $array = [
            1 => '外贸',
            2 => '外贸',
            3 => '外贸',
            4 => '',
        ];
        if(!$key){
            return '';
        }
        return $array[$key];
    }

    /**
     *获取商品的密度  如果没有找到则返回1
     * @param $goods_sysno 商品ID
     * @return int
     */
    public function getGoodsDensity($goods_sysno){
        $sql = "SELECT density FROM ".DB_PREFIX."base_goods_attribute WHERE goods_sysno = {$goods_sysno} AND isdel = 0 AND  status = 1";
        $result = $this -> dbh -> select_one($sql);
        return $result ? $result : 1;
    }

    /**
     * @param $id
     * @param $params
     * @return mixed
     */
    public function updateInList($id, $params){
        return $this -> dbh -> update(DB_PREFIX.'doc_pounds_in', $params, 'sysno=' . intval($id));
    }

    public function updateOutList($id, $params){
        return $this -> dbh -> update(DB_PREFIX.'doc_pounds_out', $params, 'sysno=' . intval($id));
    }

    /**
     * 回写入库订单  库存ID
     * @param int $id 入库订单ID
     * @param int $stock_sysno  入库ID
     * @return bool
     */
    private function updateStorageToStockIn($id, $stock_sysno){
        return $this -> dbh -> update(DB_PREFIX.'doc_stock_in_detail', ['stock_sysno' => $stock_sysno], 'sysno=' . intval($id));
    }

    /**
     * 回写入库订单  库存ID
     * @param int $id 入库订单ID
     * @param int  $beqty 入库数量
     * @return bool
     */
    private function updateBeqtyToStockIn($id, $beqty){
        $sql = "UPDATE `".DB_PREFIX."doc_stock_in_detail` SET beqty = beqty+{$beqty}  WHERE sysno = ".intval($id);
        return $this->dbh ->exe($sql);
    }

    /**
     *
     * 重车过磅
     * @param $type 过磅类型 1入库 2出库
     * @param $sysno 磅码单号
     * @param $param 需要修改的榜码单参数
     * @param $user 操作用户信息
     * @return array|bool
     * @throws Exception
     */
    public function fullCarWeigh($type, $sysno, $param, $user){
        $this->dbh->begin();
        try{
            if($type == 1){
                $poundDetail = $this-> getOneInApi($sysno);

                if(!$poundDetail){
                    throw new Exception('请检查数据来源是否正确', 312);
                    return false;
                }
                if($poundDetail['poundsinstatus'] > 2){
                    throw new Exception('请勿重复过磅', 304);
                    return false;
                }
                if($poundDetail['poundsinstatus'] != 2){
                    throw new Exception('已经重车过磅', 304);
                    return false;
                }
                //修改入库 重车过磅数据
                $param['poundsinstatus'] = 3;
                $res = $this -> updateInList($sysno, $param);
                if(!$res){
                    throw new Exception('入库数据更新失败', 307);
                    return false;
                }
                // 操作记录
                $logRes = self::addLog($sysno, 7, $user, 2, '入库重车过磅');
                if(!$logRes){
                    #如果操作记录失败 记录文件日志
                    error_log(date("Y-m-d H:i:s") . "\t" . $sysno . "\t" . json_encode($param) . "\n", 3, './logs/bangma.log');
                    throw new Exception('日志记录失败', 307);
                    return false;
                }
                $result = ['code'=>'200', 'message'=>'入库重车过磅成功'];
            }elseif($type ==2){
                $poundDetail = $this-> getOneOutApi($sysno);
                if(!$poundDetail){
                    throw new Exception('请检查数据来源是否正确', 312);
                    return false;
                }
                if($poundDetail['poundsoutstatus'] == 2){
                    throw new Exception('出库请先空车过磅', 304);
                    return false;
                }
                if($poundDetail['poundsoutstatus'] > 3){
                    throw new Exception('请勿重复过磅', 304);
                    return false;
                }
                if( $poundDetail['poundsoutstatus'] != 3) {
                    throw new Exception('已经重车过磅', 304);
                    return false;
                }
                // 计算实际数量
                $countRes = self::countOutBeqty($poundDetail, $param['fullcarqty']);
                if($countRes['code'] == 0){
                    throw new Exception($countRes['message'], 311);
                    return false;
                }
                $param['beqty'] = $countRes['beqty'];
                if($poundDetail['cartype'] == 3){
                    $param['weightdifference'] = floatval($param['fullcarqty']) - floatval($poundDetail['emptycarqty']);
                    if($poundDetail['loadtype'] == 1) { //不定量时不用填写大小磅差
                        $param['sizedifference'] = floatval($param['fullcarqty']) - floatval($poundDetail['emptycarqty']) - floatval($param['beqty']);
                        if ($poundDetail['isbucket'] == 2) {
                            $param['sizedifference'] = floatval($param['fullcarqty']) - floatval($poundDetail['emptycarqty']) - floatval($param['beqty']) - floatval($poundDetail['totalemptybucketweight']);
                        }
                    }
                }
                $param['poundsoutstatus'] = 4;

                //循环获取所有的客户ID
                $customerIdArr = [];
                foreach($poundDetail['pound_detail'] as $value){
                    $customerIdArr[] = $value['customer_sysno'];
                }
                //重组数组 方便库存 储罐等记录的实际数量的更改
                $goodsOutArr = [];
                //获取总的出库数量
                $countOutNum = 0;
                if($param['customerList'] && !empty($param['customerList'])){
                    if( count($param['customerList']) == 1 ){
                        foreach($param['customerList'] as $item){
                            $goodsOutArr[$item['sysno']] = $param['beqty'];
                            $updateDetail = [
                                'realnumber' => $param['beqty'],
                                'updated_at'=> '=NOW()',
                            ];
                            $this -> dbh -> update(DB_PREFIX.'doc_pounds_out_detail', $updateDetail,  'sysno=' . intval($item['sysno']));
                        }
                    }else{
                        foreach($param['customerList'] as $item){
                            $goodsOutArr[$item['sysno']] = $item['realnumber'];
                            if(!in_array($item['customer_sysno'], $customerIdArr)){
                                throw new Exception('请检查-拼单客户有错', 301);
                                return false;
                            }
                            $countOutNum += $item['realnumber'];
                            $updateDetail = [
                                'realnumber' => $item['realnumber'],
                                'updated_at'=> '=NOW()',
                            ];
                            $this -> dbh -> update(DB_PREFIX.'doc_pounds_out_detail', $updateDetail,  'sysno=' . intval($item['sysno']));
                        }
                    }

                }
                if(count($param['customerList']) != 1){
                    if($param['beqty'] != $countOutNum){
                        throw new Exception('拼单总重和实际出库数量有误', 301);
                        return false;
                    }
                }

                //修改出库  重车过磅数据
                unset($param['customerList']);
                $param['updated_at'] =  '=NOW()';
                $res = $this-> updateOutList($sysno, $param);
                if(!$res){
                    throw new Exception('出库数据更新失败', 308);
                    return false;
                }
                $stockStance = new StockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $storagetankInstance = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $bookOutCarsInstance = new BookoutcarsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $introductionDetailInstance = new IntroduceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $logInstance = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                foreach($poundDetail['pound_detail'] as $val) {
                    $carOutDetail = $bookOutCarsInstance -> getCarsDetail($val['stockout_sysno'], $poundDetail['carid']);
                    if($carOutDetail['cartakeqty'] != '--'){
                        #验证车辆的预提数量
                        if(floatval($carOutDetail['cartakeqty']) < floatval($goodsOutArr[$val['sysno']])){
                            throw new Exception('提货数量大于车的预计提货数量', 308);
                            return false;
                        }
                        //回写车的预提数量
                        $bookOutCarsInstance -> updateStockOutCars($carOutDetail['cars_sysno'], floatval($goodsOutArr[$val['sysno']])/1000);
                    }
                    //判断订单预提数量
                    if(floatval($val['takeqty']) < floatval($goodsOutArr[$val['sysno']])/1000){
                        throw new Exception('提货数量大于出库订单的待提数量', 308);
                        return false;
                    }

                    if($val['stocktype'] == 1){
                        // 库存数据更改
                        $stockdata['data'] = array(
                            //'customer_sysno' => $poundDetail['customer_sysno'], //由于拆单造成提货用户不是该库存的用户 故注释掉
                            'customername' => $val['customername'],
                            'goods_sysno' => $val['goods_sysno'],
                            'goodsname' => $val['goodsname'],
                            'goods_quality_sysno' => $val['goods_quality_sysno'],
                            'goodsqualityname' => $val['qualityname'],
                            //'storagetank_sysno' => $val['storagetank_sysno'],
                            'goodsnature' => $val['goodsnature'],
                            'fromid' => $val['stockin_sysno'],
                            'firstfrom_no' => $val['stockinno'],
                            'outstockqty' => floatval( $goodsOutArr[$val['sysno']] ) / 1000,
                            'clockqty' => floatval( $goodsOutArr[$val['sysno']] ) / 1000,    //减掉对应锁货量
                            //'doctype' => 2,  //船入库的也可以车出库
                            'updated_at' => '=NOW()',
                        );
                        if (isset($val['stockin_sysno'])) {
                            $stockdata['data']['sysno'] = $val['stock_sysno'];
                        }
                        $stockdata['type'] = 2;
                        $stockdata['changetype'] = 4;
                        $res = $stockStance->pubstockoperation($stockdata);
                        if ($res['code'] != 200) {
                            throw new Exception($res['message'], 309);
                            return false;
                        }
                    }elseif($val['stocktype'] == 2){
                        #修改介绍信表信息 不修改库存信息 当为库存信息是 stock_sysno 为介绍信明细表主键
                        $outstockqty = floatval( $goodsOutArr[$val['sysno']] ) / 1000;
                        $res = $introductionDetailInstance -> updateIntroductionDetail($outstockqty, $val['stockin_sysno']);
                        if($res['code'] != 200){
                            throw new Exception($res['message'], 319);
                            return false;
                        }
                    }else{
                        throw new Exception('未知库存类型', 318);
                        return false;
                    }

                    //罐容修改
                    $storagetank = [
                        'type' => 3,
                        'data' => [
                            'orderoutqty' => floatval( $goodsOutArr[$val['sysno']] ) / 1000,
                            'sysno' => $val['storagetank_sysno'],
                        ]
                    ];
                    $storagetankRes = $storagetankInstance->pubstoragetankoperation($storagetank);
                    if ($storagetankRes['code'] != 200) {
                        throw new Exception($storagetankRes['message'], 309);
                        return false;
                    }
                    //storagetankLog记录LOG日志  新增罐容日志
                    $tankLogDate = [
                        'storagetank_sysno' => $val['storagetank_sysno'],
                        'doc_sysno' => $val['out_sysno'],
                        'docno' => $val['stockoutno'],
                        'doctype' => 3,
                        'pounds_sysno' => $poundDetail['sysno'],
                        'poundsno' => $poundDetail['poundsoutno'],
                        'pounds_type' => 2,
                        'beqty' => -floatval( $goodsOutArr[$val['sysno']] ) / 1000,
                    ];
                    $storagetankLogRes = $storagetankInstance->addStoragetankLog($tankLogDate);
                    if ($storagetankLogRes['code'] != 200) {
                        throw new Exception($storagetankLogRes['message'], 309);
                        return false;
                    }

                    //回写出库详情的实提数量 和代提数量
                    $bookOutCarsInstance->updateStockOutDetail($val['out_detail_sysno'], floatval($goodsOutArr[$val['sysno']]) / 1000);
                    //回写出库预约单的实提数量和待提数量
                    $bookOutCarsInstance->updateBookOutDetail($val['bookout_detail_sysno'], floatval($goodsOutArr[$val['sysno']]) / 1000);
                    //第一次出库回写出库日期
                    $bookOutCarsInstance->updateStockOutDate($val['stockout_sysno']);
                    //客户罐容日志表更改
                    $goodsRecordLogData = [
                        'goods_sysno'       => $val['goods_sysno'],
                        'goodsname'         => $val['goodsname'],
                        'storagetank_sysno' => $val['storagetank_sysno'],
                        'storagetankname'   => $val['storagetankname'],
                        'customer_sysno'    => $val['customer_sysno'],
                        'customername'      => $val['customername'],
                        'tobeqty'           => -floatval($goodsOutArr[$val['sysno']])/1000,
                        'beqty'             => -floatval($goodsOutArr[$val['sysno']])/1000,
                        'stockin_sysno'     => $val['stockin_sysno'],
                        'stockinno'         => $val['stockinno'],
                        'doc_sysno'         => $poundDetail['sysno'],
                        'docno'             => $poundDetail['poundsoutno'],
                        'accountstoragetank_sysno' => $val['storagetank_sysno'],
                        'accountstoragetankname'   => $val['storagetankname'],
                        'doc_type'          => 4,
                        'stock_sysno' => $val['stock_sysno'],
                        'takegoodscompany' => $val['takegoodscompany'],
                        'goodsnature' => $val['goodsnature'],
                        'takegoodsno' => $val['takegoodsno'],
                        'carid' => $poundDetail['carid'],
                        'stocktype' => 1
                    ];

                    if($val['stocktype'] == 2){
                        $sql = "SELECT i.sysno,i.father_introduction_sysno,i.sale_customer_sysno,i.sale_customername,i.buy_customer_sysno,i.buy_customername,i.customer_sysno,i.customername,i.introductiontype,introductionno,stockin_sysno,stockin_no,id.stock_sysno from `".DB_PREFIX."doc_introduction` i left join `".DB_PREFIX."doc_introduction_detail` id on i.sysno=id.introduction_sysno where id.sysno = ".intval($val['stock_sysno']);
                        $introductionInfo = $this->dbh->select_row($sql);
                        //如果是顶级则直接去数据
                        if($introductionInfo['father_introduction_sysno'] == 0 )
                        {
                            if($introductionInfo['introductiontype'] == 1){
                                $goodsRecordLogData['customer_sysno'] = $introductionInfo['sale_customer_sysno'];
                                $goodsRecordLogData['customername'] = $introductionInfo['sale_customername'];
                            }else{
                                $goodsRecordLogData['customer_sysno'] = $introductionInfo['buy_customer_sysno'];
                                $goodsRecordLogData['customername'] = $introductionInfo['buy_customername'];
                            }
                        }else{//如果不是顶级则判断是否是可撤销
                            if($introductionInfo['introductiontype'] == 1){
                                $cus = self::getIntroductCustomername($introductionInfo['sysno']);
                                $goodsRecordLogData['customer_sysno'] = $cus['customer_sysno'];
                                $goodsRecordLogData['customername'] = $cus['customername'];
                            }else{
                                $goodsRecordLogData['customer_sysno'] = $introductionInfo['buy_customer_sysno'];
                                $goodsRecordLogData['customername'] = $introductionInfo['buy_customername'];
                            }
                        }
                        $goodsRecordLogData['doc_type'] = 4;
                        $goodsRecordLogData['introduction_sysno'] = $introductionInfo['sysno'];
                        $goodsRecordLogData['introductionno'] = $introductionInfo['introductionno'];
                        $goodsRecordLogData['stockin_sysno'] = $introductionInfo['stockin_sysno'];
                        $goodsRecordLogData['stockinno'] = $introductionInfo['stockin_no'];
                        $goodsRecordLogData['introduction_detail_sysno'] = $val['stock_sysno'];
                        $goodsRecordLogData['stock_sysno'] = $introductionInfo['stock_sysno'];
                        $goodsRecordLogData['stocktype'] = 2;
                    }
                    $goodsRecordRes = $logInstance -> addGoodsRecordLog($goodsRecordLogData);
                    if($goodsRecordRes['code'] != 200){
                        throw new Exception($goodsRecordRes['message'], 313);
                        return false;
                    }
                }

                // 操作记录
                $logRes = self::addLog($sysno, 8, $user, 3, '出库重车过磅成功');
                if(!$logRes){
                    #如果操作记录失败 记录文件日志
                    error_log(date("Y-m-d H:i:s") . "\t" . $sysno . "\t" . json_encode($param) . "\n", 3, './logs/bangma.log');
                    throw new Exception('操作记录失败', 300);
                    return false;
                }
                $result = ['code'=> '200', 'message' => '出库重车过磅成功'];
            }
            $this->dbh->commit();
            return $result;
        }catch (Exception $e){
            $this->dbh->rollback();
            return ['code' => $e->getCode(), 'message'=>$e->getMessage()];
        }
    }

    /**
     * 获取提单的库存承担方的名字
     * getIntroductCustomername
     * @author dxb
     * @param $sysno
     * @return Array
     */
    public function getIntroductCustomername($sysno){
        $sql = "SELECT i.sysno,i.father_introduction_sysno,i.sale_customer_sysno,i.sale_customername,i.buy_customer_sysno,i.buy_customername,customer_sysno,customername,introductiontype,introductionno,stockin_sysno,stockin_no,id.stock_sysno from `".DB_PREFIX."doc_introduction` i left join `".DB_PREFIX."doc_introduction_detail` id on i.sysno=id.introduction_sysno where id.introduction_sysno = ".intval($sysno);
        $introductionInfo = $this->dbh->select_row($sql);
        //如果是顶级则直接去数据
        if($introductionInfo['father_introduction_sysno'] == 0 )
        {
            if($introductionInfo['introductiontype'] == 1){
                $customerInfo['customer_sysno'] = $introductionInfo['sale_customer_sysno'];
                $customerInfo['customername'] = $introductionInfo['sale_customername'];
            }else{
                $customerInfo['customer_sysno'] = $introductionInfo['buy_customer_sysno'];
                $customerInfo['customername'] = $introductionInfo['buy_customername'];
            }
        }else{ //如果不是顶级则判断是否是可撤销
            if($introductionInfo['introductiontype'] == 1){
                return self::getIntroductCustomername($introductionInfo['father_introduction_sysno']);
            }else{
                $customerInfo['customer_sysno'] = $introductionInfo['buy_customer_sysno'];
                $customerInfo['customername'] = $introductionInfo['buy_customername'];
            }
        }
        return $customerInfo;
    }

    /**
     * 空车过磅
     * @param $type 过磅类型 1入库 2出库
     * @param $sysno 磅码单号
     * @param $param 需要修改的榜码单参数
     * @param $user 操作用户信息
     * @return bool
     * @throws Exception
     */
    public function emptyCarWeigh($type, $sysno, $param, $user){
        $this->dbh->begin();
        try{
            if($type == 1){
                //修改入库 空车过磅数据
                $poundDetail = $this-> getOneInApi($sysno);
                if(!$poundDetail){
                    throw new Exception('请检查数据来源是否正确', 312);
                    return false;
                }
                if($poundDetail['poundsoutstatus'] == 2){
                    throw new Exception('入库请先重车过磅', 304);
                    return false;
                }
                if($poundDetail['poundsinstatus'] > 3){
                    throw new Exception('请勿重复过磅', 303);
                    return false;
                }
                if($poundDetail['poundsinstatus'] != 3){
                    throw new Exception('已经空车过磅', 303);
                    return false;
                }

                #计算实际数量
                $param['beqty'] = self::countInBeqty($poundDetail['fullcarqty'], $param['emptycarqty']);
                if($param['beqty'] < 0 ){
                    throw new Exception('空车过磅重量不能大于重车过磅数量', 310);
                    return false;
                }
                if($param['beqty'] == 0 ){
                    throw new Exception('空车过磅重量不能等于重车过磅数量', 310);
                    return false;
                }
                if($param['beqty'] < 1){
                    throw new Exception('实际重量(称重结果)不可能小于1KG', 310);
                    return false;
                }
                $param['poundsinstatus'] = 4;
                $ullage = sprintf("%.3f",(floatval($param['beqty']) * $poundDetail['lossrate'] / 1000000)); //合约损耗量
                $param['ullage'] = $ullage; //回写入库损耗
                $res = $this->updateInList($sysno, $param);
                if(!$res){
                    throw new Exception('入库数据更新失败', 307);
                    return false;
                }
                # 库存储罐数据更改
                $stockindate = date("Y-m-d");
                $stockdata['data'] = array(
                    'instockdate' => $stockindate,
                    'firstdate' => date("Y-m-d", strtotime('' . $stockindate . '+30 day')), 
                    'isclearstock' => 0,
                    'shipname' => '',
                    'customer_sysno' => $poundDetail['customer_sysno'],
                    'customername' => $poundDetail['customername'],
                    'goods_sysno' => $poundDetail['goods_sysno'],
                    'goodsname' => $poundDetail['goodsname'],
                    'contract_sysno' => $poundDetail['contract_sysno'],
                    'goods_quality_sysno' => $poundDetail['goods_quality_sysno'],
                    'goodsqualityname' => $poundDetail['qualityname'],
                    'storagetank_sysno' => $poundDetail['storagetank_sysno'],
                    'storagetankname' => $poundDetail['storagetankname'],
                    'goodsnature' => $poundDetail['goodsnature'],
                    'instockqty' => floatval($param['beqty']) / 1000,
                    'fromid' => $poundDetail['stockin_sysno'],
                    'firstfrom_no' => $poundDetail['stockinno'],
                    'sysno' => $poundDetail['stock_sysno'],
                    'doctype' => 2,
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'ullage' => $ullage,
                    'doc_sysno' => $sysno,
                );
                //修改库存
                $stockdata['type'] = 1;
                $stockdata['changetype'] = 2;
                $stockStance = new StockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $stock_sysno_res = $stockStance->pubstockoperation($stockdata);
                if (!$stock_sysno_res) {
                    throw new Exception('入库修改库存信息失败', 309);
                    return false;
                }

                //回写入库详情表 所属库存ID
                $editRes = $this -> updateStorageToStockIn($poundDetail['stockindetail_sysno'], $stock_sysno_res);
                if(!$editRes){
                    throw new Exception('回写入库库存ID失败', 309);
                    return false;
                }
                //会写入库数量
                $this->updateBeqtyToStockIn($poundDetail['stockindetail_sysno'], floatval($param['beqty'])/1000);
                //罐容修改
                $storagetank = [
                    'type' => 1,
                    'data' =>[
                        'orderoutqty' =>floatval($param['beqty'])/1000,
                        'sysno' => $poundDetail['storagetank_sysno'],
                        'goods_quality_sysno' =>  $poundDetail['goods_quality_sysno'],
                        'qualityname' => $poundDetail['qualityname'],
                        'ullage' => $ullage,
                    ],
                ];
                $storagetankInstance = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $storagetankRes = $storagetankInstance -> pubstoragetankoperation($storagetank);
                if($storagetankRes['code'] != 200){
                    throw new Exception($storagetankRes['message'], 309);
                    return false;
                }
                $logInstance = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $goodsRecordLogData = [
                    'goods_sysno'       => $poundDetail['goods_sysno'],
                    'goodsname'         => $poundDetail['goodsname'],
                    'storagetank_sysno' => $poundDetail['storagetank_sysno'],
                    'storagetankname'   => $poundDetail['storagetankname'],
                    'customer_sysno'    => $poundDetail['customer_sysno'],
                    'customername'      => $poundDetail['customername'],
                    'tobeqty'           => floatval($poundDetail['unloadnumber'])/1000,
                    'beqty'             => floatval($param['beqty'])/1000,
                    'stockin_sysno'     => $poundDetail['stockin_sysno'],
                    'stockinno'         => $poundDetail['stockinno'],
                    'doc_sysno'         => $poundDetail['sysno'],
                    'docno'             => $poundDetail['poundsinno'],
                    'accountstoragetank_sysno' => $poundDetail['storagetank_sysno'],
                    'accountstoragetankname'   => $poundDetail['storagetankname'],
                    'doc_type'          => 2,
                    'stock_sysno' => $stock_sysno_res,
                    'ullage' => $ullage,
                    'takegoodscompany' => $poundDetail['deliverycompany'],
                    'goodsnature' => $poundDetail['goodsnature'],
                    'takegoodsno' => $poundDetail['takegoodsno'],
                    'carid' => $poundDetail['carid']
                ];
                $goodsRecordRes = $logInstance -> addGoodsRecordLog($goodsRecordLogData);
                if($goodsRecordRes['code'] != 200){
                    throw new Exception($goodsRecordRes['message'], 313);
                    return false;
                }
                //storagetankLog记录LOG日志  新增罐容日志
                $tankLogDate = [
                    'storagetank_sysno' => $poundDetail['storagetank_sysno'],
                    'doc_sysno' => $poundDetail['stockin_sysno'],
                    'docno' => $poundDetail['stockinno'],
                    'doctype' => 1,
                    'pounds_sysno' => $poundDetail['sysno'],
                    'poundsno' => $poundDetail['poundsinno'],
                    'pounds_type' => 1,
                    'beqty' => floatval($param['beqty'])/1000,
                ];
                $storagetankLogRes = $storagetankInstance -> addStoragetankLog( $tankLogDate );
                if($storagetankLogRes['code'] != 200){
                    throw new Exception($storagetankLogRes['message'], 300);
                    return false;
                }

                // 操作记录
                $logRes = self::addLog($sysno, 7, $user, 3, '入库空车过磅');
                if(!$logRes){
                    #如果操作记录失败 记录文件日志
                    error_log(date("Y-m-d H:i:s") . "\t" . $sysno . "\t" . json_encode($param) . "\n", 3, './logs/bangma.log');
                    throw new Exception('操作记录失败', 300);
                    return false;
                }

                $result = ['code'=> '200', 'message' => '入库空车过磅成功'];
            }elseif($type ==2){
                $poundDetail = $this-> getOneOutApi($sysno);
                if(!$poundDetail){
                    throw new Exception('请检查数据来源是否正确', 312);
                    return false;
                }
                if($poundDetail['poundsoutstatus'] > 2){
                    throw new Exception('请勿重复过磅', 303);
                    return false;
                }
                if($poundDetail['poundsoutstatus'] != 2){
                    throw new Exception('已经空车过磅', 303);
                    return false;
                }
                $param['poundsoutstatus'] = 3;
                //修改出库  空车过磅数据
                $res = $this->updateOutList($sysno, $param);
                if(!$res){
                    throw new Exception('出库数据更新失败', 308);
                    return false;
                }
                // 操作记录
                $logRes = self::addLog($sysno, 8, $user, 2, '出库空车过磅');
                if(!$logRes){
                    #如果操作记录失败 记录文件日志
                    error_log(date("Y-m-d H:i:s") . "\t" . $sysno . "\t" . json_encode($param) . "\n", 3, './logs/bangma.log');
                    throw new Exception('操作记录失败', 300);
                    return false;
                }
                $result = ['code'=> '200', 'message' => '出库空车过磅成功'];
            }
            $this->dbh->commit();
            return $result;
        }catch (Exception $e){
            $this->dbh->rollback();
            return ['code' => $e->getCode(), 'message'=>$e->getMessage()];
        }
    }


    /**
     * 计算入库榜码实际数量
     * @param $fullNum 数据重车数据
     * @param $emptyNum 空车数据
     * @return int
     */
    private static function countInBeqty($fullNum, $emptyNum){
        $beqty = floatval($fullNum) - floatval($emptyNum);
        return floor($beqty);
    }

    /**
     * 计算出库榜码实际数量
     * @param $detail arry
     * @param $fullNum
     * @return int
     */
    private static function countOutBeqty($detail, $fullNum){
        //基本算法
        $beqty = floatval($fullNum) - floatval($detail['emptycarqty']);
        //桶车计算方法
        if($detail['cartype'] == 3 && $beqty > 0){
            if($detail['loadtype'] == 1 ){
                //定量  实际数量 =  定量总量
                $beqty = floatval($detail['totalunchanged']);
//                //不带桶 减去空桶总重（六阳说 实际重量永远等于定量总重）
//                if($detail['isbucket'] == 2){
//                    $beqty = floatval($beqty) - floatval($detail['totalemptybucketweight']);
//                    if($beqty <=0 ) {
//                        $res = [
//                            'code' => 0 ,
//                            'message' => '定量总重不能小于或等于空桶总重'
//                        ];
//                        return $res;
//                    }
//                }
            }else{
                //不定量 不带桶 减去空桶总重
                if($detail['isbucket'] == 2){
                    $beqty = floatval($beqty) - floatval($detail['totalemptybucketweight']);
                    if($beqty <=0 ) {
                        $res = [
                            'code' => 0,
                            'message' => '过磅数据不能小于空桶总重',
                        ];
                        return $res;
                    }
                }
            }
        }
        if($beqty < 0 ) {
            $res = [
                'code' => 0,
                'message' => '空车过磅不能大于重车过磅',
            ];
            return $res;
        }
        if($beqty == 0 ) {
            $res = [
                'code' => 0,
                'message' => '空车过磅不能等于重车过磅',
            ];
            return $res;
        }
        if($beqty < 1) {
            $res = [
                'code' => 0,
                'message' => '实际重量(称重结果)不可能小于1KG',
            ];
            return $res;
        }
        $res = [
            'code' => 200,
            'message' => '成功',
            'beqty' => floor($beqty),
        ];
        return $res;
    }

    /**
     * 添加过磅操作记录
     * @param $id  操作数据ID
     * @param $doctype 操作类型 7 入库 8 出库
     * @param $user 操作用户
     * @param $opertype 操作步骤
     * @param $memo 操作说明
     * @return bool
     */
    private static function addLog($id, $doctype, $user, $opertype, $memo){
        $S = new SystemModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $input= array(
            'doc_sysno'  =>  $id,
            'doctype'  =>  $doctype,
            'opertype'  => $opertype,
            'operemployee_sysno' => $user['employee_sysno'],
            'operemployeename' => $user['employeename'],
            'opertime'    => '=NOW()',
            'operdesc'  =>  $memo,
        );
        $res = $S->addDocLog($input);
        if (!$res) {
            return false;
        }
        return true;
    }

    /**
     * 获取车退货磅码信息
     * getListBackApi
     * @author ${USER}
     * @return mixed
     */
    public function getListBackApi($params){
        $filter = array();

        if ( isset($params['begin_time']) && $params['begin_time'] != '' ) {
            $filter[] = "p.created_at >= '{$params['begin_time']}'";
        }
        if ( isset($params['emptycartime']) && $params['emptycartime'] != '' ) {
            $filter[] = "p.emptycartime >= '{$params['emptycartime']}'";
        }

        if( isset($params['end_time']) && $params['end_time'] != '' ){
            $filter[] = "p.created_at <= '{$params['end_time']}'";
        }
        if( isset($params['customername']) && $params['customername'] != '' ){
            $filter[] = "p.customername like '%{$params['customername']}%' ";
        }
        if( isset($params['poundsinstatus']) && !empty($params['poundsinstatus']) ){
            $filter[] = "p.poundsinstatus in (".implode(',', $params['poundsinstatus']).") ";
        }
        if( isset($params['stockinstatus']) && $params['stockinstatus'] != '' ){
            $filter[] = "dso.stockinstatus = '{$params['stockinstatus']}'";
        }
        $where ="p.isdel = 0 AND p.status = 1 AND p.carcheck = 1 AND p.quaulitycheck in(1,2)";
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT p.* FROM `".DB_PREFIX."doc_pounds_reback` p LEFT JOIN ".DB_PREFIX."doc_pounds_reback_detail hdprd ON hdprd.pounds_reback_sysno = p.sysno LEFT JOIN `".DB_PREFIX."doc_stock_reback` dsr ON hdprd.stockout_sysno = dsr.sysno 
                WHERE {$where} GROUP BY p.sysno ORDER BY p.sysno DESC";
        $result = $this->dbh->select($sql);
        $printInstance = new PrinttitleModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $poundInInstance = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $printArr = $printInstance -> getDefault();
        foreach($result as $key => $value){
            $pound_detail_sql = "SELECT hdprd.*
                    FROM ".DB_PREFIX."doc_pounds_reback_detail hdprd
                    LEFT JOIN `".DB_PREFIX."doc_stock_reback` dsr ON hdprd.stockout_sysno = dsr.sysno
                    LEFT JOIN `".DB_PREFIX."doc_stock_reback_detail` dsod ON hdprd.stockoutdetail_sysno = dsod.sysno 
                    WHERE hdprd.pounds_reback_sysno = {$value['sysno']}";
            $result[$key]['pound_detail'] = $this -> dbh -> select($pound_detail_sql);
            $result[$key]['area_name'] = $poundInInstance -> getStankAreaNameByStankId($value['storagetank_sysno']);
            $result[$key]['print_title_name'] = $printArr['titlename'] ? $printArr['titlename'] : '';
        }
        return $result;
    }

    /**
     * 获取退货磅码单信息
     */
    public function getOneBackApi($sysno)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."doc_pounds_reback` WHERE isdel = 0 AND status = 1 AND sysno = ".intval($sysno);
        $res = $this -> dbh -> select_row($sql);
        if(!$res ){
            return [];
        }
        $detailSql = "SELECT prd.*,sod.bookout_detail_sysno,sod.stocktype,sod.goodsnature,sod.stock_sysno,sod.stockin_sysno,sod.stockinno FROM `hengyang_doc_pounds_reback_detail` prd LEFT JOIN hengyang_doc_stock_out_detail sod ON prd.stockoutdetail_sysno = sod.sysno WHERE pounds_reback_sysno = ".intval($res['sysno']);
        $poundDetail = $this -> dbh -> select($detailSql);
        if(!$poundDetail){
            return [];
        }
        $res['pound_detail'] = $poundDetail ? $poundDetail : [];
        return $res;
    }

    /**
     * 重车退货
     */
    public function backFullCarWeigh($sysno, $param, $user)
    {

        $this -> dbh -> begin();
        try{
            $poundDetail = $this-> getOneBackApi($sysno);
            if(!$poundDetail){
                throw new Exception('请检查数据来源是否正确', 312);
                return false;
            }
            if($poundDetail['poundsinstatus'] > 2){
                throw new Exception('请勿重复过磅', 304);
                return false;
            }
            if($poundDetail['poundsinstatus'] != 2){
                throw new Exception('已经重车过磅', 304);
                return false;
            }
            //修改入库 重车过磅数据
            $param['poundsinstatus'] = 3;
            $res = $this -> updateRebackList($sysno, $param);
            if(!$res){
                throw new Exception('退货数据更新失败', 307);
                return false;
            }
            // 操作记录
            $logRes = self::addLog($sysno, 35, $user, 3, '退货重车过磅成功');
            if(!$logRes){
                #如果操作记录失败 记录文件日志
                error_log(date("Y-m-d H:i:s") . "\t" . $sysno . "\t" . json_encode($param) . "\n", 3, './logs/bangma.log');
                throw new Exception('日志记录失败', 307);
                return false;
            }
            $this -> dbh -> commit();
            $result = ['code'=>'200', 'message'=>'退货重车过磅成功'];
        }catch (Exception $e){
            $this->dbh->rollback();
            $result = ['code' => $e -> getCode(), 'message' => $e -> getMessage()];
        }
        return $result;
    }


    /**
     * 空车退货
     * backEmptyCarWeigh
     * @author ${USER}
     * @return array
     */
    public function backEmptyCarWeigh($sysno, $param, $user){
        $this -> dbh -> begin();
        try{
            $poundDetail = $this-> getOneBackApi($sysno);
            if(!$poundDetail){
                throw new Exception('请检查数据来源是否正确', 312);
                return false;
            }
            if($poundDetail['poundsinstatus'] == 2){
                throw new Exception('退货请先重车过磅', 304);
                return false;
            }
            if($poundDetail['poundsinstatus'] > 3){
                throw new Exception('请勿重复过磅', 304);
                return false;
            }
            if( $poundDetail['poundsinstatus'] != 3) {
                throw new Exception('已经空车过磅', 304);
                return false;
            }
            // 计算实际数量
            $param['beqty'] = floatval($poundDetail['fullcarqty']) - floatval($param['emptycarqty']);
            if($param['beqty'] <= 0){
                throw new Exception('实际退货数量不能小于或等于0', 304);
                return false;
            }
            $param['poundsinstatus'] = 4;

            //循环获取所有的客户ID
            $yuanDetailNum = [];
            $countOutNum = 0; //出库退货总数
            foreach($poundDetail['pound_detail'] as $value){
                $yuanDetailNum[$value['sysno']] = $value['realnumber'];
                $countOutNum += $value['realnumber'];
            }
            //重组数组 方便库存 储罐等记录的实际数量的更改
            $goodsBackArr = [];
            //获取总的出库数量
            $countBackNum = 0;
            if($param['customerList'] && !empty($param['customerList'])){
                foreach($param['customerList'] as $item){
                    $goodsBackArr[$item['sysno']] = $item['realnumber'];
                    if(!array_key_exists($item['sysno'], $yuanDetailNum)){
                        throw new Exception('请检查-退货订单明细有误', 301);
                        return false;
                    }
                    if($yuanDetailNum[$item['sysno']] < $item['realnumber']){
                        throw new Exception('退货数量超过原出库数量', 301);
                        return false;
                    }
                    $countBackNum += $item['realnumber'];
                    $updateDetail = [
                        'realnumber' => $item['realnumber'],
                        'updated_at'=> '=NOW()',
                    ];
                    $this -> dbh -> update(DB_PREFIX.'doc_pounds_reback_detail', $updateDetail,  'sysno=' . intval($item['sysno']));
                }
            }
            //修改出库  重车过磅数据
            unset($param['customerList']);
            $param['updated_at'] =  '=NOW()';
            $res = $this-> updateRebackList($sysno, $param);
            if(!$res){
                throw new Exception('退货数据更新失败', 308);
                return false;
            }
            $stockStance = new StockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $storagetankInstance = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $introductionDetailInstance = new IntroduceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $logInstance = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            foreach($poundDetail['pound_detail'] as $val) {
                #回写车预提数量
                $bookOutCarsInstance = new BookoutcarsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $carOutDetail = $bookOutCarsInstance -> getCarsDetail($val['stockout_sysno'], $val['carid']);
                if($carOutDetail['cartakeqty'] != '--'){
                    //回写车的预提数量
                    $bookOutCarsInstance -> updateStockOutCars($carOutDetail['cars_sysno'], -floatval( $goodsBackArr[$val['sysno']] ) / 1000);
                }
                //回写出库详情的实提数量 和代提数量
                $bookOutCarsInstance->updateStockOutDetail($val['stockoutdetail_sysno'], -floatval($goodsBackArr[$val['sysno']]) / 1000);
                //回写出库预约单的实提数量和待提数量
                $bookOutCarsInstance->updateBookOutDetail($val['bookout_detail_sysno'], -floatval($goodsBackArr[$val['sysno']]) / 1000);

                if($val['stocktype'] == 1){
                    // 库存数据更改
                    $stockdata['data'] = array(
                        //'customer_sysno' => $poundDetail['customer_sysno'], //由于拆单造成提货用户不是该库存的用户 故注释掉
                        'sysno' => $val['stock_sysno'],
                        'instockqty' => floatval( $goodsBackArr[$val['sysno']] ) / 1000,
                        'clockqty' => floatval( $goodsBackArr[$val['sysno']] ) / 1000,    //减掉对应锁货量
                        //'doctype' => 2,  //船入库的也可以车出库
                        'updated_at' => '=NOW()',
                    );
                    $stockdata['type'] = 16;
                    $stockdata['changetype'] = 4;
                    $res = $stockStance->pubstockoperation($stockdata);
                    if ($res['code'] != 200) {
                        throw new Exception($res['message'], 309);
                        return false;
                    }
                }elseif($val['stocktype'] == 2){
                    #修改介绍信表信息 不修改库存信息 当为库存信息是 stock_sysno 为介绍信明细表主键
                    $outstockqty = -floatval( $goodsBackArr[$val['sysno']] ) / 1000;
                    $res = $introductionDetailInstance -> updateIntroductionDetail($outstockqty, $val['stock_sysno'], true);
                    if($res['code'] != 200){
                        throw new Exception($res['message'], 319);
                        return false;
                    }
                }else{
                    throw new Exception('未知库存类型', 318);
                    return false;
                }

                //罐容修改
                $storagetank = [
                    'type' => 3,
                    'data' => [
                        'orderoutqty' => -floatval( $goodsBackArr[$val['sysno']] ) / 1000,
                        'sysno' => $poundDetail['storagetank_sysno'],
                    ]
                ];
                $storagetankRes = $storagetankInstance->pubstoragetankoperation($storagetank);
                if ($storagetankRes['code'] != 200) {
                    throw new Exception($storagetankRes['message'], 309);
                    return false;
                }
                //storagetankLog记录LOG日志  新增罐容日志
                $tankLogDate = [
                    'storagetank_sysno' => $poundDetail['storagetank_sysno'],
                    'doc_sysno' => $val['sysno'],
                    'docno' => '',
                    'doctype' => 19,
                    'pounds_sysno' => $poundDetail['sysno'],
                    'poundsno' => $poundDetail['poundsinno'],
                    'pounds_type' => 1,
                    'beqty' => floatval( $goodsBackArr[$val['sysno']] ) / 1000,
                ];
                $storagetankLogRes = $storagetankInstance->addStoragetankLog($tankLogDate);
                if ($storagetankLogRes['code'] != 200) {
                    throw new Exception($storagetankLogRes['message'], 309);
                    return false;
                }

                //客户罐容日志表更改
                $goodsRecordLogData = [
                    'goods_sysno'       => $val['goods_sysno'],
                    'goodsname'         => $val['goodsname'],
                    'storagetank_sysno' => $poundDetail['storagetank_sysno'],
                    'storagetankname'   => $poundDetail['storagetankname'],
                    'customer_sysno'    => $val['customer_sysno'],
                    'customername'      => $val['customername'],
                    'tobeqty'           => floatval($goodsBackArr[$val['sysno']])/1000,
                    'beqty'             => floatval($goodsBackArr[$val['sysno']])/1000,
                    'stockin_sysno'     => $val['stockin_sysno'],
                    'stockinno'         => $val['stockinno'],
                    'doc_sysno'         => $poundDetail['sysno'],
                    'docno'             => $poundDetail['poundsinno'],
                    'accountstoragetank_sysno' => $poundDetail['storagetank_sysno'],
                    'accountstoragetankname'   => $poundDetail['storagetankname'],
                    'doc_type'          => 26,
                    'stock_sysno' => $val['stock_sysno'],
                    'takegoodscompany' => $val['takegoodscompany'],
                    'goodsnature' => $val['goodsnature'],
                    'takegoodsno' => $val['takegoodsno'],
                    'carid' => $poundDetail['carid'],
                    'stocktype' => 1
                ];

                if($val['stocktype'] == 2){
                    $sql = "SELECT i.sysno,i.father_introduction_sysno,i.sale_customer_sysno,i.sale_customername,i.buy_customer_sysno,i.buy_customername,i.customer_sysno,i.customername,i.introductiontype,introductionno,stockin_sysno,stockin_no,id.stock_sysno from `".DB_PREFIX."doc_introduction` i left join `".DB_PREFIX."doc_introduction_detail` id on i.sysno=id.introduction_sysno where id.sysno = ".intval($val['stock_sysno']);
                    $introductionInfo = $this->dbh->select_row($sql);
                    //如果是顶级则直接去数据
                    if($introductionInfo['father_introduction_sysno'] == 0 )
                    {
                        if($introductionInfo['introductiontype'] == 1){
                            $goodsRecordLogData['customer_sysno'] = $introductionInfo['sale_customer_sysno'];
                            $goodsRecordLogData['customername'] = $introductionInfo['sale_customername'];
                        }else{
                            $goodsRecordLogData['customer_sysno'] = $introductionInfo['buy_customer_sysno'];
                            $goodsRecordLogData['customername'] = $introductionInfo['buy_customername'];
                        }
                    }else{//如果不是顶级则判断是否是可撤销
                        if($introductionInfo['introductiontype'] == 1){
                            $cus = self::getIntroductCustomername($introductionInfo['sysno']);
                            $goodsRecordLogData['customer_sysno'] = $cus['customer_sysno'];
                            $goodsRecordLogData['customername'] = $cus['customername'];
                        }else{
                            $goodsRecordLogData['customer_sysno'] = $introductionInfo['buy_customer_sysno'];
                            $goodsRecordLogData['customername'] = $introductionInfo['buy_customername'];
                        }
                    }
                    $goodsRecordLogData['introduction_sysno'] = $introductionInfo['sysno'];
                    $goodsRecordLogData['introductionno'] = $introductionInfo['introductionno'];
                    $goodsRecordLogData['stockin_sysno'] = $introductionInfo['stockin_sysno'];
                    $goodsRecordLogData['stockinno'] = $introductionInfo['stockin_no'];
                    $goodsRecordLogData['introduction_detail_sysno'] = $val['stock_sysno'];
                    $goodsRecordLogData['stock_sysno'] = $introductionInfo['stock_sysno'];
                    $goodsRecordLogData['stocktype'] = 2;
                }
                $goodsRecordRes = $logInstance -> addGoodsRecordLog($goodsRecordLogData);
                if($goodsRecordRes['code'] != 200){
                    throw new Exception($goodsRecordRes['message'], 313);
                    return false;
                }
            }

            // 操作记录
            $logRes = self::addLog($sysno, 35, $user, 4, '退货空车过磅成功');
            if(!$logRes){
                #如果操作记录失败 记录文件日志
                error_log(date("Y-m-d H:i:s") . "\t" . $sysno . "\t" . json_encode($param) . "\n", 3, './logs/bangma.log');
                throw new Exception('操作记录失败', 300);
                return false;
            }
            $this -> dbh -> commit();
            $result = ['code'=> '200', 'message' => '退货空车过磅成功'];
        }catch (Exception $e){
            $this->dbh->rollback();
            $result = ['code' => $e -> getCode(), 'message' => $e -> getMessage()];
        }
        return $result;
    }

    /**
     * @param $id
     * @param $params
     * @return mixed
     */
    public function updateRebackList($id, $params){
        return $this -> dbh -> update(DB_PREFIX.'doc_pounds_reback', $params, 'sysno=' . intval($id));
    }
}
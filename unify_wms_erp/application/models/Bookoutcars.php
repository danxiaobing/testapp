<?php
/**
 * Bookshipin Model
 *
 */

class BookoutcarsModel
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
     * BookoutcarsModel constructor.
     * @param $dbh
     * @param $mch
     */
    public function __construct($dbh, $mch)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    public function searchBookOutCar($params) {
        $filter = array();

        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " b.`stockoutdate`>='{$params['begin_time']}'";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " b.`stockoutdate`<='{$params['end_time']}'";
        }
        if (isset($params['customername']) && $params['customername'] != '') {
            $filter[] = " b.`customername` LIKE '%{$params['customername']}%' ";
        }
        if (isset($params['carid']) && $params['carid'] != '') {
            $filter[] = " c.`carid` LIKE '%{$params['carid']}%' ";
        }

        $where ='b.stockouttype = 2 AND b.stockoutstatus = 3 AND b.isdel=0 AND b.status = 1 AND c.isdel = 0 AND c.carid is not null';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT count(*) FROM `".DB_PREFIX."doc_stock_out` b
                LEFT JOIN `".DB_PREFIX."doc_stock_out_cars` c on (b.`sysno`=c.`stockout_sysno`) where {$where}";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "SELECT b.*,c.sysno out_cars_sysno,c.carid,c.stockout_sysno,c.cartakeqty
                        FROM `".DB_PREFIX."doc_stock_out` b
                        LEFT JOIN `".DB_PREFIX."doc_stock_out_cars` c on (b.`sysno`=c.`stockout_sysno`)
                        WHERE  {$where}";
                if($params['orders'] != '') {
                    $sql .= " order by ".$params['orders'] ;
                }else{
                    $sql .= " order by created_at desc";
                }
                $list = $this->dbh->select($sql);
            }else{
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);
                $sql = "SELECT b.*,c.sysno out_cars_sysno,c.carid,c.stockout_sysno,c.cartakeqty FROM `".DB_PREFIX."doc_stock_out` b
                        LEFT JOIN `".DB_PREFIX."doc_stock_out_cars` c on (b.`sysno`=c.`stockout_sysno`)
                        WHERE {$where}";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                }else{
                    $sql .= " order by created_at desc";
                }
                $list = $this->dbh->select_page($sql);
            }
            foreach($list as $key => $value){
                $other_sql = "SELECT d.goodsname,d.qualityname,d.unitname,d.goodsnature,(SELECT storagetankname FROM `".DB_PREFIX."base_storagetank` bt WHERE bt.sysno = d.storagetank_sysno) as storagetankname,d.sysno detail_out_sysno,d.storagetank_sysno
                            FROM `".DB_PREFIX."doc_stock_out_detail` d
                            WHERE d.stockout_sysno = '".$value['sysno']."'";
                $otherDetial = $this->dbh->select_row($other_sql);
                $result['list'][$key]  = array_merge($value,$otherDetial?$otherDetial:[]);
            }
        }
        return $result;
    }

    /**
     * 根据车牌号 获取车辆信息
     * @param $carid
     */
    public function getCarInfoByCarid($carid){
        $sql = "SELECT sysno,carname,mobilephone,idcard,carid,weight  FROM ".DB_PREFIX."base_car WHERE carid = '".$carid."' AND isdel = 0";
        return $this->dbh->select_row($sql);
    }

    /**
     * 获取车出库核单所需信息
     * @param  [type] $id    [出库订单sysno]
     * @param  [type] $carid [车牌号]
     * @return [type]  array()
     * @author HR
     */
    public function getBookOutcarByIds($id,$carid)
    {
        $where = ' AND 1=1';
        if ($carid){
            $where .= " AND c.carid = '".$carid."'";
        }
        $sql = "SELECT o.stockoutno,o.customername,o.customer_sysno,o.stockoutstatus,o.sysno stockout_sysno,o.takegoodsno,c.carid,c.carname,c.mobilephone,c.idcard,c.cartakeqty,s.contract_sysno,od.goodsname,od.goods_sysno,o.takegoodscompany,od.storagetank_sysno,od.sysno out_detail_sysno,od.tobeqty,od.beqty
            FROM ".DB_PREFIX."doc_stock_out o
            LEFT JOIN ".DB_PREFIX."doc_stock_out_cars c ON c.stockout_sysno = o.sysno
            LEFT JOIN ".DB_PREFIX."doc_stock_out_detail od ON o.sysno=od.stockout_sysno
            LEFT JOIN ".DB_PREFIX."doc_stock_in s ON od.stockin_sysno = s.sysno
            WHERE o.sysno = {$id}".$where;
        return $this->dbh->select($sql);
    }

    public function getStockOutCarByInfo($id,$carid){
//        $sql = "SELECT a.sysno,a.takegoodsqty,b.carid,c.beqty
//            FROM ".DB_PREFIX."doc_stock_out a
//            LEFT JOIN ".DB_PREFIX."doc_stock_out_cars b ON b.stockout_sysno=a.sysno
//            LEFT JOIN ".DB_PREFIX."doc_stock_out_detail c ON  c.stockout_sysno=a.sysno
//            WHERE a.stockoutno='{$id}' AND b.carid='{$carid}'";
        $sql = "SELECT SUM(realnumber) as realnumber
                FROM ".DB_PREFIX."doc_pounds_out c
                LEFT JOIN ".DB_PREFIX."doc_pounds_out_detail a ON c.sysno = a.pounds_out_sysno 
                WHERE a.stockout_sysno =".$id." AND c.carid='".$carid."' AND c.poundsoutstatus = 4";

        return $this->dbh->select($sql);
    }

    public function getBookOutcarById($id,$carid)
    {
        $sql = "SELECT o.stockoutno,o.customername,o.customer_sysno,o.sysno stockout_sysno,o.takegoodsno,(od.tobeqty * 1000) tobeqty,(od.takeqty*1000) takeqty,od.goodsname,od.goods_sysno,o.takegoodscompany,od.storagetank_sysno,od.sysno stockoutdetail_sysno,bs.storagetankname,0 as bucketnumber,'' as realnumber,od.inshipname,if(oc.cartakeqty=0.000, '--', (oc.cartakeqty*1000)) cartakeqty,if(oc.cartakeqtyed=0.000, '--', (oc.cartakeqtyed * 1000)) cartakeqtyed
            FROM ".DB_PREFIX."doc_stock_out o
            LEFT JOIN hengyang_doc_stock_out_cars oc ON (oc.stockout_sysno = o.sysno AND oc.carid = '".$carid."' AND oc.isdel = 0 AND oc.status = 1)
            LEFT JOIN ".DB_PREFIX."doc_stock_out_detail od ON o.sysno=od.stockout_sysno
            LEFT JOIN ".DB_PREFIX."doc_stock_in s ON od.stockin_sysno = s.sysno
            LEFT JOIN ".DB_PREFIX."base_storagetank bs ON od.storagetank_sysno = bs.sysno
            WHERE o.sysno = {$id} and od.takeqty>0";
        return $this->dbh->select($sql);
    }

    /*
     * 插入出库磅码单
     */
    public function insertPoundsOut(array $params){
        return $this->dbh->insert(DB_PREFIX.'doc_pounds_out', $params);
    }

    /*
    * 插入出库磅码明细
     */
    public function insertPoundsOutDetail($params)
    {
        return $this->dbh->insert(DB_PREFIX.'doc_pounds_out_detail', $params);
    }

    /**
     * 根据商品ID查询相同的非包罐信息
     * @param $id
     * @return mixed
     */
    public function getStoragetankByGoods($id){
        $sql = "SELECT sysno,storagetankname FROM `".DB_PREFIX."base_storagetank` WHERE goods_sysno ={$id} AND storagetankbg = 0";
        return $this->dbh->select($sql);
    }

    /**
     *回写待提数量和已提数量
     * @param $id 出库ID
     * @param $num 出库数量
     */
    public function updateStockOutDetail($id, $num){
        $sql = "UPDATE `".DB_PREFIX."doc_stock_out_detail` SET takeqty = takeqty-{$num}, beqty = beqty+{$num} ,updated_at = NOW() WHERE sysno = {$id}";
        return $this->dbh ->exe($sql);
    }
    /**
     *回写出库预约单待提数量和已提数量
     * @param $bookOutDetailId 预约出库$bookOutDetailId
     * @param $num 出库数量
     */
    public function updateBookOutDetail($bookOutDetailId, $num){
        $sql = "UPDATE `".DB_PREFIX."doc_booking_out_detail` SET untakegoodsnum = untakegoodsnum-{$num}, takegoodsqty = takegoodsqty+{$num} ,updated_at = NOW() WHERE sysno = {$bookOutDetailId}";
        return $this->dbh ->exe($sql);
    }

    /**
     * 第一次出库回写出库日期
     * @param $id 出库ID
     * @return bool
     */
    public function updateStockOutDate($id){
        $sql = "SELECT count(*) FROM `".DB_PREFIX."doc_pounds_out` WHERE stockout_sysno = {$id} AND  poundsoutstatus = 4";
        $result = $this->dbh -> select_one($sql);
        if($result == 0){
            $this -> dbh -> update(DB_PREFIX.'doc_stock_out', ['stockoutdate' => '=NOW()'], 'sysno = '.intval($id));
        }
        return true;
    }

    public  function addPoundsOut($params,$outDetailParams){
        $this->dbh->begin();
        try{
            $crane_sysno = $params['crane_sysno'];
            unset($params['crane_sysno']);
            $id = $this->insertPoundsOut($params);
            if (!$id) {
                $this->dbh->rollback();
                return false;
            }
            $carCheckInstance = new CarcheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $user = Yaf_Registry::get(SSN_VAR);
            $newCarCheckData = [
                'carcheckno' => COMMON::getCodeId('HD'),
                'businesstype' => 10,
                'business_sysno' => $id,
                'businessno' => $params['poundsoutno'],
                'booking_sysno' => '',
                'bookingno' => '',
                'stock_sysno' => '',
                'stockno' => '',
                'carcheckstatus' => 3,
                'operationtype' => 2,
                'carname' => $params['carname'],
                'mobilephone' => $params['mobilephone'],
                'idcard' => $params['idcard'],
                'carid' => $params['carid'],
                'takegoodsnum' => $params['takeqty'],
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
            #库存管理业务操作日志end
            if(!$logRes){
                throw new Exception('添加车辆核对日志失败', 300);
                return false;
            }

            foreach($outDetailParams as $key => $val){
                if(isset($val['takeqty'])){
                    unset($val['takeqty']);
                }
                if(isset($val['cartakeqty'])){
                    unset($val['cartakeqty']);
                }
                if(isset($val['bjui_local_index'])){
                    unset($val['bjui_local_index']);
                }
                if(isset($val['cartakeqtyed'])){
                    unset($val['cartakeqtyed']);
                }
                if(isset($val['cars_sysno'])){
                    unset($val['cars_sysno']);
                }
                $val['pounds_out_sysno'] = $id;
                $val['storagetank_sysno'] = $val['storagetank_sysno'];
                $val['storagetankname'] = $val['storagetankname'];
                $val['created_at'] = '=NOW()';
                $val['updated_at'] = '=NOW()';
                $outId = $this->insertPoundsOutDetail($val);
                if(!$outId) {
                    $this->dbh->rollback();
                    return false;
                }
                // $num = $val['realnumber'];
                // $this->updateStockOutDetail($val['stockoutdetail_sysno'], $num);
            }


            if($params['isqueue']==1) //判断该单是否需要排队
            {
                $Q = new QueuebaseModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

                $Queuebase_data = $Q->is_existence(1,$crane_sysno);
                // var_dump($Queuebase_data);exit;
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
                $car_queueID = $this->dbh->insert(DB_PREFIX.'doc_car_queue',$car_queuedata);
                $updateArr = array(
                    'qrcode_queue'      => '/webapi/getQueue/sysno/'.$car_queueID,
                );
                $this->updatePounds($updateArr, $id);

                if(!$car_queueID){
                    $this->dbh->rollback();
                    return false;

                }
            }

            #库存管理业务操作日志
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  8,
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


    public function getPoundscaroutList($params)
    {
        $filter = array();
        if (isset($params['carid']) && $params['carid'] != '') {
            $filter[] = "po.`carid` like '%{$params['carid']}%' ";
        }
        if ( isset($params['begin_time']) && $params['begin_time'] != '' ) {
            $filter[] = "po.fullcartime >= '{$params['begin_time']}'";
        }

        if( isset($params['end_time']) && $params['end_time'] != '' ){
            $filter[] = "po.fullcartime <= '{$params['end_time']}'";
        }

//        if( isset($params['customername']) && ($params['customername'] != '全部' && $params['customername'] != '') ){
//            $filter[] = "pod.customername like '%{$params['customername']}%' ";
//        }

        if( isset($params['status']) && $params['status'] != '' ){
            $filter[] = "po.poundsoutstatus = {$params['status']} ";
        }

        if( isset($params['stockoutno']) && $params['stockoutno'] !='' ){
            $filter[] = "pod.stockoutno = '{$params['stockoutno']}' ";
        }

        if( isset($params['storagetank_sysno']) && $params['storagetank_sysno'] !='' ){
            $filter[] = "pod.storagetank_sysno = '{$params['storagetank_sysno']}' ";
        }

        if( isset($params['goods_sysno']) && $params['goods_sysno'] !='' ){
            $filter[] = "pod.goods_sysno = '{$params['goods_sysno']}' ";
        }

        if( isset($params['poundsoutno']) && $params['poundsoutno'] !='' ){
            $filter[] = "po.poundsoutno like '%{$params['poundsoutno']}%' ";
        }

        $where =" where po.isdel=0  "  ;

        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $order = "order by po.updated_at desc";

        // if (isset($params['orders']) && $params['orders'] !='' ) {
        //     $order = " order by {$params['orders']} ";
        // }


        // $sql = "SELECT count(sysno) FROM `".DB_PREFIX."doc_pounds_out` {$where}  ";


        // $result['totalRow']=$this->dbh->select_one($sql);


        // $sql = "SELECT * FROM `".DB_PREFIX."doc_pounds_out` {$where} {$order}";

//        $sql = "SELECT po.* FROM `".DB_PREFIX."doc_pounds_out` po
//                LEFT JOIN `".DB_PREFIX."doc_pounds_out_detail` pod on po.sysno = pod.pounds_out_sysno
//                {$where} group by po.sysno {$order}";
        if($params['customername'] == '全部'){
            $params['customername'] = '';
        }
        $sql = "SELECT * FROM (SELECT po.sysno,po.poundsoutno,po.storagetank_sysno,po.storagetankname,po.carname,po.takeqty,po.loadqty,
	            po.cartype,po.carid,po.cranename,po.poundsoutstatus,po.goodsname,po.carcheck,po.beqty,po.noticenumber,
	            GROUP_CONCAT(DISTINCT pod.customername) AS customername
	            FROM `hengyang_doc_pounds_out` po LEFT JOIN `hengyang_doc_pounds_out_detail` pod ON po.sysno = pod.pounds_out_sysno
                {$where} GROUP BY po.sysno {$order}) a WHERE a.customername LIKE '%{$params['customername']}%'";

        $list = $this->dbh->select($sql);
//        $sql = "SELECT pounds_out_sysno,customername FROM `".DB_PREFIX."doc_pounds_out_detail`  ";
//
//        $detail_list = $this->dbh->select($sql);

//        if(!empty($list)){
//            $customernameArr = [];
//            foreach ($list as $k => $v) {
//
//                foreach ($detail_list as $key => $value) {
//                    if($v['sysno']==$value['pounds_out_sysno'])
//                    {
//                        if(empty($list[$k]['customername']) && in_array($value['customername'], $customernameArr)){
//                            continue;
//                        }
//                        $customernameArr[] = $value['customername'];
//                        // $list[$k]['customername'] .= empty($list[$k]['customername'])? $value['customername'] : ','.$value['customername'];
//                    }
//                }
//                if(count($customernameArr)>0){
//                    $list[$k]['customername'] = implode(',',$customernameArr);
//                }else{
//                    $list[$k]['customername'] = '' ;
//                }
//                $customernameArr = [];
//            }
//        }else{
//            return $list;
//        }
        $result = [];
        if(!empty($list)){
            if($params['page']===false && empty($params['pageSize'])){
                $result['list'] = $list;
            }else{
                $result['totalRow']=count($list);
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
                $list=array_chunk($list,$params['pageSize'],false);
                $result['list']=$list[$params['pageCurrent']-1];
                // $result['list'] == null ? $result['list']=[] : $result['list']=$result['list'];
            }
        }
        return $result;
    }




    /*
        获取磅码单信息
     */
    public function getpounds($id)
    {

        $sql = "SELECT po.*,ca.carloadweight,ca.axlenum FROM `".DB_PREFIX."doc_pounds_out` po 
            LEFT JOIN ".DB_PREFIX."base_car_axle ca ON po.car_axle_sysno = ca.sysno
            WHERE po.sysno={$id}";

        return $this->dbh->select_row($sql);
    }

    /*
        更新出库磅码单
     */
    public function updatePounds($data,$id)
    {
        return $this->dbh->update(DB_PREFIX.'doc_pounds_out', $data, 'sysno=' . intval($id));
    }

    /**
     * 作废出库磅码单进行回写
     * @param  [type] $pounds                       [出库磅码单作废信息]
     * @param  [type] $id                           [出库磅码单主键]
     * @param  [type] $pounds_detail                [磅码单明细]
     * @param  [type] $status                [磅码单状态]
     * @return [bool]                               [description]
     * @author hr
     */
    public function poundsVoid($pounds=array(),$id,$pounds_detail=array(), $status = '')
    {
        $this->dbh->begin();
        try {
            if($status !=3 ){
                foreach ($pounds_detail as $key => $value) {
                    $beqty = sprintf('%.3f',$value['realnumber']/1000);
                    $storagetank_sysno = $value['storagetank_sysno'];
                    $sql = "SELECT stocktype,stock_sysno,takeqty,beqty,bookout_detail_sysno FROM ".DB_PREFIX."doc_stock_out_detail WHERE sysno = {$value['stockoutdetail_sysno']}"; //查询出库明细
                    $info = $this->dbh->select_row($sql);
                    $stock_sysno = $info['stock_sysno'];
                    $bookout_detail_sysno = $info['bookout_detail_sysno'];
                    #回写车预提数量
                    $carOutDetail = $this -> getCarsDetail($value['stockout_sysno'], $pounds['carid']);
                    if($carOutDetail['cartakeqty'] != '--'){
                        //回写车的预提数量
                        $this -> updateStockOutCars($carOutDetail['cars_sysno'], -$beqty);
                    }
                    if($info['stocktype']==1)
                    {
                        $sql = "SELECT outstockqty,stockqty,clockqty FROM `".DB_PREFIX."storage_stock` where sysno={$stock_sysno}"; //获取库存表出库量，现存量，锁定量
                        $stock = $this->dbh->select_row($sql);
                        $stock_data = array(
                            'outstockqty' => $stock['outstockqty']-$beqty,
                            'stockqty' => $stock['stockqty']+$beqty,
                            'clockqty' => $stock['clockqty']+$beqty,
                        );
                        if($stock['outstockqty']-$beqty<0){
                            return ['code' => 300, 'message' => '当前出库量不足不允许作废'];
                        }
                        $res = $this->dbh->update(DB_PREFIX.'storage_stock',$stock_data,'sysno='.intval($stock_sysno)); //回写库存
                        if(!$res){
                            return ['code' => 201, 'message' => '回写库存失败'];
                        }
                    }
                    else
                    {
                        //回写介绍信
                        $sql = "SELECT did.untakegoodsnum,did.takegoodsqty,did.bookingqty,did.introductiondetailstatus,di.introductionstatus,di.sysno as sysno FROM `".DB_PREFIX."doc_introduction_detail` did left join `".DB_PREFIX."doc_introduction` di on (did.introduction_sysno=di.sysno) where did.sysno={$stock_sysno}"; //获取介绍信表
                        $stock = $this->dbh->select_row($sql);
                        $stock_data = array(
                            'untakegoodsnum' => $stock['untakegoodsnum']+$beqty,
                            'takegoodsqty' => $stock['takegoodsqty']-$beqty,
                            'bookingqty' => $stock['bookingqty']+$beqty,
                            'updated_at' => '=NOW()',
                        );
                        if($stock['introductiondetailstatus']!=4 && $beqty>0)
                        {
                            $stock_data['introductiondetailstatus'] = 4;
                        }
                        $res = $this->dbh->update(DB_PREFIX.'doc_introduction_detail',$stock_data,'sysno='.intval($stock_sysno)); //回写介绍信明细
                        if(!$res){
                            return ['code' => 201, 'message' => '回写提单明细失败'];
                        }
                        if($stock_data['introductionstatus']!=4 && $beqty>0)
                        {
                            $res = $this->dbh->update(DB_PREFIX.'doc_introduction',['introductionstatus'=>4],'sysno='.intval($stock['sysno'])); //回写介绍信明细
                            if(!$res){
                                return ['code' => 201, 'message' => '回写提单失败'];
                            }
                        }
                    }

                    $stockout_data = array(
                        'takeqty' => $info['takeqty']+$beqty,
                        'beqty' => $info['beqty']-$beqty,
                    );
                    $res = $this->dbh->update(DB_PREFIX.'doc_stock_out_detail',$stockout_data,'sysno='.intval($value['stockoutdetail_sysno']));
                    if(!$res){
                        return ['code' => 201, 'message' => '回写出库详情失败'];
                    }
                    $sql = "SELECT untakegoodsnum,takegoodsqty FROM `".DB_PREFIX."doc_booking_out_detail` where sysno={$bookout_detail_sysno}";
                    $booking_out_data = $this->dbh->select_row($sql);
                    $booking_out = array(
                        'untakegoodsnum' => $booking_out_data['untakegoodsnum']+$beqty,
                        'takegoodsqty' => $booking_out_data['takegoodsqty']-$beqty,
                    );
                    $res = $this->dbh->update(DB_PREFIX.'doc_booking_out_detail' , $booking_out , 'sysno='.intval($bookout_detail_sysno));
                    if(!$res){
                        return ['code' => 201, 'message' => '回写出库预约单失败'];
                    }
                    $P = new PendcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
                    $tank_stockqty = $P->getStoragetankInfo($storagetank_sysno);
                    $storagetank_data = array(
                        'tank_stockqty' => $tank_stockqty['tank_stockqty']+$beqty,
                        'orderoutqty' => $tank_stockqty['orderoutqty']+$beqty,
                    );
                    $res = $this->dbh->update(DB_PREFIX.'base_storagetank',$storagetank_data,'sysno='.intval($storagetank_sysno)); //回写储罐信息
                    if(!$res){
                        return ['code' => 201, 'message' => '回写储罐信息失败'];
                    }
                }
                $res = $this->dbh->update(DB_PREFIX.'doc_goods_record_log',array('isdel'=>1),'doc_sysno= '.$id.' AND doc_type in (4,14)');
                if(!$res){
                    return ['code'=>300, 'message' => '删出货物进出记录表失败'];
                }
            }
            $res = $this->updatePounds($pounds,$id); //更新磅码单
            if(!$res){
                return ['code' => 201, 'message' => '更新磅码单失败'];
            }
            $this->dbh->commit();
            return ['code' => 200, 'message' => '成功'];
        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['code' => 300, 'message' => '操作异常'];
        }
    }

    public function getank_stockqty($storagetank_sysno)
    {
        $sql = "SELECT tank_stockqty FROM ".DB_PREFIX."base_storagetank where sysno=".intval($storagetank_sysno);

        return $this->dbh->select_one($sql);
    }

    /**
     * 获取出库磅码单明细
     * @param  [type] $pounds_out_sysno [出库磅码单主键]
     * @return [type]                   [description]
     */
    public function getPoundsout_detail($pounds_out_sysno)
    {
        $sql = "SELECT pd.*,(sd.tobeqty *1000) tobeqty ,(sd.takeqty*1000) takeqty FROM `".DB_PREFIX."doc_pounds_out_detail` pd LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` sd ON pd.stockoutdetail_sysno = sd.sysno WHERE pd.pounds_out_sysno={$pounds_out_sysno}";

        return $this->dbh->select($sql);
    }

    /**
     * 更新出库磅码单
     * @return [type] [description]
     */
    public function updatePoundsOut($params=array(),$pound_id,$pounds_detail=array())
    {
        $detailData = [];
        foreach ($pounds_detail as $key => $value) {
            unset($value['sysno']);
            $detailData[$pounds_detail[$key]['sysno']] = $value;
        }
        $this->dbh->begin();
        try {

            $res = $this->updatePounds($params,$pound_id);

            if(!$res){
                throw new Exception("更新磅码单失败", 300);
                return false;
            }

            //删除车辆核对单
            $carCheckInstance = new CarcheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $delCarCheck = $carCheckInstance -> delCarcheck(['isdel' => 1], $pound_id, 10);
            if(!$delCarCheck){
                return false;
            }
            $user = Yaf_Registry::get(SSN_VAR);
            $newCarCheckData = [
                'carcheckno' => COMMON::getCodeId('HD'),
                'businesstype' => 10,
                'business_sysno' => $pound_id,
                'businessno' => $params['poundsoutno'],
                'booking_sysno' => '',
                'bookingno' => '',
                'stock_sysno' => '',
                'stockno' => '',
                'carcheckstatus' => 3,
                'operationtype' => 2,
                'carname' => $params['carname'],
                'mobilephone' => $params['mobilephone'],
                'idcard' => $params['idcard'],
                'carid' => $params['carid'],
                'takegoodsnum' => $params['takeqty'],
                'created_user_sysno' => $user['sysno'],
                'created_employeename' => $user['realname'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $carCheckRes = $carCheckInstance -> addCarcheck($newCarCheckData);
            if(!$carCheckRes){
                return false;
            }

            foreach ($detailData as $key => $value) {
                if(isset($value['tobeqty'])){
                    unset($value['tobeqty']);
                }
                if(isset($value['bjui_local_index'])){
                    unset($value['bjui_local_index']);
                }
                if(isset($value['takeqty'])){
                    unset($value['takeqty']);
                }
                if(isset($value['cartakeqty'])){
                    unset($value['cartakeqty']);
                }
                if(isset($value['cars_sysno'])){
                    unset($value['cars_sysno']);
                }
                if(isset($value['cartakeqtyed'])){
                    unset($value['cartakeqtyed']);
                }
                $res = $this->dbh->update(DB_PREFIX.'doc_pounds_out_detail', $value, 'sysno=' . intval($key));
                if(!$res){
                    throw new Exception("更新磅码单明细失败!", 300);
                    return false;
                }
            }

            $this->dbh->commit();
            return ['code' => 200, 'message' => '更新成功'];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['code'=>$e->getCode(),'message'=>$e->getMessage()];
        }
        exit();

    }

    public function getCarsDetail($stockoutSysno, $carid){
        $sql = "SELECT sysno cars_sysno,if(cartakeqty=0.000, '--', (cartakeqty*1000)) cartakeqty,if(cartakeqtyed=0.000, '--', (cartakeqtyed * 1000)) cartakeqtyed FROM ".DB_PREFIX."doc_stock_out_cars WHERE isdel = 0 AND status = 1 AND stockout_sysno = {$stockoutSysno} AND carid = '".$carid."'";

        return $this->dbh->select_row($sql);
    }

    /**
     *回写待提数量和已提数量
     * @param $id 出库ID
     * @param $num 出库数量
     */
    public function updateStockOutCars($id, $num){
        $sql = "UPDATE ".DB_PREFIX."doc_stock_out_cars SET cartakeqty = cartakeqty-{$num}, cartakeqtyed = cartakeqtyed+{$num} ,updated_at = NOW() WHERE sysno = {$id}";
        return $this->dbh ->exe($sql);
    }
}
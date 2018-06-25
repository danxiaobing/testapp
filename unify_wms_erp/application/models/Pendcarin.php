<?php

/**
 * Created by PhpStorm.
 * 入库磅码单
 * User: HR
 * Date: 2017/02/14 0015
 * Time: 10:38
 */
class PendcarinModel
{
    /**
     * 数据库类实例
     *
     * @var object
     */
    public $dbh = null;

    public $mch = null;

    /**
     * Constructor
     *
     * @param   object $dbh
     * @return  void
     */
    public function __construct($dbh, $mch = null)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }


    public function getList($params)
    {
        $filter = array();
        if (isset($params['carid']) && $params['carid'] != '') {
            $filter[] = " sdc.`carid` like '%{$params['carid']}%' ";
        }
        if ( isset($params['begin_time']) && $params['begin_time'] != '' ) {
            $filter[] = "s.created_at >= '{$params['begin_time']}'";
        }

        if( isset($params['end_time']) && $params['end_time'] != '' ){
            $filter[] = "s.created_at <= '{$params['end_time']}'";
        }

        if( isset($params['customername']) && $params['customername'] != '全部' ){
            $filter[] = "s.customername like '%{$params['customername']}%' ";
        }

        $where =" where s.isdel=0 AND s.stockinstatus=3 AND s.`stockintype`='2'  AND carid is not null"  ;
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $order = " order by  s.updated_at desc,s.stockinno";



        $sql = "SELECT count(*)
            FROM `".DB_PREFIX."doc_stock_in` s
            LEFT JOIN `".DB_PREFIX."doc_stock_in_cars` sdc ON s.sysno=sdc.stockin_sysno
            {$where}";

        $result['totalRow'] = $this->dbh->select_one($sql);

        $sql = "SELECT s.*,sdc.carid
            FROM `".DB_PREFIX."doc_stock_in` s
            LEFT JOIN `".DB_PREFIX."doc_stock_in_cars` sdc ON s.sysno=sdc.stockin_sysno
            {$where} {$order}";


        $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

        $this->dbh->set_page_num($params['pageCurrent']);
        $this->dbh->set_page_rows($params['pageSize']);

        $data = $this->dbh->select_page($sql);

        foreach ($data as $key => $value) {
            $sql = "SELECT sd.`goodsnature`,sd.`shipname`,bg.`goodsname`,
            sd.`beqty`,sd.`shipname`,sd.`goodsreceiptdate`,sd.`sysno` detail_sysno,gq.`qualityname`
            FROM `".DB_PREFIX."doc_stock_in` s 
            LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sd ON (s.`sysno`=sd.`stockin_sysno`) 
            LEFT JOIN `".DB_PREFIX."base_goods` bg ON bg.`sysno` = sd.`goods_sysno`
            LEFT JOIN ".DB_PREFIX."base_goods_quality gq ON sd.`goods_quality_sysno` = gq.`sysno` 
            where s.sysno={$value['sysno']} {$order} ";
            // var_dump($sql);
            $detaildata = $this->dbh->select_row($sql);

            foreach ($detaildata as $k => $v) {
                $data[$key][$k] = $detaildata[$k];
            }
        }


        $result['list']=$data;


        return $result;
    }
    //获取核单信息
    public function getinfoById($id,$carid,$poundid = false)
    {
        // $sql = "SELECT s.stockinno,s.customername,s.stockinstatus,s.sysno stockin_sysno,s.takegoodsno,sc.carid,bg.goodsname,bg.sysno as goods_sysno,bc.carname,bc.mobilephone,bc.idcard,s.contract_sysno,bs.sysno as storagetank_sysno,bs.storagetankname
        //         from ".DB_PREFIX."doc_stock_in s 
        //         LEFT JOIN ".DB_PREFIX."doc_stock_in_cars sc ON sc.stockin_sysno = s.sysno
        //         LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sd on s.sysno=sd.stockin_sysno 
        //         LEFT JOIN `".DB_PREFIX."base_goods` bg on bg.`sysno` = sd.`goods_sysno`
        //         LEFT JOIN ".DB_PREFIX."base_storagetank bs ON sd.storagetank_sysno = bs.sysno
        //         LEFT JOIN ".DB_PREFIX."base_car bc ON sc.carid = bc.carid
        //         WHERE s.sysno = {$id} AND sc.carid = '{$carid}'";
        if($poundid){
            $sql = "SELECT DISTINCT s.stockinno,s.customername,s.customer_sysno,s.stockinstatus,s.sysno stockin_sysno,s.takegoodsno,sc.carid,sc.carname,sc.mobilephone,sc.idcard,s.contract_sysno,sd.goodsname,sd.goods_sysno,sd.storagetank_sysno,sd.sysno in_detail_sysno,sd.tobeqty,sd.beqty,s.isqualitycheck,s.booking_in_sysno,dpi.sysno,dpi.memo,dpi.create_username
                from ".DB_PREFIX."doc_stock_in s 
                LEFT JOIN ".DB_PREFIX."doc_stock_in_cars sc ON sc.stockin_sysno = s.sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sd on s.sysno=sd.stockin_sysno 
                LEFT JOIN ".DB_PREFIX."base_car bc ON sc.carid = bc.carid
                left join ".DB_PREFIX."doc_pounds_in dpi on dpi.stockin_sysno = s.sysno
                WHERE s.sysno = {$id} AND sc.carid = '{$carid}' AND dpi.sysno = {$poundid}";
        }else{
            $sql = "SELECT DISTINCT s.stockinno,s.customername,s.customer_sysno,s.stockinstatus,s.sysno stockin_sysno,s.takegoodsno,sc.carid,sc.carname,sc.mobilephone,sc.idcard,s.contract_sysno,sd.goodsname,sd.goods_sysno,sd.storagetank_sysno,sd.sysno in_detail_sysno,sd.tobeqty,sd.beqty,s.isqualitycheck,s.booking_in_sysno,dpi.sysno,dpi.memo,dpi.create_username
                from ".DB_PREFIX."doc_stock_in s 
                LEFT JOIN ".DB_PREFIX."doc_stock_in_cars sc ON sc.stockin_sysno = s.sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sd on s.sysno=sd.stockin_sysno 
                LEFT JOIN ".DB_PREFIX."base_car bc ON sc.carid = bc.carid
                left join ".DB_PREFIX."doc_pounds_in dpi on dpi.stockin_sysno = s.sysno
                WHERE s.sysno = {$id} AND sc.carid = '{$carid}'";
        }
        $result = $this->dbh->select($sql);

        return $result;
    }

    //获取磅码单信息
    public function getPoundInfoById($id)
    {
        $sql = "SELECT * FROM ".DB_PREFIX."doc_pounds_in WHERE sysno = {$id}";
        $result = $this->dbh->select_row($sql);
        return $result;
    }

    /**
     * @return object
     */
    public function getStankAreaNameByStankId($id)
    {
        $sql = "SELECT ba.areaname FROM ".DB_PREFIX."base_storagetank bs LEFT JOIN ".DB_PREFIX."base_area ba ON bs.area_sysno = ba.sysno WHERE bs.sysno = ".intval($id);
        return $this->dbh->select_one($sql);
    }


    public function add($data,$attachment=null)
    {
        $this->dbh->begin();
        try {
            $goods_sysno = $data['goods_sysno'];
            unset($data['goods_sysno']);
            $resid = $this->dbh->insert(DB_PREFIX.'doc_pounds_in',$data);
            if (!$resid) {
                throw new Exception("核单失败", 300);
                return false;
            }
            $carCheckInstance = new CarcheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $user = Yaf_Registry::get(SSN_VAR);
            $newCarCheckData = [
                'carcheckno' => COMMON::getCodeId('HD'),
                'businesstype' => 4,
                'business_sysno' => $resid,
                'businessno' => $data['poundsinno'],
                'booking_sysno' => '',
                'bookingno' => '',
                'stock_sysno' => $data['stockin_sysno'],
                'stockno' => $data['stockinno'],
                'carcheckstatus' => 3,
                'operationtype' => 1,
                'carname' => $data['carname'],
                'mobilephone' => $data['mobilephone'],
                'idcard' => $data['idcard'],
                'carid' => $data['carid'],
                'takegoodsnum' => $data['unloadnumber'],
                'created_user_sysno' => $user['sysno'],
                'created_employeename' => $user['realname'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $carCheckRes = $carCheckInstance -> addCarcheck($newCarCheckData);
            if(!$carCheckRes){
                throw new Exception("新增车辆核对失败", 300);
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


            if($data['isqueue']==1) //判断该单是否需要排队
            {
                $Q = new QueuebaseModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

                $Queuebase_data = $Q->is_existence(2,$data['storagetank_sysno']);

                $sql = "show table status where Name ='".DB_PREFIX."doc_car_queue'";
                $row = $this->dbh->select_row($sql);
                $car_queuedata = array(
                    'orderno'           => $row['Auto_increment'],
                    'isup'              => 0,
                    'pounds_sysno'      => $resid,
                    'doc_type'          => 1,
                    'carid'             => $data['carid'],
                    'carname'           => $data['carname'],
                    'mobilephone'       => $data['mobilephone'],
                    'disablestatus'     => 0,
                    'queuetype_sysno'   => $Queuebase_data['queuetype_sysno'],
                    'tp_sysno'          => $Queuebase_data['sysno'],
                    'queueno'           => $Queuebase_data['queueno'],
                    'goods_sysno'       => $goods_sysno,
                    'goodsname'         => $data['goodsname'],
                    'estimateqty'       => floatval(($data['unloadnumber']*1000)/1000)/1000,
                    'loadometer'        => $data['loadometer'],
                    'status'            => 1,
                    'isdel'             => 0,
                    'created_at'        => '=NOW()',
                    'updated_at'        => '=NOW()',
                );
                $car_queueID = $this->dbh->insert(DB_PREFIX.'doc_car_queue',$car_queuedata);
                if(!$car_queueID){
                    throw new Exception("排队失败", 300);
                    return false;
                }

                //获取当前靠泊装卸单的信息，判断是否要生成品质检查单据、管线分配单、泊位分配单
                $quadata['isqualitycheck'] = $data['isqualitycheck'];
                $quadata['sysno'] = $resid;
                $quadata['bookinginno'] = $data['poundsinno'];
                $quadata['stock_sysno'] = $resid;
                $quadata['stockno'] = $data['poundsinno'];
                $quadata['bookingindate'] = date('Y-m-d');
                $quadata['shipname'] = $data['carid'];

                $bookshipinInstance = new BookshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $res = $bookshipinInstance->createThreeBill($quadata,4);
                if($res['code']!=200){
                    return false;
                }

                $updateArr = array(
                    'qrcode_queue'      => '/webapi/getQueue/sysno/'.$car_queueID,
                );

                $this->dbh->update(DB_PREFIX.'doc_pounds_in', $updateArr, 'sysno=' . intval($resid));
            }

            //添加附件
            if($attachment){
                $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                if(count($attachment)>0){
                    $res = $A->addAttachModelSysno($resid,$attachment);
                }

                if(!$res){
                    throw new Exception("添加附件失败!", 300);
                    return false;
                }
            }

            #库存管理业务操作日志
            $data= array(
                'doc_sysno'  =>  $resid,
                'doctype'  =>  7,
                'opertype'  => 1,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  '新建磅码核单',
            );
            $res = $S->addDocLog($data);
            #库存管理业务操作日志end
            if(!$res){
                throw new Exception("更新操作日志失败!", 300);
            }

            $this->dbh->commit();
            return ['code'=>200,'message'=>'核单成功!'];
        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['code'=>$e->getCode(),'message'=>$e->getMessage()];
        }
    }


    public function update($data,$id)
    {
        return $this->dbh->update(DB_PREFIX.'doc_stock_in', $data, 'sysno=' . intval($id));

    }

    public function updatePounds($data,$id,$attachment='', $state)
    {
        try{
            $this->dbh->begin();

            //添加附件
            if($attachment){
                $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                if(count($attachment)>0){
                    $res = $A->addAttachModelSysno($id,$attachment);
                }

                if(!$res){
                    return false;
                }
            }
            //删除车辆核对单
            $carCheckInstance = new CarcheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $delCarCheck =  $carCheckInstance -> delCarcheck(['isdel' => 1], $id, 4);
            if(!$delCarCheck){
                return false;
            }
            //删除品检单
            $delQualityRes = $this -> dbh -> update(DB_PREFIX.'doc_qualitycheck', ['isdel' => 1], 'businesstype = 3 AND stock_sysno ='.intval($id));
            if(!$delQualityRes){
                throw new Exception('删除品质检查单失败', 300);
                return false;
            }
            if($state == 'update') {
                $user = Yaf_Registry::get(SSN_VAR);
                $newCarCheckData = [
                    'carcheckno' => COMMON::getCodeId('HD'),
                    'businesstype' => 4,
                    'business_sysno' => $id,
                    'businessno' => $data['poundsinno'],
                    'booking_sysno' => '',
                    'bookingno' => '',
                    'stock_sysno' => $data['stockin_sysno'],
                    'stockno' => $data['stockinno'],
                    'carcheckstatus' => 3,
                    'operationtype' => 1,
                    'carname' => $data['carname'],
                    'mobilephone' => $data['mobilephone'],
                    'idcard' => $data['idcard'],
                    'carid' => $data['carid'],
                    'takegoodsnum' => $data['unloadnumber'],
                    'created_user_sysno' => $user['sysno'],
                    'created_employeename' => $user['realname'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $carCheckRes = $carCheckInstance->addCarcheck($newCarCheckData);
                if (!$carCheckRes) {
                    return false;
                }
            }
            $poundInRes = $this->dbh->update(DB_PREFIX.'doc_pounds_in', $data, 'sysno=' . intval($id));
            if(!$poundInRes){
                return false;
            }
            $this->dbh->commit();
            return true;
        }catch (Exception $e){
            $this -> dbh -> rollback();
            return false;
        }

    }

    public function poundsList($params)
    {
        $filter = array();
        if (isset($params['carid']) && $params['carid'] != '') {
            $filter[] = " `carid` like '%{$params['carid']}%' ";
        }
        if ( isset($params['begin_time']) && $params['begin_time'] != '' ) {
            $filter[] = "emptycartime >= '{$params['begin_time']}'";
        }

        if( isset($params['end_time']) && $params['end_time'] != '' ){
            $filter[] = "emptycartime <= '{$params['end_time']}'";
        }

        if( isset($params['customername']) && $params['customername'] != '全部' ){
            $filter[] = "customername like '%{$params['customername']}%' ";
        }

        if( isset($params['status']) && $params['status'] != '' ){
            $filter[] = "poundsinstatus = {$params['status']} ";
        }

        if( isset($params['stockinno']) && $params['stockinno'] != '' ){
            $filter[] = "stockinno = '{$params['stockinno']}' ";
        }

        if( isset($params['poundsinno']) && $params['poundsinno'] != '' ){
            $filter[] = "poundsinno like '%{$params['poundsinno']}%' ";
        }
        if( isset($params['goodsname']) && $params['goodsname'] != '' ){
            $filter[] = "goodsname like '%{$params['goodsname']}%' ";
        }
        $where =" where isdel=0  "  ;
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = "order by updated_at desc";
        if (isset($params['orders']) && $params['orders'] !='' ) {
            $order = " order by {$params['orders']} ";
        }
        $sql = "SELECT count(sysno) FROM `".DB_PREFIX."doc_pounds_in` {$where}  ";

        $result['totalRow']=$this->dbh->select_one($sql);

        $sql = "SELECT * FROM `".DB_PREFIX."doc_pounds_in` {$where} {$order}";

        if($params['page']===false && empty($params['pageSize'])){
            $result['list'] = $this->dbh->select($sql);
        }else{
            $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
            $this->dbh->set_page_num($params['pageCurrent']);
            $this->dbh->set_page_rows($params['pageSize']);
            $result['list'] = $this->dbh->select_page($sql);
        }

        return $result;
    }

    public function poundsDetail($id)
    {
        $sql = " SELECT * FROM `".DB_PREFIX."doc_pounds_in` where sysno = ".intval($id);

        return $this->dbh->select_row($sql);
    }

    /**
     * 获取储罐的带入量，带出量，现存量
     * @param  [type] $id [储罐sysno]
     * @return array()
     */
    public function getStoragetankInfo($id)
    {
        $sql = "SELECT tank_stockqty,orderinqty,orderoutqty,actualcapacity FROM `".DB_PREFIX."base_storagetank` WHERE sysno = {$id}";

        return $this->dbh->select_row($sql);
    }

    /**
     * 作废入库磅码单进行回写
     * @param  [type] $pounds           [description]
     * @param  [type] $storagetank_data [description]
     * @param  [type] $customer_sysno   [description]
     * @param  [type] $stockinno        [description]
     * @param  [type] $ullage         损耗
     * @return [type]                   [description]
     * @author hr
     */
    public function poundsVoid($pounds=array(),$beqty,$customer_sysno,$stockin_sysno,$id,$storagetank_sysno,$stockindetail_sysno, $ullage)
    {
        $this->dbh->begin();
        try {
            $detail = self::getPoundInfoById($id);
            if($detail['poundsinstatus'] != 3){
                $tank_stockqty = $this->getStoragetankInfo($storagetank_sysno)['tank_stockqty'];
                $storagetank_data = array(
                    "tank_stockqty" => $tank_stockqty - $beqty + $ullage, //入库扣了损耗 作废再加上损耗
                );
                if($storagetank_data['tank_stockqty'] < 0){
                    throw new Exception("该储罐当前存放量不足！", 201);
                    return false;
                }
                $res = $this->dbh->update(DB_PREFIX.'base_storagetank',$storagetank_data,'sysno='.intval($storagetank_sysno));
                if(!$res){
                    throw new Exception("更新储罐信息失败", 300);
                    return false;
                }
                $inStockData = date('Y-m-d', strtotime($detail['emptycartime']));
                $sql = "SELECT sysno,instockqty,stockqty,ullage FROM `".DB_PREFIX."storage_stock` where firstfrom_sysno={$stockin_sysno} AND iscurrent=1 AND isdel=0  AND instockdate = '".$inStockData."' AND storagetank_sysno = {$detail['storagetank_sysno']}";
                $stock = $this->dbh->select_row($sql);

                $stock_data = array(
                    'instockqty' => $stock['instockqty']-$beqty,
                    'stockqty' => $stock['stockqty'] - $beqty + $ullage,
                    'ullage' => $stock['ullage'] - $ullage,
                );
                if($stock_data['instockqty'] < 0 || $stock_data['stockqty'] < 0){
                    throw new Exception("库存信息错误", 202);
                    return false;
                }

                #查询该入库单下所有的出库磅码单数量
                $countPound = $this -> getCountPound($stock['sysno']);
                if($stock['instockqty'] - $beqty - $countPound < 0){
                    throw new Exception("该入库单下有正在出库的磅码单", 300);
                    return false;
                }
                $res = $this->dbh->update(DB_PREFIX.'storage_stock',$stock_data,'sysno='.intval($stock['sysno']));
                if(!$res){
                    throw new Exception("更新库存信息失败", 300);
                    return false;
                }
                $sql = "SELECT tobeqty,beqty FROM ".DB_PREFIX."doc_stock_in_detail WHERE sysno = {$stockindetail_sysno}";
                $info = $this->dbh->select_row($sql);
                $stockin_data = array(
                    //'tobeqty' => $info['tobeqty']+$beqty,
                    'beqty' => $info['beqty']-$beqty,
                );
                $res = $this->dbh->update(DB_PREFIX.'doc_stock_in_detail',$stockin_data,'sysno='.intval($stockindetail_sysno));
                if(!$res){
                    throw new Exception("回写入库详情失败", 300);
                    return false;
                }
                $res = $this->dbh->update(DB_PREFIX.'doc_goods_record_log',array('isdel'=>1),'doc_sysno= '.$id.' AND doc_type=2');
                if(!$res){
                    throw new Exception("删出货物进出记录表失败", 300);
                    return false;
                }
                $res = $this->dbh->update(DB_PREFIX.'doc_finance_cost_detail',array('isdel'=>1),'cost_sysno='.$id.' AND stock_sysno='.intval($stock['sysno']));
                if(!$res){
                    throw new Exception("删出费用单失败", 300);
                    return false;
                }
            }
            $res = $this->dbh->update(DB_PREFIX.'doc_pounds_in',$pounds,'sysno='.intval($id)); //更新入库磅码单
            if(!$res){
                throw new Exception("更新入库磅码单失败", 300);
                return false;
            }
            //终止车辆核对单
            $delCarCheck = $this -> dbh -> update(DB_PREFIX.'doc_carcheck', ['carcheckstatus' => 7], 'businesstype = 4 AND business_sysno ='.intval($id));
            if(!$delCarCheck){
                throw new Exception('删除车辆核对单失败', 300);
                return false;
            }
            //终止品检单
            $delQualityRes = $this -> dbh -> update(DB_PREFIX.'doc_qualitycheck', ['orderstatus' => 8], 'businesstype = 3 AND stock_sysno ='.intval($id));
            if(!$delQualityRes){
                throw new Exception('删除品质检查单失败', 300);
                return false;
            }

            $this->dbh->commit();
            return ['code'=>200,'message'=>'作废成功'];
        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['code' => $e->getCode(), 'message'=>$e->getMessage()];
        }
    }

    /**
     * 获取该入库单下所有的出库磅码单数据总和
     * getCountPound
     * @param $sysno
     * @return int
     */
    public function getCountPound($sysno)
    {
        $sql = "SELECT SUM(hdpod.realnumber) countNum FROM `".DB_PREFIX."doc_pounds_out_detail` hdpod  LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` hdsod ON hdpod.stockoutdetail_sysno = hdsod.sysno WHERE hdsod.stock_sysno =".intval($sysno);
        $countNum =  $this->dbh -> select_one($sql);
        return $countNum ? $countNum : 0;
    }

    /**
     * 获取储罐列表并根据租罐方式进行过滤
     * @param  [type] $contract_sysno [合同ID]
     * @param  [type] $goods_sysno    [商品ID]
     * @return [type] array()         [可选罐号]
     * @author [HR]
     */
    public function getStoragetanks($contract_sysno,$goods_sysno)
    {
        $sql = "SELECT contracttype,sysno FROM ".DB_PREFIX."doc_contract WHERE sysno = {$contract_sysno}";
        $data =  $this->dbh->select_row($sql);
        $contracttype =$data['contracttype'];
        // var_dump($contract_sysno); exit();
        if($contracttype==3){
            $sql=" SELECT sysno storagetank_sysno,storagetankname FROM ".DB_PREFIX."base_storagetank WHERE goods_sysno={$goods_sysno} AND sysno not in 
                (SELECT dcg.storagetank_sysno FROM ".DB_PREFIX."doc_contract dc 
                 LEFT JOIN ".DB_PREFIX."doc_contract_goods dcg ON dc.sysno=dcg.contract_sysno 
                 WHERE dc.contractenddate>NOW() AND dcg.storagetank_sysno is not null AND dcg.storagetank_sysno!=0 AND dc.isdel=0 AND dcg.isdel=0 AND dcg.`status`=1 AND dc.contractstatus=5 AND dcg.goods_sysno = {$goods_sysno} GROUP BY dcg.storagetank_sysno )";
            // error_log($sql,3,'sql_print.txt');
            $storagetank_info = $this->dbh->select($sql);

            $sql = "SELECT storagetank_sysno FROM ".DB_PREFIX."doc_contract_goods WHERE contract_sysno={$data['sysno']}";

            $htbg = $this->dbh->select($sql); //获取合同所包的罐

            foreach ($htbg as $key => $value) {
                $sql = "SELECT sysno storagetank_sysno,storagetankname FROM ".DB_PREFIX."base_storagetank WHERE sysno ={$value['storagetank_sysno']}";
                $storagetank_info[]=$this->dbh->select_row($sql);
            }

            return $storagetank_info;
        }else{
            $sql = " SELECT sysno storagetank_sysno,storagetankname FROM ".DB_PREFIX."base_storagetank WHERE goods_sysno={$goods_sysno} AND sysno not in 
                (SELECT dcg.storagetank_sysno FROM ".DB_PREFIX."doc_contract dc 
                LEFT JOIN ".DB_PREFIX."doc_contract_goods dcg ON dc.sysno=dcg.contract_sysno 
                WHERE dc.contractenddate>NOW() AND dcg.storagetank_sysno is not null AND dcg.storagetank_sysno!=0 AND dc.isdel=0 AND dcg.isdel=0 AND dcg.`status`=1 AND dc.contractstatus=5 AND dcg.goods_sysno = {$goods_sysno} GROUP BY dcg.storagetank_sysno )";
            // error_log($sql,3,'sql_print.txt');

            return $this->dbh->select($sql);

        }


    }

    /**
     * 获取出库储罐列表并根据租罐方式进行过滤 由于出库不做罐号限制
     * @param  [type] $goods_sysno    [商品ID]
     * @return [type] array()         [可选罐号]
     * @author
     */
    public function getOutStoragetanks($goods_sysno)
    {
        $sql = " SELECT sysno storagetank_sysno,storagetankname FROM ".DB_PREFIX."base_storagetank WHERE status = 1 AND  isdel = 0 AND goods_sysno={$goods_sysno}";
        return $this->dbh->select($sql);
    }

    /**
     * 获取出库储罐列表并根据租罐方式进行过滤 现在出库做罐号限
     * @author
     */
    public function getOuttankMsg($stockout_sysno)
    {
        $sql = " SELECT sod.storagetank_sysno,storagetankname
                 FROM ".DB_PREFIX."doc_stock_out so
                 LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sod on so.sysno = sod.stockout_sysno and sod.isdel=0 and sod.status<2
                 LEFT JOIN ".DB_PREFIX."base_storagetank st on st.sysno = sod.storagetank_sysno and st.isdel=0 and st.status<2
                 WHERE so.status < 2 AND  so.isdel = 0 AND so.sysno in ({$stockout_sysno})";
        return $this->dbh->select($sql);
    }

    /**
     * 获取出库储罐列表并根据租罐方式进行过滤 现在入库做罐号限
     * @author
     */
    public function getIntankMsg($stockin_sysno)
    {
        $sql = " SELECT sid.storagetank_sysno,st.storagetankname
                 FROM ".DB_PREFIX."doc_stock_in si
                 LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sid on si.sysno = sid.stockin_sysno and sid.isdel=0 and sid.status<2
                 LEFT JOIN ".DB_PREFIX."base_storagetank st on st.sysno = sid.storagetank_sysno and st.isdel=0 and st.status<2
                 WHERE si.status < 2 AND  si.isdel = 0 AND si.sysno ={$stockin_sysno}";
        return $this->dbh->select($sql);
    }
    /*
     * 根据车入库单id查询是否有有效磅码单
     */
    public function getPoundinfoByScid($stockcarinid){
        $sql = "SELECT * FROM ".DB_PREFIX."doc_pounds_in WHERE poundsinstatus != 5 AND poundsinstatus != 6 AND isdel=0 AND stockin_sysno = $stockcarinid";
        return $this->dbh->select($sql);
    }

    /*
      * 根据id查询车入库基本信息
      */
    public function getStockcarinDetailById($id)
    {
        $sql = "SELECT *  FROM `".DB_PREFIX."doc_stock_in_detail`
                where  isdel=0 and status < 2 and `sysno`=" . intval($id);
        return $this->dbh->select_row($sql);
    }

    /**后台修改出库磅码单数量
    ID 出库磅码单
    fullcarqty 重车重量(kg)
    emptycarqty 空车重量(kg)
    Data = [客户1beqty，客户2beqty,...]
     */
    public function adminEditPoundsById($id,$fullcarqty,$emptycarqty,$data)
    {
        if(!$id || !$fullcarqty || !$emptycarqty || empty($data))
        {
            return false;
        }

        $beqty = sprintf('%.3f',($fullcarqty-$emptycarqty)/1000);//实际吨数

        #查询出库磅码单
        $sql = "select pd.beqty,pd.storagetank_sysno from ".DB_PREFIX."doc_pounds_out pd WHERE pd.sysno = $id";
        $poundsOut = $this->dbh->select_row($sql);
        if(empty($poundsOut))
        {
            return false;
        }

        $oldbeqty = sprintf('%.3f',$poundsOut['beqty']/1000);//修改前实际吨数

        $diffbeqty = sprintf('%.3f', ($beqty-$oldbeqty));//正数代表要扣除库存，负数代表要多加库存

        #修改出库磅码单重车空车实际数量
        $update_pounds_out = array(
            'fullcarqty' => $fullcarqty,
            'emptycarqty' => $emptycarqty,
            'beqty' => $fullcarqty-$emptycarqty,
            'updated_at' => '=NOW()',
        );
        $this->dbh->update(DB_PREFIX.'doc_pounds_out', $update_pounds_out, 'sysno=' . intval($id));

        #查询所有出库磅码单明细表
        $sql = "select pod.sysno,sod.stock_sysno,pod.stockout_sysno,pod.stockoutdetail_sysno
                from ".DB_PREFIX."doc_pounds_out_detail pod
                left join ".DB_PREFIX."doc_stock_out_detail sod on (pod.stockoutdetail_sysno=sod.sysno)
                WHERE pod.pounds_out_sysno = $id";

        $poundsDetail = $this->dbh->select($sql);
        #修改出库磅码单明细数量
        foreach ($poundsDetail as $key => $value) {
            $update_pounds_outdetail = array(
                'realnumber' => $data[$key],
            );
            $this->dbh->update(DB_PREFIX.'doc_pounds_out_detail', $update_pounds_outdetail, 'sysno=' . intval($value['sysno']));

            #修改record
            $recordbeqty = -$update_pounds_outdetail['realnumber']/1000;
            $sql = "update ".DB_PREFIX."doc_goods_record_log SET beqty = $recordbeqty,tobeqty = $recordbeqty,updated_at = '=NOW()' where doc_sysno={$id} and doc_type = 4 and stock_sysno={$value['stock_sysno']} ";
            $this->dbh->exe($sql);

            #查询出库订单明细
            $sql = "select stocktype,stock_sysno from ".DB_PREFIX."doc_stock_out_detail WHERE sysno = {$value['stockoutdetail_sysno']}";
            $stock_out_info = $this->dbh->select_row($sql);
            if($stock_out_info['stocktype']==1)
            {
                $sql = "select * from ".DB_PREFIX."storage_stock WHERE sysno = {$stock_out_info['stock_sysno']}";
                $storage_stock = $this->dbh->select_row($sql);
                $over = abs($storage_stock['stockqty']+$diffbeqty);
                #修改库存
                $update_storage_stock = array(
                    'stockqty' => $storage_stock['stockqty']+$diffbeqty>0 ? $storage_stock['stockqty']+$diffbeqty : 0, //如果扣成负数剩余为0
                    'outstockqty' => $storage_stock['outstockqty']-$diffbeqty,
                    'beyondqty' => $storage_stock['stockqty']+$diffbeqty>0 ? $storage_stock['beyondqty'] + $over : $storage_stock['beyondqty'],//如果扣成负数剩余为0，超发字段累加
                    'updated_at' => '=NOW()',
                );
                $this->dbh->update(DB_PREFIX.'storage_stock', $update_storage_stock, 'sysno=' . intval($stock_out_info['stock_sysno']));

                //修改出库订单明细
//                $update_stock_out_detail = array(
//                    'takeqty'=>0,
//                    'beqty'=>0
//                );
//                $this->dbh->update(DB_PREFIX.'doc_stock_out_detail', $update_stock_out_detail, 'sysno=' . intval($stock_out_info['sysno']));
            }
            else
            {
                $sql = "select * from ".DB_PREFIX."storage_stock WHERE sysno = {$stock_out_info['stock_sysno']}";
                $storage_stock = $this->dbh->select_row($sql);
                $over = abs($storage_stock['stockqty']+$diffbeqty);
                #修改介绍信库存
                $update_introduction_detail = array(
                    'untakegoodsnum' => $storage_stock['stockqty']+$diffbeqty>0 ? $storage_stock['stockqty']+$diffbeqty : 0, //如果扣成负数剩余为0
                    'takegoodsqty' => $storage_stock['outstockqty']-$diffbeqty,
                    'updated_at' => '=NOW()',
                );
                $this->dbh->update(DB_PREFIX.'doc_introduction_detail', $update_introduction_detail, 'sysno=' . intval($stock_out_info['stock_sysno']));

                #修改库存
                $update_storage_stock = array(
                    'introductionqty' => $storage_stock['introductionqty']+$diffbeqty>0 ? $storage_stock['introductionqty']+$diffbeqty : 0, //如果扣成负数剩余为0
                    'updated_at' => '=NOW()',
                );
                $this->dbh->update(DB_PREFIX.'storage_stock', $update_storage_stock, 'sysno=' . intval($stock_out_info['stock_sysno']));

            }
        }

        #修改储罐进出日志
        $sql = "update ".DB_PREFIX."doc_storagetank_log SET beqty=0-{$beqty},beforebeqty=beforebeqty+{$diffbeqty},updated_at = '=NOW()' where doctype = 3 and pounds_sysno = {$id} ";
        $this->dbh->exe($sql);

        #修改储罐基本资料
        $sql = "update ".DB_PREFIX."base_storagetank SET tank_stockqty=tank_stockqty+{$diffbeqty},updated_at = '=NOW()' where sysno={$poundsOut['storagetank_sysno']} ";
        $this->dbh->exe($sql);

        return ['code' => 200,'msg' => '修改成功'];
    }

}
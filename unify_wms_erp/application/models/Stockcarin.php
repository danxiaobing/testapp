<?php

/**
 * Stockcarin Model
 *
 */
class StockcarinModel
{
    /**
     * 数据库类实例
     */
    public $dbh = null;

    /**
     * 缓存类实例
     */
    public $mch = null;


    /**
     * @param   object $dbh
     * @param   object $mch
     * @return  void
     */
    public function __construct($dbh, $mch)
    {
        $this->dbh = $dbh;

        $this->mch = $mch;
    }

    /*
     * 查询车入库订单
     */
    public function searchStockcarin($params)
    {
        $filter = array();
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " s.`stockindate` >= '{$params['begin_time']}' ";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " s.`stockindate` <= '{$params['end_time']}' ";
        }
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " s.`stockinno` LIKE '%{$params['bar_no']}%' ";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " s.`customer_sysno` = '{$params['customer_sysno']}' ";
        }
        if (isset($params['bar_stockintype']) && $params['bar_stockintype'] != '') {
            $filter[] = " s.`stockintype`='{$params['bar_stockintype']}'";
        }
        if (isset($params['bar_stockinstatus']) && $params['bar_stockinstatus'] != '-100') {
            $filter[] = " s.`stockinstatus`='{$params['bar_stockinstatus']}'";
        }
        $where = 's.isdel=0';
        $order = ' order by s.created_at desc';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT COUNT(s.sysno) FROM ".DB_PREFIX."doc_stock_in s  WHERE $where";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT s.*,sum(sd.`tobeqty`) as tobeqty,sd.goodsname,sd.`goodsnature`,sd.qualityname,sd.unitname
                        FROM `".DB_PREFIX."doc_stock_in` s
                        LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sd ON sd.stockin_sysno = s.sysno
                        where $where
                        GROUP BY s.sysno  $order";
                $result['list'] = $this->dbh->select($sql);

                $sql = "SELECT doc_sysno,SUM(beqty) AS beqty FROM ".DB_PREFIX."doc_storagetank_log WHERE doctype in (1,2) GROUP BY doc_sysno ";
                $beqtydata = $this->dbh->select($sql);

                foreach($beqtydata as $item){
                    foreach($result['list'] as $key => $value){
                        if($value['sysno']==$item['doc_sysno']){
                            $result['list'][$key]['beqty'] = $item['beqty'];
                            break;
                        }
                    }
                }


            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);
                $sql = "SELECT s.*,sum(sd.`tobeqty`) as tobeqty,sd.goodsname,sd.`goodsnature`,sd.qualityname,sd.unitname
                        FROM `".DB_PREFIX."doc_stock_in` s
                        LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sd ON sd.stockin_sysno = s.sysno
                        where $where
                        GROUP BY s.sysno  $order";
                $result['list'] = $this->dbh->select_page($sql);

                $sql = "SELECT doc_sysno,SUM(beqty) AS beqty FROM ".DB_PREFIX."doc_storagetank_log WHERE doctype in (1,2) GROUP BY doc_sysno ";
                $beqtydata = $this->dbh->select($sql);

                foreach($beqtydata as $item){
                    foreach($result['list'] as $key => $value){
                        if($value['sysno']==$item['doc_sysno']){
                            $result['list'][$key]['beqty'] = $item['beqty'];
                            break;
                        }
                    }
                }

            }
        }
        return $result;
    }

    /*
     * 根据车入库单查询总入库数量
     */
    public function getpoundsinBystockcarinId($id){
        $sql = "SELECT TRUNCATE(sum(beqty)/1000,3) FROM ".DB_PREFIX."doc_pounds_in WHERE poundsinstatus=4 AND stockin_sysno = $id";
        return $this->dbh->select_one($sql);
    }

    public function getPoundsByStockInId($stockin_id){
        $sql = "SELECT sysno,cranename,carid,if(beqty,TRUNCATE(beqty/1000,3), '--') beqty,carname,mobilephone,idcard,customername,goodsname FROM ".DB_PREFIX."doc_pounds_in WHERE  stockin_sysno = {$stockin_id} AND status = 1 AND isdel = 0";
        return $this->dbh->select($sql);
    }

    /*
     * 根据id查询车入库基本信息
     */
    public function getStockcarinById($id)
    {
        $sql = "SELECT s.*,bg.goodsname
                FROM `".DB_PREFIX."doc_stock_in` s
                left join `".DB_PREFIX."doc_stock_in_detail` sd on (s.`sysno`=sd.`stockin_sysno`)
                LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = sd.goods_sysno
                where s.`stockintype`=2 and s.`sysno`=" . intval($id);
        return $this->dbh->select_row($sql);
    }

    /*
     * 新增车入库订单
     */
    public function addStockcarin($data, $stockcarindetaildata, $stockcarincarsdata,$stockinstatus)
    {
        $this->dbh->begin();
        try {
            $sql = 'select * from `'.DB_PREFIX.'doc_booking_in` where sysno=' . intval($data['booking_in_sysno']);
            $bookdata = $this->dbh->select_row($sql);
            if (!$bookdata) {
                $this->dbh->rollback();
                return false;
            }
            if ($bookdata['issaveorder']) {
                $this->dbh->rollback();
                return false;
            }

            #车入库预约操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $id = $data['booking_in_sysno'];
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 1,
                'opertype' => 5,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => '生成入库单',
            );
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            #END
            if($stockinstatus ==2){
                $data['stockinstatus']=2;
            }elseif($stockinstatus ==3){
                $data['stockinstatus']=3;
            }

            $stockin_sysno = $this->dbh->insert(DB_PREFIX.'doc_stock_in', $data);
            if (!$stockin_sysno) {
                $this->dbh->rollback();
                return false;
            }

            $res = $this->dbh->delete(DB_PREFIX.'doc_stock_in_detail','stockin_sysno=' . intval($stockin_sysno));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if(!empty($stockcarindetaildata))
            foreach ($stockcarindetaildata as $value) {
                $input = array(
                    'stockin_sysno' => $stockin_sysno,
                    'goods_sysno' => $value['goods_sysno'],
                    'goods_quality_sysno' => $value['goods_quality_sysno'],
                    'goodsnature' => $value['goodsnature'],
                    'goodsreceiptdate' => $value['bookingindate'],
                    'tobeqty' => $value['bookinginqty'],
                    'beqty' => $value['beqty'],
                    'storagetank_sysno' => $value['storagetank_sysno'],
                    'qualityname' => $value['qualityname'],
                    'goodsname' => $value['goodsname'],
                    'unitname' => '吨',
                    'memo' => $value['memo'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                );
                $res = $this->dbh->insert(DB_PREFIX.'doc_stock_in_detail', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            $res = $this->dbh->delete(DB_PREFIX.'doc_stock_in_cars','stockin_sysno=' . intval($stockin_sysno));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if(!empty($stockcarincarsdata))
            foreach ($stockcarincarsdata as $key => $value) {
                $input = array(
                    'stockin_sysno' => $stockin_sysno,
                    'carid' => $value['carid'],
                    'carname' => $value['carname'],
                    'mobilephone' => $value['mobilephone'],
                    'idcard' => $value['idcard'],
                    'memo' => $value['memo'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                );
                $res = $this->dbh->insert(DB_PREFIX.'doc_stock_in_cars', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            #车入库操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
            $input = array(
                'doc_sysno' => $stockin_sysno,
                'doctype' => 4,
                'opertype'=>0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc'=>'新建车入库单'
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if($stockinstatus ==2){
                $input['opertype']=1;
                $input['operdesc']='暂存车入库单';
            }elseif($stockinstatus ==3){
                $input['opertype']=2;
                $input['operdesc']='提交车入库单';
            }
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 更新车入库订单
     */
    public function updateStockcarin($id, $data, $stockcarindetaildata, $stockcarincarsdata, $stockinstatus)
    {
        $this->dbh->begin();
        try {
            if ($stockinstatus == 2) {  //暂存
                $data['stockinstatus'] = 2;
            }elseif($stockinstatus ==3){ //提交
                $data['stockinstatus'] = 3;
            }

            $res = $this->dbh->update(DB_PREFIX.'doc_stock_in', $data, 'sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $detailresult = $this->dbh->delete(DB_PREFIX.'doc_stock_in_detail', 'stockin_sysno=' . intval($id));
            $carresult = $this->dbh->delete(DB_PREFIX.'doc_stock_in_cars', 'stockin_sysno=' . intval($id));
            if (!$detailresult||!$carresult) {
                $this->dbh->rollback();
                return false;
            }

            if(!empty($stockcarindetaildata))
            foreach ($stockcarindetaildata as $value) {
                $input = array(
                    'stockin_sysno' => $id,
                    'goods_sysno' => $value['goods_sysno'],
                    'goods_quality_sysno' => $value['goods_quality_sysno'],
                    'goodsnature' => $value['goodsnature'],
                    'goodsreceiptdate' => $value['goodsreceiptdate'],
                    'tobeqty' => $value['tobeqty'],
                    'beqty' => $value['beqty'],
                    'storagetank_sysno' => $value['storagetank_sysno'],
                    'qualityname' => $value['goods_quality_name'],
                    'goodsname' => $value['goodsname'],
                    'unitname' => $value['unitname'],
                    'memo' => $value['memo'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                );
                $res = $this->dbh->insert(DB_PREFIX.'doc_stock_in_detail', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            if(!empty($stockcarincarsdata))
            foreach ($stockcarincarsdata as $key => $value) {
                $input = array(
                    'stockin_sysno' => $id,
                    'carid' => $value['carid'],
                    'carname' => $value['carname'],
                    'mobilephone' => $value['mobilephone'],
                    'idcard' => $value['idcard'],
                    'memo' => $value['memo'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                );
                $res = $this->dbh->insert(DB_PREFIX.'doc_stock_in_cars', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            #车入库操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 4,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
            );

            if($stockinstatus ==2){
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存车入库单';
            }elseif($stockinstatus ==3){
                $input['opertype'] = 2;
                $input['operdesc'] = '提交车入库单';
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 车入库订单完成入库
     */
    public function finishStockcarin($id,$data,$stockcarindetaildata){
        $this->dbh->begin();
        try {
            //更新车入库单主表信息
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_in', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            //更新库存记录的超中转量
            $ST = new StockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

            $instockqty = $this->getpoundsinBystockcarinId($id);
            $goods_sysno = $stockcarindetaildata[0]['goods_sysno'];
            $goodsname = $stockcarindetaildata[0]['goodsname'];
            $qualityname = $stockcarindetaildata[0]['goods_quality_name'];
            $storagetank_sysno = $stockcarindetaildata[0]['storagetank_sysno'];
            $storagetankname = $stockcarindetaildata[0]['storagetankname'];

            //计算库存记录的超中转量
            $params = array(
                'instockqty'=>$instockqty,
                'customer_sysno'=>$data['obj_customer_sysno'],
                'goods_sysno'=>$goods_sysno,
                'contract_sysno'=>$data['contract_sysno'],
                'doctype'=>2,
            );

            $zh = $ST->pubtransferqty($params);

            //计算库存记录的超溢罐量
            $params = array(
                'instockqty'=>$instockqty,
                'customer_sysno'=>$data['obj_customer_sysno'],
                'goods_sysno'=>$goods_sysno,
                'contract_sysno'=>$data['contract_sysno'],
                'storagetank_sysno'=>$storagetank_sysno,
                'doctype'=>2,
            );
            $yi = $ST ->puboverqty($params);

            if($zh['code']=='200'&&!empty($zh['message'])){
                $search = array(
                    'firstfrom_sysno'=>$id,
                    'firstfrom_no'=>$data['stockinno']
                );
                $stockinfo = $ST->searchstock($search);
                if(!empty($stockinfo)){
                    $res = $ST->updatestock($stockinfo[0]['sysno'],$zh['message']);
                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }else{
                    $this->dbh->rollback();
                    return false;
                }
            }

            //更新库存记录的超溢罐量
            if($yi['code']=='200'&&!empty($yi['message'])){
                $search = array(
                    'firstfrom_sysno'=>$id,
                    'firstfrom_no'=>$data['stockinno']
                );
                $stockinfo = $ST->searchstock($search);
                if(!empty($stockinfo)){
                    $res = $ST->updatestock($stockinfo[0]['sysno'],$yi['message']);
                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }else{
                    $this->dbh->rollback();
                    return false;
                }
            }

            ////计算中转量费用
            $Financecost = new FinancecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

//            $stockcarindetaildata = $this->getStockcarindetailById($id);
            $stockcarindetaildata = $this->getstockbystockinid($id);
            if (!$stockcarindetaildata) {
                $this->dbh->rollback();
                return false;
            }

            if(!empty($stockcarindetaildata)){
                foreach($stockcarindetaildata as $item){
                    if($item['sysno']){
                        $stock_sysno = $item['sysno'];
                        $instockdate = $item['instockdate'];
                        $stockdata = $ST ->getElementById([$stock_sysno]);
                        if (!$stockdata) {
                            $this->dbh->rollback();
                            return false;
                        }

                        $transferqty = 0;
                        if($zh['code']!=301){
                            $transferqty = $zh['message']['transferqty'];
                        }
                        $zhdata = array(
                            'contract_sysno'=>$data['contract_sysno'],
                            'contract_no'=>$data['contractno'],
                            'stockin_sysno'=>$id,
                            'stockin_no'=>$data['stockinno'],
                            'instockqty'=>$item['instockqty'],
                            'goods_sysno'=>$goods_sysno,
                            'goodsname'=>$goodsname,
                            'storagetankname'=>$storagetankname,
                            'firstdate'=>$stockdata[0]['firstdate'],
                            'qualityname'=>$qualityname,
                            'customer_sysno'=>$data['customer_sysno'],
                            'customer_name'=>$data['customername'],
                            'stock_sysno'=>$stock_sysno,
                            'transferqty'=>$transferqty,
                        );

                        //error_log(date("Y-m-d H:i:s") . "\t" . json_encode($zhdata) . "\n", 3, './logs/stockcarin.log');

                        $res = $Financecost->costtransfer($zhdata);
                        if (!$res) {
                            $this->dbh->rollback();
                            return false;
                        }

                        //计算首期管道输送费
                        $gddata = array(
                            'contract_sysno'=>$data['contract_sysno'],
                            'contract_no'=>$data['contractno'],
                            'stockin_sysno'=>$id,
                            'stockin_no'=>$data['stockinno'],
                            'instockqty'=>$item['instockqty'],
                            'goods_sysno'=>$goods_sysno,
                            'goodsname'=>$goodsname,
                            'firstdate'=>$stockdata[0]['firstdate'],
                            'qualityname'=>$qualityname,
                            'customer_sysno'=>$data['customer_sysno'],
                            'customer_name'=>$data['customername'],
                            'stock_sysno'=>$stock_sysno,
                        );
                        $res = $Financecost->costtransportamount($gddata);

                        if (!$res) {
                            $this->dbh->rollback();
                            return false;
                        }

                        //计算首期费
                        $overqty = 0;
                        if($yi['code']!=301){
                            $overqty = $yi['message']['overqty'];
                        }
                        $P = new PendcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                        $poudindata = $P->getPoundinfoByScid($id);
                        $storagetankname = '';
                        if(!empty($poudindata)){
                            $storagetankname = $poudindata[0]['storagetankname'];
                        }

                        $fistcostdata = array(
                            'contract_sysno'=>$data['contract_sysno'],
                            'contract_no'=>$data['contractno'],
                            'stockin_sysno'=>$id,
                            'stockin_no'=>$data['stockinno'],
                            'instockqty'=>$item['instockqty'],
                            'goods_sysno'=>$goods_sysno,
                            'goodsname'=>$goodsname,
                            'firstdate'=>$item['firstdate'],
                            'qualityname'=>$qualityname,
                            'customer_sysno'=>$data['customer_sysno'],
                            'customer_name'=>$data['customername'],
                            'stock_sysno'=>$stock_sysno,
                            'storagetankname'=>$item['storagetankname'],
                            'stockindate'=>$item['instockdate'],
                            'overqty'=>$overqty,
                            'ullage' =>$item['ullage']
                        );
//                        $res = $Financecost->costfirst($fistcostdata);
//                        if (!$res) {
//                            $this->dbh->rollback();
//                            return false;
//                        }
                    }
                }
            }

            //更新车入库预约单主表信息
            $array = [
                'bookinginstatus' => '6',
                'issaveorder' => 1
            ];
            $res = $this->dbh->update(DB_PREFIX.'doc_booking_in', $array, 'sysno=' . intval($data['booking_in_sysno']));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            #查询出该车入库订单中的储罐资料 然后在释放罐容
            #查询出该车入库订单中的入库磅码单 然后更新入库订单明细中的入库数量
            $sql = 'select * from `'.DB_PREFIX.'doc_stock_in_detail` where stockin_sysno=' . intval($id);
            $stockcarindetaildata = $this->dbh->select($sql);
            $sql = "SELECT SUM(beqty) FROM ".DB_PREFIX."doc_pounds_in WHERE stockin_sysno = $id";
            $beqty = $this->dbh->select_one($sql);
            if (!$stockcarindetaildata||!$beqty) {
                $this->dbh->rollback();
                return false;
            }

            if(!empty($stockcarindetaildata))
            foreach ($stockcarindetaildata as $value) {
                $storagetank_sysno = $value['storagetank_sysno'];
                $tobeqty = $value['tobeqty'];

                //释放罐容 首先查询出被锁罐的数量
                $sql = "select orderinqty from ".DB_PREFIX."base_storagetank where sysno = $storagetank_sysno";

                $orderinqty = $this->dbh->select_one($sql);
                if (!$orderinqty) {
                    $this->dbh->rollback();
                    return false;
                }

                $storagedata = array(
                    'orderinqty'=>$orderinqty - $tobeqty,
                    'updated_at' => '=NOW()'
                );

                // error_log(date("Y-m-d H:i:s") . "\t" . json_encode($storagedata) . "\n", 3, './logs/stockcarin.log');

                $res = $this->dbh->update(DB_PREFIX.'base_storagetank', $storagedata, 'sysno=' . intval($storagetank_sysno));
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            #车入库操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 4,
                'opertype' => 3,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => '完成入库',
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            //更新国烨云仓数据
            $stockcarindata = $this->getStockcarinById($id);

            if (!$stockcarindata) {
                $this->dbh->rollback();
                return false;
            }
            if(!empty($stockcarindata)){
                $booking_in_sysno = $stockcarindata['booking_in_sysno'];
                $bookingno = $stockcarindata['bookingin_no'];
                $B = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $bookcarindata = $B->getBookcarinInfoById($booking_in_sysno);
                if (!$bookcarindata) {
                    $this->dbh->rollback();
                    return false;
                }
                if($bookcarindata['docsource']==2){
                    if($bookingno=="")
                    {
                        $bookingno = $bookcarindata['bookinginno'];
                    }
                    COMMON::editStockInStatus($bookingno , $instockqty);
                }
            }

            $this->dbh->commit();
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 车入库订单添加车辆
     */
    public function StockcarinAddcar($id,$data,$stockcarincarsdata,$stockinstatus){
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_in', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $res = $this->dbh->delete(DB_PREFIX.'doc_stock_in_cars', 'stockin_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if(!empty($stockcarincarsdata))
                foreach ($stockcarincarsdata as $key => $value) {
                    $input = array(
                        'stockin_sysno' => $id,
                        'carid' => $value['carid'],
                        'carname' => $value['carname'],
                        'mobilephone' => $value['mobilephone'],
                        'idcard' => $value['idcard'],
                        'memo' => $value['memo'],
                        'status' => 1,
                        'isdel' => 0,
                        'version' => 1,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_stock_in_cars', $input);

                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }

            #车入库操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 4,
                'opertype' => 5,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $data['changecarreason'],
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $id;

        }catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 删除车入库订单
     */
    public function delStockcarin($id, $data,$stockcarindetaildata)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_in', $data, 'sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $S = new StockcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
            $stockcarindata = $S->getStockcarinById($id);
            if($stockcarindata){
                $booking_sysno = $stockcarindata['booking_in_sysno'];
            }else{
                $this->dbh->rollback();
                return false;
            }

            $bookcarindata = array(
                'bookinginstatus'=>7,
                'issaveorder'=>0
            );

            $res = $this->dbh->update(DB_PREFIX.'doc_booking_in', $bookcarindata, 'sysno=' . intval($booking_sysno));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $S = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

            if(!empty($stockcarindetaildata))
            foreach($stockcarindetaildata as $item){
                $Storagetankinfo = $S->getStoragetankById($item['storagetank_sysno']);
                $Storagetankdata['orderinqty'] = $Storagetankinfo['orderinqty'] - $item['tobeqty'];
                $res = $this->dbh->update(DB_PREFIX.'base_storagetank', $Storagetankdata, 'sysno=' . intval($item['storagetank_sysno']));

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            #车入库操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 4,
                'opertype' => 5,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => '删除车入库单',
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
            return false;
        }
    }

    /*
     * 根据id获取车入库订单详情
     */
    public function getStockcarindetailById($id)
    {
        $sql = "SELECT s.*,(s.tobeqty-s.beqty) as waitbeqty,bg.`goodsname`,bgq.qualityname as goods_quality_name,st.`storagetankname`,ss.instockqty,ss.instockdate,ss.ullage
                FROM `".DB_PREFIX."doc_stock_in_detail` s
                left join `".DB_PREFIX."base_goods` bg on (s.`goods_sysno`=bg.`sysno`)
                LEFT JOIN ".DB_PREFIX."base_goods_quality bgq ON bgq.sysno = s.goods_quality_sysno
                left join `".DB_PREFIX."base_storagetank` st on (s.`storagetank_sysno`=st.`sysno`)
                LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = s.stock_sysno
                WHERE s.`isdel`=0 and s.`stockin_sysno`= " . intval($id);
        return $this->dbh->select($sql);
    }

    /*
     * 根据id获取车入库库存信息
     */
    public function getstockbystockinid($id){
        $sql = "SELECT ss.sysno,ss.instockqty,ss.instockdate,ss.firstdate,ss.ullage,st.storagetankname
                FROM `".DB_PREFIX."storage_stock` ss
                LEFT JOIN `".DB_PREFIX."base_storagetank` st ON (ss.`storagetank_sysno`=st.`sysno`)
                WHERE ss.iscurrent = 1 AND ss.`isdel`=0 AND ss.`firstfrom_sysno`= " . intval($id);
        return $this->dbh->select($sql);
    }

    /*
     * 根据id获取车入库订单车辆信息
     */
    public function getStockcarinCarById($id){
        $sql = "select * from ".DB_PREFIX."doc_stock_in_cars where stockin_sysno = $id";
        return $this->dbh->select($sql);
    }
}
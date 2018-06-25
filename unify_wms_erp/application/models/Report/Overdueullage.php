<?php

/**
 * Class Report_OverdueullageModel
 */
class Report_OverdueullageModel
{
    /**
     * 数据库类实例
     * @var object
     */
    public $dbh = null;

    /**
     * 缓存数据库实例
     * @var Object
     */
    public $mch = null;

    /**
     * Report_OverdueullageModel constructor.
     * @param $dbh
     * @param null $mch
     */
    public function __construct($dbh, $mch = null)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    public function index()
    {
        //当天的时间
        $date=date("Y-m-d");
        //货主超期损耗
        $sql = "SELECT ss.sysno,ss.shipname,ss.goods_sysno,ss.goodsname,ss.storagetank_sysno,bs.storagetankname,ss.customer_sysno,ss.customername,
                ss.goodsnature,ss.firstdate,ss.stockqty,ss.instockqty,ss.ullage,ss.firstfrom_sysno,ss.firstfrom_no,cg.lastlossrate,cg.isminbalanceullage,cg.minbalancenumber
                FROM ".DB_PREFIX."storage_stock ss
                LEFT JOIN ".DB_PREFIX."doc_contract dc ON ss.contract_sysno = dc.sysno
                LEFT JOIN ".DB_PREFIX."doc_contract_goods cg ON dc.sysno = cg.contract_sysno AND ss.goods_sysno = cg.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_storagetank bs ON bs.sysno = ss.storagetank_sysno
                WHERE ss.isdel = 0 AND ss.iscurrent = 1 AND ss.stockqty > 0 AND ss.firstdate < '$date'";
        $data  = $this -> dbh -> select($sql);

        //启用最小结存量扣损耗
        $sql = "SELECT ss.customer_sysno,ss.firstfrom_sysno,SUM(ss.stockqty) AS stockqty,dcg.minbalancenumber,dcg.lastlossrate FROM hengyang_storage_stock ss
                        LEFT JOIN hengyang_doc_contract dc ON dc.sysno = ss.contract_sysno
                        LEFT JOIN hengyang_doc_contract_goods dcg ON dcg.contract_sysno = dc.sysno AND ss.goods_sysno = ss.goods_sysno
                        WHERE dcg.isminbalanceullage = 1 AND ss.isdel = 0 AND ss.iscurrent = 1
                        GROUP BY ss.customer_sysno,ss.firstfrom_sysno";
        $balancestocks = $this -> dbh -> select($sql);

        //计算某个批次某个客户应该扣的损耗
        if(!empty($data))
        foreach($data as $d => $da){
            $data[$d]['minstock'] = 0;
            if(!empty($balancestocks))
                foreach($balancestocks as $balancestock){
                    if($da['customer_sysno'] == $balancestock['customer_sysno']&&$da['firstfrom_sysno'] == $balancestock['firstfrom_sysno']){
                        $data[$d]['minstock'] = $balancestock['minbalancenumber'] * $balancestock['lastlossrate']/1000;
                    }
                }
        }

        //计算超期损耗量
        if(!empty($data))
        foreach($data as $item){
            $ullage = 0;
            //如果启用最小结存量扣损耗，该批次结存量小于合同启用最小结存量
            if($item['isminbalanceullage'] == 1&&$item['stockqty'] < $item['minbalancenumber']){
                //如果该库存结存量小于该批次损耗量
                if($item['stockqty'] < $item['minstock']){
                    //从该批次第一条库存开始扣
                    $ullage = $item['stockqty'];
                    //更新该批次还需要扣的损耗
                    foreach($data as $a => $dat){
                        if($dat['customer_sysno'] == $item['customer_sysno']&&$dat['firstfrom_sysno'] == $item['firstfrom_sysno']){
                            $data[$a]['minstock'] = ($item['minstock'] - $item['stockqty']) >= 0 ? ($item['minstock'] - $item['stockqty']) : 0;
                        }
                    }
                }else{
                    $ullage = sprintf("%.3f", ($item['minbalancenumber'] * $item['lastlossrate']/1000));
                }
            }else{
                $ullage = sprintf("%.3f", ($item['stockqty'] * $item['lastlossrate']/1000));
            }


            if($ullage == 0)
                continue;

            $ullageinput = array(
                'stock_sysno'=>$item['sysno'],
                'beqty'=>$ullage,
                'beforestockqty'=>$item['stockqty'],
                'stockqty'=>$item['stockqty'] - $ullage >= 0 ? $item['stockqty'] - $ullage : 0,
                'beforeullage'=>$item['ullage'],
                'ullage'=>$item['ullage'] + $ullage,
                'doc_sysno'=>$item['firstfrom_sysno'],
                'docno'=>$item['firstfrom_no'],
                'created_at'=>'=NOW()',
                'updated_at'=>'=NOW()',
            );
            $stockdata = array(
                'stockqty'=>$item['stockqty'] - $ullage >= 0 ? $item['stockqty'] - $ullage : 0,
                'ullage'=>$item['ullage'] + $ullage
            );
            $goods_record = array(
                'doc_time'=>'=NOW()',
                'shipname'=>$item['shipname'],
                'goods_sysno'=>$item['goods_sysno'],
                'goodsname'=>$item['goodsname'],
                'storagetank_sysno'=>$item['storagetank_sysno'],
                'storagetankname'=>$item['storagetankname'],
                'customer_sysno'=>$item['customer_sysno'],
                'customername'=>$item['customername'],
                'beqty'=> 0,
                'stockin_sysno'=>$item['firstfrom_sysno'],
                'stockinno'=>$item['firstfrom_no'],
                'doc_sysno'=>'',
                'docno'=>'超期批量损耗',
                'doc_type'=>15,
                'accountstoragetank_sysno'=>'',
                'accountstoragetankname'=>'',
                'tobeqty'=>'',
                'stock_sysno'=>$item['sysno'],
                'ullage'=> $ullage,
                'takegoodscompany'=>'',
                'goodsnature'=>$item['goodsnature'],
                'takegoodsno'=>'',
                'carid'=>'',
                'introduction_sysno'=>'',
                'introductionno'=>'',
                'stocktype'=> 1,
                'introduction_detail_sysno'=>'',
                'created_at'=>'=NOW()',
                'updated_at'=>'=NOW()',
            );
            $sql = "SELECT tank_stockqty FROM ".DB_PREFIX."base_storagetank WHERE sysno = {$item['storagetank_sysno']}";
            $tank_stockqty = $this -> dbh -> select_one($sql);
            $storagetank = array(
                'tank_stockqty' => $tank_stockqty -$ullage
            );

            if($item['lastlossrate']>0){
                //插入损耗日志表
                $res = $this->insertullage($ullageinput);
                if(!$res){
                    error_log($ullageinput.'插入损耗数据'.PHP_EOL , 3, './logs/eullagebatlog.log');
                    return false;
                }
                //更新库存记录
                $res = $this ->updatestock($item['sysno'],$stockdata);
                if(!$res){
                    error_log($stockdata.'更新库存数据'.PHP_EOL , 3, './logs/eullagebatlog.log');
                    return false;
                }
                //货物进出日志插入损耗日志
                $res = $this ->insertgoodsrecord($goods_record);
                if(!$res){
                    error_log($goods_record.'更新库存数据'.PHP_EOL , 3, './logs/eullagebatlog.log');
                    return false;
                }
                //更新储罐基本表
                $res = $this->updatestoragetank($item['storagetank_sysno'],$storagetank);
                if(!$res){
                    error_log($storagetank.'插入损耗数据'.PHP_EOL , 3, './logs/eullagebatlog.log');
                    return false;
                }
            }

        }

        //查询提单中的库存与损耗
        $sql = "SELECT did.sysno,did.stock_sysno,did.firstdate,did.untakegoodsnum,did.ullage,did.introduction_sysno,
                di.introductionno,di.buy_customer_sysno,di.buy_customername,di.receivestart,di.receiveend,di.freecostdate,di.lossrate,cg.lastlossrate,ss.stockqty,ss.ullage as ssullage,
                ss.firstfrom_sysno,ss.firstfrom_no,ss.shipname,ss.goods_sysno,ss.goodsname,ss.storagetank_sysno,bs.storagetankname,
                ss.customer_sysno,ss.customername,ss.goodsnature
                FROM ".DB_PREFIX."doc_introduction_detail did
                LEFT JOIN ".DB_PREFIX."doc_introduction di ON di.sysno = did.introduction_sysno
                LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = did.stock_sysno
                LEFT JOIN ".DB_PREFIX."doc_contract dc ON ss.contract_sysno = dc.sysno
                LEFT JOIN ".DB_PREFIX."doc_contract_goods cg ON dc.sysno = cg.contract_sysno AND did.goods_sysno = cg.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_storagetank bs ON bs.sysno = ss.storagetank_sysno
                WHERE di.isdel = 0 AND ss.isdel = 0 AND did.untakegoodsnum > 0 AND did.introductiondetailstatus IN (4,5)";
        $intro = $this -> dbh -> select($sql);

        //启用最小结存量扣损耗
        $sql = "SELECT di.buy_customer_sysno,ss.firstfrom_sysno,SUM(did.untakegoodsnum) AS stockqty,dcg.minbalancenumber,dcg.lastlossrate
                        FROM ".DB_PREFIX."doc_introduction_detail did
                        LEFT JOIN ".DB_PREFIX."doc_introduction di ON di.sysno = did.introduction_sysno
                        LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = did.stock_sysno
                        LEFT JOIN hengyang_doc_contract dc ON dc.sysno = ss.contract_sysno
                        LEFT JOIN hengyang_doc_contract_goods dcg ON dcg.contract_sysno = dc.sysno AND ss.goods_sysno = ss.goods_sysno
                        WHERE dcg.isminbalanceullage = 1 AND ss.iscurrent = 1 AND ss.isdel = 0 AND di.isdel = 0 AND did.introductiondetailstatus IN (4,5)
                        GROUP BY di.buy_customer_sysno,ss.firstfrom_sysno ";
        $balancestocks = $this -> dbh -> select($sql);

        //计算某个批次某个客户应该扣的损耗
        if(!empty($intro))
            foreach($intro as $d => $da){
                $intro[$d]['minstock'] = 0;
                if(!empty($balancestocks))
                    foreach($balancestocks as $balancestock){
                        if($da['customer_sysno'] == $balancestock['buy_customer_sysno']&&$da['firstfrom_sysno'] == $balancestock['firstfrom_sysno']){
                            $intro[$d]['minstock'] = $balancestock['minbalancenumber'] * $balancestock['lastlossrate']/1000;
                        }
                    }
            }

        //计算超期损耗量
        if(!empty($intro))
        foreach($intro as $item) {
            $ullage = 0;
            //如果介绍信中的损耗率大于0，（则不存在启用最小结存量扣损耗，因为找不到合同） 并且过了免仓期，则按新的损耗算 否则按合同损耗计算
            //即 不可撤销提单 由下家承担损耗 插入batlog记录 record记录 更新介绍信
            //否则 不可撤销提单 由上家承担损耗货转 可撤销提单 插入 batlog记录 record记录 更新库存表
            if ($item['lossrate'] > 0 && date("Y-m-d", strtotime('' . $item['receiveend'] . '+1 day')) < date('Y-m-d')) {
                $ullage = sprintf("%.3f", ($item['untakegoodsnum'] * $item['lossrate'] / 1000));

                if ($ullage == 0)
                    continue;

                $ullageinput = array(
                    'stock_sysno' => $item['stock_sysno'],
                    'introduction_detail_sysno' => $item['sysno'],
                    'beqty' => $ullage,
                    'beforestockqty' => $item['untakegoodsnum'],
                    'stockqty' => $item['untakegoodsnum'] - $ullage >= 0 ? $item['untakegoodsnum'] - $ullage : 0,
                    'beforeullage' => $item['ullage'],
                    'ullage' => $item['ullage'] + $ullage,
                    'doc_sysno' => $item['introduction_sysno'],
                    'docno' => $item['introductionno'],
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                );
                $introdetaildata = array(
                    'untakegoodsnum' => $item['untakegoodsnum'] - $ullage >= 0 ? $item['untakegoodsnum'] - $ullage : 0,
                    'ullage' => $item['ullage'] + $ullage
                );
                $goods_record = array(
                    'doc_time' => '=NOW()',
                    'shipname' => $item['shipname'],
                    'goods_sysno' => $item['goods_sysno'],
                    'goodsname' => $item['goodsname'],
                    'storagetank_sysno' => $item['storagetank_sysno'],
                    'storagetankname' => $item['storagetankname'],
                    'customer_sysno' => $item['buy_customer_sysno'],
                    'customername' => $item['buy_customername'],
                    'beqty' => 0,
                    'stockin_sysno' => $item['firstfrom_sysno'],
                    'stockinno' => $item['firstfrom_no'],
                    'doc_sysno' => '',
                    'docno' => '超期批量损耗',
                    'doc_type' => 15,
                    'accountstoragetank_sysno' => '',
                    'accountstoragetankname' => '',
                    'tobeqty' => '',
                    'stock_sysno' => $item['stock_sysno'],
                    'ullage' => $ullage,
                    'takegoodscompany' => '',
                    'goodsnature' => $item['goodsnature'],
                    'takegoodsno' => '',
                    'carid' => '',
                    'introduction_sysno' => '',
                    'introductionno' => '',
                    'stocktype' => 1,
                    'introduction_detail_sysno' => $item['sysno'],
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                );
                $sql = "SELECT tank_stockqty FROM " . DB_PREFIX . "base_storagetank WHERE sysno = {$item['storagetank_sysno']}";
                $tank_stockqty = $this->dbh->select_one($sql);
                $storagetank = array(
                    'tank_stockqty' => $tank_stockqty - $ullage
                );
                //插入损耗日志表
                $res = $this->insertullage($ullageinput);
                if (!$res) {
                    error_log($ullageinput . '插入损耗数据' . PHP_EOL, 3, './logs/eullagebatlog.log');
                    return false;
                }
                //更新介绍信明细
                $res = $this->updateintrodetail($item['sysno'], $introdetaildata);
                if (!$res) {
                    error_log($introdetaildata . '更新介绍信明细数据' . PHP_EOL, 3, './logs/eullagebatlog.log');
                    return false;
                }
                //货物进出日志插入损耗日志
                $res = $this->insertgoodsrecord($goods_record);
                if (!$res) {
                    error_log($goods_record . '插入损耗数据' . PHP_EOL, 3, './logs/eullagebatlog.log');
                    return false;
                }
                //更新储罐基本表
                $res = $this->updatestoragetank($item['storagetank_sysno'], $storagetank);
                if (!$res) {
                    error_log($storagetank . '插入损耗数据' . PHP_EOL, 3, './logs/eullagebatlog.log');
                    return false;
                }

            } elseif ($item['firstdate'] < date('Y-m-d')) {
                //如果启用最小结存量扣损耗，该批次结存量小于合同启用最小结存量
                if ($item['isminbalanceullage'] == 1 && $item['stockqty'] < $item['minbalancenumber']) {
                    //如果该库存结存量小于该批次损耗量
                    if ($item['stockqty'] < $item['minstock']) {
                        //从该批次第一条库存开始扣
                        $ullage = $item['stockqty'];
                        //更新该批次还需要扣的损耗
                        foreach ($intro as $a => $dat) {
                            if ($dat['customer_sysno'] == $item['customer_sysno'] && $dat['firstfrom_sysno'] == $item['firstfrom_sysno']) {
                                $intro[$a]['minstock'] = ($item['minstock'] - $item['stockqty']) >= 0 ? ($item['minstock'] - $item['stockqty']) : 0;
                            }
                        }
                    } else {
                        $ullage = sprintf("%.3f", ($item['minbalancenumber'] * $item['lastlossrate'] / 1000));
                    }
                } else {
                    $ullage = sprintf("%.3f", ($item['untakegoodsnum'] * $item['lastlossrate'] / 1000));
                }

                if ($ullage == 0)
                    continue;

                $sql = "SELECT stockqty FROM " . DB_PREFIX . "storage_stock WHERE sysno = {$item['stock_sysno']}";
                $stockqty = $this->dbh->select_one($sql);
                $ullageinput = array(
                    'stock_sysno' => $item['stock_sysno'],
                    'beqty' => $ullage,
                    'beforestockqty' => $item['stockqty'],
                    'stockqty' => $stockqty - $ullage >= 0 ? $stockqty - $ullage : 0,
                    'beforeullage' => $item['ssullage'],
                    'ullage' => $item['ssullage'] + $ullage,
                    'doc_sysno' => $item['firstfrom_sysno'],
                    'docno' => $item['firstfrom_no'],
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                );

                //如果原货主够扣库存 则扣原货主的 否则 扣本提单的
                if ($item['stockqty'] - $ullage >= 0) {
                    $stockdata = array(
                        'stockqty' => $stockqty - $ullage >= 0 ? $stockqty - $ullage : 0,
                        'ullage' => $item['ssullage'] + $ullage
                    );
                } else {
                    $introdetaildata = array(
                        'untakegoodsnum' => $item['untakegoodsnum'] - $ullage >= 0 ? $item['untakegoodsnum'] - $ullage : 0,
                        'ullage' => $item['ullage'] + $ullage
                    );
                }

                $goods_record = array(
                    'doc_time' => '=NOW()',
                    'shipname' => $item['shipname'],
                    'goods_sysno' => $item['goods_sysno'],
                    'goodsname' => $item['goodsname'],
                    'storagetank_sysno' => $item['storagetank_sysno'],
                    'storagetankname' => $item['storagetankname'],
                    'customer_sysno' => $item['customer_sysno'],
                    'customername' => $item['customername'],
                    'beqty' => 0,
                    'stockin_sysno' => $item['firstfrom_sysno'],
                    'stockinno' => $item['firstfrom_no'],
                    'doc_sysno' => '',
                    'docno' => '超期批量损耗',
                    'doc_type' => 15,
                    'accountstoragetank_sysno' => '',
                    'accountstoragetankname' => '',
                    'tobeqty' => '',
                    'stock_sysno' => $item['stock_sysno'],
                    'ullage' => $ullage,
                    'takegoodscompany' => '',
                    'goodsnature' => $item['goodsnature'],
                    'takegoodsno' => '',
                    'carid' => '',
                    'introduction_sysno' => '',
                    'introductionno' => '',
                    'stocktype' => 1,
                    'introduction_detail_sysno' => $item['sysno'],
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                );

                //如果原货主够扣库存 则扣原货主的 否则 扣本提单的
                if ($item['stockqty'] - $ullage >= 0) {
                    $goods_record['customer_sysno'] = $item['customer_sysno'];
                    $goods_record['customername'] = $item['customername'];
                } else {
                    $goods_record['customer_sysno'] = $item['buy_customer_sysno'];
                    $goods_record['customername'] = $item['buy_customername'];
                }


                $sql = "SELECT tank_stockqty FROM " . DB_PREFIX . "base_storagetank WHERE sysno = {$item['storagetank_sysno']}";
                $tank_stockqty = $this->dbh->select_one($sql);
                $storagetank = array(
                    'tank_stockqty' => $tank_stockqty - $ullage
                );
                if ($item['lastlossrate'] > 0) {
                    $res = $this->insertullage($ullageinput);
                    if (!$res) {
                        error_log($ullageinput . '插入损耗数据' . PHP_EOL, 3, './logs/eullagebatlog.log');
                        return false;
                    }
                    if ($item['stockqty'] - $ullage >= 0) {
                        $res = $this->updatestock($item['stock_sysno'], $stockdata);
                        if (!$res) {
                            error_log($stockdata . '更新库存数据' . PHP_EOL, 3, './logs/eullagebatlog.log');
                            return false;
                        }
                    } else {
                        //更新介绍信明细
                        $res = $this->updateintrodetail($item['sysno'], $introdetaildata);
                        if (!$res) {
                            error_log($introdetaildata . '更新介绍信明细数据' . PHP_EOL, 3, './logs/eullagebatlog.log');
                            return false;
                        }
                    }

                    //货物进出日志插入损耗日志
                    $res = $this->insertgoodsrecord($goods_record);
                    if (!$res) {
                        error_log($goods_record . '插入损耗数据' . PHP_EOL, 3, './logs/eullagebatlog.log');
                        return false;
                    }
                    //更新储罐基本表
                    $res = $this->updatestoragetank($item['storagetank_sysno'], $storagetank);
                    if (!$res) {
                        error_log($storagetank . '插入损耗数据' . PHP_EOL, 3, './logs/eullagebatlog.log');
                        return false;
                    }

                }
            }
        }
        return true;
    }

    /**
     * 插入损耗数据
     */
    private function insertullage($data){
        return $this -> dbh -> insert(DB_PREFIX.'storage_stock_batlog', $data);
    }

    /*
     * 更新库存数据
     */
    private function updatestock($id,$data){
        return $this->dbh->update(DB_PREFIX.'storage_stock', $data, 'sysno=' . intval($id));
    }

    /*
     * 更新介绍信明细数据
     */
    private function updateintrodetail($id,$data){
        return $this->dbh->update(DB_PREFIX.'doc_introduction_detail', $data, 'sysno=' . intval($id));
    }

    /*
     * 货物进出日志插入损耗日志
     */
    private function insertgoodsrecord($goods_record){
        return $this -> dbh -> insert(DB_PREFIX.'doc_goods_record_log', $goods_record);
    }

    private function updatestoragetank($id,$data){
        return $this->dbh->update(DB_PREFIX.'base_storagetank', $data, 'sysno=' . intval($id));
    }


    public function backstock(){
        $sql = "SELECT grl.stock_sysno,SUM(grl.ullage) AS ullages,ss.stockqty,ss.ullage FROM hengyang_doc_goods_record_log grl
                LEFT JOIN hengyang_storage_stock ss ON ss.sysno = grl.stock_sysno
                WHERE grl.updated_at > '2017-11-02 09:00:00' AND grl.updated_at < '2017-11-02 16:00:00' AND grl.doc_type = 15
                GROUP BY grl.stock_sysno";
        $data = $this ->dbh ->select($sql);
        if(!empty($data)){
            foreach($data as $item){
                $stockdata = array(
                    'stockqty' => $item['stockqty'] + $item['ullages'],
                    'ullage' => $item['ullage'] - $item['ullages']
                );
                //更新库存记录
                $res = $this ->updatestock($item['stock_sysno'],$stockdata);
                if(!$res){
                    error_log($stockdata.'更新库存数据'.PHP_EOL , 3, './logs/backstock.log');
                    return false;
                }
            }
        }
    }

}
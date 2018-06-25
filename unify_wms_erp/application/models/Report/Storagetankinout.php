<?php
/**
 * Storagetankinout Model
 */
class Report_StoragetankinoutModel {
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
     * @param   object  $dbh
     * @return  void
     */
    public function __construct($dbh, $mch = null) {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    public function getTankList($params){ 
        $filter = array();
        if (isset($params['date1']) && $params['date1'] != '' ) {
            $filter[] = " bs.created_at >='{$params['date1']}'";
            $filter2[] = " bs.created_at <'{$params['date1']}'";
        }
        if (isset($params['date2']) && $params['date2'] != '' ) {
            $filter[] = " bs.created_at <='{$params['date2']}'";
        }
        if (isset($params['tankno']) && $params['tankno'] != '' ) {
            $filter[] = " bs.storagetank_sysno={$params['tankno']}";
            $filter2[] = " bs.storagetank_sysno={$params['tankno']}";
        }
        if (isset($params['goodsno']) && $params['goodsno'] != '' ) {
            $filter[] = "bs.goods_sysno={$params['goodsno']}";
            $filter2[] = "bs.goods_sysno={$params['goodsno']}";
        }
        $where = " WHERE bs.status=1 AND bs.isdel=0 ";
        if (count($filter)>0) {
            $where .= " AND ".implode(' AND ', $filter);
        }
        $where2 = " WHERE bs.status=1 AND bs.isdel=0 ";
        if (count($filter2)>0) {
            $where2 .= " AND ".implode(' AND ', $filter2);
        }

        $sql = "SELECT count(DISTINCT bs.sysno) FROM `hengyang_doc_goods_record_log` bs $where";
        $result['totalRow'] = $this->dbh->select_one($sql);
        $result['list'] = array();
        if(count($result['totalRow'])>0) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT bs.sysno,bs.storagetankname,bs.storagetank_sysno,bs.goodsname,bs.goods_sysno FROM hengyang_doc_goods_record_log bs $where
				GROUP BY bs.storagetank_sysno,bs.goods_sysno ORDER BY bs.storagetankname "; 
                $allData = $this->dbh->select($sql);
                $where = " WHERE bs.status=1 AND bs.isdel=0 ";
                if( $allData){
                    //上期结存量
                    $sql = "SELECT storagetank_sysno,goods_sysno,sum(beqty-ullage) as startqty from ".DB_PREFIX."doc_goods_record_log bs {$where2} and bs.created_at<='{$params['date1']}' GROUP BY storagetank_sysno,goods_sysno";
                    $startqtystock = $this->dbh->select($sql);
                    foreach ($allData as $key => $value) {
                        foreach ($startqtystock as $evalue) {
                            if($value['storagetank_sysno'] == $evalue['storagetank_sysno'] && $value['goods_sysno'] == $evalue['goods_sysno']){
                                $allData[$key]['startqty'] = $evalue['startqty'];
                            }
                        }
                    }    
                    //本期入库数量
                    $sql = "SELECT storagetank_sysno,goods_sysno,sum(beqty) as totalinstockqty from ".DB_PREFIX."doc_goods_record_log bs {$where}  AND  bs.created_at >= '{$params['date1']}' AND bs.created_at <='{$params['date2']}'and doc_type in (1,2,5,11,13,17,19,24) GROUP BY storagetank_sysno,goods_sysno";
                    $totalinstockqty = $this->dbh->select($sql);
                    foreach ($allData as $key => $value) {
                        foreach ($totalinstockqty as $evalue) {
                            if($value['storagetank_sysno'] == $evalue['storagetank_sysno'] && $value['goods_sysno'] == $evalue['goods_sysno']){
                                $allData[$key]['totalinstockqty'] = $evalue['totalinstockqty'];
                            }
                        }
                    }
                    //本期出库数量
                    $sql = "SELECT storagetank_sysno,goods_sysno,sum(beqty) as totaloutstockqty from ".DB_PREFIX."doc_goods_record_log bs {$where}  AND  bs.created_at >= '{$params['date1']}' AND bs.created_at <='{$params['date2']}'and doc_type in (3,4,6,12,14,16,20,23) GROUP BY storagetank_sysno,goods_sysno";
                    $totaloutstockqty = $this->dbh->select($sql);
                    foreach ($allData as $key => $value) {
                        foreach ($totaloutstockqty as $evalue) {
                            if($value['storagetank_sysno'] == $evalue['storagetank_sysno'] && $value['goods_sysno'] == $evalue['goods_sysno']){
                                $allData[$key]['totaloutstockqty'] = $evalue['totaloutstockqty'];
                            }
                        }
                    }
                    //本期倒入量
                    $sql = "SELECT storagetank_sysno,goods_sysno,sum(beqty) as inretank from ".DB_PREFIX."doc_goods_record_log bs {$where} AND  bs.created_at >= '{$params['date1']}' AND bs.created_at <='{$params['date2']}' and doc_type in (7,21) GROUP BY storagetank_sysno,goods_sysno";                                 //echo $sql ;die;
                    $inretank = $this->dbh->select($sql);
                    foreach ($allData as $key => $value) {
                        foreach ($inretank as $evalue) {
                            if($value['storagetank_sysno'] == $evalue['storagetank_sysno'] && $value['goods_sysno'] == $evalue['goods_sysno']){
                                $allData[$key]['inretank'] = $evalue['inretank'];
                            }
                        }
                    }
                    //本期倒出量
                    $sql = "SELECT storagetank_sysno,goods_sysno,sum(beqty) as outretank from ".DB_PREFIX."doc_goods_record_log bs {$where} AND  bs.created_at >= '{$params['date1']}' AND bs.created_at <='{$params['date2']}' and doc_type in (8,22) GROUP BY storagetank_sysno,goods_sysno";
                    $outretank = $this->dbh->select($sql);
                    foreach ($allData as $key => $value) {
                        foreach ($outretank as $evalue) {
                            if($value['storagetank_sysno'] == $evalue['storagetank_sysno'] && $value['goods_sysno'] == $evalue['goods_sysno']){
                                $allData[$key]['outretank'] = $evalue['outretank'];
                            }
                        }
                    }
                    //本期损耗量
                    $sql = "SELECT storagetank_sysno,goods_sysno,sum(-ullage) as totalclearqty from ".DB_PREFIX."doc_goods_record_log bs {$where} AND  bs.created_at >= '{$params['date1']}' AND bs.created_at <='{$params['date2']}' and doc_type = 18 GROUP BY storagetank_sysno,goods_sysno";
                    $totalclearqty = $this->dbh->select($sql);
                    foreach ($allData as $key => $value) {
                        foreach ($totalclearqty as $evalue) {
                            if($value['storagetank_sysno'] == $evalue['storagetank_sysno'] && $value['goods_sysno'] == $evalue['goods_sysno']){
                                $allData[$key]['totalclearqty'] = $evalue['totalclearqty'];
                            }
                        }
                    }
                    //本期退货量
                    $sql = "SELECT storagetank_sysno,goods_sysno,sum(beqty) as totalreturnqty from ".DB_PREFIX."doc_goods_record_log bs {$where} AND  bs.created_at >= '{$params['date1']}' AND bs.created_at <='{$params['date2']}' and doc_type = 26 GROUP BY storagetank_sysno,goods_sysno";
                    $totalreturnqty = $this->dbh->select($sql);
                    foreach ($allData as $key => $value) {
                        foreach ($totalreturnqty as $evalue) {
                            if($value['storagetank_sysno'] == $evalue['storagetank_sysno'] && $value['goods_sysno'] == $evalue['goods_sysno']){
                                $allData[$key]['totalreturnqty'] = $evalue['totalreturnqty'];
                            }
                        }
                    }

                    $result['list'] = $allData;
                }
                foreach($result['list'] as $key=>$item){
                    $result['list'][$key]['totalstockqty']=sprintf("%.3f",$item['startqty']+$item['totalinstockqty']+$item['inretank']+$item['totaloutstockqty']+$item['outretank']+$item['totalcheckqty']+$item['totalclearqty']+$item['totalreturnqty']);
                }
            }else{
                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);
                $sql = "SELECT bs.sysno,bs.storagetankname,bs.storagetank_sysno,bs.goodsname,bs.goods_sysno FROM hengyang_doc_goods_record_log bs {$where}
				GROUP BY bs.storagetank_sysno,bs.goods_sysno ORDER BY bs.storagetankname "; 
                $allData = $this->dbh->select_page($sql);
                $allDataNo = $this->dbh->select($sql);
                $where = " WHERE bs.status=1 AND bs.isdel=0 ";
                if( $allData){
                    //上期结存量
                    $sql = "SELECT storagetank_sysno,goods_sysno,sum(beqty-ullage) as startqty from ".DB_PREFIX."doc_goods_record_log bs {$where2} and bs.created_at<='{$params['date']}' GROUP BY storagetank_sysno,goods_sysno";
                    $startqtystock = $this->dbh->select($sql);
                    foreach ($allData as $key => $value) {
                        foreach ($startqtystock as $evalue) {
                            if($value['storagetank_sysno'] == $evalue['storagetank_sysno'] && $value['goods_sysno'] == $evalue['goods_sysno']){
                                $allData[$key]['startqty'] = $evalue['startqty'];
                            }
                        }
                    }
                    //本期入库数量
                    $sql = "SELECT storagetank_sysno,goods_sysno,sum(beqty) as totalinstockqty from ".DB_PREFIX."doc_goods_record_log bs {$where}  AND  bs.created_at >= '{$params['date1']}' AND bs.created_at <='{$params['date2']}'and doc_type in (1,2,5,11,13,17,19,24) GROUP BY storagetank_sysno,goods_sysno";
                    $totalinstockqty = $this->dbh->select($sql);
                    foreach ($allData as $key => $value) {
                        foreach ($totalinstockqty as $evalue) {
                            if($value['storagetank_sysno'] == $evalue['storagetank_sysno'] && $value['goods_sysno'] == $evalue['goods_sysno']){
                                $allData[$key]['totalinstockqty'] = $evalue['totalinstockqty'];
                            }
                        }
                    }

                    //本期出库数量
                    $sql = "SELECT storagetank_sysno,goods_sysno,sum(beqty) as totaloutstockqty from ".DB_PREFIX."doc_goods_record_log bs {$where} AND  bs.created_at >= '{$params['date1']}' AND bs.created_at <='{$params['date2']}' and doc_type in (3,4,6,12,14,16,20,23) GROUP BY storagetank_sysno,goods_sysno";
                    $totaloutstockqty = $this->dbh->select($sql);
                    foreach ($allData as $key => $value) {
                        foreach ($totaloutstockqty as $evalue) {
                            if($value['storagetank_sysno'] == $evalue['storagetank_sysno'] && $value['goods_sysno'] == $evalue['goods_sysno']){
                                $allData[$key]['totaloutstockqty'] = $evalue['totaloutstockqty'];
                            }
                        }
                    }

                    //本期倒入量
                    $sql = "SELECT storagetank_sysno,goods_sysno,sum(beqty) as inretank from ".DB_PREFIX."doc_goods_record_log bs {$where} AND  bs.created_at >= '{$params['date1']}' AND bs.created_at <='{$params['date2']}' and doc_type in (7,21) GROUP BY storagetank_sysno,goods_sysno";
                    $inretank = $this->dbh->select($sql);
                    foreach ($allData as $key => $value) {
                        foreach ($inretank as $evalue) {
                            if($value['storagetank_sysno'] == $evalue['storagetank_sysno'] && $value['goods_sysno'] == $evalue['goods_sysno']){
                                $allData[$key]['inretank'] = $evalue['inretank'];
                            }
                        }
                    }

                    //本期倒出量
                    $sql = "SELECT storagetank_sysno,goods_sysno,sum(beqty) as outretank from ".DB_PREFIX."doc_goods_record_log bs {$where} AND  bs.created_at >= '{$params['date1']}' AND bs.created_at <='{$params['date2']}' and doc_type in (8,22) GROUP BY storagetank_sysno,goods_sysno";
                    $outretank = $this->dbh->select($sql);
                    foreach ($allData as $key => $value) {
                        foreach ($outretank as $evalue) {
                            if($value['storagetank_sysno'] == $evalue['storagetank_sysno'] && $value['goods_sysno'] == $evalue['goods_sysno']){
                                $allData[$key]['outretank'] = $evalue['outretank'];
                            }
                        }
                    }

                    //本期损耗量
                    $sql = "SELECT storagetank_sysno,goods_sysno,sum(-ullage) as totalclearqty from ".DB_PREFIX."doc_goods_record_log bs {$where} AND  bs.created_at >= '{$params['date1']}' AND bs.created_at <='{$params['date2']}' and doc_type = 18 GROUP BY storagetank_sysno,goods_sysno";
                    $totalclearqty = $this->dbh->select($sql);
                    foreach ($allData as $key => $value) {
                        foreach ($totalclearqty as $evalue) {
                            if($value['storagetank_sysno'] == $evalue['storagetank_sysno'] && $value['goods_sysno'] == $evalue['goods_sysno']){
                                $allData[$key]['totalclearqty'] = $evalue['totalclearqty'];
                            }
                        }
                    }
                    //本期退货量
                    $sql = "SELECT storagetank_sysno,goods_sysno,sum(beqty) as totalreturnqty from ".DB_PREFIX."doc_goods_record_log bs {$where} AND  bs.created_at >= '{$params['date1']}' AND bs.created_at <='{$params['date2']}' and doc_type = 26 GROUP BY storagetank_sysno,goods_sysno";
                    $totalreturnqty = $this->dbh->select($sql);
                    foreach ($allData as $key => $value) {
                        foreach ($totalreturnqty as $evalue) {
                            if($value['storagetank_sysno'] == $evalue['storagetank_sysno'] && $value['goods_sysno'] == $evalue['goods_sysno']){
                                $allData[$key]['totalreturnqty'] = $evalue['totalreturnqty'];
                            }
                        }
                    }

                    $result['totalRow'] = count($allDataNo);
                    $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
                    $result['list'] = $allData;
                }
            }   
            foreach($result['list'] as $key=>$item){
                $result['list'][$key]['storagetanknature'] = $this ->getStorageTankNature($item['storagetank_sysno']);
                $result['list'][$key]['totalstockqty']=sprintf("%.3f",$item['startqty']+$item['totalinstockqty']+$item['inretank']+$item['totaloutstockqty']+$item['outretank']+$item['totalcheckqty']+$item['totalclearqty']+$item['totalreturnqty']);
            }
        }
        
        return $result;
    }


    /*
     * 获取储罐汇总明细表数据
     */
    public function getTankDetail($params){
        $filter = array();
        if (isset($params['date1']) && $params['date1'] != '' ) {
            $filter[] = " dsl.created_at >='{$params['date1']}'";
        }
        if (isset($params['date2']) && $params['date2'] != '' ) {
            $filter[] = " dsl.created_at <='{$params['date2']}'";
        }
        if (isset($params['tankno']) && $params['tankno'] != '' ) {
            $filter[] = " dsl.storagetank_sysno = {$params['tankno']}";
        }
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '' ) {
            $filter[] = " dsl.goods_sysno = {$params['goods_sysno']}";
        }

        $where = " where dsl.isdel = 0 AND dsl.doc_type not in (15,25) ";
        if (count($filter)>0) {
            $where .= ' AND ' .implode(' AND ', $filter);
        }
        $order = " order by dsl.created_at ";

        $sql = "SELECT count(DISTINCT dsl.sysno) FROM `hengyang_doc_goods_record_log` dsl $where"; 
        $result['totalRow'] = $this->dbh->select_one($sql);
        $result['list'] = array();
        if($result['totalRow']>0) {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);
                $sql = "SELECT DISTINCT  dsl.sysno,dsl.customername,dsl.doc_sysno,dsl.docno,dsi.booking_in_sysno,CASE dsl.doc_type
						WHEN 15 THEN -dsl.ullage
						WHEN 18 THEN -dsl.ullage
						ELSE dsl.beqty
						END as beqty,CASE dsl.doc_type
						WHEN 1 THEN dsl.shipname
						WHEN 2 THEN '槽车'
						WHEN 3 THEN dsl.shipname
						WHEN 4 THEN '槽车'
						WHEN 3 THEN '管输'
						WHEN 4 THEN '管输'
						ELSE '--'
						END as transportationtype,
						dsl.created_at,dsl.doc_type
						FROM hengyang_doc_goods_record_log dsl LEFT JOIN hengyang_doc_stock_in  dsi ON dsl.doc_sysno= dsi.sysno $where $order";
                $allData = $this->dbh->select($sql);

                if(isset($params['page'])&&$params['page']==false){
                    $result['list'] = $allData;
                }else{  
                    $result['totalRow'] = count($allData);
                    $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
                    $datalist=array_chunk($allData,$params['pageSize'],false); 
                    $result['list']= $datalist [$params['pageCurrent']-1] ? $datalist [$params['pageCurrent']-1] : [];

                    $offsetprev=floatval($params['startqty']);
                    foreach ($datalist as $key=>$val){
                        if($key < $params['pageCurrent']-1){
                            foreach ($val as $k=>$subval){
                                $offsetarr[] = $subval['beqty'];
                            }
                        }
                    }
                    $offsetprev+= array_sum($offsetarr?$offsetarr:[]); 
                }

                foreach ($result['list'] as $key=>$val){
                    $sql = "SELECT doc_type FROM hengyang_doc_goods_record_log WHERE sysno = {$val['sysno']}";
                    $res = $this->dbh->select_one($sql);
                    $result['list'][$key]['doc_sysno_type'] = COMMON::logdocsysnotypeArr[$res];
                    $offsetprev += floatval($val['beqty']);
                    $result['list'][$key]['clearingstock'] = sprintf("%.3f",$offsetprev);
                }
        }

        return $result;
    }

    public function getStartAndEnd($id,$date1){
        $sql = "SELECT (beforebeqty+beqty) AS startqty FROM hengyang_doc_goods_record_log WHERE created_at <='$date1' AND storagetank_sysno = $id ORDER BY created_at DESC LIMIT 0,1 ";
        $res = $this->dbh->select_one($sql);
        $result['startqty'] = $res?$res:0;

        $sql = "SELECT tank_stockqty as totalstockqty FROM hengyang_base_storagetank where sysno = $id";
        $res = $this->dbh->select_one($sql);
        $result['totalstockqty'] = $res?$res:0;

        return $result;
    }

    public function getStorageTank(){
        $sql = "SELECT `sysno`,`storagetanknature`,`storagetankname` FROM `hengyang_base_storagetank` WHERE `isdel`=0 AND `status`=1 ORDER BY storagetankname";
        return $this->dbh->select($sql);
    }
    //获取商品信息
    public function getGoodsInfo(){
        $sql = "SELECT `sysno`,`goodsname` FROM `hengyang_base_goods` WHERE `status`=1 AND `isdel`=0 ORDER BY created_at DESC";
        return $this->dbh->select_hash($sql);
    }
    //获取商品名称byID
    public function getGoodsName($id){
        $sql = "SELECT `goodsname` FROM `hengyang_base_goods` WHERE `status`=1 AND `isdel`=0 AND sysno={$id}";
        return $this->dbh->select_one($sql);
    }
    //获取储罐号byID
    public function getStorageTankName($id){
        $sql = "SELECT `storagetankname` FROM `hengyang_base_storagetank` WHERE `isdel`=0 AND `status`=1 AND sysno={$id}";
        return $this->dbh->select_one($sql);
    }
    //获取储罐性质byID
    public function getStorageTankNature($id){
        $sql = "SELECT `storagetanknature` FROM `hengyang_base_storagetank` WHERE `isdel`=0 AND `status`=1 AND sysno={$id}";
        return $this->dbh->select_one($sql);
    }
}
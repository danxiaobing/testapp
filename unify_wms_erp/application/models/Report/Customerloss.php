<?php
/**
 * Stockout Model
 *
 */

class Report_CustomerlossModel
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
     * Constructor
     *
     * @param   object  $dbh
     * @param   object  $mch
     * @return  void
     */
    public function __construct($dbh, $mch)
    {
        $this->dbh = $dbh;

        $this->mch = $mch;
    }
    public function search($params) {
        $filter = array();
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " a.goods_sysno = '{$params['goods_sysno']}' ";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " a.customer_sysno = '{$params['customer_sysno']}'";
        }
        if (isset($params['goodsnature']) && $params['goodsnature'] != '') {
            $filter[] = " a.goodsnature = '{$params['goodsnature']}'";
        }
        if (isset($params['start_time']) && $params['start_time'] != '') {
            $filter[] = " b.created_at >='{$params['start_time']}"." 00:00:00'";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " b.created_at <='{$params['end_time']}"." 23:59:59'";
        }
        if (isset($params['stockin_sysno']) && $params['stockin_sysno'] != '') {
            $filter[] = "a.stockin_sysno = '{$params['stockin_sysno']}'";
        }
        $where ='a.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $result = $params;
        $result['list'] = array();
        $sql = "SELECT count(a.sysno) from ".DB_PREFIX."doc_goods_record_log a 
        left join ".DB_PREFIX."doc_stock_in b on b.sysno=a.stockin_sysno 
        where  a.isdel=0 GROUP BY a.customer_sysno,a.goods_sysno,a.goodsnature,a.stockin_sysno";
        $result['totalRow'] = $this->dbh->select_one($sql);

        if ($result['totalRow']) {
            //有记录的客户
            $sql = "SELECT a.sysno,a.customername,a.customer_sysno,a.goods_sysno,a.goodsname,a.shipname,b.created_at,a.goodsnature,a.stockin_sysno from ".DB_PREFIX."doc_goods_record_log a 
            left join ".DB_PREFIX."doc_stock_in b on b.sysno=a.stockin_sysno 
            where {$where} GROUP BY a.customer_sysno,a.goods_sysno,a.goodsnature,a.stockin_sysno ORDER BY a.updated_at desc";
            $allData = $this->dbh->select($sql);
            $sql = "SELECT firstfrom_sysno,created_at from ".DB_PREFIX."storage_stock GROUP BY firstfrom_sysno ";
            $created_at = $this->dbh->select($sql);
            if(!empty($allData)){
                foreach ($allData as $k=>$v){
                    foreach ($created_at as $item){
                        if($v['stockin_sysno']==$item['firstfrom_sysno']){
                            $allData[$k]['created_at'] = $item['created_at'];
                            break;
                        }
                    }
                }
            }
//            var_dump($allData);die();
            if($allData){
                //商检量
                $storage_array = "(1,2,5,11,13,16,19,20)";
                $storage_array = " AND a.doc_type in ".$storage_array;
                $sql = "SELECT a.customername,a.customer_sysno,a.goods_sysno,a.goodsname,a.shipname,abs(sum(a.beqty)) as storagestock,b.created_at,a.goodsnature,a.stockin_sysno from ".DB_PREFIX."doc_goods_record_log a 
                left join ".DB_PREFIX."doc_stock_in b on b.sysno=a.stockin_sysno 
                where {$where} {$storage_array} GROUP BY a.customer_sysno,a.goods_sysno,a.goodsnature,a.stockin_sysno ORDER BY a.updated_at desc";
                $storagestock = $this->dbh->select($sql);

//                foreach ($storagestock as $k=>$v){
//                    $sql = "SELECT created_at from ".DB_PREFIX."storage_stock where firstfrom_sysno=".$v['stockin_sysno'];
//                    $created_at = $this->dbh->select($sql);
//                    $storagestock[$k]['created_at'] = $created_at[0]['created_at'];
//                }
                //出库量
                $outstock_array = "(3,4,12,14,17,20,23,26)";
                $outstock_array = " AND a.doc_type in ".$outstock_array;
                $sql = "SELECT a.customername,a.customer_sysno,a.goods_sysno,a.goodsname,a.shipname,abs(sum(a.beqty)) as outqty,b.created_at,a.goodsnature,a.stockin_sysno from ".DB_PREFIX."doc_goods_record_log a 
                left join ".DB_PREFIX."doc_stock_in b on b.sysno=a.stockin_sysno 
                where {$where} {$outstock_array} GROUP BY a.customer_sysno,a.goods_sysno,a.goodsnature,a.stockin_sysno ORDER BY a.updated_at desc";
                $outstock = $this->dbh->select($sql);

//                var_dump($outstock);die();
                //货转量
                $transfer_array = "(6)";
                $transfer_array = " AND a.doc_type in ".$transfer_array;
                $sql = "SELECT a.customername,a.customer_sysno,a.goods_sysno,a.goodsname,a.shipname,abs(sum(a.beqty)) as inqty,b.created_at,a.goodsnature,a.stockin_sysno from ".DB_PREFIX."doc_goods_record_log a 
                left join ".DB_PREFIX."doc_stock_in b on b.sysno=a.stockin_sysno 
                where {$where} {$transfer_array} GROUP BY a.customer_sysno,a.goods_sysno,a.goodsnature,a.stockin_sysno ORDER BY a.updated_at desc";
                $instock = $this->dbh->select($sql);
                //损耗量
                $sql = "SELECT a.customername,a.customer_sysno,a.goods_sysno,a.goodsname,a.shipname,abs(sum(a.ullage)) as ullage,b.created_at,a.goodsnature,a.stockin_sysno from ".DB_PREFIX."doc_goods_record_log a 
                left join ".DB_PREFIX."doc_stock_in b on b.sysno=a.stockin_sysno 
                where {$where} GROUP BY a.customer_sysno,a.goods_sysno,a.goodsnature,a.stockin_sysno ORDER BY a.updated_at desc";
                $ullage = $this->dbh->select($sql);
//                //退货
//                $returngood_array = "(26)";
//                $returngood_array = " AND a.doc_type in ".$returngood_array;
//                $sql = "SELECT a.customername,a.customer_sysno,a.goods_sysno,a.goodsname,a.shipname,abs(sum(a.beqty)) as returngood,b.created_at,a.goodsnature,a.stockin_sysno from ".DB_PREFIX."doc_goods_record_log a
//                left join ".DB_PREFIX."doc_stock_in b on b.sysno=a.stockin_sysno
//                where {$where} {$returngood_array} GROUP BY a.customer_sysno,a.goods_sysno,a.goodsnature,a.stockin_sysno ORDER BY a.updated_at desc";
//                $returngood = $this->dbh->select($sql);
                foreach ($allData as $key => $value) {
                    foreach ($storagestock as $evalue) {
                        if($value['stockin_sysno'] == $evalue['stockin_sysno'] && $value['goodsnature'] == $evalue['goodsnature'] && $value['customer_sysno'] == $evalue['customer_sysno'] && $value['goods_sysno'] == $evalue['goods_sysno']){
                            $allData[$key]['storagestock'] = $evalue['storagestock'];
                        }
                    }
                    foreach ($outstock as $ovalue) {
                        if($value['stockin_sysno'] == $ovalue['stockin_sysno'] && $value['goodsnature'] == $ovalue['goodsnature'] && $value['customer_sysno'] == $ovalue['customer_sysno'] && $value['goods_sysno'] == $ovalue['goods_sysno']){
                            $allData[$key]['outqty'] = $ovalue['outqty'];
                        }
                    }
                    foreach ($instock as $ivalue) {
                        if($value['stockin_sysno'] == $ivalue['stockin_sysno'] && $value['goodsnature'] == $ivalue['goodsnature'] && $value['customer_sysno'] == $ivalue['customer_sysno'] && $value['goods_sysno'] == $ivalue['goods_sysno']){
                            $allData[$key]['inqty'] = $ivalue['inqty'];
                        }
                    }
                    foreach ($ullage as $uvalue) {
                        if($value['stockin_sysno'] == $uvalue['stockin_sysno'] && $value['goodsnature'] == $uvalue['goodsnature'] && $value['customer_sysno'] == $uvalue['customer_sysno'] && $value['goods_sysno'] == $uvalue['goods_sysno']){
                            $allData[$key]['ullage'] = $uvalue['ullage'];
                        }
                    }
//                    foreach ($returngood as $gvalue) {
//                        if($value['stockin_sysno'] == $gvalue['stockin_sysno'] && $value['goodsnature'] == $gvalue['goodsnature'] && $value['customer_sysno'] == $gvalue['customer_sysno'] && $value['goods_sysno'] == $gvalue['goods_sysno']){
//                            $allData[$key]['returngood'] = '-'.$gvalue['returngood'];
//                        }
//                    }
                }
                foreach ($allData as $key => $value) {
                    if(!isset($value['storagestock'])){
                        $allData[$key]['storagestock'] = 0;
                    }
                    if(!isset($value['outqty'])){
                        $allData[$key]['outqty'] = 0;
                    }
                    if(!isset($value['inqty'])){
                        $allData[$key]['inqty'] = 0;
                    }
                    if(!isset($value['ullage'])){
                        $allData[$key]['ullage'] = 0;
                    }
//                    if(!isset($value['returngood'])){
//                        $allData[$key]['returngood'] = 0;
//                    }
                    $allData[$key]['endstock'] = ($allData[$key]['storagestock']*1000 - $allData[$key]['inqty']*1000 - $allData[$key]['outqty']*1000 - $allData[$key]['ullage']*1000)/1000;
                }
                foreach ($allData as $key => $value) {
                    $value['created_at'] = date("Y-m-d",strtotime($value['created_at']));
                    unset($value['customername']);
                    unset($value['shipname']);
                    $allData[$key]['info'] = json_encode($value);
                }
                if(isset($params['page'])&&$params['page']==false){
                    $result['list'] = $allData;
                }else{
                    $result['totalRow'] = count($allData);
                    $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
                    $datalist=array_chunk($allData,$params['pageSize'],false);
                    $result['list']= $datalist [$params['pageCurrent']-1] ? $datalist [$params['pageCurrent']-1] : [];
                }
            }
        }
        return $result;
    }

    //通过客户、品名、货物性质、时间来获取入库、货转、出库、提单信息
    public function getDetailData($params){
        $filter = array();
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " a.goods_sysno = '{$params['goods_sysno']}' ";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " a.customer_sysno = '{$params['customer_sysno']}'";
        }
        if (isset($params['goodsnature']) && $params['goodsnature'] != '') {
            $filter[] = " a.goodsnature = '{$params['goodsnature']}'";
        }
        if (isset($params['star_time']) && $params['star_time'] != '') {
            $filter[] = " b.created_at >='{$params['star_time']}"." 00:00:00'";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " b.created_at <='{$params['end_time']}"." 23:59:59'";
        }
        if (isset($params['stockin_sysno']) && $params['stockin_sysno'] != '') {
            $filter[] = "a.stockin_sysno = '{$params['stockin_sysno']}'";
        }
//        if (isset($params['date']) && $params['date'] != '') {
//            $filter[] = " DATE_FORMAT(doc_time,'%Y-%c-%d') = str_to_date('{$params['date']}','%Y-%c-%d') ";
//        }
        $where ='a.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $result = [];
        //入库记录
        $sql = "SELECT a.doc_type,UNIX_TIMESTAMP(a.doc_time) as doc_time,a.docno,a.goodsname,a.takegoodsno,a.shipname,sum(a.beqty) as inqty,a.ullage,b.created_at,a.goodsnature  
        from ".DB_PREFIX."doc_goods_record_log a
        left join ".DB_PREFIX."doc_stock_in b on b.sysno=a.stockin_sysno 
        where {$where} and a.beqty>0 and a.doc_type not in (15,7,8,26)  group by a.doc_sysno";
        $stockInData = $this->dbh->select($sql);

        //出库记录
        $sql = "SELECT a.doc_type,UNIX_TIMESTAMP(a.doc_time) as doc_time,a.docno,a.goodsname,a.takegoodsno,a.shipname,abs(sum(a.beqty)) as outqty,a.created_at,a.goodsnature  
        from ".DB_PREFIX."doc_goods_record_log a 
        left join ".DB_PREFIX."doc_stock_in b on b.sysno=a.stockin_sysno 
        where {$where} and a.beqty<0 and a.doc_type not in (15,7,8) and a.doc_type in (3,4,12,14,17,23) group by a.doc_sysno";
        $stockOutData = $this->dbh->select($sql);
        //退货
        $sql = "SELECT a.doc_type,UNIX_TIMESTAMP(a.doc_time) as doc_time,a.docno,a.goodsname,a.takegoodsno,a.shipname,(-sum(a.beqty)) as outqty,a.created_at,a.goodsnature  
        from ".DB_PREFIX."doc_goods_record_log a 
        left join ".DB_PREFIX."doc_stock_in b on b.sysno=a.stockin_sysno 
        where {$where} and a.doc_type not in (15,7,8) and a.doc_type in (26) group by a.doc_sysno";
        $returnGoodData = $this->dbh->select($sql);
        $stockOutData = array_merge($stockOutData,$returnGoodData);
//        var_dump($stockOutData);die();
        //货转记录
        $sql = "SELECT a.doc_type,UNIX_TIMESTAMP(a.doc_time) as doc_time,a.docno,a.goodsname,a.takegoodsno,a.shipname,sum(abs(a.beqty)) as tranqty,a.created_at,a.goodsnature  
        from ".DB_PREFIX."doc_goods_record_log a 
        left join ".DB_PREFIX."doc_stock_in b on b.sysno=a.stockin_sysno 
        where {$where} and a.beqty<0 and a.doc_type not in (15,7,8) and a.doc_type in (6) group by a.doc_sysno";
        $tranqtyData = $this->dbh->select($sql);
//var_dump($stockOutData);die();
        if(!$stockInData){
            return [];
        }
//        var_dump($stockInData);die();
        if($stockOutData){
            $result = array_merge($stockInData,$stockOutData);
        }else if($tranqtyData){
            $result = array_merge($stockInData,$tranqtyData);
        }else{
            $result = $stockInData;
        }

        //按单据时间排序
        $arrSort = array();  
        foreach($result AS $uniqid => $row){  
            foreach($row AS $key=>$value){  
                $arrSort[$key][$uniqid] = $value;  
            }  
        } 
        array_multisort($arrSort['doc_time'], SORT_ASC, $result);  

//        $endingstock = $params['endingstocks'];
//        foreach ($result as $key => $value) {
//            if($value['doc_type'] == 6){
//                $result[$key]['tranqty'] = $value['outqty'];
//                $result[$key]['outqty'] = '--';
//            }
//
//            if(isset($value['inqty'])){
//                $endingstock = $endingstock + $value['inqty'] - $value['ullage'];
//            }
//
//            if(isset($value['outqty'])){
//                $endingstock = $endingstock - $value['outqty'];
//            }
//            $result[$key]['endingstock'] = $endingstock;
//
//        }

        //超期损耗
        $sql = "SELECT a.ullage,a.created_at,a.goodsname from ".DB_PREFIX."doc_goods_record_log a 
         left join ".DB_PREFIX."doc_stock_in b on b.sysno=a.stockin_sysno 
         where {$where} and a.doc_type = 15 ";
        $ullage = $this->dbh->select($sql);
        if(!empty($ullage)){
            foreach($ullage as $key=>$item){
                $ullage[$key]['created_at'] = date('Y-m-d',strtotime('-1 days',strtotime($item['created_at'])));
            }
        }
        if(count($ullage) > 0){
            $result = array_merge($result,$ullage);
        }
        $totalArr = array(
            'created_at' => '合计',
            'outqty' => 0,
            'inqty' => 0,
            'tranqty' => 0,
            'ullage' => 0,
            'percent' => 0,
            'stock' => 0
        );
        foreach ($result as $k=>$v){
            if ($v['inqty']){
                $result[$k]['stock'] = $v['inqty']-$v['ullage'];
            }
//            var_dump(floatval($v['ullage']));echo '...';var_dump(floatval($v['inqty']));die();
            $ullage11 = floatval($v['ullage']);
            $inqty11 = floatval($v['inqty']);
            if($inqty11 != 0){
                $a = $ullage11/$inqty11;
                if ($a>0){
                    $b = sprintf("%.3f",$a);
                    if($b == '0.000'){
                        $result[$k]['percent'] = 0.001;
                    }else{
                        $result[$k]['percent'] = sprintf("%.3f",$a);
                    }
                }else{
                    $result[$k]['percent'] =  $a;
                }

            }

        }
        foreach ($result as $key => $value) {
            $totalArr['outqty'] +=  floatval($value['outqty']);
            $totalArr['inqty'] +=  floatval($value['inqty']);
            $totalArr['tranqty'] +=  floatval($value['tranqty']);
            $totalArr['ullage'] +=  floatval($value['ullage']);
            if ($value['stock']){
                $totalArr['stock'] += floatval($value['stock']);
            }
            if ($value['percent']){
                $totalArr['percent'] += floatval($value['percent']);
            }
            if(!isset($value['doc_type']) || !$value['doc_type']){
                $result[$key]['doc_type'] = '--';
            }

            if(!isset($value['takegoodsno']) || !$value['takegoodsno']){
                $result[$key]['takegoodsno'] = '--';
            }

            if(!isset($value['shipname']) || !$value['shipname']){
                $result[$key]['shipname'] = '--';
            }

            if(!isset($value['outqty']) || !$value['outqty']){
                $result[$key]['outqty'] = '--';
            }

            if(!isset($value['inqty']) || !$value['inqty']){
                $result[$key]['inqty'] = '--';
            }

            if(!isset($value['tranqty']) || !$value['tranqty']){
                $result[$key]['tranqty'] = '--';
            }

            if(!isset($value['ullage']) || !$value['ullage']){
                $result[$key]['ullage'] = '--';
            }

            if(!isset($value['takegoodscompany']) || !$value['takegoodscompany']){
                $result[$key]['takegoodscompany'] = '--';
            }
//            if (!isset($value['stock']) || !$value['stock'] ){
//                $totalArr['stock'] = '--';
//            }
//            if (!isset($value['percent']) || !$value['percent'] ){
//                $totalArr['percent'] = '--';
//            }
        }
        array_push($result,$totalArr);

        return $result;
    }
}
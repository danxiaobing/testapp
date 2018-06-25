<?php
/**
 * Stockout Model
 *
 */

class Report_CustomerintradayModel
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
            $filter[] = " goods_sysno = '{$params['goods_sysno']}' ";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " customer_sysno = '{$params['customer_sysno']}'";
        }
        if (isset($params['goodsnature']) && $params['goodsnature'] != '') {
            $filter[] = " goodsnature = '{$params['goodsnature']}'";
        }

        $where ='isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $result = $params;
        $result['list'] = array();
        if (isset($params['bar_date']) && $params['bar_date'] != '') {
            $where1 = ' AND ' . " DATE_FORMAT(doc_time,'%Y-%c-%d') <= str_to_date('{$params['bar_date']}','%Y-%c-%d') ";
        }else{
            $where1 = ' AND ' . " DATE_FORMAT(doc_time,'%Y-%c-%d') <= CURDATE() ";
        }
        $sql = "SELECT count(sysno) from ".DB_PREFIX."doc_goods_record_log where {$where} {$where1} GROUP BY customer_sysno,goods_sysno,goodsnature ORDER BY doc_time desc";
        $result['totalRow'] = $this->dbh->select_one($sql);

        if ($result['totalRow']) {
            if (isset($params['bar_date']) && $params['bar_date'] != '') {
                $where1 = ' AND ' . " DATE_FORMAT(doc_time,'%Y-%c-%d') = str_to_date('{$params['bar_date']}','%Y-%c-%d') ";
            }else{
                $where1 = ' AND ' . " DATE_FORMAT(doc_time,'%Y-%c-%d') = CURDATE() ";
            }
            //有记录的客户
            $sql = "SELECT sysno,customername,customer_sysno,goods_sysno,goodsname,goodsnature from ".DB_PREFIX."doc_goods_record_log where {$where} {$where1} GROUP BY customer_sysno,goods_sysno,goodsnature ORDER BY doc_time desc";
            $allData = $this->dbh->select($sql);

            if($allData){
                //昨日结存量
                if (isset($params['bar_date']) && $params['bar_date'] != '') {
                    $where1 = ' AND ' . " DATE_FORMAT(doc_time,'%Y-%c-%d') < str_to_date('{$params['bar_date']}','%Y-%c-%d') ";
                }else{
                    $where1 = ' AND ' . " DATE_FORMAT(doc_time,'%Y-%c-%d') < CURDATE() ";
                }
                $sql = "SELECT customername,customer_sysno,goods_sysno,goodsname,goodsnature,(sum(beqty)-sum(ullage)) as endingstocks from ".DB_PREFIX."doc_goods_record_log where {$where} {$where1} GROUP BY customer_sysno,goods_sysno,goodsnature ORDER BY doc_time desc";
                $endingstock = $this->dbh->select($sql);
                //今日出库
                if (isset($params['bar_date']) && $params['bar_date'] != '') {
                    $where1 = ' AND ' . " DATE_FORMAT(doc_time,'%Y-%c-%d') = str_to_date('{$params['bar_date']}','%Y-%c-%d') ";
                }else{
                    $where1 = ' AND ' . " DATE_FORMAT(doc_time,'%Y-%c-%d') =  CURDATE() ";
                }
                $sql = "SELECT customername,customer_sysno,goods_sysno,goodsname,goodsnature,sum(abs(beqty)) as outqty from ".DB_PREFIX."doc_goods_record_log where {$where} {$where1} and beqty<0 and doc_type not in(8,20,22)  GROUP BY customer_sysno,goods_sysno,goodsnature";
                $outstock = $this->dbh->select($sql);
                //今日入库
                if (isset($params['bar_date']) && $params['bar_date'] != '') {
                    $where1 = ' AND ' . " DATE_FORMAT(doc_time,'%Y-%c-%d') = str_to_date('{$params['bar_date']}','%Y-%c-%d') ";
                }else{
                    $where1 = ' AND ' . " DATE_FORMAT(doc_time,'%Y-%c-%d') =  CURDATE() ";
                }
                $sql = "SELECT customername,customer_sysno,goods_sysno,goodsname,goodsnature,sum(beqty) as inqty from ".DB_PREFIX."doc_goods_record_log where {$where} {$where1} and beqty>=0 and doc_type not in (7,21) or doc_type = 20 GROUP BY customer_sysno,goods_sysno,goodsnature";
                $instock = $this->dbh->select($sql);
                //今日损耗
                if (isset($params['bar_date']) && $params['bar_date'] != '') {
                    $where1 = ' AND ' . " DATE_FORMAT(doc_time,'%Y-%c-%d') = str_to_date('{$params['bar_date']}','%Y-%c-%d') ";
                }else{
                    $where1 = ' AND ' . " DATE_FORMAT(doc_time,'%Y-%c-%d') =  CURDATE() ";
                }
                $sql = "SELECT customername,customer_sysno,goods_sysno,goodsname,goodsnature,sum(ullage) as ullage from ".DB_PREFIX."doc_goods_record_log where {$where} {$where1} GROUP BY customer_sysno,goods_sysno,goodsnature";
                $ullage = $this->dbh->select($sql);
                foreach ($allData as $key => $value) {
                    foreach ($endingstock as $evalue) {
                        if($value['customer_sysno'] == $evalue['customer_sysno'] && $value['goods_sysno'] == $evalue['goods_sysno'] && $value['goodsnature'] == $evalue['goodsnature']){
                            $allData[$key]['endingstocks'] = $evalue['endingstocks'];
                        }
                    }
                    foreach ($outstock as $ovalue) {
                        if($value['customer_sysno'] == $ovalue['customer_sysno'] && $value['goods_sysno'] == $ovalue['goods_sysno'] && $value['goodsnature'] == $ovalue['goodsnature']){
                            $allData[$key]['outqty'] = $ovalue['outqty'];
                        }
                    }
                    foreach ($instock as $ivalue) {
                        if($value['customer_sysno'] == $ivalue['customer_sysno'] && $value['goods_sysno'] == $ivalue['goods_sysno'] && $value['goodsnature'] == $ivalue['goodsnature']){
                            $allData[$key]['inqty'] = $ivalue['inqty'];
                        }
                    }
                    foreach ($ullage as $uvalue) {
                        if($value['customer_sysno'] == $uvalue['customer_sysno'] && $value['goods_sysno'] == $uvalue['goods_sysno'] && $value['goodsnature'] == $uvalue['goodsnature']){
                            $allData[$key]['ullage'] = $uvalue['ullage'];
                        }
                    }
                }

                foreach ($allData as $key => $value) {
                    if(!isset($value['endingstocks'])){
                        $allData[$key]['endingstocks'] = 0;
                    }
                    if(!isset($value['outqty'])){
                        $allData[$key]['outqty'] = 0;
                    }
                    if(!isset($value['inqty'])){
                        $allData[$key]['inqty'] = 0;
                    }
                    if(!isset($value['ullage'])){
                        $allData[$key]['ullage'] = 0;
                    };
                    $allData[$key]['endstock'] = round(floatval($allData[$key]['endingstocks']) + floatval($allData[$key]['inqty']) - floatval($allData[$key]['outqty']) - floatval($allData[$key]['ullage']),3);
                }

                foreach ($allData as $key => $value) {
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
            $filter[] = " goods_sysno = '{$params['goods_sysno']}' ";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " customer_sysno = '{$params['customer_sysno']}'";
        }
        if (isset($params['goodsnature']) && $params['goodsnature'] != '') {
            $filter[] = " goodsnature = '{$params['goodsnature']}'";
        }
        if (isset($params['date']) && $params['date'] != '') {
            $filter[] = " DATE_FORMAT(doc_time,'%Y-%c-%d') = str_to_date('{$params['date']}','%Y-%c-%d') ";
        }
        $where ='isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $result = [];
        //入库记录
        $sql = "SELECT doc_type,UNIX_TIMESTAMP(doc_time) as doc_time,docno,takegoodsno,sum(beqty) as inqty,ullage,CASE doc_type when 1 then shipname when 2 then '槽车' when 11 then '管输' else '--' END as shipname from ".DB_PREFIX."doc_goods_record_log where {$where} and beqty>0 and doc_type not in(7,15,18,21,26) group by doc_sysno";
        $stockInData = $this->dbh->select($sql);

        //出库记录
        $sql = "SELECT doc_type,UNIX_TIMESTAMP(doc_time) as doc_time,docno,takegoodsno,CASE doc_type when 3 then shipname when 4 then '槽车' when 12 then '管输' else '--' END as shipname,sum(abs(beqty)) as outqty,ullage from ".DB_PREFIX."doc_goods_record_log where {$where} and beqty<0 and doc_type not in (8,15,22) group by doc_sysno";
        $stockOutData = $this->dbh->select($sql);
        //超期损耗
        $sql = "SELECT doc_type,UNIX_TIMESTAMP(doc_time) as doc_time,docno,takegoodsno,'--' as shipname,'--' as outqty,ullage  from ".DB_PREFIX."doc_goods_record_log where {$where} and doc_type in (15,18) ";
        $ullage = $this->dbh->select($sql);

        //退货记录
        $sql = "SELECT doc_type,UNIX_TIMESTAMP(doc_time) as doc_time,docno,takegoodsno,sum(beqty) as rebackqty ,'--' as shipname from ".DB_PREFIX."doc_goods_record_log where {$where} and beqty>0 and doc_type = 26 group by doc_sysno";
        $rebackData = $this->dbh->select($sql);

        if(!$stockInData && !$stockOutData && !$ullage && !$rebackData){
            return [];
        }
        if($stockInData && $stockOutData){
            $result = array_merge($stockInData,$stockOutData);
        }elseif($stockInData && !$stockOutData){
            $result = $stockInData;
        }elseif(!$stockInData && $stockOutData){
            $result = $stockOutData;
        }

        if($rebackData){
            $result = array_merge($result,$rebackData);
        }

        if(count($ullage) > 0){
            $result = array_merge($result,$ullage);
        }
        //按单据时间排序
        $arrSort = array();
        foreach($result AS $uniqid => $row){
            foreach($row AS $key=>$value){
                $arrSort[$key][$uniqid] = $value;
            }
        }
        array_multisort($arrSort['doc_time'], SORT_ASC, $result);

        $endingstock = $params['endingstocks'];
        foreach ($result as $key => $value) {
            if($value['doc_type'] == 6){
                $result[$key]['tranqty'] = $value['outqty'];
                $result[$key]['outqty'] = '--';
            }

            if(isset($value['inqty'])){
                $endingstock = $endingstock + $value['inqty'];
            }

            if(isset($value['outqty'])){
                $endingstock = $endingstock - $value['outqty'];
            }

            if(isset($value['ullage'])){
                $endingstock = $endingstock - $value['ullage'];
            }

            if(isset($value['rebackqty'])){
                $endingstock = $endingstock + $value['rebackqty'];
            }

            if($value['doc_type'] == 20){
                $result[$key]['inqty'] = -$value['outqty'];
                $result[$key]['outqty'] = '--';
            }

            $result[$key]['endingstock'] = $endingstock;
        }


        $totalArr = array(
            'doc_type' => '合计',
            'outqty' => 0,
            'inqty' => 0,
            'tranqty' => 0,
            'ullage' => 0,
            'rebackqty' => 0,
            );
        foreach ($result as $key => $value) {
            $totalArr['outqty'] +=  floatval($value['outqty']);
            $totalArr['inqty'] +=  floatval($value['inqty']);
            $totalArr['tranqty'] +=  floatval($value['tranqty']);
            $totalArr['ullage'] +=  floatval($value['ullage']);
            $totalArr['rebackqty'] +=  floatval($value['rebackqty']);
            if(!isset($value['takegoodsno']) || !$value['takegoodsno']){
                $result[$key]['takegoodsno'] = '--';
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

            if(!isset($value['takegoodscompany'])){
                $result[$key]['takegoodscompany'] = '--';
            }
        }

        array_push($result,$totalArr);
        return $result;
    }
}
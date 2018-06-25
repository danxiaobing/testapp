<?php
/**
 * Stockout Model
 *
 */

class Report_StoragetankintradayModel
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
            $filter[] = " dgrl.goods_sysno = '{$params['goods_sysno']}' ";
        }
        if (isset($params['storagetank_sysno']) && $params['storagetank_sysno'] != '') {
            $filter[] = " dgrl.storagetank_sysno = '{$params['storagetank_sysno']}'";
        }
        if (isset($params['bar_date']) && $params['bar_date'] != '') {
            $filter[] = " DATE_FORMAT(doc_time,'%Y-%c-%d') <= str_to_date('{$params['bar_date']}','%Y-%c-%d') ";
        }else{
            $filter[] = " DATE_FORMAT(doc_time,'%Y-%c-%d') <= CURDATE() ";
        }
 
        $where ='dgrl.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $result = $params;
        $result['list'] = array();
        
        $sql = "SELECT count(sysno) from ".DB_PREFIX."doc_goods_record_log dgrl where {$where} GROUP BY storagetank_sysno,goods_sysno ORDER BY doc_time desc";
        $result['totalRow'] = $this->dbh->select_one($sql);

        if ($result['totalRow']) {
            $sql = "SELECT dgrl.sysno,dgrl.storagetank_sysno,dgrl.storagetankname,bs.goods_sysno,bg.goodsname,bs.storagetanknature from ".DB_PREFIX."doc_goods_record_log dgrl left join ".DB_PREFIX."base_storagetank bs on dgrl.storagetank_sysno = bs.sysno left join ".DB_PREFIX."base_goods bg on bs.goods_sysno = bg.sysno where {$where} GROUP BY storagetank_sysno ORDER BY doc_time desc";
            $allData = $this->dbh->select($sql);
            if($allData){
                //结存量
                $sql = "SELECT storagetank_sysno,storagetankname,goods_sysno,goodsname,(sum(beqty)-sum(ullage)) as endstock from ".DB_PREFIX."doc_goods_record_log dgrl where {$where} GROUP BY storagetank_sysno";
                $endingstock = $this->dbh->select($sql);

                foreach ($allData as $key => $value) {
                    foreach ($endingstock as $evalue) {
                        if($value['storagetank_sysno'] == $evalue['storagetank_sysno']){
                            $allData[$key]['endstock'] = $evalue['endstock'];
                        }
                    }
                }

                foreach ($allData as $key => $value) {
                    if(!isset($value['endingstocks'])){
                        $allData[$key]['endingstocks'] = 0;
                    }
                    if(!isset($value['ullage'])){
                        $allData[$key]['ullage'] = 0;
                    }
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


    public function getDetailData($params){
        $filter = array();
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " goods_sysno = '{$params['goods_sysno']}' ";
        }
        if (isset($params['storagetank_sysno']) && $params['storagetank_sysno'] != '') {
            $filter[] = " storagetank_sysno = '{$params['storagetank_sysno']}'";
        }
        if (isset($params['bar_date']) && $params['bar_date'] != '') {
            $filter[] = " DATE_FORMAT(doc_time,'%Y-%c-%d') <= str_to_date('{$params['bar_date']}','%Y-%c-%d') ";
        }
        $where ='isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $result = [];
        //入库记录
        $sql = "SELECT customername,DATE_FORMAT(doc_time,'%Y-%c-%d') as doc_time,CASE doc_type when 1 then shipname when 2 then '槽车' when 11 then '管输' else '--' END as shipname,(sum(beqty)-sum(ullage)) as endingstock,ullage,'吨' as unit from ".DB_PREFIX."doc_goods_record_log where {$where} group by customer_sysno,stockin_sysno";
        $result = $this->dbh->select($sql);

        if(!$result){
            return [];
        }

        $totalArr = array(
            'customername' => '合计',
            'endingstock' => 0,

            );
        foreach ($result as $key => $value) {
            $totalArr['endingstock'] +=  floatval($value['endingstock']);
        }
        array_push($result,$totalArr);
        return $result;
    }
}
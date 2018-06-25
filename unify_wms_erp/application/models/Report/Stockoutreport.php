<?php

class Report_StockoutreportModel
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

    public function search($params)
    {
        $filter = array();
        if (isset($params['daterange']) && $params['daterange'] != '') {
            $filter[] = " year(sd.`updated_at`) = '{$params['daterange']}'";
        }

        $where = '1';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $result['list'] = array(
            array('month' => 1),          
            array('month' => 2),
            array('month' => 3),
            array('month' => 4),
            array('month' => 5),
            array('month' => 6),
            array('month' => 7),
            array('month' => 8),
            array('month' => 9),
            array('month' => 10),
            array('month' => 11),
            array('month' => 12),
             );
        //出库
        $sql = "SELECT  month(sd.updated_at) as month,if(sum(sd.beqty) ,truncate(sum(sd.beqty),2),0) as beqty ,if(sum(sd.bussinesscheckqty),truncate(sum(sd.bussinesscheckqty),2),0 ) as bussinesscheckqty  FROM `".DB_PREFIX."doc_stock_out` s LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` sd ON s.sysno = sd.stockout_sysno  where {$where} and s.stockouttype != 4 and s.stockoutstatus = 4 and s.isdel=0 group by extract(year_month from sd.updated_at)";
        $stockoutData = $this->dbh->select($sql);
        foreach ($result['list'] as $key => $value) {
            foreach ($stockoutData as $k => $v) {
                if($v['month'] == $value['month']){
                    $result['list'][$key] = $v;
                    break;
                }
            }
        }

        //船数 外贸
        $sql = "SELECT  month(sd.updated_at) as month ,count(sd.shipname) as shipnum  FROM `".DB_PREFIX."doc_stock_out` s LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` sd ON s.sysno = sd.stockout_sysno  where {$where} and s.stockoutstatus = 4 and s.isdel=0 and sd.goodsnature !=4 group by extract(year_month from sd.updated_at)";
        $shipnum1 = $this->dbh->select($sql);
        //船数 内贸
        $sql = "SELECT  month(sd.updated_at) as month ,count(sd.shipname) as shipnum  FROM `".DB_PREFIX."doc_stock_out` s LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` sd ON s.sysno = sd.stockout_sysno  where {$where} and s.stockoutstatus = 4 and s.isdel=0 and sd.goodsnature =4 group by extract(year_month from sd.updated_at)";
        $shipnum2 = $this->dbh->select($sql);
        //船出库
        $sql = "SELECT month(sd.updated_at) as month ,if(sum(sd.beqty) ,truncate(sum(sd.beqty),2),0) as beqty from ".DB_PREFIX."doc_stock_out s LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sd ON s.sysno = sd.stockout_sysno  where {$where} and s.stockoutstatus = 4 and s.isdel=0 and s.stockouttype =1 group by extract(year_month from sd.updated_at)";
        $stockout = $this->dbh->select($sql);
        //船出库 外贸
        $sql = "SELECT month(sd.updated_at) as month ,if(sum(sd.beqty) ,truncate(sum(sd.beqty),2),0) as beqty from ".DB_PREFIX."doc_stock_out s LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sd ON s.sysno = sd.stockout_sysno  where {$where} and s.stockoutstatus = 4 and s.isdel=0 and s.stockouttype =1 and sd.goodsnature !=4 group by extract(year_month from sd.updated_at)";
        $stockout1 = $this->dbh->select($sql);
        //船出库 内贸
        $sql = "SELECT month(sd.updated_at) as month ,if(sum(sd.beqty) ,truncate(sum(sd.beqty),2),0) as beqty from ".DB_PREFIX."doc_stock_out s LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sd ON s.sysno = sd.stockout_sysno  where {$where} and s.stockoutstatus = 4 and s.isdel=0 and s.stockouttype =1 and sd.goodsnature =4 group by extract(year_month from sd.updated_at)";
        $stockout2 = $this->dbh->select($sql);

        //槽车出库
        $sql = "SELECT  month(sd.updated_at) as month,if(sum(realnumber) ,truncate(sum(realnumber)/1000,2),0) as beqty FROM `".DB_PREFIX."doc_pounds_out` sd left join `".DB_PREFIX."doc_pounds_out_detail` pod on sd.sysno = pod.pounds_out_sysno where {$where} and cartype = 1 and sd.poundsoutstatus = 4 and sd.isdel = 0 group by extract(year_month from sd.updated_at)";
        $poundsout = $this->dbh->select($sql);

        //出库车辆、桶数
        $sql = "SELECT  month(updated_at) as month,count(carid) as carnum ,sum(bucketnumber) as bucketnumber FROM `".DB_PREFIX."doc_pounds_out` sd where {$where} and poundsoutstatus = 4 and isdel = 0 group by extract(year_month from updated_at)";
        $poundsoutData = $this->dbh->select($sql);
        //管出总量
        $sql = "SELECT month(sd.updated_at) as month ,if(sum(sd.beqty) ,truncate(sum(sd.beqty),2),0) as pipelineoutqty from ".DB_PREFIX."doc_stock_out s LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sd ON s.sysno = sd.stockout_sysno  where {$where} and s.stockoutstatus = 4 and s.isdel=0 and s.stockouttype =3 group by extract(year_month from sd.updated_at)";
        $pipelineoutData = $this->dbh->select($sql);
        //管出外贸
        $sql = "SELECT month(sd.updated_at) as month ,if(sum(sd.beqty) ,truncate(sum(sd.beqty),2),0) as pipelineoutqty from ".DB_PREFIX."doc_stock_out s LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sd ON s.sysno = sd.stockout_sysno  where {$where} and s.stockoutstatus = 4 and s.isdel=0 and s.stockouttype =3 and sd.goodsnature !=4 group by extract(year_month from sd.updated_at)";
        $pipelineoutData1 = $this->dbh->select($sql);
        //管出内贸
        $sql = "SELECT month(sd.updated_at) as month ,if(sum(sd.beqty) ,truncate(sum(sd.beqty),2),0) as pipelineoutqty from ".DB_PREFIX."doc_stock_out s LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sd ON s.sysno = sd.stockout_sysno  where {$where} and s.stockoutstatus = 4 and s.isdel=0 and s.stockouttype =3 and sd.goodsnature =4 group by extract(year_month from sd.updated_at)";
        $pipelineoutData2 = $this->dbh->select($sql);

        foreach ($result['list'] as $key => $value) {
            foreach ($shipnum1 as $sv1) {
                if($sv1['month'] == $value['month']){
                    $result['list'][$key]['shipnum1'] = $sv1['shipnum'];
                }
            }

            foreach ($shipnum2 as $sv2) {
                if($sv1['month'] == $value['month']){
                    $result['list'][$key]['shipnum2'] = $sv2['shipnum'];
                }
            }

            foreach ($stockout as $sov) {
                if($sov['month'] == $value['month']){
                    $result['list'][$key]['shipstockoutqty'] = $sov['beqty'];
                }
            }

            foreach ($stockout1 as $sov1) {
                if($sov1['month'] == $value['month']){
                    $result['list'][$key]['shipstockoutqty1'] = $sov1['beqty'];
                }
            }

            foreach ($stockout2 as $sov2) {
                if($sov2['month'] == $value['month']){
                    $result['list'][$key]['shipstockoutqty2'] = $sov2['beqty'];
                }
            }

            foreach ($poundsout as $psv) {
                if($psv['month'] == $value['month']){
                    $result['list'][$key]['pountsoutqty'] = $psv['beqty'];
                }
            }

            foreach ($poundsoutData as $psdv) {
                if($psdv['month'] == $value['month']){
                    $result['list'][$key]['carnum'] = $psdv['carnum'];
                    $result['list'][$key]['bucketnumber'] = $psdv['bucketnumber'];
                }
            }
            foreach ($pipelineoutData as $plk => $plv) {
                if($plv['month'] == $value['month']){
                    $result['list'][$key]['pipelineoutqty'] = $plv['pipelineoutqty'];
                }
            }
            foreach ($pipelineoutData1 as $plk1 => $plv1) {
                if($plv1['month'] == $value['month']){
                    $result['list'][$key]['pipelineoutqty1'] = $plv1['pipelineoutqty'];
                }
            }
            foreach ($pipelineoutData2 as $plk2 => $plv2) {
                if($plv2['month'] == $value['month']){
                    $result['list'][$key]['pipelineoutqty2'] = $plv2['pipelineoutqty'];
                }
            }
            $result['list'][$key]['month'] = COMMON::getMonth($value['month']);
        }
        foreach ($result['list'] as $key => $value) {
            $result['list'][12]['month'] = '合计';
            $result['list'][12]['beqty'] += $value['beqty'];
            $result['list'][12]['shipstockoutqty'] += $value['shipstockoutqty'];
            $result['list'][12]['shipnum1'] += $value['shipnum1'];
            $result['list'][12]['shipnum2'] += $value['shipnum2'];
            $result['list'][12]['shipstockoutqty1'] += $value['shipstockoutqty1'];
            $result['list'][12]['shipstockoutqty2'] += $value['shipstockoutqty2'];
            $result['list'][12]['pountsoutqty'] += $value['pountsoutqty'];
            $result['list'][12]['carnum'] += $value['carnum'];
            $result['list'][12]['bucketnumber'] += $value['bucketnumber'];
            $result['list'][12]['pipelineoutqty'] += $value['pipelineoutqty'];
            $result['list'][12]['pipelineoutqty1'] += $value['pipelineoutqty1'];
            $result['list'][12]['pipelineoutqty2'] += $value['pipelineoutqty2'];
        }
                 
        // foreach ($result['list'] as $key => $value) {
        //     if(!isset($value['shipnum1'])){
        //         $result['list'][$key]['shipnum1'] = 0;
        //     }
        //     if(!isset($value['shipnum2'])){
        //         $result['list'][$key]['shipnum2'] = 0;
        //     }
        //     if(!isset($value['shipstockinqty1'])){
        //         $result['list'][$key]['shipstockinqty1'] = 0;
        //     }
        //     if(!isset($value['shipstockinqty2'])){
        //         $result['list'][$key]['shipstockinqty2'] = 0;
        //     }
        //     if(!isset($value['pountsoutqty'])){
        //         $result['list'][$key]['pountsoutqty'] = 0;
        //     }
        //     if(!isset($value['carnum'])){
        //         $result['list'][$key]['carnum'] = 0;
        //     }
        //     if(!isset($value['bucketnumber'])){
        //         $result['list'][$key]['bucketnumber'] = 0;
        //     }
        // }

        // echo "<pre>";
        // print_r($result);die();

        return $result;
    }    
}
<?php

class Report_StockindeclareModel
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
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " si.`customer_sysno` = '{$params['customer_sysno']}' ";
        }
        if (isset($params['startdate']) && $params['startdate'] != '') {
            $filter[] = " date_format(sid.`updated_at`,'%Y%m%d')>=date_format('{$params['startdate']}','%Y%m%d')";
        }
        if (isset($params['enddate']) && $params['enddate'] != '') {
            $filter[] = " date_format(sid.`updated_at`,'%Y%m%d')<=date_format('{$params['enddate']}','%Y%m%d')";
        }

        $where ='1';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        //罐子报关量，维度：storagetank_sysno,customer_sysno,goods_sysno
        $sql = "SELECT sid.storagetankname,sid.storagetank_sysno,si.customer_sysno,sid.goods_sysno,si.customername,sid.goodsname,date_format(si.updated_at,'%Y-%m-%d') as updated_at,sd.shipname,sum(sid.release_num) as release_num from ".DB_PREFIX."doc_stock_in_declare sid left join ".DB_PREFIX."doc_stock_in si on sid.stockin_sysno = si.sysno left join (select ifnull(shipname,'') as shipname ,stockin_sysno from ".DB_PREFIX."doc_stock_in_detail group by stockin_sysno) sd on sd.stockin_sysno = sid.stockin_sysno where {$where} and si.stockinstatus = 4 and si.isdel = 0 group by sid.storagetank_sysno,si.customer_sysno,sid.goods_sysno ";
        // echo $sql;die();
        $declareData = $this->dbh->select($sql);

        $filter = array();
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " `customer_sysno` = '{$params['customer_sysno']}' ";
        }
        if (isset($params['startdate']) && $params['startdate'] != '') {
            $filter[] = " date_format(si.`updated_at`,'%Y%m%d')>=date_format('{$params['startdate']}','%Y%m%d')";
        }
        if (isset($params['enddate']) && $params['enddate'] != '') {
            $filter[] = " date_format(si.`updated_at`,'%Y%m%d')<=date_format('{$params['enddate']}','%Y%m%d')";
        }

        $where ='1';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        //提单量、商检量，维度：storagetank_sysno,customer_sysno,goods_sysno
        $sql = "SELECT storagetank_sysno,customer_sysno,goods_sysno,sum(tobeqty) as tobeqty,ifnull(sum(beqty),0) as bussinesscheckqty from ".DB_PREFIX."doc_stock_in si left join ".DB_PREFIX."doc_stock_in_detail sid on si.sysno=sid.stockin_sysno where {$where} and si.stockinstatus = 4 and si.isdel = 0  group by storagetank_sysno,customer_sysno,goods_sysno";
        $stockinData = $this->dbh->select($sql);
        // echo $sql;die();
        $filter = array();
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " `customer_sysno` = '{$params['customer_sysno']}' ";
        }
        if (isset($params['startdate']) && $params['startdate'] != '') {
            $filter[] = " date_format(so.`updated_at`,'%Y%m%d')>=date_format('{$params['startdate']}','%Y%m%d')";
        }
        if (isset($params['enddate']) && $params['enddate'] != '') {
            $filter[] = " date_format(so.`updated_at`,'%Y%m%d')<=date_format('{$params['enddate']}','%Y%m%d')";
        }

        $where ='1';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        //出库量，维度：storagetank_sysno,customer_sysno,goods_sysno
        $sql = "SELECT storagetank_sysno,customer_sysno,goods_sysno,sum(beqty) as beqty from ".DB_PREFIX."doc_stock_out so left join ".DB_PREFIX."doc_stock_out_detail sod on so.sysno=sod.stockout_sysno where {$where} and so.stockoutstatus=4 and so.isdel=0 group by storagetank_sysno,customer_sysno,goods_sysno";
        $stockoutData = $this->dbh->select($sql);


        $filter = array();
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " `sale_customer_sysno` = '{$params['customer_sysno']}' ";
        }
        if (isset($params['startdate']) && $params['startdate'] != '') {
            $filter[] = " date_format(st.`updated_at`,'%Y%m%d')>=date_format('{$params['startdate']}','%Y%m%d')";
        }
        if (isset($params['enddate']) && $params['enddate'] != '') {
            $filter[] = " date_format(st.`updated_at`,'%Y%m%d')<=date_format('{$params['enddate']}','%Y%m%d')";
        }

        $where ='1';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        //货转量，维度：storagetank_sysno,customer_sysno,goods_sysno
        $sql = "SELECT std.storagetank_sysno,sale_customer_sysno,ss.goods_sysno,sum(std.transqty) as transqty from ".DB_PREFIX."doc_stock_trans st left join ".DB_PREFIX."doc_stock_trans_detail std on st.sysno=std.stocktrans_sysno left join ".DB_PREFIX."storage_stock ss on std.out_stock_sysno=ss.sysno where {$where} and st.stocktransstatus =4 group by std.storagetank_sysno,sale_customer_sysno,ss.goods_sysno";
        $stocktransData = $this->dbh->select($sql);

        $filter = array();
        if (isset($params['startdate']) && $params['startdate'] != '') {
            $filter[] = " date_format(so.`updated_at`,'%Y%m%d')>=date_format('{$params['startdate']}','%Y%m%d')";
        }
        if (isset($params['enddate']) && $params['enddate'] != '') {
            $filter[] = " date_format(so.`updated_at`,'%Y%m%d')<=date_format('{$params['enddate']}','%Y%m%d')";
        }

        $where ='1';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        //罐子报关量，维度：storagetank_sysno,goods_sysno
        $sql = "SELECT storagetank_sysno,goods_sysno,sum(release_num) as release_num, unrelease_num from (select * from ".DB_PREFIX."doc_stock_in_declare order by updated_at desc) so where {$where} group by storagetank_sysno,goods_sysno ";
        $stockindeclareData = $this->dbh->select($sql);

        //罐子出库量，维度：storagetank_sysno,goods_sysno
        $sql = "SELECT storagetank_sysno,goods_sysno,sum(beqty) as beqty from ".DB_PREFIX."doc_stock_out so left join ".DB_PREFIX."doc_stock_out_detail sod on so.sysno=sod.stockout_sysno where {$where} and so.stockoutstatus=4 and so.isdel=0 group by storagetank_sysno,goods_sysno";
        $storagetankoutData = $this->dbh->select($sql);

        //初始化客户可用报关量、罐出库量
        foreach ($declareData as $key => $value) {
            $declareData[$key]['release_beqty'] = $value['release_num'];
            $declareData[$key]['storagetankoutqty'] = 0;
        }

        //罐子报关可发量
        foreach ($stockindeclareData as $key => $value) {
            $stockindeclareData[$key]['storagetank_beqty'] = $value['release_num']; //初始化
            foreach ($storagetankoutData as $k => $v) {
               if ($v['storagetank_sysno'] == $value['storagetank_sysno'] && $v['goods_sysno'] == $value['goods_sysno']) {
                   $stockindeclareData[$key]['storagetank_beqty'] = isset($v['beqty']) ? $value['release_num'] - $v['beqty'] : $value['release_num'];
               }
            }
        }

        foreach ($declareData as $key => $value) {
            //提单量、商检量
            foreach ($stockinData as $sik => $siv) {
                if($siv['storagetank_sysno'] == $value['storagetank_sysno'] && $siv['customer_sysno'] == $value['customer_sysno'] && $siv['goods_sysno'] == $value['goods_sysno']){
                    $declareData[$key]['tobeqty'] = $siv['tobeqty'];
                    $declareData[$key]['bussinesscheckqty'] = $siv['bussinesscheckqty'];
                }
            }
            //客户报关可发量    -出库量
            foreach ($stockoutData as $sok => $sov) {
                if($sov['storagetank_sysno'] == $value['storagetank_sysno'] && $sov['customer_sysno'] == $value['customer_sysno'] && $sov['goods_sysno'] == $value['goods_sysno']){
                    $declareData[$key]['release_beqty'] = isset($sov['beqty']) ? round(($declareData[$key]['release_beqty']-$sov['beqty']),3) : $declareData[$key]['release_beqty'];
                }
            }
            //客户报关可发量    -货转量
            foreach ($stocktransData as $stk => $stv) {
                if($stv['storagetank_sysno'] == $value['storagetank_sysno'] && $stv['customer_sysno'] == $value['customer_sysno'] && $stv['goods_sysno'] == $value['goods_sysno']){
                    $declareData[$key]['release_beqty'] = isset($stv['transqty']) ? round(($declareData[$key]['release_beqty']-$stv['transqty']),3) : $declareData[$key]['release_beqty'];
                }
            }
            //罐出库量
            foreach ($storagetankoutData as $sttv) {
                if($sttv['storagetank_sysno'] == $value['storagetank_sysno'] && $sttv['goods_sysno'] == $value['goods_sysno']){
                    $declareData[$key]['storagetankoutqty'] = isset($sttv['beqty']) ? $sttv['beqty'] : 0;
                }
            }
            //罐子报关可发量
            foreach ($stockindeclareData as $sidv) {
                if($sidv['storagetank_sysno'] == $value['storagetank_sysno'] && $sidv['goods_sysno'] == $value['goods_sysno']){
                    $declareData[$key]['storagetank_beqty'] = isset($sidv['storagetank_beqty']) ? round($sidv['storagetank_beqty'],3) : 0;
                    $declareData[$key]['unrelease_num'] = round($sidv['unrelease_num'],3);
                }
            }
        }

        foreach ($declareData as $key => $value) {
            $declareData[$key]['release_beqty'] = $declareData[$key]['release_beqty']< 0 ? 0:$declareData[$key]['release_beqty'];
            $declareData[$key]['storagetank_beqty'] = $declareData[$key]['storagetank_beqty']<0?0:$declareData[$key]['storagetank_beqty'];
            // $declareData[$key]['unrelease_num'] = $value['bussinesscheckqty'] - $value['release_num'] ;              //罐存未报关量
            $declareData[$key]['storagetankqty'] = $value['storagetank_beqty'] + $declareData[$key]['unrelease_num'] < 0 ? 0 : $value['storagetank_beqty'] + $declareData[$key]['unrelease_num']; //罐结存量
        }
        $data['list'] = $declareData;
        if(isset($params['page'])&&$params['page']==false){
            return $data;
        }else{
            $data['totalRow'] = count($data['list']);
            $data['totalPage'] = ceil( $data['totalRow'] / $params['pageSize']);
            $list=array_chunk($data['list'],$params['pageSize'],false);
            $data['list']= $list [$params['pageCurrent']-1];
        }

        if(empty($data['list'])){
            $data['list'] = [];
        }

        return $data;
    }

    
}
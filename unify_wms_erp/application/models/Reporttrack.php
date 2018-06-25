<?php
/**
 * Reporttrack Model
 *
 */

class ReporttrackModel
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

    public function getStocknoList($params) {
        $result=array('total'=>0,'list'=>array());
        $filter = array();
        if (isset($params['bar_enddate']) && $params['bar_enddate'] != '') {
            $filter[] = " unix_timestamp(IF(ss.doctype =  3, st.stocktransdate  ,si.stockindate))  <= unix_timestamp('".$params['bar_enddate']." 23:59:59')  ";
        }
        if (isset($params['bar_startdate']) && $params['bar_startdate'] != '') {
            $filter[] = " unix_timestamp(IF(ss.doctype =  3, st.stocktransdate  ,si.stockindate))  >= unix_timestamp('".$params['bar_startdate']." 00:00:00')  ";
        }
        if(isset($params['customer_sysno']) && $params['customer_sysno'] != ''){
            $filter[] = " ss.`customer_sysno` = '".$params['customer_sysno']."' ";
        }
        if(isset($params['bar_name']) && $params['bar_name'] != ''){
            $filter[] = " ss.`customername` LIKE '%{$params['bar_name']}%'";
        }
        if(isset($params['goodsnature']) && $params['goodsnature'] != ''){
            $filter[] = " ss.`goodsnature` = '".$params['goodsnature']."' ";
        }
        if(isset($params['bar_doctype']) && $params['bar_doctype'] != '-100'){
            $filter[] = " ss.`doctype` = '".$params['bar_doctype']."' ";
        }
        if(isset($params['bar_no']) && $params['bar_no'] != ''){
            #如果是出库单类型特殊处理
            if(substr($params['bar_no'],0,1)=='C' || substr($params['bar_no'],0,1)=='D')
            {
                $firstno = $params['bar_no'];//保存原始查询单据
                $sql = "select ss.firstfrom_sysno from ".DB_PREFIX."doc_stock_out so left join ".DB_PREFIX."doc_stock_out_detail sod on (sod.stockout_sysno=so.sysno) left join ".DB_PREFIX."storage_stock ss on (ss.sysno=sod.stock_sysno) where so.stockoutno='".$firstno."' ";
                $firstinfo = $this->dbh->select($sql);
                foreach ($firstinfo as $key => $value) {
                    $arr = $this->private_stocknolist($value['firstfrom_sysno'],$params['bar_no']);
                    $result['list'] = array_merge($result['list'],$arr['list']);
                    $result['totalRow'] = $result['totalRow']+$arr['totalRow'];
                }
                
                return $result;
            }elseif(substr($params['bar_no'],0,1)=='T'){
                $firstno = $params['bar_no'];//保存原始查询单据
                $sql = "select ss.firstfrom_sysno from ".DB_PREFIX."doc_stock_trans st left join ".DB_PREFIX."doc_stock_trans_detail std on (std.stocktrans_sysno=st.sysno) left join ".DB_PREFIX."storage_stock ss on (ss.sysno=std.out_stock_sysno) where st.stocktransno='".$firstno."' ";
                $firstinfo = $this->dbh->select($sql);
                foreach ($firstinfo as $key => $value) {
                    $arr = $this->private_stocknolist($value['firstfrom_sysno'],$params['bar_no']);
                    $result['list'] = array_merge($result['list'],$arr['list']);
                    $result['totalRow'] = $result['totalRow']+$arr['totalRow'];
                }
                
                return $result;
            }else
            {
                $filter[] = " (st.`stocktransno` = '".$params['bar_no']."' or si.`stockinno` = '".$params['bar_no']."') ";
            }
        }

        // $where ='  ss.iscurrent = 1 and ss.isdel = 0 and (st.sysno is not null or si.sysno is not null) ';
        $where =' IF(ss.doctype =  3, 1  ,si.isdel=0 and si.stockinstatus=4 ) and  ss.iscurrent = 1 and ss.isdel = 0 ';
        if (1 <= count($filter)) {
            $where .= " and ". implode(' AND ', $filter);
        }
        $orderby = " ORDER BY ss.`updated_at` DESC ";


        // $result=array('total'=>0,'list'=>array());
        $sql="select *
            from ".DB_PREFIX."storage_stock ss
            left join ".DB_PREFIX."doc_stock_trans_detail std on std.in_stock_sysno = ss.sysno
            left join ".DB_PREFIX."doc_stock_trans st on st.sysno = std.stocktrans_sysno
            left join ".DB_PREFIX."doc_stock_in si on si.sysno = ss.firstfrom_sysno
            left join  ".DB_PREFIX."base_storagetank bs on bs.sysno = ss.storagetank_sysno 
            LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = ss.goods_sysno 
            LEFT JOIN ".DB_PREFIX."base_unit un ON un.sysno = bga.unit_sysno WHERE {$where} group by ss.sysno {$orderby} ";
        $result['totalRow']= count($this->dbh->select($sql)) ? count($this->dbh->select($sql)) : 0;

        $sql="select ss.firstfrom_sysno,ss.sysno,ss.stockno,ss.customer_sysno,ss.customername, ss.doctype,
            IF(ss.doctype =  3, st.sysno  ,ss.firstfrom_sysno) as stockin_sysno ,
            IF(ss.doctype =  3, st.stocktransno  ,ss.firstfrom_no) as stockin_no ,
            IF(ss.doctype =  3, st.stocktransdate  ,si.stockindate) as stockin_date ,
            ss.storagetank_sysno,bs.storagetankname,ss.goods_sysno,ss.goodsname,un.unitname,ss.goods_quality_sysno,
            ss.goodsqualityname,ss.goodsnature,ss.instockqty,ss.outstockqty, ss.stockqty as ableqty ,
            st.sale_customername,st.buy_customername
            from ".DB_PREFIX."storage_stock ss
            left join ".DB_PREFIX."doc_stock_trans_detail std on std.in_stock_sysno = ss.sysno
            left join ".DB_PREFIX."doc_stock_trans st on st.sysno = std.stocktrans_sysno
            left join ".DB_PREFIX."doc_stock_in si on si.sysno = ss.firstfrom_sysno
            left join  ".DB_PREFIX."base_storagetank bs on bs.sysno = ss.storagetank_sysno 
            LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = ss.goods_sysno 
            LEFT JOIN ".DB_PREFIX."base_unit un ON un.sysno = bga.unit_sysno WHERE {$where} group by ss.sysno {$orderby} ";
        if(isset($params['page']) && $params['page'] == false){         //不带分页查询

            $result['list'] = $this->dbh->select($sql);
            // return $result;
        }else{      //带分页查询
            $params['pageCurrent'] = 1;
            $params['pageSize'] = 20;
            $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
            $this->dbh->set_page_num($params['pageCurrent']);
            $this->dbh->set_page_rows($params['pageSize']);
            
            $result['list'] = $this->dbh->select_page($sql);
            // return $result;
        }

        if(isset($params['bar_no']) && $params['bar_no'] != '')
        {
            $fromid = $result['list'][0]['firstfrom_sysno'];
        }
        elseif(count($result['list'])==1)
        {
            $fromid = $result['list'][0]['firstfrom_sysno'];
        }
        #确定了原始单据，开始显示树形结构，没有就直接return
        if($fromid)
        {
            // $where = "ss.iscurrent = 1 and ss.isdel = 0 and ss.`firstfrom_sysno` = '".$fromid."' and (st.sysno is not null or si.sysno is not null) ";
            $where = "IF(ss.doctype =  3, 1  ,si.isdel=0 and si.stockinstatus=4 ) and ss.iscurrent = 1 and ss.isdel = 0 and ss.`firstfrom_sysno` = '".$fromid."' ";
            $sql="select ss.firstfrom_sysno,ss.sysno,ss.stockno,ss.customer_sysno,ss.customername, ss.doctype,
            IF(ss.doctype =  3, st.sysno  ,ss.firstfrom_sysno) as stockin_sysno ,
            IF(ss.doctype =  3, st.stocktransno  ,ss.firstfrom_no) as stockin_no ,
            IF(ss.doctype =  3, st.stocktransdate  ,si.stockindate) as stockin_date ,
            ss.storagetank_sysno,bs.storagetankname,ss.goods_sysno,ss.goodsname,un.unitname,ss.goods_quality_sysno,
            ss.goodsqualityname,ss.goodsnature,ss.instockqty,ss.outstockqty, ss.stockqty as ableqty ,
            st.sale_customername,st.buy_customername,std.out_stock_sysno as parent_sysno
            from ".DB_PREFIX."storage_stock ss
            left join ".DB_PREFIX."doc_stock_trans_detail std on std.in_stock_sysno = ss.sysno
            left join ".DB_PREFIX."doc_stock_trans st on st.sysno = std.stocktrans_sysno
            left join ".DB_PREFIX."doc_stock_in si on si.sysno = ss.firstfrom_sysno
            left join  ".DB_PREFIX."base_storagetank bs on bs.sysno = ss.storagetank_sysno 
            LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = ss.goods_sysno 
            LEFT JOIN ".DB_PREFIX."base_unit un ON un.sysno = bga.unit_sysno WHERE {$where} group by ss.sysno {$orderby} ";
            
            $result['list'] = $this->dbh->select($sql);


            //标识这次查询
            for($i=0;$i<=count($result['list']);$i++){
                if($result['list'][$i]['stockouttype'] || !$result['list'][$i]['sysno'])
                {
                    continue;
                }
                $stock_sysno = $result['list'][$i]['sysno'];
                //添加出库信息
                $sql = 'select so.customername,so.stockouttype,so.sysno as outsysno,so.stockoutno as stockin_no,if(stockouttype=2,sod.beqty ,sod.beqty ) AS instockqty,sod.qualityname as goodsqualityname,sod.goodsname from '.DB_PREFIX.'doc_stock_out so left join '.DB_PREFIX.'doc_stock_out_detail sod on (so.sysno=sod.stockout_sysno) where sod.beqty>0 and so.stockoutstatus=4 and sod.stock_sysno = '.$stock_sysno.' group by so.sysno';
                $res = $this->dbh->select($sql);
                if(count($res)>0)
                {
                   foreach ($res as $key => $value) {
                       $result['totalRow']++;
                       $value['parent_sysno'] = $stock_sysno;
                       $value['doctype'] = $value['stockouttype']+4;//4船出库5车出库
                       $result['list'][] = $value;
                   }
                }
                
                if($params['bar_no']==$result['list'][$i]['stockin_no'])
                {
                    $result['list'][$i]['flag']=true;
                    // break;
                }
            }
        }

        return $result;
    }

    public function private_stocknolist($fromid,$fromno)
    {
        $where = "IF(ss.doctype =  3, 1  ,si.isdel=0 and si.stockinstatus=4 ) and ss.iscurrent = 1 and ss.isdel = 0 and ss.`firstfrom_sysno` = '".$fromid."' ";
        $sql="select ss.firstfrom_sysno,ss.sysno,ss.stockno,ss.customer_sysno,ss.customername, ss.doctype,
        IF(ss.doctype =  3, st.sysno  ,ss.firstfrom_sysno) as stockin_sysno ,
        IF(ss.doctype =  3, st.stocktransno  ,ss.firstfrom_no) as stockin_no ,
        IF(ss.doctype =  3, st.stocktransdate  ,si.stockindate) as stockin_date ,
        ss.storagetank_sysno,bs.storagetankname,ss.goods_sysno,ss.goodsname,un.unitname,ss.goods_quality_sysno,
        ss.goodsqualityname,ss.goodsnature,ss.instockqty,ss.outstockqty, ss.stockqty as ableqty ,
        st.sale_customername,st.buy_customername,std.out_stock_sysno as parent_sysno
        from ".DB_PREFIX."storage_stock ss
        left join ".DB_PREFIX."doc_stock_trans_detail std on std.in_stock_sysno = ss.sysno
        left join ".DB_PREFIX."doc_stock_trans st on st.sysno = std.stocktrans_sysno
        left join ".DB_PREFIX."doc_stock_in si on si.sysno = ss.firstfrom_sysno
        left join  ".DB_PREFIX."base_storagetank bs on bs.sysno = ss.storagetank_sysno 
        LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = ss.goods_sysno 
        LEFT JOIN ".DB_PREFIX."base_unit un ON un.sysno = bga.unit_sysno WHERE {$where} group by ss.sysno {$orderby} ";

        
        $result['list'] = $this->dbh->select($sql);

        //标识这次查询
        for($i=0;$i<count($result['list']);$i++){
            if($result['list'][$i]['stockouttype'] || !$result['list'][$i]['sysno'])
            {
                continue;
            }
            $stock_sysno = $result['list'][$i]['sysno'];
            //添加出库信息
            $sql = 'select so.customername,so.stockouttype,so.sysno as outsysno,so.stockoutno as stockin_no,if(stockouttype=2,sod.beqty ,sod.beqty ) AS instockqty,sod.qualityname as goodsqualityname,sod.goodsname from '.DB_PREFIX.'doc_stock_out so left join '.DB_PREFIX.'doc_stock_out_detail sod on (so.sysno=sod.stockout_sysno) where sod.beqty>0 and so.stockoutstatus=4 and sod.stock_sysno = '.$stock_sysno.' group by so.sysno';
            $res = $this->dbh->select($sql);
            if(count($res)>0)
            {
               foreach ($res as $key => $value) {
                   $result['totalRow']++;
                   $value['parent_sysno'] = $stock_sysno;
                   $value['doctype'] = $value['stockouttype']+4;//5船出库6车出库7管出库
                   if($fromno==$value['stockin_no'])
                   {
                    $value['flag'] = true;
                   }
                   $result['list'][] = $value;
               }
            }
        }
        return $result;
    }

}
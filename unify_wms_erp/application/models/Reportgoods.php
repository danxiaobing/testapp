<?php

/**
 * Created by PhpStorm.
 * User: HR
 * Date: 2016/11/15 0015
 * Time: 10:40
 */
class ReportgoodsModel
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


    /**
     * 货品收发存汇总
     * @author HR 
     * 
     */
    public function getlist($params)
    {


        $filter = array();
        if (isset($params['id']) && $params['id'] != '') {
            $filter[] = " st.goods_sysno = {$params['id']} ";
        }
        if ( isset($params['begin_time']) && $params['begin_time'] != '' ) {
            $filter[] = "st.instockdate >= '{$params['begin_time']}'";
        }

        if( isset($params['end_time']) && $params['end_time'] != '' ){
            $filter[] = "st.instockdate <= '{$params['end_time']}'";
        }

        $sonwhere = " WHERE isdel=0 AND iscurrent=0 AND ghosttype in(1,2,3,6) AND instockdate='{$params['begin_time']}' " ;



        $where ='WHERE  st.isdel=0 AND st.iscurrent=1 AND st.doctype!=3 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        
        $sql ="SELECT g.goodsname,g.sysno goods_sysno,bu.unitname,
                SUM(st.instockqty)  instockqty,SUM(0-st.outstockqty) outstockqty,SUM(st.clearqty) clearqty
                FROM ".DB_PREFIX."base_goods g
                LEFT JOIN ".DB_PREFIX."storage_stock st ON g.sysno=st.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON g.sysno=ga.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_unit bu ON ga.unit_sysno=bu.sysno
                {$where} AND ga.isdel=0 GROUP BY goodsname";         
        // error_log($sql,3,'sql_print.txt');
        $data = $this->dbh->select($sql);
      
        foreach ($data as $k => &$value) {
            $a = array(
                'goods_sysno' =>$value['goods_sysno'],
                'begin_time' => $search['begin_time'],
            );
            $ghoststockqty = intval($this->getqty($a));
            $data[$k]['ghoststockqty'] = $ghoststockqty;
            $data[$k]['lastqty'] = $ghoststockqty+$data[$k]['instockqty']+$data[$k]['outstockqty']-$data[$k]['clearqty'];
            
        }

        return $data;
    }

    public function getDetail($params)
    {
        $result = $params;
        // $filter = array();
        // if (isset($params['id']) && $params['id'] != '') {
        //     $filter[] = " s.goods_sysno = {$params['id']} ";
        // }


        // $where =' '; 
        // if (1 <= count($filter)) {
        //     $where .= ' AND ' . implode(' AND ', $filter);
        // }

        $order = 'order by goodsname';
        // var_dump($params);exit();
        if(empty($params['id'])){
            $data = [];
            return $data;
            exit;
        }

        //测试版SQL 希望木有bug
        $sql = "SELECT g.goodsname,g.sysno goods_sysno,bu.unitname,
                SUM(DISTINCT sid.beqty)
                as num,si.stockinno sno,si.stockindate date,si.sysno,sid.goodsnature,si.stockintype stype,1 as type
                FROM ".DB_PREFIX."storage_stock s
                LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sid ON s.firstfrom_sysno=sid.stockin_sysno
                LEFT JOIN ".DB_PREFIX."doc_stock_in si ON si.sysno = s.firstfrom_sysno
                LEFT JOIN ".DB_PREFIX."base_goods g ON g.sysno=s.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON g.sysno=ga.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_unit bu ON ga.unit_sysno=bu.sysno
                WHERE  s.isdel=0 AND s.iscurrent=1 AND s.doctype!=3  AND si.stockindate >= '{$params['begin_time']}' AND si.stockindate <= '{$params['end_time']}' AND ga.isdel=0  AND (si.stockinstatus=4 or IF(si.stockintype=2,si.stockinstatus=3,si.stockinstatus=4) )  AND  s.goods_sysno = {$params['id']}  GROUP BY si.sysno 

                UNION ALL

                SELECT g.goodsname,g.sysno goods_sysno,bu.`unitname`,
                SUM(DISTINCT 0-sd.beqty)
                as num,
                so.stockoutno sno,so.stockoutdate date,so.sysno,sd.goodsnature,so.stockouttype stype,2 as type
                FROM ".DB_PREFIX."doc_stock_out_detail sd 
                LEFT JOIN ".DB_PREFIX."doc_stock_out  so ON sd.stockout_sysno = so.sysno
                LEFT JOIN  ".DB_PREFIX."base_goods g ON sd.goods_sysno = g.sysno
                LEFT JOIN `".DB_PREFIX."base_goods_attribute` AS bga ON bga.`goods_sysno`=g.sysno
                LEFT JOIN `".DB_PREFIX."base_unit` AS bu ON bu.`sysno`=bga.`unit_sysno`
                WHERE   (so.stockoutstatus=4 or IF(so.stockouttype=2,so.stockoutstatus=3,so.stockoutstatus=4)) AND (sd.bussinesscheckqty or sd.beqty)!=0  AND so.stockoutdate >= '{$params['begin_time']}' AND  so.stockoutdate <= '{$params['end_time']}' AND sd.goods_sysno={$params['id']}   AND so.isdel=0 AND g.isdel=0 GROUP BY so.sysno 

                UNION ALL

                SELECT g.goodsname,g.sysno goods_sysno, bu.unitname ,(0-cd.okqty) num,sc.stockclearno sno, sc.stockcleardate date,sc.sysno,si.goodsnature,sc.stockclearstatus stype,3 as type
                FROM ".DB_PREFIX."doc_stock_clear_detail cd
                LEFT JOIN  ".DB_PREFIX."doc_stock_clear sc ON cd.stockclear_sysno = sc.sysno
                LEFT JOIN  ".DB_PREFIX."doc_stock_in_detail si ON si.stockin_sysno=cd.stockin_sysno
                LEFT JOIN  ".DB_PREFIX."base_goods g ON si.goods_sysno = g.sysno
                LEFT JOIN  ".DB_PREFIX."base_goods_attribute ga ON  ga.goods_sysno = g.sysno 
                LEFT JOIN  ".DB_PREFIX."base_unit bu ON ga.unit_sysno = bu.sysno
                WHERE  sc.stockcleardate >= '{$params['begin_time']}' AND  sc.stockcleardate <= '{$params['end_time']}'  AND sc.isdel=0 AND g.isdel=0  
                AND  si.goods_sysno = {$params['id']}   AND (sc.stockclearstatus =4 )
                GROUP BY sc.sysno order by type";

        $data = $this->dbh->select($sql);
        if(empty($data)){
            $data = [];
            return $data;
            exit;
        }
        if(!$params['page'] && empty($params['pageSize'])){
            return $data;
        }else{
            $result['totalRow']=count($data);
            $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
            $list=array_chunk($data,$params['pageSize'],false);
            $result['list']=$list[$params['pageCurrent']-1];            
            return $result;
        }

    }

    public function getContractInfo($params)
    {

        $filter = array();
        if (isset($params['bar_contractstatus']) && $params['bar_contractstatus'] != '') {
            $filter[] = " contractstatus = {$params['bar_contractstatus']} ";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '') {
            $filter[] = " status = {$params['bar_status']} ";
        }

        if (isset($params['bar_isdel']) && $params['bar_isdel'] != '') {
            $filter[] = " isdel = {$params['bar_isdel']} ";
        } 


        if (1 <= count($filter)) {
            $where = implode(' AND ', $filter);
        }

        $sql = "SELECT * FROM ".DB_PREFIX."doc_contract where {$where}";

        return $this->dbh->select($sql);
    }
    /**
     * 获取客户的期初数量和期末数量
     * @param  [type] $goods_sysno [商品ID]
     * @param  [type] $where       [where条件]
     * @author HR
     */
    public function getqty2($params)
    {
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " goods_sysno = {$params['goods_sysno']} ";
        }
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " ghosttime < '{$params['begin_time']} 0:00:00'";
        }

        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " customer_sysno = {$params['customer_sysno']} ";
        }
        $where = 'WHERE isdel=0 AND iscurrent=0 ';
        if (1 <= count($filter)) {
            $where .= ' AND '.implode(' AND ', $filter);
        }
        // $sql = "SELECT ghoststockqty FROM ".DB_PREFIX."storage_stock  {$where} ORDER BY sysno desc LIMIT 0,1";
        // error_log($sql, 3, 'sql_print.txt');
        $sql = "SELECT SUM(bb.ghoststockqty) as ghoststockqty FROM
(SELECT aa.ghoststockqty FROM (SELECT	sysno,ghoststockqty,customer_sysno,customername,goodsname,goods_sysno,firstfrom_sysno FROM
			".DB_PREFIX."storage_stock {$where}
		   ORDER BY sysno DESC) AS aa
           GROUP BY firstfrom_sysno) as bb";
 //       echo $sql;
        $result = $this->dbh->select_one($sql);

        return $result;
    }
    /**
     * 获取货品的期初数量和期末数量
     * @param  [type] $goods_sysno [商品ID]
     * @param  [type] $where       [where条件]
     * @author HR            
     */
    public function getqty($params)
    {
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " goods_sysno = {$params['goods_sysno']} ";
        }
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " ghosttime < '{$params['begin_time']} 0:00:00'";
        }

        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " customer_sysno = {$params['customer_sysno']} ";
        } 
        $where = 'WHERE isdel=0 AND iscurrent=0 AND ghosttype in(1,2,3,6)';
        if (1 <= count($filter)) {
            $where .= ' AND '.implode(' AND ', $filter);
        }
        // $sql = "SELECT ghoststockqty FROM ".DB_PREFIX."storage_stock  {$where} ORDER BY sysno desc LIMIT 0,1";
        // error_log($sql, 3, 'sql_print.txt');
        $sql = "SELECT aa.ghoststockqty from (SELECT sysno,ghoststockqty,customer_sysno,customername 
                FROM ".DB_PREFIX."storage_stock {$where} ORDER BY sysno desc ) as aa GROUP BY aa.customer_sysno";
        $data = $this->dbh->select($sql);
        foreach ($data as $key => $value) {
            $result += $value['ghoststockqty'];
        }

        return $result;
    }

    public function getGoodsInfo()
    {
        $sql = "SELECT * FROM ".DB_PREFIX."base_goods WHERE isdel=0 AND `status`=1";
        return $this->dbh->select($sql);
    }


    public function getlist2($search)
    {
        $result = $search;
            if (isset($search['begin_time']) && $search['begin_time'] != '') {
                $filter[0] = "sout.`stockoutdate` >= '{$search['begin_time']}'";
            }
            if (isset($search['end_time']) && $search['end_time'] != '') {
                $filter[1] = "sout.`stockoutdate` <= '{$search['end_time']}'";
            }
            if (isset($search['id']) && $search['id'] != '') {
                $filter[3] = "ss.`goods_sysno` = '{$search['id']}' ";
            }
            if(isset($search['goodsname']) && $search['goodsname'] != '') {
                $filter[4] = "ss.`goodsname` = '{$search['goodsname']}' ";
            }
            $where = '';
            if (1 <= count($filter)) {
                $where .= ' AND ' . implode(' AND ', $filter);
            }

            //出库单
            $sql = "SELECT SUM(0-soutd.beqty) as outstockqty,soutd.unitname,ss.goods_sysno,soutd.goodsname
                           FROM ".DB_PREFIX."doc_stock_out_detail soutd
                           LEFT JOIN ".DB_PREFIX."doc_stock_out sout ON sout.sysno = soutd.stockout_sysno
                           LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = soutd.stock_sysno AND ss.iscurrent=1
                           where  (sout.stockoutstatus=4 or IF(sout.stockouttype=2,sout.stockoutstatus=3,sout.stockoutstatus = 4)) {$where}
                           GROUP  BY ss.goods_sysno ORDER BY ss.sysno";
            $stockout = $this->dbh->select($sql);

            if (isset($search['begin_time']) && $search['begin_time'] != '') {
                $filter[0] = "sc.`stockcleardate` >= '{$search['begin_time']}'";
            }
            if (isset($search['end_time']) && $search['end_time'] != '') {
                $filter[1] = "sc.`stockcleardate` <= '{$search['end_time']}'";
            }
            if (isset($search['id']) && $search['id'] != '') {
                $filter[3] = "ss.`goods_sysno` = '{$search['id']}' ";
            }
            if(isset($search['goodsname']) && $search['goodsname'] != '') {
                $filter[4] = "ss.`goodsname` = '{$search['goodsname']}' ";
            }
            $where = '';
            if (1 <= count($filter)) {
                $where .= ' AND ' . implode(' AND ', $filter);
            }
            
            //清库单
            $sql = "SELECT (0-scd.okqty) as clearqty,ss.goodsname,ss.goods_sysno
                                FROM ".DB_PREFIX."doc_stock_clear_detail scd
                                LEFT JOIN ".DB_PREFIX."doc_stock_clear sc on sc.sysno = scd.stockclear_sysno
                                LEFT JOIN ".DB_PREFIX."doc_stock_in sin on sin.sysno = scd.stockin_sysno
                                LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.firstfrom_sysno=sin.sysno AND ss.iscurrent=1
                                LEFT JOIN ".DB_PREFIX."base_goods g on g.sysno = ss.goods_sysno and g.isdel =0 and g.status<2
                                LEFT JOIN ".DB_PREFIX."base_goods_attribute ga on ga.goods_sysno = g.sysno and ga.isdel=0 and ga.status<2
                                LEFT JOIN ".DB_PREFIX."base_unit u on u.sysno = ga.unit_sysno and u.isdel = 0 and u.status<2
                                WHERE  (sc.stockclearstatus=4) {$where}
                                GROUP BY ss.goods_sysno ORDER BY sc.sysno";
            $clear = $this->dbh->select($sql);

            // error_log($sql,3,'sql_print.txt');
        //库存数量
            //获取入库量
            if (isset($search['begin_time']) && $search['begin_time'] != '') {
                $filter[0] = "sin.`stockindate` >= '{$search['begin_time']}'";
            }
            if (isset($search['end_time']) && $search['end_time'] != '') {
                $filter[1] = "sin.`stockindate` <= '{$search['end_time']}'";
            }
            if (isset($search['id']) && $search['id'] != '') {
                $filter[3] = "sind.`goods_sysno` = '{$search['id']}' ";
            }
            if(isset($search['goodsname']) && $search['goodsname'] != '') {
                $filter[4] = "sind.`goodsname` = '{$search['goodsname']}' ";
            }
            $where = ' 1=1 ';
            if (1 <= count($filter)) {
                $where .= ' AND ' . implode(' AND ', $filter);
            }
            //入库数量
            $sql = "SELECT sind.goodsname,sind.goods_sysno,SUM(sind.beqty) as instockqty
                        FROM  ".DB_PREFIX."doc_stock_in sin
                        LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sind ON sin.sysno = sind.stockin_sysno
                        WHERE (sin.stockinstatus = 4 or IF(sin.stockintype=2,sin.stockinstatus = 3,sin.stockinstatus = 4)) and sind.beqty!=0 and sin.isdel = 0 and sin.status <2 AND {$where}
                        GROUP BY sind.goods_sysno ORDER BY sin.sysno
                         ";

            $instockqty= $this->dbh->select($sql);

            //没数据返回空
            if(empty($instockqty) && empty($stockout)  && empty($clear)){
                return $reuslt =array();
            }

            foreach ($instockqty as $key => $value) {
                $instockqty[$key]['outstockqty'] = number_format(0.000,3);
                $instockqty[$key]['clearqty'] = number_format(0.000,3);
                foreach ($stockout as $k => $v) {
                    if($value['goods_sysno']==$v['goods_sysno']){     
                        $instockqty[$key]['outstockqty'] = $stockout[$k]['outstockqty'];
                    }
                    $instockqty[$key]['unitname'] = $stockout[$k]['unitname'];
                }
                foreach ($clear as $ke => $val) {
                    if($value['goods_sysno']==$val['goods_sysno']){     
                        $instockqty[$key]['clearqty'] = $clear[$ke]['clearqty'];
                    }
                }
            }

        if(isset($search['get']) && $search['get'] != ''){
            return $instockqty;
        }

        foreach ($instockqty as $k => &$value) {
            $a = array(
                'id' =>$value['goods_sysno'],
                'begin_time' => $search['begin_time'],
                'get' => 1,
                );
            $ghoststockqty = floatval($this->getghostnum($a));
            $instockqty[$k]['ghoststockqty'] = $ghoststockqty;
            $instockqty[$k]['lastqty'] = $ghoststockqty+$instockqty[$k]['instockqty']+$instockqty[$k]['outstockqty']+$instockqty[$k]['clearqty'];

        }


        if($search['page']===false && empty($search['pageSize'])){
            return $instockqty;
        }else{
            $result['totalRow']=count($instockqty);
            $result['totalPage'] = ceil($result['totalRow'] / $search['pageSize']);
            $list=array_chunk($instockqty,$search['pageSize'],false);
            $result['list']=$list[$search['pageCurrent']-1];            
            return $result;
        }
    }

    public function getlist3($search)
    {
        $result = $search;
        //库存数量
        //获取入库量
        $filter = [];
        if (isset($search['begin_time']) && $search['begin_time'] != '') {
            $filter[0] = "sin.`stockindate` >= '{$search['begin_time']}'";
        }
        if (isset($search['end_time']) && $search['end_time'] != '') {
            $filter[1] = "sin.`stockindate` <= '{$search['end_time']}'";
        }
        if (isset($search['id']) && $search['id'] != '') {
            $filter[3] = "sind.`goods_sysno` = '{$search['id']}' ";
        }
        if(isset($search['goodsname']) && $search['goodsname'] != '') {
            $filter[4] = "sind.`goodsname` = '{$search['goodsname']}' ";
        }
        if(isset($search['goodsnature']) && $search['goodsnature'] != '') {
            $filter[5] = "sind.`goodsnature` = '{$search['goodsnature']}' ";
        }
        if(isset($search['wharfname']) && $search['wharfname'] != '') {
            $filter[6] = "bw.`wharfname` = '{$search['wharfname']}' ";
        }

        $where = ' 1=1 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        if(isset($search['cartype']) && $search['cartype'] != '') {
            if( $search['cartype'] == 1 ){
                $where .=  "and dpo.`cartype` = '1' ";
            }else{
                $where .= " and (dpo.`cartype` = '2' or dpo.`cartype` = '3' )";
            }
        }

        //入库数量
        $sql = "SELECT sind.goodsname,sind.goods_sysno,SUM(sind.beqty) as instockqty,sin.stockinno,sin.stockindate,sind.takegoodsnum,sind.bussinesscheckqty,sind.shipname,bw.sysno,bw.wharfname,sind.goodsnature,sin.customer_sysno,sin.customername,sin.stockintype,dpo.cartype,sum(ss.ullage) as ullage,ss.beyondqty
                        FROM  ".DB_PREFIX."doc_stock_in sin
                        LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sind ON sin.sysno = sind.stockin_sysno AND sind.isdel=0 AND sind.status<2
                        LEFT  JOIN ".DB_PREFIX."storage_stock ss ON ss.firstfrom_sysno = sin.sysno AND ss.iscurrent = 1 AND  ss.isdel=0 AND ss.status<2
                        LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sod ON sod.stockin_sysno = sin.sysno AND sod.isdel=0 AND sod.status<2
                        LEFT JOIN ".DB_PREFIX."base_wharf bw ON bw.sysno = sin.wharf_sysno AND bw.isdel=0 AND bw.status<2
                        LEFT JOIN ".DB_PREFIX."doc_pounds_out dpo ON dpo.stockoutdetail_sysno = sod.sysno AND dpo.isdel=0 AND dpo.status<2
                        WHERE  (sin.stockinstatus = 4 or IF(sin.stockintype=2,sin.stockinstatus = 3,sin.stockinstatus = 4)) and sind.beqty!=0 and sin.isdel = 0 and sin.status <2 AND {$where}
                        GROUP BY sin.stockinno ORDER BY sin.sysno
                         ";
        //error_log($sql,3,'print_sql.txt');
        $instockqty= $this->dbh->select($sql);
    //    print_r($instockqty);die;
    foreach($instockqty as $k=>$value){
        if($value['stockinno']=='') continue;
        $sinNo[$k] = $value['stockinno'];
    }

        $sinNo = implode('\',\'',array($sinNo));

        //货权转移数量
        //获取货权转移量
        $filter = [];
        if (isset($search['begin_time']) && $search['begin_time'] != '') {
            $filter[0] = "st.`stocktransdate` >= '{$search['begin_time']}'";
        }
        if (isset($search['end_time']) && $search['end_time'] != '') {
            $filter[1] = "st.`stocktransdate` <= '{$search['end_time']}'";
        }
        if (isset($search['id']) && $search['id'] != '') {
            $filter[3] = "ss.`goods_sysno` = '{$search['id']}' ";
        }
        if(isset($search['goodsname']) && $search['goodsname'] != '') {
            $filter[4] = "ss.`goodsname` = '{$search['goodsname']}' ";
        }

        $where = ' ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        //货权转移量

        foreach($instockqty as $k=> $item )
        {
            $sql = "SELECT 0-std.transqty
                        FROM ".DB_PREFIX."doc_stock_trans st
                        LEFT JOIN ".DB_PREFIX."doc_stock_trans_detail std ON std.stocktrans_sysno = st.sysno AND std.isdel=0 AND std.status<2
                        LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = std.in_stock_sysno AND ss.iscurrent=1
                        WHERE ss.firstfrom_no='{$item['stockinno']}' {$where}";
            //print_r($sql);die;
            $trans = $this->dbh->select_one($sql);
            $instockqty[$k]['transqty'] = ($trans==false)?0:$trans;

            //出库单
            $sql = "SELECT SUM(0-soutd.beqty) as outstockqty
                           FROM ".DB_PREFIX."doc_stock_out_detail soutd
                           LEFT JOIN ".DB_PREFIX."doc_stock_out sout ON sout.sysno = soutd.stockout_sysno AND sout.isdel=0 AND sout.status<2
                           where  (sout.stockoutstatus IN (4,6)or IF(sout.stockouttype=2,sout.stockoutstatus=3,sout.stockoutstatus = 4)) AND soutd.stockinno='{$item['stockinno']}'
                          GROUP BY soutd.stockinno";
            $stockout = $this->dbh->select_one($sql);
            $instockqty[$k]['outstockqty'] = ($stockout==false)?0:$stockout;
            //槽车数
            $sql = "SELECT COUNT(pin.sysno) as wagon,si.stockinno,bw.wharfname
                        FROM ".DB_PREFIX."doc_stock_in si
                        LEFT JOIN ".DB_PREFIX."doc_pounds_in pin ON pin.stockin_sysno = si.sysno AND pin.isdel=0 AND pin.status<2
                        LEFT JOIN ".DB_PREFIX."base_wharf bw ON bw.sysno = si.wharf_sysno AND bw.isdel=0 AND bw.status<2
                        WHERE (pin.poundsinstatus = 4 or IF(pin.poundsinstatus=2,pin.poundsinstatus = 3,pin.poundsinstatus = 4))and pin.isdel = 0 and pin.status <2 and si.stockinno='{$item['stockinno']}'
                        GROUP  BY si.stockinno ORDER BY pin.sysno";

            $wagon = $this->dbh->select_one($sql);
            $instockqty[$k]['wagon'] = ($wagon==false)?0:$wagon;

        }

        //print_r($search);die;

        $filter = [];

        if (isset($search['begin_time']) && $search['begin_time'] != '') {
            $filter[0] = "sc.`stockcleardate` >= '{$search['begin_time']}'";
        }
        if (isset($search['end_time']) && $search['end_time'] != '') {
            $filter[1] = "sc.`stockcleardate` <= '{$search['end_time']}'";
        }
        if (isset($search['id']) && $search['id'] != '') {
            $filter[3] = "ss.`goods_sysno` = '{$search['id']}' ";
        }
        if(isset($search['goodsname']) && $search['goodsname'] != '') {
            $filter[4] = "ss.`goodsname` = '{$search['goodsname']}' ";
        }
        $where = '';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        //清库单
        $sql = "SELECT (0-scd.okqty) as clearqty,sin.stockinno
                                FROM ".DB_PREFIX."doc_stock_clear_detail scd
                                LEFT JOIN ".DB_PREFIX."doc_stock_clear sc on sc.sysno = scd.stockclear_sysno
                                LEFT JOIN ".DB_PREFIX."doc_stock_in sin on sin.sysno = scd.stockin_sysno
                                LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.firstfrom_sysno=sin.sysno AND ss.iscurrent=1
                                WHERE  (sc.stockclearstatus=4) {$where}
                                GROUP BY sin.stockinno ORDER BY sc.sysno";

        $clear = $this->dbh->select($sql);
     //   print_r($clear);die;

        //没数据返回空
        if(empty($instockqty) && empty($stockout)  && empty($trans) && empty($wagon) && empty($clear)){
            return $reuslt =array();
        }

        $goods_sysno_where = 1;
        if (isset($search['goods_sysno']) && $search['goods_sysno'] != '') {
            $goods_sysno_where = "`sysno` = '{$search['goods_sysno']}'";
        }
        //得到所有的商品
        $sql = "select sysno as goods_sysno,goodsname as goodsname from ".DB_PREFIX."base_goods  WHERE  $goods_sysno_where";

        $goodsAll = $this->dbh->select($sql);

        foreach($goodsAll as $key=>$value){
            $arr[] = array(
                'goods_sysno' => $goodsAll[$key]['goods_sysno'],
                'goodsname' => $goodsAll[$key]['goodsname'],
                'startTime'=>$search['startTime'],
                'endTime'=>$search['endTime'],
            );
        }


        foreach ($instockqty as $key => $value) {
            foreach ($clear as $key => $data) {
                if($value['stockinno']==$data['stockinno']){
                    $instockqty[$key]['clearqty'] = $clear[$key]['clearqty'];
                }
            }
        }

        if(isset($search['get']) && $search['get'] != ''){
            return $instockqty;
        }
      //  print_r($instockqty);die;
        foreach ($instockqty as $k => &$value) {
            $a = array(
                'id' =>$value['goods_sysno'],
                'begin_time' => $search['begin_time'],
                'get' => 1,
            );

            $ghoststockqty = floatval($this->getghostnum($a));
            $instockqty[$k]['ghoststockqty'] = $ghoststockqty;
            $instockqty[$k]['lastqty'] = $ghoststockqty + $instockqty[$k]['instockqty'] + $instockqty[$k]['outstockqty'] + $instockqty[$k]['clearqty'];
          //  var_dump($instockqty[$k]['clearqty']);die;

        }
        if($search['page']===false && empty($search['pageSize'])){
            return $instockqty;
        }else{
            $result['totalRow']=count($instockqty);
            $result['totalPage'] = ceil($result['totalRow'] / $search['pageSize']);
            $list=array_chunk($instockqty,$search['pageSize'],false);
            $result['list']=$list[$search['pageCurrent']-1];
            return $result;
        }
    }

    public function getDetail2($params)
    {
        $result = $params;
        $order = 'order by goodsname';
        if(empty($params['id'])){
            $data = [];
            return $data;
            exit;
        }

        //测试版SQL 希望木有bug
        $sql = "SELECT g.goodsname,g.sysno goods_sysno,bu.unitname,
                SUM(DISTINCT sid.beqty)
                as num,si.stockinno sno,si.stockindate date,si.sysno,sid.goodsnature,sid.bussinesscheckqty,s.stockqty,sd.beqty,td.transqty,s.shipname,si.stockintype stype,1 as type
                FROM ".DB_PREFIX."storage_stock s
                LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sid ON s.firstfrom_sysno=sid.stockin_sysno
                LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sd ON sd.stock_sysno = s.sysno
                LEFT JOIN ".DB_PREFIX."doc_stock_trans_detail td ON td.out_stock_sysno = sd.sysno
                LEFT JOIN  ".DB_PREFIX."doc_stock_trans st ON st.sysno = td.stocktrans_sysno
                LEFT JOIN ".DB_PREFIX."doc_stock_in si ON si.sysno = s.firstfrom_sysno
                LEFT JOIN ".DB_PREFIX."base_goods g ON g.sysno=s.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON g.sysno=ga.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_unit bu ON ga.unit_sysno=bu.sysno
                WHERE  s.isdel=0 AND s.iscurrent=1 AND s.doctype!=3  AND si.stockindate >= '{$params['begin_time']}' AND si.stockindate <= '{$params['end_time']}' AND ga.isdel=0  AND (si.stockinstatus=4 or IF(si.stockintype=2,si.stockinstatus=3,si.stockinstatus=4) )  AND  s.goods_sysno = {$params['id']}  GROUP BY si.sysno

                UNION ALL

                SELECT g.goodsname,g.sysno goods_sysno,bu.`unitname`,
                SUM(DISTINCT 0-sd.beqty)
                as num,
                so.stockoutno sno,so.stockoutdate date,so.sysno,sd.goodsnature,si.bussinesscheckqty,s.stockqty,sd.beqty,td.transqty,s.shipname,so.stockouttype stype,2 as type
                FROM ".DB_PREFIX."doc_stock_out_detail sd
                LEFT JOIN ".DB_PREFIX."doc_stock_out  so ON sd.stockout_sysno = so.sysno
                LEFT JOIN ".DB_PREFIX."doc_stock_trans_detail td ON td.out_stock_sysno = sd.sysno
                LEFT JOIN  ".DB_PREFIX."doc_stock_trans st ON st.sysno = td.stocktrans_sysno
                LEFT JOIN ".DB_PREFIX."base_goods g ON sd.goods_sysno = g.sysno
                LEFT JOIN ".DB_PREFIX."doc_stock_in_detail si ON si.goods_sysno = g.sysno
                LEFT JOIN ".DB_PREFIX."storage_stock s ON s.goods_sysno = g.sysno
                LEFT JOIN `".DB_PREFIX."base_goods_attribute` AS bga ON bga.`goods_sysno`=g.sysno
                LEFT JOIN `".DB_PREFIX."base_unit` AS bu ON bu.`sysno`=bga.`unit_sysno`
                WHERE   (so.stockoutstatus=4 or IF(so.stockouttype=2,so.stockoutstatus=3,so.stockoutstatus=4)) AND (sd.bussinesscheckqty or sd.beqty)!=0  AND so.stockoutdate >= '{$params['begin_time']}' AND  so.stockoutdate <= '{$params['end_time']}' AND sd.goods_sysno={$params['id']}   AND so.isdel=0 AND g.isdel=0 GROUP BY so.sysno

                UNION ALL

                SELECT g.goodsname,g.sysno goods_sysno, bu.unitname ,
                SUM(DISTINCT td.transqty)
                as num,
                st.stocktransno sno,st.stocktransdate date,td.sysno,si.goodsnature,si.bussinesscheckqty,s.stockqty,sd.beqty,td.transqty,s.shipname,st.stocktransstatus stype,3 as type
                FROM ".DB_PREFIX."doc_stock_trans_detail td
                LEFT JOIN  ".DB_PREFIX."doc_stock_trans st ON st.sysno = td.stocktrans_sysno
                LEFT JOIN  ".DB_PREFIX."doc_stock_in_detail si ON si.stockin_sysno=td.in_stock_sysno
                LEFT JOIN  ".DB_PREFIX."base_goods g ON si.goods_sysno = g.sysno
                LEFT JOIN  ".DB_PREFIX."storage_stock s ON s.goods_sysno = g.sysno
                LEFT JOIN  ".DB_PREFIX."doc_stock_out_detail sd ON sd.stock_sysno = s.sysno
                LEFT JOIN  ".DB_PREFIX."base_goods_attribute ga ON  ga.goods_sysno = g.sysno
                LEFT JOIN  ".DB_PREFIX."base_unit bu ON ga.unit_sysno = bu.sysno
                WHERE  st.stocktransdate >= '{$params['begin_time']}' AND  st.stocktransdate <= '{$params['end_time']}'  AND td.isdel=0 AND g.isdel=0
                AND  si.goods_sysno = {$params['id']}   AND (st.stocktransstatus =4 )
                GROUP BY td.sysno order by type";
        //print_r($sql);die;
        $data = $this->dbh->select($sql);
        if(empty($data)){
            $data = [];
            return $data;
            exit;
        }
        if(!$params['page'] && empty($params['pageSize'])){
            return $data;
        }else{
            $result['totalRow']=count($data);
            $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
            $list=array_chunk($data,$params['pageSize'],false);
            $result['list']=$list[$params['pageCurrent']-1];
            return $result;
        }

    }



    public function getghostnum($params)
    {
        // var_dump($params['begin_time']);exit();
        $params['end_time'] = date('Y-m-d',strtotime("{$params['begin_time']}-1 day"));
        $params['begin_time'] = '';
        // var_dump($params['begin_time']);exit();
        $data = $this->getlist3($params);
        $result = $data[0]['instockqty']+$data[0]['outstockqty']+$data[0]['clearqty'];
//        print_r($data);
        return $result;
    }
}
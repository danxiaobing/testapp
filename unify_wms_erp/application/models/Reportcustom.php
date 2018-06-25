<?php
/**
 * Customer Model
 *
 */

class ReportcustomModel
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


    public function searchCustomer($params) {

    }

    /**
     * 获取客户列表
     * @author wuxianneng
     */
    public function getCustomercollerlist($search){
        $filter = array();
        if (isset($search['startTime']) && $search['startTime'] != '') {
            $filter[0] = "sin.`stockindate` >= '{$search['startTime']}'";
        }
        if (isset($search['endTime']) && $search['endTime'] != '') {
            $filter[1] = "sin.`stockindate` <= '{$search['endTime']}'";
        }
        if (isset($search['customer_sysno']) && $search['customer_sysno'] != '') {
            $filter[2] = "ss.`customer_sysno` = '{$search['customer_sysno']}' ";
        }
        if (isset($search['goods_sysno']) && $search['goods_sysno'] != '') {
            $filter[3] = "ss.`goods_sysno` = '{$search['goods_sysno']}' ";
        }
        $where = ' 1=1 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
//获取出库量，货权转移入出量，清库量
        $sqls = "SELECT DISTINCT(ss.sysno),ss.customername,ss.customer_sysno,ss.goodsname,ss.goods_sysno,SUM(soutd.beqty) as outstockqty,SUM(std_in.transqty) as intransqty,SUM(std_out.transqty) as outtransqty,SUM(clearqty) as clearqty,ghoststockqty,u.unitname
                    FROM ".DB_PREFIX."storage_stock ss
                    LEFT JOIN ".DB_PREFIX."doc_stock_trans_detail std_in ON std_in.in_stock_sysno = ss.sysno
                    LEFT JOIN  ".DB_PREFIX."doc_stock_trans st_in ON st_in.sysno = std_in.stocktrans_sysno   AND st_in.stocktransstatus=4
                    LEFT JOIN ".DB_PREFIX."doc_stock_trans_detail std_out ON std_out.out_stock_sysno = ss.sysno
                    LEFT JOIN  ".DB_PREFIX."doc_stock_trans st_out ON st_out.sysno = std_out.stocktrans_sysno   AND st_out.stocktransstatus=4
                    LEFT  JOIN ".DB_PREFIX."doc_stock_in sin ON sin.sysno = ss.firstfrom_sysno
                    LEFT JOIN  ".DB_PREFIX."doc_stock_out_detail soutd ON soutd.stock_sysno = ss.sysno
                    LEFT JOIN ".DB_PREFIX."doc_stock_out sout ON sout.sysno = soutd.stockout_sysno AND  sout.stockoutstatus=4
                    LEFT JOIN ".DB_PREFIX."base_goods g ON g.sysno = ss.goods_sysno
                    LEFT JOIN ".DB_PREFIX."base_goods_attribute ga on ga.goods_sysno = g.sysno and ga.isdel=0 AND  ga.status<2
                    LEFT JOIN ".DB_PREFIX."base_unit u on u.sysno = ga.unit_sysno and u.isdel=0 AND  u.status<2
                    WHERE ss.iscurrent=1 AND  {$where}
                    GROUP BY ss.customer_sysno,ss.goods_sysno
                    ORDER BY ss.customer_sysno DESC ";;
        $stockdetail = $this->dbh->select($sqls);
//库存数量
        //获取入库量
        if (isset($search['goods_sysno']) && $search['goods_sysno'] != '') {
            $filter[3] = "sind.`goods_sysno` = '{$search['goods_sysno']}' ";
        }
        if (isset($search['customer_sysno']) && $search['customer_sysno'] != '') {
            $filter[2] = "sin.`customer_sysno` = '{$search['customer_sysno']}' ";
        }
        $where = ' 1=1 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        //入库数量
        $sql = "SELECT sin.customername,sin.customer_sysno,sind.goodsname,sind.goods_sysno,SUM(sind.beqty) as instockqty
                FROM  ".DB_PREFIX."doc_stock_in sin
                LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sind ON sin.sysno = sind.stockin_sysno
                WHERE sin.stockinstatus = 4 and sin.isdel = 0 and sin.status <2 AND {$where}
                GROUP BY sin.customer_sysno,sind.goods_sysno
                ORDER BY sin.customer_sysno DESC ";
        $instockqty= $this->dbh->select($sql);
        if(!empty($search['startTime'] && !empty($search['endTime'])  )){
            foreach($stockdetail as $key=>$value){
                foreach($instockqty as $k=>$item){
                    if($value['customer_sysno'] == $item['customer_sysno'] && $value['goods_sysno'] == $item['goods_sysno']){
                        $stockdetail[$key]['instockqty'] = $item['instockqty'];
                    }
                }
                $stockdetail[$key]['endmath'] = round($stockdetail[$key]['instockqty']+$value['intransqty']+$value['ghoststockqty']-$value['outstockqty']-$value['outtransqty'] - $value['clearqty'],2);
                $ghoststockqty = intval($this->getqty($value['goods_sysno'],$value['customer_sysno'],$search['startTime']));
                $stockdetail[$key]['ghoststockqty'] = $ghoststockqty;
            }
            return $stockdetail;
        }else{
            return  array();
        }
    }
    /**
     * 获取货品的期初数量和期末数量
     * @param  [type] $goods_sysno [商品ID]
     * @param  [type] $where       [where条件]
     * @author HR
     */
    public function getqty($goods_sysno,$custom_sysno,$startTime)
    {
        $sql = "SELECT ghoststockqty FROM ".DB_PREFIX."storage_stock WHERE isdel=0 AND iscurrent=0 AND ghosttype in(1,2,3,6) AND instockdate='{$startTime}'  AND goods_sysno={$goods_sysno} AND  customer_sysno = {$custom_sysno} ORDER BY ghosttime LIMIT 0,1";
        //error_log($sql, 3, 'sql_print.txt');
        return $this->dbh->select_one($sql);
    }
    /*
     * 汇总表
     * jp
     * */
    function  getList($search){
        $filter = array();
        if (isset($search['startTime']) && $search['startTime'] != '') {
            $filter[0] = "ss.`instockdate` >= '{$search['startTime']}'";
        }
        if (isset($search['endTime']) && $search['endTime'] != '') {
            $filter[1] = "ss.`instockdate` <= '{$search['endTime']}'";
        }
        if (isset($search['customer_sysno']) && $search['customer_sysno'] != '') {
            $filter[2] = "ss.`customer_sysno` = '{$search['customer_sysno']}' ";
        }
        if (isset($search['goods_sysno']) && $search['goods_sysno'] != '') {
            $filter[3] = "ss.`goods_sysno` = '{$search['goods_sysno']}' ";
        }
        $where = ' 1=1 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        //清库单
        if (isset($search['startTime']) && $search['startTime'] != '') {
            $filter[0] = "sc.`stockcleardate` >= '{$search['startTime']}'";
        }
        if (isset($search['endTime']) && $search['endTime'] != '') {
            $filter[1] = "sc.`stockcleardate` <= '{$search['endTime']}'";
        }
        $where = ' 1=1 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT sc.*,scd.okqty,sum(0-scd.okqty) as clearqty,sc.stockclearno as dateno,sc.stockcleardate as dateTime,ss.goodsnature,ss.customername,ss.goodsname,ss.goods_sysno,ss.customer_sysno,u.unitname,ss.beyondqty
                        FROM ".DB_PREFIX."doc_stock_clear_detail scd
                        LEFT JOIN ".DB_PREFIX."doc_stock_clear sc on sc.sysno = scd.stockclear_sysno
                        LEFT JOIN ".DB_PREFIX."doc_stock_in sin on sin.sysno = scd.stockin_sysno
                        LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.firstfrom_sysno=sin.sysno AND ss.iscurrent=1
                        LEFT JOIN ".DB_PREFIX."base_goods g on g.sysno = ss.goods_sysno and g.isdel =0 and g.status<2
                        LEFT JOIN ".DB_PREFIX."base_goods_attribute ga on ga.goods_sysno = g.sysno and ga.isdel=0 and ga.status<2
                        LEFT JOIN ".DB_PREFIX."base_unit u on u.sysno = ga.unit_sysno and u.isdel = 0 and u.status<2
                        WHERE  ss.doctype in (1,2) and sc.stockclearstatus=4 and  {$where}
                        GROUP BY ss.customer_sysno,ss.goods_sysno";
        $clear = $this->dbh->select($sql);
        //货权转入
        if (isset($search['startTime']) && $search['startTime'] != '') {
            $filter[0] = "st.`stocktransdate` >= '{$search['startTime']}'";
        }
        if (isset($search['endTime']) && $search['endTime'] != '') {
            $filter[1] = "st.`stocktransdate` <= '{$search['endTime']}'";
        }
        $where = ' 1=1 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT st.*,st.stocktransno as dateno,st.stocktransdate as dateTime,SUM(std.transqty) as qty,SUM(std.transqty) as intransqty,ss.customer_sysno,ss.customername,ss.goods_sysno,ss.goodsname,ss.goodsnature,u.unitname,ss.beyondqty
                    FROM ".DB_PREFIX."doc_stock_trans_detail std
                    LEFT JOIN ".DB_PREFIX."doc_stock_trans st ON st.sysno = std.stocktrans_sysno and st.isdel=0 and st.status<2
                    LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = std.in_stock_sysno AND ss.iscurrent=1 and ss.isdel=0 and ss.status<2
                    LEFT JOIN ".DB_PREFIX."base_goods g on g.sysno = ss.goods_sysno
                    LEFT JOIN ".DB_PREFIX."base_goods_attribute ga on ga.goods_sysno = g.sysno and ga.isdel=0 and ga.status<2
                    LEFT JOIN ".DB_PREFIX."base_unit u on u.sysno = ga.unit_sysno and u.isdel = 0 and u.status<2
                    WHERE st.stocktransstatus=4 AND {$where}
                    GROUP  BY ss.customer_sysno,ss.goods_sysno ";
        $trankin = $this->dbh->select($sql);
        //货权转出
        $sql = "SELECT st.*,st.stocktransno as dateno,st.stocktransdate as dateTime,SUM(std.transqty) as qty,SUM(0-std.transqty) as outtransqty,ss.customer_sysno,ss.customername,ss.goods_sysno,ss.goodsname,ss.goodsnature,u.unitname,ss.beyondqty
                    FROM ".DB_PREFIX."doc_stock_trans_detail std
                    LEFT JOIN ".DB_PREFIX."doc_stock_trans st ON st.sysno = std.stocktrans_sysno and st.isdel=0 and st.status<2
                    LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = std.out_stock_sysno and ss.isdel=0 and ss.status<2 and ss.iscurrent = 1
                    LEFT JOIN ".DB_PREFIX."base_goods g ON g.sysno = ss.goods_sysno
                    LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON ga.goods_sysno = g.sysno and ga.isdel=0 and ga.status<2
                    LEFT JOIN ".DB_PREFIX."base_unit u ON u.sysno = ga.unit_sysno and u.isdel = 0 and u.status<2
                    where st.stocktransstatus=4 AND {$where}
                    GROUP  BY ss.customer_sysno,ss.goods_sysno ";
        $trankout = $this->dbh->select($sql);

        //出库单
        if (isset($search['startTime']) && $search['startTime'] != '') {
            $filter[0] = "sout.`stockoutdate` >= '{$search['startTime']}'";
        }
        if (isset($search['endTime']) && $search['endTime'] != '') {
            $filter[1] = "sout.`stockoutdate` <= '{$search['endTime']}'";
        }
        $where = ' 1=1 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT sout.*,sout.stockoutno as dateno,sout.stockoutdate as dateTime,SUM(soutd.beqty) as qty,SUM(0-soutd.beqty) as outstockqty,soutd.goodsnature,soutd.unitname,sout.customername,ss.goods_sysno,sout.customer_sysno,soutd.goodsname,ss.beyondqty
                   FROM ".DB_PREFIX."doc_stock_out_detail soutd
                   LEFT JOIN ".DB_PREFIX."doc_stock_out sout ON sout.sysno = soutd.stockout_sysno
                   LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = soutd.stock_sysno AND ss.iscurrent=1
                   where  (sout.stockoutstatus=4 OR IF(sout.stockouttype=2,sout.stockoutstatus=3,sout.stockoutstatus=4)) AND soutd.beqty !=0  and {$where}
                   GROUP  BY ss.customer_sysno,ss.goods_sysno";
        $stockout = $this->dbh->select($sql);

//库存数量
        //获取入库量
        if (isset($search['startTime']) && $search['startTime'] != '') {
            $filter[0] = "sin.`stockindate` >= '{$search['startTime']}'";
        }
        if (isset($search['endTime']) && $search['endTime'] != '') {
            $filter[1] = "sin.`stockindate` <= '{$search['endTime']}'";
        }
        if (isset($search['goods_sysno']) && $search['goods_sysno'] != '') {
            $filter[3] = "sind.`goods_sysno` = '{$search['goods_sysno']}' ";
        }
        if (isset($search['customer_sysno']) && $search['customer_sysno'] != '') {
            $filter[2] = "sin.`customer_sysno` = '{$search['customer_sysno']}' ";
        }
        $where = ' 1=1 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        //入库数量
        $sql = "SELECT DISTINCT(sind.sysno),sin.customername,sin.customer_sysno,sind.goodsname,sind.goods_sysno,SUM(sind.beqty) as instockqty,IF(ss.clearstockstatus=2,SUM(ss.ullage+ss.clearqty),SUM(ss.ullage)) as lossqty,ss.beyondqty
                FROM  ".DB_PREFIX."doc_stock_in_detail sind
                LEFT JOIN ".DB_PREFIX."doc_stock_in sin ON sin.sysno = sind.stockin_sysno
                LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.firstfrom_sysno = sin.sysno AND ss.iscurrent =1 AND ss.doctype !=3  and ss.isdel =0 and ss.`status`<2
                WHERE (sin.stockinstatus = 4 OR IF(sin.stockintype=2,sin.stockinstatus = 3,sin.stockinstatus = 4)) and sin.isdel = 0 and sin.status <2 AND sind.beqty !=0 AND {$where}
                GROUP BY sin.customer_sysno,sind.goods_sysno
                ORDER BY sin.customer_sysno DESC ";
        $instockqty= $this->dbh->select($sql);

        //查询最后一次损耗量
//        $sql = "SELECT sin.sysno,sin.stockinno,SUM(sind.beqty) as inqty,SUM(soutd.beqty) as outqty,IFNULL(SUM(std.transqty),0) AS trankqty,(SUM(sind.beqty)-SUM(soutd.beqty)-IFNULL(SUM(std.transqty),0)) as lossqty,boutd.lastout,sin.customername,sin.customer_sysno,sind.goodsname,sind.goods_sysno
//                from ".DB_PREFIX."doc_stock_in sin
//                LEFT JOIN ".DB_PREFIX."doc_stock_out_detail soutd ON soutd.stockin_sysno = sin.sysno AND soutd.isdel =0 AND soutd.status<2
//                LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sind ON sind.stockin_sysno = sin.sysno AND sind.isdel =0 AND sind.status<2
//                LEFT JOIN ".DB_PREFIX."doc_stock_out sout ON sout.sysno = soutd.stockout_sysno  AND sout.isdel =0 AND sout.status<2
//                LEFT JOIN ".DB_PREFIX."doc_booking_out bout ON bout.sysno = sout.booking_out_sysno AND bout.isdel =0 AND bout.status<2
//                LEFT JOIN ".DB_PREFIX."doc_booking_out_detail boutd ON boutd.bookingout_sysno = bout.sysno  AND boutd.isdel =0 AND boutd.status<2
//                LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.firstfrom_sysno = sin.sysno AND ss.iscurrent =1 and ss.isdel =1 and ss.`status`<2 and doctype = 3
//                LEFT JOIN ".DB_PREFIX."doc_stock_trans_detail std ON std.out_stock_sysno = ss.sysno and std.isdel = 0 AND std.`status`<2
//                where  (sin.stockinstatus = 4 OR IF(sin.stockintype=2,sin.stockinstatus = 3,sin.stockinstatus = 4)) AND boutd.lastout = 1 AND sin.isdel =0 AND sin.status<2 AND {$where}
//                GROUP BY sin.customer_sysno,soutd.goods_sysno
//                ORDER BY sin.customer_sysno DESC";
      //  $lossqty= $this->dbh->select($sql);

        //没数据返回空
        if(empty($instockqty) && empty($stockout) && empty($trankin) && empty($trankout) ){
            return $result = array();
        }
        $user_sysno_where = 1;
        if (isset($search['customer_sysno']) && $search['customer_sysno'] != '') {
            $user_sysno_where = "`sysno` = '{$search['customer_sysno']}'";
        }

        // 得到所有的客户
        $sql = "select sysno as customer_sysno,customername as customername  from ".DB_PREFIX."customer WHERE  $user_sysno_where";
        $customAll = $this->dbh->select($sql);
        $goods_sysno_where = 1;
        if (isset($search['goods_sysno']) && $search['goods_sysno'] != '') {
            $goods_sysno_where = "`sysno` = '{$search['goods_sysno']}'";
        }
        //得到所有的商品
        $sql = "select sysno as goods_sysno,goodsname as goodsname from ".DB_PREFIX."base_goods  WHERE  $goods_sysno_where";
        $goodsAll = $this->dbh->select($sql);

        foreach($customAll as $k=>$v){
            foreach($goodsAll as $key=>$value){
                $arr[] = array(
                    'customer_sysno' => $customAll[$k]['customer_sysno'],
                    'goods_sysno' => $goodsAll[$key]['goods_sysno'],
                    'customername' => $customAll[$k]['customername'],
                    'goodsname' => $goodsAll[$key]['goodsname'],
                    'startTime'=>$search['startTime'],
                    'endTime'=>$search['endTime'],
                );
            }
        }
        foreach((array)$arr as $key=>$item){
            //入库
            if($instockqty){
                foreach((array)$instockqty as $k=>$v) {
                    if ($item['goods_sysno'] == $v['goods_sysno'] && $item['customer_sysno'] == $v['customer_sysno']) {
                        $arr[$key]['instockqty'] = $v['instockqty'];
                        $arr[$key]['lossqty'] = $v['lossqty'];
                        $arr[$key]['unitname'] = $v['unitname'];
                        $arr[$key]['beyondqty'] = $v['beyondqty'];
                    }
                }
            }

            //出库
            if($stockout) {
                foreach ((array)$stockout as $k => $v) {
                    if ($item['goods_sysno'] == $v['goods_sysno'] && $item['customer_sysno'] == $v['customer_sysno']) {
                        $arr[$key]['outstockqty'] = $v['outstockqty'];
                        $arr[$key]['unitname'] = $v['unitname'];
                        $arr[$key]['beyondqty'] = $v['beyondqty'];
                    }
                }
            }

            //是否最后一次出库
//            if($lossqty) {
//                foreach ((array)$lossqty as $k => $v) {
//                    if ($item['goods_sysno'] == $v['goods_sysno'] && $item['customer_sysno'] == $v['customer_sysno']) {
//                        $arr[$key]['lossqty'] = $v['lossqty'];
//                    }else{
//                        $arr[$key]['lossqty'] =0;
//                    }
//                }
//            }

            // 货权入库
            if($trankin) {
                foreach ((array)$trankin as $k => $v) {
                    if ($item['goods_sysno'] == $v['goods_sysno'] && $item['customer_sysno'] == $v['customer_sysno']) {
                        $arr[$key]['intransqty'] = $v['intransqty'];
                        $arr[$key]['unitname'] = $v['unitname'];
                        $arr[$key]['beyondqty'] = $v['beyondqty'];
                    }
                }
            }
            // 货权出库
            if($trankout) {
                foreach ((array)$trankout as $k => $v) {
                    if ($item['goods_sysno'] == $v['goods_sysno'] && $item['customer_sysno'] == $v['customer_sysno']) {
                        $arr[$key]['outtransqty'] = $v['outtransqty'];
                        $arr[$key]['unitname'] = $v['unitname'];
                        $arr[$key]['beyondqty'] = $v['beyondqty'];
                    }
                }
            }
            //清库
//            if($clear) {
//                foreach((array)$clear as $k=>$v){
//                    if($item['goods_sysno']==$v['goods_sysno'] && $item['customer_sysno']==$v['customer_sysno']){
//                        $arr[$key]['clearqty'] = $v['clearqty'];
//                        $arr[$key]['unitname'] = $v['unitname'];
//                    }
//                }
//            }
        }
        $C = new ReportgoodsModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        foreach($arr as $key=>$item){
         //   if(empty($item['instockqty']) && empty($item['outstockqty']) && empty($item['intransqty'])  && empty($item['outtransqty'])  && empty($item['clearqty']) ){
            if((empty($item['instockqty']) && empty($item['outstockqty']) && empty($item['intransqty'])  && empty($item['outtransqty'])) || ($item['instockqty']==0 && $item['outstockqty']==0 && $item['intransqty']==0  && $item['outtransqty']==0 ) ){
                unset($arr[$key]);
            }else{
                $ghostinfo = array(
                    'goods_sysno'=> $item['goods_sysno'],
                    'customer_sysno'=>$item['customer_sysno'],
                    'begin_time'=>$search['startTime'],
                    'page'=>false,
                );
                $ghoststockqty = floatval($this->getghostNum($ghostinfo));
                $arr[$key]['ghoststockqty'] =round($ghoststockqty,3);
                $arr[$key]['endmath'] = round(1000*($arr[$key]['instockqty']+$ghoststockqty+$item['intransqty']+$item['ghoststockqty']+$item['outstockqty']+$item['outtransqty'] + $item['clearqty']),2)/1000;

                $data[] = $arr[$key];
            }
        }

        if(!$search['page'] && empty($search['pageSize'])){
            return $data;
        }else{
            $result['totalRow']=count($data);
            $result['totalPage'] = ceil($result['totalRow'] / $search['pageSize']);
            $list=array_chunk($data ? $data : [],$search['pageSize'],false);
            $result['list']=$list[$search['pageCurrent']-1];
            return $result;
        }

    }

    public function getCustomerById($id){
        $sql = "SELECT c.*,cc.`categoryname`,u.`employeename` as businessrealname,uu.`employeename` as createrealname FROM `".DB_PREFIX."customer` c left join `".DB_PREFIX."customer_category` cc on (c.`customercategory_sysno`=cc.`sysno`) left join `".DB_PREFIX."system_employee` u on (c.`business_user_sysno`=u.`sysno`) left join  `".DB_PREFIX."system_employee` uu on (c.`created_user_sysno`=uu.`sysno`) WHERE c.`sysno`=".intval($id);
        return $this->dbh->select_row($sql);
    }


    /*
     *
     * 获取客户收发货详细信息
     * jp
     * */
    public  function getDateil($search){
//        print_r($search);die;
        $filter = array();

        if($search['startTime']=='' || $search['endTime']=='' || $search['goods_sysno']=='' ||$search['customer_sysno']=='' ){
            return $result =array();
        }

        //入库单
        if (isset($search['startTime']) && $search['startTime'] != '') {
            $filter[0] = "sin.`stockindate` >= '{$search['startTime']}'";
        }
        if (isset($search['endTime']) && $search['endTime'] != '') {
            $filter[1] = "sin.`stockindate` <= '{$search['endTime']}'";
        }
        if (isset($search['customer_sysno']) && $search['customer_sysno'] != '') {
            $filter[2] = "sin.`customer_sysno` = '{$search['customer_sysno']}' ";
        }
        if (isset($search['goods_sysno']) && $search['goods_sysno'] != '') {
            $filter[3] = "sind.`goods_sysno` = '{$search['goods_sysno']}' ";
        }

        $where = ' 1=1 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT sin.*,sin.stockinno as dateno,sind.shipname as shipname,sin.stockindate as dateTime,sind.goodsnature,sin.customer_sysno,sin.customername,sind.goodsname,sind.goods_sysno,SUM(sind.beqty) as qty,sind.unitname
                 FROM ".DB_PREFIX."doc_stock_in_detail sind
                 LEFT JOIN ".DB_PREFIX."doc_stock_in sin ON sin.sysno = sind.stockin_sysno
                 WHERE (sin.stockinstatus=4 OR IF(sin.stockintype=2,sin.stockinstatus = 3,sin.stockinstatus = 4)) AND sin.isdel = 0 and sin.status <2 AND sind.beqty !=0 AND {$where}
                 GROUP BY sin.sysno";
        $stockin = $this->dbh->select($sql);
        foreach($stockin as $key=>$val){
            $stockin[$key]['type'] = '1';
            $stockin[$key]['qty1'] = $val['qty'];
        }
        $data = $stockin;
//出库单
        if (isset($search['startTime']) && $search['startTime'] != '') {
            $filter[0] = "sout.`stockoutdate` >= '{$search['startTime']}'";
        }
        if (isset($search['endTime']) && $search['endTime'] != '') {
            $filter[1] = "sout.`stockoutdate` <= '{$search['endTime']}'";
        }
        if (isset($search['customer_sysno']) && $search['customer_sysno'] != '') {
            $filter[2] = "ss.`customer_sysno` = '{$search['customer_sysno']}' ";
        }
        if (isset($search['goods_sysno']) && $search['goods_sysno'] != '') {
            $filter[3] = "ss.`goods_sysno` = '{$search['goods_sysno']}' ";
        }
        $where = ' 1=1 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT DISTINCT(soutd.sysno),sout.*,soutd.shipname,sout.stockoutno as dateno,sout.stockoutdate as dateTime,SUM(0-soutd.beqty) as qty,soutd.goodsnature,soutd.unitname,sout.customername,ss.goods_sysno,sout.customer_sysno,soutd.goodsname
               FROM ".DB_PREFIX."doc_stock_out_detail soutd
               LEFT JOIN ".DB_PREFIX."doc_stock_out sout ON sout.sysno = soutd.stockout_sysno and sout.isdel = 0 and sout.status<2
               LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = soutd.stock_sysno and ss.iscurrent=1 AND ss.isdel =0 and ss.status<2
               WHERE  (sout.stockoutstatus=4 OR IF(sout.stockouttype=2,sout.stockoutstatus=3,sout.stockoutstatus=4)) and soutd.stocktype =1 AND soutd.beqty !=0 and {$where}
               GROUP BY sout.sysno";
        $stockout = $this->dbh->select($sql);
        foreach($stockout as $key=>$val){
            $stockout[$key]['type'] = '2';
            $stockout[$key]['qty2'] = $val['qty'];
            $data[] =  $stockout[$key];
        }

//货权转入
        if (isset($search['startTime']) && $search['startTime'] != '') {
            $filter[0] = "st.`stocktransdate` >= '{$search['startTime']}'";
        }
        if (isset($search['endTime']) && $search['endTime'] != '') {
            $filter[1] = "st.`stocktransdate` <= '{$search['endTime']}'";
        }
        $where = ' 1=1 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT DISTINCT(std.sysno),st.*,st.stocktransno as dateno,st.stocktransdate as dateTime,SUM(std.transqty) as qty,ss.customer_sysno,ss.shipname,ss.customername,ss.goods_sysno,ss.goodsname,ss.goodsnature,u.unitname,sin.stockintype
                FROM ".DB_PREFIX."doc_stock_trans_detail std
                LEFT JOIN ".DB_PREFIX."doc_stock_trans st ON st.sysno = std.stocktrans_sysno and st.isdel =0 and st.status<2
                LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = std.in_stock_sysno and ss.iscurrent=1 and ss.isdel=0
                LEFT JOIN ".DB_PREFIX."doc_stock_in sin ON sin.sysno = ss.firstfrom_sysno and sin.status<2 and sin.isdel=0 and sin.stockinstatus=4
                LEFT JOIN ".DB_PREFIX."base_goods g on g.sysno = ss.goods_sysno and g.isdel = 0 and g.status<2
                LEFT JOIN ".DB_PREFIX."base_goods_attribute ga on ga.goods_sysno = g.sysno and ga.isdel = 0 and ga.status<2
                LEFT JOIN ".DB_PREFIX."base_unit u on u.sysno = ga.unit_sysno and u.isdel=0 and u.status<2
                WHERE st.stocktransstatus=4 and ss.doctype=3  and ss.iscurrent=1 AND {$where}
                GROUP BY st.sysno";
        $trankin = $this->dbh->select($sql);
        foreach($trankin as $key=>$val){
            $trankin[$key]['type'] = '3';
            $trankin[$key]['qty3'] = $val['qty'];
            $data[] =  $trankin[$key];
        }
//货权转出
        $sql = "SELECT DISTINCT(std.sysno),st.*,st.stocktransno as dateno,st.stocktransdate as dateTime,SUM(0-std.transqty) as qty,ss.shipname,ss.customer_sysno,ss.customername,ss.goods_sysno,ss.goodsname,ss.goodsnature,u.unitname,sin.stockintype
                FROM ".DB_PREFIX."doc_stock_trans_detail std
                LEFT JOIN ".DB_PREFIX."doc_stock_trans st ON st.sysno = std.stocktrans_sysno AND st.isdel=0 AND st.status<2
                LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = std.out_stock_sysno AND ss.iscurrent=1 AND  ss.isdel=0
                LEFT JOIN ".DB_PREFIX."doc_stock_in sin ON sin.sysno = ss.firstfrom_sysno and sin.status<2 and sin.isdel=0 and sin.stockinstatus=4
                LEFT JOIN ".DB_PREFIX."base_goods g ON g.sysno = ss.goods_sysno AND g.isdel = 0 AND g.status<2
                LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON ga.goods_sysno = g.sysno AND ga.isdel = 0 AND ga.status<2
                LEFT JOIN ".DB_PREFIX."base_unit u ON u.sysno = ga.unit_sysno AND u.isdel=0 AND u.status<2
                WHERE st.stocktransstatus=4  AND {$where}
                GROUP BY st.sysno";
        $trankout = $this->dbh->select($sql);
      //  print_r($trankout);die;
        foreach($trankout as $key=>$val){
            $trankout[$key]['type'] = '4';
            $trankout[$key]['qty4'] = $val['qty'];
            $data[] =   $trankout[$key];
        }

//清库单
        if (isset($search['startTime']) && $search['startTime'] != '') {
            $filter[0] = "sc.`stockcleardate` >= '{$search['startTime']}'";
        }
        if (isset($search['endTime']) && $search['endTime'] != '') {
            $filter[1] = "sc.`stockcleardate` <= '{$search['endTime']}'";
        }
        $where = ' 1=1 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT bb.* FROM (SELECT sc.sysno as sysno,sc.stockclearno as dateno,sc.stockcleardate as dateTime,(0-ss.clearqty) as clearqty,(0-scd.okqty) as qty,ss.goodsnature,ss.customername,ss.goodsname,ss.goods_sysno,ss.customer_sysno,u.unitname
                    FROM ".DB_PREFIX."doc_stock_clear_detail scd
                    LEFT JOIN ".DB_PREFIX."doc_stock_clear sc ON sc.sysno = scd.stockclear_sysno
                    LEFT JOIN ".DB_PREFIX."doc_stock_in sin ON sin.sysno = scd.stockin_sysno AND sin.isdel = 0 AND sin.status<2
                    LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.firstfrom_sysno=sin.sysno AND ss.iscurrent=1 AND ss.isdel=0 AND ss.status<2
                    LEFT JOIN ".DB_PREFIX."base_goods g ON g.sysno = ss.goods_sysno AND g.isdel = 0 AND g.status<2
                    LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON ga.goods_sysno = g.sysno AND ga.isdel = 0 AND ga.status<2
                    LEFT JOIN ".DB_PREFIX."base_unit u ON u.sysno = ga.unit_sysno AND u.isdel=0 AND u.status<2
                    WHERE  sc.stockclearstatus=4  AND {$where}
                     GROUP BY scd.sysno) as bb
                    ";
        $clear = $this->dbh->select($sql);
        foreach($clear as $key=>$val){
            $clear[$key]['type'] = '5';
            $clear[$key]['qty5'] = $val['qty'];
            $data[] = $clear[$key];
        }
        $result['count'] = 0;
        $data[0]['balanceqty'] = $data[0]['qty'];
      //  print_r($data);die;
        foreach($data as $k=>$value){
            if($value['qty']==0){
                unset($data[$k]);
            }
            $result['count'] += $value['qty'];
            if($k>0){
                $data[$k]['balanceqty'] = $data[$k-1]['balanceqty']+$data[$k]['qty'];
            }

        }
        if(empty($data)){
            $data = [];
            return $data;
            exit;
        }
        //  error_log($sql, 3, 'sql_print.txt');die;
        //分页
        if($search['page']==false && isset($search['page'])){
            $result['list'] = $data;

        }else{
            $result['totalRow']=count($data);
            $result['totalPage'] = ceil($result['totalRow'] / $search['pageSize']);
            $list=array_chunk($data ? $data : [], $search['pageSize'], false);
            $result['list']=$list[$search['pageCurrent']-1];

        }

        return  $result;
    }

//获取期初数量
    public function getghostNum($params)
    {
        $params['endTime'] = date('Y-m-d',strtotime("{$params['begin_time']}-1 day")).' 23:59:59';
        $params['startTime'] = '1970-01-01';
        $data = $this->getDateil($params);
        $ghostNum = 0;
//        print_r($params);exit();
        if($data['list']){
            foreach($data['list'] as $value){
                $ghostNum += $value['qty'];
            }
        }
        return $ghostNum;
    }

}

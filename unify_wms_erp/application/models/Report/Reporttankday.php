<?php

/**
 * Created by PhpStorm.
 * User: 129
 * Date: 2017/5/15
 * Time: 17:22
 */
class Report_ReporttankdayModel {
    /**
     * 数据库类实例
     *
     * @var object
     */
    public $dbh = null;

    public $mch = null;

    /**
     * Report_ReporttankdayModel constructor.
     * @param $dbh
     * @param null $mch
     */
    public function __construct($dbh, $mch = null) {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    /**
     * 获取入库单数据
     */
    public function getList($params){
        $result = $params;
        $filter = array();

//        if (isset($params['endtime']) && $params['endtime'] != '') {
//            $filter[] = " grl.`created_at` < '{$params['endtime']}'";
//        }

        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " grl.`customer_sysno` = '{$params['customer_sysno']}'";
        }
        if (isset($params['storagetank_sysno']) && $params['storagetank_sysno'] != '') {
            $filter[] = " grl.`storagetank_sysno` = '{$params['storagetank_sysno']}'";
        }

        if (isset($params['goodsname']) && $params['goodsname'] != '') {
            $filter[] = " grl.`goodsname` LIKE '%{$params['goodsname']}%'";
        }

        $where = 'WHERE grl.isdel = 0 AND grl.`status` = 1 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $ullagedate = date('Y-m-d H:i:s',strtotime('+1 days',strtotime($params['tankdaydate'].'00:01:00')));
        $yesendtime = date('Y-m-d H:i:s',strtotime('-1 days',strtotime($params['endtime'])));

        //查询所有库存结存量
        $sql = "SELECT grl.sysno,grl.stock_sysno,grl.customername,grl.goodsname,date(grl.doc_time) doc_time,ifnull(grl.shipname,'槽车进货') AS shipname,SUM(grl.ullage) AS wastage,(SUM(grl.beqty) - SUM(grl.ullage)) as end_num,grl.storagetankname
                FROM ".DB_PREFIX."doc_goods_record_log grl $where AND if(grl.doc_type = 15,grl.`created_at` < '$ullagedate',grl.`created_at` < '{$params['endtime']}')
                GROUP BY grl.stock_sysno,grl.customername,grl.storagetankname
                HAVING SUM(grl.beqty) - SUM(grl.ullage) >= 0";
        $data = $this->dbh->select($sql);

        //查询前一天库存量为零的库存
        $sql = "SELECT grl.sysno,grl.stock_sysno,grl.customername,date(grl.doc_time) doc_time,(SUM(grl.beqty) - SUM(grl.ullage)) as yesend_num
                FROM ".DB_PREFIX."doc_goods_record_log grl $where AND if(grl.doc_type = 15,grl.`created_at` < '{$params['endtime']}',grl.`created_at` < '$yesendtime')
                GROUP BY grl.stock_sysno,grl.customername,grl.storagetankname
                HAVING SUM(grl.beqty) - SUM(grl.ullage) = 0";
        $yesdata = $this->dbh->select($sql);

        if(!empty($data)){
            foreach($data as $key =>$item){
                if(!empty($yesdata)){
                    foreach($yesdata as $val){
                        if($item['stock_sysno'] == $val['stock_sysno']&&$item['customername'] == $val['customername'] && $item['end_num']<=0){
                            unset($data[$key]);
                            break;
                        }
                    }
                }
            }
        }

        //商检量
        $sql = "SELECT stock_sysno,customername,SUM(beqty) as beqty
                    FROM ".DB_PREFIX."doc_goods_record_log
                    WHERE beqty > 0 AND isdel = 0 AND doc_type NOT IN (26) AND created_at <='{$params['endtime']}'
                    GROUP BY stock_sysno,customername,storagetankname";
        $beqty = $this->dbh->select($sql);

        //昨日结存量
        $sql = "SELECT stock_sysno,customername,SUM(beqty) - SUM(ullage) AS qichu
                    FROM ".DB_PREFIX."doc_goods_record_log
                    WHERE isdel = 0 AND IF(doc_type = 15,created_at <= '$ullagedate',created_at <='{$params['tankdaydate']}')
                    GROUP BY stock_sysno,customername,storagetankname";
        $qichu = $this->dbh->select($sql);

        //今日出库量 = 船出+车出+管出-退货
        $sql = "SELECT stock_sysno,customername,SUM(beqty) as out_num
                    FROM ".DB_PREFIX."doc_goods_record_log
                    WHERE doc_type IN (3,4,12,26) AND isdel = 0 AND created_at>='{$params['tankdaydate']}' AND created_at <='{$params['endtime']}'
                    GROUP BY stock_sysno,customername,storagetankname";
        $out_num = $this->dbh->select($sql);

        //今日货转出量
        $sql = "SELECT stock_sysno,customername,SUM(beqty) as transout
                    FROM ".DB_PREFIX."doc_goods_record_log
                    WHERE isdel = 0 AND doc_type in (6,14) AND created_at >='{$params['tankdaydate']}' AND created_at <='{$params['endtime']}'
                    GROUP BY stock_sysno,customername,storagetankname";
        $transout = $this->dbh->select($sql);

        //今日倒出量
        $sql = "SELECT stock_sysno,customername,SUM(beqty) as tankout
                    FROM ".DB_PREFIX."doc_goods_record_log
                    WHERE isdel = 0 AND doc_type IN (8,17) AND beqty < 0 AND created_at >='{$params['tankdaydate']}' AND created_at <='{$params['endtime']}'
                    GROUP BY stock_sysno,customername,storagetankname";
        $tankout = $this->dbh->select($sql);

        if(!empty($data)){
            foreach($data as $key =>$item){
                //商检量 并入库存中
                if(!empty($beqty)){
                    foreach ($beqty as $val) {
                        if($item['stock_sysno'] == $val['stock_sysno']&&$item['customername'] == $val['customername']){
                            $data[$key]['beqty'] = $val['beqty'];
                            break;
                        }
                    }
                }
                //昨日结存量 并入库存中
                if(!empty($qichu)){
                    foreach ($qichu as $val) {
                        if($item['stock_sysno'] == $val['stock_sysno']&&$item['customername'] == $val['customername']){
                            $data[$key]['qichu'] = $val['qichu'];
                            break;
                        }
                    }
                }
                //今日出库量 并入库存中
                if(!empty($out_num)){
                    foreach ($out_num as $val) {
                        if($item['stock_sysno'] == $val['stock_sysno']&&$item['customername'] == $val['customername']){
                            $data[$key]['out_num'] = abs($val['out_num']);
                            break;
                        }
                    }
                }
                //今日货转出量 并入库存中
                if(!empty($transout)){
                    foreach ($transout as $val) {
                        if($item['stock_sysno'] == $val['stock_sysno']&&$item['customername'] == $val['customername']){
                            $data[$key]['transout'] = abs($val['transout']);
                            break;
                        }
                    }
                }
                //今日倒出量 并入库存中
                if(!empty($tankout)){
                    foreach ($tankout as $val) {
                        if($item['stock_sysno'] == $val['stock_sysno']&&$item['customername'] == $val['customername']){
                            $data[$key]['tankout'] = abs($val['tankout']);
                            break;
                        }
                    }
                }
            }
        }

        /*
        if(!empty($data)){
            foreach($data as $key =>$item){
                //商检量
                $sql = "SELECT SUM(beqty) as beqty
                    FROM ".DB_PREFIX."doc_goods_record_log
                    WHERE beqty > 0 AND isdel = 0 AND created_at <='{$params['endtime']}' AND stock_sysno = {$item['stock_sysno']} AND customername = '{$item['customername']}' ";
                $beqty = $this->dbh->select_one($sql);
                $data[$key]['beqty'] = $beqty;

                //昨日结存量 = 结存量 - 损耗量
                $sql = "SELECT SUM(beqty) AS beqty
                    FROM ".DB_PREFIX."doc_goods_record_log
                    WHERE isdel = 0 AND created_at <='{$params['tankdaydate']}' AND stock_sysno = {$item['stock_sysno']} AND customername = '{$item['customername']}' ";
                $beqty = $this->dbh->select_one($sql);
                $sql = "SELECT SUM(ullage) AS ullage
                    FROM ".DB_PREFIX."doc_goods_record_log
                    WHERE isdel = 0 AND created_at <='$ullagedate' AND stock_sysno = {$item['stock_sysno']} AND customername = '{$item['customername']}' ";
                $ullage = $this->dbh->select_one($sql);
                $data[$key]['qichu'] = ($beqty - $ullage)<=0?0:($beqty - $ullage);

                //今日出库量
                $sql = "SELECT SUM(beqty) as out_num
                    FROM ".DB_PREFIX."doc_goods_record_log
                    WHERE doc_type IN (3,4,12) AND isdel = 0 AND beqty<0 AND created_at>='{$params['tankdaydate']}' AND created_at <='{$params['endtime']}' AND stock_sysno = {$item['stock_sysno']} AND customername = '{$item['customername']}' ";
                $out_num = $this->dbh->select_one($sql);
                $data[$key]['out_num'] = abs($out_num);

                //今日货转出量
                $sql = "SELECT SUM(beqty) as transout
                    FROM ".DB_PREFIX."doc_goods_record_log
                    WHERE isdel = 0 AND doc_type = 6 AND created_at >='{$params['tankdaydate']}' AND created_at <='{$params['endtime']}' AND stock_sysno = {$item['stock_sysno']} AND customername = '{$item['customername']}' ";
                $transout = $this->dbh->select_one($sql);
                $data[$key]['transout'] = - $transout;

                //今日倒出量
                $sql = "SELECT SUM(beqty) as tankout
                    FROM ".DB_PREFIX."doc_goods_record_log
                    WHERE isdel = 0 AND doc_type IN (8,17) AND beqty < 0 AND created_at >='{$params['tankdaydate']}' AND created_at <='{$params['endtime']}' AND stock_sysno = {$item['stock_sysno']} AND customername = '{$item['customername']}' ";
                $tankout = $this->dbh->select_one($sql);
                $data[$key]['tankout'] = - $tankout;

                $data[$key]['end_num'] = $data[$key]['end_num'];
                if($data[$key]['end_num'] <= 0 && $data[$key]['qichu'] <= 0){
                    unset($data[$key]);
                }
            }
        */


        $data = $this->list_sort_by($data, 'customername', 'asc');

        $temp = $data[0];
        $index = 0;
        foreach ($data as $item) {
            if($item['customername'] == $temp['customername']){
                $arr[$index][] = $item ;

            }else{
                $temp = $item ;
                $index ++ ;
                $arr[$index][] = $item ;
            }

            $arr[$index] = $this->list_sort_by($arr[$index], 'shipname', 'asc');
        }

        unset($data);
        $data = array();
        for($i = 0 ;$i < count($arr) ;$i ++){
            if(is_array($arr[$i]))
                $data = array_merge_recursive($data,$arr[$i]);
        }

        $res = array();
        if(empty($data)){
            $data = array();
        }else{
            foreach ($data as $item) {
                if($params['shipname']!=''){
                    if($item['shipname']==$params['shipname']){
                        $res[] = $item;
                    }
                }else{
                    $res[] = $item;
                }
            }
        }
        if(isset($params['page'] ) && $params['page'] == false){
            $result['totalRow'] = count($res);
            $result['totalPage'] = ceil($result['totalRow'] /($params['pageSize']) );//$params['pageSize']
            $result['list']= $res;
            return $result;
        }else{
            $result['totalRow'] = count($res);
            $result['totalPage'] = ceil($result['totalRow'] /($params['pageSize']) );//$params['pageSize']
            $list=array_chunk($res, $params['pageSize'],false);
            $result['list']= $list [$params['pageCurrent']-1] ? $list [$params['pageCurrent']-1] : [];
            return $result;
        }

    }

    /*
     * 二维数组排序
     */
    public function list_sort_by($list, $field, $sortby = 'asc')
    {
        if (is_array($list))
        {
            $refer = $resultSet = array();
            foreach ($list as $i => $data)
            {
                $refer[$i] = &$data[$field];
            }
            switch ($sortby)
            {
                case 'asc': // 正向排序
                    asort($refer);
                    break;
                case 'desc': // 逆向排序
                    arsort($refer);
                    break;
                case 'nat': // 自然排序
                    natcasesort($refer);
                    break;
            }
            foreach ($refer as $key => $val)
            {
                $resultSet[] = &$list[$key];
            }
            return $resultSet;
        }
        return false;
    }

    /**
     * 获取入库单数据
     */
    public function getList2($params){
        $filter = array();
        if (isset($params['select_data']) && $params['select_data'] != '') {
            $filter[] = " `ghosttime` > '{$params['select_data']}'";
        }
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " `goods_sysno` = '{$params['goods_sysno']}'";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " `customer_sysno` = '{$params['customer_sysno']}'";
        }

        $where = ' isdel = 0 AND `status` = 1 ';

        //入库
        $sql = "SELECT bs.sysno,bs.storagetankname,si.stockinno , si.customer_sysno,si.customername,sid.goods_sysno,sid.goodsname,si.stockindate,sid.shipname,sid.tobeqty,sid.beqty,si.stockintype
                FROM ".DB_PREFIX."doc_stock_in si
                LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sid ON sid.stockin_sysno = si.sysno
                LEFT JOIN ".DB_PREFIX."base_storagetank bs ON bs.sysno = sid.storagetank_sysno
                WHERE si.isdel = 0 AND si.stockinstatus = 4";

        $stockindata = $this->dbh->select($sql);
//        echo "<pre>";print_r($sql);die;
        //货转
        $sql = "SELECT bs.sysno,bs.storagetankname,st.sale_customer_sysno ascustomer_sysno ,st.sale_customername as customername,st.stocktransdate as stockindate,std.transqty as beqty
                FROM ".DB_PREFIX."doc_stock_trans st
                LEFT JOIN ".DB_PREFIX."doc_stock_trans_detail std ON std.stocktrans_sysno = st.sysno
                LEFT JOIN ".DB_PREFIX."base_storagetank bs ON bs.sysno = std.storagetank_sysno
                WHERE st.isdel = 0 AND  st.stocktransstatus = 4";

        $stocktransdata = $this->dbh->select($sql);
        $result =  $stockindata+$stocktransdata;

        foreach($result as &$value){
            $chuSeach = [
                'data' => $params['select_data'] ? $params['select_data'] : date('Y-m-d'),
                'goods_sysno' => $value['goods_sysno'],
                'customer_sysno' => $value['customer_sysno'],
                'firstfrom_sysno' => $value['stockinno'],
            ];
            $value['qichu'] = self::getUserTankNum($chuSeach);

            $outSeach =  [
                'data' => $params['select_data'] ? $params['select_data'] : date('Y-m-d'),
                'goods_sysno' => $value['goods_sysno'],
                'customer_sysno' => $value['customer_sysno'],
                'firstfrom_sysno' => $value['stockinno'],
            ];
            $value['out_num'] = self::getUserTankOutNum($outSeach);

            $outSeach = [
                'data' => $params['select_data'] ?  date('Y-m-d', strtotime ("+1 day", strtotime($params['select_data']))) : date('Y-m-d', strtotime ("+1 day", strtotime(date('Y-m-d')))),
                'goods_sysno' => $value['goods_sysno'],
                'customer_sysno' => $value['customer_sysno'],
                'firstfrom_sysno' => $value['stockinno'],
            ];


            $value['end_num'] = self::getUserTankNum($outSeach);
        }
        return $result;
    }


    //根据时间 罐号 客户 查询期初数量
    public  function getUserTankNum($params){
        $filter = array();
        if (isset($params['data']) && $params['data'] != '') {

            $filter[] = " `ghosttime` < '{$params['data']}'";
        }
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " `goods_sysno` = '{$params['goods_sysno']}'";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " `customer_sysno` = '{$params['customer_sysno']}'";
        }

        $where = ' isdel = 0 AND `status` = 1 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
//        $sql ="SELECT sum(beqty) as beqty FROM ".DB_PREFIX."doc_stock_out so LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sod on so.sysno=sod.stockout_sysno WHERE                            {$where} GROUP BY storagetank_sysno,customer_sysno,goods_sysno";
        //入库总数
        $sql = "SELECT (SUM(ss.instockqty)-SUM(outstockqty)) beqty FROM (SELECT firstfrom_sysno,instockqty,outstockqty FROM ".DB_PREFIX."storage_stock WHERE {$where} GROUP BY firstfrom_sysno) ss";
//        echo $sql;die;
        $num = $this -> dbh -> select_one($sql);

        return $num ? $num : 0 ;
    }

    //根据时间 罐号 客户 查询期初数量
    public  function getUserTankOutNum($params){
        $filter = array();
        if (isset($params['data']) && $params['data'] != '') {
            $end_data = date('Y-m-d', strtotime ("+1 day", strtotime($params['data'])));
            $filter[] = " `ghosttime` < '{$end_data}'";
        }
        if (isset($params['data']) && $params['data'] != '') {
            $filter[] = " `ghosttime` > '{$params['data']}'";
        }
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " `goods_sysno` = '{$params['goods_sysno']}'";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " `customer_sysno` = '{$params['customer_sysno']}'";
        }

        $where = ' isdel = 0 AND `status` = 1 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
//        $sql ="SELECT sum(beqty) as beqty FROM ".DB_PREFIX."doc_stock_out so LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sod on so.sysno=sod.stockout_sysno WHERE                            {$where} GROUP BY storagetank_sysno,customer_sysno,goods_sysno";
        //入库总数
        $sql = "SELECT SUM(outstockqty) beqty FROM (SELECT firstfrom_sysno,instockqty,outstockqty FROM ".DB_PREFIX."storage_stock WHERE {$where} GROUP BY firstfrom_sysno) ss";
//        echo $sql;die;
        $num = $this -> dbh -> select_one($sql);

        return $num ? $num : 0 ;
    }
}
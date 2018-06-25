<?php

class Report_BucketModel
{
    /**
     * 数据库类实例
     *
     * @var object
     */
    public $dbh = null;

    /**
     * 缓存数据类实例
     * @var object
     */
    public $mch = null;

    /**
     * Report_BucketModel constructor.
     * @param $dbh
     * @param null $mch
     */
    public function __construct($dbh, $mch = null)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    /**
     * 桶车数据搜索
     * @param $params 搜索参数
     * @return array
     */
    public function search($params)
    {
        $filter = array();
        if (isset($params['select_data']) && $params['select_data'] != '') {
            $start_time = date('Y-m-01', strtotime($params['select_data']));
            $end_time = date('Y-m-t', strtotime($params['select_data']));
            $filter[] = " st.`updated_at` > '{$start_time} 00:00:00' ";
            $filter[] = " st.`updated_at` <= '{$end_time} 23:59:59'";
            $filter_balance[] = " st.`count_time` > '{$start_time} 00:00:00'";
            $filter_balance[] = " st.`count_time` <= '{$end_time} 23:59:59'";
        }

        $where = '';
        $where_balance = '';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
            $where_balance .= ' AND ' . implode(' AND ', $filter_balance);
        }
        //车出库
        $sql = "SELECT DATE_FORMAT(st.updated_at, '%Y-%m-%d') `day`,count(*) pound_count,IF(SUM(st.bucketnumber) != 0, SUM(st.bucketnumber), 0) bucketnumber
                FROM ".DB_PREFIX."doc_pounds_out st WHERE st.poundsoutstatus = 4 AND st.isdel = 0 {$where} GROUP BY `day`";
        $carResult = $this-> dbh-> select($sql);

        //船出库
        $shipSql = "SELECT DATE_FORMAT(st.updated_at, '%Y-%m-%d') `day`,count(*) ship_count FROM ".DB_PREFIX."doc_stock_out st
                    WHERE st.stockoutstatus = 4 AND st.isdel = 0 {$where} GROUP BY `day`";
        $shipResult = $this -> dbh -> select($shipSql);

        //每天每个商品的出库桶数统计
        $everyDayGoodsNumSql = "SELECT DATE_FORMAT(st.updated_at, '%Y-%m-%d') `day`, IF (SUM(st.bucketnumber) != 0, SUM(st.bucketnumber), 0 ) bucketnumber,
                                st.goodsname,st.goods_sysno FROM ".DB_PREFIX."doc_pounds_out st
                                WHERE st.poundsoutstatus = 4 AND st.isdel = 0 {$where} GROUP BY `day`,st.goods_sysno";
        $everyDayGoodsNum = $this -> dbh -> select($everyDayGoodsNumSql);
        foreach($carResult as &$value){
            foreach($everyDayGoodsNum as $item){
                if($value['day'] == $item['day']){
                    $value[$item['goods_sysno']] = $item['bucketnumber'];
                }
            }
        }
        //每天出库发货统计
        $everyDayCountSql = "SELECT DATE_FORMAT(st.updated_at, '%Y-%m-%d') `day`,sum(hdsod.beqty) count_out
                            FROM ".DB_PREFIX."doc_stock_out st
                            LEFT JOIN ".DB_PREFIX."doc_stock_out_detail hdsod ON hdsod.stockout_sysno = st.sysno
                            WHERE st.stockoutstatus = 4 AND st.isdel=0 AND hdsod.`status` =1 {$where} GROUP BY `day`";
        $everyDayCount = $this -> dbh -> select($everyDayCountSql);

        //每天堆桶出库发货统计
        $everyDayBucketSql = "SELECT DATE_FORMAT(st.ghosttime, '%Y-%m-%d') `day`,sum(st.outstockqty) bucket_out FROM ".DB_PREFIX."storage_stock st
                            LEFT JOIN ".DB_PREFIX."base_storagetank hbs ON st.goods_sysno = hbs.goods_sysno
                            WHERE st.isdel = 0 AND st.`status` = 1 AND st.ghosttype = 2 AND hbs.area_sysno = 7 {$where} GROUP BY `day`";
        $everyDayBucket = $this -> dbh -> select($everyDayBucketSql);

        //结存
        $balancesql = "SELECT st.count_time `day`,st.beqty,st.bucket_qty FROM `".DB_PREFIX."day_balance` st WHERE st.type = 1 {$where_balance}";
        $balanceRes = $this-> dbh -> select($balancesql);
        $result = array_merge($balanceRes, $carResult, $shipResult, $everyDayCount, $everyDayBucket);
        $returnRes = self::merageArray($result);

        //统计总磅码单数 车和船之和
        foreach($returnRes as  &$val){
            $val['count'] =  (isset($val['pound_count']) ? $val['pound_count'] : 0 ) + (isset($val['ship_count']) ? $val['ship_count'] : 0);
        }

        //获取所有的商品 用于合计
        $allGoods = $this -> getAllGoods();
        $allGoodsIdArr = [];
        foreach($allGoods as $item){
            $allGoodsIdArr[] = $item['sysno'];
        }
        $countKey = count($returnRes);
        $returnRes[$countKey]['day'] = '合计';
        //追加需要统计的下标
//        'beqry','bucket_qty'
        array_unshift($allGoodsIdArr, 'count_out', 'count', 'bucket_out', 'bucketnumber');
        foreach($returnRes as $key => $value){
            foreach($allGoodsIdArr as $items){
                $returnRes[$countKey][$items] += $value[$items];
            }
        }

        //分页
        if(isset($params['page']) && $params['page'] == false){
            $data['list'] = $returnRes;
        }else{
            if(!empty($returnRes)){
                $data['totalRow'] = count($returnRes);
                $data['totalPage'] = ceil($data['totalRow'] / $params['pageSize']);
                $list = array_chunk($returnRes, $params['pageSize'], false);
                $data['list'] = $list[$params['pageCurrent'] - 1];
            }else{
                $data['list'] = [];
            }
        }
        return $data;
    }

    /**
     * 槽车数据搜索
     * @param $params 搜索参数
     * @return array
     */
    public function tankSearch($params)
    {
        $filter = array();
        if (isset($params['select_data']) && $params['select_data'] != '') {
            $start_time = date('Y-m-01', strtotime($params['select_data']));
            $end_time = date('Y-m-t', strtotime($params['select_data']));
            $filter[] = " st.`updated_at` > '{$start_time} 00:00:00'";
            $filter[] = " st.`updated_at` < '{$end_time} 23:59:59'";
        }

        $where = '';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        //车出库
        $sql = "SELECT DATE_FORMAT(st.updated_at, '%Y-%m-%d') `day`,count(*) pound_count,IF(SUM(st.bucketnumber) != 0, SUM(st.bucketnumber), 0) bucketnumber
                FROM ".DB_PREFIX."doc_pounds_out st WHERE st.poundsoutstatus = 4 AND st.isdel = 0 {$where} GROUP BY `day`";
        $carResult = $this-> dbh-> select($sql);

        //船出库
        $shipSql = "SELECT DATE_FORMAT(st.updated_at, '%Y-%m-%d') `day`,count(*) ship_count FROM ".DB_PREFIX."doc_stock_out st
                    WHERE st.stockoutstatus = 4 AND st.isdel = 0 {$where} GROUP BY `day`";
        $shipResult = $this -> dbh -> select($shipSql);

        //每天每个商品的出库磅码单统计
        $everyDayGoodsNumSql = "SELECT DATE_FORMAT(st.updated_at, '%Y-%m-%d') day,count(*) goods_pount,
                                st.goodsname,st.goods_sysno FROM ".DB_PREFIX."doc_pounds_out st
                                WHERE st.poundsoutstatus = 4 AND st.isdel = 0 {$where} GROUP BY `day`,st.goods_sysno";
        $everyDayGoodsNum = $this -> dbh -> select($everyDayGoodsNumSql);
        foreach($carResult as &$value){
            foreach($everyDayGoodsNum as $item){
                if($value['day'] == $item['day']){
                    $value[$item['goods_sysno']] = $item['goods_pount'];
                }
            }
        }

//        //每天出库发货统计
//        $everyDayCountSql = "SELECT DATE_FORMAT(ghosttime, '%Y-%m-%d') day, sum(outstockqty) count_out FROM ".DB_PREFIX."storage_stock
//                            WHERE isdel = 0 AND `status` = 1 AND ghosttype = 2 GROUP BY day";
//        $everyDayCount = $this -> dbh -> select($everyDayCountSql);
//
//        //每天出库发货统计
//        $everyDayBucketSql = "SELECT DATE_FORMAT(ghosttime, '%Y-%m-%d') day,sum(outstockqty) bucket_out FROM ".DB_PREFIX."storage_stock ss
//                            LEFT JOIN ".DB_PREFIX."base_storagetank hbs ON ss.goods_sysno = hbs.goods_sysno
//                            WHERE ss.isdel = 0 AND ss.`status` = 1 AND ss.ghosttype = 2 AND hbs.area_sysno = 7 GROUP BY day;";
//        $everyDayBucket = $this -> dbh -> select($everyDayBucketSql);
//
//        //结存
//        $balancesql = "SELECT count_time `day`,beqty, bucket_qty FROM `".DB_PREFIX."day_balance` WHERE type = 1";
//        $balanceRes = $this-> dbh-> select($balancesql);

        //合并数组
//        $result = array_merge($balanceRes, $carResult, $shipResult, $everyDayCount, $everyDayBucket);
        $result = array_merge($carResult, $shipResult );
        $returnRes = self::merageArray($result);
        //统计总磅码单数 车和船之和
        foreach($returnRes as  &$val){
            $val['count'] =  (isset($val['pound_count']) ? $val['pound_count'] : 0 ) + (isset($val['ship_count']) ? $val['ship_count'] : 0);
        }
        //获取所有的商品 用于合计
        $allGoods = $this -> getAllGoods();
        $allGoodsIdArr = [];
        foreach($allGoods as $item){
            $allGoodsIdArr[] = $item['sysno'];
        }
        $countKey = count($returnRes);
        $returnRes[$countKey]['day'] = '合计';
        //追加需要统计的下标
        array_unshift($allGoodsIdArr, 'count', 'ship_count', 'pound_count');
        foreach($returnRes as $key => $value){
            foreach($allGoodsIdArr as $items){
                $returnRes[$countKey][$items] += $value[$items];
            }
        }

        //分页
        if(isset($params['page']) && $params['page'] == false){
            $data['list'] = $returnRes;
        }else{
            if(!empty($returnRes)){
                $data['totalRow'] = count($returnRes);
                $data['totalPage'] = ceil($data['totalRow'] / $params['pageSize']);
                $list = array_chunk($returnRes, $params['pageSize'], false);
                $data['list'] = $list[$params['pageCurrent'] - 1];
            }else{
                $data['list'] = [];
            }
        }
        return $data;
    }

    /**
     * 根据日期合并同一天的数据
     * @param $array 需要传入的数组
     * @return array
     */
    private static function merageArray($array){
        $result = [];
        $day_arr = [];
        if(is_array($array) && !empty($array)) {
            foreach ($array as $item) {
                //去重 并把重复的写入相同结果集里
                if (in_array($item['day'], $day_arr)) {
                    foreach ($result as $v) {
                        if ($v['day'] == $item['day']) {
                            $result[$item['day']] = $item + $v;
                        }
                    }
                } else {
                    //需要合并的标准下标
                    $day_arr[] = $item['day'];
                    //不同的的直接写入
                    $result[$item['day']] = $item;
                }
            }
        }
        ksort($result);
        $returnRes = array_values($result);//变成索引数组
        return $returnRes;
    }

    /**
     * 获取所有的商品
     * @return array
     */
    public function  getAllGoods(){
        $sql = "SELECT sysno,goodsname FROM  ".DB_PREFIX."base_goods WHERE isdel = 0 AND `status` = 1";
        return $this -> dbh -> select($sql) ? $this -> dbh -> select($sql) : [];
    }
}
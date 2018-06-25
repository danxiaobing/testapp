<?php

/**
 * Class Report_StockdayModel
 */
class Report_BalanceModel
{
    /**
     * 数据库类实例
     * @var object
     */
    public $dbh = null;

    /**
     * 缓存数据库实例
     * @var Object
     */
    public $mch = null;

    /**
     * Report_StockdayreportModel constructor.
     * @param $dbh
     * @param null $mch
     */
    public function __construct($dbh, $mch = null)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    //统计当天的所有库存的结存
    public function index(){
        //当天时间
        $data = date('Y-m-d', strtotime("-1 day"));
//        $data = date('Y-m-d');
        //查询库存的结存
        $sql = 'SELECT SUM(stockqty) FROM `'.DB_PREFIX.'storage_stock` WHERE iscurrent = 1  AND isdel = 0 AND `status` = 1';
        $num  = $this -> dbh -> select_one($sql);
        #查询堆桶场地结存
        $bucketSql = 'SELECT SUM(ss.stockqty) FROM `'.DB_PREFIX.'storage_stock` ss
                      LEFT JOIN `'.DB_PREFIX.'base_storagetank` hbs ON  hbs.sysno = ss.storagetank_sysno
                      WHERE ss.iscurrent = 1  AND ss.isdel = 0 AND ss.`status` = 1 AND hbs.area_sysno = 7;';
        $bucketQty  = $this -> dbh -> select_one($bucketSql);

        $inserData = [
            'beqty' => $num ? $num : 0 ,
            'count_time' => $data,
            'bucket_qty' => $bucketQty ? $bucketQty : 0,
            'type' => 1,
            'update_at' => '=NOW()',
            'create_at' => '=NOW()',
        ];

        //查询储罐的结存
        $storagetankSql = 'SELECT SUM(tank_stockqty) count FROM `'.DB_PREFIX.'base_storagetank` WHERE isdel = 0 AND `status` = 1';
        $storagetankNum  = $this -> dbh -> select_one($storagetankSql);
        #查询堆桶场地储罐的结存
        $storagetankBucketSql = 'SELECT SUM(tank_stockqty) count FROM `'.DB_PREFIX.'base_storagetank` WHERE isdel = 0 AND `status` = 1 AND area_sysno = 7';
        $storagetankBucketQty  = $this -> dbh -> select_one($storagetankBucketSql);
        $result = self::insertBalance($inserData);
        $storagetankInserData = [
            'beqty' => $storagetankNum ? $storagetankNum : 0 ,
            'count_time' => $data,
            'bucket_qty' => $storagetankBucketQty ? $storagetankBucketQty : 0,
            'type' => 2,
            'update_at' => '=NOW()',
            'create_at' => '=NOW()',
        ];

        self::insertBalance($storagetankInserData);
        if(!$result){
            error_log($data.'统计当天结存失败'.PHP_EOL , 3, './logs/balance.log');
            return false;
        }
        return true;
    }

    /**
     * 插入当天结存数据
     * @param $data
     * @return bool
     */
    private function insertBalance($data){
        return $this -> dbh -> insert(DB_PREFIX.'day_balance', $data);
    }

    /**
     * 获取库存当天所有物品的结存
     */
    public function getGoodsCount()
    {
        $sql = 'SELECT goods_sysno, goodsname, SUM(beqty) num FROM '.DB_PREFIX.'doc_goods_record_log WHERE doc_type != 10 AND isdel = 0 AND `status` = 1 GROUP  BY goods_sysno';
        $bucketQty  = $this -> dbh -> select($sql);
        return $bucketQty;
    }

    public function getGoodsDayCount($goods_sysno, $day = 7)
    {
        $where = ' doc_type != 10 AND isdel = 0 AND `status` = 1';
        if (isset($goods_sysno) && $goods_sysno != '') {
            $filter[] = " `goods_sysno` = {$goods_sysno}";
        }

        $day = $day ? $day : 7;
        $dayArr = [];
        for($i = $day; $i > 0; $i--){
            $dayArr[] = date('Y-m-d', strtotime('-'.$i.' days')).' 23:59:59';
        }
        array_push($dayArr, date('Y-m-d 23:59:59'));
        $reulst = [];
        foreach($dayArr as $value)
        {
            $filter[] = "`created_at` < '{$value}'";
            if (1 <= count($filter)) {
                $where .= ' AND ' . implode(' AND ', $filter);
            }
            $sql = "SELECT goods_sysno, goodsname, SUM(beqty) num FROM ".DB_PREFIX."doc_goods_record_log WHERE  {$where}";
            $res = $this -> dbh -> select_row($sql);
            $res['time'] = $value;
            $reulst[] = $res;
        }
        return $reulst;
    }

}
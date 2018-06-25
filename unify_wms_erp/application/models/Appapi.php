<?php
class AppapiModel
{
    public $dbh = null;
    public $mch = null;

    /**
     * Constructor
     */
    public function __construct($dbh, $mch)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    /**
     * 获取仓库所有商品种类个数
     * getGoodsNum
     * @author ${USER}
     */
    public function getGoodsNum(){
        $sql = "SELECT COUNT(*) FROM `".DB_PREFIX."base_goods` WHERE isdel = 0  AND `status` = 1";
        $num = $this -> dbh -> select_one($sql);
        return $num ? $num : 0;
    }

    //预计收益
    public function getFinanceNum(){
        $dateStart = date('Y-m-d');
        $dateEnd = $dateStart.' 23:59:59';
        $sql = "SELECT sum(totalprice) as totalsprice from `".DB_PREFIX."doc_finance_cost_detail` fd where fd.isdel=0 and fd.coststatus<5 and fd.customer_sysno<>0 AND fd.`costdate`<='{$dateEnd}'";
        $num = $this -> dbh -> select_one($sql);
        return $num ? $num : '0.00';
    }

    //昨日核销
    public function getYesterdayCancellationNum(){
        $dateStart = date('Y-m-d');
        $dateEnd = $dateStart.' 23:59:59';
        $sql = "SELECT sum(totalprice) as totalsprice from `".DB_PREFIX."doc_finance_cost_detail` fd where fd.isdel=0 and fd.coststatus=5 and fd.customer_sysno<>0 AND fd.`costdate` >= '{$dateStart}' AND fd.`costdate`<='{$dateEnd}'";
        $num = $this -> dbh -> select_one($sql);
        return $num ? $num : '0.00';
    }

    //当前客户
    public function getCustomerNum(){
        $sql = "SELECT COUNT(*) FROM `".DB_PREFIX."customer` WHERE isdel = 0  AND `status` = 1";
        $num = $this -> dbh -> select_one($sql);
        return $num ? $num : 0;
    }

    //当前库存
    public function getStorageCount(){
        $sql = "SELECT round(SUM(tank_stockqty), 3) tank_stockqty, round(SUM(actualcapacity), 3) actualcapacity FROM `".DB_PREFIX."base_storagetank` WHERE isdel = 0  AND `status` = 1";
        $num = $this -> dbh -> select_row($sql);
        return $num ? $num : ['tank_stockqty'=> 0, 'actualcapacity' => 0];
    }

    public function getFinanceList($params){
        $filter = array();
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " fd.`costno` LIKE '%{$params['bar_no']}%' ";
        }
        if (isset($params['customer_name']) && $params['customer_name'] != '') {
            $filter[] = " fd.`customer_name` LIKE '%{$params['customer_name']}%' ";
        }
        if (isset($params['goods_name']) && $params['goods_name'] != '') {
            $filter[] = " fd.`goodsname` LIKE '%{$params['goods_name']}%' ";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
            $filter[] = " fd.`status`='{$params['bar_status']}'";
        }
        if (isset($params['bar_coststatus']) && $params['bar_coststatus'] != '-100') {
            $filter[] = " fd.`coststatus`='{$params['bar_coststatus']}'";
        }
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " fd.`costdate`>='{$params['begin_time']}'";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " fd.`costdate`<='{$params['end_time']} 23:59:59' ";
        }

        $where = 'fd.isdel=0 and fd.coststatus<5 and fd.customer_sysno<>0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT * from `".DB_PREFIX."doc_finance_cost_detail` fd where {$where} group by fd.customer_sysno";
        $result = $params;
        $result['totalRow'] = count($this->dbh->select($sql));
        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT customer_name,customer_sysno,sum(totalprice) totalprice from `".DB_PREFIX."doc_finance_cost_detail` fd where {$where} group by fd.customer_sysno";
                if ($params['orders'] != '') {
                     $sql .= " order by ".$params['orders'] ;
                } else {
                    $sql .= " order by fd.goodsname,fd.created_at desc";
                }
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);
                $sql = "SELECT customer_name,customer_sysno,sum(totalprice) as totalsprice from `".DB_PREFIX."doc_finance_cost_detail` fd where {$where} group by fd.customer_sysno";
                if ($params['orders'] != '') {
                     $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by fd.goodsname,fd.created_at desc";
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    public function getFinanceDetail($params){
        $filter = array();
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " fd.`costno` LIKE '%{$params['bar_no']}%' ";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " fd.`customer_sysno` = '{$params['customer_sysno']}' ";
        }

        if (isset($params['customer_name']) && $params['customer_name'] != '') {
            $filter[] = " fd.`customer_name` LIKE '%{$params['customer_name']}%' ";
        }
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " fd.`costdate`>='{$params['begin_time']}'";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " fd.`costdate`<='{$params['end_time']} 23:59:59' ";
        }

        $where = 'fd.isdel=0 and fd.coststatus<5 and fd.customer_sysno<>0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $result = $params;
        $sql = "SELECT * from `".DB_PREFIX."doc_finance_cost_detail` fd where {$where} group by fd.goods_sysno";
        $result['totalRow'] = count($this->dbh->select($sql));
        $result['list'] = array();
        if ($result['totalRow']) {
            //总统计
            $count_sql = "SELECT sum(totalprice) totalprice from `".DB_PREFIX."doc_finance_cost_detail` fd where {$where}";
            $count_num = $this -> dbh -> select_one($count_sql);
            $result['count_num'] = $count_num ? $count_num : '0.00';
            //商品分组 总计金额
            $sql = "SELECT customer_name,customer_sysno,goods_sysno,goodsname,sum(totalprice) as totalsprice from `".DB_PREFIX."doc_finance_cost_detail` fd where {$where} group by fd.goods_sysno";
            $result['list'] = $this->dbh->select($sql);
            foreach ($result['list'] as $k => $v){
                //昨日核销
                $starTime = date('Y-m-d', strtotime("-1 day"));
                $endTime = $starTime.' 23:59:10';
                $sql = "SELECT sum(totalprice) as totalsprice from `".DB_PREFIX."doc_finance_cost_detail` fd where fd.isdel=0 and fd.coststatus = 5 and fd.customer_sysno = {$v['customer_sysno']} AND goods_sysno = {$v['goods_sysno']} AND fd.`costdate` >= '{$starTime}' AND fd.`costdate`<='{$endTime}'  group by fd.goods_sysno";
                $hexiao = $this->dbh->select_one($sql);
                $result['list'][$k]['hexiao'] = $hexiao ? $hexiao : '0.00';
                //首期
                $shouqisql = "SELECT sum(totalprice) as totalsprice from `".DB_PREFIX."doc_finance_cost_detail` fd where fd.isdel=0 and fd.coststatus < 5 and fd.customer_sysno = {$v['customer_sysno']} AND goods_sysno = {$v['goods_sysno']} AND costname = '首期储罐使用费'";
                $shouqi = $this-> dbh -> select_one($shouqisql);
                $result['list'][$k]['shouqi']  = $shouqi ?  $shouqi : '0.00';
                //超期
                $chaoqiSql = "SELECT sum(totalprice) as totalsprice from `".DB_PREFIX."doc_finance_cost_detail` fd where fd.isdel=0 and fd.coststatus < 5 and fd.customer_sysno = {$v['customer_sysno']} AND goods_sysno = {$v['goods_sysno']} AND costname Like '%超期费%'";
                $chaoqi = $this-> dbh -> select_one($chaoqiSql);
                $result['list'][$k]['chaoqi']  = $chaoqi ?  $chaoqi : '0.00';
                //提单
                $tidansql = "SELECT sum(totalprice) as totalsprice from `".DB_PREFIX."doc_finance_cost_detail` fd where fd.isdel=0 and fd.coststatus < 5 and fd.customer_sysno = {$v['customer_sysno']} AND goods_sysno = {$v['goods_sysno']} AND costname = '提单费用'";
                $tidan = $this-> dbh -> select_one($tidansql);
                $result['list'][$k]['tidan']  = $tidan ?  $tidan : '0.00';
            }
        }
        return $result;
    }

    //当前库存
    public function getStorageList(){
        $sql = "SELECT goods_sysno,goodsname,if(SUM(beqty-ullage) < 0, '0.000', SUM(beqty-ullage)) beqty FROM `".DB_PREFIX."doc_goods_record_log` WHERE isdel = 0 AND status = 1 GROUP BY goods_sysno";
        $result = $this -> dbh -> select($sql);
        return $result ? $result : [];
    }

    public function getStorageDetail($params){
        $todayDate = date('Y-m-d');
        $result = [];
        //库存总量
        $sql = "SELECT goods_sysno,goodsname,if(SUM(beqty-ullage) < 0, '0.000', SUM(beqty-ullage)) beqty FROM `".DB_PREFIX."doc_goods_record_log` WHERE isdel = 0 AND status = 1 AND goods_sysno = {$params['goods_sysno']}";
        $result['allBanceTank'] = $this->dbh->select_row($sql);
        if(!$result['allBanceTank']){
            return ['code' => 300, '未找到数据'];
        }
        //查询该商品的所有储罐
        $storageTankSql = "SELECT sysno,storagetankname FROM `".DB_PREFIX."base_storagetank` WHERE isdel = 0 AND status = 1 AND goods_sysno = ".intval($params['goods_sysno']);
        //." AND tank_stockqty > 0"
        $result['list'] = $this->dbh->select($storageTankSql);
        foreach ($result['list'] as $key => $item) {
            //当前结存
//            $dayBalanceSql = "SELECT SUM(beqty-ullage) FROM `".DB_PREFIX."doc_goods_record_log` WHERE isdel = 0 AND status = 1 AND goods_sysno = {$params['goods_sysno']} AND storagetank_sysno = {$item['sysno']}";
            $dayBalanceSql = "SELECT tank_stockqty FROM `".DB_PREFIX."base_storagetank` WHERE isdel = 0 AND status = 1 AND sysno = {$item['sysno']}";
            $countDay = $this ->  dbh -> select_one($dayBalanceSql);
            $result['list'][$key]['dayBalance'] = $countDay ? $countDay : 0;
            //昨日结存
            $yesterdayBalanceSql = "SELECT SUM(beqty-ullage) FROM `".DB_PREFIX."doc_goods_record_log` WHERE isdel = 0 AND status = 1 AND goods_sysno = {$params['goods_sysno']} AND storagetank_sysno = {$item['sysno']} AND created_at < '{$params['start_time']}'";
            $yesterDay = $this ->  dbh -> select_one($yesterdayBalanceSql);
            $result['list'][$key]['yesterdayBalance'] = $yesterDay ? $yesterDay : 0;
            //当日入库
            $dayInsql = "SELECT SUM(beqty-ullage) FROM `".DB_PREFIX."doc_goods_record_log` WHERE isdel = 0 AND status = 1 AND goods_sysno = {$params['goods_sysno']} AND storagetank_sysno = {$item['sysno']}  AND created_at > '{$todayDate}' AND beqty > 0";
            $dayIn = $this ->  dbh -> select_one($dayInsql);
            $result['list'][$key]['dayIn'] = $dayIn ? $dayIn : 0;
            //当日出库
            $dayOutSsql = "SELECT SUM(beqty) FROM `".DB_PREFIX."doc_goods_record_log` WHERE isdel = 0 AND status = 1 AND goods_sysno = {$params['goods_sysno']} AND storagetank_sysno = {$item['sysno']} AND created_at > '{$todayDate}' AND beqty < 0";
            $dayOut = $this ->  dbh -> select_one($dayOutSsql);
            $result['list'][$key]['dayOut'] = $dayOut ? $dayOut : 0;
        }
        return $result;
    }

}

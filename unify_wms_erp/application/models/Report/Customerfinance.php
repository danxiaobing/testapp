<?php

class Report_CustomerfinanceModel {
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
     * @param   object  $dbh
     * @return  void
     */
    public function __construct($dbh, $mch = null) {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    public function getList($params)
    {
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

        $sql = "SELECT * from `".DB_PREFIX."doc_finance_cost_detail` fd where {$where} group by fd.customer_sysno,fd.goods_sysno";
        $result = $params;

        $result['totalRow'] = count($this->dbh->select($sql));

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT *,sum(totalprice) as totalsprice from `".DB_PREFIX."doc_finance_cost_detail` fd where {$where} group by fd.customer_sysno,fd.goods_sysno";
                if ($params['orders'] != '') {
                    // $sql .= " order by ".$params['orders'] ;
                } else {
                    $sql .= " order by fd.goodsname,fd.created_at desc";
                }
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT *,sum(totalprice) as totalsprice from `".DB_PREFIX."doc_finance_cost_detail` fd where {$where} group by fd.customer_sysno,fd.goods_sysno";
                if ($params['orders'] != '') {
                    // $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by fd.goodsname,fd.created_at desc";
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;

    }

    public function getdetailList($params)
    {
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
        if (isset($params['shipname']) && $params['shipname'] != '') {
            $filter[] = " fd.`shipname` LIKE '%{$params['shipname']}%' ";
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

        $where = 'fd.isdel=0 and fd.coststatus<5';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "select * from `".DB_PREFIX."doc_finance_cost_detail` fd left join `".DB_PREFIX."storage_stock` s on (fd.stockinno=s.firstfrom_no) where {$where} group by fd.sysno";
        $result = $params;

        $result['totalRow'] = count($this->dbh->select($sql));

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "select fd.*,s.storagetank_sysno as storagetanksysno,s.instockdate,s.instockqty,s.stockqty,s.outstockqty,s.ullage,fd.ullage as costullage,s.stockqty as calccostqty from `".DB_PREFIX."doc_finance_cost_detail` fd left join `".DB_PREFIX."storage_stock` s on (fd.stock_sysno = s.sysno) where {$where} group by fd.sysno";
                if ($params['orders'] != '') {
                    // $sql .= " order by ".$params['orders'] ;
                } else {
                    $sql .= " order by fd.storagetank_sysno,fd.created_at desc";
                }
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "select fd.*,s.storagetank_sysno as storagetanksysno,s.instockdate,s.instockqty,s.stockqty,s.outstockqty,s.ullage,fd.ullage as costullage,s.stockqty as calccostqty from `".DB_PREFIX."doc_finance_cost_detail` fd left join `".DB_PREFIX."storage_stock` s on (fd.stock_sysno = s.sysno) where {$where} group by fd.sysno";
                if ($params['orders'] != '') {
                    // $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by fd.storagetank_sysno,fd.created_at desc";
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        for($i=0;$i<count($result['list']);$i++){
            if($result['list'][$i]['costname']=='溢罐首期费' || $result['list'][$i]['costname']=='首期储罐使用费')
            {
                $result['list'][$i]['datenum'] = 30;
            }
            else
            {
                $result['list'][$i]['datenum'] = COMMON::count_days(strtotime(date("Y-m-d")),strtotime( $result['list'][$i]['costdate']));
            }
            if($result['list'][$i]['storagetanksysno']>0 && $result['list'][$i]['storagetank_sysno']==0 && $result['list'][$i]['storagetankname']=='')
            {
                $sql = "select storagetankname from ".DB_PREFIX."base_storagetank where sysno = ".$result['list'][$i]['storagetanksysno']." ";
                $result['list'][$i]['storagetankname'] = $this->dbh->select_one($sql);
            }
        }
        return $result;

    }
    
}
<?php

/**
 * Financecost Model
 *
 */
class FinancecostModel
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
     * @param   object $dbh
     * @param   object $mch
     * @return  void
     */
    public function __construct($dbh, $mch)
    {
        $this->dbh = $dbh;

        $this->mch = $mch;
    }

    public function searchFinancecost($params)
    {
        $filter = array();
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " fd.`costno` LIKE '%{$params['bar_no']}%' ";
        }
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " fd.`customer_name` LIKE '%{$params['bar_name']}%' ";
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
            $filter[] = " fd.`costdate`<='{$params['end_time']}'";
        }
        if (isset($params['bar_contract']) && $params['bar_contract'] != '') {
            $filter[] = " fd.`contract_no` LIKE '%{$params['bar_contract']}%' ";
        }

        $where = 'fd.isdel=0 and fd.costtype=0 and fd.coststatus<>1 and fd.totalprice>0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "select count(*) from `".DB_PREFIX."doc_finance_cost_detail` fd where {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT fd.*,si.instockdate,si.instockqty,date_add(fd.costdate, interval 29 day) as firstdate,c.contracttype from `".DB_PREFIX."doc_finance_cost_detail` fd 
                left join `".DB_PREFIX."doc_contract` c on (c.sysno=fd.contract_sysno) 
                LEFT JOIN ".DB_PREFIX."storage_stock si ON si.sysno=fd.stock_sysno where {$where} ";
                if ($params['orders'] != '') {
                    // $sql .= " order by ".$params['orders'] ;
                } else {
                    $sql .= " order by fd.created_at desc";
                }
                //最大1000条读取
                $sql .=" limit 1000";
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT fd.*,si.instockdate,si.instockqty,date_add(fd.costdate, interval 29 day) as firstdate,c.contracttype from `".DB_PREFIX."doc_finance_cost_detail` fd 
                left join `".DB_PREFIX."doc_contract` c on (c.sysno=fd.contract_sysno) 
                LEFT JOIN ".DB_PREFIX."storage_stock si ON si.sysno=fd.stock_sysno where {$where} ";
                if ($params['orders'] != '') {
                    // $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by fd.created_at desc";
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    public function searchFinancecostother($params)
    {
        $filter = array();
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " fd.`costno` LIKE '%{$params['bar_no']}%' ";
        }
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " fd.`customer_name` LIKE '%{$params['bar_name']}%' ";
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
            $filter[] = " fd.`costdate`<='{$params['end_time']}'";
        }

        $where = 'fd.isdel=0 and fd.costtype>0 and fd.totalprice>0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "select count(*) from `".DB_PREFIX."doc_finance_cost_detail` fd where {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT fd.*,si.instockdate,date_add(fd.costdate, interval 29 day) as firstdate,c.contracttype from `".DB_PREFIX."doc_finance_cost_detail` fd
                left join `".DB_PREFIX."doc_contract` c on (c.sysno=fd.contract_sysno) 
                LEFT JOIN ".DB_PREFIX."storage_stock si ON si.sysno=fd.stock_sysno where {$where} ";

                if ($params['orders'] != '') {
                    // $sql .= " order by ".$params['orders'] ;
                } else {
                    $sql .= " order by fd.coststatus,fd.created_at desc";
                }
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT fd.*,si.instockdate,date_add(fd.costdate, interval 29 day) as firstdate,c.contracttype from `".DB_PREFIX."doc_finance_cost_detail` fd
                left join `".DB_PREFIX."doc_contract` c on (c.sysno=fd.contract_sysno) 
                LEFT JOIN ".DB_PREFIX."storage_stock si ON si.sysno=fd.stock_sysno where {$where} ";
                if ($params['orders'] != '') {
                    // $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by fd.coststatus,fd.created_at desc";
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    public function searchFinancecostcalc($params)
    {
        $filter = array();
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " fd.`costno` LIKE '%{$params['bar_no']}%' ";
        }
        if (isset($params['contract_sysno']) && $params['contract_sysno'] != '-100') {
            $filter[] = " fd.`contract_sysno`='{$params['contract_sysno']}'";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '-100') {
            $filter[] = " fd.`customer_sysno`='{$params['customer_sysno']}'";
        }
        if (isset($params['bar_coststatus']) && $params['bar_coststatus'] != '-100') {
            $filter[] = " fd.`coststatus`='{$params['bar_coststatus']}'";
        }
        if (isset($params['id']) && $params['id'] != '') {
            $filter[] = " fd.`sysno`='{$params['id']}'";
        }
        if (isset($params['shipname']) && $params['shipname'] != '') {
            $filter[] = " fd.`shipname` LIKE '%{$params['shipname']}%' ";
        }
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " fd.`costdate`>='{$params['begin_time']}'";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " fd.`costdate`<='{$params['end_time']}'";
        }
        if (isset($params['instockdate']) && $params['instockdate'] != ''){
            $filter[] = " si.`instockdate`='{$params['instockdate']}'";
        }
        if ($params['goodsname'] == '全部'){
            $params['goodsname'] = '';
        }
        if (isset($params['goodsname']) && $params['goodsname'] != ''){
            $filter[] = " fd.`goodsname`='{$params['goodsname']}'";
        }
        // if($params['contract_sysno']=='' && $params['customer_sysno']=='')
        // {
        //     $result['totalRow'] = 0;
        //     $result['list'] = array();
        //     return $result;
        // }
        $day = date("Y-m-d");

        $where = "fd.isdel=0 and fd.costtype=0 and fd.coststatus=1 and fd.costdate<='" . $day . "'  and fd.totalprice>0 ";
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "select count(*) from `".DB_PREFIX."doc_finance_cost_detail` fd 
                left join `".DB_PREFIX."doc_contract` c on (c.sysno=fd.contract_sysno) 
                LEFT JOIN ".DB_PREFIX."storage_stock si ON si.sysno=fd.stock_sysno where {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT fd.*,si.instockdate,si.instockqty,date_add(fd.costdate, interval 29 day) as firstdate,c.contracttype from `".DB_PREFIX."doc_finance_cost_detail` fd 
                left join `".DB_PREFIX."doc_contract` c on (c.sysno=fd.contract_sysno) 
                LEFT JOIN ".DB_PREFIX."storage_stock si ON si.sysno=fd.stock_sysno where {$where} ";
                if ($params['orders'] != '') {
                    // $sql .= " order by ".$params['orders'] ;
                } else {
                    $sql .= " order by fd.created_at desc";
                }
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT fd.*,si.instockdate,si.instockqty,date_add(fd.costdate, interval 29 day) as firstdate,c.contracttype from `".DB_PREFIX."doc_finance_cost_detail` fd 
                left join `".DB_PREFIX."doc_contract` c on (c.sysno=fd.contract_sysno) 
                LEFT JOIN ".DB_PREFIX."storage_stock si ON si.sysno=fd.stock_sysno where {$where} ";
                if ($params['orders'] != '') {
                    // $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by fd.created_at desc";
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }

        return $result;
    }

    public function searchFinancecostload($params)
    {
        $filter = array();
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " fd.`costno` LIKE '%{$params['bar_no']}%' ";
        }
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " fd.`customer_name` LIKE '%{$params['bar_name']}%' ";
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
            $filter[] = " fd.`costdate`<='{$params['end_time']}'";
        }
        if (isset($params['bar_contract']) && $params['bar_contract'] != '') {
            $filter[] = " fd.`contract_no` LIKE '%{$params['bar_contract']}%' ";
        }
        if (isset($params['bar_berthtype']) && $params['bar_berthtype'] != '-100') {
            $filter[] = " fd.`berthtype`='{$params['bar_berthtype']}'";
        }

        $where = 'fd.isdel=0 and fd.costtype=-1  and fd.totalprice>0 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "select count(*) from `".DB_PREFIX."doc_finance_cost_detail` fd where {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT fd.*,date_add(fd.costdate, interval 29 day) as firstdate,c.contracttype from `".DB_PREFIX."doc_finance_cost_detail` fd left join `".DB_PREFIX."doc_contract` c on (c.sysno=fd.contract_sysno) where {$where} ";
                if ($params['orders'] != '') {
                    // $sql .= " order by ".$params['orders'] ;
                } else {
                    $sql .= " order by fd.created_at desc";
                }
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT fd.*,date_add(fd.costdate, interval 29 day) as firstdate,c.contracttype from `".DB_PREFIX."doc_finance_cost_detail` fd left join `".DB_PREFIX."doc_contract` c on (c.sysno=fd.contract_sysno) where {$where} ";
                if ($params['orders'] != '') {
                    // $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by fd.created_at desc";
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    public function getFinancecostById($id)
    {
        $sql = "SELECT f.* FROM `".DB_PREFIX."doc_finance_cost_detail` fd left join `".DB_PREFIX."doc_finance_cost` f on (f.`sysno`=fd.`cost_sysno`) left join `".DB_PREFIX."customer` c on (f.`customer_sysno`=c.`sysno`) left join `".DB_PREFIX."base_storagetank` st on (fd.`storagetank_sysno`=st.`sysno`) where fd.`sysno`=" . intval($id);
        return $this->dbh->select_row($sql);
    }

    public function getFinancecostotherById($id)
    {
        $sql = "SELECT f.* FROM `".DB_PREFIX."doc_finance_cost` f where f.`sysno`=" . intval($id);
        return $this->dbh->select_row($sql);
    }

    public function getFinancecostotheroneById($id)
    {
        $sql = "SELECT fd.*,f.`costdate` as costdate2,st.`storagetankname`,fi.`invoiceno`,bg.`goodsname`,gq.`qualityname`,bu.`unitname`,s.`instockdate`,s.`firstdate` from `".DB_PREFIX."doc_finance_cost_detail` fd left join `".DB_PREFIX."doc_finance_cost` f on (f.`sysno`=fd.`cost_sysno`) left join `".DB_PREFIX."customer` c on (f.`customer_sysno`=c.`sysno`) left join `".DB_PREFIX."base_storagetank` st on (fd.`storagetank_sysno`=st.`sysno`) left join `".DB_PREFIX."doc_finance_invoice` fi on (fi.`sysno`=fd.`invoice_sysno`) left join `".DB_PREFIX."base_goods` bg on (bg.`sysno`=fd.`goods_sysno`) left join `".DB_PREFIX."base_goods_quality` gq on (gq.`sysno`=fd.`goods_quality_sysno`) left join `".DB_PREFIX."base_goods_attribute` ga on (ga.`goods_sysno`=bg.`sysno`) left join `".DB_PREFIX."base_unit` bu on (bu.`sysno`=ga.`unit_sysno`) left join `".DB_PREFIX."storage_stock` s on (s.`sysno`=fd.`stock_sysno`) where f.`sysno`=" . intval($id) . " group by f.sysno";
        return $this->dbh->select_row($sql);
    }

    public function getFinancecostloadById($id)
    {
        $sql = "SELECT f.* FROM `".DB_PREFIX."doc_finance_cost` f where f.`sysno`=" . intval($id);
        return $this->dbh->select_row($sql);
    }

    public function getFinancecosteditdetailById($id)
    {
        $sql = "SELECT fd.*,si.instockdate,si.instockqty,date_add(fd.costdate, interval 29 day) as firstdate,c.contracttype from `".DB_PREFIX."doc_finance_cost_detail` fd 
                left join `".DB_PREFIX."doc_contract` c on (c.sysno=fd.contract_sysno) 
                LEFT JOIN ".DB_PREFIX."storage_stock si ON si.sysno=fd.stock_sysno where fd.`sysno`= ".intval($id);
        return $this->dbh->select_row($sql);
    }

    public function addFinancecost($data, $financecostdetaildata)
    {
        $this->dbh->begin();
        try {

            // $res = $this->dbh->insert(DB_PREFIX.'doc_finance_cost', $data);

            // if (!$res) {
            //     $this->dbh->rollback();
            //     return false;
            // }

            // $id = $res;

            // $res = $this->dbh->delete(DB_PREFIX.'doc_finance_cost_detail', 'cost_sysno=' . intval($id));

            // if (!$res) {
            //     $this->dbh->rollback();
            //     return false;
            // }

            foreach ($financecostdetaildata as $value) {
                $input = array(
                    // 'cost_sysno' => $id,
                    'costno' => COMMON::getCodeId('F1'),
                    'costdate' => date("Y-m-d"),
                    'costdateend' => date("Y-m-d"),
                    'isexceedfirst' => $value['isexceedfirst'],
                    'isstoragetank' => $value['isstoragetank'],
                    'storagetank_sysno' => $value['storagebank_sysno'],
                    'shipname' => $value['shipname'],
                    'goods_sysno' => $value['goods_sysno'],
                    'goods_quality_sysno' => $value['goods_quality_sysno'],
                    'goodsnature' => $value['goodsnature'],
                    'customer_sysno' => $value['customer_sysno'],
                    'customer_name' => $value['customer_name'],
                    'costqty' => $value['costqty'],
                    'unitprice' => $value['unitprice'],
                    'totalprice' => $value['totalprice'],
                    'costtype' => $value['costtype'],
                    'costname' => $value['costname'],
                    'coststatus' => 2,
                    'stock_sysno' => $value['stock_sysno'],
                    'instock_sysno' => $value['instock_sysno'],
                    'stockinno' => $value['stockinno'],
                    'contract_sysno' => $value['contract_sysno'],
                    'contract_no' => $value['contract_no'],
                    'storagetankname' => $value['storagetankname'],
                    'goodsname' => $value['goodsname'],
                    'qualityname' => $value['qualityname'],
                    'unitname' => $value['unitname'],
                    'shipname' => $value['shipname'],
                    'stockindate' => $value['stockindate'],
                    'instockqty' => $value['instockqty'],
                    'iscalc' => 1,
                    'status' => 1,
                    'isdel' => 0,
                    'version' => $value['version'],
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                );
                $res = $this->dbh->insert(DB_PREFIX.'doc_finance_cost_detail', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }

                $id = $res;

                #库存管理业务操作日志
                $user = Yaf_Registry::get(SSN_VAR);
                $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 13,
                    'opertype' => 0,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => '',
                );

                $res = $S->addDocLog($input);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
                $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 13,
                    'opertype' => 1,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => '',
                );

                $res = $S->addDocLog($input);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            $this->dbh->commit();
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    public function updateFinancecost($id, $data, $financecostdetaildata, $costmarks = '', $auditstep = 0)
    {
        $this->dbh->begin();
        try {
            if ($auditstep == 1) {
                $data['coststatus'] = 2;
            }

            $res = $this->dbh->update(DB_PREFIX.'doc_finance_cost', $data, 'sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if ($auditstep == 4) {
                $sql = 'select * from `'.DB_PREFIX.'doc_finance_cost` where sysno=' . intval($id);
                $data = $this->dbh->select_row($sql);
                if (!$data) {
                    $this->dbh->rollback();
                    return false;
                }

                $sql = 'select * from `'.DB_PREFIX.'doc_finance_cost_detail` where sysno=' . intval($id);
                $financecostdetaildata = $this->dbh->select_row($sql);
                if (!$financecostdetaildata) {
                    $this->dbh->rollback();
                    return false;
                }

                foreach ($financecostdetaildata as $value) {

                    $detailid = $value['sysno'];

                    $detailinput = array(
                        'cost_sysno' => $id,
                        'costno' => COMMON::getCodeId('F1'),
                        'costdate' => $data['costdate'],
                        'costdateend' => $data['costdateend']?$data['costdateend']:$data['costdate'],
                        'isexceedfirst' => $value['isexceedfirst'],
                        'isstoragetank' => $value['isstoragetank'],
                        'storagetank_sysno' => $value['storagebank_sysno'],
                        'shipname' => $value['shipname'],
                        'goods_sysno' => $value['goods_sysno'],
                        'goods_quality_sysno' => $value['goods_quality_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'customer_sysno' => $value['customer_sysno'],
                        'customer_name' => $value['customer_name'],
                        'costqty' => $value['costqty'],
                        'unitprice' => $value['unitprice'],
                        'totalprice' => $value['totalprice'],
                        'costtype' => $value['costtype'],
                        'costname' => $value['costname'],
                        'coststatus' => 1,
                        'stock_sysno' => $value['stock_sysno'],
                        'instock_sysno' => $value['instock_sysno'],
                        'contract_sysno' => $value['contract_sysno'],
                        'storagetankname' => $value['storagetankname'],
                        'goodsname' => $value['goodsname'],
                        'qualityname' => $value['qualityname'],
                        'status' => $value['status'],
                        'isdel' => $value['isdel'],
                        'version' => $value['version'],
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                    );

                    $ret = $this->dbh->update(DB_PREFIX.'doc_finance_cost_detail', $detailinput, 'sysno=' . intval($detailid));

                    if (!$ret) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
            } elseif ($auditstep == 2 || $auditstep == 3) {
                $res = $this->dbh->delete(DB_PREFIX.'doc_finance_cost_detail', 'stock_sysno=' . intval($id));

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }

                foreach ($financecostdetaildata as $value) {
                    $input = array(
                        'cost_sysno' => $id,
                        'costno' => COMMON::getCodeId('F1'),
                        'costdate' => $data['costdate'],
                        'isexceedfirst' => $value['isexceedfirst'],
                        'isstoragetank' => $value['isstoragetank'],
                        'storagetank_sysno' => $value['storagebank_sysno'],
                        'shipname' => $value['shipname'],
                        'goods_sysno' => $value['goods_sysno'],
                        'goods_quality_sysno' => $value['goods_quality_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'customer_sysno' => $value['customer_sysno'],
                        'customer_name' => $value['customer_name'],
                        'costqty' => $value['costqty'],
                        'unitprice' => $value['unitprice'],
                        'totalprice' => $value['totalprice'],
                        'costtype' => $value['costtype'],
                        'costname' => $value['costname'],
                        'coststatus' => 1,
                        'stock_sysno' => $value['stock_sysno'],
                        'instock_sysno' => $value['instock_sysno'],
                        'contract_sysno' => $value['contract_sysno'],
                        'storagetankname' => $value['storagetankname'],
                        'goodsname' => $value['goodsname'],
                        'qualityname' => $value['qualityname'],
                        'status' => $value['status'],
                        'isdel' => $value['isdel'],
                        'version' => $value['version'],
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_finance_cost_detail', $input);

                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
            }


            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 13,
                'opertype' => $data['coststatus'] - 1,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $costmarks,
            );
            if ($auditstep == 1) {
                $input['opertype'] = 4;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    public function delFinancecost($ids)
    {
        $this->dbh->begin();
        try {
            foreach ($ids as $id) {
                $sql = "select * from ".DB_PREFIX."doc_finance_cost_detail where sysno=" . intval($id);
                $res = $this->dbh->select_row($sql);
                if ($res['coststatus'] > 2) {
                    $this->dbh->rollback();
                    return false;
                }

                $data = array(
                    'isdel' => 1,
                );

                $ret = $this->dbh->update(DB_PREFIX.'doc_finance_cost_detail', $data, 'sysno=' . intval($id));

                if (!$ret) {
                    $this->dbh->rollback();
                    return false;
                }

                #库存管理业务操作日志
                $user = Yaf_Registry::get(SSN_VAR);
                $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 13,
                    'opertype' => 4,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => $data['memo'],
                );

                $res = $S->addDocLog($input);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            $this->dbh->commit();
            return $ret;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    public function getFinancecostdetailById($id)
    {
        $sql = "SELECT f.* FROM `".DB_PREFIX."doc_finance_cost_detail` fd left join `".DB_PREFIX."doc_finance_cost` f on (f.`sysno`=fd.`cost_sysno`) left join `".DB_PREFIX."customer` c on (f.`customer_sysno`=c.`sysno`) left join `".DB_PREFIX."base_storagetank` st on (fd.`storagetank_sysno`=st.`sysno`) where f.`sysno`=" . intval($id);
        return $this->dbh->select($sql);
    }

    public function getFinancecostotherdetailById($id)
    {
        $sql = "SELECT fd.* FROM `".DB_PREFIX."doc_finance_cost_detail` fd left join `".DB_PREFIX."doc_finance_cost` f on (f.`sysno`=fd.`cost_sysno`) left join `".DB_PREFIX."customer` c on (f.`customer_sysno`=c.`sysno`) left join `".DB_PREFIX."base_storagetank` st on (fd.`storagetank_sysno`=st.`sysno`) where f.`sysno`=" . intval($id);
        return $this->dbh->select($sql);
    }

    ##新增包罐费用
    public function addFinancecostByTank($data = array(), $tpnum = 1, $countnum = 1)
    {
        if (!is_array($data) || intval($tpnum <= 0)) {
            return false;
        }
        $date1 = $data['contractcostdate'];

        $costdate = $date1;
        $costdaeend = $costdate;

        for ($i = 1; $i <= $tpnum; $i++) {

            $coststatus = 1;
            $totalprice = number_format($data['totalprice'] / $countnum, 2, '.', '');
            if ($i == 1) {
                $add = $data['totalprice'] - $totalprice * $countnum;
                $totalprice = $totalprice + $add;
            }
            if($totalprice<=0)
            {
                continue;
            }
            // $costdate = date("Y-m-d",strtotime($data['contractcostdate']." +1 month -1 day"));
            // $compaire = date("Y-m-d",strtotime("last day of next month",strtotime($data['contractcostdate'])));
            // // $d = 30*$i;
            // #contractcostdate +自然月
            // $costdate = date('Y-m-d', strtotime($data['contractcostdate'].' +'.$i.' month '));
            // $costdateend = date('Y-m-d', strtotime($data['contractcostdate'].' +'.$i.' month -1 day'));


            if ($i != 1) {
                $costdate = date('Y-m-d', strtotime('+1 day', strtotime($costdaeend)));
            }
            $next1 = date('Y-m-d', strtotime("+{$i} month -1 day", strtotime($date1)));
            $next2 = date('Y-m-d', strtotime('-1 day', strtotime('last day of next month', strtotime($costdate))));
            $costdaeend = $next1 > $next2 ? $next2 : $next1;

            $sql = "select storagetankname from ".DB_PREFIX."base_storagetank where sysno=" . intval($data['storagetank_sysno']);
            $storagetankname = $this->dbh->select_one($sql);
            $sql = "select goodsname from ".DB_PREFIX."base_goods where sysno=" . intval($data['goods_sysno']);
            $goodsname = $this->dbh->select_one($sql);
            $sql = "select qualityname from ".DB_PREFIX."base_goods_quality where sysno=" . intval($data['goods_quality_sysno']);
            $qualityname = $this->dbh->select_one($sql);

            $input = array(
                'cost_sysno' => $data['doc_sysno'],
                'costno' => COMMON::getCodeId('F1'),
                'costdate' => $costdate,
                'costdateend' => $costdaeend,
                'isexceedfirst' => 0,
                'isstoragetank' => $data['storagetank_sysno'] ? 1 : 0,
                'storagetank_sysno' => $data['storagetank_sysno'],
                'goods_sysno' => $data['goods_sysno'],
                'goods_quality_sysno' => $data['goods_quality_sysno'],
                'customer_sysno' => $data['customer_sysno'],
                'customer_name' => $data['customer_name'],
                'totalprice' => $totalprice,
                'costtype' => 0,
                'costname' => $data['storagetank_sysno'] ? '包罐租金' : '包罐容费',
                'coststatus' => 1,
                'contract_sysno' => $data['contract_sysno'],
                'contract_no' => $data['contract_no'],
                'iscalc' => 1,
                'status' => 1,
                'isdel' => 0,
                'version' => 1,
                'created_at' => '=NOW()',
                'updated_at' => '=NOW()',
                'storagetankname' => $storagetankname ? $storagetankname : '',
                'goodsname' => $goodsname ? $goodsname : '',
                'qualityname' => $qualityname ? $qualityname : '',
            );
            // error_log(date("Y-m-d H:i:s")  . "\t" . json_encode($input) . "\n", 3, './logs/cost.log');
            $res = $this->dbh->insert(DB_PREFIX.'doc_finance_cost_detail', $input);
            if (!$res) {
                return false;
            }
        }

        return true;
    }

    ##新增靠泊装卸费用
    public function addFinancecostByBerth($data = array(), $berthtype = 1, $tpnum = 1)
    {
        //berthtype:1装货2卸货
        if (!is_array($data) || intval($tpnum <= 0)) {
            return false;
        }

        $costdate = $data['contractcostdate'];
        $costdaeend = $data['contractcostdate'];

        $totalnum = 0;
        #查询入库总值
        $sql_in = "select s.* from `".DB_PREFIX."storage_stock` s left join `".DB_PREFIX."doc_stock_in` sd on (sd.`sysno`=s.`firstfrom_sysno`)  where sd.stockinstatus=4 and s.isdel = 0 and s.status = 1 and s.iscurrent = 1 and doctype in (1,2,4) and s.`customer_sysno` = ".$data['customer_sysno']." and s.`goods_sysno` = ".$data['goods_sysno']." and s.`contract_sysno` = ".$data['contract_sysno']." ";

        $stockinfo_in = $this->dbh->select($sql_in);

        if (!empty($stockinfo_in)) {
            foreach ($stockinfo_in as $key => $value) {
                $totalnum = $totalnum + $value['instockqty'];
            }
        }
        $sql = "select * from ".DB_PREFIX."doc_contract_goods where contract_sysno=" . $data['contract_sysno'] . " and goods_sysno=" . $data['goods_sysno'] . " and if(isladder>0,".$totalnum.">=ladderstart,isdel=0) and if(isladder>0,".$totalnum."<=if(ladderend>0,ladderend,99999999999999999999),isdel=0) ";
        $cost = $this->dbh->select_row($sql);
        if (!$cost) {
            return true;
        }else
        {
            if($berthtype==1)
            {
                $data['unitprice'] = $cost['berthcost'];
            }
            else
            {
                if($data['goodsnature']==4)
                {
                    $data['unitprice'] = $cost['berthcostdomestic'];
                }
                else
                {
                    $data['unitprice'] = $cost['berthcostforeign'];
                }
            }
        }

        for ($i = 1; $i <= $tpnum; $i++) {

            $coststatus = 1;
            $totalprice = $data['costqty']*$data['unitprice'];
            if($totalprice<=0)
            {
                continue;
            }

            // $sql = "select storagetankname from ".DB_PREFIX."base_storagetank where sysno=" . intval($data['storagetank_sysno']);
            // $storagetankname = $this->dbh->select_one($sql);
            $sql = "select goodsname from ".DB_PREFIX."base_goods where sysno=" . intval($data['goods_sysno']);
            $goodsname = $this->dbh->select_one($sql);
            // $sql = "select qualityname from ".DB_PREFIX."base_goods_quality where sysno=" . intval($data['goods_quality_sysno']);
            // $qualityname = $this->dbh->select_one($sql);

            $input = array(
                'costno' => COMMON::getCodeId('F3'),
                'costdate' => $costdate,
                'costdateend' => $costdaeend,
                'isexceedfirst' => 0,
                'isstoragetank' => $data['storagetank_sysno'] ? 1 : 0,
                'storagetank_sysno' => $data['storagetank_sysno'],
                'goods_sysno' => $data['goods_sysno'],
                'goods_quality_sysno' => $data['goods_quality_sysno'],
                'customer_sysno' => $data['customer_sysno'],
                'customer_name' => $data['customer_name'],
                'costqty' => $data['costqty'],
                'unitprice' => $data['unitprice'],
                'totalprice' => $totalprice,
                'costtype' => '-1',
                'costname' => $berthtype==1 ? '靠泊装卸费(装船)' : '靠泊装卸费(卸船)',
                'coststatus' => 2,
                'contract_sysno' => $data['contract_sysno'],
                'contract_no' => $data['contract_no'],
                'shipname' => $data['shipname'],
                'iscalc' => 1,
                'status' => 1,
                'isdel' => 0,
                'version' => 1,
                'created_at' => '=NOW()',
                'updated_at' => '=NOW()',
                'storagetankname' => $storagetankname ? $storagetankname : '',
                'goodsname' => $goodsname ? $goodsname : '',
                'qualityname' => $qualityname ? $qualityname : '',
                'berthtype' => $berthtype,
                'goodsnature' => $data['goodsnature'],
            );
            // error_log(date("Y-m-d H:i:s")  . "\t" . json_encode($input) . "\n", 3, './logs/cost.log');
            $res = $this->dbh->insert(DB_PREFIX.'doc_finance_cost_detail', $input);
            if (!$res) {
                return false;
            }
        }

        return true;
    }

    ##定时新增仓储费
    public function addFinancecostByPlan()
    {
        $costdate = date("Y-m-d");

        $sql = "select s.*,if(s.doctype=3, NULL,s.firstfrom_sysno) as stockin_sysno,(select td.stocktrans_sysno from `".DB_PREFIX."doc_stock_trans_detail` td where s.`sysno` = td.`in_stock_sysno` group by td.`in_stock_sysno`) as stocktrans_sysno,t.storagetankname from `".DB_PREFIX."storage_stock` s left join `".DB_PREFIX."base_storagetank` t on (t.sysno=s.storagetank_sysno) where  s.isdel = 0 and s.status = 1 and s.iscurrent = 1 and s.stockqty>0 and s.financedate<'" . $costdate . "' ";
        $data = $this->dbh->select($sql);
        // print_r($data);exit;

        if (empty($data)) {
            return true;
        }
        foreach ($data as $key => $value) {
            /*if($value['financedate']=="0000-00-00 00:00:00" || empty($value['financedate']))
            {
                $diffday = 1;
            }
            else
            {
                $diffday =  COMMON::count_days(strtotime(date("Y-m-d")),strtotime($value['financedate']));
            }
            
            if($diffday<=0)
            {
                #不需要计算，结算日期未到
                continue;
            }*/
            ##暂时没有删除费用单不考虑补差额情况下diffday为1
            $diffday = 1;
            #根据相差天数补单
            for ($i = 0; $i < $diffday; $i++) {
                #计算费用单产生日期是否已经匹配库存$i=0为当天
                $yes_costdate = date("Y-m-d", strtotime('-' . $i . ' day'));

                $sql = "select count(*) as num from `".DB_PREFIX."doc_finance_cost_detail` fd where fd.isdel = 0 and fd.status = 1 and fd.costtype = 0 and fd.stock_sysno=" . intval($value['sysno']) . " and costdate=" . $yes_costdate . " ";
                $num = $this->dbh->select_one($sql);

                if ($num < 1) {
                    if ($i != 0) {
                        $old_sysno = $value['sysno'];
                        #如果不是当天sql变成获取幽灵数据
                        $sql = "select s.*,if(s.doctype=3, NULL,s.firstfrom_sysno) as stockin_sysno,(select td.stocktrans_sysno from `".DB_PREFIX."doc_stock_trans_detail` td where s.`sysno` = td.`in_stock_sysno` group by td.`in_stock_sysno`) as stocktrans_sysno,t.storagetankname from `".DB_PREFIX."storage_stock` s left join `".DB_PREFIX."base_storagetank` t on (t.sysno=s.storagetank_sysno) where  s.isdel = 0 and s.status = 1 and s.iscurrent = 0 and s.stockqty>0 and ghostsysno=" . $old_sysno . " and ghosttime<='" . $value['financedate'] . " 23:59:59' order by ghosttime desc";
                        #value变量从iscurrent的记录变成幽灵记录
                        $value = $this->dbh->select_row($sql);
                        $value['sysno'] = $old_sysno;
                    }

                    #新增仓储费记录
                    if ($value['stocktrans_sysno']) {
                        $sql = "select c.`contractnodisplay`,c.`contracttype`,t.`buystartdate`,t.sale_customer_sysno,t.sale_customername,t.buy_customer_sysno,t.buy_customername,t.cost_contract_type from `".DB_PREFIX."doc_stock_trans` t left join `".DB_PREFIX."doc_contract` c on (c.`sysno`=if(cost_contract_type=1,t.sale_contract_sysno,t.`contract_sysno`)) where t.`sysno`=" . intval($value['stocktrans_sysno']);
                    } elseif ($value['stockin_sysno']) {
                        $sql = "select c.`contractnodisplay`,c.`contracttype` from `".DB_PREFIX."doc_stock_in` t left join `".DB_PREFIX."doc_contract` c on (c.`sysno`=t.`contract_sysno`) where  t.`sysno`=" . intval($value['stockin_sysno']);
                    } else {
                        // error_log(date("Y-m-d H:i:s") . "\t" . $value['sysno'] . "\t" . json_encode($sql) . "\n", 3, './logs/financecost_empty_'.$costdate.'.log');
                        continue;
                    }

                    #得到合同类型和货权转移的开始计费日期
                    $financecostdetaildata = $this->dbh->select_row($sql);

                    if ($value['firstdate'] > date('Y-m-d')) {
                        $isexceedfirst = 0;
                    } else {
                        $isexceedfirst = 1;
                    }

                    $contract_sysno = $value['contract_sysno'];
                    $contract_no = $financecostdetaildata['contractnodisplay'];
                    $contracttype = $financecostdetaildata['contracttype'];
                    #首次记录的入库单
                    $instock_sysno = $value['firstfrom_sysno'];
                    if ($contracttype == 3 || $contracttype == 4) {
                        $isstoragetank = 1;
                    } elseif ($contracttype == 1 || $contracttype == 2) {
                        $isstoragetank = 0;
                    } else {
                        continue;
                    }

                    #获取合同价格
                    $sql = "select * from ".DB_PREFIX."doc_contract_goods where contract_sysno=" . $contract_sysno . " and goods_sysno=" . $value['goods_sysno'] . "  ";
                    $cost = $this->dbh->select_row($sql);

                    #判断是否包罐
                    if ($isstoragetank == 1) {
                        #计算溢罐量
                        #先判断是否超出溢罐量
                        if ($value['overflag'] > 0) {
                            #再判断是否全部超出溢罐量
                            if ($value['overqty'] > 0) {
                                $costqty = $value['outstockqty'] <= $value['overqty'] ? $value['overqty'] - $value['outstockqty'] : 0;
                            } else {
                                $costqty = 0;
                            }

                            #判断是否溢罐首期
                            if ($isexceedfirst == 0) {
                                // //溢罐首期按照溢罐量字段来计算
                                // $costqty = $value['overqty'];
                                // #首期溢罐仓储费=当前数量*溢罐首期单价
                                // $unitprice = $cost['overfirstpayment'];
                                // $totalprice = $costqty*$unitprice;
                                // $costname = '溢罐首期费';
                                // if($totalprice<=0)
                                // {
                                //     // error_log(date("Y-m-d H:i:s") . "\t" . $value['sysno'] . "\t" . json_encode($cost) . "\n", 3, './logs/financecost_istank_'.$costdate.'.log');
                                //     continue;
                                // }
                                // else
                                // {
                                //     $costinfo['costdate'] = $costdate;
                                //     $costinfo['isexceedfirst'] = $isexceedfirst;
                                //     $costinfo['isstoragetank'] = $isstoragetank;
                                //     $costinfo['shipname'] = $value['shipname'];
                                //     $costinfo['goods_sysno'] = $value['goods_sysno'];
                                //     $costinfo['goods_quality_sysno'] = $value['goods_quality_sysno'];
                                //     $costinfo['customer_sysno'] = $value['customer_sysno'];
                                //     $costinfo['customer_name'] = $value['customername'];
                                //     $costinfo['costqty'] = $costqty;
                                //     $costinfo['unitprice'] = $unitprice;
                                //     $costinfo['totalprice'] = $totalprice;
                                //     $costinfo['costname'] = $costname;
                                //     $costinfo['stock_sysno'] = $value['sysno'];
                                //     $costinfo['instock_sysno'] = $value['stockin_sysno'];
                                //     $costinfo['contract_sysno'] = $contract_sysno;
                                //     $costinfo['stockinno'] = $value['firstfrom_no'];
                                //     $costinfo['contract_no'] = $contract_no;
                                //     #如果是首期，则结算日算到首期到期日结束
                                //     $costinfo['costdate_ed'] = $value['firstdate'];
                                //     $costinfo['costtype'] = 0;
                                //     $costinfo['qualityname'] = $value['goodsqualityname'];
                                //     $costinfo['storagetankname'] = $value['storagetankname'];
                                //     $costinfo['goodsname'] = $value['goodsname'];
                                //     $costinfo['costdateend'] = $value['firstdate'];

                                //     $this->insertcost($costinfo);
                                // }

                            } else {
                                #超期溢罐仓储费=当前数量*溢罐超期单价
                                $unitprice = $cost['overlastpayment'];
                                $totalprice = $costqty * $unitprice;
                                $costname = '溢罐超期费';
                                if ($totalprice <= 0) {
                                    // error_log(date("Y-m-d H:i:s") . "\t" . $value['sysno'] . "\t" . json_encode($cost) . "\n", 3, './logs/financecost_istank_'.$costdate.'.log');
                                    continue;
                                }
                                $costinfo['costdate'] = $costdate;
                                $costinfo['isexceedfirst'] = $isexceedfirst;
                                $costinfo['isstoragetank'] = $isstoragetank;
                                $costinfo['shipname'] = $value['shipname'];
                                $costinfo['goods_sysno'] = $value['goods_sysno'];
                                $costinfo['goods_quality_sysno'] = $value['goods_quality_sysno'];
                                $costinfo['customer_sysno'] = $value['customer_sysno'];
                                $costinfo['customer_name'] = $value['customername'];
                                $costinfo['costqty'] = $costqty;
                                $costinfo['unitprice'] = $unitprice;
                                $costinfo['totalprice'] = $totalprice;
                                $costinfo['costname'] = $costname;
                                $costinfo['stock_sysno'] = $value['sysno'];
                                $costinfo['instock_sysno'] = $value['stockin_sysno'];
                                $costinfo['contract_sysno'] = $contract_sysno;
                                $costinfo['stockinno'] = $value['firstfrom_no'];
                                $costinfo['contract_no'] = $contract_no;
                                $costinfo['costdate_ed'] = $costdate;
                                $costinfo['costtype'] = 0;
                                $costinfo['qualityname'] = $value['goodsqualityname'];
                                $costinfo['storagetankname'] = $value['storagetankname'];
                                $costinfo['goodsname'] = $value['goodsname'];
                                #损耗执行后查找库存损耗值
                                $sql = "select * from ".DB_PREFIX."storage_stock_batlog where left(created_at,10)='".date("Y-m-d")."' and stock_sysno=" . $value['sysno'] . " and introduction_detail_sysno=0  ";
                                $ullageinfo = $this->dbh->select_row($sql);
                                $ullage = $ullageinfo['beqty'] ? $ullageinfo['beqty'] : 0;
                                $costinfo['ullage'] = $ullage;

                                $this->insertcost($costinfo);
                            }
                        } else {
                            #没有超出溢罐量不计算费用
                            // error_log(date("Y-m-d H:i:s") . "\t" . $value['sysno'] . "\t" . json_encode($cost) . "\n", 3, './logs/financecost_istank_'.$costdate.'.log');
                        }

                    } #非包罐
                    else {
                        $costqty = $value['instockqty'];

                        #先判断是否首期
                        if ($isexceedfirst == 0) {
                            #0324改成审核和入库终止产生首期费用
                            #仓储费=入库数量*首期单价
                            // $unitprice = $cost['firststorageamount'];
                            // $totalprice = $costqty*$unitprice;
                            // $costname = '首期储罐使用费';

                            // if($totalprice<=0)
                            // {
                            //     // error_log(date("Y-m-d H:i:s") . "\t" . $value['sysno'] . "\t" . json_encode($cost) . "\n", 3, './logs/financecost_istank_'.$costdate.'.log');
                            //     continue;
                            // }
                            // else
                            // {
                            //     $costinfo['costdate'] = $costdate;
                            //     $costinfo['isexceedfirst'] = $isexceedfirst;
                            //     $costinfo['isstoragetank'] = $isstoragetank;
                            //     $costinfo['shipname'] = $value['shipname'];
                            //     $costinfo['goods_sysno'] = $value['goods_sysno'];
                            //     $costinfo['goods_quality_sysno'] = $value['goods_quality_sysno'];
                            //     $costinfo['customer_sysno'] = $value['customer_sysno'];
                            //     $costinfo['customer_name'] = $value['customername'];
                            //     $costinfo['costqty'] = $costqty;
                            //     $costinfo['unitprice'] = $unitprice;
                            //     $costinfo['totalprice'] = $totalprice;
                            //     $costinfo['costname'] = $costname;
                            //     $costinfo['stock_sysno'] = $value['sysno'];
                            //     $costinfo['instock_sysno'] = $stockin_sysno;
                            //     $costinfo['contract_sysno'] = $contract_sysno;
                            //     $costinfo['stockinno'] = $value['firstfrom_no'];
                            //     $costinfo['contract_no'] = $contract_no;
                            //     #如果是首期，则结算日算到首期到期日结束
                            //     $costinfo['costdate_ed'] = $value['firstdate'];
                            //     $costinfo['costtype'] = 0;
                            //     $costinfo['qualityname'] = $value['goodsqualityname'];
                            //     $costinfo['storagetankname'] = $value['storagetankname'];
                            //     $costinfo['goodsname'] = $value['goodsname'];
                            //     $costinfo['costdateend'] = $value['firstdate'];

                            //     $this->insertcost($costinfo);
                            // }

                        } else {
                            #再判断是否有受让方计费起始日到期了
                            if (isset($financecostdetaildata['buystartdate'])) {
                                if ($financecostdetaildata['buystartdate'] < $costdate) {
                                    // $diffday =  COMMON::count_days(strtotime(date("Y-m-d")),strtotime($financecostdetaildata['buystartdate']));
                                    $diffday = 1;//固定下家算一次费用
                                    for ($i = 0; $i < $diffday; $i++) {
                                        #如果开始计费
                                        #转让超期仓储费=余额数量 *超期单价
                                        #余量计算超期
                                        $costqty = $value['stockqty'];
                                        $unitprice = $cost['lastamount'];
                                        $totalprice = $costqty * $unitprice;
                                        $costname = '货权转移超期费';

                                        if ($totalprice <= 0) {
                                            // error_log(date("Y-m-d H:i:s") . "\t" . $value['sysno'] . "\t" . json_encode($cost) . "\n", 3, './logs/financecost_istank_'.$costdate.'.log');
                                            continue;
                                        } else {
                                            // $costinfo['costdate'] = date("Y-m-d", strtotime('' . $financecostdetaildata['buystartdate'] . '+'.$i.' day'));
                                            $costinfo['costdate'] = date("Y-m-d", strtotime("-1 day"));
                                            $costinfo['isexceedfirst'] = $isexceedfirst;
                                            $costinfo['isstoragetank'] = $isstoragetank;
                                            $costinfo['shipname'] = $value['shipname'];
                                            $costinfo['goods_sysno'] = $value['goods_sysno'];
                                            $costinfo['goods_quality_sysno'] = $value['goods_quality_sysno'];
                                            $costinfo['customer_sysno'] = $financecostdetaildata['buy_customer_sysno'];
                                            $costinfo['customer_name'] = $financecostdetaildata['buy_customername'];
                                            $costinfo['costqty'] = $costqty;
                                            $costinfo['unitprice'] = $unitprice;
                                            $costinfo['totalprice'] = $totalprice;
                                            $costinfo['costname'] = $costname;
                                            $costinfo['stock_sysno'] = $value['sysno'];
                                            $costinfo['instock_sysno'] = $stockin_sysno;
                                            $costinfo['contract_sysno'] = $contract_sysno;
                                            $costinfo['stockinno'] = $value['firstfrom_no'];
                                            $costinfo['contract_no'] = $contract_no;
                                            #如果是首期，则结算日算到首期到期日结束
                                            $costinfo['costdate_ed'] = date("Y-m-d");
                                            $costinfo['costdateend'] = date("Y-m-d", strtotime("-1 day"));
                                            $costinfo['costtype'] = 0;
                                            $costinfo['qualityname'] = $value['goodsqualityname'];
                                            $costinfo['storagetankname'] = $value['storagetankname'];
                                            $costinfo['goodsname'] = $value['goodsname'];
                                            #损耗执行后查找库存损耗值
                                            $sql = "select * from ".DB_PREFIX."storage_stock_batlog where left(created_at,10)='".date("Y-m-d")."' and stock_sysno=" . $value['sysno'] . " and introduction_detail_sysno=0  ";
                                            $ullageinfo = $this->dbh->select_row($sql);
                                            $ullage = $ullageinfo['beqty'] ? $ullageinfo['beqty'] : 0;
                                            $costinfo['ullage'] = $ullage;

                                            $this->insertcost($costinfo);
                                        }
                                    }

                                } else {
                                    //算上家
                                    $costqty = $value['stockqty'];
                                    $unitprice = $cost['lastamount'];
                                    $totalprice = $costqty * $unitprice;
                                    $costname = '货权转移超期费';

                                    if ($totalprice <= 0) {
                                        // error_log(date("Y-m-d H:i:s") . "\t" . $value['sysno'] . "\t" . json_encode($cost) . "\n", 3, './logs/financecost_istank_'.$costdate.'.log');
                                        continue;
                                    } else {
                                        // $costinfo['costdate'] = date("Y-m-d", strtotime('' . $financecostdetaildata['buystartdate'] . '+'.$i.' day'));
                                        $costinfo['costdate'] = date("Y-m-d", strtotime("-1 day"));
                                        $costinfo['isexceedfirst'] = $isexceedfirst;
                                        $costinfo['isstoragetank'] = $isstoragetank;
                                        $costinfo['shipname'] = $value['shipname'];
                                        $costinfo['goods_sysno'] = $value['goods_sysno'];
                                        $costinfo['goods_quality_sysno'] = $value['goods_quality_sysno'];
                                        $costinfo['customer_sysno'] = $financecostdetaildata['sale_customer_sysno'];
                                        $costinfo['customer_name'] = $financecostdetaildata['sale_customername'];
                                        $costinfo['costqty'] = $costqty;
                                        $costinfo['unitprice'] = $unitprice;
                                        $costinfo['totalprice'] = $totalprice;
                                        $costinfo['costname'] = $costname;
                                        $costinfo['stock_sysno'] = $value['sysno'];
                                        $costinfo['instock_sysno'] = $stockin_sysno;
                                        $costinfo['contract_sysno'] = $contract_sysno;
                                        $costinfo['stockinno'] = $value['firstfrom_no'];
                                        $costinfo['contract_no'] = $contract_no;
                                        #如果是首期，则结算日算到首期到期日结束
                                        $costinfo['costdate_ed'] = date("Y-m-d");
                                        $costinfo['costdateend'] = date("Y-m-d", strtotime("-1 day"));
                                        $costinfo['costtype'] = 0;
                                        $costinfo['qualityname'] = $value['goodsqualityname'];
                                        $costinfo['storagetankname'] = $value['storagetankname'];
                                        $costinfo['goodsname'] = $value['goodsname'];
                                        #损耗执行后查找库存损耗值
                                        $sql = "select * from ".DB_PREFIX."storage_stock_batlog where left(created_at,10)='".date("Y-m-d")."' and stock_sysno=" . $value['sysno'] . " and introduction_detail_sysno=0  ";
                                        $ullageinfo = $this->dbh->select_row($sql);
                                        $ullage = $ullageinfo['beqty'] ? $ullageinfo['beqty'] : 0;
                                        $costinfo['ullage'] = $ullage;

                                        $this->insertcost($costinfo);
                                    }
                                }
                            } else {
                                #正常入库单超出首期计算
                                $costqty = $value['stockqty'];
                                $unitprice = $cost['lastamount'];
                                $totalprice = $costqty * $unitprice;
                                $costname = '超期费';

                                if ($totalprice <= 0) {
                                    // error_log(date("Y-m-d H:i:s") . "\t" . $value['sysno'] . "\t" . json_encode($cost) . "\n", 3, './logs/financecost_istank_'.$costdate.'.log');
                                    continue;
                                } else {
                                    //$costinfo['costdate'] = $costdate;

                                    //跑批超期费用 费用日期提前一天
                                    $chaoqiCostDate = date("Y-m-d",strtotime("-1 day"));
                                    $costinfo['costdate'] = $chaoqiCostDate;
                                    $costinfo['isexceedfirst'] = $isexceedfirst;
                                    $costinfo['isstoragetank'] = $isstoragetank;
                                    $costinfo['shipname'] = $value['shipname'];
                                    $costinfo['goods_sysno'] = $value['goods_sysno'];
                                    $costinfo['goods_quality_sysno'] = $value['goods_quality_sysno'];
                                    $costinfo['customer_sysno'] = $value['customer_sysno'];
                                    $costinfo['customer_name'] = $value['customername'];
                                    $costinfo['costqty'] = $costqty;
                                    $costinfo['unitprice'] = $unitprice;
                                    $costinfo['totalprice'] = $totalprice;
                                    $costinfo['costname'] = $costname;
                                    $costinfo['stock_sysno'] = $value['sysno'];
                                    $costinfo['instock_sysno'] = $stockin_sysno;
                                    $costinfo['contract_sysno'] = $contract_sysno;
                                    $costinfo['stockinno'] = $value['firstfrom_no'];
                                    $costinfo['contract_no'] = $contract_no;
                                    #如果是首期，则结算日算到首期到期日结束
                                    $costinfo['costdate_ed'] = date("Y-m-d");
                                    //$costinfo['costdateend'] = date("Y-m-d");
                                    $costinfo['costdateend'] = $chaoqiCostDate;
                                    $costinfo['costtype'] = 0;
                                    $costinfo['qualityname'] = $value['goodsqualityname'];
                                    $costinfo['storagetankname'] = $value['storagetankname'];
                                    $costinfo['goodsname'] = $value['goodsname'];
                                    #损耗执行后查找库存损耗值
                                    $sql = "select * from ".DB_PREFIX."storage_stock_batlog where left(created_at,10)='".date("Y-m-d")."' and stock_sysno=" . $value['sysno'] . " and introduction_detail_sysno=0  ";
                                    $ullageinfo = $this->dbh->select_row($sql);
                                    $ullage = $ullageinfo['beqty'] ? $ullageinfo['beqty'] : 0;
                                    $costinfo['ullage'] = $ullage;

                                    $this->insertcost($costinfo);
                                }
                            }
                        }

                    }

                    #执行结束
                }
            }

        }
        return true;
    }

    //计算首期费
    public function costfirst($params = array())
    {
        $costdate = date("Y-m-d", strtotime('+0 day'));

        #参数字段含义：contract_sysno合同ID,contract_no合同编号,stockin_sysno入库订单ID,stockin_no入库订单编号,instockqty入库数量,goods_sysno货品ID,goodsname货品名称,qualityname质量标准,firstdate首期到期日,customer_sysno客户ID,customer_name客户姓名,stock_sysno库存ID,storagetankname储罐号stockindate入库日期,overqty溢罐量
        #非必要参数shipname

        $totalnum = 0;
        #查询入库总值
        $sql_in = "select s.* from `".DB_PREFIX."storage_stock` s left join `".DB_PREFIX."doc_stock_in` sd on (sd.`sysno`=s.`firstfrom_sysno`)  where sd.stockinstatus=4 and s.isdel = 0 and s.status = 1 and s.iscurrent = 1 and doctype in (1,2,4) and s.`customer_sysno` = ".$params['customer_sysno']." and s.`goods_sysno` = ".$params['goods_sysno']." and s.`contract_sysno` = ".$params['contract_sysno']." ";

        $stockinfo_in = $this->dbh->select($sql_in);

        if (!empty($stockinfo_in)) {
            foreach ($stockinfo_in as $key => $value) {
                $totalnum = $totalnum + $value['instockqty'];
            }
        }

        $sql = "select * from ".DB_PREFIX."doc_contract_goods where contract_sysno=" . $params['contract_sysno'] . " and goods_sysno=" . $params['goods_sysno'] . " and if(isladder>0,".$totalnum.">=ladderstart,isdel=0) and if(isladder>0,".$totalnum."<=if(ladderend>0,ladderend,99999999999999999999),isdel=0) ";
        $cost = $this->dbh->select_row($sql);
        if (!$cost) {
            return true;
        }

        //增加有没有溢罐首期
        if ($params['overqty'] > 0 && isset($params['overqty'])) {
            //溢罐首期按照溢罐量字段来计算
            $costqty = $params['overqty'];
            #首期溢罐仓储费=当前数量*溢罐首期单价
            $unitprice = $cost['overfirstpayment'];
            $totalprice = $costqty * $unitprice;

            $costname = '溢罐首期费';
            if ($totalprice <= 0) {
                // continue;
            } else {
                $costinfo['cost_sysno'] = $params['doc_sysno'];
                $costinfo['costdate'] = $params['stockindate'];
                $costinfo['isexceedfirst'] = 0;
                $costinfo['isstoragetank'] = 0;
                $costinfo['shipname'] = $params['shipname'];
                $costinfo['goods_sysno'] = $params['goods_sysno'];
                $costinfo['goods_quality_sysno'] = $params['goods_quality_sysno'];
                $costinfo['customer_sysno'] = $params['customer_sysno'];
                $costinfo['customer_name'] = $params['customer_name'];
                $costinfo['costqty'] = $costqty;
                $costinfo['unitprice'] = $unitprice;
                $costinfo['totalprice'] = $totalprice;
                $costinfo['costname'] = $costname;
                $costinfo['stock_sysno'] = $params['stock_sysno'];
                $costinfo['instock_sysno'] = $params['stockin_sysno'];
                $costinfo['contract_sysno'] = $params['contract_sysno'];
                $costinfo['stockinno'] = $params['firstfrom_no'];
                $costinfo['contract_no'] = $params['contract_no'];
                #如果是首期，则结算日算到首期到期日结束
                $costinfo['costdate_ed'] = date("Y-m-d", strtotime('' . $params['stockindate'] . '+30 day'));
                $costinfo['costtype'] = 0;
                $costinfo['qualityname'] = $params['qualityname'];
                $costinfo['storagetankname'] = $params['storagetankname'];
                $costinfo['goodsname'] = $params['goodsname'];
                $costinfo['costdateend'] = date("Y-m-d", strtotime('' . $params['stockindate'] . '+30 day'));

                // $this->insertcost($costinfo); //建滔目前没有溢罐费用
            }
        }

        $costqty = $params['instockqty'];
        $unitprice = $cost['firststorageamount'];
        $totalprice = $costqty * $unitprice;
        $costname = '首期储罐使用费';

        if ($totalprice <= 0) {
            return true;
        } else {
            $costinfo['cost_sysno'] = $params['doc_sysno'];
            $costinfo['costdate'] = $params['stockindate'];
            $costinfo['isexceedfirst'] = 0;
            $costinfo['isstoragetank'] = 0;
            $costinfo['shipname'] = $params['shipname'];
            $costinfo['goods_sysno'] = $params['goods_sysno'];
            $costinfo['goods_quality_sysno'] = $params['goods_quality_sysno'];
            $costinfo['customer_sysno'] = $params['customer_sysno'];
            $costinfo['customer_name'] = $params['customer_name'];
            $costinfo['costqty'] = $costqty;
            $costinfo['unitprice'] = $unitprice;
            $costinfo['totalprice'] = $totalprice;
            $costinfo['costname'] = $costname;
            $costinfo['stock_sysno'] = $params['stock_sysno'];
            $costinfo['instock_sysno'] = $params['stockin_sysno'];
            $costinfo['contract_sysno'] = $params['contract_sysno'];
            $costinfo['stockinno'] = $params['firstfrom_no'];
            $costinfo['contract_no'] = $params['contract_no'];
            #如果是首期，则结算日算到首期到期日结束
            $costinfo['costdate_ed'] = date("Y-m-d", strtotime('' . $params['stockindate'] . '+30 day'));
            $costinfo['costtype'] = 0;
            $costinfo['qualityname'] = $params['qualityname'];
            $costinfo['storagetankname'] = $params['storagetankname'];
            $costinfo['goodsname'] = $params['goodsname'];
            $costinfo['costdateend'] = date("Y-m-d", strtotime('' . $params['stockindate'] . '+30 day'));
            $costinfo['ullage'] = $params['ullage'];

            $res = $this->insertcost($costinfo);
            return $res;
        }

        return true;
    }

    //生成货权转移超期费
    public function costover($params = array())
    {
        //修改成每天跑批生成货权转移超期费
        return true;
        $costdate = date("Y-m-d", strtotime('+0 day'));

        #参数字段含义：contract_sysno合同ID,contract_no合同编号,stockin_sysno入库订单ID,stockin_no入库订单编号,stockqty转移数量数量,goods_sysno货品ID,goodsname货品名称,qualityname质量标准,customer_sysno客户ID(转让方),customer_name客户姓名(转让方),stock_sysno库存ID,stockindate入库日期,buystartdate受让方计费日,datenum免仓超期费真正天数,buy_customer_sysno客户ID(受让方),buy_customer_name客户姓名(受让方),last_stock_sysno
        #非必要参数shipname

        if($params['datenum']<=0)
        {
            return true;
        }

        $sql = "select * from ".DB_PREFIX."doc_contract_goods where contract_sysno=" . $params['contract_sysno'] . " and goods_sysno=" . $params['goods_sysno'] . "  ";
        $cost = $this->dbh->select_row($sql);
        if (!$cost) {
            return true;
        }
        #找到上一家货权转移客户免仓了几天
        $sql = "select st.* from ".DB_PREFIX."doc_stock_trans_detail std left join ".DB_PREFIX."doc_stock_trans st on (std.stocktrans_sysno=st.sysno) where in_stock_sysno =".$params['last_stock_sysno']." ";
        $stockinfo = $this->dbh->select_row($sql);
        if(!$stockinfo)
        {
            $free = 0;
        }
        else
        {
            $diff = COMMON::count_days(strtotime($stockinfo['buystartdate']),strtotime(date("Y-m-d")));
            if($diff>0)
            {
                $free = $diff;
            }
            else
            {
                $free = 0;
            }
        }
        if($params['datenum']-$free>=0)
        {
            $saledate = $params['datenum']-$free;
            if($params['buystartdate']<=date("Y-m-d"))
            {
                $saledate = 0;  
            }
        }
        else
        {
            $saledate = 0;
        }
        for($i=0;$i<$saledate;$i++)
        {
            #余量计算超期
            $costqty = $params['stockqty'];
            $unitprice = $cost['lastamount'];
            $totalprice = $costqty * $unitprice;
            $costname = '货权转移超期费';
            if($totalprice<=0)
            {
                continue;
            }
            $costinfo['costdate'] = date("Y-m-d", strtotime('' . date("Y-m-d") . '+'.$i.' day'));
            $costinfo['isexceedfirst'] = 1;
            $costinfo['isstoragetank'] = 0;
            $costinfo['shipname'] = $params['shipname'];
            $costinfo['goods_sysno'] = $params['goods_sysno'];
            $costinfo['goods_quality_sysno'] = $params['goods_quality_sysno'];
            $costinfo['customer_sysno'] = $params['customer_sysno'];
            $costinfo['customer_name'] = $params['customer_name'];
            $costinfo['costqty'] = $costqty;
            $costinfo['unitprice'] = $unitprice;
            $costinfo['totalprice'] = $totalprice;
            $costinfo['costname'] = $costname;
            $costinfo['stock_sysno'] = $params['stock_sysno'];
            $costinfo['instock_sysno'] = $params['stockin_sysno'];
            $costinfo['contract_sysno'] = $params['contract_sysno'];
            $costinfo['stockinno'] = $params['stockin_no'];
            $costinfo['contract_no'] = $params['contract_no'];
            #如果是首期，则结算日算到首期到期日结束
            $costinfo['costdate_ed'] = date("Y-m-d", strtotime('' . date("Y-m-d") . '+'.$i.' day'));
            $costinfo['costdateend'] = date("Y-m-d", strtotime('' . date("Y-m-d") . '+'.$i.' day'));
            $costinfo['costtype'] = 0;
            $costinfo['qualityname'] = $params['qualityname'];
            $costinfo['goodsname'] = $params['goodsname'];
            #损耗执行后查找库存损耗值
            $sql = "select * from ".DB_PREFIX."storage_stock_batlog where left(created_at,10)='".date("Y-m-d")."' and stock_sysno=" . $params['stock_sysno'] . " and introduction_detail_sysno=0  ";
            $ullageinfo = $this->dbh->select_row($sql);
            $ullage = $ullageinfo['beqty'] ? $ullageinfo['beqty'] : 0;
            $costinfo['ullage'] = $ullage;

            $this->insertcost($costinfo);
        }
        
        return true;
    }

    //计算中转量
    public function costtransfer($params = array())
    {
        $costdate = date("Y-m-d", strtotime('+0 day'));

        #参数字段含义：contract_sysno合同ID,contract_no合同编号,stockin_sysno入库订单ID,stockin_no入库订单编号,instockqty入库数量,goods_sysno货品ID,goodsname货品名称,qualityname质量标准,firstdate首期到期日,customer_sysno客户ID,customer_name客户姓名,stock_sysno库存ID,transferqty中转量,storagetankname储罐号stockindate入库日期
        #非必要参数shipname

        $sql = "select cg.*,c.contracttype,c.contractstartdate,c.contractenddate from ".DB_PREFIX."doc_contract_goods cg left join ".DB_PREFIX."doc_contract c on (c.sysno=cg.contract_sysno) where cg.contract_sysno=" . $params['contract_sysno'];
        $cost = $this->dbh->select($sql);
        if (!$cost) {
            return false;
        }

        $exyearrate = 0;
        foreach ($cost as $key => $value) {
            $exyearrate = $exyearrate+$value['exyearrate'];
        }
        //取平均值
        $exyearrate = $exyearrate/count($cost);

        // $contractbegin = $cost['contractstartdate'] ? $cost['contractstartdate'] : $costdate;
        // $contractend = $cost['contractenddate'] ? $cost['contractenddate'] : $costdate;
        $contractbegin = $params['stockindate'];
        $contractend = $params['stockindate'];

        #计算中转量
        #先判断是否超出中转量
        if ($params['transferqty'] > 0) {
            $costqty = $params['transferqty'];
            # 超出中转量额外仓储费=超出数量*费率
            $unitprice = $exyearrate;
            $totalprice = $unitprice * $costqty;
            $costname = '超中转费';
            if ($totalprice <= 0) {
                // error_log(date("Y-m-d H:i:s") . "\t" . $params['stock_sysno'] . "\t" . json_encode($cost) . "\n", 3, './logs/financecost_notank_'.$costdate.'.log');
            } else {
                $costinfo['costdate'] = $contractbegin;
                $costinfo['costdateend'] = $contractend;
                $costinfo['isexceedfirst'] = $costdate <= $params['firstdate'] ? 0 : 1;
                $costinfo['isstoragetank'] = $cost[0]['contracttype'] > 2 ? 1 : 0;
                $costinfo['shipname'] = $params['shipname'];
                $costinfo['goods_sysno'] = $params['goods_sysno'];
                $costinfo['goods_quality_sysno'] = $params['goods_quality_sysno'];
                $costinfo['customer_sysno'] = $params['customer_sysno'];
                $costinfo['customer_name'] = $params['customer_name'];
                $costinfo['costqty'] = $costqty;
                $costinfo['unitprice'] = $unitprice;
                $costinfo['totalprice'] = $totalprice;
                $costinfo['costname'] = $costname;
                $costinfo['stock_sysno'] = $params['stock_sysno'];
                $costinfo['instock_sysno'] = $params['stockin_sysno'];
                $costinfo['contract_sysno'] = $params['contract_sysno'];
                $costinfo['stockinno'] = $params['stockin_no'];
                $costinfo['contract_no'] = $params['contract_no'];
                $costinfo['storagetankname'] = $params['storagetankname'];
                #如果是首期，则结算日算到首期到期日结束,中转量保持原来
                $costinfo['costdate_ed'] = date("Y-m-d");
                $costinfo['costtype'] = 0;
                $costinfo['qualityname'] = $params['qualityname'];
                $costinfo['goodsname'] = $params['goodsname'];
                #中转量直接待开票
                $costinfo['coststatus'] = 2;

                $res = $this->insertcost($costinfo);
                return $res;
            }

        }

        return true;
    }

    //计算首期管道输送费
    public function costtransportamount($params = array())
    {
        $costdate = date("Y-m-d", strtotime('+0 day'));

        #参数字段含义：contract_sysno合同ID,contract_no合同编号,stockin_sysno入库订单ID,stockin_no入库订单编号,instockqty入库数量,goods_sysno货品ID,goodsname货品名称,qualityname质量标准,firstdate首期到期日,customer_sysno客户ID,customer_name客户姓名,stock_sysno库存ID
        #非必要参数shipname

        $totalnum = 0;
        #查询入库总值
        $sql_in = "select s.* from `".DB_PREFIX."storage_stock` s left join `".DB_PREFIX."doc_stock_in` sd on (sd.`sysno`=s.`firstfrom_sysno`)  where s.isdel = 0 and s.status = 1 and s.iscurrent = 1 and doctype in (1,2,4) and s.`customer_sysno` = ".$params['customer_sysno']." and s.`goods_sysno` = ".$params['goods_sysno']." and s.`contract_sysno` = ".$params['contract_sysno']." ";

        $stockinfo_in = $this->dbh->select($sql_in);

        if (!empty($stockinfo_in)) {
            foreach ($stockinfo_in as $key => $value) {
                $totalnum = $totalnum + $value['instockqty'];
            }
        }

        $sql = "select * from ".DB_PREFIX."doc_contract_goods where contract_sysno=" . $params['contract_sysno'] . " and goods_sysno=" . $params['goods_sysno'] . " and if(isladder>0,".$totalnum.">=ladderstart,isdel=0) and if(isladder>0,".$totalnum."<=if(ladderend>0,ladderend,99999999999999999999),isdel=0) ";
        $cost = $this->dbh->select_row($sql);
        if (!$cost) {
            return true;
        }

        #如果有首期管道运输费,firsttransportamount
        if ($cost['firsttransportamount'] > 0) {
            $costqty = $params['instockqty'];
            $unitprice = $cost['firsttransportamount'];
            $totalprice = $costqty * $unitprice;
            $costname = '管道输送费';

            // $firstdate = date("Y-m-d", strtotime('' . $params['stockindate'] . '+30 day'));

            if ($totalprice <= 0) {
                // error_log(date("Y-m-d H:i:s") . "\t" . $params['stock_sysno'] . "\t" . json_encode($cost) . "\n", 3, './logs/financecost_notank_'.$costdate.'.log');
            } else {
                $costinfo['costdate'] = $costdate;
                $costinfo['isexceedfirst'] = 0;
                $costinfo['isstoragetank'] = 0;
                $costinfo['shipname'] = $params['shipname'];
                $costinfo['goods_sysno'] = $params['goods_sysno'];
                $costinfo['goods_quality_sysno'] = $params['goods_quality_sysno'];
                $costinfo['customer_sysno'] = $params['customer_sysno'];
                $costinfo['customer_name'] = $params['customer_name'];
                $costinfo['costqty'] = $costqty;
                $costinfo['unitprice'] = $unitprice;
                $costinfo['totalprice'] = $totalprice;
                $costinfo['costname'] = $costname;
                $costinfo['stock_sysno'] = $params['stock_sysno'];
                $costinfo['instock_sysno'] = $params['stockin_sysno'];
                $costinfo['contract_sysno'] = $params['contract_sysno'];
                $costinfo['stockinno'] = $params['stockin_no'];
                $costinfo['contract_no'] = $params['contract_no'];
                #如果是首期，则结算日算到首期到期日结束，管道费保持原来
                $costinfo['costdate_ed'] = date("Y-m-d");
                $costinfo['costtype'] = -1;//管道输送费固定算杂费类型-1
                $costinfo['qualityname'] = $params['qualityname'];
                $costinfo['goodsname'] = $params['goodsname'];
                $costinfo['coststatus'] = 2;

                $res = $this->insertcost($costinfo);
                return $res;
            }
        }

        return true;
    }


    public function insertcost($costinfo = array())
    {
        $costdate = date("Y-m-d");

        #新增仓储费明细
        $input = array(
            'cost_sysno' => $costinfo['cost_sysno'] ? $costinfo['cost_sysno'] : '',
            'costno' => COMMON::getCodeId('F1'),
            'costdate' => $costinfo['costdate'] ? $costinfo['costdate'] : $costdate,
            'costdateend' => $costinfo['costdateend'] ? $costinfo['costdateend'] : $costdate,
            'isexceedfirst' => $costinfo['isexceedfirst'],
            'isstoragetank' => $costinfo['isstoragetank'],
            'storagetank_sysno' => $costinfo['storagetank_sysno'],
            'shipname' => $costinfo['shipname'],
            'goods_sysno' => $costinfo['goods_sysno'],
            'goods_quality_sysno' => $costinfo['goods_quality_sysno'],
            'customer_sysno' => $costinfo['customer_sysno'],
            'customer_name' => $costinfo['customer_name'],
            'costqty' => $costinfo['costqty'],
            'unitprice' => $costinfo['unitprice'],
            'totalprice' => $costinfo['totalprice'],
            'costtype' => 0,
            'costname' => $costinfo['costname'],
            'costtype' => $costinfo['costtype'],
            'coststatus' => $costinfo['coststatus'] ? $costinfo['coststatus'] : 1,
            'stock_sysno' => $costinfo['stock_sysno'],
            'instock_sysno' => $costinfo['instock_sysno'],
            'stockinno' => $costinfo['stockinno'],
            'contract_sysno' => $costinfo['contract_sysno'],
            'contract_no' => $costinfo['contract_no'],
            'qualityname' => $costinfo['qualityname'],
            'storagetankname' => $costinfo['storagetankname'],
            'goodsname' => $costinfo['goodsname'],
            'unitname' => '吨',
            'iscalc' => 0,
            'status' => 1,
            'isdel' => 0,
            'version' => 1,
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
            'ullage' => $costinfo['ullage'] ? $costinfo['ullage'] : 0,
        );

        // error_log(date("Y-m-d H:i:s") . "\t" . $costinfo['stock_sysno'] . "\t" . json_encode($costinfo) . "\n", 3, './logs/financecost_input_'.$costdate.'.log');

        $res = $this->dbh->insert(DB_PREFIX.'doc_finance_cost_detail', $input);

        if ($res) {
            #结算费用日期回写
            $update = array(
                'financedate' => $costinfo['costdate_ed'],
            );
            $ret = $this->dbh->update(DB_PREFIX.'storage_stock', $update, 'sysno=' . intval($costinfo['stock_sysno']));

            // error_log(date("Y-m-d H:i:s") . "\t" . $res . "\t" . json_encode($input) . "\n", 3, './logs/financecost_ok_'.$costdate.'.log');
        } else {
            return false;
        }

        return true;
    }

    public function searchCostDetail($params)
    {
        $filter = array();
        if (isset($params['bar_customer']) && $params['bar_customer'] != '') {
            $filter[] = " fd.`customer_sysno` = '{$params['bar_customer']}' ";
        }
        if (isset($params['bar_costtype']) && $params['bar_costtype'] != '') {
            $filter[] = " fd.`costtype` = '{$params['bar_costtype']}' ";
        }
        if (isset($params['bar_coststatus']) && $params['bar_coststatus'] != '') {
            $filter[] = " fd.`coststatus`='{$params['bar_coststatus']}'";
        }
        if (isset($params['bar_coststartdate']) && $params['bar_coststartdate'] != '') {
            $filter[] = " fd.`costdate` >= '{$params['bar_coststartdate']}'";
        }
        if (isset($params['bar_costenddate']) && $params['bar_costenddate'] != '') {
            $filter[] = " fd.`costdate` <= '{$params['bar_costenddate']}'";
        }
        if (isset($params['berthcost']) && $params['berthcost'] != '') {
            $filter[] = " fd.`costtype` != '-1' ";
        }
        if (isset($params['bar_goodsname']) && $params['bar_goodsname'] != ''){
            $filter[] = " fd.`goodsname` = '{$params['bar_goodsname']}'";
        }
        if (isset($params['bar_shipname']) && $params['bar_shipname'] != ''){
            $filter[] = " fd.`shipname` = '{$params['bar_shipname']}'";
        }
        $where = 'fd.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "select count(*),ss.instockqty from `".DB_PREFIX."doc_finance_cost_detail` fd 
                left join `".DB_PREFIX."storage_stock` ss on fd.stock_sysno = ss.sysno
                where {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT fd.*,ss.instockqty from `".DB_PREFIX."doc_finance_cost_detail` fd  
                left join `".DB_PREFIX."storage_stock` ss on fd.stock_sysno = ss.sysno
                where {$where} ";
                if ($params['orders'] != '')
                    $sql .= " order by " . $params['orders'];
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT fd.*,ss.instockqty from `".DB_PREFIX."doc_finance_cost_detail` fd  
                left join `".DB_PREFIX."storage_stock` ss on fd.stock_sysno = ss.sysno
                where {$where} ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        for($i=0;$i<count($result['list']);$i++) {
            if ($result['list'][$i]['costname'] == '溢罐首期费' || $result['list'][$i]['costname'] == '首期储罐使用费') {
                $result['list'][$i]['datenum'] = 30;
            } else {
                $result['list'][$i]['datenum'] = COMMON::count_days(strtotime(date("Y-m-d")), strtotime($result['list'][$i]['costdate']));
            }
        }
        return $result;
    }

    public function updateStatus($date)
    {
        if ($date) {
            $ids = $date['ids'];
            $status = array(
                'iscalc' => $date['iscalc'],
                'coststatus' => $date['coststatus'],
                'updated_at' => '=NOW()',
            );
            $res = $this->dbh->update(DB_PREFIX.'doc_finance_cost_detail', $status, 'sysno in (' . $ids . ')');
        }
        return $res;
    }

    public function updateStatusByInvoiceSysno($data)
    {

        return $this->dbh->update(DB_PREFIX.'doc_finance_cost_detail', $data['status'], 'invoice_sysno =' . intval($data['invoice_sysno']));

    }
    public function updateDetail($data)
    {
        return $this->dbh->update(DB_PREFIX.'doc_finance_cost_detail', $data['status'], 'sysno =' . intval($data['sysno']));
    }

    /**
     * @param $sysno
     * @return mixed
     * @title 根据入库单编号 查询费用状态
     */
    public function serachCostBystockId($sysno){
        $sql = "SELECT detail.coststatus FROM ".DB_PREFIX."doc_finance_cost_detail AS detail 
                LEFT JOIN ".DB_PREFIX."doc_stock_in AS stockin ON stockin.sysno = detail.stock_sysno
                where stockin.sysno = ".intval($sysno);
        return $this->dbh->select($sql);
    }

    /*
     * 费用删除
     */
    public function delcost($conid = 0, $data = array())
    {
        return  $this->dbh->update(DB_PREFIX.'doc_finance_cost_detail', $data, 'contract_sysno=' . intval($conid));

    }

    #修改费用
    public function updatedetailFinancecost($data)
    {
        $sysno=$data['sysno'];
        $input=array(
            'totalprice'=>$data['oldtotalprice'],
            'oldtotalprice'=>$data['totalprice'],
            'updated_at' => '=NOW()',
        );
            return  $this->dbh->update(DB_PREFIX.'doc_finance_cost_detail', $input, 'sysno=' . intval($sysno));
    }

}
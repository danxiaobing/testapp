<?php

/**
 * Stockout Model
 *
 */
class InvoiceModel
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

    public function searchInvoice($params)
    {
        $filter = array();
        if (isset($params['bar_startdate']) && $params['bar_startdate'] != 0) {
            $filter[] = " s.`invoicedate` >= '{$params['bar_startdate']}' ";
        }
        if (isset($params['bar_enddate']) && $params['bar_enddate'] != 0) {
            $filter[] = " s.`invoicedate` <= '{$params['bar_enddate']}' ";
        }
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " s.`customer_sysno`='{$params['bar_name']}'";
        }
        // if (isset($params['bar_cost']) && $params['bar_cost'] != '') {
        //     $filter[] = " s.`costtype`='{$params['bar_cost']}'";
        // }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != 0) {
            $filter[] = " s.`customer_sysno`='{$params['customer_sysno']}'";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '') {
            $filter[] = " s.`invoicestatus`='{$params['bar_status']}'";
        }
        if (isset($params['invoice_sysno']) && $params['invoice_sysno'] != '-100') {
            $filter[] = " s.`sysno` not in ({$params['invoice_sysno']})";
        }
        if (isset($params['invoice_company_sysno']) && $params['invoice_company_sysno'] != '') {
            $filter[] = " s.`invoice_company_sysno`='{$params['invoice_company_sysno']}'";
        }

        $where = 's.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_finance_invoice` s 
            LEFT JOIN ".DB_PREFIX."customer as c on c.sysno = s.customer_sysno
            where {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT s.*,c.receivablecost as receivablecostbycu,c.writeoffcost as writeoffcostbycu,c.remaincost as remaincostbycu FROM `".DB_PREFIX."doc_finance_invoice` s 
                  LEFT JOIN ".DB_PREFIX."customer as c on c.sysno = s.customer_sysno 
                  where {$where} ";
                if ($params['orders'] != '')
                    $sql .= " order by s." . $params['orders'];
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT s.*,c.receivablecost as receivablecostbycu,c.writeoffcost as writeoffcostbycu,c.remaincost as remaincostbycu  FROM `".DB_PREFIX."doc_finance_invoice` s 
                    LEFT JOIN ".DB_PREFIX."customer as c on c.sysno = s.customer_sysno 
                    where {$where} ";

                if ($params['orders'] != '') {
                    $sql .= " order by s." . $params['orders'];
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    public function getInvoiceDetailList($params)
    {

        $filter = array();
        if (isset($params['invoice_sysno']) && $params['invoice_sysno'] != '') {
            $filter[] = " s.`invoice_sysno` = '" . $params['invoice_sysno'] . "' ";
        }
        $where = 's.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_finance_invoice_detail` s  where {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT  d.*, s.sysno  isysno,si.takegoodsnum as takegoodsnum FROM `".DB_PREFIX."doc_finance_invoice_detail` s left join ".DB_PREFIX."doc_finance_cost_detail d on d.sysno = s.cost_detail_sysno 
                        left join `".DB_PREFIX."doc_stock_in` si on si.sysno = d.instock_sysno where {$where} ";

                if ($params['orders'] != '')
                    $sql .= " order by s." . $params['orders'];
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT d.*, s.sysno isysno,si.takegoodsnum as takegoodsnum FROM `".DB_PREFIX."doc_finance_invoice_detail` s left join ".DB_PREFIX."doc_finance_cost_detail d on d.sysno = s.cost_detail_sysno 
                        left join `".DB_PREFIX."doc_stock_in` si on si.sysno = d.instock_sysno where {$where}  ";
                if ($params['orders'] != '') {
                    $sql .= " order by s." . $params['orders'];
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
            foreach ($result['list'] as $k=>$v){
                $created_at = substr($v['created_at'],0,10);
                $result['list'][$k]['created_at'] = $created_at;
            }
        }
        return $result;
    }

    public function getInvoiceById($id)
    {
        $sql = "select * from `".DB_PREFIX."doc_finance_invoice` where sysno= " . intval($id);

        return $this->dbh->select_row($sql);
    }

    public function addInvoice($data, $detaildata, $marks = '')
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->insert(DB_PREFIX.'doc_finance_invoice', $data);

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $id = $res;

            foreach ($detaildata as $value) {
                $input = array(
                    'invoice_sysno' => $id,
                    'cost_detail_sysno' => $value['sysno'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()'
                );

                $sql = "select * from ".DB_PREFIX."doc_finance_cost_detail where sysno = '$value[sysno]' for update";

                $costData = $this->dbh->select_row($sql);

                if (!is_array($costData) || count($costData) == 0 || $costData['coststatus'] != '2') {
                    $this->dbh->rollback();
                    return false;
                }
                //              $totalbeqty = $totalbeqty+$value['tobeqty'];
                $res = $this->dbh->insert(DB_PREFIX.'doc_finance_invoice_detail', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }

                $costInput = array(
                    'coststatus' => 3,
                    'invoice_sysno' => $id,
                    'invoice_company_sysno' => $data['invoice_company_sysno'],
                    'invoice_companyname' => $data['invoice_companyname'],
                );
 
                $res = $this->dbh->update(DB_PREFIX.'doc_finance_cost_detail', $costInput, 'sysno=' . intval($value['sysno']));

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }

            }

            $user = Yaf_Registry::get(SSN_VAR);
            #库存管理业务操作日志
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 14,
                'opertype' => 0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $marks,
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if ($data['invoicestatus'] == '3') {
                $input['opertype'] = 2;
            } else if ($data['invoicestatus'] == '2') {
                $input['opertype'] = 1;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            // #释放锁
            // $this->dbh->unlock();事务中不需要unlock
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    public function updateInvoice($id, $data, $detaildata, $marks = '')
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_finance_invoice', $data, 'sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $res = $this->dbh->delete(DB_PREFIX.'doc_finance_invoice_detail', 'invoice_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $totalinstockqty = 0;
            foreach ($detaildata as $value) {
                $input = array(
                    'invoice_sysno' => $id,
                    'cost_detail_sysno' => $value['sysno'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()'
                );
                //              $totalbeqty = $totalbeqty+$value['tobeqty'];
                $res = $this->dbh->insert(DB_PREFIX.'doc_finance_invoice_detail', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            $user = Yaf_Registry::get(SSN_VAR);
            #库存管理业务操作日志
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 14,
                'opertype' => 2,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $marks
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            // #释放锁
            // $this->dbh->unlock();事务中不需要unlock
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }


    public function updateInvoiceData($id, $data)
    {
        return $this->dbh->update(DB_PREFIX.'doc_finance_invoice', $data, 'sysno=' . intval($id));
    }

    public function updateInvoiceDetail($id, $data)
    {
        return $this->dbh->update(DB_PREFIX.'doc_finance_invoice_detail', $data, 'cost_detail_sysno=' . intval($id));
    }

    public function examInvoice($id)
    {
        $this->dbh->begin();
        try {
            $sql = 'select * from `'.DB_PREFIX.'doc_finance_invoice` where sysno=' . intval($id) . " for update";
            $data = $this->dbh->select_row($sql);

            if (!$data) {
                $this->dbh->rollback();
                return false;
            }

            if ($data['invoicestatus'] != '3') {
                $this->dbh->rollback();
                return false;
            }

            $sql = 'select cost_detail_sysno from `'.DB_PREFIX.'doc_finance_invoice_detail` where invoice_sysno=' . intval($id) . " and status =1 and isdel = 0";
            $detaildata = $this->dbh->select($sql);

            if (!$detaildata) {
                $this->dbh->rollback();
                return false;
            }

            foreach ($detaildata as $value) {
                $sql = "select * from ".DB_PREFIX."doc_finance_cost_detail where sysno = {$value['cost_detail_sysno']} for update";

                $costData = $this->dbh->select_row($sql);

                if (!is_array($costData) || count($costData) == 0 || $costData['coststatus'] != '3') {
                    $this->dbh->rollback();
                    return false;
                }

                $costInput = array(
                    'coststatus' => 4,
                    'invoice_sysno' => $id,
                );

                $res = $this->dbh->update(DB_PREFIX.'doc_finance_cost_detail', $costInput, 'sysno=' . intval($value['cost_detail_sysno']));
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }

            }

            $param = array(
                'invoicestatus' => 4
            );

            $res = $this->dbh->update(DB_PREFIX.'doc_finance_invoice', $param, 'sysno=' . intval($id));

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

    public function cancelInvoice($id)
    {
        $this->dbh->begin();
        try {
            $sql = 'select * from `'.DB_PREFIX.'doc_finance_invoice` where sysno=' . intval($id) . " for update";
            $data = $this->dbh->select_row($sql);

            if (!$data) {
                $this->dbh->rollback();
                return false;
            }

            $costInput = array(
                'coststatus' => 2,
                'invoice_company_sysno' => '',
                'invoice_companyname' => '',
            );

            $res = $this->dbh->update(DB_PREFIX.'doc_finance_cost_detail', $costInput, 'invoice_sysno=' . intval($id));

            $param = array(
                'costdiscount' => 0,
                'costinvoice' => $data['costtotal'],
                'unreceivablecost' => $data['costtotal'],
                'invoicestatus' => 5
            );
            $res = $this->dbh->update(DB_PREFIX.'doc_finance_invoice', $param, 'sysno=' . intval($id));

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

    /**
     * @param $stockin_sysno
     * @title 根据入库编号查询费用详细
     */
    public function getcostdetailbystockinsysno($stockin_sysno)
    {
        $sql = "select * from ".DB_PREFIX."doc_finance_cost_detail where instock_sysno = " . intval($stockin_sysno);
        return $this->dbh->select($sql);
    }
    //更具开票通知id查询所属开票通知单的所有明细
    public function getcostdetailbyinvoicesysno($invoice_sysno)
    {
        $sql = "select * from ".DB_PREFIX."doc_finance_cost_detail where invoice_sysno = " . intval($invoice_sysno);
        return $this->dbh->select($sql);
    }

    //船名
    public function getsearchshipname(){
        $sql = "select * from ".DB_PREFIX."base_ship";
        return $this->dbh->select($sql);
    }
    //品名
    public function getsearchgoodsname(){
        $sql = "select * from ".DB_PREFIX."base_goods";
        return $this->dbh->select($sql);
    }
}
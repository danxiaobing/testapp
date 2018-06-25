<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/6 0006
 * Time: 14:24
 */
Class WriteoffModel
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
     */
    public function __construct($dbh, $mch = null)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    public function searchWriteofflist($params)
    {
        $filter = array();
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " dfw.`writeoffno` LIKE '%{$params['bar_no']}%' ";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " dfw.`customer_sysno` = '{$params['customer_sysno']}' ";
        }
        if (isset($params['writestatus']) && $params['writestatus'] != '') {
            $filter[] = " dfw.`writestatus` LIKE '%{$params['writestatus']}%'";
        }
        if (isset($params['isdel']) && $params['isdel'] != '') {
            $filter[] = " dfw.`isdel`='{$params['isdel']}'";
        }
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " dfw.`writeoffdate`>='{$params['begin_time']}'";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " dfw.`writeoffdate`<='{$params['end_time']}'";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " c.`sysno`='{$params['customer_sysno']}'";
        }
        $where = ' where dfw.isdel = 0 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT COUNT(*) from ".DB_PREFIX."doc_finance_writeoff as dfw LEFT JOIN ".DB_PREFIX."customer as c on dfw.customer_sysno = c.sysno {$where}";
        $result['totalRow'] = $this->dbh->select_one($sql);
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT dfw.*  FROM `".DB_PREFIX."doc_finance_writeoff` dfw LEFT JOIN ".DB_PREFIX."customer as c on dfw.customer_sysno = c.sysno  {$where} ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by dfw.created_at desc";
                }
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT dfw.*  FROM `".DB_PREFIX."doc_finance_writeoff` dfw LEFT JOIN ".DB_PREFIX."customer as c on dfw.customer_sysno = c.sysno  {$where} ";

                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by dfw.created_at desc";
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    public function getWriteoffInfoByInvoicesysno($invoice_sysno = 0)
    {
        $sql = "select * from `".DB_PREFIX."doc_finance_writeoff_invoice` where invoice_sysno = " . intval($invoice_sysno);
        return $this->dbh->select_one($sql);
    }

    /**
     * @param $data
     * @param $writeoff_invoice_detail
     * @title 新增核销单
     */
    public function addwriteoff($data, $writeoff_invoice_detail)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->insert(DB_PREFIX.'doc_finance_writeoff', $data);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $id = $res;
            foreach ($writeoff_invoice_detail as $item) {
                //---------1--------- 新增-核销单-开票明细
                $input = array(
                    'writeoff_sysno' => $id,
                    'invoice_sysno' => $item['sysno'],
                    'invoice_no' => $item['invoiceno'],
                    'invoicedate' => $item['invoicedate'],
                    'invoicecost' => $item['costinvoice'],
                    'receivablecost' => $item['unreceivablecost'],
                    'writeoffcost' => $item['hxcost'],
                );
                $res = $this->dbh->insert(DB_PREFIX.'doc_finance_writeoff_invoice', $input);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
                //---------1--------- END

                //---------2--------- 回写 开票通知单数据
                #2 1>查询通知单数据
                $invoice = new InvoiceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $invoicelist = $invoice->getInvoiceById($item['sysno']);
                //开票单的未收款金额 = 原有的未收款金额 - 本次核销的金额
                $unreceivablecost = $invoicelist['unreceivablecost'] - $item['hxcost'];
                //开票单的已收款金额 = 开票通知金额 - 开票单的未收款金额
                $receivablecost = $invoicelist['costinvoice'] - $unreceivablecost;
                $update = array(
                    'updated_at' => '=NOW()',
                    'unreceivablecost' => $unreceivablecost,                //未收款金额
                    'receivablecost' => $receivablecost,                   //已收款金额
                );
                if ($unreceivablecost == 0) {
                    $invoice_sysno = $invoicelist['sysno'];                                           //开票单的id
                    $invoice_detail = $invoice->getcostdetailbyinvoicesysno($invoice_sysno);         //开票单的费用明细
                    foreach ($invoice_detail as $data) {
                        $input = array(
                            'updated_at' => '=NOW()',
                            'coststatus' => '5',
                        );
                        //把开票通知单下的费用明细单 改成已关闭
                        $res = $this->dbh->update(DB_PREFIX.'doc_finance_cost_detail', $input, ' sysno = ' . intval($data['sysno']));
                        if (!$res) {
                            $this->dbh->rollback();
                            return false;
                        }
                    }
                    $update['invoicestatus'] = 7;
                }
                $res = $this->dbh->update(DB_PREFIX.'doc_finance_invoice', $update, ' sysno = ' . intval($item['sysno']));
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
                //---------2---------End

                //---------3--------- 回写客户表状态
                $customer = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $customerlist = $customer->getCustomerById($data['customer_sysno']);

                $writeoffcost = $customerlist['writeoffcost'] + $item['hxcost'];             //已核销金额
                $remaincost = $customerlist['remaincost'] - $item['hxcost'];                 //客户余额
                $update = array(
                    'updated_at' => '=NOW()',
                    'writeoffcost' => $writeoffcost,
                    'remaincost' => $remaincost,
                );
                $res = $this->dbh->update(DB_PREFIX.'customer', $update, ' sysno = ' . intval($data['customer_sysno']));
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
                //---------3--------- End
            }

            //---------4--------- 记录Log表
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 17,
                'opertype' => 2,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => '新建成功',
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            //---------4--------- End

            $this->dbh->commit();
            return $id;
        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    // 软删除
    public function delWriteoff($id, $data, $flowmemo, $writeoff_invoice_detail)
    {
        $this->dbh->begin();
        try {
            $writeoff_info = $this->getWriteoffbyId($id);
            if (!$writeoff_info) {
                $this->dbh->rollback();
                return false;
            }
            // 1 更新核销单数据
            $ret = $this->dbh->update(DB_PREFIX.'doc_finance_writeoff', $data, 'sysno=' . intval($id));
            if (!$ret) {
                $this->dbh->rollback();
                return false;
            }
            // 核销单数据更新完毕
            foreach ($writeoff_invoice_detail as $item) {
                //---------2--------- 回写 开票通知单数据
                #2 1>查询通知单数据
                $invoice = new InvoiceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $invoicelist = $invoice->getInvoiceById($item['invoice_sysno']);
                //开票单的未收款金额 = 原有的未收款金额 + 本次核销的金额
                $unreceivablecost = $invoicelist['unreceivablecost'] + $item['writeoffcost'];
                //开票单的已收款金额 = 开票通知金额 - 开票单的未收款金额
                $receivablecost = $invoicelist['receivablecost'] - $item['writeoffcost'];
                $update = array(
                    'updated_at' => '=NOW()',
                    'unreceivablecost' => $unreceivablecost,                //未收款金额
                    'receivablecost' => $receivablecost,                   //已收款金额
                );
                if ($invoicelist['invoicestatus'] == 7) {
                    $update['invoicestatus'] = 4;
                    $invoice_sysno = $invoicelist['sysno'];                                           //开票单的id
                    $invoice_detail = $invoice->getcostdetailbyinvoicesysno($invoice_sysno);         //开票单的费用明细
                    foreach ($invoice_detail as $data) {
                        $input = array(
                            'updated_at' => '=NOW()',
                            'coststatus' => '4',
                        );
                        //把开票通知单下的费用明细单 改成已关闭
                        $res = $this->dbh->update(DB_PREFIX.'doc_finance_cost_detail', $input, ' sysno = ' . intval($data['sysno']));
                        if (!$res) {
                            $this->dbh->rollback();
                            return false;
                        }
                    }
                }
                $res = $this->dbh->update(DB_PREFIX.'doc_finance_invoice', $update, ' sysno = ' . intval($item['invoice_sysno']));
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
                //---------2---------End

                //---------3--------- 回写客户表状态
                $customer = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $customerlist = $customer->getCustomerById($writeoff_info['customer_sysno']);

                $writeoffcost = $customerlist['writeoffcost'] - $item['writeoffcost'];             //已核销金额
                $remaincost = $customerlist['remaincost'] + $item['writeoffcost'];                 //客户余额
                $update = array(
                    'updated_at' => '=NOW()',
                    'writeoffcost' => $writeoffcost,
                    'remaincost' => $remaincost,
                );
                $res = $this->dbh->update(DB_PREFIX.'customer', $update, ' sysno = ' . intval($writeoff_info['customer_sysno']));
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
                //---------3--------- End
            }
            # 记录Log表
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 17,
                'opertype' => 3,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $flowmemo,
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $ret;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }


    }

    // 根据id查询 核销单
    public function getWriteoffbyId($id)
    {
        $sql = "select * from `".DB_PREFIX."doc_finance_writeoff` where sysno = " . intval($id);
        return $this->dbh->select_row($sql);
    }

    /**
     * @param $id
     * @title 查询核销单信息
     */
    public function getWriteoffDetailbyId($id)
    {
        $sql = "select a.*,b.customername from `".DB_PREFIX."doc_finance_writeoff_invoice` a left join `".DB_PREFIX."doc_finance_writeoff` b on b.sysno=a.writeoff_sysno where a.writeoff_sysno = " . intval($id);
        $data = $this->dbh->select($sql);
        return $data;
    }

    /**
     * 20180209
     */
    public function searchInvoice($params){
        $filter = array();
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

}
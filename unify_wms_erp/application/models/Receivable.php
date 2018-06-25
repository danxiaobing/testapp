<?php
/**
 * Created by PhpStorm.
 * User: Alan
 * Date: 2016/12/5 0005
 * Time: 14:10
 */

class ReceivableModel
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

    //获取列表数据
    public function  getFinanceReceivable($params)
    {
        $filter = array();

        if (isset($params['begin_time']) && $params['begin_time'] != 0) {
            $filter[] = " r.receivabledate >= '" . $params['begin_time']."' ";
        }

        if (isset($params['end_time']) && $params['end_time'] != 0) {
            $filter[] = " r.receivabledate <= '" . $params['end_time']."' ";
        }

        if (isset($params['receivablestatus']) && $params['receivablestatus'] != '') {
            $filter[] = " r.receivablestatus = '" . $params['receivablestatus']."' ";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != 0) {
            $filter[] = " r.customer_sysno = '" . $params['customer_sysno']."' ";
        }

        $where = 'r.`status` = 1 AND r.isdel = 0 AND s.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT count(r.sysno) FROM ".DB_PREFIX."doc_finance_receivable r
                LEFT JOIN ".DB_PREFIX."base_settlement s on r.base_settlement_sysno=s.sysno
                WHERE {$where}";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);
        $order = " ORDER BY r.`sysno` DESC";

        $sql ="SELECT r.*,s.settlementname FROM ".DB_PREFIX."doc_finance_receivable r
                LEFT JOIN ".DB_PREFIX."base_settlement s on r.base_settlement_sysno=s.sysno
                WHERE ".$where.$order;

        if (isset($params['page']) && $params['page'] == false) {         //不带分页查询

                    $result['list'] = $this->dbh->select($sql);
                    return $result;
                } else {      //带分页查询
                    $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
                    $this->dbh->set_page_num($params['pageCurrent']);
                    $this->dbh->set_page_rows($params['pageSize']);
                    //
                    $result['list'] = $this->dbh->select_page($sql);
                    return $result;
            }


    }

    // 添加收款单
    public function addReceivble($data)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->insert(DB_PREFIX.'doc_finance_receivable', $data);

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $id = $res;

            // var_dump($params);exit()
            // if($data['receivablestatus']==3){
            //     $sql = "UPDATE LOW_PRIORITY `".DB_PREFIX."customer` SET `receivablecost`=receivablecost+{$data['costreceivable']},`remaincost`=receivablecost-writeoffcost WHERE sysno={$data['customer_sysno']}";
            //     $this->dbh->exe($sql); //更新客户基础资料 
            // }

            $user = Yaf_Registry::get(SSN_VAR);
            #库存管理业务操作日志
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  15,
                'opertype'  => $data['receivablestatus'] -1 ,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  $marks,
            );

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

    //更新收款单
    public function updateReceivble($id, $data,$marks,$costreceivable=0)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_finance_receivable', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            // var_dump($data['receivablestatus']);exit();
            if($data['receivablestatus']==4){
                $sql = "UPDATE LOW_PRIORITY `".DB_PREFIX."customer` SET `receivablecost`=receivablecost+{$data['costreceivable']},`remaincost`=receivablecost-writeoffcost WHERE sysno={$data['customer_sysno']}";
                $this->dbh->exe($sql); //更新客户基础资料 
            }
            // var_dump($data['receivablestatus']);exit();
            if($data['receivablestatus']==6){
                $sql = "UPDATE LOW_PRIORITY `".DB_PREFIX."customer` SET `receivablecost`=receivablecost-{$costreceivable},`remaincost`=receivablecost-writeoffcost WHERE sysno={$data['customer_sysno']}";
                $this->dbh->exe($sql); //更新客户基础资料                 
            }

            $arr = array(
                4 => 3,
                5 => 5,
                6 => 4,
                );
        
            $user = Yaf_Registry::get(SSN_VAR);
            #操作日志
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  15,
                'opertype'  => $arr[$data['receivablestatus']] ,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  $marks
            );
            // echo "<pre>";
            // var_dump($input);exit();
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

    /**
     * 删除收款单
     */

    public function delReceivable($id)
    {
        $params = array();
        $params['isdel'] = 1;
        // $params['status'] = 2;

        return $this->dbh->update(DB_PREFIX.'doc_finance_receivable', $params, 'sysno=' . intval($id));
    }


    /**
     * 获取收款单信息
     */
    public function getReceivableById($id)
    {
        $sql = "SELECT r.*,s.settlementname FROM ".DB_PREFIX."doc_finance_receivable r
        LEFT JOIN ".DB_PREFIX."base_settlement s on r.base_settlement_sysno=s.sysno where r.sysno=".intval($id);
        return $this->dbh->select_row($sql);
    }

    /**
     * 获取结算方式
     * ".DB_PREFIX."base_settlement
     */
    public function getsettlementname($id)
    {
        $sql = "SELECT settlementname FROM ".DB_PREFIX."base_settlement WHERE sysno =".$id;
        return $this->dbh->select_one($sql);
    }

    public function getReceivabledetailByInvoiceId($invoice_sysno = 0)
    {
        $sql = "SELECT * FROM ".DB_PREFIX."doc_finance_receivable_detail where isdel = 0 and invoice_sysno = ".intval($invoice_sysno);
        return $this->dbh->select($sql);
    }

    /**
     * 获取客户资料里的余量
     * @return [array] 
     */
    public function getcustomerCost($id)
    {
        $sql = "SELECT remaincost FROM `".DB_PREFIX."customer` where sysno=".intval($id);

        return $this->dbh->select_one($sql);
    }

    /**
     * 收款单客户联动开票单位json
     */
    public function getCompanyJson($id){
        $sql = "SELECT invoice_company_sysno,invoice_company_sysno,invoice_companyname FROM `".DB_PREFIX."doc_finance_invoice` where customer_sysno=".intval($id);
        $company_sysno = $this->dbh->select($sql);
        $result['list'] = $company_sysno;
        return $result;
    }


}
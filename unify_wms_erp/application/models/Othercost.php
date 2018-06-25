<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17 0017
 * Time: 11:35
 */
Class OthercostModel
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

    /**
     * 查询其他收费列表
     * @author hanshutan
     */
    public function getOthercost($params, $pages = 10, $pagec = 1)
    {
        $filter = array();
        if (isset($params['othercostname']) && $params['othercostname'] != '') {
            $filter[] = " `othercostname` LIKE '%" . $params['othercostname'] . "%' ";
        }
        if (isset($params['status']) && $params['status'] != '') {
            $filter[] = " ".DB_PREFIX."base_othercost.`status` = " . $params['status'] . " ";
        }
        $where = "where ".DB_PREFIX."base_othercost.isdel=0 ";
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }

        $order = "ORDER BY ".DB_PREFIX."base_othercost.`updated_at` DESC";

        $result = array('total' => 0, 'list' => array());
        $sql = "SELECT count(".DB_PREFIX."base_othercost.`sysno`) FROM ".DB_PREFIX."base_othercost
                left join ".DB_PREFIX."base_unit on ".DB_PREFIX."base_othercost.unit_sysno = ".DB_PREFIX."base_unit.sysno
                {$where}";

        $result['totalRow'] = $this->dbh->select_one($sql);
        $result['totalPage'] = ceil($result['totalRow'] / $pages);

        $this->dbh->set_page_num($pagec);
        $this->dbh->set_page_rows($pages);

        $sql = "SELECT ".DB_PREFIX."base_othercost.*,".DB_PREFIX."base_unit.unitname FROM ".DB_PREFIX."base_othercost
                left join ".DB_PREFIX."base_unit on ".DB_PREFIX."base_othercost.unit_sysno = ".DB_PREFIX."base_unit.sysno
                {$where} {$order}";
        $result['list'] = $this->dbh->select_page($sql);
        return $result;
    }

    /*
     *
     */
    public function getlist(){
        $sql = "SELECT bo.sysno,bo.othercostname,bo.othercostprice,bu.unitname
                FROM ".DB_PREFIX."base_othercost bo
                LEFT JOIN ".DB_PREFIX."base_unit bu ON bu.sysno = bo.unit_sysno
                WHERE bo.status = 1 AND bo.isdel = 0 ";
        return $this->dbh->select($sql);
    }

    /**
     * title :根据id查询内容
     */
    public function getOthercostById($id)
    {
        $sql = "SELECT ".DB_PREFIX."base_othercost.*,".DB_PREFIX."base_unit.unitname FROM ".DB_PREFIX."base_othercost
                left join ".DB_PREFIX."base_unit on ".DB_PREFIX."base_othercost.unit_sysno = ".DB_PREFIX."base_unit.sysno
                WHERE ".DB_PREFIX."base_othercost.`sysno`= " . $id;
        $data = $this->dbh->select_row($sql);
        return $data;
    }

    /**
     * title: 新增方法
     */
    public function addothercost($data)
    {
        return $this->dbh->insert(DB_PREFIX.'base_othercost', $data);
    }

    public function updateOthercost($id, $data)
    {
        return $this->dbh->update(DB_PREFIX.'base_othercost', $data, 'sysno=' . intval($id));
    }

    /**
     * 查询合同其他费用
     */
    public function othercostcontractByid($id)
    {
        $sql = "SELECT bo.sysno,dco.othercost_sysno,bo.othercostname,unit.unitname,dco.costamount as othercostprice,
              bo.othercostmarks,dco.memo as othercostmarks,com.companyname,com.bank,com.bank_account
              FROM ".DB_PREFIX."doc_contract_othercost dco
              LEFT JOIN ".DB_PREFIX."base_othercost bo ON dco.othercost_sysno = bo.sysno
              LEFT JOIN ".DB_PREFIX."base_unit unit ON bo.unit_sysno = unit.sysno
              LEFT JOIN ".DB_PREFIX."base_company com on dco.invoice_company_sysno = com.sysno
              where dco.contract_sysno ={$id}";
        return $this->dbh->select($sql);
    }

    /**
    * 批量启用禁用
    */
    public function change($params,$state)
    {
        switch ($state) {
            case 'start':
                foreach ($params as  $v) {
                    $this->dbh->update(DB_PREFIX.'base_othercost', array('status'=>1), 'sysno='. intval($v) );
                }
                return true;
                break;
            case 'stop':
                foreach ($params as  $v) {
                    $this->dbh->update(DB_PREFIX.'base_othercost', array('status'=>2), 'sysno='. intval($v) );
                }
                return true;  
            break;
        }

    }

    public function isdel($id)
    {
      $sql = "SELECT count(co.sysno) num  from ".DB_PREFIX."doc_contract_othercost co 
              LEFT JOIN ".DB_PREFIX."doc_contract c ON c.sysno = co.contract_sysno
              LEFT JOIN ".DB_PREFIX."base_othercost o ON co.othercost_sysno = o.sysno 
              WHERE c.contractstatus !=8 AND c.isdel = 0 AND o.sysno=".$id;

      $result = $this->dbh->select_row($sql);

      if($result['num']>0){
        return true;
      }else{
        return false;
      }
    } 

    public function getOthercostList()
    {
      $sql = "SELECT * FROM ".DB_PREFIX."base_othercost where isdel = 0 and status = 1";
      return $this->dbh->select($sql);
    }

    public function getContractOthercost($params)
    {
        $filter = array();
        
        // if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
        //     $filter[] = " ".DB_PREFIX."base_othercost.`status` = " . $params['customer_sysno'] . " ";
        // }
        if (isset($params['contract_sysno']) && $params['contract_sysno'] != '') {
            $filter[] = " ".DB_PREFIX."doc_contract_othercost.`contract_sysno` = " . $params['contract_sysno'] . " ";
        }
        if (isset($params['status']) && $params['status'] != '') {
            $filter[] = " ".DB_PREFIX."base_othercost.`status` = " . $params['status'] . " ";
        }
        $where = 'where '.DB_PREFIX.'base_othercost.isdel=0 and '.DB_PREFIX.'base_othercost.status = 1 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }

        $order = "ORDER BY ".DB_PREFIX."base_othercost.`updated_at` DESC";

        $result = array('total' => 0, 'list' => array());
        $sql = "SELECT count(".DB_PREFIX."base_othercost.`sysno`) FROM ".DB_PREFIX."doc_contract_othercost 
                left join ".DB_PREFIX."base_othercost on (".DB_PREFIX."doc_contract_othercost.othercost_sysno=".DB_PREFIX."base_othercost.sysno)
                left join ".DB_PREFIX."base_unit on ".DB_PREFIX."base_othercost.unit_sysno = ".DB_PREFIX."base_unit.sysno
                {$where}";

        $result['totalRow'] = $this->dbh->select_one($sql);


        $sql = "SELECT ".DB_PREFIX."base_othercost.*,".DB_PREFIX."base_unit.unitname,".DB_PREFIX."base_othercost.sysno as costtype,".DB_PREFIX."doc_contract_othercost.costamount FROM ".DB_PREFIX."doc_contract_othercost 
                left join ".DB_PREFIX."base_othercost on (".DB_PREFIX."doc_contract_othercost.othercost_sysno=".DB_PREFIX."base_othercost.sysno)
                left join ".DB_PREFIX."base_unit on ".DB_PREFIX."base_othercost.unit_sysno = ".DB_PREFIX."base_unit.sysno
                {$where} {$order}";
        $result['list'] = $this->dbh->select($sql);
        if(count($result['list'])<=0)
        {
          $where = 'where '.DB_PREFIX.'base_othercost.isdel=0 ';

          $result = array('total' => 0, 'list' => array());
          $sql = "SELECT count(".DB_PREFIX."base_othercost.`sysno`) FROM ".DB_PREFIX."base_othercost
                  left join ".DB_PREFIX."base_unit on ".DB_PREFIX."base_othercost.unit_sysno = ".DB_PREFIX."base_unit.sysno
                  {$where}";

          $result['totalRow'] = $this->dbh->select_one($sql);
          $result['totalPage'] = ceil($result['totalRow'] / 20);

          // $this->dbh->set_page_num(1);
          // $this->dbh->set_page_rows(20);

          $sql = "SELECT ".DB_PREFIX."base_othercost.*,".DB_PREFIX."base_unit.unitname,".DB_PREFIX."base_othercost.sysno as costtype,".DB_PREFIX."base_othercost.othercostprice as costamount FROM ".DB_PREFIX."base_othercost
                  left join ".DB_PREFIX."base_unit on ".DB_PREFIX."base_othercost.unit_sysno = ".DB_PREFIX."base_unit.sysno
                  {$where} {$order}";
          $result['list'] = $this->dbh->select($sql);
        }
        return $result;
    }
}
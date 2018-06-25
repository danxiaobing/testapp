<?php

/**
 * Customer Model
 *
 */
class CustomerModel
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

    public function searchCustomer($params)
    {
        $filter = array();
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " c.`customerno` LIKE '%{$params['bar_no']}%' ";
        }
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " c.`customername` LIKE '%{$params['bar_name']}%' ";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
            $filter[] = " c.`status`='{$params['bar_status']}'";
        }
        if (isset($params['bar_isdel']) && $params['bar_isdel'] != '-100') {
            $filter[] = " c.`isdel`='{$params['bar_isdel']}'";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
            $filter[] = " c.`status`='{$params['bar_status']}'";
        }
        if (isset($params['customername']) && $params['customername'] != '-100') {
            $filter[] = " c.`customername`='{$params['customername']}'";
        }

        $where = 'c.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."customer` c
              left join `".DB_PREFIX."customer_category` cc on (c.`customercategory_sysno`=cc.`sysno`)
              left join `".DB_PREFIX."system_employee` se on (c.business_user_sysno = se.sysno)
              where {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT c.*,cc.`categoryname`,se.employeename FROM `".DB_PREFIX."customer` c
                    left join `".DB_PREFIX."customer_category` cc on (c.`customercategory_sysno`=cc.`sysno`)
                    left join `".DB_PREFIX."system_employee` se on (c.business_user_sysno = se.sysno)
                    where {$where} ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by created_at desc";
                }
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT c.*,cc.`categoryname`,se.employeename FROM `".DB_PREFIX."customer` c
                    left join `".DB_PREFIX."customer_category` cc on (c.`customercategory_sysno`=cc.`sysno`)
                    left join `".DB_PREFIX."system_employee` se on (c.business_user_sysno = se.sysno)
                        where {$where} ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by created_at desc";
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    /**
     * 获取有效合同客户列表
     * @update wuxianneng
     */
    public function searchCustomerContract($params)
    {
        $filter = array();
        if (isset($params['contractstatus']) && $params['contractstatus'] != '') {
            $filter[] = " dc.`contractstatus`={$params['contractstatus']}";
        }
        // if (isset($params['contractenddate']) && $params['contractenddate'] != '') {
        //     $filter[] = " dc.`contractenddate`>={$params['contractenddate']}";
        // }
        if (isset($params['bar_status']) && $params['bar_status'] != '') {
            $filter[] = " dc.`status` = {$params['bar_status']}";
        }
        $where = 'WHERE cu.status = 1 AND cu.isdel = 0 AND dc.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT DISTINCT cu.sysno,cu.customername
		      FROM ".DB_PREFIX."doc_contract dc 
		      LEFT JOIN ".DB_PREFIX."customer cu ON cu.sysno = dc.customer_id " . $where;
        return $this->dbh->select($sql);
    }

    public function getCustomerById($id)
    {
        $sql = "SELECT c.*,cc.`categoryname`,u.`employeename` as businessrealname,uu.`employeename` as createrealname 
                FROM `".DB_PREFIX."customer` c 
                left join `".DB_PREFIX."customer_category` cc on (c.`customercategory_sysno`=cc.`sysno`) 
                left join `".DB_PREFIX."system_employee` u on (c.`business_user_sysno`=u.`sysno`) 
                left join  `".DB_PREFIX."system_employee` uu on (c.`created_user_sysno`=uu.`sysno`) 
                WHERE c.`sysno`=" . intval($id);
        return $this->dbh->select_row($sql);
    }

    public function addCustomer($data, $contactsdata, $goodsdata, $companydata)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->insert(DB_PREFIX.'customer', $data);

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $id = $res;

            $res = $this->dbh->delete(DB_PREFIX.'customer_contacts', 'customer_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            foreach ($contactsdata as $value) {
                $input = array(
                    'customer_sysno' => $id,
                    'contactsname' => $value['contactsname'],
                    'contactsposition' => $value['contactsposition'],
                    'contactsmobilephone' => $value['contactsmobilephone'],
                    'contactsemail' => $value['contactsemail'],
                    'contactstelephone' => $value['contactstelephone'],
                    'contactsmarks' => $value['contactsmarks'],
                    'status' => $value['status'],
                    'isdel' => $value['isdel'],
                    'version' => $value['version'],
                    'created_at' =>  '=NOW()',
                    'updated_at' =>  '=NOW()',
                );
                $res = $this->dbh->insert(DB_PREFIX.'customer_contacts', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            $res = $this->dbh->delete(DB_PREFIX.'customer-r-goods', 'customer_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if ($goodsdata !== "") {
                $goodsdataArr = explode(",", $goodsdata);

                if (!empty($goodsdataArr)) {
                    foreach ($goodsdataArr as $value) {
                        $input = array(
                            'customer_sysno' => $id,
                            'goods_sysno' => $value,
                        );
                        $res = $this->dbh->insert(DB_PREFIX.'customer-r-goods', $input);

                        if (!$res) {
                            $this->dbh->rollback();
                            return false;
                        }
                    }

                }
            }

            $res = $this->dbh->delete(DB_PREFIX.'customer_company', 'customer_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if(!empty($companydata))
            foreach ($companydata as $value) {
                $input = array(
                    'customer_sysno' => $id,
                    'companyname' => $value['companyname'],
                    'memo' => $value['memo'],
                    'status' => $value['status'],
                    'isdel' => $value['isdel'],
                    'version' => $value['version'],
                    'created_at' =>  '=NOW()',
                    'updated_at' =>  '=NOW()',
                );
                $res = $this->dbh->insert(DB_PREFIX.'customer_company', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            ##新增插入客户自动添加开票公司信息
            $input = array(
                'companyno' => COMMON::getCodeId('C'),
                'companyname' => $data['customername'],
                'companysname' => $data['customerabbreviation'],
                'warehouseaddress' => $data['customeraddress'],
                'contacttel' => $data['customertelephone'],
                'contactfax' => $data['customerfax'],
                'postcode' => '',
                'legalperson' => $data['customerrepresentative'],
                'bank' => $data['customerbank'],
                'bank_account' => $data['customeraccount'],
                'isdefault' => 0,
                'status' => 1,
                'created_at' => '=NOW()',
                'updated_at' => '=NOW()'
            );
            $this->dbh->insert(DB_PREFIX.'base_company', $input);

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

    public function updateCustomer($id, $data, $contactsdata, $goodsdata, $companydata)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'customer', $data, 'sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $res = $this->dbh->delete(DB_PREFIX.'customer_contacts', 'customer_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            foreach ($contactsdata as $value) {
                $input = array(
                    'customer_sysno' => $id,
                    'contactsname' => $value['contactsname'],
                    'contactsposition' => $value['contactsposition'],
                    'contactsmobilephone' => $value['contactsmobilephone'],
                    'contactsemail' => $value['contactsemail'],
                    'contactstelephone' => $value['contactstelephone'],
                    'contactsmarks' => $value['contactsmarks'],
                    'status' => $value['status'],
                    'isdel' => $value['isdel'],
                    'version' => $value['version'],
                    'updated_at' =>  '=NOW()',
                );
                $res = $this->dbh->insert(DB_PREFIX.'customer_contacts', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            $res = $this->dbh->delete(DB_PREFIX.'customer-r-goods', 'customer_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if ($goodsdata !== "") {
                $goodsdataArr = explode(",", $goodsdata);

                if (!empty($goodsdataArr)) {
                    foreach ($goodsdataArr as $value) {
                        $input = array(
                            'customer_sysno' => $id,
                            'goods_sysno' => $value,
                        );
                        $res = $this->dbh->insert(DB_PREFIX.'customer-r-goods', $input);

                        if (!$res) {
                            $this->dbh->rollback();
                            return false;
                        }
                    }

                }
            }

            $res = $this->dbh->delete(DB_PREFIX.'customer_company', 'customer_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if(!empty($companydata))
            foreach ($companydata as $value) {
                $input = array(
                    'customer_sysno' => $id,
                    'companyname' => $value['companyname'],
                    'memo' => $value['memo'],
                    'status' => $value['status'],
                    'isdel' => $value['isdel'],
                    'version' => $value['version'],
                    'updated_at' =>  '=NOW()',
                );
                $res = $this->dbh->insert(DB_PREFIX.'customer_company', $input);

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

    public function delCustomer($id, $data, &$msg = null)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."customer` WHERE `sysno`=" . intval($id);
        $info = $this->dbh->select_row($sql);

        if ($info['customerdeal'] == 1) {
            $msg = '成交客户无法删除';
            return false;
        }
        $data['customername'] = $info['customername'].time();

        $sql = "SELECT count(*) FROM `".DB_PREFIX."doc_contract` WHERE status=1 and isdel=0 and `customer_id`=" . intval($id);
        $count = $this->dbh->select_one($sql);

        if ($count>0) {
            $msg = "客户已有合同无法删除";
            return false;
        }

        $msg = '删除失败';
        return $this->dbh->update(DB_PREFIX.'customer', $data, 'sysno=' . intval($id));
    }

    public function searchCustomercategory($params)
    {
        $filter = array();
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " c.`categoryname` LIKE '%{$params['bar_name']}%' ";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
            $filter[] = " c.`status`='{$params['bar_status']}'";
        }
        if (isset($params['bar_isdel']) && $params['bar_isdel'] != '-100') {
            $filter[] = " c.`isdel`='{$params['bar_isdel']}'";
        }

        $where = 'isdel = 0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  from ".DB_PREFIX."customer_category c where  {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {


            if (isset($params['page']) && $params['page'] == false) {
                $sql = "select c.* from ".DB_PREFIX."customer_category c where {$where} ";
                if ($params['orders'] != '')
                    $sql .= " order by " . $params['orders'];

                $arr = $this->dbh->select($sql);


                $result['list'] = $arr;
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "select c.* from ".DB_PREFIX."customer_category c where {$where} ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                }

                $arr = $this->dbh->select_page($sql);

                $result['list'] = $arr;
            }
        }

        return $result;
    }

    public function getCustomercategoryById($id)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."customer_category` WHERE `sysno`=" . intval($id);
        return $this->dbh->select_row($sql);
    }

    public function addCustomercategory($data)
    {
        return $this->dbh->insert(DB_PREFIX.'customer_category', $data);
    }

    public function updateCustomercategory($id, $data)
    {
        return $this->dbh->update(DB_PREFIX.'customer_category', $data, 'sysno=' . intval($id));
    }

    public function delCustomercategory($id, $data, &$msg = null)
    {
        $sql = "SELECT count(*) FROM `".DB_PREFIX."customer` WHERE status=1 and isdel = 0 and  `customercategory_sysno`=" . intval($id);
        $info = $this->dbh->select_one($sql);

        if ($info>0) {
            $msg = '客户分类被引用无法删除';
            return false;
        }

        $msg = '删除失败';
        return $this->dbh->update(DB_PREFIX.'customer_category', $data, 'sysno=' . intval($id));
    }

    public function searchCustomercontacts($params)
    {
        $filter = array();
        if (isset($params['bar_id']) && $params['bar_id'] != '') {
            $filter[] = " cu.`sysno`='{$params['bar_id']}' ";
        }
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " cu.`customerno`='{$params['bar_no']}' ";
        }
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " cu.`customername` LIKE '%{$params['bar_name']}%' ";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
            $filter[] = " cu.`status`='{$params['bar_status']}'";
        }
        if (isset($params['bar_isdel']) && $params['bar_isdel'] != '-100') {
            $filter[] = " cu.`isdel`='{$params['bar_isdel']}'";
        }

        $where = 'cu.isdel=0 and cc.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."customer_contacts` cc left join `".DB_PREFIX."customer` cu on (cc.`customer_sysno`=cu.`sysno`) where {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT cc.*,cu.`customername`,cu.customerfax,se.employeename,cu.customerrepresentative,cu.customerbank,cu.customeraccount,cu.customercreditcodechecked,if(cu.customercreditcodechecked=1,cu.customercreditcode,cu.customerorganizationcode) as code,cu.customeraddress,cu.customertelephone,cu.customertaxid FROM `".DB_PREFIX."customer_contacts` cc left join `".DB_PREFIX."customer` cu on (cc.`customer_sysno`=cu.`sysno`) left join `".DB_PREFIX."system_employee` se on (cu.business_user_sysno = se.sysno) where {$where} ";
                if ($params['orders'] != '')
                    $sql .= " order by " . $params['orders'];
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT cc.*,cu.`customername`,cu.customerfax,se.employeename,cu.customerrepresentative,cu.customerbank,cu.customeraccount,cu.customercreditcodechecked,if(cu.customercreditcodechecked=1,cu.customercreditcode,cu.customerorganizationcode) as code,cu.customeraddress,cu.customertelephone,cu.customertaxid FROM `".DB_PREFIX."customer_contacts` cc left join `".DB_PREFIX."customer` cu on (cc.`customer_sysno`=cu.`sysno`) left join `".DB_PREFIX."system_employee` se on (cu.business_user_sysno = se.sysno) where {$where} ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    public function getCustomercontactsById($id)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."customer_contacts` WHERE `sysno`=" . intval($id);
        return $this->dbh->select_row($sql);
    }

    public function addCustomercontacts($data)
    {
        return $this->dbh->insert(DB_PREFIX.'customer_contacts', $data);
    }

    public function updateCustomercontacts($id, $data)
    {
        return $this->dbh->update(DB_PREFIX.'customer_contacts', $data, 'sysno=' . intval($id));
    }

    public function searchCustomercompany($params)
    {
        $filter = array();
        if (isset($params['bar_id']) && $params['bar_id'] != '') {
            $filter[] = " cu.`sysno`='{$params['bar_id']}' ";
        }
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " cu.`customerno`='{$params['bar_no']}' ";
        }
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " cu.`customername` LIKE '%{$params['bar_name']}%' ";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
            $filter[] = " cu.`status`='{$params['bar_status']}'";
        }
        if (isset($params['bar_isdel']) && $params['bar_isdel'] != '-100') {
            $filter[] = " cu.`isdel`='{$params['bar_isdel']}'";
        }

        $where = 'cu.isdel=0 and cc.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."customer_company` cc left join `".DB_PREFIX."customer` cu on (cc.`customer_sysno`=cu.`sysno`) where {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT cc.*,cu.`customername`,cu.customerfax,se.employeename,cu.customerrepresentative,cu.customerbank,cu.customeraccount,cu.customercreditcodechecked,if(cu.customercreditcodechecked=1,cu.customercreditcode,cu.customerorganizationcode) as code,cu.customeraddress,cu.customertelephone,cu.customertaxid FROM `".DB_PREFIX."customer_company` cc left join `".DB_PREFIX."customer` cu on (cc.`customer_sysno`=cu.`sysno`) left join `".DB_PREFIX."system_employee` se on (cu.business_user_sysno = se.sysno) where {$where} ";
                if ($params['orders'] != '')
                    $sql .= " order by " . $params['orders'];
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT cc.*,cu.`customername`,cu.customerfax,se.employeename,cu.customerrepresentative,cu.customerbank,cu.customeraccount,cu.customercreditcodechecked,if(cu.customercreditcodechecked=1,cu.customercreditcode,cu.customerorganizationcode) as code,cu.customeraddress,cu.customertelephone,cu.customertaxid FROM `".DB_PREFIX."customer_company` cc left join `".DB_PREFIX."customer` cu on (cc.`customer_sysno`=cu.`sysno`) left join `".DB_PREFIX."system_employee` se on (cu.business_user_sysno = se.sysno) where {$where} ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    public function getCustomercompanyById($id)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."customer_contacts` WHERE `sysno`=" . intval($id);
        return $this->dbh->select_row($sql);
    }

    public function addCustomercompany($data)
    {
        return $this->dbh->insert(DB_PREFIX.'customer_company', $data);
    }

    public function updateCustomercompany($id, $data)
    {
        return $this->dbh->update(DB_PREFIX.'customer_company', $data, 'sysno=' . intval($id));
    }

    public function searchCustomergoods($params)
    {
        $filter = array();
        $filter[] = " cu.`sysno`='{$params['bar_id']}' ";
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " cu.`customerno`='{$params['bar_no']}' ";
        }
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " cu.`customername` LIKE '%{$params['bar_name']}%' ";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
            $filter[] = " cu.`status`='{$params['bar_status']}'";
        }
        if (isset($params['bar_isdel']) && $params['bar_isdel'] != '-100') {
            $filter[] = " cu.`isdel`='{$params['bar_isdel']}'";
        }

        $where = 'cu.isdel=0 and bg.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."customer-r-goods` cg left join `".DB_PREFIX."base_goods` bg on (cg.`goods_sysno`=bg.`sysno`) left join `".DB_PREFIX."customer` cu on (cg.`customer_sysno`=cu.`sysno`) where {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT cg.*,bg.*,cu.`customername` FROM `".DB_PREFIX."customer-r-goods` cg 
                        left join `".DB_PREFIX."base_goods` bg on (cg.`goods_sysno`=bg.`sysno`) 
                        left join `".DB_PREFIX."customer` cu on (cg.`customer_sysno`=cu.`sysno`) where {$where} ";
                if ($params['orders'] != '')
                    $sql .= " order by " . $params['orders'];
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT cg.*,bg.*,cu.`customername` FROM `".DB_PREFIX."customer-r-goods` cg 
                        left join `".DB_PREFIX."base_goods` bg on (cg.`goods_sysno`=bg.`sysno`) 
                        left join `".DB_PREFIX."customer` cu on (cg.`customer_sysno`=cu.`sysno`) where {$where} ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    public function getCustomergoodsByWhere($params)
    {
        if (isset($params['inwhere']) && $params['inwhere'] != '') {
            $filter[] = " `sysno` in {$params['inwhere']}";
        }
        $where = 'isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT * FROM `".DB_PREFIX."base_goods` WHERE {$where} ";
        return $this->dbh->select($sql);
    }

    public function getCustomergoodsBySelected($id, $search = array())
    {

        $G = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params = $G->getBaseGoods($search);

        $dataArr = array();
        if ($id) {
            $search = array(
                'bar_id' => $id,
                'page' => false,
            );
            $data = $this->searchCustomergoods($search);
            $dataArr = $data['list'];
        }

        foreach ($params['list'] as $row) {
            if ($row['parent_sysno'] == 0)
                $row['parent_sysno'] = null;
            if (!empty($dataArr)) {
                $row['checked'] = "";
                foreach ($dataArr as $datainfo) {
                    if ($row['sysno'] == $datainfo['sysno']) {
                        $row['checked'] = "checked";
                        break;
                    }
                }
            }
            $list[] = $row;
        }
        return $list;
    }

    public function getCustomergoodsById($id)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."base_goods` WHERE `sysno`=" . intval($id);
        return $this->dbh->select_row($sql);
    }

    public function addCustomergoods($data)
    {
        return $this->dbh->insert(DB_PREFIX.'base_goods', $data);
    }

    public function updateCustomergoods($id, $data)
    {
        return $this->dbh->update(DB_PREFIX.'base_goods', $data, 'sysno=' . intval($id));
    }

    public function addCustomerAttach($id, $attach)
    {
        if (is_array($attach) && count($attach) > 0) {
            $this->dbh->begin();
            try {
                $this->dbh->delete(DB_PREFIX.'customer-r-attach', 'ship_sysno=' . intval($id));
                foreach ($attach as $aid) {
                    $input = array(
                        'customer_sysno' => $id,
                        'attach_sysno' => $aid
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'customer-r-attach', $input);
                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
                $this->dbh->commit();
                return true;

            } catch (Exception $e) {
                $this->dbh->rollback();
                return false;
            }

        } else {
            return false;
        }
    }

    public function getCustomerAttachById($id = 0)
    {
        $sql = "select a.* from `".DB_PREFIX."customer-r-attach` sa, ".DB_PREFIX."system_attach a  where sa.customer_sysno = $id and sa.attach_sysno = a.sysno ";
        return $this->dbh->select($sql);
    }

    /*
     * 用于预约管理中查询客户已审核的有效合同
     * update wu xianneng
     */
    public function searchCustomercontractlist($params)
    {
        $filter = array();
        $filter[] = " cu.`sysno`='{$params['bar_id']}' ";
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " cu.`customerno`='{$params['bar_no']}' ";
        }
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " cu.`customername` LIKE '%{$params['bar_name']}%' ";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
            $filter[] = " cu.`status`='{$params['bar_status']}'";
        }
        if (isset($params['bar_isdel']) && $params['bar_isdel'] != '-100') {
            $filter[] = " cu.`isdel`='{$params['bar_isdel']}'";
        }
        if (isset($params['bar_contractstatus']) && $params['bar_contractstatus'] != '') {
            $filter[] = " c.`contractstatus` ={$params['bar_contractstatus']}";
        }
        if (isset($params['contracttype']) && $params['contracttype'] != '') {
            $filter[] = " c.`contracttype` in ({$params['contracttype']})";
        }
        if (isset($params['berthcosttype']) && $params['berthcosttype'] != '') {
            $filter[] = " cg.`berthcosttype` in ({$params['berthcosttype']})";
        }

        $where = "c.isdel=0 AND c.contractenddate >= '{$params['time']}' ";
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."customer` cu
                left join `".DB_PREFIX."doc_contract` c on (cu.`sysno`=c.`customer_id`)
                left join `".DB_PREFIX."doc_contract_goods` cg on (c.`sysno`=cg.`contract_sysno`)
                where {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT DISTINCT c.* FROM `".DB_PREFIX."customer` cu
                        left join `".DB_PREFIX."doc_contract` c on (cu.`sysno`=c.`customer_id`)
                        left join `".DB_PREFIX."doc_contract_goods` cg on (c.`sysno`=cg.`contract_sysno`)
                        where {$where} ";
                if ($params['orders'] != '')
                    $sql .= " order by " . $params['orders'];
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT DISTINCT c.* FROM `".DB_PREFIX."customer` cu
                        left join `".DB_PREFIX."doc_contract` c on (cu.`sysno`=c.`customer_id`)
                        left join `".DB_PREFIX."doc_contract_goods` cg on (c.`sysno`=cg.`contract_sysno`)
                        where {$where} ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    public function updateStatus($date){
        if($date){
            $ids = $date['ids'];
            $status = array(
                'status'=> $date['status'],
                'updated_at' => '=NOW()',
            );
            $res = $this->dbh->update(DB_PREFIX.'customer',$status,'sysno in ('.$ids.')');
        }
        return $res;
    }

    public function categoryupdateStatus($date){
        if($date){
            $ids = $date['ids'];
            $status = array(
                'status'=> $date['status'],
                'updated_at' => '=NOW()',
            );
            $res = $this->dbh->update(DB_PREFIX.'customer_category',$status,'sysno in ('.$ids.')');
        }
        return $res;
    }

    public function searchCustomerForApi($params)
    {
        $filter = array();
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " c.`customerno` LIKE '%{$params['bar_no']}%' ";
        }
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " c.`customername` LIKE '%{$params['bar_name']}%' ";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
            $filter[] = " c.`status`='{$params['bar_status']}'";
        }
        if (isset($params['bar_isdel']) && $params['bar_isdel'] != '-100') {
            $filter[] = " c.`isdel`='{$params['bar_isdel']}'";
        }


        $where = 'c.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."customer` c
              left join `".DB_PREFIX."customer_category` cc on (c.`customercategory_sysno`=cc.`sysno`)
              left join `".DB_PREFIX."system_employee` se on (c.business_user_sysno = se.sysno)
              where {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT c.sysno,c.customerno,c.customername  FROM `".DB_PREFIX."customer` c
                    left join `".DB_PREFIX."customer_category` cc on (c.`customercategory_sysno`=cc.`sysno`)
                    left join `".DB_PREFIX."system_employee` se on (c.business_user_sysno = se.sysno)
                    where {$where} ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by c.created_at desc";
                }
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT c.*,cc.`categoryname`,se.employeename FROM `".DB_PREFIX."customer` c
                    left join `".DB_PREFIX."customer_category` cc on (c.`customercategory_sysno`=cc.`sysno`)
                    left join `".DB_PREFIX."system_employee` se on (c.business_user_sysno = se.sysno)
                        where {$where} ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by created_at desc";
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }
}

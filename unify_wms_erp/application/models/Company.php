<?php

/**
 * Created by PhpStorm.
 * User: Jay Xu
 * Date: 2016/11/19 0015
 * Time: 14:50
 */
class CompanyModel
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
     * 根据条件检索公司
     * @return 数组
     * @author Jay Xu
     */
    public function searchCompany($params)
    {
        $filter = array();
        if (isset($params['companyname']) && $params['companyname'] != '') {
            $filter[] = " `companyname` LIKE '%{$params['companyname']}%' ";
        }

        if (isset($params['companyno']) && $params['companyno'] != '') {
            $filter[] = " `companyno` LIKE '%{$params['companyno']}%' ";
        }

        if (isset($params['companysname']) && $params['companysname'] != '') {
            $filter[] = " `companysname` LIKE '%{$params['companysname']}%' ";
        }

        if (isset($params['contacttel']) && $params['contacttel'] != '') {
            $filter[] = " `contacttel` LIKE '%{$params['contacttel']}%' ";
        }

        if (isset($params['bar_status']) && $params['bar_status'] != '') {
            $filter[] = " `status`='{$params['bar_status']}'";
        }
        if (isset($params['bar_isdel']) && $params['bar_isdel'] != '-100') {
            $filter[] = " `isdel`='{$params['bar_isdel']}'";
        }
        $where = 'where isdel =0 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."base_company` {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();

        if ($result['totalRow'])
        {
            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "SELECT * FROM `".DB_PREFIX."base_company` {$where} ";
                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;

                $arr = 	$this->dbh->select($sql);


                $result['list'] = $arr;
            }else{
                $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent'] );
                $this->dbh->set_page_rows($params['pageSize'] );


                $sql = "SELECT * FROM `".DB_PREFIX."base_company` {$where} {$order} ";
                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;

                $arr = 	$this->dbh->select_page($sql);


                $result['list'] = $arr;
            }

        }
        return $result;
    }

    /**
     * 根据id获得公司细节
     * id: 权限id
     * @return 数组
     */
    public function getCompanyById($id = 0)
    {
        $sql = "select p.* from ".DB_PREFIX."base_company p where sysno = $id ";

        return $this->dbh->select_row($sql);
    }

    /**
     * 新增公司数据添加
     * @return boolean
     * @author Jay Xu
     */
    public function addCompany($data)
    {
        if($data['isdefault']==1){
            $company=array('isdefault'=>'0');
            $this->dbh->update(DB_PREFIX.'base_company', $company,'1=1');
        }
        return $this->dbh->insert(DB_PREFIX.'base_company', $data);
    }


    /**
     * 开票公司更新
     * @param array $data
     * @param string $privileges
     * @return bool
     */

    public function updateCompany($id = 0, $data = array())
    {
        if($data['isdefault']==1){
            $company=array('isdefault'=>'0');
            $this->dbh->update(DB_PREFIX.'base_company', $company, 'sysno!=' . intval($id));
        }
        return  $this->dbh->update(DB_PREFIX.'base_company', $data, 'sysno in (' .$id.')');

    }

    /**
     * 获取默认的开票公司
     * @author Alan
     */

    public function getDefault()
    {
        $sql = "SELECT sysno,companyname FROM ".DB_PREFIX."base_company WHERE `status` = 1 AND isdel = 0 AND  `isdefault` = 1 ";
        return $this->dbh->select_row($sql);
    }
    /*
     * 更新状态
     * */
    public function updateStatus($date){
        if($date){
            $ids = $date['ids'];
            $status = ['status'=>$date['status']];
            $res = $this->dbh->update(DB_PREFIX.'base_company',$status,'sysno in ('.$ids.')');
        }
        return $res;
    }
/*
 *
 * 判断开票公司是否被占用
 * */
   public function isOccupied($companyId){
        if($companyId) {
            $sql = "select count('sysno') from  `".DB_PREFIX."doc_finance_invoice`  where  base_company_sysno in (". $companyId ." )";
            $res = $this->dbh->select_one($sql);
         }else{
            $res = 0;
        }
       return $res;
   }


}

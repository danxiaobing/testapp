<?php
/**
 * 汇签部门配置.
 * User: ty
 * Date: 2016/11/19 0019
 * Time: 12:22
 */
class SinkModel
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
     * 汇签部门数据列表
     * @author hanshutan
     */
    public function getList($search,$pages=10,$pagec=1)
    {
        $filter = array();
        if (isset($search['departmentname']) && $search['departmentname'] != '') {
            $filter[] = " `departmentname` LIKE '%".$search['departmentname']."%' ";
        }
        if (isset($search['status']) && $search['status'] != '') {
            $filter[] = " `status` = ".$search['status']." ";
        }
        $where ='where isdel = 0';
        if (1 <= count($filter)) {
            $where .= " and ". implode(' AND ', $filter);
        }

        $orderby = "ORDER BY `updated_at` DESC";

        $result=array('total'=>0,'list'=>array());
        $sql = "SELECT count(`sysno`) FROM `".DB_PREFIX."system_config_review` {$where}";
        $result['totalRow']=$this->dbh->select_one($sql);
        $result['totalPage'] = ceil($result['totalRow'] / $pages);

        $this->dbh->set_page_num($pagec);
        $this->dbh->set_page_rows($pages);

        $sql = "SELECT * FROM `".DB_PREFIX."system_config_review` {$where} {$orderby}";
        $result['list'] = $this->dbh->select_page($sql);
        return $result;

    }

    /**
     * 获取 GOODS 父类树状图
     */
    public function getDepartmentsname()
    {
        $sql = "SELECT sysno,parent_sysno,departmentname FROM ".DB_PREFIX."system_department WHERE isdel = 0";
        return $this->dbh->select($sql);
    }

    /**
     * Title: 根据编号查询内容
     */
    public function getSinkById($id){
        $sql = " SELECT * FROM ".DB_PREFIX."system_config_review WHERE sysno = ".$id;
        $data = $this->dbh->select_row($sql);
        return $data;
    }

    /**
     * Title:新增
     */
    public function add($data)
    {
        return $this->dbh->insert(DB_PREFIX.'system_config_review', $data);
    }

    /**
     * Title: 编辑
     */
    public function update($id, $data)
    {
        return $this->dbh->update(DB_PREFIX.'system_config_review', $data, 'sysno=' . intval($id));
    }

    /**
    * @author  hr
    */
    public function getSinkInfo($search)
    {
        if (isset($search['status']) && $search['status'] != '') {
            $filter[] = " `status` = ".$search['status']." ";
        }

        if (isset($search['vid']) && $search['vid'] != '') {
            $filter[] = " `version_sysno` = ".$search['vid']." ";
        }

        $where ='where isdel = 0';
        if (1 <= count($filter)) {
            $where .= " and ". implode(' AND ', $filter);
        }

        $orderby = "ORDER BY `updated_at` DESC";


        $sql = "SELECT * FROM `".DB_PREFIX."system_config_review` {$where} {$orderby}";

        $result = $this->dbh->select($sql);
        return $result;
    }

    public function del($id)
    {
        return $this->dbh->delete(DB_PREFIX.'system_config_review','version_sysno='.$id);
    }
}
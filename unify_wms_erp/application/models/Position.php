<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/15 0015
 * Time: 10:40
 */
class PositionModel
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
     * 查询岗位列表
     * @author hanshutan
     */
    public function getPosition($params,$pages=10,$pagec=1)
    {
        $filter = array();
        if (isset($params['positionno']) && $params['positionno'] != '') {
            $filter[] = " `positionno` LIKE '%".$params['positionno']."%' ";
        }
        if (isset($params['positionname']) && $params['positionname'] != '') {
            $filter[] = " `positionname` LIKE '%".$params['positionname']."%' ";
        }
        if (isset($params['department_sysno']) && $params['department_sysno'] != '') {
            $filter[] = " `department_sysno` = ".$params['department_sysno']." ";
        }
        if (isset($params['status']) && $params['status'] != '') {
            $filter[] = " position.`status` = ".$params['status']." ";
        }
        $where ='where position.isdel=0 ';
        if (1 <= count($filter)) {
            $where .= " and ". implode(' AND ', $filter);
        }

        $order = "ORDER BY position.`updated_at` DESC";

        $result=array('total'=>0,'list'=>array());
        $sql = "SELECT count(position.`sysno`) FROM ".DB_PREFIX."system_position as position
                left join ".DB_PREFIX."system_department as department  on position.department_sysno =department.sysno
                {$where}";
        $result['totalRow']=$this->dbh->select_one($sql);
        $result['totalPage'] = ceil($result['totalRow'] / $pages);

        $this->dbh->set_page_num($pagec);
        $this->dbh->set_page_rows($pages);

        $sql = "SELECT position.*,department.departmentname FROM ".DB_PREFIX."system_position as position
              left join ".DB_PREFIX."system_department as department  on position.department_sysno =department.sysno
              {$where} {$order}";
        $result['list'] = $this->dbh->select_page($sql);
        return $result;
    }

    /**
     * Title: 根据编号查询内容
     */
    public function getPositionById($id)
    {
        $sql = "SELECT position.*,department.departmentname FROM ".DB_PREFIX."system_position as position
              left join ".DB_PREFIX."system_department as department  on position.department_sysno =department.sysno
              WHERE position.`sysno`=" . $id;
        $data = $this->dbh->select_row($sql);
        return $data;
    }

    /**
     * Title:新增职位
     */
    public function addPosition($data)
    {
        return $this->dbh->insert(DB_PREFIX.'system_position', $data);
    }

    /**
     * Title: 编辑职位
     */
    public function updatePosition($id, $data)
    {
        return $this->dbh->update(DB_PREFIX.'system_position', $data, 'sysno=' . intval($id));
    }
}
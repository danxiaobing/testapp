<?php
/**
 * 用户管理
 *
 * @author  Alan
 * @date    2016-11-17 15:25:26
 *
 */

class UnitModel
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

    //获取计量单位信息表
    public function getUnit()
    {
        $sql = "SELECT sysno,unitname FROM ".DB_PREFIX."base_unit WHERE `status` = 1 AND  isdel = 0";
        return $this->dbh->select($sql);
    }

    /**
     * 
     */
    public function getBaseUnit($params)
    {
        $filter = array();
         if (isset($params['id']) && $params['id'] != '') {
             $filter[] = " sysno = '" . $params['id'] . "' ";
         }
        if (isset($params['status']) && $params['status'] != '') {
            $filter[] = " status = '" . $params['status'] . "' ";
        }
        if (isset($params['unitname']) && $params['unitname'] != '') {
            $filter[] = " unitname = '" . $params['unitname'] . "' ";
        }

        $where = '';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

         $sql = "SELECT * FROM ".DB_PREFIX."base_unit WHERE isdel = 0 ".$where;

         if(isset($params['row']) && $params['row'] != '')
         {
            return $this->dbh->select_row($sql);
        }else{
            return $this->dbh->select($sql);
        }    
    }

    public function getUnitById($id)
    {
        $sql = "SELECT * FROM ".DB_PREFIX."base_unit WHERE  sysno = " . intval($id);
        return $this->dbh->select_row($sql);
    }

    /**
     * 添加 计量单位
     */
    public function addUnit($params)
    {
        return $this->dbh->insert(DB_PREFIX.'base_unit',$params);
    }

    /**
     *  修改计量单位
     */
    public function updateUnit($params,$id)
    {
        return $this->dbh->update(DB_PREFIX.'base_unit',$params,'sysno=' . intval($id));
    }

    /**
     * 删除计量单位
     */
    public function delUnit($id)
    {
        $params = array();
        $params['isdel'] = 1;
        $params['status'] = 2;
        
        return $this->dbh->update(DB_PREFIX.'base_unit',$params,'sysno in (' . $id.')' );
    }

    /**
     * 查询唯一性
     */
    public function getBaseUnitOnly($params){
        $filter = array();
        if (isset($params['sysno']) && $params['sysno'] != '') {
            $filter[] = " `sysno` !='" . $params['sysno'] . "' ";
        }
        if (isset($params['unitname']) && $params['unitname'] != '') {
            $filter[] = " unitname = '" . $params['unitname'] . "' ";
        }
        $where = '';
        if (1 <= count($filter)) {
            $where .= ' and ' . implode(' AND ', $filter);
        }

        $sql = "SELECT * FROM ".DB_PREFIX."base_unit WHERE isdel = 0 ".$where;

        return $this->dbh->select($sql);

    }
    /*
         * 更新状态
         * */
    public function updateStatus($date){
        if($date){
            $ids = $date['ids'];
            $status = ['status'=>$date['status']];
            $res = $this->dbh->update(DB_PREFIX.'base_unit',$status,'sysno in ('.$ids.')');
        }
        return $res;
    }
    function isDel($id){
        if(id){
            $sql = "select count(unit_sysno) from ".DB_PREFIX."base_othercost  where unit_sysno IN (".$id.")";
            return $this->dbh->select_one($sql);
        }else{
            return false;
        }


    }

}
<?php

/**
 * Created by PhpStorm.
 * User: hanshutan
 * Date: 2016/11/17 0017
 * Time: 16:00
 */
Class StoragecostModel
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
     * @title 查询所有的信息
     * @param $params
     * @param int $pages
     * @param int $pagec
     */
    public function getStoragecost($params, $pages = 10, $pagec = 1)
    {
        $filter = array();

        if (isset($params['storagecostno']) && $params['storagecostno'] != '') {
            $filter[] = " cost.`storagecostno` LIKE '%{$params['storagecostno']}%' ";
        }

        if (isset($params['storagecostname']) && $params['storagecostname'] != '') {
            $filter[] = " cost.`storagecostname` LIKE '%{$params['storagecostname'] }%' ";
        }

        if (isset($params['storagecosttype']) && $params['storagecosttype'] != '') {
            $filter[] = " cost.`storagecosttype` = {$params['storagecosttype'] } ";
        }

        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " cost.`goods_sysno` = {$params['goods_sysno'] } ";
        }

        if (isset($params['storagetank_category_sysno']) && $params['storagetank_category_sysno'] != '') {
            $filter[] = " cost.`storagetank_category_sysno` = {$params['storagetank_category_sysno'] } ";
        }

        if (isset($params['status']) && $params['status'] != '') {
            $filter[] = " cost.`status` = {$params['status'] } ";
        }

        if (isset($params['isdel']) && $params['isdel'] != '') {
            $filter[] = " cost.`isdel` = {$params['isdel'] } ";
        }

        if (1 <= count($filter)) {

            $where = " WHERE " . implode(' AND ', $filter);
        }

        $order = "ORDER BY cost.`updated_at` DESC";

        $result = array('total' => 0, 'list' => array());
        $sql = "SELECT count(cost.`sysno`) FROM `".DB_PREFIX."base_storagecost` as cost
                LEFT JOIN `".DB_PREFIX."base_storagetank_category` as tank on cost.`storagetank_category_sysno` =tank.`sysno`
                LEFT JOIN `".DB_PREFIX."base_goods` as goods on cost.`goods_sysno` = goods.`sysno`
                LEFT JOIN `".DB_PREFIX."base_unit` AS bu ON bu.`sysno`=cost.`unit`
                {$where}";
        $result['totalRow'] = $this->dbh->select_one($sql);
        $result['totalPage'] = ceil($result['totalRow'] / $pages);

        $this->dbh->set_page_num($pagec);
        $this->dbh->set_page_rows($pages);

        $sql = "SELECT cost.*,tank.`storagetank_categoryname`,goods.`goodsname`,bu.`unitname`
                FROM ".DB_PREFIX."base_storagecost as cost
                LEFT JOIN ".DB_PREFIX."base_storagetank_category as tank on cost.`storagetank_category_sysno` =tank.`sysno`
                LEFT JOIN ".DB_PREFIX."base_goods as goods on cost.`goods_sysno` = goods.`sysno`
                LEFT JOIN `".DB_PREFIX."base_unit` AS bu ON bu.`sysno`=cost.`unit`
                {$where} {$order}";
        $result['list'] = $this->dbh->select_page($sql);
        return $result;
    }

    /**
     * Title: 根据编号查询内容
     */
    public function getStoragecostById($id)
    {
        $sql = "SELECT cost.*,tank.storagetank_categoryname,goods.goodsname,bu.`unitname` FROM ".DB_PREFIX."base_storagecost as cost
                left join ".DB_PREFIX."base_storagetank_category as tank on cost.storagetank_category_sysno =tank.sysno
                left join ".DB_PREFIX."base_goods as goods on cost.goods_sysno = goods.sysno 
                LEFT JOIN `".DB_PREFIX."base_unit` AS bu ON bu.`sysno`=cost.`unit` 
                WHERE cost.`sysno`=" . $id;
        return  $this->dbh->select_row($sql);
    }

    /**
     * Title:新增
     */
    public function addStoragecost($data)
    {
        return $this->dbh->insert(DB_PREFIX.'base_storagecost', $data);
    }

    /**
     * Title: 编辑
     */
    public function upStoragecost($id, $data)
    {
        return $this->dbh->update(DB_PREFIX.'base_storagecost', $data, 'sysno=' . intval($id));
    }

    /**
    * 批量启用禁用
    */
    public function change($params,$state)
    {
        switch ($state) {
            case 'start':
                foreach ($params as  $v) {
                    $this->dbh->update(DB_PREFIX.'base_storagecost', array('status'=>1), 'sysno='. intval($v) );
                }
                return true;
                break;
            case 'stop':
                foreach ($params as  $v) {
                    $this->dbh->update(DB_PREFIX.'base_storagecost', array('status'=>2), 'sysno='. intval($v) );
                }
                return true;  
            break;
        }

    } 

}
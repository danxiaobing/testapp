<?php
/**
 * Created by PhpStorm.
 * User: hanshutan
 * Date: 2016/11/18 0018
 * Time: 15:33
 */
Class SettlementModel
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
    public function getSettlement($params, $pages = 10, $pagec = 1)
    {
        $filter = array();
        if (isset($params['settlementname']) && $params['settlementname'] != '') {
            $filter[] = " `settlementname` LIKE '%" . $params['settlementname'] . "%' ";
        }
        if(isset($params['status']) && $params['status'] != '' ){
            $filter[] = " `status` = " . $params['status'];
        }

        $where = 'where isdel = 0 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $order = "ORDER BY `updated_at` DESC";

        $result = array('total' => 0, 'list' => array());
        $sql = "SELECT count(`sysno`) FROM ".DB_PREFIX."base_settlement {$where}";
        $result['totalRow'] = $this->dbh->select_one($sql);
        $result['totalPage'] = ceil($result['totalRow'] / $pages);

        $this->dbh->set_page_num($pagec);
        $this->dbh->set_page_rows($pages);

        $sql = "SELECT * FROM ".DB_PREFIX."base_settlement {$where} {$order}";
        $result['list'] = $this->dbh->select_page($sql);
        return $result;
    }

    /**
     * Title: 根据编号查询内容
     */
    public function getSettlementById($id)
    {
        $sql = "SELECT * FROM ".DB_PREFIX."base_settlement where `sysno`=" . $id;
        $data = $this->dbh->select_row($sql);
        return $data;
    }

    /**
     * Title:新增职位
     */
    public function addSettlement($data)
    {
        return $this->dbh->insert(DB_PREFIX.'base_settlement', $data);
    }

    /**
     * Title: 编辑职位
     */
    public function upSettlement($id, $data)
    {
        return $this->dbh->update(DB_PREFIX.'base_settlement', $data, 'sysno in (' .$id. ')' );
    }
    /*
     * 更新状态
     * */
    public function updateStatus($date){
        if($date){
            $ids = $date['ids'];
            $status = ['status'=>$date['status']];
            $res = $this->dbh->update(DB_PREFIX.'base_settlement',$status,'sysno in ('.$ids.')');
        }
        return $res;
    }
}
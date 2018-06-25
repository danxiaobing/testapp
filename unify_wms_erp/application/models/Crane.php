<?php
/**
 * Created by PhpStorm.
 * User: Jay Xu
 * Date: 2017/7/5 0010
 * Time: 17:14
 */

class CraneModel
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
     * 根据条件检索鹤位
     * @return 数组
     * @author Jay Xu
     */
    public function searchCrane($params)
    {
        $filter = array();

        if (isset($params['cranename']) && $params['cranename'] != '') {
            $filter[] = " `cranename` LIKE '%{$params['cranename']}%' ";
        }

        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " `goods_sysno` LIKE '%{$params['goods_sysno']}%' ";
        }

        if (isset($params['goodsname']) && $params['goodsname'] != '') {
            $filter[] = " `goodsname` LIKE '%{$params['goodsname']}%' ";
        }

        if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
            $filter[] = " `status`='{$params['bar_status']}'";
        }
        if (isset($params['bar_isdel']) && $params['bar_isdel'] != '-100') {
            $filter[] = " `isdel`='{$params['bar_isdel']}'";
        }
        $where = 'where isdel =0 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $order = "order by `updated_at` desc";
        $sql = "SELECT COUNT(*)  FROM `" . DB_PREFIX . "base_crane` {$where} {$order} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();

        if ($result['totalRow']) {

            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT * FROM `" . DB_PREFIX . "base_crane` {$where} {$order} ";
                if ($params['orders'] != '')
                    $sql .= " order by " . $params['orders'];

                $arr = $this->dbh->select($sql);


                $result['list'] = $arr;
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);


                $sql = "SELECT * FROM `" . DB_PREFIX . "base_crane` {$where} {$order} ";
                if ($params['orders'] != '')
                    $sql .= " order by " . $params['orders'];

                $arr = $this->dbh->select_page($sql);


                $result['list'] = $arr;
            }

        }
        return $result;
    }

    /**
     * 新增鹤位数据添加
     * @return boolean
     * @author Jay Xu
     */
    public function addCrane($data)
    {
        return $this->dbh->insert(DB_PREFIX . 'base_crane', $data);
    }


    /**
     * 根据id获得鹤位细节
     * id: 权限id
     * @return 数组
     */
    public function getCraneById($id)
    {
        $sql = "select * from " . DB_PREFIX . "base_crane where sysno=" . intval($id);

        return $this->dbh->select_row($sql);

    }


    /**
     * 鹤位更新
     * @param array $data
     * @param string $privileges
     * @return bool
     */

    public function updateCrane($data, $id)
    {
        return $this->dbh->update(DB_PREFIX . 'base_crane', $data, 'sysno=' . intval($id));
    }


    /**
     * 批量启用禁用
     */
    public function change($params, $state)
    {
        switch ($state) {
            case 'start':
                foreach ($params as $v) {
                    $this->dbh->update(DB_PREFIX . 'base_crane', array('status' => 1), 'sysno=' . intval($v));
                }
                return true;
                break;
            case 'stop':
                foreach ($params as $v) {
                    $this->dbh->update(DB_PREFIX . 'base_crane', array('status' => 2), 'sysno=' . intval($v));
                }
                return true;
                break;
        }

    }

}





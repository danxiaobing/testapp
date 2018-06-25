<?php
/**
 * Created by PhpStorm.
 * User: Jay Xu
 * Date: 2017/5/15 0010
 * Time: 14:04
 */

class WharfModel
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
     * 根据条件检索码头
     * @return 数组
     * @author Jay Xu
     */
    public function searchWharf($params)
    {
        $filter = array();
        if (isset($params['wharfno']) && $params['wharfno'] != '') {
            $filter[] = " `wharfno`='{$params['wharfno']}'";
        }

        if (isset($params['wharfname']) && $params['wharfname'] != '') {
            $filter[] = " `wharfname` LIKE '%{$params['wharfname']}%' ";
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
        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."base_wharf` {$where} {$order} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();

        if ($result['totalRow'])
        {

            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "SELECT * FROM `".DB_PREFIX."base_wharf` {$where} {$order} ";
                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;

                $arr = 	$this->dbh->select($sql);


                $result['list'] = $arr;
            }else{
                $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent'] );
                $this->dbh->set_page_rows($params['pageSize'] );


                $sql = "SELECT * FROM `".DB_PREFIX."base_wharf` {$where} {$order} ";
                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;

                $arr = 	$this->dbh->select_page($sql);


                $result['list'] = $arr;
            }

        }
        return $result;
    }

    /**
     * 新增码头数据添加
     * @return boolean
     * @author Jay Xu
     */
    public function addWharf($data)
    {
        return $this->dbh->insert(DB_PREFIX.'base_wharf', $data);
    }


    /**
     * 根据id获得码头细节
     * id: 权限id
     * @return 数组
     */
    public function getWharfById($id = 0)
    {
        $sql = "select w.* from ".DB_PREFIX."base_wharf w where sysno = $id ";

        return $this->dbh->select_row($sql);
    }


    /**
     * 码头更新
     * @param array $data
     * @param string $privileges
     * @return bool
     */

    public function updateWharf($id = 0, $data = array())
    {
        return  $this->dbh->update(DB_PREFIX.'base_wharf', $data, 'sysno=' . intval($id));

    }

    /**
     * 获取片区所有记录
     * @author zhaoshiyu
     * @return array
     */

    public function getRecordCount()
    {
        $sql = "select sysno,wharfname from ".DB_PREFIX."base_wharf where status=1 and isdel=0";
        return  $this->dbh->select($sql);

    }


    /**
     * 批量启用禁用
     */
    public function change($params,$state)
    {
        switch ($state) {
            case 'start':
                foreach ($params as  $v) {
                    $this->dbh->update(DB_PREFIX.'base_wharf', array('status'=>1), 'sysno='. intval($v) );
                }
                return true;
                break;
            case 'stop':
                foreach ($params as  $v) {
                    $this->dbh->update(DB_PREFIX.'base_wharf', array('status'=>2), 'sysno='. intval($v) );
                }
                return true;
                break;
        }

    }
    /**
     * 获取片区所有记录
     * @author zhaoshiyu
     * @return array
     */

    public function getwharfdetail()
    {
        $sql = "select * from ".DB_PREFIX."base_wharf where status<2 and isdel=0";
        return  $this->dbh->select($sql);

    }


}
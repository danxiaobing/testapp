<?php

/**
 * Created by PhpStorm.
 * User: Jay Xu
 * Date: 2016/11/19 0015
 * Time: 14:50
 */
class PrinttitleModel
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
     * 根据条件检索票据抬头
     * @return 数组
     * @author Jay Xu
     */
    public function searchPrinttitle($params)
    {
        $filter = array();
        if (isset($params['titlename']) && $params['titlename'] != '') {
            $filter[] = " `titlename` LIKE '%{$params['titlename']}%' ";
        }
        if (isset($params['isdefault']) && $params['isdefault'] != '') {
            $filter[] = " `isdefault` = '{$params['isdefault']}' ";
        }

        if (isset($params['status']) && $params['status'] != '') {
            $filter[] = " `status`='{$params['status']}'";
        }
        $where = 'where isdel =0 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."base_printtitle` {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();

        if ($result['totalRow'])
        {
            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "SELECT * FROM `".DB_PREFIX."base_printtitle` {$where} ";
                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;

                $arr = 	$this->dbh->select($sql);


                $result['list'] = $arr;
            }else{
                $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent'] );
                $this->dbh->set_page_rows($params['pageSize'] );
                if($params['orders']){
                    $order = ' ORDER BY '.$params['orders'].' desc';
                }else{
                    $order = ' ORDER BY updated_at desc';
                }

                $sql = "SELECT * FROM `".DB_PREFIX."base_printtitle` {$where} {$order} ";
                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;

                $arr = 	$this->dbh->select_page($sql);


                $result['list'] = $arr;
            }

        }
        return $result;
    }

    /**
     * 根据id获得 票据抬头明细
     * id: 权限id
     * @return 数组
     */
    public function getPrintTitleById($id = 0)
    {
        $sql = "select * from ".DB_PREFIX."base_printtitle where sysno = $id ";

        return $this->dbh->select_row($sql);
    }

    /**
     * 新增票据抬头
     * @return boolean
     * @author Jay Xu
     */
    public function addPrintTitle($data)
    {
        if($data['isdefault']==1){
            $printTitle = array('isdefault'=>'0');
            $this->dbh->update(DB_PREFIX.'base_printtitle', $printTitle,'1=1');
        }else{
            $res = $this -> getDefault();
            if(!$res){
                return 'mustDefault';
            }
        }
        return $this->dbh->insert(DB_PREFIX.'base_printtitle', $data);
    }


    /**
     * 更新票据抬头
     * @param int $id
     * @param array $data
     * @return string
     */
    public function updatePrintTitle($id = 0, $data = array())
    {
        if($data['isdefault'] == 1){
            $company=array('isdefault'=>'0');
            $this->dbh->update(DB_PREFIX.'base_printtitle', $company, 'sysno !=' . intval($id));
        }else{
            if(isset($data['isdefault'])){
                $res = $this -> getDefault();
                if($res['sysno']  == $id){
                    return 'mustDefault';
                }
            }
        }
        return  $this->dbh->update(DB_PREFIX.'base_printtitle', $data, 'sysno in (' .$id.')');

    }

    /**
     * 获取默认的票据抬头
     * @author Alan
     */
    public function getDefault()
    {
        $sql = "SELECT * FROM ".DB_PREFIX."base_printtitle WHERE `status` = 1 AND isdel = 0 AND  `isdefault` = 1 ";
        return $this->dbh->select_row($sql);
    }

    /**
     * 获取有效的票据抬条数
     */
    public function getDefaultCount()
    {
        $sql = "SELECT COUNT(*) as num  FROM ".DB_PREFIX."base_printtitle WHERE `status` = 1 AND isdel = 0 AND  `isdefault` = 1 ";
        return $this->dbh->select_one($sql);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: jp
 * Date: 2017/7/06 0017
 * Time: 11:42
 */

class BerthModel
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
     * 根据条件检索片区
     * @return 数组
     * @author jp
     */
    public function searchBerth($params)
    {
        $filter = array();


        if (isset($params['berthname']) && $params['berthname'] != '') {
            $filter[] = " `berthname` LIKE '%{$params['berthname']}%' ";
        }
        if (isset($params['wharfname']) && $params['wharfname'] != '') {
            $filter[] = " `wharfname` LIKE '%{$params['wharfname']}%' ";
        }

        if (isset($params['bar_status']) && $params['bar_status'] != '') {
            $filter[] = " `status`='{$params['bar_status']}'";
        }

        if (isset($params['sysno']) && $params['sysno'] != '') {
            $filter[] = " `sysno` !='{$params['sysno']}'";
        }

        $where = 'where isdel =0 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $order = "order by `updated_at` desc";
        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."base_berth` {$where} {$order} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();

        if ($result['totalRow'])
        {

            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "SELECT * FROM `".DB_PREFIX."base_berth` {$where} {$order} ";
                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;

                $arr = 	$this->dbh->select($sql);


                $result['list'] = $arr;
            }else{
                $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent'] );
                $this->dbh->set_page_rows($params['pageSize'] );


                $sql = "SELECT * FROM `".DB_PREFIX."base_berth` {$where} {$order} ";

                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;

                $arr = 	$this->dbh->select_page($sql);

                $result['list'] = $arr;
            }

        }
        return $result;
    }

    /**
     * 新增泊位数据
     * @return boolean
     * @author jp
     */
    public function addBerth($data)

    {
        return $this->dbh->insert(DB_PREFIX.'base_berth', $data);
    }


    /**
     * 根据id获得泊位细节
     * id: 权限id
     * @return 数组
     */
    public function getBerthById($id = 0)
    {
        $sql = "select * from ".DB_PREFIX."base_berth  where sysno = $id ";

        return $this->dbh->select_row($sql);
    }


    /**
     * 片区更新
     * @param array $data
     * @param string $privileges
     * @return bool
     */

    public function updateBerth($id = 0, $data = array(),$berthorderno)
    {
        $sql = "select COUNT(sysno) as berthnum from ".DB_PREFIX."doc_berthorder_detail bd
                where bd.berthname ={$berthorderno} and bd.isdel = 0 and bd.status < 2";
       // echo $sql;die;
        $berthnum = $this->dbh->select_one($sql);

       if($berthnum>=1){
           return ['statuscode'=>300,'msg'=>'被引用不许删除'];
       }else{
           $res = $this->dbh->update(DB_PREFIX.'base_berth', $data, 'sysno=' . intval($id));
           if(!$res){
               return ['statuscode'=>300,'msg'=>'删除失败'];
           }
           return ['statuscode'=>200,$res];
       }


    }

    public function updateBerthedit($id = 0, $data = array())
    {
        return    $res = $this->dbh->update(DB_PREFIX.'base_berth', $data, 'sysno=' . intval($id));
    }
    /**
     * 批量启用禁用
     */
    public function change($params,$state)
    {
        switch ($state) {
            case 'start':
                foreach ($params as  $v) {
                    $this->dbh->update(DB_PREFIX.'base_berth', array('status'=>1), 'sysno='. intval($v) );
                }
                return true;
                break;
            case 'stop':
                foreach ($params as  $v) {
                    $this->dbh->update(DB_PREFIX.'base_berth', array('status'=>2), 'sysno='. intval($v) );
                }
                return true;
                break;
        }

    }


    /*
     * 获取码头信息
     * */
    public function getWharfMsg(){
        $sql = "select * from `".DB_PREFIX."base_wharf` where isdel = 0 and status < 2 ";
        $result  = $this->dbh->select($sql);
        if($result){
            return $result;
        }else{
            return array();
        }
    }
/*
 * 获取历史记录
 *
 * */
    public function getberthHistory($params)
    {

        $filter = array();
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " usetime >= '{$params['begin_time']}' ";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " usetime <= '{$params['end_time']} 23:59:59' ";
        }
        if ( isset($params['pipelinetype']) && $params['pipelinetype'] != '' ) {
            $filter[] = "pipelinetype = '{$params['pipelinetype']}'";
        }

        if( isset($params['goods_sysno']) && $params['goods_sysno'] != '' ){
            $filter[] = "goods_sysno = '{$params['goods_sysno']}'";
        }

        if( isset($params['berth_sysno']) && $params['berth_sysno'] != '' ){
            $filter[] = "berth_sysno = '{$params['berth_sysno']}'";
        }

        if( isset($params['wharfname']) && $params['wharfname'] != '' ){
            $filter[] = "wharf_sysno = '{$params['wharfname']}'";
        }

        if( isset($params['businesstype']) && $params['businesstype'] != '' ){
            $filter[] = "businesstype = '{$params['businesstype']}'";
        }

        $where =" where isdel=0 "  ;
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $order = " order by  created_at desc";



        $sql = "SELECT count(sysno) FROM `".DB_PREFIX."base_berth_uselog` {$where} ";

        $result['totalRow'] = $this->dbh->select_one($sql);

        $sql = "SELECT * FROM `".DB_PREFIX."base_berth_uselog` {$where} {$order}";

        if($params['page']===false){

            $result = $this->dbh->select($sql);

        }else{

            $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
            $this->dbh->set_page_num($params['pageCurrent']);
            $this->dbh->set_page_rows($params['pageSize']);

            $data = $this->dbh->select_page($sql);


            $result['list']=$data;
        }


        return $result;
    }

}
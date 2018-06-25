<?php
/**
 * Created by PhpStorm.
 * User: Jay Xu
 * Date: 2016/11/17 0017
 * Time: 11:42
 */

    class AreaModel
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
         * @author Jay Xu
         */
        public function searchArea($params)
        {
            $filter = array();
            if (isset($params['areaid']) && $params['areaid'] != '') {
                $filter[] = " `areaid`='{$params['areaid']}'";
            }

            if (isset($params['areaname']) && $params['areaname'] != '') {
                $filter[] = " `areaname` LIKE '%{$params['areaname']}%' ";
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
            $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."base_area` {$where} {$order} ";

            $result = $params;

            $result['totalRow'] = $this->dbh->select_one($sql);

            $result['list'] = array();

            if ($result['totalRow'])
            {

                if( isset($params['page'] ) && $params['page'] == false){
                    $sql = "SELECT * FROM `".DB_PREFIX."base_area` {$where} {$order} ";
                    if($params['orders'] != '')
                        $sql .= " order by ".$params['orders'] ;

                    $arr = 	$this->dbh->select($sql);


                    $result['list'] = $arr;
                }else{
                    $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);

                    $this->dbh->set_page_num($params['pageCurrent'] );
                    $this->dbh->set_page_rows($params['pageSize'] );


                    $sql = "SELECT * FROM `".DB_PREFIX."base_area` {$where} {$order} ";
                    if($params['orders'] != '')
                        $sql .= " order by ".$params['orders'] ;

                    $arr = 	$this->dbh->select_page($sql);


                    $result['list'] = $arr;
                }

            }
            return $result;
        }

        /**
         * 新增片区数据添加
         * @return boolean
         * @author Jay Xu
         */
        public function addArea($data)
        {
            return $this->dbh->insert(DB_PREFIX.'base_area', $data);
        }


        /**
         * 根据id获得片区细节
         * id: 权限id
         * @return 数组
         */
        public function getAreaById($id = 0)
        {
            $sql = "select p.* from ".DB_PREFIX."base_area p where sysno = $id ";

            return $this->dbh->select_row($sql);
        }


        /**
         * 片区更新
         * @param array $data
         * @param string $privileges
         * @return bool
         */

        public function updateArea($id = 0, $data = array())
        {
            return  $this->dbh->update(DB_PREFIX.'base_area', $data, 'sysno=' . intval($id));

        }

        /**
         * 获取片区所有记录
         * @author zhaoshiyu
         * @return array
         */

        public function getRecordCount()
        {   
            $sql = "select sysno,areaname from ".DB_PREFIX."base_area where status=1 and isdel=0";
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
                        $this->dbh->update(DB_PREFIX.'base_area', array('status'=>1), 'sysno='. intval($v) );
                    }
                    return true;
                    break;
                case 'stop':
                    foreach ($params as  $v) {
                        $this->dbh->update(DB_PREFIX.'base_area', array('status'=>2), 'sysno='. intval($v) );
                    }
                    return true;  
                break;
            }

        } 


        /**
        * 判断片区是否被储罐引用
        */

        public function isdel($id)
        {
            $sql = "select count(*) num from `".DB_PREFIX."base_storagetank` where isdel = 0 AND area_sysno=".$id;
            $result = $this->dbh->select_row($sql);

            if($result['num']>0){
                return true;
            }else{
                return false;
            }
        }

    }
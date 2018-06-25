<?php

/**
 * @Author: Dujiangjiang
 * @Date:   2016-12-07 14:35:05
 * @Last Modified by:   Dujiangjiang
 * @Last Modified time: 2016-12-14 14:40:12
 */
class BookingModel
{
    /**
     * 数据库类实例
     *
     * @var object
     */
    public $dbh = null;

    /**
     * 缓存类实例
     *
     * @var object
     */
    public $mch = null;


    /**
     * Constructor
     *
     * @param   object $dbh
     * @param   object $mch
     * @return  void
     */
    public function __construct($dbh, $mch)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    /**
     * 待入库预约数据列表
     * @author Dujiangjiang
     * @return array()
     */
    public function getBookingInList($params)
    {
        $result = $params;
        $filter = array();

        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " dbi.`bookinginno`='{$params['bar_no']}' ";
        }
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " dbi.`customer_name` LIKE '%{$params['bar_name']}%' ";
        }
        if (isset($params['stockintype']) && $params['stockintype'] != '') {
            $filter[] = " dbi.`stockintype`={$params['stockintype']}";
        }
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " dbi.`bookingindate`>='{$params['begin_time']}'";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " dbi.`bookingindate`<='{$params['end_time']}'";
        }

        $where = ' dbi.`isdel`=0 AND dbi.`issave`=1 AND dbi.`status`=1 AND dbi.`bookinginstatus`=5 AND dbi.`issaveorder`=0 ';
        if (count($filter) > 0) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(DISTINCT(dbi.`sysno`)) FROM `".DB_PREFIX."doc_booking_in` AS dbi
        		LEFT JOIN `".DB_PREFIX."doc_booking_in_cars` AS dbic ON dbic.`bookingin_sysno`=dbi.`sysno`
        		LEFT JOIN `".DB_PREFIX."doc_booking_in_detail` AS dbid ON dbid.`bookingin_sysno`=dbi.`sysno`
        		LEFT JOIN `".DB_PREFIX."base_goods` AS bg ON bg.`sysno`=dbid.`goods_sysno`
                LEFT JOIN `".DB_PREFIX."customer` AS c ON c.`sysno`=dbi.`customer_sysno`
        		WHERE {$where}";
        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT dbi.*,dbic.`carname`,dbic.`mobilephone`,dbic.`idcard`,dbic.`carid`,bg.`goodsname`,bgq.qualityname,
                      c.`customername`,dbid.`goodsnature`,dbid.shipname
                    FROM `".DB_PREFIX."doc_booking_in` AS dbi
                    LEFT JOIN `".DB_PREFIX."doc_booking_in_cars` AS dbic ON dbic.`bookingin_sysno`=dbi.`sysno`
                    LEFT JOIN `".DB_PREFIX."doc_booking_in_detail` AS dbid ON dbid.`bookingin_sysno`=dbi.`sysno`
                    LEFT JOIN `".DB_PREFIX."base_goods` AS bg ON bg.`sysno`=dbid.`goods_sysno`
                    LEFT JOIN ".DB_PREFIX."base_goods_quality AS bgq ON bgq.sysno = dbid.goods_quality_sysno
                    LEFT JOIN `".DB_PREFIX."customer` AS c ON c.`sysno`=dbi.`customer_sysno`
                    WHERE {$where} group by dbi.sysno";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by created_at desc";
                }

                $result['list'] = $this->dbh->select($sql);

                $sql = "SELECT bookingin_sysno,SUM(bookinginqty) AS bookinginqty FROM ".DB_PREFIX."doc_booking_in_detail GROUP BY bookingin_sysno";

                $detailtata = $this->dbh->select($sql);

                foreach($result['list'] as $key=> $item){
                    foreach($detailtata as $value){
                        if($value['bookingin_sysno'] ==$item['sysno']){
                            $result['list'][$key]['bookinginqty'] = $value['bookinginqty'];
                            break;
                        }
                    }
                }

            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT dbi.*,dbic.`carname`,dbic.`mobilephone`,dbic.`idcard`,dbic.`carid`,bg.`goodsname`,bgq.qualityname,
                      c.`customername`,dbid.`goodsnature`,dbid.shipname
                    FROM `".DB_PREFIX."doc_booking_in` AS dbi
                    LEFT JOIN `".DB_PREFIX."doc_booking_in_cars` AS dbic ON dbic.`bookingin_sysno`=dbi.`sysno`
                    LEFT JOIN `".DB_PREFIX."doc_booking_in_detail` AS dbid ON dbid.`bookingin_sysno`=dbi.`sysno`
                    LEFT JOIN `".DB_PREFIX."base_goods` AS bg ON bg.`sysno`=dbid.`goods_sysno`
                    LEFT JOIN ".DB_PREFIX."base_goods_quality AS bgq ON bgq.sysno = dbid.goods_quality_sysno
                    LEFT JOIN `".DB_PREFIX."customer` AS c ON c.`sysno`=dbi.`customer_sysno`
                    WHERE {$where} group by dbi.sysno";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by created_at desc";
                }

                $result['list'] = $this->dbh->select_page($sql);

                $sql = "SELECT bookingin_sysno,SUM(bookinginqty) AS bookinginqty FROM ".DB_PREFIX."doc_booking_in_detail GROUP BY bookingin_sysno";

                $detailtata = $this->dbh->select($sql);

                foreach($result['list'] as $key=> $item){
                    foreach($detailtata as $value){
                        if($value['bookingin_sysno'] ==$item['sysno']){
                            $result['list'][$key]['bookinginqty'] = $value['bookinginqty'];
                            break;
                        }
                    }
                }

            }
        }
        return $result;
    }

    /**
     * 根据ID获取预约入库数据
     * @author Dujiangjiang
     * @return array()
     */
    public function getBookingInById($id)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."doc_booking_in` WHERE `sysno`={$id} and isdel = 0 and status < 2";
        return $this->dbh->select_row($sql);
    }

    /**
     * 待出库预约数据列表
     * @author Dujiangjiang
     * @return array()
     */
    public function getBookingOutList($params)
    {
        $filter = array();

        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " dbo.`bookingoutno` LIKE '%{$params['bar_no']}%' ";
        }
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " dbo.`customer_name` LIKE '%{$params['bar_name']}%' ";
        }
        if (isset($params['stockintype']) && $params['stockintype'] != '') {
            $filter[] = " dbo.`stockouttype`={$params['stockintype']}";
        }
        if (isset($params['bar_receivenumber']) && $params['bar_receivenumber'] != '') {
            $filter[] = " dbo.`receivenumber` like '%{$params['bar_receivenumber']}%'";
        }

        $where = ' dbo.`isdel`=0 AND dbo.`status`=1 AND dbo.`bookingoutstatus`= 5 AND dbo.`issaveorder`=0 ';
        if (count($filter) > 0) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        /*  $sql = "SELECT COUNT(DISTINCT(dbo.`sysno`)) FROM `".DB_PREFIX."doc_booking_out` AS dbo
                  LEFT JOIN `".DB_PREFIX."doc_booking_out_cars` AS dboc ON dboc.`bookingout_sysno`=dbo.`sysno`
                  LEFT JOIN `".DB_PREFIX."doc_booking_out_detail` AS dbod ON dbod.`bookingout_sysno`=dbo.`sysno`
                  LEFT JOIN `".DB_PREFIX."customer` AS c ON c.`sysno`=dbo.`customer_sysno`
                  WHERE {$where}";*/
        $sql = "select count(*)  FROM `".DB_PREFIX."doc_booking_out` AS dbo WHERE {$where}";
        $result['totalRow'] = $this->dbh->select_one($sql);

        $data['totalRow'] = $result['totalRow'];
        $data['list'] = array();
        if ($result['totalRow']) {
            // $this->dbh->set_page_num($params['pageCurrent']);
            // $this->dbh->set_page_rows($params['pageSize']);

            /*$sql = "SELECT DISTINCT(dbo.`sysno`),dbo.*,dboc.`carname`,dboc.`mobilephone`,dboc.`idcard`,dboc.`carid`,c.`customername`
                    FROM `".DB_PREFIX."doc_booking_out` AS dbo
                    LEFT JOIN `".DB_PREFIX."doc_booking_out_cars` AS dboc ON dboc.`bookingout_sysno`=dbo.`sysno`
                    LEFT JOIN `".DB_PREFIX."doc_booking_out_detail` AS dbod ON dbod.`bookingout_sysno`=dbo.`sysno`
                    LEFT JOIN `".DB_PREFIX."customer` AS c ON c.`sysno`=dbo.`customer_sysno`
                    WHERE {$where}";*/
            $sql = "select dbo.* ,c.`customername` FROM `".DB_PREFIX."doc_booking_out` AS dbo
                   LEFT JOIN `".DB_PREFIX."customer` AS c ON c.`sysno`=dbo.`customer_sysno`
                   WHERE {$where}";
            if ($params['orders'] != '') {
                $sql .= " order by " . $params['orders'];
            } else {
                $sql .= " order by created_at desc";
            }
            $list = $this->dbh->select($sql);
            $dData = array();
            if($list){
                foreach ($list as $row) {
                    if(isset($params['bar_goodsname']) && $params['bar_goodsname'] != ''){
                        $subsql = "select *,sum(bookingoutqty) as  total,group_concat(DISTINCT shipname Separator' ') as shipname FROM `".DB_PREFIX."doc_booking_out_detail` where bookingout_sysno = '{$row['sysno']}' and goodsname like '%{$params['bar_goodsname']}%' group by bookingout_sysno";
                    }else{
                        $subsql = "select *,sum(bookingoutqty) as  total,group_concat(DISTINCT shipname Separator' ') as shipname FROM `".DB_PREFIX."doc_booking_out_detail` where bookingout_sysno = '{$row['sysno']}' group by bookingout_sysno";
                    }
                    
                    $extendRow = $this->dbh->select_row($subsql);
                    $row['goodsname'] = $extendRow['goodsname'];
                    $row['unitname'] = $extendRow['unitname'];
                    $row['goodsnature'] = $extendRow['goodsnature'];
                    $row['qualityname'] = $extendRow['qualityname'];
                    $row['bookingoutqty'] = $extendRow['total'];
                    $row['shipname'] = $extendRow['shipname'];
                    if(isset($params['bar_goodsname']) && $params['bar_goodsname'] != '' && !$row['goodsname']){
                        continue;
                    }
                    $dData[] = $row;
                }
            }
            if(isset($params['page'])&&$params['page']==false){
                $data['list'] = $dData;
                
            }else{
                $data['totalRow'] = count($dData);
                $data['totalPage'] = ceil($data['totalRow'] / $params['pageSize']);
                $datalist=array_chunk($dData,$params['pageSize'],false);
                $data['list']= $datalist [$params['pageCurrent']-1] ? $datalist [$params['pageCurrent']-1] : [];
            }
        
        }
        return $data;
    }

    /**
     * 根据ID获取预约出库数据
     * @author Dujiangjiang
     * @return array()
     */
    public function getBookingOutById($id)
    {
        $sql = "SELECT * FROM `".DB_PREFIX."doc_booking_out` WHERE `sysno`={$id}";
        return $this->dbh->select_row($sql);
    }

    public function getBookingDetailList($params)
    {

        $filter = array();
        if (isset($params['bookingin_sysno']) && $params['bookingin_sysno'] != '') {
            $filter[] = " s.`bookingin_sysno` = '" . $params['bookingin_sysno'] . "' ";
        }
        $where = 's.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_booking_in_detail` s  where {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT  s.*,bg.goodsname,gq.qualityname as goods_quality_name,bu.unitname,st.storagetankname,s.bookingindate as goodsreceiptdate,gq.qualityname
                FROM `".DB_PREFIX."doc_booking_in_detail` s 
                LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno=s.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON ga.goods_sysno=bg.sysno
                LEFT JOIN ".DB_PREFIX."base_goods_quality gq ON gq.sysno=s.goods_quality_sysno
                LEFT JOIN ".DB_PREFIX."base_unit bu ON bu.sysno=ga.unit_sysno
                LEFT join ".DB_PREFIX."base_storagetank st ON st.sysno=s.storagetank_sysno
                where {$where} group by s.`sysno`";
                if ($params['orders'] != '')
                    $sql .= " order by " . $params['orders'];
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT  s.*,bg.goodsname,gq.qualityname as goods_quality_name,bu.unitname,st.storagetankname,s.bookingindate as goodsreceiptdate,gq.qualityname
                FROM `".DB_PREFIX."doc_booking_in_detail` s 
                LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno=s.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON ga.goods_sysno=bg.sysno
                LEFT JOIN ".DB_PREFIX."base_goods_quality gq ON gq.sysno=s.goods_quality_sysno
                LEFT JOIN ".DB_PREFIX."base_unit bu ON bu.sysno=ga.unit_sysno
                LEFT join ".DB_PREFIX."base_storagetank st ON st.sysno=s.storagetank_sysno
                where {$where} group by s.`sysno`";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    public function searchBookingCar($params)
    {

        $filter = array();
        if (isset($params['bookingin_sysno']) && $params['bookingin_sysno'] != '') {
            $filter[] = " s.`bookingin_sysno` = '" . $params['bookingin_sysno'] . "' ";
        }
        $where = 's.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_booking_in_cars` s  where {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT  s.* FROM `".DB_PREFIX."doc_booking_in_cars` s  where {$where} ";
                if ($params['orders'] != '')
                    $sql .= " order by " . $params['orders'];
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT s.* FROM `".DB_PREFIX."doc_booking_in_cars` s  where {$where}  ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }


    /**
     * 获取入库预约容量
     * @author zhaoshiyu
     */
    public function getBookingInData()
    {
        $where = " where dbi.`isdel`=0 AND dbi.`issave`=1 AND dbi.`status`=1 AND dbi.`bookinginstatus`=4 AND dbi.`issaveorder`=0 group by dbid.storagetank_sysno";
        $sql = "select dbid.storagetank_sysno,sum(bookinginqty) as bookinginqty from ".DB_PREFIX."doc_booking_in dbi 
                left join 
                (select bookingin_sysno,storagetank_sysno,bookinginqty from ".DB_PREFIX."doc_booking_in_detail where `status`=1 and `isdel`=0) dbid on dbi.sysno=dbid.bookingin_sysno $where";
        return $this->dbh->select($sql);

    }

    /*
     * 根据预约单ID获取预约容量
     * @author zhaoshiyu
     */
    public function getBookingStockById($id)
    {
        $where = "where dbi.sysno = " . intval($id);
        $sql = "SELECT dbid.storagetank_sysno,dbid.bookinginqty,bgs.goodsname,dbid.goods_sysno,dbid.goodsnature  
            FROM ".DB_PREFIX."doc_booking_in dbi 
            left join ".DB_PREFIX."doc_booking_in_detail dbid on dbi.sysno=dbid.bookingin_sysno
            left join ".DB_PREFIX."base_goods bgs on bgs.sysno = dbid.goods_sysno $where";
        return $this->dbh->select($sql);
    }


    /*
     * 插入入库待审核提醒消息记录
     */
    public function shipinsertmes($info){

        $user = Yaf_Registry::get(SSN_VAR);
        $B = new BookoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        switch ($info['stockintype']) {
            case 1:
                if($info['stockinno'])
                {
                    $order  = "船入库订单";
                    $privilegename = "船入库";
                    $sql = "SELECT sysno from hengyang_system_privilege where privilegename = '{$privilegename}'";
                    $privilege_sysno = $this->dbh->select_one($sql);
                    $action = "list"; 
                    $control = "stockshipin";
                    //根据父级获取权限用户
                    $sql = "SELECT sysno from hengyang_system_privilege where privilegecontroller like '%".$control."%' and privilegeaction like '%".$action."' and parent_sysno=".intval($privilege_sysno);
                    $privilege_sysno = $this->dbh->select_one($sql);
                    $userArr = $B->getUsers($privilege_sysno);
                }else{
                    $order = "船入库预约单";
                    $privilegename = "船入库预约";
                    $sql = "SELECT sysno from hengyang_system_privilege where privilegename = '{$privilegename}'";
                    $privilege_sysno = $this->dbh->select_one($sql);

                    $action = "review"; 
                    $control = "bookshipin";
                    //根据父级获取权限用户
                    $sql = "SELECT sysno from hengyang_system_privilege where privilegecontroller like '%".$control."%' and privilegeaction like '%".$action."' and parent_sysno=".intval($privilege_sysno);
                    $privilege_sysno = $this->dbh->select_one($sql);
                    $userArr = $B->getUsers($privilege_sysno);
                }
                break;
            case 2:
                    $order = "车入库预约单";
                    $privilegename = "车入库预约";
                    $sql = "SELECT sysno from hengyang_system_privilege where privilegename = '{$privilegename}'";
                    $privilege_sysno = $this->dbh->select_one($sql);

                    $action = "review"; 
                    $control = "bookcarin";
                     //根据父级获取权限用户
                    $sql = "SELECT sysno from hengyang_system_privilege where privilegecontroller like '%".$control."%' and privilegeaction like '%".$action."' and parent_sysno=".intval($privilege_sysno);
                    $privilege_sysno = $this->dbh->select_one($sql);
                    $userArr = $B->getUsers($privilege_sysno);
                break;
            case 3:
                if($info['stockinno'])
                {
                    $order  = "管入库订单";
                    $privilegename = "管入库";
                    $sql = "SELECT sysno from hengyang_system_privilege where privilegename = '{$privilegename}'";
                    $privilege_sysno = $this->dbh->select_one($sql);

                    $action = "list"; 
                    $control = "Stockpipein";
                    //根据父级获取权限用户
                    $sql = "SELECT sysno from hengyang_system_privilege where privilegecontroller like '%".$control."%' and privilegeaction like '%".$action."' and parent_sysno=".intval($privilege_sysno);
                    $privilege_sysno = $this->dbh->select_one($sql);
                    $userArr = $B->getUsers($privilege_sysno);
                }else{
                    $order = "管入库预约单";
                    $privilegename = "管入库预约";
                    $sql = "SELECT sysno from hengyang_system_privilege where privilegename = '{$privilegename}'";
                    $privilege_sysno = $this->dbh->select_one($sql);

                    $action = "audit"; 
                    $control = "bookpipelinein";
                    //根据父级获取权限用户
                    $sql = "SELECT sysno from hengyang_system_privilege where privilegecontroller like '%".$control."%' and privilegeaction like '%".$action."' and parent_sysno=".intval($privilege_sysno);
                    $privilege_sysno = $this->dbh->select_one($sql);
                    $userArr = $B->getUsers($privilege_sysno);
                }
                break;
            case 4:
                if($info['stockinno'])
                {
                    $order = "靠泊装货订单";
                    $privilegename = "靠泊装货";
                    $sql = "SELECT sysno from hengyang_system_privilege where privilegename = '{$privilegename}'";
                    $privilege_sysno = $this->dbh->select_one($sql);

                    $action = "list"; 
                    $control = "stockberthin";
                    //根据父级获取权限用户
                    $sql = "SELECT sysno from hengyang_system_privilege where privilegecontroller like '%".$control."%' and privilegeaction like '%".$action."' and parent_sysno=".intval($privilege_sysno);
                    $privilege_sysno = $this->dbh->select_one($sql);
                    $userArr = $B->getUsers($privilege_sysno);
                }else{
                    $order = "靠泊装货预约单";
                    $privilegename = "靠泊装货预约";
                    $sql = "SELECT sysno from hengyang_system_privilege where privilegename = '{$privilegename}'";
                    $privilege_sysno = $this->dbh->select_one($sql);

                    $action = "list"; 
                    $control = "bookberthin";
                    //根据父级获取权限用户
                    $sql = "SELECT sysno from hengyang_system_privilege where privilegecontroller like '%".$control."%' and privilegeaction like '%".$action."' and parent_sysno=".intval($privilege_sysno);
                    $privilege_sysno = $this->dbh->select_one($sql);
                    $userArr = $B->getUsers($privilege_sysno);
                }
                break;
        }

        // var_dump($info);exit;
        
        $customername = $info['customer_name']? $info['customer_name'] : $info['customername'];
        $inno = $info['bookinginno']? $info['bookinginno'] : $info['stockinno'];
        $content = $customername.'的'.$order.$inno."需要审核";

        foreach($userArr as $value){
            $input = array(
                'send_from_id'=>$user['sysno'],
                'send_from_name'=>$user['username'],
                'send_to_id'=>$value['user_sysno'],
                'viewstatus'=>1,
                'subject'=>$order.'待审核',
                'content'=>$content,
                'message_type'=>1,
                'replyid'=>'',
                'created_at'=>'=NOW()',
                'updated_at'=>'=NOW()',
                'action'=>$action,
                'control'=>$control,
                'doc_sysno'=>$info['sysno'],
            );
            $res = $this->dbh->insert(DB_PREFIX.'doc_message', $input);
            if(!$res)
            {
                return false;
            }
        }

        return true;
    }

}
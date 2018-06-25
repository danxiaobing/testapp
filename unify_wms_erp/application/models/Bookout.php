<?php
/**
 * Stockout Model
 *
 */

class BookoutModel
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
     * @param   object  $dbh
     * @param   object  $mch
     * @return  void
     */
    public function __construct($dbh, $mch)
    {
        $this->dbh = $dbh;

        $this->mch = $mch;
    }

    public function searchBookout($params) {
        $filter = array();
        if (isset($params['bookingoutno']) && $params['bookingoutno'] != '') {
            $filter[] = " s.`bookingoutno` LIKE '%{$params['bookingoutno']}%' ";
        }
        if (isset($params['bookingoutdate']) && $params['bookingoutdate'] != '') {
            $filter[] = " s.`bookingoutdate` = '{$params['bookingoutdate']}' ";
        }
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " s.`customer_name` LIKE '%{$params['bar_name']}%'";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '') {
            $filter[] = " s.`bookingoutstatus`='{$params['bar_status']}'";
        }
        if (isset($params['stockouttype']) && $params['stockouttype'] != '') {
            $filter[] = " s.`stockouttype`='{$params['stockouttype']}'";
        }
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " s.`bookingoutdate`>='{$params['begin_time']}'";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " s.`bookingoutdate`<='{$params['end_time']}'";
        }
        if (isset($params['bar_docsource']) && $params['bar_docsource'] != '') {
            $filter[] = " s.`docsource`='{$params['bar_docsource']}'";
        }
        if (isset($params['bar_receivenumber']) && $params['bar_receivenumber'] != '') {
            $filter[] = " s.`receivenumber` like '%{$params['bar_receivenumber']}%'";
        }


        $where ='s.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_booking_out` s  where {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);
        $data['totalRow'] = $result['totalRow'];
        $data['list'] = array();
        if ($result['totalRow']) {
            $sql = "SELECT s.*  FROM `".DB_PREFIX."doc_booking_out` s where {$where}";
            if ($params['orders'] != '') {
                $sql .= " order by " . $params['orders'];
            }
            else
            {
                $sql .= " order by created_at desc";
            }
            $list = $this->dbh->select($sql);

            $dData = array();
            if($list){
                foreach ($list as $key=>$row){
                    if(isset($params['bar_goodsname']) && $params['bar_goodsname'] != ''){
                        $subsql = "select *,sum(bookingoutqty) as  total  FROM `".DB_PREFIX."doc_booking_out_detail` where bookingout_sysno = '{$row['sysno']}' and goodsname like '%{$params['bar_goodsname']}%'";
                    }else{
                        $subsql = "select *,sum(bookingoutqty) as  total ,sum(noticenum) as bookingoutqty ,sum(untakegoodsnum) as bookuntakegoodsnum ,sum(takegoodsqty) as booktakegoodsqty FROM `".DB_PREFIX."doc_booking_out_detail` where bookingout_sysno = '{$row['sysno']}'";
                    }
                    $extendRow  =  $this->dbh->select_row($subsql);

                    $row['storagetankname'] =  $extendRow['storagetankname'];
                    $row['goodsname'] =  $extendRow['goodsname'];
                    $row['unitname'] =  $extendRow['unitname'];
                    $row['goodsnature'] =  $extendRow['goodsnature'];
                    $row['qualityname'] =  $extendRow['qualityname'];
                    $row['bookingoutqty'] =  $extendRow['total'];
                    $row['shipname'] =  $extendRow['shipname'];
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
    public function searchBookoutForApi($params) {
        $filter = array();
        if (isset($params['bookingoutno']) && $params['bookingoutno'] != '') {
            $filter[] = " s.`bookingoutno` LIKE '%{$params['bookingoutno']}%' ";
        }
        if (isset($params['bookingoutdate']) && $params['bookingoutdate'] != '') {
            $filter[] = " s.`bookingoutdate` = '{$params['bookingoutdate']}' ";
        }
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " s.`customer_name` LIKE '%{$params['bar_name']}%'";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '') {
            $filter[] = " s.`bookingoutstatus`='{$params['bar_status']}'";
        }
        if (isset($params['stockouttype']) && $params['stockouttype'] != '') {
            $filter[] = " s.`stockouttype`='{$params['stockouttype']}'";
        }
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " s.`bookingoutdate`>='{$params['begin_time']}'";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " s.`bookingoutdate`<='{$params['end_time']}'";
        }
        if (isset($params['bar_docsource']) && $params['bar_docsource'] != '') {
            $filter[] = " s.`docsource`='{$params['bar_docsource']}'";
        }
        if (isset($params['bar_receivenumber']) && $params['bar_receivenumber'] != '') {
            $filter[] = " s.`receivenumber` like '%{$params['bar_receivenumber']}%'";
        }


        $where ='s.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_booking_out` s  where {$where} ";

        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);
        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT s.*  FROM `".DB_PREFIX."doc_booking_out` s where {$where}";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by created_at desc";
                }
                $list = $this->dbh->select($sql);
            } else{
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);
                $sql = "SELECT s.*  FROM `".DB_PREFIX."doc_booking_out` s where {$where}";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by created_at desc";
                }
                $list = $this->dbh->select_page($sql);
            }
            foreach ($list as $key=>$row){
                if(isset($params['bar_goodsname']) && $params['bar_goodsname'] != ''){
                    $subsql = "select *,sum(bookingoutqty) as  total  FROM `".DB_PREFIX."doc_booking_out_detail` where bookingout_sysno = '{$row['sysno']}' and goodsname like '%{$params['bar_goodsname']}%'";
                }else{
                    $subsql = "select *,sum(bookingoutqty) as  total ,sum(noticenum) as bookingoutqty ,sum(untakegoodsnum) as bookuntakegoodsnum ,sum(takegoodsqty) as booktakegoodsqty FROM `".DB_PREFIX."doc_booking_out_detail` where bookingout_sysno = '{$row['sysno']}'";
                }
                $extendRow  =  $this->dbh->select_row($subsql);
                //$row['storagetankname'] =  $extendRow['storagetankname'];
                $row['goodsname'] =  $extendRow['goodsname'];
                $row['unitname'] =  $extendRow['unitname'];
                $row['goodsnature'] =  $extendRow['goodsnature'];
                $row['qualityname'] =  $extendRow['qualityname'];
                $row['bookingoutqty'] =  $extendRow['total'];
                $row['shipname'] =  $extendRow['shipname'];

                $result['list'][$key] = $row;
            }
        }
        return $result;
    }

    public function getBookoutDetailList($params){

        $filter = array();
        if (isset($params['bookingout_sysno']) && $params['bookingout_sysno'] != '') {
            $filter[] = " s.`bookingout_sysno` = '".$params['bookingout_sysno']."' ";
        }
        $where ='s.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT COUNT(*) FROM `".DB_PREFIX."doc_booking_out_detail` s
                         LEFT JOIN ".DB_PREFIX."base_storagetank t ON t.sysno = s.storagetank_sysno
                         where {$where}  ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);
        $result['list'] = array();
        if ($result['totalRow']) {
            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "SELECT s.*,ss.stockno,t.storagetankname FROM `".DB_PREFIX."doc_booking_out_detail` s
                        LEFT JOIN ".DB_PREFIX."base_storagetank t ON t.sysno = s.storagetank_sysno
                        LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = s.stock_sysno
                         where {$where}  ";
                if($params['orders'] != '')
                    $sql .= " order by s.".$params['orders'] ;
                $result['list'] = $this->dbh->select($sql);
            }else{
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT s.*,ss.stockno,t.storagetankname FROM `".DB_PREFIX."doc_booking_out_detail` s
                        LEFT JOIN ".DB_PREFIX."base_storagetank t ON t.sysno = s.storagetank_sysno
                        LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = s.stock_sysno
                         where {$where}  ";
                if ($params['orders'] != '') {
                    $sql .= " order by s." . $params['orders'];
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    /*
     * 新增船出库查询船出库预约详情
     * @author wu xianneng
     */
    public function getBookDetail($params){
        $filter = array();
        if (isset($params['bookingout_sysno']) && $params['bookingout_sysno'] != '') {
            $filter[] = " bod.`bookingout_sysno` = '".$params['bookingout_sysno']."' ";
        }
        $where ='WHERE bod.isdel = 0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT bod.*,bod.storagetank_sysno as storagetank_sysno,bod.inshipname as stockinshipname,ss.stockno,ss.stockqty,bs.storagetankname FROM
                ".DB_PREFIX."doc_booking_out_detail bod
                LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = bod.stock_sysno
                LEFT JOIN ".DB_PREFIX."base_storagetank bs ON bs.sysno = bod.storagetank_sysno  {$where}  ";

        return  $this->dbh->select($sql);

    }

    public function searchBookoutCar($params){

        $filter = array();
        if (isset($params['bookingout_sysno']) && $params['bookingout_sysno'] != '') {
            $filter[] = " s.`bookingout_sysno` = '".$params['bookingout_sysno']."' ";
        }
        $where ='s.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_booking_out_cars` s  where {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "SELECT  s.* FROM `".DB_PREFIX."doc_booking_out_cars` s  where {$where} ";
                if($params['orders'] != '')
                    $sql .= " order by ".$params['orders'] ;
                $result['list'] = $this->dbh->select($sql);
            }else{
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT s.* FROM `".DB_PREFIX."doc_booking_out_cars` s  where {$where}  ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    public function getBookoutCarByid($id){
        $sql = "select * from `".DB_PREFIX."doc_booking_out_cars` where bookingout_sysno = ".intval($id);
        return $this->dbh->select($sql);
    }

    public function getBookoutById($id){
        $sql = "select * from `".DB_PREFIX."doc_booking_out` where sysno= ".intval($id);

        return $this->dbh->select_row($sql);
    }

    public function addCarBookout($data,$detaildata,$cardata)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->insert(DB_PREFIX.'doc_booking_out', $data);

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $id = $res;
            foreach ($detaildata as $value) {
                $input = array(
                    'bookingout_sysno' => $id,
                    'stock_sysno' => $value['stock_sysno'],
                    'bookingoutqty' => $value['bookingoutqty'],
                    'memo' => $value['memo'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                    'goodsname' => $value['goodsname'],
                    'goodsnature' => $value['goodsnature'],
                    'stockin_sysno' => $value['stockin_sysno'],
                    'stockin_no' => $value['stockin_no'],
                    'qualityname' => $value['qualityname'],
                    'unitname' => $value['unitname'],
                    'storagetank_sysno' => $value['storagetank_sysno'],
                    'goods_sysno' => $value['goods_sysno'],
                    'stocktype' => $value['stocktype'],
                    'inshipname' => $value['inshipname']
                );
                $res = $this->dbh->insert(DB_PREFIX.'doc_booking_out_detail', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            if(count($cardata) > 0){
                foreach ($cardata as $value) {
                    $input = array(
                        'bookingout_sysno' => $id,
                        'carname' => $value['carname'],
                        'mobilephone' => $value['mobilephone'],
                        'idcard' => $value['idcard'],
                        'carid' => $value['carid'],
                        'carmarks' => $value['carmarks'],
                        'cartakeqty' => $value['cartakeqty'],
                        'status' => 1,
                        'isdel' => 0,
                        'version' => 1,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()'
                    );

                    $res = $this->dbh->insert(DB_PREFIX.'doc_booking_out_cars', $input);

                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
            }

            $user = Yaf_Registry::get(SSN_VAR);
            #库存管理业务操作日志
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  => $id,
                'doctype'  => 2,
                'opertype'  => 0 ,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  '',
              );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if ($data['bookingoutstatus'] == 4) {
                $sql = "SELECT sysno from hengyang_system_privilege where privilegename = '车出库预约'";
                $privilege_sysno = $this->dbh->select_one($sql);

                $sql = "SELECT sysno from hengyang_system_privilege where privilegecontroller like '%bookout%' and privilegeaction like '%examjson%' and parent_sysno=".intval($privilege_sysno);
                $privilege_sysno = $this->dbh->select_one($sql);
                $userArr = $this->getUsers($privilege_sysno);
                if(count($userArr)>0){
                    $content = $data['customer_name']."的车出库预约单".$data['bookingoutno']."待审核";
                    foreach ($userArr as $uvalue) {
                        $messageInput = array(
                            'send_from_id'=>$user['sysno'],
                            'send_from_name'=>$user['username'],
                            'send_to_id'=>$uvalue['user_sysno'],
                            'viewstatus'=>1,
                            'subject'=>'车出库预约待审核',
                            'content'=>$content,
                            'message_type'=>1,
                            'replyid'=>'',
                            'created_at'=>'=NOW()',
                            'updated_at'=>'=NOW()',
                            'action'=>'caraudit',
                            'control'=>'bookout',
                            'doc_sysno'=>$id,
                        );
                        $S ->addmessage($messageInput);
                    }
                }
                $input['opertype'] = 2;
            }else{
                $input['opertype'] = 1;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            // #释放锁
            // $this->dbh->unlock();事务中不需要unlock
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    public function updateCarBookout($id,$data,$detaildata,$cardata)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_booking_out', $data, 'sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $res = $this->dbh->delete(DB_PREFIX.'doc_booking_out_detail', 'bookingout_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            foreach ($detaildata as $value) {
                $input = array(
                    'bookingout_sysno' => $id,
                    'stock_sysno' => $value['stock_sysno'],
                    'bookingoutqty' => $value['bookingoutqty'],
                    'memo' => $value['memo'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                    'goodsname' => $value['goodsname'],
                    'goodsnature' => $value['goodsnature'],
                    'stockin_sysno' => $value['stockin_sysno'],
                    'stockin_no' => $value['stockin_no'],
                    'qualityname' => $value['qualityname'],
                    'unitname' => $value['unitname'],
                    'storagetank_sysno' => $value['storagetank_sysno'],
                    'goods_sysno' => $value['goods_sysno'],
                    'stocktype' => $value['stocktype'],
                    'inshipname' => $value['inshipname']
                );

                $res = $this->dbh->insert(DB_PREFIX.'doc_booking_out_detail', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            $res = $this->dbh->delete(DB_PREFIX.'doc_booking_out_cars', 'bookingout_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if(count($cardata) > 0){
                foreach ($cardata as $value) {
                    $input = array(
                        'bookingout_sysno' => $id,
                        'carname' => $value['carname'],
                        'mobilephone' => $value['mobilephone'],
                        'idcard' => $value['idcard'],
                        'carid' => $value['carid'],
                        'carmarks' => $value['carmarks'],
                        'cartakeqty' => $value['cartakeqty'],
                        'status' => 1,
                        'isdel' => 0,
                        'version' => 1,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()'
                    );

                    $res = $this->dbh->insert(DB_PREFIX.'doc_booking_out_cars', $input);

                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }

            }

            $user = Yaf_Registry::get(SSN_VAR);
            #库存管理业务操作日志
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  2,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  '',
            );

            if ($data['bookingoutstatus'] == 4) {
                $sql = "SELECT sysno from hengyang_system_privilege where privilegename = '车出库预约'";
                $privilege_sysno = $this->dbh->select_one($sql);

                $sql = "SELECT sysno from hengyang_system_privilege where privilegecontroller like '%bookout%' and privilegeaction like '%examjson%' and parent_sysno=".intval($privilege_sysno);
                $privilege_sysno = $this->dbh->select_one($sql);
                $userArr = $this->getUsers($privilege_sysno);
                if(count($userArr)>0){
                    $content = $data['customer_name']."的车出库预约单".$data['bookingoutno']."待审核";
                    foreach ($userArr as $uvalue) {
                        $messageInput = array(
                            'send_from_id'=>$user['sysno'],
                            'send_from_name'=>$user['username'],
                            'send_to_id'=>$uvalue['user_sysno'],
                            'viewstatus'=>1,
                            'subject'=>'车出库预约待审核',
                            'content'=>$content,
                            'message_type'=>1,
                            'replyid'=>'',
                            'created_at'=>'=NOW()',
                            'updated_at'=>'=NOW()',
                            'action'=>'caraudit',
                            'control'=>'bookout',
                            'doc_sysno'=>$id,
                        );
                        $S ->addmessage($messageInput);
                    }
                }
                $input['opertype'] = 2;
            }else{
                $input['opertype'] = 1;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            // #释放锁
            // $this->dbh->unlock();事务中不需要unlock
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    //判断提货区间
    public function checkTimeRange($detailData,$receivestart,$receiveend)
    {
        $timeRange = array();
        $receivestart = strtotime($receivestart);
        $receiveend = strtotime($receiveend);

        foreach ($detailData as $key => $value) {
            if($value['stocktype'] == 2){
                $sql = "SELECT receivestart,receiveend from `".DB_PREFIX."doc_introduction` i left join `".DB_PREFIX."doc_introduction_detail` id on i.sysno = id.introduction_sysno where i.introductiontype = 1 and id.sysno = " . intval($value['stock_sysno']);
                $introduceInfo = $this->dbh->select_row($sql);
                if($introduceInfo){
                    if($introduceInfo['receivestart'] != '0000-00-00' && $introduceInfo['receiveend'] != '0000-00-00'){
                        $timeRange['start'][] = $introduceInfo['receivestart'];
                        $timeRange['end'][] = $introduceInfo['receiveend'];
                    }
                }
            }
        }

        if(!empty($timeRange)){
            $startTime = strtotime($timeRange['start'][0]);
            for ($i=1; $i<count($timeRange['start'])  ; $i++) {
                if($startTime < strtotime($timeRange['start'][$i])){
                    $startTime = strtotime($timeRange['start'][$i]);
                }
            }
            $endTime = strtotime($timeRange['end'][0]);
            for ($i=1; $i<count($timeRange['end'])  ; $i++) {
                if($endTime > strtotime($timeRange['end'][$i])){
                    $endTime = strtotime($timeRange['end'][$i]);
                }
            }

            if($startTime > $endTime){
                return array('code' => '300' , 'msg' => '出库明细没有公共提货区间不能提交');
            }

            if($receivestart < $startTime){
                return array('code' => '300' , 'msg' => '提货开始时间不能小于提单公共区间开始时间');
            }

            if($receivestart > $endTime && $endTime){
                return array('code' => '300' , 'msg' => '提货开始时间不能大于提单公共区间结束时间');
            }

            if($receiveend > $endTime){
                return array('code' => '300' , 'msg' => '提货结束时间不能大于提单公共区间结束时间');
            }
        }
    }

    public function addShipBookout($data,$detaildata)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->insert(DB_PREFIX.'doc_booking_out', $data);

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $id = $res;
            foreach ($detaildata as $value) {
                $input = array(
                    'bookingout_sysno' => $id,
                    'stock_sysno' => $value['stock_sysno'],
                    'bookingoutqty' => $value['bookingoutqty'],
                    'shipokdate' => $value['shipokdate'],
                    'memo' => $value['memo'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                    'shipname' => $value['shipname'],
                    'goodsname' => $value['goodsname'],
                    'goodsnature' => $value['goodsnature'],
                    'stockin_sysno' => $value['stockin_sysno'],
                    'stockin_no' => $value['stockin_no'],
                    'qualityname' => $value['qualityname'],
                    'unitname' => $value['unitname'],
                    'storagetank_sysno' => $value['storagetank_sysno'],
                    'goods_sysno' => $value['goods_sysno'],
                    'stocktype' => $value['stocktype'],
                );

                $res = $this->dbh->insert(DB_PREFIX.'doc_booking_out_detail', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            $user = Yaf_Registry::get(SSN_VAR);
            #库存管理业务操作日志
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  2,
                'opertype'  => 0 ,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  '',
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if ($data['bookingoutstatus'] == 4) {
                $sql = "SELECT sysno from hengyang_system_privilege where privilegename = '船出库预约'";
                $privilege_sysno = $this->dbh->select_one($sql);

                $sql = "SELECT sysno from hengyang_system_privilege where privilegecontroller like '%bookout%' and privilegeaction like '%examjson%' and parent_sysno=".intval($privilege_sysno);
                $privilege_sysno = $this->dbh->select_one($sql);
                $userArr = $this->getUsers($privilege_sysno);
                if(count($userArr)>0){
                    $content = $data['customer_name']."的船出库预约单".$data['bookingoutno']."待审核";
                    foreach ($userArr as $uvalue) {
                        $messageInput = array(
                            'send_from_id'=>$user['sysno'],
                            'send_from_name'=>$user['username'],
                            'send_to_id'=>$uvalue['user_sysno'],
                            'viewstatus'=>1,
                            'subject'=>'船出库预约待审核',
                            'content'=>$content,
                            'message_type'=>1,
                            'replyid'=>'',
                            'created_at'=>'=NOW()',
                            'updated_at'=>'=NOW()',
                            'action'=>'shipaudit',
                            'control'=>'bookout',
                            'doc_sysno'=>$id,
                        );
                        $S ->addmessage($messageInput);
                    }
                }
                $input['opertype'] = 2;
            }else{
                $input['opertype'] = 1;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            // #释放锁
            // $this->dbh->unlock();事务中不需要unlock
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    public function updateShipBookout($id,$data,$detaildata)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_booking_out', $data, 'sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $res = $this->dbh->delete(DB_PREFIX.'doc_booking_out_detail', 'bookingout_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            foreach ($detaildata as $value) {
                $input = array(
                    'bookingout_sysno' => $id,
                    'stock_sysno' => $value['stock_sysno'],
                    'bookingoutqty' => $value['bookingoutqty'],
                    'shipokdate' => $value['shipokdate'],
                    'shipname' => $value['shipname'],
                    'memo' => $value['memo'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                    'goodsname' => $value['goodsname'],
                    'goodsnature' => $value['goodsnature'],
                    'stockin_sysno' => $value['stockin_sysno'],
                    'stockin_no' => $value['stockin_no'],
                    'qualityname' => $value['qualityname'],
                    'unitname' => $value['unitname'],
                    'storagetank_sysno' => $value['storagetank_sysno'],
                    'goods_sysno' => $value['goods_sysno'],
                    'stocktype' => $value['stocktype'],
                );

                $res = $this->dbh->insert(DB_PREFIX.'doc_booking_out_detail', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            $user = Yaf_Registry::get(SSN_VAR);
            #库存管理业务操作日志
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  2,
                // 'opertype'  => 2 ,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  '',
            );
            if ($data['bookingoutstatus'] == 4) {
                $sql = "SELECT sysno from hengyang_system_privilege where privilegename = '船出库预约'";
                $privilege_sysno = $this->dbh->select_one($sql);

                $sql = "SELECT sysno from hengyang_system_privilege where privilegecontroller like '%bookout%' and privilegeaction like '%examjson%' and parent_sysno=".intval($privilege_sysno);
                $privilege_sysno = $this->dbh->select_one($sql);
                $userArr = $this->getUsers($privilege_sysno);
                if(count($userArr)>0){
                    $content = $data['customer_name']."的船出库预约单".$data['bookingoutno']."待审核";
                    foreach ($userArr as $uvalue) {
                        $messageInput = array(
                            'send_from_id'=>$user['sysno'],
                            'send_from_name'=>$user['username'],
                            'send_to_id'=>$uvalue['user_sysno'],
                            'viewstatus'=>1,
                            'subject'=>'船出库预约待审核',
                            'content'=>$content,
                            'message_type'=>1,
                            'replyid'=>'',
                            'created_at'=>'=NOW()',
                            'updated_at'=>'=NOW()',
                            'action'=>'shipaudit',
                            'control'=>'bookout',
                            'doc_sysno'=>$id,
                        );
                        $S ->addmessage($messageInput);
                    }
                }
                $input['opertype'] = 2;
            }else{
                $input['opertype'] = 1;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            // #释放锁
            // $this->dbh->unlock();事务中不需要unlock
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    public function updateBookoutData($id,$data){
        $this->dbh->begin();
        try{
            $sql = "select * from ".DB_PREFIX."doc_booking_out_detail where bookingout_sysno =".intval($id);
            $res = $this->dbh->select($sql);
            if(!$res){
                return false;
            }

            $sql = "select * from ".DB_PREFIX."doc_booking_out where sysno =".intval($id);
            $bookoutInfo = $this->dbh->select_row($sql);
            if(!$bookoutInfo){
                return false;
            }

            if (isset($data['examidentify']) && $data['examidentify'] == 'back') {
                $updateData = array(
                    'isdel' => 1,
                    );
                if($bookoutInfo['stockouttype'] == 1){
                    $type = 7;
                }elseif($bookoutInfo['stockouttype'] == 3){
                    $type = 11;
                }
                if($bookoutInfo['stockouttype'] == 1 || $bookoutInfo['stockouttype'] == 3){
                    if($bookoutInfo['ispipelineorder'] == '1'){
                        $pres = $this->dbh->update(DB_PREFIX.'doc_pipelineorder',$updateData,'businesstype = '.$type.' and booking_sysno ='.intval($id));

                        if (!$pres) {
                            $this->dbh->rollback();
                            return false;
                        }
                    }

                    if($bookoutInfo['isberthorder'] == '1'){
                        $bres = $this->dbh->update(DB_PREFIX.'doc_berthorder',$updateData,'businesstype = '.$type.' and booking_sysno =' .intval($id));
                        if (!$bres) {
                            $this->dbh->rollback();
                            return false;
                        }
                    }

                    if($bookoutInfo['isqualitycheck'] == '1'){
                        if($bookoutInfo['stockouttype'] == 1){
                            $type = 6;
                        }elseif($bookoutInfo['stockouttype'] == 3){
                            $type = 8;
                        }
                        $qres = $this->dbh->update(DB_PREFIX.'doc_qualitycheck',$updateData,'businesstype = '.$type.' and booking_sysno =' .intval($id));
                        if (!$qres) {
                            $this->dbh->rollback();
                            return false;
                        }
                    }
                }

            }
            $bookingoutqty = 0;
            $SG = new StoragetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $S = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

            foreach ($res as $key => $value) {
                if (isset($data['examidentify']) && $data['examidentify'] == 'back') {
                    $bookingoutqty = $value['bookingoutqty'];
                }

                //更新库存锁货量
                if($value['stocktype'] == 1){
                    //更新储罐待出量
                    $updatesgres = $SG ->pubstoragetankoperation(
                        array(
                            'type' => 9,
                            'data' => array(
                                'sysno' => $value['storagetank_sysno'],
                                'orderoutqty' => $bookingoutqty,
                            ),
                         ));

                    if($updatesgres['code'] != '200'){
                        $this->dbh->rollback();
                        $msg = '更新罐容失败';
                        return false;
                    }
                    $updatestock = $S->pubstockoperation(
                        array(
                            'type' => 13,
                            'data' => array(
                                'sysno' => $value['stock_sysno'],
                                'clockqty' => $bookingoutqty,
                            ),
                        ));

                    if($updatestock['code'] != '200'){
                        $this->dbh->rollback();
                        $msg = '更新库容失败';
                        return false;
                    }
                }elseif ($value['stocktype'] == 2) {
                    $sql = "SELECT bookingqty,introductiondetailstatus from `".DB_PREFIX."doc_introduction_detail` where sysno = ".intval($value['stockin_sysno']);
                    $introduceInfo = $this->dbh->select_row($sql);
                    if($introduceInfo['introductiondetailstatus'] == 7){
                        $this->dbh->rollback();
                        $msg = '提单已撤销，禁止退回';
                        return false;
                    }

                    $bookingqty = $introduceInfo['bookingqty'] - $bookingoutqty;
                    $res = $this->dbh->update(DB_PREFIX."doc_introduction_detail",array('bookingqty' => $bookingqty),'sysno='.intval($value['stockin_sysno']));
                    if(!$res){
                        $msg = "更新介绍信预约量失败";
                        $this->dbh->rollback();
                        return false;
                    }
                }


            }
            unset($data['examidentify']);
            unset($data['type']);
            $res = $this->dbh->update(DB_PREFIX.'doc_booking_out', $data, 'sysno=' . intval($id));
            if(!$res){
                $this->dbh->rollback();
                return false;
            }
            $this->dbh->commit();
            return $id;
        }catch (Exception $e) {
            $this->dbh->rollback();
            $msg = '数据库操作异常';
            return false;
        }
    }

    public function updateBookoutDetaiData($id)
    {
        $this->dbh->begin();
        try{
            $res = $this->dbh->update(DB_PREFIX.'doc_booking_out', array('isdel' => 1), 'sysno=' . intval($id));
            if(!$res){
                $this->dbh->rollback();
                return false;
            }

            $res = $this->dbh->update(DB_PREFIX.'doc_booking_out_detail', array('isdel' => 1), 'bookingout_sysno=' . intval($id));
            if(!$res){
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $id;
        }catch (Exception $e) {
            $this->dbh->rollback();
            $msg = '数据库操作异常';
            return false;
        }

    }

    //云仓订单驳回
    public function bookoutReject($id,$data)
    {
        return $this->dbh->update(DB_PREFIX.'doc_booking_out', $data, 'sysno=' . intval($id));
    }

    public function examBookout($id,&$msg = null, $auditreason = null){
        $S = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $ST = new StoragetankModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        $this->dbh->begin();
        try{
            $sql = 'select * from `'.DB_PREFIX.'doc_booking_out` where sysno=' . intval($id) ." for update";
            $data = $this->dbh->select_row($sql);

            if (!$data) {
                $msg = '数据为空';
                $this->dbh->rollback();
                return false;
            }

            if($data['bookingoutstatus'] != '4'){
                $msg = '预约单不是提交状态，不能审核';
                $this->dbh->rollback();
                return false;
            }

            $param = array(
                'bookingoutstatus' => 5,
                'auditreason' => $auditreason
            );
            $res = $this->dbh->update(DB_PREFIX.'doc_booking_out', $param, 'sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                $msg = '数据更新出错';
                return false;
            }

            $sql = 'select * from `'.DB_PREFIX.'doc_booking_out_detail` where bookingout_sysno=' . intval($id);
            $detaildata = $this->dbh->select($sql);

            if (!$detaildata) {
                $this->dbh->rollback();
                $msg = '列表详情为空';
                return false;
            }

            foreach ($detaildata as $value) {
                if($value['stocktype'] == 1){
                    //更新罐子待出量
                    $storagetankdata = array(
                        'sysno' => $value['storagetank_sysno'],
                        'orderoutqty' => $value['bookingoutqty']
                    );
                    $params['data'] = $storagetankdata;
                    $params['type'] = 10;
                    $storagetankres = $ST->pubstoragetankoperation($params);

                    if ($storagetankres['code'] !='200') {
                        $msg = $storagetankres['message'];
                        $this->dbh->rollback();
                        return false;
                    }
                    //更新库存预约锁定量
                    $params = array('type'=>8);
                    $stockdata = array(
                        'sysno' => $value['stock_sysno'],
                        'clockqty' => $value['bookingoutqty']

                    );

                    $params['data'] = $stockdata;
                    $stockres = $S->pubstockoperation($params);

                    if ($stockres['code'] !='200') {
                        $msg = $stockres['message'];
                        $this->dbh->rollback();
                        return false;
                    }
                }elseif ($value['stocktype'] == 2) {
                    $sql = "SELECT bookingqty,untakegoodsnum,introductiondetailstatus from `".DB_PREFIX."doc_introduction_detail` where sysno = ".intval($value['stock_sysno']);
                    $introduceInfo = $this->dbh->select_row($sql);
                    if($introduceInfo['introductiondetailstatus'] == 7){
                        $msg = '提单已撤销，无法审核通过';
                        $this->dbh->rollback();
                        return false;
                    }

                    $bookingqty = $introduceInfo['bookingqty'] + $value['bookingoutqty'];
                    $res = $this->dbh->update(DB_PREFIX."doc_introduction_detail",array('bookingqty' => $bookingqty),'sysno='.intval($value['stockin_sysno']));
                    if(!$res){
                        $msg = "更新介绍信信息失败";
                        $this->dbh->rollback();
                        return false;
                    }
                }
            }

            if($data['stockouttype'] == 2){
                //获取车预约单车辆信息
                $SO = new StockoutModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
                $stockoutdetaildata = array();
                $stockoutcardata = array();
                $search = array(
                    'bookingout_sysno' => $id,
                    'page' => false
                );

                $carData = $this->searchBookoutCar($search);

                $input = array(
                    'stockouttype' => $data['stockouttype'],
                    'stockoutno' => $data['bookingoutno'],
                    'stockoutdate' => $data['bookingoutdate'],
                    'customer_sysno' => $data['customer_sysno'],
                    'customername' => $data['customer_name'],
                    'booking_out_sysno' => $id,
                    'bookingoutno' => $data['bookingoutno'],
                    'cs_employee_sysno' => $data['cs_employee_sysno'],
                    'cs_employeename' => $data['cs_employeename'],
                    'stockoutstatus' => 3,
                    'takegoodsno' => $data['receivenumber'],
                    'receivestart' => $data['receivestart'],
                    'receiveend' => $data['receiveend'],
                    'receiveover' => $data['receiveover'],
                    'takegoodscompany' => $data['receiveunitname'],
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                    'status' => 1,
                    'isdel' => 0,
                );

                $res = $this->dbh->insert(DB_PREFIX.'doc_stock_out', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }

                $stockout_sysno = $res;

                foreach ($detaildata as $value) {
                    $stockoutdetaildata = array(
                        'stockout_sysno' => $stockout_sysno,
                        'stockin_sysno' => $value['stockin_sysno'],
                        'stockinno' => $value['stockin_no'],
                        'goods_sysno' => $value['goods_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'tobeqty' => $value['noticenum'] ? $value['noticenum'] : $value['bookingoutqty'],
                        'beqty' => 0,
                        'takeqty' => $value['noticenum'] ? $value['noticenum'] : $value['bookingoutqty'],
                        'storagetank_sysno' => $value['storagetank_sysno'],
                        'memo' => $value['memo'],
                        'bookout_detail_sysno' => $value['sysno'],
                        'stock_sysno' => $value['stock_sysno'],
                        'goodsname' => $value['goodsname'],
                        'qualityname' => $value['qualityname'],
                        'unitname' => $value['unitname'],
                        'stocktype' => $value['stocktype'],
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                        'inshipname' => $value['inshipname'],
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_stock_out_detail', $stockoutdetaildata);

                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }

                foreach ($carData['list'] as $value) {
                    $stockoutcardata = array(
                        'stockout_sysno' => $stockout_sysno,
                        'bookout_detail_sysno' => $id,
                        'carname' => $value['carname'],
                        'mobilephone' => $value['mobilephone'],
                        'idcard' => $value['idcard'],
                        'carid' => $value['carid'],
                        'carmarks' => $value['carmarks'],
                        'weight' => $value['weight'],
                        'cartakeqty' => $value['cartakeqty'],
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_stock_out_cars', $stockoutcardata);
                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }

                $user = Yaf_Registry::get(SSN_VAR);
                #库存管理业务操作日志
                $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

                $inputLog = array(
                    'doc_sysno' => $stockout_sysno,
                    'doctype'  =>  6,
                    'opertype' => 0,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => '',
                );

                $res = $S->addDocLog($inputLog);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }

                $inputLog['opertype'] = 2;

                $res = $S->addDocLog($inputLog);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
                //生成出库订单后回写预约单状态
                $bookoutData = array(
                    'bookingoutstatus' => '6',
                    'issaveorder' => '1'
                );

                $res = $this->dbh->update(DB_PREFIX.'doc_booking_out', $bookoutData, 'sysno=' . $id);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }
            //生成管线分配单、泊位分配单、品质检查单
            $sql = 'select bo.sysno,bo.bookingoutno,bo.bookingoutdate,bo.ispipelineorder,bo.isberthorder,bo.isqualitycheck,bo.customer_sysno,bo.customer_name,bod.shipname,bo.stockouttype,bod.goodsname,bod.goods_sysno from `'.DB_PREFIX.'doc_booking_out` bo left join `'.DB_PREFIX.'doc_booking_out_detail` bod on bo.sysno = bod.bookingout_sysno where bo.sysno=' . intval($id);

            $data = $this->dbh->select_row($sql);
            if($data['stockouttype'] == 1 || $data['stockouttype'] == 3){
                $type = $data['stockouttype'] == 1 ? 7 : 11;
                $res = $this->createPBQ($data,$type);
                if($res['code'] != 200){
                    $msg = $res['msg'];
                    $this->dbh->rollback();
                    return false;
                }
            }
            $this->dbh->commit();
            return $id;
        }catch (Exception $e) {
            $this->dbh->rollback();
            $msg = '数据库操作异常';
            return false;
        }
    }

    /**
     * 生成管线分配单、泊位分配单、品质检查单
     * @author zhaoshiyu
     */
    public function createPBQ($bookoutinfo,$type)
    {
        //管线分配单
        if($bookoutinfo['ispipelineorder'] == 1){
            $gdata = array(
                'pipelineorderno' => COMMON::getCodeId('G'),
                'businesstype' => $type,
                'booking_sysno' => $bookoutinfo['sysno'],
                'bookingno' => $bookoutinfo['bookingoutno'],
                'bookingdate' => $bookoutinfo['bookingoutdate'],
                'applydate' => '=NOW()',
                'orderstatus' => 2,
                'status' => 1,
                'isdel' => 0,
                'version' => 1,
                'created_at' => '=NOW()',
                'updated_at' => '=NOW()',
                );
            $res = $this->dbh->insert(DB_PREFIX.'doc_pipelineorder',$gdata);
            if(!$res){
                return array('code' => 300,'msg' => '创建管道分配单失败');
            }

        }
        //泊位分配单
        if($bookoutinfo['isberthorder'] == 1){
            $pdata = array(
                'berthorderno' => COMMON::getCodeId('P'),
                'businesstype' => $type,
                'booking_sysno' => $bookoutinfo['sysno'],
                'bookingno' => $bookoutinfo['bookingoutno'],
                'bookingdate' => $bookoutinfo['bookingoutdate'],
                'applydate' => '=NOW()',
                'orderstatus' => 2,
                'status' => 1,
                'isdel' => 0,
                'version' => 1,
                'created_at' => '=NOW()',
                'updated_at' => '=NOW()',
                );
            $res = $this->dbh->insert(DB_PREFIX.'doc_berthorder',$pdata);
            if(!$res){
                return array('code' => 300,'msg' => '创建泊位分配单失败');
            }
        }
        //品质检查单
        if($bookoutinfo['isqualitycheck'] == 1){
            $type = $bookoutinfo['stockouttype'] == 1 ? 6 : 8;
            $jdata = array(
                'qualitycheckno' => COMMON::getCodeId('J'),
                'businesstype' => $type,
                'booking_sysno' => $bookoutinfo['sysno'],
                'bookingno' => $bookoutinfo['bookingoutno'],
                'bookingdate' => $bookoutinfo['bookingoutdate'],
                'carshipname' => $type == 6 ? $bookoutinfo['shipname'] : '管输',
                'customer_sysno' => $bookoutinfo['customer_sysno'],
                'customername' => $bookoutinfo['customer_name'],
                'goods_sysno' => $bookoutinfo['goods_sysno'],
                'goodsname' => $bookoutinfo['goodsname'],
                'applydate' => '=NOW()',
                'orderstatus' => 2,
                'status' => 1,
                'isdel' => 0,
                'version' => 1,
                'created_at' => '=NOW()',
                'updated_at' => '=NOW()',
                );
            $res = $this->dbh->insert(DB_PREFIX.'doc_qualitycheck',$jdata);
            if(!$res){
                return array('code' => 300,'msg' => '创建品质检查单失败');
            }
        }
        return array('code' => 200 ,'msg' => '创建成功');

    }

    //根据ID获取出库预约单明细信息
    public function getBookoutDetailById($id = 0)
    {
        if (!$id) {
            COMMON::result(300, '信息有误');
            return false;
        }
        $sql = "select * from ".DB_PREFIX."doc_booking_out_detail where sysno = ".intval($id);
        return $this->dbh->select_row($sql);
    }

    /**
     * 船出库api
     * @param $data
     * @param $bookshipoutdetaildata
     * @return bool
     */
    public function addBookshipoutForApi($data, $bookshipoutdetaildata)
    {
        $this->dbh->begin();
        try {
            $id = $this->dbh->insert(DB_PREFIX.'doc_booking_out', $data);
            if (!$id) {
                $this->dbh->rollback();
                return false;
            }

            //内控损耗率
            // $G = new GoodsModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
            // $info = $G->getratewasteByGoodsName($bookshipindetaildata['goodsname']);
            if (!empty($bookshipoutdetaildata)) {
                $inputs = array(
                    'bookingout_sysno' => $id,
                    'stock_sysno' => $bookshipoutdetaildata['stock_sysno'],
                    'bookingoutqty' => $bookshipoutdetaildata['bookingoutqty'],
                    'shipokdate' => $bookshipoutdetaildata['shipokdate'],
                    'memo' => $bookshipoutdetaildata['memo'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                    'shipname' => $bookshipoutdetaildata['shipname'],
                    'goodsname' => $bookshipoutdetaildata['goodsname'],
                    'goodsnature' => $bookshipoutdetaildata['goodsnature'],
                    'stockin_sysno' => $bookshipoutdetaildata['stockin_sysno'],
                    'stockin_no' => $bookshipoutdetaildata['stockin_no'],
                    'qualityname' => $bookshipoutdetaildata['qualityname'],
                    'unitname' => $bookshipoutdetaildata['unitname'],
                    'storagetank_sysno' => $bookshipoutdetaildata['storagetank_sysno'],
                    'release_no' => $bookshipoutdetaildata['release_no'],
                    'declaration' => $bookshipoutdetaildata['declaration'],
                    'noticenum' => $bookshipoutdetaildata['bookingoutqty'],
                    'untakegoodsnum' => $bookshipoutdetaildata['bookingoutqty'],
                    'goods_sysno' => $bookshipoutdetaildata['goods_sysno'],
                );

                $res = $this->dbh->insert(DB_PREFIX.'doc_booking_out_detail', $inputs);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }
            #库存管理业务操作日志
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 2,
                'opertype' => 0,
                'operemployee_sysno' => '0',
                'operemployeename' => '云仓',
                'opertime' => '=NOW()',
                'operdesc' => '',
            );
            switch ($data['bookingoutstatus']) {
                case 1:
                    $input['opertype'] = 1;
                    $input['operdesc'] = "暂存数据";
                    break;
                case 2:
                    $input['opertype'] = 2;
                    $input['operdesc'] = "已提交";
                    break;
                case 3:
                    $input['opertype'] = 3;
                    $input['operdesc'] = "确认通过";
                    break;
                case 4:
                    $input['opertype'] = 4;
                    $input['operdesc'] = "审核";
                    break;
                case 5:
                    $input['opertype'] = 5;
                    $input['operdesc'] = "审核";
                    break;
                case 6:
                    $input['opertype'] = 6;
                    $input['operdesc'] = "作废";
                    break;
                default:
                    $input['opertype'] = 0;
            }
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            if ($data['bookinginstatus'] >= 2) {
                $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 2,
                    'opertype' => $data['bookingoutstatus'] - 1,
                    'operemployee_sysno' => 0,
                    'operemployeename' => '云仓',
                    'opertime' => '=NOW()',
                    'operdesc' => '',
                );

                $res = $S->addDocLog($input);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }
            $this->dbh->commit();
            return $id;
        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /**
     * api 车出库预约单
     * @param $data 基础数据
     * @param $detaildata 详细数据
     * @param $cardata 预约车数据
     * @return bool
     */
    public function addCarBookoutForApi($data,$detaildata,$cardata)
    {
        $this->dbh->begin();
        try {
            $id = $this->dbh->insert(DB_PREFIX.'doc_booking_out', $data);
            if (!$id) {
                $this->dbh->rollback();
                return false;
            }

            $input = array(
                'bookingout_sysno' => $id,
                'stock_sysno' => $detaildata['stock_sysno'],
                'bookingoutqty' => $detaildata['bookingoutqty'],
                'memo' => $detaildata['memo'],
                'status' => 1,
                'isdel' => 0,
                'version' => 1,
                'created_at' => '=NOW()',
                'updated_at' => '=NOW()',
                'goodsname' => $detaildata['goodsname'],
                'goodsnature' => $detaildata['goodsnature'],
                'stockin_sysno' => $detaildata['stockin_sysno'],
                'stockin_no' => $detaildata['stockin_no'],
                'qualityname' => $detaildata['qualityname'],
                'unitname' => $detaildata['unitname'],
                'storagetank_sysno' => $detaildata['storagetank_sysno'],
//                'lastout' => $value['lastout'] == '是' ? 1 : 0,
//                'controlloss' => $value['controlloss'],
                'noticenum' => $detaildata['bookingoutqty'],
                'untakegoodsnum' => $detaildata['bookingoutqty'],
                'goods_sysno' => $detaildata['goods_sysno'],
            );

            $res = $this->dbh->insert(DB_PREFIX.'doc_booking_out_detail', $input);

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }


            if(count($cardata) > 0){
                foreach ($cardata as $value) {
                    $input = array(
                        'bookingout_sysno' => $id,
                        'carname' => $value['carname'],
                        'mobilephone' => $value['mobilephone'],
                        'idcard' => $value['idcard'],
                        'carid' => $value['carid'],
                        'carmarks' => $value['carmarks'],
                        'cartakeqty' => $value['cartakeqty'],
                        'status' => 1,
                        'isdel' => 0,
                        'version' => 1,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()'
                    );

                    $res = $this->dbh->insert(DB_PREFIX.'doc_booking_out_cars', $input);

                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
            }



            #库存管理业务操作日志
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  2,
                'opertype'  => 0 ,
                'operemployee_sysno' => 0,
                'operemployeename' => '云仓',
                'opertime'    => '=NOW()',
                'operdesc'  =>  '',
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  2,
                'opertype'  => 1 ,
                'operemployee_sysno' => 0,
                'operemployeename' => '云仓',
                'opertime'    => '=NOW()',
                'operdesc'  =>  '',
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            // #释放锁
            // $this->dbh->unlock();事务中不需要unlock
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    //根据预约ID获取出库预约单明细信息
    public function getBookoutDetailDataById($id = 0)
    {
        if (!$id) {
            COMMON::result(300, '信息有误');
            return false;
        }
        $sql = "select * from ".DB_PREFIX."doc_booking_out_detail where bookingout_sysno = ".intval($id);
        return $this->dbh->select($sql);
    }

    //插入管出库预约
    public function addPipelineBookout($data,$detaildata)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->insert(DB_PREFIX.'doc_booking_out', $data);

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $id = $res;
            foreach ($detaildata as $value) {
                $input = array(
                    'bookingout_sysno' => $id,
                    'stock_sysno' => $value['stock_sysno'],
                    'bookingoutqty' => $value['bookingoutqty'],
                    'memo' => $value['memo'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                    'goodsname' => $value['goodsname'],
                    'goodsnature' => $value['goodsnature'],
                    'stockin_sysno' => $value['stockin_sysno'],
                    'stockin_no' => $value['stockin_no'],
                    'qualityname' => $value['qualityname'],
                    'unitname' => '吨',
                    'storagetank_sysno' => $value['storagetank_sysno'],
                    'goods_sysno' => $value['goods_sysno'],
                    'stocktype' => $value['stocktype'],
                );

                $res = $this->dbh->insert(DB_PREFIX.'doc_booking_out_detail', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            $user = Yaf_Registry::get(SSN_VAR);
            #库存管理业务操作日志
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  24,
                'opertype'  => 0 ,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  '',
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if ($data['bookingoutstatus'] == 4) {
                $sql = "SELECT sysno from hengyang_system_privilege where privilegename = '管出库预约'";
                $privilege_sysno = $this->dbh->select_one($sql);

                $sql = "SELECT sysno from hengyang_system_privilege where privilegecontroller like '%bookout%' and privilegeaction like '%examjson%' and parent_sysno=".intval($privilege_sysno);
                $privilege_sysno = $this->dbh->select_one($sql);
                $userArr = $this->getUsers($privilege_sysno);
                if(count($userArr)>0){
                    $content = $data['customer_name']."的管出库预约单".$data['bookingoutno']."待审核";
                    foreach ($userArr as $uvalue) {
                        $messageInput = array(
                            'send_from_id'=>$user['sysno'],
                            'send_from_name'=>$user['username'],
                            'send_to_id'=>$uvalue['user_sysno'],
                            'viewstatus'=>1,
                            'subject'=>'管出库预约待审核',
                            'content'=>$content,
                            'message_type'=>1,
                            'replyid'=>'',
                            'created_at'=>'=NOW()',
                            'updated_at'=>'=NOW()',
                            'action'=>'pipelineaudit',
                            'control'=>'bookout',
                            'doc_sysno'=>$id,
                        );
                        $S ->addmessage($messageInput);
                    }
                }
                $input['opertype'] = 2;
            }else{
                $input['opertype'] = 1;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            // #释放锁
            // $this->dbh->unlock();事务中不需要unlock
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    public function updatePipelineBookout($id,$data,$detaildata)
    {
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_booking_out', $data, 'sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $res = $this->dbh->delete(DB_PREFIX.'doc_booking_out_detail', 'bookingout_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            foreach ($detaildata as $value) {
                $input = array(
                    'bookingout_sysno' => $id,
                    'stock_sysno' => $value['stock_sysno'],
                    'bookingoutqty' => $value['bookingoutqty'],
                    'shipokdate' => $value['shipokdate'],
                    'shipname' => $value['shipname'],
                    'memo' => $value['memo'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                    'goodsname' => $value['goodsname'],
                    'goodsnature' => $value['goodsnature'],
                    'stockin_sysno' => $value['stockin_sysno'],
                    'stockin_no' => $value['stockin_no'],
                    'qualityname' => $value['qualityname'],
                    'unitname' => $value['unitname'],
                    'storagetank_sysno' => $value['storagetank_sysno'],
                    'goods_sysno' => $value['goods_sysno'],
                    'stocktype' => $value['stocktype'],
                );

                $res = $this->dbh->insert(DB_PREFIX.'doc_booking_out_detail', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            $user = Yaf_Registry::get(SSN_VAR);
            #库存管理业务操作日志
            $S = new SystemModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $input= array(
                'doc_sysno'  =>  $id,
                'doctype'  =>  24,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime'    => '=NOW()',
                'operdesc'  =>  '',
            );
            if ($data['bookingoutstatus'] == 4) {
                $sql = "SELECT sysno from hengyang_system_privilege where privilegename = '管出库预约'";
                $privilege_sysno = $this->dbh->select_one($sql);

                $sql = "SELECT sysno from hengyang_system_privilege where privilegecontroller like '%bookout%' and privilegeaction like '%examjson%' and parent_sysno=".intval($privilege_sysno);
                $privilege_sysno = $this->dbh->select_one($sql);
                $userArr = $this->getUsers($privilege_sysno);
                if(count($userArr)>0){
                    $content = $data['customer_name']."的管出库预约单".$data['bookingoutno']."待审核";
                    foreach ($userArr as $uvalue) {
                        $messageInput = array(
                            'send_from_id'=>$user['sysno'],
                            'send_from_name'=>$user['username'],
                            'send_to_id'=>$uvalue['user_sysno'],
                            'viewstatus'=>1,
                            'subject'=>'管出库预约待审核',
                            'content'=>$content,
                            'message_type'=>1,
                            'replyid'=>'',
                            'created_at'=>'=NOW()',
                            'updated_at'=>'=NOW()',
                            'action'=>'pipelineaudit',
                            'control'=>'bookout',
                            'doc_sysno'=>$id,
                        );
                        $S ->addmessage($messageInput);
                    }
                }
                $input['opertype'] = 2;
            }else{
                $input['opertype'] = 1;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            // #释放锁
            // $this->dbh->unlock();事务中不需要unlock
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }
    //获取报关量信息
    public function getDeclareNum($stockin_sysno)
    {
        $sql ="SELECT release_num,islimitout from `".DB_PREFIX."doc_stock_in` where isdel = 0 and sysno = " .intval($stockin_sysno);
        return $this->dbh->select_row($sql);
    }

    //获取权限对应的用户
    public function getUsers($privilege_sysno)
    {
        $sql = "SELECT DISTINCT sur.user_sysno FROM hengyang_system_user su
            LEFT JOIN `hengyang_system_user-r-role` sur ON su.sysno=sur.user_sysno
            LEFT JOIN hengyang_system_role sr ON sur.role_sysno = sr.sysno
            LEFT JOIN `hengyang_system_role-r-privilege` srp ON sr.sysno = srp.role_sysno
            LEFT JOIN hengyang_system_privilege sp ON srp.privilege_sysno = sp.sysno
            where su.`status`=1 and su.isdel=0 and sr.`status`=1 and sr.isdel=0 and sp.`status`=1 and sp.isdel=0 and srp.privilege_sysno=".intval($privilege_sysno);
        return $this->dbh->select($sql);
    }
}
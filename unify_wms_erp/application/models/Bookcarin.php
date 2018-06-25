<?php
class BookcarinModel
{
    /**
     * 数据库类实例
     */
    public $dbh = null;

    /**
     * 缓存类实例
     */
    public $mch = null;

    /**
     * @param   object $dbh
     * @param   object $mch
     * @return  void
     */
    public function __construct($dbh, $mch)
    {
        $this->dbh = $dbh;

        $this->mch = $mch;
    }

    /*
     * 查询车入库预约单
     */
    public function searchBookcarin($params)
    {
        $filter = array();
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " b.`bookingindate` >= '{$params['begin_time']}' ";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " b.`bookingindate` <= '{$params['end_time']}' ";
        }
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " b.`bookinginno` LIKE '%{$params['bar_no']}%' ";
        }
        if (isset($params['docsource']) && $params['docsource'] != '') {
            $filter[] = " b.`docsource` = {$params['docsource']} ";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " b.`customer_sysno` = '{$params['customer_sysno']}' ";
        }
        if (isset($params['contract_sysno']) && $params['contract_sysno'] != '') {
            $filter[] = " b.`contract_sysno` = {$params['contract_sysno']} ";
        }
        if (isset($params['bar_bookcarinstatus']) && $params['bar_bookcarinstatus'] != '-100') {
            $filter[] = " b.`bookinginstatus`= {$params['bar_bookcarinstatus']} ";
        }
        if (isset($params['bar_stockintype']) && $params['bar_stockintype'] != '') {
            $filter[] = " b.`stockintype`='{$params['bar_stockintype']}'";
        }

        $where = 'b.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = " order by b.created_at desc";

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_booking_in` b
                left join `".DB_PREFIX."customer` c on b.`customer_sysno`=c.`sysno` where {$where} ";

        $result = $params;
        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT b.*, bd.`goodsnature`,bd.`shipname`,sum(bd.`bookinginqty`) as bookinginqty,bd.`storagetank_sysno`,
                        (select goodsname from ".DB_PREFIX."base_goods bg where bg.`sysno` = bd.`goods_sysno`) as goodsname,
                        (select qualityname from ".DB_PREFIX."base_goods_quality gq where gq.`sysno`=bd.`goods_quality_sysno` ) as goods_quality_name,
                        (select storagetankname from ".DB_PREFIX."base_storagetank bs where bs.`sysno`=bd.`storagetank_sysno` ) as storagetankname
                        FROM `".DB_PREFIX."doc_booking_in` b left join `".DB_PREFIX."customer` c on (b.`customer_sysno`=c.`sysno`)
                        left join `".DB_PREFIX."doc_booking_in_detail` bd on (b.`sysno`=bd.`bookingin_sysno`)
                        where {$where} group by b.`sysno` $order";

                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT b.*, bd.`goodsnature`,bd.`shipname`,sum(bd.`bookinginqty`) as bookinginqty,bd.`storagetank_sysno`,
                        (select goodsname from ".DB_PREFIX."base_goods bg where bg.`sysno` = bd.`goods_sysno`) as goodsname,
                        (select qualityname from ".DB_PREFIX."base_goods_quality gq where gq.`sysno`=bd.`goods_quality_sysno` ) as goods_quality_name,
                        (select storagetankname from ".DB_PREFIX."base_storagetank bs where bs.`sysno`=bd.`storagetank_sysno` ) as storagetankname
                        FROM `".DB_PREFIX."doc_booking_in` b left join `".DB_PREFIX."customer` c on (b.`customer_sysno`=c.`sysno`)
                        left join `".DB_PREFIX."doc_booking_in_detail` bd on (b.`sysno`=bd.`bookingin_sysno`)
                        where {$where} group by b.`sysno` $order";

                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    /*
     * 根据车入库预约单查询基本信息
     */
    public function getBookcarinInfoById($id){
        $sql = "select * from ".DB_PREFIX."doc_booking_in where sysno = $id";
        return $this->dbh->select_row($sql);
    }

    /*
     * 根据车入库预约单查询详情  此方法好像可以被上面方法代替
     */
    public function getBookcarinById($id)
    {
        $sql = "SELECT b.*,bd.`goodsnature`,bd.`shipname`,
            (select goodsname from ".DB_PREFIX."base_goods bg where bg.`sysno` = bd.`goods_sysno`) as goodsname,
            sum(bd.`bookinginqty`) as bookinginqty,bd.`storagetank_sysno`,
            (select qualityname from ".DB_PREFIX."base_goods_quality gq where gq.`sysno`=bd.`goods_quality_sysno` ) as goods_quality_name,
            (select storagetankname from ".DB_PREFIX."base_storagetank bs where bs.`sysno`=bd.`storagetank_sysno` ) as storagetankname
            FROM `".DB_PREFIX."doc_booking_in` b
            left join `".DB_PREFIX."customer` c on (b.`customer_sysno`=c.`sysno`)
            left join `".DB_PREFIX."doc_booking_in_detail` bd on (b.`sysno`=bd.`bookingin_sysno`)
            where b.`stockintype`=2 and b.`sysno`= " . intval($id) . " group by bd.`bookingin_sysno` ";
        return $this->dbh->select_row($sql);
    }

    /*
     * 添加车入库预约单
     */
    public function addBookcarin($data, $bookcarindetaildata, $bookcarincarsdata,$bookinginstatus)
    {
        $this->dbh->begin();
        try {
            if($bookinginstatus ==2){
                $data['bookinginstatus'] = 2;
            }elseif($bookinginstatus ==3){
                $data['bookinginstatus'] = 3;
            }
            $res = $this->dbh->insert(DB_PREFIX.'doc_booking_in', $data);

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $id = $res;

            $res = $this->dbh->delete(DB_PREFIX.'doc_booking_in_detail', 'bookingin_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $res = $this->dbh->delete(DB_PREFIX.'doc_booking_in_cars', 'bookingin_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if (!empty($bookcarindetaildata)) {
                foreach ($bookcarindetaildata as $value) {
                    $input = array(
                        'bookingin_sysno' => $id,
                        'goods_sysno' => $value['goods_sysno'],
                        'goods_quality_sysno' => $value['goods_quality_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'bookinginqty' => $value['bookinginqty'],
                        'bookingindate'=>$value['bookingindate'],
                        'storagetank_sysno' => $value['storagetank_sysno'],
                        'shipname' => $value['shipname'],
                        'memo' => $value['memo'],
                        'status' => 1,
                        'isdel' => 0,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()'
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_booking_in_detail', $input);

                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
            }

            if (!empty($bookcarincarsdata)) {
                foreach ($bookcarincarsdata as $value) {
                    $input = array(
                        'bookingin_sysno' => $id,
                        'carname' => $value['carname'],
                        'mobilephone' => $value['mobilephone'],
                        'idcard' => $value['idcard'],
                        'carid' => $value['carid'],
                        'memo' => $value['memo'],
                        'status' => 1,
                        'isdel' => 0,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()'
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_booking_in_cars', $input);

                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
            }

            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 1,
                'opertype'=>0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' =>'新建车入库预约单'
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if($bookinginstatus ==2){  //暂存时操作日志
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存车入库预约单';
            }elseif($bookinginstatus ==3){
                $input['opertype'] = 2;
                $input['operdesc'] = '提交车入库预约单';
            }
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 更新车入库预约单
     */
    public function updateBookcarin($id, $data, $bookcarindetaildata, $bookcarincarsdata, $bookinginstatus)
    {
        $this->dbh->begin();
        try {
            $S = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $bookcarininfo = $S->getBookcarinById($id);
            if ($bookinginstatus == 2&&$bookcarininfo['bookinginstatus']!=7) {
                $data['bookinginstatus'] = 2;
            }elseif($bookinginstatus == 3){
                $data['bookinginstatus'] = 3;
            }elseif($bookinginstatus == 8){
                $data['bookinginstatus'] = 8;
            }

            $res = $this->dbh->update(DB_PREFIX.'doc_booking_in', $data, 'sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $detailresult = $this->dbh->delete(DB_PREFIX.'doc_booking_in_detail', 'bookingin_sysno=' . intval($id));
            $carresult = $this->dbh->delete(DB_PREFIX.'doc_booking_in_cars', 'bookingin_sysno=' . intval($id));

            if (!$detailresult||!$carresult) {
                $this->dbh->rollback();
                return false;
            }

            if(!empty($bookcarindetaildata))
            foreach ($bookcarindetaildata as $value) {
                $input = array(
                    'bookingin_sysno' => $id,
                    'goods_sysno' => $value['goods_sysno'],
                    'goods_quality_sysno' => $value['goods_quality_sysno'],
                    'goodsnature' => $value['goodsnature'],
                    'bookinginqty' => $value['bookinginqty'],
                    'bookingindate'=>$value['bookingindate'],
                    'storagetank_sysno' => $value['storagetank_sysno'],
                    'shipname' => $value['shipname'],
                    'memo' => $value['memo'],
                    'status' => $value['status'],
                    'isdel' => $value['isdel'],
                    'version' => $value['version'],
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()'
                );
                $res = $this->dbh->insert(DB_PREFIX.'doc_booking_in_detail', $input);

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            if(!empty($bookcarincarsdata))
                foreach ($bookcarincarsdata as $value) {
                    $input = array(
                        'bookingin_sysno' => $id,
                        'carname' => $value['carname'],
                        'mobilephone' => $value['mobilephone'],
                        'idcard' => $value['idcard'],
                        'carid' => $value['carid'],
                        'carmarks' => $value['carmarks'],
                        'status' => $value['status'],
                        'isdel' => $value['isdel'],
                        'version' => $value['version'],
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()'
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_booking_in_cars', $input);

                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
            if($bookinginstatus ==8){
//                if()
                COMMON::editStockInReject($data['bookinginno'], $data['rejectreason']);
            }

            #车入库预约操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 1,
                'opertype' =>2,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
            );

            if($bookinginstatus ==2){
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存车入库预约单';
            }elseif($bookinginstatus ==3){
                $input['opertype'] = 2;
                $input['operdesc'] = '提交车入库预约单';
            }elseif($bookinginstatus ==8){
                $input['opertype'] = 9;
                $input['operdesc'] = '驳回车入库预约单';
            }

            $res = $S->addDocLog($input);

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 登记车入库预约单车辆信息
     */
    public function addcarBookcarin($id,$bookcarincarsdata){
        $this->dbh->begin();
        try {

            $carresult = $this->dbh->delete(DB_PREFIX.'doc_booking_in_cars', 'bookingin_sysno=' . intval($id));

            if (!$carresult) {
                $this->dbh->rollback();
                return false;
            }

            if(!empty($bookcarincarsdata))
                foreach ($bookcarincarsdata as $value) {
                    $input = array(
                        'bookingin_sysno' => $id,
                        'carname' => $value['carname'],
                        'mobilephone' => $value['mobilephone'],
                        'idcard' => $value['idcard'],
                        'carid' => $value['carid'],
                        'carmarks' => $value['carmarks'],
                        'status' => $value['status'],
                        'isdel' => $value['isdel'],
                        'version' => $value['version'],
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()'
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_booking_in_cars', $input);

                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }

            #车入库预约操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 1,
                'opertype' =>7,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'operdesc' =>'登记车辆信息',
                'opertime' => '=NOW()',
            );

            $res = $S->addDocLog($input);

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $this->dbh->commit();
            return $id;
        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 车入库预约单仓储确认
     */
    public function sureBookcarin($id, $data,$bookcarindetaildata){
        $this->dbh->begin();
        try{
            $res = $this->dbh->update(DB_PREFIX.'doc_booking_in', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $detailresult = $this->dbh->delete(DB_PREFIX.'doc_booking_in_detail', 'bookingin_sysno=' . intval($id));
            if (!$detailresult) {
                $this->dbh->rollback();
                return false;
            }

            if(!empty($bookcarindetaildata))
                foreach ($bookcarindetaildata as $value) {
                    $input = array(
                        'bookingin_sysno' => $id,
                        'goods_sysno' => $value['goods_sysno'],
                        'goods_quality_sysno' => $value['goods_quality_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'bookinginqty' => $value['bookinginqty'],
                        'bookingindate'=>$value['bookingindate'],
                        'storagetank_sysno' => $value['storagetank_sysno'],
                        'shipname' => $value['shipname'],
                        'memo' => $value['memo'],
                        'status' => $value['status'],
                        'isdel' => $value['isdel'],
                        'version' => $value['version'],
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()'
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_booking_in_detail', $input);

                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }

            $S = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

            //如果仓储确认则锁定罐容 否则不锁
            if($data['bookinginstatus']==4){
                foreach($bookcarindetaildata as $item){
                    $Storagetankinfo = $S->getStoragetankById($item['storagetank_sysno']);
                    $Storagetankdata['orderinqty'] = $Storagetankinfo['orderinqty'] + $item['bookinginqty'];

                    // error_log(date("Y-m-d H:i:s") . "\t" . json_encode($Storagetankdata) . "\n", 3, './logs/bookcarin.log');

                    $res = $this->dbh->update(DB_PREFIX.'base_storagetank', $Storagetankdata, 'sysno=' . intval($item['storagetank_sysno']));

                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
            }

            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 1,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $data['confirmreason'],
            );

            if($data['bookinginstatus']==4){
                $input['opertype'] = 3;
                #添加提示信息
                $booking_data = $this->getBookcarinInfoById($id);
                $booking = new BookingModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $res = $booking->shipinsertmes($booking_data);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
                #添加提示信息end
            }elseif($data['bookinginstatus']==7){
                $input['opertype'] = 6;
            }

            $res = $S->addDocLog($input);

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $this->dbh->commit();
            return $res;
        }catch(Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 车入库预约单审核
     */
    public function auditBookcarin($id, $data,$bookcarindetaildata)
    {
        $this->dbh->begin();
        try {
            $ret = $this->dbh->update(DB_PREFIX.'doc_booking_in', $data, 'sysno=' . intval($id));

            if (!$ret) {
                $this->dbh->rollback();
                return false;
            }

            //审核不通过，释放罐容
            $S = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            if($data['bookinginstatus']==7){
                foreach($bookcarindetaildata as $item){
                    $Storagetankinfo = $S->getStoragetankById($item['storagetank_sysno']);
                    $Storagetankdata['orderinqty'] = $Storagetankinfo['orderinqty'] - $item['bookinginqty'];

                    $res = $this->dbh->update(DB_PREFIX.'base_storagetank', $Storagetankdata, 'sysno=' . intval($item['storagetank_sysno']));

                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 1,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'operdesc' =>$data['auditreason'],
                'opertime' => '=NOW()',
            );

            if($data['bookinginstatus']==5){
                $input['opertype'] = 4;
            }elseif($data['bookinginstatus']==7){
                $input['opertype'] = 6;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $ret;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 车入库预约单删除
     */
    public function delBookcarin($id, $data)
    {
        $this->dbh->begin();
        try {
            $ret = $this->dbh->update(DB_PREFIX.'doc_booking_in', $data, 'sysno=' . intval($id));

            if (!$ret) {
                $this->dbh->rollback();
                return false;
            }

            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 1,
                'opertype' => 8,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => '删除',
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $ret;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 车入库预约单退回
     */
    public function backbookcarin($id, $data,$bookcarindetaildata){
        $this->dbh->begin();
        try{
            $res = $this->dbh->update(DB_PREFIX.'doc_booking_in', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $S = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

            foreach($bookcarindetaildata as $item){
                $Storagetankinfo = $S->getStoragetankById($item['storagetank_sysno']);
                $Storagetankdata['orderinqty'] = $Storagetankinfo['orderinqty'] - $item['bookinginqty'];

                $res = $this->dbh->update(DB_PREFIX.'base_storagetank', $Storagetankdata, 'sysno=' . intval($item['storagetank_sysno']));

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 1,
                'opertype' => 6,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $data['backreason'],
            );
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $this->dbh->commit();
            return $res;
        }catch(Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 根据车入库预约单查询明细
     */
    public function getBookcarindetailById($id)
    {
        $sql = "SELECT b.*,bg.`goodsname`,st.`storagetankname`
                FROM `".DB_PREFIX."doc_booking_in_detail` b
                left join `".DB_PREFIX."base_goods` bg on (b.`goods_sysno`=bg.`sysno`)
                left join `".DB_PREFIX."base_storagetank` st on (b.`storagetank_sysno`=st.`sysno`)
                where b.`isdel`=0 and b.`bookingin_sysno`=" . intval($id);
        return $this->dbh->select($sql);
    }

    /*
     * 根据车入库预约单查询车辆信息
     */
    public function getBookcarincarsById($id)
    {
        $sql = "SELECT b.`carname`,b.`mobilephone`,b.`idcard`,b.`carid`,b.`memo`
                FROM `".DB_PREFIX."doc_booking_in_cars` b
                where b.`isdel`=0 and b.`bookingin_sysno`=" . intval($id);
        return $this->dbh->select($sql);
    }

    /*
     * 添加车入库预约单
     */
    public function addBookcarinForApi($data, $bookcarindetaildata, $bookcarincarsdata,$bookinginstatus)
    {
        $this->dbh->begin();
        try {
            if($bookinginstatus ==2){
                $data['bookinginstatus'] = 2;
            }elseif($bookinginstatus ==3){
                $data['bookinginstatus'] = 3;
            }
            $id = $this->dbh->insert(DB_PREFIX.'doc_booking_in', $data);
            if (!$id) {
                $this->dbh->rollback();
                return false;
            }

            $res = $this->dbh->delete(DB_PREFIX.'doc_booking_in_detail', 'bookingin_sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $res = $this->dbh->delete(DB_PREFIX.'doc_booking_in_cars', 'bookingin_sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if (!empty($bookcarindetaildata)) {
                $input = array(
                    'bookingin_sysno' => $id,
                    'goods_sysno' => $bookcarindetaildata['goods_sysno'],
                    'goods_quality_sysno' => $bookcarindetaildata['goods_quality_sysno'],
                    'goodsnature' => $bookcarindetaildata['goodsnature'],
                    'bookinginqty' => $bookcarindetaildata['bookinginqty'],
                    'bookingindate'=>$bookcarindetaildata['bookingindate'],
                    'storagetank_sysno' => $bookcarindetaildata['storagetank_sysno'],
                    'shipname' => $bookcarindetaildata['shipname'],
                    'memo' => $bookcarindetaildata['memo'],
                    'status' => 1,
                    'isdel' => 0,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()'
                );
                $res = $this->dbh->insert(DB_PREFIX.'doc_booking_in_detail', $input);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            if (!empty($bookcarincarsdata)) {
                foreach ($bookcarincarsdata as $value) {
                    $input = array(
                        'bookingin_sysno' => $id,
                        'carname' => $value['carname'],
                        'mobilephone' => $value['mobilephone'],
                        'idcard' => $value['idcard'],
                        'carid' => $value['carid'],
                        'memo' => $value['memo'],
                        'status' => 1,
                        'isdel' => 0,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()'
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_booking_in_cars', $input);
                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
            }

            #库存管理业务操作日志
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 1,
                'operemployee_sysno' => 0,
                'operemployeename' => '云仓',
                'opertime' => '=NOW()',
            );

            //默认暂存时操作日志
            $input['opertype'] = 1;
            $input['operdesc'] = '暂存车入库预约单';
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            if($bookinginstatus ==3){  //暂存时操作日志
                $input['opertype'] = 2;
                $input['operdesc'] = '提交车入库预约单';
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
}
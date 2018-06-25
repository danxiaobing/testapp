<?php

/**
 * Created by PhpStorm.
 * User: 129
 * Date: 2017/7/6
 * Time: 11:28
 */
class BookberthinModel
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
     *搜索靠泊装货预约单
     */
    public function searchBookberthin($params)
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
        if (isset($params['inshipname']) && $params['inshipname'] != '') {
            $filter[] = " b.`inshipname` = '{$params['inshipname']}' ";
        }
        if (isset($params['contract_sysno']) && $params['contract_sysno'] != '') {
            $filter[] = " b.`contract_sysno` = {$params['contract_sysno']} ";
        }
        if (isset($params['bar_bookinstatus']) && $params['bar_bookinstatus'] != '-100') {
            $filter[] = " b.`bookinginstatus`= {$params['bar_bookinstatus']} ";
        }
        if (isset($params['bar_stockintype']) && $params['bar_stockintype'] != '') {
            $filter[] = " b.`stockintype`='{$params['bar_stockintype']}'";
        }

        $where = 'b.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = " order by b.updated_at desc";

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
     *根据id获取装货预约单
     */
    public function getBookberthinById($id){
        $sql = "SELECT * FROM `".DB_PREFIX."doc_booking_in` where `sysno`= " . intval($id) . "";
        return $this->dbh->select_row($sql);
    }

    /*
     * 根据id获取装货预约单详情
     */
    public function getBookberthindetailById($id){
        $sql = "SELECT bid.*,bg.goodsname FROM `".DB_PREFIX."doc_booking_in_detail` bid
        LEFT JOIN ".DB_PREFIX."base_goods bg on bg.sysno = bid.goods_sysno
        where `bookingin_sysno`= " . intval($id) . "";
        return $this->dbh->select($sql);
    }

    /*
     * 添加靠泊装货预约单
     */
    public function addBookberthin($data, $bookberthindetaildata, $bookinginstatus)
    {
        $this->dbh->begin();
        try {
            if($bookinginstatus ==2){
                $data['bookinginstatus'] = 2;
            }elseif($bookinginstatus ==4){
                $data['bookinginstatus'] = 4;
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

            if (!empty($bookberthindetaildata)) {
                foreach ($bookberthindetaildata as $value) {
                    $input = array(
                        'bookingin_sysno' => $id,
                        'goods_sysno' => $value['goods_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'bookinginqty' => $value['bookinginqty'],
                        'bookingindate'=> $value['bookingindate'],
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

            #业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 18,
                'opertype'=>0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' =>'新建靠泊装预约单'
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if($bookinginstatus ==2){  //暂存时操作日志
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存靠泊装预约单';
            }elseif($bookinginstatus ==4){
                #添加提示信息
                $booking_data = $this->getBookberthinById($id);
                $booking = new BookingModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $res = $booking->shipinsertmes($booking_data);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
                #添加提示信息end
                $input['opertype'] = 2;
                $input['operdesc'] = '提交靠泊装预约单';
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
     * 更新靠泊装货预约单
     */
    public function updatebookberthin($id, $data, $bookberthindetaildata,$bookinginstatus){
        $this->dbh->begin();
        try {
            $S = new BookberthinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $bookcarininfo = $S->getBookberthinById($id);
            if ($bookinginstatus == 2&&$bookcarininfo['bookinginstatus']!=7) {
                $data['bookinginstatus'] = 2;
            }elseif($bookinginstatus == 4){
                $data['bookinginstatus'] = 4;
            }elseif($bookinginstatus == 8){
                $data['bookinginstatus'] = 8;
            }

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

            if(!empty($bookberthindetaildata))
                foreach ($bookberthindetaildata as $value) {
                    $input = array(
                        'bookingin_sysno' => $id,
                        'goods_sysno' => $value['goods_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'bookinginqty' => $value['bookinginqty'],
                        'bookingindate'=>$value['bookingindate'],
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

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 18,
                'opertype' =>2,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
            );

            if($bookinginstatus ==2){
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存靠泊装预约单';
            }elseif($bookinginstatus ==4){
                $input['opertype'] = 2;
                $input['operdesc'] = '提交靠泊装预约单';
                #添加提示信息
                $booking_data = $this->getBookberthinById($id);
                $booking = new BookingModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $res = $booking->shipinsertmes($booking_data);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
                #添加提示信息end            
            }elseif($bookinginstatus ==8){
                $input['opertype'] = 9;
                $input['operdesc'] = '驳回靠泊装预约单';
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
     * 审核靠泊装货预约单
     */
    public function auditBookberthin($id, $data,$bookberthindetaildata){
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_booking_in', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 18,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'operdesc' =>empty($data['auditreason'])?' ':$data['auditreason'],
                'opertime' => '=NOW()',
            );

            if($data['bookinginstatus']==5){
                $input['opertype'] = 3;
            }elseif($data['bookinginstatus']==7){
                $input['opertype'] = 5;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $res;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }

    }

    /*
     * 删除靠泊装货预约单
     */
    public function delBookberthin($id,$input)
    {
        return $this->dbh->update(DB_PREFIX.'doc_booking_in', $input, 'sysno=' . intval($id));
    }

}
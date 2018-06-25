<?php

/**
 * Created by PhpStorm.
 * User: 129
 * Date: 2017/7/6
 * Time: 13:32
 */
class BookberthoutModel
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
     *搜索靠泊卸货预约单
     */
    public function searchBookberthout($params)
    {
        $filter = array();
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " b.`bookingoutdate` >= '{$params['begin_time']}' ";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " b.`bookingoutdate` <= '{$params['end_time']}' ";
        }
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " b.`bookingoutno` LIKE '%{$params['bar_no']}%' ";
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
        if (isset($params['bar_bookberthoutstatus']) && $params['bar_bookberthoutstatus'] != '-100') {
            $filter[] = " b.`bookingoutstatus`= {$params['bar_bookberthoutstatus']} ";
        }
        if (isset($params['bar_stockouttype']) && $params['bar_stockouttype'] != '') {
            $filter[] = " b.`stockouttype`='{$params['bar_stockouttype']}'";
        }

        $where = 'b.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = " order by b.updated_at desc";

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_booking_out` b
                left join `".DB_PREFIX."customer` c on b.`customer_sysno`=c.`sysno` where {$where} ";

        $result = $params;
        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT b.*,bod.goodsname,bod.goodsnature,SUM(bod.bookingoutqty) as bookingoutqty FROM ".DB_PREFIX."doc_booking_out b
                        LEFT JOIN ".DB_PREFIX."doc_booking_out_detail bod ON bod.bookingout_sysno = b.sysno
                        where {$where} group by b.`sysno` $order";

                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT b.*,bod.goodsname,bod.goodsnature,SUM(bod.bookingoutqty) as bookingoutqty FROM ".DB_PREFIX."doc_booking_out b
                        LEFT JOIN ".DB_PREFIX."doc_booking_out_detail bod ON bod.bookingout_sysno = b.sysno
                        where {$where} group by b.`sysno` $order";

                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    /*
     * 获取明细信息
     * */
    public  function getBookoutdetailById($params){
        $filter = array();
        if (isset($params['bookingout_sysno']) && $params['bookingout_sysno'] != '') {
            $filter[] = " bd.`bookingout_sysno` = {$params['bookingout_sysno']} ";
        }

        $where = 'bd.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }else{
            return array();
        }

        $sql = "select * from ".DB_PREFIX."doc_booking_out_detail bd where {$where}" ;

        return $this->dbh->select($sql);

    }

    /*
     *根据id获取靠泊卸货预约单
     */
    public function getBookberthoutById($id){
        $sql = "SELECT * FROM `".DB_PREFIX."doc_booking_out` where `sysno`= " . intval($id) . "";
        return $this->dbh->select_row($sql);
    }

    /*
     *根据id获取靠泊卸货预约单明细
     */
    public function getBookberthoutdetailById($id){
        $sql = "SELECT bid.*,bg.goodsname FROM `".DB_PREFIX."doc_booking_out_detail` bid
        LEFT JOIN ".DB_PREFIX."base_goods bg on bg.sysno = bid.goods_sysno
        where `bookingout_sysno`= " . intval($id) . "";
        return $this->dbh->select($sql);
    }

    /*
     * 添加靠泊卸货预约单
     */
    public function addBookberthout($data, $bookberthoutdetaildata, $bookingoutstatus)
    {
        $this->dbh->begin();
        try {
            if($bookingoutstatus ==2){
                $data['bookingoutstatus'] = 2;
            }elseif($bookingoutstatus ==3){
                $data['bookingoutstatus'] = 4;
            }
            $res = $this->dbh->insert(DB_PREFIX.'doc_booking_out', $data);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $id = $res;
            $res = $this->dbh->delete(DB_PREFIX.'doc_booking_out_detail', 'bookingout_sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if (!empty($bookberthoutdetaildata)) {
                foreach ($bookberthoutdetaildata as $value) {
                    $input = array(
                        'bookingout_sysno' => $id,
                        'goods_sysno' => $value['goods_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'goodsname' => $value['goodsname'],
                        'bookingoutqty' => $value['bookingoutqty'],
                        'shipokdate'=>$value['shipokdate'],
                        'shipname' => $value['shipname'],
                        'memo' => $value['memo'],
                        'status' => 1,
                        'isdel' => 0,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()'
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_booking_out_detail', $input);
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
                'doctype' => 19,
                'opertype'=>0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' =>'新建靠泊卸货预约单'
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if($bookingoutstatus ==2){  //暂存时操作日志
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存靠泊卸货预约单';
            }elseif($bookingoutstatus ==3){
                $input['opertype'] = 2;
                $input['operdesc'] = '提交靠泊卸货预约单';
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
     *更新靠泊卸货预约单
     */
    public function updatebookberthout($id, $data, $bookberthoutdetaildata,$bookingoutstatus){
        $this->dbh->begin();
        try {
            $B = new BookberthoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $bookberthoutinfo = $B->getBookberthoutById($id);
            if ($bookingoutstatus == 2&&$bookberthoutinfo['bookingoutstatus']!=7) {
                $data['bookingoutstatus'] = 2;
            }elseif($bookingoutstatus == 3){
                $data['bookingoutstatus'] = 4;
            }elseif($bookingoutstatus == 8){
                $data['bookingoutstatus'] = 8;
            }

            $res = $this->dbh->update(DB_PREFIX.'doc_booking_out', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $detailresult = $this->dbh->delete(DB_PREFIX.'doc_booking_out_detail', 'bookingout_sysno=' . intval($id));
            if (!$detailresult) {
                $this->dbh->rollback();
                return false;
            }

            if(!empty($bookberthoutdetaildata))
                foreach ($bookberthoutdetaildata as $value) {
                    $input = array(
                        'bookingout_sysno' => $id,
                        'goods_sysno' => $value['goods_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'goodsname' => $value['goodsname'],
                        'bookingoutqty' => $value['bookingoutqty'],
                        'shipokdate'=>$value['shipokdate'],
                        'shipname' => $value['shipname'],
                        'memo' => $value['memo'],
                        'status' => 1,
                        'isdel' => 0,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()'
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_booking_out_detail', $input);
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
                'doctype' => 19,
                'opertype' =>2,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
            );

            if($bookingoutstatus ==2){
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存靠泊装预约单';
            }elseif($bookingoutstatus ==3){
                $input['opertype'] = 2;
                $input['operdesc'] = '提交靠泊装预约单';
            }elseif($bookingoutstatus ==8){
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
     * 审核靠泊卸货预约单
     */
    public function auditBookberthout($id, $data){
        $this->dbh->begin();
        try {
            $input =[
                'bookingoutstatus' => $data['bookingoutstatus'],
                'auditreason' => $data['auditreason'],
                'updated_at' => '=NOW()'
            ];
            $res = $this->dbh->update(DB_PREFIX.'doc_booking_out', $input, 'sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 19,
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
            return $res;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 删除靠泊装货预约单
     */
    public function delBookberthout($id,$input)
    {
        return $this->dbh->update(DB_PREFIX.'doc_booking_out', $input, 'sysno=' . intval($id));
    }

}
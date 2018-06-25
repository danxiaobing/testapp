<?php

/**
 * Created by PhpStorm.
 * User: 129
 * Date: 2018/1/29
 * Time: 11:22
 */
class StockadjustModel
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
     *搜索库存调整订单
     */
    public function searchStockadjust($params)
    {
        $filter = array();
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " s.`stockcheckdate` >= '{$params['begin_time']}' ";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " s.`stockcheckdate` <= '{$params['end_time']}' ";
        }
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " s.`stockcheckno` LIKE '%{$params['bar_no']}%' ";
        }

        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " s.`customer_sysno` = '{$params['customer_sysno']}' ";
        }
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " s.`goods_sysno` = {$params['goods_sysno']} ";
        }
        if (isset($params['bar_stockcheckstatus']) && $params['bar_stockcheckstatus'] != '-100') {
            $filter[] = " s.`stockcheckstatus`= {$params['bar_stockcheckstatus']} ";
        }

        $where = 's.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = " order by s.updated_at desc";

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_stock_check` s where {$where} ";

        $result = $params;
        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT s.sysno,s.stockcheckno,date(s.stockcheckdate) AS stockcheckdate,s.customername,s.goodsname,SUM(scd.stockqty) AS beqty,s.stockcheckstatus
                        FROM `".DB_PREFIX."doc_stock_check`s
                        LEFT JOIN `".DB_PREFIX."doc_stock_check_detail`scd ON s.sysno = scd.stockcheck_sysno
                        WHERE {$where} group by s.`sysno` $order";

                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT s.sysno,s.stockcheckno,date(s.stockcheckdate) AS stockcheckdate,s.customername,s.goodsname,SUM(scd.stockqty) AS beqty,s.stockcheckstatus
                        FROM `".DB_PREFIX."doc_stock_check`s
                        LEFT JOIN `".DB_PREFIX."doc_stock_check_detail`scd ON s.sysno = scd.stockcheck_sysno
                        WHERE {$where} group by s.`sysno` $order";

                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    /*
     * 根据id查找库存调整订单
     */
    public function getstockadjustById($id){
        $sql = "SELECT * FROM `".DB_PREFIX."doc_stock_check` where `sysno`= " . intval($id) . "";
        return $this->dbh->select_row($sql);
    }

    /*
     *根据id查找库存调整订单详情
     */
    public function getStockadjustdetailById($id){
        $sql = "SELECT * FROM `".DB_PREFIX."doc_stock_check_detail` where `stockcheck_sysno`= " . intval($id) . "";
        return $this->dbh->select($sql);
    }

    /*
     * 根据客户id查找客户库存小于0的货品
     */
    public function getstockgoodsleft0($id){
        $sql = "SELECT DISTINCT goods_sysno,goodsname FROM `".DB_PREFIX."storage_stock` where iscurrent = 1 AND stockqty < 0 AND `customer_sysno`= " . intval($id) . "";
        return $this->dbh->select($sql);
    }

    /*
     * 根据客户id与货品id查找客户库存小于0的数据
     */
    public function searchCustomergoodsleft0($search){
        $sql = "SELECT sysno,doctype,shipname,goodsqualityname AS qualityname,goodsnature,instockqty,stockqty,firstfrom_no as stockinno
                FROM `".DB_PREFIX."storage_stock`
                where iscurrent = 1 AND stockqty < 0 AND customer_sysno= {$search['customer_sysno']} AND goods_sysno = {$search['goods_sysno']}";
        $data = $this->dbh->select($sql);

        if(!empty($data)){
            foreach($data as $key => $item){
                if($item['doctype'] == 3){
                    $sql = "SELECT st.stocktransno as stockinno FROM `".DB_PREFIX."doc_stock_trans` st
                            LEFT JOIN `".DB_PREFIX."doc_stock_trans_detail` std ON st.sysno = std.stocktrans_sysno
                            WHERE std.in_stock_sysno = {$item['sysno']}";
                    $data[$key][''] = $this->dbh->select_one($sql);
                }
            }
        }
        return $data;
    }

    /*
     * 判断库存是否被引用
     */
    public function checkstockisquotes($id){
        //使用包括出库预约暂存/待审核、出库的出库中/待审核、
        //货权转移的暂存/待审核、倒罐的暂存/待审核、清库暂存/待审核、退货暂存/待审核；

        //出库预约单暂存/待审核
        $sql = "SELECT bod.sysno FROM hengyang_doc_booking_out_detail bod
                            LEFT JOIN hengyang_doc_booking_out bo ON bod.bookingout_sysno = bo.sysno
                            WHERE bo.isdel = 0 AND bo.bookingoutstatus IN (2,3,4) AND bod.stock_sysno IN ($id)";
        $bookoutres = $this ->dbh ->select_one($sql);
        if ($bookoutres) {
            $this->dbh->rollback();
            return ['statusCode' => 300, 'msg' => '存在出库预约单暂存/待审核的单据，不允许调整此库存'];
        }

        //出库订单出库中/待审核
        $sql = "SELECT sod.sysno FROM hengyang_doc_stock_out_detail sod
                            LEFT JOIN hengyang_doc_stock_out so ON sod.stockout_sysno = so.sysno
                            WHERE so.isdel = 0 AND so.stockoutstatus IN (2,3,6) AND sod.stock_sysno IN ($id)";
        $stockoutres = $this ->dbh ->select_one($sql);
        if ($stockoutres) {
            $this->dbh->rollback();
            return ['statusCode' => 300, 'msg' => '存在出库订单暂存/待审核的单据，不允许调整此库存'];
        }

        //货权转移暂存/待审核
        $sql = "SELECT std.sysno FROM hengyang_doc_stock_trans_detail std
                            LEFT JOIN hengyang_doc_stock_trans st ON std.stocktrans_sysno = st.sysno
                            WHERE st.isdel = 0 AND st.stocktransstatus IN (2,3,6) AND std.out_stock_sysno IN ($id)
                            OR std.in_stock_sysno IN ($id)";
        $stocktransres = $this ->dbh ->select_one($sql);
        if ($stocktransres) {
            $this->dbh->rollback();
            return ['statusCode' => 300, 'msg' => '存在货权转移提交成功的单据，不允许调整此库存'];
        }

        //倒罐的暂存/待审核
        $sql = "SELECT srd.sysno FROM hengyang_doc_stock_retank_detail srd
                            LEFT JOIN hengyang_doc_stock_retank sr ON srd.stockretank_sysno = sr.sysno
                            WHERE sr.isdel = 0 AND sr.stockretankstatus IN (2,3,6) AND srd.out_stock_sysno IN ($id)";
        $stockretankres = $this ->dbh ->select_one($sql);
        if ($stockretankres) {
            $this->dbh->rollback();
            return ['statusCode' => 300, 'msg' => '存在倒罐的暂存/待审核的单据，不允许调整此库存'];
        }

        //清库暂存/待审核
        $sql = "SELECT scd.sysno FROM hengyang_doc_stock_clear_detail scd
                            LEFT JOIN hengyang_doc_stock_clear sc ON scd.stockclear_sysno = sc.sysno
                            WHERE sc.isdel = 0 AND sc.stockclearstatus IN (2,3,6) AND scd.stock_sysno IN ($id)";
        $stockclearres = $this ->dbh ->select_one($sql);
        if ($stockclearres) {
            $this->dbh->rollback();
            return ['statusCode' => 300, 'msg' => '存在清库提交成功的单据，不允许调整此库存'];
        }
        //退货暂存/待审核
        $sql = "SELECT srd.sysno FROM hengyang_doc_stock_reback_detail srd
                            LEFT JOIN hengyang_doc_stock_reback sr ON srd.stockreback_sysno = sr.sysno
                            LEFT JOIN hengyang_doc_stock_out_detail sod ON srd.stockoutdetail_sysno = sod.sysno
                            WHERE sr.isdel = 0 AND sr.stockinstatus IN (2,3,6) AND sod.stock_sysno IN ($id)";
        $stockrebackres = $this ->dbh ->select_one($sql);
        if ($stockrebackres) {
            $this->dbh->rollback();
            return ['statusCode' => 300, 'msg' => '存在退货暂存/待审核的单据，不允许调整此库存'];
        }

        return ['statusCode' => 200];
    }

    /*
     * 新增库存调整订单
     */
    public function addStockadjust($data, $stockadjustdetaildata,$stockcheckstatus)
    {
        $this->dbh->begin();
        try {
            $stockcheck_sysno = $this->dbh->insert(DB_PREFIX.'doc_stock_check', $data);
            if (!$stockcheck_sysno) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '添加主表失败'];
            }

            if(!empty($stockadjustdetaildata))
                foreach ($stockadjustdetaildata as $value) {
                    $res = $this ->checkstockisquotes($value['stock_sysno']);
                    if($res['statusCode'] == '300'){
                        return ['statusCode' => 300, 'msg' => $res['msg']];
                    }

                    $input = array(
                        'stockcheck_sysno' => $stockcheck_sysno,
                        'stockinno' => $value['stockinno'],
                        'shipname' => $value['shipname'],
                        'qualityname' => $value['qualityname'],
                        'goodsnature' => $value['goodsnature'],
                        'instockqty' => $value['instockqty'],
                        'stockqty' => $value['stockqty'],
                        'stock_sysno' => $value['stock_sysno'],
                        'memo' => $value['memo'],
                        'status' => 1,
                        'isdel' => 0,
                        'version' => 1,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_stock_check_detail', $input);
                    if (!$res) {
                        $this->dbh->rollback();
                        return ['statusCode' => 300, 'msg' => '添加明细失败'];
                    }
                }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
            $input = array(
                'doc_sysno' => $stockcheck_sysno,
                'doctype' => 33,
                'opertype'=>0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc'=>'新建库存调整订单'
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if($stockcheckstatus ==2){
                $input['opertype']=1;
                $input['operdesc']='暂存库存调整订单';
            }elseif($stockcheckstatus ==3){
                $input['opertype']=2;
                $input['operdesc']='提交库存调整订单';
            }
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '添加操作记录失败'];
            }

            $this->dbh->commit();
            return ['statusCode' => 200, 'msg' => $stockcheck_sysno];
//            return $stockcheck_sysno;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     *更新库存调整订单
     */
    public function updatestockadjust($id, $data, $stockadjustdetaildata,$stockcheckstatus){
        $this->dbh->begin();
        try {
            $stockadjustinfo = $this->getStockadjustById($id);
            if ($stockcheckstatus == 2&&$stockadjustinfo['stockinstatus']!=7) {
                $data['stockcheckstatus'] = 2;
            }elseif($stockcheckstatus == 3){
                $data['stockcheckstatus'] = 3;
            }elseif($stockcheckstatus == 8){
                $data['stockcheckstatus'] = 8;
            }
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_check', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '更新主表失败'];
            }
            $detailresult = $this->dbh->delete(DB_PREFIX.'doc_stock_check_detail', 'stockcheck_sysno=' . intval($id));
            if (!$detailresult) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '删除明细失败'];
            }

            if(!empty($stockadjustdetaildata))
                foreach ($stockadjustdetaildata as $value) {
                    $res = $this ->checkstockisquotes($value['stock_sysno']);
                    if($res['statusCode'] == '300'){
                        return ['statusCode' => 300, 'msg' => $res['msg']];
                    }

                    $input = array(
                        'stockcheck_sysno' => $id,
                        'stockinno' => $value['stockinno'],
                        'shipname' => $value['shipname'],
                        'qualityname' => $value['qualityname'],
                        'goodsnature' => $value['goodsnature'],
                        'instockqty' => $value['instockqty'],
                        'stockqty' => $value['stockqty'],
                        'stock_sysno' => $value['stock_sysno'],
                        'memo' => $value['memo'],
                        'status' => 1,
                        'isdel' => 0,
                        'version' => 1,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_stock_check_detail', $input);
                    if (!$res) {
                        $this->dbh->rollback();
                        return ['statusCode' => 300, 'msg' => '添加明细失败'];
                    }
                }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 33,
                'opertype' =>2,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
            );

            if($stockcheckstatus ==2){
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存库存调整订单';
            }elseif($stockcheckstatus ==3){
                $input['opertype'] = 2;
                $input['operdesc'] = '提交库存调整订单';
                #添加提示信息
//                $stock_data = $this->getstockadjustById($id);
//                $booking = new BookingModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
//                $res = $booking->shipinsertmes($stock_data);
//                if (!$res) {
//                    $this->dbh->rollback();
//                    return ['statusCode' => 300, 'msg' => '添加操作记录失败'];
//                }
                #添加提示信息end
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '添加操作记录失败'];
            }

            $this->dbh->commit();
            return ['statusCode' => 200, 'msg' => $id];
//            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 删除库存调整订单
     */
    public function delStockadjust($id,$data){
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_check', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 33,
                'opertype' =>5,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => '删除库存调整订单',
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $id;
        }catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 审核库存调整订单
     */
    public function auditStockadjust($id, $data,$stockadjustdetaildata){
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_check', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '更新主表失败'];
            }

            //审核通过
            if($data['stockcheckstatus'] == 4){
                foreach($stockadjustdetaildata as $value){
                    $res = $this ->checkstockisquotes($value['stock_sysno']);
                    if($res['statusCode'] == '300'){
                        $this->dbh->rollback();
                        return ['statusCode' => 300, 'msg' => $res['msg']];
                    }

                    //添加储罐货品日志记录
                    $L = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                    $sql = "SELECT s.sysno,s.goods_sysno,s.goodsname,s.storagetank_sysno,bs.storagetankname,s.customer_sysno,s.customername,
                            s.stockqty,s.firstfrom_sysno,s.firstfrom_no
                            FROM hengyang_storage_stock s
                            LEFT JOIN hengyang_base_storagetank bs ON bs.sysno = s.storagetank_sysno
                            WHERE s.sysno = {$value['stock_sysno']}";
                    $stockdata = $this ->dbh ->select_row($sql);
                    if(!$stockdata){
                        $this->dbh->rollback();
                        return ['statusCode' => 300, 'msg' => '添加操作记录失败'];
                    }

                    $recordlogData = array(
                        'shipname' => $value['shipname'],
                        'goods_sysno' => $stockdata['goods_sysno'],
                        'goodsname' => $stockdata['goodsname'],
                        'storagetank_sysno' => $stockdata['storagetank_sysno'],
                        'storagetankname' => $stockdata['storagetankname'],
                        'customer_sysno' => $stockdata['customer_sysno'],
                        'customername' => $stockdata['customername'],
                        'beqty' => - $stockdata['stockqty'],
                        'stockin_sysno' => $stockdata['firstfrom_sysno'],
                        'stockinno' => $stockdata['firstfrom_no'],
                        'doc_sysno' => $id,
                        'docno' => $data['stockcheckno'],
                        'accountstoragetank_sysno' => $stockdata['storagetank_sysno'],
                        'accountstoragetankname' => $stockdata['storagetankname'],
                        'tobeqty' => - $value['stockqty'],
                        'doc_type' => 25,
                        'stock_sysno' => $value['stock_sysno'],
                        'goodsnature' => $value['goodsnature'],
                    );

                    $res = $L->addGoodsRecordLog($recordlogData);
                    if($res['code'] == '300'){
                        $this->dbh->rollback();
                        return ['statusCode' => 300, 'msg' => $res['message']];
                    }

                    //更新库存
                    $sql = "UPDATE hengyang_storage_stock SET stockqty = 0 WHERE sysno = {$value['stock_sysno']}";
                    $this ->dbh ->exe($sql);

                }
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 33,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'operdesc' =>$data['auditreason'],
                'opertime' => '=NOW()',
            );

            if($data['stockcheckstatus']==4){
                $input['opertype'] = 3;
            }elseif($data['stockcheckstatus']==6){
                $input['opertype'] = 5;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '添加操作记录失败'];
            }

            $this->dbh->commit();
            return ['statusCode' => 200, 'msg' => $id];
//            return $res;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     * 作废库存调整订单
     */
    public function blankStockadjust($id, $data,$stockadjustdetaildata){
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_check', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '更新主表失败'];
            }

            foreach($stockadjustdetaildata as $value){
                $sql = "SELECT beqty FROM hengyang_doc_goods_record_log WHERE doc_type = 25 AND doc_sysno = $id";
                $stockqty = $this ->dbh ->select_one($sql);
                if(empty($stockqty)){
                    $this->dbh->rollback();
                    return ['statusCode' => 300, 'msg' => '查询库存失败'];
                }

                //更新库存
                $sql = "UPDATE hengyang_storage_stock SET stockqty = -$stockqty WHERE sysno = {$value['stock_sysno']}";
                $this ->dbh ->exe($sql);

            }

            //删除record记录
            $sql = "UPDATE hengyang_doc_goods_record_log SET isdel = 1 WHERE doc_type = 23 AND doc_sysno = $id";
            $this ->dbh ->exe($sql);

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 33,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'operdesc' =>$data['abandonreason'],
                'opertype'=>4,
                'opertime' => '=NOW()',
            );
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['statusCode' => 300, 'msg' => '添加操作记录失败'];
            }

            $this->dbh->commit();
            return ['statusCode' => 200, 'msg' => $id];
//            return $res;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

}
<?php

/**
 * Created by PhpStorm.
 * User: 129
 * Date: 2017/7/6
 * Time: 15:50
 */
class StockberthinModel
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
     *搜索靠泊装货订单
     */
    public function searchStockberthin($params)
    {
        $filter = array();
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " b.`stockindate` >= '{$params['begin_time']}' ";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " b.`stockindate` <= '{$params['end_time']}' ";
        }
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " b.`stockinno` LIKE '%{$params['bar_no']}%' ";
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
        if (isset($params['bar_stockinstatus']) && $params['bar_stockinstatus'] != '-100') {
            $filter[] = " b.`stockinstatus`= {$params['bar_stockinstatus']} ";
        }
        if (isset($params['bar_stockintype']) && $params['bar_stockintype'] != '') {
            $filter[] = " b.`stockintype`='{$params['bar_stockintype']}'";
        }

        $where = 'b.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = " order by b.updated_at desc";

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_stock_in` b
                left join `".DB_PREFIX."customer` c on b.`customer_sysno`=c.`sysno` where {$where} ";

        $result = $params;
        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT b.*, bd.`goodsnature`,b.`inshipname`,sum(bd.`tobeqty`) as tobeqty,sum(bd.`beqty`) as beqty,bd.`storagetank_sysno`,
                        (select goodsname from ".DB_PREFIX."base_goods bg where bg.`sysno` = bd.`goods_sysno`) as goodsname,
                        (select qualityname from ".DB_PREFIX."base_goods_quality gq where gq.`sysno`=bd.`goods_quality_sysno` ) as goods_quality_name,
                        (select storagetankname from ".DB_PREFIX."base_storagetank bs where bs.`sysno`=bd.`storagetank_sysno` ) as storagetankname
                        FROM `".DB_PREFIX."doc_stock_in` b left join `".DB_PREFIX."customer` c on (b.`customer_sysno`=c.`sysno`)
                        left join `".DB_PREFIX."doc_stock_in_detail` bd on (b.`sysno`=bd.`stockin_sysno`)
                        where {$where} group by b.`sysno` $order";

                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT b.*, bd.`goodsnature`,b.`inshipname`,sum(bd.`tobeqty`) as tobeqty,sum(bd.`beqty`) as beqty,bd.`storagetank_sysno`,
                        (select goodsname from ".DB_PREFIX."base_goods bg where bg.`sysno` = bd.`goods_sysno`) as goodsname,
                        (select qualityname from ".DB_PREFIX."base_goods_quality gq where gq.`sysno`=bd.`goods_quality_sysno` ) as goods_quality_name,
                        (select storagetankname from ".DB_PREFIX."base_storagetank bs where bs.`sysno`=bd.`storagetank_sysno` ) as storagetankname
                        FROM `".DB_PREFIX."doc_stock_in` b left join `".DB_PREFIX."customer` c on (b.`customer_sysno`=c.`sysno`)
                        left join `".DB_PREFIX."doc_stock_in_detail` bd on (b.`sysno`=bd.`stockin_sysno`)
                        where {$where} group by b.`sysno` $order";

                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    /*
     * 根据id查找靠泊装货订单
     */
    public function getstockberthinById($id){
        $sql = "SELECT * FROM `".DB_PREFIX."doc_stock_in` where `sysno`= " . intval($id) . "";
        return $this->dbh->select_row($sql);
    }

    /*
     *根据id查找靠泊装货订单详情
     */
    public function getStockberthindetailById($id){
        $sql = "SELECT * FROM `".DB_PREFIX."doc_stock_in_detail` where `stockin_sysno`= " . intval($id) . "";
        return $this->dbh->select($sql);
    }

    /*
     *根据预约id查找管线详情
     */
    public function getpipdetailByBookingId($id){
        $sql = "SELECT pd.* FROM `".DB_PREFIX."doc_pipelineorder_detail` pd
                LEFT JOIN ".DB_PREFIX."doc_pipelineorder dp ON dp.sysno = pd.pipelineorder_sysno
                where dp.isdel = 0 and dp.businesstype=15 and dp.`booking_sysno`= " . intval($id) . "";
        return $this->dbh->select($sql);
    }

    /*
     *根据预约id查找泊位详情
     */
    public function getberdetailByBookingId($id){
        $sql = "SELECT pd.* FROM `".DB_PREFIX."doc_berthorder_detail` pd
                LEFT JOIN ".DB_PREFIX."doc_berthorder dp ON dp.sysno = pd.berthorder_sysno
                where dp.isdel = 0 and dp.businesstype=15 and dp.`booking_sysno`= " . intval($id) . "";
        return $this->dbh->select($sql);
    }

    /*
     * 新增靠泊装货订单
     */
    public function addStockberthin($data, $stockcarindetaildata,$stockinstatus)
    {
        $this->dbh->begin();
        try {
            $sql = 'select * from `'.DB_PREFIX.'doc_booking_in` where sysno=' . intval($data['booking_in_sysno']);
            $bookdata = $this->dbh->select_row($sql);
            if (!$bookdata) {
                $this->dbh->rollback();
                return false;
            }
            if ($bookdata['issaveorder']) {
                $this->dbh->rollback();
                return false;
            }

            if($stockinstatus ==2){
                $data['stockinstatus']=2;
            }elseif($stockinstatus ==3){
                $data['stockinstatus']=3;
            }

            $stockin_sysno = $this->dbh->insert(DB_PREFIX.'doc_stock_in', $data);
            if (!$stockin_sysno) {
                $this->dbh->rollback();
                return false;
            }

            if(!empty($stockcarindetaildata))
                foreach ($stockcarindetaildata as $value) {
                    $input = array(
                        'stockin_sysno' => $stockin_sysno,
                        'goods_sysno' => $value['goods_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'shipbookingdate' => $value['bookingindate'],
                        'tobeqty' => $value['bookinginqty'],
                        'beqty' => $value['beqty'],
                        'goodsname' => $value['goodsname'],
                        'unitname' => '吨',
                        'memo' => $value['memo'],
                        'status' => 1,
                        'isdel' => 0,
                        'version' => 1,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_stock_in_detail', $input);

                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }

            //获取当前靠泊装卸单的信息，判断是否要生成品质检查单据、管线分配单、泊位分配单

            $data['ispipelineorder'] = $bookdata['ispipelineorder'];
            $data['isberthorder'] = $bookdata['isberthorder'];
            $data['sysno']=$data['booking_in_sysno'];
            $data['bookinginno']=$data['bookingin_no'];
            $data['stock_sysno']=$stockin_sysno;
            $data['stockno']=$data['stockinno'];
            $data['bookingindate']=$data['stockindate'];

            $bookshipinInstance = new BookshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $res = $bookshipinInstance->createThreeBill($data,15);

            if($res['code']!=200){
                return ['code'=>300, 'message'=>$res];
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
            $input = array(
                'doc_sysno' => $stockin_sysno,
                'doctype' => 27,
                'opertype'=>0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc'=>'新建靠泊装货订单'
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            if($stockinstatus ==2){
                $input['opertype']=1;
                $input['operdesc']='暂存靠泊装货订单';
            }elseif($stockinstatus ==3){
                $input['opertype']=2;
                $input['operdesc']='提交靠泊装货订单';
            }
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return $stockin_sysno;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    /*
     *更新靠泊装货订单
     */
    public function updatestockberthin($id, $data, $stockberthindetaildata,$stockinstatus){
        $this->dbh->begin();
        try {
            $S = new StockberthinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $stockberthininfo = $S->getStockberthinById($id);
            if ($stockinstatus == 2&&$stockberthininfo['stockinstatus']!=7) {
                $data['stockinstatus'] = 2;
            }elseif($stockinstatus == 3){
                $data['stockinstatus'] = 3;
            }elseif($stockinstatus == 8){
                $data['stockinstatus'] = 8;
            }

            $res = $this->dbh->update(DB_PREFIX.'doc_stock_in', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $detailresult = $this->dbh->delete(DB_PREFIX.'doc_stock_in_detail', 'stockin_sysno=' . intval($id));
            if (!$detailresult) {
                $this->dbh->rollback();
                return false;
            }

            if(!empty($stockberthindetaildata))
                foreach ($stockberthindetaildata as $value) {
                    $input = array(
                        'stockin_sysno' => $id,
                        'goods_sysno' => $value['goods_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'shipbookingdate' => $value['shipbookingdate'],
                        'shipactualdate' => $value['shipactualdate'],
                        'tobeqty' => $value['tobeqty'],
                        'beqty' => $value['beqty'],
                        'goodsname' => $value['goodsname'],
                        'unitname' => '吨',
                        'memo' => $value['memo'],
                        'status' => 1,
                        'isdel' => 0,
                        'version' => 1,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_stock_in_detail', $input);
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
                'doctype' => 27,
                'opertype' =>2,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
            );

            if($stockinstatus ==2){
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存靠泊装货订单';
            }elseif($stockinstatus ==3){
                $input['opertype'] = 2;
                $input['operdesc'] = '提交靠泊装货订单';
                #添加提示信息
                $stock_data = $this->getstockberthinById($id);
                $booking = new BookingModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $res = $booking->shipinsertmes($stock_data);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
                #添加提示信息end 
            }elseif($stockinstatus ==8){
                $input['opertype'] = 9;
                $input['operdesc'] = '驳回靠泊装订单';
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
     * 审核靠泊装货订单
     */
    public function auditStockberthin($id, $data,$stockberthindetaildata){
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_in', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            //添加靠泊装货费用
            $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $F = new FinancecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $goods_sysno = $stockberthindetaildata[0]['goods_sysno'];
            $shipname = $stockberthindetaildata[0]['shipname'];
            $costqty = 0 ;
            foreach($stockberthindetaildata as $item){
                $costqty +=$item['beqty'];
            }
            $unitprice = $C ->getberthcost($data['contract_sysno'],$goods_sysno,1);
            $berthindata = array(
                'contractcostdate'=>$data['stockindate'],
                'costqty'=>$costqty,
                'unitprice'=>$unitprice['berthcost'],
                'goods_sysno'=>$goods_sysno,
                'shipname' =>$shipname,
                'customer_sysno'=>$data['customer_sysno'],
                'customer_name'=>$data['customername'],
                'contract_sysno'=>$data['contract_sysno'],
                'contract_no'=>$data['contractno']
            );
            $res = $F->addFinancecostByBerth($berthindata,1,1);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 27,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'operdesc' =>$data['auditreason'],
                'opertime' => '=NOW()',
            );

            if($data['stockinstatus']==4){
                $input['opertype'] = 3;
            }elseif($data['stockinstatus']==6){
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
     * 退回靠泊装货订单
     */
    public function backStockberthin($id, $data,$backreason){
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_in', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $dataildata = $this->getstockberthinById($id);
            $bookingdata = array(
                'bookinginstatus'=>7,
                'backreason'=>$backreason
            );

            $res = $this->dbh->update(DB_PREFIX.'doc_booking_in', $bookingdata, 'sysno=' . intval($dataildata['booking_in_sysno']));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 27,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'operdesc' =>$data['backreason'],
                'opertype'=>5,
                'opertime' => '=NOW()',
            );
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $input = array(
                'doc_sysno' => $dataildata['booking_in_sysno'],
                'doctype' => 18,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'operdesc' =>$data['backreason'],
                'opertype'=>5,
                'opertime' => '=NOW()',
            );
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
     * 作废靠泊装货订单
     */
    public function blankStockberthin($id, $data){
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_in', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            #删除费用


            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 27,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'operdesc' =>$data['backreason'],
                'opertype'=>4,
                'opertime' => '=NOW()',
            );
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

}
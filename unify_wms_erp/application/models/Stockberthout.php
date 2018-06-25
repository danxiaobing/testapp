<?php

/**
 * Created by PhpStorm.
 * User: 129
 * Date: 2017/7/6
 * Time: 17:03
 */
class StockberthoutModel
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
     *搜索靠泊卸货订单
     */
    public function searchStockberthout($params)
    {
        $filter = array();
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " b.`stockoutdate` >= '{$params['begin_time']}' ";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " b.`stockoutdate` <= '{$params['end_time']}' ";
        }
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " b.`stockoutno` LIKE '%{$params['bar_no']}%' ";
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
        if (isset($params['bar_stockoutstatus']) && $params['bar_stockoutstatus'] != '-100') {
            $filter[] = " b.`stockoutstatus`= {$params['bar_stockoutstatus']} ";
        }
        if (isset($params['bar_stockouttype']) && $params['bar_stockouttype'] != '') {
            $filter[] = " b.`stockouttype`='{$params['bar_stockouttype']}'";
        }

        $where = 'b.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = " order by b.updated_at desc";

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_stock_out` b
                left join `".DB_PREFIX."customer` c on b.`customer_sysno`=c.`sysno` where {$where} ";

        $result = $params;
        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT b.*, bd.`goodsnature`,bd.`shipname`,sum(bd.`tobeqty`) as tobeqty,sum(bd.`beqty`) as beqty,bd.`storagetank_sysno`,
                        (select goodsname from ".DB_PREFIX."base_goods bg where bg.`sysno` = bd.`goods_sysno`) as goodsname,
                        (select qualityname from ".DB_PREFIX."base_goods_quality gq where gq.`sysno`=bd.`goods_quality_sysno` ) as goods_quality_name,
                        (select storagetankname from ".DB_PREFIX."base_storagetank bs where bs.`sysno`=bd.`storagetank_sysno` ) as storagetankname
                        FROM `".DB_PREFIX."doc_stock_out` b left join `".DB_PREFIX."customer` c on (b.`customer_sysno`=c.`sysno`)
                        left join `".DB_PREFIX."doc_stock_out_detail` bd on (b.`sysno`=bd.`stockout_sysno`)
                        where {$where} group by b.`sysno` $order";

                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT b.*, bd.`goodsnature`,bd.`shipname`,sum(bd.`tobeqty`) as tobeqty,sum(bd.`beqty`) as beqty,bd.`storagetank_sysno`,
                        (select goodsname from ".DB_PREFIX."base_goods bg where bg.`sysno` = bd.`goods_sysno`) as goodsname,
                        (select qualityname from ".DB_PREFIX."base_goods_quality gq where gq.`sysno`=bd.`goods_quality_sysno` ) as goods_quality_name,
                        (select storagetankname from ".DB_PREFIX."base_storagetank bs where bs.`sysno`=bd.`storagetank_sysno` ) as storagetankname
                        FROM `".DB_PREFIX."doc_stock_out` b left join `".DB_PREFIX."customer` c on (b.`customer_sysno`=c.`sysno`)
                        left join `".DB_PREFIX."doc_stock_out_detail` bd on (b.`sysno`=bd.`stockout_sysno`)
                        where {$where} group by b.`sysno` $order";

                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    /*
     *根据id查找靠泊卸货订单
     */
    public function getStockberthoutById($id){
        $sql = "SELECT * FROM `".DB_PREFIX."doc_stock_out` where `sysno`= " . intval($id) . "";
        return $this->dbh->select_row($sql);
    }

    /*
     *根据id查找靠泊装货订单详情
     */
    public function getStockberthoutdetailById($id){
        $sql = "SELECT * FROM `".DB_PREFIX."doc_stock_out_detail` where `stockout_sysno`= " . intval($id) . "";
        return $this->dbh->select($sql);
    }

    /*
    *根据bookid查找泊位
    */
    public function getberthByBookId($id){
        $sql = "SELECT * FROM `".DB_PREFIX."doc_berthorder_detail`  bd
                left join ".DB_PREFIX."doc_berthorder b on bd.berthorder_sysno = b.sysno
                where b.isdel = 0 and b.businesstype=16 and b.`stock_sysno`= " . intval($id) . "";
        return $this->dbh->select($sql);
    }

    /*
  *根据bookid查找管线
  */
    public function getpipeByBookId($id){
        $sql = "SELECT pd.* FROM `".DB_PREFIX."doc_pipelineorder_detail`  pd
                left join ".DB_PREFIX."doc_pipelineorder p on pd.pipelineorder_sysno = p.sysno
                where p.isdel = 0 and p.businesstype=16 and p.`stock_sysno`= " . intval($id) . "";
        return $this->dbh->select($sql);
    }

    /*
     * 新增靠泊卸货订单
     */
    public function addStockberthout($bookout_sysno,$input, $stockberthoutdetaildata,$stockoutstatus)
    {
        $this->dbh->begin();
        try {
            $sql = 'select * from `' . DB_PREFIX . 'doc_booking_out` where sysno=' . intval($bookout_sysno);
            $bookdata = $this->dbh->select_row($sql);
            if (!$bookdata) {
                $this->dbh->rollback();
                return ['statusCode'=>'300','msg'=>'查询预约单失败'];
            }
            if ($bookdata['issaveorder']==1) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'已生成过靠泊卸货订单'];
            }

            $input['bookingoutno'] = $input['bookingout_no'];
            unset($input['bookingout_no']);
            $res = $this->dbh->insert(DB_PREFIX . 'doc_stock_out', $input);
            $id = $res;
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'生成靠泊卸货订单失败'];
            }

            if (!empty($stockberthoutdetaildata))
                foreach ($stockberthoutdetaildata as $value) {
                    $detailinput = array(
                        'stockout_sysno' => $id,
                        'goods_sysno' => $value['goods_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'outdate' => $value['shipokdate'],
                        'shipbookingdate'=> $value['shipokdate'],
                        'tobeqty' => $value['bookingoutqty'],
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
                    $res = $this->dbh->insert(DB_PREFIX . 'doc_stock_out_detail', $detailinput);
                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }

            //获取当前靠泊卸货单的信息，判断是否要生成品质检查单据、管线分配单、泊位分配单
            $data['ispipelineorder'] = $bookdata['ispipelineorder'];
            $data['isberthorder'] = $bookdata['isberthorder'];
            $data['sysno'] = $bookout_sysno;
            $data['bookinginno'] = $input['bookingoutno'];
            $data['stock_sysno']=$id;
            $data['stockno']=$input['bookingoutno'];
            $data['bookingindate']=$input['stockoutdate'];

            $bookshipinInstance = new BookshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $res = $bookshipinInstance->createThreeBill($data,16);
            if($res['code']!=200){
                return ['code'=>300, 'message'=>$res];
            }

            #靠泊装货操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 28,
                'opertype' => 0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => '新建靠泊装货订单'
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'更新日志失败'];
            }

            if ($stockoutstatus == 2) {
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存靠泊装货订单';
            } elseif ($stockoutstatus == 3) {
                $input['opertype'] = 2;
                $input['operdesc'] = '提交靠泊装货订单';
            }
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'更新日志失败'];
            }

            $this->dbh->commit();
            return  ['statusCode'=>200,'msg'=>$id];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return  ['statusCode'=>300,'msg'=>'更新失败'];
        }
    }

    /*
     *更新靠泊卸货订单
     */
    public function updatestockberthout($id, $data, $stockberthoutdetaildata,$stockoutstatus){
        $this->dbh->begin();
        try {
            $S = new StockberthoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $stockberthoutinfo = $S->getStockberthoutById($id);
            if ($stockoutstatus == 2&&$stockberthoutinfo['stockoutstatus']!=7) {
                $data['stockoutstatus'] = 2;
            }elseif($stockoutstatus == 3){
                $data['stockoutstatus'] = 3;
            }elseif($stockoutstatus == 8){
                $data['stockoutstatus'] = 8;
            }

            $res = $this->dbh->update(DB_PREFIX.'doc_stock_out', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $detailresult = $this->dbh->delete(DB_PREFIX.'doc_stock_out_detail', 'stockout_sysno=' . intval($id));
            if (!$detailresult) {
                $this->dbh->rollback();
                return false;
            }

            if(!empty($stockberthoutdetaildata))
                foreach ($stockberthoutdetaildata as $value) {
                    $input = array(
                        'stockout_sysno' => $id,
                        'goods_sysno' => $value['goods_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'tobeqty' => $value['tobeqty'],
                        'beqty' => $value['beqty'],
                        'goodsname' => $value['goodsname'],
                        'shipbookingdate' => $value['shipbookingdate'],
                        'shipactualdate' => $value['shipactualdate'],
                        'unitname' => '吨',
                        'memo' => $value['memo'],
                        'status' => 1,
                        'isdel' => 0,
                        'version' => 1,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_stock_out_detail', $input);
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
                'doctype' => 28,
                'opertype' =>2,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
            );

            if($stockoutstatus ==2){
                $input['opertype'] = 1;
                $input['operdesc'] = '暂存靠泊卸货订单';
            }elseif($stockoutstatus ==3){
                $input['opertype'] = 2;
                $input['operdesc'] = '提交靠泊卸货订单';
            }elseif($stockoutstatus ==8){
                $input['opertype'] = 9;
                $input['operdesc'] = '驳回靠泊卸货订单';
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
     * 审核靠泊卸货订单
     */
    public function auditStockberthout($id, $data,$stockberthoutdetaildata){
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_out', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            //添加靠泊装货费用
            $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $F = new FinancecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            foreach($stockberthoutdetaildata as $item){
                $goods_sysno = $item['goods_sysno'];
                $shipname = $stockberthoutdetaildata[0]['shipname'];
                $bercost = $C ->getberthcost($data['contract_sysno'],$goods_sysno,2);
                if($item['goodsnature']==4){
                    $unitprice = $bercost['berthcostdomestic'];
                }else{
                    $unitprice = $bercost['berthcostforeign'];
                }

                $berthoutdata = array(
                    'contractcostdate'=>$data['stockoutdate'],
                    'costqty'=>$item['beqty'],
                    'goodsnature'=>$item['goodsnature'],
                    'unitprice'=>$unitprice,
                    'goods_sysno'=>$goods_sysno,
                    'shipname' =>$shipname,
                    'customer_sysno'=>$data['customer_sysno'],
                    'customer_name'=>$data['customername'],
                    'contract_sysno'=>$data['contract_sysno'],
                    'contract_no'=>$data['contractno'],
                );

                $res = $F->addFinancecostByBerth($berthoutdata,2,1);
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
                'doctype' => 28,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'operdesc' =>$data['auditreason'],
                'opertime' => '=NOW()',
            );

            if($data['stockoutstatus']==4){
                $input['opertype'] = 3;
            }elseif($data['stockoutstatus']==6){
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
     * 退回靠泊卸货订单
     */
    public function backStockberthout($id, $data,$backreason){
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_out', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $dataildata = $this->getstockberthoutById($id);
            $bookingdata = array(
                'bookingoutstatus'=>7,
                'backreason'=>$backreason
            );

            $res = $this->dbh->update(DB_PREFIX.'doc_booking_out', $bookingdata, 'sysno=' . intval($dataildata['booking_out_sysno']));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 28,
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
                'doctype' => 19,
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
     * 作废靠泊卸货订单
     */
    public function blankStockberthout($id, $data){
        $this->dbh->begin();
        try {
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_out', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            #操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 28,
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
<?php

/**
 * Stockshipin Model
 *
 */
class StockpipeinModel
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

    public function searchStockpipein($params)
    {
        $filter = array();
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " s.`stockinno` LIKE '%{$params['bar_no']}%' ";
        }
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " s.`customername` LIKE '%{$params['bar_name']}%' ";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
            $filter[] = " s.`status`='{$params['bar_status']}'";
        }
        if (isset($params['bar_isdel']) && $params['bar_isdel'] != '-100') {
            $filter[] = " s.`isdel`='{$params['bar_isdel']}'";
        }
        if (isset($params['bar_stockintype']) && $params['bar_stockintype'] != '') {
            $filter[] = " s.`stockintype`='{$params['bar_stockintype']}'";
        }
        if (isset($params['bar_stockinstatus']) && $params['bar_stockinstatus'] != '-100') {
            $filter[] = " s.`stockinstatus`='{$params['bar_stockinstatus']}'";
        }
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " s.`stockindate`>='{$params['begin_time']}'";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " s.`stockindate`<='{$params['end_time']}'";
        }
          $where = 's.isdel=0 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  FROM (select c.* from `".DB_PREFIX."doc_stock_in` s left join `".DB_PREFIX."customer` c on (s.`customer_sysno`=c.`sysno`) left join `".DB_PREFIX."doc_stock_in_detail` sd on (s.`sysno`=sd.`stockin_sysno`) where {$where} group by sd.`stockin_sysno`) temp";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT s.*,sd.`goodsnature`,sd.`shipname`,sd.`carid`,sum(sd.tobeqty) as tobeqty," .
                    "sum(sd.shipcheckqty) as shipcheckqty,SUM(sd.`beqty`)as beqty,sd.`goodsreceiptdate`,unit.unitname," .
                    "(select goodsname from ".DB_PREFIX."base_goods bg where bg.`sysno` = sd.`goods_sysno`) as goodsname " .
                    " FROM `".DB_PREFIX."doc_stock_in` s " .
                    "left join `".DB_PREFIX."customer` c on (s.`customer_sysno`=c.`sysno`) " .
                    "left join `".DB_PREFIX."doc_stock_in_detail` sd on (s.`sysno`=sd.`stockin_sysno`) " .
                    "LEFT JOIN  ".DB_PREFIX."base_goods_attribute bga on (bga.goods_sysno = sd.goods_sysno and bga.isdel=0)" .
                    "LEFT JOIN  ".DB_PREFIX."base_unit as unit on unit.sysno = bga.unit_sysno " .
                    " where {$where}" .
                    "group by sd.`stockin_sysno` ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by created_at desc";
                }
                #var_dump($sql);exit();
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT s.*,sd.`goodsnature`,sd.`shipname`,sd.`carid`,sum(sd.tobeqty) as tobeqty," .
                    "sum(sd.shipcheckqty) as shipcheckqty,SUM(sd.`beqty`)as beqty,sd.`goodsreceiptdate`," .
                    "unit.unitname,(select goodsname from ".DB_PREFIX."base_goods bg where bg.`sysno` = sd.`goods_sysno`) as goodsname " .
                    "FROM `".DB_PREFIX."doc_stock_in` s " .
                    "left join `".DB_PREFIX."customer` c on (s.`customer_sysno`=c.`sysno`) " .
                    "left join `".DB_PREFIX."doc_stock_in_detail` sd on (s.`sysno`=sd.`stockin_sysno`) " .
                    "LEFT JOIN  ".DB_PREFIX."base_goods_attribute bga on (bga.goods_sysno = sd.goods_sysno and bga.isdel=0)" .
                    "LEFT JOIN  ".DB_PREFIX."base_unit as unit on unit.sysno = bga.unit_sysno " .
                    " where {$where} " .
                    "group by sd.`stockin_sysno` ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by s.created_at desc";
                }
                #var_dump($sql);exit();
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    public function getStockshipinById($id)
    {
        $sql = "SELECT s.*,(select goodsname from ".DB_PREFIX."base_goods bg where bg.`sysno` = sd.`goods_sysno`) as goodsname
                FROM `".DB_PREFIX."doc_stock_in` s
                left join `".DB_PREFIX."customer` c on (s.`customer_sysno`=c.`sysno`)
                left join `".DB_PREFIX."doc_stock_in_detail` sd on (s.`sysno`=sd.`stockin_sysno`)
                where s.`stockintype`=3 and s.`sysno`=" . intval($id);
        return $this->dbh->select_row($sql);
    }

    public function addStockshipin($data, $stockshipindetaildata, $stockmarks = '')
    {
        $this->dbh->begin();
        try {
            #锁定表行
            // $tables[] = array('tbl'=>DB_PREFIX.'doc_stock_in','alias'=>'','tp'=>2);
            // $tables[] = array('tbl'=>DB_PREFIX.'doc_stock_in_detail','alias'=>'','tp'=>2);
            // $tables[] = array('tbl'=>DB_PREFIX.'doc_log','alias'=>'l','tp'=>2);
            // $this->dbh->lock($tables);

            $sql = 'select * from `'.DB_PREFIX.'doc_booking_in` where sysno=' . intval($data['booking_in_sysno']);
            $bookdata = $this->dbh->select_row($sql);
            if (!$bookdata) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'查询预约单失败'];
            }
            if ($bookdata['issaveorder'] == 1) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'已生成过管入库单'];
            }
            $res = $this->dbh->insert(DB_PREFIX.'doc_stock_in', $data);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'生成主表失败'];
            }
            $id = $res;

            $res = $this->dbh->delete(DB_PREFIX.'doc_stock_in_detail', 'stockin_sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'删除明细失败'];
            }

            if ($data['booking_in_sysno'] != '') {
                $bking = array('bookinginstatus' => '6', 'issaveorder' => '1');
                $res = $this->dbh->update(DB_PREFIX.'doc_booking_in', $bking, 'sysno=' . $data['booking_in_sysno']);
                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'回写预约单状态失败'];
                }
            }
            foreach ($stockshipindetaildata as $value) {
                $input = array(
                    'stockin_sysno' => $id,
                    'goods_sysno' => $value['goods_sysno'],
                    'goods_quality_sysno' => $value['goods_quality_sysno'],
                    'goodsnature' => $value['goodsnature'],
                    'goodsreceiptdate' => $value['goodsreceiptdate'],
                    'tobeqty' => $value['tobeqty'],
                    'shipcheckqty' => $value['shipcheckqty'],
                    'bussinesscheckqty' => $value['beqty'],
                    'beqty' => $value['beqty'],
                    'storagetank_sysno' => $value['storagetank_sysno'],
                    'expresscompanyname' => $value['expresscompanyname'],
                    'memo' => $value['memo'],
                    'shipname' => $value['shipname'],
                    'carid' => $value['carid'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                    'goodsname' => $value['goodsname'],
                    'qualityname' => $value['goods_quality_name'],
                    'unitname' => $value['unitname'],
                    'takegoodsnum' => $value['takegoodsnum'],
                    'release_no' => $value['release_no'],
                    'shipactualdate'=>$value['shipactualdate'],
                    'shipbookingdate'=>$value['shipbookingdate'],
                );

                $res = $this->dbh->insert(DB_PREFIX.'doc_stock_in_detail', $input);
                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'生成管入库明细失败'];
                }
            }

            //判断泊位、管线、品质是否要进行分配检查，如果是更新这些单据,插入入库单的id和单号
            $updateData = array(
                'stock_sysno' => $id,
                'stockno' => $data['stockinno'],
                'businesstype' => 6,
            );

            if($bookdata['ispipelineorder'] == '1'){
                $res = $this->dbh->update(DB_PREFIX.'doc_pipelineorder',$updateData,'booking_sysno ='.intval($bookdata['sysno']));
                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'更新管分配单失败'];
                }
            }

            if($bookdata['isqualitycheck'] == '1'){
                $updateData['businesstype'] =5;
                $res = $this->dbh->update(DB_PREFIX.'doc_qualitycheck',$updateData,'booking_sysno =' .intval($bookdata['sysno']));
                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'更新品质检查单失败'];
                }
            }


            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 25,
                'opertype' => 0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $stockmarks,
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return  ['statusCode'=>300,'msg'=>'生成日志失败'];
            }
            if ($data['stockinstatus'] >= 2) {
                $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 25,
                    'opertype' => $data['stockinstatus'] - 1,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => $stockmarks,
                );

                $res = $S->addDocLog($input);
                if (!$res) {
                    $this->dbh->rollback();
                    return  ['statusCode'=>300,'msg'=>'生成日志失败2'];
                }
            }

            if($data['stockinstatus']==3)
            {
                #添加提示信息
                    $stock_data = $this->getStockshipinById($id);
                    $booking = new BookingModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                    $res = $booking->shipinsertmes($stock_data);
                    if (!$res) {
                        $this->dbh->rollback();
                        return ['statusCode'=>300,'msg'=>'添加消息提醒失败！'];;
                    }
                #添加提示信息end
            }

            $this->dbh->commit();
            // #释放锁
            // $this->dbh->unlock();
            return  ['statusCode'=>200,'msg'=>$id];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return  ['statusCode'=>300,'msg'=>'失败'];
        }
    }

    /**
     * @param $input
     * @param $stockshipindetaildata
     * @param $stockmarks
     * @title 登记船订单
     */
    public function Registershipin($input, $stockshipindetaildata, $stockmarks)
    {

    }

    public function backpipein($id, $array, $stockshipindetaildata, $beqty = 0)
    {
        $this->dbh->begin();
        try {
            // 1 更改".DB_PREFIX."doc_stock_in表 状态
            $updatestockinres = $this->dbh->update(DB_PREFIX.'doc_stock_in', $array, 'sysno = ' . intval($id));
            if (!$updatestockinres) {
                $this->dbh->rollback();
                return ['code'=>300,'message'=>'更改入库表失败'];
            }

            $tank = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc')); //储罐资料
            $S = new StockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $book = new BookshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc')); //预约船入库

           //将三单 退回
            $sql = "select * from ".DB_PREFIX."doc_stock_in where sysno = {$id}";
            $info = $this->dbh->select_row($sql);
            $booking_in_sysno = $info['booking_in_sysno'];
            $param = array(
                'businesstype'=>5,
                'stock_sysno'=>null,
                'stockno'=>null,
                'updated_at'=>'=NOW()',

            );
            if($info['ispipelineorder']==1){
              $res =  $this->dbh->update(DB_PREFIX.'doc_pipelineorder',$param,'booking_sysno = '.$booking_in_sysno);
                if (!$res) {
                    $this->dbh->rollback();
                    return ['code'=>300,'message'=>'更新管线分配单失败'];
                }
            }
            if($info['isberthorder']==1){
                $res =  $this->dbh->update(DB_PREFIX.'doc_berthorder',$param,'booking_sysno = '.$booking_in_sysno);
                if (!$res) {
                    $this->dbh->rollback();
                    return ['code'=>300,'message'=>'更新泊位分配单失败'];
                }

            }
            if($info['isqualitycheck']==1){
                $res =  $this->dbh->update(DB_PREFIX.'doc_qualitycheck',$param,'booking_sysno = '.$booking_in_sysno);
                if (!$res) {
                    $this->dbh->rollback();
                    return ['code'=>300,'message'=>'更新品质分配单失败'];
                }

            }
          //  print_r($info);die;

            foreach ($stockshipindetaildata as $item) {
                #echo '<pre>';var_dump($item);exit();
                // 2 更新可用罐容
                $tankinfo = $tank->getStoragetankById($item['storagetank_sysno']);
                // 1> 罐容的可存放量 = 当前存放量 - 当前订单的商检数量
                $tankinfo['tank_stockqty'] = $tankinfo['tank_stockqty'] - $item['beqty'];//bussinesscheckqty
                $tankinfo['updated_at'] = '=NOW()';
                $tankinfo['goods_sysno'] = $item['goods_sysno'];
                $tankinfo['goods_quality_sysno'] = $item['goods_quality_sysno'];
                // 2> 罐容的待入量 = 原有待有量 + 当前订单的预约数量
                $tankinfo['orderinqty'] = $tankinfo['orderinqty'] + $item['tobeqty'];
                unset($tankinfo['sysno']);
                unset($tankinfo['goodsname']);
                unset($tankinfo['goodsno']);
                unset($tankinfo['storagetank_categoryname']);
                unset($tankinfo['areaname']);
                #var_dump($tankinfo);exit();
                // error_log(date("Y-m-d H:i:s") . "\t" . json_encode($tankinfo) . "\n", 3, './logs/stockshipin.log');

                $updatetankres = $this->dbh->update(DB_PREFIX.'base_storagetank', $tankinfo, 'sysno = ' . intval($item['storagetank_sysno']));
                if (!$updatetankres) {
                    $this->dbh->rollback();
                    return ['code'=>300,'message'=>'更改罐容表失败'];
                }
                # 更新可用罐容 End
                // 3 更新可用库存
                $input['type'] = 14;
                $input['data'] = [
                    'stock_sysno'=>$item['stock_sysno'],
                    'stocksysno' => $item['stockin_sysno'],
                    'sysno' => $item['stock_sysno'],
                    'goodssysno' => $item['goods_sysno'],
                    'instockqty' => $item['beqty'],
                    'doctype' => 4,
                    'iscurrent' => 0,
                    'firstfrom_sysno' => $item['stockin_sysno'],
                ];
                //调用更新方法 库存表
                $res = $S->pubstockoperation($input);
                if (!$res) {
                    $this->dbh->rollback();
                    return ['code'=>300,'message'=>'更新库存表失败'];
                }
                # 更新可用库存 End
                $arr = $array;
                // 4 罐容日志表
                $stocklist = $this->getStockshipinById($id);
                $array = [
                    'doc_sysno' => $id,
                    'docno' => $stocklist['stockinno'],
                    'storagetank_sysno' => $item['storagetank_sysno'],
                    'beqty' => -$item['beqty'],
                    'doctype' => 6, #1车入库2车入库作废3车出库4车出库作废5船入库6船入库作废7船出库8船出库作废9盘点10倒罐
                    'status' => 1,
                    'isdel' => 0
                ];
                $addtanklog = $tank->addStoragetankLog($array);

                if ($addtanklog['code'] != 200) {
                    $this->dbh->rollback();
                    return ['code'=>300,'message'=>'增加罐容log日志失败'];
                }
                # 4 罐容日志表 End
                // 5 修改预约单状态 '已完成'=> 已审核
                #echo'<pre>';var_dump($stocklist['booking_in_sysno']);exit();
                $booklist = $book->getBookshipinById($stocklist['booking_in_sysno']);
                unset($booklist['sysno']);
                unset($booklist['goodsnature']);
                unset($booklist['shipname']);
                unset($booklist['goodsname']);
                unset($booklist['bookinginqty']);
                unset($booklist['storagetank_sysno']);
                unset($booklist['goods_quality_name']);
                unset($booklist['storagetankname']);
                unset($booklist['memo']);

                $booklist['updated_at'] = '=NOW()';
                $booklist['issaveorder'] = 0;
                $booklist['bookinginstatus'] = 5;

                $updatebookres = $this->dbh->update(DB_PREFIX.'doc_booking_in', $booklist, 'sysno = ' . intval($stocklist['booking_in_sysno']));

                if (!$updatebookres) {
                    $this->dbh->rollback();
                    return ['code'=>300,'message'=>'更新入库预约表失败'];
                }
                #echo'<pre>';var_dump($booklist);exit();
                # 修改预约单状态 End
            }

            // 6  作废时候 更新费用单 相关信息
            $invoice = new InvoiceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $costdetail = $invoice->getcostdetailbystockinsysno($id);
            if ($costdetail) {
                $data = array(
                    'updated_at' => '=NOW()',
                    'isdel' => 1,
                );
                if ($costdetail['sysno'] == 0) {
                    COMMON::result(
                        300, '该单据费用明细不存在！'
                    );
                }
                $res = $this->dbh->update(DB_PREFIX.'doc_finance_cost_detail', $data, 'sysno =' . intval($costdetail['sysno']));
                if (!$res) {
                    $this->dbh->rollback();
                    return ['code'=>300,'message'=>'更改".DB_PREFIX."doc_finance_cost_detail表失败'];
                }
            }
            // 7 ".DB_PREFIX."doc_log 表 记录字段更改
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

            $input = array(
                'doc_sysno' => $id,
                'doctype' => 25,
                'opertype' => 4,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $arr['abandonreason'],
            );
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'message'=>'增加操作日志失败'];
            }
            # 日志表记录 End

            #货物进出日志begin
            $res = $this->dbh->update(DB_PREFIX.'doc_goods_record_log',array('isdel'=>1),'doc_sysno= '.$id.' AND doc_type=11');
            if(!$res){
                $this->dbh->rollback();
                return ['code'=>300,'message'=>'更新货物进出日志失败'];
            }
            #货物进出日志end

            $this->dbh->commit();
            return ['code'=>200,'message'=>'更新成功!'];
        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['code'=>300,'message'=>'更新失败!'];
        }
    }

    public function updateStockpipein($id, $data = array(), $stockshipindetaildata, $stockmarks = '', $auditstep = 0, $beqty = 0)
    {
        #   $beqty实际入库量 $stockmarks 备注 $auditstep 备注
        $this->dbh->begin();
        try {
            if ($auditstep == 1) {
                $data['stockinstatus'] = 2;
            } elseif ($auditstep == 8) {
                $info = $this->getStockshipinById($id);
                $data['stockinstatus'] = $info['stockinstatus'];
            } elseif ($auditstep == 4){
                $data['auditreason'] = $stockmarks; //审核意见
            }
            $res = $this->dbh->update(DB_PREFIX.'doc_stock_in', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'message'=>'更新入库单失败!'];
            }
            #审批方法
            if ($auditstep == 4) {
                $sql = 'select * from `'.DB_PREFIX.'doc_stock_in` where sysno=' . intval($id);
                $data = $this->dbh->select_row($sql);

                if (!$data) {
                    $this->dbh->rollback();
                    return ['code'=>300,'message'=>'查询入库单详情失败!'];
                }

                foreach ($stockshipindetaildata as $key=>$value) {
                    $detailid = $value['sysno'];
                    //-- type 11 所用参数
                    $goodsname = $value['goodsname'];
                    $goodsqualityname = $value['qualityname'];
                    $stockindate = $data['stockindate'] ? $data['stockindate'] : date("Y-m-d");
                    $stockdata = array(
                        'doctype' => 4,
                        'instockdate' => $stockindate,
                        'firstdate' => date("Y-m-d", strtotime('' . $stockindate . '+30 day')),
                        'isclearstock' => 0,
                        'shipname' => $value['shipname'],
                        'customer_sysno' => $data['customer_sysno'],
                        'customername' => $data['customername'],
                        'goods_sysno' => $value['goods_sysno'],
                        'goodsname' => $goodsname,
                        'goods_quality_sysno' => $value['goods_quality_sysno'],
                        'goodsqualityname' => $goodsqualityname,
                        'storagetank_sysno' => $value['storagetank_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'instockqty' => $value['beqty'],
                        'outstockqty' => 0,
                        'stockqty' => $value['beqty'],
                        'status' => 1,
                        'isdel' => 0,
                        'version' => 1,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                        'qualityname' => $value['goods_quality_name'],
                        'unitname' => $value['unitname'],
                        'takegoodsnum' => $value['takegoodsnum'],
                        'release_no' => $value['release_no'],
                        'contract_sysno'=>$data['contract_sysno'],
                    );

                    $params['data'] = $stockdata;
                    $params['fromid'] = $id;



                    $params['firstfrom_no'] = $data['stockinno'];//入库单号
                    $params['contract_sysno'] = $stockdata['contract_sysno'];

                    $params['sysno_stockin'] = $data['sysno'];
                    $params['inno_stockin'] = $data['stockinno'];
                    //-- type 11 所用参数END
                    #var_dump($value['beqty']);exit();
                    //------------更新罐容表
                    $tank = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                    $tank_info = $tank->getStoragetankById($value['storagetank_sysno']);
                    //罐容存量 = 原有存量 + 订单量
                    $update_storagetank['tank_stockqty'] = $tank_info['tank_stockqty'] + $value['beqty'];
                    $update_storagetank['goods_quality_sysno'] = $value['goods_quality_sysno'];
                    $update_storagetank['qualityname'] = $value['qualityname'];

                    $sql_edit_storagetank = $this->dbh->update(DB_PREFIX.'base_storagetank', $update_storagetank, 'sysno=' . intval($value['storagetank_sysno']));
                    //-----------更新罐容表 End
                    if (!$sql_edit_storagetank) {
                        $this->dbh->rollback();
                        return ['code'=>300,'message'=>'更新储罐表失败!'];
                    }
                    //------------End

                    #--------------公共方法 库存表,中转量，中转量仓储费，首期管道输送费，更新库存记录的超溢罐量
                    $params['type'] = 11;
                    $params['data']['instockqty'] = $value['beqty'];
                    $params['beqty'] = $value['beqty'];
                    $params['stockindate'] = $data['stockindate'];
                    $params['data']['firstfrom_sysno'] = $id;
                    $S = new StockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

                    $stockid = $S->pubstockoperation($params);
                    if (!$stockid) {
                        $this->dbh->rollback();
                        return ['code'=>300,'message'=>'添加库存信息失败!'];
                    }

                    //更新库存明细
                    //
                    $sotckinput = array(
                        'stock_sysno' => $stockid,
                    );
                    $ret = $this->dbh->update(DB_PREFIX.'doc_stock_in_detail', $sotckinput, 'sysno=' . intval($detailid));
                    if (!$ret) {
                        $this->dbh->rollback();
                        return ['code'=>300,'message'=>'更新入库单明细失败!'];
                    }


                    //更新货物进出日志
                    $res = $this->dbh->update(DB_PREFIX.'doc_goods_record_log',$sotckinput,'doc_type=11 AND doc_sysno='.$id);

                    if (!$ret) {
                        $this->dbh->rollback();
                        return ['code'=>300,'message'=>'更新货物进出记录失败!'];
                    }

                    #============入库扣减损耗==================
                    if($key==0) {
                        $sql_contract_goods = "SELECT isminstockin,minnumber,isminstockinullage,IF(contractrate,contractrate,firstlossrate) AS contractrate FROM `" . DB_PREFIX . "doc_contract_goods` WHERE contract_sysno={$data['contract_sysno']} AND goods_sysno = {$stockshipindetaildata[0]['goods_sysno']} AND isdel<>1";

                        $contract_goods_data = $this->dbh->select_row($sql_contract_goods);

                        //开启了最小入库量的进行库存扣减
                        $instock_ullage_params = array(
                            'beqty'        => $beqty,  //实际入库量
                            'redusqty'     =>$value['beqty'],
                            'stock_sysno'    => $stockid,
                            'contractrate' => $contract_goods_data['contractrate'], //合约损耗率
                            'minnumber' => $contract_goods_data['minnumber'],  //最小入库量
                            'isminstockin' => $contract_goods_data['isminstockin'],
                            'isminstockinullage'   => $contract_goods_data['isminstockinullage'],//是否启用最小入库量损耗:0否1是
                        );
                        $res_ullage = $this->instock_ullage($instock_ullage_params);
                        if (!$res_ullage['res']) {
                            $this->dbh->rollback();
                            return ['code' => 300, 'message' => '扣减损耗失败'];
                        }

                        //入库扣减储罐损耗
                        $storagetank_ullage['tank_stockqty'] = $update_storagetank['tank_stockqty'] - $res_ullage['ullage'];
                        $sql_ullage_storagetank = $this->dbh->update(DB_PREFIX.'base_storagetank', $storagetank_ullage, 'sysno=' . intval($value['storagetank_sysno']));
                        //-----------更新罐容表 End
                        if (!$sql_ullage_storagetank) {
                            $this->dbh->rollback();
                            return ['code'=>300,'message'=>'更新储罐损耗失败!'];
                        }
                    }

                    # 罐容记录表
                    $storage = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                    $arr = [
                        'doc_sysno' => $id,
                        'docno' => $data['stockinno'],
                        'doctype' => 5,   #1车入库2车入库作废3车出库4车出库作废5船入库6船入库作废7船出库8船出库作废9盘点10倒罐
                        'storagetank_sysno' => $value['storagetank_sysno'],
                        'beqty' => $key==0?$value['beqty']- $res_ullage['ullage']:$value['beqty'],
                        'status' => 1,
                        'isdel' => 0
                    ];
                    if ($data['stockinstatus'] == 4) {
                        $arr['doctype'] = 5;
                    } elseif ($data['stockinstatus'] == 7) {
                        $arr['doctype'] = 6;
                    }
                    $res = $storage->addStoragetankLog($arr);
                    if ($res['code'] != 200) {
                        $this->dbh->rollback();
                        return ['code'=>300,'message'=>'添加罐容记录失败!'];
                    }
                    #end

                    # 进出货物记录begin
                    $goodsinoutlog = array(
                        'doc_time'          => '=NOW()',
                        'shipname'          => '管输',
                        'goods_sysno'       => $value['goods_sysno'],
                        'goodsname'         => $value['goodsname'],
                        'storagetank_sysno' => $value['storagetank_sysno'],
                        'storagetankname'   => $value['storagetankname'],
                        'customer_sysno'    => $data['customer_sysno'],
                        'customername'      => $data['customername'],
                        'beqty'             => $value['beqty'],
                        'tobeqty'           => $value['tobeqty'],
                        'stockin_sysno'     => $id,
                        'stockinno'         => $data['stockinno'],
                        'doc_sysno'         => $id,
                        'docno'             => $data['stockinno'],
                        'accountstoragetank_sysno' => $value['storagetank_sysno'],
                        'accountstoragetankname'   => $value['storagetankname'],
                        'stock_sysno'       => $stockid,
                        'doc_type'          => 11,
                        'ullage'            =>$key==0?$res_ullage['ullage']:0,
                        'takegoodscompany'  =>'',
                        'goodsnature'       =>$value['goodsnature'],
                        'takegoodsno'       =>$data['takegoodsno'],
                        'status'            => 1,
                        'isdel'             => 0,
                        'created_at'        =>'=NOW()',
                        'updated_at'        =>'=NOW()',
                    );
                    # 进出货物记录end
                    $logInstance = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                    $res = $logInstance->addGoodsRecordLog($goodsinoutlog);
                    if ($res['code']!=200) {
                        $this->dbh->rollback();
                        return ['code'=>300,'message'=>'添加进出货物记录失败!'.$res['message']];
                    }

                }

                //判断是否是入库起始日计费合同，调用生成包罐费用action 一定要放在加库存方法之前!!!!
                $ContractInstance = new ContractModel(Yaf_Registry::get('db'), Yaf_Registry::get('mc'));
                $contractdata = $ContractInstance->getContractInfoById($data['contract_sysno']);
                $ifcontract = $this->ifcontract_instock($data['contract_sysno']);


                foreach ($contractdata as $key => $value) {
                    $value['customer_sysno'] = $value['customer_id'];
                    $value['customer_name'] = $value['customername'];
                    $value['contract_sysno'] = $value['sysno'];
                    $value['contract_no'] = $value['contractnodisplay'];
                    $value['totalprice']  = $value['yearamount'];
                    if($value['costtype']==2 && $ifcontract==false)
                    {
                        $FinancecostInstance = new FinancecostModel(Yaf_Registry::get('db'), Yaf_Registry::get('mc'));
                        $res = $FinancecostInstance->addFinancecostByTank($value, $value['instockdate'], $value['instockdate']);
                        if(!$res){
                            $this->dbh->rollback();
                            return ['code'=>300, 'message'=>'新增包罐费用失败！'];
                        }
                    }
                }


                #--------------------------释放罐容新方法
                $tank = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                #待入量 - 预约入库量
                $booking = new BookingModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $result = $booking->getBookingStockById($data['booking_in_sysno']);
                foreach ($result as $key => $item) {
                    $bookingqty[$key] = $item['bookinginqty'];#预约入库量
                    $booktanksysno[$key] = $item['storagetank_sysno'];#预约罐号
                    $tanklist = $tank->getStoragetankById($booktanksysno[$key]);
                    $orderinqty = $tanklist['orderinqty'] - $bookingqty[$key];
                    $params['type'] = 10;
                    $params['data'] = [
                        'goods_sysno' => $item['goods_sysno'],
                        'storagetank_sysno' => $booktanksysno[$key],
                        'orderinqty' => $orderinqty
                    ];

                    // error_log(date("Y-m-d H:i:s") . "\t" . json_encode($params['data']) . "\n", 3, './logs/stockshipin.log');

                    #var_dump($params['data']);exit();
                    /*if($key = 1){
                        var_dump($params['data']);exit();
                    }*/
                    $S = new StockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                    $res = $S->pubstockoperation($params);
                    if (!$res) {
                        $this->dbh->rollback();
                        return ['code'=>300,'message'=>'释放待入量失败!'];
                    }
                }
                #--------------------------END释放罐容

                //审核驳回 状态=>‘退回’
            } elseif ($auditstep == 6) {
                $data['stockinstatus'] = $auditstep;
                $data['auditreason'] = $stockmarks;
                $res = $this->dbh->update(DB_PREFIX.'doc_stock_in', $data, 'sysno=' . intval($id));
                if (!$res) {
                    $this->dbh->rollback();
                    return ['code'=>300,'message'=>'更新入库单失败!'];
                }

            } elseif ($auditstep == 2 || $auditstep == 3 || $auditstep == 8) {
                $res = $this->dbh->delete(DB_PREFIX.'doc_stock_in_detail', 'stockin_sysno=' . intval($id));

                if (!$res) {
                    $this->dbh->rollback();
                    return ['code'=>300,'message'=>'更新入库单明细失败!'];
                }

                foreach ($stockshipindetaildata as $value) {
                    $input = array(
                        'stockin_sysno' => $id,
                        'goods_sysno' => $value['goods_sysno'],
                        'goods_quality_sysno' => $value['goods_quality_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'goodsreceiptdate' => $value['goodsreceiptdate'],
                        'tobeqty' => $value['tobeqty'],
                        'shipcheckqty' => $value['shipcheckqty'],
                        'bussinesscheckqty' => $value['bussinesscheckqty'],
                        'beqty' => $value['beqty'],
                        'storagetank_sysno' => $value['storagetank_sysno'],
                        'expresscompanyname' => $value['expresscompanyname'],
                        'memo' => $value['memo'],
                        'shipname' => $value['shipname'],
                        'carid' => $value['carid'],
                        'status' => 1,
                        'isdel' => 0,
                        'version' => 1,
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                        'goodsname' => $value['goodsname'],
                        'qualityname' => $value['qualityname'],
                        'unitname' => $value['unitname'],
                        'takegoodsnum' => $value['takegoodsnum'],
                        'release_no' => $value['release_no'],
                        'shipbookingdate'=>$value['shipbookingdate'],
                        'shipactualdate'=>$value['shipactualdate'],
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_stock_in_detail', $input);
                    if (!$res) {
                        $this->dbh->rollback();
                        return ['code'=>300,'message'=>'添加入库单明细失败!'];
                    }
                }

                if($auditstep == 3)
                {
                    #添加提示信息
                    $stock_data = $this->getStockshipinById($id);
                    $booking = new BookingModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                    $res = $booking->shipinsertmes($stock_data);
                    if (!$res) {
                        $this->dbh->rollback();
                        return ['code'=>300,'message'=>'添加消息提醒失败!'];
                    }
                    #添加提示信息end
                }
            }
            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 25,
                'opertype' => $data['stockinstatus'] - 1,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $stockmarks,
            );
            if ($auditstep == 1) {
                $input['opertype'] = 4;
            } else if ($auditstep == 6) {
                $input['opertype'] = 7;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'message'=>'添加操作日志失败!'];
            }

            $this->dbh->commit();
            return ['code'=>200,'message'=>'更新成功!'];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['code'=>300,'message'=>'更新失败!'];
        }
    }

    public function delStockshipin($id, $data)
    {
        $this->dbh->begin();
        try {
            //查询相关的入库预约单
            $stock_ship = $this->getStockshipinById($id);
            $booking_in_sysno = $stock_ship['booking_in_sysno'];
            //更新预约单 状态为 已审核
            $array['bookinginstatus'] = 5;
            $array['issaveorder'] = 0;
            $array['updated_at'] = '=NOW()';
            $ret = $this->dbh->update(DB_PREFIX.'doc_booking_in', $array, 'sysno=' . intval($booking_in_sysno));

            if (!$ret) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'更新预约单状态失败'];
            }
            //更新入库单
            $ret = $this->dbh->update(DB_PREFIX.'doc_stock_in', $data, 'sysno=' . intval($id));

            if (!$ret) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'更新订单状态失败'];
            }
            if($stock_ship['ispipelineorder']==1){
                //更新管线分配单
                $input = array(
                    'stock_sysno'=>'',
                    'stockno'=>'',
                    'businesstype'=>5,

                );
                $ret = $this->dbh->update(DB_PREFIX.'doc_pipelineorder', $input, 'businesstype = 6 and  booking_sysno=' . intval($stock_ship['booking_in_sysno']));

                if (!$ret) {
                    $this->dbh->rollback();
                    return ['code'=>300,'msg'=>'更新管线分配单失败'];
                }
            }
            if($stock_ship['isberthorder']==1){
                //更新泊位分配单
                $input = array(
                    'stock_sysno'=>'',
                    'stockno'=>'',
                    'businesstype'=>5,

                );
                $ret = $this->dbh->update(DB_PREFIX.'doc_berthorder', $input, 'businesstype = 6 and  booking_sysno=' . intval($stock_ship['booking_in_sysno']));

                if (!$ret) {
                    $this->dbh->rollback();
                    if (!$ret) {
                        $this->dbh->rollback();
                        return ['code'=>300,'msg'=>'更新泊位分配单失败'];
                    }
                }
            }
            if($stock_ship['isqualitycheck']==1){
                //更新品质分配单
                $input = array(
                    'stock_sysno'=>'',
                    'stockno'=>'',
                    'businesstype'=>5,

                );
                $ret = $this->dbh->update(DB_PREFIX.'doc_qualitycheck', $input, 'businesstype = 6 and  booking_sysno=' . intval($stock_ship['booking_in_sysno']));

                if (!$ret) {
                    $this->dbh->rollback();
                    return ['code'=>300,'msg'=>'更新品质分配单失败'];
                }
            }
            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 25,
                'opertype' => 5,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $data['abandonreason'],
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'更新日志失败'];
            }

            $this->dbh->commit();
            return ['code'=>200,'msg'=>$ret];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['code'=>300,'msg'=>'失败'];
        }
    }

    public function getStockpipeinDetailData($id)
    {
        $sql = "SELECT si.customername,si.stockinstatus,group_concat(shipname) as shipname ,goodsname ,sum(tobeqty) as tobeqty,group_concat(bs.storagetankname) as storagetankname FROM `".DB_PREFIX."doc_stock_in` si 
			LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid ON si.sysno = sid.stockin_sysno 
			LEFT JOIN ".DB_PREFIX."base_storagetank bs ON bs.sysno = sid.storagetank_sysno
                    WHERE si.sysno = ".intval($id)." group by sid.stockin_sysno";
        return $this->dbh->select_row($sql);
    }

    public function getStockpipeindetailById($id)
    {
        $sql = "SELECT s.*,bg.`goodsname`,st.`storagetankname`,s.shipname
                FROM `".DB_PREFIX."doc_stock_in_detail` s
                left join `".DB_PREFIX."base_goods` bg on (s.`goods_sysno`=bg.`sysno`)
                left join `".DB_PREFIX."base_storagetank` st on (s.`storagetank_sysno`=st.`sysno`)
                where s.`isdel`=0 and s.`stockin_sysno`=" . intval($id);
        return $this->dbh->select($sql);
    }

    public function getStockinByCustomerId($id)
    {
        $sql = "SELECT s.*,sd.`shipname`,sd.`goods_sysno`,sd.`goods_quality_sysno`,sd.`goodsnature`,sd.`storagetank_sysno`,bs.`storagetankname`,(select goodsname from ".DB_PREFIX."base_goods bg
                where bg.`sysno` = sd.`goods_sysno`) as goodsname,gq.`qualityname`,(select contracttype
                from ".DB_PREFIX."doc_contract dc where dc.`sysno` = s.`contract_sysno`) as isstoragetank
                FROM `".DB_PREFIX."doc_stock_in` s
                 left join `".DB_PREFIX."customer` c on (s.`customer_sysno`=c.`sysno`)
                 left join `".DB_PREFIX."doc_stock_in_detail` sd on (s.`sysno`=sd.`stockin_sysno`)
                 left join `".DB_PREFIX."base_storagetank` bs on (bs.`sysno`=sd.`storagetank_sysno`)
                 left join `".DB_PREFIX."base_goods_quality` gq on (gq.`sysno`=sd.`goods_quality_sysno`)
                 where s.`isdel`=0 and s.`stockinstatus`=4 and s.`customer_sysno`=" . intval($id) . " group by s.sysno ";
        return $this->dbh->select($sql);
    }

    public function getStockinByCustomerAndContractId($id, $contract_sysno)
    {
        $sql = "SELECT s.*,sd.`shipname`,sd.`goods_sysno`,sd.`goods_quality_sysno`,sd.`goodsnature`,sd.`storagetank_sysno`,bs.`storagetankname`,(select goodsname from ".DB_PREFIX."base_goods bg where bg.`sysno` = sd.`goods_sysno`) as goodsname,gq.`qualityname`,(select contracttype from ".DB_PREFIX."doc_contract dc where dc.`sysno` = s.`contract_sysno`) as isstoragetank FROM `".DB_PREFIX."doc_stock_in` s left join `".DB_PREFIX."customer` c on (s.`customer_sysno`=c.`sysno`) left join `".DB_PREFIX."doc_stock_in_detail` sd on (s.`sysno`=sd.`stockin_sysno`) left join `".DB_PREFIX."base_storagetank` bs on (bs.`sysno`=sd.`storagetank_sysno`) left join `".DB_PREFIX."base_goods_quality` gq on (gq.`sysno`=sd.`goods_quality_sysno`) where s.`isdel`=0 and s.`stockinstatus`=4 and s.`customer_sysno`=" . intval($id) . " and s.`contract_sysno`=" . intval($contract_sysno) . " group by s.sysno ";
        return $this->dbh->select($sql);
    }

    public function getStockpipeinDetailList($params)
    {
        $filter = array();
        if (isset($params['stockin_sysno']) && $params['stockin_sysno'] != '') {
            $filter[] = " s.`stockin_sysno` = '" . $params['stockin_sysno'] . "' ";
        }
        $where = 's.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_stock_in_detail` s  where {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT s.*,si.stockinno stockinno2,ss.stockno,si.stockindate,bg.goodsname,gq.qualityname,bu.unitname,st.storagetankname
                                FROM `".DB_PREFIX."doc_stock_in_detail` s
                                LEFT JOIN ".DB_PREFIX."doc_stock_in si ON si.sysno=s.stockin_sysno
                                LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno=s.stock_sysno
                                LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno=s.goods_sysno
                                LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON ga.goods_sysno=bg.sysno
                                LEFT JOIN ".DB_PREFIX."base_goods_quality gq ON gq.sysno=s.goods_quality_sysno
                                LEFT JOIN ".DB_PREFIX."base_unit bu ON bu.sysno=ga.unit_sysno
                                LEFT join ".DB_PREFIX."base_storagetank st ON st.sysno=s.storagetank_sysno
                                where {$where} group by s.sysno";
                if ($params['orders'] != '')
                    $sql .= " order by " . $params['orders'];
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT s.*,si.stockinno stockinno2,ss.stockno,si.stockindate,bg.goodsname,gq.qualityname,bu.unitname,st.storagetankname
                                FROM `".DB_PREFIX."doc_stock_in_detail` s
                                LEFT JOIN ".DB_PREFIX."doc_stock_in si ON si.sysno=s.stockin_sysno
                                LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno=s.stock_sysno
                                LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno=s.goods_sysno
                                LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON ga.goods_sysno=bg.sysno
                                LEFT JOIN ".DB_PREFIX."base_goods_quality gq ON gq.sysno=s.goods_quality_sysno
                                LEFT JOIN ".DB_PREFIX."base_unit bu ON bu.sysno=ga.unit_sysno
                                LEFT join ".DB_PREFIX."base_storagetank st ON st.sysno=s.storagetank_sysno
                                where {$where} group by s.sysno";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    /**
     * 获取报关列表数据
     * @return [type] [description]
     */
    public function getdeclareList($params)
    {
        $filter = array();
        if (isset($params['start_time']) && $params['start_time'] != '') {
            $filter[] = " si.`updated_at` >= '" . $params['start_time'] . "' ";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " si.`updated_at` <= '" . $params['end_time'] . "' ";
        }
        if (isset($params['customername']) && $params['customername'] != '') {
            $filter[] = " si.`customername` LIKE  '%" . $params['customername']."%'";
        }
        if (isset($params['stockinno']) && $params['stockinno'] != '') {
            $filter[] = " si.`stockinno` =   '{$params['stockinno']}' ";
        }

        $where = 'si.isdel=0 AND si.stockinstatus=4 AND si.stockintype=1 AND sid.goodsnature in(1,2,3)';
        if (count($filter)>0) {
            $where .= ' AND '.implode(' AND ', $filter);
        }

        $sql = "SELECT count(*) FROM ( SELECT COUNT(si.sysno) FROM `".DB_PREFIX."doc_stock_in` si
                LEFT JOIN `".DB_PREFIX."doc_stock_in_declare` side ON si.sysno=side.stockin_sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid ON si.sysno=sid.stockin_sysno
                WHERE {$where}  GROUP BY si.sysno ORDER BY si.updated_at DESC) as a";

        $result['totalRow'] = $this->dbh->select_one($sql);


        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT si.*,sid.goodsname,sid.shipname,SUM(sid.bussinesscheckqty) beqty FROM `".DB_PREFIX."doc_stock_in` si
                    LEFT JOIN `".DB_PREFIX."doc_stock_in_declare` side ON si.sysno=side.stockin_sysno
                    LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid ON si.sysno=sid.stockin_sysno
                    WHERE {$where}  GROUP BY si.sysno ORDER BY si.updated_at DESC";

                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT si.*,sid.goodsname,sid.shipname,SUM(sid.bussinesscheckqty) beqty  FROM `".DB_PREFIX."doc_stock_in` si
                        LEFT JOIN `".DB_PREFIX."doc_stock_in_declare` side ON si.sysno=side.stockin_sysno
                        LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid ON si.sysno=sid.stockin_sysno
                        WHERE {$where}  GROUP BY si.sysno ORDER BY si.updated_at DESC";

                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;

    }

    public function getdeclareDetail($stockin_sysno)
    {
        $sql = "SELECT si.*,sid.goodsname,sid.shipname,sid.beqty FROM `".DB_PREFIX."doc_stock_in` si
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid ON si.sysno=sid.stockin_sysno
                WHERE si.sysno = {$stockin_sysno}
                ";
        $data=$this->dbh->select_row($sql);
        // $sql = "SELECT si.sysno stockin_sysno,sid.goodsname,sid.shipname,sid.beqty,side.release_num,if(side.unrelease_num is null,0,side.unrelease_num) unrelease_num,sid.goods_sysno,sid.storagetank_sysno,side.declaration,side.storagetankname FROM `".DB_PREFIX."doc_stock_in` si
        //         LEFT JOIN `".DB_PREFIX."doc_stock_in_declare` side ON si.sysno=side.stockin_sysno
        //         LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid ON si.sysno=sid.stockin_sysno
        //         WHERE si.sysno = {$stockin_sysno}
        //         ";
        $sql = "SELECT si.sysno stockin_sysno,side.release_num,if(side.unrelease_num is null,0,side.unrelease_num) unrelease_num
                ,side.declaration,side.bussinesscheckqty beqty,side.storagetankname,side.storagetank_sysno,side.goodsname,side.goods_sysno,side.customername,side.customer_sysno FROM `".DB_PREFIX."doc_stock_in` si
                LEFT JOIN `".DB_PREFIX."doc_stock_in_declare` side ON si.sysno=side.stockin_sysno
                WHERE si.sysno = {$stockin_sysno} ";
        $data['detaildata'] = $this->dbh->select($sql);

        if(empty($data['detaildata'][0]['release_num']))
        {
            $sql = "SELECT si.sysno stockin_sysno,sid.goodsname,sid.shipname,SUM(sid.beqty) beqty,sid.goods_sysno,sid.storagetank_sysno,0 as unrelease_num
                    FROM `".DB_PREFIX."doc_stock_in` si
                    LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid ON si.sysno=sid.stockin_sysno
                    WHERE si.sysno = {$stockin_sysno}
                    ";
            $data['detaildata'] = $this->dbh->select_row($sql);
        }

        return $data;
    }


    //添加报关明细
    public function adddeclare($params,$attachment)
    {

        $this->dbh->begin();

        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if(count($attachment)>0){
            $res = $A->addAttachModelSysno($params[0]['stockin_sysno'],$attachment);
        }

        // if(!$res){
        //     return ['code'=>300,'message'=>'添加附件失败!'];
        // }
        $res = $this->dbh->delete(DB_PREFIX.'doc_stock_in_declare','stockin_sysno='.$params[0]['stockin_sysno']);

        if(!$res)
        {
            $this->dbh->rollback();
            return ['code'=>300,'message'=>'删除报关明细失败!'];
        }

        $release_num = 0;
        foreach ($params as $key => $value) {
            $res = $this->dbh->insert(DB_PREFIX.'doc_stock_in_declare',$value);
            if(!$res)
            {
                $this->dbh->rollback();
                return ['code'=>300,'message'=>'添加报关明细失败!'];
            }

            $release_num += $value['release_num'];
        }

        $count = count($params);
        $stockin_data = array(
            'release_num'  => $release_num,
            'unrelease_num'  => $params[$count-1]['unrelease_num'],
        );
        $res = $this->dbh->update(DB_PREFIX.'doc_stock_in',$stockin_data,'sysno='.$params[0]['stockin_sysno']);

        if(!$res)
        {
            $this->dbh->rollback();
            return ['code'=>300,'message'=>'回写入库单失败!'];
        }

        $this->dbh->commit();
        return ['code'=>200,'message'=>'添加成功!'];
    }


    public function getStoragetankBYstockinId($stockin_sysno)
    {
        $sql = "SELECT sid.storagetank_sysno sysno,bs.storagetankname FROM ".DB_PREFIX."doc_stock_in_detail sid
                LEFT JOIN ".DB_PREFIX."base_storagetank bs ON sid.storagetank_sysno=bs.sysno
                WHERE sid.stockin_sysno={$stockin_sysno} group by storagetank_sysno";

        return $this->dbh->select($sql);
    }


    public function isCAstock($booking_in_sysno)
    {
        $sql = "SELECT ca_address FROM ".DB_PREFIX."doc_booking_in WHERE sysno={$booking_in_sysno}";

        return $this->dbh->select_one($sql);
    }

    public function getoutqty($stockin_sysno)
    {
        $sql = "SELECT SUM(dsod.beqty) FROM ".DB_PREFIX."doc_stock_out dso
                LEFT JOIN ".DB_PREFIX."doc_stock_out_detail dsod ON dso.sysno=dsod.stockout_sysno
                WHERE dso.stockoutstatus=4 AND dso.isdel=0 AND dsod.stockin_sysno={$stockin_sysno}";
        return $this->dbh->select_one($sql);
    }

    /**
     * 判断该合同是否存在于入库单中
     * @param  [type] $contract_sysno [合同主键]
     * @return [type]                 [description]
     */
    public function ifcontract_instock($contract_sysno)
    {
        $sql = "SELECT sysno FROM ".DB_PREFIX."storage_stock WHERE contract_sysno={$contract_sysno} AND isdel<>1 AND iscurrent=1";
        $result = $this->dbh->select_one($sql);
        $result = empty($result) ? false : true;

        return $result;
    }

    /**
     * 合同启用最小入库量时
     * 入库即扣库存损耗
     * @param  [array] $[params]  array('stock_sysno'=>'库存主键', 'beqty'=>'实际入库数量', 'minnumber'=>'最小入库量', 'contractrate'=>'合约损耗率' , 'isminstockin' => '是否开启最小入库');
     * @return [bool] [description]
     * @author HR
     */
    public function instock_ullage($params)
    {
        if($params['stock_sysno']=='' && !isset($params['stock_sysno']))
        {
            return ['code'=>300, 'message'=> '库存ID不能为空!'];
        }
        if ($params['isminstockin']==1 && $params['isminstockinullage']==1){

            $ullage = ( $params['beqty']>$params['minnumber'] ? $params['beqty'] : $params['minnumber'] ) * ($params['contractrate']/1000);
        } else {
            $ullage = $params['beqty'] * ($params['contractrate']/1000);
        }

        $stockqty = $params['redusqty'] - $ullage; //可用库存量


        $stock_params = array(
            // 'instockqty' => $stockqty,//不需要更新入库时候的数据
            'stockqty'   => $stockqty,
            'ullage'     => $ullage
        );
//print_r($stock_params);
        $res = $this->dbh->update(DB_PREFIX.'storage_stock', $stock_params, 'sysno='.intval($params['stock_sysno']));
//print_r($params['stock_sysno']);die;

        ##更新首期仓储费费用单ullage字段
        $update_cost = array(
            'ullage'=>$ullage,
        );
        $this->dbh->update(DB_PREFIX.'doc_finance_cost_detail', $update_cost, 'sysno='.intval($params['stock_sysno']));
        
        $result = array(
            'res'=>$res,
            'ullage'=>$ullage
        );
        return $result;

    }

}
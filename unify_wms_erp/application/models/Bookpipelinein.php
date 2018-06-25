<?php

/**
 * 管入库预约model
 * User: HR
 * Date: 2017/7/12
 */
class BookpipelineinModel
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
     * 查询管入库预约
     */
    public function searchBookshipin($params)
    {
        $filter = array();
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " b.`bookinginno` LIKE '%{$params['bar_no']}%' ";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " c.`sysno` = '{$params['customer_sysno']}' ";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
            $filter[] = " b.`status`='{$params['bar_status']}'";
        }
        if (isset($params['bar_isdel']) && $params['bar_isdel'] != '-100') {
            $filter[] = " b.`isdel`='{$params['bar_isdel']}'";
        }
        if (isset($params['bar_stockintype']) && $params['bar_stockintype'] != '') {
            $filter[] = " b.`stockintype`='{$params['bar_stockintype']}'";
        }
        if (isset($params['bar_bookinginstatus']) && $params['bar_bookinginstatus'] != '-100') {
            $filter[] = " b.`bookinginstatus`='{$params['bar_bookinginstatus']}'";
        }
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " b.`bookingindate`>='{$params['begin_time']}'";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " b.`bookingindate`<='{$params['end_time']}'";
        }
        if (isset($params['docsource']) && $params['docsource'] != '') {
            $filter[] = "b.`docsource`='{$params['docsource']}' ";
        }

        $where = 'WHERE b.isdel=0 and b.stockintype = 3';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = "ORDER BY b.created_at desc";

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_booking_in` b left join `".DB_PREFIX."customer` c on (b.`customer_sysno`=c.`sysno`) $where ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT b.*, bd.`goodsnature`,bd.`shipname`,sum(bd.`bookinginqty`) as bookinginqty,bd.`storagetank_sysno`,(select goodsname from ".DB_PREFIX."base_goods bg where bg.`sysno` = bd.`goods_sysno`) as goodsname,(select qualityname from ".DB_PREFIX."base_goods_quality gq where gq.`sysno`=bd.`goods_quality_sysno` ) as goods_quality_name,group_concat(bs.storagetankname) as storagetankname FROM `".DB_PREFIX."doc_booking_in` b left join `".DB_PREFIX."customer` c on (b.`customer_sysno`=c.`sysno`)  
                left join `".DB_PREFIX."doc_booking_in_detail` bd on (b.`sysno`=bd.`bookingin_sysno`) 
                LEFT JOIN `".DB_PREFIX."base_storagetank` bs ON bs.`sysno` = bd.`storagetank_sysno`
                $where group by b.`sysno` $order";
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT b.*, bd.`goodsnature`,bd.`shipname`,sum(bd.`bookinginqty`) as bookinginqty,bd.`storagetank_sysno`,(select goodsname from ".DB_PREFIX."base_goods bg where bg.`sysno` = bd.`goods_sysno`) as goodsname,(select qualityname from ".DB_PREFIX."base_goods_quality gq where gq.`sysno`=bd.`goods_quality_sysno` ) as goods_quality_name,group_concat(bs.storagetankname) as storagetankname FROM `".DB_PREFIX."doc_booking_in` b left join `".DB_PREFIX."customer` c on (b.`customer_sysno`=c.`sysno`)  
                left join `".DB_PREFIX."doc_booking_in_detail` bd on (b.`sysno`=bd.`bookingin_sysno`) 
                LEFT JOIN `".DB_PREFIX."base_storagetank` bs ON bs.`sysno` = bd.`storagetank_sysno`
                $where group by b.`sysno` $order";
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    /*
     * 新增管入库预约
     */
    public function addBookshipin($data, $bookshipindetaildata, $bookinginstatus)
    {

        $this->dbh->begin();
        try {
            $res = $this->dbh->insert(DB_PREFIX.'doc_booking_in', $data);

            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300, 'message'=>'添加入库预约失败!'];
            }

            $id = $res;

            $res = $this->dbh->delete(DB_PREFIX.'doc_booking_in_detail', 'bookingin_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300, 'message'=>'删除入库预约明细失败!'];
            }

            if (!empty($bookshipindetaildata)) {
                foreach ($bookshipindetaildata as $value) {
                    $input = array(
                        'bookingin_sysno' => $id,
                        'goods_sysno' => $value['goods_sysno'],
                        'goods_quality_sysno' => $value['goods_quality_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'bookinginqty' => $value['bookinginqty'],
                        'bookingindate' => $value['bookingindate'],
                        'storagetank_sysno' => $value['storagetank_sysno'],
                        'shipname' => $value['shipname'],
                        'memo' => $value['memo'],
                        'status' => $value['status'],
                        'isdel' => $value['isdel'],
                        'version' => $value['version'],
                        'created_at' => '=NOW()',
                        'updated_at' => '=NOW()',
                        'release_no' => $value['release_no'],
                        'declaration' => $value['declaration'],
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_booking_in_detail', $input);

                    if (!$res) {
                        $this->dbh->rollback();
                        return ['code'=>300, 'message'=>'添加入库预约明细失败!'];
                    }
                }
            }

            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 23,
                'opertype' => 0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => '新建制单',
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300, 'message'=>'添加业务操作日志失败!'];
            }
            #库存管理业务操作日志
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 23,
                'opertype' => '',
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => '',
            );
            if ($bookinginstatus == 2) {
                $input['opertype'] = 1;
                $input['operdesc'] = "暂存数据";
            } elseif ($bookinginstatus == 4) {
                #添加提示信息
                $booking_data = $this->getBookshipinById($id);
                $booking = new BookingModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $res = $booking->shipinsertmes($booking_data);
                if (!$res) {
                    $this->dbh->rollback();
                    return ['code'=>300, 'message'=>'添加消息提醒失败!']; 
                }
                #添加提示信息end    
                $input['opertype'] = 2;
                $input['operdesc'] = "已提交";
            }
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300, 'message'=>'添加业务操作日志失败!'];            
            }


            $this->dbh->commit();
            return ['code'=>200, 'message'=>$id];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['code'=>300, 'message'=>'添加失败!'];
        }
    }

    /*
     * 通过id获取管入库预约单
     */
    public function getBookshipinById($id)
    {
        $sql = "SELECT b.*,bd.`goodsnature`,bd.`shipname`,bd.`memo`,
                (select goodsname from ".DB_PREFIX."base_goods bg where bg.`sysno` = bd.`goods_sysno`) as goodsname,
                 (select sysno from ".DB_PREFIX."base_goods bg where bg.`sysno` = bd.`goods_sysno`) as goods_sysno,
                sum(bd.`bookinginqty`) as bookinginqty,bd.`storagetank_sysno`,
                (select qualityname from ".DB_PREFIX."base_goods_quality gq where gq.`sysno`=bd.`goods_quality_sysno` ) as goods_quality_name,
                (select storagetankname from ".DB_PREFIX."base_storagetank bs where bs.`sysno`=bd.`storagetank_sysno` ) as storagetankname 
                FROM `".DB_PREFIX."doc_booking_in` b 
                left join `".DB_PREFIX."customer` c on (b.`customer_sysno`=c.`sysno`) 
                left join `".DB_PREFIX."doc_booking_in_detail` bd on (b.`sysno`=bd.`bookingin_sysno`) 
                where b.`stockintype`=3 and b.`sysno`=" . intval($id) . " group by bd.`bookingin_sysno` ";
        return $this->dbh->select_row($sql);
    }

    /*
     * 删除管入库预约单
     */
    public function delbookpinin($id, $data)
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
                'doctype' => 23,
                'opertype' => 5,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $data['flowmemo'],
            );

            if ($data['bookinginstatus'] == 5) {
                $input['opertype'] = 4;
                $input['operdesc'] = "审核通过";
            } elseif ($data['bookinginstatus'] == 7) {
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
     * 管入库预约更新
     */
    public function updateBookpipelinein($id, $data, $bookshipindetaildata, $stockmarks = '', $auditstep = 0)
    {
        $this->dbh->begin();
        try {
            if ($auditstep == 1) {
                $data['bookinginstatus'] = 2;
            }elseif ($auditstep == 3) {

                $data['bookinginstatus'] = 4;
            } elseif ($auditstep == 8) {
                //登及状态
                $data['bookinginstatus'] = 5;
            }elseif ($auditstep == 10){
                $data['bookinginstatus'] = 8;
            }
            $res = $this->dbh->update(DB_PREFIX.'doc_booking_in', $data, 'sysno=' . intval($id));
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            //预约单已审核
            // @author zhaoshiyu
            if ($auditstep == 4) {
                //从待确认状态变成待审核状态
                //更改预约单状态
                $res = $this->dbh->delete(DB_PREFIX.'doc_booking_in_detail', 'bookingin_sysno=' . intval($id));

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }

                foreach ($bookshipindetaildata as $value) {
                    $input = array(
                        'bookingin_sysno' => $id,
                        'goods_sysno' => $value['goods_sysno'],
                        'goods_quality_sysno' => $value['goods_quality_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'bookinginqty' => $value['bookinginqty'],
                        'bookingindate' => $value['bookingindate'],
                        'storagetank_sysno' => $value['storagetank_sysno'],
                        'shipname' => $value['shipname'],
                        'memo' => $value['memo'],
                        'status' => $value['status'],
                        'isdel' => $value['isdel'],
                        'version' => $value['version'],
                        'created_at' => 'NOW()',
                        'updated_at' => '=NOW()',
                        'release_no' => $value['release_no'],
                        'declaration' => $value['declaration'],
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_booking_in_detail', $input);

                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }

                #审核时候锁定罐容量
                //查询审核通过后预约入库量
                $booking = new BookingModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $stock = new StockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $params['type'] = 10;
                $result = $booking->getBookingStockById($id);
                foreach ($result as $key => $item) {
                    $bookingqty[$key] = $item['bookinginqty'];
                    $bookingtanksysno[$key] = $item['storagetank_sysno'];
                    $goods_sysno[$key] = $item['goods_sysno'];
                    //查询所预约储罐的可用容量
                    $stockqtyinfo = $stock->getStockInfoByStoragetankno($bookingtanksysno[$key], $goods_sysno[$key]);
                    if ($stockqtyinfo['code'] == 202) {
                        $this->dbh->rollback();
                        return false;
                    }
                    $params['data'] = [
                        'goods_sysno' => $goods_sysno[$key],
                        'orderinqty' => $stockqtyinfo['orderinqty'] + $bookingqty[$key],
                        'storagetank_sysno' => $bookingtanksysno[$key]
                    ];

                    // error_log(date("Y-m-d H:i:s") . "\t" . json_encode($params['data']) . "\n", 3, './logs/bookshipin.log');

                    $res = $stock->pubstockoperation($params);
                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
                #锁罐 END

                #添加提示信息
                $booking_data = $this->getBookshipinById($id);
                $res = $booking->shipinsertmes($booking_data);
                if (!$res) {
                    $this->dbh->rollback();
                    return false; 
                }
                #添加提示信息end  

            } elseif ($auditstep == 2 || $auditstep == 3 || $auditstep == 7 || $auditstep == 8) {
                $res = $this->dbh->delete(DB_PREFIX.'doc_booking_in_detail', 'bookingin_sysno=' . intval($id));

                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
                foreach ($bookshipindetaildata as $value) {
                    $input = array(
                        'bookingin_sysno' => $id,
                        'goods_sysno' => $value['goods_sysno'],
                        'goods_quality_sysno' => $value['goods_quality_sysno'],
                        'goodsnature' => $value['goodsnature'],
                        'bookinginqty' => $value['bookinginqty'],
                        'bookingindate' => $value['bookingindate'],
                        'storagetank_sysno' => $value['storagetank_sysno'],
                        'shipname' => $value['shipname'],
                        'memo' => $value['memo'],
                        'status' => $value['status'],
                        'isdel' => $value['isdel'],
                        'version' => $value['version'],
                        'created_at' => 'NOW()',
                        'updated_at' => '=NOW()',
                        'release_no' => $value['release_no'],
                        'declaration' => $value['declaration'],
                    );
                    $res = $this->dbh->insert(DB_PREFIX.'doc_booking_in_detail', $input);

                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }

            } elseif ($auditstep == 10) {
                $bookshipinfo = $this->getBookshipinById($id);
                if ($bookshipinfo['bookinginno']) {
                    $res = COMMON::editStockInReject($bookshipinfo['bookinginno'], $stockmarks);
                    if ($res['code'] != '200') {
                        $this->dbh->rollback();
                        return false;
                    }
                } else {
                    return false;
                }
            }

            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 23,
                'opertype' => $data['bookinginstatus'] - 1,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $stockmarks,
            );

            switch ($auditstep) {
                case 1:
                    $input['opertype'] = 0;
                    $input['operdesc'] = "??？";
                    break;
                case 2:
                    $input['opertype'] = 1;
                    $input['operdesc'] = "暂存数据";
                    break;
                case 4:
                    $input['opertype'] = 2;
                    $input['operdesc'] = "提交待审核";
                    break;
                case 5:
                    $input['opertype'] = 4;
                    $input['operdesc'] = "审核通过";
                    break;
                case 6:
                    $input['opertype'] = 6;
                    #$input['operdesc'] = "作废";
                    break;
                case 7:
                    $input['opertype'] = 6;
                    $input['operdesc'] = "退回";
                    break;
                case 8:
                    $input['opertype'] = 8;
                    $input['operdesc'] = '登记完成';
                    break;
                case 10:
                    $input['opertype'] = 9; //驳回订单
                    break;
                default:
                    $input['opertype'] = 0;
            }
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }

            $this->dbh->commit();
            return true;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }


    /**
     * 审核管入库预约
     */
    public function AuditBookpipin($id, $data)
    {
        $this->dbh->begin();
        try {
            if ($data['bookinginstatus'] == 5) {
                #审核时候锁定罐容量
                //查询审核通过后预约入库量
                $booking = new BookingModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $stock = new StockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $params['type'] = 10;
                $result = $booking->getBookingStockById($id);
                foreach ($result as $key => $item) {
                    $bookingqty[$key] = $item['bookinginqty'];
                    $bookingtanksysno[$key] = $item['storagetank_sysno'];
                    $goods_sysno[$key] = $item['goods_sysno'];
                    //查询所预约储罐的可用容量
                    $stockqtyinfo = $stock->getStockInfoByStoragetankno($bookingtanksysno[$key], $goods_sysno[$key]);
                    if ($stockqtyinfo['code'] == 202) {
                        $this->dbh->rollback();
                        return false;
                    }
                    $params['data'] = [
                        'goods_sysno' => $goods_sysno[$key],
                        'orderinqty' => $stockqtyinfo['orderinqty'] + $bookingqty[$key],
                        'storagetank_sysno' => $bookingtanksysno[$key]
                    ];

                    // error_log(date("Y-m-d H:i:s") . "\t" . json_encode($params['data']) . "\n", 3, './logs/bookshipin.log');

                    $res = $stock->pubstockoperation($params);
                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
                #锁罐 END

                #添加提示信息
                $booking_data = $this->getBookshipinById($id);
                $res = $booking->shipinsertmes($booking_data);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
                #添加提示信息end
            }

            $ret = $this->dbh->update(DB_PREFIX.'doc_booking_in', $data, 'sysno=' . intval($id));
            if (!$ret) {
                $this->dbh->rollback();
                return ['code'=>300, 'message'=>'更新预约单失败!'];
            }

            //获取当前船入库预约单的信息，判断是否要生成品质检查单据、管线分配单、泊位分配单，退回状态不再生成
            if($data['bookinginstatus']!=7){
                $booking_data = $this->getBookshipinById($id);
                $bookshipinInstance = new BookshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $res = $bookshipinInstance->createThreeBill($booking_data,4);

                if($res['code']!=200){
                    return ['code'=>300, 'message'=>$res];
                }

            }

            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 23,
                'opertype' => 5,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $data['flowmemo'],
            );

            if ($data['bookinginstatus'] == 5) {
                //审核通过
                $input['opertype'] = 4;
                $input['operdesc'] = "审核通过";
            } elseif ($data['bookinginstatus'] == 7) {
                //审核驳回
                $input['opertype'] = 6;
            }

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300, 'message'=>'添加业务操作日志失败!'];
            }

            $this->dbh->commit();
            return ['code'=>200, 'message'=>$ret];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return ['code'=>300, 'message'=>'更新失败!'];
        }
    }

    /*
     * 退回管入库预约
     */
    public function freedBookshipin($id, $array, $bookshipindetaildata)
    {
        $this->dbh->begin();
        try {
            #1更新预约表的状态
            $ret = $this->dbh->update(DB_PREFIX.'doc_booking_in', $array, 'sysno=' . intval($id));
            if (!$ret) {
                $this->dbh->rollback();
                return false;
            }
            #释放罐容新方法
            $tank = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $S = new StockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            #待入量 - 预约入库量
            foreach ($bookshipindetaildata as $item) {
                $bookingqty = $item['bookinginqty'];#预约入库量
                $booktanksysno = $item['storagetank_sysno'];#预约罐号
                $tanklist = $tank->getStoragetankById($booktanksysno);
                $orderinqty = $tanklist['orderinqty'] - $bookingqty;

                $params['type'] = 10;
                $params['data'] = [
                    'goods_sysno' => $item['goods_sysno'],
                    'storagetank_sysno' => $booktanksysno,
                    'orderinqty' => $orderinqty,
                    'goods_quality_sysno' => $item['goods_quality_sysno'],
                    'qualityname' => $item['goods_quality_name'],
                ];

                $res = $S->pubstockoperation($params);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            //获取主表信息删除三单
            $sql = "select * from ".DB_PREFIX."doc_booking_in where isdel = 0 and status < 2 and sysno = {$id}";
            $info = $this->dbh->select_row($sql);
            $params = array(
                'isdel' => 1,
                'updated_at'=>'=NOW()',
            );
            if($info['ispipelineorder']==1){
                $res = $this->dbh->update(DB_PREFIX.'doc_pipelineorder',$params,'businesstype = 5 and booking_sysno = '.$id);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }
            if($info['isberthorder']==1){
                $res = $this->dbh->update(DB_PREFIX.'doc_berthorder',$params,'businesstype = 5 and booking_sysno = '.$id);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }
            if($info['isqualitycheck']==1){
                $res = $this->dbh->update(DB_PREFIX.'doc_qualitycheck',$params,'businesstype = 5 and booking_sysno = '.$id);
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
                'doctype' => 23,
                'opertype' => 6,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $array['stockmarks'],
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
     * 获取预约明细
     * */
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
                $sql = "SELECT  s.*,s.bookingindate as shipbookingdate,s.bookinginqty as tobeqty,bg.goodsname,gq.qualityname as goods_quality_name,bu.unitname,st.storagetankname,s.bookingindate as goodsreceiptdate,gq.qualityname
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
    public function getDetailforPrint($id)
    {
        $sql = "SELECT si.customer_name,si.bookinginstatus,si.bookingindate,si.takegoodsno,group_concat(distinct shipname) as shipname,goodsname,sum(bookinginqty) as beqty,group_concat(bs.storagetankname) as storagetankname
                FROM `".DB_PREFIX."doc_booking_in` si
                LEFT JOIN `".DB_PREFIX."doc_booking_in_detail` sid ON si.sysno = sid.bookingin_sysno
                LEFT JOIN `".DB_PREFIX."base_goods` bg ON bg.sysno = sid.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_storagetank bs ON bs.sysno = sid.storagetank_sysno
                WHERE si.sysno = ".intval($id)." group by si.sysno";

        return $this->dbh->select_row($sql);
    }
}
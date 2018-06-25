<?php

/**
 * Bookshipin Model
 *
 */
class BookshipinModel
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

        $where = 'b.isdel=0 and b.stockintype = 1';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_booking_in` b left join `".DB_PREFIX."customer` c on (b.`customer_sysno`=c.`sysno`) where {$where} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT b.*, bd.`goodsnature`,bd.`shipname`,sum(bd.`bookinginqty`) as bookinginqty,bd.`storagetank_sysno`,(select goodsname from ".DB_PREFIX."base_goods bg where bg.`sysno` = bd.`goods_sysno`) as goodsname,(select qualityname from ".DB_PREFIX."base_goods_quality gq where gq.`sysno`=bd.`goods_quality_sysno` ) as goods_quality_name,group_concat(bs.storagetankname) as storagetankname FROM `".DB_PREFIX."doc_booking_in` b left join `".DB_PREFIX."customer` c on (b.`customer_sysno`=c.`sysno`)  
                left join `".DB_PREFIX."doc_booking_in_detail` bd on (b.`sysno`=bd.`bookingin_sysno`) 
                LEFT JOIN `".DB_PREFIX."base_storagetank` bs ON bs.`sysno` = bd.`storagetank_sysno`
                where {$where} group by b.`sysno` 
                ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by created_at desc";
                }
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT b.*, bd.`goodsnature`,bd.`shipname`,sum(bd.`bookinginqty`) as bookinginqty,bd.`storagetank_sysno`,(select goodsname from ".DB_PREFIX."base_goods bg where bg.`sysno` = bd.`goods_sysno`) as goodsname,(select qualityname from ".DB_PREFIX."base_goods_quality gq where gq.`sysno`=bd.`goods_quality_sysno` ) as goods_quality_name,group_concat(bs.storagetankname) as storagetankname FROM `".DB_PREFIX."doc_booking_in` b left join `".DB_PREFIX."customer` c on (b.`customer_sysno`=c.`sysno`)  
                left join `".DB_PREFIX."doc_booking_in_detail` bd on (b.`sysno`=bd.`bookingin_sysno`) 
                LEFT JOIN `".DB_PREFIX."base_storagetank` bs ON bs.`sysno` = bd.`storagetank_sysno`
                where {$where} group by b.`sysno` 
                ";
                if ($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                } else {
                    $sql .= " order by created_at desc";
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

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
                where b.`stockintype`=1 and b.`sysno`=" . intval($id) . " group by bd.`bookingin_sysno` ";
        return $this->dbh->select_row($sql);
    }

    public function getchange($data)
    {
        $storagetank_name = $params['storagetankname'];
        $goods_sysno = $params['goods_sysno'];
        $result = array();
        if (!$storagetank_name || !$goods_sysno) {
            // return false;
            return $result['result'] = 0;
        }
        // $sql = "SELECT ss.goods_sysno FROM `".DB_PREFIX."storage_stock` ss
        //         left join  ".DB_PREFIX."base_storagetank bs on bs.sysno = ss.storagetank_sysno
        //         where iscurrent = 1 and isclearstock=0 and stockqty>0 and bs.storagetankname ='" . trim($storagetank_name) . "'
        //         group by goods_sysno";
        $sql = "select goods_sysno from `".DB_PREFIX."base_storagetank` where storagetankname='" . $storagetank_name . "'";
        $res = $this->dbh->select_row($sql);
        if ($res['goods_sysno']) {
            if ($res['goods_sysno'] != $goods_sysno) {
                $result['result'] = 0;
                return $result;
            } else {
                $result['result'] = 1;
                return $result;
            }
        }
    }

    /*
     * 查询客户船入库预约
     * @author wu xianneng
     */
    public function searchCustormerBookinShip($params)
    {
        $filter = array();
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " ss.`customer_sysno` = {$params['customer_sysno']} ";
        }
        if (isset($params['iscurrent']) && $params['iscurrent'] != '') {
            $filter[] = " ss.`iscurrent` = {$params['iscurrent']} ";
        }

        if (1 <= count($filter)) {
            $where = ' WHERE ' . implode(' AND ', $filter);
        }

        $sql = "SELECT bid.*,bi.bookinginno,bg.goodsname,gq.qualityname,un.unitname,st.storagetankname
                FROM ".DB_PREFIX."doc_booking_in_detail bid
                LEFT JOIN ".DB_PREFIX."doc_booking_in bi ON bi.sysno = bid.bookingin_sysno
                LEFT JOIN ".DB_PREFIX."customer cu ON cu.sysno = bi.sysno
                LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = bid.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_goods_quality gq ON gq.sysno = bid.goods_quality_sysno 
                LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON ga.goods_sysno = bg.sysno 
                LEFT JOIN ".DB_PREFIX."base_unit un ON un.sysno = ga.unit_sysno 
                LEFT JOIN ".DB_PREFIX."base_storagetank st ON st.sysno = bid.storagetank_sysno 
                LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.storagetank_sysno = bid.storagetank_sysno ";

        $sql = "SELECT ss.*,si.stockinno,un.unitname,st.storagetankname
                FROM ".DB_PREFIX."storage_stock ss
                LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sd ON ss.sysno = sd.stock_sysno
                LEFT JOIN ".DB_PREFIX."doc_stock_in si on sd.stockin_sysno = si.sysno
                LEFT JOIN ".DB_PREFIX."base_storagetank st ON st.sysno = ss.storagetank_sysno
                LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON ga.goods_sysno = ss.goods_sysno
                LEFT JOIN ".DB_PREFIX."base_unit un ON un.sysno = ga.unit_sysno " . $where;

        return $this->dbh->select($sql);
    }

    public function addBookshipin($data, $bookshipindetaildata, $bookinginstatus)
    {
        $this->dbh->begin();
        try {
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
                'opertype' => 0,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => '新建制单',
            );

            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            #库存管理业务操作日志
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 1,
                'opertype' => $bookinginstatus,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => '',
            );
            if ($bookinginstatus == 2) {
                $input['opertype'] = 1;
                $input['operdesc'] = "暂存数据";
            } elseif ($bookinginstatus == 3) {
                $input['opertype'] = 2;
                $input['operdesc'] = "已提交";
            }
            $res = $S->addDocLog($input);
            if (!$res) {
                $this->dbh->rollback();
                return false;            }

            $this->dbh->commit();
            return $id;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

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
            //删除管线单
            $booking_data = $this->getBookshipinById($id);

            if($booking_data['ispipelineorder']==1)
            {
                $res = $this->dbh->delete(DB_PREFIX.'doc_pipelineorder','businesstype=1 AND booking_sysno='.intval($id));
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }                
            }

            //删除泊位单
            if($booking_data['isberthorder']==1)
            {
                $res = $this->dbh->delete(DB_PREFIX.'doc_berthorder','businesstype=1 AND booking_sysno='.intval($id));
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }                
            }

            //删除品质检查单
            if($booking_data['isqualitycheck']==1)
            {
                $res = $this->dbh->delete(DB_PREFIX.'doc_qualitycheck','businesstype=1 AND booking_sysno='.intval($id));
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

    public function updateBookshipin($id, $data, $bookshipindetaildata, $stockmarks = '', $auditstep = 0)
    {
        $this->dbh->begin();
        try {
            if ($auditstep == 1) {
                $data['bookinginstatus'] = 2;
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
            $stockqtyinfo = array();
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
                'doctype' => 1,
                'opertype' => $data['bookinginstatus'] - 1,
                'operemployee_sysno' => $user['employee_sysno'],
                'operemployeename' => $user['employeename'],
                'opertime' => '=NOW()',
                'operdesc' => $stockmarks,
            );
            if ($input['opertype'] == 3) {
                $input['operdesc'] = "确认通过";
            }
            switch ($auditstep) {
                case 1:
                    $input['opertype'] = 0;
                    $input['operdesc'] = "??？";
                    break;
                case 2:
                    $input['opertype'] = 1;
                    $input['operdesc'] = "暂存数据";
                    break;
                case 3:
                    $input['opertype'] = 2;
                    $input['operdesc'] = "提交待确认";
                    break;
                case 4:
                    $input['opertype'] = 3;
                    $input['operdesc'] = "确认通过";
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
     * @param $id
     * @param $data
     * @title 船入库预约审核方法
     */
    public function AuditBookshipin($id, $data)
    {
        $this->dbh->begin();
        try {
            if ($data['bookinginstatus'] == 7) {
                // 审核驳回 释放罐容
                #--------------------------释放罐容新方法
                $tank = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $booking = new BookingModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                //查询预约单明细
                $result = $booking->getBookingStockById($id);
                foreach ($result as $key => $item) {
                    //预约单 待入库数量
                    $bookingqty = $item['bookinginqty'];
                    // 预约罐号
                    $booktanksysno = $item['storagetank_sysno'];
                    // 根据罐号 查询罐子相关信息
                    $tanklist = $tank->getStoragetankById($booktanksysno);
                    // 罐容的待入库量 = 原有数量 - 预约入库量
                    $orderinqty = $tanklist['orderinqty'] - $bookingqty;

                    $params['type'] = 10;
                    $params['data'] = [
                        'goods_sysno' => $item['goods_sysno'],
                        'storagetank_sysno' => $booktanksysno,
                        'orderinqty' => $orderinqty
                    ];

                    // error_log(date("Y-m-d H:i:s") . "\t" . json_encode($params['data']) . "\n", 3, './logs/stockshipin.log');

                    $S = new StockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                    $res = $S->pubstockoperation($params);
                    if (!$res) {
                        $this->dbh->rollback();
                        return false;
                    }
                }
                #--------------------------END释放罐容
            }
            $ret = $this->dbh->update(DB_PREFIX.'doc_booking_in', $data, 'sysno=' . intval($id));
            if (!$ret) {
                $this->dbh->rollback();
                return false;
            }

            //获取当前船入库预约单的信息，判断是否要生成品质检查单据、管线分配单、泊位分配单
            $booking_data = $this->getBookshipinById($id);
            $res = $this->createThreeBill($booking_data,1);
            if($res['code']!=200){
                return false;
            }


            #库存管理业务操作日志
            $user = Yaf_Registry::get(SSN_VAR);
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 1,
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
                return false;
            }

            $this->dbh->commit();
            return $ret;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    public function delBookshipin($id, $data)
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

    public function getBookshipindetailById($id)
    {
        $sql = "SELECT b.*,bg.`goodsname`,st.`storagetankname`,(select qualityname from ".DB_PREFIX."base_goods_quality gq where gq.`sysno`=b.`goods_quality_sysno` ) as goods_quality_name
                  FROM `".DB_PREFIX."doc_booking_in_detail` b left join `".DB_PREFIX."base_goods` bg on (b.`goods_sysno`=bg.`sysno`) 
                  left join `".DB_PREFIX."base_storagetank` st on (b.`storagetank_sysno`=st.`sysno`) 
                  where b.`isdel`=0 and b.`bookingin_sysno`=" . intval($id);
        return $this->dbh->select($sql);
    }

    /*
     * 查询储罐是否被入库预约单引用
     * @author wu xianneng
     */
    public function searchBookinStorage($params)
    {
        $filter = array();
        if (isset($params['storagetank_sysno']) && $params['storagetank_sysno'] != '0') {
            $filter[] = "`storagetank_sysno` = {$params['storagetank_sysno']} ";
        }

        if (1 <= count($filter)) {
            $where = ' WHERE ' . implode(' AND ', $filter);
        }
        $sql = "SELECT * FROM ".DB_PREFIX."doc_booking_in_detail " . $where;

        return $this->dbh->select($sql);
    }


    public function addBookshipinForApi($data, $bookshipindetaildata)
    {
        $this->dbh->begin();
        try {
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
            if (!empty($bookshipindetaildata)) {
                $input = array(
                    'bookingin_sysno' => $id,
                    'goods_sysno' => $bookshipindetaildata['goods_sysno'],
                    'goods_quality_sysno' => $bookshipindetaildata['goods_quality_sysno'],
                    'goodsnature' => $bookshipindetaildata['goodsnature'],
                    'bookinginqty' => $bookshipindetaildata['bookinginqty'],
                    'bookingindate' => $bookshipindetaildata['bookingindate'],
                    'storagetank_sysno' => $bookshipindetaildata['storagetank_sysno'],
                    'shipname' => $bookshipindetaildata['shipname'],
                    'memo' => $bookshipindetaildata['memo'],
                    'status' => 1,
                    'isdel' => 0,
                    'version' => 1,
                    'created_at' => '=NOW()',
                    'updated_at' => '=NOW()',
                    'release_no' => $bookshipindetaildata['release_no'],
                    'declaration' => $bookshipindetaildata['declaration'],
                );
                $res = $this->dbh->insert(DB_PREFIX.'doc_booking_in_detail', $input);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
            }
            #库存管理业务操作日志
            $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $input = array(
                'doc_sysno' => $id,
                'doctype' => 1,
                'opertype' => 0,
                'operemployee_sysno' => '0',
                'operemployeename' => '云仓',
                'opertime' => '=NOW()',
                'operdesc' => '',
            );
            switch ($data['bookinginstatus']) {
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
                    'doctype' => 1,
                    'opertype' => $data['bookinginstatus'] - 1,
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
     * 
     * @param  [type] $info [description]
     * @param  [type] $id   [description]
     * @return [type]       [description]
     */
    public function createThreeBill($info,$businesstype)
    {
        $where_sysno = empty($info['stock_sysno']) ? 'booking_sysno='.$info['sysno'] : 'stock_sysno='.$info['stock_sysno'];
        $qualitycheck_sql  = "SELECT sysno FROM ".DB_PREFIX."doc_qualitycheck WHERE  $where_sysno AND businesstype=$businesstype ";
        $pipelineorder_sql = "SELECT sysno FROM ".DB_PREFIX."doc_qualitycheck WHERE  $where_sysno AND businesstype=$businesstype ";
        $berthorder_sql    = "SELECT sysno FROM ".DB_PREFIX."doc_qualitycheck WHERE  $where_sysno AND businesstype=$businesstype ";
        $exist_qualitycheck  = $this->dbh->select_one($qualitycheck_sql); //判断品质单是否生成过了
        $exist_pipelineorder = $this->dbh->select_one($pipelineorder_sql); //判断管线单是否生成过了
        $exist_berthorder    = $this->dbh->select_one($berthorder_sql); //判断泊位单是否生成过了
        if($info['isqualitycheck']==1 && !$exist_qualitycheck)
        {
            //添加品质检查单据
            $qualitycheckdata = array(
                    'businesstype'  => $businesstype,
                    'booking_sysno' => $info['sysno'],
                    'bookingno'     => $info['bookinginno'],
                    'stock_sysno'   => $info['stock_sysno'],
                    'stockno'       => $info['stockno'],
                    'bookingdate'   => $info['bookingindate'],
                    'shipname'   => $info['shipname'],
                    'customer_sysno'=>$info['customer_sysno'],
                    'customername'=>$info['customer_name'],
                    'goodsname'=>$info['goodsname'],
                    'goods_sysno'=>$info['goods_sysno'],
                    'carshipname'=>$info['shipname'],
                );
            $Qualitycheck = new QualitycheckModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $res = $Qualitycheck->newQualitycheck($qualitycheckdata);
            
            if($res['code']!=200){
                return ['code'=>300, 'message'=> $res['message']];
            }
        }


        if($info['ispipelineorder'] == 1 && !$exist_pipelineorder){
                //管线分配单
                $pipelineorderdata = array(
                        'businesstype'  => $businesstype,
                        'booking_sysno' => $info['sysno'],
                        'bookingno'     => $info['bookinginno'],
                        'stock_sysno'   => $info['stock_sysno'],
                        'stockno'       => $info['stockno'],
                        'bookingdate'   => $info['bookingindate'],
                        'applydate'     => '=NOW()',
                        'status'        => 1,
                        'isdel'         => 0,
                        'version'       => 1,
                        'created_at'    => '=NOW()',
                        'updated_at'    => '=NOW()',
                    );
                $Pipelineorder = new PipelineorderModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $res = $Pipelineorder->insertPipelineMain($pipelineorderdata);
                
                if($res['code']!=200){
                    return ['code'=>300, 'message'=> $res['message']];
                }
            
        }
        //泊位分配单
        if($info['isberthorder'] == 1 && !$exist_berthorder){
            $pdata = array(
                'berthorderno'  => COMMON::getCodeId('P'),
                'businesstype'  => $businesstype,
                'booking_sysno' => $info['sysno'],
                'bookingno'     => $info['bookinginno'],
                'stock_sysno'   => $info['stock_sysno'],
                'stockno'       => $info['stockno'],
                'bookingdate'   => $info['bookingindate'],
                'applydate'     => '=NOW()',
                'orderstatus'   => 2,
                'status'        => 1,
                'isdel'         => 0,
                'version'       => 1,
                'created_at'    => '=NOW()',
                'updated_at'    => '=NOW()',
                );
            $res = $this->dbh->insert(DB_PREFIX.'doc_berthorder',$pdata);
            if(!$res){
                return ['code' => 300,'message' => '创建泊位分配单失败'];
            }
        }

        return ['code'=> 200, 'message'=> '操作成功'];

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
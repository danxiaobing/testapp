<?php
class BookcarbackModel
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
     * 获取正在出库的订单
     */
    public function getstockcaringdetail($params)
    {
        $filter = array();

        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " so.`customer_sysno` = '{$params['customer_sysno']}' ";
        }

        if (isset($params['contract_sysno']) && $params['contract_sysno'] != '') {
            $filter[] = " so.`contract_sysno` = {$params['contract_sysno']} ";
        }

        if (isset($params['bar_stockintype']) && $params['bar_stockintype'] != '') {
            $filter[] = " so.`stockintype`='{$params['bar_stockintype']}'";
        }

        $where = 'so.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $order = " order by so.created_at desc";

        $sql = "SELECT COUNT(ss.sysno)FROM ".DB_PREFIX."doc_stock_out so
                LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sod on (so.sysno = sod.stockout_sysno)
                LEFT JOIN ".DB_PREFIX."storage_stock ss on (sod.stock_sysno = ss.sysno) and ss.iscurrent =1 and ss.isdel =0 and ss.status<2
                LEFT JOIN ".DB_PREFIX."doc_stock_in sin on (sin.sysno = ss.firstfrom_sysno)  and sin.isdel =0 and sin.status < 2
                where stockouttype = 2 and so.stockoutstatus = 3 and {$where} {$order}";

        $result = $params;
        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            $sql = "SELECT so.sysno as stockout_sysno,so.stockouttype,so.stockoutno,ss.customer_sysno,so.customername,so.booking_out_sysno,so.bookingoutno,so.takegoodsqty,so.stockoutstatus,so.contract_sysno,so.contractno,sod.tobeqty,sod.takeqty,sod.beqty,sod.stock_sysno,sod.storagetank_sysno,ss.*,sin.sysno as stockin_sysno FROM ".DB_PREFIX."doc_stock_out so
                        LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sod on (so.sysno = sod.stockout_sysno)
                        LEFT JOIN ".DB_PREFIX."storage_stock ss on (sod.stock_sysno = ss.sysno)  and ss.isdel =0 and ss.status<2
                        LEFT JOIN ".DB_PREFIX."doc_stock_in sin on (sin.sysno = ss.firstfrom_sysno) and sin.isdel =0 and sin.status < 2
                        WHERE stockouttype = 2 and so.stockoutstatus = 3 and {$where} {$order}";
            if (isset($params['page']) && $params['page'] == false) {
                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    /*
        * 添加车入库预约单
        */
    public function addBookcarback($data, $bookcarindetaildata, $bookcarincarsdata,$bookinginstatus)
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
                return ['code'=>300,'msg'=>'新增主表信息失败'];
            }

            $id = $res;

            $res = $this->dbh->delete(DB_PREFIX.'doc_booking_in_detail', 'bookingin_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'删除明细表信息失败'];
            }

            $res = $this->dbh->delete(DB_PREFIX.'doc_booking_in_cars', 'bookingin_sysno=' . intval($id));

            if (!$res) {
                $this->dbh->rollback();
                return ['code'=>300,'msg'=>'删除车辆信息失败'];
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
                        return ['code'=>300,'msg'=>'新增明细表信息失败'];
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
                        return ['code'=>300,'msg'=>'新增车辆表信息失败'];
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
                return ['code'=>300,'msg'=>'新增日志失败1'];
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
                return ['code'=>300,'msg'=>'新增日志失败2'];
            }

            $this->dbh->commit();
            return ['code'=>200,'msg'=>$id];

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

















}
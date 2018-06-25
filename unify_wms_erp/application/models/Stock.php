<?php

/**
 * 库存查询
 * User: Aty
 * Date: 2016/11/22 0022
 * Time: 9:14
 */
class StockModel
{
    /**
     * 数据库类实例
     *
     * @var object
     */
    public $dbh = null;

    public $mch = null;

    /**
     * Constructor
     *
     * @param   object $dbh
     * @return  void
     */
    public function __construct($dbh, $mch = null)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    /**
     * 库存基准列表
     * @author josy
     */

    public function getStockList($search)
    {
        $filter = array();
        if (isset($search['bar_enddate']) && $search['bar_enddate'] != '') {
            $filter[] = " unix_timestamp(IF(ss.doctype =  3, st.stocktransdate  ,si.stockindate))  <= unix_timestamp('" . $search['bar_enddate'] . " 00:00:00')  ";
        }
        if (isset($search['bar_startdate']) && $search['bar_startdate'] != '') {
            $filter[] = " unix_timestamp(IF(ss.doctype =  3, st.stocktransdate  ,si.stockindate))  >= unix_timestamp('" . $search['bar_startdate'] . " 00:00:00')  ";
        }
        if (isset($search['customer_sysno']) && $search['customer_sysno'] != '') {
            $filter[] = " ss.`customer_sysno` = '" . $search['customer_sysno'] . "' ";
        }
        if (isset($search['goodsnature']) && $search['goodsnature'] != '') {
            $filter[] = " ss.`goodsnature` = '" . $search['goodsnature'] . "' ";
        }

        $where = '  ss.iscurrent = 1 and ss.isdel = 0 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $orderby = " ORDER BY ss.`updated_at` DESC ";

        $result = array('total' => 0, 'list' => array());
        $sql = "SELECT count(ss.sysno)  from ".DB_PREFIX."storage_stock ss
            left join ".DB_PREFIX."doc_stock_trans_detail std on std.in_stock_sysno = ss.sysno
            left join ".DB_PREFIX."doc_stock_trans st on st.sysno = std.stocktrans_sysno
            left join ".DB_PREFIX."doc_stock_in_detail sid on sid.stock_sysno = ss.sysno
            left join ".DB_PREFIX."doc_stock_in si on si.sysno = sid.stockin_sysno
            left join  ".DB_PREFIX."base_storagetank bs on bs.sysno = ss.storagetank_sysno WHERE {$where} ";
        $result['totalRow'] = $this->dbh->select_one($sql);

        $sql = "select DISTINCT(ss.sysno),ss.stockno,ss.customer_sysno,ss.customername,ss.customername as custname,ss.doctype,
			IF(ss.doctype =  3, st.sysno  ,si.sysno) as stockin_sysno ,
            IF(ss.doctype =  3, st.stocktransno  ,si.stockinno) as stockin_no ,
            IF(ss.doctype =  3, st.stocktransdate  ,si.stockindate) as stockin_date ,
            IF(ss.doctype =  3, st.stocktransdate  ,si.stockindate) as stockindate ,
            IF(ss.doctype =  3, st.stocktransdate  ,si.stockindate) as instockdate ,
            ss.storagetank_sysno,bs.storagetankname,ss.goods_sysno,ss.goodsname,bga.unit_sysno,un.unitname,ss.goods_quality_sysno,
			ss.goodsqualityname,ss.goodsnature,ss.instockqty,ss.outstockqty,ss.clockqty, (ss.stockqty  -ss.clockqty) as ableqty,(ss.stockqty  -ss.clockqty) as stockqty,ss.firstfrom_sysno as firstfrom_sysno,ss.contract_sysno as contract_sysno
            from ".DB_PREFIX."storage_stock ss
            left join ".DB_PREFIX."doc_stock_trans_detail std on std.in_stock_sysno = ss.sysno
            left join ".DB_PREFIX."doc_stock_trans st on st.sysno = std.stocktrans_sysno
            left join ".DB_PREFIX."doc_stock_in_detail sid on sid.stock_sysno = ss.sysno
            left join ".DB_PREFIX."doc_stock_in si on si.sysno = sid.stockin_sysno
            left join  ".DB_PREFIX."base_storagetank bs on bs.sysno = ss.storagetank_sysno 
			LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = ss.goods_sysno 
			LEFT JOIN ".DB_PREFIX."base_unit un ON un.sysno = bga.unit_sysno WHERE {$where} {$orderby}";
        if (isset($search['page']) && $search['page'] == false) {         //不带分页查询

            $result['list'] = $this->dbh->select($sql);
            return $result;
        } else {      //带分页查询
            $result['totalPage'] = ceil($result['totalRow'] / $search['pageSize']);
            $this->dbh->set_page_num($search['pageCurrent']);
            $this->dbh->set_page_rows($search['pageSize']);
            //
            $result['list'] = $this->dbh->select_page($sql);
            return $result;
        }
    }

    /**
     * 库存数据列表
     * @author HR
     *
     */
    public function getList($search)
    {
        $filter = array();

        if (isset($search['begin_time']) && $search['begin_time'] != '') {
            $filter[] = "ss.created_at >= '{$search['begin_time']}'";
        }

        if (isset($search['end_time']) && $search['end_time'] != '') {
            $filter[] = "ss.created_at <= '{$search['end_time']}'";
        }

        if (isset($search['customer_sysno']) && $search['customer_sysno'] != '') {
            $filter[] = " ss.`customer_sysno` = '" . $search['customer_sysno'] . "' ";
        }
        if (isset($search['goodsnature']) && $search['goodsnature'] != '') {
            $filter[] = " ss.`goodsnature` = '" . $search['goodsnature'] . "' ";
        }

        if (isset($search['iscurrent']) && $search['iscurrent'] != '') {
            $filter[] = " ss.`iscurrent` = '" . $search['iscurrent'] . "' ";
        }
        if (isset($search['goodsname']) && $search['goodsname'] != '') {
            $filter[] = " ss.`goods_sysno` = '" . $search['goodsname'] . "' ";
        }
        if (isset($search['contractno']) && $search['contractno'] != '') {
            $filter[] = " dc.`sysno` = '" . $search['contractno'] . "' ";
        }
        if (isset($search['isclearstock']) && $search['isclearstock'] != '') {
            $filter[] = " ss.`isclearstock` = '" . $search['isclearstock'] . "' ";
        }
        if (isset($search['stockqty']) && $search['stockqty'] != '-100') {
            $filter[] = " ss.`stockqty` = '" . $search['stockqty'] . "' ";
        }
        if (isset($search['stock_sysno']) && $search['stock_sysno'] != '') {
            $filter[] = " ss.`sysno` = '" . $search['stock_sysno'] . "' ";
        }
        $where = ' ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        if (!$search['orders']) {
            $search['orders'] = "updated_at DESC";
        }

        $des = explode(' ', $search['orders']);

        $arr = [
            'updated_at' => 'ss',
            'sysno' => 'ss',
            'firstfrom_no' => 'ss',
            'stockindate' => 'si',
            'stocktransdate' => 'st',
            'firstdate' => 'ss',
            'financedate' => 'ss',
            'doctype' => 'ss',
            'isclearstock' => 'ss',
            'shipname' => 'ss',
            'customer_sysno' => 'ss',
            'customername' => 'ss',
            'goods_sysno' => 'ss',
            'goodsname' => 'ss',
            'goodsqualityname' => 'ss',
            'unitname' => 'unit',
            'storagetankname' => 'bsk',
            'instockqty' => 'ss',
            'stockqty' => 'ss',
            'goodsnature' => 'ss',
            'clockqty' => 'ss',
            'transferflag' => 'ss',
            'transferqty' => 'ss',
            'stockno' => 'ss',
            'created_at' => 'si',
        ];

        $a = $arr[$des[0]];
        $orderby = "  ORDER BY {$a}.{$search['orders']} ";

        // $result = array('total' => 0, 'list' => array());


        $sql = "SELECT COUNT( DISTINCT ss.sysno)
                FROM `".DB_PREFIX."storage_stock` ss 
                LEFT JOIN `".DB_PREFIX."doc_stock_in` si ON ss.firstfrom_sysno = si.sysno 
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid ON ss.sysno=sid.stock_sysno AND si.sysno=sid.stockin_sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_trans_detail` std ON ss.sysno = std.in_stock_sysno 
                LEFT JOIN `".DB_PREFIX."doc_stock_trans` st ON ss.sysno = std.stocktrans_sysno  AND st.stocktransstatus!=5
                LEFT JOIN `".DB_PREFIX."base_goods_attribute` bga ON bga.`goods_sysno`=ss.goods_sysno 
                LEFT JOIN `".DB_PREFIX."base_unit` unit ON unit.`sysno`=bga.`unit_sysno` 
                LEFT JOIN `".DB_PREFIX."doc_contract` dc ON ss.contract_sysno = dc.sysno
                WHERE ss.`iscurrent`=1 AND ss.`status`=1 AND ss.`isdel`=0 AND si.stockinstatus!=5  {$where}";

        $result['totalRow'] = $this->dbh->select_one($sql);


        $sql = "SELECT ss.*,si.stockindate,st.stocktransdate,unit.unitname,dc.contractno,si.sysno in_sysno,st.sysno stocktrans_sysno,st.buy_customername,st.stocktransno,(ss.stockqty-(if(ss.clockqty>0,ss.clockqty,0))) as rankqty,dc.contractnodisplay,
        if(ss.doctype=3,0,ss.instockqty) as inqty
                FROM `".DB_PREFIX."storage_stock` ss 
                LEFT JOIN `".DB_PREFIX."doc_stock_in` si ON ss.firstfrom_sysno = si.sysno 
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` sid ON ss.sysno=sid.stock_sysno AND si.sysno=sid.stockin_sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_trans_detail` std ON ss.sysno = std.in_stock_sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_trans` st ON st.sysno = std.stocktrans_sysno  AND st.stocktransstatus!=5
                LEFT JOIN `".DB_PREFIX."base_goods_attribute` bga ON bga.`goods_sysno`=ss.goods_sysno 
                LEFT JOIN `".DB_PREFIX."base_unit` unit ON unit.`sysno`=bga.`unit_sysno` 
                LEFT JOIN `".DB_PREFIX."doc_contract` dc ON ss.contract_sysno = dc.sysno
                WHERE ss.`iscurrent`=1 AND ss.`status`=1 AND ss.`isdel`=0  AND si.stockinstatus!=5 {$where} GROUP BY ss.sysno {$orderby} ";

        // error_log($sql, 3, 'sql_print.txt');
        if (empty($search['pageSize']) && $search['page'] == false) {         //不带分页查询

            $result['list'] = $this->dbh->select($sql);
            return $result;
        } else {      //带分页查询
            $result['totalPage'] = ceil($result['totalRow'] / $search['pageSize']);
            $this->dbh->set_page_num($search['pageCurrent']);
            $this->dbh->set_page_rows($search['pageSize']);
            //
            $result['list'] = $this->dbh->select_page($sql);
            return $result;
        }
    }

    /**
     * 库存数据详情
     * @author wu xianneng
     */
    public function stockdetail($search)
    {
        $filter = array();
        if (isset($search['customer_sysno']) && $search['customer_sysno'] != '') {
            $filter[] = " si.`customer_sysno` = '" . $search['customer_sysno'] . "' ";
        }

        $where = 'where si.`isdel` = 0';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }

        $orderby = " ORDER BY si.`updated_at` DESC ";

        $result = array('total' => 0, 'list' => array());

        $sql = "SELECT sid.*,bg.goodsname,gq.qualityname,si.stockinno,si.stockindate instockdate,ss.stockno,unit.unitname 
			 FROM ".DB_PREFIX."doc_stock_in_detail sid 
			 LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno=sid.goods_sysno 
			 LEFT JOIN ".DB_PREFIX."base_goods_quality gq ON gq.sysno=sid.goods_quality_sysno 
			 LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno=bg.sysno 
			 LEFT JOIN ".DB_PREFIX."doc_stock_in si ON si.sysno=sid.stockin_sysno
			 LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno=sid.stock_sysno
			 LEFT JOIN ".DB_PREFIX."base_unit unit on bga.unit_sysno = unit.sysno {$where}";

        $result['totalRow'] = $this->dbh->select_one($sql);

        if (isset($search['page']) && $search['page'] == false) {         //不带分页查询
            $sql = "SELECT sid.*,bg.goodsname,gq.qualityname,si.stockinno,si.stockindate instockdate,ss.stockno,unit.unitname
			 FROM ".DB_PREFIX."doc_stock_in_detail sid 
			 LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno=sid.goods_sysno 
			 LEFT JOIN ".DB_PREFIX."base_goods_quality gq ON gq.sysno=sid.goods_quality_sysno 
			 LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno=bg.sysno 
			 LEFT JOIN ".DB_PREFIX."doc_stock_in si ON si.sysno=sid.stockin_sysno
			 LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno=sid.stock_sysno
			 LEFT JOIN ".DB_PREFIX."base_unit unit on bga.unit_sysno = unit.sysno {$where} {$orderby}";
            $result['list'] = $this->dbh->select($sql);
        } else {      //带分页查询
            $result['totalPage'] = ceil($result['totalRow'] / $search['pageSize']);

            $this->dbh->set_page_num($search['pageCurrent']);
            $this->dbh->set_page_rows($search['pageSize']);

            $sql = "SELECT sid.*,bg.goodsname,gq.qualityname,si.stockinno,si.stockindate instockdate,ss.stockno,unit.unitname 
			 FROM ".DB_PREFIX."doc_stock_in_detail sid 
			 LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno=sid.goods_sysno 
			 LEFT JOIN ".DB_PREFIX."base_goods_quality gq ON gq.sysno=sid.goods_quality_sysno 
			 LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno=bg.sysno 
			 LEFT JOIN ".DB_PREFIX."doc_stock_in si ON si.sysno=sid.stockin_sysno
			 LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno=sid.stock_sysno
			 LEFT JOIN ".DB_PREFIX."base_unit unit on bga.unit_sysno = unit.sysno {$where} {$orderby}";

            //error_log($sql, 3, 'sql_print.txt');
            $result['list'] = $this->dbh->select_page($sql);

        }
        return $result;

    }


    /**
     * 公共库存操作
     * @param array() type ：1车增加库存 2减少库存 3货权转移 4 倒罐 5 盘点 6 清库 7 查询库存量 8 锁定出库容量 9 退回出库锁定容量 10 罐容待入量更新 11船入库增加库存  12船出库作废 13 退回出库锁定容量 14 船入库作废 15 提单审核更新库存可用量和提单量|| data (相关字段) customer_buy (货权转移:买入方customer_sysno) storagetank_in (倒罐:导入罐sysno) ; 16 退货更新库存
     * @example
     * $S = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
     * $params = array('type'=>7);
     * $data = array(
     * );
     * $params['data'] = $data;
     * $ret = $S->pubstockoperation($params);
     * if(!$ret) {
     * $this->dbh->rollback();
     * return false;
     * }
     */

    public function pubstockoperation($params = array())
    {
        $data = $params['data'];
//        var_dump($data);die();
        if (!is_array($data)) {
            return false;
        }
        #键值名匹配库存表字段
        // $keyname = array('sysno','stockno','doctype','instockdate','firstdate','isclearstock','shipname','customer_sysno','customername','goods_sysno','goodsname','goods_quality_sysno','goodsqualityname','storagetank_sysno','instockqty','outstockqty','stockqty','checkqty','clockqty','clearstockstatus','iscurrent','status','isdel','version','created_at','updated_at','transferflag','transferqty','contract_sysno','goodsnature','shengyu','tankNum','stockretank_out_sysno','stockretank_in_sysno');
        // foreach ($data as $key => $value) {
        //     if(!in_array($key, $keyname)) {
        //         return false;
        //     }
        // }
        #库容量
        $totalinstockqty = $data['instockqty'] ? $data['instockqty'] : 0;
        $totaloutstockqty = $data['outstockqty'] ? $data['outstockqty'] : 0;
        $totalcheckqty = $data['checkqty'] ? $data['checkqty'] : 0;
        $totalclockqty = $data['clockqty'] ? $data['clockqty'] : 0;

        $introductionqty = $data['introductionqty'] ? $data['introductionqty'] : 0;

        #根据搜索条件,先获取库存表记录
        $filter = array();
        if (isset($data['customer_sysno']) && $data['customer_sysno'] != '') {
            $filter[] = " `customer_sysno` = '" . $data['customer_sysno'] . "' ";
        }
        if (isset($data['shipname']) && $data['shipname'] != '') {
            $filter[] = " `shipname` = '" . $data['shipname'] . "' ";
        }
        if (isset($data['goods_sysno']) && $data['goods_sysno'] != 0) {
            $filter[] = " `goods_sysno` = '" . $data['goods_sysno'] . "' ";
        }
        //不一样的储罐记录不一样
        if (isset($data['storagetank_sysno']) && $data['storagetank_sysno'] != 0) {
            $filter[] = " `storagetank_sysno` = '" . $data['storagetank_sysno'] . "' ";
        }
        if (isset($data['goods_quality_sysno']) && $data['goods_quality_sysno'] != 0) {
            $filter[] = " `goods_quality_sysno` = '" . $data['goods_quality_sysno'] . "' ";
        }
        if (isset($data['firstfrom_sysno']) && $data['firstfrom_sysno'] != '') {
            $filter[] = " `firstfrom_sysno` = '" . $data['firstfrom_sysno'] . "' ";
        }
        if (isset($data['doctype']) && $data['doctype'] != 0) {
            $filter[] = " `doctype` = '" . $data['doctype'] . "' ";
        }
        if (isset($data['stockno']) && $data['stockno'] != '') {
            $filter[] = " `stockno` = '" . $data['stockno'] . "' ";
        }
        //车入库库存 必须根据入库日期产生不同的库存记录
        if($params['type'] == 1){
            if (isset($data['instockdate']) && $data['instockdate'] != '') {
                $filter[] = " `instockdate` = '" . $data['instockdate'] . "' ";
            }
        }
        if (isset($data['sysno']) && $data['sysno'] != 0) {
            $filter[] = " `sysno` = '" . $data['sysno'] . "' ";
        }
        if (isset($data['fromid']) && $data['fromid'] != 0) {
            $filter[] = " `firstfrom_sysno` = '" . $data['fromid'] . "' ";
        }
        $where = ' isdel = 0 and status = 1 and iscurrent = 1';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }

        $sql = "select * from `".DB_PREFIX."storage_stock` where {$where} order by `instockdate`";
        if ($params['type'] == 7) {
            $stockinfo = $this->dbh->select($sql);
        } else {
            $stockinfo = $this->dbh->select_row($sql);

        }
//        var_dump($stockinfo);die();
        #业务类型
        switch ($params['type']) {
            case 1:
                if ($totalinstockqty <= 0) {
                    return false;
                }
                if (!empty($stockinfo)) {
                    #更新库存明细记录
                    $update = $stockinfo;
                    $update['updated_at'] = '=NOW()';
                    // $update['instockqty'] = $update['instockqty'] + $totalinstockqty - $data['ullage'];
                    $update['instockqty'] = $update['instockqty'] + $totalinstockqty;
                    $update['stockqty'] = $update['stockqty'] + $totalinstockqty - $data['ullage'];
                    $update['ullage'] = $update['ullage'] + $data['ullage'];
                    //更改最后操作状态和时间
                    $update['changetime'] = '=NOW()';
                    $update['changetype'] = $params['changetype'] ? $params['changetype'] : 0;
                    $res = $this->dbh->update(DB_PREFIX.'storage_stock', $update, 'sysno=' . intval($update['sysno']));
                    if (!$res) {
                        return false;
                    }
                    #备份记录
                    $stockinfo['ghostsysno'] = $stockinfo['sysno'];
                    unset($stockinfo['sysno']);
                    $stockinfo['updated_at'] = '=NOW()';
                    $stockinfo['iscurrent'] = 0;
                    $stockinfo['ghosttime'] = '=NOW()';
                    $stockinfo['ghosttype'] = 1;
                    $stockinfo['ghoststockqty'] = $update['stockqty'];
                    $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
                    if (!$res) {
                        return false;
                    }

                    $sysno = $update['sysno'];
                } else {
                    #新增库存明细记录
                    if (!isset($data['customer_sysno']) || !isset($data['goods_sysno']) || !isset($data['goods_quality_sysno']) || !isset($data['storagetank_sysno']) || !isset($data['doctype'])) {
                        return false;
                    }
                    unset($data['sysno']); //删除传入的SYSNO
                    unset($data['doc_sysno']);
                    $input = $data;
                    $input['stockno'] = COMMON::getCodeId('S');
                    $input['stockqty'] = $totalinstockqty - $data['ullage'];
                    // $input['instockqty'] = $totalinstockqty - $data['ullage'];
                    $input['instockqty'] = $totalinstockqty;
                    $input['created_at'] = '=NOW()';
                    $input['updated_at'] = '=NOW()';
                    $input['financedate'] = '=NOW()';
                    $input['ghosttime'] = '=NOW()';
                    $input['iscurrent'] = 1;
                    $input['firstfrom_sysno'] = $data['fromid'];
                    //更改最后操作状态和时间
                    $update['changetime'] = '=NOW()';
                    $update['changetype'] = $params['changetype'] ? $params['changetype'] : 0;
                    /**
                     * 第一次入库时回写入库入库订单日期
                     */
                    $this->dbh->update(DB_PREFIX.'doc_stock_in', ['stockindate'=> $data['instockdate']], 'sysno=' . intval($data['fromid']));
                    unset($input['storagetankname']);
                    unset($input['fromid']);
                    $sysno = $this->dbh->insert(DB_PREFIX.'storage_stock', $input);
                }
                #根据合同查询是否需要生成费用
                $ContractInstance = new ContractModel(Yaf_Registry::get('db'), Yaf_Registry::get('mc'));
                $contractdata = $ContractInstance -> getContractInfoById($data['contract_sysno']);
                $stockShipInstance = new StockshipinModel(Yaf_Registry::get('db'), Yaf_Registry::get('mc'));
                $ifcontract = $stockShipInstance -> ifcontract_instock($data['contract_sysno']);
                foreach ($contractdata as $key => $value) {
                    $value['customer_sysno'] = $value['customer_id'];
                    $value['customer_name'] = $value['customername'];
                    $value['contract_sysno'] = $value['sysno'];
                    $value['contract_no'] = $value['contractnodisplay'];
                    $value['totalprice']  = $value['yearamount'];
                    $value['doc_sysno'] = $params['data']['doc_sysno'];//增加费用单保存时候单据ID
                    //零租费用产生方式
                    if(in_array($value['contracttype'], [1,2]))
                    {
                        $value['instockqty'] = $totalinstockqty;
                        $value['stockindate'] = $data['instockdate'];
                        $value['stock_sysno'] = $sysno;
                        $value['ullage'] = $data['ullage'];
                        //$value['shipname'] = '槽车入库';
                        $value['firstfrom_no'] = $data['firstfrom_no'];
                        $value['stockin_sysno'] = $data['fromid'];
                        $value['qualityname'] = $data['goodsqualityname'];
                        $value['storagetankname'] = $data['storagetankname'];
                        $value['storagetank_sysno'] = $data['storagetank_sysno'];
                        $FinancecostInstance = new FinancecostModel(Yaf_Registry::get('db'), Yaf_Registry::get('mc'));
                        $res = $FinancecostInstance->costfirst($value);
                        if(!$res){
                            return false;
                        }
                    //整租产生费用 只有入库之日起 才会产生一条费用
                    }elseif(in_array($value['contracttype'], [3,4]) && $value['costtype'] == 2 && $ifcontract == false){
                        $FinancecostInstance = new FinancecostModel(Yaf_Registry::get('db'), Yaf_Registry::get('mc'));
                        $res = $FinancecostInstance->addFinancecostByTank($value, $value['instockdate'], $value['instockdate']);
                        if(!$res){
                            return false;
                        }
                    }
                }
                return $sysno;
            case 2:
                if ($totaloutstockqty <= 0) {
                    $msgcode = array(
                        'code' => 300,
                        'message' => '出库参数错误',
                    );
                    return $msgcode;
                }
                if (empty($stockinfo)) {
                    $msgcode = array(
                        'code' => 300,
                        'message' => '库存记录不存在',
                    );
                    return $msgcode;
                }

                $update = $stockinfo;
                $update['updated_at'] = '=NOW()';
                //出库时 实际数量可为负数
                   if($update['stockqty'] < $totalcheckqty){
                       $msgcode = array(
                           'code' => 300,
                           'message' => '库存数量不足',
                       );
                       return $msgcode;
                   }

                $update['outstockqty'] = $update['outstockqty'] + $totaloutstockqty;
                #新增超发量慨念  超出库存的设置成超发量
                $stockqty = $update['stockqty'] - $totaloutstockqty;
                if($stockqty < 0){
                    $update['beyondqty'] = $totaloutstockqty - $update['stockqty'];
                }
                $update['stockqty'] = $stockqty < 0 ? 0 : $stockqty;
                $update['clockqty'] = $update['clockqty'] - $totalclockqty;
                //更改最后操作状态和时间
                $update['changetime'] = '=NOW()';
                $update['changetype'] = $params['changetype'] ? $params['changetype'] : 0;
                $res = $this->dbh->update(DB_PREFIX.'storage_stock', $update, 'sysno=' . intval($update['sysno']));

                if (!$res) {
                    $msgcode = array(
                        'code' => 201,
                        'message' => '修改库存失败',
                    );
                    return $msgcode;
                }
                #备份记录
                $stockinfo['ghostsysno'] = $stockinfo['sysno'];
                unset($stockinfo['sysno']);
                $stockinfo['updated_at'] = '=NOW()';
                $stockinfo['iscurrent'] = 0;
                $stockinfo['ghosttime'] = '=NOW()';
                $stockinfo['ghosttype'] = 2;
                $stockinfo['ghoststockqty'] = $stockqty; //如果为负数直接记录 备份不做超发处理
                $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
                if (!$res) {
                    $msgcode = array(
                        'code' => 201,
                        'message' => '新增库存备份失败',
                    );
                    return $msgcode;
                }
                $msgcode = array(
                    'code' => 200,
                    'message' => $update['sysno'],
                );
                return $msgcode;
            case 3:

                #买入方判断
                if (!isset($params['customer_buy']) || !isset($params['contract_sysno'])) {
                    $msgcode = array(
                        'code' => 300,
                        'message' => '参数错误',
                    );
                    return $msgcode;
                } else {
                    $sql = 'select * from `'.DB_PREFIX.'customer` where sysno =' . intval($params['customer_buy']);
                    $customerinfo = $this->dbh->select_row($sql);
                    $customername = $customerinfo['customername'] ? $customerinfo['customername'] : '';
                }
                // if($totaloutstockqty<=0){
                //     $msgcode = array(
                //         'code' => 300,
                //         'message' => '参数错误',
                //     );
                //     return $msgcode;
                // }
                #卖方出货
                if (!empty($stockinfo)) {
                    $update = $stockinfo;
                    $update['updated_at'] = '=NOW()';
                    #判断单条余量
                    if ($update['stockqty'] <= 0) {
                        $msgcode = array(
                            'code' => 300,
                            'message' => '库存数量不足',
                        );
                        return $msgcode;
                    }
                    if ($update['stockqty'] >= $totaloutstockqty) {
                        $update['outstockqty'] = $update['outstockqty'] + $totaloutstockqty;
                        $update['stockqty'] = $update['stockqty'] - $totaloutstockqty;
                        // $update['clockqty'] = $update['clockqty'] - $totaloutstockqty>0 ? $update['clockqty'] - $totaloutstockqty : 0;
                    } else {
                        $update['outstockqty'] = $update['outstockqty'] + $update['stockqty'];
                        $update['stockqty'] = 0;
                        // $update['clockqty'] = 0;
                    }
                    //更改最后操作状态和时间
                    $update['changetime'] = '=NOW()';
                    $update['changetype'] = $params['changetype'] ? $params['changetype'] : 0;
                    $res = $this->dbh->update(DB_PREFIX.'storage_stock', $update, 'sysno=' . intval($update['sysno']));
                    if (!$res) {
                        $msgcode = array(
                            'code' => 201,
                            'message' => '数据库操作失败',
                        );
                        return $msgcode;
                    }
                    #备份记录
                    $stockinfo['ghostsysno'] = $stockinfo['sysno'];
                    unset($stockinfo['sysno']);
                    $stockinfo['updated_at'] = '=NOW()';
                    $stockinfo['iscurrent'] = 0;
                    $stockinfo['ghosttime'] = '=NOW()';
                    $stockinfo['ghosttype'] = 3;
                    $stockinfo['ghoststockqty'] = $update['stockqty'];
                    $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
                    if (!$res) {
                        $msgcode = array(
                            'code' => 201,
                            'message' => '数据库操作失败',
                        );
                        return $msgcode;
                    }

                    #买方入货
                    $input = $stockinfo;
                    $input['stockno'] = COMMON::getCodeId('S');
                    $input['customer_sysno'] = $params['customer_buy'];
                    $input['instockqty'] = $totaloutstockqty;
                    $input['outstockqty'] = 0;
                    $input['stockqty'] = $totaloutstockqty;
                    $input['doctype'] = 3;//货权转移类型
                    $input['created_at'] = '=NOW()';
                    $input['updated_at'] = '=NOW()';
                    $input['financedate'] = $data['stocktransdate'];//货权转移日期
                    $input['iscurrent'] = 1;
                    $input['ghosttype'] = 0;
                    $input['clockqty'] = 0;
                    $input['customername'] = $customername;
                    $input['ullage'] = 0;   //入库损耗为0
                    $input['introductionqty'] = 0;   //介绍信数量0
                    unset($input['contract_sysno']);
                    unset($input['transferflag']);
                    unset($input['transferqty']);
                    unset($input['overflag']);
                    unset($input['overqty']);

                    //新增买卖方合同id
                    if ($params['contract_sysno']) {
                        $input['contract_sysno'] = $params['contract_sysno'];
                    } else {
                        $msgcode = array(
                            'code' => 201,
                            'message' => '合同不能为空',
                        );
                        return $msgcode;
                    }

                    #货权转移不算中转量和溢罐量
                    // #获取中转量
                    // $search = array(
                    //     'customer_sysno' => $stockinfo['customer_sysno'],
                    //     'goods_sysno' => $stockinfo['goods_sysno'],
                    //     'goods_quality_sysno' => $stockinfo['goods_quality_sysno'],
                    //     'contract_sysno' => $stockinfo['contract_sysno'],
                    // );
                    // $transferqty = $this->pubtransferqty($search);
                    // #获取合同中转量
                    // $sql = 'select goodsqty from '.DB_PREFIX.'doc_contract_goods cg left join '.DB_PREFIX.'doc_contract c on (c.sysno=cg.contract_sysno) where cg.goods_quality_sysno = ' . $stockinfo['goods_quality_sysno'] . ' and cg.contract_sysno = ' . $data['contract_sysno'] . ' and cg.goods_sysno = ' . $stockinfo['goods_sysno'] . ' and c.contracttype=3 ';
                    // $contract_transferqty = $this->dbh->select_one($sql);
                    // if ($contract_transferqty > 0) {
                    //     $transferflagnum = $totalinstockqty + $transferqty - $contract_transferqty;
                    //     if ($transferflagnum > 0) {
                    //         $input['transferflag'] = 1;
                    //         if ($transferqty < $contract_transferqty) {
                    //             $input['transferqty'] = $contract_transferqty - $transferqty;
                    //         } else {
                    //             $input['transferqty'] = 0;
                    //         }
                    //     }
                    // }

                    $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $input);
                    if (!$res) {
                        $msgcode = array(
                            'code' => 201,
                            'message' => '数据库操作失败',
                        );
                        return $msgcode;
                    }

                    $msgcode = array(
                        'code' => 200,
                        'message' => $res,
                    );
                    return $msgcode;
                } else {
                    $msgcode = array(
                        'code' => 300,
                        'message' => '库存记录不存在',
                    );
                    return $msgcode;
                }
            case 4:
//                if(false){
//                    if(!isset($params['storagetank_in'])){
//                        return false;
//                    }
//                    if($totaloutstockqty<=0){
//                        return false;
//                    }
//                    if(!empty($stockinfo))
//                    {
//                        #倒罐出
//                        $update = $stockinfo;
//                        $update['updated_at'] = '=NOW()';
//                        #判断单条余量
//                        if($update['stockqty']<=0)
//                        {
//                            return false;
//                        }
//                        if($update['stockqty']>=$totaloutstockqty)
//                        {
//                            $update['outstockqty'] = $update['outstockqty'] + $totaloutstockqty;
//                            $update['stockqty'] = $update['stockqty'] - $totaloutstockqty;
//                        }
//                        else
//                        {
//                            $update['outstockqty'] = $update['outstockqty'] + $update['stockqty'];
//                            $update['stockqty'] = 0;
//                        }
//                        $res = $this->dbh->update(DB_PREFIX.'storage_stock', $update, 'sysno=' . intval($update['sysno']));
//                        if(!$res){
//                            return false;
//                        }
//                        #备份记录
//                        unset($stockinfo['sysno']);
//                        $stockinfo['updated_at'] = '=NOW()';
//                        $stockinfo['iscurrent'] = 0;
//                        $stockinfo['ghosttime'] = '=NOW()';
//                        $stockinfo['ghosttype'] = 4;
//                        $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
//                        if(!$res){
//                            return false;
//                        }
//
//                        #倒罐入
//                        $input = $stockinfo;
//                        $input['stockno'] = COMMON::getCodeId('S');
//                        $input['storagetank_sysno'] = $params['storagetank_in'];
//                        $input['instockqty'] = $totaloutstockqty;
//                        $input['outstockqty'] = 0;
//                        $input['stockqty'] = $totaloutstockqty;
//                        $input['created_at'] = '=NOW()';
//                        $input['updated_at'] = '=NOW()';
//                        $input['ghosttime'] = '=NOW()';
//                        $input['financedate'] = '=NOW()';
//                        $input['iscurrent'] = 1;
//                        $input['ghosttype'] = 0;
//
//                        $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $input);
//                        if(!$res){
//                            return false;
//                        }
//
//                        return $res;
//                    }
//                    else
//                    {
//                        return false;
//                    }
//                }
                if (!isset($params)) {
                    return false;
                }
                if (empty($params['data'])) {
                    return false;
                }
                $date = $params['data'];
                #倒罐出
                $update['updated_at'] = '=NOW()';
                #减少出罐的当前罐容
                $update['tank_stockqty'] = $date['shengyu'];
                $res = $this->dbh->update(DB_PREFIX.'base_storagetank', $update, 'sysno=' . intval($date['stockretank_out_sysno']));
                if (!$res) {
                    return false;
                }

                #更新倒出罐库存记录信息
                $stockinfo['updated_at'] = '=NOW()';
                $stockinfo['instockqty'] = $stockinfo['instockqty'] - $totalinstockqty;
                $stockinfo['outstockqty'] = $stockinfo['outstockqty'] + $totalinstockqty;
                $stockinfo['stockqty'] = $stockinfo['stockqty'] - $totalinstockqty;
                $res = $this->dbh->update(DB_PREFIX.'storage_stock', $stockinfo, 'sysno=' . intval($data['sysno']));
                if (!$res) {
                    return false;
                }
                #备份记录
                $stockinfo['ghostsysno'] = $stockinfo['sysno'];
                unset($stockinfo['sysno']);
                $stockinfo['updated_at'] = '=NOW()';
                $stockinfo['iscurrent'] = 0;
                $stockinfo['ghosttime'] = '=NOW()';
                $stockinfo['ghosttype'] = 4;
                $stockinfo['ghoststockqty'] = $stockinfo['stockqty'];
                $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
                if (!$res) {
                    return false;
                }

                #倒罐入库新增库存记录
                $input = $stockinfo;
                $input['stockno'] = COMMON::getCodeId('S');
                $input['storagetank_sysno'] = $data['stockretank_in_sysno'];
                $input['instockqty'] = $totalinstockqty;
                $input['outstockqty'] = 0;
                $input['stockqty'] = $totalinstockqty;
                $input['created_at'] = '=NOW()';
                $input['updated_at'] = '=NOW()';
                $input['ghosttime'] = '=NOW()';
                $input['iscurrent'] = 1;
                $input['ghosttype'] = 0;
                $input['ghostsysno'] = 0;
                $input['doctype'] = 5;
                $input['changetype'] = 8;
                $input['isclearstock'] = 0;
                $input['ullage'] = 0;
                unset($input['sysno']);
                $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $input);
                $instocksysno = $res;
                if(!$res){
                    return false;
                }

                ##获取新库存id，更新倒罐明细
                $arr = array(
                    'in_stock_sysno'=>$res,
                    'stock_sysno'=>$res
                );
                $res = $this->dbh->update(DB_PREFIX.'doc_stock_retank_detail', $arr, 'sysno=' . intval($data['retankdetail_sysno']));
                if (!$res) {
                    return false;
                }
                return $instocksysno;


//                #倒罐入增加入罐罐容
//                $update['tank_stockqty'] = $date['tankNum'];
//                $res = $this->dbh->update(DB_PREFIX.'base_storagetank', $update, 'sysno=' . intval($date['stockretank_in_sysno']));
//                if (!$res) {
//                    return false;
//                }
//                return $res;


            case 5:
                if (!isset($data['checkqty'])) {
                    return false;
                }
                #更新盘点库存
                if (!empty($stockinfo)) {
                    $update = $stockinfo;
                    $update['updated_at'] = '=NOW()';
                    $update['checkqty'] = $update['checkqty'] + $totalcheckqty;
                    // $update['stockqty'] = $update['stockqty'] + $totalcheckqty > 0 ? $update['stockqty'] + $totalcheckqty : 0;
                    $res = $this->dbh->update(DB_PREFIX.'storage_stock', $update, 'sysno=' . intval($update['sysno']));
                    if (!$res) {
                        return false;
                    }
                    #备份记录
                    $stockinfo['ghostsysno'] = $stockinfo['sysno'];
                    unset($stockinfo['sysno']);
                    $stockinfo['updated_at'] = '=NOW()';
                    $stockinfo['iscurrent'] = 0;
                    $stockinfo['ghosttime'] = '=NOW()';
                    $stockinfo['ghosttype'] = 5;
                    $stockinfo['ghoststockqty'] = $stockinfo['stockqty'];
                    $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
                    if (!$res) {
                        return false;
                    }

                    return $update['sysno'];
                } else {
                    return false;
                }

                return true;
            case 6:
                #清库
                if (!empty($stockinfo)) {
                    $update = $stockinfo;
                    $update['updated_at'] = '=NOW()';
                    $update['stockqty'] = 0;
                    $update['isclearstock'] = 1;
                    $update['clearqty'] = $data['clearqty'];
                    $update['clearstockstatus'] = 2;

                    $res = $this->dbh->update(DB_PREFIX.'storage_stock', $update, 'sysno=' . intval($update['sysno']));
                    if (!$res) {
                        return false;
                    }
                    #备份记录
                    $stockinfo['ghostsysno'] = $stockinfo['sysno'];
                    unset($stockinfo['sysno']);
                    $stockinfo['updated_at'] = '=NOW()';
                    $stockinfo['iscurrent'] = 0;
                    $stockinfo['ghosttime'] = '=NOW()';
                    $stockinfo['ghosttype'] = 6;
                    $stockinfo['ghoststockqty'] = $update['stockqty'];
                    $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
                    if (!$res) {
                        return false;
                    }

                    return $update['sysno'];
                } else {
                    return false;
                }
            case 7:
                $totalstockqty = 0;
                if (!empty($stockinfo)) {
                    foreach ($stockinfo as $key => $value) {
                        $totalstockqty = $totalstockqty + $value['stockqty'] - $value['clockqty'];
                    }

                    return $totalstockqty;
                } else {
                    return $totalstockqty;
                }
            case 8:
                if ($totalclockqty <= 0) {
                    $msgcode = array(
                        'code' => 300,
                        'message' => '预约锁定库存参数错误',
                    );
                    return $msgcode;
                }
                if (!empty($stockinfo)) {
                    $update = $stockinfo;
                    $update['updated_at'] = '=NOW()';
                    #判断单条余量
                    // if ($update['stockqty'] - $update['clockqty'] - $totalclockqty < 0) {
                    //     $msgcode = array(
                    //         'code' => 300,
                    //         'message' => '预约库存数量不足',
                    //     );
                    //     return $msgcode;
                    // }
                    $update['clockqty'] = $update['clockqty'] + $totalclockqty;

                    $res = $this->dbh->update(DB_PREFIX.'storage_stock', $update, 'sysno=' . intval($update['sysno']));
                    if (!$res) {
                        $msgcode = array(
                            'code' => 201,
                            'message' => '数据库操作失败',
                        );
                        return $msgcode;
                    }
                    #备份记录
                    $stockinfo['ghostsysno'] = $stockinfo['sysno'];
                    unset($stockinfo['sysno']);
                    $stockinfo['updated_at'] = '=NOW()';
                    $stockinfo['iscurrent'] = 0;
                    $stockinfo['ghosttime'] = '=NOW()';
                    $stockinfo['ghosttype'] = 8;
                    $stockinfo['ghoststockqty'] = $update['stockqty'];
                    $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
                    if (!$res) {
                        $msgcode = array(
                            'code' => 201,
                            'message' => '数据库操作失败',
                        );
                        return $msgcode;
                    }

                    $msgcode = array(
                        'code' => 200,
                        'message' => $update['sysno'],
                    );
                    return $msgcode;
                } else {
                    // error_log(date("Y-m-d H:i:s") . "\t" . json_encode($params) . "\n", 3, './logs/stock_err.log');
                    $msgcode = array(
                        'code' => 300,
                        'message' => '库存记录不存在',
                    );
                    return $msgcode;
                }
            // case 9:
            //     if ($totalclockqty <= 0) {
            //         $msgcode = array(
            //             'code' => 300,
            //             'message' => '预约锁定库存参数错误',
            //         );
            //         return $msgcode;
            //     }
            //     if (!empty($stockinfo)) {
            //         $update = $stockinfo;
            //         $update['updated_at'] = '=NOW()';
            //         #判断单条余量
            //         if ($update['stockqty'] - $update['clockqty'] - $totalclockqty < 0) {
            //             $msgcode = array(
            //                 'code' => 300,
            //                 'message' => '预约库存数量不足',
            //             );
            //             return $msgcode;
            //         }
            //         $update['clockqty'] = $update['clockqty'] + $totalclockqty;

            //         $res = $this->dbh->update(DB_PREFIX.'storage_stock', $update, 'sysno=' . intval($update['sysno']));
            //         if (!$res) {
            //             $msgcode = array(
            //                 'code' => 201,
            //                 'message' => '数据库操作失败',
            //             );
            //             return $msgcode;
            //         }
            //         #备份记录
            //         $stockinfo['ghostsysno'] = $stockinfo['sysno'];
            //         unset($stockinfo['sysno']);
            //         $stockinfo['updated_at'] = '=NOW()';
            //         $stockinfo['iscurrent'] = 0;
            //         $stockinfo['ghosttime'] = '=NOW()';
            //         $stockinfo['ghosttype'] = 9;
            //         $stockinfo['ghoststockqty'] = $update['stockqty'];
            //         $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
            //         if (!$res) {
            //             $msgcode = array(
            //                 'code' => 201,
            //                 'message' => '数据库操作失败',
            //             );
            //             return $msgcode;
            //         }

            //         $msgcode = array(
            //             'code' => 200,
            //             'message' => $update['sysno'],
            //         );
            //         return $msgcode;
            //     } else {
            //         $msgcode = array(
            //             'code' => 300,
            //             'message' => '库存记录不存在',
            //         );
            //         return $msgcode;
            //     }
            case 10:
                $update['orderinqty'] = $data['orderinqty'];
                $update['goods_sysno'] = $data['goods_sysno'];

                $res = $this->dbh->update(DB_PREFIX.'base_storagetank', $update, 'sysno=' . intval($data['storagetank_sysno']));
                if (!$res) {
                    return false;
                }
                return true;
            case 11:
                if ($totalinstockqty <= 0) {
                    return false;
                }

                if (!empty($stockinfo)) {
                    #更新库存明细记录
                    $update = $stockinfo;
                    $update['stockqty'] = $totalcheckqty;
                    $update['updated_at'] = '=NOW()';
                    $update['instockqty'] = $params['beqty'];
                    $update['stockqty'] = $params['beqty'];
                    $update['firstfrom_sysno'] = $params['fromid'];
                    $update['firstfrom_no'] = $params['firstfrom_no'];
                    $update['contract_sysno'] = $params['contract_sysno'];
                    $res = $this->dbh->update(DB_PREFIX.'storage_stock', $update, 'sysno=' . intval($update['sysno']));
                    if (!$res) {
                        return false;
                    }
                    #备份记录
                    $stockinfo['ghostsysno'] = $stockinfo['sysno'];
                    unset($stockinfo['sysno']);
                    $stockinfo['updated_at'] = '=NOW()';
                    $stockinfo['iscurrent'] = 0;
                    $stockinfo['ghosttime'] = '=NOW()';
                    $stockinfo['ghosttype'] = 1;
                    $stockinfo['firstfrom_sysno'] = $params['fromid'];
                    $stockinfo['firstfrom_no'] = $params['firstfrom_no'];
                    $stockinfo['contract_sysno'] = $params['contract_sysno'];
                    $stockinfo['ghoststockqty'] = $update['stockqty'];
                    $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
                    if (!$res) {
                        return false;
                    }
                    $sysno = $update['sysno'];
                } else {
                    #新增库存明细记录
                    if (!isset($data['customer_sysno']) || !isset($data['goods_sysno']) || !isset($data['goods_quality_sysno']) || !isset($data['storagetank_sysno']) || !isset($data['doctype'])) {
                        return false;
                    }
                    $input = $data;
                    unset($input['unitname']);
                    unset($input['qualityname']);
                    unset($input['takegoodsnum' ]);
                    unset($input['release_no']);
                    $input['goodsqualityname'] = $data['goodsqualityname'];
                    $input['stockno'] = COMMON::getCodeId('S');
                    #$input['stockqty'] = $totalinstockqty;
                    $input['stockqty'] = $params['beqty'];
                    $input['created_at'] = '=NOW()';
                    $input['updated_at'] = '=NOW()';
                    $input['financedate'] = '=NOW()';
                    $input['ghosttime'] = '=NOW()';
                    $input['iscurrent'] = 1;
                    $input['firstfrom_sysno'] = $params['fromid'];
                    $input['firstfrom_no'] = $params['firstfrom_no'];
                    $input['contract_sysno'] = $params['contract_sysno'];

                    $sysno = $this->dbh->insert(DB_PREFIX.'storage_stock', $input);
                    if (!$sysno) {
                        return false;
                    }
                }
                #获取中转量
                $search = array(
                    'instockqty' => $params['beqty'],
                    'customer_sysno' => $data['customer_sysno'],
                    'goods_sysno' => $data['goods_sysno'],
                    'goods_quality_sysno' => $data['goods_quality_sysno'],
                    'contract_sysno' => $data['contract_sysno'],
                    'doctype' => 1
                );
                $transferqty = $this->pubtransferqty($search);

                if ($transferqty['code'] == '200' && !empty($transferqty['message'])) {
                    $search = array(
                        'firstfrom_sysno' => $params['fromid'],
                        'firstfrom_no' => $params['firstfrom_no'],
                    );
                    $stockinfo = $this->searchstock($search);
                    if (!empty($stockinfo)) {
                        $res = $this->updatestock($sysno, $transferqty['message']);
                    } else {
                        COMMON::result(300, '未查询到该入库单的库存');
                        return;
                    }
                }
                $financecost = new FinancecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $contract = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $cttlist = $contract->getContractById($data['contract_sysno']);
                //中转量仓储费
                if (isset($transferqty['message']['transferqty'])) {
                    $transferqty = $transferqty['message']['transferqty'];
                } else {
                    $transferqty = 0;
                }
                $storage = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $tankinfo = $storage->getStoragetankById($data['storagetank_sysno']);

                $array = array(
                    'contract_sysno' => $data['contract_sysno'],
                    'contract_no' => $cttlist['contractnodisplay'],
                    'stockin_sysno' => $params['sysno_stockin'],
                    'stockin_no' => $params['inno_stockin'],
                    'instockqty' => $params['beqty'],
                    'stockindate' => $params['stockindate'],
                    'goods_sysno' => $data['goods_sysno'],
                    'goodsname' => $data['goodsname'],
                    'qualityname' => $data['goodsqualityname'],
                    'firstdate' => $data['firstdate'],
                    'customer_sysno' => $data['customer_sysno'],
                    'customer_name' => $data['customername'],
                    'stock_sysno' => $sysno,
                    'transferqty' => $transferqty,
                    'shipname' => $data['shipname'],
                    'storagetankname' => $tankinfo['storagetankname'],
                );
                //中转量仓储费
                $res = $financecost->costtransfer($array);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
                //首期管道输送费
                $res = $financecost->costtransportamount($array);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }
                //更新库存记录的超溢罐量
                $params = array(
                    'instockqty' => $data['instockqty'],
                    'customer_sysno' => $data['customer_sysno'],
                    'goods_sysno' => $data['goods_sysno'],
                    'contract_sysno' => $data['contract_sysno'],
                    'storagetank_sysno' => $data['storagetank_sysno'],
                    'doctype' => 1,
                );
                $yi = $this->puboverqty($params);
                if ($yi['code'] == '200' && !empty($yi['message'])) {
                    $search = array(
                        'firstfrom_sysno' => $params['fromid'],
                        'firstfrom_no' => $params['firstfrom_no'],
                    );
                    $stockinfo = $this->searchstock($search);
                    if (!empty($stockinfo)) {
                        $res = $this->updatestock($sysno, $yi['message']);
                    } else {
                        COMMON::result(300, '未查询到该入库单的库存');
                        return;
                    }
                }
                $array['overqty'] = isset($yi['message']['overqty'])?$yi['message']['overqty']:0;
                //计算首期费
                $res = $financecost->costfirst($array);
                if (!$res) {
                    $this->dbh->rollback();
                    return false;
                }

                //储罐信息更改
                $storagetank_sysno = $data['storagetank_sysno'] ? $data['storagetank_sysno'] : 0;

                //检车是否存在该储罐 TODO 储罐其他信息的验证
                $sql_storagetank = "SELECT * FROM `".DB_PREFIX."base_storagetank` WHERE sysno = {$storagetank_sysno} AND status = 1 AND isdel = 0";
                $storagetankInfo = $this->dbh->select_row($sql_storagetank);

                if (!$storagetankInfo) {
                    return false;
                }
                return $sysno;
            case 12:
                if ($totaloutstockqty <= 0) {
                    $msgcode = array(
                        'code' => 300,
                        'message' => '出库参数错误',
                    );
                    return $msgcode;
                }

                if (!empty($stockinfo)) {
                    $update = $stockinfo;
                    $update['updated_at'] = '=NOW()';
                    // #判断单条余量
                    // if ($update['stockqty'] <= 0) {
                    //     $msgcode = array(
                    //         'code' => 300,
                    //         'message' => '库存数量不足',
                    //     );
                    //     return $msgcode;
                    // }

                    $update['outstockqty'] = $update['outstockqty'] - $totaloutstockqty;
                    $update['stockqty'] = $update['stockqty'] + $totaloutstockqty;
                    $update['clockqty'] = $update['clockqty'] + $totalclockqty;

                    $res = $this->dbh->update(DB_PREFIX.'storage_stock', $update, 'sysno=' . intval($update['sysno']));

                    if (!$res) {
                        $msgcode = array(
                            'code' => 201,
                            'message' => '数据库操作失败',
                        );
                        return $msgcode;
                    }
                    #备份记录
                    $stockinfo['ghostsysno'] = $stockinfo['sysno'];
                    unset($stockinfo['sysno']);
                    $stockinfo['updated_at'] = '=NOW()';
                    $stockinfo['iscurrent'] = 0;
                    $stockinfo['ghosttime'] = '=NOW()';
                    $stockinfo['ghosttype'] = 2;
                    $stockinfo['ghoststockqty'] = $update['stockqty'];
                    $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);

                    if (!$res) {
                        $msgcode = array(
                            'code' => 201,
                            'message' => '数据库操作失败',
                        );
                        return $msgcode;
                    }

                    $msgcode = array(
                        'code' => 200,
                        'message' => $update['sysno'],
                    );
                    return $msgcode;
                } else {
                    $msgcode = array(
                        'code' => 300,
                        'message' => '库存记录不存在',
                    );
                    return $msgcode;
                }
            case 13:
                if ($totalclockqty < 0) {
                    $msgcode = array(
                        'code' => 300,
                        'message' => '预约锁定库存参数错误',
                    );
                    return $msgcode;
                }
                if (!empty($stockinfo)) {
                    $update = $stockinfo;
                    $update['updated_at'] = '=NOW()';
                    #判断单条余量
                    // if ($update['stockqty'] - $update['clockqty'] - $totalclockqty < 0) {
                    //     $msgcode = array(
                    //         'code' => 300,
                    //         'message' => '预约库存数量不足',
                    //     );
                    //     return $msgcode;
                    // }
                    $update['clockqty'] = $update['clockqty'] - $totalclockqty;

                    $res = $this->dbh->update(DB_PREFIX.'storage_stock', $update, 'sysno=' . intval($update['sysno']));
                    if (!$res) {
                        $msgcode = array(
                            'code' => 201,
                            'message' => '数据库操作失败',
                        );
                        return $msgcode;
                    }
                    #备份记录
                    $stockinfo['ghostsysno'] = $stockinfo['sysno'];
                    unset($stockinfo['sysno']);
                    $stockinfo['updated_at'] = '=NOW()';
                    $stockinfo['iscurrent'] = 0;
                    $stockinfo['ghosttime'] = '=NOW()';
                    $stockinfo['ghosttype'] = 9;
                    $stockinfo['ghoststockqty'] = $update['stockqty'];
                    $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
                    if (!$res) {
                        $msgcode = array(
                            'code' => 201,
                            'message' => '数据库操作失败',
                        );
                        return $msgcode;
                    }

                    $msgcode = array(
                        'code' => 200,
                        'message' => $update['sysno'],
                    );
                    return $msgcode;
                } else {
                    $msgcode = array(
                        'code' => 300,
                        'message' => '库存记录不存在',
                    );
                    return $msgcode;
                }
            case 14:
                #作废状态 更新库存
                if ($totalinstockqty <= 0) {
                    $array = [
                        'code' => 300,
                        'msg' => '库存信息不正确！'
                    ];
                }
                if (!empty($stockinfo)) {
                    #更新库存明细记录
                    $update = $stockinfo;
                    $update['stockqty'] = $update['stockqty'] -$totalinstockqty;
                    $update['updated_at'] = '=NOW()';
                    $update['iscurrent'] = 0;
                    $res = $this->dbh->update(DB_PREFIX.'storage_stock', $update, 'sysno=' . intval($data['stock_sysno']));

                    if (!$res) {
                        $array = [
                            'code' => 300,
                            'msg' => '库存明细更新失败！'
                        ];
                    }
                    #备份记录
                    $stockinfo['ghostsysno'] = $stockinfo['sysno'];
                    unset($stockinfo['sysno']);
                    $stockinfo['updated_at'] = '=NOW()';
                    $stockinfo['iscurrent'] = 0;
                    $stockinfo['ghosttime'] = '=NOW()';
                    $stockinfo['ghosttype'] = 1;
                    $stockinfo['firstfrom_sysno'] = $params['fromid'];
                    $stockinfo['firstfrom_no'] = $params['firstfrom_no'];
                    $stockinfo['contract_sysno'] = $params['contract_sysno'];
                    $stockinfo['ghoststockqty'] = $update['stockqty'];

                    $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
                    if (!$res) {
                        $array = [
                            'code' => 300,
                            'msg' => '备份记录添加失败'
                        ];
                    }
                } else {
                    $array = [
                        'code' => 300,
                        'msg' => '没有找到相关库存明细'
                    ];
                }
                if ($array['code'] == 300) {
                    return false;
                } else {
                    return true;
                }
            case 15:
                #提单审核 更新库存
                if ($introductionqty < 0) {
                    $array = [
                        'code' => 300,
                        'msg' => '库存信息不正确！'
                    ];
                    return $array;
                }
                if (!empty($stockinfo)) {
                    #更新库存明细记录
                    $update['stockqty'] = $stockinfo['stockqty'] - $introductionqty;
                    $update['introductionqty'] = $stockinfo['introductionqty'] + $introductionqty;
                    $update['updated_at'] = '=NOW()';
                    if($update['stockqty'] < 0){
                        $array = [
                            'code' => 300,
                            'msg' => '库存不能为负'
                        ];
                        return $array;
                    }
                    $res = $this->dbh->update(DB_PREFIX.'storage_stock', $update, 'sysno=' . intval($data['sysno']));

                    if (!$res) {
                        $array = [
                            'code' => 300,
                            'msg' => '库存明细更新失败！'
                        ];
                        return $array;
                    }
                    #备份记录
                    $stockinfo['ghostsysno'] = $stockinfo['sysno'];
                    unset($stockinfo['sysno']);
                    $stockinfo['updated_at'] = '=NOW()';
                    $stockinfo['iscurrent'] = 0;
                    $stockinfo['ghosttime'] = '=NOW()';
                    $stockinfo['ghosttype'] = 13;
                    $stockinfo['ghoststockqty'] = $update['stockqty'];

                    $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
                    if (!$res) {
                        $array = [
                            'code' => 300,
                            'msg' => '备份记录添加失败'
                        ];
                        return $array;
                    }
                    $array = [
                        'code' => 200,
                        'msg' => '更新库存成功'
                    ];
                } else {
                    $array = [
                        'code' => 300,
                        'msg' => '没有找到相关库存明细'
                    ];
                }
                return $array;
            case 16: //退货入库
                if (empty($stockinfo)) {
                    return [
                        'code' => 300,
                        'message' => '未找到该库存'
                    ];
                }
                #更新库存明细记录
                $update = $stockinfo;
                $update['updated_at'] = '=NOW()';
                $update['clockqty'] = $update['clockqty'] + $totalinstockqty;
                $update['stockqty'] = $update['stockqty'] + $totalinstockqty;
                $update['outstockqty'] = $update['outstockqty'] - $totalinstockqty;
                //更改最后操作状态和时间
                $update['changetime'] = '=NOW()';
                $update['changetype'] = 7;
                $res = $this->dbh->update(DB_PREFIX.'storage_stock', $update, 'sysno=' . intval($update['sysno']));
                if (!$res) {
                    return [
                        'code' => 300,
                        'message' => '修改库存失败'
                    ];
                }
                #备份记录
                $stockinfo['ghostsysno'] = $stockinfo['sysno'];
                unset($stockinfo['sysno']);
                $stockinfo['updated_at'] = '=NOW()';
                $stockinfo['iscurrent'] = 0;
                $stockinfo['ghosttime'] = '=NOW()';
                $stockinfo['ghosttype'] = 15;
                $stockinfo['ghoststockqty'] = $update['stockqty'];
                $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
                if (!$res) {
                    return [
                        'code' => 300,
                        'message' => '备份库存失败'
                    ];
                }
                return [
                    'code' => 200,
                    'message' => $update['sysno']
                ];
            default:
                return false;
        }

    }

    /**
     * 获取中转量
     */
    public function pubtransferqty($params = array())
    {
        if (!is_array($params)) {
            $msgcode = array(
                'code' => 300,
                'message' => '参数错误',
            );
            return $msgcode;
        }

        //该方法在订单终止时候调用 实际数量是订单的总数量
        //必传参数
        //$params['instockqty'] 本次入库数量 $params['customer_sysno'] $params['goods_sysno'] $params['contract_sysno'] $params['doctype'] 1是船 2是车
        //返回transferflag代表订单有中转标记，transferqty表示更新库存中转拆分量的字段

        $filter = array();
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " s.`customer_sysno` = '" . $params['customer_sysno'] . "' ";
        }
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " s.`goods_sysno` = '" . $params['goods_sysno'] . "' ";
        }
        if (isset($params['contract_sysno']) && $params['contract_sysno'] != '') {
            $filter[] = " s.`contract_sysno` = '" . $params['contract_sysno'] . "' ";
        }

        $where = ' s.isdel = 0 and s.status = 1 and s.iscurrent = 1 and s.doctype in (1,2,4) and sd.stockinstatus=4 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }

        $sql_in = "select s.* from `".DB_PREFIX."storage_stock` s left join `".DB_PREFIX."doc_stock_in` sd on (sd.`sysno`=s.`firstfrom_sysno`)  where {$where} group by s.`sysno` order by s.`instockdate`";

        $stockinfo_in = $this->dbh->select($sql_in);

        //error_log(date("Y-m-d H:i:s") . "\t"  . json_encode($stockinfo_in) . "\n", 3, './logs/stockcarin.log');

        $transferqty = 0;
        if (!empty($stockinfo_in)) {
            foreach ($stockinfo_in as $key => $value) {
                $transferqty = $transferqty + $value['instockqty'];
            }
        }
        $transferqty = $transferqty - $params['instockqty'];

        $input = array();

        $sql = "select * from ".DB_PREFIX."doc_contract c left join ".DB_PREFIX."doc_contract_goods cg on (c.sysno=cg.contract_sysno) where c.sysno = " . $params['contract_sysno'] . " and cg.goods_sysno=" . $params['goods_sysno'];
        $contractinfo = $this->dbh->select($sql);
        $yearqty = 0;
        foreach ($contractinfo as $key => $value) {
            $yearqty = $yearqty + $value['yearqty'];
        }

        if ($contractinfo[0]['contracttype'] == 3 || $contractinfo[0]['contracttype'] == 4) {
            if ($yearqty <= 0) {
                $msgcode = array(
                    'code' => 200,
                    'message' => $input,
                );
                return $msgcode;
            }
            #包罐计算中转量
            $transferflagnum = $params['instockqty'] + $transferqty - $yearqty;
            if ($transferflagnum > 0) {
                $input['transferflag'] = 1;
                if ($transferflagnum <= $params['instockqty']) {
                    $input['transferqty'] = $transferflagnum;
                } else {
                    $input['transferqty'] = $params['instockqty'];
                }
            }
            $msgcode = array(
                'code' => 200,
                'message' => $input,
            );

            //error_log(date("Y-m-d H:i:s") . "\t"  . json_encode($msgcode) . "\n", 3, './logs/stockcarin.log');
            return $msgcode;
        } else {
            $msgcode = array(
                'code' => 301,
                'message' => '非整租合同',
            );
            return $msgcode;
        }
    }

    /**
     * 获取溢罐量
     */
    public function puboverqty($params = array())
    {
        if (!is_array($params)) {
            $msgcode = array(
                'code' => 300,
                'message' => '参数错误',
            );
            return $msgcode;
        }

        //该方法在订单终止时候调用 实际数量是订单的总数量
        //必传参数 
        //$params['instockqty'] 本次入库数量 $params['customer_sysno'] $params['goods_sysno'] $params['contract_sysno'] $params['storagetank_sysno'] $params['doctype'] 1是船 2是车
        //返回overflag代表订单有中转标记，overqty表示更新库存溢罐拆分量的字段

        $filter = array();
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " s.`customer_sysno` = '" . $params['customer_sysno'] . "' ";
        }
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " s.`goods_sysno` = '" . $params['goods_sysno'] . "' ";
        }
        if (isset($params['contract_sysno']) && $params['contract_sysno'] != '') {
            $filter[] = " s.`contract_sysno` = '" . $params['contract_sysno'] . "' ";
        }

        $where = ' s.isdel = 0 and s.status = 1 and s.iscurrent = 1 and s.doctype in (1,2,4) and sd.stockinstatus=4 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }

        $sql_in = "select s.* from `".DB_PREFIX."storage_stock` s left join `".DB_PREFIX."doc_stock_in` sd on (sd.`sysno`=s.`firstfrom_sysno`)  where {$where} group by s.`sysno` order by s.`instockdate`";

        $stockinfo_in = $this->dbh->select($sql_in);

        //error_log(date("Y-m-d H:i:s") . "\t"  . json_encode($stockinfo_in) . "\n", 3, './logs/stockcarin.log');

        $overqty = 0;
        if (!empty($stockinfo_in)) {
            foreach ($stockinfo_in as $key => $value) {
                $overqty = $overqty + $value['stockqty'];
            }
        }
        $overqty = $overqty - $params['instockqty'];

        $input = array();

        $sql = "select * from ".DB_PREFIX."doc_contract c left join ".DB_PREFIX."doc_contract_goods cg on (c.sysno=cg.contract_sysno) where c.sysno = " . $params['contract_sysno'] . " and cg.goods_sysno=" . $params['goods_sysno'];
        $contractinfo = $this->dbh->select($sql);
        $overcapacity = 0;
        foreach ($contractinfo as $key => $value) {
            $overcapacity = $overcapacity + $value['overcapacity'];
        }

        if ($contractinfo[0]['contracttype'] == 3 || $contractinfo[0]['contracttype'] == 4) {
            if ($overcapacity <= 0) {
                $msgcode = array(
                    'code' => 200,
                    'message' => $input,
                );
                return $msgcode;
            }
            #包罐容计算溢出量
            $overqtyflagnum = $params['instockqty'] + $overqty - $overcapacity;
            if ($overqtyflagnum > 0) {
                $input['overflag'] = 1;
                if ($overqtyflagnum <= $params['instockqty']) {
                    $input['overqty'] = $overqtyflagnum;
                } else {
                    $input['overqty'] = $params['instockqty'];
                }
            }
            $msgcode = array(
                'code' => 200,
                'message' => $input,
            );

            //error_log(date("Y-m-d H:i:s") . "\t"  . json_encode($msgcode) . "\n", 3, './logs/stockcarin.log');

            return $msgcode;
        } else {
            $msgcode = array(
                'code' => 301,
                'message' => '非整租合同',
            );
            return $msgcode;
        }
    }


    //获取单个库存和详细信息
    public function getElementById($ids)
    {
        $ids_str = implode(',', $ids);
        $sql = "SELECT DISTINCT s.*,si.stockinno,st.storagetankname,q.qualityname,u.unitname
        FROM ".DB_PREFIX."storage_stock s
        LEFT JOIN ".DB_PREFIX."doc_stock_in_detail d ON(s.sysno=d.stock_sysno AND d.isdel='0')
        LEFT JOIN ".DB_PREFIX."doc_stock_in si ON si.sysno = d.stockin_sysno
        LEFT JOIN ".DB_PREFIX."base_goods goods ON(s.goods_sysno=goods.sysno)
        LEFT JOIN ".DB_PREFIX."base_goods_quality q ON(s.goods_quality_sysno=q.sysno)
        LEFT JOIN ".DB_PREFIX."base_storagetank st ON(s.storagetank_sysno=st.sysno)
        LEFT JOIN ".DB_PREFIX."base_goods_attribute ga ON (ga.goods_sysno = goods.sysno)
        LEFT JOIN ".DB_PREFIX."base_unit u ON (u.sysno = ga.unit_sysno)
        WHERE s.isdel='0' AND s.sysno in ($ids_str)";
        //echo $sql;exit;
        return $this->dbh->select($sql);
    }

    public function getStockinfoByid($id)
    {
        $sql = "select * from ".DB_PREFIX."storage_stock where isdel = 0 and status = 1 and iscurrent = 1 and sysno = " . intval($id);
        return $this->dbh->select_row($sql);
    }


    /**
     * 获取最近3次入库的所有罐号
     * @author zhaoshiyu
     */
    public function getStoragetankNoList($value = '')
    {
        $sql = "SELECT c.storagetank_sysno FROM
                (SELECT * FROM ".DB_PREFIX."storage_stock a WHERE 
                (SELECT count(*) FROM ".DB_PREFIX."storage_stock WHERE storagetank_sysno = a.storagetank_sysno AND UNIX_TIMESTAMP(updated_at)> UNIX_TIMESTAMP(a.updated_at) )<3 ORDER BY a.storagetank_sysno,updated_at DESC)  c WHERE c.ghosttype=1 GROUP BY c.storagetank_sysno";
        $list = $this->dbh->select($sql);

        return $list;
    }

    /**
     * 获取每个罐子最近3次入库记录
     * @author zhaoshiyu
     */
    public function getStockRecord()
    {
        // $sql = "SELECT c.storagetank_sysno,c.goods_sysno,c.updated_at FROM
        //         (SELECT * FROM ".DB_PREFIX."storage_stock a WHERE
        //         (SELECT count(*) FROM ".DB_PREFIX."storage_stock WHERE storagetank_sysno = a.storagetank_sysno AND UNIX_TIMESTAMP(updated_at)> UNIX_TIMESTAMP(a.updated_at) )<3 ORDER BY a.storagetank_sysno,updated_at DESC)  c WHERE c.ghosttype=1";
        $sql = "SELECT b.storagetank_sysno,bg.goodsname,b.updated_at from ".DB_PREFIX."base_goods bg right join (SELECT a.storagetank_sysno,a.goods_sysno,a.updated_at FROM (SELECT * FROM ".DB_PREFIX."storage_stock ss WHERE (SELECT count(*) FROM ".DB_PREFIX."storage_stock WHERE storagetank_sysno = ss.storagetank_sysno AND UNIX_TIMESTAMP(updated_at)> UNIX_TIMESTAMP(ss.updated_at) )<3 ORDER BY ss.storagetank_sysno,updated_at DESC)  a WHERE a.ghosttype=1) b on bg.sysno=b.goods_sysno";
        $stockData = $this->dbh->select($sql);
        $list = $this->getStoragetankNoList();
        foreach ($list as $key => $value) {
            foreach ($stockData as $k => $v) {
                if ($v['storagetank_sysno'] == $value['storagetank_sysno']) {
                    $list[$key]['stockData'][] = $v;
                }
            }
        }
        return $list;
    }

    /*
     * 根据库存单查询来源单号
     * @author wu xianneng
     */
    public function getStockinnoByStockId($id)
    {
        $sql = "select
			IF(ss.doctype =  3, st.sysno  ,si.sysno) as stockin_sysno ,
            IF(ss.doctype =  3, st.stocktransno  ,si.stockinno) as stockin_no
			from ".DB_PREFIX."storage_stock ss
			left join ".DB_PREFIX."doc_stock_trans_detail std on std.in_stock_sysno = ss.sysno
			left join ".DB_PREFIX."doc_stock_trans st on st.sysno = std.stocktrans_sysno
			left join ".DB_PREFIX."doc_stock_in_detail sid on sid.stock_sysno = ss.sysno
			left join ".DB_PREFIX."doc_stock_in si on si.sysno = sid.stockin_sysno
			WHERE ss.sysno = $id ";
        return $this->dbh->select($sql);
    }

    /*
     * 根据预约单获取储罐容量信息
     * @author zhaoshiyu
     * edit by hanshutan 2017-02-21 从储罐表读取可用容量
     */
    public function getStockInfoByStoragetankno($storagetank_sysno, $goods_sysno)
    {
        /*$where = "where a.sysno = ".$storagetank_sysno;
        if($type == 'car'){
            $sql = "SELECT a.sysno,a.storagetankname,(a.theoreticalcapacity-ifnull(b.stockqty,0))as stockqty
                    from ".DB_PREFIX."base_storagetank a left join (select storagetank_sysno,sum(stockqty) as stockqty
                    from ".DB_PREFIX."storage_stock where isclearstock=0 and iscurrent=1 and status=1 and isdel=0
                    group by storagetank_sysno ) b on a.sysno = b.storagetank_sysno $where" ;
        }elseif ($type == 'ship') {
                $sql = "SELECT a.sysno,a.storagetankname,(a.theoreticalcapacity-ifnull(b.stockqty,0))as stockqty
                from ".DB_PREFIX."base_storagetank a left join (select storagetank_sysno,sum(stockqty) as stockqty
                from ".DB_PREFIX."storage_stock where isclearstock=0 and iscurrent=1 and status=1 and isdel=0
                group by storagetank_sysno ) b on a.sysno = b.storagetank_sysno $where" ;
                // $stockqtyinfo = $this->dbh->select_row($sql);
        }
        return $this->dbh->select_row($sql);*/
        $where = " where a.sysno = " . $storagetank_sysno;
        $where .= " and a.goods_sysno = " . $goods_sysno;
        $sql = " select count(*) from ".DB_PREFIX."base_storagetank a left join
                (select storagetank_sysno,sum(stockqty) as stockqty
                from ".DB_PREFIX."storage_stock where isclearstock=0 and iscurrent=1 and status=1 and isdel=0
                group by storagetank_sysno ) b on a.sysno = b.storagetank_sysno $where ";
        $count = $this->dbh->select_row($sql);
        $sql = " select a.orderinqty,(a.actualcapacity-a.tank_stockqty-a.orderinqty)as ton ,a.sysno,a.storagetankname
                from ".DB_PREFIX."base_storagetank a left join
                (select storagetank_sysno,sum(stockqty) as stockqty
                from ".DB_PREFIX."storage_stock where isclearstock=0 and iscurrent=1 and status=1 and isdel=0
                group by storagetank_sysno ) b on a.sysno = b.storagetank_sysno $where ";
        $res = $this->dbh->select_row($sql);
        if (!$count) {
            $array = ['code' => 201, 'num' => 0, 'orderinqty' => $res['orderinqty'], 'msg' => '无法匹配商品和储罐!'];
        } else {
            if ($res) {
                $array = ['code' => 200, 'num' => $res['ton'], 'orderinqty' => $res['orderinqty'], 'msg' => 'success'];
            } else {
                $array = ['code' => 202, 'num' => $res['ton'], 'msg' => '数据库链接失败'];
            }
        }
        return $array;
    }


    /**
     * 公共控货方法调用
     * @param array ('customer_sysno'=>cid,'goods_sysno'=>gid,'contract_sysno'=>coid,'num'=>num)
     * 返回array array(1=>'欠费超信用期限',2=>'欠费超信用额度',3=>'欠费超控货比重') code含义：200正常返回 非200代表异常
     */
    public function controlgoods($params = array())
    {
        if (count($params) != 4 || in_array('', $params)) {
            $arr = array(
                'code' => 300,
                'message' => '数据异常',
            );
            return $arr;
        }
        $date = date("Y-m-d") . " 23:59:59";

        $where = " WHERE customer_sysno={$params['customer_sysno']}  AND contract_sysno = {$params['contract_sysno']} AND status=1 AND isdel = 0 AND coststatus!=5";

        $sql = "SELECT costdate from ".DB_PREFIX."doc_finance_cost_detail {$where}  ORDER BY costdate ASC";

        $day = $this->dbh->select_one($sql); //获取该用户首个费用日期

        $sql = "SELECT customercredit,customerterm,remaincost from ".DB_PREFIX."customer WHERE sysno = {$params['customer_sysno']}";

        $result = $this->dbh->select_row($sql); //获取改用户的信用额度和信用期限

        $sql = "SELECT sum(totalprice) from ".DB_PREFIX."doc_finance_cost_detail WHERE customer_sysno={$params['customer_sysno']} AND coststatus!=5 AND coststatus!=4 AND isdel=0 AND costdate<='$date'";
        // echo $sql;exit();
        $totalprice = $this->dbh->select_one($sql); //该用户产生的所有费用
        $totalprice = ($totalprice == 0) ? 0 : $totalprice;

        $sql = "SELECT (sum(unreceivablecost)-sum(receivablecost)) cost FROM ".DB_PREFIX."doc_finance_invoice WHERE customer_sysno={$params['customer_sysno']} AND invoicestatus<5 AND isdel=0";

        $cost = $this->dbh->select_one($sql);

        $totalprice += $cost;
        // var_dump($totalprice);exit();
        // return $totalprice;
        $customerday = COMMON::count_days(strtotime(date("Ymd")), strtotime($day)); //计算产生费用天数


        if ($customerday > ($result['customerterm'] * 30)) {
            $arrearsday = $customerday - ($result['customerterm'] * 30); //欠费超信用期限
            if (is_bool($day)) {
                $arrearsday = 0;
            }
        } else {
            $arrearsday = 0;
        }


        if (($totalprice - $result['remaincost']) > $result['customercredit']) {
            $arrearscost = ($totalprice - $result['remaincost']) - $result['customercredit']; //欠费超信用额度
        } else {
            $arrearscost = 0;
        }

        $sql = "SELECT goods_sysno from ".DB_PREFIX."storage_stock WHERE customer_sysno={$params['customer_sysno']}  AND iscurrent = 1 AND isdel=0";

        $goods = $this->dbh->select($sql); //获取用户所拥有的货品

        foreach ($goods as $k => $v) {

            $sql = "SELECT (sum(stockqty) - sum(if(clockqty>0,clockqty,0))) num from ".DB_PREFIX."storage_stock WHERE customer_sysno={$params['customer_sysno']} AND {$v['goods_sysno']}  AND iscurrent = 1 AND isdel=0";

            $available = $this->dbh->select_one($sql); //查询可用数量

            // return $available;
            $sql = "SELECT controlprice,controlproportion FROM ".DB_PREFIX."base_goods_attribute WHERE goods_sysno = {$v['goods_sysno']} AND `status`=1 AND isdel = 0";

            $data = $this->dbh->select_row($sql); // 查询改货品的控货单价和控货比重

            $kz += ($available - $params['num']) * $data['controlprice'] * $data['controlproportion']; //留存货值

        }

        $cost = $totalprice - $result['remaincost'];
        if ($cost > $kz) {
            $arrearsportion = $cost - $kz;
        } else {
            $arrearsportion = 0;
        }

        $arr = array(
            'code' => 200,
            'message' => array(1 => $arrearsday, 2 => $arrearscost, 3 => $arrearsportion),
        );
        return $arr;
    }

    //根据客户ID获取所有的库存记录
    public function getAllStock($customer_sysno)
    {
        $sql = "SELECT ss.sysno,ss.instockdate ,case when ss.doctype =1 then ss.shipname when ss.doctype =2 then '槽车入库' when ss.doctype =4 then '管输' end as inshipname ,@stocktype := 1 as stocktype,ss.doctype,ss.customer_sysno ,ss.customername ,ss.goods_sysno ,ss.goodsname ,ss.goods_quality_sysno ,ss.goodsqualityname,ss.instockqty ,@introduceqty := '--' as introduceqty,ss.outstockqty ,(ss.stockqty+ss.checkqty) as stockqty ,ss.clockqty ,ss.goodsnature,ss.firstfrom_sysno ,ss.firstfrom_no ,'吨' as unitname ,(ss.stockqty - (if(ss.clockqty>0,ss.clockqty,0)) + (if(ss.checkqty<0,ss.checkqty,0))) ableqty ,ss.storagetank_sysno,hbs.storagetankname,ifnull(si.release_num,0) as release_num,@introductiontype := 0 as introductiontype,ss.firstdate,(hbs.tank_stockqty-hbs.orderoutqty) as storagetankableqty from ".DB_PREFIX."storage_stock ss 
            LEFT JOIN ".DB_PREFIX."doc_stock_in si on si.sysno = ss.firstfrom_sysno
            left join ".DB_PREFIX."base_storagetank hbs on ss.storagetank_sysno = hbs.sysno 
            LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = ss.goods_sysno 
            where IF(si.stockintype=2,si.stockinstatus in(3,4),si.stockinstatus=4) and bga.isdel = 0 and ss.iscurrent = 1 and ss.status = 1 and ss.isdel = 0 and ss.customer_sysno = " . intval($customer_sysno) .
            " union 
            SELECT id.sysno,i.introductiondate as instockdate,id.shipname as inshipname,@stocktype := 2 as stocktype,@doctype := 99 as doctype,i.buy_customer_sysno as customer_sysno,i.buy_customername as customername,id.goods_sysno,id.goodsname,id.goods_quality_sysno ,id.goodsqualityname,@instockqty := '--' as instockqty,id.takegoodsnum as introduceqty,id.takegoodsqty as outstockqty,id.untakegoodsnum as stockqty,@clockqty := '--' as clockqty,id.goodsnature,id.sysno as firstfrom_sysno,i.introductionno as firstfrom_no,id.unitname,(id.untakegoodsnum-id.bookingqty) as ableqty,id.storagetank_sysno,id.storagetankname,@release_num := '' as release_num,i.introductiontype,id.firstdate,(hbs.tank_stockqty-hbs.orderoutqty) as storagetankableqty from `".DB_PREFIX."doc_introduction_detail` id 
            left join `".DB_PREFIX."doc_introduction` i on id.introduction_sysno = i.sysno 
            left join ".DB_PREFIX."base_storagetank hbs on id.storagetank_sysno = hbs.sysno 
            where i.isdel=0 and i.introductionstatus=4 and i.buy_customer_sysno=" . intval($customer_sysno);

        return $this->dbh->select($sql);
    }

    public function getAllStocks($customer_sysno)
    {
        $sql = "SELECT ss.sysno,ss.instockdate ,case when ss.doctype =1 then ss.shipname when ss.doctype =2 then '槽车入库' when ss.doctype =3 then ss.shipname when ss.doctype =4 then '管输' when ss.doctype =5 then ss.shipname end as shipname ,@stocktype := 1 as stocktype,ss.doctype,ss.customer_sysno ,ss.customername ,ss.goods_sysno ,ss.goodsname ,ss.goods_quality_sysno ,ss.goodsqualityname,ss.instockqty ,@introduceqty := '--' as introduceqty,ss.outstockqty ,(ss.stockqty+ss.checkqty) as stockqty ,ss.clockqty ,ss.goodsnature,ss.firstfrom_sysno ,ss.firstfrom_no ,'吨' as unitname ,(ss.stockqty - (if(ss.clockqty>0,ss.clockqty,0)) + (if(ss.checkqty<0,ss.checkqty,0))) ableqty ,ss.storagetank_sysno,hbs.storagetankname,ifnull(si.release_num,0) as release_num,@introductiontype := 0 as introductiontype,ss.firstdate from ".DB_PREFIX."storage_stock ss 
            LEFT JOIN ".DB_PREFIX."doc_stock_in si on si.sysno = ss.firstfrom_sysno
            left join ".DB_PREFIX."base_storagetank hbs on ss.storagetank_sysno = hbs.sysno 
            LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = ss.goods_sysno 
            where IF(si.stockintype=2,si.stockinstatus in(3,4),si.stockinstatus=4) and bga.isdel = 0 and ss.iscurrent = 1 and ss.status = 1 and ss.isdel = 0 and ss.customer_sysno = " . intval($customer_sysno);
        return $this->dbh->select($sql);
    }

    //根据客户ID获取所有的库存记录
    public function getAllStockForApi($customer_sysno, $goods_sysno, $goods_quality_sysno)
    {
        $filter = array();
        if ($customer_sysno && $customer_sysno != '') {
            $filter[] = " ss.customer_sysno = '$customer_sysno'";
        }

        if ($goods_sysno && $goods_sysno != '') {
            $filter[] = " ss.goods_sysno = '$goods_sysno' ";
        }
        if ($goods_quality_sysno && $goods_quality_sysno != '') {
            $filter[] = " ss.goods_quality_sysno = '$goods_quality_sysno' ";
        }
        $where = 'WHERE ss.stockqty > 0 AND bga.isdel = 0 AND ss.isclearstock = 0 AND ss.iscurrent = 1 AND ss.status = 1 AND ss.isdel = 0 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $sql = "SELECT ss.*,un.unitname ,(ss.stockqty-ss.checkqty-(if(ss.clockqty > 0,ss.clockqty,0))) ableqty FROM ".DB_PREFIX."storage_stock ss
            LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = ss.goods_sysno
            LEFT JOIN ".DB_PREFIX."base_unit un ON un.sysno = bga.unit_sysno {$where}";
        return $this->dbh->select($sql);

    }

    /*
     * 查询
     */
    public function searchstock($params)
    {
        $filter = array();
        if (isset($params['firstfrom_sysno']) && $params['firstfrom_sysno'] != '') {
            $filter[] = " `firstfrom_sysno`='{$params['firstfrom_sysno']}'";
        }

        if (isset($params['firstfrom_no']) && $params['firstfrom_no'] != '') {
            $filter[] = " `firstfrom_no` = '{$params['firstfrom_no']}' ";
        }
        $where = 'where isdel =0 ';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $sql = "SELECT *  FROM `".DB_PREFIX."storage_stock` $where";
        return $this->dbh->select($sql);
    }

    /*
     * 库存更新
     */

    public function updatestock($id, $data)
    {
        $where = ' isdel = 0 and status = 1 and iscurrent = 1 and sysno = ' . intval($id);
        $sql = "select * from `".DB_PREFIX."storage_stock` where {$where} order by `instockdate`";
        $stockinfo = $this->dbh->select_row($sql);

        $result = $this->dbh->update(DB_PREFIX.'storage_stock', $data, 'sysno=' . intval($id));
        if (!$result) {
            $msgcode = array(
                'code' => 201,
                'message' => '数据库操作失败',
            );
            return $msgcode;
        }

        #备份记录
        $stockinfo['ghostsysno'] = $stockinfo['sysno'];
        unset($stockinfo['sysno']);
        $stockinfo['updated_at'] = '=NOW()';
        $stockinfo['iscurrent'] = 0;
        $stockinfo['ghosttime'] = '=NOW()';
        $stockinfo['ghosttype'] = 12;
        $stockinfo['ghoststockqty'] = $stockinfo['stockqty'];
        $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
        if (!$res) {
            $msgcode = array(
                'code' => 201,
                'message' => '数据库操作失败',
            );
            return $msgcode;
        }

        $msgcode = array(
            'code' => 200,
            'message' => $id,
        );
        return $msgcode;
    }

    public function getCountStockList($search)
    {
        $filter = array();
        if (isset($search['customer_sysno']) && $search['customer_sysno'] != '' && $search['customer_sysno'] != 0 ) {
            $filter[] = " hss.`customer_sysno`='{$search['customer_sysno']}'";
        }
        if (isset($search['goods_sysno']) && $search['goods_sysno'] != '' && $search['goods_sysno'] != 0) {
            $filter[] = " hss.`goods_sysno` = '{$search['goods_sysno']}' ";
        }
        $where = ' hss.iscurrent=1 and hss.isdel= 0 and hss.`status` = 1';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $sql = 'SELECT hss.customer_sysno,hss.customername,hss.goodsname,hss.goods_sysno,SUM(stockqty) as num,hbg.goodsno FROM `'.DB_PREFIX.'storage_stock` hss
                LEFT JOIN '.DB_PREFIX.'base_goods hbg ON hss.goods_sysno = hbg.sysno
                WHERE '.$where.' GROUP BY hss.customer_sysno,hss.goods_sysno';
        return $this->dbh->select($sql);
    }

    public function getCustomerBygoodssyno($goods_sysno){
        $sql="SELECT customer_sysno,customername FROM `".DB_PREFIX."storage_stock` WHERE goods_sysno=".$goods_sysno." GROUP BY customer_sysno";
        return $this->dbh->select($sql);
    }
}
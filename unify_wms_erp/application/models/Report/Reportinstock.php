<?php
/**
 * @Author: wu xianneng
 */
class Report_ReportinstockModel {
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
     * @param   object  $dbh
     * @return  void
     */
    public function __construct($dbh, $mch = null) {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    /**
     * 获取入库单数据
     */
    public function getList($params){
        $result = $params;
        $filter = array();
        $carfilter = array();
        if (isset($params['date1']) && $params['date1'] != '') {
            $filter[] = " si.`stockindate`>='{$params['date1']}'";
            $carfilter[] = " si.`created_at`>='{$params['date1']}'";
        }
        if (isset($params['date2']) && $params['date2'] != '') {
            $filter[] = " si.`stockindate`<='{$params['date2']}'";
            $carfilter[] = " si.`created_at`<='{$params['date2']}'";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " si.`customer_sysno`={$params['customer_sysno']}";
            $carfilter[] = " si.`customer_sysno`={$params['customer_sysno']}";
        }
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " sid.`goods_sysno`={$params['goods_sysno']}";
        }
        if (isset($params['goodsname']) && $params['goodsname'] != '') {
            $carfilter[] = " pi.`goodsname`='{$params['goodsname']}'";
        }
        if (isset($params['stockinno']) && $params['stockinno'] != '') {
            $filter[] = " si.`stockinno`='{$params['stockinno']}'";
            $carfilter[] = " si.`stockinno`='{$params['stockinno']}'";
        }

        $where = '';
        $carwhere = '';
        if (1 <= count($filter)) {
            $where .= " AND " . implode(' AND ', $filter);
            $carwhere .= " AND " . implode(' AND ', $carfilter);
        }
        $order = "ORDER BY si.stockindate ASC";
        $result['list'] = array();
        //船入库、管入库
        $sql = "SELECT si.sysno,si.stockinno,si.stockintype,si.stockindate,sid.goodsname,si.customername,sid.shipname,si.takegoodsnum,sum(sid.beqty) AS beqty
                FROM ".DB_PREFIX."doc_stock_in si
                LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sid ON sid.stockin_sysno = si.sysno
                WHERE si.stockintype IN (1,3) AND si.isdel = 0 AND si.stockinstatus = 4 $where
                GROUP BY si.sysno  $order ";
        $shipstock['list'] = $this->dbh->select($sql);
        if(!empty($shipstock['list'])){
            foreach($shipstock['list'] as $key=>$item){
                $sql = "SELECT bs.storagetankname FROM ".DB_PREFIX."doc_stock_in_detail sid
                        LEFT JOIN ".DB_PREFIX."base_storagetank bs ON bs.sysno = sid.storagetank_sysno
                        WHERE sid.stockin_sysno = {$item['sysno']}";
                $storagetanknames = $this->dbh->select($sql);
                foreach($storagetanknames as $value){
                    $storagetanknamestr[$key][] = $value['storagetankname'];
                }
                if($storagetanknamestr[$key])
                $shipstock['list'][$key]['storagetankname'] = implode(',',$storagetanknamestr[$key]);

            }
        }

        //车入库
        $sql = "SELECT si.sysno,si.stockinno,si.stockintype,si.stockindate,pi.goodsname,si.customername,'槽车进货' AS shipname,si.takegoodsnum,SUM(pi.beqty)/1000 as beqty
            FROM ".DB_PREFIX."doc_pounds_in pi
            LEFT JOIN ".DB_PREFIX."doc_stock_in si ON pi.stockin_sysno = si.sysno
            WHERE si.stockintype = 2 AND si.isdel = 0 AND pi.poundsinstatus = 4 $carwhere
            GROUP BY pi.stockinno,pi.storagetank_sysno  $order ";
        $carstockin['list'] = $this->dbh->select($sql);
        if(!empty($carstockin['list'])){
            foreach($carstockin['list'] as $key=>$item){
                $sql = "SELECT bs.storagetankname FROM ".DB_PREFIX."storage_stock ss
                        LEFT JOIN ".DB_PREFIX."base_storagetank bs ON bs.sysno = ss.storagetank_sysno
                        WHERE ss.iscurrent = 1 AND ss.firstfrom_sysno = {$item['sysno']}";
                $storagetanknames2 = $this->dbh->select($sql);
                foreach($storagetanknames2 as $value){
                    $storagetanknamestr2[$key][] = $value['storagetankname'];
                }
                $carstockin['list'][$key]['storagetankname'] = implode(',',$storagetanknamestr2[$key]);

            }
        }

        $result['list'] = $shipstock['list'];
        if(!empty($carstockin['list'])){
            $result['list'] =  array_merge_recursive($result['list'] , $carstockin['list']);
        }
        if(!empty($pipestockin['list'])){
            $result['list'] = array_merge_recursive($result['list'] , $pipestockin['list']);
        }

        if(isset($params['page'])&&$params['page']==false){
            $data = $result;
        }else{
            $data['totalRow'] = count($result['list']);
            $data['totalPage'] = ceil($data['totalRow'] / $params['pageSize']);
            $list=array_chunk($result['list'],$params['pageSize'],false);
            $data['list']= $list [$params['pageCurrent']-1];
        }
        if(empty($data['list'])){
            $data['list'] = [];
        }
        return $data;
    }

    /*
     * 获取入库单基本信息
     */
    public function getStockindetail($id){
        $sql = "SELECT si.stockintype,si.customername,si.stockindate,si.takegoodsnum,sid.shipname,sid.goodsname
                FROM ".DB_PREFIX."doc_stock_in si
                LEFT JOIN ".DB_PREFIX."doc_stock_in_detail sid ON si.sysno = sid.stockin_sysno
                LEFT JOIN ".DB_PREFIX."storage_stock ss ON sid.stock_sysno = ss.sysno
                WHERE si.sysno = $id ";
        $result = $this->dbh->select_row($sql);
        //储罐拼接
        $sql = "SELECT DISTINCT bs.storagetankname FROM ".DB_PREFIX."storage_stock ss
                    LEFT JOIN ".DB_PREFIX."base_storagetank bs ON bs.sysno = ss.storagetank_sysno
                    WHERE ss.iscurrent = 1 AND ss.firstfrom_sysno = $id";
        $storagetanknames = $this->dbh->select($sql);
        foreach($storagetanknames as $value){
            $storagetanknamestr[] = $value['storagetankname'];
        }
        $result['storagetankname'] = implode(',',$storagetanknamestr);
        //入库量、损耗量、客户结存量
        $sql = "SELECT doctype,instockqty,(ullage + clearqty) AS ullage,stockqty FROM ".DB_PREFIX."storage_stock
                    WHERE iscurrent = 1 AND firstfrom_sysno = $id";
        $data = $this->dbh->select($sql);
        $instockqty = 0;
        $ullage = 0;
        $stockqty = 0;
        foreach($data as $item){
            if($item['doctype'] == 1|| $item['doctype'] == 2 || $item['doctype'] == 4 || $item['doctype'] == 5){
                $instockqty += $item['instockqty'];
            }
            if($item['doctype'] != 3){
                $ullage += $item['ullage'];
                $stockqty += $item['stockqty'];
            }
        }
        $result['instockqty'] = $instockqty;
        $result['ullage'] = $ullage;

        //结存量=客户结存量+第一层可撤销剩余量+n层可撤销剩余量
        //第一层可撤销剩余量
        //查询货主该批次下的库存
        $sql = "SELECT stock_sysno FROM ".DB_PREFIX."doc_stock_in_detail WHERE stockin_sysno = $id";
        $stocks = $this->dbh->select($sql);
        $onebeqty = 0;
        foreach($stocks as $stock){
            if(empty($stock['stock_sysno']))
            {
                continue;
            }
            $sql = "SELECT SUM(did.untakegoodsnum) FROM ".DB_PREFIX."doc_introduction_detail did
                LEFT JOIN ".DB_PREFIX."doc_introduction di ON di.sysno = did.introduction_sysno
                WHERE di.introductiontype = 1 AND did.stocktype = 1 AND did.stock_sysno = {$stock['stock_sysno']}";
            $onebeqty += $this->dbh->select_one($sql);
        }

        //n层可撤销剩余量
        //该入库单下所有库存
        $sql = "SELECT stock_sysno FROM ".DB_PREFIX."doc_stock_in_detail
                WHERE stockin_sysno = $id ";
        $stocks = $this->dbh->select($sql);

        $nbeqty = 0;
        foreach($stocks as $item){
            if(empty($item['stock_sysno']))
            {
                continue;
            }
            //可撤销提单明细
            $sql = "SELECT did.sysno FROM ".DB_PREFIX."doc_introduction_detail did
                    LEFT JOIN ".DB_PREFIX."doc_introduction di ON di.sysno = did.introduction_sysno
                    WHERE di.introductiontype = 1 AND did.stocktype = 1 AND did.stock_sysno = {$item['stock_sysno']}";
            $intrdetails = $this->dbh->select($sql);
            $nbeqty1  = 0;
            if(!empty($intrdetails))
            foreach($intrdetails as $intrdetail){
                $data = $this->getIntroduceDetailTree($intrdetail['sysno']);
                $nbeqty2 = 0;
                if(!empty($data))
                foreach($data as $value){
                    $nbeqty2 += $value['untakegoodsnum'];
                }
                $nbeqty1 += $nbeqty2;
            }
            $nbeqty += $nbeqty1;
        }
        $result['stockqty'] = $stockqty + $onebeqty + $nbeqty;

        return $result;
    }

    //获取明细上下级
    public function getIntroduceDetailTree($introductiondetail_sysno)
    {
        $sql = "SELECT did.sysno,did.introductiondetail_sysno,did.untakegoodsnum
                FROM ".DB_PREFIX."doc_introduction_detail did
                LEFT JOIN ".DB_PREFIX."doc_introduction di ON di.sysno = did.introduction_sysno
                WHERE di.introductiontype = 1 AND did.stocktype = 2";
        $data = $this->dbh->select($sql);
        return $this->_getIntroduceDetailTree($data,$introductiondetail_sysno);
    }

    private function _getIntroduceDetailTree($data, $introductiondetail_sysno)
    {
        static $list = array();
        foreach ($data as $k => $v) {
            if ($v['sysno'] == $introductiondetail_sysno) {
                $list[] = $v;
                $this->_getIntroduceDetailTree($data,$v['introductiondetail_sysno']);
            }
        }
        return $list;
    }

    /*
     * 获取货转信息
     */
    public function getStocktrans($id){
        ////结存量=客户结存量+第一层可撤销剩余量+n层可撤销剩余量
        $sql = "SELECT st.stocktransno,st.stocktransdate,st.sale_customername,st.buy_customername,std.transqty,ss.sysno,(ss.ullage + ss.clearqty) AS ullage,ss.stockqty,bs.storagetankname
                FROM ".DB_PREFIX."doc_stock_trans st
                LEFT JOIN ".DB_PREFIX."doc_stock_trans_detail std ON std.stocktrans_sysno = st.sysno
                LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = std.in_stock_sysno
                LEFT JOIN ".DB_PREFIX."base_storagetank bs ON bs.sysno = ss.storagetank_sysno
                WHERE ss.firstfrom_sysno = $id AND ss.doctype = 3 AND ss.iscurrent = 1 ";
        $stocks = $this->dbh->select($sql);

        //结存量=货转结存量+第一层可撤销剩余量+n层可撤销剩余量
        //第一层可撤销剩余量
        foreach($stocks as $k =>$stock){
            $sql = "SELECT SUM(did.untakegoodsnum) FROM ".DB_PREFIX."doc_introduction_detail did
                LEFT JOIN ".DB_PREFIX."doc_introduction di ON di.sysno = did.introduction_sysno
                WHERE di.introductiontype = 1 AND did.stocktype = 1 AND did.stock_sysno = {$stock['sysno']}";
            $beqty = $this->dbh->select_one($sql);
            $stocks[$k]['stockqty'] += $beqty;
        }

        foreach($stocks as $key => $item){
            //可撤销提单明细
            $sql = "SELECT did.sysno FROM ".DB_PREFIX."doc_introduction_detail did
                    LEFT JOIN ".DB_PREFIX."doc_introduction di ON di.sysno = did.introduction_sysno
                    WHERE di.introductiontype = 1 AND did.stocktype = 1 AND did.stock_sysno = {$item['sysno']}";
            $intrdetails = $this->dbh->select($sql);
            $nbeqty1  = 0;

            if(!empty($intrdetails))
                foreach($intrdetails as $intrdetail){
                    $data = $this->getIntroduceDetailTree($intrdetail['sysno']);
                    $nbeqty2 = 0;
                    if(!empty($data))
                        foreach($data as $value){
                            $nbeqty2 += $value['untakegoodsnum'];
                        }
                    $nbeqty1 += $nbeqty2;
                }
            $stocks[$key]['stockqty'] += $nbeqty1;
        }

        return $stocks;

    }

    /*
     * 获取货转、出库详细信息
     */
    public function getstockoutdetail($id){
        //查询船、管库存出库量
        $sql = "SELECT so.stockoutdate AS date,so.stockouttype AS doctype,so.customername,so.stockoutno AS poundsoutno,sod.shipname,so.takegoodsno,sod.beqty
            FROM ".DB_PREFIX."doc_stock_out_detail sod
            LEFT JOIN ".DB_PREFIX."doc_stock_out so ON sod.stockout_sysno = so.sysno
            WHERE so.isdel = 0 AND so.stockouttype IN (1,3) AND sod.stocktype = 1 AND so.stockoutstatus = 4 AND sod.stockin_sysno = $id";
        $stockoutshippip['list'] = $this->dbh->select($sql);

        //查询船、管提单出库量
        $sql = "SELECT so.stockoutdate AS date,so.stockouttype AS doctype,so.customername,so.stockoutno AS poundsoutno,sod.shipname,so.takegoodsno,sod.beqty
            FROM ".DB_PREFIX."doc_stock_out_detail sod
            LEFT JOIN ".DB_PREFIX."doc_stock_out so ON sod.stockout_sysno = so.sysno
            LEFT JOIN ".DB_PREFIX."doc_introduction_detail did ON did.sysno = sod.stockin_sysno
            WHERE so.isdel = 0 AND so.stockouttype IN (1,3) AND sod.stocktype = 2 AND so.stockoutstatus = 4 AND did.stockin_sysno = $id";
        $stockoutshippip2['list'] = $this->dbh->select($sql);

        //查询车库存出库量
        $sql = "SELECT DISTINCT date(po.fullcartime) as date,2 AS doctype,pod.customername,po.poundsoutno,po.carid AS shipname,pod.takegoodsno,pod.realnumber/1000 beqty
                FROM ".DB_PREFIX."doc_pounds_out_detail pod
                LEFT JOIN ".DB_PREFIX."doc_pounds_out po ON po.sysno = pod.pounds_out_sysno
                LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sod ON pod.stockoutdetail_sysno = sod.sysno
                LEFT JOIN ".DB_PREFIX."doc_stock_out so ON so.sysno = sod.stockout_sysno
                WHERE so.isdel = 0 AND sod.stocktype = 1 AND po.poundsoutstatus = 4 AND  sod.stockin_sysno = $id";
        $stockoutcar['list'] = $this->dbh->select($sql);

        //查询车提单出库量
        $sql = "SELECT DISTINCT date(po.fullcartime) as date,2 AS doctype,pod.customername,po.poundsoutno,po.carid AS shipname,pod.takegoodsno,pod.realnumber/1000 beqty
                FROM ".DB_PREFIX."doc_pounds_out_detail pod
                LEFT JOIN ".DB_PREFIX."doc_pounds_out po ON po.sysno = pod.pounds_out_sysno
                LEFT JOIN ".DB_PREFIX."doc_stock_out_detail sod ON pod.stockoutdetail_sysno = sod.sysno
                LEFT JOIN ".DB_PREFIX."doc_stock_out so ON so.sysno = sod.stockout_sysno
                LEFT JOIN ".DB_PREFIX."doc_introduction_detail did ON did.sysno = sod.stockin_sysno
                WHERE so.isdel = 0 AND sod.stocktype = 2 AND po.poundsoutstatus = 4 AND  did.stockin_sysno = $id";
        $stockoutcar2['list'] = $this->dbh->select($sql);

        $stockout['list'] = array_merge_recursive($stockoutshippip['list'],$stockoutcar['list']);
        $stockout['list'] = array_merge_recursive($stockout['list'],$stockoutshippip2['list']);
        $stockout['list'] = array_merge_recursive($stockout['list'],$stockoutcar2['list']);
        $stockout['list'] = $this->list_sort_by($stockout['list'], 'date', 'asc');

        return $stockout;
    }

    /*
     * 二维数组排序
     */
    public function list_sort_by($list, $field, $sortby = 'asc')
    {
        if (is_array($list))
        {
            $refer = $resultSet = array();
            foreach ($list as $i => $data)
            {
                $refer[$i] = &$data[$field];
            }
            switch ($sortby)
            {
                case 'asc': // 正向排序
                    asort($refer);
                    break;
                case 'desc': // 逆向排序
                    arsort($refer);
                    break;
                case 'nat': // 自然排序
                    natcasesort($refer);
                    break;
            }
            foreach ($refer as $key => $val)
            {
                $resultSet[] = &$list[$key];
            }
            return $resultSet;
        }
        return false;
    }

}
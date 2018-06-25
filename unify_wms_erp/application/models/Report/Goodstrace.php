<?php

class Report_GoodstraceModel
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

    public function getList($params)
    {
        $filter = array();
        if (isset($params['startTime']) && $params['startTime'] != '') {
            $filter[] = " dsi.`stockindate` >= '{$params['startTime']}'";
        }
        if (isset($params['endTime']) && $params['endTime'] != '') {
            $filter[] = " dsi.`stockindate` <= '{$params['endTime']}'";
        }

        if (isset($params['customer_sysno']) && $params['customer_sysno'] != '') {
            $filter[] = " dsi.`customer_sysno` = '{$params['customer_sysno']}'";
        }
        if (isset($params['stockinno']) && $params['stockinno'] != '') {
            $filter[] = " dsi.`stockinno` = '{$params['stockinno']}'";
        }
        if (isset($params['goods_sysno']) && $params['goods_sysno'] != '') {
            $filter[] = " dsid.`goods_sysno` = '{$params['goods_sysno']}'";
        }
        if (isset($params['shipname']) && $params['shipname'] != '') {
            $filter[] = " dsid.`shipname` = '{$params['shipname']}'";
        }

        $where = 'where dsi.isdel = 0 AND dsi.`status` = 1 AND dsi.stockinstatus = 4 ';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT dsi.sysno FROM `".DB_PREFIX."doc_stock_in` dsi LEFT JOIN ".DB_PREFIX."doc_stock_in_detail dsid ON dsid.stockin_sysno = dsi.sysno {$where} group by dsi.sysno";
        $result = $params;
        $res = $this->dbh->select($sql);
        $result['totalRow'] = count($res);
        $result['list'] = array();
        if ($result['totalRow'])
        {
            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "SELECT dsi.sysno,dsi.stockinno,dsi.stockintype,dsi.stockindate,dsi.customername,dsi.takegoodsnum,dsid.goods_sysno,dsid.shipname,dsid.goodsname,sum(beqty) as beqty FROM `".DB_PREFIX."doc_stock_in` dsi LEFT JOIN ".DB_PREFIX."doc_stock_in_detail dsid ON dsid.stockin_sysno = dsi.sysno {$where} group by dsi.sysno";
                if($params['orders'] != ''){
                    $sql .= " order by ".$params['orders'];
                }
                $arr = 	$this->dbh->select($sql);
                $result['list'] = $arr;
            }else{
                $result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);
                $this->dbh->set_page_num($params['pageCurrent'] );
                $this->dbh->set_page_rows($params['pageSize'] );
                $sql = "SELECT dsi.sysno,dsi.stockinno,dsi.stockintype,dsi.stockindate,dsi.customername,dsi.takegoodsnum,dsid.goods_sysno,dsid.shipname,dsid.goodsname,sum(beqty) as beqty FROM `".DB_PREFIX."doc_stock_in` dsi LEFT JOIN ".DB_PREFIX."doc_stock_in_detail dsid ON dsid.stockin_sysno = dsi.sysno {$where} group by dsi.sysno";
                if($params['orders'] != '') {
                    $sql .= " order by " . $params['orders'];
                }
                $arr = 	$this->dbh->select_page($sql);
                $result['list'] = $arr;
            }
        }
        return $result;
    }

    /**
     * 根据主表ID获取详情
     * getDetail
     * @author dxb
     * @param $sysno
     * @return array
     */
    public function getDetail($sysno){
        $sql = "SELECT dsi.sysno,dsi.stockinno,dsi.stockintype,dsi.stockindate,ss.customername,dsi.takegoodsnum,ss.goods_sysno,ss.shipname,ss.goodsname, ss.instockqty,ss.ullage,ss.stockqty,bs.storagetankname FROM ".DB_PREFIX."doc_stock_in dsi LEFT JOIN `".DB_PREFIX."storage_stock` ss ON ss.firstfrom_sysno = dsi.sysno LEFT JOIN ".DB_PREFIX."base_storagetank bs ON ss.storagetank_sysno = bs.sysno WHERE ss.iscurrent = 1 AND dsi.sysno = ".intval($sysno);
        $res = $this->dbh -> select($sql);
        return $res ? $res : [];
    }

    /**
     * 获取出入库记录
     * getOutDetail
     * @author dxb
     * @param $sysno
     * @return array
     */
    public function getOutDetail($sysno){
        $sql = "SELECT dgrl.sysno,dgrl.docno,dgrl.doc_type,dgrl.customername,dgrl.created_at,if(dgrl.takegoodsno, dgrl.takegoodsno, '--') takegoodsno,dgrl.shipname,dgrl.carid,dgrl.beqty,dgrl.ullage,dgrl.stock_sysno,dgrl.stockin_sysno,dgrl.father_stock_sysno FROM `".DB_PREFIX."doc_goods_record_log` dgrl WHERE dgrl.stockin_sysno = ".intval($sysno);
        $res = $this->dbh -> select($sql);
        return $res ? $res : [];
    }


    public function getInstockqty($sysno)
    {
        $sql = "SELECT sum(stockqty) stockqty FROM ".DB_PREFIX."storage_stock WHERE iscurrent = 1 and `status` = 1 and firstfrom_sysno = ".intval($sysno) ;
        return $this-> dbh -> select_one($sql) ? $this-> dbh -> select_one($sql) : 0;
    }

    public function getTranstockqty()
    {
        return 0;
    }
}
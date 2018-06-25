<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/15 0015
 * Time: 10:40
 */
class ReportstockModel
{
    /**
     * 数据库类实例
     *
     * @var object
     */
    public $dbh = null;

    public $mch = null;

    /**
     * ReportstockModel constructor.
     * @param $dbh
     * @param null $mch
     */
    public function __construct($dbh, $mch = null)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }

    /**
     * @param $params
     * @return mixed
     */
    public function getlist($params)
    {
        $filter = array();
        if ( isset($params['start_time']) && $params['start_time'] != '' ) {
            $filter[] = "hdsi.stockindate >= '{$params['start_time']}'";
        }
        if( isset($params['end_time']) && $params['end_time'] != '' ){
            $filter[] = "hdsi.stockindate <= '{$params['end_time']}'";
        }
        if (isset($params['customer_sysno']) && $params['customer_sysno'] != 0) {
            $filter[] = "hss.customer_sysno = {$params['customer_sysno']} ";
        }
        if (isset($params['clearstockstatus']) && $params['clearstockstatus'] != 0) {
            $filter[] = "hss.clearstockstatus = {$params['clearstockstatus']} ";
        }
        if (isset($params['stockinno']) && $params['stockinno'] != '') {
            $filter[] = "hdsi.stockinno like '%{$params['stockinno']}%'";
        }

        $where =' hdsi.status = 1 AND hdsi.isdel=0 AND hdsi.stockinstatus = 4 AND hss.iscurrent = 1';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }
        $sql = "SELECT count(*) FROM `".DB_PREFIX."doc_stock_in` hdsi
                LEFT JOIN `".DB_PREFIX."storage_stock` hss ON hdsi.sysno = hss.firstfrom_sysno  AND hss.doctype in (1,2)
                LEFT JOIN `".DB_PREFIX."base_storagetank` hbs ON hbs.sysno = hss.storagetank_sysno
                WHERE {$where} GROUP BY hdsi.sysno";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if( isset($params['page'] ) && $params['page'] == false){
                $sql = "SELECT hss.*,hbs.storagetankname,IFNULL(hdsid.unitname, '吨') unitname
                        FROM `".DB_PREFIX."doc_stock_in` hdsi
                        LEFT JOIN `".DB_PREFIX."storage_stock` hss ON hdsi.sysno = hss.firstfrom_sysno  AND hss.doctype in (1,2)
                        LEFT JOIN `".DB_PREFIX."base_storagetank` hbs ON hbs.sysno = hss.storagetank_sysno
                        LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` hdsid ON  hdsid.stockin_sysno = hdsi.sysno
                        WHERE {$where} GROUP BY hdsi.sysno";
                if($params['orders'] != '') {
                    $sql .= " order by hss.changetime,".$params['orders'];
                }else{
                    $sql .= " order by hss.changetime desc";
                }
                $result['list'] = $this->dbh->select($sql);
            }else{
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);
                $sql = "SELECT hss.*,hbs.storagetankname,IFNULL(hdsid.unitname, '吨') unitname
                        FROM `".DB_PREFIX."doc_stock_in` hdsi
                        LEFT JOIN `".DB_PREFIX."storage_stock` hss ON hdsi.sysno = hss.firstfrom_sysno AND hss.doctype in (1,2)
                        LEFT JOIN `".DB_PREFIX."base_storagetank` hbs ON hbs.sysno = hss.storagetank_sysno
                        LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` hdsid ON  hdsid.stockin_sysno = hdsi.sysno
                        WHERE {$where} GROUP BY hdsi.sysno";
                if ($params['orders'] != '') {
                    $sql .= " order by hss.changetime" . $params['orders'];
                }else{
                    $sql .= " order by hss.changetime DESC";
                }
//                echo "<pre>";print_r($sql);die;
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

    /**
     * 获取清库记录信息
     * @param $sysnoin_sysno 出库订单ID
     * @return mixed
     */
    public function getClearStockDetail($sysnoin_sysno){
        $sql = "SELECT hdsc.*,hdsi.stockindate,dscd.instockqty,dscd.outstockqty,dscd.okqty,hdsid.goodsname,IFNULL(hdsid.unitname, '吨') unitname,(dscd.okqty/dscd.instockqty)*1000 as num,
                ifnull((SELECT hdso.stockoutdate FROM `".DB_PREFIX."doc_stock_out` hdso
                LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` dsod ON hdso.sysno = dsod.stockout_sysno
                WHERE dsod.stockin_sysno = dscd.stockin_sysno
                ORDER BY  hdso.stockoutdate DESC limit 1) ,'--') as stockoutdate
                FROM `".DB_PREFIX."doc_stock_clear` hdsc
                LEFT JOIN `".DB_PREFIX."doc_stock_clear_detail` dscd ON  hdsc.sysno = dscd.stockclear_sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_in_detail` hdsid ON  dscd.stockin_sysno = hdsid.stockin_sysno
                LEFT JOIN `".DB_PREFIX."doc_stock_in` hdsi ON  dscd.stockin_sysno = hdsi.sysno
                WHERE hdsc.status = 1 AND hdsc.isdel = 0 AND dscd.stockin_sysno = '".intval($sysnoin_sysno)."' GROUP BY hdsc.sysno" ;
        return $this->dbh->select($sql);
    }

    /**
     * 获取出库记录信息
     * @param $sysnoin_sysno 出库订单ID
     * @return mixed
     */
    public function getOutStockDetail($sysnoin_sysno){
        $sql = "SELECT hdso.*,sum(dsod.beqty) beqty, dsod.shipname,dsod.unitname,dsod.goodsname,dsod.goodsnature,dsod.shipcheckqty
                FROM `".DB_PREFIX."doc_stock_out` hdso
                LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` dsod  ON hdso.sysno = dsod.stockout_sysno
                WHERE hdso.isdel = 0 AND dsod.stockin_sysno = '".intval($sysnoin_sysno)."' GROUP BY dsod.stockout_sysno" ;
        $result = $this->dbh->select($sql);

        //车出库数量得要从榜码单求和  船出库直接取数量
        foreach($result as $key => $value){
            if($value['stockouttype'] == 2){
                $count_sql = "SELECT SUM(beqty) FROM `".DB_PREFIX."doc_pounds_out` WHERE poundsoutstatus = 4 AND stockout_sysno = '".intval($value['sysno'])."'" ;
                $num = $this->dbh->select_one($count_sql);
                $result[$key]['countnum'] = floatval($num) ? floatval($num)/1000 : 0;
            }else{
                $result[$key]['countnum'] = floatval($value['beqty']) ? $value['beqty'] : $value['shipcheckqty'];
            }
        }
        return $result;
    }

    /**
     * 获取货权转移记录信息
     * @param $sysno 库存ID
     * @return mixed
     */
    public function getChangeStockDetail($sysno){
        $sql = "SELECT hdst.*,SUM(dstd.transqty) tobetransqty,hss.goodsname,hss.goodsnature,hss.firstfrom_sysno FROM `".DB_PREFIX."doc_stock_trans` hdst
                LEFT JOIN `".DB_PREFIX."doc_stock_trans_detail` dstd ON hdst.sysno = dstd.stocktrans_sysno
                LEFT JOIN `".DB_PREFIX."storage_stock` hss ON  hss.sysno = dstd.out_stock_sysno
                WHERE  hdst.status = 1 AND hdst.isdel = 0 AND dstd.out_stock_sysno = '".intval($sysno)."' GROUP BY dstd.sysno";
        $result =  $this->dbh->select($sql);
        $result = $result ? $result : [];
        foreach($result as $key => $value){
            $sql = "SELECT stockin_sysno, IFNULL(unitname, '吨') unitname FROM ".DB_PREFIX."doc_stock_in_detail WHERE stockin_sysno = {$value['firstfrom_sysno']}";
            $res = $this->dbh->select_row($sql);
            $result[$key] = array_merge($value, $res);
        }
        return $result;
    }

}
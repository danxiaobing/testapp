<?php
/**
 * @Author: wu xianneng
 * @Date:   2016-12-17 14:38:52
 * @Last Modified by:   wu xianneng
 * @Last Modified time: 2016-12-17 17:13:54
 */
class ReportformModel {
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
	 * 获取储罐收发存汇总表数据
	 * @return 数组、
	 * @author Dujiangjiang
	 */
	public function getTankList($params){
		$result = $params;
		$filter = array();
		if (isset($params['date1']) && $params['date1'] != '') {
            $filter[] = " ss.`instockdate`>='{$params['date1']}'";
        }
        if (isset($params['date2']) && $params['date2'] != '') {
           $filter[] = " ss.`instockdate`<='{$params['date2']}'";
        }
        if (isset($params['tankno']) && $params['tankno'] != '' && $params['tankno'] != '0') {
            $filter[] = " ss.`storagetank_sysno`={$params['tankno']}";
        }
        
        $where ='ss.iscurrent = 1 AND ss.`isdel`=0 ';
        if (count($filter)>0) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

		$sql = "SELECT count(ss.`sysno`) FROM `".DB_PREFIX."storage_stock` AS ss 
				LEFT JOIN `".DB_PREFIX."base_storagetank` AS bs ON bs.`sysno`=ss.`storagetank_sysno`
    			LEFT JOIN `".DB_PREFIX."base_goods_attribute` AS bga ON bga.`goods_sysno`=ss.`goods_sysno`
    			LEFT JOIN `".DB_PREFIX."base_unit` AS bu ON bu.`sysno`=bga.`unit_sysno`
				WHERE {$where}";
        $result['totalRow'] = $this->dbh->select_one($sql);
        
        $result['list'] = array();
        if(count($result['totalRow'])>0){
        	$result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
            $this->dbh->set_page_num($params['pageCurrent']);
            $this->dbh->set_page_rows($params['pageSize']);

        	$sql = "SELECT ss.*,bs.`storagetankname`,bs.`storagetanknature`,bu.`unitname`
        			FROM `".DB_PREFIX."storage_stock` AS ss 
        			LEFT JOIN `".DB_PREFIX."base_storagetank` AS bs ON bs.`sysno`=ss.`storagetank_sysno`
        			LEFT JOIN `".DB_PREFIX."base_goods_attribute` AS bga ON bga.`goods_sysno`=ss.`goods_sysno`
        			LEFT JOIN `".DB_PREFIX."base_unit` AS bu ON bu.`sysno`=bga.`unit_sysno`
        			WHERE {$where}";
        	$result['list'] = $this->dbh->select_page($sql);
        }
       	return $result;
	}

	/*
	 * 获取储罐收发存汇总表数据
	 * @author wu xianneng
	 */
	public function getTankList2($params){
		$filter = array();
		if (isset($params['date1']) && $params['date1'] != '') {
			$filter[] = " ss.`instockdate`>='{$params['date1']}'";
		}
		if (isset($params['date2']) && $params['date2'] != '') {
			$filter[] = " ss.`instockdate`<='{$params['date2']}'";
		}
		if (isset($params['tankno']) && $params['tankno'] != '' ) {
			$filter[] = " ss.`storagetank_sysno`={$params['tankno']}";
		}

		$where ='ss.iscurrent = 1 AND ss.`isdel`=0 ';
		if (count($filter)>0) {
			$where .= ' AND ' . implode(' AND ', $filter);
		}

		$sql = "SELECT count(DISTINCT ss.`storagetank_sysno`) FROM `".DB_PREFIX."storage_stock` AS ss
				LEFT JOIN `".DB_PREFIX."base_storagetank` AS bs ON bs.`sysno`=ss.`storagetank_sysno`
    			LEFT JOIN `".DB_PREFIX."base_goods_attribute` AS bga ON bga.`goods_sysno`=ss.`goods_sysno`
    			LEFT JOIN `".DB_PREFIX."base_unit` AS bu ON bu.`sysno`=bga.`unit_sysno`
				WHERE {$where}";
		$result['totalRow'] = $this->dbh->select_one($sql);

		$result['list'] = array();
		if(count($result['totalRow'])>0){
			$result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
			$this->dbh->set_page_num($params['pageCurrent']);
			$this->dbh->set_page_rows($params['pageSize']);

			$sql = "SELECT ss.*,bs.`storagetankname`,bs.`storagetanknature`,bu.`unitname`,
					FORMAT(SUM(ss.stockqty)-SUM(ss.instockqty)-ifnull(SUM(srd2.stockretankqty)/COUNT(ss.storagetank_sysno),0)+SUM(ss.outstockqty)+SUM(ss.checkqty)+ifnull(SUM(srd.stockretankqty)/COUNT(ss.storagetank_sysno),0)+ifnull(SUM(scd.okqty),0),3) AS startqty,
					SUM(ss.instockqty) as totalinstockqty,
					SUM(ss.outstockqty) as totaloutstockqty,
					SUM(ss.checkqty) as totalcheckqty,
					SUM(ss.stockqty) as totalstockqty,
					ifnull(FORMAT(SUM(srd.stockretankqty)/COUNT(ss.storagetank_sysno),3),0) as outretank,
					ifnull(FORMAT(SUM(srd2.stockretankqty)/COUNT(ss.storagetank_sysno),3),0) as inretank,
					ifnull(SUM(scd.okqty),0) as clearretank
					FROM `".DB_PREFIX."storage_stock` AS ss
        			LEFT JOIN `".DB_PREFIX."base_storagetank` AS bs ON bs.`sysno`=ss.`storagetank_sysno`
					LEFT JOIN ".DB_PREFIX."doc_stock_retank_detail AS srd ON srd.stockretank_sysno = ss.`storagetank_sysno`
					LEFT JOIN ".DB_PREFIX."doc_stock_retank_detail AS srd2 ON srd2.instoragetank_sysno = ss.`storagetank_sysno`
					LEFT JOIN ".DB_PREFIX."doc_stock_clear_detail scd ON scd.stock_sysno = ss.sysno
        			LEFT JOIN `".DB_PREFIX."base_goods_attribute` AS bga ON bga.`goods_sysno`=ss.`goods_sysno`
        			LEFT JOIN `".DB_PREFIX."base_unit` AS bu ON bu.`sysno`=bga.`unit_sysno`
        			WHERE  {$where} GROUP BY ss.`storagetank_sysno`";
			$result['list'] = $this->dbh->select_page($sql);
		}
		return $result;
	}

	public function getTankList3($params){
		$filter = array();
		if (isset($params['tankno']) && $params['tankno'] != '' ) {
			$filter[] = " bs.sysno`={$params['tankno']}";
		}

		if (count($filter)>0) {
			$where = " Where bs.sysno = '{$params['tankno']}'";
		}

		$sql = "SELECT count(DISTINCT bs.sysno) FROM `".DB_PREFIX."base_storagetank` bs $where";
		$result['totalRow'] = $this->dbh->select_one($sql);

		$result['list'] = array();
		if(count($result['totalRow'])>0) {
			if (isset($params['page']) && $params['page'] == false) {
				$sql = "SELECT bs.sysno,bs.storagetankname,bg.goodsname,
				(SELECT (sl.beforebeqty+sl.beqty) AS beforebeqty FROM ".DB_PREFIX."doc_storagetank_log sl WHERE sl.created_at<='{$params['date']}' AND sl.storagetank_sysno = bs.sysno ORDER BY sl.created_at DESC LIMIT 0,1  ) as startqty,
				(SELECT SUM(sl2.beqty) FROM ".DB_PREFIX."doc_storagetank_log sl2 WHERE sl2.doctype in (1,2,5,6) AND sl2.created_at >= '{$params['date1']}' AND sl2.created_at <='{$params['date2']}' AND sl2.storagetank_sysno = bs.sysno ) as totalinstockqty,
				(SELECT SUM(sl2.beqty) FROM ".DB_PREFIX."doc_storagetank_log sl2 WHERE sl2.doctype = 10 AND sl2.created_at >= '{$params['date1']}' AND sl2.created_at <= '{$params['date2']}' AND sl2.storagetank_sysno = bs.sysno AND sl2.beqty > 0 ) AS in1,
				(SELECT SUM(sl2.beqty) FROM ".DB_PREFIX."doc_storagetank_log sl2 WHERE sl2.doctype = 12 AND sl2.created_at >= '{$params['date1']}' AND sl2.created_at <= '{$params['date2']}' AND sl2.storagetank_sysno = bs.sysno AND sl2.beqty < 0 ) AS in2,
				(SELECT SUM(sl2.beqty) FROM ".DB_PREFIX."doc_storagetank_log sl2 WHERE sl2.doctype in (3,4,7,8) AND sl2.created_at >= '{$params['date1']}' AND sl2.created_at <= '{$params['date2']}' AND sl2.storagetank_sysno = bs.sysno ) as totaloutstockqty,
				(SELECT SUM(sl2.beqty) FROM ".DB_PREFIX."doc_storagetank_log sl2 WHERE sl2.doctype = 10 AND sl2.created_at >= '{$params['date1']}' AND sl2.created_at <= '{$params['date2']}' AND sl2.storagetank_sysno = bs.sysno  AND sl2.beqty < 0 ) as out1,
				(SELECT SUM(sl2.beqty) FROM ".DB_PREFIX."doc_storagetank_log sl2 WHERE sl2.doctype = 12 AND sl2.created_at >= '{$params['date1']}' AND sl2.created_at <= '{$params['date2']}' AND sl2.storagetank_sysno = bs.sysno  AND sl2.beqty > 0 ) AS out2,
				(SELECT SUM(sl2.beqty) FROM ".DB_PREFIX."doc_storagetank_log sl2 WHERE sl2.doctype in (9,11) AND sl2.created_at >= '{$params['date1']}' AND sl2.created_at <= '{$params['date2']}' AND sl2.storagetank_sysno = bs.sysno ) AS totalcheckqty,
				bs.tank_stockqty as totalstockqty,bu.unitname
				FROM ".DB_PREFIX."base_storagetank bs
				LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = bs.goods_sysno
				LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = bg.sysno
				LEFT JOIN ".DB_PREFIX."base_unit bu ON bu.sysno = bga.unit_sysno $where
				GROUP BY bs.sysno";
				$result['list'] = $this->dbh->select($sql);
			}else{
				$result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
				$this->dbh->set_page_num($params['pageCurrent']);
				$this->dbh->set_page_rows($params['pageSize']);
				$sql = "SELECT bs.sysno,bs.storagetankname,bg.goodsname,
				(SELECT (sl.beforebeqty+sl.beqty) AS beforebeqty FROM ".DB_PREFIX."doc_storagetank_log sl WHERE sl.created_at<='{$params['date']}' AND sl.storagetank_sysno = bs.sysno ORDER BY sl.created_at DESC LIMIT 0,1  ) as startqty,
				(SELECT SUM(sl2.beqty) FROM ".DB_PREFIX."doc_storagetank_log sl2 WHERE sl2.doctype in (1,2,5,6) AND sl2.created_at >= '{$params['date1']}' AND sl2.created_at <='{$params['date2']}' AND sl2.storagetank_sysno = bs.sysno ) as totalinstockqty,
				(SELECT SUM(sl2.beqty) FROM ".DB_PREFIX."doc_storagetank_log sl2 WHERE sl2.doctype = 10 AND sl2.created_at >= '{$params['date1']}' AND sl2.created_at <= '{$params['date2']}' AND sl2.storagetank_sysno = bs.sysno AND sl2.beqty > 0 ) AS in1,
				(SELECT SUM(sl2.beqty) FROM ".DB_PREFIX."doc_storagetank_log sl2 WHERE sl2.doctype = 12 AND sl2.created_at >= '{$params['date1']}' AND sl2.created_at <= '{$params['date2']}' AND sl2.storagetank_sysno = bs.sysno AND sl2.beqty < 0 ) AS in2,
				(SELECT SUM(sl2.beqty) FROM ".DB_PREFIX."doc_storagetank_log sl2 WHERE sl2.doctype in (3,4,7,8) AND sl2.created_at >= '{$params['date1']}' AND sl2.created_at <= '{$params['date2']}' AND sl2.storagetank_sysno = bs.sysno ) as totaloutstockqty,
				(SELECT SUM(sl2.beqty) FROM ".DB_PREFIX."doc_storagetank_log sl2 WHERE sl2.doctype = 10 AND sl2.created_at >= '{$params['date1']}' AND sl2.created_at <= '{$params['date2']}' AND sl2.storagetank_sysno = bs.sysno  AND sl2.beqty < 0 ) as out1,
				(SELECT SUM(sl2.beqty) FROM ".DB_PREFIX."doc_storagetank_log sl2 WHERE sl2.doctype = 12 AND sl2.created_at >= '{$params['date1']}' AND sl2.created_at <= '{$params['date2']}' AND sl2.storagetank_sysno = bs.sysno  AND sl2.beqty > 0 ) AS out2,
				(SELECT SUM(sl2.beqty) FROM ".DB_PREFIX."doc_storagetank_log sl2 WHERE sl2.doctype in (9,11) AND sl2.created_at >= '{$params['date1']}' AND sl2.created_at <= '{$params['date2']}' AND sl2.storagetank_sysno = bs.sysno ) AS totalcheckqty,
				bs.tank_stockqty as totalstockqty,bu.unitname
				FROM ".DB_PREFIX."base_storagetank bs
				LEFT JOIN ".DB_PREFIX."base_goods bg ON bg.sysno = bs.goods_sysno
				LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = bg.sysno
				LEFT JOIN ".DB_PREFIX."base_unit bu ON bu.sysno = bga.unit_sysno $where
				GROUP BY bs.sysno";

				$result['list'] = $this->dbh->select_page($sql);
			}

			foreach($result['list'] as $key=>$item){
				$result['list'][$key]['inretank']=$item['in1']+$item['in2'];
				$result['list'][$key]['outretank']=$item['out1']+$item['out2'];
			}
		}



		return $result;
	}

	/*
	 * 获取储罐收发存明细表数据
	 * @author wu xianneng
	 */
	public function getTankDetail($params){
		$result = $params;
		$filter = array();
		if (isset($params['date1']) && $params['date1'] != '') {
			$filter[] = " ss.`instockdate`>='{$params['date1']}'";
		}
		if (isset($params['date2']) && $params['date2'] != '') {
			$filter[] = " ss.`instockdate`<='{$params['date2']}'";
		}
		if (isset($params['tankno']) && $params['tankno'] != '') {
			$filter[] = " ss.`storagetank_sysno`={$params['tankno']}";
		}


		$where ='ss.iscurrent = 1 AND ss.`isdel`=0 ';
		if (count($filter)>0) {
			$where .= ' AND ' . implode(' AND ', $filter);
		}

		$sql = "SELECT count(ss.`stockqty`) FROM `".DB_PREFIX."storage_stock` AS ss
				LEFT JOIN `".DB_PREFIX."base_storagetank` AS bs ON bs.`sysno`=ss.`storagetank_sysno`
				LEFT JOIN `".DB_PREFIX."base_goods_attribute` AS bga ON bga.`goods_sysno`=ss.`goods_sysno`
				LEFT JOIN `".DB_PREFIX."base_unit` AS bu ON bu.`sysno`=bga.`unit_sysno`
				WHERE $where ";
		$result['totalRow'] = $this->dbh->select_one($sql);

		$result['list'] = array();
		if(count($result['totalRow'])>0){
			$result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
			$this->dbh->set_page_num($params['pageCurrent']);
			$this->dbh->set_page_rows($params['pageSize']);

			$sql = "select ss.*,bs.`storagetankname`,bs.`storagetanknature`,bu.`unitname`,(select sum(stockqty) from ".DB_PREFIX."storage_stock where sysno <= ss.sysno and isdel = 0 AND iscurrent = 1 AND storagetank_sysno = {$params['tankno']}) as endtotal
					from ".DB_PREFIX."storage_stock ss
					LEFT JOIN `".DB_PREFIX."base_storagetank` AS bs ON bs.`sysno`=ss.`storagetank_sysno`
					LEFT JOIN `".DB_PREFIX."base_goods_attribute` AS bga ON bga.`goods_sysno`=ss.`goods_sysno`
					LEFT JOIN `".DB_PREFIX."base_unit` AS bu ON bu.`sysno`=bga.`unit_sysno`
					WHERE $where ";
			$result['list'] = $this->dbh->select_page($sql);
		}
		return $result;
	}

	/*
	 * 获取储罐收发存明细表数据
	 * @author wu xianneng
	 */
	public function getTankDetail2($params){
		$result = $params;
		$startdate = date('Y-m-d',strtotime("-1 month"));
		$enddate = date('Y-m-d',time());

		if (isset($params['date1']) && $params['date1'] != '') {
			$startdate = $params['date1'];
		}

		if (isset($params['date2']) && $params['date2'] != '') {
			$enddate = $params['date2'];
		}

		#查询该储罐所有可用库存得到库存ID和库存类型
		#->查询每条单据对应的入库ID和货权转移ID,查询每条单据对应的入库单号和货权转移单号
		#->查询每条单据对应的出库单、货权转移单、盘点单、清库单 最后单独查询倒罐单
		$stocksql = "SELECT * FROM ".DB_PREFIX."storage_stock
					WHERE iscurrent = 1 AND storagetank_sysno ={$params['tankno']} AND instockdate BETWEEN '$startdate' AND '$enddate'";
		$stockresult = $this->dbh->select($stocksql);
		if(count($stockresult)!=0) {
			foreach ($stockresult as $stock) {
				#入库查询
				if ($stock['doctype'] == 1 || $stock['doctype'] == 2) { //车船入库
					$stockin = "SELECT ss.goodsname,un.unitname,si.stockindate docdate,si.stockintype doctype,si.stockinno docno,sid.goodsnature,sid.beqty
						FROM ".DB_PREFIX."doc_stock_in_detail sid
						LEFT JOIN ".DB_PREFIX."doc_stock_in si ON si.sysno = sid.stockin_sysno
						LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = sid.stock_sysno
						LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = ss.goods_sysno
						LEFT JOIN ".DB_PREFIX."base_unit un ON un.sysno = bga.unit_sysno
						WHERE sid.stock_sysno = {$stock['sysno']} AND si.stockindate BETWEEN '$startdate' AND '$enddate'";

					$stockinresult = $this->dbh->select($stockin);
					if (count($stockinresult) != 0)
					$data['list'][] = $stockinresult[0];
				}
				#货权转移入库查询
				elseif ($stock['doctype'] == 3) {
					$stocktrans = "SELECT ss.goodsname,un.unitname,st.stocktransdate docdate,st.stocktransno docno,ss.goodsnature,std.transqty beqty
							FROM ".DB_PREFIX."doc_stock_trans_detail std
							LEFT JOIN ".DB_PREFIX."doc_stock_trans st ON st.sysno = std.stocktrans_sysno
							LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = std.in_stock_sysno
							LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = ss.goods_sysno
							LEFT JOIN ".DB_PREFIX."base_unit un ON un.sysno = bga.unit_sysno
							WHERE std.in_stock_sysno ={$stock['sysno']} AND st.stocktransdate BETWEEN '$startdate' AND '$enddate'";

					$stocktransresult = $this->dbh->select($stocktrans);
					if (count($stocktransresult) != 0){
						$stockinresult[0]['doctype'] = 3;
						$data['list'][] = $stockinresult[0];
					}
				}

				#出库查询
				$stockout = "SELECT ss.goodsname,un.unitname,so.stockoutdate docdate,so.stockouttype doctype,so.stockoutno docno,sod.goodsnature,sod.beqty
							FROM ".DB_PREFIX."doc_stock_out_detail sod
							LEFT JOIN ".DB_PREFIX."doc_stock_out so ON so.sysno = sod.stockout_sysno
							LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = sod.stock_sysno
							LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = ss.goods_sysno
							LEFT JOIN ".DB_PREFIX."base_unit un ON un.sysno = bga.unit_sysno
							WHERE sod.stock_sysno ={$stock['sysno']} AND so.stockoutdate BETWEEN '$startdate' AND '$enddate'";

				$stockoutresult = $this->dbh->select($stockout);

				if (count($stockoutresult) != 0)
					foreach ($stockoutresult as $item) {
						if ($item['doctype'] == 1)
							$item['doctype'] = 4;
						else
							$item['doctype'] = 5;
						$item['beqty'] = -$item['beqty'];
						$data['list'][] = $item;
					}


				#货权转移出库查询
				$stocktransout = "SELECT ss.goodsname,un.unitname,st.stocktransdate docdate,st.stocktransno docno,ss.goodsnature,std.transqty beqty
								FROM ".DB_PREFIX."doc_stock_trans_detail std
								LEFT JOIN ".DB_PREFIX."doc_stock_trans st ON st.sysno = std.stocktrans_sysno
								LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = std.out_stock_sysno
								LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = ss.goods_sysno
								LEFT JOIN ".DB_PREFIX."base_unit un ON un.sysno = bga.unit_sysno
								WHERE std.out_stock_sysno = {$stock['sysno']} AND st.stocktransdate BETWEEN '$startdate' AND '$enddate'";

				$stocktransoutresult = $this->dbh->select($stocktransout);
				if (count($stocktransoutresult) != 0)
					foreach ($stocktransoutresult as $item) {
						$item['doctype'] = 6;
						$item['beqty'] = -$item['beqty'];
						$data['list'][] = $item;
					}

				#盘点单查询
				$stockcheck = "SELECT ss.goodsname,un.unitname,sc.stockcheckdate docdate,sc.stockcheckno docno,ss.goodsnature,scd.stockcheckqty beqty
							FROM ".DB_PREFIX."doc_stock_check_detail scd
							LEFT JOIN ".DB_PREFIX."doc_stock_check sc ON sc.sysno = scd.stockcheck_sysno
							LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = scd.stock_sysno
							LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = ss.goods_sysno
							LEFT JOIN ".DB_PREFIX."base_unit un ON un.sysno = bga.unit_sysno
							WHERE scd.stock_sysno ={$stock['sysno']} AND sc.stockcheckdate BETWEEN '$startdate' AND '$enddate'";

				$stockcheckresult = $this->dbh->select($stockcheck);
				if (count($stockcheckresult) != 0) {
					$stockcheckresult[0]['doctype'] = 7;
					$stockcheckresult[0]['beqty'] = -$stockcheckresult[0]['beqty'];
					$data['list'][] = $stockcheckresult[0];
				}

				#清库单查询
				$stockclear = "SELECT ss.goodsname,un.unitname,sc.stockcleardate docdate,sc.stockclearno docno,ss.goodsnature,scd.okqty beqty
							FROM ".DB_PREFIX."doc_stock_clear_detail scd
							LEFT JOIN ".DB_PREFIX."doc_stock_clear sc ON sc.sysno = scd.stockclear_sysno
							LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = scd.stock_sysno
							LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = ss.goods_sysno
							LEFT JOIN ".DB_PREFIX."base_unit un ON un.sysno = bga.unit_sysno
							WHERE scd.stock_sysno ={$stock['sysno']} AND sc.stockcleardate BETWEEN '$startdate' AND '$enddate'";

				$stockclearresult = $this->dbh->select($stockclear);
				if (count($stockclearresult) != 0) {
					$stockclearresult[0]['doctype'] = 8;
					$stockclearresult[0]['beqty'] = -$stockclearresult[0]['beqty'];
					$data['list'][] = $stockclearresult[0];
				}

				#倒罐单入罐查询
				$stockretankin = "SELECT ss.goodsname,un.unitname,sr.stockretankdate docdate,sr.stockretankno docno,ss.goodsnature,srd.stockretankqty beqty
								FROM ".DB_PREFIX."doc_stock_retank_detail srd
								LEFT JOIN ".DB_PREFIX."doc_stock_retank sr ON sr.sysno = srd.stockretank_sysno
								LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = srd.in_stock_sysno
								LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = ss.goods_sysno
								LEFT JOIN ".DB_PREFIX."base_unit un ON un.sysno = bga.unit_sysno
								WHERE srd.in_stock_sysno = {$stock['sysno']} AND sr.stockretankdate BETWEEN '$startdate' AND '$enddate'";

				$stockretankinresult = $this->dbh->select($stockretankin);
				if (count($stockretankinresult) != 0) {
					$stockretankinresult[0]['doctype'] = 9;
					$data['list'][] = $stockretankinresult[0];
				}

				#倒罐单出罐查询
				$stockretankout = "SELECT ss.goodsname,un.unitname,sr.stockretankdate docdate,sr.stockretankno docno,ss.goodsnature,srd.stockretankqty beqty
								FROM ".DB_PREFIX."doc_stock_retank_detail srd
								LEFT JOIN ".DB_PREFIX."doc_stock_retank sr ON sr.sysno = srd.stockretank_sysno
								LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = srd.out_stock_sysno
								LEFT JOIN ".DB_PREFIX."base_goods_attribute bga ON bga.goods_sysno = ss.goods_sysno
								LEFT JOIN ".DB_PREFIX."base_unit un ON un.sysno = bga.unit_sysno
								WHERE srd.out_stock_sysno = {$stock['sysno']} AND  sr.stockretankdate BETWEEN '$startdate' AND '$enddate'";

				$stockretankoutresult = $this->dbh->select($stockretankout);
				if (count($stockretankoutresult) != 0) {
					$stockretankoutresult[0]['doctype'] = 10;
					$stockretankoutresult[0]['beqty'] = -$stockretankoutresult[0]['beqty'];
					$data['list'][] = $stockretankoutresult[0];
				}
			}

			$result['totalRow'] = count($data['list']);
			$result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
			$list=array_chunk($data['list'],$params['pageSize'],false);
			$result['list']= $list [$params['pageCurrent']-1];

		}
		else{
			$result['totalRow'] = 0;
			$result['list'] = array();
		}
		array_multisort(array_column($result['list'],'docdate'),SORT_DESC,$result['list']);

		return $result;
	}

	/*
	 * 获取储罐收发存明细表数据
	 * @author wu xianneng
	 */
	public function getTankDetail3($params){
		$filter = array();
		if (isset($params['date1']) && $params['date1'] != '' ) {
			$filter[] = " dsl.created_at >='{$params['date1']}'";
		}
		if (isset($params['date2']) && $params['date2'] != '' ) {
			$filter[] = " dsl.created_at <='{$params['date2']}'";
		}
		if (isset($params['tankno']) && $params['tankno'] != '' ) {
			$filter[] = " dsl.storagetank_sysno = {$params['tankno']}";
		}

		$where = " where ";
		if (count($filter)>0) {
			$where .= implode(' AND ', $filter);
		}
		$order = " order by dsl.created_at desc ";

		$sql = "SELECT count(DISTINCT dsl.sysno) FROM `".DB_PREFIX."doc_storagetank_log` dsl $where";
		$result['totalRow'] = $this->dbh->select_one($sql);

		$result['list'] = array();
		if($result['totalRow']>0) {
			if (isset($params['page']) && $params['page'] == false) {
				$sql = "SELECT DISTINCT  dsl.sysno,CASE dsl.doctype
						WHEN 1 THEN dsl.pounds_sysno
						WHEN 2 THEN dsl.pounds_sysno
						WHEN 3 THEN dsl.pounds_sysno
						WHEN 4 THEN dsl.pounds_sysno
						ELSE dsl.doc_sysno
						END as doc_sysno,CASE dsl.doctype
						WHEN 1 THEN dsl.poundsno
						WHEN 2 THEN dsl.poundsno
						WHEN 3 THEN dsl.poundsno
						WHEN 4 THEN dsl.poundsno
						ELSE dsl.docno
						END as docno,dsl.beqty,dsl.created_at,dsl.doctype,CASE dsl.doctype
						WHEN 1 THEN dsi.goodsname
						WHEN 2 THEN dsi.goodsname
						WHEN 5 THEN dsi.goodsname
						WHEN 6 THEN dsi.goodsname
						WHEN 9 THEN scd.goodsname
						WHEN 11 THEN scd.goodsname
						WHEN 10 THEN srd.goodsname
						WHEN 12 THEN srd.goodsname
						ELSE dso.goodsname
						END as goodsname,CASE dsl.doctype
						WHEN 1 THEN dsi.goodsnature
						WHEN 2 THEN dsi.goodsnature
						WHEN 5 THEN dsi.goodsnature
						WHEN 6 THEN dsi.goodsnature
						WHEN 9 THEN scd.goodsnature
						WHEN 11 THEN scd.goodsnature
						WHEN 10 THEN srd.goodsnature
						WHEN 12 THEN srd.goodsnature
						ELSE dso.goodsnature
						END as goodsnature,CASE dsl.doctype
						WHEN 1 THEN dsi.unitname
						WHEN 2 THEN dsi.unitname
						WHEN 5 THEN dsi.unitname
						WHEN 6 THEN dsi.unitname
						WHEN 9 THEN scd.unitname
						WHEN 11 THEN scd.unitname
						WHEN 10 THEN srd.unitname
						WHEN 12 THEN srd.unitname
						ELSE dso.unitname
						END as unitname
						FROM ".DB_PREFIX."doc_storagetank_log dsl
						LEFT JOIN ".DB_PREFIX."doc_stock_in_detail dsi ON dsi.stockin_sysno = dsl.doc_sysno
						LEFT JOIN ".DB_PREFIX."doc_stock_out_detail dso ON dso.stockout_sysno = dsl.doc_sysno
						LEFT JOIN ".DB_PREFIX."doc_stock_check_detail scd ON scd.stockcheck_sysno = dsl.doc_sysno
						LEFT JOIN ".DB_PREFIX."doc_stock_retank_detail srd ON srd.stockretank_sysno = dsl.doc_sysno $where $order";
				$result['list'] = $this->dbh->select($sql);
			}else{
				$result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);
				$this->dbh->set_page_num($params['pageCurrent']);
				$this->dbh->set_page_rows($params['pageSize']);
				$sql = "SELECT DISTINCT  dsl.sysno,CASE dsl.doctype
						WHEN 1 THEN dsl.pounds_sysno
						WHEN 2 THEN dsl.pounds_sysno
						WHEN 3 THEN dsl.pounds_sysno
						WHEN 4 THEN dsl.pounds_sysno
						ELSE dsl.doc_sysno
						END as doc_sysno,CASE dsl.doctype
						WHEN 1 THEN dsl.poundsno
						WHEN 2 THEN dsl.poundsno
						WHEN 3 THEN dsl.poundsno
						WHEN 4 THEN dsl.poundsno
						ELSE dsl.docno
						END as docno,dsl.beqty,dsl.created_at,dsl.doctype,CASE dsl.doctype
						WHEN 1 THEN dsi.goodsname
						WHEN 2 THEN dsi.goodsname
						WHEN 5 THEN dsi.goodsname
						WHEN 6 THEN dsi.goodsname
						WHEN 9 THEN scd.goodsname
						WHEN 11 THEN scd.goodsname
						WHEN 10 THEN srd.goodsname
						WHEN 12 THEN srd.goodsname
						ELSE dso.goodsname
						END as goodsname,CASE dsl.doctype
						WHEN 1 THEN dsi.goodsnature
						WHEN 2 THEN dsi.goodsnature
						WHEN 5 THEN dsi.goodsnature
						WHEN 6 THEN dsi.goodsnature
						WHEN 9 THEN scd.goodsnature
						WHEN 11 THEN scd.goodsnature
						WHEN 10 THEN srd.goodsnature
						WHEN 12 THEN srd.goodsnature
						ELSE dso.goodsnature
						END as goodsnature,CASE dsl.doctype
						WHEN 1 THEN dsi.unitname
						WHEN 2 THEN dsi.unitname
						WHEN 5 THEN dsi.unitname
						WHEN 6 THEN dsi.unitname
						WHEN 9 THEN scd.unitname
						WHEN 11 THEN scd.unitname
						WHEN 10 THEN srd.unitname
						WHEN 12 THEN srd.unitname
						ELSE dso.unitname
						END as unitname
						FROM ".DB_PREFIX."doc_storagetank_log dsl
						LEFT JOIN ".DB_PREFIX."doc_stock_in_detail dsi ON dsi.stockin_sysno = dsl.doc_sysno
						LEFT JOIN ".DB_PREFIX."doc_stock_out_detail dso ON dso.stockout_sysno = dsl.doc_sysno
						LEFT JOIN ".DB_PREFIX."doc_stock_check_detail scd ON scd.stockcheck_sysno = dsl.doc_sysno
						LEFT JOIN ".DB_PREFIX."doc_stock_retank_detail srd ON srd.stockretank_sysno = dsl.doc_sysno $where $order";
				$result['list'] = $this->dbh->select_page($sql);
			}
		}
		return $result;
	}

	public function getStartAndEnd($id,$date1){
		$sql = "SELECT (beforebeqty+beqty) AS startqty FROM ".DB_PREFIX."doc_storagetank_log WHERE created_at <='$date1' AND storagetank_sysno = $id ORDER BY created_at DESC LIMIT 0,1 ";
		$res = $this->dbh->select_one($sql);
		$result['startqty'] = $res?$res:0;

		$sql = "SELECT tank_stockqty as totalstockqty FROM ".DB_PREFIX."base_storagetank where sysno = $id";
		$res = $this->dbh->select_one($sql);
		$result['totalstockqty'] = $res?$res:0;

		return $result;
	}

	public function getStorageTank(){
		$sql = "SELECT `sysno`,`storagetankname` FROM `".DB_PREFIX."base_storagetank` WHERE `isdel`=0";
		return $this->dbh->select_hash($sql);
	}
}
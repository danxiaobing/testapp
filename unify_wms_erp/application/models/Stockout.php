<?php

/**
 * Stockout Model
 *
 */
class StockoutModel
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

	public function searchStockout($params)
    {
        $filter = array();
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " s.`stockoutno` LIKE '%{$params['bar_no']}%' ";
        }
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " s.`customername` LIKE '%{$params['bar_name']}%' ";
        }
        if (isset($params['bar_stockoutstatus']) && $params['bar_stockoutstatus'] != '-100') {
            $filter[] = " s.`stockoutstatus`='{$params['bar_stockoutstatus']}'";
        }
        if (isset($params['stockouttype']) && $params['stockouttype'] != '') {
            $filter[] = " s.`stockouttype`='{$params['stockouttype']}'";
        }
        if (isset($params['begin_time']) && $params['begin_time'] != '') {
            $filter[] = " s.`stockoutdate`>='{$params['begin_time']}'";
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $filter[] = " s.`stockoutdate`<='{$params['end_time']}'";
        }
        if (isset($params['bar_receivenumber']) && $params['bar_receivenumber'] != '') {
            $filter[] = " s.`takegoodsno` like '%{$params['bar_receivenumber']}%'";
        }
        if (isset($params['bar_goodsname']) && $params['bar_goodsname'] != '') {
            $filter[] = " sod.`goodsname` like '%{$params['bar_goodsname']}%'";
        }

        $where = 's.isdel=0';
        if (1 <= count($filter)) {
            $where .= ' AND ' . implode(' AND ', $filter);
        }

        $sql = "SELECT count(*) FROM `".DB_PREFIX."doc_stock_out` s LEFT JOIN (select stockout_sysno,group_concat(DISTINCT shipname Separator' ') as shipname,goodsname,qualityname,goodsnature,unitname,sum(takeqty) as takeqty,sum(tobeqty) as tobeqty,sum(beqty) as beqty,sum(bussinesscheckqty) as bussinesscheckqty from ".DB_PREFIX."doc_stock_out_detail where isdel = 0 group by stockout_sysno) sod on s.sysno = sod.stockout_sysno where {$where}";
        $result = $params;
        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {
            if (isset($params['page']) && $params['page'] == false) {
                $sql = "SELECT * FROM `".DB_PREFIX."doc_stock_out` s LEFT JOIN (select stockout_sysno,group_concat(DISTINCT shipname Separator' ') as shipname,goodsname,qualityname,goodsnature,unitname,sum(takeqty) as takeqty,sum(tobeqty) as tobeqty,sum(beqty) as beqty,sum(bussinesscheckqty) as bussinesscheckqty from ".DB_PREFIX."doc_stock_out_detail where isdel = 0 group by stockout_sysno) sod on s.sysno = sod.stockout_sysno where {$where} ";


                if ($params['orders'] != '')
                    $sql .= " order by s." . $params['orders'];

                $result['list'] = $this->dbh->select($sql);
            } else {
                $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

                $this->dbh->set_page_num($params['pageCurrent']);
                $this->dbh->set_page_rows($params['pageSize']);

                $sql = "SELECT * FROM `".DB_PREFIX."doc_stock_out` s LEFT JOIN (select stockout_sysno,group_concat(DISTINCT shipname Separator' ') as shipname,goodsname,qualityname,goodsnature,unitname,sum(takeqty) as takeqty,sum(tobeqty) as tobeqty,sum(beqty) as beqty,sum(bussinesscheckqty) as bussinesscheckqty from ".DB_PREFIX."doc_stock_out_detail where isdel = 0 group by stockout_sysno) sod on s.sysno = sod.stockout_sysno where {$where}  ";

                if ($params['orders'] != '') {
                    $sql .= " order by s." . $params['orders'];
                }
                $result['list'] = $this->dbh->select_page($sql);
            }
        }
        return $result;
    }

	public function getStockoutDetailList($params)
	{
		$filter = array();
		if (isset($params['stockout_sysno']) && $params['stockout_sysno'] != '') {
			$filter[] = " s.`stockout_sysno` = '" . $params['stockout_sysno'] . "' ";
		}
		$where = 's.isdel=0';
		if (1 <= count($filter)) {
			$where .= ' AND ' . implode(' AND ', $filter);
		}
		$sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_stock_out_detail` s  where {$where} ";
		$result = $params;

		$result['totalRow'] = $this->dbh->select_one($sql);

		$result['list'] = array();
		if ($result['totalRow']) {
			if (isset($params['page']) && $params['page'] == false) {
				$sql = "SELECT s.bookout_detail_sysno,s.stockin_sysno, s.stockinno as stockin_no,s.bussinesscheckqty,s.takeqty,s.goodsname,s.goods_sysno,s.qualityname,s.goodsnature,s.unitname,s.outdate,s.tobeqty,s.beqty,s.storagetank_sysno,bs.storagetankname,s.shipname,s.memo,s.stock_sysno ,s.stocktype,s.inshipname
						FROM `".DB_PREFIX."doc_stock_out_detail` s
						LEFT JOIN ".DB_PREFIX."base_storagetank bs ON bs.sysno = s.storagetank_sysno
						where {$where} ";
				if ($params['orders'] != '')
					$sql .= " order by s." . $params['orders'];

				$result['list'] = $this->dbh->select($sql);

			} else {
				$result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

				$this->dbh->set_page_num($params['pageCurrent']);
				$this->dbh->set_page_rows($params['pageSize']);

				$sql = "SELECT s.bookout_detail_sysno,s.stockinno stockin_no,s.bussinesscheckqty,s.takeqty,s.goodsname,s.qualityname,s.goodsnature,s.unitname,s.outdate,s.tobeqty,s.beqty,s.storagetank_sysno,bs.storagetankname,s.shipname,s.memo,s.stock_sysno ,s.stocktype,s.inshipname
						FROM `".DB_PREFIX."doc_stock_out_detail` s
						LEFT JOIN ".DB_PREFIX."base_storagetank bs ON bs.sysno = s.storagetank_sysno
						where {$where}  ";
				if ($params['orders'] != '') {
					$sql .= " order by s." . $params['orders'];
				}
				$result['list'] = $this->dbh->select_page($sql);
			}
		}

		return $result;
	}


	public function getStockoutCarList($params)
	{
		$filter = array();
		if (isset($params['stockout_sysno']) && $params['stockout_sysno'] != '') {
			$filter[] = " s.`stockout_sysno` = '" . $params['stockout_sysno'] . "' ";
		}
		$where = 's.isdel=0';
		if (1 <= count($filter)) {
			$where .= ' AND ' . implode(' AND ', $filter);
		}
		$sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."doc_stock_out_cars` s  where {$where} ";
		$result = $params;

		$result['totalRow'] = $this->dbh->select_one($sql);
		$result['list'] = array();
		if ($result['totalRow']) {
			if (isset($params['page']) && $params['page'] == false) {
				$sql = "SELECT s.*
								FROM `".DB_PREFIX."doc_stock_out_cars` s

								where {$where} ";
				if ($params['orders'] != '')
					$sql .= " order by " . $params['orders'];
				$result['list'] = $this->dbh->select($sql);
			} else {
				$result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

				$this->dbh->set_page_num($params['pageCurrent']);
				$this->dbh->set_page_rows($params['pageSize']);

				$sql = "SELECT s.*
								FROM `".DB_PREFIX."doc_stock_out_cars` s

								where {$where}  ";
				if ($params['orders'] != '') {
					$sql .= " order by " . $params['orders'];
				}
				$result['list'] = $this->dbh->select_page($sql);
			}
		}
		return $result;
	}

	public function getStockoutById($id)
	{
		$sql = "select * from `".DB_PREFIX."doc_stock_out` where isdel = 0 and sysno= " . intval($id);
		return $this->dbh->select_row($sql);
	}

    public function getStockoutCarById($id){
        $sql = "select * from `".DB_PREFIX."doc_stock_out_cars` where stockout_sysno = ".intval($id);
        return $this->dbh->select($sql);
    }

	public function getStockoutDetailData($id)
	{
		$sql = "SELECT so.customername,so.stockoutdate,so.takegoodscompany,so.takegoodsno,group_concat(DISTINCT sod.inshipname SEPARATOR ' ') as inshipname,so.stockoutstatus,group_concat(DISTINCT shipname Separator' ') as shipname ,goodsname ,sum(tobeqty) as tobeqty,group_concat(DISTINCT bs.storagetankname) as storagetankname,sum(beqty) as beqty,so.memo,so.sby_employeename FROM `".DB_PREFIX."doc_stock_out` so
			LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` sod ON so.sysno = sod.stockout_sysno
			LEFT JOIN ".DB_PREFIX."base_storagetank bs ON bs.sysno = sod.storagetank_sysno
                    WHERE so.sysno = ".intval($id)." group by sod.stockout_sysno";
        return $this->dbh->select_row($sql);
	}

	/*
	 * 查询储罐详单
	 */
	public function getStockOutDetailByStockOutSysno($id)
	{
		$sql = "SELECT * FROM ".DB_PREFIX."doc_stock_out_detail WHERE stockout_sysno= " . intval($id);
		return $this->dbh->select($sql);
	}

    /*
     * 查询提货单信息根据磅码单
     */
    public function getStockOutByPoundsId($id)
    {
        $sql = "SELECT hdpod.*,dso.takegoodsqty,dso.takegoodscompany,dsod.goods_sysno,dsod.stockin_sysno,ss.goodsnature,ss.shipname
                    FROM ".DB_PREFIX."doc_pounds_out_detail hdpod
                    LEFT JOIN `".DB_PREFIX."doc_stock_out` dso ON hdpod.stockout_sysno = dso.sysno
                    LEFT JOIN `".DB_PREFIX."doc_stock_out_detail` dsod ON hdpod.stockoutdetail_sysno = dsod.sysno
                    LEFT JOIN ".DB_PREFIX."storage_stock ss ON ss.sysno = dsod.stock_sysno
                    WHERE hdpod.pounds_out_sysno = ".intval($id);
        return $this->dbh->select($sql);
    }

	/**
	 * @auther hanshutan
	 * @param $id
	 * @title 根据入库单id查询 出库预约单状态
	 * @return  出库单状态
	 */
	public function IsdestoryByinId($id)
	{
		$sql = "SELECT dbo.bookingoutstatus FROM ".DB_PREFIX."doc_booking_out as dbo
				LEFT JOIN ".DB_PREFIX."doc_booking_out_detail as dbod on dbo.sysno = dbod.bookingout_sysno
				where dbo.bookingoutstatus = 5 AND dbod.stockin_sysno =  " . intval($id);
		$result = $this->dbh->select_row($sql);
		if ($result) {
			$array = ['code' => 200, 'status' => $result['bookingoutstatus']];
		} else {
			$array = ['code' => 300, 'status' => '无数据'];
		}
		return $array;
	}

	public function addStockout($data, $stockoutdetaildata, $stockmarks = '')
	{
		$this->dbh->begin();
		try {
			$sql = 'select * from `'.DB_PREFIX.'doc_booking_out` where sysno=' . intval($data['booking_out_sysno']);
			$bookdata = $this->dbh->select_row($sql);

			if (!$bookdata) {
				$this->dbh->rollback();
				return false;
			}

			if ($bookdata['issaveorder']) {
				$this->dbh->rollback();
				return false;
			}

			$res = $this->dbh->insert(DB_PREFIX.'doc_stock_out', $data);

			if (!$res) {
				$this->dbh->rollback();
				return false;
			}

			$id = $res;

			$totalinstockqty = 0;


			foreach ($stockoutdetaildata as $value) {
				$sql = "select shipname from ".DB_PREFIX."doc_stock_in si left join ".DB_PREFIX."doc_stock_in_detail sid on si.sysno = sid.stockin_sysno where si.isdel = 0 and si.stockintype = 1 and si.sysno = " .intval($value['stockin_sysno']);
                $inshipname = $this->dbh->select_one($sql);
				$input = array(
					'stockout_sysno' => $id,
					'stockin_sysno' => $value['stockin_sysno'],
					'stockinno' => $value['stockin_no'],
					'goods_sysno' => $value['goods_sysno'],
					'goods_quality_sysno' => $value['goods_quality_sysno'],
					'goodsnature' => $value['goodsnature'],
					'outdate' => $value['outdate'],
					'beqty' => $value['bussinesscheckqty'],
					'takeqty' => $value['takeqty'],
					'tobeqty' => $value['tobeqty'],
					'storagetank_sysno' => $value['storagetank_sysno'],
					'memo' => $value['memo'],
					'shipname' => $value['shipname'],
					'bookout_detail_sysno' => $value['bookout_detail_sysno'],
					'stock_sysno' => $value['stock_sysno'],
					'status' => 1,
					'isdel' => 0,
					'version' => 1,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
					'goodsname' => $value['goodsname'],
					'qualityname' => $value['qualityname'],
					'unitname' => $value['unitname'],
					'bussinesscheckqty' => $value['bussinesscheckqty'],
					'stocktype' => $value['stocktype'],
					'inshipname' => $inshipname
				);

				$res = $this->dbh->insert(DB_PREFIX.'doc_stock_out_detail', $input);

				if (!$res) {
					$this->dbh->rollback();
					return false;
				}
			}

			$updateData = array(
				'stock_sysno' => $id,
				'stockno' => $data['stockoutno'],
				);
			if($data['stockouttype'] == 1){
				$updateData['businesstype'] = 8;
				$oldbusinesstype = 7;
			}elseif($data['stockouttype'] == 3){
				$updateData['businesstype'] = 12;
				$oldbusinesstype = 11;
			}
			if($bookdata['ispipelineorder'] == '1'){
				$res = $this->dbh->update(DB_PREFIX.'doc_pipelineorder',$updateData,'businesstype = '.$oldbusinesstype.' and booking_sysno ='.intval($bookdata['sysno']));
				if (!$res) {
					$this->dbh->rollback();
					return false;
				}
			}

			if($bookdata['isberthorder'] == '1'){
				$res = $this->dbh->update(DB_PREFIX.'doc_berthorder',$updateData,'businesstype = '.$oldbusinesstype.' and booking_sysno =' .intval($bookdata['sysno']));
				if (!$res) {
					$this->dbh->rollback();
					return false;
				}
			}

			if($bookdata['isqualitycheck'] == '1'){
				if($data['stockouttype'] == 1){
					$updateData['businesstype'] = 7;
					$oldbusinesstype = 6;
				}elseif($data['stockouttype'] == 3){
					$updateData['businesstype'] = 9;
					$oldbusinesstype = 8;
				}
				$res = $this->dbh->update(DB_PREFIX.'doc_qualitycheck',$updateData,'businesstype = '.$oldbusinesstype.' and booking_sysno =' .intval($bookdata['sysno']));
				if (!$res) {
					$this->dbh->rollback();
					return false;
				}
			}

			$bookoutData = array(
				'bookingoutstatus' => '6',
				'issaveorder' => '1'
			);

			$res = $this->dbh->update(DB_PREFIX.'doc_booking_out', $bookoutData, 'sysno=' . $data['booking_out_sysno']);
			if (!$res) {
				$this->dbh->rollback();
				return false;
			}

			$user = Yaf_Registry::get(SSN_VAR);
			#库存管理业务操作日志
			$S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
			$B = new BookoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
			$input = array(
				'doc_sysno' => $id,
				'opertype' => 1,
				'operemployee_sysno' => $user['employee_sysno'],
				'operemployeename' => $user['employeename'],
				'opertime' => '=NOW()',
				'operdesc' => $stockmarks,
			);
			if ($data['stockouttype'] == 1) {
				$input['doctype'] = 5;
				$title = '船';
				$action = 'shipcheck';
			}elseif($data['stockouttype'] == 3){
				$input['doctype'] = 26;
				$title = '管';
				$action = 'pipelineAudit';
			}
			$res = $S->addDocLog($input);
			if (!$res) {
				$this->dbh->rollback();
				return false;
			}

			if ($data['stockoutstatus'] == 3) {
				$sql = "SELECT sysno from hengyang_system_privilege where privilegename = '{$title}出库'";
                $privilege_sysno = $this->dbh->select_one($sql);

                $sql = "SELECT sysno from hengyang_system_privilege where privilegecontroller like '%stockout%' and privilegeaction like '%examjson%' and parent_sysno=".intval($privilege_sysno);
                $privilege_sysno = $this->dbh->select_one($sql);
                $userArr = $B->getUsers($privilege_sysno);
                if(count($userArr)>0){
                    $content = $data['customername']."的{$title}出库订单".$data['stockoutno']."待审核";
                    foreach ($userArr as $uvalue) {
                        $messageInput = array(
                            'send_from_id'=>$user['sysno'],
                            'send_from_name'=>$user['username'],
                            'send_to_id'=>$uvalue['user_sysno'],
                            'viewstatus'=>1,
                            'subject'=>$title.'出库订单待审核',
                            'content'=>$content,
                            'message_type'=>1,
                            'replyid'=>'',
                            'created_at'=>'=NOW()',
                            'updated_at'=>'=NOW()',
                            'action'=>$action,
                            'control'=>'stockout',
                            'doc_sysno'=>$id,
                        );
                        $S ->addmessage($messageInput);
                    }
                }
				$input['opertype'] = 2;
                $res = $S->addDocLog($input);
                if (!$res) {
					$this->dbh->rollback();
					return false;
				}
			}

			if ($data['stockoutstatus'] == 8) {
				$input['opertype'] = 6;
				$res = $S->addDocLog($input);
				if (!$res) {
					$this->dbh->rollback();
					return false;
				}
			}


			$this->dbh->commit();
			// #释放锁
			// $this->dbh->unlock();事务中不需要unlock
			// error_log(date("Y-m-d H:i:s") . "\t" . $id . "\n", 3, './logs/stockout.log');
			return $id;

		} catch (Exception $e) {
			$this->dbh->rollback();
			return false;
		}
	}

	public function updateStockout($id, $data, $stockoutdetaildata, $stockmarks = '')
	{
		$this->dbh->begin();
		try {
			$res = $this->dbh->update(DB_PREFIX.'doc_stock_out', $data, 'sysno=' . intval($id));

			if (!$res) {
				$this->dbh->rollback();
				return false;
			}

			$res = $this->dbh->delete(DB_PREFIX.'doc_stock_out_detail', 'stockout_sysno=' . intval($id));

			if (!$res) {
				$this->dbh->rollback();
				return false;
			}

			$totalinstockqty = 0;
			foreach ($stockoutdetaildata as $value) {
				$sql = "select shipname from ".DB_PREFIX."doc_stock_in si left join ".DB_PREFIX."doc_stock_in_detail sid on si.sysno = sid.stockin_sysno where si.isdel = 0 and si.stockintype = 1 and si.sysno = " .intval($value['stockin_sysno']);
                $inshipname = $this->dbh->select_one($sql);
				$input = array(
					'stockout_sysno' => $id,
					'bookout_detail_sysno' => $value['bookout_detail_sysno'],
					'stockin_sysno' => $value['stockin_sysno'],
					'stockinno' => $value['stockin_no'],
					'goods_sysno' => $value['goods_sysno'],
					'goods_quality_sysno' => $value['goods_quality_sysno'],
					'goodsnature' => $value['goodsnature'],
					'outdate' => $value['outdate'],
					'takeqty' => $value['takeqty'],
					'tobeqty' => $value['tobeqty'],
					'beqty' => $value['bussinesscheckqty'],
					'storagetank_sysno' => $value['storagetank_sysno'],
					'memo' => $value['memo'],
					'shipname' => $value['shipname'],
					'stock_sysno' => $value['stock_sysno'],
					'status' => 1,
					'isdel' => 0,
					'version' => 1,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
					'goodsname' => $value['goodsname'],
					'qualityname' => $value['qualityname'],
					'unitname' => $value['unitname'],
					'bussinesscheckqty' => $value['bussinesscheckqty'],
					'stocktype' => $value['stocktype'],
					'inshipname' => $inshipname
				);
				if($data['stockoutstatus'] == 6){
					$input['bussinesscheckqty'] = 0;
				}

				$res = $this->dbh->insert(DB_PREFIX.'doc_stock_out_detail', $input);

				if (!$res) {
					$this->dbh->rollback();
					return false;
				}
			}

			$res = $this->dbh->delete(DB_PREFIX.'doc_stock_out_cars', 'stockout_sysno=' . intval($id));

			if (!$res) {
				$this->dbh->rollback();
				return false;
			}

			$user = Yaf_Registry::get(SSN_VAR);
			#库存管理业务操作日志
			$S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
			$B = new BookoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

			$res = $this->getStockoutById($id);
			if (!$res) {
				$this->dbh->rollback();
				return false;
			}

			$input = array(
				'doc_sysno' => $id,
				'operemployee_sysno' => $user['employee_sysno'],
				'operemployeename' => $user['employeename'],
				'opertime' => '=NOW()',
				'operdesc' => $stockmarks,
			);

			if ($data['stockouttype'] == 1) {
				$input['doctype'] = 5;
				$title = '船';
				$action = 'shipcheck';
			}else{
				$input['doctype'] = 26;
				$title = '管';
				$action = 'pipelineAudit';
			}

			if ($data['stockoutstatus'] == 2 || $data['stockoutstatus'] == 6) {
				$input['opertype'] = 1;
			}elseif($data['stockoutstatus'] == 3){
				$sql = "SELECT sysno from hengyang_system_privilege where privilegename = '{$title}出库'";
                $privilege_sysno = $this->dbh->select_one($sql);

                $sql = "SELECT sysno from hengyang_system_privilege where privilegecontroller like '%stockout%' and privilegeaction like '%examjson%' and parent_sysno=".intval($privilege_sysno);
                $privilege_sysno = $this->dbh->select_one($sql);
                $userArr = $B->getUsers($privilege_sysno);
                if(count($userArr)>0){
                    $content = $data['customername']."的{$title}出库订单".$data['stockoutno']."待审核";
                    foreach ($userArr as $uvalue) {
                        $messageInput = array(
                            'send_from_id'=>$user['sysno'],
                            'send_from_name'=>$user['username'],
                            'send_to_id'=>$uvalue['user_sysno'],
                            'viewstatus'=>1,
                            'subject'=>$title.'出库订单待审核',
                            'content'=>$content,
                            'message_type'=>1,
                            'replyid'=>'',
                            'created_at'=>'=NOW()',
                            'updated_at'=>'=NOW()',
                            'action'=>$action,
                            'control'=>'stockout',
                            'doc_sysno'=>$id,
                        );
                        $S ->addmessage($messageInput);
                    }
                }
				$input['opertype'] = 2;
			}elseif($data['stockoutstatus'] == 8){
				$input['opertype'] = 6;
			}

			$res = $S->addDocLog($input);
			if (!$res) {
				$this->dbh->rollback();
				return false;
			}

			$this->dbh->commit();
			// #释放锁
			// $this->dbh->unlock();事务中不需要unlock
			return $id;

		} catch (Exception $e) {
			$this->dbh->rollback();
			return false;
		}
	}


	public function stopStockout($id)
	{
		$sql = "SELECT * FROM ".DB_PREFIX."doc_pounds_out po left join ".DB_PREFIX."doc_pounds_out_detail pod on po.sysno = pod.pounds_out_sysno  where po.isdel = 0  and po.status = 1 and pod.stockout_sysno= " . intval($id);

		return $this->dbh->select($sql);
	}

	public function updateStockoutData($id, $data)
	{
		return $this->dbh->update(DB_PREFIX.'doc_stock_out', $data, 'sysno=' . intval($id));
	}

    public function poundSoutstatus($id,$carid){
        $sql = "SELECT po.poundsoutstatus FROM ".DB_PREFIX."doc_pounds_out po
        LEFT JOIN ".DB_PREFIX."doc_pounds_out_detail pod on pod.pounds_out_sysno = po.sysno
	    where po.isdel = 0  and po.status = 1 and po.poundsoutstatus != 5 and po.carid = '{$carid}' and pod.stockout_sysno = '{$id}' ";
        return $this->dbh->select($sql);

    }
    public function updateStockCar($carid,$data)
    {
        return $this->dbh->update(DB_PREFIX.'doc_stock_out_cars', $data, "carid='" . $carid." ' ");
    }

	//删除船出库订单
	public function shipdelStockoutData($id, $data)
	{
		$this->dbh->begin();
		try{
			$stockoutInfo = $this->getStockoutById($id);
			if (!$stockoutInfo) {
				$this->dbh->rollback();
				return false;
			}

			$res = $this->dbh->update(DB_PREFIX.'doc_stock_out', $data, 'sysno=' . intval($id));

			if (!$res) {
				$this->dbh->rollback();
				return false;
			}

			$res = $this->dbh->update(DB_PREFIX.'doc_booking_out', array('bookingoutstatus' => 5 ,'issaveorder' => 0), 'sysno=' . intval($stockoutInfo['booking_out_sysno']));
			if (!$res) {
				$this->dbh->rollback();
				return false;
			}

			$updateData = array('stock_sysno' => '','stockno' => '');
			if ($stockoutInfo['stockouttype'] = 1) {
				$updateData['businesstype'] = 7;
				$oldbusinesstype = 8;
			}else{
				$updateData['businesstype'] = 11;
				$oldbusinesstype = 12;
			}
			if($stockoutInfo['ispipelineorder'] == 1){
				$res = $this->dbh->update(DB_PREFIX.'doc_pipelineorder',$updateData,'stock_sysno =' .intval($id) . ' and businesstype = ' . $oldbusinesstype);
				if (!$res) {
					$this->dbh->rollback();
					return false;
				}
			}

			if($stockoutInfo['isberthorder'] == 1){
				$res = $this->dbh->update(DB_PREFIX.'doc_berthorder',$updateData,'stock_sysno =' .intval($id) . ' and businesstype = ' . $oldbusinesstype);
				if (!$res) {
					$this->dbh->rollback();
					return false;
				}
			}

			if($stockoutInfo['isqualitycheck'] == 1){
				if ($stockoutInfo['stockouttype'] = 1) {
					$updateData['businesstype'] = 6;
					$oldbusinesstype = 7;
				}else{
					$updateData['businesstype'] = 8;
					$oldbusinesstype = 9;
				}
				$res = $this->dbh->update(DB_PREFIX.'doc_qualitycheck',$updateData,'stock_sysno =' .intval($id) . ' and businesstype = ' . $oldbusinesstype);
				if (!$res) {
					$this->dbh->rollback();
					return false;
				}
			}

			$this->dbh->commit();
			return $id;
		}catch (Exception $e) {
			$this->dbh->rollback();
			$msg = '数据库操作出错';
			return false;
		}

	}

	public function examStockout($id, &$msg = null, $auditreason = null)
	{
		$this->dbh->begin();
		try {
			$sql = 'SELECT * from `'.DB_PREFIX.'doc_stock_out` where sysno=' . intval($id) . " for update";
			$data = $this->dbh->select_row($sql);

			if (!$data) {
				$this->dbh->rollback();
				$msg = '无出库记录';
				return false;
			}
			//品质检查
			if($data['isqualitycheck'] == 1){
				$businesstype = $data['stockouttype'] == 1 ? 7 : 9;
				$sql = "SELECT * from `".DB_PREFIX."doc_qualitycheck` dq left join `".DB_PREFIX."doc_qualitycheck_detail` dqd on dq.sysno = dqd.qualitycheck_sysno where dq.isdel = 0 and dq.orderstatus in (4,7,8) and dq.businesstype = ".$businesstype." and dqd.isdel = 0 and dq.stock_sysno = " . intval($id) ." order by dqd.created_at desc";
				$qualityCheck = $this->dbh->select_row($sql);
				if (!$qualityCheck) {
					$this->dbh->rollback();
					$msg = '没有完成品质检查';
					return false;
				}

				if($qualityCheck['ischecked'] == 2 && ($qualityCheck['isskip'] == 2 || $qualityCheck['isskip'] == 0)){
					$this->dbh->rollback();
					$msg = '品质检查不合格';
					return false;
				}
			}
			//管线分配
			if($data['ispipelineorder'] == 1){
				$businesstype = $data['stockouttype'] == 1 ? 8 : 12;
				$sql = "SELECT * from `".DB_PREFIX."doc_pipelineorder` dp left join `".DB_PREFIX."doc_pipelineorder_detail` dpd on dp.sysno = dpd.pipelineorder_sysno where dp.isdel = 0 and dp.businesstype = ".$businesstype." and dpd.isdel = 0 and dp.stock_sysno = " . intval($id);
				$pipelineOrder = $this->dbh->select($sql);
				if(!$pipelineOrder){
					$this->dbh->rollback();
					$msg = '此订单没有进行管线分配';
					return false;
				}
			}
			//泊位分配
			if($data['ispipelineorder'] == 1 && $data['stockouttype'] == 1){
				$sql = "SELECT * from `".DB_PREFIX."doc_berthorder` db left join `".DB_PREFIX."doc_berthorder_detail` dbd on db.sysno = dbd.berthorder_sysno where db.isdel = 0 and db.businesstype = 8 and dbd.isdel = 0 and db.stock_sysno = " . intval($id);
				$berthOrder = $this->dbh->select($sql);
				if(!$berthOrder){
					$this->dbh->rollback();
					$msg = '此订单没有进行泊位分配';
					return false;
				}
			}
			$S = new StockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
			$SG = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

			$sql = 'SELECT * from `'.DB_PREFIX.'doc_stock_out_detail` where stockout_sysno=' . intval($id);
			$stockoutdetaildata = $this->dbh->select($sql);

			if (!$stockoutdetaildata) {
				$this->dbh->rollback();
				$msg = '无出库详情记录';
				return false;
			}
			$Log = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
			$I = new IntroduceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
			foreach ($stockoutdetaildata as $value) {
				#只有审核才能调用
				//减少罐容量
				$storagetankParams = array(
					'type' => 2,
					'data' => array(
						'sysno' => $value['storagetank_sysno'],
						'tobeqty' => $value['bussinesscheckqty'],   //实际出的量
					),
				);

				$updatesgres = $SG->pubstoragetankoperation($storagetankParams);

				if ($updatesgres['code'] != '200') {
					$this->dbh->rollback();
					$msg = $updatesgres['message'];
					return false;
				}

				$sql = 'select storagetankname from `'.DB_PREFIX.'base_storagetank` where sysno=' . intval($value['storagetank_sysno']);
				$storagetankname = $this->dbh->select_one($sql);

				//货物进出记录
				$recordlog = array(
					'shipname'          => $value['shipname'],
					'goods_sysno'       => $value['goods_sysno'],
					'goodsname'         => $value['goodsname'],
					'storagetank_sysno' => $value['storagetank_sysno'],
					'storagetankname'   => $storagetankname,
					'customer_sysno'    => $data['customer_sysno'],
					'customername'      => $data['customername'],
					'beqty'             => -$value['bussinesscheckqty'],
					'tobeqty'           => $value['takeqty'],
					'stockin_sysno'     => $value['stockin_sysno'],
					'stockinno'         => $value['stockinno'],
					'doc_sysno'         => $id,
					'docno'             => $data['stockoutno'],
					'accountstoragetank_sysno' => $value['storagetank_sysno'],
					'accountstoragetankname'   => $storagetankname,
					'doc_type'          => 3,
					'stock_sysno'       => $value['stock_sysno'],
					'goodsnature'       => $value['goodsnature'],
					'takegoodscompany'  => $data['takegoodscompany'],
					'takegoodsno'       => $data['takegoodsno'],
					'stocktype'         => 1,
					);
				if($data['stockouttype'] == 3){
					$recordlog['doc_type'] = 12;
				}
				if($value['stocktype'] == 2){
					$sql = "SELECT i.sysno,customer_sysno,customername,introductiontype,introductionno,stockin_sysno,stockin_no,stock_sysno,father_introduction_sysno from `".DB_PREFIX."doc_introduction` i left join `".DB_PREFIX."doc_introduction_detail` id on i.sysno=id.introduction_sysno where id.sysno = ".intval($value['stock_sysno']);
					$introductionInfo = $this->dbh->select_row($sql);

					//查询父级有没有不可撤销提单
					if($introductionInfo['father_introduction_sysno'] != 0 && $introductionInfo['introductiontype'] != 2){
						$tempList = $I->getIntroduceTree($introductionInfo['father_introduction_sysno']);
						if(!empty($tempList)){
							foreach ($tempList as $tempValue) {
								if($tempValue['introductiontype'] == 2){
									$recordlog['customer_sysno'] = $tempValue['buy_customer_sysno'];
									$recordlog['customername'] = $tempValue['buy_customername'];
									break;
								}
							}
						}
					}else{
						if($introductionInfo['introductiontype'] == 1){
							$recordlog['customer_sysno'] = $introductionInfo['customer_sysno'];
							$recordlog['customername'] = $introductionInfo['customername'];
						}
					}
					$recordlog['introduction_sysno'] = $introductionInfo['sysno'];
					$recordlog['introductionno'] = $introductionInfo['introductionno'];
					$recordlog['stockin_sysno'] = $introductionInfo['stockin_sysno'];
					$recordlog['stockinno'] = $introductionInfo['stockin_no'];
					$recordlog['stock_sysno'] = $introductionInfo['stock_sysno'];
					$recordlog['introduction_detail_sysno'] = $value['stock_sysno'];
					$recordlog['stocktype'] = 2;
				}
				$res = $Log->addGoodsRecordLog($recordlog);

				if(!$res){
					$msg = '插入货物进出日志失败';
					$this->dbh->rollback();
					return false;
				}

				$sql = 'select * from `'.DB_PREFIX.'doc_booking_out_detail` where sysno=' . intval($value['bookout_detail_sysno']);
				$bookdetailInfo = $this->dbh->select_row($sql);

				if($value['stocktype'] == 1){
					$result = array();
	                $sql = "SELECT * from ".DB_PREFIX."storage_stock where isdel = 0 and status = 1 and iscurrent = 1 and status =1 and  sysno = " . intval($value['stock_sysno']);
	                $stockInfo = $this->dbh->select_row($sql);

	                $result['stockInfo'] = $stockInfo;
	                $result['sysno'] = $value['stock_sysno'];
	                $result['data']['outstockqty'] = $stockInfo['outstockqty'] + $value['bussinesscheckqty']; //出库量
	                $result['data']['clockqty'] = $stockInfo['clockqty'] - $bookdetailInfo['bookingoutqty'];  //锁货量
	                $result['data']['changetype'] = 3;
	                $result['data']['stockqty'] = $stockInfo['stockqty'] - $value['bussinesscheckqty'];

	                if($result['data']['stockqty'] < 0){
	                	$msg = "库存不足";
	                    $this->dbh->rollback();
	                    return false;
	                    // $result['data']['beyondqty'] = $stockInfo['beyondqty'] + ($value['bussinesscheckqty'] - $stockInfo['stockqty']);
	                }
	                $stockres = $this->dbh->update(DB_PREFIX.'storage_stock' ,$result['data'],'sysno = ' .intval($result['sysno']));

	                if (!$stockres) {
	                    $msg = "更新库存失败";
	                    $this->dbh->rollback();
	                    return false;
	                }

	                #库存备份记录
	                $stockInfo['stockInfo'] = $result['stockInfo'];
	                $stockInfo['stockInfo']['ghostsysno'] = $result['stockInfo']['sysno'];
	                unset($stockInfo['stockInfo']['sysno']);
	                $stockInfo['stockInfo']['updated_at'] = '=NOW()';
	                $stockInfo['stockInfo']['iscurrent'] = 0;
	                $stockInfo['stockInfo']['ghosttime'] = '=NOW()';
	                $stockInfo['stockInfo']['ghosttype'] = 2;
	                $stockInfo['stockInfo']['ghoststockqty'] = $result['data']['stockqty'];
	                $res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockInfo['stockInfo']);
	                if (!$res) {
	                    $this->dbh->rollback();
	                    $msg = '新增库存备份失败';
	                    return false;
	                }
				}elseif($value['stocktype'] == 2){
					//更新介绍信预约量、实提数量
					$sql = "SELECT takegoodsqty,bookingqty,untakegoodsnum,introduction_sysno,introductiondetailstatus,storagetank_sysno from `".DB_PREFIX."doc_introduction_detail` where sysno = ".intval($value['stock_sysno']);
                    $introduceInfo = $this->dbh->select_row($sql);

                    if($introduceInfo['introductiondetailstatus'] == 7){
                    	$this->dbh->rollback();
                    	$msg = '提单已撤销，禁止出库';
	                    return false;
                    }
                    $update = array(
                    	'bookingqty' => $introduceInfo['bookingqty'] - $bookdetailInfo['bookingoutqty'] < 0 ? 0 : $introduceInfo['bookingqty'] - $bookdetailInfo['bookingoutqty'],
                    	'takegoodsqty' => $introduceInfo['takegoodsqty'] + $value['bussinesscheckqty'],
                    	'untakegoodsnum' => $introduceInfo['untakegoodsnum'] - $value['bussinesscheckqty'],
                    	);
                    if($update['untakegoodsnum'] < 0){
                    	$this->dbh->rollback();
                    	$msg = '提单结存量不足，禁止出库';
	                    return false;
                    }
                    if($update['untakegoodsnum'] == 0){
                    	$update['introductiondetailstatus'] = 5;
                    }
                    $res = $this->dbh->update(DB_PREFIX.'doc_introduction_detail' ,$update,'sysno = ' .intval($value['stock_sysno']));
                    if (!$res) {
	                    $this->dbh->rollback();
	                    $msg = '更新介绍信失败';
	                    return false;
	                }
	                //更新主表状态
	                $sql = "SELECT sum(id.untakegoodsnum) as untakegoodsnum ,i.introductionstatus from `".DB_PREFIX."doc_introduction` i left join `".DB_PREFIX."doc_introduction_detail` id on i.sysno=id.introduction_sysno where i.sysno = " .intval($introduceInfo['introduction_sysno'])." group by id.introduction_sysno";
                    $info = $this->dbh->select_row($sql);

                    if($info['untakegoodsnum'] == 0 && $info['introductionstatus'] == 4){
                        $res = $this->dbh->update(DB_PREFIX."doc_introduction",array('introductionstatus' => 5),'sysno = ' . intval($introduceInfo['introduction_sysno']));

                        if (!$res) {
                            $this->dbh->rollback();
                            $msg = '数据更新出错';
                            return false;
                        }
                    }
				}
				//释放预约罐待出量
				$storagetankParams = array(
					'type' => 9,
					'data' => array(
						'sysno' => $bookdetailInfo['storagetank_sysno'],
						'orderoutqty' => $value['stocktype'] == 1 ? $value['takeqty'] : $value['bussinesscheckqty'],
					),
				);
				$updatesgres = $SG->pubstoragetankoperation($storagetankParams);
				if ($updatesgres['code'] != '200') {
					$this->dbh->rollback();
					$msg = $updatesgres['message'];
					return false;
				}

				//储罐日志
				$tankLogDate = array(
					'storagetank_sysno' => $value['storagetank_sysno'],  //储罐号 必填
					'doc_sysno' => $id,
					'docno' => $data['stockoutno'],
					'doctype' => 7,
					'beqty' => -$value['bussinesscheckqty'], //实际量 必填 增加为正 减少为负
				);
				if($data['stockouttype'] == 3){
					$tankLogDate['doctype'] = 15;
				}
				$result = $SG->addStoragetankLog($tankLogDate);

				if ($result['code'] != '200') {
					$this->dbh->rollback();
					$msg = $result['message'];
					return false;
				}
            }

			$bookoutData = array(
				'bookingoutstatus' => '6',
				'issaveorder' => '1'
			);

			$sql = 'select * from `'.DB_PREFIX.'doc_booking_out` where sysno=' . intval($data['booking_out_sysno']);
			$bookoutInfo = $this->dbh->select_row($sql);
			if(!$bookoutInfo){
				$this->dbh->rollback();
				$msg = '查询预约信息失败';
				return false;
			}

			$receiveend = strtotime($bookoutInfo['receiveend']);
			$now = strtotime(date('Y-m-d',time()));

			if ($now > $receiveend) {
				$bookoutData['receiveover'] = 1;
			}else{
				$bookoutData['receiveover'] = 0;
			}

			$res = $this->dbh->update(DB_PREFIX.'doc_booking_out', $bookoutData, 'sysno=' . $data['booking_out_sysno']);
			if (!$res) {
				$this->dbh->rollback();
				return false;
			}

			$param = array(
				'stockoutstatus' => 4,
                'auditreason' => $auditreason
			);

			$res = $this->dbh->update(DB_PREFIX.'doc_stock_out', $param, 'sysno=' . intval($id));
			if (!$res) {
				$this->dbh->rollback();
				$msg = '数据更新出错';
				return false;
			}

			$this->dbh->commit();
			return true;

		} catch (Exception $e) {
			$this->dbh->rollback();
			$msg = '数据库操作出错';
			return false;
		}
	}

    /**
     * @param      $id
     * @param null $msg
     *
     * @return bool
     */
    public function cancelStockout($id, &$msg = null)
	{
		$this->dbh->begin();
		try {
			$sql = 'select * from `'.DB_PREFIX.'doc_stock_out` where sysno=' . intval($id) . " for update";
			$data = $this->dbh->select_row($sql);

			if (!$data) {
				$this->dbh->rollback();
				$msg = '无出库记录';
				return false;
			}
            $updateData = array(
                'stock_sysno' => '',
                'stockno' => '',
            );

			if($data['stockouttype'] == 1){
                $updateData['businesstype'] =7;
                $type = 8;
            }elseif ($data['stockouttype'] == 3){
                $updateData['businesstype'] =11;
                $type = 12;
            }

            if($data['ispipelineorder'] == '1'){
                $pres = $this->dbh->update(DB_PREFIX.'doc_pipelineorder',$updateData,'businesstype = '.$type.' and stock_sysno ='.intval($id));
                if (!$pres) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            if($data['isberthorder'] == '1'){
                $bres = $this->dbh->update(DB_PREFIX.'doc_berthorder',$updateData,'businesstype = '.$type.' and stock_sysno =' .intval($id));
                if (!$bres) {
                    $this->dbh->rollback();
                    return false;
                }
            }

            if($data['isqualitycheck'] == '1'){
            	if($data['stockouttype'] == 1){
	                $updateData['businesstype'] = 6;
	                $type = 7;
	            }elseif ($data['stockouttype'] == 3){
	                $updateData['businesstype'] = 8;
	                $type = 9;
	            }
                $qres = $this->dbh->update(DB_PREFIX.'doc_qualitycheck',$updateData,'businesstype = '.$type.' and stock_sysno =' .intval($id));
                if (!$qres) {
                    $this->dbh->rollback();
                    return false;
                }
            }
			$S = new StockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
			$ST = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
			$sql = 'select * from `'.DB_PREFIX.'doc_stock_out_detail` where stockout_sysno=' . intval($id);
			$stockoutdetaildata = $this->dbh->select($sql);

			if (!$stockoutdetaildata) {
				$this->dbh->rollback();
				$msg = '无出库详情记录';
				return false;
			}
			foreach ($stockoutdetaildata as $value) {
				if($value['stocktype'] == 2){
					$sql = "SELECT introductiondetailstatus from `".DB_PREFIX."doc_introduction_detail` where sysno = ".intval($value['stock_sysno']);
					$introductiondetailstatus = $this->dbh->select_row($sql);
					if($introductiondetailstatus == 7){
						$this->dbh->rollback();
						$msg = '所属提单已撤销,无法作废';
						return false;
					}
				}
			}

			foreach ($stockoutdetaildata as $value) {
				if($value['stocktype'] == 1){
					$sql = 'select * from `'.DB_PREFIX.'storage_stock` where sysno=' . intval($value['stock_sysno']);
					$stockdetaildata = $this->dbh->select_row($sql);

					if (!$stockdetaildata) {
						$this->dbh->rollback();
						$msg = '无库存记录';
						return false;
					}

					if ($stockdetaildata['isclearstock'] == 1) {
						$this->dbh->rollback();
						$msg = '对应入库单已被清库';
						return false;
					}
					//增加对应入库单的库存

					$params = array('type' => 12);
					$stockdata = array(
						'sysno' => $value['stock_sysno'],
						'outstockqty' => $value['bussinesscheckqty'],
						'clockqty' => $value['takeqty'],
					);
/*					if($stockdetaildata['stockqty'] < 0){
	                    $stockdata['beyondqty'] = $stockdetaildata['beyondqty'] + $stockdetaildata['stockqty'];
	                }*/
					$params['data'] = $stockdata;
					$stockres = $S->pubstockoperation($params);

					if ($stockres['code'] != '200') {
						$msg = $stockres['message'];
						$this->dbh->rollback();
						return false;
					}
				}elseif($value['stocktype'] == 2){
					//更新介绍信预约量、实提数量
					$sql = "SELECT takegoodsqty,bookingqty,untakegoodsnum,introduction_sysno from `".DB_PREFIX."doc_introduction_detail` where sysno = ".intval($value['stock_sysno']);
                    $introduceInfo = $this->dbh->select_row($sql);

                    $update = array(
                    	'bookingqty' => $introduceInfo['bookingqty'] + $value['takeqty'],
                    	'takegoodsqty' => $introduceInfo['takegoodsqty'] - $value['bussinesscheckqty'],
                    	'untakegoodsnum' => $introduceInfo['untakegoodsnum'] + $value['bussinesscheckqty'],
                    	);
                    if($introduceInfo['untakegoodsnum'] == 0){
                    	$update['introductiondetailstatus'] = 4;
                    }
                    $res = $this->dbh->update(DB_PREFIX.'doc_introduction_detail' ,$update,'sysno = ' .intval($value['stock_sysno']));

                    if (!$res) {
	                    $this->dbh->rollback();
	                    $msg = '更新介绍信失败';
	                    return false;
	                }

	                //更新主表状态
	                $sql = "SELECT i.sysno,sum(untakegoodsnum) as untakegoodsnum ,i.introductionstatus from `".DB_PREFIX."doc_introduction` i left join `".DB_PREFIX."doc_introduction_detail` id on i.sysno=id.introduction_sysno where i.sysno = " .intval($introduceInfo['introduction_sysno']);
                    $info = $this->dbh->select_row($sql);

                    if($update['untakegoodsnum'] != 0 && $info['introductionstatus'] == 5){
                        $res = $this->dbh->update(DB_PREFIX."doc_introduction",array('introductionstatus' => 4),'sysno = ' . intval($introduceInfo['introduction_sysno']));

                        if (!$res) {
                            $this->dbh->rollback();
                            $msg = '数据更新出错';
                            return false;
                        }
                    }
				}


				//更新罐子结存量
				$storagetankdata = array(
					'sysno' => $value['storagetank_sysno'],
					'tobeqty' => $value['bussinesscheckqty'],     //实际出的量

				);
				$params['data'] = $storagetankdata;
				$params['type'] = 12;
				$storagetankres = $ST->pubstoragetankoperation($params);

				if ($storagetankres['code'] != '200') {
					$msg = $storagetankres['message'];
					$this->dbh->rollback();
					return false;
				}


				//退回预约罐待出量
				$sql = 'select * from `'.DB_PREFIX.'doc_booking_out_detail` where sysno=' . intval($value['bookout_detail_sysno']);
				$bookdetailInfo = $this->dbh->select_row($sql);

				$storagetankParams = array(
					'type' => 10,
					'data' => array(
						'sysno' => $bookdetailInfo['storagetank_sysno'],
						'orderoutqty' => $value['stocktype'] == 1 ? $value['takeqty'] : $value['bussinesscheckqty'],
					),
				);

				$updatesgres = $ST->pubstoragetankoperation($storagetankParams);

				if ($updatesgres['code'] != '200') {
					$this->dbh->rollback();
					$msg = $updatesgres['message'];
					return false;
				}

				$tankLogDate = array(
					'storagetank_sysno' => $value['storagetank_sysno'],  //储罐号 必填
					'doc_sysno' => $id,
					'docno' => $data['stockoutno'],
					'doctype' => 8,
					'beqty' => $value['stocktype'] == 1 ? $value['takeqty'] : $value['bussinesscheckqty'], //实际量 必填 增加为正 减少为负
				);
				if($data['stockouttype'] == 3){
					$tankLogDate['doctype'] = 16;
				}
				$result = $ST->addStoragetankLog($tankLogDate);

				if ($result['code'] != '200') {
					$this->dbh->rollback();
					$msg = $result['message'];
					return false;
				}

			}
			$doc_type = 3;
			if($data['stockouttype'] == 3){
				$doc_type = 12;
			}
			//货物进出日志
			$res = $this->dbh->update(DB_PREFIX.'doc_goods_record_log',['isdel' => 1 ,'doc_type' => $doc_type],'doc_sysno = '.intval($id));
			if(!$res){
				$msg = '插入货物进出日志失败';
				$this->dbh->rollback();
				return false;
			}

			//作废成功将单据状态置为作废
			$param = array(
				'stockoutstatus' => 5
			);
			$res = $this->dbh->update(DB_PREFIX.'doc_stock_out', $param, 'sysno=' . intval($id));

			if (!$res) {
				$this->dbh->rollback();
				$msg = '数据更新出错';
				return false;
			}
			//将出库预约单状态置为已审核
			$param = array(
				'bookingoutstatus' => 5,
				'issaveorder' => 0
			);
			$res = $this->dbh->update(DB_PREFIX.'doc_booking_out', $param, 'sysno=' . intval($data['booking_out_sysno']));

			if (!$res) {
				$this->dbh->rollback();
				$msg = '数据更新出错';
				return false;
			}

			$this->dbh->commit();
			return true;
		} catch (Exception $e) {
			$this->dbh->rollback();
			$msg = '数据库操作出错';
			return false;
		}
	}

	//通过出库单主键查找出库单明细
	public function getStockoutDetailBySysno($id)
	{
		$sql = "SELECT sum(beqty) as beqty,sum(bussinesscheckqty) as bussinesscheckqty from ".DB_PREFIX."doc_stock_out_detail where isdel = 0 and stockout_sysno = {$id}";
		return $this->dbh->select($sql);
	}

	//通过出库单主键查找预约单信息
	public function getBookoutDataBysysno($id)
	{
	   $sql = "select dbt.*,dbo.docsource,dbo.bookingoutno from ".DB_PREFIX."doc_stock_out_detail dsod left join ".DB_PREFIX."doc_booking_out_detail dbt on dsod.bookout_detail_sysno = dbt.sysno

		left join ".DB_PREFIX."doc_booking_out dbo on dbt.bookingout_sysno = dbo.sysno where dsod.stockout_sysno = " . intval($id) . " group by dbo.sysno";

		return $this->dbh->select($sql);
	}

	//根据出库单查询磅码信息
	public function getPoundsoutByStockoutsysno($stockout_sysno = 0)
	{
		$sql = "SELECT sum(realnumber) as beqty from ".DB_PREFIX."doc_pounds_out po left join ".DB_PREFIX."doc_pounds_out_detail pod on po.sysno = pod.pounds_out_sysno where po.poundsoutstatus = 4 and po.status = 1 and po.isdel = 0 and pod.stockout_sysno = " . intval($stockout_sysno);
		return $this->dbh->select_one($sql);
	}
	//根据出库单查询磅码信息
	public function getPoundsOutDetailByStockOutSysno($stockout_sysno = 0)
	{
		$sql = "SELECT dpo.sysno,dpo.carid,dpo.carname,dpo.mobilephone,dpo.idcard,if(dpo.poundsoutstatus =4, sum(dpod.realnumber), '--') as beqty FROM `".DB_PREFIX."doc_pounds_out_detail` dpod
                LEFT JOIN ".DB_PREFIX."doc_pounds_out dpo ON dpod.pounds_out_sysno = dpo.sysno
                WHERE dpo.status = 1 AND dpo.isdel = 0 AND dpod.stockout_sysno = ".intval($stockout_sysno)." GROUP BY dpod.pounds_out_sysno";
		return $this->dbh->select($sql);
	}

	public function stockoutAddcar($id,$stockoutcardata)
	{
		$this->dbh->begin();
		try {

			$res = $this->dbh->delete(DB_PREFIX.'doc_stock_out_cars', 'stockout_sysno=' . intval($id));

			if (!$res) {
				$this->dbh->rollback();
				return false;
			}

			if (count($stockoutcardata) > 0) {
				foreach ($stockoutcardata as $value) {
					$input = array(
						'stockout_sysno' => $id,
						'bookout_detail_sysno' => $value['bookout_detail_sysno'],
						'stock_sysno' => $value['stock_sysno'],
						'carname' => $value['carname'],
						'mobilephone' => $value['mobilephone'],
						'idcard' => $value['idcard'],
						'carid' => $value['carid'],
						'carmarks' => $value['carmarks'],
						'weight' => $value['weight'],
						'fullcartime' => $value['fullcartime'],
						'fullcarqty' => $value['fullcarqty'],
						'emptycartime' => $value['emptycartime'],
						'emptycarqty' => $value['emptycarqty'],
						'carqty' => $value['carqty'],
						'goodsname' => $value['goodsname'],
						'qualityname' => $value['qualityname'],
						'goodsnature' => $value['goodsnature'],
						'unitname' => $value['unitname'],
						'cartakeqty' => $value['cartakeqty'],
						'status' => 1,
						'isdel' => 0,
						'version' => 1,
						'created_at' => '=NOW()',
						'updated_at' => '=NOW()'
					);

					$res = $this->dbh->insert(DB_PREFIX.'doc_stock_out_cars', $input);

					if (!$res) {
						$this->dbh->rollback();
						return false;
					}
				}
			}


			$user = Yaf_Registry::get(SSN_VAR);
			#库存管理业务操作日志
			$S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

			$input = array(
				'doc_sysno' => $id,
				'doctype'  =>  6,
				'opertype' => 2,
				'operemployee_sysno' => $user['employee_sysno'],
				'operemployeename' => $user['employeename'],
				'opertime' => '=NOW()',
				'operdesc' => '添加车辆信息',
			);

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

	//获取入库单的船名
	public function getStockinShipname($id)
	{
		$sql = "SELECT group_concat(DISTINCT shipname Separator' ') as shipname from ".DB_PREFIX."doc_stock_in si left join ".DB_PREFIX."doc_stock_in_detail sid on si.sysno = sid.stockin_sysno where si.isdel = 0 and si.sysno = " . intval($id);

		return $this->dbh->select_one($sql);
	}

	//车出库订单终止
	public function stopCarStockout($id = 0)
	{
		if (!$id) {
			return array('code' => 300,'msg' => '订单信息错误');
		}
		$this->dbh->begin();
		try{
			$data = array(
				'stockoutstatus'=> 4,
				'updated_at' =>'=NOW()'
				);

			$So = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
			$B = new BookoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
			$S = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
			$stock = new StockModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

			$sql = "SELECT * from ".DB_PREFIX."doc_stock_out_detail where stockout_sysno=" . intval($id);
            $detailData = $this->dbh->select($sql);
            if (!$detailData) {
                return array('code' => 300,'msg' => '订单明细信息错误');
            }

			//出库信息
			$stockoutInfo = $this->getStockoutById($id);
			if (!$stockoutInfo) {
				return array('code' => 300,'msg' => '出库信息错误');
			}
			//出库预约明细
            $bookoutdetailData = $B->getBookoutDetailDataById($stockoutInfo['booking_out_sysno']);
			if (!$bookoutdetailData) {
				return array('code' => 300,'msg' => '出库预约信息错误');
			}

			foreach ($bookoutdetailData as $key => $value) {
				//获取磅码单实际出库量
				$sql = "SELECT sum(realnumber) as beqty from ".DB_PREFIX."doc_pounds_out po left join ".DB_PREFIX."doc_pounds_out_detail pod on po.sysno = pod.pounds_out_sysno where po.poundsoutstatus = 4 and po.status = 1 and po.isdel = 0 and pod.stockoutdetail_sysno = " . intval($detailData[$key]['sysno']);
				$beqty = $this->dbh->select_one($sql);
				$beqty = $beqty ? floatval($beqty)/1000 : 0;
				//获取退货磅码单实际退货量
				$sql = "SELECT sum(realnumber) as rebackqty from ".DB_PREFIX."doc_pounds_reback pr left join ".DB_PREFIX."doc_pounds_reback_detail prd on pr.sysno = prd.pounds_reback_sysno where pr.poundsinstatus = 4 and pr.status = 1 and pr.isdel = 0 and prd.stockoutdetail_sysno = " . intval($detailData[$key]['sysno']);
				$rebackqty = $this->dbh->select_one($sql);
				$rebackqty = $rebackqty ? floatval($rebackqty)/1000 : 0;
				if($value['stocktype'] == 1){
					//库存信息
					$stockinfo = $stock->getStockinfoByid($detailData[$key]['stock_sysno']);
					if (!$stockinfo) {
						return array('code' => 300,'msg' => '库存信息错误');
					}
					$params = array(
						'clockqty' => $stockinfo['clockqty'] + $beqty - $rebackqty - floatval($value['bookingoutqty']),
						);

					$res = $stock->updatestock($detailData[$key]['stock_sysno'],$params);

					if($res['code'] != 200){
						$this->dbh->rollback();
						return $res;
					}

					#库存备份记录
					$stockInfo['stockInfo'] = $stockinfo;
	                $stockInfo['stockInfo']['ghostsysno'] = $stockinfo['sysno'];
	                unset($stockInfo['stockInfo']['sysno']);
	                $stockInfo['stockInfo']['updated_at'] = '=NOW()';
	                $stockInfo['stockInfo']['iscurrent'] = 0;
	                $stockInfo['stockInfo']['ghosttime'] = '=NOW()';
	                $stockInfo['stockInfo']['ghosttype'] = 2;
	                $stockInfo['stockInfo']['ghoststockqty'] = $stockInfo['data']['stockqty'];
					$res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockInfo['stockInfo']);
					if (!$res) {
						$this->dbh->rollback();
						$msgcode = array(
							'code' => 201,
							'message' => '新增库存备份失败',
						);
						return $msgcode;
					}
				}elseif($value['stocktype'] == 2){
					//更新介绍信预约量、实提数量
					$sql = "SELECT bookingqty,untakegoodsnum,introduction_sysno,storagetank_sysno from `".DB_PREFIX."doc_introduction_detail` where sysno = ".intval($value['stock_sysno']);
                    $introduceInfo = $this->dbh->select_row($sql);

                    $update = [];
                    if($introduceInfo['bookingqty'] != 0){
                    	$update = array(
	                    	'bookingqty' => $introduceInfo['bookingqty'] + $beqty - $rebackqty - floatval($value['bookingoutqty']) < 0 ? 0 : $introduceInfo['bookingqty'] + $beqty - $rebackqty - floatval($value['bookingoutqty']),
	                    	);
                    }

                    if($introduceInfo['untakegoodsnum'] == 0){
                    	$update['introductiondetailstatus'] = 5;
                    }
                    if (!empty($update)) {
                    	$res = $this->dbh->update(DB_PREFIX.'doc_introduction_detail' ,$update,'sysno = ' .intval($value['stock_sysno']));

	                    if (!$res) {
		                    $this->dbh->rollback();
		                    $msg = '更新介绍信失败';
		                    return false;
		                }
                    }



	                //更新主表状态
	                $sql = "SELECT sum(untakegoodsnum) as untakegoodsnum ,i.introductionstatus from `".DB_PREFIX."doc_introduction` i left join `".DB_PREFIX."doc_introduction_detail` id on i.sysno=id.introduction_sysno where i.sysno = " .intval($introduceInfo['introduction_sysno']);
                    $info = $this->dbh->select_row($sql);

                    if($info['untakegoodsnum'] == 0 && $info['introductionstatus'] == 4){
                        $res = $this->dbh->update(DB_PREFIX."doc_introduction",array('introductionstatus' => 5),'sysno = ' . intval($introduceInfo['introduction_sysno']));

                        if (!$res) {
                            $this->dbh->rollback();
                            $msg = '数据更新出错';
                            return false;
                        }
                    }
				}
				//释放储罐待储量
				$storagetankdata = array(
					'type' => 9,
					'data' => array(
								'sysno' => $value['storagetank_sysno'],
								'orderoutqty' => $value['stocktype'] == 1 ? $value['bookingoutqty'] : $beqty,
								)
					);
				$res = $S->pubstoragetankoperation($storagetankdata);

				if($res['code'] != 200){
					$this->dbh->rollback();
					return $res;
				}

			}

			$beqty = $this->getPoundsoutByStockoutsysno($id);
			$beqty = $beqty ? floatval($beqty)/1000 : 0;
			$data['takegoodsqty'] = $beqty;
			$res = $this->dbh->update(DB_PREFIX.'doc_stock_out',$data,'sysno=' . intval($id)); // 更新状态

			if(!$res){
				$this->dbh->rollback();
				return array('code' => 300,'msg' => '更新出库单状态失败');
			}

			//更新预约单状态
			$sql = 'select * from `'.DB_PREFIX.'doc_stock_out` where sysno=' . intval($id);
			$sdata = $this->dbh->select_row($sql);

			if (!$sdata) {
				$this->dbh->rollback();
				return array('code' => 300,'msg' => '无出库记录');
			}

			$sql = 'select * from `'.DB_PREFIX.'doc_booking_out` where sysno=' . intval($sdata['booking_out_sysno']);
			$bookoutInfo = $this->dbh->select_row($sql);

			if (!$bookoutInfo) {
				$this->dbh->rollback();
				return array('code' => 300,'msg' => '预约信息错误');
			}

			$receiveend = strtotime($bookoutInfo['receiveend']);
			$now = strtotime(date('Y-m-d',time()));

			if ($now > $receiveend) {
				$bookoutData['receiveover'] = 1;
			}else{
				$bookoutData['receiveover'] = 0;
			}

			$res = $this->dbh->update(DB_PREFIX.'doc_booking_out', $bookoutData, 'sysno=' . $sdata['booking_out_sysno']);

			if (!$res) {
				$this->dbh->rollback();
				return array('code' => 300,'msg' => '更新预约单失败');
			}

			$this->dbh->commit();
			return array('code' => 200,'msg' => '终止成功');;
		}catch (Exception $e) {
			$this->dbh->rollback();
			return false;
		}

	}

	//获取管道分配明细、泊位分配明细、品质检查明细
    # 按照品质检查表的businesstype类型 统一设置
	public function getPBQ($bookdata,$type)
	{
		$tempType = $type;
        if($type == 4){
            $type = 5;
        }elseif($type ==5){
            $type = 6;
        }elseif($type ==6){
            $type = 7;
        }elseif($type ==7){
            $type = 8;
        }elseif($type ==8){
            $type = 11;
        }elseif($type ==9){
            $type = 12;
        }
		if($bookdata){
			$result = array();
			if($bookdata['ispipelineorder'] == '1'){
				$sql = "SELECT * from ".DB_PREFIX."doc_pipelineorder dp left join ".DB_PREFIX."doc_pipelineorder_detail dpd on dp.sysno = dpd.pipelineorder_sysno where dpd.pipelineorder_sysno is not null and dp.orderstatus = 3 and dp.isdel = 0 and dp.booking_sysno = " .intval($bookdata['sysno']) ." and dp.businesstype = " . intval($type);
				$result['pipelineorder'] = $this->dbh->select($sql);
				if (!$result['pipelineorder']) {
					$result['pipelineorder'] = array();
				}
			}else{
				$result['pipelineorder'] = array();
			}

			if($bookdata['isberthorder'] == '1'){
				$sql = "SELECT * from ".DB_PREFIX."doc_berthorder db left join ".DB_PREFIX."doc_berthorder_detail dbd on db.sysno = dbd.berthorder_sysno where dbd.berthorder_sysno is not null and db.orderstatus = 3 and db.isdel = 0 and db.booking_sysno = " .intval($bookdata['sysno'])." and db.businesstype = " . intval($type);
				$result['berthorder'] = $this->dbh->select($sql);
				if (!$result['berthorder']) {
					$result['berthorder'] = array();
				}
			}else{
				$result['berthorder'] = array();
			}

			if($bookdata['isqualitycheck'] == '1'){
				$sql = "SELECT * from ".DB_PREFIX."doc_qualitycheck dq left join ".DB_PREFIX."doc_qualitycheck_detail dqd on dq.sysno = dqd.qualitycheck_sysno where dqd.qualitycheck_sysno is not null and dq.orderstatus in (4,7) and dq.isdel = 0 and dq.booking_sysno = " .intval($bookdata['sysno']) ." and dq.businesstype = " . intval($tempType)." ORDER BY dq.sysno DESC";
				$result['qualitycheck'] = $this->dbh->select($sql);
				if ($result['qualitycheck'] && isset($result['qualitycheck'])) {
					foreach ($result['qualitycheck'] as $key => $value) {
						if($value['ischecked'] == 1){
							$result['qualitycheck'][$key]['isskip'] = '--';
						}
					}
				}else{
					$result['qualitycheck'] = array();
				}

			}else{
				$result['qualitycheck'] = array();
			}
			return $result;
		}else{
			return false;
		}
	}
	public function checkStock($takeqty,$stocktype,$tobeqty,$stock_sysno)
	{
		if($stocktype == 1){
			$sql = "SELECT (stockqty - (if(clockqty>0,clockqty,0)) + (if(checkqty<0,checkqty,0))) as ableqty from ".DB_PREFIX."storage_stock where sysno = " .intval($stock_sysno);
		}elseif($stocktype == 2){
			$sql = "SELECT (untakegoodsnum-bookingqty) as ableqty from ".DB_PREFIX."doc_introduction_detail where sysno = " .intval($stock_sysno);
		}

		$ableqty = $this->dbh->select_one($sql);
		if($stocktype == 1){
			if($tobeqty>($ableqty+$tobeqty)){
				return array('code' => 300,'msg' => '通知数量大于此批次在储罐的库存数量，是否提交？');
			}else{
				return array('code' => 200,'msg' => '');
			}
		}elseif($stocktype == 2){
			if($tobeqty>($ableqty+$takeqty)){
				return array('code' => 300,'msg' => '通知数量大于此批次在储罐的库存数量，是否提交？');
			}else{
				return array('code' => 200,'msg' => '');
			}
		}

	}

	//车出库订单终止脚本
	public function stopAllTimeOut()
	{
		$data = array(
			'stockoutstatus'=> 4,
			'updated_at' =>'=NOW()'
			);

		$So = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
		$B = new BookoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
		$S = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
		$stock = new StockModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

		$sql = "SELECT * from ".DB_PREFIX."doc_stock_out where receiveend < CURDATE() and receiveend != '0000-00-00' and stockouttype = 2 and stockoutstatus = 3 and isdel = 0 ";
        $timeOutData = $this->dbh->select($sql);
        if(!$timeOutData){
        	return ture;
        }
        foreach ($timeOutData as $tvalue) {
        	$sql = "SELECT * from ".DB_PREFIX."doc_stock_out_detail where stockout_sysno=" . intval($tvalue['sysno']);
	        $detailData = $this->dbh->select($sql);
	        if (!$detailData) {
	            continue;
	        }

			//出库信息
			$stockoutInfo = $this->getStockoutById($tvalue['sysno']);
			if (!$stockoutInfo) {
				continue;
			}

			//出库预约明细
	        $bookoutdetailData = $B->getBookoutDetailDataById($stockoutInfo['booking_out_sysno']);
			if (!$bookoutdetailData) {
				continue;
			}

			foreach ($bookoutdetailData as $key => $value) {
				 $sql = "SELECT sum(realnumber) as beqty from ".DB_PREFIX."doc_pounds_out po left join ".DB_PREFIX."doc_pounds_out_detail pod on po.sysno = pod.pounds_out_sysno where po.poundsoutstatus = 4 and po.status = 1 and po.isdel = 0 and pod.stockoutdetail_sysno = " . intval($detailData[$key]['sysno']);
				$beqty = $this->dbh->select_one($sql);
				$beqty = $beqty ? floatval($beqty)/1000 : 0;
				if($value['stocktype'] == 1){
					//库存信息
					$stockinfo = $stock->getStockinfoByid($detailData[$key]['stock_sysno']);
					if (!$stockinfo) {
						continue;
					}
					$params = array(
						'clockqty' => $stockinfo['clockqty'] + $beqty - floatval($value['bookingoutqty']),
						);

					$res = $stock->updatestock($detailData[$key]['stock_sysno'],$params);

					if($res['code'] != 200){
						continue;
					}

					#库存备份记录
					$stockInfo['stockInfo'] = $stockinfo;
	                $stockInfo['stockInfo']['ghostsysno'] = $stockinfo['sysno'];
	                unset($stockInfo['stockInfo']['sysno']);
	                $stockInfo['stockInfo']['updated_at'] = '=NOW()';
	                $stockInfo['stockInfo']['iscurrent'] = 0;
	                $stockInfo['stockInfo']['ghosttime'] = '=NOW()';
	                $stockInfo['stockInfo']['ghosttype'] = 2;
	                $stockInfo['stockInfo']['ghoststockqty'] = $stockInfo['data']['stockqty'];
					$res = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockInfo['stockInfo']);
					if (!$res) {
						continue;
					}
				}elseif($value['stocktype'] == 2){
					//更新介绍信预约量、实提数量
					$sql = "SELECT bookingqty,untakegoodsnum,introduction_sysno from `".DB_PREFIX."doc_introduction_detail` where sysno = ".intval($value['stock_sysno']);
	                $introduceInfo = $this->dbh->select_row($sql);
	                if(floatval($value['bookingoutqty']) - $beqty > 0){
                    	$storagetankdata = array(
							'type' => 8,
							'data' => array(
										'sysno' => $introduceInfo['storagetank_sysno'],
										'orderoutqty' => floatval($value['bookingoutqty']) - $beqty,
										)
							);

						$res = $S->pubstoragetankoperation($storagetankdata);

						if($res['code'] != 200){
							$this->dbh->rollback();
							return $res;
						}
                    }
	                $update = [];
	                if($introduceInfo['bookingqty'] != 0){
	                	$update = array(
	                    	'bookingqty' => $introduceInfo['bookingqty'] + $beqty - floatval($value['bookingoutqty']) < 0 ? 0 : $introduceInfo['bookingqty'] + $beqty - floatval($value['bookingoutqty']),
	                    	);
	                }

	                if($introduceInfo['untakegoodsnum'] == 0){
	                	$update['introductiondetailstatus'] = 5;
	                }
	                if (!empty($update)) {
	                	$res = $this->dbh->update(DB_PREFIX.'doc_introduction_detail' ,$update,'sysno = ' .intval($value['stock_sysno']));

	                    if (!$res) {
		                    continue;
		                }
	                }



	                //更新主表状态
	                $sql = "SELECT sum(untakegoodsnum) as untakegoodsnum ,i.introductionstatus from `".DB_PREFIX."doc_introduction` i left join `".DB_PREFIX."doc_introduction_detail` id on i.sysno=id.introduction_sysno where i.sysno = " .intval($introduceInfo['introduction_sysno']);
	                $info = $this->dbh->select_row($sql);

	                if($info['untakegoodsnum'] == 0 && $info['introductionstatus'] == 4){
	                    $res = $this->dbh->update(DB_PREFIX."doc_introduction",array('introductionstatus' => 5),'sysno = ' . intval($introduceInfo['introduction_sysno']));

	                    if (!$res) {
	                       continue;
	                    }
	                }
				}

			}

			foreach ($detailData as $key => $value) {
				$bookdetailinfo = $B->getBookoutDetailById($value['bookout_detail_sysno']);
				$storagetankdata = array(
					'type' => 9,
					'data' => array(
								'sysno' => $bookdetailinfo['storagetank_sysno'],
								'orderoutqty' => $bookdetailinfo['bookingoutqty'],
								)
					);

				$res = $S->pubstoragetankoperation($storagetankdata);

				if($res['code'] != 200){
					continue;
				}
			}

			$beqty = $this->getPoundsoutByStockoutsysno($tvalue['sysno']);
			$beqty = $beqty ? floatval($beqty)/1000 : 0;
			$data['takegoodsqty'] = $beqty;
			$res = $this->dbh->update(DB_PREFIX.'doc_stock_out',$data,'sysno=' . intval($tvalue['sysno'])); // 更新状态

			if(!$res){
				continue;
			}

			//更新预约单状态
			$sql = 'select * from `'.DB_PREFIX.'doc_stock_out` where sysno=' . intval($tvalue['sysno']);
			$sdata = $this->dbh->select_row($sql);

			if (!$sdata) {
				continue;
			}

			$sql = 'select * from `'.DB_PREFIX.'doc_booking_out` where sysno=' . intval($sdata['booking_out_sysno']);
			$bookoutInfo = $this->dbh->select_row($sql);

			if (!$bookoutInfo) {
				continue;
			}

			$receiveend = strtotime($bookoutInfo['receiveend']);
			$now = strtotime(date('Y-m-d',time()));

			if ($now > $receiveend) {
				$bookoutData['receiveover'] = 1;
			}else{
				$bookoutData['receiveover'] = 0;
			}

			$res = $this->dbh->update(DB_PREFIX.'doc_booking_out', $bookoutData, 'sysno=' . $sdata['booking_out_sysno']);

			if (!$res) {
				continue;
			}
		}
		return ture;

	}

	//获取退货单信息
	public function getRebackData($id)
	{
		$sql = "SELECT * FROM `".DB_PREFIX."doc_stock_reback` r LEFT JOIN `".DB_PREFIX."doc_stock_reback_detail` rd ON (rd.`stockreback_sysno`=r.`sysno`) WHERE r.stockinstatus not in (7,8) and r.isdel = 0 and rd.stockout_sysno =  " . intval($id);
		return $this->dbh->select($sql);
	}
}
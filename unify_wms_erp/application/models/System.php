<?php
/**
 * 系统资源 Model
 *
 * @author  totti
 * @date    2014-07-10 16:33
 *
 */

class SystemModel {
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
	 * 根据父id获得children权限
	 * pid: 父id
	 * type: 查询类型 0 全部 1菜单2显示权限3操作权限
	 * @return 数组
	 */
	public function getPrivilegeListByPid($pid = 0, $type = 0) {


        $filter = array();

        if($pid > -1){
            $filter[] = " `parent_sysno` = $pid ";
        }
        if($type){
            $filter[] = " `parentsysnotype` = $type ";
        }

        if (1 <= count($filter)) {
            $where = ' WHERE isdel=0 AND ' . implode(' AND ', $filter);
        }

        $sql = "select * from ".DB_PREFIX."system_privilege ".$where;


        return  $this->dbh->select($sql);
	}

	/**
	 * 根据父id获得children权限
	 * pid: 父id
	 * type: 查询类型 0 全部 1菜单2显示权限3操作权限
	 * @return 数组
	 */
	public function getPrivilegeListByPidUid($uid =0,$pid = 0, $type = 0) {
		$arr = array();

		
		if($uid){
			$sql = "select distinct p.* from `".DB_PREFIX."system_privilege` p LEFT JOIN `".DB_PREFIX."system_role-r-privilege` rp ON rp.privilege_sysno=p.sysno
					LEFT JOIN `".DB_PREFIX."system_user-r-role` ur ON rp.role_sysno=ur.role_sysno 
					where ((ur.user_sysno = '".$uid."') or p.needcheck = 0 ) and p.isdel = 0 and p.status = 1 ";
			if ($pid > -1) {
				$sql .= " and p.parent_sysno = $pid ";
			}
			if ($type) {
				$sql .= " and p.parentsysnotype = $type ";
			}

			$sql .= "  order by p.menuorder desc ";
			
		}else{
			$sql = "select * from ".DB_PREFIX."system_privilege where isdel = 0 and status = 1";
			if ($pid > -1) {
				$sql .= " and parent_sysno = $pid ";
			}
			if ($type) {
				$sql .= " and parentsysnotype = $type ";
			}
		}

		$arr = $this->dbh->select($sql);
		return $arr;
	}

	public function addPrivilege($data) {

		return $this->dbh->insert(DB_PREFIX.'system_privilege', $data);
	}

	public function updatePrivilege($id = 0, $data = array()) {
		return $this->dbh->update(DB_PREFIX.'system_privilege', $data, 'sysno=' . intval($id));
	}

	/**
	 * 根据id获得权限细节
	 * id: 权限id
	 * @return 数组
	 */
	public function getPrivilegeById($id = 0) {
		$sql = "select p.*,(select privilegename from ".DB_PREFIX."system_privilege pp where pp.sysno = p.parent_sysno) as parent_privilegename from ".DB_PREFIX."system_privilege p where sysno = $id ";

		return $this->dbh->select_row($sql);
	}

	/**
	 * 根据条件显示权限列表
	 * @return 数组
	 */
	public function searchPrivilege($params) {
		$filter = array();

		if (isset($params['bar_name']) && $params['bar_name'] != '') {
			$filter[] = " p.privilegename LIKE '%" . $params['bar_name'] . "%' ";
		}
		if (isset($params['bar_parentid']) && $params['bar_parentid'] != '-100') {
			$filter[] = "p.parent_sysno  = '" . $params['bar_parentid'] . "'";
		}
		if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
			$filter[] = "p.status  = '" . $params['bar_status'] . "'";
		}

		$where = 'where isdel =0';
		if (1 <= count($filter)) {
			$where .= ' AND ' . implode(' AND ', $filter);
		}

		$sql = "SELECT COUNT(*)  from ".DB_PREFIX."system_privilege p {$where} ";

		$result = $params;

		$result['totalRow'] = $this->dbh->select_one($sql);

		$result['list'] = array();
		if ($result['totalRow'])
		{

			if( isset($params['page'] ) && $params['page'] == false){
				$sql = "select p.*,(select privilegename from ".DB_PREFIX."system_privilege pp where pp.sysno = p.parent_sysno) as parent_privilegename from ".DB_PREFIX."system_privilege p {$where} ";
				if($params['orders'] != '')
					$sql .= " order by ".$params['orders'] ;

				$arr = 	$this->dbh->select($sql);


				$result['list'] = $arr;
			}else{
				$result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);

				$this->dbh->set_page_num($params['pageCurrent'] );
				$this->dbh->set_page_rows($params['pageSize'] );


				$sql = "select p.*,(select privilegename from ".DB_PREFIX."system_privilege pp where pp.sysno = p.parent_sysno) as parent_privilegename from ".DB_PREFIX."system_privilege p {$where} ";
				if($params['orders'] != '')
					$sql .= " order by ".$params['orders'] ;

				$arr = 	$this->dbh->select_page($sql);


				$result['list'] = $arr;
			}

		}

        //如果在所有列的子菜单中找不到父菜单，则将它赋值为空
        for($i=0;$i<count($result['list']);$i++){
            $flag='no';
            for($j=0;$j<count($result['list']);$j++){
                if($result['list'][$i]['parent_sysno']==$result['list'][$j]['sysno']){
                    $flag='yes';
                    break;
                }

            }
            if($result['list'][$i]['parent_sysno']==0||$flag=='no'){
                $result['list'][$i]['parent_sysno']=null;
            }
        }

		return $result;
	}

	public function addRole($data, $privileges = "") {
		$this->dbh->begin();
		try {
			//".DB_PREFIX."system_role update
			$res = $this->dbh->insert(DB_PREFIX.'system_role', $data);

			if (!$res) {
				$this->dbh->rollback();
				return false;
			}

			$id = $res;

			//".DB_PREFIX."system_role-r-privilege delete
			$res = $this->dbh->delete(DB_PREFIX.'system_role-r-privilege', 'role_sysno=' . intval($id));

			if (!$res) {
				$this->dbh->rollback();
				return false;
			}

			if ($privileges !== "") {
				$privilegeArr = explode(",", $privileges);

				if (!empty($privilegeArr)) {
					foreach ($privilegeArr as $value) {
						$privilegesdata = array(
							'role_sysno' => $id,
							'privilege_sysno' => $value,
						);
						//".DB_PREFIX."system_role-r-privilege insert
						$res = $this->dbh->insert(DB_PREFIX.'system_role-r-privilege', $privilegesdata);

						if (!$res) {
							$this->dbh->rollback();
							return false;
						}
					}

				}
			}

			$this->dbh->commit();
			return $id;

		} catch (Exception $e) {
			$this->dbh->rollback();
			return false;
		}
	}

	public function updateRole($id = 0, $data = array(), $privileges = "") {
		$this->dbh->begin();
		try {
			//".DB_PREFIX."system_role update
			$res = $this->dbh->update(DB_PREFIX.'system_role', $data, 'sysno=' . intval($id));

			if (!$res) {
				$this->dbh->rollback();
				return false;
			}

			//".DB_PREFIX."system_role-r-privilege delete
			$res = $this->dbh->delete(DB_PREFIX.'system_role-r-privilege', 'role_sysno=' . intval($id));

			if (!$res) {
				$this->dbh->rollback();
				return false;
			}

			if ($privileges !== "") {
				$privilegeArr = explode(",", $privileges);

				if (!empty($privilegeArr)) {
					foreach ($privilegeArr as $value) {
						$privilegesdata = array(
							'role_sysno' => $id,
							'privilege_sysno' => $value,
						);
						//".DB_PREFIX."system_role-r-privilege insert
						$res = $this->dbh->insert(DB_PREFIX.'system_role-r-privilege', $privilegesdata);

						if (!$res) {
							$this->dbh->rollback();
							return false;
						}
					}

				}
			}

			$this->dbh->commit();
			return true;

		} catch (Exception $e) {
			$this->dbh->rollback();
			return false;
		}
	}

	/**
	 * 所有权限
	 */
	public function getAllPrivilege($params) {
		if (isset($params['bar_parentid']) && $params['bar_parentid'] != '-100') {
			$filter[] = "p.parent_sysno  = '" . $params['bar_parentid'] . "'";
		}

		$where = 'p.status = 1 and p.isdel = 0';

		if (1 <= count($filter)) {
			$where .= ' AND ' . implode(' AND ', $filter);
		}

		$sql = "select p.*,(select privilegename from ".DB_PREFIX."system_privilege pp where pp.sysno = p.parent_sysno) as parent_privilegename from ".DB_PREFIX."system_privilege p where {$where} ";

		return $this->dbh->select($sql);
	}

	/**
	 * 角色对应权限by数据库
	 */
	public function getRolePrivilege($id = 0) {
		$sql = "select p.* from `".DB_PREFIX."system_role-r-privilege` p where role_sysno = $id ";

		return $this->dbh->select($sql);
	}

	/**
	 * 角色对应权限by视图
	 */
	public function getRoleViewPrivilege($roleprivileges = array()) {
		$search = array();
		$privileges = $this->getAllPrivilege($search);
		$privilegesview = array();
		$module = array();

		foreach ($privileges as $privilege) {

			$privilege['check'] = false;
			foreach ($roleprivileges as $roleprivilege) {
				if ($roleprivilege['privilege_sysno'] == $privilege['sysno']) {
					$privilege['check'] = true;
					break;
				}
			}

			$privilegesview[] = $privilege;

			if ($privilege['parent_sysno'] == 0 && $privilege['parentsysnotype']!=3) {
				$module[] = array('mval' => $privilege['privilegename'], 'msysno' => $privilege['sysno'], 'check' => $privilege['check']);
			}
		}

		// foreach ($module as $mgroup) {
		// 	foreach ($privilegesview as $group) {
		// 		if($group['parent_sysno']==$mgroup['msysno'])
		// 		{
		// 			$privilegesview2[] = $group;
		// 			foreach ($privilegesview as $group2) {
		// 				if($group2['parent_sysno']==$group['sysno'])
		// 					{
		// 						$privilegesview3[] = $group2;
		// 					}
		// 			}
		// 		}
		// 	}
		// }

		foreach ($module as $mgroup) {
			$children2 = array();
			foreach ($privilegesview as $group) {
				if($group['parent_sysno']==$mgroup['msysno'])
				{
					$children3 = array();
					foreach ($privilegesview as $group2) {
						if($group2['parent_sysno']==$group['sysno'])
							{
								$children3[] = array('id'=>$group2['sysno'],'name'=>$group2['privilegename'],'checked'=>$group2['check']);
							}
					}
					if($group['check'])
					{
						$children2[] = array('id'=>$group['sysno'],'name'=>$group['privilegename'],'isParent'=>true,'children'=>$children3,'checked'=>$group['check']);
					}
					else
					{
						$children2[] = array('id'=>$group['sysno'],'name'=>$group['privilegename'],'children'=>$children3,'checked'=>$group['check']);
					}
				}
			}
			$newprivilege[] = array('id'=>$mgroup['msysno'],'name'=>$mgroup['mval'],'isParent'=>true,'children'=>$children2,'checked'=>$mgroup['check']);
		}


		$out['newmodule'] = $newprivilege;

		// $out['privileges'] = $privilegesview;
		// $out['privileges2'] = $privilegesview2;
		// $out['privileges3'] = $privilegesview3;
		$out['module'] = $module;

		return $out;
	}

	/**
	 * 根据id获得角色细节
	 * id: 权限id
	 * @return 数组
	 */
	public function getRoleById($id = 0) {
		$sql = "select p.* from ".DB_PREFIX."system_role p where sysno = $id ";

		return $this->dbh->select_row($sql);
	}

	/**
	 * 根据条件显示角色列表
	 * @return 数组
	 */
	public function searchRole($params) {
		$filter = array();
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " p.roleno LIKE '%" . $params['bar_no'] . "%' ";
        }
		if (isset($params['bar_name']) && $params['bar_name'] != '') {
			$filter[] = " p.rolename LIKE '%" . $params['bar_name'] . "%' ";
		}
		if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
			$filter[] = "p.status  = '" . $params['bar_status'] . "'";
		}

		$where = 'p.isdel = 0';
		if (1 <= count($filter)) {
			$where .= ' AND ' . implode(' AND ', $filter);
		}

		$sql = "SELECT COUNT(*)  from ".DB_PREFIX."system_role p where  {$where} ";

		$result = $params;

		$result['totalRow'] = $this->dbh->select_one($sql);


		$result['list'] = array();
		if ($result['totalRow']) {

			if( isset($params['page'] ) && $params['page'] == false){
				$sql = "select p.* from ".DB_PREFIX."system_role p where {$where} ";
				if($params['orders'] != '')
					$sql .= " order by ".$params['orders'] ;

				$arr = 	$this->dbh->select($sql);


				$result['list'] = $arr;
			}else{
				$result['totalPage'] =  ceil($result['totalRow'] / $params['pageSize']);
				
				$this->dbh->set_page_num($params['pageCurrent']);
				$this->dbh->set_page_rows($params['pageSize']);

				$sql = "select p.* from ".DB_PREFIX."system_role p where {$where} ";
				if ($params['orders'] != '') {
					$sql .= " order by " . $params['orders'];
				}

				$arr = $this->dbh->select_page($sql);

				$result['list'] = $arr;
			}
		}

		return $result;
	}

	/**
	 * 记录业务操作日志(依据编号)
	 *
	 * @param  integer      $fid
	 * @param  array        $user
	 * @param  string       $remark
	 * @return void
	 */
	public function addDocLog($input =array())
    {
        return $this->dbh->insert(DB_PREFIX.'doc_log', $input);
    }

	/**
	 * 添加消息提醒记录
	 */
	public function addmessage($input =array())
	{
		return $this->dbh->insert(DB_PREFIX.'doc_message', $input);
	}

	/**
	 * 更新消息提醒记录
	 */
	public function updateMessage($doc_sysno)
	{
		return $this->dbh->update(DB_PREFIX.'doc_message', array('isdel'=>1),"doc_sysno=".intval($doc_sysno));
	}

	#初始化数据插入,type 1入库 2货转 3提单
	public function InitData($data = array(),$type=1)
	{
		if(empty($data))
		{
			return false;
		}
		// echo "<pre>";
		// var_dump($data);exit;
		switch ($type) {
			case '1':
				if(!empty($data))
				{
					$this->InitDataStockin($data);
				}
				break;
			case 2:
				if(!empty($data))
				{
					$this->InitDataStocktrans($data);
				}
				break;
			case 3:
				if(!empty($data))
				{
					$this->InitDataIntroduction($data);
				}
				break;
			case 4:
				if(!empty($data))
				{
					$this->InitDataContract($data);
				}
				break;
			
			default:
				# code...
				break;
		}
		
		return true;
	}

	private function InitDataStockin($data = array())
	{
		foreach ($data as $key => $value) {
			#获取客户Sysno
			if($value[0]!="")
			{
				$customername = $value[0];
				$oldcustomername = $customername;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."customer where isdel=0 and status=1 and customername='".$value[0]."' ";
				$customer_sysno = $this->dbh->select_one($sql);
				if(!$customer_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}
			else
			{
				$customername = $oldcustomername;
				#说明是多明细
			}

			#获取品名Sysno
			if($value[1]!="")
			{
				$goodsname = $value[1];
				$oldgoodsname = $goodsname;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."base_goods where isdel=0 and status=1 and goodsname='".$value[1]."' ";
				$goods_sysno = $this->dbh->select_one($sql);
				if(!$goods_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}
			else
			{
				$goodsname = $oldgoodsname;
				#说明是多明细
			}

			#获取储罐Sysno
			if($value[2]!="")
			{
				$storagetankname = $value[2];
				$oldstoragetankname = $storagetankname;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."base_storagetank where isdel=0 and status=1 and storagetankname='".$value[2]."' ";
				$storagetank_sysno = $this->dbh->select_one($sql);
				if(!$storagetank_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}
			else
			{
				$storagetankname = $oldstoragetankname;
				#说明是多明细
			}

			#获取货物性质
			switch ($value[3]) {
				case '保税':
					$goodsnature = 1;
					break;
				case '外贸':
					$goodsnature = 2;
					break;
				case '外贸':
					$goodsnature = 2;
					break;
				case '内贸':
					$goodsnature = 4;
					break;
				default:
					break;
			}

			#获取规格
			if($value[4]!="")
			{
				$qualityname = $value[4];
				$oldqualityname = $qualityname;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."base_goods_quality where isdel=0 and status=1 and qualityname='".$value[4]."' ";
				$goods_quality_sysno = $this->dbh->select_one($sql);
				if(!$goods_quality_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}
			else
			{
				$qualityname = $oldqualityname;
				#说明是多明细
			}

			#获取船名
			if($value[5]!="")
			{
				$shipname = $value[5];
				$oldshipname = $shipname;
			}
			else
			{
				$shipname = $oldshipname;
				#说明是多明细
			}

			#获取入库方式
			if($shipname=="槽车入库")
			{
				$stockintype = 2;
			}
			elseif($shipname=="管输")
			{
				$stockintype = 3;
			}
			else
			{
				$stockintype = 1;
			}

			#获取入库时间
			if($value['6']!="")
			{
				$instockdate = date("Y-m-d",strtotime($value[6]));
				$oldinstockdate = $instockdate;
			}
			else
			{
				$instockdate = $oldinstockdate;
				#说明是多明细
			}

			#获取提单数量
			if($value[7]!="")
			{
				$takegoodsnum = $value[7];
				$oldtakegoodsnum = $takegoodsnum;
			}
			else
			{
				$takegoodsnum = $oldtakegoodsnum;
				#说明是多明细
			}

			#获取入库数量
			$instockqty = $value[8] ? $value[8] : 0;

			#获取报关数量
			$declareqty = $value[9] ? $value[9] : 0;

			#获取合同编号Sysno
			if($value[10]!="")
			{
				$contractnodisplay = $value[10];
				$oldcontractnodisplay = $contractnodisplay;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."doc_contract where isdel=0 and status=1 and contractnodisplay='".$value[10]."' ";
				$contract_sysno = $this->dbh->select_one($sql);
				if(!$contract_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}
			else
			{
				$contractnodisplay = $oldcontractnodisplay;
				#说明是多明细
			}

			#获取结存量
			$stockqty = $value[11] ? $value[11] : 0;

			#获取损耗量
			$ullage = $value[12] ? $value[12] : 0;	
			
			#获取欠费
			$costnum = floatval($value[13]) ? floatval($value[13]) : 0;

			#插入--入库单主表
			if($value[0]!='')
			{
				if($stockintype==1)
				{
					$pre_code = 'A2';
				}
				elseif($stockintype==2)
				{
					$pre_code = 'B2';
				}
				elseif($stockintype==3)
				{
					$pre_code = 'R2';
				}
				else
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
				$stockininfo = array(
					'stockintype'=> $stockintype,
					'stockinno' => COMMON::getCodeId($pre_code),
					'stockindate' => $instockdate,
					'customer_sysno' => $customer_sysno,
					'customername' => $customername,
					'contract_sysno' => $contract_sysno,
					'contractno' => $contractnodisplay,
					'stockinstatus' => 4,
					'takegoodsnum' => $takegoodsnum,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
					'release_num' => $stockqty,
				);
				#入库单主表id
				$stockin_sysno = $this->dbh->insert(DB_PREFIX.'doc_stock_in', $stockininfo);
                if (!$stockin_sysno) {
                    $count_inserterror++;#sql插入错误
                    $stockin_sysno = 0;#避免明细表插入错误
					continue;
                }
			}
			else
			{
				#有明细累加入库单总量
				$sql = "update ".DB_PREFIX."doc_stock_in SET takegoodsnum=takegoodsnum+{$takegoodsnum},release_num=release_num+{$stockqty} where sysno=".intval($stockin_sysno);
				$this->dbh->exe($sql);
			}
			#插入--入库单明细表
			if($stockin_sysno)
			{
				$stockindetailinfo = array(
					'stockin_sysno' => $stockin_sysno,
					'goods_sysno' => $goods_sysno,
					'goods_quality_sysno' => $goods_quality_sysno,
					'goodsnature' => $goodsnature,
					'goodsreceiptdate' => $instockdate,
					'tobeqty' => $instockqty,
					'bussinesscheckqty' => $stockqty,
					'beqty' => $stockqty,
					'storagetank_sysno' => $storagetank_sysno,
					'shipname' => $shipname,
					'goodsname' => $goodsname,
					'qualityname' => $qualityname,
					'unitname' => '吨',
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
				);

				$stockindetail_sysno = $this->dbh->insert(DB_PREFIX.'doc_stock_in_detail', $stockindetailinfo);
                if (!$stockindetail_sysno) {
                    $count_inserterror++;#sql插入错误
                    $stockindetail_sysno = 0;#避免磅码单表插入错误
					continue;
                }
                if($stockintype==2)
                {
                	#车磅码单
                    $poundsininfo = array(
                    	'poundsinno' => COMMON::getCodeId('B3'),
                    	'stockin_sysno' => $stockin_sysno,
                    	'stockinno' => $stockininfo['stockinno'],
                    	'stockindetail_sysno' => $stockindetail_sysno,
                    	'loadometer' => '50T',
                    	'storagetank_sysno' => $storagetank_sysno,
                    	'storagetankname' => $storagetankname,
                    	'carid' => '初始化车牌',
                    	'carname' => '初始化司机',
                    	'customername' => $customername,
                    	'goodsname' => $goodsname,
                    	'poundsinstatus' => 4,
                    	'beqty' => $stockqty*1000,
                    	'created_at' => '=NOW()',
						'updated_at' => '=NOW()',
                    );

                    $poundsin_sysno = $this->dbh->insert(DB_PREFIX.'doc_pounds_in', $poundsininfo);
                }
                
			}

			#插入--报关单表
			if($declareqty)
			{
				$stockindeclareinfo = array(
					'stockin_sysno'=> $stockin_sysno,
					'goods_sysno' => $goods_sysno,
					'goodsname' => $goodsname,
					'bussinesscheckqty' => $stockqty,
					'storagetank_sysno' => $storagetank_sysno,
					'storagetankname' => $storagetankname,
					'release_num' => $declareqty,
					'customer_sysno' => $customer_sysno,
					'customername' => $customername,
					'takegoodsnum' => $takegoodsnum,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
				);

				$this->dbh->insert(DB_PREFIX.'doc_stock_in_declare', $stockindeclareinfo);
			}

			#插入--库存记录表
			if($value[0]!='')
			{
				if($stockintype==1)
				{
					$doctype = 1;
				}
				elseif($stockintype==2)
				{
					$doctype = 2;
				}
				elseif($stockintype==3)
				{
					$doctype = 4;
				}
				else
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
				$stockinfo = array(
					'stockno' => COMMON::getCodeId('S'),
					'doctype' => $doctype,
					'instockdate' => $instockdate,
					'firstdate' => date("Y-m-d", strtotime('' . $instockdate . '+30 day')),
					'shipname' => $shipname,
					'customer_sysno' => $customer_sysno,
					'customername' => $customername,
					'goods_sysno' => $goods_sysno,
					'goodsname' => $goodsname,
					'goods_quality_sysno' => $goods_quality_sysno,
					'goodsqualityname' => $qualityname,
					'storagetank_sysno' => $storagetank_sysno,
					'instockqty' => $instockqty,
					'outstockqty' => $instockqty-$stockqty,
					'stockqty' => $stockqty,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
					'financedate' => date("Y-m-d", strtotime('' . $instockdate . '+30 day')),
					'goodsnature' => $goodsnature,
					'firstfrom_sysno' => $stockin_sysno,
					'firstfrom_no' => $stockininfo['stockinno'],
					'contract_sysno' => $contract_sysno,
					'ullage' => $ullage,
				);
				$stock_sysno = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
                if (!$stock_sysno) {
                    $count_inserterror++;#sql插入错误
                    $stock_sysno = 0;#避免更新错误
					continue;
                }
                #更新明细表库存字段
                $update_stockindetail = array(
                	'stock_sysno' => $stock_sysno,
                );
                $this->dbh->update(DB_PREFIX.'doc_stock_in_detail', $update_stockindetail, 'sysno=' . intval($stockindetail_sysno));
			}elseif($value[0]==''){
				$sql = "SELECT sysno,stockqty,instockqty FROM ".DB_PREFIX."storage_stock WHERE shipname = '".$shipname."' and instockdate = '".$instockdate."' and goods_sysno = $goods_sysno and customer_sysno = $customer_sysno and storagetank_sysno = $storagetank_sysno and  firstfrom_sysno = $stockin_sysno AND iscurrent = 1";
				$stock = $this ->dbh ->select_row($sql);
				if(!empty($stock))
				{
					#多明细表更新库存字段
					$update_stock = array(
							'instockqty' => $stock['instockqty'] + $instockqty,
							'stockqty' => $stock['stockqty'] + $stockqty,
					);
					$this->dbh->update(DB_PREFIX.'storage_stock', $update_stock, 'sysno=' . intval($stock['sysno']));
				}
				else
				{
					#多明细新增库存
					$stockinfo = array(
						'stockno' => COMMON::getCodeId('S'),
						'doctype' => $doctype,
						'instockdate' => $instockdate,
						'firstdate' => date("Y-m-d", strtotime('' . $instockdate . '+30 day')),
						'shipname' => $shipname,
						'customer_sysno' => $customer_sysno,
						'customername' => $customername,
						'goods_sysno' => $goods_sysno,
						'goodsname' => $goodsname,
						'goods_quality_sysno' => $goods_quality_sysno,
						'goodsqualityname' => $qualityname,
						'storagetank_sysno' => $storagetank_sysno,
						'instockqty' => $instockqty,
						'outstockqty' => $instockqty-$stockqty,
						'stockqty' => $stockqty,
						'created_at' => '=NOW()',
						'updated_at' => '=NOW()',
						'financedate' => date("Y-m-d", strtotime('' . $instockdate . '+30 day')),
						'goodsnature' => $goodsnature,
						'firstfrom_sysno' => $stockin_sysno,
						'firstfrom_no' => $stockininfo['stockinno'],
						'contract_sysno' => $contract_sysno,
						'ullage' => $ullage,
					);
					$stock_sysno = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
	                if (!$stock_sysno) {
	                    $count_inserterror++;#sql插入错误
	                    $stock_sysno = 0;#避免更新错误
						continue;
	                }
	                #更新明细表库存字段
	                $update_stockindetail = array(
	                	'stock_sysno' => $stock_sysno,
	                );
				}
			}

			#插入--货物记录表
			if($stockqty>0)
			{
				if($stockintype==1)
				{
					$doc_type = 1;
				}
				elseif($stockintype==2)
				{
					$doc_type = 2;
				}
				elseif($stockintype==3)
				{
					$doc_type = 11;
				}
				else
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
				$goodsrecordinfo = array(
					'doc_time' => $instockdate,
					'shipname' => $shipname,
					'goods_sysno' => $goods_sysno,
					'goodsname' => $goodsname,
					'storagetank_sysno' => $storagetank_sysno,
					'storagetankname' => $storagetankname,
					'customer_sysno' => $customer_sysno,
					'customername' => $customername,
					'beqty' => $stockqty+$ullage,
					'stockin_sysno' => $stockin_sysno,
					'stockinno' => $stockininfo['stockinno'],
					'doc_sysno' => $stockintype==2 ? $poundsin_sysno : $stockin_sysno,
					'docno' => $stockintype==2 ? $poundsininfo['poundsinno'] : $stockininfo['stockinno'],
					'doc_type' => $doc_type,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
					'tobeqty' => $stockqty+$ullage,
					'stock_sysno' => $stock_sysno,
					'ullage' => $ullage,
					'takegoodscompany' => '初始化公司',
					'goodsnature' => $goodsnature,
					'takegoodsno' => '初始化提单号',
					'carid' => '初始化车牌',
				);
				$this->dbh->insert(DB_PREFIX.'doc_goods_record_log', $goodsrecordinfo);
			}

			#插入--储罐记录表
			if($stockqty>0)
			{
				if($stockintype==1)
				{
					$doc_type = 1;
				}
				elseif($stockintype==2)
				{
					$doc_type = 2;
				}
				elseif($stockintype==3)
				{
					$doc_type = 13;
				}
				else
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
				$storagetankloginfo = array(
					'storagetank_sysno' => $storagetank_sysno,
					'beqty' => $stockqty,
					'doc_sysno' => $stockintype==2 ? $poundsin_sysno : $stockin_sysno,
					'docno' => $stockintype==2 ? $poundsininfo['poundsinno'] : $stockininfo['stockinno'],
					'doctype' => $doc_type,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
				);
				$this->dbh->insert(DB_PREFIX.'doc_storagetank_log', $storagetankloginfo);
			}

			#更新储罐资料数据
			if($stockqty>0)
			{
				$sql = "select * from ".DB_PREFIX."base_storagetank where sysno =" . intval($storagetank_sysno);
            	$storagetankinfo = $this->dbh->select_row($sql);
				$update_storagetank = array(
                	'tank_stockqty' => $storagetankinfo['tank_stockqty']+$stockqty,
                );
                $this->dbh->update(DB_PREFIX.'base_storagetank', $update_storagetank, 'sysno=' . intval($storagetank_sysno));
			}

			#插入--费用单表
			if($costnum>0)
			{
				$costinfo = array(
					'costno' => COMMON::getCodeId('F'),
					'costdate' => date("Y-m-d"),
					'costdateend' => date("Y-m-d"),
					'isexceedfirst' => 0,
					'isstoragetank' => 0,
					'storagetank_sysno' => $storagetank_sysno,
					'shipname' => $shipname,
					'goods_sysno' => $goods_sysno,
					'goods_quality_sysno' => $goods_quality_sysno,
					'goodsnature' => $goodsnature,
					'customer_sysno' => $customer_sysno,
					'customer_name' => $customername,
					'costtype' => 0,
					'costname' => '初始化欠费',
					'coststatus' => 2,
					'stock_sysno' => $stock_sysno,
					'instock_sysno' => $stockin_sysno,
					'stockinno' => $stockinfo['stockinno'],
					'contract_sysno' => $contract_sysno,
					'contract_no' => $contractnodisplay,
					'storagetankname' => $storagetankname,
					'goodsname' => $goodsname,
					'qualityname' => $qualityname,
					'unitname' => '吨',
					'ullage' => $ullage,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
					'totalprice' => $costnum,
					'costqty' => $stockqty,
					'unitprice' => $costnum/$stockqty,
				);
				$this->dbh->insert(DB_PREFIX.'doc_finance_cost_detail', $costinfo);
			}

			#插入--收款单
			$sql = "select sysno from ".DB_PREFIX."base_settlement where 1 limit 1";
			$base_settlement_sysno = $this->dbh->select_one($sql);
			if($costnum<0)
			{
				$costinfo = array(
					'receivableno' => COMMON::getCodeId('R'),
					'receivabledate' => date("Y-m-d"),
					'customer_sysno' => $customer_sysno,
					'customername' => $customername,
					'base_company_sysno' => $customer_sysno,
					'base_companyname' => $customername,
					'base_settlement_sysno' => $base_settlement_sysno ? $base_settlement_sysno : 1,
					'invoice_company_sysno' => $customer_sysno,
					'invoice_companyname' => $customername,
					'goodsname' => $goodsname,
					'costreceivable' => abs($costnum),
					'receivablestatus' => 4,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
				);
				$this->dbh->insert(DB_PREFIX.'doc_finance_receivable', $costinfo);

				#更新客户资料收款余额
				$sql = "select * from ".DB_PREFIX."customer where sysno =" . intval($customer_sysno);
            	$customerinfo = $this->dbh->select_row($sql);
				$update_customer = array(
					'receivablecost' => $customerinfo['receivablecost'] - $costnum,
				);
				$this->dbh->update(DB_PREFIX.'customer', $update_customer, 'sysno=' . intval($customer_sysno));
			}
		}
		return true;
	}

	private function InitDataStocktrans($data = array())
	{
		foreach ($data as $key => $value) {
			#获取客户Sysno
			if($value[0]!="")
			{
				$customername = $value[0];
				$oldcustomername = $customername;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."customer where isdel=0 and status=1 and customername='".$value[0]."' ";
				$customer_sysno = $this->dbh->select_one($sql);
				if(!$customer_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}
			else
			{
				$customername = $oldcustomername;
				#说明是多明细
			}

			#获取品名Sysno
			if($value[1]!="")
			{
				$goodsname = $value[1];
				$oldgoodsname = $goodsname;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."base_goods where isdel=0 and status=1 and goodsname='".$value[1]."' ";
				$goods_sysno = $this->dbh->select_one($sql);
				if(!$goods_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}
			else
			{
				$goodsname = $oldgoodsname;
				#说明是多明细
			}

			#获取储罐Sysno
			if($value[2]!="")
			{
				$storagetankname = $value[2];
				$oldstoragetankname = $storagetankname;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."base_storagetank where isdel=0 and status=1 and storagetankname='".$value[2]."' ";
				$storagetank_sysno = $this->dbh->select_one($sql);
				if(!$storagetank_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}
			else
			{
				$storagetankname = $oldstoragetankname;
				#说明是多明细
			}

			#获取货物性质
			switch ($value[3]) {
				case '保税':
					$goodsnature = 1;
					break;
				case '外贸':
					$goodsnature = 2;
					break;
				case '外贸':
					$goodsnature = 2;
					break;
				case '内贸':
					$goodsnature = 4;
					break;
				default:
					break;
			}

			#获取规格
			if($value[4]!="")
			{
				$qualityname = $value[4];
				$oldqualityname = $qualityname;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."base_goods_quality where isdel=0 and status=1 and qualityname='".$value[4]."' ";
				$goods_quality_sysno = $this->dbh->select_one($sql);
				if(!$goods_quality_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}
			else
			{
				$qualityname = $oldqualityname;
				#说明是多明细
			}

			#获取船名
			if($value[5]!="")
			{
				$shipname = $value[5];
				$oldshipname = $shipname;
			}
			else
			{
				$shipname = $oldshipname;
				#说明是多明细
			}

			#获取入库方式
			if($shipname=="槽车入库")
			{
				$stockintype = 2;
			}
			elseif($shipname=="管输")
			{
				$stockintype = 3;
			}
			else
			{
				$stockintype = 1;
			}

			#获取入库时间
			if($value['6']!="")
			{
				$instockdate = date("Y-m-d",strtotime($value[6]));
				$oldinstockdate = $instockdate;
			}
			else
			{
				$instockdate = $oldinstockdate;
				#说明是多明细
			}

			#获取提单数量
			if($value[7]!="")
			{
				$takegoodsnum = $value[7];
				$oldtakegoodsnum = $takegoodsnum;
			}
			else
			{
				$takegoodsnum = $oldtakegoodsnum;
				#说明是多明细
			}

			#获取入库数量
			$instockqty = $value[8] ? $value[8] : 0;

			#获取转让时间作为入库时间
			if($value[9]!="")
			{
				$instockdate = date("Y-m-d",strtotime($value[9]));
			}

			#获取货转数量作为入库数量
			$instockqty = $value[10] ? $value[10] : 0;

			#获取开始计费时间
			if($value[11]!="")
			{
				$financedate = date("Y-m-d",strtotime($value[11]));
			}
			
			#获取免仓天数
			$financeday = $value[12] ? $value[12] : 0;

			#获取合同编号Sysno
			if($value[13]!="")
			{
				$contractnodisplay = $value[13];
				$oldcontractnodisplay =$contractnodisplay;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."doc_contract where isdel=0 and status=1 and contractnodisplay='".$value[13]."' ";
				$contract_sysno = $this->dbh->select_one($sql);
				if(!$contract_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}
			else
			{
				$contractnodisplay = $oldcontractnodisplay;
				#说明是多明细
			}

			#获取结存量
			$stockqty = $value[15] ? $value[15] : 0;

			#获取损耗量
			$ullage = $value[16] ? $value[16] : 0;	
			
			#获取欠费
			$costnum = floatval($value[17]) ? floatval($value[17]) : 0;

			#插入--入库单主表
			if($value[0]!='')
			{
				if($stockintype==1)
				{
					$pre_code = 'A2';
				}
				elseif($stockintype==2)
				{
					$pre_code = 'B2';
				}
				elseif($stockintype==3)
				{
					$pre_code = 'R2';
				}
				else
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
				$stockininfo = array(
					'stockintype'=> $stockintype,
					'stockinno' => COMMON::getCodeId($pre_code),
					'stockindate' => $instockdate,
					'customer_sysno' => $customer_sysno,
					'customername' => $customername,
					'contract_sysno' => $contract_sysno,
					'contractno' => $contractnodisplay,
					'stockinstatus' => 4,
					'takegoodsnum' => $takegoodsnum,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
					'release_num' => $stockqty,
				);
				#入库单主表id
				$stockin_sysno = $this->dbh->insert(DB_PREFIX.'doc_stock_in', $stockininfo);
                if (!$stockin_sysno) {
                    $count_inserterror++;#sql插入错误
                    $stockin_sysno = 0;#避免明细表插入错误
					continue;
                }
			}
			else
			{
				#有明细累加入库单总量
				$sql = "update ".DB_PREFIX."doc_stock_in SET takegoodsnum=takegoodsnum+{$takegoodsnum},release_num=release_num+{$stockqty} where sysno=".intval($stockin_sysno);
				$this->dbh->exe($sql);
			}
			#插入--入库单明细表
			if($stockin_sysno)
			{
				$stockindetailinfo = array(
					'stockin_sysno' => $stockin_sysno,
					'goods_sysno' => $goods_sysno,
					'goods_quality_sysno' => $goods_quality_sysno,
					'goodsnature' => $goodsnature,
					'goodsreceiptdate' => $instockdate,
					'tobeqty' => $instockqty,
					'bussinesscheckqty' => $stockqty,
					'beqty' => $stockqty,
					'storagetank_sysno' => $storagetank_sysno,
					'shipname' => $shipname,
					'goodsname' => $goodsname,
					'qualityname' => $qualityname,
					'unitname' => '吨',
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
				);

				$stockindetail_sysno = $this->dbh->insert(DB_PREFIX.'doc_stock_in_detail', $stockindetailinfo);
                if (!$stockindetail_sysno) {
                    $count_inserterror++;#sql插入错误
                    $stockindetail_sysno = 0;#避免磅码单表插入错误
					continue;
                }
                if($stockintype==2)
                {
                	#车磅码单
                    $poundsininfo = array(
                    	'poundsinno' => COMMON::getCodeId('B3'),
                    	'stockin_sysno' => $stockin_sysno,
                    	'stockinno' => $stockininfo['stockinno'],
                    	'stockindetail_sysno' => $stockindetail_sysno,
                    	'loadometer' => '50T',
                    	'storagetank_sysno' => $storagetank_sysno,
                    	'storagetankname' => $storagetankname,
                    	'carid' => '初始化车牌',
                    	'carname' => '初始化司机',
                    	'customername' => $customername,
                    	'goodsname' => $goodsname,
                    	'poundsinstatus' => 4,
                    	'beqty' => $stockqty*1000,
                    	'created_at' => '=NOW()',
						'updated_at' => '=NOW()',
                    );

                    $poundsin_sysno = $this->dbh->insert(DB_PREFIX.'doc_pounds_in', $poundsininfo);
                }
                
			}

			#插入--报关单表
			if($declareqty)
			{
				$stockindeclareinfo = array(
					'stockin_sysno'=> $stockin_sysno,
					'goods_sysno' => $goods_sysno,
					'goodsname' => $goodsname,
					'bussinesscheckqty' => $stockqty,
					'storagetank_sysno' => $storagetank_sysno,
					'storagetankname' => $storagetankname,
					'release_num' => $declareqty,
					'customer_sysno' => $customer_sysno,
					'customername' => $customername,
					'takegoodsnum' => $takegoodsnum,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
				);

				$this->dbh->insert(DB_PREFIX.'doc_stock_in_declare', $stockindeclareinfo);
			}

			#插入--库存记录表
			if($value[0]!='')
			{
				if($stockintype==1)
				{
					$doctype = 1;
				}
				elseif($stockintype==2)
				{
					$doctype = 2;
				}
				elseif($stockintype==3)
				{
					$doctype = 4;
				}
				else
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
				$stockinfo = array(
					'stockno' => COMMON::getCodeId('S'),
					'doctype' => $doctype,
					'instockdate' => $instockdate,
					'firstdate' => date("Y-m-d", strtotime('' . $instockdate . '+30 day')),
					'shipname' => $shipname,
					'customer_sysno' => $customer_sysno,
					'customername' => $customername,
					'goods_sysno' => $goods_sysno,
					'goodsname' => $goodsname,
					'goods_quality_sysno' => $goods_quality_sysno,
					'goodsqualityname' => $qualityname,
					'storagetank_sysno' => $storagetank_sysno,
					'instockqty' => $instockqty,
					'outstockqty' => $instockqty-$stockqty,
					'stockqty' => $stockqty,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
					'financedate' => date("Y-m-d", strtotime('' . $instockdate . '+30 day')),
					'goodsnature' => $goodsnature,
					'firstfrom_sysno' => $stockin_sysno,
					'firstfrom_no' => $stockininfo['stockinno'],
					'contract_sysno' => $contract_sysno,
					'ullage' => $ullage,
				);
				$stock_sysno = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
                if (!$stock_sysno) {
                    $count_inserterror++;#sql插入错误
                    $stock_sysno = 0;#避免更新错误
					continue;
                }
                #更新明细表库存字段
                $update_stockindetail = array(
                	'stock_sysno' => $stock_sysno,
                );
                $this->dbh->update(DB_PREFIX.'doc_stock_in_detail', $update_stockindetail, 'sysno=' . intval($stockindetail_sysno));
			}elseif($value[0]==''){
				$sql = "SELECT sysno,stockqty,instockqty FROM ".DB_PREFIX."storage_stock WHERE shipname = '".$shipname."' and goods_sysno = $goods_sysno and customer_sysno = $customer_sysno and storagetank_sysno = $storagetank_sysno and  firstfrom_sysno = $stockin_sysno AND iscurrent = 1";
				$stock = $this ->dbh ->select_row($sql);
				if(!empty($stock))
				{
					#多明细表更新库存字段
					$update_stock = array(
							'instockqty' => $stock['instockqty'] + $instockqty,
							'stockqty' => $stock['stockqty'] + $stockqty,
					);
					$this->dbh->update(DB_PREFIX.'storage_stock', $update_stock, 'sysno=' . intval($stock['sysno']));
				}
				else
				{
					#多明细新增库存
					$stockinfo = array(
						'stockno' => COMMON::getCodeId('S'),
						'doctype' => $doctype,
						'instockdate' => $instockdate,
						'firstdate' => date("Y-m-d", strtotime('' . $instockdate . '+30 day')),
						'shipname' => $shipname,
						'customer_sysno' => $customer_sysno,
						'customername' => $customername,
						'goods_sysno' => $goods_sysno,
						'goodsname' => $goodsname,
						'goods_quality_sysno' => $goods_quality_sysno,
						'goodsqualityname' => $qualityname,
						'storagetank_sysno' => $storagetank_sysno,
						'instockqty' => $instockqty,
						'outstockqty' => $instockqty-$stockqty,
						'stockqty' => $stockqty,
						'created_at' => '=NOW()',
						'updated_at' => '=NOW()',
						'financedate' => date("Y-m-d", strtotime('' . $instockdate . '+30 day')),
						'goodsnature' => $goodsnature,
						'firstfrom_sysno' => $stockin_sysno,
						'firstfrom_no' => $stockininfo['stockinno'],
						'contract_sysno' => $contract_sysno,
						'ullage' => $ullage,
					);
					$stock_sysno = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
	                if (!$stock_sysno) {
	                    $count_inserterror++;#sql插入错误
	                    $stock_sysno = 0;#避免更新错误
						continue;
	                }
	                #更新明细表库存字段
	                $update_stockindetail = array(
	                	'stock_sysno' => $stock_sysno,
	                );
				}
			}

			#插入--货物记录表
			if($stockqty>0)
			{
				if($stockintype==1)
				{
					$doc_type = 1;
				}
				elseif($stockintype==2)
				{
					$doc_type = 2;
				}
				elseif($stockintype==3)
				{
					$doc_type = 11;
				}
				else
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
				$goodsrecordinfo = array(
					'doc_time' => $instockdate,
					'shipname' => $shipname,
					'goods_sysno' => $goods_sysno,
					'goodsname' => $goodsname,
					'storagetank_sysno' => $storagetank_sysno,
					'storagetankname' => $storagetankname,
					'customer_sysno' => $customer_sysno,
					'customername' => $customername,
					'beqty' => $stockqty+$ullage,
					'stockin_sysno' => $stockin_sysno,
					'stockinno' => $stockininfo['stockinno'],
					'doc_sysno' => $stockintype==2 ? $poundsin_sysno : $stockin_sysno,
					'docno' => $stockintype==2 ? $poundsininfo['poundsinno'] : $stockininfo['stockinno'],
					'doc_type' => $doc_type,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
					'tobeqty' => $stockqty+$ullage,
					'stock_sysno' => $stock_sysno,
					'ullage' => $ullage,
					'takegoodscompany' => '初始化公司',
					'goodsnature' => $goodsnature,
					'takegoodsno' => '初始化提单号',
					'carid' => '初始化车牌',
				);
				$this->dbh->insert(DB_PREFIX.'doc_goods_record_log', $goodsrecordinfo);
			}

			#插入--储罐记录表
			if($stockqty>0)
			{
				if($stockintype==1)
				{
					$doc_type = 1;
				}
				elseif($stockintype==2)
				{
					$doc_type = 2;
				}
				elseif($stockintype==3)
				{
					$doc_type = 13;
				}
				else
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
				$storagetankloginfo = array(
					'storagetank_sysno' => $storagetank_sysno,
					'beqty' => $stockqty,
					'doc_sysno' => $stockintype==2 ? $poundsin_sysno : $stockin_sysno,
					'docno' => $stockintype==2 ? $poundsininfo['poundsinno'] : $stockininfo['stockinno'],
					'doctype' => $doc_type,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
				);
				$this->dbh->insert(DB_PREFIX.'doc_storagetank_log', $storagetankloginfo);
			}

			#更新储罐资料数据
			if($stockqty>0)
			{
				$sql = "select * from ".DB_PREFIX."base_storagetank where sysno =" . intval($storagetank_sysno);
            	$storagetankinfo = $this->dbh->select_row($sql);
				$update_storagetank = array(
                	'tank_stockqty' => $storagetankinfo['tank_stockqty']+$stockqty,
                );
                $this->dbh->update(DB_PREFIX.'base_storagetank', $update_storagetank, 'sysno=' . intval($storagetank_sysno));
			}

			#插入--费用单表
			if($costnum>0)
			{
				$costinfo = array(
					'costno' => COMMON::getCodeId('F'),
					'costdate' => date("Y-m-d"),
					'costdateend' => date("Y-m-d"),
					'isexceedfirst' => 0,
					'isstoragetank' => 0,
					'storagetank_sysno' => $storagetank_sysno,
					'shipname' => $shipname,
					'goods_sysno' => $goods_sysno,
					'goods_quality_sysno' => $goods_quality_sysno,
					'goodsnature' => $goodsnature,
					'customer_sysno' => $customer_sysno,
					'customer_name' => $customername,
					'costtype' => 0,
					'costname' => '初始化欠费',
					'coststatus' => 2,
					'stock_sysno' => $stock_sysno,
					'instock_sysno' => $stockin_sysno,
					'stockinno' => $stockinfo['stockinno'],
					'contract_sysno' => $contract_sysno,
					'contract_no' => $contractnodisplay,
					'storagetankname' => $storagetankname,
					'goodsname' => $goodsname,
					'qualityname' => $qualityname,
					'unitname' => '吨',
					'ullage' => $ullage,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
					'totalprice' => $costnum,
					'costqty' => $stockqty,
					'unitprice' => $costnum/$stockqty,
				);
				$this->dbh->insert(DB_PREFIX.'doc_finance_cost_detail', $costinfo);
			}

			#插入--收款单
			$sql = "select sysno from ".DB_PREFIX."base_settlement where 1 limit 1";
			$base_settlement_sysno = $this->dbh->select_one($sql);
			if($costnum<0)
			{
				$costinfo = array(
					'receivableno' => COMMON::getCodeId('R'),
					'receivabledate' => date("Y-m-d"),
					'customer_sysno' => $customer_sysno,
					'customername' => $customername,
					'base_company_sysno' => $customer_sysno,
					'base_companyname' => $customername,
					'base_settlement_sysno' => $base_settlement_sysno ? $base_settlement_sysno : 1,
					'invoice_company_sysno' => $customer_sysno,
					'invoice_companyname' => $customername,
					'goodsname' => $goodsname,
					'costreceivable' => abs($costnum),
					'receivablestatus' => 4,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
				);
				$this->dbh->insert(DB_PREFIX.'doc_finance_receivable', $costinfo);

				#更新客户资料收款余额
				$sql = "select * from ".DB_PREFIX."customer where sysno =" . intval($customer_sysno);
            	$customerinfo = $this->dbh->select_row($sql);
				$update_customer = array(
					'receivablecost' => $customerinfo['receivablecost'] - $costnum,
				);
				$this->dbh->update(DB_PREFIX.'customer', $update_customer, 'sysno=' . intval($customer_sysno));
			}
		}
		return true;
	}

	private function InitDataIntroduction($data = array())
	{
		foreach ($data as $key => $value) {
			#获取原始客户Sysno
			if($value[3]!="")
			{
				$customername = $value[3];
				$oldcustomername = $customername;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."customer where isdel=0 and status=1 and customername='".$value[3]."' ";
				$customer_sysno = $this->dbh->select_one($sql);
				if(!$customer_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}
			else
			{
				$customername = $oldcustomername;
				#说明是多明细
			}

			#获取货权转移库存客户Sysno
			if($value[1]!="")
			{
				$trans_customername = $value[1];
				$oldtrans_customername = $trans_customername;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."customer where isdel=0 and status=1 and customername='".$value[1]."' ";
				$trans_customer_sysno = $this->dbh->select_one($sql);
				if(!$trans_customer_sysno)
				{
//					$count_error++;#无法找到数据记录导入错误数量
//					continue;
				}
			}
			else
			{
				$trans_customername = $oldtrans_customername;
				#说明是多明细
			}

			#获取转让方Sysno
			if($value[2]!="")
			{
				$sale_customername = $value[2];
				$oldsale_customername = $sale_customername;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."customer where isdel=0 and status=1 and customername='".$value[2]."' ";
				$sale_customer_sysno = $this->dbh->select_one($sql);
				if(!$sale_customer_sysno)
				{
					//$count_error++;#无法找到数据记录导入错误数量
					//continue;
				}
			}
			else
			{
				$sale_customername = $oldsale_customername;
				#说明是多明细
			}

			#获取受让方Sysno
			if($value[3]!="")
			{
				$buy_customername = $value[3];
				$oldbuy_customername = $buy_customername;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."customer where isdel=0 and status=1 and customername='".$value[3]."' ";
				$buy_customer_sysno = $this->dbh->select_one($sql);
				if(!$buy_customer_sysno)
				{
					//$count_error++;#无法找到数据记录导入错误数量
					//continue;
				}
			}
			else
			{
				$buy_customername = $oldbuy_customername;
				#说明是多明细
			}

			#获取提货单号
			if($value[4]!="")
			{
				$takegoodsno = $value[4];
			}

			#获取提单类型
			if($value[5]!="")
			{
				switch ($value[5]) {
					case '可撤销':
							$introductiontype = 1;
						break;
					case '可撤销':
							$introductiontype = 2;
						break;
					
					default:
						# code...
						break;
				}
			}

			#费用承担方
			if($value[6]!="")
			{
				$fin_customername = $value[6];
				$oldfin_customername = $fin_customername;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."customer where isdel=0 and status=1 and customername='".$value[6]."' ";
				$fin_customer_sysno = $this->dbh->select_one($sql);
				if(!$fin_customer_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}
			elseif($value[6]=="\\")
			{
				#如果没有费用承担方，用开单客户最为替换
				$fin_customername = $trans_customername;
				$oldfin_customername = $trans_customername;
				$fin_customer_sysno = $trans_customer_sysno;
			}
			else
			{
				$fin_customername = $oldfin_customername;
				#说明是多明细
			}

			#获取提货区间开始
			if($value[11]!="")
			{
				$receivestart = date("Y-m-d",strtotime($value[11]));
			}
			#获取提货区间结束
			if($value[12]!="")
			{
				$receiveend = date("Y-m-d",strtotime($value[12]));
			}

			#获取超期费用
			if($value[13]!="")
			{
				$lastamount = $value[13] ? (float)$value[13] : 0;
			}

			#获取超期损耗率
			if($value[14]!="")
			{
				$lossrate = $value[14] ? (float)$value[14] : 0;
			}

			#获取品名Sysno
			if($value[15]!="")
			{
				$goodsname = $value[15];
				$oldgoodsname = $goodsname;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."base_goods where isdel=0 and status=1 and goodsname='".$value[15]."' ";
				$goods_sysno = $this->dbh->select_one($sql);
				if(!$goods_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}
			else
			{
				$goodsname = $oldgoodsname;
				#说明是多明细
			}

			#获取储罐Sysno
			if($value[16]!="")
			{

				$storagetankname = $value[16];
				$oldstoragetankname = $storagetankname;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."base_storagetank where isdel=0 and status=1 and storagetankname='".$value[16]."' ";
				$storagetank_sysno = $this->dbh->select_one($sql);
				if(!$storagetank_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}
			else
			{
				$storagetankname = $oldstoragetankname;
				#说明是多明细
			}

			#获取货物性质
			switch ($value[17]) {
				case '保税':
					$goodsnature = 1;
					break;
				case '外贸':
					$goodsnature = 2;
					break;
				case '外贸':
					$goodsnature = 2;
					break;
				case '内贸':
					$goodsnature = 4;
					break;
				default:
					break;
			}

			#获取规格
			if($value[18]!="")
			{
				$qualityname = $value[18];
				$oldqualityname = $qualityname;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."base_goods_quality where isdel=0 and status=1 and qualityname='".$value[18]."' ";
				$goods_quality_sysno = $this->dbh->select_one($sql);
				if(!$goods_quality_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}
			else
			{
				$qualityname = $oldqualityname;
				#说明是多明细
			}

			#获取船名
			if($value[19]!="")
			{
				$shipname = $value[19];
				$oldshipname = $shipname;
			}
			else
			{
				$shipname = $oldshipname;
				#说明是多明细
			}

			#获取入库方式
			if($shipname=="槽车入库")
			{
				$stockintype = 2;
			}
			elseif($shipname=="管输")
			{
				$stockintype = 3;
			}
			else
			{
				$stockintype = 1;
			}

			#获取入库时间
			// if($value[20]!="")
			// {
			// 	$instockdate = date("Y-m-d",strtotime($value[20]));
			// 	$oldinstockdate = $instockdate;
			// }
			// else
			// {
			// 	$instockdate = $oldinstockdate;
			// 	#说明是多明细
			// }
			if($receiveend<date("Y-m-d"))
			{
				$instockdate = $receiveend;//提单类型的入库改成提单结束时间
			}
			else
			{
				$instockdate = date("Y-m-d");
			}

			#获取入库数量
			$instockqty = $value[22] ? $value[22] : 0;

			$freecostdate = $receiveend - $receivestart>0 ? $receiveend - $receivestart : 0;


			if($fin_customername==$sale_customername)
			{
				$costtype = 1;
			}
			elseif($fin_customername==$buy_customername)
			{
				$costtype = 2;
			}

			#获取剩余提单量
			$stockqty = $value[22] ? $value[22] : 0;

			#获取损耗量
			$ullage = $value[23] ? $value[23] : 0;

			#获取欠费
			$costnum = floatval($value[24]) ? floatval($value[24]) : 0;

			#获取合同编号Sysno
			if($value[25]!="")
			{
				$contractnodisplay = $value[25];
				$oldcontractnodisplay = $contractnodisplay;
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."doc_contract where isdel=0 and status=1 and contractnodisplay='".$value[25]."' ";
				$contract_sysno = $this->dbh->select_one($sql);
				if(!$contract_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}
			else
			{
				$contractnodisplay = $oldcontractnodisplay;
				#说明是多明细
			}

			#序号
			$sysno = $value[26] ? $value[26] : 0;

			#父级序号
			$father_sysno = $value[27] ? $value[27] : 0;

			#插入--入库单主表
			if($stockqty>0)
			{
				if($stockintype==1)
				{
					$pre_code = 'A2';
				}
				elseif($stockintype==2)
				{
					$pre_code = 'B2';
				}
				elseif($stockintype==3)
				{
					$pre_code = 'R2';
				}
				else
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
				$stockininfo = array(
					'stockintype'=> $stockintype,
					'stockinno' => COMMON::getCodeId($pre_code),
					'stockindate' => $instockdate,
					'customer_sysno' => $customer_sysno,
					'customername' => $customername,
					'contract_sysno' => $contract_sysno,
					'contractno' => $contractnodisplay,
					'stockinstatus' => 4,
					'takegoodsnum' => $stockqty,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
					'release_num' => $stockqty,
				);
				#入库单主表id
				$stockin_sysno = $this->dbh->insert(DB_PREFIX.'doc_stock_in', $stockininfo);
                if (!$stockin_sysno) {
                    $count_inserterror++;#sql插入错误
                    $stockin_sysno = 0;#避免明细表插入错误
					continue;
                }
			}
			#插入--入库单明细表
			if($stockin_sysno)
			{
				$stockindetailinfo = array(
					'stockin_sysno' => $stockin_sysno,
					'goods_sysno' => $goods_sysno,
					'goods_quality_sysno' => $goods_quality_sysno,
					'goodsnature' => $goodsnature,
					'goodsreceiptdate' => $instockdate,
					'tobeqty' => $instockqty,
					'bussinesscheckqty' => $stockqty,
					'beqty' => $stockqty,
					'storagetank_sysno' => $storagetank_sysno,
					'shipname' => $shipname,
					'goodsname' => $goodsname,
					'qualityname' => $qualityname,
					'unitname' => '吨',
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
				);

				$stockindetail_sysno = $this->dbh->insert(DB_PREFIX.'doc_stock_in_detail', $stockindetailinfo);
                if (!$stockindetail_sysno) {
                    $count_inserterror++;#sql插入错误
                    $stockindetail_sysno = 0;#避免磅码单表插入错误
					continue;
                }
                if($stockintype==2)
                {
                	#车磅码单
                    $poundsininfo = array(
                    	'poundsinno' => COMMON::getCodeId('B3'),
                    	'stockin_sysno' => $stockin_sysno,
                    	'stockinno' => $stockininfo['stockinno'],
                    	'stockindetail_sysno' => $stockindetail_sysno,
                    	'loadometer' => '50T',
                    	'storagetank_sysno' => $storagetank_sysno,
                    	'storagetankname' => $storagetankname,
                    	'carid' => '初始化车牌',
                    	'carname' => '初始化司机',
                    	'customername' => $customername,
                    	'goodsname' => $goodsname,
                    	'poundsinstatus' => 4,
                    	'beqty' => $stockqty*1000,
                    	'created_at' => '=NOW()',
						'updated_at' => '=NOW()',
                    );

                    $poundsin_sysno = $this->dbh->insert(DB_PREFIX.'doc_pounds_in', $poundsininfo);
                }
                
			}

			#插入--报关单表
			if($declareqty)
			{
				$stockindeclareinfo = array(
					'stockin_sysno'=> $stockin_sysno,
					'goods_sysno' => $goods_sysno,
					'goodsname' => $goodsname,
					'bussinesscheckqty' => $stockqty,
					'storagetank_sysno' => $storagetank_sysno,
					'storagetankname' => $storagetankname,
					'release_num' => $declareqty,
					'customer_sysno' => $customer_sysno,
					'customername' => $customername,
					'takegoodsnum' => $takegoodsnum,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
				);

				$this->dbh->insert(DB_PREFIX.'doc_stock_in_declare', $stockindeclareinfo);
			}

			#插入--库存记录表
			if($stockqty>0)
			{
				if($stockintype==1)
				{
					$doctype = 1;
				}
				elseif($stockintype==2)
				{
					$doctype = 2;
				}
				elseif($stockintype==3)
				{
					$doctype = 4;
				}
				else
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
				$stockinfo = array(
					'stockno' => COMMON::getCodeId('S'),
					'doctype' => $doctype,
					'instockdate' => $instockdate,
					'firstdate' => date("Y-m-d", strtotime('' . $receiveend . '+1 day')),//提单类型的入库改成提单结束时间
					'shipname' => $shipname,
					'customer_sysno' => $customer_sysno,
					'customername' => $customername,
					'goods_sysno' => $goods_sysno,
					'goodsname' => $goodsname,
					'goods_quality_sysno' => $goods_quality_sysno,
					'goodsqualityname' => $qualityname,
					'storagetank_sysno' => $storagetank_sysno,
					'instockqty' => $instockqty,
					'outstockqty' => $instockqty-$stockqty,
					'stockqty' => $stockqty,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
					'financedate' => date("Y-m-d", strtotime('' . $receiveend . '+1 day')),//提单类型的入库改成提单结束时间
					'goodsnature' => $goodsnature,
					'firstfrom_sysno' => $stockin_sysno,
					'firstfrom_no' => $stockininfo['stockinno'],
					'contract_sysno' => $contract_sysno,
					'ullage' => $ullage,
				);
				$stock_sysno = $this->dbh->insert(DB_PREFIX.'storage_stock', $stockinfo);
                if (!$stock_sysno) {
                    $count_inserterror++;#sql插入错误
                    $stock_sysno = 0;#避免更新错误
					continue;
                }
                #更新明细表库存字段
                $update_stockindetail = array(
                	'stock_sysno' => $stock_sysno,
                );
                $this->dbh->update(DB_PREFIX.'doc_stock_in_detail', $update_stockindetail, 'sysno=' . intval($stockindetail_sysno));
			}

			#插入--货物记录表
			if($stockqty>0)
			{
				if($stockintype==1)
				{
					$doc_type = 1;
				}
				elseif($stockintype==2)
				{
					$doc_type = 2;
				}
				elseif($stockintype==3)
				{
					$doc_type = 11;
				}
				else
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
				$goodsrecordinfo = array(
					'doc_time' => $instockdate,
					'shipname' => $shipname,
					'goods_sysno' => $goods_sysno,
					'goodsname' => $goodsname,
					'storagetank_sysno' => $storagetank_sysno,
					'storagetankname' => $storagetankname,
					'customer_sysno' => $customer_sysno,
					'customername' => $customername,
					'beqty' => $stockqty+$ullage,
					'stockin_sysno' => $stockin_sysno,
					'stockinno' => $stockininfo['stockinno'],
					'doc_sysno' => $stockintype==2 ? $poundsin_sysno : $stockin_sysno,
					'docno' => $stockintype==2 ? $poundsininfo['poundsinno'] : $stockininfo['stockinno'],
					'doc_type' => $doc_type,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
					'tobeqty' => $stockqty+$ullage,
					'stock_sysno' => $stock_sysno,
					'ullage' => $ullage,
					'takegoodscompany' => '初始化公司',
					'goodsnature' => $goodsnature,
					'takegoodsno' => '初始化提单号',
					'carid' => '初始化车牌',
				);
				$this->dbh->insert(DB_PREFIX.'doc_goods_record_log', $goodsrecordinfo);
			}

			#插入--储罐记录表
			if($stockqty>0)
			{
				if($stockintype==1)
				{
					$doc_type = 1;
				}
				elseif($stockintype==2)
				{
					$doc_type = 2;
				}
				elseif($stockintype==3)
				{
					$doc_type = 13;
				}
				else
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
				$storagetankloginfo = array(
					'storagetank_sysno' => $storagetank_sysno,
					'beqty' => $stockqty,
					'doc_sysno' => $stockintype==2 ? $poundsin_sysno : $stockin_sysno,
					'docno' => $stockintype==2 ? $poundsininfo['poundsinno'] : $stockininfo['stockinno'],
					'doctype' => $doc_type,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
				);
				$this->dbh->insert(DB_PREFIX.'doc_storagetank_log', $storagetankloginfo);
			}

			#更新储罐资料数据
			if($stockqty>0)
			{
				$sql = "select * from ".DB_PREFIX."base_storagetank where sysno =" . intval($storagetank_sysno);
            	$storagetankinfo = $this->dbh->select_row($sql);
				$update_storagetank = array(
                	'tank_stockqty' => $storagetankinfo['tank_stockqty']+$stockqty,
                );
                $this->dbh->update(DB_PREFIX.'base_storagetank', $update_storagetank, 'sysno=' . intval($storagetank_sysno));
			}

			#插入--费用单表
			if($costnum>0)
			{
				$costinfo = array(
					'costno' => COMMON::getCodeId('F'),
					'costdate' => date("Y-m-d"),
					'costdateend' => date("Y-m-d"),
					'isexceedfirst' => 0,
					'isstoragetank' => 0,
					'storagetank_sysno' => $storagetank_sysno,
					'shipname' => $shipname,
					'goods_sysno' => $goods_sysno,
					'goods_quality_sysno' => $goods_quality_sysno,
					'goodsnature' => $goodsnature,
					'customer_sysno' => $customer_sysno,
					'customer_name' => $customername,
					'costtype' => 0,
					'costname' => '初始化欠费',
					'coststatus' => 2,
					'stock_sysno' => $stock_sysno,
					'instock_sysno' => $stockin_sysno,
					'stockinno' => $stockinfo['stockinno'],
					'contract_sysno' => $contract_sysno,
					'contract_no' => $contractnodisplay,
					'storagetankname' => $storagetankname,
					'goodsname' => $goodsname,
					'qualityname' => $qualityname,
					'unitname' => '吨',
					'ullage' => $ullage,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
					'totalprice' => $costnum,
					'costqty' => $stockqty,
					'unitprice' => $costnum/$stockqty,
				);
				$this->dbh->insert(DB_PREFIX.'doc_finance_cost_detail', $costinfo);
			}

			#插入--收款单
			$sql = "select sysno from ".DB_PREFIX."base_settlement where 1 limit 1";
			$base_settlement_sysno = $this->dbh->select_one($sql);
			if($costnum<0)
			{
				$costinfo = array(
					'receivableno' => COMMON::getCodeId('R'),
					'receivabledate' => date("Y-m-d"),
					'customer_sysno' => $customer_sysno,
					'customername' => $customername,
					'base_company_sysno' => $customer_sysno,
					'base_companyname' => $customername,
					'base_settlement_sysno' => $base_settlement_sysno ? $base_settlement_sysno : 1,
					'invoice_company_sysno' => $customer_sysno,
					'invoice_companyname' => $customername,
					'goodsname' => $goodsname,
					'costreceivable' => abs($costnum),
					'receivablestatus' => 4,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
				);
				$this->dbh->insert(DB_PREFIX.'doc_finance_receivable', $costinfo);

				#更新客户资料收款余额
				$sql = "select * from ".DB_PREFIX."customer where sysno =" . intval($customer_sysno);
            	$customerinfo = $this->dbh->select_row($sql);
				$update_customer = array(
					'receivablecost' => $customerinfo['receivablecost'] - $costnum,
				);
				$this->dbh->update(DB_PREFIX.'customer', $update_customer, 'sysno=' . intval($customer_sysno));
			}
			
		}
		return true;
	}

	private function InitDataContract($data = array())
	{
		foreach ($data as $key => $value) {
			#获取客户Sysno
			$customername = $value[1];
			if($value[1]!="")
			{
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."customer where isdel=0 and status=1 and customername='".$value[1]."' ";
				$customer_sysno = $this->dbh->select_one($sql);
				if(!$customer_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}

			#获取品名Sysno
			$goodsname = $value[7];
			if($value[7]!="")
			{
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."base_goods where isdel=0 and status=1 and goodsname='".$value[7]."' ";
				$goods_sysno = $this->dbh->select_one($sql);
				if(!$goods_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}

			#获取货物性质
			switch ($value[8]) {
				case '保税':
					$goodsnature = 1;
					break;
				case '外贸':
					$goodsnature = 2;
					break;
				case '外贸':
					$goodsnature = 2;
					break;
				case '内贸':
					$goodsnature = 4;
					break;
				default:
					break;
			}

			#获取规格
			$qualityname = $value[9];
			if($value[9]!="")
			{
				#如果是存在客户则新插入
				$sql = "select sysno from ".DB_PREFIX."base_goods_quality where isdel=0 and status=1 and qualityname='".$value[9]."' ";
				$goods_quality_sysno = $this->dbh->select_one($sql);
				if(!$goods_quality_sysno)
				{
					$count_error++;#无法找到数据记录导入错误数量
					continue;
				}
			}

			#插入--合同主表
			if($customername)
			{
				$contractinfo = array(
					'contractno' => COMMON::getCodeId($pre_code),
					'contractnodisplay' => $value[0],
					'contractdate' => $value[2],
					'customer_id' => $customer_sysno,
					'customername' => $customername,
					'contracttype' => 1,
					'contractstartdate' => $value[2],
					'contractenddate' => $value[3],
					'contractcostdate' => $value[4],
					'contractstatus' => 5,
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
				);
				#入库单主表id
				$contract_sysno = $this->dbh->insert(DB_PREFIX.'doc_contract', $contractinfo);
                if (!$contract_sysno) {
                    $count_inserterror++;#sql插入错误
                    $contract_sysno = 0;#避免明细表插入错误
					continue;
                }
			}
			#插入--合同货品明细表
			if($contract_sysno)
			{
				$contractgoodsinfo = array(
					'contract_sysno' => $contract_sysno,
					'goods_sysno' => $goods_sysno,
					'goods_quality_sysno' => $goods_quality_sysno,
					'goodsnature' => $goodsnature,
					'goodsname' => $goodsname,
					'lastamount' => $value[5],
					'lastlossrate' => $value[6],
					'created_at' => '=NOW()',
					'updated_at' => '=NOW()',
				);

				$contractdetail_sysno = $this->dbh->insert(DB_PREFIX.'doc_contract_goods', $contractgoodsinfo);
                if (!$contractdetail_sysno) {
                    $count_inserterror++;#sql插入错误
                    $contractdetail_sysno = 0;#避免明细表插入错误
					continue;
                }
			}
		}
		return true;
	}
}
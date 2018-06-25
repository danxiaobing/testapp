<?php
/**
 * @Author: Dujiangjiang
 * @Date:   2016-11-15 17:40:32
 * @Last Modified by:   Dujiangjiang
 * @Last Modified time: 2016-11-19 10:49:39
 */
class DepartmentModel {
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
	 * 根据条件检索部门
	 * @return 数组、
	 * @author Dujiangjiang
	 */
	public function searchDepartment($params) {
		$filter = array();
		if (isset($params['bar_no']) && $params['bar_no'] != '') {
			$filter[] = " d.`departmentno` LIKE '%{$params['bar_no']}%' ";
		}
		if (isset($params['bar_name']) && $params['bar_name'] != '') {
			$filter[] = " d.`departmentname` LIKE '%{$params['bar_name']}%' ";
		}
		if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
			$filter[] = " d.`status`='{$params['bar_status']}'";
		}
		if (isset($params['bar_isdel']) && $params['bar_isdel'] != '-100') {
			$filter[] = " d.`isdel`='{$params['bar_isdel']}'";
		}
		$where ='';
		if (1 <= count($filter)) {
			$where .= "WHERE ". implode(' AND ', $filter);
		}

		$sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."system_department` AS d {$where} ";
		$result = $params;

		$result['totalRow'] = $this->dbh->select_one($sql);
		
		$result['list'] = array();
		if ($result['totalRow']) {
			if( isset($params['page'] ) && $params['page'] == false){
				$sql = "SELECT d.*,(SELECT dd.`departmentname` FROM `".DB_PREFIX."system_department` as dd WHERE dd.`sysno` = d.`parent_sysno`) as `parent_departmentname` FROM `".DB_PREFIX."system_department` AS d {$where} ";
				if($params['orders'] != '')
					$sql .= " order by ".$params['orders'] ;
				$result['list'] = $this->dbh->select($sql);
			}else{
				$result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

				$this->dbh->set_page_num($params['pageCurrent']);
				$this->dbh->set_page_rows($params['pageSize']);

				$sql = "SELECT * FROM `".DB_PREFIX."system_department` {$where} ";
				if ($params['orders'] != '') {
					$sql .= " order by " . $params['orders'];
				}
				$result['list'] = $this->dbh->select_page($sql);
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

	/**
	 * 根据父ID获取部门数据
	 * @return 数组
	 * @author Dujiangjiang
	 */
	public function getDepartMentByPid($pid=0){
		$sql = "SELECT * FROM `".DB_PREFIX."system_department` WHERE isdel=0 AND `parent_sysno`=".intval($pid);
		return $this->dbh->select($sql);
	}

	/**
	 * 获取部门数据
	 * @return 数组
	 * @author Dujiangjiang
	 */
	public function getDepartMents(){
		$sql = "SELECT * FROM `".DB_PREFIX."system_department` WHERE `isdel`=0";
		return $this->dbh->select($sql);
	}

	/**
	 * 新增部门数据添加
	 * @return boolean
	 * @author Dujiangjiang
	 */
	public function addDepartment($data,$privileges="") {
		$this->dbh->begin();
		try {
			$res = $this->dbh->insert(DB_PREFIX.'system_department', $data);
			if (!$res) {
				$this->dbh->rollback();
				return false;
			}
			$id = $res;

			$res = $this->dbh->delete(DB_PREFIX.'system_department-r-privilege', 'department_sysno=' . intval($id));

			if (!$res) {
				$this->dbh->rollback();
				return false;
			}

			if ($privileges !== "") {
				$privilegeArr = explode(",", $privileges);

				if (!empty($privilegeArr)) {
					foreach ($privilegeArr as $value) {
						$privilegesdata = array(
							'department_sysno' => $id,
							'privilege_sysno' => $value,
						);
						//".DB_PREFIX."system_role-r-privilege insert
						$res = $this->dbh->insert(DB_PREFIX.'system_department-r-privilege', $privilegesdata);

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

	/**
	 * 根据主键获取部门信息
	 * @return 数组
	 * @author Dujiangjiang
	 */
	public function getDepartmentById($id){
		$sql = "SELECT * FROM `".DB_PREFIX."system_department` WHERE isdel=0 AND `sysno`=".intval($id);
		return $this->dbh->select_row($sql);
	}

	/**
	 * 编辑部门数据更新
	 * @return boolean
	 * @author Dujiangjiang
	 */
	public function updateDepartment($id = 0, $data = array(), $privileges = "") {
		$this->dbh->begin();
		try {
			//".DB_PREFIX."system_role update
			$res = $this->dbh->update(DB_PREFIX.'system_department', $data, 'sysno=' . intval($id));
			if (!$res) {
				$this->dbh->rollback();
				return false;
			}
			//".DB_PREFIX."system_role-r-privilege delete
			$res = $this->dbh->delete(DB_PREFIX.'system_department-r-privilege', 'department_sysno=' . intval($id));

			if (!$res) {
				$this->dbh->rollback();
				return false;
			}
			if ($privileges !== "") {
				$privilegeArr = explode(",", $privileges);

				if (!empty($privilegeArr)) {
					foreach ($privilegeArr as $value) {
						$privilegesdata = array(
							'department_sysno' => $id,
							'privilege_sysno' => $value,
						);
						//".DB_PREFIX."system_role-r-privilege insert
						$res = $this->dbh->insert(DB_PREFIX.'system_department-r-privilege', $privilegesdata);

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
	 * 根据部门ID获取部门对应权限
	 * @return 数组
	 * @author Dujiangjiang
	 */
	public function getDepartmentPrivilege($id){
		$sql = "SELECT * FROM `".DB_PREFIX."system_department-r-privilege` WHERE `department_sysno`=".intval($id);
		return $this->dbh->select($sql);
	}

	/**
	 * 部门对应权限by视图
	 */
	public function getDepartmentViewPrivilege($roleprivileges = array()) {
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

			if ($privilege['parent_sysno'] == 0) {
				$module[] = array('mval' => $privilege['privilegename'], 'msysno' => $privilege['sysno'], 'check' => $privilege['check']);
			}
		}

		$out['privileges'] = $privilegesview;
		$out['module'] = $module;

		return $out;
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
	//编号是否唯一、添加
		public function isUniqueNoadd($departmentno){
			//查询部门编号是否唯一
			if($departmentno){
				$sql = "select count(*) as departmentno from  ".DB_PREFIX."system_department where isdel = 0 AND departmentno = '".$departmentno."' ";
				$res = $this->dbh->select_one($sql);
					if($res !=0){
						return ['code'=>300,'status'=>'部门编号已存在'];
					}
			}

		}
	//编号是否唯一、编辑
	public function isUniqueNoedit($departmentno,$id){
		//查询部门编号是否唯一
		if($departmentno){
			$sql = "select departmentno as no from  ".DB_PREFIX."system_department where isdel = 0 AND sysno = '".$id."' ";
			$no = $this->dbh->select_one($sql);
			if($no != $departmentno){
				$sql = "select count(*) as departmentno from  ".DB_PREFIX."system_department where isdel = 0 AND departmentno = '".$departmentno."' ";
				$res = $this->dbh->select_one($sql);
				if($res !=0){
					return ['code'=>300,'status'=>'部门编号已存在'];
				}
			}

		}

	}




}
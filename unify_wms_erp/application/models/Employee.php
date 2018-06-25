<?php
/**
 * 系统资源 Model
 *
 * @author  totti
 * @date    2014-07-10 16:33
 *
 */
class EmployeeModel
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
     * 根据条件检索员工
     * @return 数组
     * @author Jay Xu
     */
    public function searchEmployee($params)
    {
        $filter = array();
        if (isset($params['bar_no']) && $params['bar_no'] != '') {
            $filter[] = " se. `employeeno` LIKE '%{$params['bar_no']}%' ";
        }
        if (isset($params['bar_name']) && $params['bar_name'] != '') {
            $filter[] = " se. `employeename` LIKE '%{$params['bar_name']}%' ";
        }
        if (isset($params['bar_status']) && $params['bar_status'] != '-100') {
            $filter[] = " se. `status`= {$params['bar_status']} ";
        }
        if (isset($params['bar_isdel']) && $params['bar_isdel'] != '-100') {
            $filter[] = " se. `isdel`='{$params['bar_isdel']}'";
        }
        if (isset($params['department_sysno']) && $params['department_sysno'] != '') {
            $filter[] = " se. `department_sysno`='{$params['department_sysno']}'";
        }
        $where = 'where se.isdel =0';
        if (1 <= count($filter)) {
            $where .= " and " . implode(' AND ', $filter);
        }
        $order = "order by se.`updated_at` desc";
        $sql = "SELECT COUNT(*)  FROM `".DB_PREFIX."system_employee` se
                left join ".DB_PREFIX."system_department sd on sd.sysno = se.department_sysno and sd.`isdel`=0 
                {$where} {$order} ";
        $result = $params;

        $result['totalRow'] = $this->dbh->select_one($sql);

        $result['list'] = array();
        if ($result['totalRow']) {

          if (isset($params['page']) && $params['page'] == false) {
              $sql = "SELECT se.*,sd.departmentname,sp.positionname 
                      FROM `".DB_PREFIX."system_employee` se 
                      LEFT JOIN ".DB_PREFIX."system_department sd ON sd.sysno = se.department_sysno AND sd.`isdel`=0 
			          LEFT JOIN ".DB_PREFIX."system_position sp ON sp.sysno = se.position_sysno {$where} {$order} ";
              if ($params['orders'] != '')
                  $sql .= " order by " . $params['orders'];

              $arr = $this->dbh->select($sql);


              $result['list'] = $arr;
          } else {
              $result['totalPage'] = ceil($result['totalRow'] / $params['pageSize']);

              $this->dbh->set_page_num($params['pageCurrent']);
              $this->dbh->set_page_rows($params['pageSize']);


              $sql = "SELECT se.*,sd.departmentname,sp.positionname 
                      FROM `".DB_PREFIX."system_employee` se 
                      LEFT JOIN ".DB_PREFIX."system_department sd ON sd.sysno = se.department_sysno AND sd.`isdel`=0 
			          LEFT JOIN ".DB_PREFIX."system_position sp ON sp.sysno = se.position_sysno {$where} {$order} ";
              if ($params['orders'] != '')
                  $sql .= " order by " . $params['orders'];

              $arr = $this->dbh->select_page($sql);


              $result['list'] = $arr;
          }

      }



        return $result;
    }

    /**
     * 新增员工数据添加
     * @return boolean
     * @author Jay Xu
     */
    public function addEmployee($data)
    {
        return $this->dbh->insert(DB_PREFIX.'system_employee', $data);
    }


    /**
     * 根据id获得员工细节
     * id: 权限id
     * @return 数组
     */
    public function getEmployeeById($id = 0)
    {
        $sql = "select p.*,".DB_PREFIX."system_department.departmentname from ".DB_PREFIX."system_employee p
                left join ".DB_PREFIX."system_department on ".DB_PREFIX."system_department.sysno = p.department_sysno
                where p.sysno = $id ";

        return $this->dbh->select_row($sql);
    }

    /**
     * 查询员工所有的数据
     */
    public function getEmployeelist(){
        $sql = "select p.sysno,p.employeename from ".DB_PREFIX."system_employee p
                where p.isdel = 0 and p.status = 1 ";
        return $this->dbh->select($sql);
    }

    /**
     * 员工更新
     * @param array $data
     * @param string $privileges
     * @return bool
     */

    public function updateEmployee($id = 0, $data = array())
    {
        $res = $this->dbh->update(DB_PREFIX.'system_employee', $data, 'sysno=' . intval($id));
        return $res;
    }

    /**
     * 获取部门下拉列表
     * @return 数组
     * @author Dujiangjiang
     */
    public function getDepartMents(){
        $sql = "SELECT `sysno`,`departmentname` FROM `".DB_PREFIX."system_department` WHERE `status`=1 AND `isdel`=0";
        return $this->dbh->select_hash($sql);
    }

    /**
     * 获取岗位下拉列表
     * @return 数组
     * @author Dujiangjiang
     */
    public function getPositions(){
        $sql = "SELECT `sysno`,`positionname` FROM `".DB_PREFIX."system_position` WHERE `status`=1 AND `isdel`=0";
        return $this->dbh->select_hash($sql);
    }

    /**
     * 获取单个员工的岗位
     * @return 岗位名
     * @author hr
     */
    public function getJob($id){
        $sql = "SELECT p.positionname from 
                ".DB_PREFIX."system_user u
                LEFT JOIN ".DB_PREFIX."system_employee e ON u.employee_sysno = e.sysno
                LEFT JOIN ".DB_PREFIX."system_position p ON e.position_sysno = p.sysno
                where p.isdel=0 AND u.sysno={$id}";
        return $this->dbh->select_one($sql);
    }


    //编号是否唯一、添加
    public function isUniqueNoadd($employeeno){
        //查询部门编号是否唯一
        if($employeeno){
            $sql = "select count(*) as employeeno from  ".DB_PREFIX."system_employee where isdel = 0 AND employeeno = '".$employeeno."' ";
            $res = $this->dbh->select_one($sql);
            if($res !=0){
                return ['code'=>300,'status'=>'员工编号已存在'];
            }
        }

    }

    //编号是否唯一、编辑
    public function isUniqueNoedit($employeeno,$id){
        //查询员工编号是否唯一
        if($employeeno){
            $sql = "select employeeno as no from  ".DB_PREFIX."system_employee where isdel = 0 AND sysno = '".$id."' ";
            $no = $this->dbh->select_one($sql);
            if($no != $employeeno){
                $sql = "select count(*) as employeeno from  ".DB_PREFIX."system_employee where isdel = 0 AND employeeno = '".$employeeno."' ";
                $res = $this->dbh->select_one($sql);
                if($res !=0){
                    return ['code'=>300,'status'=>'员工编号已存在'];
                }
            }

        }

    }
}
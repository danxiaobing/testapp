<?php
/**
 * @Author: Jay Xu
 * @Date:   2016-11-15 17:38:10
 */
class EmployeeController extends Yaf_Controller_Abstract
{
    /**
     * IndexController::init()
     *
     * @return void
     */
    public function init()
    {
        # parent::init();
    }


    /**
     * 员工管理
     * @author Jay Xu
     */
    public function employeeAction()
    {

        $params = array(
            'bar_no'=>'',
            'bar_name'=>''
        );
        $this->getView()->make('employee.list',$params);

    }

    public function listAction(){
        $params = array();
        $this->getView()->make('employee.list',$params);
    }

    /**
     * 员工管理列表JSON
     * @author Jay Xu
     */
    public function employeeListJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'bar_parentid' => $request->getPost('bar_parentid', '-100'),
            'bar_status' => $request->getPost('bar_status', '-100'),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );

        $S = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $list = $S->searchEmployee($search);

        echo json_encode($list);
    }

    public function employeeEditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);

        $S = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $department = new DepartmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if (!$id) {
            $action = "/employee/employeeNewJson/";
            $params = array();
        } else {
            $action = "/employee/employeeEditJson/";
            $params = $S->getEmployeeById($id);
        }

        $params['id'] = $id;
        $params['action'] = $action;
        $params['department'] = $department->getDepartMents();
        $params['position'] = $S->getPositions();

        $this->getView()->make('employee.edit', $params);
    }

    public function employeeNewJsonAction()
    {

        $request = $this->getRequest();

        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'department_sysno'  =>  $request->getPost('parentId',''),
            'position_sysno' => $request->getPost('position_sysno', ''),
            'employeeno' => $request->getPost('employeeno', ''),
            'employeeid' => $request->getPost('employeeid', ''),
            'employeename' => $request->getPost('employeename', ''),
            'employeephoto' => $request->getPost('employeephoto', ''),
            'employeegender' => $request->getPost('employeegender', ''),
            'employeebirthdate' => $request->getPost('employeebirthdate', ''),
            'employeenation' => $request->getPost('employeenation', ''),
            'employeeorigin' => $request->getPost('employeeorigin', ''),
            'employeemarital' => $request->getPost('employeemarital', ''),
            'employeepolitics' => $request->getPost('employeepolitics', ''),
            'employeeeducation' => $request->getPost('employeeeducation', ''),
            'employeemajor' => $request->getPost('employeemajor', ''),
            'employeeuniversity' => $request->getPost('employeeuniversity', ''),
            'employeecontactaddress' => $request->getPost('employeecontactaddress', ''),
            'employeeemail' => $request->getPost('employeeemail', ''),
            'employeeidnumber' => $request->getPost('employeeidnumber', ''),
            'employeecontacttel' => $request->getPost('employeecontacttel', ''),
            'employeebankaccount' => $request->getPost('employeebankaccount', ''),
            'employeeentrydate' => $request->getPost('employeeentrydate', ''),
            'employeetitle' => $request->getPost('employeetitle', ''),
            'employeecontractperiod' => $request->getPost('employeecontractperiod', ''),
            'employeeemploymentform' => $request->getPost('employeeemploymentform', ''),
            'employeeinservicestate' => $request->getPost('employeeinservicestate', ''),
            'employeeresume' => $request->getPost('employeeresume', ''),
            'employeeremarks' => $request->getPost('employeeremarks', ''),
            'status' => $request->getPost('status', '1'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );
        //判断编号是否唯一
        $uniqueno =	$E->isUniqueNoadd($input['employeeno']);
        if($uniqueno['code']==300){
            COMMON::result(300,'更新失败:'.$uniqueno['status'],$uniqueno['status']);
            return false;
        }

        if ($id = $E->addEmployee($input)) {
            $row = $E->getEmployeeById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }


    public function employeeEditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'department_sysno'  =>  $request->getPost('parentId',''),
            'position_sysno' => $request->getPost('position_sysno', ''),
            'employeeno' => $request->getPost('employeeno', ''),
            'employeeid' => $request->getPost('employeeid', ''),
            'employeename' => $request->getPost('employeename', ''),
            'employeephoto' => $request->getPost('employeephoto', ''),
            'employeegender' => $request->getPost('employeegender', ''),
            'employeebirthdate' => $request->getPost('employeebirthdate', ''),
            'employeenation' => $request->getPost('employeenation', ''),
            'employeeorigin' => $request->getPost('employeeorigin', ''),
            'employeemarital' => $request->getPost('employeemarital', ''),
            'employeepolitics' => $request->getPost('employeepolitics', ''),
            'employeeeducation' => $request->getPost('employeeeducation', ''),
            'employeemajor' => $request->getPost('employeemajor', ''),
            'employeeuniversity' => $request->getPost('employeeuniversity', ''),
            'employeecontactaddress' => $request->getPost('employeecontactaddress', ''),
            'employeeemail' => $request->getPost('employeeemail', ''),
            'employeeidnumber' => $request->getPost('employeeidnumber', ''),
            'employeecontacttel' => $request->getPost('employeecontacttel', ''),
            'employeebankaccount' => $request->getPost('employeebankaccount', ''),
            'employeeentrydate' => $request->getPost('employeeentrydate', ''),
            'employeetitle' => $request->getPost('employeetitle', ''),
            'employeecontractperiod' => $request->getPost('employeecontractperiod', ''),
            'employeeemploymentform' => $request->getPost('employeeemploymentform', ''),
            'employeeinservicestate' => $request->getPost('employeeinservicestate', ''),
            'employeeresume' => $request->getPost('employeeresume', ''),
            'employeeremarks' => $request->getPost('employeeremarks', ''),
            'status' => $request->getPost('status', '1'),
            'updated_at' => '=NOW()'
        );

        $uniqueno =	$E->isUniqueNoedit($input['employeeno'],$id);
        if($uniqueno['code']==300){
            COMMON::result(300,'更新失败:'.$uniqueno['status'],$uniqueno['status']);
            return false;
        }

        if ($E->updateEmployee($id, $input)) {
            $row = $E->getEmployeeById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }


    public function employeeDelJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);

        $S = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'isdel' => 1,
            'employeeno'=>'del'.strtotime(date('Y-m-d H:i:s')),
        );

        if ($S->updateEmployee($id, $input)) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败');
        }
    }

    public  function  photoAction(){
        $request = $this->getRequest();
        $params['pic']=$request->getParam('pic', '');
        echo $params['pic'];
        $this->getView()->make('employee.photo', $params);
    }

    public function ajaxUploadAction(){
        $request = $this->getRequest();
        $backid=$request->getPost('backid','');

        $result = array(
            'statusCode'=>'200',
            'message'=>'上传成功',
            'backid'=>$backid,
            'backval'=>''
        );

        $path = "upload/employee/";
        $up = new FileUpload;
        //设置属性(上传的位置， 大小， 类型， 名是是否要随机生成)
        $up->set("path", $path);
        $up->set("maxsize", 2000000);
        $up->set("allowtype", array("gif", "png", "jpg", "jpeg"));
        $up->set("israndname", true);

        //使用对象中的upload方法， 就可以上传文件， 方法需要传一个上传表单的名子 pic, 如果成功返回true, 失败返回false
        if ($up->upload('file')) {
            $result['backval'] = $path . $up->getFileName();
        } else {
            $result['statusCode']='300';
            $result['message']='上传失败';
        }
        echo json_encode($result);
    }

    public function employeeListAllJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'page' => false,
        );

        $S = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $list = $S->searchEmployee($search);

        echo json_encode($list);
    }

    public function employeeNameJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);

        $search = array(
            'page' => false,
        );

        $S = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params = $S->getEmployeeById($id);
        $list[] = array('value'=>$params['employeename'],'label'=>$params['employeename']);
        echo json_encode($list);
    }

}

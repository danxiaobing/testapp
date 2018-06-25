<?php

/**
 * Created by PhpStorm.
 * User: hanshutan
 * Date: 2016/11/15 0015
 * Time: 9:08
 */
class PositionController extends Yaf_Controller_Abstract
{
    public function init()
    {
        # parent::init();
    }

    /**
     * Title:查询所有的职位信息
     */
    public function listAction()
    {
        $params = array();
        $D = new DepartmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['departments'] = $D->getDepartMents();
        $this->getView()->make('position.list', $params);
    }

    public function datailJsonAction()
    {
        $request = $this->getRequest();
        $pages = $request->getPost('pageSize', '10');
        $pagec = $request->getPost('pageCurrent', '1');

        $search = array(
            'positionno' => $request->getPost('positionno', ''),
            'positionname' => $request->getPost('positionname', ''),
            'department_sysno' => $request->getPost('department_sysno', ''),
            'status' => $request->getPost('status', '')
        );
        $position = new PositionModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $params = $position->getPosition($search, $pages, $pagec);
        echo json_encode($params);
    }


    /**
     * 新增与编辑页面
     */
    public function positionAddEditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        $position = new PositionModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $department = new DepartmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if (!$id) {
            $action = "/position/addposition/";
            $params = array();
            $params['rootlist'] = $department->getDepartMents();
        } else {
            $action = "/position/editposition/";
            $params = $position->getPositionById($id);
        }
        $params['id'] = $id;
        $params['action'] = $action;
        $params['departmentlist'] = $department->getDepartMents();
        $this->getView()->make('position.edit', $params);
    }


    /**
     * Title:新增职位
     */
    public function AddPositionAction()
    {
        $position = new PositionModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $request = $this->getRequest();
        $department_sysno = $request->getPost('parentId');
        $input = array(
            'positionno' => $request->getPost('positionno', '0'),
            'department_sysno' => $department_sysno,
            'positionname' => $request->getPost('positionname', ''),
            'positiondesc' => $request->getPost('positiondesc', ''),
            'status' => $request->getPost('status', '1'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );
        if ($id = $position->addPosition($input)) {
            $row = $position->getPositionById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    /**
     * Title:编辑职位
     */
    public function EditPositionAction()
    {
        $position = new PositionModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $department = new DepartmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $array = trim($request->getPost('department_sysno', ''));
        $arr = array(
            'bar_name' => $array,
            'page' => false
        );
        $data = $department->searchDepartment($arr);
        #var_dump($data['list'][0]['sysno']);exit;
        $input = array(
            'positionno' => $request->getPost('positionno', '0'),
            'department_sysno' => $data['list'][0]['sysno'],
            'positionname' => $request->getPost('positionname', ''),
            'positiondesc' => $request->getPost('positiondesc', ''),
            'status' => $request->getPost('status', '1'),
            'updated_at' => '=NOW()'
        );
        if ($position->updatePosition($id, $input)) {
            $row = $position->getPositionById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    /**
     * Title:删除职位
     */
    public function DeletePositionAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);

        $position = new PositionModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'isdel' => 1
        );
        if ($position->updatePosition($id, $input)) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败');
        }
    }

}
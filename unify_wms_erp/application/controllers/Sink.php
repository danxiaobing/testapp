<?php
/**
 * 汇签部门配置
 * User:ty
 * Date: 2016/11/19 0019
 * Time: 12:19
 */
class SinkController extends Yaf_Controller_Abstract
{
    public function init()
    {
        # parent::init();
    }

    /**
     * Title:质量列表展示
     */
    public function listAction(){
        $params=array();

        $this->getView()->make('system.sink.list', $params);
    }

    public function sinklistJsonAction(){
        $request = $this->getRequest();
        $pages= $request->getPost('pageSize','10');
        $pagec= $request->getPost('pageCurrent','1');

        $search = array (
            'departmentname' => $request->getPost('departmentname',''),
            'status' => $request->getPost('bar_status',''),
        );

        $sink = new SinkModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        $params = $sink->getList($search,$pages,$pagec);

        echo json_encode($params);
    }

    /**
     * 添加修改展示
     */
    public function sinkeditAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $sink = new SinkModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));
        if(!$id){
            $action = "/sink/add/";
            $params =  array ();
        } else {
            $action = "/sink/edit/";
            $params = $sink->getSinkById($id);
        }

        $params['id'] = $id;
        $params['action'] =  $action;

        $S = new SendversionModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $version = $S->getVersionInfo();

        $params['version'] = $version;

        $params['departmentsname'] = $sink->getDepartmentsname();

//        echo "<pre>";
//        var_dump($params);
//        exit();

        $this->getView()->make('system.sink.edit',$params);
    }


    /**
     * 新增方法
     */
    public function AddAction(){
        $request = $this->getRequest();
        $input = array(
            'reviewtype' => $request->getPost('reviewtype','1'),
            'department_sysno' => $request->getPost('parentId','0'),
            'departmentname' => $request->getPost('departmentname','0'),
            'version' => $request->getPost('version','0'),
            'sortnum' => $request->getPost('sortnum','1'),
            'status'  =>  $request->getPost('status','1'),
            'isdel'=>  $request->getPost('isdel','0'),
            'created_at'	=> 	'=NOW()',
            'updated_at'	=> 	'=NOW()'
        );
        $sink = new SinkModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if($id = $sink->add($input)){
            $row = $sink->getSinkById($id);
            COMMON::result(200,'添加成功',$row);
        }else{
            COMMON::result(300,'添加失败');
        }
    }

    /**
     * Title:编辑方法
     */
    public function EditAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id',0);
        $input = array(
            'reviewtype' => $request->getPost('reviewtype','1'),
            'department_sysno' => $request->getPost('parentId',''),
            'departmentname' => $request->getPost('departmentname','0'),
            'sortnum' => $request->getPost('sortnum','1'),
            'status'  =>  $request->getPost('status','1'),
            'version_sysno' => $request->getPost('version_sysno','0'),
            'isdel'=>  $request->getPost('isdel','0'),
            'updated_at'=>  '=NOW()'
        );

        $sink = new SinkModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if($sink->update($id,$input)){
            $row = $sink->getSinkById($id);
            COMMON::result(200,'更新成功',$row);
        }else{
            COMMON::result(300,'更新失败');
        }
    }
    /**
     * Title:删除方法
     */
    public function DeleteAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno',0);

        $sink = new SinkModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'isdel' => 1
        );
        if($sink->update($id,$input)){
            COMMON::result(200,'删除成功');
        }else{
            COMMON::result(300,'删除失败');
        }
    }

}
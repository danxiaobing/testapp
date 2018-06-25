<?php
/**
 * Created by PhpStorm.
 * User: Jay Xu
 * Date: 2016/11/17 0017
 * Time: 10:35
 */
class AreaController extends Yaf_Controller_Abstract
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
     * 片区管理
     * @author Jay Xu
     */

    public function listAction()
    {
        $params = array(

        );
        $this->getView()->make('area.list', $params);
    }

    /**
     * 片区管理列表JSON
     * @author Jay Xu
     */

    public function areaListJsonAction()
    {
        $request = $this->getRequest();

        $search = array (
            'areaid' => $request->getPost('areaid',''),
            'areaname' => $request->getPost('areaname',''),
            'bar_status'   => $request->getPost('bar_status','-100'),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );

        $S = new AreaModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $list = $S->searchArea($search);

        echo json_encode($list);
    }


    public function areaEditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);

        $S = new AreaModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if (!$id) {
            $action = "/area/areaNewJson/";
            $params = array();
        } else {
            $action = "/area/areaEditJson/";
            $params = $S->getAreaById($id);
        }

        $params['id'] = $id;
        $params['action'] = $action;
        $this->getView()->make('area.edit', $params);
    }

    public function areaNewJsonAction()
    {

        $request = $this->getRequest();

        $S = new AreaModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'areaid'  => $request->getPost('areaid',''),
            'areaname' => $request->getPost('areaname', ''),
            'areamarks' => $request->getPost('areamarks', ''),
            'status' => $request->getPost('status', '1'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        #后台验证片区编号
        $search = array (
            'areaid'=>$input['areaid'],
            'page' => false,
        );
        $areaid = $S->searchArea($search);
        if(!empty($areaid['list'])){
            COMMON::result(300,'片区编号不能重复');
            return;
        }

            if ($id = $S->addArea($input)) {
                $row = $S->getAreaById($id);
                COMMON::result(200, '添加成功', $row);
            } else {
                COMMON::result(300, '添加失败');
            }
        }



    public function areaEditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        $S = new AreaModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'areaid'  => $request->getPost('areaid',''),
            'areaname' => $request->getPost('areaname', ''),
            'areamarks' => $request->getPost('areamarks', ''),
            'status' => $request->getPost('status', '1'),
            'updated_at' => '=NOW()'
        );

        #后台验证片区编号
        $search = array (
            'areaid' =>$input['areaid'],
            'page' =>false,
        );
        $areaid = $S->searchArea($search);
        if(!empty($areaid['list']) && $areaid['list'][0]['sysno']!=$id){
            COMMON::result(300,'片区编号不能重复');
            return;
        }

        if ($S->updateArea($id, $input)) {
            $row = $S->getAreaById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }


    public function areaDelJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);

        $S = new AreaModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if($S->isdel($id)){
            COMMON::result(300, '该片区已经被储罐引用不能删除!');
            exit;
        }

        $input = array(
            'isdel' => 1
        );

        if ($S->updateArea($id, $input)) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败');
        }
    }


    /**
    * 批量启用禁用
    * @author hr
    */
    public function AreaChangeAction()
    {
        $request = $this->getRequest();

        $data = $request->getPost('data','');

        $state = $request->getPost('state','');

        $S = new AreaModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if($S->change($data,$state)){
            COMMON::result(200,'更新成功');
        }else{

            COMMON::result(300,'更新失败');
        }
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Jay Xu
 * Date: 2017/5/15 0010
 * Time: 13:32
 */
class WharfController extends Yaf_Controller_Abstract
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
     * 码头管理
     * @author Jay Xu
     */

    public function listAction()
    {
        $params = array(

        );
        $this->getView()->make('wharf.list', $params);
    }

    /**
     * 码头管理列表JSON
     * @author Jay Xu
     */

    public function wharfListJsonAction()
    {
        $request = $this->getRequest();

        $search = array (
            'wharfname' => $request->getPost('wharfname',''),
            'wharfno' => $request->getPost('wharfno',''),
            'bar_status'   => $request->getPost('bar_status','-100'),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );

        $W = new WharfModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $list = $W->searchWharf($search);

        echo json_encode($list);
    }


    public function wharfEditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);

        $W = new WharfModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if (!$id) {
            $action = "/wharf/wharfNewJson/";
            $params = array();
        } else {
            $action = "/wharf/wharfEditJson/";
            $params = $W->getWharfById($id);
        }

        $params['id'] = $id;
        $params['action'] = $action;
        $this->getView()->make('wharf.edit', $params);
    }

    public function WharfNewJsonAction()
    {

        $request = $this->getRequest();

        $W = new WharfModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'wharfname'  => $request->getPost('wharfname',''),
            'wharfno' => COMMON::getCodeId('W'),
            'wharfmarks' => $request->getPost('wharfmarks', ''),
            'status' => $request->getPost('status', '1'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        #后台验证片区编号
        $search = array (
            'wharfno'=>$input['wharfno'],
            'page' => false,
        );
        if ($id = $W->addWharf($input)) {
            $row = $W->getWharfById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }



    public function wharfEditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        $W = new WharfModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'wharfname'  => $request->getPost('wharfname',''),
            'wharfno' => $request->getPost('wharfno', ''),
            'wharfmarks' => $request->getPost('wharfmarks', ''),
            'status' => $request->getPost('status', '1'),
            'updated_at' => '=NOW()'
        );

        if ($W->updateWharf($id, $input)) {
            $row = $W->getWharfById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }


    public function wharfDelJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);

        $W = new WharfModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        
        $input = array(
            'isdel' => 1
        );

        if ($W->updateWharf($id, $input)) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败');
        }
    }


    /**
     * 批量启用禁用
     * @author hr
     */
    public function WharfChangeAction()
    {
        $request = $this->getRequest();

        $data = $request->getPost('data','');

        $state = $request->getPost('state','');

        $W = new WharfModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if($W->change($data,$state)){
            COMMON::result(200,'更新成功');
        }else{

            COMMON::result(300,'更新失败');
        }
    }

}
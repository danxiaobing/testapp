<?php

class OthercostController extends Yaf_Controller_Abstract
{
    public function init()
    {
        # parent::init();
    }

    public function listAction()
    {
        $params = array();
        $this->getView()->make('othercost.list', $params);
    }

    /**
     * Title:查询列表
     */

    public function datailAction()
    {
        $request = $this->getRequest();
        $pages = $request->getPost('pageSize', '10');
        $pagec = $request->getPost('pageCurrent', '1');
        $search = array(
            'othercostname' => $request->getPost('othercostname', ''),
            'status' => $request->getPost('bar_status',''),
        );

        $othercost = new OthercostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $params = $othercost->getOthercost($search, $pages, $pagec);

        echo json_encode($params);
    }

    public function listjsonforconAction(){
        $othercost = new OthercostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params = $othercost->getlist();

        echo json_encode($params);
    }


    /**
     * Title：添加编辑视图
     */
    public function othercostaddeditAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $othercost = new OthercostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $unit = new UnitModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if(!$id){
            $action = "/othercost/addothercost/";
            $params =  array();
        }else{
            $action = "/othercost/editothercost/";
            $params = $othercost->getOthercostById($id);
        }
        $params['id'] = $id;
        $params['action'] =  $action;
        $params['unitlist'] = $unit->getUnit();
  
        $this->getView()->make('othercost.edit',$params);
    }

    /**
     * title : 新增方法
     */
    public function AddothercostAction(){
        $request = $this->getRequest();
        $othercost = new OthercostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'othercostname'=>  $request->getPost('othercostname','0'),
            'unit_sysno'  =>  $request->getPost('unit_sysno',''),
            'othercostprice'=>  $request->getPost('othercostprice',''),
            'othercostmarks'=>  $request->getPost('othercostmarks',''),
            'status'       	=>  $request->getPost('status','1'),
            'created_at'	=> 	'=NOW()',
            'updated_at'	=> 	'=NOW()'
        );

        if($id = $othercost->addothercost($input)){
            $row = $othercost->getOthercostById($id);
            COMMON::result(200,'添加成功',$row);
        }else{
            COMMON::result(300,'添加失败');
        }
    }

    /**
     * title :编辑方法
     */
    public function editothercostAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id',0);
        $othercost = new OthercostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'othercostname'=>  $request->getPost('othercostname','0'),
            'unit_sysno'  =>  $request->getPost('unit_sysno',''),
            'othercostprice'=>  $request->getPost('othercostprice',''),
            'othercostmarks'=>  $request->getPost('othercostmarks',''),
            'status'       	=>  $request->getPost('status','1'),
            'updated_at'	=> 	'=NOW()'
        );
        if($othercost->updateOthercost($id,$input)){
            $row = $othercost->getOthercostById($id);
            COMMON::result(200,'更新成功',$row);
        }else{
            COMMON::result(300,'更新失败');
        }
    }

    /**
     * title :其他收费管理 软删除
     */
    public function deleteothercostAction(){
        $request = $this->getRequest();
        $id = $request->getPost('sysno',0);

        $othercost = new OthercostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if($othercost->isdel($id)){
            COMMON::result(300,'该项被合同引用无法删除!');
            exit;
        }

        $input = array(
            'isdel' => 1
        );
        if($othercost->updateOthercost($id,$input)){
            COMMON::result(200,'删除成功');
        }else{
            COMMON::result(300,'更新失败');
        }
    }

    public function othercostPriceAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $othercost = new OthercostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $price = $othercost->getOthercostById($id);
        $list[] = array('price'=>$price['othercostprice']);
        echo json_encode($list);
    }

    /**
    * 批量启用禁用
    * @author hr
    */
    public function othercostChangeAction()
    {
        $request = $this->getRequest();

        $data = $request->getPost('data','');

        $state = $request->getPost('state','');

        $othercost = new OthercostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if($othercost->change($data,$state)){
            COMMON::result(200,'更新成功');
        }else{

            COMMON::result(300,'更新失败');
        }
    }

    public function contractdatailAction()
    {
        $request = $this->getRequest();
        $pages = $request->getPost('pageSize', '10');
        $pagec = $request->getPost('pageCurrent', '1');
        
        $search = array(
            'customer_sysno' => $request->getParam('customer_sysno', '0'),
            'contract_sysno' => $request->getParam('contract_sysno', '0'),
            'status' => $request->getPost('bar_status',''),
            'page'=>false,
        );

        $othercost = new OthercostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $params = $othercost->getContractOthercost($search);

        echo json_encode($params);
    }
}
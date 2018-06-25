<?php

/**
 * Created by PhpStorm.
 * User: hanshutan
 * Date: 2016/11/18 0018
 * Time: 15:03
 */
Class SettlementController extends Yaf_Controller_Abstract
{

    public function init()
    {
        # parent::init();
    }

    public function listAction()
    {
        $params = array();
        $this->getView()->make('settlement.list', $params);
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
            'settlementname' => $request->getPost('settlementname', ''),
            'status' => $request->getPost('bar_status','')
        );

        $settlement = new SettlementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $params = $settlement->getSettlement($search, $pages, $pagec);

        echo json_encode($params);
    }

    /**
     * Title：添加编辑视图
     */
    public function SettlementaddeditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        $settlement = new SettlementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if (!$id) {
            $action = "/settlement/addsettlement/";
            $params = array();
        } else {
            $action = "/settlement/editsettlement/";
            $params = $settlement->getSettlementById($id);
        }
        $params['id'] = $id;
        $params['action'] = $action;
        $this->getView()->make('settlement.edit', $params);
    }

    /**
     * title : 新增方法
     */
    public function AddsettlementAction()
    {
        $request = $this->getRequest();
        $settlement = new SettlementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'settlementname'=>  $request->getPost('settlementname', ''),
            'status'       	=>  $request->getPost('status', '1'),
            'isdel'        	=>  $request->getPost('isdel', '0'),
            'created_at'	=> 	'=NOW()',
            'updated_at'	=> 	'=NOW()'
        );

        if ($id = $settlement->addSettlement($input)) {
            $row = $settlement->getSettlementById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    /**
     * title :编辑方法
     */
    public function editsettlementAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id',0);
        $settlement = new SettlementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'settlementname'=>  $request->getPost('settlementname', ''),
            'status'       	=>  $request->getPost('status', '1'),
            'isdel'        	=>  $request->getPost('isdel', '0'),
            'updated_at'	=> 	'=NOW()'
        );
        if($settlement->upSettlement($id,$input)){
            $row = $settlement->getSettlementById($id);
            COMMON::result(200,'更新成功',$row);
        }else{
            COMMON::result(300,'更新失败');
        }
    }

    /**
     * title :仓储管理 软删除
     */
    public function deletesettlementAction(){
        $request = $this->getRequest();
        $id = $request->getPost('date',0);
        $id = implode(',',$id);
        //print_r($id);die;
        $settlement = new SettlementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'isdel' => 1
        );
        if($settlement->upSettlement($id,$input)){
            COMMON::result(200,'删除成功');
        }else{
            COMMON::result(300,'更新失败');
        }
    }
    /*
     * 新增状态开启停用
     * */
    public function statuschangeAction(){
        $request = $this->getRequest();
        $date = $request->getPost('date','');
        $ids = implode(',',$date);
        $settlement = new SettlementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $parms = array(
            'ids'=>$ids,
            'status'=>1,//启用
        );
        $res = $settlement->updateStatus($parms);
        if($res){
            COMMON::result(200, '启用成功');
        }else{
            COMMON::result('300','启用失败');
        }
    }
    /*
 * 新增状态停用
 * */
    public function statusoverAction(){
        $request = $this->getRequest();
        $date = $request->getPost('date','');
        $ids = implode(',',$date);
        $parms = array(
            'ids'=>$ids,
            'status'=>2,//停用
        );
        $settlement = new SettlementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $res = $settlement->updateStatus($parms);
        if($res){
            COMMON::result(200, '停用成功');
        }else{
            COMMON::result('300','停用失败');
        }
    }



}
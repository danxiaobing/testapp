<?php

class PrinttitleController extends Yaf_Controller_Abstract
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
     * 票据抬头信息
     *
     * @return string
     */
    public function indexAction() {
        $params = array();
        $this->getView()->make('printtitle.index',$params);
    }

    public function getlistJsonAction() {
        $request = $this->getRequest();
        $search = array (
            'titlename' => $request->getPost('titlename', ''),
            'isdefault' => $request->getPost('isdefault', ''),
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),

        );
        $S = new PrinttitleModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $S->searchPrinttitle($search);
        echo json_encode($list);
    }

    public function PrintTitleEditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);

        $S = new PrinttitleModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if (!$id) {
            $action = "/Printtitle/newJson/";
//            $params = array('title' => '票据抬头管理-添加');
        } else {
            $action = "/Printtitle/editJson/";
            $params = $S->getPrintTitleById($id);
//            $params['title'] =  '-编辑';
        }

        $params['id'] = $id;
        $params['action'] = $action;
        $this->getView()->make('printtitle.edit', $params);
    }

    public function newJsonAction()
    {
        $request = $this->getRequest();
        $S = new PrinttitleModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'titlename' => $request->getPost('titlename', ''),
            'titlemarks' => $request->getPost('titlemarks', ''),
            'isdefault' => $request->getPost('isdefault', '0'),
            'status' => $request->getPost('status', '1'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );
        if ($id = $S->addPrintTitle($input)) {
            if($id == 'mustDefault'){
                COMMON::result(300, '必须默认启用一个公司');
                exit();
            }
            $row = $S->getPrintTitleById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    public function editJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $S = new PrinttitleModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'titlename' => $request->getPost('titlename', ''),
            'titlemarks' => $request->getPost('titlemarks', ''),
            'isdefault' => $request->getPost('isdefault','0'),
            'status' => $request->getPost('status', '1'),
            'updated_at' => '=NOW()'
        );
        if ($res = $S->updatePrintTitle($id, $input)) {
            if((float)$res == 'mustDefault'){
                COMMON::result(300, '必须默认启用一个公司');
                exit();
            }
            $row = $S->getPrintTitleById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    /**
     * 删除票据抬头
     * @return bool
     */
    public function printTitleDelJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('date', 0);

        $S = new PrinttitleModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $res = $S-> getDefault();
        if(in_array($res['sysno'] , $id)){
            COMMON::result(300, '必须默认启用一个公司');
            exit();
        }
        $id = implode(',',$id);
        $input = array(
            'isdel' => 1
        );
        if ( $S -> updatePrintTitle($id, $input)) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败');
        }
    }

//    /**
//     * 新增状态开启停用
//     */
//    public function statuschangeAction(){
//        $request = $this->getRequest();
//        $date = $request->getPost('date','');
//        $ids = implode(',',$date);
//        $S = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
//        $parms = array(
//            'ids'=>$ids,
//            'status'=>1,//启用
//        );
//        $res = $S->updateStatus($parms);
//        if($res){
//            COMMON::result(200, '启用成功');
//        }else{
//            COMMON::result('300','启用失败');
//        }
//    }
//    /**
//     * 新增状态停用
//     */
//    public function statusoverAction(){
//        $request = $this->getRequest();
//        $date = $request->getPost('date','');
//        $ids = implode(',',$date);
//        $parms = array(
//            'ids'=>$ids,
//            'status'=>2,//停用
//        );
//        $S = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
//        $res = $S->updateStatus($parms);
//        if($res){
//            COMMON::result(200, '停用成功');
//        }else{
//            COMMON::result('300','停用失败');
//        }
//    }


}
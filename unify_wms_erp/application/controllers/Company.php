<?php

class CompanyController extends Yaf_Controller_Abstract
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
     * 开票公司信息
     *
     * @return string
     */
    public function listAction() {
        $params = array();
        $this->getView()->make('company.list',$params);
    }

    public function companylistJsonAction() {
        $request = $this->getRequest();

        $search = array (
            'companyno' => $request->getPost('companyno',''),
            'companyname' => $request->getPost('companyname',''),
            'companysname' => $request->getPost('companysname',''),
            'contacttel' => $request->getPost('contacttel',''),
            'bar_status' => $request->getPost('bar_status',''),
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),

        );

        $S = new CompanyModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        $list = $S->searchCompany($search);

        echo json_encode($list);
    }

    public function companyEditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);

        $S = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if (!$id) {
            $action = "/company/companyNewJson/";
            $params = array();
        } else {
            $action = "/company/companyEditJson/";
            $params = $S->getCompanyById($id);
        }

        $params['id'] = $id;
        $params['action'] = $action;
        $this->getView()->make('company.edit', $params);
    }

    public function companyNewJsonAction()
    {

        $request = $this->getRequest();

        $S = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'companyno' => $request->getPost('companyno', ''),
            'companyname' => $request->getPost('companyname', ''),
            'companysname' => $request->getPost('companysname', ''),
            'warehouseaddress' => $request->getPost('warehouseaddress', ''),
            'contacttel' => $request->getPost('contacttel', ''),
            'contactfax' => $request->getPost('contactfax', ''),
            'postcode' => $request->getPost('postcode', ''),
            'legalperson' => $request->getPost('legalperson', ''),
            'bank' => $request->getPost('bank',''),
            'bank_account' => $request->getPost('bank_account',''),
            'isdefault' => $request->getPost('isdefault','0'),
            'status' => $request->getPost('status', '1'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );
        if ($id = $S->addCompany($input)) {
            $row = $S->getCompanyById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    public function companyEditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        $S = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(

            'companyno' => $request->getPost('companyno', ''),
            'companyname' => $request->getPost('companyname', ''),
            'companysname' => $request->getPost('companysname', ''),
            'warehouseaddress' => $request->getPost('warehouseaddress', ''),
            'contacttel' => $request->getPost('contacttel', ''),
            'contactfax' => $request->getPost('contactfax', ''),
            'postcode' => $request->getPost('postcode', ''),
            'legalperson' => $request->getPost('legalperson', ''),
            'bank' => $request->getPost('bank',''),
            'bank_account' => $request->getPost('bank_account',''),
            'isdefault' => $request->getPost('isdefault','0'),
            'status' => $request->getPost('status', '1'),
            'updated_at' => '=NOW()'
        );

        if ($S->updateCompany($id, $input)) {
            $row = $S->getCompanyById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }


    public function companyDelJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('date', 0);
        $id = implode(',',$id);
      //  print_r($id);die;
        $S = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'isdel' => 1
        );
         $res = $S->isOccupied($id);
         if($res>0){
              COMMON::result('300','该公司已被开票通知单引用，不能删除!');
              return false;
         }
        if ($S->updateCompany($id, $input)) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败');
        }
    }
/*
 * 新增状态开启停用
 * */
    public function statuschangeAction(){
        $request = $this->getRequest();
        $date = $request->getPost('date','');
        $ids = implode(',',$date);
        $S = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $parms = array(
            'ids'=>$ids,
            'status'=>1,//启用
        );
        $res = $S->updateStatus($parms);
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
        $S = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $res = $S->updateStatus($parms);
        if($res){
            COMMON::result(200, '停用成功');
        }else{
            COMMON::result('300','停用失败');
        }
    }


}
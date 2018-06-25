<?php

class SupplementController extends Yaf_Controller_Abstract
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

    public function  listAction() {
        $params = array();
        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'contractstatus' => 5,
            'contractenddate' => 'NOW()',
            'page' => false,
            'bar_status' => 1
        );
        $list = $C->searchCustomerContract($search);
        $params['customerlist'] = $list;

        $this->getView()->make('supplement.list',$params);
    }

    public function listJsonAction() {
        $request = $this->getRequest();

        $search = array (
            'supplementdate'=> $request->getPost('supplementdate',''),
            'customer_sysno' => $request->getPost('customer_sysno',''),
            'goods_sysno' => $request->getPost('goods_sysno',''),
            'goodsname' => $request->getPost('goodsname',''),
            'supplementstatus' => $request->getPost('supplementstatus',''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'orders'  => 'created_at desc',
        );
        $S = new SupplementModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $S->searchSupplement($search);

        echo json_encode($list);

    }

    /*
    * 显示编辑页面
    */
    public function EditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $mode = $request->getParam('mode','');

        $S = new SupplementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if (!$id) {
            $action = "/supplement/newJson/";
            $params = array();
        } else {
            $params = $S->getsupplementById($id);
            $supplementstatus = $params['supplementstatus'];

            if($mode == 'edit'){
                if($supplementstatus == 2 || $supplementstatus == 6){
                    $action = '/supplement/editJson';
                }
                else{
                    COMMON::result(300, '非暂存或退回状态不能编辑！');
                    return;
                }
            }elseif($mode == 'audit'){
                if ($supplementstatus != 3) {
                    COMMON::result(300, '该状态无法审核');
                    return;
                }
                $action = '/supplement/auditJson';
            }elseif($mode == 'view'){
                $action = '/supplement/view';
            }
        }

        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'contractstatus' => 5,
            'contractenddate' => 'NOW()',
            'page' => false,
            'bar_status' => 1
        );
        $list = $C->searchCustomerContract($search);
        $params['customerlist'] = $list;

        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];

        $params['id'] = $id;
        $params['action'] = $action;
        $params['mode'] = $mode;
        $this->getView()->make('supplement.detail', $params);
    }

    /*
     * 新建补充单
     */
    public function newJsonAction()
    {
        $request = $this->getRequest();
        $step = $request->getPost('step', '');
        $detaildata = $request->getPost('supplement_detail_data', "");
        $detaildata = json_decode($detaildata, true);

        if (count($detaildata) == 0) {
            COMMON::result(300, '明细信息不能为空');
            return;
        }
        foreach($detaildata as $key=>$value){
            if($value['beqty']==''){
                COMMON::result(300, '补充数量不能为空');
                return;
            }
            if(trim($value['goodsnature'])=='保税'){
                 $detaildata[$key]['goodsnature']=1;
            }elseif(trim($value['goodsnature'])=='外贸'){
                 $detaildata[$key]['goodsnature']=2;
            }elseif(trim($value['goodsnature'])=='内贸转出口'){
                 $detaildata[$key]['goodsnature']=3;
            }elseif(trim($value['goodsnature'])=='内贸内销'){
                $detaildata[$key]['goodsnature']=4;
            }

        }
        //主表数据
        $input = array(
            'supplementno' => COMMON::getCodeId('B2'),
            'supplementdate' => $request->getPost('supplementdate', ''),
            'goods_sysno' => $request->getPost('goods_sysno', ''),
            'goodsname' => $request->getPost('goodsname', ''),
            'customer_sysno' => $request->getPost('customer_sysno', '1'),
            'customername' => $request->getPost('customername', '1'),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'stockin_sysno' => $request->getPost('stockin_sysno', ''),
            'stockinno' =>$request->getPost('stockinno', ''),
            'shipname' => $request->getPost('shipname', ''),
            'supplementstatus' => $request->getPost('step', ''),
            'status' => 1,
            'isdel' => 0,
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

//           print_r($request->getRequest());
//           print_r($detaildata);
//           print_r($input);die;
        $S = new SupplementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $res = $S->addSupplement($input,$detaildata,$step);
        if ($res['code']==200) {
            $search = array('sysno'=>$res['msg'],'page'=>false);
            $row = $S->searchSupplement($search);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败'.$res['msg']);
        }
    }

    /*
     * 编辑补充单
     */
    public function editJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $step = $request->getPost('step', '');
        $supplementstatus = $request->getPost('supplementstatus', '');
        $detaildata = $request->getPost('supplement_detail_data', "");
        $detaildata = json_decode($detaildata, true);

        if(!$id){
            COMMON::result(300, '数据异常丢失id');
            return;
        }

        if (count($detaildata) == 0) {
            COMMON::result(300, '入库明细信息不能为空');
            return;
        }
        foreach($detaildata as $key=>$value){
            if($value['beqty']==''){
                COMMON::result(300, '补充数量不能为空');
                return;
            }

        }

        //主表数据
        $input = array(
            'supplementdate' => $request->getPost('supplementdate', ''),
            'goods_sysno' => $request->getPost('goods_sysno', ''),
            'goodsname' => $request->getPost('goodsname', ''),
            'customer_sysno' => $request->getPost('customer_sysno', '1'),
            'customername' => $request->getPost('customername', '1'),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'stockin_sysno' => $request->getPost('stockin_sysno', ''),
            'stockinno' =>$request->getPost('stockinno', ''),
            'shipname' => $request->getPost('shipname', ''),
            'supplementstatus' => $request->getPost('step', ''),
            'status' => 1,
            'isdel' => 0,
            'updated_at' => '=NOW()'
        );

//        print_r($id);
//        print_r($input);
//        print_r($detaildata);
//        die;

        $S = new SupplementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $res= $S->updateSupplement($id,$input,$detaildata,$supplementstatus,$step);

        if ($res['code']==200) {
            $search = array('sysno'=>$res['msg'],'page'=>false);
            $row = $S->searchSupplement($search);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    /**
     * @title 审批方法
     */
    public function auditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $step = $request->getPost('step', '');
        $S = new SupplementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if(!$id){
            COMMON::result(300, '数据异常丢失id');
            return;
        }
        $search = array(
            'sysno' =>$id,
            'page'=>false,
        );
        $detaildata = $S->getdetailById($search)['list'];

        if (count($detaildata) == 0) {
            COMMON::result(300, '明细不能为空');
            return;
        }

        $input  =  $S->getsupplementById($id);
        if ($step == 6) {
            if ($request->getPost('auditreason') == '') {
                COMMON::result(300, '审核备注不能为空');
                return;
            }
        }

        $res = $S->auditSupplement($id,$input,$detaildata, $step,$request->getPost('auditreason'));
        if ($res['code']==200) {
            $search = array('sysno'=>$id,'page'=>false);
            $row = $S->searchSupplement($search);
            COMMON::result(200, '审核成功', $row);
        } else {
            COMMON::result(300, $res['message']);
        }
    }

    public function getstockinListAction(){
        $request = $this->getRequest();
        $customer_sysno = $request->getParam('customer_sysno',0);
        $S = new SupplementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'customer_sysno'=>$customer_sysno,
            'page'=>false,
        );
       $stockinList = $S->getstockinList($search);
        //print_r($stockinList);die;
        echo json_encode($stockinList['list']);

    }

    /*
     * ******************
     * ***********************************
     *
     * */
    /*
       * 添加修改补充单明细页面
       */
    public function supplementeditAction()
    {
        $request = $this->getRequest();
        $stockin_sysno = $request->getParam('stockin_sysno', '0');
        $type = $request->getParam('type','0');

        $S = new SupplementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        //     print_r($id);
        if($type=='add'){
            //获取退货明细
            $search = array(
                'stockin_sysno'=>$stockin_sysno,
                'page'=>false,
            );

            $stockindata = $S->getstockinList($search);

            $params['list'] = $stockindata['list'][0];
        }elseif($type=='edit'){
            $params['list'] = $request->getPost('datadetail',array());
            $params['beqty'] = $params['list']['beqty'];
            $params['memo'] = $params['list']['memo'];
            if(trim($params['list']['goodsnature'])=='保税'){
                $params['list']['goodsnature']=1;
            }elseif(trim($params['list']['goodsnature'])=='外贸'){
                $params['list']['goodsnature']=2;
            }elseif(trim($params['list']['goodsnature'])=='内贸转出口'){
                $params['list']['goodsnature']=3;
            }elseif(trim($params['list']['goodsnature'])=='内贸内销'){
                $params['list']['goodsnature']=4;
            }
        }

        $params['stockin_sysno'] =$stockin_sysno;
        $params['type'] = $type;

      //  print_r($params);die;
        $this->getView()->make('supplement.addedit', $params);
    }


    //addedit页面获取储罐信息-基础放大镜使用
    public function gettankJsonAction() {
        $request = $this->getRequest();
        $storagetank_sysno = $request->getParam('storagetank_sysno',0);
        $search = array (
            'bar_status' => '1',
            'bar_isdel' => '0',
            'storagetank_sysno'=>$storagetank_sysno,
            'page' => false,
        );

        $S = new SupplementModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $S->getstankinfoByid($search);

        echo json_encode($list['list']);

    }
    /*
    *  编辑明细列表 dataurl 加载明细数据
    */
    public function getdetailJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');

        $S = new SupplementModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $search = array(
            'sysno'=>$id,
            'page'=>false,
        );

        $list = $S->getdetailById($search);

        //print_r($list);die;
        echo json_encode($list);
    }

//删除方法
    public  function deleteAction(){
        $request = $this->getRequest();
        $data = $request->getPost('selectdata', "");

        $id = $data[0]['sysno'];
        $supplementstatus = $data[0]['supplementstatus'];
        if($supplementstatus==4){
            COMMON::result(300, '已审核的单据不能删除');
            return;
        }

        $S = new SupplementModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $input = array(
            'isdel'=>1,
            'updated_at' => '=NOW()'
        );


        $res = $S->delSupplement($id,$input);
        if ($res['code']==200) {
            $search = array('sysno'=>$id,'page'=>false);
            $row = $S->searchSupplement($search);
            COMMON::result(200, '删除成功', $row);
        } else {
            COMMON::result(300, '删除成失败');
        }

    }
    /*
        * ******************
        * *****************
        *
        * */

}
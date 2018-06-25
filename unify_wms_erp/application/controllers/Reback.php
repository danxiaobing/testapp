<?php

class RebackController extends Yaf_Controller_Abstract
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

        $this->getView()->make('reback.list',$params);
    }

    public function listJsonAction() {
        $request = $this->getRequest();

        $search = array (
            'begin_time'=> $request->getPost('begin_time',''),
            'end_time' => $request->getPost('end_time',''),
            'carid' => trim($request->getPost('carid','')),
            'stockinstatus' => $request->getPost('stockinstatus',''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'orders'  => 'created_at desc',
        );
//print_r($search);die;
        $B = new RebackModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $B->searchReback($search);

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

        $R = new RebackModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if (!$id) {
            $action = "/reback/newJson/";
            $params = array();
        } else {
            $params = $R->getrebackInfoById($id);
            $status = $params['stockinstatus'];

            if($mode == 'edit'){
                if($status == 2||$status == 6){
                    $action = '/reback/editJson';
                }
                else{
                    COMMON::result(300, '非暂存或退回状态不能编辑！');
                    return;
                }
            }elseif($mode == 'audit'){
                if ($status != 3) {
                    COMMON::result(300, '该状态无法审核');
                    return;
                }
                $action = '/reback/auditJson1';
            }elseif($mode == 'view'){
                $action = '/reback/view';
            }


        }

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
//print_r($params);die;
        $this->getView()->make('reback.detail', $params);
    }

    /*
     * 新建退货单
     */
    public function newJsonAction()
    {
        $request = $this->getRequest();
        $status = $request->getPost('status', '');
        $detaildata = $request->getPost('reback_detail_data', "");
        $detaildata = json_decode($detaildata, true);

        if (count($detaildata) == 0) {
            COMMON::result(300, '入库明细信息不能为空');
            return;
        }
        foreach($detaildata as $key=>$value){
            if($value['rebacknumber']==''){
                COMMON::result(300, '退货数量不能为空');
            } else{
                if($value['rebacknumber']>$value['realnumber']){
                    COMMON::result(300, '退货数量不能大于提货数量');
                }
            }
        }

        //主表数据
        $input = array(
            'stockrebackno' => COMMON::getCodeId('TH'),
            'poundsout_sysno' => $request->getPost('poundsout_sysno', ''),
            'poundsoutno' => $request->getPost('poundsoutno', ''),
            'stockrebackdate' => $request->getPost('stockrebackdate', ''),
            'docsource' => $request->getPost('docsource', '1'),
            'goods_sysno' => $request->getPost('goods_sysno', '1'),
            'goodsname' => $request->getPost('goodsname', ''),
            'carid' => $request->getPost('carid', ''),
            'takegoodsnumber' => $request->getPost('beqty', ''),
            'cs_employee_sysno' =>$request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'zj_employee_sysno' => $request->getPost('zj_employee_sysno', ''),
            'zj_employeename' => $request->getPost('zj_employeename', ''),
            'stockinstatus' => $request->getPost('status', ''),
            'status' => 1,
            'isdel' => 0,
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        //  print_r($request->getRequest());
        //   print_r($detaildata);
      //   print_r($input);die;
        $R = new RebackModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $res = $R->addReback($input,$detaildata,$status);
        if ($res['code']==200) {
             $search = array('sysno'=>$res['msg'],'page'=>false);
             $row = $R->searchReback($search);
             COMMON::result(200, '添加成功', $row);
        } else {
             COMMON::result(300, '添加失败'.$res['msg']);
        }
    }

    /*
     * 编辑退货单
     */
    public function editJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $status = $request->getPost('status', '');
        $stockinstatus = $request->getPost('stockinstatus', '');
        $detaildata = $request->getPost('reback_detail_data', "");
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
            if($value['rebacknumber']==''){
                COMMON::result(300, '退货数量不能为空');
            } else{
                if($value['rebacknumber']>$value['realnumber']){
                    COMMON::result(300, '退货数量不能大于提货数量');
                }
            }
        }

        //主表数据
        $input = array(
            'poundsout_sysno' => $request->getPost('poundsout_sysno', ''),
            'poundsoutno' => $request->getPost('poundsoutno', ''),
            'stockrebackdate' => $request->getPost('stockrebackdate', ''),
            'docsource' => $request->getPost('docsource', '1'),
            'goods_sysno' => $request->getPost('goods_sysno', '1'),
            'goodsname' => $request->getPost('goodsname', ''),
            'carid' => $request->getPost('carid', ''),
            'takegoodsnumber' => $request->getPost('beqty', ''),
            'cs_employee_sysno' =>$request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'zj_employee_sysno' => $request->getPost('zj_employee_sysno', ''),
            'zj_employeename' => $request->getPost('zj_employeename', ''),
            'stockinstatus' => $request->getPost('status', ''),
            'status' => 1,
            'isdel' => 0,
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );


//        print_r($id);
//        print_r($input);
//        print_r($detaildata);
//        print_r($stockinstatus);
//        print_r($status);die;
        $R = new RebackModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $res= $R->updateReback($id,$input,$detaildata,$stockinstatus,$status);

        if ($res['code']==200) {
            $search = array('sysno'=>$res['msg'],'page'=>false);
            $row = $R->searchReback($search);
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
        $status = $request->getPost('status', '');

        $R = new RebackModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if(!$id){
            COMMON::result(300, '数据异常丢失id');
            return;
        }
         $search = array(
             'sysno' =>$id,
             'page'=>false,
         );
        $detaildata = $R->getRebackdetailById($search)['list'];

        if (count($detaildata) == 0) {
            COMMON::result(300, '退货明细不能为空');
            return;
        }
        foreach($detaildata as $key=>$value){
            if($value['rebacknumber']==''){
                COMMON::result(300, '退货数量不能为空');
            } else{
                if($value['rebacknumber']>$value['realnumber']){
                    COMMON::result(300, '退货数量不能大于提货数量');
                }
            }
        }
        #var_dump($booklist['docsource']);exit();
        //获取主表信息
        $input  =  $R->getrebackInfoById($id);
//        print_r($input);
//        print_r($detaildata);die;

        if ($status == 6) {
            if ($request->getPost('auditreason') == '') {
                COMMON::result(300, '审核备注不能为空');
                return;
            }
        }

        $res = $R->auditReback($id, $input, $detaildata, $status,$request->getPost('auditreason'));
        if ($res['code']==200) {
            $search = array('sysno'=>$id,'page'=>false);
            $row = $R->searchReback($search);
            COMMON::result(200, '审核成功', $row);
        } else {
            COMMON::result(300, $res['message']);
        }
    }
    /*
       * 修改退货单明细页面
       */
    public function rebackdetaileditAction()
    {
        $request = $this->getRequest();
        $stockinstatus = $request->getParam('stockinstatus', '0');
        $id = $request->getParam('id', '0');
        $handlestatus = $request->getParam('handlestatus','0');
        $mode = $request ->getParam('mode','');

        $params = $request->getPost('datadetail',array());
        $R = new RebackModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
   //     print_r($id);
         if($id && !$params['pounddetail_sysno']){
            //获取退货明细
             $search = array(
                 'detail_sysno'=>$params['sysno'],
                 'page'=>false,
             );

             $detail = $R->getRebackdetailById($search);

             $params['list'] = $detail['list'][0];
             $params['list']['stock_sysno'] = $params['list']['doc_sysno'];
//             print_r($detail);die;
         }else{
            //获取磅码明细
             $pounddetail_sysno = $params['pounddetail_sysno'];
             $search = array(
                 'pd_sysno'=>$pounddetail_sysno,
                 'page' => false,
             );
             $pounddetail = $R->getPoundDetailByid($search);
             $params['list'] = $pounddetail['list'];
         }


        $params['handlestatus'] = $handlestatus;
        $params['mode'] = $mode;

      //    print_r($params);die;
        $this->getView()->make('reback.addedit', $params);
    }


    //获取磅码单明细-基础放大镜使用
    public function poundsAllJsonAction() {
        $request = $this->getRequest();

        $search = array (
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,

        );

        $R = new RebackModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $R->getPounds($search);

        echo json_encode($list['list']);

    }
    //reload到明细表中-放大镜选择回调数据
    public function getdetailAction(){
        $request = $this->getRequest();
        $id = $request->getPost('sysno','');
     //   print_r($id);
        $search = array (
            'bar_status' => '1',
            'bar_isdel' => '0',
            'sysno'=>$id,
            'page' => false,

        );

        $R = new RebackModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $R->getPoundDetail($search);
    //   print_r($list);die;

        echo json_encode($list);

    }

    /*
    *  编辑明细列表 dataurl 加载明细数据
    */
    public function getdetailJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');

        $R = new RebackModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $search = array(
            'sysno'=>$id,
            'page'=>false,
        );

        $list = $R->getRebackdetailById($search);
        foreach($list['list'] as $key=>$value )
        {
            $list['list'][$key]['stock_sysno'] = $value['doc_sysno'];
        }

        //print_r($list);die;
        echo json_encode($list);
    }

    //删除方法
    public  function deleteAction(){
        $request = $this->getRequest();
        $data = $request->getPost('selectdata', "");

        $id = $data[0]['sysno'];
        $stockinstatus = $data[0]['stockinstatus'];
        if($stockinstatus==4 || $stockinstatus==7){
            COMMON::result(300, '已审核的单据不能删除');
            return;
        }
        $R = new RebackModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $status = $R->getStatusRebackById($id);

        if($status){
            COMMON::result(300, '非暂存和待审核状态不能删除');
            return;
        }
        $statuspound = $R->getrebackpoundsInfoBystockinsysno($id);
        if($statuspound){
            COMMON::result(300, '已生成退货磅码单不能删除');
            return;
        }
        $input = array(
            'isdel'=>1,
            'updated_at' => '=NOW()'
        );

        $poundsout_sysno = $data[0]['poundsout_sysno'];
        $res = $R->delReback($id,$input,$poundsout_sysno);
        if ($res['code']==200) {
            $search = array('sysno'=>$id,'page'=>false);
            $row = $R->searchReback($search);
            COMMON::result(200, '删除成功', $row);
        } else {
            COMMON::result(300, '删除成失败');
        }

    }

    //退货单作废
    public  function cancellation1Action(){
        $request = $this->getRequest();
        $data = $request->getPost('selectdata', "");

        $id = $data[0]['sysno'];
        $stockinstatus = $data[0]['stockinstatus'];
        
        $R = new RebackModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $info = $R->getrebackpoundsInfoBystockinsysno($id);
        if($stockinstatus!=4){
            COMMON::result(300, '非退货中单据不能作废');
            return;
        }
        if($info){
            COMMON::result(300, '已生成磅单据不能作废');
            return;
        }
        $input = array(
            'stockinstatus'=>7,
            'updated_at' => '=NOW()'
        );


        $res = $R->cancellationReback1($id,$input);
        if ($res['code']==200) {
            $search = array('sysno'=>$id,'page'=>false);
            $row = $R->searchReback($search);
            COMMON::result(200, '作废成功', $row);
        } else {
            COMMON::result(300, '作废失败');
        }

    }

    //完成退货
    public  function rebackdoneAction(){
        $request = $this->getRequest();
        $data = $request->getPost('selectdata', "");

        $id = $data[0]['sysno'];
        $stockinstatus = $data[0]['stockinstatus'];

        $R = new RebackModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $status = $R->getStatusRebackpoundsById($id);

        if($stockinstatus!=4){
            COMMON::result(300, '非退货中单据不能进行完成退货操作');
            return;
        }
        if(!$status){
            COMMON::result(300, '未过磅单据不能进行完成退货操作');
            return;
        }

        $input = array(
            'stockinstatus'=>8,
            'updated_at' => '=NOW()'
        );


        $res = $R->doneReback($id,$input);
        if ($res['code']==200) {
            $search = array('sysno'=>$id,'page'=>false);
            $row = $R->searchReback($search);
            COMMON::result(200, '完成退货成功', $row);
        } else {
            COMMON::result(300, '完成退货失败');
        }

    }

    /**
     * @title 审批方法
     */
    public function auditJson1Action()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $status = $request->getPost('status', '');

        $R = new RebackModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if(!$id){
            COMMON::result(300, '数据异常丢失id');
            return;
        }
        $search = array(
            'sysno' =>$id,
            'page'=>false,
        );
        $detaildata = $R->getRebackdetailById($search)['list'];

        if (count($detaildata) == 0) {
            COMMON::result(300, '退货明细不能为空');
            return;
        }
        foreach($detaildata as $key=>$value){
            if($value['rebacknumber']==''){
                COMMON::result(300, '退货数量不能为空');
            } else{
                if($value['rebacknumber']>$value['realnumber']){
                    COMMON::result(300, '退货数量不能大于提货数量');
                }
            }
        }
        //获取主表信息
        //$input  =  $R->getrebackInfoById($id);
        if ($status == 6) {
            if ($request->getPost('auditreason') == '') {
                COMMON::result(300, '审核备注不能为空');
                return;
            }
        }
        $updata = array(
            'updated_at' => '=NOW()'
        );

        $res = $R->auditReback1($id, $updata, $status,$request->getPost('auditreason'));

        if ($res['code']==200) {
            $search = array('sysno'=>$id,'page'=>false);
            $row = $R->searchReback($search);
            COMMON::result(200, '审核成功', $row);
        } else {
            COMMON::result(300, $res['message']);
        }
    }


}
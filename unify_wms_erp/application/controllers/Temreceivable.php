<?php
/**
 * Created by PhpStorm.
 * User: jp
 * Date: 2017/07/05 0017
 * Time: 10:35
 */
class TemreceivableController extends Yaf_Controller_Abstract
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
     * 临时收款单
     * @author jp
     */

    public function listAction()
    {
        //业务类型
        $params = array();
        //客户列表
        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $C->searchCustomer(['page' => false,'bar_status'=>1]);
        $params['customerlist'] =  $list['list'];
        //  print_r($params);die;
        $this->getView()->make('temreceivable.list', $params);
    }

    /**
     * 片区管理列表JSON
     * @author jp
     */

    public function ListJsonAction()
    {
        $request = $this->getRequest();

        $search = array (
            'startTime' => $request->getPost('startTime',''),
            'endTime' => $request->getPost('endTime',''),
            'customername'   => $request->getPost('customername',''),
            'receivablestatus'   => $request->getPost('receivablestatus',''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'page'=>false,
        );
//print_r($search);die;
        $T = new TemreceivableModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $list = $T->searchTem($search);

        echo json_encode($list);
    }


    public function EditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $mode = $request->getParam('mode','');

        if(!isset($id)) {
            $id = 0;
        }

        $T = new TemreceivableModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        if(!$id){
            $action = "/Temreceivable/newJson/";
            $params =  array();
        } else {
            if ($mode == 'edit') {
                $action = "/Temreceivable/editJson/";
            } elseif ($mode == 'audit') {
                $action = "/Temreceivable/auditJson/";
            } elseif ($mode == 'back') {
                $action = "/Temreceivable/backJson/";
            }
            $search = array(
                'id' => $id,
                'page' => false,
            );
            $row = $T->searchTem($search);
      //      print_r($row);die;
            //  $type = $request->getParam('type','');
            if ($row['list'][0]['receivablestatus'] != 2 && $row['list'][0]['receivablestatus'] != 6 && $mode == 'edit') {
                COMMON::result(300, '只能选择暂存或退回状态的数据');
                return;
            }

            $params['list'] = $row['list'][0];

        }
        $params['id'] =  $id;
        $params['mode'] =  $mode;
        $params['action'] =  $action;

        //获取开票公司资料
        $S = new CompanyModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $search = array (
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),
            'page'=>false

        );
        $company = $S->searchCompany($search);
        $params['company'] = $company['list'];
//    print_r($company);die;
        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $C->searchCustomer(['page' => false,'bar_status'=>1]);
        $params['customerlist'] =  $list['list'];
        // print_r($params);die;
        $this->getView()->make('temreceivable.edit', $params);
    }

/*
 * 编辑数据处理
 * */
    public function EditJsonAction()
    {

        $request = $this->getRequest();
        $id = $request->getPost('id', '');
        $step = $request->getPost('temreceivablestatus', 0);
        if(!$id){
            COMMON::result(300,'数据异常');
            return;
        }

        //明细表信息
        //  print_r($request->getRequest());die;
        $details = $request->getPost('temdetaildata','');
        $details = json_decode($details,true);
        if (count($details) == 0) {
            COMMON::result(300, '临时收费单明细不能为空');
            return;
        }
        if(1<count($details)){
            $is_costname = array();
            foreach($details as $item){
                if(!in_array(trim($item['costname']),$is_costname)){
                    $is_costname[] = trim($item['costname']);
                }elseif(in_array(trim($item['costname']),$is_costname)){
                    COMMON::result(300, '临时收费单明细不能重复');
                    return;
                }
            }
        }
        $T= new TemreceivableModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        //主表信息
        $input = array(
            'receivabledate' => $request->getPost('receivabledate', ''),       //收款单日期
            'customer_sysno' => $request->getPost('customer_sysno', 0),       //客户id
            'customername' => $request->getPost('customername', 0),             //客户
            'invoice_startdate' => $request->getPost('invoice_startdate', ''),  //结算日期开始
            'receivablestatus' => $request->getPost('receivablestatus'),                             //提交的状态
            'invoice_enddate' => $request->getPost('invoice_enddate', ''),        //结算日期结束
            'base_company_sysno' => $request->getPost('base_company_sysno', ''), // 收款开票单位
            'base_companyname' => $request->getPost('base_companyname', ''),      //收款开票单位名称
            'goodsname' => $request->getPost('goodsname', ''),                        //货品名
            'costreceivable' => $request->getPost('costreceivable', ''),               //收款额
            'invoice_company_sysno' => $request->getPost('invoice_company_sysno', ''), //开票抬头公司id
            'invoice_companyname' => $request->getPost('invoice_companyname', ''),   //开票抬头公司名称
            'costtype' => 2,                                                       //收款单类型:1仓储费收款2临时收款
            'status' => 1,
            'isdel' => 0,
            'updated_at' => "=NOW()"
        );
        //    print_r($input);
        //   print_r($details);die;

        $res = $T->updateTemreceivable($input,$details,$id,$step);

        if($res['statusCode']==200){
            $params['id'] = $res['msg'];
            $params['page'] = false;
            $row = $T->searchTem($params);
            COMMON::result(200, '更新成功',$row);
        } else {
            COMMON::result(300, '更新失败：'.$res['msg']);
        };
    }
/*
 *审核数据处理
 * */
    public function auditJsonAction()
    {

        $request = $this->getRequest();
        $id = $request->getPost('id', '');
        $step = $request->getPost('temreceivablestatus', 0);
        if(!$id){
            COMMON::result(300,'数据异常');
            return;
        }
         if($step==6){
             $auditreason = $request->getPost('auditreason','');
         }

        //明细表信息
        // print_r($request->getRequest());die;
        $details = $request->getPost('temdetaildata','');
        $details = json_decode($details,true);
        if (count($details) == 0) {
            COMMON::result(300, '临时收费单明细不能为空');
            return;
        }

        $T= new TemreceivableModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        //主表信息
        $input = array(
            'receivablestatus' => $request->getPost('receivablestatus'),                             //提交的状态
            'auditreason' => $auditreason,
            'updated_at' => "=NOW()"
        );
        //    print_r($input);
        //   print_r($details);die;

        $res = $T->auditTemreceivable($input,$details,$id,$step);

        if($res['statusCode']==200){
            $params['id'] = $res['msg'];
            $params['page'] = false;
            $row = $T->searchTem($params);
            COMMON::result(200, '审核成功',$row);
        } else {
            COMMON::result(300, '审核失败：'.$res['msg']);
        };
    }

    /*
     *作废数据处理
     * */
    public function backJsonAction()
    {

        $request = $this->getRequest();
        $id = $request->getPost('id', '');
        $step = $request->getPost('temreceivablestatus', 0);
        if(!$id){
            COMMON::result(300,'数据异常');
            return;
        }
        if($step==6){
            $abandonreason = $request->getPost('$abandonreason','');
        }

        //明细表信息
      //  print_r($request->getRequest());die;
        $details = $request->getPost('temdetaildata','');
        $details = json_decode($details,true);
        if (count($details) == 0) {
            COMMON::result(300, '临时收费单明细不能为空');
            return;
        }

        $T= new TemreceivableModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        //主表信息
        $input = array(
            'receivablestatus' => $request->getPost('receivablestatus'),                             //提交的状态
            'abandonreason' => $abandonreason,
            'updated_at' => "=NOW()"
        );
        //    print_r($input);
        //   print_r($details);die;

        $res = $T->backTemreceivable($input,$details,$id,$step);

        if($res['statusCode']==200){
            $params['id'] = $res['msg'];
            $params['page'] = false;
            $row = $T->searchTem($params);
            COMMON::result(200, '作废成功',$row);
        } else {
            COMMON::result(300, '作废失败：'.$res['msg']);
        };
    }


    /*
     * 新增临时收费表
     *
     * */
    /**
     * 添加临时收费表 数据操作
     */
    public function newJsonAction()
    {
        //获取所添加的信息
        $request = $this->getRequest();

        $receivablestatus = $request->getPost('temreceivablestatus', 0);
        //明细
        $detaildata = $request->getPost('temdetaildata','');
        $detaildata = json_decode($detaildata, true);
        if (count($detaildata) == 0) {
            COMMON::result(300, '临时收费单明细不能为空');
            return;
        }
       // print_r($detaildata);die;
        if(1<count($detaildata)){
            $is_costname = array();
            foreach($detaildata as $item){
                if(!in_array(trim($item['costname']),$is_costname)){
                    $is_costname[] = trim($item['costname']);
                }elseif(in_array(trim($item['costname']),$is_costname)){
                    COMMON::result(300, '临时收费单明细不能重复');
                    return;
                }
            }
        }
     // print_r($request->getRequest());die;

        $input = array(
            'receivableno' => COMMON::getCodeId('L'),                         //收款单编号
            'receivabledate' => $request->getPost('receivabledate', ''),       //收款单日期
            'customer_sysno' => $request->getPost('customer_sysno', 0),       //客户id
            'customername' => $request->getPost('customername', 0),             //客户
            'invoice_startdate' => $request->getPost('invoice_startdate', ''),  //结算日期开始
            'receivablestatus' => $receivablestatus,                             //提交的状态
            'invoice_enddate' => $request->getPost('invoice_enddate', ''),        //结算日期结束
            'base_company_sysno' => $request->getPost('base_company_sysno', ''), // 收款开票单位
             'base_companyname' => $request->getPost('base_companyname', ''),      //收款开票单位名称
            'goodsname' => $request->getPost('goodsname', ''),                        //货品名
            'costreceivable' => $request->getPost('costreceivable', ''),               //收款额
            'invoice_company_sysno' => $request->getPost('invoice_company_sysno', ''), //开票抬头公司id
            'invoice_companyname' => $request->getPost('invoice_companyname', ''),   //开票抬头公司名称
            'costtype' => 2,                                                       //收款单类型:1仓储费收款2临时收款
            'status' => 1,
            'isdel' => 0,
            'created_at' => "=NOW()",
            'updated_at' => "=NOW()"
        );
        $T= new TemreceivableModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        //print_r($input);
        //print_r($detaildata);die;
        $res = $T->addTemreceivable($input,$detaildata);

        if($res['statusCode']==200){
            $params['id'] = $res['msg'];
            $params['page'] = false;
            $row = $T->searchTem($params);
            COMMON::result(200, '新增成功');
        } else {
            COMMON::result(300, '新增失败：'.$res['msg'] );
        };
    }


    /*
     * 明细表json
     *
     *  */
    public function detailJsonAction(){

        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        if(!isset($id)) {
            $id = 0;
        }
        $P = new TemreceivableModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if($id){
            $where['sysno'] = $id;
            $list = $P->getListDetials($where);
        }

        $list = $list?$list:array();
        //  print_r($list);die;
        echo json_encode($list);

    }

    /*
     * 添加页面
     * */
    public function AddeditAction(){
        $request = $this->getRequest();
        $params['list'] = $request->getPost('selectedDatasArray',array());
        $params['type'] = $request->getParam('type','');


        //   print_r($params);die;
        $this->getView()->make('temreceivable.addedit',$params);

    }


    public function DelJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);

        $T = new TemreceivableModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'isdel' => 1
        );

        $oneId = explode(',',$id);
        if(count($oneId)==1){

             $status = $T->searchTem(['id'=>$id,'page'=>false]);

            $receivablestatus = $status['list'][0]['receivablestatus'];

            if($receivablestatus>=3 && $receivablestatus<=5){//
                COMMON::result(300,'只有暂存或退回的单据才能删除');
                return ;
            }

            if ($T->updatedel($id, $input)) {
                COMMON::result(200, '删除成功');
            } else {
                COMMON::result(300, '删除失败');
            }

        }else{
            COMMON::result('300','请选择一条数据');
            return false;
        }




    }
    /*
       * 获取移入储罐罐容信息
       * */
    public function getStocklistJsonAction()
    {
        $P = new PipelineorderModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $params = $P->getStockRetankInfo();

        echo json_encode($params);
    }

    /*
      * 获取所有商品
      * */
    public function getgoodslistJsonAction()
    {
        $P = new PipelineorderModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $params = $P->getgoodsInfo();

        echo json_encode($params);
    }

}
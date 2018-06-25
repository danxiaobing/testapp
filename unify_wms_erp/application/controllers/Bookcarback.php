<?php

class BookcarbackController extends Yaf_Controller_Abstract
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

    /*
    * 显示编辑页面
    */
    public function EditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $mode = $request->getParam('mode','');

        $S = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if (!$id) {
            $action = "/bookcarback/newJson/";
            $params = array();
        } else {
            $params = $S->getBookcarinInfoById($id);
            $status = $params['bookinginstatus'];

            if($mode == 'edit'){
                if($status == 2||$status == 7){
                    $action = '/bookcarin/editJson';
                }
                else{
                    COMMON::result(300, '非暂存或退回状态不能编辑！');
                    return;
                }
            }elseif($mode == 'sure'){
                $action = '/bookcarin/surebookcarin';
            }elseif($mode == 'audit'){
                $action = '/bookcarin/auditJson';
            }elseif($mode == 'back'){
                $action = '/bookcarin/backbookcarin';
            }elseif($mode == 'addattach'){
                $action = '/bookcarin/addcontractattach';
            }elseif($mode =='addcar'){
                $action = "/bookcarin/addcarjson/";
            }

            //暂存状态 编辑
            $params['attach'] = array();
            $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $attach = $A->getAttachByMAS('bookcarback', 'bookcarbackatt', $id);
            $params['attach'] = array_merge($params['attach'], $attach);

            if (is_array($attach) && count($attach)) {
                $files1 = array();
                foreach ($attach as $file) {
                    $files1[] = $file['sysno'];
                }
                $params['uploaded1'] = join(',', $files1);
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

        $this->getView()->make('bookcarback.bookcarbackedit', $params);
    }

    /*
     * 新建车入库预约单
     */
    public function newJsonAction()
    {
        $request = $this->getRequest();
        $bookinginstatus = $request->getPost('bookinginstatus', '');
        $bookcarbackdetaildata = $request->getPost('bookcarbackdetaildata', "");
        $bookcarbackdetaildata = json_decode($bookcarbackdetaildata, true);

        $bookcarincarsdata = $request->getPost('bookcarincarsdata', "");
        $bookcarincarsdata = json_decode($bookcarincarsdata, true);
        if (count($bookcarbackdetaildata) == 0) {
            COMMON::result(300, '入库明细信息不能为空');
            return;
        }
        if (count($bookcarbackdetaildata) > 1) {
            COMMON::result(300, '只能添加一行明细');
            return;
        }
        if($bookcarbackdetaildata[0]['stock_sysno']){
            $ST = new StockModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $search = array(
                'stock_sysno'=>$bookcarbackdetaildata[0]['stock_sysno'],
                'page'=>false,
            );
            $stockinfo = $ST ->getList($search);
        }

        $G = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $T = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        foreach ($bookcarbackdetaildata as $key => $value) {
            $bookcarbackdetaildata[$key]['goodsname'] = $value['goodsname'];
            $bookcarbackdetaildata[$key]['goods_sysno'] = $value['goods_sysno'];
            $bookcarbackdetaildata[$key]['unitname'] = $value['unitname'] ? $value['unitname'] : '吨';
            $detailarr[$bookcarbackdetaildata[$key]['goods_sysno']] = 1;

            $storagetank_sysno = $stockinfo['list'][0]['storagetank_sysno'];
            $goods_sysno = $bookcarbackdetaildata[$key]['goods_sysno'];
            $bookcarbackdetaildata[$key]['storagetank_sysno'] = $storagetank_sysno;

            if($storagetank_sysno){
                if ($T->storagetankgoodsbyid($storagetank_sysno)!=$goods_sysno) {

                    COMMON::result(300, '该储罐还有其他货品存量');
                    return;
                }
            }

            $detailtankarr[$storagetank_sysno] = $value['bookinginqty'];
        }

        if (count($detailarr) > 1) {
            COMMON::result(300, '只能添加一种货品');
            return;
        }


//print_r($stockinfo);die;


        $S = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $B = new BookcarbackModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));


        $input = array(
            'stockintype' => $request->getPost('stockintype', '2'),
            'bookinginno' => COMMON::getCodeId('BT'),
            'bookingindate' => $request->getPost('bookingindate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customer_name' => $request->getPost('obj_customer_name', ''),
            'contract_sysno' => $stockinfo['list'][0]['contract_sysno'],
            'contract_no' => $stockinfo['list'][0]['contractno'],
        //    'contracttype' => $contracttype,
            'docsource' => $request->getPost('docsource', '1'),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'takegoodsno' =>$request->getPost('takegoodsno', ''),
            'carname' => $request->getPost('carname', ''),
            'issave' => $request->getPost('issave', '1'),
            'isbusinesscheck' => $request->getPost('isbusinesscheck', ''),
            'businesschecktype' => $request->getPost('businesschecktype', ''),
            'businesscheckunitname' => $request->getPost('businesscheckunitname', ''),
            'bookinginstatus' => $request->getPost('bookinginstatus', '2'),
            'isqualitycheck' => $request->getPost('isqualitycheck', 1),
            'ispipelineorder'=>2,
            'isberthorder'=>2,
            'isreback'=>2,
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        //若车辆信息不在数据库中，则登记车辆信息
        if($bookcarincarsdata){
            $Su = new SupplierModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $Su -> checkCardata($bookcarincarsdata);
        }

        if(count($bookcarincarsdata)>=2){
            for($i=0;$i<count($bookcarincarsdata);$i++){
                for($j=$i+1;$j<count($bookcarincarsdata);$j++){
                    if($bookcarincarsdata[$i]['carid']==$bookcarincarsdata[$j]['carid']&&
                        $bookcarincarsdata[$i]['carname']==$bookcarincarsdata[$j]['carname']){
                        COMMON::result(300, '车辆信息不能重复');
                        return;
                    }
                }
            }
        }
//        print_r($input);
//        print_r($bookcarbackdetaildata);
//        print_r($bookcarincarsdata);die;
        $res = $B->addBookcarback($input, $bookcarbackdetaildata, $bookcarincarsdata,$bookinginstatus);
        if ($res['code']==200) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($res['msg'], $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $S->getBookcarinById($res['msg']);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败'.$res['msg']);
        }
    }

    /*
     * 编辑车入库预约单
     */
    public function editJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $T = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $bookinginstatus = $request->getPost('bookinginstatus', '');

        $bookcarindetaildata = $request->getPost('bookcarindetaildata', "");
        $bookcarincarsdata = $request->getPost('bookcarincarsdata', "");
        $bookcarindetaildata = json_decode($bookcarindetaildata, true);
        $bookcarincarsdata = json_decode($bookcarincarsdata, true);
        if (count($bookcarindetaildata) == 0) {
            COMMON::result(300, '入库预约单明细不能为空');
            return;
        }

        foreach ($bookcarindetaildata as $key => $value) {
            $bookcarindetaildata[$key]['goodsname'] = $value['goodsname'];
            $bookcarindetaildata[$key]['goods_sysno'] = $value['goods_sysno'];
            $bookcarindetaildata[$key]['unitname'] = $value['unitname'] ? $value['unitname'] : '吨';
            $detailarr[$bookcarindetaildata[$key]['goods_sysno']] = 1;

            $storagetank_sysno = $value['storagetank_sysno'];
            $goods_sysno = $bookcarindetaildata[$key]['goods_sysno'];

            if($storagetank_sysno){
                if ($T->storagetankgoodsbyid($storagetank_sysno)!=$goods_sysno) {

                    COMMON::result(300, '该储罐还有其他货品存量');
                    return;
                }
            }

            $detailtankarr[$storagetank_sysno] = $value['bookinginqty'];
        }
        if (count($detailarr) > 1) {
            COMMON::result(300, '只能添加一种货品');
            return;
        }
        foreach ($detailtankarr as $key => $value) {
            $search = array(
                'storagetank_sysno' => $key,
            );
            $available = $T->getStoragetankavailable($search);
            if ($available < $value) {
                COMMON::result(300, '该储罐可存放容量不足,当前储罐可用容量:' . $available . '吨');
                return;
            }
        }

        $S = new BookcarinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $contracttypedata = $C->getContractById($request->getPost('contract_sysno', '0'));
        if ($contracttypedata) {
            $contracttype = $contracttypedata['contracttype'];
        }

        $input = array(
            'stockintype' => $request->getPost('stockintype', '2'),
            'bookinginno' => $request->getPost('bookinginno', ''),
            'bookingindate' => $request->getPost('bookingindate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customer_name' => $request->getPost('obj_customer_name', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contract_no' => $request->getPost('contract_no', ''),
            'contracttype' => $contracttype,
            'docsource' => $request->getPost('docsource', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'takegoodsno' =>$request->getPost('takegoodsno', ''),
            'carname' => $request->getPost('carname', ''),
            'issave' => $request->getPost('issave', '1'),
            'isbusinesscheck' => $request->getPost('isbusinesscheck', ''),
            'businesschecktype' => $request->getPost('businesschecktype', ''),
            'businesscheckunitname' => $request->getPost('businesscheckunitname', ''),
            'isqualitycheck' => $request->getPost('isqualitycheck', ''),
            'bookinginstatus' => $request->getPost('bookinginstatus', ''),
            'rejectreason' => $request->getPost('rejectreason', ''),
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        //若车辆信息不在数据库中，则登记车辆信息
        if($bookcarincarsdata){
            $Su = new SupplierModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $Su -> checkCardata($bookcarincarsdata);
        }

        if(count($bookcarincarsdata)>=2){
            for($i=0;$i<count($bookcarincarsdata);$i++){
                for($j=$i+1;$j<count($bookcarincarsdata);$j++){
                    if($bookcarincarsdata[$i]['carid']==$bookcarincarsdata[$j]['carid']&&
                        $bookcarincarsdata[$i]['carname']==$bookcarincarsdata[$j]['carname']){
                        COMMON::result(300, '车辆信息不能重复');
                        return;
                    }
                }
            }
        }

        $bookcarininfo = $S->getBookcarinById($id);
        if($bookcarininfo['bookinginstatus']==7){
            $input['bookinginstatus'] = 7;
        }

        if ($S->updateBookcarin($id, $input, $bookcarindetaildata, $bookcarincarsdata, $bookinginstatus)) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $S->getBookcarinById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }


    /*
       * 添加车入库预约单明细页面
       */
    public function bookcarbackdetaileditAction()
    {
        $request = $this->getRequest();
        $cid = $request->getParam('cid', '0');
        $handlestatus = $request->getParam('handlestatus','0');
        $mode = $request ->getPost('mode','');

        $params = $request->getPost('selectedDatasArray',array());

//        $S = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
//        $params['storagetanklist'] = $S->getStoragetank2();

        $search = array(
            'customer_sysno' => $cid,
            'page' => false,
            'orders' => $request->getPost('orders', ''),

        );

        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $quality->getList($search, 99, 1);
        $params['goodsqualitylist'] = $list['list'];

        $params['customer_sysno'] = $cid;
        $params['handlestatus'] = $handlestatus;
        $params['mode'] = $mode;
//print_r($params);die;
        $this->getView()->make('bookcarback.bookcarbackadddetail', $params);
    }

    /*
     * 添加车入库预约单明细数据
     */
    public function adddetailJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');

        $S = new BookcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getBookcarindetailById($id);

        if ($id && !empty($list)) {

            foreach ($list as $key => $value) {
                $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $data = $quality->getQualityById($value['goods_quality_sysno']);
                $list[$key]['qualityname'] = $data['qualityname'];
                $list[$key]['goods_sysno'] = $value['goods_sysno'];
                $list[$key]['goodsname'] = $value['goodsname'];
                $list[$key]['carid'] = $value['carid'];
            }
        }
        echo json_encode($list);
    }

    /*
     * @title 查询正在出库的库存记录
     * @author jp
     */
    public function stockoutinglistJsonAction() {
        $request = $this->getRequest();

        $search = array (
            'customer_sysno'=>$request->getPost('customer_sysno',0),
            'orders'  => $request->getPost('orders',''),
            'page'    => false,
        );

      //  print_r($search);die;
        $B = new BookcarbackModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

        $data = $B->getstockcaringdetail($search);

        foreach($data['list'] as $key=>$value){
            if($value['goodsnature']==1){
                $data['list'][$key]['goodsnature_name'] = '保税';
            }elseif($value['goodsnature']==2){
                $data['list'][$key]['goodsnature_name'] = '外贸';
            }elseif($value['goodsnature']==3){
                $data['list'][$key]['goodsnature_name'] = '内贸转出口';
            }elseif($value['goodsnature']==4){
                $data['list'][$key]['goodsnature_name'] = '内贸内销';
            }


        }
//print_r($data);die;
        $list = $data;

        echo json_encode($list);

    }

    /*
        * 添加车入库预约单车辆信息页面
        */
    public function bookcarbackeditcarsdetailAction()
    {
        $request = $this->getRequest();
        $cid = $request->getParam('cid', '0');
        $handlestatus = $request->getParam('handlestatus','0');
        $params = $request->getPost('selectedDatasArray',array());

        $params['customer_sysno'] = $cid;
        $params['handlestatus'] = $handlestatus;

        $this->getView()->make('bookcarback.bookcarbackaddcars', $params);
    }

    /*
     * 添加车入库预约单车辆信息数据
     */
    public function addcarsdetailJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');

        $S = new BookcarinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getBookcarincarsById($id);

        echo json_encode($list);
    }


}
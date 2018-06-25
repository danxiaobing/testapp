<?php
/**
 * 管入库预约
 * User: HR
 * Date: 2017/07/12 
 */
class BookpipelineinController extends Yaf_Controller_Abstract {
	/**
	 * IndexController::init()
	 *
	 * @return void
	 */
	public function init() {
		# parent::init();
    }

    /**
     * 显示管入库预约列表页面
     */
    public function listAction()
    {
        $params = array(
            'bar_no' => '',
            'bar_name' => ''
        );

        $params = array(
            'page' => false
        );
        $c = new CustomerModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $customerlist = $c->searchCustomer($params);
        $params['customerlist'] = $customerlist['list'];

        $this->getView()->make('bookpipelinein.list', $params);
    }

    /*
     * 获取管入库预约列表数据
     */
    public function listJsonAction()
    {
        $request = $this->getRequest();
        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'bar_status' => $request->getPost('bar_status', '-100'),
            'bar_isdel' => $request->getPost('bar_isdel', '-100'),
            'bar_stockintype' => $request->getPost('bar_stockintype', ''),
            'bar_bookinginstatus' => $request->getPost('bar_bookinginstatus', '-100'),
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'docsource' => $request->getPost('docsource', ''),
        );
        $S = new BookpipelineinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchBookshipin($search);
        echo json_encode($list);
    }

    /*
     * 显示管入库预约编辑页面
     */
    public function editAction()
    {

		$request = $this->getRequest();
        $id = $request->getParam('booking_sysno', 0);
        $type = $request->getParam('type', '');

        $S = new BookpipelineinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params = array();
        if (!$id) {
            $action = '/Bookpipelinein/newJson/';
        } else {
            $params = $S->getBookshipinById($id);
            $params['attach'] = array();
            $status = $params['bookinginstatus'];
            if ($type == 'edit') {
                $action = '/Bookpipelinein/editJson';
                if ($status != 7 && $status != 2) {
                    COMMON::result(300, '该状态不能编辑！');
                    return;
                }
            } elseif ($type == 'sure') {
                $action = '/Bookpipelinein/surebookshipin';
            } elseif ($type == 'review') {
                $action = '/Bookpipelinein/auditJson';
            } elseif ($type == 'back') {
                $action = '/Bookpipelinein/backbookshipin';
            } elseif ($type == 'addattach') {
                $action = '/Bookpipelinein/addAttachJson';
            }

            //添加附件的显示
            $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
            $sysno = $id;
            $attach = $A->getAttachByMAS('bookspipelinein', 'booking', $sysno);
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
        //$list = $C->searchCustomer($search);
        $list = $C->searchCustomerContract($search);
        $params['customerlist'] = $list;
        $params['bookinstatusnamelist'] = array(
            0 => array('id' => 0, 'name' => '新建'),
            2 => array('id' => 2, 'name' => '暂存'),
            3 => array('id' => 3, 'name' => '待确认'),
            4 => array('id' => 4, 'name' => '待审核'),
            5 => array('id' => 5, 'name' => '已审核'),
            6 => array('id' => 6, 'name' => '已完成'),
            7 => array('id' => 7, 'name' => '退回'),
            8 => array('id' => 8, 'name' => '驳回'),
        );
        $params['issavelist'] = array(
            0 => array('id' => "1", 'name' => '是'),
            1 => array('id' => "0", 'name' => '否'),
        );
        $params['isbusinesschecklist'] = array(
            0 => array('id' => "0", 'name' => '否'),
            1 => array('id' => "1", 'name' => '是'),
        );
        $params['businesschecktypelist'] = array(
            0 => array('id' => "1", 'name' => '送检'),
            1 => array('id' => "2", 'name' => '取样'),
        );

        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];
        $params['id'] = $id;
        $params['type'] = $type;
        $params['action'] = $action;

    	return $this->getView()->make('bookpipelinein.edit', $params);
    }

    /*
     * 管入库预约编辑
     */
    public function editJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $bookinginstatus = $request->getPost('bookinginstatus', '');
        $bookpipelineindetaildata = $request->getPost('bookpipelineindetaildata', "");
        $bookpipelineindetaildata = json_decode($bookpipelineindetaildata, true);

        $T = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if (count($bookpipelineindetaildata) == 0) {
            COMMON::result(300, '入库预约单明细不能为空');
            return;
        }
        $tankonly = array();
        $docsource = $request->getPost('docsource', 0);
        if ($docsource != 2) {
            foreach ($bookpipelineindetaildata as $key => $value) {
                $bookpipelineindetaildata[$key]['goodsname'] = $value['goodsname'];
                $bookpipelineindetaildata[$key]['goods_sysno'] = $value['goods_sysno'];
                $bookpipelineindetaildata[$key]['unitname'] = $value['unitname'] ? $value['unitname'] : '吨';
                $detailarr[$bookpipelineindetaildata[$key]['goods_sysno']] = 1;
                $dgdate = $value['goods_quality_sysno'];
                if (!$dgdate) {
                    COMMON::result(300, '请填写预计到港日期');
                    return;
                }
                $qutityname = $value['goods_quality_name'];
                if (!$qutityname) {
                    COMMON::result(300, '请填写货品规格');
                    return;
                }
                $storagetank_sysno = $value['storagetank_sysno'];
                if (!$storagetank_sysno) {
                    COMMON::result(300, '请选择储罐');
                    return;
                }
                if(!in_array($value['storagetank_sysno'],$tankonly)){
                    $tankonly[$key]=$value['storagetank_sysno'];
                }else{
                    COMMON::result(300, '储罐不能重复');
                    return;
                }
                $goods_sysno = $bookpipelineindetaildata[$key]['goods_sysno'];

                if ($T->storagetankgoodsbyid($storagetank_sysno) != $goods_sysno) {
                    COMMON::result(300, $value['storagetankname'] . '还有其他货品存量');
                    return;
                }

            }
        }

        if (count($detailarr) > 1) {
            COMMON::result(300, '只能添加一种货品');
            return;
        }

        $S = new BookpipelineinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $contracttypedata = $C->getContractById($request->getPost('contract_sysno', '0'));
        if ($contracttypedata) {
            $contracttype = $contracttypedata['contracttype'];
        }

        $input = array(
            'stockintype' => $request->getPost('stockintype', '1'),
            'bookinginno' => $request->getPost('bookinginno', ''),
            'bookingindate' => $request->getPost('bookingindate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customer_name' => $request->getPost('obj_customer_name', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contract_no' => $request->getPost('contract_no', ''),
            'contracttype' => $contracttype,
            'docsource' => $request->getPost('docsource', 1),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'issave' => $request->getPost('issave', '1'),
            'isbusinesscheck' => $request->getPost('isbusinesscheck', ''),
            'businesschecktype' => $request->getPost('businesschecktype', ''),
            'businesscheckunitname' => $request->getPost('businesscheckunitname', ''),
            'shipproxyname' => $request->getPost('shipproxyname', ''),
            'flowmemo' => $request->getPost('flowmemo', ''),
            'bookinginstatus' => $bookinginstatus,
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'ispipelineorder'   => $request->getPost('ispipelineorder', ''),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        $stockmarks = $request->getPost('stockmarks', '');
        $res = $S->updateBookpipelinein($id, $input, $bookpipelineindetaildata, $stockmarks, $bookinginstatus);

        if ($res) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $S->getBookshipinById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    /*
     * 显示管入库预约明细页面
     */
    public function detaileditAction()
    {
        $request = $this->getRequest();
        $uid = $request->getParam('uid', '0');
        $cid = $request->getParam('cid', '0');
        $handlestatus = $request->getParam('handlestatus', '0');
        $type = $request->getParam('type', '');
        //预约单状态
        $status = $request->getPost('status', '');
        $params['status'] = $status;
        $params = $request->getPost('selectedDatasArray', array());
        $S = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $params['storagetanklist'] = $S->getStoragetank2();
        $search = array(
            'customer_sysno' => $uid,
            'contract_sysno' => $cid,
            'page' => false,
            'orders' => $request->getPost('orders', ''),
        );

        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $quality->getList($search, 99, 1);
        $params['goodsqualitylist'] = $list['list'];

        $params['type'] = $type;
        $params['customer_sysno'] = $uid;
        $params['contract_sysno'] = $cid;
        $params['handlestatus'] = $handlestatus;

        $this->getView()->make('bookpipelinein.detail', $params);
    }

    /*
     * 新增管入库预约
     */
    public function newJsonAction()
    {
        $request = $this->getRequest();

        $bookinginstatus = $request->getPost('bookinginstatus', '');
        $bookpipelineindetaildata = $request->getPost('bookpipelineindetaildata', ""); //获取管入库预约明细
        $bookpipelineindetaildata = json_decode($bookpipelineindetaildata, true);

        $T = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $tankonly = array();
        foreach ($bookpipelineindetaildata as $key => $value) {
            $bookpipelineindetaildata[$key]['goodsname'] = $value['goodsname'];
            $bookpipelineindetaildata[$key]['goods_sysno'] = $value['goods_sysno'];
            $bookpipelineindetaildata[$key]['unitname'] = $value['unitname'] ? $value['unitname'] : '吨';
            $bookpipelineindetaildata[$key]['goods_quality_name'] = $value['goods_quality_name'];
            $detail[$key] = $bookpipelineindetaildata[$key]['goods_quality_name'];
            $detailarr[$bookpipelineindetaildata[$key]['goods_sysno']] = 1;

            if(!in_array($value['storagetank_sysno'],$tankonly)){
                $tankonly[$key]=$value['storagetank_sysno'];
            }else{
                COMMON::result(300, '储罐不能重复');
                return;
            }
            $dgdate = $value['goods_quality_sysno'];
            if (!$dgdate) {
                COMMON::result(300, '请填写预计到港日期');
                return;
            }
            $qutityname = $value['goods_quality_name'];
            if (!$qutityname) {
                COMMON::result(300, '请填写货品规格');
                return;
            }
            $storagetank_sysno = $value['storagetank_sysno'];
            if (!$storagetank_sysno) {
                COMMON::result(300, '请选择储罐');
                return;
            }
            $goods_sysno = $bookpipelineindetaildata[$key]['goods_sysno'];

            if ($T->storagetankgoodsbyid($storagetank_sysno) != $goods_sysno) {
                COMMON::result(300, $value['storagetankname'] . '还有其他货品存量');
                return;
            }
        }

        if (count($detailarr) > 1) {
            COMMON::result(300, '只能添加一种货品');
            return;
        }

        if (count($detail) > 1) {
            if ($detail[0] != $detail[1]) {
                COMMON::result(300, '请确认同种货品同种规格！');
                return;
            }
        }

        if ($bookinginstatus == 3) {
            if (count($bookpipelineindetaildata) == 0) {
                COMMON::result(300, '入库明细信息不能为空');
                return;
            }
        }

        $S = new BookpipelineinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $C = new ContractModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $contracttypedata = $C->getContractById($request->getPost('contract_sysno', '0'));
        if ($contracttypedata) {
            $contracttype = $contracttypedata['contracttype'];
        }

        $input = array(
            'stockintype' => $request->getPost('stockintype', '3'),
            'bookinginno' => COMMON::getCodeId('R1'),
            'bookingindate' => $request->getPost('bookingindate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customer_name' => $request->getPost('obj_customer_name', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contract_no' => $request->getPost('contract_no', ''),
            'contracttype' => $contracttype,
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'issave' => $request->getPost('issave', '1'),
            'isbusinesscheck' => $request->getPost('isbusinesscheck', ''),
            'businesschecktype' => $request->getPost('businesschecktype', ''),
            'businesscheckunitname' => $request->getPost('businesscheckunitname', ''),
            'shipproxyname' => $request->getPost('shipproxyname', ''),
            'flowmemo' => $request->getPost('flowmemo', ''),
            'bookinginstatus' => $request->getPost('bookinginstatus', '2'),
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
            'ispipelineorder' => $request->getPost('ispipelineorder', ''),
            'isberthorder'    => $request->getPost('isberthorder', ''),
            'isqualitycheck'  => $request->getPost('isqualitycheck', ''),

        );

        $res = $S->addBookshipin($input, $bookpipelineindetaildata, $bookinginstatus);
        if ($res['code']==200) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $ret = $A->addAttachModelSysno($res['message'], $attach);
                if (!$ret) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }
            $row = $S->getBookshipinById($res['message']);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, $res['message']);
        }
    }

    /**
     * 审核管入库预约
     */
    public function auditJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $status = $request->getPost('bookinginstatus', '');
        $bookpipelineindetaildata = $request->getPost('bookpipelineindetaildata', ""); //获取管入库预约明细
        $bookpipelineindetaildata = json_decode($bookpipelineindetaildata, true);

        $S = new BookpipelineinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $L = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $input = array(
            'bookinginstatus' => $status,
            'flowmemo' => $request->getPost('stockmarks'),
            'updated_at' => '=NOW()'
        );

        if($status==5){
            foreach($bookpipelineindetaildata as $key=>$value){
                //添加储罐和货物性质对应关系
                $tankId = $value['storagetank_sysno'];
                $goodsnature = $value['goodsnature'];
                $res = $L->tankTonature($tankId,$goodsnature);
                if($res['code']==300){
                    COMMON::result(300, $res['message']);
                    return;
                }
            }
        }
        $res = $S->AuditBookpipin($id, $input);
        if ($res['code']==200) {
            $row = $S->getBookshipinById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, $res['message']);
        }
    }

    /*
     * 查看管入库预约
     */
    public function showAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);

        $S = new BookpipelineinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params = $S->getBookshipinById($id);
        $params['attach'] = array();
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $sysno = $id;
        $attach = $A->getAttachByMAS('bookspipelinein', 'booking', $sysno);
        $params['attach'] = array_merge($params['attach'], $attach);

        if (is_array($attach) && count($attach)) {
            $files1 = array();
            foreach ($attach as $file) {
                $files1[] = $file['sysno'];
            }

            $params['uploaded1'] = join(',', $files1);
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
        $params['bookinstatusnamelist'] = array(
            0 => array('id' => "", 'name' => '新建'),
            1 => array('id' => 1, 'name' => '新建'),
            2 => array('id' => 2, 'name' => '暂存'),
            3 => array('id' => 3, 'name' => '待确认'),
            4 => array('id' => 4, 'name' => '待审核'),
            5 => array('id' => 5, 'name' => '已审核'),
            6 => array('id' => 6, 'name' => '已完成'),
            7 => array('id' => 7, 'name' => '退回'),
            8 => array('id' => 8, 'name' => '驳回'),
        );
        $params['issavelist'] = array(
            0 => array('id' => "1", 'name' => '是'),
            1 => array('id' => "0", 'name' => '否'),
        );
        $params['isbusinesschecklist'] = array(
            0 => array('id' => "0", 'name' => '否'),
            1 => array('id' => "1", 'name' => '是'),
        );
        $params['businesschecktypelist'] = array(
            0 => array('id' => "1", 'name' => '送检'),
            1 => array('id' => "2", 'name' => '取样'),
        );

        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];
        $params['id'] = $id;

        $Company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $companyname = $Company->getDefault();
        $params['companyname'] = $companyname['companyname'];

        $params['type'] = 'look';

        $this->getView()->make('bookpipelinein.edit', $params);
    }

    /*
     * 删除管入库预约
     */
    public function deljsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);
        $S = new BookpipelineinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $S->getBookshipinById($id);

        if ($params['docsource'] == 2) {
            COMMON::result(300, '云仓数据不能删除！');
            return;
        }

        $params['attach'] = array();
        $status = $params['bookinginstatus'];

        if ($status != 7 && $status != 2) {
            COMMON::result(300, '该状态无法删除！');
            return;
        }
        $input = array(
            'isdel' => 1
        );
        if ($S->delbookpinin($id, $input)) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败');
        }
    }

    /*
     * 显示待审核管入库预约页面
     */
    public function auditAction()
    {
        $params = array(
            'bar_no' => '',
            'bar_name' => ''
        );
        $this->getView()->make('bookpipelinein.reviewlist', $params);        
    }

    /*
     * 获取待审核管入库预约数据
     */
    public function reviewJsonAction()
    {
        $request = $this->getRequest();
        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'bar_bookinginstatus' => 4,
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR()
        );
        $S = new BookpipelineinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $S->searchBookshipin($search);

        echo json_encode($list);
    }

    /**
     * 管入库预约退回
     */
    public function backbookshipinAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id');
        $remarks = $request->getPost('stockmarks', '退回');
        $status = $request->getPost('bookinginstatus');
        if ($remarks == '') {
            COMMON::result(300, '退回信息不能为空');
            return;
        }

        $S = new BookpipelineinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $T = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $bookpipelineindetaildata = $request->getPost('bookpipelineindetaildata', "");
        $bookpipelineindetaildata = json_decode($bookpipelineindetaildata, true);

        foreach ($bookpipelineindetaildata as $key => $item) {
            $storagetank_sysno = $item['storagetank_sysno'];
            $goods_sysno = $bookpipelineindetaildata[$key]['goods_sysno'];
            $flag = $T->storagetankgoodsbyid($storagetank_sysno, $goods_sysno);
            if (!$flag) {
                COMMON::result(300, '该储罐还有其他货品存量');
                return;
            }
        }
        $array = [
            'backreason' => $remarks,
            'bookinginstatus' => $status,
            'updated_at' => '=NOW()'
        ];

        $res = $S->freedBookshipin($id, $array, $bookpipelineindetaildata);
        if ($res) {
            COMMON::result(200, '退回成功!');
        } else {
            COMMON::result(300, '退回失败！');
        }


    }

    /*
     * 导出excel
     */
    public function ExcellistAction()
    {
        $request = $this->getRequest();

        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'bar_status' => $request->getPost('bar_status', '-100'),
            'bar_isdel' => $request->getPost('bar_isdel', '-100'),
            'bar_stockintype' => $request->getPost('bar_stockintype', '3'),
            'bar_bookinginstatus' => $request->getPost('bar_bookinginstatus', '-100'),
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'page' => false,
            'orders' => $request->getPost('orders', ''),

        );
        $S = new BookpipelineinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchBookshipin($search);
        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("管入库预约单")
            ->setSubject("列表")
            ->setDescription("管入库预约单");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '入库预约单号'),
            array('B1:B1', 'B1', '005E9CD3', '单据来源'),
            array('C1:C1', 'C1', '005E9CD3', '客户'),
            array('D1:D1', 'D1', '0094CE58', '合同编号'),
            array('E1:E1', 'E1', '0094CE58', '品名'),
            array('F1:F1', 'F1', '0094CE58', '货物性质'),
            array('G1:G1', 'G1', '0094CE58', '规格'),
            array('H1:H1', 'H1', '003376B3', '计量单位'),
            array('I1:I1', 'I1', '003376B3', '数量'),
            array('J1:J1', 'J1', '0094CE58', '罐号'),
            array('K1:K1', 'K1', '003376B3', '客服'),
            array('L1:L1', 'L1', '003376B3', '单据状态'),
        );

        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('管入库预约单');

        foreach ($mainTitle as $row) {
            $objActSheet->mergeCells($row[0]);
            $objActSheet->setCellValue($row[1], $row[3]);

            $objStyle = $objActSheet->getStyle($row[1]);

            $objStyle->getAlignment()->setHorizontal("center");
            $objStyle->getAlignment()->setVertical("center");
            $objStyle->getAlignment()->setWrapText(true);
            $objStyle->getFont()->setBold(true);
        }

        $line = 1;
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L');

        foreach ($list['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['bookinginno'];
                        break;
                    case 1:
                        if ($item['docsource'] == 1) {
                            $value = '手工创建';
                        } else if ($item['docsource'] == 2) {
                            $value = '国烨云仓';
                        }
                        break;
                    case 2:
                        $value = $item['customer_name'];
                        break;
                    case 3:
                        $value = $item['contract_no'];
                        break;
                    case 4:
                        $value = $item['goodsname'];
                        break;
                    case 5:
                        if ($item['goodsnature'] == 1) {
                            $value = "保税";
                        } else if ($item['goodsnature'] == 2) {
                            $value = "外贸";
                        } else if ($item['goodsnature'] == 3) {
                            $value = "内贸转出口";
                        } else if ($item['goodsnature'] == 4) {
                            $value = "内贸内销";
                        }
                        break;
                    case 6:
                        $value = $item['goods_quality_name'];
                        break;
                    case 7:
                        $value = '吨';
                        break;
                    case 8:
                        $value = $item['bookinginqty'];
                        break;
                    case 9:
                        $value = $item['storagetankname'];
                        break;
                    case 10:
                       $value = $item['cs_employeename'];
                        break;
                    case 11:
                        if ($item['bookinginstatus'] == 2) {
                            $value = "暂存";
                        } else if ($item['bookinginstatus'] == 3) {
                            $value = "待确认";
                        } else if ($item['bookinginstatus'] == 4) {
                            $value = "待审核";
                        } else if ($item['bookinginstatus'] == 5) {
                            $value = "已审核";
                        } else if ($item['bookinginstatus'] == 6) {
                            $value = "已完成";
                        } else if ($item['bookinginstatus'] == 7) {
                            $value = "退回";
                        } else {
                            $value = "新建";
                        }
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="管入库预约单.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

    }
//核单打印
    public function executePrintAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id',0);

        $P = new BookpipelineinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $bookpipeinData = $P->getDetailforPrint($id);
        //  print_r($bookpipeinData);die;
        if(!$bookpipeinData){
            echo json_encode(array('code' => 300,'msg' => '入库信息有误'));
            die();
        }

        echo json_encode($bookpipeinData);

    }

}
<?php

class StockshipinController extends Yaf_Controller_Abstract
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
     * @title 显示整个后台页面框架及菜单
     * @return string
     */
    public function listAction()
    {
        $params = array(
            'bar_no' => '',
            'bar_name' => ''
        );

        $search = array(
            'page' => false,
        );

        $this->getView()->make('stockshipin.stockshipinlist', $params);
    }

    public function listJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'bar_status' => $request->getPost('bar_status', '-100'),
            'bar_isdel' => $request->getPost('bar_isdel', '-100'),
            'bar_stockintype' => $request->getPost('bar_stockintype', '1'),
            'bar_stockinstatus' => $request->getPost('bar_stockinstatus', '-100'),
            'goodsnature'=>$request->getPost('goodsnature',''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'orders' => $request->getPost('orders', ''),
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
        );
        $S = new StockshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStockshipin($search);
        echo json_encode($list);

    }

    /**
     * @title 查看方法
     */
    public function showAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $booking_sysno = $request->getPost('booking_sysno', '0');
        $val = $request->getPost('val', '');

        $S = new StockshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $B = new BookingModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $BO = new BookoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $action = "/stockshipin/editJson/";
        $params = $S->getStockshipinById($id);

        $status = $params['stockinstatus'];

        //查询入库明细
        $detaillist = $S->getStockshipindetailById($id);

        //连接明细字段与计算入库总量
        $qty = 0;
        for ($i = 0; $i < count($detaillist); $i++) {

            $params['detailgoodsname'] = $detaillist[$i]['goodsname'];

            if ($detaillist[$i]['shipname'] == $detaillist[$i + 1]['shipname']) {
                $params['detailshipname'] = '';
            } else {
                $params['detailshipname'] .= $detaillist[$i]['shipname'] . ' ';
            }

            if ($detaillist[$i]['storagetankname'] == $detaillist[$i + 1]['storagetankname']) {
                $params['detailstoragebankname'] = $detaillist[$i]['storagetankname'];
            } else {
                $params['detailstoragebankname'] .= $detaillist[$i]['storagetankname'] . ' ';
            }

            $qty += $detaillist[$i]['beqty'];
        }

        $params['beqty'] = $qty;

        #预约带过来
        $search = array(
            'stockin_sysno' => $params['sysno'],
            'status' => 1,
            'page' => false
        );
        $detailData = $S->getStockshipinDetailList($search);
        $params['detaillist'] = json_encode($detailData['list']);


        $params['attach'] = array();

        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $sysno = $id;
        $attach = $A->getAttachByMAS('stockshipin', 'uploading', $sysno);
        $params['attach'] = array_merge($params['attach'], $attach);

        if (is_array($attach) && count($attach)) {
            $files1 = array();
            foreach ($attach as $file) {
                $files1[] = $file['sysno'];
            }

            $params['uploaded1'] = join(',', $files1);
        }

        $sysno = $id;
        $attach = $A->getAttachByMAS('stockshipin', 'declare_release', $sysno);
        $params['attach'] = array_merge($params['attach'], $attach);

        if (is_array($attach) && count($attach)) {
            $files2 = array();
            foreach ($attach as $file) {
                $files2[] = $file['sysno'];
            }

            $params['uploaded2'] = join(',', $files2);
        }

        if ($booking_sysno) {
            $B = new BookingModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
            $booking = $B->getBookingInById($booking_sysno);
            $params['customer_sysno'] = $booking['customer_sysno'];
            $params['customername'] = $booking['customer_name'];
            $params['booking_in_sysno'] = $booking['sysno'];
            $params['bookingin_no'] = $booking['bookinginno'];
            $params['contract_sysno'] = $booking['contract_sysno'];
            $params['contractno'] = $booking['contract_no'];
            $params['cs_employee_sysno'] = $booking['cs_employee_sysno'];
            $params['cs_employeename'] = $booking['cs_employeename'];
            $ship_info = $S->getStockshipindetailById($id);
            $params['memo'] = $ship_info[0]['memo'];
        }


        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $C->searchCustomer($search);
        $params['customerlist'] = $list['list'];
        $params['stockinstatusnamelist'] = array(
            0 => array('id' => "", 'name' => '新建'),
            1 => array('id' => 1, 'name' => '新建'),
            2 => array('id' => 2, 'name' => '暂存'),
            3 => array('id' => 3, 'name' => '待审核'),
            4 => array('id' => 4, 'name' => '已完成'),
            5 => array('id' => 5, 'name' => '作废'),
            6 => array('id' => 6, 'name' => '退回'),
        );

        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];

        $W = new WharfModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $wharflist = $W->searchWharf(array('page'=>false,'bar_status'=>1,'bar_isdel'=>0));

        $params['wharflist'] = $wharflist['list'];

        $params['id'] = $id;
        $params['val'] = $val;
        $params['action'] = $action;

        $Company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $companyname = $Company->getDefault();
        $params['companyname'] = $companyname['companyname'];
        $Sout = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $businesstype = 2;
        $pbqData = $Sout->getPBQ($booking, $businesstype);
        if($pbqData){
            $params['pipelineorder'] = empty($pbqData['pipelineorder'][0]['pipelineorder_sysno'])? json_encode([]):json_encode($pbqData['pipelineorder']);
            $params['berthorder'] = empty($pbqData['berthorder'][0]['berthorder_sysno'])? json_encode([]):json_encode($pbqData['berthorder']);
            $params['qualitycheck'] = empty($pbqData['qualitycheck'][0]['qualitycheck_sysno'])? json_encode([]):json_encode($pbqData['qualitycheck']);
        }else{
            $params['pipelineorder'] = json_encode([]);
            $params['berthorder'] =  json_encode([]);
            $params['qualitycheck'] = json_encode([]);
        }

        $this->getView()->make('stockshipin.showstockshipin', $params);
    }

    /**
     * 编辑审核新增视图方法
     */
    public function EditAction()
    {
        $request = $this->getRequest();
        $Cost = new FinancecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $O = new StockoutModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $S = new StockshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $E = new EmployeeModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $B = new BookingModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $id = $request->getParam('id', '0');
        $booking_sysno = $request->getPost('booking_sysno', '0');

        $type = $request->getParam('type');

        if ($id) {
            $businesstype = 2;
            $params = $S->getStockshipinById($id);
            if(!$params['booking_in_sysno'])
            {
                COMMON::result(300, '初始化入库单无法编辑作废');
                return;
            }
            $booking = $B->getBookingInById($params['booking_in_sysno']);
            $status = $params['stockinstatus'];
            if ($type == 'edit') {
                $action = '/stockshipin/editJson';
                /*echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
                echo '<br>';
                var_dump($status);
                die;*/
                // 2暂存 3 待审核 4 已完成 5 作废 6退回
                if ($status != 2 &&  $status != 6) {
                    COMMON::result(300, '该状态无法编辑');
                    return;
                }
            } elseif ($type == 'review') {
                if ($status != 3) {
                    COMMON::result(300, '该状态无法审核');
                    return;
                }
                $action = '/stockshipin/auditJson';
                $isCA = $S->isCAstock($booking_sysno);
                $params['isCA'] = $isCA;
            } elseif ($type == 'back') {
                // 1 查询入库单状态
                if ($status != 4) {
                    COMMON::result(300, '该状态无法作废');
                    return;
                }
                // 2 根据入库单号 查询船出库预约单
                $out_info = $O->IsdestoryByinId($id);
                //判断出库单状态
                if ($out_info['status'] != '无数据') {
                    COMMON::result(300, '此订单正在使用中');
                    return;
                }
                // 3 查询入库单 相关费用单状态
                $cost_status = $Cost->serachCostBystockId($id);
                if ($cost_status == 5) {
                    COMMON::result(300, '费用单已关闭，无法删除');
                    return;
                }
                $action = '/stockshipin/backJson';
            } elseif ($type == 'attach') {
                $action = "/stockshipin/addattachJson";
            } elseif ($type == 'register') {
                $action = "/stockshipin/registerJson";
            }
            //查询入库明细
            $detaillist = $S->getStockshipindetailById($id);
            //连接明细字段与计算入库总量
            $qty = 0;
            for ($i = 0; $i < count($detaillist); $i++) {
                $params['detailshipname'] .= $detaillist[$i]['shipname'];
                $params['detailgoodsname'] .= $detaillist[$i]['goodsname'];
                $params['detailstoragebankname'] .= $detaillist[$i]['storagetankname'];
                $qty += $detaillist[$i]['beqty'];
                if ($i >= count($detaillist)) {
                    $params['detailshipname'] .= ',';
                    $params['detailgoodsname'] .= ',';
                    $params['detailstoragebankname'] .= ',';
                }
            }
            $params['beqty'] = $qty;
            #预约带过来
            $search = array(
                'stockin_sysno' => $params['sysno'],
                'status' => 1,
                'page' => false
            );
            $detailData = $S->getStockshipinDetailList($search);
            $params['detaillist'] = json_encode($detailData['list']);


            //生成HTML 标签
            $params['$table_html'] = "<style>
                                    table,td,th {border: 1px solid black;border-style: solid;border-collapse: collapse;text-align:center;}
                                    .title_top{border: 0px solid red;border-style: solid;border-collapse: collapse;text-align:center;}
                                    .title{width: 200px;height: 50px;}
                                    .content{width: 400px;height: 50px;}
                                    .remark{width: 250px;height: 50px;}
                                    .title_bottom{width: 800px;height: 50px;}
                                    .title_bottom span{width: 200px;display: block;float: left;height: 80px;line-height: 80px;text-align: center;}
                                    </style>
                                    <table border='1'>
                                    <tr height='80px' class='title_top' >
                                      <td colspan='3'></td>
                                    </tr>
                                    <tr height='80px' >
                                      <td colspan='3'></td>
                                    </tr>
                                    <tr>
                                      <td class='title'>货主名称</td>
                                      <td class='content'></td>
                                      <td class='remark'>备注</td>
                                    </tr>
                                    </table>";

            $params['attach'] = array();

            $sysno = $id;
            $attach = $A->getAttachByMAS('stockshipin', 'uploading', $sysno);
            $params['attach'] = array_merge($params['attach'], $attach);

            if (is_array($attach) && count($attach)) {
                $files1 = array();
                foreach ($attach as $file) {
                    $files1[] = $file['sysno'];
                }

                $params['uploaded1'] = join(',', $files1);
            }

            $sysno = $id;

            $attach = $A->getAttachByMAS('stockshipin', 'release_no', $sysno);
            $params['attach'] = array_merge($params['attach'], $attach);

            if (is_array($attach) && count($attach)) {
                $files2 = array();
                foreach ($attach as $file) {
                    $files2[] = $file['sysno'];
                }

                $params['uploaded2'] = join(',', $files2);
            }


        } else {
            $businesstype = 1;
            $action = "/stockshipin/newJson/";
            $params = array();
            if ($booking_sysno) {
                $search = array(
                    'bookingin_sysno' => $booking_sysno,
                    'page' => false
                );
                $detailData = $B->getBookingDetailList($search);
                if (count($detailData['list']) > 0) {
                    $dData = array();
                    $tmp = $detailData['list'];
                    foreach ($tmp as $row) {
                        $row['tobeqty'] = $row['bookinginqty'];
                        $row['bookin_detail_sysno'] = $row['sysno'];
                        $dData[] = $row;
                    }
                    $params['detaillist'] = json_encode($dData);
                } else
                    $params['detaillist'] = json_encode(array());
            } else {
                $params['detaillist'] = json_encode(array());
            }

            $params['attach'] = array();
            $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $sysno = $booking_sysno;

            $attach = $A->getAttachByMAS('stockshipin', 'release_no', $sysno);
            $params['attach'] = array_merge($params['attach'], $attach);

            if (is_array($attach) && count($attach)) {
                $files2 = array();
                foreach ($attach as $file) {
                    $files2[] = $file['sysno'];
                }

                $params['uploaded2'] = join(',', $files2);
            }

        }
        $params['action'] = $action;
        if ($booking_sysno) {
            $booking = $B->getBookingInById($booking_sysno);
            $params['customer_sysno'] = $booking['customer_sysno'];
            $params['customername'] = $booking['customer_name'];
            $params['booking_in_sysno'] = $booking['sysno'];
            $params['bookingin_no'] = $booking['bookinginno'];
            $params['contract_sysno'] = $booking['contract_sysno'];
            $params['contractno'] = $booking['contract_no'];
            $params['cs_employee_sysno'] = $booking['cs_employee_sysno'];
            $params['cs_employeename'] = $booking['cs_employeename'];
            $params['ispipelineorder'] = $booking['ispipelineorder'];
            $params['isberthorder'] = $booking['isberthorder'];
            $params['isqualitycheck'] = $booking['isqualitycheck'];
            $ship_info = $S->getStockshipindetailById($id);
          //  $params['memo'] = $ship_info[0]['memo'];
        }
        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $C->searchCustomer($search);
        $params['customerlist'] = $list['list'];#1新建2暂存3待审核4已完成5作废
        $params['stockinstatusnamelist'] = array(
            0 => array('id' => "", 'name' => '新建'),
            1 => array('id' => 1, 'name' => '新建'),
            2 => array('id' => 2, 'name' => '暂存'),
            3 => array('id' => 3, 'name' => '待审核'),
            4 => array('id' => 4, 'name' => '已完成'),
            5 => array('id' => 5, 'name' => '作废'),
            6 => array('id' => 6, 'name' => '退回'),
        );

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $list = $E->searchEmployee($search);
        $params['employeelist'] = $list['list'];


        $W = new WharfModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $wharflist = $W->searchWharf(array('page'=>false,'bar_status'=>1,'bar_isdel'=>0));

        $params['wharflist'] = $wharflist['list'];

        $params['id'] = $id;
        $params['type'] = $type;
        $params['load_user'] =  Yaf_Registry::get(SSN_VAR);
        $Company = new CompanyModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $companyname = $Company->getDefault();
        $params['companyname'] = $companyname['companyname'];
        
        $Sout = new StockoutModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $pbqData = $Sout->getPBQ($booking, $businesstype);
        if($pbqData){
            $params['pipelineorder'] = empty($pbqData['pipelineorder'][0]['pipelineorder_sysno'])? json_encode([]):json_encode($pbqData['pipelineorder']);
            $params['berthorder'] = empty($pbqData['berthorder'][0]['berthorder_sysno'])? json_encode([]):json_encode($pbqData['berthorder']);
            //防止换行影响页面js报错
            if(!empty($pbqData['qualitycheck'][0]['memo'])){
                $pbqData['qualitycheck'][0]['memo'] = str_replace(PHP_EOL,' ',$pbqData['qualitycheck'][0]['memo']);
            }
            $params['qualitycheck'] = empty($pbqData['qualitycheck'][0]['qualitycheck_sysno'])? json_encode([]):json_encode($pbqData['qualitycheck']);
        }else{
            $params['pipelineorder'] = json_encode([]);
            $params['berthorder'] =  json_encode([]);
            $params['qualitycheck'] = json_encode([]);
        }
//print_r($params);die;
        $this->getView()->make('stockshipin.stockshipinedit', $params);
    }

    /**
     * @title 登记方法
     */
    public function registerJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockshipindetaildata = $request->getPost('stockshipindetaildata', "");
        $stockshipindetaildata = json_decode($stockshipindetaildata, true);
        $stockmarks = $request->getPost('stockmarks', '');

        $stockshipin = new StockshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $info = $stockshipin->getStockshipinById($id);
        $stockinstatus = $info['stockinstatus'];
        if ($stockinstatus != 4) {
            COMMON::result(300, '非完成状态无法登记！');
            return;
        }
        //页面传递 status 8 登记
        $status = $request->getPost('stockinstatus', '');
/*        if (count($stockshipindetaildata) == 0) {
            COMMON::result(300, '入库单明细不能为空');
            return;
        }
        $T = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $beqty = 0;
        foreach ($stockshipindetaildata as $key => $value) {
            if ($value['beqty'] <= 0) {
                COMMON::result(300, $value['storagetankname'] . '请填写实际数量');
                return;
            }
            $beqty = $beqty + $value['beqty'];
            $storagetank_sysno = $value['storagetank_sysno'];
            $goods_sysno = $value['goods_sysno'];
            $flag = $T->storagetankgoodsbyid($storagetank_sysno);
            if ($flag != $goods_sysno) {
                COMMON::result(300, $value['storagetankname'] . '还有其他货品存量');
                return;
            }
            $detailtankarr[$storagetank_sysno] = $detailtankarr[$storagetank_sysno] + $value['beqty'];
            foreach ($detailtankarr as $key => $it) {
                $search = array(
                    'storagetank_sysno' => $key,
                );
                $available = $T->getStoragetankavailable($search);
                if ($available < $it) {
                    COMMON::result(300, $value['storagetankname'] . '可存放容量不足,可用容量为:' . $available . '吨');
                    return;
                }
            }
        }*/

        $S = new StockshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'stockintype' => $request->getPost('stockintype', '1'),
            'stockindate' => $request->getPost('stockindate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customername' => $request->getPost('obj_customername', ''),
            'booking_in_sysno' => $request->getPost('booking_in_sysno', ''),
            'bookingin_no' => $request->getPost('bookingin_no', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contractno' => $request->getPost('contractno', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'zj_employee_sysno' => $request->getPost('zj_employee_sysno', ''),
            'zj_employeename' => $request->getPost('zj_employeename', ''),
            'cc_employee_sysno' => $request->getPost('cc_employee_sysno', ''),
            'cc_employeename' => $request->getPost('cc_employeename', ''),
            'stockinstatus' => $request->getPost('stockinstatus', ''),
            'updated_at' => '=NOW()'
        );
        if ($input['stockinstatus'] == 1) {
            if ($request->getPost('stockmarks') == '') {
                COMMON::result(300, '审核备注不能为空');
                return;
            }
        }
        $flag = $S->updateStockshipin($id, $input, $stockshipindetaildata, $stockmarks, $status, $beqty);
        if ($flag['code']==200) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $S->getStockshipinById($id);
            COMMON::result(200, '登记成功', $row);
        } else {
            COMMON::result(300, $flag['message']);
        }
    }

    /**
     * @title 新增方法
     */
    public function newJsonAction()
    {
        $request = $this->getRequest();
        $stockshipindetaildata = $request->getPost('stockshipindetaildata', "");
        $stockshipindetaildata = json_decode($stockshipindetaildata, true);

        if (count($stockshipindetaildata) == 0) {
            COMMON::result(300, '入库单明细不能为空');
            return;
        }
        $tankonly = array();
        $T = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        foreach ($stockshipindetaildata as $key => $value) {
            if ($value['beqty'] <= 0) {
                COMMON::result(300, '请填写实际数量');
                return;
            }
            $storagetank_sysno = $value['storagetank_sysno'];
            $tank_name = $value['storagetankname'];
            $goods_sysno = $value['goods_sysno'];
            $flag = $T->storagetankgoodsbyid($storagetank_sysno);
            if ($flag != $goods_sysno) {
                COMMON::result(300, $tank_name . '还有其他货品存量');
                return;
            }
            $detailtankarr[$storagetank_sysno] = $detailtankarr[$storagetank_sysno] + $value['beqty'];
            foreach ($detailtankarr as $key => $arr) {
                $search = array(
                    'storagetank_sysno' => $key,
                );
                $available = $T->getStoragetankavailable($search);
                if ($available < $arr) {
                    COMMON::result(300, $tank_name . '存放容量不足,可用容量为:' . $available . '吨');
                    return;
                }
            }
            if(!in_array($value['storagetank_sysno'],$tankonly)){
                $tankonly[$key]=$value['storagetank_sysno'];
            }else{
                COMMON::result(300, '储罐不能重复');
                return;
            }
        }


        $S = new StockshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'stockintype' => $request->getPost('stockintype', '1'),
            'stockinno' => COMMON::getCodeId('A2'),
            'stockindate' => $request->getPost('stockindate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customername' => $request->getPost('obj_customername', ''),
            'booking_in_sysno' => $request->getPost('booking_in_sysno', ''),
            'bookingin_no' => $request->getPost('bookingin_no', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contractno' => $request->getPost('contractno', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'zj_employee_sysno' => $request->getPost('zj_employee_sysno', ''),
            'zj_employeename' => $request->getPost('zj_employeename', ''),
            'cc_employee_sysno' => $request->getPost('cc_employee_sysno', ''),
            'cc_employeename' => $request->getPost('cc_employeename', ''),
            'stockinstatus' => $request->getPost('stockinstatus', '1'),
            'wharf_sysno' => $request->getPost('wharf_sysno',''),
            'wharfname' => $request->getPost('wharfname',''),
            'wharf_date' => $request->getPost('wharf_date',''),
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'isberthorder' => $request->getPost('isberthorder', ''),
            'ispipelineorder' => $request->getPost('ispipelineorder', ''),
            'isqualitycheck' => $request->getPost('isqualitycheck', ''),
            'takegoodsnum' => $request->getPost('takegoodsnum', ''),
            'shipcheckqty' => $request->getPost('shipcheckqty', ''),
            'sby_employee_sysno'=>$request->getPost('sby_employee_sysno', ''),
            'sby_employeename'=>$request->getPost('sby_employeename', ''),
            'deliverycompany'=>$request->getPost('deliverycompany', ''),
            'memo'=>$request->getPost('memo', ''),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
        );

        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        //页面上传的附件
        $fujian = $request->getPost('attachment', array());
        //原有预约单放行单附件
        // $attach = $A->getAttachByMAS('bookshipin', 'release_no', $input['booking_in_sysno']);
        $attach = array();//现有逻辑不在读取预约单放行附件
        //数组合并
        $params['attach'] = array_merge($fujian, $attach);

        if (!empty($attach)) {
            $array = [
                '0' => $params['attach'][0]['sysno'],
            ];
        } else {
            $array = $params['attach'];
        }
        $id = $S->addStockshipin($input, $stockshipindetaildata, $request->getPost('stockmarks', ''));
        if ($id) {
            if (count($array) > 0) {
                $res = $A->addAttachModelSysno($id, $array);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }
            $row = $S->getStockshipinById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    /**
     * @title 编辑方法
     */
    public function editJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockshipindetaildata = $request->getPost('stockshipindetaildata', "");
        $stockshipindetaildata = json_decode($stockshipindetaildata, true);
        $stockmarks = $request->getPost('stockmarks', '');
        $status = $request->getPost('stockinstatus', '');
        if (count($stockshipindetaildata) == 0) {
            COMMON::result(300, '入库单明细不能为空');
            return;
        }
        $T = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $beqty = 0;
        $tankonly = array();
        foreach ($stockshipindetaildata as $key => $value) {
            if ($value['beqty'] <= 0) {
                COMMON::result(300, $value['storagetankname'] . '请填写实际数量');
                return;
            }
            $beqty = $beqty + $value['beqty'];
            $storagetank_sysno = $value['storagetank_sysno'];
            $goods_sysno = $value['goods_sysno'];
            $flag = $T->storagetankgoodsbyid($storagetank_sysno);
            if ($flag != $goods_sysno) {
                COMMON::result(300, $value['storagetankname'] . '还有其他货品存量');
                return;
            }
            $detailtankarr[$storagetank_sysno] = $detailtankarr[$storagetank_sysno] + $value['beqty'];
            foreach ($detailtankarr as $key => $it) {
                $search = array(
                    'storagetank_sysno' => $key,
                );
                $available = $T->getStoragetankavailable($search);
                if ($available < $it) {
                    COMMON::result(300, $value['storagetankname'] . '可存放容量不足,可用容量为:' . $available . '吨');
                    return;
                }
            }
            if(!in_array($value['storagetank_sysno'],$tankonly)){
                $tankonly[$key]=$value['storagetank_sysno'];
            }else{
                COMMON::result(300, '储罐不能重复');
                return;
            }
        }

        $S = new StockshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'stockintype' => $request->getPost('stockintype', '1'),
            'stockindate' => $request->getPost('stockindate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customername' => $request->getPost('obj_customername', ''),
            'booking_in_sysno' => $request->getPost('booking_in_sysno', ''),
            'bookingin_no' => $request->getPost('bookingin_no', ''),
            'contract_sysno' => $request->getPost('contract_sysno', ''),
            'contractno' => $request->getPost('contractno', ''),
            'cs_employee_sysno' => $request->getPost('cs_employee_sysno', ''),
            'cs_employeename' => $request->getPost('cs_employeename', ''),
            'zj_employee_sysno' => $request->getPost('zj_employee_sysno', ''),
            'zj_employeename' => $request->getPost('zj_employeename', ''),
            'cc_employee_sysno' => $request->getPost('cc_employee_sysno', ''),
            'cc_employeename' => $request->getPost('cc_employeename', ''),
            'wharf_sysno' => $request->getPost('wharf_sysno',''),
            'wharfname' => $request->getPost('wharfname',''),
            'wharf_date'=> $request->getPost('wharf_date',''),
            'stockinstatus' => $request->getPost('stockinstatus', ''),
            'takegoodsnum' => $request->getPost('takegoodsnum', ''),
            'shipcheckqty' => $request->getPost('shipcheckqty', ''),
            'sby_employee_sysno'=>$request->getPost('sby_employee_sysno', ''),
            'sby_employeename'=>$request->getPost('sby_employeename', ''),
            'deliverycompany'=>$request->getPost('deliverycompany', ''),
            'memo'=>$request->getPost('memo', ''),
            'updated_at' => '=NOW()'
        );
        if ($input['stockinstatus'] == 1) {
            if ($request->getPost('stockmarks') == '') {
                COMMON::result(300, '审核备注不能为空');
                return;
            }
        }
        $flag = $S->updateStockshipin($id, $input, $stockshipindetaildata, $stockmarks, $status, $beqty);
        if ($flag['code']==200) {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $S->getStockshipinById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, $flag['message']);
        }
    }

    /**
     * @title 新增附件方法
     */
    public function addattachJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $attach = $request->getPost('attachment', array());

        if (count($attach) > 0) {
            $res = $A->addAttachModelSysno($id, $attach);
            if (!$res) {
                COMMON::result(300, '添加附件失败');
                return;
            } else {
                COMMON::result(200, '添加附件成功');
                return;
            }
        } else {
            COMMON::result(300, '请先上传附件');
            return;
        }
    }

    /**
     * @title 审批方法
     */
    public function auditJsonAction()
    {
        $request = $this->getRequest();
        if($request->getPost('isCA',0)){
            sleep(2);
        }
        $id = $request->getPost('id', 0);
        $stockmarks = $request->getPost('stockmarks', '');
        $status = $request->getPost('stockinstatus', '');
        $S = new StockshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $T = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $L = new LogModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $stockshipindetaildata = $request->getPost('stockshipindetaildata', "");
        $stockshipindetaildata = json_decode($stockshipindetaildata, true);
        $stockshipinquality = $request->getPost('stockshipinquality', "");
        $stockshipinquality = json_decode($stockshipinquality, true);
     //   print_r($stockshipinquality);die;
        //取出实际入库量
        $beqty = 0;
        foreach ($stockshipindetaildata as $key => $value) {
            $beqty = $beqty + $value['beqty'];
            $storagetank_sysno = $value['storagetank_sysno'];
            $goods_sysno = $stockshipindetaildata[$key]['goods_sysno'];
            $flag = $T->storagetankgoodsbyid($storagetank_sysno);
            if ($flag != $goods_sysno) {
                COMMON::result(300, '该储罐还有其他货品存量');
                return;
            }

            //添加储罐和货物性质对应关系
            $tankId = $value['storagetank_sysno'];
            $goodsnature = $value['goodsnature'];
            $res = $L->tankTonature($tankId,$goodsnature);
            if($res['code']==300 && $status==4){
                COMMON::result(300, $res['message']);
                return;
            }
        }
        if($status == 4){
            if($stockshipinquality[0]['orderstatus']!=7){
                if($stockshipinquality[0]['orderstatus']==4 && $stockshipinquality[0]['ischecked']==2){
                    COMMON::result(300, '品质检查不合格');
                    return;
                }
            }
        }
        $bookingin_no = $request->getPost('bookingin_no', '');
        $booking_in_sysno = $request->getPost('booking_in_sysno', '');
        $book = new BookshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $booklist = $book->getBookshipinById($booking_in_sysno);
        #var_dump($booklist['docsource']);exit();
        $input = array(
            'stockinstatus' => $status,
            'updated_at' => '=NOW()',
            'customer_sysno'=> $request->getPost('log_customer_sysno'),
            'customername'=> $request->getPost('obj_customername'),
        );
        if ($input['stockinstatus'] == 1) {
            if ($request->getPost('stockmarks') == '') {
                COMMON::result(300, '审核备注不能为空');
                return;
            }
        }
        $flag = $S->updateStockshipin($id, $input, $stockshipindetaildata, $stockmarks, $status, $beqty);

        if ($flag['code']==200) {
            if ($booklist['docsource'] == 2) {
                COMMON::editStockInStatus($bookingin_no, $beqty);
            }
            $row = $S->getStockshipinById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, $flag['message']);
        }
    }

    /**
     * @title 退回方法
     */
    public function backJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $status = $request->getPost('stockinstatus', '');
        $remarks = $request->getPost('stockmarks', '');
        if ($remarks == '') {
            COMMON::result(300, '作废备注不能为空');
            return;
        }

        $S = new StockshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $stockshipindetaildata = $request->getPost('stockshipindetaildata', "");
        $stockshipindetaildata = json_decode($stockshipindetaildata, true);

        //取出实际入库量
        $beqty = 0;
        foreach ($stockshipindetaildata as $key => $value) {
            $beqty = $beqty + $value['beqty'];
        }

        $array = [
            'abandonreason' => $remarks,
            'stockinstatus' => $status,
            'updated_at' => '=NOW()',
        ];

        $res = $S->backshipin($id, $array, $stockshipindetaildata, $beqty);
        if ($res['code']==200) {
            COMMON::result(200, $res['message']);
        } else {
            COMMON::result(300, $res['message']);
        }
    }

    /**
     * @title 删除方法
     */
    public function delJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);

        $S = new StockshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $input = array(
            'isdel' => 1,
            'abandonreason' => '删除'
        );
        $data = $S->getStockshipinById($id);
       // print_r($data);die;
        if ($data) {
            if ($data['stockinstatus'] != 2 && $data['stockinstatus'] != 6) {
                COMMON::result(300, '该状态无法删除');
                return;
            }
        }

        $flag = $S->delStockshipin($id, $input);
        if ($flag['code']==200) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败'.$flag['msg']);
        }
    }

    /**
     * @title 详情表编辑方法
     */
    public function detailEditAction()
    {
        $request = $this->getRequest();

        $params = $request->getRequest();

        $Storage = new StoragetankModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $storageList = $Storage->getStoragetankBygoods($params['goods_sysno']); //获取储罐列表

        $params['storageList'] = $storageList;

        //规格
        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $quality->getList($search, 99, 1);
        $params['goodsqualitylist'] = $list['list'];
        //end
        $params['action'] = '/stockshipin/detailsubmit/';

        //入库单状态
        $status = $request->getParam('status', '0');
        $params['status'] = $status;
        $params['split'] = $request->getParam('split',0);
        $this->getView()->make('stockshipin.detailedit', $params);

    }

    public function adddetailAction()
    {
        $request = $this->getRequest();
        $cid = $request->getParam('cid', '0');

        $params = array(
            'bar_no' => '',
            'bar_name' => ''
        );

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false,
        );
        $S = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStoragetank($search);
        $params['storagetanklist'] = $list['list'];

        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $list = $quality->getList($search, 99, 1);
        $params['goodsqualitylist'] = $list['list'];

        $params['customer_sysno'] = $cid;

        $this->getView()->make('stockshipin.stockshipinadddetail', $params);
    }

    public function adddetailJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');

        if (!isset($id)) {
            $id = 0;
        }

        $S = new StockshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getStockshipindetailById($id);

        if ($id && !empty($list)) {

            foreach ($list as $key => $value) {
                $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                $data = $quality->getQualityById($value['goods_quality_sysno']);
                $list[$key]['goods_quality_name'] = $data['qualityname'];
                $list[$key]['obj.goods_sysno'] = $value['goods_sysno'];
                $list[$key]['obj.goodsname'] = $value['goodsname'];
                $list[$key]['obj.shipname'] = $value['shipname'];
            }
        }
        echo json_encode($list);
    }

    public function detailsubmitAction()
    {
        $request = $this->getRequest();
        $list = array(
            'goods_sysno' => $request->getPost('obj_goods_sysno', ''),
            'goodsname' => $request->getPost('obj_goodsname', ''),
            'goods_quality_sysno' => $request->getPost('goodsquality', ''),
            'goodsnature' => $request->getPost('goodsnature', ''),
            'goodsreceiptdate' => $request->getPost('goodsreceiptdate', ''),
            'tobeqty' => $request->getPost('tobeqty', ''),#通知数量
            'shipcheckqty' => $request->getPost('shipcheckqty', ''),#船检数量
            'bussinesscheckqty' => $request->getPost('beqty', ''),#商检数量
            'beqty' => $request->getPost('beqty', ''), #商检数量 ==实到数量
            'storagebank_sysno' => $request->getPost('storagebank_sysno', ''),
            'shipname' => $request->getPost('obj_shipname', ''),
            'expresscompanyname' => $request->getPost('expresscompanyname', ''),
            'memo' => $request->getPost('memo', ''),
        );
        $S = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $data = $S->getStoragetankById($list['storagebank_sysno']);
        $list['storagetankname'] = $data['storagetankname'];

        $quality = new QualityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $data = $quality->getQualityById($list['goods_quality_sysno']);
        $list['goods_quality_name'] = $data['qualityname'];
        echo json_encode($list);
    }

    /**
     * @title Excel导出
     */
    public function excelAction()
    {
        $request = $this->getRequest();

        $search = array(
            'bar_no' => $request->getPost('bar_no', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'bar_status' => $request->getPost('bar_status', '-100'),
            'bar_isdel' => $request->getPost('bar_isdel', '-100'),
            'bar_stockintype' => $request->getPost('bar_stockintype', '1'),
            'bar_stockinstatus' => $request->getPost('bar_stockinstatus', '-100'),
            'page' => '',
            'orders' => $request->getPost('orders', ''),
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
        );
        $S = new StockshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStockshipin($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("当前船入库列表")
            ->setSubject("列表")
            ->setDescription("当前船入库列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '入库单号'),
            array('B1:B1', 'B1', '005E9CD3', '客户'),
            array('C1:C1', 'C1', '005E9CD3', '货品名称'),
            array('D1:D1', 'D1', '005E9CD3', '计量单位'),
            array('E1:E1', 'E1', '005E9CD3', '提单数量'),
            array('F1:F1', 'F1', '005E9CD3', '船检数量'),
            array('G1:G1', 'G1', '005E9CD3', '商检数量'),
            array('H1:H1', 'H1', '0094CE58', '总报关量'),
            array('I1:I1', 'I1', '0094CE58', '船名'),
            array('J1:J1', 'J1', '003376B3', '货物性质'),
            array('K1:K1', 'K1', '003376B3', '质计员'),
            array('L1:L1', 'L1', '003376B3', '客服'),
            array('M1:M1', 'M1', '003376B3', '仓储'),
            array('N1:N1', 'N1', '003376B3', '单据状态'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('当前船入库列表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M');

        foreach ($list['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['stockinno'];
                        break;
                    case 1:
                        $value = $item['customername'];
                        break;
                    case 2:
                        $value = $item['goodsname'];
                        break;
                    case 3:
                        $value = $item['unitname'];
                        break;
                    case 4:
                        $value = $item['tobeqty'];
                        break;
                    case 5:
                        $value = $item['shipcheckqty'];
                        break;
                    case 6:
                        $value = $item['beqty'];
                        break;
                    case 7:
                        $value = $item['goodsnature']==4?'--':$item['release_num'];
                        break;
                    case 8:
                        $value = $item['shipname'];
                        break;
                    case 9:
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
                    case 10:
                        $value = $item['zj_employeename'];
                        break;
                    case 11:
                        $value = $item['cs_employeename'];
                        break;
                    case 12:
                        $value = $item['cc_employeename'];
                        break;
                    case 13:
                        if ($item['stockinstatus'] == 2) {
                            $value = "暂存";
                        } else if ($item['stockinstatus'] == 3) {
                            $value = "待审核";
                        } else if ($item['stockinstatus'] == 4) {
                            $value = "已完成";
                        } else if ($item['stockinstatus'] == 5) {
                            $value = "作废";
                        } else if ($item['stockinstatus'] == 6) {
                            $value = "退回";
                        } else {
                            $value = "新建";
                        }
                        break;
                    default:
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="当前船入库查询.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

    }

    /**
     * 报关列表页
     * @return [type] [description]
     */
    public function declareListAction()
    {
        $params = [];
        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();
        return $this->getView()->make('stockshipin.declare.list',$params);
    }

    /**
     * 报关列表页数据
     */
    public function declareListJsonAction()
    {
        $request = $this->getRequest();
        $search = array(
            'start_time'    => $request->getPost('start_time', ''),
            'end_time'      => $request->getPost('end_time', ''),
            'customername'  => $request->getPost('customername', ''),
            'stockinno'     => $request->getPost('stockinno', ''),
            'stockintype'   => $request->getPost('bar_stockintype', ''),
            'goods_sysno'   => $request->getPost('bar_goodsname', ''),
            'pageCurrent'   => COMMON:: P(),
            'pageSize'      => COMMON:: PR(),
            );
        $S = new StockshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $S->getdeclareList($search);
        // var_dump($params);exit;
        echo json_encode($params);
    }

    //报关详情
    public function declareDetailAction()
    {   
        $request = $this->getRequest();
        $stockin_sysno = $request->getParam('stockin_sysno','');
        $S = new StockshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $S->getdeclareDetail($stockin_sysno); 
        foreach ($params['detaildata'] as $key => $value) {
            if(is_array($value)){
                is_null($value['customername']) ? $params['detaildata'][$key]['customername']= $params['customername'] :$value['customername'];
                is_null($value['customer_sysno'])? $params['detaildata'][$key]['customer_sysno']= $params['customer_sysno'] : $value['customer_sysno'];
            }else{
                is_null($params['detaildata']['customername']) ? $params['detaildata']['customername']= $params['customername'] : $params['detaildata']['customername'];
                is_null($params['detaildata']['customer_sysno']) ? $params['detaildata']['customer_sysno']= $params['customer_sysno'] : $params['detaildata']['customer_sysno'];
            }
        }

        $params['detailData'] = json_encode($params['detaildata']);

        $params['id'] = $stockin_sysno;

        //添加附件的显示
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $uploaded1 = $A->getAttachByMAS('stockshipin','declare_release',$stockin_sysno);
        if( is_array($uploaded1) && count($uploaded1)){
            $files1 = array();
            foreach ($uploaded1 as $file){
                $files1[] = $file['sysno'];
            }
            $params['uploaded1']  =  join(',',$files1);
        }

        return $this->getView()->make('stockshipin.declare.edit',$params);

    }

    public function declaredetaileditAction()
    {
        $request = $this->getRequest();

        $params = $request->getRequest('data','');

        // var_dump($params);exit;
        $S = new StockshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $storageList = $S->getStoragetankBYstockinId($params['stockin_sysno']); //获取储罐列表

        $params['storageList'] = $storageList;

        //获取客户列表
        $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $search = array(
            'page' => false,
        );
        $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $C->searchCustomer($search);

        $params['customerList'] = $list['list']; 

        return $this->getView()->make('stockshipin.declare.detailedit',$params);
    }

    //查看报关录入信息
    public function showdeclaredetailAction()
    {
        $request = $this->getRequest();
        $stockin_sysno = $request->getParam('stockin_sysno','');
        $S = new StockshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $S->getdeclareDetail($stockin_sysno); 
        foreach ($params['detaildata'] as $key => $value) {
            if(is_array($value)){
                is_null($value['customername']) ? $params['detaildata'][$key]['customername']= $params['customername'] :$value['customername'];
                is_null($value['customer_sysno'])? $params['detaildata'][$key]['customer_sysno']= $params['customer_sysno'] : $value['customer_sysno'];
            }else{
                is_null($params['detaildata']['customername']) ? $params['detaildata']['customername']= $params['customername'] : $params['detaildata']['customername'];
                is_null($params['detaildata']['customer_sysno']) ? $params['detaildata']['customer_sysno']= $params['customer_sysno'] : $params['detaildata']['customer_sysno'];
            }
        }
        $params['detailData'] = json_encode($params['detaildata']);
        $params['type'] = 1;
        $params['id'] = $stockin_sysno;

        //添加附件的显示
        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $uploaded1 = $A->getAttachByMAS('stockshipin','declare_release',$stockin_sysno);
        $params['uploadess'] = [];
        if(is_array($uploaded1) && count($uploaded1)){
            $files1 = array();
            foreach ($uploaded1 as $file){
                $files1[] = $file['sysno'];
            }
            $params['uploadess'] = $files1;
            $params['uploaded1']  =  join(',',$files1);
        }

        return $this->getView()->make('stockshipin.declare.edit',$params);
    }

    //添加报关明细
    public function adddeclareAction()
    {
        $request = $this->getRequest();

        $detail = json_decode($request->getPost('detail',''),true);

        $params = $detail;

        $attachment = $request->getPost('attachment',array());

        $S = new StockshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $res = $S->adddeclare($params,$attachment);

        if($res['code']==200){
            COMMON::result(200,$res['message']);
        }else{
            COMMON::result(300,$res['message']);
        }

    }


    //ajax获取该入库单被出库了多少
    public function AjaxgetoutQtyAction()
    {
        $request = $this->getRequest();

        $stockin_sysno = $request->getPost('stockin_sysno',0);

        $S = new StockshipinModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $outqty = $S->getoutqty($stockin_sysno);

        echo $outqty;
    }

    //核单打印
    public function executePrintAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $type = $request->getParam('type',0);
        $S = new StockshipinModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $stockinData = $S->getStockinDetailData($id);

        if(!$stockinData){
            echo json_encode(array('code' => 300,'msg' => '入库信息有误'));
            die();
        }
        if($type==1){
            if($stockinData['stockinstatus'] != 4){
                echo json_encode(array('code' => 300,'msg' => '只有已审核的单据才可以打印'));
                return false;
            }
            echo json_encode($stockinData);
        }else{

            echo json_encode($stockinData);
        }


    }
}
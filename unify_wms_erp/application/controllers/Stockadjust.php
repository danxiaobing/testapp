<?php

/**
 * Created by PhpStorm.
 * User: 129
 * Date: 2018/1/29
 * Time: 11:21
 */
class StockadjustController extends Yaf_Controller_Abstract
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
     * 库存调整列表
     */
    public function listAction()
    {
        $C = new CustomerModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $G = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_status'=>1,
            'page'=>false
        );
        $customerdata = $C->searchCustomer($search);
        $goodsdata = $G ->getBaseGoods($search);
        $params['customerlist'] = $customerdata['list'];
        $params['goodslist'] = $goodsdata['list'];

        $this->getView()->make('stockadjust.stockadjustlist', $params);
    }

    /*
     * 获取列表数据
     */
    public function listJsonAction()
    {
        $request = $this->getRequest();

        $search = array(
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'bar_no' => $request->getPost('bar_no', ''),
            'customer_sysno'=>$request->getPost('customer_sysno', ''),
            'goods_sysno'=>$request->getPost('goods_sysno', ''),
            'bar_stockcheckstatus' => $request->getPost('bar_stockcheckstatus', '-100'),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );
        $S = new StockadjustModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->searchStockadjust($search);
        echo json_encode($list);

    }

    /**
     * 库存调整编辑
     */
    public function editAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $mode = $request->getParam('mode','');
        $S = new StockadjustModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if (!$id) {
            $action = "/stockadjust/newJson/";
            $params = array();
        } else {
            $params = $S->getstockadjustById($id);
            $status = $params['stockcheckstatus'];

            if($mode == 'edit'){
                if($status == 2||$status == 6){
                    $action = '/stockadjust/editJson';
                }
                else{
                    COMMON::result(300, '非暂存或退回状态不能编辑！');
                    return;
                }
            }elseif($mode == 'audit'){
                $action = '/stockadjust/auditJson';
            }elseif($mode == 'back'){
                $action = '/stockadjust/backstockadjust';
            }elseif($mode == 'blank'){
                $action = '/stockadjust/blankstockadjust';
            }elseif($mode == 'addattach'){
                $action = '/stockadjust/addcontractattach';
            }
            //暂存状态 编辑
            $params['attach'] = array();
            $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
            $attach = $A->getAttachByMAS('stockadjust', 'stockadjustatt', $id);
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
            'page' => false,
            'bar_status' => 1
        );
        $list = $C->searchCustomer($search);
        $params['customerlist'] = $list['list'];

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

        $this->getView()->make('stockadjust.stockadjustedit', $params);
    }

    /*
     *根据客户查询库存小于0的货品
     */
    public function customergoodsJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        $S = new StockadjustModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $data = $S->getstockgoodsleft0($id);

        $list[] = array('value'=>'','label'=>'请选择');
        if(!empty($data))
        foreach ($data as $key => $value) {
            $list[] = array('value'=>$value['goods_sysno'],'label'=>$value['goodsname']);
        }
        echo json_encode($list);

    }

    /*
     * 获取该客户该货品库存小于0的数据
     */
    public function customerstocklistJsonAction(){
        $request = $this->getRequest();
        $search = array (
            'customer_sysno'=>$request->getPost('customer_sysno',0),
            'goods_sysno'=>$request->getPost('goods_sysno',0),
        );
        $S = new StockadjustModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $data = $S->searchCustomergoodsleft0($search);

        echo json_encode($data);
    }

    /*
     * 获取库存调整明细数据
     */
    public function adddetailJsonAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $S = new StockadjustModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $S->getStockadjustdetailById($id);
        echo json_encode($list);
    }

    /*
     * 库存调整明细视图
     */
    public function stockadjustdetaileditAction(){
        $request = $this->getRequest();
        $cid = $request->getParam('cid', '0');
        $goodid = $request->getParam('goodid', '0');
        $handlestatus = $request->getParam('handlestatus','0');
        $mode = $request ->getPost('mode','');
        $params = $request->getPost('selectedDatasArray',array());

        $params['customer_sysno'] = $cid;
        $params['goods_sysno'] = $goodid;
        $params['handlestatus'] = $handlestatus;
        $params['mode'] = $mode;

        $this->getView()->make('stockadjust.stockadjustdetail', $params);
    }

    /*
     * 添加库存调整
     */
    public function newJsonAction(){
        $request = $this->getRequest();
        $stockcheckstatus = $request->getPost('stockcheckstatus', '');
        $stockadjustdetaildata = $request->getPost('stockadjustdetaildata', "");
        $stockadjustdetaildata = json_decode($stockadjustdetaildata, true);
        if (count($stockadjustdetaildata) == 0) {
            COMMON::result(300, '明细不能为空');
            return;
        }
        $stockadjustdetaildata = $this->list_sort_by($stockadjustdetaildata, 'stock_sysno', 'asc');
        if(count($stockadjustdetaildata) >= 2){
            for($i = 0 ;$i< count($stockadjustdetaildata)-1;$i++){
                if($stockadjustdetaildata[$i]['stock_sysno'] == $stockadjustdetaildata[$i+1]['stock_sysno']){
                    COMMON::result(300, '明细不能重复');
                    return;
                }
            }
        }

        $S = new StockadjustModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'stockcheckno' => COMMON::getcheckCodeId('TZ'),
            'stockcheckdate' => $request->getPost('stockcheckdate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customername' => $request->getPost('obj_customer_name', ''),
            'goods_sysno' => $request->getPost('goods_sysno', ''),
            'goodsname' => $request->getPost('goodsname', ''),
            'zj_employee_sysno' => $request->getPost('zj_employee_sysno', ''),
            'zj_employeename' => $request->getPost('zj_employeename', ''),
            'stockcheckstatus' => $stockcheckstatus,
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );

        $res = $S->addstockadjust($input, $stockadjustdetaildata,$stockcheckstatus);

        if ($res['statusCode'] == '200') {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($res['msg'], $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $S->getstockadjustById($res['msg']);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败,'.$res['msg']);
        }
    }

    /*
     * 编辑库存调整
     */
    public function editJsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockcheckstatus = $request->getPost('stockcheckstatus', '');
        $stockadjustdetaildata = $request->getPost('stockadjustdetaildata', "");
        $stockadjustdetaildata = json_decode($stockadjustdetaildata, true);
        if (count($stockadjustdetaildata) == 0) {
            COMMON::result(300, '装卸货品明细不能为空');
            return;
        }
        $stockadjustdetaildata = $this->list_sort_by($stockadjustdetaildata, 'stock_sysno', 'asc');
        if(count($stockadjustdetaildata) >= 2){
            for($i = 0 ;$i< count($stockadjustdetaildata)-1;$i++){
                if($stockadjustdetaildata[$i]['stock_sysno'] == $stockadjustdetaildata[$i+1]['stock_sysno']){
                    COMMON::result(300, '明细不能重复');
                    return;
                }
            }
        }

        $S = new StockadjustModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'stockcheckno' => $request->getPost('stockcheckno', ''),
            'stockcheckdate' => $request->getPost('stockcheckdate', ''),
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customername' => $request->getPost('obj_customer_name', ''),
            'goods_sysno' => $request->getPost('goods_sysno', ''),
            'goodsname' => $request->getPost('goodsname', ''),
            'zj_employee_sysno' => $request->getPost('zj_employee_sysno', ''),
            'zj_employeename' => $request->getPost('zj_employeename', ''),
            'stockcheckstatus' => $stockcheckstatus,
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'updated_at' => '=NOW()'
        );

        $stockadjustinfo = $S->getstockadjustById($id);
        if($stockadjustinfo['stockinstatus']==7){
            $input['stockinstatus'] = 7;
        }

        $res = $S->updatestockadjust($id, $input, $stockadjustdetaildata,$stockcheckstatus);
        if ($res['statusCode'] == '200') {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($res['msg'], $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $S->getstockadjustById($res['msg']);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败,'.$res['msg']);
        }
    }

    /*
     * 删除库存调整订单
     */
    public function deljsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('sysno',0);
        $S = new StockadjustModel(Yaf_Registry::get('db'),Yaf_Registry::get('mc'));
        $data = $S ->getstockadjustById($id);

        if($data['stockcheckstatus']!= 2 &&$data['stockcheckstatus']!= 6){
            COMMON::result(300,'只有暂存和退回的订单才能删除，不能删除');
            return ;
        }

        $input = array(
            'isdel' => 1,
            'updated_at' => '=NOW()'
        );

        if($S->delStockadjust($id,$input)){
            $row = $S->getstockadjustById($id);
            COMMON::result(200,'删除成功',$row);
        }else{
            COMMON::result(300,'删除失败');
        }
    }

    /*
     * 审核库存调整订单
     */
    public function auditJsonAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockcheckstatus = $request->getPost('stockcheckstatus', '');
        $auditreason = $request->getPost('auditreason', '');
        $stockadjustdetaildata = $request->getPost('stockadjustdetaildata', "");
        $stockadjustdetaildata = json_decode($stockadjustdetaildata, true);

        $S = new StockadjustModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        if ($stockcheckstatus == 6) {
            if ($auditreason == '') {
                COMMON::result(300, '审核备注不能为空');
                return;
            }
            $status = 6;
        } elseif ($stockcheckstatus == 4) {
            $status = 4;
        }

        $input = array(
            'stockcheckno' => $request->getPost('stockcheckno', ''),
            'stockcheckstatus' => $status,
            'customer_sysno' => $request->getPost('obj_customer_sysno', ''),
            'customername' => $request->getPost('obj_customer_name', ''),
            'auditreason' => $auditreason,
            'updated_at' => '=NOW()'
        );

        $res = $S->auditStockadjust($id, $input,$stockadjustdetaildata);
        if ($res['statusCode'] == '200') {
            $attach = $request->getPost('attachment', array());
            if (count($attach) > 0) {
                $res = $A->addAttachModelSysno($id, $attach);
                if (!$res) {
                    COMMON::result(300, '添加附件失败');
                    return;
                }
            }

            $row = $S->getStockadjustById($id);
            COMMON::result(200, '审核成功', $row);
        } else {
            COMMON::result(300, '审核失败,'.$res['msg']);
        }
    }

    /*
     *作废库存调整订单
     */
    public function blankstockadjustAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $abandonreason = $request->getPost('abandonreason', '');
        $stockadjustdetaildata = $request->getPost('stockadjustdetaildata', "");
        $stockadjustdetaildata = json_decode($stockadjustdetaildata, true);

        $S = new StockadjustModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        if ($abandonreason == '') {
            COMMON::result(300, '作废备注不能为空');
            return;
        }

        $input = array(
            'stockcheckstatus' => 5,
            'abandonreason' => $abandonreason,
            'updated_at' => '=NOW()'
        );

        $res = $S->blankStockadjust($id, $input,$stockadjustdetaildata);
        if ($res['statusCode'] == '200') {
            $row = $S->getStockadjustById($id);
            COMMON::result(200, '作废成功', $row);
        } else {
            COMMON::result(300, '作废失败,'.$res['msg']);
        }
    }

    /*
     * 库存调整订单上传附件
     */
    public function addcontractattachAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id','');

        $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        $attach =  $request->getPost('attachment',array());

        if(count($attach) > 0){
            $res =  $A->addAttachModelSysno($id,$attach);
            if($res){
                COMMON::result(200,'添加附件成功');
            }else{
                COMMON::result(300,'添加附件失败');
                return;
            }
        }else{
            COMMON::result(300,'请添加附件再上传');
        }
    }

    /*
     * 库存调整订单导出excel
     */
    public function excelAction(){
        $request = $this->getRequest();
        $search = array(
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
            'bar_no' => $request->getPost('bar_no', ''),
            'customer_sysno'=>$request->getPost('customer_sysno', ''),
            'goods_sysno'=>$request->getPost('goods_sysno', ''),
            'bar_stockcheckstatus' => $request->getPost('bar_stockcheckstatus', '-100'),
            'page' => false,
        );

        $S = new StockadjustModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $stockcarindata = $S->searchStockadjust($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("当前库存调整订单列表")
            ->setSubject("列表")
            ->setDescription("当前库存调整订单列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '库存单号'),
            array('B1:B1', 'B1', '005E9CD3', '调整时间'),
            array('C1:C1', 'C1', '005E9CD3', '客户名称'),
            array('D1:D1', 'D1', '005E9CD3', '货品名称'),
            array('E1:E1', 'E1', '005E9CD3', '调整数量（吨）'),
            array('F1:F1', 'F1', '0094CE58', '单据状态'),
            array('G1:G1', 'G1', '005E9CD3', ''),
            array('H1:H1', 'H1', '0094CE58', ''),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('当前库存调整订单列表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I','J','K','L','M','N','O','P','Q','R');

        foreach ($stockcarindata['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['stockcheckno'];
                        break;
                    case 1:
                        $value = $item['stockcheckdate'];
                        break;
                    case 2:
                        $value = $item['customername'];
                        break;
                    case 3:
                        $value = $item['goodsname'];
                        break;
                    case 4:
                        $value = $item['beqty'];
                        break;
                    case 5:
                        if ($item['stockcheckstatus']==2) {
                            $value = "暂存";
                        }
                        else if ($item['stockcheckstatus']==3) {
                            $value = "待审核";
                        }else if ($item['stockcheckstatus']==4) {
                            $value = "已审核";
                        }else if ($item['stockcheckstatus']==5) {
                            $value = "作废";
                        }else{
                            $value = "新建";
                        }
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="库存调整订单.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    /*
     * 二维数组排序
     */
    public function list_sort_by($list, $field, $sortby = 'asc')
    {
        if (is_array($list))
        {
            $refer = $resultSet = array();
            foreach ($list as $i => $data)
            {
                $refer[$i] = &$data[$field];
            }
            switch ($sortby)
            {
                case 'asc': // 正向排序
                    asort($refer);
                    break;
                case 'desc': // 逆向排序
                    arsort($refer);
                    break;
                case 'nat': // 自然排序
                    natcasesort($refer);
                    break;
            }
            foreach ($refer as $key => $val)
            {
                $resultSet[] = &$list[$key];
            }
            return $resultSet;
        }
        return false;
    }

}
<?php
/**
 * 收款单
 * User: Alan
 * Date: 2016-12-2
 * Time: 10:55:42
 */


class ReceivableController extends Yaf_Controller_Abstract
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

    //查询收款单列表
    public function listAction()
    {
        $request = $this->getRequest();
        $params = array(
            'begin_time' => $request->getPost('startdate',0),
            'end_time' => $request->getPost('enddate',0),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'customer_sysno' => $request->getPost('customer_sysno',0),
        );
        $search = array(
        'page' => false,
        );

        $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $C->searchCustomer($search);
        $params['customerlist'] =  $list['list'];
        // $params['customer_sysno'] =  0;
        // $params['start'] =  0;
        // $params['end'] =  0;
        $this->getView()->make('receivable.list',$params);
    }

    //查询收款单列表数据
    public function listJsonAction()
    {
        $request = $this->getRequest();
        $cus = $request->getParam('cus','');
        $start = $request->getParam('start','');
        $end = $request->getParam('end','');
        // var_dump($cus);exit();
        $search = array (
            'begin_time' => $request->getPost('begin_time',$start),
            'end_time' => $request->getPost('end_time',$end),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'orders'  => $request->getPost('orders',''),
            'receivablestatus' => $request->getPost('receivablestatus',''),
            'customer_sysno' => $request->getPost('customer_sysno',$cus),
        );

        $R = new ReceivableModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $R->getFinanceReceivable($search);      
        
        echo json_encode($list);
    }

    // 查看编辑 -- 页面展示
    public function editAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $mode = $request->getParam('mode','');

        if(!isset($id)) {
            $id = 0;
        }
        $R = new ReceivableModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

        if(!$id){
            $action = "/receivable/newJson/";
            $params =  array();
            $params['detaillist'] = json_encode(array());
        } else {
            if($mode=='edit'){
                $action = "/receivable/editJson/";
            }elseif($mode=='audit'){
                $action = "/receivable/auditJson/";
            }

            $params = $R->getReceivableById($id);
            //获取结算方式
            $params['settlementname'] = $R->getsettlementname($params['base_settlement_sysno']);
        }
        $params['id'] =  $id;
        $params['action'] =  $action;
        // var_dump($action);exit;
        $params['void'] = $request->getParam('void',0);
        $search = array(
            'bar_status' => 1,
            'page' => false,
        );

        $list = $C->searchCustomer($search);
        $params['customerlist'] =  $list['list'];
        $P = new CompanyModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $listInfo = $P->searchCompany($search);
        $params['companylist'] =  $listInfo['list'];

        $arr = array(
            'status' => 1
        );
        $settlement = new SettlementModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $data = $settlement->getSettlement($arr);
        $params['settlementlist'] = $data['list'];
        // var_dump($params['settlementlist']);exit();

        $this->getView()->make('receivable.receivableedit',$params);
    }

    //通过客户获取开票通知单
    public function detailjsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id',0);
        $startdate =  $request->getPost('startdate','');
        $enddate =  $request->getPost('enddate','');

        if($id == '' || $id == '0')
        {
            COMMON::result(300, '客户名称不能为空');
            return;
        }

        $I = new InvoiceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $search = array(
            'customer_sysno' =>	 $id ,
            'bar_startdate' =>	 $startdate ,
            'bar_enddate' =>	 $enddate ,
            'page' => false
        );
        $detailData = $I->searchInvoice($search);
        // var_dump($detailData);
        $list = $detailData['list'];
        echo json_encode($list);
    }

    // 新增收款单
    public function newJsonAction()
    {
        $request = $this->getRequest();

        $R = new ReceivableModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $input = array(
            'receivableno' => COMMON::getCodeId('R'),
            'receivabledate' =>$request->getPost('receivabledate', ''),
            'customer_sysno' =>$request->getPost('customer_sysno', ''),
            'customername' =>$request->getPost('customer_name', ''),
            'base_company_sysno' =>$request->getPost('base_company_sysno', ''),
            'base_companyname' =>$request->getPost('base_companyname', ''),
            'base_settlement_sysno' =>$request->getPost('settlement_sysno', ''),
            'costreceivable' =>$request->getPost('costreceivable', ''),
            'receivablestatus' =>$request->getPost('receivablestatus', ''),
            'status' => 1,
            'isdel' => 0,
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()'
        );
        // echo "<pre>";
        // var_dump($input);exit();
        if ($id = $R->addReceivble($input)) {
             //$row = $R->getReceivbleById($id);
            COMMON::result(200, '添加成功');
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    //编辑开票单
    public function editJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $stockmarks = $request->getPost('stockmarks');
        $receivablestatus = $request->getPost('receivablestatus','');
        // var_dump($receivablestatus);exit();
        $R = new ReceivableModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        if($receivablestatus==5 || $receivablestatus==4 ){
            $input = array(
                'receivablestatus' =>$receivablestatus,
                'costreceivable' =>$request->getPost('costreceivable', ''),
                'customer_sysno' =>$request->getPost('customer_sysno', ''),
                'updated_at' => '=NOW()'
                );
        }else{
            $input = array(
                'receivabledate' =>$request->getPost('receivabledate', ''),
                'customer_sysno' =>$request->getPost('customer_sysno', ''),
                'customername' =>$request->getPost('customer_name', ''),
                'base_company_sysno' =>$request->getPost('base_company_sysno', ''),
                'base_companyname' =>$request->getPost('base_companyname', ''),
                'base_settlement_sysno' =>$request->getPost('settlement_sysno', ''),
                'costreceivable' =>$request->getPost('costreceivable', ''),
                'receivablestatus' =>$receivablestatus,
                'status' => 1,
                'isdel' => $request->getPost('isdel', '0'),
                'updated_at' => '=NOW()'
            );
        }
        if ($R->updateReceivble($id,$input,$stockmarks)) {
            // $row = $R->getInvoiceById($id);
            COMMON::result(200, '更新成功');
        } else {
            COMMON::result(300, '更新失败');
        }

    }

    /**
     * 收款单客户联动开票单位json
     */
    public function compayJsonAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);

        $C = new ReceivableModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $data = $C->getCompanyJson($id);
        $list[] = array('value'=>'','label'=>'请选择');
        foreach ($data['list'] as $key => $value) {
            if (!empty($value['invoice_companyname'])){
                $list[] = array('value'=>$value['invoice_company_sysno'],'label'=>$value['invoice_companyname']);
            }
        }
        foreach ($list as $v)
        {
            $v = join(",",$v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
            $temp[] = $v;
        }
        $temp = array_unique($temp); //去掉重复的字符串,也就是重复的一维数组
        foreach ($temp as $k => $v)
        {
            $temp[$k] = explode(",",$v); //再将拆开的数组重新组装
        }
        echo json_encode($temp);
    }

     /**
      * 审核通过
      */
     public function auditJsonAction()
     {
         $request = $this->getRequest();
         $id = $request->getPost('id', 0);
         $stockmarks = $request->getPost('stockmarks');
         $input = array(
            'receivablestatus' =>$request->getPost('receivablestatus', ''),
            'costreceivable' =>$request->getPost('costreceivable', ''),
            'customer_sysno' =>$request->getPost('customerid', ''),
            'updated_at' => '=NOW()'
         );
         // echo "<pre>";
         // var_dump($input);exit();
         // if ($input['receivablestatus'] == 1) {
         //     if ($request->getPost('stockmarks') == '') {
         //         COMMON::result(300, '审核备注不能为空');
         //         return;
         //     }
         // }

         $R = new ReceivableModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

         if ($R->updateReceivble($id, $input, $stockmarks)) {
             //$row = $R->getInvoiceById($id);
             COMMON::result(200, '审核成功');
         } else {
             COMMON::result(300, '审核失败');
         }

     }

    /**
     * 获取明细数据
     */
    public function adddetailJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id','0');

        if(!isset($id)) {
            $id = 0;
        }
        $R = new ReceivableModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $R->getReceivabledetailById($id);
        echo json_encode($list);
    }


    /**
     * 删除 收款单
     */
    public function deljsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno',0);
        $R = new ReceivableModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $params = $R->getReceivableById($id);
        if($params['receivablestatus']!=2 && $params['receivablestatus']!=5){
            COMMON::result(300,'不是暂存状态的单据不能删除');
            exit();
        }
        // var_dump($id);exit;
        $R = new ReceivableModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        if($R->delReceivable($id)){
            COMMON::result(200,'删除成功');
        }else{
            COMMON::result(300,'删除失败');
        }
    }

    /**
     * 查看收款单
     */
    public function lookAction()
    {
        $id = $this->getRequest()->getParam('id','');

        $R = new ReceivableModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $params = $R->getReceivableById($id);
        // var_dump($id); exit;

            //获取结算方式
        $params['settlementname'] = $R->getsettlementname($params['base_settlement_sysno']);
        $search = array(
        'page' => false,
        );
        $params['id'] = $id;
        $list = $C->searchCustomer($search);
        $params['customerlist'] =  $list['list'];

        $P = new CompanyModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $listInfo = $P->searchCompany($search);
        $params['companylist'] =  $listInfo['list'];
        // var_dump($params['sysno']);
        $this->getView()->make('receivable.look',$params);
    }

    /**
     * 作废
     */
    public function recevableVoidAction()
    {   
        $request = $this->getRequest();

        $id = $request->getParam('id',''); 

        $stockmarks = urldecode($request->getParam('stockmarks',''));

        $costreceivable = $request->getParam('costreceivable','');

        $customer_sysno = $request->getParam('customer_sysno','');
        // var_dump($stockmarks);exit;

        $input = array(
            'receivablestatus' =>6,
            'customer_sysno'=> $customer_sysno,
            'updated_at' => '=NOW()'
        );
        $R = new ReceivableModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        $remaincost = $R->getcustomerCost($customer_sysno);

        if($costreceivable>$remaincost){
            COMMON::result(300,'该客户金额余量不足不允许作废！');
            exit();
        }

        $res = $R->updateReceivble($id,$input,$stockmarks,$costreceivable);
        if($res){
            COMMON::result(200,'作废成功！');
        }else{
            COMMON::result(300,'作废失败！');
        }     
    }

    /**
     * excel导出
     * @return [type] [description]
     */
    public function excelAction()
    {

        $request = $this->getRequest();

        $search = array (
            'begin_time' => $request->getPost('begin_time',''),
            'end_time' => $request->getPost('end_time',''),
            'orders'  => $request->getPost('orders',''),
            'receivablestatus' => $request->getPost('receivablestatus',''),
            'customer_sysno' => $request->getPost('customer_sysno',''),
            'page' => false,
        );

        $R = new ReceivableModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $R->getFinanceReceivable($search);  
        // var_dump($params); exit();
        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("国烨云仓")
            ->setTitle("收款单列表")
            ->setSubject("列表")
            ->setDescription("收款单列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '收款单号'),
            array('B1:B1', 'B1', '005E9CD3', '收款日期'),
            array('C1:C1', 'C1', '005E9CD3', '客户名称'),
            array('D1:D1', 'D1', '0094CE58', '结算方式'),
            array('E1:E1', 'E1', '0094CE58', '收款单位'),
            array('F1:F1', 'F1', '0094CE58', '收款金额'),
            array('G1:G1', 'G1', '0094CE58', '单据状态'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('收款单列表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G');

        $receivablestatus = array();
        $receivablestatus['1'] = '新建';
        $receivablestatus['2'] = '暂存';
        $receivablestatus['3'] = '待审核';
        $receivablestatus['4'] = '已审核';
        $receivablestatus['5'] = '退回';
        $receivablestatus['6'] = '作废';

        foreach ($list['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['receivableno'];
                        break;
                    case 1:
                        $value = $item['receivabledate'];
                        break;
                    case 2:
                        $value = $item['customername'];
                        break;
                    case 3:
                        $value = $item['settlementname'];
                        break;
                    case 4:
                        $value = $item['base_companyname'];
                        break;
                    case 5:
                        $value = $item['costreceivable'];
                        break;
                    case 6:
                        $value = $receivablestatus[$item['receivablestatus']];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="收款单列表.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');   
    }
}
<?php

class Report_CustomerfinanceController extends Yaf_Controller_Abstract {
    /**
     * IndexController::init()
     *
     * @return void
     */
    public function init() {
        # parent::init();
    }

    public function listAction()
    {
        $request = $this->getRequest();

        $params = array();
        $customer_name = $request->getPost('customer_name','');
        $goods_name = $request->getPost('goods_name','');
        $begin_time = $request->getPost('begin_time','');
        $end_time = $request->getPost('end_time','');
        if(isset($customer_name))
        {
            $params['customer_name'] = $customer_name;
        }
        if(isset($customer_name))
        {
            $params['goods_name'] = $goods_name;
        }
        if(isset($begin_time))
        {
            $params['begin_time'] = $begin_time;
        }
        if(isset($end_time))
        {
            $params['end_time'] = $end_time;
        }
        $this->getView()->make('customerfinance.list', $params);
    }

    public function listJsonAction() {
        $request = $this->getRequest();

        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'customer_name' => $request->getPost('customer_name',''),
            'goods_name' => $request->getPost('goods_name',''),
            'bar_status' => $request->getPost('bar_status','-100'),
            'bar_isdel' => $request->getPost('bar_isdel','-100'),
            'bar_coststatus' => $request->getPost('bar_coststatus','-100'),
            'begin_time' => $request->getPost('startdate',date('Y-m-d',strtotime('-1 months'))),
            'end_time' => $request->getPost('enddate',date('Y-m-d',time())),
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),
            'orders'  => $request->getPost('orders',''),
        );

        $R = new Report_CustomerfinanceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $R->getList($search);

        echo json_encode($list);
    }

    public function detaillistAction()
    {
        $request = $this->getRequest();

        $params = array();
        $customer_name = $request->getPost('customer_name','');
        $goods_name = $request->getPost('goods_name','');
        $begin_time = $request->getPost('begin_time','');
        $end_time = $request->getPost('end_time','');
        if(isset($customer_name) && $customer_name!='')
        {
            $params['customer_name'] = $customer_name;
        }
        if(isset($goods_name) && $goods_name!='')
        {
            $params['goods_name'] = $goods_name;
        }
        if(isset($begin_time) && $begin_time!='')
        {
            $params['begin_time'] = $begin_time;
        }
        if(isset($end_time) && $end_time!='')
        {
            $params['end_time'] = $end_time;
        }
        $this->getView()->make('customerfinance.detaillist', $params);
    }

    public function detaillistJsonAction() {
        $request = $this->getRequest();

        $customer_name = urldecode($request->getParam('customer_name'));
        $goods_name = urldecode($request->getParam('goods_name'));
        $begin_time = urldecode($request->getParam('begin_time',date('Y-m-d',strtotime('-1 months'))));
        $end_time = urldecode($request->getParam('end_time',date('Y-m-d',time())));


        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'customer_name' => $request->getPost('customer_name',$customer_name),
            'goods_name' => $request->getPost('goods_name',$goods_name),
            'shipname' => $request->getPost('shipname',''),
            'bar_status' => $request->getPost('bar_status','-100'),
            'bar_isdel' => $request->getPost('bar_isdel','-100'),
            'bar_coststatus' => $request->getPost('bar_coststatus','-100'),
            'begin_time' => $request->getPost('startdate',$begin_time),
            'end_time' => $request->getPost('enddate',$end_time),
            'pageCurrent' => COMMON :: P(),
            'pageSize' => COMMON :: PR(),
            'orders'  => $request->getPost('orders',''),
        );

        $R = new Report_CustomerfinanceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $R->getdetailList($search);

        echo json_encode($list);
    }

    public function listexcelAction() {

        $request = $this->getRequest();

        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'customer_name' => $request->getPost('customer_name',''),
            'goods_name' => $request->getPost('goods_name',''),
            'bar_status' => $request->getPost('bar_status','-100'),
            'bar_isdel' => $request->getPost('bar_isdel','-100'),
            'bar_coststatus' => $request->getPost('bar_coststatus','-100'),
            'begin_time' => $request->getPost('begin_time',date('Y-m-d',strtotime('-1 months'))),
            'end_time' => $request->getPost('end_time',date('Y-m-d',time())),
            'page'=>false,
        );
        $R = new Report_CustomerfinanceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $R->getList($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("客户费用汇总表")
            ->setSubject("列表")
            ->setDescription("客户费用汇总表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '客户'),
            array('B1:B1', 'B1', '005E9CD3', '品名'),
            array('C1:C1', 'C1', '005E9CD3', '金额'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('客户费用汇总表');

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

        foreach ($list['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['customer_name'];
                        break;
                    case 1:
                        $value = $item['goodsname'];
                        break;
                    case 2:
                        $value = $item['totalsprice'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="客户费用汇总表.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function detaillistexcelAction() {

        $request = $this->getRequest();

        $search = array (
            'bar_no'=>$request->getPost('bar_no',''),
            'customer_name' => $request->getPost('customer_name',''),
            'goods_name' => $request->getPost('goods_name',''),
            'shipname' => $request->getPost('shipname',''),
            'bar_status' => $request->getPost('bar_status','-100'),
            'bar_isdel' => $request->getPost('bar_isdel','-100'),
            'bar_coststatus' => $request->getPost('bar_coststatus','-100'),
            'begin_time' => $request->getPost('begin_time',date('Y-m-d',strtotime('-1 months'))),
            'end_time' => $request->getPost('end_time',date('Y-m-d',time())),
            'page'=>false,
        );
        $R = new Report_CustomerfinanceModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $R->getdetailList($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("客户费用明细表")
            ->setSubject("列表")
            ->setDescription("客户费用明细表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '入库日期'),
            array('B1:B1', 'B1', '005E9CD3', '船名'),
            array('C1:C1', 'C1', '005E9CD3', '进货数量（吨）'),
            array('D1:D1', 'D1', '0094CE58', '已提数量（吨）'),
            array('E1:E1', 'E1', '0094CE58', '损耗（吨）'),
            array('F1:F1', 'F1', '0094CE58', '费用明细'),
            array('G1:G1', 'G1', '0094CE58', '数量'),
            array('H1:H1', 'H1', '0094CE58', '单价'),
            array('I1:I1', 'I1', '0094CE58', '天数（天）'),
            array('J1:J1', 'J1', '0094CE58', '总金额（元）'),
            array('K1:K1', 'K1', '0094CE58', '开票状态'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('客户费用汇总表');

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

        foreach ($list['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['instockdate'];
                        break;
                    case 1:
                        $value = $item['shipname'];
                        break;
                    case 2:
                        $value = $item['instockqty'];
                        break;
                    case 3:
                        $value = $item['outstockqty'];
                        break;
                    case 4:
                        $value = $item['ullage'];
                        break;
                    case 5:
                        $value = $item['costname'];
                        break;
                    case 6:
                        $value = $item['costqty'];
                        break;
                    case 7:
                        $value = $item['unitprice'];
                        break;
                    case 8:
                        $value = $item['datenum'];
                        break;
                    case 9:
                        $value = $item['totalprice'];
                        break;
                    case 10:
                        if ($item['coststatus']==1) {
                            $value = "未生效";
                        }else if ($item['coststatus']==2) {
                            $value = "未开票";
                        }
                        else if ($item['coststatus']==3) {
                            $value = "开票待审核";
                        }else if ($item['coststatus']==4) {
                            $value = "已开票";
                        }else if ($item['coststatus']==5) {
                            $value = "已关闭";
                        }
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="客户费用明细表.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

}
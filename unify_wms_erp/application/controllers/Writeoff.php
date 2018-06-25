<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/6 0006
 * Time: 14:04
 */
Class WriteoffController extends yaf_controller_abstract
{
    public $request;

    public function init()
    {
        $this->request = $this->getRequest();
    }

    public function listAction()
    {
        $params = array(
            'page' => false
        );
        $c = new CustomerModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $customerlist = $c->searchCustomer($params);
        $params['customerlist'] = $customerlist['list'];
        $this->getView()->make('writeoff.list', $params);
    }

    public function listJsonAction()
    {
        $request = $this->getRequest();
        $pageSize = $request->getPost('pageSize', '14');
        $pageCurrent = $request->getPost('pageCurrent', '1');
        $search = array(
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'pageCurrent' => $pageCurrent,
            'pageSize' => $pageSize,
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
        );
        $D = new WriteoffModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $list = $D->searchWriteofflist($search);
        if ($list['totalRow'] == 0) {
            $list = array();
        }
        echo json_encode($list);
    }

    public function editAction()
    {
        $request = $this->getRequest();
        $id = $request->getRequest('id', 0);
        $params['id'] = $id;

        $customer = new CompanyModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $customer->searchCompany(['page' => false, 'bar_status' => 1]);
        $params['customerlist'] = $list['list'];

        $params['action'] = '/Writeoff/addJson';

        $this->getView()->make('writeoff.edit', $params);
    }

    //开票通知页面参数
    public function addinvoiceAction()
    {
        $request = $this->getRequest();
        $cid = $request->getParam('cid', '0');
        $invoice_sysno = $request->getParam('invoice_sysno', '');
        if (!$invoice_sysno) $invoice_sysno = '-100';
        $params['wo_customer_sysno'] = $cid;
        $params['invoice_sysno'] = $invoice_sysno;
        $this->getView()->make('writeoff.invoicelist', $params);
    }

    public function showAction()
    {
        $request = $this->getRequest();
        $id = $request->getRequest('id', 0);
        $writeoff = new WriteoffModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $writeofflist = $writeoff->getWriteoffbyId($id);

        $params['id'] = $id;
        $params['writeoffno'] = $writeofflist['writeoffno'];
        $params['writeoffdate'] = $writeofflist['writeoffdate'];
        $params['writestatus'] = $writeofflist['writestatus'];
        $params['writeoffcost'] = $writeofflist['writeoffcost'];
        $params['wo_customer_sysno'] = $writeofflist['customer_sysno'];

        $customer = new CustomerModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $customer->searchCustomer(['page' => false, 'bar_status' => 1]);
        $params['customerlist'] = $list['list'];

        $detaillist = $writeoff->getWriteoffDetailbyId($id);

        $params['detaillist'] = json_encode($detaillist);

        $this->getView()->make('writeoff.show', $params);
    }

    public function addJsonAction()
    {
        $request = $this->getRequest();
        $user = Yaf_Registry::get(SSN_VAR);
        $writeoff = new WriteoffModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $data = array(
            'writeoffno' => COMMON::getCodeId('H'),
            'writeoffdate' => $request->getPost('writeoffdate', ''),
            'customer_sysno' => $request->getPost('wo_customer_sysno', ''),
            'customername' => $request->getPost('wo_customer_name', ''),
            'writeoffcost' => $request->getPost('writeoffcost', ''),
            'writestatus' => 2,# 1新建 2完成
            'hx_employee_sysno' => $user['sysno'],
            'hx_employeename' => $user['employeename'],
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
        );
        $writeoff_invoice_detail = $request->getPost('writeoff_invoice_detail', '');
        $writeoff_invoice_detail = json_decode($writeoff_invoice_detail, true);
        $id = $writeoff->addwriteoff($data, $writeoff_invoice_detail);
        if ($id) {
            $row = $writeoff->getWriteoffbyId($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    public function delJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);
        if ($id == 0) {
            COMMON::result('300', '请选择有效数据!');
            return;
        }
        $D = new WriteoffModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $data = array(
            'isdel' => '1',
            'updated_at' => '=NOW()',
        );
        $writeoff_invoice_detail = $D->getWriteoffDetailbyId($id);
        $flowmemo = '删除';
        $delres = $D->delWriteoff($id, $data, $flowmemo, $writeoff_invoice_detail);
        $row = $D->getWriteoffbyId($id);
        if (!$delres) {
            COMMON::result('300', '删除失败');
        } else {
            COMMON::result('200', '删除成功', $row);
        }
    }

    public function excelAction()
    {
        $request = $this->getRequest();
        $search = array(
            'customer_sysno' => $request->getPost('stock_customer_sysno', ''),
            'page' => '',
            'begin_time' => $request->getPost('begin_time', ''),
            'end_time' => $request->getPost('end_time', ''),
        );
        $D = new WriteoffModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $list = $D->searchWriteofflist($search);

        if ($list['totalRow'] == 0) {
            COMMON::result(300, '数据为空，无法导出');
            return;
        }
        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("核销单据")
            ->setSubject("列表")
            ->setDescription("核销单据");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '核销单号'),
            array('B1:B1', 'B1', '005E9CD3', '核销日期'),
            array('C1:C1', 'C1', '005E9CD3', '客户名称'),
            array('D1:D1', 'D1', '0094CE58', '核销金额'),
            array('E1:E1', 'E1', '0094CE58', '核销人'),
        );

        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('核销单据');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E');

        foreach ($list['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['writeoffno'];
                        break;
                    case 1:
                        $value = $item['writeoffdate'];
                        break;
                    case 2:
                        $value = $item['customername'];
                        break;
                    case 3:
                        $value = $item['writeoffcost'];
                        break;
                    case 4:
                        $value = $item['hx_employeename'];
                        break;
                    default:
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="核销单据.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function noticelistJsonAction(){
        $request = $this->getRequest();
        $cus = $request->getParam('cus',0);
        $invoice_sysno = $request->getPost('invoice_sysno','-100');

        $search = array(
            'invoice_company_sysno' => $request->getPost('wo_customer_sysno', $cus),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'bar_status' => $request->getPost('bar_status',''),
            'invoice_sysno' => $invoice_sysno,
            'orders' => 'invoicedate desc',
        );

        $D = new WriteoffModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $list = $D->searchInvoice($search);

        foreach ($list['list'] as $key => $value) {
            $list['list'][$key]['period'] = $value['coststartdate'] . '至' . $value['costenddate'];

        }
        echo json_encode($list);
    }

}
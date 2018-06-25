<?php

/**
 * 库存查询
 * User: josy
 * Date: 2016/11/22 0022
 * Time: 9:13
 */
class InvoiceController extends Yaf_Controller_Abstract
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
     * 显示整个后台页面框架及菜单
     *
     * @return string
     */
    public function noticelistAction()
    {
        $request = $this->getRequest();
        $params = array(
            'bar_startdate' => $request->getPost('startdate', 0),
            'bar_enddate' => $request->getPost('enddate', 0),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'customer_sysno' => $request->getPost('customer_sysno', 0),
        );
        $C = new CustomerModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $P = new CompanyModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $search = array(
            'page' => false,
        );

        $list = $C->searchCustomer($search);
        $params['customerlist'] = $list['list'];

        $list = $P->searchCompany($search);
        $params['companylist'] = $list['list'];

        // $O = new OthercostModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        // $othercost = $O->getOthercostList();
        // $params['othercost'] = $othercost;

        $this->getView()->make('invoice.noticelist', $params);
    }

    public function noticelistJsonAction()
    {
        $request = $this->getRequest();
        $cus = $request->getParam('cus',0);
        $start = $request->getParam('start','');
        $end = $request->getParam('end','');
        $invoice_sysno = $request->getPost('invoice_sysno','-100');

        $search = array(
            'bar_startdate' => $request->getPost('bar_startdate', $start),
            'bar_enddate' => $request->getPost('bar_enddate', $end),
            'bar_name' => $request->getPost('bar_name', ''),
            'bar_cost' => $request->getPost('bar_cost', ''),
            'invoice_company_sysno' => $request->getPost('invoice_company_sysno', ''),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'bar_status' => $request->getPost('bar_status',''),
            'customer_sysno' => $request->getPost('wo_customer_sysno', $cus),
            'invoice_sysno' => $invoice_sysno,
            'orders' => 'invoicedate desc',
        );

        $I = new InvoiceModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $W = new WriteoffModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        // $O = new OthercostModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $list = $I->searchInvoice($search);

        foreach ($list['list'] as $key => $value) {
            $list['list'][$key]['period'] = $value['coststartdate'] . '至' . $value['costenddate'];
            // $writeoffinfo = $W->getWriteoffInfoByInvoicesysno($value['sysno']);
            // if (!$writeoffinfo) {
            //     $list['list'][$key]['writeoffcost'] = 0;
            //     $list['list'][$key]['noreceipt'] = $value['costinvoice'];
            // } else {
            //     $list['list'][$key]['writeoffcost'] = $writeoffinfo['writeoffcost'];
            //     $list['list'][$key]['noreceipt'] = $value['costinvoice'] - $writeoffinfo['writeoffcost'] >= 0 ? ($value['costinvoice'] - $writeoffinfo['writeoffcost']) : 0;
            // }

            // $othercost = $O->getOthercostById($value['costtype']);
            // if ($othercost) {
            //     $list['list'][$key]['costtype'] = $othercost['othercostname'];
            // }else if($value['costtype'] == '0'){
            //     $list['list'][$key]['costtype'] = '仓储费';
            // }else if($value['costtype'] == '-1'){
            //     $list['list'][$key]['costtype'] = '管道费';
            // }

        }
        echo json_encode($list);

    }

    public function noticedetaillistJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', 0);
        $I = new InvoiceModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $search = array(
            'invoice_sysno' => $id,
            'page' => false
        );
        $detailData = $I->getInvoiceDetailList($search);

        echo json_encode($detailData['list']);
    }

    public function detailJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $coststartdate = $request->getPost('coststartdate', '');
        $costenddate = $request->getPost('costenddate', '');
        $berthcost = $request->getPost('berthcost', 0);
        $companygoodsname = $request->getPost('companygoodsname','');
        $companyshipname = $request->getPost('companyshipname','');
        if ($companygoodsname == '请选择'){
            $companygoodsname = '';
        }
        // $costtype = $request->getPost('costtype', '');

        $costenddate = date("Y-m-d",strtotime('+1 days',strtotime($costenddate)));
        $F = new FinancecostModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $search = array(
            'bar_customer' => $id,
            'bar_coststartdate' => $coststartdate,
            'bar_costenddate' => $costenddate,
            // 'bar_costtype' => $costtype,
            'bar_coststatus' => 2,
            'bar_goodsname' => $companygoodsname,
            'bar_shipname' => $companyshipname,
            'page' => false
        );
        if($berthcost == 1){
            $search['bar_costtype'] = -1;
        }else{
            $search['berthcost'] = 1;    //当查询的是非装卸费的时候使用
        }

        $detailData = $F->searchCostDetail($search);

        foreach ($detailData['list'] as $key => $value) {
            $detailData['list'][$key]['unitname'] = '吨';
        }

        echo json_encode($detailData['list']);
    }

    public function EditAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');

        if (!isset($id)) {
            $id = 0;
        }

        $I = new InvoiceModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $C = new CustomerModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $P = new CompanyModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        // $O = new OthercostModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        if (!$id) {
            $action = "/invoice/newJson/";
            $params = array();
            $params['detaillist'] = json_encode(array());
        } else {
            $action = "/invoice/editJson/";
            $params = $I->getInvoiceById($id);

            if ($params['invoicestatus'] > 2 && $params['invoicestatus'] != 6) {
                COMMON::result(300, '只有暂存状态才允许编辑');
                return false;
            }

            $search = array(
                'invoice_sysno' => $params['sysno'],
                'page' => false
            );

            $detailData = $I->getInvoiceDetailList($search);
            foreach ($detailData['list'] as $key => $value) {
                $detailData['list'][$key]['unitname'] = '吨';
            }

            $params['detaillist'] = json_encode($detailData['list']);


            $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

            $attach = $A->getAttachByMAS('invoice', 'notice', $id);
            if (is_array($attach) && count($attach)) {
                $files = array();
                foreach ($attach as $file) {
                    $files[] = $file['sysno'];
                }

                $params['uploaded'] = join(',', $files);
            }

            $params['attach'] = $attach;
        }

        $search = array(
            'page' => false,
        );

        $list = $C->searchCustomer($search);
        $params['customerlist'] = $list['list'];

        $list = $P->searchCompany($search);
        $params['companylist'] = $list['list'];
        $list = $I->getsearchshipname();
        $params['ship_name'] = $list;
        $list = $I->getsearchgoodsname();
        $params['goods_name'] = $list;
        $defaultComoany = $P->getDefault();
        $params['base_company_sysno'] = $defaultComoany['sysno'];
        $params['base_companyname'] = $defaultComoany['companyname'];

        $list = $I->getInvoiceById($id);
        $params['coststatus'] = $list['invoicestatus'];

        $params['id'] = $id;
        $params['action'] = $action;
        $params['status'] = COMMON::getInvoiceStatus($params['invoicestatus']);

        $this->getView()->make('invoice.noticeedit', $params);
    }

    public function newJsonAction()
    {
        $request = $this->getRequest();
        $invoicedetaildata = $request->getPost('invoicedetaildata', "");
        $invoicedetaildata = json_decode($invoicedetaildata, true);
        $berthcost = $request->getPost('berthcost', 0);
        if (count($invoicedetaildata) == 0) {
            COMMON::result(300, '开票单明细不能为空');
            return;
        }

        $I = new InvoiceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'invoiceno' => $request->getPost('invoiceno', ''),
            'invoicedate' => $request->getPost('invoicedate', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'customer_name' => $request->getPost('customer_name', ''),
            'base_company_sysno' => $request->getPost('base_company_sysno', ''),
            'base_companyname' => $request->getPost('base_companyname', ''),
            // 'costtype' => $request->getPost('costtype', '0'),
            'invoicegoodsname' => $request->getPost('invoicegoodsname', ''),
            'coststartdate' => $request->getPost('coststartdate', ''),
            'costenddate' => $request->getPost('costenddate', ''),
            'costdiscount' => $request->getPost('costdiscount', ''),
            'costinvoice' => $request->getPost('costinvoice', ''),
            'costtotal' => $request->getPost('costtotal', ''),
            'unreceivablecost' => $request->getPost('costinvoice', ''),
            'receivablecost' => 0,
            'uninvoicecost' => $request->getPost('costinvoice', ''),
            'hasinvoicecost' => 0,
            'invoicestatus' => $request->getPost('coststatus', '1'),
            'invoice_company_sysno' => $request->getPost('invoice_company_sysno', ''),
            'invoice_companyname' => $request->getPost('invoice_companyname', ''),
            'status' => $request->getPost('status', '1'),
            'isdel' => $request->getPost('isdel', '0'),
            'created_at' => '=NOW()',
            'updated_at' => '=NOW()',
            'memo' => $request->getPost('memo','')
        );

        if($berthcost == 1){
            $input['costtype'] = 2; //初步定义2为靠泊装卸费类型
        }
        if ($id = $I->addInvoice($input, $invoicedetaildata, $request->getPost('exammarks', ''))) {
            $attach =  $request->getPost('attachment',array());

            if(count($attach) > 0){
                $A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
                $res = $A->addAttachModelSysno($id,$attach);
                if(!$res){
                    COMMON::result(300,'添加附件失败');
                    return;
                }
            }
            $row = $I->getInvoiceById($id);
            COMMON::result(200, '添加成功', $row);
        } else {
            COMMON::result(300, '添加失败');
        }
    }

    public function editJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $invoicedetaildata = $request->getPost('invoicedetaildata', "");
        $invoicedetaildata = json_decode($invoicedetaildata, true);
        $berthcost = $request->getPost('berthcost', 0);
        if (count($invoicedetaildata) == 0) {
            COMMON::result(300, '开票单明细不能为空');
            return;
        }

        $I = new InvoiceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $input = array(
            'invoicedate' => $request->getPost('invoicedate', ''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'customer_name' => $request->getPost('customer_name', ''),
            'base_company_sysno' => $request->getPost('base_company_sysno', ''),
            'base_companyname' => $request->getPost('base_companyname', ''),
            // 'costtype' => $request->getPost('costtype', '0'),
            'invoicegoodsname' => $request->getPost('invoicegoodsname', ''),
            'coststartdate' => $request->getPost('coststartdate', ''),
            'costenddate' => $request->getPost('costenddate', ''),
            'costdiscount' => $request->getPost('costdiscount', ''),
            'costinvoice' => $request->getPost('costinvoice', ''),
            'unreceivablecost' => $request->getPost('costinvoice', ''),
            'uninvoicecost' => $request->getPost('costinvoice', ''),
            'costtotal' => $request->getPost('costtotal', ''),
            'invoicestatus' => $request->getPost('coststatus', '1'),
            'invoice_company_sysno' => $request->getPost('invoice_company_sysno', ''),
            'invoice_companyname' => $request->getPost('invoice_companyname', ''),
            'updated_at' => '=NOW()'
        );
        if($berthcost == 1){
            $input['costtype'] = 2; //初步定义2为靠泊装卸费类型
        }
        if ($I->updateInvoice($id, $input, $invoicedetaildata, $request->getPost('exammarks', ''))) {

            $row = $I->getInvoiceById($id);
            COMMON::result(200, '更新成功', $row);
        } else {
            COMMON::result(300, '更新失败');
        }
    }

    public function examJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $examstep = $request->getPost('examstep', 0);
        $exammarks = $request->getPost('exammarks', '');

        if ($id == 0 || $examstep == 0 || ($examstep == 6 && $exammarks == '')) {
            COMMON::result(300, '缺少参数');
            return;
        }

        $L = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $I = new InvoiceModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $F = new FinancecostModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $user = Yaf_Registry::get(SSN_VAR);

        if ($examstep == 6) {
            $data = array(
                'invoicestatus' => 6
            );

            #库存管理业务操作日志

            $res = $I->updateInvoiceData($id, $data);

            // $costdata = array(
            //     'invoice_sysno' => $id,
            //     'status' =>array(
            //         'coststatus' => 3,
            //         ),
            //     );
            // $costres = $F->updateStatusByInvoiceSysno($costdata);
            if ($res) {

                $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 14,
                    'opertype' => 5,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => $exammarks
                );

                $L->addDocLog($input);

                COMMON::result(200, '操作成功');
                return;
            } else {
                COMMON::result(300, '操作失败');
                return;
            }
        } elseif ($examstep == 4) {
            $res = $I->examInvoice($id);

            if ($res) {
                $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 14,
                    'opertype' => 3,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => $exammarks
                );

                $L->addDocLog($input);

                COMMON::result(200, '操作成功');
                return;
            } else {
                COMMON::result(300, '操作失败');
                return;
            }

        } elseif ($examstep == 5) {
            $res = $I->cancelInvoice($id);

            if ($res) {
                $input = array(
                    'doc_sysno' => $id,
                    'doctype' => 14,
                    'opertype' => 4,
                    'operemployee_sysno' => $user['employee_sysno'],
                    'operemployeename' => $user['employeename'],
                    'opertime' => '=NOW()',
                    'operdesc' => $exammarks
                );

                $L->addDocLog($input);

                COMMON::result(200, '操作成功');
                return;
            } else {
                COMMON::result(300, '操作失败');
                return;
            }

        }
        COMMON::result(300, '操作失败');
        return;
    }

    public function delJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('sysno', 0);

        $I = new InvoiceModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $params = $I->getInvoiceById($id);

        if ($params['invoicestatus'] > 2 && $params['invoicestatus'] != 6) {
            COMMON::result(300, '只有暂存状态才允许删除');
            return false;
        }

        $F = new FinancecostModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $costData = array(
            'invoice_sysno' => $id,
            'status' =>array(
                'coststatus' => 2,
                'invoice_sysno' => 0,
                ),
            );
        $costres = $F->updateStatusByInvoiceSysno($costData);

        if (!$costres) {
            COMMON::result(300, '删除失败');
            return false;
        }


        $input = array(
            'isdel' => 1
        );

        if ($I->updateInvoiceData($id, $input)) {
            COMMON::result(200, '删除成功');
        } else {
            COMMON::result(300, '删除失败');
        }
    }
    public function deldetailAction()
    {
        $request = $this->getRequest();
        $sysno = $request->getParam('sysno', 0);

        $I = new InvoiceModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $F = new FinancecostModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $costData = array(
            'sysno' => $sysno,
            'status' =>array(
                'coststatus' => 2,
                'invoice_sysno' => 0,
                ),
            );
        $costres = $F->updateDetail($costData);

        if (!$costres) {
            echo json_encode(array('code' => 300));
            return;
        }else{
            $input = array(
                'isdel' => 1
            );

            if ($I->updateInvoiceDetail($sysno, $input)) {
                echo json_encode(array('code' => 200));
                return;
            } else {
                echo json_encode(array('code' => 300));
                return;
            }

        }

    }

    public function showContentAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $type = $request->getParam('type', '');
        $I = new InvoiceModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $C = new CustomerModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $P = new CompanyModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        // $O = new OthercostModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $R = new ReceivableModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $I->getInvoiceById($id);

        $receivableinfo = $R->getReceivabledetailByInvoiceId($id);
        if ($type == 'check' && $params['invoicestatus'] != 3) {
            COMMON::result(300, '只有待审核状态才允许审核');
            return false;
        }

        if ($type == 'cancel' && $params['invoicestatus'] != 4) {
            COMMON::result(300, '只有审核状态才允许作废');
            return false;
        }

        if ($type == 'cancel' && $params['receivablecost'] > 0) {
            COMMON::result(300, '已收款金额大于0不允许作废');
            return false;
        }

        if ($type == 'print' && $params['invoicestatus'] != 4) {
            COMMON::result(300, '只有审核状态才允许打印');
            return false;
        }

        $search = array(
            'invoice_sysno' => $params['sysno'],
            'page' => false
        );

        $detailData = $I->getInvoiceDetailList($search);
        foreach ($detailData['list'] as $key => $value) {
            $detailData['list'][$key]['unitname'] = '吨';
        }

        $params['detaillist'] = json_encode($detailData['list']);


        $search = array(
            'page' => false,
        );

        $list = $C->searchCustomer($search);
        $params['customerlist'] = $list['list'];

        $list = $P->searchCompany($search);
        $params['companylist'] = $list['list'];

        $list = $I->getInvoiceById($id);

        $params['coststatus'] = $list['invoicestatus'];

        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $attach = $A->getAttachByMAS('invoice', 'notice', $id);
        if (is_array($attach) && count($attach)) {
            $files = array();
            foreach ($attach as $file) {
                $files[] = $file['sysno'];
            }

            $params['uploaded'] = join(',', $files);
        }


        $params['id'] = $id;
        $params['type'] = $type;
        $params['status'] = COMMON::getInvoiceStatus($params['coststatus']);

        // $othercost = $O->getOthercostList();
        // $params['othercost'] = $othercost;

        $this->getView()->make('invoice.showcontent', $params);
    }

    //关闭功能,主要用于已收款金额大于0且单据状态为已审核的开票通知单;
    public function closeinvoiceAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');

        $I = new InvoiceModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $F = new FinancecostModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $I->getInvoiceById($id);
        if ($params['invoicestatus'] != 4) {
            COMMON::result(300, '已审核的单据才允许关闭');
            return false;
        }
        if ($params['receivablecost'] <= 0) {
            COMMON::result(300, '已收款金额大于0的单据才允许关闭');
            return false;
        }
        $data = array(
            'invoicestatus' => 7,
        );
        $res = $I->updateInvoiceData($id, $data);

        $costData = array(
            'invoice_sysno' => $id,
            'status' =>array(
                'coststatus' => 5,
                ),
            );
        $costres = $F->updateStatusByInvoiceSysno($costData);
        if ($res && $costres) {
            COMMON::result(200, '关闭成功');
            return;
        } else {
            COMMON::result(300, '关闭失败');
            return;
        }

    }

    public function addInvoiceAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', '0');
        $I = new InvoiceModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $C = new CustomerModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $P = new CompanyModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));


        $params = $I->getInvoiceById($id);

        if ($params['invoicestatus'] != 4) {
            COMMON::result(300, '只有审核状态才允许补录发票');
            return false;
        }

        $search = array(
            'invoice_sysno' => $params['sysno'],
            'page' => false
        );

        $detailData = $I->getInvoiceDetailList($search);
        foreach ($detailData['list'] as $key => $value) {
            $detailData['list'][$key]['unitname'] = '吨';
        }

        $params['detaillist'] = json_encode($detailData['list']);


        $search = array(
            'page' => false,
        );

        $list = $C->searchCustomer($search);
        $params['customerlist'] = $list['list'];

        $list = $P->searchCompany($search);
        $params['companylist'] = $list['list'];

        $list = $I->getInvoiceById($id);

        $params['coststatus'] = $list['invoicestatus'];

        $A = new AttachmentModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $attach = $A->getAttachByMAS('invoice', 'notice', $id);
        if (is_array($attach) && count($attach)) {
            $files = array();
            foreach ($attach as $file) {
                $files[] = $file['sysno'];
            }

            $params['uploaded'] = join(',', $files);
        }

        $params['id'] = $id;
        $params['type'] = $type;
        $params['status'] = COMMON::getInvoiceStatus($params['coststatus']);
        // $O = new OthercostModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        // $othercost = $O->getOthercostList();
        // $params['othercost'] = $othercost;
        $this->getView()->make('invoice.addinvoice', $params);
    }

    public function addInvoiceJsonAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id', 0);
        $input = array(
            'isinvoice' => 1,
            'memo' => $request->getPost('memo',''),
            'invoicenumber' => $request->getPost('invoicenumber', ''),
            'hasinvoicecost' => $request->getPost('hasinvoicecost', 0),
            'uninvoicecost' => $request->getPost('uninvoicecost', 0),
            'updated_at' => '=NOW()'
        );
        $I = new InvoiceModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $res = $I->updateInvoiceData($id, $input);
        if (!$res) {
            COMMON::result(300, '添加失败');
            return false;
        } else {
            COMMON::result(200, '添加成功');
            return false;
        }
    }

    public function dbtoexcelAction()
    {

        $request = $this->getRequest();
        $search = array(
            'bar_startdate' => $request->getPost('bar_startdate', ''),
            'bar_enddate' => $request->getPost('bar_enddate', ''),
            'bar_name' => $request->getPost('bar_name', ''),
            'bar_cost' => $request->getPost('bar_cost', ''),
            'invoice_company_sysno' => $request->getPost('invoice_company_sysno', ''),
            'page' => false,
            'bar_stastus' => $request->getPost('stastus',''),
            'customer_sysno' => $request->getPost('customer_sysno', ''),
            'orders' => 'invoicedate desc',
        );

        $I = new InvoiceModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $W = new WriteoffModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        // $O = new OthercostModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $list = $I->searchInvoice($search);

        foreach ($list['list'] as $key => $value) {
            $list['list'][$key]['period'] = $value['coststartdate'] . '至' . $value['costenddate'];

            // $othercost = $O->getOthercostById($value['costtype']);
            // if ($othercost) {
            //     $list['list'][$key]['costtype'] = $othercost['othercostname'];
            // }else if($value['costtype'] == '0'){
            //     $list['list'][$key]['costtype'] = '仓储费';
            // }else if($value['costtype'] == '-1'){
            //     $list['list'][$key]['costtype'] = '管道费';
            // }

        }


        /*------------------查询筛选条件返回参数-----------------*/

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("国烨云仓")
            ->setTitle("开票通知单列表")
            ->setSubject("开票通知单列表")
            ->setDescription("开票通知单列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '通知单号'),
            array('B1:B1', 'B1', '005E9CD3', '通知日期'),
            array('C1:C1', 'C1', '005E9CD3', '客户名称'),
            array('D1:D1', 'D1', '0094CE58', '货品名称'),
            array('E1:E1', 'E1', '0094CE58', '发票抬头'),
            array('F1:F1', 'F1', '0094CE58', '是否开票'),
            array('G1:G1', 'G1', '0094CE58', '发票号'),
            array('H1:H1', 'H1', '003376B3', '结算期间'),
            array('I1:I1', 'I1', '003376B3', '总金额'),
            array('J1:J1', 'J1', '003376B3', '开票通知金额'),
            array('K1:K1', 'K1', '003376B3', '折扣金额'),
            array('L1:L1', 'L1', '003376B3', '已收款金额'),
            array('M1:M1', 'M1', '003376B3', '未收款金额'),
            array('N1:N1', 'N1', '0094CE58', '单据状态'),
        );

        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('开票通知单列表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I','J','K','L','M','N');

        foreach ($list['list'] as $item) {

            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['invoiceno'];
                        break;
                    case 1:
                        $value = $item['invoicedate'];
                        break;
                    case 2:
                        $value = $item['customer_name'];
                        break;
                    case 3:
                        $value = $item['invoicegoodsname'];
                        break;
                    case 4:
                        $value = $item['invoice_companyname'];
                        break;
                    case 5:
                        switch ($item['isinvoice']) {
                            case "0":
                                $value = "否";
                                break;
                            case "1":
                                $value = "是";
                                break;
                            default:
                                $value = "否";
                        }
                        break;
                    case 6:
                        $value = $item['invoicenumber'];
                        break;
                    case 7:
                        $value = $item['period'];
                        break;
                    case 8:
                        $value = $item['costtotal'];
                        break;
                    case 9:
                        $value = $item['costinvoice'];
                        break;
                    case 10:
                        $value = $item['costdiscount'];
                        break;
                    case 11:
                        $value = $item['receivablecost'];
                        break;
                    case 12:
                        $value = $item['unreceivablecost'];
                        break;
                    case 13:
                        switch ($item['invoicestatus']) {
                            case "2":
                                $value = "暂存";
                                break;
                            case "3":
                                $value = "待审核";
                                break;
                            case "4":
                                $value = "已审核";
                                break;
                            case "5":
                                $value = "作废";
                                break;
                            case '6':
                                $value = "退回";
                                break;
                            case '7':
                                $value = "已关闭";
                                break;
                            default:
                                $value = "新建";
                        }
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }

// Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="开票通知单列表.xlsx"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

    }


}

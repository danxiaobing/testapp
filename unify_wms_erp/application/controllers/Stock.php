<?php
/**
 * 库存查询
 * User: ty
 * Date: 2016/11/22 0022
 * Time: 9:13
 */
class StockController extends Yaf_Controller_Abstract
{
    public function init()
    {
        # parent::init();
    }

    /**
     * Title:质量列表展示
     */
    public function listAction(){
        $params=array();

        $search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
            'page' => false
        );

        //客户
        $C = new CustomerModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
        $list = $C->searchCustomer($search);
        $params['customerlist'] =  $list['list'];

        //合同

        $G = new ReportgoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $search = array(
            'bar_contractstatus'=>5,
            'bar_status' => 1,
            'bar_isdel' => 0,
        );

        $params['list'] = $G->getContractInfo($search);
        // echo "<pre>";
        // var_dump($params['contractnolist']);exit;

        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();


        $this->getView()->make('stock.list', $params);
    }

    public function stocklistJsonAction(){
        $request = $this->getRequest();

        $search = array (
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
            'begin_time' => $request->getPost('begin_time',''),
            'end_time' => $request->getPost('end_time',''),
            'customer_sysno' => $request->getPost('customer_sysno',''),
            'goodsnature' =>  $request->getPost('goodsnature',''),
            'goodsname' => $request->getPost('goodsname',''),
            'contractno' => $request->getPost('contract_no',''),
//            'isclearstock' => $request->getPost('isclearstock','0'),
            // 'orders' => $request->getPost('orders','updated_at DESC'),

        );

        $sink = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        $params = $sink->getList($search);

        echo json_encode($params);
    }

    /**
     * EXCEL导出
     */
    public function ExcelAction() {

        $request = $this->getRequest();

        $search = array (
            'page'=>false,
            'begin_time' => $request->getPost('begin_time',''),
            'end_time' => $request->getPost('end_time',''),
            'customer_sysno' => $request->getPost('customer_sysno',''),
            'goodsnature' =>  $request->getPost('goodsnature',''),
            'goodsname' => $request->getPost('goodsname',''),
            'contractno' => $request->getPost('contract_no',''),
            'isclearstock' => $request->getPost('isclearstock','0'),
            'orders' => $request->getPost('orders','updated_at DESC'),
        );

        $sink = new StockModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

        $stock = $sink->getList($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("国烨云仓")
            ->setTitle("当前库存列表")
            ->setSubject("列表")
            ->setDescription("当前库存列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '来源单号'),
            array('B1:B1', 'B1', '005E9CD3', '单据日期'),
            array('C1:C1', 'C1', '005E9CD3', '首期到期日'),
            array('D1:D1', 'D1', '0094CE58', '仓储费结算至'),
            array('E1:E1', 'E1', '0094CE58', '单据类型'),
            array('F1:F1', 'F1', '0094CE58', '清库'),
            array('G1:G1', 'G1', '0094CE58', '船名'),
            array('H1:H1', 'H1', '003376B3', '客户'),
            array('I1:I1', 'I1', '003376B3', '品名'),
            array('J1:J1', 'J1', '0094CE58', '规格'),
            array('K1:K1', 'K1', '003376B3', '计量单位'),
            array('L1:L1', 'L1', '003376B3', '入库数量'),
            array('M1:M1', 'M1', '0094CE58', '余量'),
            array('N1:N1', 'N1', '003376B3', '货物性质'),
            array('O1:O1', 'O1', '003376B3', '锁货数量'),
            array('P1:P1', 'P1', '003376B3', '是否溢罐'),
            array('Q1:Q1', 'Q1', '003376B3', '溢出吨数'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('当前库存列表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I','J','K','L','M','N','O','P','Q');

        $doctype = array();
        $doctype['1'] = '船入库';
        $doctype['2'] = '车入库';
        $doctype['3'] = '货权转移';
        $goodsnature = array();
        $goodsnature['1'] = '保税';
        $goodsnature['2'] = '外贸';
        $goodsnature['3'] = '内贸转出口';
        $goodsnature['4'] = '内贸内销';

        foreach ($stock['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                            $value = $item['firstfrom_no'];
                        break;
                    case 1:
                        if ($item['stockindate']) {
                            $value = $item['stockindate'];
                        } else {
                            $value = $item['stocktransdate'];
                        }
                        break;
                    case 2:
                        $value = $item['firstdate'];
                        break;
                    case 3:
                        $value = $item['financedate'];
                        break;
                    case 4:
                        $value = $doctype[$item['doctype']];
                        break;
                    case 5:
                        if ($item['isclearstock']) {
                            $value = "是";
                        } else {
                            $value = "否";
                        }
                        break;
                    case 6:
                        $value = $item['shipname'];
                        break;
                    case 7:
                        if ($item['customername']) {
                            $value = $item['customername'];
                        } else {
                            $value = $item['buy_customername'];
                        }
                        break;
                    case 8:
                        $value = $item['goodsname'];
                        break;
                    case 9:
                        $value = $item['goodsqualityname'];
                        break;
                    case 10:
                        $value = $item['unitname'];
                        break;
                    case 11:
                        $value = $item['inqty'];
                        break;
                    case 12:
                        $value = $item['stockqty'];
                        break;
                    case 13:
                        $value = $goodsnature[$item['goodsnature']];
                        break;
                    case 14:
                        $value = $item['clockqty'];
                        break;
                    case 15:
                        if ($item['overflag']==1) {
                            $value = '是';
                        }else{
                            $value = '否';
                        }
                        break;
                    case 16:
                        $value = $item['overqty'];
                        break;
                    // case 17:
                    //     if ($item['transferflag']==1) {
                    //         if($value = $item['transferqty']>0){
                    //             $value = $item['transferqty'];
                    //         }else{
                    //             $value = $item['instockqty'];
                    //         }
                    //     } else {
                    //         $value = '0';
                    //     }
                    //     break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="当前库存查询.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}
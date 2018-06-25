<?php

/**
 * Created by PhpStorm.
 * User: 129
 * Date: 2017/5/15
 * Time: 17:20
 */
class Report_ReporttankdayController extends Yaf_Controller_Abstract
{
    /**
     * IndexController::init()
     *
     * @return void
     */
    public function init() {
        # parent::init();
    }

    public function overdueullageAction() {
        $overdueullage = new Report_OverdueullageModel(Yaf_Registry :: get("db"));
        $res = $overdueullage -> index();
        if($res) {
            $S = new FinancecostModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
            $S->addFinancecostByPlan();
        }
        $mesage = array(
            'mes'=>1
        );
        echo json_encode($mesage);
    }

//    public function overdueullageAction(){
//        $overdueullage = new Report_OverdueullageModel(Yaf_Registry :: get("db"));
//        $res = $overdueullage -> backstock();
//        $mesage = array(
//            'mes'=>1
//        );
//        echo json_encode($mesage);
//    }

    /**
     * 储罐货物进出汇总日表
     */
    public function ListAction(){
        $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $S = new StoragetankModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $search = array(
            'page' => false,
            'isdel'=>0
        );
        $params['customers'] = $C->searchCustomer($search);
        $params['storagetanks'] = $S ->searchStoragetank($search);

        $this->getView()->make('reporttankday.list',$params);
    }

    /*
     *获取报表数据
     */
    public function ListJsonAction(){
        $request = $this->getRequest();
        $search = array(
            'tankdaydate'=>$request->getPost('tankdaydate', date('Y-m-d')),
            'customer_sysno'=>$request->getPost('customer_sysno',''),
            'storagetank_sysno'=>$request->getPost('storagetank_sysno',''),
            'shipname'=>$request->getPost('shipname',''),
            'goodsname'=>$request->getPost('goodsname',''),
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
        );
        $search['endtime'] = $search['tankdaydate'].' 23:59:59';
        $R = new Report_ReporttankdayModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $result=$R->getList($search);
        echo json_encode($result);
    }

    public function ExcelAction()
    {
        $request = $this->getRequest();
        $search = array(
            'tankdaydate'=>$request->getPost('tankdaydate', date('Y-m-d')),
            'customer_sysno'=>$request->getPost('customer_sysno',''),
            'storagetank_sysno'=>$request->getPost('storagetank_sysno',''),
            'shipname'=>$request->getPost('shipname',''),
            'goodsname'=>$request->getPost('goodsname',''),
            'page' => false,
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
        );

        $search['endtime'] = $search['tankdaydate'].' 23:59:59';
        $R = new Report_ReporttankdayModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $list = $R->getList($search);

        /*------------------查询筛选条件返回参数-----------------*/

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("货物进出汇总日表")
            ->setSubject('货物进出汇总日表')
            ->setDescription("货物进出汇总日表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '客户'),
            array('B1:B1', 'B1', '005E9CD3', '品名'),
            array('C1:C1', 'C1', '005E9CD3', '进货时间'),
            array('D1:D1', 'D1', '0094CE58', '进货船名'),
            array('E1:E1', 'E1', '0094CE58', '商检量'),
            array('F1:F1', 'F1', '0094CE58', '昨日结存量'),
            array('G1:G1', 'G1', '0094CE58', '今日出库量'),
            array('H1:H1', 'H1', '0094CE58', '今日货转出量'),
            array('I1:I1', 'I1', '003376B3', '今日倒出量'),
            array('J1:J1', 'J1', '003376B3', '损耗量'),
            array('K1:K1', 'K1', '003376B3', '今日结存量'),
            array('L1:L1', 'L1', '003376B3', '罐号'),
        );

        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('货物进出汇总日表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J','K','L');

        foreach ($list['list'] as $item) {
                $line++;
                for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['customername'];
                        break;
                    case 1:
                        $value = $item['goodsname'];
                        break;
                    case 2:
                        $value = $item['doc_time'];
                        break;
                    case 3:
                        $value = $item['shipname'];
                        break;
                    case 4:
                        $value = $item['beqty'];
                        break;
                    case 5:
                        $value = $item['qichu'];
                        break;
                    case 6:
                        $value = $item['out_num'];
                        break;
                    case 7:
                        $value = $item['transout'];
                        break;
                    case 8:
                        $value = $item['tankout'];
                        break;
                    case 9:
                        $value = $item['wastage'];
                        break;
                    case 10:
                        $value = $item['end_num'];
                        break;
                    case 11:
                        $value = $item['storagetankname'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="货物进出汇总日表.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}
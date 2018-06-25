<?php
/**
 * @Author: wu xianneng
 * @Date:   2016-12-17 11:55:36
 * @Last Modified by:   wu xianneng
 * @Last Modified time: 2016-12-17 17:06:33
 */
class Report_ReportinstockcountController extends Yaf_Controller_Abstract {
    /**
     * IndexController::init()
     *
     * @return void
     */
    public function init() {
        # parent::init();
    }

    /**
     * 入库统计表页面显示
     * @author wu xianneng
     */
    public function ListAction(){
        $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $search = array(
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
            'isdel'=>0
        );
        $params['customers']=$C->searchCustomer($search);

        $this->getView()->make('reportinstockcount.list',$params);
    }

    /**
     * 入库统计表Json数据
     * @author wu xianneng
     */
    public function ListJsonAction(){
        $request = $this->getRequest();
        $search = array(
            'year'=>$request->getPost('year',date('Y',time())),
            'page' => false
        );

        $R = new Report_ReportinstockcountModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $result=$R->getList($search);
        echo json_encode($result);
    }

    /*
     * 入库统计表excel
     */
    public function excelAction(){
        $request = $this->getRequest();
        $params = array(
            'year'=>$request->getPost('year',date('Y',time())),
            'page' => false,
        );

        $R = new Report_ReportinstockcountModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $result=$R->getList($params);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $title = $params['year'].'年入库统计表';

        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle($title)
            ->setSubject("列表")
            ->setDescription($title);

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '月份'),
            array('B1:B1', 'B1', '005E9CD3', '入库总量'),
            array('C1:C1', 'C1', '005E9CD3', '船入库总量'),
            array('D1:D1', 'D1', '0094CE58', '船数（外贸）'),
            array('E1:E1', 'E1', '0094CE58', '船数（内贸）'),
            array('F1:F1', 'F1', '0094CE58', '船入库数量（外贸）'),
            array('G1:G1', 'G1', '0094CE58', '船入库数量（内贸）'),
            array('H1:H1', 'H1', '003376B3', '槽车入库总量'),
            array('I1:I1', 'I1', '003376B3', '车数'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle($title);

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

        foreach ($result['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['month'];
                        break;
                    case 1:
                        $value = $item['totalqty'];
                        break;
                    case 2:
                        $value = $item['shipqty'];
                        break;
                    case 3:
                        $value = $item['shipoutnu'];
                        break;
                    case 4:
                        $value = $item['shipinnu'];
                        break;
                    case 5:
                        $value = $item['shipoutqty'];
                        break;
                    case 6:
                        $value = $item['shipinqty'];
                        break;
                    case 7:
                        $value = $item['carqty'];
                        break;
                    case 8:
                        $value = $item['carnu'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$title.'.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }



}
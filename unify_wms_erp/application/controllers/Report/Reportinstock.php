<?php
/**
 * @Author: wu xianneng
 */
class Report_ReportinstockController extends Yaf_Controller_Abstract {
    /**
     * IndexController::init()
     *
     * @return void
     */
    public function init() {
        # parent::init();
    }

    /**
     * 入库单
     * @author wu xianneng
     */
    public function ListAction(){
        $C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $G = new GoodsModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $search = array(
            'page' => false,
            'isdel'=>0
        );
        $params['customers']=$C->searchCustomer($search);
        $params['goods']=$G->getBaseGoods($search);
        $this->getView()->make('reportinstock.list',$params);
    }

    /**
     * 入库单Json数据
     * @author wu xianneng
     */
    public function ListJsonAction(){
        $request = $this->getRequest();
        $date2 = $request->getPost('date2',date('Y-m-d'));
        $search = array(
            'date1'=>$request->getPost('date1',date('Y-m-d')),
            'date2'=>$date2.' 23:59:59',
            'customer_sysno'=>$request->getPost('customer_sysno',''),
            'goods_sysno'=>$request->getPost('goods_sysno',''),
            'goodsname'=>$request->getPost('goodsname',''),
            'stockinno'=>$request->getPost('stockinno',''),
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
        );

        $R = new Report_ReportinstockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $result=$R->getList($search);

        echo json_encode($result);
    }

    /*
     * 入库单excel
     */
    public function excelAction(){
        $request = $this->getRequest();
        $date2 = $request->getPost('date2',date('Y-m-d'));
        $search = array(
            'date1'=>$request->getPost('date1',date('Y-m-d')),
            'date2'=>$date2.' 23:59:59',
            'customer_sysno'=>$request->getPost('customer_sysno',''),
            'goods_sysno'=>$request->getPost('goods_sysno',''),
            'page' => false
        );

        $R = new Report_ReportinstockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $result=$R->getList($search);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("客户进出货表")
            ->setSubject("列表")
            ->setDescription("客户进出货表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '进货单号'),
            array('B1:B1', 'B1', '005E9CD3', '类型'),
            array('C1:C1', 'C1', '005E9CD3', '入库日期'),
            array('D1:D1', 'D1', '005E9CD3', '罐号'),
            array('E1:E1', 'E1', '0094CE58', '品名'),
            array('F1:F1', 'F1', '0094CE58', '客户'),
            array('G1:G1', 'G1', '0094CE58', '进货车船名'),
            array('H1:H1', 'H1', '0094CE58', '提单量/货转量'),
            array('I1:I1', 'I1', '0094CE58', '商检量'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('客户进货出表');

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
                        $value = $item['stockinno'];
                        break;
                    case 1:
                         if($item['stockintype']=='1'){ $value='船入库';}
                         elseif($item['stockintype']=='2'){ $value='车入库';}
                         elseif($item['stockintype']=='3'){ $value='管入库';}
                        break;
                    case 2:
                        $value = $item['stockindate'];
                        break;
                    case 3:
                        $value = $item['storagetankname'];
                        break;
                    case 4:
                        $value = $item['goodsname'];
                        break;
                    case 5:
                        $value = $item['customername'];
                        break;
                    case 6:
                        $value = $item['shipname'];
                        break;
                    case 7:
                        $value = $item['takegoodsnum'];
                        break;
                    case 8:
                        $value = $item['beqty'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="客户进货表.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    /*
     * 入库单详情
     */
    public function detaillistAction(){
        $request = $this->getRequest();
        $params = array(
            'id'=>$request->getpost('id',''),
        );
        $this->getView()->make('reportinstock.detaillist',$params);
    }

    /*
     * 入库单基本信息数据
     */
    public function baseJsonAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id','0');
        $R = new Report_ReportinstockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $result=$R->getStockindetail($id);
        echo json_encode($result);
    }

    /*
     * 货转信息数据
     */
    public function transJsonAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id','0');
        $R = new Report_ReportinstockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $result=$R->getStocktrans($id);
        echo json_encode($result);
    }

    /*
     * 入库单详细出库数据
     */
    public function detailJsonAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id','0');
        $R = new Report_ReportinstockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $result=$R->getstockoutdetail($id);
        echo json_encode($result);
    }

    /*
     * 根据搜索条件查询出库信息
     */
    public function searchinstockAction(){
        $request = $this->getRequest();
        $instockdetaildata = $request->getPost('detail','');

        $instockdetaildata = json_decode($instockdetaildata, true);
        $shipname = $request->getPost('shipname','');
        $takegoodsno = $request->getPost('takegoodsno','');
        $customername = $request->getPost('customername','');

        $searchdata = array();
        if(!empty($instockdetaildata)){
            foreach ($instockdetaildata as $item) {
                if($shipname!=''&&$takegoodsno==''){
                    if(stripos($item['shipname'],$shipname)>=0 && is_numeric(stripos($item['shipname'],$shipname))){
                        $searchdata[] = $item;
                    }
                }elseif($shipname==''&&$takegoodsno!=''){
                    if(stripos($item['takegoodsno'],$takegoodsno)>=0 && is_numeric(stripos($item['takegoodsno'],$takegoodsno))){
                        $searchdata[] = $item;
                    }
                }elseif($shipname!=''&&$takegoodsno!=''){
                    if(stripos($item['shipname'],$shipname)>=0 && is_numeric(stripos($item['shipname'],$shipname))&&stripos($item['takegoodsno'],$takegoodsno)>=0 && is_numeric(stripos($item['takegoodsno'],$takegoodsno))){
                        $searchdata[] = $item;
                    }
                }else{
                    $searchdata[] = $item;
                }
            }
            if(!empty($searchdata))
            {
                $customersearchdata = array();
                foreach ($searchdata as $key => $value) {
                    if($customername!='')
                    {
                        if(stripos($value['customername'],$customername)>=0 && is_numeric(stripos($value['customername'],$customername))) {
                            $customersearchdata[] = $value;
                        }
                    }
                    else
                    {
                        $customersearchdata[] = $value;
                    }
                }
                if(!empty($customersearchdata))
                {
                    $searchdata = $customersearchdata;
                }
                else
                {
                    $searchdata = array();
                }
            }
        }

        echo json_encode($searchdata);
    }

    /*
     * 客户货物进出记录导出
     */
    public function exceldetailAction(){
        $request = $this->getRequest();
        $id = $request->getPost('id','0');
        $R = new Report_ReportinstockModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

        $resultbase = $R->getStockindetail($id);
        $resulttrans = $R->getStocktrans($id);
        $resultdetail = $R->getstockoutdetail($id);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("客户进出货表")
            ->setSubject("列表")
            ->setDescription("客户进出货明细");

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);

        //基本信息
        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '货主'),
            array('B1:B1', 'B1', '005E9CD3', '进货船名'),
            array('C1:C1', 'C1', '005E9CD3', '货物名称'),
            array('D1:D1', 'D1', '0094CE58', '进货日期'),
            array('E1:E1', 'E1', '0094CE58', '进货罐号'),
            array('F1:F1', 'F1', '0094CE58', '提单量'),
            array('G1:G1', 'G1', '0094CE58', '商检岸罐'),
            array('H1:H1', 'H1', '0094CE58', '损耗量'),
            array('I1:I1', 'I1', '0094CE58', '结存量'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('客户进出货明细');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H','I');

        $line ++;
        for ($i = 0; $i < count($subtitle); $i++) {
            $site = $subtitle[$i] . $line;
            $value = '';
            switch ($i) {
                case 0:
                    $value = $resultbase['customername'];
                    break;
                case 1:
                    if($resultbase['stockintype']=='2'){
                        $value = '槽车进货';
                    }elseif($resultbase['stockintype']=='3'){
                        $value = '管输';
                    }else{
                        $value = $resultbase['shipname'];
                    }
                    break;
                case 2:
                    $value = $resultbase['goodsname'];
                    break;
                case 3:
                    $value = $resultbase['stockindate'];
                    break;
                case 4:
                    $value = $resultbase['storagetankname'];
                    break;
                case 5:
                    $value = $resultbase['takegoodsnum'];
                    break;
                case 6:
                    $value = $resultbase['instockqty'];
                    break;
                case 7:
                    $value = $resultbase['ullage'];
                    break;
                case 8:
                    $value = $resultbase['stockqty'];
                    break;
            }
            $objActSheet->setCellValue($site, $value);
            $objStyle = $objActSheet->getStyle($site);
            $objStyle->getAlignment()->setHorizontal("center");
            $objStyle->getAlignment()->setVertical("center");
        }

        $arr  = array('A', 'B', 'C', 'D', 'E', 'F', 'F', 'G','H', 'I');
        $count = count($arr);
        for($i=1;$i<=$line;$i++){
            for($j=0;$j<$count;$j++){
                $objActSheet->getStyle($arr[$j].$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }

//        货转信息
        $line = $line + 2;
        $tabindex = $line;
        $mainTitle2 = array(
            array('A1:A1', 'A'.$line, '0094CE58', '日期'),
            array('B1:B1', 'B'.$line, '005E9CD3', '转让方'),
            array('C1:C1', 'C'.$line, '005E9CD3', '受让方'),
            array('D1:D1', 'D'.$line, '0094CE58', '转货单号'),
            array('E1:E1', 'E'.$line, '0094CE58', '罐号'),
            array('F1:F1', 'F'.$line, '0094CE58', '货转量'),
            array('G1:G1', 'G'.$line, '0094CE58', '损耗量'),
            array('H1:H1', 'H'.$line, '0094CE58', '结存量'),
        );
        foreach ($mainTitle2 as $row2) {

            $objActSheet->setCellValue($row2[1], $row2[3]);
            $objStyle = $objActSheet->getStyle($row2[1]);

            $objStyle->getAlignment()->setHorizontal("center");
            $objStyle->getAlignment()->setVertical("center");
            $objStyle->getAlignment()->setWrapText(true);
            $objStyle->getFont()->setBold(true);
        }

        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
        foreach ($resulttrans as $item) {
            $line ++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['stocktransdate'];
                        break;
                    case 1:
                        $value = $item['sale_customername'];
                        break;
                    case 2:
                        $value = $item['buy_customername'];
                        break;
                    case 3:
                        $value = $item['stocktransno'];
                        break;
                    case 4:
                        $value = $item['storagetankname'];
                        break;
                    case 5:
                        $value = $item['transqty'];
                        break;
                    case 6:
                        $value = $item['ullage'];
                        break;
                    case 7:
                        $value = $item['stockqty'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }

        $arr  = array('A', 'B', 'C', 'D', 'E', 'F', 'F', 'G');
        $count = count($arr);
        for($i=$tabindex;$i<=$line;$i++){
            for($j=0;$j<$count;$j++){
                $objActSheet->getStyle($arr[$j].$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }

        //出库信息
        $line = $line + 2;
        $tab2index = $line;
        $mainTitle3 = array(
            array('A1:A1', 'A'.$line, '0094CE58', '日期'),
            array('B1:B1', 'B'.$line, '005E9CD3', '类型'),
            array('C1:C1', 'C'.$line, '005E9CD3', '客户'),
            array('D1:D1', 'D'.$line, '0094CE58', '磅单号'),
            array('E1:E1', 'E'.$line, '0094CE58', '车/船号'),
            array('F1:F1', 'F'.$line, '0094CE58', '提单号'),
            array('G1:G1', 'G'.$line, '0094CE58', '实际提货量'),
        );
        foreach ($mainTitle3 as $row) {
            $objActSheet->setCellValue($row[1], $row[3]);
            $objStyle = $objActSheet->getStyle($row[1]);

            $objStyle->getAlignment()->setHorizontal("center");
            $objStyle->getAlignment()->setVertical("center");
            $objStyle->getAlignment()->setWrapText(true);
            $objStyle->getFont()->setBold(true);
        }

        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G');
        foreach ($resultdetail['list'] as $item) {
            $line ++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['date'];
                        break;
                    case 1:
                        if($item['doctype']==1){
                            $value = '船出库';
                        }elseif($item['doctype']==2){
                            $value = '车出库';
                        }elseif($item['doctype']==3){
                            $value = '管出库';
                        }
                        break;
                    case 2:
                        $value = $item['customername'];
                        break;
                    case 3:
                        $value = $item['poundsoutno'];
                        break;
                    case 4:
                        if($item['shipname']==''){
                            $value = '管输';
                        }else{
                            $value = $item['shipname'];
                        }
                        break;
                    case 5:
                        $value = $item['takegoodsno'];
                        break;
                    case 6:
                        $value = $item['beqty'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }

        $arr  = array('A', 'B', 'C', 'D', 'E', 'F', 'F', 'G');
        $count = count($arr);
        for($i=$tab2index;$i<=$line;$i++){
            for($j=0;$j<$count;$j++){
                $objActSheet->getStyle($arr[$j].$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="客户进出货明细.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}
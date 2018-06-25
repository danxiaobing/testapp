<?php

class Report_StoragetankinoutController extends Yaf_Controller_Abstract {
    /**
     * IndexController::init()
     *
     * @return void
     */
    public function ListAction(){
        $S = new Report_StoragetankinoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $params['storagetank']=$S->getStorageTank();
        $params['getgoodsinfo']=$S->getGoodsInfo();
        $this->getView()->make('reportstoragetankinout.list',$params);
    }

    public function ListJsonAction(){
        $request = $this->getRequest();

        $date = $request->getPost('date1',date('Y-m-d',strtotime('-1 months')));
        $date1 = $request->getPost('date1',date('Y-m-d',strtotime('-1 months')));
        $date2 = $request->getPost('date2',date('Y-m-d'));
        $search = array(
            'date'=>$date,
            'date1'=>date('Y-m-d',strtotime($date1)),//'-1 days',strtotime($date1)
            'date2'=>date('Y-m-d',strtotime($date2)),//'1 days',strtotime($date2)
            'tankno'=>$request->getPost('tankno',''),
            'goodsno'=>$request->getPost('goodsno',''),
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
        );
        $search['date2'] = date('Y-m-d H:i:s',strtotime('23 hours 59 minutes 59 seconds',strtotime($date2)));

        $S = new Report_StoragetankinoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $result=$S->getTankList($search);
        echo json_encode($result);
    }

    public function detaillistAction(){
        $request = $this->getRequest();
        $params['tankno'] = $request->getParam('sid','0');
        $params['startqty']= $request->getPost('startqty','0');
        $params['totalstockqty']= $request->getPost('totalstockqty','0')?sprintf("%.3f",$request->getPost('totalstockqty','0')):0;
        $params['date1']= $request->getPost('date1','0');
        $params['date2']= $request->getPost('date2','0');
        $S = new Report_StoragetankinoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $params['storagetank'] = $S->getStorageTank();
        $params['getgoodsinfo']=$S->getGoodsInfo();
        $goods_sysno = $request->getPost('goods_sysno','0');
        $storagetanksysno = $request->getPost('storagetanksysno','0');
        $params['goodsname'] = $S->getGoodsName($goods_sysno);
        $params['goods_sysno'] = $goods_sysno;
        $params['storagetankname'] = $S->getStorageTankName($storagetanksysno);
        $params['storagetanknature'] = $request->getPost('storagetanknature','0');
        $this->getView()->make('reportstoragetankinout.detail',$params);
    }

    public function tankDetailJsonAction(){
        $request = $this->getRequest();
        $sid=$request->getParam('sid','0');
        $goods_sysno=$request->getParam('goods_sysno','0');
        $startqty=$request->getParam('startqty','0');;
        $date1 = $request->getParam('date1',strtotime('-1 months'));
        $date2 = $request->getParam('date2',date('Y-m-d'));
        $search = array(
            'date1'=>$request->getPost('date1',$date1),
            'date2'=>$request->getPost('date2',$date2),
            'tankno'=>$request->getPost('tankno',$sid),
            'goods_sysno'=>$request->getPost('goods_sysno',$goods_sysno),
            'startqty'=>$startqty,
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
        );
        $search['date2'] = date('Y-m-d H:i:s',strtotime('23 hours 59 minutes 59 seconds',strtotime($date2)));
        if($search['tankno']==''){
            $search['tankno'] = 0;
        }
        $S = new Report_StoragetankinoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $params=$S->getTankDetail($search);
        echo json_encode($params);
    }

    /*
 * 获取储罐某一时间起始值
 */
    public function getStartAndEndAction(){
        $request = $this->getRequest();
        $sid = $request->getParam('sid','0');
        if($sid == ''){
            $sid = 0;
        }
        $date1 = $request->getPost('date1',date('Y-m-d',strtotime('-1 months')));
        $S = new Report_StoragetankinoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $params=$S->getStartAndEnd($sid,$date1);
        echo json_encode($params);
    }
    /*
    * 导出储罐汇总excel
    */
    public function excelAction(){
        $request = $this->getRequest();
        $date2 = $request->getPost('date2',date('Y-m-d'));
        $params = array(
            'date1'=>$request->getPost('date1',date('Y-m-d',strtotime('-1 months'))),
            'date2'=>date('Y-m-d',strtotime('1 days',strtotime($date2))),
            'tankno'=>$request->getPost('tankno',''),
            'page' => false,
        );

        $S = new Report_StoragetankinoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $result=$S->getTankList($params);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("储罐汇总列表")
            ->setSubject("列表")
            ->setDescription("储罐汇总列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '储罐号'),
            array('B1:B1', 'B1', '0094CE58', '储罐性质'),
            array('C1:C1', 'C1', '005E9CD3', '产品名称'),
            array('D1:D1', 'D1', '0094CE58', '上期结存量(吨)'),
            array('E1:E1', 'E1', '0094CE58', '本期入库量(吨)'),
            array('F1:F1', 'F1', '0094CE58', '本期出库量(吨)'),
            array('G1:G1', 'G1', '0094CE58', '本期倒入量(吨)'),
            array('H1:H1', 'H1', '003376B3', '本期倒出量(吨)'),
            array('I1:I1', 'I1', '003376B3', '本期损耗量(吨)'),
            array('J1:J1', 'J1', '003376B3', '本期退货量(吨)'),
            array('K1:K1', 'K1', '003376B3', '本期结存量(吨)'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('储罐汇总列表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I','J','K');

        foreach ($result['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['storagetankname'];
                        break;
                    case 1:
                        if($item['storagetanknature'] == '1'){
                            $value = '内贸罐';
                        }elseif($item['storagetanknature'] == '2'){
                            $value = '外贸罐';
                        }else{
                            $value = '保税罐';
                        }
                        break;
                    case 2:
                        $value = $item['goodsname'];
                        break;
                    case 3:
                        if(!$item['startqty']){
                            $value = 0;
                        }else{
                            $value = $item['startqty'];
                        }
                        break;
                    case 4:
                        if(!$item['totalinstockqty']){
                            $value = 0;
                        }else{
                            $value = $item['totalinstockqty'];
                        }
                        break;
                    case 5:
                        if(!$item['totaloutstockqty']){
                            $value = 0;
                        }else{
                            $value = $item['totaloutstockqty'];
                        }
                        break;
                    case 6:
                        if(!$item['inretank']){
                            $value = 0;
                        }else{
                            $value = $item['inretank'];
                        }
                        break;
                    case 7:
                        if(!$item['outretank']){
                            $value = 0;
                        }else{
                            $value = $item['outretank'];
                        }
                        break;
                    case 8:
                        if(!$item['totalclearqty']){
                            $value = 0;
                        }else {
                            $value = $item['totalclearqty'];
                        }
                        break;
                    case 9:
                        if(!$item['totalreturnqty']){
                            $value = 0;
                        }else{
                            $value = $item['totalreturnqty'];
                        }
                        break;
                    case 10:
                        $value = $item['totalstockqty'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="储罐汇总汇总表.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    /*
     * 储罐汇总明细导出excel
     */
    public function detailexcelAction(){
        $request = $this->getRequest();
        $date2 = $request->getPost('date2',date('Y-m-d'));
        $params = array(
            'date1'=>$request->getPost('date1',date('Y-m-d',strtotime('-1 months'))),
            'date2'=>date('Y-m-d',strtotime('1 days',strtotime($date2))),
            'tankno'=>$request->getPost('tankno',''),
            'page' => false,
        );

        $S = new Report_StoragetankinoutModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
        $result=$S->getTankDetail($params);

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("储罐汇总明细表")
            ->setSubject("列表")
            ->setDescription("储罐汇总明细表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '客户名称'),
            array('B1:B1', 'B1', '005E9CD3', '日期'),
            array('C1:C1', 'C1', '0094CE58', '方式'),
            array('D1:D1', 'D1', '0094CE58', '船名/车'),
            array('E1:E1', 'E1', '0094CE58', '数量(吨)'),
            array('F1:F1', 'F1', '0094CE58', '结存量(吨)'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('储罐汇总明细表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F');

        foreach ($result['list'] as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['customername'];
                        break;
                    case 1:
                        $value = $item['created_at'];
                        break;
                    case 2:
                        $value = $item['doc_sysno_type'];
                        break;
                    case 3:
                        $value = $item['transportationtype'];
                        break;
                    case 4:
                        $value = $item['beqty'];
                        break;
                    case 5:
                        $value = $item['clearingstock'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="储罐汇总明细表.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}
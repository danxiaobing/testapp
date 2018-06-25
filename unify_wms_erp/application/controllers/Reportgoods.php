<?php
/**
 * 货品收发存汇总
 * Author: HR
 * Date: 2016/12/15
 * Time: 13:47
 */
class ReportgoodsController extends Yaf_Controller_Abstract
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

    /*
     * 显示后台页面
     */
    public function indexAction()
    {

        $goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $wharf = new WharfModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $date = date('Y-m-d');
        // var_dump($date);
        $timestamp=strtotime($date);
        $begin_time=date( 'Y-m-d',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-'.date('d',$timestamp) ) );

        $params['goods'] = $goods->getGoodsInfo();
        $params['wharf'] = $wharf->getRecordCount();
        $params['end_time'] = $date;
        $params['begin_time'] = $begin_time;
        $this->getView()->make('reportgoods.list',$params);
    }

    public function listJsonAction()
    {
        $date = date('Y-m-d');
        $timestamp=strtotime($date);
        $begin_time=date( 'Y-m-d',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-'.date('d',$timestamp) ) );

        $request = $this->getRequest();

        $id = $request->getPost('id','');
        $begin_time = $request->getPost('begin_time',$begin_time);
        $end_time = $request->getPost('end_time',$date);
        $goodsnature = $request->getPost('goodsnature','');
        $wharfname = $request->getPost('wharfname','');
        $search = array(
                'id'=>$id,
                'begin_time'=>$begin_time,
                'end_time'=>$end_time,
                'goodsnature'=>$goodsnature,
                'wharfname'=>$wharfname,
                'cartype'=>$request->getPost('cartype',''),
                'pageCurrent' => COMMON::P(),
                'pageSize' => COMMON::PR()

            );

        $R = new ReportgoodsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $R->getlist3($search);
        echo json_encode($params);

    }

    /*
        显示货品收发存明细页
     */
    public function detailAction()
    {

        $date = date('Y-m-d');
        $timestamp=strtotime($date);
        $begin_time=date( 'Y-m-d',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-'.date('d',$timestamp) ) );

        // $id = '';
        $request = $this->getRequest();
        $id=$request->getPost('goods_sysno',0);

        $begin_time=$request->getPost('begin_time',$begin_time);
        $end_time=$request->getPost('end_time',$date);
        $params['id'] = $id;
        $params['begin_time'] = $begin_time;
        $params['end_time'] = $end_time;


        $params['ghoststockqty'] = $request->getPost('ghoststockqty','');;
        $params['lastqty'] = $request->getPost('lastqty','');;


        $R = new ReportgoodsModel(Yaf_Registry::get("db") , Yaf_Registry::get("mc") );

        $params['goods'] = $R->getGoodsInfo();

        $this->getView()->make('reportgoods.detail',$params);
    }


    public function detailJsonAction()
    {

        $date = date('Y-m-d');
        $timestamp=strtotime($date);
        $start_time=date( 'Y-m-d',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-'.date('d',$timestamp) ) );
        $request = $this->getRequest();
        $id = $request->getPost('goods_sysno','');
        $begin_time = $request->getPost('Begin_time','');
        $end_time = $request->getPost('End_time','');
        if(!$id){
            $id = $request->getParam('id','');
        }
        if(!$begin_time){
            $begin_time = $request->getParam('begin_time',$start_time);
        }
        // var_dump($request->getPost('end_time',''));
        if(!$end_time){
            $end_time = $request->getParam('end_time',$date);
        }    
        
        
  
        $search = array(
                'id'=>$id,
                'begin_time' => $begin_time,
                'end_time'=>$end_time,
                'pageCurrent' => COMMON::P(),
                'pageSize' => COMMON::PR()
            );
        // var_dump($end_time);exit();
        $R = new ReportgoodsModel(Yaf_Registry::get("db") , Yaf_Registry::get("mc") );
        $params = $R->getDetail2($search);

        echo json_encode($params);
    }

    public function ajaxgetqtyAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id','');
        $begin_time = $request->getPost('begin_time','');
        $end_time = $request->getPost('end_time','');
        $search = array(
                'id'=>$id,
                'begin_time'=>$begin_time,
                'end_time'=>$end_time,
                'page' => false,
            );        

        $R = new ReportgoodsModel(Yaf_Registry::get("db") , Yaf_Registry::get("mc") );
        $params = $R->getlist3($search);

        $lastqty = $params[0]['lastqty'];
        $ghoststockqty = $params[0]['ghoststockqty'];

        $arr = array(
            'lastqty' => $lastqty,
            'ghoststockqty' => $ghoststockqty
            );
        echo json_encode($arr);
    }

    public function ExcellistAction()
    {
   $request = $this->getRequest();

        $date = date('Y-m-d');
        $timestamp=strtotime($date);
        $begin_time=date( 'Y-m-d',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-'.date('d',$timestamp) ) );

        $request = $this->getRequest();

        $id = $request->getPost('id','');
        $begin_time = $request->getPost('begin_time',$begin_time);
        $end_time = $request->getPost('end_time',$date);
  
        $search = array(
                'id'=>$id,
                'begin_time'=>$begin_time,
                'end_time'=>$end_time,
                'page' =>false,

            );

        $R = new ReportgoodsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $R->getlist3($search);
        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("国烨云仓")
            ->setTitle("货品收发存汇总表")
            ->setSubject("列表")
            ->setDescription("货品收发存汇总表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '入库单号'),
            array('B1:B1', 'B1', '0094CE58', '客户名称'),
            array('C1:C1', 'C1', '0094CE58', '品名'),
            array('D1:D1', 'D1', '0094CE58', '进货日期'),
            array('E1:E1', 'E1', '005E9CD3', '进货船名/槽车进货'),
            array('F1:F1', 'F1', '0094CE58', '提单量'),
            array('G1:G1', 'G1', '005E9CD3', '商检量'),
            array('H1:H1', 'H1', '0094CE58', '期初数量'),
            array('I1:I1', 'I1', '0094CE58', '出库数量'),
            array('J1:J1', 'J1', '0094CE58', '货权转移数量'),
            array('K1:K1', 'K1', '0094CE58', '卸船码头'),
            array('L1:L1', 'L1', '0094CE58', '内外贸'),
            array('M1:M1', 'M1', '0094CE58', '损耗数量'),
            array('N1:N1', 'N1', '0094CE58', '期末余量'),
            array('O1:O1', 'O1', '0094CE58', '槽车车数'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('货品收发存汇总表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O');

        $goodsnature = array();
        $goodsnature['1'] = '保税';
        $goodsnature['2'] = '外贸';
        $goodsnature['3'] = '内贸转出口';
        $goodsnature['4'] = '内贸内销';

        foreach ($params as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                            $value = $item['stockinno'];
                        break;
                    case 1:
                            $value = $item['customername'];
                        break;
                    case 2:
                            $value = $item['goodsname'];
                        break;
                    case 3:
                            $value = $item['stockindate'];
                        break;
                    case 4:
                            $value = $item['shipname'];
                        break;
                    case 5:
                            $value = $item['takegoodsnum'];
                        break;
                    case 6:
                            $value = $item['bussinesscheckqty'];
                        break;
                    case 7:
                            $value = $item['ghoststockqty'];
                        break;
                    case 8:
                            $value = $item['outstockqty'];
                        break;
                    case 9:
                            $value = $item['transqty'];
                        break;
                    case 10:
                            $value = $item['wharfname'];
                        break;
                    case 11:
                            $value = $goodsnature[$item['goodsnature']];
                        break;
                    case 12:
                            $value = $item['lossqty'];
                        break;
                    case 13:
                            $value = $item['lastqty'];
                        break;
                    case 14:
                            $value = $item['wagon'];
                        break;

                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="货品收发存汇总表.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');        
    }


    //货品收发存明细导出
    public function ExceldetailAction()
    {
        $date = date('Y-m-d');
        $timestamp=strtotime($date);
        $start_time=date( 'Y-m-d',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-'.date('d',$timestamp) ) );
        $request = $this->getRequest();
        $id = $request->getPost('goods_sysno','');
        $begin_time = $request->getPost('Begin_time','');
        $end_time = $request->getPost('End_time','');
        if(!$id){
            $id = $request->getParam('id','');
        }
        if(!$begin_time){
            $begin_time = $request->getParam('begin_time',$start_time);
        }
        if(!$end_time){
            $end_time = $request->getParam('end_time',$date);
        }   
  
        $search = array(
                'id'=>$id,
                'begin_time'=>$begin_time,
                'end_time'=>$end_time,
                'page' =>false,

            );

        $R = new ReportgoodsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $R->getDetail2($search);
        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("国烨云仓")
            ->setTitle("货品收发存明细表")
            ->setSubject("列表")
            ->setDescription("货品收发存明细表");

        $mainTitle = array(
            array('A1:A1', 'A1', '005E9CD3', '单据日期'),
            array('B1:B1', 'B1', '005E9CD3', '单据编号'),
            array('C1:C1', 'C1', '0094CE58', '单据类型'),
            array('D1:D1', 'D1', '0094CE58', '槽车/船名'),
            array('E1:E1', 'E1', '0094CE58', '商检量'),
            array('F1:F1', 'F1', '0094CE58', '出货量'),
            array('G1:G1', 'G1', '0094CE58', '货品转出量'),
            array('H1:H1', 'H1', '0094CE58', '结存量'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('货品收发存明细表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G','H');

        $type = array();
        $type['1'] = '入库单';
        $type['2'] = '出库单';
        $type['3'] = '货权转移单';

        foreach ($params as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                            $value = $item['date'];
                        break;
                    case 1:
                        $value = $item['sno'];
                        break;
                    case 2:
                        $value = $type[$item['type']];
                        break;
                    case 3:
                        $value = $item['shipname'];
                        break;
                    case 4:
                        $value = $item['bussinesscheckqty'];
                        break;
                    case 5:
                        $value = $item['beqty'];
                        break;
                    case 6:
                        $value = $item['num'];
                        break;
                    case 7:
                        $value = $item['stockqty'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="货品收发存明细表.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');        
    }

    public function testAction()
    {
        $search = array(
                'num'=> 100,
                'goods_sysno' => 84,
                'customer_sysno' => 14,
                'contract_sysno' => 18

            );

        $R = new StockModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $R->controlgoods($search);
        echo "<pre>";
        var_dump($params);       
    }
}
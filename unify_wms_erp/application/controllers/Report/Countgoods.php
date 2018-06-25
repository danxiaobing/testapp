<?php
/**
 * 货物系列报表
 * @date   2017-05-10
 * @author HR <[<email address>]>
 */
class Report_CountgoodsController extends Yaf_Controller_Abstract
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
     * 显示前台页面货物统计表
     */
    public function CountgoodsAction()
    {
    	return $this->getView()->make('goodsseries.countgoods',[]);
    }

    /**
     * 获取货物统计表数据
     */
    public function CountgoodsJsonAction()
    {
    	$request = $this->getRequest();
    	$search = array(
    		'year'	=> $request->getPost('year',date('Y')),
    		'type'	=> $request->getPost('type',1),
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),            
    		); 
    	$C = new Report_CountgoodsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
    	$params = $C->getCountgoodsList($search);
    	echo json_encode($params);
    }

    /*
     * 入库单excel
     */
    public function ExcellistAction(){
        $request = $this->getRequest();
        $params = array(
            'year'  => $request->getPost('year',date('Y')),
            'type'  => $request->getPost('type',1),
            'page'  => false,
        );

        $C = new Report_CountgoodsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $result = $C->getCountgoodsList($params);
        // var_dump($result);exit;
        ob_end_clean();//清除缓冲区,避免乱码

        $arr = array(
            1=> '入库统计',
            2=> '出库统计',
            3=> '损耗统计',
            4=> '存货统计',
            );

        $objPHPExcel = new PHPExcel();
        $title = $params['year'].'年货品统计表('.$arr[$params['type']].')';

        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle($title)
            ->setSubject("列表")
            ->setDescription($title);

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '品名'),
            array('B1:B1', 'B1', '005E9CD3', '一月份'),
            array('C1:C1', 'C1', '005E9CD3', '二月份'),
            array('D1:D1', 'D1', '0094CE58', '三月份'),
            array('E1:E1', 'E1', '0094CE58', '四月份'),
            array('F1:F1', 'F1', '0094CE58', '五月份'),
            array('G1:G1', 'G1', '0094CE58', '六月份'),
            array('H1:H1', 'H1', '003376B3', '七月份'),
            array('I1:I1', 'I1', '003376B3', '八月份'),
            array('J1:J1', 'J1', '003376B3', '九月份'),
            array('K1:K1', 'K1', '003376B3', '十月份'),
            array('L1:L1', 'L1', '003376B3', '十一月份'),
            array('M1:M1', 'M1', '003376B3', '十二月份'),
            array('N1:N1', 'N1', '003376B3', '总量'),
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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I','J','K','L','M','N');

        foreach ($result as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['goodsname'];
                        break;
                    case 1:
                        $value = $item['1'];
                        break;
                    case 2:
                        $value = $item['2'];
                        break;
                    case 3:
                        $value = $item['3'];
                        break;
                    case 4:
                        $value = $item['4'];
                        break;
                    case 5:
                        $value = $item['5'];
                        break;
                    case 6:
                        $value = $item['6'];
                        break;
                    case 7:
                        $value = $item['7'];
                        break;
                    case 8:
                        $value = $item['8'];
                        break;
                    case 9:
                        $value = $item['9'];
                        break;
                    case 10:
                        $value = $item['10'];
                        break;
                    case 11:
                        $value = $item['11'];
                        break;
                    case 12:
                        $value = $item['12'];
                        break;
                    case 13:
                        $value = $item['countnum'];
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


    //货物进出报表begin
    
    public function GoodsinoutAction()
    {

        $date = date('Y-m');

        $params['begin_time'] = $date;

        return $this->getView()->make('goodsseries.goodsinout',$params);
    }

    //获取货物进出报表数据
    public function GoodsinoutJsonAction()
    {
        $request = $this->getRequest();

        $date = date('Y-m');


        $search = array(
            'begin_time' => $request->getPost('begin_time',$date),
            'customername' => $request->getPost('customername',''),
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
            );

        $C = new Report_CountgoodsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $C->getGoodsoutin2($search);

        
         echo json_encode($params);

    }

    public function testAction()
    {
        $request = $this->getRequest();
        $search = array(
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
            ); 
        $C = new Report_CountgoodsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $C->getGoodsoutin2($search);
        echo '<pre>';
        print_r($params);
    }



    /*
     * 入库单excel
     */
    public function ExcelinoutlistAction(){
        $request = $this->getRequest();

        $date = date('Y-m');


        $params = array(
            'begin_time' => $request->getPost('begin_time',''),
            'end_time' => $request->getPost('end_time',''),
            'customername' => $request->getPost('customername',''),
            'page'  => false,
        );

        $C = new Report_CountgoodsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $result = $C->getGoodsoutin2($params);
        // var_dump($result);exit;
        ob_end_clean();//清除缓冲区,避免乱码



        $objPHPExcel = new PHPExcel();
        $title = '货品进出报表';

        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle($title)
            ->setSubject("列表")
            ->setDescription($title);

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '罐号'),
            array('B1:B1', 'B1', '005E9CD3', '品名'),
            array('C1:C1', 'C1', '005E9CD3', '客户'),
            array('D1:D1', 'D1', '0094CE58', '上期结存'),
            array('E1:E1', 'E1', '0094CE58', '本期进货'),
            array('F1:F1', 'F1', '0094CE58', '本期出库'),
            array('G1:G1', 'G1', '0094CE58', '本期货转入'),
            array('H1:H1', 'H1', '003376B3', '本期货转出'),
            array('I1:I1', 'I1', '003376B3', '客户结存量'),
            array('J1:J1', 'J1', '003376B3', '储罐结存量'),
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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I','J');

        foreach ($result as $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['storagetankname'];
                        break;
                    case 1:
                        $value = $item['goodsname'];
                        break;
                    case 2:
                        $value = $item['customername'];
                        break;
                    case 3:
                        $value = $item['lastqty'];
                        break;
                    case 4:
                        $value = $item['instock'];
                        break;
                    case 5:
                        $value = $item['outstock'];
                        break;
                    case 6:
                        $value = $item['traninstock'];
                        break;
                    case 7:
                        $value = $item['tranoutstock'];
                        break;
                    case 8:
                        $value = $item['customer_beqty'];
                        break;
                    case 9:
                        $value = $item['storagetank_qty'];
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


    public function see_inoutdetailAction()
    {
        $request = $this->getRequest();
        $customer_sysno = $request->getPost('customer_sysno','');
        $storagetank_sysno = $request->getPost('storagetank_sysno','');
        $goods_sysno = $request->getPost('goods_sysno','');
        $time = $request->getPost('time','');

        $params['customer_sysno'] = $customer_sysno;
        $params['storagetank_sysno'] = $storagetank_sysno;
        $params['goods_sysno'] = $goods_sysno;
        $params['time'] = $time;
        return $this->getView()->make('goodsseries.detail',$params);
    }

    public function inoutdetailJsonAction()
    {
        $request = $this->getRequest();
        $customer_sysno = $request->getParam('customer_sysno','');
        $storagetank_sysno = $request->getParam('storagetank_sysno','');
        $goods_sysno = $request->getParam('goods_sysno','');
        $time = $request->getParam('time','');

        $search = array(
            'customer_sysno' => $customer_sysno,
            'storagetank_sysno' => $storagetank_sysno,
            'goods_sysno' => $goods_sysno,
            'time' => $time,
            'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),            
            );
        
        $C = new Report_CountgoodsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $result = $C->getGoodsoutindetail($search);
        echo  json_encode($result);
    }
}
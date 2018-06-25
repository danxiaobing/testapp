<?php

/**
 * Created by PhpStorm.
 * User: hanshutan
 * Date: 2016/11/22 0022
 * Time: 9:52
 */
class CapacityController extends Yaf_Controller_Abstract
{
    public function init()
    {
        # parent::init();
    }

    public function listAction()
    {
        $request = $this->getRequest();
        $goods = new GoodsModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $tank = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $area = new AreaModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));

        $search = array(
            'bar_status' => 1,
            'bar_isdel' => 0,
            'page' => false
        );
        $tankdata = $tank->searchStoragetank($search);
        $params['tanklist'] = $tankdata['list'];

        $areadata = $area->searchArea($search);
        $params['arealist'] = $areadata['list'];

        $goods = $goods->getBaseGoods($search);
        $params['goodslist'] = $goods['list'];

        $this->getView()->make('capacity.list', $params);
    }

    /**
     * Title:查询列表
     */

    public function datailAction()
    {
        $request = $this->getRequest();
        $pageSize = $request->getPost('pageSize', '14');
        $pageCurrent = $request->getPost('pageCurrent', '1');
        $search = array(
            'storagetank_sysno' => $request->getPost('storagetank_sysno', ''),
            'area_sysno' => $request->getPost('area_sysno', ''),
            'goods_sysno' => $request->getPost('goods_sysno', ''),
            'customername' => $request->getPost('customername', ''),
            'pageCurrent' => $pageCurrent,
            'pageSize' => $pageSize
        );
        $S = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $capacity = new CapacityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $data = $capacity->searchCapacity($search);
        if ($data['totalRow'] == 0) {
            COMMON::result('300', '无数据！');
            return;
        }
        #是否包罐 1 $stockResult 返回数组  2 $stockgoodsRes 返回查询记录
        foreach ($data['list'] as &$item) {
            //判断是否包罐
            $stockResult = $S->isbgById($item['sysno']);
            $item['storagetankbg'] = $stockResult['istank_bg'];
            $item['contractdate'] = $stockResult['contractdate'];
            $item['customer_sysno'] = $stockResult['customer_sysno'];
            $item['customername'] = $stockResult['customername'];
            //取最近3条记录
            $stockgoodsRes = $capacity->getstockgoodsbytankid($item['sysno']);
            $item['onegoodslog'] = '';
            $item['twogoodslog'] = '';
            $item['threegoodslog'] = '';
            if (!empty($stockgoodsRes)) {
                foreach ($stockgoodsRes as $k => $val) {
                    if ($k == 0) {
                        $item['onegoodslog'] = $val['goodslog'];
                    } elseif ($k == 1) {
                        $item['twogoodslog'] = $val['goodslog'];
                    } elseif ($k == 2) {
                        $item['threegoodslog'] = $val['goodslog'];
                    }
                }
            }
        }
        echo json_encode($data);
    }

    public function dbtoexcelAction()
    {
        $request = $this->getRequest();
        $search = array(
            'storagetank_sysno' => $request->getPost('storagetank_sysno', ''),
            'area_sysno' => $request->getPost('area_sysno', ''),
            'goods_sysno' => $request->getPost('goods_sysno', ''),
            'customername' => $request->getPost('customername', ''),
            'page' => false,
        );
        $S = new StoragetankModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $capacity = new CapacityModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));

        $params = $capacity->searchCapacity($search);
        #是否包罐 1 $stockResult 返回数组  2 $stockgoodsRes 返回查询记录
        foreach ($params['list'] as &$item) {
            //判断是否包罐
            $stockResult = $S->isbgById($item['sysno']);
            $item['storagetankbg'] = $stockResult['istank_bg'];
            $item['contractdate'] = $stockResult['contractdate'];
            $item['customer_sysno'] = $stockResult['customer_sysno'];
            $item['customername'] = $stockResult['customername'];
            //取最近3条记录
            $stockgoodsRes = $capacity->getstockgoodsbytankid($item['sysno']);
            $item['onegoodslog'] = '';
            $item['twogoodslog'] = '';
            $item['threegoodslog'] = '';
            if (!empty($stockgoodsRes)) {
                foreach ($stockgoodsRes as $k => $val) {
                    if ($k == 0) {
                        $item['onegoodslog'] = $val['goodslog'];
                    } elseif ($k == 1) {
                        $item['twogoodslog'] = $val['goodslog'];
                    } elseif ($k == 2) {
                        $item['threegoodslog'] = $val['goodslog'];
                    }
                }
            }
        }
        /*------------------查询筛选条件返回参数-----------------*/
        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("国烨云仓")
            ->setTitle("可用罐容列表")
            ->setSubject("可用罐容列表")
            ->setDescription("可用罐容列表");

        $mainTitle = array(
            array('A1:A1', 'A1', '0094CE58', '罐号'),
            array('B1:B1', 'B1', '005E9CD3', '储罐材质'),
            array('C1:C1', 'C1', '005E9CD3', '储罐性质'),
            array('D1:D1', 'D1', '0094CE58', '合同到期日'),
            array('E1:E1', 'E1', '0094CE58', '是否包罐'),
            array('F1:F1', 'F1', '0094CE58', '客户'),
            array('G1:G1', 'G1', '0094CE58', '品种'),
            array('H1:H1', 'H1', '003376B3', '上载1品种'),
            array('I1:I1', 'I1', '003376B3', '上载2品种'),
            array('J1:J1', 'J1', '003376B3', '上载3品种'),
            array('K1:K1', 'K1', '003376B3', '可存放吨数'),
            array('L1:L1', 'L1', '003376B3', '计量单位'),
            array('M1:M1', 'M1', '0094CE58', '待入量'),
            array('N1:N1', 'N1', '003376B3', '待出量'),
            array('O1:O1', 'O1', '003376B3', '现存量'),
            array('P1:P1', 'P1', '0094CE58', '片区'),
        );

        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('可用罐容列表');

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
        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P');

        foreach ($params['list'] as $item) {

            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['storagetankname'];
                        break;
                    case 1:
                        $value = $item['storagetank_categoryname'];
                        break;
                    case 2:
                        if ($item['storagetanknature'] == 1) {
                            $value = "内贸罐";
                        } elseif ($item['storagetanknature'] == 2) {
                            $value = "外贸罐";
                        } else {
                            $value = "保税罐";
                        }
                        break;
                    case 3:
                        $value = $item['contractdate'];
                        break;
                    case 4:
                        if ($item['storagetankbg'] == 1) {
                            $value = "是";
                        } else {
                            $value = "否";
                        }
                        break;
                    case 5:
                        $value = $item['customername'];
                        break;
                    case 6:
                        $value = $item['goodsname'];
                        break;
                    case 7:
                        $value = $item['onegoodslog'];
                        break;
                    case 8:
                        $value = $item['twogoodslog'];
                        break;
                    case 9:
                        $value = $item['threegoodslog'];
                        break;
                    case 10:
                        $value = $item['actualcapacity'];
                        break;
                    case 11:
                        $value = $item['unitname'];
                        break;
                    case 12:
                        $value = $item['orderinqty'];
                        break;
                    case 13:
                        $value = $item['orderoutqty'];
                        break;
                    case 14:
                        $value = $item['tank_stockqty'];
                        break;
                    case 15:
                        $value = $item['areaname'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }


// Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="可用罐容列表.xlsx"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

    }


}
<?php
/**
 * 货物系列报表
 * @date   2017-05-10
 * @author HR <[<email address>]>
 */
class Report_StockoutbucketController extends Yaf_Controller_Abstract
{
    /**
     * request 对象
     * @var
     */
    public $request;

    /**
     * IndexController::init()
     *
     * @return void
     */
    public function init()
    {
        # parent::init();
        $this -> request = $this -> getRequest();
    }

    /**
     * 显示前台页面货物统计表
     */
    public function listAction()
    {
        $bucketInstance = new Report_BucketModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params['list'] = $bucketInstance -> getAllGoods();
    	return $this->getView()->make('bucket.list',$params);
    }

    /**
     * 获取货物统计表数据
     */
    public function listJsonAction()
    {
    	$search = array(
                'select_data' => $this->request->getPost('select_data', date('Y-m')),
                'pageCurrent' => COMMON:: P(),
                'pageSize' => COMMON:: PR(),
    		);
    	$bucketInstance = new Report_BucketModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
    	$params = $bucketInstance->search($search);
    	echo json_encode($params);
    }

    /**
     * 槽车统计VIEW
     * @return
     */
    public function  tankAction(){
        $bucketInstance = new Report_BucketModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params['list'] = $bucketInstance -> getAllGoods();
        return $this->getView()->make('bucket.tank',$params);
    }

    /**
     * 获取槽车统计表数据
     */
    public function tankJsonAction()
    {
        $search = array(
            'select_data' => $this->request->getPost('select_data', date('Y-m')),
            'pageCurrent' => COMMON:: P(),
            'pageSize' => COMMON:: PR(),
        );
        $bucketInstance = new Report_BucketModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $params = $bucketInstance->tankSearch($search);
        echo json_encode($params);
    }

    /**
     * 桶车发货统计Excel 导出
     */
    public function listToExcelAction(){
        $bucketInstance = new Report_BucketModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $allGoods = $bucketInstance -> getAllGoods();
        $search = array(
            'select_data' => $this->request->getPost('select_data', date('Y-m')),
            'page' => false,
        );
        $result = $bucketInstance->search($search);

        ob_end_clean();//清除缓冲区,避免乱码
        $objPHPExcel = new PHPExcel();
        $excelName = date('Y年m月',strtotime($search['select_data'])).'发货桶车统计表';
        $allSubtitle = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
            'O', 'P', 'Q', 'R', 'L', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN',
            'AO', 'AP', 'AQ', 'AR', 'AL', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
        );

        $titleArray = [
             '日期', '磅单数', '发货吨位', '结存', '堆桶场地发货吨位', '堆桶场地结存', '罐桶总数',
        ];
        $goodTitleArr = [];
        foreach($allGoods as $val){
            $goodTitleArr['name'][] = $val['goodsname'];
            $goodTitleArr['sysno'][] = $val['sysno'];
        }
        $allTitle = array_merge($titleArray, $goodTitleArr['name']);
        //Excel总列数
        $subtitle = array_slice($allSubtitle , 0, count($allTitle));
        //商品标题
        $goodSubTitle = array_slice($allSubtitle , count($titleArray), count($goodTitleArr['sysno']));
        //商品ID相对应的EXCEL行数
        $goodsTitleSysno = [];
        foreach($goodTitleArr['sysno'] as $key => $value)
        {
            $goodsTitleSysno[$goodSubTitle[$key]] = $value;
        }
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle($excelName);
        foreach ($allTitle as $key => $row) {
            $objActSheet->setCellValue($subtitle[$key].'1', $row);
            $objStyle = $objActSheet->getStyle($subtitle[$key].'1');
            $objStyle->getAlignment()->setHorizontal("center");
            $objStyle->getAlignment()->setVertical("center");
            $objStyle->getAlignment()->setWrapText(true);
            $objStyle->getFont()->setBold(true);
        }
        $line = 1;
        foreach ($result['list'] as  $key => $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['day'];
                        break;
                    case 1:
                        $value = $item['count'];
                        break;
                    case 2:
                        $value = $item['count_out'];
                        break;
                    case 3:
                        $value = $item['beqty'];
                        break;
                    case 4:
                        $value = $item['bucket_out'];
                        break;
                    case 5:
                        $value = $item['bucket_qty'];
                        break;
                    case 6:
                        $value = $item['bucketnumber'];
                        break;
                    case $i:
                        $value = isset($item[$goodsTitleSysno[$subtitle[$i]]]) ? $item[$goodsTitleSysno[$subtitle[$i]]] : $value;
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$excelName.'.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    /**
     * 槽车发货统计Excel 导出
     */
    public function tankToExcelAction()
    {
        $bucketInstance = new Report_BucketModel(Yaf_Registry::get("db"), Yaf_Registry::get('mc'));
        $allGoods = $bucketInstance -> getAllGoods();
        $search = array(
            'select_data' => $this->request->getPost('select_data', date('Y-m')),
            'page' => false,

        );
        $result = $bucketInstance->tankSearch($search);

        ob_end_clean();//清除缓冲区,避免乱码
        $objPHPExcel = new PHPExcel();
        $excelName = date('Y年m月',strtotime($search['select_data'])).'发货槽车统计表';
        $allSubtitle = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
            'O', 'P', 'Q', 'R', 'L', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN',
            'AO', 'AP', 'AQ', 'AR', 'AL', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
        );

        $titleArray = [
            '日期', '磅单数', '发船数', '槽车数量'
        ];
        $goodTitleArr = [];
        foreach($allGoods as $val){
            $goodTitleArr['name'][] = $val['goodsname'];
            $goodTitleArr['sysno'][] = $val['sysno'];
        }
        $allTitle = array_merge($titleArray, $goodTitleArr['name']);
        //Excel总列数
        $subtitle = array_slice($allSubtitle , 0, count($allTitle));
        //商品标题
        $goodSubTitle = array_slice($allSubtitle , count($titleArray), count($goodTitleArr['sysno']));
        //商品ID相对应的EXCEL行数
        $goodsTitleSysno = [];
        foreach($goodTitleArr['sysno'] as $key => $value)
        {
            $goodsTitleSysno[$goodSubTitle[$key]] = $value;
        }
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle($excelName);
        foreach ($allTitle as $key => $row) {
            $objActSheet->setCellValue($subtitle[$key].'1', $row);
            $objStyle = $objActSheet->getStyle($subtitle[$key].'1');
            $objStyle->getAlignment()->setHorizontal("center");
            $objStyle->getAlignment()->setVertical("center");
            $objStyle->getAlignment()->setWrapText(true);
            $objStyle->getFont()->setBold(true);
        }
        $line = 1;
        foreach ($result['list'] as  $key => $item) {
            $line++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['day'];
                        break;
                    case 1:
                        $value = $item['count'];
                        break;
                    case 2:
                        $value = $item['ship_count'];
                        break;
                    case 3:
                        $value = $item['pound_count'];
                        break;
                    case $i:
                        $value = isset($item[$goodsTitleSysno[$subtitle[$i]]]) ? $item[$goodsTitleSysno[$subtitle[$i]]] : $value;
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$excelName.'.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}
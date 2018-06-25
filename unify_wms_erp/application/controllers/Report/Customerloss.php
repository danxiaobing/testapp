<?php
/**
 * 损耗
 */
class Report_CustomerlossController extends Yaf_Controller_Abstract {
	/**
	 * IndexController::init()
	 *
	 * @return void
	 */
	public function init() {
		# parent::init();
    }

	public function listAction() {
		$C = new CustomerModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

		$search = array(
            'bar_status' => '1',
            'bar_isdel' => '0',
			'page' => false,
		);

		$list = $C->searchCustomer($search);
		$params['customerlist'] =  $list['list'];
		$goods = new GoodsModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
        $params['goodslist'] = $goods->getGoodsInfo();
		$this->getView()->make('customerloss.list',$params);
	}


	public function listJsonAction() {
		$request = $this->getRequest();
		
		$search = array (
			'start_time' => $request->getPost('start_time',''),
            'end_time' => $request->getPost('end_time',''),
			'customer_sysno' => $request->getPost('customer_sysno',''),
			'goods_sysno'=> $request->getPost('goods_sysno',''),
//			'goodsnature' => $request->getPost('goodsnature',''),
			'pageCurrent' => COMMON:: P(),
			'pageSize' => COMMON:: PR(),
		);
		$RC = new Report_CustomerlossModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$list = $RC->search($search);
		echo json_encode($list);
	}
	public function detailListAction()
	{
		$request = $this->getRequest();
        $data =json_decode($request->getPost('data',''),true);
		$start_time = $request->getPost('start_time','');
		$end_time = $request->getPost('end_time','');
        $data['start_time'] = $start_time;
        $data['end_time'] = $end_time;

        $params['data'] = json_encode($data);
		$this->getView()->make('customerloss.detail',$params);
	}

	public function detailJsonAction() {
		$request = $this->getRequest();
		$data = $request->getPost('data','');
		$data['page'] = false;
		$data['bar_date'] = $data['date'];
		if($data){
			$RC = new Report_CustomerlossModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
			$result = $RC->search($data);
			foreach ($result['list'] as $key => $value) {
				$result['list'][$key]['date'] = $data['date'];
			}
                echo json_encode($result['list']);
		}else{
			echo json_encode([]);
		}
		
	}

	public function inAndOutDetailJsonAction() {
		$request = $this->getRequest();
		$data = $request->getPost('data','');
		if($data){
			$RC = new Report_CustomerlossModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
			$list = $RC->getDetailData($data);
			echo json_encode($list);
		}else{
			echo json_encode([]);
		}
		
	}

    public function exceldetailAction(){
        $request = $this->getRequest();
        $data = $request->getPost('data','');
        $data['page'] = false;
        $R = new Report_CustomerlossModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));

        $resultbase = $R->search($data);
        $resulttrans = $R->getDetailData($data);
//        var_dump($resultbase);die();

        ob_end_clean();//清除缓冲区,避免乱码

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("云仓管家")
            ->setTitle("客户损耗表")
            ->setSubject("列表")
            ->setDescription("客户损耗表明细");

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
            array('A1:A1', 'A1', '0094CE58', '客户'),
            array('B1:B1', 'B1', '005E9CD3', '货品'),
            array('C1:C1', 'C1', '005E9CD3', '入库时间'),
            array('D1:D1', 'D1', '0094CE58', '车/船名'),
            array('E1:E1', 'E1', '0094CE58', '商检量'),
            array('F1:F1', 'F1', '0094CE58', '出库量'),
            array('G1:G1', 'G1', '0094CE58', '货转量'),
            array('H1:H1', 'H1', '0094CE58', '结存量'),
            array('I1:I1', 'I1', '0094CE58', '损耗量'),
        );
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setTitle('客户损耗表明细');

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
                    $value = $resultbase['list'][0]['customername'];
                    break;
                case 1:
                    $value = $resultbase['list'][0]['goodsname'];
                    break;
                case 2:
                    $value = $resultbase['list'][0]['created_at'];
                    break;
                case 3:
                    $value = $resultbase['list'][0]['shipname'];
                    break;
                case 4:
                    $value = $resultbase['list'][0]['storagestock'];
                    break;
                case 5:
                    $value = $resultbase['list'][0]['outqty'];
                    break;
                case 6:
                    $value = $resultbase['list'][0]['inqty'];
                    break;
                case 7:
                    $value = $resultbase['list'][0]['ullage'];
                    break;
                case 8:
                    $value = $resultbase['list'][0]['endstock'];
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
        //出库信息
        $line = $line + 2;
        $tab2index = $line;
        $mainTitle3 = array(
            array('A1:A1', 'A'.$line, '0094CE58', '出/入库时间'),
            array('B1:B1', 'B'.$line, '005E9CD3', '船名'),
            array('C1:C1', 'C'.$line, '005E9CD3', '品名'),
            array('D1:D1', 'D'.$line, '0094CE58', '入库量'),
            array('E1:E1', 'E'.$line, '0094CE58', '发货量'),
            array('F1:F1', 'F'.$line, '0094CE58', '货转量'),
            array('G1:G1', 'G'.$line, '0094CE58', '库存量'),
            array('H1:H1', 'H'.$line, '0094CE58', '损耗量'),
            array('I1:I1', 'I'.$line, '0094CE58', '损耗标准'),
        );
        foreach ($mainTitle3 as $row) {
            $objActSheet->setCellValue($row[1], $row[3]);
            $objStyle = $objActSheet->getStyle($row[1]);

            $objStyle->getAlignment()->setHorizontal("center");
            $objStyle->getAlignment()->setVertical("center");
            $objStyle->getAlignment()->setWrapText(true);
            $objStyle->getFont()->setBold(true);
        }

        $subtitle = array('A', 'B', 'C', 'D', 'E', 'F', 'G','H','I');
        foreach ($resulttrans as $item) {
            $line ++;
            for ($i = 0; $i < count($subtitle); $i++) {
                $site = $subtitle[$i] . $line;
                $value = '';
                switch ($i) {
                    case 0:
                        $value = $item['created_at'];
                        break;
                    case 1:
                        $value = $item['shipname'];
                        break;
                    case 2:
                        $value = $item['goodsname'];
                        break;
                    case 3:
                        $value = $item['inqty'];
                        break;
                    case 4:
                        $value = $item['outqty'];
                        break;
                    case 5:
                        $value = $item['tranqty'];
                        break;
                    case 6:
                        $value = $item['stock'];
                        break;
                    case 7:
                        $value = $item['ullage'];
                        break;
                    case 8:
                        $value = $item['percent'];
                        break;
                }
                $objActSheet->setCellValue($site, $value);
            }
        }

        $arr  = array('A', 'B', 'C', 'D', 'E', 'F', 'F', 'G','H','I');
        $count = count($arr);
        for($i=$tab2index;$i<=$line;$i++){
            for($j=0;$j<$count;$j++){
                $objActSheet->getStyle($arr[$j].$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="客户损耗表明细.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}

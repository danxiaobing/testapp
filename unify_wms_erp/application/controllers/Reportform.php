<?php
/**
 * @Author: wu xianneng
 * @Date:   2016-12-17 11:55:36
 * @Last Modified by:   wu xianneng
 * @Last Modified time: 2016-12-17 17:06:33
 */
class ReportformController extends Yaf_Controller_Abstract {
	/**
	 * IndexController::init()
	 *
	 * @return void
	 */
	public function init() {

    }

    /**
	 * 储罐收发存汇总表
	 * @author wu xianneng
	 */
	public function tankListAction(){
		$R = new ReportformModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$params['storagetank']=$R->getStorageTank();

		$this->getView()->make('reportform.tanklist',$params);
	}

	/**
	 * 储罐收发存汇总表Json数据
	 * @author wu xianneng
	 */
	public function tankListJsonAction(){
		$request = $this->getRequest();

		$date = $request->getPost('date1',date('Y-m-d',strtotime('-1 months')));
		$date1 = $request->getPost('date1',date('Y-m-d',strtotime('-1 months')));
		$date2 = $request->getPost('date2',date('Y-m-d'));
		$search = array(
			'date'=>$date,
			'date1'=>date('Y-m-d',strtotime($date1)),//'-1 days',strtotime($date1)
			'date2'=>date('Y-m-d',strtotime($date2)),//'1 days',strtotime($date2)
			'tankno'=>$request->getPost('tankno',''),
			'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
		);
		$search['date2'] = date('Y-m-d H:m:s',strtotime('23 hours 59 minutes 59 seconds',strtotime($date2)));

		$R = new ReportformModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$result=$R->getTankList3($search);
		echo json_encode($result);
	}

	/*
	 * 导出储罐收发汇总excel
	 */
	public function excelAction(){
		$request = $this->getRequest();
		$date2 = $request->getPost('date2',date('Y-m-d'));//
		$params = array(
				'date1'=>$request->getPost('date1',date('Y-m-d',strtotime('-1 months'))),//
				'date2'=>date('Y-m-d',strtotime('1 days',strtotime($date2))),//
				'tankno'=>$request->getPost('tankno',''),
				'page' => false,
		);

		$R = new ReportformModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$result=$R->getTankList3($params);

		ob_end_clean();//清除缓冲区,避免乱码

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("云仓管家")
				->setTitle("储罐收发汇总列表")
				->setSubject("列表")
				->setDescription("储罐收发汇总列表");

		$mainTitle = array(
				array('A1:A1', 'A1', '0094CE58', '储罐号'),
				array('B1:B1', 'B1', '005E9CD3', '产品名称'),
				array('C1:C1', 'C1', '005E9CD3', '计量单位'),
				array('D1:D1', 'D1', '0094CE58', '期初数量'),
				array('E1:E1', 'E1', '0094CE58', '入库数量'),
				array('F1:F1', 'F1', '0094CE58', '倒罐入库数量'),
				array('G1:G1', 'G1', '0094CE58', '出库数量'),
				array('H1:H1', 'H1', '003376B3', '倒罐出库数量'),
				array('I1:I1', 'I1', '003376B3', '盘点'),
				array('J1:J1', 'J1', '003376B3', '期末余额'),
		);
		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->setTitle('储罐收发汇总列表');

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
						$value = $item['storagetankname'];
						break;
					case 1:
						$value = $item['goodsname'];
						break;
					case 2:
						$value = $item['unitname'];
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
						$value = $item['inretank'];
						break;
					case 6:
						if(!$item['totaloutstockqty']){
							$value = 0;
						}else{
							$value = $item['totaloutstockqty'];
						}
						break;
					case 7:
						$value = $item['outretank'];
						break;
					case 8:
						if(!$item['totalcheckqty']){
							$value = 0;
						}else{
							$value = $item['totalcheckqty'];
						}
						break;
					case 9:
						$value = $item['totalstockqty'];
						break;
				}
				$objActSheet->setCellValue($site, $value);
			}
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="储罐收发汇总表.xlsx"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}

	public function tankdetailAction(){
		$request = $this->getRequest();
		$params['tankno'] = $request->getParam('sid','0');

		$params['startqty']= $request->getPost('startqty','0');
		$params['totalstockqty']= $request->getPost('totalstockqty','0');
		$params['date1']= $request->getPost('date1','0');
		$params['date2']= $request->getPost('date2','0');
		$R = new ReportformModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$params['storagetank'] = $R->getStorageTank();

		$this->getView()->make('reportform.tankdetail',$params);
	}

	/**
	 * 储罐收发存明细表Json数据
	 * @author wu xianneng
	 */
	public function tankDetailJsonAction(){
		$request = $this->getRequest();
		$sid=$request->getParam('sid','0');

		$date1 = $request->getParam('date1',strtotime('-1 months'));
		$date2 = $request->getPost('date2',date('Y-m-d'));

		$search = array(
				'date1'=>$request->getPost('date1',$date1),//date('Y-m-d',strtotime('-1 months'))
				'date2'=>$request->getPost('date2',$date2),//date('Y-m-d',strtotime('1 days',strtotime($date2)))
				'tankno'=>$request->getPost('tankno',$sid),
				'pageCurrent' => COMMON::P(),
				'pageSize' => COMMON::PR(),
		);

		$search['date2'] = date('Y-m-d H:m:s',strtotime('23 hours 59 minutes 59 seconds',strtotime($date2)));
		if($search['tankno']==''){
			$search['tankno'] = 0;
		}

		$R = new ReportformModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$params=$R->getTankDetail3($search);

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
		$R = new ReportformModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$params=$R->getStartAndEnd($sid,$date1);
		echo json_encode($params);
	}

	/*
	 * 储罐明细导出excel
	 */
	public function detailexcelAction(){
		$request = $this->getRequest();
		$date2 = $request->getPost('date2',date('Y-m-d'));//
		$params = array(
				'date1'=>$request->getPost('date1',date('Y-m-d',strtotime('-1 months'))),//
				'date2'=>date('Y-m-d',strtotime('1 days',strtotime($date2))),//
				'tankno'=>$request->getPost('tankno',''),
				'page' => false,
		);

		$R = new ReportformModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$result=$R->getTankDetail3($params);

		ob_end_clean();//清除缓冲区,避免乱码

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("云仓管家")
				->setTitle("储罐收发明细表")
				->setSubject("列表")
				->setDescription("储罐收发明细表");

		$mainTitle = array(
				array('A1:A1', 'A1', '0094CE58', '品名'),
				array('B1:B1', 'B1', '005E9CD3', '计量单位'),
				array('C1:C1', 'C1', '005E9CD3', '单据日期'),
				array('D1:D1', 'D1', '0094CE58', '单据类型'),
				array('E1:E1', 'E1', '0094CE58', '单据编号'),
				array('F1:F1', 'F1', '0094CE58', '货物性质'),
				array('G1:G1', 'G1', '0094CE58', '数量'),
		);
		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->setTitle('储罐收发明细表');

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
						$value = $item['goodsname'];
						break;
					case 1:
						$value = $item['unitname'];
						break;
					case 2:
						$value = $item['created_at'];
						break;
					case 3:
						if($item['doctype']==1){
							$value = '车入库';
						}elseif($item['doctype']==2){
							$value = '车入库作废';
						}elseif($item['doctype']==3){
							$value = '车出库';
						}elseif($item['doctype']==4){
							$value = '车出库作废';
						}elseif($item['doctype']==5){
							$value = '船入库';
						}elseif($item['doctype']==6){
							$value = '船入库作废';
						}elseif($item['doctype']==7){
							$value = '船出库';
						}elseif($item['doctype']==8){
							$value = '船出库作废';
						}elseif($item['doctype']==9){
							$value = '盘点';
						}elseif($item['doctype']==11){
							$value = '盘点作废';
						}elseif($item['doctype']==10){
							$value = '倒罐';
						}elseif($item['doctype']==12){
							$value = '倒罐作废';
						}
						break;
					case 4:
						$value = $item['docno'];
						break;
					case 5:
						if($item['goodsnature'] ==1){
							$value = '保税';
						}elseif($item['goodsnature'] ==2){
							$value = '外贸';
						}elseif($item['goodsnature'] ==3){
							$value = '内贸转出口';
						}elseif($item['goodsnature'] ==4){
							$value = '内贸内销';
						}

						break;
					case 6:
						$value = $item['beqty'];
						break;
				}
				$objActSheet->setCellValue($site, $value);
			}
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="储罐收发明细表.xlsx"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}

}
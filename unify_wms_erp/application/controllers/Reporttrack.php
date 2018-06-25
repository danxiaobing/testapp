<?php

class ReporttrackController extends Yaf_Controller_Abstract {
	/**
     * IndexController::init()
     *
     * @return void
     */
    public function init() {
        # parent::init();
         

    }    

	public function listAction(){
		$request = $this->getRequest();
		$params = array(
			'bar_no'=>$request->getpost('stockinno','')
		);

		$this->getView()->make('reporttrack.list',$params);
	}

	public function listJsonAction(){
		$request = $this->getRequest();
		$sid=$request->getParam('sid','0');

		$result=array();
		$params = array(
			'bar_no' => $request->getPost('bar_no',''),
            'bar_doctype' => $request->getPost('bar_doctype','-100'),
            'bar_status' => $request->getPost('bar_status','-100'),
            'bar_startdate' => $request->getPost('bar_startdate',''),
            'bar_enddate' => $request->getPost('bar_enddate',''),
            'bar_name' => $request->getPost('bar_name', ''),
			'pageCurrent' => COMMON::P(),
            'pageSize' => COMMON::PR(),
		);

		$R = new ReporttrackModel(Yaf_Registry::get("db"),Yaf_Registry::get('mc'));
		$result=$R->getStocknoList($params);
		echo json_encode($result);
	}

}
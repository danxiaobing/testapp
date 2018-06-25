<?php
class ImportController extends Yaf_Controller_Abstract {
	/**
	 * IndexController::init()
	 *
	 * @return void
	 */
	public function init() {
		# parent::init();
        $user  = Yaf_Registry::get(SSN_VAR);
    }

	/**
	 *
	 * @return string
	 */
	public function indexAction() {
		$params = array();
		$this->getView()->make('import.index',$params);
	}


	public function importAction(){
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $path = APPLICATION_PATH.'/public/upload/importexcel/';
            $hz = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            if(!in_array($hz, ['xlsx', 'xlsm', 'xltx', 'xltm', 'xls', 'xlt'])){
                COMMON::ApiJson(300, '请上传Excel文件');
                return;
            }
            if(COMMON::makeDir($path)){
                $filename = time().".".$hz;//更改文件名
                $dest = $path.'/'.$filename;
                if(move_uploaded_file($_FILES['file']['tmp_name'], $dest)){
                    $S = new SystemModel(Yaf_Registry:: get("db"), Yaf_Registry:: get('mc'));
                    $result_in = COMMON::importExcel($dest, 1, [6]);
                    if(!empty($result_in))
                    {
                        $res_in = $S->InitData($result_in,1);
                    }
                    
                    $result_trans = COMMON::importExcel($dest, 2, [6,9,11]);
                    if(!empty($result_trans))
                    {
                        $res_trans = $S->InitData($result_trans,2);
                    }
                    
                    $result_intr = COMMON::importExcel($dest, 3, [11,12,20]);
                    if(!empty($result_intr))
                    {
                        $res_intr = $S->InitData($result_intr,3);
                    }
                    
                    $result_contract = COMMON::importExcel($dest, 4, [2,3,4]);
                    if(!empty($result_contract))
                    {
                        $res_contract = $S->InitData($result_contract,4);
                    }
                    
                    COMMON::ApiJson(200, '上传成功', []);
                    return ;
                }else{
                    COMMON::ApiJson(300,'目录存放失败');
                    return;
                }
            }else{
                COMMON::ApiJson(300,'目录创建失败');
                return;
            }
        } else {
            COMMON::ApiJson(300,'上传失败');
            return;
        }

    }
}

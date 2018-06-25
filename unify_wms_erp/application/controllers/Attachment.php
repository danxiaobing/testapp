<?php

class AttachmentController extends Yaf_Controller_Abstract {
	/**
	 * IndexController::init()
	 *
	 * @return void
	 */
	public function init() {
		# parent::init();
    }


	function  uploadJsonAction(){
		$request = $this->getRequest();

		$input = array(
			'name'       =>  $request->getPost('name',''),
			'path'=>  $request->getPost('path',''),
			'type'      =>  $request->getPost('type',''),
			'size'   =>  $request->getPost('size',''),
			'module'   =>  $request->getPost('module',''),
			'doc_sysno' =>  $request->getPost('doc_sysno','0'),
			'action'   =>  $request->getPost('action',''),
			'status'             =>  $request->getPost('status','1'),
			'isdel'              =>  $request->getPost('isdel','0'),
			'created_at'		=>'=NOW()',
			'updated_at'		=>'=NOW()'
		);


		if (is_uploaded_file($_FILES['file']['tmp_name'])) {
			$path = APPLICATION_PATH."/attachment/".$input['module'].'/'.$input['action'];
			
			if(COMMON::makeDir($path)){
				$input['name'] = time().$input['name'];//更改文件名
				$dest = $path.'/'.$input['name'];
				if(move_uploaded_file($_FILES['file']['tmp_name'],$dest)){
					$A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

					$input['path'] = $path;
					if($id = $A->addAttachment($input)){
						$row['filename'] = $id;
						COMMON::result(200,'上传成功',$row);
						return;
					}else{
						COMMON::result(300,'插入数据库失败');
						return;
					}
				}else{
					COMMON::result(300,'目录存放失败');
					return;
				}
			}else{
				COMMON::result(300,'目录创建失败');
				return;
			}
		} else {
			COMMON::result(300,'上传失败');
			return;
		}



	}

	public function delJsonAction(){
		$request = $this->getRequest();

		$id = $request->getPost('sysno',0);

        $type = $request->getParam('type');
		if ($type == 1){
            COMMON::result(300,'不能删除');
            return;
        }

		$A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));

		if($A->delAttach($id)){
			COMMON::result(200,'删除成功');
		}else{
			COMMON::result(300,'删除失败');
		}

		return;
	}

	/**
	 * 显示整个后台页面框架及菜单
	 *
	 * @return string
	 */
	public function viewAction() {
		$request = $this->getRequest();

		$module = $request->getParam('module','');
		$action = $request->getParam('action','');
		$sysno = $request->getParam('sysno','0');

		$attach = array();
		/*switch($action) {
			case 'ship':
				$S = new SupplierModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
				$attach = $S->getShipAttachById($sysno);
				break;
		}*/
		$A = new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$attach = $A->getAttachByMAS($module,$action,$sysno);

		$params['attach'] = $attach;

		$this->getView()->make('attachment.view',$params);

	}

	public function downloadAction() {
		$request = $this->getRequest();

		$id = $request->getParam('id','0');

		$A= new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$attach  = $A->getAttachmentById($id);

		$fullpath = $attach['path'].'/'.$attach['name'];

		if(file_exists($fullpath)){
			if(strpos($attach['type'] ,'image') != false)
				$size = getimagesize($fullpath); //获取mime信息
			else
				$size = filesize($fullpath); //获取mime信息

			$fp=fopen($fullpath, "rb"); //二进制方式打开文件
			if ($size && $fp) {
				header("Content-type: {$size['mime']}");
				Header( "Accept-Ranges:  bytes ");
				Header( "Content-Disposition:  attachment;  filename= ".$attach['name']);
				fpassthru($fp); // 输出至浏览器
				exit;
			}
		}else{
			echo $fullpath;
			exit;
		}
	}

	/**
	 * 显示整个后台页面框架及菜单
	 *
	 * @return string
	 */
	public function previewAction() {
		$request = $this->getRequest();

		$id = $request->getParam('id','0');

		$A= new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$attach  = $A->getAttachmentById($id);

		$fullpath = $attach['path'].'/'.$attach['name'];

		if(file_exists($fullpath)){
			if(strpos($attach['type'] ,'image') !== false)
				$size = getimagesize($fullpath); //获取mime信息
			else{

				$fullpath = APPLICATION_PATH.'/public/static/images/noimg.jpg';
				
				$size = getimagesize($fullpath); //获取mime信息
				$size['mime'] = 'image/jpeg';
			}
				

			$fp=fopen($fullpath, "rb"); //二进制方式打开文件
			if ($size && $fp) {
				header("Content-type: {$size['mime']}");
				fpassthru($fp); // 输出至浏览器
				exit;
			}
		}else{
			echo $fullpath;
			exit;
		}
	}

	public function viewAllAction() {
		$request = $this->getRequest();

		$id = $request->getParam('id','0');

		$A= new AttachmentModel(Yaf_Registry :: get("db"), Yaf_Registry :: get('mc'));
		$attach  = $A->getAttachmentById($id);

		$fullpath = $attach['path'].'/'.$attach['name'];

		if(file_exists($fullpath)){
			$size = getimagesize($fullpath); //获取mime信息
			$fp=fopen($fullpath, "rb"); //二进制方式打开文件
			if ($size && $fp) {
				header("Content-type: {$size['mime']}");
				fpassthru($fp); // 输出至浏览器
				exit;
			}
		}else{
			echo $fullpath;
			exit;
		}
	}


}

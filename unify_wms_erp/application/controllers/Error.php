<?php
/**
 * 统一的异常抛出接口
 *
 * @version $Id$
 * @access  public
 */
class ErrorController extends yaf_controller_abstract
{
	public  $request;
	public function init(){
		$this->request = $this->getRequest();
	}
	/**
	 * 异常处理函数
	 *
	 * @param  object  $exception
	 * @return string  html
	 */
	public function ErrorAction($exception)
    {
		$params = [];
		if(!Yaf_Registry::get('api')){
			return $this->getView()->make('exception', $params);
		}
		$config = Yaf_Registry::get('config');
		$apiExt = $config->get("apiExt");
		$result = [
				'code' => 200,
				'message' => '',
				'ext' => '',
		];
		switch ($exception->getCode()) {
			case YAF_ERR_NOTFOUND_MODULE:
				$result = [
					'code' => 404,
					'message' => $exception ->getMessage(),
					'ext' => '未找到MODULE',
				];
				break;
			case YAF_ERR_NOTFOUND_CONTROLLER:
				$result = [
						'code' => 404,
						'message' => $exception ->getMessage(),
						'ext' => '未找到CONTROLLER',
				];
				break;
			case YAF_ERR_NOTFOUND_ACTION:
				$result = [
						'code' => 404,
						'message' => $exception ->getMessage(),
						'ext' => '未找到ACTION',
				];
				break;
			case YAF_ERR_NOTFOUND_VIEW:
				$result = [
						'code' => 404,
						'message' => $exception ->getMessage(),
						'ext' => '未找到VIEW',
				];
				break;
			default :
				break;
		}

		if($result['code'] == 404){
			if($apiExt){
				COMMON::ApiJson($result['code'], $result['message'], $result);
			}
			COMMON::ApiJson($result['code'], $result['message']);
		}
	}

}

<?php
/**
 * Bootstrap, 应用操作处理之前预定义/处理
 * 系统会依次处理 _init 开头的方法
 *
 * @author  dujiangjiang
 * @date    2011-12-31 17:18
 * @version $Id$
 */


class Bootstrap extends Yaf_Bootstrap_Abstract {

	/**
	 * 初始化全局变量及对象
	 */
	public function _initVariables(Yaf_Dispatcher $dispatcher) {
		$config = Yaf_Application::app()->getConfig();
		Yaf_Registry::set("config", $config);



		$db = $config->get("db");
		Yaf_Registry::set("db", new MySQL(
			$db->host,
			$db->port,
			$db->username,
			$db->password,
			$db->default,
			$db->charset
		));
/*
		$redis = $config->get("redis");
		Yaf_Registry::set("mc", new Cache(
			$redis->host,
			$redis->port
		));*/

		$session = Yaf_Session::getInstance();
		if ($session->has(SSN_VAR)) {
			Yaf_Registry::set(SSN_VAR, $session->get(SSN_VAR));
		} else {
			Yaf_Registry::set(SSN_VAR, false);
		}


		
	}

	/**
	 * 注册插件
	 */
	public function _initPlugin(Yaf_Dispatcher $dispatcher) {

		$config = Yaf_Application::app()->getConfig();
		$sys = $config->get("sys");

		if ($sys->encrypt) {
			$g = new GlobalPlugin();
			$dispatcher->registerPlugin($g);
		}

		$p = new PrivilegePlugin();
		$dispatcher->registerPlugin($p);
	}

	/**
	 * 指定视图引擎
	 */
	public function _initView(Yaf_Dispatcher $dispatcher) {
		#Yaf_Dispatcher::getInstance()->disableView();


		$dispatcher->setView(new View(APPLICATION_PATH . '/application/views',APPLICATION_PATH . '/application/cache'));
//		Yaf_Registry::set('view', true);
	}

	/**
	 * 添加路由规则
	 */
	public function _initRoute(Yaf_Dispatcher $dispatcher)
	{
		$rules = Yaf_Registry::get('config')->routes;
		if (!empty($rules))
		{
			$router = $dispatcher->getRouter();
			$router->addConfig($rules);
		}
	}


}
<?php
/**
 * 权限 Model
 *
 * @author  James
 * @date    2012-01-09 10:00
 * @version $Id$
 */

class PrivilegeModel
{
    /**
     * 数据库类实例
     *
     * @var object
     */
    public $dbh = null;

    /**
     * 缓存类实例
     *
     * @var object
     */
    public $mch = null;


    /**
     * Constructor
     *
     * @param   object  $dbh
     * @param   object  $mch
     * @return  void
     */
    public function __construct($dbh, $mch)
    {
        $this->dbh = $dbh;

        $this->mch = $mch;
    }

	/**
	 * 判断用户是否对某项操作有权限
	 *
	 * @param  string   $m
	 * @param  string   $a
	 * @param  boolean  $online
	 * @param  array    $user
	 * @return boolean
	 */
	public function check($controller, $action, $user)
    {
        $res = true; //默认未记录之操作均允许操作

        if(!$user){
            return false;
        }
				
        $sql = "SELECT * FROM ".DB_PREFIX."system_privilege WHERE privilegecontroller='#1' AND privilegeaction='#2' and status in (1,2) and isdel = 0 ";
        $rows = $this->dbh->select($sql, $controller, $action);
        if (!empty($rows))
        {
            foreach ($rows as $row) {
                if( !$row['needcheck'])
                return $res;
                    
                 $sql = "select count(rp.sysno) from  `".DB_PREFIX."system_role-r-privilege` rp , `".DB_PREFIX."system_user-r-role` ur where rp.privilege_sysno = '#1' and rp.role_sysno = ur.role_sysno and ur.user_sysno = '#2' ";

                $cnt = $this->dbh->select_one($sql, $row['sysno'], $user['sysno']);
                 
                 if($cnt > 0){
                        $input= array(
                        'user_sysno'  =>  $user['sysno'],
                        'user_realname'  =>  $user['realname'],
                        'loginip'  => COMMON::getclientIp(),
                        'controller' => $controller,
                        'action' => $action,
                        'url'    => $_SERVER["REQUEST_URI"],
                        'created_at'  =>  '=NOW()'
                      );
                      
                      $this->addPrivilegeLog($input);
                      $res = true;
                      break;
                
                 }else{
                    $res = false;
                 }
            }
            
        }

        return $res;
	}
 

	/**
	 * 记录操作日志(依据编号)
	 *
	 * @param  integer      $fid
	 * @param  array        $user
	 * @param  string       $remark
	 * @return void
	 */
	public function addPrivilegeLog($input =array())
    {
        $this->dbh->insert(DB_PREFIX.'system_log_privilege', $input);
    }

	/**
	 * 更新指定用户id的授权操作(依据角色)
	 *
	 * @param  integer     $uid     用户id
	 * @param  integer     $rids    角色ids
	 * @return void
	 */
	public function setUserPrivilegesByRole($uid, $rids)
    {
        $all = array();
        if ($rids)
        {
            $sql = "SELECT DISTINCT af_id FROM admin_privilege WHERE ap_object=2 AND ap_object_id IN (#1) AND ap_status=1";
            $all = $this->dbh->select($sql, $rids);
        }
        $key = "privilege:user:{$uid}";
        if ($this->mch->exists($key))
        {
            $this->mch->delete($key);
        }
        foreach ($all as $row)
        {
            $this->mch->sAdd($key, $row['af_id']);
        }
        if (count($all))
        {
            //$this->mch->setTimeout($key, 60*60); //1 hour
            $this->mch->expire($key, 60*60*8); //9 hour
        }
    }

	/**
	 * 返回指定用户的授权操作菜单
	 *
	 * @param  array    $user   用户
	 * @return void
	 */
	public function getUserMenu($user)
    {
        $menu = '';
        $all = array();
        if ($user['sa'] == SSN_SA)
        {
            $sql = "SELECT * FROM admin_function WHERE af_menu_on=1 AND af_status>=0 ORDER BY af_app,af_menu_order";
            $all = $this->dbh->select($sql);
        }
        else
        {
            $key = "privilege:user:{$user['id']}";
            $arr = $this->mch->sMembers($key);
            if (count($arr))
            {
                $sql = "SELECT * FROM admin_function WHERE af_id IN (#1) AND af_menu_on=1 AND af_status>=0 ORDER BY af_app,af_menu_order";
                $all = $this->dbh->select($sql, implode(',', $arr));
            }
        }
        $app = array();
        foreach ($all as $i => $row)
        {
            if (!isset($app["{$row['af_app']}"]))
            {
                $app["{$row['af_app']}"] = 1;
                if ($i > 0)
                {
                    $menu .= "</ul></li>\r\n<li><a>" . substr($row['af_app'],1). "</a><ul>";
                }
                else
                {
                    $menu .= "<li><a>" . substr($row['af_app'],1). "</a><ul>";
                }
            }
            $menu .= "<li><a href=\"" . WEB_ROOT . "{$row['af_menu_url']}\" target=\"navTab\" rel=\"{$row['af_module']}.{$row['af_action']}\">{$row['af_name']}</a></li>";
        }
        if (count($app))
        {
            $menu .= "</ul></li>";
        }

        return $menu;
    }

    /**
     * 返回指定用户的授权操作菜单
     *
     * @param  array    $user   用户
     * @return void
     */
    public function getUserMenuByNav($user,$navId)
    {
        $menu = '';
        $all = array();
        
        $nav_arr = array(0=>"common",1=>"carneed",2=>"need",3=>"xinneed",4=>"trainneed",5=>"marryneed",6=>"ticketneed");
        $nav_str = " ('carneed','need','xinneed','trainneed','marryneed','ticketneed') ";
        if ($user['sa'] == SSN_SA)
        {
            if ($navId == 0) {
                $sql = "SELECT * FROM admin_function WHERE af_menu_on=1 AND af_module not in {$nav_str} AND af_status>=0 ORDER BY af_app,af_menu_order";
            }
            else{
                $sql = "SELECT * FROM admin_function WHERE af_menu_on=1 AND af_module='{$nav_arr[$navId]}' AND af_status>=0 ORDER BY af_app,af_menu_order";    
            }
            
            $all = $this->dbh->select($sql);
        }
        else
        {
            $key = "privilege:user:{$user['id']}";
            $arr = $this->mch->sMembers($key);
            if (count($arr))
            {
                if ($navId == 0) {
                    $sql = "SELECT * FROM admin_function WHERE af_id IN (#1) AND af_menu_on=1 AND af_module not in {$nav_str} AND af_status>=0 ORDER BY af_app,af_menu_order";
                }else{
                    $sql = "SELECT * FROM admin_function WHERE af_id IN (#1) AND af_menu_on=1 AND af_module='{$nav_arr[$navId]}' AND af_status>=0 ORDER BY af_app,af_menu_order";
                }
                // $sql = "SELECT * FROM admin_function WHERE af_id IN (#1) AND af_menu_on=1 AND af_module='{$nav_arr[$navId]}' AND af_status>=0 ORDER BY af_app,af_menu_order";
                $all = $this->dbh->select($sql, implode(',', $arr));
            }
        }
        $app = array();
        foreach ($all as $i => $row)
        {
            if (!isset($app["{$row['af_app']}"]))
            {
                $app["{$row['af_app']}"] = 1;
                if ($i > 0)
                {
                    $menu .= "</ul></li>\r\n<li><a>" . substr($row['af_app'],1). "</a><ul>";
                }
                else
                {
                    $menu .= "<li><a>" . substr($row['af_app'],1). "</a><ul>";
                }
            }
            $menu .= "<li><a href=\"" . WEB_ROOT . "{$row['af_menu_url']}\" target=\"navTab\" rel=\"{$row['af_module']}.{$row['af_action']}\">{$row['af_name']}</a></li>";
        }
        if (count($app))
        {
            $menu .= "</ul></li>";
        }

        return $menu;
    }

    public function getUserMenu1($user)
    {
    	$menu = '';
    	$all = array();
    	if ($user['sa'] == SSN_SA)
    	{
    		$sql = "SELECT * FROM admin_function WHERE af_menu_on=1 AND af_module='carneed' AND af_status>=0 ORDER BY af_app,af_menu_order";
    		$all = $this->dbh->select($sql);
    	}
    	else
    	{
    		$key = "privilege:user:{$user['id']}";
    		$arr = $this->mch->sMembers($key);
    		if (count($arr))
    		{
    			$sql = "SELECT * FROM admin_function WHERE af_id IN (#1) AND af_menu_on=1 AND af_status>=0 AND af_module='carneed' ORDER BY af_app,af_menu_order";
    			$all = $this->dbh->select($sql, implode(',', $arr));
    		}
    	}
    	$app = array();
    	foreach ($all as $i => $row)
    	{
    		if (!isset($app["{$row['af_app']}"]))
    		{
    			$app["{$row['af_app']}"] = 1;
    			if ($i > 0)
    			{
    				$menu .= "</ul></li>\r\n<li><a>" . substr($row['af_app'],1). "</a><ul>";
    			}
    			else
    			{
    				$menu .= "<li><a>" . substr($row['af_app'],1). "</a><ul>";
    			}
    		}
    		$menu .= "<li><a href=\"" . WEB_ROOT . "{$row['af_menu_url']}\" target=\"navTab\" rel=\"{$row['af_module']}.{$row['af_action']}\">{$row['af_name']}</a></li>";
    	}
    	if (count($app))
    	{
    		$menu .= "</ul></li>";
    	}
    
    	return $menu;
    }    

    public function getUserForApi($user){
        $sql = "SELECT sp.sysno,sp.privilegeno,sp.privilegename FROM ".DB_PREFIX."system_privilege as sp 
                LEFT JOIN `hengyang_system_role-r-privilege` rp ON sp.sysno = rp.privilege_sysno
                LEFT JOIN `hengyang_system_user-r-role` ur ON rp.role_sysno = ur.role_sysno
                WHERE sp.privilegecontroller='api' and sp.status in (1,2) and sp.isdel = 0 
                AND ur.user_sysno = {$user['sysno']} GROUP BY sp.sysno";
        $rows = $this->dbh->select($sql);
        return $rows ? $rows : [];
    }
}

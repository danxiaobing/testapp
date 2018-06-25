<?php
/**
 * 用户管理
 *
 * @author  Alan
 * @date    2016-11-17 15:25:26
 *
 */

class PassworderrorModel
{
    /**
     * 数据库类实例
     *
     * @var object
     */
    public $dbh = null;

    public $mch = null;

    /**
     * Constructor
     *
     * @param   object $dbh
     * @return  void
     */
    public function __construct($dbh, $mch = null)
    {
        $this->dbh = $dbh;
        $this->mch = $mch;
    }




    /**
     * 插入错误记录
     * @author zhaoshiyu
     * @time 2016-11-11 14:38:38
     */
    public function insetErrorLog($params)
    {
        $this->dbh->begin();
        try{
            $res = $this->dbh->insert(DB_PREFIX.'system_passworderror', $params);
            if (!$res) {
                $this->dbh->rollback();
                return false;
            }
            $this->dbh->commit();
            return true;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }

    public function countErrorLog($id = 0)
    {
        $sql = "select count(user_sysno) as num from ".DB_PREFIX."system_passworderror  where user_sysno = $id ";

        return $this->dbh->select_row($sql);
    }
    public function delErrorLog($id = 0)
    {
        return $this->dbh->delete(DB_PREFIX.'system_passworderror', 'user_sysno=' . intval($id));
    }




}
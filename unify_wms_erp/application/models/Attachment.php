<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/15 0015
 * Time: 10:40
 */
class AttachmentModel
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
     * 添加附件
     * @author hanshutan
     */
    public function addAttachment($data) {

        return $this->dbh->insert(DB_PREFIX.'system_attach', $data);
    }

    public function getAttachmentById($id = 0) {
        $sql = "select * from ".DB_PREFIX."system_attach  where sysno = $id ";

        return $this->dbh->select_row($sql);
    }
   
    public function updateAttach($id = 0, $data = array()) {
        return $this->dbh->update(DB_PREFIX.'system_attach', $data, 'sysno=' . intval($id));
    }

    public function addAttachModelSysno($sysno,$attach) {

        if(is_array($attach) && count($attach) > 0){

            $data = array(
                'doc_sysno'  => $sysno
            );
            $aids = join(',',$attach);

            return $this->dbh->update(DB_PREFIX.'system_attach', $data, ' sysno in (' . $aids .')' );

        }else{
            return false;
        }
    }

    public function getAttachByMAS($module,$action,$sysno) {
        $sql = "select * from ".DB_PREFIX."system_attach  where module='$module' and action='$action' and doc_sysno  = '$sysno' and status =1 and isdel = 0 ";
        return $this->dbh->select($sql) ? $this->dbh->select($sql) : [];
    }

    public function delAttach($id = 0) {
        $data = array(
            'isdel' => 1
        );

        return $this->dbh->update(DB_PREFIX.'system_attach', $data, 'sysno=' . intval($id));
    }

    public function getAttachByDocsysno($doc_sysno)
    {
        $sql = "select * from ".DB_PREFIX."system_attach  where doc_sysno = ". intval($doc_sysno);

        return $this->dbh->select($sql);
    }
    /*
     *查看附件
     * */
    public function getAttachMAS($doc_sysno,$module,$action)
    {
        $sql = "select * from ".DB_PREFIX."system_attach  where doc_sysno = '$doc_sysno' and module ='$module' and action = '$action' and status =1 and isdel = 0 ";

        return $this->dbh->select($sql);
    }
    /*public function delAttach($id = 0) {
        $this->dbh->begin();
        $row = $this->dbh->select_row("select * from ".DB_PREFIX."system_attach where sysno = " . $id . " for update");
        if (!is_array($row) || count($row) == 0) {
            $this->dbh->rollback();
            return false;
        }

        try {
            $module = $row['module'];
            $action = $row['action'];
            $res = true;
            switch($action){
                case 'ship':
                    $res = $this->dbh->delete(DB_PREFIX.'base_ship-r-attach',  'attach_sysno=' . intval($id));
                    break;
                case 'customercertificates':
                    $res = $this->dbh->delete(DB_PREFIX.'customer-r-attach',  'attach_sysno=' . intval($id));
                    break;
                case 'customerlading':
                    $res = $this->dbh->delete(DB_PREFIX.'customer-r-attach',  'attach_sysno=' . intval($id));
                    break;
            }

            if(!$res){
                $this->dbh->rollback();
                return false;
            }

            $data = array(
                'isdel' => 1
            );

            $res = $this->dbh->update(DB_PREFIX.'system_attach', $data, 'sysno=' . intval($id));

            if(!$res){
                $this->dbh->rollback();
                return false;
            }
            
            $this->dbh->commit();
            return true;

        } catch (Exception $e) {
            $this->dbh->rollback();
            return false;
        }
    }*/
}
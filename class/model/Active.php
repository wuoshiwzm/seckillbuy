<?php
/**
 *活动相关数据库操作
 * Class model\Active
 * @author 王毅
 */

namespace model;

//Crud的子类  操作相关数据库
class Active Extends \Mysql\Crud {
    protected $table = 'ms_active';
    protected $pk	 = 'id';

    /**
     * 分页查询，不需要条件
     * @param int $start 分页开始偏移位置
     * @param int $end 分页结束便宜位置
     * @return mixed 列表
     */
    public function getList($start = 0, $end = 50) {
        $start = max(0, $start);
        $end = min(50, $end);
        $sql = "SELECT * FROM `" . $this->table . "` ORDER BY `$this->pk` DESC LIMIT $start, $end";
        return $this->getDb()->query($sql);
    }

    /**
     * 上线中的活动
     * @return mixed 列表
     */
    public function getListInuse() {
//        $now = time();
        //引入global变量
        global $now;
        //原生sql
//        $sql = "SELECT * FROM `" . $this->table . "` WHERE sys_status=1 AND time_end>$now ORDER BY `$this->pk` DESC";
//        return $this->getDb()->query($sql);
        //pdo
        $sql = "SELECT * FROM `" . $this->table . "` WHERE sys_status=:sys_status AND time_end>:now ORDER BY `$this->pk` DESC";
        $params = array('sys_status' => 1, 'now' => $now);
        //pdo
//        $sql = "SELECT * FROM `" . $this->table . "` WHERE sys_status=? AND time_end>? ORDER BY `$this->pk` DESC";
//        $params = array(1, $now);
        return $this->getDb()->query($sql, $params);
    }

}
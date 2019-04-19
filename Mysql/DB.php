<?php
namespace Mysql;
class DB
{
    private $pdo;

    private $sQuery;

    private $settings;

    private $bConnected = false;

    private $log;

    private $parameters;

    private static $instances = array();

    public static function getInstance($name = 'master')
    {
        if (isset(self::$instances[$name])) {
            return self::$instances[$name];
        }
        self::$instances[$name] = new \Mysql\DB($name);
        return self::$instances[$name];
    }

    private function __construct($name = 'master')
    {
        $this->Connect($name);
        $this->parameters = array();
    }

    private function Connect($name = 'master')
    {
        global $config;
        $mtime1 = microtime();
        $this->settings = $config['db'][$name];
        $dsn = 'mysql:dbname'.$this->setting['dbname'].';host='.$this->setting['host'].'';

        try{
            $this->pdo = new \PDO($dsn,$this->settings['user'],$this->settings['password'],array(\PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));

            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

            $this->bConnected = true;


        }catch (\PDOException $e){
            print_r($e);
            echo $this->ExceptionLog($e->getMessage());
            die();
        }

        $mtime2 = microtime();

        //写入日志
        \common\DebugLog::_mysql('connect',null,array('host'=>$this->settings['host'],'dbname'=>$this->settings['dbname']),$mtime1,$mtime2,null);
    }


    private function CloseConnection()
    {
        $this->pdo = null;
    }

    private function Init($query, $parameters="")
    {
        if(!$this->bConnected){
            $this->Connect();
        }

        try{
            //预处理
            $this->sQuery = $this->pdo->prepare($query);

            $this->bindMore($parameters);

            if(!empty($this->parameters)){
                foreach ($this->parameters as $param) {
                    $parameters = explode("\x7F", $param);
                    $this->sQuery->bindParam($parameters[0],$parameters[1]);
                }
            }
            $this->succes = $this->sQuery->execute();
        }catch (\PDOException $e){
            echo $this->ExceptionLog($e->getMessage(),$query);
            die();
        }

    }

    public function bindMore($parray)
    {
        if(empty($this->parameters) && is_array($parray)){
            $columns = array_keys($parray);
            foreach ($columns as $i => &$column) {
                $this->bind($column,$parray[$column]);
            }
        }
    }


    public function bind($para, $value)
    {
        $this->parameters[sizeof($this->parameters)] = ":".$para."\x7F".$value;
    }





























}
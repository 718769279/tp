<?php
/**
 * Created by PhpStorm.
 * User: wangwen
 * Date: 19-7-5
 * Time: 上午11:36
 */
namespace Think\Db\Driver;

use Think\Db\Driver;

/**
 * mysql数据库驱动
 */
class Dm extends Driver
{
    private $table       = '';
//    protected $selectSql = 'SELECT%DISTINCT% %FIELD% FROM %TABLE%%FORCE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%%LIMIT% %UNION%%LOCK%%COMMENT%';
    protected $selectSql = 'SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (SELECT  %DISTINCT% %FIELD% FROM %TABLE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%) thinkphp ) %LIMIT%%COMMENT%';

    /**
     * 解析pdo连接的dsn信息
     * @access public
     * @param array $config 连接信息
     * @return string
     */
    protected function parseDsn($config)
    {
        $dsn = 'dm:host=' . $config['hostname'] .':'. $config['hostport'].'/'.$config['database'];
        return $dsn;
    }

    /**
     * 执行语句
     * @access public
     * @param string $str  sql指令
     * @param boolean $fetchSql  不执行只是获取SQL
     * @return integer
     */
    public function execute($str, $fetchSql = false)
    {

        $this->initConnect(true);
        if (!$this->_linkID) {
            return false;
        }

        $this->queryStr = $str;

        if (!empty($this->bind)) {
            $that           = $this;
            $this->queryStr = strtr($this->queryStr, array_map(function ($val) use ($that) {return '\'' . $that->escapeString($val) . '\'';}, $this->bind));
        }
        if ($fetchSql) {
            return $this->queryStr;
        }
        $flag = false;
        if (preg_match("/^\s*(INSERT\s+INTO)\s+(\w+.\w+)\s+/i", $str, $match)) {
            $this->table = $match[2];
            $flag        = (boolean) $this->query("SELECT * FROM user_sequences WHERE sequence_name='" . strtoupper($this->table) . "'");
        }
        //释放前次的查询结果
        if (!empty($this->PDOStatement)) {
            $this->free();
        }
        $this->executeTimes++;
        N('db_write', 1); // 兼容代码
        // 记录开始执行时间
        $this->debug(true);
        $this->PDOStatement = $this->_linkID->prepare($str);
        if (false === $this->PDOStatement) {
            $this->error();
            return false;
        }

        foreach ($this->bind as $key => $val) {
            if (is_array($val)) {
                $this->PDOStatement->bindValue($key, $val[0], $val[1]);
            } else {
                $this->PDOStatement->bindValue($key, $val);
            }
        }
        $this->bind = array();
        $this->PDOStatement->bindValue("myKey", "123");
        $result     = $this->PDOStatement->execute();
        $this->debug(false);
        if (false === $result) {
            $this->error();
            return false;
        } else {
            $this->numRows = $this->PDOStatement->rowCount();
            if ($flag || preg_match("/^\s*(INSERT\s+INTO|REPLACE\s+INTO)\s+/i", $str)) {
                $this->lastInsID = $this->_linkID->lastInsertId();
            }
            return $this->numRows;
        }
    }

    /**
     * 取得数据表的字段信息
     * @access public
     */
    public function getFields($tableName)
    {
        list($database,$tableName) = explode('.', $tableName);
        $this->table = $tableName;
        $result          = $this->query("select a.column_name,data_type,decode(nullable,'Y',0,1) notnull,data_default,decode(a.column_name,b.column_name,1,0) pk "
            . "from user_tab_columns a,(select column_name from user_constraints c,user_cons_columns col "
            . "where c.constraint_name=col.constraint_name and c.constraint_type='P'and c.table_name='" . strtoupper($tableName)
            . "') b where table_name='" . strtoupper($tableName) . "' and a.column_name=b.column_name(+)");

        $info = array();
        if ($result) {
            foreach ($result as $key => $val) {
                $info[strtolower($val['column_name'])] = array(
                    'name'    => strtolower($val['column_name']),
                    'type'    => strtolower($val['data_type']),
                    'notnull' => $val['notnull'],
                    'default' => $val['data_default'],
                    'primary' => $val['pk'],
                    'autoinc' => $val['pk'],
                );
            }
        }
        return $info;
    }

    /**
     * SQL指令安全过滤
     * @access public
     * @param string $str  SQL指令
     * @return string
     */
    /*public function escapeString($str)
    {
        return str_ireplace("'", "''", $str);
    }*/

    /**
     * 执行存储过程查询 返回多个数据集
     * @access public
     * @param string $str  sql指令
     * @param boolean $fetchSql  不执行只是获取SQL
     * @return mixed
     */
    public function procedure($str, $fetchSql = false)
    {
        $this->initConnect(false);
        $this->_linkID->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
        if (!$this->_linkID) {
            return false;
        }

        $this->queryStr = $str;
        if ($fetchSql) {
            return $this->queryStr;
        }
        //释放前次的查询结果
        if (!empty($this->PDOStatement)) {
            $this->free();
        }

        $this->queryTimes++;
        N('db_query', 1); // 兼容代码
        // 调试开始
        $this->debug(true);
        $this->PDOStatement = $this->_linkID->prepare($str);
        if (false === $this->PDOStatement) {
            $this->error();
            return false;
        }
        try {
            $result = $this->PDOStatement->execute();
            // 调试结束
            $this->debug(false);
            do {
                $result = $this->PDOStatement->fetchAll(\PDO::FETCH_ASSOC);
                if ($result) {
                    $resultArr[] = $result;
                }
            } while ($this->PDOStatement->nextRowset());
            $this->_linkID->setAttribute(\PDO::ATTR_ERRMODE, $this->options[\PDO::ATTR_ERRMODE]);
            return $resultArr;
        } catch (\PDOException $e) {
            $this->error();
            $this->_linkID->setAttribute(\PDO::ATTR_ERRMODE, $this->options[\PDO::ATTR_ERRMODE]);
            return false;
        }
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: wangwen
 * Date: 19-7-3
 * Time: 上午11:56
 */
namespace Think\Db\Driver;

use Think\Db\Driver;

class Dm extends Driver
{
    private $table       = '';

    /**
     * 连接数据库方法
     * @access public
     */
    public function connect($config = '', $linkNum = 0)
    {
        if (!isset($this->linkID[$linkNum])) {
            if (empty($config)) {
                $config = $this->config;
            }

            try {
                $link = $config['hostname'].($config['hostport'] ? ':' . $config['hostport'] : '').($config['database'] ? '/' . $config['database'] : '');
                $this->linkID[$linkNum] = dm_connect($link, $config['username'], $config['password']);
            } catch (\Exception $e) {
                E($e->getmessage());
            }
        }
        return $this->linkID[$linkNum];
    }

    /**
     * 执行查询 返回数据集
     * @access public
     * @param string $str  sql指令
     * @param boolean $fetchSql  不执行只是获取SQL
     * @param boolean $master  是否在主服务器读操作
     * @return mixed
     */
    public function query($str, $fetchSql = false, $master = false)
    {
        $this->initConnect($master);
        if (!$this->_linkID) {
            return false;
        }

        $this->queryStr = $str;

        if ($fetchSql) {
            return $this->queryStr;
        }

        try {
            $resultId = dm_query($str);

            if(!$resultId)
            {
                throw new \Exception("Query failed : " . dm_error());
            }

            $result = array();
            while ($line = dm_fetch_array($resultId, DM_ASSOC)) {
                $result[] = $line;
            }
            //释放前次的查询结果
            dm_free_result($resultId);
            return $result;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
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

        $this->executeTimes++;
        N('db_write', 1); // 兼容代码
        $result     = $this->query($this->queryStr, $fetchSql);

        if (false === $result) {
            $this->error();
            return false;
        } else {
            $this->numRows = dm_affected_rows();
            if (is_array($result) || preg_match("/^\s*(INSERT\s+INTO|REPLACE\s+INTO)\s+/i", $str)) {
                $this->lastInsID = dm_insert_id();
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
                $info[strtolower($val['COLUMN_NAME'])] = array(
                    'name'    => strtolower($val['COLUMN_NAME']),
                    'type'    => strtolower($val['DATA_TYPE']),
                    'notnull' => $val['NOTNULL'],
                    'default' => $val['DATA_DEFAULT'],
                    'primary' => $val['PK'],
                    'autoinc' => $val['PK'],
                );
            }
        }
        return $info;
    }

    public function getLastInsID()
    {
        return dm_insert_id();
    }
}
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
    /**
     * 解析pdo连接的dsn信息
     * @access public
     * @param array $config 连接信息
     * @return string
     */
    /*protected function parseDsn($config)
    {
        $dsn = 'jdbc:dm://' . $config['DB_HOST'] . ($config['DB_PORT'] ? ':' . $config['DB_PORT'] : '') . '/' . $config['DB_NAME'];
        if (!empty($config['DB_CHARSET'])) {
            $dsn .= ';charset=' . $config['DB_CHARSET'];
        }
        return $dsn;
    }*/

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
                $this->linkID[$linkNum] = dm_connect($config['hostname'], $config['username'], $config['password']);
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
        print_r($this->table);exit;
        $flag = false;
        if (preg_match("/^\s*(INSERT\s+INTO)\s+(\w+)\s+/i", $str, $match)) {
            $this->table = C("DB_SEQUENCE_PREFIX") . str_ireplace(C("DB_PREFIX"), "", $match[2]);
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

//    /**
//     * 插入记录
//     * @access public
//     * @param mixed $data 数据
//     * @param array $options 参数表达式
//     * @param boolean $replace 是否replace
//     * @return false | integer
//     */
//    public function insert($data, $options = array(), $replace = false)
//    {
//        $values      = $fields      = array();
//        $this->model = $options['model'];
//        $this->parseBind(!empty($options['bind']) ? $options['bind'] : array());
//        foreach ($data as $key => $val) {
//            if (isset($val[0]) && 'exp' == $val[0]) {
//                $fields[] = $this->parseKey($key);
//                $values[] = $val[1];
//            } elseif (is_null($val)) {
//                $fields[] = $this->parseKey($key);
//                $values[] = 'NULL';
//            } elseif (is_scalar($val)) {
//                // 过滤非标量数据
//                $fields[] = $this->parseKey($key);
//                if (0 === strpos($val, ':') && in_array($val, array_keys($this->bind))) {
//                    $values[] = $val;
//                } else {
//                    $name     = count($this->bind);
//                    $values[] = ':' . $key . '_' . $name;
//                    $this->bindParam($key . '_' . $name, $val);
//                }
//            }
//        }
//        // 兼容数字传入方式
//        $replace = (is_numeric($replace) && $replace > 0) ? true : $replace;
//        $sql     = (true === $replace ? 'REPLACE' : 'INSERT') . ' INTO ' . $this->parseTable($options['table']) . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')' . $this->parseDuplicate($replace);
//        $sql .= $this->parseComment(!empty($options['comment']) ? $options['comment'] : '');
//        return $this->execute($sql, !empty($options['fetch_sql']) ? true : false);
//    }
}
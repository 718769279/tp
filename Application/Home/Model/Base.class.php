<?php

/**
 * +------------------------------------------------------
 * 商品操作类
 * +------------------------------------------------------
 * @描述：请使用 M(self::TAB_NAME)
 * +------------------------------------------------------
 */

namespace Home\Model;

use Think\Model;

class Base extends Model
{
    static $g_del = ['status' => 1];

    /**
     * 获取子类的表名
     *
     * @param
     * @return void
     */
    static public function tabName()
    {
        $modelStr = get_called_class();

        return $modelStr::tableName();
    }

    /**
     * 添加一条数
     *
     * @param $data 一维数据
     * @return int
     */
    static public function addOne($data)
    {
        if (!$data || !is_array($data)) return false;

        return M(self::tabName())->add($data);
    }

    /**
     * 添加多条数
     *
     * @param $data 二维数据
     * @return int
     */
    static public function adds($data)
    {
        if (!$data || !is_array($data)) return false;

        return M(self::tabName())->addAll($data);
    }

    /**
     * 根据id更新一条数据
     *
     * @param $id    设置id
     * @param $data  更新数据
     * @return int
     */
    static public function update($id, $data)
    {
        if (!intval($id) || !$data || !is_array($data)) return false;

        return M(self::tabName())->where(['id' => $id])->save($data);
    }

    /**
     * 根据条件更新一组数据
     *
     * @param $id    设置id
     * @param $data  更新数据
     * @return int
     */
    static public function updateByCondition($con, $data)
    {
        if (!$con || !is_array($con)) return false;
        if (!$data || !is_array($data)) return false;

        return M(self::tabName())->where($con)->save($data);
    }

    /**
     * 根据id获取一条数据
     *
     * @param $id    设置id
     * @param $order 排序字段
     * @return array
     */
    static public function getOneByLockId($id, $field = '*')
    {
        if (!intval($id)) return false;

//        return M(self::tabName())->lock(true)->field($field)->where(['productid' => $id])->find();
        return M(self::tabName())->lock(true)->field($field)->where(['id' => $id])->find();
    }

    /**
     * 根据id获取一条数据
     *
     * @param $id    设置id
     * @param $order 排序字段
     * @return array
     */
    static public function getOneById($id, $field = '*', $del = false)
    {
        if (!intval($id)) return false;
        if ($del) {
            return M(self::tabName())->field($field)->where(['id' => $id])->find();
        } else {
            return M(self::tabName())->field($field)->where(['id' => $id])->where(self::$g_del)->find();
        }
    }

    /**
     * 通过条件查询一条数据
     *
     * @param
     * @return void
     */
    static public function getOneByCondition($con, $field = '*', $order = 'id DESC', $del = false)
    {
        if ($del) {
            return M(self::tabName())->field($field)->where($con)->order($order)->find();
        } else {
            return M(self::tabName())->field($field)->where($con)->where(self::$g_del)->order($order)->find();
        }
    }

    /**
     * 通过条件查询一列数据
     *
     * @param
     * @return void
     */
    static public function getFieldByCondition($con, $field, $del = false)
    {
        if ($del) {
            return M(self::tabName())->field($field)->where($con)->getField($field, true);
        } else {
            return M(self::tabName())->field($field)->where($con)->where(self::$g_del)->getField($field, true);
        }
    }

    /**
     * 通过条件查询和数据
     *
     * @param
     * @return void
     */
    static public function getSumByCondition($con, $field, $del = false)
    {
        if ($del) {
            return M(self::tabName())->field($field)->where($con)->sum($field);
        } else {
            return M(self::tabName())->field($field)->where($con)->where(self::$g_del)->sum($field);
        }
    }

    /**
     * 获取设所有数据
     *
     * @param $field 获取字段
     * @param $order 排序字段
     * @return array
     */
    static public function getAll($field = '*', $order = 'id', $del = false)
    {
        if ($del) {
            return M(self::tabName())->field($field)->order($order)->select();
        } else {
            return M(self::tabName())->field($field)->where(self::$g_del)->order($order)->select();
        }
    }

    /**
     * 查询列表
     *
     * @param
     * @return void
     */
    static public function getAllByCondition($con = [], $field = '*', $order = 'id DESC', $del = false)
    {
        if ($del) {
            return M(self::tabName())->field($field)->where($con)->order($order)->select();
        } else {
            return M(self::tabName())->field($field)->where($con)->where(self::$g_del)->order($order)->select();
        }
    }

    /**
     * 查询总数。
     *
     * @param string
     * @return array
     */
    static public function getTotal($del = false)
    {
        if ($del) {
            return M(self::tabName())->count();
        } else {
            return M(self::tabName())->where(self::$g_del)->count();
        }
    }

    /**
     * 获取数据列表
     *
     * @param $start ,$limit 分页
     * @param $field 获取字段
     * @param $order 排序字段
     * @return array
     */
    static public function getList($start = 0, $limit = 20, $field = '*', $order = 'id', $del = false)
    {
        if ($del) {
            return M(self::tabName())->field($field)->limit($start, $limit)->order($order)->select();
        } else {
            return M(self::tabName())->field($field)->where(self::$g_del)->limit($start, $limit)->order($order)->select();
        }
    }

    /**
     * 通过条件查询总数。
     *
     * @param string
     * @return array
     */
    static public function getTotalByCondition($con, $del = false)
    {
        if ($del) {
            return M(self::tabName())->where($con)->where(self::$g_del)->count('id');
        } else {
            return M(self::tabName())->where($con)->where(self::$g_del)->count('id');
        }
    }

    /**
     * 通过条件查询列表
     *
     * @param
     * @return void
     */
    static public function getListByCondition($con, $start = 0, $limit = 15, $field = '*', $order = 'id DESC', $del = false)
    {
        if ($del) {
            return M(self::tabName())->field($field)->where($con)->limit($start, $limit)->order($order)->select();
        } else {
            return M(self::tabName())->field($field)->where($con)->where(self::$g_del)->limit($start, $limit)->order($order)->select();
        }
    }

    /**
     * 原始SQL语句。
     *
     * @param
     * @return void
     */
    static public function getBySql($sql)
    {
        if (!$sql || !is_string($sql))
            return false;

        return M()->query($sql);
    }

}
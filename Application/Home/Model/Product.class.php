<?php
/**
 * Created by PhpStorm.
 * User: wangwen
 * Date: 19-7-6
 * Time: 上午10:08
 */
namespace Home\Model;
use Home\Model\Base;

class Product extends Base
{
    const TAB_NAME = 'production.product';

    /**
     * 返回表名
     */
    static public function tableName()
    {
        return self::TAB_NAME;
    }
}
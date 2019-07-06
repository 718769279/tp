<?php
namespace Home\Controller;

use Think\Controller;
use Home\Model\Base;
use Home\Model\Product;

class IndexController extends Controller
{
    public function dm()
    {
        //查询所有数据
        $info = Product::getAll("*","", true);

        //查询指定字段和指定排序的所有结果
//        $info = Product::getAll("name,author,publisher,publishtime,nowprice","nowprice desc", true);

        //更具id查询
//        $info = Product::getOneByLockId("25","name,author,publisher,publishtime,nowprice");

        //新增
//        $data = array(
//            "name"=>"封神榜123123",
//            "author"=>"许仲琳",
//            "publisher"=>"中华书局",
//            "publishtime"=>"1600-04-01",
//            "product_subcategoryid"=>"4",
//            "productno"=>"97875391259952",
//            "satetystocklevel"=>"10",
//            "originalprice"=>"66",
//            "nowprice"=>"65",
//            "discount"=>"8.8",
//            "description"=>"详细的描述",
//            "photo"=>"",
//            "type"=>"16",
//            "papertotal"=>"988",
//            "wordtotal"=>"2342340",
//            "sellstarttime"=>"2019-07-03",
//            "sellendtime"=>""
//        );
//        $info = Product::addOne($data);

        //批量添加
//        $data = array(
//            array(
//                "name"=>"封神榜111",
//                "author"=>"许仲琳",
//                "publisher"=>"中华书局",
//                "publishtime"=>"1600-04-01",
//                "product_subcategoryid"=>"4",
//                "productno"=>"97875391259953",
//                "satetystocklevel"=>"10",
//                "originalprice"=>"66",
//                "nowprice"=>"65",
//                "discount"=>"8.8",
//                "description"=>"详细的描述",
//                "photo"=>"",
//                "type"=>"16",
//                "papertotal"=>"988",
//                "wordtotal"=>"2342340",
//                "sellstarttime"=>"2019-07-03",
//                "sellendtime"=>""
//            ),
//            array(
//                "name"=>"封神榜222",
//                "author"=>"许仲琳",
//                "publisher"=>"中华书局",
//                "publishtime"=>"1600-04-01",
//                "product_subcategoryid"=>"4",
//                "productno"=>"97875391259954",
//                "satetystocklevel"=>"10",
//                "originalprice"=>"66",
//                "nowprice"=>"65",
//                "discount"=>"8.8",
//                "description"=>"详细的描述",
//                "photo"=>"",
//                "type"=>"16",
//                "papertotal"=>"988",
//                "wordtotal"=>"2342340",
//                "sellstarttime"=>"2019-07-03",
//                "sellendtime"=>""
//            )
//        );
//        $info = Product::adds($data);

        //更新
//        $info = Product::updateByCondition(array("productid"=>"26"), array("name"=>"封神榜123456789"));

        //求和
//        $info = Product::getSumByCondition(array("product_subcategoryid"=>4),"nowprice",true);

        //获取总数
//        $info = Product::getTotal(true);

        //分页
//        $info = Product::getList(0, 5, "*", "nowprice", true);

        //查询原始语句
//        $info = Product::getBySql("SELECT * FROM production.product");

        //通过条件查询一列数据
//        $info = Product::getFieldByCondition(array("product_subcategoryid"=>4),"name",true);
//        print_r($info);exit;

        //M方法调用
        $info = M("product")
//            ->field("name,author,publisher,publishtime,nowprice")
//            ->where(array("product_subcategoryid"=>4))
//            ->limit(0,3)
//            ->sum("nowprice");
//            ->max("nowprice");
//            ->min("nowprice");
//            ->avg("nowprice");
            ->select();
        print_r($info);exit;
    }
}
<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
    }

    public function test()
    {
//        $info = M('', '', C('DB_DM'))->query("select * from PRODUCTION .product");
//        $info = M('PRODUCTION.product', '', C('DB_DM'))
//            ->where(array("SATETYSTOCKLEVEL"=>10))
//            ->where(array("PRODUCT_SUBCATEGORYID"=>4))
//            ->limit(1)
//            ->select();
//        $info = M("teacher")->select();

        $data = array(
            "name"=>"封神榜",
            "author"=>"许仲琳",
            "publisher"=>"中华书局",
            "publishtime"=>"1600-04-01",
            "product_subcategoryid"=>"4",
            "productno"=>"9787539125996",
            "satetystocklevel"=>"10",
            "originalprice"=>"66",
            "nowprice"=>"65",
            "discount"=>"8.8",
            "description"=>"详细的描述",
            "photo"=>"",
            "type"=>"16",
            "papertotal"=>"988",
            "wordtotal"=>"2342340",
            "sellstarttime"=>"2019-07-03",
            "sellendtime"=>""
        );

        $info = M('PRODUCTION.product', '', C('DB_DM'))->add($data);

        var_dump($info);
    }
}
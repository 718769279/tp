<?php
/**
 * Created by PhpStorm.
 * User: Wangwen
 * Date: 2019/7/17
 * Time: 14:38
 */
namespace Lib;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class DnsAliyun
{
    // Access Key ID
    private $accessKeyId = '';
    // Access Access Key Secret
    private $accessKeySecret = '';

    public function __construct($config = array())
    {
        $this->accessKeyId = $config['accessKeyId']?$config['accessKeyId']:"VjdXsFTMMr9EGMWX";
        $this->accessKeySecret = $config['accessKeySecret']?$config['accessKeySecret']:"aEsNgKfCirDXRYB13dc9PwfmNKpvVd";

        AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessKeySecret)
            ->regionId("cn-hangzhou")
            ->asDefaultClient();
    }

    public function DescribeDomainRecordInfo()
    {
        try{
            $result = AlibabaCloud::rpc()
                ->product('Alidns')
                ->action('DescribeDomainRecordInfo')
                ->method('POST')
                ->request();
            print_r($result->toArray());
        } catch (ClientException $e){
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e){
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }
}
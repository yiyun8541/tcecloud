# 简介

腾讯 TCECLOUD SDK 的laravel 包

# 安装
## 配置

生成包配置，根据需要，配置 tcecloud.php，详见包里边的 config.php

## config/app.php

```
'provider'  => [
    Cloud\TceCloud\TceCloudServiceProvider::class,
],

'aliases' => [
    'TceCloud' => Cloud\TceCloud\Facades\TceCloud::class,
],
```


# 提供的 method
## sdkVersion SDK的版本号
若不需要版本号，则无需调用该方法，前提是需去掉TCE 提供的SDK的版本号，如 Bspaccount SDK 包，其 SDK 为
TceCloud\Bspaccount\V20190314，开发者自行去掉该包 V20190314 相关的字眼
## area 调用区域
根据区域来调取数据， 默认取 config.php 里的 'default' 配置
## apiVersion
即调用地址中的 opbill.yunapi3.tcecqpoc.fsphere.cn 中的 'yunapi3'


# 示例

```
use Cloud\TceCloud\Facades\TceCloud;

// 获取默认区域的用户数据，具体参数同 tcecloud-sdk-php 中的示例 
TceCloud::driver('bspaccount')->sdkVersion('V20190314')->GetCustomAccount([
    'PageNum' => 1,
    'PageSize' => 100
]);

// 获取'shenzhen'区域的用户数据，需自行配置区域数据，请参考 tcecloud.php 配置
TceCloud::driver('bspaccount')->sdkVersion('V20190314')->area('shenzhen')->GetCustomAccount([
    'PageNum' => 1,
    'PageSize' => 100
]);

// 租户端调用数据
TceCloud::driver('bspaccount')->sdkVersion('V20190314')->apiVersion('api3')...

// 获取CVM实例
TceCloud::driver('cvm')->sdkVersion('V20170312')->area($region)->QueryInstances([
   'Filters' => [
       [
           'Name' => 'zone',
           'Values' => ['ap-shanghai-1', 'ap-shanghai-2']
       ]
]);
```


<?php

namespace Cloud\TceCloud;

use Illuminate\Http\Request;
use TceCloud\Common\Credential;
use TceCloud\Common\Profile\ClientProfile;
use TceCloud\Common\Profile\HttpProfile;
use Cloud\TceCloud\Exception\TceCloudException;

abstract class AbstractProvider
{

    /**
     * The Client instance.
     *
     */
    protected $client;


    /**
     * The Cloud config.
     *
     * @var string
     */
    protected $config;

    /**
     * The Credential instance.
     *
     */
    protected $credential;

    /**
     * The clientProfile instance.
     *
     */
    protected $clientProfile;

    /**
     * The httpProfile instance.
     *
     */
    protected $httpProfile;


    /**
     * The product name.
     *
     */
    protected $productName;

    /**
     * The SDK version.
     *
     */
    protected $SDKVersion;

    /**
     * The request domain
     *
     */
    protected $domain;

    /**
     * The request endpoint
     *
     */
    protected $endpoint;


    /**
     * The request timeout.
     *
     */
    protected $timeout = 5;

    /**
     * The api version.
     *
     */
    protected $apiVersion = 'yunapi3';


    /**
     * The area alias.
     *
     */
    protected $area = 'default';


    /**
     * The sign method.
     *
     */
    protected $signMethod = 'TC3-HMAC-SHA256';

    /**
     * Create a new provider instance.
     *
     * @param  string  $productName 产品名称别名如 'opbill', 'cvm'
     * @return void
     */
    public function __construct($productName, $config)
    {
        $this->productName = $productName;
        $this->config = $config;
    }

    /**
     * Set request endpoint
     *
     * @param  string  $endpoint
     * @return $this
     */
    public function endpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * Get the endpoint
     *
     * @return string
     */
    public function getEndpoint()
    {
        if ( $this->endpoint ) {
            return  $this->endpoint;
        }

        $config = $this->config->get('area');
        if (empty($config[$this->area]['domain'])) {
            throw new TceCloudException(500, "The  domain of {$this->area} area is empty!");
        }
        $this->domain = $config[$this->area]['domain'];
        return "{$this->productName}.{$this->apiVersion}.{$this->domain}";
    }

    /**
     *  Set the SDKVersion.
     *
     * @param  string  $version
     * @return $this
     */
    public function sdkVersion($version)
    {
        $this->SDKVersion = $version;
        return $this;
    }

    /**
     *  Set the apiVersion.
     *
     * @param  string  $version
     * @return $this
     */
    public function apiVersion($version)
    {
        $this->apiVersion = $version;
        return $this;
    }

    /**
     *  Set the request area.
     *
     * @param  string  $areaAlias
     * @return $this
     */
    public function area($areaAlias)
    {
        $this->area = $areaAlias;
        return $this;
    }

    /**
     *  Set the request timeout.
     *
     * @param  string  $seconds
     * @return $this
     */
    public function timeout($seconds)
    {
        $this->timeout = $seconds;
        return $this;
    }

    /**
     *  Set the request timeout.
     *
     * @param  string  $seconds
     * @return $this
     */
    public function signMethod($method)
    {
        $this->signMethod = $method;
        return $this;
    }

    /**
     *  Set the request httpProfile.
     *
     * @param TceCloud\Common\Profile\HttpProfile
     * @return $this
     */
    public function httpProfile(HttpProfile $httpProfile)
    {
        $this->httpProfile = $httpProfile;
        return $this;
    }


    protected function setClientProfile()
    {
        if ($this->httpProfile == null ) {
            // 实例化一个http选项，可选的，没有特殊需求可以跳过
            $httpProfile = new HttpProfile();
            $httpProfile->setReqMethod("POST");  // post请求(默认为post请求)
            $httpProfile->setReqTimeout($this->timeout);    // 请求超时时间，单位为秒(默认60秒)
            $httpProfile->setProtocol("http://");
            $httpProfile->setEndpoint($this->getEndpoint());  // TODO: opbill是接入TCE的计费模块，api3是调用版本， {{conf.main_domain}}是主域名。

        }

        // 实例化一个client选项，可选的，没有特殊需求可以跳过
        $clientProfile = new ClientProfile();
        $clientProfile->setSignMethod($this->signMethod);  // 指定签名算法(默认为HmacSHA256)
        $clientProfile->setHttpProfile($httpProfile);
        $this->clientProfile = $clientProfile;
        return $this;
    }


    protected function setCredential()
    {
        $config = $this->config->get('area');
        if (empty($config[$this->area]['secretId']) || empty($config[$this->area]['secretKey']) ) {
            throw new TceCloudException(500, "The  secretId or secretKey of {$this->area} area is empty!");
        }

        $this->credential = new Credential($config[$this->area]['secretId'], $config[$this->area]['secretKey']);
        return $this->credential;
    }

    /**
     * Get Client class path
     *
     * @param  string  $endpoint
     * @return void
     */
    public function clientInit()
    {
        $this->setCredential();
        $this->setClientProfile();
        $sdkVersion = $this->SDKVersion?"\\{$this->SDKVersion}":"";
        $this->clientClass = "TceCloud\\".ucfirst($this->productName).$sdkVersion."\\".ucfirst($this->productName)."Client";
    }

    /**
     * Get Client instance
     *
     * @param  string  $endpoint
     * @return void
     */
    public function getClient()
    {
        if(!$this->client) {
            $this->setClient();
        }
        return $this->client;
    }


    /**
     * Set product client instance.
     *
     * @return $this
     */
    protected function setClient()
    {
        $this->clientInit();
        $this->client = new $this->clientClass($this->credential, "", $this->clientProfile);
    }


    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (isset($parameters[0]) && is_object($parameters[0])) {
            $req = $parameters[0];
        } else {
            $sdkVersion = $this->SDKVersion?"\\{$this->SDKVersion}":"";
            $requestClass = "TceCloud\\".ucfirst($this->productName)."{$sdkVersion}\\Models\\".ucfirst($method)."Request";
            $req = new $requestClass;

            $parameters = isset($parameters[0]) && is_array($parameters[0])?$parameters[0]:$parameters;
            $req->fromJsonString(json_encode($parameters));
        }
        return $this->getClient()->$method($req);
    }

}

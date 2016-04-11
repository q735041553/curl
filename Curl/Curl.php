<?php

// +----------------------------------------------------------------------
// | 悟空信息技术有限公司
// +----------------------------------------------------------------------
// | Copyright (c)2016 http://www.wkidt.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: wkidt team LSQ <admin@wkidt.com> 2016/4/11 10:58
// +----------------------------------------------------------------------
// | Readme: curl抽象类
// +----------------------------------------------------------------------

namespace Curl;

abstract class Curl {

    /**
     * 获取一个实例
     * @param string $code
     * @return bool
     */
    public static function getInstance($code='curl'){
        $code = ucwords($code);
        $className = "Curl\\Driver\\{$code}";
        if( class_exists($className) ){
            return new $className;
        }else{
            return false;
        }
    }

    /**
     * get抽象方法
     * @param $url
     * @param $params
     * @return mixed
     */
    abstract public function get($url, $params);

    /**
     * 设置ua
     * @param $ua
     * @return mixed
     */
    abstract public function setUserAgent($ua);

    /**
     * 设置Header
     * @param $header
     * @return mixed
     */
    abstract public function setHeader($header);

    /**
     * 把数组连接为&k=v形式
     * @param $params
     * @return string
     */
    protected function _parseParam($params){
        if( is_array($params) ){
            $res = '';
            foreach($params as $k=>$v){
                $res .= "&{$k}=".urlencode($v);
            }
            $res = substr($res, 1);
            return $res;
        }else{
            return $params;
        }
    }

    /**
     * 分析url
     * @param $url
     * @param string $params
     * @return string
     */
    protected function _parseUrl($url, $params=''){
        return false!==strpos($url, '?')?$url."&".$this->_parseParam($params):$url."?".$this->_parseParam($params);
    }
}
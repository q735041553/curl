<?php

/**
 * Curl 封装
 * Some rights reserved：abc3210.com
 * Contact email:admin@abc3210.com
 */

namespace Curl\Driver;

class Curl extends \Curl\Curl
{

    /**
     * 执行请求
     * @param string $method 请求方式
     * @param string $url 地址
     * @param string|array $fields 附带参数，可以是数组，也可以是字符串
     * @param string $userAgent 浏览器UA
     * @param string $httpHeaders header头部，数组形式
     * @param string $username 用户名
     * @param string $password 密码
     * @return boolean
     */
    protected function execute($method, $url, $fields = '', $userAgent = '', $httpHeaders = '', $username = '', $password = '')
    {
        $ch = $this->create(); //得到一个curl句柄
        if (false === $ch) {
            return false;
        }
        if (is_string($url) && strlen($url)) {
            $ret = curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL地址
        } else {
            return false;
        }
        //是否显示头部信息
        curl_setopt($ch, CURLOPT_HEADER, false);
        //
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //将 curl_exec() 获取的信息以文件流的形式返回，而不是直接输出。
        if ($username != '') {
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password); //传递一个连接中需要的用户名和密码，格式为
        }

        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  //检查服务器SSL证书中是否存在一个公用名(common name)。
        }

        $method = strtolower($method);
        if ('post' == $method) {
            curl_setopt($ch, CURLOPT_POST, true); //启用时会发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
            if (is_array($fields)) {
                $sets = array();
                foreach ($fields AS $key => $val) {
                    $sets[] = $key . '=' . urlencode($val);
                }
                $fields = implode('&', $sets);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields); //post的数据，如username=a&password=123456等格式
        } else if ('put' == $method) {
            curl_setopt($ch, CURLOPT_PUT, true);
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, 10); //设置curl超时秒数
        if (strlen($userAgent)) {
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        }
        if (is_array($httpHeaders)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders); //array('Content-type: text/plain', 'Content-length: 100')
        }
        $ret = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            return array(curl_error($ch), curl_errno($ch));
        } else {
            curl_close($ch);
            if (!is_string($ret) || !strlen($ret)) {
                return false;
            }
            return $ret;
        }
    }

    /**
     * 发送POST请求
     * @param string $url 地址
     * @param string|array $params 附带参数，可以是数组，也可以是字符串
     * @return boolean
     */
    public function post($url, $params)
    {
        $ret = $this->execute('POST', $url, $params);
        if (false === $ret) {
            return false;
        }
        if (is_array($ret)) {
            return false;
        }
        return $ret;
    }

    /**
     * GET
     * @param string $url 地址
     * @param string $params 浏览器UA
     * @return boolean
     */
    public function get($url, $params = '')
    {
        $ret = $this->execute('GET', $this->_parseUrl($url, $params));
        if (false === $ret) {
            return false;
        }
        if (is_array($ret)) {
            return false;
        }
        return $ret;
    }

    /**
     * 设置ua
     * @param $ua
     * @return bool
     */
    public function setUserAgent($ua)
    {
        return false;
    }

    /**
     * 设置头信息
     * @param $header
     * @return bool
     */
    public function setHeader($header)
    {
        return false;
    }

    /**
     * curl支持 检测
     * @return boolean
     */
    public function create()
    {
        $ch = null;
        if (!function_exists('curl_init')) {
            return false;
        }
        $ch = curl_init();
        if (!is_resource($ch)) {
            return false;
        }
        return $ch;
    }
}
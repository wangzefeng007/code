<?php

namespace app\common\behavior;

/**
 * CORS 跨域的概念与 TP5 的解决方案
 * Author: jyook <jyook@qq.com>
 */
class CORS
{
    protected $corsBase = TRUE; // TRUE 代表全局通过，指定域名则失效
    public function run(&$params)
    {

        // 来路URL地址
        $origin = isset ( $_SERVER ['HTTP_ORIGIN'] ) ? $_SERVER ['HTTP_ORIGIN'] : '';
        $protocol = $_SERVER ['REQUEST_SCHEME'] . '://';
        
        // 允许访问地址
        $origin_allow = [ 
            $protocol . '127.0.0.1', 
            $protocol . '47.96.25.111'
        ];
        
        // 允许请求方法
        if ($this->corsBase || in_array ( $origin, $origin_allow )) {
            header ( 'Access-Control-Allow-Origin:' . $origin ); // 允许跨域访问的域，可以是一个域的列表，也可以是通配符"*"。这里要注意Origin规则只对域名有效，并不会对子目录有效。即http://foo.example/subdir/ 是无效的。但是不同子域名需要分开设置，这里的规则可以参照同源策略
            header ( 'Access-Control-Allow-Credentials: true' ); // 是否允许请求带有验证信息，这部分将会在下面详细解释
            header ( 'Access-Control-Max-Age: 172800' ); // 缓存此次请求的秒数。在这个时间范围内，所有同类型的请求都将不再发送预检请求而是直接使用此次返回的头作为判断依据，非常有用，大幅优化请求次数
            header ( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' ); // 允许使用的请求方法，以逗号隔开
            header ( 'Access-Control-Allow-Headers: Token, X-Requested-With, Content-Type, Accept' ); // 允许自定义的头部，以逗号隔开，大小写不敏感
        }
        
        // 判断是否为OPTIONS请求
        if (request ()->isOptions ()) {
            exit ();
        }
    }
}
<?php 
namespace addons\csmding\library\dingtalk;
//<?php

/**
 * 结果
 * @author auto create
 */
class IsvAuthCodeResult
{
	
	/** 
	 * 授权码有效期，unix时间戳，单位ms
	 **/
	public $expire_time;
	
	/** 
	 * 授权码
	 **/
	public $isv_code;	
}
?>
<?php 
namespace addons\csmding\library\dingtalk;
//<?php
/**
 * dingtalk API: dingtalk.oapi.process.workrecord.update request
 * 
 * @author auto create
 * @since 1.0, 2019.08.09
 */
class OapiProcessWorkrecordUpdateRequest
{
	/** 
	 * 请求
	 **/
	private $request;
	
	private $apiParas = array();
	
	public function setRequest($request)
	{
		$this->request = $request;
		$this->apiParas["request"] = $request;
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function getApiMethodName()
	{
		return "dingtalk.oapi.process.workrecord.update";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}

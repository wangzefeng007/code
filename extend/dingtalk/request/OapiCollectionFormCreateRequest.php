<?php
/**
 * dingtalk API: dingtalk.oapi.collection.form.create request
 * 
 * @author auto create
 * @since 1.0, 2020.08.25
 */
class OapiCollectionFormCreateRequest
{
	/** 
	 * 根请求
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
		return "dingtalk.oapi.collection.form.create";
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

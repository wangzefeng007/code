<?php 
namespace addons\csmding\library\dingtalk;
//<?php
/**
 * dingtalk API: dingtalk.oapi.rhino.mos.exec.perform.create request
 * 
 * @author auto create
 * @since 1.0, 2020.03.23
 */
class OapiRhinoMosExecPerformCreateRequest
{
	/** 
	 * 工序执行记录
	 **/
	private $operations;
	
	/** 
	 * 租户ID
	 **/
	private $tenantId;
	
	/** 
	 * 用户ID
	 **/
	private $userid;
	
	private $apiParas = array();
	
	public function setOperations($operations)
	{
		$this->operations = $operations;
		$this->apiParas["operations"] = $operations;
	}

	public function getOperations()
	{
		return $this->operations;
	}

	public function setTenantId($tenantId)
	{
		$this->tenantId = $tenantId;
		$this->apiParas["tenant_id"] = $tenantId;
	}

	public function getTenantId()
	{
		return $this->tenantId;
	}

	public function setUserid($userid)
	{
		$this->userid = $userid;
		$this->apiParas["userid"] = $userid;
	}

	public function getUserid()
	{
		return $this->userid;
	}

	public function getApiMethodName()
	{
		return "dingtalk.oapi.rhino.mos.exec.perform.create";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->tenantId,"tenantId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}

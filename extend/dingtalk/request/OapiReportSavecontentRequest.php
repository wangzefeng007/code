<?php
/**
 * dingtalk API: dingtalk.oapi.report.savecontent request
 * 
 * @author auto create
 * @since 1.0, 2020.09.17
 */
class OapiReportSavecontentRequest
{
	/** 
	 * 保存日志的参数对象
	 **/
	private $createReportParam;
	
	private $apiParas = array();
	
	public function setCreateReportParam($createReportParam)
	{
		$this->createReportParam = $createReportParam;
		$this->apiParas["create_report_param"] = $createReportParam;
	}

	public function getCreateReportParam()
	{
		return $this->createReportParam;
	}

	public function getApiMethodName()
	{
		return "dingtalk.oapi.report.savecontent";
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

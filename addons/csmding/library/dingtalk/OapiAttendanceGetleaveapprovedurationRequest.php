<?php 
namespace addons\csmding\library\dingtalk;
//<?php
/**
 * dingtalk API: dingtalk.oapi.attendance.getleaveapproveduration request
 * 
 * @author auto create
 * @since 1.0, 2019.07.03
 */
class OapiAttendanceGetleaveapprovedurationRequest
{
	/** 
	 * 请假开始时间
	 **/
	private $fromDate;
	
	/** 
	 * 请假结束时间
	 **/
	private $toDate;
	
	/** 
	 * 员工在企业内的UserID，企业用来唯一标识用户的字段。
	 **/
	private $userid;
	
	private $apiParas = array();
	
	public function setFromDate($fromDate)
	{
		$this->fromDate = $fromDate;
		$this->apiParas["from_date"] = $fromDate;
	}

	public function getFromDate()
	{
		return $this->fromDate;
	}

	public function setToDate($toDate)
	{
		$this->toDate = $toDate;
		$this->apiParas["to_date"] = $toDate;
	}

	public function getToDate()
	{
		return $this->toDate;
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
		return "dingtalk.oapi.attendance.getleaveapproveduration";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->fromDate,"fromDate");
		RequestCheckUtil::checkNotNull($this->toDate,"toDate");
		RequestCheckUtil::checkNotNull($this->userid,"userid");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}

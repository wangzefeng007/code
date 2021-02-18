<?php 
namespace addons\csmding\library\dingtalk;
//<?php

/**
 * demo
 * @author auto create
 */
class TopSimpleGroupVO
{
	
	/** 
	 * 考勤地址
	 **/
	public $address_list;
	
	/** 
	 * id
	 **/
	public $id;
	
	/** 
	 * 考勤组管理员
	 **/
	public $manager_list;
	
	/** 
	 * 人员人数
	 **/
	public $member_count;
	
	/** 
	 * 名称
	 **/
	public $name;
	
	/** 
	 * 考勤组主负责人 id
	 **/
	public $owner_user_id;
	
	/** 
	 * 固定值，轮班制
	 **/
	public $type;
	
	/** 
	 * 跳转链接
	 **/
	public $url;
	
	/** 
	 * wifi名称
	 **/
	public $wifis;
	
	/** 
	 * 工作日
	 **/
	public $work_day_list;	
}
?>
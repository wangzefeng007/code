<?php

/**
 * 职位数据列表
 * @author auto create
 */
class AtsPageResult
{
	
	/** 
	 * 是否还有数据
	 **/
	public $has_more;
	
	/** 
	 * 职位信息列表
	 **/
	public $list;
	
	/** 
	 * 游标，下次分页请求使用
	 **/
	public $next_cursor;	
}
?>
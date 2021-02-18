<?php

/**
 * 创建动态的入参
 * @author auto create
 */
class FvPostCreateOpenDto
{
	
	/** 
	 * 用户在归属组织内的userId
	 **/
	public $belong_corp_user_id;
	
	/** 
	 * 动态的内容
	 **/
	public $content;
	
	/** 
	 * 圈子的corpId
	 **/
	public $corp_id;
	
	/** 
	 * 动态所属标签或话题
	 **/
	public $tags;
	
	/** 
	 * 请求的唯一标识，防止同一请求多次访问。若重复会返回错误:需要uuid
	 **/
	public $uuid;	
}
?>
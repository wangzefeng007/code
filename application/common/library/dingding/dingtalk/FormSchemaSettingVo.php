<?php 
namespace addons\csmding\library\dingtalk;
//<?php

/**
 * 设置
 * @author auto create
 */
class FormSchemaSettingVo
{
	
	/** 
	 * 表单类型
	 **/
	public $biz_type;
	
	/** 
	 * 创建时间
	 **/
	public $create_time;
	
	/** 
	 * 填表截止时间
	 **/
	public $end_time;
	
	/** 
	 * 是否循环填表
	 **/
	public $form_type;
	
	/** 
	 * 填表周期，周一到周日分别用1-7表示
	 **/
	public $loop_days;
	
	/** 
	 * 填表时间
	 **/
	public $loop_time;
	
	/** 
	 * 应填人数
	 **/
	public $should_participation_cnt;
	
	/** 
	 * 填表是否终止的标记
	 **/
	public $stop;	
}
?>
<?php 
namespace addons\csmding\library\dingtalk;
//<?php

/**
 * 2C订单列表
 * @author auto create
 */
class OpenApiSalesOrderCustomInfoDto
{
	
	/** 
	 * 局定需求id
	 **/
	public $biz_id_customer_order;
	
	/** 
	 * 2C订单下单时间
	 **/
	public $gmt_order_create;
	
	/** 
	 * 2C订单计划交期时间
	 **/
	public $gmt_planned_delivery;
	
	/** 
	 * 2C订单制造域计划完成时间
	 **/
	public $gmt_planned_production_finished;
	
	/** 
	 * 图片链接
	 **/
	public $img_url;
	
	/** 
	 * 对应生产订单id
	 **/
	public $product_order_id;
	
	/** 
	 * 数量
	 **/
	public $quantity;
	
	/** 
	 * 尺码ID
	 **/
	public $size_id;
	
	/** 
	 * 尺码名称
	 **/
	public $size_name;
	
	/** 
	 * 是否跳过定制（印空花）
	 **/
	public $skip_customized;	
}
?>
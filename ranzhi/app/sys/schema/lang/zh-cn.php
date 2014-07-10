<?php
/**
 * The schema module zh-cn file of RanZhi.
 *
 * @copyright   Copyright 2013-2014 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     LGPL
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     schema
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
$lang->schema->common   = '导入模板';
$lang->schema->create   = '创建模板';
$lang->schema->edit     = '编辑模板';

$lang->schema->name     = '模板名称';
$lang->schema->feeRow   = '手续费为一条记录';

$lang->schema->placeholder = new stdclass();
$lang->schema->placeholder->common = '填写对账单对应到该字段的列，如：A';
$lang->schema->placeholder->type   = '填写“收入/支出”所对应的列';
$lang->schema->placeholder->date   = '填写“付款时间”所对应的列';
$lang->schema->placeholder->desc   = '账目备注，可以填写多列，用,隔开，如：I,O';
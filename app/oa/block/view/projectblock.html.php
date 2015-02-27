<?php
/**
 * The project block view file of block module of RanZhi.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv11.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     block
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<table class='table table-data table-hover block-project'>
  <tr>
    <th class='text-left'><?php echo $lang->project->name;?></th>
    <th class='text-center w-80px'><?php echo $lang->block->doneTask;?></th>
    <th class='text-center w-80px'><?php echo $lang->block->waitTask;?></th>
    <th class='text-center w-80px'><?php echo $lang->block->rate;?></th>
  </tr>
  <?php foreach($projects as $id => $project):?>
  <?php $appid = ($this->get->app == 'sys' and isset($_GET['entry'])) ? "class='app-btn text-center' data-id={$this->get->entry}" : "class='text-center'"?>
  <tr data-url='<?php echo $this->createLink('oa.task', 'browse', "projectID=$id"); ?>' <?php echo $appid?>>
    <td class='text-left'><?php echo $project->name;?></td>
    <td><?php echo $project->done;?></td>
    <td><?php echo $project->wait;?></td>
    <td><?php echo $project->rate;?></td>
  </tr>
  <?php endforeach;?>
</table>
<script>$('.block-project').dataTable();</script>

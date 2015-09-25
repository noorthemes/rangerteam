<?php
/**
 * The privilege view file of project module of RanZhi.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv11.html)
 * @author      chujilu<chujilu@cnezsoft.com>
 * @package     project 
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../../sys/common/view/header.modal.html.php';?>
<?php include '../../../sys/common/view/kindeditor.html.php';?>
<?php include '../../../sys/common/view/datepicker.html.php';?>
<?php include '../../../sys/common/view/chosen.html.php';?>
<form method='post' id='ajaxForm' action='<?php echo inlink('privilege', "projectID={$project->id}")?>' class='form-inline'>
  <table class='table-form w-p90'>
    <tr>
      <th class='w-p20'><?php echo $lang->project->acl;?></th>
      <td colspan='2'><?php echo nl2br(html::radio('acl', $lang->project->aclList, $project->acl, "onclick='updateChecked();'", 'block'));?></td>
    </tr>  
    <tr id='whitelistBox'>
      <th><?php echo $lang->project->whitelist;?></th>
      <td colspan='2'><?php echo html::checkbox('whitelist', $groups, $project->whitelist, "onChange='updateChecked();'");?></td>
    </tr>
    <tr id='viewListBox'>
      <th><?php echo $lang->project->viewTask;?></th>
      <td colspan='2'>
        <?php foreach($groupUsers as $groupID => $groupUser):?>
          <?php foreach($groupUser as $account => $realname):?>
            <?php $class  = "group-$groupID";?>
            <?php $class .= in_array($account, $project->members) ? ' in-team' : '';?>
            <label class='checkbox <?php echo $class?>' id='viewuser<?php echo $account?>'>
              <input type='checkbox' name='viewList[]' value='<?php echo $account?>' <?php echo in_array($account, $project->viewList) ? "checked='checked'" : ''?> onChange='updateChecked();' />
              <?php echo $realname;?>
            </label>
          <?php endforeach;?>
        <?php endforeach;?>
      </td>
    </tr>  
    <tr id='editListBox'>
      <th><?php echo $lang->project->editTask;?></th>
      <td colspan='2'>
        <?php foreach($groupUsers as $groupID => $groupUser):?>
          <?php foreach($groupUser as $account => $realname):?>
            <?php $class  = "group-$groupID";?>
            <?php $class .= in_array($account, $project->members) ? ' in-team' : '';?>
            <label class='checkbox <?php echo $class?>' id='viewuser<?php echo $account?>'>
              <input type='checkbox' name='editList[]' value='<?php echo $account?>' <?php echo in_array($account, $project->editList) ? "checked='checked'" : ''?> onChange='updateChecked();' />
              <?php echo $realname;?>
            </label>
          <?php endforeach;?>
        <?php endforeach;?>
      </td>
    </tr>  
    <tr><th></th><td><?php echo html::submitButton();?></td></tr>
  </table>
</form>
<?php include '../../../sys/common/view/footer.modal.html.php';?>
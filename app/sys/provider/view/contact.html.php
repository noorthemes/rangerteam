<?php
/**
 * The contact list file of customer module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     customer
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
?>
<?php include '../../../sys/common/view/header.modal.html.php';?>
<table class='table table-bordered table-data'>
  <tr class='text-center'>
    <th class='w-50px'><?php echo $lang->contact->id;?></th>
    <th class='w-80px'><?php echo $lang->contact->realname;?></th>
    <th class='w-200px'><?php echo $lang->contact->email;?></th>
    <th class='w-110px'><?php echo $lang->contact->phone;?></th>
    <th class='w-80px'><?php echo $lang->contact->qq;?></th>
    <th class='w-100px'><?php echo $lang->actions;?></th>
    <th class='w-70px text-middle' rowspan='<?php echo count($contacts) + 1;?>'>
      <?php commonModel::printLink('provider', 'linkContact', "providerID=$providerID", $lang->create, "class='loadInModal btn btn-primary' title='{$lang->provider->linkContact}'")?>
    </th>
  </tr>
  <?php foreach($contacts as $contact):?>
  <tr class='text-center'>
    <td><?php echo $contact->id;?></td>
    <td>
      <?php
      echo $contact->realname;
      if($contact->maker) echo " ({$lang->resume->maker})";
      ?>
    </td>
    <td><?php echo $contact->email;?></td>
    <td><?php echo $contact->phone . ' ' . $contact->mobile;?></td>
    <td><?php echo $contact->qq;?></td>
    <td>
      <?php commonModel::printLink('crm.contact', 'edit', "contactID=$contact->id", $lang->edit, "class='loadInModal'");?>
      <?php commonModel::printLink('crm.contact', 'delete', "contactID=$contact->id", $lang->delete, "class='deleter'");?>
    </td>
  </tr>
  <?php endforeach;?>
</table>
<?php include '../../../sys/common/view/footer.modal.html.php';?>

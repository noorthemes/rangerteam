<?php
/**
 * The header mobile view of common module of RanZhi.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Hao Sun <sunhao@cnezsoft.com>
 * @package     common 
 * @version     $Id: header.lite.html.php 3299 2015-12-02 02:10:06Z daitingting $
 * @link        http://www.ranzhico.com
 */

if($extView = $this->getExtViewFile(__FILE__)){include $extView; return helper::cd();}
$bodyClass = 'with-appbar-top with-appnav-top';
include 'm.header.lite.html.php';
?>

<header class='appbar heading primary affix dock-top dock-auto'>
  <a class='title' data-display='dropdown' data-target='#appMenu' data-backdrop='true' data-placement='beside-bottom-start'><?php echo $lang->ranzhi ?></a>
  <nav class='nav'>
    <a data-target='#userMenu' data-backdrop='true' data-display data-placement='beside-bottom-end' class='has-padding-sm'>
      <?php commonModel::printUserAvatar('circle');?>
    </a>
  </nav>
</header>

<div id='appMenu' class='list layer hidden fade dock-top dock-left'>
  <a href='##' class='item'>TEST</a>
  <a href='##' class='item'>TEST2</a>
  <a href='##' class='item'>TEST3</a>
</div>

<div id='userMenu' class='list compact layer hidden fade dock-top dock-right'>
  <a class='item multi-lines primary-pale'>
    <?php commonModel::printUserAvatar('circle');?>
    <div class='content'>
      <div class='title'><?php echo empty($app->user->realname) ? ('@' . $app->user->account) : $app->user->realname ?></div>
      <div class='subtitle'><?php echo $lang->user->profile ?></div>
    </div>
  </a>
  <div class='divider no-margin'></div>
  <div class='item'>
    <i class='icon icon-globe muted'></i>
    <div class="content">
      <nav class='nav lang-menu dock'>
        <?php foreach($config->langs as $key => $value):?>
          <a data-value='<?php echo $key; ?>'<?php if($key === $this->app->getClientLang()) echo ' class="active"' ?>><?php echo $value; ?></a>
        <?php endforeach;?>
      </nav>
    </div>
  </div>
  <div class='divider no-margin'></div>
  <a class='item' href='<?php echo $this->createLink('misc', 'about');?>' data-display='modal' data-placement='bottom'><i class='icon icon-info-sign muted'></i> <span class='title'><?php echo $lang->index->about?></span></a>
  <div class='divider no-margin'></div>
  <?php echo html::a($this->createLink('user', 'logout'), "<i class='icon icon-signout muted'></i> <span class='title'>{$lang->logout}</span>", "class='item'")?>
</div>
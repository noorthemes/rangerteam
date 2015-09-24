<?php
/**
 * The control file of todo module of RanZhi.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv11.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     todo
 * @version     $Id: control.php 4976 2013-07-02 08:15:31Z wyd621@gmail.com $
 * @link        http://www.ranzhico.com
 */
class todo extends control
{
    /**
     * Construct function, load model of date.
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->app->loadClass('date');
        $this->loadModel('task');
        $this->loadModel('order', 'crm');
        $this->loadModel('customer', 'crm');
        $this->loadModel('attend', 'oa');
        $this->loadModel('leave', 'oa');
    }

    /**
     * calendar view.
     * 
     * @param  string $date 
     * @access public
     * @return void
     */
    public function calendar($date = '')
    {
        if($date == '') $date = date('Ymd');
        $account = $this->app->user->account;
        $todoList['custom'] = $this->todo->getList('future', $account);
        $todoList['task'] = $this->task->getUserTaskPairs($account, 'wait,doing');

        /* Get order data. */
        $toduList = array();
        $todoList['order'] = array();
        $orderList = $this->order->getList('thismonth');
        foreach($orderList as $order) $todoList['order'][$order->id] = $order->title; 

        $customerList = $this->customer->getList('thismonth');
        $todoList['customer'] = array();
        foreach($customerList as $customer) $todoList['customer'][$customer->id] = $customer->name;

        /* Get attend and leave data. */
        $deptList   = $this->loadModel('tree')->getDeptManagedByMe($account);
        $attendList = array();
        $leaveList  = array();
        if(!empty($deptList)) 
        {
            $dept = join(',', array_keys($deptList));
            $attendList = $this->attend->getByDept($dept, $startDate = '', $endDate = '', 'wait');
            $leaveList  = $this->leave->getList(date('Y'), date('m'), '', $dept, 'wait');
        }
        $users = $this->loadModel('user')->getPairs();

        $todoList['attend'] = array();
        foreach($attendList as $deptKey => $attendDept)
        {
            foreach($attendDept as $accountKey => $attendUser)
            {
                foreach($attendUser as $attend)
                {
                    $todoList['attend'][$attend->id] = $deptList[$deptKey]->name . '/' . $users[$accountKey] . '(' . $attend->date . ')';
                }
            }
        }

        $todoList['leave'] = array();
        foreach($leaveList as $leave)
        {
            $todoList['leave'][$leave->id] = $leave->realname . '(' . $leave->begin . ')[' . $this->lang->leave->typeList[$leave->type] . ']';
        }

        $this->view->title    = $this->lang->todo->calendar;
        $this->view->date     = $date;
        $this->view->data     = $this->todo->getCalendarData($date);
        $this->view->todoList = $todoList;
        $this->display();
    }

    /**
     * Create a todo.
     * 
     * @param  string|date $date 
     * @param  string      $account 
     * @access public
     * @return void
     */
    public function create($date = 'today', $account = '')
    {
        if($date == 'today') $date = date::today();
        if($account == '')   $account = $this->app->user->account;
        if(!empty($_POST))
        {
            $todoID = $this->todo->create($date, $account);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->loadModel('action')->create('todo', $todoID, 'opened');
            $date = str_replace('-', '', $this->post->date);
            if($date == '')
            {
                $date = 'future'; 
            }
            else if($date == date('Ymd'))
            {
                $date = 'today'; 
            }
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('todo', 'calendar', "date=$date")));
        }

        $this->view->title      = $this->lang->todo->common . $this->lang->colon . $this->lang->todo->create;
        $this->view->date       = strftime("%Y-%m-%d", strtotime($date));
        $this->view->times      = date::buildTimeList($this->config->todo->times->begin, $this->config->todo->times->end, $this->config->todo->times->delta);
        $this->view->time       = date::now();
        $this->display();      
    }

    /**
     * Batch create todo
     * 
     * @param  string $date 
     * @access public
     * @return void
     */
    public function batchCreate($date = 'today')
    {
        if($date == 'today') $date = date(DT_DATE1, time());
        if(!empty($_POST))
        {
            $this->todo->batchCreate();
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            /* Locate the browser. */
            $date = str_replace('-', '', $this->post->date);
            if($date == '')
            {
                $date = 'future'; 
            }
            else if($date == date('Ymd'))
            {
                $date= 'today'; 
            }
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('todo', 'calendar', "date=$date")));
        }

        $this->view->title      = $this->lang->todo->common . $this->lang->colon . $this->lang->todo->batchCreate;
        $this->view->date       = (int)$date == 0 ? $date : date('Y-m-d', strtotime($date));
        $this->view->times      = date::buildTimeList($this->config->todo->times->begin, $this->config->todo->times->end, $this->config->todo->times->delta);
        $this->view->time       = date::now();

        $this->display();
    }

    /**
     * Edit a todo.
     * 
     * @param  int    $todoID 
     * @access public
     * @return void
     */
    public function edit($todoID)
    {
        if(!empty($_POST))
        {
            $changes = $this->todo->update($todoID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('todo', $todoID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('todo', 'calendar', "date={$this->post->date}")));
        }

        /* Judge a private todo or not, If private, die. */
        $todo = $this->todo->getById($todoID);
        if($todo->private and $this->app->user->account != $todo->account) die('private');
       
        if($todo->date != '00000000') $todo->date = strftime("%Y-%m-%d", strtotime($todo->date));
        $this->view->title      = $this->lang->todo->edit;
        $this->view->position[] = $this->lang->todo->common;
        $this->view->position[] = $this->lang->todo->edit;
        $this->view->times      = date::buildTimeList($this->config->todo->times->begin, $this->config->todo->times->end, $this->config->todo->times->delta);
        $this->view->todo       = $todo;
        $this->display();
    }

    /**
     * View a todo. 
     * 
     * @param  int    $todoID 
     * @param  string $from     my|company
     * @access public
     * @return void
     */
    public function view($todoID, $from = 'company')
    {
        $todo = $this->todo->getById($todoID, true);
        if(!$todo) $this->locate($this->createLink('todo', 'calendar'));

        $this->view->title      = "{$this->lang->todo->common} #$todo->id $todo->name";
        $this->view->todo       = $todo;
        $this->view->times      = date::buildTimeList($this->config->todo->times->begin, $this->config->todo->times->end, $this->config->todo->times->delta);
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->actions    = $this->loadModel('action')->getList('todo', $todoID);
        $this->view->from       = $from;

        $this->display();
    }

    /**
     * Delete a todo.
     * 
     * @param  int    $todoID 
     * @access public
     * @return void
     */
    public function delete($todoID)
    {
        $this->dao->delete()->from(TABLE_TODO)->where('id')->eq($todoID)->exec();
        $this->loadModel('action')->create('todo', $todoID, 'erased');
        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
        $this->send(array('result' => 'success', 'locate' => $this->createLink('todo', 'calendar')));
    }

    /**
     * Finish a todo.
     * 
     * @param  int    $todoID 
     * @access public
     * @return void
     */
    public function finish($todoID)
    {
        $todo = $this->todo->getById($todoID);
        if($todo->status != 'done') $this->todo->finish($todoID);
        if($todo->type != 'custom')
        {
            $entry = 'oa';
            if($todo->type == 'order' or $todo->type == 'customer') $entry = 'crm';
            $confirmNote = sprintf($this->lang->todo->confirmTip, $this->lang->{$todo->type}->common, $todo->id);
            $confirmURL  = $this->createLink("{$entry}.{$todo->type}", 'view', "id=$todo->idvalue", 'html');
            if($todo->type == 'attend' or $todo->type == 'leave') $confirmURL = $this->createLink("{$entry}.{$todo->type}", 'browseReview', "", 'html');
            $this->send(array('result' => 'success', 'confirm' => array('note' => $confirmNote, 'confirm' => $confirmURL, 'entry' => $entry)));
        }
        $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('todo', 'calendar', "date=$todo->date")));
    }

    /**
     * AJAX: get actions of a todo. for web app.
     * 
     * @param  int    $todoID 
     * @access public
     * @return void
     */
    public function ajaxGetDetail($todoID)
    {
        $this->view->actions = $this->loadModel('action')->getList('todo', $todoID);
        $this->display();
    }
}
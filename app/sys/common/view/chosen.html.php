<?php
/**
 * The chosen view of common module of RanZhi.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv11.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     common 
 * @version     $Id$
 * @link        http://www.ranzhico.com
 */
if($extView = $this->getExtViewFile(__FILE__)){include $extView; return helper::cd();}
css::import($jsRoot . 'jquery/chosen/min.css');
js::import($jsRoot . 'jquery/chosen/min.js');
?>

<script>
window.chosenDefaultOptions = {no_results_text: '<?php echo $lang->noResultsMatch;?>', disable_search_threshold: 1, search_contains: true, width: '100%'};
$(document).ready(function()
{
    $(".chosen").chosen(chosenDefaultOptions);
});
</script>

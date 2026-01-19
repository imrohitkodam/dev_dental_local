<?php
defined('_JEXEC') or die;
?>
<form id="edocmantags" action="index.php?option=com_edocman&view=search" method="post">
    <?php
        echo $dropdown;
    ?>
    <input type="hidden" name="option" value="com_edocman">
    <input type="hidden" name="view" value="search">
    <?php echo JHtml::_( 'form.token' ); ?>
</form>


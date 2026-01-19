<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

if (!(defined('_JEXEC') || defined('ABSPATH'))) {     die('Restricted access'); };
?>
<style>
    .sourcecoast ul.nav-stacked li {
        padding: 0;
    }

    .sourcecoast ul.nav-tabs > .active > a {
        background-color: #0088cc;
        color: #fff;
    }
</style>
<div class="span2 pull-left autotune">
    <h3>Steps</h3>
    <ul class="nav nav-tabs nav-stacked">
        <?php

        $currentLayout = $this->getLayout();
        foreach ($this->autotuneSteps as $step)
        {
            $class = ($step['task'] == $currentLayout) ? 'class="active"' : '';
            echo '<li ' . $class . '>';
            echo '<a href="index.php?option=com_jfbconnect&view=autotune&task=' . $step['task'] . '">' . $step['name'] . '</a></li>';
        }
        ?>
    </ul>
</div>
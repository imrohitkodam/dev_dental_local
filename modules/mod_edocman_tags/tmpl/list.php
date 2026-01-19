<?php
defined('_JEXEC') or die;
if (count($tags)) {
    ?>
    <ul class="tags <?php echo 'tags_'.$moduleclass; ?>">
        <?php
        foreach ($tags as $row)
        {
            $size = rand(8,16);
            ?>
            <li>
                <a href="<?php echo JRoute::_('index.php?option=com_edocman&view=search&filter_tag='.urlencode($row->tag)) ?>"
                    style="color:#CCCCCC;font-size:<?php echo $size."px"; ?>"
                >
                    <?php echo $row->tag; ?>
                </a>
            </li>
        <?php
        }
        ?>
    </ul>
<?php
}
?>
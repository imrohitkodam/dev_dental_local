<?php
$config      = $this->config;
$bootstrapHelper = $this->bootstrapHelper;
$rowFluidCss = $bootstrapHelper->getClassMapping('row-fluid');
$span12Css   = $bootstrapHelper->getClassMapping('span12');
$span6Css    = $bootstrapHelper->getClassMapping('span6');
$span4Css    = $bootstrapHelper->getClassMapping('span4');
$span3Css    = $bootstrapHelper->getClassMapping('span3');
$span8Css    = $bootstrapHelper->getClassMapping('span8');
$span9Css    = $bootstrapHelper->getClassMapping('span9');
$relates     = $this->related_items;
?>
<div class="<?php echo $rowFluidCss; ?>">
    <div class="<?php echo $span12Css; ?> relateddocuments">
        <div class="<?php echo $rowFluidCss; ?>">
            <div class="<?php echo $span12Css; ?>">
                <h3>
                    <?php
                    echo JText::_('EDOCMAN_RELATED_DOCUMENTS');
                    ?>
                </h3>
            </div>
        </div>
        <div class="<?php echo $rowFluidCss; ?>">
            <?php
            $j = 0;
            $Itemid = JFactory::getApplication()->input->getInt('Itemid');
            foreach ($relates as $item){
                $item->data     = new EDocman_File($item->id,$item->filename, $config->documents_path);
                $url            = JRoute::_('index.php?option=com_edocman&view=document&id='.$item->id.'&Itemid='.$Itemid);
                $imgSrc         = '';
                if ($item->image && JFile::exists(JPATH_ROOT.'/media/com_edocman/document/thumbs/'.$item->image))
                {
                    $imgSrc     = JUri::root(true).'/media/com_edocman/document/thumbs/'.$item->image ;
                }
                else
                {
                    $imgSrc     = JUri::root(true).'/components/com_edocman/assets/images/icons/thumbs/'.$item->data->ext.'.png';
                }
                ?>
                <div class="<?php echo $span6Css; ?>">
                    <div class="<?php echo $rowFluidCss; ?>">
                        <div class="<?php echo $span3Css?>">
                            <?php
                            if($imgSrc != ""){
                                ?>
                                <img src="<?php echo $imgSrc; ?>" alt="<?php echo $item->title; ?>" class="edocman-thumb-left img-polaroid" />
                                <?php
                            }
                            ?>
                        </div>
                        <div class="<?php echo $span9Css?>">
                            <strong>
                            <a href="<?php echo $url?>">
                                <?php echo $item->title;?>
                            </a>
                            </strong>
                            <div class="clearfix"></div>
                            <?php
                            if (!$item->short_description)
                            {
                                $item->short_description = $item->description;
                            }
                            $description = $item->short_description;
                            if((int)$config->number_words > 0){
                                $descriptionArr = explode(" ",$description);
                                if(count($descriptionArr) > (int)$config->number_words){
                                    for($d = 0;$d < (int)$config->number_words - 1;$d++){
                                        echo $descriptionArr[$d]." ";
                                    }
                                    echo "..";
                                }else{
                                    echo $description;
                                }
                            }else{
                                echo $description;
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                $j++;
                if($j == 2){
                    $j = 0;
                    ?>
                    </div>
                    <div class="<?php echo $rowFluidCss; ?>">
                    <?php
                }
            }
            ?>
        </div>
    </div>
</div>
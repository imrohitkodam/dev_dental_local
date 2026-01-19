<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
		
JToolBarHelper::title(   JText::_( 'EDOCMAN_MANAGEMENT'), 'generic.png' );
JToolBarHelper::save('save');
JToolBarHelper::cancel();		
?>
<form action="index.php?option=com_edocman&view=language" method="post" name="adminForm" id="adminForm">
	<table width="100%">
        <tr>
            <td width="40%" style="text-align: left;">
                <?php echo JText::_( 'EDOCMAN_FILTER' ); ?>:
                <input type="text" name="search" id="search" value="<?php echo $this->search;?>" class="text_area search-query" onchange="document.adminForm.submit();" />
                <button onclick="this.form.submit();" class="btn"><?php echo JText::_( 'EDOCMAN_GO' ); ?></button>
                <button onclick="document.getElementById('search').value='';this.form.submit();" class="btn"><?php echo JText::_( 'Reset' ); ?></button>
            </td>
            <td width="60%" style="text-align:right;">
                &nbsp;<?php echo JText::_("EDOCMAN_SELECT_LANGUAGE")?>: &nbsp;
                <?php echo $this->lists['langs'];?>
                <?php echo JText::_("EDOCMAN_SELECT_SIDE")?>: &nbsp;
                <?php echo $this->lists['site'];?>
            </td>
        </tr>
	</table>			
	<table class="adminlist table table-bordered">
		<thead>		
			<tr>
                <th class="key" style="width:5%; text-align: center;background-color:#671E62;color:white;""><?php echo JText::_('#'); ?></td>
				<th class="key" style="width:20%; text-align: left;background-color:#671E62;color:white;">Key</th>
				<th class="key" style="width:40%; text-align: left;background-color:#671E62;color:white;">Original</th>
				<th class="key" style="width:40%; text-align: left;background-color:#671E62;color:white;">Translation</th>
			</tr>	
		</thead>
        <tfoot>
        <tr>
            <td colspan="4" style="text-align:center;">
                <?php
                echo $this->pagNav->getListFooter();
                ?>
            </td>
        </tr>
        </tfoot>
		<tbody>	
		<?php
            $j = 0;
			$original = $this->trans['en-GB'][$this->item] ;
			$trans = $this->trans[$this->lang][$this->item] ;

            $search = @strtolower($this->search);
			foreach ($original as  $key=>$value) {
                $j++;
                $i = $j - 1;
                $str[] = $key;
                $show = true ;
                if (isset($tran[$key])) {
                    $translatedValue = $tran[$key];
                    $missing = false ;
                } else {
                    $translatedValue = $value;
                    $missing = true ;
                }
                if ($search != "") {
                    if (strpos(JString::strtolower($key), $search) === false && strpos(JString::strtolower($value), $search) === false) {
                        $show = false ;
                    }
                }
                if ($show) {
                    if($j % 2 == 0){
                        $bgcolor = "#efefef";
                    }else{
                        $bgcolor = "#ffffff";
                    }
                    ?>
                    <tr>
                        <td class="key" style="text-align:center;background-color:<?php echo $bgcolor;?>">
                            <?php echo $j + $this->pagNav->limitstart;?>.
                        </td>
                        <td class="key" style="text-align: left;background-color:<?php echo $bgcolor;?>"><?php echo $key; ?></td>
                        <td style="text-align: left;background-color:<?php echo $bgcolor;?>"><?php echo $value; ?></td>
                        <td style="text-align: left;background-color:<?php echo $bgcolor;?>">
                            <?php
                                if (isset($trans[$key]))
                                {
                                    $translatedValue = $trans[$key];
                                    $missing = false ;
                                }
                                else
                                {
                                    $translatedValue = $value;
                                    $missing = true ;
                                }
                            ?>
							<input type="hidden" name="keys[]" value="<?php echo $key; ?>" />
							<input type="hidden" name="items[]" value="<?php echo $i;?>" />
							<input type="text" id="item_<?php echo $i?>" name="item_<?php echo $i?>" value="<?php echo $translatedValue; ; ?>" class="input-xlarge" />
                            <?php
                                if ($missing) {
                                ?>
                                    <span style="color:red;">*</span>
                                <?php
                                }
                            ?>
                        </td>
                    </tr>
                    <?php }else{ ?>
                    <tr style="display: none;">
                        <td colspan="4">
                            <input type="hidden" name="keys[]" value="<?php echo $key; ?>" />
                            <input type="hidden" name="<?php echo $key; ?>"  value="<?php echo $translatedValue; ; ?>" />
                        </td>
                    </tr>
                    <?php } ?>
			<?php
			}
		?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="com_edocman" />	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="item" value="com_edocman" />			
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
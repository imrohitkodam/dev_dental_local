<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>


<table class="table table-striped" id="articleList">
	<thead>
		<tr>
			<th width="1%">
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
			</th>
												<th>
						<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
					</th>
																																																																																		
												
																					<th width="5%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_FSSADD_FORM_FSSADD_CANNED_CATEGORY', 'a.category', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																																																																																																																																																																																																							
												
																					<th width="170px" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_FSSADD_FORM_FSSADD_CANNED_FIELD_COUNT', 'a.field_count', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																																																							
												
																					<th width="5%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_FSSADD_FORM_FSSADD_CANNED_SHOWFOR', 'a.showfor', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																					<th class="order" style="width:110px;">
						<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
						<?php if ($saveOrder) :?>
							<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'canneds.saveorder'); ?>
						<?php endif; ?>
					</th>
																<th width="5%">
						<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
					</th>
																<th width="10%">
						<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
					</th>
																<th width="5%">
						<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
					</th>
																<th width="1%" class="nowrap">
						<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
								

		</tr>
	</thead>

	<tbody>

	<?php foreach ($this->items as $i => $item) :
				$canCreate	= 1;//$user->authorise('core.create',		'com_fsj_fssadd.category.'.$item->catid);
		$canEdit	= 1;//$user->authorise('core.edit',			'com_fsj_fssadd.article.'.$item->id);
		$canCheckin	= 1;//$user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
		$canEditOwn	= 1;//$user->authorise('core.edit.own',		'com_fsj_fssadd.article.'.$item->id) && $item->created_by == $userId;
		$canChange	= 1;//$user->authorise('core.edit.state',	'com_fsj_fssadd.article.'.$item->id) && $canCheckin;
			
		if (array_key_exists("debug",$_GET))
			print_p($item);
		?>
								<tr class="row<?php echo $i % 2; ?>">
						
			<td class="center">
				<?php echo JHtml::_('grid.id', $i, $item->id); ?>
			</td>
			
												<td>
				
								
								
														<?php if ($canEdit || $canEditOwn) : ?>
									<a 
																			href="<?php echo JRoute::_('index.php?option=com_fsj_fssadd&task=canned.edit&tmpl='.JRequest::getVar('tmpl').'&id='.$item->id);?>">
														
									<?php echo $this->escape($item->title); ?></a>
								<?php else : ?>
									<?php echo $this->escape($item->title); ?>
								<?php endif; ?>
								
						
													<span class="small">
								<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
							</span>
						
											</td>
							
				
												
																																																																	
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjstring'))
									{
										$category = new JFormFieldfsjstring();
										$category->fsjstring = json_decode('{"size":60,"maxlength":250,"tmpl":""}');
										echo $category->AdminDisplay(!empty($item->category) ? $item->category : '', 'category', $item, $i);
									} else {
										echo $item->category;
									}
								?>
															</td>
												
						
												
						
																																																																																																									
												
																																																																																																																																																																	
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjcount'))
									{
										$field_count = new JFormFieldfsjcount();
										$field_count->fsjcount = json_decode('{"field":"canned_id","table":"fssadd_canned_field","display":"%COUNT% fields"}');
										echo $field_count->AdminDisplay(!empty($item->field_count) ? $item->field_count : '', 'field_count', $item, $i);
									} else {
										echo $item->field_count;
									}
								?>
															</td>
												
						
												
						
									
												
																																																																																																																	
						
													<td class="center small">
								<?php 
									$options = array();
																		$options['0'] = JText::_('All');
																		$options['1'] = JText::_('Users');
																		$options['2'] = JText::_('Handlers');
																		$value = $item->showfor;
									if (isset($options[$value]))
										$value = $options[$value];
								?>
								<?php echo $value; ?>
							</td>
												
						
												
						
																																																									
												<td class="order small" style="width:110px;">
													<?php if ($canChange) : ?>
								<?php if ($saveOrder) :?>
									<?php if (strtolower($listDirn) == 'asc') : ?>
										<span><?php echo $this->pagination->orderUpIcon($i, true, 'canneds.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
										<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'canneds.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
										<?php elseif (strtolower($listDirn) == 'desc') : ?>
										<span><?php echo $this->pagination->orderUpIcon($i, true, 'canneds.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
										<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'canneds.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
									<?php endif; ?>
								<?php endif; ?>
								<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
								<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
							<?php else : ?>
								<?php echo $item->ordering; ?>
							<?php endif; ?>
											</td>
				
												<td class="center">
													<?php echo JHtml::_('jgrid.published', $item->state, $i, 'canneds.', $canChange); ?>
											</td>
				
												<td class="center small">
						<?php echo $this->escape($item->access_level); ?>
					</td>
				
												<td class="center small">
						<?php if ($item->language=='*'):?>
							<?php echo JText::alt('JALL', 'language'); ?>
						<?php else:?>
							<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
						<?php endif;?>
					</td>
				
												<td class="center">
						<?php echo (int) $item->id; ?>
					</td>
				
					
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php echo $this->pagination->getListFooter(); ?>

	
      
        <iframe src='index.php?option=com_fsj_fssadd&view=canned_config&tmpl=component&type=inline' width='600' height='600' frameBorder="0" scrolling="no" />
      
    	
	
	
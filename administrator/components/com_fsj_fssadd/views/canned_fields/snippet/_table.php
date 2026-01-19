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
																																																																																		
												
																					<th width="40%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_FSSADD_FORM_FSSADD_CANNED_FIELD_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
								</th>
												
						
																																																			
												
														<th width="1%" class="nowrap">	
									<?php echo JHtml::_('searchtools.sort', 'FSJ_FSSADD_FORM_FSSADD_CANNED_FIELD_CANNED_ID', 'lf33', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																															
												
																					<th width="5%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_FSSADD_FORM_FSSADD_CANNED_FIELD_TAB', 'a.tab', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																															
												
																					<th width="5%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_FSSADD_FORM_FSSADD_CANNED_FIELD_FIELDTYPE', 'a.fieldtype', $listDirn, $listOrder); ?>
								</th>
												
						
																																																									<th class="order" style="width:110px;">
						<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
						<?php if ($saveOrder) :?>
							<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'canned_fields.saveorder'); ?>
						<?php endif; ?>
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
																			class='parent_popup'
										href="<?php echo JRoute::_('index.php?option=com_fsj_fssadd&task=canned_field.edit&popup=1&tmpl='.JRequest::getVar('tmpl').'&id='.$item->id);?>">
														
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
									if (class_exists('JFormFieldfsjtext'))
									{
										$description = new JFormFieldfsjtext();
										$description->fsjtext = json_decode('{"fullfield":"","introfield":"","readmore":0,"pagebreak":0,"image":1,"width":550,"height":400,"cols":60,"rows":20,"strip":0,"maxsize":0,"preformat":0,"divwidth":0,"divheight":0,"rawtoggle":"","htmledit":1,"style":""}');
										echo $description->AdminDisplay(!empty($item->description) ? $item->description : '', 'description', $item, $i);
									} else {
										echo $item->description;
									}
								?>
															</td>
												
						
												
						
																					
												
																	
						
													<td nowrap class="small">
								<?php
									$canned_id = new JFormFieldFSJLookup();
									$canned_id->lookup = json_decode('{"table":"#__fsj_fssadd_canned","field":"id","display":"title","musthave":0,"alias":"l33","fieldalias":"lf33","warning":"","joinfield":"","displayonly":"","inlineedit":0,"nested":0,"options":[],"use_state":0,"state":"","onchange":"","jtext":0,"tmpl":"","or_sql":"","readonly":0}');
									echo $canned_id->AdminDisplay(!empty($item->canned_id) ? $item->canned_id : '', 'canned_id', $item, $i);
								?>
							</td>
												
						
												
						
																																																																					
												
																													
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjstring'))
									{
										$tab = new JFormFieldfsjstring();
										$tab->fsjstring = json_decode('{"size":60,"maxlength":250,"tmpl":""}');
										echo $tab->AdminDisplay(!empty($item->tab) ? $item->tab : '', 'tab', $item, $i);
									} else {
										echo $item->tab;
									}
								?>
															</td>
												
						
												
						
																																																									
												
																																									
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjcftype'))
									{
										$fieldtype = new JFormFieldfsjcftype();
										$fieldtype->fsjcftype = json_decode('{"paramfield":"params","class":"chzn-done testclass"}');
										echo $fieldtype->AdminDisplay(!empty($item->fieldtype) ? $item->fieldtype : '', 'fieldtype', $item, $i);
									} else {
										echo $item->fieldtype;
									}
								?>
															</td>
												
						
												
						
																																													
												<td class="order small" style="width:110px;">
													<?php if ($canChange) : ?>
								<?php if ($saveOrder) :?>
									<?php if (strtolower($listDirn) == 'asc') : ?>
										<span><?php echo $this->pagination->orderUpIcon($i, true, 'canned_fields.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
										<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'canned_fields.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
										<?php elseif (strtolower($listDirn) == 'desc') : ?>
										<span><?php echo $this->pagination->orderUpIcon($i, true, 'canned_fields.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
										<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'canned_fields.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
									<?php endif; ?>
								<?php endif; ?>
								<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
								<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
							<?php else : ?>
								<?php echo $item->ordering; ?>
							<?php endif; ?>
											</td>
				
												<td class="center">
						<?php echo (int) $item->id; ?>
					</td>
				
					
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php echo $this->pagination->getListFooter(); ?>

	
	
	
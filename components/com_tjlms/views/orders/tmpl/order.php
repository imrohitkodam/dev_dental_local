<?php
/**
 * @version    SVN: <svn_id>
 * @package    Tjlms
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die();

/*if user is on payment layout and log out at that time undefined order is is found
in such condition send to home page or provide error msg
*/

$params = JComponentHelper::getParams( 'com_tjlms' );
$user = JFactory::getUser();
$jinput = JFactory::getApplication()->input;
$logedin_user = $user->id;
$session = JFactory::getSession();
$order_currency = $params->get('currency');
$this->comtjlmsHelper = new comtjlmsHelper;
$this->tjlmsCoursesHelper	= new tjlmsCoursesHelper;
$paymentStatus['I']=JText::_('COM_TJLMS_PSTATUS_INITIATED');
$paymentStatus['P']=JText::_('COM_TJLMS_PSTATUS_PENDING');
$paymentStatus['C']=JText::_('COM_TJLMS_PSTATUS_COMPLETED');
$paymentStatus['D']=JText::_('COM_TJLMS_PSTATUS_DECLINED');
$paymentStatus['E']=JText::_('COM_TJLMS_PSTATUS_FAILED');
$paymentStatus['UR']=JText::_('COM_TJLMS_PSTATUS_UNDERREVIW');
$paymentStatus['RF']=JText::_('COM_TJLMS_PSTATUS_REFUNDED');
$paymentStatus['CRV']=JText::_('COM_TJLMS_PSTATUS_CANCEL_REVERSED');
$paymentStatus['RV']=JText::_('COM_TJLMS_PSTATUS_REVERSED');
//$is_zero_amountOrder = $session->get('JT_is_zero_amountOrder');
//$JT_coupon_code = $session->get('JT_coupon_code');

$billinfo = '';

if (isset($this->orderinfo))
{
	$coupon_code = $this->orderinfo[0]->coupon_code;

	if (isset($this->orderinfo[0]->address_type) && $this->orderinfo[0]->address_type == 'BT')
	{
		$billinfo = $this->orderinfo[0];
	}
	else if(isset($this->orderinfo[1]->address_type) && $this->orderinfo[1]->address_type == 'BT')
	{
		$billinfo = $this->orderinfo[1];
	}
}

if (isset($this->orderinfo))
{
	$where = " o.id=" . $this->orderinfo['0']->order_id;

	if ($this->orderinfo['0']->order_id)
	{
		$orderdetails = $this->comtjlmsHelper->getallCourseDetailsByOrder($where);
	}

	$this->orderinfo = $this->orderinfo[0];
}

?>

<div class="">

<?php
if($this->order_authorized == 0)
{
?>
<div class="well" >
	<div class="alert alert-error">
		<span ><?php echo JText::_('COM_TJLMS_ORDER_UNAUTHORISED'); ?> </span>
	</div>
</div>
<?php
	return false;
}

if(isset($this->orderview))
	{
		?>
		<div class="row-fluid">
			<div class="span12 order_print">

				<div class="pull-right">
					<input type="button" class="btn btn-success" onclick="printDiv()" value="<?php echo JText::_('COM_TJLMS_PRINT');?>">
				</div>
			</div>
		</div>
<?php
	}	?>
	<div id="printDiv">
		<div class="row-fluid">
			<hr class="hr hr-condensed"/>
			<!--SHOW COMAPNY DETAILS ON LEFT SIDE-->
			<?php
					$company_name = $params->get('company_name','','STRING');
					$company_address = $params->get('company_address','','STRING');
					$company_vat_no = $params->get('company_vat_no','','STRING');
			?>

			<div class="span8">
				<div class="span3">
						<?php
						if(isset($orderdetails[0]->image))
						{
							//$imagePath = 'media/com_tjlms/course_images/';
							//$imagePath = JRoute::_(JUri::base() . $imagePath . $orderdetails[0]->image, false);
							$imageToUse = $this->tjlmsCoursesHelper->getCourseImage((array)$orderdetails[0],'S_');
							?>
							<img class="img-polaroid com_tjlms_image_w98pc" src="<?php echo $imageToUse;?>">
				<?php	}	?>
				</div>
				<div class="span9 orderCourseTitle">

					<?php $isModal = $jinput->get('tmpl', '', 'STRING'); ?>
						<?php if ($isModal != 'component'): ?>
							<a href="<?php echo $this->comtjlmsHelper->tjlmsRoute('index.php?option=com_tjlms&view=course&id=' . $orderdetails[0]->course_id); ?>">
						<?php endif; ?>
								<span class="order_course"><?php echo $orderdetails[0]->title; ?></span>
						<?php if ($isModal != 'component'): ?>
							</a>
						<?php endif; ?>
				</div>
			</div><!--SPAN 8 ENDS-->
			<div class="tjlms-text-left">
				<div class="span1"></div>
				<div class="pull-left span6 orderalign">
						<div>
							<?php 	echo $company_name;	?>
						</div>
						<div>
							<?php 	echo $company_address;	?>
						</div>
						<div>
							<?php 	echo $company_vat_no;	?>
						</div>
				</div>

				<div class="tjlms-text-right pull-right order-details span5 orderalign" >
					<div>
						<strong><?php echo JText::_('COM_TJLMS_ORDER_ID'); ?></strong>  <?php echo $this->orderinfo->orderid_with_prefix ; ?>
					</div>
					<!--SHOW ORDER STATUS-->
					<div class="row-fluid">
						<strong><?php echo JText::_('COM_JTJLMS_ORDER_PAYMENT_STATUS'); ?></strong>  <?php echo $paymentStatus[$this->orderinfo->status]; ?>
					</div>
					<!--SHOW ORDER STATUS-->
					<div>
						<strong><?php echo JText::_('COM_TJLMS_ORDER_DATE'); ?></strong>  <?php echo $this->orderinfo->cdate ; ?>
					</div>
				</div>
			</div>
			<!--COMPANY DETAILS ENDS-->

			<!--USER INFO STRAT-->
		<?php
			if(!empty($billinfo))
			{
		?>
				<div class="span12">
					<hr class="hr hr-condensed"/>
					<div class="pull-left" >
						<div>
							<?php echo JText::_('COM_TJLMS_TO'); ?>
						</div>
						<div>
							<?php echo $billinfo->firstname.' '.$billinfo->lastname;?>
						</div>
							<?php
							if(!empty($billinfo->vat_number))
								{
									?>
									<div>
										<?php echo $billinfo->vat_number;?>
									</div>
						<?php 	}	?>

						<div>
							<?php echo $billinfo->phone;?>
						</div>
						<div>
							<?php echo $billinfo->user_email;?>
						</div>
						<div>
							<?php echo $billinfo->address.' '.$billinfo->city;?>
						</div>
						<div>
							<?php echo $billinfo->zipcode.' '.$billinfo->state_code.' '.$billinfo->country_code;?>
						</div>
					</div>
				</div>
			<!--USER INFO ENDS-->
		<?php
			}	?>
		</div><!--ROWFLUID ENDS-->


		<!--SUBSCRIPTON PLANS DETAILS START HERE-->
		<div class="row-fluid">
			<hr class="hr hr-condensed"/>
				<div class="span12 "> <!-- plan detail start -->
					<span class="order-plan"><?php echo JText::sprintf('COM_TJLMS_PLAN_INFO',$orderdetails[0]->title); ?></span>
									<div class="table-responsive">
										<table class="table">
											<tr>

												<th class="lms_name" align="left" ><?php echo  JText::_('COM_TJLMS_PRODUCT_NAM'); ?></th>

												<th class="lms_price" align="left"  ><?php echo JText::_('COM_TJLMS_PRODUCT_PRICE'); ?></th>
												<th class="lms_tprice" align="left" ><?php echo JText::_('COM_TJLMS_PRODUCT_TPRICE'); ?></th>
											</tr>


											<?php
												$showoptioncol=0;
												$i=1;
												$order = $this->orderitems[0];
													$totalprice = 0;
													if (!isset($order->price))
													{
														$order->price=0;
													}
												?>
												<tr class="row0">
													<td class="lms_name" ><?php echo $order->order_item_name;?></td>

													<td class="lms_price" >
														<span>
															<?php echo $this->comtjlmsHelper->getFromattedPrice( number_format(($order->price),2),$order_currency);?>
														</span>
													</td>

													<td class="lms_tprice" >
														<span>
															<?php $totalprice = $order->price;
															echo $this->comtjlmsHelper->getFromattedPrice(number_format($totalprice,2),$order_currency); ?>
														</span>
													</td>

												</tr>

												<tr>
													<td colspan="3">&nbsp;</td>
												</tr>
												<tr>
													<?php
													$col = 1;

													if ($showoptioncol==1)
													{
														$col=2;
													}
													?>
													<td colspan="<?php echo $col;?>" > </td>
													<td class="lms_tprice_label" align="left">
														<strong>
															<?php echo JText::_('COM_TJLMS_PRODUCT_TOTAL'); ?>
														</strong>
													</td>
													<td class="lms_tprice" >
														<span id= "cop_discount" >
															<?php echo $this->comtjlmsHelper->getFromattedPrice( number_format($totalprice,2),$order_currency); ?>
														</span>
													</td>
												</tr>
												<!--discount price -->

										<?php
										$coupon_code = trim($coupon_code);
										$total_amount_after_disc = $this->orderinfo->original_amount;

										if ($this->orderinfo->coupon_discount > 0)
										{
											$total_amount_after_disc = $total_amount_after_disc-$this->orderinfo->coupon_discount;
										?>
											<tr>
												<td colspan="<?php echo $col;?>" > </td>
												<td class="lms_tprice_label" align="left">
													<strong>
														<?php echo sprintf(JText::_('COM_TJLMS_PRODUCT_DISCOUNT'),$this->orderinfo->coupon_code); ?>
													</strong>
												</td>
												<td class="lms_tprice" >
													<span id= "coupon_discount" >
														<?php echo $this->comtjlmsHelper->getFromattedPrice(number_format($this->orderinfo->coupon_discount,2),$order_currency);
														?>
													</span>
												</td>
											</tr>
											<!-- total amt after Discount row-->
											<tr class="dis_tr" 	>
												<td colspan = "<?php echo $col;?>"></td>
												<td  class="lms_tprice_label" align="left">
													<strong>
														<?php echo JText::_('COM_TJLMS_NET_AMT_PAY');?>
													</strong>
												</td>
												<td class="lms_tprice" >
													<span id= "total_dis_cop" >
														<?php
															echo $this->comtjlmsHelper->getFromattedPrice(number_format($total_amount_after_disc,2),$order_currency);
														?>
													</span>
												</td>
											</tr>

									<?php
										}

											if(isset($this->orderinfo->order_tax) and $this->orderinfo->order_tax>0)
											{
												$tax_json = $this->orderinfo->order_tax_details;
												$tax_arr = json_decode($tax_json,true);
											?>
												<tr>
													<td colspan="<?php echo $col;?>" > </td>
													<td class="lms_tprice_label" align="left">
														<strong>
															<?php echo JText::_('TAX_AMOUNT'); ?>
														</strong>
															<?php echo $tax_arr['percent']; ?>
													</td>
													<td class="lms_tprice" >
														<span id= "tax_amt" >
															<?php echo $this->comtjlmsHelper->getFromattedPrice(number_format($this->orderinfo->order_tax,2),$order_currency); ?>
														</span>
													</td>
												</tr>
									<?php 	} 	?>

													<tr>
														<td colspan="<?php echo $col;?>" > </td>
														<td class="lms_tprice_label" align="left">
															<strong>
																<?php echo JText::_('COM_TJLMS_ORDER_TOTAL'); ?>
															</strong>
														</td>
														<td class="lms_tprice" >
															<strong>
																<span id="final_amt_pay"	name="final_amt_pay">
																	<?php echo $this->comtjlmsHelper->getFromattedPrice(number_format($this->orderinfo->amount,2),$order_currency); ?>
																</span>
															</strong>
														</td>
													</tr>

									</table>
								</div>
				</div>
		</div>


	</div><!--PRINT DIV ENDS-->

</div>
<script type="text/javascript">

	function printDiv()
	{
		var printContents = document.getElementById('printDiv').innerHTML;
		var originalContents = document.body.innerHTML;

		document.body.innerHTML = printContents;

		window.print();

		document.body.innerHTML = originalContents;
	}
</script>


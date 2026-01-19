<?php
/**
 * @package     Shika
 * @subpackage  com_tjlms
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('bootstrap.framework');
HTMLHelper::_('jquery.framework');

HTMLHelper::stylesheet('media/techjoomla_strapper/bs3/css/bootstrap.css');
HTMLHelper::_('stylesheet', 'components/com_tjlms/bootstrap/css/bootstrap.min.css');
$baseurl = Route::_(Uri::root() . 'index.php');
$rootURL = Uri::root();

if (!empty($this->userbill))
{
	foreach ($this->userbill as &$value)
	{
		$value = $this->escape($value);
	}
}
?>

<script type="text/javascript">
	jQuery(document).ready(function() {
		var DBuserbill="<?php echo (isset($this->userbill->state_code))?$this->userbill->state_code:''; ?>";
		tjlms.billing.init(DBuserbill,"<?php echo Text::_('ADS_BILLIN_SELECT_STATE');?>","<?php echo $rootURL ?>");
		jQuery('#country_mobile_code').val("<?php echo $this->defaultCountryMobileCode; ?>");
		
		// Ensure form fields are populated with user data after login
		if (<?php echo $this->user->id ? 'true' : 'false'; ?>) {
			// Force populate form fields if user data exists
			var userData = <?php echo json_encode($this->userbill); ?>;
			if (userData && typeof userData === 'object') {
				if (userData.firstname) jQuery('#fnam').val(userData.firstname);
				if (userData.lastname) jQuery('#lnam').val(userData.lastname);
				if (userData.user_email) jQuery('#email1').val(userData.user_email);
				if (userData.vat_number) jQuery('#vat_num').val(userData.vat_number);
				if (userData.phone) jQuery('#phon').val(userData.phone);
				if (userData.address) jQuery('#addr').val(userData.address);
				if (userData.city) jQuery('#city').val(userData.city);
				if (userData.zipcode) jQuery('#zip').val(userData.zipcode);
				if (userData.country_code) jQuery('#country').val(userData.country_code);
				if (userData.country_mobile_code) jQuery('#country_mobile_code').val(userData.country_mobile_code);
				
				// Trigger country change to load states if country is set
				if (userData.country_code) {
					setTimeout(function() {
						jQuery('#country').trigger('change');
					}, 100);
				}
			}
		}
	});
</script>

<!-- Start OF billing_info_tab-->
<div id="billing-info" class="tjlms-checkout-steps">
	<form action="<?php echo $this->tjlmsFrontendHelper->tjlmsRoute('index.php?option=com_tjlms&view=buy&course_id=' . $this->course_id); ?>" name="billing_info_form" action="" id="billing_info_form" class="form-validate">

	<!--Start User Details Tab-->
	<?php

	$com_params = ComponentHelper::getParams('com_tjlms');
	$allowSilentRegistration = $com_params->get('allow_silent_registration', 0);
	
	// Debug: Check user status
	$isGuest = !$this->user->id;
	$silentRegEnabled = $allowSilentRegistration;
	
	// Only return false if user is guest AND silent registration is disabled
	if ($isGuest && !$silentRegEnabled)
	{
		return false;
	}
	?>
	<!--End User Details Tab-->
		<div class="checkout-content pb-30 mt-25 px-25 checkout-first-step-billing-info container-fluid" id="billing-info-tab">
			<div id="login" class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<?php if (!$this->user->id): ?>
					<h3><?php echo Text::_('COM_TJLMS_CHECKOUT_RETURNING_CUSTOMER'); ?></h3>
					<p><?php echo Text::_('COM_TJLMS_CHECKOUT_RETURNING_CUSTOMER_WELCOME'); ?></p>
					<form id="login-form" onsubmit="return false;">
						<b><?php echo Text::_('COM_TJLMS_CHECKOUT_USERNAME'); ?></b><br />
						<input type="text" name="email" id="login-email" value="" class="form-control" required/>
						<br />
						<br />
						<b><?php echo Text::_('COM_TJLMS_CHECKOUT_PASSWORD'); ?></b><br />
						<input type="password" name="password" id="login-password" value="" class="form-control" required/>
						<br />
						<br />
						<input
							type="button"
							value="<?php echo Text::_('COM_TJLMS_CHECKOUT_LOGIN'); ?>"
							id="button-login"
							class="button btn btn-primary"
							onclick="lms_silent_login(this)"/>
						<div id="login-message" class="mt-2"></div>
					</form>
					<h3><?php echo Text::_('COM_TJLMS_CHECKOUT_RETURNING_CUSTOMER_ELSE'); ?></h3>
					<p><?php echo Text::_('COM_TJLMS_CHECKOUT_RETURNING_CUSTOMER_ELSE_NOTE'); ?></p>
				<?php endif; ?>
				<br />
			</div>
			<div class="row-fluid form-horizontal tjlms-filters">
				<div class="section-billing">
					<div class="form-group" id="">
						<span class="help-inline" id="billmail_msg"></span>
					</div>
					<div class="form-group">
						<label  for="req" class="redColor col-xs-12">
							<i><?php echo Text::_('COM_TJLMS_BILLIN_REQ')?></i>
						</label>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="col-xs-12 col-sm-6 section-billing section-line">
					<div class="form-group">
						<label  for="fnam" class="col-xs-12">
							<?php echo Text::_('COM_TJLMS_BILLIN_FNAM') . Text::_('COM_TJLMS_STAR');?>
						</label>
						<div class="col-xs-12">
							<!-- This pattern will take only character and space -->
							<input id="fnam" class=" input-style bill inputbox required validate-name" type="text" value="<?php echo (isset($this->userbill->firstname))?$this->userbill->firstname:''; ?>" maxlength="250" size="32" name="bill[fnam]" title="<?php echo Text::_('COM_TJLMS_BILLIN_FNAM_DESC')?>" pattern="[a-zA-Z\s\.]+">
						</div>
					</div>

					<div class="form-group">
						<label for="lnam" class="col-xs-12">
							<?php echo Text::_('COM_TJLMS_BILLIN_LNAM') . Text::_('COM_TJLMS_STAR');?>
						</label>
						<div class="col-xs-12">
							<!-- This pattern will take only character and space -->
							<input id="lnam" class="input-style  bill inputbox required validate-name" type="text" value="<?php echo (isset($this->userbill->lastname))?$this->userbill->lastname:''; ?>" maxlength="250" size="32" name="bill[lnam]" title="<?php echo Text::_('COM_TJLMS_BILLIN_LNAM_DESC')?>" pattern="[a-zA-Z\s\.]+">
						</div>
					</div>
					<div class="form-group">
						<label for="email1" class="col-xs-12">
							<?php echo Text::_('COM_TJLMS_BILLIN_EMAIL') . Text::_('COM_TJLMS_STAR');?>
						</label>
							<!-- This pattern will take character, number and special symbols like . and @ only -->
						<div class="col-xs-12"><input id="email1" class="input-style bill inputbox required validate-email"  type="email" value="<?php echo (isset($this->userbill->user_email))?$this->userbill->user_email:'' ; ?>" maxlength="250" size="32" name="bill[email1]"  title="<?php echo Text::_('COM_TJLMS_BILLIN_EMAIL_DESC')?>" pattern="([\w\.-]*[a-zA-Z0-9_]@[\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z])*">
						</div>
						<div id="email-warning" class="col-xs-12" style="display: none;">
							<div class="alert alert-warning" role="alert">
								<strong>Warning:</strong> <?php echo Text::_('COM_TJLMS_USER_ALREADY_EXISTS_LOGIN'); ?>
							</div>
						</div>
					</div>
					<?php
					if($this->enable_bill_vat=="1")
					{
						?>
						<div class="form-group">
							<label for="vat_num"  class="col-xs-12">
								<?php echo Text::_('COM_TJLMS_BILLIN_VAT_NUM');?>
							</label>
							<div class="col-xs-12">
							<input id="vat_num" class="input-style bill inputbox validate-integer" type="text" value="<?php echo (isset($this->userbill->vat_number))?$this->userbill->vat_number:''; ?>" size="32" name="bill[vat_num]" title="<?php echo Text::_('COM_TJLMS_BILLIN_VAT_NUM_DESC')?>">
							</div>
						</div>
						<?php
					} ?>
					<div class="form-group">
						<label for="phon" class="col-xs-12">
							<?php echo Text::_('COM_TJLMS_BILLIN_PHON') . Text::_('COM_TJLMS_STAR');?>
						</label>
						<div class="col-xs-12 d-inline-flex">

						<?php
							$mobileCountryCode = $this->country;
							$default_code      = ((isset($this->userbill->country_mobile_code)) ? $this->userbill->country_mobile_code : '');

							$options = array();
							$options[] = HTMLHelper::_('select.option', "", Text::_('COM_TJLMS_BILLIN_SELECT_COUNTRY'));

							foreach ($mobileCountryCode as $key => $value)
							{
								$countryMobileCode = $value['country'] . ' (+' . $value['country_dial_code'] . ')';
								$options[] = HTMLHelper::_('select.option', $value['id'], $countryMobileCode);
							}

							echo $this->dropdown = HTMLHelper::_('select.genericlist',$options,'bill[country_mobile_code]','class="input-style lms_select bill col-xs-4 mr-5"  required="required" aria-invalid="false" size="1" ', 'value', 'text', $default_code, 'country_mobile_code');
							?>

							<!-- This pattern will take only numbers -->
							<input id="phon" class="input-style bill inputbox required validate-numeric col-xs-8 ml-5" type="text"  maxlength="50" value="<?php echo (isset($this->userbill->phone))?$this->userbill->phone:''; ?>" size="32" name="bill[phon]" title="<?php echo Text::_('COM_TJLMS_BILLIN_PHON_DESC')?>">
						</div>
					</div>
				</div>

				<div class="col-xs-12 col-sm-6 section-billing">
					<div class="form-group">
						<label for="addr"  class="col-xs-12">
							<?php echo Text::_('COM_TJLMS_BILLIN_ADDR') . Text::_('COM_TJLMS_STAR');?>
						</label>
						<div class="col-xs-12">
						<textarea id="addr" class="input-style-text bill inputbox required" name="bill[addr]"  maxlength="250" rows="3" title="<?php echo 		Text::_('COM_TJLMS_BILLIN_ADDR_DESC')?>" ><?php echo (isset($this->userbill->address))?$this->userbill->address:''; ?></textarea>
						<p class="help-block">
							<span id="characterLeft" style="width: 24px;border: none;color: grey;">
						</span></p>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<label for="country" class="col-xs-12">
									<?php echo Text::_('COM_TJLMS_BILLIN_COUNTRY').Text::_('COM_TJLMS_STAR');?>
								</label>
								<div class="col-xs-12">
								<?php
										$country = $this->country;
										$default_country = '';
										$default_country = ((isset($this->userbill->country_code)) ? $this->userbill->country_code : '');

										$options = array();
										$options[] = HTMLHelper::_('select.option', "", Text::_('COM_TJLMS_BILLIN_SELECT_COUNTRY'));

										foreach ($country as $key => $value)
										{
											$options[] = HTMLHelper::_('select.option', $value['id'], $value['country']);
										}

										$tprice = 1;
										echo $this->dropdown = HTMLHelper::_('select.genericlist',$options,'bill[country]','class="input-style lms_select bill col-sm-12"  required="required" aria-invalid="false" size="1" onchange=\'tjlms.billing.generateState(id,"",' . $tprice . ')\' ', 'value', 'text', $default_country, 'country');
								?>
								</div>
							</div>
							<div class="form-group">
								<label for="city" class="col-xs-12">
									<?php echo Text::_('COM_TJLMS_BILLIN_CITY');?>
								</label>
								<div class="col-xs-12">
									<!-- This pattern will take only characters and space -->
									<input id="city" class="input-style bill inputbox col-sm-12 " type="text" value="<?php echo (isset($this->userbill->city))?$this->userbill->city:''; ?>" maxlength="250" size="32" name="bill[city]" title="<?php echo Text::_('COM_TJLMS_BILLIN_CITY_DESC')?>" pattern="[a-zA-Z\s\.]+">
								</div>
							</div>
						</div>

						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<label for="state" id="state_lbl" class="col-xs-12"><?php echo Text::_('COM_TJLMS_BILLIN_STATE');?><span id="state_star"></span></label>
								<div class="col-xs-12">
									<select name="bill[state]" id="state" class="input-style lms_select bill col-sm-12">
										<option selected="selected" value="" ><?php echo Text::_('COM_TJLMS_BILLIN_SELECT_STATE');?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="zip"  class="col-xs-12"><?php echo Text::_('COM_TJLMS_BILLIN_ZIP') . Text::_('COM_TJLMS_STAR');?></label>
								<div class="col-xs-12">
									<!-- This pattern will take only numbers -->
									<input id="zip"  class="input-style bill inputbox required col-sm-12" type="text" value="<?php echo (isset($this->userbill->zipcode))?$this->userbill->zipcode:''; ?>" onblur="" maxlength="20" size="32" name="bill[zip]" title="<?php echo Text::_('COM_TJLMS_BILLIN_ZIP_DESC')?>" pattern="[a-zA-Z0-9\s\.]+">
								</div>
							</div>
						</div>
					</div>

					<?php

					if ($this->tnc && $this->doesArticleExists)
					{
						?>
						<div class="form-group term-condition">
							<label for="state" class="col-sm-3 col-xs-12">
								<?php
								$link = Route::_(Uri::root() . "index.php?option=com_content&view=article&id=" . $this->article . "&tmpl=component");
								?>
									<?php
									$modalConfig = array('width' => '800px', 'height' => '600px', 'modalWidth' => 80, 'bodyHeight' => 70);
									$modalConfig['url'] = $link;
									$modalConfig['title'] = Text::_('COM_TJLMS_TERMS_CONDITION');
									echo HTMLHelper::_('bootstrap.renderModal', 'termsandconditions', $modalConfig);
									?>
									<a data-bs-target="#termsandconditions" data-bs-toggle="modal" onclick="jQuery('#termsandconditions').removeClass('fade');" class="af-relative af-d-block ">
										<?php echo Text::_('COM_TJLMS_TERMS_CONDITION');?>
									</a>
							</label>
							<div class="col-xs-12 col-sm-9 billingcheckbox">
								<input class="inputbox " type="checkbox" name="accpt_terms" id="accpt_terms" size="30" <?php echo (!empty($this->userbill->ptnc)?"checked":""); ?> />&nbsp;&nbsp;<?php echo Text::_('COM_TJLMS_YES'); ?>

							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>	<!-- END OF row-fluid-->
		</div>
	</form><!-- END OF Form-->
</div>
<!-- END OF billing_info_tab-->

<script type="text/javascript">
	function lms_silent_login(objid)
	{
		var lms_baseurl = '<?php echo $baseurl; ?>';
		var email = jQuery('#login-email').val();
		var password = jQuery('#login-password').val();
		
		// Basic validation
		if (!email || !password) {
			jQuery('#login-message').html('<div class="alert alert-danger">Please enter both email and password.</div>');
			return false;
		}
		
		// Clear previous messages
		jQuery('#login-message').html('');
		
		jQuery.ajax({
			url: lms_baseurl + '?option=com_tjlms&task=buy.loginValidate&tmpl=component',
			type: 'post',
			data: {
				email: email,
				password: password
			},
			dataType: 'json',
			beforeSend: function() {
				jQuery('#button-login').attr('disabled', true);
				jQuery('#button-login').val('<?php echo Text::_('COM_TJLMS_CHECKOUT_LOGGING_IN'); ?>');
			},
			complete: function() {
				jQuery('#button-login').attr('disabled', false);
				jQuery('#button-login').val('<?php echo Text::_('COM_TJLMS_CHECKOUT_LOGIN'); ?>');
			},
			success: function(json)
			{
				if (typeof json.error !== 'undefined')
				{
					jQuery('#login-message').html('<div class="alert alert-danger">' + json.error.warning + '</div>');
				}
				else if (json.success)
				{
					// Store success message to be shown after billing tab loads
					sessionStorage.setItem('tjlms_login_success_msg', 'true');
					
					// Store the billing tab state in session storage before reloading
					sessionStorage.setItem('tjlms_active_tab', 'billing');
					console.log('Stored billing tab state in session storage');
					
					// Verify storage
					var stored = sessionStorage.getItem('tjlms_active_tab');
					console.log('Verified stored value:', stored);
					
					// Show loader overlay to prevent seeing tab switching
					jQuery('#billing-info').append('<div id="login-loader"><div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-3">Loading your billing information...</p></div></div>');
					
					// Hide login form (don't show success message yet)
					jQuery('#login').html('<div class="alert alert-info"><h4>Logging you in...</h4><p>Please wait while we load your information.</p></div>');

					// Reload the page to load user data
					setTimeout(function() {
						console.log('Reloading page...');
						location.reload();
					}, 1000);
					
					// Fallback: Remove loader if page reload takes too long
					setTimeout(function() {
						if (jQuery("#login-loader").length) {
							console.log('Fallback: Removing loader due to timeout');
							jQuery("#login-loader").remove();
						}
					}, 3000);
				}
				else
				{
					jQuery('#login-message').html('<div class="alert alert-danger">Login failed. Please try again.</div>');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				jQuery('#login-message').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
				console.log('Login error:', xhr.responseText);
			}
		});
	}
	
	// Function to check if user already exists
	function checkExistingUser(email) {
		if (!email || email.length < 5) {
			jQuery('#email-warning').hide();
			return;
		}
		
		// Only check if user is not logged in (guest user)
		<?php if (!$this->user->id): ?>
		jQuery.ajax({
			url: '<?php echo $baseurl; ?>?option=com_tjlms&task=buy.checkExistingUser&tmpl=component',
			type: 'post',
			data: {
				email: email
			},
			dataType: 'json',
			success: function(data) {
				if (data.exists) {
					jQuery('#email-warning').show();
				} else {
					jQuery('#email-warning').hide();
				}
			},
			error: function() {
				// Hide warning on error
				jQuery('#email-warning').hide();
			}
		});
		<?php endif; ?>
	}
	
	// Add event listener for email field
	jQuery(document).ready(function() {
		jQuery('#email1').on('blur', function() {
			var email = jQuery(this).val();
			checkExistingUser(email);
		});
		
		// Also check on input for better UX
		var emailCheckTimer;
		jQuery('#email1').on('input', function() {
			clearTimeout(emailCheckTimer);
			var email = jQuery(this).val();
			emailCheckTimer = setTimeout(function() {
				checkExistingUser(email);
			}, 500); // Wait 500ms after user stops typing
		});
	});
</script>

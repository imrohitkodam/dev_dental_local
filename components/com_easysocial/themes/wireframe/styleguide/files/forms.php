<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="t-lg-mb--xl">
		<h3>Forms</h3>
		<hr class="es-hr">
</div>
<h4>Labels - solid background</h4>

<div data-styleguide-section>
		<div data-behavior="sample_code">
				<form>
					<div class="o-form-group o-form-group--float">
						<input type="email" class="o-form-control o-float-label__input " id="exampleInputEmail1" placeholder="Email">
						<label class="o-control-label" for="exampleInputEmail1">Float label Default</label>
					</div>

					<div class="o-form-group o-form-group--float has-leading-icon has-trailing-icon">
						<a class="o-form-group__icon" href="javascript:void(0);"><i class="fas fa-exclamation-circle"></i></a>
						<a class="o-form-group__icon" href="javascript:void(0);"><i class="fas fa-question-circle"></i></a>
						<input type="email" class="o-form-control o-float-label__input " id="exampleInputEmail11" placeholder="Email">
						<label class="o-control-label" for="exampleInputEmail11">has-leading-icon has-trailing-icon</label>
					</div>

					<div class="o-form-group o-form-group--float has-leading-icon">
						<a class="o-form-group__icon" href="javascript:void(0);"><i class="fas fa-exclamation-circle"></i></a>

						<input type="email" class="o-form-control o-float-label__input " id="exampleInputEmail11" placeholder="Email">
						<label class="o-control-label" for="exampleInputEmail11">has-leading-icon</label>
					</div>

					<div class="o-form-group o-form-group--float has-trailing-icon">
						<a class="o-form-group__icon" href="javascript:void(0);"><i class="fas fa-question-circle"></i></a>

						<input type="email" class="o-form-control o-float-label__input " id="exampleInputEmail11" placeholder="Email">
						<label class="o-control-label" for="exampleInputEmail11">has-trailing-icon</label>
					</div>

					<div class="o-form-group o-form-group--float" >
						<label class="o-control-label" for="exampleInputEmail2">Select float label</label>
						<div class="o-select-group">
							<select name="" id="" class="o-form-control o-float-label__input ">
								<option  selected=""></option>
								<option value="a1">testing</option>
								<option value="a2">testing 2</option>
								<option value="a3">testing 3</option>
							</select>
							<span class="o-select-group__drop"></span>
						</div>
					</div>

					<div class="o-form-group o-form-group--float">
						<label class="o-control-label" for="exampleFormControlTextarea1">Textarea</label>

						<textarea class="o-form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
					</div>

					<hr>
					<div class="o-form-group">
						<label for="exampleInputEmail1">Email address</label>
						<input type="email" class="o-form-control" id="exampleInputEmail1" placeholder="Email">
					</div>
					<div class="o-form-group">
						<label for="exampleInputPassword1">Password</label>
						<input type="password" class="o-form-control" id="exampleInputPassword1" placeholder="Password">
					</div>
					<div class="o-form-group">
						<label for="exampleInputFile">File input</label>
						<input type="file" id="exampleInputFile">
						<p class="help-block">Example block-level help text here.</p>
					</div>
					<div class="o-form-group">
						<div class="o-select-group">
							<select name="" id="" class="o-form-control">
								<option value="">testing</option>
								<option value="">testing 2</option>
								<option value="">testing 3</option>
							</select>
							<label for="" class="o-select-group__drop"></label>
						</div>
					</div>
					<div class="o-form-group">
						<div class="o-select-group o-select-group--inline">
							<select name="" id="" class="o-form-control">
								<option value="">testing</option>
								<option value="">testing 2</option>
								<option value="">testing 3</option>
							</select>
							<label for="" class="o-select-group__drop"></label>
						</div>
						<div class="o-select-group o-select-group--inline">
							<select name="" id="" class="o-form-control">
								<option value="">testing</option>
								<option value="">testing 2</option>
								<option value="">testing 3</option>
							</select>
							<label for="" class="o-select-group__drop"></label>
						</div>
					</div>
					<div class="o-checkbox">
							<input type="checkbox" id="item-checkbox-1">
							<label for="item-checkbox-1">
									Custom checkbox
							</label>
					</div>
					<div class="">
							<div class="o-checkbox o-checkbox--inline">
									<input type="checkbox" id="item-checkbox-2">
									<label for="item-checkbox-2">
											Custom checkbox
									</label>
							</div>
							<div class="o-checkbox o-checkbox--inline">
									<input type="checkbox" id="item-checkbox-3">
									<label for="item-checkbox-3">
											Custom checkbox
									</label>
							</div>
					</div>
					<div class="o-radio">
							<input type="radio" id="item-radio-1">
							<label for="item-radio-1">
									Custom radio
							</label>
					</div>
					<div class="o-radio">
							<input type="radio" id="item-radio-2">
							<label for="item-radio-2">
									Custom radio
							</label>
					</div>
					<div class="">
							<div class="o-radio o-radio--inline">
									<input type="radio" id="item-radio-3">
									<label for="item-radio-3">
											Custom radio
									</label>
							</div>
							<div class="o-radio o-radio--inline">
									<input type="radio" id="item-radio-4">
									<label for="item-radio-4">
											Custom radio
									</label>
							</div>
					</div>

					<div class="o-onoffswitch">
							<input type="checkbox" name="onoffswitch" class="o-onoffswitch__checkbox" id="onoffswitch" checked>
							<label class="o-onoffswitch__label" for="onoffswitch"></label>
					</div>

					<div class="o-onoffswitch">
							<input type="checkbox" name="onoffswitch-2" class="o-onoffswitch__checkbox" id="onoffswitch-2" >
							<label class="o-onoffswitch__label" for="onoffswitch-2"></label>
					</div>

					<button type="submit" class="btn btn-default">Submit</button>
				</form>
		</div>
</div>

<form action="" class="o-form-horizontal">
		<div data-fieldname="es-fields-8" data-required="0" data-id="8" data-element="permalink" data-profile-edit-fields-item="">
				<div data-check="" data-edit-field-8="" data-edit-field="" data-field-8="" data-field="" class="o-form-group  has-error">
						<!-- Field title -->
						<label for="es-fields-8" class="o-control-label">
								Profile Permalink:
						</label>
						<div class="o-control-input">
								<div data-content="" class=" data">
										<div data-error-required="Your permalink is required." data-error-length="Your permalink is too long." data-max="100" data-field-permalink="">
												<div class="o-input-group">
														<input type="text" placeholder="your-name" data-permalink-input="" autocomplete="off" value="" id="permalink" name="es-fields-8" class="o-form-control validation keyup length-4 required">
														<span class="o-input-group__btn">
										<button data-permalink-check="" class="btn btn-es-default-o" type="button">Check</button>
								</span>
												</div>
												<div class="">
														<span style="display: none;" data-permalink-available="" class="help-block">
										<span class="t-text--success">Great, the permalink is available.</span>
														</span>
												</div>
										</div>
								</div>
								<div class="controls-error ">
										<div data-check-notice="" class="text-error">
										</div>
								</div>
								<!-- Tooltip note -->
								<div class="o-help-block">
										<div class="">
												<strong>Note:</strong> Give your own profile a unique link. </div>
								</div>
						</div>



				</div>
		</div>

</form>

<hr class="es-hr">

<h4>o-form-horizontal</h4>
<form action="" class="o-form-horizontal">
		<div class="o-form-group">
				<label class="o-control-label" for="review-ratings">
						Ratings: </label>
				<div class="o-control-input">
						[Ratings placehodler]
				</div>
		</div>

		<div class="o-form-group">
			<label class="o-control-label">
				Switch
			</label>
			<div class="o-control-input">
				<div class="o-onoffswitch">
						<input type="checkbox" name="onoffswitch-2" class="o-onoffswitch__checkbox" id="onoffswitch-3" >
						<label class="o-onoffswitch__label" for="onoffswitch-3"></label>
				</div>
			</div>
		</div>
		<div class="o-form-group">
			<label class="o-control-label">
				Switch
			</label>
			<div class="o-control-input">
				<div class="o-onoffswitch">
						<input type="checkbox" name="onoffswitch-2" class="o-onoffswitch__checkbox" id="onoffswitch-4" >
						<label class="o-onoffswitch__label" for="onoffswitch-4"></label>
				</div>
			</div>
		</div>
		<div class="o-form-group">
			<label class="o-control-label">
				Switch
			</label>
			<div class="o-control-input">
				<div class="o-onoffswitch">
						<input type="checkbox" name="onoffswitch-2" class="o-onoffswitch__checkbox" id="onoffswitch-5" >
						<label class="o-onoffswitch__label" for="onoffswitch-5"></label>
				</div>
			</div>
		</div>


		<div class="o-form-group">
				<label class="o-control-label" for="review-title">
						Title: </label>
				<div class="o-control-input">
						<input type="text" id="review-title" value="" placeholder="" name="title" class="o-form-control">
				</div>
		</div>
		<div class="o-form-group">
				<label class="o-control-label" for="review-desc">
						Your review: </label>
				<div class="o-control-input">
						<textarea placeholder="" name="description" class="o-form-control" rows="5" id="review-desc"></textarea>
				</div>
		</div>


		<div class="o-form-group">
				<label class="o-control-label" for="review-desc">
						Address: </label>
				<div class="o-control-input">
					<div class="o-grid o-grid--gutters">
						<div class="o-grid__cell">
							<input class="o-form-control validation keyup length-4" placeholder="Address line 1" name="es-fields-212[address1]" value="" data-field-address-address1="" type="text">
						</div>
					</div>
					<div class="o-grid o-grid--gutters">
						<div class="o-grid__cell">
							<input class="o-form-control validation keyup length-4" placeholder="Address line 1" name="es-fields-212[address1]" value="" data-field-address-address1="" type="text">
						</div>
					</div>
					<div class="o-grid o-grid--gutters">
						<div class="o-grid__cell">
							<input class="o-form-control validation keyup length-4" placeholder="City" name="es-fields-212[city]" value="" data-field-address-city="" type="text">
						</div>
						<div class="o-grid__cell o-grid__cell--1of3">
							<input class="o-form-control validation keyup length-4" placeholder="Zip code" name="es-fields-212[zip]" value="" data-field-address-zip="" type="text">
						</div>
					</div>
					<div class="o-grid o-grid--gutters">
						<div class="o-grid__cell">
							<select class="o-form-control" name="es-fields-212[country]" data-field-address-country="">
								<option value="">Please select a country</option>
							</select>
						</div>
						<div class="o-grid__cell o-grid__cell--1of3">
							<select class="o-form-control" name="es-fields-212[state]" data-field-address-state="">
								<option value="">State</option>
								<option value="">Please select a country first</option>
							</select>
						</div>
					</div>
				</div>
		</div>

		<div class="o-form-group">
				<label class="o-control-label" for="review-title">
						Title: </label>
				<div class="o-control-input">
						<div class="o-grid o-grid--1of4">
							<div class="o-grid__cell">
								<div class="o-input-group">
									<input class="o-form-control" placeholder="Recipient's username" aria-describedby="basic-addon2" type="text">
									<span class="o-input-group__addon" id="basic-addon2">Days</span>
								</div>
							</div>
						</div>

				</div>
		</div>

		<div class="o-form-group">
				<label class="o-control-label" for="review-title">
						Title: </label>
				<div class="o-control-input">
						<div class="o-grid o-grid--2of4">
							<div class="o-grid__cell t-lg-pr--md t-xs-pr--no t-xs-mb--lg">
								<div class="o-input-group">
									<input class="o-form-control" placeholder="Recipient's username" aria-describedby="basic-addon2" type="text">
									<span class="o-input-group__addon" id="basic-addon2">Days</span>
								</div>
							</div>
							<div class="o-grid__cell">
								<div class="o-input-group">
									<input class="o-form-control" placeholder="Recipient's username" aria-describedby="basic-addon2" type="text">
									<span class="o-input-group__addon" id="basic-addon2">Days</span>
								</div>
							</div>
						</div>

				</div>
		</div>

		<div class="o-form-group">
				<label class="o-control-label" for="review-title">
						Title: </label>
				<div class="o-control-input">
						<div class="dropdown_">
								<button type="button" class="btn-popdown dropdown-toggle_" data-bs-toggle="dropdown">
									<i class="fa fa-caret-down btn-popdown__caret"></i>
									<div>
										<i class="fa fa-globe"></i> Open Event
										<div class="btn-popdown__desp"> Anyone can join the event and it does not require approval. These events will appear in search results. </div>
									</div>

								</button>

								<ul class="dropdown-menu dropdown-menu--popdown">
										<li>
												<a href="javascript:void(0);">
														<i class="fa fa-globe"></i> Open Event
														<div class="dropdown-menu--popdown__desp"> Anyone can join the event and it does not require approval. These events will appear in search results. </div>
												</a>
										</li>
										<li>
												<a href="javascript:void(0);">
														<i class="fa fa-globe"></i> Open Event
														<div class="dropdown-menu--popdown__desp"> Anyone can join the event and it does not require approval. These events will appear in search results. </div>
												</a>
										</li>
								</ul>
							</div>

				</div>
		</div>

		<div class="o-form-group">
				<label class="o-control-label" for="review-desc">
						Business Hours: </label>
				<div class="o-control-input">
					<div class="o-form-inline es-form-business-hour">
						<div class="es-form-business-hour__start">
							<div class="es-form-business-hour__label">Start</div>
							<div class="es-form-business-hour__time">
								<div class="o-select-group o-select-group--inline">
									<select name="" id="" class="o-o-form-control input-sm">
										<option value="">00</option>
										<option value="">11</option>
										<option value="">22</option>
									</select>
									<label for="" class="o-select-group__drop"></label>
								</div>
								:
								<div class="o-select-group o-select-group--inline">
									<select name="" id="" class="o-o-form-control input-smx">
										<option value="">00</option>
										<option value="">11</option>
										<option value="">22</option>
									</select>
									<label for="" class="o-select-group__drop"></label>
								</div>
							</div>

						</div>
						<div class="es-form-business-hour__end">
							<div class="es-form-business-hour__label">End</div>
							<div class="es-form-business-hour__time">
								<select class="o-o-form-control input-sm">
									<option>1</option>
									<option>2</option>
									<option>3</option>
									<option>4</option>
									<option>12</option>
								</select>
								:
								<select class="o-o-form-control input-sm">
									<option>00</option>
									<option>05</option>
									<option>10</option>
									<option>15</option>
									<option>20</option>
								</select>
							</div>

						</div>
					</div>
					<div class="o-grid">
						<div class="o-grid__cell">

						</div>
						<div class="o-grid__cell">

						</div>
					</div>

				</div>
		</div>

		<!-- <div class="o-form-group">
				<label class="o-control-label" for="review-desc">
						Business Days: </label>
				<div class="o-control-input">
					<div class="es-form-business-day-wrap">
						<div class="o-checkbox">
								<input type="checkbox" id="business-day-1">
								<label for="business-day-1">
										Monday
								</label>
						</div>
						<div class="o-checkbox">
								<input type="checkbox" id="business-day-2">
								<label for="business-day-2">
										Tuesday
								</label>
						</div>
						<div class="o-checkbox">
								<input type="checkbox" id="business-day-3">
								<label for="business-day-3">
										Wednesday
								</label>
						</div>
						<div class="o-checkbox">
								<input type="checkbox" id="business-day-3">
								<label for="business-day-3">
										Thursday
								</label>
						</div>
						<div class="o-checkbox">
								<input type="checkbox" id="business-day-3">
								<label for="business-day-3">
										Friday
								</label>
						</div>
						<div class="o-checkbox">
								<input type="checkbox" id="business-day-3">
								<label for="business-day-3">
										Saturday
								</label>
						</div>
						<div class="o-checkbox">
								<input type="checkbox" id="business-day-3">
								<label for="business-day-3">
										Sunday
								</label>
						</div>
					</div>
				</div>
		</div> -->

		<div class="o-form-group">
				<label class="o-control-label" for="review-desc">
						Working Hours: </label>
				<div class="o-control-input">
					<div class="">
						<div class="o-radio">
								<input id="es-working-hr-selected" type="radio" name="working-hr">
								<label for="es-working-hr-selected">
										Selected hours
								</label>
						</div>
						<div class="o-radio">
								<input id="es-working-hr-always" type="radio" name="working-hr">
								<label for="es-working-hr-always">
										Always open
								</label>
						</div>
					</div>

					<div class="es-form-working-hour-wrap">

						<div class="es-form-working-hour">
							<div class="es-form-working-hour__title">
								<?php echo JText::_('APP_FIELD_PAGES_HOURS_WORKING_DAYS');?>:
							</div>

							<div class="es-form-working-hour__day">
								<div class="o-checkbox">
									<input type="checkbox" id="business-day-1" name="business-day">
									<label for="business-day-1">
											Monday
									</label>
								</div>
								<div class="es-form-working-hour__grid">
									<div class="es-form-working-hour__cell">
										<div class="o-input-group t-lg-mt--sm">
											<input type="text" class="o-form-control input-sm es-form-working-hour__time" placeholder="8:00">
											<div class="o-input-group__select">
												<div class="o-select-group">
													<select name="" id="" class="o-form-control input-sm">
														<option value="">AM</option>
														<option value="">PM</option>
													</select>
													<label for="" class="o-select-group__drop"></label>
												</div>
											</div>
										</div>
									</div>
									<div class="es-form-working-hour__cell es-form-working-hour__cell--divider">
										&#8211;
									</div>
									<div class="es-form-working-hour__cell">
										<div class="o-input-group t-lg-mt--sm">
											<input type="text" class="o-form-control input-sm es-form-working-hour__time" placeholder="8:00">
											<div class="o-input-group__select">
												<div class="o-select-group">
													<select name="" id="" class="o-form-control input-sm">
														<option value="">AM</option>
														<option value="">PM</option>
													</select>
													<label for="" class="o-select-group__drop"></label>
												</div>
											</div>
										</div>
									</div>
									<div class="es-form-working-hour__cell es-form-working-hour__cell--action">
										<a href="" class="es-form-working-hour__action-link"><i class="fa fa-plus-circle t-icon--success"></i></a>

									</div>
								</div>

							</div>

							<?php for ($i=0; $i < 7; $i++) { ?>

							<div class="es-form-working-hour__day">
								<div class="o-checkbox">
									<input type="checkbox" id="business-day-1" name="business-day">
									<label for="business-day-1">
											Monday
									</label>
								</div>
								<div class="es-form-working-hour__grid t-lg-pl--xl">
									<div class="es-form-working-hour__cell">
										<div class="o-input-group t-lg-mt--sm">
											<input type="text" class="o-form-control input-sm es-form-working-hour__time" placeholder="8:00">
											<div class="o-input-group__select">
												<div class="o-select-group">
													<select name="" id="" class="o-form-control input-sm">
														<option value="">AM</option>
														<option value="">PM</option>
													</select>
													<label for="" class="o-select-group__drop"></label>
												</div>
											</div>
										</div>
									</div>
									<div class="es-form-working-hour__cell es-form-working-hour__cell--divider">
										&#8211;
									</div>
									<div class="es-form-working-hour__cell">
										<div class="o-input-group t-lg-mt--sm">
											<input type="text" class="o-form-control input-sm es-form-working-hour__time" placeholder="8:00">
											<div class="o-input-group__select">
												<div class="o-select-group">
													<select name="" id="" class="o-form-control input-sm">
														<option value="">AM</option>
														<option value="">PM</option>
													</select>
													<label for="" class="o-select-group__drop"></label>
												</div>
											</div>
										</div>
									</div>
									<div class="es-form-working-hour__cell es-form-working-hour__cell--action">
										<a href="" class="es-form-working-hour__action-link"><i class="fa fa-plus-circle t-icon--success"></i></a>
										<a href="" class="es-form-working-hour__action-link"><i class="fa fa-minus-circle t-icon--danger"></i></a>
									</div>
								</div>
								<div class="es-form-working-hour__grid t-lg-pl--xl">
									<div class="es-form-working-hour__cell">
										<div class="o-input-group t-lg-mt--sm">
											<input type="text" class="o-form-control input-sm es-form-working-hour__time" placeholder="8:00">
											<div class="o-input-group__select">
												<div class="o-select-group">
													<select name="" id="" class="o-form-control input-sm">
														<option value="">AM</option>
														<option value="">PM</option>
													</select>
													<label for="" class="o-select-group__drop"></label>
												</div>
											</div>
										</div>
									</div>
									<div class="es-form-working-hour__cell es-form-working-hour__cell--divider">
										&#8211;
									</div>
									<div class="es-form-working-hour__cell">
										<div class="o-input-group t-lg-mt--sm">
											<input type="text" class="o-form-control input-sm es-form-working-hour__time" placeholder="8:00">
											<div class="o-input-group__select">
												<div class="o-select-group">
													<select name="" id="" class="o-form-control input-sm">
														<option value="">AM</option>
														<option value="">PM</option>
													</select>
													<label for="" class="o-select-group__drop"></label>
												</div>
											</div>
										</div>
									</div>
									<div class="es-form-working-hour__cell es-form-working-hour__cell--action">
										<a href="" class="es-form-working-hour__action-link"><i class="fa fa-plus-circle t-icon--success"></i></a>
										<a href="" class="es-form-working-hour__action-link"><i class="fa fa-minus-circle t-icon--danger"></i></a>
									</div>
								</div>

							</div>

							<?php } ?>

							<!-- 24 format -->
							<div class="es-form-working-hour es-form-working-hour--24">
								<div class="es-form-working-hour__title">
									24 format
								</div>
								<div class="es-form-working-hour__day">
									<div class="o-checkbox">
										<input type="checkbox" id="business-day-1" name="business-day">
										<label for="business-day-1">
												Monday
										</label>
									</div>
									<div class="es-form-working-hour__grid t-lg-pl--xl">
										<div class="es-form-working-hour__cell">
											<input type="text" class="o-form-control input-sm es-form-working-hour__time" placeholder="8:00">
										</div>
										<div class="es-form-working-hour__cell es-form-working-hour__cell--divider">
											&#8211;
										</div>
										<div class="es-form-working-hour__cell">
											<input type="text" class="o-form-control input-sm es-form-working-hour__time" placeholder="8:00">
										</div>
										<div class="es-form-working-hour__cell es-form-working-hour__cell--action">
											<a href="" class="es-form-working-hour__action-link"><i class="fa fa-plus-circle t-icon--success"></i></a>
											<a href="" class="es-form-working-hour__action-link"><i class="fa fa-minus-circle t-icon--danger"></i></a>
										</div>
									</div>
									<div class="es-form-working-hour__grid t-lg-pl--xl">
										<div class="es-form-working-hour__cell">
											<input type="text" class="o-form-control input-sm es-form-working-hour__time" placeholder="8:00">
										</div>
										<div class="es-form-working-hour__cell es-form-working-hour__cell--divider">
											&#8211;
										</div>
										<div class="es-form-working-hour__cell">
											<input type="text" class="o-form-control input-sm es-form-working-hour__time" placeholder="8:00">
										</div>
										<div class="es-form-working-hour__cell es-form-working-hour__cell--action">
											<a href="" class="es-form-working-hour__action-link"><i class="fa fa-plus-circle t-icon--success"></i></a>
											<a href="" class="es-form-working-hour__action-link"><i class="fa fa-minus-circle t-icon--danger"></i></a>
										</div>
									</div>

								</div>
							</div>


							<div class="es-form-working-hour__day">
								<div class="o-grid">
									<div class="o-grid__cell">

										<div class="o-select-group">
											<select name="" id="" class="o-form-control">
												<option value="">Monday</option>
												<option value="">testing 2</option>
												<option value="">testing 3</option>
											</select>
											<label for="" class="o-select-group__drop"></label>

										</div>
										<div class="es-form-working-hour__grid">
											<div class="es-form-working-hour__cell">
												<div class="o-input-group t-lg-mt--sm">
													<input type="text" class="o-form-control input-sm es-form-working-hour__time" placeholder="8:00">
													<div class="o-input-group__select">
														<div class="o-select-group">
															<select name="" id="" class="o-form-control input-sm">
																<option value="">AM</option>
																<option value="">PM</option>
															</select>
															<label for="" class="o-select-group__drop"></label>
														</div>
													</div>
												</div>
											</div>
											<div class="es-form-working-hour__cell es-form-working-hour__cell--divider">
												&#8211;
											</div>
											<div class="es-form-working-hour__cell">
												<div class="o-input-group t-lg-mt--sm">
													<input type="text" class="o-form-control input-sm es-form-working-hour__time" placeholder="8:00">
													<div class="o-input-group__select">
														<div class="o-select-group">
															<select name="" id="" class="o-form-control input-sm">
																<option value="">AM</option>
																<option value="">PM</option>
															</select>
															<label for="" class="o-select-group__drop"></label>
														</div>
													</div>
												</div>
											</div>

										</div>
									</div>
									<div class="o-grid__cell o-grid__cell--auto-size">
										<a href="" class="es-form-working-hour__action-link"><i class="fa fa-plus-circle t-icon--success"></i></a>
										<a href="" class="es-form-working-hour__action-link"><i class="fa fa-minus-circle t-icon--danger"></i></a>
									</div>
								</div>


							</div>
						</div>




					</div>
				</div>
		</div>

		<div class="o-form-group">
			<label class="o-control-label" for="">Category: </label>
			<div class="o-control-input">
				<div class="es-form-working-hour-wrap">
					<div class="o-grid">
						<div class="o-grid__cell">
							<div class="o-select-group">
								<select name="" id="" class="o-form-control">
									<option value="">Select category</option>
									<option value="">testing 2</option>
									<option value="">testing 3</option>
								</select>
								<label for="" class="o-select-group__drop"></label>
							</div>
						</div>
						<div class="o-grid__cell es-form-working-hour__cell--action">

						</div>
					</div>

				</div>
			</div>
		</div>
		<div class="o-form-group">
			<label class="o-control-label" for="">Days: </label>
			<div class="o-control-input">
				<div class="es-form-working-hour-wrap">
					<div class="es-form-working-hour-hour">

						<div class="es-form-working-hour__day">
							<div class="o-grid">
								<div class="o-grid__cell">

									<div class="o-select-group">
										<select name="" id="" class="o-form-control">
											<option value="">Monday</option>
											<option value="">testing 2</option>
											<option value="">testing 3</option>
										</select>
										<label for="" class="o-select-group__drop"></label>

									</div>
									<div class="es-form-working-hour__grid">
										<div class="es-form-working-hour__cell">
											<div class="o-input-group t-lg-mt--sm">
												<input type="text" class="o-form-control input-sm es-form-working-hour__time" placeholder="8:00">
												<div class="o-input-group__select">
													<div class="o-select-group">
														<select name="" id="" class="o-form-control input-sm">
															<option value="">AM</option>
															<option value="">PM</option>
														</select>
														<label for="" class="o-select-group__drop"></label>
													</div>
												</div>
											</div>
										</div>
										<div class="es-form-working-hour__cell es-form-working-hour__cell--divider">
											&#8211;
										</div>
										<div class="es-form-working-hour__cell">
											<div class="o-input-group t-lg-mt--sm">
												<input type="text" class="o-form-control input-sm es-form-working-hour__time" placeholder="8:00">
												<div class="o-input-group__select">
													<div class="o-select-group">
														<select name="" id="" class="o-form-control input-sm">
															<option value="">AM</option>
															<option value="">PM</option>
														</select>
														<label for="" class="o-select-group__drop"></label>
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
								<div class="o-grid__cell o-grid__cell--auto-size es-form-working-hour__cell--action">
									<a href="" class="es-form-working-hour__action-link"><i class="fa fa-plus-circle t-icon--success"></i></a>
									<a href="" class="es-form-working-hour__action-link"><i class="fa fa-minus-circle t-icon--danger"></i></a>
								</div>
							</div>


						</div>
					</div>
				</div>
			</div>
		</div>


		<code>Backup for future</code>
		<div class="o-form-group">
				<label class="o-control-label" for="review-desc">
						Business Hours: </label>
				<div class="o-control-input">
					<div class="o-form-inline es-form-business-hour">
						<div class="es-form-business-hour__start">
							<div class="es-form-business-hour__label">Start</div>
							<div class="es-form-business-hour__time">
								<select class="o-o-form-control input-sm">
									<option>1</option>
									<option>2</option>
									<option>3</option>
									<option>4</option>
									<option>5</option>
								</select>
								:
								<select class="o-o-form-control input-sm">
									<option>00</option>
									<option>05</option>
									<option>10</option>
									<option>15</option>
									<option>20</option>
								</select>
							</div>
							<div class="es-form-business-hour__ampm">
								<div data-bs-toggle="radio-buttons" class="o-btn-group-ampm">
									<button data-bs-toggle-value="1" class="btn btn-sm btn--am" type="button">AM</button>
									<button data-bs-toggle-value="0" class="btn btn-sm btn--pm active" type="button">PM</button>
								</div>
							</div>
						</div>
						<div class="es-form-business-hour__end">
							<div class="es-form-business-hour__label">End</div>
							<div class="es-form-business-hour__time">
								<select class="o-o-form-control input-sm">
									<option>1</option>
									<option>2</option>
									<option>3</option>
									<option>4</option>
									<option>12</option>
								</select>
								:
								<select class="o-o-form-control input-sm">
									<option>00</option>
									<option>05</option>
									<option>10</option>
									<option>15</option>
									<option>20</option>
								</select>
							</div>
							<div class="es-form-business-hour__ampm">
								<div data-bs-toggle="radio-buttons" class="o-btn-group-ampm">
									<button data-bs-toggle-value="1" class="btn btn-sm btn--am" type="button">AM</button>
									<button data-bs-toggle-value="0" class="btn btn-sm btn--pm active" type="button">PM</button>
								</div>
							</div>
						</div>
					</div>
					<div class="o-grid">
						<div class="o-grid__cell">

						</div>
						<div class="o-grid__cell">

						</div>
					</div>

				</div>
		</div>



		<div class="o-form-actions">
				<a class="btn btn-es-danger btn-sm pull-left" href="/">Cancel</a>
				<button data-save-button="" class="btn btn-es-primary btn-sm pull-right">Save</button>
		</div>

</form>

<hr class="es-hr">
<h4>Edit profile</h4>

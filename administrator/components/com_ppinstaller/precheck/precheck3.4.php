<?php
class precheck34 
{
	function start() { ?>
		<style>
			.bs-callout {
			    -moz-border-bottom-colors: none;
			    -moz-border-left-colors: none;
			    -moz-border-right-colors: none;
			    -moz-border-top-colors: none;
			    border-color: #eee;
			    border-image: none;
			    border-radius: 3px;
			    border-style: solid;
			    border-width: 1px 1px 1px 5px;
			    margin: 20px 0;
			    padding: 20px;
			}
			.bs-callout-danger {
			    border-left-color: #d9534f;
			}
		</style>
		<div class="bs-callout bs-callout-danger">
			<p><h3><span class="label label-warning">PayPlans 3.4 release has core changes related to the Tax and Discount.</span></h3></p><br/>
			<p>If you upgrade PayPlans to 3.4.x, make sure if you have any changes in tax and discount related apps then take backup before upgrading.</p>
			<p>Following apps have been modified so you must upgrade them from Appstore.</p>
					<ul>
						<li>Basic Tax</li>
						<li>EU-VAT</li>
						<li>Pro Discount</li>
						<li>Gift App</li>
						<li>Plan Addons</li>
						<li>Referral</li>
						<li>PDF Invoice</li>
					</ul>
		<p><h5><span class="label label-danger">Please note that PayPlans 3.4.0 is a Beta release which might have some minor issues. You should wait for a stable release to use it on your live site.</span></h5></p>
		  </div>
		  
		<?php return true;
	}
}

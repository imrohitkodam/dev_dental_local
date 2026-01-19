<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		RBInstaller
* @subpackage	Back-end
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!-- Change the below code with Rb-Framework 1.1 -->
<link rel="stylesheet" type="text/css" href="<?php echo JHtml::stylesheet('com_rbinstaller/admin.css', array(), true, true);?>" />	


<script type="text/javascript">
(function($){
	$(document).ready(function(){	

		// if ugrade is available then do not verify the credential,
		// so that only on popup will be shown at the same time
		<?php if(!$is_uppgradable) :?>			
			// send ajax request to check the credential
			var url = 'index.php?option=com_rbinstaller&view=item&task=credential&action=check&product_tag='+rbinstaller_product_tag_alias;
			rb.ajax.go(url);
		<?php else :?>
			$('#rbinstaller-upgrade-modal').modal({ keyboard: false, backdrop: 'static' });
			$('#rbinstaller-upgrade-modal').modal('show');
		<?php endif;?>
		
	});
})(rb.jQuery);
</script>

<script>
	var rbinstaller_items	 			= <?php echo json_encode($data['items'], JSON_NUMERIC_CHECK);?>;
	var rbinstaller_tag_items 			= <?php echo json_encode($data['tag_items']);?>;
	var rbinstaller_product_tag 		= <?php echo $data['tags'][$product_tag]['id'];?>;
	var rbinstaller_product_tag_alias	= '<?php echo $product_tag;?>';	
	var rbinstaller_added_items 		= <?php echo json_encode($added_items);?>;	
	var rbinstaller_invoices 			= <?php echo json_encode($invoices);?>;
	var rbinstaller_config 				= <?php echo json_encode($config);?>;
	var rbinstaller_tags 				= <?php echo json_encode($data['tags']);?>;
	var rbinstaller_color_codes 		= <?php echo json_encode($data['color_codes']);?>;
	var rbinstaller_compatibilities		= <?php echo json_encode($data['compatibilities']);?>;	
	var rbinstaller_base_url			= '<?php echo JURI::root();?>administrator/components/com_rbinstaller/templates/default/item/';
	var rbinstaller_item_alias_map		= <?php echo json_encode($data['alias_map']);?>;
	var rbinstaller_version 			= '<?php echo RBINSTALLER_VERSION;?>';
</script>

<div id="rbinstaller-login-modal" class="modal hide fade">
</div>

<?php if($is_uppgradable) :?>
	<?php echo $this->loadTemplate('upgrade');?>
<?php endif;?>

<div data-ng-app="rb_appmanager_app" class="rbinstaller-container" data-ng-controller="AppController" data-ng-init="applyFilter();">
	<div>
		<div id="rbinstaller-header">
			<?php 	
			$version = new JVersion();
			$suffix = 'jom=J'.$version->RELEASE.'&utm_campaign=broadcast&rbinstaller=RI'.RBINSTALLER_VERSION.'&dom='.JURI::getInstance()->toString(array('scheme', 'host', 'port'));
			?>		
						  
			<div class="header-title">
				<div class="container">
					<div class="row-fluid">
						<div class="span4">
							<iframe style="border: 0px; height:54px; overflow: hidden;" src="http://pub.readybytes.net/broadcast/rbinstaller/broadcast.html?<?php echo $suffix?>"></iframe>
						</div>
						<div class="span4 text-center">
							<h1><?php echo $data['tags'][$product_tag]['title']?> App Store</h1>
						</div>
					</div>
					<div class="row-fluid">
						<div class="pull-right muted"><?php echo $data['tags'][$product_tag]['title']?> : <?php echo $product_version;?></div>
					</div>
				</div>
			</div>
		
			<div class="progress" style="height:5px;  margin-bottom: 0px;">
	    		<div class="bar" style="width: 100%; background-image:none; background-color: <?php echo $data['color_codes'][$product_tag];?>"></div>
	    	</div>
	    </div>
	    
		<div class="search-bar" id="rbinstaller-search-bar">
			<div class="search-bar-container">			
				<div class="row-fluid">
					<div class="span1 filters text-center">
						<div class="padding-top-20">
							<a href="<?php echo Rb_Route::_($return_url);?>" class="btn-link">
								<i class="fa fa-arrow-left"></i> Back 
							</a>
						</div>
					</div>
									
					<div class="span2 filters text-center">
						<div class="padding-top-20">
							<a href="#/app" onclick="return rbinstaller.filters.toggle(this);" id="rbinstaller-toggle-filter">
								Show Filters
								<i class="fa fa-angle-double-down"></i>
							</a>
						</div>			 
					</div>
						
					<div class="span5 text-center">
						<div class="padding-top-20">
							<input type="text" class="span10 search-query" data-ng-model="filter_query" placeholder="Search...">				
						</div>
					</div>
						
					<div class="span2 mycart text-center">
						<div class="padding-top-20" id="rbinstaller-cart-icon">
							<?php if(is_array($added_items) && count($added_items) > 0) :?>
								<a href="#" data-toggle="modal" data-target="#rbinstaller-cart">
									<i class="fa fa-shopping-cart fa-2x"></i>
									My Cart
									<sup class="badge badge-warning">
										<?php echo count($added_items);?>
									</sup>
					    		</a>
					    	<?php else:?>
					    		<span class="muted">
									<i class="fa fa-fa fa-shopping-cart fa-2x"></i>
									My Cart
									<sup class="badge">0</sup>
					    		</span>
					    	<?php endif;?>
						</div>
					</div>
						
					<div class="span2 text-center">
						<div class="padding-top-20">
			    			<div class="btn-group margin-top-0">								 
								<button tabindex="-1" class="btn" onclick="rb.ajax.go('index.php?option=com_rbinstaller&view=item&task=update_account'); return false;">
									<i class="fa fa-user"></i>
                                    Update Account
								</button>
							</div>
			    		</div>
					</div>
				</div>
			</div>
			
			
			<div class="row-fluid rbinstaller-filter-bar-container hide" id="rbinstaller-filter-bar-container">
				<div class="rbinstaller-filter-bar">
					<div class="row-fluid padding-top-20">						
						<div class="span2">
							<select data-ng-model="filter_price" class="input-medium" data-ng-change="trackFilter()">
								<option value="">-- Price --</option>
								<option value="free">Free</option>
								<option value="paid">Paid</option>								
							</select>
						</div>						
						<div class="span2">
							<select data-ng-model="filter_tag" class="input-medium" data-ng-change="trackFilter()">
								<option value="">-- Category --</option>
								<?php unset($data['tag_items'][$data['tags'][$product_tag]['id']]);?>
								<?php foreach($data['tags'] as $tag_alias => $tag) :?>
									<?php if(isset($data['tag_items'][$tag['id']])) : ?>
										<option value="<?php echo $tag['id'];?>"><?php echo $tag['title'];?></option>
									<?php endif;?>
								<?php endforeach;?>
							</select>
						</div>						
						<div class="span2">
							<select data-ng-model="filter_status" class="input-medium" data-ng-change="trackFilter()">
								<option value="">-- Status --</option>
								<option value="installed">Installed</option>
								<option value="upgradable">Upgradable</option>
								<option value="not_available">Not Available</option>
								<option value="not_installed">Not Installed</option>
							</select>
						</div>						
						<div class="span2">
							<select data-ng-model="filter_subscription_status" class="input-medium" data-ng-change="trackFilter()">
								<option value="">-- Subscription --</option>
								<option value="active">Active</option>
								<option value="expired">Expired</option>								
							</select>
						</div>
						<div class="span2">
							<select data-ng-model="filter_type" class="input-medium" data-ng-change="trackFilter()">
								<option value="">-- Type --</option>
								<option value="component">Component</option>
								<option value="file">File</option>
								<option value="language">Language</option>
								<option value="library">Library</option>
								<option value="module">Module</option>
								<option value="plugin">Plugin</option>
								<option value="template">Template</option>
							</select>
						</div>
						<div class="span2">
							<button data-ng-click="resetFilters();" class="btn btn-small">
								Reset
							</button>
						</div>
					</div>
				</div>
			</div>
		
		</div>	
	</div>
	<div>&nbsp;</div>
	<div ui-view class="container">	
	</div>
	
</div>

<div class="hide">
	<iframe id="installation-traker-iframe" src=""></iframe>
</div>
<?php 

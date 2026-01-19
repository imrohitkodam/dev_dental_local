// XITODO : where to put this constant
var rb_app_manager_limit_per_row 		= 3;
var rb_app_manager_invoice_status_paid 	= 402;
var rb_app_manager_invoice_status_inprocess	= 404;

var controllers = {};
	
controllers.AppController = function($scope, $state, $filter, $cookies, $timeout){	
	$scope.base_url			= rbinstaller_base_url;
	$scope.items 			= rbinstaller_items;
	$scope.tag_items 		= rbinstaller_tag_items;
	$scope.product_tag 		= rbinstaller_product_tag;	
	$scope.product_tag_alias= rbinstaller_product_tag_alias;
	$scope.added_items 		= rbinstaller_added_items;
	$scope.invoices 		= rbinstaller_invoices;
	$scope.config 			= rbinstaller_config;
	$scope.tags 			= rbinstaller_tags;
	$scope.install_item		= 0;
	$scope.color_codes		= rbinstaller_color_codes;
	$scope.compatibilities		= rbinstaller_compatibilities;
	$scope.item_alias_map	= rbinstaller_item_alias_map;
	
	$scope.tag_id_map	= {};
	for(var index in $scope.tags) {
    	if(typeof($scope.tags[index]) != 'undefined' ){
    		$scope.tag_id_map[$scope.tags[index].id] = $scope.tags[index];
    	}
    }
	
	$scope.fullview_row_number = -1;
	$scope.recent_clicked_item = 0;
	
	// pagination
    $scope.itemsPerPage = 12;
    $scope.pagedItems = [];
    $scope.currentPage = 0;
    
    // calculate page in place
    $scope.groupToPages = function () {
        $scope.pagedItems = [];
        var counter = 0;
        
        var items = $filter('orderObjectBy')($scope.filteredItems, 'hits', true);
        
        for(var index in items) {
			if(items.hasOwnProperty(index)== false){
    			continue;
    		}

        	if(counter % $scope.itemsPerPage === 0){
	        	$scope.pagedItems[Math.floor(counter / $scope.itemsPerPage)]= [];
        	}
        	
        	$scope.pagedItems[Math.floor(counter / $scope.itemsPerPage)].push(items[index]);
        	
            counter++;
        }
    };
    
    // watch it because search box is in parent scope so need to refresh the search when description changed
    $scope.$watch('filter_query',function(){        
        $scope.search();
        $cookies.filter_query = $scope.filter_query;
    });
    
    $scope.range = function (start, end) {
        var ret = [];
        if (!end) {
            end = start;
            start = 0;
        }
        for (var i = start; i < end; i++) {
            ret.push(i);
        }
        return ret;
    };
    
    $scope.prevPage = function () {
        if ($scope.currentPage > 0) {
        	$scope.hideFullDetailView();
            $scope.currentPage--;            
        }
    };
    
    $scope.nextPage = function () {
        if ($scope.currentPage < Object.keys($scope.pagedItems).length - 1) {
        	$scope.hideFullDetailView();
            $scope.currentPage++;
        }
    };
    
    $scope.setPage = function () {
    	$scope.hideFullDetailView();
        $scope.currentPage = this.n;
    };
    
    $scope.search = function () {
    	$scope.hideFullDetailView();
    	$scope.filteredItems = $filter('tag_filter')($scope.items, $scope.filter_tag, $scope.tag_items);
        $scope.filteredItems = $filter('item_filter')($scope.filteredItems, $scope.filter_query);
        $scope.filteredItems = $filter('type_filter')($scope.filteredItems, $scope.filter_type);
        $scope.filteredItems = $filter('status_filter')($scope.filteredItems, $scope.filter_status);
        $scope.filteredItems = $filter('subscription_status_filter')($scope.filteredItems, $scope.filter_subscription_status);
        $scope.filteredItems = $filter('price_filter')($scope.filteredItems, $scope.filter_price);
        
        // reset current page
        $scope.currentPage = 0;
        // now group by pages
        $scope.groupToPages();
    };
    
	$scope.templates = { 
			item: $scope.base_url+'list.html',
			myapps: $scope.base_url+'myapps.html',
			alert: {
				expired_installed 	: $scope.base_url+'alert_expired_installed.html',
				expired_upgradable 	: $scope.base_url+'alert_expired_upgradable.html',
				expired_install     : $scope.base_url+'alert_expired_install.html'
			} ,
			cart : $scope.base_url+'cart.html',
			install : $scope.base_url+'install.html',
			fullview : $scope.base_url+'fullview.html',
			image_modal :  $scope.base_url+'image_modal.html'
	 };
		
	//function to find proper place to render this view
	$scope.find_last_item_block = function(item_id){
	
		// if not item id is available in url then do nothing
		if(!item_id){
			return true;
		}
		
		var found = false;
		// update the currenct page
		for(var index in $scope.pagedItems){
			if($scope.pagedItems.hasOwnProperty(index)){
				for(var item_index in $scope.pagedItems[index]) {
	    			if($scope.pagedItems[index].hasOwnProperty(item_index)== false){
	        			continue;
	        		}
	    			
	    			if(item_id == $scope.pagedItems[index][item_index].item_id){
	    				$scope.currentPage = index;
	    				found = true;
	    				break;
	    			}
	    		}
			}

			if(found){
				break;
			}
		}	    	

		// following change need to be done after the scope is appied
		// using below function instead of $scope.$apply();
		$timeout(function() {    		
			var item_blocks = rb.jQuery('.item-block:visible');
			
			var index_of_item = -1;
			for(var index = 0; index < item_blocks.length ; index++){
				
				if(item_blocks[index].id == ('rbinstaller-item-'+item_id)){
					index_of_item = index;
					break;
				}
			}
			
			$scope.recent_clicked_item = index_of_item % rb_app_manager_limit_per_row;
			
			var remaining_items_to_check = rb_app_manager_limit_per_row - (index_of_item % rb_app_manager_limit_per_row) - 1;
			
			var new_row_number  = index_of_item / rb_app_manager_limit_per_row;
			var prev_row_number = $scope.fullview_row_number;		
			$scope.fullview_row_number = new_row_number;
			
			if(new_row_number != $scope.fullview_row_number){
				rb.jQuery('#rbinstaller-fulldetail-view').hide();
			}
					
			var last_index = index_of_item; 
			
			for(; remaining_items_to_check > 0 ; remaining_items_to_check--){			
				if(typeof(item_blocks[last_index + 1]) == 'undefined'){
					break;
				}
				
				last_index++;
			}		
			
			rb.jQuery('#rbinstaller-fulldetail-view').insertAfter('#' + item_blocks[last_index].id);
			rb.jQuery('html,body').animate({
		        scrollTop: rb.jQuery('#' + item_blocks[last_index].id).offset().top});		
		
			if(new_row_number != $scope.fullview_row_number  || !rb.jQuery('#paymart-fulldetail-view').is(':visible')){
				rb.jQuery('#rbinstaller-fulldetail-view').show('fast');
			}
		});
		return true;
	};	
	
	$scope.getTemplatePath = function(item_id){
		var base_path = $scope.base_url;		
		
		switch($scope.items[item_id].status){
			case 'not_available': 
			case 'not_compatible':	
			
			case 'none_addedtocart' :
			case 'none_buynow' :
			case 'none_installed' :   // Rare Condition
			case 'none_upgradable' :  // Rare Condition
			
			case 'free_install' :
			case 'active_install' :
			case 'active_installed' :
			case 'active_upgradable' :
			
			case 'expired_install' :
			case 'expired_installed' :
			case 'expired_upgradable' :
					return base_path + 'item_' + $scope.items[item_id].status +'.html';
			default : return '';//base_path + 'default_list_item_buynow.html';		
		}		
	};
	
	$scope.install = {};
	$scope.install.view = function(item_id){
		$scope.install_item = $scope.items[item_id];
		
		$scope.item_compatibilities = $scope.get_compatibilities($scope.install_item);
		
		rb.jQuery('#rbinstaller-install').modal('show');
		
		rb.jQuery('#install-item-details').show();
		rb.jQuery('#install-item-version').hide();
		rb.jQuery('#install-success').hide();
		rb.jQuery('#install-failure').hide();
	};
	
	$scope.install.item = function(itemversion_id){
		$scope._recent_item_install = $scope.install_item.item_id;
		$scope._recent_item_install_version = itemversion_id;
		
		rb.jQuery('#install-version-id').text('[ Version : '+$scope.install_item.version[itemversion_id].version_name+' ]');
		
		rb.jQuery('#install-item-details').hide();
		rb.jQuery('#install-item-version').show();
		rb.jQuery('#install-success').hide();
		rb.jQuery('#install-failure').hide();
		
		rbinstaller.item.install($scope.install_item.item_id, itemversion_id, $scope.product_tag_alias);
	};
	
	$scope.install.success = function(){
		$scope.items[$scope._recent_item_install].previous_status = 'active_installed';		
		$scope.items[$scope._recent_item_install].status = 'active_installed';
		
		$scope.updateFullView($scope._recent_item_install, true);
		
		$scope.$apply();
		rb.jQuery('#install-item-details').hide();
		rb.jQuery('#install-item-version').hide();
		rb.jQuery('#install-success').show();
		rb.jQuery('#install-failure').hide();
		
		//tracking
		var event_args = { 'item'    : $scope.items[$scope._recent_item_install].title, 
						   'price'   : $scope.items[$scope._recent_item_install].price,
						   'version' : $scope._recent_item_install_version
				 };
		rbinstaller.tracking('install_success', event_args)
	};
	
	$scope.install.error = function(response){
		var reason = '<big>Installation of this version is failed, please try again or report this issue to service provider.</big>';
		if(typeof(response.reason) != 'undefined'){
			reason = '<big>'+response.reason+'</big>';
		}
		
		rb.jQuery('#install-failure .modal-body').html(reason);
		rb.jQuery('#install-item-details').hide();
		rb.jQuery('#install-item-version').hide();
		rb.jQuery('#install-success').hide();
		rb.jQuery('#install-failure').show();
		
		//tracking
		var event_args = { 'item'    : $scope.items[$scope._recent_item_install].title, 
						   'price'   : $scope.items[$scope._recent_item_install].price,
						   'version' : $scope._recent_item_install_version
		 };
		rbinstaller.tracking('install_failed', event_args)
	}
	
	$scope.cart = {};
	
	$scope.cart.addToCart = function(item_id){
		$scope._recent_item_added = item_id;
		rbinstaller.cart.add_item(item_id);
		
		//tracking
		var event_args = { 'item'    : $scope.items[item_id].title, 
						   'price'   : $scope.items[item_id].price
		};
		rbinstaller.tracking('addtocart', event_args)
	};

	$scope.cart.add_success = function(items){
		$scope.added_items = items;
		
		$scope.items[$scope._recent_item_added].previous_status = $scope.items[$scope._recent_item_added].status;
		$scope.items[$scope._recent_item_added].status = 'none_addedtocart';
		$scope.updateFullView($scope._recent_item_added, true);
		
		$scope.$apply();	
	
		rb.jQuery('#rbinstaller-cart').modal('show');
	};
	
	$scope.cart.removeFromCart = function(item_id){
		$scope._recent_item_remove = item_id;
		rbinstaller.cart.remove_item(item_id);
	};
	
	$scope.cart.remove_success = function(items){
		$scope.added_items = items;
		
		var item_id = $scope._recent_item_remove;
		if($scope.items[item_id].previous_status == $scope.items[item_id].status){
			$scope.items[item_id].status = 'none_buynow';
			$scope.items[item_id].previous_status  = 'none_buynow';
			
			$scope.updateFullView(item_id, true);
		}
		else{
			$scope.items[item_id].status = $scope.items[item_id].previous_status;
			$scope.updateFullView(item_id, true);
		}		
		
		$scope.$apply();
		if(items.length <= 0){			
			rb.jQuery('#rbinstaller-cart').modal('hide');
		}
	};
	
	$scope.cart.checkout = function(added_items)
	{
		rbinstaller.cart.checkout(added_items);
	};	

	// XITODO : use directives
	$scope.fromJson = function(json){	
			return angular.isString(json) ? rb.jQuery.parseJSON(json): json;
	};
	

	$scope.mysql_to_date = function(dateString){
		// Split timestamp into [ Y, M, D, h, m, s ]
		var time = dateString.split(/[- :]/);
		// Apply each element to the Date function
		return new Date(Date.UTC(time[0], time[1]-1, time[2], time[3], time[4], time[5]));			
	};
	
	$scope.add_expiration = function(dateString, expiration){		
		// Split timestamp into [ Y, M, D, h, m, s ]
		var time = dateString.split(/[- :]/);
		
		var exp = Array();		
		for(var count = 0; count <= 5 ; count++){
			time[count] = parseInt(time[count]) + parseInt(expiration.slice(count*2, (count*2)+2));			 
		}		
		
		return new Date(Date.UTC(time[0], time[1]-1, time[2], time[3], time[4], time[5]));
	};
	
	$scope.get_item_status = function(status, paid_date, expiration){
		if(expiration == '000000000000'){
			return 'active';
		}
		
		if(status == rb_app_manager_invoice_status_inprocess){
			return 'inprocess';
		}
		
		if(status == rb_app_manager_invoice_status_paid){
			var expiration_date = $scope.add_expiration(paid_date, expiration);
			var now = new Date();
			
			if(expiration_date.getTime() > now.getTime()){
				return 'active';
			}
			
			return 'expired';
		}
		
		return '-';
	};
	
	$scope.get_cart_total = function(){
		var total = 0;
		for(var index in $scope.added_items){
			if($scope.added_items.hasOwnProperty(index)){
				total = parseFloat(total) + parseFloat($scope.items[$scope.added_items[index]].price);
			}
		}
		
		return total;
	};
	
	$scope.get_cart_item_count = function(){
		if(typeof($scope.added_items) != 'object'){
			return 0;
		}
		
		return Object.keys($scope.added_items).length;
	};
	
	$scope.get_compatibilities = function(item){
		var item_compatibilities = {};
		for(var version_id in item.version){
			if(item.version.hasOwnProperty(version_id)){
				for(var compatibility_value in $scope.compatibilities){
					if($scope.compatibilities.hasOwnProperty(compatibility_value) && (item.version[version_id][compatibility_value] == 1)){				
						var prefix  = $scope.compatibilities[compatibility_value].prefix;
						var name  = $scope.compatibilities[compatibility_value].name;
						
						if(typeof(item_compatibilities[version_id]) == 'undefined'){
							item_compatibilities[version_id] = {}
						}
						
						if(typeof(item_compatibilities[version_id][prefix]) == 'undefined'){
							item_compatibilities[version_id][prefix] = {'title' : name, 'values': []};					
						}
						
						item_compatibilities[version_id][prefix]['values'].push($scope.compatibilities[compatibility_value].value); 
					}
				} 
			}
		}
		
		return item_compatibilities;
	};
	
	$scope.has_data = function(data){		
		for(var index in data){
			if(data.hasOwnProperty(index) && data[index].length > 0){
				return true;
			}
		}
		
		return false;
	};
	
	$scope.screnshot_index = -1;
	// send second parameter true if want to check itemid before changing the fullview
	$scope.updateFullView = function(item_id, check){		
		if(check == true && typeof($scope.item) != 'undefined' && $scope.item.item_id != item_id){
			return true;
		}

		// if item id is null , due to invalid alias
		if(!item_id){
			return true;
		}

		$scope.version_model = '';
		$scope.item = $scope.items[item_id];
		$scope.header_template = $scope.base_url+'fullview_header_'+ $scope.item.status +'.html';
		$scope.compatibility_template = $scope.base_url+'fullview_header_compatibility.html';
		
		$scope.item_compatibilities = $scope.get_compatibilities($scope.item);
			
		$scope.prefix_model = $scope.product_tag_alias;	
		$scope.default_prefix = $scope.product_tag_alias;
		$scope.reference = $scope.fromJson($scope.item.item_reference);
		$scope.screenshots = $scope.fromJson($scope.item.screenshot);
		$scope.screnshot_index = -1;
		
		//tracking
		var event_args = { 'item'    : $scope.items[item_id].title, 
						   'price'   : $scope.items[item_id].price
		};
		
		rbinstaller.tracking('viewed', event_args)
	};	
	
	$scope.setScrenshotIndex = function(index){
		$scope.screnshot_index = index;
	};
	
	$scope.changeBarMouseHover = function(value){
		$scope.prefix_model = value;
		rb.jQuery('.compatibility-info').css('height', '50px');
		rb.jQuery('.compatibility-spectrum').css('height', '20px');		
	};
	
	$scope.changeBarMouseLeave = function(value){
		$scope.prefix_model = value;		
		rb.jQuery('.compatibility-info').css('height', '55px');
		rb.jQuery('.compatibility-spectrum').css('height', '15px');		
	};	
	
	$scope.updateBarWidth = function(){
		$scope.version_model = this.version_list_model;
		// IMP : set the default prefix which will be displayed in spectrum
		if(typeof($scope.item_compatibilities[this.version_model][$scope.product_tag_alias]) == 'undefined'){
			for (var index in $scope.item_compatibilities[this.version_model]){
				if($scope.item_compatibilities[this.version_model].hasOwnProperty(index)){
					$scope.prefix_model = index;
				}
			}
			$scope.default_prefix = $scope.prefix_model;
		}
		else{
			$scope.prefix_model = $scope.product_tag_alias;
		}
		
		if(typeof($scope.item_compatibilities[this.version_model]) == 'undefined'){
			return 0;
		}
		
		var count = Object.keys($scope.item_compatibilities[this.version_model]).length;
		if(count == 0) {
			return 0;
		}
		
		this.barwidth_model = 100/count;
	};	
	
	$scope.hideFullDetailView = function(){
		rb.jQuery('#rbinstaller-fulldetail-view').slideUp(300);		
	};
	
	$scope.search();
	
	if($state.params.fullview_item_alias){
		var item_id = $scope.item_alias_map[$state.params.fullview_item_alias];
		$scope.updateFullView(item_id);
	}

	$scope.getCount = function(entity){
		if(typeof(entity) == 'object'){
			return Object.keys(entity).length;
		}
		
		return 0;
	};
	
	$scope.resetFilters = function(){
		$scope.filter_query = '';
		$scope.filter_tag = '';
		$scope.filter_type = '';
		$scope.filter_status = '';
		$scope.filter_subscription_status = '';
		$scope.filter_price = '';
	};
	
	$scope.applyFilter = function(){		
		if(typeof($cookies.filter_query) != 'undefined' && $cookies.filter_query != 'undefined'){
			$scope.filter_query = $cookies.filter_query;
		}
		
		var show_filter = false;
		if(typeof($cookies.filter_tag) != 'undefined' && $cookies.filter_tag != 'undefined' && $cookies.filter_tag.length > 0){
			$scope.filter_tag = $cookies.filter_tag;
			show_filter = true;
		}
		
		if(typeof($cookies.filter_type) != 'undefined' && $cookies.filter_type != 'undefined' &&  $cookies.filter_type.length > 0){
			$scope.filter_type = $cookies.filter_type;
			show_filter = true;
		}
		
		if(typeof($cookies.filter_status) != 'undefined' && $cookies.filter_status != 'undefined' && $cookies.filter_status.length > 0){
			$scope.filter_status = $cookies.filter_status;
			show_filter = true;
		}
		
		if(typeof($cookies.filter_subscription_status) != 'undefined' && $cookies.filter_subscription_status != 'undefined' && $cookies.filter_subscription_status.length > 0){
			$scope.filter_subscription_status = $cookies.filter_subscription_status;
			show_filter = true;
		}
		
		if(typeof($cookies.filter_price) != 'undefined' && $cookies.filter_price != 'undefined' && $cookies.filter_price.length > 0){
			$scope.filter_price = $cookies.filter_price;
			show_filter = true;
		}
		
		if(show_filter == true){
			rbinstaller.filters.toggle('#rbinstaller-toggle-filter');
		}
	};
	
    $scope.$watch('filter_tag',function(){        
        $scope.search();
        $cookies.filter_tag = $scope.filter_tag;
    });
    
    $scope.$watch('filter_status',function(){        
        $scope.search();
        $cookies.filter_status = $scope.filter_status;
    });
    
    $scope.$watch('filter_price',function(){        
        $scope.search();
        $cookies.filter_price = $scope.filter_price;
    });
    
    $scope.$watch('filter_type',function(){        
        $scope.search();
        $cookies.filter_type = $scope.filter_type;
    });
    
    $scope.$watch('filter_subscription_status',function(){        
        $scope.search();
        $cookies.filter_subscription_status = $scope.filter_subscription_status;
    });
    
    $scope.trackMessage = function(item_id){
    	//tracking
		var event_args = { 'item'    : $scope.items[item_id].title, 
						   'price'   : $scope.items[item_id].price,
						   'status'	 : $scope.item.status
		};
		rbinstaller.tracking('message', event_args);
	};
	
	$scope.trackReference = function(reference, item_id){
		var event_args = { 'item'    	: $scope.items[item_id].title, 
						   'reference'	: $scope.reference.documentation_link
		};
		
		if(reference == 'support'){
			event_args.reference = $scope.reference.support_link
		}
		
		rbinstaller.tracking(reference, event_args);
	};
	
	$scope.trackFilter = function(){        
		var event_args = {};
		
		if($scope.filter_tag){
			event_args.filter_tag = $scope.tag_id_map[$scope.filter_tag].title;
		}
		
		if($scope.filter_price){
			event_args.filter_price = $scope.filter_price;		
		}
		
		if($scope.filter_status){
			event_args.filter_status = $scope.filter_status;
		}
		
		if($scope.filter_type){
			event_args.filter_type = $scope.filter_type;
		}
		
		if($scope.filter_subscription_status){
			event_args.filter_subscription = $scope.filter_subscription_status;
		}
		
		rbinstaller.tracking('search', event_args);
	};

};
	
rb_appmanager_app.controller(controllers);

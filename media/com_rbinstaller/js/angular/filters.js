rb_appmanager_app.filter('item_filter', function() {	
	  return function(items, searchText) {	   
	    if (searchText == undefined) {
	        return items;
	    }
	    
		var result = {};
		if(typeof(searchText) != 'undefined'){
			searchText = searchText.replace(/ +(?= )/g,' ').split(' ');
			var regex = '';
			for(var searchIndex in searchText){	  
				if(searchText.hasOwnProperty(searchIndex)){
					regex += '(?=.*'+searchText[searchIndex] + ')';
				}
			}

			var searchRegx = new RegExp(regex, "i");	
    		for(var index in items) {		    		 
		    	if( items.hasOwnProperty(index) && ( items[index].title.search(searchRegx) >= 0  || items[index].description.search(searchRegx) >= 0)){
		    		result[index] = items[index];	    			
	    		}
	    	}
	    }

	    return result;
	  }
	});


rb_appmanager_app.filter('myapp_invoice_filter', function() {	
	  return function(invoices) {	    
		var result = {};
	    for(var index in invoices) {
	    	if( invoices.hasOwnProperty(index) && (invoices[index].status != rb_app_manager_invoice_status_paid && invoices[index].status != rb_app_manager_invoice_status_inprocess)){
	    		continue;
	    	}
	    	
	    	result[index] = invoices[index];
	    }

	    return result;
	  }
	});


rb_appmanager_app.filter('fullview_tag_filter', function() {	
	  return function(tags, item_id, tag_items) {	    
		var result = {};
	    for(var index in tags) {
	    	if(tags.hasOwnProperty(index) && (typeof(tag_items[tags[index].id]) != 'undefined' && tag_items[tags[index].id].indexOf(parseInt(item_id)) > -1)){
	    		result[index] = tags[index];
	    	}    	
	    }

	    return result;
	  }
	});

rb_appmanager_app.filter('expiration_time_filter', function() {	
	  return function(raw_time) {	
		  	if(raw_time == "000000000000"){
		  		return "Life time";
		  	}
		  	
		  	var str_time = "";
		  	var strings = Array('Year', 'Month', 'Day', 'Hour', 'Minute', 'Second');
		  		
			for(var count = 0; count <= 5 ; count++){
				var value = parseInt(raw_time.slice(count*2, (count*2)+2));				
				if(value > 0){
					str_time += value + ' '+strings[count];					
					if(value > 1){
						str_time += 's';						
					}
					str_time += ' ';
				}
			}
			
			return str_time;
	  }
});

rb_appmanager_app.filter('tag_filter', function() {	
	  return function(items, tag, tag_items) {
		if(typeof(tag) != 'undefined' && tag.length > 0 && typeof(tag_items[tag]) != 'undefined'){
			var result = {};
			var tag_item = tag_items[tag];
			
			for(var searchIndex in tag_item){	 
				if(tag_item.hasOwnProperty(searchIndex)){
					result[tag_item[searchIndex]] = items[tag_item[searchIndex]];
				}
					
			}
			
			return result;
	    }

	    return items;
	  }
	});

rb_appmanager_app.filter('type_filter', function() {	
	  return function(items, type) {
		if(typeof(type) != 'undefined' && type.length > 0){
			var result = {};
			for(var searchIndex in items){
				if(items.hasOwnProperty(searchIndex) && items[searchIndex].type == type){
					result[searchIndex] = items[searchIndex];
				}
			}
			
			return result;
	    }

	    return items;
	  }
	});

rb_appmanager_app.filter('status_filter', function() {	
	  return function(items, status) {
		if(typeof(status) != 'undefined' && status.length > 0){
			
			var all_status = {installed : {none_installed : true, active_installed : true, expired_installed : true,
											none_upgradable : true, active_upgradable : true, expired_upgradable : true},
							  upgradable: {none_upgradable : true, active_upgradable : true, expired_upgradable : true},
							  not_available: {not_available : true},
							  not_installed: {none_addedtocart : true, none_buynow : true, free_install : true, active_install : true, expired_install : true}
							};
			var result = {};
						
			for(var searchIndex in items){
				if(items.hasOwnProperty(searchIndex) && typeof(all_status[status][items[searchIndex].status]) != 'undefined'){
					result[searchIndex] = items[searchIndex];
				}
			}
			
			return result;
	    }

	    return items;
	  }
	});

rb_appmanager_app.filter('subscription_status_filter', function() {	
	  return function(items, status) {
		if(typeof(status) != 'undefined' && status.length > 0){
			
			var all_status = {active : {active_installed : true, active_upgradable : true, free_install : true, active_install : true},
							  expired : {expired_installed : true, expired_upgradable : true, expired_install : true},
							};
			var result = {};
						
			for(var searchIndex in items){
				if(items.hasOwnProperty(searchIndex) && typeof(all_status[status][items[searchIndex].status]) != 'undefined'){
					result[searchIndex] = items[searchIndex];
				}
			}
			
			return result;
	    }

	    return items;
	  }
	});

rb_appmanager_app.filter('price_filter', function() {	
	  return function(items, price) {
		if(typeof(price) != 'undefined' && price.length > 0){
			var result = {};
			for(var searchIndex in items){
				if(!items.hasOwnProperty(searchIndex)){
					continue;
				}
				if(price == 'free' && parseFloat(items[searchIndex].price) == parseFloat(0)){
					result[searchIndex] = items[searchIndex];
				}
				else if(price == 'paid' && parseFloat(items[searchIndex].price) > parseFloat(0)){
					result[searchIndex] = items[searchIndex];
				}
				else{
					// do nothing
				}
			}
			
			return result;
	    }

	    return items;
	  }
	});

rb_appmanager_app.filter('orderObjectBy', function() {
	  return function(items, field, reverse) {
	    var filtered = [];
	    angular.forEach(items, function(item) {
	      filtered.push(item);
	    });
	    
	    filtered.sort(function (a, b) {
	      return (a[field] > b[field] ? 1 : -1);
	    });
	    
	    if(reverse) filtered.reverse();
	    return filtered;	  
	  };
	});

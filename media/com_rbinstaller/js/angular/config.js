var rb_appmanager_app = angular.module('rb_appmanager_app', ['ui.router', 'ngCookies']);

var $AnchorScrollProvider = function() {
  this.$get = ['$window', '$location', '$rootScope', function($window, $location, $rootScope) {
    function scroll() {
    }
    return scroll;
  }];
}

rb_appmanager_app.provider('$anchorScroll', $AnchorScrollProvider);

rb_appmanager_app.config(function($stateProvider, $urlRouterProvider, $cookiesProvider){
    
    // For any unmatched url, send to /route1
    $urlRouterProvider.otherwise("/app")
    
    $stateProvider
      .state('app', {
          url: "/app",
          controller: 	'AppController',
          templateUrl: rbinstaller_base_url+'list.html'
      })
        .state('app.list', {
            url: "/{fullview_item_alias}",
            templateUrl: rbinstaller_base_url+'list.html',
            controller: 	'AppController'
        })
  });
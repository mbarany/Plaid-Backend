var cache = {
    accounts: {}
};

angular.module('myApp', ['ngRoute'])

    .controller('MainController', function($scope, $route, $routeParams, $location) {
        $scope.$route = $route;
        $scope.$location = $location;
        $scope.$routeParams = $routeParams;
    })

    .controller('ListAccountController', function($scope, $http) {
        $scope.accounts = [];
        $http.get("/api/accounts")
            .success(function(data) {
                $scope.accounts = data;
            })
            .error(function() {
                console.log("Error!!!!");
            });
    })

    .controller('ListInstitutionController', function($scope, $http) {
        $scope.accounts = [];
        $http.get("/api/institutions")
            .success(function(data) {
                $scope.accounts = data;
            })
            .error(function() {
                console.log("Error!!!!");
            });
    })

    .controller('ViewAccountController', function($scope, $routeParams, $http) {
        if (cache.accounts[$routeParams.accountId]) {
            $scope.data = cache.accounts[$routeParams.accountId];
            return;
        }
        $scope.data = [];
        $http.get("/api/account/" + $routeParams.accountId)
            .success(function(data) {
                for (var i in data.accounts) {
                    var balance = data.accounts[i].info.balance.current;
                    for (var j in data.accounts[i].transactions) {
                        data.accounts[i].transactions[j].balance = balance;
                        balance = balance + data.accounts[i].transactions[j].amount;
                    }
                }
                cache.accounts[$routeParams.accountId] = data;
                $scope.data = data;
                console.log(data);
            })
            .error(function() {
                console.log("Error!!!!");
            });
    })

    .controller('AddAccountController', function($scope, $routeParams) {
        $scope.name = "AddAccountController";
        $scope.params = $routeParams;
    })

    .config(function($routeProvider, $locationProvider) {
        $locationProvider.hashPrefix('!');
        $routeProvider
            .when('/account/:accountId', {
                templateUrl: '/_/views/view-account.html',
                controller: 'ViewAccountController'
            })
            .when('/add/:accountId', {
                templateUrl: '/_/views/add-account.html',
                controller: 'AddAccountController'
            });
    });

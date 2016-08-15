(function(){

 var myApp = angular.module('json', []);
 
myApp.controller('jsonCtrl', function($http,$scope,$timeout) {


 
$scope.images = [
{'skyline': 'img/skyline.svg',
'road':'img/road1.png',
'vehicle':'img/truck.png'},
{'skyline': 'img/skyline.svg',
'road':'img/road1.png',
'vehicle':'img/pickup.png'},
{'skyline': 'img/skyline.svg',
'road':'img/road1.png',
'vehicle':'img/bus.png'},
{'skyline': 'img/skyline.svg',
'road':'img/rails.png',
'vehicle':'img/train.png'}            
];




$scope.mydata = [
{"id":1,
     "state": "suspended"},
    {"id":2,
     "state": "suspended"},
     {"id":3,
     "state": "suspended"},
     {"id":4,
     "state": "suspended"}

];

$scope.getData = function () {
    $http({
       method : "GET",
       url : "http://crossroad.local/api/lights",
       headers: { 'X-CROSSROAD-AUTH': 'trzasq' },
   }).then(function mySucces(response) {
    
     
        $scope.mydata = response.data;
     

       
   }, function myError(response) {
       $scope.error = response.statusText;
   console.log($scope.error);
   });
};

$scope.intervalFunction = function(){
    $timeout(function() {
      $scope.getData();
      $scope.intervalFunction();
    }, 1000)
  };



$scope.getData();
$scope.intervalFunction();

   });






})();
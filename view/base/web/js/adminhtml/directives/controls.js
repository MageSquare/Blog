// Generated by CoffeeScript 1.9.3

/*
 * MagPleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE.txt
 *
 * @category   Magpleasure
 * @package    Magpleasure_Blog
 * @copyright  Copyright (c) 2012-2021 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE.txt
 */


/*
  Directives for Blog Pro Additional Controls
 */

(function() {
  var module;

  module = angular.module('mpBlogControls', []);

  module.directive('mpSelector', [
    function() {
      return {
        restrict: 'C',
        scope: {
          'mpName': '@',
          'mpValue': '@',
          'mpOptions': '='
        },
        templateUrl: 'mpblog/selector.html',
        controller: [
          '$scope', function($scope) {
            $scope.name = $scope.mpName;
            $scope.options = $scope.mpOptions;
            $scope.radio = {
              value: $scope.mpValue
            };
            $scope.setValue = function(value) {
              return $scope.radio.value = value;
            };
            $scope.isActive = function(value) {
              return $scope.scope.radio.value === value;
            };
            return $scope;
          }
        ]
      };
    }
  ]);

}).call(this);

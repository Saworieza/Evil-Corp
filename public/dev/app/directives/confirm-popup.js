app.directive("lkrConfirmPopup", function($compile, $parse) {
    return {
        restrict: 'AE',
        replace: false,
        terminal: true,
        priority: 1000,
        scope: {
            lkrConfirmPopup: '@',
            lkrConfirmCb: '&',
            cbValue: '@lkrConfirmCbValue'

        },

    link: function link(scope,element, attrs) {

            scope.retValue = scope.cbValue; //Dont remove this (weird)

            scope.okClick = function()
            {
                var cbValue = scope.$eval(scope.retValue);
                scope.lkrConfirmCb(cbValue);
            }

            scope.options = scope.$eval(scope.lkrConfirmPopup);

            element.attr("popup-show", "directives/pop-confirmation.html");
            element.attr('tooltip-placement', 'bottom');
            element.removeAttr("lkr-confirm-popup"); //remove the attribute to avoid indefinite loop

            $compile(element)(scope);


    }
    };
});
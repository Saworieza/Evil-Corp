app.directive('lkrMessageItem', function($q, apiService, formatterService, $sce) {
    return {
        restrict: 'EA',
        templateUrl: 'directives/message-item.html',
        scope: {
            message: '=message'

        },

        link: function(scope, element, attrs) {

            scope.showReply = false;
            scope.message.replyText = '';
            scope.formatter = formatterService;

            scope.body = $sce.trustAsHtml(scope.message.body);
            scope.sent = angular.isDefined(attrs.sent);

            scope.showReplyClick = function()
            {
                scope.showReply = ! scope.showReply;

            }

            scope.sendClick = function()
            {
                scope.$emit('replyMessage', {id: scope.message.id, body: scope.message.replyText});
                scope.showReply = false;
                scope.message.replyText = '';

            }

            scope.markAsReadClick = function()
            {
                scope.$emit('markMessageAsRead', scope.message.id);
            }

        }

 }})

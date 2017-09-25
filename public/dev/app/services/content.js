app.service('contentService', function($q, apiService){
    var currentSpaceCode = null;

    var contentClasses = {
        0:{name:'message'},
        1:{name:'link'},
        2:{name:'table'},
        3:{name:'chart'},
        4:{name:'poll', validate: "newContent.poll.options.length > 1"},
        5:{name:'event'},
        6:{name:'location'}
    };

    return {
        setSpace: function(spaceCode) {
            currentSpaceCode = spaceCode;
        },

        getStream : function(params){
            return apiService.get(['content/space', currentSpaceCode], params);
        },

        pool : function(maxId){
          return apiService.get(['pool', currentSpaceCode, maxId] );
        },

        getOne : function(contentId) {
            return apiService.get(['content', contentId], {truncate: false});
        },

        getTasks: function(contentId) {
            return apiService.get(['task'], {content_id: contentId});
        },

        prepareNewContent : function(prevComment){

            var newContent = {};
            newContent.content_text = '';
            if(! isEmpty(prevComment))
            {
                newContent.content_text = prevComment;
            }

            newContent.waiting = false;
            newContent.location = { address: '', zoom: 14, done: false, waiting: false},
            newContent.link = { title: '', image: '', url: '', description: '', icon:'', done:false, waiting: false};
            newContent.files = [];
            newContent.table = {
                cols: [{ type: 'text', title: translations['NEW_COLUMN']},
                    { type: 'text', title: translations['NEW_COLUMN']},
                    { type: 'text', title: translations['NEW_COLUMN']}
                ],
                rows: [ ['','','']]
            };
            newContent.poll  = {allow_add: false, options:['']};
            newContent.event = {start_date: null, end_date:null, all_day:false, type:''};
            newContent.chart = {
                chartType : 'line',
                labels: [ translations['SERIE'] ],
                xkey: 'xlabel',
                skeys:[1],
                resize: true,
                parseTime: false,
                hideHover: 'auto',
                data:[
                    {xlabel: translations['LABEL'], s1:0}
                ]
            };

            return newContent;
        },

        delete: function(contentId){
            return apiService.delete(['content', contentId]);
        },

        viewMore: function(contentId){
            return apiService.get(['viewmore', contentId])
        },

        post : function(newContent, shareType){
            var deferred = $q.defer();
            var errors = {};

            var payLoad = {
                content_text: newContent.content_text,
                files: newContent.files
            };

            if(newContent.content_text == ''){
                errors['content_text'] = translations['CONTENT_FIELD_REQUIRED'];
            }

            var className = contentClasses[shareType].name;

            payLoad[className] = newContent[className];

            if( angular.isDefined(contentClasses[shareType].validate)){
                if(! eval(contentClasses[shareType].validate)){
                   errors['validation'] = false;
                };
            };

            if(shareType == 5){ //event

                if(! moment(newContent.event.start_date).isValid()) {
                    errors['event.start_date'] = 'Invalid date';

                }
                if(! moment(newContent.event.end_date).isValid()) {
                    errors['event.end_date'] = 'Invalid date';
                }

                if(newContent.event.all_day) {
                    newContent.event.start_date.setHours(12,00,00);
                    newContent.event.end_date.setHours(12,00,00);
                }

                if(newContent.event.end_date < newContent.event.start_date) {
                    errors['event.end_date'] = 'Invalid date';
                }
                payLoad['event'] = newContent.event;
            }

            if(! jQuery.isEmptyObject(errors) )
            {

                deferred.reject(errors)

            } else {

                return apiService.post(['content',currentSpaceCode, shareType], payLoad);

            }
            return deferred.promise;
        }
    }

})
app.service('formatterService', function(){

    this.formatDateCalendar = function(dte) {
        if(!isEmpty(dte))
        {
            dte = moment.utc(dte);
            dte.local();
            return moment(dte).calendar();
        } else {
            return "";
        }

    };

    this.formatDateCalendarUtc = function(dte) {

        if(!isEmpty(dte))
        {
            dte = moment.utc(dte);
            dte.local();
            return moment(dte).calendar();
        } else {
            return "";
        }
    };

    this.formatDateTime = function(dte) {
        if(isEmpty(dte)) {
            return "";
        } else {
            dte = moment.utc(dte);
            dte.local();
            return dte.format('L h:mm A');
        }
    };

    this.formatDate = function(dte) {
        if(isEmpty(dte)) {
            return "";
        } else {
            dte = moment.utc(dte);
            dte.local();
            return dte.format('MMM D YYYY');
        }
    };

    this.isMe = function(id)
    {

        return id == user.id;
    };

    this.longDate = function(dte){

        if(!dte) return null;

        var d = moment(dte);
        if(d.isValid()) return d.format('LLLL')

        return null;
    };



    this.formatMonth = function(dte) {
        var d = moment(dte);
        if(d.isValid()) return d.format('MMM')

        return null;
    };
    this.formatDay = function(dte) {
        return moment(dte).format('D');
    };
    this.formatFileSize = function(asize){
        if(asize == '' || asize == null) return '';
        var asize = parseInt(asize);
        if(asize > 1000000) return (asize/1000000).toFixed(2) + ' MB';
        if(asize > 1000) return (asize/1000).toFixed(2) + ' KB';
        return asize + ' Bytes';
    }

    this.getMyAvatar = function()
    {
        return this.getAvatar(user.id);
    };

    this.getAvatar = function(id) {

        return assetsUrl + '/avatar/' + id + '.jpg';
    }

    this.getFullName = function()
    {
        return user.fullname;
    }

})

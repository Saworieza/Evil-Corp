app.filter('joinBy', function () {
        return function (input,delimiter) {
            if( Object.prototype.toString.call( input ) === '[object Array]' ) {
                return (input || []).join(delimiter || ',');
            } else {
                return input;
            }
        };
    });

app.filter('words', function () {
    return function (input, words) {
        if (isNaN(words)) {
            return input;
        }
        if (words <= 0) {
            return '';
        }
        if (input) {
            var inputWords = input.split(/\s+/);
            if (inputWords.length > words) {
                input = inputWords.slice(0, words).join(' ') + '\u2026';
            }
        }
        return input;
    };
});
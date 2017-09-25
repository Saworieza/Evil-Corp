// Include gulp
var gulp = require('gulp'); 

// Include Our Plugins
var jshint = require('gulp-jshint');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var less = require('gulp-less');
var minifyCSS = require('gulp-minify-css');
var angularTemplates = require('gulp-angular-templatecache');
var shell = require('gulp-shell');
var minifyHTML = require('gulp-minify-html');
var lessImport = require('gulp-less-import');
var ngAnnotate = require('gulp-ng-annotate');


//Templates
gulp.task('templates', function () {
    return gulp.src('public/dev/app/templates/**/*.html')
        .pipe(minifyHTML({empty:true}))
        .pipe(angularTemplates('app.tpls.min.js', {module:"app" }))
        .pipe(gulp.dest('public/build'));
});


gulp.task('themes', function () {

    var themes = ['darkblue', 'red', 'green', 'lightblue', 'ubu', 'dark', 'teal'];

    for(var i=0; i<themes.length; i++)
    {
        gulp.src('public/dev/styles/less/color-'+themes[i]+'.less')
            .pipe(less())
            .pipe(minifyCSS())
            .pipe(rename(themes[i]+'.min.css'))
            .pipe(gulp.dest('public/build'));

    }


});

gulp.task('less', function () {
  gulp.src('public/dev/styles/less/app.less')
    .pipe(less())
      .pipe(minifyCSS())
    .pipe(rename('default.min.css'))
    .pipe(gulp.dest('public/build'));
});

// Concatenate & Minify App
gulp.task('scripts', function() {
    return gulp.src('public/dev/app/**/*.js')
        .pipe(concat('app.js'))
        .pipe(ngAnnotate())
        .pipe(uglify())
        .pipe(rename('app.min.js'))
        .pipe(gulp.dest('public/build'));
});

// dump components
gulp.task('components', function(){
    return gulp.src(['public/dev/components/jquery/dist/jquery.min.js',
                     'public/dev/components/underscore/underscore-min.js',
                     'public/dev/components/bootstrap/dist/js/bootstrap.min.js',
                     'public/dev/components/angular/angular.min.js',
                     'public/dev/components/angular-route/angular-route.min.js',
                     'public/dev/components/angular-sanitize/angular-sanitize.min.js',
                     'public/dev/components/angular-animate/angular-animate.min.js',
                     'public/dev/components/angular-strap/dist/angular-strap.min.js',
                     'public/dev/components/angular-strap/dist/angular-strap.tpl.min.js',
                     'public/dev/components/angular-bindonce/bindonce.min.js',
                     'public/dev/components/ng-file-upload/angular-file-upload.min.js',
                     'public/dev/components/lightbox/dist/ekko-lightbox.min.js',
                     'public/dev/components/ment.io/dist/mentio.min.js',
                     'public/dev/components/angular-ui-router/release/angular-ui-router.min.js',
                     'public/dev/components/moment/min/moment-with-locales.min.js',
                     'public/dev/components/morris/raphael-min.js',
                     'public/dev/components/angular-datepicker/index.min.js',
                     'public/dev/components/morris/morris.js',
                     'public/dev/components/angular-elastic/elastic.js',
                     'public/dev/components/angular-growl/build/angular-growl.js',
                     'public/dev/components/ng-tags-input/ng-tags-input.min.js',
                     'public/dev/components/ng-infinite-scroll/ng-infinite-scroll.js',
                     'public/dev/components/summernote/dist/summernote.min.js',
                     'public/dev/components/angular-spinner/spinner.js',
                     'public/dev/components/angular-spinner/angular-spinner.min.js',
                     'public/dev/components/checklist-model/checklist-model.js',
                     'public/dev/components/ui-calendar/src/calendar.js',
                     'public/dev/components/fullcalendar/dist/fullcalendar.min.js',
                     'public/dev/components/fullcalendar/dist/lang-all.js',
                     'public/dev/components/sparkline/jquery-sparkline.min.js',
                     'public/dev/components/angular-translate/angular-translate.min.js',
                     'public/dev/components/angular-tree-control/angular-tree-control.js'

        ])
            .pipe(concat('lib.min.js'))
            .pipe(gulp.dest('public/build/'));
});

// dump basic components (auth pages)
gulp.task('basiclib', function(){
    return gulp.src(['public/dev/components/jquery/dist/jquery.min.js',
                     'public/dev/components/angular/angular.min.js',
                     'public/dev/components/angular-sanitize/angular-sanitize.min.js',
                     'public/dev/components/angular-animate/angular-animate.min.js',
                     'public/dev/components/angular-growl/build/angular-growl.js'
        ])
        .pipe(concat('basiclib.min.js'))
        .pipe(gulp.dest('public/build/'));
});


// Watch Files For Changes
gulp.task('watch', function() {
    gulp.watch('public/dev/app/**/*.js', ['scripts']);
    gulp.watch('public/dev/styles/less/*', ['less']);
    gulp.watch('public/dev/styles/bootstrap/*.less', ['less']);
    gulp.watch('public/dev/app/templates/**/*.html', ['templates']);
});

gulp.task('build', ['templates', 'less', 'scripts', 'components'])
// Default Task
gulp.task('default', ['watch']);

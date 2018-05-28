/**
 * Created by andery on 13-12-6.
 */
var require_config ={
    paths: {
        text: '/assets/js/require/text',
        underscore: '/assets/js/underscore/underscore',
        backbone: '/assets/js/backbone/backbone',
        backboneValidate: '/assets/js/backbone/backbone-validation-amd',
        backbonePaginator: '/assets/js/backbone/backbone.paginator',
        components: '/assets/js/components/',
        jquery: '/assets/js/jquery/jquery-1.10.2.min',
        bootstrap: '/assets/js/bootstrap/bootstrap.min',
        nprogress: '/assets/js/jquery/nprogress',
        dialog: '/assets/js/dialog/dialog',
        dialogPlus: '/assets/js/dialog/dialog-plus',
        dialogConfig: '/assets/js/dialog/dialog-config',
        popup: '/assets/js/dialog/popup',
        drag: '/assets/js/dialog/drag',
        validform: '/assets/js/jquery/Validform_v5.3.2',
        clipboard: '/assets/js/clipboard/ZeroClipboard',
        tagsinput: '/assets/js/bootstrap/bootstrap-tagsinput.min',
        moment: '/assets/js/moment.min',
        dateRangePicker: '/assets/js/bootstrap/daterangepicker',
        calendar: '/assets/js/calendar/lhgcalendar.min',
        fineuploader: '/assets/js/jquery/fineuploader.min',
        typeahead: '/assets/js/typeahead/backbone.typeahead.min',
        colorselector: '/assets/js/colorselector/bootstrap-colorselector',
        copy: '/assets/js/copy/copy',
        highCharts: '/assets/js/chart/highcharts',
        bootstrapSwitch: '/assets/js/bootstrap/bootstrap-switch',
        map: '/assets/js/map/map',
        ueditor_config: '/assets/js/ueditor/ueditor.config',
        ueditor: '/assets/js/ueditor/ueditor.all',
        ueditor_defined: '/assets/js/ueditor',
        //bmap: 'http://api.map.baidu.com/getscript?v=2.0&ak=D5894ef9e6eb52db5afab2366bbbef58&services=&t=' + (new Date()).getTime()
    },
    shim: {
        underscore: {
            exports: '_'
        },
        backbone: {
            deps: [
                'underscore',
                'jquery'
            ],
            exports: 'Backbone'
        },
        backboneValidate: {
            deps: ['backbone'],
            exports: 'backboneValidate'
        },
        backbonePaginator: {
            deps: ['backbone'],
            exports: 'backbonePaginator'
        },
        bootstrap: {
            deps: ['jquery'],
            exports: 'Bootstrap'
        },
        dialog: {
            deps: ['jquery'],
            exports: 'Dialog'
        },
        nprogress: {
            deps: ['jquery'],
            exports: 'Nprogress'
        },
        validform: {
            deps: ['jquery'],
            exports: 'BalidForm'
        },
        tagsinput: {
            deps: ['jquery']
        },
        calendar: {
            deps: ['jquery'],
            exports: 'Calendar'
        },
        highCharts: {
            deps: ['jquery'],
            exports: 'highCharts'
        },
        dateRangePicker: {
            deps: ['jquery', 'moment'],
            exports: 'DateRangePicker'
        },
        fineuploader: {
            deps: ['jquery'],
            exports: 'Uploader'
        },
        typeahead: {
            deps: ['jquery', 'backbone'],
            exports: 'Typeahead'
        },
        colorselector:{
            deps: ['jquery', 'backbone'],
            exports: 'ColorSelector'
        },
        copy: {
            deps: ['jquery'],
            exports: 'copy'
        },
        bootstrapSwitch: {
            deps: ['jquery','bootstrap'],
            exports: 'jQuery.fn.bootstrapSwitch'
        },
        map: {
            deps: ['jquery'],
            exports: 'map'
        },
        bmap: {
            exports: 'bmap'
        },
        ueditor: {
            deps: ['ueditor_config'],
            exports: 'ueditor'
        },
        ueditor_defined: {
            deps: ['jquery'],
            exports: 'ueditor_defined'
        },
    },
    urlArgs: "bust=" +  (new Date()).getTime()
};
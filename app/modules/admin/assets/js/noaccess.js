/**
 * Created by river on 14-11-10.
 */
require.config(require_config);

define([
    'jquery',
    'components/kunrou',
    'nprogress'
], function( $, kunrou, Nprogress ){
    Nprogress.start();

    Nprogress.done();

});
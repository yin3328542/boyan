/**
 * Created by river on 14-10-23.
 */
require.config(require_config);
define(['jquery', 'components/collection'], function($, Collection) {
    //数据分页
    var ct = new Collection;
    var paginated = function() {
        var html = '<ul class="pagination">';
        var page_size = ct.page.page_size;
        if(ct._count < page_size) {
            return false;
        }
        ct.page.page_total = Math.ceil(ct._count / page_size);
        if(ct.page.page_total <= ct.page.page_max) {
            html += '<li><a href="#">&laquo;</a></li>';

        }
    };
});
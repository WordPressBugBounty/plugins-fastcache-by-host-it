/**
 * FastCache - performs several front-end optimizations for fast downloads
 *
 * @package   FastCache
 * @author    Host.it <info@host.it>
 * @copyright Copyright (c) 2025-2026 FastCache
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

var fastcacheMultiselect = (function ($) {
    $(document).ready(function () {

        var timestamp = getTimeStamp();
        var datas = [];
        //Get all the multiple select fields and iterate through each
        $('select.fastcache-multiselect').each(function () {
            var el = $(this);

            datas.push({
                'id': el.attr('id'),
                'type': el.attr('data-fastcache_type'),
                'param': el.attr('data-fastcache_param'),
                'group': el.attr('data-fastcache_group')
            });

        });

        var xhr = $.ajax({
            dataType: 'json',
            url: adminUtilities.fastcache_ajax_url_multiselect + '&_=' + timestamp,
            data: { 'data': datas },
            method: 'POST',
            success: function (response) {
                $.each(response.data, function (id, obj) {

                    const select = $("#" + id);

                    $.each(obj.data, function (value, option) {
                        select.append('<option value="' + value + '">' + option + '</option>');
                    });


                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error returned from ajax function \'getmultiselect\'');
                console.error('textStatus: ' + textStatus);
                console.error('errorThrown: ' + errorThrown);
                console.warn('response: ' + jqXHR.responseText);
            },
            complete: function () {
                //Remove all loading images
                $('img.fastcache-multiselect-loading-image').each(function () {
                    $(this).remove();
                });
            }
        });
    });
})(jQuery);




define(['jquery'], function($) {
    return {
        init: function(targetSelector) {
            $('#selectall').on('change', function() {
                $(targetSelector).prop('checked', $(this).prop('checked'));
            });
        }
    };
});

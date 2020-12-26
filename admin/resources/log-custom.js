jQuery(document).ready(function($) {
    $('a.wure-view-in-popup').on('click touch', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var column = $(this).data('column');
        var data = {
            'action': 'wure_view_logs',
            'security': ajax_object.security,
            'id': id,
            'column': column
        };
        jQuery.post(ajax_object.ajax_url, data, function (response) {
            $(response).dialog({
                modal: true,
                width: 500,
                height: 'auto',
                draggable: false,
                resizable: false,
                title: column,
                dialogClass: "wure-log-dialog",
                position: {my: "center", at: "center", of: window},
            })
        })
    })
})
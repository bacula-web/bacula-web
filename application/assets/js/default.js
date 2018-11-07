$(document).ready(function() {

    /* DataTable */
    $('table.paginate').DataTable( {
            searching: false,
            ordering: false,
            lengthMenu: [ 25, 50, 75, 100 ]
    } );

    /* Hide graph images for few milliseconds */
    $("img").hide();
    $(".img_loader").delay(200).fadeOut(300);
    $("img").delay(500).fadeIn(600);
    
    /* Bootstrap Tooltip */
    $('[data-toggle="tooltip"]').tooltip();

    // Set DateTimePicker locale and date/time format
    $('.datetimepicker').datetimepicker({
        locale: config_language,
        format: 'YYYY-MM-DD HH:mm:ss'
    });
});

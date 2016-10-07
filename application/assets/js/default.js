$(document).ready(function(){
    /* Hide graph images for few milliseconds */
    $("img").hide();
    $(".img_loader").delay(200).fadeOut(300);
    $("img").delay(500).fadeIn(600);
    
    /* Bootstrap Tooltip */
    $('[data-toggle="tooltip"]').tooltip();

    $('.datetimepicker').datetimepicker({
        locale: config_language,
        format: 'YYYY-MM-DD HH:mm:ss'
    });
});

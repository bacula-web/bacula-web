$(document).ready(function(){
    /* Bootstrap Tooltip */
    $('[data-toggle="tooltip"]').tooltip();

    /* Hide graph images for few milliseconds */
    $("img").hide();
    /* The code below does not work :( 
    $(".img_loader").delay(1000).fadeOut(500);
    */
    $("img").delay(500).fadeIn('slow');
});           

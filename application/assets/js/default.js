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

function bytes(bytes, label, scale) {
    if (bytes == 0) return '';
    if(scale){
	    var mm = 1000;
	    var s = ['', 'k', 'M', 'G', 'T', 'P'];
	}else{
		var mm = 1024;
		var s = [' bytes', ' KB', ' MB', ' GB', ' TB', ' PB'];
	}
    var e = Math.floor(Math.log(bytes)/Math.log(mm));
    var value = ((bytes/Math.pow(mm, Math.floor(e))).toFixed(2));
    e = (e<0) ? (-e) : e;
    if (label) value += s[e];
    return value;
}
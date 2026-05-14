import {TempusDominus} from '@eonasdan/tempus-dominus';

// set date/time picker locale from current web browser locale
const userLocale =
    navigator.languages && navigator.languages.length
        ? navigator.languages[0]
        : navigator.language;

var starttime = document.getElementById("starttime");
if(starttime) {
    new TempusDominus(document.getElementById('starttime'), {
        localization: {
            format: 'yyyy-MM-dd HH:mm:ss',
            locale: userLocale
        }
    });
}

var endtime = document.getElementById("endtime");
if(endtime) {
    new TempusDominus(document.getElementById('endtime'), {
        localization: {
            format: 'yyyy-MM-dd HH:mm:ss',
            locale: userLocale
        }
    });
}
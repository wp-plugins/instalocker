jQuery( document ).ready(function(){
	var followed = getParameterByName('followed');
	var username = getParameterByName('username');

	if(followed && username) {
		if (document.cookie.indexOf('instafollow-lock_'+instafollow_content_locker.ID) >= 0) {
			//do nothing
		}else{
			//check if user actually followed
			checkrelationship(username,to);
		}
	}
	if(document.location.href.indexOf('instalockersettings') >=0 && username) {
		jQuery('#mt_instagram_username').val(username);
		jQuery('#instalockeradminform').submit();
	}
	jQuery('.instafollow-button').click(function(){
		var dataAttributes = jQuery(this).data();
		var query = '';
		for(var i in dataAttributes){
			query += i+'='+dataAttributes[i]+'&';
		}
		query += 'utm_source=instafollow-button';

		document.location = "http://instafollow.in/followbutton/instagram/"+to+"/?"+query;
	});
});	

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function checkrelationship(from,to){
	jQuery.ajax({
		url: "http://instafollow.in/relationships/checkrelationship/",
		// the name of the callback parameter, as specified by the YQL service
		jsonp: "callback",
		// tell jQuery we're expecting JSONP
		dataType: "jsonp",
		// tell YQL what we want and that we want JSON
		data: {
			from: from,
			to: to
		},
		// work with the response
		success: function( response ) {
			if(response=='follows') {
				createCookie('instafollow-lock_'+instafollow_content_locker.ID,true,9999);
				location.reload(); //if you use cache maybe you want to add a ?nocache=true to this url
			}
		}
	});
}

function createCookie( name, value, days ) {
    var expires;
    if ( days ) {
        var date = new Date();
        date.setTime( date.getTime() + (days * 24 * 60 * 60 * 1000) );
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = escape( name ) + "=" + escape( value ) + expires + "; path=/";
}
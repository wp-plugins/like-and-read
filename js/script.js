jQuery( document ).ready(function(){
	//Create cookie for the URL on Facebook like event
	FB.Event.subscribe( 'edge.create', function( href ) {
		createCookie(href,true,9999);
		//Want to use Ajax here
		location.reload();
	});
	
	FB.Event.subscribe( 'edge.remove', function( href ) {
		deleteCookie(href);
		//Want to use Ajax here
		location.reload();
	});
});	

//Create cookie function
function createCookie( name, value, days ) {
  var expires;
  if ( days ) {
    var date = new Date();
    date.setTime( date.getTime() + (days * 24 * 60 * 60 * 1000) );
    expires = "; expires=" + date.toGMTString();
    
  } else {
    expires = "";
  }
  
  //Apparently cookie name does not like special chars
  var n = name.replace(/[^a-z0-9]/gi,'');
  
  //Cookie value set to 'true' with name as only chars of URL
  document.cookie = n + "=" + escape( value ) + expires + "; path=/";
}

//Delete cookie function
function deleteCookie(name) {
	var n = name.replace(/[^a-z0-9]/gi,'');
  createCookie(n,"",-1);
}
jQuery(document).ready(function() {
	jQuery('a').each(function() {
		var a = jQuery(this);
		var href = a.attr('href');
		// Check if the a tag has a href, if not, stop for the current link
		if ( href == undefined )
			return;
		
		var hrefArray = href.split('/').reverse();
		var domain = hrefArray[2];
		
		// If the link is external
	 	if ( ( href.match(/^http/) ) && ( !href.match(document.domain) ) ) {
	    	// Add the tracking code
			a.click(function() {
				if ( analyticsEventTracking == 'enabled' ) {
					pageTracker._trackEvent(eventName, keywordName, href);
				}});
		}
	});
});
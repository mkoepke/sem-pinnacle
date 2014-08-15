/**
 * Created by michaelkoepke on 3/19/14.
 */

jQuery(document).ready(function($) {

	// add hover class when list item is hovered
	$('#header_nav').find('li').hover(function() {
        $(this).addClass('hover');
	}, function() {
        $(this).removeClass('hover');
    });

	$('#header-menu-icon').click(function() {
		$('#header_nav').slideToggle();
	});

	var windowWidth = $(window).width();

	$(window).resize(function(){
		if(windowWidth > 649) {
			$('#header_nav').removeAttr("style");
		}
	});

	if(windowWidth <= 649) {
		$('#header_nav').find('li.nav_branch > a').click(function(e) {
			e.preventDefault();
			var $this = $(this);
			$this.toggleClass('open').next('ul').toggleClass('open');
		});
	}
});


/* ios viewport scaling bug fix - https://gist.github.com/mathiasbynens/901295 */
(function(doc) {

	var addEvent = 'addEventListener',
	    type = 'gesturestart',
	    qsa = 'querySelectorAll',
	    scales = [1, 1],
	    meta = qsa in doc ? doc[qsa]('meta[name=viewport]') : [];

	function fix() {
		meta.content = 'width=device-width,minimum-scale=' + scales[0] + ',maximum-scale=' + scales[1];
		doc.removeEventListener(type, fix, true);
	}

	if ((meta = meta[meta.length - 1]) && addEvent in doc) {
		fix();
		scales = [.25, 1.6];
		doc[addEvent](type, fix, true);
	}

}(document));

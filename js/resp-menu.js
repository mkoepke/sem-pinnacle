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


	$('#header_nav').before('<div id="header-menu-icon" class="fa fa-bars"></div>');
	$('#header-menu-icon').click(function() {
		$('#header_nav').slideToggle();
	});
	$(window).resize(function(){
		if(window.innerWidth > 649) {
			$('#header_nav').removeAttr("style");
		}
	});

	$('#header_nav').find('li.nav_branch > a').click(function(e) {
		e.preventDefault();
		var $this = $(this);
		$this.toggleClass('open').next('ul').toggleClass('open');
	});
});

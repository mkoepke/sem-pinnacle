<?php


class SemPinnacleUtils {

	/**
	 * init_widgets()
	 *
	 * @param int
	 * @return string
	 **/
	static function auto_copyright( $year = 'auto' ) {

		$copyright = '';

		if(intval($year) == 'auto') {
			$year = date('Y');
		}
	    if(intval($year) == date('Y')) {
		    $copyright = intval($year);
	    }
	    elseif(intval($year) < date('Y')) {
		    $copyright = intval($year) . ' - ' . date('Y');
	    }
	    elseif(intval($year) > date('Y')){
		    $copyright = date('Y');
	    }

		return $copyright;
	}

	/**
	 * get_site_name()
	 *
	 * @return string
	 **/
	static function get_site_name() {
		return get_bloginfo( 'name' );
	}

}

<?php
/**
 * Plugin Helpers
 *
 * Repeatable pieces of code to use through out the plugin.
 * 
 * @package   Prayer
 * @author    Kaleb Heitzman <kalebheitzman@gmail.com>
 * @link      https://github.com/kalebheitzman/prayer
 * @copyright 2016 Kaleb Heitzman
 * @license   GPL-3.0
 * @version   0.9.0
 */

class Prayer_Plugin_Helper
{
    /**
     * Parse Location
     *
     * Takes a location string, such as Raleigh, NC and passes it to the Google
     * geocode API. The API returns a JSON encoded response where location data
     * like latitude, longitude, country codes, etc are extracted.
     * 
     * @param  string User inputted location
     * @return array Parsed location data
     * @since 0.9.0 
     */
    static public function parse_location( $location = null ) {
        if ( is_null($location) ) return;
        
        // prep the address
        $prepAddr = str_replace(' ', '+', $location);
        // send the address to google
        $geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false');
        // decode the output
        $output= json_decode($geocode);
        // build a parsed location array to return
        $parsed_location['formatted_address'] = $output->results[0]->formatted_address; // Lexington, KY, USA
        $parsed_location['lat'] = $output->results[0]->geometry->location->lat;
        $parsed_location['long'] = $output->results[0]->geometry->location->lng;
        $parsed_location['country_long'] = $output->results[0]->address_components[3]->long_name;
        $parsed_location['country_short'] = $output->results[0]->address_components[3]->short_name;

        return $parsed_location;
    }

    /**
     * Save Location Data to Meta
     *
     * Saves location to different meta fields. I may serialize this into one meta
     * field for the future to save on db calls.
     * 
     * @param integer ID
     * @param array Parsed location data
     * @since 0.9.0
     */
    static public function save_location_meta( $id = 0, $location = null ) {
        if ( is_null($location) || $id == 0 ) return;

        update_post_meta( $id, 'prayer-location-latitude', $location['lat'] );
        update_post_meta( $id, 'prayer-location-longitude', $location['long'] );
        update_post_meta( $id, 'prayer-location-formatted-address', $location['formatted_address'] );
        update_post_meta( $id, 'prayer-location-country-long', $location['country_long'] );
        update_post_meta( $id, 'prayer-location-country-short', $location['country_short'] );
    }

    /**
     * Recursive Array Search
     * @param  String $needle  
     * @param  Array $haystack
     * @return boolean
     */
    static function in_array_rec($needle, $haystack) { 
       
        if ( in_array($needle, $haystack) ) { return true; } 
       
        foreach($haystack as $elem) 
        {
            if ( is_array($elem) && self::in_array_rec($needle, $elem) )
            {
                return true;
            }
        }
        return false; 
    }

}
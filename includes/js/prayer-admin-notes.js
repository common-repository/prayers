/**
 * Creates a new input element to be appended to the DOM that's used to represent a single
 * note (be it an address, tweet, image URL, etc.) to be referenced in the post.
 *
 * @since    0.4.0
 * @param    object    $    A reference to the jQuery object
 * @return   object         An input element to be appended to the DOM.
 */
function createInputElement( $ ) {
 
    var $inputElement, iInputCount;
 
    /* First, count the number of input fields that already exist. This is how we're going to
     * implement the name and ID attributes of the element.
     */
    iInputCount = $( '#prayer-notes' ).children().length;
    iInputCount++;
 
    // Next, create the actual input element and then return it to the caller
    $inputElement =
        $( '<textarea />' )
            .attr( 'name', 'prayer-notes[' + iInputCount + ']' )
            .attr( 'id', 'prayer-note-' + iInputCount )
            .attr( 'placeholder', 'Enter a note' );
  
    return $inputElement;
 
 
}
 
(function( $ ) {
    'use strict';
 
    $(function() {
 
        var $inputElement;
 
        $( '#prayer-add-note' ).on( 'click', function( evt ) {
 
            evt.preventDefault();
 
            /* Create a new input element that will be used to capture the users input
             * and append it to the container just above this button.
             */
            $( '#prayer-notes' ).append ( createInputElement( $ ) );
 
 
 
        });
 
    });
 
})( jQuery );
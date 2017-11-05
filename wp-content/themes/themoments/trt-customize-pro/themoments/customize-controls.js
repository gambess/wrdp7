( function( api ) {

	// Extends our custom "themoments" section.
	api.sectionConstructor['themoments'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );

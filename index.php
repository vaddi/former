<?php

// Target Url (form action or url to parse)
$url = "https://www.google.de/"; // for example google.com

// if we want to submitting a form, 
// edit this array to fit the input name(s) and input value(s)
$formData = array( 	
	// input fields by
	// name => value
	'q' => 'Former the php html parser'
);

// provide the Parser Class
require_once( __DIR__ . '/class/Parser.php' ); 
$result = null;

//
// examples
//

// find form action by (action url) segment 
//$parser = new Parser( $url, $formData, '/search', 'GET' );

// send formdata to url
//$parser = new Parser( $url, $formData );
 
// simple use the given url
$parser = new Parser( $url . 'search?q=' . str_replace( ' ', '%20', $formData['q'] ) );



// get the raw response
$result = $parser->rawResponse();

// get element by id
//$result = $parser->getById( 'tabNav-container' );

// get element by node
//$result = $parser->getByNode( 'table' );

// get element by node name and class value
//$result = $parser->getByNodeClass( 'tbody', 'scheduledCon' );

// get element by node name, node attribute value (null or '*', for all) and node attribute name
//$result = $parser->getByNodeAttribute( 'td', 'time', 'class' );

// get all href's inside of a td element
//$result = $parser->getByNodeAttribute( 'td', '*', 'href' );

// get all image sources 
//$result = $parser->getByNodeAttribute( 'img', '*', 'src' );



// Dirty output replace by you own
//echo "<pre>";
//print_r( $result );
//echo "</pre>";

echo utf8_encode( $result );

?>

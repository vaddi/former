<?php

class Parser {
	
	/**
	 * Class variables
	 */
	private $dom = null;
	private $response = null;
	
	/**
	 * Constructor
	 */
	public function __construct( $url = null, $formData = array(), $inform = false, $method = 'POST' ) {
		try {
			if( $url === null || $url === "" ) throw new Exception( 'No url given, abort.' );
			if( ! is_array( $formData) ) throw new Exception( 'formData musst be an array, abort.' );
			
			if( count( $formData ) > 0 ) {
				if( $inform ) {
					// find form action and send them the data
					$raw = file_get_contents( $url );
					// get temp response and buld temp dom
					$this->response = $raw;
					$this->buildDom();
					$forms = $this->getByNodeAttribute( 'form', '*', 'action' );
					$responseUrl = false;
					foreach ( $forms as $key => $form ) {
						// exclude some urls
						// if( $form === '' || $form === '/q' ) { unset( $forms[ $key ] ); continue; }
//						$form = str_replace( '//', '/', $form );
						if( strpos( $form, $inform ) !== false ) $responseUrl = $form;
					}
					if( $responseUrl ) {
						// brocken urls
						if( strpos( $responseUrl, 'http' ) === false ) {
							// example, fix missing hostname
							 $responseUrl = $this->getDomain( $url ) . $responseUrl;
							// example, fix missing scheme
							// $responseUrl = $this->getScheme( $url ) . $responseUrl;
//							$responseUrl = 'http:/' . $responseUrl; // simple prepend scheme
						}
						// finaly, get response from form
						$this->response = self::request( $responseUrl, $formData, $method );
					} else {
						// no form action suits, fallback to get response from url
//						$this->response = file_get_contents( $url );
						// or throw an exception
						throw new Exception( 'No suitable form action found, abort.' );
					}
				} else {
					// get the response from the given url (inform is false, formdata is options array > 0 )
					$this->response = self::request( $url, $formData, $method );
				}
			} else {
				// get the response from the given url (no formdata)
				$this->response = file_get_contents( $url );
			}
			
			// reset the correct dom
			$this->buildDom();
		} catch( Exception $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
	}
	
	
	/**
	 * Helper function to get the html instead of the object
	 * (include the node its self html!)
	 */
	public function toHTML( $object = null ) {
		return $this->dom->saveHTML( $object );
	}
	
	
	/**
	 * Simple get the raw response object
	 */
	public function rawResponse() {
		return $this->response;
	}
	
	/**
	 * Simple get all nodes by a given html node name (a, td, table, etc)
	 */
	public function getByNode( $node = null ) {
		$result = false;
		foreach ( $this->dom->getElementsByTagName( $node ) as $element ) {
			$result[] = $element;
		}
		return $result;
	}
	
	
	/**
	 * return elemen by given css id
	 */
	public function getById( $id = null ) {
		return $this->dom->getElementById( $id );
	}
	
	
	/**
	 * return elements by given css class
	 */
	public function getByNodeClass( $node = null, $attribute = null ) {
		return $this->getByNodeAttribute( $node, $attribute );
	}
	
	
	/**
	 * get element by node name, node attribute value (null or '*', for all) and node attribute name
	 */
	public function getByNodeAttribute( $node = null, $attribute = null, $type = null ) {
		$result = false;
		if( $type === null ) $type = 'class';
		foreach ( $this->dom->getElementsByTagName( $node ) as $element ) {
			if( $attribute === null || $attribute === '*' ) {
				if( $element->attributes->length > 0 ) {
					foreach ( $element->attributes as $attr ) {
						if( ($attr != null || $attr != '') && $attr->nodeName === $type ) {
							$result[] = $attr->nodeValue;
						} 
					}
				} else if( $element->childNodes->length > 0 ) {
					foreach ( $element->childNodes as $child ) {
						if( $child->attributes && $child->attributes->length > 0 ) {
							foreach ( $child->attributes as $attr ) {
								if( ( $attr != null || $attr != '' ) && $attr->nodeName === $type ) {
									$result[] = $attr->nodeValue;
								}
							}
						}
					}
				}
			} else if( strpos( $element->getAttribute( $type ), $attribute ) !== false ) {
				$result[] = $element;
			}
		}
		return $result;
	}
	
	
	/**
	 * fill formData into formfields and return the result
	 * param $url				string	The form action url
	 * param $formData 	array		Array of Keys (input names) and Values to use
	 * return $result		string	Result from form request
	 */
	private static function request( $url = null, $formData = null, $method = 'POST' ) {
		try {
			
			// check incoming
			if( $url === null ) throw	new	Exception( 'No url given, abort.' );
			if( $formData === null || ! is_array( $formData ) ) throw	new	Exception( 'Cannot use form data, abort.' );
			
			// build options array
			$options = array(
				'http' => array( // use key 'http' even if you send the request to https://...
				    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				    'method'  => $method
				),
			);
			
			// append data if is post
			if( $method === 'POST' ) $options['http']['content'] = http_build_query($formData);
			
			$context  = stream_context_create( $options );
			$result = file_get_contents( $url, false, $context );
			
			if( $result === FALSE ) throw new Exception( 'No data received, abort.' );
			
			return $result;
			
		} catch( Exception $e ) {
			throw new Exception( __CLASS__ . '::' . __FUNCTION__ . ' throw ' . $e->getMessage() );
		}
	}
	
	
	/**
	 * Helper function to get the scheme from a url
	 */
	private function getScheme( $url = null ) {
		return parse_url( $url, PHP_URL_SCHEME );
	}
	
	
	/**
	 * Helper function to get the fqdn from a url
	 * like getScheme() a wrapper for php parse_url
	 * http://php.net/manual/de/function.parse-url.php
	 */
	private function getDomain( $url = null ) {
		return $this->getScheme( $url ) . '://' . parse_url( $url, PHP_URL_HOST );
	}
	
	
	/**
	 * Helper function to lbuild a dom object
	 */
	private function buildDom() {
		// create new dom object
		$dom = new \DOMDocument('1.0', 'UTF-8');
		// set error level
		$internalErrors = libxml_use_internal_errors(true);
		// load the html into the object
		$dom->loadHTML( $this->response );
		// discard white space
		$dom->preserveWhiteSpace = false;
		// Restore error level
		libxml_use_internal_errors($internalErrors);
		$this->dom = $dom;
	}
	
}

?>

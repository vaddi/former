# Former #

*Former the php html parser.*

Often we have websites which we fill in a few formfields and submitting a form to get desired Information (searchpages, traveling or shopping informations). Here it would be nice to have a Tool that can fill the form for you (you have manually look which formfields are neccessary) 

It can also automaticly try to find a form inside of a page by a given url and a piece of the targetting form action url.


## Description ##

A small PHP App to parse a html page by a given url. Most usefull feature is that they can also send form data (a simple assoc-array of input fields names and values) to a url and parse the response. Also featured a auto form action finder, that get all form action urls and try to find one by a given url segment.


## installation ##

Get last version from github.com by following command:

Git

		git clone git://github.com/vaddi/former.git

Http

		git clone https://github.com/vaddi/former.git


## Usage ##

### instance ###
Create a instance of the Parser class

		$parser = new Parser( $url );


### simple functions ###
Get the raw response

		$parser->rawResponse();

Get Elements by node name (table)

		$parser->getByNode( 'table' );

Get Element by css id (example)

		$parser->getById( 'example' );

Get Elements by node name and css class (node = tbody, class = example )

		$parser->getByNodeClass( 'tbody', 'example' );


### advanced functions ###
Get element by node name, node attribute value (null or '*' for all) and a node attribute name (class, href, rel, etc.)

		$parser->getByNodeAttribute( 'td', 'time', 'class' );

Get all href's inside of a td element

		$parser->getByNodeAttribute( 'td', '*', 'href' );

Get all image (img) sources (src)

		$parser->getByNodeAttribute( 'img', '*', 'src' );

Get action url from each form

		$parser->getByNodeAttribute( 'form', '*', 'action' )


See index.php and class/Parser.php for more examples and settings



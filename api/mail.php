<?php 
# Include the Autoloader (see "Libraries" for install instructions)
require '../vendor/autoload.php';
use Mailgun\Mailgun;
# Instantiate the client.
$mgClient = new Mailgun('8662aef18e1642a962e70481fed248e7-6b161b0a-3d3d0a2b');
$domain = "sandboxeae8ea8f4bc94617b604599a8920c079.mailgun.org";
# Make the call to the client.
$result = $mgClient->sendMessage($domain, array(
	'from'	=> 'Excited User <mailgun@YOUR_DOMAIN_NAME>',
	'to'	=> 'Baz <YOU@YOUR_DOMAIN_NAME>',
	'subject' => 'Hello',
	'text'	=> 'Testing some Mailgun awesomeness!'
));
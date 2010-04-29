2Performant PHP API
===================

The API allows you to integrate any 2Performant network in your application. It's goal is to make sure developers can implement anything that can be done via the web interface using API functions.

The API is RESTful XML over HTTP using all four verbs (GET/POST/PUT/DELETE). The PHP implementation is built as an PHP4 class.

API documentation can be found at:
http://help.2performant.com/API


Some Examples
=============

Interacting with 2Performant networks is very easy.

First you initialize an object

        $session = new TPerformant("simple", array("user => "user", "pass" => "pass"), 'http://api.yournetwork.com');
        // or via OAuth
        $consumer = new HTTP_OAuth_Consumer($APP_KEY, $APP_SECRET, $oauth_data['token'], $oauth_data['secret']);
        $obj = new Tperformant("oauth", $consumer, "http://api.yournetwork.com");

Afterwards you can call any function from the TPerformant class:

        // display the last 6 received messages
        print_r($session->received_messages_list());

For details about each API function the documentation can be found at:
http://help.2performant.com/API


Advanced Applications
=====================

You can build advanced applications using the 2Performant API and have them distributed over 2Performant App Store. 

Get Started at: http://apps.2performant.com and http://help.2performant.com/Developers-Area

Reporting Problems
==================

If you encounters any problems don't hesitate to contact us at:
support (at) 2performant.com

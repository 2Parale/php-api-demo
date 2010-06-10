<?php
/* ================================
   2Performant.com Network API 
   ver. 0.2.5
   http://help.2performant.com/API
   ================================ */

ini_set(
  'include_path',
  ini_get( 'include_path' ) . PATH_SEPARATOR . "2pphp/PEAR/" . PATH_SEPARATOR . "2pphp/" . PATH_SEPARATOR . "PEAR/"
);

require_once 'HTTP/Request2.php';
require_once 'XML/Serializer.php';
require_once 'XML/Unserializer.php';
require_once 'HTTP/OAuth.php';
require_once 'HTTP/OAuth/Consumer.php';

class TPerformant {
	
	var $user;
	var $pass;
        var $host;
        var $version = "v1.0";
        var $auth_type;
        var $oauth;
        var $oauthRequest;
	
	function TPerformant($auth_type, $auth_obj, $host) {
                if ($auth_type == 'simple') {
    		    $this->user = $auth_obj['user'];
		    $this->pass = $auth_obj['pass'];
                } elseif ($auth_type == 'oauth') {
                    $this->oauth = $auth_obj;

                    $this->oauthRequest = new HTTP_Request2;
                    $this->oauthRequest->setHeader('Content-type: text/xml; charset=utf-8');
                } else {
                    return false;
                }

                $this->auth_type = $auth_type;
                $this->host = $host;
	}

        /*=======*/
        /* Users */
        /*=======*/

        /* Display public information about a user */
        function user_show($user_id) {
                return $this->hook("/users/{$user_id}.xml", "user");
        }

        /* Display public information about the logged in user */
        function user_loggedin() {
                return $this->hook("/users/loggedin.xml", "user");
        }

        /*===========*/
        /* Campaigns */
        /*===========*/

        /* List campaigns. Displays the first 6 entries by default. */
        function campaigns_list($category_id=null, $page=1, $perpage=6) {
                $request['category_id'] = $category_id;
                $request['page']        = $page;
                $request['perpage']     = $perpage; 
         
                return $this->hook("/campaigns.xml", "campaign", $request, 'GET');
        }

        /* Search for campaigns */
        function campaigns_search($search, $page=1, $perpage=6) {
		$request['page']    = $page;
                $request['perpage'] = $perpage;
                $request['search']  = $search;
               
                return $this->hook("/campaigns/search.xml", "campaign", $request, 'GET');
        }

        /* Display public information about a campaign */
        function campaign_show($campaign_id) {
                return $this->hook("/campaigns/{$campaign_id}.xml", "campaign");
        }

        /* Affiliates: List campaigns which have the logged in user accepted */
        function campaigns_listforaffiliate() {
                return $this->hook("/campaigns/listforaffiliate.xml", "campaign");
        }

        /* Merchants: List all campaigns created by the logged in user */
        function campaigns_listforowner() {
                return $this->hook("/campaigns/listforowner.xml", "campaign");
        }

        /* Merchants: Display complete information about a campaign (only available to owner) */
        function campaign_showforowner($campaign_id) {
                return $this->hook("/campaigns/{$campaign_id}/showforowner.xml", "campaign");
        }
         
        /* Merchants: Update a campaign */
        function campaign_update($campaign_id, $campaign) {
                $request['campaign'] = $campaign;
                return $this->hook("/campaigns/{$campaign_id}.xml", "campaign", $request, 'PUT');
        }
      
        /* Create a Deep Link. This method was created so it wouldn't make a request for every Quick Link.
           You may need to get some data before using it. */
        function campaign_quicklink($campaign_id, $aff_code, $redirect) {
          $url = $this->host."/events/click?ad_type=quicklink&aff_code=".$aff_code."&unique=".$campaign_id."&redirect_to=".$redirect;
          if ($this->auth_type == 'oauth') {
            $url = $url."&app=".$this->oauth->getToken();
          }

          return $url;
        }

        /*=======*/
        /* Sales */
        /*=======*/

        function sale_create($campaign_id, $sale) {
                $request['sale'] = $sale;

                return $this->hook("/campaigns/{$campaign_id}/sales.xml", "sale", $request, 'POST');
        }

        /*=======*/
        /* Leads */
        /*=======*/

        function lead_create($campaign_id, $lead) {
                $request['lead'] = $lead;

                return $this->hook("/campaigns/{$campaign_id}/leads.xml", "lead", $request, 'POST');
        }

        /*============*/
        /* Affiliates */
        /*============*/

        /* Search for affiliates */
        function affiliates_search($search, $page=1, $perpage=6) {
                $request['page']    = $page;
                $request['perpage'] = $perpage;
                $request['search']  = $search;

                return $this->hook("/affiliates/search", "user", $request, 'GET');
        }

        /* Merchants: List affiliates approved in campaigns */
	function affiliates_listformerchant($campaign_id=null) {
		$request['campaign_id'] = $campaign_id;
                return $this->hook("/affiliates/listformerchant", "user", $request, 'GET');
        } 
       
        /*=============*/
        /* Commissions */
        /*=============*/
  
        /* Search for commissions.  Month: 01 to 12; Year: 20xx. Status: accepted, pending or rejected. null if empty search.*/
        function commissions_search($options, $campaign_id=null, $month=null, $year=null, $page=1, $perpage=6) {
                $request['campaign_id'] = $campaign_id;
                $request['month']       = $month;
                $request['year']        = $year;

                foreach($options as $key => $value)
                  $request[$key] = $value;

                $request['page']    = $page;
                $request['perpage'] = $perpage;

                return $this->hook("/commissions/search.xml", "commission", $request, 'GET');
        }

        /* Merchants: List commissions on campaigns. Month: 01 to 12; Year: 20xx. */
        function commissions_listformerchant($campaign_id, $month, $year) {
                $request['campaign_id'] = $campaign_id;
		$request['month']       = $month;
                $request['year']        = $year;

                return $this->hook("/commissions/listformerchant.xml", "campaign", $request, 'GET');
        }

        /* Affiliates: List commissions on campaigns. Month: 01 to 12; Year: 20xx. */
        function commissions_listforaffiliate($campaign_id, $month, $year) {
                $request['campaign_id'] = $campaign_id;
                $request['month']       = $month;
                $request['year']        = $year;

                return $this->hook("/commissions/listforaffiliate.xml", "commission", $request, 'GET');
        }

	/* Merchant Campaign Owner or Affiliate Commission Owner: Show information about a commission */
        function commission_show($commission_id) {
                return $this->hook("/commissions/{$commission_id}.xml", "commission");
        }

        /* Merchant: Update a commission */
        function commission_update($commission_id, $commission) {
                $request['commission'] = $commission;
                return $this->hook("/commissions/{$commission_id}.xml", "commission", $request, 'PUT');
        }

        /*=======*/
        /* Sites */
        /*=======*/

        /* List sites. Displays the first 6 entries by default. */
        function sites_list($category_id=null, $page=1, $perpage=6) {
                $request['category_id'] = $category_id;
                $request['page']        = $page;
                $request['perpage']     = $perpage;

                return $this->hook("/sites.xml", "site", $request);
        }

        /* Display information about a site */
        function site_show($site_id) {
                return $this->hook("/sites/{$site_id}.xml", "site");
        }

        /* Search for sites */
        function sites_search($search, $page=1, $perpage=6) {
                $request['page']    = $page;
                $request['perpage'] = $perpage;
                $request['search']  = $search;

                return $this->hook("/sites/search.xml", "site", $request, 'GET');
        }

        /* Affiliates: List all sites created by the logged in user */
        function sites_listforowner() {
                return $this->hook("/sites/listforowner.xml", "site");
        }

        /* Affiliates: Update a site */
        function site_update($site_id, $site) {
                $request['site'] = $site;
                return $this->hook("/sites/{$site_id}.xml", "site", $request, 'PUT');
        }


        /* Affiliates: Destroy a site */
        function site_destroy($site_id) {
                return $this->hook("/sites/{$site_id}.xml", "site", $request, 'DELETE');
        }

        /*============*/
        /* Text Links */
        /*============*/

        /* List text links from a campaign. Displays the first 6 entries by default. */
        function txtlinks_list($campaign_id, $page=1, $perpage=6) {
                $request['page']    = $page;
                $request['perpage'] = $perpage;

                return $this->hook("/campaigns/{$campaign_id}/txtlinks.xml", "txtlink", $request, 'GET');
        }

        /* Display information about a text link */
        function txtlink_show($campaign_id, $txtlink_id) {
                return $this->hook("/campaigns/{$campaign_id}/txtlinks/{$txtlink_id}.xml", "txtlink");
        }

        /* Search for text links in a campaign */
        function txtlinks_search($campaign_id, $search, $page=1, $perpage=6, $sort='date') {
                $request['page']    = $page;
                $request['perpage'] = $perpage;
                $request['search']  = $search;
                $request['sort']    = $sort;

                return $this->hook("/campaigns/{$campaign_id}/txtlinks/search.xml", "txtlink", $request, 'GET');
        }

        /* 
           Merchants: Create Text Link. 
           Txtlink must look like: array("title" => "title", "url" => "url", "help" => "help");  where "help" is optional
        */
        function txtlink_create($campaign_id, $txtlink) {
		$request['txtlink'] = $txtlink;

                return $this->hook("/campaigns/{$campaign_id}/txtlinks.xml", "txtlink", $request, 'POST');
        }

        /* Merchants: Update a text link */
        function txtlink_update($campaign_id, $txtlink_id, $txtlink) {
                $request['txtlink'] = $txtlink;
                return $this->hook("/campaigns/{$campaign_id}/txtlinks/{$txtlink_id}.xml", "txtlink", $request, 'PUT');
        }

        /* Merchants: Destroy a text link */
        function txtlink_destroy($campaign_id, $txtlink_id) {
                return $this->hook("/campaigns/{$campaign_id}/txtlinks/{$txtlink_id}.xml", "txtlink", null, 'DELETE');
        }

        /*============*/
        /* Text Ads */
        /*============*/

        /* List text ads from a campaign. Displays the first 6 entries by default. */
        function txtads_list($campaign_id, $page=1, $perpage=6) {
                $request['page']    = $page;
                $request['perpage'] = $perpage;

                return $this->hook("/campaigns/{$campaign_id}/txtads.xml", "txtad", $request, 'GET');
        }

        /* Display information about a text ad */
        function txtad_show($campaign_id, $txtad_id) {
                return $this->hook("/campaigns/{$campaign_id}/txtads/{$txtad_id}.xml", "txtad");
        }

        /* Search for text ads in a campaign */
        function txtads_search($campaign_id, $search, $page=1, $perpage=6, $sort='date') {
                $request['page']    = $page;
                $request['perpage'] = $perpage;
                $request['search']  = $search;
                $request['sort']    = $sort;

                return $this->hook("/campaigns/{$campaign_id}/txtads/search.xml", "txtad", $request, 'GET');
        }

        /* 
           Merchants: Create Text Ad. 
           Txtad must look like: array("title" => "title", "content" => "content", "url" => "url", "help" => "help");  where "help" is optional
        */
        function txtad_create($campaign_id, $txtad) {
                $request['txtad'] = $txtad;
        
                return $this->hook("/campaigns/{$campaign_id}/txtads.xml", "txtad", $request, 'POST');
        }


        /* Merchants: Update a text ad */
        function txtad_update($campaign_id, $txtad_id, $txtad) {
                $request['txtad'] = $txtad;
                return $this->hook("/campaigns/{$campaign_id}/txtads/{$txtad_id}.xml", "txtad", $request, 'PUT');
        }

        /* Merchants: Destroy a text ad */
        function txtad_destroy($campaign_id, $txtad_id) {
                return $this->hook("/campaigns/{$campaign_id}/txtads/{$txtad_id}.xml", "txtad", null, 'DELETE');
        }

        /*=========*/
        /* Banners */
        /*=========*/

        /* List banners from a campaign. Displays the first 6 entries by default. */
        function banners_list($campaign_id, $page=1, $perpage=6) {
                $request['page']    = $page;
                $request['perpage'] = $perpage;

                return $this->hook("/campaigns/{$campaign_id}/banners.xml", "banner", $request, 'GET');
        }

        /* Display information about a banner */
        function banner_show($campaign_id, $banner_id) {
                return $this->hook("/campaigns/{$campaign_id}/banners/{$banner_id}.xml", "banner");
        }

        /* Search for banners in a campaign */
        function banners_search($campaign_id, $search, $page=1, $perpage=6, $sort='date') {
                $request['page']    = $page;
                $request['perpage'] = $perpage;
                $request['search']  = $search;
                $request['sort']    = $sort;

                return $this->hook("/campaigns/{$campaign_id}/banners/search.xml", "banner", $request, 'GET');
        }

        /* Merchants: Create a banner */
        function banner_create($campaign_id,$banner, $banner_picture) {
                $request['banner'] = $banner;
                $request['banner_picture'] = $banner_picture;

                return $this->hook("/campaigns/{$campaign_id}/banners.xml", "banner", $request, 'POST');
        }

        /* Merchants: Update a banner */
        function banner_update($campaign_id, $banner_id, $banner) {
                $request['banner'] = $banner;
                return $this->hook("/campaigns/{$campaign_id}/banners/{$banner_id}.xml", "banner", $request, 'PUT');
        }

        /* Merchants: Destroy a banner */
        function banner_destroy($campaign_id, $banner_id) {
                return $this->hook("/campaigns/{$campaign_id}/banners/{$banner_id}.xml", "banner", null, 'DELETE');
        }

        /*===============*/
        /* Product Stores */
        /*===============*/

        /* List Product Stores from a Campaign */
        function product_stores_list($campaign_id) {
                $request['campaign_id'] = $campaign_id;

                return $this->hook("/product_stores.xml", "product-store", $request);
        }

        /* Show a Product Store */
        function product_store_show($product_store_id) {
                return $this->hook("/product_stores/{$product_store_id}.xml", "product-store");
        }

        /* Show Products from a Product Store */
        function product_store_showitems($product_store_id, $category=null, $page=1, $perpage=6, $uniq_products=null) {
                $request['category']      = $category;
                $request['page']          = $page;
                $request['perpage']       = $perpage;

                if ($uniq_products)
                  $request['uniq_products'] = $uniq_products;

                return $this->hook("/product_stores/{$product_store_id}/showitems.xml", "product-store-data", $request);
        }

        /* Show a Product from a Product Store */
        function product_store_showitem($product_store_id, $product_id) {
                $request['product_id'] = $product_id;

                return $this->hook("/product_stores/{$product_store_id}/showitem.xml", "product-store-data", $request);
        }


        /* Search for Products in a Product Store */
        function product_store_products_search($campaign_id, $search, $product_store_id='all', $category=null, $page=1, $perpage=6, $sort='date', $uniq_products=false) {
                $request['page']          = $page;
                $request['perpage']       = $perpage;
                $request['search']        = $search;
                $request['category']      = $category;
                $request['campaign_id']   = $campaign_id;
                $request['sort']          = $sort;

                if ($uniq_products)
                  $request['uniq_products'] = $uniq_products;

                if (!$product_store_id)
                  $product_store_id = 'all';

                return $this->hook("/product_stores/{$product_store_id}/searchpr.xml", "product-store-data", $request, 'GET');
        }

        /* Merchants: Update a Product Store */
        function product_store_update($product_store_id, $product_store) {
                $request['product_store'] = $product_store;
                return $this->hook("/product_stores/{$product_store_id}.xml", "product-store", $request, 'PUT');
        }

        /* Merchants: Destroy a Product Store */
        function product_store_destroy($product_store_id) {
                return $this->hook("/product_stores/{$product_store_id}.xml", "product-store", null, 'DELETE');
        }

        /* 
           Merchants: Create a Product Store Product. 
           WidgetStoreProduct must look like: 
              array("title" => "title", "description" => "desc", "caption" => "caption", "price" => "price(integer in RON)", 
                    "promoted" => "promoted (0 or 1)", "category" => "category", "subcategory" => "subcategory",  "url" => "url", 
                    "image_url" => "url to image location", "prid" => "product id");
        */
        function product_store_createitem($product_store_id, $product) {
                $request['product'] = $product;

                return $this->hook("/product_stores/{$product_store_id}/createitem.xml", "product-store-data", $request, 'POST');
        }

        /* Merchants: Update a product */
        function product_store_updateitem($product_store_id, $product_id, $product) {
                $request['product'] = $product;
                $request['product_id']   = $product_id;

                return $this->hook("/product_stores/{$product_store_id}/updateitem.xml", "product-store-data", $request, 'PUT');
        }

        /* Merchants: Destroy a product */
        function product_store_destroyitem($product_store_id, $product_id) {
        	$request['pr_id'] = $product_id;

                return $this->hook("/product_stores/{$product_store_id}/destroyitem.xml", "product-store-data", $request, 'DELETE');
        }

        /*=====================*/
        /* Affiliate Ad Groups */
        /*=====================*/
        
        /* Affiliates: List Ad Groups */
        function ad_groups_list() {
                return $this->hook("/ad_groups.xml", "ad_group", null, "GET");
        }

        /* Affiliates: Display information about an Ad Group */
        function ad_group_show($ad_group_id) {
                return $this->hook("/ad_groups/{$ad_group_id}.xml", "ad_group", null, "GET");
        }

        /* Affiliates: Add Item to Ad Group / Create new Ad Group */
        function ad_group_createitem($group_id, $tool_type, $tool_id, $new_group=null) {
                $request['group_id']  = $group_id;
                $request['new_group'] = $new_group;

                $request['tool_type'] = $tool_type;
                $request['tool_id']   = $tool_id;

                return $this->hook("/ad_groups/createitem.xml", "ad_group", $request, "POST");
        }

        /* Affiliates: Destroy an Ad Group */
        function ad_group_destroy($ad_group_id) {
                return $this->hook("/ad_groups/{$ad_group_id}.xml", "ad_group", null, "DELETE");
        }

	/* Affiliates: Delete an Tool from a Group. $tooltype is one of 'txtlink', 'txtad' or 'banner'. */
        function ad_group_destroyitem($ad_group_id, $tool_type, $tool_id) {
                $request['tool_type'] = $tool_type;
                $request['tool_id']   = $tool_id;

                return $this->hook("/ad_groups/{$ad_group_id}/destroyitem.xml", "ad_group", $request, "DELETE");
        }

        /*=================*/
        /* Affiliate Feeds */
        /*=================*/

        /* Affiliates: List Feeds */
        function feeds_list() {
                return $this->hook("/feeds.xml", "feed", null, "GET");
        }

        /* Affiliates: Create a Feed */
        function feed_create($feed) {
                $request['feed'] = $feed;

                return $this->hook("/feeds.xml", "feed", $request, 'POST');
        }

        /* Affiliates: Update a Feed */
        function feed_update($feed_id, $feed) {
                $request['feed'] = $feed;

                return $this->hook("/feeds/{$feed_id}.xml", "feed", $request, 'PUT');
        }


        /* Affiliates: Destroy a Feed */
        function feed_destroy($feed_id) {
                return $this->hook("/feeds/{$feed_id}.xml", "feed", null, "DELETE");
        }

        /*==========*/
        /* Messages */
        /*==========*/

        /* List received messages. Displays the first 6 entries by default. */
        function received_messages_list($page=1, $perpage=6) {
                $request['page']      = $page;
                $request['perpage']   = $perpage;

                return $this->hook("/messages.xml", "message", null, "GET");
        }

        /* List sent messages. Displays the first 6 entries by default. */
        function sent_messages_list($page=1, $perpage=6) {
                $request['page']      = $page;
                $request['perpage']   = $perpage;

                return $this->hook("/messages/sent.xml", "message", null, "GET");
        }

        /* Display information about a message */
        function message_show($message_id) {
                return $this->hook("/messages/{$message_id}.xml", "message");
        }

        /* Destroy a message */
        function message_destroy($message_id) {
                return $this->hook("/messages/{$message_id}.xml", "message", null, 'DELETE');
        }


        /*=======*/
        /* Hooks */
        /*=======*/
    
        /* List Hooks */
        function hooks_list($oauth_token_key='current') {
               return $this->hook("/oauth_clients/{$oauth_token_key}/hooks.xml", "hook", null, 'GET');
        }


        /* Create a Hook */
        function hook_create($hook, $oauth_token_key='current') {
               $request['hook'] = $hook;

               return $this->hook("/oauth_clients/{$oauth_token_key}/hooks.xml", "hook", $request, 'POST');
        }

        /* Destroy a Hook */
        function hook_destroy($hook_id, $oauth_token_key='current') {
                return $this->hook("/oauth_clients/{$oauth_token_key}/hooks/{$hook_id}.xml", "hook", null, 'DELETE');
        }


        /*===========================*/
        /* Actually process the data */
        /*===========================*/
	
	function hook($url,$expected, $send = null, $method = 'GET') {
		$returned = $this->unserialize($this->request($url, $send, $method));
		$placement = $expected;
		if (isset($returned->{$expected})) {
			$this->{$placement} = $returned->{$expected};	
			return $returned->{$expected};
		} else {
			$this->{$placement} = $returned;
			return $returned;
		}
	}
	
	function request($url, $params = null, $method) {
                $url = $this->host . "/" . $this->version . $url;

                if ($this->auth_type == 'simple') {
                        return $this->simpleHttpRequest($url, $params, $method);
                } else if ($this->auth_type == 'oauth') {
                        return $this->oauthHttpRequest($url, $params, $method);        
                }
	}

        function simpleHttpRequest($url, $params, $method) {
                $req = new HTTP_Request2($url, $method);

                //authorize
                $req->setAuth($this->user, $this->pass);

                if ($params) {
                        //serialize the data
                        $xml = $this->serialize($params);
                        ($xml)?$req->setBody($xml):false;
                }

                //set the headers
                $req->setHeader("Accept", "application/xml");
                $req->setHeader("Content-Type", "application/xml");

                $response = $req->send();

                if (PEAR::isError($response)) {
                        return $response->getMessage();
                } else {
                        return $response->getBody();
                }
        }

        function oauthHttpRequest($url, $params, $method) {
                $xml = null;

                //set the headers
                $this->oauthRequest->setHeader("Accept", "application/xml");
                $this->oauthRequest->setHeader("Content-Type", "application/xml");

                if ($params) {
                        //serialize the data
                        $xml = $this->serialize($params);

                        $this->oauthRequest->setBody($xml);
                        $this->oauth->accept($this->oauthRequest);
                }
                
                $response = $this->oauth->sendRequest($url, array(), $method);
                return $response->getBody();
        }

	function serialize($data) {
		$options = array(	XML_SERIALIZER_OPTION_MODE => XML_SERIALIZER_MODE_SIMPLEXML,
                                        XML_SERIALIZER_OPTION_ROOT_NAME   => 'request',
                                        XML_SERIALIZER_OPTION_CDATA_SECTIONS => true,
                                    	XML_SERIALIZER_OPTION_INDENT => '  ');
		$serializer = new XML_Serializer($options);
		$result = $serializer->serialize($data);
		return ($result)?$serializer->getSerializedData():false;
	}
	
	function unserialize($xml) {
		$options = array (XML_UNSERIALIZER_OPTION_COMPLEXTYPE => 'object');
		$unserializer = new XML_Unserializer($options);
		$status = $unserializer->unserialize($xml); 
	    $data = (PEAR::isError($status))?$status->getMessage():$unserializer->getUnserializedData();
		return $data;
	}
}


?>

<?php
/* ================================
   2Performant.com Network API 
   ver. 0.4.2
   http://help.2performant.com/API
   ================================ */

ini_set(
  'include_path',
  ini_get( 'include_path' ) . PATH_SEPARATOR . "2pphp/PEAR/" . PATH_SEPARATOR . "2pphp/" . PATH_SEPARATOR . "PEAR/"
);

require 'PEAR.php';
require_once 'HTTP/Request2.php';
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
                    $this->oauthRequest->setHeader('Content-type: text/json; charset=utf-8');
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
                return $this->hook("/users/{$user_id}.json", "user");
        }

        /* Display public information about the logged in user */
        function user_loggedin() {
                return $this->hook("/users/loggedin.json", "user");
        }

        /* Create a new User */
        function user_create($user, $user_info, $fast_activation = 0) {
                if (!$user)
                  $user = array();

                $user['fast_activation'] = $fast_activation;

                $request['user'] = $user;
		$request['user_info'] = $user_info;

                return $this->hook("/users.json", "user", $request, 'POST');
        }

        /*===========*/
        /* Campaigns */
        /*===========*/

        /* List campaigns. Displays the first 6 entries by default. */
        function campaigns_list($category_id=null, $page=1, $perpage=6) {
                $request['category_id'] = $category_id;
                $request['page']        = $page;
                $request['perpage']     = $perpage; 
         
                return $this->hook("/campaigns.json", "campaign", $request, 'GET');
        }

        /* Search for campaigns */
        function campaigns_search($search, $page=1, $perpage=6) {
		$request['page']    = $page;
                $request['perpage'] = $perpage;
                $request['search']  = $search;
               
                return $this->hook("/campaigns/search.json", "campaign", $request, 'GET');
        }

        /* Display public information about a campaign */
        function campaign_show($campaign_id) {
                return $this->hook("/campaigns/{$campaign_id}.json", "campaign");
        }

        /* Affiliates: List campaigns which have the logged in user accepted */
        function campaigns_listforaffiliate() {
                return $this->hook("/campaigns/listforaffiliate.json", "campaign");
        }

        /* Merchants: List all campaigns created by the logged in user */
        function campaigns_listforowner() {
                return $this->hook("/campaigns/listforowner.json", "campaign");
        }

        /* Merchants: Display complete information about a campaign (only available to owner) */
        function campaign_showforowner($campaign_id) {
                return $this->hook("/campaigns/{$campaign_id}/showforowner.json", "campaign");
        }
         
        /* Merchants: Update a campaign */
        function campaign_update($campaign_id, $campaign) {
                $request['campaign'] = $campaign;
                return $this->hook("/campaigns/{$campaign_id}.json", "campaign", $request, 'PUT');
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

                return $this->hook("/campaigns/{$campaign_id}/sales.json", "sale", $request, 'POST');
        }

        /*=======*/
        /* Leads */
        /*=======*/

        function lead_create($campaign_id, $lead) {
                $request['lead'] = $lead;

                return $this->hook("/campaigns/{$campaign_id}/leads.json", "lead", $request, 'POST');
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
	function affiliates_listforadvertiser($campaign_id=null) {
		$request['campaign_id'] = $campaign_id;
                return $this->hook("/affiliates/listforadvertiser", "user", $request, 'GET');
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

                return $this->hook("/commissions/search.json", "commission", $request, 'GET');
        }

        /* Merchants: List commissions on campaigns. Month: 01 to 12; Year: 20xx. */
        function commissions_listforadvertiser($campaign_id, $month, $year) {
                $request['campaign_id'] = $campaign_id;
		$request['month']       = $month;
                $request['year']        = $year;

                return $this->hook("/commissions/listforadvertiser.json", "campaign", $request, 'GET');
        }

        /* Affiliates: List commissions on campaigns. Month: 01 to 12; Year: 20xx. */
        function commissions_listforaffiliate($campaign_id, $month, $year) {
                $request['campaign_id'] = $campaign_id;
                $request['month']       = $month;
                $request['year']        = $year;

                return $this->hook("/commissions/listforaffiliate.json", "commission", $request, 'GET');
        }

	/* Merchant Campaign Owner or Affiliate Commission Owner: Show information about a commission */
        function commission_show($commission_id) {
                return $this->hook("/commissions/{$commission_id}.json", "commission");
        }

        /* Merchant: Update a commission */
        function commission_update($commission_id, $commission) {
                $request['commission'] = $commission;
                return $this->hook("/commissions/{$commission_id}.json", "commission", $request, 'PUT');
        }

        /*=======*/
        /* Sites */
        /*=======*/

        /* List sites. Displays the first 6 entries by default. */
        function sites_list($category_id=null, $page=1, $perpage=6) {
                $request['category_id'] = $category_id;
                $request['page']        = $page;
                $request['perpage']     = $perpage;

                return $this->hook("/sites.json", "site", $request);
        }

        /* Display information about a site */
        function site_show($site_id) {
                return $this->hook("/sites/{$site_id}.json", "site");
        }

        /* Search for sites */
        function sites_search($search, $page=1, $perpage=6) {
                $request['page']    = $page;
                $request['perpage'] = $perpage;
                $request['search']  = $search;

                return $this->hook("/sites/search.json", "site", $request, 'GET');
        }

        /* Affiliates: List all sites created by the logged in user */
        function sites_listforowner() {
                return $this->hook("/sites/listforowner.json", "site");
        }

        /* Affiliates: Update a site */
        function site_update($site_id, $site) {
                $request['site'] = $site;
                return $this->hook("/sites/{$site_id}.json", "site", $request, 'PUT');
        }


        /* Affiliates: Destroy a site */
        function site_destroy($site_id) {
                return $this->hook("/sites/{$site_id}.json", "site", $request, 'DELETE');
        }

        /*============*/
        /* Text Links */
        /*============*/

        /* List text links from a campaign. Displays the first 6 entries by default. */
        function txtlinks_list($campaign_id, $page=1, $perpage=6) {
                $request['page']    = $page;
                $request['perpage'] = $perpage;

                return $this->hook("/campaigns/{$campaign_id}/txtlinks.json", "txtlink", $request, 'GET');
        }

        /* Display information about a text link */
        function txtlink_show($campaign_id, $txtlink_id) {
                return $this->hook("/campaigns/{$campaign_id}/txtlinks/{$txtlink_id}.json", "txtlink");
        }

        /* Search for text links in a campaign */
        function txtlinks_search($campaign_id, $search, $page=1, $perpage=6, $sort='date') {
                $request['page']    = $page;
                $request['perpage'] = $perpage;
                $request['search']  = $search;
                $request['sort']    = $sort;

                return $this->hook("/campaigns/{$campaign_id}/txtlinks/search.json", "txtlink", $request, 'GET');
        }

        /* 
           Merchants: Create Text Link. 
           Txtlink must look like: array("title" => "title", "url" => "url", "help" => "help");  where "help" is optional
        */
        function txtlink_create($campaign_id, $txtlink) {
		$request['txtlink'] = $txtlink;

                return $this->hook("/campaigns/{$campaign_id}/txtlinks.json", "txtlink", $request, 'POST');
        }

        /* Merchants: Update a text link */
        function txtlink_update($campaign_id, $txtlink_id, $txtlink) {
                $request['txtlink'] = $txtlink;
                return $this->hook("/campaigns/{$campaign_id}/txtlinks/{$txtlink_id}.json", "txtlink", $request, 'PUT');
        }

        /* Merchants: Destroy a text link */
        function txtlink_destroy($campaign_id, $txtlink_id) {
                return $this->hook("/campaigns/{$campaign_id}/txtlinks/{$txtlink_id}.json", "txtlink", null, 'DELETE');
        }

        /*============*/
        /* Text Ads */
        /*============*/

        /* List text ads from a campaign. Displays the first 6 entries by default. */
        function txtads_list($campaign_id, $page=1, $perpage=6) {
                $request['page']    = $page;
                $request['perpage'] = $perpage;

                return $this->hook("/campaigns/{$campaign_id}/txtads.json", "txtad", $request, 'GET');
        }

        /* Display information about a text ad */
        function txtad_show($campaign_id, $txtad_id) {
                return $this->hook("/campaigns/{$campaign_id}/txtads/{$txtad_id}.json", "txtad");
        }

        /* Search for text ads in a campaign */
        function txtads_search($campaign_id, $search, $page=1, $perpage=6, $sort='date') {
                $request['page']    = $page;
                $request['perpage'] = $perpage;
                $request['search']  = $search;
                $request['sort']    = $sort;

                return $this->hook("/campaigns/{$campaign_id}/txtads/search.json", "txtad", $request, 'GET');
        }

        /* 
           Merchants: Create Text Ad. 
           Txtad must look like: array("title" => "title", "content" => "content", "url" => "url", "help" => "help");  where "help" is optional
        */
        function txtad_create($campaign_id, $txtad) {
                $request['txtad'] = $txtad;
                return $this->hook("/campaigns/{$campaign_id}/txtads.json", "txtad", $request, 'POST');
        }


        /* Merchants: Update a text ad */
        function txtad_update($campaign_id, $txtad_id, $txtad) {
                $request['txtad'] = $txtad;
                return $this->hook("/campaigns/{$campaign_id}/txtads/{$txtad_id}.json", "txtad", $request, 'PUT');
        }

        /* Merchants: Destroy a text ad */
        function txtad_destroy($campaign_id, $txtad_id) {
                return $this->hook("/campaigns/{$campaign_id}/txtads/{$txtad_id}.json", "txtad", null, 'DELETE');
        }

        /*=========*/
        /* Banners */
        /*=========*/

        /* List banners from a campaign. Displays the first 6 entries by default. */
        function banners_list($campaign_id, $page=1, $perpage=6) {
                $request['page']    = $page;
                $request['perpage'] = $perpage;

                return $this->hook("/campaigns/{$campaign_id}/banners.json", "banner", $request, 'GET');
        }

        /* Display information about a banner */
        function banner_show($campaign_id, $banner_id) {
                return $this->hook("/campaigns/{$campaign_id}/banners/{$banner_id}.json", "banner");
        }

        /* Search for banners in a campaign */
        function banners_search($campaign_id, $search, $page=1, $perpage=6, $sort='date') {
                $request['page']    = $page;
                $request['perpage'] = $perpage;
                $request['search']  = $search;
                $request['sort']    = $sort;

                return $this->hook("/campaigns/{$campaign_id}/banners/search.json", "banner", $request, 'GET');
        }

        /* Merchants: Create a banner */
        function banner_create($campaign_id,$banner, $banner_picture) {
                $request['banner'] = $banner;
                $request['banner_picture'] = $banner_picture;

                return $this->hook("/campaigns/{$campaign_id}/banners.json", "banner", $request, 'POST');
        }

        /* Merchants: Update a banner */
        function banner_update($campaign_id, $banner_id, $banner) {
                $request['banner'] = $banner;
                return $this->hook("/campaigns/{$campaign_id}/banners/{$banner_id}.json", "banner", $request, 'PUT');
        }

        /* Merchants: Destroy a banner */
        function banner_destroy($campaign_id, $banner_id) {
                return $this->hook("/campaigns/{$campaign_id}/banners/{$banner_id}.json", "banner", null, 'DELETE');
        }

        /*===============*/
        /* Product Stores */
        /*===============*/

        /* List Product Stores from a Campaign */
        function product_stores_list($campaign_id) {
                $request['campaign_id'] = $campaign_id;

                return $this->hook("/product_stores.json", "product_store", $request);
        }

        /* Show a Product Store */
        function product_store_show($product_store_id) {
                return $this->hook("/product_stores/{$product_store_id}.json", "product_store");
        }

        /* Show Products from a Product Store */
        function product_store_showitems($product_store_id, $category=null, $page=1, $perpage=6, $uniq_products=null) {
                $request['category']      = $category;
                $request['page']          = $page;
                $request['perpage']       = $perpage;

                if ($uniq_products)
                  $request['uniq_products'] = $uniq_products;

                return $this->hook("/product_stores/{$product_store_id}/showitems.json", "product_store_data", $request);
        }

        /* Show a Product from a Product Store */
        function product_store_showitem($product_store_id, $product_id) {
                $request['product_id'] = $product_id;

                return $this->hook("/product_stores/{$product_store_id}/showitem.json", "product_store_data", $request);
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

                return $this->hook("/product_stores/{$product_store_id}/searchpr.json", "product_store_data", $request, 'GET');
        }

        /* Merchants: Update a Product Store */
        function product_store_update($product_store_id, $product_store) {
                $request['product_store'] = $product_store;
                return $this->hook("/product_stores/{$product_store_id}.json", "product_store", $request, 'PUT');
        }

        /* Merchants: Destroy a Product Store */
        function product_store_destroy($product_store_id) {
                return $this->hook("/product_stores/{$product_store_id}.json", "product_store", null, 'DELETE');
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

                return $this->hook("/product_stores/{$product_store_id}/createitem.json", "product_store_data", $request, 'POST');
        }

        /* Merchants: Update a product */
        function product_store_updateitem($product_store_id, $product_id, $product) {
                $request['product'] = $product;
                $request['product_id']   = $product_id;

                return $this->hook("/product_stores/{$product_store_id}/updateitem.json", "product_store_data", $request, 'PUT');
        }

        /* Merchants: Destroy a product */
        function product_store_destroyitem($product_store_id, $product_id) {
        	$request['pr_id'] = $product_id;

                return $this->hook("/product_stores/{$product_store_id}/destroyitem.json", "product_store_data", $request, 'DELETE');
        }

        /*=====================*/
        /* Affiliate Ad Groups */
        /*=====================*/
        
        /* Affiliates: List Ad Groups */
        function ad_groups_list() {
                return $this->hook("/ad_groups.json", "ad_group", null, "GET");
        }

        /* Affiliates: Display information about an Ad Group */
        function ad_group_show($ad_group_id) {
                return $this->hook("/ad_groups/{$ad_group_id}.json", "ad_group", null, "GET");
        }

        /* Affiliates: Add Item to Ad Group / Create new Ad Group */
        function ad_group_createitem($group_id, $tool_type, $tool_id, $new_group=null) {
                $request['group_id']  = $group_id;
                $request['new_group'] = $new_group;

                $request['tool_type'] = $tool_type;
                $request['tool_id']   = $tool_id;

                return $this->hook("/ad_groups/createitem.json", "ad_group", $request, "POST");
        }

        /* Affiliates: Destroy an Ad Group */
        function ad_group_destroy($ad_group_id) {
                return $this->hook("/ad_groups/{$ad_group_id}.json", "ad_group", null, "DELETE");
        }

	/* Affiliates: Delete an Tool from a Group. $tooltype is one of 'txtlink', 'txtad' or 'banner'. */
        function ad_group_destroyitem($ad_group_id, $tool_type, $tool_id) {
                $request['tool_type'] = $tool_type;
                $request['tool_id']   = $tool_id;

                return $this->hook("/ad_groups/{$ad_group_id}/destroyitem.json", "ad_group", $request, "DELETE");
        }

        /*=================*/
        /* Affiliate Feeds */
        /*=================*/

        /* Affiliates: List Feeds */
        function feeds_list() {
                return $this->hook("/feeds.json", "feed", null, "GET");
        }

        /* Affiliates: Create a Feed */
        function feed_create($feed) {
                $request['feed'] = $feed;

                return $this->hook("/feeds.json", "feed", $request, 'POST');
        }

        /* Affiliates: Update a Feed */
        function feed_update($feed_id, $feed) {
                $request['feed'] = $feed;

                return $this->hook("/feeds/{$feed_id}.json", "feed", $request, 'PUT');
        }


        /* Affiliates: Destroy a Feed */
        function feed_destroy($feed_id) {
                return $this->hook("/feeds/{$feed_id}.json", "feed", null, "DELETE");
        }

        /*==========*/
        /* Messages */
        /*==========*/

        /* List received messages. Displays the first 6 entries by default. */
        function received_messages_list($page=1, $perpage=6) {
                $request['page']      = $page;
                $request['perpage']   = $perpage;

                return $this->hook("/messages.json", "message", null, "GET");
        }

        /* List sent messages. Displays the first 6 entries by default. */
        function sent_messages_list($page=1, $perpage=6) {
                $request['page']      = $page;
                $request['perpage']   = $perpage;

                return $this->hook("/messages/sent.json", "message", null, "GET");
        }

        /* Display information about a message */
        function message_show($message_id) {
                return $this->hook("/messages/{$message_id}.json", "message");
        }

        /* Destroy a message */
        function message_destroy($message_id) {
                return $this->hook("/messages/{$message_id}.json", "message", null, 'DELETE');
        }

        /*=================================*/
        /*        ADMIN FUNCTIONS          */
        /*=================================*/


        /*====================*/
        /* Affiliate Invoices */
        /*====================*/

        /* List Affiliate Invoices. Displays the first 15 entries by default. */
        function admin_affiliate_invoices_list($page=1, $perpage=15) {
                $request['page']        = $page;
                $request['perpage']     = $perpage;

                return $this->hook("/users/all/affiliate_invoices.json", "affiliate_invoice", $request, 'GET', 'admin');
        }

        /* Search for Affiliate Invoices */
        function admin_affiliate_invoices_search($search, $page=1, $perpage=15) {
                $request['page']    = $page;
                $request['perpage'] = $perpage;
                $request['search']  = $search;

                return $this->hook("/affiliate_invoices/search.json", "affiliate_invoice", $request, 'POST', 'admin');
        }

        /* Create an Affiliate Invoice */
        function admin_affiliate_invoice_create($user_id, $affiliate_invoice, $commissions, $taxes) {
                $request['affiliate_invoice'] = $affiliate_invoice;
                $request['commissions'] = $commissions;
                $request['taxes'] = $taxes;

                return $this->hook("/users/$user_id/affiliate_invoices.json", "affiliate_invoice", $request, 'POST', 'admin');
        }      
 
        /* Update an Affiliate Invoice */      
        function admin_affiliate_invoice_update($user_id, $affiliate_invoice_id, $affiliate_invoice, $taxes=null) {
                $request['affiliate_invoice'] = $affiliate_invoice;
                $request['taxes'] = $taxes;

                return $this->hook("/users/$user_id/affiliate_invoices/$affiliate_invoice_id.json", "affiliate_invoice", $request, 'PUT', 'admin');
        }

        /* Destroy an Affiliate Invoice */
        function admin_affiliate_invoice_destroy($user_id, $affiliate_invoice_id) {
                return $this->hook("/users/$user_id/affiliate_invoices/$affiliate_invoice_id.json", "affiliate_invoice", null, 'DELETE', 'admin');
        }

        /*=====================*/
        /* Advertiser Invoices */
        /*=====================*/

        /* List Advertiser Invoices. Displays the first 15 entries by default. */
        function admin_advertiser_invoices_list($page=1, $perpage=15) {
                $request['page']        = $page;
                $request['perpage']     = $perpage;

                return $this->hook("/users/all/advertiser_invoices.json", "advertiser_invoice", $request, 'GET', 'admin');
        }

        /* Search for Advertiser Invoices */
        function admin_advertiser_invoices_search($search, $page=1, $perpage=15) {
                $request['page']    = $page;
                $request['perpage'] = $perpage;
                $request['search']  = $search;

                return $this->hook("/advertiser_invoices/search.json", "advertiser_invoice", $request, 'POST', 'admin');
        }

        /* Create an Advertiser Invoice */
        function admin_advertiser_invoice_create($user_id, $advertiser_invoice, $commissions, $fees) {
                $request['advertiser_invoice'] = $advertiser_invoice;
                $request['commissions'] = $commissions;
                $request['fees'] = $fees;

                return $this->hook("/users/$user_id/advertiser_invoices.json", "advertiser_invoice", $request, 'POST', 'admin');
        }

        /* Update an Advertiser Invoice */
        function admin_advertiser_invoice_update($user_id, $advertiser_invoice_id, $advertiser_invoice, $fees=null) {
                $request['advertiser_invoice'] = $advertiser_invoice;
                $request['fees'] = $fees;

                return $this->hook("/users/$user_id/advertiser_invoices/$advertiser_invoice_id.json", "advertiser_invoice", $request, 'PUT', 'admin');
        }

        /* Destroy an Advertiser Invoice */
        function admin_advertiser_invoice_destroy($user_id, $advertiser_invoice_id) {
                return $this->hook("/users/$user_id/advertiser_invoices/$advertiser_invoice_id.json", "advertiser_invoice", null, 'DELETE', 'admin');
        }


        /*=================*/
        /* Admin Campaigns */
        /*=================*/

        /* List Campaigns. Displays the first 15 entries by default. */
        function admin_campaigns_list($page=1, $perpage=15) {
                $request['page']        = $page;
                $request['perpage']     = $perpage;

                return $this->hook("/campaigns.json", "campaign", $request, 'GET', 'admin');
        }

        /* Search for Advertiser Invoices */
        function admin_campaigns_search($search, $page=1, $perpage=15) {
                $request['page']    = $page;
                $request['perpage'] = $perpage;
                $request['search']  = $search;

                return $this->hook("/campaigns/search.json", "campaign", $request, 'POST', 'admin');
        }


        /* Update a Campaign */
        function admin_campaign_update($campaign_id, $suspend=null, $reset=null) {
                $request['suspend'] = $suspend;
                $request['reset']   = $reset;

                return $this->hook("/campaigns/$campaign_id.json", "campaign", $request, 'PUT', 'admin');
        }

        /* Destroy a Campaign */
        function admin_campaign_destroy($campaign_id) {
                return $this->hook("/campaigns/$campaign_id.json", "campaign", null, 'DELETE', 'admin');
        }

        /*===================*/
        /* Admin Commissions */
        /*===================*/

        /* List Affiliates Commissions  */
        function admin_affiliates_commissions_list($search=null, $page=1, $perpage=15) {
                $request['page']    = $page;
                $request['perpage'] = $perpage;
                $request['search']  = $search;

                return $this->hook("/commissions/affiliates", "commission", $request, 'GET', 'admin');
        }

        /* List Advertiser Commissions  */
        function admin_advertisers_commissions_list($search=null, $page=1, $perpage=15) {
                $request['page']    = $page;
                $request['perpage'] = $perpage;
                $request['search']  = $search;

                return $this->hook("/commissions/advertisers", "commission", $request, 'GET', 'admin');
        }

        /*==========*/
        /* Deposits */
        /*==========*/

        /* List Deposits */
        function admin_deposits_list($page=1, $perpage=15) {
                $request['page']    = $page;
                $request['perpage'] = $perpage;

                return $this->hook("/deposits.json", "deposit", $request, 'GET', 'admin');
        }

        /* Create a Deposit */
        function admin_deposit_create($deposit) {
                $request['deposit'] = $deposit;

                return $this->hook("/users/all/deposits.json", "deposit", $request, 'POST', 'admin');
        }

        /* Destroy a Deposit */
        function admin_deposit_destroy($user_id, $deposit_id) {
                return $this->hook("/users/$user_id/deposits/$deposit_id.json", "deposit", null, 'DELETE', 'admin');
        }


        /*=============*/
        /* Admin Users */
        /*=============*/

        /* List Users */
        function admin_users_list($page=1, $perpage=15) {
                $request['page']    = $page;
                $request['perpage'] = $perpage;

                return $this->hook("/users.json", "user", $request, 'GET', 'admin');
        }

        /* Search for Users */
        function admin_users_search($search=null, $page=1, $perpage=15) {
                $request['page']    = $page;
                $request['perpage'] = $perpage;
                $request['search']  = $search;

                return $this->hook("/users/search.json", "user", $request, 'POST', 'admin');
        }

        /* List Pending Users */
        function admin_users_pending_list() {
                return $this->hook("/users/pending.json", "user", null, 'GET', 'admin');
        }

        /* Process (Accept/Reject) a Pending User */
        function admin_users_pending_process($user_id, $status, $message=null) {
                $request['status'] = $status;
                $request['message'] = $message;

                return $this->hook("/users/$user_id/pending_process.json", "user", $request, 'POST', 'admin');
        }

        /* Destroy a User */
        function admin_user_destroy($user_id) {
                return $this->hook("/users/$user_id.json", "user", null, 'DELETE', 'admin');
	}

        /*=======*/
        /* Hooks */
        /*=======*/
    
        /* List Hooks */
        function hooks_list($oauth_token_key='current') {
               return $this->hook("/oauth_clients/{$oauth_token_key}/hooks.json", "hook", null, 'GET');
        }


        /* Create a Hook */
        function hook_create($hook, $oauth_token_key='current') {
               $request['hook'] = $hook;

               return $this->hook("/oauth_clients/{$oauth_token_key}/hooks.json", "hook", $request, 'POST');
        }

        /* Destroy a Hook */
        function hook_destroy($hook_id, $oauth_token_key='current') {
                return $this->hook("/oauth_clients/{$oauth_token_key}/hooks/{$hook_id}.json", "hook", null, 'DELETE');
        }


        /*===========================*/
        /* Actually process the data */
        /*===========================*/
	
	function hook($url,$expected, $send = null, $method = 'GET', $where = 'main') {
		$returned = json_decode($this->request($url, $send, $method, $where));
		$result = null;

		if (is_array($returned)) {
			$result = array();
			foreach($returned as $item) {
				if ($item->{$expected})
					array_push($result, $item->{$expected});
			}
		} else {
			if ($returned->{$expected})
				$result = $returned->{$expected};
		}
		return $result;
	}
	
	function request($url, $params = null, $method, $where) {
                if ($where == 'admin') {
			$admin_host = str_replace("api.", "admin.", $this->host);
			$url = $admin_host . $url;
                } else {
			$url = $this->host . '/' . $this->version . $url;
                }

                if ($this->auth_type == 'simple') {
                        return $this->simpleHttpRequest($url, $params, $method);
                } else if ($this->auth_type == 'oauth') {
                        return $this->oauthHttpRequest($url, $params, $method);        
                }
	}

        function simpleHttpRequest($url, $params, $method) {
                $req = new HTTP_Request2($url, $method, array ('ssl_verify_peer' => false, 'ssl_verify_host' => false));

                //authorize
                $req->setAuth($this->user, $this->pass);

                if ($params) {
                        //serialize the data
                        $json = json_encode($params);
                        ($json)?$req->setBody($json):false;
                }

                //set the headers
                $req->setHeader("Accept", "application/json");
                $req->setHeader("Content-Type", "application/json");

                $response = $req->send();

                if (PEAR::isError($response)) {
                        return $response->getMessage();
                } else {
                        return $response->getBody();
                }
        }

        function oauthHttpRequest($url, $params, $method) {
                $json = null;

                //set the headers
                $this->oauthRequest->setHeader("Accept", "application/json");
                $this->oauthRequest->setHeader("Content-Type", "application/json");

                if ($params) {
                        //serialize the data
                        $json = json_encode($params);

                        $this->oauthRequest->setBody($json);
                        $this->oauth->accept($this->oauthRequest);
                }
                
                $response = $this->oauth->sendRequest($url, array(), $method);
                return $response->getBody();
        }

}


?>

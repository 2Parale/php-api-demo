<?php 
  /* Let's get a 2Performant object using the stored data */
  require 'config.php';
  
  if ($_GET['token']) {
    $oauth_data = find_token($_GET['token']);

    $consumer = new HTTP_OAuth_Consumer($KEY, $SECRET, $oauth_data['token'], $oauth_data['secret']);
    $obj = new Tperformant("oauth", $consumer, "http://".$oauth_data['network']);
     
    $user = $obj->user_loggedin();
  }
?>

<h3>Hello There<?php if ($user) echo ", ".$user->name ?> </h3>

I am a demo application. File <i>support.php</i>. This is where I can show information, news, allow configuration options. Anything that helps the affiliate...<br/><br/>

<b>There are a couple other files:</b>

<ul>
  <li><i>init.php</i> - this file initializes the OAuth connection. It gets called by 2Performant when an affiliate wants to add the application.</li>
  <li><i>callback.php</i> - once the affiliate approves the application we redirect to this file with the OAuth access data.</li>
  <li><i>settings.php</i> - this is a dedicated file for configuration. When an affiliates clicks on Settings(in the above header) this file will be opened.</li>
  <li><i>embed.php</i> - when an affiliate adds the 'Embed this Code to use App' to his website an iframe is created that points to this file.</li>
</ul>

If you want to lean more about the 2Performant Application Documentation <a href='http://help.2performant.com'>click here</a>.

<?php
  /* Let's get a 2Performant object using the stored data */
  require 'config.php';

  if ($_GET['public_token']) {
    $oauth_data = find_public_token($_GET['public_token']);
    $width      = $_GET['width'];
    $height     = $_GET['height'];
    
    $consumer = new HTTP_OAuth_Consumer($KEY, $SECRET, $oauth_data['token'], $oauth_data['secret']);
    $obj = new Tperformant("oauth", $consumer, "http://".$oauth_data['network']);

    $products = $obj->product_store_products_search('approved', '', null, null, 1, 1);
    if ($products)
	$product = $products[0];

    // We want to make sure the image is not bigger than the box
    $img_size = ($width < $height) ? "width='".$width."px'" : "height='".($height - 50)."px'";
?>

<html>
<body>
<?php if ($product) : ?>
<h5><?php echo $product->title; ?></h5>
    <div style='float: left;'><img <?php echo $img_size; ?> src='<?php echo $product->image_url; ?>'></div>
    <div style='float: left; margin-left: 10px; font-size: 12px;'><a href='<?php echo $product->aff_link; ?>' target='_blank'>Buy Now!</a><br/><br/><b>Price:</b> <?php echo $product->price; ?></div>
<?php else : ?>
    You have to be a member in a campaign with at least one Widget Store..
<?php endif; ?>
</body>
</html>
<?php } ?>

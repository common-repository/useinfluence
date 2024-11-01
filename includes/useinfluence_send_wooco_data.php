<?php

function useinfluence_getApiKey()
{
  	 global $wpdb;
	 $query = $wpdb->get_results("SELECT * FROM tracking_id ORDER BY ID DESC LIMIT 1", OBJECT);
	 if(isset($query) && !empty($query))
	 {
		return $query[0]->app_key;
	 }
	 return false;
}

function useinfluence_order_processed($id)
{
    $order = wc_get_order($id);
    $items = $order->get_items();
    $products = array();
    foreach ($items as $item) {
        $quantity = $item->get_quantity();
        $product = $item->get_product();
        $images_arr = wp_get_attachment_image_src($product->get_image_id(), array('72', '72'), false);
        $image = null;
        if ($images_arr !== null && $images_arr[0] !== null) {
            $image = $images_arr[0];
          /*  if (is_ssl() && strpos($image, "https") == false) {
				$image = str_replace('http', 'https', $image);
            }*/
        }
        $p = array(
            'id' => $product->get_id(),
            'quantity' => (int) $quantity,
            'price' => (int) $product->get_price(),
            'name' => $product->get_name(),
            'link' => get_permalink($product->get_id()),
            'image' => $image,
        );
        array_push($products, $p);
    }
    useinfluence_send_webhook($order, $products);
}

add_action('woocommerce_checkout_order_processed', 'useinfluence_order_processed');
//add_action( 'woocommerce_payment_complete', 'code_for_payment' );

function useinfluence_send_webhook($order, $products)
{
    $apiKey = useinfluence_getApiKey();
	$campaign_id = get_option('useinflu_campaign_id');

    if (!isset($apiKey)) {
        return;
    }
	
	 $headers = array(
        'Content-Type' => 'application/json',
        'apikey' => $apiKey
    );
	
	foreach($products as $pval)
    {
		$data = array("email"=>$order->get_billing_email(),
					  "name"=>$order->get_billing_first_name()." ".$order->get_billing_last_name(),
					  "websiteUrl"=> get_site_url(),
					  "date_created"=>$order->get_date_created()->format('d-m-Y'),
					  "city"=>$order->get_billing_city(),
					  "country"=>$order->get_billing_country(),
					  "product_name"=>$pval['name'],
					  "product_url"=>$pval['link'],
					  "product_id"=>$pval['id'],
					  "product_img"=>$pval['image'],
							 );
        if(isset($campaign_id) && !empty($campaign_id))
        {
			$data['campaignId'] = $campaign_id;
		}			
		$url = "https://api.useinfluence.co/webhookCallback";
		$res = wp_remote_post($url, array(
			'headers' => $headers,
			'body' => json_encode($data),
		));
	}
	
	
}
?>
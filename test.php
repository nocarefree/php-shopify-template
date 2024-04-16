<?php

include(__DIR__ . "/vendor/autoload.php");

use Liquid\Liquid;
use Liquid\Context;
use Liquid\Models\Drop;
use Liquid\Filters\Standard;


$server = (new ShopifyTemplate\ThemeArchitecture());
$server->install(__DIR__ . '/test/templates/spotlight');


$shop = new Drop([
    "accepts_gift_cards" => true,
    "address" => [],
    "brand" => [],
    "collections_count" => 7,
    "currency" => "CAD",
    "customer_accounts_enabled" => true,
    "customer_accounts_optional" => true,
    "description" => "Canada's foremost retailer for potions and potion accessories. Try one of our award-winning artisanal potions, or find the supplies to make your own!",
    "domain" => "test.com",
    "email" => "polinas@test.com",
    "enabled_currencies" => [],
    "enabled_locales" => [],
    "enabled_payment_types" => [
        "american_express",
        "apple_pay",
        "diners_club",
        "discover",
        "google_pay",
        "master",
        "paypal",
        "shopify_pay",
        "visa"
    ],
    "id" => 56174706753,
    "locale" => "en",
    "metafields" => [],
    "metaobjects" => [],
    "money_format" => "\${{amount}}",
    "money_with_currency_format" => "\${{amount}} CAD",
    "name" => "Polina's Potent Potions",
    "password_message" => "Our store will be opening when the moon is in the seventh house!!",
    "permanent_domain" => "test.com",
    "phone" => "416-123-1234",
    "policies" => [],
    "privacy_policy" => [],
    "products_count" => 19,
    "published_locales" => [],
    "refund_policy" => [],
    "secure_url" => "https://test.com",
    "shipping_policy" => [],
    "subscription_policy" => null,
    "taxes_included" => false,
    "terms_of_service" => [],
    "types" => [
        "",
        "Animals & Pet Supplies",
        "Baking Flavors & Extracts",
        "Container",
        "Cooking & Baking Ingredients",
        "Dried Flowers",
        "Fruits & Vegetables",
        "Gift Cards",
        "Health",
        "Health & Beauty",
        "Invisibility",
        "Love",
        "Music & Sound Recordings",
        "Seasonings & Spices",
        "Water"
    ],
    "url" => "https://polinas-potent-potions.myshopify.com",
    "vendors" => [
        "Clover's Apothecary",
        "Polina's Potent Potions",
        "Ted's Apothecary Supply"
    ]
]);


$shop_locale = new Drop([
    "endonym_name" => "English",
    "iso_code" => "en",
    "name" => "English",
    "primary" => true,
    "root_url" => "/"
]);

$request = new Drop([
    "design_mode" => false,
    "host" => "test.com",
    "locale" => $shop_locale,
    "origin" => "https://test.com",
    "page_type" => "index",
    "path" => "/",
    "visual_preview_mode" => false
]);

$localization = new Drop([
    "available_countries" => [],
    "available_languages" => [],
    "country" => [],
    "language" => [],
    "market" => [],
]);


echo $server->renderTemplate('index', [
    'request' => $request,
    'canonical_url' => $request->origin . $request->path,
    'page_title' => 'index page',
    'page_description' => null,
    'shop' => $shop,
    'localization' => $localization,
]);

exit;

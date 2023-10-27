<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
function getM2Products() {
  $URLWebshop = "https://partycorner.store/rest/V1/products/";
  $yourApiKey = "nt2ud9hjj0up366xtbt7o5g4yt9ertbi";
  $method = "GET";

  $searchCriteria = [
    "searchCriteria" => [
      "filterGroups" => [
        [
          "filters" => [
            [
              "field" => "status",
              "value" => "1", // Actieve producten (aanpassen indien nodig)
              "condition_type" => "eq"
            ]
          ]
        ]
      ]
    ]
  ];

  $response = executeRest($URLWebshop, $searchCriteria, $yourApiKey, $method);
  $responseData = json_decode($response, true);

  if (isset($responseData['message'])) {
    echo "Fout bij het ophalen van producten: " . $responseData['message'];
    return array();
  }
  return $responseData;
}

function executeRest($url, $data, $accessToken1, $method) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Authorization: Bearer ' . $accessToken1
  ));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $response = curl_exec($ch);
  curl_close($ch);

  return $response;
}

$products = getM2Products();

if (!empty($products)) {
  echo "<h1>Magento Producten</h1>";
  echo "<ul>";
  var_dump($products);
  foreach ($products as $product) {
      echo "<li><strong>" . $product['name'] . "</strong><br>";
      echo "Prijs: " . $product['price'] . "<br>";
      echo "Beschrijving: " . $product['description'] . "</li>";
  }
  echo "</ul>";
} else {
  echo "Geen producten gevonden.";
}

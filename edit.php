<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$yourApiKey = $_ENV['API_KEY'];

// Verbindingsgegevens voor de database (vervang 'jouw_gegevens' door de juiste databasegegevens)
$host = "localhost";
$username = "bit_academy";
$password = "bit_academy";
$database = 'partycorner'; // De naam van de database

$productId = null; // Standaard is het product-ID null

// Controleer of er een product-ID aanwezig is in de URL
if (isset($_GET['id'])) {
    // Verkrijg het product-ID uit de URL
    $productId = $_GET['id'];
}

$productData = null; // Standaard zijn de productgegevens null

if ($productId !== null) {
    try {
        // Maak een nieuwe PDO-verbinding
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        // Zorg ervoor dat PDO uitzonderingen genereert bij fouten
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query om productgegevens uit de database te halen op basis van het product-ID
        $sql = "SELECT * FROM producten WHERE product_id = :product_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();

        // Haal de resultaten op
        $productData = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Verbinding met de database is mislukt: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        form {
            text-align: center;
            margin: 10px;

        }

        input[type="text"] {
            padding: 10px;
            width: 300px;
            font-size: 16px;
        }

        input[type="button"] {
            cursor: pointer;
        }

        #resultContainer {
            padding: 20px;
            margin: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            height: 300px;
            border: 1px solid #ccc;
            background-color: #2D3748;
            color: #E5E7EB;
            border-radius: 10px;
            padding: 20px;
            margin: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
    </style>

    <script>
        let eventSource = null;

        function startStreaming() {
            const promptInput = document.getElementById('prompt');
            const promptText = promptInput.value;
            const resultContainer = document.getElementById('resultContainer');

            // Maak een EventSource voor streaming
            eventSource = new EventSource(`stream.php?prompt=${encodeURIComponent("kan je het tekst omschrijven: " + promptText)}`);

            eventSource.onmessage = function(event) {
                const responseText = event.data;

                // Voeg de tekst toe aan het resultaatcontainer
                resultContainer.innerHTML += responseText;
                resultContainer.scrollTop = resultContainer.scrollHeight; // Scroll naar het nieuwe bericht

                // Als er een "Stop" is ontvangen, stop met streamen
                if (responseText === 'Stop') {
                    stopStreaming();
                }
            };

            eventSource.onerror = function(error) {
                console.error('Er is een fout opgetreden bij het streamen van resultaten.', error);
                stopStreaming();
            };
        }

        function stopStreaming() {
            if (eventSource) {
                eventSource.close();
                eventSource = null;
            }
        }
    </script>
</head>

<body>

    <div class="bg-gray-800 p-8 rounded-md w-full">
        <div class="flex items-center justify-between pb-6">
            <div>
                <h1 class="text-gray-300 text-4xl">Product</h1>
                <span class="text-2xl text-gray-600">
                    <?php
                    if ($productData !== null) {
                        echo $productData['product_naam'];
                    } else {
                        echo "Product niet gevonden";
                    }
                    ?>
                </span>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex bg-gray-700 items-center rounded-md">
                    <form id="promptForm" method="post">
                        <a href="index.php" class="bg-gray-700 text-white hover:bg-gray-800 font-bold py-2 px-4 rounded inline-block">Terug naar Index</a>
                        <!-- <input name="prompt" id="prompt" value="Charleston net handschoenen parelmoer"> -->
                        <input type="button" class="bg-gray-700 text-white hover:bg-gray-800 font-bold py-2 px-4 rounded inline-block" value="Maak een Omshrijven Product" onclick="startStreaming()">
                        <input type="button" class="bg-gray-700 text-white hover:bg-gray-800 font-bold py-2 px-4 rounded inline-block" value="Stop Streaming" onclick="stopStreaming()">
                    </form>
                </div>
            </div>
        </div>
        <?php if ($productData !== null) : ?>
            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                    <table class="min-w-full leading-normal text-white">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold uppercase tracking-wider">
                                    Title
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold uppercase tracking-wider">
                                    Omschrijving
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold uppercase tracking-wider">
                                    Leverancier
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold uppercase tracking-wider">
                                    Aangemaakt Datum

                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold uppercase tracking-wider">
                                    Zichtbaarheid

                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold uppercase tracking-wider">
                                    Prijs
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="flex items-center">
                                        <div class="dark:hover:bg-neutral-600">
                                            <div class="text-sm font-medium text-white-900 dark:hover:bg-neutral-600"><?= $productData['product_naam'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="flex items-center">
                                        <div class="dark:hover:bg-neutral-600">
                                            <div class="text-sm font-medium text-white-900 dark:hover:bg-neutral-600"> <input class="text-sm font-medium text-white-900 bg-gray-800" name="prompt" id="prompt" value="<?= $productData['omschrijving'] ?>" disabled></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="flex items-center">
                                        <div class="dark:hover:bg-neutral-600">
                                            <div class="text-sm font-medium text-white-900 dark:hover:bg-neutral-600"><?= $productData['leverancier'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="flex items-center">
                                        <div class="dark:hover:bg-neutral-600">
                                            <div class="text-sm font-medium text-white-900 dark:hover:bg-neutral-600"><?= $productData['zichtbaarheid'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="flex items-center">
                                        <div class="dark:hover:bg-neutral-600">
                                            <div class="text-sm font-medium text-white-900 dark:hover:bg-neutral-600"><?= $productData['aangemaakt_datum'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="flex items-center">
                                        <div class="dark:hover:bg-neutral-600">
                                            <div class="text-sm font-medium text-white-900 dark:hover:bg-neutral-600"><?= $productData['prijs'] ?></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        <div id="resultContainer" class="cursor-text">
            <h2 class="text-2xl text-white">Nieuw Omschrijven</h2>
        </div>

    </div>

</body>

</html>
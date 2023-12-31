<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$yourApiKey = $_ENV['API_KEY'];

$host = "localhost";
$username = "bit_academy";
$password = "bit_academy";
$database = 'partycorner';

$productId = null;

if (isset($_GET['id'])) {
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

        @keyframes pop-word {
            to {
                transform: rotateX(0);
            }
        }

        @keyframes show {
            to {
                opacity: 1;
            }
        }

        @keyframes bar-scale {
            to {
                transform: scaleY(1);
            }
        }

        @keyframes sparkle {
            0% {
                transform: scale(0);
            }

            60% {
                transform: scale(1) translate(4px, 1px) rotate(8deg);
            }

            100% {
                transform: scale(0) translate(4px, 1px) rotate(8deg);
            }
        }

        @keyframes shimmer {
            to {
                text-shadow: 0 0 8px red;
            }
        }

        .word {

            animation: show 0.05s forwards, pop-word 1.5s forwards;
            animation-timing-function: cubic-bezier(0.14, 1.23, 0.33, 1.16);
            opacity: 0;

            transform: rotateX(120deg);
            transform-origin: 50% 100%;
        }

        .superscript {
            position: relative;
            animation-delay: 3.6s;
            animation-duration: 0.25s;
            animation-name: shimmer;

            vertical-align: text-top;
        }

        /* bars */
        .superscript::before {
            --bar-width: 25%;

            position: absolute;

            top: 37%;
            left: 47%;
            width: 14%;
            height: 48%;

            animation: bar-scale 0.25s linear 3s 1 forwards;

            background: linear-gradient(to right,
                    white var(--bar-width),
                    transparent var(--bar-width) calc(100% - var(--bar-width)),
                    white calc(100% - var(--bar-width)));

            content: "";

            transform: scaleY(var(--bar-scale-y));
        }

        /* sparkle */
        .superscript::after {
            --size: 10rem;

            position: absolute;

            top: -5%;
            left: -85%;

            width: var(--size);
            height: var(--size);

            animation: sparkle 0.4s linear 3.5s 1 forwards;

            background: radial-gradient(circle at center,
                    rgb(252 249 241 / 94%) 0% 7%,
                    transparent 7% 100%),
                conic-gradient(transparent 0deg 18deg,
                    var(--sparkle-color) 18deg,
                    transparent 20deg 40deg,
                    var(--sparkle-color) 40deg,
                    transparent 43deg 87deg,
                    var(--sparkle-color) 87deg,
                    transparent 95deg 175deg,
                    var(--sparkle-color) 175deg,
                    transparent 178deg 220deg,
                    var(--sparkle-color) 220deg,
                    transparent 222deg 270deg,
                    var(--sparkle-color) 270deg,
                    transparent 275deg 300deg,
                    var(--sparkle-color) 300deg,
                    transparent 303deg 360deg);

            border-radius: 50%;
            clip-path: polygon(50% 0,
                    59.13% 26.64%,
                    85.13% -2.35%,
                    100% 50%,
                    50% 100%,
                    0 50%,
                    31.39% 34.86%);

            content: "";

            filter: blur(1px);

            transform: scale(0);
        }

        @media screen and (max-width: 600px) {
            h1 {
                font-size: 5rem;
            }

            /* sparkle */
            .superscript::after {
                --size: 6rem;
            }
        }
    </style>

    <script>
        let eventSource = null;

        function Herschrijven() {
            const promptInput = document.getElementById('prompt');
            const promptText = promptInput.value;
            const resultContainer = document.getElementById('resultContainer');

            // Maak een EventSource voor streaming
            eventSource = new EventSource(`stream.php?prompt=${encodeURIComponent("kan je het tekst herschrijven: " + promptText)}`);

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

        function omschrijven() {
            const promptInput = document.getElementById('product_naam');
            const promptText = promptInput.value;
            const resultContainer = document.getElementById('resultContainer');

            // Maak een EventSource voor streaming
            eventSource = new EventSource(`stream.php?prompt=${encodeURIComponent("kan je een omschrijven schrijven voor deze title: " + promptText)}`);

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

<body class="bg-gray-800">
    <div class="container mx-auto">
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
                    <div class="flex bg-gray-700 items-center rounded-md ">
                        <form id="promptForm" method="post">
                            <a href="index.php" class=" text-white font-bold rounded inline-block hover:bg-gray-800 py-2 px-4">Terug naar Index</a>
                            <!-- <input name="prompt" id="prompt" value=""> -->
                            <!-- <input type="button" class="bg-gray-700 text-white hover:bg-gray-800 font-bold py-2 px-4 rounded inline-block" value="Maak een Omshrijven Product" > -->
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
                                                <div class="text-sm font-medium text-white-900"><input class="text-sm font-medium text-white-900 bg-gray-800" name="product_naam" id="product_naam" value="<?= $productData['product_naam'] ?>" disabled></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="flex items-center">
                                            <div class="dark:hover:bg-neutral-600">
                                                <div class="text-sm font-medium text-white-900"><input class="text-sm font-medium text-white-900 bg-gray-800" name="prompt" id="prompt" value="<?= $productData['omschrijving'] ?>" disabled></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="flex items-center">
                                            <div class="dark:hover:bg-neutral-600">
                                                <div class="text-sm font-medium text-white-900"><?= $productData['leverancier'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="flex items-center">
                                            <div class="dark:hover:bg-neutral-600">
                                                <div class="text-sm font-medium text-white-900"><?= $productData['aangemaakt_datum'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="flex items-center">
                                            <div class="dark:hover:bg-neutral-600">
                                                <div class="text-sm font-medium text-white-900"><?= $productData['zichtbaarheid'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="flex items-center">
                                            <div class="dark:hover:bg-neutral-600">
                                                <div class="text-sm font-medium text-white-900"><?= $productData['prijs'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            <h1 class="text-3xl text-white text-center mt-8 word">
                <span class="word">Wat kan ik voor u doen?</span>
            </h1>
            <div class="flex justify-center mt-4 word">
                <button id="option1" type="button" class="bg-gray-700 text-white hover:bg-gray-800 font-bold py-2 px-4 rounded inline-block" onclick="omschrijven()">Wil u nieuw omschrijven voor het product?</button>
                <button id="option2" class=" bg-gray-700 text-white hover-bg-gray-800 font-bold py-2 px-4 rounded inline-block ml-4" onclick="Herschrijven()">Wilt u het product opnieuw herschrijven?</button>
            </div>

            <div id="resultContainer" class="cursor-text word">
                <h2 class="text-2xl text-white">Nieuw</h2>
                <input type="button" class="bg-gray-700 text-white hover:bg-gray-800 font-bold py-2 px-4 rounded inline-block absolute bottom-4 right-4" value="Stop Streaming" onclick="stopStreaming()">
            </div>
        </div>
    </div>
</body>

</html>
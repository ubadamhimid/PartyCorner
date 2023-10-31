<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$yourApiKey = $_ENV['API_KEY'];

$host = "localhost";
$username = "bit_academy";
$password = "bit_academy";
$database = 'partycorner';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM producten";
    $producten = $pdo->query($sql);
} catch (PDOException $e) {
    die("Verbinding met de database is mislukt: " . $e->getMessage());
}

$categories = [];
try {
    $sql = "SELECT * FROM categories";
    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $categories[] = $row;
    }
} catch (PDOException $e) {
    die("Fout bij ophalen van categorieën: " . $e->getMessage());
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
            margin-top: 20px;
        }

        input[type="text"] {
            padding: 10px;
            width: 300px;
            font-size: 16px;
        }
    </style>

</head>

<body class="bg-gray-800">
    <div class="container mx-auto">
        <div class="bg-gray-800 p-8 rounded-md w-full">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-gray-300 text-4xl">Products</h1>
                </div>
                <!-- <div class="flex items-center justify-between">
                <div class="flex bg-gray-700 items-center p-2 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    <input class="bg-gray-700 outline-none ml-1 block text-white" type="text" name="" id="" placeholder="search...">
                </div>
            </div> -->
            </div>
            <div>
                <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                    <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                        <table class="min-w-full leading-normal text-white">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold uppercase tracking-wider">
                                        Titel
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold uppercase tracking-wider">
                                        Leverancier
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold uppercase tracking-wider">
                                        Aangemaakt
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold uppercase tracking-wider">
                                        Zichtbaarheid
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold uppercase tracking-wider">
                                        Prijs
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold uppercase tracking-wider">
                                        Edit?
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $producten->fetch()) { ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="flex items-center">
                                                <!-- <div class="flex-shrink-0 w-10 h-10">
                                                <img class="w-full h-full rounded-full" src="https://cdn.webshopapp.com/shops/4119/files/441125122/00641d-1.webp" alt="">
                                            </div> -->
                                                <div class="">
                                                    <div class="text-sm font-medium text-white-900"><?= $row['product_naam'] ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><?= $row['leverancier'] ?></td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><?= $row['aangemaakt_datum'] ?></td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <span class="px-2 py-1 font-semibold leading-tight text-gray-200 rounded-full">Zichtbaar</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">€ <?= number_format($row['prijs'], 2) ?></td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <span class="relative inline-block px-3 py-1 font-semibold text-blue-300 leading-tight">
                                                <span aria-hidden class="absolute inset-0 bg-blue-500 opacity-25 rounded-lg"></span>
                                                <a class="relative" href="edit.php?id=<?= $row['product_id'] ?>">Edit</a>
                                            </span>
                                        </td>
                                    </tr>
                                <?php }; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gray-800 p-8 rounded-md w-full category-table">
            <h1 class="text-gray-300 text-4xl">Categories</h1>
            <table class="min-w-full leading-normal text-white mt-4">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold uppercase tracking-wider">
                            Category Name
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-600 bg-gray-700 text-left text-xs font-semibold uppercase tracking-wider">
                            Edit?
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category) { ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <div class="text-sm font-medium text-white-900"><?= $category['category_name'] ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 max-w-xl"><?= $category['description'] ?></td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                <span class="relative inline-block px-3 py-1 font-semibold text-blue-300 leading-tight">
                                    <span aria-hidden class="absolute inset-0 bg-blue-500 opacity-25 rounded-lg"></span>
                                    <a class="relative" href="editcategory.php?id=<?= $category['category_id'] ?>">Edit</a>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
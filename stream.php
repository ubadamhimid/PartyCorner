<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$yourApiKey = $_ENV['API_KEY'];
$client = OpenAI::client($yourApiKey);

if (isset($_GET['prompt'])) {
    $prompt = $_GET['prompt'];

    $stream = $client->completions()->createStreamed([
        'model' => 'gpt-3.5-turbo-instruct',
        'prompt' => $prompt,
        'max_tokens' => 100,
        'temperature' => 0.9,
    ]);

    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');

    foreach ($stream as $response) {
        $message = $response->choices[0]->text;

        echo "data: $message\n\n";
        ob_flush();
        flush();
        usleep(200000);
    }
} else {
    echo 'Geen prompt ontvangen.';
}
?>

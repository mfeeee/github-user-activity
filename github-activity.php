<?php

$opts = [
    'http' => [
        'method'=>"GET",
        // Use CRLF \r\n to separate multiple headers
        'header' => "Accept: application/json\r\n" .
                    "X-Github-Api-Version: 2022-11-28\r\n" .
                    "User-Agent: Github-User-Activity\r\n"
    ]
];

$context = stream_context_create($opts);

if ($argc < 2) {
    echo "GitHub User Activity Guide\n" . PHP_EOL;
    echo "Usage: php github-activity.php <username>" . PHP_EOL;
    exit(1);
}

$username = $argv[1];
$url = "https://api.github.com/users/{$username}/events";

// Open the file using HTTP headers above
$jsonData = file_get_contents($url, false, $context);
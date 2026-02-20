<?php

if ($argc < 2) {
    echo "GitHub User Activity Guide\n" . PHP_EOL;
    echo "Usage: php github-activity.php <username>" . PHP_EOL;
    exit(1);
}

$username = $argv[1];
$url = "https://api.github.com/users/{$username}/events";
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
// Use @ to suppress the default PHP warning
$jsonData = @file_get_contents($url, false, $context);

if(!$jsonData) {
    echo "Error. This user doesn't exist" . PHP_EOL;
    exit(1);
}

$events = json_decode($jsonData, true);

if(empty($events)) {
    echo "No recent activity found for this user." . PHP_EOL;
    exit;
}

function handleCommitCommentEvent(array $event, array $payload) {
    $repoName = $event['repo']['name'];
    $action = ucfirst($payload['action']);
    echo "- $action commit comment in $repoName" . PHP_EOL;
}

function handleCreateDeleteEvent(array $event, array $payload) {
    $repoName = $event['repo']['name'];
    $type = ($event['type'] === 'CreateEvent') ? 'Created' : 'Deleted';
    $ref_type = $payload['ref_type'];
    $full_ref = $payload['full_ref'];

    $refs = "refs/heads/";
    $full_ref = str_replace($refs, "", $full_ref);

    if (is_null($ref_type)) {
        $ref_type = "";
    } else {
        $ref_type = " " . $ref_type . " ";
    }
    
    if ($type == 'Created') {
        $start = "Created a new";
    } else {
        $start = "Deleted a";
    }

    echo "- $start $ref_type named $full_ref  in $repoName" . PHP_EOL;
}

function handleDiscussionEvent(array $event, array $payload) {
    $repoName = $event['repo']['name'];
    $action = ucfirst($payload['action']);
    echo "- $action a discussion in $repoName" . PHP_EOL;
}

function handleForkEvent(array $payload) {
    $forkedRepo = $payload['forkee']['full_name'] ?? $payload['forkee']['name'] ?? "a repository";
    echo "- Forked $forkedRepo" .  PHP_EOL;
}


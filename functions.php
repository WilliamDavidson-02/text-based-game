<?php

declare(strict_types=1);

function createErrorMsg(string $msg)
{
    $_SESSION['errorMessage'] = $msg;
}

function addToStory(string $command, string $story)
{
    $newPart = [
        'command' => $command,
        'story' => $story
    ];

    array_unshift($_SESSION['storyLine'], $newPart);
}

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

function isSameStory(string $value): bool
{
    foreach ($_SESSION['storyLine'] as $story) {
        if ($story['story'] === $value) {
            return true;
        }
    }
    return false;
}

function isPlayerInRoom(string $room): bool
{
    return $_SESSION['player']['current_room'] === $room ? true : false;
}

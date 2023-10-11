<?php

session_start();

$rooms = [
    [
        'description' => 'room1',
        'exits' => ['north' => 1]
    ],
    [
        'description' => 'room2',
        'exits' => ['south' => 0]
    ],
];

$commands = [
    'movements' => ['north', 'west', 'south', 'east'],
];

$_SESSION['currentRoom'] = isset($_SESSION['currentRoom']) ? $_SESSION['currentRoom'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (in_array($_POST['command'], $commands['movements'])) {
    }
}

?>

<form method="post">
    <input name="command" type="text">
</form>
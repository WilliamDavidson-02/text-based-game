<?php

session_start();

require_once __DIR__ . '/rooms.php';

$commands = [
    'directions' => ['north', 'west', 'south', 'east'],
    'room_interactions' => ['look', 'take', 'move']
];
$errorMessage = null;

if (!isset($_SESSION['player'])) {
    $_SESSION['player'] = [
        'current_room' => 'forest_entrance',
        'inventory' => [],
        'prev_commands' => [],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$actionType, $action] = explode(' ', $_POST['command']);
    switch (strtolower($actionType)) {
        case 'move':
            if (array_key_exists(strtolower($action), $rooms[$_SESSION['player']['current_room']]['connection'])) {
                $_SESSION['player']['current_room'] = $rooms[$_SESSION['player']['current_room']]['connection'][strtolower($action)];
            }
            break;
        case 'look':

            break;
        default:
            $errorMessage = "You are unable to $actionType.";
    }

    // save prev commands, - 6 to get the 5 latest in the array.
    $_SESSION['player']['prev_commands'][] = $_POST['command'];

    if (count($_SESSION['player']['prev_commands']) > 5) {
        array_splice($_SESSION['player']['prev_commands'], count($_SESSION['player']['prev_commands']) - 6);
    }
    // preventing same action to run again if page is reloaded.
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

require_once __DIR__ . '/header.php'; ?>
<main>
    <section class="player-info-container">
        <!-- player info -->
        <div>
            <h3>Player inventory</h3>
        </div>
        <div>
            <h3>Player commands</h3>
            <?php foreach ($_SESSION['player']['prev_commands'] as $command) : ?>
                <div><?= $command; ?></div>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="game-container">
        <div>
            <h1><?= $_SESSION['player']['current_room']; ?></h1>
            <div><?= $rooms[$_SESSION['player']['current_room']]['description']; ?></div>
        </div>
        <form class="form-command" method="post">
            <?php if ($errorMessage !== null) : ?>
                <div class="error-container"><?= $errorMessage; ?></div>
            <?php endif; ?>
            <div class="input-container">
                <input autofocus autocomplete="off" placeholder="e.g move north" name="command" type="text">
                <button type="submit" class="btn">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </section>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
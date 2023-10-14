<?php

session_start();

if (isset($_POST['reset'])) {
    unset($_SESSION['player']);
    unset($_SESSION['storyLine']);
    unset($_SESSION['errorMessage']);

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

require_once __DIR__ . '/rooms.php';
require_once __DIR__ . '/functions.php';

$commands = [
    'directions' => ['north', 'west', 'south', 'east'],
    'room_interactions' => ['look', 'take', 'move']
];

$errorMessage = isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : '';

if (!isset($_SESSION['player'])) {
    $_SESSION['player'] = [
        'current_room' => 'forest_entrance',
        'inventory' => [],
        'prev_commands' => [],
    ];
}

if (!isset($_SESSION['storyLine'])) {
    $_SESSION['storyLine'] = [
        [
            'command' => 'Beginning',
            'story' => $rooms['forest_entrance']['description']
        ],
        [
            'command' => 'Lost in the Enchanted Forest',
            'story' => 'You find yourself in a dense, mysterious forest. The air is thick with magic, and the trees seem to whisper secrets. Your goal is to find the legendary Fountain of Wisdom, rumoured to grant knowledge beyond imagination. Along the way, you\'ll encounter challenges, collect items, and meet mystical creatures.'
        ]
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['errorMessage'] = '';
    [$actionType, $action] = explode(' ', $_POST['command']);
    switch (strtolower($actionType)) {
        case 'move':
            if (array_key_exists(strtolower($action), $rooms[$_SESSION['player']['current_room']]['connection'])) {
                $_SESSION['player']['current_room'] = $rooms[$_SESSION['player']['current_room']]['connection'][strtolower($action)];
                addToStory($_POST['command'], $rooms[$_SESSION['player']['current_room']]['description']);
            } else {
                createErrorMsg("Hmm there seams to be no leading path to $action, maybe try a different direction ?");
            }
            break;
        case 'look':
            if (array_key_exists(strtolower($actionType), $rooms[$_SESSION['player']['current_room']]['actions'])) {
                foreach ($rooms[$_SESSION['player']['current_room']]['actions']['look'] as $looks) {
                    if (!isSameStory($looks)) {
                        addToStory($_POST['command'], $looks);
                    } else {
                        createErrorMsg("Nothing more to see here.");
                    }
                }
            } else {
                createErrorMsg("Nothing special to look at here.");
            }
            break;
        case 'take':
            // get item and remove action type from explode and implode item in to one word.
            $action = $rooms[$_SESSION['player']['current_room']]['actions'];
            if (array_key_exists(strtolower($actionType), $action)) {
                $item = implode(' ', array_splice(explode(' ', $_POST['command']), 1));
                if (!isSameStory($action['take'][$item])) {
                    addToStory($_POST['command'], $action['take'][$item]);
                    $_SESSION['player']['inventory'][] = $item;
                } else {
                    createErrorMsg("You have already picked up $item");
                }
            } else {
                createErrorMsg("Nothing to take here.");
            }
            break;
        default:
            createErrorMsg("You are unable to $actionType.");
    }

    // save prev commands, - 5 to get the 5 latest in the array.
    $_SESSION['player']['prev_commands'][] = $_POST['command'];

    if (count($_SESSION['player']['prev_commands']) > 5) {
        $_SESSION['player']['prev_commands'] = array_splice($_SESSION['player']['prev_commands'], count($_SESSION['player']['prev_commands']) - 5);
    }
    // preventing same action to run again if page is reloaded.
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Redo current room string to a nice title.
$titleExplode = explode('_', $_SESSION['player']['current_room']);
$roomTitle = ucwords(implode(' ', $titleExplode));

require_once __DIR__ . '/header.php'; ?>
<main>
    <section class="player-info-container">
        <div class="player-info-child">
            <div class="player-info">
                <h3>Player inventory</h3>
                <?php foreach ($_SESSION['player']['inventory'] as $item) : ?>
                    <div class="small-dark-tx"><?= $item; ?></div>
                <?php endforeach; ?>
            </div>
            <div class="player-info">
                <h3>Player commands</h3>
                <?php foreach ($_SESSION['player']['prev_commands'] as $command) : ?>
                    <div class="small-dark-tx"><?= $command; ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <form method="post">
            <button type="submit" name="reset" class="brown-btn">New Game</button>
        </form>
    </section>
    <section class="game-container">
        <div class="story-container">
            <h1><?= $roomTitle; ?></h1>
            <div class="story">
                <?php foreach ($_SESSION['storyLine'] as $description) : ?>
                    <div class="story-card">
                        <p class="small-dark-tx"><?= $description['command'] ?></p>
                        <p><?= $description['story'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <form class="form-command" method="post">
            <?php if ($errorMessage !== '') : ?>
                <div class="error-container"><?= $errorMessage; ?></div>
            <?php endif; ?>
            <div class="input-container">
                <input autofocus autocomplete="off" placeholder="e.g move, look or take" name="command" type="text">
                <button type="submit" class="btn">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </section>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
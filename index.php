<?php

session_start();

require_once __DIR__ . '/rooms.php';
require_once __DIR__ . '/functions.php';

if (isset($_POST['reset'])) {
    unset($_SESSION['player']);
    unset($_SESSION['storyLine']);
    unset($_SESSION['errorMessage']);

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$commands = [
    'directions' => ['north', 'west', 'south', 'east'],
    'room_interactions' => ['look', 'take', 'move']
];
$errorMessage = isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : '';
$itemRules = [
    'silver key' => [
        'room' => 'fountain_of_wisdom',
        'on' => 'ornate box',
        'story' => 'With the small, ornate box in hand, you approach the Fountain of Wisdom. As you carefully place the box on the edge of the fountain, a soft, melodic hum fills the air. The gems on the box begin to glow, casting prismatic reflections across the water.

        With a sense of anticipation, you open the box. Inside, you find a brilliant, pulsating crystal, radiating with an ethereal light. As you hold it aloft, a wave of knowledge washes over you, filling your mind with ancient wisdom.
        
        You have succeeded. You have found the legendary Fountain of Wisdom. The forest, once mysterious and foreboding, now feels like an old friend, its secrets unlocked. You carry the crystal with you, a beacon of enlightenment.
        
        As you leave the enchanted forest, you are forever changed. The knowledge you gained will shape your destiny, and the memories of this mystical journey will forever reside in your heart.
        
        Congratulations, brave adventurer. You have completed your quest.'
    ]
];

if (!isset($_SESSION['player'])) {
    $_SESSION['player'] = [
        'current_room' => 'forest_entrance',
        'inventory' => [],
        'prev_commands' => [],
        'health' => 100,
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
    $command = explode(' ', $_POST['command']);
    $actionType = $command[0]; // e.g move, look, take
    switch (strtolower($actionType)) {
        case 'move':
            $action = $command[1]; // direction, e.g north
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
                if (array_key_exists($item, $action['take'])) {
                    if (!isSameStory($action['take'][$item])) {
                        addToStory($_POST['command'], $action['take'][$item]);
                        $_SESSION['player']['inventory'][] = $item;
                    } else {
                        createErrorMsg("You have already picked up $item");
                    }
                } else {
                    createErrorMsg("Hmm there seams to be no $item to take.");
                }
            } else {
                createErrorMsg("Nothing to take here.");
            }
            break;
        case 'use':
            // pick out item to use and to use on also taking account for items with multiple words.
            $itemToUse = [];
            $itemToUseOn = [];
            $wordFlag = false;
            foreach ($command as $word) {
                $word = strtolower($word);
                if ($word === 'use') {
                    $wordFlag = true;
                } else if ($word === 'on') {
                    $wordFlag = false;
                } else {
                    if ($wordFlag) {
                        $itemToUse[] = $word;
                    } else {
                        $itemToUseOn[] = $word;
                    }
                }
            }

            $itemToUse = implode(' ', $itemToUse);
            $itemToUseOn = implode(' ', $itemToUseOn);

            // check if the player has both items.
            if (!in_array($itemToUse, $_SESSION['player']['inventory'])) {
                createErrorMsg("$itemToUse dose not exist in your inventory");
            } else if (!in_array($itemToUseOn, $_SESSION['player']['inventory'])) {
                createErrorMsg("$itemToUseOn dose not exist in your inventory");
            } else {
                // check if the items your are using can be used on the second item
                if ($itemRules[$itemToUse]['on'] === $itemToUseOn) {
                    // check if there is a room the item has to be used in.
                    if (array_key_exists('room', $itemRules[$itemToUse])) {
                        if (isPlayerInRoom($itemRules[$itemToUse]['room'])) {
                            createErrorMsg("You seam to be in the wrong place to use $itemToUse");
                            break;
                        }
                    }
                    if ($itemToUse === 'silver key' && $itemToUseOn === 'ornate box') {
                        addToStory($_POST['command'], $itemRules[$itemToUse]['story']);
                    }
                } else {
                    createErrorMsg("You are unable to use $itemToUse on $itemToUseOn");
                }
            }
            break;
        case 'ask':
            if (isPlayerInRoom('whispering_grove')) {
                // removes ask from string and gets the who it is directed to.
                $question = implode(' ', array_splice(explode(' ', $_POST['command']), 1));
                if (strtolower($question) === 'trees') {
                    addToStory($_POST['command'], $rooms[$_SESSION['player']['current_room']]['actions']['ask']);
                } else {
                    createErrorMsg("Who is $question ?");
                }
            } else {
                createErrorMsg("You are talking to your self.");
            }
            break;
        case 'attack':
            if (isPlayerInRoom('goblin\'s_hideout')) {
            } else {
                createErrorMsg("Relax, no enemies nearby.");
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
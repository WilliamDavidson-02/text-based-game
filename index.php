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
$isGameCompleted = false;
$gamePoints = 0;
$itemRules = [
    'silver key' => [
        'room' => 'fountain_of_wisdom',
        'on' => 'ornate box',
        'story' => "With the small, ornate box in hand, you approach the Fountain of Wisdom. As you carefully place the box on the edge of the fountain, a soft, melodic hum fills the air.
        <br/><br/>
        With a sense of anticipation, you open the box. Inside, you find a brilliant, pulsating crystal, radiating with an ethereal light. As you hold it aloft, a wave of knowledge washes over you, filling your mind with ancient wisdom.
        <br/><br/>
        You have succeeded. You have found the legendary Fountain of Wisdom. The forest, once mysterious and foreboding, now feels like an old friend, its secrets unlocked. You carry the crystal with you, a beacon of enlightenment.
        <br/><br/>
        As you leave the enchanted forest, you are forever changed. The knowledge you gained will shape your destiny, and the memories of this mystical journey will forever reside in your heart.
        <br/><br/>
        Congratulations, brave adventurer. You have completed your quest."
    ],
    'mushroom' => [
        'on' => 'me',
        'story' => 'You eat the mushroom, and now you hare feeling a 100%'
    ]
];

if (!isset($_SESSION['player'])) {
    $_SESSION['player'] = [
        'current_room' => 'forest_entrance',
        'inventory' => ['me'],
        'prev_commands' => [],
        'health' => 100,
        'enemies' => [
            'goblin' => [
                'health' => 100,
                'damage' => 10,
                'room' => 'goblin\'s_hideout'
            ],
            'spider' => [
                'health' => 100,
                'damage' => 50,
                'room' => 'spider\'s_lair'
            ]
        ],
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
            $action = trim($command[1]); // direction, e.g north
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
                $item = trim(implode(' ', array_splice(explode(' ', $_POST['command']), 1)));
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
                $word = trim(strtolower($word));
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
                            addToStory($_POST['command'], $itemRules[$itemToUse]['story']);
                        } else {
                            createErrorMsg("You seam to be in the wrong place to use $itemToUse");
                        }
                    } else {
                        if ($itemToUse === 'mushroom') {
                            $_SESSION['player']['health'] = 100;
                            addToStory($_POST['command'], $itemRules[$itemToUse]['story']);
                            $_SESSION['player']['inventory'] = array_values(array_filter($_SESSION['player']['inventory'], function ($item) use ($itemToUse, $itemToUseOn) {
                                return $item !== $itemToUse;
                            }));
                        }
                    }
                } else {
                    createErrorMsg("You are unable to use $itemToUse on $itemToUseOn");
                }
            }
            break;
        case 'ask':
            if (array_key_exists('ask', $rooms[$_SESSION['player']['current_room']]['actions'])) {
                // removes ask from string and gets the who it is directed to.
                $question = trim(implode(' ', array_splice(explode(' ', $_POST['command']), 1)));
                if (array_key_exists(strtolower($question), $rooms[$_SESSION['player']['current_room']]['actions']['ask'])) {
                    addToStory($_POST['command'], $rooms[$_SESSION['player']['current_room']]['actions']['ask'][strtolower($question)]);
                } else {
                    createErrorMsg("Who is $question ?");
                }
            } else {
                createErrorMsg("You are talking to your self.");
            }
            break;
        case 'attack':
            $victim = trim(implode(' ', array_splice($command, 1)));
            if (isPlayerInRoom($_SESSION['player']['enemies'][$victim]['room'])) {
                if (array_key_exists(strtolower($victim), $_SESSION['player']['enemies'])) {
                    $dominator = rand(0, 2);

                    if ($dominator == 0) {
                        // Player wins
                        $_SESSION['player']['enemies'][$victim]['health'] -= in_array('sword', $_SESSION['player']['inventory']) ? 40 : 5;
                    } elseif ($dominator == 2) {
                        // Enemy wins
                        $_SESSION['player']['health'] -= $_SESSION['player']['enemies'][$victim]['damage'];
                    }

                    if ($_SESSION['player']['enemies'][$victim]['health'] <= 0) {
                        addToStory($_POST['command'], $rooms[$_SESSION['player']['current_room']]['actions']['attack']['kill']);
                    } else {
                        addToStory($_POST['command'], $rooms[$_SESSION['player']['current_room']]['actions']['attack']['story'][$dominator]);
                    }
                } else {
                    createErrorMsg("Who is $victim");
                }
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

if (isPlayerInRoom('fountain_of_wisdom')) {
    foreach ($_SESSION['storyLine'] as $story) {
        if ($story['story'] === $itemRules['silver key']['story']) {
            $isGameCompleted = true;
            break;
        }
    }
}

if ($isGameCompleted) {
    // Calculate end game points
    if ($_SESSION['player']['enemies']['goblin']['health'] <= 0) {
        $gamePoints += 10;
    }
    if ($_SESSION['player']['enemies']['spider']['health'] <= 0) {
        $gamePoints += 20;
    }
    if (in_array('feather', $_SESSION['player']['inventory'])) {
        $gamePoints += 5;
    }
}

// Redo current room string to a nice title.
$titleExplode = explode('_', $_SESSION['player']['current_room']);
$roomTitle = ucwords(implode(' ', $titleExplode));

require_once __DIR__ . '/header.php'; ?>
<main>
    <?php if ($_SESSION['player']['health'] <= 0) : ?>
        <div class="over-container">
            <div class="over-content">
                <h1>Game over</h1>
                <p>You where killed</p>
                <form style="width: 100%;" method="post">
                    <button type="submit" name="reset" class="brown-btn">New Game</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
    <section class="player-info-container">
        <div class="player-info-child">
            <?php if ($isGameCompleted) : ?>
                <div class="player-info">
                    <h3>Game completed</h3>
                    <div>Points: <?= $gamePoints; ?></div>
                </div>
            <?php endif; ?>
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
            <div class="player-info">
                <h3>Game commands</h3>
                <div class="word-card">
                    <span>Move</span>
                    <span>Look</span>
                    <span>Take</span>
                    <span>Ask</span>
                    <span>Attack</span>
                    <span>Use</span>
                </div>
                <div class="examples-container">
                    <span>Examples</span>
                    <span><strong>move</strong> north</span>
                    <span><strong>attack</strong> enemy</span>
                    <span><strong>take</strong> item</span>
                    <span><strong>use</strong> item <strong>on</strong> another item</span>
                </div>
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
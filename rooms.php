<?php

$rooms = [
    'forest_entrance' => [
        'description' => 'You stand at the entrance of the forest. Towering trees loom overhead, their branches reaching for the sky. The air is cool and carries a faint scent of moss and earth. Before you begin your adventure take a look',
        'actions' => [
            'look' => ['You notice a shimmering object half-buried in the soft soil, could it be a sword ?', 'There seams to be a path north.'],
            'take' => ['sword' => 'As you pick it up, a mysterious energy pulses through your veins, urging you north.']
        ],
        'connection' => ['north' => 'mystical_clearing']
    ],
    'mystical_clearing' => [
        'description' => 'A small, peaceful clearing surrounded by ancient trees. Sunlight filters through the leaves, casting dappled patterns on the forest floor. In the center, there\'s a sparkling pool fed by a gentle stream. You can hear the soothing sound of running water.',
        'actions' => [
            'look' => ['A delicate silver key hangs from a low branch, catching the light.', 'West of you there is something whispering, what could it be.'],
            'take' => ['silver key' => 'With the key in hand, you feel a sense of purpose.']
        ],
        'connection' => ['west' => 'whispering_grove', 'south' => 'forest_entrance']
    ],
    'whispering_grove' => [
        'description' => 'This grove feels alive with magic. The trees here have faces carved into their bark, and they seem to murmur secrets to each other, maybe you should ask the trees there secret ? Don\'t forget to look around',
        'actions' => [
            'look' => ['A stone pedestal at the center of the grove holds a mysterious, ornate box.'],
            'take' => ['ornate box' => 'You picked up the ornate box, the box is locked.'],
            'ask' => 'I\'m where the compass points, where the cold winds blow,
            In the direction where the polar lights glow.'
        ],
        'connection' => ['east' => 'mystical_clearing', 'north' => 'goblin\'s_hideout']
    ],
    'goblin\'s_hideout' => [
        'description' => 'You find yourself standing at the entrance of the Goblin\'s Hideout, a dark and foreboding place nestled within the heart of the ancient forest. The air is thick with the earthy scent of moss and damp leaves, and the sounds of rustling leaves and distant animal calls echo through the trees.',
        'actions' => [
            'look' => ['In the center of the hideout, you spot the menacing figure of a goblin, its wiry form hunched over, guarding the passage that leads deeper into the forest. Its eyes gleam with a malevolent glint, and a wicked grin stretches across its twisted face. The creature brandishes a crude, serrated blade, clearly intent on preventing anyone from advancing further.'],
        ],
        'connection' => ['south' => 'whispering_grove']
    ],
    // '' => [
    //     'description' => '',
    //     'actions' => [
    //         'look' => [''],
    //         'take' => ['' => ''],
    //     ],
    //     'connection' => ['' => '']
    // ],
    // '' => [
    //     'description' => '',
    //     'actions' => [
    //         'look' => [''],
    //         'take' => ['' => ''],
    //     ],
    //     'connection' => ['' => '']
    // ],
    // '' => [
    //     'description' => '',
    //     'actions' => [
    //         'look' => [''],
    //         'take' => ['' => ''],
    //     ],
    //     'connection' => ['' => '']
    // ],
    // '' => [
    //     'description' => '',
    //     'actions' => [
    //         'look' => [''],
    //         'take' => ['' => ''],
    //     ],
    //     'connection' => ['' => '']
    // ],
    // '' => [
    //     'description' => '',
    //     'actions' => [
    //         'look' => [''],
    //         'take' => ['' => ''],
    //     ],
    //     'connection' => ['' => '']
    // ],
];

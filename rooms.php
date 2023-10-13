<?php

$rooms = [
    'forest_entrance' => [
        'description' => 'You stand at the entrance of the forest. Towering trees loom overhead, their branches reaching for the sky. The air is cool and carries a faint scent of moss and earth. Before you begin your adventure take a look around',
        'actions' => [
            'look' => ['You notice a glimmering object half-buried in the soft soil.'],
            'take' => 'As you pick it up, a mysterious energy pulses through your veins, urging you north.'
        ],
        'item' => ['Shimmering object'],
        'connection' => ['north' => 'mystical_clearing']
    ],
    'mystical_clearing' => [
        'description' => 'A small, peaceful clearing surrounded by ancient trees. Sunlight filters through the leaves, casting dappled patterns on the forest floor. In the center, there\'s a sparkling pool fed by a gentle stream. You can hear the soothing sound of running water.',
        'actions' => [
            'look' => ['A delicate silver key hangs from a low branch, catching the light.', 'West of you there is something whispering, what could it be.'],
            'take' => 'With the key in hand, you feel a sense of purpose.'
        ],
        'item' => ['Silver key'],
        'connection' => ['west' => 'whispering_grove', 'south' => 'forest_entrance']
    ],
    'whispering_grove' => [
        'description' => 'This grove feels alive with magic. The trees here have faces carved into their bark, and they seem to murmur secrets to each other. A soft, ethereal light bathes the area, creating an otherworldly atmosphere.',
        'actions' => [
            'look' => ['A stone pedestal at the center of the grove holds a mysterious, ornate box.'],
            'take' => 'You take the ornate box, the box is locked.',
            'use' => ['key' => 'This key dose not work on the ornate box']
        ],
        'item' => ['ornate box'],
        'connection' => ['east' => 'mystical_clearing']
    ],
];

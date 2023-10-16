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
            'ask' => ['trees' => 'I\'m where the compass points, where the cold winds blow,
            In the direction where the polar lights glow.']
        ],
        'connection' => ['east' => 'mystical_clearing', 'north' => 'goblin\'s_hideout']
    ],
    'goblin\'s_hideout' => [
        'description' => 'You find yourself standing at the entrance of the Goblin\'s Hideout, a dark and foreboding place nestled within the heart of the ancient forest. The air is thick with the earthy scent of moss and damp leaves, and the sounds of rustling leaves and distant animal calls echo through the trees.',
        'actions' => [
            'look' => ['In the center of the hideout, you spot the menacing figure of a goblin, its wiry form hunched over, guarding the passage that leads deeper into the forest. Its eyes gleam with a malevolent glint, and a wicked grin stretches across its twisted face. The creature brandishes a crude, serrated blade, clearly intent on preventing anyone from advancing further.'],
            'attack' => [
                'story' => ['You swiftly unsheathes their sword and lunges at the goblin. The blade connects, dealing a solid blow. The goblin grimaces, clearly wounded.', 'In a tense exchange, you and goblin clash with equal force. Blades meet, but neither side gains the upper hand.', 'As you lunge, the goblin anticipates and counters with a swift strike of its own. The blade finds its mark, delivering a sharp blow. You staggers back, feeling the sting of the attack, while the goblin grins, seizing the upper hand in the skirmish.'],
                'kill' => 'The goblin lies defeated, its menacing presence replaced by stillness. You stands victorious, the hideout is now a silent witness to the battle\'s end. The Enchanted Glen is east of you once blocked by the goblin no is free to pass'
            ]
        ],
        'connection' => ['south' => 'whispering_grove', 'east' => 'enchanted_glen']
    ],
    'enchanted_glen' => [
        'description' => 'A hidden glen where time seems to stand still. Vibrant, luminescent mushrooms carpet the ground, casting an enchanting glow. A gentle breeze carries the sweet scent of wildflowers',
        'actions' => [
            'look' => ['The glowing mushrooms seams to be the legendary enchanted healing mushrooms, if you are feeling weak take a mushroom and use it on your self "me".', 'As you look around you notice spider web around the mushrooms the further you walk in the glen the more spider web there is, take a deep breath because north of you the spider\'s lair is waiting for you ready you sword.'],
            'take' => ['mushroom' => 'You pick a mushroom from the ground.'],
        ],
        'connection' => ['north' => 'spider\'s_lair']
    ],
    'spider\'s_lair' => [
        'description' => 'A web-strewn chamber, dimly lit by the bioluminescence fungi. Silken threads glisten in the half-light, hinting at the presence of its weaver. You tread carefully, mindful of the intricate traps set by its resident.',
        'actions' => [
            'look' => ['You look above you and se multiple red eyes, oh no the spider has spotted you, this is your chance attack the spider before it attacks you', 'North of you lies the exit of the lair, do you make a run for it'],
            'attack' => [
                'story' => [
                    'You deftly maneuver, avoiding the spider\'s fangs and striking swiftly. The blade connects, delivering a decisive blow. The spider recoils, clearly wounded.',
                    'In a tense exchange, you and spider clash with equal force. The spider\'s fangs snap dangerously, but your reflexes hold firm, preventing a successful strike.',
                    'The spider lunges with surprising speed, sinking its fangs into the your arm. Pain shoots through your limb as you stagger back, narrowly avoiding a more serious injury. The spider eyes you hungrily, reveling in its momentary victory.'
                ],
                'kill' => 'The spider lies defeated, its menacing presence replaced by stillness. You stand victorious, the hideout now a silent witness to the battle\'s end.'
            ]
        ],
        'connection' => ['north' => 'wise_owl\'s_perch']
    ],
    'wise_owl\'s_perch' => [
        'description' => 'At the heart of a colossal tree, you find the cozy nest of a wise old owl. Moonlight filters through the leaves, illuminating shelves of ancient tomes. The owl regards you with keen, knowing eyes.',
        'actions' => [
            'look' => ['A feather, seemingly imbued with ancient magic, rests on a nearby book.'],
            'take' => ['feather' => 'As you take the feather, the owl hoots softly, urging you to follow the path north.'],
        ],
        'connection' => ['north' => 'riddle_bridge']
    ],
    'riddle_bridge' => [
        'description' => 'The Riddle Bridge spans the serene river, its ancient stones weathered by time. Moss and lichen cling to its surface, whispering secrets of old. Ask the bridge it\'s secrete.',
        'actions' => [
            'ask' => ['bridge' => 'I\'m a bridge that plays a tricky game,
            No matter which way you aim,
            west, east, or north you stride,
            the Fountain of Wisdom is where you will arrive'],
        ],
        'connection' => ['west' => 'fountain_of_wisdom', 'east' => 'fountain_of_wisdom', 'north' => 'fountain_of_wisdom', 'south' => 'fountain_of_wisdom']
    ],
    'fountain_of_wisdom' => [
        'description' => 'You have arrived at your destination. Before you stands the Fountain of Wisdom, a marvel of ancient craftsmanship. Crystal-clear water flows from the mouth of a wise stone visage, its eyes seeming to impart knowledge. The air hums with an electric charge.',
        'actions' => [
            'look' => ['As you step into the room, a soft, ethereal glow emanates from the crystal atop the ornate box in your inventory. It catches your attention, casting an enchanting light across the room. The box, with its intricate carvings and polished surface, seems to beckon to you. It\'s as if the presence of the Fountain of Wisdom has awakened some hidden magic within it. Now might be the perfect moment to try opening it, perhaps with the silver key you possess.']
        ]
    ],
];

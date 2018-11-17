<?php

return [
    'php' => 'php',
    'find' => 'find',
    'phpcs' => base_path('vendor/squizlabs/php_codesniffer/bin/phpcs'),
    'phpcbf' => base_path('vendor/squizlabs/php_codesniffer/bin/phpcbf'),
    'phpcs_standard' => '--standard=config/phpcs/',
    'phpmd' => base_path('vendor/phpmd/phpmd/src/bin/phpmd'),
    'phpmd_standard' => 'config/phpmd/rulesets/cleancode'.
        ',config/phpmd/rulesets/codesize'.
        ',config/phpmd/rulesets/controversial'.
        ',config/phpmd/rulesets/design'.
        ',config/phpmd/rulesets/naming'.
        ',config/phpmd/rulesets/unusedcode',
    'phpcs_target' => 'tests routes config app',
    'phplint_target' => 'tests routes config app',
    'phpmd_target' => 'tests routes config app',
];

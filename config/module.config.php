<?php
return array(
    'widgetizer' => array(
        // Management Template Name, this is a not final yimaTheme template
        // Achieved and Loaded by "ManagementThemeResolver" ThemeResolver
        'management_template'  => 'builderfront',

        // yimaAuthorize Module Permission Service To Enable Management Template
        'authorize_permission' => 'yima_adminor',
    ),

    // ---- Attach Assets and Surrounding Area and Widgets Template ------------------------------------------\
    'yima-theme' => array(
        'theme_locator' => array(
            'resolver_adapter_service' => array(
                'Widgetizer\Mvc\ManagementThemeResolver' => 1000,
            ),
        ),

        'themes' => array(
            # default management template
            'builderfront' => array(
                'dir_path' => __DIR__ .DS. '..' .DS. 'themes',
            ),
        ),
    ),
);

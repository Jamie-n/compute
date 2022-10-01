<?php

return [
    /**
     * The name of the application.
     */
    'name' => 'Compute',

    /**
     * The environment to use by default.
     */
    'default' => 'production',

    'environments' => [

        'production' => [

            /**
             * SSH hostname to use to connect to the server.
             */
            'ssh_host' => '167.172.184.208',

            /**
             * SSH user to use to connect to the server.
             */
            'ssh_user' => 'root',

            /**
             * The path on the remote server where the application should be deployed.
             */
            'deploy_path' => '/var/www/compute',

            /**
             * URL to the repository.
             */
            'repository_url' => 'git@github.com:hudds-awp-cht2520/assignment-02-Jamie-n.git',

            /**
             * Listed files will be symlinked into each release directory.
             */
            'linked_files'   => ['.env'],

            /**
             * Listed directories will be symlinked into the release directory.
             */
            'linked_dirs'    => ['storage/app', 'storage/framework', 'storage/logs'],

            /**
             * Listed directories will be copied from the current release into the release directory.
             */
            'copied_dirs'    => ['node_modules', 'vendor'],

            /**
             * The last n releases are kept for possible rollbacks.
             */
            'keep_releases' => 3,


            /**
             * Additional composer options.
             */
            'cmd_composer_options' => ''
        ],
    ],
];

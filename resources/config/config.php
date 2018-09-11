<?php

return [

      /*
      |--------------------------------------------------------------------------
      | Github Registry Username
      |--------------------------------------------------------------------------
      |
      | The default Builder Extension registry
      |
      */
      'registry'           => env('BUILDER_REGISTRY', 'pyrocms-templates'),

      /*
      |--------------------------------------------------------------------------
      | Template of templates
      |--------------------------------------------------------------------------
      |
      | To create an addon template, that used to create addons.
      |
      */
      'default-template'  => env('BUILDER_DEFAULT_TEMPLATE', 'template-template'),

      /*
      |--------------------------------------------------------------------------
      | Default Templates
      |--------------------------------------------------------------------------
      |
      | The default templates used by the Builder to scaffold addon code When
      | the user invoke make:addon command.
      |
      */
      'templates' => [
        'module' => env('BUILDER_DEFAULT_MODULE', 'default-module'),
        'skin' => env('BUILDER_DEFAULT_MODULE', 'default-skin'),
        'theme' => [
            env('BUILDER_DEFAULT_ADMIN', 'pyrocms-theme'),
            env('BUILDER_DEFAULT_FRONT', 'starter-theme')
          ]
      ],

      /*
      |--------------------------------------------------------------------------
      | Cache Minutes
      |--------------------------------------------------------------------------
      |
      | How long an API should be cached
      |
      |
      */
      'ttl' => env('BUILDER_TTL', 60),

      /*
      |--------------------------------------------------------------------------
      | Storage path
      |--------------------------------------------------------------------------
      |
      | Where templates are stored in the storage folder
      |
      | i.e. 'storage/streams/default/builder'
      |
      |
      */
      'path' => env('BUILDER_PATH', 'builder'),

      /*
      |--------------------------------------------------------------------------
      | Builder template archive url
      |--------------------------------------------------------------------------
      |
      | Url template used to download a compressed template from its repo
      |
      |
      */
      'archive' => env('BUILDER_ARCHIVE_URL', 'http://github.com/{{ registry }}/{{ template }}/archive/master.zip'),

      /*
      |--------------------------------------------------------------------------
      | Builder template temp file
      |--------------------------------------------------------------------------
      |
      | Location and name of template compressed file which is stored in the
      | builder path (see above)
      |
      |
      */
      'tmp' => env('BUILDER_TMP', 'tmp/master.zip'),

      /*
      |--------------------------------------------------------------------------
      | Migration attributes spacing
      |--------------------------------------------------------------------------
      |
      | Padding (number of chars) between the 'key', 'value' of
      | associative arrays inside a migration file (keep'me tidy)
      |
      |
      */
      'padding' => env('MIGRATION_PADDING', '30'),

];

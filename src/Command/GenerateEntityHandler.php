<?php

namespace Websemantics\EntityBuilderExtension\Command;

use Websemantics\EntityBuilderExtension\Command\Traits\TemplateProcessor;
use Websemantics\EntityBuilderExtension\Command\GenerateEntity;
use Websemantics\EntityBuilderExtension\Filesystem\Filesystem;
use Websemantics\EntityBuilderExtension\Parser\EntityNameParser;
use Websemantics\EntityBuilderExtension\Parser\ModuleNameParser;
use Websemantics\EntityBuilderExtension\Parser\VendorNameParser;
use Websemantics\EntityBuilderExtension\Parser\NamespaceParser;
use Websemantics\EntityBuilderExtension\Parser\SeedersParser;
use Websemantics\EntityBuilderExtension\Parser\EntityLabelParser;
use Anomaly\Streams\Platform\Addon\Module\Module;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Anomaly\Streams\Platform\Support\Parser;

/**
 * Class GenerateEntityHandler.
 *
 * This handles 'StreamWasCreated' event
 *
 * @link      http://websemantics.ca/ibuild
 * @link      http://ibuild.io
 *
 * @author    WebSemantics, Inc. <info@websemantics.ca>
 * @author    Adnan Sagar <msagar@websemantics.ca>
 */
class GenerateEntityHandler
{
    use TemplateProcessor;

    /**
     * Create a new GenerateEntityHandler instance.
     *
     * @param Filesystem  $files
     * @param Parser      $parser
     * @param Application $application
     */
    public function __construct(Filesystem $files, Parser $parser)
    {
        $this->setFiles($files);
        $this->setParser($parser);
    }

    /**
     * Handle the command.
     *
     * @param GenerateEntity $command
     */
    public function handle(GenerateEntity $command)
    {
        $stream = $command->getStream();
        $module = $command->getModule();

        $entityPath = __DIR__.'/../../resources/assets/entity';
        $modulePath = __DIR__.'/../../resources/assets/module';

        $namespace_folder = ebxGetNamespaceFolderTemplate($module);

        $data = $this->getTemplateData($module, $stream);

        $this->files->setAvoidOverwrite(ebxGetAvoidOverwrite($module, [
            // $data['module_name'] . 'ModuleSeeder.php',
            // $data['module_name'] . 'Module.php',
            // $data['module_name'] . 'ModuleServiceProvider.php',
            ]));

        $destination = $module->getPath();

        /* Make sure Module's main files are present: Seeder etc
        (TODO, optomize, doesn't have to run everytime) */
        $this->files->parseDirectory(
            $modulePath.'/src',
            $destination.'/src',
            $data
        );

        $this->files->parseDirectory(
            $entityPath."/code/$namespace_folder",
            $destination.'/src',
            $data
        );

        try {
            $this->processFile(
                $destination.'/src/'.$data['module_name'].'ModuleServiceProvider.php',
                ['routes' => $entityPath.'/templates/module/routes.php',
                 'bindings' => $entityPath.'/templates/module/bindings.php',
                'singletons' => $entityPath.'/templates/module/singletons.php',
                ],
                $data
            );

            $this->processFile(
                $destination.'/src/'.$data['module_name'].'Module.php',
                ['sections' => $entityPath.'/templates/module/sections.php'],
                $data
            );

            $this->processFile(
                $destination.'/src/'.$data['module_name'].'ModuleSeeder.php',
                ['seeders' => $entityPath.'/templates/module/seeding.php'],
                $data
            );

            $this->processFile(
                $destination.'/resources/lang/en/section.php',
                [$data['entity_name_lower_plural'] => $entityPath.'/templates/module/section.php'],
                $data
            );

            $this->processFile(
                $destination.'/resources/lang/en/stream.php',
                [$data['stream_slug'] => $entityPath.'/templates/module/stream.php'],
                $data
            );
        } catch (\PhpParser\Error $e) {
            die($e->getMessage());
        }
    }

    /**
     * process a language file.
     *
     * @param string $file,     a php file to modify
     * @param string $templates file location
     * @param string $data      used to replace placeholders inside all template files
     */
    protected function processLanguage($file, $template, $data)
    {
        $this->processTemplate($file, $template, $data, 'return [', '];');
    }

    /**
     * Get the template data from a stream object.
     *
     * @param Module          $module
     * @param StreamInterface $stream
     *
     * @return array
     */
    protected function getTemplateData(Module $module, StreamInterface $stream)
    {
        $entityName = (new EntityNameParser())->parse($stream);
        $entityLabel = (new EntityLabelParser())->parse($stream);
        $moduleName = (new ModuleNameParser())->parse($module);
        $namespace = (new NamespaceParser())->parse($stream);
        $vendorName = (new VendorNameParser())->parse($module);

        // Wheather we use a grouping folder for all streams with the same namespace
        $namespace_folder = ebxGetNamespaceFolder($module, $namespace);

        return [
            'docblock' => ebxGetDocblock($module),
            'namespace' => $namespace,
            'seeder_data' => (new SeedersParser())->parse($module, $stream),
            'namespace_folder' => $namespace_folder,
            'vendor_name' => $vendorName,
            'vendor_name_lower' => strtolower($vendorName),
            'module_name' => $moduleName,
            'module_name_lower' => strtolower($moduleName),
            'stream_slug' => $stream->getSlug(),
            'studly_case_stream_slug' => studly_case($stream->getSlug()),
            'entity_label' => $entityLabel,
            'entity_label_plural' => str_plural($entityLabel),
            'entity_name' => $entityName,
            'entity_name_plural' => str_plural($entityName),
            'entity_name_lower' => strtolower($entityName),
            'entity_name_lower_plural' => strtolower(str_plural($entityName)),
            'extends_repository' => ebxExtendsRepository($module),
            'extends_repository_use' => ebxExtendsRepositoryUse($module),

        ];
    }
}

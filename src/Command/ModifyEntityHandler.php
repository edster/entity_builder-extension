<?php

namespace Websemantics\EntityBuilderExtension\Command;

use Websemantics\EntityBuilderExtension\Command\ModifyEntity;
use Websemantics\EntityBuilderExtension\Filesystem\Filesystem;
use Websemantics\EntityBuilderExtension\Command\Traits\TemplateProcessor;
use Anomaly\Streams\Platform\Addon\Module\Module;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Anomaly\Streams\Platform\Assignment\AssignmentModel;
use Anomaly\Streams\Platform\Support\Parser;
use Websemantics\EntityBuilderExtension\Parser\EntityNameParser;
use Websemantics\EntityBuilderExtension\Parser\ModuleNameParser;
use Websemantics\EntityBuilderExtension\Parser\VendorNameParser;
use Websemantics\EntityBuilderExtension\Parser\NamespaceParser;
use Websemantics\EntityBuilderExtension\Parser\AssignmentSlugParser;
use Websemantics\EntityBuilderExtension\Parser\AssignmentLabelParser;

/**
 * Class ModifyEntityHandler.
 *
 * This handles 'AssignmentWasCreated' event
 *
 * @link      http://websemantics.ca/ibuild
 * @link      http://ibuild.io
 *
 * @author    WebSemantics, Inc. <info@websemantics.ca>
 * @author    Adnan Sagar <msagar@websemantics.ca>
 */
class ModifyEntityHandler
{
    use TemplateProcessor;

    /**
     * Create a new ModifyEntityHandler instance.
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
     * @param ModifyEntity $command
     */
    public function handle(ModifyEntity $command)
    {
        $module = $command->getModule();
        $stream = $command->getStream();
        $assignment = $command->getAssignment();

        // Get the field config params from build.php
        $field_config = ebxGetFieldConfig(
            $module,
            $stream->getNamespace(),
            $assignment->getFieldSlug()
        );

        $entity = __DIR__.'/../../resources/assets/entity';

        // Set a list of files to avoid overwrite
        $this->files->setAvoidOverwrite(ebxGetAvoidOverwrite($module));

        // Get the template data
        $data = $this->getTemplateData($module, $stream, $assignment, $field_config);

        $source = $entity.'/code/{namespace}/';

        // Get the namespace destination folder, if any!
        $namespace_folder = ebxGetNamespaceFolder($module, $data['namespace'], true);

        $destination = $module->getPath();

        $entity_destination = $destination.'/src/'.$namespace_folder.'/'.$data['entity_name'];

        // Get the assigned class name, i.e. TextFieldType
        $fieldTypeClassName = ebxGetFieldTypeClassName($assignment);

        // (1) Process the form builder class
        if (!$field_config['hide_field']) {
            $this->processFormBuilder(
                $entity_destination.'/Form/'.
                $data['entity_name'].'FormBuilder.php',
                $entity."/templates/field/form/$fieldTypeClassName.txt",
                $data
            );
        }

        // (2) Process the table column class
        if (!$field_config['hide_column']) {
            $this->processTableColumns(
                $entity_destination.'/Table/'.
                $data['entity_name'].'TableColumns.php',
                $entity.'/templates/field/table/'.($data['column_template'] ? 'template/' : '')."$fieldTypeClassName.txt",
                $data
            );
        }

        // (3) Process the field language file
        $this->processFile(
            $destination.'/resources/lang/en/field.php',
            [$data['field_slug'] => $entity.'/templates/module/field.php'],
            $data
        );
    }

    /**
     * process the form builder and add fields to it.
     *
     * @param string $file,     a php file to modify
     * @param string $templates file location
     * @param string $data      used to replace placeholders inside all template files
     */
    protected function processFormBuilder($file, $template, $data)
    {
        $this->processTemplate($file, $template, $data, 'protected $fields = [', '];');
    }

    /**
     * process the table columns and add fields to it.
     *
     * @param string $file,     a php file to modify
     * @param string $templates file location
     * @param string $data      used to replace placeholders inside all template files
     */
    protected function processTableColumns($file, $template, $data)
    {
        $this->processTemplate($file, $template, $data, '$builder->setColumns([', ']);');
    }

    /**
     * Get the template data from a stream object.
     *
     * @param Module          $module
     * @param StreamInterface $stream
     * @param AssignmentModel $assignment
     * @param array           $field_config
     *
     * @return array
     */
    protected function getTemplateData(
        Module $module,
        StreamInterface $stream,
        AssignmentModel $assignment,
        $field_config
    ) {
        $entityName = (new EntityNameParser())->parse($stream);
        $moduleName = (new ModuleNameParser())->parse($module);
        $fieldSlug = (new AssignmentSlugParser())->parse($assignment);
        $fieldLabel = (new AssignmentLabelParser())->parse($assignment);
        $namespace = (new NamespaceParser())->parse($stream);



        // Wheather we use a grouping folder for all streams with the same namespace
        $namespace_folder = ebxGetNamespaceFolder($module, $namespace);

        return [
            'namespace' => $namespace,
            'namespace_folder' => $namespace_folder,
            'vendor_name' => (new VendorNameParser())->parse($module),
            'module_name' => $moduleName,
            'entity_name' => $entityName,
            'field_slug' => $fieldSlug,
            'field_label' => $fieldLabel,
            'relation_name' => camel_case($fieldSlug),
            'null_relationship_entry' => ebxNullRelationshipEntry($module),
            'column_template' => $field_config['column_template'],
        ];
    }
}

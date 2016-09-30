<?php namespace Websemantics\BuilderExtension\Command;

use Websemantics\BuilderExtension\Traits\TemplateProcessor;
use Anomaly\Streams\Platform\Addon\Module\Module;

/**
 * Class ModifyModule.
 *
 * @link      http://websemantics.ca/ibuild
 * @link      http://ibuild.io
 * @author    WebSemantics, Inc. <info@websemantics.ca>
 * @author    Adnan M.Sagar, Phd. <adnan@websemantics.ca>
 * @copyright 2012-2016 Web Semantics, Inc.
 * @package   Websemantics\BuilderExtension
 */

class ModifyModule
{
  use TemplateProcessor;

    /**
     * The module class.
     *
     * @var \Anomaly\Streams\Platform\Addon\Module\Module
     */
    protected $module;

    /**
     * Create a new ModifyModule instance.
     *
     * @param Module $module
     * @param Filesystem  $files
     * @param Parser      $parser
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
        $this->setFiles(app('Websemantics\BuilderExtension\Filesystem\Filesystem'));
        $this->setParser(app('Websemantics\BuilderExtension\Support\TwigParser'));
    }

    /**
     * Handle the command.
     *
     * Add a default Module route, language entries etc per Module
     *
     */
    public function handle()
    {
        $module = $this->module;
        $data = $this->getTemplateData($module);
        $module_name = studly_case($data['module']['slug']);
        $src = __DIR__.'/../../resources/stubs/module';

        try {
          if(config($module->getNamespace('builder.landing_page'))){

            /* adding routes to the module service provider class
            (currently, just for the optional landing (home) page) */
            $this->processFile($module->getPath().'/src/'.$module_name.'ModuleServiceProvider.php',
                ['routes' => $src.'/routes.php'], $data);

            /* adding sections to the module class
            (currently, just for the optional landing (home) page)*/
            $this->processFile($module->getPath().'/src/'.$module_name.'Module.php',
                              ['sections' => $src.'/sections.php'], $data, true);
          }

          /* adding module icon */
          $this->processVariable($module->getPath().'/src/'.$module_name.'Module.php',
          ' "'.config($module->getNamespace('builder.icon')).'"','protected $icon =', ';');

        } catch (\PhpParser\Error $e) {
            die($e->getMessage());
        }
    }

    /**
     * Get template data.
     *
     * @param Module          $module
     * @param StreamInterface $stream
     *
     * @return array
     */
    protected function getTemplateData(Module $module)
    {
      return [
          'config' => config($module->getNamespace('builder')),
          'vendor' => $module->getVendor(),
          'module_slug' => $module->getSlug()
      ];
    }
}

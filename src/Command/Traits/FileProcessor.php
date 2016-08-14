<?php namespace Websemantics\EntityBuilderExtension\Command\Traits;

use Websemantics\EntityBuilderExtension\Parser\GenericPhpParser;


/**
 * Class FileProcessor
 *
 * @link      http://websemantics.ca/ibuild
 * @link      http://ibuild.io
 * @author    WebSemantics, Inc. <info@websemantics.ca>
 * @author    Adnan Sagar <msagar@websemantics.ca>
 * @copyright 2012-2015 Web Semantics, Inc.
 * @package   Websemantics\EntityBuilderExtension
 */

trait FileProcessor
{

  /**
     * The file system utility.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The parser utility.
     *
     * @var Parser
     */
    protected $parser;

    /**
     * Setter for files
     *
     * @param  Filesystem $files, an instance of Filesystem class
     */
    protected function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * Setter for parser
     *
     * @param  Parser $parser, an instance of Parser class
     */
    protected function setParser($parser)
    {
        $this->parser = $parser;
    }

    /**
     * Get the file content
     *
     * @param  string $file, a php file template
     */
    public function get($file)
    {
        return $this->files->get($file);
    }

    /**
     * True if file exists
     *
     * @param  string $file, a php file template
     */
    public function exists($file)
    {
        $this->files->exists($file);
    }

    /**
     * Process a php target file to append PHP syntax-sensitive content
     * from multiple template sources.
     *
     * @param  string $file, a php file to modify
     * @param  array  $templates list of key (property name), value (template file)
     * @param  string $data used to replace placeholders inside all template files
     * @param Boolean $front, set location to the front of the array
     */
    protected function processFile($file, $templates, $data, $front = false)
    {
        $content = $this->files->get($file);

        $phpParser = new GenericPhpParser($content, $data, $this->parser);

        foreach ($templates as $property => $template) {
           $phpParser->parse($property, $template, $front);
        }

        $content = $phpParser->prettyPrint();
        if(!is_null($content)){
           $this->files->put($file, $content);
        }
    }
}

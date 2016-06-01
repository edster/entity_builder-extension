<?php namespace Websemantics\EntityBuilderExtension\Parser;

use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Anomaly\Streams\Platform\Addon\Module\Module;

/**
 * Class SeedersParser
 *
 * @link      http://websemantics.ca/ibuild
 * @link      http://ibuild.io
 * @author    WebSemantics, Inc. <info@websemantics.ca>
 * @author    Adnan Sagar <msagar@websemantics.ca>
 * @copyright 2012-2015 Web Semantics, Inc.
 * @package   Websemantics\EntityBuilderExtension
 */

class SeedersParser
{

    /**
     * Return the entity name.
     *
     * @param  StreamInterface $stream
     * @param  Module $module
     * @return string
     */
    public function parse(Module $module, StreamInterface $stream)
    {
        // First, check if the user has default content
        $destination = $module->getPath();
        $entityName  = strtolower(str_singular($stream->getSlug()));
        $file        = $destination."/resources/seeders/$entityName".".php";
        return file_exists($file) ? file_get_contents($file) : '';
    }
}

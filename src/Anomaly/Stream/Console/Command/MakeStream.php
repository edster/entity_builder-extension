<?php namespace Websemantics\EntityBuilderExtension\Anomaly\Stream\Console\Command;

use Websemantics\EntityBuilderExtension\Parser\EntityNameParser;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Filesystem\Filesystem;

/**
 * Class MakeStream.
 *
 * @link      http://websemantics.ca/ibuild
 * @link      http://ibuild.io
 * @author    WebSemantics, Inc. <info@websemantics.ca>
 * @author    Adnan Sagar <msagar@websemantics.ca>
 * @copyright 2012-2016 Web Semantics, Inc.
 * @package   Websemantics\EntityBuilderExtension\Anomaly\Stream\Console\Command
 */

class MakeStream implements SelfHandling
{
    /**
     * The entity name lower case
     *
     * @var string
     */
    private $entity;

    /**
     * The addon path.
     *
     * @var string
     */
    private $path;

    /**
     * Create a new MakeStream instance.
     *
     * @param         $slug, stream slug
     * @param         path
     */
    public function __construct($slug, $path)
    {
      $this->entity = strtolower((new EntityNameParser())->parse($slug));
      $this->path = $path;
    }

    /**
     * Handle the command, plant the seed
     *
     * @param Filesystem  $fm
     *
     * @return string
     */
    public function handle(Filesystem $fm)
    {
        /* create an empty seeder if it does not exist */
        $fm->put($this->path . '/resources/seeders/' . $this->entity . '.php', '', true);
    }
}

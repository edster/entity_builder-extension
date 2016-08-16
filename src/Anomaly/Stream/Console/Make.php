<?php namespace Websemantics\EntityBuilderExtension\Anomaly\Stream\Console;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Anomaly\Streams\Platform\Addon\Addon;
use Anomaly\Streams\Platform\Addon\AddonCollection;
use Websemantics\EntityBuilderExtension\Traits\JobsDispatcher;
use Websemantics\EntityBuilderExtension\Anomaly\Stream\Console\Command\MakeStream;

/**
 * Class Make.
 *
 * Overrides core Stream Make command to avoid conflic with Entity Builder.
 *
 * @link      http://websemantics.ca/ibuild
 * @link      http://ibuild.io
 * @author    WebSemantics, Inc. <info@websemantics.ca>
 * @author    Adnan Sagar <msagar@websemantics.ca>
 * @copyright 2012-2016 Web Semantics, Inc.
 */
class Make extends \Anomaly\Streams\Platform\Stream\Console\Make
{
    use DispatchesJobs, JobsDispatcher {
       JobsDispatcher::dispatch insteadof DispatchesJobs;
    }

    /**
     * Execute the console command.
     */
    public function fire(AddonCollection $addons)
    {
      parent::fire($addons);

      $slug  = $this->argument('slug');
      $addon = $addons->get($this->argument('addon'));
      $path = $addon->getPath();

      /* after a successful stream migration, create a seeder template file */
      $this->dispatch(new MakeStream($slug, $path));
    }
}

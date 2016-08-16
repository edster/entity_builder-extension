<?php namespace Websemantics\EntityBuilderExtension\Handler;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Anomaly\Streams\Platform\Addon\Module\Event\ModuleWasInstalled;
use Anomaly\Streams\Platform\Addon\Module\ModuleCollection;
use Websemantics\EntityBuilderExtension\Command\ModifyModule;
use Websemantics\EntityBuilderExtension\Command\SeedModule;

/**
 * Class ModuleWasInstalledHandler
 *
 * @link      http://websemantics.ca/ibuild
 * @link      http://ibuild.io
 * @author    WebSemantics, Inc. <info@websemantics.ca>
 * @author    Adnan Sagar <msagar@websemantics.ca>
 * @copyright 2012-2016 Web Semantics, Inc.
 * @package   Websemantics\EntityBuilderExtension
 */

class ModuleWasInstalledHandler {

  use DispatchesCommands;

	protected $modules;

	/**
	 * Create the event handler.
	 *
	 * @return void
	 */
	public function __construct(ModuleCollection $moduleCollection)
	{
		$this->modules = $moduleCollection->withConfig('builder');
	}

	/**
	 * Dispaches two jobs, 'ModifyModule' and 'SeedModule' *question of configuration
	 *
	 * @param  ModuleWasInstalled  $event
	 * @return void
	 */
	public function handle(ModuleWasInstalled $event)
	{
		$module = $event->getModule();

		if(count(ebxGetNamespaces($module)) > 0){

        $this->dispatch(new ModifyModule($module));

        if(ebxSeedingOption($module) === 'yes'){
    			$this->dispatch(new SeedModule($module));
    		}
		}

	}

}

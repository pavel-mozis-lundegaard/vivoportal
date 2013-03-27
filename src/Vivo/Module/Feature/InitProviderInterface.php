<?php
namespace Vivo\Module\Feature;

use Vivo\Module\ModuleManagerInterface;

/**
 * InitProviderInterface
 */
interface InitProviderInterface
{
    /**
     * Initialize workflow
     * @param  ModuleManagerInterface $manager
     * @return void
     */
    public function init(ModuleManagerInterface $manager);

}
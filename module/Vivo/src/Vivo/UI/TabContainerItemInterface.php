<?php
namespace Vivo\UI;

/**
 * TabContainerItemInterface
 *
 * @author peter.krajcar
 */
interface TabContainerItemInterface
{
	public function select();
	
	public function isDisabled();
    
    public function getLabel();
}

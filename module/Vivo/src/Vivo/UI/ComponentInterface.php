<?php
namespace Vivo\UI;

/**
 * Interface for UI components.
 */
interface ComponentInterface
{
    public function init();
    public function view();
    public function done();
    public function getName();
    public function getparent();
}

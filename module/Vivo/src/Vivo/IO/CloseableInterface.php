<?php
namespace Vivo\IO;

interface CloseableInterface
{
    public function close();
    public function isClosed();
}

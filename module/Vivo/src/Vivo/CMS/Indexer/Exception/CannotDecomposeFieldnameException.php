<?php
namespace Vivo\CMS\Indexer\Exception;

/**
 * CannotDecomposeFieldnameException
 * This exception is thrown when class name or property cannot be assessed from the indexer field name
 */
class CannotDecomposeFieldnameException extends \InvalidArgumentException implements ExceptionInterface
{
}

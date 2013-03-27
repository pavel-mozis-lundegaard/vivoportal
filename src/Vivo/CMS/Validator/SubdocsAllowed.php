<?php
namespace Vivo\CMS\Validator;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception\RuntimeException;

/**
 * ConfirmSubdocs
 * Expects 0|1 values, 1 is always valid, 0 is valid only when the document does not have any children
 * This validator is useful for checkboxes confirming action on subdocuments
 */
class SubdocsAllowed extends AbstractValidator
{
    const SUBDOCS_NOT_ALLOWED   = 'subdocsNotAllowed';

    /**
     * Message templates
     * @var array
     */
    protected $messageTemplates = array(
        self::SUBDOCS_NOT_ALLOWED   => 'Subdocuments are not allowed',
    );

    /**
     * Does
     * @var boolean
     */
    protected $hasSubdocs   = true;

    /**
     * Sets if there are subdocuments
     * @param bool $hasSubdocs
     */
    public function setHasSubdocs($hasSubdocs)
    {
        $this->hasSubdocs   = (bool) $hasSubdocs;
    }

    /**
     * Returns true if and only if $value meets the validation requirements
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     * @param  mixed $value
     * @return boolean
     * @throws RuntimeException If validation of $value is impossible
     */
    public function isValid($value)
    {
        $value  = (bool) $value;
        if ($value) {
            //Subdocs allowed
            $valid  = true;
        } else {
            //Subdocs not allowed
            if ($this->hasSubdocs) {
                $valid  = false;
                $this->error(self::SUBDOCS_NOT_ALLOWED);
            } else {
                $valid  = true;
            }
        }
        return $valid;
    }
}

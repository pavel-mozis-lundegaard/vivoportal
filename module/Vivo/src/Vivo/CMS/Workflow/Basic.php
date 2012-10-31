<?php
namespace Vivo\CMS\Workflow;

// use Vivo\CMS;

/**
 * Basic workflow implementation (default or the one and only if you like).
 * @author miroslav.hajek
 */
class Basic extends AbstractWorkflow {
	/**
	 * Associative array where keys are workflow states and values are all groups.
	 * @var array
	 */
	public $parameters;

	/**
	 * @param array Associative array where keys are workflow states and values are groups.
	 */
	function __construct($parameters) {
		$this->parameters = $parameters;
	}

	/**
	 * Returns all workflow states.
	 * @return array
	 */
	public function getAllStates() {
		return array_keys($this->parameters);
	}

	/**
	 * Returns all principals workflow states.
	 * @see Vivo\CMS\Security\Manager::isUserInGroup()
	 * @return array
	 */
	public function getAvailableStates() {
// 		$principal = CMS::$securityManager->getUserPrincipal();
// 		$available_states = array();
// 		foreach ($this->parameters as $state => $groups) {
// 			foreach ($groups as $group) {
// 				if (CMS::$securityManager->isUserInGroup($principal->domain, $principal->username, $group)) {
// 					$available_states[] = $state;
// 				}
// 			}
// 		}
// 		return $available_states;
	}

	/**
	 * Sets new workflow state only if principal is authorized for Content.ChangeState.
	 * @param Vivo\CMS\Model\Document $document
	 * @param Vivo\CMS\Model\Content $content
	 * @param string $state Workflow state.
	 * @param int $index Content index means position in the Multi Content Document.
	 * @throws Vivo\CMS\Exception 500, Workflow change state error.
	 * @return bool
	 */
// 	function setState($document, $content, $state, $index = false) {
// 		if($content->state == $state) {
// 			return false;
// 		}
// 		CMS::$securityManager->authorize($document, 'Content.ChangeState');
// 		if (in_array($state, $this->getAvailableStates())) {
// 			if ($state == self::STATE_PUBLISHED) {
// 				foreach ($document->getContents($index) as $another_content) {
// 					if (($another_content->path != $content->path) && ($another_content->state == self::STATE_PUBLISHED)) {
// 						$another_content->state = self::STATE_ARCHIVED;
// 						CMS::$repository->saveEntity($another_content);
// 						CMS::$event->invoke(CMS\Event::CHANGE_STATE, $another_content);
// 						CMS::$audit->log(
// 							CMS\Audit::OPERATION,
// 							$document,
// 							sprintf('Vivo\CMS\Audit\Content.ChangeState:%d:%d:%s:%s', ($index = $content->getIndex()) ? $index : 1, $another_content->getVersion(), self::STATE_PUBLISHED, self::STATE_ARCHIVED),
// 							200,
// 							array('entity'=>get_class($content), 'mimeType'=>$content->mime_type));
// 					}
// 				}
// 			}
// 			$oldState = $content->state;
// 			$content->state = $state;
// 			CMS::$repository->saveEntity($content);
// 			CMS::$event->invoke(CMS\Event::CHANGE_STATE, $content);
// 			CMS::$audit->log(
// 				CMS\Audit::OPERATION,
// 				$document,
// 				sprintf('Vivo\CMS\Audit\Content.ChangeState:%d:%d:%s:%s', ($index = $content->getIndex()) ? $index : 1, $content->getVersion(), $oldState, $state),
// 				200,
// 				array('entity'=>get_class($content), 'mimeType'=>$content->mime_type));
// 			return true;
// 		} else {
// 			throw new ChangeStateException();
// 		}
// 	}

	/**
	 * @param Vivo\CMS\Model\Document $document
	 * @param bool $throw_exception
	 * @param int $index Content index means position in the Multi Content Document.
	 * @throws Vivo\CMS\Exception 403, No published content.
	 * @return Vivo\CMS\Model\Content|null
	 */
// 	function getPublishedContent($document, $throw_exception = true, $index = false) {
// 		if ($contents = $document->getContents($index)) {
// 			foreach ($contents as $content) {
// 				if ($content->state == self::STATE_PUBLISHED)
// 					return $content;
// 			}
// 		}
// 		if ($throw_exception) {
// 			throw new CMS\NoPublishedContentException($document->path);
// 		} else {
// 			return null;
// 		}
// 	}

}


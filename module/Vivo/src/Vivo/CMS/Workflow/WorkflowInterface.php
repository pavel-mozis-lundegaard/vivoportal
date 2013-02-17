<?php
namespace Vivo\CMS\Workflow;

/**
 * WorkflowInterface
 * Workflow represents VIVO content workflow in time. Initial states are new for new document.
 * Documents with published content are visible to users. State archived is last state of the workflow.
 * Only one content version can be in state published, other versions are in state new or archived.
 */
interface WorkflowInterface {

	const STATE_NEW         = 'NEW';
	const STATE_PUBLISHED   = 'PUBLISHED';
	const STATE_ARCHIVED    = 'ARCHIVED';

	public function getAllStates();
}

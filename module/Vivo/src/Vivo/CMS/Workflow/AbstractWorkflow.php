<?php
namespace Vivo\CMS\Workflow;

/**
 * Class workflow represents VIVO content workflow in time. Initial states are new for new document.
 * Document with published content are visible for users. State archived is last state from workflow.
 * Only one content version can be in state published, other versions are in state new or archived.
 * @author miroslav.hajek
 */
class AbstractWorkflow {

	const STATE_NEW = 'NEW';
	const STATE_PUBLISHED = 'PUBLISHED';
	const STATE_ARCHIVED = 'ARCHIVED';

}

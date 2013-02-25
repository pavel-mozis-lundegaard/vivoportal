<?php
namespace Vivo\UI;

use Zend\Session;
use Zend\Stdlib\SplQueue;

/**
 * UI component for viewing user messages.
 *
 * Messages are saved to session until next request.
 */
class Alert extends Component
{
    /**
     * Message types.
     */
    const TYPE_INFO     = 'info';
    const TYPE_SUCCESS  = 'success';
    const TYPE_WARNING  = 'warning';
    const TYPE_ERROR    = 'error';

    /**
     * @var Container
     */
    protected $session;

    /**
     * @var boolean
     */
    protected $loaded = false;

    /**
     * Current messages to show.
     * @var array
     */
    protected $currentMessages = array();

    /**
     * Constructor.
     * @param Session\SessionManager $sessionManager
     */
    public function __construct(Session\SessionManager $sessionManager)
    {
        $this->session = new Session\Container(__CLASS__);
        $this->session->setExpirationHops(1, null, true);
        if (!$this->session->messages instanceof SplQueue) {
            $this->session->messages = new SplQueue();
        }
    }

    /**
     * Adds message to show.
     * @param string $message
     * @param string $type
     */
    public function addMessage($message, $type = self::TYPE_INFO)
    {
        $this->session->messages
                ->push(
                        array('message' => $message, 'type' => $type));
    }

    /**
     * Return messages to show in current request.
     * @return array
     */
    protected function getCurrentMessages()
    {
        return $this->currentMessages;
    }

    /**
     * Pull messages from session.
     */
    protected function loadMessages()
    {
        if (!$this->loaded) {
            $this->currentMessages = $this->session->messages->toArray();
            unset($this->session->messages);
            $this->loaded = true;
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\Component::view()
     */
    public function view()
    {
        $this->loadMessages();
        $this->getView()->messages = $this->getCurrentMessages();
        return parent::view();
    }
}

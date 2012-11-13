<?php
namespace Vivo\Controller;

use Zend\EventManager\EventInterface as Event;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

use Zend\EventManager\EventManagerInterface;

/**
 * The front controller which is responsible for dispatching all requests for documents and files in CMS repository.
 * @author kormik
 */
class CMSFrontController implements DispatchableInterface,
    InjectApplicationEventInterface, ServiceLocatorAwareInterface
{

    /**
     * @var \Zend\Mvc\MvcEvent
     */
    protected $event;

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    private $serviceLocator;

    /**
     * @var EventManagerInterface     */
    protected $events;

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function dispatch(Request $request, Response $response = null)
    {
        //TODO find document in repository and return it
        $path = $this->event->getRouteMatch()->getParam('path');
        $host = $this->event->getRouteMatch()->getParam('host');

//        $filename   = 'c:\Work\x\testfile.txt';
//        $fh         = fopen($filename, 'r+b');
//        echo '<br>' . fread($fh, 4);
//        fwrite($fh, 'BMW');
//        echo '<br>' . fread($fh, 4);
//        fwrite($fh, 'GUZZI');
//        echo '<br>' . fread($fh, 4) . '<br>';
//        fclose($fh);

//        $index  = \ZendSearch\Lucene\Lucene::create('c:\Work\LuceneTest');
//        $doc1   = new \ZendSearch\Lucene\Document();
//        $doc1->addField(\ZendSearch\Lucene\Document\Field::text('path', '/abc/def/ghi'));
//        $doc1->addField(\ZendSearch\Lucene\Document\Field::text('title', 'My first indexed document'));
//        $doc1->addField(\ZendSearch\Lucene\Document\Field::unStored('content', 'This is the content of the document'));
//        $index->addDocument($doc1);
//        $doc2   = new \ZendSearch\Lucene\Document();
//        $doc2->addField(\ZendSearch\Lucene\Document\Field::text('path', '/foo/bar/bat'));
//        $doc2->addField(\ZendSearch\Lucene\Document\Field::text('title', 'And now for something completely different'));
//        $doc2->addField(\ZendSearch\Lucene\Document\Field::unStored('content', 'Lorem ipsum dolor sit amet.'));
//        $index->addDocument($doc2);
//        die();

//        $pathBuilder        = new \Vivo\Storage\PathBuilder\PathBuilder('/');
//        $storageOpts        = array('root' => 'c:\Work\LuceneStorageTest', 'path_builder' => $pathBuilder);
//        $storage            = new \Vivo\Storage\LocalFileSystemStorage($storageOpts);
//        $luceneDirectory    = new \Vivo\ZendSearch\Lucene\Storage\Directory\VivoStorage($storage, '/luceneStorage');
        //$luceneDirectory    = 'c:\Work\LuceneTest';


        $pathBuilder        = new \Vivo\Storage\PathBuilder\PathBuilder('/');
        $storageOpts        = array('root' => 'c:\Work\LuceneStorageTest', 'path_builder' => $pathBuilder);
        $storage            = new \Vivo\Storage\LocalFileSystemStorage($storageOpts);
//        \Vivo\Storage\StreamWrapper::register('lucene', $storage);
//        $luceneDirectory    = 'lucene:///abc';
//        $luceneDirectory    = 'c:\Work\LuceneTest';
        $luceneDirectory    = new \Vivo\ZendSearch\Lucene\Storage\Directory\VivoStorage($storage, '/abc');
//        $index  = \ZendSearch\Lucene\Lucene::create($luceneDirectory);
        $index  = \ZendSearch\Lucene\Lucene::open($luceneDirectory);
//        $doc1   = new \ZendSearch\Lucene\Document();
//        $doc1->addField(\ZendSearch\Lucene\Document\Field::text('uuid', '0123456789AB'));
//        $doc1->addField(\ZendSearch\Lucene\Document\Field::text('path', '/abc/def/ghi'));
//        $doc1->addField(\ZendSearch\Lucene\Document\Field::text('title', 'My first indexed document alpha'));
//        $doc1->addField(\ZendSearch\Lucene\Document\Field::unStored('content', 'This is the alpha content of the document'));
//        $index->addDocument($doc1);
//        $doc2   = new \ZendSearch\Lucene\Document();
//        $doc2->addField(\ZendSearch\Lucene\Document\Field::text('uuid', 'ABCDEF01234586'));
//        $doc2->addField(\ZendSearch\Lucene\Document\Field::text('path', '/foo/bar/bat'));
//        $doc2->addField(\ZendSearch\Lucene\Document\Field::text('title', 'And now for something completely different'));
//        $doc2->addField(\ZendSearch\Lucene\Document\Field::unStored('content', 'Lorem ipsum alpha dolor sit amet.'));
//        $index->addDocument($doc2);
//        for ($i = 0; $i < 100; $i++) {
//            $doc    = new \ZendSearch\Lucene\Document();
//            $doc->addField(\ZendSearch\Lucene\Document\Field::text('my_id', $i));
//            $doc->addField(\ZendSearch\Lucene\Document\Field::text('hash', md5(time())));
//            $index->addDocument($doc);
//        }

        $hits    = $index->find('alpha');
//        foreach ($hits as $hit) {
//            /* @var $hit \ZendSearch\Lucene\Search\QueryHit */
//            echo '<br>id: ' .  $hit->id;
//            echo '<br>score: ' .  $hit->score;
//            echo '<br>uuid: ' .  $hit->uuid;
//            echo '<br>';
//        }
//        \Zend\Debug\Debug::dump($hits);
//        $index->optimize();
        die('END');



        $response->setContent('CMS document for path: ' . $path);
        $response->setStatusCode(HttpResponse::STATUS_CODE_200);
        return $response;
    }

    /**
     * @param Event $event
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @return \Zend\Mvc\MvcEvent
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set the event manager instance
     * @param  EventManagerInterface $events
     * @return CMSFrontController
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events
            ->setIdentifiers(
                array(__CLASS__, get_called_class(), 'cms_front_controller',));
        $this->events = $events;
        $this->attachDefaultListeners();
        return $this;
    }

    /**
     * Retrieve the event manager
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->events;
    }

    /**
     * Register the default event listeners
     * @return CMSFrontController
     */
    protected function attachDefaultListeners()
    {
        $events = $this->getEventManager();
        $events
            ->attach(ModuleEvent::EVENT_LOAD_MODULES,
                array($this, 'onLoadModules'));
    }

}

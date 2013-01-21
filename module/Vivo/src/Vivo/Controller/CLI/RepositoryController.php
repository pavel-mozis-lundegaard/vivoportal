<?php
namespace Vivo\Controller\CLI;

use Vivo\CMS\Api\Repository as RepositoryApi;
use Vivo\SiteManager\Event\SiteEvent;
use Vivo\Repository\Repository;

/**
 * Vivo CLI controller for command 'repository'
 */
class RepositoryController extends AbstractCliController
{
    const COMMAND = 'repository';

    /**
     * Repository API
     * @var RepositoryApi;
     */
    protected $repositoryApi;

    /**
     * Repository
     * @var Repository
     */
    protected $repository;

    /**
     * SiteEvent
     * @var SiteEvent
     */
    protected $siteEvent;

    /**
     * Constructor
     * @param \Vivo\CMS\Api\Repository $repositoryApi
     * @param SiteEvent $siteEvent
     * @param \Vivo\Repository\Repository $repository
     */
    public function __construct(RepositoryApi $repositoryApi, SiteEvent $siteEvent, Repository $repository)
    {
        $this->repositoryApi    = $repositoryApi;
        $this->siteEvent        = $siteEvent;
        $this->repository       = $repository;
    }

    /**
     * Returns list of duplicate UUIDs
     * @return string
     */
    public function getDuplicateUuidsAction()
    {
        //Prepare params
        $request    = $this->getRequest();
        /* @var $request \Zend\Console\Request */
        $host   = $request->getParam('host');
        if (!$this->siteEvent->getSite()) {
            $output = sprintf("No site object created; host = '%s'", $host);
            return $output;
        }
        $site   = $this->siteEvent->getSite();
        $path   = $site->getPath();
        $duplicateUuids = $this->repositoryApi->getDuplicateUuids($path);
        $numOfDuplicates    = count($duplicateUuids);
        if ($numOfDuplicates == 0) {
            $output = "\nThere are no duplicate uuids";
        } else {
            $output = sprintf("\n%s UUIDs are duplicated (not unique) in repository:", $numOfDuplicates);
            foreach ($duplicateUuids as $uuid => $paths) {
                $output .= sprintf("\n\n%s", $uuid);
                foreach ($paths as $path) {
                    $output .= sprintf("\n    %s", $path);
                }
            }
        }
        return $output;
    }

    public function makeUuidsUniqueAction()
    {
        //Prepare params
        $request    = $this->getRequest();
        /* @var $request \Zend\Console\Request */
        $host   = $request->getParam('host');
        if (!$this->siteEvent->getSite()) {
            $output = sprintf("No site object created; host = '%s'", $host);
            return $output;
        }
        $site   = $this->siteEvent->getSite();
        $path   = $site->getPath();
        $duplicateUuids = $this->repositoryApi->getDuplicateUuids($path);
        $numOfDuplicates    = count($duplicateUuids);
        if ($numOfDuplicates == 0) {
            $output = "\nThere are no duplicate uuids";
        } else {
            $output = sprintf("\n%s UUIDs are duplicated (not unique) in repository", $numOfDuplicates);
            foreach ($duplicateUuids as $uuid => $paths) {
                $output .= sprintf("\n\n%s", $uuid);
                array_shift($paths);
                foreach ($paths as $path) {
                    $entity = $this->repository->getEntity($path);
                    $newUuid    = md5($path . mt_rand() . date('Y-m-d H:i:s'));
                    $entity->setUuid($newUuid);
                    $this->repository->saveEntity($entity);
                    $output .= sprintf("\n    %s -> %s", $path, $newUuid);
                }
            }
            $this->repository->commit();
            $output .= "\n\nDo not forget to reindex!";
        }
        return $output;

    }

    public function helpAction()
    {
        return $this->getConsoleUsage();
    }

    public function getConsoleUsage()
    {
        $output = "\nRepository usage:";
        $output .= "\n\nrepository duplicateuuids <host>";
        return $output;
    }
}

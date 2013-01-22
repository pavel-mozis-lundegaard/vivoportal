<?php
namespace Vivo\Controller\CLI;

use Vivo\CMS\Api\Repository as RepositoryApi;
use Vivo\SiteManager\Event\SiteEvent;
use Vivo\Repository\Repository;
use Vivo\CMS\Api\CMS;
use Vivo\Uuid\Generator as UuidGenerator;

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
     * CMS Api
     * @var CMS
     */
    protected $cms;

    /**
     * SiteEvent
     * @var SiteEvent
     */
    protected $siteEvent;

    /**
     * UUID Generator
     * @var UuidGenerator
     */
    protected $uuidGenerator;

    /**
     * Constructor
     * @param \Vivo\CMS\Api\Repository $repositoryApi
     * @param SiteEvent $siteEvent
     * @param \Vivo\Repository\Repository $repository
     * @param \Vivo\CMS\Api\CMS $cms
     * @param \Vivo\Uuid\Generator $uuidGenerator
     */
    public function __construct(RepositoryApi $repositoryApi, SiteEvent $siteEvent, Repository $repository, CMS $cms,
                                UuidGenerator $uuidGenerator)
    {
        $this->repositoryApi    = $repositoryApi;
        $this->siteEvent        = $siteEvent;
        $this->repository       = $repository;
        $this->cms              = $cms;
        $this->uuidGenerator    = $uuidGenerator;
    }

    /**
     * Returns list of duplicate UUIDs
     * @return string
     */
    public function duplicateUuidsAction()
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

    /**
     * Replaces duplicate uuids with newly generated ones
     * For development purposes only!
     * @return string
     */
    public function uniqueUuidsAction()
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
                foreach ($paths as $pathOfDup) {
                    $entity = $this->repository->getEntity($pathOfDup);
                    $newUuid    = $this->uuidGenerator->create();
                    $entity->setUuid($newUuid);
                    $this->repository->saveEntity($entity);
                    $output .= sprintf("\n    %s -> %s", $pathOfDup, $newUuid);
                }
            }
            $this->repository->commit();
            //Reindex
            $reindexedNum   = $this->cms->reindex($path, true);
            $output .= sprintf("\n\nReindexed %s", $reindexedNum);
        }
        return $output;

    }

    public function getConsoleUsage()
    {
        $output = "\nRepository usage:";
        $output .= "\n\nrepository duplicate-uuids <host>";
        $output .= "\nrepository unique-uuids <host>";
        return $output;
    }
}

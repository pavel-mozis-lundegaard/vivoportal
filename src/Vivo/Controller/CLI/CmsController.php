<?php
namespace Vivo\Controller\CLI;

use Vivo\CMS\Api\CMS;
use Vivo\SiteManager\Event\SiteEvent;
use Vivo\Repository\RepositoryInterface;
use Vivo\Uuid\GeneratorInterface as UuidGeneratorInterface;
use Vivo\CMS\Api\IndexerInterface as IndexerApiInterface;

/**
 * CmsController
 * Vivo CLI controller for command 'cms'
 */
class CmsController extends AbstractCliController
{
    const COMMAND = 'cms';

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
     * Repository
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * UUID Generator
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * Indexer API
     * @var IndexerApiInterface
     */
    protected $indexerApi;

    /**
     * Constructor
     * @param \Vivo\CMS\Api\CMS $cms
     * @param \Vivo\SiteManager\Event\SiteEvent $siteEvent
     * @param \Vivo\Repository\RepositoryInterface $repository
     * @param \Vivo\Uuid\GeneratorInterface $uuidGenerator
     * @param \Vivo\CMS\Api\IndexerInterface $indexerApi
     */
    public function __construct(CMS $cms,
                                SiteEvent $siteEvent,
                                RepositoryInterface $repository,
                                UuidGeneratorInterface $uuidGenerator,
                                IndexerApiInterface $indexerApi)
    {
        $this->cms              = $cms;
        $this->siteEvent        = $siteEvent;
        $this->repository       = $repository;
        $this->uuidGenerator    = $uuidGenerator;
        $this->indexerApi       = $indexerApi;
    }

    public function getConsoleUsage()
    {
        $output = "\nCMS usage:";
        $output .= "\ncms duplicateuuids <host>";
        $output .= "\ncms uniqueuuids <host> [--force|-f]";

        return $output;
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
        $duplicateUuids = $this->cms->getDuplicateUuidsInStorage($path);
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
        // Check force flag
        $force  = $request->getParam('force') || $request->getParam('f');

        $site   = $this->siteEvent->getSite();
        $path   = $site->getPath();

        if ($force) {
            //Replace all UUIDs
            $entities   = $this->repository->getDescendantsFromStorage($path);
            $me         = $this->repository->getEntityFromStorage($path);
            if ($me) {
                $entities[] = $me;
            }
            $output     = "\nReplacing all UUIDs...";
            $count      = 0;
            foreach ($entities as $entity) {
                $newUuid    = $this->uuidGenerator->create();
                $entity->setUuid($newUuid);
                $this->cms->saveEntity($entity, false);
                $count++;
            }
            $this->repository->commit();
            $output     .= sprintf("\nCommitted %s updated entities into repository", $count);
        } else {
            //Replace only duplicate UUIDs
            $duplicateUuids     = $this->cms->getDuplicateUuidsInStorage($path);
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
                        $this->cms->saveEntity($entity, false);
                        $output .= sprintf("\n    %s -> %s", $pathOfDup, $newUuid);
                    }
                }
                $this->repository->commit();
            }
        }
        //Reindex
        $reindexedNum   = $this->indexerApi->reindex($site, '/', true);
        $output         .= sprintf("\n\nReindexed %s", $reindexedNum);
        return $output;
    }
}

<?php
namespace Vivo\Controller\CLI;

use Vivo\CMS\Api\CMS;
use Vivo\CMS\Api\Site as SiteApi;
use Vivo\SiteManager\Event\SiteEvent;
use Vivo\Repository\RepositoryInterface;
use Vivo\Uuid\GeneratorInterface as UuidGeneratorInterface;
use Vivo\CMS\Api\IndexerInterface as IndexerApiInterface;
use Vivo\CMS\Api\Exception\SiteAlreadyExistsException;

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
    protected $cmsApi;

    /**
     * Site API
     * @var SiteApi
     */
    protected $siteApi;

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
     * @param \Vivo\CMS\Api\CMS $cmsApi
     * @param \Vivo\CMS\Api\Site $siteApi
     * @param \Vivo\SiteManager\Event\SiteEvent $siteEvent
     * @param \Vivo\Repository\RepositoryInterface $repository
     * @param \Vivo\Uuid\GeneratorInterface $uuidGenerator
     * @param \Vivo\CMS\Api\IndexerInterface $indexerApi
     */
    public function __construct(CMS $cmsApi,
                                SiteApi $siteApi,
                                SiteEvent $siteEvent,
                                RepositoryInterface $repository,
                                UuidGeneratorInterface $uuidGenerator,
                                IndexerApiInterface $indexerApi)
    {
        $this->cmsApi           = $cmsApi;
        $this->siteApi          = $siteApi;
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
        $output .= "\ncms createsite <name> <secdomain> <hosts> [<site_title>]";
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
        $duplicateUuids = $this->cmsApi->getDuplicateUuidsInStorage($path);
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
                $this->cmsApi->saveEntity($entity, false);
                $count++;
            }
            $this->repository->commit();
            $output     .= sprintf("\nCommitted %s updated entities into repository", $count);
        } else {
            //Replace only duplicate UUIDs
            $duplicateUuids     = $this->cmsApi->getDuplicateUuidsInStorage($path);
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
                        $this->cmsApi->saveEntity($entity, false);
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

    /**
     * Creates site
     * @return string
     */
    public function createSiteAction()
    {
        $output     = 'Create site';
        //Prepare params
        $request    = $this->getRequest();
        /* @var $request \Zend\Console\Request */
        $name       = $request->getParam('name');
        $output     .= "\nName: " . $name;
        $secDomain  = $request->getParam('secdomain');
        $output     .= "\nSecurity domain: " . $secDomain;
        $hosts      = $request->getParam('hosts');
        $hosts      = explode(',', $hosts);
        foreach ($hosts as $key => $host) {
            $hosts[$key] = trim($host);
        }
        $output     .= "\nHosts: " . implode(', ', $hosts);
        if ($request->getParam('title')) {
            $title  = $request->getParam('title');
        } else {
            $title  = null;
        }
        try {
            $this->siteApi->createSite($name, $secDomain, $hosts, $title);
            $output     .= "\nSITE CREATED";
        } catch (SiteAlreadyExistsException $e) {
            $output .= "\nERROR: " . $e->getMessage();
        }
        return $output;;
    }
}

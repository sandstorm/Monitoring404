<?php
namespace Sandstorm\Monitoring404\Domain\Service;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Request;
use Neos\Flow\Persistence\Doctrine\PersistenceManager;
use Sandstorm\Monitoring404\Domain\Model\RequestWhichTriggered404;
use Sandstorm\Monitoring404\Domain\Repository\RequestWhichTriggered404Repository;

class Monitoring404Service
{

    /**
     * @Flow\InjectConfiguration(path="enabled")
     * @var string
     */
    protected $monitoringEnabled;

    /**
     * @Flow\InjectConfiguration(path="maxNewRecords")
     * @var string
     */
    protected $maxNewRecords;

    /**
     * @Flow\Inject
     * @var RequestWhichTriggered404Repository
     */
    protected $requestWhichTriggered404Repository;

    /**
     * @Flow\Inject
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @param Request $request
     */
    public function logRequest(Request $request)
    {
        if (!$this->monitoringEnabled) {
            return;
        }

        $requestWhichTriggered404 = $this->requestWhichTriggered404Repository->findOneByRequest($request);

        if ($requestWhichTriggered404 !== null) {
            $requestWhichTriggered404->incrementHitCounter();
            $this->requestWhichTriggered404Repository->update($requestWhichTriggered404);
        } else {
            if ($this->requestWhichTriggered404Repository->countNew() >= $this->maxNewRecords) {
                // We don't accept new entries to not spam the DB
                return;
            }
            $requestWhichTriggered404 = RequestWhichTriggered404::fromRequest($request);
            $this->requestWhichTriggered404Repository->add($requestWhichTriggered404);
        }

        // In a GET, we need to manually whitelist and persist
        if ($request->getMethod() === 'GET') {
		    $this->persistenceManager->whitelistObject($requestWhichTriggered404);
		    $this->persistenceManager->persistAll();
        }
    }
}

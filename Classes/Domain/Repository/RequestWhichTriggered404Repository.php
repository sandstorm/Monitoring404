<?php
namespace Sandstorm\Monitoring404\Domain\Repository;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Request;
use Neos\Flow\Persistence\Doctrine\Repository;
use Neos\Flow\Persistence\QueryInterface;
use Sandstorm\Monitoring404\Domain\Model\RequestWhichTriggered404;

/**
 * Repository for uris that have triggered 404s.
 *
 * @Flow\Scope("singleton")
 */
class RequestWhichTriggered404Repository extends Repository
{
    /**
     * @var array
     */
    protected $defaultOrderings = [
        'numberOfHits' => QueryInterface::ORDER_DESCENDING
    ];

    public function findOneByRequest(Request $request)
    {
        return $this->findOneBy([
            'method' => $request->getMethod(),
            'scheme' => $request->getUri()->getScheme(),
            'host' => $request->getUri()->getHost(),
            'port' => $request->getUri()->getPort(),
            'path' => $request->getUri()->getPath(),
            'query' => $request->getUri()->getQuery()
        ]);
    }

    /**
     * Counts all new records.
     * @return int
     */
    public function countNew()
    {
        $qb = $this->createQueryBuilder('r');
        $qb->select('COUNT(r)');
        $qb->where('r.status = \'' . RequestWhichTriggered404::STATUS_NEW . '\'');
        return (int) $qb->getQuery()->execute()[0][1];
    }
}

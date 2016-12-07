<?php
namespace Sandstorm\Monitoring404\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Request;
use Neos\Flow\Utility\Now;

/**
 * A request for which no route could be found and therefore has triggered a 404.
 *
 * @Flow\Entity
 * @ORM\Table(
 *    indexes={
 * 		@ORM\Index(name="requeststatus",columns={"status"}),
 *    }
 * )
 */
class RequestWhichTriggered404
{

    /**
     * @var string
     */
    protected $method;

    /**
     * The scheme / protocol of the locator, eg. http
     * @var string
     */
    protected $scheme;

    /**
     * Host of the locator, eg. some.subdomain.example.com
     * @var string
     */
    protected $host;

    /**
     * Port of the locator, if any was specified. Eg. 80
     * @var integer
     * @ORM\Column(nullable=TRUE)
     */
    protected $port;

    /**
     * The hierarchical part of the URI, eg. /products/acme_soap
     * @var string
     */
    protected $path;

    /**
     * Query string of the locator, if any. Eg. color=red&size=large
     * @var string
     * @ORM\Column(nullable=TRUE)
     */
    protected $query;

    /**
     * @var \DateTime
     */
    protected $firstHitDate;

    /**
     * @var \DateTime
     */
    protected $lastHitDate;

    /**
     * @var string
     */
    protected $status;
    const STATUS_NEW = 'new';
    const STATUS_IGNORED = 'ignored';

    /**
     * @var int
     */
    protected $numberOfHits;

    public function __construct($method, $scheme, $host, $port, $path, $query) {
        $this->method = $method;
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
        $this->path = $path;
        $this->query = $query;
        $this->numberOfHits = 1;
        $this->firstHitDate = new Now();
        $this->lastHitDate = new Now();
        $this->status = self::STATUS_NEW;
    }

    /**
     * @param Request $request
     * @return self
     */
    public static function fromRequest(Request $request)
    {
        return new self(
            $request->getMethod(),
            $request->getUri()->getScheme(),
            $request->getUri()->getHost(),
            $request->getUri()->getPort(),
            $request->getUri()->getPath(),
            $request->getUri()->getQuery()
        );
    }

    public function incrementHitCounter()
    {
        $this->numberOfHits++;
        $this->lastHitDate = new Now();
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return \DateTime
     */
    public function getLastHitDate()
    {
        return $this->lastHitDate;
    }

    /**
     * @return int
     */
    public function getNumberOfHits()
    {
        return $this->numberOfHits;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @param string $scheme
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @param \DateTime $lastHitDate
     */
    public function setLastHitDate($lastHitDate)
    {
        $this->lastHitDate = $lastHitDate;
    }

    /**
     * @param int $numberOfHits
     */
    public function setNumberOfHits($numberOfHits)
    {
        $this->numberOfHits = $numberOfHits;
    }
}

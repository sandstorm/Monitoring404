<?php
namespace Sandstorm\Monitoring404\Http\Component;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Component\ComponentContext;
use Neos\Flow\Http\Component\ComponentInterface;
use Neos\Flow\Mvc\Routing\RoutingComponent;
use Sandstorm\Monitoring404\Domain\Service\Monitoring404Service;

/**
 * Monitoring 404 Component
 */
class Monitoring404Component implements ComponentInterface
{
    /**
     * @Flow\Inject
     * @var Monitoring404Service
     */
    protected $monitoring404Service;

    /**
     * Check if the current response is a 404 and save it if applicable.
     *
     * @param ComponentContext $componentContext
     * @return void
     */
    public function handle(ComponentContext $componentContext)
    {
        // Ask the router if he has something for this request
        $routingMatchResults = $componentContext->getParameter(RoutingComponent::class, 'matchResults');
        if ($routingMatchResults !== null) {
            return;
        }

//        try {
            $this->monitoring404Service->logRequest($componentContext->getHttpRequest());
//        } catch (\Exception $e) {
//             Swallow the exception - we want to show the 404 even if something went wrong here
//        }
    }
}

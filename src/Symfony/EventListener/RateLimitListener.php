<?php

namespace ApiPlatform\Symfony\EventListener;

use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Symfony\RateLimit\ApiRateLimitFactory;
use ApiPlatform\Symfony\Security\ResourceAccessCheckerInterface;
use ApiPlatform\Util\OperationRequestInitiatorTrait;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class RateLimitListener
{
    use OperationRequestInitiatorTrait;

    public function __construct(
        private readonly ApiRateLimitFactory $apiRateLimitFactory,
        ?ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory = null,
    )
    {
        $this->resourceMetadataCollectionFactory = $resourceMetadataCollectionFactory;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $operation = $this->initializeOperation($event->getRequest());
        if (!$operation || !$operation->getRateLimiterConfig()) {
            return;
        }

        $limiter = $this->apiRateLimitFactory->create($event->getRequest(), $operation);

        if (false === $limiter->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }
    }
}

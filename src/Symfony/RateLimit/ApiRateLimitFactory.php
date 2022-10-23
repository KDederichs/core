<?php

namespace ApiPlatform\Symfony\RateLimit;

use ApiPlatform\Metadata\HttpOperation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\NoLock;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\Policy\NoLimiter;
use Symfony\Component\RateLimiter\Storage\StorageInterface;

class ApiRateLimitFactory
{
    public function __construct(
        private readonly StorageInterface $storage,
        private readonly ?LockFactory $lockFactory = null
    ) {}

    public function create(Request $request, HttpOperation $operation): LimiterInterface
    {
        if (!$operation->getRateLimiterConfig()) {
            return new NoLimiter();
        }

        $limiterConfig = $operation->getRateLimiterConfig();

        $limiterClass = $limiterConfig['class'] ?? null;

        if (!is_subclass_of($limiterClass, LimiterInterface::class)) {
            throw new \Exception(sprintf('Class %s does not implement %s', $limiterClass, LimiterInterface::class));
        }

        $id = $operation->getName().'-'.$request->getClientIp();

        if (isset($limiterConfig['keyClosure'])) {
            $id = $operation->getName().$limiterConfig['keyClosure']($request);
        }
        $lock = $this->lockFactory ? $this->lockFactory->createLock($id) : new NoLock();
        new $limiterClass($id, $limiterConfig['limit'], $limiterConfig['rate'], $this->storage, $lock);
    }
}

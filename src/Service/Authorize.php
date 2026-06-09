<?php

declare(strict_types=1);

namespace BjyAuthorize\Service;

use BjyAuthorize\Guard\GuardInterface;
use BjyAuthorize\Provider\Identity\ProviderInterface as IdentityProvider;
use BjyAuthorize\Provider\Resource\ProviderInterface as ResourceProvider;
use BjyAuthorize\Provider\Role\ProviderInterface as RoleProvider;
use BjyAuthorize\Provider\Role\ProviderInterface as RoleProviderInterface;
use BjyAuthorize\Provider\Rule\ProviderInterface as RuleProvider;
use Closure;
use Interop\Container\ContainerInterface;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Exception\InvalidArgumentException;
use Laminas\Permissions\Acl\Resource\GenericResource;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Traversable;

use function count;
use function is_array;
use function is_int;
use function is_string;
use function print_r;

/**
 * Authorize service
 */
class Authorize
{
    public const TYPE_ALLOW = 'allow';

    public const TYPE_DENY = 'deny';

    /** @var Acl */
    protected $acl;

    /** @var RoleProvider[] */
    protected $roleProviders = [];

    /** @var ResourceProvider[] */
    protected $resourceProviders = [];

    /** @var RuleProvider[] */
    protected $ruleProviders = [];

    /** @var IdentityProvider */
    protected $identityProvider;

    /** @var GuardInterface[] */
    protected $guards = [];

    /** @var Closure|null */
    protected $loaded;

    /** @var ContainerInterface */
    protected $serviceLocator;

    /** @var array */
    protected $config;

    /**
     * @param array $config
     */
    public function __construct(array $config, ContainerInterface $serviceLocator)
    {
        $this->config         = $config;
        $this->serviceLocator = $serviceLocator;
        $that                 = $this;
        $this->loaded         = function () use ($that) {
            $that->load();
        };
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
     * @return self
     */
    public function addRoleProvider(RoleProvider $provider)
    {
        $this->loaded && $this->loaded->__invoke();

        $this->roleProviders[] = $provider;

        return $this;
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
     * @return self
     */
    public function addResourceProvider(ResourceProvider $provider)
    {
        $this->loaded && $this->loaded->__invoke();

        $this->resourceProviders[] = $provider;

        return $this;
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
     * @return self
     */
    public function addRuleProvider(RuleProvider $provider)
    {
        $this->loaded && $this->loaded->__invoke();

        $this->ruleProviders[] = $provider;

        return $this;
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
     * @return self
     */
    public function setIdentityProvider(IdentityProvider $provider)
    {
        $this->loaded && $this->loaded->__invoke();

        $this->identityProvider = $provider;

        return $this;
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
     * @return IdentityProvider
     */
    public function getIdentityProvider()
    {
        $this->loaded && $this->loaded->__invoke();

        return $this->identityProvider;
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
     * @return self
     */
    public function addGuard(GuardInterface $guard)
    {
        $this->loaded && $this->loaded->__invoke();

        $this->guards[] = $guard;

        if ($guard instanceof ResourceProvider) {
            $this->addResourceProvider($guard);
        }

        if ($guard instanceof RuleProvider) {
            $this->addRuleProvider($guard);
        }

        return $this;
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 1.4.x+,
     *             please retrieve the guards from the `BjyAuthorize\Guards` service
     *
     * @return GuardInterface[]
     */
    public function getGuards()
    {
        $this->loaded && $this->loaded->__invoke();

        return $this->guards;
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 1.4.x+,
     *             please retrieve the identity from the
     *             `BjyAuthorize\Provider\Identity\ProviderInterface` service
     *
     * @return string
     */
    public function getIdentity()
    {
        $this->loaded && $this->loaded->__invoke();

        return 'bjyauthorize-identity';
    }

    /**
     * @return Acl
     */
    public function getAcl()
    {
        $this->loaded && $this->loaded->__invoke();

        return $this->acl;
    }

    /**
     * @param string|ResourceInterface $resource
     * @param null $privilege
     * @param RoleProviderInterface|null $roleProvider
     * @return bool
     */
    public function isAllowed($resource, $privilege = null, ?RoleProviderInterface $roleProvider = null)
    {
        $this->loaded && $this->loaded->__invoke();

        $roleList = [$this->getIdentity()];

        if ($roleProvider !== null) {
            $roleList = $roleProvider->getRoles();
        }

        $result = false;

        foreach ($roleList as $role) {
            try {
                $isAllowed = $this->acl->isAllowed($role, $resource, $privilege);
                if ($isAllowed) {
                    $result = true;
                    break;
                }
            } catch (InvalidArgumentException $e) {
                break;
            }
        }

        return $result;
    }

    /**
     * Initializes the service
     *
     * @internal
     *
     * @return void
     */
    public function load()
    {
        if (null === $this->loaded) {
            return;
        }

        $this->loaded = null;

        /** @var StorageInterface $cache */
        $cache = $this->serviceLocator->get('BjyAuthorize\Cache');

        /** @var callable $cacheKeyGenerator */
        $cacheKeyGenerator = $this->serviceLocator->get('BjyAuthorize\CacheKeyGenerator');
        $cacheKey          = $cacheKeyGenerator();

        $success      = false;
        $cacheEnabled = $this->config['cache_enabled'] ?? false;
        if ($cacheEnabled) {
            $this->acl = $cache->getItem($cacheKey, $success);
        }

        if (! $this->acl instanceof Acl || ! $success) {
            $this->loadAcl();
            if ($cacheEnabled) {
                $cache->setItem($cacheKey, $this->acl);
            }
        }

        $this->setIdentityProvider($this->serviceLocator->get(IdentityProvider::class));

        $parentRoles = $this->getIdentityProvider()->getIdentityRoles();

        $this->acl->addRole($this->getIdentity(), $parentRoles);
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
     * @param RoleInterface[] $roles
     */
    protected function addRoles($roles)
    {
        if (! is_array($roles) && ! $roles instanceof Traversable) {
            $roles = [$roles];
        }

        /** @var Role $role */
        foreach ($roles as $role) {
            if ($this->acl->hasRole($role)) {
                continue;
            }

            if ($role->getParent() !== null) {
                $this->addRoles([$role->getParent()]);
                $this->acl->addRole($role, $role->getParent());
            } elseif (! $this->acl->hasRole($role)) {
                $this->acl->addRole($role);
            }
        }
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
     * @param string[]|ResourceInterface[] $resources
     * @param mixed|null $parent
     */
    protected function loadResource($resources, $parent = null)
    {
        if (! is_array($resources) && ! $resources instanceof Traversable) {
            throw new \InvalidArgumentException('Resources argument must be traversable: ' . print_r($resources, true));
        }

        foreach ($resources as $key => $value) {
            if ($value instanceof ResourceInterface) {
                $key = $value;
            } elseif (is_string($key)) {
                $key = new GenericResource($key);
            } elseif (is_int($key)) {
                $key = new GenericResource($value);
            }

            if (is_array($value) || $value instanceof Traversable) {
                $this->acl->addResource($key, $parent);
                $this->loadResource($value, $key);
            } elseif (! $this->acl->hasResource($key)) {
                $this->acl->addResource($key, $parent);
            }
        }
    }

    /**
     * @deprecated this method will be removed in BjyAuthorize 2.0.x
     *
     * @param mixed $rule
     * @param mixed $type
     *
     * @throws \InvalidArgumentException
     */
    protected function loadRule(array $rule, $type)
    {
        $privileges = $assertion = null;
        $ruleSize   = count($rule);

        if (4 === $ruleSize) {
            [$roles, $resources, $privileges, $assertion] = $rule;
            $assertion                                    = $this->serviceLocator->get($assertion);
        } elseif (3 === $ruleSize) {
            [$roles, $resources, $privileges] = $rule;
        } elseif (2 === $ruleSize) {
            [$roles, $resources] = $rule;
        } else {
            throw new \InvalidArgumentException('Invalid rule definition: ' . print_r($rule, true));
        }

        if (is_string($assertion)) {
            $assertion = $this->serviceLocator->get($assertion);
        }

        if (static::TYPE_ALLOW === $type) {
            $this->acl->allow($roles, $resources, $privileges, $assertion);
        } else {
            $this->acl->deny($roles, $resources, $privileges, $assertion);
        }
    }

    /**
     * Initialize the Acl
     */
    private function loadAcl()
    {
        $this->acl = new Acl();

        foreach ($this->serviceLocator->get('BjyAuthorize\RoleProviders') as $provider) {
            $this->addRoleProvider($provider);
        }

        foreach ($this->serviceLocator->get('BjyAuthorize\ResourceProviders') as $provider) {
            $this->addResourceProvider($provider);
        }

        foreach ($this->serviceLocator->get('BjyAuthorize\RuleProviders') as $provider) {
            $this->addRuleProvider($provider);
        }

        foreach ($this->serviceLocator->get('BjyAuthorize\Guards') as $guard) {
            $this->addGuard($guard);
        }

        foreach ($this->roleProviders as $provider) {
            $this->addRoles($provider->getRoles());
        }

        foreach ($this->resourceProviders as $provider) {
            $this->loadResource($provider->getResources(), null);
        }

        foreach ($this->ruleProviders as $provider) {
            $rules = $provider->getRules();
            if (isset($rules['allow'])) {
                foreach ($rules['allow'] as $rule) {
                    $this->loadRule($rule, static::TYPE_ALLOW);
                }
            }

            if (isset($rules['deny'])) {
                foreach ($rules['deny'] as $rule) {
                    $this->loadRule($rule, static::TYPE_DENY);
                }
            }
        }
    }
}

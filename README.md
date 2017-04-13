# Service Provider Integration

This project provides a means of integrating
[PSR-11 containers](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-11-container.md)
at the provider-level.

**STATUS:** this is completely experimental and exploratory work - everything is subject to change!

### Comparison with [`container-interop/service-provider`](https://github.com/container-interop/service-provider) 

The approach proposed here is radically different from standardizing the service-providers themselves,
which is a much harder problem.

What this project defines is a simpler, more high-level exchange of entries, accomplishing much the
same thing as standardizing the providers themselves, while removing the decision for module-authors
to write "standard" vs "proprietary" service-providers, allowing full use of all of the features of
a selected container-implementation, and avoids compromising the internal integrity of
individual containers.

The `ServiceProviderInterface` of `container-interop/service-provider` includes a set of rules about
how the published entries must be treated by the receiving container. By contrast, the
`ServiceProviderInterface` defined by this package uses a simpler and more formal contract, in the
form of a second interface, which defines a "service registry" facet of a receiving container (or
container-factory, builder, adapter, etc.)

Unlike `container-interop/service-provider`, where individual package vendors implement the
`ServiceProviderInterface` many times over, the `ServiceProviderInterface` defined by this project
is intended to be implemented once, by the vendor of the container package:
 
- Implementing the `ServiceProviderInterface` enables the container to act as a service-provider,
  e.g. makes it capable of providing a container instance and *exporting* a list of entry identifiers.
  
- Implementing the `ServiceRegistryInterface` enables the container to act as a service-registry,
  e.g. makes it capable of *importing* entries defined by an external service-provider.

In other words, the service-provider and service-registry facets provide a synchronous contract,
enabling container (or factory/builder, adapter, etc.) implementations to act as either providers
or importers of a set of container entries from a container instance.

As noted [here](https://github.com/container-interop/container-interop/issues/55#issuecomment-285939658),
these interfaces may afford better separation of concerns, and may enable more patterns and concepts
to be implemented, such as the notion of public vs private entries, and more flexibility in scenarios
involving multiple containers.

Compared with the first attempt ([0.1](https://github.com/mindplay-dk/provider-interop/releases/tag/0.1))
to define this concept, this revision places control with the receiving container, as does the
[original service-provider proposal](https://github.com/container-interop/service-provider) - but (like
the first revision) attempts to reformulate the problem as finding a means of enabling interoperability
between containers, as opposed to standardizing the way containers get configured.

## Usage

To implement a self-contained "module", you will need to select a PSR-11 container-implementation
that is capable of acting as a service-provider, e.g. implements `ServiceProviderInterface` to export
it's entry identifiers, which may then be imported into a service-registry, e.g. another container,
factory, builder or adapter, etc.

In the following example, assume that `YourProvider` implements `ServiceProviderInterface` to expose
a `ContainerInterface` facet (or adapter) and a matching list of provided entry identifiers. The container
itself is an implementation detail of your provider, which could be an adapter, an extended PSR-11 container,
or any other structure capable of generating a container and list of entries.

Also, assume that `$my_container` is an instance of a container that is capable of acting as a service-registry,
e.g. implements `ServiceRegistryInterface` to import entries from your configured instance of `YourContainer`.

In the following example, I am importing container entries provided by a container that your provider
internally creates and configures:

```php
// I create an instance of your module's provider:

$your_provider = new YourProvider();

// Your provider exposes a PSR-11 container and list of entry identifiers:

assert($your_provider instanceof ServiceProviderInterface);

// My container can act as a service-registry:

assert($my_container instanceof ServiceRegistryInteface);

// I can now import the entries defined by your provider into my container:

$my_container->registerProvider($your_provider);
```

Note that a service-provider can be anything - it doesn't have to be a full container implementation, it
only has to expose entries in a PSR-11 format. In very simple scenarios, where your package does not benefit
from using a container to bootstrap itself internally, you can implement `ServiceProviderInterface` directly. 

## Implementation

To implement the `ServiceProviderInterface` as a facet of a container, the container-implementation must
provide a list of the published entry identifiers, for example by iterating over it's internal private map
of entries, in some proprietary format: 

```php
class MyContainer implements ContainerInterface, ServiceProviderInterface
{
    /**
     * @var callable[] map where entry identifier maps to callable factory function
     */
    private $this->factory_functions = [];
    
    public function listIdentifiers()
    {
        return array_keys($this->factory_functions);
    }
    
    public function getContainer()
    {
        return $this;
    }
    
    // ...
}
```

Note that this is just an example - in a container-implementation with an immutable container and a mutable
container-factory/builder, likely the factory/builder would act as the service-provider, such that the
creation of the actual container can be deferred until an entry from it is first needed.

The `ServiceRegistryInterface` may be implemented as a facet of a mutable container, or as a facet of a
container factory/builder, etc. and provides a means of importing entries into the internal service-registry
of a container-implementation, wherever that may be.

For example:

```php
class MyContainer implements ContainerInterface, ServiceRegistryInterface
{
    /**
     * @var callable[] map where entry identifier maps to callable factory function
     */
    private $this->factory_functions = [];
    
    public function registerProvider(ServiceProviderInterface $provider)
    {
        foreach ($provider->listIdentifiers() as $id) {
            $this->factory_functions[$id] = function () use ($id, $provider) {
                return $provider->getContainer()->get($id);
            };
        }
    }
    
    // ...
}
```

As you can see from this example, the first call to `getContainer()` is deferred until an entry is
required from it for the first time - this fact allows for the implementation of caching (and other
optimization) strategies, internally, in container/factory/adapter-implementations.

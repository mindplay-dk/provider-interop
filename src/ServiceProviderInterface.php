<?php

namespace Interop\Provider;

/**
 * A service-provider registers container entries with a registry (e.g. a container or factory/builder)
 *
 * A container-implementation may implement this interface, enabling the container to act as a provider,
 * effectively *exporting* all (or part) of it's container entries to a given container/factory/builder.
 */
interface ServiceProviderInterface
{
    /**
     * Registers all container entries published by this service-provider.
     *
     * @param ServiceRegistryInterface $registry
     */
    public function registerWith(ServiceRegistryInterface $registry);
}

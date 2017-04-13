<?php

namespace Interop\Provider;

/**
 * A service-registry provides a means of registering a list of entries defined by a service-provider.
 *
 * A container (or container-factory) implementation may implement this interface, enabling it to act
 * as a service-registry, effectively *importing* the entries defined by an external provider.
 */
interface ServiceRegistryInterface
{
    /**
     * Register a given provider with this service-registry.
     *
     * The service-registry will query the list of entry identifiers exported by the given provider,
     * and internally register these entries. (Note that mutable containers must be fully populated
     * prior to registration - that is, the list of entries must be complete when registered.)
     *
     * The service-registry may obtain the container exposed by the given provider at a later time,
     * e.g. when any of the entries listed by that provider are required.
     *
     * @param ServiceProviderInterface $provider
     */
    public function registerProvider(ServiceProviderInterface $provider);
}

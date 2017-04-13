<?php

namespace Interop\Provider;

use Psr\Container\ContainerInterface;

/**
 * A service-provider exposes a list of entry identifiers, and a matching `ContainerInterface` instance
 * capable of providing those entries.
 *
 * A container (or container-factory) implementation may implement this interface, enabling the container
 * to act as a provider, effectively *exporting* all (or part) of it's entries for other containers (or
 * container-factories) to obtain.
 */
interface ServiceProviderInterface
{
    /**
     * @return ContainerInterface a container instance capable of providing the listed entries
     */
    public function getContainer();

    /**
     * @return string[] list of identifiers for which the container instance can provide entries
     */
    public function listIdentifiers();
}

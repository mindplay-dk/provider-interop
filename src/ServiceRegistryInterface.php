<?php

namespace Interop\Provider;

/**
 * A Registry provides a means of registering container (or factory/builder) entries.
 *
 * A container (or factory/builder) implementation may implement this interface, enabling
 * the container/factory/builder to act as a service registry, effectively *importing* any
 * container entries defined by an external provider.
 */
interface ServiceRegistryInterface
{
    /**
     * Registers an entry by providing a callback (to defer the resolution of that entry.)
     *
     * A valid resolver function has zero arguments and returns an entry value of any type.
     *
     * @param string   $id       Identifier of the entry to register
     * @param callable $resolver A function that resolves and returns the value/component
     */
    public function register($id, callable $resolver);
}

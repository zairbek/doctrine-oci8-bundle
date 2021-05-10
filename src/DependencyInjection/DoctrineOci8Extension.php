<?php

/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace EcPhp\DoctrineOci8Bundle\DependencyInjection;

use EcPhp\DoctrineOci8\Doctrine\DBAL\Driver\OCI8\Driver;
use EcPhp\DoctrineOci8\Doctrine\DBAL\Types\CursorType;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DoctrineOci8Extension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        // noop
    }

    public function prepend(ContainerBuilder $container)
    {
        if (false === $container->hasExtension('doctrine')) {
            return false;
        }

        $container
            ->loadFromExtension(
                'doctrine',
                [
                    'dbal' => [
                        'driver_class' => Driver::class,
                        'types' => [
                            'cursor' => CursorType::class,
                        ],
                    ],
                ],
            );
    }
}

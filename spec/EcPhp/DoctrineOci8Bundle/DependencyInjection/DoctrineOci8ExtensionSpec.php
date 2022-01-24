<?php

/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/ecphp
 */

declare(strict_types=1);

namespace spec\EcPhp\DoctrineOci8Bundle\DependencyInjection;

use EcPhp\DoctrineOci8\Doctrine\DBAL\Driver\OCI8\Driver;
use EcPhp\DoctrineOci8\Doctrine\DBAL\Types\CursorType;
use EcPhp\DoctrineOci8Bundle\DependencyInjection\DoctrineOci8Extension;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineOci8ExtensionSpec extends ObjectBehavior
{
    public function it_do_not_change_anything_if_doctrine_extension_is_not_available(ContainerBuilder $containerBuilder)
    {
        $containerBuilder
            ->hasExtension('doctrine')
            ->willReturn(false)
            ->shouldBeCalledOnce();

        $this
            ->prepend($containerBuilder)
            ->shouldReturn(false);
    }

    public function it_is_initializable(ContainerBuilder $containerBuilder)
    {
        $this->shouldHaveType(DoctrineOci8Extension::class);

        $containerBuilder->setParameter()->shouldNotBeCalled();

        $this->load([], $containerBuilder);
    }

    public function it_setup_the_doctrine_configuration(ContainerBuilder $containerBuilder)
    {
        $containerBuilder
            ->hasExtension('doctrine')
            ->willReturn(true)
            ->shouldBeCalledOnce();

        $containerBuilder
            ->loadFromExtension(
                'doctrine',
                [
                    'dbal' => [
                        'driver_class' => Driver::class,
                        'types' => [
                            'cursor' => CursorType::class,
                        ],
                    ],
                ]
            )
            ->shouldBeCalledOnce();

        $this->prepend($containerBuilder);
    }
}

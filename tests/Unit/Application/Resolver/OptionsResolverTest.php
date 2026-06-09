<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Tests\Unit\Application\Resolver;

use Letkode\FormSchemaBundle\Application\DTO\OptionDTO;
use Letkode\FormSchemaBundle\Application\Resolver\OptionsResolver;
use Letkode\FormSchemaBundle\Domain\Contract\OptionsSourceInterface;
use Letkode\FormSchemaBundle\Domain\Contract\OptionsSourceRegistryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class OptionsResolverTest extends TestCase
{
    private OptionsSourceRegistryInterface&MockObject $registry;
    private OptionsResolver $resolver;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(OptionsSourceRegistryInterface::class);
        $this->resolver = new OptionsResolver($this->registry);
    }

    #[Test]
    public function testReturnsEmptyArrayWhenSetOptionsEmpty(): void
    {
        $this->registry->expects(self::never())->method('get');

        $result = $this->resolver->resolve([], 'es');

        self::assertSame([], $result);
    }

    #[Test]
    public function testReturnsEmptyArrayWhenTypeNotSet(): void
    {
        $this->registry->expects(self::never())->method('get');

        $result = $this->resolver->resolve(['params' => ['some' => 'param']], 'es');

        self::assertSame([], $result);
    }

    #[Test]
    public function testDelegatesResolveToSource(): void
    {
        $option = new OptionDTO(value: 1, label: 'Option 1');
        $source = $this->createMock(OptionsSourceInterface::class);
        $source->expects(self::once())
            ->method('resolve')
            ->with(['entity' => 'Foo'], 'es')
            ->willReturn([$option]);

        $this->registry->expects(self::once())
            ->method('get')
            ->with('general')
            ->willReturn($source);

        $result = $this->resolver->resolve(['type' => 'general', 'params' => ['entity' => 'Foo']], 'es');

        self::assertCount(1, $result);
        self::assertSame($option, $result[0]);
    }
}

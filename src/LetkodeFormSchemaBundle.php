<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle;

use Letkode\FormSchemaBundle\DependencyInjection\Compiler\RegisterFormOptionsProvidersPass;
use Letkode\FormSchemaBundle\Domain\Contract\FieldTypeInterface;
use Letkode\FormSchemaBundle\Domain\Contract\FormRenderInterface;
use Letkode\FormSchemaBundle\Domain\Contract\GroupRenderInterface;
use Letkode\FormSchemaBundle\Domain\Contract\InteractionHandlerInterface;
use Letkode\FormSchemaBundle\Domain\Contract\OptionsSourceInterface;
use Letkode\FormSchemaBundle\Domain\Contract\SectionRenderInterface;
use Letkode\FormSchemaBundle\Infrastructure\Cache\CachedFormSchemaResolver;
use Letkode\FormSchemaBundle\Infrastructure\Cache\DoctrineCacheInvalidationSubscriber;
use Letkode\FormSchemaBundle\Infrastructure\Doctrine\TableNameSubscriber;
use Letkode\FormSchemaBundle\Seeder\Contract\FormSeederInterface;
use Letkode\FormSchemaBundle\Seeder\Contract\OptionGeneralSeederInterface;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class LetkodeFormSchemaBundle extends AbstractBundle
{
    #[\Override]
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('default_locale')->defaultValue('es')->end()
                ->arrayNode('available_locales')
                    ->scalarPrototype()->end()
                    ->defaultValue(['es'])
                ->end()
                ->scalarNode('entity_namespace')->defaultValue('App\\Entity')->end()
                ->scalarNode('table_prefix')->defaultValue('')->end()
                ->arrayNode('table_names')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('form')->defaultNull()->end()
                        ->scalarNode('form_section')->defaultNull()->end()
                        ->scalarNode('form_group')->defaultNull()->end()
                        ->scalarNode('form_field')->defaultNull()->end()
                        ->scalarNode('form_option_general')->defaultNull()->end()
                        ->scalarNode('form_option_general_value')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('cache')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->scalarNode('pool')->defaultValue('cache.app')->end()
                        ->integerNode('ttl')->defaultValue(3600)->end()
                        ->scalarNode('key_prefix')->defaultValue('fsb')->end()
                        ->booleanNode('auto_invalidate')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('disabled_field_types')->scalarPrototype()->end()->defaultValue([])->end()
                ->arrayNode('disabled_options_sources')->scalarPrototype()->end()->defaultValue([])->end()
                ->arrayNode('disabled_form_renders')->scalarPrototype()->end()->defaultValue([])->end()
                ->arrayNode('disabled_section_renders')->scalarPrototype()->end()->defaultValue([])->end()
                ->arrayNode('disabled_group_renders')->scalarPrototype()->end()->defaultValue([])->end()
                ->scalarNode('seeds_path')->defaultValue('%kernel.project_dir%/config/seeds/form-schema')->end()
            ->end()
        ;
    }

    #[\Override]
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__ . '/Resources/config/services.yaml');

        $builder->setParameter('letkode_form_schema.default_locale', $config['default_locale']);
        $builder->setParameter('letkode_form_schema.available_locales', $config['available_locales']);
        $builder->setParameter('letkode_form_schema.entity_namespace', $config['entity_namespace']);
        $builder->setParameter('letkode_form_schema.table_prefix', $config['table_prefix']);
        $builder->setParameter('letkode_form_schema.table_names', $config['table_names']);
        $builder->setParameter('letkode_form_schema.cache.enabled', $config['cache']['enabled']);
        $builder->setParameter('letkode_form_schema.cache.pool', $config['cache']['pool']);
        $builder->setParameter('letkode_form_schema.cache.ttl', $config['cache']['ttl']);
        $builder->setParameter('letkode_form_schema.cache.key_prefix', $config['cache']['key_prefix']);
        $builder->setParameter('letkode_form_schema.cache.auto_invalidate', $config['cache']['auto_invalidate']);
        $builder->setParameter('letkode_form_schema.disabled_field_types', $config['disabled_field_types']);
        $builder->setParameter('letkode_form_schema.disabled_options_sources', $config['disabled_options_sources']);
        $builder->setParameter('letkode_form_schema.disabled_form_renders', $config['disabled_form_renders']);
        $builder->setParameter('letkode_form_schema.disabled_section_renders', $config['disabled_section_renders']);
        $builder->setParameter('letkode_form_schema.disabled_group_renders', $config['disabled_group_renders']);
        $builder->setParameter('letkode_form_schema.seeds_path', $config['seeds_path']);

        $container->services()
            ->set(TableNameSubscriber::class)
                ->args([$config['table_prefix'], $config['table_names']])
                ->tag('doctrine.event_listener', ['event' => 'loadClassMetadata']);

        if ($config['cache']['enabled']) {
            $container->services()
                ->set(Infrastructure\Cache\FormSchemaCacheInvalidator::class)
                ->args([
                    service($config['cache']['pool']),
                    $config['cache']['key_prefix'],
                ])
                ->public();

            $container->services()
                ->set(CachedFormSchemaResolver::class)
                ->decorate('Letkode\FormSchemaBundle\Domain\Contract\FormSchemaResolverInterface')
                ->args([
                    service('.inner'),
                    service($config['cache']['pool']),
                    $config['cache']['ttl'],
                    $config['cache']['key_prefix'],
                ])
                ->public();

            if ($config['cache']['auto_invalidate']) {
                $container->services()
                    ->set(DoctrineCacheInvalidationSubscriber::class)
                    ->args([service(Infrastructure\Cache\FormSchemaCacheInvalidator::class)])
                    ->tag('doctrine.event_listener', ['event' => 'postUpdate'])
                    ->tag('doctrine.event_listener', ['event' => 'postPersist'])
                    ->tag('doctrine.event_listener', ['event' => 'postRemove']);
            }
        }
    }

    #[\Override]
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterFormOptionsProvidersPass());

        $container->registerForAutoconfiguration(FieldTypeInterface::class)
            ->addTag('form_schema.field_type');

        $container->registerForAutoconfiguration(OptionsSourceInterface::class)
            ->addTag('form_schema.options_source');

        $container->registerForAutoconfiguration(FormRenderInterface::class)
            ->addTag('form_schema.form_render');

        $container->registerForAutoconfiguration(SectionRenderInterface::class)
            ->addTag('form_schema.section_render');

        $container->registerForAutoconfiguration(GroupRenderInterface::class)
            ->addTag('form_schema.group_render');

        $container->registerForAutoconfiguration(InteractionHandlerInterface::class)
            ->addTag('form_schema.interaction_handler');

        $container->registerForAutoconfiguration(FormSeederInterface::class)
            ->addTag('form_schema.form_seed');

        $container->registerForAutoconfiguration(OptionGeneralSeederInterface::class)
            ->addTag('form_schema.option_general_seed');
    }
}

# Changelog

## [1.0.3] - 2026-06-09

### Changed

- Recipe: variables de entorno `LKA_*` inyectadas en el `.env` del proyecto al instalar el bundle vía Symfony Flex
  - `LKA_DEFAULT_LOCALE` (default `es`) — locale por defecto del bundle
  - `LKA_FORM_SCHEMA_CACHE` (default `false`) — activa/desactiva la caché PSR-6; en producción setear a `true`
- Recipe: `available_locales` queda como lista estática en el YAML (los nodos array no soportan env vars en Symfony Config)

---

## [1.0.2] - 2026-06-09

### Changed

> **BREAKING** — requiere migración de BD y actualización de código si usabas las entidades directamente.

- **`FormOptionGeneral` → `FormOption`**: clase PHP renombrada; tabla BD `form_option_general` → `form_option`
- **`FormOptionGeneralValue` → `FormOptionValue`**: clase PHP renombrada; tabla BD `form_option_general_value` → `form_option_value`
- **Config `table_names`**: claves `form_option_general` y `form_option_general_value` → `form_option` y `form_option_value`
- **`OptionGeneralSeederInterface`** eliminada; reemplazada por `OptionSeederInterface` con método `getOptionData()` (antes `getOptionGeneralData()`)
- **YAML seed key**: la clave raíz de los archivos de opciones cambia de `option_general:` a `option:`
- **Directorio de seeds de opciones**: `general_options/` → `options/`

### Fixed

- Recipe: directorio `general-options/` renombrado a `options/` (notación underscore, alineado con el rename de entidad)
- Recipe: scaffold de `config/seeds/` restaurado vía `copy-from-recipe` usando `config/` como raíz

### Migration guide

**1. Migración de BD** (necesaria aunque no hayas personalizado nombres de tabla):

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

Las migraciones renombrarán `form_option_general` → `form_option` y `form_option_general_value` → `form_option_value`.

**2. Config** — si tenías `table_names` con los nombres anteriores:

```yaml
# antes
table_names:
    form_option_general: my_custom_name
    form_option_general_value: my_custom_value_name

# después
table_names:
    form_option: my_custom_name
    form_option_value: my_custom_value_name
```

**3. Código** — actualiza cualquier import o referencia directa a las entidades:

```php
// antes
use Letkode\FormSchemaBundle\Domain\Entity\FormOptionGeneral;
use Letkode\FormSchemaBundle\Domain\Entity\FormOptionGeneralValue;
use Letkode\FormSchemaBundle\Domain\Repository\FormOptionGeneralRepositoryInterface;

// después
use Letkode\FormSchemaBundle\Domain\Entity\FormOption;
use Letkode\FormSchemaBundle\Domain\Entity\FormOptionValue;
use Letkode\FormSchemaBundle\Domain\Repository\FormOptionRepositoryInterface;
```

**4. Seeders PHP** — si implementabas `OptionGeneralSeederInterface`:

```php
// antes
class MySeeder implements OptionGeneralSeederInterface
{
    public function getOptionGeneralData(): array { ... }
}

// después
class MySeeder implements OptionSeederInterface
{
    public function getOptionData(): array { ... }
}
```

**5. YAML de opciones** — renombrar la clave raíz y mover el archivo:

```yaml
# antes (en config/seeds/form_schema/general_options/countries.yaml)
option_general:
  tag: countries
  ...

# después (en config/seeds/form_schema/options/countries.yaml)
option:
  tag: countries
  ...
```

---

## [1.0.1] - 2026-06-09

### Added

- **Symfony 8.x compatibility** — ampliar constraints de `symfony/*` a `^7.0 || ^8.0`
- **Sistema de seeders** — mecanismo declarativo para poblar y mantener schemas de formularios desde archivos versionados (ver [docs/seeders.md](docs/seeders.md)):
  - Dos formatos de fuente: archivos YAML y clases PHP (`implements FormSeederInterface` / `OptionSeederInterface`)
  - Estructura de directorios en el proyecto consumidor:
    ```
    config/seeds/form_schema/
        forms/       ← YAMLs de formularios
        options/     ← YAMLs de catálogos de opciones
    ```
  - Tracking automático por checksum sha256 en columna `seed_checksum` — los seeds sin cambios se saltan automáticamente
  - Cuatro comandos de consola:
    - `letkode:form-schema:seed` — siembra formularios (`--seed`, `--prune`, `--force`)
    - `letkode:form-schema:seed:options` — siembra catálogos de opciones (`--option`, `--prune`, `--force`)
    - `letkode:form-schema:remove <type> <tag>` — soft-delete de un formulario u opción
    - `letkode:form-schema:export <type> <tag>` — exporta un formulario u opción a YAML (`--output`)
  - Clave de configuración `seeds_path` (default `%kernel.project_dir%/config/seeds/form_schema`)
  - `symfony/yaml` incorporado como dependencia directa
- **Atributos PHP** `#[AsFormSeed]` y `#[AsOptionSeed]` para autodescubrir seeders PHP vía service tags

### Fixed

- Recipe: notación en underscore en nombres de directorio bajo `config/`
- Recipe: reemplazados `.gitkeep` por `.gitignore` en directorios de seeds

---

## [1.0.0] - 2026-06-09

### Added
- Initial release of `letkode/form-schema-bundle` Symfony Bundle
- 6 Doctrine entities: `Form`, `FormSection`, `FormGroup`, `FormField`, `FormOptionGeneral` (→ `FormOption` en 1.0.2), `FormOptionGeneralValue` (→ `FormOptionValue` en 1.0.2)
- 22 built-in field types with extensible Strategy + Registry pattern
- `FieldTypeInterface::getDefaultParams(): array` — each FieldType declares its UI params with defaults; the resolver merges them with DB-stored params (DB wins)
- `SelectFieldType` (`select`) — single-select. Params: `size`, `searchable`, `tags_mode`, `search_limit`, `min_search_length`. Value: `string|null`
- `SelectMultipleFieldType` (`select-multiple`) — multi-select. Params: same as `select` plus `max_count_items`. Value: always `array`; `formatDefaultValue` normalises `null → []` and `string → [string]`
- `RangeFieldType` — range slider with `color`, `min`, `max`, `step`, `show_steps` params (FlyonUI `range`)
- `ComboboxFieldType` — autocomplete with `open_on_focus`, `min_search_length`, `api_url` params (FlyonUI `combo-box`)
- `PinFieldType` — PIN/OTP input with `variant`, `length`, `input_type`, `chars_pattern` params (FlyonUI `pin-input`)
- `TreeFieldType` — hierarchical tree with `selection_mode` (checkbox/radio), `auto_select_children`, `expanded_by_default` params (FlyonUI `tree-view`); tree hierarchy via `option.data.children`
- `DuallistFieldType` — dual-panel selector with `searchable`, `source_label`, `target_label`, `show_move_all_buttons` params
- `OptionDTO.data` (default `[]`) — universal extensibility field for options: data-attrs for client-side interaction filtering and `data.children` for tree hierarchy
- `OptionDTO.label` / `OptionGroupDTO.label` — display-text key for options and option groups
- 2 built-in option sources: `general` (internal catalog) and `entity` (project repositories via `#[AsFormOptionsProvider]`)
- 3-level structural renders with clear separation of responsibilities:
  - `FormRender` — defines how sections are navigated: `default`, `stepper`, `tabs`
  - `SectionRender` — defines how groups are distributed within a section: `default`, `accordion`, `collapsible`, `tabs`
  - `GroupRender` — defines how fields are distributed within a group: `default`, `fieldset`, `matrix`, `tabs`
- `FieldAttributes.actions` — context-keyed map replacing individual boolean flags. Each context entry supports `enabled` (visibility) plus per-context attribute overrides (`required`, `readonly`, etc.)
- `FieldAttributes::withActionOverrides(string $context)` — returns a new instance with overrides applied for the given context
- `FormSchemaResolver` with immutable builder pattern (`schema()->withLocale()->withContext()->resolve()`)
- PSR-6 opt-in cache with `TagAwareAdapterInterface` support and `FormSchemaCacheInvalidator`
- `DoctrineCacheInvalidationSubscriber` opt-in for automatic cache invalidation
- Interaction handler system with 7 built-in handlers (`toggle_visibility`, `toggle_required`, `set_value`, `filter_options`, `ajax_validate`, `set_date_constraint`, `compute`)
- PHP 8.4 native features: property hooks, asymmetric visibility, `array_find`/`array_any`/`array_all`
- Soft delete on all entities via Gedmo SoftDeleteable
- Entity mapping via project's Doctrine Entity Manager (`type: attribute`, no auto-registration)

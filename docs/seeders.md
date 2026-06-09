# Seeders

El sistema de seeders permite crear y mantener formularios y catálogos de opciones desde archivos versionados, sin tocar la base de datos directamente. Soporta dos formatos de fuente: YAML y clases PHP.

---

## Directorios por defecto

```
config/seeds/form_schema/
    forms/               ← un archivo YAML por formulario
        contact.yaml
    options/     ← un archivo YAML por catálogo de opciones
        countries.yaml
```

La ruta raíz es configurable con la clave `seeds_path`:

```yaml
# config/packages/letkode_form_schema.yaml
letkode_form_schema:
    seeds_path: '%kernel.project_dir%/config/seeds/form_schema'
```

---

## Formato YAML — formularios

```yaml
# config/seeds/form_schema/forms/contact.yaml
form:
  tag: contact
  name: Formulario de contacto
  enabled: true
  default_lang: es
  parameters:
    type_render: default
  sections:
    - tag: personal
      name: Datos personales
      position: 0
      enabled: true
      parameters:
        type_render: default
      groups:
        - tag: nombres
          name: Nombres
          position: 0
          enabled: true
          parameters:
            type_render: default
          fields:
            - tag: first_name
              name: Nombre
              type: text
              position: 0
              enabled: true
              parameters:
                placeholder: Escribe tu nombre
                default_value: ~
              attributes:
                required: true
                readonly: false
```

Campos obligatorios: `tag` (único por formulario), `name`. El `tag` es el identificador estable — nunca cambia aunque el nombre cambie.

---

## Formato YAML — catálogos de opciones

```yaml
# config/seeds/form_schema/options/countries.yaml
option:
  tag: countries
  name: Países
  values:
    - tag: mx
      label: México
      position: 0
      enabled: true
    - tag: us
      label: Estados Unidos
      position: 1
      enabled: true
    - tag: ar
      label: Argentina
      position: 2
      enabled: true
      description: Opcional — descripción del valor
```

---

## Formato PHP — formularios

Útil cuando los datos del seed son dinámicos o se generan programáticamente.

```php
<?php
// src/FormSeeds/ContactFormSeeder.php
declare(strict_types=1);

use Letkode\FormSchemaBundle\Attribute\AsFormSeed;
use Letkode\FormSchemaBundle\Seeder\Contract\FormSeederInterface;

#[AsFormSeed]
final class ContactFormSeeder implements FormSeederInterface
{
    public function getFormData(): array
    {
        return [
            'tag'          => 'contact',
            'name'         => 'Formulario de contacto',
            'enabled'      => true,
            'default_lang' => 'es',
            'parameters'   => ['type_render' => 'default'],
            'sections'     => [
                [
                    'tag'      => 'personal',
                    'name'     => 'Datos personales',
                    'position' => 0,
                    'enabled'  => true,
                    'groups'   => [],
                ],
            ],
        ];
    }
}
```

El atributo `#[AsFormSeed]` registra automáticamente la clase como servicio tagged. No hace falta declararlo en `services.yaml`.

---

## Formato PHP — catálogos de opciones

```php
<?php
declare(strict_types=1);

use Letkode\FormSchemaBundle\Attribute\AsOptionSeed;
use Letkode\FormSchemaBundle\Seeder\Contract\OptionSeederInterface;

#[AsOptionSeed]
final class CountriesOptionSeeder implements OptionSeederInterface
{
    public function getOptionData(): array
    {
        return [
            'tag'    => 'countries',
            'name'   => 'Países',
            'values' => [
                ['tag' => 'mx', 'label' => 'México',          'position' => 0, 'enabled' => true],
                ['tag' => 'us', 'label' => 'Estados Unidos',  'position' => 1, 'enabled' => true],
            ],
        ];
    }
}
```

---

## Tracking por checksum

Cada seed registra un hash sha256 en la columna `seed_checksum` de la entidad. Si el contenido no cambió, el seed se salta automáticamente — no genera escrituras innecesarias en BD.

| Escenario | Resultado |
|---|---|
| Primer seed | `created` |
| Re-seed sin cambios | `skipped` |
| Seed con contenido modificado | `updated` |
| Seed con `--force` | `updated` (ignora checksum) |
| Seed con error de validación | `error` — ningún cambio en BD |

> La columna `seed_checksum` se añade vía migración de Doctrine. Ejecuta `doctrine:migrations:diff` + `doctrine:migrations:migrate` tras instalar esta versión.

---

## Comandos de consola

### `letkode:form-schema:seed`

Siembra formularios desde archivos YAML y/o clases PHP.

```bash
# Procesar todos los seeds de formularios
php bin/console letkode:form-schema:seed

# Procesar solo un formulario concreto
php bin/console letkode:form-schema:seed --seed=contact

# Forzar re-seed aunque el checksum no haya cambiado
php bin/console letkode:form-schema:seed --force

# Eliminar (soft-delete) secciones/grupos/campos que ya no están en el seed
php bin/console letkode:form-schema:seed --prune

# Combinar opciones
php bin/console letkode:form-schema:seed --seed=contact --prune --force
```

La salida es una tabla con columnas `Source | Tag | Status | Errors`.

---

### `letkode:form-schema:seed:options`

Siembra catálogos de opciones.

```bash
php bin/console letkode:form-schema:seed:options
php bin/console letkode:form-schema:seed:options --option=countries
php bin/console letkode:form-schema:seed:options --prune --force
```

---

### `letkode:form-schema:remove`

Soft-delete de un formulario o catálogo de opciones por tag. Pide confirmación interactiva.

```bash
# Eliminar un formulario
php bin/console letkode:form-schema:remove form contact

# Eliminar un catálogo de opciones
php bin/console letkode:form-schema:remove option countries

# Sin confirmación (útil en scripts CI)
php bin/console letkode:form-schema:remove form contact --no-interaction
```

---

### `letkode:form-schema:export`

Exporta el estado actual de un formulario u opción desde BD a formato YAML. Útil para generar el seed inicial a partir de datos ya existentes.

```bash
# Imprimir en stdout
php bin/console letkode:form-schema:export form contact

# Escribir a archivo
php bin/console letkode:form-schema:export form contact --output=config/seeds/form_schema/forms/contact.yaml

# Exportar un catálogo de opciones
php bin/console letkode:form-schema:export option countries --output=config/seeds/form_schema/options/countries.yaml
```

---

## Comportamiento con `--prune`

Sin `--prune` el seed solo **añade o actualiza** — nunca elimina. Con `--prune`, cualquier sección, grupo, campo o valor que esté en BD pero no aparezca en el seed recibe un soft-delete.

```
BD antes del seed:
  contact → personal → nombres → [first_name, last_name]

YAML del seed:
  contact → personal → nombres → [first_name]   ← last_name desapareció

Resultado SIN --prune: last_name permanece en BD
Resultado CON --prune: last_name recibe soft-delete
```

---

## Detección de tags duplicados

Si dos fuentes (YAML + PHP, o dos archivos YAML) declaran el mismo `tag`, el comando aborta antes de procesar ningún seed y lista los duplicados. Esto evita comportamientos ambiguos.

---

## Convivencia YAML y PHP

Ambos formatos se descubren en paralelo y se procesan juntos. Un proyecto puede usar solo YAML, solo PHP, o ambos. La restricción es que no puede haber dos seeders con el mismo `tag`, independientemente del formato.

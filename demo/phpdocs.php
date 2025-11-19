<?php
require_once __DIR__ . '/app.php';

use Illuminate\Database\Capsule\Manager;
//accept 1 argument: the model name to generate phpdoc for (optional) it can be multiple words without spaces separated by commas
$modelNames = $argv[1] ?? null;

if ($modelNames) {
    echo "Generando PHPDoc solo para el modelo {$modelNames}..." . PHP_EOL;
} else {
    echo "Generando PHPDoc para todos los modelos..." . PHP_EOL;
}

// get all the models inside the Models directory
$modelsDir = __DIR__ . '/../app/Models';
$modelFiles = scandir($modelsDir);
$models = [];

if ($modelNames) {
    $models = explode(',', $modelNames);
    foreach ($models as $model) {
        if (!in_array("{$model}.php", $modelFiles)) {
            echo "El modelo {$model} no existe en la carpeta Models." . PHP_EOL;
            exit(1);
        }
    }
} else {
    foreach ($modelFiles as $file) {
        if (preg_match('/^([A-Za-z0-9_]+)\.php$/', $file, $matches)) {
            $models[] = $matches[1];
        }
    }
}


foreach ($models as $model) {
    $properties = [];
    $modelClass = "App\\Models\\{$model}";

    //get the table name from the model
    $tableName = (new $modelClass)->getTable();
    echo "Generando PHPDoc para el modelo {$model} basado en la tabla {$tableName}..." . PHP_EOL;
    $modelFile = "{$modelsDir}/{$model}.php";


    // Obtener info de columnas
    $columns = Manager::schema()->getColumns($tableName);


    foreach ($columns as $col) {
        $properties[$col['name']] = mapType($col['type_name']);
    }

    // Para cada función, buscar qué relación retorna
    $contents = file_get_contents($modelFile);

    // Buscar todas las funciones public
    preg_match_all('/public function (\w+)\s*\(/', $contents, $matches);
    /** @var string[] */
    $functions = $matches[1] ?? [];

    $relationMap = []; // name => model/collection info

    // Primero detectamos relaciones directas
    foreach ($functions as $fn) {
        if (preg_match("/function $fn.*?\{(.*?)\}/s", $contents, $block)) {

            $body = $block[1];

            // hasMany
            if (preg_match('/return\s+\$this->hasMany\(\s*([A-Za-z0-9_\\\\]+)::class/', $body, $m)) {
                $relatedModel = trim(str_replace(['::class', "'"], '', $m[1]));
                $relationMap[$fn] = [
                    'type' => 'collection',
                    'model' => $relatedModel
                ];
            }

            // belongsTo
            if (preg_match('/return\s+\$this->belongsTo\(\s*([A-Za-z0-9_\\\\]+)::class/', $body, $m)) {
                $relatedModel = trim(str_replace(['::class', "'"], '', $m[1]));
                $relationMap[$fn] = [
                    'type' => 'single',
                    'model' => $relatedModel
                ];
            }

            // hasOne
            if (preg_match('/return\s+\$this->hasOne\(\s*([A-Za-z0-9_\\\\]+)::class/', $body, $m)) {
                $relatedModel = trim(str_replace(['::class', "'"], '', $m[1]));
                $relationMap[$fn] = [
                    'type' => 'single',
                    'model' => $relatedModel
                ];
            }

            // belongsToMany
            if (preg_match('/return\s+\$this->belongsTo Many\(\s*([A-Za-z0-9_\\\\]+)::class/', $body, $m)) {
                $relatedModel = trim(str_replace(['::class', "'"], '', $m[1]));
                $relationMap[$fn] = [
                    'type' => 'collection',
                    'model' => $relatedModel
                ];
            }
        }
    }

    // Ahora detectar relaciones derivadas: return $this->charges()->where...
    foreach ($functions as $fn) {
        if (!isset($relationMap[$fn])) {

            if (preg_match("/function $fn.*?\{(.*?)\}/s", $contents, $block)) {
                $body = $block[1];

                // Detectar return $this->X()->...
                if (preg_match('/return\s+\$this->(\w+)\s*\(\)\s*->/', $body, $m)) {

                    $base = $m[1]; // nombre de relación padre

                    if (isset($relationMap[$base])) {
                        // hereda el tipo de la relación base
                        $relationMap[$fn] = $relationMap[$base];
                    }
                }
            }
        }
    }



    // Añadir al PHPDoc
    foreach ($relationMap as $name => $rel) {
        $properties[$name] = $rel['type'] === 'collection' ? "Collection<int, {$rel['model']}>" : "{$rel['model']}|null";
    }

    // Construir PHPDoc
    $phpdoc = "/**\n";
    mergePropertiesWithCasts($properties, (new $modelClass)->getCasts());

    // Verificar si hay propiedades tipo Collection
    $hasCollectionProperty = false;
    $hasDateProperty = false;
    foreach ($properties as $property => $phpType) {
        $phpdoc .= " * @property {$phpType} \${$property}\n";
        if (str_contains($phpType, 'Collection<')) {
            $hasCollectionProperty = true;
        }
        if (str_contains($phpType, 'CarbonInterface')) {
            $hasDateProperty = true;
        }
    }
    $phpdoc .= " */\n";

    // 1. Quitar PHPDoc anterior si existe (solo el que está inmediatamente antes de la clase)
    $contents = preg_replace(
        "/\/\*\*[\s\S]*?\*\/\s*class\s+{$model}\s+/",
        "class {$model} ",
        $contents,
        1
    );

    // 2. Agregar import de Collection si es necesario y no existe
    if ($hasCollectionProperty) {
        // Verificar si ya existe algún import de Collection
        $hasEloquentCollection = preg_match('/use\s+Illuminate\\\\Database\\\\Eloquent\\\\Collection;/', $contents);
        $hasSupportCollection = preg_match('/use\s+Illuminate\\\\Support\\\\Collection;/', $contents);

        // Si no hay ningún import de Collection, agregar el de Eloquent
        if (!$hasEloquentCollection && !$hasSupportCollection) {
            // Buscar la última línea "use ..." antes de la clase
            $contents = preg_replace(
                "/(use\s+[^;]+;)\s*\n\s*\n/",
                "$1\nuse Illuminate\\Database\\Eloquent\\Collection;\n\n",
                $contents,
                1
            );
        }
        // Si solo existe Support\Collection, no hacer nada (el usuario lo prefiere así)
        // Si ya existe Eloquent\Collection, no hacer nada
    }

    // Agregar import de CarbonInterface si es necesario y no existe
    if ($hasDateProperty) {
        $hasCarbonImport = preg_match('/use\s+Carbon\\\\CarbonInterface;/', $contents);
        if (!$hasCarbonImport) {
            // Buscar la última línea "use ..." antes de la clase
            $contents = preg_replace(
                "/(use\s+[^;]+;)\s*\n\s*\n/",
                "$1\nuse Carbon\\CarbonInterface;\n\n",
                $contents,
                1
            );
        }
    }

    // 3. Insertar el nuevo PHPDoc antes de "class ModelName"
    $contents = preg_replace(
        "/class\s+{$model}\s+/",
        "{$phpdoc}class {$model} ",
        $contents,
        1
    );

    file_put_contents($modelFile, $contents);
}



function mergePropertiesWithCasts(array &$properties, array $casts): array
{
    foreach ($casts as $field => $castType) {

        $phpType = match ($castType) {
            'int', 'integer' => 'int',
            'real', 'float', 'double' => 'float',
            'string' => 'string',
            'bool', 'boolean' => 'bool',
            'array', 'json' => 'array',
            'collection' => 'Collection',
            'date', 'date:Y-m-d' => 'CarbonInterface',
            default => 'mixed',
        };

        // REEMPLAZAR si existe o AGREGAR si no existe
        $properties[$field] = $phpType;
    }

    return $properties;
}

function mapType($mysqlType)
{
    $type = strtolower($mysqlType);

    return match (true) {
        str_contains($type, 'int')      => 'int',
        str_contains($type, 'decimal'),
        str_contains($type, 'float'),
        str_contains($type, 'double')   => 'float',
        str_contains($type, 'char'),
        str_contains($type, 'text'),
        str_contains($type, 'date'),
        str_contains($type, 'time'),
        str_contains($type, 'json')     => 'string',
        default                         => 'mixed',
    };
}

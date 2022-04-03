<?php
spl_autoload_register(function ($className) {

    if (startsWith($className, 'PHPUnit')) {
        // ignore PHPUnit classes
        return;
    }

    $classFolders = ['src' . DIRECTORY_SEPARATOR . 'controllers',
        'src' . DIRECTORY_SEPARATOR . 'model',
        'src' . DIRECTORY_SEPARATOR . 'services',
        'src' . DIRECTORY_SEPARATOR . 'persistence',
        'tests' . DIRECTORY_SEPARATOR . 'controllers',
        'tests' . DIRECTORY_SEPARATOR . 'model',
        'tests' . DIRECTORY_SEPARATOR . 'services',];

    $included = false;
    foreach ($classFolders as &$classFolder) {
        if (!$included) {
            $path = sprintf('%1$s%2$s%3$s.php',
                // %1$s: get absolute path of src folder
                realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $classFolder),
                // %2$s: / or \ (depending on OS)
                DIRECTORY_SEPARATOR,
                // replace _ by / or \ (depending on OS)
                str_replace('_', DIRECTORY_SEPARATOR, $className)
            );
            if (file_exists($path)) {
                include_once $path;
                $included = true;
            }
        }
    }
    if (!$included) {
        throw new InvalidArgumentException(
            sprintf('Class with name %1$s not found. Looked in %2$s.',
                $className,
                $path
            )
        );
    }

});

function startsWith($value, $prefix)
{
    $length = strlen($prefix);
    return substr($value, 0, $length) === $prefix;
}

?>

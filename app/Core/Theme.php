<?php

namespace App\Core;

class Theme
{
    public static bool $modeSwitchEnabled = false;
    public static string $modeDefault = 'light';

    public static string $direction = 'ltr';

    public static array $htmlAttributes = [];
    public static array $htmlClasses = [];


    /**
     * Page level assets
     */
    public static array $javascriptFiles = [];
    public static array $cssFiles = [];
    public static array $vendorFiles = [];

    /**
     * Add HTML attributes by scope.
     */
    function addHtmlAttribute(mixed $scope, mixed  $name, mixed  $value): void
    {
        self::$htmlAttributes[$scope][$name] = $value;
    }

    /**
     * Add multiple HTML attributes by scope.
     */
    function addHtmlAttributes(mixed $scope, mixed  $attributes): void
    {
        foreach ($attributes as $key => $value) {
            self::$htmlAttributes[$scope][$key] = $value;
        }
    }

    /**
     * Add HTML class by scope.
     */
    function addHtmlClass(mixed $scope, mixed  $value): void
    {
        self::$htmlClasses[$scope][] = $value;
    }

    /**
     * Remove HTML class by scope.
     */
    function removeHtmlClass(mixed $scope, mixed $value): void
    {
        $key = array_search($value, self::$htmlClasses[$scope]);
        unset(self::$htmlClasses[$scope][$key]);
    }

    /**
     * Print HTML attributes for the HTML template.
     */
    function printHtmlAttributes(mixed $scope): string
    {
        $attributes = [];
        if (isset(self::$htmlAttributes[$scope])) {
            foreach (self::$htmlAttributes[$scope] as $key => $value) {
                $attributes[] = sprintf('%s="%s"', $key, $value);
            }
        }

        return join(' ', $attributes);
    }

    /**
     * Print HTML classes for the HTML template.
     */
    function printHtmlClasses(mixed $scope, bool $full = true): string
    {
        if (empty(self::$htmlClasses)) {
            return '';
        }

        $classes = [];
        if (isset(self::$htmlClasses[$scope])) {
            $classes = self::$htmlClasses[$scope];
        }

        if ($full) {
            return sprintf('class="%s"', implode(' ', (array) $classes));
        }

        return $classes;
    }

    /**
     * Get SVG icon content.
     */
    function getSvgIcon(string $path, string $classNames = 'svg-icon'): string
    {
        if (file_exists(public_path('assets/media/icons/' . $path))) {
            return sprintf('<span class="%s">%s</span>', $classNames, file_get_contents(public_path('assets/media/icons/' . $path)));
        }

        return '';
    }

    /**
     * Set dark mode enabled status.
     */
    function setModeSwitch(bool $flag): void
    {
        self::$modeSwitchEnabled = $flag;
    }

    /**
     * Check dark mode status.
     */
    function isModeSwitchEnabled(): bool
    {
        return self::$modeSwitchEnabled;
    }

    /**
     * Set the mode to dark or light.
     */
    function setModeDefault(string $mode): void
    {
        self::$modeDefault = $mode;
    }

    /**
     * Get current mode.
     */
    function getModeDefault(): string
    {
        return self::$modeDefault;
    }

    /**
     * Set style direction.
     */
    function setDirection(string $direction): void
    {
        self::$direction = $direction;
    }

    /**
     * Get style direction.
     */
    function getDirection(): string
    {
        return self::$direction;
    }

    /**
     * Check if style direction is RTL.
     */
    function isRtlDirection(): bool
    {
        return self::$direction === 'rtl';
    }

    /**
     * Include favicon from settings.
     */
    function includeFavicon(): string
    {
        return sprintf(
            '<link rel="shortcut icon" href="%s" />',
            asset(config('settings.theme.assets.favicon'))
        );
    }

    /**
     * Include the fonts from settings.
     */
    function includeFonts(): string
    {
        $content = '';

        foreach (config('settings.theme.assets.fonts') as $url) {
            $content .= sprintf('<link rel="stylesheet" href="%s">', asset($url));
        }

        return $content;
    }

    /**
     * Get the global assets.
     */
    function getGlobalAssets(string $type = 'js'): array
    {
        return config('settings.theme.assets.global.' . $type);
    }

    /**
     * Add multiple vendors to the page by name. 
     */
    function addVendors(array $vendors): array
    {
        foreach ($vendors as $value) {
            self::$vendorFiles[] = $value;
        }

        return array_unique(self::$vendorFiles);
    }

    /**
     * Add single vendor to the page by name. 
     */
    function addVendor(string $vendor): void
    {
        self::$vendorFiles[] = $vendor;
    }

    /**
     * Add custom javascript file to the page
     */
    function addJavascriptFile(string $file): void
    {
        self::$javascriptFiles[] = $file;
    }

    /**
     * Add custom CSS file to the page
     */
    function addCssFile(string $file): void
    {
        self::$cssFiles[] = $file;
    }

    /**
     * Get vendor files from settings. 
     */
    function getVendors(mixed $type): array
    {
        $files = [];
        foreach (self::$vendorFiles as $vendor) {
            $vendors = config('settings.theme.vendors.' . $vendor);
            if (isset($vendors[$type])) {
                foreach ($vendors[$type] as $path) {
                    $files[] = $path;
                }
            }
        }

        return array_unique($files);
    }

    /**
     * Get custom js files from the settings.
     */
    function getCustomJs(): array
    {
        return self::$javascriptFiles;
    }

    /**
     * Get custom css files from the settings.
     */
    function getCustomCss(): array
    {
        return self::$cssFiles;
    }

    /**
     * Get HTML attribute based on the scope
     */
    function getHtmlAttribute(mixed $scope, mixed $attribute): array
    {
        return self::$htmlAttributes[$scope][$attribute] ?? [];
    }

    /**
     * Render the icon markup.
     * 
     * @param string<duotone|solid|outline> $type  Icon style.
     */
    function getIcon(string $name, string  $class = '', string  $type = ''): string
    {
        $type = config('settings.theme.icons', 'duotone');
        $tag = 'span';

        if ($type === 'duotone') {
            $icons = cache()->remember('duotone-icons', 3600, function () {
                return json_decode(file_get_contents(public_path('icons.json')), true);
            });

            $pathsNumber = data_get($icons, 'duotone-paths.' . $name, 0);
            $output = '<' . $tag . ' class="ki-' . $type . ' ki-' . $name . (!empty($class) ? " " . $class : '') . '">';

            for ($i = 0; $i < $pathsNumber; $i++) {
                $output .= '<' . $tag . ' class="path' . ($i + 1) . '"></' . $tag . '>';
            }

            $output .= '</' . $tag . '>';
        } else {
            $output = '<' . $tag . ' class="ki-' . $type . ' ki-' . $name . (!empty($class) ? " " . $class : '') . '"></' . $tag . '>';
        }

        return $output;
    }
}

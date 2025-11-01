<?php

namespace App\Services\Trip;

use App\Attributes\TripStrategy;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use function dump;

class TripStrategyDiscovery
{
    private array $strategies = [];

    public function discover(string $directory): void
    {
        $finder = new Finder();
        $finder->files()->in($directory)->name('*.php');

        foreach ($finder as $file) {
            $className = $this->getClassNameFromFile($file->getRealPath());

            if (!$className || !class_exists($className)) {
                continue;
            }

            try {
                $reflection = new ReflectionClass($className);

                // ✅ تحقق إنه مش interface ومش abstract
                if ($reflection->isInterface() || $reflection->isAbstract()) {
                    continue;
                }

                // ✅ تحقق إنه بينفذ الـ TripCostStrategy interface
                if (!$reflection->implementsInterface(TripCostStrategy::class)) {
                    continue;
                }

                // جيب الـ attributes
                $attributes = $reflection->getAttributes(TripStrategy::class);

                if (empty($attributes)) {
                    continue;
                }

                // ✅ دلوقتي آمن نعمل newInstance
                /** @var TripStrategy $attribute */
                $attribute = $attributes[0]->newInstance();
                $this->strategies[$attribute->type] = $className;

            } catch (\ReflectionException $e) {
                // تجاهل الـ classes اللي مش ينفع نعمل لها reflection
                continue;
            }
        }
    }

    public function getStrategies(): array
    {
        if (empty($this->strategies)) {
            // register default strategies if none discovered
            $this->discover(__DIR__);
        }
        return $this->strategies;
    }

    private function getClassNameFromFile(string $file): ?string
    {
        $content = file_get_contents($file);

        // استخرج الـ namespace والـ class name
        $namespace = '';
        $className = '';

        if (preg_match('/namespace\s+([^;]+);/i', $content, $namespaceMatch)) {
            $namespace = trim($namespaceMatch[1]);
        }

        if (preg_match('/^(?:abstract\s+)?(?:final\s+)?class\s+(\w+)/im', $content, $classMatch)) {
            $className = $classMatch[1];
        }

        if ($namespace && $className) {
            return $namespace . '\\' . $className;
        }

        return null;
    }
}
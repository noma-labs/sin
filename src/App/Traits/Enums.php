<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// https://gist.github.com/jhoff/b68dc2ac0e106a68488475e8430b38dc

trait Enums
{
    /**
     * Enum property getter
     *
     * @return mixed|false
     */
    public static function getEnum(string $field)
    {
        $instance = new self;
        if ($instance->hasEnumProperty($field)) {
            $property = $instance->getEnumProperty($field);

            return $instance->$property;
        }

        return false;
    }

    /**
     * Check for the presence of a property that starts
     *     with enum for the provided attribute
     *
     * @param  string  $field
     * @return $this
     * @throws Exception
     */
    public function setAttribute($field, mixed $value)
    {
        if ($this->hasEnumProperty($field)) {
            if (! $this->isValidEnum($field, $value)) {
                throw new Exception('Invalid value for '.static::class."::$field ($value)");
            }
            if ($this->isKeyedEnum($field, $value)) {
                $value = $this->getKeyedEnum($field, $value);
            }
        }

        return parent::setAttribute($field, $value);
    }

    /**
     * Gets the expected enum property
     */
    protected function getEnumProperty(string $field): string
    {
        // return 'enum' . Str::plural(Str::studly($field));
        return 'enum'.Str::studly($field);

    }

    /**
     * Gets the enum value by key
     *
     * @return mixed
     */
    protected function getKeyedEnum(string $field, mixed $key)
    {
        return static::getEnum($field)[$key];
    }

    /**
     * Is an enum property defined for the provided field
     */
    protected function hasEnumProperty(string $field): bool
    {
        $property = $this->getEnumProperty($field);

        return isset($this->$property) && is_array($this->$property);
    }

    /**
     * Is the provided value a key in the enum
     */
    protected function isKeyedEnum(string $field, mixed $key): bool
    {
        return in_array($key, array_keys(static::getEnum($field)), true);
    }

    /**
     * Is the value a valid enum in any way
     */
    protected function isValidEnum(string $field, mixed $value): bool
    {
        return $this->isValueEnum($field, $value) ||
            $this->isKeyedEnum($field, $value);
    }

    /**
     * Is the provided value in the enum
     */
    protected function isValueEnum(string $field, mixed $value): bool
    {
        return in_array($value, static::getEnum($field));
    }

    public static function getPossibleEnumValues(string $name, string $table): array
    {
        $expression = DB::raw('SHOW COLUMNS FROM '.$table.' WHERE Field = "'.$name.'"');

        $type = DB::select($expression->getValue(DB::connection()->getQueryGrammar()))[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $enum = [];
        foreach (explode(',', $matches[1]) as $value) {
            $v = trim($value, "'");
            $enum[] = $v;
        }

        return $enum;
    }
}

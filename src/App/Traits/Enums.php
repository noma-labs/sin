<?php

namespace App\Traits;

use App\Exceptions\InvalidEnumException;
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
     * @param string $field
     * @param mixed $value
     * @return $this
     *
     * @throws InvalidEnumException
     */
    public function setAttribute($field, $value)
    {
        if ($this->hasEnumProperty($field)) {
            if (!$this->isValidEnum($field, $value)) {
                throw new InvalidEnumException('Invalid value for ' . static::class . "::$field ($value)");
            }
            if ($this->isKeyedEnum($field, $value)) {
                $value = $this->getKeyedEnum($field, $value);
            }
        }

        return parent::setAttribute($field, $value);
    }

    /**
     * Gets the expected enum property
     *
     * @return string
     */
    protected function getEnumProperty(string $field)
    {
        // return 'enum' . Str::plural(Str::studly($field));
        return 'enum' . Str::studly($field);

    }

    /**
     * Gets the enum value by key
     *
     * @param mixed $key
     * @return mixed
     */
    protected function getKeyedEnum(string $field, $key)
    {
        return static::getEnum($field)[$key];
    }

    /**
     * Is an enum property defined for the provided field
     *
     * @return bool
     */
    protected function hasEnumProperty(string $field)
    {
        $property = $this->getEnumProperty($field);

        return isset($this->$property) && is_array($this->$property);
    }

    /**
     * Is the provided value a key in the enum
     *
     * @param mixed $key
     * @return bool
     */
    protected function isKeyedEnum(string $field, $key)
    {
        return in_array($key, array_keys(static::getEnum($field)), true);
    }

    /**
     * Is the value a valid enum in any way
     *
     * @param mixed $value
     * @return bool
     */
    protected function isValidEnum(string $field, $value)
    {
        return $this->isValueEnum($field, $value) ||
            $this->isKeyedEnum($field, $value);
    }

    /**
     * Is the provided value in the enum
     *
     * @param mixed $value
     * @return bool
     */
    protected function isValueEnum(string $field, $value)
    {
        return in_array($value, static::getEnum($field));
    }

    /**
     * ritorna i possibili valori di un enum
     *
     * @param nome colonna enum
     * @param nome tabella nel db
     *
     * @author Matteo Neri
     **/
    public static function getPossibleEnumValues($name, $table)
    {
        $type = DB::select(DB::raw('SHOW COLUMNS FROM ' . $table . ' WHERE Field = "' . $name . '"'))[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $enum = [];
        foreach (explode(',', $matches[1]) as $value) {
            $v = trim($value, "'");
            $enum[] = $v;
        }

        return $enum;
    }
}

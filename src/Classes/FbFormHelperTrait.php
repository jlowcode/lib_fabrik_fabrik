<?php

namespace Fabrik\Library\Fabrik\Classes;

use Joomla\CMS\Factory;

trait FbFormHelperTrait
{

    public static function loadFieldType($type, $new = true)
    {
        return static::loadType('field', $type, $new);
    }

    protected static function loadType($entity, $type, $new = true)
    {
        $container = Factory::getContainer();

        // Reference to an array with current entity's type instances
        $types = &self::$entities[$entity];

        $key = md5($type);

        // Return an entity object if it already exists and we don't need a new one.
        if (isset($types[$key]) && $new === false) {
            return $types[$key];
        }

        $class = self::loadClass($entity, $type);

        if ($class === false) {
            return false;
        }

 	   // Instantiate a new type object.
       if ($container->has($class)) {
	        // Use the container class which may have been overriden
	        $types[$key] = new $container->get($class);
        } else {
        	// Normal flow
	        $types[$key] = new $class();
        }

        return $types[$key];
    }

}

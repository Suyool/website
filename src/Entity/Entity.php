<?php
namespace App\Entity;

use App\Utils\Helper;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class Entity
{
    public function __construct()
    {

    }

    /**
     * Assign entity properties using an array
     *
     * @param array $attributes assoc array of values to assign
     */
    public function fromArray(array $attributes)
    {
        foreach ($attributes as $name => $value) {
            $name = (new Helper())->dashesToCamelCase($name);
            if (property_exists($this, $name)) {
                $methodName = $this->_getSetterName($name);
                if ($methodName) {
                    $this->{$methodName}($value);
                } else {
                    $this->$name = $value;
                }
            }
        }
    }

    public function fromObject($obj) {
        $attributes = get_object_vars($obj);
        foreach ($attributes as $name => $value) {
            if (property_exists($this, $name)) {
                $methodName = $this->_getSetterName($name);
                if ($methodName) {
                    $this->{$methodName}($value);
                } else {
                    $this->$name = $value;
                }
            }
        }

        return $this;
    }

    public function fillEmptyValues() {
        $attributes = get_object_vars($this);
        foreach ($attributes as $name => $value) {
            if ($name != "id" && $name != "notificationDate" && $value == null) {
                $this->$name = "";
            }
        }

        return $this;
    }

    public function convertToObject($array)
    {
        $hydratedObjectArray = [];
        foreach ($array as $key=>$elm) {
            $name = get_called_class();
            $object = new $name;
            $object->fromArray($elm);
            $hydratedObjectArray[$key] = $object;
        }

        return $hydratedObjectArray;
    }

    public function convertJoinToObject($array,$childObjectName,$variableName,$manyToOne = false)
    {
        $hydratedObjectArray = [];
        foreach ($array as $key=>$elm) {
            $name = get_called_class();
            $childObjectPath = "App\\Entity\\".$childObjectName;
            $childObject = new $childObjectPath;
            if(!$manyToOne)
                $elm[$variableName] = $childObject->convertToObject($elm[$variableName]);
            else if($elm[$variableName] != null) {
                $tempObj = $childObject;
                $tempObj->fromArray($elm[$variableName]);
                $elm[$variableName] = $tempObj;
            }

            $object = new $name;
            $object->fromArray($elm);
            $hydratedObjectArray[$key] = $object;
        }

        return $hydratedObjectArray;
    }
    /**
     * Get property setter method name (if exists)
     *
     * @param string $propertyName entity property name
     * @return false|string
     */
    protected function _getSetterName($propertyName)
    {
        $prefixes = array('add', 'set');

        foreach ($prefixes as $prefix) {
            $methodName = sprintf('%s%s', $prefix, ucfirst(strtolower($propertyName)));

            if (method_exists($this, $methodName)) {
                return $methodName;
            }
        }
        return false;
    }
}
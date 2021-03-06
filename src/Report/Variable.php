<?php
/**
 * Variable
 *
 * @package     Rootwork\Report
 * @copyright   Copyright (c) 2017 Rootwork InfoTech LLC
 * @license     MIT
 * @author      Mike Soule <mike@rootwork.it>
 * @filesource
 */

namespace Rootwork\Report;

/**
 * Class Variable
 *
 * @package Rootwork\Report
 */
class Variable implements \JsonSerializable
{

    /**
     * Type constants
     */
    const TYPE_CHECKBOX        = 'checkbox';
    const TYPE_DATE            = 'date';
    const TYPE_NUMBER          = 'number';
    const TYPE_SELECT          = 'select';
    const TYPE_SELECT_MULTIPLE = 'select-multiple';
    const TYPE_TEXT            = 'text';

    /**
     * The variable name.
     *
     * @var string
     */
    protected $name;

    /**
     * The variable display name.
     *
     * @var string
     */
    protected $display;

    /**
     * The variable type.
     *
     * @var string
     */
    protected $type;

    /**
     * Options for variables with constrained values.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The default value.
     *
     * @var mixed
     */
    protected $default;

    /**
     * Optional formatting string.
     *
     * @var string
     */
    protected $format;

    /**
     * The current value of the variable.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Description of the variable.
     *
     * @var string|null
     */
    protected $description = null;

    /**
     * Reserved words that cannot be used as variable names.
     *
     * @var array
     */
    protected $reserved = ['limit', 'order', 'page'];

    /**
     * Variable constructor.
     *
     * @param string      $name
     * @param string      $display
     * @param string      $type
     * @param mixed|null  $default
     * @param array       $options
     * @param string      $format
     * @param string|null $description
     */
    public function __construct(
        $name,
        $display,
        $type,
        $default = null,
        array $options = [],
        $format = null,
        $description = null
    ) {
        $this->setName($name)
            ->setDisplay($display)
            ->setType($type)
            ->setDefault($default)
            ->setOptions($options)
            ->setFormat($format)
            ->setDescription($description);

        $this->value = $this->getDefault();
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $reserved = array_map('strtolower', $this->reserved);

        if (in_array(strtolower($name), $reserved)) {
            throw new \InvalidArgumentException("Variable cannot be named '$name' because that is a reserved word");
        }

        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the display name.
     *
     * @param string $display
     *
     * @return $this
     */
    public function setDisplay($display)
    {
        $this->display = $display;
        return $this;
    }

    /**
     * Get the display name.
     *
     * @return string
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $types = [
            self::TYPE_DATE,
            self::TYPE_NUMBER,
            self::TYPE_SELECT,
            self::TYPE_SELECT_MULTIPLE,
            self::TYPE_TEXT,
        ];

        if (!in_array($type, $types)) {
            throw new \InvalidArgumentException("Invalid variable type: '$type'");
        }

        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the options for variables with constrained values.
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options = [])
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the default value.
     *
     * @param mixed $default
     *
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Set the format.
     *
     * @param string $format
     *
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set the variable description.
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the variable description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value.
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        switch ($this->getType()) {
            case self::TYPE_CHECKBOX:
                $this->value = boolval($value);
                break;
            case self::TYPE_DATE:
                $this->value = $this->getValidDate($value);
                break;
            case self::TYPE_SELECT:
            case self::TYPE_SELECT_MULTIPLE:
                $this->value = $this->getValidSelect($value);
                break;
            default:
                $this->value = $value;
        }

        return $this;
    }

    /**
     * Get the value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Validate a date value.
     *
     * @param string $value
     *
     * @return bool
     */
    protected function getValidDate($value)
    {
        if (!$this->format) {
            throw new \UnexpectedValueException("A format is required for date variables");
        }

        $date = \DateTime::createFromFormat($this->format, $value);

        if (!$date || $date->format($this->format) != $value) {
            throw new \InvalidArgumentException("Invalid value given for $this->name: '$value'");
        }

        return $value;
    }

    /**
     * Validate a select or multiple-select value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function getValidSelect($value)
    {
        $selected = (array) $value;

        foreach ($selected as $item) {
            if (!array_key_exists($item, $this->getOptions())) {
                throw new \InvalidArgumentException("Invalid value given for $this->name: '$item'");
            }
        }

        return $value;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'name'        => $this->getName(),
            'display'     => $this->getDisplay(),
            'type'        => $this->getType(),
            'default'     => $this->getDefault(),
            'options'     => $this->getOptions(),
            'format'      => $this->getFormat(),
            'description' => $this->getDescription(),
        ];
    }
}

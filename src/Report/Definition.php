<?php
/**
 * Definition
 *
 * @package     Rootwork\Report
 * @copyright   Copyright (c) 2017 Rootwork InfoTech LLC
 * @license     MIT
 * @author      Mike Soule <mike@rootwork.it>
 * @filesource
 */

namespace Rootwork\Report;

/**
 * Report definition class.
 *
 * Parses a JSON report definition.
 *
 * @package Rootwork\Report
 */
class Definition implements \JsonSerializable
{

    /**
     * The report title.
     *
     * @var string
     */
    protected $title;

    /**
     * Column definitions.
     *
     * @var Column[]
     */
    protected $columns = [];

    /**
     * @var Variable[]
     */
    protected $variables = [];

    /**
     * Set the report title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add a column.
     *
     * @param Column $column
     *
     * @return $this
     */
    public function addColumn(Column $column)
    {
        $this->columns[] = $column;
        return $this;
    }

    /**
     * Set the column definitions.
     *
     * @param Column[] $columns
     *
     * @return $this
     */
    public function setColumns(array $columns = [])
    {
        $this->columns = [];

        foreach ($columns as $column) {
            $this->addColumn($column);
        }

        return $this;
    }

    /**
     * @return Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Add a variable.
     *
     * @param Variable $variable
     *
     * @return $this
     */
    public function addVariable(Variable $variable)
    {
        $this->variables[] = $variable;
        return $this;
    }

    /**
     * Set the variable definitions.
     *
     * @param Variable[] $variables
     *
     * @return $this
     */
    public function setVariables(array $variables = [])
    {
        $this->variables = [];

        foreach ($variables as $variable) {
            $this->addVariable($variable);
        }

        return $this;
    }

    /**
     * Get a variable by name.
     *
     * @param string $name
     *
     * @return null|Variable
     */
    public function getVariable($name)
    {
        foreach ($this->variables as $variable) {
            if ($variable->getName() == $name) {
                return $variable;
            }
        }

        return null;
    }

    /**
     * @return Variable[]
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Set variable values from an array.
     *
     * @param array $values
     *
     * @return $this
     */
    public function setVariableValues(array $values)
    {
        foreach ($this->variables as $variable) {
            if (array_key_exists($variable->getName(), $values)) {
                $variable->setValue($values[$variable->getName()]);
            }
        }

        return $this;
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
            'title'     => $this->getTitle(),
            'columns'   => $this->getColumns(),
            'variables' => $this->getVariables(),
        ];
    }
}

<?php
/**
 * ReportInterface
 *
 * @package     Rootwork\Report
 * @copyright   Copyright (c) 2017 Rootwork InfoTech LLC
 * @license     MIT
 * @author      Mike Soule <mike@rootwork.it>
 * @filesource
 */

namespace Rootwork\Report;


interface ReportInterface
{

    /**
     * Run the report and return results.
     *
     * @return array[]
     */
    public function run();
}
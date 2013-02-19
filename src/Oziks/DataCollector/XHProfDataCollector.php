<?php

/*
 * This file is part of the oziks/XHProfServiceProvider.
 *
 * (c) Morgan Brunot <brunot.morgan@gmail.com>
 */

namespace Oziks\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;

use Oziks\Lib\XHProfRun;

/**
 * XHProfDataCollector.
 *
 * @author Morgan Brunot <brunot.morgan@gmail.com>
 */
class XHProfDataCollector extends DataCollector
{
    protected $xhprof;

    public function __construct(XHProfRun $xhprof)
    {
        $this->xhprof = $xhprof;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        // ended xhprof run process
        $this->xhprof->end();

        $this->updateReport();
        $this->updateReports();
    }

    /**
     * Gets the current report.
     *
     * @return array The current report
     */
    public function getReport()
    {
        return $this->data['current_report'];
    }

    /**
     * Updates the current report data.
     */
    public function updateReport()
    {
        $this->data['current_report'] = $this->xhprof->getReport();
    }

    /**
     * Gets the last reports.
     *
     * @return array The last reports
     */
    public function getReports()
    {
        return $this->data['reports'];
    }

    /**
     * Updates the last reports data.
     */
    public function updateReports()
    {
        $this->data['reports'] = $this->xhprof->getReports();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'xhprof';
    }
}

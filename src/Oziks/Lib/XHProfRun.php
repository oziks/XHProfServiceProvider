<?php

/*
 * This file is part of the oziks/XHProfServiceProvider.
 *
 * (c) Morgan Brunot <brunot.morgan@gmail.com>
 */

namespace Oziks\Lib;

use Symfony\Component\Finder\Finder;

use Exception;
use SplFileInfo;
use XHProfRuns_Default;

/**
 * XHProfRun.
 *
 * @author Morgan Brunot <brunot.morgan@gmail.com>
 */
class XHProfRun
{
    protected
        $dir,
        $namespace,
        $id,
        $data,
        $date,
        $host,
        $started = false;

    public function __construct($dir, $host)
    {
        if (!file_exists($dir)) {
            throw new Exception("This directory does not exist.", 1);
        }

        $this->dir = $dir;
        $this->host = $host;
    }

    /**
     * Start run
     */
    public function start($namespace = 'xhprof')
    {
        if (!extension_loaded('xhprof')) {
            throw new Exception("XHProf extension is not loaded.", 1);
        }

        if (!$this->started) {
            xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
            $this->namespace = $namespace;
            $this->started = true;
        }

        return $this;
    }

    /**
     * Triggers the end of the run
     *
     * @return boolean True if the run is started before call end method
     */
    public function end()
    {
        if (!extension_loaded('xhprof')) {
            throw new Exception("XHProf extension is not loaded.", 1);
        }

        if ($this->started) {
            $this->data = xhprof_disable();

            require_once(sprintf('%s/xhprof_lib/utils/xhprof_runs.php', $this->dir));
            $xhprof_runs = new XHProfRuns_Default(ini_get("xhprof.output_dir"));
            $this->id = $xhprof_runs->save_run($this->data, $this->namespace);
        }

        return $this->started;
    }

    /**
     * Gets the current report.
     *
     * @return array The current report
     */
    public function getReport($id = null)
    {
        if ($id === null) {
            $id = $this->id;
        }

        return array('runId' => $id, 'report' => sprintf('%s?run=%s&source=%s', $this->host, $id, $this->namespace));
    }

    /**
     * Gets the last reports.
     *
     * @return array The last reports
     */
    public function getReports()
    {
        $reports = array();
        $dir = ini_get("xhprof.output_dir");

        if (is_dir($dir)) {
            $finder = new Finder();
            $finder
                ->files()
                ->name('*.'.$this->namespace)
                ->notName($this->id.'.'.$this->namespace)
                ->in($dir)
                ->sort(function (SplFileInfo $a, SplFileInfo $b) {
                    return strcmp($b->getRealpath(), $a->getRealpath());
                })
            ;

            foreach ($finder as $runFile) {
                $infos = explode('.', basename($runFile));
                $runId = array_shift($infos);

                $reports[] = $this->getReport($runId);

                if (count($reports) > 5) {
                    break;
                }
            }
        }

        return $reports;
    }
}

<?php

namespace Classes\Utils;

/**
 * Class for logging of execute timings for our console scripts
 *
 * @author Ken Depelchin <ken.depelchin@gmail.com>
 */
class Timing {

    private $now;

    private $start;
    private $total;

    /**
     * Format the current time
     *
     * @return string
     */
    public function getNowFormatted() {
        return date('D jS F Y G:i:s', $this->now);
    }

    public function start() {
        $this->now = time();
        $this->start = microtime(true);
    }

    public function stop() {
        $this->setTotal(round((microtime(true) - $this->start), 3));
    }

    public function setTotal($total) {
        $this->total = $total;
    }

    public function getTotal() {
        return $this->total;
    }

    public function __toString() {
        if (!$this->getTotal()) {
            return "<error>Forgot to stop the timer?</error>";
        }

        return "<info>Script took " . $this->getTotal() . " seconds.</info>";
    }
}

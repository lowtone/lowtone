<?php
namespace lowtone\util\profiling;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\util\profiling
 */
class Timer {
	
	private $itsStart = 0,
		$itsTime = 0,
		$itsSplit = array(),
		$itsLastSplit = 0,
		$itsState = self::STATE_STOPPED;
		
	const REPORT_FORMAT = '<span style="font-family: monospace;">Total: <span style="color: #f57900;">%s</span>s, Leap: <span style="color: #f57900;">%s</span>s (%s)</span>';
	
	const STATE_STOPPED = 0,
		STATE_STARTED = 1;
	
	/**
	 * Start the timer.
	 * @return Timer Returns the Timer object on success.
	 */
	public function start($start = NULL) {
		$timer = (isset($this) && $this instanceof Timer) ? $this : self::create();
		
		$timer->itsStart = is_numeric($start) ? $start : microtime(true);
		
		$timer->setState(self::STATE_STARTED);
		
		return $timer;
	}
	
	/**
	 * Add a split value.
	 * @param int $leap A reference to a variable that will hold the number of 
	 * milliseconds since the last splitvalue.
	 * @return int Returns the number of milliseconds since the timer started.
	 */
	public function split(&$leap = NULL) {
		$time = $this->getCurrentTime();
		$leap = ($time - (float) end($this->itsSplit));
		
		$this->itsSplit[] = $time;
		
		return $time;
	}
	
	/**
	 * Stop the timer.
	 * @return int Returns the time in milliseconds since the timer started.
	 */
	public function stop() {
		$time = $this->getTime();
		
		$this->setState(self::STATE_STOPPED);
		
		return $time;
	}
	
	/**
	 * Generate output for a report.
	 * @param string $message An optional additional message.
	 */
	public function report($message = "") {
		$time = $this->split($leap);
		
		echo sprintf(self::REPORT_FORMAT, number_format($time, 10, ".", ""), number_format($leap, 10, ".", ""), $message) . "<br />\n";
	}
	
	/**
	 * Get the number of milliseconds since the timer started.
	 * @return int Returns the number of milliseconds since the timer started.
	 */
	private function getCurrentTime() {
		return (microtime(true) - $this->itsStart);
	}
	
	/**
	 * Get the elapsed time.
	 * @return int Returns the time elapsed from when the timer was started 
	 * until it was stopped.
	 */
	public function getTime() {
		if ($this->itsState == self::STATE_STARTED)
			$this->itsTime = $this->getCurrentTime();
		
		return $this->itsTime;
	}
	
	/**
	 * Change the state for the timer.
	 * @param int $state The new state for the timer.
	 */
	private function setState($state) {
		$this->itsState = $state;
	}
	
	/**
	 * Inline creation of a timer object.
	 * @return Timer Returns a new Timer object.
	 */
	public static function create() {
		return new Timer();
	}
	
}
?>
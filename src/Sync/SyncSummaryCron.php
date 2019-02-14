<?php

namespace iLUB\Plugins\CleanUpSessions\Sync;

/**
 * Class SyncSummaryCron
 *
 * @package iLUB\Plugins\CleanUpSessions\Sync
 */
class SyncSummaryCron {

	/**
	 * @return string
	 */
	public function getOutputAsString() {
		$return = "";
		foreach ($this->syncs as $sync) {
			$return .= $this->renderOneSync($sync) . "\n\n";
		}

		return $return;
	}


	/**
	 * @return string
	 */
	private function renderOneSync() {
		// TODO: create nice summary message

		$msg = "message";

		return $msg;
	}
}

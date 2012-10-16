<?php

class Reviser {
	protected $rootEntry = null;

	function __construct($rootEntry = '') {
        $this->rootEntry = $rootEntry;
    }

	//$entryId is start with todoEntry, and indics in each sub entry levels
	//concatenated with underline
	public function getExactEntry($entryId) {
		$indics = explode('_', $entryId);
		$entry = $this->rootEntry;
		$count = count($indics);
		for ($i=1; $i < $count; $i++) { 
			$index = $indics[$i];
			if (count($entry->subEntries)-1 < $index) return null;
			$entry = $entry->subEntries[$index];
		}

		return $entry;
	}

	public function setAttibutes($entryId, $attributes) {
		$entry = $this->getExactEntry($entryId);
		$entry->attributes = $attributes;
	}

	public function getRootEntry() {
		return $this->rootEntry;
	}
}

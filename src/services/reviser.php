<?php

include_once 'src/models/entry.php';
include_once 'src/utilities/util.php';

class Reviser {
	protected $rootEntry = null;

	function __construct($rootEntry = '') {
        $this->rootEntry = $rootEntry;
    }

	public function getRootEntry() {
		return $this->rootEntry;
	}

	//$entryId is start with todoEntry, and indics in each sub entry levels
	//concatenated with underline
	public function getExactEntry($entryId) {
		$indics = explode('_', $entryId);
		$entry = $this->rootEntry;
		$count = count($indics);
		for ($i=1; $i < $count; $i++) { 
			$index = $indics[$i];
			if (count($entry->subEntries)-1 < $index){
				$entry = null;
				break;
			}
			$entry = $entry->subEntries[$index];
		}

		if (!$entry || $entry == $this->rootEntry) {
			Util::addError('Cannot find entry with ID: '.$entryId);
			return new TutorialEntry();
		}

		return $entry;
	}
	
	public function setText($entryId, $text) {
		$text = trim($text);
		if (empty($text)) {
			Util::addError("Text cannot be empty!");
			return;
		}
		
		$entry = $this->getExactEntry($entryId);
		$entry->text = $text;
	}

	public function setLink($entryId, $link) {
		$entry = $this->getExactEntry($entryId);
		$entry->link = $link;
	}

	public function setDescription($entryId, $link) {
		$entry = $this->getExactEntry($entryId);
		$entry->description = $link;
	}

	public function setAttibutes($entryId, $attributes) {
		$entry = $this->getExactEntry($entryId);
		$entry->attributes = $attributes;
	}

	public function addSubEntries($entryId, $subContent) {
		$subEntries = $this->getEntriesFromContent($subContent);
		if (!$subEntries) {
			return null;
		}
		$entry = $this->getExactEntry($entryId);
		$entry->subEntries = array_merge($entry->subEntries, $subEntries);
		return $entry;
	}

	public function getEntriesFromContent($content) {
		$jsonEntries = json_decode($content);
		if (!$jsonEntries) {
			return null;
		}

		if (!is_array($jsonEntries)) {
			$jsonEntries = array($jsonEntries);
		}

		foreach ($jsonEntries as $jsonEntry) {
			if (!TutorialEntry::matchingProperties($jsonEntry)) {
				return null;
			}
		}

		return $jsonEntries;
	}


}

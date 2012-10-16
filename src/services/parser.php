<?php

include_once 'src/models/entry.php';
include_once 'src/untilities/util.php';

abstract class ParserBase {

	protected $homeURL = '';

	public function parse($content, $mainEntryText, $mainURL='', $homeURL='') {
		$doc = new DOMDocument();
		$doc->loadHTML($content);

		$mainEntry = new TodoEntry();
		$mainEntry->text = $mainEntryText;
		$mainEntry->link = $mainURL;
 		
 		$this->homeURL = $homeURL;

		//DOMDocument contains two children, the first child is HTML description
		//the second child is the real HTML content
		$this->analyze($doc->lastChild, $mainEntry);

		return $mainEntry;
	}	

	protected function analyze(DOMNode $node, TodoEntry $entry) {
		if ($node->nodeName == 'li') {
			$subEntry = $this->getEntry($node);
			if (!$subEntry) {
				Util::addError('No entry info find inside of list node');
			} else {
				//new foud entry to previous entry as sub
				$entry->subEntries[] = $subEntry;
				//make new entry as parent for rest of nodes
				$entry = $subEntry;
			}
		}

		if (!$node->childNodes) return;
		foreach ($node->childNodes as $child) {
			$this->analyze($child, $entry);
		}
	}

	abstract protected function getEntry(DOMNode $liNode);
}

class AndroidTutorialParser extends ParserBase {

 	protected function getEntry(DOMNode $liNode){
		$linkNode = $this->findFirstLinkNode($liNode);
		if (!$linkNode) return null;

		$textNode = $this->findTextNode($linkNode);
		if (!$textNode) return null;

		if ($textNode->nodeType != XML_TEXT_NODE) Util::addError('Not a XML_TEXT_NODE');

		$entry = new TodoEntry();

		$entry->link = $this->homeURL.$linkNode->attributes->getNamedItem('href')->nodeValue;
		$entry->text = trim($textNode->nodeValue);

		return $entry;
	}

	protected function findFirstLinkNode(DOMNode $node) {
		if ($node->nodeName == 'a') return $node;

		if (!$node->childNodes) return null;

		foreach ($node->childNodes as $child) {
			$elem = $this->findFirstLinkNode($child);
			if ($elem) return $elem;
		}
	}

	protected function findTextNode(DOMNode $linkNode) {
		$spanNodes = $linkNode->getElementsByTagName('span');
		if ($spanNodes->length > 0) return $spanNodes->item(0)->firstChild;

		return $linkNode->firstChild;
	}
}

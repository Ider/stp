<?php

include_once 'src/models/entry.php';
include_once 'src/utilities/util.php';

abstract class ParserBase {
	//most href of link tag is using relative url, so we need use home url to concatnate absolut url
	public $homeURL = ''; 

	public function parse($content) {
		$doc = new DOMDocument();
		$doc->loadHTML($content);

		$rootEntry = new TutorialEntry();

		//DOMDocument contains two children, the first child is HTML description
		//the second child is the real HTML content
		$this->traverse($doc->lastChild, $rootEntry);

		return $rootEntry;
	}	

	protected function traverse(DOMNode $node, TutorialEntry $entry) {
		if ($node instanceof DOMElement) $node->setAttribute('stp-traversed', '1');

		if ($this->containEntry($node)) {
			$subEntry = new TutorialEntry();
			$subDom = $this->loadEntry($node, $subEntry);
			if (empty($subEntry->text)) {
				Util::addError('No entry info find inside of node: '.$node->nodeName.'; with value: '.$node->nodeValue);
			} else {
				//new foud entry to previous entry as sub
				$entry->subEntries[] = $subEntry;
				//make new entry as parent for rest of nodes
				$entry = $subEntry;
				if (isset($subDom) && $subDom instanceof DOMNode) {
					$this->traverse($subDom, $entry);
					return;
				}
			}
		}
		if (!$node->hasChildNodes()) return;
		foreach ($node->childNodes as $child) {
			//Do not traverse twice
			// if (($node instanceof DOMElement)
			// 	&& !is_null($node->getAttribute('stp-traversed')))
			// 	continue;

			$this->traverse($child, $entry);
		}
	}

	/**
	 * Check if DOMNode contains entry information, 
	 * if true parse will pass this node to loadEntry method to get TutorialEntry
	 * @return bool 
	 */
	abstract protected function containEntry(DOMNode $node);
	/**
	 * Load TutorialEntry from the DOMNode, TutorialEntry information should be set on passed in argument
	 * @return DOMNode: DOMNode that would contain subEntries of this TutorialEntry, 
	 * if nothing returned, parser will keep traversing childNodes of current DOMNode
	 */
	abstract protected function loadEntry(DOMNode $node, TutorialEntry $entry);
}

class ListParser extends ParserBase {
	protected function containEntry(DOMNode $node) {
		return ($node->nodeType == XML_ELEMENT_NODE && $node->nodeName == 'li');
	}

	protected function loadEntry(DOMNode $node, TutorialEntry $entry) {
		$linkNode = $this->findLinkNode($node);
		if (!$linkNode) return null;

		$textNode = $this->findTextNode($linkNode);
		if (!$textNode) return null;

		$entry->link = $this->homeURL . $linkNode->getAttribute('href');
		$entry->text = trim($textNode->nodeValue);
		$siblingNode = $linkNode->nextSibling;
		if (isset($siblingNode) && $siblingNode->nodeType == XML_TEXT_NODE)
			$entry->description = trim($siblingNode->nodeValue);

		$lastNode = $node->lastChild;
		if (isset($lastNode) && $node->nodeName == 'ul') return $lastNode;

		return null;
	}

	protected function findLinkNode(DOMNode $node) {
		if ($node->nodeName == 'a') return $node;

		if (!$node->hasChildNodes()) return;
		foreach ($node->childNodes as $child) {
			$elem = $this->findLinkNode($child);
			if ($elem) return $elem;
		}

		return null;
	}

	protected function findTextNode(DOMNode $linkNode) {
		$spanNodes = $linkNode->getElementsByTagName('span');
		if ($spanNodes->length > 0) return $spanNodes->item(0)->firstChild;

		return $linkNode->firstChild;
	}
}

class DefinitionTermParser extends ParserBase {
	protected function containEntry(DOMNode $node) {
		return ($node->nodeType == XML_ELEMENT_NODE && $node->nodeName == 'dt');
	}

	protected function loadEntry(DOMNode $node, TutorialEntry $entry) {
		$linkNode = $this->findLinkNode($node);
		if (!$linkNode) return null;

		$textNode = $this->findTextNode($linkNode);
		if (!$textNode) return null;

		$entry->link = $this->homeURL . $linkNode->getAttribute('href');
		$entry->text = trim($textNode->nodeValue);
		$siblingNode = $linkNode->nextSibling;
		if (isset($siblingNode) && $siblingNode->nodeType == XML_TEXT_NODE)
			$entry->description = trim($siblingNode->nodeValue);

		$ddNode = $node->nextSibling;
		if (isset($ddNode) && $ddNode->nodeName == 'dd') return $ddNode;

		return null;
	}

	protected function findLinkNode(DOMNode $node) {
		if ($node->nodeName == 'a') return $node;

		if (!$node->hasChildNodes()) return;
		foreach ($node->childNodes as $child) {
			$elem = $this->findLinkNode($child);
			if ($elem) return $elem;
		}

		return null;
	}

	protected function findTextNode(DOMNode $linkNode) {
		$spanNodes = $linkNode->getElementsByTagName('span');
		if ($spanNodes->length > 0) return $spanNodes->item(0)->firstChild;

		return $linkNode->firstChild;
	}
}

/****************** Android Developer *******************/


Class AndroidDefinitionTermParser extends DefinitionTermParser {

	protected function loadEntry(DOMNode $node, TutorialEntry $entry) {
		$linkNode = $this->findLinkNode($node);
		if (!$linkNode) return null;

		$textNode = $linkNode->nextSibling->firstChild;
		if (!$linkNode) return null;

		$entry->link = $this->homeURL . $linkNode->getAttribute('name');
		$entry->text = trim($textNode->nodeValue);

		$ddNode = $node->nextSibling;
		if (isset($ddNode) && $ddNode->nodeName == 'dd') return $ddNode;

		return null;
	}
}


/****************** jQuery Api *******************/

class jQueryListParser extends ListParser {
	protected function findTextNode(DOMNode $linkNode) {
		return $linkNode->lastChild;
	}
}







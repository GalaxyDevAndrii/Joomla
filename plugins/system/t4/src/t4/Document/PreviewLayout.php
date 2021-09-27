<?php
namespace T4\Document;

use Joomla\CMS\Factory as JFactory;

class PreviewLayout extends Preview {

	public function renderRow(&$data) {
		$content = parent::renderRow($data);
		$name = $data['name'] ?? 'Section';
		$content .= "<div class=\"t4-preview t4-preview-section\"><span>Section: {$name}</span></div>";
		return $content;
	}

	public function renderContent ($data) {
		$content = parent::renderContent($data);
		$type = ucfirst($data['type'] ?? 'no-type');
		$name = $data['name'] ?? 'no-name';
		$content .= "<div class=\"t4-preview t4-preview-content t4-preview-{$type}\"><span>{$type}: {$name}</span></div>";
		return $content;
	}

	public function renderSingleCol($col) {
		$extra_class = empty($col['data']['extra_class']) ? '' : ' ' . $col['data']['extra_class'];
		return "<div class=\"t4-col {$extra_class}\">{$col['content']}</div>";
	}

	public function afterRender() {
		parent::afterRender();

		$app = JFactory::getApplication();
		$buffer = $app->getBody();

		// render head
		$head = $this->doc->getBuffer('head');
		$buffer = str_replace('{{head}}', $head, $buffer);

		$app->setBody($buffer);
	}

}

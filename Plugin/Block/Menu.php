<?php
/**
 * Cytracon
 *
 * This source file is subject to the Cytracon Software License, which is available at https://www.cytracon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.cytracon.com for more information.
 *
 * @category  Cytracon
 * @package   Cytracon_Core
 * @copyright Copyright (C) 2019 Cytracon (https://www.cytracon.com)
 */

namespace Cytracon\Core\Plugin\Block;

class Menu
{
	public function aroundRenderNavigation(
		\Magento\Backend\Block\Menu $menuBlock,
		callable $proceed,
		$menu, $level = 0, $limit = 0, $colBrakes = []
	) {

		foreach ($menu as $item) {
			if ($item->getId() == 'Cytracon_Core::extensions' && (!$item->hasChildren() || !$item->getChildren()->getFirstAvailable())) {
				$menu->remove('Cytracon_Core::extensions');
				break;
			}
		}
		$result = $proceed($menu, $level, $limit, $colBrakes);
		return $result;
	}
}
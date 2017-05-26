<?php
?>
<?php
class ARW_Megamenu_Block_Mainmenu_Menu extends Mage_Catalog_Block_Navigation
{
    protected $_column;

    protected function _construct()
    {
        parent::_construct();
    }

    protected function _renderCategoryMenuGroupItemHtml($category, $level = 0, $isLast = false, $isFirst = false,
                                                   $isOutermost = false, $outermostItemClass = '', $childrenWrapClass = '', $noEventAttributes = false)
    {
        if (!$category->getIsActive()) {
            return '';
        }
        $html = array();
		$max_level=Mage::getStoreConfig('megamenu/general/depth');
        // get all children
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = (array)$category->getChildrenNodes();
            $childrenCount = count($children);
        } else {
            $children = $category->getChildren();
            $childrenCount = $children->count();
        }
        $hasChildren = ($children && $childrenCount);

        // select active children
        $activeChildren = array();
        foreach ($children as $child) {
			if($level >= $max_level-1){break;}
            if ($child->getIsActive()) {
                $activeChildren[] = $child;
            }
        }
        $activeChildrenCount = count($activeChildren);
        $hasActiveChildren = ($activeChildrenCount > 0);
		$catdetail = Mage::getModel('catalog/category')->load($category->getId());
        $menutypes = $catdetail->getData('arwmenu_cat_groups');

        // Check if show category block if no sub-category
        $showblock = false;
        $showblock = $hasActiveChildren;
        if (Mage::helper('megamenu')->getCfg('menu/show_if_no_children')) {$showblock = true; }
        // prepare list item html classes
        $classes = array();
        $classes[] = $catdetail->getData('custom_classes');
        $classes[] = 'level' . $level;
        if(!$hasActiveChildren)
        {
            $classes[]='no-child';
        }
        if($level==1){
            $classes[] = 'groups item';
        }
        $classes[] = 'nav-' . $this->_getItemPosition($level);
        if ($this->isCategoryActive($category)) {
            $classes[] = 'active';
        }
        $linkClass = '';
        if ($isOutermost && $outermostItemClass) {
            $classes[] = $outermostItemClass;
            $linkClass = $outermostItemClass;
        }
        if ($isFirst) {
            $classes[] = 'first';
        }
        if($menutypes == 'drop_group'){
        	$classes[] = 'm-dropdown';
        }
        if($level == 0){
            $classes[] = 'menu-style-'. $menutypes;
        }
        if ($isLast) {
            $classes[] = 'last';
        }
        if ($hasActiveChildren && $level!=1) {
            $classes[] = 'parent';
        }
        if ($level==0 && $showblock) {
            $classes[] = 'parent';
        }

        // prepare list item attributes
        $attributes = array();
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }
        if ($hasActiveChildren && !$noEventAttributes) {
            $attributes['onmouseover'] = 'toggleMenu(this,1)';
            $attributes['onmouseout'] = 'toggleMenu(this,0)';
        }

        // assemble list item with attributes

        $htmlLi = '<li';
        foreach ($attributes as $attrName => $attrValue) {
            $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
        }
        $htmlLi .= '>';
        $html[] = $htmlLi;
        if ($level == 1 && $showblock){
            if($this->_getCatBlock($catdetail, 'arwmenu_cat_block_top')){
                $html[] = '<div class="arwmenu-block arwmenu-block-level1-top std">';
                $html[] = $this->_getCatBlock($catdetail, 'arwmenu_cat_block_top');
                $html[] = '</div>';
            }
        }
        $title_group='';
        if($menutypes=="drop_group"||$menutypes="group")
        {
            if($level==1)
            {
                $title_group = "title-group";
            }
        }
        $html[] = '<a class="'.$title_group . $linkClass .'" href="'.$this->getCategoryUrl($category).'">';
        $labelCategory = $this->_getCategoryLabelHtml($catdetail, $level);
		$category_icon= $this->_getCategoryIconHtml($catdetail, $level);
        $html[] = '<span>' . $category_icon.$labelCategory . $this->escapeHtml($category->getName()).'</span>';
        $html[] = '</a>';
        if($level == 1){
            if($imageUrl=$this->_getCategoryImageHtml($catdetail,$level))
            {
                $html[]= '<a href="'.$this->getCategoryUrl($category).'" class="category_image"><img src="'.$imageUrl.'" alt=""/></a>';
            }
        }
        if ($level == 0) {
            $cat_block_right = $this->_getCatBlock($catdetail, 'arwmenu_cat_block_right');
            $cat_block_left = $this->_getCatBlock($catdetail, 'arwmenu_cat_block_left');
            if ($catdetail->getData('arwmenu_proportions_right') || $catdetail->getData('arwmenu_proportions_left')) {
                $columns = $catdetail->getData('arwmenu_cat_columns');
                $proportion_right = $catdetail->getData('arwmenu_proportions_right');
                $proportion_left = $catdetail->getData('arwmenu_proportions_left');
            } else {
                if($catdetail->getData('arwmenu_cat_columns')==''){
                    $columns = 4;
                }else{
                    $columns = $catdetail->getData('arwmenu_cat_columns');
                }
                $proportion_right = 1;
                $proportion_left = 1;
            }
            $goups = $proportion_right + $proportion_left;
            if (empty($cat_block_right) || empty($cat_block_left) || $menutypes == 'drop_group'){
                if(empty($cat_block_right)){
                    $gridCount1 = 'grid12-'.(12 - $proportion_left);
                    $gridCountLeft = 'grid12-' . ($proportion_left);
                }
                if(empty($cat_block_left)){
                    $gridCount1 = 'grid12-'.(12 - $proportion_right);
                    $gridCountRight = 'grid12-' . ($proportion_right);
                }
                if(empty($cat_block_right) && empty($cat_block_left)){
                    $gridCount1 = 'grid12-12';
                }
            } elseif (!$hasActiveChildren){
                $gridCountRight = 'grid12-'.$proportion_right;
                $gridCountLeft = 'grid12-'.$proportion_left;
            } else {
                $grid = 12 - $goups;
                $gridCount1 = 'grid12-' . ($grid);
                $gridCountRight = 'grid12-' . ($proportion_right);
                $gridCountLeft = 'grid12-' . ($proportion_left);
            }
            $goups = $proportion_right + $proportion_left;
        }

        // render children
        $li = '';
        $j = 0;
        $i = 0;
        foreach ($activeChildren as $child) {
            $li .= $this->_renderCategoryMenuGroupItemHtml(
                $child,
                ($level + 1),
                ($j == $activeChildrenCount - 1),
                ($j == 0),
                false,
                $outermostItemClass,
                $childrenWrapClass,
                $noEventAttributes
            );
            $j++;
        }

        if ($childrenWrapClass && $showblock) {
            if($menutypes == 'drop_group'){
                if($level==1){
                    $html[] = '<div class="groups-wrapper">';
                }else{
                    $html[] = '<div class="level'.$level.' dropdown ' . $childrenWrapClass . ' shown-sub" style="display:none; height:auto;">';
                }

            }else{
                if($level==1){
                    $html[] = '<div class="groups-wrapper">';
                }else{
                    $html[] = '<div class="level'.$level.' ' . $childrenWrapClass . ' shown-sub" style="display:none; height:auto;">';
                }
            }
        }
        if($level==0 && $showblock){
            if($this->_getCatBlock($catdetail, 'arwmenu_cat_block_top')){
                $html[] = '<div class="arwmenu-block arwmenu-block-top grid-full std">';
                $html[] = $this->_getCatBlock($catdetail, 'arwmenu_cat_block_top');
                $html[] = '</div>';
            }
            if($menutypes != 'drop_group'){
                if($this->_getCatBlock($catdetail, 'arwmenu_cat_block_left') && $proportion_left){
                    $html[] = '<div class="menu-static-blocks arwmenu-block arwmenu-block-left '.$gridCountLeft.'">';
                    $html[] = $this->_getCatBlock($catdetail, 'arwmenu_cat_block_left');
                    $html[] = '</div>';
                }
            }
        }
        if (!empty($li) && $hasActiveChildren) {
            if($level==0){
                $colCenter = 'itemgrid itemgrid-'. $columns .'col';
                $html[] = '<div class="arwmenu-block arwmenu-block-center menu-items '.$gridCount1.' '. $colCenter .'">';
            }
                    $html[] = '<ul class="level' . $level . '">';
                    $html[] = $li;
                    $html[] = '</ul>';
            if($level==0){
                $html[] = '</div>';
            }
        }

        if($level==0 && $showblock){
            if($menutypes != 'drop_group'){
                if($this->_getCatBlock($catdetail, 'arwmenu_cat_block_right') && $proportion_right){
                    $html[] = '<div class="menu-static-blocks arwmenu-block arwmenu-block-right '.$gridCountRight.'">';
                    $html[] = $this->_getCatBlock($catdetail, 'arwmenu_cat_block_right');
                    $html[] = '</div>';
                }
            }
            if($this->_getCatBlock($catdetail, 'arwmenu_cat_block_bottom')){
                $html[] = '<div class="arwmenu-block arwmenu-block-bottom grid-full std">';
                $html[] = $this->_getCatBlock($catdetail, 'arwmenu_cat_block_bottom');
                $html[] = '</div>';
            }
        }
        if ($childrenWrapClass && $showblock) {
            $html[] = '</div>';
        }

        if ($level == 1 && $showblock){
            if($this->_getCatBlock($catdetail, 'arwmenu_cat_block_bottom')){
                $html[] = '<div class="arwmenu-block arwmenu-block-level1-top std">';
                $html[] = $this->_getCatBlock($catdetail, 'arwmenu_cat_block_bottom');
                $html[] = '</div>';
            }
        }
        $html[] = '</li>';

        $html = implode("\n", $html);
        return $html;

    }

    protected function _renderCategoryMenuItemHtml($category, $level = 0, $isLast = false, $isFirst = false,
                                                   $isOutermost = false, $outermostItemClass = '', $childrenWrapClass = '', $noEventAttributes = false)
    {
        if (!$category->getIsActive()) {
            return '';
        }
        $html = array();
		$max_level=Mage::getStoreConfig('megamenu/general/depth');
        // get all children
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = (array)$category->getChildrenNodes();
            $childrenCount = count($children);
        } else {
            $children = $category->getChildren();
            $childrenCount = $children->count();
        }
        $hasChildren = ($children && $childrenCount);

        // select active children
        $activeChildren = array();
        foreach ($children as $child) {
		if($level >= $max_level-1){break;}
            if ($child->getIsActive()) {
                $activeChildren[] = $child;
            }

        }
        $activeChildrenCount = count($activeChildren);
        $hasActiveChildren = ($activeChildrenCount > 0);
        $catdetail = Mage::getModel('catalog/category')->load($category->getId());
        $menutypes = $catdetail->getData('arwmenu_cat_groups');
        // Check if show category block if no sub-category
        $showblock = false;
        $showblock = $hasActiveChildren;
        if (Mage::helper('megamenu')->getCfg('menu/show_if_no_children')) {$showblock = true; }
        // prepare list item html classes
        $classes = array();
        $classes[] = $catdetail->getData('custom_classes');
        $classes[] = 'level' . $level;
        if($level==1){
            $classes[] = 'item classic';
        }
        $classes[] = 'nav-' . $this->_getItemPosition($level);
        if ($this->isCategoryActive($category)) {
            $classes[] = 'active';
        }
        $linkClass = '';
        if ($isOutermost && $outermostItemClass) {
            $classes[] = $outermostItemClass;
            $linkClass = ' class="'.$outermostItemClass.'"';
        }
        if ($isFirst) {
            $classes[] = 'first';
        }
        if($menutypes == 'dropdown'){
            $classes[] = 'm-dropdown';
        }
        if($level == 0){
            $classes[] = 'menu-style-'. $menutypes;
        }
        if ($isLast) {
            $classes[] = 'last';
        }
        if ($hasActiveChildren) {
            $classes[] = 'parent';
        }
        if ($level==0 && $showblock && $this->_isMomenu == FALSE) {
            $classes[] = 'parent';
        }

        // prepare list item attributes
        $attributes = array();
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }
        if ($hasActiveChildren && !$noEventAttributes) {
            $attributes['onmouseover'] = 'toggleMenu(this,1)';
            $attributes['onmouseout'] = 'toggleMenu(this,0)';
        }

        // assemble list item with attributes
        $htmlLi = '<li';
        foreach ($attributes as $attrName => $attrValue) {
            $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
        }
        $htmlLi .= '>';
        $html[] = $htmlLi;
        if ($level == 1 && $showblock && $this->_isMomenu == FALSE){
            if($this->_getCatBlock($catdetail, 'arwmenu_cat_block_top')){
                $html[] = '<div class="arwmenu-block arwmenu-block-level1-top std">';
                $html[] = $this->_getCatBlock($catdetail, 'arwmenu_cat_block_top');
                $html[] = '</div>';
            }
        }
        $labelCategory = $this->_getCategoryLabelHtml($catdetail, $level);
		$category_icon	=$this->_getCategoryIconHtml($catdetail, $level);
        $html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
        $html[] = '<span>' . $category_icon . $this->escapeHtml($category->getName()) . $labelCategory.  '</span>';
        $html[] = '</a>';

        if ($level == 0 && $this->_isMomenu == FALSE) {
            $cat_block_right = $this->_getCatBlock($catdetail, 'arwmenu_cat_block_right');
            $cat_block_left = $this->_getCatBlock($catdetail, 'arwmenu_cat_block_left');
            if ($catdetail->getData('arwmenu_proportions_right') || $catdetail->getData('arwmenu_proportions_left')) {
                $columns = $catdetail->getData('arwmenu_cat_columns');
                $proportion_right = $catdetail->getData('arwmenu_proportions_right');
                $proportion_left = $catdetail->getData('arwmenu_proportions_left');
            } else {
                if($catdetail->getData('arwmenu_cat_columns')==''){
                    $columns = 4;
                }else{
                    $columns = $catdetail->getData('arwmenu_cat_columns');
                }
                $proportion_right = 1;
                $proportion_left = 1;
            }
            $goups = $proportion_right + $proportion_left;
            if (empty($cat_block_right) || empty($cat_block_left) || $menutypes == 'drop_group'){
                if(empty($cat_block_right)){
                    $gridCount1 = 'grid12-'.(12 - $proportion_left);
                    $gridCountLeft = 'grid12-' . ($proportion_left);
                }
                if(empty($cat_block_left)){
                    $gridCount1 = 'grid12-'.(12 - $proportion_right);
                    $gridCountRight = 'grid12-' . ($proportion_right);
                }
                if(empty($cat_block_right) && empty($cat_block_left)){
                    $gridCount1 = 'grid12-12';
                }
            } elseif (!$hasActiveChildren){
                $gridCountRight = 'grid12-'.$proportion_right;
                $gridCountLeft = 'grid12-'.$proportion_left;
            } else {
                $grid = 12 - $goups;
                $gridCount1 = 'grid12-' . ($grid);
                $gridCountRight = 'grid12-' . ($proportion_right);
                $gridCountLeft = 'grid12-' . ($proportion_left);
            }
            $goups = $proportion_right + $proportion_left;
        }

        // render children
        $li = '';
        $j = 0;
        $i = 0;
        foreach ($activeChildren as $child) {
            $li .= $this->_renderCategoryMenuItemHtml(
                $child,
                ($level + 1),
                ($j == $activeChildrenCount - 1),
                ($j == 0),
                false,
                $outermostItemClass,
                $childrenWrapClass,
                $noEventAttributes
            );
            $j++;
        }


        if ($childrenWrapClass && $showblock && $this->_isMomenu == FALSE) {
            if($menutypes == 'dropdown'){
                $html[] = '<div class="dropdown ' . $childrenWrapClass . ' shown-sub" style="display:none; height:auto;">';
            }else{
                $html[] = '<div class="' . $childrenWrapClass . ' shown-sub" style="display:none; height:auto;">';
            }
        }

        if($level==0 && $showblock && $this->_isMomenu == FALSE){
            if($this->_getCatBlock($catdetail, 'arwmenu_cat_block_top')){
                $html[] = '<div class="arwmenu-block arwmenu-block-top grid-full std">';
                $html[] = $this->_getCatBlock($catdetail, 'arwmenu_cat_block_top');
                $html[] = '</div>';
            }
            if($menutypes != 'dropdown'){
                if($this->_getCatBlock($catdetail, 'arwmenu_cat_block_left') && $proportion_left){
                    $html[] = '<div class="menu-static-blocks arwmenu-block arwmenu-block-left '.$gridCountLeft.'">';
                    $html[] = $this->_getCatBlock($catdetail, 'arwmenu_cat_block_left');
                    $html[] = '</div>';
                }
            }
        }
        if (!empty($li) && $hasActiveChildren) {
            if($level == 0 && $this->_isMomenu == FALSE){
                $colCenter = 'itemgrid itemgrid-'. $columns .'col';
                $html[] = '<div class="arwmenu-block arwmenu-block-center menu-items '.$gridCount1.' '. $colCenter .'">';
            }
                $html[] = '<ul class="level' . $level . '">';
                $html[] = $li;
                $html[] = '</ul>';
            if($level==0 && $this->_isMomenu == FALSE){
                $html[] = '</div>';
            }
        }
        if($level==0 && $showblock && $this->_isMomenu == FALSE){
            if($menutypes != 'dropdown'){
                if($this->_getCatBlock($catdetail, 'arwmenu_cat_block_right') && $proportion_right){
                    $html[] = '<div class="menu-static-blocks arwmenu-block arwmenu-block-right '.$gridCountRight.'">';
                    $html[] = $this->_getCatBlock($catdetail, 'arwmenu_cat_block_right');
                    $html[] = '</div>';
                }
            }
            if($this->_getCatBlock($catdetail, 'arwmenu_cat_block_bottom')){
                $html[] = '<div class="arwmenu-block arwmenu-block-bottom grid-full std">';
                $html[] = $this->_getCatBlock($catdetail, 'arwmenu_cat_block_bottom');
                $html[] = '</div>';
            }
        }

        if ($childrenWrapClass && $showblock && $this->_isMomenu == FALSE) {
            $html[] = '</div>';
        }

        if ($level == 1 && $showblock && $this->_isMomenu == FALSE){
            if($this->_getCatBlock($catdetail, 'arwmenu_cat_block_bottom') && $menutypes != 'dropdown'){
                $html[] = '<div class="arwmenu-block arwmenu-block-level1-top std">';
                $html[] = $this->_getCatBlock($catdetail, 'arwmenu_cat_block_bottom');
                $html[] = '</div>';
            }
        }
        $html[] = '</li>';

        $html = implode("\n", $html);
        return $html;

    }

    public function renderCategoriesMenuHtml($momenu = FALSE, $level = 0, $outermostItemClass = '', $childrenWrapClass = '')
    {
        $this->_isMomenu = $momenu;
        $activeCategories = array();
        foreach ($this->getStoreCategories() as $child) {
            if ($child->getIsActive()) {
                $activeCategories[] = $child;
            }
        }
        $activeCategoriesCount = count($activeCategories);
        $hasActiveCategoriesCount = ($activeCategoriesCount > 0);

        if (!$hasActiveCategoriesCount) {
            return '';
        }

        $html = '';
        $j = 0;
        foreach ($activeCategories as $category) {
            if($this->_isMomenu){
                $html .= $this->_renderCategoryMenuItemHtml(
                    $category,
                    $level,
                    ($j == $activeCategoriesCount - 1),
                    ($j == 0),
                    true,
                    $outermostItemClass,
                    $childrenWrapClass,
                    true
                );
            }else{
                $catdetail = Mage::getModel('catalog/category')->load($category->getId());
                $menutype = $catdetail->getData('arwmenu_cat_groups');

                switch ($menutype) {
                    case 'group':
                    case 'drop_group':
                        $html .= $this->_renderCategoryMenuGroupItemHtml(
                            $category,
                            $level,
                            ($j == $activeCategoriesCount - 1),
                            ($j == 0),
                            true,
                            $outermostItemClass,
                            $childrenWrapClass,
                            true
                        );
                        break;
                    case 'classic':
                    case 'dropdown':
                        $html .= $this->_renderCategoryMenuItemHtml(
                            $category,
                            $level,
                            ($j == $activeCategoriesCount - 1),
                            ($j == 0),
                            true,
                            $outermostItemClass,
                            $childrenWrapClass,
                            true
                        );
                        break;
                    default:
                        $html .= $this->_renderCategoryMenuGroupItemHtml(
                            $category,
                            $level,
                            ($j == $activeCategoriesCount - 1),
                            ($j == 0),
                            true,
                            $outermostItemClass,
                            $childrenWrapClass,
                            true
                        );
                        break;
                }
            }
            $j++;
        }

        return $html;
    }
    protected function _getCatBlock($category, $block){
        if (!$this->_tplProcessor){
            $this->_tplProcessor = Mage::helper('cms')->getBlockTemplateProcessor();
        }
        return $this->_tplProcessor->filter( trim($category->getData($block)) );
    }
    protected function _getCategoryLabelHtml($category, $level){
        $labelCategory = $category->getData('arwmenu_cat_label');
		$category_name = $category->getData('name');
		$background_label_category=$category->getData('arwmenu_cat_label_color');
		$text_label_category=$category->getData("arwmenu_cat_label_text_color");
		$style='background-color:'.$background_label_category.';'.'color:'.$text_label_category;
        $attribute_cat_details = Mage::getSingleton("eav/config")->getAttribute("catalog_category", 'arwmenu_cat_label');
       // $op_cat_labels = $attribute_cat_details->getSource()->getAllOptions(false);
		$op_cat_labels=Mage::getModel('megamenu/system_config_source_category_attribute_source_categorylabel')->getAllOptions();
       if ($labelCategory){
        		foreach  ($op_cat_labels as $op )
        		{
        			if($op['value']==$labelCategory)
        			{
        				$getLabel=$op['label'];
        				break;
        			}
        		}
        //    $getLabel = trim(Mage::helper('megamenu')->getCfg('category_label/label'));
            if ($getLabel) {
                $tmp = str_replace('label','',$labelCategory);
                if ($level == 0){
                    return ' <span style="'.$style.'" class="cat-label cat-label-'.($tmp+1).' cat-label-'. $category_name .' pin-top">' . $getLabel . '</span>';
                }else{
                    return ' <span style="'.$style.'" class="cat-label cat-label-'.($tmp+1).' cat-label-'. $category_name .'">' . $getLabel . '</span>';
                }
            }
        }
		return '';
    }
	 protected function _getCategoryIconHtml($category, $level){
			$category_icon=$category->getData('arwmenu_category_icon');
			if($category_icon)
				{
                    if (preg_match("/fa-/i", $category_icon, $match)){
                        return ' <span class="category_icon fa ' .$category_icon. '"></span>';
                    }else{
                        return ' <span class="category_icon ' .$category_icon. '"></span>';
                    }
				}
		
	 }
	 protected function _getCategoryImageHtml($category, $level){
		return $this->getThumbnailUrl($category,$level);
	 }
	 public function getThumbnailUrl($category,$level)
      {
          $url = false;
          if ($image = Mage::getModel('catalog/category')->load($category->getId())->getThumbnail()) {
              $url = Mage::getBaseUrl('media').'catalog/category/'.$image;
          }
          return $url;
      }
	 public function getCategoryLabel()
		{
			return Mage::helper('megamenu')->getCfg('sidemenu/block_name');
		}
		

}
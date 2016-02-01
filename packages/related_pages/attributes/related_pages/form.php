<div class="ccm-related-pages-attribute-wrapper">
	Stuff here
	<?= $form->text($this->field('relatedPages'), $relatedPages) ?>
	
	<?= $form->selectMultiple($this->field('relatedPages'), $pagesForSelect) ?>
	
</div>

<script>jQuery(document).ready(function($){
	// Initialise bsmSelect
	$("select[multiple]").bsmSelect({
		animate: true,
		highlight: true,
		plugins: [
		  $.bsmSelect.plugins.sortable({ axis : 'y' }, { /*listSortableClass : 'bsmListSortableCustom'*/ })
		  // $.bsmSelect.plugins.compatibility()
		]
	});
	
});</script>

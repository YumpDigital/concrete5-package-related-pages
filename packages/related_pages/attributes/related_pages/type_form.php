<?php
/**
 * This file is shown when creating a new attribute of this type.
 * 
 * We display the following options:
 * 
 *   - Do you want to limit the options to a particular page type?
 *   
 */

/**** original script to display a topic tree *****
    <script type="text/javascript">
        $(function() {
            $('.tree-view-template').ccmtopicstree({  // run first time around to get default tree if new.
                'treeID': <?= $tree->getTreeID(); ?>,
                'chooseNodeInForm': true,
                'noDrag' : true,
                //'selectMode': 2,
                <?php if($parentNode) { ?>
                 'selectNodesByKey': [<?= $parentNode ?>],
                 <?php } ?>
                'onSelect' : function(select, node) {
                     if (select) {
                        $('input[name=akTopicParentNodeID]').val(node.data.key);
                     } else {
                        $('input[name=akTopicParentNodeID]').val('');
                     }
                 }
            });

            var treeViewTemplate = $('.tree-view-template');
            $('select[name=topicTreeIDSelect]').on('change', function() {
                $('input[name="akTopicTreeID"]').val($(this).find(':selected').val());
                $('.tree-view-template').remove();
                $('.tree-view-container').append(treeViewTemplate);
                var toolsURL = '<?= Loader::helper('concrete/urls')->getToolsURL('tree/load'); ?>';
                var chosenTree = $(this).val();
                $('.tree-view-template').ccmtopicstree({
                    'treeID': chosenTree,
                    'chooseNodeInForm': true,
                    'onSelect' : function(select, node) {
                         if (select) {
                            $('input[name=akTopicParentNodeID]').val(node.data.key);
                         } else {
                            $('input[name=akTopicParentNodeID]').val('');
                         }
                     }
                });
            });
        });
    </script>
/***********************/ ?>
    
    <fieldset>
    
        <legend><?= t('Page Types')?></legend>
        <!-- <div class="clearfix"></div> -->
        
        <div class="form-group">
            <label for="pageTypeId">Do you want to limit the list of available pages to a specific page type?</label>
            <select class="form-control" name="pageTypeId">
                <option value="0">All Pages</option>
                <?php foreach ($pageTypes as $pageType) { ?>
                    <option value="<?= $pageType->ptID ?>" <?= $pageType->ptID == $pageTypeId ? 'selected' : '' ?>><?= $pageType->ptName ?></option>
                <?php } ?>
            </select>
        </div>
        
        <input type="hidden" name="akTopicParentNodeID" value="<?= $parentNode ?>">
        <input type="hidden" name="akTopicTreeID" value="<?//= $tree->getTreeID(); ?>">
        
    </fieldset>

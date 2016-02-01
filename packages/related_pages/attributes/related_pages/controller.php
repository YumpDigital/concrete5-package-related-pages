<?php

namespace Concrete\Package\RelatedPages\Attribute\RelatedPages;

// use Concrete\Core\Search\ItemList\Database\AttributedItemList;
// use Concrete\Core\Tree\Node\Node;
// use Concrete\Core\Tree\Type\Topic as TopicTree;
// use Concrete\Core\Tree\Tree;
// use Concrete\Core\Tree\Node\Node as TreeNode;

use Concrete\Core\Attribute\Controller as AttributeController;
use Core;
use Database;
use PageType;

class Controller extends AttributeController
{
    // This valid options for this attribute can be filtered by the specific type of page
    // If so, this variable is initialised in loadAttributeSettings()
    public $pageTypeId;
    
    // Make the form-helper available to all views
    public $helpers = array('form');
    
    // protected $searchIndexFieldDefinition = array(
    //     'type' => 'text',
    //     'options' => array('length' => 4294967295, 'default' => null, 'notnull' => false),
    // );


    // public function filterByAttribute(AttributedItemList $list, $value, $comparison = '=')
    // {
    //     if ($value instanceof TreeNode) {
    //         $topic = $value;
    //     } else {
    //         $topic = Node::getByID(intval($value));
    //     }
    //     if (is_object($topic) && $topic instanceof \Concrete\Core\Tree\Node\Type\Topic) {
    //         $column = 'ak_' . $this->attributeKey->getAttributeKeyHandle();
    //         $qb = $list->getQueryObject();
    //         $qb->andWhere(
    //             $qb->expr()->like($column, ':topicPath')
    //         );
    //         $qb->setParameter('topicPath', "%||" . $topic->getTreeNodeDisplayPath() . '%||');
    //     }
    // }

    /**
     * Called when saving the details of an attribute under
     * Pages & Themes > Attributes
     * 
     * Saves any custom settings that are present in type_form.php
     * 
     * @param array $data - form POST data
     * @return null
     */
    public function saveKey($data)
    {
        $ak = $this->getAttributeKey();

        Database::get()->Replace('atRelatedPagesSettings', [
            'akID' => $ak->getAttributeKeyID(),
            'pageTypeId' => $data['pageTypeId'],
        ], ['akID'], true);
    }


    // public function getDisplayValue()
    // {
    //     $list = $this->getSelectedOptions();
    //     $topics = array();
    //     foreach ($list as $node) {
    //         $topic = Node::getByID($node);
    //         if (is_object($topic)) {
    //             $topics[] = $topic->getTreeNodeDisplayName();
    //         }
    //     }

    //     return implode(', ', $topics);
    // }

    // public function getDisplaySanitizedValue()
    // {
    //     return $this->getDisplayValue();
    // }

    // public function getSelectedOptions()
    // {
    //     $avID = $this->getAttributeValueID();
    //     $db = Database::get();
    //     $optionIDs = $db->GetCol(
    //         'select TopicNodeID from atSelectedTopics where avID=?',
    //         array($avID)
    //     );

    //     return $optionIDs;
    // }

    // public function exportValue(\SimpleXMLElement $akn)
    // {
    //     $avn = $akn->addChild('topics');
    //     $nodes = $this->getSelectedOptions();
    //     foreach ($nodes as $node) {
    //         $topic = Node::getByID($node);
    //         if (is_object($topic)) {
    //             $avn->addChild('topic', $topic->getTreeNodeDisplayPath());
    //         }
    //     }
    // }

    // public function importValue(\SimpleXMLElement $akn)
    // {
    //     $selected = array();
    //     if (isset($akn->topics)) {
    //         foreach ($akn->topics->topic as $topicPath) {
    //             $selected[] = (string) $topicPath;
    //         }
    //     }

    //     return $selected;
    // }

    // public function saveValue($nodes)
    // {
    //     $selected = array();
    //     $this->loadAttributeSettings();
    //     $tree = Tree::getByID($this->akTopicTreeID);
    //     foreach ($nodes as $topicPath) {
    //         $node = $tree->getNodeByDisplayPath($topicPath);
    //         if (is_object($node)) {
    //             $selected[] = $node->getTreeNodeID();
    //         }
    //     }

    //     $db = Database::get();
    //     $db->Execute('delete from atSelectedTopics where avID = ?', array($this->getAttributeValueID()));

    //     foreach ($selected as $optionID) {
    //         $db->execute(
    //             'INSERT INTO atSelectedTopics (avID, TopicNodeID) VALUES (?, ?)',
    //             array($this->getAttributeValueID(), $optionID)
    //         );
    //     }
    // }

    // public function exportKey($key)
    // {
    //     $this->loadAttributeSettings();
    //     $tree = Tree::getByID($this->akTopicTreeID);
    //     $node = Node::getByID($this->akTopicParentNodeID);
    //     $path = $node->getTreeNodeDisplayPath();
    //     $treeNode = $key->addChild('tree');
    //     $treeNode->addAttribute('name', $tree->getTreeName());
    //     $treeNode->addAttribute('path', $path);

    //     return $key;
    // }

    // public function importKey($key)
    // {
    //     $name = (string) $key->tree['name'];
    //     $tree = \Concrete\Core\Tree\Type\Topic::getByName($name);
    //     $node = $tree->getNodeByDisplayPath((string) $key->tree['path']);
    //     $this->setNodes($node->getTreeNodeID(), $tree->getTreeID());
    // }

    /**
     * Renders the edit form for the attribute within a page/user object
     * 
     * For pages this might be displayed within composer view, or within Sitemap > Click a Page > Attributes
     * 
     * @param  boolean $additionalClass
     * @return null
     */
    public function form($additionalClass = false)
    {
        $this->loadAttributeSettings();       
        
        // $this->requireAsset('core/topics');
        // $this->requireAsset('javascript', 'jquery');
        $this->requireAsset('bsmSelect');               // Defined in package/controller.php
        
        // Fetch a list of pages that match pageTypeId
        // This provides the options for the editor to select from
        // See http://documentation.concrete5.org/developers/working-with-pages/searching-and-sorting-with-the-pagelist-object
        $pageList = new \Concrete\Core\Page\PageList();
        $pageList->filterByPageTypeId($this->pageTypeId);
        $pageList->sortByDisplayOrder();
        $pages = $pageList->getResults();
        // d($pages);                
        $this->set('pages', $pages);
        
        // Page list for select box
        $pagesForSelect = [];
        foreach ($pages as $p)
            $pagesForSelect[$p->cID] = $p->getVersionObject()->cvName;
        $this->set('pagesForSelect', $pagesForSelect);
        
        // Fetch existing value of the attribute (if present)
        $this->set('relatedPages', $this->getValue()[0]['pageId']);        
        
        // if (is_object($this->attributeValue)) {
        //     $value = $this->getAttributeValue()->getValue();
        // }
        // if ($this->getAttributeValueID()) {
        //     $valueIDs = array();
        //     foreach ($this->getSelectedOptions() as $valueID) {
        //         $withinParentScope = false;
        //         $nodeObj = TreeNode::getByID($valueID);
        //         if (is_object($nodeObj)) {
        //             $parentNodeArray = $nodeObj->getTreeNodeParentArray();
        //             // check to see if selected node is still within parent scope, in case it has been changed.
        //             foreach ($parentNodeArray as $parent) {
        //                 if ($parent->treeNodeID == $this->akTopicParentNodeID) {
        //                     $withinParentScope = true;
        //                     break;
        //                 }
        //             }
        //             if ($withinParentScope) {
        //                 $valueIDs[] = $valueID;
        //             }
        //         }
        //     }
        //     $this->set('valueIDs', implode(',', $valueIDs));
        // }
        
        // $this->set('valueIDArray', $valueIDs);
        // $ak = $this->getAttributeKey();
        // $this->set('akID', $ak->getAttributeKeyID());
        // $this->set('parentNode', $this->akTopicParentNodeID);
        // $this->set('treeID', $this->akTopicTreeID);
        // $this->set('avID', $this->getAttributeValueID());
    }

    // public function searchForm($list)
    // {
    //     $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $this->request('treeNodeID'));

    //     return $list;
    // }

    // public function getSearchIndexValue()
    // {
    //     $str = "||";
    //     $nodeKeys = $this->getSelectedOptions();
    //     foreach ($nodeKeys as $nodeKey) {
    //         $nodeObj = TreeNode::getByID($nodeKey);
    //         if (is_object($nodeObj)) {
    //             $str .= $nodeObj->getTreeNodeDisplayPath() . "||";
    //         }
    //     }
    //     // remove line break for empty list
    //     if ($str == "\n") {
    //         return '';
    //     }

    //     return $str;
    // }

    // public function search()
    // {
    //     $this->requireAsset('core/topics');
    //     $this->loadAttributeSettings();
    //     $tree = TopicTree::getByID(Core::make('helper/security')->sanitizeInt($this->akTopicTreeID));
    //     $this->set('tree', $tree);
    //     $treeNodeID = $this->request('treeNodeID');
    //     if (!$treeNodeID) {
    //         $treeNodeID = $this->akTopicParentNodeID;
    //     }
    //     $this->set('selectedNode', $treeNodeID);
    //     $this->set('attributeKey', $this->attributeKey);
    // }

    /**
     * Called when saving a page/user object to save the actual attribute data
     * 
     * @return null
     */
    public function saveForm($data)
    {
        $db = Database::get();
        $ak = $this->getAttributeKey();
        
        // Delete all existing values for this object
        $db->delete('atRelatedPages', ['avID' => $this->getAttributeValueID()]);
        
        // Save new ones
        $db->insert('atRelatedPages', [
            'avID' => $this->getAttributeValueID(),
            'pageId' => $data['relatedPages'],
        ]);
    }

    public function getValue()
    {
        $value = Database::get()->fetchAll('SELECT * from atRelatedPages where avID = ?', [$this->getAttributeValueID()]);
        return $value;
    }
    
    /**
     * Do I need this function?
     * 
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    // public function saveValue($value) 
    // {
    //     echo 'Saving';
    //     d($value);       
    // }

    /**
     * Called when deleting an attribute from Dashboard > Pages & Themes > Attributes
     * 
     * @return null
     */
    public function deleteKey()
    {
        $db = Database::get();
        
        // Delete all attribute values
        $arr = $this->attributeKey->getAttributeValueIDList();
        foreach ($arr as $id) {
            $db->Execute('delete from atDefault where avID = ?', array($id));
        }

        // Delete attribute settings
        $db->Execute('delete from atRelatedPagesSettings where akID = ?', [$this->attributeKey->getAttributeKeyID()]);
    }

    /**
     * Called prior to rendering the attribute settings form under
     * Dashboard > Pages & Themes > Attributes > Create/Edit
     * 
     * Should retrieve any settings from DB and set any additional variables needed to display the form
     * 
     * saveKey() is later called when user clicks "Save" button on form
     * 
     * @return null
     */
    public function type_form()
    {
        // $this->requireAsset('core/topics');
        // $this->requireAsset('javascript', 'jquery/form');
        $this->loadAttributeSettings();
        
        $this->set('pageTypes', PageType::getList());
        
        // $tt = new TopicTree();
        // $defaultTree = $tt->getDefault();
        // $topicTreeList = $tt->getList();
        // $tree = $tt->getByID(Core::make('helper/security')->sanitizeInt($this->akTopicTreeID));
        // if (!$tree) {
        //     $tree = $defaultTree;
        // }
        // $this->set('tree', $tree);
        // $trees = array();
        // if (is_object($defaultTree)) {
        //     $trees[] = $defaultTree;
        //     foreach ($topicTreeList as $ctree) {
        //         if ($ctree->getTreeID() != $defaultTree->getTreeID()) {
        //             $trees[] = $ctree;
        //         }
        //     }
        // }
        // $this->set('trees', $trees);
        // $this->set('parentNode', $this->akTopicParentNodeID);
    }

    // public function validateKey($data = false)
    // {
    //     if ($data == false) {
    //         $data = $this->post();
    //     }
    //     $e = parent::validateKey($data);
    //     if (!$data['akTopicParentNodeID'] || !$data['akTopicTreeID']) {
    //         $e->add(t('You must specify a valid topic tree parent node ID and topic tree ID.'));
    //     }

    //     return $e;
    // }

    // public function validateValue()
    // {
    //     $val = $this->getValue();

    //     return is_object($val);
    // }

    // public function validateForm($data)
    // {
    //     // TODO: form validation
    // }

    /**
     * Internal helper function to retrieve settings for the current attribute.
     * Stores them as class variables and also sends them to view.
     * 
     * Called in many functions
     * 
     * @return null
     */
    protected function loadAttributeSettings()
    {        
        $ak = $this->getAttributeKey();
        if (!is_object($ak)) {
            return false;
        }

        $db = Database::get();
        $row = $db->GetRow('SELECT pageTypeId FROM atRelatedPagesSettings WHERE akID = ?', [$ak->getAttributeKeyID()]);
        $this->pageTypeId = $row['pageTypeId'];
        $this->set('pageTypeId', $this->pageTypeId);
        
    //     $ak = $this->getAttributeKey();
    //     if (!is_object($ak)) {
    //         return false;
    //     }
    //     $db = Database::get();
    //     $row = $db->GetRow('select * from atTopicSettings where akID = ?', $ak->getAttributeKeyID());
    //     $this->akTopicParentNodeID = $row['akTopicParentNodeID'];
    //     $this->akTopicTreeID = $row['akTopicTreeID'];
    }

    /**
     * No idea when this is called. How do you duplicate an attribute?
     * 
     * (Doesn't make sense to call this when duplicating a page perhaps?)
     * 
     * @param $newAK
     * @return null
     * /
    public function duplicateKey($newAK)
    {
        trigger_error('Have not quite tested/implemented this function yet.', E_USER_NOTICE);
        
        $this->loadAttributeSettings();
        $db = Database::get();
        $db->Execute('INSERT INTO atRelatedPagesSettings (akID, pageTypeId) values (?, ?)', [
            $newAK->getAttributeKeyID(), 
            $this->pageTypeId
        ]);
    }
    */
}

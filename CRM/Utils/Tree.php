<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2015                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2015
 */

/**
 * Manage simple Tree data structure.
 *
 * Example of Tree is
 *
 *                             'a'
 *                              |
 *    --------------------------------------------------------------
 *    |                 |                 |              |         |
 *   'b'               'c'               'd'            'e'       'f'
 *    |                 |         /-----/ |                        |
 *  -------------     ---------  /     --------     ------------------------
 *  |           |     |       | /      |      |     |           |          |
 * 'g'         'h'   'i'     'j'      'k'    'l'   'm'         'n'        'o'
 *                            |
 *                  ----------------------
 *                 |          |          |
 *                'p'        'q'        'r'
 *
 *
 *
 * From the above diagram we have
 *   'a'  - root node
 *   'b'  - child node
 *   'g'  - leaf node
 *   'j'  - node with multiple parents 'c' and 'd'
 *
 *
 * All nodes of the tree (including root and leaf node) contain the following properties
 *       Name      - what is the node name ?
 *       Children  - who are it's children
 *       Data      - any other auxiliary data
 *
 *
 * Internally all nodes are an array with the following keys
 *      'name' - string
 *      'children' - array
 *      'data' - array
 */
class CRM_Utils_Tree {

  /**
   * Store the tree information as a string or array.
   * @var string|array
   */
  private $tree;

  /**
   * Constructor for the tree.
   *
   * @param string $nodeName
   *
   * @internal param string $rootNode
   *
   * @return CRM_Utils_Tree
   */
  public function __construct($nodeName) {
    // create the root node
    $rootNode = &$this->createNode($nodeName);

    // add the root node to the tree
    $this->tree['rootNode'] = &$rootNode;
  }

  /**
   * Find a node that matches the given string.
   *
   * @param string $name
   *   Name of the node we are searching for.
   * @param array (ref) $parentNode which parent node should we search in ?
   *
   * @return array(ref) | false node if found else false
   */
  public function &findNode($name, &$parentNode) {
    // if no parent node specified, please start from root node
    if (!$parentNode) {
      $parentNode = &$this->tree['rootNode'];
    }

    // first check the nodename of subtree itself
    if ($parentNode['name'] == $name) {
      return $parentNode;
    }

    $falseRet = FALSE;
    // no children ? return false
    if ($this->isLeafNode($node)) {
      return $falseRet;
    }

    // search children of the subtree
    foreach ($parentNode['children'] as $key => $childNode) {
      $cNode = &$parentNode['children'][$key];
      if ($node = &$this->findNode($name, $cNode)) {
        return $node;
      }
    }

    // name does not match subtree or any of the children, negative result
    return $falseRet;
  }

  /**
   * Check if node is a leaf node.
   *
   * Currently leaf nodes are strings and non-leaf nodes are arrays
   *
   * @param array $node node which needs to checked
   *
   * @return bool
   */
  public function isLeafNode(&$node) {
    return (count($node['children']) ? TRUE : FALSE);
  }

  /**
   * Create a node.
   *
   * @param string $name
   *
   * @return array
   *   (ref)
   */
  public function &createNode($name) {
    $node['name'] = $name;
    $node['children'] = array();
    $node['data'] = array();

    return $node;
  }

  /**
   * Add node.
   *
   * @param string $parentName
   *   Name of the parent ?.
   * @param array (ref) $node - node to be added
   */
  public function addNode($parentName, &$node) {
    $temp = '';
    $parentNode = &$this->findNode($parentName, $temp);

    $parentNode['children'][] = &$node;
  }

  /**
   * Add Data.
   *
   * @param string $parentName Name of the parent ?.
   * @param string $childName - key to be used (optional)
   * @param mixed $data - data to be added
   */
  public function addData($parentName, $childName, $data) {
    $temp = '';
    if ($parentNode = &$this->findNode($parentName, $temp)) {
      foreach ($parentNode['children'] as $key => $childNode) {
        $cNode = &$parentNode['children'][$key];
        if ($cNode = &$this->findNode($childName, $parentNode)) {
          $cNode['data']['fKey'] = &$data;
        }
      }
    }
  }

  /**
   * Get Tree.
   *
   * @return tree
   */
  public function getTree() {
    return $this->tree;
  }

  /**
   * Print the tree.
   */
  public function display() {
    print_r($this->tree);
  }

}

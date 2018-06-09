<?php

/* *********************************************************************
 * This Source Code Form is copyright of 51Degrees Mobile Experts Limited. 
 * Copyright 2014 51Degrees Mobile Experts Limited, 5 Charlotte Close,
 * Caversham, Reading, Berkshire, United Kingdom RG4 7BY
 * 
 * This Source Code Form is the subject of the following patent 
 * applications, owned by 51Degrees Mobile Experts Limited of 5 Charlotte
 * Close, Caversham, Reading, Berkshire, United Kingdom RG4 7BY: 
 * European Patent Application No. 13192291.6; and 
 * United States Patent Application Nos. 14/085,223 and 14/085,301.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0.
 * 
 * If a copy of the MPL was not distributed with this file, You can obtain
 * one at http://mozilla.org/MPL/2.0/.
 * 
 * This Source Code Form is "Incompatible With Secondary Licenses", as
 * defined by the Mozilla Public License, v. 2.0.
 * ********************************************************************* */

/**
 * @file
 * Provides functionality for a linked list.
 */

/**
 * Provides functionality for a linked list, allowing nodes to be added and
 * removed from an arbitrary place in the list while maintaining performance.
 */
class LinkedList {
  
  /**
   * The current node being pointed at. -1 if there are no nodes.
   */
  public $current = -1;
  /**
   * The first node in the list. -1 if there are no nodes.
   */
  public $first = -1;
  /**
   * The last node in the list. -1 if there are no nodes.
   */
  public $last = -1;
  
  public $count = 0;
  
  function __construct() {
  
  }
  
  function getCount() {
    $node = $this->first;
    $fcount = 0;
    while ($node !== -1) {
      $node = $node->nextNode;
      $fcount++;
    }
    return $fcount;
  }
  
  /**
   * Moves the current node. Sets $current to -1 if there is no node afterwards.
   *
   * @return LinkedListNode
   *   Returns the new current node.
   */
  function moveNext() {
    if ($this->current !== -1 && $this->current->nextNode !== -1) {
      $this->current = $this->current->nextNode;
    }
    else {
      $this->current = -1;
    }
    return $this->current;
  }
  
  /**
   * Moves the current node. Sets $current to -1 if there is no node before.
   *
   * @return LinkedListNode
   *   Returns the new current node.
   */
  function moveBack() {
    if ($this->current !== -1 && $this->current->lastNode !== -1) {
      $this->current = -1;
    }
    else {
      $this->current = $this->current->lastNode;
    }
    return $this->current;
  }
  
  /**
   * Adds the $value to the end of the list.
   *
   * A new node will be created and given $value. Other nodes will have their
   * pointers changed to accommodate the new node.
   *
   * @param $value
   *   The value to add to the node. This can be any variable.
   *
   * @return LinkedListNode
   *   The new node just added.
   */
  function addLast($value) {
    $this->count++;
    $newNode = new LinkedListNode($this);
    $newNode->value = $value;
    if ($this->first === -1) {
      $this->first = $newNode;
    }
    if ($this->last !== -1) {
      $this->last->nextNode = $newNode;
      $newNode->lastNode = $this->last;
    }
    $this->last = $newNode;
    if ($this->current === -1) {
      $this->current = $newNode;
    }
    return $newNode;
  }
  
  /**
   * Adds the $value to the beginning of the list.
   *
   * A new node will be created and given $value. Other nodes will have their
   * pointers changed to accommodate the new node.
   *
   * @param $value
   *   The value to add to the node. This can be any variable.
   *
   * @return LinkedListNode
   *   The new node just added.
   */
  function addFirst($value) {
    $this->count++;
    $newNode = new LinkedListNode($this);
    $newNode->value = $value;
    if ($this->first !== -1) {
      $newNode->nextNode = $this->first;
      $this->first->lastNode = $newNode;
    }
    if ($this->last === -1) {
      $this->last = $newNode;
    }
    $this->first = $newNode;
    if ($this->current === -1) {
      $this->current = $newNode;
    }
    return $newNode;
  }
}

/**
 * Represents a node with pointers to other nodes for the LinkedList class.
 */
class LinkedListNode {
  /**
   * The LinkedList this node is contained in.
   */
  public $linkedList;
  /**
   * The value of this node. This can be any PHP variable.
   */
  public $value = -1;
  
  /**
   * The pointer to the next node. -1 means there is no next node.
   */
  public $nextNode = -1;
  /**
   * The pointer to the last node. -1 means there is no last node.
   */
  public $lastNode = -1;

  function __construct($linkedList) {
    $this->linkedList = $linkedList;
  }

  /**
   * Adds $value to a new node before this node.
   *
   * Creates a new node with $value that is inserted in the list before this
   * node. The list pointers are modified to accomodate the new node.
   *
   * @param $value
   *   The value of the new node. This can be any PHP variable.
   *
   * @return LinkedListNode
   *   The new node that was just created.
   */
  function addBefore($value) {
    $this->linkedList->count++;
    $newNode = new LinkedListNode($this->linkedList);
    $newNode->value = $value;
    $newNode->lastNode = $this->lastNode;
    $newNode->nextNode = $this;
    
    if ($this->lastNode !== -1) {
      $this->lastNode->nextNode = $newNode;
    }
    $this->lastNode = $newNode;

    if ($this->linkedList->first === $this) {
      $this->linkedList->first = $newNode;
    }
    return $newNode;
  }

  /**
   * Adds $value to a new node after this node.
   *
   * Creates a new node with $value that is inserted in the list after this
   * node. The list pointers are modified to accomodate the new node.
   *
   * @param $value
   *   The value of the new node. This can be any PHP variable.
   *
   * @return LinkedListNode
   *   The new node that was just created.
   */
  function addAfter($value) {
    $this->linkedList->count++;
    $newNode = new LinkedListNode($this->linkedList);
    $newNode->value = $value;
    $newNode->lastNode = $this;
    $newNode->nextNode = $this->nextNode;

    if ($this->nextNode !== -1) {
      $this->nextNode->lastNode = $newNode;
    }
    $this->nextNode = $newNode;

    if ($this->linkedList->last === $this) {
      $this->linkedList->last = $newNode;
    }
    return $newNode;
  }

  /**
   * Removes this node from the list and from other nodes referencing this one.
   *
   * This node will no longer be available from the list or any node in the
   * list.
   */
  function remove() {
    $this->linkedList->count--;
    if ($this->nextNode !== -1 && $this->lastNode !== -1) {
      $this->nextNode->lastNode = $this->lastNode;
      $this->lastNode->nextNode = $this->nextNode;
    }
    if ($this->linkedList->first === $this) {
      $this->linkedList->first = $this->nextNode;
    }
    if ($this->linkedList->last === $this) {
      $this->linkedList->last = $this->lastNode;
    }
    if ($this->linkedList->current === $this) {
      $this->linkedList->moveNext();
    }
  }
}
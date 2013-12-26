# Functions: sb_is_child_page


## Description

Tests to see if current (or specified) page has a parent. Can also test to see if page is child of a specific parent.

## Usage

	<?php sb_is_child_page( $parent_id, $page_id ) ?>

## Parameters

* **$parent_id**

    (integer) (optional) Specify a page ID to test if page is child of specific parent.

	* Default: None 

* **$page_id**

    (integer) (optional) Specify a page ID to test a specific page (defaults to current page when used inside loop).

	* Default: None 

## Return Values

* (boolean) 

    Whether the specified page has a specified parent. 

## Examples

### Check if Page Has a Parent

	<?php 
	if ( sb_is_child_page() {
    	// Page has a parent, do something...
	} else {
    	//Page has no parent, do something else...
	}
	?>

### Check if Page Has a Specific Parent

	<?php 
	if ( sb_is_child_page(14) {
    	// Page is a child of page ID 14, do something...
	} else {
    	//Page is not a child of page ID 14, do something else...
	}
	?>

### Check if Specific Page Has a Parent

	<?php 
	if ( sb_is_child_page( null, 15 ) {
	    // Page 15 has a parent, do something...
	} else {
	    // Page 15 has no parent, do something else...
	}
	?>

### Check if Specific Page Has a Specific Parent

	<?php 
	if ( sb_is_child_page(12, 15) {
	    // Page 15 is a child of Page 12, do something...
	} else {
	    //Page 15 is not a child of Page 12, do something else...
	}
	?>

## Notes
## Change Log

Since: 2.4.9

## Source File

sb_is_child_page() is located in /startbox/includes/functions/custom.php 
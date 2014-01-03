/*
Title: sbx_delete_term_meta
Description: Parameters and examples of the sbx_delete_term_meta function
Author: Michael Beckwith
Date: 12-20-2013
Last Edited: 01-02-2014
 */

# sbx_delete_term_meta

## Description

Deletes the term meta for a given term ID.

Primarily used to remove specified layouts for terms.

## Usage

	<?php sbx_delete_term_meta( $term_id, $meta_key, $meta_value ); ?>

## Parameters

* **object_id**

	(integer) (optional) ID for the term

	* Default: 0

* **meta_key**

	(string) (optional) Meta key to look up and delete to

	* Default: ''

* **meta_value**

	(string) (optional) Value to delete with the meta key

	* Default: ''

## Examples

	sbx_delete_term_meta( $object_id, 'layout', esc_attr( $layout ) );

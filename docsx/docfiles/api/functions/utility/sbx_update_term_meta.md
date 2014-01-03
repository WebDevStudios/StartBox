/*
Title: sbx_update_term_meta
Description: Parameters and examples of the sbx_update_term_meta function
Author: Michael Beckwith
Date: 12-20-2013
Last Edited: 01-02-2014
 */

# sbx_update_term_meta

## Description

Updates the term meta key for a given term ID. Returns the value of `update_option()`

Primarily used to update specified layouts for terms.

## Usage

	<?php sbx_update_term_meta( $term_id, $meta_key, $meta_value ); ?>

## Parameters

* **term_id**

	(integer) (optional) ID for the term

	* Default: 0

* **meta_key**

	(string) (optional) Meta key to look up and save to

	* Default: ''

* **meta_value**

	(string) (optional) Value to save with the meta key

	* Default: ''

## Examples

	sbx_update_term_meta( $object_id, 'layout', esc_attr( $layout ) );

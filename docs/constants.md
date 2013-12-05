# Constants

StartBox defines a number of constants during initialization that you may find useful.

## Theme Constants

* **THEME_NAME**
	* Returns active theme name
* **SBX_VERSION**
	* Returns active theme version number
* **SBX_OPTIONS**
	* Returns 'startbox', used for interacting with the primary serialized theme option in the database.
* **THEME_PREFIX**
	* Returns 'sb', used to prefix various things throughout StartBox.

## Theme Paths

* **THEME_DIR**
	* Returns path to active parent theme directory ( get_template_directory() ).
* **THEME_URI**
	* Returns URI to active parent theme directory ( get_template_directory_uri() ).
* **CHILD_THEME_DIR**
	* Returns path to active child theme directory ( get_stylesheet_directory() ).
* **CHILD_THEME_URI**
	* Returns URI to active child theme directory ( get_stylesheet_directory_uri() ).
* **SBX_DIR**
	* Returns path to the SBX directory.
* **SBX_URI**
	* Returns URI to the SBX directory.
* **SBX_ADMIN**
	* Returns path to the SBX admin directory.
* **SBX_CLASSES**
	* Returns path to the SBX classes directory.
* **SBX_CSS**
	* Returns URI to the SBX css directory.
* **SBX_EXTENSIONS**
	* Returns path to the SBX extensions directory.
* **SBX_IMAGES**
	* Returns path to the SBX images directory.
* **SBX_JS**
	* Returns URI to the SBX js directory.
* **SBX_LANGUAGES**
	* Returns path to the SBX languages directory.

## Undefined Constants

Set this constant if you want the default settings to not be used at all.

* **SB_REMOVE_DEFAULT_SETTINGS**
	* Remove all default panels from the Theme Options page. You can also use the function sb_remove_default_settings()
# Options API

StartBox comes with an incredibly easy-to-use, modular settings page. For users, this means a quick and painless setup, for developers this means an incredible new level of control and flexibility for your themes.

## Adding an Options Panel

You can use the [sb_register_settings()]() function to add your own options panel.

## Removing an Options Panel

Use the [sb_unregister_settings()]() function to remove a default option panel or [sb_remove_default_settings()]() to remove ALL default option panels.

## Creating Options

The Options API derives its real power from your ability to [Create Options](). There are loads of pre-defined option types at your disposal, ranging from an AJAX uploader to simple text boxes.

Use [sb_register_option()]() and [sb_unregister_option()]() to add or remove single options from existing metaboxes.

## Interacting With Your Options

Because the options are all stored to a single array, StartBox has a few helper functions to make it easy for you interact with that data.

* [sb_add_option()]() - Add option data to the database.
* [sb_remove_option()]() - Remove option data from the database.
* [sb_get_option()]() - Retrieve option data from the database.
* [sb_update_option()]() - Update an existing option's data in the database.
Table Of Contents
=============================================================================

Released under the [MIT license](http://opensource.org/licenses/MIT). Copyright (c) 2013 mcbSolutions.at

**Version** 0.1 alpha; Please report errors.

**Generates a table of contents for the current page.**

Installation
=============================================================================
1. Copy/save the plugin into `plugins` folder

index.html
-----------------------------------------------------------------------------
1. Add `<link rel="stylesheet" href="{{ base_url }}/plugins/mcb_TableOfContent/style.css" media="screen,projection,print">` and `<link rel="stylesheet" href="{{ base_url }}/plugins/mcb_TableOfContent/print.css" media="print">` in the `head` section of your layout file.
2. **Optional - Smooth scrolling:** Add `<script src="{{ base_url }}/plugins/mcb_TableOfContent/code.js"></script>` after `<script src="{{ base_url }}/vendor/jquery/jquery.min.js"></script>` inside the `head` section.
2. Add `{{ mcb_toc_top }}` directly after the `body` tag.
3. Add `{{ mcb_toc }}` where you want the table of contents displayed.
4. Add `{{ mcb_top_link }}` if you want a link to top outside the content.
    
Optional: Config
-----------------------------------------------------------------------------

### mcb_toc_depth
**integer**

Only display header h1 to h`n` (where `n` is 1-6)

	$config['mcb_toc_depth']		= 3;
	
### mcb_toc_min_headers
**integer**

Only generate Table of content with at least `n` headers

	$config['mcb_toc_min_headers']	= 3;	
	
### mcb_toc_top_txt					
**string**

Text to display for "Move to top"

	$config['mcb_toc_top_txt']		= 'Top';				
	
### mcb_toc_caption
**string**

Text to display as caption for the table of contents

	$config['mcb_toc_caption']		= 'Table of contents';
	
### mcb_toc_anchor
**bool**

Set to false, if you like to add your own anchor

	$config['mcb_toc_anchor']       = false;
	
**Note**

If you use `$config['mcb_toc_anchor'] = true;` then `{{ mcb_toc_top }}` will be disabled.

Screenshot
=============================================================================
![Screenshot of Table Of Contents](./Screenshot.png)
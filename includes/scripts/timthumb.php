<?php
/**
 * TimThumb script created by Ben Gillbanks, originally created by Tim McDaniels and Darren Hoyt
 * http://code.google.com/p/timthumb/
 * 
 * GNU General Public License, version 2
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Examples and documentation available on the project homepage
 * http://www.binarymoon.co.uk/projects/timthumb/
 */

define ('CACHE_SIZE', 1000);				// number of files to store before clearing cache
define ('CACHE_CLEAR', 20);					// maximum number of files to delete on each cache clear
define ('CACHE_USE', TRUE);					// use the cache files? (mostly for testing)
define ('CACHE_MAX_AGE', 864000);			// time to cache in the browser
define ('VERSION', '1.31');					// version number (to force a cache refresh)
define ('DIRECTORY_CACHE', '../../../../uploads/cache');	// cache directory
define ('MAX_WIDTH', 2500);					// maximum image width
define ('MAX_HEIGHT', 2500);				// maximum image height
define ('ALLOW_EXTERNAL', FALSE);			// allow external website (override security precaution - not advised!)
define ('MEMORY_LIMIT', '30M');				// set PHP memory limit
define ('MAX_FILE_SIZE', 1500000);			// file size limit to prevent possible DOS attacks (roughly 1.5 megabytes)
define ('CURL_TIMEOUT', 10);				// timeout duration. Tweak as you require (lower = better)

// external domains that are allowed to be displayed on your website
$allowedSites = array (
	'flickr.com',
	'picasa.com',
	'blogger.com',
	'wordpress.com',
	'img.youtube.com',
	'upload.wikimedia.org',
	'photobucket.com',
);

// STOP MODIFYING HERE!
// --------------------

// sort out image source
$src = get_request ('src', '');
if ($src == '' || strlen ($src) <= 3) {
    display_error ('no image specified');
}


// clean params before use
$src = clean_source ($src);

// get mime type of src
$mime_type = mime_type ($src);

// used for external websites only
$external_data_string = '';

// generic file handle for reading and writing to files
$fh = '';

// check to see if this image is in the cache already
// if already cached then display the image and die
check_cache ($mime_type);

// cache doesn't exist and then process everything
// check to see if GD function exist
if (!function_exists ('imagecreatetruecolor')) {
    display_error ('GD Library Error: imagecreatetruecolor does not exist - please contact your webhost and ask them to install the GD library');
}

if (function_exists ('imagefilter') && defined ('IMG_FILTER_NEGATE')) {
	$imageFilters = array (
		1 => array (IMG_FILTER_NEGATE, 0),
		2 => array (IMG_FILTER_GRAYSCALE, 0),
		3 => array (IMG_FILTER_BRIGHTNESS, 1),
		4 => array (IMG_FILTER_CONTRAST, 1),
		5 => array (IMG_FILTER_COLORIZE, 4),
		6 => array (IMG_FILTER_EDGEDETECT, 0),
		7 => array (IMG_FILTER_EMBOSS, 0),
		8 => array (IMG_FILTER_GAUSSIAN_BLUR, 0),
		9 => array (IMG_FILTER_SELECTIVE_BLUR, 0),
		10 => array (IMG_FILTER_MEAN_REMOVAL, 0),
		11 => array (IMG_FILTER_SMOOTH, 0),
	);
}

// get standard input properties
$new_width =  (int) abs (get_request ('w', 0));
$new_height = (int) abs (get_request ('h', 0));
$zoom_crop = (int) get_request ('zc', 1);
$quality = (int) abs (get_request ('q', 90));
$align = get_request ('a', 'c');
$filters = get_request ('f', '');
$sharpen = (bool) get_request ('s', 0);
$canvas_color = get_request ('cc', 'ffffff');

// set default width and height if neither are set already
if ($new_width == 0 && $new_height == 0) {
    $new_width = 100;
    $new_height = 100;
}

// ensure size limits can not be abused
$new_width = min ($new_width, MAX_WIDTH);
$new_height = min ($new_height, MAX_HEIGHT);

// set memory limit to be able to have enough space to resize larger images
ini_set ('memory_limit', MEMORY_LIMIT);

if (file_exists ($src)) {

    // open the existing image
    $image = open_image ($mime_type, $src);
    if ($image === false) {
        display_error ('Unable to open image : ' . $src);
    }

    // Get original width and height
    $width = imagesx ($image);
    $height = imagesy ($image);
	$origin_x = 0;
	$origin_y = 0;

    // generate new w/h if not provided
    if ($new_width && !$new_height) {
        $new_height = floor ($height * ($new_width / $width));
    } else if ($new_height && !$new_width) {
        $new_width = floor ($width * ($new_height / $height));
    }

	// scale down and add borders
	if ($zoom_crop == 3) {

		$final_height = $height * ($new_width / $width);

		if ($final_height > $new_height) {
			$new_width = $width * ($new_height / $height);
		} else {
			$new_height = $final_height;
		}

	}

	// create a new true color image
	$canvas = imagecreatetruecolor ($new_width, $new_height);
	imagealphablending ($canvas, false);

	if (strlen ($canvas_color) < 6) {
		$canvas_color = 'ffffff';
	}

	$canvas_color_R = hexdec (substr ($canvas_color, 0, 2));
	$canvas_color_G = hexdec (substr ($canvas_color, 2, 2));
	$canvas_color_B = hexdec (substr ($canvas_color, 2, 2));

	// Create a new transparent color for image
	$color = imagecolorallocatealpha ($canvas, $canvas_color_R, $canvas_color_G, $canvas_color_B, 127);

	// Completely fill the background of the new image with allocated color.
	imagefill ($canvas, 0, 0, $color);

	// scale down and add borders
	if ($zoom_crop == 2) {

		$final_height = $height * ($new_width / $width);
		
		if ($final_height > $new_height) {
			
			$origin_x = $new_width / 2;
			$new_width = $width * ($new_height / $height);
			$origin_x = round ($origin_x - ($new_width / 2));

		} else {

			$origin_y = $new_height / 2;
			$new_height = $final_height;
			$origin_y = round ($origin_y - ($new_height / 2));

		}

	}

	// Restore transparency blending
	imagesavealpha ($canvas, true);

	if ($zoom_crop > 0) {

		$src_x = $src_y = 0;
		$src_w = $width;
		$src_h = $height;

		$cmp_x = $width / $new_width;
		$cmp_y = $height / $new_height;

		// calculate x or y coordinate and width or height of source
		if ($cmp_x > $cmp_y) {

			$src_w = round ($width / $cmp_x * $cmp_y);
			$src_x = round (($width - ($width / $cmp_x * $cmp_y)) / 2);

		} else if ($cmp_y > $cmp_x) {

			$src_h = round ($height / $cmp_y * $cmp_x);
			$src_y = round (($height - ($height / $cmp_y * $cmp_x)) / 2);

		}

		// positional cropping!
		if ($align) {
			if (strpos ($align, 't') !== false) {
				$src_y = 0;
			}
			if (strpos ($align, 'b') !== false) {
				$src_y = $height - $src_h;
			}
			if (strpos ($align, 'l') !== false) {
				$src_x = 0;
			}
			if (strpos ($align, 'r') !== false) {
				$src_x = $width - $src_w;
			}
		}

		imagecopyresampled ($canvas, $image, $origin_x, $origin_y, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h);

    } else {

        // copy and resize part of an image with resampling
        imagecopyresampled ($canvas, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    }

    if ($filters != '' && function_exists ('imagefilter') && defined ('IMG_FILTER_NEGATE')) {
        // apply filters to image
        $filterList = explode ('|', $filters);
        foreach ($filterList as $fl) {

            $filterSettings = explode (',', $fl);
            if (isset ($imageFilters[$filterSettings[0]])) {

                for ($i = 0; $i < 4; $i ++) {
                    if (!isset ($filterSettings[$i])) {
						$filterSettings[$i] = null;
                    } else {
						$filterSettings[$i] = (int) $filterSettings[$i];
					}
                }

                switch ($imageFilters[$filterSettings[0]][1]) {

                    case 1:

                        imagefilter ($canvas, $imageFilters[$filterSettings[0]][0], $filterSettings[1]);
                        break;

                    case 2:

                        imagefilter ($canvas, $imageFilters[$filterSettings[0]][0], $filterSettings[1], $filterSettings[2]);
                        break;

                    case 3:

                        imagefilter ($canvas, $imageFilters[$filterSettings[0]][0], $filterSettings[1], $filterSettings[2], $filterSettings[3]);
                        break;

                    case 4:

                        imagefilter ($canvas, $imageFilters[$filterSettings[0]][0], $filterSettings[1], $filterSettings[2], $filterSettings[3], $filterSettings[4]);
                        break;

                    default:

                        imagefilter ($canvas, $imageFilters[$filterSettings[0]][0]);
                        break;

                }
            }
        }
    }

	// sharpen image
	if ($sharpen && function_exists ('imageconvolution')) {

		$sharpenMatrix = array (
			array (-1,-1,-1),
			array (-1,16,-1),
			array (-1,-1,-1),
		);

		$divisor = 8;
		$offset = 0;

		imageconvolution ($canvas, $sharpenMatrix, $divisor, $offset);

	}

    // output image to browser based on mime type
    show_image ($mime_type, $canvas);

    // remove image from memory
    imagedestroy ($canvas);

	// if not in cache then clear some space and generate a new file
	clean_cache ();

	die ();

} else {

    if (strlen ($src)) {
        display_error ('image ' . $src . ' not found');
    } else {
        display_error ('no source specified');
    }

}


/**
 *
 * @global <type> $quality
 * @param <type> $mime_type
 * @param <type> $image_resized 
 */
function show_image ($mime_type, $image_resized) {

    global $quality;

    $cache_file = get_cache_file ($mime_type);

	switch ($mime_type) {
		case 'jpg':
			imagejpeg ($image_resized, $cache_file, $quality);
			break;

		default:
		case 'png':
			imagepng ($image_resized, $cache_file, floor ($quality * 0.09));
			break;

	}

	show_cache_file ($mime_type);

}


/**
 *
 * @param <type> $property
 * @param <type> $default
 * @return <type> 
 */
function get_request ($property, $default = 0) {

    if (isset ($_GET[$property])) {
        return $_GET[$property];
    } else {
        return $default;
    }

}


/**
 *
 * @param <type> $mime_type
 * @param <type> $src
 * @return <type>
 */
function open_image ($mime_type, $src) {

	switch ($mime_type) {
		case 'jpg':
			$image = imagecreatefromjpeg ($src);
			break;

		case 'png':
			$image = imagecreatefrompng ($src);
			break;

		case 'gif':
			$image = imagecreatefromgif ($src);
			break;
	}

    return $image;

}

/**
 * clean out old files from the cache
 * you can change the number of files to store and to delete per loop in the defines at the top of the code
 *
 * @return <type>
 */
function clean_cache () {

	// add an escape
	// Reduces the amount of cache clearing to save some processor speed
	if (rand (1, 50) > 10) {
		return true;
	}

	flush ();

    $files = glob (DIRECTORY_CACHE . '/*', GLOB_BRACE);

	if (count ($files) > CACHE_SIZE) {
		
        $yesterday = time () - (24 * 60 * 60);

        usort ($files, 'filemtime_compare');
        $i = 0;

		foreach ($files as $file) {

			$i ++;

			if ($i >= CACHE_CLEAR) {
				return;
			}

			if (@filemtime ($file) > $yesterday) {
				return;
			}

			if (file_exists ($file)) {
				unlink ($file);
			}

		}

    }

}


/**
 * compare the file time of two files
 *
 * @param <type> $a
 * @param <type> $b
 * @return <type>
 */
function filemtime_compare ($a, $b) {

	$break = explode ('/', $_SERVER['SCRIPT_FILENAME']);
	$filename = $break[count ($break) - 1];
	$filepath = str_replace ($filename, '', $_SERVER['SCRIPT_FILENAME']);

	$file_a = realpath ($filepath . $a);
	$file_b = realpath ($filepath . $b);

    return filemtime ($file_a) - filemtime ($file_b);

}


/**
 * determine the file mime type
 *
 * @param <type> $file
 * @return <type>
 */
function mime_type ($file) {

	$file_infos = getimagesize ($file);
	
	// no mime type
	if (empty ($file_infos['mime'])) {
		display_error ('no mime type specified in image');
	}

	$mime_type = $file_infos['mime'];

	// use mime_type to determine mime type
    if (!preg_match ("/jpg|jpeg|gif|png/i", $mime_type)) {
		display_error ('Invalid src mime type: ' . $mime_type);
    }

	$mime_type = strtolower ($mime_type);
	$mime_type = str_replace ('image/', '', $mime_type);

	if ($mime_type == 'jpeg') {
		$mime_type = 'jpg';
	}

    return $mime_type;

}


/**
 *
 * @param <type> $mime_type
 */
function check_cache ($mime_type) {

	if (CACHE_USE) {

		if (!show_cache_file ($mime_type)) {
			// make sure cache dir exists
			if (!file_exists (DIRECTORY_CACHE)) {
				// give 777 permissions so that developer can overwrite
				// files created by web server user
				mkdir (DIRECTORY_CACHE);
				chmod (DIRECTORY_CACHE, 0777);
			}
		}

	}

}


/**
 *
 * @param <type> $mime_type
 * @return <type> 
 */
function show_cache_file ($mime_type) {

	// use browser cache if available to speed up page load
	if (!empty ($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
		if (strtotime ($_SERVER['HTTP_IF_MODIFIED_SINCE']) < strtotime ('now')) {
			header ('HTTP/1.1 304 Not Modified');
			die ();
		}
	}

	$cache_file = get_cache_file ($mime_type);

	if (file_exists ($cache_file)) {

		// change the modified headers
		$gmdate_expires = gmdate ('D, d M Y H:i:s', strtotime ('now +10 days')) . ' GMT';
		$gmdate_modified = gmdate ('D, d M Y H:i:s') . ' GMT';

		// send content headers then display image
		header ('Content-Type: image/' . get_file_type ($mime_type));
		header ('Accept-Ranges: bytes');
		header ('Last-Modified: ' . $gmdate_modified);
		header ('Content-Length: ' . filesize ($cache_file));
		header ('Cache-Control: max-age=' . CACHE_MAX_AGE . ', must-revalidate');
		header ('Expires: ' . $gmdate_expires);

		if (!@readfile ($cache_file)) {
			$content = file_get_contents ($cache_file);
			if ($content != FALSE) {
				echo $content;
			} else {
				display_error ('cache file could not be loaded');
			}
		}

		die ();

    }

	return FALSE;

}


/**
 *
 * @param type $extension
 * @return type 
 */
function get_file_type ($extension) {
	
	switch ($extension) {
		case 'png':
		case 'gif':
			return 'png';
			
		case 'jpg':
		case 'jpeg':
			return 'jpeg';
			
		default:
			display_error ('file type not found : ' . $extension);
	}
	
}


/**
 *
 * @staticvar string $cache_file
 * @param <type> $mime_type
 * @return string
 */
function get_cache_file ($mime_type) {

    static $cache_file;
	global $src;

    if (!$cache_file) {
		// filemtime is used to make sure updated files get recached
        $cache_file = DIRECTORY_CACHE . '/' . md5 ($_SERVER ['QUERY_STRING'] . VERSION . filemtime ($src)) . '.' . $mime_type;
    }

    return $cache_file;

}


/**
 *
 * @param <type> $url
 * @return <type> 
 */
function validate_url ($url) {
	$pattern = "/\b(?:(?:https?):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
	return preg_match ($pattern, $url);
}


/**
 *
 * @global array $allowedSites
 * @param string $src
 * @return string
 */
function check_external ($src) {

	global $allowedSites;

	// work out file details
	$file_details = pathinfo ($src);
	$filename = 'external_' . md5 ($src);
	$local_filepath = DIRECTORY_CACHE . '/' . $filename . '.' . $file_details['extension'];
	
	// only do this stuff the file doesn't already exist
	if (!file_exists ($local_filepath)) {

		if (strpos (strtolower ($src), 'http://') !== false || strpos (strtolower ($src), 'https://') !== false) {

			if (!validate_url ($src)) {
				display_error ('invalid url');
			}

			$url_info = parse_url ($src);

			if (count (explode ('.', $url_info['path'])) > 2) {
				display_error ('source filename invalid');
			}			

			// convert youtube video urls
			// need to tidy up the code

			if ($url_info['host'] == 'www.youtube.com' || $url_info['host'] == 'youtube.com') {
				parse_str ($url_info['query']);

				if (isset ($v)) {
					$src = 'http://img.youtube.com/vi/' . $v . '/0.jpg';
					$url_info['host'] = 'img.youtube.com';
				}
			}

			// check allowed sites (if required)
			if (ALLOW_EXTERNAL) {

				$isAllowedSite = true;

			} else {

				$isAllowedSite = false;
				foreach ($allowedSites as $site) {
					if (strpos (strtolower ($url_info['host']), $site) !== false) {
						$isAllowedSite = true;
					}
				}

			}

			// if allowed
			if ($isAllowedSite) {

				if (function_exists ('curl_init')) {

					global $fh;

					$fh = fopen ($local_filepath, 'w');
					$ch = curl_init ($src);

					curl_setopt ($ch, CURLOPT_TIMEOUT, CURL_TIMEOUT);
					curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
					curl_setopt ($ch, CURLOPT_URL, $src);
					curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt ($ch, CURLOPT_HEADER, 0);
					curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt ($ch, CURLOPT_FILE, $fh);
					curl_setopt ($ch, CURLOPT_WRITEFUNCTION, 'curl_write');

					// error so die
					if (curl_exec ($ch) === FALSE) {
						unlink ($local_filepath);
						touch ($local_filepath);
						display_error ('error reading file ' . $src . ' from remote host: ' . curl_error ($ch));
					}

					curl_close ($ch);
					fclose ($fh);

                } else {

					if (!$img = file_get_contents ($src)) {
						display_error ('remote file for ' . $src . ' can not be accessed. It is likely that the file permissions are restricted');
					}

					if (file_put_contents ($local_filepath, $img) == FALSE) {
						display_error ('error writing temporary file');
					}

				}

				if (!file_exists ($local_filepath)) {
					display_error ('local file for ' . $src . ' can not be created');
				}

				$src = $local_filepath;

			} else {

				display_error ('remote host "' . $url_info['host'] . '" not allowed');

			}

		}

    } else {

		$src = $local_filepath;

	}

    return $src;

}


/**
 * callback for curl command to receive external images
 * limit the amount of data downloaded from external servers
 * 
 * @global <type> $data_string
 * @param <type> $handle
 * @param <type> $data
 * @return <type>
 */
function curl_write ($handle, $data) {

	global $external_data_string, $fh;

	fwrite ($fh, $data);
	$external_data_string .= $data;

	if (strlen ($external_data_string) > MAX_FILE_SIZE) {
		return 0;
	} else {
		return strlen ($data);
	}

}


/**
 * tidy up the image source url
 *
 * @param <type> $src
 * @return string
 */
function clean_source ($src) {

	$host = str_replace ('www.', '', $_SERVER['HTTP_HOST']);
	$regex = "/^(http(s|):\/\/)(www\.|)" . $host . "\//i";

	$src = preg_replace ($regex, '', $src);
	$src = strip_tags ($src);
    $src = check_external ($src);

    // remove slash from start of string
    if (strpos ($src, '/') === 0) {
        $src = substr ($src, -(strlen ($src) - 1));
    }

    // don't allow users the ability to use '../'
    // in order to gain access to files below document root
    $src = preg_replace ("/\.\.+\//", "", $src);
	
    // get path to image on file system
    $src = get_document_root ($src) . '/' . $src;

	if (!is_file ($src)) {
		display_error ('source is not a valid file');
	}

	if (filesize ($src) > MAX_FILE_SIZE) {
		display_error ('source file is too big (filesize > MAX_FILE_SIZE)');
	}

	if (filesize ($src) <= 0) {
		display_error ('source file <= 0 bytes. Possible external file download error (file is too large)');
	}

    return realpath ($src);

}


/**
 *
 * @param <type> $src
 * @return string
 */
function get_document_root ($src) {

    // check for unix servers
    if (file_exists ($_SERVER['DOCUMENT_ROOT'] . '/' . $src)) {
        return $_SERVER['DOCUMENT_ROOT'];
    }

    // check from script filename (to get all directories to timthumb location)
    $parts = array_diff (explode ('/', $_SERVER['SCRIPT_FILENAME']), explode ('/', $_SERVER['DOCUMENT_ROOT']));

	$path = './';
	
	foreach ($parts as $part) {
		if (file_exists ($path . '/' . $src)) {
			return realpath ($path);
		}
		$path .= '../';
	}

    // special check for microsoft servers
    if (!isset ($_SERVER['DOCUMENT_ROOT'])) {
        $path = str_replace ("/", "\\", $_SERVER['ORIG_PATH_INFO']);
        $path = str_replace ($path, '', $_SERVER['SCRIPT_FILENAME']);

        if (file_exists ($path . '/' . $src)) {
            return realpath ($path);
        }
    }

    display_error ('file not found');

}


/**
 * generic error message
 *
 * @param <type> $errorString
 */
function display_error ($errorString = '') {

    header ('HTTP/1.1 400 Bad Request');
	echo '<pre>' . htmlentities ($errorString);
	echo '<br />Query String : ' . htmlentities ($_SERVER['QUERY_STRING']);
	echo '<br />TimThumb version : ' . VERSION . '</pre>';
    die ();

}

?>
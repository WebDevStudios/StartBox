<?php

/**
 * The file description. *
 * @package Pico
 * @subpackage Adv meta
 * @version 0.1.4
 * @author Shawn Sandy <shawnsandy04@gmail.com>
 *
 */
class Adv_Meta {

    private /* default meta values */
            $meta_values = array(
                //page slug keep lower case
                'slug' => 'Slug',
                //page category
                'category' => 'Category',
                //page status
                'status' => 'Status',
                //Type -- page, post, plugin
                'type' => 'Type',
                //Page Thumbnail -- (theme/images)
                'thumbnail' => 'Thumbnail',
                // image for page icon -- (theme/images/)
                'icon' => 'Icon',
                //use custom page template(s)
                'tpl' => 'Tpl'
                    ),
            $content = null,
            $config = null,
            $custom_meta_enabled = false,
            $meta = array();

    public function __construct() {

    }

    public function before_load_content(&$file) {

        if (file_exists($file))
            $this->content = file_get_contents($file);
    }

    public function config_loaded(&$settings) {

        $this->config = $settings;
        if (isset($settings['custom_meta_values']))
            $this->meta_values = $settings['custom_meta_values'];
    }

    public function before_read_file_meta(&$headers) {
        $this->adv_file_meta();
        foreach ($this->meta_values as $key => $value) {
            $headers[$key] = $value;
        }
        //var_dump($headers);
    }

    public function get_page_data(&$data, $page_meta) {

        //$data = array_merge($data, $this->adv_file_meta()) ;

        foreach ($page_meta as $key => $value) {
            $data[$key] = $value ;
        }
    }

    public function before_render(&$twig_vars, &$twig) {

        $twig_vars['adv_meta'] = $this->adv_file_meta();
        //var_dump($this->adv_file_meta());
    }

    /**
     * Grab the file meta here
     * @return string
     */
    private function adv_file_meta() {

        //include the config aand grab some useful values
        $content = $this->content;
        $config = $this->config;

        if (!isset($this->config))
            $config = array();

        if (isset($config['adv_meta_values']))
            $this->meta_values = $config['adv_meta_values'];

        $headers = $this->meta_values;

        foreach ($headers as $field => $regex) {
            if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $content, $match) && $match[1]) {
                $headers[$field] = trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $match[1]));
            } else {
                $headers[$field] = '';
            }
        }
        //var_dump($headers);
        return $headers;
    }

}

<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: river
 * Date: 13-12-12
 * Time: 下午6:41
 * 重写Router实现module
 */

class KR_Router extends CI_Router {

    private $_module = false;

    function _set_routing()
    {
        $segments = array();
        if ($this->config->item('enable_query_strings') === TRUE AND isset($_GET[$this->config->item('controller_trigger')]))
        {
            if (isset($_GET[$this->config->item('modules_trigger')]))
            {
                $this->set_module(trim($this->uri->_filter_uri($_GET[$this->config->item('modules_trigger')])));
                $segments[] = $this->fetch_module();
            }
            if (isset($_GET[$this->config->item('directory_trigger')]))
            {
                $this->set_directory(trim($this->uri->_filter_uri($_GET[$this->config->item('directory_trigger')])));
                $segments[] = $this->fetch_directory();
            }

            if (isset($_GET[$this->config->item('controller_trigger')]))
            {
                $this->set_class(trim($this->uri->_filter_uri($_GET[$this->config->item('controller_trigger')])));
                $segments[] = $this->fetch_class();
            }

            if (isset($_GET[$this->config->item('function_trigger')]))
            {
                $this->set_method(trim($this->uri->_filter_uri($_GET[$this->config->item('function_trigger')])));
                $segments[] = $this->fetch_method();
            }
        }

        if (defined('ENVIRONMENT') AND is_file(APPPATH.'config/'.ENVIRONMENT.'/routes'.EXT))
        {
            include(APPPATH.'config/'.ENVIRONMENT.'/routes'.EXT);
        }
        elseif (is_file(APPPATH.'config/routes'.EXT))
        {
            include(APPPATH.'config/routes'.EXT);
        }
        if ($handle = opendir(MODPATH)){
            while (false !== ($item = readdir($handle))){
                if (is_dir(MODPATH.$item) and $item != "." and $item != ".."){
                    if (defined('ENVIRONMENT') AND is_file(MODPATH.$item.'/config/'.ENVIRONMENT.'/routes'.EXT))
                    {
                        include(MODPATH.$item.'/config/'.ENVIRONMENT.'/routes'.EXT);
                    }
                    elseif (is_file(MODPATH.$item.'/config/routes'.EXT))
                    {
                        include(MODPATH.$item.'/config/routes'.EXT);
                    }
                }
            }
            closedir($handle);
        }
        $this->routes = ( ! isset($route) OR ! is_array($route)) ? array() : $route;
        unset($route);
        $this->default_module = ( ! isset($this->routes['default_module']) OR $this->routes['default_module'] == '') ? FALSE : strtolower($this->routes['default_module']);
        $this->default_controller = ( ! isset($this->routes['default_controller']) OR $this->routes['default_controller'] == '') ? FALSE : strtolower($this->routes['default_controller']);
        if (count($segments) > 0)
        {
            return $this->_validate_request($segments);
        }
        $this->uri->_fetch_uri_string();
        if ($this->uri->uri_string == '')
        {
            return $this->_set_default_controller();
        }
        $this->uri->_remove_url_suffix();
        $this->uri->_explode_segments();
        $this->_parse_routes();
        $this->uri->_reindex_segments();
    }

    function _set_request($segments = array())
    {
        $segments = $this->_validate_request($segments);
        if (count($segments) == 0)
        {
            return $this->_set_default_controller();
        }

        if ($this->_set_module_routes($segments))
        {
            return;
        }

        $this->set_class($segments[0]);

        if (isset($segments[1]))
        {
            $this->set_method($segments[1]);
        }
        else
        {
            $segments[1] = 'index';
        }
        $this->uri->rsegments = $segments;
    }

    function set_module($module)
    {
        $this->_module = $module;
    }

    function fetch_module()
    {
        return $this->_module;
    }

    function fetch_method()
    {
        return $this->method;
    }

    function _set_default_controller()
    {
        $x[0] = '';
        if ($this->default_module !== FALSE) {
            $this->set_module($this->default_module);
            $x[0] = $this->default_module;
        }

        if ($this->default_controller === FALSE)
        {
            show_error("Unable to determine what should be displayed. A default route has not been specified in the routing file.");
        }
        // Is the method being specified?
        if (strpos($this->default_controller, '/') !== FALSE)
        {
            $x = array_merge($x, explode('/', $this->default_controller));
            $this->set_class($x[1]);
            $this->set_method($x[2]);
            $this->_set_request($x);
        }
        else
        {
            $this->set_class($this->default_controller);
            $this->set_method('index');
            $x[] = $this->default_controller;
            $x[] = 'index';
            $this->_set_request($x);
        }

        // re-index the routed segments array so it starts with 1 rather than 0
        $this->uri->_reindex_segments();

        log_message('debug', "No URI present. Default controller set.");
    }

    function _set_module_routes($segments)
    {
        if (file_exists(MODPATH . $segments[0]))
        {
            $this->set_module($segments[0]);
            if (isset($segments[1]) && file_exists(MODPATH . $segments[0]. '/controllers/'. $this->fetch_directory() .  $segments[1].EXT)) {
                $this->set_class($segments[1]);
                if (isset($segments[2]))
                {
                    $this->set_method($segments[2]);
                }
                else
                {
                    $segments[2] = 'index';
                }
            } else {
                if (strpos($this->default_controller, '/') !== FALSE)
                {
                    $x = explode('/', $this->default_controller);

                    $this->set_class($x[0]);
                    $this->set_method($x[1]);
                }
                else
                {
                    $this->set_class($this->default_controller);
                    $this->set_method('index');
                }
            }
            unset($segments[0]);
            $this->uri->rsegments = $segments;
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    function _validate_request($segments)
    {
        if (count($segments) == 0)
        {
            return $segments;
        }

        if (file_exists(MODPATH . $segments[0]))
        {
            $this->set_module($segments[0]);

            if (count($segments) > 1 && file_exists(MODPATH . $segments[0]. '/controllers/'. $this->fetch_directory() .  $segments[1].EXT))
            {
                return $segments;
            }

            if (!isset($segments[1])) {
                return $segments;
            }

            if (is_dir(MODPATH . $segments[0] .  '/controllers/' . $segments[1]))
            {
                $this->set_directory($segments[1]);
                unset($segments[1]);

                foreach ($segments as $segment)
                {
                    $n_segments[] = $segment;
                }

                $segments = $n_segments;

                if (count($segments) > 0)
                {
                    if (!isset($segments[1]) || ! file_exists(MODPATH . $segments[0]. '/controllers/'. $this->fetch_directory() .  $segments[1].EXT))
                    {
                        show_404($this->fetch_directory().$segments[0]);
                    }

                    $this->set_class($segments[1]);
                    return $segments;
                }
                else
                {
                    if (strpos($this->default_controller, '/') !== FALSE)
                    {
                        $x = explode('/', $this->default_controller);

                        $this->set_class($x[0]);
                        $this->set_method($x[1]);
                    }
                    else
                    {
                        $this->set_class($this->default_controller);
                        $this->set_method('index');
                    }

                    if ( ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$this->default_controller.EXT))
                    {
                        $this->directory = '';
                        return array();
                    }
                }
            }
        }

        if (file_exists(APPPATH.'controllers/'.$segments[0].EXT))
        {
            return $segments;
        }

        if (is_dir(APPPATH.'controllers/'.$segments[0]))
        {
            $this->set_directory($segments[0]);
            $segments = array_slice($segments, 1);

            if (count($segments) > 0)
            {
                if ( ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$segments[0].EXT))
                {
                    show_404($this->fetch_directory().$segments[0]);
                }
            }
            else
            {
                if (strpos($this->default_controller, '/') !== FALSE)
                {
                    $x = explode('/', $this->default_controller);

                    $this->set_class($x[0]);
                    $this->set_method($x[1]);
                }
                else
                {
                    $this->set_class($this->default_controller);
                    $this->set_method('index');
                }

                if ( ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$this->default_controller.EXT))
                {
                    $this->directory = '';
                    return array();
                }

            }

            return $segments;
        }

        if (!empty($this->routes['404_override']))
        {
            $x = explode('/', $this->routes['404_override']);

            $this->set_class($x[0]);
            $this->set_method(isset($x[1]) ? $x[1] : 'index');

            return $x;
        }

        show_404($segments[0]);
    }
}

<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: river
 * Date: 13-12-12
 * Time: 下午6:41
 *
 * REST客户端
 */

class REST
{
    protected $_ci;

    protected $supported_formats = array(
		'xml' 				=> 'application/xml',
		'json' 				=> 'application/json',
		'serialize' 		=> 'application/vnd.php.serialized',
		'php' 				=> 'text/plain',
    	'csv'				=> 'text/csv'
	);

    protected $auto_detect_formats = array(
		'application/xml' 	=> 'xml',
		'text/xml' 			=> 'xml',
		'application/json' 	=> 'json',
		'text/json' 		=> 'json',
		'text/csv' 			=> 'csv',
		'application/csv' 	=> 'csv',
    	'application/vnd.php.serialized' => 'serialize'
	);

	protected $rest_server;
    protected $app_key = NULL;
    protected $secret_key = NULL;
    protected $format;
    protected $mime_type;
    protected $response_string;
    public $access_token = NULL;

    function __construct($config = array())
    {
		$this->load->library('curl');
        isset($config['server']) && $this->rest_server = $config['server'];
        if (substr($this->rest_server, -1, 1) != '/') {
            $this->rest_server .= '/';
        }
        isset($config['app_key']) && $this->app_key = $config['app_key'];
        isset($config['secret_key']) && $this->secret_key = $config['secret_key'];
    }

	function __destruct()
	{
		$this->curl->set_defaults();
	}

    public function get($uri, $params = array(), $format = NULL, $cache = TRUE)
    {
        $params = $this->_arrange_params($params);
        $uri .= '?'.(is_array($params) ? http_build_query($params, '', '&') : $params);

        return $this->_call('get', $uri, NULL, $format);
    }

    public function post($uri, $params = array(), $format = NULL)
    {
        $params = $this->_arrange_params($params);
        return $this->_call('post', $uri, $params, $format);
    }

    public function put($uri, $params = array(), $format = NULL)
    {
        $params = $this->_arrange_params($params);
        return $this->_call('put', $uri, $params, $format);
    }

    public function delete($uri, $params = array(), $format = NULL)
    {
        $params = $this->_arrange_params($params);
        return $this->_call('delete', $uri, $params, $format);
    }

    public function language($lang)
	{
		if (is_array($lang))
		{
			$lang = implode(', ', $lang);
		}

		$this->curl->http_header('Accept-Language', $lang);
	}

    protected function _call($method, $uri, $params = array(), $format = NULL)
    {
    	$format !== NULL && $this->format($format);
		$this->http_header('Accept', $this->mime_type);
        $this->curl->create($this->rest_server.$uri);
        $this->curl->option('failonerror', FALSE);
        $this->curl->{$method}($params);
        $response = $this->curl->execute();
        //$this->curl->debug();
        if ($this->status() == '403') {
            return $this->_format_response('no_sigin');
        }
        return $this->_format_response($response);
    }

    protected function _arrange_params($api_params = array())
    {
        $sys_params = array(
            'app_key' => $this->app_key,
            'timestamp' => date("Y-m-d H:i:s")
        );
        if ($this->access_token) {
            $sys_params['access_token'] = $this->access_token;
        }

        $params = array_merge((array)$api_params, (array)$sys_params);
        $params["sign"] = $this->generate_sign($params);
        return $params;
    }

    /**
     * 生成签名
     *
     * @param $params
     * @return string
     */
    protected function generate_sign($params)
    {
        ksort($params);
        reset($params);
        $params_signed = '';
        foreach ($params as $k => $v) {
            if("@" != substr($v, 0, 1)) {
                $params_signed .= "$k$v";
            }
        }
        unset($k, $v);
        $params_signed .= $this->secret_key;

        return strtoupper(md5($params_signed));
    }

    public function format($format)
	{
		if (array_key_exists($format, $this->supported_formats)) {
			$this->format = $format;
			$this->mime_type = $this->supported_formats[$format];
		} else {
			$this->mime_type = $format;
		}

		return $this;
	}

	public function status()
	{
		return $this->info('http_code');
	}

	public function info($key = null)
	{
		return $key === null ? $this->curl->info : @$this->curl->info[$key];
	}

	public function option($code, $value)
	{
		$this->curl->option($code, $value);
	}

	public function http_header($header, $content = NULL)
	{
		$params = $content ? array($header, $content) : array($header);
		call_user_func_array(array($this->curl, 'http_header'), $params);
	}

	protected function _format_response($response)
	{
		$this->response_string =& $response;
		if (array_key_exists($this->format, $this->supported_formats))
		{
			return $this->{"_".$this->format}($response);
		}
		$returned_mime = @$this->curl->info['content_type'];
		if (strpos($returned_mime, ';'))
		{
			list($returned_mime) = explode(';', $returned_mime);
		}
		$returned_mime = trim($returned_mime);
		if (array_key_exists($returned_mime, $this->auto_detect_formats))
		{
			return $this->{'_'.$this->auto_detect_formats[$returned_mime]}($response);
		}
		return $response;
	}

    protected function _xml($string)
    {
    	return $string ? (array) simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA) : array();
    }

    protected function _csv($string)
    {
		$data = array();
		$rows = explode("\n", trim($string));
		$headings = explode(',', array_shift($rows));
		foreach( $rows as $row )
		{
			// The substr removes " from start and end
			$data_fields = explode('","', trim(substr($row, 1, -1)));

			if (count($data_fields) === count($headings))
			{
				$data[] = array_combine($headings, $data_fields);
			}

		}

		return $data;
    }

    protected function _json($string)
    {
    	return json_decode(trim($string), true);
    }

    protected function _serialize($string)
    {
    	return unserialize(trim($string));
    }

    protected function _php($string)
    {
    	$string = trim($string);
    	$populated = array();
    	eval("\$populated = \"$string\";");
    	return $populated;
    }

    function __get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }

    public function debug()
    {
        $request = $this->curl->debug_request();

        echo "=============================================<br/>\n";
        echo "<h2>REST Test</h2>\n";
        echo "=============================================<br/>\n";
        echo "<h3>Request</h3>\n";
        echo $request['url']."<br/>\n";
        echo "=============================================<br/>\n";
        echo "<h3>Response</h3>\n";

        if ($this->response_string)
        {
            echo "<code>".nl2br(htmlentities($this->response_string))."</code><br/>\n\n";
        }

        else
        {
            echo "No response<br/>\n\n";
        }

        echo "=============================================<br/>\n";

        if ($this->curl->error_string)
        {
            echo "<h3>Errors</h3>";
            echo "<strong>Code:</strong> ".$this->curl->error_code."<br/>\n";
            echo "<strong>Message:</strong> ".$this->curl->error_string."<br/>\n";
            echo "=============================================<br/>\n";
        }

        echo "<h3>Call details</h3>";
        echo "<pre>";
        print_r($this->curl->info);
        echo "</pre>";

    }

}

/* End of file REST.php */
/* Location: ./application/libraries/REST.php */
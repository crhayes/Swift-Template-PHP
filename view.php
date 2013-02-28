<?php
/**
 * Swift Template PHP
 *
 * View Inheritance allows HTML views to manage themselves, saving the
 * controllers from worrying about display logic. In short, inheritance in
 * your views offers the same decoupling benefits as standard PHP objects
 * making it easier to manage presentation.
 *
 * This class is more powerful than it looks.
 *
 * This class is based off work by David Pennington
 * <https://github.com/Xeoncross/PHP-Template>. It has been modified to 
 * create a more intuitive and easier to use API.
 *
 * @package		PHP View
 * @author 		Chris Hayes <chris@chrishayes.ca>
 * @author		http://github.com/Xeoncross/php-template
 * @copyright	(c) 2013 Chris Hayes <http://chrishayes.ca>
 * @copyright	(c) 2011 David Pennington <http://xeoncross.com>
 * @license		MIT License
 */

/**
 * Helper functions to make it easier to work with views.
 */
function view($view)
{
	return new View($view);
}

function extend($name)
{
	View::getInstance()->extend($name);
}

function section($name)
{
	View::getInstance()->section($name);
}

function close()
{
	echo View::getInstance()->close();
}

function show($name)
{
	echo View::getInstance()->show($name);
}

function partial($name)
{
	echo View::getInstance()->load($name);
}

/**
 * Swift Template PHP View Class
 */
class View
{
	/**
	 * Store an instance of the View class.
	 * 
	 * @var View
	 */
	private static $instance;

	/**
	 * Store the path to the views folder.
	 * 
	 * @var string
	 */
	private static $viewPath = 'views/';

	/**
	 * The name of the view we are loading.
	 * 
	 * @var string
	 */
	private $viewName;

	/**
	 * The view we are extending.
	 * 
	 * @var string
	 */
	private $extendedView;

	/**
	 * Store the contents of sections.
	 * 
	 * @var array
	 */
	private $sections;

	/**
	 * The currently opened (started) section.
	 * 
	 * @var string
	 */
	private $openSection;

	/**
	 * Returns a new template object
	 *
	 * @param string $view
	 */
	public function __construct($view)
	{
		$this->viewName = $view;

		self::$instance = $this;
	}

	/**
	 * Static interface to return an instance of the View class.
	 *
	 * This is used to return an instance of the view class to the helper
	 * methods that would otherwise have no access to the View data.
	 * 
	 * @return View
	 */
	public static function getInstance()
	{
		return self::$instance;
	}

	/**
	 * Set the view path.
	 * 
	 * @param string 	$path
	 */
	public static function setViewPath($path)
	{
		self::$viewPath = $path;
	}

	/**
	 * Allows setting template values while still returning the object instance
	 * $template->title($title)->text($text);
	 *
	 * @return this
	 */
	public function __call($key, $args)
	{
		$this->$key = $args[0];
		return $this;
	}

	/**
	 * Render View HTML.
	 *
	 * @return mixed
	 */
	public function render()
	{
		try {
			$view = $this->load($this->viewName);

			if ($this->extendedView) {
				$view = $this->load($this->extendedView);
			}

			echo $view;
		}
		catch(\Exception $e)
		{
			return (string) $e;
		}
	}

	/**
	 * Load the given template and return the contents.
	 *
	 * @param  string 	$view
	 * @return string
	 */
	public function load($view)
	{
		ob_start();
		extract((array) $this);
		require self::$viewPath . $view . '.php';
		return ob_get_clean();
	}

	/**
	 * Extend a parent View.
	 *
	 * @param  string 	$view
	 * @return void
	 */
	public function extend($view)
	{
		$this->extendedView = $view;
		ob_end_clean(); // Ignore this child class and load the parent!
		ob_start();
	}

	/**
	 * Start a new section.
	 * 
	 * @param  string 	$name
	 * @return void
	 */
	public function section($name)
	{
		$this->openSection = $name;
		ob_start();
	}

	/**
	 * Close a section and return the buffered contents.
	 *
	 * @return string
	 */
	public function close()
	{
		$name = $this->openSection;

		$buffer = ob_get_clean();

		if ( ! isset($this->sections[$name])) {
			$this->sections[$name] = $buffer;
		} elseif (isset($this->sections[$name])) {
			$this->sections[$name] = str_replace('@parent', $buffer, $this->sections[$name]);
		}

		return $this->sections[$name];
	}

	/**
	 * show the contents of a section.
	 *
	 * @param  string 	$name
	 * @return string
	 */
	public function show($name)
	{
		if(isset($this->sections[$name]))
		{
			return $this->sections[$name];
		}
	}
}

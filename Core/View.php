<?php

namespace Core;

use App\Lib\Config;
use App\Lib\HTTPRequester;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * View
 *
 * PHP version 7.0
 */
class View
{

	/**
	 * Render a view template using Twig
	 *
	 * @param string $template The template file
	 * @param array  $args     Associative array of data to display in the view (optional)
	 *
	 * @return void
	 */
	public static function render( string $template, array $args = [] )
	{
		static $twig = null;

		if($twig == null) {

			$loader = new FilesystemLoader(['resources/views/pages', 'resources/views/partials']);
			$twig = new Environment($loader, [
				'cache' => Config::CACHE,
				'auto_reload' => Config::RELOAD
			]);

			$twig->addGlobal('CURRENT_ROUTE', Router::currentRoute());

			$twig->addFilter( new TwigFilter('cast_to_array', function ($stdClassObject) {
				return (array) $stdClassObject;
			}));

			$twig->addFunction(new TwigFunction('get_var', function ($content) {
				return Config::env($content);
			}));

			$twig->addFunction(new TwigFunction('is_auth', function () {
				if(isset($_SESSION['USER'])) {
					if(isset($_SESSION['LAST_ACTIVITY']) && ( time() - $_SESSION['LAST_ACTIVITY'] > 1800 ))
						return false;
					else return true;
				} else return false;
			}));

			$twig->addFunction(new TwigFunction('decode', function ($data) {
				return html_entity_decode($data);
			}));

			$twig->addFunction(new TwigFunction('is_', function ($roles) {
				if($roles == 'approved') {
					if(isset($_SESSION['USER']) && $_SESSION['USER']->status == 1) return true;
					else return false;
				} else if($roles == 'auth') {
					if(isset($_SESSION['USER'])) {
						if(isset($_SESSION['LAST_ACTIVITY']) && ( time() - $_SESSION['LAST_ACTIVITY'] > 1800 ))
							return false;
						else return true;
					} else return false;
				}
				else return isset($_SESSION['USER']) && $_SESSION['USER']->roles == $roles;
			}));
		}

		try {
			echo $twig->render($template . '.twig', $args);
		} catch (LoaderError | RuntimeError | SyntaxError $e) {
			echo '<pre>' . $e . '</pre>';
		}
	}



}

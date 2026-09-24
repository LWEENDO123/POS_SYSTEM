<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);
    }

    /**
     * Coding-flow trace.
     * -------------------------------------------------------------------
     * Writes a consistent, readable line to the application log so the whole
     * request story can be followed like a book, e.g.:
     *
     *   DEBUG --> [NewSalesController:checkout] ENTER | user=jackbanda cart_count=3
     *   DEBUG --> [NewSalesController:checkout] SALE CREATED | sale_id=182 status=PENDING
     *   DEBUG --> [NewSalesController:checkout] EXIT -> redirect newsales/payment
     *
     * Every public action should at least call trace() at ENTER and before
     * every EXIT (redirect/view/return) so the flow is fully legible.
     *
     * @param string     $method   name of the controller method running
     * @param string     $details  human-readable stage + key context
     * @param ?array     $context  optional {key} placeholders for log_message
     */
    protected function trace(string $method, string $details = '', ?array $context = null): void
    {
        // static::class -> FQCN e.g. "App\Controllers\NewSalesController".
        // Strip the namespace so the log line stays short and readable.
        $controller = static::class;
        $lastSlash  = strrchr($controller, '\\');

        if ($lastSlash !== false) {
            $controller = substr($lastSlash, 1);
        }

        log_message('debug', '[' . $controller . ':' . $method . '] ' . $details, $context ?? []);
    }
}

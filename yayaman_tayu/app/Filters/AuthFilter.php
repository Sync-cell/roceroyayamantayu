<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not change the request or response.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        // Check if user is admin and trying to access admin routes
        $uri = $request->getPath();
        if (strpos($uri, '/admin') === 0) {
            if ($session->get('role') !== 'admin') {
                return redirect()->to('/customer/dashboard')->with('error', 'Unauthorized access.');
            }
        }

        // Check if user is customer and trying to access customer routes
        if (strpos($uri, '/customer') === 0) {
            if ($session->get('role') !== 'customer') {
                return redirect()->to('/admin/dashboard')->with('error', 'Unauthorized access.');
            }
        }

        return $request;
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop processing the request and assume that, if
     * you were to have a redirect in an After filter, it
     * would probably not work as expected.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
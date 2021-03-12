<?php

/**
 * The IndexController class is a Controller that shows the home page.
 *
 * @author Apurv Tyagi
 * @copyright Copyright (c) Netbeans
 */
class IndexController extends Controller
{
    /**
     * Handle the page request.
     *
     * @param array $request the page parameters from a form post or query string
     */
    protected function handleRequest(&$request)
    {
        $user = $this->getUserSession();
        $this->assign('user', $user);
    }
}

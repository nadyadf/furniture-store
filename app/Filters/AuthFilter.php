<?php 

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->has('usrid')) {
            return redirect()->to(site_url('signin'));
        }

        $userModel = model('UserModel');
        $user = $userModel->find($session->get('usrid'));

        if (!$user) {
            $session->destroy();
            return redirect()->to(site_url('signin'));
        }

        // update last login
        $userModel->update($user['id'], [
            'tgl' => date('Y-m-d H:i:s')
        ]);

    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}

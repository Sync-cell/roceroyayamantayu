<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class Files extends BaseController
{
    // Stream KYC files (safe, uses basename to avoid path traversal)
    public function kycs($filename = null)
    {
        $filename = basename($filename ?? '');
        if (empty($filename)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $publicPath   = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'kyc' . DIRECTORY_SEPARATOR . $filename;
        $writablePath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'kyc' . DIRECTORY_SEPARATOR . $filename;

        if (is_file($publicPath)) {
            return $this->response->setFile($publicPath);
        }

        if (is_file($writablePath)) {
            return $this->response->setFile($writablePath);
        }

        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
}
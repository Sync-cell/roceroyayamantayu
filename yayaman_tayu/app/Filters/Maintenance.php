<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\SettingModel;

class Maintenance implements FilterInterface
{
    /**
     * Run before controller
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Skip for CLI requests
        if (php_sapi_name() === 'cli') {
            return;
        }

        $settingModel = new SettingModel();

        // Get maintenance flag
        $maintenance = $settingModel->where('key', 'maintenance_mode')->first();
        $isOn = $maintenance && $maintenance['value'] === '1';
        if (! $isOn) {
            return; // not in maintenance
        }

        // Allow requests coming from whitelisted IPs
        $currentIp = $request->getIPAddress();
        $whitelistRec = $settingModel->where('key', 'maintenance_whitelist')->first();
        $whitelist = [];
        if ($whitelistRec && ! empty($whitelistRec['value'])) {
            $whitelist = json_decode($whitelistRec['value'], true) ?? [];
        }

        // Allow if IP in whitelist
        if (in_array($currentIp, $whitelist, true)) {
            return;
        }

        // Allow admin users (session)
        $session = session();
        if ($session && $session->get('isLoggedIn') && ($session->get('role') === 'admin' || $session->get('admin_id'))) {
            return;
        }

        // Otherwise show maintenance page
        // Note: return a Response object to stop further processing
        $response = service('response');
        $response->setStatusCode(503);
        echo view('maintenance', ['currentIp' => $currentIp]);
        // ensure no further output
        exit;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing
    }
}
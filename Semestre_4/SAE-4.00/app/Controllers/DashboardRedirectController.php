<?php

declare(strict_types=1);

namespace App\Controllers;

final class DashboardRedirectController extends Controller
{
    public function redirect(): ?string
    {
        require_once appPath('config/session.php');

        if (!isAuthenticated()) {
            header('Location: ' . publicRouteUrl('login'));
            exit();
        }

        return match (getUserType() ?? 'guest') {
            'admin' => $this->redirectAdmin(),
            'candidate' => $this->redirectCandidate(),
            'voter' => $this->redirectVoter(),
            default => $this->redirectHome(),
        };
    }

    private function redirectAdmin(): never
    {
        header('Location: ' . appUrl('admin/dashboard'));
        exit();
    }

    private function redirectCandidate(): never
    {
        header('Location: ' . appUrl('candidate/dashboard'));
        exit();
    }

    private function redirectVoter(): never
    {
        header('Location: ' . appUrl('user/dashboard'));
        exit();
    }

    private function redirectHome(): never
    {
        header('Location: ' . publicRouteUrl('home'));
        exit();
    }
}
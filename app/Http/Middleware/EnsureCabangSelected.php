<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCabangSelected
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika sudah auth tapi belum ada session cabang, paksa logout
        if (auth()->check() && !session()->has('cabang_id')) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('filament.admin.auth.login')
                ->withErrors(['cabang' => 'Silakan pilih Cabang Optik saat login.']);
        }

        return $next($request);
    }
}

<?php

namespace App\Filament\Admin\Pages\Auth;

use App\Models\CabangOptik;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

class Login extends BaseLogin
{
    /**
     * Override form() untuk menambahkan field Cabang Optik.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
                $this->getCabangFormComponent(),
            ]);
    }

    protected function getCabangFormComponent(): Component
    {
        return Select::make('cabang_id')
            ->label('Cabang Optik')
            ->placeholder('-- Pilih Cabang --')
            ->options(
                CabangOptik::active()
                    ->orderBy('nama')
                    ->pluck('nama', 'id')
                    ->toArray()
            )
            ->required()
            ->searchable()
            ->native(false);
    }

    /**
     * Override authenticate() untuk menyimpan cabang ke session setelah login berhasil.
     */
    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        $response = parent::authenticate();

        // Setelah login berhasil, simpan cabang ke session
        $cabangId = $data['cabang_id'] ?? null;
        if ($cabangId) {
            $cabang = CabangOptik::find($cabangId);
            if ($cabang) {
                session([
                    'cabang_id'   => $cabang->id,
                    'cabang_nama' => $cabang->nama,
                ]);
            }
        }

        return $response;
    }
}

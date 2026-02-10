<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login as BaseAuth;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;

class Login extends BaseAuth
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                $this->getLoginFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath("data");
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make("login")
            ->label("Login")
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(["tabindex" => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = filter_var($data["login"], FILTER_VALIDATE_EMAIL)
            ? "email"
            : "nik";
        return [
            $login_type =>
                $login_type == "nik" ? $data["login"] : $data["login"],
            "password" => $data["password"],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            "data.login" => __(
                "filament-panels::auth/pages/login.messages.failed",
            ),
        ]);
    }
}

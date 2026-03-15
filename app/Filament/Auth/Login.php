<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login as BaseAuth;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

class Login extends BaseAuth
{
    protected string $view = 'filament.pages.login-page';

    public function getHeading(): string | Htmlable | null
    {
        return null;
    }

    public function getSubheading(): string | Htmlable | null
    {
        return null;
    }

    public function hasLogo(): bool
    {
        return false;
    }
    
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
            : "nis";
        return [
            $login_type =>
                $login_type == "nis" ? $data["login"] : $data["login"],
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

<?php

namespace App\Http\Livewire;

use App\Enums\UserRole;
use App\Models\Application;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

/**
 * @see \JeffGreco13\FilamentBreezy\Http\Livewire\Auth\Register
 */
class Register extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public $first_name;
    public $middle_name;
    public $last_name;
    public $email;
    public $password;
    public $password_confirm;

    public function mount(): void
    {
        //
    }

    public function messages(): array
    {
        return [
            'email.unique' => __('filament-breezy::default.registration.notification_unique'),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('first_name')
                ->label('First Name')
                ->required(),
            Forms\Components\TextInput::make('middle_name')
                ->label('Middle Name')
                ->required(),
            Forms\Components\TextInput::make('last_name')
                ->label('Last Name')
                ->required(),
            Forms\Components\TextInput::make('email')
                ->label(__('filament-breezy::default.fields.email'))
                ->required()
                ->email()
                ->unique(table: config('filament-breezy.user_model')),
            Forms\Components\TextInput::make('password')
                ->label(__('filament-breezy::default.fields.password'))
                ->required()
                ->password()
                ->rules(config('filament-breezy.password_rules')),
            Forms\Components\TextInput::make('password_confirm')
                ->label('Confirm password')
                ->required()
                ->password()
                ->same('password'),
        ];
    }

    protected function prepareModelData($data): array
    {
        $preparedData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ];

        return $preparedData;
    }

    private function createUser(): User
    {
        $data = $this->form->getState();

        $user = User::create([
            'role' => UserRole::Applicant,
            'password' => Hash::make($data['password']),
        ] + $data);

        return $user;
    }

    public function register()
    {
        $user = $this->createUser();

        event(new Registered($user));

        Auth::login($user, true);

        return redirect()->to(config('filament-breezy.register_redirect_url'));
    }

    public function render(): View
    {
        $view = view('filament-breezy::register');

        $view->layout('filament::components.layouts.base', [
            'title' => __('filament-breezy::default.registration.title'),
        ]);

        return $view;
    }
}

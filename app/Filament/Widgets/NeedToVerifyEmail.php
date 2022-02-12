<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\Widget;

class NeedToVerifyEmail extends Widget
{
    protected static string $view = 'filament.widgets.need-to-verify-email';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -5;

    public static function canView(): bool
    {
        /** @var User */
        $user = auth()->user();
        return !$user->hasVerifiedEmail();
    }
}

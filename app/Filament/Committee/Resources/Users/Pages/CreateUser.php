<?php

namespace App\Filament\Committee\Resources\Users\Pages;

use App\Filament\Committee\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}

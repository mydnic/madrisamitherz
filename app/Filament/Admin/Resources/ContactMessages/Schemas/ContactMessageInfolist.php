<?php

namespace App\Filament\Admin\Resources\ContactMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')->label('E-Mail')->copyable(),
                TextEntry::make('phone')->label('Telefon')->placeholder('-'),
                TextEntry::make('subject')->label('Anliegen')->placeholder('-'),
                TextEntry::make('message')->label('Nachricht')->columnSpanFull(),
                TextEntry::make('locale')->label('Sprache'),
                TextEntry::make('created_at')->label('Datum')->dateTime('d.m.Y H:i'),
            ]);
    }
}

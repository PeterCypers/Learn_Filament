<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        // defines what should be shown in the users/create page (after clicking btn 'New user')
        // uses fields in the User-model -> see user migrations for info on the available fields
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('email')->email(),
                TextInput::make('password')->password(),
                Select::make('role')
                ->options(User::ROLES)
                ->required(),
                // Select::make('name')->options([
                //     'db-stored-value' => 'user-visible-option',
                //     'test' => 'test',
                // ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // defines what the table of users looks like
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name')
                ->searchable(),
                TextColumn::make('email')
                ->searchable(),

                Tables\Columns\TextColumn :: make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn :: make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn :: make('role')
                ->badge()
                ->color(function(string $state): string {
                    // options: 'info'(blue) / 'warning'(amber) / 'danger'(red) / 'success'(green) / 'gray' (zinc) / 'primary' (amber)
                    // if ($state == 'ADMIN') return 'danger';
                    // if ($state == 'EDITOR') return 'info';
                    // if ($state == 'USER') return 'success';
                    // return 'gray';

                    return match($state) {
                        'ADMIN' => 'danger',
                        'EDITOR' => 'info',
                        'USER' => 'success',
                        // default => 'gray',
                    };
                })
                ->sortable()
                ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

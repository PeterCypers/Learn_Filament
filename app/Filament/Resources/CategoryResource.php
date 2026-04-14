<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Filament\Resources\CategoryResource\RelationManagers\PostsRelationManager;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    // to change the name of this tab in the tabs-sidebar:
    // protected static ?string $modelLabel = 'Post-Categories';

    protected static bool $shouldSkipAuthorization = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(function(string $operation, string $state, Forms\Set $set, Forms\Get $get, Category $category) { // important: exact names for enject-variables
                    #dump($operation); // create -> edit | create
                    #dump($state); // 'typed text'
                    #dump($set); // Set object -> can be used to update(set) values in any defined fields in the form
                    #dump($Get); // Get object -> can be used to get values from any defined fields in the form (watch out if other formfields aren't live() you won't get a live-updated value)
                    #dump($category); // the current $model, only works on edit
                    if ($operation === 'edit') {
                        return; // -> when you want the slug only to generate 1x, UC: you don't want paths to change after being set once
                    }
                    $set('slug', Str::slug($state)); // use Illuminate\Support\Str; string helper
                }),
                TextInput::make('slug')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('slug'),
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
            PostsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}

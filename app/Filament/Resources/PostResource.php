<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\PostResource\RelationManagers\AuthorsRelationManager;
use App\Models\Category;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
// use Filament\Resources\Components\Tab;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Tabs::make('Create New Post')->tabs([
                    Tab::make('Tab 1')
                    ->icon('heroicon-o-folder')
                    ->iconPosition(IconPosition::Before) // before is default, but you can set it After also
                    ->badge('Hi')
                    ->schema([
                        TextInput::make('title')->rules('min:3|max:10')->required(),
                        TextInput::make('slug')->required()->unique(ignoreRecord:true),

                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            //->searchable() // removed searchable because of relationship things are only pulled from DB as needed, so we won't see anything until searching a letter
                            ->required(),

                        ColorPicker::make('color')->required(),
                    ]),
                    Tab::make('Content')->icon('heroicon-o-newspaper')->schema([
                        MarkdownEditor::make('content')->required()->columnSpanFull(),
                    ]),
                    Tab::make('Meta')->icon('heroicon-o-photo')->schema([
                        FileUpload::make('thumbnail')->disk('public')->directory('thumbnails'),
                        TagsInput::make('tags')->required(),
                        Checkbox::make('published'),
                    ]),
                    Tab::make('Authors')->icon('heroicon-o-users')->schema([
                        // without preload it only loads users when you start searching, so you need to know user names
                        // now we preload, which shows the users that can be selected, downside: if many users exist it'll take longer
                        Select::make('authors')
                        ->label('Co Authors')
                        ->multiple()
                        //->searchable(false)
                        ->preload()
                        ->relationship('authors', 'name'),

                        // CheckboxList instead of Select + multiple
                        // Section::make('Authors')->schema([
                        //     CheckboxList::make('authors')
                        //     ->label('Co Authors')
                        //     ->relationship('authors', 'name'),
                        // ]),
                    ])
                    // set the tab that is shown when page loads
                    // shows the tabs in the URL so you can share this webpage including active tab
                ])->columnSpanFull()->activeTab(1)->persistTabInQueryString(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                ->sortable()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault:true),
                ImageColumn::make('thumbnail'),
                ColorColumn::make('color'),
                TextColumn::make('title')
                ->sortable()
                ->searchable()
                ->toggleable(),
                TextColumn::make('slug')
                ->sortable()
                ->searchable()
                ->toggleable(),
                TextColumn::make('category.name') // works because we have a set-relationship, it knows that category has name field
                ->sortable()
                ->searchable()
                ->toggleable(),
                TextColumn::make('tags'),
                CheckboxColumn::make('published'),
                TextColumn::make('created_at')
                ->label('Published on')
                ->date()
                ->sortable()
                ->searchable()
                ->toggleable(),
            ])
            ->filters([
                // name is not so important
                Filter::make('Published Posts')->query(
                    // accepts any type of eloquent query
                    function ($query){
                        return $query->where('published', true);
                    }
                ),
                Filter::make('UnPublished Posts')->query(
                    // accepts any type of eloquent query
                    function ($query){
                        return $query->where('published', false);
                    }
                ),
                SelectFilter::make('category_id')
                //->options(Category::all()->pluck('name', 'id'))
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->multiple(),

                // TernaryFilter::make('published')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            AuthorsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}

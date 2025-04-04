<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ImageColumn;


class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Forum';
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user() && auth()->user()->isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->placeholder('Enter the category name')
                    ->maxlength(255),
                    Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->placeholder('Enter the category slug')
                    ->unique(ignoreRecord: true),
                    Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->placeholder('Enter the category description')
                    ->nullable(),
                    Forms\Components\Select ::make('parent_id')
                    ->label('Parent Category')
                    ->placeholder('Select the parent category if exists')
                    ->relationship('parent', 'name')
                    ->nullable(),
                    Forms\Components\FileUpload::make('image')
                    ->label('Image')
                    ->placeholder('Upload an image for the category')
                    ->image()
                    ->directory('categories')
                    ->preserveFilenames()
                    ->imagePreviewHeight('150')
                    ->required()
                    ->disk('public')
                    ->visibility('public')
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '16:9'
                    ]),
                    Forms\Components\Checkbox::make('active')
                    ->label('Active')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                TextColumn::make('parent.name')
                    ->label('Parent Category')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->size(50)
                    ->circular()
                    ->rounded(),
               CheckboxColumn::make('active')
                    ->label('Active')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('jS M y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                     ->label('Filter by Parent Category')
                     ->relationship('parent', 'name')
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}

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

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
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
                    Forms\Components\TextArea::make('description')
                    ->label('Description')
                    ->placeholder('Enter the category description')
                    ->nullable(),
                    Forms\Components\Checkbox::make('active')
                    ->label('Active')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Column\TextColumn::make('name')
                    ->label('Name')
                    ->primary()
                    ->searchable()
                    ->sortable(),
                Tables\Column\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                Tables\Column\CheckboxColumn::make('active')
                    ->label('Active')
                    ->sortable(),
                Tables\Column\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}

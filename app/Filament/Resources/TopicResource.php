<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TopicResource\Pages;
use App\Filament\Resources\TopicResource\RelationManagers;
use App\Models\Topic;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;


class TopicResource extends Resource
{
    protected static ?string $model = Topic::class;
    protected static ?string $navigationGroup = 'Forum';
    protected static ?string $navigationIcon = 'heroicon-o-document';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Select::make('category_id')
                ->label('Category')
                ->options(\App\Models\Category::where('active', true)->pluck('name', 'id'))
                ->required(),
            Forms\Components\Hidden::make('user_id')
                ->default(auth()->id()), 
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\RichEditor::make('content')->required(),
            Forms\Components\Checkbox::make('pinned'),
            Forms\Components\Checkbox::make('locked'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                ->sortable()
                ->searchable(),
                TextColumn::make('category.name')
                ->label('Category')
                ->sortable()
                ->searchable(),
                CheckboxColumn::make('pinned')
                ->label('Pinned')
                ->sortable(),
                CheckboxColumn::make('locked')
                ->label('Locked')
                ->sortable(),
                TextColumn::make('created_at')
                ->label('Created At')
                ->dateTime('jS M y')
                ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                     ->label('Filter by Category')
                        ->options(\App\Models\Category::pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ViewAction::make(),
                Action::make('viewComments')
                    ->label('Manage Comments')
                    ->icon('heroicon-o-chat-bubble-bottom-center')
                    ->url(fn (Topic $record) => CommentResource::getUrl('index') . '?tableFilters[commentable_id][value]=' . $record->id)
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListTopics::route('/'),
            'create' => Pages\CreateTopic::route('/create'),
            'edit' => Pages\EditTopic::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Filament\Resources\CommentResource\RelationManagers;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\ViewAction;




class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center';
    protected static ?string $navigationGroup = 'Forum';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('content')
                    ->label('Content')
                    ->required(),
                Forms\Components\Checkbox::make('active')
                ->label('Active')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               TextColumn::make('user.userName')
                ->label('User')
                ->sortable()
                ->searchable(),
                TextColumn::make('content')
                    ->label('Content')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('commentable_type')
                    ->label('Section')
                    ->formatStateUsing(fn (string $state) => class_basename($state))
                    ->searchable(),
                TextColumn::make('commentable_id')
                    ->label('Section-Title')
                    ->formatStateUsing(fn ($state, $record) => optional($record->commentable)->title ?? 'N/A')
                    ->sortable(),
                IconColumn::make('active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime('jS M y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('commentable_type')
                    ->label('Section')
                    ->options([
                        'App\Models\Topic' => 'Topic',
                        'App\Models\Article' => 'Article',
                    ]),
                    Tables\Filters\Filter::make('commentable_id')
                    ->form([
                        Forms\Components\TextInput::make('value')
                            ->hidden(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->where('commentable_id', $value)
                        );
                    }),
            ])
            ->actions([
                
                ViewAction::make(),
                Action::make('toggleActive')
                    ->label(function (Comment $record): string {
                        return $record->active ? 'Deactivate' : 'Activate';
                    })
                    ->color (function (Comment $record): string {
                        return $record->active ? 'danger' : 'success';
                    })
                    ->action(function (Comment $record): void {
                        $record->update(['active' => !$record->active]);
                    })
                    ->requiresConfirmation(),
                Action::make('viewReplies')
                    ->label('Manage Replies')
                    ->icon('heroicon-o-chat-bubble-bottom-center')
                    ->color('warning')
                    ->url(function (Comment $record) {
                    return ReplyResource::getUrl('index', [
                        'tableFilters[comment_id][value]' => $record->id
                    ]);
                })
                ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                   
                ]),
            ])
            ->recordUrl(null);
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
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
    public static function canCreate(): bool
{
    return false;
}
}

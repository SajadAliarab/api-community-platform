<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReplyResource\Pages;
use App\Filament\Resources\ReplyResource\RelationManagers;
use App\Models\Reply;
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


class ReplyResource extends Resource
{
    protected static ?string $model = Reply::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = null;

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
                    ->limit(100)
                    ->searchable(),
                IconColumn::make('active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime('jS M y')
                    ->sortable(),
                TextColumn::make('comment_id')
                    ->hidden(),
   

            ])
            ->filters([
                Tables\Filters\Filter::make('comment_id')
                ->form([
                    Forms\Components\TextInput::make('value')
                            ->hidden(),
                ])
                ->query (function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['value'],
                        fn (Builder $query, $value): Builder => $query->where('comment_id', $value)
                    );
                })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('toggleActive')
                    ->label(function (Reply $record): string {
                        return $record->active ? 'Deactivate' : 'Activate';
                    })
                    ->color (function (Reply $record): string {
                        return $record->active ? 'danger' : 'success';
                    })
                    ->action(function (Reply $record): void {
                        $record->update(['active' => !$record->active]);
                    })
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListReplies::route('/'),
            'create' => Pages\CreateReply::route('/create'),
            'edit' => Pages\EditReply::route('/{record}/edit'),
        ];
    }
    public static function canCreate(): bool
    {
        return false;
 
   }

}

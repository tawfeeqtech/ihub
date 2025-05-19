<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkspaceResource\Pages;
use App\Filament\Resources\WorkspaceResource\RelationManagers;
use App\Helpers\FilamentAccess;
use App\Models\Workspace;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;

use Filament\Forms\Components\FileUpload;



class WorkspaceResource extends Resource
{
    protected static ?string $model = Workspace::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function canAccess(): bool
    {
        return FilamentAccess::isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('location')->required(),

                Forms\Components\Section::make('معلومات الدفع')
                    ->schema([
                        Forms\Components\Toggle::make('bank_payment_supported')
                            ->label('هل تدعم التحويل البنكي؟')
                            ->default(false)
                            ->reactive(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('bank_account_number')
                                    ->label('رقم الحساب البنكي')
                                    ->nullable()
                                    ->visible(fn($livewire) => ($livewire->data['bank_payment_supported'] ?? false) === true),

                                Forms\Components\TextInput::make('mobile_payment_number')
                                    ->label('رقم الجوال البنكي')
                                    ->nullable()
                                    ->visible(fn($livewire) => ($livewire->data['bank_payment_supported'] ?? false) === true),
                            ]),
                    ]),

                Forms\Components\Textarea::make('description'),

                FileUpload::make('logo'),


                Forms\Components\Repeater::make('features')
                    ->schema([
                        Forms\Components\TextInput::make('value')
                            ->label('الميزة')
                            ->required()->columnSpan('full'),
                    ])
                    ->label('مميزات المساحة')
                    ->addActionLabel('أضف ميزة')
                    ->default([])
                    ->columns(4)
                    ->grid(4)
                    ->columnSpan('full')
                    ->itemLabel(null),

                // Forms\Components\Repeater::make('images')
                //     ->relationship()
                //     ->label('معرض الصور')
                //     ->addActionLabel('إضافة صورة')
                //     ->schema([
                //         FileUpload::make('image')
                //             ->label('الصورة')
                //             ->image()
                //             ->disk('public') // تأكد إنها نفس القرص المضبوط في config/filesystems.php
                //             ->directory('workspace_gallery')
                //             ->visibility('public')
                //             ->preserveFilenames()
                //             ->imagePreviewHeight('200') // اختياري
                //             ->loadingIndicatorPosition('left')
                //             ->panelAspectRatio('1:1')
                //             ->removeUploadedFileButtonPosition('right'),
                //     ])
                //     ->columns(1)
                //     ->collapsible()
                //     ->defaultItems(0),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('location')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('رفع الصور')
                    ->icon('heroicon-o-photo')
                    ->url(fn(Workspace $record) => route('admin.upload-images.create', $record->id))
                    ->color('success'),
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
            'index' => Pages\ListWorkspaces::route('/'),
            'create' => Pages\CreateWorkspace::route('/create'),
            'edit' => Pages\EditWorkspace::route('/{record}/edit'),
        ];
    }
}

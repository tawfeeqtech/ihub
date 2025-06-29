<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkspaceResource\Pages;
use App\Helpers\FilamentAccess;
use App\Models\Workspace;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;


use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class WorkspaceResource extends Resource
{
    protected static ?string $model = Workspace::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?int $navigationSort = 6;


    public static function canAccess(): bool
    {
        return FilamentAccess::isAdmin();
    }

    public static function form(Form $form): Form
    {
        $languages = config('app.supported_locales', ['ar', 'en']);

        return $form
            ->schema([
                Section::make('معلومات عامة')
                    ->schema([
                        Repeater::make('name_translations')
                            ->label('الاسم متعدد اللغات')
                            ->addActionLabel('إضافة لغة أخرى')
                            ->schema([
                                Select::make('locale')
                                    ->label('اللغة')
                                    ->options(fn() => $languages)
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('value')
                                    ->label('الاسم')
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->default([
                                ['locale' => 'en', 'value' => ''],
                            ])
                            ->columns(2)
                            ->grid(2)
                            ->columnSpan('full'),


                        Repeater::make('location_translations')
                            ->label('العنوان متعدد اللغات')
                            ->addActionLabel('إضافة لغة أخرى')
                            ->schema([
                                Select::make('locale')
                                    ->label('اللغة')
                                    ->options(fn() => $languages)
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('value')
                                    ->label('العنوان')
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->default([
                                ['locale' => 'en', 'value' => ''],
                            ])
                            ->columns(2)
                            ->grid(2)
                            ->columnSpan('full'),


                        Repeater::make('description_translations')
                            ->label('الوصف متعدد اللغات')
                            ->addActionLabel('إضافة لغة أخرى')
                            ->schema([
                                Select::make('locale')
                                    ->label('اللغة')
                                    ->options(fn() => $languages)
                                    ->required()
                                    ->columnSpan(1),

                                Textarea::make('value')
                                    ->label('الوصف')
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->default([
                                ['locale' => 'en', 'value' => ''],
                            ])
                            ->columns(2)
                            ->grid(2)
                            ->columnSpan('full'),

                    ]),

                Section::make('معلومات الدفع')
                    ->schema([
                        Toggle::make('bank_payment_supported')
                            ->label('هل تدعم التحويل البنكي؟')
                            ->default(false)
                            ->reactive(),

                        Grid::make(2)->schema([
                            TextInput::make('bank_account_number')
                                ->label('رقم الحساب البنكي')
                                ->nullable()
                                ->visible(fn($livewire) => ($livewire->data['bank_payment_supported'] ?? false) === true),

                            TextInput::make('mobile_payment_number')
                                ->label('رقم الجوال البنكي')
                                ->nullable()
                                ->visible(fn($livewire) => ($livewire->data['bank_payment_supported'] ?? false) === true),
                        ]),
                    ]),


                // FileUpload::make('logo'),


                Repeater::make('features')
                    ->schema([
                        TextInput::make('value')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkspaces::route('/'),
            'create' => Pages\CreateWorkspace::route('/create'),
            'edit' => Pages\EditWorkspace::route('/{record}/edit'),
        ];
    }
}

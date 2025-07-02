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

    public static function getPluralModelLabel(): string
    {
        return __('filament.WorkspaceResource.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.WorkspaceResource.label');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): string | array | null
    {
        $count = static::getNavigationBadge();

        return $count > 0 ? 'primary' : 'gray';
    }
    public static function canAccess(): bool
    {
        return FilamentAccess::isAdmin();
    }

    public static function form(Form $form): Form
    {
        $languages = config('app.supported_locales', ['ar', 'en']);

        return $form
            ->schema([
                Section::make(__('filament.WorkspaceResource.form.GeneralInformation'))
                    ->schema([
                        Repeater::make('name_translations')
                            ->label(__('filament.WorkspaceResource.form.name_translations.label'))
                            ->addActionLabel(__('filament.WorkspaceResource.form.name_translations.addActionLabel'))
                            ->schema([
                                Select::make('locale')
                                    ->label(__('filament.Service.form.locale'))
                                    ->options(fn() => $languages)
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('value')
                                    ->label(__('filament.WorkspaceResource.table.name'))
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
                            ->label(__('filament.WorkspaceResource.form.location_translations.label'))
                            ->addActionLabel(__('filament.Service.form.addActionLabel'))
                            ->schema([
                                Select::make('locale')
                                    ->label(__('filament.Service.form.locale'))
                                    ->options(fn() => $languages)
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('value')
                                    ->label(__('filament.WorkspaceResource.form.location_translations.title'))
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
                            ->label(__('filament.WorkspaceResource.form.description_translations.label'))
                            ->addActionLabel(__('filament.Service.form.addActionLabel'))
                            ->schema([
                                Select::make('locale')
                                    ->label(__('filament.Service.form.locale'))
                                    ->options(fn() => $languages)
                                    ->required()
                                    ->columnSpan(1),

                                Textarea::make('value')
                                    ->label(__('filament.WorkspaceResource.form.description_translations.title'))
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

                Section::make(__('filament.WorkspaceResource.form.payment_information.title'))
                    ->schema([
                        Toggle::make('bank_payment_supported')
                            ->label(__('filament.WorkspaceResource.form.bank_payment_supported.label'))
                            ->default(false)
                            ->reactive(),

                        Grid::make(2)->schema([
                            TextInput::make('bank_account_number')
                                ->label(__('filament.WorkspaceResource.form.bank_account_number.label'))
                                ->nullable()
                                ->visible(fn($livewire) => ($livewire->data['bank_payment_supported'] ?? false) === true),

                            TextInput::make('mobile_payment_number')
                                ->label(__('filament.WorkspaceResource.form.mobile_payment_number.label'))
                                ->nullable()
                                ->visible(fn($livewire) => ($livewire->data['bank_payment_supported'] ?? false) === true),
                        ]),
                    ]),


                // FileUpload::make('logo'),


                Repeater::make('features')
                    ->schema([
                        TextInput::make('value')
                            ->label(__('filament.WorkspaceResource.form.features.value.label'))
                            ->required()->columnSpan('full'),
                    ])
                    ->label(__('filament.WorkspaceResource.form.features.label'))
                    ->addActionLabel(__('filament.WorkspaceResource.form.features.addActionLabel'))
                    ->default([])
                    ->columns(4)
                    ->grid(4)
                    ->columnSpan('full')
                    ->itemLabel(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        $currentLocale = auth()->user()?->current_locale;
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name.' . $currentLocale)->label(__('filament.WorkspaceResource.table.name'))->searchable(),
                Tables\Columns\TextColumn::make('location.' . $currentLocale)->label(__('filament.WorkspaceResource.table.location'))->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('uploadImage')
                    ->label(__('filament.WorkspaceResource.table.uploadImage'))
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

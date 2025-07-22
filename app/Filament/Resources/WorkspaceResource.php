<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkspaceResource\Pages;
use App\Helpers\FilamentAccess;
use App\Models\Workspace;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;


use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\FileUpload;

class WorkspaceResource extends Resource
{
    protected static ?string $model = Workspace::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?int $navigationSort = 2;

    public static function getPluralModelLabel(): string
    {
        return __('filament.WorkspaceResource.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.WorkspaceResource.label');
    }

    public static function getLabel(): ?string
    {
        return __('filament.WorkspaceResource.label');
    }

    public static function getNavigationBadge(): ?string
    {
        if (FilamentAccess::isAdmin()) {
            return static::getModel()::count();
        }
        return static::getModel()::where('id', auth()->user()->workspace_id)->count();
    }
    public static function getNavigationBadgeColor(): string | array | null
    {
        $count = static::getNavigationBadge();

        return $count > 0 ? 'primary' : 'gray';
    }

    public static function canCreate(): bool
    {
        return FilamentAccess::isAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        return FilamentAccess::isAdmin() || FilamentAccess::isSecretary();
    }
    public static function canDelete(Model $record): bool
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



                        Grid::make(4)
                        ->columns([
                            'default' => 1,
                            'sm' => 2,
                            'md' => 4,
                        ])
                            ->schema([

                                Select::make('governorate_id')
                                    ->label(__('filament.governorate'))
                                    ->relationship('governorate', 'name')
                                    ->getOptionLabelFromRecordUsing(fn($record) => $record->translated_name)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn(callable $set) => $set('region_id', null)),

                                Select::make('region_id')
                                    ->label(__('filament.region'))
                                    ->relationship('region', 'name', fn($query, callable $get) => $query->where('governorate_id', $get('governorate_id')))
                                    ->getOptionLabelFromRecordUsing(fn($record) => $record->translated_name)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                        $governorateId = $get('governorate_id');
                                        $regionId = $state;

                                        if ($governorateId && $regionId) {
                                            $governorate = \App\Models\Governorate::find($governorateId);
                                            $region = \App\Models\Region::find($regionId);
                                            if ($governorate && $region) {
                                                $arLocation = "{$governorate->getTranslatedNameAttribute('ar')} {$region->getTranslatedNameAttribute('ar')}";
                                                $enLocation = "{$governorate->getTranslatedNameAttribute('en')} {$region->getTranslatedNameAttribute('en')}";
                                                $location = [
                                                    'ar' => $arLocation,
                                                    'en' => $enLocation,
                                                ];
                                                $set('location', $location);
                                            }
                                        }
                                    }),
                                Toggle::make('has_evening_shift')
                                    ->label(__('filament.WorkspaceResource.table.has_evening_shift'))
                                    ->default(false),
                                Toggle::make('has_free')
                                    ->label(__('filament.WorkspaceResource.table.has_free'))
                                    ->default(false),
                            ]),

                        Hidden::make('location')
                            ->dehydrated(true),
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


                // Repeater::make('features')
                //     ->schema([
                //         TextInput::make('value')
                //             ->label(__('filament.WorkspaceResource.form.features.value.label'))
                //             ->required()->columnSpan('full'),
                //     ])
                //     ->label(__('filament.WorkspaceResource.form.features.label'))
                //     ->addActionLabel(__('filament.WorkspaceResource.form.features.addActionLabel'))
                //     ->default([])
                //     ->columns(4)
                //     ->grid(4)
                //     ->columnSpan('full')
                //     ->itemLabel(null),

                Repeater::make('features')
                ->label(__('filament.WorkspaceResource.form.features.label'))
                ->addActionLabel(__('filament.WorkspaceResource.form.features.addActionLabel'))
                ->schema([
                    TextInput::make('ar')
                        ->label(__('filament.WorkspaceResource.form.features.ar'))
                        ->required()
                        ->columnSpan(1),
                    TextInput::make('en')
                        ->label(__('filament.WorkspaceResource.form.features.en'))
                        ->required()
                        ->columnSpan(1),
                ])
                ->default([['ar' => '', 'en' => '']])
                ->columns(2)
                ->grid(2)
                ->columnSpan('full'),

                FileUpload::make('logo')
                    ->label(__('filament.logo'))
                    ->image()
                    ->disk('public')
                    ->directory('workspaces/logos')
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                    ->nullable()
                    ->preserveFilenames(),


                FileUpload::make('workspace_images')
                    ->label(__('filament.workspace_images'))
                    ->multiple()
                    ->image()
                    ->disk('public')
                    ->directory('workspaces/images')
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                    ->enableReordering()
                    ->enableOpen()
                    ->enableDownload()
                    ->preserveFilenames(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $currentLocale = auth()->user()?->current_locale;
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name.' . $currentLocale)->label(__('filament.WorkspaceResource.table.name'))->searchable(),

                Tables\Columns\TextColumn::make('governorate.name.' . $currentLocale)
                    ->label(__('filament.governorate'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('region.name.' . $currentLocale)
                    ->label(__('filament.region'))
                    ->searchable(),

                Tables\Columns\BooleanColumn::make('has_evening_shift')
                    ->label(__('filament.WorkspaceResource.table.has_evening_shift')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('governorate')
                    ->label(__('filament.governorate'))
                    ->relationship('governorate', 'name'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkspaces::route('/'),
            'create' => Pages\CreateWorkspace::route('/create'),
            'edit' => Pages\EditWorkspace::route('/{record}/edit'),
        ];
    }
}

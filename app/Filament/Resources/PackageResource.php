<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
use App\Helpers\FilamentAccess;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Log;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?int $navigationSort = 3;

    // public static function canAccess(): bool
    // {
    //     return FilamentAccess::isAdmin();
    // }

    public static function getPluralModelLabel(): string
    {
        return __('filament.PackageResource.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.PackageResource.label');
    }
    public static function getLabel(): ?string
    {
        return __('filament.PackageResource.label');
    }

    public static function canAccessCreateForm(): bool
    {
        return static::canCreate();
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        // القيم المستخدمة
        $used = Package::where('workspace_id', $user->workspace_id)->pluck('name')->toArray();

        // القيم الممكنة
        $all = ['hour', 'day', 'week', 'month'];

        return count(array_diff($all, $used)) > 0;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'secretary';
    }



    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['workspace_id'] = auth()->user()->workspace_id;
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['workspace_id'] = auth()->user()->workspace_id;
        return $data;
    }


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('workspace_id', auth()->user()->workspace_id)->count();

        // return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): string | array | null
    {
        $count = static::getNavigationBadge();

        return $count > 0 ? 'primary' : 'gray';
    }

    public static function form(Form $form): Form
    {
        $user = auth()->user();
        $usedNames = Package::where('workspace_id', $user->workspace_id)->pluck('name')->toArray();

        // جميع القيم الممكنة
        $allOptions = [
            'hour' => __('filament.PackageResource.form.name.hour'),
            'day' => __('filament.PackageResource.form.name.day'),
            'week' => __('filament.PackageResource.form.name.week'),
            'month' => __('filament.PackageResource.form.name.month'),
        ];

        $availableOptions = array_diff_key($allOptions, array_flip($usedNames));

        $record = $form->getRecord(); // الحصول على السجل الحالي
        if ($record && $form->getOperation() === 'edit') {
            $currentName = $record->name;
            if (isset($allOptions[$currentName])) {
                $availableOptions[$currentName] = $allOptions[$currentName];
            }
        }

        return $form
            ->schema([
                Forms\Components\Hidden::make('workspace_id')
                    ->default($user->workspace_id),

                Forms\Components\Select::make('name')
                    ->label(__('filament.PackageResource.table.name'))
                    ->options($availableOptions)
                    ->required()->reactive()
                    ->default(fn($record) => $record ? $record->name : null)
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($state === 'hour') {
                            $set('duration', 1); // تعيين تلقائي
                        }
                    })
                    ->afterStateHydrated(function ($state, Set $set) {
                        if ($state === 'hour') {
                            $set('duration', 1); // تعيين القيمة عند تحميل النموذج
                        }
                    })
                    ->disabled(fn() => empty($availableOptions)),

                // Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('price')
                    ->label(__('filament.PackageResource.table.price'))->numeric()->required(),
                Forms\Components\TextInput::make('duration')
                    ->label(__('filament.PackageResource.table.duration'))
                    ->numeric()
                    ->required()
                    ->disabled(fn(Get $get) => $get('name') === 'hour')
                    ->dehydrateStateUsing(function ($state, Get $get) {
                        return $get('name') === 'hour' ? 1 : $state;
                    })
                    ->dehydrated(true), // التأكد من إرسال الحقل دائمًا
                // ->dehydrated(true)
            ]);
    }

    public static function table(Table $table): Table
    {
        $currentLocale = auth()->user()?->current_locale;
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('workspace.name.' . $currentLocale),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament.PackageResource.table.name'))->formatStateUsing(function ($state) {
                        return __('filament.PackageResource.form.name.' . $state);
                    }),
                Tables\Columns\TextColumn::make('price')->label(__('filament.PackageResource.table.price')),
                Tables\Columns\TextColumn::make('duration')->label(__('filament.PackageResource.table.duration')),
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
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}

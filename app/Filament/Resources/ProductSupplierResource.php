<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductSupplierResource\Pages;
use App\Models\ProductSupplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductSupplierResource extends Resource
{
    protected static ?string $model = ProductSupplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = '商品管理';
    protected static ?string $modelLabel = '供應商';
    protected static ?string $pluralModelLabel = '供應商';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('供應商名稱')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label('供應商代碼')
                    ->required()
                    ->maxLength(50),
                Forms\Components\FileUpload::make('logo')
                    ->label('Logo')
                    ->image()
                    ->imageEditor()
                    ->directory('supplier-logos')
                    ->columnSpanFull()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->downloadable()
                    ->openable()
                    ->getUploadedFileNameForStorageUsing(
                        fn($file): string => (string) str(Str::uuid7() . '.webp')
                    )
                    ->saveUploadedFileUsing(function ($file) {
                        $manager = new ImageManager(new Driver());
                        $image = $manager->read($file);
                        $image->cover(1024, 1024);
                        $filename = Str::uuid7()->toString() . '.webp';
                        if(!file_exists(storage_path('app/public/supplier-logos'))) {
                            mkdir(storage_path('app/public/supplier-logos'), 0755, true);
                        }
                        $image->toWebp(80)->save(storage_path('app/public/supplier-logos/' . $filename));
                        return 'supplier-logos/' . $filename;
                    }),
                Forms\Components\TextInput::make('contact_person')
                    ->label('聯絡人')
                    ->maxLength(100),
                Forms\Components\TextInput::make('phone')
                    ->label('電話')
                    ->tel()
                    ->maxLength(20),
                Forms\Components\TextInput::make('mobile')
                    ->label('手機')
                    ->tel()
                    ->maxLength(20),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(100),
                Forms\Components\TextInput::make('line_id')
                    ->label('Line ID')
                    ->maxLength(50),
                Forms\Components\Toggle::make('is_active')
                    ->label('啟用')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('供應商名稱')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('供應商代碼')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo'),
                Tables\Columns\TextColumn::make('contact_person')
                    ->label('聯絡人')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('電話'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('啟用')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListProductSuppliers::route('/'),
            'create' => Pages\CreateProductSupplier::route('/create'),
            'edit' => Pages\EditProductSupplier::route('/{record}/edit'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 
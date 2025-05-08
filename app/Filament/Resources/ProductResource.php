<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
// use Malzariey\FilamentLexicalEditor\FilamentLexicalEditor;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = '商品管理';
    protected static ?string $modelLabel = '商品';
    protected static ?string $pluralModelLabel = '商品';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('supplier_id')
                    ->label('供應商')
                    ->relationship('supplier', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('name')
                    ->label('商品名稱')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label('商品代碼')
                    ->required()
                    ->maxLength(50),
                Forms\Components\FileUpload::make('logo')
                    ->label('Logo')
                    ->image()
                    ->imageEditor()
                    ->directory('product-logos')
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
                        if (!file_exists(storage_path('app/public/product-logos'))) {
                            mkdir(storage_path('app/public/product-logos'), 0755, true);
                        }
                        $image->toWebp(80)->save(storage_path('app/public/product-logos/' . $filename));
                        return 'product-logos/' . $filename;
                    }),
                Forms\Components\Textarea::make('description')
                    ->label('說明')
                    ->maxLength(1000),
                TinyEditor::make('introduction')
                    ->label('商品介紹')
                    ->columnSpanFull(),
                TinyEditor::make('specification')
                    ->label('規格說明')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('shipping_method')
                    ->label('運送方式')
                    ->maxLength(1000),
                Forms\Components\Select::make('categories')
                    ->label('歸屬類別')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Forms\Components\Toggle::make('is_active')
                    ->label('啟用')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('供應商')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('商品名稱')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('商品代碼')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

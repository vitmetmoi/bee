<?php

namespace App\Filament\Widgets;

use App\Models\Recipe;
use App\Models\User;
use App\Models\Rating;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentActivityWidget extends BaseWidget
{
    protected static ?string $heading = 'Hoạt động gần đây';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 5;



    public function table(Table $table): Table
    {
        return $table
            ->query(
                Recipe::query()
                    ->with(['user'])
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Hoạt động')
                    ->formatStateUsing(function (Recipe $record): string {
                        return "Tạo công thức: {$record->title}";
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Người thực hiện')
                    ->default('Không xác định'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Trạng thái')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'draft' => 'Bản nháp',
                            'pending' => 'Chờ duyệt',
                            'approved' => 'Đã duyệt',
                            'rejected' => 'Từ chối',
                            default => $state,
                        };
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Thời gian')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ]);
    }
}
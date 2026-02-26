<?php

namespace App\Filament\Resources\MortgageRequests\RelationManagers;

use Illuminate\Database\Eloquent\{Builder, SoftDeletingScope};
use Filament\Actions\{
    AssociateAction,
    BulkActionGroup,
    CreateAction,
    DeleteAction,
    DeleteBulkAction,
    DissociateAction,
    DissociateBulkAction,
    EditAction,
    ForceDeleteAction,
    ForceDeleteBulkAction,
    RestoreAction,
    RestoreBulkAction
};
use Filament\Forms\Components\{FileUpload, Select, TextInput, ToggleButtons};
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Tables\Table;
use Filament\Tables\Columns\{IconColumn, TextColumn};
use Filament\Tables\Filters\TrashedFilter;

class InstallmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'installments';

    private static function fmtIDR($v): string
    {
        return number_format((int) $v, 0, ',', '.');
    }

    private static function toInt($v): int
    {
        return (int) preg_replace('/\D+/', '', (string) $v);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make([
                Step::make('Installments')
                    ->schema([
                        TextInput::make('no_of_payment')
                            ->label('No. Payment')
                            ->helperText('Pembayaran cicilan ke berapa')
                            ->required()
                            ->numeric(),

                        Select::make('sub_total_amount')
                            ->label('Monthly Payment')
                            ->options(function () {
                                $mortgageRequest = $this->getOwnerRecord();
                                $monthly = (int) ($mortgageRequest?->monthly_amount ?? 0);

                                return $monthly
                                    ? [$monthly => 'IDR ' . self::fmtIDR($monthly)]
                                    : [];
                            })
                            ->required()
                            ->live(debounce: 0)
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $subTotal = self::toInt($state);

                                $tax = (int) round($subTotal * 0.11);
                                $insurance = 900000;
                                $grandTotal = (int) round($subTotal + $tax + $insurance);

                                // simpan value tampilan sebagai string IDR bertitik
                                $set('total_tax_amount', self::fmtIDR($tax));
                                $set('insurance_amount', self::fmtIDR($insurance));
                                $set('grand_total_amount', self::fmtIDR($grandTotal));

                                $mortgageRequest = $this->getOwnerRecord();
                                if ($mortgageRequest) {
                                    $lastInstallment = $mortgageRequest->installments()
                                        ->where('is_paid', true)
                                        ->orderBy('no_of_payment', 'desc')
                                        ->first();

                                    $previousRemainingLoan = (int) (
                                        $lastInstallment
                                        ? $lastInstallment->remaining_loan_amount
                                        : $mortgageRequest->loan_interest_total_amount
                                    );

                                    $remainingAfter = max($previousRemainingLoan - $subTotal, 0);

                                    // before payment (display only)
                                    $set('remaining_loan_amount_before_payment', self::fmtIDR($previousRemainingLoan));

                                    // after payment (stored)
                                    $set('remaining_loan_amount', self::fmtIDR($remainingAfter));
                                }
                            }),

                        TextInput::make('total_tax_amount')
                            ->label('Tax 11%')
                            ->readOnly()
                            ->required()
                            ->prefix('IDR')
                            ->dehydrateStateUsing(fn($state) => self::toInt($state)),

                        TextInput::make('insurance_amount')
                            ->label('Additional Insurance')
                            ->readOnly()
                            ->default(self::fmtIDR(900000))
                            ->prefix('IDR')
                            ->dehydrateStateUsing(fn($state) => self::toInt($state)),

                        TextInput::make('grand_total_amount')
                            ->label('Total Payment')
                            ->readOnly()
                            ->required()
                            ->prefix('IDR')
                            ->dehydrateStateUsing(fn($state) => self::toInt($state)),

                        // ini cuma display, gak disimpan ke DB (biar aman)
                        TextInput::make('remaining_loan_amount_before_payment')
                            ->label('Remaining Loan Amount Before Payment')
                            ->readOnly()
                            ->prefix('IDR')
                            ->dehydrated(false),

                        TextInput::make('remaining_loan_amount')
                            ->label('Remaining Loan Amount After Payment')
                            ->readOnly()
                            ->required()
                            ->prefix('IDR')
                            ->dehydrateStateUsing(fn($state) => self::toInt($state)),
                    ]),

                Step::make('Payment Method')
                    ->schema([
                        ToggleButtons::make('is_paid')
                            ->label('Payment Status')
                            ->boolean()
                            ->grouped()
                            ->icons([
                                true => 'heroicon-o-check-circle',
                                false => 'heroicon-o-x-circle',
                            ])
                            ->required(),

                        Select::make('payment_type')
                            ->label('Payment Type')
                            ->options([
                                'Midtrans' => 'Midtrans',
                                'Manual' => 'Manual',
                            ])
                            ->required(),

                        FileUpload::make('proof')
                            ->label('Payment Proof')
                            ->image(),
                    ]),
            ])
                ->columnSpan('full')
                ->columns(1)
                ->skippable(),
        ]);
    }

    public function table(Table $table): Table
    {
        $money = fn($state) => 'IDR ' . self::fmtIDR($state ?? 0);

        return $table
            ->recordTitleAttribute('no_of_payment')
            ->columns([
                TextColumn::make('no_of_payment')->sortable(),
                TextColumn::make('sub_total_amount')->formatStateUsing($money),
                TextColumn::make('insurance_amount')->formatStateUsing($money),
                TextColumn::make('total_tax_amount')->formatStateUsing($money),
                IconColumn::make('is_paid')
                    ->boolean()
                    ->label('Verified')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),


            ])
            ->filters([
                // TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make(),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}

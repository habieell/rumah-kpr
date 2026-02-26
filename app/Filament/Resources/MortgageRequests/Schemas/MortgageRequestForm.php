<?php

namespace App\Filament\Resources\MortgageRequests\Schemas;

use App\Models\{House, Interest, User};
use Filament\Forms\Components\{FileUpload, Hidden, Select, TextInput};
use Filament\Schemas\Schema;
use Filament\Schemas\Components\{Grid, Wizard};
use Filament\Schemas\Components\Wizard\Step;

class MortgageRequestForm
{
    private static function fmtIDR(int $v): ?string
    {
        return $v > 0 ? number_format($v, 0, ',', '.') : null;
    }

    private static function recalc(callable $get, callable $set): void
    {
        $housePrice    = (int) ($get('house_price') ?? 0);
        $dpPct         = (int) ($get('dp_percentage') ?? 0);
        $durationYears = (int) ($get('duration') ?? 0);
        $interestRate  = (float) ($get('interest') ?? 0);

        if ($dpPct <= 0) {
            $set('dp_total_amount', 0);
            $set('loan_total_amount', 0);
            $set('monthly_amount', 0);
            $set('loan_interest_total_amount', 0);

            $set('dp_total_amount_display', null);
            $set('loan_total_amount_display', null);
            $set('monthly_amount_display', null);
            $set('loan_interest_total_amount_display', null);

            return;
        }

        $dpAmount  = $housePrice > 0 ? (int) round(($dpPct / 100) * $housePrice) : 0;
        $loanAmount = max($housePrice - $dpAmount, 0);

        $set('dp_total_amount', $dpAmount);
        $set('loan_total_amount', $loanAmount);

        $set('dp_total_amount_display', self::fmtIDR($dpAmount));
        $set('loan_total_amount_display', self::fmtIDR($loanAmount));

        if ($durationYears > 0 && $loanAmount > 0 && $interestRate > 0) {
            $totalPayments = $durationYears * 12;
            $monthlyInterestRate = $interestRate / 100 / 12;

            $numerator = $loanAmount * $monthlyInterestRate * pow(1 + $monthlyInterestRate, $totalPayments);
            $denominator = pow(1 + $monthlyInterestRate, $totalPayments) - 1;
            $monthlyPayment = $denominator > 0 ? $numerator / $denominator : 0;

            $monthly = (int) round($monthlyPayment);
            $totalPay = (int) round($monthlyPayment * $totalPayments);

            $set('monthly_amount', $monthly);
            $set('loan_interest_total_amount', $totalPay);

            $set('monthly_amount_display', self::fmtIDR($monthly));
            $set('loan_interest_total_amount_display', self::fmtIDR($totalPay));
        } else {
            $set('monthly_amount', 0);
            $set('loan_interest_total_amount', 0);
            $set('monthly_amount_display', null);
            $set('loan_interest_total_amount_display', null);
        }
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make([
                Step::make('Product and Price')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('house_id')
                                ->label('House')
                                ->options(House::query()->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live(debounce: 0)
                                ->afterStateHydrated(function ($state, callable $set, callable $get) {
                                    // ambil raw dari DB (sudah tersimpan)
                                    $housePrice = (int) ($get('house_price') ?? 0);
                                    $dpAmount   = (int) ($get('dp_total_amount') ?? 0);
                                    $loanAmount = (int) ($get('loan_total_amount') ?? 0);
                                    $monthly    = (int) ($get('monthly_amount') ?? 0);
                                    $totalPay   = (int) ($get('loan_interest_total_amount') ?? 0);

                                    // set display formatted
                                    $set('house_price_display', self::fmtIDR($housePrice));
                                    $set('dp_total_amount_display', self::fmtIDR($dpAmount));
                                    $set('loan_total_amount_display', self::fmtIDR($loanAmount));
                                    $set('monthly_amount_display', self::fmtIDR($monthly));
                                    $set('loan_interest_total_amount_display', self::fmtIDR($totalPay));

                                    // kalau mau ensure hitung ulang juga (optional):
                                    self::recalc($get, $set);
                                })
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $price = (int) House::whereKey($state)->value('price');

                                    $set('house_price', $price);
                                    $set('house_price_display', self::fmtIDR($price));

                                    self::recalc($get, $set);
                                }),
                            Select::make('interest_id')
                                ->label('Annual Interest in %')
                                ->options(function (callable $get) {
                                    $houseId = $get('house_id');

                                    if ($houseId) {
                                        return Interest::where('house_id', $houseId)->pluck('interest', 'id');
                                    }

                                    return [];
                                })
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live(debounce: 0)
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $interest = Interest::with('bank')->find($state);

                                    if ($interest) {
                                        $set('bank_name', $interest->bank->name ?? '');
                                        $set('interest', $interest->interest);
                                        $set('duration', $interest->duration);
                                    } else {
                                        $set('bank_name', '');
                                        $set('interest', 0);
                                        $set('duration', 0);
                                    }

                                    self::recalc($get, $set);
                                }),

                            TextInput::make('bank_name')
                                ->label('Bank Name')
                                ->required()
                                ->readOnly(),

                            TextInput::make('duration')
                                ->label('Duration in Years')
                                ->required()
                                ->readOnly()
                                ->numeric()
                                ->suffix('Years'),

                            TextInput::make('interest')
                                ->label('Interest Rate')
                                ->required()
                                ->readOnly()
                                ->numeric()
                                ->suffix('%'),

                            // ===== House price raw + display =====
                            Hidden::make('house_price')->required(),

                            TextInput::make('house_price_display')
                                ->label('House price')
                                ->prefix('IDR')
                                ->disabled()
                                ->dehydrated(false)
                                ->required(),

                            // ===== DP % =====
                            Select::make('dp_percentage')
                                ->label('Down Payment (%)')
                                ->options([
                                    15 => '15%',
                                    20 => '20%',
                                    25 => '25%',
                                    30 => '30%',
                                    40 => '40%',
                                    50 => '50%',
                                ])
                                ->required()
                                ->live(debounce: 0)
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    self::recalc($get, $set);
                                }),


                            Hidden::make('dp_total_amount')->required(),
                            Hidden::make('loan_total_amount')->required(),
                            Hidden::make('monthly_amount')->required(),
                            Hidden::make('loan_interest_total_amount')->required(),

                            TextInput::make('dp_total_amount_display')
                                ->label('Down Payment Amount')
                                ->prefix('IDR')
                                ->disabled()
                                ->dehydrated(false),

                            TextInput::make('loan_total_amount_display')
                                ->label('Loan Amount')
                                ->prefix('IDR')
                                ->disabled()
                                ->dehydrated(false),

                            TextInput::make('monthly_amount_display')
                                ->label('Monthly Payment')
                                ->prefix('IDR')
                                ->disabled()
                                ->dehydrated(false),

                            TextInput::make('loan_interest_total_amount_display')
                                ->label('Total Payment Amount')
                                ->prefix('IDR')
                                ->disabled()
                                ->dehydrated(false),
                        ]),
                    ]),

                Step::make('Customer Information')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('customer', 'email')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $user = User::find($state);
                                $name = $user?->name;
                                $email = $user?->email;
                                $set('name', $name);
                                $set('email', $email);
                            })
                            ->afterStateHydrated(function (callable $set, $state) {
                                $userId = $state;

                                if ($userId) {
                                    $user = User::find($userId);
                                    $name = $user?->name;
                                    $email = $user?->email;
                                    $set('name', $name);
                                    $set('email', $email);
                                }
                            }),

                        TextInput::make('name')
                            ->required()
                            ->readOnly()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->required()
                            ->readOnly()
                            ->maxLength(255),


                    ]),

                Step::make('Bank Approval')
                    ->schema([
                        FileUpload::make('documents')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required(),

                        Select::make('status')
                            ->label('Approval Status')
                            ->options([
                                'Waiting for Bank' => 'Waiting for Bank',
                                'Approved' => 'Approved',
                                'Rejected' => 'Rejected',
                            ])
                            ->required(),
                    ]),


            ])
                ->columnSpan('full')
                ->columns(1)
                ->skippable(),
        ]);
    }
}

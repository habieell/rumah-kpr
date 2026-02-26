<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\{HouseService, MortgageService};

class FrontController extends Controller
{
    protected HouseService $houseService;
    protected MortgageService $mortgageService;

    public function __construct(HouseService $houseService, MortgageService $mortgageService)
    {
        $this->houseService = $houseService;
        $this->mortgageService = $mortgageService;
    }

    public function index()
    {
        $data = $this->houseService->getCategoriesAndCities();
        return view('front.index', $data);
    }
}

<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Currency\CurrencyCollection;
use App\Repositories\Currency\CurrencyRepository;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencyRepository = new CurrencyRepository();

        return new CurrencyCollection($currencyRepository->all(false));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ProductWallet;
use Illuminate\Http\Request;

class ProductWalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductWallet  $productWallet
     * @return \Illuminate\Http\Response
     */
    public function show(ProductWallet $productWallet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductWallet  $productWallet
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductWallet $productWallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductWallet  $productWallet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductWallet $productWallet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductWallet  $productWallet
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductWallet $productWallet)
    {
        //
    }


    public function updateproductwallet(Request $request){
        try {
        $store = ProductWallet::find($request->id);
        $store->view = $request->view;
        $store->update();
        return response()->json(['status' =>'Product Wallet updated']);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'Error' => $e->getMessage()
        ]);

    }
    }
}

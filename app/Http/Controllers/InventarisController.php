<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventarisStoreRequest;
use App\Models\Inventaris;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Inventaris::paginate(10)->withQueryString();

        if ($data->count() < 1) {
            return response()->json([
                "status" => false,
                "data" => $data,
                "message" => "Record is empty"
            ], 404);
        }

        return response()->json([
            "status" => true,
            "data" => $data->items(),
            "pagination" => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
            ],
            "message" => "Success get data"
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InventarisStoreRequest $request)
    {
        $data = $request->validated();;

        // mengecek data apakah sudah ada
        if ($data_exist = Inventaris::where('stuf_name', $data['stuf_name'])->first()) {
            $amount = $data_exist->amount + $data['amount'];

            Inventaris::where('stuf_name', $data['stuf_name'])->update([
                'amount' => $amount
            ]);

            return response()->json([
                "status" => true,
                "data" => [
                    "stuf_name" => $data_exist->stuf_name,
                    "amount" => $amount
                ],
                "message" => "Data already exists, the amount of data is increased"
            ], 200);
        }

        DB::beginTransaction();
        try {
            $newData = Inventaris::create($data);
            DB::commit();
            return response()->json([
                "status" => true,
                "data" => [
                    "stuf_code" => $newData->code,
                    "stuf_name" => $newData->stuf_name
                ],
                "message" => "Add new data success"
            ], 201);
        } catch (\Exception $err) {
            DB::rollBack();
            throw new HttpResponseException(response()->json([
                "status" => false,
                "data" => [],
                "message" => "Add new data failed",
                "errors" => $err
            ], 400));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Inventaris::where('id', $id)->first();

        if (!$data) {
            throw new HttpResponseException(response()->json([
                "status" => false,
                "data" => [
                    "errors" => [
                        "Data with id " . $id . " is not exist"
                    ]
                ],
                "message" => "Get data failed"
            ], 400));
        }

        return response()->json([
            "status" => true,
            "data" => $data,
            "message" => "Get data success"
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InventarisStoreRequest $request, string $id)
    {
        $data = $request->validated();

        $is_data_exist = Inventaris::find($id);

        if (!$is_data_exist) {
            throw new HttpResponseException(response()->json([
                "status" => false,
                "data" => [
                    "errors" => [
                        "Data with id " . $id . " is not exist"
                    ]
                ],
                "message" => "Update data failed"
            ], 404));
        }

        DB::beginTransaction();
        try {
            Inventaris::where('id', $id)->update($data);
            DB::commit();
            return response()->json([
                "status" => true,
                "data" => [
                    "stuf_name" => $data['stuf_name']
                ],
                "message" => "Update data success"
            ], 200);
        } catch (\Exception $err) {
            DB::rollback();
            throw new HttpResponseException(response()->json([
                "status" => false,
                "data" => [],
                "message" => "Update data failed",
                "errors" => $err
            ], 400));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Inventaris::where('id', $id)->first();

        // mengecek apakah data yang akan dihapus ada
        if (!$data) {
            throw new HttpResponseException(response()->json([
                "status" => false,
                "data" => [
                    "errors" => [
                        "Data with id " . $id . " is not exist"
                    ]
                ],
                "message" => "Delete data failed"
            ], 400));
        }

        DB::beginTransaction();
        try {
            Inventaris::where('id', $id)->delete();
            DB::commit();
            return response()->json([
                "status" => true,
                "data" => [],
                "message" => "Delete data success"
            ], 200);
        } catch (\Exception $err) {
            DB::rollback();
            throw new HttpResponseException(response()->json([
                "status" => false,
                "data" => [
                    "errors" => $err
                ],
                "message" => "Delete data failed"
            ], 400));
        }
    }
}
